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
class BSeller_SkyHub_Model_Processor_Sales_Order_Status extends BSeller_SkyHub_Model_Integrator_Abstract
{
    /**
     * @param string $skyhubStatusCode
     * @param string $skyhubStatusType
     * @param Mage_Sales_Model_Order $order
     *
     * @return bool|$this
     */
    public function processOrderStatus($skyhubStatusCode, $skyhubStatusType, Mage_Sales_Model_Order $order, array $orderData, $updateFromSkyhubQueue = false)
    {
        if (!$this->validateOrderStatusType($skyhubStatusType)) {
            return false;
        }

        $state = $this->getStateBySkyhubStatusType($skyhubStatusType);

        //if the state is the same, means there's no order movement, so keep it in track;
        if ($state != Mage_Sales_Model_Order::STATE_COMPLETE && $state == $order->getState()) {
            return false;
        }

        if (
            $order->hasShipments() && $order->hasInvoices() &&
            (
                $order->getStatus() == $this->getShipmentExceptionOrderStatus() ||
                $order->getStatus() == $this->getDeliveredOrdersStatus()
            )
        ) {
            return false;
        }

        //if the state is 'holded', just skip;
        if ($order->getState() == Mage_Sales_Model_Order::STATE_HOLDED) {
            return false;
        }

        if (
            $order->hasShipments() &&
            $skyhubStatusType == BSeller_SkyHub_Model_System_Config_Source_Skyhub_Status_Types::TYPE_SHIPPED
        ) {
            return false;
        }

        if (
            $skyhubStatusType == BSeller_SkyHub_Model_System_Config_Source_Skyhub_Status_Types::TYPE_APPROVED
        ) {
            $order->setBsellerSkyhubStatus(
                BSeller_SkyHub_Model_System_Config_Source_Skyhub_Status_Types::TYPE_APPROVED
            );
        }

        /**
         * If order is CANCELED in SkyHub.
         */
        if ($state == Mage_Sales_Model_Order::STATE_CANCELED) {
            $this->cancelOrder($order);
        }

        /**
         * If order is APPROVED in SkyHub.
         */
        if ($state == Mage_Sales_Model_Order::STATE_PROCESSING && $order->canInvoice()) {
            $this->invoiceOrder($order);
            $status = $this->getApprovedOrdersStatus();
        }

        /**
         * If order is SHIPPED in SkyHub.
         */
        if ($state == Mage_Sales_Model_Order::STATE_COMPLETE && $order->canShip()) {
            $trackingNumbers = $this->getTrackingNumbers($orderData);
            $this->shipOrder($order, $trackingNumbers);
            $isOrderShippedStatus = true;
        }

        /**
         * If order dont have invoice but have shipment, invoiced order
         */
        if ($order->hasShipments() && !$order->hasInvoices()) {
            $this->invoiceOrder($order);
            $status = $this->getApprovedOrdersStatus();
        }

        /**
         * If order is DELIVERED in SkyHub.
         */
        if ($skyhubStatusType == BSeller_SkyHub_Model_System_Config_Source_Skyhub_Status_Types::TYPE_DELIVERED) {
            $status = $this->getDeliveredOrdersStatus();
            $isOrderDeliveredStatus = true;
        }

        /**
         * If order is SHIPMENT_EXCEPTION in SkyHub.
         */
        if ($skyhubStatusType == BSeller_SkyHub_Model_System_Config_Source_Skyhub_Status_Types::TYPE_SHIPMENT_EXCEPTION) {
            $status = $this->getShipmentExceptionOrderStatus();
        }

        $message = $this->__(
            'Change automatically by SkyHub. Status %s, Type %s.',
            $skyhubStatusCode,
            $skyhubStatusType
        );

        //this is because inside method "shipOrder" it already changes the status/state of the order;
        if (!isset($isOrderShippedStatus)) {
            $status = isset($status) ? $status : true;

            if (!isset($isOrderDeliveredStatus)) {
                $order->setState($state, $status, $message);
            } else {
                $order->addStatusHistoryComment($message, $status);
            }

            $order->save();
        } else {
            $order->addStatusHistoryComment(
                $message
            );
            $order->save();
        }

        return true;
    }


    /**
     * @param string $skyhubStatusType
     *
     * @return string
     */
    public function getStateBySkyhubStatusType($skyhubStatusType)
    {
        switch ($skyhubStatusType) {
            case BSeller_SkyHub_Model_System_Config_Source_Skyhub_Status_Types::TYPE_APPROVED:
                return Mage_Sales_Model_Order::STATE_PROCESSING;
            case BSeller_SkyHub_Model_System_Config_Source_Skyhub_Status_Types::TYPE_CANCELED:
                return Mage_Sales_Model_Order::STATE_CANCELED;
            case BSeller_SkyHub_Model_System_Config_Source_Skyhub_Status_Types::TYPE_SHIPPED:
            case BSeller_SkyHub_Model_System_Config_Source_Skyhub_Status_Types::TYPE_SHIPMENT_EXCEPTION:
            case BSeller_SkyHub_Model_System_Config_Source_Skyhub_Status_Types::TYPE_DELIVERED:
                return Mage_Sales_Model_Order::STATE_COMPLETE;
            case BSeller_SkyHub_Model_System_Config_Source_Skyhub_Status_Types::TYPE_NEW:
            default:
                return Mage_Sales_Model_Order::STATE_NEW;
        }
    }


    /**
     * @param string $skyhubStatusType
     *
     * @return bool
     */
    public function validateOrderStatusType($skyhubStatusType)
    {
        /** @var BSeller_SkyHub_Model_System_Config_Source_Skyhub_Status_Types $source */
        $source = Mage::getModel('bseller_skyhub/system_config_source_skyhub_status_types');
        $allowedTypes = $source->toArray();

        return isset($allowedTypes[$skyhubStatusType]);
    }


    /**
     * @param Mage_Sales_Model_Order $order
     *
     * @return bool
     *
     * @throws Exception
     */
    protected function cancelOrder(Mage_Sales_Model_Order $order)
    {
        /** @var $order Mage_Sales_Model_Order */
        if ($order->hasInvoices()) {
            /** @var \Mage_Sales_Model_Order_Invoice $invoice */
            foreach ($order->getInvoiceCollection() as $invoice) {
                $this->cancelInvoice($invoice);
            }
        }

        if (!$order->canCancel()) {
            Mage::throwException($this->__('Order is canceled in SkyHub but could not be canceled in Magento.'));
        }
        $order->addStatusHistoryComment($this->__('Order canceled automatically by SkyHub.'));
        $order->cancel();
        return true;
    }


    /**
     * @param Mage_Sales_Model_Order $order
     *
     * @return bool
     *
     * @throws Exception
     */
    protected function invoiceOrder(Mage_Sales_Model_Order $order)
    {
        if (!$order->canInvoice()) {
            $comment = $this->__('This order is APPROVED in SkyHub but cannot be invoiced in Magento.');
            $order->addStatusHistoryComment($comment, true);
            $order->save();

            return false;
        }

        /** @var Mage_Sales_Model_Order_Invoice $invoice */
        $invoice = $order->prepareInvoice();
        $invoice->register();

        $comment = $this->__('Invoiced automatically via SkyHub.');
        $invoice->addComment($comment);

        /** @var Mage_Core_Model_Resource_Transaction $transaction */
        $transaction = Mage::getResourceModel('core/transaction');
        $transaction->addObject($order)
            ->addObject($invoice)
            ->save();

        return true;
    }

    /**
     * @param Mage_Sales_Model_Order $order
     *
     * @return bool
     *
     * @throws Exception
     */
    protected function shipOrder(Mage_Sales_Model_Order $order, $trackingNumbers)
    {
        if (!$order->canShip() || !$trackingNumbers || empty($trackingNumbers)) {
            Mage::throwException("Can't create shipment, there's no tracking code or order not allowed");
        }

        $items = array();

        foreach ($order->getAllItems() as $item) {
            $items[$item->getId()] = $item->getQtyOrdered();
        }

        $shipment = Mage::getModel('sales/service_order', $order)->prepareShipment($items);

        foreach($trackingNumbers as $trackingNumber) {
            $dataTracking = array(
                'carrier_code' => 'custom',
                'title' => $trackingNumber['carrier'],
                'number' => $trackingNumber['code']
            );

            /** @var Mage_Sales_Model_Order_Shipment_Track $track */
            $track = Mage::getModel('sales/order_shipment_track')->addData($dataTracking);
            $shipment->addTrack($track);
        }

        $shipment->setEmailSent(true);
        $shipment->sendEmail(true);
        $shipment->getOrder()->setCustomerNoteNotify(true);
        $shipment->register();
        $shipment->getOrder()->setIsInProcess(true);

        Mage::getModel('core/resource_transaction')
            ->addObject($shipment)
            ->save();
        return $this;
    }

    protected function getTrackingNumbers($skyhubOrderData)
    {
        $shipments = $this->arrayExtract($skyhubOrderData, 'shipments');
        $arrayResult = array();

        foreach ($shipments as $shipment) {
            $tracks = $shipment['tracks'];

            foreach ($tracks as $track) {
                $arrayResult[] = array(
                    'code' => $track['code'],
                    'carrier' => $track['carrier']
                );
            }
        }

        return $arrayResult;
    }

    protected function cancelInvoice($invoice)
    {
        if ($invoice->isCanceled()) {
            return $this;
        }
        $invoice->cancel();
        Mage::getModel('core/resource_transaction')
            ->addObject($invoice)
            ->save();
    }
}