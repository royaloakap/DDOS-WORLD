<?php
require '@/config.php';
require '@/init.php';
session_start();
unset($_SESSION['captcha']);
$_SESSION['captcha'] = rand(1, 100);
$x1 = rand(2,10);
$x2 = rand(1,10);
$x = SHA1(($x1 + $x2).$_SESSION['captcha']);
if ($user -> LoggedIn())
{
header('Location: index.php');
}
?>
<!DOCTYPE html>
<!--[if IE 9]>         <html class="no-js lt-ie10"> <![endif]-->
<!--[if gt IE 9]><!--> <html class="no-js"> <!--<![endif]-->
<head>
		<script>
		function register()
		{
		var username=$('#username').val();
		var password=$('#password').val();
		var rpassword=$('#rpassword').val();
		var email=$('#email').val();
		var scode=$('#scode').val();
		var question=$('#question').val();
		var answer="<?php echo $x; ?>";
		document.getElementById("registerdiv").style.display="none";
		document.getElementById("registerimage").style.display="inline"; 
		var xmlhttp;
		if (window.XMLHttpRequest)
		  {// code for IE7+, Firefox, Chrome, Opera, Safari
		  xmlhttp=new XMLHttpRequest();
		  }
		else
		  {// code for IE6, IE5
		  xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
		  }
		xmlhttp.onreadystatechange=function()
		  {
		  if (xmlhttp.readyState==4 && xmlhttp.status==200)
			{
			document.getElementById("registerdiv").innerHTML=xmlhttp.responseText;
			document.getElementById("registerimage").style.display="none";
			document.getElementById("registerdiv").style.display="inline";
			if (xmlhttp.responseText.search("Redirecting") != -1)
			{
			setInterval(function(){window.location="index.php"},3000);
			}
			}
		  }
		xmlhttp.open("POST","ajax/login.php?type=register",true);
		xmlhttp.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		xmlhttp.send("username=" + username + "&password=" + password + "&rpassword=" + rpassword + "&scode=" + scode + "&email=" + email + "&question=" + question + "&answer=" + answer);
		}
		</script>
        <meta charset="utf-8">

        <title><?php echo htmlspecialchars($sitename); ?> - Register</title>
        <meta name="author" content="StrikeREAD">
        <meta name="robots" content="noindex, nofollow">
        <meta name="viewport" content="width=device-width,initial-scale=1,maximum-scale=1.0">
        <link rel="shortcut icon" href="img/favicon.png">
        <link rel="apple-touch-icon" href="img/icon57.png" sizes="57x57">
        <link rel="apple-touch-icon" href="img/icon72.png" sizes="72x72">
        <link rel="apple-touch-icon" href="img/icon76.png" sizes="76x76">
        <link rel="apple-touch-icon" href="img/icon114.png" sizes="114x114">
        <link rel="apple-touch-icon" href="img/icon120.png" sizes="120x120">
        <link rel="apple-touch-icon" href="img/icon144.png" sizes="144x144">
        <link rel="apple-touch-icon" href="img/icon152.png" sizes="152x152">
        <link rel="apple-touch-icon" href="img/icon180.png" sizes="180x180">
        <link rel="stylesheet" href="css/bootstrap.min.css">
        <link rel="stylesheet" href="css/plugins.css">
        <link rel="stylesheet" href="css/main.css">
	   <link rel="stylesheet" href="css/themes/amethyst.css" id="theme-link">
        <link rel="stylesheet" href="css/themes.css">
		<link rel="stylesheet" href="css/themes/amethyst.css" id="theme-link">
	   <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
        <script src="js/vendor/modernizr-2.8.1.min.js"></script>
	   <style type="text/css">.jqstooltip { width: auto !important; height: auto !important; position: absolute;left: 0px;top: 0px;visibility: hidden;background: #000000;color: white;font-size: 11px;text-align: left;white-space: nowrap;padding: 5px;z-index: 10000;}.jqsfield { color: white;font: 10px arial, san serif;text-align: left;}</style>
</head>
<body>
<img src="back.jpg" alt="Full Background" class="full-bg animation-pulseSlow"></img>
<div id="login-container">
<h1 class="h2 text-light text-center push-top-bottom animation-slideDown">
<i class="fa fa-plus"></i> <strong>Create Account</strong>
</h1>
<div id="registerdiv" style="display:inline"></div>
<div class="block animation-fadeInQuickInv">
<div class="block-title">
<div class="block-options pull-right">
<a href="login.php" class="btn btn-effect-ripple btn-primary" data-toggle="tooltip" data-placement="left" title="Back to login"><i class="fa fa-user"></i></a>
</div>
<h2>Register <img id="registerimage" src="img/jquery.easytree/loading.gif" style="display:none"/></h2>
</div>
<div class="form-horizontal">
<div class="form-group">
<div class="col-xs-12">
<input type="text" id="username" class="form-control" placeholder="Username">
</div>
</div>
<div class="form-group">
<div class="col-xs-12">
<input type="text" id="email" class="form-control" placeholder="Email">
</div>
</div>
<div class="form-group">
<div class="col-xs-12">
<input type="password" id="password" class="form-control" placeholder="Password">
</div>
</div>
<div class="form-group">
<div class="col-xs-12">
<input type="password" id="rpassword" class="form-control" placeholder="Verify Password">
</div>
</div>
<div class="form-group">
<div class="col-xs-12">
<input type="text" id="scode" class="form-control" placeholder="Secret Code, 4 digits">
</div>
</div>
<div class="form-group">
<div class="col-xs-12">
<input type="text" id="question" class="form-control" placeholder="<?php echo ''.$x1.'+'.$x2.'?'; ?>">
</div>
</div>
<div class="form-group form-actions">
<div class="col-xs-6">
<label class="csscheckbox csscheckbox-primary" data-toggle="tooltip" title="Agree to the terms">
<input type="checkbox" name="keep" >
<span></span>
</label>
<a href="<?php echo htmlspecialchars($tos); ?>" target="_blank">I agree Terms of Service</a></a>
</div>
<div class="col-xs-4 text-right">
<button class="btn btn-effect-ripple btn-default" type="button" onclick="register()"><i class="fa fa-plus"></i> Create Account</button>
</div>
</div>
</div>
</div>
<footer class="text-muted text-center animation-pullUp">
<small><span id="year-copy"></span> &copy; <a href="<?php echo htmlspecialchars($siteurl); ?>" target="_blank"><?php echo htmlspecialchars($sitename); ?></a></small>
</footer>
</div>
<script src="js/vendor/jquery-2.1.4.min.js"></script>
<script src="js/vendor/bootstrap.min.js"></script>
<script src="js/plugins.js"></script>
<script src="js/app.js"></script><script src="js/pages/readyLogin.js"></script>
</body>
</html>