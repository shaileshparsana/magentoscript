<?php

abstract class Orderexport_Abstract extends Mage_Core_Model_Abstract
{
    /**
     * Definition of abstract method to export orders to a file in a specific format in var/export.
     *
     * @param $orders List of orders of type Mage_Sales_Model_Order or order ids to export.
     * @return String The name of the written file in var/export
     */
    abstract public function exportOrders($orders,$dir,$orderIncrementId);

    /**
     * Returns the name of the website, store and store view the order was placed in.
     *
     * @param Mage_Sales_Model_Order $order The order to return info from
     * @return String The name of the website, store and store view the order was placed in
     */
	
    protected function getStoreName($order) 
    {
        $storeId = $order->getStoreId();
        if (is_null($storeId)) {
            return $this->getOrder()->getStoreName();
        }
        $store = Mage::app()->getStore($storeId);
        $name = array(
        $store->getWebsite()->getName(),
        $store->getGroup()->getName(),
        $store->getName()
        );
        return implode(', ', $name);
    }

    /**
     * Returns the payment method of the given order.
     *
     * @param Mage_Sales_Model_Order $order The order to return info from
     * @return String The name of the payment method
     */
    protected function getPaymentMethod($order)
    {
        return $order->getPayment()->getMethod();
    }
    
    /**
     * Returns the shipping method of the given order.
     *
     * @param Mage_Sales_Model_Order $order The order to return info from
     * @return String The name of the shipping method
     */
    protected function getShippingMethod($order)
    {
        if (!$order->getIsVirtual() && $order->getShippingMethod()) {
            return $order->getShippingMethod();
        }
        return '';
    }
    
    /**
     * Returns the total quantity of ordered items of the given order.
     *
     * @param Mage_Sales_Model_Order $order The order to return info from
     * @return int The total quantity of ordered items
     */
    protected function getTotalQtyItemsOrdered($order) {
        $qty = 0;
        $orderedItems = $order->getItemsCollection();
        foreach ($orderedItems as $item)
        {
            if (!$item->isDummy()) {
                $qty += (int)$item->getQtyOrdered();
            }
        }
        return $qty;
    }

    /**
     * Returns the sku of the given item dependant on the product type.
     *
     * @param Mage_Sales_Model_Order_Item $item The item to return info from
     * @return String The sku
     */
    protected function getItemSku($item)
    {
        if ($item->getProductType() == Mage_Catalog_Model_Product_Type::TYPE_CONFIGURABLE) {
            return $item->getProductOptionByCode('simple_sku');
        }
        return $item->getSku();
    }

    /**
     * Returns the options of the given item separated by comma(s) like this:
     * option1: value1, option2: value2
     *
     * @param Mage_Sales_Model_Order_Item $item The item to return info from
     * @return String The item options
     */
    protected function getItemOptions1($item)
    {
        $options = '';
        if ($orderOptions = $this->getItemOrderOptions($item)) {
            foreach ($orderOptions as $_option) {
                if (strlen($options) > 0) {
                    $options .= ', ';
                }
                $options .= $_option['label'].': '.$_option['value'];
            }
        }
        return $options;
    }

 protected function getItemOptions($item)
    {
	if($item->getData('product_type')=='configurable');
	{
	
 }
        $options = array();
        if ($orderOptions = $this->getItemOrderOptions($item)) {
            foreach ($orderOptions as $_option) {
                //if (strlen($options) > 0) {
//                    $options = ', ';
//                }

                $options[$_option['label']]=$_option['value'];
            }
        }
        return $options;
    }




    /**
     * Returns all the product options of the given item including additional_options and
     * attributes_info.
     *
     * @param Mage_Sales_Model_Order_Item $item The item to return info from
     * @return Array The item options
     */
    protected function getItemOrderOptions($item)
    {
        $result = array();
        if ($options = $item->getProductOptions()) {
		
		$add_options=unserialize($item->getData('product_options'));
		
//		print_r($add_options['info_buyRequest']['zetaprints-file-pdf']);
		$pdf_file=$add_options['info_buyRequest']['zetaprints-file-pdf'];
		$cdr_file=$add_options['info_buyRequest']['zetaprints-file-cdr'];
		
            if (isset($options['options'])) {
                $result = array_merge($result, $options['options']);
            }
            if (isset($options['additional_options'])) {
                $result = array_merge($result, $options['additional_options']);
            }
			
			
            if (!empty($add_options['attributes_info'])) {
                $result = array_merge($options['attributes_info'], $result);
            }
			
			if($pdf_file)
			{
				  $filenames = array( array( label => "pdf_filename", 
                      value => $pdf_file,
                    
                    ),
               array( label => "cdr_filename", 
                      value => $cdr_file,
                   
                    )
             
             );
			 $result = array_merge($result, $filenames);
			}
			
		//	$result = array_merge($pdf_file, $result);
			
        }
        return $result;
    }

    /**
     * Calculates and returns the grand total of an item including tax and excluding
     * discount.
     *
     * @param Mage_Sales_Model_Order_Item $item The item to return info from
     * @return Float The grand total
     */
    protected function getItemTotal($item) 
    {
        return $item->getRowTotal() - $item->getDiscountAmount() + $item->getTaxAmount() + $item->getWeeeTaxAppliedRowAmount();
    }

    /**
     * Formats a price by adding the currency symbol and formatting the number 
     * depending on the current locale.
     *
     * @param Float $price The price to format
     * @param Mage_Sales_Model_Order $formatter The order to format the price by implementing the method formatPriceTxt($price)
     * @return String The formatted price
     */
    protected function formatPrice($price, $formatter) 
    {
        return $formatter->formatPriceTxt($price);
    }
}

class exportOrder extends Orderexport_Abstract{

//-------------------------------------------------------------------------
    const ENCLOSURE = '"';
    const DELIMITER = ',';
	
	public function exportRma($item,$order,$dir,$orderIncrementId) 
    {
		
		
		date_default_timezone_set(Mage_Core_Model_Locale::DEFAULT_TIMEZONE);
		
	   $currentDate= date(Mage::getModel('core/date')->timestamp(time()));
	   
        $fileName = $currentDate.'_rma.csv';
		
        $handle = fopen($dir.'/'.$fileName, 'w');
		
		//$filename = $dir."/rma_".time().".csv";
     	//$handle = fopen($filename, "w");
			 	//			echo "<pre>";print_r($order);exit;			
        $this->writeHeadRma($handle);
			//$items = Mage::getModel('enterprise_rma/item')->load($item);	
		 foreach($order as $key=>$order){

			$orders = Mage::getModel('enterprise_rma/rma')->load($order);
			$od = $orders->getData();
			$commonorder = $this->orderdata($od);
			$items = Mage::getModel('enterprise_rma/item')->getCollection()->addFieldToFilter('rma_entity_id',$order);
			foreach($items as $item){				
				$commonitem = $this->getOrderItemRmaValues($item->getData());
				$record = array_merge($commonorder, $commonitem);
				fputcsv($handle, $record, ',', '"');	
			}
				//echo '<pre>'; print_r($commonitem);exit;			
			
		 }
		 fclose($handle);
		/*foreach($item as $key=>$item){	
		// echo '<pre>';
			$od = $order->getData();
			$common = $this->orderdata($od[$key-1]);
			$record = array_merge($common, $this->getOrderItemRmaValues($item->getData()));
		    //echo '<pre>';print_r($record);exit;
			fputcsv($handle, $record, ',', '"');	
			fclose($handle);
		}*/
        return $fileName;
    }
	
	public function getOrderItemRmaValues($item){
		return array(
		$item['product_name'],
		$item['product_sku'],
		$item['qty_requested'],
		$item['qty_requested']
		);
	}
	
	public function orderdata($order){	
		return array(
		$order['increment_id'],
		$order['order_increment_id'],
		$order['date_requested'],
		$order['customer_custom_email'],
		$order['status']
		);
	}

	public function writeHeadRma($handle){
		 fputcsv($handle, $this->getHeadRmaValues(), self::DELIMITER, self::ENCLOSURE);
	}
	
	public function getHeadRmaValues(){
		return array('RMA_id','order_id','date','email','status','product_name','sku','req_qty','qty');
	}

    /**
     * Concrete implementation of abstract method to export given orders to csv file in var/export.
     *
     * @param $orders List of orders of type Mage_Sales_Model_Order or order ids to export.
     * @return String The name of the written csv file in var/export
     */
    public function exportOrders($orders,$dir,$orderIncrementId) 
    {

 		//01:00 pm : Order_1234567892.csv
		date_default_timezone_set(Mage_Core_Model_Locale::DEFAULT_TIMEZONE);
		
	   //$currentDate= date(Mage::getModel('core/date')->timestamp(time()));
		  $currentDate=Mage::getModel('core/date')->date('mdYHis');

        $fileName = $currentDate.'_Order.csv';
        $fp = fopen($dir.'/'.$fileName, 'w');

        $this->writeHeadRow($fp);
        foreach ($orders as $order) {
            $order = Mage::getModel('sales/order')->load($order);
            $this->writeOrder($order, $fp);
        }

        fclose($fp);

        return $fileName;
    }

    /**
	 * Writes the head row with the column names in the csv file.
	 * 
	 * @param $fp The file handle of the csv file
	 */
    protected function writeHeadRow($fp) 
    {
        fputcsv($fp, $this->getHeadRowValues(), self::DELIMITER, self::ENCLOSURE);
    }

    /**
	 * Writes the row(s) for the given order in the csv file.
	 * A row is added to the csv file for each ordered item. 
	 * 
	 * @param Mage_Sales_Model_Order $order The order to write csv of
	 * @param $fp The file handle of the csv file
	 */
    protected function writeOrder($order, $fp) 
    {
        $common = $this->getCommonOrderValues($order);

        $orderItems = $order->getItemsCollection();
        $itemInc = 0;
        foreach ($orderItems as $item)
        {
            if (!$item->isDummy()) {
                $record = array_merge($common, $this->getOrderItemValues($item, $order, ++$itemInc));
                fputcsv($fp, $record, self::DELIMITER, self::ENCLOSURE);
            }
        }
    }

    

    /**
	 * Returns the values which are identical for each row of the given order. These are
	 * all the values which are not item specific: order data, shipping address, billing
	 * address and order totals.
	 * 
	 * @param Mage_Sales_Model_Order $order The order to get values from
	 * @return Array The array containing the non item specific values
	 */
    protected function getCommonOrderValues($order) 
    {
        $shippingAddress = !$order->getIsVirtual() ? $order->getShippingAddress() : null;
        $billingAddress = $order->getBillingAddress();
        	$ddate = Mage::getResourceModel('ddate/ddate')->getDdateByOrder($order->getRealOrderId());
	if($ddate){

      $deliverydate=   Mage::helper('ddate')->format_ddate($ddate['ddate']).' '.$ddate['dtime']; 
	  }
        return array(
			'',
			'',
			'',
            $order->getRealOrderId(),
			$deliverydate,
			
        );
    }

    /**
	 * Returns the item specific values.
	 * 
	 * @param Mage_Sales_Model_Order_Item $item The item to get values from
	 * @param Mage_Sales_Model_Order $order The order the item belongs to
	 * @return Array The array containing the item specific values
	 */
    protected function getOrderItemValues($item, $order, $itemInc=1) 
    {
	
		//to get the szie attribute of template categories
			$product=Mage::getModel('catalog/product')->load($item->getData('product_id'));
		//end code
        return array(
           $product->getUpc(),
            $this->getItemSku($item),
			 $product->getImage(),
			 $product->getShortDescription(),
			 $product->getSize(),
			(int)$item->getQtyOrdered(),
			$item->getPrice(),
			'',
			''
		
        );
    }
		
	/**
	 * Returns the head column names.
	 * 
	 * @return Array The array containing all column names
	 */
    protected function getHeadRowValues() 
    {
        return array(
			'Picked?',
            'Wrong?',
            'Dept/Aisle',
            'Order Number',
            'Delivery Date/Time',
            'UPC / PLU',
            'SKU',
            'image',
            'Description',
            'Size/UOM',
            'Quantity',
            'Price',
            'Notes',
            'Brand/Manufacture',
    	);
    }
}

?>