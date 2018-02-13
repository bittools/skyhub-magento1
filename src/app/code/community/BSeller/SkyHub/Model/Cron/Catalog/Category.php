<?php

class BSeller_SkyHub_Model_Cron_Catalog_Category extends BSeller_SkyHub_Model_Cron_Abstract
{

    /**
     * @param Mage_Cron_Model_Schedule $schedule
     */
    public function createCategoriesQueue(Mage_Cron_Model_Schedule $schedule)
    {
        if (!$this->canRun()) {
            return;
        }

        /** @var Varien_Db_Select $select */
        $select = $this->getCategoryCollection()->getSelect()
            ->reset('columns')
            ->columns('entity_id');

        $categoryIds = (array) Mage::getSingleton('core/resource')->getConnection('read')
            ->fetchCol($select);

        if (empty($categoryIds)) {
            $schedule->setMessages($this->__('No category to be listed right now.'));
            return;
        }

        $this->getQueueResource()->queue($categoryIds, BSeller_SkyHub_Model_Entity::TYPE_CATALOG_CATEGORY);

        $schedule->setMessages(
            $this->__('The categories were successfully queued. Category IDs: %s.', implode(',', $categoryIds))
        );
    }


    /**
     * @param Mage_Cron_Model_Schedule $schedule
     */
    public function executeCategoriesQueue(Mage_Cron_Model_Schedule $schedule)
    {
        if (!$this->canRun()) {
            return;
        }

        $categoryIds = (array) $this->getQueueResource()
            ->getPendingEntityIds(BSeller_SkyHub_Model_Entity::TYPE_CATALOG_CATEGORY);

        if (empty($categoryIds)) {
            $schedule->setMessages($this->__('No category to be integrated right now.'));
            return;
        }

        /** @var Mage_Catalog_Model_Resource_Category_Collection $collection */
        $collection = $this->getCategoryCollection()
            ->addFieldToFilter('entity_id', $categoryIds);

        $successIds = [];
        $errorIds   = [];

        /** @var Mage_Catalog_Model_Category $category */
        foreach ($collection as $category) {
            /** @var bool|\SkyHub\Api\Handler\Response\HandlerInterface $response */
            $response = $this->catalogCategoryIntegrator()->createOrUpdate($category);

            if (!$response || $response->exception()) {
                $errorIds[] = $category->getId();
                continue;
            }

            $successIds[] = $category->getId();
        }

        if (!empty($successIds)) {
            $this->getQueueResource()
                ->removeFromQueue($successIds, BSeller_SkyHub_Model_Entity::TYPE_CATALOG_CATEGORY);
        }

        if (!empty($errorIds)) {
            $this->getQueueResource()
                ->setFailedEntityIds($errorIds, BSeller_SkyHub_Model_Entity::TYPE_CATALOG_CATEGORY);
        }

        $schedule->setMessages($this->__(
            'Queue was processed. Success: %s. Errors: %s.',
            implode(',', $successIds),
            implode(',', $errorIds)
        ));
    }


    /**
     * @return Mage_Catalog_Model_Resource_Category_Collection
     */
    protected function getCategoryCollection()
    {
        /** @var Mage_Catalog_Model_Resource_Category_Collection $collection */
        $collection = Mage::getResourceModel('catalog/category_collection');
        return $collection;
    }


    /**
     * @return bool
     */
    protected function canRun()
    {
        if (!$this->isCronCatalogCategoriesEnabled()) {
            return false;
        }

        return parent::canRun();
    }
}
