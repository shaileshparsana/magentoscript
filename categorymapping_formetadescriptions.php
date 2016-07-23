<?php

ini_set('memory_limit','512M');
set_time_limit(0);

require_once 'app/Mage.php';
Mage::app();

//set store id according to the requirement
$store_id=2;

      $category = Mage::getModel('catalog/category');
	   $category->setStoreId(2);	
		$category=Mage::getModel('catalog/category')->load(146);
	
$general['meta_title'] = "My Category"; //Page title
$general['meta_keywords'] = "My  Category";
$general['meta_description'] = "Some description to be found by meta search robots. 2";
 
//$general['url_key'] = "cars";//url to be used for this category's page by magento.
//$general['image'] = "cars.jpg";
 
 
$category->addData($general);
 
try {
    $category->save();
    echo "Success! Id: ".$category->getId();
}
catch (Exception $e){
    echo $e->getMessage();
}			
				
				exit;


	$fileName = 'metadescriptions1.csv';
	$target_path = Mage::getBaseDir() .'/'.$fileName;
	$fh = fopen('categories_ids.csv', 'w+');
	$header_array[0] = 'sku';
	$header_array[1] = 'categories';
	fputcsv($fh, $header_array);
if( file_exists( $target_path ) ) {
		 $handle = fopen($target_path, "r");
		 while ( ($data = fgetcsv($handle, 10000, ",") ) !== FALSE ) {
			$number_of_fields = count($data);
//			if( $data[1]!='categories' && $data[1]!='')
//			{
//			echo $data[1];
//			 $catname=explode(',',$data[1]);
//			 $catids=Array();
//			 foreach($catname as $catname)
//			 {
//				$cat = Mage::getResourceModel('catalog/category_collection')->addFieldToFilter('name', $catname);
//				echo $catids[]=$cat->getFirstItem()->getEntityId();
//			}



		$cat = Mage::getResourceModel('catalog/category_collection')->addFieldToFilter('name', $data[0])->addAttributeToSelect('entity_id');
			 foreach($cat as $cat1)
		 		{
		
				$category=Mage::getModel('catalog/category')->load($cat1->getData('entity_id'));
				echo $data[3];
				$category->setMetaKeywords($data[3]);
				  $category->save();
				}
		//	echo $cat->getName();
			//exit;
//					$categories=implode(',',$catids);
//					$row['sku'] = $data[0];
//					$row['categories'] = $categories;
//					fputcsv($fh, $row);
			}
			$cnt++; 	
		// }
		 try {

                  $category->save();

                  echo "Suceeded <br /> ";

              }

              catch (Exception $e){

                    echo "Failed <br />";
 
              }

	}
	
	
?>
