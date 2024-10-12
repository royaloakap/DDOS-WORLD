<?php 

	header('Location: login.php');

?>

<?php
 require('waff.php');
 $xWAF = new xWAF();
 $xWAF->start();
?>  