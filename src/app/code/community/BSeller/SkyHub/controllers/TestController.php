<?php

class BSeller_SkyHub_TestController extends BSeller_SkyHub_Controller_Front_Action
{

    use BSeller_SkyHub_Trait_Processors;


    public function productAttributesAction()
    {
        /** @var array $attributes */
        $attributes = Mage::getModel('catalog/product')->getAttributes();

        /** @var Mage_Catalog_Model_Resource_Attribute $attribute */
        foreach ($attributes as $attribute) {
            $this->catalogProductAttributeProcessor()->create($attribute);

        }
    }

}
