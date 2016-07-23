<?php

class Plumtree_Zipcodes_Block_Adminhtml_Zipcodes_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
  protected function _prepareForm()
  {
      $form = new Varien_Data_Form();
      $this->setForm($form);
      $fieldset = $form->addFieldset('zipcodes_form', array('legend'=>Mage::helper('zipcodes')->__('Item information')));
     
      $fieldset->addField('email', 'text', array(
          'label'     => Mage::helper('zipcodes')->__('Email'),
          'class'     => 'required-entry',
          'required'  => true,
          'name'      => 'title',
      ));
	  $fieldset->addField('postcode', 'text', array(
          'label'     => Mage::helper('zipcodes')->__('Postcode'),
          'class'     => 'required-entry',
          'required'  => true,
          'name'      => 'postcode',
      ));

     
		
      $fieldset->addField('status', 'select', array(
          'label'     => Mage::helper('zipcodes')->__('Status'),
          'name'      => 'status',
          'values'    => array(
              array(
                  'value'     => 1,
                  'label'     => Mage::helper('zipcodes')->__('Pending'),
              ),

              array(
                  'value'     => 2,
                  'label'     => Mage::helper('zipcodes')->__('Success'),
              ),
          ),
      ));
     
    
     
      if ( Mage::getSingleton('adminhtml/session')->getZipcodesData() )
      {
          $form->setValues(Mage::getSingleton('adminhtml/session')->getZipcodesData());
          Mage::getSingleton('adminhtml/session')->setZipcodesData(null);
      } elseif ( Mage::registry('zipcodes_data') ) {
          $form->setValues(Mage::registry('zipcodes_data')->getData());
      }
      return parent::_prepareForm();
  }
}