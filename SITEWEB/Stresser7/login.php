<?php
require '@/config.php';
require '@/init.php';
if ($user -> LoggedIn())
{
header('Location: index.php');
}
?>
<!DOCTYPE html>
<!--[if IE 9]>         <html class="no-js lt-ie10"> <![endif]-->
<!--[if gt IE 9]><!--> <html class="no-js"> <!--<![endif]-->
<head>
<script src="../assets/js/spinner.js"></script>
		<script>
		function login()
		{
		var username=$('#loginusername').val();
		var password=$('#loginpassword').val();
		document.getElementById("logindiv").style.display="none";
		document.getElementById("loginimage").style.display="inline";
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
			document.getElementById("logindiv").innerHTML=xmlhttp.responseText;
			document.getElementById("loginimage").style.display="none";
			document.getElementById("logindiv").style.display="inline";
			if (xmlhttp.responseText.search("Redirecting") != -1)
			{
			setInterval(function(){window.location="/index.php"},3000);
			}
			}
		  }
		xmlhttp.open("POST","ajax/login.php?type=login",true);
		xmlhttp.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		xmlhttp.send("username=" + username + "&password=" + password);
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
           <div id="login-container">
   <h1 class="display-4 text-center mb-3">Sign in</h1>
  <div id="logindiv" style="display:none"></div>
<img id="loginimage" src="/img/jquery.easytree/loading.gif" style="display:none"/>
		   <h5 class="text-muted font-weight-normal mb-4">Welcome back! Log in to your account.</h5>
<form class="forms-sample">
<h6 class="card-title">Username</h6>
<div class="form-group">
<div class="col-xs-12">
<input type="text" id="loginusername" class="form-control" placeholder="Your username..">
 </div>
  </div>
  <h6 class="card-title">Password</h6>
 <div class="form-group">
<div class="col-xs-12">
 <input type="password" id="loginpassword" class="form-control" placeholder="Your password..">
  </div>
</div>
 <div class="form-group form-actions">
<div class="col-xs-4 text-center">
<button type="button" onclick="login()"  class="btn btn-effect-ripple btn-sm btn-primary" onclick="showSwal('mixin')">Let's Go<span class="spinner-grow spinner-grow-sm" role="status" aria-hidden="true"></span></button>

 </div>
  </div>
   </div>
    </div>
	<h6  class="text-muted text-center"> Don't have an account yet? <a href="register.php">Sign up</a>.</h6>
     </div>
    </div>
   </div>    
  </div>
    </div>

  
<!-- base js -->
    <script src="../js/app.js"></script>
    <script src="../assets/plugins/feather-icons/feather.min.js"></script>
    <!-- end base js -->

    <!-- plugin js -->
        <!-- end plugin js -->

    <!-- common js -->
    <script src="../assets/js/template.js"></script>
    <!-- end common js -->
<script src="js/app.js"></script><script src="js/pages/readyLogin.js"></script>
<script type="text/javascript">
        $("#signinForm").validate({
		rules: {
			login: "required",
			password: "required"
		},
		messages: {
			firstname: "Please enter your login",
			lastname: "Please enter your password"			
		}
	});            
</script>
<script type="text/javascript">
var Tawk_API=Tawk_API||{}, Tawk_LoadStart=new Date();
(function(){
var s1=document.createElement("script"),s0=document.getElementsByTagName("script")[0];
s1.async=true;
s1.src='https://embed.tawk.to/5deffb0043be710e1d217ca7/default';
s1.charset='UTF-8';
s1.setAttribute('crossorigin','*');
s0.parentNode.insertBefore(s1,s0);
})();
</script>
</body>
</html>