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
 * @author    Bruno Gemelli <bruno.gemelli@e-smart.com.br>
 */
class BSeller_SkyHub_Block_Adminhtml_Sales_Plp_View
    extends BSeller_Core_Block_Adminhtml_Widget_Form_Container
{
    
    protected $_blockGroup = 'bseller_skyhub';
    protected $_controller = 'adminhtml_sales_plp';
    protected $_mode       = 'view';
    protected $_objectId   = 'id';
    
    
    public function __construct()
    {
        parent::__construct();
        $this->removeButton('save');
        $this->removeButton('reset');
        $this->removeButton('delete');
    }
    
    
    /**
     * @return string
     */
    public function getHeaderText()
    {
        return $this->__('Pre-post list (PLP) Detail');
    }

}
