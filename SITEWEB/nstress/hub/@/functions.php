<?php
	function get_tiny_url($url)
	{
		$ch = curl_init();  
		$timeout = 5;  
		curl_setopt($ch,CURLOPT_URL,'http://tinyurl.com/api-create.php?url='.$url);  
		curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);  
		curl_setopt($ch,CURLOPT_CONNECTTIMEOUT,$timeout);  
		$data = curl_exec($ch);  
		curl_close($ch);  
		return $data;  
	}
	function bitcoin($id, $key, $bitcoin, $siteurl)
	{
		$my_callback_url = $siteurl.'gateway/bitcoinipn.php?id='.$id.'&userid='.$_SESSION['ID'].'&key='.$key;
		$root_url = 'https://blockchain.info/api/receive';
		$parameters = 'method=create&address=' . $bitcoin .'&anonymous=false&shared=false&callback='. urlencode($my_callback_url);
		$response = @file_get_contents($root_url . '?' . $parameters);
		$object = json_decode($response);
		return $object->input_address;
	}
	function checkSession($odb)
	{
		if ($_SERVER['REMOTE_ADDR'] != $odb->query("SELECT `ip` FROM `loginlogs` WHERE `username` = '{$_SESSION['username']}'")->fetchColumn(0))
		{
			unset($_SESSION['username']);
			unset($_SESSION['ID']);
			session_destroy();
			header('location: login.php');
		}
	}
class user
{
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
	function isVIP($odb)
	{
		$SQL = $odb -> prepare("SELECT `rank` FROM `users` WHERE `ID` = :id");
		$SQL -> execute(array(':id' => $_SESSION['ID']));
		$rank = $SQL -> fetchColumn(0);
		if ($rank == 3)
		{
			return true;
		}
		else
		{
			return false;
		}
	}
	function isPremium($odb)
	{
		$SQL = $odb -> prepare("SELECT `Premium` FROM `users` WHERE `ID` = :id");
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
	function isSupporter($odb)
	{
		$SQL = $odb -> prepare("SELECT `rank` FROM `users` WHERE `ID` = :id");
		$SQL -> execute(array(':id' => $_SESSION['ID']));
		$rank = $SQL -> fetchColumn(0);
		if ($rank > 0)
		{
			return true;
		}
		else
		{
			return false;
		}
	}
	function availableuser($odb, $user)
	{
		$SQL = $odb -> prepare("SELECT COUNT(*) FROM `users` WHERE `username` = :username");
		$SQL -> execute(array(':username' => $user));
		$count = $SQL -> fetchColumn(0);
		if ($count == 1)
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
		@session_start();
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
	
	function safeString($string)
	{
		$parameters = array("<script", "alert(", "<iframe", ".css", ".js", "<meta", ">", "*", ";", "<", "<frame", "<img", "<embed", "<xml", "<IMG", "<SCRIPT", "<IFRAME", "<META", "<FRAME", "<EMBED", "<XML");
		foreach ($parameters as $parameter)
		{
			if (strpos($string,$parameter) !== false)
			{
				return true;
			}
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
	function activeUsers($odb)
	{
		$SQL = $odb -> query("SELECT COUNT(*) FROM `users` WHERE `expire` > UNIX_TIMESTAMP()");
		return $SQL->fetchColumn(0);
	}
	function referrals($odb, $user)
	{
		$SQL = $odb -> prepare("SELECT COUNT(*) FROM `users` WHERE `referral` = :username");
		$SQL -> execute(array(':username' => $user));
		return $SQL->fetchColumn(0);
	}
	function referralbalance($odb, $user)
	{
		$SQL = $odb -> prepare("SELECT `referralbalance` FROM `users` WHERE `username` = :username");
		$SQL -> execute(array(':username' => $user));
		return $SQL->fetchColumn(0);
	}
	function totalBoots($odb)
	{
		$SQL = $odb -> query("SELECT COUNT(*) FROM `logs`");
		return $SQL->fetchColumn(0);
	}
	function runningBoots($odb)
	{
		$SQL = $odb -> query("SELECT COUNT(*) FROM `logs` WHERE `time` + `date` > UNIX_TIMESTAMP() AND `stopped` = 0");
		return $SQL->fetchColumn(0);
	}
	function concurrents($odb)
	{
		$SQL = $odb -> prepare("SELECT `plans`.`concurrents` FROM `plans` LEFT JOIN `users` ON `users`.`membership` = `plans`.`ID` WHERE `users`.`ID` = :id");
		$SQL -> execute(array(':id' => $_SESSION['ID']));
		return $SQL->fetchColumn(0);
	}
	function countRunning($odb, $user)
	{
		$SQL = $odb -> prepare("SELECT COUNT(*) FROM `logs` WHERE `user` = :username  AND `time` + `date` > UNIX_TIMESTAMP() AND `stopped` = 0");
		$SQL -> execute(array(':username' => $user));
		return $SQL->fetchColumn(0);
	}
	function totalBootsForUser($odb, $user)
	{
		$SQL = $odb -> prepare("SELECT COUNT(*) FROM `logs` WHERE `user` = :user");
		$SQL -> execute(array(':user' => $user));
		return $SQL->fetchColumn(0);
	}
	function purchases($odb)
	{
		$SQL = $odb -> prepare("SELECT COUNT(*) FROM `payments` WHERE `user` = :id");
		$SQL -> execute(array(':id' => $_SESSION['ID']));
		return $SQL->fetchColumn(0);
	}
	function serversonline($odb)
	{
		$SQL = $odb -> query("SELECT COUNT(*) FROM `api`");
		return $SQL->fetchColumn(0);
	}
	function tickets($odb)
	{
		$SQL = $odb -> prepare("SELECT * FROM `tickets` WHERE `username` = :username AND `status` = 'Waiting for user response' ORDER BY `id` DESC");
		$SQL -> execute(array(':username' => $_SESSION['username']));
		return $SQL->fetchColumn(0);
	}
	function admintickets($odb)
	{
		$SQL = $odb -> query("SELECT COUNT(*) FROM `tickets` WHERE `status` = 'Waiting for admin response'");
		return $SQL->fetchColumn(0);
	}
	function usersforplan($odb, $plan)
	{
		$SQL = $odb -> prepare("SELECT COUNT(*) FROM `users` WHERE `membership` = :membership");
		$SQL -> execute(array(":membership" => $plan));
		return $SQL->fetchColumn(0);
	}
}
?>
