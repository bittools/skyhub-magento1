<?php

use SkyHub\Api\EntityInterface\Catalog\Product\Attribute;

class BSeller_SkyHub_Model_Transformer_Catalog_Product_Attribute
    extends BSeller_SkyHub_Model_Transformer_TransformerAbstract
{

    use BSeller_SkyHub_Trait_Service;


    /**
     * @param Mage_Eav_Model_Entity_Attribute $attribute
     *
     * @return \SkyHub\Api\EntityInterface\Catalog\Product\Attribute
     */
    public function convert(Mage_Eav_Model_Entity_Attribute $attribute)
    {
        /** @var \SkyHub\Api\EntityInterface\Catalog\Product\Attribute $interface */
        $interface = $this->api()->productAttribute()->entityInterface();

        try {
            $code  = $attribute->getAttributeCode();
            $label = $attribute->getStoreLabel(Mage::app()->getDefaultStoreView());

            $interface->setCode($code)
                ->setLabel($label);

            $this->appendAttributeOptions($attribute, $interface);
        } catch (Exception $e) {
            Mage::logException($e);
        }

        return $interface;
    }


    /**
     * @param Mage_Eav_Model_Entity_Attribute $attribute
     * @param Attribute                       $interface
     *
     * @return $this
     *
     * @throws Mage_Core_Exception
     */
    protected function appendAttributeOptions(Mage_Eav_Model_Entity_Attribute $attribute, Attribute $interface)
    {
        if (!in_array($attribute->getFrontend()->getInputType(), ['select', 'multiselect'])) {
            return $this;
        }

        if (!$attribute->getSourceModel()) {
            return $this;
        }

        foreach ($attribute->getSource()->getAllOptions() as $option) {
            if (!isset($option['label']) || empty($option['label'])) {
                continue;
            }

            $optionLabel = $option['label'];

            $interface->addOption($optionLabel);
        }

        return $this;
    }
}
