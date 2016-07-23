<?php   
	      
	set_time_limit(0); 
	ini_set('memory_limit', '-1'); 
	$rootdir = dirname(__DIR__);
	require_once $rootdir.'/app/Mage.php';
	umask(0);
	Mage::app();
	init($start,$end);
	function init($start,$end)
	{ 

		$file_path = $_SERVER['DOCUMENT_ROOT'].'/custom_script/export_all_products_ct_store_seo.csv'; //file path of the CSV file in which the $data to be saved
		
		$start = $_POST['start_id']; 
		$end = $_POST['end_id'];

		$mage_csv = new Varien_File_Csv(); //mage CSV
	
		
		$products_ids = Mage::getModel('catalog/product')->getCollection()->addWebsiteFilter(1);
		//$products_ids->addAttributeToFilter('entity_id',array('from' => 43,'to' => $end));

		$products_model = Mage::getModel('catalog/product'); //get $products model
		$products_row = array();    
		
		foreach ($products_ids as $pid)
		{
			$product = $products_model->load($pid->getId());
			$stockItem = $product->getStockItem();			
			$data['sku'] = $product->getSku();
			$data['name'] = $product->getName();
			$data['meta_title'] = $product->getMetaTitle();
			$data['meta_description'] = $product->getMetaDescription();
			$data['description'] = $product->getDescription();
			$data['url_key'] = $product->getUrlKey();
			$data['url_path'] = $product->getUrlPath();		  

			$products_row[] = $data;
			$product->clearInstance(); 
		
			
		} 
		$headrs = array("sku","name","meta_title","meta_description","description","url_key","url_path");
		
		 array_unshift($products_row,$headrs);
		
	
		//echo '<pre>';
		//print_r($products_row);
		//echo '</pre>';
		$mage_csv->saveData($file_path,$products_row);
		echo "Successfully $products exported at:-$file_path";
		
		}