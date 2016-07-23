<?php
/**
 * Mirasvit
 *
 * This source file is subject to the Mirasvit Software License, which is available at http://mirasvit.com/license/.
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs.
 * Please refer to http://www.magentocommerce.com for more information.
 *
 * @category  Mirasvit
 * @package   Advanced Reports
 * @version   1.0.0
 * @build     358
 * @copyright Copyright (C) 2015 Mirasvit (http://mirasvit.com/)
 */


require_once 'abstract.php';

class Mirasvit_Shell_Advr extends Mage_Shell_Abstract
{
    public function run()
    {
        ini_set('display_errors', 1);
        error_reporting(E_ALL);
        set_time_limit(36000);

        if ($this->getArg('notify')) {
            $this->_notify();
        } elseif ($this->getArg('test')) {
            $this->_test();
        } else { 
            echo $this->usageHelp();
        }
    }

    protected function _notify()
    {
        $emails = Mage::getModel('advd/notification')->getCollection()
            ->addFieldToFilter('is_active', 1);

        foreach ($emails as $email) {
            $email = $email->load($email->getId());
            $email->send();

            echo $email->getRecipientEmail().' OK'.PHP_EOL;
        }
    }

    protected function _test()
    {
        $emails = Mage::getModel('advd/notification')->getCollection()
            ->addFieldToFilter('is_active', 1);

        foreach ($emails as $email) {
            $email = $email->load($email->getId());

            $gmt = Mage::getSingleton('core/date')->gmtTimestamp();
            $local = Mage::getSingleton('core/date')->timestamp();

            echo 'GMT:   '.date('M, d h:i a', $gmt).PHP_EOL;
            echo 'Local: '.date('M, d h:i a', $local).PHP_EOL;
            echo $email->canSend($gmt);

            echo PHP_EOL.PHP_EOL;
        }
    }

    public function _validate() {}

    public function usageHelp()
    {
        return <<<USAGE
Usage:  php -f advr.php -- [options]

  --notify         Send notifications to all subscribed users

USAGE;
    }
}

$shell = new Mirasvit_Shell_Advr();
$shell->run();