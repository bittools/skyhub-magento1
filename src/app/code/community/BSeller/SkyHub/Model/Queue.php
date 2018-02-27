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

/**
 * Class BSeller_SkyHub_Model_Queue
 *
 * @method $this setEntityId(integer $entityId)
 * @method $this setReference(string $reference)
 * @method $this setEntityType(string $type)
 * @method $this setStatus(int $status)
 * @method $this setMessages(string $message)
 * @method $this setCanProcess(boolean $flag)
 * @method $this setProcessAfter(string $datetime)
 * @method $this setCreatedAt(string $datetime)
 * @method $this setUpdatedAt(string $datetime)
 *
 * @method int     getEntityId()
 * @method string  getReference()
 * @method string  getEntityType()
 * @method int     getStatus()
 * @method string  getMessages()
 * @method boolean getCanProcess()
 * @method string  getProcessAfter()
 * @method string  getCreatedAt()
 * @method string  getUpdatedAt()
 */
class BSeller_SkyHub_Model_Queue extends BSeller_Core_Model_Abstract
{

    const STATUS_PENDING       = 1;
    const STATUS_FAIL          = 2;
    const STATUS_RETRY         = 3;

    const PROCESS_TYPE_IMPORT  = 1;
    const PROCESS_TYPE_EXPORT  = 2;


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
