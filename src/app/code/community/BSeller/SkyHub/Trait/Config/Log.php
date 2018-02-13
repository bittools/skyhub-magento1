<?php

trait BSeller_SkyHub_Trait_Config_Log
{

    /**
     * @param string $field
     *
     * @return string|integer
     */
    protected function getLogConfig($field)
    {
        return $this->getSkyHubModuleConfig($field, 'log');
    }


    /**
     * @return boolean
     */
    protected function isLogEnabled()
    {
        return (bool) $this->getLogConfig('enabled');
    }


    /**
     * @return string
     */
    protected function getLogFilename()
    {
        return (string) $this->getLogConfig('filename');
    }
}
