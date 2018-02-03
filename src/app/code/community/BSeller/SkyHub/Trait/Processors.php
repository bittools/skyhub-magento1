<?php

trait BSeller_SkyHub_Trait_Processors
{

    /**
     * @return BSeller_SkyHub_Model_Processor_Catalog_Product_Attribute
     */
    protected function catalogProductAttributeProcessor()
    {
        /** @var BSeller_SkyHub_Model_Processor_Catalog_Product_Attribute $attributeProcessor */
        $attributeProcessor = Mage::getModel('bseller_skyhub/processor_catalog_product_attribute');
        return $attributeProcessor;
    }

}
