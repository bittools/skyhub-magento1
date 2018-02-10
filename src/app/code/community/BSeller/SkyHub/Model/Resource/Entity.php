<?php

class BSeller_SkyHub_Model_Resource_Entity extends BSeller_Core_Model_Resource_Abstract
{

    protected function _construct()
    {
        $this->_init('bseller_skyhub/entity_id', 'id');
    }


    /**
     * @param integer $entityId
     * @param string  $entityType
     *
     * @return bool
     */
    public function createEntity($entityId, $entityType)
    {
        $entityExists = $this->entityExists($entityId, $entityType);

        if ($entityExists) {
            return false;
        }

        try {
            $this->beginTransaction();
            $this->_getWriteAdapter()->insert($this->getMainTable(), [
                'entity_id'   => (int)    $entityId,
                'entity_type' => (string) $entityType,
                'created_at'  => now(),
            ]);
            $this->commit();

            return true;
        } catch (Exception $e) {
            Mage::logException($e);
            $this->rollBack();
        }

        return false;
    }


    /**
     * @param integer $entityId
     * @param string  $entityType
     *
     * @return bool|string
     */
    public function entityExists($entityId, $entityType)
    {
        /** @var Varien_Db_Select $select */
        $select = $this->getReadConnection()
            ->select()
            ->from($this->getMainTable(), 'entity_id')
            ->where('entity_id = ?', (int) $entityId)
            ->where('entity_type = ?', (string) $entityType)
            ->limit(1);

        $result = $this->getReadConnection()->fetchOne($select);

        if (!$result) {
            return false;
        }

        return (int) $result;
    }
}
