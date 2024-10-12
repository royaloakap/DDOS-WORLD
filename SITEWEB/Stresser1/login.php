<?php 

	ob_start();
	require_once 'includes/app/config.php';
	require_once 'includes/app/init.php';
	require_once 'includes/mail/class.phpmailer.php';
	require_once 'includes/mail/class.smtp.php';

	$mail = new PHPMailer;

	if (!(empty($maintaince))) {
		header('Location: maintenace.php');
		exit;
	}

	//Set IP (are you using cloudflare?)
	if ($cloudflare == 1){
		$ip = $_SERVER["HTTP_CF_CONNECTING_IP"];
	}
	else{
		$ip = $user -> realIP();
	}

	//Are you already logged in?
	if ($user -> LoggedIn()){
		header('Location: home.php');
		exit;
	}


	if(isset($_POST['doLogin'])){
		$username = $_POST['login-username'];
		$password = $_POST['login-password'];
		
		
		$date = strtotime('-1 hour', time());
		$attempts = $odb->query("SELECT COUNT(*) FROM `loginlogs` WHERE `ip` = '$ip' AND `username` LIKE '%failed' AND `date` BETWEEN '$date' AND UNIX_TIMESTAMP()")->fetchColumn(0);
		if ($attempts > 2) {
			$date = strtotime('+1 hour', $waittime = $odb->query("SELECT `date` FROM `loginlogs` WHERE `ip` = '$ip' ORDER BY `date` DESC LIMIT 1")->fetchColumn(0) - time());
			//$error = 'Too many failed attempts. Please wait '.$date.' seconds and try again.';
		}
		
		if(empty($username) || empty($password)){
			$error = error("Пожалуйста, заполните все поля");
		}

		/// Main Checks Against the Inputs
		
		$SQLCheckLogin = $odb -> prepare("SELECT COUNT(*) FROM `users` WHERE `username` = :username");
		$SQLCheckLogin -> execute(array(':username' => $username));
		$countLogin = $SQLCheckLogin -> fetchColumn(0);
		if (!($countLogin == 1)){
			$SQL = $odb -> prepare("INSERT INTO `loginlogs` VALUES(:username, :ip, UNIX_TIMESTAMP(), 'XX')");
			$SQL -> execute(array(':username' => $username." - failed",':ip' => $ip));
			$error = error("Имя пользователя не существует в нашей системе.");
		}
		
		$SQLCheckLogin = $odb -> prepare("SELECT COUNT(*) FROM `users` WHERE `username` = :username AND `password` = :password");
		$SQLCheckLogin -> execute(array(':username' => $username, ':password' => SHA1(md5($password))));
		$countLogin = $SQLCheckLogin -> fetchColumn(0);
		if (!($countLogin == 1)){
			$SQL = $odb -> prepare("INSERT INTO `loginlogs` VALUES(:username, :ip, UNIX_TIMESTAMP(), 'XX')");
			$SQL -> execute(array(':username' => $username." - failed login",':ip' => $ip));
			$error = error('Введенный пароль недействителен.');
		}
		
		$SQL = $odb -> prepare("SELECT `status` FROM `users` WHERE `username` = :username");
		$SQL -> execute(array(':username' => $username));
		$status = $SQL -> fetchColumn(0);
		if ($status == 1){
			$ban = $odb -> query("SELECT `reason` FROM `bans` WHERE `username` = '$username'") -> fetchColumn(0);
			if(empty($ban)){ $ban = "Причина не указана."; }
			$error = error('Вы заблокированы. Причина: '.htmlspecialchars($ban));
		}
		// Check if 2auth enabled
		if(empty($error)){

			$SQL = $odb -> prepare("SELECT * FROM `users` WHERE `username` = :username");		$SQL -> execute(array(':username' => $username));
			$userInfo = $SQL -> fetch();
			$ipcountry = json_decode(file_get_contents("http://www.geoplugin.net/json.gp?ip=".$ip)) -> {'geoplugin_countryName'};
			if (empty($ipcountry)) {$ipcountry = 'XX';}
			$SQL = $odb -> prepare('INSERT INTO `loginlogs` VALUES(:username, :ip, UNIX_TIMESTAMP(), :ipcountry)');
			$SQL -> execute(array(':ip' => $ip, ':username' => $username, ':ipcountry' => $ipcountry));
			$_SESSION['username'] = $userInfo['username'];
			$_SESSION['ID'] = $userInfo['ID'];
			setcookie("username", $userInfo['username'], time() + 720000);
			header('Location: home.php');
			exit;

		}
	}

	if(isset($_POST['forgotPw']))	

	{
		$value = $_POST['input'];

		
		if(empty($value))
		{
			$error = error('The email was empty please try again.');
		}

		$SQL = $odb -> prepare("SELECT COUNT(`email`) FROM `users` WHERE `email` = :email");
		$SQL -> execute(array(':email' => $value));
		$status = $SQL -> fetchColumn(0);
		if ($status == 0){
			$error = error('Email does not exist!');
		}
			
			/// Change Password Here
			if(empty($error))
			{
			function generateRandomString($length = 10) {
				$characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
				$charactersLength = strlen($characters);
				$randomString = '';
				for ($i = 0; $i < $length; $i++) {
					$randomString .= $characters[rand(0, $charactersLength - 1)];
				}
				return $randomString;
			}
			$newpass = generateRandomString();

			//$SQL = $odb -> query("UPDATE `users` SET `password` = {$newpass} WHERE `ID` = {$userID}");

			$SQLUpdate = $odb -> prepare("UPDATE `users` SET `password` = :password WHERE `email` = :id");
			$SQLUpdate -> execute(array(':password' => SHA1(md5($newpass)), ':id' => $value));
			
			
			/// Email Send Here
			$mail->isSMTP();                         // Set mailer to use SMTP
			$mail->Host = $Shost;  // Specify main and backup SMTP servers
			$mail->SMTPAuth = $SAuth;                               // Enable SMTP authentication
			$mail->Username = $Susername;                 // SMTP username
			$mail->Password = $Spassword;                           // SMTP password
			$mail->SMTPSecure = 'tls';                            // Enable TLS encryption, `ssl` also accepted
			$mail->Port = $Sport;                                    // TCP port to connect to
			$mail->setFrom($Susername, $sitename);
			$mail->addAddress($value, $sitename);     // Add a recipient             // Name is optional
			$mail->addReplyTo('no-reply@dead.com', 'Sorry Guys!');
			$mail->isHTML(true);                                  // Set email format to HTML
			$mail->Subject = ''.$sitename.' - Password Rest';
			$mail->Body    = '<h3> You requested your password to be reset </h3> <br> Your new password is: <b>'.$newpass.'</b>  <br/> Please change this as soon as you can to make sure your safe. <br/> If you worry about your account being hacked enable 2auth using any authenticator app and setup 2auth for your account so guranteed safety!';

			if(!$mail->send()) {
				$error = success('Message could not be sent.');
				$error = success('Mailer Error: ' . $mail->ErrorInfo);
			} else {
				$error = success('Email has been sent with new passsword!');
			}
		}
	}

?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="description" content="">
<meta name="author" content="">
<link rel="icon" type="image/png" sizes="16x16" href="../plugins/images/favicon.png">
<title>MaoStress - Вход</title>
<!-- Bootstrap Core CSS -->
<link href="includes/theme/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet">
<!-- animation CSS -->
<link href="includes/theme/css/animate.css" rel="stylesheet">
<!-- Custom CSS -->
<link href="includes/theme/css/style.css" rel="stylesheet">
<!-- color CSS -->
<link href="includes/theme/css/colors/default-dark.css" id="theme"  rel="stylesheet">
<script src='https://www.google.com/recaptcha/api.js'></script>
<!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
<!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
<!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
    <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
<![endif]-->
</head>

<body>
<!-- Preloader -->
<div class="preloader">
  <div class="cssload-speeding-wheel"></div>
</div>
<section id="wrapper" class="login-register">
  <div class="login-box">
  <?php
					if(!empty($error)){
						echo ($error);
					}
				?>
    <div class="white-box">
      <form class="form-horizontal form-material" id="loginform" method="post">
        <h3 class="box-title m-b-20">Авторизация</h3>
        <div class="form-group ">
          <div class="col-xs-12">
            <input class="form-control" type="text" required="" name="login-username" placeholder="Имя пользователя">
          </div>
        </div>
        <div class="form-group">
          <div class="col-xs-12">
            <input class="form-control" type="password" required="" name="login-password"  placeholder="Пароль">
          </div>
        </div>
        <div class="form-group text-center m-t-20">
          <div class="col-xs-12">
            <button type="submit" name="doLogin" class="btn btn-info btn-lg btn-block text-uppercase waves-effect waves-light" type="submit">Вход</button>
          </div>
        </div>
        <div class="form-group m-b-0">
          <div class="col-sm-12 text-center">
            <p>У вас нет аккаунта? <a href="register.php" class="text-primary m-l-5"><b>Зарегестрироваться</b></a></p>
          </div>
        </div>
      </form>
      <form class="form-horizontal" id="recoverform" method="post">
        <div class="form-group ">
          <div class="col-xs-12">
            <h3>Recover Password</h3>
            <p class="text-muted">Enter your Email and instructions will be sent to you! </p>
          </div>
        </div>
        <div class="form-group ">
          <div class="col-xs-12">
            <input class="form-control" type="text" required="" name="input" placeholder="Email">
          </div>
        </div>
        <div class="form-group text-center m-t-20">
          <div class="col-xs-12">
            <button name="forgotPw" class="btn btn-primary btn-lg btn-block text-uppercase waves-effect waves-light" type="submit">Reset</button>
          </div>
        </div>
      </form>
    </div>
  </div>
</section>
<!-- jQuery -->
<script src="includes/theme/plugins/bower_components/jquery/dist/jquery.min.js"></script>
<!-- Bootstrap Core JavaScript -->
<script src="includes/theme/bootstrap/dist/js/bootstrap.min.js"></script>
<!-- Menu Plugin JavaScript -->
<script src="includes/theme/plugins/bower_components/sidebar-nav/dist/sidebar-nav.min.js"></script>

<!--slimscroll JavaScript -->
<script src="includes/theme/js/jquery.slimscroll.js"></script>
<!--Wave Effects -->
<script src="includes/theme/js/waves.js"></script>
<!-- Custom Theme JavaScript -->
<script src="includes/theme/js/custom.js"></script>
<!--Style Switcher -->
<script src="includes/theme/plugins/bower_components/styleswitcher/jQuery.style.switcher.js"></script>
</body>
</html>

