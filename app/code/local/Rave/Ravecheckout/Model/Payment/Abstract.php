<?php
/**
 * RavePay (Powered by Flutterwave).
 * author:    Falodun O. Solomon, falodunosolomon@gmail.com
 * @category    Rave
 */
abstract class Rave_Ravecheckout_Model_Payment_Abstract extends Mage_Payment_Model_Method_Abstract {

    protected $_helper = null;
    protected $_order = null;

    public function __construct() {
        if (is_null($this->_helper)) {
            $this->_helper = Mage::helper('ravecheckout/data');
            $this->_order = Mage::getModel('sales/order');
        }
        parent::__construct();
    }

    protected $_iso4217Currencies = array(
        'AED' => '784', 'AFN' => '971',
        'ALL' => '008', 'AMD' => '051', 'ANG' => '532', 'AOA' => '973', 'ARS' => '032', 'AUD' => '036', 'AWG' => '533',
        'AZN' => '944', 'BAM' => '977', 'BBD' => '052', 'BDT' => '050', 'BGN' => '975', 'BHD' => '048', 'BIF' => '108',
        'BMD' => '060', 'BND' => '096', 'BOB' => '068', 'BOV' => '984', 'BRL' => '986', 'BSD' => '044', 'BTN' => '064',
        'BWP' => '072', 'BYR' => '974', 'BZD' => '084', 'CAD' => '124', 'CDF' => '976', 'CHE' => '947', 'CHF' => '756',
        'CHW' => '948', 'CLF' => '990', 'CLP' => '152', 'CNY' => '156', 'COP' => '170', 'COU' => '970', 'CRC' => '188',
        'CUC' => '931', 'CUP' => '192', 'CVE' => '132', 'CZK' => '203', 'DJF' => '262', 'DKK' => '208', 'DOP' => '214',
        'DZD' => '012', 'EEK' => '233', 'EGP' => '818', 'ERN' => '232', 'ETB' => '230', 'EUR' => '978', 'FJD' => '242',
        'FKP' => '238', 'GBP' => '826', 'GEL' => '981', 'GHS' => '936', 'GIP' => '292', 'GMD' => '270', 'GNF' => '324',
        'GTQ' => '320', 'GYD' => '328', 'HKD' => '344', 'HNL' => '340', 'HRK' => '191', 'HTG' => '332', 'HUF' => '348',
        'IDR' => '360', 'ILS' => '376', 'INR' => '356', 'IQD' => '368', 'IRR' => '364', 'ISK' => '352', 'JMD' => '388',
        'JOD' => '400', 'JPY' => '392', 'KES' => '404', 'KGS' => '417', 'KHR' => '116', 'KMF' => '174', 'KPW' => '408',
        'KRW' => '410', 'KWD' => '414', 'KYD' => '136', 'KZT' => '398', 'LAK' => '418', 'LBP' => '422', 'LKR' => '144',
        'LRD' => '430', 'LSL' => '426', 'LTL' => '440', 'LVL' => '428', 'LYD' => '434', 'MAD' => '504', 'MDL' => '498',
        'MGA' => '969', 'MKD' => '807', 'MMK' => '104', 'MNT' => '496', 'MOP' => '446', 'MRO' => '478', 'MUR' => '480',
        'MVR' => '462', 'MWK' => '454', 'MXN' => '484', 'MXV' => '979', 'MYR' => '458', 'MZN' => '943', 'NAD' => '516',
        'NGN' => '566', 'NIO' => '558', 'NOK' => '578', 'NPR' => '524', 'NZD' => '554', 'OMR' => '512', 'PAB' => '590',
        'PEN' => '604', 'PGK' => '598', 'PHP' => '608', 'PKR' => '586', 'PLN' => '985', 'PYG' => '600', 'QAR' => '634',
        'RON' => '946', 'RSD' => '941', 'RUB' => '643', 'RWF' => '646', 'SAR' => '682', 'SBD' => '090', 'SCR' => '690',
        'SDG' => '938', 'SEK' => '752', 'SGD' => '702', 'SHP' => '654', 'SLL' => '694', 'SOS' => '706', 'SRD' => '968',
        'STD' => '678', 'SYP' => '760', 'SZL' => '748', 'THB' => '764', 'TJS' => '972', 'TMT' => '934', 'TND' => '788',
        'TOP' => '776', 'TRY' => '949', 'TTD' => '780', 'TWD' => '901', 'TZS' => '834', 'UAH' => '980', 'UGX' => '800',
        'USD' => '844', 'USN' => '997', 'USS' => '998', 'UYU' => '858', 'UZS' => '860', 'VEF' => '937', 'VND' => '704',
        'VUV' => '548', 'WST' => '882', 'XAF' => '950', 'XAG' => '961', 'XAU' => '959', 'XBA' => '955', 'XBB' => '956',
        'XBC' => '957', 'XBD' => '958', 'XCD' => '951', 'XDR' => '960', 'XOF' => '952', 'XPD' => '964', 'XPF' => '953',
        'XPT' => '962', 'XTS' => '963', 'XXX' => '999', 'YER' => '886', 'ZAR' => '710', 'ZMK' => '894', 'ZWL' => '932',
    );

    abstract public function requeryGateWay($param1, $param2);

    abstract public function getOrderPlaceRedirectUrl();

    abstract public function getPaymentMethodStoredConfig();

    /**
     * 
     * @return Mage_Checkout_Model_Session $session
     */
    public function getCheckoutSession() {
        return Mage::getSingleton('checkout/session');
    }

    /**
     * 
     * @return Mage_Core_Model_Session $session
     */
    public function getCoreSession() {
        return Mage::getSingleton('core/session');
    }

    public function getCurrencyNumber($currency_symbol) {
        $currency_array = $this->_iso4217Currencies;
        if ($currency_symbol) {
            return $currency_array[$currency_symbol];
        }
    }

    /**
     * 
     * @param type $increment_id
     * @return type Mage_Sales_Model_Order
     */
    public function getOrder($increment_id = null) {
        if ($this->_order->getId() !== null && $this->_order->getIncrementId() == $increment_id) {
            return $this->_order;
        }
        $this->_order = $this->_order->getIncrementId() != $increment_id && $increment_id != null ?
                $this->_order->loadByIncrementId($increment_id) : $this->_order;

        return $this->_order;
    }

    protected function _updateOrderStatus($order, $status, $comment) {
        $order->setState($status, true, $comment);
        $order->getSendConfirmation(null);
        $order->sendNewOrderEmail();
        $order->setEmailSent(true);
        $order->save();
    }

    /**
     * 
     * @param type $payment_code
     * @param type $config_keys
     * @return type array
     */
    public function getPaymentStoredConfig($payment_code, $config_keys) {
        $payment_config_keys = array();
        foreach ($config_keys as $config_key) {
            $payment_config_keys[$config_key] = Mage::getStoreConfig("payment/$payment_code/$config_key", Mage::app()->getStore());
        }
        return $payment_config_keys;
    }

    public function getOrderTranxDetails() {
        $customer = Mage::getModel('customer/customer');
        $customerData = null;
        $customer_phone_no = null;
        $order_increment_id = Mage::getSingleton('checkout/session')->getLastRealOrderId();
        $order = Mage::getModel('sales/order')->loadByIncrementId($order_increment_id);
        $currency_code = $order->getOrderCurrency()->getCurrencyCode();
        $tranx_verification_code = $this->getVerificationNumber();
        Mage::getSingleton('core/session')->setVerificationNumber($tranx_verification_code);

        if (!$order->getCustomerIsGuest()) {// i.e. if customer is logged in and not a guest
            $customer = Mage::getModel('customer/customer')->load(Mage::getSingleton('customer/session')->getCustomer()->getId());
            $customerData = $customer->getData();
            $customer_phone_no = $customer->getPrimaryBillingAddress()->getTelephone();
        } else {
            $customer_phone_no = Mage::getModel('sales/order_address')->load($order->getBillingAddressId())->getTelephone();
        }

        return array(
            'order_id' => $order_increment_id,
            'order' => $order,
            'tranx_amount' => floatval($order->getGrandTotal()),
            'currency_code' => $currency_code,
            'currency_symbol' => Mage::app()->getLocale()->currency($currency_code)->getSymbol(),
            'currency_number' => $this->getCurrencyNumber($currency_code),
            'tranx_verification_code' => $tranx_verification_code,
            'customer' => $customer,
            'customer_data' => $customerData,
            'customer_id' => $customer->getId() !== null ? $customer->getId() : '',
            'customer_name' => $order->getCustomerLastname() . ' ' . $order->getCustomerFirstname(),
            'customer_email' => $order->getCustomerEmail(),
            'payment_code' => $order->getPayment()->getMethodInstance()->getCode(),
            'customer_phone_no' => $customer_phone_no
        );
    }

}
