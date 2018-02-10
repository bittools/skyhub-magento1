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


    /**
     * @param string $entityType
     *
     * @return array
     */
    public function getPendingEntityIds($entityType)
    {
        $integrableStatuses = [
            BSeller_SkyHub_Model_Queue::STATUS_PENDING,
            BSeller_SkyHub_Model_Queue::STATUS_RETRY
        ];

        /** @var Varien_Db_Select $select */
        $select = $this->getReadConnection()
            ->select()
            ->from($this->getMainTable())
            ->where('status IN (?)', imploe(',', $integrableStatuses))
            ->where('can_process = 1')
            ->where('process_after <= ?', now())
            ->where('entity_type = ?', (string) $entityType)
        ;

        $ids = $this->getReadConnection()->fetchCol($select);

        return (array) $ids;
    }


    /**
     * @param integer|array $entityIds
     * @param string        $entityType
     *
     * @return $this
     */
    public function removeFromQueue($entityIds, $entityType)
    {
        $entityIds = $this->filterEntityIds($entityIds);

        if (empty($entityIds)) {
            return $this;
        }

        $entityIds = implode(',', $entityIds);

        $where = new Zend_Db_Expr("entity_id IN ({$entityIds}) AND entity_type = '{$entityType}'");
        $this->_getWriteAdapter()->delete($this->getMainTable(), $where);

        return $this;
    }


    /**
     * @param integer|array $entityIds
     * @param string        $entityType
     *
     * @return $this
     */
    public function setFailedEntityIds($entityIds, $entityType)
    {
        $this->updateQueueStatus($entityIds, $entityType, BSeller_SkyHub_Model_Queue::STATUS_FAIL);
        return $this;
    }


    /**
     * @param integer|array $entityIds
     * @param string        $entityType
     *
     * @return $this
     */
    public function setPendingEntityIds($entityIds, $entityType)
    {
        $this->updateQueueStatus($entityIds, $entityType, BSeller_SkyHub_Model_Queue::STATUS_PENDING);
        return $this;
    }


    /**
     * @param integer|array $entityIds
     * @param string        $entityType
     *
     * @return $this
     */
    public function setRetryEntityIds($entityIds, $entityType)
    {
        $this->updateQueueStatus($entityIds, $entityType, BSeller_SkyHub_Model_Queue::STATUS_RETRY);
        return $this;
    }


    /**
     * @param int|array $entityIds
     * @param string    $entityType
     * @param int       $status
     *
     * @return $this
     */
    public function updateQueueStatus($entityIds, $entityType, $status)
    {
        $this->updateQueues($entityIds, $entityType, ['status' => $status]);
        return $this;
    }


    /**
     * @param integer|array $entityIds
     * @param string        $entityType
     * @param array         $binds
     *
     * @return $this
     */
    public function updateQueues($entityIds, $entityType, array $binds = [])
    {
        $entityIds = $this->filterEntityIds($entityIds);

        if (empty($entityIds)) {
            return $this;
        }

        $entityIds = implode(',', $entityIds);

        $where = new Zend_Db_Expr("entity_id IN ({$entityIds}) AND entity_type = '{$entityType}'");
        $this->_getWriteAdapter()
            ->update($this->getMainTable(), $binds, $where);

        return $this;
    }


    /**
     * @param array $entityIds
     *
     * @return array
     */
    protected function filterEntityIds(array $entityIds)
    {
        $entityIds = (array) array_filter($entityIds, function (&$value) {
            $value = (int) $value;
            return $value;
        });

        return $entityIds;
    }
}
