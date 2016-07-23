<?php
require_once 'abstract.php';

/**
 * Naim website level tax class update Shell Script
 *
 * @author     Naim
 */
class Plumtree_Shell_Deleteproductclass extends Mage_Shell_Abstract {

    /**
     * Run script
     *
     */
    public function run() {
		
		
		
        Mage::app()->setCurrentStore(Mage_Core_Model_App::ADMIN_STORE_ID);
	    		
        $file_handle = fopen("../vendor_feed/other_update/delete-products.csv", "r") or die("CSV file not found or not able to open it.");
		$key = array();
		$i = 1;
		$count =0;
		$fp = file('../vendor_feed/other_update/delete-products.csv', FILE_SKIP_EMPTY_LINES);
		echo '<style type="text/css">';
		echo 'ul { list-style-type:none; padding:0; margin:0; }';
		echo 'li { margin-left:0; border:1px solid #ccc; margin:2px; padding:2px 2px 2px 2px; font:normal 12px sans-serif; }';
		echo 'img { margin-right:5px; }';
		echo '</style>';
		echo '<ul ><li style="background-color:#DDF;">Starting profile execution, please wait... </li><li style="background-color:#DDF;">Warning: Please do not close the window during importing/exporting data </li><li style="background-color:#DDF;"> Found '.(count($fp)-1).' rows.</li>';
		$file_path ='chicago_delete_products.csv';
		$mage_csv = new Varien_File_Csv();
		$products_row = array();    
        while (!feof($file_handle)) {
			//flush();
            $line_of_text = fgetcsv($file_handle, 0);	
			if($i==1){
				$key=$line_of_text;
			}
			
			if($i!=1){ 
				if(!empty($line_of_text)){
					//print_r($line_of_text);
					$products =  Mage::getModel('catalog/product')->loadByAttribute('sku',$line_of_text[0]);
									
					if($products){
						$products_model = Mage::getModel('catalog/product');
						$product = $products_model->load($products->getId());	
						$data['category_ids'] = join(',', $product->getCategoryIds());
						$data['sku'] = $product->getSku();
						$data['name'] = $product->getName();
						$data['price'] = number_format((float)$product->getPrice(), 2, '.', '');
						$data['udropship_vendor'] = $product->getUdropshipVendor();
						$data['licensed_treatment'] = $product->getLicensedTreatment();
						$data['image'] = $product->getMediaConfig()->getMediaUrl($product->getData('image'));
						$data['product_url'] = $product->getProductUrl();	
						$data['description'] = $product->getDescription();
					
					
						//stock attributes
						$stockItem = $product->getStockItem();
						$data['qty'] = $stockItem->getData('qty');
						$data['is_in_stock'] = $stockItem->getData('is_in_stock');
						$data['min_qty'] = $stockItem->getData('min_qty');
						$data['use_config_min_qty'] = $stockItem->getData('use_config_min_qty');
						$data['is_qty_decimal'] = $stockItem->getData('is_qty_decimal');
						$data['backorders'] = $stockItem->getData('backorders');
						$data['use_config_backorders'] = $stockItem->getData('use_config_backorders');
						$data['min_sale_qty'] = $stockItem->getData('min_sale_qty');
						$data['use_config_min_sale_qty'] = $stockItem->getData('use_config_min_sale_qty');
						$data['max_sale_qty'] = $stockItem->getData('max_sale_qty');
						$data['use_config_max_sale_qty'] = $stockItem->getData('use_config_max_sale_qty');
						
						$data['low_stock_date'] = $stockItem->getData('low_stock_date');
						$data['notify_stock_qty'] = $stockItem->getData('notify_stock_qty');
						$data['use_config_notify_stock_qty'] = $stockItem->getData('use_config_notify_stock_qty');
						$data['manage_stock'] = $stockItem->getData('manage_stock');
						$data['use_config_manage_stock'] = $stockItem->getData('use_config_manage_stock');
						$data['stock_status_changed_auto'] = $stockItem->getData('stock_status_changed_auto');
						$data['use_config_qty_increments'] = $stockItem->getData('use_config_qty_increments');
						$data['qty_increments'] = $stockItem->getData('qty_increments');
						$data['use_config_enable_qty_inc'] = $stockItem->getData('use_config_enable_qty_inc');
						$data['enable_qty_increments'] = $stockItem->getData('enable_qty_increments');
						$data['is_decimal_divided'] = $stockItem->getData('is_decimal_divided');
						$data['stock_status_changed_automatically'] = $stockItem->getData('stock_status_changed_automatically');
						$data['use_config_enable_qty_increments'] = $stockItem->getData('use_config_enable_qty_increments');
						$data['size'] = $product->getSize();
						$data['color'] = $product->getColor();
					
				//	
					
					$products_row[] = $data;
					$product->delete();
					$product->clearInstance();
					$products->clearInstance();
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
            Mage::log($log, null, "vendor-feed-delete.log");
			//ob_end_flush();
        }
		$headrs = array("category_ids","sku","name","price",'udropship_vendor','licensed_treatment','image','product_url','description',"qty","is_in_stock","min_qty",'use_config_min_qty','is_qty_decimal','backorders','use_config_backorders','min_sale_qty',"use_config_min_sale_qty","max_sale_qty","use_config_max_sale_qty",'low_stock_date','notify_stock_qty','use_config_notify_stock_qty','manage_stock','use_config_manage_stock',"stock_status_changed_auto","use_config_qty_increments","qty_increments",'use_config_enable_qty_inc','enable_qty_increments','is_decimal_divided','stock_status_changed_automatically','use_config_enable_qty_increments','size','color');
		
		array_unshift($products_row,$headrs);
		$mage_csv->saveData($file_path,$products_row);
		
		echo "Successfully $products exported at:-$file_path";
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

$shell = new Plumtree_Shell_Deleteproductclass();
$shell->run();