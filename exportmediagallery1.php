<?php 
define('MAGENTO', realpath(dirname(__FILE__)));
	require_once MAGENTO . '/app/Mage.php';
	Mage::app();
 $file_path = MAGENTO . '/var/import/mediagallery.csv'; 
 
 $mage_csv = new Varien_File_Csv(); //mage CSV   
  
    $products_model = Mage::getModel('catalog/product')->getCollection()->addStoreFilter(1);//get products model
    $products_row = array();    
     
   foreach ($products_model as $prod)
 {
		$pid = $prod->getId(); 
		
        $prod = Mage::getModel('catalog/product')->load($pid);
		
		$smallimage =explode('/',$prod->getSmallImage());
		if($prod->getSmallImage()!='' && $smallimage[count($smallimage)-1]!='no_selection')
			$simage='+/'.$smallimage[count($smallimage)-1];
		else
			$simage='';
		$thumbimage =explode('/',$prod->getThumbnail());
		if($prod->getThumbnail()!='' && $thumbimage[count($thumbimage)-1]!='no_selection' )			
			$timage='+/'.$thumbimage[count($thumbimage)-1];
		else
		$timage='';
		$mainimage =explode('/',$prod->getImage());
		if($prod->getImage()!='' && $mainimage[count($mainimage)-1]!='no_selection')	
			$mimage='+/'.$mainimage[count($mainimage)-1];
		else
		$mimage='';	
		$mediaGallery = $prod->getMediaGallery();
            $mediaGallery = $mediaGallery['images'];
            $add_images = array();
            foreach ($mediaGallery as $add_image) {
                if (!$add_image['disabled']){	
					$name=explode('/',$add_image['file']);					
					$add_images[] ='/'.$name[count($name)-1];
                }
            }
			
   /* $mediaApi = Mage::getModel("catalog/product_attribute_media_api");
    $items = $mediaApi->items($prod->getId());
	$add_images = array();
	foreach($items as $item) {
		//$add_images .= $item['file'].';';
		$add_images[] = $item['file'];
	}*/
	

	$image = implode(';', $add_images);
        $data = array();
		$products_row[0]= array('sku','image','small_image','thumbnail','media_gallery');
		$data['sku'] = $prod->getSku();
		$data['image'] 		 =  $mimage;
		$data['small_image'] =  $simage;
		$data['thumbnail']   =  $timage;
		$data['media_gallery'] =  $image;
		
            
        $products_row[] = $data;                
 }   
     
      $mage_csv->saveData($file_path, $products_row); 
	  ?>
	  