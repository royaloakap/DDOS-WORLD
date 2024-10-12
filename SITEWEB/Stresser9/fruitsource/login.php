<?php 
include "controls/database.php";
$page = "Login";
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

<body background="http://stormandsky.com/gif/9.gif">
<audio autoplay loop>
      <source src="song2.mp3">
</audio>
		<div class="container-fluid content">
		<div class="row">
					<div id="content" class="col-sm-12 full">
			<div class="row">
				<div class="login-box">
					
<center><img src="http://i.imgur.com/oNGZcEd.png" alt="Smiley face" height="70%" width="70%"></center>
<br/>
					
					<form class="form-horizontal login" action="" method="POST">
						<?php
if (!($user -> LoggedIn()))
{
	if (isset($_POST['loginBtn']))
	{
		$username = $_POST['username'];
		$password = $_POST['password'];
		if (!empty($username) && !empty($password))
		{
			if (!ctype_alnum($username) || strlen($username) < 4 || strlen($username) > 15)
			{
				echo '<div class="g_12"><div class="alert alert-danger"><strong>ERROR</strong>:Invalid username format</div></div>';
			}
			else
			{
				$SQLCheckLogin = $odb -> prepare("SELECT COUNT(*) FROM `users` WHERE `username` = :username AND `password` = :password");
				$SQLCheckLogin -> execute(array(':username' => $username, ':password' => SHA1($password)));
				$countLogin = $SQLCheckLogin -> fetchColumn(0);
				if ($countLogin == 1)
				{
					$SQLGetInfo = $odb -> prepare("SELECT `username`, `ID`,`status` FROM `users` WHERE `username` = :username AND `password` = :password");
					$SQLGetInfo -> execute(array(':username' => $username, ':password' => SHA1($password)));
					$userInfo = $SQLGetInfo -> fetch(PDO::FETCH_ASSOC);
					if ($userInfo['status'] == 0)
					{
						$_SESSION['username'] = $userInfo['username'];
						$_SESSION['ID'] = $userInfo['ID'];
						$Query = $odb-> query("INSERT INTO `logins` VALUES ('$username','$ip','$newcount','$time')");
						echo '<div class="g_12"><div class="alert alert-success"><strong>Success</strong>: You are now being redirected to Dashboard</div></div><meta http-equiv="refresh" content="2;url=index.php">';
					}
					else
					{
						echo '<div class="g_12"><div class="alert alert-danger"><strong>ERROR</strong>: Your user was banned</div></div>';
					}
				}
				else
				{
					echo '<div class="g_12"><div class="alert alert-danger"><strong>ERROR</strong>: Login Failed</div></div>';
				}
			}
		}
		else
		{
			echo '<div class="g_12"><div class="alert alert-danger"><strong>ERROR</strong>: Please fill in all fields</div></div>';
		}
	}
}
else
{
	header('location: index.php');
}
?>

<br/>
<br/>
						<fieldset class="col-sm-12">
							<div class="form-group">
							  	<div class="controls row">
									<div class="input-group col-sm-12">	
										<input type="text" class="form-control" name="username" id="username" placeholder="Username"/>
										
									</div>	
							  	</div>
							</div>
							<br/>
							<div class="form-group">
							  	<div class="controls row">
									<div class="input-group col-sm-12">	
										<input type="password" name="password" class="form-control" id="password" placeholder="Password"/>
										
									</div>	
							  	</div>
							</div>

								

							<div class="row">
							<br/>
<br/>
<br/>
<br/>
<br/>
								<button type="submit" name="loginBtn" class="btn btn-lg btn-primary col-xs-12">Login</button>
							
							</div>
								
						</fieldset>	

					</form>
<br/>
<br/>
					
					<center><a href="register.php">Don't have an account?<b>Sign Up!</b></a></center>
					
					<div class="clearfix"></div>				
						
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