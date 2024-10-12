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
<head>
 <title><?php echo htmlspecialchars($sitename); ?></title>
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  
  <!-- CSRF Token -->
  <meta name="_token" content="str5hm5z8d6ux1jpmcILUXYo2rVyGNeI2uayBt35">
  
  <link rel="shortcut icon" href="favicon/favicon.ico">

  <!-- plugin css -->
  <link media="all" type="text/css" rel="stylesheet" href="assets/fonts/feather-font/css/iconfont.css">
  <link media="all" type="text/css" rel="stylesheet" href="assets/plugins/perfect-scrollbar/perfect-scrollbar.css">
  <!-- end plugin css -->

    <link media="all" type="text/css" rel="stylesheet" href="assets/plugins/bootstrap-datepicker/css/bootstrap-datepicker.min.css">

  <!-- common css -->
  <link media="all" type="text/css" rel="stylesheet" href="css/app.css">
  <!-- end common css -->

    
  <!-- Global site tag (gtag.js) - Google Analytics start -->
  <script async src="https://www.googletagmanager.com/gtag/js?id=UA-146586338-1"></script>
  <script>
    window.dataLayer = window.dataLayer || [];
    function gtag(){dataLayer.push(arguments);}
    gtag('js', new Date());

    gtag('config', 'UA-146586338-1');
  </script>
    </head>
</head>
<body>
<script src="../assets/js/spinner.js"></script>
  <div class="main-wrapper" id="app">
    <div class="page-wrapper full-page">
      <div class="page-content d-flex align-items-center justify-content-center">
         <div class="col-md-4 stretch-card">
            <div class="card">
              <div class="card-body">
			  <a class="btn btn-primary" href="login.php" role="button">Log in</a>
           <div id="login-container">
   <h1 class="display-4 text-center mb-3">Sign up</h1>
    <div id="registerdiv" style="display:inline"></div>
  <div id="registerdiv" style="display:none"></div>
<img id="registerimage" src="img/jquery.easytree/loading.gif" style="display:none"/>
	<h5 class="text-muted font-weight-normal mb-4">Free access to our dashboard.</h5>
     <div class="form-horizontal">
       <div class="form-group">
        <div class="col-xs-12">
         <input type="text" id="username" class="form-control" placeholder="Username">
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
<div class="col-xs-4 text-center">
<button class="btn btn-effect-ripple btn-primary" type="button" onclick="register()"><i class="fa fa-plus"></i> Create Account<span class="spinner-grow spinner-grow-sm" role="status" aria-hidden="true"></span></button>
 </div>
  </div>
   </div>
    </div>
     </div>
      </div>
       </div>
	     </div>
<?php include("@/footer.php"); ?>
    <script src="../js/app.js"></script>
    <script src="../assets/plugins/feather-icons/feather.min.js"></script>
    <!-- end base js -->

    <!-- plugin js -->
        <!-- end plugin js -->

    <!-- common js -->
    <script src="../assets/js/template.js"></script>
    <!-- end common js -->
<script src="js/app.js"></script><script src="js/pages/readyLogin.js"></script>
</body>
</html>