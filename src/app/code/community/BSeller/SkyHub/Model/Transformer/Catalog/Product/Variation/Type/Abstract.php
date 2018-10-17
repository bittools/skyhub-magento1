<?php

use SkyHub\Api\EntityInterface\Catalog\Product;

abstract class BSeller_SkyHub_Model_Transformer_Catalog_Product_Variation_Type_Abstract
    extends BSeller_SkyHub_Model_Transformer_Abstract
    implements BSeller_SkyHub_Model_Transformer_Catalog_Product_Variation_Type_Interface
{
    
    use BSeller_SkyHub_Trait_Catalog_Product,
        BSeller_SkyHub_Trait_Catalog_Product_Attribute_Mapping;
    
    
    /**
     * @param Mage_Catalog_Model_Product $product
     * @param Product                    $interface
     *
     * @return Product\Variation
     * @throws Mage_Core_Exception
     */
    protected function addVariation(Mage_Catalog_Model_Product $product, Product $interface)
    {
        /** @var Product\Variation $variation */
        $variation = $interface->addVariation($product->getSku(), $this->getStockQty($product));

        /**
         * EAN Attribute
         *
         * @var BSeller_SkyHub_Model_Catalog_Product_Attributes_Mapping $mapping
         */

        $mapping = $this->getMappedAttribute('ean');

        /** @var Mage_Eav_Model_Entity_Attribute $attribute */
        if ($mapping->getId() && $mapping->getAttribute()->getId()) {
            $ean = $mapping->extractProductValue($product);
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

        Mage::dispatchEvent('bseller_skyhub_product_variation_create_after',
            array(
                'product'   => $product,
                'variation' => $variation,
            )
        );

        return $variation;
    }


    /**
     * @param Mage_Catalog_Model_Product $product
     * @param Product\Variation          $variation
     *
     * @return $this
     */
    protected function addSpecificationsToVariation(Mage_Catalog_Model_Product $product, Product\Variation $variation)
    {
        $this->addMappedAttributesToProductVariation($product, $variation);
        $this->addPricesToProductVariation($product, $variation);
        
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
    
        $price = $this->extractProductPrice($product, $attributePrice);
        
        if (!empty($price)) {
            $variation->addSpecification($mappedPrice->getSkyhubCode(), (float) $price);
        }
        
        $specialPrice = $this->extractProductSpecialPrice($product, $attributeSpecialPrice, $price);
        
        if (!empty($specialPrice)) {
            $variation->addSpecification($mappedSpecialPrice->getSkyhubCode(), (float) $specialPrice);
        }
        
        return $this;
    }
    
    
    /**
     * @param Mage_Catalog_Model_Product $product
     * @param Product\Variation          $variation
     *
     * @return $this
     */
    protected function addMappedAttributesToProductVariation(
        Mage_Catalog_Model_Product $product,
        Product\Variation $variation
    )
    {
        /** @var BSeller_SkyHub_Model_Catalog_Product_Attributes_Mapping $mappedAttribute */
        foreach ($this->getFixedMappedAttributes() as $mappedAttribute) {
            $value = $mappedAttribute->extractProductValue($product);
            $code  = $mappedAttribute->getSkyhubCode();
        
            if (empty($code) || empty($value)) {
                Mage::log('empty code = '.$code);
                continue;
            }
        
            $variation->addSpecification($code, $value);
        }
        
        return $this;
    }
    
    
    /**
     * @return array
     */
    protected function getFixedMappedAttributes()
    {
        return array(
            'weight' => $this->getMappedAttribute('weight'),
            'height' => $this->getMappedAttribute('height'),
            'length' => $this->getMappedAttribute('length'),
            'width'  => $this->getMappedAttribute('width'),
        );
    }


    /**
     * @param Mage_Catalog_Model_Product $product
     *
     * @return float
     */
    protected function getStockQty(Mage_Catalog_Model_Product $product)
    {
        if (!$product->isSalable()) {
            return 0;
        }

        /** @var Mage_CatalogInventory_Model_Stock_Item $stockItem */
        $stockItem = Mage::getModel('cataloginventory/stock_item');
        $stockItem->loadByProduct($product);

        if (!$stockItem->getManageStock()) {
            return 1000;
        }

        if (!$stockItem->getIsInStock()) {
            return 0;
        }

        return (float) $stockItem->getQty();
    }
    
    
    /**
     * @param Mage_Catalog_Model_Product $product
     * @param Product\Variation          $variation
     *
     * @return $this
     *
     * @throws Mage_Core_Exception
     */
    protected function addImagesToVariation(Mage_Catalog_Model_Product $product, Product\Variation $variation)
    {
        if (!$product->getMediaGalleryImages()) {
            /** @var Mage_Eav_Model_Entity_Attribute $attribute */
            $attribute = Mage::getModel('eav/entity_attribute');
            $attribute->loadByCode(
                Mage_Catalog_Model_Product::ENTITY,
                'media_gallery'
            );

            /** @var Mage_Catalog_Model_Product_Attribute_Backend_Media $media */
            $media = Mage::getModel('catalog/product_attribute_backend_media');
            $media->setAttribute($attribute)
                ->afterLoad($product);
        }

        /** @var Varien_Data_Collection|null $gallery */
        $gallery = $product->getMediaGalleryImages();

        if (!$gallery || !$gallery->getSize()) {
            return $this;
        }

        /** @var Varien_Object $galleryImage */
        foreach ($gallery as $galleryImage) {
            $variation->addImage($galleryImage->getData('url'));
        }

        return $this;
    }
}
