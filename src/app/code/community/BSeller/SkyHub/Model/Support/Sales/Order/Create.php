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


class BSeller_SkyHub_Model_Support_Sales_Order_Create
{
    
    use BSeller_SkyHub_Trait_Data,
        BSeller_SkyHub_Trait_Customer;
    
    
    /** @var Mage_Core_Model_Store */
    private $store;
    
    /** @var array */
    private $data = [];
    
    /** @var BSeller_SkyHub_Model_Adminhtml_Sales_Order_Create */
    protected $creator;
    
    
    /**
     * BSeller_SkyHub_Model_Support_Sales_Order_Create constructor.
     *
     * @param null|Mage_Core_Model_Store $store
     *
     * @throws Mage_Core_Model_Store_Exception
     */
    public function __construct($store = null)
    {
        $data = [
            'session' => [
                'store_id' => $this->getStore($store)->getId(),
            ],
            'order'   => [
                'currency' => $this->getStore($store)->getCurrentCurrencyCode(),
            ],
        ];
        
        $this->merge($data);
    }
    
    
    /**
     * @param Varien_Object                $order
     * @param Mage_Customer_Model_Customer $customer
     *
     * @return $this
     */
    public function setOrderInfo(Varien_Object $order)
    {
        $data = [
            'order' => [
                'increment_id'      => $order->getData('increment_id'),
                'send_confirmation' => $order->getData('send_confirmation')
            ],
        ];
        
        $this->merge($data);
        
        return $this;
    }
    
    
    /**
     * @param null|string $comment
     *
     * @return $this
     */
    public function setComment($comment = null)
    {
        $data = [
            'order' => [
                'comment' => [
                    'customer_note' => $comment,
                ]
            ],
        ];
        
        $this->merge($data);
        
        return $this;
    }
    
    
    /**
     * @param Mage_Catalog_Model_Product $product
     * @param int                        $qty
     *
     * @return $this
     */
    public function addProduct(Mage_Catalog_Model_Product $product, $qty = 1)
    {
        $data = [
            'products' => [
                (int) $product->getId() => [
                    'model'  => $product,
                    'config' => [
                        'qty' => (float) ($qty ? $qty : 1)
                    ],
                ]
            ]
        ];
        
        $this->merge($data);
        
        return $this;
    }
    
    
    /**
     * @param string $method
     *
     * @return $this
     */
    public function setPaymentMethod($method = 'checkmo')
    {
        $data = [
            'payment' => [
                'method' => $method,
            ]
        ];
        
        $this->merge($data);
        
        return $this;
    }
    
    
    /**
     * @param string $method
     *
     * @return $this
     */
    public function setShippingMethod($method = 'flatrate_flatrate')
    {
        $data = [
            'order' => [
                'shipping_method' => $method,
            ]
        ];
        
        $this->merge($data);
        
        return $this;
    }
    
    
    /**
     * @param Mage_Customer_Model_Customer $customer
     *
     * @return $this
     */
    public function setCustomer(Mage_Customer_Model_Customer $customer)
    {
        $data = [
            'order' => [
                'account' => [
                    'group_id' => $customer->getGroupId(),
                    'email'    => $customer->getEmail()
                ]
            ],
            'session' => [
                'customer_id' => $customer->getId()
            ]
        ];
    
        $this->merge($data);
        
        return $this;
    }
    
    
    /**
     * @param string        $type
     * @param Varien_Object $address
     *
     * @return $this
     */
    public function addOrderAddress($type, Varien_Object $address)
    {
        $fullname = trim($address->getData('full_name'));
        
        /** @var Varien_Object $nameObject */
        $nameObject = $this->breakName($fullname);
        
        $street = [
            $address->getData('street'),
        ];
        
        $data = [
            'order' => [
                "{$type}_address" => [
                    'customer_address_id' => $address->getData('customer_address_id'),
                    'prefix'              => '',
                    'firstname'           => $nameObject->getData('firstname'),
                    'middlename'          => $nameObject->getData('middlename'),
                    'lastname'            => $nameObject->getData('lastname'),
                    'suffix'              => '',
                    'company'             => '',
                    'street'              => implode(' - ', $street),
                    'city'                => $address->getData('city'),
                    'country_id'          => $address->getData('country'),
                    'region'              => $address->getData('region'),
                    'region_id'           => '',
                    'postcode'            => $address->getData('postcode'),
                    'telephone'           => $address->getData('phone'),
                    'fax'                 => $address->getData('secondary_phone'),
                ]
            ]
        ];
        
        $this->merge($data);
        
        return $this;
    }
    
    
    /**
     * @return $this
     */
    public function reset()
    {
        $this->creator = null;
        $this->data    = [];
        $this->store   = null;
        
        return $this;
    }
    
    
    /**
     * @return Mage_Sales_Model_Quote
     */
    public function getQuote()
    {
        return $this->getOrderCreator()->getQuote();
    }
    
    
    /**
     * @return $this
     */
    protected function resetQuote()
    {
        $this->getQuote()
             ->setTotalsCollectedFlag(false);
        
        return $this;
    }
    
    
    /**
     * @return BSeller_SkyHub_Model_Adminhtml_Session_Quote
     */
    protected function getSession()
    {
        /** @var BSeller_SkyHub_Model_Adminhtml_Session_Quote $session */
        $session = Mage::getSingleton('bseller_skyhub/adminhtml_session_quote');
        return $session;
    }
    
    
    /**
     * Retrieve order create model
     *
     * @return  Mage_Adminhtml_Model_Sales_Order_Create
     */
    protected function getOrderCreator()
    {
        if (!$this->creator) {
            $this->creator = Mage::getModel('bseller_skyhub/adminhtml_sales_order_create');
        }
        
        return $this->creator;
    }
    
    
    /**
     * Initialize order creation session data
     *
     * @param array $data
     *
     * @return $this
     */
    protected function initSession($data)
    {
        /* Get/identify customer */
        if (!empty($data['customer_id'])) {
            $this->getSession()->setCustomerId((int) $this->arrayExtract($data, 'customer_id'));
        }
        
        /* Get/identify store */
        if (!empty($data['store_id'])) {
            $this->getSession()->setStoreId((int) $this->arrayExtract($data, 'store_id'));
        }
        
        return $this;
    }
    
    
    /**
     * Creates order
     */
    public function create()
    {
        $orderData = $this->data;
        
        if (!empty($orderData)) {
            $this->initSession($this->arrayExtract($orderData, 'session'));
            
            try {
                $this->processQuote($orderData);
                $payment = $this->arrayExtract($orderData, 'payment');
                
                if (!empty($payment)) {
                    $this->getOrderCreator()
                         ->setPaymentData($payment);
                    
                    $this->getQuote()
                         ->getPayment()
                         ->addData($payment);
                }
                
                /** This can be necessary. */
                // $this->processProductOptions();
                
                Mage::app()->getStore()->setConfig(Mage_Sales_Model_Order::XML_PATH_EMAIL_ENABLED, "0");
                
                /** @var Mage_Sales_Model_Order $order */
                $order = $this->getOrderCreator()
                              ->importPostData($this->arrayExtract($orderData, 'order'))
                              ->createOrder();
                
                $this->getSession()->clear();
                Mage::unregister('rule_data');
                
                return $order;
            } catch (Exception $e) {
                Mage::logException($e);
            }
        }
        
        return null;
    }
    
    
    /**
     * @return $this
     */
    protected function processProductOptions()
    {
        /**
         * @var int                        $productId
         * @var Mage_Catalog_Model_Product $product
         */
        $products = $this->arrayExtract($this->data, 'products');
        
        foreach ($this->products as $productId => $product) {
            $item = $this->getOrderCreator()->getQuote()->getItemByProduct($product);
    
            $options = [
                [
                    'product' => $product,
                    'code'    => 'option_ids',
                    'value'   => '5',
                    // Option id goes here. If more options, then comma separate
                ], [
                    'product' => $product,
                    'code'    => 'option_5',
                    'value'   => 'Some value here',
                ]
            ];
            
            /** @var array $option */
            foreach ($options as $option) {
                $item->addOption(new Varien_Object($option));
            }
        }
        
        return $this;
    }
    
    
    /**
     * @param array $data
     *
     * @return $this
     */
    protected function processQuote($data = array())
    {
        $order = (array) $this->arrayExtract($data, 'order', []);
        
        /* Saving order data */
        if (!empty($order)) {
            $this->getOrderCreator()->importPostData($order);
            $this->getQuote()
                 ->setReservedOrderId($this->arrayExtract($order, 'increment_id'));
        }
        
        // $this->getOrderCreator()->getBillingAddress();
        // $this->getOrderCreator()->getShippingAddress();
        
        /* Just like adding products from Magento admin grid */
        $products = (array) $this->arrayExtract($data, 'products', []);
        
        /** @var array $product */
        foreach ($products as $item) {
            $this->getOrderCreator()
                 ->addProduct($item['model'], $item['config']);
        }
        
        /* Collect shipping rates */
        $this->resetQuote()
             ->getOrderCreator()
             ->collectShippingRates();
        
        /* Add payment data */
        $payment = $this->arrayExtract($data, 'payment', []);
        if (!empty($payment)) {
            $this->getOrderCreator()
                 ->getQuote()
                 ->getPayment()
                 ->addData($payment);
        }
        
        $this->getOrderCreator()
             ->initRuleData()
             ->saveQuote();
        
        if (!empty($payment)) {
            $this->getOrderCreator()
                 ->getQuote()
                 ->getPayment()
                 ->addData($payment);
        }
        
        return $this;
    }
    
    
    /**
     * @param array $data
     *
     * @return $this
     */
    protected function merge(array $data = [])
    {
        $this->data = array_merge_recursive($this->data, $data);
        
        return $this;
    }
    
    
    /**
     * @return Mage_Core_Model_Store
     *
     * @throws Mage_Core_Model_Store_Exception
     */
    protected function getStore($store = null)
    {
        if (empty($store)) {
            $store = null;
        }
        
        if (!$this->store) {
            $this->store = Mage::app()->getStore($store);
        }
        
        return $this->store;
    }
}