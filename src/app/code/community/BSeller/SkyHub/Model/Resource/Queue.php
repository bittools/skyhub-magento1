<?php

class BSeller_SkyHub_Model_Resource_Queue extends BSeller_Core_Model_Resource_Abstract
{

    protected function _construct()
    {
        $this->_init('bseller_skyhub/queue', 'id');
    }


    /**
     * @param int|array   $entityIds
     * @param string      $entityType
     * @param bool        $canProcess
     * @param null|string $processAfter
     *
     * @return $this
     */
    public function queue($entityIds, $entityType, $canProcess = true, $processAfter = null)
    {
        $entityIds = (array) $entityIds;
        $entityIds = array_filter($entityIds, function (&$value) {
            $value = (int) $value;
            return $value;
        });

        if (empty($entityIds)) {
            return $this;
        }

        $items = [];

        foreach ($entityIds as $entityId) {
            $items[] = [
                'entity_id'     => (int) $entityId,
                'entity_type'   => (string) $entityType,
                'status'        => BSeller_SkyHub_Model_Queue::STATUS_PENDING,
                'can_process'   => (bool) $canProcess,
                'process_after' => empty($processAfter) ? now() : $processAfter,
                'created_at'    => now(),
            ];
        }

        try {
            $ids   = implode(',', $entityIds);
            $where = new Zend_Db_Expr("entity_id IN ($ids) AND entity_type = '{$entityType}'");
            $this->_getWriteAdapter()->delete($this->getMainTable(), $where);
        } catch (Exception $e) {
            Mage::logException($e);
        }

        /** @var array $item */
        foreach ($items as $item) {
            $this->beginTransaction();

            try {
                $this->_getWriteAdapter()->insert($this->getMainTable(), $item);
                $this->commit();
            } catch (Exception $e) {
                Mage::logException($e);
                $this->rollBack();
            }
        }

        return $this;
    }

}
