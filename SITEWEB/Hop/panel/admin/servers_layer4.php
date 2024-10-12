<?php
ob_start();
require_once '../includes/db.php';
require_once '../includes/init.php';
if (!($user -> LoggedIn()))
{
	header('location: ../login.php');
	die();
}
if (!($user->isAdmin($odb)))
{
	header('unset.php');
	die();
}
if (!($user -> notBanned($odb)))
{
	header('location: ../login.php');
	die();
}

$currentPage = "admin_index";
$pageon = "Servers Management";
$plansql = $odb -> prepare("SELECT `users`.*,`plans`.`name`, `plans`.`mbt`,`plans`.`max_boots` AS `pboots` FROM `users`, `plans` WHERE `plans`.`ID` = `users`.`membership` AND `users`.`ID` = :id LIMIT 1");
$plansql -> execute(array(":id" => $_SESSION['ID']));
$userInfo = $plansql -> fetch(PDO::FETCH_ASSOC);		
?>
<!DOCTYPE html>
<html lang="en">
<head><meta http-equiv="Content-Type" content="text/html; charset=windows-1252">
	
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title><?php echo $bootername; ?><?php echo $pageon ?></title>
	
	<link rel="icon" sizes="192x192" href="../img/touch-icon.png" /> 
	<link rel="apple-touch-icon" href="../img/touch-icon-iphone.png" /> 
	<link rel="apple-touch-icon" sizes="76x76" href="../img/touch-icon-ipad.png" /> 
	<link rel="apple-touch-icon" sizes="120x120" href="../img/touch-icon-iphone-retina.png" />
	<link rel="apple-touch-icon" sizes="152x152" href="../img/touch-icon-ipad-retina.png" />
	
	<link rel="shortcut icon" type="image/x-icon" href="../img/web-hosting.ico" />

	<link rel="stylesheet" type="text/css" href="../css/bootstrap.min.css">
	<link rel="stylesheet" type="text/css" href="../css/main.min.css">
</head>
<body>
	<header class="top-bar">
		<?php include "../includes/template/header.php"; ?>
	</header> <!-- /top-bar -->


	<div class="wrapper">

		<aside class="sidebar">
			<?php include "../includes/template/sidebar.php"; ?>
		</aside> <!-- /sidebar -->
		
		<section class="content">
			<header class="main-header">
				<div class="main-header__nav">
					<h1 class="main-header__title">
						<i class="pe-7s-server"></i>
						<span> <?php echo $pageon ?></span>
					</h1>
					<ul class="main-header__breadcrumb">
						<li><a href="#" onclick="return false;"><?php include '../includes/name.php'; ?></a></li>
						<li><a href="#" onclick="return false;"><?php echo $pageon ?></a></li>
					</ul>
				</div>
				
				<div class="main-header__date">
					<input type="radio" id="radio_date_1" name="tab-radio" value="today" checked><!--
					--><input type="radio" id="radio_date_2" name="tab-radio" value="yesterday"><!--
					--><button>
						<i class="pe-7f-date"></i>
						<span><?php echo date('m-d-Y' ,$userInfo['expire']); ?></span>
					</button>
				</div>
			</header>

<?php
$action = (isset($_GET['action']) ? $_GET['action'] : "list");

if ($action === "create") {

	if (isset($_POST['name'], $_POST['address'], $_POST['strength']))
	{
		$createServer = $odb->prepare("insert into servers_layer4 values(null, :name, :addr, :res, :desc, :strength, 0, 'caution', 0, :active)");
		$createServer->execute(array(
			":name" => $_POST['name'],
			":addr" => $_POST['address'],
			":res"  => (isset($_POST['resource']) ? $_POST['resource'] : ""),
			":desc" => (isset($_POST['description']) ? $_POST['description'] : ""),
			":strength" => $_POST['strength'],
			":active" => (isset($_POST['status']) ? 1 : 0),
		));


		echo "<div class='alert alert-success'>Server has been successfully created</div>";
	} else {
		echo "<div class='alert alert-danger'>Please specify all fields</div>";
	}

} else if ($action === "edit") {

	if (isset($_POST['updateBtn'])) {
		$update = $odb->prepare("update servers_layer4 set `name`=:n, `description`=:d,`addr`=:a,`resource`=:r,`strength`=:s,`active`=:active where id = :id");
		$update->execute(array(
			":n" => $_POST['name'],
			":d" => (isset($_POST['description']) ? $_POST['description'] : ""),
			":a" => $_POST['address'],
			":r" => (isset($_POST['resource']) ? $_POST['resource'] : ""),
			":s" => $_POST['strength'],
			":id" => $_GET['sid'],

			":active" => (isset($_POST['status']) ? 1 : 0),
		));

		echo "<div class='alert alert-success'>Server has been successfully modified</div>";
	}

	if (isset($_GET['sid'])) {
		$fetchServer = $odb->prepare("select * from servers_layer4 where id = :id");
		$fetchServer->execute(array(":id" => $_GET['sid']));
		$row = $fetchServer->fetch(PDO::FETCH_ASSOC);

		?>


<div class="row">
 <form action="?action=edit&sid=<?php echo $row['id']; ?>" method="POST">
					<div class="col-md-12">
						<article class="widget widget__form">
							<header class="widget__header">
								<div class="widget__title" style="width:100%">
									<i class="pe-7s-menu"></i><h3>Edit Server</h3>
								</div> 
							</header>

							<div class="widget__content">
								<input placeholder="Name" name="name" maxlength="50" type="text" value="<?php echo $row['name']; ?>" />
								<input placeholder="Description" name="description" type="text" value="<?php echo $row['description']; ?>" />
								<input placeholder="Address (http://api.yourserver.ua/)" name="address" type="text" value="<?php echo $row['addr']; ?>"/>
								<input placeholder="Resource" name="resource" value="<?php echo $row['resource']; ?>" type="text" />
								<input placeholder="Strength" name="strength" type="text" value="<?php echo $row['strength']; ?>"/>

								Server Enabled:
								<input type="checkbox" name="status" id="status" class="sw" <?php echo ($row['active'] == 1 ? " checked" : ""); ?> />
								<label class="switch green" for="status"></label> 

								<button type="submit" name="updateBtn">Update</button>
								
							</div>
						</article><!-- /widget -->
					</div>
					
</form>
					
					</div>


		<?php
	} else {
		echo "<div class='alert alert-danger'>No server id specified</div>";
	}

} else if ($action === "delete") {
	if (isset($_GET['sid'])) {
		$deleteServer = $odb->prepare("delete from servers_layer4 where id = :id");
		$deleteServer->execute(array(":id" => $_GET['sid']));
		echo "<div class='alert alert-success'>Server has been successfully deleted</div>";
	} else {
		echo "<div class='alert alert-danger'>No server id specified</div>";
	}
}

?>

<?php if ($action != "edit") { ?>
<div class="row">
 <form action="?action=create" method="POST">
					<div class="col-md-12">
						<article class="widget widget__form">
							<header class="widget__header">
								<div class="widget__title" style="width:100%">
									<i class="pe-7s-menu"></i><h3>Add Server</h3>
								</div> 
							</header>

							<div class="widget__content">
								<input placeholder="Name" name="name" maxlength="50" type="text" />
								<input placeholder="Description" name="description" type="text" />
								<input placeholder="Address (http://api.yourserver.ua/)" name="address" type="text" />
								<input placeholder="Resource" name="resource" value="send.php?target=%host%&method=%method%&port=%port%&time=%time%&key=[Insert Key]" type="text" />
								<input placeholder="Strength" name="strength" type="text" />

								Server Enabled:
								<input type="checkbox" name="status" id="status" class="sw" />
								<label class="switch green" for="status"></label> 

								<button type="submit" name="create">Create</button>
								
							</div>
						</article><!-- /widget -->
					</div>
					
</form>
					
					</div> <?php } ?>

					<div class="col-md-12">
						<article class="widget">
							<header class="widget__header">
								<div class="widget__title" style="width:100%">
									<i class="pe-7s-menu"></i><h3>Manage Servers</h3>
								</div>
							</header>

							<div class="widget__content table-responsive">
								
								<table class="table table-striped media-table">
									  	<thead>
									  		<tr>
                                                    <th><center>ID</th>
                                                    <th><center>Name</th>
                                                    <th><center>Strength</th>
                                                    <th style="width:69px"><center>Status</th>
                                                    <th style="width:225px;"><center>Actions</th>
                                                </tr>
                                            </thead>
											<tbody>
<?php

$getServers = $odb->query("select * from servers_layer4");
if ($getServers->rowCount() != 0) {
	while ($row = $getServers->fetch(PDO::FETCH_ASSOC)) {
		echo "<tr class='spacer'></tr>
		<tr>
			<td>" . $row['id'] . "</td>
			<td>" . $row['name'] . "</td>
			<td>" . $row['strength'] . "</td>
			<td>"  . ($row['active'] == "1" ? "<span class='badge badge--green'>Enabled</span>" : "<span class='badge badge--red'>Disabled</span>") . "</td>
			<td>
				<a href='?action=edit&sid=" . $row['id'] . "' class='btn btn--primary'>Edit</a>
				<a href='?action=delete&sid=" . $row['id'] . "' class='btn btn--primary'>Delete</a>
			</td>
		</tr>";
	}
} else {
	echo "<tr class='spacer'></tr>
		<tr><td colspan='5'>There are no servers, create one</td></tr>";
}

?>
								</tbody>
										</table>
										




							</div>
						</article><!-- /widget -->
					</div>	

		</section> <!-- /content -->

	</div>


	 
	<script type="text/javascript" src="../js/main.js"></script>
</body>
</html>