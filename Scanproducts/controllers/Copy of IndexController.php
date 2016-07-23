<?php 
class Plumtree_Scanproducts_IndexController extends Mage_Core_Controller_Front_Action
{  
	private $_customerId = 0;  
	 public function preDispatch() 
    {
        parent::preDispatch();
        $action = $this->getRequest()->getActionName();
        $loginUrl = Mage::helper('customer')->getLoginUrl();

        if (!Mage::getSingleton('customer/session')->authenticate($this, $loginUrl)) {
            $this->setFlag('', self::FLAG_NO_DISPATCH, true);
        }
    }
    public function indexAction()
    {
		$this->loadLayout()  ;
		$this->renderLayout();
    }
	 public function uploadAction()
    {	
		
		$filescollection=Mage::getModel('scanproducts/scanproducts')->getCollection()->addFieldToFilter('scanproducts_id',$this->getRequest()->getParam('scanproductid'))->addFieldToFilter('customer_id',Mage::getSingleton('customer/session')->getCustomer()->getId());
		
		
		if($filescollection->getSize())
		{
		$this->loadLayout();     
		$this->renderLayout();
		}
		else
		{
		 $this->_redirect('scanproducts/index/index/');
		}
	}
	public function newAction()
	{ 
		 	$not_exist_counter=0; 
			$list_counter=0; 
			
			$data = file_get_contents($_FILES["scannerdatafile"]["tmp_name"], true);
			$filename = pathinfo($_FILES['scannerdatafile']['name']);
			$ext = $filename['extension'];
			if($ext !== 'txt'){
				Mage::getSingleton('core/session')->addError("Selected file is not supported"); 
				$this->_redirect('*/*/');
				return;
			}
			
			if($data):
			$fp = fopen(Mage::getBaseDir()."/scanner/upc_not_exist.txt","a"); 
			$convert = explode("\n", $data); 
			for ($i=0;$i<count($convert);$i++)   
			{  
				$parts=explode(',',$convert[$i]);      
				if(!empty($parts[count($parts)-1]))
				$list_counter++;
				$skulist[]=$parts[count($parts)-1]; 
				$_product=Mage::getModel('catalog/product')->loadByAttribute('upc',trim($parts[count($parts)-1]));
				
						if(!$_product && !empty($convert[$i])):
							//fwrite($fp,str_pad( $convert[$i], 40, ' ', STR_PAD_RIGHT));
							fwrite($fp,trim($convert[$i]).'            '.'CustomreId: ');
							fwrite($fp, Mage::getSingleton('customer/session')->getCustomer()->getId());
							fwrite($fp, "\r\n");
							$not_exist_counter++;
						endif;
				
			}
			fclose($fp);
			$available_prodcuts=$list_counter-$not_exist_counter;
				if($available_prodcuts>0):
						$skulist=array_map('trim', $skulist);
						$skus=array_unique(array_filter($skulist)); 
						$skudata=implode(',',$skus); 
						$scanproductmodel = Mage::getModel('scanproducts/scanproducts');
						$scanproductmodel->setContent($skudata);
						$scanproductmodel->setFilename($_FILES["scannerdatafile"]['name']);
						$scanproductmodel->setCustomerId(Mage::getSingleton('customer/session')->getCustomer()->getId());
						$scanproductmodel->setCreatedTime(now());
						$scanproductmodel->setUpdateTime(now());
						$scanproductmodel->save();
						 Mage::getSingleton('core/session')->addSuccess($this->__('File successfully uploaded.'));
					else:
						Mage::getSingleton('core/session')->addNotice("You scanned ".$list_counter." Bar codes but no items were available, the Store@Home customer service staff will review the items not found and get back to you.");
					endif;
					if($not_exist_counter>0 && $available_prodcuts>0):
						Mage::getSingleton('core/session')->addNotice("You scanned ".$list_counter." Bar codes but only ".$available_prodcuts." items were available, the Store@Home customer service staff will review the items not found and get back to you.");
					endif;
			endif;		 
				$this->_redirect('*/*/');

	}
	
 	protected function _addToCart($params){
	//	if(!Mage::helper('quickorder')->checkLicense($this)){return;}
 		$cart = $this->_getCart();
		 try {
		 
            if (isset($params['qty'])) {
                $filter = new Zend_Filter_LocalizedToNormalized(
                    array('locale' => Mage::app()->getLocale()->getLocaleCode())
                );
                $params['qty'] = $filter->filter($params['qty']);
            }
            $product = $this->_initProduct($params['product']);
            if (!$product) {
                return;
            }
 			
			if($product->isSalable())
			$cart->addProduct($product, $params);
		
  		  }catch (Mage_Core_Exception $e) {
		  	return false;
		  } catch (Exception $e){
		  	return false;
		  }
 	}
 
 	protected function _initProduct($productId)
    {
	//	if(!Mage::helper('quickorder')->checkLicense($this)){return;}
        if ($productId) {
            $product = Mage::getModel('catalog/product')
                ->setStoreId(Mage::app()->getStore()->getId())
                ->load($productId);
            if ($product->getId()) {
                return $product;
            }
        }
        return false;
    }
 	protected function _getSession()
    {
	//	if(!Mage::helper('quickorder')->checkLicense($this)){return;}
        return Mage::getSingleton('checkout/session');
    }
  	protected function _getCart()
    {
	//	if(!Mage::helper('quickorder')->checkLicense($this)){return;}
        return Mage::getSingleton('checkout/cart');
    }
	 
    function addtocartAction()
    { 
 		$post = $this->getRequest()->getPost();
		$cart = $this->_getCart();	
		$products = $post['product'];
		if(empty($products)){  return; }
		try{
		if($post['selected_productid'])
			{
				foreach($products as $_product){
					if($post['selected_productid']==$_product[product])
					$this->_addToCart($_product);
					else
					continue;
				}
					 Mage::getSingleton('core/session')->addSuccess($this->__('Item(s) added to cart successfully.'));
					$data['redirect_url'] = $this->_getRefererUrl();
			}
			else
			{
				$prodid=$this->getRequest()->getParam('productslist');
				foreach($products as $_product){
				if(in_array($_product[product],$prodid))
				$this->_addToCart($_product);	
				}  
				$data['redirect_url'] = Mage::helper('checkout/cart')->getCartUrl();
			}
			$cart->save();
			$this->_getSession()->setCartWasUpdated(true);
			$this->_getSession()->addSuccess('Item(s) added to cart.');
			$data['success'] = '1';
			$data['message'] = 'Item(s) added to cart successfully.';
			
		}catch(Exception $e){
			$data['success'] = '0';
			$data['message'] = 'Item add to cart failed.';			
		}
		$this->getResponse()->setRedirect($data['redirect_url']);
		 $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($data));
 }
 
 	function addtofavoritesAction()
	{
		 $session = Mage::getSingleton('customer/session');
		 $this->_customerId = $session->getCustomer()->getId();
		 $list      = Mage::getModel('amlist/list');
	   	 $listId    = $this->getRequest()->getParam('list');
		 if (!$listId)
		 { //get default - last
	       $listId = Mage::getModel('amlist/list')->getLastListId($this->_customerId);
	   	 }
		 $post = $this->getRequest()->getPost();
		 $products = $post['product'];
	  	 if (!$listId) 
		 { 
		 	$product_ids=$this->getRequest()->getParam('productslist');
			
			Mage::getSingleton('amlist/session')->setAddProductId($product_ids);
			Mage::getSingleton('amlist/session')->setAddAllProductId($products);
	        
			$this->_redirect('amlist/list/edit/');
	        return;
	     }
		 
		 
		 $prodid=$this->getRequest()->getParam('productslist');
		 $favcollection=Mage::getModel('amlist/item')->getCollection()->addFieldToFilter('list_id',$listId);
		 $favcollection->addFieldToSelect('product_id');
		 $fav_products=$favcollection->getData();
		 foreach( $favcollection as $prod):
			 	$favproducts[]=$prod->getProductId();
		 endforeach;
			 foreach($products as $_product){
			
				 if(in_array($_product[product],$prodid) && !in_array($_product[product],$favproducts))
				 {
				   $item = Mage::getModel('amlist/item')
					->setProductId($_product[product])
					->setListId($listId) 
					->setQty($_product[qty])
					->setBuyRequest(serialize($_product));
					
					$item->save();
				}
			}
		
			   $message = $this->__('Product has been successfully added to the folder. Click <a href="%s">here</a> to continue shopping', $this->_getRefererUrl());
               
	          // $session->setRedirectUrl($product->getProductUrl());
               $session->addSuccess($message); 
			 $this->_redirect('amlist/list/edit', array('id'=>$listId));
	}
 	//add to single configurable product into the favorites list
 	function addconfig_to_favoritesAction()
	{
		$session = Mage::getSingleton('customer/session');
		 $this->_customerId = $session->getCustomer()->getId();
		 $list      = Mage::getModel('amlist/list');
	   	 $listId    = $this->getRequest()->getParam('list');
		 $post = $this->getRequest()->getPost();
		 $products = $post['product'];
		 if (!$listId){ //get default - last
	       $listId = Mage::getModel('amlist/list')->getLastListId($this->_customerId);
	   	 }
	  	 if (!$listId) 
		 { 
		   //create new
		   //print_r($post['selected_productid']);
		   //exit;
	       Mage::getSingleton('amlist/session')->setAddProductId($post['selected_productid']);
		   Mage::getSingleton('amlist/session')->setAddAllProductId($products);
	       $this->_redirect('amlist/list/edit/');
	       return;
	    }
			if($post['selected_productid'])
			{
				
				foreach($products as $_product)
				{
					if($post['selected_productid']==$_product[product]):
						$item = Mage::getModel('amlist/item')
						->setProductId($_product[product])
						->setListId($listId) 
						->setQty($_product[qty])
						->setBuyRequest(serialize($_product));
						$item->save();
					
					endif;
				}
				 Mage::getSingleton('core/session')->addSuccess($this->__('Product has been successfully added to the folder.'));
				  $this->_redirectReferer();
			}
	}

	function deleteAction()
	{
			Mage::getModel('scanproducts/scanproducts')->load($this->getRequest()->getParam('scanproductid'))->delete();
			$this->_redirect('*/*/');
	}

	function deletefilesAction()
	{
			$filesid=$this->getRequest()->getParam('files');
			foreach($filesid as $id)
			Mage::getModel('scanproducts/scanproducts')->load($id)->delete();
			$this->_redirect('*/*/');
			
	}
	
	
}