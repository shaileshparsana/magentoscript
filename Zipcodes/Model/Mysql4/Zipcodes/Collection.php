<?php

class Plumtree_Zipcodes_Model_Mysql4_Zipcodes_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('zipcodes/zipcodes');
    }
}