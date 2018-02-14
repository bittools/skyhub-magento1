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
 * @author    Tiago Sampaio <tiago.sampaio@e-smart.com.br>
 */

class BSeller_SkyHub_Adminhtml_Bseller_Skyhub_Catalog_Product_Attributes_MappingController
    extends BSeller_SkyHub_Controller_Admin_Action
{

    use BSeller_SkyHub_Trait_Catalog_Product_Attribute,
        BSeller_SkyHub_Trait_Config;

    
    /**
     * @return $this
     */
    protected function init($actionTitle = null)
    {
        parent::init('Product Attributes');
    
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
        
        $this->_setActiveMenu('bseller/bseller_skyhub/catalog_product_attributes_mapping/grid');
        
        $this->renderLayout();
    }
    
    
    /**
     * Attributes Mapping Edit Action.
     */
    public function editAction()
    {
        $this->init('Attributes Mapping Edit');
    
        $id = $this->getRequest()->getParam('id', null);
    
        /** @var BSeller_SkyHub_Model_Catalog_Product_Attributes_Mapping $mapping */
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
        
        $this->_setActiveMenu('bseller/bseller_skyhub/catalog_product_attributes_mapping/grid');
    
        $this->renderLayout();
    }
    
    
    public function saveAction()
    {
        $id          = (int) $this->getRequest()->getPost('id');
        $attributeId = (int) $this->getRequest()->getPost('attribute_id');
    
        /**
         * @var BSeller_SkyHub_Model_Catalog_Product_Attributes_Mapping $mapping
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

        /** @var BSeller_SkyHub_Model_Catalog_Product_Attributes_Mapping $mapping */
        $mapping = $this->getMapping($id);

        if (!$mapping->getId()) {
            $this->redirectToAttributeMapping();
            return;
        }

        if ($attributeId = $this->loadProductAttribute($mapping->getSkyhubCode())->getId()) {
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
            'visible_on_front'=> 0,
            'filterable'      => 0,
            'searchable'      => 0,
            'comparable'      => 0,
            'user_defined'    => 1,
            'is_configurable' => 0,
            'global'          => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
            'note'            => sprintf(
                '%s. %s.',
                'Created automatically by BSeller SkyHub module.',
                $mapping->getSkyhubDescription()
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
        $attribute = $this->createProductAttribute($mapping->getSkyhubCode(), (array) $config);

        if (!$attribute || !$attribute->getId()) {
            $this->_getSession()->addError('There was a problem when trying to create the attribute.');
            $this->redirectToAttributeMapping();
            return;
        }

        try {
            $mapping->setAttributeId((int) $attribute->getId())
                ->save();

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
     * @return BSeller_SkyHub_Model_Catalog_Product_Attributes_Mapping
     *
     * @throws Mage_Core_Exception
     */
    protected function getMapping($id)
    {
        /** @var BSeller_SkyHub_Model_Catalog_Product_Attributes_Mapping $mapping */
        $mapping = Mage::getModel('bseller_skyhub/catalog_product_attributes_mapping');
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
