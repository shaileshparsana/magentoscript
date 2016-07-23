<?php
class MageCoders_QuickOrder_IndexController extends Mage_Core_Controller_Front_Action{
 
	public function suggestAction(){
	  
		$params = $this->getRequest()->getParams();
		$str = strip_tags(trim($params['q']));
		$queryStr = mysql_escape_string($str);
		
		$query = '%'.$queryStr.'%';
		
		if($query==''){ return; }
		
		$data = array();
		$isCollection = false;
		$isSku = false;					

		$visibility = $this->getConfig('visibility_filter');
		$sort_column = $this->getConfig('sort_column');
		$limit =  $this->getConfig('number_result');
		
		$product = Mage::getModel('catalog/product')->loadByAttribute('sku',$str);
		if(!$product){
			$product = Mage::getModel('catalog/product')->loadByAttribute('upc',$str);
		}
		
		
		if($product){
					$isSku = true;
					$json = array();
					
					if(!$this->isProductAllowed($product)){ return; }
					
					if($product->getStatus()==1 && $product->getVisibility()==$visibility){
						
						$imageUrl = $this->getImageUrl($product);
						$json['value'] = $product->getSku();
						$json['name'] = $product->getName();
						$json['upc'] = $product->getUpc();
						$json['image'] = $imageUrl;
						$json['is_sku'] = $isSku;
						$data[] =  $json;
						$this->getResponse()->setBody(Mage::helper('core')->jsonEncode($data));
						return;
					}
					else
					{
					return;
					}
					
		}
		 
		 
		$types = Mage::helper('quickorder/protected')->getProductTypes();

		$collection = Mage::getModel('catalog/product')->getCollection()
								//->addFieldToFilter('name',array('like'=>$query))
								->addFieldToFilter(array(
                              array('attribute' => 'name', 'like' => $query),
                              array('attribute' => 'brand_name', 'like' => $query)
                              ))
								//->addFieldToFilter('brand_name',array('like'=>$query))
								->addAttributeToSelect(array('sku','name','small_image','image','thumbnail','upc'))
								->addAttributeToFilter('visibility',$visibility)
								->addAttributeToFilter('type_id',array('in'=>$types))
								//->addAttributeToSort($sort_column,'DESC')
								->addAttributeToFilter('status',1)								
								->setPageSize($limit);
								$collection->getSelect()->Order("abs(popularity) desc");

		if($collection->count()>0){
			$isCollection = true;
		}else{
			$collection = Mage::getModel('catalog/product')->getCollection()
								  ->addAttributeToFilter(
									array(
										array('attribute'=>'sku','like'=>$query),
										array('attribute'=>'upc','like'=>$query),
									)
								  )
								->addAttributeToSelect(array('name','sku','small_image','image','thumbnail','upc'))
								->addAttributeToFilter('visibility',$visibility)
								->addAttributeToFilter('type_id',array('in'=>$types))
								->addAttributeToFilter('status',1)
							//	->addAttributeToSort($sort_column,'DESC')
								->setPageSize($limit);	
								$collection->getSelect()->Order("abs(popularity) desc");
			$isSku = true;					
			$isCollection = true;								
		}
		
		if(!$collection->count()){
			
			$collection = Mage::getModel('catalog/product')->getCollection();
			$options = $this->getDropdownOptions('brand_name');//get all options
				$optionId = false;

				foreach ($options as $option) {
					$pattern = '/^'.$queryStr.'/i';
					if(preg_match($pattern,$option['label'], $matches, PREG_OFFSET_CAPTURE)){
						$optionId = $option['value'];
						break;
					}
				}
				if ($optionId) { //if there is an id...
					$collection->addAttributeToFilter('brand_name', $optionId);
				}
				
				$collection->addAttributeToSelect(array('name','sku','upc','small_image','image','thumbnail'))
						->addAttributeToFilter('visibility',$visibility)
						->addAttributeToFilter('type_id',array('in'=>$types))
						->addAttributeToFilter('status',1)
						//->addAttributeToSort($sort_column,'DESC')
						->setPageSize($limit);		
						$collection->getSelect()->Order("abs(popularity) desc");

			$isSku = false;	
		}
		
		
		
		if($isCollection){ 
		
			foreach($collection as $_product){
					
					if(!$this->isProductAllowed($_product)){ continue; }
					
					$imageUrl = $this->getImageUrl($_product);
					$json = array();
					$json['value'] = $_product->getSku();
					$json['name'] = $_product->getName();
					$json['image'] = $imageUrl;
					$json['brand_name'] = $_product->getAttributeText('brand_name');
					$json['upc'] = $_product->getData('upc');
					$json['is_sku'] = $isSku;
					$data[] =  $json;
			}	
		}
	
		
		$this->getResponse()->setBody(Mage::helper('core')->jsonEncode($data));
		
	}
	
	public function getImageUrl($_product){
		$product = $_product->getData();
		if($product['thumbnail']!=''){
			$image = $product['thumbnail'];
			$attr = 'thumbnail';
		}elseif($product['small_image']!=''){
			$image = $product['small_image'];
			$attr = 'small_image';
		}else{
			$image = $product['image'];
			$attr = 'image';			
		}
		$url = (string)$_product->getMediaConfig()->getMediaUrl($image);
		if(file_exists($url)){
			$imageUrl =	(string)Mage::helper('catalog/image')->init($_product, $attr)->resize(85,95);
		}else{
			$imageUrl = (string)Mage::helper('catalog/image')->init($_product, $attr);
		}
		return $imageUrl;
		
	}
	
	
	public function loadproductAction(){
		$sku = $this->getRequest()->getParam('sku');
		
		$sku = mysql_escape_string(trim($sku));		
		if($sku==''){ return; }
		
		$data = array();
		$product = Mage::getModel('catalog/product')->loadByAttribute('sku',$sku);
		if($product){
			$data['product']['name'] = $product->getName();
			$data['success'] =  true;
		}
		$this->getResponse()->setBody(Mage::helper('core')->jsonEncode($data));
		
	}
	
	protected function _initProduct($sku){
	
        if ($sku) {
            $product = Mage::getModel('catalog/product')
                ->setStoreId(Mage::app()->getStore()->getId());
			$product->load($product->getIdBySku($sku));
            if ($product->getId()) {
                return $product;
            }
        }
        return false;
	}
	
	
	 /**
     * Add product to shopping cart action
     */
    public function addcartAction()
    {
        $params = $this->getRequest()->getParams();
		$products = $params['product'];

		
		$json = array();
		$c = 0;
		foreach($params as $k=>$v){
			if(strstr($k,'as_values_')){
				if(!empty($v)){ $c++;} 
			}
		}
		
		if($c==0){
			$json['success'] = false;
			$json['message'] = $this->__('There are no valid products to add in cart.');	
		}elseif(!empty($products)){
			$products = array_filter($products,array($this,'filterProducts'));
			
			if(count($products)>0){
				$f = $this->addProduct($products);
				if($f === true){
					$json['success'] = true;
					$json['message'] = $this->__('Items added to cart successfully.');
					$json['redirect_url'] = Mage::helper('checkout/cart')->getCartUrl();
				}else{
					$json['success'] = false;
					$json['message'] = $this->__('Please remove invalid product name.');
				}
			}else{
				$json['success'] = false;
				$json['message'] = $this->__('There are no products to add in cart.');	 
			}	
		}else{
		  $json['success'] = false;
		  $json['message'] = $this->__('There are no products to add in cart.');	 
		}

		$this->getResponse()->setBody(Mage::helper('core')->jsonEncode($json));
       
    }
	
	protected function addProduct($products){
		$cart = Mage::getSingleton('checkout/cart');
	 	try {
			
			foreach($products as $params){
				if (isset($params['qty'])) {
					$params['qty'] = (int)$params['qty'];
					$filter = new Zend_Filter_LocalizedToNormalized(
						array('locale' => Mage::app()->getLocale()->getLocaleCode())
					);
					$params['qty'] = $filter->filter($params['qty']);
				}
				$product = $this->_initProduct($params['sku']);
				if (!$product) {
					return;
				}
				$params['product'] = $product->getId();
				 $cart->addProduct($product, $params);
				unset($params['product']);
			}
			 $cart->save();
	         $this->_getSession()->setCartWasUpdated(true);

            if (!$this->_getSession()->getNoCartRedirect(true)) {
                if (!$cart->getQuote()->getHasError()){
                    $message = $this->__('%s was added to your shopping cart.', Mage::helper('core')->htmlEscape($product->getName()));
                    $this->_getSession()->addSuccess($message);
                }
				return true;
            }
        } catch (Mage_Core_Exception $e) {
			return $e->getMessage();
        } catch (Exception $e){
			return $e->getMessage();		
		}
	
	}
	
	
	protected function filterProducts($value){
		if($value['sku']!=''){
			return $value;
		}
	}

	protected function _getSession()
    {
        return Mage::getSingleton('checkout/session');
    }

	
	protected function getConfig($key){
		if($key!=''){
			return Mage::getStoreConfig('quickorder/settings/'.$key);
		}
	}
	
	protected function isProductAllowed($product){
		return Mage::helper('quickorder/protected')->isProductAllowed($product);
	}
	
	
	protected function getDropdownOptions($attrcode){
		
		$cacheId = 'quickorder_attr_'.$attrcode;
	
		if (false !== ($data = Mage::app()->getCache()->load($cacheId))) {
			$options = unserialize($data);
		} else {
			$options =	Mage::getModel('eav/config')->getAttribute('catalog_product',$attrcode)
									->getSource()->getAllOptions(); 
			Mage::app()->getCache()->save(serialize($options), $cacheId);
		}

		return $options;
	}
	
	
	public function mobilescancodesAction()
	{
			$tasktitle = $_POST['scancode'];
			setlocale(LC_TIME, "fi_FI"); 
			date_default_timezone_set('UTC');
			$date = strftime("%m/%d/%Y");
			$timesaved = strftime("%H:%M:%S");
			$cont = $date. "," .$timesaved .",". $tasktitle ; 
			
			$fp = fopen(Mage::getBaseDir()."/scanner/mobile_upc_not_exist.txt","a"); 
			fwrite($fp, $cont.'            '.'CustomreId: ');
			fwrite($fp, Mage::getSingleton('customer/session')->getCustomer()->getId());
			fwrite($fp, "\r\n");
			fclose($f);
	}
}