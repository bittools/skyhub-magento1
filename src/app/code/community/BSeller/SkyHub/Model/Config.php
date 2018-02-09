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
class BSeller_SkyHub_Model_Config
{
    
    /** @var Mage_Core_Model_Config_Base */
    protected $config;

    /** @var array */
    protected $attributes = [];


    /**
     * @param string $code
     *
     * @return array
     */
    public function getAttributeInstallConfig($code)
    {
        $this->getSkyHubFixedAttributes();

        if (!isset($this->attributes[$code], $this->attributes[$code]['attribute_install_config'])) {
            return [];
        }

        return (array) $this->attributes[$code]['attribute_install_config'];
    }
    
    
    /**
     * @return array
     */
    public function getSkyHubFixedAttributes()
    {
        if (empty($this->attributes)) {
            $this->attributes = (array) $this->getConfig()->getNode('skyhub/attributes')->asArray();
        }

        return (array) $this->attributes;
    }
    
    
    /**
     * @return Mage_Core_Model_Config_Base
     */
    public function getConfig()
    {
        if (empty($this->config)) {
            $this->config = Mage::app()->getConfig()->loadModulesConfiguration('skyhub.xml');
        }
    
        return $this->config;
    }

}
