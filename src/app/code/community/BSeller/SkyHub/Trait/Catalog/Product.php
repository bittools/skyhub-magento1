<?php

trait BSeller_SkyHub_Trait_Catalog_Product
{

    /**
     * @param Mage_Catalog_Model_Product $product
     * @param                            $attribute
     * @return array|bool|mixed|string
     */
    protected function productAttributeRawValue(Mage_Catalog_Model_Product $product, $attribute)
    {
        if ($attribute instanceof Mage_Eav_Model_Entity_Attribute) {
            $attribute = $attribute->getAttributeCode();
        }

        if ($product->hasData($attribute)) {
            $data = $product->getData($attribute);
        }

        if (empty($data)) {
            try {
                $data = $product->getResource()
                    ->getAttributeRawValue($product->getId(), $attribute, Mage::app()->getStore());
                return $data;
            } catch (Exception $e) {}
        }

        return $data;
    }

}
