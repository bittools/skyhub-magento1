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
 * @author    Julio Reis <julio.reis@b2wdigital.com>
 */
trait BSeller_SkyHub_Model_Integrator_Catalog_Product_Type_Configurable_Validation
{
    /**
     * @param Mage_Catalog_Model_Product $product
     * @param bool $bypassVisibleCheck
     * @return bool
     */
    public function canIntegrateChildProduct(Mage_Catalog_Model_Product $product, $bypassVisibleCheck = false)
    {
        $ignoreMarketplace = $this->productAttributeRawValue($product, 'ignore_marketplace');
        return $ignoreMarketplace ? false : true;
    }
}