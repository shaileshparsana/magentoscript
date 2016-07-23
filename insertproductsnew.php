ok <?php
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
		$key = array();
		$i = 1;
		
		
        while (!feof($file_handle)) {
			
            $line_of_text = fgetcsv($file_handle, 0);	
			if($i==1){
				$key=$line_of_text;
			}
			
			if($i!=1){
				
				$productimport = new Mage_Catalog_Model_Convert_Adapter_Productscript();
				$log = $productimport->saveRow(array_combine($key,$line_of_text));
				if($log=="" || $log === true){
					$log = 'Success : sku = '.$line_of_text['5'].' '.$log;
				}else{
						$log = 'Error : sku = '.$line_of_text['5'].' '.$log;
				}
			}
			$i++;
			echo $log.'<br>';
           Mage::log($log, null, "vendor-feed-insert.log");
        }
		
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