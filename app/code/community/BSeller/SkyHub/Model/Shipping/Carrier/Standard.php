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

class BSeller_SkyHub_Model_Shipping_Carrier_Standard extends Mage_Shipping_Model_Carrier_Freeshipping
{

    /** @var string */
    protected $_code = 'bseller_skyhub_standard';


    /**
     * FreeShipping Rates Collector
     *
     * @param Mage_Shipping_Model_Rate_Request $request
     *
     * @return Mage_Shipping_Model_Rate_Result|false
     */
    public function collectRates(Mage_Shipping_Model_Rate_Request $request)
    {
        if (!$this->getConfigFlag('active')) {
            return false;
        }

        if (!Mage::registry('bseller_skyhub_process_order_creation')) {
            return false;
        }
    
        $amount     = 0;
        $methodCode = null;
        
        /** @var Mage_Sales_Model_Quote $quote */
        $quote = Mage::getSingleton('bseller_skyhub/adminhtml_session_quote')->getQuote();
        
        Mage::dispatchEvent(
            'bseller_skyhub_shipping_standard_collect_rates',
            array(
                'quote' => $quote
            )
        );
        
        if ($quote) {
            $amount         = (float)  $quote->getData('fixed_shipping_amount');
            $methodCode     = (string) $quote->getData('fixed_shipping_method_code');
            $methodTitle    = (string) $quote->getData('fixed_shipping_title');
        }
        
        /** @var Mage_Shipping_Model_Rate_Result $result */
        $result = Mage::getModel('shipping/rate_result');

        /** @var Mage_Shipping_Model_Rate_Result_Method $method */
        $method = Mage::getModel('shipping/rate_result_method');

        $method->setCarrier('bseller_skyhub');
        $method->setCarrierTitle($this->getConfigData('title'));

        $method->setMethod($this->getShippingMethod($methodCode));
        $method->setMethodTitle($methodTitle);

        $method->setPrice((float) $amount);
        $method->setCost(0.0000);
    
        Mage::dispatchEvent(
            'bseller_skyhub_shipping_standard_collect_rates_after',
            array(
                'quote'  => $quote,
                'method' => $method,
                'result' => $result,
            )
        );

        $result->append($method);

        return $result;
    }
    
    
    /**
     * @param string $method
     *
     * @return mixed
     */
    protected function getShippingMethod($method = null)
    {
        if (!$method) {
            return 'standard';
        }

        $method = $this->helper()->normalizeString($method);
        
        return $method;
    }
    
    
    /**
     * @param null|string $carrier
     *
     * @return null
     */
    protected function getShippingCarrierTitle($carrier = null)
    {
        if (!$carrier) {
            return $this->getConfigData('title');
        }
        
        return $carrier;
    }
    
    
    /**
     * @return BSeller_SkyHub_Helper_Data
     */
    protected function helper()
    {
        /** @var BSeller_SkyHub_Helper_Data $helper */
        $helper = Mage::helper('bseller_skyhub');
        return $helper;
    }
}
