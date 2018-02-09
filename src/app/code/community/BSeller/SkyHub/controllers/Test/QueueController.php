<?php

class BSeller_SkyHub_Test_QueueController extends BSeller_SkyHub_Controller_Front_Action
{

    use BSeller_SkyHub_Trait_Integrators;


    public function productQueueCreateAction()
    {
        /** @var BSeller_SkyHub_Model_Resource_Queue $resource */
        $resource = Mage::getResourceModel('bseller_skyhub/queue');
        $resource->queue($this->product()->getId(), BSeller_SkyHub_Model_Queue::ENTITY_TYPE_CATALOG_PRODUCT, 'create');
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
