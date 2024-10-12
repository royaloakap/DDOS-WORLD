<?php
		if (isset($_POST['update']))
		{
			$updateName = $_POST['nameAdd'];
			$updateUnit = $_POST['unit'];
			$updateLength = $_POST['lengthAdd'];
			$updateMbt = intval($_POST['mbt']);
			$updatePrice = floatval($_POST['price']);
			$updateconcurrents = $_POST['concurrents'];
			$updateprivate = $_POST['private'];
			
			if (empty($updatePrice) || empty($updateName) || empty($updateUnit) || empty($updateLength) || empty($updateMbt) || empty($updateconcurrents))
			{
				$error = 'Fill in all fields';
			}
			if (empty($error))
			{
				$SQLinsert = $odb -> prepare("UPDATE `plans` SET `name` = :name, `mbt` = :mbt, `unit` = :unit, `length` = :length, `price` = :price, `concurrents` = :concurrents, `private` = :private WHERE `ID` = :id");
				$SQLinsert -> execute(array(':name' => $updateName, ':mbt' => $updateMbt, ':unit' => $updateUnit, ':length' => $updateLength, ':price' => $updatePrice, ':concurrents' => $updateconcurrents, ':private' => $updateprivate, ':id' => $_GET['id']));
				echo success('Plan has been updated <meta http-equiv="refresh" content="3;url=pmanager.php">');
				$currentName = $updateName;
				$currentUnit = $updateUnit;
				$currentMbt = $updateMbt;
				$currentPrice = $updatePrice;
				$currentLength = $updateLength;
				$currentconcurrents = $updateconcurrents;
				$currentprivate = $updateprivate;
			}
			else
			{
				echo error($error);
			}
		}
?>