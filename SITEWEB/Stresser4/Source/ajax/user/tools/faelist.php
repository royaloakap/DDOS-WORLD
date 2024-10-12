<?php

	ob_start(); 
	require_once '../../../inc/config.php';	require_once '../../../inc/init.php';

	if (!empty($maintaince)) {
		die($maintaince);
	}

	if (!($user->LoggedIn()) || !($user->notBanned($odb)) || !(isset($_SERVER['HTTP_REFERER']))) {
		die();
	}

	if (!($user->hasMembership($odb)) && $testboots == 0) {
		die();
	}
	
	$userid = $_SESSION['ID'];

?>
<table class="table">
	<thead>
        <tr>
            <th width="30%"><div class="text-center">IP</div></th>
            <th><div class="text-center">Type</div></th>
            <th><div class="text-center">Actions</div></th>
        </tr>
    </thead>
    <tbody>
<?php
	$SQLSelect = $odb->prepare("SELECT * FROM `fe` WHERE `userID` = :user ORDER BY `ID` DESC");
	$SQLSelect->execute(array(':user' => $_SESSION['ID']));
	while ($show = $SQLSelect->fetch(PDO::FETCH_ASSOC)) {
		$ipShow = $show['ip'];
		$id     = $show['ID'];
		$type   = ($show['type'] == 'f') ? 'Friend' : 'Enemy';
		echo '<tr>
				<td><div class="text-center"><strong>' . htmlentities($ipShow) . '</strong></div></td>
				<td><div class="text-center">' . $type . '</div></td>
				<td><div class="text-center"><button onclick="removefae(' . $id . ')" class="btn btn-danger"><i class="fa fa-trash-o"></i></button></div></td>
			  </tr>';
	}
?>

	</tbody>
</table>