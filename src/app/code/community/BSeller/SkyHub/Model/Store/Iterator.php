<?php
/**
 * BSeller Platform | B2W - Companhia Digital
 *
 * Do not edit this file if you want to update this module for future new versions.
 *
 * @category  BSeller
 * @package   BSeller_SkyHub
 *
 * @copyright Copyright (c) 2018 B2W Digital - BSeller Platform. (http://www.bseller.com.br)
 *
 * @author    Tiago Sampaio <tiago.sampaio@e-smart.com.br>
 */

class BSeller_SkyHub_Model_Store_Iterator implements BSeller_SkyHub_Model_Store_Iterator_Interface
{
    
    use BSeller_SkyHub_Trait_Config,
        BSeller_SkyHub_Trait_Service;
    
    
    /** @var Mage_Core_Model_Store */
    protected $initialStore = null;
    
    /** @var Mage_Core_Model_Store */
    protected $previousStore = null;
    
    /** @var Mage_Core_Model_Store */
    protected $currentStore = null;
    
    /** @var array */
    protected $stores = array();
    
    
    public function __construct()
    {
        $this->initStores();
    }
    
    
    /**
     * @return array
     */
    public function getStores()
    {
        $this->initStores();
        return (array) $this->stores;
    }
    
    
    /**
     * @param object $subject
     * @param string $method
     * @param array  $params
     *
     * @return $this
     *
     * @throws Mage_Core_Exception
     */
    public function iterate($subject, $method, array $params = array())
    {
        $this->initIterator()
            ->initStores();
        
        if (!$this->validateObjectMethod($subject, $method)) {
            return $this;
        }
        
        $eventParams = array(
            'iterator' => $this,
            'subject'  => $subject,
            'method'   => $method,
            'params'   => $params,
        );
        
        Mage::dispatchEvent('bseller_skyhub_store_iterate_before', $eventParams);
        
        /** @var Mage_Core_Model_Store $store */
        foreach ($this->getStores() as $store) {
            $eventParams['store']          = $this->getCurrentStore();
            $eventParams['initial_store']  = $this->getInitialStore();
            $eventParams['previous_store'] = $this->getPreviousStore();
            
            Mage::dispatchEvent('bseller_skyhub_store_iterate', $eventParams);
            
            $this->call($subject, $method, $params, $store);
        }
    
        Mage::dispatchEvent('bseller_skyhub_store_iterate_after', $eventParams);
        
        $this->endIterator();
        
        return $this;
    }
    
    
    /**
     * @param object                $subject
     * @param string                $method
     * @param array                 $params
     * @param Mage_Core_Model_Store $store
     * @param bool                  $force
     *
     * @return mixed
     */
    public function call($subject, $method, array $params = array(), Mage_Core_Model_Store $store, $force = false)
    {
        if (!$this->validateStore($store) && !$force) {
            return false;
        }
        
        if (!$this->validateObjectMethod($subject, $method)) {
            return false;
        }
    
        $result = false;

        $previousStore = $this->currentStore;

        $this->simulateStore($store);
        
        try {
            $params['__store'] = $store;
            $result = call_user_func_array(array($subject, $method), $params);
        } catch (Exception $e) {
            Mage::logException($e);
        }
    
        if (!$previousStore) {
            return $result;
        }

        $this->simulateStore($previousStore);
        
        return $result;
    }
    
    
    /**
     * @param Mage_Core_Model_Store $store
     *
     * @return $this
     */
    public function simulateStore(Mage_Core_Model_Store $store)
    {
        try {
            Mage::app()->setCurrentStore($store);
            
            /** Reinitialize the service parameters. */
            $this->service()->initApi();
            
            $this->currentStore = $store;
        } catch (Exception $e) {
            Mage::logException($e);
        }
        
        return $this;
    }
    
    
    /**
     * @return bool|Mage_Core_Model_Store
     */
    public function getDefaultStore($onlyIfActive = false)
    {
        $store = Mage::app()->getDefaultStoreView();
        
        if (true === $onlyIfActive) {
            if (!$this->isModuleEnabled($store->getId())) {
                return false;
            }
        }
        
        return $store;
    }
    
    
    /**
     * @return Mage_Core_Model_Store
     */
    public function getCurrentStore()
    {
        return $this->currentStore;
    }
    
    
    /**
     * @return Mage_Core_Model_Store
     */
    public function getPreviousStore()
    {
        return $this->previousStore;
    }
    
    
    /**
     * @return Mage_Core_Model_Store
     */
    public function getInitialStore()
    {
        return $this->initialStore;
    }
    
    
    /**
     * @return bool
     */
    public function isIterating()
    {
        return (bool) Mage::registry(self::REGISTRY_KEY);
    }
    
    
    /**
     * @return $this
     *
     * @throws Mage_Core_Exception
     */
    protected function initIterator()
    {
        Mage::register(self::REGISTRY_KEY, true, true);
        return $this;
    }
    
    
    /**
     * @return $this
     */
    protected function endIterator()
    {
        Mage::unregister(self::REGISTRY_KEY);
        $this->reset();
        
        return $this;
    }
    
    
    /**
     * @return $this
     */
    public function reset()
    {
        $this->simulateStore($this->getInitialStore());
        $this->clear();
        
        return $this;
    }
    
    
    /**
     * @return $this
     */
    protected function clear()
    {
        $this->previousStore = null;
        $this->currentStore  = null;
        
        return $this;
    }
    
    
    /**
     * @param object $subject
     * @param string $method
     *
     * @return bool
     */
    protected function validateObjectMethod($subject, $method)
    {
        if (!is_object($subject)) {
            return false;
        }
        
        if (!method_exists($subject, $method)) {
            return false;
        }
        
        if (!is_callable(array($subject, $method))) {
            return false;
        }
        
        return true;
    }
    
    
    /**
     * @return $this
     */
    protected function initStores()
    {
        if (!empty($this->stores)) {
            return $this;
        }
        
        try {
            $this->initialStore = Mage::app()->getStore();
            $this->currentStore = Mage::app()->getStore();
    
            /** @var array $stores */
            $stores = Mage::app()->getStores();
    
            Mage::dispatchEvent(
                'bseller_skyhub_store_init_stores',
                array(
                    'stores' => $stores,
                )
            );
    
            /** @var Mage_Core_Model_Store $store */
            foreach ($stores as $store) {
                $this->addStore($store);
            }
        } catch (Exception $e) {
            Mage::logException($e);
        }
        
        return $this;
    }
    
    
    /**
     * @param Mage_Core_Model_Store $store
     *
     * @return $this
     */
    protected function addStore(Mage_Core_Model_Store $store)
    {
        if (!$this->validateStore($store)) {
            return $this;
        }
        
        $this->stores[$store->getId()] = $store;
        return $this;
    }
    
    
    /**
     * @param Mage_Core_Model_Store $store
     *
     * @return bool
     */
    protected function validateStore(Mage_Core_Model_Store $store)
    {
        if ($store->isAdmin()) {
            return false;
        }
        
        if (!$store->getIsActive()) {
            return false;
        }
        
        if (!$this->isModuleEnabled($store->getId())) {
            return false;
        }
        
        if (!$this->isConfigurationOk($store->getId())) {
            return false;
        }
        
        return true;
    }
}
