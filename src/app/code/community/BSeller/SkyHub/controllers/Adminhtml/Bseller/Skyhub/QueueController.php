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
class BSeller_SkyHub_Adminhtml_Bseller_Skyhub_QueueController extends BSeller_SkyHub_Controller_Admin_Action
{

    /**
     * @param string|null $actionTitle
     *
     * @return $this
     */
    protected function init($actionTitle = null)
    {
        parent::init('Queues Tracking');

        if (!empty($actionTitle)) {
            $this->_title($this->__($actionTitle));
        }

        return $this;
    }
    
    
    /**
     * Catalog Products Queue
     */
    public function productsQueueAction()
    {
        $this->init('Catalog Products Queue');
        $this->_setActiveMenu('bseller/bseller_skyhub/queues/products_queue');
        
        $this->renderLayout();
    }


    /**
     * Catalog Categories Queue
     */
    public function categoriesQueueAction()
    {
        $this->init('Catalog Categories Queue');
        $this->_setActiveMenu('bseller/bseller_skyhub/queues/categories_queue');

        $this->renderLayout();
    }


    /**
     * Catalog Product Attributes Queue
     */
    public function productAttributesQueueAction()
    {
        $this->init('Catalog Product Attributes Queue');
        $this->_setActiveMenu('bseller/bseller_skyhub/queues/product_attributes_queue');

        $this->renderLayout();
    }

}
