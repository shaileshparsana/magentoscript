<?php 

$rootdir = dirname(__DIR__);

require_once $rootdir.'\app/Mage.php';

	function addSimpleProudct($data)
	{
	
		$sku = trim($data[0]);
		$productdata = Mage::getModel('catalog/product')->loadByAttribute('sku',$sku);
		
		if ($productdata) 
		{
		
			$productdata->setWebsiteIds(array(Mage::app()->getStore(true)->getWebsite()->getId()));
			$attributeSetId = Mage::getSingleton('catalog/config')->getAttributeSetId('catalog_product', 'default'); 
			$productdata->setAttributeSetId($attributeSetId); // need to look this up
			
			$attributecode = 'color';
			$colorvalue = trim($data[5]);
			//setOrAddOptionAttribute($productdata,$attributecode,$colorvalue);
			
			$attribute = $productdata->getResource()->getAttribute($attributecode);
			$source = $attribute->getSource();
			$optionId = getSourceOptionId($source,$colorvalue);

			$productdata->setData($attributecode,$optionId)->save();
			$productdata->setCost($data[6]);
			$productdata->setCustomerFave($data[9]);
			$productdata->setPrice($data[38]);
			$productnames = trim($data[33]);
			$productdata->setName($productnames);
			
			$waistvalue = trim($data[46]);
			$attributecode = 'size_waist';
			//setOrAddOptionAttribute($productdata,$attributecode,$waistvalue);
			
			$attribute = $productdata->getResource()->getAttribute($attributecode);
			$source = $attribute->getSource();
			$optionId = getSourceOptionId($source,$waistvalue);

			$productdata->setData($attributecode,$optionId)->save();
			$productdata->setStatus($data[52]); // enabled
			$productdata->setTaxClassId($data[54]); // taxable goods
			$productdata->setRequiredOptions($data[42]);
			$visible = array('Not Visible Individually'=>1,'Catalog'=>2,'Search'=>3,'Catalog, Search'=>4);
			$productdata->setVisibility($visible[$data[60]]); // catalog, search
			
			/* Image update code */ 
			if(!empty($data[86]))
			{
				// check if image is already there.
				$mediaApi = Mage::getModel("catalog/product_attribute_media_api");
				$items = $mediaApi->items($productdata->getId());
				
				if(empty($items))
				{
					$seprateImages = explode(",",$data[86]);

					$j = 0;
					foreach($seprateImages as $images)
					{
						// j = 0 for if first image csv than we assign it as image', 'small_image', 'thumbnail
						if($j == 0)
						{
							$importImages = Mage::getBaseDir('media') . DS . 'imports/' .$images;
							if(file_exists($importImages))
							{

								$productdata->addImageToMediaGallery($importImages,array('image', 'small_image', 'thumbnail'),false,false);	
								
							}
							else
							{
								echo $sku."=>".$images," Image is not available",PHP_EOL;
								echo "<br/>"; 
							}
							
						}
						else
						{
							$importImages = Mage::getBaseDir('media') . DS . 'imports/' .$images;
							if(file_exists($importImages))
							{

								$productdata->addImageToMediaGallery($importImages,null,false,false);	
								
							}
							else
							{
								echo $sku."=>".$images," Image is not available",PHP_EOL;
								echo "<br/>"; 
							}
							
						}
						$j++;
						
					}	
				}	
			}	
			/* End here image upload code */
			
			$productdata->save();

			$productId = $productdata->getId();
			$stockItem =Mage::getModel('cataloginventory/stock_item')->loadByProduct($productId);
			$stockItemId = $stockItem->getId();

			$stockItem->setData('manage_stock', 1);
			$stockItem->setData('qty',$data[63]);

			$stockItem->save(); 

			echo $sku," Updated: Name: '",$data[33],"', Price: ",$data[38],", Stock level: ",$data[63],PHP_EOL;
			echo "<br/>"; 
		}
		else
		{
			$product = Mage::getModel('catalog/product');
			$productskus = trim($data[0]);
			$product->setSku($productskus);
			$product->setWebsiteIds(array(Mage::app()->getStore(true)->getWebsite()->getId()));
			$attributeSetId = Mage::getSingleton('catalog/config')->getAttributeSetId('catalog_product', 'default'); 
			$product->setAttributeSetId($attributeSetId); // need to look this up
			$product->setTypeId('simple');
			
			//using this option add product option value example :- color attribute add values
			$attributecode = 'color';
			$colorvalue = trim($data[5]);
			
			$attr = $product->getResource()->getAttribute($attributecode);
			if ($attr->usesSource()) {
				$color_id = $attr->getSource()->getOptionId($colorvalue);
			}
			$product->setData($attributecode,$color_id);
			
			
			$product->setCost($data[6]);
			$product->setCustomerFave($data[9]);
			$product->setPrice($data[38]);
			$product->setRequiredOptions($data[42]);
			$productnames = trim($data[33]);
			$product->setName($productnames);
			
			$attributecode1 = 'size_waist';
			$waistvalue = trim($data[46]);
			
			$attr = $product->getResource()->getAttribute($attributecode1);
			if ($attr->usesSource()) {
				$size_waist_id = $attr->getSource()->getOptionId($waistvalue);
			}
			$product->setData($attributecode1,$size_waist_id);

			/* Image update code */ 
			if(!empty($data[86]))
			{
				// check if image is already there.
			
				$seprateImages = explode(",",$data[86]);

				$j = 0;
				foreach($seprateImages as $images)
				{
					// j = 0 for if first image csv than we assign it as image', 'small_image', 'thumbnail
					if($j == 0)
					{
						$importImages = Mage::getBaseDir('media') . DS . 'imports/' .$images;
						if(file_exists($importImages))
						{

							$product->addImageToMediaGallery($importImages,array('image', 'small_image', 'thumbnail'),false,false);	
							
						}
						else
						{
							echo $sku."=>".$images," Image is not available",PHP_EOL;
							echo "<br/>"; 
						}
						
					}
					else
					{
						$importImages = Mage::getBaseDir('media') . DS . 'imports/' .$images;
						if(file_exists($importImages))
						{

							$product->addImageToMediaGallery($importImages,null,false,false);	
							
						}
						else
						{
							echo $sku."=>".$images," Image is not available",PHP_EOL;
							echo "<br/>"; 
						}
						
					}
					$j++;
					
				}	
					
			}	
			/* End here image upload code */
			
			//$product->setDescription($data[4]);
			//$product->setShortDescription('');
			
			$product->setStatus($data[52]); // enabled
			$product->setTaxClassId($data[54]); // taxable goods
			$visible = array('Not Visible Individually'=>1,'Catalog'=>2,'Search'=>3,'Catalog, Search'=>4);
			$product->setVisibility($visible[$data[60]]); // catalog, search
				
			// $product->setCategoryIds(array($categories[$data[11]])); // need to look these up
			//$product->setWeight($data[62]);
		
			$stockData = $product->getStockData();
			$stockData['qty'] = $data[63]; //18
			$stockData['is_in_stock'] = $data[73]; //17
			$stockData['manage_stock'] = $data[76];
			$stockData['use_config_manage_stock'] = $data[77];
			$stockData['min_qty'] =$data[64];
			$stockData['use_config_min_qty'] =$data[65];
			$stockData['min_sale_qty'] = $data[69];
			$stockData['max_sale_qty'] = $data[71];
			$stockData['use_config_max_sale_qty'] =$data[72];
			$stockData['is_qty_decimal'] =$data[66];
			$stockData['backorders'] =$data[67];
			$stockData['use_config_backorders'] =$data[68];
			$stockData['notify_stock_qty'] =$data[74];
			$stockData['use_config_notify_stock_qty'] =$data[75];
			$product->setStockData($stockData);
			try
			{
				$product->save();    
				echo $product->getId()," Imported: Name: '",$data[33],"', Sku: ",$data[0],PHP_EOL;
				echo "<br/>";
				$product->clearInstance(); 
			}
			catch (Mage_Core_Exception $e)
			{
				print_r($e);
				print_r($product);
			}
		}	
	}
	function addConfigurableProudct($data)
	{
		$sku = (string)$data[0];
		$productdata = Mage::getModel('catalog/product')->loadByAttribute('sku',$sku);
		if($productdata) 
		{
		
			$productnames = trim($data[33]);
			$productdata->setName($productnames);
			//$productdata->setDescription($data[4]);
			//$productdata->setShortDescription('');
			$productdata->setCustomerFave($data[9]);
			$visible = array('Not Visible Individually'=>1,'Catalog'=>2,'Search'=>3,'Catalog, Search'=>4);
			$visibilitydata = trim($data[60]);
			$productdata->setVisibility($visible[$visibilitydata]); // catalog, search
			$productdata->setCost($data[6]);
			$productdata->setColor($data[5]);
			$productdata->setSizeWaist($data[46]);
			$productdata->setTaxClassId($data[54]);		
			$productdata->setStatus($data[52]); // enabled
			
			/* Image update code */ 
			if(!empty($data[86]))
			{
				// check if image is already there.
				$mediaApi = Mage::getModel("catalog/product_attribute_media_api");
				$items = $mediaApi->items($productdata->getId());
				
				if(empty($items))
				{
					$seprateImages = explode(",",$data[86]);

					$j = 0;
					foreach($seprateImages as $images)
					{
						
						if($j == 0)
						{
							$importImages = Mage::getBaseDir('media') . DS . 'imports/' .$images;
							if(file_exists($importImages))
							{

								$productdata->addImageToMediaGallery($importImages,array('image', 'small_image', 'thumbnail'),false,false);	
								
							}
							else
							{
								echo $sku."=>".$images," Image is not available",PHP_EOL;
								echo "<br/>"; 
							}
							
						}
						else
						{
							$importImages = Mage::getBaseDir('media') . DS . 'imports/' .$images;
							if(file_exists($importImages))
							{

								$productdata->addImageToMediaGallery($importImages,null,false,false);	
								
							}
							else
							{
								echo $sku."=>".$images," Image is not available",PHP_EOL;
								echo "<br/>"; 
							}
							
						}
						$j++;
						
					}	
				}	
			}	
			/* End here image upload code */
			
			$productdata->save();  
			
			$configurableProduct = Mage::getModel('catalog/product')->loadByAttribute('sku',$sku);


			$simpleProducts = array();
				
			$simpleProductSkus = $data[83];
			
			$simpleSku = explode(",",$simpleProductSkus);
			
			$simpleproducts = count($simpleSku);

			$ids = $configurableProduct->getTypeInstance()->getUsedProductIds();
			
			
			$newids = array();
			$i = 0;
			foreach($simpleSku as $value)			
			{
				
				$simpleProduct = Mage::getModel('catalog/product')->loadByAttribute('sku',$value);
				if(!empty($simpleProduct))
				{
					$simpleId =  $simpleProduct->getId();
					$newids[$i] = $simpleId;
				}
				$i++;
				
			}
				
			$finalSimpleProducts = array_merge($ids,$newids);
			$result_ids =  array_unique($finalSimpleProducts);
			Mage::getResourceModel('catalog/product_type_configurable')->saveProducts($configurableProduct->getId(),$result_ids);
			echo $sku," Updated: Name: '",$data[33],PHP_EOL;
			echo "<br/>";
			
		}
		else
		{
			
			$productModel = Mage::getModel('catalog/product');	
			$product = Mage::getModel('catalog/product');
			$product->setTypeId('configurable');
			$product->setTaxClassId($data[54]);		
			$productskus = trim($data[0]);
			$product->setSku($productskus);
			$productnames = trim($data[33]);
			$product->setName($productnames);
			$product->setCustomerFave($data[9]);
			$product->setCost($data[6]);
			$product->setColor($data[5]);
			$product->setSizeWaist($data[46]);
			//$product->setDescription($data[4]);
			$product->setShortDescription('');
			$product->setPrice($data[38]);
			$product->setRequiredOptions($data[42]);
		 
			$attributeSetId = Mage::getSingleton('catalog/config')->getAttributeSetId('catalog_product', 'default'); 
			$product->setAttributeSetId($attributeSetId); // need to look this up
		   // $product->setCategoryIds(array($categories[$data[11]])); // need to look these up
			$product->setWeight($data[62]);
		  
			$visible = array('Not Visible Individually'=>1,'Catalog'=>2,'Search'=>3,'Catalog, Search'=>4);
			$visibilitydata = trim($data[60]);
			$product->setVisibility($visible[$visibilitydata]); // catalog, search
			$product->setStatus($data[52]); // enabled
			
			/* Image update code */ 
			if(!empty($data[86]))
			{
				
				// check if image is already there.
			
				$seprateImages = explode(",",$data[86]);

				$j = 0;
				foreach($seprateImages as $images)
				{
					// j = 0 for if first image csv than we assign it as image', 'small_image', 'thumbnail
					if($j == 0)
					{
						$importImages = Mage::getBaseDir('media') . DS . 'imports/' .$images;
						if(file_exists($importImages))
						{

							$product->addImageToMediaGallery($importImages,array('image', 'small_image', 'thumbnail'),false,false);	
							
						}
						else
						{
							echo $sku."=>".$images," Image is not available",PHP_EOL;
							echo "<br/>"; 
						}
						
					}
					else
					{
						$importImages = Mage::getBaseDir('media') . DS . 'imports/' .$images;
						if(file_exists($importImages))
						{

							$product->addImageToMediaGallery($importImages,null,false,false);	
							
						}
						else
						{
							echo $sku."=>".$images," Image is not available",PHP_EOL;
							echo "<br/>"; 
						}
						
					}
					$j++;
				}	
					
			}	
			/* End here image upload code */
			
			// assign product to the default website
			$product->setWebsiteIds(array(Mage::app()->getStore(true)->getWebsite()->getId()));
			
			
				$product->setCanSaveConfigurableAttributes(true);
				$product->setCanSaveCustomOptions(true);
	 
				
				// Now we need to get the information back in Magento's own format, and add bits of data to what it gives us..
				//("size", "color","material"); // etc..
				$configurableAttributes = trim($data[85]);
				$configAttributes = explode(",",$configurableAttributes);
				foreach($configAttributes as $attrCode)
				{
	 
						$super_attribute= Mage::getModel('eav/entity_attribute')->loadByCode('catalog_product',$attrCode);
						$configurableAtt = Mage::getModel('catalog/product_type_configurable_attribute')->setProductAttribute($super_attribute);
				 
						$newAttributes[] = array(
						   'id'             => $configurableAtt->getId(),
						   'label'          => $configurableAtt->getLabel(),
						   'position'       => $super_attribute->getPosition(),
						   'values'         => $configurableAtt->getPrices() ? $configProduct->getPrices() : array(),
						   'attribute_id'   => $super_attribute->getId(),
						   'attribute_code' => $super_attribute->getAttributeCode(),
						   'frontend_label' => $super_attribute->getFrontend()->getLabel(),
						);
				}
				
				// Add it back to the configurable product..
				$product->setConfigurableAttributesData($newAttributes);
				
				$simpleProducts = array();
				
				$simpleProductSkus = $data[83];
				
				$simpleSku = explode(",", $simpleProductSkus);
				
				
			
				foreach($simpleSku as $value)			
				{
					$simpleProduct = Mage::getModel('catalog/product')->loadByAttribute('sku',$value);
					if($simpleProduct)
					{
						array_push(
								$simpleProducts,
								array(
									"id" => $simpleProduct->getId(),
									"price" => $simpleProduct->getPrice(),
									 //"attr_code" => 'color',
									// "attr_id" => 272,
									// "value" => 59,
									// "label" => 'Brown'
								)
							);
					}	
				}	
			
				 
				$dataArray = array();
				foreach ($simpleProducts as $simpleArray)
				{
					$dataArray[$simpleArray['id']] = array();
					foreach ($newAttributes as $attrArray) {
						array_push(
							$dataArray[$simpleArray['id']],
							array(
							
								"attribute_id" => $simpleArray['attr_id'],
								"label" => $simpleArray['label'],
								"is_percent" => false,
								"pricing_value" => $simpleArray['price']
							)
						);
					}
				}
				
				// Set stock data. Yes, it needs stock data. No qty, but we need to tell it to manage stock, and that it's actually
				// in stock, else we'll end up with problems later..
				$product->setStockData(array(
					'use_config_manage_stock' => 1,
					'is_in_stock' => 1,
					'is_salable' => 1
				));
				
				
				$product->setConfigurableProductsData($dataArray);
				$product->setConfigurableAttributesData($newAttributes);
			
				try
				{
					$product->save();    
					print "Product Id: ".$product->getId()."::"."Product Sku: ".$product->getSku()."<br/>";
					$product->clearInstance(); 
				}
				catch (Mage_Core_Exception $e)
				{
					print_r($e);
					print_r($product);
				} 
		}		
	}		
	
	//this function is used to set dropdown value
	function getSourceOptionId(Mage_Eav_Model_Entity_Attribute_Source_Interface $source, $value)
	{
		foreach ($source->getAllOptions() as $option)
		{
			if (strcasecmp($option['label'], $value) == 0)
			{
				return $option['value'];
			}
		}
		return null;
	}
	function setOrAddOptionAttribute($arg_attribute, $arg_value) 
	{
		$attribute_model = Mage::getModel('eav/entity_attribute');
		$attribute_options_model = Mage::getModel('eav/entity_attribute_source_table');

		$attribute_code = $attribute_model->getIdByCode('catalog_product', $arg_attribute);
		$attribute = $attribute_model->load($attribute_code);

		$attribute_options_model->setAttribute($attribute);
		$options = $attribute_options_model->getAllOptions(false);

		// determine if this option exists
		$value_exists = false;
		foreach($options as $option) {
			if ($option['label'] == $arg_value) {
				$value_exists = true;
				break;
			}
		}
		// if this option does not exist, add it.
		if (!$value_exists) {
			if(!empty($arg_value))
			{
				$attribute->setData('option', array(
					'value' => array(
						'option' => array($arg_value,$arg_value)
					)
				));
				$attribute->save();
				echo "attribute created ".$arg_value." <br/>";
			}	
		}
	}
?>	