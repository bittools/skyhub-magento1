<?php

class BSeller_SkyHub_Model_Cron_Catalog_Product_Attribute extends BSeller_SkyHub_Model_Cron_Abstract
{

    use BSeller_SkyHub_Trait_Catalog_Product_Attribute;


    /**
     * @param Mage_Cron_Model_Schedule $schedule
     */
    public function createAttributesQueue(Mage_Cron_Model_Schedule $schedule)
    {
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

}
