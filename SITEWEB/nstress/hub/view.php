<?php

$paginaname = 'View Ticket';

?>
<!DOCTYPE html>
<!--[if IE 9]>         <html class="no-js lt-ie10"> <![endif]-->
<!--[if gt IE 9]><!--> <html class="no-js"> <!--<![endif]-->
			<?php 
			
			include("@/header.php");
			include("AntiXSS.php");
			
			
			if(is_numeric($_GET['id']) == false) {
			echo "Nice try.";
			exit;
			}
			$id = intval($_GET['id']);
			$x1 = rand(2,10);
			$x2 = rand(1,10);
			$x = SHA1($x1 + $x2);
			$SQLGetTickets = $odb -> query("SELECT * FROM `tickets` WHERE `id` = '$id'");
			while ($getInfo = $SQLGetTickets -> fetch(PDO::FETCH_ASSOC))
			{
			$username = $getInfo['username'];
			$subject = $getInfo['subject'];
			$status = $getInfo['status'];
			$original = $getInfo['content'];
			$date = date("m-d-Y, h:i:s a" ,$getInfo['date']);
			}
			if ($username != $_SESSION['username'])
			{
			die;
			}
			if ($user -> safeString($original))
			{
			die('Ticket contains unsafe data, you can not view the content for security reasons.');
			}

			?>
				
					<div id="page-content" class="inner-sidebar-left">
 
<div id="page-content-sidebar">
 
<div class="block-section">
<a href="#modal-compose" class="btn btn-effect-ripple btn-block btn-success" data-toggle="modal"><i class="fa fa-pencil"></i> New Ticket</a>
</div>
<div id="modal-compose" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
<div class="modal-dialog">
<div class="modal-content">
<div class="modal-header">
<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
<h3 class="modal-title"><strong>New Ticket <img src="img/jquery.easytree/loading.gif" id="ticketimage" style="display:none"/></strong></h3>
</div>
<div class="modal-body">
<?php 
if (isset($_POST['updateBtn']))
{
	$subject = $_POST['subject'];
	$content = $_POST['content'];
	if (empty($subject) || empty($content))
	{
		$error = 'Fill in all fields';
	}
	if ($user -> safeString($content) || $user -> safeString($subject))
	{
		$error = 'Unsafe characters were set.';
	}
	$SQLCount = $odb -> query("SELECT COUNT(*) FROM `tickets` WHERE `username` = '{$_SESSION['username']}' AND `status` = 'Waiting for admin response'")->fetchColumn(0);
	if ($SQLCount > 2)
	{
		$error = 'You have too many opened tickets. Please wait them to be responded before you open a new one';
	}
	if (empty($error))
	{
		$SQLinsert = $odb -> prepare("INSERT INTO `tickets` VALUES(NULL, :subject, :content, :status, :username, UNIX_TIMESTAMP())");
		$SQLinsert -> execute(array(':subject' => $subject, ':content' => $content, ':status' => 'Waiting for admin response', ':username' => $_SESSION['username']));
		echo success('Ticket has been created. Redirecting to inbox..');
	}
	else
	{
		echo error($error);
	}
}
?>
<form class="form-horizontal form-bordered" method="post">
<div class="form-group">
<div class="col-xs-12">
<input type="text" name="subject" id="subject" value="" class="form-control" placeholder="Subject">
</div>
</div>
<div class="form-group">
<div class="col-xs-12">
<textarea name="content" id="content" rows="7" class="form-control" placeholder="Write your message.."></textarea>
</div>
</div>
<input type="hidden" id="username" name="username" value="<?php echo $_SESSION['username']; ?>"  />
<div class="form-group form-actions">
<div class="col-xs-12 text-left">
By submitting this ticket I agree that I already read the <a href="faq.php">FAQ</a>.
</div>
<div class="col-xs-12 text-right">
<button name="updateBtn" class="btn btn-effect-ripple btn-primary">Send</button>
</div>
</div>
</form>
</div>
</div>
</div>
</div>
 
 
<a href="javascript:void(0)" class="btn btn-block btn-effect-ripple btn-default visible-xs" data-toggle="collapse" data-target="#email-nav">Navigation</a>
<div id="email-nav" class="collapse navbar-collapse remove-padding">
 
<div class="block-section">
<h4 class="inner-sidebar-header">
Labels
</h4>
<ul class="nav nav-pills nav-stacked nav-icons">
<li>
<a href="javascript:void(0)">
<i class="fa fa-fw fa-circle icon-push text-info"></i> <strong>Read ticket</strong>
</a>
</li>
<li>
<a href="javascript:void(0)">
<i class="fa fa-fw fa-circle icon-push text-success"></i> <strong>Unread ticket</strong>
</a>
</li>
<li>
<a href="javascript:void(0)">
<i class="fa fa-fw fa-circle icon-push text-danger"></i> <strong>Closed ticket</strong>
</a>
</li>
</ul>
</div>
 
</div>
 
</div>
 
 
<div class="block overflow-hidden">
 
<div id="message-list">
 
<div id="message-view" class="block-section animation-fadeInQuick2">
<div class="block-title clearfix">
<div class="block-options pull-right">
<form method="post">
<button name="closeBtn" class="btn btn-effect-ripple btn-danger" data-toggle="tooltip" title="Close ticket"><i class="gi gi-lock"></i></button>
</form>
</div>
<div class="block-options pull-left"><a class="btn btn-effect-ripple btn-default" href="tickets.php"><i class="fa fa-chevron-left"></i> Back to Inbox</a> <small><i class="fa fa-spinner fa-2x fa-spin text-success" id="ticketloader" style="display:none"></i></small>
</div>
</div>
<h3><strong><?php echo htmlspecialchars($subject); ?> </strong> <small><em class="pull-right"><?php echo $date; ?></em></small></h3>
<p><a><strong>Client</strong></a> <strong>&lt;<?php echo htmlspecialchars($username); ?> &gt;</strong></p>
<p> <?php echo $original; ?></p>
<hr>

<?php
$SQLGetMessages = $odb -> prepare("SELECT * FROM `messages` WHERE `ticketid` = :ticketid ORDER BY `messageid` ASC");
$SQLGetMessages -> execute(array(':ticketid' => $id));
while ($getInfo = $SQLGetMessages -> fetch(PDO::FETCH_ASSOC))
{
 $sender = $getInfo['sender'];
 $content = $getInfo['content'];
 $date = date("m-d-Y, h:i:s a" ,$getInfo['date']);
 if ($sender == 'Admin')
 if ($user -> safeString($content))
 {
 die('Ticket contains unsafe data, you can not view the content for security reasons.');
 }
 echo '
 <p><a><strong>'.$sender.'</strong></a> <small><em class="pull-right">'.$date.'</em></small></p>
<p>'.$content.'</p>
<hr>';
}
?>

<?php
	   if (isset($_POST['closeBtn']))
	   {
$SQLupdate = $odb -> prepare("UPDATE `tickets` SET `status` = :status WHERE `id` = :id");
$SQLupdate -> execute(array(':status' => 'Closed', ':id' => $id));
echo success('Ticket has been closed successfuly, redirecting to inbox <meta http-equiv="refresh" content="3;url=tickets.php">');

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
			$i = 0;
			$SQLGetMessages = $odb -> query("SELECT * FROM `messages` WHERE `ticketid` = '$id' ORDER BY `messageid` DESC");
			while ($getInfo = $SQLGetMessages -> fetch(PDO::FETCH_ASSOC))
			{
			if ($getInfo['sender'] == 'Client') { $i++; }
			}
			if ($i >= 999)
			{
			$error = 'Please wait for an admin to respond until you send a new message';
			}
			if (empty($error))
			{
				$SQLinsert = $odb -> prepare("INSERT INTO `messages` VALUES(NULL, :ticketid, :content, :sender, UNIX_TIMESTAMP())");
				$SQLinsert -> execute(array(':sender' => 'Client', ':content' => $updatecontent, ':ticketid' => $id));
			{
				$SQLUpdate = $odb -> prepare("UPDATE `tickets` SET `status` = :status WHERE `id` = :id");
				$SQLUpdate -> execute(array(':status' => 'Waiting for admin response', ':id' => $id));
				echo success('Message has been sent successfully. <meta http-equiv="refresh" content="3;url=view.php?id='.$id.'">');

			}
			}
			else
			{
				echo error($error);
			}
		}
?>			

<form method="post"><textarea name="message" rows="5" class="form-control push-bit" placeholder="Your message.."></textarea>
<button name="newmessage" class="btn btn-effect-ripple btn-primary"><i class="fa fa-share"></i> Reply</button>
</form>
</div>

 
</div>
 
 
<div id="message-view" class="block-section display-none">
</div>
 
</div>
 
</div>
                    <? // NO BORRAR LOS TRES DIVS! ?>
               </div>         
          </div>
	</div>

		<?php include("@/script.php"); ?>
    </body>
</html>