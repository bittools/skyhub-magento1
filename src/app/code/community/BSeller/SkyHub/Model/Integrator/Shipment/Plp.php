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
 * @author    Bruno Gemelli <bruno.gemelli@e-smart.com.br>
 */

class BSeller_SkyHub_Model_Integrator_Shipment_Plp extends BSeller_SkyHub_Model_Integrator_Abstract
{
    /**
     * @return bool|\SkyHub\Api\Handler\Response\HandlerInterface
     */
    public function plps()
    {
        /** @var \SkyHub\Api\EntityInterface\Shipment\Plp $interface */
        $interface = $this->getEntityInterface();
        $result    = $interface->plps();

        if ($result->exception() || $result->invalid()) {
            return false;
        }

        /** @var \SkyHub\Api\Handler\Response\HandlerDefault $items */
        $items = $result->toArray();

        if (empty($items) || !isset($items['plp'])) {
            return false;
        }

        return (array) $items['plp'];
    }


    /**
     * @return bool|\SkyHub\Api\Handler\Response\HandlerInterface
     */
    public function getOrdersAvailableToGroup()
    {
        /** @var \SkyHub\Api\EntityInterface\Shipment\Plp $interface */
        $interface = $this->getEntityInterface();
        $result    = $interface->ordersReadyToGroup();

        if ($result->exception() || $result->invalid()) {
            return false;
        }

        /** @var \SkyHub\Api\Handler\Response\HandlerDefault $ordersToGroup */
        $ordersToGroup = $result->toArray();

        if (empty($ordersToGroup) || !isset($ordersToGroup['orders'])) {
            return false;
        }

        return (array) $ordersToGroup['orders'];
    }


    /**
     * @param array $orders
     *
     * @return array|bool
     */
    public function group(array $orders)
    {
        /** @var \SkyHub\Api\EntityInterface\Shipment\Plp $interface */
        $interface = $this->getEntityInterface();

        foreach ($orders as $order) {
            $interface->addOrder($this->_prepareOrderCode($order));
        }

        $result = $interface->group();

        if ($result->exception() || $result->invalid()) {
            return false;
        }

        /** @var \SkyHub\Api\Handler\Response\HandlerDefault $data */
        $data = $result->toArray();

        if (empty($data)) {
            return false;
        }

        return (array) $data;
    }

    /**
     * @param $code
     * @return mixed
     */
    protected function _prepareOrderCode($code)
    {
        $code = explode('-', $code);
        return isset($code[1]) ? $code[1] : $code[0];
    }

    /**
     * @return BSeller_SkyHub_Helper_Data
     */
    protected function _helper()
    {
        return Mage::helper('bseller_skyhub');
    }

    /**
     * @param $plp
     *
     * @return string|bool
     */
    public function viewFile($plp)
    {
        /** @var \SkyHub\Api\EntityInterface\Shipment\Plp $interface */
        $interface = $this->getEntityInterface();

        $interface->setId($plp->getSkyhubCode());

        $result = $interface->viewFile();

        if ($result->exception() || $result->invalid()) {
            return false;
        }

        /** @var \SkyHub\Api\Handler\Response\HandlerDefault $data */
        $data = $result->body();

        if (empty($data)) {
            return false;
        }

        return $data;
    }


    /**
     * @param string $id
     *
     * @return array|bool
     */
    public function ungroup($id)
    {
        /** @var \SkyHub\Api\EntityInterface\Shipment\Plp $interface */
        $interface = $this->getEntityInterface();

        $interface->setId($id);

        $result = $interface->ungroup();

        if ($result->exception() || $result->invalid()) {
            return false;
        }

        /** @var \SkyHub\Api\Handler\Response\HandlerDefault $data */
        $data = $result->toArray();

        if (empty($data)) {
            return false;
        }

        return (array) $data;
    }


    /**
     * @return \SkyHub\Api\EntityInterface\Shipment\Plp
     */
    protected function getEntityInterface()
    {
        return $this->api()->plp()->entityInterface();
    }
}