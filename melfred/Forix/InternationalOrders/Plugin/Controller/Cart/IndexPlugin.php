<?php
/**
 * Created by Mercurial
 * Job Title: Magento Developer
 * Project Name: M2.2.3-EE - Melfredborzall
 */
namespace Forix\InternationalOrders\Plugin\Controller\Cart;

class IndexPlugin
{
    /**
     * @var \Forix\InternationalOrders\Helper\Helper
     */
    protected $helper;

    /**
     * @var \Magento\Framework\Message\ManagerInterface
     */
    protected $messageManager;

    /**
     * @var \Magento\Framework\Controller\Result\RedirectFactory
     */
    protected $resultRedirectFactory;

    /**
     * IndexPlugin constructor.
     * @param \Forix\InternationalOrders\Helper\Helper $helper
     * @param \Magento\Framework\Message\ManagerInterface $messageManager
     */
    public function __construct(
        \Forix\InternationalOrders\Helper\Helper $helper,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        \Magento\Framework\Controller\Result\RedirectFactory $resultRedirectFactory
    ) {
        $this->helper = $helper;
        $this->messageManager = $messageManager;
        $this->resultRedirectFactory = $resultRedirectFactory;
    }

    public function aroundExecute(\Magento\Checkout\Controller\Cart\Index $subject, callable $proceed)
    {
        if(!$this->helper->isDomestic() && !$this->helper->isCustomerLogin()) {
            $this->messageManager->addError(__('Please login to request a quote.'));
            return $this->resultRedirectFactory->create()->setPath('customer/account/login');
        }
        return $proceed();
    }
}