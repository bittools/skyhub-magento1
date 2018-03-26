<?php
/**
 * BSeller Platform | B2W - Companhia Digital
 *
 * Do not edit this file if you want to update this module for future new versions.
 *
 * @category  BSeller
 * @package   BSeller_SkyHub
 *
 * @copyright Copyright (c) 2018 B2W Digital - BSeller Platform. (http://www.bseller.com.br)
 *
 * @author    Tiago Sampaio <tiago.sampaio@e-smart.com.br>
 * @author    Bruno Gemelli <bruno.gemelli@e-smart.com.br>
 */

class BSeller_SkyHub_Model_Cron_Queue_Catalog_Product extends BSeller_SkyHub_Model_Cron_Queue_Abstract
{
    use BSeller_SkyHub_Trait_Catalog_Product_Attribute_Notification;

    /**
     * @param Mage_Cron_Model_Schedule $schedule
     */
    public function create(Mage_Cron_Model_Schedule $schedule)
    {
        if (!$this->canRun($schedule)) {
            return;
        }

        $queuedIds = (array) $this->getQueueResource()->getPendingEntityIds(
            BSeller_SkyHub_Model_Entity::TYPE_CATALOG_PRODUCT,
            BSeller_SkyHub_Model_Queue::PROCESS_TYPE_EXPORT
        );

        $queuedIds          = $this->filterIds($queuedIds);
        $skyhubEntityTable  = Mage::getSingleton('core/resource')->getTableName('bseller_skyhub/entity_id');

        /** @var Mage_Catalog_Model_Resource_Product_Collection $collection */
        $collection = $this->getProductCollection()
            ->addAttributeToFilter('visibility', [
                'neq' => Mage_Catalog_Model_Product_Visibility::VISIBILITY_NOT_VISIBLE
            ]);

        if (!empty($queuedIds)) {
            $collection->addFieldToFilter('entity_id', ['nin' => $queuedIds]);
        }

        /** @var Varien_Db_Select $select */
        $select = $collection->getSelect()
            ->joinLeft(
                array('bseller_skyhub_entity' => $skyhubEntityTable),
                'bseller_skyhub_entity.entity_id = e.entity_id 
                      AND bseller_skyhub_entity.entity_type = \''.BSeller_SkyHub_Model_Entity::TYPE_CATALOG_PRODUCT.'\''
            )
            ->reset('columns')
            ->columns('e.entity_id')
            ->where('bseller_skyhub_entity.updated_at IS NULL OR e.updated_at >= bseller_skyhub_entity.updated_at')
            ->order(array('e.updated_at DESC', 'e.created_at DESC'))
        ;
    
        /** Set limitation. */
        $limit = abs($this->getCronConfig()->catalogProduct()->getQueueCreateLimit());
        
        if ($limit) {
            $select->limit((int) $limit);
        }

        $productIds = (array) $this->getQueueResource()->getReadConnection()->fetchCol($select);

        if (empty($productIds)) {
            $schedule->setMessages($this->__('No products to be queued this time.'));
            return;
        }

        $this->getQueueResource()
            ->queue(
                $productIds,
                BSeller_SkyHub_Model_Entity::TYPE_CATALOG_PRODUCT,
                BSeller_SkyHub_Model_Queue::PROCESS_TYPE_EXPORT
            );

        $schedule->setMessages(
            $this->__('%s product(s) were queued. IDs: %s.', count($productIds), implode(',', $productIds))
        );
    }


    /**
     * @param Mage_Cron_Model_Schedule $schedule
     */
    public function execute(Mage_Cron_Model_Schedule $schedule)
    {
        if (!$this->canRun($schedule)) {
            return;
        }

        $productIds = (array) $this->getQueueResource()->getPendingEntityIds(
            BSeller_SkyHub_Model_Entity::TYPE_CATALOG_PRODUCT,
            BSeller_SkyHub_Model_Queue::PROCESS_TYPE_EXPORT
        );

        $productIds = $this->filterIds($productIds);

        /** @var Mage_Catalog_Model_Resource_Product_Collection $collection */
        $collection = $this->getProductCollection()
            ->addFieldToFilter('entity_id', ['in' => $productIds]);

        /** Set limitation. */
        $limit = abs($this->getCronConfig()->catalogProduct()->getQueueExecuteLimit());
        
        if ($limit) {
            $collection->getSelect()->limit((int) $limit);
        }

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

            if ($this->isErrorResponse($response)) {
                $errorIds[] = $product->getId();

                /** @var \SkyHub\Api\Handler\Response\HandlerException $response */
                $this->getQueueResource()->setFailedEntityIds(
                    $product->getId(),
                    BSeller_SkyHub_Model_Entity::TYPE_CATALOG_PRODUCT,
                    $response->message()
                );
                continue;
            }

            $successIds[] = $product->getId();
        }

        if (!empty($successIds)) {
            $this->getQueueResource()
                ->removeFromQueue($successIds, BSeller_SkyHub_Model_Entity::TYPE_CATALOG_PRODUCT);
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


    /**
     * @param Mage_Cron_Model_Schedule $schedule
     *
     * @return bool
     */
    protected function canRun(Mage_Cron_Model_Schedule $schedule)
    {
        if (!$this->getCronConfig()->catalogProduct()->isEnabled()) {
            $schedule->setMessages($this->__('Catalog Product Cron is Disabled'));
            return false;
        }

        //if the notification block can be showed, it means there's a products attributes mapping problem;
        if ($this->canShowAttributesNotificiationBlock()) {
            $schedule->setMessages($this->__('The installation is not completed. All required product attributes must be mapped.'));
            return false;
        }

        return parent::canRun($schedule);
    }
}
