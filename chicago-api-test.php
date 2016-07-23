<?php 
        $host = "store.baltimoresun.com"; //our online shop url
        $client = new SoapClient("http://".$host."/api/soap/?wsdl"); //soap handle
        $apiuser= "brad@pediment.com"; //webservice user login
        $apikey = "5Qf*FkfI6hd1H|9eaA"; //webservice user pass
        $action = "catalog_product.info"; //an action to call later (loading Sales Order List)
		
		
		
		 $sess_id= $client->login($apiuser, $apikey); //we do login
		
		
		try { 
				$vendorid= $_GET['vendor_id'];
				
				$filters =array(array('status'=>array('eq'=>'processing'),'store_id'=>array('eq'=>'4')));
				
				$orderList = $client->call($sess_id, 'customvendorapi_api.orderlist',array('filters' => $filters, 'vendorid' => $vendorid));
				//var_dump($orderList);
				if(!empty($orderList))
				{	
						echo "Result for all Orders of vendor ID = ".$vendorid;
						echo "<pre />";	
						print_r($orderList);
				}
				else
				{
						echo "There is no order related to vedor ID = ".$vendorid;	
				}
		
		
		}
		 catch (Exception $e) { //while an error has occured
            echo "==> Error: ".$e->getMessage(); //we print this
               exit();
        }
		



?>