<?php
/**
 * BSeller Platform | B2W - Companhia Digital
 *
 * Do not edit this file if you want to update this module for future new versions.
 *
 * @category  BSeller
 * @package   BSeller_SkyHub
 *
 * @copyright Copyright (c) 2021 B2W Digital - BSeller Platform. (http://www.bseller.com.br)
 *
 * Access https://ajuda.skyhub.com.br/hc/pt-br/requests/new for questions and other requests.
 */

/**
 * @var BSeller_SkyHub_Model_Resource_Setup $this
 * @var Magento_Db_Adapter_Pdo_Mysql        $conn
 */

$this->startSetup();

//**********************************************************************************************************************
// Update sales/order
//**********************************************************************************************************************
$conn->addColumn(
    $this->getTable('sales/order'),
    'bseller_skyhub_status',
    array(
        'type'     => $this::TYPE_TEXT,
        'size'     => 255,
        'nullable' => true,
        'default'  => false,
        'after'    => 'bseller_skyhub_invoice_key',
        'comment'  => 'SkyHub Status'
    )
);

$conn->addColumn(
    $this->getTable('sales/order'),
    'bseller_skyhub_nfe_xml',
    array(
        'type'     => $this::TYPE_BOOLEAN,
        'size'     => 255,
        'nullable' => true,
        'default'  => false,
        'after'    => 'bseller_skyhub_invoice_key',
        'comment'  => 'SkyHub Nfe XML'
    )
);

$this->endSetup();