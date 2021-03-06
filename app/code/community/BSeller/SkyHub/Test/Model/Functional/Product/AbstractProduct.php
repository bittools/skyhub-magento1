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
class BSeller_SkyHub_Test_Product_AbstractProduct extends BSeller_SkyHub_Test_AbstractTest
{
    use BSeller_SkyHub_Trait_Catalog_Product_Attribute;

    public function createAndMapAllProductAttributes()
    {
        $mapIds = [
            10, 11, 12, 13, 14, 15
        ];

        foreach ($mapIds as $mapId) {
            $this->createAndMapProductAttribute($mapId);
        }
    }

    public function deleteEavAttribute($attrCode) {
        $attribute = Mage::getModel('eav/entity_attribute')->loadByCode(Mage_Catalog_Model_Product::ENTITY, $attrCode);
        $attribute->delete();
    }

    public function createAndMapProductAttribute($mapId)
    {
        /** @var BSeller_SkyHub_Model_Catalog_Product_Attributes_Mapping $mapping */
        $mapping = $this->getMapping($mapId);

        if ($mapping->getAttributeId()) {
            return $mapping;
        }

        $config = array(
            'label' => $mapping->getSkyhubLabel(),
            'type' => 'varchar',
            'input' => 'text',
            'required' => 0,
            'visible_on_front' => 0,
            'filterable' => 0,
            'searchable' => 0,
            'comparable' => 0,
            'user_defined' => 1,
            'is_configurable' => 0,
            'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
            'note' => sprintf(
                '%s. %s.',
                'Created automatically by BSeller SkyHub module.',
                $mapping->getSkyhubDescription()
            ),
        );

        $installConfig = (array)$mapping->getAttributeInstallConfig();

        foreach ($installConfig as $configKey => $itemValue) {
            if (is_null($itemValue)) {
                continue;
            }

            $config[$configKey] = $itemValue;
        }

        $attributeId = $this->loadProductAttribute($mapping->getSkyhubCode())->getId();
        if (!$attributeId) {
            /** @var Mage_Eav_Model_Entity_Attribute $attribute */
            $attribute = $this->createProductAttribute($mapping->getSkyhubCode(), (array)$config);
            $attributeId = $attribute->getId();

        }
        $mapping->setAttributeId($attributeId)
            ->save();

        return $mapping;
    }

    protected function getMapping($id)
    {
        /** @var BSeller_SkyHub_Model_Catalog_Product_Attributes_Mapping $mapping */
        $mapping = Mage::getModel('bseller_skyhub/catalog_product_attributes_mapping');
        $mapping->load((int)$id);

        Mage::register('current_attributes_mapping', $mapping, true);

        return $mapping;
    }
}