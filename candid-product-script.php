<?php 
require_once 'app/Mage.php'; 
Mage::app();
$_productCollection = Mage::getModel('catalog/product')
                        ->getCollection()
                        ->addAttributeToSort('created_at', 'ASC')
                        ->addAttributeToSelect('*')
                        ->load();
if (count($_productCollection)>0) {
	$outputFile = "candid-products-format.csv";
	$write = fopen($outputFile, 'w+');
$header_array[0] = 'id';
$header_array[1] = 'label';
$header_array[2] = 'url';
$header_array[3] = 'headline';
$header_array[4] = 'image_url';
fputcsv($write, $header_array);
foreach ($_productCollection as $_product){
		$row['id'] = $_product->getSku();
		$row['label'] = $_product->getName().' '. $_product->getPrice();
		$row['url'] = $_product->getProductUrl();
		$row['headline'] = "Shop";
		if($_product->getImage() && $_product->getImage()!="no_selection")
		$row['image_url'] = Mage::getModel('catalog/product_media_config')->getMediaUrl( $_product->getImage());		
		else
		$row['image_url'] = "";		
		fputcsv($write, $row);
}
}
?>