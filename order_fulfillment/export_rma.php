<?php 
require_once 'config.php';

require_once 'Abstract.php';

$order_export  = $rma_export_dir;

///=================================

$export = new exportOrder();
$items = Mage::getModel('enterprise_rma/item')->getCollection();
$orders = Mage::getModel('enterprise_rma/rma')->getCollection();

//echo "<pre>";print_r($orders->getData());exit;

//where('eat.entity_type_code = ?', $this->_getEntityTypeCode());*/
//join in magento
$resource = Mage::getSingleton('core/resource');	
$tablename = $resource->getTableName('enterprise_rma/item_entity');	
$thirdtablename = $resource->getTableName('enterprise_rma/rma_grid');		
$collection = Mage::getModel('enterprise_rma/rma')->getCollection();
$collection->getSelect()->joinLeft( array('second_table'=>$tablename), 'main_table.entity_id = second_table.rma_entity_id', array('second_table.entity_type_id','second_table.product_sku'))
						->joinLeft( array('third_table'=>$thirdtablename), 'main_table.order_increment_id  = third_table.order_increment_id ', array('third_table.customer_name','third_table.order_date'));
//echo $collection->getSelect();
//echo '<pre>';print_r($collection->getData());exit;


$filename = $rma_export_dir."/last_rma.txt";
$handle = fopen($filename, "r");
$lastOrderID = fread($handle, filesize($filename));
fclose($handle);
//$lastOrderID =0;
foreach($orders as $order){
		//echo $order->getId();exit;
		if($order->getId()>$lastOrderID){
			$orderIds[] = $order->getId();
			$orderID = $order->getId();
			$orderIncrementId = $order->getIncrementId();
		}
}

if($orderID!=''){
	$fp = fopen($rma_export_dir.'/last_rma.txt', 'w');
	fwrite($fp, $orderID);
	fclose($fp);
}
if(empty($orderIncrementId))
{
	$orders = Mage::getModel('sales/order')->load($lastOrderID);
	$orderIncrementId = $orders->getIncrementId();
}

$uploadfile = $export->exportRma($items,$orderIds,$rma_export_dir,$orderIncrementId);
echo 'Order exported';
echo "<br/>";


//$uploadedfile = Mage::getBaseUrl()."order_fulfillment/csvfiles/rma/export/".$uploadfile;
/*
$uploadedfile = Mage::getBaseDir()."/order_fulfillment/csvfiles/rma/export/".$uploadfile;

$file = "/RMA/".$uploadfile;
$fp = fopen($uploadedfile, 'r');

$ftp_server="download.bigstarusa.com";
$ftp_user_name="plumtree";
$ftp_user_pass="233ba232";

$conn_id = ftp_connect($ftp_server) or die("Couldn't connect to $ftp_server"); 
$login_result = ftp_login($conn_id, $ftp_user_name, $ftp_user_pass)  or die('Not able to login to FTP');
ftp_pasv($conn_id, true);
  
if (ftp_fput($conn_id,$file, $fp, FTP_ASCII)) {
    echo "Successfully uploaded $file\n";
} else {
    echo "There was a problem while uploading $file\n";
}

 //close the connection
ftp_close($conn_id);

*/