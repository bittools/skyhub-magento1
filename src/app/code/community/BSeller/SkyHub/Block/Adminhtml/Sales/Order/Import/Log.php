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

class BSeller_SkyHub_Block_Adminhtml_Sales_Order_Import_Log
    extends BSeller_SkyHub_Block_Adminhtml_Widget_Grid_Container
{
    
    protected $_controller = 'adminhtml_sales_order_import_log';
    
    
    public function __construct()
    {
        $this->_headerText = $this->__('Order Import Errors');
        parent::__construct();
    }
}
