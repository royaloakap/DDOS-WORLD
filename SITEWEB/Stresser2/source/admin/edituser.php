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
$plansql = $odb -> prepare("SELECT `users`.*,`plans`.`name`, `plans`.`mbt`,`plans`.`max_boots` AS `pboots` FROM `users`, `plans` WHERE `plans`.`ID` = `users`.`membership` AND `users`.`ID` = :id LIMIT 1");
$plansql -> execute(array(":id" => $_SESSION['ID']));
$userInfo = $plansql -> fetch(PDO::FETCH_ASSOC);

$currentPage = "admin_user";
$pageon = "Edit User";
$usersPerPage = 300;
$page = 1;
if (isset($_GET['page']) && !empty($_GET['page']) && is_numeric($_GET['page']))
	$page = $_GET['page'];	
?>
<!DOCTYPE html>
<html lang="en">
<head><meta http-equiv="Content-Type" content="text/html; charset=windows-1252">
	
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title><?php echo $bootername; ?>Edit User</title>
	
	<link rel="icon" sizes="192x192" href="../img/touch-icon.png" /> 
	<link rel="apple-touch-icon" href="../img/touch-icon-iphone.png" /> 
	<link rel="apple-touch-icon" sizes="76x76" href="../img/touch-icon-ipad.png" /> 
	<link rel="apple-touch-icon" sizes="120x120" href="../img/touch-icon-iphone-retina.png" />
	<link rel="apple-touch-icon" sizes="152x152" href="../img/touch-icon-ipad-retina.png" />
	
	<link rel="shortcut icon" type="image/x-icon" href="../img/web-hosting.ico" />

	<link rel="stylesheet" type="text/css" href="../css/bootstrap.min.css">
	<link rel="stylesheet" type="text/css" href="../css/main.min.css">
	<script type="text/javascript" src="../js/main.js"></script>
</head>
<body>


	<div class="wrapper">

		<aside class="sidebar">
			<?php include "../includes/template/sidebar.php"; ?>
		</aside> <!-- /sidebar -->
		
		<section class="content">
			<header class="main-header">
				<div class="main-header__nav">
					<h1 class="main-header__title">
						<i class="pe-7f-wallet"></i>
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

				<!-- /main-header -->
<!-- /row -->
		
		<?php
			if (isset($_POST['search'])) {
				if (isset($_POST['username']) && !empty($_POST['username']) && isset($_POST['email']) && !empty($_POST['email'])) {
					$query = $odb->prepare("SELECT * FROM `users` WHERE `username` LIKE :user AND `email` LIKE :email");
					$query->execute(array(":user"=>"%".$_POST['username'] ."%",":email"=>"%".$_POST['email'] ."%"));
				} else if (isset($_POST['username']) && !empty($_POST['username'])) {
					$query = $odb->prepare("SELECT * FROM `users` WHERE `username` LIKE :user");
					$query->execute(array(":user"=>"%".$_POST['username'] ."%"));
				} else if (isset($_POST['email']) && !empty($_POST['email'])) {
					$query = $odb->prepare("SELECT * FROM `users` WHERE `email` LIKE :email");
					$query->execute(array(":email"=>"%".$_POST['email'] ."%"));
				} else {
					echo "<div class='alert alert-error'>Please search with an email or username</div>";
				}
				
				if (isset($query)) {
					if ($query->rowCount() != 0) {
					?>
					<div class="col-md-12">
						<article class="widget">
							<header class="widget__header">
								<div class="widget__title">
									<i class="pe-7s-search"></i><h3>Search Criteria</h3>
								</div>
								<div class="widget__config">
									<a href="#"><i class="pe-7f-refresh"></i></a>
									<a href="#"><i class="pe-7s-close"></i></a>
								</div>
							</header>

							<div class="widget__content filled pad20">
								
								<div class="row">
									<div class="col-md-12 text-center btn__showcase2">
										
										<table class="table table-hover media-table">
									  	<thead>
									  		<tr>
                                                    <th><center>ID</th>
                                                    <th><center>Username</th>
                                                    <th><center>Email</th>
                                                    <th><center>Rank</th>
                                                    <th><center>Actions</th>
                                                </tr>
                                            </thead>
											<tbody>
                                            <?php
								while ($getInfo = $query -> fetch(PDO::FETCH_ASSOC))
								{
									$id = $getInfo['ID'];
									$user = htmlentities($getInfo['username']);
									$email = htmlentities($getInfo['email']);
									$rank = ($getInfo['rank'] == 1) ? 'Admin' : 'Member';
									echo '<tr><td>'.$id.'</td><td><a href="user.php?id='.$id.'">'.$user.'</a></td><td>'.$email.'</td><td>'.$rank.'</td><td><a href="logins.php?user=' . htmlentities($getInfo['username']) . '" class="btn blue inverse">View Login History</a></td></tr>';
								}
								?>
								</tbody>
										</table>
										



									</div>

								</div>

							</div>
						</article><!-- /widget -->
					</div>
					<?php
					} else {
						echo "<div class='alert alert-error'>No results found for criteria</div>";
					}
				}
			} else {
			?>
		
				<div class="row">
 <form action="" method="POST">
 
		
					<div class="col-md-4">
					<div class="row">
					<div class="col-md-12">
						<article class="widget">
							<header class="widget__header">
								<div class="widget__title" style="width:100%;">
									<i class="pe-7s-graph"></i><h3>User Statistics</h3>
								</div>
							</header>

							<div class="widget__content filled pad20">
								
								<div class="row">
									
									<div class="col-md-12">
										<div class="row">
											<div class="col-md-6">
												Users Registered</div>
											<div class="col-md-6">
												<span class="badge blue">
													<?php
														$statistics = array();
													
													$fetchUsers = $odb->query("SELECT COUNT(*) FROM `users`");
													$statistics['users'] = $fetchUsers->fetchColumn(0);
													echo $statistics['users'];
												?>
												</span>
											</div>
											<div class="col-md-6">
												Users With Plans</div>
											<div class="col-md-6">
												<span class="badge blue">
													<?php
													$fetchUsers = $odb->query("SELECT COUNT(*) FROM `users` WHERE `membership`!=0");
													$statistics['plans'] = $fetchUsers->fetchColumn(0);
													echo $statistics['plans'];
												?>
												</span>
											</div>
											<div class="col-md-6">
												Users Without Plans</div>
											<div class="col-md-6">
												<span class="badge blue">
													<?php
													echo ($statistics['users'] - $statistics['plans']);
												?>
												</span>
											</div>
										</div>
									</div>

								</div>

							</div>
						</article><!-- /widget -->
					</div>
					<div class="col-md-12">
					<article class="widget widget__form">
							<header class="widget__header">
								<div class="widget__title" style="width:100%;">
									<i class="pe-7s-search"></i><h3>User Search</h3>
								</div>
							</header>

								
									
									<label for="input-1" class="stacked-label"><i class="pe-7f-user"></i></label>
	<input type="text" name="username" class="stacked-input" id="input-1" placeholder="Username">
	<label for="input-2" class="stacked-label"><i class="pe-7s-mail"></i></label>
	<input type="text"  name="email" class="stacked-input" id="input-2" placeholder="Email Address">
	<button type="submit" name="search">Search</button>


							
						</article><!-- /widget -->
					</div>
					</div>
					</div>
					<div class="col-md-8">
						<article class="widget">
							<header class="widget__header">
								<div class="widget__title" style="width:100%;">
									<i class="pe-7s-graph"></i><h3>User Plans</h3>
								</div>
							</header>

							<div class="widget__content filled pad20">
								
								<div class="row">
									
									<div class="col-md-12">
										<div id="chartdiv3" style="width: 100%; height: 362px;"></div>
										
	
									</div>

								</div>

							</div>
						</article><!-- /widget -->
					</div>
				
					
					<div class="col-md-12">
						<article class="widget">

							<div class="widget__content filled pad20">
								
								<div class="row">
									<div class="col-md-12 text-center btn__showcase2">
										
										<div class="row">
										
											<?php
												$pages = round($statistics['users']/$usersPerPage);
												$pagination = "<div class='btn-group block'>
													<a" . ($page > 1 ? " href='?page=" . ($page-1) . "'" : "") . " class='btn dark inverse'>&laquo; Previous</a>";
												
												for ($i = 1; $i <= $pages; $i++)
													$pagination .= "<a href=\"?page=" . $i . "\" class='btn " . ($i==$page?"red" : "blue") . " inverse'>" . $i . "</a>";
												
												
												$pagination .= "<a" . ($page < $pages ? " href='?page=" . ($page+1) . "'" : "") . " class='btn dark inverse'>Next &raquo;</a></div>";
												
												echo $pagination;
											?>
										
										
										</div>

									</div>

								</div>

							</div>
						</article><!-- /widget -->
					</div>			

					<div class="col-md-12">
						<article class="widget">
							<header class="widget__header">
								<div class="widget__title">
									<i class="pe-7s-mouse"></i><h3><?php echo $pageon ?></h3>
								</div>
								<div class="widget__config">
									<a href="#"><i class="pe-7f-refresh"></i></a>
									<a href="#"><i class="pe-7s-close"></i></a>
								</div>
							</header>

							<div class="widget__content table-responsive">
								
								<table class="table table-striped media-table">
									  	<thead>
									  		<tr>
                                                    <th><center>ID</th>
                                                    <th><center>Username</th>
                                                    <th><center>Email</th>
                                                    <th><center>Rank</th>
                                                    <th><center>Actions</th>
                                                </tr>
                                            </thead>
											<tbody>
                                            <?php
								$SQLGetUsers = $odb -> prepare("SELECT * FROM `users` ORDER BY `ID` DESC LIMIT :start,:max");
								$SQLGetUsers->bindValue(":start" , ($page-1)*$usersPerPage, PDO::PARAM_INT);
								$SQLGetUsers->bindValue(":max" , $usersPerPage, PDO::PARAM_INT);
								$SQLGetUsers->execute();
								while ($getInfo = $SQLGetUsers -> fetch(PDO::FETCH_ASSOC))
								{
									$id = $getInfo['ID'];
									$user = htmlentities($getInfo['username']);
									$email = htmlentities($getInfo['email']);
									$rank = ($getInfo['rank'] == 1) ? 'Admin' : 'Member';
									echo '<tr class="spacer"></tr><tr><td>'.$id.'</td><td><a href="user.php?id='.$id.'">'.$user.'</a></td><td>'.$email.'</td><td>'.$rank.'</td><td><a href="logins.php?user=' . htmlentities($getInfo['username']) . '" class="btn blue inverse">View Login History</a></td></tr>';
								}
								?>
								</tbody>
										</table>
										




							</div>
						</article><!-- /widget -->
					</div>	
					<div class="col-md-12">
						<article class="widget">

							<div class="widget__content filled pad20">
								
								<div class="row">
									<div class="col-md-12 text-center btn__showcase2">
										
										<div class="row">
										
											<?php
												
												
												echo $pagination;
											?>
										
										
										</div>

									</div>

								</div>

							</div>
						</article><!-- /widget -->
					</div>	

				 <?php }
				 ?>

		</section> <!-- /content -->

	</div>


	 
	<script type="text/javascript" src="../js/amcharts/amcharts.js"></script>
	<script type="text/javascript" src="../js/amcharts/serial.js"></script>
	<script type="text/javascript" src="../js/amcharts/pie.js"></script>
	<?php if (!isset($_POST['search'])) { ?><script type="text/javascript">
		jQuery(function() {
		AmCharts.ready(function () {
			var chart3 = AmCharts.makeChart("chartdiv3", {
				"type": "pie",
				"theme": "none",
				"dataProvider": [
<?php
$i = 1;
$fetchUsers = $odb->query("
	SELECT `m`.`membership`,`p`.`name`, COUNT(*) AS `total`
	FROM `users` AS `m`
	LEFT JOIN `plans` AS `p`
	ON `p`.`ID` = `m`.`membership`
	WHERE `m`.`expire` != 0
	GROUP BY `m`.`membership` 
");
while ($row = $fetchUsers->fetch(PDO::FETCH_ASSOC)) {
	echo "{'title':'" . $row['name'] . " (#" . $row['membership'] . ")','value':'" . $row['total'] . "'}";
	if ($i < $fetchUsers->rowCount()) echo ",";
	$i++;
}
?>
				],
				"titleField": "title",
				"valueField": "value",
				"labelRadius": 5,
				"color": "rgba(255,255,255,0.5)",
				"radius": "42%",
				"innerRadius": "60%",
				"labelText": "[[title]]"
			});
		});
		});
	</script> <?php } ?>
</body>
</html>