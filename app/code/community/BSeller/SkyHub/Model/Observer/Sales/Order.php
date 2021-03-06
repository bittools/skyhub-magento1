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
class BSeller_SkyHub_Model_Observer_Sales_Order extends BSeller_SkyHub_Model_Observer_Sales_Abstract
{
    use BSeller_SkyHub_Model_Integrator_Catalog_Product_Validation;
    use BSeller_SkyHub_Trait_Queue;
    use BSeller_SkyHub_Trait_Entity;

    const RULE_REGISTRY_KEY = 'rule_to_registry';

    /**
     * @param Varien_Event_Observer $observer
     *
     * @throws Mage_Core_Model_Store_Exception
     */
    public function logOrderDetails(Varien_Event_Observer $observer)
    {
        if (true === Mage::registry('disable_order_log')) {
            return;
        }

        /**
         * @var Exception $exception
         * @var array $orderData
         */
        $exception = $observer->getData('exception');
        $orderData = (array)$observer->getData('order_data');

        if (!$exception || !$orderData) {
            return;
        }

        $orderCode = $this->arrayExtract($orderData, 'code');

        $data = array(
            'entity_id' => null,
            'reference' => (string)$orderCode,
            'entity_type' => BSeller_SkyHub_Model_Entity::TYPE_SALES_ORDER,
            'status' => BSeller_SkyHub_Model_Queue::STATUS_FAIL,
            'process_type' => BSeller_SkyHub_Model_Queue::PROCESS_TYPE_IMPORT,
            'messages' => $exception->getMessage(),
            'additional_data' => json_encode($orderData),
            'can_process' => false,
            'store_id' => (int)$this->getStoreId(),
        );

        /** @var BSeller_SkyHub_Model_Queue $queue */
        $queue = Mage::getModel('bseller_skyhub/queue');
        $queue->setData($data);
        $queue->save();
    }


    /**
     * @param Varien_Event_Observer $observer
     */
    public function cancelOrderAfter(Varien_Event_Observer $observer)
    {
        if ($this->isRunningQueueProcess()) {
            return;
        }

        /** @var Mage_Sales_Model_Order $order */
        $order = $observer->getData('order');

        if (!$order || !$order->getId()) {
            return;
        }
        $this->getStoreIterator()->call($this->orderIntegrator(), 'cancel', array($order->getId()), $order->getStore());
    }

    /**
     * @param Varien_Event_Observer $observer
     */
    public function reintegrateOrderProducts(Varien_Event_Observer $observer)
    {
        /** @var Mage_Sales_Model_Order $order */
        $order = $observer->getData('order');

        if (!$order || !$order->getId()) {
            return;
        }

        $products = $order->getAllVisibleItems();

        foreach ($products as $item) {
            $this->processReintegrationOrderProducts($item->getProduct(), $order->getStore());
        }
    }

    /**
     * @param $product
     * @param Mage_Core_Model_Store|null $store
     */
    protected function processReintegrationOrderProducts($product, Mage_Core_Model_Store $store = null)
    {
        $parentIds = Mage::getModel('catalog/product_type_configurable')->getParentIdsByChild($product->getId());
        foreach ($parentIds as $id) {
            $this->processReintegrationOrderProducts(Mage::getModel('catalog/product')->load($id), $store);
        }

        if (!$this->canIntegrateProduct($product, false, $store)) {
            return;
        }

        $hasActiveIntegrateProductsOnOrderPlaceFlag = $this->hasActiveIntegrateProductsOnOrderPlaceFlag();
        if ($hasActiveIntegrateProductsOnOrderPlaceFlag) {
            /**
             * integrate all order items on skyhub (mainly to update stock qty)
             */
            $response = $this->catalogProductIntegrator()->createOrUpdate($product, $store);

            if ($response && $response->success()) {
                return;
            }
        }

        $queueResource = $this->getQueueResource();
        /**
         * put the product on the line
         */
        $queueResource->queue(
            array(
                $product->getId()
            ),
            BSeller_SkyHub_Model_Entity::TYPE_CATALOG_PRODUCT,
            BSeller_SkyHub_Model_Queue::PROCESS_TYPE_EXPORT
        );
    }

    /**
     * @param Varien_Event_Observer $observer
     *
     * @throws Mage_Core_Exception
     * @return $this
     */
    public function removePromotions(Varien_Event_Observer $observer)
    {
        $quote = $observer->getQuote();
        if (!$quote->getData('bseller_skyhub')) {
            return;
        }

        $item = $observer->getItem();

        $result = $observer->getResult();
        $rule = $observer->getRule();
        $result->setDiscountAmount(0);
        $result->setBaseDiscountAmount(0);
        $item->setDiscountPercent(0);
        $item->setAppliedRuleIds(null);
        $this->_registerRuleToRemove($item, $rule);
        return $this;
    }

    /**
     * @param Mage_Sales_Model_Quote_Item $item
     * @param Mage_SalesRule_Model_Rule $rule
     *
     * @throws Mage_Core_Exception
     * @return $this
     */
    protected function _registerRuleToRemove(Mage_Sales_Model_Quote_Item $item, Mage_SalesRule_Model_Rule $rule)
    {
        $registry = Mage::registry(self::RULE_REGISTRY_KEY);

        Mage::unregister(self::RULE_REGISTRY_KEY);

        $registry[$item->getId()][] = $rule->getId();

        Mage::register(self::RULE_REGISTRY_KEY, $registry, true);

        return $this;
    }

    /**
     * @param Varien_Event_Observer $observer
     * @return $this
     */
    public function cleanRuleIds(Varien_Event_Observer $observer)
    {
        $quote = $observer->getQuote();
        if (!$quote->getData('bseller_skyhub') || !$quote->getAppliedRuleIds()) {
            return;
        }

        $this->removeRulesFromItem($quote);
        return $this;
    }

    /**
     * @param Mage_Sales_Model_Quote $quote
     * @return $this
     */
    public function removeRulesFromItem(Mage_Sales_Model_Quote $quote)
    {
        $registry = Mage::registry(self::RULE_REGISTRY_KEY);

        /** @var Mage_Sales_Model_Quote_Item $item */
        foreach ($quote->getAllItems() as $item) {
            if (isset($registry[$item->getId()])) {
                $rules = explode(',', $item->getAppliedRuleIds());
                foreach ($registry[$item->getId()] as $ruleId) {
                    $key = array_search($ruleId, $rules);

                    if ($key !== false) {
                        unset($rules[$key]);
                    }
                }

                $item->setAppliedRuleIds(implode(',', $rules));
            }
        }

        return $this;
    }

    /**
     * @return bool
     */
    protected function isRunningQueueProcess()
    {
        return BSeller_SkyHub_Model_Cron_Queue_Sales_Order_Queue::isRunning();
    }
}
