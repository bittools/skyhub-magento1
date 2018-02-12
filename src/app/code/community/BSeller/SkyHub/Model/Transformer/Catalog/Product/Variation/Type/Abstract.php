<?php

use SkyHub\Api\EntityInterface\Catalog\Product;

abstract class BSeller_SkyHub_Model_Transformer_Catalog_Product_Variation_Type_Abstract
    extends BSeller_SkyHub_Model_Transformer_Abstract
    implements BSeller_SkyHub_Model_Transformer_Catalog_Product_Variation_Type_Interface
{


    /** @var BSeller_SkyHub_Model_Resource_Catalog_Product_Attributes_Mapping_Collection */
    protected $mappedAttributesCollection;

    /** @var array */
    protected $configurableAttributes = [];


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

            if (empty($code) || empty($value)) {
                continue;
            }

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
     * @param Product\Variation          $variation
     *
     * @return $this
     */
    protected function addImagesToVariation(Mage_Catalog_Model_Product $product, Product\Variation $variation)
    {
        if (!$product->getMediaGalleryImages()) {
            /** @var Mage_Eav_Model_Entity_Attribute $attribute */
            $attribute = Mage::getModel('eav/entity_attribute')->loadByCode(
                Mage_Catalog_Model_Product::ENTITY,
                'media_gallery'
            );

            /** @var Mage_Catalog_Model_Product_Attribute_Backend_Media $media */
            Mage::getModel('catalog/product_attribute_backend_media')
                ->setAttribute($attribute)
                ->afterLoad($product);
        }

        /** @var Varien_Data_Collection|null $gallery */
        $gallery = $product->getMediaGalleryImages();

        if (!$gallery || !$gallery->getSize()) {
            return $this;
        }

        /** @var Varien_Object $galleryImage */
        foreach ($product->getMediaGalleryImages() as $galleryImage) {
            $variation->addImage($galleryImage->getData('url'));
        }

        return $this;
    }

}
