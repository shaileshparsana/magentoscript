<?php
	
	set_time_limit(0); 
	ini_set('memory_limit', '-1'); 
	$rootdir = dirname(__DIR__);
	require_once $rootdir.'/storeathome/app/Mage.php';
	umask(0);
	Mage::app();
	$row = 0;

$target_file  = Mage::getBaseDir().'/attributes/manufacturer.csv';

 if (($handle = fopen($target_file, "r")) !== FALSE) 
 {			
	//	Mage::app()->setCurrentStore(Mage_Core_Model_App::ADMIN_STORE_ID);
			while (($data = fgetcsv($handle, 10000, ",")) !== FALSE)
			{				
			   $attributecode = 'testsize';
			   $brandvalue = trim($data[0]);
			   print_r($brandvalue);
			   setOrAddOptionAttribute($attributecode,$brandvalue);			   
			   echo "Updated: Name: '",$data[0],PHP_EOL;
			   echo " <br/>";
			   $row++;
			}
			fclose($handle);
 } 
function setOrAddOptionAttribute($arg_attribute, $arg_value) 
	{		
		Mage::app()->setCurrentStore(Mage_Core_Model_App::ADMIN_STORE_ID); 
		$attribute_model = Mage::getModel('eav/entity_attribute');
		$attribute_options_model = Mage::getModel('eav/entity_attribute_source_table');

		$attribute_code = $attribute_model->getIdByCode('catalog_product', $arg_attribute);
		$attribute = $attribute_model->load($attribute_code);

		$attribute_options_model->setAttribute($attribute);
		$options = $attribute_options_model->getAllOptions(false);

		// determine if this option exists
		$value_exists = false;
		foreach($options as $option) {
			if ($option['label'] == $arg_value) {
				$value_exists = true;
				break;
			}
		}
		// if this option does not exist, add it.
		if (!$value_exists) {
			if(!empty($arg_value))
			{
				$attribute->setData('option', array(
					'value' => array(
						'option' => array($arg_value,$arg_value) //$arg_value(return  0=>'colorvalue ' 0 is store id...)
					),
				));				
				$attribute->save();
				echo "<br/> Attribute Created ".$arg_value." <br/>";
			}	
		}
	}	
?>