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
 * @author    Bruno Gemelli <bruno.gemelli@e-smart.com.br>
 */

class BSeller_SkyHub_Controller_Admin_Shipment_Plp_Action extends BSeller_SkyHub_Controller_Admin_Action
{

    /**
     * @param $id
     *
     * @return bool
     */
    protected function _validatePlp($id)
    {
        /** @var BSeller_SkyHub_Model_Shipment_Plp $plp */
        $plp = $this->_getPlp($id);

        if (!$plp->getId()) {
            $this->_getSession()->addError($this->__('This PLP does not exist anymore.'));
            $this->_redirect('*/*');
            return false;
        }

        return true;
    }


    /**
     * @param int $id
     *
     * @return BSeller_SkyHub_Model_Shipment_Plp
     */
    protected function _getPlp($id)
    {
        /** @var BSeller_SkyHub_Model_Shipment_Plp $plp */
        $plp = Mage::getModel('bseller_skyhub/shipment_plp');
        $plp->load((int) $id);

        Mage::register('current_plp', $plp, true);

        return $plp;
    }
}