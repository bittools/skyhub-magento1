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
 * @author    Bruno Gemelli <bruno.gemelli@e-smart.com.br>
 */

/**
 * @var BSeller_SkyHub_Model_Resource_Setup $this
 * @var Magento_Db_Adapter_Pdo_Mysql        $conn
 */

$this->startSetup();


//**********************************************************************************************************************
// Install bseller_skyhub/plp
//**********************************************************************************************************************
$tableName = (string) $this->getTable('bseller_skyhub/plp');

//@todo multi-store structure

/** @var Varien_Db_Ddl_Table $table */
$table = $this->newTable($tableName)
    ->addColumn(
        'skyhub_code',
        $this::TYPE_VARCHAR,
        30,
        [
            'nullable' => false,
        ]
    )
    ->addColumn(
        'expiration_date',
        $this::TYPE_DATE,
        null,
        [
            'nullable' => false,
        ]
    );

$this->addTimestamps($table);
$conn->createTable($table);

$this->addIndex(['skyhub_code'], $tableName);


//**********************************************************************************************************************
// Install bseller_skyhub/plp_orders
//**********************************************************************************************************************
$tableName = (string) $this->getTable('bseller_skyhub/plp_orders');

/** @var Varien_Db_Ddl_Table $table */
$table = $this->newTable($tableName)
    ->addColumn(
        'plp_id',
        $this::TYPE_INTEGER,
        10,
        [
            'nullable' => false,
            'primary'  => true,
        ]
    )
    ->addColumn(
        'skyhub_order_code',
        $this::TYPE_VARCHAR,
        128,
        [
            'nullable' => false
        ]
    )
    ->addColumn(
        'additional_data',
        $this::TYPE_TEXT,
        null,
        [
            'nullable' => true,
        ]
    );

$conn->createTable($table);

$this->addForeignKey($tableName, 'plp_id', 'bseller_skyhub/plp', 'id');

$this->addIndex(['plp_id'], $tableName, Varien_Db_Adapter_Interface::INDEX_TYPE_INDEX);
$this->addIndex(['skyhub_order_code'], $tableName, Varien_Db_Adapter_Interface::INDEX_TYPE_INDEX);

$this->endSetup();
