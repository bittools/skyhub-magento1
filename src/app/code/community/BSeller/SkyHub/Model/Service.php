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

require_once dirname(__DIR__) . '/vendor/autoload.php';

use SkyHub\Api;

class BSeller_SkyHub_Model_Service
{

    use BSeller_SkyHub_Trait_Config;


    /** @var Api */
    protected $api;


    public function __construct()
    {
        $this->initApi();
    }


    /**
     * @return Api
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
        $this->api = new Api(
            $this->getServiceEmail(),
            $this->getServiceApiKey(),
            'bZa6Ml0zgS'
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
