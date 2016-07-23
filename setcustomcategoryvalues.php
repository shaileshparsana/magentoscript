<?php

ini_set('memory_limit','512M');
set_time_limit(0);

require_once 'app/Mage.php';
Mage::app();
//set store id according to the requirement
$store_id=5;


	$fileName = 'customcategorydetails1.csv';
	$target_path = Mage::getBaseDir() .'/'.$fileName;

if( file_exists( $target_path ) ) {
		 $handle = fopen($target_path, "r");
		 while ( ($data = fgetcsv($handle, 10000, ",") ) !== FALSE ) {
			$number_of_fields = count($data);
			if( $data[0]!='category_name')
			{
			
			 $catname=$data[0];
			 $catids=Array();
		
				$cat = Mage::getResourceModel('catalog/category_collection')->addFieldToFilter('name', $catname);
				$catids=$cat->getFirstItem()->getEntityId();
				if($catids==16)
				$catids=143;
				echo $catids;
				echo '<br />';
				$_category = Mage::getModel('catalog/category')->load($catids);
				$_category->setIncludeInMenu($data[1]);
				
				$_category->save();
			}
			$cnt++; 	
		 }
	}
	
	
?>
