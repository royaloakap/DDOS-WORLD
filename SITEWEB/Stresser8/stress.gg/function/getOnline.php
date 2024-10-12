<?php

require_once("../include/bdd.php");

$getOnline = $pdo->prepare("SELECT * FROM `users_online` WHERE `time`>?");
$getOnline->execute(array(time()-2));

echo $getOnline->rowCount();


?>