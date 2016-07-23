<?php	

	

    $output = '';	

	echo '<br><br>'.$output = shell_exec("php -f /www/sites/www.baltimoresun.com/files/html/shell/indexer.php -- --reindex catalog_product_price");	

	mail("magento@webindiainc.com","catalog reindexing cron",$output);

?>