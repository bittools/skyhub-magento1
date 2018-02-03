<?php

trait BSeller_SkyHub_Trait_Service
{

    /**
     * @return BSeller_SkyHub_Model_Service
     */
    public function service()
    {
        return Mage::getSingleton('bseller_skyhub/service');
    }


    /**
     * @return \SkyHub\Api
     */
    public function api()
    {
        return $this->service()->api();
    }

}
