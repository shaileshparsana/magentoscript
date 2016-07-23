<?php

class Plumtree_Scanproducts_Model_Scanproducts extends Mage_Core_Model_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('scanproducts/scanproducts');
    }
}