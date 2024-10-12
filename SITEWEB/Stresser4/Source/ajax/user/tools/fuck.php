<?php	error_reporting(0);

	if (!isset($_SERVER['HTTP_REFERER'])){
		die;
	}
	
	ob_start();
	require_once '../../../inc/config.php';
	require_once '../../../inc/init.php';

	if (!empty($maintaince)){
		die();
	}		$userx = $_SESSION['username'];									$oneday = time() - 86400;									$SQL = $odb -> prepare("SELECT COUNT(*) FROM `logs` WHERE `user` LIKE :user AND `date` > :date");									$SQL -> execute(array(":date" => $oneday, ':user' => $userx));									$todayattacks = $SQL->fetchColumn(0);		$ta = $stats -> totalBoots($odb);	$ra = $stats -> runningBoots($odb);	$ts = $stats -> serversonline($odb);	$tu = $stats -> totalUsers($odb);	$my = $todayattacks;	$fck = $_GET['v'];	if($fck == "ta")	{		die($ta);	}		if($fck == "ra")	{		die($ra);	}		if($fck == "ts")	{		die($ts);	}		if($fck == "tu")	{		die($tu);	}		if($fck == "my")	{				if($user -> isAdmin($odb)){			die(''.$my.'/100');		} 				$plansql = $odb -> prepare("SELECT `users`.`expire`, `plans`.`name`, `plans`.`concurrents`, `plans`.`mbt` FROM `users`, `plans` WHERE `plans`.`ID` = `users`.`membership` AND `users`.`ID` = :id");				$plansql -> execute(array(":id" => $_SESSION['ID']));				$row = $plansql -> fetch(); 				$plan_name = $row['name'];					if($plan_name == "Trial Plan")		{			die(''.$my.'/10');		} else {							die(''.$my.'/40');		}	}
?>