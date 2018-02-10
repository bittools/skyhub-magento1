<?php

trait BSeller_SkyHub_Trait_Queue
{

    /**
     * @param string $method
     *
     * @return bool
     */
    /**
    protected function canQueue($method)
    {
        switch ($method) {
            case BSeller_SkyHub_Model_System_Config_Source_Integration_Method::INTEGRATION_METHOD_QUEUE:
                return true;
            case BSeller_SkyHub_Model_System_Config_Source_Integration_Method::INTEGRATION_METHOD_BOTH:
            case BSeller_SkyHub_Model_System_Config_Source_Integration_Method::INTEGRATION_METHOD_ON_SAVE:
            default:
                return false;
        }
    }
    **/


    /**
     * @param int|array     $entityIds
     * @param string        $entityType
     * @param bool          $canProcess
     * @param null|string   $processAfter
     *
     * @return $this
     */
    protected function queue($entityIds, $entityType, $canProcess = true, $processAfter = null)
    {
        /** @var BSeller_SkyHub_Model_Resource_Queue $resource */
        $resource = Mage::getResourceSingleton('bseller_skyhub/queue');
        $resource->queue($entityIds, $entityType, $canProcess, $processAfter);

        return $this;
    }

}
