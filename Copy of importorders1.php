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
      	$obj->setShippingMethod($row['Order Shipping Method']);

		$obj->setPaymentMethod($row['Order Payment Method']);
		//$obj->setPoNumber($row['PO Number']);
		//  $obj->setShippingMethod('');
		//$obj->setShippingMethod('flatrate_flatrate');
  
       
		if(strstr($row['Order Grand Total'],'$'))
		{
			$row['Order Grand Total'] = substr($row['Order Grand Total'],1,strlen($row['Order Grand Total']));
		}
		
		if(strstr($row['Order Discount'],'$'))
		{
			$row['Order Discount'] = substr($row['Order Discount'],1,strlen($row['Order Discount']));
		}
		if(strstr($row['Order Shipping'],'$'))
		{
			$row['Order Shipping'] = substr($row['Order Shipping'],1,strlen($row['Order Shipping']));
		}
		if(strstr($row['Order Due'],'$'))
		{
			$row['Order Due'] = substr($row['Order Due'],1,strlen($row['Order Due']));
		}
			if(strstr($row['Order Subtotal'],'$'))
		{
			$row['Order Subtotal'] = substr($row['Order Subtotal'],1,strlen($row['Order Subtotal']));
		}
			if(strstr($row['Order Paid'],'$'))
		{
			$row['Order Paid'] = substr($row['Order Paid'],1,strlen($row['Order Paid']));
		}
			if(strstr($row['Order Paid'],'$'))
		{
			$row['Order Paid'] = substr($row['Order Paid'],1,strlen($row['Order Paid']));
		}
			if(strstr($row['Order Tax'],'$'))
		{
			$row['Order Tax'] = substr($row['Order Tax'],1,strlen($row['Order Tax']));
		}
			if(strstr($row['Item Original Price'],'$'))
		{
			$row['Item Original Price'] = substr($row['Item Original Price'],1,strlen($row['Item Original Price']));
		}
			if(strstr($row['Item Price'],'$'))
		{
			$row['Item Price'] = substr($row['Item Price'],1,strlen($row['Item Price']));
		}
			if(strstr($row['Item Tax'],'$'))
		{
			$row['Item Tax'] = substr($row['Item Tax'],1,strlen($row['Item Tax']));
		}
			if(strstr($row['Item Discount'],'$'))
		{
			$row['Item Discount'] = substr($row['Item Discount'],1,strlen($row['Item Discount']));
		}
			if(strstr($row['Item Total'],'$'))
		{
			$row['Item Total'] = substr($row['Item Total'],1,strlen($row['Item Total']));
		}
		
		
		$ProductPrice = ($row['Item Total'] + $row['Item Discount']) - ($row['ShippingPrice'] + $row['Item Tax']);
		$orderSubTotal = $ProductPrice -  $row['Item Discount'];
		 $obj->setSubtotal($row['Order Subtotal']);
        $obj->setTax($row['Order Tax']);
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
		$shippingObject->setName($row['Shipping Name']);
		$billingname = explode(' ',$row['Billing Name']);		
		$billingObject  = new Varien_Object();
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
        $billingObject->setName($row['Billing Name']);
        //$billingObject->setComments($row['Comments']);

//         // get Customer , if customer not found create new customer
        $items[$i]['name']           = $row['Item Name'];
        $items[$i]['status']         = $row['Item Status'];
        $items[$i]['sku']            = $row['Item SKU'];
        $items[$i]['options']        = $row['Item Options'];
		echo  $ProductPrice;
		echo '**//*/';
      $items[$i]['original_price'] = $ProductPrice;//str_replace('-','',str_replace(' ','',$itemPrice));
        $items[$i]['price']          = $ProductPrice;
        $items[$i]['qty_ordered']    = $row['Item Qty Ordered'];
        $itemQty    += $row['Item Qty Ordered'];
        // addding first Product to OrdersArray
        
    }else {
        $items[$i]['name']           = $row['Item Name'];
        $items[$i]['status']         = $row['Item Status'];
        $items[$i]['sku']            = $row['Item SKU'];
        $items[$i]['options']        = $row['Item Options'];
       $items[$i]['original_price'] = $ProductPrice;//str_replace('-','',str_replace(' ','',$itemPrice));
	   		echo  $ProductPrice;
		echo '**//*///';

        $items[$i]['price']          = $ProductPrice;
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
		$IsOrder = createOrder($obj,$items,$shippingObject,$billingObject);
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


function createOrder(Varien_Object $obj,$items,Varien_Object $shippingAddress,Varien_Object $billingAddress)
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
			$price = $obj->getMP();
			if(!is_object($productObj)){
				return FALSE;
			}
            if($productObj)
            {
                $quoteItem = Mage::getModel('sales/quote_item')->setProduct($productObj);
                $quoteItem->setQuote($quoteObj);
                $quoteItem->setQty($item['qty_ordered']);
				$quoteItem->setPrice($price);
				$quoteItem->setBasePrice($price);
				
				$quoteItem->setOriginalPrice($price);
				$quoteItem->setBaseOriginalPrice($price);
				
				$quoteItem->setDiscountAmount($obj->getDiscountAmount());
				$quoteItem->setBaseDiscountAmount($obj->getDiscountAmount());
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
        $quoteObj->save();
		

	$orderId = createOrderFromQuote($quoteObj->getEntityId(),$obj->getPaymentMethod(),'',$obj);

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
 function createOrderFromQuote($quoteId, $paymentMethod, $paymentData,$obj)
{

        $quoteObj = Mage::getModel('sales/quote')->load($quoteId); // Mage_Sales_Model_Quote
        $items = $quoteObj->getAllItems();
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
		
		$orderObj->setBaseShippingAmount($obj->getShippingAmount());
		$orderObj->setTaxAmount($obj->getTax());
		$orderObj->setBaseTaxAmount($obj->getTax());
		//$orderObj->setDiscount($obj->getDiscountAmount());
		$orderObj->setBaseDiscount($obj->getDiscountAmount());
		$orderObj->setGrandTotal($obj->getGrandTotal());
		$orderObj->setBaseGrandTotal($obj->getGrandTotal());
		$orderObj->setTotalPaid($obj->getGrandTotal());
		$orderObj->setSubtotal($obj->getSubtotal());
		$orderObj->setBaseSubtotal($obj->getSubtotal());
        $orderPaymentObj = $convertQuoteObj->paymentToOrderPayment($quotePaymentObj);

        // convert quote addresses
        $orderObj->setBillingAddress($convertQuoteObj->addressToOrderAddress($quoteObj->getBillingAddress()));
        if($quoteObj->isVirtual() == 0) {
          $orderObj->setShippingAddress($convertQuoteObj->addressToOrderAddress($quoteObj->getShippingAddress()));
        }
        // set payment options
        $orderObj->setPayment($convertQuoteObj->paymentToOrderPayment($quoteObj->getPayment()));
		
	
        if ($paymentData) {
        $orderObj->getPayment()->setCcNumber($paymentData->ccNumber);
        $orderObj->getPayment()->setCcType($paymentData->ccType);
        $orderObj->getPayment()->setCcExpMonth($paymentData->ccExpMonth);
        $orderObj->getPayment()->setCcExpYear($paymentData->ccExpYear);
        $orderObj->getPayment()->setCcLast4(substr($paymentData->ccNumber,-4));
        }
        // convert quote items
        foreach ($items as $item) {
                // @var $item Mage_Sales_Model_Quote_Item
                $orderItem = $convertQuoteObj->itemToOrderItem($item);
                if ($item->getParentItem()) {
                        $orderItem->setParentItem($orderObj->getItemByQuoteItemId($item->getParentItem()->getId()));
                }
				$price = $obj->getMP();
				$orderItem->setPrice($price);
				$orderItem->setBasePrice($price);
				$orderItem->setQty($item->getQty());
				
				$orderItem->setOriginalPrice($price);
				$orderItem->setBaseOriginalPrice($price);
				
				$orderItem->setDiscountAmount($obj->getDiscountAmount());
				$orderItem->setBaseDiscountAmount($obj->getDiscountAmount());
				
				$orderItem->setRowTotal($price);
        		$orderItem->setSubTotal($price);
                $orderObj->addItem($orderItem);
        }

        $orderObj->setCanShipPartiallyItem(false);
		

        try {
                $orderObj->place();
        } catch (Exception $e){
                Mage::log($e->getMessage());
                Mage::log($e->getTraceAsString());
        }

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
		
	$orderOBJ = Mage::getModel('sales/order')->load($orderId);
	  	$billingId = $orderOBJ->getBillingAddress()->getId();
        $billingaddress = Mage::getModel('sales/order_address')->load($billingId);
		$shippingId = $orderOBJ->getShippingAddress()->getId();
        $shippingaddress = Mage::getModel('sales/order_address')->load($shippingId);  
		$rate = $orderOBJ->getShippingAddress()->getShippingRatesCollection();
		$ship = $orderOBJ->getShippingAddress();
		$tax = $orderOBJ->getTaxAmount();
		$_payment = $orderOBJ->getPayment();
		
		$items = $orderOBJ->getAllItems();
		$discount = $orderOBJ->formatPrice($orderOBJ->getDiscount());
		
	
		
		$table="<table>";
		$table.="<tr><td colspan=7> <b>Order Number:&nbsp; #";
		$table.="<u><a target='_blank' href='".Mage::helper("adminhtml")->getUrl("adminhtml/sales_order/view/",array("order_id"=>$orderOBJ->getId()))."'>".$orderOBJ->getRealOrderId()."</a></u></b></td></tr>";
		$table.='<tr><td colspan=7>&nbsp;</td></tr>';
		$table.='<tr><td><b>Billing Address</b></td><td>&nbsp;</td><td><b>Shipping Address</b></td><td>&nbsp;</td><td><b>Shipping Method</b></td><td>&nbsp;</td>
		<td><b>Payment Method</b></td></tr>';
		$table.='<tr><td>'.$billingaddress->getName().'<br/>'.$billingaddress->getData("street").'<br/>'.$billingaddress->getData("city").
		$billingaddress->getData("postcode").'<br/>'.$billingaddress->getCountry().'</td><td>&nbsp;</td>';
		$table.='<td>'.$shippingaddress->getName().'<br/>'.$shippingaddress->getData("street").'<br/>'.$shippingaddress->getData("city").
		$shippingaddress->getData("postcode").'<br/>'.$shippingaddress->getCountry().'</td><td>&nbsp;</td>';
		$table.='<td>'.$orderOBJ->getShippingDescription().'</td><td>&nbsp;</td>';
		$table.='<td>'.$_payment->getMethod().'</td></tr>';
		$table.='<tr><td colspan=7>&nbsp;</td></tr>';
		$table.='<tr><td colspan=7>';
		$table1.='<table border="1" bordercolor="#000000" style="width:100%">';
		$table1.='<tr bgcolor="#000000" style="color:#fff; font-weight:bold;"><td colspan="3">Product Name</td><td>Price</td><td>Quantity</td><td colspan="2">Sub Total</td></tr>';
		$dis=0;
		foreach ($items as $item)
		{
			
			$table1.='<tr><td colspan="3">'.$item->getName().'</td><td>'.$orderOBJ->formatPrice($item->getPrice()*1).'</td>
			<td>'.($item->getQtyOrdered()*1).'</td><td colspan="2">'.$orderOBJ->formatPrice($item->getRowTotal()*1).'</td></tr>';
			$dis+=$item->getDiscountAmount();
		}
		$table1.='<tr><td colspan="7" style="text-align:right">Discount&nbsp;'.$orderOBJ->formatPrice($dis).'</td></tr>';
		$table1.='<tr><td colspan="7" style="text-align:right">Tax&nbsp;'.$orderOBJ->formatPrice($tax).'</td></tr>';
		
		$table1.='<tr><td colspan="7" style="text-align:right">Order Sub Total&nbsp;'.$orderOBJ->formatPrice($orderOBJ->getSubtotal()).'</td></tr>';
		$table1.='<tr><td colspan="7" style="text-align:right">Shiping Cost &nbsp;'.$orderOBJ->formatPrice($orderOBJ->getShippingAmount()).'</td></tr>';
		$table1.='<tr><td colspan="7" style="text-align:right">Net Total &nbsp;'.$orderOBJ->formatPrice($orderOBJ->getGrandTotal()).'</td></tr>';
		
		
		$table1.='</table>';
		$table.=$table1.'</td></tr>';
	       $table.='</table>';
		   
		//   echo $table;
		 //  exit;

 	   
//	   	/**
// 		* Initialization.
// 		*/

		//kyConfig::set(new kyConfig("http://www.shoplunada.com/kayako/api/index.php?", "65dc5cab-4ccb-9d44-250a-c45bbb9cc01a", "OTMwYWU4MWUtNTM1Ny01Y2Q0LWY5ZjYtOTI2NTU1MmU0ZjljY2ZhOWNlMTYtNWQ3Ni0xNjI0LTk5NjMtNmJlMWIwMzkyNGU4"));
//		kyConfig::get()->setDebugEnabled(false);
////		
////		/**
////		 * Optional. Setting defaults for new tickets.
////		 * WARNING:
////		 * Names may be different in your instalation.
////		 */
////		
//		$default_status_id = kyTicketStatus::getAll()->filterByTitle("Closed")->first()->getId();
//		$default_priority_id = kyTicketPriority::getAll()->filterByTitle("Normal")->first()->getId();
//		$default_type_id = kyTicketType::getAll()->filterByTitle("Order")->first()->getId();
//		kyTicket::setDefaults($default_status_id, $default_priority_id, $default_type_id);
// 	 
//		$general_department = kyDepartment::getAll()
//			->filterByTitle("Orders")
//			->filterByModule(kyDepartment::MODULE_TICKETS)
//			->first();
//
//		$user = kyUser::getAll()->filterByEmail($orderOBJ->getCustomerEmail())->first();
//		if($user)
//		 {
//			$ticket= kyTicket::createNew($general_department, $user, $table, $order->getRealOrderId())->create();
//		 }
//		 else 
//		 {	
//		 	if($billingaddress->getTelephone() != "" )	
//			{
//			 $user = kyUser::getAll()->filterByPhone($billingaddress->getTelephone())->first();	
//				if($user)
//				 {
//					$ticket= kyTicket::createNew($general_department, $user, $table, $order->getRealOrderId())->create();
//				 }
//				 else
//				 {
//				 $ticket= kyTicket::createNewAuto($general_department,$billingaddress->getName(),$billingaddress->getEmail(), $table, $order->getRealOrderId())->create();
//				}
//			}	
//			else
//				 {
//				 $ticket= kyTicket::createNewAuto($general_department,$billingaddress->getName(),$billingaddress->getEmail(), $table, $order->getRealOrderId())->create();
//				}		
//		 }	
// 		
		
		
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
