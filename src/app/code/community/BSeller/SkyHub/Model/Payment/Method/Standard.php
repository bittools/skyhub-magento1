<?php

class BSeller_SkyHub_Model_Payment_Method_Standard extends Mage_Payment_Model_Method_Free
{

    protected $_code = 'free';


    /**
     * @param null $quote
     *
     * @return bool
     */
    public function isAvailable($quote = null)
    {
        return true;
    }

}
