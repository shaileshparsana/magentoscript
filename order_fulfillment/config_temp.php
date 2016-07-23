<?php 
set_time_limit(0);

require_once '/chroot/home/wearpact/wearpact.com/html/app/Mage.php';
Mage::app();


$dir  = '/chroot/home/wearpact/wearpact.com/html/order_fulfillment/csvfiles';
$order_export_dir = $dir.'/orders/export';
$order_import_dir = $dir.'/orders/import';
$stock_export_dir = $dir.'/inventory/export';
$stock_import_dir = $dir.'/inventory/import';
$category_export_dir = $dir.'/category/export';


$stock_import_file = 'stock_import.csv';
$order_import_file = 'orders_tracking_import.csv';
