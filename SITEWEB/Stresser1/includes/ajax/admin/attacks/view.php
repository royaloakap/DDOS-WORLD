<?php

	ob_start(); 
	require_once '../../../app/config.php'; 
	require_once '../../../app/init.php';  

	if (!($user->LoggedIn()) || !($user->notBanned($odb)) || !($user -> isAdmin($odb)) || !(isset($_SERVER['HTTP_REFERER']))) {
		die();
	}
	
?>
<table class="table">
	<thead>
        <tr>
            <th style="font-size: 12px;" class="text-center">User</th>
            <th style="font-size: 12px;" class="text-center">Target</th>
            <th style="font-size: 12px;" class="text-center">Method</th>
            <th style="font-size: 12px;" class="text-center">Expires</th>
			<th style="font-size: 12px;" class="text-center">Stop</th>
        </tr>
    </thead>
    <tbody>
<?php
    $SQLSelect = $odb->query("SELECT * FROM `logs` WHERE `time` + `date` > UNIX_TIMESTAMP() AND `stopped` = 0 ORDER BY `id` DESC");
    while ($show = $SQLSelect->fetch(PDO::FETCH_ASSOC)) {
        $user      = $show['user'];
        $ip      = $show['ip'];
        $port    = $show['port'];
        $time    = $show['time'];
        $method  = $odb->query("SELECT `fullname` FROM `methods` WHERE `name` = '{$show['method']}' LIMIT 1")->fetchColumn(0);
        $rowID   = $show['id'];
        $date    = $show['date'];
        $expires = $date + $time - time();
		$countdown = '<div id="a' . $rowID . '"></div>';
        echo '
		<script id="ajax">
			var count=' . $expires . ';
			var counter=setInterval(a' . $rowID . ', 1000);
			function a' . $rowID . '(){
				count=count-1;
				if (count <= 0){
					clearInterval(counter);
					adminattacks();
					return;
				}				
				document.getElementById("a' . $rowID . '").innerHTML=count;
			}
		</script>';
        $action = '<button type="button" onclick="stop(' . $rowID . ')" id="st" class="btn btn-danger btn-xs"><i class="fa fa-power-off"></i> Stop</button>';
        echo 	'<tr style="font-size: 12px;" class="text-center">
					<td>' . $user . '</td>
					<td>' . htmlspecialchars($ip) . ':'.$port . '</td>
					<td>' . $method . '</td>
					<td>' . $countdown . '</td>
					<td>' . $action . '</td>
				</tr>';
	}
?> 

	</tbody>
</table>