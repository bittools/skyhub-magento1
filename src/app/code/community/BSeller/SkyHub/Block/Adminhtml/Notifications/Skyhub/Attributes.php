<?php

/**
 * Class BSeller_SkyHub_Block_Adminhtml_Notifications_Skyhub_Attributes
 */
class BSeller_SkyHub_Block_Adminhtml_Notifications_Skyhub_Attributes extends BSeller_Core_Block_Adminhtml_Template
{

    protected function _construct()
    {
        $this->setTemplate('bseller/skyhub/notifications/skyhub/attributes.phtml');
        parent::_construct();
    }


    /**
     * @return bool
     */
    public function canShow()
    {
        return (bool) ($this->getPendingAttributesCollection()->getSize()>0);
    }


    /**
     * @param null|int $id
     *
     * @return string
     */
    public function getMappingManagerUrl($id = null)
    {
        if ($id) {
            return $this->getUrl('adminhtml/bseller_skyhub_catalog_product_attributes_mapping/edit', ['id' => $id]);
        }

        return $this->getUrl('adminhtml/bseller_skyhub_catalog_product_attributes_mapping/index');
    }


    /**
     * @param int $id
     *
     * @return string
     */
    public function getAttributeAutoUrl($id)
    {
        return $this->getUrl('adminhtml/bseller_skyhub_catalog_product_attributes_mapping/createAutomatically', [
            'id' => $id
        ]);
    }


    /**
     * @return BSeller_SkyHub_Model_Resource_Catalog_Product_Attributes_Mapping_Collection
     */
    public function getPendingAttributesCollection()
    {
        $key = 'notification_pending_attributes_collection';

        if (Mage::registry($key)) {
            return Mage::registry($key);
        }

        /** @var BSeller_SkyHub_Model_Resource_Catalog_Product_Attributes_Mapping_Collection $collection */
        $collection = Mage::getResourceModel('bseller_skyhub/catalog_product_attributes_mapping_collection');
        $collection->setPendingAttributesFilter();

        Mage::register($key, $collection, true);

        return $collection;
    }

}