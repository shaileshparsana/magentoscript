<?php
class plumtree_wishlistproducts_model_observer
{
	 public function addwishlist($observer)
	 {
	 	  /*$customer = Mage::getModel('customer/customer');
		  $wishlist = Mage::getModel('wishlist/wishlist');
		  $product = Mage::getModel('catalog/product');
		  //$product = Mage::getResourceModel('catalog/product_collection');
		  //$product->getSelect()->order('rand()');
		  //$product->getSelect()->limit(4);
		  Mage::register('wishlist', $wishlist);
		  //$wishlist->_init('wishlist/wishlist');
		  $customer_id = $observer->getCustomer()->getId();
		  $product_id = 23;
		  $customer->load($customer_id);
		  $wishlist->loadByCustomer($customer_id);
		  
		  //$buyRequest = $item->getBuyRequest();
		  $wishlist->addNewItem($product->load($product_id));
		  Mage::dispatchEvent('wishlist_add_product', array('wishlist'=>$wishlist, 'product'=>$product->load($product_id)));
		  print_r($wishlist->getData());//exit;	*/
  	 } 
	
}
?>