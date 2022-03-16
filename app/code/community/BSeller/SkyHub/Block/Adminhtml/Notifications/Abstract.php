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

abstract class BSeller_SkyHub_Block_Adminhtml_Notifications_Abstract extends BSeller_Core_Block_Adminhtml_Template
{
    
    use BSeller_SkyHub_Trait_Store_Iterator;
    
    
    protected abstract function canShow();
    
    
    /**
     * @return string
     */
    protected function _toHtml()
    {
        if (!$this->canShow()) {
            return '';
        }
        
        return parent::_toHtml();
    }
}