<?php

class Plumtree_Scanproducts_Model_Mysql4_Scanproducts_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('scanproducts/scanproducts');
    }
}