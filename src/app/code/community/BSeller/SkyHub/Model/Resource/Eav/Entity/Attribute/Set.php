<?php

class BSeller_SkyHub_Model_Resource_Eav_Entity_Attribute_Set extends Mage_Eav_Model_Resource_Entity_Attribute_Set
{

    /**
     * @param int    $entityTypeId
     * @param string $groupName
     *
     * @return bool
     */
    public function setupEntityAttributeGroups($entityTypeId, $groupName, $sortOrder = 900)
    {
        /** @var Varien_Db_Select $select */
        $select = $this->getReadConnection()
            ->select()
            ->from(['sets' => $this->getMainTable()], 'attribute_set_id')
            ->joinLeft(
                ['groups' => $this->getTable('eav/attribute_group')],
                "sets.attribute_set_id = groups.attribute_set_id AND groups.attribute_group_name='{$groupName}'",
                'attribute_group_id'
            )
            ->where('entity_type_id = ?', (int) $entityTypeId)
            ->where('ISNULL(attribute_group_id)')
        ;

        try {
            $results = (array) $this->getReadConnection()->fetchAll($select);
        } catch (Exception $e) {
            Mage::logException($e);
            return false;
        }

        if (empty($results)) {
            return true;
        }

        $groups = [];

        /** @var array $result */
        foreach ($results as $result) {
            $setId = (int) $result['attribute_set_id'];

            if (empty($setId)) {
                continue;
            }

            $groups[] = [
                'attribute_set_id'     => $setId,
                'attribute_group_name' => $groupName,
                'sort_order'           => (int) $sortOrder,
            ];
        }

        try {
            $this->beginTransaction();

            if (!empty($groups)) {
                $this->_getWriteAdapter()->insertMultiple($this->getTable('eav/attribute_group'), $groups);
            }

            $this->commit();

            return true;
        } catch (Exception $e) {
            Mage::logException($e);
            $this->rollBack();
        }

        return false;
    }

}
