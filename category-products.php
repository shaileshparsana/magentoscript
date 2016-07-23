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
        $file_handle = fopen("../vendor_feed/other_update/update_categories.csv", "r") or die("CSV file not found or not able to open it.");
		$key = array();
		$i = 1;
		
		$count =0;
		$fp = file('../vendor_feed/other_update/update_categories.csv', FILE_SKIP_EMPTY_LINES);
		
		echo '<style type="text/css">';
		echo 'ul { list-style-type:none; padding:0; margin:0; }';
		echo 'li { margin-left:0; border:1px solid #ccc; margin:2px; padding:2px 2px 2px 2px; font:normal 12px sans-serif; }';
		echo 'img { margin-right:5px; }';
		echo '</style>';
		echo '<ul ><li style="background-color:#DDF;">Starting profile execution, please wait... </li><li style="background-color:#DDF;">Warning: Please do not close the window during importing/exporting data </li><li style="background-color:#DDF;"> Found '.(count($fp)-1).' rows.</li>';
		
		$massage = array();
		ob_start();
		$shopid=array();
		$pcatid=array();
		$pcat=array();
		
		$resource = Mage::getSingleton('core/resource'); //get an instance of the core resource
		$connection = $resource->getConnection('core_write'); //get an instance of the write connection
		$tableName = $resource->getTableName('catalog/category_product'); //this should add the prefix if you have one
		
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
					$oldCatIds = explode("|",$row['old_cat_ids']);
					$shopid[]= $row['new_cat_id'];
					foreach($oldCatIds as $id){
					  	$_category = Mage::getModel('catalog/category')->load($id);
					  	$_productCollection = Mage::getModel('catalog/product')->getCollection();
					   	$_productCollection->addCategoryFilter($_category);
						foreach($_productCollection as $_product)
						{
							$_product->getSku()." ";
							$position = $_product->getCatIndexPosition();
							if($position == 1){
								$position = 50;	
							}
							$cIds = $_product->getCategoryIds();
							$id = $_product->getId();
							
							
							
						
							//get last category id
							$flag = 0;
							$lastId = $row['new_cat_id'];
							for($j=0;$j<=count($cIds)-1;$j++)
							{
								if($cIds[$j]==$lastId) 
								{
									$flag = 1;
								}
							}
							// now add that last id
							if($flag != 1){
								$cIds[] = $lastId;	
									Mage::getSingleton('catalog/category_api')->assignProduct($lastId,$_product->getId());
									$sql = "UPDATE {$tableName} SET `position` = {$position} WHERE `category_id` = {$lastId} and `product_id` = {$id}";		//set the position to 0 for the product in all the categories.
									$connection->query($sql); //run the query
								 
									
							}
							
							//$_product->setCategoryIds($cIds);
//							$_product->save();
							
							if($flag!=1 ){
								$logStyle ='<li style="background-color:#DDF;">';
								$log .= 'Success : sku = '. $_product->getSku(). '.  >> New Category :  '.$row['new_cat_id'].'. CAT IDS :  '.implode(",",$cIds).'<br>';
								$count++;
							}else{
								$logStyle ='<li style="background-color:#FDD;">';
								$log .= 'sku = '.$_product->getSku().'. >> '.$row['new_cat_id'].' category already added.<br>';
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
		   echo $logStyle .$log.'</li>';
           Mage::log($log, null, "category-feed-update.log");
		   ob_end_flush();
        }
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