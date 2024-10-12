<?php

error_reporting(0);
ini_set("display_errors", "Off");

class user
{

	public function fetchAllowedMethods()
	{
		global $odb;
		$fetchMethods = $odb->prepare("
			SELECT `b`.*
			FROM `users` AS `u`
			
			LEFT JOIN `plans` AS `p`
			ON `p`.`id` = `u`.`membership`
			
			LEFT JOIN `boot_methods` AS `b`
			ON FIND_IN_SET(`b`.`method` , `p`.`allowed_methods`) >0
			
			WHERE
				`u`.`id` = :id
				AND `b`.`active` = '1'
		");
		$fetchMethods->execute(array(
			":id" => $_SESSION['ID'],
		));
		if ($fetchMethods->rowCount() != 0) {
			return $fetchMethods->fetchAll(PDO::FETCH_ASSOC);
		}
	}

	public function fetchUserInfo() {
		global $odb;
		$qry = $odb->prepare("SELECT * FROM `users` WHERE `ID` = :uid LIMIT 1;");
		$qry->execute(array(":uid"=>$_SESSION['ID']));
		if ($qry->rowCount() != 0) return $qry->fetch(PDO::FETCH_ASSOC);
	}

	function isAdmin($odb)
	{
		$SQL = $odb -> prepare("SELECT `rank` FROM `users` WHERE `ID` = :id");
		$SQL -> execute(array(':id' => $_SESSION['ID']));
		$rank = $SQL -> fetchColumn(0);
		if ($rank == 1)
		{
			return true;
		}
		else
		{
			return false;
		}
	}
	function LoggedIn()
	{
		if (isset($_SESSION['username'], $_SESSION['ID']))
		{
			return true;
		}
		else
		{
			return false;
		}
	}
	function hasMembership($odb)
	{
		$SQL = $odb -> prepare("SELECT `expire` FROM `users` WHERE `ID` = :id");
		$SQL -> execute(array(':id' => $_SESSION['ID']));
		$expire = $SQL -> fetchColumn(0);
		if (time() < $expire)
		{
			return true;
		}
		else
		{
			$SQLupdate = $odb -> prepare("UPDATE `users` SET `membership` = 0 WHERE `ID` = :id");
			$SQLupdate -> execute(array(':id' => $_SESSION['ID']));
			return false;
		}
	}
	
	
	
	
	
	
	
	function hasMembership2($odb)
	{
		$SQL = $odb -> prepare("SELECT `layer4` FROM `users` WHERE `ID` = :id");
		$SQL -> execute(array(':id' => $_SESSION['ID']));
		$acces = $SQL -> fetchColumn(0);
		if ($acces == 1)
		{
			return true;
		}
		else
		{
			return false;
		}
	}
	
	
	
	
		function notBanned($odb)
	{
		$SQL = $odb -> prepare("SELECT `status` FROM `users` WHERE `ID` = :id");
		$SQL -> execute(array(':id' => $_SESSION['ID']));
		$result = $SQL -> fetchColumn(0);
		if ($result == 0)
		{
			return true;
		}
		else
		{
			session_destroy();
			return false;
		}
	}
}
class stats
{
	function totalUsers($odb)
	{
		$SQL = $odb -> query("SELECT COUNT(*) FROM `users`");
		return $SQL->fetchColumn(0);
	}
	function onlineServers($odb)
	{
		$SQL = $odb -> query("SELECT COUNT(*) FROM `servers`");
		return $SQL->fetchColumn(0);
	}
		function onlineServers1($odb)
	{
		$SQL = $odb -> query("SELECT COUNT(*) FROM `servers_layer4`");
		return $SQL->fetchColumn(0);
	}
	function attackMethhods($odb)
	{
		$SQL = $odb -> query("SELECT COUNT(*) FROM `boot_methods`");
		return $SQL->fetchColumn(0);
	}
	function totalBoots($odb)
	{
		$SQL = $odb -> query("SELECT COUNT(*) FROM `logs`");
		return $SQL->fetchColumn(0);
	}
	function runningBoots($odb)
	{
		$SQL = $odb -> query("SELECT COUNT(*) FROM `logs` WHERE `time` + `date` > UNIX_TIMESTAMP() AND `stopped` = 0 ");
		return $SQL->fetchColumn(0);
	}
	function totalBootsForUser($odb, $user)
	{
		$SQL = $odb -> prepare("SELECT COUNT(*) FROM `logs` WHERE `user` = :user");
		$SQL -> execute(array(":user" => $user));
		return $SQL->fetchColumn(0);
	}
}

/**
* Recursive array search
*
* @access	public
* @param	mixed $needle The value to be searched
* @param	array $haystack The array
* @return	mixed Returns the key for the needle if found, false if not found
* @since	0.3
*/
function arraySearchRecursive($needle, $haystack) {
	foreach ($haystack as $key => $value) {
		if ($needle === $value || (is_array($value) &&
			arraySearchRecursive($needle, $value) !== false))
		return $key;
	}
	return false;
}


function timeElapsedFromUNIX($time){
		$since = time() - $time;
		
		$chunks = array(
			array(31536000, "year"),
			array(2592000, "month"),
			array(604800, "week"),
			array(86400, "day"),
			array(3600, "hour"),
			array(60, "minute"),
			array(1, "second")
		);

		for ($i = 0, $j = count($chunks); $i < $j; $i++)
		{
			$seconds = $chunks[$i][0];
			$name = $chunks[$i][1];
			if (($count = floor($since / $seconds)) != 0)
			{
				break;
			}
		}

		$print = ($count == 1) ? '1 ' . $name : $count . ' ' . $name . 's';

		if ($time >= time())
			return "Moments ago";

		return $print." ago";	
	}
	
	function getUsersOpenedTickets()
	{
		global $odb;
		$countOpenTickets = $odb->prepare("SELECT COUNT(*) AS `total` FROM `tickets` WHERE `username`=:user AND `status`='Waiting for User response.'");
		$countOpenTickets->execute(array(":user" => $_SESSION['username']));
		return $countOpenTickets->fetchColumn(0);
	}
