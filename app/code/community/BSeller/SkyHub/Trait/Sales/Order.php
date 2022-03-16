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

trait BSeller_SkyHub_Trait_Sales_Order
{
    /**
     * @return Mage_Sales_Model_Resource_Order_Collection
     */
    public function getPendingOrdersFromSkyHub()
    {
        $deniedStates = array(
            Mage_Sales_Model_Order::STATE_CANCELED,
            Mage_Sales_Model_Order::STATE_CLOSED,
            Mage_Sales_Model_Order::STATE_COMPLETE,
        );

        /** @var Mage_Sales_Model_Resource_Order_Collection $collection */
        $collection = Mage::getResourceModel('sales/order_collection');

        $collection ->addFieldToFilter('state', array('nin' => $deniedStates))
            ->addFieldToFilter('bseller_skyhub', 1)
            ->addFieldToFilter('store_id', Mage::app()->getStore()->getId());

        return $collection;
    }


    /**
     * @param string $skyhubCode
     *
     * @return string
     */
    protected function getOrderId($skyhubCode)
    {
        /** @var BSeller_SkyHub_Model_Resource_Sales_Order $orderResource */
        $orderResource = Mage::getResourceModel('bseller_skyhub/sales_order');

        /*
         * try to get the original skyhub ID by "Bizz Commerce" module column
         */
        $orderId = $orderResource->getEntityIdByBizzCommerceSkyhubCode($skyhubCode);

        if (!$orderId) {
            $orderId = $orderResource->getEntityIdBySkyhubCode($skyhubCode);
        }

        return $orderId;
    }


    /**
     * @param $code
     *
     * @return int
     */
    protected function getNewOrderIncrementId($code)
    {
        $useDefaultIncrementId = $this->getSkyHubModuleConfig('use_default_increment_id', 'cron_sales_order_queue');
        if (!$useDefaultIncrementId) {
            return $code;
        }
        return null;
    }


    /**
     * @param int $orderId (entity_id)
     *
     * @return string
     */
    protected function getOrderIncrementId($orderId)
    {
        /** @var BSeller_SkyHub_Model_Resource_Sales_Order $orderResource */
        $orderResource = Mage::getResourceModel('bseller_skyhub/sales_order');
        $skyhubCode = $orderResource->getBizzCommerceSkyhubCodeByOrderId($orderId);

        if (!$skyhubCode) {
            $skyhubCode = $orderResource->getSkyhubCodeByOrderId($orderId);
        }

        return $skyhubCode;
    }
}