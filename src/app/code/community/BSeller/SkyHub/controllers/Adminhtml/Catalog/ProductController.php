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

class BSeller_SkyHub_Adminhtml_Catalog_ProductController extends BSeller_SkyHub_Controller_Admin_Action
{
    
    use BSeller_SkyHub_Trait_Integrators,
        BSeller_SkyHub_Model_Integrator_Catalog_Product_Validation;
    
    
    /**
     * This method processes the product integration for each available store in Magento.
     */
    public function integrateAction()
    {
        $productId = (int) $this->getRequest()->getParam('product_id');
        
        $this->processStoreIteration($this, 'integrateProduct', $productId);
        
        $proceed = Mage::registry('result_redirect');
        
        if ($proceed && is_callable($proceed)) {
            $proceed();
            return;
        }
        
        $this->redirectProductList();
    }

    /**
     * Check for is allowed
     *
     * @return boolean
     */
    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('catalog/products');
    }

    /**
     * @param int                   $productId
     * @param Mage_Core_Model_Store $store
     *
     * @throws Mage_Core_Exception
     * @throws Varien_Exception
     */
    public function integrateProduct($productId, Mage_Core_Model_Store $store)
    {
        if (!$this->isModuleEnabled()) {
            $this->resultRedirect($productId, 'edit');
            return;
        }
    
        /** @var Mage_Catalog_Model_Product $product */
        $product = Mage::getModel('bseller_skyhub/catalog_product');
        $product->setStoreId($store->getId());
        $product->load($productId);
    
        if (!$this->canIntegrateProduct($product)) {
            $this->_getSession()
                 ->addNotice($this->__('This product cannot be integrated for store %s.', $store->getCode()));
            $this->resultRedirect($productId, 'edit');
            return;
        }
    
        /** @var \SkyHub\Api\Handler\Response\HandlerInterface $response */
        $response = $this->catalogProductIntegrator()->createOrUpdate($product);
    
        /**
         * After the product to be integrated, we show the information.
         */
        if ($response && $response->success()) {
            $this->_getSession()
                 ->addSuccess($this->__('The product was successfully integrated in store %s.', $store->getCode()));
        }
    
        if ($response && $response->exception()) {
            $message = $this->__(
                'There was a problem when trying to integrate the product in store %s.',
                $store->getCode()
            );
            
            $this->_getSession()->addError($message);
        }
    
        $this->resultRedirect($productId, 'edit');
    }
    
    
    /**
     * @param null $flag
     *
     * @return $this|mixed
     *
     * @throws Mage_Core_Exception
     */
    protected function resultRedirect($productId, $redirect = null)
    {
        switch ($redirect) {
            case 'edit':
                $result = function () use ($productId) {
                    $this->_redirect(
                        'adminhtml/catalog_product/edit',
                        array('id' => (int) $productId)
                    );
                };
                break;
            default:
                $result = function () {
                    $this->_redirect('adminhtml/catalog_product');
                };
        }
        
        $key = 'result_redirect';
        
        Mage::register($key, $result, true);
        
        return $this;
    }


    /**
     * @param $productId
     *
     * @return void
     */
    protected function redirectProductEdit($productId)
    {
        $this->_redirect(
            'adminhtml/catalog_product/edit',
            array('id' => (int) $productId)
        );
        return;
    }
    
    
    /**
     * @return void
     */
    protected function redirectProductList()
    {
        $this->_redirect('adminhtml/catalog_product');
        return;
    }
}
