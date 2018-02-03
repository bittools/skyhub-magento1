<?php

abstract class BSeller_SkyHub_Model_Processor
{

    use BSeller_SkyHub_Trait_Service,
        BSeller_SkyHub_Trait_Config;


    /**
     * BSeller_SkyHub_Model_Processor constructor.
     */
    public function __construct()
    {
        $this->init();
    }


    /**
     * @return $this
     */
    protected function init()
    {
        $defaultStore = Mage::app()->getDefaultStoreView();
        Mage::app()->setCurrentStore($defaultStore);

        return $this;
    }

}
