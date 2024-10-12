<?php

	ob_start(); 
	require_once '../../../inc/config.php';
	require_once '../../../inc/init.php'; 

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
            <th width="30%"><div class="text-center">Logged IP</div></th>
            <th><div class="text-center">Date</div></th>
            <th><div class="text-center">Actions</div></th>
        </tr>
    </thead>
    <tbody>
<?php

    $SQLSelect = $odb->prepare("SELECT * FROM `iplogs` WHERE `userID` = :id ORDER BY `date` DESC LIMIT 5");
	$SQLSelect->execute(array(':id' => $userid));
    while ($show = $SQLSelect->fetch(PDO::FETCH_ASSOC)) {

        $id = $show['ID'];
		$loggedIP = $show['logged'];
		$date = date("m-d-Y, H:i:s", $show['date']);

		echo '<tr>
				<td><div class="text-center"><a>' . $loggedIP . '</a></div></td>
				<td><div class="text-center">' . $date . '</div></td>
				<td><div class="text-center">
					<button name="delete" onclick="removeip(' . $id . ')" class="btn btn-danger">
						<i class="fa fa-trash-o"></i>
					</button>
				</div></td>
			  </tr>';

    }
?> 

	</tbody>
</table>