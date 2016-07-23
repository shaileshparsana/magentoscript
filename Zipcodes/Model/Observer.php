<?php

class Plumtree_Zipcodes_Model_Observer extends Mage_Core_Model_Abstract
{
    public function allowzipcodes($observer = null)
    { 
        
     $event = $observer->getEvent();
 
        $controllerAction = $event->getControllerAction();
  
        $controllerAction->getRequest()->getParams();
	//	print_r(Mage::app()->getRequest()->getParams());
	
		if(Mage::app()->getRequest()->getParam('postcode')=='AD500')
		{
		//Mage::app()->_redirectError(Mage::getUrl('*/*/edit', array('id' => Mage::app()->getRequest()->getParam('id'))));
//		 return $this->_redirectError(Mage::getUrl('*/*/edit', array('id' => Mage::app()->getRequest()->getParam('id'))));
//	$this->_redirectUrl(Mage::getUrl('*/*/edit', array('id' => Mage::app()->getRequest()->getParam('id'))));

//$controllerAction = $observer->getEvent()->getControllerAction();
$result = array();
$result['error'] = '-1';
$result['message'] = 'YOUR MESSAGE HERE';
$controllerAction->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
$response = Mage::app()->getFrontController()->getResponse();
$response->setRedirect(Mage::getUrl('*/*/edit', array('id' => Mage::app()->getRequest()->getParam('id'))));

}
		

            
    
    }
}