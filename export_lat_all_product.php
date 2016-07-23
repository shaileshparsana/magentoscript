<?php   
	
	set_time_limit(0); 
	ini_set('memory_limit', '-1'); 
	$rootdir = dirname(__DIR__);
	require_once '../app/Mage.php';
	umask(0);
	Mage::app();
	$file_path ='export_lat_all_product.csv'; //file path of the CSV file in which the $data to be saved

	$start = $_POST['start_id']; 
	$end = $_POST['end_id'];

	$mage_csv = new Varien_File_Csv(); //mage CSV
	
		
			$products_ids = Mage::getModel('catalog/product')->getCollection()
							->addAttributeToSelect('*')
							->addWebsiteFilter(5);
							//->addAttributeToFilter(array(array('attribute'=>'type_id','eq' => 'configurable'))); 

			$products_model = Mage::getModel('catalog/product');
					//get $products model
		
			

		$products_row = array();    
		
		foreach ($products_ids as $pid)
		{
			$product = $products_model->load($pid->getId());
			
			$data['sku'] = $product->getSku();
			$data['category_ids'] = join(',', $product->getCategoryIds());
			$data['name'] = $product->getName();
			$data['visibility'] = $product->getVisibility();
			$data['store'] = $product->getStoreId();
			$data['status'] = $product->getStatus();
			$data['type'] = $product->getTypeId();
			$data['url_key'] = Mage::getResourceSingleton('catalog/product')->getAttributeRawValue($pid->getId(), 'url_key', Mage::app()->getStore());
			$data['websites'] = join(',', $product->getWebsiteIds());
			
			
			//$childIds = Mage::getModel('catalog/product_type_configurable')->getChildrenIds($product->getId());
			//$configurable= Mage::getModel('catalog/product_type_configurable')->setProduct($product);
//$simpleCollection = $configurable->getUsedProductCollection()->addAttributeToSelect('*')->addFilterByRequiredOptions();

			//foreach($simpleCollection as $child) {
				//print_r($child->getName());  // You can use any of the magic get functions on this object to get the value
				
				/*$data['sku'] = $child->getSku();
				$data['category_ids'] = join(',', $child->getCategoryIds());
				$data['name'] = $child->getName();
				$data['visibility'] = $child->getVisibility();*/
				
				
	/*			$data['image'] = $product->getMediaConfig()->getMediaUrl($product->getData('image'));
				$data['product_url'] = $product->getProductUrl();	
				$data['description'] = $product->getDescription();
	*/			
				//stock attributes
			//	$stockItem = $product->getStockItem();
			//	$data['qty'] = $stockItem->getData('qty');
			//	$data['is_in_stock'] = $stockItem->getData('is_in_stock');
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
			//}
			
					
		}

		
		$headrs = array("sku","category_ids","name","visibility","store","status","type","url_key","websites");
		
		 array_unshift($products_row,$headrs);
		
	
		
		$mage_csv->saveData($file_path,$products_row);
		echo "Successfully $products exported at:-$file_path";
		
		