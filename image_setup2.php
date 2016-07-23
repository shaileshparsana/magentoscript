<?php 
ini_set('memory_limit','1024M');
set_time_limit(0); 
require_once 'app/Mage.php';
Mage::app();
umask(0);
					

$target_file  = 'produts1.csv';

if (($handle = fopen($target_file, "r")) !== FALSE) 
{
	while (($data = fgetcsv($handle, 10000, ",")) !== FALSE)
	{
		if($data[0] == 'Sku') continue;
		try
		{
		
			$pId = Mage::getModel("catalog/product")->getIdBySku($data[0]); 
			
			$product=Mage::getModel('catalog/product')->load($pId);
			$mediaGallery = $product->getMediaGallery();
//if there are images
if (isset($mediaGallery['images'])){
echo $pId;
echo '<br />';
    //loop through the images
    foreach ($mediaGallery['images'] as $image){
        //set the first image as the base image
        Mage::getSingleton('catalog/product_action')->updateAttributes(array($product->getId()), array('image'=>$image['file'],'small_image'=>$image['file'],'thumbnail'=>$image['file']), 0);
        //stop
        break;
    }
}
			
		}
		catch (Exception $e)
		{
			echo "Could not delete product with ID: ". $pId ."<br />";
		}
		

		$row++;
		
	}

	fclose($handle);		
	
}

?>