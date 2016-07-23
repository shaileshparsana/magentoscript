<?php   
	
	set_time_limit(-1); 
	ini_set('memory_limit', '-1'); 
	$rootdir = dirname(__DIR__);
	require_once '../app/Mage.php';
	umask(0);
	Mage::app();
	
	
	$file_path ='chicago_export_all_products.csv'; //file path of the CSV file in which the $data to be saved

	$start = $_POST['start_id']; 
	$end = $_POST['end_id'];

	$mage_csv = new Varien_File_Csv(); //mage CSV
	
		
			$products_ids = Mage::getModel('catalog/product')->getCollection()
							->addAttributeToSelect('*')
					->addAttributeToFilter('udropship_vendor', array('eq' => 112)); 
			

		$products_model = Mage::getModel('catalog/product');
					//get $products model
		
		

		$products_row = array();    
		
		foreach ($products_ids as $pid)
		{
			$product = $products_model->load($pid->getId());
		
			$data['category_ids'] = join(',', $product->getCategoryIds());
			$data['sku'] = $product->getSku();
			$data['name'] = $product->getName();
			$data['price'] = number_format((float)$product->getPrice(), 2, '.', '');
			$data['udropship_vendor'] = $product->getUdropshipVendor();
			$data['licensed_treatment'] = $product->getLicensedTreatment();
/*			$data['image'] = $product->getMediaConfig()->getMediaUrl($product->getData('image'));
			$data['product_url'] = $product->getProductUrl();	
			$data['description'] = $product->getDescription();
*/			
			//stock attributes
			$stockItem = $product->getStockItem();
			$data['qty'] = $stockItem->getData('qty');
			$data['is_in_stock'] = $stockItem->getData('is_in_stock');
			/*$data['min_qty'] = $stockItem->getData('min_qty');
			$data['use_config_min_qty'] = $stockItem->getData('use_config_min_qty');
			$data['is_qty_decimal'] = $stockItem->getData('is_qty_decimal');
			$data['backorders'] = $stockItem->getData('backorders');
			$data['use_config_backorders'] = $stockItem->getData('use_config_backorders');
			$data['min_sale_qty'] = $stockItem->getData('min_sale_qty');
			$data['use_config_min_sale_qty'] = $stockItem->getData('use_config_min_sale_qty');
			$data['max_sale_qty'] = $stockItem->getData('max_sale_qty');
			$data['use_config_max_sale_qty'] = $stockItem->getData('use_config_max_sale_qty');*/
			
			/*$data['low_stock_date'] = $stockItem->getData('low_stock_date');
			$data['notify_stock_qty'] = $stockItem->getData('notify_stock_qty');
			$data['use_config_notify_stock_qty'] = $stockItem->getData('use_config_notify_stock_qty');
			$data['manage_stock'] = $stockItem->getData('manage_stock');
			$data['use_config_manage_stock'] = $stockItem->getData('use_config_manage_stock');
			$data['stock_status_changed_auto'] = $stockItem->getData('stock_status_changed_auto');
			$data['use_config_qty_increments'] = $stockItem->getData('use_config_qty_increments');
			$data['qty_increments'] = $stockItem->getData('qty_increments');
			$data['use_config_enable_qty_inc'] = $stockItem->getData('use_config_enable_qty_inc');
			$data['enable_qty_increments'] = $stockItem->getData('enable_qty_increments');
			$data['is_decimal_divided'] = $stockItem->getData('is_decimal_divided');
			$data['stock_status_changed_automatically'] = $stockItem->getData('stock_status_changed_automatically');
			$data['use_config_enable_qty_increments'] = $stockItem->getData('use_config_enable_qty_increments');*/
			
			
			
		//	$data['size'] = $product->getSize();
			
			$products_row[] = $data;
			$product->clearInstance(); 
					
		}

			
		
		$headrs = array("category_ids","sku","name","price",'udropship_vendor','licensed_treatment','qty','is_in_stock');
		
		 array_unshift($products_row,$headrs);
		
	
		
		$mage_csv->saveData($file_path,$products_row);
		echo "Successfully $products exported at:-$file_path";
		
		