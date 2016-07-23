<?php
require_once 'config.php'; 
 
//$local_file = Mage::getBaseUrl()."order_fulfillment/csvfiles/inventory/import/stock_import.csv";
$local_file = Mage::getBaseDir()."/order_fulfillment/csvfiles/inventory/import/".$stock_import_file;

$handel = fopen($local_file,'w');

fclose($handel);

$ftp_server="download.bigstarusa.com";
$ftp_user_name="plumtree";
$ftp_user_pass="233ba232";
$server_file = "/INVENTORY/".$stock_import_file;

$conn_id = ftp_connect($ftp_server) or die("Couldn't connect to $ftp_server"); 
$login_result = ftp_login($conn_id, $ftp_user_name, $ftp_user_pass)  or die('Not able to login to FTP');
//ftp_pasv($conn_id, true);
  
if (ftp_get($conn_id,$local_file, $server_file,FTP_BINARY)) {
   echo "Successfully written to $local_file\n";
 } else {
    echo "There was a problem\n";
    }
  //close the connection
ftp_close($conn_id);



//$stock_import = $stock_import_dir.'/'.$stock_import_file;

//chmod($stock_import,0777);
//========================================================================

$product = Mage::getModel('catalog/product');

if (($fhandle = fopen($local_file, "r")) !== FALSE) {
	$row = 0;
	while (($data = fgetcsv($fhandle, 1000, ",")) !== FALSE) {
		if($row>0){
			$sku = $data[0];
			$qty = $data[1];
			if($sku!='' && $qty>=0 && $product->loadByAttribute('sku',$sku)){
					$product = $product->loadByAttribute('sku',$sku);
				if(is_object($product)){
					$stock =Mage::getModel('cataloginventory/stock_item')
							->loadByProduct($product->getId());
					$product_qty = $stock->getQty();
					if($product_qty!=$qty)
					{
						if(!$stock->getId()){
							$stockItem->setData('product_id', $product->getId());
							$stockItem->setData('stock_id', 1);
							$stock->setData('sku',$sku);
							$stock->setData('qty',$qty);
						}else{
							$stock_data = $stock->getData();
							foreach($stock_data as $key=>$value){
								$stock->setData($key,$value);
								$stock->setData('sku',$sku);
								$stock->setData('qty',$qty);
								if($qty>0)
								{
									$stock->setData('is_in_stock', 1);
								}
							}
						}
						$stock->save();
						echo 'Product ['.$sku.'] stock updated <br>';	
					}
					else
					{
						echo 'Product ['.$sku.'] stock already updated <br>';	
					}
					
				}else{
					echo 'Product '.$sku.' not exists <br>';
				}	
					
			}
			
		}	
		$row++;
	}
	fclose($fhandle);
	
	if(file_exists($stock_import) && !is_dir($stock_import)){
		unlink($stock_import);
	}
	$process = Mage::getModel('index/indexer')->getProcessByCode('cataloginventory_stock');
	$process->reindexAll();
	$process = Mage::getModel('index/indexer')->getProcessByCode('catalog_product_flat');
	$process->reindexAll();
}
else
 echo "cannot open file ".$stock_import;

