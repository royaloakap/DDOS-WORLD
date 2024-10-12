<?php

header("X-XSS-Protection: 1; mode=block");

	ob_start(); 
	require_once '../../../avg/mycon.php'; 
	require_once '../../../avg/usv.php';  

	if (!($user->LoggedIn()) || !($user->notBanned($odb)) || !($user -> isAdmin($odb)) || !(isset($_SERVER['HTTP_REFERER']))) {
		die();
	}
	
?>
<table class="table">
	<thead>
        <tr>
            <th class="text-center" style="font-size: 12px;">User</th>
            <th class="text-center" style="font-size: 12px;">Target</th>
            <th class="text-center" style="font-size: 12px;">Method</th>
            <th class="text-center" style="font-size: 12px;">Expires</th>
			<th class="text-center" style="font-size: 12px;">Stop</th>
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
        $action = '<button type="button" onclick="stop(' . $rowID . ')" id="st" class="btn btn-danger btn-sm"></i> Stop</button>';
        echo 	'<tr class="text-center" style="font-size: 12px;">
					<td>' . $user . '</td>
					<td>' . htmlspecialchars($ip) . '</td>
					<td>' . $method . '</td>
					<td>' . $countdown . '</td>
					<td>' . $action . '</td>
				</tr>';
	}
?> 

	</tbody>
</table>
						 <?php
    // Create connection
    $conn = new mysqli(DB_HOST, DB_USERNAME, DB_PASSWORD, DB_NAME, 3306);
    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    } 

    $sql = "SELECT * FROM `logs` WHERE `time` + `date` > UNIX_TIMESTAMP() AND `stopped` = 0 ORDER BY `id` DESC";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        // data is available 

    } else {
        echo '<div class="mt-4" style="font-size: 16px;"><center>No running attacks.</center></div>';
    }

    $conn->close();

    ?>