<?php
require_once 'abstract.php';

/**
 * Naim website level tax class update Shell Script
 *
 * @author     Naim
 */
class Plumtree_Shell_Updateproducttaxclass extends Mage_Shell_Abstract {

    /**
     * Run script
     *
     */
    public function run() {
		
		
		
        Mage::app()->setCurrentStore(Mage_Core_Model_App::ADMIN_STORE_ID);
	    		
        $file_handle = fopen("../vendor_feed/other_update/tax-class-chicago.csv", "r") or die("CSV file not found or not able to open it.");
		$key = array();
		$i = 1;
		$count =0;
		$fp = file('../vendor_feed/other_update/tax-class-chicago.csv', FILE_SKIP_EMPTY_LINES);
		
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
					//print_r($line_of_text);
					$product =  Mage::getModel('catalog/product')->loadByAttribute('sku',$line_of_text[0]);     
					//$product->setTaxClassId(7)->save();
					//$product->setStoreId(2)->setTaxClassId(6)->save();
					$productId = $product->getId();
					
					
					/// chicago store
					/*$storeId = 1;					
					Mage::getSingleton('catalog/product_action')->updateAttributes(
						array($productId),
						array('tax_class_id' => '7'),
						$storeId
					);
					
					// baltimore store
					$storeId = 4;					
					Mage::getSingleton('catalog/product_action')->updateAttributes(
						array($productId),
						array('tax_class_id' => '6'),
						$storeId
					);
					
					// morning call store
					$storeId = 3;					
					Mage::getSingleton('catalog/product_action')->updateAttributes(
						array($productId),
						array('tax_class_id' => '8'),
						$storeId
					);
					
					
					// la times store
					$storeId = 5;					
					Mage::getSingleton('catalog/product_action')->updateAttributes(
						array($productId),
						array('tax_class_id' => '9'),
						$storeId
					);
					
					
					// hartford store
					$storeId = 6;					
					Mage::getSingleton('catalog/product_action')->updateAttributes(
						array($productId),
						array('tax_class_id' => '10'),
						$storeId
					);
					
					
					// dailypress store
					$storeId = 7;					
					Mage::getSingleton('catalog/product_action')->updateAttributes(
						array($productId),
						array('tax_class_id' => '11'),
						$storeId
					);*/
					
					// orlando store
					$storeId = 8;					
					Mage::getSingleton('catalog/product_action')->updateAttributes(
						array($productId),
						array('tax_class_id' => '12'),
						$storeId
					);
					
					// south florida store
					$storeId = 9;					
					Mage::getSingleton('catalog/product_action')->updateAttributes(
						array($productId),
						array('tax_class_id' => '13'),
						$storeId
					);
					// Red Eye store
					/*$storeId = 10;					
					Mage::getSingleton('catalog/product_action')->updateAttributes(
						array($productId),
						array('tax_class_id' => '23'),
						$storeId
					);
					
					// San Diego store
					$storeId = 11;					
					Mage::getSingleton('catalog/product_action')->updateAttributes(
						array($productId),
						array('tax_class_id' => '24'),
						$storeId
					);*/
					
					
					//if($log=="" || $log === true){
						$logStyle ='<li style="background-color:#DDF;">';
						$log = 'Success : sku = '.$line_of_text[0];
						$count++;
					//}else{
						//$logStyle ='<li style="background-color:#FDD;">';
						//$log = 'sku = '.$line_of_text[0].' '.$log;
					//}
					
				}
				else
				{
					continue;
				}
			}
			$i++;
			echo $log.'</li>';
			flush();
            Mage::log($log, null, "vendor-feed-insert.log");
			//ob_end_flush();
        }
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

$shell = new Plumtree_Shell_Updateproducttaxclass();
$shell->run();