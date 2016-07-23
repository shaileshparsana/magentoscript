<?php
class Plumtree_Picklist_IndexController extends Mage_Core_Controller_Front_Action{
    public function IndexAction() {
      
	  $this->loadLayout();   
	  $this->getLayout()->getBlock("head")->setTitle($this->__("picklist"));
	        $breadcrumbs = $this->getLayout()->getBlock("breadcrumbs");
      $breadcrumbs->addCrumb("home", array(
                "label" => $this->__("Home Page"),
                "title" => $this->__("Home Page"),
                "link"  => Mage::getBaseUrl()
		   ));

      $breadcrumbs->addCrumb("picklist", array(
                "label" => $this->__("picklist"),
                "title" => $this->__("picklist")
		   ));

      $this->renderLayout(); 
	  
    }
	
	   public function PrintAction() 
	   {
		   	$orders = Mage::getModel('sales/order')->getCollection() ->addAttributeToFilter('created_at', array('from'=>Mage::app()->getRequest()->getParam('fromdate'), 'to'=>Mage::app()->getRequest()->getParam('todate')));
	 Mage::register('picklistData', $orders);
		 	$this->loadLayout('print');
        	$this->renderLayout();
			
			
    }
}