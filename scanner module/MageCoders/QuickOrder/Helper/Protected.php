<?php 
class MageCoders_QuickOrder_Helper_Protected extends Mage_Core_Helper_Abstract{
	
	const VERSION = 'community';
	
	public function getProductTypes(){ 
		if(self::VERSION=='community'){
			return array('simple','downloadable','virtual','grouped');
		}else{
			return array('simple','downloadable','virtual','configurable','grouped');
		}
	}
	
	
	public function isProductAllowed($product){
	
		$allowed = true;
		$product_types = $this->getProductTypes();
		if(!in_array($product->getTypeId(),$product_types)){
			$allowed = false;
		}elseif($product->getHasOptions() && $product->getTypeId()!='configurable'){
			$allowed = false;
		}
		return $allowed;
	}
	
	
	
}