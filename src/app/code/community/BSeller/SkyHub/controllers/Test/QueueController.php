<?php

class BSeller_SkyHub_Test_QueueController extends BSeller_SkyHub_Controller_Front_Action
{

    use BSeller_SkyHub_Trait_Integrators;


    public function productQueueCreateAction()
    {
        /** @var BSeller_SkyHub_Model_Resource_Queue $resource */
        $resource = Mage::getResourceModel('bseller_skyhub/queue');
        $resource->queue($this->product()->getId(), BSeller_SkyHub_Model_Entity::TYPE_CATALOG_PRODUCT, 'create');
    }


    public function productIntegrateCreateAction()
    {
        $this->catalogProductIntegrator()
            ->create($this->product());
    }


    public function productIntegrateUpdateAction()
    {
        $this->catalogProductIntegrator()
            ->update($this->product());
    }


    public function createProductAttributeQueueAction()
    {
        /** @var BSeller_SkyHub_Model_Cron_Catalog_Product_Attribute $cron */
        $cron = Mage::getModel('bseller_skyhub/cron_catalog_product_attribute');
        $cron->createAttributesQueue(new Mage_Cron_Model_Schedule());
    }


    public function processProductAttributeQueueAction()
    {
        /** @var BSeller_SkyHub_Model_Cron_Catalog_Product_Attribute $cron */
        $cron = Mage::getModel('bseller_skyhub/cron_catalog_product_attribute');
        $cron->executeAttributesQueue(new Mage_Cron_Model_Schedule());
    }


    public function queueCategoriesByCronAction()
    {
        /** @var BSeller_SkyHub_Model_Cron_Catalog_Category $cron */
        $cron = Mage::getModel('bseller_skyhub/cron_catalog_category');
        $cron->createCategoriesQueue(new Mage_Cron_Model_Schedule());
    }


    public function queueProductsByCronAction()
    {
        /** @var BSeller_SkyHub_Model_Cron_Catalog_Product $cron */
        $cron = Mage::getModel('bseller_skyhub/cron_catalog_product');
        $cron->createProductsQueue(new Mage_Cron_Model_Schedule());
    }


    public function executeProductsByCronAction()
    {
        /** @var BSeller_SkyHub_Model_Cron_Catalog_Product $cron */
        $cron = Mage::getModel('bseller_skyhub/cron_catalog_product');
        $cron->executeProductsQueue(new Mage_Cron_Model_Schedule());
    }


    /**
     * @return Mage_Catalog_Model_Product
     */
    protected function product()
    {
        /** @var Mage_Catalog_Model_Product $product */
        $product = Mage::getModel('catalog/product')->load(2);
        return $product;
    }
}
