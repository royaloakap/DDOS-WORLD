<?php

header("X-XSS-Protection: 1; mode=block");

	if (!isset($_SERVER['HTTP_REFERER'])){
		die;
	}
	
	ob_start();
	require_once '../../../avg/mycon.php';
	require_once '../../../avg/usv.php'; 

	if (!empty($maintaince)){
		die();
	}
	
	if (!($user -> LoggedIn()) || !($user -> notBanned($odb))){
		die();
	}

	if (empty($_GET['id'])){
		die(error('You need to enter a reply'));
	}
	
	$SQLGetMessages = $odb -> prepare("SELECT * FROM `messages` WHERE `ticketid` = :ticketid ORDER BY `messageid` ASC");
	$SQLGetMessages -> execute(array(':ticketid' => $_GET['id']));
	while ($show = $SQLGetMessages -> fetch(PDO::FETCH_ASSOC)){
		$class = "";
		if($show['sender'] == "Admin"){
			$class = 'class="blockquote-reverse"';
			$username = 'Administrator';
		}
		echo '
			<blockquote '. $class .'>
			<h4 class="header-title">Message: </h4><p>
				<th>'. $show['content'] .'</th>
				<footer>- '. $show['sender'] .' [ '. date('d/m/Y, h:i:s a', $show['date']) .' ]</font></footer>
			</blockquote>
		';
	}
	
?>