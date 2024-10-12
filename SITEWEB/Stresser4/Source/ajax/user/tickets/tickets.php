<?php



	if (!isset($_SERVER['HTTP_REFERER'])){

		die;

	}

	

	ob_start();

	require_once '../../../inc/config.php';

	require_once '../../../inc/init.php';



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

				<h5>'. $show['content'] .'</h5>

				<footer>'. $show['sender'] .' [ '. date('d-m-Y h:i:s a', $show['date']) .' ]</footer>

			</blockquote>

		';

	}

	

?>