<?php
class Plumtree_Zipcodes_Block_Adminhtml_Zipcodes extends Mage_Adminhtml_Block_Widget_Grid_Container
{
  public function __construct()
  {
    $this->_controller = 'adminhtml_zipcodes';
    $this->_blockGroup = 'zipcodes';
    $this->_headerText = Mage::helper('zipcodes')->__('Item Manager');
    $this->_addButtonLabel = Mage::helper('zipcodes')->__('Add Item');
    parent::__construct();
  }
}