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
    
    use BSeller_SkyHub_Trait_Integrators;
    
    
    public function integrateAction()
    {
        $productId = (int) $this->getRequest()->getParam('product_id');
        
        /** @var Mage_Catalog_Model_Product $product */
        $product = Mage::getModel('catalog/product')->load($productId);
        
        if (!$product->getId()) {
            $this->redirectProductList();
            return;
        }

        $response = $this->catalogProductIntegrator()->createOrUpdate($product);
    
        /**
         * After the product to be integrated, we show the information.
         */
        if ($response->success()) {
            $this->_getSession()->addSuccess($this->__('The product was successfully integrated.'));
        }
        
        if ($response->exception()) {
            $this->_getSession()->addError($this->__('There was a problem when trying to integrate the product.'));
        }
        
        $this->redirectProductEdit($product->getId());
    }


    /**
     * @param $productId
     *
     * @return void
     */
    protected function redirectProductEdit($productId)
    {
        $this->_redirect('adminhtml/catalog_product/edit', ['id' => (int) $productId]);
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
