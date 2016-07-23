<?php   
class Plumtree_Picklist_Block_Print extends Mage_Core_Block_Template
{   
 
		public function getorderscollection()
		{
		
			return $formData = Mage::registry('picklistData');
				
		}

}