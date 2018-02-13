<?php

class BSeller_SkyHub_Block_Adminhtml_Queue_Catalog_Product_Attribute
    extends BSeller_SkyHub_Block_Adminhtml_Widget_Grid_Container
{

    protected $_controller = 'adminhtml_queue_catalog_product_attribute';


    public function __construct()
    {
        $this->_headerText = $this->__('Product Attributes Queue');
        parent::__construct();
    }
}
