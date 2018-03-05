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

class BSeller_SkyHub_Model_Integrator_Sales_Order extends BSeller_SkyHub_Model_Integrator_Abstract
{

    /**
     * @param int   $page
     * @param int   $perPage
     * @param null  $saleSystem
     * @param array $statuses
     *
     * @return array|bool
     */
    public function orders($page = 1, $perPage = 30, $saleSystem = null, array $statuses = [])
    {
        /** @var \SkyHub\Api\EntityInterface\Sales\Order $interface */
        $interface = $this->api()->order()->entityInterface();
        $result    = $interface->orders($page, $perPage, $saleSystem, $statuses);

        if ($result->exception() || $result->invalid()) {
            return false;
        }

        /** @var \SkyHub\Api\Handler\Response\HandlerDefault $result */
        $orders = $result->toArray();

        if (empty($orders) || !isset($orders['orders'])) {
            return false;
        }

        return (array) $orders['orders'];
    }
    
    
    /**
     * @param integer $orderId
     *
     * @return array|bool
     */
    public function orderByOrderId($orderId)
    {
        $incrementId = $this->getOrderIncrementId((int) $orderId);
        
        if (empty($incrementId)) {
            return false;
        }
        
        return $this->order($incrementId);
    }


    /**
     * @param int $orderId
     *
     * @return array|bool
     */
    public function order($orderId)
    {
        /** @var  $result */
        $result = $this->getEntityInterface()->order($orderId);

        if ($result->exception() || $result->invalid()) {
            return false;
        }

        /** @var \SkyHub\Api\Handler\Response\HandlerDefault $result */
        $order = $result->toArray();

        return (array) $order;
    }


    /**
     * @param int    $orderId
     * @param string $invoiceKey
     *
     * @return bool
     */
    public function invoice($orderId, $invoiceKey)
    {
        $incrementId = $this->getOrderIncrementId($orderId);

        /** @var \SkyHub\Api\Handler\Response\HandlerInterface $result */
        $result = $this->getEntityInterface()->invoice($incrementId, $invoiceKey);

        if ($result->exception() || $result->invalid()) {
            return false;
        }

        return true;
    }


    /**
     * @param int $orderId
     *
     * @return bool
     */
    public function cancel($orderId)
    {
        $incrementId = $this->getOrderIncrementId($orderId);

        /** @var \SkyHub\Api\Handler\Response\HandlerInterface $result */
        $result = $this->getEntityInterface()->cancel($incrementId);

        if ($result->exception() || $result->invalid()) {
            return false;
        }

        return true;
    }


    /**
     * @param int $orderId
     *
     * @return bool
     */
    public function delivery($orderId)
    {
        $incrementId = $this->getOrderIncrementId($orderId);

        /** @var \SkyHub\Api\Handler\Response\HandlerInterface $result */
        $result = $this->getEntityInterface()->delivery($incrementId);

        if ($result->exception() || $result->invalid()) {
            return false;
        }

        return true;
    }


    /**
     * @param int $orderId
     *
     * @return array|bool|stdClass
     */
    public function shipmentLabels($orderId)
    {
        $incrementId = $this->getOrderIncrementId($orderId);

        /** @var \SkyHub\Api\Handler\Response\HandlerInterface $result */
        $result = $this->getEntityInterface()->shipmentLabels($incrementId);

        if ($result->exception() || $result->invalid()) {
            return false;
        }

        /** @var \SkyHub\Api\Handler\Response\HandlerDefault $result */
        return $result->toArray();
    }


    /**
     * @param string $orderId
     * @param array  $items
     * @param string $trackCode
     * @param string $trackCarrier
     * @param string $trackMethod
     * @param string $trackUrl
     *
     * @return array|bool|stdClass
     */
    public function shipment($orderId, array $items, $trackCode, $trackCarrier, $trackMethod, $trackUrl)
    {
        $incrementId = $this->getOrderIncrementId($orderId);

        /** @var \SkyHub\Api\Handler\Response\HandlerInterface $result */
        $result = $this->getEntityInterface()
            ->shipment($incrementId, $items, $trackCode, $trackCarrier, $trackMethod, $trackUrl);

        if ($result->exception() || $result->invalid()) {
            return false;
        }

        /** @var \SkyHub\Api\Handler\Response\HandlerDefault $result */
        return $result->toArray();
    }
    
    
    /**
     * @param int    $orderId
     * @param string $datetime
     * @param string $observation
     *
     * @return bool
     */
    public function shipmentException($orderId, $datetime, $observation)
    {
        $incrementId = $this->getOrderIncrementId($orderId);
        $result = $this->getEntityInterface()
                       ->shipmentException($incrementId, $datetime, $observation);
    
        if ($result->exception() || $result->invalid()) {
            return false;
        }
    
        /** @var \SkyHub\Api\Handler\Response\HandlerDefault $result */
        return true;
    }


    /**
     * @param int $orderId
     *
     * @return string
     */
    protected function getOrderIncrementId($orderId)
    {
        /** @var Mage_Sales_Model_Resource_Order $resource */
        $resource    = Mage::getResourceModel('sales/order');
        $incrementId = $resource->getIncrementId($orderId);

        return $incrementId;
    }


    /**
     * @return \SkyHub\Api\EntityInterface\Sales\Order
     */
    protected function getEntityInterface()
    {
        return $this->api()->order()->entityInterface();
    }

}
