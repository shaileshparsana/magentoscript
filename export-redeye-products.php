<?php
ini_set('display_errors', 1);
require_once 'abstract.php';

/**
 * Mydons Inventoryupdate Shell Script
 *
 * @author     Mydons
 */
class Plumtree_Shell_Categoryproducts extends Mage_Shell_Abstract {
	
    /**
     * Run script
     *
     */
    public function run() {
		Mage::app()->setCurrentStore(Mage_Core_Model_App::ADMIN_STORE_ID);
        $file_handle = fopen("../vendor_feed/other_update/update_product.csv", "r") or die("CSV file not found or not able to open it.");
		$key = array();
		$i = 1;
		
		$count =0;
		$fp = file('../vendor_feed/other_update/update_product.csv', FILE_SKIP_EMPTY_LINES);
		
		echo '<style type="text/css">';
		echo 'ul { list-style-type:none; padding:0; margin:0; }';
		echo 'li { margin-left:0; border:1px solid #ccc; margin:2px; padding:2px 2px 2px 2px; font:normal 12px sans-serif; }';
		echo 'img { margin-right:5px; }';
		echo '</style>';
		echo '<ul ><li style="background-color:#DDF;">Starting profile execution, please wait... </li><li style="background-color:#DDF;">Warning: Please do not close the window during importing/exporting data </li><li style="background-color:#DDF;"> Found '.(count($fp)-1).' rows.</li>';
		$file_path ='../custom_script/export_Redeye_products.csv';
		$massage = array();
		ob_start();
		$shopid=array();
		$pcatid=array();
		$pcat=array();
		$mage_csv = new Varien_File_Csv(); 
        while (!feof($file_handle)) {
			flush();
            $line_of_text = fgetcsv($file_handle, 0);
				if($i==1){
					$key=$line_of_text;
				}
			if($i!=1){
				$log = "";
				if(!empty($line_of_text)){
					$row = array_combine($key,$line_of_text);
					$product = Mage::getModel('catalog/product')->loadByAttribute('sku', $row['sku']); 
					//$product = Mage::getModel('catalog/product')->load($row['sku'], 'sku');
					//$childIds = Mage::getModel('catalog/product_type_configurable')->getChildrenIds($product->getId());
					
					$configurable= Mage::getModel('catalog/product_type_configurable')->setProduct($product);
					$simpleCollection = $configurable->getUsedProductCollection()->addAttributeToSelect('*')->addFilterByRequiredOptions();
					
					foreach($simpleCollection as $child) {
						//print_r($child->getName());  // You can use any of the magic get functions on this object to get the value
						
						$data['sku'] = $child->getSku();
						$data['type'] = $child->getTypeId();
						$data['category_ids'] = join(',', $child->getCategoryIds());
						$data['name'] = $child->getName();
						$data['websites'] = join(',', $child->getWebsiteIds());
						$products_row[] = $data;
						$child->clearInstance(); 
					}
					$data['sku'] = $product->getSku();
					$data['type'] = $product->getTypeId();
					$data['category_ids'] = join(',', $product->getCategoryIds());
					$data['name'] = $product->getName();
					$data['websites'] = join(',', $product->getWebsiteIds());
					$products_row[] = $data;
						
				}
				else
				{
					continue;
				}
			}
		   $i++;
		   echo $logStyle .$log.'</li>';
           Mage::log($log, null, "category-feed-update.log");
		   ob_end_flush();
        }
		$headrs = array("sku","type","category_ids","name","websites"); 
		
		 array_unshift($products_row,$headrs);
		
	
		
		$mage_csv->saveData($file_path,$products_row);
		echo "Successfully $products exported at:-$file_path";
		echo '<li style="background-color:#DDF;">update '.$count.' records</li><li style="background-color:#DDF;"> Finished profile execution. </li></ul>';
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
$shell = new Plumtree_Shell_Categoryproducts();
$shell->run();