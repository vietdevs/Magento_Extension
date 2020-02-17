<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_Shopby
 */


namespace Amasty\Shopby\Block\Navigation\Widget;

use Magento\Framework\View\Element\Template;
use Magento\Catalog\Model\Layer\Filter\FilterInterface;
use Amasty\Shopby\Helper\UrlBuilder;

class FromTo extends \Magento\Framework\View\Element\Template implements WidgetInterface
{
    /**
     * @var \Amasty\ShopbyBase\Api\Data\FilterSettingInterface
     */
    private $filterSetting;

    /**
     * @var string
     */
    protected $_template = 'layer/widget/fromto.phtml';

    /**
     * @var \Amasty\Shopby\Helper\Data
     */
    private $helper;

    /**
     * @var string
     */
    private $type;

    /**
     * @var  FilterInterface
     */
    private $filter;

    /**
     * @var UrlBuilder
     */
    private $urlBuilder;

    public function __construct(
        Template\Context $context,
        \Amasty\Shopby\Helper\Data $helper,
        UrlBuilder $urlBuilder,
        array $data = []
    ) {
        $this->helper = $helper;
        parent::__construct($context, $data);
        $this->urlBuilder = $urlBuilder;
    }

    /**
     * @param \Amasty\ShopbyBase\Api\Data\FilterSettingInterface $filterSetting
     * @return $this
     */
    public function setFilterSetting(\Amasty\ShopbyBase\Api\Data\FilterSettingInterface $filterSetting)
    {
        $this->filterSetting = $filterSetting;
        return $this;
    }

    /**
     * @return string
     */
    public function getSliderUrlTemplate()
    {
        return $this->urlBuilder->buildUrl($this->getFilter(), 'amshopby_slider_from-amshopby_slider_to');
    }

    /**
     * @return string
     */
    public function collectFilters()
    {
        return $this->helper->collectFilters();
    }

    /**
     * @return \Amasty\ShopbyBase\Api\Data\FilterSettingInterface
     */
    public function getFilterSetting()
    {
        return $this->filterSetting;
    }

    /**
     * @param $type
     * @return $this
     */
    public function setWidgetType($type)
    {
        $this->type = $type;
        return $this;
    }

    /**
     * @return string
     */
    public function getWidgetType()
    {
        return $this->type;
    }

    /**
     * @param $filter
     * @return $this
     */
    public function setFilter($filter)
    {
        $this->filter = $filter;
        return $this;
    }

    /**
     * @return FilterInterface
     */
    public function getFilter()
    {
        return $this->filter;
    }
}
