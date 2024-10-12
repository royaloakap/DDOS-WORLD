<?php
$conn = mysqli_connect("localhost", "root", "", "stressing");
$pdo=new PDO('mysql:host=localhost;dbname=stressing', "root", "");
$pdo->exec("set names utf8");
if (!$conn) {
    echo "Erreur : Impossible de se connecter à MySQL." . PHP_EOL;
    echo "Errno de débogage : " . mysqli_connect_errno() . PHP_EOL;
    echo "Erreur de débogage : " . mysqli_connect_error() . PHP_EOL;
    exit;
}

$conn->set_charset("utf8");


?>