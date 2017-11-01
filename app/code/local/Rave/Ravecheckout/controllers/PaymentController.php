<?php
/**
 * RavePay (Powered by Flutterwave).
 * Refactored By:    Falodun O. Solomon, falodunosolomon@gmail.com
 * @category    Rave
 */
class Rave_Ravecheckout_PaymentController extends Mage_Core_Controller_Front_Action {

    public function processAction() {
        $this->loadLayout();
        $block = $this->getLayout()->createBlock('Mage_Core_Block_Template', 'ravecheckout', array('template' => 'ravecheckout/process.phtml'));
        $this->getLayout()->getBlock('root')->unsetChild('right');
        $this->getLayout()->getBlock('root')->unsetChild('left');
        $this->getLayout()->getBlock('content')->append($block);
        $this->renderLayout();
    }

    public function handlerAction() {
        $paymentMethod = Mage::getSingleton('ravecheckout/paymentMethod');
        $secretKey = $paymentMethod->getConfigData('secret_key'); 
        $flwRef = $this->getRequest()->get("flwRef");

        $order = Mage::getSingleton('ravecheckout/order')->getOrder();

        $txn = json_decode($paymentMethod->requeryGateWay($flwRef, $secretKey));

        if (!empty($txn->data) && $this->_is_successful($txn->data)) {
            $txref = $txn->data->tx_ref;
            $tx_ref_arr = explode('-', $txref);
            $txn_order_id = (int) $tx_ref_arr[2];

            $orderId = (int) $order->getIncrementId();

            $order_amount = number_format($order->getGrandTotal(), 2);
            $tx_amount = number_format($txn->data->amount, 2);

            if ($order_amount !== $tx_amount) {
                Mage::getSingleton('core/session')->addNotice("Kindly contact the administrator regarding the status of your order '" . $txn_order_id . "'");
                $comment = "<strong>Payment Successful</strong><br>"
                        . "Attention: New order has been placed on hold because of incorrect payment amount. Please, look into it. <br>"
                        . "Amount paid: $tx_amount <br> Order amount: $order_amount <br> Ref:</strong> $txref";
                $order->setState(Mage_Sales_Model_Order::STATE_HOLDED, true, $comment);
            } elseif ($txn_order_id !== $orderId) {
                Mage::getSingleton('core/session')->addNotice("Kindly contact the administrator regarding the status of your order '" . $txn_order_id . "'");
                $comment = "<strong>Payment Successful</strong><br>"
                        . "Attention: New order has been placed on hold because payment was made for a different order. Please, look into it. <br>"
                        . "Order id paid for: $txn_order_id <br> Current order: $orderId <br> Ref:</strong> $txref";
                $order->setState(Mage_Sales_Model_Order::STATE_HOLDED, true, $comment);
            } else {
                Mage::getSingleton('core/session')->addSuccess('Payment Successful');
                $comment = '<strong>Payment Successful</strong><br><strong>Transaction ref:</strong> ' . $txref;
                $order->setState(Mage_Sales_Model_Order::STATE_NEW, true, $comment);
            }
            
            $redirect_url = 'checkout/onepage/success';
        } else {
            $comment = '<br>Attention: Payment couldn\'t be confirmed; please, verify manually. <br>';
            $order->setState(Mage_Sales_Model_Order::STATE_PAYMENT_REVIEW, true, $comment);
            Mage::getSingleton('checkout/session')->addError("Attention: Payment couldn\'t be confirmed; please, verify manually. ");
            $redirect_url = 'checkout/onepage/failure';
        }

        $order->save()->setData();
        Mage_Core_Controller_Varien_Action::_redirect($redirect_url, array('_secure' => false));
    }

    private function _is_successful($data) {
        return $data->flwMeta->chargeResponse === '00' || $data->flwMeta->chargeResponse === '0';
    }

    public function getPaymentDetailsAction() {
        $paymentMethod = Mage::getSingleton('ravecheckout/paymentMethod');

        $data = $paymentMethod->getPaymentData();
        $data['integrity_hash'] = $paymentMethod->getIntegrityHash($data);
        $data['cart_url'] = Mage::getUrl('checkout/cart', array('_secure' => false));
        $data['payment_url'] = Mage::helper('ravecheckout')->getPaymentProcessUrl();

        $this->getResponse()->clearHeaders()->setHeader('Content-type', 'application/json', true);
        $this->getResponse()->setBody(json_encode($data));
    }

}
