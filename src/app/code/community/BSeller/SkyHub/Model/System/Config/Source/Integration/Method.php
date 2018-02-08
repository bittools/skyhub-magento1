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
class BSeller_SkyHub_Model_System_Config_Source_Integration_Method
    extends BSeller_Core_Model_System_Config_Source_Abstract
{
    
    const INTEGRATION_METHOD_QUEUE   = 1;
    const INTEGRATION_METHOD_ON_SAVE = 2;
    const INTEGRATION_METHOD_BOTH    = 3;
    
    
    /**
     * @return array
     */
    protected function optionsKeyValue()
    {
        return [
            self::INTEGRATION_METHOD_QUEUE   => $this->__('Only With Queues'),
            self::INTEGRATION_METHOD_ON_SAVE => $this->__('On Entity Save'),
            self::INTEGRATION_METHOD_BOTH    => $this->__('Anyone'),
        ];
    }
    
}