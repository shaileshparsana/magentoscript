<?php 
set_time_limit(0);
 $rootdir = dirname(__DIR__);
#/chroot/home/bigstaru/bigstarusa.com/html/
require_once $rootdir.'\app/Mage.php';
Mage::app();


$dir  = Mage::getBaseDir().'/order_fulfillment/csvfiles';
$order_export_dir = $dir.'/orders/export';
$order_import_dir = $dir.'/orders/import';
$stock_export_dir = $dir.'/inventory/export';
$stock_import_dir = $dir.'/inventory/import';
$category_export_dir = $dir.'/category/export';
$rma_export_dir = $dir.'/rma/export'; 


$stock_import_file = 'Inventory.csv';
$order_import_file = 'orders_tracking_import.csv';
