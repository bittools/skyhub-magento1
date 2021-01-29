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
 * Access https://ajuda.skyhub.com.br/hc/pt-br/requests/new for questions and other requests.
 */

interface BSeller_SkyHub_Model_Cron_Config_Interface
{

    /**
     * @return bool
     */
    public function isEnabled();


    /**
     * @param string $field
     * @return mixed
     */
    public function getGroupConfig($field);
}
