<?php

use SkyHub\Api\Handler\Response\HandlerDefault;
use SkyHub\Api\Handler\Response\HandlerException;

class BSeller_SkyHub_Model_Integrator_Catalog_Product_Attribute extends BSeller_SkyHub_Model_Integrator_Abstract
{

    use BSeller_SkyHub_Trait_Entity,
        BSeller_SkyHub_Trait_Transformers;


    /**
     * @param Mage_Eav_Model_Entity_Attribute $attribute
     *
     * @return bool|\SkyHub\Api\Handler\Response\HandlerInterface
     */
    public function createOrUpdate(Mage_Eav_Model_Entity_Attribute $attribute)
    {
        $exists = $this->productAttributeExists($attribute->getId());

        if (true == $exists) {
            /** Update Product Attribute */
            return $this->update($attribute);
        }

        /** Create Product Attribute */
        $response = $this->create($attribute);

        if ($response->success()) {
            $this->registerProductAttributeEntity($attribute->getId());
        }

        return $response;
    }


    /**
     * @param Mage_Eav_Model_Entity_Attribute $attribute
     *
     * @return bool|HandlerDefault|HandlerException
     */
    public function create(Mage_Eav_Model_Entity_Attribute $attribute)
    {
        if (!$this->canIntegrateAttribute($attribute)) {
            return false;
        }

        /** @var SkyHub\Api\EntityInterface\Catalog\Product\Attribute $interface */
        $interface = $this->productAttributeTransformer()->convert($attribute);
        return $interface->create();
    }


    /**
     * @param Mage_Eav_Model_Entity_Attribute $attribute
     *
     * @return bool|HandlerDefault|HandlerException
     */
    public function update(Mage_Eav_Model_Entity_Attribute $attribute)
    {
        if (!$this->canIntegrateAttribute($attribute)) {
            return false;
        }

        /** @var SkyHub\Api\EntityInterface\Catalog\Product\Attribute $interface */
        $interface = $this->productAttributeTransformer()->convert($attribute);
        return $interface->update();
    }


    /**
     * @param Mage_Eav_Model_Entity_Attribute $attribute
     *
     * @return bool
     */
    protected function canIntegrateAttribute(Mage_Eav_Model_Entity_Attribute $attribute)
    {
        return (bool) ($attribute->getId() && $attribute->getAttributeCode() && $attribute->getFrontendLabel());
    }
}
