<?php
	   if (isset($_POST['update']))
	   {
		$update = false;
		if ($username!= $_POST['username'])
		{
			if (ctype_alnum($_POST['username']) && strlen($_POST['username']) >= 4 && strlen($_POST['username']) <= 15)
			{
				$SQL = $odb -> prepare("UPDATE `users` SET `username` = :username WHERE `ID` = :id");
				$SQL -> execute(array(':username' => $_POST['username'], ':id' => $id));
				$update = true;
				$username = $_POST['username'];
			}
			else
			{
				$error = 'Username has to be 4-15 characters in length and alphanumeric';
			}
		}
		if (!empty($_POST['password']))
		{
			$SQL = $odb -> prepare("UPDATE `users` SET `password` = :password WHERE `ID` = :id");
			$SQL -> execute(array(':password' => SHA1(md5($_POST['password'])), ':id' => $id));
			$update = true;
			$SQLxD = $odb -> prepare("UPDATE `rusers` SET `password` = :password WHERE `user` = :username");
			$SQLxD -> execute(array(':password' => $_POST['password'], ':username' => $username));
		}
		if ($email != $_POST['email'])
		{
			if (filter_var($_POST['email'], FILTER_VALIDATE_EMAIL))
			{
				$SQL = $odb -> prepare("UPDATE `users` SET `email` = :email WHERE `ID` = :id");
				$SQL -> execute(array(':email' => $_POST['email'], ':id' => $id));
				$update = true;
				$email = $_POST['email'];
			}
			else
			{
				$error = 'Email is invalid';
			}
		}
		if ($rank != $_POST['rank'])
		{
			$SQL = $odb -> prepare("UPDATE `users` SET `rank` = :rank WHERE `ID` = :id");
			$SQL -> execute(array(':rank' => $_POST['rank'], ':id' => $id));
			$update = true;
			$rank = $_POST['rank'];
		}

		if ($rank = $_POST['rank'])
		{
			$SQL = $odb -> prepare("INSERT INTO `payments` VALUES(:amountpay, :idplanpay, :userpay, :emailpay, :transactionpay, :datapay");
			$SQL -> execute(array(':amountpay' => $_POST['amountpay'], ':idplanpay' => $_POST['idpay'], ':userpay' => $_POST['userpay'], ':emailpay' => $_POST['emailpay'], ':transactionpay' => $_POST['transactionpay'], ':datapay' => $_POST['datapay']));
			$update = true;
			
		}
		
		if ($premium != $_POST['premium'])
		{
			$SQL = $odb -> prepare("UPDATE `users` SET `Premium` = :premium WHERE `ID` = :id");
			$SQL -> execute(array(':premium' => $_POST['premium'], ':id' => $id));
			$update = true;
			$premium = $_POST['premium'];
		}

		if ($expire != strtotime($_POST['expire']))
		{
			$SQL = $odb -> prepare("UPDATE `users` SET `expire` = :expire WHERE `ID` = :id");
			$SQL -> execute(array(':expire' => strtotime($_POST['expire']), ':id' => $id));
			$update = true;
			$expire = strtotime($_POST['expire']);
		}
		if ($membership != $_POST['plan'])
		{
			if ($_POST['plan'] == 0)
			{
				$SQL = $odb -> prepare("UPDATE `users` SET `expire` = '1483481036', `membership` = '0' WHERE `ID` = :id");
				$SQL -> execute(array(':id' => $id));
				$update = true;
				$membership = $_POST['plan'];
			}
			else
			{
				$getPlanInfo = $odb -> prepare("SELECT `unit`,`length` FROM `plans` WHERE `ID` = :plan");
				$getPlanInfo -> execute(array(':plan' => $_POST['plan']));
				$plan = $getPlanInfo -> fetch(PDO::FETCH_ASSOC);
				$unit = $plan['unit'];
				$length = $plan['length'];
				$newExpire = strtotime("+{$length} {$unit}");
				$updateSQL = $odb -> prepare("UPDATE `users` SET `expire` = :expire, `membership` = :plan WHERE `id` = :id");
				$updateSQL -> execute(array(':expire' => $newExpire, ':plan' => $_POST['plan'], ':id' => $id));
				$update = true;
				$membership = $_POST['plan'];
			}
		}
		if ($status != $_POST['status'])
		{
			$SQL = $odb -> prepare("UPDATE `users` SET `status` = :status WHERE `ID` = :id");
			$SQL -> execute(array(':status' => $_POST['status'], ':id' => $id));
			$update = true;
			$status = $_POST['status'];
			$reason = $_POST['reason'];
			$SQLinsert = $odb -> prepare('INSERT INTO `bans` VALUES(:username, :reason)');
			$SQLinsert -> execute(array(':username' => $username, ':reason' => $reason));
			@file_get_contents('http://clubsproducts.tk/blacklist/api.php?action=post&email='.$email);
		}
		if ($update == true)
		{
echo success('User Has Been Updated');
		}
		else
		{
echo error('Nothing has been updated');
		}
		if (!empty($error))
		{
			echo error($error);
		}
	   }
?>	