<?php
include("header.php");
if(is_numeric($_GET['id']) == false) {
echo "lol";
exit;
}
$id = intval($_GET['id']);
$x1 = rand(2,10);
$x2 = rand(1,10);
$x = SHA1($x1 + $x2);
$SQLGetTickets = $odb -> query("SELECT * FROM `tickets` WHERE `id` = $id");
while ($getInfo = $SQLGetTickets -> fetch(PDO::FETCH_ASSOC))
{
$username = $getInfo['username'];
$subject = $getInfo['subject'];
$status = $getInfo['status'];
$original = $getInfo['content'];
$date = date("m-d-Y, h:i:s a" ,$getInfo['date']);
}
if ($user -> safeString($original))
{
die('Ticket contains unsafe data, you can not view the content for security reasons.');
}
?>
            <div class="page-content">

                <div class="container">
                    
                    <div class="page-toolbar">
                        
                        <div class="page-toolbar-block">
                            <div class="page-toolbar-title"><?php echo htmlspecialchars($subject); ?></div>
                            <div class="page-toolbar-subtitle"><?php echo htmlspecialchars($status); ?></div>
                        </div>
                        
                        <ul class="breadcrumb">
                            <li><a href="index.php">Dashboard</a></li>
                            <li class="active">View Message</li>
                        </ul>                        
                        
                    </div>                    
<?php
	   if (isset($_POST['closeBtn']))
	   {
$SQLupdate = $odb -> prepare("UPDATE `tickets` SET `status` = :status WHERE `id` = :id");
$SQLupdate -> execute(array(':status' => 'Closed', ':id' => $id));
echo success('Ticket has been closed successfuly, redirecting to index <meta http-equiv="refresh" content="3;url=index.php">');

 	   }
	   if (isset($_POST['newmessage']))
	   {
	   	$updatecontent = $_POST['message'];

			$errors = array();
			if (empty($updatecontent))
			{
				$error = 'Fill in all fields';
			}
			if ($user -> safeString($updatecontent))
			{
				$error = 'Unsafe characters were set.';
			}
			if (empty($error))
			{
				$SQLinsert = $odb -> prepare("INSERT INTO `messages` VALUES(NULL, :ticketid, :content, :sender, UNIX_TIMESTAMP())");
				$SQLinsert -> execute(array(':sender' => 'Admin', ':content' => $updatecontent, ':ticketid' => $id));
			{
				$SQLUpdate = $odb -> prepare("UPDATE `tickets` SET `status` = :status WHERE `id` = :id");
				$SQLUpdate -> execute(array(':status' => 'Waiting for user response', ':id' => $id));
				echo success('Message has been sent successfully, redirecting to index. <meta http-equiv="refresh" content="3;url=index.php">');

			}
			}
			else
			{
				echo error($error);
			}
		}
?>			
                    <div class="row">
                        <div class="col-md-9">
                            
                            <div class="block">
                                <div class="block-head">                                    
                                    <div class="block-title">
                                        <?php echo 'Re: '.htmlspecialchars($subject); ?>
                                    </div>                 
                                    <div class="block-title-date">
									<form method="post">
                                        <button name="closeBtn" class="btn btn-default"><i class="fa fa-trash-o"></i> Close Ticket</button>
									</form>
                                    </div>                                    
                                </div>
                                <div class="block-content">
                                    <div class="pull-left">
                                        <img src="../img/user.png" class="img-circle"/> <?php echo htmlspecialchars($username); ?> 
                                    </div>
                                    <div class="btn-group pull-right">
                                        <?php echo $date; ?>
                                    </div>
                                </div>
                                <div class="block-content">
                                    <?php echo $original; ?>
                                </div>
<?php
$SQLGetMessages = $odb -> prepare("SELECT * FROM `messages` WHERE `ticketid` = :ticketid ORDER BY `messageid` ASC");
$SQLGetMessages -> execute(array(':ticketid' => $id));
while ($getInfo = $SQLGetMessages -> fetch(PDO::FETCH_ASSOC))
{
 $sender = $getInfo['sender'];
 $content = $getInfo['content'];
 $date = date("m-d-Y, h:i:s a" ,$getInfo['date']);
 if ($sender == 'Admin') {$image = 'admin.png';} else {$image = 'user.png';}
 if ($user -> safeString($content))
 {
 die('Ticket contains unsafe data, you can not view the content for security reasons.');
 }
 echo '<div class="block-head"></div><div class="block-content"><div class="pull-left"><img src="../img/'.$image.'"" class="img-circle"/> '.$sender.'</div><div class="btn-group pull-right">'.$date.'</div></div><div class="block-content">'.$content.'</div>';
}
?>
								<form method="post">
                                <div class="block-content">
                                    <textarea class="form-control" name="message" placeholder="Post replay..." style="height: 100px" id="email_editor"></textarea>
                                </div>
                                <div class="block-content">
                                    <div class="pull-right">
                                        <button name="newmessage" class="btn btn-default">Send Message</button>
                                    </div>
                                </div>
								</form>
                            </div>
                            
                        </div>
                    </div>
                    
                </div>
                
            </div>
            <div class="page-sidebar"></div>
        </div>

    </body>
</html>