<?php

class BSeller_SkyHub_Model_Integrator_Catalog_Category extends BSeller_SkyHub_Model_Integrator_Abstract
{
    
    use BSeller_SkyHub_Trait_Entity,
        BSeller_SkyHub_Trait_Transformers,
        BSeller_SkyHub_Model_Integrator_Catalog_Category_Validation;

    /** @var string */
    protected $eventType = 'catalog_category';


    /**
     * @param Mage_Catalog_Model_Category $category
     *
     * @return bool|\SkyHub\Api\Handler\Response\HandlerInterface
     */
    public function createOrUpdate(Mage_Catalog_Model_Category $category)
    {
        $exists = $this->categoryExists($category->getId());

        if (true == $exists) {
            /** Update Category */
            return $this->update($category);
        }

        /** Create Category */
        $response = $this->create($category);

        if ($response && $response->success()) {
            $this->registerCategoryEntity($category->getId());
        }

        return $response;
    }

    
    /**
     * @param Mage_Catalog_Model_Category $category
     *
     * @return bool|\SkyHub\Api\Handler\Response\HandlerInterface
     */
    public function create(Mage_Catalog_Model_Category $category)
    {
        if (!$this->canIntegrateCategory($category)) {
            return false;
        }

        /** @var \SkyHub\Api\EntityInterface\Catalog\Category $interface */
        $interface = $this->categoryTransformer()
                          ->convert($category);

        $this->eventMethod = 'create';
        $this->eventParams = [
            'category'  => $category,
            'interface' => $interface,
        ];

        $this->beforeIntegration();
        $response = $interface->create();
        $this->eventParams[] = $response;
        $this->afterIntegration();

        return $response;
    }
    
    
    /**
     * @param Mage_Catalog_Model_Category $category
     *
     * @return bool|\SkyHub\Api\Handler\Response\HandlerInterface
     */
    public function update(Mage_Catalog_Model_Category $category)
    {
        if (!$this->canIntegrateCategory($category)) {
            return false;
        }
        
        /** @var \SkyHub\Api\EntityInterface\Catalog\Category $interface */
        $interface = $this->categoryTransformer()
                          ->convert($category);

        $this->eventMethod = 'update';
        $this->eventParams = [
            'category'  => $category,
            'interface' => $interface,
        ];

        $this->beforeIntegration();
        $response = $interface->update();
        $this->eventParams[] = $response;
        $this->afterIntegration();

        return $response;
    }
    

    /**
     * @param int $categoryId
     *
     * @return bool|\SkyHub\Api\Handler\Response\HandlerInterface
     */
    public function delete($categoryId)
    {
        /** @var \SkyHub\Api\EntityInterface\Catalog\Category $interface */
        $interface = $this->api()->category()->entityInterface();
        $interface->setCode((int) $categoryId);

        $this->eventMethod = 'delete';

        $this->beforeIntegration();
        $response = $interface->delete();
        $this->afterIntegration();

        return $response;
    }
}
