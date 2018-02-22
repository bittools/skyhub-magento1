<?php

use SkyHub\Api\EntityInterface\Catalog\Product;

class BSeller_SkyHub_Model_Transformer_Catalog_Product_Variation_Type_Configurable
    extends BSeller_SkyHub_Model_Transformer_Catalog_Product_Variation_Type_Abstract
{

    use BSeller_SkyHub_Trait_Catalog_Product,
        BSeller_SkyHub_Trait_Eav_Option;
    
    
    /** @var array */
    protected $configurableAttributes = [];
    
    
    /**
     * @param Mage_Catalog_Model_Product $product
     * @param Product                    $interface
     *
     * @return $this
     *
     * @throws Mage_Core_Exception
     */
    public function create(Mage_Catalog_Model_Product $product, Product $interface)
    {
        $this->prepareProductVariationAttributes($product, $interface);

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

        $children = $this->getChildrenProducts($product);
        
        if (empty($children)) {
            return $this;
        }

        /** @var Mage_Catalog_Model_Product $child */
        foreach ($children as $child) {
            /** @var Product\Variation $variation */
            $variation = $this->addVariation($child, $interface);
        }

        return $this;
    }
    
    
    /**
     * @param Mage_Catalog_Model_Product $product
     *
     * @return array
     */
    protected function getChildrenProducts(Mage_Catalog_Model_Product $product)
    {
        /** @var Mage_Catalog_Model_Product_Type_Configurable $typeInstance */
        $typeInstance = $product->getTypeInstance();
        $usedProducts = $typeInstance->getUsedProducts(null, $product);
        
        return $usedProducts;
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
    
    
    /**
     * @param Mage_Catalog_Model_Product $product
     * @param Product\Variation          $variation
     *
     * @return $this
     *
     * @throws Mage_Core_Exception
     */
    protected function addSpecificationsToVariation(Mage_Catalog_Model_Product $product, Product\Variation $variation)
    {
        /** @var Mage_Eav_Model_Entity_Attribute $configurableAttribute */
        foreach ($this->configurableAttributes as $configurableAttribute) {
            $code  = $configurableAttribute->getAttributeCode();
            $value = $this->productAttributeRawValue($product, $code);
            $text  = $configurableAttribute->getSource()->getOptionText($value);
            
            if (!$text) {
                continue;
            }
            
            $variation->addSpecification($code, $text);
        }
        
        parent::addSpecificationsToVariation($product, $variation);
        
        return $this;
    }
}
