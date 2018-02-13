<?php

class BSeller_SkyHub_Model_Cron_Abstract
{

    use BSeller_SkyHub_Trait_Data,
        BSeller_SkyHub_Trait_Config,
        BSeller_SkyHub_Trait_Queue,
        BSeller_SkyHub_Trait_Integrators;


    /**
     * @return bool
     */
    protected function canRun()
    {
        if (!$this->isModuleEnabled()) {
            return false;
        }

        return true;
    }

}
