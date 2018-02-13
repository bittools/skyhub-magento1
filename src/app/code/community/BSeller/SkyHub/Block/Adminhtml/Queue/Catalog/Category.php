<?php

class BSeller_SkyHub_Block_Adminhtml_Queue_Catalog_Category extends BSeller_SkyHub_Block_Adminhtml_Widget_Grid_Container
{

    protected $_controller = 'adminhtml_queue_catalog_category';


    public function __construct()
    {
        $this->_headerText = $this->__('Catalog Categories Queue');
        parent::__construct();
    }
}
