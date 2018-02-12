<?php

class BSeller_SkyHub_Model_Integrator_Catalog_Product extends BSeller_SkyHub_Model_Integrator_Abstract
{
    
    use BSeller_SkyHub_Trait_Entity,
        BSeller_SkyHub_Trait_Transformers,
        BSeller_SkyHub_Model_Integrator_Catalog_Product_Validation;

    /** @var string */
    protected $eventType = 'catalog_product';


    /**
     * @param Mage_Catalog_Model_Product $product
     *
     * @return bool|\SkyHub\Api\Handler\Response\HandlerInterface
     */
    public function createOrUpdate(Mage_Catalog_Model_Product $product)
    {
        $exists = $this->productExists($product->getId());

        if (true == $exists) {
            /** Update Product */
            return $this->update($product);
        }

        /** Create Product */
        $response = $this->create($product);

        if ($response && $response->success()) {
            $this->registerProductEntity($product->getId());
        }

        return $response;
    }

    
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

        $this->eventMethod = 'create';
        $this->eventParams = [
            'product'   => $product,
            'interface' => $interface,
        ];

        $this->beforeIntegration();
        $response = $interface->create();
        $this->eventParams[] = $response;
        $this->afterIntegration();

        return $response;
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

        $this->eventMethod = 'update';
        $this->eventParams = [
            'product'   => $product,
            'interface' => $interface,
        ];

        $this->beforeIntegration();
        $response = $interface->update();
        $this->eventParams[] = $response;
        $this->afterIntegration();

        return $response;
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

        $this->eventMethod = 'product';
        
        /** @var \SkyHub\Api\EntityInterface\Catalog\Product $interface */
        $interface = $this->api()
                          ->product()
                          ->entityInterface();
        $interface->setSku($sku);

        $this->beforeIntegration();
        $response = $interface->product();
        $this->afterIntegration();

        return $response;
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

        $this->eventMethod = 'products';
        
        /** @var \SkyHub\Api\EntityInterface\Catalog\Product $interface */
        $interface = $this->api()
                          ->product()
                          ->entityInterface();

        $this->beforeIntegration();
        $interface->setStatus($statusFilter);
        $this->afterIntegration();
        
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

        $this->eventMethod = 'urls';

        $this->beforeIntegration();
        $response = $interface->urls();
        $this->afterIntegration();

        return $response;
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

        $this->eventMethod = 'delete';

        $this->beforeIntegration();
        $response = $interface->delete();
        $this->afterIntegration();

        return $response;
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
