<?php

use SkyHub\Api\Handler\Response\HandlerDefault;
use SkyHub\Api\Handler\Response\HandlerException;

class BSeller_SkyHub_Model_Processor_Catalog_Product_Attribute extends BSeller_SkyHub_Model_Processor
{

    /**
     * @param Mage_Catalog_Model_Resource_Eav_Attribute $attribute
     *
     * @return bool|HandlerDefault|HandlerException
     */
    public function create(Mage_Catalog_Model_Resource_Eav_Attribute $attribute)
    {
        if (!$this->canIntegrateAttribute($attribute)) {
            return false;
        }

        /** @var BSeller_SkyHub_Model_Transformer_Catalog_Product_Attribute $transformer */
        $transformer = Mage::getModel('bseller_skyhub/transformer_catalog_product_attribute');

        /** @var SkyHub\Api\EntityInterface\Catalog\Product\Attribute $entityInterface */
        $entityInterface = $transformer->convert($attribute);
        return $entityInterface->create();
    }


    public function update(Mage_Catalog_Model_Resource_Eav_Attribute $attribute)
    {
        if (!$this->canIntegrateAttribute($attribute)) {
            return false;
        }

        /** @var BSeller_SkyHub_Model_Transformer_Catalog_Product_Attribute $transformer */
        $transformer = Mage::getModel('bseller_skyhub/transformer_catalog_product_attribute');

        /** @var SkyHub\Api\EntityInterface\Catalog\Product\Attribute $entityInterface */
        $entityInterface = $transformer->convert($attribute);
        return $entityInterface->update();
    }


    /**
     * @return \SkyHub\Api\Handler\Request\Catalog\Product\AttributeHandler
     */
    public function handler()
    {
        return $this->api()->productAttribute();
    }


    /**
     * @param Mage_Catalog_Model_Resource_Eav_Attribute $attribute
     *
     * @return bool
     */
    protected function canIntegrateAttribute(Mage_Catalog_Model_Resource_Eav_Attribute $attribute)
    {
        return (bool) ($attribute->getId() && $attribute->getAttributeCode() && $attribute->getFrontendLabel());
    }
}
