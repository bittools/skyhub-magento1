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

use SkyHub\Api\Handler\Response\HandlerDefault;
use SkyHub\Api\Handler\Response\HandlerException;

class BSeller_SkyHub_Model_Integrator_Catalog_Product_Attribute extends BSeller_SkyHub_Model_Integrator_Abstract
{

    use BSeller_SkyHub_Trait_Entity,
        BSeller_SkyHub_Trait_Transformers;


    /**
     * @param Mage_Eav_Model_Entity_Attribute $attribute
     *
     * @return bool|HandlerDefault|HandlerException
     */
    public function createOrUpdate(Mage_Eav_Model_Entity_Attribute $attribute)
    {
        $exists = $this->productAttributeExists($attribute->getId());

        $eventParams = array(
            'attribute' => $attribute
        );

        Mage::dispatchEvent('bseller_skyhub_catalog_product_attribute_integrate_before', $eventParams);

        if (true == $exists) {
            /** Update Product Attribute */
            $response = $this->update($attribute);
            $eventParams['method'] = 'update';
        } else {
            /** Create Product Attribute */
            $response = $this->create($attribute);
            $eventParams['method'] = 'create';

            if ($response && $response->success()) {
                $this->registerProductAttributeEntity($attribute->getId());
            }
        }

        $eventParams['response'] = $response;

        Mage::dispatchEvent('bseller_skyhub_catalog_product_attribute_integrate_after', $eventParams);

        return $response;
    }


    /**
     * @param Mage_Eav_Model_Entity_Attribute $attribute
     *
     * @return bool|HandlerDefault|HandlerException
     */
    public function create(Mage_Eav_Model_Entity_Attribute $attribute)
    {
        if (!$this->canIntegrateAttribute($attribute)) {
            return false;
        }

        /** @var SkyHub\Api\EntityInterface\Catalog\Product\Attribute $interface */
        $interface = $this->productAttributeTransformer()->convert($attribute);
        return $interface->create();
    }


    /**
     * @param Mage_Eav_Model_Entity_Attribute $attribute
     *
     * @return bool|HandlerDefault|HandlerException
     */
    public function update(Mage_Eav_Model_Entity_Attribute $attribute)
    {
        if (!$this->canIntegrateAttribute($attribute)) {
            return false;
        }

        /** @var SkyHub\Api\EntityInterface\Catalog\Product\Attribute $interface */
        $interface = $this->productAttributeTransformer()->convert($attribute);
        return $interface->update();
    }


    /**
     * @param Mage_Eav_Model_Entity_Attribute $attribute
     *
     * @return bool
     */
    protected function canIntegrateAttribute(Mage_Eav_Model_Entity_Attribute $attribute)
    {
        return (bool) ($attribute->getId() && $attribute->getAttributeCode() && $attribute->getFrontendLabel());
    }
}
