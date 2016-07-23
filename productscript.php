<?php

ini_set('memory_limit','512M');
set_time_limit(0);

require_once 'app/Mage.php';
Mage::app();
//set store id according to the requirement
$store_id=2;
//$fh = fopen('groupproduct_attributes.csv', 'w+');
//$header_array[0] = 'sku';
//$header_array[1] = 'associated';
//fputcsv($fh, $header_array);
$productConfig = Mage::getResourceModel('catalog/product_collection')->addAttributeToSelect('entity_id')->addStoreFilter($store_id);
$readonce = Mage::getSingleton('core/resource')->getConnection('core_read');

		foreach($productConfig as $product)
		{ 
		
			$rows = $readonce->fetchAll("select * from st_catalog_product_entity_media_gallery where entity_id = '". $product->getData('entity_id') ."'");	
			echo '<pre />';
			print_r($rows);	
			//$row['sku'] = $sku;
			//print_r($associated);
			//$row['associated'] = $associated;
			//fputcsv($fh, $row);
		}
?>
