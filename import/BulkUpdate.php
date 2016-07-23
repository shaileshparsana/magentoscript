<?php

require_once 'abstract.php';
class Plumtree_Shell_Insertproduct extends Mage_Shell_Abstract {
    /**
     * Run script
     *
     */ 
    public function run() {
        Mage::app()->setCurrentStore(Mage_Core_Model_App::ADMIN_STORE_ID);
		//$ch = curl_init();
//		$source = "http://www.thebetterhealthstore.com/magentodata/Magento.csv";
//		curl_setopt($ch, CURLOPT_URL, $source);
//		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
//		$data = curl_exec ($ch);
//		curl_close ($ch);
		date_default_timezone_set('US/Eastern');
		$curr_hour=date('H');
		$fiename="EST_".ltrim($curr_hour, '0').".csv";
 		$destination =   dirname(dirname(__FILE__))."/var/product_import/Catalog/BulkUpdate/".$fiename;
		

		if (!file_exists($destination)) {
			$fiename="EST_".ltrim($curr_hour, '0').".CSV";
			$destination =   dirname(dirname(__FILE__))."/var/product_import/Catalog/BulkUpdate/".$fiename;
		
		}
		
        $file_handle = fopen($destination, "r") or die("CSV file not found or not able to open it.");
		//$file_handle = fopen("../var/import/Magento-products.csv", "r") or die("CSV file not found or not able to open it.");
		$key = array();
		$i = 1;
		$count =0;
		//$fp = file("../var/import/Magento.csv",FILE_SKIP_EMPTY_LINES);
		//echo '<style type="text/css">';
//		echo 'ul { list-style-type:none; padding:0; margin:0; }';
//		echo 'li { margin-left:0; border:1px solid #ccc; margin:2px; padding:2px 2px 2px 2px; font:normal 12px sans-serif; }';
//		echo 'img { margin-right:5px; }';
//		echo '</style>';
//		echo '<ul ><li style="background-color:#DDF;">Starting profile execution, please wait... </li><li style="background-color:#DDF;">Warning: Please do not close the window during importing/exporting data </li><li style="background-color:#DDF;"></li>';
		Mage::log("\r\nFilename:- ".$destination, null, "bulk_update.log");		
		$logErrorStart = "######### ";
		$logErrorStart .= "Starting profile execution Time : ".date("Y-m-d h:i:s a");
		$logErrorStart .= " #########";
		Mage::log($logErrorStart, null, "bulk_update.log");
		ob_start();
		$counter=0;
        while (!feof($file_handle)) {
			flush();
			
			
            $line_of_text = fgetcsv($file_handle, 0);	
			
			if($i==1){
				$key=$line_of_text;
			}
			if($i!=1){ 
				if(!empty($line_of_text)){
					//0001579409123
					if($line_of_text[1] == '0001579409123') continue;
					
					//to empty url key column.
					$line_of_text[11]='';
					
					
					$productimport = new Mage_Catalog_Model_Convert_Adapter_Productscript();
					//echo "<pre>";print_r($line_of_text);//exit;
					$log = $productimport->saveRow(array_combine($key,array_map('trim',$line_of_text)));
				//	console.log($i);
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
			//	echo $logStyle .$logError.'</li>';
	            Mage::log($logError, null, "bulk_update.log");
				$logError ="";
			}			
        }
		$logErrorEnd = "######### ";
		$logErrorEnd .= "Ending profile execution Time : ".date("Y-m-d h:i:s a");
		$logErrorEnd .= " #########";
		Mage::log($logErrorEnd, null, "bulk_update.log");
		$logErrorEnd = "";		
		$logSuccessEnd = "######### ";
		//$logSuccessEnd .= "Ending profile execution Time : ".date("Y-m-d h:i:s a");
		$logSuccessEnd .= ' Imported '.$count.' records. Finished profile execution.';
		$logSuccessEnd .= " #########";
		Mage::log($logSuccessEnd, null, "bulk_update.log");
		$logSuccessEnd = "";		
	//	echo '<li style="background-color:#DDF;">Imported '.$count.' records</li><li style="background-color:#DDF;"> Finished profile execution. </li></ul>';
		ob_end_flush();
		//flush();
        fclose($file_handle);
		
		$new_location =   dirname(dirname(__FILE__))."/var/product_import/Catalog_Archive/BulkUpdate/";
		$newfilename=date('Ymd').'_'.$fiename;
		$newLocation = $new_location.$newfilename;
		if (copy($destination, $newLocation)) {
				unlink( $destination );
		}
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

$timezone = date_default_timezone_get();
$msg = "The current server timezone is: " . $timezone . ' - '. date('m/d/Y h:i:s a', time());
@mail('pravin@plumtreegroup.net','BHS cron' ,$msg );

?>

