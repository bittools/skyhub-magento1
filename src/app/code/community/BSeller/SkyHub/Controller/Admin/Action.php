<?php

class BSeller_SkyHub_Controller_Admin_Action extends BSeller_Core_Controller_Adminhtml_Action
{

    use BSeller_SkyHub_Trait_Data,
        BSeller_SkyHub_Trait_Config;


    /**
     * @param null|string $actionTitle
     *
     * @return $this
     */
    protected function init($actionTitle = null)
    {
        $this->loadLayout();
        $this->_title($this->__('BSeller SkyHub'));

        if (!empty($actionTitle)) {
            $this->_title($this->__($actionTitle));
        }

        return $this;
    }
    
}
