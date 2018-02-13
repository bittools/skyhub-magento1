<?php

class BSeller_SkyHub_Controller_Admin_Action extends BSeller_Core_Controller_Adminhtml_Action
{

    use BSeller_SkyHub_Trait_Config;

    
    /**
     * @return $this
     */
    protected function init()
    {
        $this->loadLayout();
        $this->_title($this->__('BSeller SkyHub'));
        
        return $this;
    }
    
}
