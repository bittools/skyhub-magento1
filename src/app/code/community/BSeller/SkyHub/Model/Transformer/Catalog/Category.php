<?php

class BSeller_SkyHub_Model_Transformer_Catalog_Category extends BSeller_SkyHub_Model_Transformer_Abstract
{

    use BSeller_SkyHub_Trait_Service,
        BSeller_SkyHub_Trait_Catalog_Category;


    /**
     * @param Mage_Catalog_Model_Category $category
     *
     * @return \SkyHub\Api\EntityInterface\Catalog\Category
     */
    public function convert(Mage_Catalog_Model_Category $category)
    {
        /** @var \SkyHub\Api\EntityInterface\Catalog\Category $interface */
        $interface = $this->api()->category()->entityInterface();
        $interface->setCode($category->getId())
            ->setName($this->extractProductCategoryPathString($category));

        return $interface;
    }

}
