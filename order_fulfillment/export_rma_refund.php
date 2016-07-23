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
	 $historyTable = $resource->getTableName('enterprise_rma/rma_status_history');
	$collection = Mage::getModel('enterprise_rma/rma')->getCollection()->addFieldToFilter('main_table.status',array('eq'=>'authorized'));
	 $collection->getSelect()->joinLeft( array('second_table'=>$historyTable), 'main_table.entity_id  = second_table.rma_entity_id',array('second_table.*'))
	 ->joinLeft( array('third_table'=>$tablename), 'main_table.entity_id = third_table.rma_entity_id',array('third_table.*'));
	 $collection->getSelect()->reset(Zend_Db_Select::COLUMNS)
	 ->columns('main_table.status as rma_status')
	 ->columns('second_table.status as history_level_status')
	 ->columns('second_table.created_at as history_created')
	 ->columns('third_table.status as item_level_status')
	 ->columns('second_table.*')
	 ->columns('third_table.*')
	 ->columns('main_table.*'); 
	
	
	$todayDate  = Mage::getModel('core/date')->date('Y-m-d');    
	$collection->addFieldToFilter('second_table.status',array('eq'=>'authorized'))
	->addFieldToFilter('second_table.created_at',array('gteq'=>$todayDate));
	
	if($collection->getsize() == 0)
	{
		$todayDate = date('Y-m-d',strtotime("-1 days"));
		$collection->addFieldToFilter('second_table.status',array('eq'=>'authorized'))
		->addFieldToFilter('second_table.created_at',array('gteq'=>$todayDate));
	}
	
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
	
	$headrs = array("Rma_id","Order_id","Date","Customer_email","Status","Product_name","Sku","Qty_Requested","Qty_Authorized","Itemlevel_status","reason","resolution");
	
	array_unshift($rma_row,$headrs);	
	$mage_csv->saveData($file_path,$rma_row);
	echo "Successfully $products exported at:-$file_path";
	
	