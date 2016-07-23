<?php

ini_set('memory_limit','512M');
set_time_limit(0);

require_once 'app/Mage.php';
Mage::app();
//set store id according to the requirement
$store_id=2;
$fh = fopen('groupproduct_attributes.csv', 'w+');
$header_array[0] = 'sku';
$header_array[1] = 'associated';
fputcsv($fh, $header_array);
$productConfig = Mage::getResourceModel('catalog/product_collection')->addAttributeToFilter('type_id', 'grouped')->addStoreFilter($store_id);
 
		foreach($productConfig as $product)
		{ 
		foreach ($product->getGroupedLinkCollection() as $linkprod) {
   $prod=Mage::getModel('catalog/product')->load($linkprod->getData('linked_product_id'));
  $associatedsku[]= $prod->getSku();
   
  }
  print_r($associatedsku);
  exit;
			$associatedProducts = $product->getTypeInstance(true)->getAssociatedProducts($product);
			 $sku=$product->getData('sku');			
			 $associatedsku=Array();
			 foreach($associatedProducts as $associatedProducts){  
			 print_r($associatedProducts->getData());		
				    $associatedsku[] = $associatedProducts->getData('sku') ;
			 }  
			 echo '----';
			 print_r($associatedsku);
			 
			$associated=implode(',',$associatedsku);			
			$row['sku'] = $sku;
			
			$row['associated'] = $associated;
			fputcsv($fh, $row);
		}
?>
