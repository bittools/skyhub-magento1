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

class BSeller_SkyHub_Adminhtml_Bseller_Skyhub_Shipment_PlpController extends BSeller_SkyHub_Controller_Admin_Action
{

    use BSeller_SkyHub_Trait_Integrators;
    use BSeller_SkyHub_Trait_Service;
    use BSeller_SkyHub_Trait_Integrators;


    const RESPONSE_TYPE_PDF = 'pdf';


    /**
     * @param string|null $actionTitle
     *
     * @return $this
     */
    protected function _init($actionTitle = null)
    {
        parent::init('Pre-post list (PLP)');

        if (!empty($actionTitle)) {
            $this->_title($this->__($actionTitle));
        }

        $this->_setActiveMenu('bseller/bseller_skyhub/plp');

        return $this;
    }
    
    
    /**
     * PLP list
     */
    public function indexAction()
    {
        $this->_init();
        $this->renderLayout();
    }


    /**
     * PLP create page
     */
    public function newAction()
    {
        $this->_init('Pre-post list (PLP) Creation');
        $this->renderLayout();
    }


    /**
     * Group 'n' orders in a PLP in SkyHub API
     */
    public function massGroupAction()
    {
        $skyhubOrderIds = (array) $this->getRequest()->getPost('skyhub_order_ids');

        if (empty($skyhubOrderIds)) {
            $this->_getSession()->addError($this->__('No PLP selected for PLP generation.'));
            $this->_redirect('*/*/index');
            return;
        }

        $result = $this->_groupOrdersInPlp($skyhubOrderIds);

        if (!$result) {
            $this->_getSession()->addError($this->__('There was a problem when trying to create the PLP.'));
            $this->_redirect('*/*');
            return;
        }

        $this->_getSession()->addSuccess($this->__('The PLP has been created.'));
        $this->_redirect('*/*/index');
    }


    /**
     * PLP detail
     */
    public function viewAction()
    {
        $id = $this->getRequest()->getParam('id', null);

        /** @var BSeller_SkyHub_Model_Shipment_Plp $plp */
        $plp = $this->_getPlp($id);

        if (!$plp->getId()) {
            $this->_getSession()->addError($this->__('This PLP does not exist anymore.'));
            $this->_redirect('*/*');
            return;
        }

        $this->_init('Pre-post list (PLP) Detail');
        $this->renderLayout();
    }


    /**
     * PLP PDF file detail
     */
    public function viewPdfFileAction()
    {
        $id = $this->getRequest()->getParam('id', null);

        /** @var BSeller_SkyHub_Model_Shipment_Plp $plp */
        $plp = $this->_getPlp($id);

        if (!$plp->getId()) {
            $this->_getSession()->addError($this->__('This PLP does not exist anymore.'));
            $this->_redirect('*/*');
            return;
        }

        Mage::register('bseller_skyhub_response_format', SELF::RESPONSE_TYPE_PDF, true);

        /** @var string $file */
        $file = $this->shipmentPlpIntegrator()->viewFile($plp);

        if (!$file) {
            $this->_getSession()->addError($this->__('An error occurred while getting PLP file.'));
            $this->_redirect('*/*/view', array('id' => $id));
            return;
        }

        return $this->_prepareDownloadResponse(
            'plp-'.$plp->getSkyhubCode().'.pdf', $file,
            'application/pdf'
        );
    }


    /**
     * PLP JSON file detail
     */
    public function viewJsonFileAction()
    {
        $id = $this->getRequest()->getParam('id', null);

        /** @var BSeller_SkyHub_Model_Shipment_Plp $plp */
        $plp = $this->_getPlp($id);

        if (!$plp->getId()) {
            $this->_getSession()->addError($this->__('This PLP does not exist anymore.'));
            $this->_redirect('*/*');
            return;
        }

        $this->_init('Pre-post list (PLP) File Detail');
        $this->renderLayout();
    }


    /**
     * PLP ungroup action
     */
    public function ungroupAction()
    {
        $id = $this->getRequest()->getParam('id', null);

        /** @var BSeller_SkyHub_Model_Shipment_Plp $plp */
        $plp = $this->_getPlp($id);

        if (!$plp->getId()) {
            $this->_getSession()->addError($this->__('This PLP does not exist anymore.'));
            $this->_redirect('*/*');
            return;
        }

        /** @var BSeller_SkyHub_Model_Integrator_Shipment_Plp $plpIntegrator */
        $plpIntegrator = $this->shipmentPlpIntegrator();

        $skyhubResult = $plpIntegrator->ungroup($plp->getSkyhubCode());

        if (!$skyhubResult) {
            $this->_getSession()->addError($this->__('There was a problem when trying to ungroup the PLP.'));
            $this->_redirect('*/*');
            return;
        }

        $result = $this->_deletePlp($id);

        if (!$result) {
            $this->_getSession()->addError($this->__('There was a problem when trying to ungroup the PLP in Magento.'));
            $this->_redirect('*/*');
            return;
        }

        $this->_getSession()->addSuccess($this->__('The PLP has been ungrouped.'));
        $this->_redirect('*/*/index');
    }


    /**
     * @param array $ids
     *
     * @return bool
     */
    protected function _groupOrdersInPlp($ids)
    {
        /** @var  $plpIntegrator */
        $plpIntegrator = $this->shipmentPlpIntegrator();

        $skyhubResult = $plpIntegrator->group($ids);

        if (!$skyhubResult) {
            return false;
        }

        $plpId = $this->_extractPlpId($skyhubResult['message']);

        if (!$plpId) {
            return false;
        }

        return $this->_savePlp($plpId, $ids);
    }


    /**
     * @todo get PLP data (expiration date)
     *
     * @param string    $skyhubId
     * @param array     $skyhubOrderIds
     *
     * @return bool
     */
    protected function _savePlp($skyhubId, $skyhubOrderIds)
    {
        try {

            /** @var BSeller_SkyHub_Model_Shipment_Plp $plp */
            $plp = Mage::getModel('bseller_skyhub/shipment_plp');
            $plp->setSkyhubCode($skyhubId);

            foreach ($skyhubOrderIds as $order) {
                /** @var BSeller_SkyHub_Model_Shipment_Plp_Order $plpOrder */
                $plpOrder = Mage::getModel('bseller_skyhub/shipment_plp_order');
                $plpOrder->setSkyhubOrderCode($order);
                $plp->addOrder($plpOrder);
            }

            $plp->save();

        } catch (Mage_Exception $e) {
            Mage::logException($e);
            return false;
        }

        return true;
    }


    /**
     * @param int $id
     *
     * @return bool
     */
    protected function _deletePlp($id)
    {
        try {

            /** @var BSeller_SkyHub_Model_Shipment_Plp $plp */
            $plp = Mage::getModel('bseller_skyhub/shipment_plp')->load((int) $id);

            if (!$plp) {
                Mage::throwException($this->__('There was a problem when trying to ungroup the PLP.'));
            }

            $plp->delete();

        } catch (Mage_Exception $e) {
            Mage::logException($e);
            return false;
        }

        return true;
    }


    /**
     * Extract PLP ID from API result message
     *
     * @param string $message
     *
     * @return int|null
     */
    protected function _extractPlpId($message)
    {
        $pieces = explode(' ', $message);

        if (count($pieces) == 0 || (int)$pieces[1] == 0) {
            return null;
        }

        return (int)$pieces[1];
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
