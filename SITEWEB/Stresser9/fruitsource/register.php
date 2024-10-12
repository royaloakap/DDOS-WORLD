<?php 
include "controls/database.php";
$page = "Register";
?>
<!DOCTYPE html>
<html lang="en">
	<head>
    	<meta charset="utf-8">
	    <meta http-equiv="X-UA-Compatible" content="IE=edge">
	    <meta name="viewport" content="width=device-width, initial-scale=1">
	    <meta name="description" content="CleverAdmin - Bootstrap Admin Template">
		<meta name="author" content="Łukasz Holeczek">
		<meta name="keyword" content="CleverAdmin, Dashboard, Bootstrap, Admin, Template, Theme, Responsive, Fluid, Retina">
	    <link rel="apple-touch-icon-precomposed" sizes="144x144" href="assets/ico/apple-touch-icon-144-precomposed.png">
		<link rel="apple-touch-icon-precomposed" sizes="114x114" href="assets/ico/apple-touch-icon-114-precomposed.png">
		<link rel="apple-touch-icon-precomposed" sizes="72x72" href="assets/ico/apple-touch-icon-72-precomposed.png">
		<link rel="apple-touch-icon-precomposed" sizes="57x57" href="assets/ico/apple-touch-icon-57-precomposed.png">
		<link rel="shortcut icon" href="assets/ico/favicon.png">

	    <title><?php
				$getNames = $odb -> query("SELECT * FROM `admin`");
				while($Names = $getNames -> fetch(PDO::FETCH_ASSOC)) {
					echo $Names['bootername'];
				}
			?> - <?php echo $page ?></title>

	    <!-- Bootstrap core CSS -->
	    <link href="assets/css/bootstrap.min.css" rel="stylesheet">
		
		<!-- page css files -->
		<link href="assets/css/font-awesome.min.css" rel="stylesheet">
		<link href="assets/css/jquery-ui.min.css" rel="stylesheet">	

	    <!-- Custom styles for this template -->
	    <link href="assets/css/style.min.css" rel="stylesheet">

	    <!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
	    <!--[if lt IE 9]>
			<script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
			<script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
	    <![endif]-->
<style> 
@font-face {
   font-family: poppins;
   src: url(Poppins.woff);
}

body {
   font-family: poppins;
}
</style>
	</head>
</head>

<body>
		<div class="container-fluid content">
		<div class="row">
					<div id="content" class="col-sm-12 full">
			<div class="row">
				
				<div class="login-box">
					
					<center><img src="http://i.imgur.com/oNGZcEd.png" alt="Smiley face" height="70%" width="70%"></center>
					<br/>
<br/>
					<form class="form-horizontal register" action="" method="POST">
						<?php
if (isset($_POST['registerBtn']))
{
	$username = $_POST['username'];
	$password = $_POST['password'];
	$rpassword = $_POST['rpassword'];
	$email = $_POST['email'];
	$cap = $_POST['cap'];
	if($cap == "19" || $cap == "21") {
		$checkUsername = $odb -> prepare("SELECT COUNT(*) FROM `users` WHERE `username` = :username");
		$checkUsername -> execute(array(':username' => $username));
		$countUsername = $checkUsername -> fetchColumn(0);
		if (empty($username) || empty($password) || empty($rpassword) || empty($email))
		{
			echo '<div class="g_12"><div class="alert alert-danger"><strong>Error</strong>: Please fill in all fields</div></div>';
		}
		else
		{
			if (!ctype_alnum($username) || strlen($username) < 4 || strlen($username) > 15)
			{
				echo '<div class="g_12"><div class="alert alert-danger"><strong>Error</strong>:Username must be 4-15 length and alnum</div></div>';
			}
			else
			{
				if (!($countUsername == 0))
				{
					echo '<div class="g_12"><div class="alert alert-danger"><strong>Error</strong>:Username is taken</div></div>';
				}
				else
				{
					if (!filter_var($email, FILTER_VALIDATE_EMAIL))
					{
						echo '<div class="g_12"><div class="alert alert-danger"><strong>Error</strong>:Invalid Email</div></div>';
					}
					else
					{
						if ($password != $rpassword)
						{
							echo '<div class="g_12"><div class="alert alert-danger"><strong>Error</strong>:Passwords do not match</div></div>';
						}
						else
						{
							$insertUser = $odb -> prepare("INSERT INTO `users` VALUES(NULL, :username, :password, :email, 0, 55, 1513450186, 0)");
							$insertUser -> execute(array(':username' => $username, ':password' => SHA1($password), ':email' => $email));
							echo '<div class="g_12"><div class="alert alert-success"><strong>Success</strong>: You are now being redirected to Login</div></div><meta http-equiv="refresh" content="2;url=login.php">';
						}
					}
				}
			}
		} 
	} else {
		echo '<div class="g_12"><div class="alert alert-danger"><strong>Error</strong>:Captcha Invalid</div></div>';
	}
}
?>
						<fieldset class="col-sm-12">
							
							<div class="form-group">
							  	<label class="control-label" for="username">Username</label>
							  	<div class="controls row">
									<div class="input-group col-sm-12">
										<input type="text" class="form-control" name="username" id="username"/>
									</div>	
							  	</div>
							</div>
							
							<div class="form-group">
							  	<label class="control-label" for="password">Password</label>
							  	<div class="controls row">
									<div class="input-group col-sm-12">
										<input type="password" class="form-control" name="password" id="password"/>
									</div>	
							  	</div>
							</div>
							
							<div class="form-group">
							  	<label class="control-label" for="password">Repeat Password</label>
							  	<div class="controls row">
									<div class="input-group col-sm-12">
										<input type="password" class="form-control" name="rpassword" id="password"/>
									</div>	
							  	</div>
							</div>
							
							<div class="form-group">
							  	<label class="control-label" for="password">Email</label>
							  	<div class="controls row">
									<div class="input-group col-sm-12">
										<input type="text" class="form-control" name="email" id="email"/>
									</div>	
							  	</div>
							</div>
							
							<div class="form-group">
							  	<label class="control-label" for="password">
								<?php
									$rand = rand(0, 4);
									$array = array('What is 9+10?','What is 11+8?','What is 20+1?','What is 24-3?','What is 10+11?');
									echo $array[$rand];
								?>	
								</label>
							  	<div class="controls row">
									<div class="input-group col-sm-12">
										<input type="text" name="cap" class="form-control" placeholder="Captcha" id="captcha"/>
									</div>	
							  	</div>
							</div>

							
							<div class="row">

								<button type="submit" name="registerBtn" class="btn btn-primary btn-lg col-xs-12">Create Account!</button>
							
							</div>
							
							<div class="text-with-hr">
								<span>or do you have an account already?</span>
							</div>
							
						</fieldset>	

					</form>			
					
					<p>
						<center><a href="login.php" class="btn btn-default"><span>Already have an account?</span></a>
					</p>				
						
				</div>
			</div><!--/row-->
		
		</div>	
			
				</div><!--/row-->		
		
	</div><!--/container-->
		
		
	<!-- start: JavaScript-->
	<!--[if !IE]>-->

			<script src="assets/js/jquery-2.1.0.min.js"></script>

	<!--<![endif]-->

	<!--[if IE]>
	
		<script src="assets/js/jquery-1.11.0.min.js"></script>
	
	<![endif]-->

	<!--[if !IE]>-->

		<script type="text/javascript">
			window.jQuery || document.write("<script src='assets/js/jquery-2.1.0.min.js'>"+"<"+"/script>");
		</script>

	<!--<![endif]-->

	<!--[if IE]>
	
		<script type="text/javascript">
	 	window.jQuery || document.write("<script src='assets/js/jquery-1.11.0.min.js'>"+"<"+"/script>");
		</script>
		
	<![endif]-->
	<script src="assets/js/jquery-migrate-1.2.1.min.js"></script>
	<script src="assets/js/bootstrap.min.js"></script>	
	
	
	<!-- page scripts -->
	<script src="assets/js/jquery.icheck.min.js"></script>
	
	<!-- theme scripts -->
	<script src="assets/js/custom.min.js"></script>
	<script src="assets/js/core.min.js"></script>
	
	<!-- inline scripts related to this page -->
	<script src="assets/js/pages/login.js"></script>
	
	<!-- end: JavaScript-->
	
</body>
</html>