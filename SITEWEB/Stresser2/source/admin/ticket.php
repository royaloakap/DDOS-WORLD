<?php

error_reporting(0);

ob_start();
require_once '../includes/db.php';
require_once '../includes/init.php';

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
if (!($user -> isAdmin($odb)))
{
	die('You are not admin');
}
$plansql = $odb -> prepare("SELECT `users`.*,`plans`.`name`, `plans`.`mbt`,`plans`.`max_boots` AS `pboots` FROM `users`, `plans` WHERE `plans`.`ID` = `users`.`membership` AND `users`.`ID` = :id LIMIT 1");
$plansql -> execute(array(":id" => $_SESSION['ID']));
$userInfo = $plansql -> fetch(PDO::FETCH_ASSOC);		


$currentPage = "admin_support";
$pageon = "Ticket";
$id = ($_GET['id']);
$closed = false;

$SQLGetTickets = $odb -> prepare("SELECT * FROM `tickets` WHERE `id` = :id ");
$SQLGetTickets->execute(array(
	":id"   => $id,
));
if ($SQLGetTickets->rowCount() != 0) {
	while ($getInfo = $SQLGetTickets -> fetch(PDO::FETCH_ASSOC))
	{
		$ticketUser = ($getInfo['username']);
		$ticketSubject = ($getInfo['subject']);
		$ticketDate = ($getInfo['date']);
		$ticketStatus = ($getInfo['status']);
		$ticketMsg = ($getInfo['context']);


		$closed = ($getInfo['status'] == "Closed");
	}
} else {
	die("Invalid ticket ID");
}
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


			
										<?php
				if (isset($_GET['closeTicket']))
	   {
$SQLupdate = $odb -> prepare("UPDATE `tickets` SET `status` = :status WHERE `id` = :id");
$SQLupdate -> execute(array(':status' => 'Closed', ':id' => $id));
echo '<div class="g_12"><div class="alert alert-success">SUCCESS: Ticket has been closed.  Redirecting....</div></div><meta http-equiv="refresh" content="3;url=support.php">';
 	   }
	   ?>
	   <?php
	   if (isset($_POST['updateBtn']))
	   {
	   	$updatecontent = $_POST['content'];

			$errors = array();
			if (empty($updatecontent))
			{
				$errors[] = 'Fill in all fields';
			}
			if (empty($errors))
			{
				$SQLinsert = $odb -> prepare("INSERT INTO `messages` VALUES(NULL, :ticketid, :content, :sender, UNIX_TIMESTAMP())");
			$SQLinsert -> execute(array(':sender' => $_SESSION['username'], ':content' => $updatecontent, ':ticketid' => $id));
			{
				$SQLUpdate = $odb -> prepare("UPDATE `tickets` SET `status` = :status WHERE `id` = :id");
				$SQLUpdate -> execute(array(':status' => 'Waiting for User response.', ':id' => $id));
				echo '<div class="g_12"><div class="alert alert-success">SUCCESS: Reply has been sent.</div></div>';
			}
			}
			else
			{
				echo '<div class="alert alert-error"><p><strong>ERROR:</strong><br />';
				foreach($errors as $error)
				{
					echo '-'.$error.'<br />';
				}
				echo '</div>';
			}
		}
		?>


                                   
	<div class="row">
		<div class="col-md-12">
						<article class="widget">
							<header class="widget__header">
								<div class="widget__title">
									<i class="pe-7f-chat"></i><h3>Ticket Conversation <small><?php echo htmlentities($ticketSubject); ?></small> <?php echo ($closed ? '<span class="right badge badge--red">Closed</span>' : '<span class="right badge badge--green">' . htmlentities($ticketStatus) . '</span>'); ?></h3>
								</div>
								<div class="widget__config">
									<a href="#reply" style="line-height: 68px;" class="override"><i class="pe-7s-note"></i></a>
									<a href="?id=<?php echo $id; ?>&closeTicket=1" style="line-height: 68px;" class="override"><i class="pe-7s-close"></i></a>
								</div>
							</header>
							
							<div class="widget__content">
								
								<div class="media message">
										<figure class="pull-left rounded-image message__img">
											<img class="media-object" src="../img/user.png" alt="user" height="48" width="48">
										</figure>
										<div class="media-body">
											<h4 class="media-heading message__heading"><?php echo htmlentities($ticketUser); ?> <span><span class="badge badge--blue">original message</span> <?php echo (isset($ticketDate) && !is_null($ticketDate) ? timeElapsedFromUNIX($ticketDate) : "n/a"); ?></span></h4>
											<p class="message__msg"><?php echo nl2br(htmlentities($ticketMsg)); ?></p>
										</div>
									</div>
		
		<?php
			
			$SQLGetMessages = $odb -> prepare("SELECT `m`.*,`u`.`rank` FROM `messages` AS `m` LEFT JOIN `users` AS `u` ON `u`.`username`=`m`.`sender`
												WHERE `m`.`ticketid` = :ticketid ORDER BY `m`.`messageid` ASC");
			$SQLGetMessages -> execute(array(':ticketid' => $id));
			while ($row = $SQLGetMessages -> fetch(PDO::FETCH_ASSOC))
			{
				$isStaff = ($row['rank'] == "1");
?>
											<div class="media message ticket-msg-<?php echo $row['messageid'] . ($isStaff ? " fav msg-staff" : " msg-user"); ?>">
												<figure class="pull-left rounded-image message__img">
													<img class="media-object" src="../img/admin.png" alt="admin" height="48" width="48">
												</figure>
												<div class="media-body">
													<h4 class="media-heading message__heading"><?php echo htmlentities($row['sender']); ?> <span><?php echo ($isStaff ? '<span class="badge badge--red">Layer7 Stresser Staff</span>' : "") . (isset($row['date']) && !is_null($row['date']) ? timeElapsedFromUNIX($row['date']) : "n/a"); ?></span></h4>
													<p class="message__msg"><?php echo nl2br(htmlentities($row['content'])); ?></p>
												</div>
											</div> <!-- /message -->
<?php
		}

	 ?>
											

											<div class="message__write" id="reply" name="reply">
												<form action="" method="POST">
													<input type="text" name="content" placeholder="Leave a Message ..." />
													<input type="submit" name="updateBtn" id="send_msg">
														<label for="send_msg" style="width:122px;"><i class="pe-7s-note"></i></label>
												
													</form>
											</div>
										
								

								
							</div> <!-- /widget__content -->

						</article><!-- /widget -->
					</div>
			</div>		 
					
		</section> <!-- /content -->

	</div>


	 
	<script type="text/javascript" src="../js/main.js"></script>
	<script type="text/javascript" src="../js/amcharts/amcharts.js"></script>
	<script type="text/javascript" src="../js/amcharts/serial.js"></script>
	<script type="text/javascript" src="../js/amcharts/pie.js"></script>
	<script type="text/javascript" src="../js/chart.js"></script>
</body>
</html>

