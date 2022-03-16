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

class BSeller_SkyHub_Model_Observer_Sales_Order_Shipment extends BSeller_SkyHub_Model_Observer_Sales_Abstract
{

    /**
     * @param Varien_Event_Observer $observer
     */
    public function integrateOrderShipmentTracking(Varien_Event_Observer $observer)
    {
        /** @var Mage_Sales_Model_Order_Shipment_Track $track */
        $track = $observer->getData('track');

        if (!$track || !$track->getId()) {
            return;
        }

        /** @var Mage_Sales_Model_Order $order */
        $order = $this->getOrder($track->getOrderId());

        if (!$order->getBsellerSkyhubChannel() || empty($order->getBsellerSkyhubChannel())) {
            return false;
        }

        $items = array();

        /** @var Mage_Sales_Model_Order_Item $item */
        foreach ($order->getAllVisibleItems() as $item) {
            $items[] = array(
                'sku' => (string) $item->getSku(),
                'qty' => (int)    $item->getQtyOrdered(),
            );
        }

        $shippingMethod = $order->getShippingMethod();

        try {
            $params = array(
                $order->getId(),
                $items,
                $track->getNumber(),
                $track->getTitle(),
                $shippingMethod,     // Track method like SEDEX...
                $this->_getCarriersUrlConfig($track->getCarrierCode())  // Tracking URL (www.correios.com.br)
            );
            
            $this->getStoreIterator()->call($this->orderIntegrator(), 'shipment', $params, $order->getStore());
        } catch (Exception $e) {
            Mage::logException($e);
        }
    }

    /**
     * Return url of tracking
     *
     * @param string $shippingMethod
     * @return string
     */
    protected function _getCarriersUrlConfig($shippingMethod)
    {
        $url = '';
        $config = unserialize(Mage::getStoreConfig('bseller_skyhub/tracking/carriers'));
        if (!$config) {
            return $url;
        }
        
        foreach ($config as $value) {
            if ($shippingMethod != $value['carriers']) {
                continue;
            }
            return $value['carriers_url'];
        }
    }

    /**
     * @param int $orderId
     *
     * @return Mage_Sales_Model_Order
     */
    protected function getOrder($orderId)
    {
        /** @var Mage_Sales_Model_Order $order */
        $order = Mage::getModel('sales/order')->load($orderId);
        return $order;
    }

}