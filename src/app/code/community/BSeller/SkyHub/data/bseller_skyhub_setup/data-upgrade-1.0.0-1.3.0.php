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
 * @author    Julio Reis <julio.reis@e-smart.com.br>
 */

$this->startSetup();

//**********************************************************************************************************************
// Install bseller_skyhub/customer_attributes_mapping data.
//**********************************************************************************************************************
$this->installSkyHubRequiredAttributes('customer');

$this->endSetup();
