<?php

class Rave_Ravecheckout_Model_PaymentMethod extends Rave_Ravecheckout_Model_Payment_Abstract {

    protected $_code = 'ravecheckout';

    public function getOrderPlaceRedirectUrl() {
        return Mage::getUrl('ravecheckout/payment/process', array('_secure' => true));
    }

    public function getBaseUrl() {
        $goLive = Mage::getStoreConfig('payment/ravecheckout/go_live'); 
        return (bool) $goLive ? 'https://api.ravepay.co/' : 'https://rave-api-v2.herokuapp.com/';
    }

    public function getPaymentMethodStoredConfig() {
        $orderDetails = $this->getOrderTranxDetails();
        $orderDetails['public_key'] = $this->getConfigData('public_key');
        $orderDetails['modal_title'] = $this->getConfigData('modal_title');
        $orderDetails['modal_desc'] = $this->getConfigData('modal_desc');
        $orderDetails['country'] = $this->getConfigData('country');
        $orderDetails['logo'] = $this->getConfigData('logo');

        return $orderDetails;
    }

    protected function getTransactionRef(Mage_Sales_Model_Order $order) {
        $date = date('Y') . date('m');
        $tranxRef = 'RAVE-' . $date . '-' . $order->getIncrementId() . '-' . time();
        Mage::getModel('core/session')->setRavePayTranxRef($tranxRef);
        
        return $tranxRef;
    }

    public function requeryGateWay($flwRef, $secretKey) {
        $paymentMethod = Mage::getSingleton('ravecheckout/paymentMethod');
        $base_url = $paymentMethod->getBaseUrl();

        $URL = $base_url . "flwv3-pug/getpaidx/api/verify";
        $data = http_build_query(array(
            'flw_ref' => $flwRef,
            'SECKEY' => $secretKey
        ));

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $URL);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
//        curl_setopt($ch, CURLOPT_SSLVERSION, 3);
        $output = curl_exec($ch);
        $failed = curl_errno($ch);
        $error = curl_error($ch);
        curl_close($ch);

        return ($failed) ? $error : $output;
    }

    public function getIntegrityHash(array $data) {
        unset($data['order_id']);
        unset($data['order']);
        ksort($data);
        $concatData = '';
        foreach ($data as $value) {
            $concatData .= $value;
        }
        $concatData .= $this->getConfigData('secret_key');

        return hash('sha256', $concatData);
    }

    public function getPaymentData() {
        $orderDetails = $this->getPaymentMethodStoredConfig();
        $order = Mage::getSingleton('ravecheckout/order')->getOrder();
        $amount = $order->getGrandTotal();
        $orderId = $order->getId();

        return array(
            'amount' => $amount,
            'country' => $orderDetails['country'],
            'currency' => $orderDetails['currency_code'],
            'custom_description' => $orderDetails['modal_desc'],
            'custom_logo' => Mage::getDesign()->getSkinUrl() . 'custom/images/logo.svg',
            'custom_title' => $orderDetails['modal_title'],
            'customer_email' => $orderDetails['customer_email'],
            'customer_phonenumber' => $orderDetails['customer_phone_no'],
            'customer_firstname' => $order->getCustomerLastname(),
            'customer_lastname' => $order->getCustomerFirstname(),
            'pay_button_text' => 'Pay Now',
            'PBFPubKey' => $orderDetails['public_key'],
            'txref' => $this->getTransactionRef($order),
            'order_id' => $orderId,
            'order' => $order,
        );
    }

}
