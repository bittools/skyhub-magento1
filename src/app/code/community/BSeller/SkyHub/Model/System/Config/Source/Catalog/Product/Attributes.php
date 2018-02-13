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

class BSeller_SkyHub_Model_System_Config_Source_Catalog_Product_Attributes
    extends BSeller_Core_Model_System_Config_Source_Abstract
{
    
    use BSeller_SkyHub_Trait_Catalog_Product_Attribute;
    
    
    /**
     * @return array
     */
    protected function optionsKeyValue()
    {
        $attributes = $this->getProductAttributesCollection();
        $options    = [
            '' => $this->__('-- Select One Attribute --'),
        ];
        
        /** @var Mage_Eav_Model_Entity_Attribute $attribute */
        foreach ($attributes as $attribute) {
            $label = "{$attribute->getFrontend()->getLabel()} [{$attribute->getAttributeCode()}]";
            $options[$attribute->getId()] = $label;
        }
        
        return $options;
    }
}
