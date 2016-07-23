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
		$this->loadLayout();     
		$this->renderLayout();
	}
	public function newAction()
	{
		 
			$data = file_get_contents($_FILES["scannerdatafile"]["tmp_name"], true);
			if($data):
			$fp = fopen(Mage::getBaseDir()."/scanner/upc_not_exist.txt","a"); 
			$convert = explode("\n", $data); 
			for ($i=0;$i<count($convert);$i++)  
			{
				$parts=explode(',',$convert[$i]);
				$skulist[]=$parts[count($parts)-1]; 
				$_product=Mage::getModel('catalog/product')->loadByAttribute('upc',trim($parts[count($parts)-1]));
						if(!$_product):
							fwrite($fp,str_pad( $convert[$i], 40, ' ', STR_PAD_RIGHT));
							fwrite($fp, 'CustomreId: ');
							fwrite($fp, Mage::getSingleton('customer/session')->getCustomer()->getId());
							fwrite($fp, "\r\n");
						endif;
				
			}
			fclose($fp);
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
			endif;	
				 Mage::getSingleton('core/session')->addSuccess($this->__('File successfully uploaded.'));
				
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
		 if (!$listId){ //get default - last
	       $listId = Mage::getModel('amlist/list')->getLastListId($this->_customerId);
	   	 }
	  	if (!$listId) { //create new
	       Mage::getSingleton('amlist/session')->setAddProductId($post['selected_productid']);
	       $this->_redirect('amlist/list/edit/');
	       return;
	    }
		 $post = $this->getRequest()->getPost();
		 $products = $post['product'];
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
	  	 if (!$listId) { //create new
	       Mage::getSingleton('amlist/session')->setAddProductId($post['selected_productid']);
	       $this->_redirect('amlist/list/edit/');
	       return;
	    }
	
		
		
			if($post['selected_productid'])
			{
				foreach($products as $_product){
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