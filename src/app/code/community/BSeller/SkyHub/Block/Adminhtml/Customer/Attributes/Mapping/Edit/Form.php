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
 * @author    Julio Reis <julio.reis@e-smart.com.br>
 */
class BSeller_SkyHub_Block_Adminhtml_Customer_Attributes_Mapping_Edit_Form
    extends BSeller_Core_Block_Adminhtml_Widget_Form
{
    
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
        /** @var Varien_Data_Form $form */
        $form = new Varien_Data_Form(
            array(
                'id'     => 'edit_form',
                'action' => $this->getData('action'),
                'method' => 'post'
            )
        );
    
        /** @var Varien_Data_Form_Element_Fieldset $fieldset */
        $fieldset = $form->addFieldset(
            'general',
            array(
                'legend' => $this->__('General Information')
            )
        );

        $fieldset->addField(
            'id',
            'hidden',
            array(
                'name' => 'id',
            )
        );

        $fieldset->addField(
            'skyhub_code',
            'label',
            array(
                'name' => 'skyhub_code',
                'label' => $this->__('SkyHub Code'),
            )
        );

        $fieldset->addField(
            'skyhub_label',
            'label',
            array(
                'name' => 'skyhub_label',
                'label' => $this->__('SkyHub Label'),
            )
        );

        $fieldset->addField(
            'skyhub_description',
            'label',
            array(
                'name' => 'skyhub_description',
                'label' => $this->__('SkyHub Description'),
            )
        );
        
        /** @var BSeller_SkyHub_Model_System_Config_Source_Customer_Attributes $attributesSource */
        $attributesSource = Mage::getModel('bseller_skyhub/system_config_source_customer_attributes');

        $mappingAttributeId = $this->getMapping()->getData('id');
        $magentoAttributeId = $this->getMapping()->getData('attribute_id');
        $url = Mage::getUrl('*/*/loadAttributeOptions');

        $scriptToLoad = '';
        if ($magentoAttributeId) {
            $scriptToLoad = "<script>renderAttributeOptions($magentoAttributeId , '"
                . $mappingAttributeId
                . "', 'options_container' , '"
                . $url . "')</script>";
        }
        $fieldset->addField(
            'attribute_id',
            'select',
            array(
                'name' => 'attribute_id',
                'label' => $this->__('Related Attribute'),
                'required' => true,
                'options' => $attributesSource->toArray(true),
                'onchange' => "renderAttributeOptions(this.value, '" . $mappingAttributeId . "', 'options_container' , '" . $url . "')"
            )
        )->setAfterElementHtml(
            "
                <div id=\"options_container\"></div>
                {$scriptToLoad}
            "
        );
    
        $form->setValues($this->getMapping()->getData());
        $form->setUseContainer(true);
        $this->setForm($form);
        
        return $this;
    }
    
    
    /**
     * @return BSeller_SkyHub_Model_Customer_Attributes_Mapping
     */
    protected function getMapping()
    {
        /** @var BSeller_SkyHub_Model_Customer_Attributes_Mapping $mapping */
        $mapping = Mage::registry('current_attributes_mapping');
        
        if (!$mapping) {
            $mapping = Mage::getModel('bseller_skyhub/customer_attributes_mapping');
        }
        
        return $mapping;
    }
}
