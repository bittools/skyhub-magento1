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
 * @var BSeller_SkyHub_Model_Resource_Setup $this
 * @var Magento_Db_Adapter_Pdo_Mysql        $conn
 */

//**********************************************************************************************************************
// Install bseller_skyhub/product_attributes_mapping
//**********************************************************************************************************************
$tableName = (string) $this->getTable('bseller_skyhub/product_attributes_mapping');

/** @var Varien_Db_Ddl_Table $table */
$table = $this->newTable($tableName)
     ->addColumn('skyhub_code', $this::TYPE_TEXT, 255, [
         'nullable' => false,
     ])
     ->addColumn('skyhub_label', $this::TYPE_TEXT, 255, [
         'nullable' => true,
     ])
     ->addColumn('skyhub_description', $this::TYPE_TEXT, null, [
         'nullable' => true,
     ])
     ->addColumn('enabled', $this::TYPE_BOOLEAN, 1, [
         'nullable' => false,
         'default' => true,
     ])
     ->addColumn('type', $this::TYPE_TEXT, 255, [
         'nullable' => false,
     ])
     ->addColumn('input', $this::TYPE_TEXT, 255, [
         'nullable' => false,
     ])
     ->addColumn('validation', $this::TYPE_TEXT, null, [
         'nullable' => true,
     ])
     ->addColumn('attribute_id', $this::TYPE_INTEGER, 255, [
         'nullable' => true,
         'default'  => null,
     ])
     ->addColumn('required', $this::TYPE_BOOLEAN, 1, [
         'nullable' => false,
         'default'  => true,
     ])
     ->addColumn('editable', $this::TYPE_BOOLEAN, 1, [
         'nullable' => false,
         'default'  => true,
     ])
;

$this->addTimestamps($table);
$conn->createTable($table);

$this->addIndex(['skyhub_code', 'attribute_id'], $tableName);
$this->addForeignKey(
    $tableName, 'attribute_id', 'eav/attribute', 'attribute_id', $this::FK_ACTION_SET_NULL, $this::FK_ACTION_SET_NULL
);
