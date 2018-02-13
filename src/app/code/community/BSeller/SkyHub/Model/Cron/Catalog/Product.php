<?php

class BSeller_SkyHub_Model_Cron_Catalog_Product extends BSeller_SkyHub_Model_Cron_Abstract
{

    /**
     * @param Mage_Cron_Model_Schedule $schedule
     */
    public function createProductsQueue(Mage_Cron_Model_Schedule $schedule)
    {
        $queuedIds = (array) $this->getQueueResource()
            ->getPendingEntityIds(BSeller_SkyHub_Model_Entity::TYPE_CATALOG_PRODUCT);

        $queuedIds = $this->filterIds($queuedIds);

        /** @var Mage_Catalog_Model_Resource_Product_Collection $collection */
        $collection = $this->getProductCollection()
            ->addAttributeToFilter('visibility', [
                'neq' => Mage_Catalog_Model_Product_Visibility::VISIBILITY_NOT_VISIBLE
            ]);

        if (!empty($queuedIds)) {
            $collection->addFieldToFilter('entity_id', ['nin' => $queuedIds]);
        }

        $limit = 1;

        /** @var Varien_Db_Select $select */
        $select = $collection->getSelect()
            ->limit((int) $limit)
            ->reset('columns')
            ->columns('entity_id')
            ->order('updated_at DESC')
            ->order('created_at DESC')
        ;

        $productIds = (array) $this->getQueueResource()->getReadConnection()->fetchCol($select);

        if (empty($productIds)) {
            $schedule->setMessages($this->__('No products to be queued this time.'));
            return;
        }

        $this->getQueueResource()->queue($productIds, BSeller_SkyHub_Model_Entity::TYPE_CATALOG_PRODUCT);

        $schedule->setMessages(
            $this->__('%s product(s) were queued. IDs: %s.', count($productIds), implode(',', $productIds))
        );
    }


    /**
     * @param Mage_Cron_Model_Schedule $schedule
     */
    public function executeProductsQueue(Mage_Cron_Model_Schedule $schedule)
    {
        $productIds = (array) $this->getQueueResource()
            ->getPendingEntityIds(BSeller_SkyHub_Model_Entity::TYPE_CATALOG_PRODUCT);

        $productIds = $this->filterIds($productIds);

        /** @var Mage_Catalog_Model_Resource_Product_Collection $collection */
        $collection = $this->getProductCollection()
            ->addFieldToFilter('entity_id', ['in' => $productIds]);

        if (!$collection->getSize()) {
            $schedule->setMessages($this->__('No product to be integrated this time.'));
            return;
        }

        $successIds = [];
        $errorIds   = [];

        /** @var Mage_Catalog_Model_Product $product */
        foreach ($collection as $product) {
            /** @var \SkyHub\Api\Handler\Response\HandlerInterface $response */
            $response = $this->catalogProductIntegrator()->createOrUpdate($product);

            if ($response->exception()) {
                $errorIds[] = $product->getId();
                continue;
            }

            $successIds[] = $product->getId();
        }

        if (!empty($successIds)) {
            $this->getQueueResource()->removeFromQueue($successIds, BSeller_SkyHub_Model_Entity::TYPE_CATALOG_PRODUCT);
        }

        if (!empty($errorIds)) {
            $this->getQueueResource()->setFailedEntityIds($errorIds, BSeller_SkyHub_Model_Entity::TYPE_CATALOG_PRODUCT);
        }

        $schedule->setMessages($this->__(
            'Queue was processed. Success: %s. Errors: %s.',
            implode(',', $successIds),
            implode(',', $errorIds)
        ));
    }


    /**
     * @return Mage_Catalog_Model_Resource_Product_Collection
     */
    protected function getProductCollection()
    {
        /** @var Mage_Catalog_Model_Resource_Product_Collection $collection */
        $collection = Mage::getResourceModel('catalog/product_collection');
        return $collection;
    }


    /**
     * @param array $ids
     *
     * @return array
     */
    protected function filterIds(array $ids)
    {
        $ids = array_filter($ids, function (&$value) {
            $value = (int) $value;
            return $value;
        });

        return (array) $ids;
    }
}
