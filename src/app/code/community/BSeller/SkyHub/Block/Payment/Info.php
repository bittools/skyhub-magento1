<?php

class BSeller_SkyHub_Block_Payment_Info extends Mage_Payment_Block_Info
{
    protected $_paymentSpecificInformation = null;

    public function _construct()
    {
        parent::_construct();
        $this->setTemplate('bseller/skyhub/payment/info/default.phtml');
    }

    public function getSkyhubJson()
    {
        $order = $this->getInfo()->getOrder();
        $json = $order->getBsellerSkyhubJson();
        if ($json) {
            return json_decode($json, true);
        }

        return null;
    }
}
