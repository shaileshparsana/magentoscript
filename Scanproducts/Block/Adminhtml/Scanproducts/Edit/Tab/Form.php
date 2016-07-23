<?php

class Plumtree_Scanproducts_Block_Adminhtml_Scanproducts_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
  protected function _prepareForm()
  {
      $form = new Varien_Data_Form();
      $this->setForm($form);
      $fieldset = $form->addFieldset('scanproducts_form', array('legend'=>Mage::helper('scanproducts')->__('Item information')));
     
      $fieldset->addField('filename', 'text', array(
          'label'     => Mage::helper('scanproducts')->__('File Name'),
          'class'     => 'required-entry',
          'required'  => true,
          'name'      => 'filename',
      ));
	   $fieldset->addField('customer_id', 'text', array(
          'label'     => Mage::helper('scanproducts')->__('Customer ID'),
          'class'     => 'required-entry',
          'required'  => true,
          'name'      => 'customer_id',
      ));
      $fieldset->addField('file_data', 'editor', array(
          'name'      => 'file_data',
          'label'     => Mage::helper('scanproducts')->__('Content'),
          'title'     => Mage::helper('scanproducts')->__('Content'),
          'style'     => 'width:700px; height:500px;',
          'wysiwyg'   => false,
          'required'  => false,
      ));

      $fieldset->addField('content', 'editor', array(
          'name'      => 'content',
          'label'     => Mage::helper('scanproducts')->__('UPC List'),
          'title'     => Mage::helper('scanproducts')->__('Content'),
          'style'     => 'width:700px; height:500px;',
          'wysiwyg'   => false,
          'required'  => false,
      ));
     
      if ( Mage::getSingleton('adminhtml/session')->getScanproductsData() )
      {
          $form->setValues(Mage::getSingleton('adminhtml/session')->getScanproductsData());
          Mage::getSingleton('adminhtml/session')->setScanproductsData(null);
      } elseif ( Mage::registry('scanproducts_data') ) {
          $form->setValues(Mage::registry('scanproducts_data')->getData());
      }
      return parent::_prepareForm();
  }
}