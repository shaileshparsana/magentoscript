<?php
class Plumtree_Picklist_Adminhtml_PicklistbackendController extends Mage_Adminhtml_Controller_Action 
{   
	public function indexAction() 
    {   
       $this->loadLayout();
	   $this->_title($this->__("Picklist"));
	   $this->renderLayout();
    }
	 public function printAction() 
	   {
		   	if(Mage::app()->getRequest()->getParam('fromdate'))
			$fromDate = date('Y-m-d', strtotime(Mage::app()->getRequest()->getParam('fromdate')));
			if(Mage::app()->getRequest()->getParam('todate'))
			$toDate = date('Y-m-d', strtotime(Mage::app()->getRequest()->getParam('todate')));	   
			
			$ddate = Mage::getModel('ddate/ddate')->getCollection() ->addFieldToFilter('ddate', array('from'=>$fromDate, 'to'=>$toDate));
			//	$ddate->getSelect()->joinRight(Mage::getSingleton('core/resource')->getTableName('ddate/ddate_store'), Mage::getSingleton('core/resource')->getTableName('ddate/ddate_store') . '.ddate_id = main_table.ddate_id');
			$ddate->getSelect()->reset(Zend_Db_Select::COLUMNS)->columns(array('ddate'=>'ddate','dtimetext'=>'dtimetext'))->joinRight(Mage::getSingleton('core/resource')->getTableName('ddate/ddate_store'),Mage::getSingleton('core/resource')->getTableName('ddate/ddate_store') . '.ddate_id = main_table.ddate_id',array('increment_id'=>'increment_id','is_gogreen'=>'is_gogreen'));
			
			
			if(Mage::app()->getRequest()->getParam('sort_attribute'))			
			{
					$ddate->getSelect()->order(Mage::app()->getRequest()->getParam('sort_attribute').' '.Mage::app()->getRequest()->getParam('sort_order'));
		
			}
		//	echo Mage::app()->getRequest()->getParam('ddate');
		//	exit;
			
		
///		print_r($ddate->getSelect()->__toString());
//		exit;
			
			if($fromDate)
			{
			Mage::getSingleton('core/session')->setFromDate( date('m/d/y', strtotime($fromDate)));
			Mage::getSingleton('core/session')->setToDate( date('m/d/y', strtotime($toDate)));
	 		Mage::register('adminpicklistData', $ddate);
			}
		 	$this->loadLayout();
        	$this->renderLayout();
			
			
    }
}