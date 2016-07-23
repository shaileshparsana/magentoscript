<?php

ini_set('memory_limit','512M');
set_time_limit(0);

require_once 'app/Mage.php';
Mage::app();
//set store id according to the requirement
$store_id=0;
$fh = fopen('configproduct_attributes.csv', 'w+');
$header_array[0] = 'sku';
$header_array[1] = 'config_attributes';
$header_array[2] = 'associated';
fputcsv($fh, $header_array);
$productConfig = Mage::getResourceModel('catalog/product_collection')->addAttributeToFilter('type_id', 'configurable')->addStoreFilter($store_id);
foreach($productConfig as $product)
{ 
			$sku=$product->getData('sku');
			$superattribute=Array();
			 $associatedsku=Array();
			$childProducts = Mage::getModel('catalog/product_type_configurable')
                    ->getUsedProducts(null,$product); 	
					$configurableAttributeCollection=$product->getTypeInstance()->getConfigurableAttributes();  
			 foreach($configurableAttributeCollection as $attribute){  
				 $superattribute[] = $attribute->getProductAttribute()->getAttributeCode()  ;
			 }  
					 
			foreach($childProducts as $child) {
				 $associatedsku[]= $child->getSku(); 
			}

$superattr=implode(',',$superattribute);
$associated=implode(',',$associatedsku);


$row['sku'] = $sku;
$row['config_attributes'] = $superattr;
$row['associated'] = $associated;
fputcsv($fh, $row);
}
?>
