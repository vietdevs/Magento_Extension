<?php
/**
 * User: Forix
 * Date: 6/24/2016
 * Time: 1:24 PM
 */

namespace Forix\Bannerslider\Model\ResourceModel\Value;

/**
 * Value Collection
 * @category Forix
 * @package  Forix_Bannerslider
 * @module   Bannerslider
 * @author   Forix Developer
 */
class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     * construct
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Forix\Bannerslider\Model\Value', 'Forix\Bannerslider\Model\ResourceModel\Value');
    }
}
