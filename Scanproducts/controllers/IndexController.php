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
			$exist_counter=0;
			$list_counter=0; 
			$admin_data=array();
			$data = file_get_contents($_FILES["scannerdatafile"]["tmp_name"], true);
			$filename = pathinfo($_FILES['scannerdatafile']['name']);
			$ext = $filename['extension'];
			if($ext !== 'txt' && $ext !== 'TXT'){
				Mage::getSingleton('core/session')->addError("Selected file is not supported"); 
				$this->_redirect('*/*/');
				return;
			}
			
			if($data):
			$fp = fopen(Mage::getBaseDir()."/scanner/upc_not_exist.txt","a"); 
			$convert = explode("\n", $data); 
			$upclist=array();
			$all_upc=array();
			
		
			for ($i=0;$i<count($convert);$i++)   
			{     
			if(strlen(trim($convert[$i])) > 0){
			$list_counter++;  
			$parts=explode(',',$convert[$i]);       
			$all_upc[]=$parts[count($parts)-1]; 
		//	if(!in_array($parts[count($parts)-1],$upclist))
		//	{
			$finalupc=$parts[count($parts)-1]; 
		
					$length = strlen((string) trim($parts[count($parts)-1]));
					if($length==8)
					{
						$finalupc=$this->convert_upce_to_upca($parts[count($parts)-1]);
					}
					$_product=Mage::getModel('catalog/product')->loadByAttribute('upc',$finalupc);
						if(!$_product):
					
							//fwrite($fp,str_pad( $convert[$i], 40, ' ', STR_PAD_RIGHT));
							fwrite($fp,trim($convert[$i]).'            '.'CustomreId: ');
							fwrite($fp, Mage::getSingleton('customer/session')->getCustomer()->getId());
							fwrite($fp, "\r\n");
							$not_exist_counter++;
							$admin_data[]=trim($convert[$i]).' This one was not found';
						else:
							$admin_data[]=trim($convert[$i]).' Found';
							array_push($upclist,$finalupc);
							$exist_counter++;
						endif;
		//	}
			}
			}	
			$admin_detail=implode(PHP_EOL, $admin_data);
			fclose($fp);
			
			$available=$list_counter-$not_exist_counter;

			
			if(!empty($all_upc))
			{
				$unique_upc=array_unique($upclist);
				$upclist_contant=implode(',',$unique_upc); 
				$scanproductmodel = Mage::getModel('scanproducts/scanproducts');
				$scanproductmodel->setContent($upclist_contant);
				$scanproductmodel->setFilename($_FILES["scannerdatafile"]['name']);
				$scanproductmodel->setCustomerId(Mage::getSingleton('customer/session')->getCustomer()->getId());
				$scanproductmodel->setCreatedTime(now());
				$scanproductmodel->setUpdateTime(now());
				$scanproductmodel->setFileData($admin_detail);
				$scanproductmodel->save();
				Mage::getSingleton('core/session')->addSuccess($this->__('File successfully uploaded.'));
				
			}
			if($exist_counter>0):
					if($exist_counter==$list_counter):
					Mage::getSingleton('core/session')->addNotice("You scanned ".$list_counter ." Bar Codes ".$exist_counter. " items were available <br /> (Any duplicates will appear once, Please change qty if more is needed)");
					else:
					Mage::getSingleton('core/session')->addNotice("You scanned ".$list_counter." Bar codes but only ".$exist_counter." items were available, the Store@Home customer service staff will review the items not found and get back to you. (Any duplicates will appear once, Please change qty if more is needed)");
					endif;
			else:
			Mage::getSingleton('core/session')->addNotice("You scanned ".$list_counter." Bar codes but no items were available, the Store@Home customer service staff will review the items not found and get back to you. (Any duplicates will appear once, Please change qty if more is needed)");
			$this->_redirect('*/*/');
			return;  
			endif;

			$this->_redirect('*/*/');
			endif;

	
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
	

		public function convert_upce_to_upca($upc) {
		
			if(!isset($upc)) { return false; }
			if(!preg_match("/^0/", $upc)) { return false; }
			if(preg_match("/0(\d{5})([0-3])(\d{1})/", $upc, $upc_matches)) {
			$upce_test = preg_match("/0(\d{1})(\d{1})(\d{1})(\d{1})(\d{1})(\d{1})(\d{1})/", $upc, $upc_matches);
			if($upce_test==false) { return false; }
			if($upc_matches[6]==0) {
			$upce = "0".$upc_matches[1].$upc_matches[2].$upc_matches[6]."0000".$upc_matches[3].$upc_matches[4].$upc_matches[5].$upc_matches[7]; }
			if($upc_matches[6]==1) {
			$upce = "0".$upc_matches[1].$upc_matches[2].$upc_matches[6]."0000".$upc_matches[3].$upc_matches[4].$upc_matches[5].$upc_matches[7]; }
			if($upc_matches[6]==2) {
			$upce = "0".$upc_matches[1].$upc_matches[2].$upc_matches[6]."0000".$upc_matches[3].$upc_matches[4].$upc_matches[5].$upc_matches[7]; }
			if($upc_matches[6]==3) {
			$upce = "0".$upc_matches[1].$upc_matches[2].$upc_matches[3]."00000".$upc_matches[4].$upc_matches[5].$upc_matches[7]; } }
			if(preg_match("/0(\d{5})([4-9])(\d{1})/", $upc, $upc_matches)) {
			preg_match("/0(\d{1})(\d{1})(\d{1})(\d{1})(\d{1})(\d{1})(\d{1})/", $upc, $upc_matches);
			if($upc_matches[6]==4) {
			$upce = "0".$upc_matches[1].$upc_matches[2].$upc_matches[3].$upc_matches[4]."00000".$upc_matches[5].$upc_matches[7]; }
			if($upc_matches[6]==5) {
			$upce = "0".$upc_matches[1].$upc_matches[2].$upc_matches[3].$upc_matches[4].$upc_matches[5]."0000".$upc_matches[6].$upc_matches[7]; }
			if($upc_matches[6]==6) {
			$upce = "0".$upc_matches[1].$upc_matches[2].$upc_matches[3].$upc_matches[4].$upc_matches[5]."0000".$upc_matches[6].$upc_matches[7]; }
			if($upc_matches[6]==7) {
			$upce = "0".$upc_matches[1].$upc_matches[2].$upc_matches[3].$upc_matches[4].$upc_matches[5]."0000".$upc_matches[6].$upc_matches[7]; }
			if($upc_matches[6]==8) {
			$upce = "0".$upc_matches[1].$upc_matches[2].$upc_matches[3].$upc_matches[4].$upc_matches[5]."0000".$upc_matches[6].$upc_matches[7]; }
			if($upc_matches[6]==9) {
			$upce = "0".$upc_matches[1].$upc_matches[2].$upc_matches[3].$upc_matches[4].$upc_matches[5]."0000".$upc_matches[6].$upc_matches[7]; } }
			return $upce;
		}
		
}

