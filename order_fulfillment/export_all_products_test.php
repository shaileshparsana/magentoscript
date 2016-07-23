<table>
<form method="post" action="">
	<tr>
		<td>
			<label>Start Id</label>
		</td>
		<td>
			<input type="text" name="start_id" value="" id="start_id">
		</td>
	</tr>
	<tr>
		<td>
			<label>End Id</label>
		</td>
		<td>
			<input type="text" name="end_id" value="" id="end_id">
		</td>
	</tr>
	<tr>
		<td>
			<label>&nbsp;</label>
		</td>
		<td>
			<input type="submit" value="submit">
		</td>
	</tr>
</form>
</table>
<?php   
	//error_reporting(E_ERROR | E_WARNING | E_PARSE);
	ini_set('memory_limit','512M');
	set_time_limit(0); 
	$rootdir = dirname(__DIR__);
	 require_once('../app/Mage.php'); 

	umask(0);
	Mage::app('default');
	init();
function init()
{

	$file_path = $local_file = Mage::getBaseDir()."/order_fulfillment/csvfiles/products.csv"; //file path of the CSV file in which the $data to be saved
	
	$start = $_POST['start_id']; 
	$end = $_POST['end_id'];

	$mage_csv = new Varien_File_Csv(); //mage CSV
	
		
		$products_ids = Mage::getModel('catalog/product')->getCollection();
		$products_ids->addAttributeToFilter('entity_id',array('from' => $start,'to' => $end));

		$products_model = Mage::getModel('catalog/product'); //get $products model
		$products_row = array();    
		
		foreach ($products_ids as $pid)
		{
			$product = $products_model->load($pid->getId());
			if($product->getTypeId() == 'simple')
			{
				$stockItem = $product->getStockItem();
				$data['store'] = 'Admin';
				$data['websites'] = 'base';
				$data['attribute_set'] = 'default';
				$data['type'] = $product->getTypeId();
				$data['category_ids'] = join(',', $product->getCategoryIds());
				$data['sku'] = $product->getSku();
				$data['has_options'] = $product->getHasOptions();
				$attr = $products_model->getResource()->getAttribute("color");
				$color_label = '';
				if ($attr->usesSource()) {
					$color_label = $attr->getSource()->getOptionText($product->getColor());
				} 
				$data['color'] = $color_label;
				$status = array(1=>'Enabled',2=>'Disabled');
				$data['status'] = $status[$product->getStatus()];
				$visible = array(1=>'Not Visible Individually',2=>'Catalog',3=>'Search',4=>'Catalog, Search');
				$data['visibility'] = $visible[$product->getVisibility()];
				$data['enable_googlecheckout'] = $product->getEnableGooglecheckout();
				$taxclass = array(0=>'None',1=>'default',2=>'Taxable Goods',4=>'Shipping');
				$data['tax_class_id'] = $taxclass[$product->getTaxClassId()];
				$data['related_tgtr_position_limit'] = $product->getRelatedTgtrPositionLimit();
				$data['related_tgtr_position_behavior'] = $product->getRelatedGtrPositionBehavior();
				$data['upsell_tgtr_position_limit'] = $product->getUpsellTgtrPositionLimit();
				$data['upsell_tgtr_position_behavior'] = $product->getUpsellTgtrPositionBehavior();
				$attrBrands = $products_model->getResource()->getAttribute("brands");
				// $brandsLabel = '';
				// if ($attrBrands->usesSource()) {
					// $brandsLabel = $attr->getSource()->getOptionText($product->getBrands());
				// } 
				$data['brands'] = $brandsLabel;
				$data['size'] = $product->getSize();
				$attr = $products_model->getResource()->getAttribute("fabric");
				// $fabricLabel = '';
				// if ($attr->usesSource()) {
					// $fabricLabel = $attr->getSource()->getOptionText($product->getFabric());
				// } 
				$data['fabric'] = $fabricLabel;
				$data['dynamic_imaging'] = $product->getDynamicImaging();
				$data['customize_product'] = $product->getCustomizeProduct();
				$data['customizable'] = $product->getCustomizable();
				$data['allow_screenpriting'] = $product->getAllowScreenpriting();
				$data['allow_embroidery'] = $product->getAllowEmbroidery();
				$data['add_to_cart_display'] = $product->getAddToCartDisplay();
				$data['name'] = $product->getName();
				$data['meta_title'] = $product->getMetaTitle();
				$data['meta_description'] = $product->getMetaDescription();
				$data['image'] = $product->getImage();
				$data['small_image'] = $product->getSmallImage();
				$data['thumbnail'] = $product->getThumbnail();
				$data['gallery'] = $product->getGallery();
				$data['url_key'] = $product->getUrlKey();
				$data['url_path'] = $product->getUrlPath();
				$data['custom_design'] = $product->getCustomDesign();
				$data['page_layout'] = $product->getPageLayout();
				$data['options_container'] = $product->getOptionsContainer();
				$data['image_label'] = $product->getImageLabel();
				$data['small_image_label'] = $product->getSmallImageLabel();
				$data['thumbnail_label'] = $product->getThumbnailLabel();
				$data['country_of_manufacture'] = $product->getCountryOfManufacture();
				$data['msrp_enabled'] = $product->getMsrpEnabled();
				$data['msrp_display_actual_price_type'] = $product->getMsrpDisplayActualPriceType();
				$data['gift_message_available'] = $product->getGiftMessageAvailable();
				$data['gift_wrapping_available'] = $product->getGiftWrappingAvailable();
				$data['is_returnable'] = $product->getIsReturnable();
				$data['suppliers'] = $product->getSuppliers();
				$data['companion_sku'] = $product->getCompanionSku();
				$data['webtoprint_template'] = $product->getWebtoprintTemplate();
				$data['style_number'] = $product->getStyleNumber();
				$data['sizing'] = $product->getSizing();
				$data['price'] = $product->getPrice();
				$data['special_price'] = $product->getSpecialPrice();
				$data['cost'] = $product->getCost();
				$data['msrp'] = $product->getMsrp();
				$data['gift_wrapping_price'] = $product->getGiftWrappingPrice();
				$data['description'] = $product->getDescription();
				$data['short_description'] = $product->getShort_description();
				$data['meta_keyword'] = $product->getMetaKeyword();
				$data['custom_layout_update'] = $product->getCustomLayoutUpdate();
				$data['brand_info'] = $product->getBrandInfo();
				$data['special_from_date'] = $product->getSpecialFromDate();
				$data['special_to_date'] = $product->getSpecialToDate();
				$data['news_from_date'] = $product->getNewsFromDate();
				$data['news_to_date'] = $product->getNewsToDate();
				$data['custom_design_from'] = $product->getCustomDesignFrom();
				$data['custom_design_to'] = $product->getCustomDesignTo();
				$data['last_product_updated'] = $product->getLastProductUpdated();
				$data['qty'] = $stockItem->getData('qty');
				$data['min_qty'] = $stockItem->getData('min_qty');
				$data['use_config_min_qty'] = $stockItem->getData('use_config_min_qty');
				$data['is_qty_decimal'] = $stockItem->getData('is_qty_decimal');
				$data['backorders'] = $stockItem->getData('backorders');
				$data['use_config_backorders'] = $stockItem->getData('use_config_backorders');
				$data['min_sale_qty'] = $stockItem->getData('min_sale_qty');
				$data['use_config_min_sale_qty'] = $stockItem->getData('use_config_min_sale_qty');
				$data['max_sale_qty'] = $stockItem->getData('max_sale_qty');
				$data['use_config_max_sale_qty'] = $stockItem->getData('use_config_max_sale_qty');
				$data['is_in_stock'] = $stockItem->getData('is_in_stock');
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
				$data['product_name'] = $product->getProductName();
				$data['store_id'] = $product->getStoreId();
				$data['product_type_id'] = $product->getTypeId();
				$data['product_status_changed'] = $product->getProductStatusChanged();  
				$data['product_changed_websites'] = $product->getProductChangedWebsites();
				// $brandLabel = '';
				// if ($attr->usesSource()) {
					// $brandLabel = $attr->getSource()->getOptionText($product->getBrand());
				// } 
				$data['brand'] = $brandLabel;
				$data['salesforce_id'] = $product->getSalesforceId();
				$data['salesforce_pricebook_id'] = $product->getSalesforcePricebookId();
				$data['min_qty_text'] = $product->getMinQtyText();
				$data['names_number_colors'] = $product->getNamesNumberColors();
				$data['material_features'] = $product->getMaterialFeatures();
				// $colorGroupLabel = '';
				// if ($attr->usesSource()) {
					// $colorGroupLabel = $attr->getSource()->getOptionText($product->getColorGroup());
				// } 
				$data['color_group'] = $colorGroupLabel;
				$data['length'] = $product->getLength();
				$data['height'] = $product->getHeight();
				$data['width'] = $product->getWidth();
				$data['companion_producttext'] = $product->getCompanionProducttext();
				$data['is_recurring'] = $product->getIsRecurring();
				$data['recurring_profile'] = $product->getRecurringProfile();
				$data['amazon_id'] = $product->get();
				$data['unique_no'] = $product->getUniqueNo();
				$products_row[] = $data;
			}
			
		} 
		
		$headrs = array("store","websites","attribute_set","type","category_ids","sku","has_options","color","status","visibility","enable_googlecheckout","tax_class_id","related_tgtr_position_limit","related_tgtr_position_behavior","upsell_tgtr_position_limit","upsell_tgtr_position_behavior","brands","size","fabric","dynamic_imaging","customize_product","customizable","allow_screenpriting","allow_embroidery","add_to_cart_display","name","meta_title","meta_description","image","small_image","thumbnail","gallery","url_key","url_path","custom_design","page_layout","options_container","image_label","small_image_label","thumbnail_label","country_of_manufacture","msrp_enabled","msrp_display_actual_price_type","gift_message_available","gift_wrapping_available","is_returnable","suppliers","companion_sku","webtoprint_template","style_number","sizing","price","special_price","cost","msrp","gift_wrapping_price","description","short_description","meta_keyword","custom_layout_update","brand_info","special_from_date","special_to_date","news_from_date","news_to_date","custom_design_from","custom_design_to","last_product_updated","qty","min_qty","use_config_min_qty","is_qty_decimal","backorders","use_config_backorders","min_sale_qty","use_config_min_sale_qty","max_sale_qty","use_config_max_sale_qty","is_in_stock","low_stock_date","notify_stock_qty","use_config_notify_stock_qty","manage_stock","use_config_manage_stock","stock_status_changed_auto","use_config_qty_increments","qty_increments","use_config_enable_qty_inc","enable_qty_increments","is_decimal_divided","stock_status_changed_automatically","use_config_enable_qty_increments","product_name","store_id","product_type_id","product_status_changed","product_changed_websites","brand","salesforce_id","salesforce_pricebook_id","min_qty_text","names_number_colors","material_features","color_group","length","height","width","companion_producttext","is_recurring","recurring_profile","amazon_id","unique_no");
		
		 array_unshift($products_row,$headrs);
		
	
		
		$mage_csv->saveData($file_path,$products_row);
		echo "Successfully $products exported at:-$file_path";
		
		}