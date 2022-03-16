<?php

use SkyHub\Api\EntityInterface\Catalog\Product;

class BSeller_SkyHub_Model_Transformer_Catalog_Product_Variation_Type_Configurable
    extends BSeller_SkyHub_Model_Transformer_Catalog_Product_Variation_Type_Abstract
{

    use BSeller_SkyHub_Trait_Catalog_Product,
        BSeller_SkyHub_Trait_Eav_Option,
        BSeller_SkyHub_Model_Integrator_Catalog_Product_Type_Configurable_Validation;
    
    
    /** @var array */
    protected $configurableAttributes    = array();

    /** @var array */
    protected $configurableAttributesPrices = array();
    
    
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
        $this->configurableAttributes       = array();
        $this->configurableAttributesPrices = array();

        $this->prepareProductVariationAttributes($product, $interface);

        /** @var array $children */
        $children = (array) $this->getChildrenProducts($product);
        
        if (empty($children)) {
            return $this;
        }

        /** @var Mage_Catalog_Model_Product $child */
        foreach ($children as $child) {
            if (!$this->canIntegrateChildProduct($child)) {
                continue;
            }

            $child->setData('parent_product', $product);

            /** @var Product\Variation $variation */
            $this->addVariation($child, $interface);
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
        /** @var Mage_Catalog_Model_Product $parentProduct */
        if (!$parentProduct = $this->getParentProduct($product)) {
            $parentProduct = $product;
        }

        $parentProduct->setData('current_child', $product);
        
        /**
         * @var BSeller_SkyHub_Model_Catalog_Product_Attributes_Mapping $mappedPrice
         * @var BSeller_SkyHub_Model_Catalog_Product_Attributes_Mapping $mappedSpecialPrice
         */
        $mappedPrice        = $this->getMappedAttribute('price');
        $mappedSpecialPrice = $this->getMappedAttribute('promotional_price');
        
        /** @var Mage_Eav_Model_Entity_Attribute $attributeSpecialPrice */
        $attributeSpecialPrice = $mappedSpecialPrice->getAttribute();

        /**
         * Add Price
         */
        $price = $mappedPrice->extractProductValue($parentProduct);
        
        if (!empty($price)) {
            $price = (float) $this->calculatePrice($parentProduct, (float) $price);
        } else {
            $price = null;
        }
    
        $variation->addSpecification($mappedPrice->getSkyhubCode(), $price);

        /**
         * Add Special Price
         */
        $specialPrice = $this->extractProductSpecialPrice($parentProduct, $attributeSpecialPrice, $price);

        if (!empty($specialPrice)) {
            $specialPrice = (float) $this->calculatePrice($parentProduct, (float) $specialPrice);
        }

        if (empty($specialPrice)) {
            $specialPrice = $price;
        }
    
        $variation->addSpecification($mappedSpecialPrice->getSkyhubCode(), $specialPrice);
        
        return $this;
    }


    /**
     * @param Mage_Catalog_Model_Product $configurableProduct
     * @param float                      $price
     *
     * @return float
     */
    protected function calculatePrice(Mage_Catalog_Model_Product $configurableProduct, $price)
    {
        /** @var array $priceData */
        foreach ($this->getConfigurableAttributePrices($configurableProduct) as $priceData) {
            $isPercent    = (bool)  $priceData['is_percent'];
            $pricingValue = (float) $priceData['pricing_value'];

            if (true === $isPercent) {
                $price += ($price * ($pricingValue/100));
            }

            if (false === $isPercent) {
                $price += $pricingValue;
            }
        }

        return (float) $price;
    }


    /**
     * @param Mage_Catalog_Model_Product $configurableProduct
     *
     * @return array
     */
    protected function getConfigurableAttributePrices(Mage_Catalog_Model_Product $configurableProduct)
    {
        $prices = array();

        /** @var Mage_Catalog_Model_Product $childProduct */
        if (!$childProduct = $this->getCurrentChildProduct($configurableProduct)) {
            return $prices;
        }

        /**
         * @var integer $attributeId
         * @var array   $pricesCollection
         */
        foreach ($this->extractConfigurableAttributePrices($configurableProduct) as $attributeId => $pricesCollection) {
            /** @var Mage_Eav_Model_Entity_Attribute $attribute */
            $attribute      = Mage::getModel('eav/entity_attribute')->load($attributeId);
            $attributeValue = $this->productAttributeRawValue($childProduct, $attribute);

            /** @var array $priceData */
            foreach ($pricesCollection as $priceData) {
                $valueIndex = $this->arrayExtract($priceData, 'value_index', 0.0000);

                if ($attributeValue != $valueIndex) {
                    continue;
                }

                $prices[] = $priceData;
            }
        }

        return $prices;
    }


    /**
     * @param Mage_Catalog_Model_Product $configurableProduct
     *
     * @return array
     */
    protected function extractConfigurableAttributePrices(Mage_Catalog_Model_Product $configurableProduct)
    {
        if (empty($this->configurableAttributesPrices)) {
            /**
             * CASE 1: When a product is just saved and the is being integrated via observer [after save event].
             */
            $usedAttributes = (array) $configurableProduct->getData('configurable_attributes_data');

            if (empty($usedAttributes)) {
                /**
                 * CASE 2: Otherwise the product was loaded and being integrated via another way.
                 */
                $usedAttributes = (array) $configurableProduct->getData('_cache_instance_used_attributes');
            }

            /** @var Mage_Catalog_Model_Product_Type_Configurable_Attribute|array $usedAttribute */
            foreach ($usedAttributes as $usedAttribute) {
                $attributeId = null;
                $prices      = array();

                if ($usedAttribute instanceof Mage_Catalog_Model_Product_Type_Configurable_Attribute) {
                    /**
                     * Refers to CASE 2 (product was loaded and integrated.)
                     */
                    $attributeId = $usedAttribute->getAttributeId();
                    $prices      = $usedAttribute->getData('prices');
                } elseif (isset($usedAttribute['attribute_id'])) {
                    /**
                     * Refers to CASE 1 (product was just saved and is being integrated via observer.)
                     */
                    $attributeId = $usedAttribute['attribute_id'];
                    $prices      = $usedAttribute['values'];
                }

                if (empty($attributeId) || empty($prices)) {
                    continue;
                }

                $this->configurableAttributesPrices[(int) $attributeId] = (array) $prices;
            }
        }

        return $this->configurableAttributesPrices;
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


    /**
     * @param Mage_Catalog_Model_Product $parentProduct
     *
     * @return bool|Mage_Catalog_Model_Product
     */
    protected function getCurrentChildProduct(Mage_Catalog_Model_Product $parentProduct)
    {
        /** @var Mage_Catalog_Model_Product $childProduct */
        $childProduct = $parentProduct->getData('current_child');

        if (!$childProduct || !$childProduct->getId()) {
            return false;
        }

        return $childProduct;
    }
}
