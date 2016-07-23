<?php
class Plumtree_Customreport_Block_Adminhtml_Customreport extends Mage_Adminhtml_Block_Widget_Grid_Container
{	
	 public function __construct()
    {
        $this->_blockGroup = 'customreport';
        $this->_controller = 'adminhtml_customreport';
        $this->_headerText = Mage::helper('customreport')->__('Custom Report');
        parent::__construct();
        $this->_removeButton('add');
        
    }
   	
}
