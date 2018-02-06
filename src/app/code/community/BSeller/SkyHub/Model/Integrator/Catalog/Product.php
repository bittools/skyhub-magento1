<?php

class BSeller_SkyHub_Model_Integrator_Catalog_Product extends BSeller_SkyHub_Model_Integrator_IntegratorAbstract
{
    
    use BSeller_SkyHub_Trait_Transformers;


    public function create(Mage_Catalog_Model_Product $product)
    {
        if (!$this->canIntegrateProduct($product)) {
            return false;
        }
        
        /** @var \SkyHub\Api\EntityInterface\Catalog\Product $interface */
        $interface = $this->productTransformer()->convert($product);
        return $interface->create();
    }
    
    
    /**
     * @param Mage_Catalog_Model_Product $product
     *
     * @return bool
     */
    public function canIntegrateProduct(Mage_Catalog_Model_Product $product)
    {
        return true;
    }
    
}
