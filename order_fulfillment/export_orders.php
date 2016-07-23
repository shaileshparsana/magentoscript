<?php 

error_reporting(E_ERROR | E_WARNING | E_PARSE);

require_once 'config.php';

require_once 'Abstract.php';

$order_export  = $order_export_dir;


///=================================
$export = new exportOrder();

$filename = $order_export_dir."/last_order.txt";
$handle = fopen($filename, "r");
$lastOrderID = fread($handle, filesize($filename));
fclose($handle);
$lastOrderID =0;
$orders = Mage::getModel('sales/order')->getCollection();
foreach($orders as $order){

		if($order->getId()>$lastOrderID){
			$orderIds[] = $order->getId();
			$orderID = $order->getId();
			$orderIncrementId = $order->getIncrementId();
		}
}
if($orderID!=''){
	$fp = fopen($order_export_dir.'/last_order.txt', 'w');
	fwrite($fp, $orderID);
	fclose($fp);
}


if(empty($orderIncrementId))
{
	$orders = Mage::getModel('sales/order')->load($lastOrderID);
	$orderIncrementId = $orders->getIncrementId();
}

$filename = $export->exportOrders($orderIds,$order_export_dir,$orderIncrementId);
echo 'Order exported.';
echo "<br/>";



?>