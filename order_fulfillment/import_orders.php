<?php
require_once 'config.php';
//=======================================================

  
$ftp_server="198.57.168.245";
$ftp_user_name="digiwrap@cfcdi.org";
$ftp_user_pass="?Xy*?iqE*R7^";
$date = date('Y-m-d');  

$local_file = Mage::getBaseDir()."/order_fulfillment/csvfiles/orders/import/orders_tracking_import.csv";
$server_file = "/SHIPPING/shipping_".$date.".csv";

//if(file_exists($server_file))
//{ 
	$conn_id = ftp_connect($ftp_server) or die("Couldn't connect to $ftp_server"); 
	$login_result = ftp_login($conn_id, $ftp_user_name, $ftp_user_pass)  or die('Not able to login to FTP');
	//ftp_pasv($conn_id, true);
	  
	if (ftp_get($conn_id,$local_file, $server_file,FTP_BINARY)) 
	{
		echo "Successfully written to $local_file\n";Fileimport();echo "Order Tracking updated successfully!";
	} 
	else 
	{
		$date = date('Y-m-d',time()-24*3600);
		$local_file = Mage::getBaseDir()."/order_fulfillment/csvfiles/orders/import/orders_tracking_import.csv";
		$server_file = "/SHIPPING/shipping_".$date.".csv";
		if (ftp_get($conn_id,$local_file, $server_file,FTP_BINARY)) 
		{
			echo "Successfully written to $local_file\n";
			Fileimport();echo "Order Tracking updated successfully!";
		}
		else
		{
			echo "There was a problem\n";
		}
	}
	ftp_close($conn_id);
	
	$currentOrder = ''; 
	function Fileimport() {
		global $order_import_dir,$order_import_file,$currentOrder;
		  if( !function_exists( "updateOrder" ) ) {
		  
			function updateOrder($orderId,$trackingNum, $method, $title, $sku) {

				 //echo $orderId."".$trackingNum."".$method."".$title."".$sku;exit;
				
				
				$order = Mage::getModel('sales/order')->loadByIncrementId($orderId);
				$orderentityId = $order->getId();
				$total_item = $order->getTotalItemCount(); //for count no. of item
				
				//for checking already increament id exist or not in sales_flat_shipment....
				$data1 = Mage::getModel('sales/order_shipment')->loadByIncrementId($orderId);
				
				
				
				 $resource = Mage::getSingleton('core/resource')->getConnection('core_read');
				 $table =  Mage::getSingleton('core/resource')->getTableName('sales_flat_shipment');	
				 //for setting of differnt increament id in flat_shipment table for each item
				 $query = 'SELECT MAX(`increment_id`) FROM '.$table;
				 $ids = $resource->fetchOne($query);
				
				//for avoidance of repeat of tracking number for same orders item...
				$tracknoExist = Mage::getModel('sales/order_shipment_track')->getCollection()->addFieldToFilter('track_number',$trackingNum)->addFieldToFilter('order_id',$orderentityId);
				//if(count($tracknoExist->getData())>0){ 
						//msg for order tracking number is already exist with same number for this order.....	
				//		echo 'Same Tracking code for order id '.$orderId .' is found...<br>';			
				//}
				//else{
								
					global $currentOrder;	
					$includeComment = false;
					$comment = NULL;
					$matchSku = 0;
					
					$email = $order->getCustomerEmail();										  
					$orderStatus = $order->getStatus();
					
					//    This converts the order to "Completed".
					if( $orderStatus == "processing" ) {
						$convertor = Mage::getModel('sales/convert_order');
						$shipment = $convertor->toShipment($order);	
						$shipment->setIncrementId($ids+1);	
						
						foreach ($order->getAllItems() as $orderItem) {						
						   $orderItem->getQtyToShip();
							if (!$orderItem->getQtyToShip()) {
								continue;
							}
							if ($orderItem->getIsVirtual()) {
								continue;
							}
							
							if($orderItem->getSku() == $sku){
								$item = $convertor->itemToShipmentItem($orderItem);
								$item->setQty($orderItem->getQtyToShip());
								$shipment->addItem($item);
								$matchSku = 1;
								
							}
						}
						
					 } else {
						foreach ($order->getShipmentsCollection() as $shipment) {
							$shipmentId = $shipment->getIncrementId();
						}
							$shipment = Mage::getModel('sales/order_shipment')->loadByIncrementId($shipmentId);
					}
				  if($matchSku){
						$shipment->register();
						$shipment->addComment($comment, $email && $includeComment);
						$shipment->setEmailSent(true);
						$shipment->getOrder()->setIsComplete(true); 
						
						if($sendEmail==true)
						{
							$corder = Mage::getModel('sales/order')->loadByIncrementId($currentOrder);
							$email = $corder->getCustomerEmail();
							foreach ($corder->getShipmentsCollection() as $shipment) {
							 $shipmentId = $shipment->getIncrementId();
							}
							$shipment = Mage::getModel('sales/order_shipment')->loadByIncrementId($shipmentId);
							if(!$shipment->getEmailSent())
							{
							 $shipment->sendEmail($email, ($includeComment ? $comment : ''));
							 $shipment->setEmailSent(true);
							}
						 
							$currentOrder = $orderId;
						}
				  //shipid is useful for get data of shipment table....
				  $shipid = Mage::getModel('sales/order_shipment')->getCollection()->addFieldToFilter('order_id',$orderentityId)->getData();
				  $shipmentId = $shipid[0]['entity_id']; 
				  $carrierTitle = $title;
				  $carrier = $method;
					
				   //save data in sales_flat_shipment_track for each record with title and tracking number....
					$trackCollection = Mage::getModel('sales/order_shipment_track');
					$trackCollection->setParentId($shipmentId)
									->setOrderId($orderentityId)
									->setTrackNumber($trackingNum)
									->setTitle($title)
									->setCarrierCode($method);
					$shipment->addTrack($trackCollection);
					//for ship button remove and order status is complted......
					$transactionSave = Mage::getModel('core/resource_transaction')->addObject($shipment)->addObject($shipment->getOrder())->save(); 		
				 
			}
				return;
			
				//}
		}
		}       
		$fileName = $order_import_file;
		$target_path = $order_import_dir.'/'.$fileName;
		
	   // $fileName = $order_import_file;
		//$target_path = Mage::getBaseDir()."/order_fulfillment/csvfiles/orders/import/orders_tracking_import.csv";
		ini_set("auto_detect_line_endings", 1);
		$current_row = 1;
		if( file_exists( $target_path ) ) {
			$handle = fopen($target_path, "r");
			$csvData = array();
			
			while ( ($data = fgetcsv($handle, 10000, ",") ) !== FALSE ) {
				$number_of_fields = count($data);
				if ($current_row == 1) {    //Header line
					for ($c=0; $c < $number_of_fields; $c++) {
						$header_array[$c] = $data[$c];
					}
				} else {    //Data line
					for ($c=0; $c < $number_of_fields; $c++) {
						$data_array[$header_array[$c]] = $data[$c];
					}
					$csvData[] = $data_array;
				}
				$current_row++;
			}
			fclose($handle);
			foreach($csvData as $rec) { 			   
				if($rec['Tracking Code']!='CANCELLED')
				{
					$sendEmail = false;
					if($currentOrder=='')
					{
					$currentOrder = $rec['OrderNo'];
					//echo $currentOrder;
				//	 $sendEmail = true;
					}
					else if($currentOrder != $rec['OrderNo'])
					{
						$sendEmail = true;
					}
					updateOrder($rec['OrderNo'], $rec['TrackingNumber'], strtolower($rec['Carrier Code']), $rec['ServiceType'], $rec['SKU'],$rec['comment']); 
				}
			} //foreach end......
		} //if(file exist..)  end...
	}
	Fileimport();	
	echo "Order Tracking updated successfully!";
