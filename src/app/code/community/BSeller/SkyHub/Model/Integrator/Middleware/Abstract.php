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
abstract class BSeller_SkyHub_Model_Integrator_Middleware_Abstract
    implements BSeller_SkyHub_Model_Integrator_Middleware_Interface
{
    
    use BSeller_SkyHub_Trait_Integrators,
        BSeller_SkyHub_Trait_Config,
        BSeller_SkyHub_Trait_Queue;


    /**
     * @return BSeller_SkyHub_Model_Integrator_Abstract
     */
    abstract protected function integrator();


    /**
     * @param string $method
     *
     * @return bool
     */
    protected function canIntegrate($method)
    {
        switch ($method) {
            case BSeller_SkyHub_Model_System_Config_Source_Integration_Method::INTEGRATION_METHOD_QUEUE:
                return false;
            case BSeller_SkyHub_Model_System_Config_Source_Integration_Method::INTEGRATION_METHOD_BOTH:
            case BSeller_SkyHub_Model_System_Config_Source_Integration_Method::INTEGRATION_METHOD_ON_SAVE:
            default:
                return true;
        }
    }
}
