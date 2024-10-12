<?php 

header("X-XSS-Protection: 1; mode=block");

	if(basename($_SERVER['SCRIPT_FILENAME']) == basename(__FILE__)) die("Access denied");
	ob_start();
	
	require_once 'vvv/avg/mycon.php';
	require_once 'vvv/avg/usv.php';
	
	if (!(empty($maintaince))) {
		header('Location: maintenance.php');
		exit;
	}
	
	if (!($user -> LoggedIn()) || !($user -> notBanned($odb))){
		header('location: login.php');
		die();
	}
	
?>

<!DOCTYPE html>
<html lang="en"><head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta name="description" content="">
	<meta name="author" content="">
	<link rel="icon" type="image/png" sizes="16x16" href="assets/images/logo-icon.png">
	<title>Scapy.club | Dashboard</title>
	<link href="assets/css/c3.min.css" rel="stylesheet">
	<link href="assets/css/chartist.min.css" rel="stylesheet">
	<link href="assets/css/jquery-jvectormap-2.0.2.css" rel="stylesheet">
	<link href="assets/css/style.min.css" rel="stylesheet">
</head>
<body>
	<div class="preloader" style="display: none;">
		<div class="lds-ripple">
			<div class="lds-pos"></div>
			<div class="lds-pos"></div>
		</div>
	</div>
	<div id="main-wrapper" data-theme="light" data-layout="vertical" data-navbarbg="skin6" data-sidebartype="full" data-sidebar-position="fixed" data-header-position="fixed" data-boxed-layout="full">
		<header class="topbar" data-navbarbg="skin6">
			<nav class="navbar top-navbar navbar-expand-md navbar-light">
				<div class="navbar-header" data-logobg="skin6">
					<a class="nav-toggler waves-effect waves-light d-block d-md-none" href="javascript:void(0)"><i class="ti-menu ti-close"></i></a>
					<div class="navbar-brand">
						<b class="logo-icon text-center">
							<img src="assets/images/logo-icon.png" alt="homepage" class="dark-logo">
							<img src="assets/images/logo-icon.png" alt="homepage" class="light-logo">
						</b>
						<b class="text-center" style="padding-top: 5px;">SCAPY.CLUB</b>
					</div>
					<a class="topbartoggler d-block d-md-none waves-effect waves-light" href="javascript:void(0)" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation"><i class="ti-more"></i></a>
				</div>
				<div class="navbar-collapse collapse" id="navbarSupportedContent">
					<ul class="navbar-nav float-left mr-auto ml-3 pl-1"></ul>
					<ul class="navbar-nav float-right">
						<li class="nav-item dropdown">
							<a class="nav-link dropdown-toggle" href="javascript:void(0)" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
								<img src="assets/images/profile.jpg" alt="user" class="rounded-circle" width="40">
								<span class="ml-2 d-none d-lg-inline-block"><span class="text-dark">Hello, qweqwe!</span>
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
						<li class="nav-small-cap"><span class="hide-menu">Main</span></li>
						<li class="sidebar-item">
							<a class="sidebar-link sidebar-link active" href="dashboard.php" aria-expanded="false">
								<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-home feather-icon"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path><polyline points="9 22 9 12 15 12 15 22"></polyline></svg>
								<span class="hide-menu">Dashboard</span>
							</a>
						</li>
						<li class="sidebar-item">
							<a class="sidebar-link sidebar-link" href="purchase.php" aria-expanded="false">
								<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-shopping-cart feather-icon"><circle cx="9" cy="21" r="1"></circle><circle cx="20" cy="21" r="1"></circle><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path></svg>
								<span class="hide-menu">Purchase</span>
							</a>
						</li>
						<li class="sidebar-item">
							<a class="sidebar-link sidebar-link" href="profile.php" aria-expanded="false">
								<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-user feather-icon"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
								<span class="hide-menu">Profile</span>
							</a>
						</li>
						<li class="list-divider"></li>
						<li class="nav-small-cap"><span class="hide-menu">Hub</span></li>
						<li class="sidebar-item">
							<a class="sidebar-link sidebar-link" href="hub.php" aria-expanded="false">
								<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-activity feather-icon"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"></polyline></svg>
								<span class="hide-menu">Attack Hub</span>
							</a>
						</li>
						<li class="list-divider"></li>
						<li class="nav-small-cap"><span class="hide-menu">Help</span></li>
						<li class="sidebar-item">
							<a class="sidebar-link sidebar-link" href="tickets.php" aria-expanded="false">
								<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-mail feather-icon"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path><polyline points="22,6 12,13 2,6"></polyline></svg>
								<span class="hide-menu">Tickets</span>
								<!--<span class="badge bg-primary font-12 text-white font-weight-medium badge-pill ml-2 d-lg-block d-md-none">+1</span>-->
							</a>
						</li>
						<li class="sidebar-item">
							<a class="sidebar-link sidebar-link" href="javascript:void(0)" data-toggle="modal" data-target="#tos-modal" aria-expanded="false">
								<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-file-text feather-icon"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline></svg>
								<span class="hide-menu">TOS</span>
							</a>
						</li>
						<li class="sidebar-item">
							<a class="sidebar-link sidebar-link" href="https://t.me/shawtyclub" aria-expanded="false" target="_blank">
								<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-send feather-icon"><line x1="22" y1="2" x2="11" y2="13"></line><polygon points="22 2 15 22 11 13 2 9 22 2"></polygon></svg>
								<span class="hide-menu">Telegram</span>
							</a>
						</li>
												<li class="list-divider"></li>
						<li class="sidebar-item">
							<a class="sidebar-link sidebar-link" href="logout.php" aria-expanded="false">
								<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-power feather-icon"><path d="M18.36 6.64a9 9 0 1 1-12.73 0"></path><line x1="12" y1="2" x2="12" y2="12"></line></svg>
								<span class="hide-menu">Logout</span>
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
						<p>By using the Server Stress Testing services provided by Shawty.club, you agree to be responsible for all actions and consequences..</p>
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
						<p>In conjunction with the Limitation of Warranties as explained above, you expressly understand and agree that any claim against us will be limited to the amount you paid, if any, for use of products and/or services. Shawty.club will not be liable for any direct, indirect, incidental, consequential or exemplary loss or damages which may be incurred by you as a result of using our Resources, or as a result of any changes, data loss or corruption, cancellation, loss of access, or downtime to the full extent that applicable limitation of liability laws apply.</p>
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
		<div class="page-wrapper" style="display: block;">
			<div class="page-breadcrumb">
				<div class="d-flex align-items-center">
					<h4 class="page-title text-truncate text-dark font-weight-medium mb-0">Dashboard</h4>
					<div class="ml-auto">
						<nav aria-label="breadcrumb">
							<ol class="breadcrumb m-0 p-0">
								<li class="breadcrumb-item text-primary active" aria-current="page">Shawty.club</li>
								<li class="breadcrumb-item text-muted" aria-current="page">Dashboard</li>
							</ol>
						</nav>
					</div>
				</div>
			</div>
      </ul>
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


<div class="jvectormap-tip"></div></body></html>