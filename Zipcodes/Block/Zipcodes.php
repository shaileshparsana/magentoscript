<?php
class Plumtree_Zipcodes_Block_Zipcodes extends Mage_Core_Block_Template
{
	public function _prepareLayout()
    {
		return parent::_prepareLayout();
    }
    
     public function getZipcodes()     
     { 
        if (!$this->hasData('zipcodes')) {
            $this->setData('zipcodes', Mage::registry('zipcodes'));
        }
        return $this->getData('zipcodes');
        
    }
}