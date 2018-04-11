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
    
    const REGISTRY_KEY = 'skyhub_store_iterator_iterating';
    
    
    /** @var Mage_Core_Model_Store */
    protected $initialStore = null;
    
    /** @var array */
    protected $stores = [];
    
    
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
     * @param object $object
     * @param string $method
     * @param array  $params
     *
     * @return $this
     */
    public function iterate($object, $method, array $params = [])
    {
        $this->initIterator()
            ->initStores();
        
        if (!$this->validateObjectMethod($object, $method)) {
            return $this;
        }
        
        /** @var Mage_Core_Model_Store $store */
        foreach ($this->getStores() as $store) {
            $this->simulateStore($store);
            call_user_func_array([$object, $method], $params);
        }
        
        $this->endIterator();
        
        return $this;
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
     *
     * @throws Mage_Core_Exception
     */
    protected function endIterator()
    {
        Mage::unregister(self::REGISTRY_KEY);
        return $this;
    }
    
    
    /**
     * @param Mage_Core_Model_Store $store
     *
     * @return $this
     */
    protected function simulateStore(Mage_Core_Model_Store $store)
    {
        Mage::app()->setCurrentStore($store);
        return $this;
    }
    
    
    /**
     * @param object $object
     * @param string $method
     *
     * @return bool
     */
    protected function validateObjectMethod($object, $method)
    {
        if (!is_object($object)) {
            return false;
        }
        
        if (!method_exists($object, $method)) {
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
    
            /** @var array $stores */
            $stores = Mage::app()->getStores();
    
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
        
        return true;
    }
}
