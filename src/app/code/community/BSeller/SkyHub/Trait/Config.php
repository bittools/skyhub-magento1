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
 * @author    Bruno Gemelli <bruno.gemelli@e-smart.com.br>
 * @author    Julio Reis <julio.reis@e-smart.com.br>
 */

trait BSeller_SkyHub_Trait_Config
{

    use BSeller_Core_Trait_Config,
        BSeller_SkyHub_Trait_Config_Service,
        BSeller_SkyHub_Trait_Config_Log,
        BSeller_SkyHub_Trait_Config_General;


    /**
     * @param string   $field
     * @param string   $group
     * @param int|null $storeId
     *
     * @return mixed
     */
    protected function getSkyHubModuleConfig($field, $group, $storeId = null)
    {
        return $this->getModuleConfig($field, $group, 'bseller_skyhub', $storeId);
    }

    /**
     * @param $field
     * @param $group
     * @param $value
     * @param null $storeId
     *
     * @return Mage_Core_Store_Config
     */
    protected function setSkyhubModuleConfig($field, $group, $value, $storeId = 0)
    {
        $path = implode('/', array('bseller_skyhub', $group, $field));
        $success = Mage::getConfig()->saveConfig($path, $value, 'default', $storeId);

        Mage::app()->getStore()->resetConfig();

        return $success;
    }

    /**
     * @param string   $field
     * @param string   $group
     * @param int|null $storeId
     *
     * @return array
     */
    protected function getSkyHubModuleConfigAsArray($field, $group, Mage_Core_Model_Store $store = null)
    {
        $values      = $this->getModuleConfig($field, $group, 'bseller_skyhub', $store);
        $arrayValues = explode(',', $values);

        return $arrayValues;
    }


    /**
     * @param string   $field
     * @param null|int $storeId
     *
     * @return string|integer
     */
    protected function getGeneralConfig($field, $storeId = null)
    {
        return $this->getSkyHubModuleConfig($field, 'general', $storeId);
    }


    /**
     * @param null|int $storeId
     *
     * @return boolean
     */
    protected function isModuleEnabled($storeId = null)
    {
        return (bool) $this->getGeneralConfig('enabled', $storeId);
    }


    /**
     * @return int
     */
    protected function getNewOrdersDefaultStoreId()
    {
        return (int) $this->getNewOrdersDefaultStore()->getId();
    }


    /**
     * @return Mage_Core_Model_Store
     */
    protected function getNewOrdersDefaultStore($storeId = null)
    {
        if (is_null($storeId)) {
            $storeId = (int) Mage::app()->getDefaultStoreView();
        }

        try {
            if (Mage::app()->getStore($storeId)->isAdmin()) {
                $storeId = Mage::app()->getDefaultStoreView()->getId();
            }

            return Mage::app()->getStore($storeId);
        } catch (Exception $e) {}

        return Mage::app()->getDefaultStoreView();
    }


    /**
     * @return string
     */
    protected function getNewOrdersStatus($storeId = null)
    {
        $status = (string) $this->getSkyHubModuleConfig('new_order_status', 'sales_order_status', $storeId);

        if (empty($status)) {
            $status = $this->getDefaultStatusByState(Mage_Sales_Model_Order::STATE_NEW);
        }

        return $status;
    }


    /**
     * @param null|int $storeId
     *
     * @return string
     */
    protected function getApprovedOrdersStatus($storeId = null)
    {
        $status = (string) $this->getSkyHubModuleConfig('approved_order_status', 'sales_order_status', $storeId);

        if (empty($status)) {
            $status = $this->getDefaultStatusByState(Mage_Sales_Model_Order::STATE_PROCESSING);
        }

        return $status;
    }


    /**
     * @param null|int $storeId
     *
     * @return string
     */
    protected function getDeliveredOrdersStatus($storeId = null)
    {
        $status = (string) $this->getSkyHubModuleConfig('delivered_order_status', 'sales_order_status', $storeId);

        if (empty($status)) {
            $status = $this->getDefaultStatusByState(Mage_Sales_Model_Order::STATE_COMPLETE);
        }

        return $status;
    }


    /**
     * @param null|int $storeId
     *
     * @return string
     */
    protected function getShipmentExceptionOrderStatus($storeId = null)
    {
        $status = (string) $this->getSkyHubModuleConfig(
            'shipment_exception_order_status', 'sales_order_status', $storeId
        );

        if (empty($status)) {
            $status = $this->getDefaultStatusByState(Mage_Sales_Model_Order::STATE_COMPLETE);
        }

        return $status;
    }

    /**
     * @param null|int $storeId
     *
     * @return string|null
     */
    protected function getSkyHubCustomShippedStatus($storeId = null)
    {
        $status = (string) $this->getSkyHubModuleConfig('shipped_status', 'sales_order_status_skyhub', $storeId);

        return $status ?: null;
    }

    /**
     * @param null|int $storeId
     *
     * @return string|null
     */
    protected function getSkyHubCustomDeliveredStatus($storeId = null)
    {
        $status = (string) $this->getSkyHubModuleConfig(
            'delivered_order_status',
            'sales_order_status_skyhub',
            $storeId
        );

        return $status ?: null;
    }

    /**
     * @param null|int $storeId
     *
     * @return string|null
     */
    protected function getSkyHubCustomCanceledStatus($storeId = null)
    {
        $status = (string) $this->getSkyHubModuleConfig(
            'canceled_order_status',
            'sales_order_status_skyhub',
            $storeId
        );

        return $status ?: null;
    }

    /**
     * @param null|int $storeId
     *
     * @return string|null
     */
    protected function getSkyHubCustomShipmentExceptionStatus($storeId = null)
    {
        $status = (string) $this->getSkyHubModuleConfig(
            'shipment_exception_status',
            'sales_order_status_skyhub',
            $storeId
        );

        return $status ?: null;
    }

    /**
     * @param null|int $storeId
     *
     * @return string|null
     */
    protected function getSkyHubCustomInvoicedStatus($storeId = null)
    {
        $status = (string) $this->getSkyHubModuleConfig('invoiced_status', 'sales_order_status_skyhub', $storeId);

        return $status ?: null;
    }

    /**
     * @param string $state
     *
     * @return string
     */
    protected function getDefaultStatusByState($state)
    {
        /** @var Mage_Sales_Model_Order_Status $status */
        $status = Mage::getModel('sales/order_status');
        $status->loadDefaultByState($state);

        return (string) $status->getId();
    }


    /**
     * @return string
     */
    protected function getTaxInvoiceKeyPattern($storeId = null)
    {
        return (string) $this->getSkyHubModuleConfig('pattern', 'tax_invoice_key', $storeId);
    }


    /**
     * @return BSeller_SkyHub_Model_Config
     */
    protected function getProductSkyHubConfig()
    {
        return Mage::getSingleton('bseller_skyhub/config_catalog_product');
    }

    /**
     * @return BSeller_SkyHub_Model_Config
     */
    protected function getCustomerSkyHubConfig()
    {
        return Mage::getSingleton('bseller_skyhub/config_customer');
    }


    /**
     * @return boolean
     */
    protected function hasActiveIntegrateOnSaveFlag($storeId = null)
    {
        return (bool) $this->getGeneralConfig('immediately_integrate_product_on_save_price_stock_change', $storeId);
    }

    /**
     * @param string $field
     *
     * @return string|integer
     */
    protected function getCustomerConfig($field)
    {
        return $this->getSkyHubModuleConfig($field, 'customer');
    }

    /**
     * @param string $field
     *
     * @return string|integer
     */
    protected function allowCustomerEmailCreationWithTaxvat()
    {
        return $this->getCustomerConfig('allow_customer_email_creation_with_taxvat');
    }

    /**
     * @param string $field
     *
     * @return string|integer
     */
    protected function allowRegisterCustomerAddress()
    {
        return (bool)$this->getCustomerConfig('allow_register_customer_address');
    }

    protected function customerEmailCreationWithTaxvatPattern()
    {
        return $this->getCustomerConfig('customer_email_creation_with_taxvat_pattern');
    }

    /**
     * @return boolean
     */
    protected function hasActiveIntegrateProductsOnOrderPlaceFlag()
    {
        return (bool)$this->getGeneralConfig('immediately_integrate_order_products_order_create');
    }
}
