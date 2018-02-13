<?php

class BSeller_SkyHub_Model_System_Config_Source_Queue_Status extends BSeller_Core_Model_System_Config_Source_Abstract
{

    /**
     * @return array
     */
    protected function optionsKeyValue()
    {
        return [
            BSeller_SkyHub_Model_Queue::STATUS_PENDING => $this->__('Pending'),
            BSeller_SkyHub_Model_Queue::STATUS_FAIL    => $this->__('Fail'),
            BSeller_SkyHub_Model_Queue::STATUS_RETRY   => $this->__('Retry'),
        ];
    }

}
