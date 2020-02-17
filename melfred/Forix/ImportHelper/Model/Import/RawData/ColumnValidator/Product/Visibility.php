<?php
/**
 * Created by Hidro Le.
 * Job Title: Magento Developer
 * Project Name: M2.2.3-EE - Melfredborzall
 * Date: 24/07/2018
 * Time: 12:21
 */

namespace Forix\ImportHelper\Model\Import\RawData\ColumnValidator\Product;


class Visibility extends \Forix\ImportHelper\Model\Import\RawData\ColumnValidator\AbstractColumnType
{

    /**
     * @param $value
     * @param $rowData
     * @return bool
     */
    public function validate($value, $rowData)
    {
        $visibility = [
            'Not Visible Individually',
            'Catalog',
            'Search',
            'Catalog, Search'
        ];
        if(!in_array($value, $visibility)){
            $this->_addMessages([self::ERROR_INVALID_ATTRIBUTE_OPTION . " visibility: " . $value]);
            return false;
        }
        return true;
    }
}