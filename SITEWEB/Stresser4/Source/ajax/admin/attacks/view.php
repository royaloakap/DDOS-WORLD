<?php

	ob_start(); 
	require_once '../../../inc/config.php';
	require_once '../../../inc/init.php'; 

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
			   <th style="font-size: 12px;" class="text-center">Server</th>
			   <th style="font-size: 12px;" class="text-center">Network</th>
            <th style="font-size: 12px;" class="text-center">Expires</th>
			<th style="font-size: 12px;" class="text-center">Stop</th>
        </tr>
    </thead>
    <tbody>
<?php
    $SQLSelect = $odb->query("SELECT * FROM `logs` WHERE `time` + `date` > UNIX_TIMESTAMP() AND `stopped` = 0 ORDER BY `id` DESC");
    while ($show = $SQLSelect->fetch(PDO::FETCH_ASSOC)) {
        $method  = $odb->query("SELECT `fullname` FROM `methods` WHERE `name` = '{$show['method']}' LIMIT 1")->fetchColumn(0);
        $rowID   = $show['id'];
        $expires = $show['date'] + $show['time'] - time();
		$countdown = '<div id="a' . $rowID . '"></div>';
        echo '			<tr style="font-size: 12px;" class="text-center">
				<td>' . $show['user'] . '</td>
				<td>' . htmlspecialchars($show['ip']) . ':'.$show['port'] . '</td>
				<td>' . $method . '</td>
				<td>'.$show['handler'] . '</td>
				<td>'.$show['network'] . '</td>
				<td id="a' . $rowID . '"></td>
				<td><button type="button" onclick="stop(' . $rowID . ')" id="st" class="btn btn-danger btn-xs"><i class="fa fa-power-off"></i> Stop</button></td>
			</tr>		';	
	}
?> 

	</tbody>
</table><script id="ajax"><?php	$SQLSelect = $odb->query("SELECT * FROM `logs` WHERE `time` + `date` > UNIX_TIMESTAMP() AND `stopped` = 0 ORDER BY `id` DESC");    while ($show = $SQLSelect->fetch(PDO::FETCH_ASSOC)) {        $rowID   = $show['id'];        $expires = $show['date'] + $show['time'] - time();		echo '			var a'.$rowID.' = setInterval(a' . $rowID . ', 1000);			var c'.$rowID.' = '.$expires.';			function a' . $rowID . '(){				c'.$rowID.'=c'.$rowID.'-1;				if (c'.$rowID.' <= 0){					clearInterval(a'.$rowID.');					adminattacks();				}				document.getElementById("a' . $rowID . '").innerHTML=c'.$rowID.';			}		';	}?></script>