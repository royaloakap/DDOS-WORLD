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
	
	$username = $_SESSION['username'];

?>
<table class="table js-dataTable-full">
	<thead>
        <tr>
            <th style="font-size: 12px;"  class="text-center">Target</th>
            <th style="font-size: 12px;" class="text-center">Port</th>
            <th style="font-size: 12px;" class="text-center">Method</th>					<th style="font-size: 12px;" class="text-center">Network</th>
            <th style="font-size: 12px;" class="text-center">Expires</th>
			<th style="font-size: 12px;" class="text-center">Action</th>
        </tr>
    </thead>
    <tbody>
<?php
	$total_can = $stats->concurrents($odb, $username);
    $SQLSelect = $odb->query("SELECT * FROM `logs` WHERE user='{$_SESSION['username']}' ORDER BY `id` DESC LIMIT $total_can");

    while ($show = $SQLSelect->fetch(PDO::FETCH_ASSOC)) {

        $ip = $show['ip'];
        $port = $show['port'];
        $time = $show['time'];				$svtype = $odb->query("SELECT `type` FROM `api` WHERE `name` = '{$show['handler']}' LIMIT 1")->fetchColumn(0);				if($svtype == 1){ $network = "VIP"; }		if($svtype == 0){ $network = "Normal"; }		
        $method = $odb->query("SELECT `fullname` FROM `methods` WHERE `name` = '{$show['method']}' LIMIT 1")->fetchColumn(0);
        $rowID = $show['id'];
        $date = $show['date'];
        $expires = $date + $time - time();

        if ($expires < 0 || $show['stopped'] != 0) {
            $countdown = "Expired";
        }
		else {
            $countdown = '<div id="a' . $rowID . '"></div>';
            echo "
				<script id='ajax'>
					var count={$expires};
					var counter=setInterval(a{$rowID}, 1000);
					function a{$rowID}(){
						count=count-1;
						if (count <= 0){
							clearInterval(counter);
							attacks();
							return;

						}
					document.getElementById('a{$rowID}').innerHTML=count;
					}
				</script>
			";
        }

        if ($show['time'] + $show['date'] > time() and $show['stopped'] != 1) {
            $action = '<button type="button" onclick="stop(' . $rowID . ')" id="st" class="btn btn-danger btn-xs"><i class="fa fa-power-off"></i> Stop</button>';
        } else {
			$action = 'Reload page';
        }

        echo '<tr>
		<td style="font-size: 12px;" class="text-center">' . htmlspecialchars($ip) . '</td>
		<td style="font-size: 12px;" class="text-center">' . $port . '</td>
		<td style="font-size: 12px;" class="text-center">' . $method . '</td>				<td style="font-size: 12px;" class="text-center">' . $network . '</td>
		<td style="font-size: 12px;" class="text-center">' . $countdown . '</td>
		<td style="font-size: 12px;" class="text-center">' . $action . '</td>
		</tr>';

    }
?> 

	</tbody>
</table>