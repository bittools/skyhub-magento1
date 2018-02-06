<?php

use SkyHub\Api\Handler\Response\HandlerDefault;
use SkyHub\Api\Handler\Response\HandlerException;

class BSeller_SkyHub_Model_Processor_Catalog_Product_Attribute extends BSeller_SkyHub_Model_Processor
{

    use BSeller_SkyHub_Trait_Transformers;


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
