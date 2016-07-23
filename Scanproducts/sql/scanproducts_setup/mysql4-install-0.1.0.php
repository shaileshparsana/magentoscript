<?php

$installer = $this;

$installer->startSetup();

$installer->run("

-- DROP TABLE IF EXISTS {$this->getTable('scanproducts')};
CREATE TABLE {$this->getTable('scanproducts')} (
  `scanproducts_id` int(11) unsigned NOT NULL auto_increment,
  `customer_id` int(10) unsigned NOT NULL,
  `filename` varchar(255) NOT NULL default '',
  `content` text NOT NULL default '',
  `created_time` datetime NULL,
  `update_time` datetime NULL,
  PRIMARY KEY (`scanproducts_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

    ");

$installer->endSetup(); 