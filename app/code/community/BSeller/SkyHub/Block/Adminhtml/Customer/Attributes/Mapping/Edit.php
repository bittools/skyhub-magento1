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
class BSeller_SkyHub_Block_Adminhtml_Customer_Attributes_Mapping_Edit
    extends BSeller_Core_Block_Adminhtml_Widget_Form_Container
{
    
    protected $_blockGroup = 'bseller_skyhub';
    protected $_controller = 'adminhtml_customer_attributes_mapping';
    protected $_mode       = 'edit';
    protected $_objectId   = 'id';
    
    
    /**
     * BSeller_SkyHub_Block_Adminhtml_Customer_Attributes_Mapping_Edit constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->removeButton('delete');
    }
    
    
    /**
     * @return string
     */
    public function getHeaderText()
    {
        return $this->__('Edit Customer Attribute Mapping');
    }
    
    
    /**
     * @return string
     */
    public function getFormActionUrl()
    {
        return $this->getUrl('*/*/save');
    }
}
