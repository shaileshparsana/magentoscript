<?php 
class MageCoders_QuickOrder_Model_System_Source_Styles extends Mage_Core_Model_Abstract{

	 public function toOptionArray()
     {
	 	$sort = array(
					array(
						'label'=>'Collapse Menu',
						'value'=>'collapse'
					),
					array(
						'label'=>'CMS Page',
						'value'=>'cms_page'
					),					
				);
				
		return $sort;		
		
	 }

}