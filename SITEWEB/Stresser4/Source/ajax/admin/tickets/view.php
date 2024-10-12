<?php
	
	ob_start();
	require_once '../../../inc/config.php';
	require_once '../../../inc/init.php';
	
	if (!isset($_SERVER['HTTP_REFERER']) || !($user -> LoggedIn()) || !($user -> notBanned($odb)) || empty($_GET['id']) || !($user -> isSupporter($odb))){
		die();
	}
	
	$SQLGetMessages = $odb -> prepare("SELECT * FROM `messages` WHERE `ticketid` = :ticketid ORDER BY `messageid` ASC");
	$SQLGetMessages -> execute(array(':ticketid' => $_GET['id']));
	while ($show = $SQLGetMessages -> fetch(PDO::FETCH_ASSOC)){
		$class = "";
		if($show['sender'] == "Admin"){
			$class = 'class="blockquote-reverse"';
			$username = 'Administrator';
		}
		if ($user -> safeString($show['content'])){
			die(error('Unsafe characters were set'));
		}
		echo '
			<blockquote '. $class .'>
				<h5>'. $show['content'] .'</h5>
				<footer>'. $show['sender'] .' [ '. date('d-m-Y h:i:s a', $show['date']) .' ]</footer>
			</blockquote>
		';
	}
	
?>