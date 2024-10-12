<?php
include_once 'inc.php';
if(!$ST->optionExisit('lastbackup')){
	$ST->AddOption('lastbackup',1);
	$ST->Add();
}

function backup_tables($host,$user,$pass,$name,$tables = '*')
{
	
	$link = mysql_connect($host,$user,$pass);
	mysql_query("set character_set_server='utf8'");
	mysql_query("set names 'utf8'");
	mysql_select_db($name,$link);
	if($tables == '*')
	{
		$tables = array();
		$result = mysql_query('SHOW TABLES');
		while($row = mysql_fetch_row($result))
		{
			$tables[] = $row[0];
		}
	}
	else
	{
		$tables = is_array($tables) ? $tables : explode(',',$tables);
	}
	
	//cycle through
	foreach($tables as $table)
	{
		$result = mysql_query('SELECT * FROM '.$table);		
		$num_fields = mysql_num_fields($result);
		
		$return.= 'DROP TABLE IF EXISTS '.$table.';';
		$row2 = mysql_fetch_row(mysql_query('SHOW CREATE TABLE '.$table));
		$return.= "\n\n".$row2[1].";\n\n";
		
		for ($i = 0; $i < $num_fields; $i++) 
		{
			while($row = mysql_fetch_row($result))
			{
				$return.= 'INSERT INTO '.$table.' VALUES(';
				for($j=0; $j<$num_fields; $j++) 
				{
					$row[$j] = addslashes($row[$j]);
					$row[$j] = ereg_replace("\n","\\n",$row[$j]);
					if (isset($row[$j])) { $return.= '"'.$row[$j].'"' ; } else { $return.= '""'; }
					if ($j<($num_fields-1)) { $return.= ','; }
				}
				$return.= ");\n";
			}
		}
		$return.="\n\n\n";
	}
	return $return;
}
$last = $ST->get('lastbackup');
if($last<(time()-(60*60*24))){
	$Bk =  backup_tables('localhost',$DBUSER,$DBPASS,$DBNAME); 
	header('Content-Disposition: attachment; filename="hloun_backup_'.date('d-m-y',time()).'.sql"');
	header("Content-type: application/octet-stream; charset=UTF-8");
	header('Content-Length: ' . strlen($Bk));
	header('Connection: close');
	echo $Bk;	
	$ST->updateOption('lastbackup',time());
	$ST->update();
}else{
	$calc = ($last+(60*60*24)) - time() ;
	$msg = "";
	if($calc<60)
		$msg = $calc ." sec";
	else if($calc>=60 && $calc <3600){
		$msg = $calc/60 ." min";
	}else if($calc>=3600){
		$msg = ceil($calc/3600) . " hours";
	}
	echo "<h2>Sorry You Can backup once everyday come back after  ".$msg."</h2>";
}


?>
