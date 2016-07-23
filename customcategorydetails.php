<?php

ini_set('memory_limit','512M');
set_time_limit(0);

require_once 'app/Mage.php';
Mage::app();
//set store id according to the requirement
$store_id=5;
$categoryRootId = Mage::app()->getStore($store_id)->getRootCategoryId();
$collection = Mage::getModel('catalog/category')->getCollection()
->addFieldToFilter('path', array('like' => "%/{$categoryRootId}/%"))
    ->addAttributeToSelect(array('include_in_menu','name'))
    ->setStoreId($store_id)
    ->load();
$fh = fopen('customcategorydetails1.csv', 'w+');
$header_array[0] = 'category_name';
$header_array[1] = 'include_in_menu';

fputcsv($fh, $header_array);
	foreach ($collection as $item) {
		
			$row['category_name'] = $item->getData('name');
			$row['include_in_menu'] =  $item->getData('include_in_menu');
			
			fputcsv($fh, $row);
			  //echo $item->getData('custom_fields_three_title'). '<br />';
		
			}
		
?>
