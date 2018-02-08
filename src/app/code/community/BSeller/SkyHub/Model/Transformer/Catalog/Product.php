<?php

use SkyHub\Api\EntityInterface\Catalog\Product;

class BSeller_SkyHub_Model_Transformer_Catalog_Product extends BSeller_SkyHub_Model_Transformer_TransformerAbstract
{

    use BSeller_SkyHub_Trait_Service,
        BSeller_SkyHub_Trait_Catalog_Product_Attribute;
    
    
    /**
     * @param Mage_Catalog_Model_Product $product
     *
     * @return Product
     *
     * @throws Mage_Core_Exception
     */
    public function convert(Mage_Catalog_Model_Product $product)
    {
        $this->initProductAttributes($product);
        
        /** @var Product $interface */
        $interface = $this->api()->product()->entityInterface();
        $this->prepareMappedAttributes($product, $interface);
        $this->prepareSpecificationAttributes($product, $interface);
        
        $this->prepareProductImages($product, $interface);

        return $interface;
    }
    
    
    /**
     * @param Mage_Catalog_Model_Product $product
     * @param Product                    $interface
     *
     * @return $this
     */
    public function prepareProductImages(Mage_Catalog_Model_Product $product, Product $interface)
    {
        /** @var Varien_Object $image */
        foreach ($product->getMediaGalleryImages() as $image) {
            $url = $image->getData('url');
            $interface->addImage($url);
        }
        
        return $this;
    }
    
    
    /**
     * @param Mage_Catalog_Model_Product $product
     * @param Product                    $interface
     *
     * @return $this
     *
     * @throws Mage_Core_Exception
     */
    public function prepareSpecificationAttributes(Mage_Catalog_Model_Product $product, Product $interface)
    {
        /**
         * Let's get the processed attributes to exclude'em from the specification list.
         */
        $processedAttributeIds = (array) $product->getData('processed_attributes');
        $remainingAttributes   = (array) $this->getProductAttributes($product, [], array_keys($processedAttributeIds));
    
        /** @var Mage_Eav_Model_Entity_Attribute $specificationAttribute */
        foreach ($remainingAttributes as $attribute) {
            /**
             * If the specification attribute is not valid then skip.
             *
             * @var Mage_Eav_Model_Entity_Attribute $attribute
             */
            if (!$attribute || !$this->validateSpecificationAttribute($attribute)) {
                continue;
            }
            
            $value = $this->extractProductData($product, $attribute);
        
            if (empty($value)) {
                continue;
            }
        
            $interface->addSpecification($attribute->getFrontend()->getLabel(), $value);
        }
        
        return $this;
    }
    
    
    /**
     * @param Mage_Eav_Model_Entity_Attribute $attribute
     *
     * @return bool
     */
    public function validateSpecificationAttribute(Mage_Eav_Model_Entity_Attribute $attribute)
    {
        if ($this->isAttributeCodeInBlacklist($attribute->getAttributeCode())) {
            return false;
        }
        
        return true;
    }
    
    
    /**
     * @param Mage_Catalog_Model_Product $product
     * @param Product                    $interface
     *
     * @return $this
     *
     * @throws Mage_Core_Exception
     */
    public function prepareMappedAttributes(Mage_Catalog_Model_Product $product, Product $interface)
    {
        /** @var BSeller_SkyHub_Model_Resource_Catalog_Product_Attributes_Mapping_Collection $mappedAttributes */
        $mappedAttributes    = $this->getMappedAttributesCollection();
        $processedAttributes = [];
    
        /** @var BSeller_SkyHub_Model_Catalog_Product_Attributes_Mapping $mappedAttribute */
        foreach ($mappedAttributes as $mappedAttribute) {
            /** @var string $code */
            $code   = (string) $mappedAttribute->getSkyhubCode();
            $method = 'set'.preg_replace('/[^a-zA-Z]/', null, uc_words($code));
        
            if (!method_exists($interface, $method)) {
                continue;
            }
            
            switch ($code) {
                case 'qty':
                    /** @var Mage_CatalogInventory_Model_Stock_Item $stockItem */
                    $stockItem = Mage::getModel('cataloginventory/stock_item');
                    $stockItem->loadByProduct($product);
                    
                    $value = (float) $stockItem->getQty();
                    
                    break;
                default:
                    /** @var Mage_Eav_Model_Entity_Attribute|bool $attribute */
                    if (!$attribute = $this->getAttributeById($product, $mappedAttribute->getAttributeId())) {
                        $attribute = Mage::getModel('eav/entity_attribute')->load($mappedAttribute->getAttributeId());
                    }
                    
                    if (!$attribute) {
                        continue;
                    }
                    
                    $value = $this->getProductAttributeValue($product, $attribute, $mappedAttribute->getType());
            }
    
            $processedAttributes[$attribute->getId()] = $attribute;
        
            call_user_func([$interface, $method], $value);
        }
        
        $product->setData('processed_attributes', $processedAttributes);
        
        return $this;
    }
    
    
    /**
     * @param Mage_Catalog_Model_Product      $product
     * @param Mage_Eav_Model_Entity_Attribute $attribute
     * @param null|string                     $type
     *
     * @return array|bool|float|int|mixed|string
     * @throws Mage_Core_Exception
     */
    public function getProductAttributeValue(
        Mage_Catalog_Model_Product $product,
        Mage_Eav_Model_Entity_Attribute $attribute,
        $type = null
    )
    {
        if (!$attribute) {
            return false;
        }
    
        $value = $this->extractProductData($product, $attribute);
        $value = $this->castValue($value, $type);
        
        return $value;
    }
    
    
    /**
     * @return BSeller_SkyHub_Model_Resource_Catalog_Product_Attributes_Mapping_Collection
     */
    public function getMappedAttributesCollection()
    {
        /** @var BSeller_SkyHub_Model_Resource_Catalog_Product_Attributes_Mapping_Collection $collection */
        $collection = Mage::getResourceModel('bseller_skyhub/catalog_product_attributes_mapping_collection');
        $collection->setMappedAttributesFilter();
        
        return $collection;
    }
    
    
    /**
     * @param Mage_Catalog_Model_Product      $product
     * @param Mage_Eav_Model_Entity_Attribute $attribute
     *
     * @return array|bool|mixed|string
     *
     * @throws Mage_Core_Exception
     */
    public function extractProductData(
        Mage_Catalog_Model_Product $product,
        Mage_Eav_Model_Entity_Attribute $attribute
    )
    {
        $data = null;
        
        if ($product->hasData($attribute->getAttributeCode())) {
            $data = $product->getData($attribute->getAttributeCode());
        }
        
        if (empty($data)) {
            try {
                $data = $product->getResource()
                                ->getAttributeRawValue(
                                    $product->getId(),
                                    $attribute->getAttributeCode(),
                                    $this->getStore()
                                );
                return $data;
            } catch (Exception $e) {}
        }
        
        switch ($attribute->getAttributeCode()) {
            case 'status':
                if ($data == Mage_Catalog_Model_Product_Status::STATUS_ENABLED) {
                    return true;
                }
                
                if ($data == Mage_Catalog_Model_Product_Status::STATUS_DISABLED) {
                    return false;
                }
                
                break;
        }
    
        /**
         * Attribute is from type select.
         */
        if (in_array($attribute->getFrontend()->getInputType(), ['select', 'multiselect'])) {
            $data = $attribute->getSource()->getOptionText($data);
        }
        
        if (!is_null($data)) {
            return $data;
        }
        
        return false;
    }
    
    
    /**
     * @param string $value
     * @param string $type
     *
     * @return bool|float|int|string
     */
    protected function castValue($value, $type)
    {
        switch ($type) {
            case BSeller_SkyHub_Model_Catalog_Product_Attributes_Mapping::DATA_TYPE_INTEGER:
                return (int) $value;
                break;
            case BSeller_SkyHub_Model_Catalog_Product_Attributes_Mapping::DATA_TYPE_DECIMAL:
                return (float) $value;
                break;
            case BSeller_SkyHub_Model_Catalog_Product_Attributes_Mapping::DATA_TYPE_BOOLEAN:
                return (bool) $value;
                break;
            case BSeller_SkyHub_Model_Catalog_Product_Attributes_Mapping::DATA_TYPE_STRING:
            default:
                return (string) $value;
        }
    }
    
    
    /**
     * @return Mage_Core_Model_Store
     *
     * @throws Mage_Core_Model_Store_Exception
     */
    protected function getStore()
    {
        return Mage::app()->getStore();
    }
}
