<?php 

header("X-XSS-Protection: 1; mode=block");

	ob_start();
	require_once 'vvv/avg/mycon.php';
	require_once 'vvv/avg/usv.php';

	if (!(empty($maintaince))) {
		header('Location: maintenance.php');
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
		header('Location: dashboard.php');
		exit;
	}

	$db_connection123 = mysqli_connect(DB_HOST, DB_USERNAME, DB_PASSWORD, DB_NAME);
	if (!$db_connection123)
		die(ERROR_MESSAGE);
	
	if(isset($_POST['doRegister'])){
		$username = mysqli_real_escape_string($db_connection123, $_POST['username']);
		$password = mysqli_real_escape_string($db_connection123, $_POST['password']);
		$rpassword = mysqli_real_escape_string($db_connection123, $_POST['rpassword']);
		
		if(empty($username) || empty($password) || empty($rpassword)){
			$error = error('Please enter all fields');
		}


		//Check if the username is legit
		if (!ctype_alnum($username) || strlen($username) < 4 || strlen($username) > 15){
			$error = error('Username must be  alphanumberic and 4-15 characters in length');
		}
		
		//Check referral
		$referral='0';
		
		if(empty($referrer))
		{
			$referrer = '0';
		}

		//Check if user is available
		$SQL = $odb -> prepare("SELECT COUNT(*) FROM `users` WHERE `username` = :username");
		$SQL -> execute(array(':username' => $username));
		$countUser = $SQL -> fetchColumn(0);
		if ($countUser > 0){
			$error = error('Username is already taken');
		}
		
		//Compare first to second password
		if ($password != $rpassword){
			$error = error('Passwords do not match');
		}
		
		session_start();
        if($_POST['captcha'] != $_SESSION['rand_code']){
            $error = error('Wrong Captcha!');
		}
   
		
		//Make registeration
		if(empty($error)){
				
					$insertUser = $odb -> prepare("INSERT INTO `users` VALUES(NULL, :username, :password, 0, 0, 0, 0, :referral, 0, 0, 0, 0,  :refered, null)");
					$resultInsert = $insertUser -> execute(array(':username' => $username, ':password' => SHA1(md5($password)), ':referral' => $referral, ':refered' => $referrer));
					if (!$resultInsert) {
						var_dump($insertUser->errorInfo());
						die();
					}
			        $SQL = $odb -> prepare("SELECT * FROM `users` WHERE `username` = :username");		$SQL -> execute(array(':username' => $username));
			        $userInfo = $SQL -> fetch();
			        $ipcountry = json_decode(file_get_contents("http://www.geoplugin.net/json.gp?ip=".$ip)) -> {'geoplugin_countryName'};
			        if (empty($ipcountry)) {$ipcountry = 'XX';}
			        $SQL = $odb -> prepare('INSERT INTO `loginlogs` VALUES(:username, :ip, UNIX_TIMESTAMP(), :ipcountry)');
			        $SQL -> execute(array(':ip' => $ip, ':username' => $username, ':ipcountry' => $ipcountry));
			        $_SESSION['username'] = $userInfo['username'];
			        $_SESSION['ID'] = $userInfo['ID'];
					$error = success('You have succesfully registered! Redirecting...<meta http-equiv="refresh" content="3;URL=dashboard.php">');	
					setcookie("username", $userInfo['username'], time() + 720000);
				
			
			
		}
	}

?>

<html><head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta name="description" content="">
	<meta name="author" content="">
	<link rel="icon" type="image/png" sizes="16x16" href="assets/images/logo-icon.png">
	<title><?php echo $sitename; ?> | Register</title>
	<link href="assets/css/style.min.css" rel="stylesheet">
</head>
<body>
	<div class="main-wrapper">
		<div class="preloader">
			<div class="lds-ripple">
				<div class="lds-pos"></div>
				<div class="lds-pos"></div>
			</div>
		</div>
		<div class="auth-wrapper d-flex no-block justify-content-center align-items-center position-relative">
			<div class="row justify-content-center">
					<div class="col-lg-5">
						<div class="p-3">
						  <?php
					if(!empty($error)){
						echo ($error);
					}
				?>
							<div class="card">
								<div class="card-body">
									<h2 class="card-title text-center"><?php echo $sitename; ?></h2>
									<h3 class="card-title text-center">Register<h3>
									<form class="mt-4" method="post">
										<div class="row">
											<div class="col-lg-12">
												<div class="form-group">
													<label class="text-white" for="username">Username</label>
													<input class="form-control" id="username" name="username" type="text" placeholder="enter your username">
												</div>
											</div>
											<div class="col-lg-12">
												<div class="form-group">
													<label class="text-white" for="password">Password</label>
													<input class="form-control" id="password" name="password" type="password" placeholder="enter your password">
												</div>
											</div>
											<div class="col-lg-12">
												<div class="form-group">
													<label class="text-white" for="rpassword">Repeat Password</label>
													<input class="form-control" id="rpassword" name="rpassword" type="password" placeholder="repeat your password">
												</div>
											</div>
											<div class="col-lg-12">
												<div class="form-group">
													<center>
														<img src="captcha.php" id="captcha_image" title="Click to update" onclick="this.src='captcha.php?rand=' + (+new Date());">
													</center>
													<label class="text-white" for="captcha">Captcha</label>
													<input class="form-control" id="captcha" name="captcha" type="text" placeholder="enter the text from the picture">
												</div>
											</div>
											<!--<div class="col-lg-12">
												<div class="form-group">
													<div class="custom-control custom-checkbox">
														<input type="checkbox" class="custom-control-input" id="agreement" disabled="" checked="">
														<label class="custom-control-label" for="agreement">I agree with <a href="javascript:void(0)" class="text-primary" data-toggle="modal" data-target="#tos-modal">Terms of Service</a></label>
													</div>
												</div>
											</div>-->
											<div class="col-lg-12 text-center">
												<div class="form-group">
													<button name="doRegister" type="submit" class="btn btn-block btn-primary">Sign Up</button>
												</div>
											</div>
										</div>
									</form>
								</div>
							</div>
							<div class="col-lg-12 text-center mt-3">
								Already have an account? <a href="login.php" class="text-primary">Sign In</a>
							</div>
						</div>
                    </div>
			</div>
		</div>
	</div>
	<div class="modal fade" id="tos-modal" tabindex="-1" role="dialog" aria-labelledby="tosModalTitle" aria-hidden="true">
		<div class="modal-dialog modal-dialog-scrollable" role="document">
			<div class="modal-content">
				<div class="modal-header modal-colored-header bg-primary">
					<h5 class="modal-title" id="tosModalTitle">Terms of Service</h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">×</span>
					</button>
				</div>
				<div class="modal-body">
					<h4><u>1. Responsability</u></h4>
					<p>By using the Server Stress Testing services provided by <?php echo $sitename; ?>, you agree to be responsible for all actions and consequences..</p>
					<hr>
					<h4><u>2. Privacy</u></h4>
					<p>Your privacy is very important to us, that is why we do not store attack logs, IP addresses, or any device information.</p>
					<hr>
					<h4><u>3. Service</u></h4>
					<p>We purposes our Server Stress Testing services to individuals and business only for personal network security testing. You are not authorized to use our services for interrupt the connectivity of a server/home connection/network. Use this service only on your own server/network, if this term is broken, your account will be permanently banned. Attacking government and educational websites is strictly forbidden!</p>
					<hr>
					<h4><u>4. Limitation of Warranties</u></h4>
					<p>By using our website, you understand and agree that all Resources we provide are "as is" and "as available". This means that we do not represent or warrant to you that:
					<br><br>
					- the use of our Resources will meet your needs or requirements;
					<br><br>
					- the use of our Resources will be uninterrupted, timely, secure or free from errors;
					<br><br>
					- the information obtained by using our Resources will be accurate or reliable, and any defects in the operation or functionality of any Resources we provide will be repaired or corrected.</p>
					<hr>
					<h4><u>5. Limitation of Liability</u></h4>
					<p>In conjunction with the Limitation of Warranties as explained above, you expressly understand and agree that any claim against us will be limited to the amount you paid, if any, for use of products and/or services. <?php echo $sitename; ?> will not be liable for any direct, indirect, incidental, consequential or exemplary loss or damages which may be incurred by you as a result of using our Resources, or as a result of any changes, data loss or corruption, cancellation, loss of access, or downtime to the full extent that applicable limitation of liability laws apply.</p>
					<hr>
					<h4><u>6. Commitment</u></h4>
					<p>The terms of services will be valid from your first use of our services. If you break our TOS, your account will be permanently banned from our services.</p>
					<hr>
					<h4><u>7. Refunds</u></h4>
					<p>We do not provide refunds from any kind, Abuse of our service will be a ground for immediate termination of service or account without refunds.</p>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-primary" data-dismiss="modal">Close</button>
				</div>
			</div>
		</div>
	</div>
	<script src="assets/js/jquery.min.js "></script>
	<script src="assets/js/popper.min.js "></script>
	<script src="assets/js/bootstrap.min.js "></script>
	<script>
		$(".preloader ").fadeOut();
	</script>


</body></html>