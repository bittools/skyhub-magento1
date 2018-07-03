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
class BSeller_SkyHub_Block_Adminhtml_Catalog_Product_Attributes_Mapping_Grid
    extends BSeller_Core_Block_Adminhtml_Widget_Grid
{
    
    
    /**
     * @return Mage_Adminhtml_Block_Widget_Grid
     */
    protected function _prepareCollection()
    {
        /** @var BSeller_SkyHub_Model_Resource_Catalog_Product_Attributes_Mapping_Collection $collection */
        $collection = Mage::getResourceModel('bseller_skyhub/catalog_product_attributes_mapping_collection');
        $collection->getSelect()
            ->joinLeft(
                array(
                    'ea' => Mage::getSingleton('core/resource')->getTableName('eav/attribute')
                ),
                'main_table.attribute_id = ea.attribute_id',
                'attribute_code'
            );
        
        $this->setCollection($collection);
        
        return parent::_prepareCollection();
    }
    
    
    /**
     * @return $this
     *
     * @throws Exception
     */
    protected function _prepareColumns()
    {
        $this->addColumn('type', array(
            'header'    => $this->__('Type'),
            'align'     => 'left',
            'width'     => '50px',
            'type'      => 'options',
            'options'   => Mage::getModel('bseller_skyhub/system_config_source_data_types')->toArray(),
        ));
        
        $this->addColumn('skyhub_code', array(
            'header'           => $this->__('SkyHub Code'),
            'width'            => '150px',
            'align'            => 'left',
            'column_css_class' => 'skyhub-code',
        ));
    
        $this->addColumn('attribute_code', array(
            'header'           => $this->__('Magento Attribute Code'),
            'width'            => '150px',
            'align'            => 'left',
            'column_css_class' => 'magento-code',
        ));
        
        $this->addColumn('skyhub_label', array(
            'header'    => $this->__('SkyHub Label'),
            'width'     => '200px',
            'align'     => 'left',
        ));
        
        $this->addColumn('skyhub_description', array(
            'header'    => $this->__('SkyHub Description'),
            'align'     => 'left',
        ));
        
        return parent::_prepareColumns();
    }
    
    
    /**
     * @param BSeller_SkyHub_Model_Catalog_Product_Attributes_Mapping $mapping
     *
     * @return string|bool
     */
    public function getRowUrl($mapping)
    {
        if ($mapping->getEditable()) {
            return $this->getUrl(
                '*/*/edit',
                array('id' => $mapping->getId())
            );
        }
        
        return false;
    }


    /**
     * @param BSeller_SkyHub_Model_Catalog_Product_Attributes_Mapping $mapping
     *
     * @return string
     */
    public function getRowClass($mapping)
    {
        $class = '';
        
        if (!$mapping->getEditable()) {
            $class .= 'not-editable';
        }
        
        if ($mapping->getAttributeId()) {
            $class .= ' linked';
        }
        
        if (!$mapping->getAttributeId()) {
            $class .= ' unlinked';
        }
        
        return $class;
    }
}
