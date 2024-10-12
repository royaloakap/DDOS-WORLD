<?php 
if (basename($_SERVER['SCRIPT_FILENAME']) == basename(__FILE__)) {exit("NOT ALLOWED");}
ob_start();
require_once '../../@/config.php';
require_once '../../@/init.php';
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
<html lang="en">
    <head>        
        <title><?php echo htmlspecialchars($sitename); ?></title>    

        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />        
        <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0" />
        
        <link href="../css/styles.css" rel="stylesheet" type="text/css" />
        <!--[if lt IE 10]><link rel="stylesheet" type="text/css" href="../css/ie.css"/><![endif]-->
        
        <script type="text/javascript" src="../js/plugins/jquery/jquery.min.js"></script>
        <script type="text/javascript" src="../js/plugins/jquery/jquery-ui.min.js"></script>
        <script type="text/javascript" src="../js/plugins/bootstrap/bootstrap.min.js"></script>
        <script type="text/javascript" src="../js/plugins/mcustomscrollbar/jquery.mCustomScrollbar.min.js"></script> <!-- Scroller -->     
        
        <script type="text/javascript" src="../js/plugins/sparkline/jquery.sparkline.min.js"></script> <!-- Spark Line -->       
        <script type="text/javascript" src="../js/plugins/fancybox/jquery.fancybox.pack.js"></script> <!-- Fancy Box -->             

        <script type="text/javascript" src="../js/plugins/rickshaw/d3.v3.js"></script> <!-- Graph -->
        <script type="text/javascript" src="../js/plugins/rickshaw/rickshaw.min.js"></script> <!-- Graph -->
        
        <script type='text/javascript' src='js/plugins/knob/jquery.knob.js'></script> <!-- Pie -->

        <script type="text/javascript" src="../js/plugins/highlight/jquery.highlight-4.js"></script>
        <script type="text/javascript" src="../js/plugins/other/faq.js"></script> <!-- FAQ -->
		
        <script type="text/javascript" src="../js/plugins.js"></script>        
        <script type="text/javascript" src="../js/actions.js"></script>        
        
		<script type='text/javascript' src="../js/plugins/noty/jquery.noty.js"></script> <!-- Alert -->
        <script type='text/javascript' src="../js/plugins/noty/layouts/topCenter.js"></script> <!-- Alert -->
        <script type='text/javascript' src="../js/plugins/noty/themes/default.js"></script> <!-- Alert -->
		
		<script type="text/javascript" src="../js/plugins/summernote/summernote.min.js"></script> <!-- Editor -->
		
		<script type="text/javascript" src="../js/plugins/datatables/jquery.dataTables.min.js"></script> <!-- Data Table --> 
    </head>
        <div class="page-container">
<?php
$tickets = $odb->query("SELECT COUNT(*) FROM `tickets` WHERE `username` = '{$_SESSION['username']}' AND `status` = 'Waiting for user response' ORDER BY `id` DESC")->fetchColumn(0);
?>
            <div class="page-head">
                <ul class="page-head-elements pull-right">
                    <li>
                        <div class="informer informer-pulsate"><?php echo $tickets; ?></div>
                        <a href="#" class="dropdown"><span class="fa fa-comments"></span></a>                        
                        <div class="popup">
                            <div class="list no-controls scroll" style="height: 300px;">
<?php 
$SQLGetTickets = $odb -> prepare("SELECT * FROM `tickets` WHERE `username` = :username AND `status` = 'Waiting for user response' ORDER BY `id` DESC");
$SQLGetTickets -> execute(array(':username' => $_SESSION['username']));
while ($getInfo = $SQLGetTickets -> fetch(PDO::FETCH_ASSOC))
{
$id = $getInfo['id'];
$subject = $getInfo['subject'];
$status = $getInfo['status'];
$content = $getInfo['content'];
$date = date("m-d-Y" ,$getInfo['date']);
echo '<a href="../ticket.php?id='.$id.'" class="list-item"><div class="list-item-content"><br><p>'.htmlspecialchars(substr($subject, 0, 30)).'...</p><span class="date">'.$date.'</span></div></a>';
}
?>                              
                            </div>
                            <div class="popup-block tac"><a href="../support.php">Show all messages</a></div>
                        </div>
                        <div class="informer informer-pulsate"><?php echo $tickets; ?></div>
                    </li>
                </ul>
            </div>
            <div class="page-navigation">
                
                <div class="page-navigation-info">
                    <a href="../index.php" class="logo"><?php echo htmlspecialchars($sitename); ?></a>
                </div>
                
                <div class="profile">                    
                    <img src="../img/user.png"/>
                    <div class="profile-info">
                        <a href="#" class="profile-title"><?php echo $_SESSION['username']; ?></a>
                        <span class="profile-subtitle"><?php if ($user->hasMembership($odb)) {echo 'Active Membership';} else {echo 'No Membership';} ?></span>
                        <div class="profile-buttons">
                            <div class="btn-group">                                
                                <a class="but dropdown-toggle" data-toggle="dropdown"><i class="fa fa-cog"></i></a>
                                <ul class="dropdown-menu" role="menu">
                                    <li role="presentation" class="dropdown-header">Profile Menu</li>
                                    <li><a href="../profile.php">Profile</a></li>                                    
                                    <li><a href="../support.php">My Tickets</a></li>
                                    <li class="divider"></li>
                                    <li><a href="../logout.php">Logout</a></li>
                                </ul>
                            </div>
                        </div>                        
                    </div>
                </div>

                <ul class="navigation">
                    <li class="active"><a href="index.php"><i class="fa fa-dashboard"></i> Dashboard</a></li>
                    <li><a href="settings.php"><i class="fa fa-gears"></i> Settings</a>
                    <li><a href="logs.php"><i class="fa fa-bars"></i> Logs</a>
                    </li>                    
                </ul>
                
            </div>