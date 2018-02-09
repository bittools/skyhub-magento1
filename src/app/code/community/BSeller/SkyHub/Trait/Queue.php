<?php

trait BSeller_SkyHub_Trait_Queue
{

    /**
     * @param string $method
     *
     * @return bool
     */
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


    /**
     * @param string        $method
     * @param int           $entityId
     * @param string        $entityType
     * @param string        $action
     * @param bool          $canProcess
     * @param null|string   $processAfter
     *
     * @return $this
     */
    protected function queue($method, $entityId, $entityType, $action, $canProcess = true, $processAfter = null)
    {
        if (!$this->canQueue($method)) {
            return $this;
        }

        /** @var BSeller_SkyHub_Model_Resource_Queue $resource */
        $resource = Mage::getResourceSingleton('bseller_skyhub/queue');
        $resource->queue($entityId, $entityType, $action, $canProcess, $processAfter);

        return $this;
    }

}
