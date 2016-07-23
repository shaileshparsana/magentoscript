<?php
class Plumtree_Scanproducts_Block_Adminhtml_Scanproducts extends Mage_Adminhtml_Block_Widget_Grid_Container
{
  public function __construct()
  {
    $this->_controller = 'adminhtml_scanproducts';
    $this->_blockGroup = 'scanproducts';
    $this->_headerText = Mage::helper('scanproducts')->__('Item Manager');
    $this->_addButtonLabel = Mage::helper('scanproducts')->__('Add Item');
    parent::__construct();
  }
}