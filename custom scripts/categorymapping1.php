<?php

ini_set('memory_limit','512M');
set_time_limit(0);

require_once 'app/Mage.php';
Mage::app();
//set store id according to the requirement
$store_id=0;


	$fileName = 'categories_name.csv';
	$target_path = Mage::getBaseDir() .'/'.$fileName;
	$fh = fopen('categories_ids.csv', 'w+');
	$header_array[0] = 'sku';
	$header_array[1] = 'categories';
	fputcsv($fh, $header_array);
if( file_exists( $target_path ) ) {
		 $handle = fopen($target_path, "r");
		 while ( ($data = fgetcsv($handle, 10000, ",") ) !== FALSE ) {
			$number_of_fields = count($data);
			if( $data[1]!='categories' && $data[1]!='')
			{
			echo $data[1];
			 $catname=explode(',',$data[1]);
			 $catids=Array();
			 foreach($catname as $catname)
			 {
				$cat = Mage::getResourceModel('catalog/category_collection')->addFieldToFilter('name', $catname);
				echo $catids[]=$cat->getFirstItem()->getEntityId();
			}
					$categories=implode(',',$catids);
					$row['sku'] = $data[0];
					$row['categories'] = $categories;
					fputcsv($fh, $row);
			}
			$cnt++; 	
		 }
	}
	
	
?>
