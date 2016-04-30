<?php

$list=array(
	'table1'=> 'midle_time'
	
	);
	
	
	
	

	
	
		
		// define our database connection
define('DB_SERVER', 'localhost');
define('DB_SERVER_USERNAME', 'your_name');
define('DB_SERVER_PASSWORD', 'password');
define('DB_DATABASE', 'your_name');

$link = @mysql_connect(DB_SERVER, DB_SERVER_USERNAME, DB_SERVER_PASSWORD);
	if($link) mysql_select_db(DB_DATABASE); else die('ERROR CONNECT');
	mysql_query("set names utf8mb4");
		
	

	@set_time_limit(0);

	 
	
	while(true)
	{
		
		if (!mysql_ping ($link)) {
						mysql_close($link);
						$link = @mysql_connect(DB_SERVER, DB_SERVER_USERNAME, DB_SERVER_PASSWORD);
						if($link) mysql_select_db(DB_DATABASE); else die('ERROR CONNECT');
						mysql_query("set names utf8mb4");
						}
		
		$need_events_array=array();
			
			$need_events_query = mysql_query("select post_id from wp_rhc_events where now()>event_start and now()<event_end");
			while($need_event=mysql_fetch_array($need_events_query))
				
				{
					 $need_events_array[]=$need_event['post_id'];
					
				}
		
		foreach($need_events_array as $need_events_id){
		
		if (!mysql_ping ($link)) {
						mysql_close($link);
						$link = @mysql_connect(DB_SERVER, DB_SERVER_USERNAME, DB_SERVER_PASSWORD);
						if($link) mysql_select_db(DB_DATABASE); else die('ERROR CONNECT');
						mysql_query("set names utf8mb4");
						}
						
			mysql_query("DELETE FROM maraphone_time_for_show WHERE events_id = ".$need_events_id." and user_id !='999999'");		
						
		
		//start time
		$start_time_qwery = mysql_query("select math_start_time, device_id FROM maraphone_start_time where distance='88' and events_id='".$need_events_id."' limit 1");
		$start_time = mysql_fetch_array($start_time_qwery);
		
		if ($start_time[0]>0){
		mysql_query("INSERT INTO maraphone_time_for_show (events_id,user_id,gate_id,time,uin) VALUES ('".$need_events_id."','999999','".$start_time['device_id']."','".$start_time['math_start_time']."','".$need_events_id."999999".$start_time['device_id']."st".$start_time['math_start_time']."') ON DUPLICATE KEY UPDATE events_id='".$need_events_id."',user_id='999999',gate_id='".$start_time['device_id']."',time='".$start_time['math_start_time']."'");}
		

			
			
			$start_number_query = mysql_query("select start_number, user_id from maraphone_partitipants where events_id = '".$need_events_id."'");
			while($start_number_aray=mysql_fetch_array($start_number_query))
				
			
				
				{
					//var_dump($start_number_aray['start_number']);
					
					$time_query = mysql_query("select mt.time, mt.device_id from maraphone_midle_time mt where mt.start_number = '".$start_number_aray['start_number']."' and mt.events_id='".$need_events_id."'");
					while($time_aray=mysql_fetch_array($time_query)) {
						
						
						
						
						$gate_id=$time_aray['device_id'];
						$time=explode(',',$time_aray['time']);
						
						//$time_for_show=array_sum($time)/count($time);
						$time_for_show=$time[0];
						
						$uin=$need_events_id.$start_number_aray['start_number'].$gate_id.$time_for_show;
						
						
						
						mysql_query("INSERT INTO maraphone_time_for_show (events_id,user_id,gate_id,time,uin) VALUES ('".$need_events_id."','".$start_number_aray['user_id']."','".$gate_id."','".$time_for_show."','".$uin."') ON DUPLICATE KEY UPDATE events_id='".$need_events_id."',user_id='".$start_number_aray['user_id']."',gate_id='".$gate_id."',time='".$time_for_show."'");
						
						
						
						
						
					}
					
					
				}
			
				
			
			
			
		}	
		sleep(30);
			
		}
			
			
			
		
		
	   
        
    
	


	mysql_close($link);
		
?>
