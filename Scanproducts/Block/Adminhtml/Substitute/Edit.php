<?php

class Plumtree_Scanproducts_Block_Adminhtml_Scanproducts_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        parent::__construct();
                 
        $this->_objectId = 'id';
        $this->_blockGroup = 'scanproducts';
        $this->_controller = 'adminhtml_scanproducts';
        
        $this->_updateButton('save', 'label', Mage::helper('scanproducts')->__('Save Item'));
        $this->_updateButton('delete', 'label', Mage::helper('scanproducts')->__('Delete Item'));
		
        $this->_addButton('saveandcontinue', array(
            'label'     => Mage::helper('adminhtml')->__('Save And Continue Edit'),
            'onclick'   => 'saveAndContinueEdit()',
            'class'     => 'save',
        ), -100);

        $this->_formScripts[] = "
            function toggleEditor() {
                if (tinyMCE.getInstanceById('scanproducts_content') == null) {
                    tinyMCE.execCommand('mceAddControl', false, 'scanproducts_content');
                } else {
                    tinyMCE.execCommand('mceRemoveControl', false, 'scanproducts_content');
                }
            }

            function saveAndContinueEdit(){
                editForm.submit($('edit_form').action+'back/edit/');
            }
        ";
    }

    public function getHeaderText()
    {
        if( Mage::registry('scanproducts_data') && Mage::registry('scanproducts_data')->getId() ) {
            return Mage::helper('scanproducts')->__("Edit Item '%s'", $this->htmlEscape(Mage::registry('scanproducts_data')->getTitle()));
        } else {
            return Mage::helper('scanproducts')->__('Add Item');
        }
    }
}