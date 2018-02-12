<?php

use SkyHub\Api\EntityInterface\Catalog\Product;

class BSeller_SkyHub_Model_Transformer_Catalog_Product_Variation_Type_Configurable
    extends BSeller_SkyHub_Model_Transformer_Catalog_Product_Variation_Type_Abstract
{

    use BSeller_SkyHub_Trait_Catalog_Product;


    /**
     * @param Mage_Catalog_Model_Product $product
     * @param Product                    $interface
     *
     * @return $this
     */
    public function create(Mage_Catalog_Model_Product $product, Product $interface)
    {
        $this->prepareProductVariationAttributes($product, $interface);

        /** @var Mage_Catalog_Model_Product_Type_Configurable $typeInstance */
        $typeInstance = $product->getTypeInstance();

        /** @var array $configurationOptions */
        /**
        $configurationOptions = $typeInstance->getConfigurableOptions($product);

        $options = [];

        /** @var array $option * /
        foreach ($configurationOptions as $optionId => $configurationOption) {
            foreach ($configurationOption as $item) {
                $attributeCode = $item['attribute_code'];
                $optionTitle   = $item['option_title'];
                $isPercent     = $item['pricing_is_percent'];
                $pricingValue  = $item['pricing_value'];
                $sku           = $item['sku'];

                $options[$sku][$attributeCode] = [
                    'price' => $pricingValue
                ];
            }
        }
        */

        $childrenIds = (array) $typeInstance->getChildrenIds($product->getId());
        $childrenIds = (array) array_pop($childrenIds);

        if (empty($childrenIds)) {
            return $this;
        }

        /** @var Mage_Catalog_Model_Resource_Product_Collection $childrenCollection */
        $childrenCollection = Mage::getResourceModel('catalog/product_collection');
        $childrenCollection->addFieldToFilter('entity_id', ['in' => $childrenIds]);

        /** @var Mage_Catalog_Model_Product $child */
        foreach ($childrenCollection as $child) {
            /** @var Product\Variation $variation */
            $variation = $this->addVariation($child, $interface);
        }

        return $this;
    }


    /**
     * @param Mage_Catalog_Model_Product $product
     * @param Product                    $interface
     *
     * @return $this
     */
    public function prepareProductVariationAttributes(Mage_Catalog_Model_Product $product, Product $interface)
    {
        /** @var Mage_Catalog_Model_Product_Type_Configurable $typeInstance */
        $typeInstance = $product->getTypeInstance();

        /** @var Mage_Catalog_Model_Resource_Product_Type_Configurable_Attribute_Collection $configurableAttributes */
        $configurableAttributes = $typeInstance->getUsedProductAttributes($product);

        /** @var Mage_Catalog_Model_Resource_Eav_Attribute $attribute */
        foreach ($configurableAttributes as $attribute) {
            if (!$attribute || !$attribute->getAttributeId()) {
                continue;
            }

            $this->configurableAttributes[] = $attribute;

            $interface->addVariationAttribute($attribute->getAttributeCode());
        }

        return $this;
    }

}
