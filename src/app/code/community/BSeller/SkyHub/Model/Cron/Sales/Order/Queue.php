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

class BSeller_SkyHub_Model_Cron_Sales_Order_Queue extends BSeller_SkyHub_Model_Cron_Abstract
{

    use BSeller_Core_Trait_Data,
        BSeller_SkyHub_Trait_Customer,
        BSeller_SkyHub_Trait_Service;


    /**
     * @param Mage_Cron_Model_Schedule $schedule
     */
    public function importNext(Mage_Cron_Model_Schedule $schedule)
    {
        if (!$this->canRun()) {
            return;
        }

        /**
        $mock = '{
          "total": 1,
          "orders": [
            {
              "updated_at": "2018-02-12T19:23:48-02:00",
              "total_ordered": 123.45,
              "sync_status": "NOT_SYNCED",
              "status": {
                "type": "NEW",
                "label": "Pagamento Pendente (SkyHub)",
                "code": "book_product"
              },
              "shipping_method": "Correios PAC",
              "shipping_cost": 0,
              "shipping_address": {
                "street": "Rua Sacadura Cabral",
                "secondary_phone": "21 3722-3902",
                "region": "RJ",
                "reference": null,
                "postcode": "20081262",
                "phone": "21 3722-3902",
                "number": "130",
                "neighborhood": "Centro",
                "full_name": "Bruno santos",
                "detail": "foo",
                "country": "BR",
                "city": "Rio de Janeiro"
              },
              "shipments": [],
              "placed_at": "2018-02-12T19:23:48-02:00",
              "payments": [
                {
                  "value": 0,
                  "status": null,
                  "parcels": 1,
                  "method": "skyhub_payment",
                  "description": "Skyhub"
                }
              ],
              "items": [
                {
                  "special_price": 0,
                  "qty": 1,
                  "product_id": "PS4SLIM",
                  "original_price": 55.22,
                  "name": "Console Playstation 4 Slim 500GB",
                  "id": "PS4SLIM"
                }
              ],
              "invoices": [],
              "interest": 0,
              "import_info": {
                "remote_id": null,
                "remote_code": "1518470628274"
              },
              "estimated_delivery_shift": null,
              "estimated_delivery": "2018-02-10T22:00:00-02:00",
              "discount": 0,
              "customer": {
                "vat_number": "78732371683",
                "phones": [
                  "21 3722-3902",
                  "21 3722-3902"
                ],
                "name": "Bruno santos",
                "gender": "male",
                "email": "exemplo@skyhub.com.br",
                "date_of_birth": "1998-01-25"
              },
              "code": "TESTE-'.Mage::helper('core')->getRandomString(15, '1234567890').'",
              "channel": "Teste",
              "calculation_type": null,
              "billing_address": {
                "street": "Rua Fidencio Ramos",
                "secondary_phone": "21 3722-3902",
                "region": "RJ",
                "reference": null,
                "postcode": "04551101",
                "phone": "21 3722-3902",
                "number": "302",
                "neighborhood": "Centro",
                "full_name": "Bruno santos",
                "detail": "foo",
                "country": "BR",
                "city": "Rio de Janeiro"
              }
            }
          ]
        }';
    
        /** @var array $order * /
        $orders = json_decode($mock, true);
        */

        /** @var \SkyHub\Api\EntityInterface\Sales\Order\Queue $interface */
        $interface = $this->api()->queue()->entityInterface();
        // $interface = $this->api()->order()->entityInterface();
        $result = $interface->orders();
        
        if ($result->exception() || $result->invalid()) {
            return;
        }
        
        /** @var \SkyHub\Api\Handler\Response\HandlerDefault $result */
        $orderData = $result->json();
        
        if (empty($orderData)) {
            $schedule->setMessages($this->__('No order found in the queue.'));
            return;
        }
    
        /** @var Mage_Sales_Model_Order $order */
        $order = $this->getIntegrator()->importOrder($orderData);
        
        if (!$order || !$order->getId()) {
            $schedule->setMessages($this->__('Order cannot be created.'));
            return;
        }
        
        $message = $this->__('Order %s successfully created.', $order->getIncrementId());
        
        /** @var \SkyHub\Api\Handler\Response\HandlerDefault $isDeleted */
        $isDeleted = $interface->delete($order->getIncrementId());
        
        if ($isDeleted->success()) {
            $message .= ' ' . $this->__('It was also removed from queue.');
        }
    
        $schedule->setMessages($message);
    }
    
    
    /**
     * @return BSeller_SkyHub_Model_Integrator_Sales_Order
     */
    protected function getIntegrator()
    {
        return Mage::getModel('bseller_skyhub/integrator_sales_order');
    }
    
    
    /**
     * @return bool
     */
    protected function canRun()
    {
        if (!$this->isCronSalesOrderQueueEnabled()) {
            return false;
        }
        
        return parent::canRun();
    }
}
