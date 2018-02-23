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
            $child->setData('parent_product', $product);
            
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
        $typeInstance = $product->getTypeInstance(true);
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
        /** @var Mage_Catalog_Model_Resource_Eav_Attribute $attribute */
        foreach ($this->getConfigurableAttributes($product) as $attribute) {
            $interface->addVariationAttribute($attribute->getAttributeCode());
        }

        return $this;
    }
    
    
    /**
     * @param Mage_Catalog_Model_Product $product
     *
     * @return array
     */
    protected function getConfigurableAttributes(Mage_Catalog_Model_Product $product)
    {
        if (empty($this->configurableAttributes)) {
            /** @var Mage_Catalog_Model_Product_Type_Configurable $typeInstance */
            $typeInstance = $product->getTypeInstance();
    
            /** @var Mage_Catalog_Model_Resource_Product_Type_Configurable_Attribute_Collection $configurableAttributes */
            $configurableAttributes = $typeInstance->getUsedProductAttributes($product);
            
            foreach ($configurableAttributes as $attribute) {
                if (!$attribute || !$attribute->getAttributeId()) {
                    continue;
                }
    
                $this->configurableAttributes[$attribute->getId()] = $attribute;
            }
        }
        
        return (array) $this->configurableAttributes;
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
    
    
    /**
     * @param Mage_Catalog_Model_Product $product
     * @param Product\Variation          $variation
     *
     * @return $this
     */
    protected function addPricesToProductVariation(Mage_Catalog_Model_Product $product, Product\Variation $variation)
    {
        $additionalPrice = (float) $this->getConfigurableProductAdditionalPrice($product);
    
        /** @var Mage_Catalog_Model_Product $parentProduct */
        if (!$parentProduct = $this->getParentProduct($product)) {
            $parentProduct = $product;
        }
        
        /**
         * @var BSeller_SkyHub_Model_Catalog_Product_Attributes_Mapping $mappedPrice
         * @var BSeller_SkyHub_Model_Catalog_Product_Attributes_Mapping $mappedSpecialPrice
         */
        $mappedPrice        = $this->getMappedAttribute('price');
        $mappedSpecialPrice = $this->getMappedAttribute('promotional_price');
        
        /**
         * @var Mage_Eav_Model_Entity_Attribute $attributePrice
         * @var Mage_Eav_Model_Entity_Attribute $attributeSpecialPrice
         */
        $attributePrice        = $mappedPrice->getAttribute();
        $attributeSpecialPrice = $mappedSpecialPrice->getAttribute();
        
//        $price = $this->extractProductPrice($product, $attributePrice);
        $price = $this->extractProductPrice($parentProduct, $attributePrice);
        
        if (!empty($price)) {
            $price = (float) array_sum([$price, $additionalPrice]);
        } else {
            $price = null;
        }
    
        $variation->addSpecification($mappedPrice->getSkyhubCode(), $price);
        
//        $specialPrice = $this->extractProductSpecialPrice($product, $attributeSpecialPrice, $price);
        $specialPrice = $this->extractProductSpecialPrice($parentProduct, $attributeSpecialPrice, $price);
        
        if (!empty($specialPrice)) {
            $specialPrice = (float) array_sum([$specialPrice, $additionalPrice]);
        } else {
            $specialPrice = null;
        }
    
        $variation->addSpecification($mappedSpecialPrice->getSkyhubCode(), (float) $specialPrice);
        
        return $this;
    }
    
    
    /**
     * @param Mage_Catalog_Model_Product $product
     *
     * @return float
     */
    protected function getConfigurableProductAdditionalPrice(Mage_Catalog_Model_Product $product)
    {
        $additionalPrice = 0;
    
        /** @var Mage_Catalog_Model_Product $parentProduct */
        if (!$parentProduct = $this->getParentProduct($product)) {
            return $additionalPrice;
        }
        
        $filter = (array) $this->getAttributesFilter($product);
        
        /** @var BSeller_SkyHub_Model_Resource_Catalog_Product_Configurable_Price $resource */
        $resource           = Mage::getResourceModel('bseller_skyhub/catalog_product_configurable_price');
        $configurablePrices = $resource->getConfigurableOptionPrices($parentProduct->getId(), $filter);
    
        /** @var array $configurablePrice */
        foreach ($configurablePrices as $configurablePrice) {
            $additionalPrice += (float) $this->arrayExtract($configurablePrice, 'pricing_value');
        }
        
        return (float) $additionalPrice;
    }
    
    
    /**
     * @param Mage_Catalog_Model_Product $product
     *
     * @return array
     */
    protected function getAttributesFilter(Mage_Catalog_Model_Product $product)
    {
        $attributes = [];
        
        /** @var Mage_Catalog_Model_Product $parentProduct */
        if (!$parentProduct = $this->getParentProduct($product)) {
            return $attributes;
        }
    
        $usedAttributes = $this->getConfigurableAttributes($parentProduct);
    
        /** @var Mage_Eav_Model_Entity_Attribute $usedAttribute */
        foreach ($usedAttributes as $usedAttribute) {
            $attributeId    = $usedAttribute->getId();
            $attributeValue = $this->productAttributeRawValue($product, $usedAttribute->getAttributeCode()) ;
        
            $attributes[$attributeId] = $attributeValue;
        }
        
        return $attributes;
    }
    
    
    /**
     * @param Mage_Catalog_Model_Product $product
     *
     * @return bool|Mage_Catalog_Model_Product
     */
    protected function getParentProduct(Mage_Catalog_Model_Product $product)
    {
        /** @var Mage_Catalog_Model_Product $parentProduct */
        $parentProduct = $product->getData('parent_product');
    
        if (!$parentProduct || !$parentProduct->getId()) {
            return false;
        }
        
        return $parentProduct;
    }
}
