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

class BSeller_SkyHub_Adminhtml_Bseller_Skyhub_Customer_Attributes_MappingController
    extends BSeller_SkyHub_Controller_Admin_Action
{

    use BSeller_SkyHub_Trait_Customer_Attribute,
        BSeller_SkyHub_Trait_Customer_Attribute_Mapping,
        BSeller_SkyHub_Trait_Config;

    
    /**
     * @return $this
     */
    protected function init($actionTitle = null)
    {
        parent::init('Customer Attributes');
    
        if (!empty($actionTitle)) {
            $this->_title($this->__($actionTitle));
        }
        
        return $this;
    }
    
    
    /**
     * Attributes Mapping Grid Action.
     */
    public function indexAction()
    {
        $this->init('Attributes Mapping');
        
        $this->_setActiveMenu('bseller/bseller_skyhub/customer_attributes_mapping/grid');
        
        $this->renderLayout();
    }
    
    
    /**
     * Attributes Mapping Edit Action.
     */
    public function editAction()
    {
        $this->init('Attributes Mapping Edit');
    
        $id = $this->getRequest()->getParam('id', null);
    
        /** @var BSeller_SkyHub_Model_Customer_Attributes_Mapping $mapping */
        $mapping = $this->getMapping($id);
        
        if (!$mapping->getId()) {
            $this->_getSession()->addError($this->__('This mapping does not exist anymore.'));
            $this->_redirect('*/*');
            return;
        }

        if (!$mapping->getEditable()) {
            $this->_getSession()->addError($this->__('This attribute is not editable.'));
            $this->_redirect('*/*');
            return;
        }
        
        $this->_setActiveMenu('bseller/bseller_skyhub/customer_attributes_mapping/grid');
    
        $this->renderLayout();
    }
    
    
    public function saveAction()
    {
        $id          = (int) $this->getRequest()->getPost('id');
        $attributeId = (int) $this->getRequest()->getPost('attribute_id');
    
        /**
         * @var BSeller_SkyHub_Model_Customer_Attributes_Mapping $mapping
         * @var Mage_Eav_Model_Entity_Attribute                         $attribute
         */
        $mapping   = $this->getMapping($id);
        $attribute = $this->getEntityAttribute($attributeId);
        
        if (!$mapping->getId() || !$attribute->getId()) {
            $this->_redirect('*/*/');
            return;
        }
        
        $mapping->setAttributeId($attribute->getId());
        $mapping->save();

        /**
         * if the attribute has options
         */
        if ($mapping->getHasOptions()) {
            $attributesMappingOptions = $this->getRequest()->getPost('attributes_mapping_options');

            foreach ($attributesMappingOptions as $skyhubCode => $magentoOptionValue) {
                $mapping->updateOption($skyhubCode, $magentoOptionValue);
            }
        }
        
        $this->_getSession()
             ->addSuccess($this->__(
                 'SkyHub Attribute `%s` successfully linked to Magento attribute `%s`.',
                 $mapping->getSkyhubCode(),
                 $attribute->getAttributeCode()
             ));
        
        $this->_redirect('*/*');
    }


    public function createAutomaticallyAction()
    {
        $id = (int) $this->getRequest()->getParam('id');

        /** @var BSeller_SkyHub_Model_Customer_Attributes_Mapping $mapping */
        $mapping = $this->getMapping($id);

        if (!$mapping->getId()) {
            $this->redirectToAttributeMapping();
            return;
        }

        if ($attributeId = $this->loadCustomerAttribute($mapping->getSkyhubCode())->getId()) {
            $mapping->setAttributeId((int) $attributeId)
                ->save();

            $this->_getSession()
                ->addNotice($this->__('There was already an attribute with the code "%s".', $mapping->getSkyhubCode()))
                ->addSuccess($this->__('The attribute was only mapped automatically.'));

            $this->redirectToAttributeMappingEdit($mapping->getId());
            return;
        }

        $config = [
            'label'           => $mapping->getSkyhubLabel(),
            'type'            => 'varchar',
            'input'           => 'text',
            'required'        => 0,
            'visible'         => 0,
            'visible_on_front'=> 0,
            'filterable'      => 0,
            'searchable'      => 0,
            'comparable'      => 0,
            'user_defined'    => 1,
            'is_configurable' => 0,
            'global'          => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
            'note'            => sprintf(
                '%s. %s.',
                $this->__('Created automatically by BSeller SkyHub module.'),
                $this->__($mapping->getSkyhubDescription())
            ),
        ];

        $installConfig = (array) $mapping->getAttributeInstallConfig();

        foreach ($installConfig as $configKey => $itemValue) {
            if (is_null($itemValue)) {
                continue;
            }

            $config[$configKey] = $itemValue;
        }

        /** @var Mage_Eav_Model_Entity_Attribute $attribute */
        $attribute = $this->createCustomerAttribute($mapping->getSkyhubCode(), (array) $config);

        if (!$attribute || !$attribute->getId()) {
            $this->_getSession()->addError('There was a problem when trying to create the attribute.');
            $this->redirectToAttributeMapping();
            return;
        }

        try {
            $mapping->setAttributeId((int) $attribute->getId())
                ->save();

            if ($mapping->getHasOptions()) {
                $customerAttributesXml = Mage::getSingleton('bseller_skyhub/config_customer')->getSkyHubFixedAttributes();
                $attributeConfig = $this->arrayExtract($customerAttributesXml, $mapping->getData('skyhub_code'), false);
                $options = $this->arrayExtract($attributeConfig, 'options', false);
                foreach ($options as $option) {
                    $mapping->updateOption($option['skyhub_code'], $option['default_value']);
                }
            }

            $message = $this->__(
                'The attribute "%s" was created in Magento and associated to SkyHub attribute "%s" automatically.',
                $attribute->getAttributeCode(),
                $mapping->getSkyhubCode()
            );

            $this->_getSession()->addSuccess($message);
            $this->redirectToAttributeMapping();
        } catch (Exception $e) {
            Mage::logException($e);

            $this->_getSession()->addError('There was a problem when trying to map the attribute.');
            $this->redirectToAttributeMapping();
        }
    }

    public function loadAttributeOptionsAction()
    {
        $html = $this->getLayout()
            ->createBlock('bseller_skyhub/adminhtml_customer_attributes_mapping_edit_form_options')
            ->setRequestData(Mage::app()->getRequest()->getParams())
            ->toHtml();

        $this->getResponse()->setHeader('Content-type', 'text/html');
        $this->getResponse()->setBody($html);
    }


    /**
     * @return void
     */
    protected function redirectToAttributeMapping()
    {
        $this->_redirect('*/*');
    }


    /**
     * @param int $id
     *
     * @return void
     */
    protected function redirectToAttributeMappingEdit($id)
    {
        $this->_redirect('*/*/edit', ['id' => (int) $id]);
    }
    
    
    /**
     * @param int $id
     *
     * @return BSeller_SkyHub_Model_Customer_Attributes_Mapping
     *
     * @throws Mage_Core_Exception
     */
    protected function getMapping($id)
    {
        /** @var BSeller_SkyHub_Model_Customer_Attributes_Mapping $mapping */
        $mapping = Mage::getModel('bseller_skyhub/customer_attributes_mapping');
        $mapping->load((int) $id);
        
        Mage::register('current_attributes_mapping', $mapping, true);
        
        return $mapping;
    }
    
    
    /**
     * @param int $attributeId
     *
     * @return Mage_Eav_Model_Entity_Attribute
     */
    protected function getEntityAttribute($attributeId)
    {
        /** @var Mage_Eav_Model_Entity_Attribute $attribute */
        $attribute = Mage::getModel('eav/entity_attribute')->load((int) $attributeId);
        return $attribute;
    }

}
