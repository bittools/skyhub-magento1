<?php
/**
 * BSeller Platform | B2W - Companhia Digital
 *
 * Do not edit this file if you want to update this module for future new versions.
 *
 * @category  BSeller
 * @package   BSeller_SkyHub
 *
 * @copyright Copyright (c) 2018 B2W Digital - BSeller Platform. (http://www.bseller.com.br)
 *
 * @author    Tiago Sampaio <tiago.sampaio@e-smart.com.br>
 */
trait BSeller_SkyHub_Model_Integrator_Catalog_Product_Validation
{

    use BSeller_SkyHub_Trait_Catalog_Product;


    /**
     * @param Mage_Catalog_Model_Product $product
     * @param bool                       $bypassVisibleCheck
     *
     * @return bool
     */
    public function canIntegrateProduct(Mage_Catalog_Model_Product $product, $bypassVisibleCheck = false)
    {
        if (!$product->getId()) {
            return false;
        }

        if (!$product->getSku()) {
            return false;
        }

        if (!$bypassVisibleCheck && !$product->isVisibleInSiteVisibility()) {
            return false;
        }

        /**
        switch ($this->getCatalogProductIntegrationMethod()) {
            case BSeller_SkyHub_Model_System_Config_Source_Integration_Method::INTEGRATION_METHOD_QUEUE:
                return false;
        }
        **/
        
        return true;
    }
    
}
