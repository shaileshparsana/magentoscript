var row_id;
var last_row_id = 5;
var actionUrl;
var configAttr;	
var currentProduct;
var rowCount = 0;

jQuery(document).ready(function(){
	
	actionUrl = jQuery("#quickorderAction").attr("value");

	jQuery("#quickorder_link").click(function(){
		
		
		jQuery(this).toggleClass("active");												
		jQuery("#quickorder_box").slideToggle("slow");
		
		 jQuery("#camerapart").hide();
		 jQuery('#stop').click();

	});
	
	jQuery("#quickorder_close").click(function(){
		jQuery("#quickorder_link").removeClass("active");												
		jQuery("#quickorder_box").slideUp("slow");			
		
		jQuery('#stop').click();
		 jQuery("#camerapart").hide();
		
	});	
	
	
	jQuery("[id^=row_]").each(function(){
		rowCount++;									   	
	});
	


removeItem(); 
autoSuggest('');
// add new items row
	jQuery("#add_item").click(function(){
		var row = jQuery(".itemsBox tr:last");
		var html = '<tr id="row_'+last_row_id+'">';
			html+='<td class="qosku itemsku_'+last_row_id+'"><span class="">Type Product Name/Sku #/UPC</span><input id="sku_'+last_row_id+'" type="text" class="input-text sku" title="sku_'+last_row_id+'" name="product['+last_row_id+'][sku]" alt="sku_'+last_row_id+'" /></td>';
			html+='<td class="qoqty"><span class="">Qty</span><input type="text" id="qty_'+last_row_id+'" class="input-text qty" name="product['+last_row_id+'][qty]">';
			html+='<input type="hidden" name="product['+last_row_id+'][product]" id="id_'+last_row_id+'" value="" />';
			html+='</td>';
			html+='<td class="iphonescan"></td>';
			 
			html+='<td id="itemdata_'+last_row_id+'" class="qodata"><textarea class="product_name_title" name="pname_data_'+last_row_id+'" readonly="readonly"></textarea></td>';
			html+='<td class="a-right qoremove"><a href="javascript:;" class="remove" id="remove_'+last_row_id+'">x Remove</a></td>';
			html+='</tr>';
		 //block.after(html);
		 //row .before(html);
		 jQuery("#lastRow").before(html);
		 rowCount++;
		 autoSuggest(".itemsku_"+last_row_id);
         removeItem();	
		// blurSKU(); 
		 last_row_id = last_row_id+1;
	});
	
	 
	jQuery("#add_to_cart").click(function(){
	});	
	

});


function autoSuggest(element){
if(element==''){
	//element_id = ".sku_field input[type=text]";
	element_id = "input[type=text].sku";
}else{
	element_id = element+' input[type=text]';
}	
var selected_value = '';
jQuery(element_id).autoSuggest(actionUrl+'suggest/', 
		{
			minChars: 2, matchCase: false,selectionLimit:1,
			limitText:" ",
			emptyText:'No results found',
			startText:"",
			keyDelay: 500,
			selectedItemProp:"name",
			searchObjProps:"name",
			formatList: function(data, elem){
			var html = '<div>'+
						'<table>'+
							'<tr>'+
							'<td class="pimage"><img src="'+data.image+'" width="60" height="57"/></td>'+
						'<td class="pdetail"><strong>'+data.name+'</strong><br> SKU#: '+data.value+'<br> UPC: '+data.upc+'</td>'+
							'</tr>'+
						'</table>'+
						'</div>';
			var new_elem = elem.html(html);
			return new_elem;
			},
			resultClick:function(data){
					var id = jQuery(this).parents("div.as-results").attr("id");
					var newid = id.replace("results","values");
					var value = jQuery("#"+newid).attr("value");
					id = newid.replace("values","input");

					jQuery("#"+id).attr("value",value);
					
					//custom code start plumtree
				//	var row_id= jQuery("#"+id).attr("title");
					var row_id= jQuery("#"+id).attr("alt");
					//custom code end

					loadProduct(row_id,value);
					//custom code to hide suggested text
					jQuery("#quickorder_box .suggested-text-scan").hide();
					//custom code end here
					productLoaded = true;
			},
			selectionRemoved:function(elem){
					//alert(elem);
				}	
		});

}




function loadProduct(id,sku){	
		row_id = id.replace("sku_","");
		var optBlock = jQuery("#itemdata_"+row_id);	
		var qtyBlock = jQuery("#qty_"+row_id);
		var value = sku;
		
		if(value!=''){
				if(!value.length>2){
					return false;
				}
			if(qtyBlock.attr("value")==""){
				optBlock.html('<span class="ajax-loader">&nbsp;</span>');
			}			

		
		//if(jQuery.inArray(value,Products)!=-1){
//				if(qtyBlock.attr("value")==""){
//					//jQuery(this).focus();
//					//jQuery(this).attr("value","");
//					//addError(optBlock,'Product already added with this SKU.');
//				}
//				//return true;
//		}


		
			var myAjax = new Ajax.Request(
			actionUrl+'loadproduct/?sku='+value, 
			{
				method: 'post', 
				onComplete: function(response){
					 var json = response.responseText.evalJSON();
					 if(json.success==1){
						productLoaded = true;
				 		//Products.push(json.product.sku);	
						 qtyBlock.attr("value","1");	
						 var product = json.product;
						 if(product!=''){
							 jQuery("#id_"+row_id).attr("value",product.id);
							var output =  parseProductOptions(product,row_id); 
							optBlock.html(output).hide().fadeIn();
						 }
					 }else{
						 productLoaded = false;
						addError(optBlock,'Invalid Product SKU.');						 					 }
				}
			});
		}else{
			addError(optBlock,'Please enter SKU.');
		}
}

	
function parseProductOptions(product,pid){
	console.log(pid);
	var html = '<table class="qck_product_options" width="100%">';
		html+='<tr>'; 
	
		if(product.is_configurable){

		html+= configProductOptions(product);

		}else if(product.custom_options){
		//	html+=customProductOptions(product);				
		}
		html+='<td>';
		html+='<span class="title" style="display:none">'+product.name+'</span>';
		html+='<textarea class="product_name_title active_pname" name="pname_data_'+pid+'" readonly="readonly">'+product.name+'</textarea>';
		
		html+='</td>';		
		html+='</tr>';
	html+='</table>';
	return html;
}	



function removeItem(){
	
	jQuery("a.remove").unbind('click');
	jQuery("a.remove").bind("click",function(){
		if(rowCount<=1){
				alert('You can not delete all rows.');
				return false;
		}									 
											 
		var id = jQuery(this).attr("id");
		row_id = id.replace("remove_","");
		jQuery("#row_"+row_id).attr("bgcolor","#FFB3B3").hide().fadeIn().remove();
		rowCount--;
	});
}
function addError(elem,msg){	
	elem.html('<span class="error">'+msg+'</span>')
					.hide().fadeIn();	
}

function submitQuickorderForm(){
	var error = false;
	var message = '';
	var statusDiv = jQuery("#loader");
	statusDiv.html('<span class="ajax-loader">&nbsp;</span>');
	
	jQuery(".input-required option:selected").each(function(){
		var val = jQuery(this).attr("value");
		if(val==''){
			jQuery(this).parent().removeClass('validation-passed');
			jQuery(this).parent().addClass('validation-failed');
			error = true;
		}else{
			jQuery(this).parent().removeClass('validation-failed');
			jQuery(this).parent().addClass('validation-passed');
		}	
	});
	
	/*if(Products.length==0){
		error = true;
		message = 'There is no product to add to cart';
	}*/
	
	
	if(error){
		message = (message!='')?message:'Please fix errors.';
		addError(statusDiv,message);
		return false;
	}
	var params = jQuery("#quickorderFrm").serialize();
	
	var myAjax = new Ajax.Request(
			actionUrl+'addcart/', 
			{
				method: 'post', 
				parameters:  params,
				onComplete: function(response){
					var json = response.responseText.evalJSON();
					if(json.success){
						addSuccess(statusDiv,json.message);	
						location.href = json.redirect_url;
					}else{
						addError(statusDiv,json.message);	
					}
				}
			});
	
	
}

function addSuccess(elem,msg){
	elem.html('<span class="success-msg success">'+msg+'</span>')
					.hide().fadeIn();	
}

/*---------------------------------------------------------*/

function configProductOptions(product){
	
	var output = '';
	
	var attributes = product.options.attributes;	
   var it = 0;
   
	configAttr =  product.options;
     
   output+= '<td width="40%"><div class="qck_options_box">';
   output+='<ul>';
	for(var i in attributes){
		var attr = attributes[i];
		var attrId = attr.id;
		var id = 'attribute'+attrId+'_'+row_id;
		var attr_opts = attr.options;
		
		output+='<li>';	

		output+='<select title="'+attr.label+'" name="product['+row_id+'][super_attribute]['+attrId+']" id="'+id+'" ';
		if(it>0){
			output+=' class="input-required super-attribute-select_'+product.id+'_'+row_id+'"';
			output+=' disabled = "disabled"';
		}else{
			output+=' class="input-required super-attribute-select_'+product.id+'_'+row_id+'"';
			output+=' onfocus="updateOptions(this)"';	
		}
		output+='>';
		
		output+='<option value="">Choose '+attr.label+'</option>';
		if(it==0){
			for(j in attr_opts){
				if(attr_opts[j].label != undefined){
					output+='<option value="'+attr_opts[j].id+'">'+attr_opts[j].label+'</option>';
				}
			}
		}
		
		output+='</select>';

		
			output+='</li>';	
		
		it++;
		
	}
	   output+='</ul>';
	output+='</div></td>';
	
	//var settings = jQuery("[class^=super-attribute-select_]");

	
	
	 
	
	if(it>0){
		return output;
	}else{
		return '';	
	}
	
}
function updateOptions(element){
	
	configAttr.row_id = row_id;
	
	var spConfig = new Quickorder_Product.Config(configAttr);

	//alert(configAttr)	
//	configAttr =  product.options;
//	var settings = $$('super-attribute-select_'+product.id);
//	fillSelect(element);
}




function goforscan(scanurl)
{
	

		repo.preserve($form/*, $form.attr('id')*/ ); // don't necessarily need an identifier


	setTimeout(function() {
			if( isMobile.iOS() ) 
			window.location = "https://itunes.apple.com/in/app/pic2shop-barcode-scanner-qr/id308740640?mt=8";
			else if(isMobile.Android())
			window.location = "https://play.google.com/store/apps/details?id=com.visionsmarts.pic2shop";
			else if(isMobile.BlackBerry())
			window.location = "http://apk4bb.com/APK-App_pic2shop-Barcode-amp-QR-Scanner_for-BB-BlackBerry.html";
			else if(isMobile.Windows())	
			window.location = "https://www.microsoft.com/en-us/store/apps/pic2shop/9wzdncrcwpg4";
	}, 25);
//window.location="pic2shop://scan?callback=http://"+scanurl;
//window.open("pic2shop://scan?callback=http://"+scanurl,"_self");
location.href = "pic2shop://scan?callback=http://"+scanurl;
//window.location="http://"+scanurl;
}