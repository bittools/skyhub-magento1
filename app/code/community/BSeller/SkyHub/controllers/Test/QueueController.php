<?php

class BSeller_SkyHub_Test_QueueController extends BSeller_SkyHub_Controller_Front_Action
{

    use BSeller_SkyHub_Trait_Integrators;


    public function productQueueCreateAction()
    {
        /** @var BSeller_SkyHub_Model_Resource_Queue $resource */
        $resource = Mage::getResourceModel('bseller_skyhub/queue');
        $resource->queue(
            $this->product()->getId(),
            BSeller_SkyHub_Model_Entity::TYPE_CATALOG_PRODUCT,
            BSeller_SkyHub_Model_Queue::PROCESS_TYPE_EXPORT,
            'create'
        );
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
        /** @var BSeller_SkyHub_Model_Cron_Queue_Catalog_Product_Attribute $cron */
        $cron = Mage::getModel('bseller_skyhub/cron_queue_catalog_product_attribute');
        $cron->create(new Mage_Cron_Model_Schedule());
    }


    public function processProductAttributeQueueAction()
    {
        /** @var BSeller_SkyHub_Model_Cron_Catalog_Product_Attribute $cron */
        $cron = Mage::getModel('bseller_skyhub/cron_queue_catalog_product_attribute');
        $cron->execute(new Mage_Cron_Model_Schedule());
    }


    public function queueCategoriesByCronAction()
    {
        /** @var BSeller_SkyHub_Model_Cron_Queue_Catalog_Category $cron */
        $cron = Mage::getModel('bseller_skyhub/cron_queue_catalog_category');
        $cron->create(new Mage_Cron_Model_Schedule());
    }


    public function queueProductsByCronAction()
    {
        /** @var BSeller_SkyHub_Model_Cron_Queue_Catalog_Product $cron */
        $cron = Mage::getModel('bseller_skyhub/cron_queue_catalog_product');
        $cron->create(new Mage_Cron_Model_Schedule());
    }


    public function executeProductsByCronAction()
    {
        /** @var BSeller_SkyHub_Model_Cron_Queue_Catalog_Product $cron */
        $cron = Mage::getModel('bseller_skyhub/cron_queue_catalog_product');
        $cron->execute(new Mage_Cron_Model_Schedule());
    }


    /**
     * @return Mage_Catalog_Model_Product
     */
    protected function product()
    {
        /** @var Mage_Catalog_Model_Product $product */
        $product = Mage::getModel('bseller_skyhub/catalog_product')->load(2);
        return $product;
    }
}
