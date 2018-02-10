<?php

trait BSeller_SkyHub_Trait_Data
{

    use BSeller_Core_Trait_Data;


    /**
     * @return BSeller_SkyHub_Model_Resource_Entity
     */
    protected function getEntityResource()
    {
        return Mage::getResourceSingleton('bseller_skyhub/entity');
    }


    /**
     * @param int $id
     *
     * @return bool
     */
    protected function productExists($id)
    {
        return (bool) $this->getEntityResource()
            ->entityExists((int) $id, BSeller_SkyHub_Model_Entity::TYPE_CATALOG_PRODUCT);
    }


    /**
     * @param int $id
     *
     * @return bool
     */
    protected function productAttributeExists($id)
    {
        return (bool) $this->getEntityResource()
            ->entityExists((int) $id, BSeller_SkyHub_Model_Entity::TYPE_CATALOG_PRODUCT_ATTRIBUTE);
    }


    /**
     * @param int $id
     *
     * @return bool
     */
    protected function categoryExists($id)
    {
        return (bool) $this->getEntityResource()
            ->entityExists((int) $id, BSeller_SkyHub_Model_Entity::TYPE_CATALOG_CATEGORY);
    }
}
