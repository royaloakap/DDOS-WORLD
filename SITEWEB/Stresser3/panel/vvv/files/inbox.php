<?php

header("X-XSS-Protection: 1; mode=block");

	ob_start(); 
	require_once '../avg/mycon.php';
	require_once '../avg/usv.php'; 

	if (!empty($maintaince)) {
		die($maintaince);
	}

	if (!($user->LoggedIn()) || !($user->notBanned($odb)) || !(isset($_SERVER['HTTP_REFERER']))) {
		die();
	}

	$userid = $_SESSION['ID'];

?>

   <div class="table-responsive">
	<table class="table">
		<thead>
			<tr>
				<th scope="col"><center>Status</center></th>
				<th scope="col">Subject</th>
				<th scope="col">Date</th>
				<th scope="col">Action</th>
			</tr>
		</thead>
		<tbody>
	<?php
	$SQLGetTickets = $odb -> prepare("SELECT * FROM `tickets` WHERE `username` = :username ORDER BY `id` DESC");
	$SQLGetTickets -> execute(array(':username' => $_SESSION['username']));
	while ($getInfo = $SQLGetTickets -> fetch(PDO::FETCH_ASSOC))
	{
	$id = $getInfo['id'];
	$subject = $getInfo['subject'];
	$status = $getInfo['status'];
	$date = date("d/m/Y" ,$getInfo['date']);
	if ($status == 'Waiting for user response')
	{
		$group = 'fas fa-circle text-primary"';
	}
	elseif ($status == 'Waiting for admin response')
	{
		$group = 'fas fa-circle text-success';
	}
	elseif($status == "Closed")
	{
		$group = 'fas fa-circle text-danger';
	}
	else
	{
		$group = 'warning';
	}
	echo '<tr>
	<td>
	<center><span class="'.$group.'"></span></center>					</td>
	<td>'.htmlspecialchars($subject).'</td>
	<td>'.$date.'</td>
          <td><a href="ticket.php?id='.$id.'">View</a></td>
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

    $sql = "SELECT * FROM `tickets` WHERE `username` = '{$_SESSION['username']}'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        // data is available 

    } else {
        echo '<div class="mt-4"><center>You do not have tickets.</center></div>';
    }

    $conn->close();

    ?>										
