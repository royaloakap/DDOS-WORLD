<?php
session_start();

require_once("../include/bdd.php");

if(isset($_SESSION['id'])){
	$getAttacks=$pdo->prepare("SELECT * FROM `attaques` WHERE `userid`=? AND `timestamp_end`>? AND `statut`=0 ORDER BY `timestamp` DESC");
	$getAttacks->execute(array($_SESSION['id'], time()));
	if($getAttacks->rowCount() != 0){
		while($att=$getAttacks->fetch()){
			$diff=intval($att['timestamp_end'])-time();
			$timestamp=$att['timestamp'];
			$timestamp=$timestamp+3600+3600;
			
			echo "
			<tr>
				<td><i class='fad fa-bullseye-arrow'></i> ".$att['target']."</td>
				<td><i class='fad fa-alarm-clock'></i> ".$diff."s</td>
				<td><i class='fad fa-calendar-week'></i> ".gmdate("H\hi", $timestamp)."</td>
				<td><a onclick='stopAttack(\"".$att['id']."\")'><span class='buttonb feed'><i style='color:red' class='fas fa-power-off'></i></span></a></td>
			</tr>
			
			";
		}
	} else {
		echo "<tr>
			<td><center><i class='fad fa-times fa-2x'></i> Aucune attaque en cours</center></td>
		</tr>";
	}
}

?>