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
 * Access https://ajuda.skyhub.com.br/hc/pt-br/requests/new for questions and other requests.
 */

class BSeller_SkyHub_Model_Resource_Entity extends BSeller_Core_Model_Resource_Abstract
{

    protected function _construct()
    {
        $this->_init('bseller_skyhub/entity_id', 'id');
    }


    /**
     * @param integer $entityId
     * @param string  $entityType
     * @param int     $storeId
     *
     * @return bool
     */
    public function createEntity($entityId, $entityType, $storeId = 0)
    {
        $entityExists = $this->entityExists($entityId, $entityType);

        if ($entityExists) {
            return false;
        }

        try {
            $this->beginTransaction();
            $this->_getWriteAdapter()->insert(
                $this->getMainTable(),
                array(
                    'entity_id'   => (int)    $entityId,
                    'entity_type' => (string) $entityType,
                    'store_id'    => (int)    Mage::app()->getStore($storeId)->getId(),
                    'created_at'  => Varien_Date::now(),
                    'updated_at'  => Varien_Date::now(),
                    'integrate'   => 0,
                )
            );
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
     * @param int     $storeId
     * @param int     $integrate
     *
     * @return bool
     */
    public function updateEntity($entityId, $entityType, $storeId = 0, $integrate = 0)
    {
        $entityExists = $this->entityExists($entityId, $entityType);

        if (!$entityExists) {
            return false;
        }

        $data = $this->entityData($integrate);

        $where = array(
            'entity_id = ?'     => (int)    $entityId,
            'entity_type = ?'   => (string) $entityType,
            'store_id = ?'      => (int)    Mage::app()->getStore($storeId)->getId(),
        );

        try {
            $this->beginTransaction();
            $this->_getWriteAdapter()->update($this->getMainTable(), $data, $where);
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
     * @param integer $storeId
     *
     * @return bool|string
     */
    public function entityExists($entityId, $entityType, $storeId = 0)
    {
        /** @var Varien_Db_Select $select */
        $select = $this->getReadConnection()
            ->select()
            ->from($this->getMainTable(), 'entity_id')
            ->where('entity_id = ?', (int) $entityId)
            ->where('entity_type = ?', (string) $entityType)
            ->where('store_id = ?', (int) Mage::app()->getStore($storeId)->getId())
            ->limit(1);

        try {
            $result = $this->getReadConnection()->fetchOne($select);

            if ($result) {
                return (int) $result;
            }
        } catch (Exception $e) {
            Mage::logException($e);
        }

        return false;
    }

    public function isEntityFlagged($entityId, $entityType, $storeId = 0)
    {
        /** @var Varien_Db_Select $select */
        $select = $this->getReadConnection()
            ->select()
            ->from($this->getMainTable(), 'integrate')
            ->where('entity_id = ?', (int) $entityId)
            ->where('entity_type = ?', (string) $entityType)
            ->where('store_id = ?', (int) Mage::app()->getStore($storeId)->getId())
            ->limit(1);

        try {
            return (int) $this->getReadConnection()->fetchOne($select);
        } catch (Exception $e) {
            Mage::logException($e);
        }

        return false;
    }

    /**
     * @param string  $entityType
     *
     * @return $this
     */
    public function truncateEntityType($entityType)
    {
        $this->_getWriteAdapter()->query('DELETE FROM '.$this->getMainTable().' WHERE entity_type = "'.$entityType.'"');
        return $this;
    }

    /**
     * @param int $integrate
     * @return array
     */
    public function entityData($integrate)
    {
        if ($integrate) {
            $data = array(
                'integrate'   => $integrate,
            );
        } else {
            $data = array(
                'updated_at'  => Varien_Date::now(),
                'integrate'   => $integrate,
            );
        }

        return $data;
    }

    /**
     * @param integer $entityId
     * @param string $entityType
     * @param int $storeId
     *
     * @return bool
     */
    public function deleteEntity($entityId, $entityType, $storeId = 0)
    {
        $entityExists = $this->entityExists($entityId, $entityType);

        if (!$entityExists) {
            return false;

        }

        /**
         * handling params to SQL
         */
        $storeId = Mage::app()->getStore($storeId)->getId();
        $where = new Zend_Db_Expr("entity_id = {$entityId} AND entity_type = '{$entityType}' AND store_id = {$storeId}");

        try {
            $this->beginTransaction();
            $this->_getWriteAdapter()->delete(
                $this->getMainTable(),
                $where
            );
            $this->commit();

            return true;
        } catch (Exception $e) {
            Mage::logException($e);
            $this->rollBack();
        }

        return false;
    }
}
