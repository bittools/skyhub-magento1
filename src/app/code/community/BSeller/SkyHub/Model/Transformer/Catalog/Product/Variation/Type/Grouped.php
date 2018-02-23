<?php

use SkyHub\Api\EntityInterface\Catalog\Product;

class BSeller_SkyHub_Model_Transformer_Catalog_Product_Variation_Type_Grouped
    extends BSeller_SkyHub_Model_Transformer_Catalog_Product_Variation_Type_Abstract
{

    /**
     * @param Mage_Catalog_Model_Product $product
     * @param Product                    $interface
     *
     * @return $this
     */
    public function create(Mage_Catalog_Model_Product $product, Product $interface)
    {
        /** @var Mage_Catalog_Model_Product_Type_Grouped $typeInstance */
        $typeInstance = $product->getTypeInstance();

        /** @var Mage_Catalog_Model_Resource_Product_Link_Product_Collection $collection */
        $collection = $typeInstance->getAssociatedProductCollection($product);

        /**
         * @var int                        $optionId
         * @var Mage_Catalog_Model_Product $childProduct
         */
        foreach ($collection as $productId => $childProduct) {
            /** @var Product\Variation $variation */
            $this->addVariation($childProduct, $interface);
        }

        return $this;
    }
}
