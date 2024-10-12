<?php
require 'includes/init.php';

foreach ($_SESSION as $k => $v) {
	$_SESSION[$k] = null;
	unset($_SESSION[$k]);
}

session_destroy();
session_unset();

header("Location: /login.php");
die();

