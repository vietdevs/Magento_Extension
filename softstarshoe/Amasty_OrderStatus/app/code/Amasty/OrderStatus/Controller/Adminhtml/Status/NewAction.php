<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2017 Amasty (https://www.amasty.com)
 * @package Amasty_OrderStatus
 */


namespace Amasty\OrderStatus\Controller\Adminhtml\Status;

class NewAction extends \Magento\Backend\App\Action
{
    public function execute()
    {
        $this->_forward('edit');
    }

    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Amasty_OrderStatus::amostatus');
    }
}
