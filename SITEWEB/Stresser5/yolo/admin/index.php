<?php
include("header.php");
?>
            <div class="page-content">

                <div class="container">
                    <div class="page-toolbar">
                        
                        <div class="page-toolbar-block">
                            <div class="page-toolbar-title">Dashboard</div>
                            <div class="page-toolbar-subtitle">Admin dashboard</div>
                        </div>
                        
                        <div class="page-toolbar-block pull-right">
                            <div class="widget-info">
                                <div class="widget-info-title">Registered users</div>
                                <div class="widget-info-value"><?php echo $stats -> totalUsers($odb); ?></div>
                            </div>
                            <div class="widget-info">
                                <div class="widget-info-title">Users with memberships</div>
                                <div class="widget-info-value"><?php echo $stats -> activeUsers($odb); ?></div>
                            </div>                                                       
                        </div>         
                        
                    </div>                    

                    <div class="row">                        
                        <div class="col-md-4">
                            <div class="widget-window">
                                <div class="window window-success window-npb">
                                    <div class="window-title">Daily Profits</div> 
                                </div>
                                <div class="window window-success tac">
                                    <span class="sparkline" sparkType="line" sparkHighlightSpotColor="#FFF" sparkSpotRadius="5" sparkMaxSpotColor="#FFFFFF" sparkMinSpotColor="#FFFFFF" sparkSpotColor="#FFFFFF" sparkLineColor="#FFFFFF" sparkHeight="100" sparkWidth="300" sparkLineWidth="3" sparkFillColor="false">
<?php
$i = 9;
while ($i>=0)
{
$paid = 0;
$start = strtotime('-'.$i.' day', (time()));
$end = strtotime('-1 day', $start);
$paid = $odb->query("SELECT `paid` FROM `payments` WHERE `date` BETWEEN '$end' AND '$start'")->fetchColumn(0) + $paid;
$i--;
if ($i==0) {
echo $paid;
}
else
{
echo $paid.',';
}
}

$overall = 0;
$today = 0;
$month = 0;
$SQLGetMoney = $odb -> query("SELECT * FROM `payments`");
while($getInfo = $SQLGetMoney -> fetch(PDO::FETCH_ASSOC))
{
if (date('m-d') == date('m-d', $getInfo['date'])) {
$today = $getInfo['paid'] + $today;
}
if (date('m') == date('m', $getInfo['date'])) {
$month = $getInfo['paid'] + $month;
}
$overall = $getInfo['paid'] + $overall;
}
?>
</span>
                                </div>
                                <div class="window window-dark">
                                    <div class="window-block">
                                        <h4>Today's earnings</h4>
                                        <p>$<?php echo $today; ?></p>                                    
                                        <h4>This month's earnings</h4>
                                        <p>$<?php echo $month; ?></p>
                                        <h4>Overall earnings</h4>
                                        <p>$<?php echo $overall; ?></p>
                                    </div>
                                </div>
                            </div>
                        </div>
<div class="col-md-6">
                            <div class="block">
                                <div class="block-content">
                                    <h2>Tickets awaiting reply</h2>
                                </div>                                
                                <div class="block-content np">
                                    <table class="table table-striped">
                                        <tr>
                                            <th>Subject</th><th>Sender</th><th>Date</th>
                                        </tr>
			<?php 
			$SQLGetTickets = $odb -> query("SELECT * FROM `tickets` WHERE `status` = 'Waiting for admin response' ORDER BY `date` DESC");
			while ($getInfo = $SQLGetTickets -> fetch(PDO::FETCH_ASSOC))
			{
				$username = $getInfo['username'];
				$id = $getInfo['id'];
				$subject = $getInfo['subject'];
				$date = date("m-d-Y, h:i:s a" ,$getInfo['date']);
				$userid = $odb->query("SELECT `ID` FROM `users` WHERE `username` = '$username'")->fetchColumn(0);
				echo '<tr><td><a href="ticket.php?id='.$id.'">'.htmlspecialchars($subject).'</a></td><td><a href="user.php?id='.$userid.'">'.htmlspecialchars($username).'</a></td><td>'.$date.'</td></tr>';
			}
			?>                                       
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                </div>
                
            </div>
            <div class="page-sidebar"></div>
        </div>
    </body>
</html>
