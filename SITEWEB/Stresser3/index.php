<?php 

    header("X-XSS-Protection: 1; mode=block");
	header('Location: /panel/login.php');
	
?>

<?php
 require('/panel/waff.php');
 $xWAF = new xWAF();
 $xWAF->start();
?>  