<?php
class Magentojack_Custom_Block_Form extends Mage_Core_Block_Template{
	protected $_entity = null;
    protected $_type   = null;
    protected $_giftMessage = null;

    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('custom/form.phtml');
    }

}