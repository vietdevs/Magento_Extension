<?php
/**
 * Created by PhpStorm.
 * User: joel
 * Date: 17/10/2016
 * Time: 16:09
 */

namespace Magenest\SagepayUS\Model;

use Magenest\SagepayUS\Helper\Constant;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\HTTP\ZendClientFactory;
use Magento\Sales\Model\Order;

class SagepayUSPayment extends \Magento\Payment\Model\Method\AbstractMethod
{
    const CODE = 'magenest_sagepayus';

    protected $_code = self::CODE;
    protected $_isGateway = true;
    protected $_canAuthorize = true;
    protected $_canCapture = true;
    protected $_canRefund = false;
    protected $_canVoid = false;
    protected $_canUseInternal = false;
    protected $customerSession;
    protected $backendAuthSession;
    protected $sessionQuote;
    protected $checkoutSession;
    protected $quoteRepository;
    protected $quoteManagement;
    protected $_messageManager;
    protected $checkoutData;
    protected $configHelper;
    protected $_httpClientFactory;
    protected $sageLogger;

    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Api\ExtensionAttributesFactory $extensionFactory,
        \Magento\Framework\Api\AttributeValueFactory $customAttributeFactory,
        \Magento\Payment\Helper\Data $paymentData,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Payment\Model\Method\Logger $logger,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Backend\Model\Auth\Session $backendAuthSession,
        \Magento\Backend\Model\Session\Quote $sessionQuote,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Quote\Api\CartRepositoryInterface $quoteRepository,
        \Magento\Quote\Api\CartManagementInterface $quoteManagement,
        \Magenest\SagepayUS\Helper\ConfigHelper $configHelper,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        \Magento\Checkout\Helper\Data $checkoutData,
        ZendClientFactory $clientFactory,
        \Magenest\SagepayUS\Helper\Logger $sageLogger,
        array $data = []
    ) {
        parent::__construct(
            $context,
            $registry,
            $extensionFactory,
            $customAttributeFactory,
            $paymentData,
            $scopeConfig,
            $logger,
            null,
            null,
            $data
        );
        $this->customerSession = $customerSession;
        $this->backendAuthSession = $backendAuthSession;
        $this->sessionQuote = $sessionQuote;
        $this->configHelper = $configHelper;
        $this->checkoutSession = $checkoutSession;
        $this->quoteRepository = $quoteRepository;
        $this->quoteManagement = $quoteManagement;
        $this->_messageManager = $messageManager;
        $this->checkoutData = $checkoutData;
        $this->_httpClientFactory = $clientFactory;
        $this->sageLogger = $sageLogger;
    }

    public function assignData(\Magento\Framework\DataObject $data)
    {
        parent::assignData($data);

        $_tmpData = $data->_data;
        $additionalDataRef = $_tmpData['additional_data'];
        $sageResp = $additionalDataRef['sage_resp'];
        $sageHash = $additionalDataRef['sage_hash'];
        $sageCardInfo = $additionalDataRef['sage_cardinfo'];
        $saveCardCheckbox = $additionalDataRef['sage_savecard'];

        $infoInstance = $this->getInfoInstance();
        $infoInstance->setAdditionalInformation('sage_resp', $sageResp);
        $infoInstance->setAdditionalInformation('sage_hash', $sageHash);
        $infoInstance->setAdditionalInformation('sage_cardinfo', $sageCardInfo);
        $infoInstance->setAdditionalInformation('sage_savecard', $saveCardCheckbox);
        return $this;
    }

    public function authorize(\Magento\Payment\Model\InfoInterface $payment, $amount)
    {
        $this->sageLogger->debug("Authorize order: ".$payment->getOrder()->getIncrementId());
        $this->performPayment($payment, $amount);
        return parent::authorize($payment, $amount);
    }


    /**
     * @param \Magento\Payment\Model\InfoInterface|\Magento\Sales\Model\Order\Payment $payment
     * @param float $amount
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function capture(\Magento\Payment\Model\InfoInterface $payment, $amount)
    {
        $this->sageLogger->debug("Capture order: ".$payment->getOrder()->getIncrementId());
        $this->performPayment($payment, $amount);
        return parent::capture($payment, $amount); // TODO: Change the autogenerated stub
    }

    /**
     * @param \Magento\Payment\Model\InfoInterface|\Magento\Sales\Model\Order\Payment $payment
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    private function performPayment($payment, $amount){
        /**
         * @var \Magento\Sales\Model\Order $order
         * @var \Magento\Sales\Model\Order\Address $billing
         */
        $sageResp = $payment->getAdditionalInformation('sage_resp');
        $sageHash = $payment->getAdditionalInformation('sage_hash');
        $sageCardInfo = $payment->getAdditionalInformation('sage_cardinfo');
        $payRespObject = json_decode($sageResp, true);
        $responseObject = @$payRespObject['gatewayResponse'];
        $amountValidate = @$payRespObject['data'];
        $this->sageLogger->debug(var_export($payRespObject, true));
        if (@$responseObject['status'] == 'Approved') {
            $transactionId = @$responseObject['transactionId'];
            $payment->setCcAvsStatus(@$responseObject['avsResult']);
            $payment->setCcSecureVerify(@$responseObject['cvvResult']);
            $payment->setTransactionId($transactionId);
            $payment->setLastTransId($transactionId);
            $payment->setShouldCloseParentTransaction(1);
            $payment->setIsTransactionClosed(1);
            $this->savePayaResponse($payment, $responseObject);
            $payment->setAdditionalInformation("paya_amount_paid", $amountValidate);
            $hashCheck = "Pass";
            //edit by Forix
            $amountValidate = round($amountValidate,2);

            if (bccomp($amountValidate, $amount, 2) != 0) {
                //submit fraud order if amount is not equal
                $payment->setIsFraudDetected(true);
            }
            if (!$this->configHelper->validateResponse($sageResp, $sageHash)) {
                //submit fraud order if hash is not equal
                $hashCheck = "Fail";
                $payment->setIsFraudDetected(true);
            }else{
                if (isset($responseObject['vaultResponse'])) {
                    $saveCardCheckbox = $payment->getAdditionalInformation("sage_savecard");
                    $this->configHelper->saveCard($responseObject['vaultResponse'], $sageCardInfo, $saveCardCheckbox);
                }
            }
            $payment->setAdditionalInformation("paya_hash_check", $hashCheck);
        } else {
            throw new \Magento\Framework\Exception\LocalizedException(
                __('Payment Declined')
            );
        }
    }

    /**
     * @param \Magento\Payment\Model\InfoInterface|\Magento\Sales\Model\Order\Payment $payment
     */
    private function savePayaResponse($payment, $responseObject){
        $listParams = ['status', 'reference', 'message', 'code', 'cvvResult', 'avsResult', 'riskCode', 'networkId', 'isPurchaseCard', 'orderNumber', 'transactionId'];
        $data = [];
        foreach ($responseObject as $k => $v){
            if(in_array($k, $listParams)){
                $data[] = [
                   'label' => $k,
                   'value' => $v
                ];;
            }
        }
        $payment->setAdditionalInformation("paya_response_data", json_encode($data));
    }
}
