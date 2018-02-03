<?php

class BSeller_SkyHub_Model_Transformer_Catalog_Product_Attribute
    extends BSeller_SkyHub_Model_Transformer_TransformerAbstract
{

    use BSeller_SkyHub_Trait_Service;


    /**
     * @param Mage_Catalog_Model_Resource_Eav_Attribute $attribute
     *
     * @return \SkyHub\Api\EntityInterface\Catalog\Product\Attribute
     */
    public function convert(Mage_Catalog_Model_Resource_Eav_Attribute $attribute)
    {
        /** @var \SkyHub\Api\EntityInterface\Catalog\Product\Attribute $interface */
        $interface = $this->api()->productAttribute()->entityInterface();

        try {
            $code  = $attribute->getAttributeCode();
            $label = $attribute->getStoreLabel(Mage::app()->getDefaultStoreView());

            $interface->setCode($code)
                ->setLabel($label);

            foreach ($attribute->getSource()->getAllOptions() as $option) {
                if (!isset($option['label']) || empty($option['label'])) {
                    continue;
                }

                $optionLabel = $option['label'];

                $interface->addOption($optionLabel);
            }
        } catch (Exception $e) {
            Mage::logException($e);
        }

        return $interface;
    }

}
