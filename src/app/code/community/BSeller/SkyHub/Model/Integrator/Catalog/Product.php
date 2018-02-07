<?php

class BSeller_SkyHub_Model_Integrator_Catalog_Product extends BSeller_SkyHub_Model_Integrator_IntegratorAbstract
{
    
    use BSeller_SkyHub_Trait_Transformers,
        BSeller_SkyHub_Model_Integrator_Catalog_Product_Validation;
    
    
    /**
     * @param Mage_Catalog_Model_Product $product
     *
     * @return bool|\SkyHub\Api\Handler\Response\HandlerInterface
     */
    public function create(Mage_Catalog_Model_Product $product)
    {
        if (!$this->canIntegrateProduct($product)) {
            return false;
        }
        
        /** @var \SkyHub\Api\EntityInterface\Catalog\Product $interface */
        $interface = $this->productTransformer()
                          ->convert($product);
        return $interface->create();
    }
    
    
    /**
     * @param Mage_Catalog_Model_Product $product
     *
     * @return bool|\SkyHub\Api\Handler\Response\HandlerInterface
     */
    public function update(Mage_Catalog_Model_Product $product)
    {
        if (!$this->canIntegrateProduct($product)) {
            return false;
        }
        
        /** @var \SkyHub\Api\EntityInterface\Catalog\Product $interface */
        $interface = $this->productTransformer()
                          ->convert($product);
        return $interface->update();
    }
    
    
    /**
     * @param string $sku
     *
     * @return bool|\SkyHub\Api\Handler\Response\HandlerInterface
     */
    public function product($sku)
    {
        if (!$this->validateSku($sku)) {
            return false;
        }
        
        /** @var \SkyHub\Api\EntityInterface\Catalog\Product $interface */
        $interface = $this->api()
                          ->product()
                          ->entityInterface();
        $interface->setSku($sku);
        
        return $interface->product();
    }
    
    
    /**
     * @param null|bool $statusFilter
     *
     * @return bool|\SkyHub\Api\Handler\Response\HandlerInterface
     */
    public function products($statusFilter = null)
    {
        if (!is_null($statusFilter) || !is_bool($statusFilter)) {
            return false;
        }
        
        /** @var \SkyHub\Api\EntityInterface\Catalog\Product $interface */
        $interface = $this->api()
                          ->product()
                          ->entityInterface();
        $interface->setStatus($statusFilter);
        
        return $interface->products();
    }
    
    
    /**
     * @return \SkyHub\Api\Handler\Response\HandlerInterface
     */
    public function urls()
    {
        /** @var \SkyHub\Api\EntityInterface\Catalog\Product $interface */
        $interface = $this->api()
                          ->product()
                          ->entityInterface();
        
        return $interface->urls();
    }
    
    
    /**
     * @param $sku
     *
     * @return bool|\SkyHub\Api\Handler\Response\HandlerInterface
     */
    public function delete($sku)
    {
        if (!$this->validateSku($sku)) {
            return false;
        }
        
        /** @var \SkyHub\Api\EntityInterface\Catalog\Product $interface */
        $interface = $this->api()
                          ->product()
                          ->entityInterface();
        $interface->setSku($sku);
        
        return $interface->delete();
    }
    
    
    /**
     * @param string $sku
     *
     * @return bool
     */
    public function validateSku($sku)
    {
        if (empty($sku)) {
            return false;
        }
        
        return true;
    }
    
}
