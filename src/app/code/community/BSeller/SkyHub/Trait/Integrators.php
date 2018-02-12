<?php

trait BSeller_SkyHub_Trait_Integrators
{

    /**
     * @return BSeller_SkyHub_Model_Integrator_Catalog_Product_Attribute
     */
    protected function catalogProductAttributeIntegrator()
    {
        /** @var BSeller_SkyHub_Model_Integrator_Catalog_Product_Attribute $integrator */
        $integrator = Mage::getModel('bseller_skyhub/integrator_catalog_product_attribute');
        return $integrator;
    }


    /**
     * @return BSeller_SkyHub_Model_Integrator_Catalog_Product
     */
    protected function catalogProductIntegrator()
    {
        /** @var BSeller_SkyHub_Model_Integrator_Catalog_Product $integrator */
        $integrator = Mage::getModel('bseller_skyhub/integrator_catalog_product');
        return $integrator;
    }


    /**
     * @return BSeller_SkyHub_Model_Integrator_Catalog_Category
     */
    protected function catalogCategoryIntegrator()
    {
        /** @var BSeller_SkyHub_Model_Integrator_Catalog_Category $integrator */
        $integrator = Mage::getModel('bseller_skyhub/integrator_catalog_category');
        return $integrator;
    }
}
