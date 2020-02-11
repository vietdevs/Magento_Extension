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
 * @package   mirasvit/module-email-report
 * @version   1.0.5
 * @copyright Copyright (C) 2017 Mirasvit (https://mirasvit.com/)
 */



namespace Mirasvit\EmailReport\Model\ResourceModel;


use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Mirasvit\EmailReport\Api\Data\ClickInterface;

class Click extends AbstractDb
{
    /**
     * @inheritDoc
     */
    protected function _construct()
    {
        $this->_init(ClickInterface::TABLE_NAME, ClickInterface::ID);
    }
}
