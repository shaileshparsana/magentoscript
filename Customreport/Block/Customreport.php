<?php
class Plumtree_Customreport_Block_Customreport extends Mage_Core_Block_Template {
 
    public function _prepareLayout() {
        return parent::_prepareLayout();
    }
 
    public function getCustomreport() {
        if (!$this->hasData('customreport')) {
            $this->setData('customreport', Mage::registry('customreport'));
        }
        return $this->getData('customreport');
    }
 
}