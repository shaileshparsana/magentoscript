<?php
require_once 'abstract.php';

/**
 * Mydons Inventoryupdate Shell Script
 *
 * @author     Mydons
 */
class Plumtree_Shell_Updateproduct extends Mage_Shell_Abstract {

    /**
     * Run script
     *
     */
    public function run() {
        Mage::app()->setCurrentStore(Mage_Core_Model_App::ADMIN_STORE_ID);
        $file_handle = fopen("../vendor_feed/update_product_feed/update_product_import.csv", "r") or die("CSV file not found or not able to open it.");
		$key = array();
		$i = 1;
		
		$count =0;
		$fp = file('../vendor_feed/update_product_feed/update_product_import.csv', FILE_SKIP_EMPTY_LINES);
		
		echo '<style type="text/css">';
		echo 'ul { list-style-type:none; padding:0; margin:0; }';
		echo 'li { margin-left:0; border:1px solid #ccc; margin:2px; padding:2px 2px 2px 2px; font:normal 12px sans-serif; }';
		echo 'img { margin-right:5px; }';
		echo '</style>';
		echo '<ul ><li style="background-color:#DDF;">Starting profile execution, please wait... </li><li style="background-color:#DDF;">Warning: Please do not close the window during importing/exporting data </li><li style="background-color:#DDF;"> Found '.(count($fp)-1).' rows.</li>';
		
		$massage = array();
		ob_start();
        while (!feof($file_handle)) {
			flush();
            $line_of_text = fgetcsv($file_handle, 0);
				if($i==1){
					$key=$line_of_text;
				}
			if($i!=1){
				if(!empty($line_of_text)){
					
					$productimport = new Mage_Catalog_Model_Convert_Adapter_Productscript();
					$log = $productimport->updateRow(array_combine($key,$line_of_text));
					
					if($log=="" || $log === true){
						$logStyle ='<li style="background-color:#DDF;">';
						$log = 'Success : sku = '.$line_of_text['0'];
						$count++;
					}else{
						$logStyle ='<li style="background-color:#FDD;">';
						$log = 'sku = '.$line_of_text['0'].' '.$log;
					}
				}
				else
				{
					continue;
				}
			}
		   $i++;
		   echo $logStyle .$log.'</li>';
           Mage::log($log, null, "vendor-feed-update.log");
		   ob_end_flush();
        }
		echo '<li style="background-color:#DDF;">Imported '.$count.' records</li><li style="background-color:#DDF;"> Finished profile execution. </li></ul>';
		flush();
		fclose($file_handle);
    }

    /**
     * Retrieve Usage Help Message
     *
     */
    public function usageHelp() {
        return <<<USAGE
Usage:  php -f inventory.php -- [options]
        php -f inventory.php 


USAGE;
    }

}

$shell = new Plumtree_Shell_Updateproduct();
$shell->run();