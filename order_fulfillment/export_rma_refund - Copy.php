<?php 
	
	ini_set('memory_limit','512M');
	set_time_limit(0); 
	
	$rootdir = dirname(__DIR__);

	require_once $rootdir.'\app/Mage.php';

	umask(0);
	Mage::app('default');
	
	$file_path = Mage::getBaseDir().'/order_fulfillment/csvfiles/rma/export/rma_refund.csv'; //file path of the CSV file in which the $data to be saved

	$mage_csv = new Varien_File_Csv(); //mage CSV
	
	 $resource = Mage::getSingleton('core/resource');	
	 $tablename = $resource->getTableName('enterprise_rma/item_entity');			
	 $collection = Mage::getModel('enterprise_rma/rma')->getCollection();//->addFieldToFilter('main_table.status',array('eq'=>'closed'));
	 $collection->getSelect()->joinLeft( array('second_table'=>$tablename), 'main_table.entity_id = second_table.rma_entity_id',array('second_table.*'));
	 $collection->getSelect()->reset(Zend_Db_Select::COLUMNS)
	 ->columns('main_table.status as rma_status')
	 ->columns('second_table.status as item_level_status')
	 ->columns('second_table.*')
	 ->columns('main_table.*');
	
	
	$data = array();    
	$rma_row = array();  
	$dataArray = array();	
	foreach ($collection->getData() as $rmaValue)
	{
		$rmaItemId = $rmaValue['rma_entity_id'];
		$data['Rma_id'] = $rmaValue['increment_id'];			
		$data['Order_id'] = $rmaValue['order_increment_id'];		
		$data['Date'] = $rmaValue['date_requested'];		
		$data['Customer_email'] = $rmaValue['customer_custom_email'];	
		$data['Status'] = $rmaValue['rma_status'];	
		$data['Product_name'] = $rmaValue['product_name'];
		$data['Sku'] = $rmaValue['product_sku'];
		$data['qty_requested'] = $rmaValue['qty_requested'];
		$data['qty_authorized'] = $rmaValue['qty_authorized'];
		$data['Itemlevel_status'] = $rmaValue['item_level_status'];
		
		
		
 		$rmaItemsData = Mage::getModel('enterprise_rma/item')->load($rmaItemId);
		
			
		//get value from option id;
		$attr = Mage::getModel('eav/entity_attribute_option')->getCollection()->setStoreFilter()
		->join('attribute', 'attribute.attribute_id=main_table.attribute_id', 'attribute_code')
		->addFieldToFilter('main_table.option_id',array('eq'=>$rmaItemsData->getReason()))
		->getFirstItem();
		
		$data['reason'] = $attr->getValue();
		
		$attrresolution = Mage::getModel('eav/entity_attribute_option')->getCollection()->setStoreFilter()
		->join('attribute', 'attribute.attribute_id=main_table.attribute_id', 'attribute_code')
		->addFieldToFilter('main_table.option_id',array('eq'=>$rmaItemsData->getResolution()))
		->getFirstItem();
		
		
		$data['resolution'] = $attrresolution->getValue();
		
		$rma_row[] = $data;
				
	}

	// This for fetch those record thats refunded
	/* foreach($rma_row as $finalData)
	{
		$customArray = array();
		if($finalData['resolution'] == 'Refund')
		{
			$customArray['Rma_id'] = $finalData['Rma_id'];
            $customArray['Order_id'] = $finalData['Order_id'];
            $customArray['Date'] = $finalData['Date'];
            $customArray['Customer_email'] = $finalData['Customer_email'];
            $customArray['Status'] = $finalData['Status'];
			$customArray['Product_name'] = $finalData['Product_name'];
            $customArray['Sku'] = $finalData['Sku'];
            $customArray['qty_requested'] = $finalData['qty_requested'];
            $customArray['qty_authorized'] = $finalData['qty_authorized'];
            $customArray['Itemlevel_status'] = $finalData['Itemlevel_status'];
            $customArray['reason'] = $finalData['reason'];
            $customArray['resolution'] = $finalData['resolution'];
			$dataArray[] = $finalData;
		}
	}	 */
	
	//echo "<pre>";print_r($rma_row);exit;
	
	$headrs = array("Rma_id","Order_id","Date","Customer_email","Status","Product_name","Sku","Qty_Requested","Qty_Authorized","Item_level_status","reason","resolution");
	
	array_unshift($rma_row,$headrs);	
	$mage_csv->saveData($file_path,$rma_row);
	echo "Successfully $products exported at:-$file_path";
	