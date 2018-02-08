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
abstract class BSeller_SkyHub_Model_Observer_Abstract
{
    
    use BSeller_SkyHub_Trait_Data,
        BSeller_SkyHub_Trait_Config,
        BSeller_SkyHub_Trait_Integrators,
        BSeller_SkyHub_Trait_Integration_Middlewares,
        BSeller_SkyHub_Model_Integrator_Catalog_Product_Validation;
    
}