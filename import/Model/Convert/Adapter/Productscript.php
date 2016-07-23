<?php
/**
* Product_import.php
* 
* @copyright  copyright (c) 2009 toniyecla[at]gmail.com
* @license    http://opensource.org/licenses/osl-3.0.php open software license (OSL 3.0)
*/

class Mage_Catalog_Model_Convert_Adapter_Productscript extends Mage_Catalog_Model_Convert_Adapter_Product
{
 private function _editTierPrices(&$product, $tier_prices_field = false)
    {  
        if (($tier_prices_field) && !empty($tier_prices_field)) {             
            if(trim($tier_prices_field) == 'REMOVE')
			{
                $product->setTierPrice(array());
            } 
			else 
			{
                $existing_tps = $product->getTierPrice();                
                $etp_lookup = array();
                //make a lookup array to prevent dup tiers by qty
                foreach($existing_tps as $key => $etp){
                    $etp_lookup[intval($etp['price_qty'])] = $key;
                }
                
                //parse incoming tier prices string
                $incoming_tierps = explode('|',$tier_prices_field);
$tps_toAdd = array();                
foreach($incoming_tierps as $tier_str){
                    if (empty($tier_str)) continue;
                    
                    $tmp = array();
                    $tmp = explode('=',$tier_str);
                    
                    if ($tmp[0] == 0 && $tmp[1] == 0) continue;
                    
                    $tps_toAdd[$tmp[0]] = array(
                                        'website_id' => 0, // !!!! this is hard-coded for now
                                        'cust_group' => 32000, // !!! so is this
                                        'price_qty' => $tmp[0],
                                        'price' => $tmp[1],
                                        'delete' => ''
                                    );
                                    
                    //drop any existing tier values by qty
                    if(isset($etp_lookup[intval($tmp[0])])){
                        unset($existing_tps[$etp_lookup[intval($tmp[0])]]);
                    }
                    
                }

                //combine array
                $tps_toAdd =  array_merge($existing_tps, $tps_toAdd);
               
                //save it
                $product->setTierPrice($tps_toAdd);
                //$product->setData('tier_price', $tps_toAdd);
            }            
        }        
    }  
 	/**
    * update product (import)
    * 
    * @param array $importData 
    * @throws Mage_Core_Exception
    * @return bool 
    */
	public function updateRow( array $importData )
    {
		$product = $this -> getProductModel();
        $product -> setData( array() );        
        if ( $stockItem = $product -> getStockItem() ) {
            $stockItem -> setData( array() );
            } 
       
      	 $store = $this -> getStoreByCode( 'admin' );
           
        if ( empty( $importData['sku'] ) ) {
			
			$message = Mage :: helper( 'catalog' ) -> __( 'Skip import row, required field "%s" not defined', 'sku' );
			
			return $message;
            } 
			
        $product -> setStoreId( $store -> getId() );
        $productId = $product -> getIdBySku( $importData['sku'] );
        $new = true; // fix for duplicating attributes error
        if ( $productId ) {
            $product -> load( $productId );
            $new = false; // fix for duplicating attributes error
		}
		else
		{
			$message = Mage :: helper( 'catalog' ) -> __( 'Skip import row, product is not defined.' );
			return $message;
		}
        $productTypes = $this -> getProductTypes();
        $productAttributeSets = $this -> getProductAttributeSets();
        
		if(!empty($importData['type'] ))
		{
			$product -> setTypeId( $productTypes[strtolower( $importData['type'] )] );
		}
        if(!empty($importData['attribute_set'] ))
		{
        	$product -> setAttributeSetId( $productAttributeSets[$importData['attribute_set']] );
		}
        
        foreach ( $this -> _requiredFields as $field ) {
            $attribute = $this -> getAttribute( $field );
            } 
			
       if ( isset( $importData['category_ids'] ) ) {
			$category_Ids = explode(',',$importData['category_ids']);
            $product -> setCategoryIds( $category_Ids );
            } 
        
        if ( isset( $importData['categories'] ) ) {
            
            if ( isset( $store ) ) {
                $cat_store = $this -> _stores[$store];
                } else {
                $message = Mage :: helper( 'catalog' ) -> __( 'Skip import row, required field "store" for new products not defined', $field );
				return $message;
                //Mage :: throwException( $message );
                }             
            $categoryIds = $this -> _addCategories( $importData['categories'], $cat_store );
            if ( $categoryIds ) {
                $product -> setCategoryIds( $categoryIds );
                } 
            
            } 
        
        foreach ( $this -> _ignoreFields as $field ) {
            if ( isset( $importData[$field] ) ) {
                unset( $importData[$field] );
                } 
            } 
        
        if ( $store -> getId() != 0 ) {
            $websiteIds = $product -> getWebsiteIds();
            if ( !is_array( $websiteIds ) ) {
                $websiteIds = array();
                } 
            if ( !in_array( $store -> getWebsiteId(), $websiteIds ) ) {
                $websiteIds[] = $store -> getWebsiteId();
                } 
            $product -> setWebsiteIds( $websiteIds );
            } 
        
        if ( isset( $importData['websites'] ) ) {
            $websiteIds = $product -> getWebsiteIds();
            if ( !is_array( $websiteIds ) ) {
                $websiteIds = array();
                } 
           
			 $websiteCodes = explode( ',', $importData['websites'] );
		
            foreach ( $websiteCodes as $websiteCode ) {
                try {
                    $website = Mage :: app() -> getWebsite( trim( $websiteCode ) );
                    if ( !in_array( $website -> getId(), $websiteIds ) ) {
                        $websiteIds[] = $website -> getId();
                        } 
                    } 
                catch ( Exception $e ) {
                    } 
                } 
            $product -> setWebsiteIds( $websiteIds );
            unset( $websiteIds );
            }         
        foreach ( $importData as $field => $value ) {
            if ( in_array( $field, $this -> _inventoryFields ) ) { 
                continue;
                } 
            if ( in_array( $field, $this -> _imageFields ) ) {
                continue;
                } 
            
            $attribute = $this -> getAttribute( $field );
            if ( !$attribute ) {
                continue;
                } 

            
            $isArray = false;
            $setValue = $value;
            
            if ( $attribute -> getFrontendInput() == 'multiselect' ) {
                $value = explode( self :: MULTI_DELIMITER, $value );
                $isArray = true;
                $setValue = array();
                } 
            
            if ( $value && $attribute -> getBackendType() == 'decimal' ) {
                $setValue = $this -> getNumber( $value );
                } 
            
            if ( $attribute -> usesSource() ) {
                $options = $attribute -> getSource() -> getAllOptions( false );
                
                if ( $isArray ) {
                    foreach ( $options as $item ) {
                        if ( in_array( $item['label'], $value ) ) {
                            $setValue[] = $item['value'];
                            } 
                        } 
                    } 
                else {
                    $setValue = null;
                    foreach ( $options as $item ) {
                        if ( $item['label'] == $value ) {
                            $setValue = $item['value'];
                            } 
                        } 
                    } 
                } 
            
            $product -> setData( $field, $setValue );
            } 
        
        if ( !$product -> getVisibility() ) {
            $product -> setVisibility( Mage_Catalog_Model_Product_Visibility :: VISIBILITY_NOT_VISIBLE );
            } 
        
        $stockData = array();
        $inventoryFields = isset($this->_inventoryFieldsProductTypes[$product->getTypeId()])
            ? $this->_inventoryFieldsProductTypes[$product->getTypeId()]
            : array(); 
        foreach ( $inventoryFields as $field ) {
            if ( isset( $importData[$field] ) ) {
                if ( in_array( $field, $this -> _toNumber ) ) {
                    $stockData[$field] = $this -> getNumber( $importData[$field] );
                    } 
                else {
                    $stockData[$field] = $importData[$field];
                    } 
                } 
            } 
        $product -> setStockData( $stockData );
        
        // start external image url add  by shailesh patel 
		$mediaGalleryBackendModel = $this->getAttribute('media_gallery')->getBackend();
        $arrayToMassAdd = array();
		foreach ($product->getMediaAttributes() as $mediaAttributeCode => $mediaAttribute) {
			if (isset($importData[$mediaAttributeCode])) {
				$file = trim($importData[$mediaAttributeCode]);
				if (!empty($file) && !$mediaGalleryBackendModel->getImage($product, $file)) {
				// Start Of Code To Import Images From Urls
					if (preg_match('%https?://[a-z0-9\-./]+\.(?:jpeg|png|jpg|gif)%i', $file))
					{
						$path_parts = pathinfo($file);
						$html_filename = DS . $path_parts['basename'];
						$fullpath = Mage::getBaseDir('media') . DS . 'import' . $html_filename;
					
							if(!file_exists($fullpath)) {
								file_put_contents($fullpath, file_get_contents(trim($file)));
							}
							
							if(file_get_contents(trim(Mage::getBaseDir('media') . DS . 'import'.$html_filename))){
								$arrayToMassAdd[] = array('file' => trim($html_filename), 'mediaAttribute' => $mediaAttributeCode);
							}
					}
					else
					{
						if(file_get_contents(trim(Mage::getBaseDir('media') . DS . 'import'.$file))){
							$arrayToMassAdd[] = array('file' => trim($file), 'mediaAttribute' => $mediaAttributeCode);
						}
					}
				}
			}
		}
		if(!empty($arrayToMassAdd)){
			$addedFilesCorrespondence = $mediaGalleryBackendModel->addImagesWithDifferentMediaAttributes(
				$product,
				$arrayToMassAdd, Mage::getBaseDir('media') . DS . 'import',
				false,
				false
			);
		}


        foreach ($product->getMediaAttributes() as $mediaAttributeCode => $mediaAttribute) {
            $addedFile = '';
            if (isset($importData[$mediaAttributeCode . '_label'])) {
                $fileLabel = trim($importData[$mediaAttributeCode . '_label']);
                if (isset($importData[$mediaAttributeCode])) {
                    $keyInAddedFile = array_search($importData[$mediaAttributeCode],
                        $addedFilesCorrespondence['alreadyAddedFiles']);
                    if ($keyInAddedFile !== false) {
                        $addedFile = $addedFilesCorrespondence['alreadyAddedFilesNames'][$keyInAddedFile];
                    }
                }

                if (!$addedFile) {
                    $addedFile = $product->getData($mediaAttributeCode);
                }
                if ($fileLabel && $addedFile) {
                    $mediaGalleryBackendModel->updateImage($product, $addedFile, array('label' => $fileLabel));
                }
            }
        }
		// end external image url add  by shailesh patel 
		
		$imageData = array();
        foreach ( $this -> _imageFields as $field ) {
            if ( !empty( $importData[$field] ) && $importData[$field] != 'no_selection' ) {
                if ( !isset( $imageData[$importData[$field]] ) ) {
                    $imageData[$importData[$field]] = array();
                    } 
                $imageData[$importData[$field]][] = $field;
                } 
            } 
        	
        foreach ( $imageData as $file => $fields ) {
			 if(file_get_contents(trim(Mage::getBaseDir('media') . DS . 'import/'.$file))){
                $product -> addImageToMediaGallery( Mage :: getBaseDir( 'media' ) . DS . 'import/' . $file, $fields, false );
			 }
		} 
        
        if ( !empty( $importData['gallery'] ) ) {
            $galleryData = explode( ',', $importData["gallery"] );
            foreach( $galleryData as $gallery_img ) {
		    	if(file_get_contents(trim(Mage::getBaseDir('media') . DS . 'import'.$gallery_img))){
                    $product -> addImageToMediaGallery( Mage :: getBaseDir( 'media' ) . DS . 'import' . $gallery_img, null, false, false );
                 }
			} 
		}
		//set associated child products for configurable
            if ( isset( $importData['Simple_Skus'] ) ) {
				$product->setCanSaveConfigurableAttributes(true);
               	 $product -> setConfigurableProductsData( $this -> skusToIds( $importData['Simple_Skus'], $product ) );
                }        
		
		$product -> setIsMassupdate( true );
        $product -> setExcludeUrlRewrite( true );        
        $product -> save();		
		
		if ($importData['Simple_Skus']!="" &&  $importData['options']!="" ) {	
		$config_attr = explode(',',$importData["configurable_attributes"]);
			foreach($config_attr as $code)
			{
				$importData["config_attribute"]=$code;
				$this->saveConfPrice($importData);
			}
		}        
        return true;         
	}
	
    public function getAttributeOptionValue($arg_attribute, $arg_value) {
		$attribute_model        = Mage::getModel('eav/entity_attribute');
        $attribute_options_model= Mage::getModel('eav/entity_attribute_source_table') ;

        $attribute_code         = $attribute_model->getIdByCode('catalog_product', $arg_attribute);
        $attribute              = $attribute_model->load($attribute_code);

        $attribute_table        = $attribute_options_model->setAttribute($attribute);
        $options                = $attribute_options_model->getAllOptions(false);

        foreach($options as $option)
        {
            if ($option['label'] == $arg_value)
            {
                return $option['value'];
            }
        }

        return false;
	}
	
	public function addAttributeOption($arg_attribute, $arg_value, array $importData) { 
		$attribute_model        = Mage::getModel('eav/entity_attribute');

        $attribute_code         = $attribute_model->getIdByCode('catalog_product', $arg_attribute);
        $attribute              = $attribute_model->load($attribute_code);

        if(!$this->getAttributeOptionValue($arg_attribute, $arg_value))
        {
            $value['option'] = array($arg_value,$arg_value);
            $result = array('value' => $value);
            $attribute->setData('option',$result);
            $attribute->save();
        }

		$attribute_options_model= Mage::getModel('eav/entity_attribute_source_table') ;
        $attribute_table        = $attribute_options_model->setAttribute($attribute);
        $options                = $attribute_options_model->getAllOptions(false);

        foreach($options as $option)
        {
            if ($option['label'] == $arg_value)
            {
				
                return $option['value'];
            }
        }
       return false; 
	}
    
    public function saveRow( array $importData )
    {	
		
		if(!isset($importData['websites'])){
			$importData['websites'] = 'base';
		}
		$product = $this -> getProductModel();
        $product -> setData( array() );
        
        if ( $stockItem = $product -> getStockItem() ) {
            $stockItem -> setData( array() );
            } 
       
        if ( empty( $importData['store'] ) ) {
			$importData['store'] = 'admin';
			$store = $this -> getStoreByCode( $importData['store'] );
            /*if ( !is_null( $this -> getBatchParams( 'store' ) ) ) {				
	                $store = $this -> getStoreById( $this -> getBatchParams( 'store' ) );
                } else {					
					$message = Mage :: helper( 'catalog' ) -> __( 'Skip import row, required field "%s" not defined', 'store' );
					return $message;
                //Mage :: throwException( $message );
                }*/ 
            } else {
            $store = $this -> getStoreByCode( $importData['store'] );
            } 
         
        if ( $store === false ) {
            $message = Mage :: helper( 'catalog' ) -> __( 'Skip import row, store "%s" field not exists', $importData['store'] );
			return $message;
           // Mage :: throwException( $message );
            } 
        
        if ( empty( $importData['sku'] ) ) {
            $message = Mage :: helper( 'catalog' ) -> __( 'Skip import row, required field "%s" not defined', 'sku' );
           // Mage :: throwException( $message );
			return $message;
            } 
        
        $product -> setStoreId( $store -> getId() );
        $productId = $product -> getIdBySku( $importData['sku'] );
        $new = true; // fix for duplicating attributes error
        if ( $productId ) {
        	$product -> load( $productId );
			$websiteIds = $product->getWebsiteIds();
			$flag = 0;
			foreach($websiteIds as $id){
				$website = Mage::app()->getWebsite($id);
				
				if($website->getCode() == $importData['websites']){
					$flag = 1;
				}
			}
			/*if($flag == 1){
				$message = Mage :: helper( 'catalog' ) -> __( 'Skip import row,  "%s" sku is all ready defined', $importData['sku'] );
				return $message;
			}*/
			
			
			//cusotm code by bharat to stop duplicate images
			if($flag == 1){
			
			$attributes = $product->getTypeInstance()->getSetAttributes();
			if (isset($attributes['media_gallery'])) {
				$gallery = $attributes['media_gallery'];
			}
					if($product->getImage() != 'no_selection' && file_exists(Mage::getBaseDir('media') . DS . 'import'. $importData['image']))
					{
						 $mediaApi = Mage::getModel("catalog/product_attribute_media_api");
    					 $items = $mediaApi->items($product->getId());
							foreach ($items as $image)
							{
								if ($image['file'] == $product->getImage()){
									Mage::getSingleton('catalog/product_action')->updateAttributes(array($product->getId()), array('image'=>$image['file']), 0);
									$gallery->getBackend()->removeImage($product, $image['file']);
									$imageOldFile = Mage::getBaseDir('media') . DS . 'catalog' . DS . 'product' . $image['file'];
									if (file_exists($imageOldFile)) {
										unlink($imageOldFile);
									}
								}
							}
						
					
					}
					else
					{
						unset($importData['image']);
					}
					
					if($product->getSmallImage() != 'no_selection' && file_exists(Mage::getBaseDir('media') . DS . 'import'. $importData['small_image']))
					{
						 $mediaApi = Mage::getModel("catalog/product_attribute_media_api");
    					 $items = $mediaApi->items($product->getId());
							foreach ($items as $image)
							{
								if ($image['file'] == $product->getSmallImage()){
									Mage::getSingleton('catalog/product_action')->updateAttributes(array($product->getId()), array('small_image'=>$image['file']), 0);
									$gallery->getBackend()->removeImage($product, $image['file']);
									$imageOldFile = Mage::getBaseDir('media') . DS . 'catalog' . DS . 'product' . $image['file'];
									if (file_exists($imageOldFile)) {
										unlink($imageOldFile);
									}
								}
							}
						
					
					}
					else
					{
						unset($importData['small_image']);
					}
					
					
					
					
					if($product->getThumbnail() != 'no_selection' && file_exists(Mage::getBaseDir('media') . DS . 'import'. $importData['thumbnail']))
					{
						 $mediaApi = Mage::getModel("catalog/product_attribute_media_api");
    					 $items = $mediaApi->items($product->getId());
							foreach ($items as $image)
							{
								if ($image['file'] == $product->getThumbnail()){
									Mage::getSingleton('catalog/product_action')->updateAttributes(array($product->getId()), array('thumbnail'=>$image['file']), 0);
									$gallery->getBackend()->removeImage($product, $image['file']);
									$imageOldFile = Mage::getBaseDir('media') . DS . 'catalog' . DS . 'product' . $image['file'];
									if (file_exists($imageOldFile)) {
										unlink($imageOldFile);
									}
								}
							}
						
					
					}
					else
					{
						unset($importData['thumbnail']);
					}
					
					
			}
			//custom code end here
			$new = false; 
        } 
        $productTypes = $this -> getProductTypes();
        $productAttributeSets = $this -> getProductAttributeSets();   
         
        
        if ( empty( $importData['type'] ) || !isset( $productTypes[strtolower( $importData['type'] )] ) ) {
			$importData['type'] = 'simple';
            $product -> setTypeId( $productTypes[strtolower( $importData['type'] )] );
			/*$value = isset( $importData['type'] ) ? $importData['type'] : '';
            $message = Mage :: helper( 'catalog' ) -> __( 'Skip import row, is not valid value "%s" for field "%s"', $value, 'type' );
			return $message;*/
            } 
        $product -> setTypeId( $productTypes[strtolower( $importData['type'] )] );
        
        if ( empty( $importData['attribute_set'] ) || !isset( $productAttributeSets[$importData['attribute_set']] ) ) {
			 $importData['attribute_set'] = 'Default';
			 $product -> setAttributeSetId( $productAttributeSets[$importData['attribute_set']] );
            /*$value = isset( $importData['attribute_set'] ) ? $importData['attribute_set'] : '';
            $message = Mage :: helper( 'catalog' ) -> __( 'Skip import row, is not valid value "%s" for field "%s"', $value, 'attribute_set' );
		   	return $message;*/
            } 
        $product -> setAttributeSetId( $productAttributeSets[$importData['attribute_set']] );
        
        foreach ( $this -> _requiredFields as $field ) {
		
            $attribute = $this -> getAttribute( $field );
			
            if ( !isset( $importData[$field] ) && $attribute && $attribute -> getIsRequired() ) {
                $message = Mage :: helper( 'catalog' ) -> __( 'Skip import row, required field "%s" for new products not defined', $field );
                //Mage :: throwException( $message );
			//	$message['attribute_set'] = $message;
				return $message;
                } 
            } 
      
		
		
        if ( $importData['type'] == 'configurable' ) {
            $product -> setCanSaveConfigurableAttributes( true );
            $configAttributeCodes = $this -> userCSVDataAsArray( $importData['configurable_attributes'] );
            $usingAttributeIds = array();
            foreach( $configAttributeCodes as $attributeCode ) {
                $attribute = $product -> getResource() -> getAttribute( $attributeCode );
                if ( $product -> getTypeInstance() -> canUseAttribute( $attribute ) ) {
                    if ( $new ) { // fix for duplicating attributes error
                        $usingAttributeIds[] = $attribute -> getAttributeId();
                        } 
                    } 
                } 
            if ( !empty( $usingAttributeIds ) ) {
					$product -> getTypeInstance() -> setUsedProductAttributeIds( $usingAttributeIds );
					$product -> setConfigurableAttributesData( $product -> getTypeInstance() -> getConfigurableAttributesAsArray() );
					$product -> setCanSaveConfigurableAttributes( true );
					$product -> setCanSaveCustomOptions( true );
                }
				else
				{
					$message = Mage :: helper( 'catalog' ) -> __( 'Skip import row, configure attribute value not found' ,$importData['sku']);
					return $message;
				} 
            if ( isset( $importData['Simple_Skus'] ) ) {
               	 $product -> setConfigurableProductsData( $this -> skusToIds( $importData['Simple_Skus'], $product ) );
                } 
            } 
        
			
        if ( isset( $importData['related'] ) ) {
            $linkIds = $this -> skusToIds( $importData['related'], $product );
            if ( !empty( $linkIds ) ) {
                $product -> setRelatedLinkData( $linkIds );
                } 
            } 
        
        if ( isset( $importData['upsell'] ) ) {
            $linkIds = $this -> skusToIds( $importData['upsell'], $product );
            if ( !empty( $linkIds ) ) {
                $product -> setUpSellLinkData( $linkIds );
                } 
            } 
        
        if ( isset( $importData['crosssell'] ) ) {
            $linkIds = $this -> skusToIds( $importData['crosssell'], $product );
            if ( !empty( $linkIds ) ) {
                $product -> setCrossSellLinkData( $linkIds );
                } 
            } 
        
        if ( isset( $importData['grouped'] ) ) {
            $linkIds = $this -> skusToIds( $importData['grouped'], $product );
            if ( !empty( $linkIds ) ) {
                $product -> setGroupedLinkData( $linkIds );
                } 
            } 
        
        if ( isset( $importData['category_ids'] ) ) {
			$category_Ids = explode(',',$importData['category_ids']);
            $product -> setCategoryIds( $category_Ids );
            } 
        
        
        if ( isset( $importData['categories'] ) ) {
            
            if ( isset( $importData['store'] ) ) {
                $cat_store = $this -> _stores[$importData['store']];
                } else {
                $message = Mage :: helper( 'catalog' ) -> __( 'Skip import row, required field "store" for new products not defined', $field );
				return $message;
                //Mage :: throwException( $message );
                } 
            
            $categoryIds = $this -> _addCategories( $importData['categories'], $cat_store );
            if ( $categoryIds ) {
                $product -> setCategoryIds( $categoryIds );
                } 
            
            } 
        
        foreach ( $this -> _ignoreFields as $field ) {
            if ( isset( $importData[$field] ) ) {
                unset( $importData[$field] );
                } 
            } 
        
        if ( $store -> getId() != 0 ) {
            $websiteIds = $product -> getWebsiteIds();
            if ( !is_array( $websiteIds ) ) {
                $websiteIds = array();
                } 
            if ( !in_array( $store -> getWebsiteId(), $websiteIds ) ) {
                $websiteIds[] = $store -> getWebsiteId();
                } 
            $product -> setWebsiteIds( $websiteIds );
            } 
        
        if ( isset( $importData['websites'] ) ) {
            $websiteIds = $product -> getWebsiteIds();
            if ( !is_array( $websiteIds ) ) {
                $websiteIds = array();
                } 
           
			 $websiteCodes = explode( ',', $importData['websites'] );
		
            foreach ( $websiteCodes as $websiteCode ) {
                try {
                    $website = Mage :: app() -> getWebsite( trim( $websiteCode ) );
                    if ( !in_array( $website -> getId(), $websiteIds ) ) {
                        $websiteIds[] = $website -> getId();
                        } 
                    } 
                catch ( Exception $e ) {
                    } 
                } 
            $product -> setWebsiteIds( $websiteIds );
            unset( $websiteIds );
            }//added by rakesh jesadiya
			else{
				$importData['websites'] = 'base';
			    try {
                    $website = Mage :: app() -> getWebsite( trim( $websiteCode ) );
                    if ( !in_array( $website -> getId(), $websiteIds ) ) {
                        $websiteIds[] = $website -> getId();
                        } 
                    } 
                catch ( Exception $e ) {
                    }
				 $product -> setWebsiteIds( $websiteIds );
	             unset( $websiteIds ); 
			}
        
        foreach ( $importData as $field => $value ) {
			
            if ( in_array( $field, $this -> _inventoryFields ) ) { 
                continue;
                } 
            if ( in_array( $field, $this -> _imageFields ) ) {
                continue;
                } 
            
            $attribute = $this -> getAttribute( $field );
            if ( !$attribute ) {
                continue;
                } 

            
            $isArray = false;
            $setValue = $value;
            
            if ( $attribute -> getFrontendInput() == 'multiselect' ) {
                $value = explode( self :: MULTI_DELIMITER,  $value );
                $isArray = true;
                $setValue = array();
                } 
            if ( $value && $attribute -> getBackendType() == 'decimal' ) {
                $setValue = $this -> getNumber( $value );
                } 
            
        	
            if ( $attribute -> usesSource() ) {
                $options = $attribute -> getSource() -> getAllOptions( false );
                if ( $isArray ) {
                    foreach ( $options as $item ) {
                        if ( in_array( $item['label'], $value ) ) {
                            $setValue[] = $item['value'];
                            } 
                        } 
                    } 
                else {
                    $setValue = null;
                    foreach ( $options as $item ) {
                        if ( $item['label'] == $value ) {
                            $setValue = $item['value'];
                            } 
                        } 
                    } 
                }
			
			//custom attribute added by rakesh
			if($field == 'dairy_free' || $field == 'gluten_free' ||$field == 'low_salt' ||$field == 'non_gmo' || $field == 'organic' || $field == 'sugar_free' || $field == 'vegan'){
				if($value == 'N')
					$setValue = 0;
				else
					$setValue = 1;
			} 	
            $product -> setData( $field, $setValue );
            } 	
		
        if ( !$product -> getVisibility() ) {
				$product -> setVisibility( Mage_Catalog_Model_Product_Visibility :: VISIBILITY_BOTH );
				//$product -> setVisibility( Mage_Catalog_Model_Product_Visibility :: VISIBILITY_NOT_VISIBLE );
            } 
     
        $stockData = array();
        $inventoryFields = isset($this->_inventoryFieldsProductTypes[$product->getTypeId()])
            ? $this->_inventoryFieldsProductTypes[$product->getTypeId()]
            : array();
			
		//echo "<pre>";print_r($inventoryFields);exit;
        foreach ( $inventoryFields as $field ) {
            if ( isset( $importData[$field] ) ) {
                if ( in_array( $field, $this -> _toNumber ) ) {
                    $stockData[$field] = $this -> getNumber( $importData[$field] );
                    } 
                else {
                    $stockData[$field] = $importData[$field];
                    } 
                } 
            }
			
			if ( !isset( $importData['is_in_stock'] ) ) {
				$stockData['is_in_stock'] = '1';
			}
			if ( empty( $importData['qty'] ) ) {
				$stockData['use_config_manage_stock'] = 0;
			}
        $product -> setStockData( $stockData );
           
        // start external image url add  by shailesh patel 
		$mediaGalleryBackendModel = $this->getAttribute('media_gallery')->getBackend();
		
        $arrayToMassAdd = array();
		foreach ($product->getMediaAttributes() as $mediaAttributeCode => $mediaAttribute) {
			
			if (isset($importData[$mediaAttributeCode])) {
				$file = trim($importData[$mediaAttributeCode]);
				//echo $file;
				if (!empty($file) && !$mediaGalleryBackendModel->getImage($product, $file)) {
				// Start Of Code To Import Images From Urls
					if (preg_match('%https?://[a-z0-9\-./]+\.(?:jpeg|png|jpg|gif)%i', $file))
					{
						$path_parts = pathinfo($file);
						$html_filename = DS . $path_parts['basename'];
						$fullpath = Mage::getBaseDir('media') . DS . 'import' . $html_filename;
					
							if(!file_exists($fullpath)) {
								file_put_contents($fullpath, file_get_contents(trim($file)));
							}
							
							if(file_get_contents(trim(Mage::getBaseDir('media') . DS . 'import'.$html_filename))){
								$arrayToMassAdd[] = array('file' => trim($html_filename), 'mediaAttribute' => $mediaAttributeCode);
							}
					}
					else
					{
						if(file_get_contents(trim(Mage::getBaseDir('media') . DS . 'import'.$file))){
							
							$arrayToMassAdd[] = array('file' => trim($file), 'mediaAttribute' => $mediaAttributeCode);
							$deletefiles[]=trim($file);
					
						}
					}
				}
			}
		}
		//echo '<pre>';print_r($arrayToMassAdd);
		if(!empty($arrayToMassAdd)){
			$addedFilesCorrespondence = $mediaGalleryBackendModel->addImagesWithDifferentMediaAttributes(
				$product,
				$arrayToMassAdd, Mage::getBaseDir('media') . DS . 'import',
				false,
				true //this is for exclude true image options now images are not display in more view becase we have set true.
			);
		}
		
        foreach ($product->getMediaAttributes() as $mediaAttributeCode => $mediaAttribute) {
            $addedFile = '';
            if (isset($importData[$mediaAttributeCode . '_label'])) {
                $fileLabel = trim($importData[$mediaAttributeCode . '_label']);
                if (isset($importData[$mediaAttributeCode])) {
                    $keyInAddedFile = array_search($importData[$mediaAttributeCode],
                        $addedFilesCorrespondence['alreadyAddedFiles']);
                    if ($keyInAddedFile !== false) {
                        $addedFile = $addedFilesCorrespondence['alreadyAddedFilesNames'][$keyInAddedFile];
                    }
                }

                if (!$addedFile) {
                    $addedFile = $product->getData($mediaAttributeCode);
                }
                if ($fileLabel && $addedFile) {
                    $mediaGalleryBackendModel->updateImage($product, $addedFile, array('label' => $fileLabel));
                }
            }
        }
		// end external image url add  by shailesh patel 
		
		$imageData = array();
        foreach ( $this -> _imageFields as $field ) {
            if ( !empty( $importData[$field] ) && $importData[$field] != 'no_selection' ) {
                if ( !isset( $imageData[$importData[$field]] ) ) {
                    $imageData[$importData[$field]] = array();
                    } 
                $imageData[$importData[$field]][] = $field;
                } 
            } 
        
        foreach ( $imageData as $file => $fields ) {
			 if(file_get_contents(trim(Mage::getBaseDir('media') . DS . 'import/'.$file))){
			 
                $product -> addImageToMediaGallery( Mage :: getBaseDir( 'media' ) . DS . 'import/' . $file, $fields, false );
			 }
		} 
        
        
        if ( !empty( $importData['gallery'] ) ) {
            $galleryData = explode( ',', $importData["gallery"] );
            foreach( $galleryData as $gallery_img ) {
		    	if(file_get_contents(trim(Mage::getBaseDir('media') . DS . 'import'.$gallery_img))){
                    $product -> addImageToMediaGallery( Mage :: getBaseDir( 'media' ) . DS . 'import' . $gallery_img, null, false, false );
                 }
			} 
		} 
        $product -> setIsMassupdate( true );
        $product -> setExcludeUrlRewrite( true );
       // echo '<pre>';print_r($this -> _imageFields);exit;
        $product -> save();
		foreach($deletefiles as $path)
		{
				rename(trim(Mage::getBaseDir('media') . DS . 'import'.$path), trim(Mage::getBaseDir('media') . DS . 'import' . DS .'archive' .$path));
			
		}
		//$product -> save();
		
		if ($importData['Simple_Skus']!="" &&  $importData['options']!="" ) {	
		$config_attr = explode(',',$importData["configurable_attributes"]);
			foreach($config_attr as $code)
			{
				$importData["config_attribute"]=$code;
				$this->saveConfPrice($importData);
			}
		}
		
        return true;
		
         
	} 
	
  	public function saveConfPrice( array $importData )
    {
		$isConfigurable = false;
        $product = $this -> getProductModel()
		->reset();
        $product -> setData( array() );
        
        if ( $stockItem = $product -> getStockItem() ) {
            $stockItem -> setData( array() );
            } 
        
        if ( empty( $importData['store'] ) ) {
            if ( !is_null( $this -> getBatchParams( 'store' ) ) ) {
                $store = $this -> getStoreById( $this -> getBatchParams( 'store' ) );
                } else {
                $message = Mage :: helper( 'catalog' ) -> __( 'Skip import row, required field "%s" not defined', 'store' );
                Mage :: throwException( $message );
                } 
            } else {
            $store = $this -> getStoreByCode( $importData['store'] );
            } 
        
        if ( $store === false ) {
            $message = Mage :: helper( 'catalog' ) -> __( 'Skip import row, store "%s" field not exists', $importData['store'] );
            Mage :: throwException( $message );
            } 
        
        
        $product -> setStoreId( $store -> getId() );
        $productId = $product -> getIdBySku( $importData['sku'] );//get the sku of the configurable
        $new = true; // fix for duplicating attributes error
        if ( $productId ) {
            $product -> load( $productId );
            $new = false; // fix for duplicating attributes error
            } 
        $productTypes = $this -> getProductTypes();
        $productAttributeSets = $this -> getProductAttributeSets();
        	
		$configurablePrice = $product->getPrice(); //get configurable price
		$associatedProducts=$product->getTypeInstance()->getUsedProducts();
				
		$stack = array();
				
		for($j=0; $j< sizeof($associatedProducts) ; $j++)
		{
						
			$config_attribute_code = $importData["config_attribute"];
			
				array_push($stack,  Array($config_attribute_code =>$associatedProducts[$j]["'".$config_attribute_code."'"], "price"=>$associatedProducts[$j]['price']));
			
		}	
			
		if ($data = $product->getTypeInstance()->getConfigurableAttributesAsArray(($product))) 
		{
				foreach ($data as $attributeData) 
				{
					if($attributeData['attribute_code']==$config_attribute_code)
					{
						if ( !empty( $importData['options'] ) && !empty($importData['option_extra_prices']) ) //if there are options and prices
						{
								$optionsData = explode( ',', $importData['options'] );
								$pricesData = explode( ',', $importData['option_extra_prices'] );
								
								$id = isset($attributeData['id']) ? $attributeData['id'] : null;			
								$size = sizeof($attributeData['values']);
								
								$j=0;
								foreach($attributeData['values'] as $optionsAttr)//traverse each attribute value
								{
									$label = strval($optionsAttr['default_label']);
									$optionIndex = array_search($label,$optionsData);
									if($optionIndex!==false)
									{
										$attributeData['values'][$j]['pricing_value'] = $pricesData[$optionIndex];
									}
									$j++;
								}		
						}
						$attribute = Mage::getModel('catalog/product_type_configurable_attribute')
						->setData($attributeData)
						->setId($id)
						->setStoreId($product->getStoreId())
						->setProductId($productId)
						->save();
					}
				}
		}
        return true;
		
    }  
    protected function userCSVDataAsArray( $data )
    
    {
        return explode( ',', str_replace( " ", "", $data ) );
        } 
    
    protected function skusToIds( $userData, $product )
    
    {
        $productIds = array();
        foreach ( $this -> userCSVDataAsArray( $userData ) as $oneSku ) {
            if ( ( $a_sku = ( int )$product -> getIdBySku( $oneSku ) ) > 0 ) {
                parse_str( "position=", $productIds[$a_sku] );
                } 
            } 
        return $productIds;
        } 
    
    protected $_categoryCache = array();
    
    protected function _addCategories( $categories, $store )
    
    {
        // $rootId = $store->getRootCategoryId();
        // $rootId = Mage::app()->getStore()->getRootCategoryId();
        $rootId = 51; // our store's root category id
        if ( !$rootId ) {
            return array();
            } 
        $rootPath = '1/' . $rootId;
        if ( empty( $this -> _categoryCache[$store -> getId()] ) ) {
            $collection = Mage :: getModel( 'catalog/category' ) -> getCollection()
             -> setStore( $store )
             -> addAttributeToSelect( 'name' );
            $collection -> getSelect() -> where( "path like '" . $rootPath . "/%'" );
            
            foreach ( $collection as $cat ) {
                try {
                    $pathArr = explode( '/', $cat -> getPath() );
                    $namePath = '';
                    for ( $i = 2, $l = sizeof( $pathArr ); $i < $l; $i++ ) {
                        $name = $collection -> getItemById( $pathArr[$i] ) -> getName();
                        $namePath .= ( empty( $namePath ) ? '' : '/' ) . trim( $name );
                        } 
                    $cat -> setNamePath( $namePath );
                    } 
                catch ( Exception $e ) {
                    echo "ERROR: Cat - ";
                    print_r( $cat );
                    continue;
                    } 
                } 
            
            $cache = array();
            foreach ( $collection as $cat ) {
                $cache[strtolower( $cat -> getNamePath() )] = $cat;
                $cat -> unsNamePath();
                } 
            $this -> _categoryCache[$store -> getId()] = $cache;
            } 
        $cache = &$this -> _categoryCache[$store -> getId()];
        
        $catIds = array();
        foreach ( explode( ',', $categories ) as $categoryPathStr ) {
            $categoryPathStr = preg_replace( '#s*/s*#', '/', trim( $categoryPathStr ) );
            if ( !empty( $cache[$categoryPathStr] ) ) {
                $catIds[] = $cache[$categoryPathStr] -> getId();
                continue;
                } 
            $path = $rootPath;
            $namePath = '';
            foreach ( explode( '/', $categoryPathStr ) as $catName ) {
                $namePath .= ( empty( $namePath ) ? '' : '/' ) . strtolower( $catName );
                if ( empty( $cache[$namePath] ) ) {
                    $cat = Mage :: getModel( 'catalog/category' )
                     -> setStoreId( $store -> getId() )
                     -> setPath( $path )
                     -> setName( $catName )
                     -> setIsActive( 1 )
                     -> save();
                    $cache[$namePath] = $cat;
                    } 
                $catId = $cache[$namePath] -> getId();
                $path .= '/' . $catId;
                } 
            if ( $catId ) {
                $catIds[] = $catId;
                } 
            } 
        return join( ',', $catIds );
        } 
    
    protected function _removeFile( $file )
    
    {
        if ( file_exists( $file ) ) {
            if ( unlink( $file ) ) {
                return true;
                } 
            } 
        return false;
        } 
		
		
		
		
		
		
		
		
		
		
		
		
    }
    
