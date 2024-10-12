<?php
include("header.php");
if (!($user -> isAdmin($odb)))
{
	header('location: ../index.php');
	die();
}

if (isset($_POST['users']))
{
$SQL = $odb -> query("DELETE FROM `users` WHERE `membership` = 0");
}
if (isset($_POST['payments']))
{
$SQL = $odb -> query("TRUNCATE `payments`");
}
if (isset($_POST['attacks']))
{
$SQL = $odb -> query("TRUNCATE `logs`");
}
if (isset($_POST['logins']))
{
$SQL = $odb -> query("TRUNCATE `loginlogs`");
}

?>
<form method="post">
            <div class="page-content">

                <div class="container">
                    <div class="page-toolbar">
                        
                        <div class="page-toolbar-block">
                            <div class="page-toolbar-title">Logs & Users</div>
                            <div class="page-toolbar-subtitle">Users, Payments, Attacks and Logins</div>
                        </div>        
                       <ul class="page-toolbar-tabs">
                            <li class="active"><a href="#page-tab-1">Users</a></li>
                            <li><a href="#page-tab-2">Payments</a></li>
                            <li><a href="#page-tab-3">Attacks</a></li>
                            <li><a href="#page-tab-4">Logins</a></li>
                        </ul>
                    </div>                    
<div class="row page-toolbar-tab active" id="page-tab-1">
                            <div class="block">
                                <div class="block-head">
                                    <h2>Users <button type="submit" class="btn btn-link btn-xs" name="users">Delete users with no membership</button></h2>
                                </div>
                                <div class="block-content np">
                                    <table class="table table-bordered table-striped sortable">
                                        <thead>
                                            <tr>
                                                <th></th>
                                                <th>Username</th>
                                                <th>Email</th>
                                                <th>Rank</th>
                                                <th>Membership</th>                                    
                                            </tr>
                                        </thead>
                                        <tbody>
<?php
$SQLGetUsers = $odb -> query("SELECT * FROM `users` ORDER BY `ID` DESC");
while ($getInfo = $SQLGetUsers -> fetch(PDO::FETCH_ASSOC))
{
	$id = $getInfo['ID'];
	$user = $getInfo['username'];
	$email = $getInfo['email'];
	if ($getInfo['expire']>time()) {$plan = $odb -> query("SELECT `plans`.`name` FROM `users`, `plans` WHERE `plans`.`ID` = `users`.`membership` AND `users`.`ID` = '$id'") -> fetchColumn(0);} else {$plan='No membership';}
	$rank = $getInfo['rank'];
		if ($rank == 1)
		{
			$rank = 'Admin';
		}
		elseif ($rank == 2)
		{
			$rank = 'Supporter';
		}
		else
		{
			$rank = 'Member';
		}
	echo '<tr><td></td><td><a href="user.php?id='.$id.'">'.htmlspecialchars($user).'</a></td><td>'.htmlspecialchars($email).'</td><td>'.$rank.'</td><td>'.htmlspecialchars($plan).'</td></tr>';
}
?>											
                                        </tbody>
                                    </table>                                                                        
                                </div>
                            </div>

</div>
<div class="row page-toolbar-tab" id="page-tab-2">
                            <div class="block">
                                <div class="block-head">
                                    <h2>Payments <button type="submit" class="btn btn-link btn-xs" name="payments">Clear payments</button></h2>
                                </div>
                                <div class="block-content np">
                                    <table class="table table-bordered table-striped sortable">
                                        <thead>
                                            <tr>
                    <th>User</th>
                    <th>Plan</th>
                    <th>Email</th>
                    <th>Transaction ID</th>
                    <th>Date</th>
                                            </tr>
                                        </thead>
                                        <tbody>
<?php
$SQLGetLogs = $odb -> query("SELECT `payments`.* , `plans`.`name` AS `planname`, `users`.`username` FROM `payments` LEFT JOIN `plans` ON `payments`.`plan` = `plans`.`ID` LEFT JOIN `users` ON `payments`.`user` = `users`.`ID` ORDER BY `ID` DESC");
while($getInfo = $SQLGetLogs -> fetch(PDO::FETCH_ASSOC))
{
	$user = $getInfo['username'];
	$plan = $getInfo['planname'];
	$email = $getInfo['email'];
	$tid = $getInfo['tid'];
	$amount = $getInfo['paid'];
	$date = date("m-d-Y, h:i:s a" ,$getInfo['date']);
	echo '<tr"><td>'.htmlspecialchars($user).'</td><td>'.htmlspecialchars($plan).' ($'.$amount.')</td><td>'.htmlspecialchars($email).'</td><td>'.htmlspecialchars($tid).'</td><td>'.$date.'</td></tr>';
}
?>						
                                        </tbody>
                                    </table>                                                                        
                                </div>
                            </div>
</div>
<div class="row page-toolbar-tab" id="page-tab-3">
                            <div class="block">
                                <div class="block-head">
                                    <h2>Attacks <button type="submit" class="btn btn-link btn-xs" name="attacks">Clear attacks</button></h2>
                                </div>
                                <div class="block-content np">
                                    <table class="table table-bordered table-striped sortable">
                                        <thead>
                                            <tr>
                    <th>User</th>
                    <th>Host</th>
                    <th>Time</th>
                    <th>Handler</th>
                    <th>Date</th>                               
                                            </tr>
                                        </thead>
                                        <tbody>
<?php
$SQLGetLogs = $odb -> query("SELECT * FROM `logs` ORDER BY `date` DESC");
while($getInfo = $SQLGetLogs -> fetch(PDO::FETCH_ASSOC))
{
	$user = $getInfo['user'];
	$host = $getInfo['ip'];
	if (filter_var($host, FILTER_VALIDATE_URL)) {$port='';} else {$port=':'.$getInfo['port'];}
	$time = $getInfo['time'];
	$method = $getInfo['method'];
	$handler = $getInfo['handler'];
	$date = date("m-d-Y, h:i:s a" ,$getInfo['date']);
	echo '<tr><td>'.htmlspecialchars($user).'</td><td>'.htmlspecialchars($host).$port.' ('.htmlspecialchars($method).')<br></td><td>'.$time.'</td><td>'.htmlspecialchars($handler).'</td><td>'.$date.'</td></tr>';
}
?>
                                        </tbody>
                                    </table>                                                                        
                                </div>
                            </div>
</div>
<div class="row page-toolbar-tab" id="page-tab-4">
                            <div class="block">
                                <div class="block-head">
                                    <h2>Login logs <button type="submit" class="btn btn-link btn-xs" name="logins">Clear login logs</button></h2>
                                </div>
                                <div class="block-content np">
                                    <table class="table table-bordered table-striped sortable">
                                        <thead>
                                            <tr>
                                                <th></th>
                    <th>User</th>
                    <th>IP</th>
                    <th>Date</th>
                    <th>Country</th>                                  
                                            </tr>
                                        </thead>
                                        <tbody>
<?php 
$SQLGetUsers = $odb -> query("SELECT * FROM `loginlogs` ORDER BY `date` DESC");
while ($getInfo = $SQLGetUsers -> fetch(PDO::FETCH_ASSOC))
{
	$username = $getInfo['username'];
	$ip = $getInfo['ip'];
	$date = date("m-d-Y, h:i:s a" ,$getInfo['date']);
	$country = $getInfo['country'];
	echo '<tr><td></td><td>'.htmlspecialchars($username).'</td><td>'.htmlspecialchars($ip).'</td><td>'.$date.'</td><td>'.htmlspecialchars($country).'</td></tr>';
}
?>
                                        </tbody>
                                    </table>                                                                        
                                </div>
                            </div>
</div>
                </div>
                
            </div>
			</form>
            <div class="page-sidebar"></div>
        </div>
    </body>
</html>
