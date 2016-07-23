<?php
class Plumtree_Zipcodes_IndexController extends Mage_Core_Controller_Front_Action
{
    public function indexAction()
    {
		$this->loadLayout();     
		$this->renderLayout();
    }
	public function checkzipcodeAction()
	{
		$zipcodes = Mage::getStoreConfig('zipcode_list/general/zipcodes');
		$zipcodelist=explode(',',$zipcodes);
		if(in_array(Mage::app()->getRequest()->getParam('zipcode'),$zipcodelist)):
			 echo 'true';
			 $cookie = Mage::getSingleton('core/cookie');
			 $cookie->set('checkzipcode', Mage::app()->getRequest()->getParam('zipcode') ,time()+86400,'/');
			 
		else:
			 echo 'false';
		endif;
	}
	
	public function subscribeAction()
	{
		try
		{
		$zipcodesmodel = Mage::getModel('zipcodes/zipcodes');
		$zipcodesmodel->setEmail(Mage::app()->getRequest()->getParam('email'));
		$zipcodesmodel->setPostcode(Mage::app()->getRequest()->getParam('zipcode-subscribe'));
		$zipcodesmodel->setCreatedTime(now());
		$zipcodesmodel->setUpdateTime(now());
		$zipcodesmodel->setStatus(1);
		$zipcodesmodel->save();
		Mage::getSingleton('core/session')->addSuccess('Your inquiry was submitted.');
		
		//$this->_redirectReferer();
		 $this->_redirect('*/*/');
		return;
		}
		catch(Exception $e){
				Mage::getSingleton('customer/session')->addSuccess(Mage::helper('zipcodes')->__('Your inquiry was submitted.	'));
				
				$this->_redirectReferer();
				return;
		}
		
	}
	
	
}
