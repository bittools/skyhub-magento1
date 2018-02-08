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
     */
    public function convert(Mage_Catalog_Model_Product $product)
    {
        $this->initAttributeCollection();
        
        /** @var Product $interface */
        $interface = $this->api()->product()->entityInterface();
        $this->prepareMappedAttributes($product, $interface);
        $this->prepareSpecificationAttributes($product, $interface);
        
        /*
        $interface->setSku($product->getSku())
            ->setName($product->getName())
            ->setDescription($product->getDescription())
            ->setBrand('')
            ->setCost((float) $product->getCost())
            ->setPrice((float) $product->getPrice())
            ->setPromotionalPrice((float) $product->getFinalPrice())
            ->setWeight((float) $product->getWeight())
            ->setWidth((float) $product->getWeight())
            ->setHeight(1)
            ->setLength(1)
            ->setStatus((bool) $product->getStatus())
        ;
        */
        
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
     * @throws Mage_Core_Model_Store_Exception
     */
    protected function prepareSpecificationAttributes(Mage_Catalog_Model_Product $product, Product $interface)
    {
        $processedAttributeIds = (array) $product->getData('processed_attributes');
    
        /** @var Mage_Eav_Model_Entity_Attribute $specificationAttribute */
        foreach ($product->getAttributes() as $specificationAttribute) {
            if (isset($processedAttributeIds[$specificationAttribute->getAttributeId()])) {
                continue;
            }
        
            if (!$specificationAttribute || !$this->validateSpecificationAttribute($specificationAttribute)) {
                continue;
            }
            
            $specificationValue = $this->extractProductData($product, $specificationAttribute);
        
            if (empty($specificationValue)) {
                continue;
            }
        
            $interface->addSpecification($specificationAttribute->getAttributeCode(), $specificationValue);
        }
        
        return $this;
    }
    
    
    /**
     * @param Mage_Eav_Model_Entity_Attribute $attribute
     *
     * @return bool
     */
    protected function validateSpecificationAttribute(Mage_Eav_Model_Entity_Attribute $attribute)
    {
        if ($this->isAttributeCodeInBlacklist($attribute->getAttributeCode())) {
            return false;
        }
        
        return true;
    }
    
    
    /**
     * @param Product $interface
     *
     * @return $this
     * @throws Mage_Core_Model_Store_Exception
     */
    protected function prepareMappedAttributes(Mage_Catalog_Model_Product $product, Product $interface)
    {
        /** @var BSeller_SkyHub_Model_Resource_Catalog_Product_Attributes_Mapping_Collection $mappedAttributes */
        $mappedAttributes    = $this->getMappedAttributesCollection();
        $processedAttributes = [];
    
        /** @var BSeller_SkyHub_Model_Catalog_Product_Attributes_Mapping $mappedAttribute */
        foreach ($mappedAttributes as $mappedAttribute) {
            /** @var string $code */
            $code   = (string) $mappedAttribute->getSkyhubCode();
            $method = 'set'.uc_words($code);
        
            if (!method_exists($interface, $method)) {
                continue;
            }
        
            /** @var Mage_Eav_Model_Entity_Attribute|bool $attribute */
            $attribute = $this->getAttributeById($mappedAttribute->getAttributeId());
        
            if (!$attribute) {
                continue;
            }
            
            $value = $this->extractProductData($product, $attribute);
            $value = $this->castValue($value, $mappedAttribute->getType());
    
            $processedAttributes[$attribute->getId()] = $attribute;
        
            call_user_func([$interface, $method], $value);
        }
        
        $product->setData('processed_attributes', $processedAttributes);
        
        return $this;
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
     * @return BSeller_SkyHub_Model_Resource_Catalog_Product_Attributes_Mapping_Collection
     */
    protected function getMappedAttributesCollection()
    {
        /** @var BSeller_SkyHub_Model_Resource_Catalog_Product_Attributes_Mapping_Collection $collection */
        $collection = Mage::getResourceModel('bseller_skyhub/catalog_product_attributes_mapping_collection');
        $collection->setMappedAttributesFilter();
        
        return $collection;
    }
    
    
    /**
     * @param Mage_Catalog_Model_Product $product
     * @param string                     $dataKey
     *
     * @return array|bool|mixed|string
     *
     * @throws Mage_Core_Model_Store_Exception
     */
    protected function extractProductData(Mage_Catalog_Model_Product $product, Mage_Eav_Model_Entity_Attribute $attribute)
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
    
        /**
         * Attribute is from type select.
         */
        if ($attribute->getFrontend()->getInputType() == 'select') {
            $data = $attribute->getSource()->getOptionText($data);
        }
        
        if (!empty($data)) {
            return $data;
        }
        
        return false;
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
