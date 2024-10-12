<?php

	ob_start(); 
	require_once '../../../app/config.php';
	require_once '../../../app/init.php'; 

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
<table class="table table-bordered">
	<thead>
        <tr>
            <th style="font-size: 12px;"  class="text-center">Цель</th>
            <th style="font-size: 12px;" class="text-center">Порт</th>
            <th style="font-size: 12px;" class="text-center">Метод</th>
            <th style="font-size: 12px;" class="text-center">Время</th>
			<th style="font-size: 12px;" class="text-center">Действие</th>
        </tr>
    </thead>
    <tbody>
<?php

    $SQLSelect = $odb->query("SELECT * FROM `logs` WHERE user='{$_SESSION['username']}' ORDER BY `id` DESC LIMIT 5");

    while ($show = $SQLSelect->fetch(PDO::FETCH_ASSOC)) {

        $ip = $show['ip'];
        $port = $show['port'];
        $time = $show['time'];
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
          
        } else {
            $action = '<button type="button" id="rere" onclick="renew(' . $rowID . ')" class="btn btn-success btn-xs"><i class="fa fa-refresh"></i> Продлить</button>';
        }

        echo '<tr>
		<td style="font-size: 12px;" class="text-center">' . htmlspecialchars($ip) . '</td>
		<td style="font-size: 12px;" class="text-center">' . $port . '</td>
		<td style="font-size: 12px;" class="text-center">' . $method . '</td>		
		<td style="font-size: 12px;" class="text-center">' . $countdown . '</td>
		<td style="font-size: 12px;" class="text-center">' . $action . '</td>
		</tr>';

    }
?> 

	</tbody>
</table>