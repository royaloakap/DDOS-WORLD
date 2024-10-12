<?php
require_once("../include/bdd.php");

$getAttacks=$pdo->prepare("SELECT * FROM `attaques` WHERE `timestamp_end`>? AND `statut`=0 ORDER BY `timestamp` DESC");
$getAttacks->execute(array(time()));
if($getAttacks->rowCount() != 0){
	while($att=$getAttacks->fetch()){
		$diff=intval($att['timestamp_end'])-time();
		$timestamp=$att['timestamp'];
		$timestamp=$timestamp+3600+3600;
		
		echo "
		<tr>
			<td><i class='fad fa-bullseye-arrow'></i> ".substr($att['target'], 0, strlen($att['target'])-8)."...</td>
			<td><i class='fad fa-alarm-clock'></i> ".$diff."s</td>
			<td><i class='fad fa-calendar-week'></i> ".gmdate("H\hi", $timestamp)."</td>
			<td><a href='boutique'><span class='buttonb feed'><i class='fad fa-browser'></i></span></a></td>
		</tr>
		
		";
	}
} else {
	echo "<tr>
		<td><center><i class='fad fa-times fa-2x'></i> Aucune attaque en cours</center></td>
	</tr>";
}

?>