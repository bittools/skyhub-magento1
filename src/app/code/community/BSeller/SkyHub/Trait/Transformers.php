<?php

trait BSeller_SkyHub_Trait_Transformers
{

    /**
     * @return BSeller_SkyHub_Model_Transformer_Catalog_Product_Attribute
     */
    public function productAttributeTransformer()
    {
        /** @var BSeller_SkyHub_Model_Transformer_Catalog_Product_Attribute $transformer */
        $transformer = Mage::getSingleton('bseller_skyhub/transformer_catalog_product_attribute');
        return $transformer;
    }

    /**
     * @return BSeller_SkyHub_Model_Transformer_Catalog_Product
     */
    public function productTransformer()
    {
        /** @var BSeller_SkyHub_Model_Transformer_Catalog_Product $transformer */
        $transformer = Mage::getSingleton('bseller_skyhub/transformer_catalog_product');
        return $transformer;
    }

    /**
     * @return BSeller_SkyHub_Model_Transformer_Catalog_Category
     */
    public function categoryTransformer()
    {
        /** @var BSeller_SkyHub_Model_Transformer_Catalog_Category $transformer */
        $transformer = Mage::getSingleton('bseller_skyhub/transformer_catalog_category');
        return $transformer;
    }
}
