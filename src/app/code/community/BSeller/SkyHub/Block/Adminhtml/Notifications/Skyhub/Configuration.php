<?php

/**
 * Class BSeller_SkyHub_Block_Adminhtml_Notifications_Skyhub_Configuration
 */
class BSeller_SkyHub_Block_Adminhtml_Notifications_Skyhub_Configuration extends BSeller_Core_Block_Adminhtml_Template
{

    use BSeller_SkyHub_Trait_Config;


    protected function _construct()
    {
        $this->setTemplate('bseller/skyhub/notifications/skyhub/configuration.phtml');
        parent::_construct();
    }


    /**
     * @return bool
     */
    public function canShow()
    {
        return (bool) !$this->isConfigurationOk();
    }


    /**
     * @return string
     */
    public function getConfigurationUrl()
    {
        return $this->getUrl('adminhtml/system_config/edit/section/bseller_skyhub');
    }
}
