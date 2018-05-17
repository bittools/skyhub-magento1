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

class BSeller_SkyHub_Model_Cron_Config_Sales_Order_Queue extends BSeller_SkyHub_Model_Cron_Config_Abstract
{

    protected $group = 'cron_sales_order_queue';
    
    
    /**
     * @return int
     */
    public function getLimit()
    {
        return (int) $this->getGroupConfig('limit');
    }

}
