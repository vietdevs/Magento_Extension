<?php
/**
 * Created by Hidro Le.
 * Job Title: Magento Developer
 * Project Name: M2.2.3-EE - Melfredborzall
 * Date: 02/08/2018
 * Time: 10:34
 */

namespace Forix\ImportHelper\Model\Import\RawData\ColumnValidator\Product;

use Forix\ImportHelper\Model\Import\RawData\ColumnValidator\AbstractColumnType;

class DownloadValidator extends EmptyValidator
{
    protected $_itemFormat = '<li><a href="{{media url=wysiwyg/Home/{filename}}}">{title}</a></li>';

    /**
     * @param $value
     * @param $rowData
     * @return bool
     */
    public function validate($value, $rowData)
    {
        if (parent::validate($value, $rowData)) {
            $re = '/.*?\:{2}.*?\|/';
            if ('|' !== substr($value, -1)) {
                $value .= '|';
            }
            $matchCount = preg_match_all($re, $value);
            if (false !== $matchCount && 0 !== $matchCount) {
                if ($matchCount !== 1 && $matchCount % 2 !== 0) {
                    $this->_addMessages([self::ERROR_INVALID_FORMAT . ":" . "{$value}"]);
                    return false;
                }
                return true;
            }
            $this->_addMessages([self::ERROR_INVALID_FORMAT . ":" . "{$value}"]);
        }
        return false;
    }

    public function customValue($value, $rawData = [])
    {
        if (empty($this->getMessages())) {
            $newValue = [];
            $items = explode('|', $value);
            $replaceItems = ['{filename}', '{title}'];
            foreach ($items as $item) {
                if ($item) {
                    list($url, $title) = explode(":", $item);
                    if (strpos('http', $url)) {
                        $replaceItems[0] = '{{media url=wysiwyg/Home/{filename}}}';
                    }
                    $itemHtml = str_replace($replaceItems, [$url, $title], $this->_itemFormat);
                    $newValue[] = $itemHtml;
                }
            }
            return '<ul>' . (implode('', $newValue)) . '</ul>';
        } else {
            $this->_clearMessages();
        }
        return $value;
    }
}