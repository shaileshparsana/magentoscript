<?php

class Plumtree_Zipcodes_Model_Mysql4_Zipcodes extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {    
        // Note that the zipcodes_id refers to the key field in your database table.
        $this->_init('zipcodes/zipcodes', 'zipcodes_id');
    }
}