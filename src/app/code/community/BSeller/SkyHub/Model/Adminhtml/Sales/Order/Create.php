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
 
class BSeller_SkyHub_Model_Adminhtml_Sales_Order_Create extends Mage_Adminhtml_Model_Sales_Order_Create
{
    
    use BSeller_SkyHub_Trait_Data,
        BSeller_SkyHub_Trait_Catalog_Product;
    

    public function __construct()
    {
        /** @var BSeller_SkyHub_Model_Adminhtml_Session_Quote $session */
        $this->_session = Mage::getSingleton('bseller_skyhub/adminhtml_session_quote');
    }
    
    
    /**
     * Initialize data for price rules
     *
     * @return Mage_Adminhtml_Model_Sales_Order_Create
     *
     * @throws Mage_Core_Exception
     */
    public function initRuleData()
    {
        Mage::register('rule_data', new Varien_Object(array(
            'store_id'          => $this->_session->getStore()->getId(),
            'website_id'        => $this->_session->getStore()->getWebsiteId(),
            'customer_group_id' => $this->getCustomerGroupId(),
        )), true);
        
        return $this;
    }
    
    
    /**
     * @param array $data
     *
     * @return bool
     *
     * @throws Mage_Core_Exception
     */
    public function addProductByData(array $data = array())
    {
        $productData = (array) $this->arrayExtract($data, 'product');
        $productId   = (int)   $this->arrayExtract($productData, 'product_id');
        
        if (!$productId) {
            return false;
        }
    
        /** @var Mage_Catalog_Model_Product $product */
        $product = $this->getProduct($productId);
        if (!$product->getId()) {
            return false;
        }
        
        $qty = (float) $this->arrayExtract($productData, 'qty');
        
        $this->registerCurrentData($product, $productData);
        
        switch ($product->getTypeId()) {
            case Mage_Catalog_Model_Product_Type::TYPE_CONFIGURABLE:
                $this->addProductConfigurable($product, $productData);
                break;
            case Mage_Catalog_Model_Product_Type::TYPE_GROUPED:
                $this->addProductGrouped($product, $productData);
                break;
            case Mage_Catalog_Model_Product_Type::TYPE_SIMPLE:
            default:
                $config = array('qty' => $qty);
                $this->addProduct($product, $config);
        }
        
        return true;
    }
    
    
    /**
     * @param BSeller_SkyHub_Model_Catalog_Product $product
     * @param array                                $productData
     *
     * @return $this
     * @throws Mage_Core_Exception
     */
    protected function registerCurrentData(Mage_Catalog_Model_Product $product, array $productData)
    {
        $key = 'skyhub_product_configuration';
        $product->setData($key, (array) $productData);
        
        return $this;
    }
    
    
    /**
     * @param BSeller_SkyHub_Model_Catalog_Product $product
     * @param array                                $productData
     *
     * @return bool
     */
    protected function addProductConfigurable(Mage_Catalog_Model_Product $product, array $productData = array())
    {
        $qty = (float) $this->arrayExtract($productData, 'qty');
    
        /**
         * @var BSeller_SkyHub_Model_Catalog_Product_Type_Configurable $typeInstance
         * @var Mage_Catalog_Model_Resource_Product_Type_Configurable_Attribute_Collection $attributes
         */
        $typeInstance    = $product->getTypeInstance(true);
        $attributes      = $typeInstance->getConfigurableAttributes($product);
        $superAttributes = array();
        $children        = (array) $this->arrayExtract($productData, 'children');
        
        /** @var Mage_Catalog_Model_Product_Type_Configurable_Attribute $attribute */
        foreach ($attributes as $attribute) {
            /** @var array $child */
            foreach ($children as $child) {
                $childId     = (int) $this->arrayExtract($child, 'product_id');
                $attributeId = $attribute->getAttributeId();
                $value       = $product->getResource()
                                       ->getAttributeRawValue($childId, $attributeId, $product->getStore());
    
                if (!$value) {
                    continue;
                }
                
                $superAttributes[$attributeId] = $value;
            }
        }
        
        $config = array(
            'qty'             => $qty,
            'config'          => $productData,
            'super_attribute' => $superAttributes,
        );
        
        $this->addProduct($product, $config);
        
        return true;
    }
    
    
    /**
     * @param BSeller_SkyHub_Model_Catalog_Product $product
     * @param array                      $productData
     *
     * @return $this
     */
    protected function addProductGrouped(Mage_Catalog_Model_Product $product, array $productData = array())
    {
        $children = (array) $this->arrayExtract($productData, 'children');
        $qty      = (float) $this->arrayExtract($productData, 'qty');
        
        $childrenIds = array();
        
        /** @var array $child */
        foreach ($children as $child) {
            $childId = $this->arrayExtract($child, 'product_id');
            
            if (!$childId || !$this->validateProductId($childId)) {
                continue;
            }
            
            $childrenIds[$childId] = $qty;
        }
        
        $params = array(
            'config'      => $productData,
            'super_group' => $childrenIds,
        );
        
        $this->addProduct($product, $params);
        
        return $this;
    }
    
    
    /**
     * @param int $productId
     *
     * @return BSeller_SkyHub_Model_Catalog_Product
     */
    protected function getProduct($productId)
    {
        /** @var BSeller_SkyHub_Model_Catalog_Product $product */
        $product = Mage::getModel('bseller_skyhub/catalog_product');
        
        if ($productId) {
            $product->load((int) $productId);
        }
        
        return $product;
    }
    
    
    /**
     * @param int $productId
     *
     * @return bool
     */
    protected function validateProductId($productId)
    {
        /** @var Mage_Catalog_Model_Resource_Product $resource */
        $resource = Mage::getResourceSingleton('catalog/product');
        $result   = $resource->getProductsSku((array) $productId);
        
        return !empty($result);
    }

    /**
     * Validate quote data before order creation
     *
     * @return Mage_Adminhtml_Model_Sales_Order_Create
     */
    protected function _validate()
    {
        $customerId = $this->getSession()->getCustomerId();
        if (is_null($customerId)) {
            Mage::throwException(Mage::helper('adminhtml')->__('Please select a customer.'));
        }

        $storeId = $this->getSession()->getStore()->getId();
        if (!$storeId && $storeId != 0) {
            Mage::throwException(
                Mage::helper('adminhtml')->__('Please select a store. %s', var_export($storeId, true))
            );
        }

        $items = $this->getQuote()->getAllItems();

        if (count($items) == 0) {
            $this->_errors[] = Mage::helper('adminhtml')->__('You need to specify order items.');
        }

        foreach ($items as $item) {
            $messages = $item->getMessage(false);
            if ($item->getHasError() && is_array($messages) && !empty($messages)) {
                $this->_errors = array_merge($this->_errors, $messages);
            }
        }

        if (!$this->getQuote()->isVirtual()) {
            if (!$this->getQuote()->getShippingAddress()->getShippingMethod()) {
                $this->_errors[] = Mage::helper('adminhtml')->__('Shipping method must be specified.');
            }
        }

        if (!$this->getQuote()->getPayment()->getMethod()) {
            $this->_errors[] = Mage::helper('adminhtml')->__('Payment method must be specified.');
        } else {
            $method = $this->getQuote()->getPayment()->getMethodInstance();
            if (!$method) {
                $this->_errors[] = Mage::helper('adminhtml')->__('Payment method instance is not available.');
            } else {
                if (!$method->isAvailable($this->getQuote())) {
                    $this->_errors[] = Mage::helper('adminhtml')->__('Payment method is not available.');
                } else {
                    try {
                        $method->validate();
                    } catch (Mage_Core_Exception $e) {
                        $this->_errors[] = $e->getMessage();
                    }
                }
            }
        }

        if (!empty($this->_errors)) {
            foreach ($this->_errors as $error) {
                $this->getSession()->addError($error);
            }
            Mage::throwException('');
        }
        return $this;
    }
}
