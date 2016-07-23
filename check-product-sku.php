<?php
require_once 'abstract.php';

/**
 * Naim website level tax class update Shell Script
 *
 * @author     Naim
 */
class Plumtree_Shell_Checkproductsku extends Mage_Shell_Abstract {

    /**
     * Run script
     *
     */
    public function run() {
		
		$file_path ="../vendor_feed/other_update/missing_all_products.csv"; //file path of the CSV file in which the $data to be saved
		$mage_csv = new Varien_File_Csv(); //mage CSV
		$products_row = array();    
		
        Mage::app()->setCurrentStore(Mage_Core_Model_App::ADMIN_STORE_ID);
	    		
        $file_handle = fopen("../vendor_feed/other_update/check-product-sku.csv", "r") or die("CSV file not found or not able to open it.");
		$key = array();
		$i = 1;
		$count =0;
		$fp = file('../vendor_feed/other_update/check-product-sku.csv', FILE_SKIP_EMPTY_LINES);
		
		/*echo '<style type="text/css">';
		echo 'ul { list-style-type:none; padding:0; margin:0; }';
		echo 'li { margin-left:0; border:1px solid #ccc; margin:2px; padding:2px 2px 2px 2px; font:normal 12px sans-serif; }';
		echo 'img { margin-right:5px; }';
		echo '</style>';
		echo '<ul ><li style="background-color:#DDF;">Starting profile execution, please wait... </li><li style="background-color:#DDF;">Warning: Please do not close the window during importing/exporting data </li><li style="background-color:#DDF;"> Found '.(count($fp)-1).' rows.</li>'; */
		//ob_start();
        while (!feof($file_handle)) {
			//flush();
            $line_of_text = fgetcsv($file_handle, 0);			
			if($i==1){
				$key=$line_of_text;				
			}
			
			if($i!=1){ 
				if(!empty($line_of_text)){
					$productid =  Mage::getModel('catalog/product')->getIdBySku($line_of_text[0]); 
					
					if($productid != "" ){
						echo $line_of_text[0].'<br>';						for($i=0; $i<count($key);$i++){						$data[$key[$i]] = $line_of_text[$i];												}
						//$data['sku'] = $line_of_text[0];
						//$data['name'] = $line_of_text[1];
						$products_row[] = $data;
						$count++;	
					}
				}
				else
				{
					continue;
				}
			}
			
			$i++;
			echo $log.'</li>';
			flush();
            Mage::log($log, null, "check-product sku.log");
			//ob_end_flush();
        }
		$headrs = $key;				
		array_unshift($products_row,$headrs);
		$mage_csv->saveData($file_path,$products_row);
		echo '<li style="background-color:#DDF;">Imported '.$count.' records</li><li style="background-color:#DDF;"> Finished profile execution. </li></ul>';
		
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

$shell = new Plumtree_Shell_Checkproductsku();
$shell->run();