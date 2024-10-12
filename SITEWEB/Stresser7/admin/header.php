<?php 
if (basename($_SERVER['SCRIPT_FILENAME']) == basename(__FILE__)) {exit("NOT ALLOWED");}
ob_start();
require_once '../@/config.php';
require_once '../@/init.php';
if (!($user -> LoggedIn()))
{
	header('location: ../login.php');
	die();
}
if (!($user -> isSupporter($odb)))
{
	header('location: ../index.php');
	die();
}
?>
<!DOCTYPE html>
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
    <body>
      <nav class="navbar">
  <a href="#" class="sidebar-toggler">
    <i data-feather="menu"></i>
  </a>
  <div class="navbar-content">
    <ul class="navbar-nav">
      <li class="nav-item dropdown nav-profile">
        <a class="nav-link dropdown-toggle" href="#" id="profileDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
          <img src="../assets/images/faces/astronaut.png" alt="profile">
		            <div class="indicator">
            <div class="circle"></div>
          </div>
        </a>
        <div class="dropdown-menu" aria-labelledby="profileDropdown">
          <div class="dropdown-header d-flex flex-column align-items-center">
            <div class="figure mb-3">
              <img src="../assets/images/faces/astronaut.png" alt="">
            </div>
          </div>
          <div class="dropdown-body">
            <ul class="profile-nav p-0 pt-3">
              <li class="nav-item">
                <a href="/profile.php" class="nav-link">
                  <i data-feather="user"></i>
                  <span>Profile</span>
                </a>
              </li>
              <li class="nav-item">
                <a href="logout.php"" class="nav-link">
                  <i data-feather="log-out"></i>
                  <span>Log Out</span>
                </a>
              </li>
            </ul>
          </div>
        </div>
      </li>
    </ul>
  </div>
</nav>
  <script src="assets/js/spinner.js"></script>
  <div class="main-wrapper" id="app">
    <nav class="sidebar">
  <div class="sidebar-header">
    <a href="index.php" class="sidebar-brand" >
    <?php echo htmlspecialchars($sitename); ?>
    </a>	
  </div>
  <div class="sidebar-body">
    <ul class="nav">
	<button type="button" class="btn btn-primary" disabled>User ID: <?php echo $_SESSION['ID']; ?></button>
      <li class="nav-item nav-category">Main</li>
      <li class="nav-item">
        <a href="index.php" class="nav-link">
          <i class="link-icon" data-feather="airplay"></i>
          <span class="link-title">Dashboard</span>
        </a>
      </li>
	   <li class="nav-item ">
        <a href="/admin/gsetting.php" class="nav-link">
          <i class="link-icon" data-feather="inbox"></i>
          <span class="link-title">General Manager</span>
        </a>
      </li>
      <li class="nav-item ">
        <a href="/admin/pmanager.php" class="nav-link">
          <i class="link-icon" data-feather="shopping-cart"></i>
          <span class="link-title">Plan Manager</span>
        </a>
      </li>
      <li class="nav-item ">
        <a href="/admin/addons.php" class="nav-link">
          <i class="link-icon" data-feather="gift"></i>
          <span class="link-title">Addon Manager</span>
        </a>
      </li>
       <li class="nav-item ">
        <a href="/admin/servers.php" class="nav-link">
          <i class="link-icon" data-feather="server"></i>
          <span class="link-title">Api Manager</span>
        </a>
      </li>
		      <li class="nav-item ">
        <a href="/admin/methods.php" class="nav-link">
          <i class="link-icon" data-feather="zap"></i>
          <span class="link-title">Methods Manager</span>
        </a>
      </li>
	        <li class="nav-item ">
        <a href="/admin/news.php" class="nav-link">
          <i class="link-icon" data-feather="alert-triangle"></i>
          <span class="link-title">News Manager</span>
        </a>
      </li>
      <li class="nav-item ">     
      <li class="nav-item ">
        <a href="/admin/support.php" class="nav-link">
          <i class="link-icon" data-feather="book-open"></i>
          <span class="link-title">Support Manager</span>
        </a>
      </li>
      <li class="nav-item ">
        <a href="/admin/muser.php" class="nav-link">
          <i class="link-icon" data-feather="user"></i>
          <span class="link-title">User Manager</span>
        </a>
      </li>
      <li class="nav-item ">
        <a href="/admin/mfaq.php" class="nav-link">
          <i class="link-icon" data-feather="edit"></i>
          <span class="link-title">Methods Descriptions</span>
        </a>
      </li>
      <li class="nav-item ">
        <a href="/index.php" class="nav-link">
          <i class="link-icon" data-feather="log-out"></i>
          <span class="link-title">Home</span>
        </a>
      </li>  	  
      </li>       						
    </ul>
  </div>
</nav>

					<!--Start of Tawk.to Script-->
  <!-- base js -->
    <script src="js/app.js"></script>
    <script src="assets/plugins/feather-icons/feather.min.js"></script>
    <script src="assets/plugins/perfect-scrollbar/perfect-scrollbar.min.js"></script>
    <!-- end base js -->

    <!-- plugin js -->
      <script src="assets/plugins/chartjs/Chart.min.js"></script>
  <script src="assets/plugins/jquery.flot/jquery.flot.js"></script>
  <script src="assets/plugins/jquery.flot/jquery.flot.resize.js"></script>
  <script src="assets/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js"></script>
  <script src="assets/plugins/apexcharts/apexcharts.min.js"></script>
  <script src="assets/plugins/progressbar-js/progressbar.min.js"></script>
    <!-- end plugin js -->

    <!-- common js -->
    <script src="assets/js/template.js"></script>
    <!-- end common js -->

      <script src="assets/js/dashboard.js"></script>
  <script src="assets/js/datepicker.js"></script>
<!--End of Tawk.to Script-->
