<?php
set_time_limit(0);

require_once 'Product_methods.php'; 

$app = Mage::app('default');

/* server read file
$local_file = Mage::getBaseDir()."/order_fulfillment/csvfiles/product_import/product.csv";

$handel = fopen($local_file,'w');

fclose($handel);

$ftp_server="download.bigstarusa.com";
$ftp_user_name="plumtree";
$ftp_user_pass="233ba232";
$server_file = "/CATALOGUE/product.csv";

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
*/


$row = 0;
$target_file  = Mage::getBaseDir().'/order_fulfillment/csvfiles/product_import/product.csv';

 if (($handle = fopen($target_file, "r")) !== FALSE) 
{
	while (($data = fgetcsv($handle, 10000, ",")) !== FALSE)
	{
	
	   if($data[0] == 'sku')	;
	   
	   $attributecode = 'color';
	   $colorvalue = trim($data[5]);
	   setOrAddOptionAttribute($attributecode,$colorvalue);

	   
	   $attributecode1 = 'size_waist';
	   $waistvalue = trim($data[46]);
	   setOrAddOptionAttribute($attributecode1,$waistvalue);
	   $row++;
	}
	fclose($handle);
		
}  

$product = Mage::getModel('catalog/product')->getCollection();
$row1 = 0;

$target_file  = Mage::getBaseDir().'/order_fulfillment/csvfiles/product_import/product.csv';


if (($handle = fopen($target_file, "r")) !== FALSE) 
{
	while (($data = fgetcsv($handle, 10000, ",")) !== FALSE)
	{
	   if($data[3] == 'simple')
	   {
			$row1++;
			if($row1 == 0) continue;
			addSimpleProudct($data);
	   } 
	   if($data[3] == 'configurable')
	   {
			$row1++;
			if($row1 == 0) continue;
			addConfigurableProudct($data);
	   }	
	 }
	   
	fclose($handle);		
}


?>
