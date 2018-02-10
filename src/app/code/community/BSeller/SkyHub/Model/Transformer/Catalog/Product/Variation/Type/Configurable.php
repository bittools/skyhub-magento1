<?php

use SkyHub\Api\EntityInterface\Catalog\Product;

class BSeller_SkyHub_Model_Transformer_Catalog_Product_Variation_Type_Configurable
    extends BSeller_SkyHub_Model_Transformer_Catalog_Product_Variation_Type_Abstract
{

    use BSeller_SkyHub_Trait_Catalog_Product;


    /** @var array */
    protected $configurableAttributes = [];

    /** @var BSeller_SkyHub_Model_Resource_Catalog_Product_Attributes_Mapping_Collection */
    protected $mappedAttributesCollection;


    public function __construct()
    {
        $this->mappedAttributesCollection = Mage::getResourceModel(
            'bseller_skyhub/catalog_product_attributes_mapping_collection'
        );
    }


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
        $configurationOptions = $typeInstance->getConfigurableOptions($product);

        $options = [];

        /** @var array $option */
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

        foreach ($options as $sku => $option) {
            $productId = $product->getResource()->getIdBySku($sku);

            if (!$productId) {
                continue;
            }

            /** @var Mage_Catalog_Model_Product $childProduct */
            $childProduct = Mage::getModel('catalog/product')->load($productId);

            /** @var Product\Variation $variation */
            $variation = $this->addVariation($childProduct, $interface);
        }

        return $this;
    }


    /**
     * @param Mage_Catalog_Model_Product $product
     * @param Product                    $interface
     *
     * @return Product\Variation
     */
    protected function addVariation(Mage_Catalog_Model_Product $product, Product $interface)
    {
        /** @var Product\Variation $variation */
        $variation = $interface->addVariation($product->getSku(), $this->getStockQty($product));

        /**
         * EAN Attribute
         */

        /** @var BSeller_SkyHub_Model_Catalog_Product_Attributes_Mapping $mapping */
        $mapping = $this->mappedAttributesCollection->getBySkyHubCode('ean');

        /** @var Mage_Eav_Model_Entity_Attribute $attribute */
        if ($mapping->getId() && $attribute = $mapping->getAttribute()) {
            $ean = $this->productAttributeRawValue($product, $attribute->getAttributeCode());
            $variation->setEan($ean);
        }

        /**
         * Product Images.
         */
        $this->addImagesToVariation($product, $variation);

        /**
         * Product Variations.
         */
        $this->addSpecificationsToVariation($product, $variation);

        return $variation;
    }


    /**
     * @param Mage_Catalog_Model_Product $product
     * @param Product\Variation          $variation
     *
     * @return $this
     */
    protected function addImagesToVariation(Mage_Catalog_Model_Product $product, Product\Variation $variation)
    {
        /** @var Varien_Object $galleryImage */
        foreach ($product->getMediaGalleryImages() as $galleryImage) {
            $variation->addImage($galleryImage->getData('url'));
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

        $mapped = [
            $this->mappedAttributesCollection->getBySkyHubCode('price'),
            $this->mappedAttributesCollection->getBySkyHubCode('promotional_price'),
        ];

        /** @var BSeller_SkyHub_Model_Catalog_Product_Attributes_Mapping $fixedAttribute */
        foreach ($mapped as $fixedAttribute) {
            /** @var Mage_Eav_Model_Entity_Attribute $attribute */
            $attribute = $fixedAttribute->getAttribute();

            if (!$attribute || !$attribute->getAttributeId()) {
                continue;
            }

            $code  = $attribute->getAttributeCode();
            $value = $this->productAttributeRawValue($product, $code);

            $variation->addSpecification($code, $value);
        }

        return $this;
    }


    /**
     * @param Mage_Catalog_Model_Product $product
     *
     * @return float
     */
    protected function getStockQty(Mage_Catalog_Model_Product $product)
    {
        /** @var Mage_CatalogInventory_Model_Stock_Item $stockItem */
        $stockItem = Mage::getModel('cataloginventory/stock_item');
        $stockItem->loadByProduct($product);

        return (float) $stockItem->getQty();
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
        $configurableAttributes = $typeInstance->getConfigurableAttributes($product);

        /** @var Mage_Catalog_Model_Product_Type_Configurable_Attribute $configurableAttribute */
        foreach ($configurableAttributes as $configurableAttribute) {
            /** @var Mage_Catalog_Model_Resource_Eav_Attribute $attribute */
            $attribute = $configurableAttribute->getProductAttribute();

            if (!$attribute || !$attribute->getAttributeId()) {
                continue;
            }

            $this->configurableAttributes[] = $attribute;

            $interface->addVariationAttribute($attribute->getAttributeCode());
        }

        return $this;
    }

}
