<?php	
	
    $output = '';
	echo '<br><br>'.$output = shell_exec("php -f /www/sites/www.baltimoresun.com/files/html/shell/indexer.php -- --reindex catalog_product_attribute");	
	echo '<br><br>'.$output = shell_exec("php -f /www/sites/www.baltimoresun.com/files/html/shell/indexer.php -- --reindex catalog_product_price");
	echo '<br><br>'.$output = shell_exec("php -f /www/sites/www.baltimoresun.com/files/html/shell/indexer.php -- --reindex cataloginventory_stock");
	echo '<br><br>'.$output = shell_exec("php -f /www/sites/www.baltimoresun.com/files/html/shell/indexer.php -- --reindex catalog_category_product");
	mail("naim@plumtreegroup.net","catalog reindexing cron",$output);
?>