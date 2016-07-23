 <?php 
//ini_set('memory_limit','512M');
//set_time_limit(0);

require_once 'app/Mage.php';
Mage::app();

error_reporting(-1);

//$product = Mage::getModel('catalog/product')->loadByAttribute('sku',$_POST['id']);
$product = Mage::getModel("catalog/product")->getIdBySku($_POST['id']);
$productdel['productid']=$product;
$product = Mage::getModel("catalog/product")->load($product);
$productdel['minimumQty'] = $product->getStockItem()->getMinSaleQty(); 

Mage::getSingleton('core/session')->setCounter('350');


/*if($product->getAttributeText('availablity')!='')
{
 $availablity = $product->getAttributeText('availablity');
}*/
//return $minimumQty

//echo json_encode($productid);
echo json_encode($productdel);


?>