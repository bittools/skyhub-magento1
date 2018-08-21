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
class BSeller_SkyHub_Block_Adminhtml_Shipment_Plp_View_Form
    extends BSeller_Core_Block_Adminhtml_Widget_Form
{

    use BSeller_SkyHub_Trait_Shipment_Plp;


    /**
     * Init form
     */
    public function __construct()
    {
        parent::__construct();
        $this->setId('block_form');
    }
    
    
    /**
     * @return $this
     */
    protected function _prepareForm()
    {
        $fileButtonParams = "'popUpWindow','height=400,width=600,left=10,top=10,,scrollbars=yes,menubar=no";

        /** @var Varien_Data_Form $form */
        $form = new Varien_Data_Form(
            [
                'id'        => 'view_form',
            ]
        );
    
        /** @var Varien_Data_Form_Element_Fieldset $fieldset */
        $fieldset = $form->addFieldset(
            'general',
            [
                'legend'    => $this->__('General Information')
            ]
        );

        $fieldset->addField(
            'created_at',
            'label',
            [
                'name'      => 'created_at',
                'label'     => $this->__('Creation Date'),
            ]
        );

        $fieldset->addField(
            'skyhub_code',
            'label',
            [
                'name'      => 'skyhub_code',
                'label'     => $this->__('PLP Code'),
            ]
        );

        $fieldset->addField(
            'pdf_file',
            'button',
            [
                'label'     => Mage::helper('core')->__('PLP File (PDF format)'),
                'value'     => Mage::helper('core')->__('Download PLP File (PDF format)'),
                'name'      => 'pdf_file',
                'class'     => 'form-button',
                'onclick'   => "window.setLocation('{$this->_getFileButtonLink('pdf')}'); return false;",
            ]
        );

        $fieldset->addField(
            'json_file',
            'button',
            [
                'label'     => Mage::helper('core')->__('PLP File (JSON format)'),
                'value'     => Mage::helper('core')->__('Download PLP File (JSON format)'),
                'name'      => 'json_file',
                'class'     => 'form-button',
                'onclick'   => "window.open('{$this->_getFileButtonLink('json')}',{$fileButtonParams}'); return false;",
            ]
        );

        /** @var Varien_Data_Form_Element_Fieldset $fieldset */
        $fieldset = $form->addFieldset(
            'orders',
            [
                'legend'    => $this->__('Orders Information')
            ]
        );

        $fieldset->addType(
            'order_grid',
            'BSeller_SkyHub_Block_Adminhtml_Shipment_Plp_View_Form_Renderer_Order_Grid'
        );


        $fieldset->addField(
            'orders_id',
            'order_grid',
            [
                'label'     => Mage::helper('core')->__('Related Orders'),
                'name'      => 'orders_id',
            ]
        );

        $form->addValues($this->_getPlpFormattedData($this->getCurrentPlp()));
        $form->setUseContainer(true);
        $this->setForm($form);
        
        return $this;
    }


    /**
     * @param string $format
     *
     * @return string
     */
    protected function _getFileButtonLink($format = 'json')
    {
        return $this->getUrl(
            '*/*/view'.ucfirst($format).'File',
            [
                'id'        => $this->getCurrentPlp()->getId(),
                'format'    => $format
            ]
        );
    }


    /**
     * @param   BSeller_SkyHub_Model_Shipment_Plp $plp
     *
     * @return  null|array
     */
    protected function _getPlpFormattedData($plp)
    {
        if (!$plp) {
            return null;
        }

        /** @var BSeller_SkyHub_Helper_Data $helper */
        $helper = Mage::helper('bseller_skyhub');

        $plp->setCreatedAt($helper->formatDate($plp->getCreatedAt()));
        $plp->setExpirationDate($helper->formatDateWithoutTimezone($plp->getExpirationDate()));

        return $plp->getData();
    }
}