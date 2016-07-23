<?php

class Plumtree_Scanproducts_Model_Mysql4_Scanproducts extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {    
        // Note that the scanproducts_id refers to the key field in your database table.
        $this->_init('scanproducts/scanproducts', 'scanproducts_id');
    }
}