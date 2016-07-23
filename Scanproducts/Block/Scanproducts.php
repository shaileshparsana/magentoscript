<?php
class Plumtree_Scanproducts_Block_Scanproducts extends Mage_Core_Block_Template
{
	public function _prepareLayout()
    {
		return parent::_prepareLayout();
    }
    
     public function getScanproducts()     
     { 
        if (!$this->hasData('scanproducts')) {
            $this->setData('scanproducts', Mage::registry('scanproducts'));
        }
        return $this->getData('scanproducts');
        
    }
}