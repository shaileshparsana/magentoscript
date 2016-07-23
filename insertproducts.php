<?php
require_once 'abstract.php';

/**
 * Mydons Inventoryupdate Shell Script
 *
 * @author     Mydons
 */
class Plumtree_Shell_Insertproduct extends Mage_Shell_Abstract {

    /**
     * Run script
     *
     */
    public function run() {
        Mage::app()->setCurrentStore(Mage_Core_Model_App::ADMIN_STORE_ID);
		
		$file_handle = fopen("../vendor_feed/new_product_feed/new_product_import.csv", "r") or die("CSV file not found or not able to open it.");
		$curretDateTime = date("Y-m-d h:i:s a");
		$key = array();
		$i = 1;
		$count =0;
		$fp = file('../vendor_feed/new_product_feed/new_product_import.csv', FILE_SKIP_EMPTY_LINES);
		
        while (!feof($file_handle)) {
		//	flush();
            $line_of_text = fgetcsv($file_handle, 0);	
			if($i==1){
				$key=$line_of_text;
			}
			
			if($i!=1){
				if(!empty($line_of_text)){
					$importData =array_combine($key,$line_of_text);
					if(!empty($importData['size'])){
						$productimport = new Mage_Catalog_Model_Convert_Adapter_Productscript();
						$log1 = $productimport->getAttributeOptionValue('size',trim($importData['size']));
						if($log1==""){
							$optonVialue = $productimport->addAttributeOption("size", trim($importData['size']));
							if($optonVialue==""){
								$log1 .= $__( 'Size option value "%s" not defined', $importData['size']);
							}
						}
					}
					if(!empty($importData['color'])){
						$productimport = new Mage_Catalog_Model_Convert_Adapter_Productscript();
						$log1 = $productimport->getAttributeOptionValue('color',trim($importData['color']));
						if($log1==""){
							$optonVialue = $productimport->addAttributeOption("color", trim($importData['color']));
							if($optonVialue==""){
								$log1 .= $__( 'Color option value "%s" not defined', $importData['color']);
							}
						}
					}
					if(!empty($importData['size_painting'])){
						$productimport = new Mage_Catalog_Model_Convert_Adapter_Productscript();
						$log1 = $productimport->getAttributeOptionValue('size_painting',trim($importData['size_painting']));
						if($log1==""){
							$optonVialue = $productimport->addAttributeOption("size_painting", trim($importData['size_painting']));
							if($optonVialue==""){
								$log1 .= $__( 'Printing Size option value "%s" not defined', $importData['size_painting']);
							}
						}
					}
				}
				else
				{
					continue;
				}
			}
			$i++;
            Mage::log($log1, null, "attribute-insert.log");
        }
        fclose($file_handle); 
    
		
		
		
       $file_handle = fopen("../vendor_feed/new_product_feed/new_product_import.csv", "r") or die("CSV file not found or not able to open it.");
		$key = array();
		$i = 1;
		$count =0;
		$fp = file('../vendor_feed/new_product_feed/new_product_import.csv', FILE_SKIP_EMPTY_LINES);
		
		echo '<style type="text/css">';
		echo 'ul { list-style-type:none; padding:0; margin:0; }';
		echo 'li { margin-left:0; border:1px solid #ccc; margin:2px; padding:2px 2px 2px 2px; font:normal 12px sans-serif; }';
		echo 'img { margin-right:5px; }';
		echo '</style>';
		echo '<ul ><li style="background-color:#DDF;">Starting profile execution, please wait... </li><li style="background-color:#DDF;">Warning: Please do not close the window during importing/exporting data </li><li style="background-color:#DDF;"> Found '.(count($fp)-1).' rows.</li>';
		
		$logErrorStart = "######### ";
		$logErrorStart .= "Starting profile execution Time : ".date("Y-m-d h:i:s a");
		$logErrorStart .= " #########";
		Mage::log($logErrorStart, null, "vendor-feed-insert.log");
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
					
					$log = $productimport->saveRow(array_combine($key,array_map('trim',$line_of_text)));
					if($log=="" || $log === true){
						$logStyle ='<li style="background-color:#DDF;">';
						$logSuccess = 'Success : sku = '.$line_of_text['5'];
						$count++; 
					}else{
						$logStyle ='<li style="background-color:#FDD;">';
						$logError .= 'sku = '.$line_of_text['5'].' '.$log;
					}
				}
				else
				{
					continue;
				}
			}
			$i++;
			if($logError != ""){
				echo $logStyle .$logError.'</li>';
	            Mage::log($logError, null, "vendor-feed-insert.log");
				$logError ="";
			}
			
        }
		$logErrorEnd = "######### ";
		$logErrorEnd .= "Ending profile execution Time : ".date("Y-m-d h:i:s a");
		$logErrorEnd .= " #########";
		Mage::log($logErrorEnd, null, "vendor-feed-insert.log");
		$logErrorEnd = "";
		
		$logSuccessEnd = "######### ";
		//$logSuccessEnd .= "Ending profile execution Time : ".date("Y-m-d h:i:s a");
		$logSuccessEnd .= ' Imported '.$count.' records. Finished profile execution.';
		$logSuccessEnd .= " #########";
		Mage::log($logSuccessEnd, null, "vendor-feed-insert.log");
		$logSuccessEnd = "";
		
		echo '<li style="background-color:#DDF;">Imported '.$count.' records</li><li style="background-color:#DDF;"> Finished profile execution. </li></ul>';
		ob_end_flush();
		//flush();
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

$shell = new Plumtree_Shell_Insertproduct();
$shell->run();