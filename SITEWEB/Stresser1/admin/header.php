<?php 

	if(basename($_SERVER['SCRIPT_FILENAME']) == basename(__FILE__)) die("Access denied");
	ob_start();
	
	require_once '../includes/app/config.php';
	require_once '../includes/app/init.php';
	
	if (!(empty($maintaince))) {
		header('Location: ../maintenance.php');
		exit;
	}
	
	if (!($user -> LoggedIn()) || !($user -> notBanned($odb))){
		header('location: ../relogin.php');
		die();
	}
	
	if(!$user->isAdmin($odb)){
		header('home.php');
		exit;
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
<title>MaoStress -> <?php echo $page; ?></title>
<!-- Bootstrap Core CSS -->
<link href="../includes/theme/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet">
<!-- Menu CSS -->
<link href="../includes/theme/plugins/bower_components/sidebar-nav/dist/sidebar-nav.min.css" rel="stylesheet">
<?php if($page == "Manage TOS") { ?> <link rel="stylesheet" href="../includes/theme/plugins/bower_components/html5-editor/bootstrap-wysihtml5.css" />
<?php } ?>
<!-- morris CSS -->
<link href="../includes/theme/plugins/bower_components/morrisjs/morris.css" rel="stylesheet">
<!-- animation CSS -->
<link href="../includes/theme/css/animate.css" rel="stylesheet">
<!-- Custom CSS -->
<link href="../includes/theme/css/style.css" rel="stylesheet">
<!-- color CSS -->
<link href="../includes/theme/css/colors/default-dark.css" id="theme"  rel="stylesheet">
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.2.1/Chart.bundle.js"></script>
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
<div id="wrapper">
  <!-- Navigation -->
  <nav class="navbar navbar-default navbar-static-top m-b-0">
    <div class="navbar-header"> <a class="navbar-toggle hidden-sm hidden-md hidden-lg " href="javascript:void(0)" data-toggle="collapse" data-target=".navbar-collapse"><i class="ti-menu"></i></a>
      <div class="top-left-part"><a href="home.php"> <h2> <b>⠀⠀MaoStress</h2></b><p></p></a></div>
      <ul class="nav navbar-top-links navbar-left hidden-xs">
       
      </ul>
      <ul class="nav navbar-top-links navbar-right pull-right">
        <!-- /.dropdown -->
        <li class="dropdown"> <a class="dropdown-toggle profile-pic" data-toggle="dropdown" href="#"> <b>Account</b> </a>
           <ul class="dropdown-menu animated flipInY">
                <li><a href="../profile.php"><i class="ti-user"></i> My Profile</a></li>
                <li><a href="../support.php"><i class="ti-settings"></i> Support Tickets</a></li>
                <li role="separator" class="divider"></li>
                <li><a href="../logout.php"><i class="fa fa-power-off"></i> Logout</a></li>
              </ul>
          <!-- /.dropdown-user -->
        </li>
        <!-- /.dropdown -->
      </ul>
    </div>
    <!-- /.navbar-header -->
    <!-- /.navbar-top-links -->
    <!-- /.navbar-static-side -->
  </nav>
  <!-- Left navbar-header -->
  <div class="navbar-default sidebar" role="navigation">
    <div class="sidebar-nav navbar-collapse slimscrollsidebar">
      <ul class="nav" id="side-menu">
        <?php
			/// Guest Only See Main Menu (You have to be member to see the hub)
		?>
        <li class="nav-small-cap m-t-10">⠀⠀⠀⠀⠀Main & Settings</li>
        <li> <a href="home.php" class="waves-effect"><i class="linea-icon linea-basic fa-fw" data-icon="v"></i> <span class="hide-menu"> Dashboard </a></li>
		<li> <a href="#" class="waves-effect"><i data-icon="&#xe008;" class="fa fa-sticky-note fa-fw"></i> <span class="hide-menu">Settings<span class="fa arrow"></span></span></a>
        <ul class="nav nav-second-level">
		<li> <a href="hsettings.php" class="waves-effect"> <span class="hide-menu"> Hub Settings </span></a></li>
		<li> <a href="gsettings.php" class="waves-effect"> <span class="hide-menu"> General Settings </span></a></li>
        </ul>
        </li>
		<li> <a href="#" class="waves-effect"><i data-icon="&#xe008;" class="fa fa-sticky-note fa-fw"></i> <span class="hide-menu">General<span class="fa arrow"></span></span></a>
        <ul class="nav nav-second-level">
		<li> <a href="umanage.php" class="waves-effect"> <span class="hide-menu"> Manage Users </span></a></li>
		<li> <a href="nmanage.php" class="waves-effect"> <span class="hide-menu"> Manage News </span></a></li>
		<li> <a href="pmanage.php" class="waves-effect"> <span class="hide-menu"> Manage Plans </span></a></li>
		<li> <a href="bmanage.php" class="waves-effect"> <span class="hide-menu"> Manage Blacklist </span></a></li>
		<li> <a href="tosmanage.php" class="waves-effect"> <span class="hide-menu"> Manage Terms Of Services </span></a></li>
        </ul>
        </li>
		<?php
			/// The can only been seen by membership active now
		?>
		<li> <a href="#" class="waves-effect "><i data-icon="&#xe008;" class="fa fa-sticky-note fa-fw"></i> <span class="hide-menu">Logs<span class="fa arrow"></span></span></a>
          <ul class="nav nav-second-level">
		<li> <a href="llogs.php" class="waves-effect"> <span class="hide-menu"> Login </span></a></li>
		<li> <a href="hlogs.php" class="waves-effect"> <span class="hide-menu"> Attack Logs </span></a></li>
          </ul>
        </li>
        <li class="nav-small-cap">⠀⠀⠀⠀⠀Miscellaneous</li>
		<li> <a href="faq.php" class="waves-effect"><i class="fa fa-question fa-fw" data-icon="v"></i> <span class="hide-menu"> FAQ</span></a></li>
		<li> <a href="cards.php" class="waves-effect"><i class="fa fa-credit-card fa-fw" data-icon="v"></i> <span class="hide-menu"> Gift Cards</span></a></li>
		<li> <a href="liveattacks.php" class="waves-effect"><i class="fa fa-hdd-o fa-fw" data-icon="v"></i> <span class="hide-menu"> Live Attacks </span></a></li>
		<li> <a href="https://mao-stress.cf/home.php" class="waves-effect"><i class="fa fa-arrow-left fa-fw" data-icon="v"></i> <span class="hide-menu"> Return to home </span></a></li>
    </div>
  </div>
  <!-- Left navbar-header end -->