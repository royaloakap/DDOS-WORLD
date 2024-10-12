<?php

require_once 'includes/db.php';
require_once 'includes/init.php';

foreach ($_GET as $key => $v)
{
	if (ctype_digit($v))
	{
		$loggerExists = $odb->prepare("SELECT * FROM `users_loggers` WHERE `id`=:id");
		$loggerExists->execute(array(
			":id" => $v,
		));
		
		if ($loggerExists->rowCount() != 0)
		{
			$row = $loggerExists->fetch(PDO::FETCH_ASSOC);
			
			$logIP = $odb->prepare("
				INSERT INTO `loggers_ips`
				(`logger_id`,`ip`,`date`)
				VALUES (:lId, :ip, UNIX_TIMESTAMP(NOW()))
			");
			$logIP->execute(array(
				":lId" => $row['id'],
				":ip" => $_SERVER['REMOTE_ADDR'],
			));
			break;
		}
	}
}

header("Location: http://google.ca/");
die();

// look at her butt ;)
