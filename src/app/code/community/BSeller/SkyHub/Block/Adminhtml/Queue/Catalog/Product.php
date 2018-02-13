<?php

class BSeller_SkyHub_Block_Adminhtml_Queue_Catalog_Product extends BSeller_SkyHub_Block_Adminhtml_Widget_Grid_Container
{

    protected $_controller = 'adminhtml_queue_catalog_product';


    public function __construct()
    {
        $this->_headerText = $this->__('Catalog Products Queue');
        parent::__construct();
    }
}
