<?php 
require_once 'app/Mage.php';
umask(0);
Mage::app('default');

header("Content-type:text/octect-stream");
header("Content-Disposition:attachment;filename=data.csv");

$tableName = Mage::getSingleton('core/resource')
 ->getTableName('catalog_product_entity_tier_price');
$storeId    = Mage::app()->getStore()->getId();
$product    = Mage::getModel('catalog/product');
$products   = $product->getCollection()->addStoreFilter(2)->getAllIds();

$fieldname = array("sku","tier_price_website","tier_price_customer_group","tier_price_qty","tier_price_price");
print stripslashes(implode(',',$fieldname)) . "\n";
foreach($products as $productid)
{
$existingTierPrice = $product ->load($productid)->tier_price;
$sku = $product->getSku();
$name = $product->getName();
$cnt=0;
foreach($existingTierPrice as $key=>$value)
{
if($cnt==0)
{
$tierarray = array($sku,"all","all",$value['price_qty'],$value['price']);
print stripslashes(implode(',',$tierarray)) . "\n";
}
else
{
$tierarray = array("","all","all",$value['price_qty'],$value['price']);
print stripslashes(implode(',',$tierarray)) . "\n";
}
$cnt++;


}
}
?>