<?php

class Plumtree_Zipcodes_Block_Adminhtml_Zipcodes_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{

  public function __construct()
  {
      parent::__construct();
      $this->setId('zipcodes_tabs');
      $this->setDestElementId('edit_form');
      $this->setTitle(Mage::helper('zipcodes')->__('Item Information'));
  }

  protected function _beforeToHtml()
  {
      $this->addTab('form_section', array(
          'label'     => Mage::helper('zipcodes')->__('Item Information'),
          'title'     => Mage::helper('zipcodes')->__('Item Information'),
          'content'   => $this->getLayout()->createBlock('zipcodes/adminhtml_zipcodes_edit_tab_form')->toHtml(),
      ));
     
      return parent::_beforeToHtml();
  }
}