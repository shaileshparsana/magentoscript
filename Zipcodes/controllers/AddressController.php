<?php  
require_once 'Mage/Customer/controllers/AddressController.php';
class Plumtree_Zipcodes_AddressController extends Mage_Customer_AddressController
{
	public function formPostAction()
    {
	    if (!$this->_validateFormKey()) {
            return $this->_redirect('*/*/');
        }
        // Save data
        if ($this->getRequest()->isPost()) {
            $customer = $this->_getSession()->getCustomer();
            /* @var $address Mage_Customer_Model_Address */
            $address  = Mage::getModel('customer/address');
            $addressId = $this->getRequest()->getParam('id');
            if ($addressId) 
			{
                $existsAddress = $customer->getAddressById($addressId);
                if ($existsAddress->getId() && $existsAddress->getCustomerId() == $customer->getId()) 
				{
                    $address->setId($existsAddress->getId());
                }
            }

            $errors = array();

            /* @var $addressForm Mage_Customer_Model_Form */
            $addressForm = Mage::getModel('customer/form');
            $addressForm->setFormCode('customer_address_edit')
                ->setEntity($address);
            $addressData    = $addressForm->extractData($this->getRequest());
            $addressErrors  = $addressForm->validateData($addressData);
            
			if ($addressErrors !== true) 
			{
                $errors = $addressErrors;
            }

            try {
                $addressForm->compactData($addressData);
                $address->setCustomerId($customer->getId())
                    ->setIsDefaultBilling($this->getRequest()->getParam('default_billing', false))
                    ->setIsDefaultShipping($this->getRequest()->getParam('default_shipping', false));

                $addressErrors = $address->validate();
                if ($addressErrors !== true) {
                    $errors = array_merge($errors, $addressErrors);
                }

                if (count($errors) === 0) 
				{
					if (strpos($this->getRequest()->getParam('postcode'), '-') !== false) {
						$arr = explode("-",$this->getRequest()->getParam('postcode'));

						 $current_zipcode = $arr[0];
						
					}
					else
					{
						$current_zipcode = $this->getRequest()->getParam('postcode');
					}
					
                    $configValue = Mage::getStoreConfig('zipcode_list/general/zipcodes');
					$zipcodes_list=explode(',',$configValue);
					
					if($this->getRequest()->getParam('default-shipping-selected'))
					{
						if(!in_array($current_zipcode,$zipcodes_list) )
						{
								$this->_getSession()->addError($this->__("Sorry but we don't deliver to this location, please enter another address or contact us."));
								$this->_redirectSuccess(Mage::getUrl('*/*/edit', array('id'=>$this->getRequest()->getParam('id'),'_secure'=>true)));
								return;					
						}
					}
					
					if(!in_array($current_zipcode,$zipcodes_list) && $this->getRequest()->getParam('default_shipping') )
					{
						$this->_getSession()->addError($this->__("Sorry but we don't deliver to this location, please enter another address or contact us."));
						$this->_redirectSuccess(Mage::getUrl('*/*/edit', array('id'=>$this->getRequest()->getParam('id'),'_secure'=>true)));
						return;					
					}
					
					$address->save();
                    $this->_getSession()->addSuccess($this->__('The address has been saved.'));
                    $this->_redirectSuccess(Mage::getUrl('*/*/index', array('_secure'=>true)));
                    return;
                } else {
                    $this->_getSession()->setAddressFormData($this->getRequest()->getPost());
                    foreach ($errors as $errorMessage) {
                        $this->_getSession()->addError($errorMessage);
                    }
                }
            } catch (Mage_Core_Exception $e) {
                $this->_getSession()->setAddressFormData($this->getRequest()->getPost())
                    ->addException($e, $e->getMessage());
            } catch (Exception $e) {
                $this->_getSession()->setAddressFormData($this->getRequest()->getPost())
                    ->addException($e, $this->__('Cannot save address.'));
            }
        }

        return $this->_redirectError(Mage::getUrl('*/*/edit', array('id' => $address->getId())));
    }
}