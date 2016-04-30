<?php
$script_array=array('tcp_server.php', 'db_server.php');



// $script_name = 'tcp_server.php'; 


function serch($script_name)
{
unset($output);
exec ( 'ps ax| grep '.$script_name , $output, $retval);

//var_dump($output);

foreach ($output as $process_id => $prosess) {
	//var_dump($prosess);
	if (strpos($prosess,'/www/'.$script_name)) {
	$pid=explode(' ',trim($prosess));
	
	
	return $pid[0];
	}
}
	
	return false;

	
}

foreach ($script_array as $script_names) {



$process_id=serch($script_names);
 if ($process_id=== false){
//echo 'start'; 
pclose(popen( 'php /www/'.$script_names.'&', 'r'));

 mail('mail@mail.com', 'Process started!', 'Process started '.$script_names.'!','From: mail@mail.com'); } 

	
}


?>