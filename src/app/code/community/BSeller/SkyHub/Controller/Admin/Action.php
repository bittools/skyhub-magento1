<?php

class BSeller_SkyHub_Controller_Admin_Action extends Mage_Adminhtml_Controller_Action
{
    
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
