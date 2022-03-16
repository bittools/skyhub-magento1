<?php

class BSeller_SkyHub_Test_EntityController extends BSeller_SkyHub_Controller_Front_Action
{

    public function insertAction()
    {
        /** @var BSeller_SkyHub_Model_Resource_Entity $resource */
        $resource = Mage::getResourceModel('bseller_skyhub/entity');
        $resource->createEntity(249999, BSeller_SkyHub_Model_Entity::TYPE_CATALOG_PRODUCT);
        $resource->createEntity(249998, BSeller_SkyHub_Model_Entity::TYPE_CATALOG_PRODUCT);
        $resource->createEntity(249998, BSeller_SkyHub_Model_Entity::TYPE_CATALOG_PRODUCT);
    }

}
