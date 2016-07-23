<?php

class Plumtree_Zipcodes_Model_Zipcodes extends Mage_Core_Model_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('zipcodes/zipcodes');
    }
}