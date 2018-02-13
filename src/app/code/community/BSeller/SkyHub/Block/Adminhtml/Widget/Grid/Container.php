<?php

class BSeller_SkyHub_Block_Adminhtml_Widget_Grid_Container extends BSeller_Core_Block_Adminhtml_Widget_Grid_Container
{

    protected $_blockGroup = 'bseller_skyhub';


    public function __construct()
    {
        parent::__construct();
        $this->removeButton('add');
    }
}
