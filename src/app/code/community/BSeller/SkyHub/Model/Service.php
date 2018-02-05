<?php

require_once dirname(__DIR__) . '/vendor/autoload.php';

class BSeller_SkyHub_Model_Service
{

    use BSeller_SkyHub_Trait_Config;


    /**
     * @var \SkyHub\Api;
     */
    protected $api;


    public function __construct()
    {
        $this->initApi();
    }


    /**
     * @return \SkyHub\Api
     */
    public function api()
    {
        if (!$this->api) {
            $this->initApi();
        }

        return $this->api;
    }


    /**
     * @return \SkyHub\Api\Service\ServiceAbstract
     */
    public function apiService()
    {
        return $this->api()->service();
    }


    /**
     * @return $this
     */
    public function initApi()
    {
        $this->api = new \SkyHub\Api(
            $this->getServiceBaseUri(),
            $this->getServiceEmail(),
            $this->getServiceApiKey(),
            $this->getServiceApiToken()
        );

        if ($this->isLogEnabled()) {
            $logFileName = $this->getLogFilename();
            $logFilePath = Mage::app()->getConfig()->getOptions()->getLogDir();

            $this->apiService()
                ->setLogAllowed(true)
                ->setLogFileName($logFileName)
                ->setLogFilePath($logFilePath)
            ;
        }

        return $this;
    }

}
