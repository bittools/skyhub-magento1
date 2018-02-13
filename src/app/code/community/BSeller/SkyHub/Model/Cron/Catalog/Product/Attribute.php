<?php

use SkyHub\Api\Handler\Response\HandlerException;
use SkyHub\Api\Handler\Response\HandlerDefault;

class BSeller_SkyHub_Model_Cron_Catalog_Product_Attribute extends BSeller_SkyHub_Model_Cron_Abstract
{

    use BSeller_SkyHub_Trait_Catalog_Product_Attribute;


    /**
     * @param Mage_Cron_Model_Schedule $schedule
     */
    public function createAttributesQueue(Mage_Cron_Model_Schedule $schedule)
    {
        if (!$this->canRun()) {
            return;
        }

        $integrableIds = (array) array_keys($this->getIntegrableProductAttributes());

        try {
            $this->queue($integrableIds, BSeller_SkyHub_Model_Entity::TYPE_CATALOG_PRODUCT_ATTRIBUTE);
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
    public function executeAttributesQueue(Mage_Cron_Model_Schedule $schedule)
    {
        if (!$this->canRun()) {
            return;
        }

        $attributeIds = (array) $this->getQueueResource()
            ->getPendingEntityIds(BSeller_SkyHub_Model_Entity::TYPE_CATALOG_PRODUCT_ATTRIBUTE);

        if (empty($attributeIds)) {
            $schedule->setMessages($this->__('No product attribute to process.'));
        }

        $attributes      = $this->getProductAttributes($attributeIds);
        $successQueueIds = [];
        $failedQueueIds  = [];

        /** @var Mage_Eav_Model_Entity_Attribute $attribute */
        foreach ($attributes as $attribute) {
            /** @var HandlerDefault|HandlerException $response */
            $response = $this->catalogProductAttributeIntegrator()->createOrUpdate($attribute);

            if ($response->exception() || $response->invalid()) {
                $failedQueueIds[] = $attribute->getId();
                continue;
            }

            $successQueueIds[] = $attribute->getId();
        }

        $this->getQueueResource()
            ->removeFromQueue($successQueueIds, BSeller_SkyHub_Model_Entity::TYPE_CATALOG_PRODUCT_ATTRIBUTE)
            ->setFailedEntityIds($failedQueueIds, BSeller_SkyHub_Model_Entity::TYPE_CATALOG_PRODUCT_ATTRIBUTE);

        $schedule->setMessages($this->__('All product attributes were successfully integrated.'));
    }


    /**
     * @return bool
     */
    protected function canRun()
    {
        if (!$this->isCronCatalogProductAttributesEnabled()) {
            return false;
        }

        return parent::canRun();
    }
}
