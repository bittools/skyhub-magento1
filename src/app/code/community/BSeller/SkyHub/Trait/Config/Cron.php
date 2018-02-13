<?php

trait BSeller_SkyHub_Trait_Config_Cron
{

    /**
     * @param string $field
     *
     * @return string|integer
     */
    protected function getCronConfig($field)
    {
        return $this->getSkyHubModuleConfig($field, 'cron');
    }


    /**
     * @return bool
     */
    protected function isCronCatalogProductAttributesEnabled()
    {
        return (bool) $this->getCronConfig('catalog_product_attributes_queue_enabled');
    }


    /**
     * @return bool
     */
    protected function isCronCatalogProductsEnabled()
    {
        return (bool) $this->getCronConfig('catalog_products_queue_enabled');
    }


    /**
     * @param int $default
     *
     * @return bool
     */
    protected function getCatalogProductQueueExecuteLimit($default = 500)
    {
        $quantity = (int) $this->getCronConfig('catalog_products_queue_execute_limit');

        if (!$quantity) {
            $quantity = $default;
        }

        return (int) $quantity;
    }


    /**
     * @return bool
     */
    protected function isCronCatalogCategoriesEnabled()
    {
        return (bool) $this->getCronConfig('catalog_categories_queue_enabled');
    }

}
