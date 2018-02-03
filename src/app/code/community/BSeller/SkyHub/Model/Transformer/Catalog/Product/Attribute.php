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
            $interface->setCode($attribute->getAttributeCode())
                ->setLabel($attribute->getFrontendLabel());

            foreach ($attribute->getSource()->getAllOptions() as $option) {
                if (!isset($option['value']) || empty($option['value'])) {
                    continue;
                }

                $interface->addOption($option['value']);
            }
        } catch (Exception $e) {
            Mage::logException($e);
        }

        return $interface;
    }

}
