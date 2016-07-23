<?php 

/*$uploadedfile = 'test';
$ftp_server="download.bigstarusa.com";
$ftp_user_name="plumtree";
$ftp_user_pass="233ba232";
$file = $uploadedfile;
$remote_file = "/Order";

 // set up basic connection
$conn_id = ftp_connect($ftp_server) or die("Couldn't connect to $ftp_server"); 
$login_result = ftp_login($conn_id, $ftp_user_name, $ftp_user_pass)  or die('Not able to login to FTP');

 if (ftp_put($conn_id, $remote_file, $file, FTP_ASCII)) {
    echo "successfully uploaded $file\n";
    exit;
 } else {
    echo "There was a problem while uploading $file\n";
    exit;
    }
 // close the connection
 ftp_close($conn_id);*/
 
?>
 
<?php

$rootdir = dirname(__DIR__);

require_once $rootdir.'\app/Mage.php';


Mage::app();
$category = Mage::getModel('catalog/category');
$tree = $category->getTreeModel();
$tree->load();
$ids = $tree->getCollection()->getAllIds();   // we can get all level categories id
if($ids):

foreach ($ids as $id)
{
	$cat = Mage::getModel('catalog/category');
	$cat->load($id);
	if($id != 3): // if  category id is not "root catalog" id – here root catalog id is 3
		if($cat->getIsActive()): // if category is active
			$catName[] =  $cat->getName(); //  To get name of the category
			echo $cat->getName()." ".$cat->getLevel()."<br/>";
			$path[] = $cat->getUrl(); // to get url of category
		endif;
	endif;
}
endif;
?> 