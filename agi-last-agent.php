#!/usr/bin/php -q
<?php
$mysql_username=''
$mysql_password=''
$mysql_host='localhost'


  require('phpagi.php');
  error_reporting(E_ALL);

  $agi = new AGI();
		
	$cid = $agi->get_variable("CALLERID(num)");
	$cid = $cid['data'];
	
  	$con = mysql_connect($mysql_host,$mysql_username,$mysql_password);
		if (!$con)
		{
		$agi->Verbose('NOT CONNECTED ***');
		die('Could not connect: ' . mysql_error());

		}
		
		$agi->Verbose('CONNECTED ***');
		$query_agent="select * from cdr where dst LIKE '%".$cid. "%' and disposition IN ('ANSWERED','NO ANSWER','BUSY') and lastapp='Dial' and calldate >= CURDATE()-2 order by calldate desc limit 1";
		$name_agent = mysql_db_query("asteriskcdrdb",$query_agent,$con);
		$row_agent = mysql_fetch_array($name_agent);
		$lastDialedAgent_agent= $row_agent[src]; // last agent that called customer
		$agi->Verbose('#####lastDialedCustomer:'.$lastDialedAgent_agent);
		if($lastDialedAgent_agent){
			$agi->Verbose('#####if verbosed:');
			$agi->exec_goto('from-internal-additional', $lastDialedAgent_agent, '1');
		}else{
			$query_customer="select * from cdr where src LIKE '%".$cid. "%' and disposition='ANSWERED' and lastapp='Dial' and calldate >= CURDATE()-2 order by calldate desc limit 1";
			$name_customer = mysql_db_query("asteriskcdrdb",$query_customer,$con);
			$row_customer = mysql_fetch_array($name_customer);
			$lastDialedAgent_customer= $row_customer[dst]; // last agent that answered customer's phone
			$agi->Verbose('#####lastDialedAgent:'.$lastDialedAgent_customer);
			if($lastDialedAgent_customer){
				$agi->Verbose('#####else if verbosed:');
				//$agi->answer();
				//$confirmtemp = $agi->get_data('custom/call-last-agent',15000,1);
				//$confirm = $confirmtemp['result'];
			
				  //if($confirm == '1'){
				$agi->exec_goto('from-internal-additional', $lastDialedAgent_customer, '1');
	
				//}else{
					//$agi->exec_goto('from-internal-additional', '600', '1');	
				//}
	
			}else{
				if($lastDialedAgent_customer){
			$agi->Verbose('#####else if verbosed:');
			//$agi->answer();
			//$confirmtemp = $agi->get_data('custom/call-last-agent',15000,1);
			//$confirm = $confirmtemp['result'];
		
  			//if($confirm == '1'){
			$agi->exec_goto('from-internal-additional', $lastDialedAgent_customer, '1');

			//}else{
				//$agi->exec_goto('from-internal-additional', '600', '1');	
			//}

		}
			}
		}
		mysql_close($con);
?>
