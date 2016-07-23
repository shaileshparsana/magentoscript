<form name="index" method="post" action="">
<table cellspacing="0" id="indexer_processes_grid_table" class="data">
   <colgroup>
      <col width="40" class="a-center">
      <col width="210">
   </colgroup>
   <thead>
      <tr class="headings">
         <th><span class="nobr">&nbsp;</span></th>
         <th class=" no-link"><span class="nobr">Index</span></th>
      </tr>
   </thead>
   <tbody>
      <tr >
         <td class="a-center ">
            <input type="checkbox" class="massaction-checkbox" value="1" name="index_process_1">                    
         </td>
         <td class="a-left ">
            Product Attributes                    
         </td>
      </tr>
      <tr >
         <td class="a-center ">
            <input type="checkbox" class="massaction-checkbox" value="9" name="index_process_9">                    
         </td>
         <td class="a-left ">
            Tag Aggregation Data                    
         </td>
      </tr>
      <tr title="" class="even">
         <td class="a-center ">
           <input type="checkbox" class="massaction-checkbox" value="2" name="index_process_2">                   
         </td>
         <td class="a-left ">
            Catalog product price                 
         </td>
         
      </tr>
      <tr title="" class="">
         <td class="a-center ">
           <input type="checkbox" class="massaction-checkbox" value="3" name="index_process_3">                  
         </td>
         <td class="a-left ">
            Catalog URL Rewrites                   
         </td>
         
      </tr>
      <tr title="" class="even">
         <td class="a-center ">
           <input type="checkbox" class="massaction-checkbox" value="4" name="index_process_4">                  
         </td>
         <td class="a-left ">
            Product URL Rewrites                  
         </td>
        
      </tr>
      <tr title="" class="">
         <td class="a-center ">
            <input type="checkbox" class="massaction-checkbox" value="5" name="index_process_5">                    
         </td>
         <td class="a-left ">
            Category URL Rewrites                  
         </td>
        
      </tr>
      <tr title="" class="even">
         <td class="a-center ">
           <input type="checkbox" class="massaction-checkbox" value="6" name="index_process_6">                    
         </td>
         <td class="a-left ">
           Catalog Category/Product Index                 
         </td>
        
      </tr>
      <tr title="" class="">
         <td class="a-center ">
          <input type="checkbox" class="massaction-checkbox" value="7" name="index_process_7">                   
         </td>
         <td class="a-left "> 
             Catalog Search Index                   
         </td>
        
      </tr>
      <tr title="" class="even">
         <td class="a-center ">
           <input type="checkbox" class="massaction-checkbox" value="8" name="index_process_8">                   
         </td>
         <td class="a-left ">
            Stock Status		        
         </td>
         
      </tr>
       <tr title="" class="even">
         <td  align="right" colspan="2">
           <input type="submit" class="submit" value="Submit" name="submit">                   
         </td>
         
      </tr>
   </tbody>
</table>
</form>
<?php
if(isset($_POST['submit'])){
	
	$files = glob('var/locks/*'); // get all file names
	foreach($files as $file){ // iterate files
	  if(is_file($file)){
		  $file_path = explode('.',$file);
		  $file_name_arr =explode('/',$file_path[0]);
		  $file_name =  $file_name_arr[count($file_name_arr)-1];
		  if(array_key_exists($file_name, $_POST)){
			  unlink($file); // delete file
		  }
	  }
	}
	if(isset($_POST['index_process_1']) && $_POST['index_process_1'] == 1 )
	{
		echo '<br><br>'.$output = shell_exec("php -f indexer.php -- --reindex catalog_product_attribute");
	}
	if(isset($_POST['index_process_2']) && $_POST['index_process_2'] == 2 )
	{
		echo '<br><br>'.$output = shell_exec("php -f indexer.php -- --reindex catalog_product_price");
	}
	if(isset($_POST['index_process_3']) && $_POST['index_process_3'] == 3 )
	{
		echo '<br><br>'.$output = shell_exec("php -f indexer.php -- --reindex url_redirect");
	}
	if(isset($_POST['index_process_4']) && $_POST['index_process_4'] == 4 )
	{
		echo '<br><br>'.$output = shell_exec("php -f indexer.php -- --reindex catalog_url_product");
	}
	if(isset($_POST['index_process_5']) && $_POST['index_process_5'] == 5 )
	{
		echo '<br><br>'.$output = shell_exec("php -f indexer.php -- --reindex catalog_url_category");
	}
	if(isset($_POST['index_process_6']) && $_POST['index_process_6'] == 6 )
	{
		echo '<br><br>'.$output = shell_exec("php -f indexer.php -- --reindex catalog_category_product");
	}
	if(isset($_POST['index_process_7']) && $_POST['index_process_7'] == 7 )
	{
		echo '<br><br>'.$output = shell_exec("php -f indexer.php -- --reindex catalogsearch_fulltext");
	}
	if(isset($_POST['index_process_8']) && $_POST['index_process_8'] == 8 )
	{
		echo '<br><br>'.$output = shell_exec("php -f indexer.php -- --reindex cataloginventory_stock");
	}
	if(isset($_POST['index_process_9']) && $_POST['index_process_9'] == 9 )
	{
		echo '<br><br>'.$output = shell_exec("php -f indexer.php -- --reindex tag_summary");
	}
	$url ='http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']; 
	//header('Location: "'.$url.'"');
	

}
?>