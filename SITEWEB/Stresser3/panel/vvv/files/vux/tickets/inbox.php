<?php

header("X-XSS-Protection: 1; mode=block");

	ob_start(); 
	require_once '../../../avg/mycon.php';
	require_once '../../../avg/usv.php'; 

	if (!empty($maintaince)) {
		die($maintaince);
	}

	if (!($user->LoggedIn()) || !($user->notBanned($odb)) || !(isset($_SERVER['HTTP_REFERER']))) {
		die();
	}

	$userid = $_SESSION['ID'];

?>

<div class="content" id="messages"><table class="table mb-0">
	<thead>
		<tr>
			<th style="font-size: 16px;">Status</th>
			<th style="font-size: 16px;">Subject</th>
			<th style="font-size: 16px;">Content</th>
			<th style="font-size: 16px;">Date</th>
		</tr>
	</thead>
	<tbody>
<?php

$select = $odb->query("SELECT * FROM `tickets` WHERE `status` = 'Waiting for admin response' ORDER BY `id` DESC");
while($show = $select->fetch(PDO::FETCH_ASSOC)){
												
?>
<tr onclick="location.href = 'ticket.php?id=<?php echo $show['id']; ?>'">
						
                                               <span class="<?php if($show['status'] == "Waiting for admin response") echo "active"; ?>">
													<td class="text-muted" style="width: 250px;">
                                                              <label class="fas fa-circle text-danger">
                                                        </label>                                                    </td>
													<td>
                                                        <a class="font-w600"><?php echo $show['subject']; ?></a>
                                                    </td>
													<td class="font-w600"><?php echo $show['content']; ?></td>
                                                    <td class="font-w600" style="width: 120px;">
                                                        <a><?php echo date('m/d/Y', $show['date']); ?></a>
                                                    </td>
                                                </tr>
											<?php
											
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

    $sql = "SELECT * FROM `tickets` WHERE `status` = 'Waiting for admin response' ORDER BY `id` DESC";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        // data is available 

    } else {
        echo '<div class="mt-4" style="font-size: 16px;"><center>No data available.</center></div>';
    }

    $conn->close();

    ?>
