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



namespace Mirasvit\Seo\Block\Snippets;

use Magento\Framework\View\Element\Template;
use Magento\Framework\Registry;
use Magento\Framework\View\Element\Template\Context;
use Mirasvit\Seo\Model\Config;
use Mirasvit\Seo\Api\Service\Snippet\ProductSnippetInterface;

class Product extends Template
{

    /**
     * @param \Mirasvit\Seo\Model\Config                       $config
     * @param \Magento\Framework\Locale\ListsInterface         $localeLists
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Mirasvit\Seo\Helper\Data                        $seoData
     * @param \Magento\Store\Model\Information                 $_storeInfo
     * @param array                                            $data
     */
    public function __construct(
        ProductSnippetInterface $snippet,
        Registry $registry,
        Config $config,
        Context $context,
        array $data = []
    ) {
        $this->snippet = $snippet;
        $this->registry = $registry;
        $this->config = $config;
        $this->context = $context;
        parent::__construct($context, $data);
    }

    public function getProductSnippets()
    {
        return $this->snippet->getProductSnippets();
    }
}
