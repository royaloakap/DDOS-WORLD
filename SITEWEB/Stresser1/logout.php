<?php

	session_start();
			unset($_SESSION['username']);
			unset($_SESSION['ID']);
			setcookie("username", "", time() + 720000);
			session_destroy();
	header('location: login.php');
	
?>