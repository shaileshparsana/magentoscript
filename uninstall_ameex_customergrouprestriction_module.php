<?php
set_time_limit(0); 
	ini_set('memory_limit', '-1'); 
	$rootdir = dirname(__DIR__);
	require_once '../app/Mage.php';
	umask(0);
	Mage::app();
	
	$setup = Mage::getModel('eav/entity_setup',  'core_setup');
	$setup->startSetup();
	$setup->removeAttribute('catalog_category', 'gift_position');
	
	$setup->run("
	  DELETE FROM {$setup->getTable('core/resource')}
		WHERE code = 'holidaygiftposition_setup'");
	$setup->endSetup();
	
	echo 'Product Restriction Based On Customer Group has been uninstalled completely';
