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

abstract class BSeller_SkyHub_Model_Cron_Config_Abstract implements BSeller_SkyHub_Model_Cron_Config_Interface
{

    use BSeller_SkyHub_Trait_Config;


    /** @var string */
    protected $group = '';

    /** @var string */
    protected $enabledField = 'enabled';


    /**
     * @return bool
     */
    public function isEnabled()
    {
        return (bool) $this->getSkyHubModuleConfig($this->enabledField, $this->group);
    }


    /**
     * @param string $field
     *
     * @return mixed
     */
    public function getGroupConfig($field)
    {
        return $this->getSkyHubModuleConfig($field, $this->group);
    }
}