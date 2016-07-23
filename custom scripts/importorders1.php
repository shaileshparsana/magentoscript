<?php

ini_set('memory_limit','512M');
set_time_limit(0);

require_once 'app/Mage.php';
Mage::app();

$filename = "order_export_20130624_105054.csv";
//$filename = "oomph_order_02.csv";
$data	  = ConvertCSV2Array($filename);

$orderIds  = array();
$items = array();
$created = 0;
$skipped = 0;

$orderObj = Mage::getModel('sales/order');


foreach($data as $i=>$row)
{

	$order = $orderObj->loadByIncrementId(trim($row['Order Number']));
	if($order){
		if($order->getEntityId()){
			echo $order->getEntityId().'--'.$row['Order Number'].' === exists<br>';
			continue;
		}
	}	


    if(!in_array($row['Order Number'],$orderIds))
    {
        array_push($orderIds,$row['Order Number']);
		
   
        $obj = new Varien_Object();

         $obj->setWebsiteCode($row['Site']);
        $obj->setOrderNo($row['Order Number']);
        $obj->setOrderDate($row['Order Date']);
        $obj->setOrderStatus($row['Order Status']);
       	$obj->setExPaymentMethod($row['Order Payment Method']);
		if($row['Order Shipping Method']=='Free Shipping - Free')
		$row['Order Shipping Method']='freeshipping_freeshipping';
      	$obj->setShippingMethod($row['Order Shipping Method']);

		$obj->setPaymentMethod('checkmo');
		//$obj->setPoNumber($row['PO Number']);
		//  $obj->setShippingMethod('');
		//$obj->setShippingMethod('flatrate_flatrate');
  
        $obj->setSubtotal($row['Order Subtotal']);
        $obj->setTax($row['Order Tax']);
		
		if(strstr($row['Order Discount'],'$'))
		{
			$row['Order Discount'] = substr($row['Order Discount'],1,strlen($row['Order Discount']));
		}
		if(strstr($row['Order Grand Total'],'$'))
		{
			$row['Order Grand Total'] = substr($row['Order Grand Total'],1,strlen($row['Order Grand Total']));
		}
		//echo '**'.$row['Order Discount'].'---';
        $obj->setShippingAmount($row['Order Shipping']);
			$obj->setDiscountAmount($row['Order Discount']);
        $obj->setGrandTotal($row['Order Grand Total']);
        $obj->setTotalPaid($row['Order Paid']);
        $obj->setTotalRefunded($row['Order Refunded']);
        $obj->setOrderDue($row['Order Due']);
        $obj->setTotalItemOrdered($row['Total Qty Items Ordered']);

        $obj->setCustomerName($row['Customer Name']);
        $obj->setCustomerEmail($row['Customer Email']);
        
        $shippingObject  = new Varien_Object();
		
		
      $ShippingName = explode(' ',$row['Shipping Name']);		
	    $shippingObject->setFirstname($ShippingName[0]);
        $shippingObject->setLastname($ShippingName[1]);
        $shippingObject->setCompany($row['Shipping Company']);
        $shippingObject->setStreet($row['Shipping Street']);
        $shippingObject->setPostcode($row['Shipping Zip']);
        $shippingObject->setCity($row['Shipping City']);
        $shippingObject->setRegion($row['Shipping State']);
        $shippingObject->setRegionId($row['Shipping State']);
        $shippingObject->setCountryId($row['Shipping Country']);
        $shippingObject->setTelephone($row['Shipping Phone Number']);

       	$billingObject  = new Varien_Object();
		$billingname = explode(' ',$row['Billing Name']);
        $billingObject->setFirstname($billingname[0]);
        $billingObject->setLastname($billingname[1]);
        $billingObject->setCompany($row['Billing Company']);
        $billingObject->setStreet($row['Billing Street']);
        $billingObject->setPostcode($row['Billing Zip']);
        $billingObject->setCity($row['Billing City']);
        $billingObject->setRegion($row['Billing State']);
        $billingObject->setRegionId($row['Billing State']);
        $billingObject->setCountryId($row['Billing Country']);
        $billingObject->setTelephone($row['Billing Phone Number']);
		$paymentobj = new Varien_Object();
		//$ccnum1=(float)$row['CCnum'];
		//printf("'%d",  $row['CCnum']); 
		$paymentobj->setCcNumber($row['CCnum']);
		$paymentobj->setCcOwner($row['CardHolder']);		
		$paymentobj->setCcType($row['CardType']);
		$paymentobj->setCcExpMonth($row['ExpMonth']);
		$paymentobj->setCcExpYear($row['Expyear']);
		$paymentobj->setProcessedAmount($row['Processing Amount']);
		
		
        
        //$billingObject->setComments($row['Comments']);

//         // get Customer , if customer not found create new customer
        $items[$i]['name']           = $row['Item Name'];
        $items[$i]['status']         = $row['Item Status'];
        $items[$i]['sku']            = $row['Item SKU'];
     $items[$i]['product_options']        = $row['config item options'];
        $items[$i]['original_price'] = $row['Item Original Price'];
        $items[$i]['price']          = $row['Item Price'];
        $items[$i]['qty_ordered']    = $row['Item Qty Ordered'];
        $itemQty    += $row['Item Qty Ordered'];
        // addding first Product to OrdersArray
        
    }else {
        $items[$i]['name']           = $row['Item Name'];
        $items[$i]['status']         = $row['Item Status'];

        $items[$i]['sku']            = $row['Item SKU'];
      $items[$i]['product_options']        = $row['config item options'];
        $items[$i]['original_price'] = $row['Item Original Price'];
        $items[$i]['price']          = $row['Item Price'];
        $items[$i]['qty_ordered']    = $row['Item Qty Ordered'];
        $itemQty    += $row['Item Qty Ordered'];
		//$skipped--;
    }
  
    // echo "<br/>".$row['Order Number']."===".$itemQty."===".$obj->getTotalItemOrdered();
	
	if(is_object($obj)){
		$totalQty = $obj->getData('total_item_ordered');
	}
	//$totalQty = $itemQty;
    if($itemQty == $totalQty)
    {
		$IsOrder = createOrder($obj,$items,$shippingObject,$billingObject,$paymentobj);
		if(isset($IsOrder) && $IsOrder==TRUE){
			echo "Order #".$obj->getOrderNo().' created ! <br/>';		
			$created++;
		}else{
			$skip[$row['Order Number']]	 = $row['Order Number'];
			$skipped++;
		}
		
        $itemQty = 0;
        unset($obj);
        unset($items);
    }
}
echo '<br>';
echo '===========================================';
echo '<br>';
echo "Total Orders : ".($created+$skipped).'<br><br>';
echo "Total Created : ".$created.'<br><br>';
echo "Total Skipped : ".$skipped.'<br>';
echo "<br>========= Skipped Orders =============<br>";
if(isset($skip) && is_array($skip)){
	foreach($skip as $sk){
		echo $sk.'<br>';
	}
}


function createOrder(Varien_Object $obj,$items,Varien_Object $shippingAddress,Varien_Object $billingAddress,Varien_Object $paymentobj)
{

		if(!is_object($obj)){
			return FALSE;
		}

    $quoteObj = Mage::getModel('sales/quote');

 		// add products to quote
		$p = 0;
        $productModel = Mage::getModel('catalog/product');
        foreach($items as $item) 
        {
            $productObj        = getProductFromSku($item['sku']);
            if($productObj)
            {
                $quoteItem = Mage::getModel('sales/quote_item')->setProduct($productObj);
                $quoteItem->setQuote($quoteObj);
				$quoteItem->setAdditionalData($item['product_options']);
                $quoteItem->setQty($item['qty_ordered']);
                $quoteObj->addItem($quoteItem);
            }else{
				$p++;
			}
        }
		

	if($p==0){
		//$code = $obj->getWebsiteCode();
		//$websiteData= Mage::getModel('core/website')->loadConfig($code);
		//$website    = $websiteData->getData('website_id');
		//$storeId    = $websiteData->getData('store_id');	
		$storeId = Mage::app()->getStore()->getId();
		$website = Mage::app()->getWebsite()->getId();
		

		$customer = Mage::getModel('customer/customer');
		
		$customer->setWebsiteId($website);
		$customer->loadByEmail($obj->getCustomerEmail());	
		
	if(!$customer->getId())
    {
        // create quote
        $quoteObj->setIsMultiShipping(false);
        $quoteObj->setCheckoutMethod('guest');
        $quoteObj->setCustomerId(null);
        $quoteObj->setCustomerEmail($obj->getCustomerEmail());
        $quoteObj->setCustomerIsGuest(true);
        $quoteObj->setCustomerGroupId(Mage_Customer_Model_Group::NOT_LOGGED_IN_ID);

        // set store
        $quoteObj->setStore(Mage::app()->getStore());
    } else {

        $customerObj = Mage::getModel('customer/customer')->load($customer->getId());
        $quoteObj->assignCustomer($customerObj);
        $storeObj = $quoteObj->getStore()->load($storeId);
        $quoteObj->setStore($storeObj);
    }
   
       
        
        // addresses
        $quoteShippingAddress = new Mage_Sales_Model_Quote_Address();
        $quoteShippingAddress->setData($shippingAddress->getData());

        $quoteBillingAddress = new Mage_Sales_Model_Quote_Address();
        $quoteBillingAddress->setData($billingAddress->getData());

        $quoteObj->setShippingAddress($quoteShippingAddress);
        $quoteObj->setBillingAddress($quoteBillingAddress);

        $quoteObj->getShippingAddress()->setShippingMethod($obj->getShippingMethod());
        $quoteObj->getShippingAddress()->setCollectShippingRates(true);
        $quoteObj->getShippingAddress()->collectShippingRates();
        $quoteObj->collectTotals();	// calls $address->collectTotals();
		
		
		
		if(strstr($obj->getData('grand_total'),'$'))
		{
			$temp = substr($obj->getData('grand_total'),1,strlen($obj->getData('grand_total')));
		}
	//	echo '<pre />';
	//	print_r($obj->getData());
		
		   $quoteObj->setBaseSubtotalWithDiscount($obj->getDiscountAmount());
		    $quoteObj->setSubtotal($temp );
        $quoteObj->save();
		
$quoteId=$quoteObj->getId();
	$orderId = createOrderFromQuote($quoteId,$obj->getPaymentMethod(),$paymentobj,$quoteObj);

		updateOrderStatus($orderId,$obj->getOrderStatus());
        UpdateNewIncrementId($orderId,$obj->getOrderNo(),$obj->getOrderDate(),$obj);
		
   		return TRUE;
	}else{
		return FALSE;
	}


   
		
}
function getProductFromSku($sku)
{
    $product = Mage::getModel('catalog/product');
    $id = $product->getIdBySku($sku);
    if($id) {
        $product->load($id);
        return $product;
    }
    return FALSE;
}


/**
 * Creates order in Magento for logged in customers
 * Converts Quote to order
 *
 * @param int $quoteId
 * @param string $paymentMethod authorizenet, paypal_express, purchaseorder...
 * @param stdClass $paymentData
 * @return int $orderId
 */
 function createOrderFromQuote($quoteId, $paymentMethod, $paymentData,$quotedata)
{
        $quoteObj = Mage::getModel('sales/quote')->load($quoteId); // Mage_Sales_Model_Quote
        $items = $quoteObj->getAllItems();
			$quoteObj->collectTotals();
        $quoteObj->reserveOrderId();

      // set payment method
        $quotePaymentObj = $quoteObj->getPayment(); // Mage_Sales_Model_Quote_Payment
        $quotePaymentObj->setMethod($paymentMethod);
       // $quotePaymentObj->setPoNumber($paymentData);
        $quoteObj->setPayment($quotePaymentObj);

        // convert quote to order
        $convertQuoteObj = Mage::getSingleton('sales/convert_quote');

        if($quoteObj->isVirtual() == 0) {
          $orderObj = $convertQuoteObj->addressToOrder($quoteObj->getShippingAddress());
        } else {
          $orderObj = $convertQuoteObj->addressToOrder($quoteObj->getBillingAddress());
        }

        $orderPaymentObj = $convertQuoteObj->paymentToOrderPayment($quotePaymentObj);

        // convert quote addresses
        $orderObj->setBillingAddress($convertQuoteObj->addressToOrderAddress($quoteObj->getBillingAddress()));
        if($quoteObj->isVirtual() == 0) {
          $orderObj->setShippingAddress($convertQuoteObj->addressToOrderAddress($quoteObj->getShippingAddress()));
        }
        // set payment options
        $orderObj->setPayment($convertQuoteObj->paymentToOrderPayment($quoteObj->getPayment()));
		
	
        if ($paymentData) {
		$orderObj->getPayment()->setCcOwner($paymentData->ccOwner);
        $orderObj->getPayment()->setCcNumber($paymentData->ccNumber);
        $orderObj->getPayment()->setCcType($paymentData->ccType);
        $orderObj->getPayment()->setCcExpMonth($paymentData->ccExpMonth);
        $orderObj->getPayment()->setCcExpYear($paymentData->ccExpYear);
	
		$orderObj->getPayment()->setProcessedAmount($paymentData->processedAmount);
        $orderObj->getPayment()->setCcLast4(substr($paymentData->ccNumber,-4));
        }
        // convert quote items
        foreach ($items as $item) {
                // @var $item Mage_Sales_Model_Quote_Item
                $orderItem = $convertQuoteObj->itemToOrderItem($item);
				$add_data=unserialize($item->getData('additional_data'));
				$orderItem->setProductOptions($add_data);
			
                if ($item->getParentItem()) {
                        $orderItem->setParentItem($orderObj->getItemByQuoteItemId($item->getParentItem()->getId()));
                }
                $orderObj->addItem($orderItem);
        }

        $orderObj->setCanShipPartiallyItem(false);
		

        try {
                $orderObj->place();
        } catch (Exception $e){
                Mage::log($e->getMessage());
                Mage::log($e->getTraceAsString());
        }
//echo 'sereererer';
//print_r($quotedata->getData());
//echo $quotedata->getBaseSubtotalWithDiscount();
$orderObj->setDiscountAmount($quotedata->getBaseSubtotalWithDiscount())	;
$orderObj->setSubtotal($quoteObj->getSubtotal());
$orderObj->setGrandTotal($quoteObj->getSubtotal());
$orderObj->setBaseGrandTotal($quoteObj->getSubtotal());	

        $orderObj->save();
        //$orderObj->sendNewOrderEmail();
        return $orderObj->getId();
        unset ($orderObj, $quoteObj);
}


function UpdateNewIncrementId($orderId,$newOrderNo,$createdDate,Varien_Object $obj){
		$order = Mage::getModel('sales/order')->load($orderId);
		$order->setData('increment_id',$newOrderNo);
		$order->setData('created_at',date('Y-m-d 12:00:00',  strtotime($createdDate)));
		//$order->setData('status','processing');
		if($obj->getExPaymentMethod()=='authorizenet'){
			$payment = $order->getPayment();
			$payment->setData('base_amount_authorized',$payment->getAmountPaid());
			$payment->setData('method','authorizenet');		
			$payment->setData('amount_authorized',$payment->getAmountPaid());
			$payment->save();
		}else{
			$payment = $order->getPayment();
			$payment->setData('method',$obj->getExPaymentMethod());		
			$payment->save();
		}
		

		
		
		$order->save();
		
}

function updateOrderStatus($orderId,$status){
	
		$order = Mage::getModel('sales/order')->load($orderId);
		$status = strtolower(trim($status));
		$incrementId = $order->getIncrementId();
		
		
		
		
		switch($status){
			case "pending":
				break;
			case "processing":
					if($order->canInvoice()){
						generateInvoice($incrementId);
					}
				break;
			case "complete":
					if($order->canInvoice()){
						generateInvoice($incrementId);
					}
					if($order->canShip()){
						generateShipment($incrementId,$order);
					}
				break;
			case "canceled":
				if($order->canCancel()){
					$order->cancel()->save();
				}else{
					$order->setData('status','canceled')->save();
				}
				break;
			case "closed":
					if($order->canInvoice()){
						generateInvoice($incrementId);
					}	
					$order->setData('base_total_paid',$order->getBaseGrandTotal());
					$order->save();
					createCreditmemo($order);
					$order->setData('status',$status)->save();
				break;		
			default:
				$order->setData('status',$status)->save();	
					
		}

		
		
}

function getOrderState($status){
$orderstate = array('completed'=>Mage_Sales_Model_Order::STATE_COMPLETE,
							'pending'=>Mage_Sales_Model_Order::STATE_NEW,
							'processing'=>Mage_Sales_Model_Order::STATE_PROCESSING,
							'closed'=>Mage_Sales_Model_Order::STATE_CLOSED,
							'canceled'=>Mage_Sales_Model_Order::STATE_CANCELED,
							'holded'=>Mage_Sales_Model_Order::STATE_HOLDED);
		return $orderstate[$status];					
}


function createCreditmemo($order) {
        //$orderObj = Mage::getModel('sales/order')->load($orderId);
		$orderObj = $order;
        $convertOrderObj = Mage::getModel('sales/convert_order');
        $creditmemoObj = $convertOrderObj->toCreditmemo($orderObj);

        foreach($orderObj->getAllItems() as $item) {
            $creditmemoItem = $convertOrderObj->itemToCreditmemoItem($item);
            $creditmemoObj->addItem($creditmemoItem);
            $creditmemoItem->setQty($item->getQtyToRefund());
            $creditmemoItem->calcRowTotal();
        }
     
        $creditmemoObj->collectTotals();
        $creditmemoObj->register();
        
        $creditmemoObj->save();
        $orderObj->save();
        
        return $creditmemoObj->getId();
    }

function generateInvoice($incrementId){
	
	$invoiceId = Mage::getModel('sales/order_invoice_api')
				->create($incrementId, array());
	$invoice = Mage::getModel('sales/order_invoice')
				->loadByIncrementId($invoiceId);
	$invoice->pay();

}
function generateShipment($incrementId,$order){
		$shipmentId = Mage::getModel('sales/order_shipment_api')
							->create($incrementId, array());
		$shipment = Mage::getModel('sales/order_shipment')
								->loadByIncrementId($shipmentId);
		$shipment->setOrder($order);
}


function ConvertCSV2Array($filename)
{
	$row = 0;
	$field_arr = array();
	$index = -1;
	$handle = fopen($filename, "r");
	while (($csv_data = fgetcsv($handle, 1000, ",")) !== FALSE)
	{
		   $num = count($csv_data);
	    for ($c=0; $c < $num; $c++)
	    {
	    	if($row==0)
	    	{
        	$field_arr[$c]	= $csv_data[$c];
	    	}
	    	else
	    	{
        	$data[$index][$field_arr[$c]] = $csv_data[$c];

	    	}

	    }
	    $index++;
	    $row++;

	}
	fclose($handle);
	return $data;

}


 


?>
