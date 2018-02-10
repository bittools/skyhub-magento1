<?php

use SkyHub\Api\EntityInterface\Catalog\Product;

interface BSeller_SkyHub_Model_Transformer_Catalog_Product_Variation_Type_Interface
{

    /**
     * @param Mage_Catalog_Model_Product $product
     * @param Product                    $interface
     *
     * @return $this
     */
    public function create(Mage_Catalog_Model_Product $product, Product $interface);


    /**
     * @param Mage_Catalog_Model_Product $product
     * @param Product                    $interface
     *
     * @return $this
     */
    public function prepareProductVariationAttributes(Mage_Catalog_Model_Product $product, Product $interface);

}
