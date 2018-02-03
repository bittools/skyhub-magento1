<?php

class BSeller_SkyHub_Model_Transformer_Catalog_Product extends BSeller_SkyHub_Model_Transformer_TransformerAbstract
{

    use BSeller_SkyHub_Trait_Service;


    /**
     * @param Mage_Catalog_Model_Product $product
     *
     * @return \SkyHub\Api\EntityInterface\Catalog\Product
     */
    public function convert(Mage_Catalog_Model_Product $product)
    {
        /** @var \SkyHub\Api\EntityInterface\Catalog\Product $interface */
        $interface = $this->api()->productAttribute()->entityInterface();
        $interface->setSku($product->getSku())
            ->setName($product->getName())
            ->setDescription($product->getDescription())
            ->setBrand('')
            ->setCost((float) $product->getCost())
            ->setPrice((float) $product->getPrice())
            ->setPromotionalPrice((float) $product->getFinalPrice())
            ->setWeight((float) $product->getWeight())
            ->setWidth((float) $product->getWeight())
            ->setHeight(1)
            ->setLength(1)
            ->setStatus((bool) $product->getStatus())
        ;

        return $interface;
    }

}
