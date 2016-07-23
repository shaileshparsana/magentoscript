<?php   
	      
	set_time_limit(0); 
	ini_set('memory_limit', '-1'); 
	$rootdir = dirname(__DIR__);
	require_once $rootdir.'/app/Mage.php';
	umask(0);
	Mage::app();
	init($start,$end);
	function init($start,$end)
	{ 

		$file_path = $_SERVER['DOCUMENT_ROOT'].'/custom_script/export_all_products_dw_store_cat.csv'; //file path of the CSV file in which the $data to be saved
		
		$start = $_POST['start_id']; 
		$end = $_POST['end_id'];

		$mage_csv = new Varien_File_Csv(); //mage CSV
	
		
		$products_ids = Mage::getModel('catalog/product')->getCollection()->addWebsiteFilter(4);
		//$products_ids->addAttributeToFilter('entity_id',array('from' => 43,'to' => $end));

		$products_model = Mage::getModel('catalog/product'); //get $products model
		$products_row = array();    
		
		foreach ($products_ids as $pid)
		{
			$product = $products_model->load($pid->getId());
			$stockItem = $product->getStockItem();
			$data['store'] = 'Admin';
			$data['websites'] = Mage::app()->getWebsite(4)->getName();
			$data['attribute_set'] =  Mage::getModel('eav/entity_attribute_set')->load($product->getAttributeSetId())->getAttributeSetName();
			$data['type'] = $product->getTypeId();
			$data['category_ids'] =implode(',',$product->getCategoryIds());
			$data['sku'] = $product->getSku();
			$data['name'] = $product->getName();
			$data['meta_title'] = $product->getMetaTitle();
			$data['price'] = $product->getPrice();
			$data['special_price'] = $product->getSpecialPrice();
			$data['weight'] = $product->getWeight();
			$attr = $products_model->getResource()->getAttribute("manufacturer");
			$manufacturer_label = '';
			if ($attr->usesSource()) {
				$manufacturer_label = $attr->getSource()->getOptionText($product->getManufacturer());
			} 
			$data['manufacturer'] = $manufacturer_label;
			$status = array(1=>'Enabled',2=>'Disabled');
			$data['status'] = $status[$product->getStatus()];
			$visible = array(1=>'Not Visible Individually',2=>'Catalog',3=>'Search',4=>'Catalog, Search');
			$data['visibility'] = $visible[$product->getVisibility()];
			$taxclass = array(0=>'None',1=>'default',2=>'Taxable Goods',4=>'Shipping');
			$data['tax_class_id'] = $taxclass[$product->getTaxClassId()];
			$data['description'] = $product->getDescription();
			$data['short_description'] = $product->getShortDescription();
			$data['qty'] = $stockItem->getData('qty');
			$data['is_in_stock'] = $stockItem->getData('is_in_stock');
			$data['product_name'] = $product->getProductName();
			$data['store_id'] = $product->getStoreId();
			$data['product_type_id'] = $product->getTypeId();
			$data['width'] = $product->getWidth();
			$attr = $products_model->getResource()->getAttribute("color");
			$color_label = '';
			if ($attr->usesSource()) {
				$color_label = $attr->getSource()->getOptionText($product->getColor());
			} 
			$data['color'] = $color_label;
			$productType = $product->getTypeId();
			$attributeCode = '';
			$associated='';

			if($product->getTypeId() == "configurable")
			{
				
				$_product = Mage::getModel('catalog/product')->load($pid->getId());
				$superattribute=Array();
				$associatedsku=Array();
				$childProducts = Mage::getModel('catalog/product_type_configurable')
						->getUsedProducts(null,$_product); 	
						$configurableAttributeCollection=$_product->getTypeInstance()->getConfigurableAttributes();  
				foreach($configurableAttributeCollection as $attribute){  
					 $superattribute[] = $attribute->getProductAttribute()->getAttributeCode();
				 }  
						 
				foreach($childProducts as $child) {
					 $associatedsku[]= $child->getSku(); 
				}
			
				$attributeCode=implode(',',$superattribute);
				$associated=implode(',',$associatedsku);
			}
			   $data['Simples_Skus'] = $associated;
			    $data['configurable_attributes'] = $attributeCode;
			
			//$data['enable_googlecheckout'] = $product->getEnableGooglecheckout();
			//$data['size'] = $product->getSize();
			//$attr = $products_model->getResource()->getAttribute("fabric");
			//$attrBrands = $products_model->getResource()->getAttribute("brands");
			// $brandsLabel = '';
			// if ($attrBrands->usesSource()) {
				// $brandsLabel = $attr->getSource()->getOptionText($product->getBrands());
			// } 
			//$data['brands'] = $brandsLabel;
			// $fabricLabel = '';
			// if ($attr->usesSource()) {
				// $fabricLabel = $attr->getSource()->getOptionText($product->getFabric());
			// } 
			//$data['category_ids'] = join(',', $product->getCategoryIds());
			//$data['has_options'] = $product->getHasOptions();
			//$data['meta_description'] = $product->getMetaDescription();
			//$data['meta_keyword'] = $product->getMetaKeyword();
			//$data['cost'] = $product->getCost();
			//$data['msrp'] = $product->getMsrp();
			// $colorGroupLabel = '';
			// if ($attr->usesSource()) {
				// $colorGroupLabel = $attr->getSource()->getOptionText($product->getColorGroup());
			// } 
			// $brandLabel = '';
			// if ($attr->usesSource()) {
				// $brandLabel = $attr->getSource()->getOptionText($product->getBrand());
			// } 
			//$data['image'] = $product->getImage();
			//$data['small_image'] = $product->getSmallImage();
			//$data['thumbnail'] = $product->getThumbnail();
			//$data['gallery'] = $product->getGallery();
			//$data['min_qty'] = $stockItem->getData('min_qty');
			//$data['min_qty_text'] = $product->getMinQtyText();
			//$data['names_number_colors'] = $product->getNamesNumberColors();
			//$data['material_features'] = $product->getMaterialFeatures();
			//$data['brand'] = $brandLabel;

			$products_row[] = $data;
			$product->clearInstance(); 
		
			
		} 
		$headrs = array("store","websites","attribute_set","type","category_ids","sku","name","meta_title","price","special_price","weight","manufacturer","status","visibility","tax_class_id","description","short_description","qty","is_in_stock","product_name","store_id","product_type_id","width","color","simples_skus","configurable_attributes");
		
		 array_unshift($products_row,$headrs);
		
	
		echo '<pre>';
		print_r($products_row);
		echo '</pre>';
		$mage_csv->saveData($file_path,$products_row);
		echo "Successfully $products exported at:-$file_path";
		
		}