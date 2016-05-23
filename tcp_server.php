<?php

$address = "@ip@";
$port = "port";

$list=array(
	'table1'=> 'midle_time'
	
	);
		
		// define our database connection
define('DB_SERVER', 'localhost');
define('DB_SERVER_USERNAME', 'your_names');
define('DB_SERVER_PASSWORD', 'password');
define('DB_DATABASE', 'your_name');
		
	$link = @mysql_connect(DB_SERVER, DB_SERVER_USERNAME, DB_SERVER_PASSWORD);
	if($link) mysql_select_db(DB_DATABASE); else die('ERROR CONNECT');
	mysql_query("set names utf8mb4");

	@set_time_limit(0);

	
	$socket = stream_socket_server("tcp://".$address.":".$port."", $errno, $errstr);

if (!$socket) {
    die("$errstr ($errno)\n");
}

	
	$file = "/www/soc_log.txt";
	
	
	
	
	$connects = array();
	$fh = fopen($file, "a");
	while(true)
	{
		if (!mysql_ping ($link)) {
						mysql_close($link);
						$link = @mysql_connect(DB_SERVER, DB_SERVER_USERNAME, DB_SERVER_PASSWORD);
						if($link) mysql_select_db(DB_DATABASE); else die('ERROR CONNECT');
						mysql_query("set names utf8mb4");
						}
		
		
		
		//формируем массив прослушиваемых сокетов:
    $read = $connects;
    $read []= $socket;
    $write = $except = null;

    if (!stream_select($read, $write, $except, null)) {//ожидаем сокеты доступные для чтения (без таймаута)
        break;
    }
	sleep(1);
	//var_dump($socket);
	//var_dump($read);

    if (in_array($socket, $read)) {//есть новое соединение
        $connect = stream_socket_accept($socket, -1);//принимаем новое соединение
        $connects[] = $connect;//добавляем его в список необходимых для обработки
        //unset($read[ array_search($socket, $read) ]);
    }
	
	

    foreach($read as $connect) {//обрабатываем все соединения
        $buffer = '';
        $headers = stream_socket_recvfrom($connect, 2000);
		
			
			$buff =bin2hex($headers);
			
			
			
			
						  
		

		    	if(strlen($buff)>3)  {
					
					$out = $buff . '_' . PHP_EOL;
					
					$len=strlen($buff)-4;
					
					$send=false;
					
					if (strpos($buff,'77d')===0) {
						$start_device_send =substr($buff, 2, 2);
					$buff=substr($buff,4,$len);  $send=true; }
					if (strpos($buff,'88d')===0) {
						
						//var_dump($connects);
			
			$start_device =substr($buff, 2, 2);
			$start_time =substr($buff, 4, 8);
			
			$start_time_math=hexdec($start_time)/100;
			
			//echo "UPDATE maraphone_start_time set start_time='".$start_time."', math_start_time='".$start_time_math."' where distance='88' ";
			
			mysql_query("UPDATE maraphone_start_time set start_time='".$start_time."', math_start_time='".$start_time_math."', real_time=now()  where distance='88' and device_id='".$start_device."' ");
			
			$start_time_w='MEGA$'.hex2bin($start_time);
			
			foreach ($connects as $conect_start) {fwrite($conect_start, $start_time_w);}
			
			$buff=substr($buff, 12);
			
		}
		
		if (strpos($buff,'89d')===0) {
			$start_device =substr($buff, 2, 2);
			
			mysql_query("UPDATE maraphone_start_time set start_time='FFFF', math_start_time='0', real_time=now()  where distance='88' and device_id='".$start_device."' ");
			
			foreach ($connects as $conect_start) {fwrite($conect_start, 'MEGA+READY');}
			$buff=substr($buff, 4);
			
		}
		
			if (strpos($buff,'99d')===0) {
		
		$buff_array=explode('99d', $buff);
		
		foreach ($buff_array as $key=>$value) {
			
			$buff='99d'.$value;
				
		$device_id = substr($buff, 2, 2);
		
			
		
		
		if (true) {
			

			$quantity = hexdec(substr($buff, 4, 2));
			if ($quantity>15) $quantity=1;
			
			for ($x_data=0; $x_data<$quantity; $x_data++) {
				
				$time_stamp_pos = 10 + 36*$x_data;
				$rf_id_pos = 18 + 36*$x_data;
				$reader_id_pos=6+36*$x_data;
				$antena_id_pos=8+36*$x_data;
				
				
			
		$time_stamp = hexdec(substr($buff, $time_stamp_pos, 8))/100;

		//$time_stamp=$time_stamp+172800;
		
		$rf_id = substr($buff, $rf_id_pos, 24);
		$rf_id = substr($rf_id, 16, 8);	
		
		//$rf_id=preg_replace('/d/','\d',$rf_id);
		
		$reader_id = substr($buff, $reader_id_pos, 2);	
		$antena_id = substr($buff, $antena_id_pos, 2);
			$part_device_id=$reader_id.'_'.$antena_id;
		
		$real_time =  'now()';
		
		foreach($list as $key => $value){
			
			if (!mysql_ping ($link)) {
						mysql_close($link);
						$link = @mysql_connect(DB_SERVER, DB_SERVER_USERNAME, DB_SERVER_PASSWORD);
						if($link) mysql_select_db(DB_DATABASE); else die('ERROR CONNECT');
						mysql_query("set names utf8mb4");
						} 
			
			//$metka=true;
			$chek_time='';
			//var_dump($rf_id);
			
			
			$start_number='';
			$events_id='';
			
			$start_number_query = mysql_query("(select start_number, events_id from maraphone_partitipants where rf_id like '%".$rf_id."%' and events_id in(select post_id from wp_rhc_events where now()>event_start and now()<event_end ) limit 1)");
			while($start_number_aray=mysql_fetch_array($start_number_query))
				
				{
					$start_number=$start_number_aray['start_number'];
					$events_id=$start_number_aray['events_id'];
					
				}
			
				
			$res = mysql_query("select id, time FROM maraphone_".$value." where device_id='".$device_id."' and start_number='".$start_number."' and  events_id='".$events_id."' order by id desc limit 1");	
			while($check=mysql_fetch_array($res))
				
				{
					$chek_time=end(explode(',',$check['time']));
					$midle_time_id=$check['id'];
				}
				
			$metka=abs($time_stamp-$chek_time);
			//var_dump($start_number);
			//var_dump($metka);
	if ($start_number !='')	{	
if ($metka<10 && $metka != $time_stamp) {
		//echo "UPDATE maraphone_".$value." set time= CONCAT_WS('\,',time,'".$time_stamp."'), real_time=".$real_time.", part_device_id =CONCAT_WS('\,',part_device_id,'".$part_device_id."') where id='".$midle_time_id."'";
		
			mysql_query("UPDATE maraphone_".$value." set time= CONCAT_WS('\,',time,'".$time_stamp."'), real_time=".$real_time.", part_device_id =CONCAT_WS('\,',part_device_id,'".$part_device_id."') where id='".$midle_time_id."'");
			
			
		}
		else {
			
			//echo "INSERT INTO maraphone_".$value." (real_time,start_number,time,device_id,part_device_id,events_id) VALUES (".$real_time.",'".$start_number."','".$time_stamp."','".$device_id."','".$part_device_id."','".$events_id."')";
			
			mysql_query("INSERT INTO maraphone_".$value." (real_time,start_number,time,device_id,part_device_id,events_id) VALUES (".$real_time.",'".$start_number."','".$time_stamp."','".$device_id."','".$part_device_id."','".$events_id."')");
			
		}
		}	
		}
/* var_dump($device_id); 
var_dump($reader_id); 
var_dump($antena_id); 
var_dump($rf_id); 
var_dump($time_stamp); 
var_dump($real_time);  */
			//запись в общую таблицу
			mysql_query("INSERT INTO maraphone_alfa_base (device_id,reader_id,antena_id,rf_id,time_stamp,d_real_time) VALUES ('".$device_id."','".$reader_id."','".$antena_id."','".$rf_id."','".$time_stamp."',".$real_time.")");
			
			
			}
		}
		$today = getdate();
		$out .= $buff . '_'.$today['yday'].'.'.$today['hours'].'.'.$today['minutes'].'.'.$today['seconds'] . PHP_EOL;
		}
		
		}
		
		if ($send) {
			$start_q= mysql_query("select start_time FROM maraphone_start_time where distance='88' and events_id='".$events_id."' limit 1");	
			while($start=mysql_fetch_array($start_q))
				
				{$convert=$start['start_time'];
				}
				//var_dump($convert);
			$start_time='MEGA$'.hex2bin($convert);
			
			fwrite($connect, $start_time);	
			//var_dump($start_time);	
				}
				
			


fwrite($fh, $out);
		//fwrite($connect, '66');
		}
	

		
            
        
        
        //unset($connects[ array_search($connect, $connects) ]);
    }
	


	
	
    
		//$buff = @socket_read($accept, 200);
		//print_r($buff);
		
		//var_dump($buff);
		


	
	}
	fclose($connect);
	fclose($fh);
	//socket_close($socket);
	//fclose($socket);
	//mysql_close($link);
		
?>
