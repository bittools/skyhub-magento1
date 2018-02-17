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

class BSeller_SkyHub_Model_Processor_Sales_Order extends BSeller_SkyHub_Model_Processor_Abstract
{
    
    /**
     * @param array $data
     *
     * @return Mage_Sales_Model_Order|bool
     *
     * @throws Exception
     */
    public function createOrder(array $data)
    {
        $code        = $this->arrayExtract($data, 'code');
        $channel     = $this->arrayExtract($data, 'channel');
        $incrementId = $this->getOrderIncrementId($code);
        
        /** @var BSeller_SkyHub_Model_Resource_Sales_Order $orderResource */
        $orderResource = Mage::getResourceModel('bseller_skyhub/sales_order');
        $orderId       = $orderResource->getEntityIdByIncrementId($incrementId);
        
        if ($orderId) {
            /**
             * @var Mage_Sales_Model_Order $order
             *
             * Order already exists.
             */
            $order = Mage::getModel('sales/order')->load($orderId);
            return $order;
        }
        
        $info = new Varien_Object([
            'increment_id'      => $incrementId,
            'send_confirmation' => 0
        ]);
    
        $billingAddress  = new Varien_Object($this->arrayExtract($data, 'billing_address'));
        $shippingAddress = new Varien_Object($this->arrayExtract($data, 'shipping_address'));
        
        $customerData = (array) $this->arrayExtract($data, 'customer', []);
        $customerData = array_merge_recursive($customerData, [
            'billing_address'  => $billingAddress,
            'shipping_address' => $shippingAddress
        ]);
        
        /** @var Mage_Customer_Model_Customer $customer */
        $customer = $this->getCustomer($customerData);
    
        /** @var BSeller_SkyHub_Model_Support_Sales_Order_Create $creation */
        $creation = Mage::getModel('bseller_skyhub/support_sales_order_create', $this->getStore());
        $creation->setOrderInfo($info)
                 ->setCustomer($customer)
                 ->setShippingMethod('flatrate_flatrate')
                 ->setPaymentMethod('checkmo')
                 ->addOrderAddress('billing', $billingAddress)
                 ->addOrderAddress('shipping', $shippingAddress)
                 ->setComment('This order was automatically created by SkyHub import process.')
        ;
    
        $products = $this->getProducts((array) $this->arrayExtract($data, 'items'));
    
        if (empty($products)) {
            return false;
        }
    
        /** @var Mage_Catalog_Model_Product $product */
        foreach ($products as $product) {
            $creation->addProduct($product, $product->getData('qty'));
        }
    
        /** @var Mage_Sales_Model_Order $order */
        $order = $creation->create();

        if (!$order) {
            return false;
        }
    
        $order->setData('bseller_skyhub', true);
        $order->setData('bseller_skyhub_code', $code);
        $order->setData('bseller_skyhub_channel', $channel);

        /** Bizcommerce_SkyHub uses these fields. */
        $order->setData('skyhub_code', $code);
        $order->setData('skyhub_marketplace', $channel);
    
        $order->getResource()->save($order);

        $order->setData('is_created', true);

        return $order;
    }
    
    
    /**
     * @param array $items
     *
     * @return array
     */
    protected function getProducts(array $items)
    {
        $products = [];
        
        foreach ($items as $item) {
            $sku = $this->arrayExtract($item, 'product_id');
            $qty = $this->arrayExtract($item, 'qty');

            /** @var Mage_Catalog_Model_Product $product */
            $product   = Mage::getModel('catalog/product');
            $productId = (int) $product->getResource()->getIdBySku($sku);
            
            if (!$productId) {
                continue;
            }
            
            $product->load($productId);
            $product->setData('qty', (float) $qty);
            $product->setData('request_item', $item);

            $products[] = $product;
        }
        
        return $products;
    }
    
    
    /**
     * @param array                  $data
     *
     * @return Mage_Customer_Model_Customer
     *
     * @throws Exception
     */
    protected function getCustomer(array $data)
    {
        $email = $this->arrayExtract($data, 'email');
        
        /** @var Mage_Customer_Model_Customer $customer */
        $customer = Mage::getModel('customer/customer');
        $customer->setStore($this->getStore());
        $customer->loadByEmail($email);
        
        if (!$customer->getId()) {
            $this->createCustomer($data, $customer);
        }
        
        return $customer;
    }
    
    
    /**
     * @param array                        $data
     * @param Mage_Customer_Model_Customer $customer
     *
     * @return Mage_Customer_Model_Customer
     *
     * @throws Exception
     * @throws Mage_Core_Model_Store_Exception
     */
    protected function createCustomer(array $data, Mage_Customer_Model_Customer $customer)
    {
        $customer->setStore(Mage::app()->getStore());
        
        $dateOfBirth = $this->arrayExtract($data, 'date_of_birth');
        $email       = $this->arrayExtract($data, 'email');
        $gender      = $this->arrayExtract($data, 'gender');
        $name        = $this->arrayExtract($data, 'name');
        $vatNumber   = $this->arrayExtract($data, 'vat_number');
        $phones      = $this->arrayExtract($data, 'phones', []);
        
        /** @var Varien_Object $nameObject */
        $nameObject = $this->breakName($name);
        
        $customer->setFirstname($nameObject->getData('firstname'));
        $customer->setLastname($nameObject->getData('lastname'));
        $customer->setMiddlename($nameObject->getData('middlename'));
        $customer->setEmail($email);
        $customer->setDob($dateOfBirth);
        $customer->setTaxvat($vatNumber);
        
        /** @var string $phone */
        foreach ($phones as $phone) {
            $customer->setTelephone($phone);
            break;
        }
        
        switch ($gender) {
            case 'male':
                $customer->setGender(1);
                break;
            case 'female':
                $customer->setGender(2);
                break;
        }
        
        $customer->save();
        
        /** @var Varien_Object $billing */
        if ($billing = $this->arrayExtract($data, 'billing_address')) {
            $address = $this->createCustomerAddress($billing);
            $address->setCustomer($customer);
        }
        
        /** @var Varien_Object $billing */
        if ($shipping = $this->arrayExtract($data, 'shipping_address')) {
            $address = $this->createCustomerAddress($shipping);
            $address->setCustomer($customer);
        }
        
        return $customer;
    }
    
    
    /**
     * @param Varien_Object $addressObject
     *
     * @return Mage_Customer_Model_Address
     */
    protected function createCustomerAddress(Varien_Object $addressObject)
    {
        /** @var Mage_Customer_Model_Address $address */
        $address = Mage::getModel('customer/address');
        
        /**
         * @todo Create customer address algorithm based on $addressObject.
         */
        
        return $address;
    }
    
    
    /**
     * @return Mage_Core_Model_Store
     */
    protected function getStore()
    {
        return $this->getNewOrdersDefaultStore();
    }
    
    
    /**
     * @param string $code
     *
     * @return string
     */
    protected function getOrderIncrementId($code)
    {
        return $code;
    }
    
}
