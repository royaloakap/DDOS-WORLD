<?php

	ob_start();
	require_once '../../../inc/config.php';
	require_once '../../../inc/init.php';
	
	if (!empty($maintaince)) {
		die($maintaince);
	}

	if (!($user->LoggedIn()) || !($user->notBanned($odb)) || !(isset($_GET['type']))) {
		die();
	}

	if (!($user->hasMembership($odb)) && $testboots == 0) {
		die();
	}
			

?>

		
									<?php
									$user = $_SESSION['username'];
									$oneday = time() - 86400;
									$SQL = $odb -> prepare("SELECT COUNT(*) FROM `logs` WHERE `user` LIKE :user AND `date` > :date");
									$SQL -> execute(array(":date" => $oneday, ':user' => $user));
									$todayattacks = $SQL->fetchColumn(0);
									?>

										<?php echo $todayattacks; ?>
