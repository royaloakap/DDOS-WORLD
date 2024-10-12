<?php
ob_start();
require_once 'includes/db.php';
require_once 'includes/init.php';
if (!($user -> LoggedIn()))
{
	header('location: login.php');
	die();
}
if (!($user -> notBanned($odb)))
{
	header('location: login.php');
	die();
}


$currentPage = "News";
$pageon = "News";
$plansql = $odb -> prepare("SELECT `users`.*,`plans`.`name`, `plans`.`mbt`,`plans`.`max_boots` AS `pboots` FROM `users`, `plans` WHERE `plans`.`ID` = `users`.`membership` AND `users`.`ID` = :id LIMIT 1");
$plansql -> execute(array(":id" => $_SESSION['ID']));
$userInfo = $plansql -> fetch(PDO::FETCH_ASSOC);

?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title><?php echo $bootername; ?><?php echo $pageon ?></title>
	
	<link rel="icon" sizes="192x192" href="img/touch-icon.png" /> 
	<link rel="apple-touch-icon" href="img/touch-icon-iphone.png" /> 
	<link rel="apple-touch-icon" sizes="76x76" href="img/touch-icon-ipad.png" /> 
	<link rel="apple-touch-icon" sizes="120x120" href="img/touch-icon-iphone-retina.png" />
	<link rel="apple-touch-icon" sizes="152x152" href="img/touch-icon-ipad-retina.png" />
	
	<link rel="shortcut icon" type="image/x-icon" href="img/web-hosting.ico" />

	<link rel="stylesheet" type="text/css" href="css/bootstrap.min.css">
	<link rel="stylesheet" type="text/css" href="css/main.min.css">
</head>
<body>
	<header class="top-bar">
			<?php include "includes/template/header.php"; ?>
			
	</header> <!-- /top-bar -->


	<div class="wrapper">

		<aside class="sidebar">
			<?php include "includes/template/sidebar.php"; ?>
		</aside> <!-- /sidebar -->
		
		<section class="content">

			<header class="main-header">
				<div class="main-header__nav">
					<h1 class="main-header__title">
						<i class="pe-7s-news-paper"></i>
						<span><?php echo $pageon ?></span>
					</h1>
					<ul class="main-header__breadcrumb">
						<li><a href="#" onclick="return false;"><?php include 'includes/name.php'; ?></a></li>
						<li><a href="#" onclick="return false;"><?php echo $pageon ?></a></li>
						
					</ul>
				</div>
				
				<div class="main-header__date">
					<input type="radio" id="radio_date_1" name="tab-radio" value="today" checked><!--
					--><input type="radio" id="radio_date_2" name="tab-radio" value="yesterday"><!--
					--><button>
						<i class="pe-7f-date"></i>
						<span>Expire: <?php echo date('d-m-Y' ,$userInfo['expire']); ?></span>
					</button>
				</div>
			</header> <!-- /main-header -->

			

				<div class="row">
				
					<div class="col-md-12">
<?php
	$fetchNews = $odb->query("
		SELECT `n`.*, `u`.`username`
		FROM `news` AS `n`
		LEFT JOIN `users` AS `u`
		ON `u`.`id` = `n`.`author_id`
		ORDER BY `date` DESC
		LIMIT 22;
	");
	if ($fetchNews->rowCount() != 0)
	{
		while ($row = $fetchNews->fetch(PDO::FETCH_ASSOC))
		{
?>

						<div class="media message tabs">
							<figure class="pull-left rounded-image message__img">
								<img class="media-object" src="img/admin.png" alt="user"  height="48" width="48">
							</figure>
							<div class="media-body">
								<h4 class="media-heading message__heading"><?php echo htmlentities($row['username']); ?>
									<span><?php echo timeElapsedFromUNIX($row['date']); ?></span>
								</h4>
								<p class="message__msg"><?php echo nl2br(htmlentities($row['detail'])); ?></p>
							</div>
						</div>

<?php
		}
	} else {
		echo '<div class="alert alert-error">There is no latest news!</div>';
	}
?>
					

					</div> 
				</div> <!-- /row -->


		</section> <!-- /content -->

	</div>


	 
	<script type="text/javascript" src="js/main.js?v4"></script>
</body>
</html>