<?php

//error_reporting(-1);
//ini_set("display_errors", "on");


require 'includes/db.php';
require 'includes/init.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title><?php echo $bootername; ?>Login</title>
	
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




<?php

if (!($user -> LoggedIn()))
{
	if (isset($_POST['loginBtn']))
	{
		$username = $_POST['username'];
		$password = $_POST['password'];
		$errors = array();
		if (!ctype_alnum($username) || strlen($username) < 4 || strlen($username) > 15)
		{
			//$errors[] = 'Username Must Be  Alphanumberic And 4-15 characters in length';
		}
		
		if (empty($username) || empty($password))
		{
			$errors[] = 'Please fill in all fields';
		}
		
		if (empty($errors))
		{
			$SQLCheckLogin = $odb -> prepare("SELECT COUNT(*) FROM `users` WHERE `username` = :username AND `password` = :password");
			$SQLCheckLogin -> execute(array(':username' => $username, ':password' => SHA1($password)));
			$countLogin = $SQLCheckLogin -> fetchColumn(0);
			if ($countLogin == 1)
			{
				$SQLGetInfo = $odb -> prepare("SELECT `username`, `ID`, `status` FROM `users` WHERE `username` = :username AND `password` = :password");
				$SQLGetInfo -> execute(array(':username' => $username, ':password' => SHA1($password)));
				$userInfo = $SQLGetInfo -> fetch(PDO::FETCH_ASSOC);
				if ($userInfo['status'] == 0)
				{
					$logAddr = $odb->prepare("INSERT INTO `login_history` (`username`,`ip`,`date`,`http_agent`) VALUES (:user, :ip, UNIX_TIMESTAMP(NOW()), :agent);");
					$logAddr->execute(array( ":user" => $username, ":ip" => $_SERVER['REMOTE_ADDR'], ":agent" => $_SERVER['HTTP_USER_AGENT']));
					$_SESSION['username'] = $userInfo['username'];
					$_SESSION['ID'] = $userInfo['ID'];
					echo '<div class="alert alert-success"><p><strong>SUCCESS: </strong>Login Successful.  Redirecting....</p></div><meta http-equiv="refresh" content="3;url=index.php">';
				}
				else
				{
					echo '<div class="alert alert-danger"><p><strong>ERROR: </strong>Your account has been suspended.</p></div>';
				}
			}
			else
			{
				echo '<div class="alert alert-danger"><p><strong>ERROR: </strong>Login Failed</p></div>';
			}
		}
		else
		{
			echo '<div class="alert alert-danger"><p><strong>ERROR:</strong><br />';
			foreach($errors as $error)
			{
				echo '-'.$error.'<br />';
			}
			echo '</div>';
		}
	}
}
else
{
	header('location: index.php');
}
?>

				
				
					<div>
					
					<div class="col-md-4  col-md-offset-4">
						<article class="widget widget__login">
							<header class="widget__header one-btn">
								<div class="widget__title">
									<div class="main-logo"><img src="img/web-hosting.png" height="74" width="63"></div> Sign in
								</div>
								<div class="widget__config">
									<a href="#" onclick="window.location.href = 'register.php'">Sign Up</a>
								</div>
							</header>
							
							<form role="form" action="" method="POST">
							<div class="widget__content">
								<input type="text" name="username" placeholder="Username">
								<input type="password" name="password" placeholder="Password">
								<div class="widget_content">
								<button name="loginBtn">Sign in</button>
								
							</div>
							<h4></h4><div class="login__remember text-center">
								<input type="checkbox" class="custom-checkbox" id="cc1" checked>
								<label for="cc1"></label>
								Remember me - <a href="terms.php">Terms of Service</a>
							</div>
						
							
						</article><!-- /widget -->
					</div>
				</div>



	 
	<script type="text/javascript" src="js/main.js"></script>
	<script type="text/javascript" src="js/amcharts/amcharts.js"></script>
	<script type="text/javascript" src="js/amcharts/serial.js"></script>
	<script type="text/javascript" src="js/amcharts/pie.js"></script>
	<script type="text/javascript" src="js/chart.js"></script>
</body>
</html>