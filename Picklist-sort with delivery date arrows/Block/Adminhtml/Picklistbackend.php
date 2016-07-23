<?php  

class Plumtree_Picklist_Block_Adminhtml_Picklistbackend extends Mage_Adminhtml_Block_Template {
	public function __construct()
  	{
	
  		parent::__construct();
    	$this->setTemplate('picklist/picklistorders.phtml'); 
		$this->setFormAction(Mage::getUrl('picklist/adminhtml_picklistbackend/print'));	 
	}
	public function getFormData()
	{
			if(Mage::registry('adminpicklistData'))
			{
			$formData = Mage::registry('adminpicklistData');
			return $formData;	
			}
	}
}