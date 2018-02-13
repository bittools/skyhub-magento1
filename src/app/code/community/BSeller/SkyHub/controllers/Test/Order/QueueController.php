<?php

class BSeller_SkyHub_Test_Order_QueueController extends BSeller_SkyHub_Controller_Front_Action
{

    public function ordersAction()
    {
        /** @var Mage_Cron_Model_Schedule $schedule */
        $schedule = Mage::getModel('cron/schedule');

        /** @var BSeller_SkyHub_Model_Cron_Sales_Order $cron */
        $cron = Mage::getModel('bseller_skyhub/cron_sales_order');
        $cron->importOrders($schedule);
    }

}
