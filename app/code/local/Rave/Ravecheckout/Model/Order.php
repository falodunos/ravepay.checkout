<?php

/**
 * RavePay (Powered by Flutterwave).
 * Refactored By:    Falodun O. Solomon, falodunosolomon@gmail.com
 * @category    Rave
 */
class Rave_Ravecheckout_Model_Order extends Mage_Sales_Model_Order {

    public function getLastOrderId() {
        return Mage::getModel('ravecheckout/paymentMethod')
                        ->getCheckoutSession()
                        ->getLastRealOrder()
                        ->getId();
    }

    public function getOrder() {
        return $this->load($this->getLastOrderId());
    }

}
