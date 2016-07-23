<?php

ini_set('memory_limit','512M');
set_time_limit(0);

require_once 'app/Mage.php';
Mage::app();
//set store id according to the requirement
$store_id=0;
$fh = fopen('categories_name.csv', 'w+');
$header_array[0] = 'sku';
$header_array[1] = 'categories';
fputcsv($fh, $header_array);
$productConfig = Mage::getResourceModel('catalog/product_collection')->addStoreFilter($store_id);
foreach($productConfig as $product)
{ 
			$categories = $product->getCategoryCollection()  
				->addAttributeToSelect('name');
			foreach($categories as $category) {
			  $cat_name[]=$category->getName();
			}
			$sku=$product->getData('sku');
			$categories=implode(',',$cat_name);
			$row['sku'] = $sku;
			$row['categories'] = $categories;
			fputcsv($fh, $row);
}
?>
