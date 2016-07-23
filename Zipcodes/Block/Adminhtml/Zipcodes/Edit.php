<?php

class Plumtree_Zipcodes_Block_Adminhtml_Zipcodes_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        parent::__construct();
                 
        $this->_objectId = 'id';
        $this->_blockGroup = 'zipcodes';
        $this->_controller = 'adminhtml_zipcodes';
        
        $this->_updateButton('save', 'label', Mage::helper('zipcodes')->__('Save Item'));
        $this->_updateButton('delete', 'label', Mage::helper('zipcodes')->__('Delete Item'));
		
        $this->_addButton('saveandcontinue', array(
            'label'     => Mage::helper('adminhtml')->__('Save And Continue Edit'),
            'onclick'   => 'saveAndContinueEdit()',
            'class'     => 'save',
        ), -100);

        $this->_formScripts[] = "
            function toggleEditor() {
                if (tinyMCE.getInstanceById('zipcodes_content') == null) {
                    tinyMCE.execCommand('mceAddControl', false, 'zipcodes_content');
                } else {
                    tinyMCE.execCommand('mceRemoveControl', false, 'zipcodes_content');
                }
            }

            function saveAndContinueEdit(){
                editForm.submit($('edit_form').action+'back/edit/');
            }
        ";
    }

    public function getHeaderText()
    {
        if( Mage::registry('zipcodes_data') && Mage::registry('zipcodes_data')->getId() ) {
            return Mage::helper('zipcodes')->__("Edit Item '%s'", $this->htmlEscape(Mage::registry('zipcodes_data')->getTitle()));
        } else {
            return Mage::helper('zipcodes')->__('Add Item');
        }
    }
}