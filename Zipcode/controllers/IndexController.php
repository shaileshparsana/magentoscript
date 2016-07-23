<?php
class Plumtree_Zipcode_IndexController extends Mage_Core_Controller_Front_Action
{   
	
    public function indexAction()
    { 
	
	$zipcodes = Mage::getStoreConfig('zipcode_list/general/zipcodes');
	$zipcodelist=explode(',',$zipcodes);
	if(in_array(Mage::app()->getRequest()->getParam('zipcode'),$zipcodelist)):
		 echo 'true';
		 $cookie = Mage::getSingleton('core/cookie');
		 $cookie->set('checkzipcode', 'yes' ,time()+86400,'/');
	else:
		 echo 'false';
	endif;
	
    }

}