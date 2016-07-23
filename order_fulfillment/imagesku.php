<?php
ini_set('memory_limit','512M');
	set_time_limit(0); 
	require_once '/app/Mage.php';
	umask(0);
	Mage::app('default');