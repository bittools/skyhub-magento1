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

class BSeller_SkyHub_Model_System_Config_Source_Queue_Status extends BSeller_Core_Model_System_Config_Source_Abstract
{

    /**
     * @return array
     */
    protected function optionsKeyValue($multiselect = null)
    {
        return array(
            BSeller_SkyHub_Model_Queue::PROCESS_TYPE_IMPORT => $this->__('Import'),
            BSeller_SkyHub_Model_Queue::PROCESS_TYPE_EXPORT => $this->__('Export'),
        );
    }
}
