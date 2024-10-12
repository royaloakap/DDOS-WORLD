<?php

	if (!isset($_SERVER['HTTP_REFERER'])){
		die;
	}
	
	ob_start();
	require_once '../../app/config.php';
	require_once '../../app/init.php'; 

	if (!empty($maintaince)){
		die();
	}
	
	if (!($user -> LoggedIn()) || !($user -> notBanned($odb))){
		die();
	}
	$timeleft = $cooldownTime - time();
	$correct = gmdate("H:i:s", $timeleft);
            
	if($cooldown == 1)
	{
		echo '<div class="alert alert-primary"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button><strong>COOLDOWN Enabled:</strong> Please be patient and wait for the cooldown to end. Remaning time: '.$correct.'</div>';
	}
	
	if($correct == "00:00:00")
	{
		$SQL = $odb -> query("UPDATE `settings` SET `cooldown` = '0'");
	}
?>
	
