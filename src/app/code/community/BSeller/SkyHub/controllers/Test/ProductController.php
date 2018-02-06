<?php

class BSeller_SkyHub_Test_ProductController extends BSeller_SkyHub_Controller_Front_Action
{

    use BSeller_SkyHub_Trait_Integrator;


    public function attributesAction()
    {
        /** @var array $attributes */
        $attributes = Mage::getModel('catalog/product')->getAttributes();

        /** @var Mage_Catalog_Model_Resource_Eav_Attribute $attribute */
        foreach ($attributes as $attribute) {
            $result = $this->catalogProductAttributeProcessor()->create($attribute);
            
            if (!$result) {
                continue;
            }
            
            break;
        }
    }
    
    
    public function entityAction()
    {
        $productId = $this->getRequest()->getParam('id');
        
        /** @var Mage_Catalog_Model_Product $product */
        $product = Mage::getModel('catalog/product')->load($productId);
        
    }

}
