<?php 
require_once 'config.php';

require_once 'Abstract.php';

   function _getTmpDir()
    {
        return Mage::getBaseDir('var') . DS . 'export' . DS . 'ksa' . DS;
    }



	function order_success_page_view()
    {
        // get order id
		$orderId = $_REQUEST['order'];
		$order = Mage::getModel('sales/order')->load($orderId);
		//print_r($order); exit;
		$store = $order->getStore();
        $name = array($store->getWebsite()->getName(),$store->getGroup()->getName(),$store->getName());
        $storename=implode("\n", $name);

		if($store->getWebsite()->getName() != ''){
	 
			function hex2bin($str) {
				$bin = "";
				$i = 0;
				do {
					$bin .= chr(hexdec($str{$i}.$str{($i + 1)}));
					$i += 2;
				} while ($i < strlen($str));
				return $bin;
			}

			$RecordTerminatior=hex2bin(bin2hex(chr(ord("\t"))));
			$FileTerminatior=hex2bin(bin2hex(chr(ord("\r")))); 
			$FileTerminatior.=hex2bin(bin2hex(chr(ord("\n")))); 
			$orderNo = $order->getIncrementId();
			$customerid=$order->getCustomerId();
			
			$DelimiterChar=$RecordTerminatior;


			$orderNo = $order->getIncrementId();
			$orderDate = date('Y-m-d',strtotime($order->getCreatedAtFormated('m/d/Y')));


			$FilePrefix='KSA';

			// format 20090403_141048_order_100000007_7.xml
			$filename = 'IN_'.$FilePrefix.'_O_' . $orderNo.'.txt';

			// create content
			$commoncontent_1= $orderNo.$DelimiterChar; // '1 - Sales Order Number (array field 0)     varchar(15)     * required
			$commoncontent_1.= $orderDate.$DelimiterChar; //'2 - Date               (array field 1)     datetime

			$commoncontent_1.= $order->getData('customer_id').$DelimiterChar; //'3 - Customer Code      (array field 2)     varchar(20)     * required
			$commoncontent_1.=$order->getBillingAddress()->getFirstname()." ".$order->getBillingAddress()->getLastname().$DelimiterChar; //'4 - Bill to Cust Name  (array field 3)     varchar(100)    * required
			$commoncontent_1.= $order->getRealOrderId().$DelimiterChar; //'5 - Cust PO Number     (array field 4)     varchar(20)
			$commoncontent_1.= $order->getBillingAddress()->getStreet1().$DelimiterChar; //'6 - Bill to Addr 1     (array field 5)     varchar(100)
			$commoncontent_1.= $order->getBillingAddress()->getStreet2().$DelimiterChar; //'7 - Bill to Addr 2     (array field 6)     varchar(100)
			$commoncontent_1.= $order->getBillingAddress()->getCity().$DelimiterChar; //'8 - Bill to City       (array field 7)     varchar(35)
			$commoncontent_1.= $order->getBillingAddress()->getRegionCode().$DelimiterChar; //'9 - Bill to State      (array field 8)     varchar(2)
			$commoncontent_1.= $order->getBillingAddress()->getPostcode().$DelimiterChar; //'10 - Bill to Zip Code  (array field 9)     varchar(10)
			$commoncontent_1.= $order->getShippingAddress()->getFirstname()." ".$order->getShippingAddress()->getLastname().$DelimiterChar; //'11 - Ship to Cust Name (array field 10)    varchar(100)
			$commoncontent_1.= $order->getShippingAddress()->getStreet1().$DelimiterChar; //'12 - Ship to Addr 1    (array field 11)    varchar(100)
			$commoncontent_1.= $order->getShippingAddress()->getStreet2().$DelimiterChar; //'13 - Ship to Addr 2    (array field 12)    varchar(100)
			$commoncontent_1.= $order->getShippingAddress()->getCity().$DelimiterChar; //'14 - Ship to City      (array field 13)    varchar(35)
			$commoncontent_1.= $order->getShippingAddress()->getRegionCode().$DelimiterChar; //'15 - Ship to State     (array field 14)    varchar(2)
			$commoncontent_1.= $order->getShippingAddress()->getPostcode().$DelimiterChar; //'16 - Ship to Zip Code  (array field 15)    varchar(10)
			
		
			$commoncontent_2= $order->getBillingAddress()->getCountry_id().$DelimiterChar; //'30 - Bill to Country   (array field 29)    varchar(35)
			$commoncontent_2.= $order->getShippingAddress()->getCountry_id().$DelimiterChar; //'31 - Ship to Country   (array field 30)    varchar(35)
			$commoncontent_2.= "".$DelimiterChar; //'32 - MSRP              (array field 31)    real
			$commoncontent_2.= "".$DelimiterChar; //'33 - Priority Code     (array field 32)    tinyint
			$commoncontent_2.= "".$DelimiterChar; //'34 - Department        (array field 33)    varchar(15)
			$commoncontent_2.= "".$DelimiterChar; //'35 - Class             (array field 34)    varchar(15)
			$commoncontent_2.= "".$DelimiterChar; //'36 - Notes (array field 35) char(60)
			$commoncontent_2.= "".$DelimiterChar; //'37 - Event Code (array field 36) char(20)
			$commoncontent_2.= "".$DelimiterChar; //'39 - Vendor Item SKU (array field 38) char(20) 
			$commoncontent_2.= "".$DelimiterChar; //'40 - Vendor Item Des (array field 39) char(25)
			$commoncontent_2.= "".$DelimiterChar; //'41 - Vendor Item Color (array field 40) char (60)
			$commoncontent_2.= "".$DelimiterChar; //'42 - Vendor Item Size (array field 41) char(5)
			$commoncontent_2.= "".$DelimiterChar; //'43 - Master assortment sku # (array field 42) char(20)
			$commoncontent_2.= "".$DelimiterChar; //'44 - store # (array field 43) char(5)
			$commoncontent_2.= "".$DelimiterChar; //'45 - ship to DC# (array field 44) char(5)
			$commoncontent_2.= "".$FileTerminatior; //'46 - store ssc/bill to # (array field 45) char(5)

			$items = $order->getAllItems();
			$itemcount=count($items);
			$kCount=0;
			$content='';


			foreach ($items as $itemId => $item){
				
				if($item->getParentItem()){
				}else{
					$content.=$commoncontent_1;
					$content.=$item->getSku().$DelimiterChar; //'17 - Part Number       (array field 16)    varchar(20)     * required
					$content.=$item->getName().$DelimiterChar;  //'18 - Part Description  (array field 17)    varchar(100)
					$content.='EA'.$DelimiterChar; //'19 - Unit of Issue     (array field 18)    varchar(50)     * required (generally "EA")
					$content.=($item->getQtyOrdered() * 1).$DelimiterChar;//'20 - Order Qty         (array field 19)    real
					$content.=$order->getShippingDescription().$DelimiterChar; //'21 - Ship Via Code     (array field 20)    varchar(100)
					$content.=++$kCount.$DelimiterChar;//'22 - Line Number       (array field 21)    varchar(10)     * must be numeric
					$content.=$order->getShippingAddress()->getFirstname()." ".$order->getShippingAddress()->getLastname().$DelimiterChar;//'23 - Attn              (array field 22)    varchar(255)
					$content.= $order->getCustomerEmail().$DelimiterChar; //'24 - Email             (array field 23)    varchar(100)
					$content.= $order->getBillingAddress()->getTelephone().$DelimiterChar; //'25 - Phone             (array field 24)    varchar(35)
					$content.= number_format($item->getTaxPercent(),2).$DelimiterChar; //'26 - Tax %             (array field 25)    real
					$content.= number_format($item->getBasePrice(),2).$DelimiterChar; //'27 - Unit Price        (array field 26)    real
					$content.= "".$DelimiterChar; //'28 - Style Code        (array field 27)    varchar(100)
					$content.= ''.$DelimiterChar; //'29 - Shipping Charges  (array field 28)    real
					$content.=$commoncontent_2;
				}
			}


			
		 	if($store->getWebsite()->getName() != ""){
				$path = _getTmpDir();

				if (isset($path) && !is_dir($path)) {
					mkdir($path);
					chmod($path,777);
				}

				// write file to server
				file_put_contents($path . $filename, $content);	
			}
				
			
			$to = 'orders@planetaccesscompany.com';
			//$to = 'naim@plumtreegroup.net';
			$subject = 'New order from wearpact.com';
	
			$message = strip_tags('Please find attached new order file with this email.');
			$attachment = chunk_split(base64_encode(file_get_contents($path . $filename)));
			$filename = $filename;
	
			$boundary =md5(date('r', time())); 
	
			//$headers = "From: orders@wearPACT.com\r\nReply-To: orders@wearPACT.com";
			//$headers .= "\r\nMIME-Version: 1.0\r\nContent-Type: multipart/mixed; boundary=\"_1_$boundary\"";
			
			$headers = "From: orders@wearpact.com\r\n";
			$headers .= "BCC: naim@plumtreegroup.net\r\n";
			$headers .= "Reply-To: orders@wearpact.com\r\n";
			$headers .= "MIME-Version: 1.0\r\n";
			$headers .= "Content-Type: multipart/mixed; boundary=\"".$boundary."\"\r\n\r\n";
			$headers .= "This is a multi-part message in MIME format.\r\n";
			$headers .= "--".$boundary."\r\n";
			$headers .= "Content-type:text/html; charset=iso-8859-1\r\n";
			$headers .= "Content-Transfer-Encoding: 7bit\r\n\r\n";
			$headers .= $message."\r\n\r\n";
			$headers .= "--".$boundary."\r\n";
			$headers .= "Content-Type: application/octet-stream; name=\"".basename($filename)."\"\r\n";
			$headers .= "Content-Transfer-Encoding: base64\r\n";
			$headers .= "Content-Disposition: attachment; filename=\"".basename($filename)."\"\r\n\r\n";
			$headers .= $attachment."\r\n\r\n";
			$headers .= "--".$boundary."--";
			
			
			if(mail($to, $subject, $message, $headers))
			{
			  echo "Order email with data send to warehouse.";	
			}
			else
			{
			  echo "Not able to send order data to warehouse.";	
			}

		}
    }
order_success_page_view();
?>