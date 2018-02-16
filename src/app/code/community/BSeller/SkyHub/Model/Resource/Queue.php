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
     * @param int         $storeId
     *
     * @return $this
     */
    public function queue($entityIds, $entityType, $canProcess = true, $processAfter = null, $storeId = 0)
    {
        $entityIds = (array) $entityIds;
        $entityIds = $this->filterEntityIds($entityIds);

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
                'store_id'      => (int) Mage::app()->getStore($storeId)->getId(),
                'created_at'    => now(),
            ];
        }
        
        $deleteSets = array_chunk($entityIds, 1000);
        
        foreach ($deleteSets as $deleteIds) {
            $this->beginTransaction();
            
            try {
                $deleteIds = implode(',', $deleteIds);
                $where     = new Zend_Db_Expr("entity_id IN ($deleteIds) AND entity_type = '{$entityType}'");
                
                $this->_getWriteAdapter()->delete($this->getMainTable(), $where);
        
                $this->commit();
            } catch (Exception $e) {
                Mage::logException($e);
                $this->rollBack();
            }
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
     * @param string   $entityType
     * @param int|null $limit
     *
     * @return array
     */
    public function getPendingEntityIds($entityType, $limit = null)
    {
        $integrableStatuses = [
            BSeller_SkyHub_Model_Queue::STATUS_PENDING,
            BSeller_SkyHub_Model_Queue::STATUS_RETRY
        ];

        /** @var Varien_Db_Select $select */
        $select = $this->getReadConnection()
            ->select()
            ->from($this->getMainTable(), 'entity_id')
            ->where('status IN (?)', implode(',', $integrableStatuses))
            ->where('can_process = 1')
            ->where('process_after <= ?', now())
            ->where('entity_type = ?', (string) $entityType)
        ;

        if (!is_null($limit)) {
            $select->limit((int) $limit);
        }

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
        $entityIds = $this->filterEntityIds((array) $entityIds);

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
