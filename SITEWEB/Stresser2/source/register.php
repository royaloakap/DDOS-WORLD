<?php

error_reporting(0);
//ini_set("display_errors", "on");

require 'includes/db.php';
require 'includes/init.php';
if ($user -> LoggedIn())
{
	header('Location: index.php');
	die();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title><?php echo $bootername; ?>Register</title>
	
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
if (isset($_POST['registerBtn']))
{
		
			$username = $_POST['username'];
			$password = $_POST['password'];
			$rpassword = $_POST['rpassword'];
			$email = $_POST['email'];
			$checkUsername = $odb -> prepare("SELECT COUNT(*) FROM `users` WHERE `username` = :username");
			$checkUsername -> execute(array(':username' => $username));
			$countUsername = $checkUsername -> fetchColumn(0);
			if (isset($_POST['terms']) && !empty($_POST['terms'])) {
				if (empty($username) || empty($password) || empty($rpassword) || empty($email))
				{
					echo '<div class="g_12"><div class="error iDialog">ERROR: Please fill in all fields</div></div>';
				}
				else
				{
					if (!ctype_alnum($username) || strlen($username) < 4 || strlen($username) > 26)
					{
						echo '<div class="g_12"><div class="error iDialog">Username must be 4-26 length and alnum</div></div>';
					}
					else
					{
						if (!($countUsername == 0))
						{
							echo '<div class="g_12"><div class="error iDialog">Username is taken</div></div>';
						}
						else
						{
							if (!filter_var($email, FILTER_VALIDATE_EMAIL))
							{
								echo '<div class="g_12"><div class="error iDialog">Invalid Email</div></div>';
							}
							else
							{
								if ($password != $rpassword)
								{
									echo '<div class="g_12"><div class="error iDialog">Passwords do not match</div></div>';
								}
								else
								{
									$insertUser = $odb -> prepare("INSERT INTO `users` (`username`,`password`,`email`,`rank`,`membership`,`max_boots`,`expire`,`status`) VALUES(:username, :password, :email, 0, 0, 0, 0, 0)");
									$insertUser -> execute(array(':username' => $username, ':password' => SHA1($password), ':email' => $email));
									echo '<div class="g_12"><div class="alert alert-success">User Registered</div></div><meta http-equiv="refresh" content="2;url=login.php">';
								}
							}
						}
					}
				}
			} else {
				echo '<div class="g_12"><div class="alert alert-danger">ERROR: You must agree to the Terms of Service in order to register with PureStresser!</div></div>';
			}
}
?>



				
				
					<div>
					
				<div class="col-md-4  col-md-offset-4">
						<article class="widget widget__login">
							<header class="widget__header one-btn">
								<div class="widget__title">
									<div class="main-logo"><img src="img/web-hosting.png" height="74" width="63"></div> Register
								</div>
								<div class="widget__config">
									<a href="#" onclick="window.location.href = 'login.php'">Sign in</a>
								</div>
							</header>
							<form role="form" action="" method="POST">
							<div class="widget__content">
								<input type="text" name="username" class="validate[required]" id="username" maxlength="15" placeholder="Username"/>
								<input type="password" name="password" class="validate[required]" id="pass" placeholder="Password" />
								<input type="password" name="rpassword" class="validate[required]" id="rpass" placeholder="Repeat Password" />
								<input type="text" name="email" class="validate[required]" id="email" placeholder="Email" />
								
								<div class="widget__content filled" style="padding:12px 36px;">
									<input type="checkbox" class="custom-checkbox" name="terms" id="terms" />
									<label for="terms"></label> 
									<label for="terms">I agree to the <a href="terms.php">Terms of Service</a> -<a href="privacy.php"> Privacy Policy</a></label> 
								</div>
								
								<div class="widget__content">
									<button name="registerBtn">Sign Up</button>
								</div>
							
							
							</form>
						</article><!-- /widget -->
					
				</div>

				<style>
				
				#recaptcha_area, #recaptcha_table { line-height: 0!important;}
#recaptcha_response_field { padding:0px; height: 30px; background: none; border: none; color: #000 !important; }
#recaptcha_privacy { position: relative; left: 140px;}
#recaptcha_privacy a { color:#FFF; font-size: 75%; } 

</style>


	 
	<script type="text/javascript" src="js/main.js"></script>
	<script type="text/javascript" src="js/amcharts/amcharts.js"></script>
	<script type="text/javascript" src="js/amcharts/serial.js"></script>
	<script type="text/javascript" src="js/amcharts/pie.js"></script>
	<script type="text/javascript" src="js/chart.js"></script>
</body>
</html>