<?php 

header("X-XSS-Protection: 1; mode=block");

	if(basename($_SERVER['SCRIPT_FILENAME']) == basename(__FILE__)) die("Access denied");
	ob_start();
	
	require_once '../vvv/avg/mycon.php';
	require_once '../vvv/avg/usv.php';
	
	if (!(empty($maintaince))) {
		header('Location: ../maintenance.php');
		exit;
	}
	
	if (!($user -> LoggedIn()) || !($user -> notBanned($odb))){
		header('location: ../dashboard.php');
		die();
	}
	
	if(!$user->isAdmin($odb)){
		header('dashboard.php');
		exit;
	}
	
	
?>

<!DOCTYPE html>
<html lang="en"><head>
<head>
	<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.8.1/css/all.css" integrity="sha384-50oBUHEmvpQ+1lW4y57PTFmhCaXp0ML5d60M1M7uH2+nqUivzIebhndOJK28anvf" crossorigin="anonymous">
</head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta name="description" content="">
	<meta name="author" content="">
	<link rel="icon" type="image/png" sizes="16x16" href="../assets/images/logo-icon.png">
	<link href="../assets/css/c3.min.css" rel="stylesheet">
	<link href="../assets/css/chartist.min.css" rel="stylesheet">
	<link href="../assets/css/themify-icons.css" rel="stylesheet">
	<link href="../assets/css/jquery-jvectormap-2.0.2.css" rel="stylesheet">
	<link href="../assets/css/style.min.css" rel="stylesheet">
</head>
<body>
	<div class="preloader">
		<div class="lds-ripple">
<div class="spinner-grow" role="status">
  <span class="sr-only">Loading...</span>
</div>
		</div>
	</div>
	<div id="main-wrapper" data-theme="light" data-layout="vertical" data-navbarbg="skin6" data-sidebartype="full" data-sidebar-position="fixed" data-header-position="fixed" data-boxed-layout="full">
		<header class="topbar" data-navbarbg="skin6">
			<nav class="navbar top-navbar navbar-light">
				<div class="navbar-header" data-logobg="skin6">
					<a class="nav-toggler waves-effect waves-light d-block d-md-none" href="javascript:void(0)"><i class="ti-menu ti-close"></i></a>
					<div class="navbar-brand">
						<b class="logo-icon text-center">
							<img src="../assets/images/logo-icon.png" alt="homepage" class="dark-logo">
							<img src="../assets/images/logo-icon.png" alt="homepage" class="light-logo">
						</b>
						<b class="text-center text-white" style="padding-top:3px;padding-right:50px"><?php echo $sitename; ?></b>
					</div>
					<a class="topbartoggler d-block d-md-none waves-effect waves-light" href="javascript:void(0)" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation"></i></a>
				</div>
				<div class="navbar-collapse collapse" id="navbarSupportedContent">
					<ul class="navbar-nav float-left mr-auto ml-3 pl-1"></ul>
					<ul class="navbar-nav float-right">
						<li class="nav-item dropdown">
							<a class="nav-link dropdown-toggle" href="javascript:void(0)" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
								<img src="../assets/images/logo-icon.png" alt="homepage" class="dark-logo" width="29">
								<span class="ml-2 d-none d-lg-inline-block"><span class="text-dark">Hello, <?php echo $_SESSION['username']; ?>!</span>
									<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-chevron-down svg-icon"><polyline points="6 9 12 15 18 9"></polyline></svg>
								</span>
							</a>
							<div class="dropdown-menu dropdown-menu-right user-dd animated flipInY">
								<a class="dropdown-item" href="tickets.php"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-mail svg-icon mr-2 ml-1"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path><polyline points="22,6 12,13 2,6"></polyline></svg>Inbox</a>
								<a class="dropdown-item" href="profile.php"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-user svg-icon mr-2 ml-1"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>Profile</a>
								<div class="dropdown-divider"></div>
								<a class="dropdown-item" href="logout.php"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-power svg-icon mr-2 ml-1"><path d="M18.36 6.64a9 9 0 1 1-12.73 0"></path><line x1="12" y1="2" x2="12" y2="12"></line></svg>Logout</a>
							</div>
						</li>
					</ul>
				</div>
			</nav>
		</header>
		<aside class="left-sidebar" data-sidebarbg="skin6">
			<div class="scroll-sidebar ps-container ps-theme-default" data-sidebarbg="skin6" data-ps-id="3a29f672-0086-86d3-9391-5e6ad0e81820">
				<nav class="sidebar-nav">
					<ul id="sidebarnav" class="in">
						<li class="nav-small-cap"><span class="hide-menu">Admin Panel</span></li>
						<li class="sidebar-item">
							<a class="sidebar-link sidebar-link" href="dashboard.php" aria-expanded="false">
								<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-home feather-icon"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path><polyline points="9 22 9 12 15 12 15 22"></polyline></svg>
								<span class="hide-menu">Dashboard</span>
							</a>
						</li>
						<li class="sidebar-item">
							<a class="sidebar-link sidebar-link" href="settingsapi.php" aria-expanded="false">
								<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-shield feather-icon"><path d="M12 22s8-4 8-10V4l-8-2-8 2v8c0 6 8 10 8 10z"></path></svg>
								<span class="hide-menu">Api Settings</span>
							</a>
						</li>
							<li class="sidebar-item">
							<a class="sidebar-link sidebar-link" href="settings.php" aria-expanded="false">
								<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-arrow-up-circle feather-icon"><path d="M22 12 A10 10 0 0 1 12 22 A10 10 0 0 1 2 12 A10 10 0 0 1 22 12 z"/><path d="M16 12 L12 8 L8 12"/><path d="M12 16 L12 8"/></svg>
								<span class="hide-menu">Settings</span>
							</a>
						</li>
						<li class="sidebar-item">
							<a class="sidebar-link sidebar-link" href="giftcards.php" aria-expanded="false">
								<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-plus feather-icon"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
								<span class="hide-menu">Giftcards</span>
							</a>
						</li>
							<li class="sidebar-item">
							<a class="sidebar-link sidebar-link" href="users.php" aria-expanded="false">
								<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-users feather-icon"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>
								<span class="hide-menu">Users</span>
							</a>
						</li>
							<li class="sidebar-item">
							<a class="sidebar-link sidebar-link" href="plans.php" aria-expanded="false">
								<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-lock feather-icon"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect><path d="M7 11V7a5 5 0 0 1 10 0v4"></path></svg>
								<span class="hide-menu">Plans</span>
							</a>
						</li>
							<li class="sidebar-item">
							<a class="sidebar-link sidebar-link" href="liveattacks.php" aria-expanded="false">
								<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-alert-circle feather-icon"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12" y2="16"></line></svg>
								<span class="hide-menu">Live Attacks</span>
							</a>
						</li>
							<li class="sidebar-item">
							<a class="sidebar-link sidebar-link" href="attacklogs.php" aria-expanded="false">
								<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-wifi-off feather-icon"><line x1="1" y1="1" x2="23" y2="23"></line><path d="M16.72 11.06A10.94 10.94 0 0 1 19 12.55"></path><path d="M5 12.55a10.94 10.94 0 0 1 5.17-2.39"></path><path d="M10.71 5.05A16 16 0 0 1 22.58 9"></path><path d="M1.42 9a15.91 15.91 0 0 1 4.7-2.88"></path><path d="M8.53 16.11a6 6 0 0 1 6.95 0"></path><line x1="12" y1="20" x2="12" y2="20"></line></svg>
								<span class="hide-menu">Attack Logs</span>
							</a>
						</li>
						<li class="sidebar-item">
							<a class="sidebar-link sidebar-link" href="loginlogs.php" aria-expanded="false">
								<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-user feather-icon"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
								<span class="hide-menu">Login Logs</span>
							</a>
						</li>
						<li class="sidebar-item">
							<a class="sidebar-link sidebar-link" href="tickets.php" aria-expanded="false">
								<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-mail feather-icon"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path><polyline points="22,6 12,13 2,6"></polyline></svg>
								<span class="hide-menu">Tickets</span>
								<!--<span class="badge bg-primary font-12 text-white font-weight-medium badge-pill ml-2 d-lg-block d-md-none">+1</span>-->
							</a>
						</li>
						<li class="sidebar-item">
							<a class="sidebar-link sidebar-link" href="../dashboard.php" aria-expanded="false">
								<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-power feather-icon"><path d="M18.36 6.64a9 9 0 1 1-12.73 0"></path><line x1="12" y1="2" x2="12" y2="12"></line></svg>
								<span class="hide-menu">Back</span>
							</a>
						</li>
					</ul>
				</nav>
			<div class="ps-scrollbar-x-rail" style="left: 0px; bottom: 0px;"><div class="ps-scrollbar-x" tabindex="0" style="left: 0px; width: 0px;"></div></div><div class="ps-scrollbar-y-rail" style="top: 0px; right: 3px;"><div class="ps-scrollbar-y" tabindex="0" style="top: 0px; height: 0px;"></div></div></div>
		</aside>
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
						<p>By using the Server Stress Testing services provided by Scapy.club, you agree to be responsible for all actions and consequences..</p>
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
						<p>In conjunction with the Limitation of Warranties as explained above, you expressly understand and agree that any claim against us will be limited to the amount you paid, if any, for use of products and/or services. Scapy.club will not be liable for any direct, indirect, incidental, consequential or exemplary loss or damages which may be incurred by you as a result of using our Resources, or as a result of any changes, data loss or corruption, cancellation, loss of access, or downtime to the full extent that applicable limitation of liability laws apply.</p>
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
    <script src="assets/js/jquery.min.js"></script>
    <script src="assets/js/popper.min.js"></script>
    <script src="assets/js/bootstrap.min.js"></script>
    <script src="assets/js/app-style-switcher.js"></script>
    <script src="assets/js/feather.min.js"></script>
    <script src="assets/js/perfect-scrollbar.jquery.min.js"></script>
    <script src="assets/js/sidebarmenu.js"></script>
    <script src="assets/js/custom.min.js"></script>
    <script src="assets/js/d3.min.js"></script>
    <script src="assets/js/c3.min.js"></script>
    <script src="assets/js/chartist.min.js"></script>
    <script src="assets/js/chartist-plugin-tooltip.min.js"></script>
    <script src="assets/js/jquery-jvectormap-2.0.2.min.js"></script>
    <script src="assets/js/jquery-jvectormap-world-mill-en.js"></script>
    <script src="assets/js/dashboard1.min.js"></script>