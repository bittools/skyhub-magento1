<?php

abstract class BSeller_SkyHub_Model_Integrator_Abstract implements BSeller_SkyHub_Model_Integrator_Interface
{

    use BSeller_SkyHub_Trait_Service,
        BSeller_SkyHub_Trait_Config;


    protected $eventPrefix = 'skyub_integrator';
    protected $eventType   = null;
    protected $eventMethod = null;
    protected $eventSuffix = null;
    protected $eventParams = [];


    /**
     * BSeller_SkyHub_Model_Integrator constructor.
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


    /**
     * @return string
     */
    protected function getEventName()
    {
        return vsprintf('%s_%s_%s_$s', [
            $this->eventPrefix,
            $this->eventType,
            $this->eventMethod,
            $this->eventSuffix,
        ]);
    }


    /**
     * @return $this
     */
    protected function resetEvent()
    {
        $this->eventType   = null;
        $this->eventMethod = null;

        return $this;
    }


    /**
     * @return $this
     */
    protected function beforeIntegration()
    {
        $this->resetEvent();

        $this->eventSuffix = 'before';
        Mage::dispatchEvent($this->getEventName(), (array) $this->eventParams);
        return $this;
    }


    /**
     * @return $this
     */
    protected function afterIntegration()
    {
        $this->eventSuffix = 'after';
        Mage::dispatchEvent($this->getEventName(), (array) $this->eventParams);
        return $this;
    }

}
