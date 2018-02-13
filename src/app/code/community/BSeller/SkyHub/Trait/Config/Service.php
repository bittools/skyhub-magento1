<?php

trait BSeller_SkyHub_Trait_Config_Service
{

    /**
     * @param string $field
     *
     * @return string|integer
     */
    protected function getServiceConfig($field)
    {
        return $this->getSkyHubModuleConfig($field, 'service');
    }


    /**
     * @return string
     */
    protected function getServiceBaseUri()
    {
        return (string) $this->getServiceConfig('base_uri');
    }


    /**
     * @return string
     */
    protected function getServiceEmail()
    {
        return (string) $this->getServiceConfig('email');
    }


    /**
     * @return string
     */
    protected function getServiceApiKey()
    {
        return (string) $this->getServiceConfig('api_key');
    }


    /**
     * @return string
     */
    protected function getServiceApiToken()
    {
        return (string) $this->getServiceConfig('api_token');
    }

}
