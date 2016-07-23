<?php   
	
	ini_set('memory_limit','512M');
	set_time_limit(0); 
	
	$rootdir = dirname(__DIR__);

	require_once $rootdir.'\app/Mage.php';

	umask(0);
	Mage::app('default');
	
		$file_path = Mage::getBaseDir().'/order_fulfillment/csvfiles/shipment.csv'; //file path of the CSV file in which the $data to be saved

		$mage_csv = new Varien_File_Csv(); //mage CSV
	
		$row = 0;
		$target_path = Mage::getBaseDir()."/order_fulfillment/csvfiles/orders/import/orders_tracking_import.csv";

		

		 if (($handle = fopen($target_path, "r")) !== FALSE) 
		{
			while (($data = fgetcsv($handle, 10000, ",")) !== FALSE)
			{
				
				if($data[0] == 'OrderNo')continue;
				
				
				$order = Mage::getModel('sales/order')->loadByIncrementId($data[0]);
				
				$orderID = $order->getId();			     
				
				$shipmentData = Mage::getModel('sales/order_shipment')->getCollection()
				->addFieldToFilter('order_id',array('eq' => $orderID))->getData();
				
				$data = array();    
				
				
				foreach ($shipmentData as $shipment)
				{
					$orderID = $shipment['order_id'];
					$shipmentId = $shipment['entity_id'];


					$data['created_at'] = $shipment['created_at'];
					$data['order_id'] = $order->getIncrementId();
					
					$shipmentTrackModel = Mage::getModel('sales/order_shipment_track')->getCollection()
					->addFieldToFilter('parent_id',array('eq' =>$shipmentId))->getData();			
					
					$customArray = array();
					$trackingData = array();
					foreach($shipmentTrackModel as $value)
					{
						$trackingData['tracking_no'] = $value['track_number'];
						$trackingData['carrier_code'] = $value['carrier_code'];
						$trackingData['service_method'] = $value['title'];
						$customArray = $trackingData;
					}
					
					
					$data['customer_email'] = $order->getCustomerEmail();
					
					$data['shipment_id'] = 	$shipment['increment_id'];
					
					$shipmentsItemModel = Mage::getModel('sales/order_shipment_item')->getCollection()
					->addFieldToFilter('parent_id',array('eq' =>$shipmentId))->getData();			
					
					
					$skuArray = array();	
					$qtyArray = array();			
					foreach($shipmentsItemModel as $skus)	
					{
						
						$qty['qty'] = number_format($skus['qty'], 2, '.', '');
						$skuArray[] = $skus['sku'];
						$qtyArray[]	 = $qty['qty'];
					}
						
					
					$finalskus = implode(",",$skuArray);
					$finalqtys = implode(",",$qtyArray);
					
					$data['Sku'] = 	$finalskus;
					$data['qty'] = $finalqtys;
					
					$shipment_row[] =  array_merge($data,$customArray);
				
						
				}
			}
			$headrs = array("shipment_date","order_	id","customer_email","shipment_id","Sku","qty","tracking_no","carrier_code","service_method");
			$items_Array = array_unique($shipment_row,SORT_REGULAR);
				
			array_unshift($items_Array,$headrs);
					
			$mage_csv->saveData($file_path,$items_Array);
			echo "Successfully $products exported at:-$file_path";
		}
		else
		{
				echo "File not found:-$file_path";
		}