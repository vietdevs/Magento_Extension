<?php
/**
 * Mirasvit
 *
 * This source file is subject to the Mirasvit Software License, which is available at https://mirasvit.com/license/.
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs.
 * Please refer to http://www.magentocommerce.com for more information.
 *
 * @category  Mirasvit
 * @package   mirasvit/module-seo
 * @version   2.0.24
 * @copyright Copyright (C) 2017 Mirasvit (https://mirasvit.com/)
 */



namespace Mirasvit\SeoSitemap\Preference\ResourceModel;

use Magento\CatalogUrlRewrite\Model\ProductUrlRewriteGenerator;
use Magento\Store\Model\Store;
use Magento\Framework\App\ObjectManager;

/**
 * Sitemap resource product collection model
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Product extends \Magento\Sitemap\Model\ResourceModel\Catalog\Product
{
    /**
     * @var \Magento\Catalog\Helper\Image
     */
    private $catalogImageHelper;

    /**
     * @var null|bool
     */
    private $isEnableImageFriendlyUrl = null;

    /**
     * @var string
     */
    private $imageUrlTemplate = null;

    /**
     * @var string
     */
    private $productRepository;

    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_init('catalog_product_entity', 'entity_id');
        $this->catalogImageHelper = ObjectManager::getInstance()
            ->get(\Magento\Catalog\Helper\Image::class);
        $this->productRepository = ObjectManager::getInstance()
            ->get(\Magento\Catalog\Api\ProductRepositoryInterface::class);
        $moduleManager = ObjectManager::getInstance()
            ->get(\Magento\Framework\Module\Manager::class);
        if ($moduleManager->isEnabled('Mirasvit_Seo')) {
            $imageConfig = ObjectManager::getInstance()
                ->get(\Mirasvit\Seo\Api\Config\ImageConfigServiceInterface::class);
            $this->isEnableImageFriendlyUrl = $imageConfig->isEnableImageFriendlyUrl();
            $this->imageUrlTemplate = $imageConfig->getImageUrlTemplate();
        }
    }

    /**
     * Get category collection array
     *
     * @param null|string|bool|int|Store $storeId
     * @return array|bool
     */
    public function getCollection($storeId)
    {
        $products = [];

        /* @var $store Store */
        $store = $this->_storeManager->getStore($storeId);
        if (!$store) {
            return false;
        }

        $connection = $this->getConnection();

        $this->_select = $connection->select()->from(
            ['e' => $this->getMainTable()],
            //sku need for friendly image
            [$this->getIdFieldName(), $this->_productResource->getLinkField(), 'updated_at', 'sku']
        )->joinInner(
            ['w' => $this->getTable('catalog_product_website')],
            'e.entity_id = w.product_id',
            []
        )->joinLeft(
            ['url_rewrite' => $this->getTable('url_rewrite')],
            'e.entity_id = url_rewrite.entity_id AND url_rewrite.is_autogenerated = 1 AND url_rewrite.metadata IS NULL'
            . $connection->quoteInto(' AND url_rewrite.store_id = ?', $store->getId())
            . $connection->quoteInto(' AND url_rewrite.entity_type = ?', ProductUrlRewriteGenerator::ENTITY_TYPE),
            ['url' => 'request_path']
        )->where(
            'w.website_id = ?',
            $store->getWebsiteId()
        );

        $this->_addFilter($store->getId(), 'visibility', $this->_productVisibility->getVisibleInSiteIds(), 'in');
        $this->_addFilter($store->getId(), 'status', $this->_productStatus->getVisibleStatusIds(), 'in');

        // Join product images required attributes
        $imageIncludePolicy = $this->_sitemapData->getProductImageIncludePolicy($store->getId());
        if (\Magento\Sitemap\Model\Source\Product\Image\IncludeImage::INCLUDE_NONE != $imageIncludePolicy) {
            $this->_joinAttribute($store->getId(), 'name');

            //fix error SQLSTATE: Column not found: 1054 Unknown column 't2_name.value' in 'field list'
            if (strpos($this->_select->__toString(), 'AS `t2_name`') === false) {
                $this->_select->columns(
                    ['name' => $this->getConnection()->getIfNullSql('t1_name.value')]
                );
            } else {
                $this->_select->columns(
                    ['name' => $this->getConnection()->getIfNullSql('t2_name.value', 't1_name.value')]
                );
            }

            if (\Magento\Sitemap\Model\Source\Product\Image\IncludeImage::INCLUDE_ALL == $imageIncludePolicy) {
                $this->_joinAttribute($store->getId(), 'thumbnail');
                $this->_select->columns(
                    [
                        'thumbnail' => $this->getConnection()->getIfNullSql(
                            't2_thumbnail.value',
                            't1_thumbnail.value'
                        ),
                    ]
                );
            } elseif (\Magento\Sitemap\Model\Source\Product\Image\IncludeImage::INCLUDE_BASE == $imageIncludePolicy) {
                $this->_joinAttribute($store->getId(), 'image');
                $this->_select->columns(
                    ['image' => $this->getConnection()->getIfNullSql('t2_image.value', 't1_image.value')]
                );
            }
        }

        $query = $connection->query($this->_select);
        while ($row = $query->fetch()) {
            $product = $this->_prepareProduct($row, $store->getId());
            $products[$product->getId()] = $product;
        }

        return $products;
    }

    /**
     * Load product images
     *
     * @param \Magento\Framework\DataObject $product
     * @param int $storeId
     * @return void
     */
    protected function _loadProductImages($product, $storeId)
    {
        /** @var $helper \Magento\Sitemap\Helper\Data */
        $helper = $this->_sitemapData;
        $imageIncludePolicy = $helper->getProductImageIncludePolicy($storeId);

        // Get product images
        $imagesCollection = [];
        if (\Magento\Sitemap\Model\Source\Product\Image\IncludeImage::INCLUDE_ALL == $imageIncludePolicy) {
            $imagesCollection = $this->_getAllProductImages($product, $storeId);
        } elseif (\Magento\Sitemap\Model\Source\Product\Image\IncludeImage::INCLUDE_BASE == $imageIncludePolicy &&
            $product->getImage() &&
            $product->getImage() != self::NOT_SELECTED_IMAGE
        ) {
            $imagesCollection = [
                new \Magento\Framework\DataObject(
                    ['url' => $this->getCurrentProductImageUrl($product, $product->getImage())]
                ),
            ];
        }

        if ($imagesCollection) {
            // Determine thumbnail path
            $thumbnail = $product->getThumbnail();
            if ($thumbnail && $product->getThumbnail() != self::NOT_SELECTED_IMAGE) {
                $thumbnail = $this->getCurrentProductImageUrl($product, $thumbnail);
            } else {
                $thumbnail = $imagesCollection[0]->getUrl();
            }

            $product->setImages(
                new \Magento\Framework\DataObject(
                    ['collection' => $imagesCollection, 'title' => $product->getName(), 'thumbnail' => $thumbnail]
                )
            );
        }
    }

    /**
     * Get all product images
     *
     * @param \Magento\Framework\DataObject $product
     * @param int $storeId
     * @return array
     */
    protected function _getAllProductImages($product, $storeId)
    {
        $product->setStoreId($storeId);
        $gallery = $this->mediaGalleryResourceModel->loadProductGalleryByAttributeId(
            $product,
            $this->mediaGalleryReadHandler->getAttribute()->getId()
        );

        $imagesCollection = [];
        if ($gallery) {
            foreach ($gallery as $image) {
                $imagesCollection[] = new \Magento\Framework\DataObject(
                    [
                        'url' => $this->getCurrentProductImageUrl($product, $image['file']),
                        'caption' => $image['label'] ? $image['label'] : $image['label_default'],
                    ]
                );
            }
        }

        return $imagesCollection;
    }

    /**
     * Get all product images
     *
     * @param \Magento\Framework\DataObject $product
     * @param string $image
     * @return string
     */
    protected function getCurrentProductImageUrl($product, $image)
    {
        if ($this->isEnableImageFriendlyUrl
            && $this->imageUrlTemplate
            && !$this->isEnoughData($this->imageUrlTemplate)) {
                $product = $this->productRepository->getById($product->getId());
        }
        $imgUrl = $this->catalogImageHelper
            ->init($product, 'product_page_image_large')
            ->setImageFile($image)
            ->getUrl();

        return $imgUrl;
    }

    /**
     * Check if enaoug data for template creating
     *    
     * @param string $imageUrlTemplate
     * @return string
     */
    protected function isEnoughData($imageUrlTemplate)
    {
        $imageUrlTemplate = str_replace(
            [\Mirasvit\Seo\Api\Config\ImageConfigServiceInterface::DEFAULT_TEMPLATE,
                \Mirasvit\Seo\Api\Config\ImageConfigServiceInterface::SKU_TEMPLATE
            ],
            '',
            $imageUrlTemplate
        );

        if (strpos($imageUrlTemplate, ']') !== false) {
            return false;
        }

        return true;
    }
}
