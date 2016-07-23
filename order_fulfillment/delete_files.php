<?php
require_once 'config.php';
$type = 'orders/export';  
$daysOld = 2;
//====================================================================


$d = mktime(0, 0, 0, date("m"), date("d")-$daysOld, date("Y"));
$date = date('Y-m-d',$d);
deleteFile($dir,$date,$type);

function deleteFile($dir,$date,$type){
	
$dir = $dir.'/'.$type;
	if ($handle = opendir($dir)) { 
		while (false !== ($file = readdir($handle))) {
			if ($file != "." && $file != "..") {
				if(preg_match('/^order_export/',$file)){
					$file = $dir.'/'.$file;
					if (file_exists($file)) {
						$filetime = date ("Y-m-d", filemtime($file));
						if($date==$filetime){
							unlink($file);
						}
					}
				}	
			}
		}
		closedir($handle);
		echo $dir.' directory files deleted';
	}
}


