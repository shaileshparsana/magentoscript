<?php

$installer = $this;

$installer->startSetup();

$installer->run("

-- DROP TABLE IF EXISTS {$this->getTable('zipcodes')};
CREATE TABLE {$this->getTable('zipcodes')} (
  `zipcodes_id` int(11) unsigned NOT NULL auto_increment,
  `email` varchar(255)  NULL default '',
  `postcode` varchar(255)  NULL default '',
   `status` varchar(32) NOT NULL default '0',
  `customer_id` int(11)  NULL ,
  `created_time` datetime NULL,
  `update_time` datetime NULL,
  PRIMARY KEY (`zipcodes_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

    ");

$installer->endSetup(); 