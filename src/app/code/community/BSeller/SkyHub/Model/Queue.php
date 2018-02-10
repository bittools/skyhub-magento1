<?php

/**
 * Class BSeller_SkyHub_Model_Queue
 *
 * @method $this setEntityId(integer $entityId)
 * @method $this setEntityType(string $type)
 * @method $this setStatus(int $status)
 * @method $this setMessages(string $message)
 * @method $this setCanProcess(boolean $flag)
 * @method $this setProcessAfter(string $datetime)
 * @method $this setCreatedAt(string $datetime)
 * @method $this setUpdatedAt(string $datetime)
 */
class BSeller_SkyHub_Model_Queue extends BSeller_Core_Model_Abstract
{

    const STATUS_PENDING = 1;
    const STATUS_FAIL    = 2;
    const STATUS_RETRY   = 3;


    protected function _construct()
    {
        $this->_init('bseller_skyhub/queue');
    }


    /**
     * @param int           $entityId
     * @param string        $entityType
     * @param bool          $canProcess
     * @param null|string   $processAfter
     * @return $this
     */
    public function queue($entityId, $entityType, $canProcess = true, $processAfter = null)
    {
        $this->setEntityId($entityId)
            ->setEntityType($entityType)
            ->setCanProcess((bool) $canProcess)
            ->setProcessAfter($processAfter);

        return $this;
    }


    /**
     * @return $this
     */
    protected function _beforeSave()
    {
        if (!$this->getProcessAfter()) {
            $this->setProcessAfter(now());
        }

        parent::_beforeSave();

        return $this;
    }

}
