<?php
/**
 * BSeller - B2W Companhia Digital
 *
 * DISCLAIMER
 *
 * Do not edit this file if you want to update this module for future new versions.
 *
 * @copyright     Copyright (c) 2017 B2W Companhia Digital. (http://www.bseller.com.br/)
 *
 * @author        Julio Reis <julio.reis@e-smart.com.br>
 */
require '/home/julio/Public/magento-default-docker/shell/abstract.php';

class BSeller_SkyHub_Shell_Abstract extends Mage_Shell_Abstract
{
    public function run()
    {
        $schedule = new Mage_Cron_Model_Schedule();
        Mage::getModel('bseller_skyhub/cron_queue_catalog_product')->create($schedule);
    }

}

(new BSeller_SkyHub_Shell_Abstract())->run();