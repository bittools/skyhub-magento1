<?php

trait BSeller_SkyHub_Model_Integrator_Catalog_Category_Validation
{

    /**
     * @param Mage_Catalog_Model_Category $category
     *
     * @return bool
     */
    protected function canIntegrateCategory(Mage_Catalog_Model_Category $category)
    {
        if (!$category->getId()) {
            return false;
        }

        return true;
    }

}
