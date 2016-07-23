<?php   
class Plumtree_Picklist_Block_Index extends Mage_Core_Block_Template{   

public function getorderscollection($fromDate,$toDate)
{
echo 'bherer';
print_r($_POST);
exit;
$orders = Mage::getModel('sales/order')->getCollection()
    ->addAttributeToFilter('created_at', array('from'=>$fromDate, 'to'=>$toDate));
	
	return $orders;
}

}