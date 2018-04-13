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
 */

use SkyHub\Api\Handler\Response\HandlerException;
use SkyHub\Api\Handler\Response\HandlerDefault;

class BSeller_SkyHub_Model_Cron_Queue_Catalog_Product_Attribute extends BSeller_SkyHub_Model_Cron_Queue_Abstract
{

    use BSeller_SkyHub_Trait_Catalog_Product_Attribute;


    /**
     * @param Mage_Cron_Model_Schedule $schedule
     */
    public function create(Mage_Cron_Model_Schedule $schedule)
    {
        if (!$this->canRun($schedule)) {
            return;
        }

        $integrableIds = (array) array_keys($this->getIntegrableProductAttributes());

        try {
            $this->getQueueResource()->queue(
                $integrableIds,
                BSeller_SkyHub_Model_Entity::TYPE_CATALOG_PRODUCT_ATTRIBUTE,
                BSeller_SkyHub_Model_Queue::PROCESS_TYPE_EXPORT
            );
            $message = $this->__(
                'Queue successfully created. IDs: %s.', implode(',', $integrableIds)
            );
        } catch (Exception $e) {
            $message = $this->__(
                'An has error has occurred when trying to queue the IDs: %s.', implode(',', $integrableIds)
            );
        }

        $schedule->setMessages($message);
    }


    /**
     * @param Mage_Cron_Model_Schedule $schedule
     */
    public function execute(Mage_Cron_Model_Schedule $schedule)
    {
        $this->processStoreIteration($this, 'executeIntegration', $schedule);
    
        $successQueueIds = $this->extractResultSuccessIds($schedule);
        $failedQueueIds  = $this->extractResultFailIds($schedule);
    
        $this->getQueueResource()->removeFromQueue(
            $successQueueIds,
            BSeller_SkyHub_Model_Entity::TYPE_CATALOG_PRODUCT_ATTRIBUTE
        );
        
        $message = $this->__('All product attributes were successfully integrated.');
    
        if (!empty($failedQueueIds)) {
            $message .= " " . $this->__('Some attributes could not be integrated.');
        }
    
        $schedule->setMessages($message);
    }
    
    
    /**
     * @param Mage_Cron_Model_Schedule $schedule
     * @param Mage_Core_Model_Store    $store
     */
    public function executeIntegration(Mage_Cron_Model_Schedule $schedule, Mage_Core_Model_Store $store)
    {
        if (!$this->canRun($schedule, $store->getId())) {
            return;
        }
        
        $attributeIds = (array) $this->getQueueResource()->getPendingEntityIds(
            BSeller_SkyHub_Model_Entity::TYPE_CATALOG_PRODUCT_ATTRIBUTE,
            BSeller_SkyHub_Model_Queue::PROCESS_TYPE_EXPORT
        );
    
        if (empty($attributeIds)) {
            $schedule->setMessages($this->__('No product attribute to process.'));
            return;
        }
    
        $attributes = $this->getProductAttributes($attributeIds);
        
        $successQueueIds = [];
        $failedQueueIds  = [];
    
        /** @var Mage_Eav_Model_Entity_Attribute $attribute */
        foreach ($attributes as $attribute) {
            /** @var HandlerDefault|HandlerException $response */
            $response = $this->catalogProductAttributeIntegrator()->createOrUpdate($attribute);
        
            if ($response && $this->isErrorResponse($response)) {
                $failedQueueIds[] = $attribute->getId();
            
                $this->getQueueResource()->setFailedEntityIds(
                    $attribute->getId(),
                    BSeller_SkyHub_Model_Entity::TYPE_CATALOG_PRODUCT_ATTRIBUTE,
                    $response->message()
                );
            
                continue;
            }
        
            $successQueueIds[$attribute->getId()] = $attribute->getId();
        }
        
        $this->mergeResults($schedule, $successQueueIds, $failedQueueIds);
    }


    /**
     * @param Mage_Cron_Model_Schedule $schedule
     * @param int|null                 $storeId
     *
     * @return bool
     */
    protected function canRun(Mage_Cron_Model_Schedule $schedule, $storeId = null)
    {
        if (!$this->getCronConfig()->catalogProductAttribute()->isEnabled()) {
            $schedule->setMessages($this->__('Catalog Product Attribute Cron is Disabled'));
            return false;
        }

        return parent::canRun($schedule, $storeId);
    }
}
