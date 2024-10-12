<?php

header("X-XSS-Protection: 1; mode=block");

	/// Require the header that already contains the sidebar and top of the website and head body tags
	require_once 'header.php'; 
	
		
?>

<html>
	<title><?php echo $sitename; ?> | Login Logs</title>
		<div class="page-wrapper" style="display: block;">
			<div class="page-breadcrumb">
				<div class="d-flex align-items-center">
					<h4 class="page-title text-truncate text-white font-weight-medium mb-0">Login Logs</h4>
					<div class="ml-auto">
						<nav aria-label="breadcrumb">
							<ol class="breadcrumb m-0 p-0">
								<li class="breadcrumb-item text-sql" aria-current="page"><?php echo $sitename; ?></li>
								<li class="breadcrumb-item text-muted" aria-current="page">Login Logs</li>
							</ol>
						</nav>
					</div>
				</div>
			</div>
      </ul>
	  <?php
		if(isset($notify)){
			echo ($notify);
		}
		?>
		  <div class="container-fluid">  
	     <div class="col-md-12 col-sm-12 col-xs-12">
          <div class="white-box">
				<table class="table js-dataTable-full">
							<thead>
								<tr>
									<th class="text-center" style="font-size: 12px;"></th>
									<th style="font-size: 12px;">Name</th>
									<th style="font-size: 12px;">IP</th>
									<th style="font-size: 12px;">Date</th>
									<th style="font-size: 12px;">Country</th>
								</tr>
							</thead>
							<tbody style="font-size: 12px;">
							<?php
							$SQLGetUsers = $odb -> query("SELECT * FROM `loginlogs` ORDER BY `date` DESC LIMIT 30");
							while ($getInfo = $SQLGetUsers -> fetch(PDO::FETCH_ASSOC)){
								$username = $getInfo['username'];
								$ip = $getInfo['ip'];
								$date = date("d-m-Y, h:i:s a" ,$getInfo['date']);
								$country = $getInfo['country'];
								echo '<tr>
										<td></td>
										<td>'.htmlspecialchars($username).'</td>
										<td>'.htmlspecialchars($ip).'</td>
										<td>'.$date.'</td>
										<td>'.htmlspecialchars($country).'</td>
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

    $sql = "SELECT * FROM `loginlogs`";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        // data is available 

    } else {
        echo '<div class="mt-4" style="font-size: 16px;"><center>No data available.</center></div>';
    }

    $conn->close();

    ?>	
			
          </div>
        </div>
		
      </div>