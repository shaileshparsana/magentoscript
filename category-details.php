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
		
		
		
        while (!feof($file_handle)) {
			flush();
            $line_of_text = fgetcsv($file_handle, 0);
				if($i==1){
					$key=$line_of_text;
				}
			if($i!=1){
				$log = "";
				
				if(!empty($line_of_text)){
					$row[] = $line_of_text[0];
				}
			}
			$i++;
		}
	//	echo '<pre>';print_r($row);exit;
						$pathArray = array();
						$collection1 = Mage::getModel('catalog/category')->getCollection()
							->addAttributeToSelect('is_active')
							->addAttributeToSelect('path');
							
						$pathIdss = array();
						$a = array();
						foreach($collection1 as $cat1){            
							$pathIds = explode('/', $cat1->getPath());
							//s
						//	echo '<pre>';print_r($pathIds);
							$a[] = $pathIds[count($pathIds)-1];
							//print_r($a);
							//exit;
							
							$collection = Mage::getModel('catalog/category')->getCollection()
								->setStoreId(Mage::app()->getStore()->getId())
								->addAttributeToSelect('name')
								->addAttributeToSelect('is_active')
								->addFieldToFilter('entity_id', array('in' => $pathIds));
							
							
							$pahtByName = '';
							foreach($collection as $cat){                
								$pahtByName .= '/' . $cat->getName();
								$id =  $cat->getId();
							}
									//echo '<br>'.$pahtByName;
							if($a[0] == $id  /*&& in_array(substr($pahtByName ,-(strlen($pahtByName)-1))
,$row,false)*/ ){
							//if($a[0] == $id  && in_array($pahtByName,$row) ){
								$pathArray['name'] = $pahtByName;
								$pathArray['category_ids'] = $id;
								
								$products_row[] = $pathArray;
								echo '<br>'.$pahtByName. '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'.$id;
							}
							$a = "";
						}
		
        	
			
		   $i++;
		   //echo $logStyle .$log.'</li>';
          // Mage::log($log, null, "category-feed-update.log");
		   ob_end_flush();
       // }
		
		
		//echo '<li style="background-color:#DDF;">update '.$count.' records</li><li style="background-color:#DDF;"> Finished profile execution. </li></ul>';
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