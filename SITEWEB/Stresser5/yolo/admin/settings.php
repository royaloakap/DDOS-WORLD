<?php
include("header.php");
if (!($user -> isAdmin($odb)))
{
	header('location: ../index.php');
	die();
}
?>
            <div class="page-content">

                <div class="container">
<?php 
if (isset($_POST['update']))
{
if ($sitename != $_POST['sitename'])
{
$SQL = $odb -> prepare("UPDATE `settings` SET `sitename` = :sitename");
$SQL -> execute(array(':sitename' => $_POST['sitename']));
$sitename = $_POST['sitename'];
}

if ($description != $_POST['description'])
{
$SQL = $odb -> prepare("UPDATE `settings` SET `description` = :description");
$SQL -> execute(array(':description' => $_POST['description']));
$description = $_POST['description'];
}

if ($paypal != $_POST['paypal'])
{
$SQL = $odb -> prepare("UPDATE `settings` SET `paypal` = :paypal");
$SQL -> execute(array(':paypal' => $_POST['paypal']));
$paypal = $_POST['paypal'];
}

if ($bitcoin != $_POST['bitcoin'])
{
$SQL = $odb -> prepare("UPDATE `settings` SET `bitcoin` = :bitcoin");
$SQL -> execute(array(':bitcoin' => $_POST['bitcoin']));
$bitcoin = $_POST['bitcoin'];
}

if ($maintaince != $_POST['maintaince'])
{
$SQL = $odb -> prepare("UPDATE `settings` SET `maintaince` = :maintaince");
$SQL -> execute(array(':maintaince' => $_POST['maintaince']));
$maintaince = $_POST['maintaince'];
}

if ($tos != $_POST['tos'])
{
$SQL = $odb -> prepare("UPDATE `settings` SET `tos` = :tos");
$SQL -> execute(array(':tos' => $_POST['tos']));
$tos = $_POST['tos'];
}

if ($maxattacks != $_POST['maxattacks'])
{
$SQL = $odb -> prepare("UPDATE `settings` SET `maxattacks` = :maxattacks");
$SQL -> execute(array(':maxattacks' => $_POST['maxattacks']));
$maxattacks = $_POST['maxattacks'];
}

if ($siteurl != $_POST['url'])
{
$SQL = $odb -> prepare("UPDATE `settings` SET `url` = :url");
$SQL -> execute(array(':url' => $_POST['url']));
$siteurl = $_POST['url'];
}

if (isset($_POST['rotation']) AND $rotation == 0)
{
$SQL = $odb -> query("UPDATE `settings` SET `rotation` = 1");
$rotation == 1;
}

if (!(isset($_POST['rotation'])) AND $rotation == 1)
{
$SQL = $odb -> query("UPDATE `settings` SET `rotation` = 0");
$rotation == 0;
}

if (isset($_POST['testboots']) AND $testboots == 0)
{
$SQL = $odb -> query("UPDATE `settings` SET `testboots` = 1");
$testboots == 1;
}

if (!(isset($_POST['testboots'])) AND $testboots == 1)
{
$SQL = $odb -> query("UPDATE `settings` SET `testboots` = 0");
$testboots == 0;
}

if (isset($_POST['cbp']) AND $cbp == 0)
{
$SQL = $odb -> query("UPDATE `settings` SET `cbp` = 1");
$cbp == 1;
}

if (!(isset($_POST['cbp'])) AND $cbp == 1)
{
$SQL = $odb -> query("UPDATE `settings` SET `cbp` = 0");
$cbp == 0;
}

if (isset($_POST['cloudflare']) AND $cloudflare == 0)
{
$SQL = $odb -> query("UPDATE `settings` SET `cloudflare` = 1");
$cloudflare == 1;
}

if (!(isset($_POST['cloudflare'])) AND $cloudflare == 1)
{
$SQL = $odb -> query("UPDATE `settings` SET `cloudflare` = 0");
$cloudflare == 0;
}

if ($system != $_POST['system'])
{
$SQL = $odb -> prepare("UPDATE `settings` SET `system` = :system");
$SQL -> execute(array(':system' => $_POST['system']));
$system = $_POST['system'];
}

echo success('Updating settings...<meta http-equiv="refresh" content="3;url=settings.php">');
}

if (isset($_POST['delete']))
{
$delete = $_POST['delete'];
$SQL = $odb -> prepare("DELETE FROM `methods` WHERE `id` = :id");
$SQL -> execute(array(':id' => $delete));
echo success('method has been removed');
}
if (isset($_POST['add']))
{
if (empty($_POST['name']) || empty($_POST['fullname']) || empty($_POST['type']))
{
$error = 'Please verify all fields';
}

if (empty($error))
{
$name = $_POST['name'];
$fullname = $_POST['fullname'];
$type = $_POST['type'];
if ($system=='servers') {$command = $_POST['command'];} else {$command = '';}
$SQLinsert = $odb -> prepare("INSERT INTO `methods` VALUES(NULL, :name, :fullname, :type, :command)");
$SQLinsert -> execute(array(':name' => $name, ':fullname' => $fullname, ':type' => $type, ':command' => $command));
echo success('method has been added');
}
else
{
echo error($error);
}
}

if (isset($_POST['deleteapi']))
{
$delete = $_POST['deleteapi'];
$SQL = $odb -> prepare("DELETE FROM `api` WHERE `id` = :id");
$SQL -> execute(array(':id' => $delete));
echo success('API has been removed');
}
if (isset($_POST['deleteserver']))
{
$delete = $_POST['deleteserver'];
$SQL = $odb -> prepare("DELETE FROM `servers` WHERE `id` = :id");
$SQL -> execute(array(':id' => $delete));
echo success('server has been removed');
}
if (isset($_POST['addapi']))
{
if (empty($_POST['api']) || empty($_POST['name']) || empty($_POST['slots']) || empty($_POST['methods']))
{
$error = 'Please verify all fields';
}
$api = $_POST['api'];
$name = $_POST['name'];
$slots = $_POST['slots'];
$methods = implode(" ",$_POST['methods']);
if (!(is_numeric($slots)))
{
$error = 'Slots field has to be numeric';
}
$parameters = array("[host]", "[port]", "[time]", "[method]");
foreach ($parameters as $parameter)
{
if (strpos($api,$parameter) == false)
{
$error = 'Could not find parameter "'.$parameter.'"';
}
}
if(!ctype_alnum(str_replace(' ','',$name)) || !ctype_alnum(str_replace(' ','',$methods)))
{
$error = 'Invalid characters in the name or methods fields';
}
if (empty($error))
{
$SQLinsert = $odb -> prepare("INSERT INTO `api` VALUES(NULL, :name, :api, :slots, :methods)");
$SQLinsert -> execute(array(':api' => $api, ':name' => $name, ':slots' => $slots, ':methods' => $methods));
echo success('API has been added');
}
else
{
error($error);
}
}

if (isset($_POST['addserver']))
{
if (empty($_POST['ip']) || empty($_POST['password']) || empty($_POST['name']) || empty($_POST['slots']) || empty($_POST['methods']))
{
$error = 'Please verify all fields';
}
$name = $_POST['name'];
$ip = $_POST['ip'];
$password = $_POST['password'];
$slots = $_POST['slots'];
$methods = implode(" ",$_POST['methods']);
if (!(is_numeric($slots)))
{
$error = 'Slots field has to be numeric';
}
if (!filter_var($ip, FILTER_VALIDATE_IP))
{
$error = 'IP is invalid';
}
if(!ctype_alnum(str_replace(' ','',$name)) || !ctype_alnum(str_replace(' ','',$methods)))
{
$error = 'Invalid characters in the name or commands field';
}
if (empty($error))
{
$SQLinsert = $odb -> prepare("INSERT INTO `servers` VALUES(NULL, :name, :ip, :password, :slots, :methods)");
$SQLinsert -> execute(array(':name' => $name, ':ip' => $ip, ':password' => $password, ':slots' => $slots, ':methods' => $methods));
echo success('server has been added');
}
else
{
echo error($error);
}
}

if (isset($_POST['deleteblacklist']))
{
$delete = $_POST['deleteblacklist'];
$SQL = $odb -> query("DELETE FROM `blacklist` WHERE `ID` = '$delete'");
echo success('blacklist has been removed');
}
if (isset($_POST['addblacklist']))
{
if (empty($_POST['value']))
{
$error = 'Please verify all fields';
}
$value = $_POST['value'];
$type = $_POST['type'];
if (empty($error))
{
$SQLinsert = $odb -> prepare("INSERT INTO `blacklist` VALUES(NULL, :value, :type)");
$SQLinsert -> execute(array(':value' => $value, ':type' => $type));
echo success('blacklist has been added');
}
else
{
error($error);
}
}

if (isset($_POST['video2xD']))
{
$date2 = $_POST['date2'];
$video2 = $_POST['video2'];

$SQL = $odb -> prepare("UPDATE `yt` SET `id2` = :id2, `date2` = :date2");
$SQL -> execute(array(':id2' => $video2, ':date2' => $date2));

}

if (isset($_POST['video1xD']))
{
$video1 = $_POST['video1'];
$date1 = $_POST['date1'];

$SQL = $odb -> prepare("UPDATE `yt` SET `id1` = :id1, `date1` = :date1");
$SQL -> execute(array(':id1' => $video1, ':date1' => $date1));

}

if (isset($_POST['addplan']))
{
			$name = $_POST['name'];
			$unit = $_POST['unit'];
			$length = $_POST['length'];
			$mbt = intval($_POST['mbt']);
			$price = floatval($_POST['price']);
			$concurrents = $_POST['concurrents'];
			$private = $_POST['private'];
			$errors = array();
			
			if (empty($price) || empty($name) || empty($unit) || empty($length) || empty($mbt) || empty($concurrents))
			{
				$error = 'Fill in all fields';
			}
			if (empty($error))
			{
				$SQLinsert = $odb -> prepare("INSERT INTO `plans` VALUES(NULL, :name, :mbt, :unit, :length, :price, :concurrents, :private)");
				$SQLinsert -> execute(array(':name' => $name, ':mbt' => $mbt, ':unit' => $unit, ':length' => $length, ':price' => $price, ':concurrents' => $concurrents, ':private' => $private));
				echo success('Plan has been added');
			}
			else
			{
				echo error($error);
			}
}

if (isset($_POST['deleteplan']))
{
$delete = $_POST['deleteplan'];
$SQL = $odb -> query("DELETE FROM `plans` WHERE `ID` = '$delete'");
echo success('plan has been removed');
}

if (isset($_POST['deletenews']))
{
$delete = $_POST['deletenews'];
$SQL = $odb -> query("DELETE FROM `news` WHERE `ID` = '$delete'");
echo success('news has been removed');
}


if (isset($_POST['addnews']))
{
if (empty($_POST['title']) || empty($_POST['content']) || empty($_POST['author']))
{
$error = 'Please verify all fields';
}
if (empty($error))
{
$SQLinsert = $odb -> prepare("INSERT INTO `news` VALUES(NULL, :title, :content, UNIX_TIMESTAMP(), :author)");
$SQLinsert -> execute(array(':title' => $_POST['title'], ':content' => $_POST['content'], ':author' => $_POST['author']));
echo success('News has been added');
}
else
{
echo error($error);
}
}

if (isset($_POST['deletefaq']))
{
$delete = $_POST['deletefaq'];
$SQL = $odb -> query("DELETE FROM `faq` WHERE `id` = '$delete'");
echo success('FAQ has been removed');
}


if (isset($_POST['addfaq']))
{
if (empty($_POST['question']) || empty($_POST['answer']))
{
$error = 'Please verify all fields';
}
if (empty($error))
{
$SQLinsert = $odb -> prepare("INSERT INTO `faq` VALUES(NULL, :question, :answer)");
$SQLinsert -> execute(array(':question' => $_POST['question'], ':answer' => $_POST['answer']));
echo success('FAQ has been added');
}
else
{
echo error($error);
}
}

?>
                    <div class="page-toolbar">
                        
                        <div class="page-toolbar-block">
                            <div class="page-toolbar-title">Settings</div>
                            <div class="page-toolbar-subtitle">Manage Settings</div>
                        </div>                   
                        <ul class="page-toolbar-tabs">
                            <li class="active"><a href="#page-tab-1">General</a></li>
                            <li><a href="#page-tab-2">Hub</a></li>
                            <li><a href="#page-tab-3">Plans</a></li>
                            <li><a href="#page-tab-4">News</a></li>
					   <li><a href="#page-tab-6">VIDEOS</a></li>
                        </ul>
                    </div>  
 <div class="row page-toolbar-tab active" id="page-tab-1">					
                    <div class="row">
                        <div class="col-md-4">
                            <div class="block">
                                <div class="block-content">
                                    <h2><strong>Site</strong> Settings</h2>
                                </div>
                                <div class="block-content controls">
                                    <form method="post">
                                    <div class="row-form">
                                        <div class="col-md-4"><strong>Site Name:</strong></div>
                                        <div class="col-md-8"><input type="text" class="form-control" name="sitename" value="<?php echo htmlspecialchars($sitename); ?>"/></div>
                                    </div>
                                    <div class="row-form">
                                        <div class="col-md-4"><strong>Site Description:</strong></div>
                                        <div class="col-md-8"><input type="text" class="form-control" name="description" value="<?php echo htmlspecialchars($description); ?>"/></div>
                                    </div>
                                    <div class="row-form">
                                        <div class="col-md-4"><strong>ToS URL:</strong></div>
                                        <div class="col-md-8"><input type="text" class="form-control" value="<?php echo htmlspecialchars($tos); ?>" name="tos"/></div>
                                    </div>
                                    <div class="row-form">
                                        <div class="col-md-4"><strong>Site URL:</strong></div>
                                        <div class="col-md-8"><input type="text" class="form-control" name="url" value="<?php echo htmlspecialchars($siteurl); ?>"/></div>
                                    </div>
                                    <div class="row-form">
                                        <div class="col-md-4"><strong>Maintaince Message:</strong></div>
                                        <div class="col-md-8"><input type="text" class="form-control tip" name="maintaince" title="Leave empty for maintaince mode off" value="<?php echo htmlspecialchars($maintaince); ?>"/></div>
                                    </div>
                                    <div class="row-form">
                                        <div class="col-md-4"><strong>Cloudflare Mode:</strong></div>
                                        <div class="col-md-8"><input type="checkbox" name="cloudflare" <?php if ($cloudflare == 1) { echo 'checked'; } ?>/></div>
                                    </div>
									<center><button name="update" class="btn btn-success">Update</button></center>
                                </div>
                                
                            </div>
							</div>
                        <div class="col-md-4">
                            <div class="block">
                                <div class="block-content">
                                    <h2><strong>Billing</strong> Settings</h2>
                                </div>
                                <div class="block-content controls">
								
                                    <div class="panel panel-default">
                                        <div class="panel-heading">
                                            <h4 class="panel-title">
                                                <a data-toggle="collapse" data-parent="#accordion" href="#collapseOne">Paypal</a>
                                            </h4>
                                        </div>
                                        <div id="collapseOne" class="panel-collapse collapse">
                                            <div class="panel-body">
                                    <div class="row-form">
                                        <div class="col-md-4"><strong>Paypal Email:</strong></div>
                                        <div class="col-md-8"><input type="text" class="form-control tip" title="Insert 0 to disable Paypal autobuy" name="paypal" value="<?php echo htmlspecialchars($paypal); ?>"/></div>
                                    </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="panel panel-default">
                                        <div class="panel-heading">
                                            <h4 class="panel-title">
                                                <a data-toggle="collapse" data-parent="#accordion" href="#collapseTwo">Bitcoin</a>
                                            </h4>
                                        </div>
                                        <div id="collapseTwo" class="panel-collapse collapse">
                                            <div class="panel-body">
                                    <div class="row-form">
                                        <div class="col-md-4"><strong>Bitcoin Address:</strong></div>
                                        <div class="col-md-8"><input type="text" class="form-control tip" title="Insert 0 to disable Bitcoin autobuy" name="bitcoin" value="<?php echo htmlspecialchars($bitcoin); ?>"/></div>
                                    </div>
									</div>
                                        </div>
                                    </div>
									<center><button name="update" class="btn btn-success">Update</button></center>
                                </div>
                                
                            </div>
							</div>
                        <div class="col-md-4">
                            <div class="block">
                                <div class="block-content">
                                    <h2><strong>Hub</strong> Settings</h2>
                                </div>
                                <div class="block-content controls">
                                    <div class="row-form">
                                        <div class="col-md-4"><strong>Max Attack Slots:</strong></div>
                                        <div class="col-md-8"><input type="text" class="form-control tip" title="insert 0 to disable" name="maxattacks" value="<?php echo htmlspecialchars($maxattacks); ?>"/></div>
                                    </div>
                                    <div class="row-form">
                                        <div class="col-md-4"><strong>Attack System:</strong></div>
                                        <div class="col-md-8">
                                            <select name="system" class="form-control">
                                                <option value="api" <?php if ($system == 'api') { echo 'selected'; } ?>>API</option>
                                                <option value="servers" <?php if ($system == 'servers') { echo 'selected'; } ?>>Servers</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="row-form">
                                        <div class="col-md-4"><strong>Rotation:</strong></div>
                                        <div class="col-md-8"><input type="checkbox" name="rotation" <?php if ($rotation == 1) { echo 'checked'; } ?>/></div>
                                    </div>
                                    <div class="row-form">
                                        <div class="col-md-4"><strong>Test Boots:</strong></div>
                                        <div class="col-md-8"><input type="checkbox" name="testboots" <?php if ($testboots == 1) { echo 'checked'; } ?>/></div>
                                    </div>
									<center><button name="update" class="btn btn-success">Update</button></center>
									</form>
                                </div>
                                
                            </div>
							</div>
							</div>
</div>
 <div class="row page-toolbar-tab" id="page-tab-2">					
                    <div class="row">
                        <div class="col-md-3">
                            <div class="block">
                                <div class="block-content">
                                    <h2><strong>Methods</strong> Manager</h2>
                                </div>
                                    <table class="table table-striped">
                                        <tr>
                                            <th>Name</th><th>Tag</th><th>Type</th><?php if($system == 'servers'){echo '<th>command</th>';}?><th>Delete</th>
                                        </tr>
                                        <tr>
										<form method="post">
<?php
$SQLGetMethods = $odb -> query("SELECT * FROM `methods`");
while($getInfo = $SQLGetMethods -> fetch(PDO::FETCH_ASSOC))
{
 $id = $getInfo['id'];
 $name = $getInfo['name'];
 $fullname = $getInfo['fullname'];
 $type = $getInfo['type'];
 if ($system == 'servers') {$command = '<td>'.$getInfo['command'].'</td>';} else {$command = '';}
 echo '<tr><td>'.htmlspecialchars($name).'</td><td>'.htmlspecialchars($fullname).'</td><td>'.$type.'</td>'.$command.'<td><button name="delete" value="'.$id.'" class="btn btn-danger"><i class="fa fa-trash-o"></i></button></td></tr>';
}
if (empty($SQLGetMethods))
{
echo 'No logs';
}
?>
</form>
                                        </tr>                                       
                                    </table>
                                <div class="panel-group" id="accordion">
                                    <div class="panel panel-default">
                                        <div class="panel-heading">
                                            <h4 class="panel-title">
                                                <a data-toggle="collapse" data-parent="#accordion" href="#collapse1">Add a New Method</a>
                                            </h4>
                                        </div>
                                        <div id="collapse1" class="panel-collapse collapse">
                                            <div class="panel-body">
                                <div class="block-content controls">
								<form method="post">
                                    <div class="row-form">
                                        <div class="col-md-4"><strong>Name:</strong></div>
                                        <div class="col-md-8"><input type="text" class="form-control tip" title="This will be used when executing the attack" name="name"/></div>
                                    </div>
                                    <div class="row-form">
                                        <div class="col-md-4"><strong>Tag Name:</strong></div>
                                        <div class="col-md-8"><input type="text" class="form-control tip" title="This will be used on the Hub page" name="fullname"/></div>
                                    </div>
                                    <div class="row-form">
                                        <div class="col-md-4"><strong>Type:</strong></div>
                                        <div class="col-md-8">
                                            <select name="type" class="form-control">
									<option value="spe">Special</option>
                                                <option value="tcp">TCP</option>
									<option value="udp">UDP</option>
                                                <option value="layer7">Layer7</option>
                                            </select>
                                        </div>
                                    </div>
<?php
if ($system == 'servers')
{
?>
                                    <div class="row-form">
                                        <div class="col-md-4"><strong>Command:</strong></div>
                                        <div class="col-md-8"><input type="text" class="form-control tip" title="Syntax: {$host} {$port} {$time}" name="command"/></div>
                                    </div>
<?php
}
?>
									<button name="add" class="btn btn-success">Submit</button>
									</form>                                            </div>
                                        </div>
                                    </div>
								</div>
                                </div>
                                
                            </div>
							</div>
                        <div class="col-md-3">
                            <div class="block">
                                <div class="block-content">
                                    <h2><strong>Blacklist</strong> Manager</h2>
                                </div>
                                    <table class="table table-striped">
                                        <tr>
                                            <th>Value</th><th>Type</th><th>Delete</th>
                                        </tr>
                                        <tr>
										<form method="post">
<?php
$SQLGetMethods = $odb -> query("SELECT * FROM `blacklist`");
while($getInfo = $SQLGetMethods -> fetch(PDO::FETCH_ASSOC))
{
 $id = $getInfo['ID'];
 $value = $getInfo['data'];
 $type = $getInfo['type'];
 echo '<tr><td>'.htmlspecialchars($value).'</td><td>'.htmlspecialchars($type).'</td><td><button name="deleteblacklist" value="'.$id.'"class="btn btn-danger"><i class="fa fa-trash-o"></i></button></td></tr>';
}
?>
</form>
                                        </tr>                                       
                                    </table>
                                <div class="panel-group" id="accordion">
                                    <div class="panel panel-default">
                                        <div class="panel-heading">
                                            <h4 class="panel-title">
                                                <a data-toggle="collapse" data-parent="#accordion" href="#collapse2">Add a New Blacklist</a>
                                            </h4>
                                        </div>
                                        <div id="collapse2" class="panel-collapse collapse">
                                            <div class="panel-body">
                                <div class="block-content controls">
                                    <form method="post">
                                    <div class="row-form">
                                        <div class="col-md-4"><strong>Value:</strong></div>
                                        <div class="col-md-8"><input type="text" class="form-control" name="value"/></div>
                                    </div>
                                    <div class="row-form">
                                        <div class="col-md-4"><strong>Type:</strong></div>
                                        <div class="col-md-8">
                                            <select name="type" class="form-control">
                                                <option value="victim">Website/IP Address</option>
                                            </select>
                                        </div>
                                    </div>
									<button name="addblacklist" class="btn btn-success">Submit</button>
									</form>
                                </div>
								</div>
                                        </div>
                                    </div>
								</div>
                                
                            </div>
							</div>
                        <div class="col-md-6">
                            <div class="block">
                                <div class="block-content">
                                    <h2><strong><?php if ($system == 'api') { echo 'APIs'; } else { echo 'Servers'; } ?></strong> Manager</h2>
                                </div>
                                    <table class="table table-striped">
                                        <tr>
<?php if ($system == 'api') { ?>
                    <th>Name</th>
                    <th>API</th>
                    <th>Slots</th>
                    <th>Methods</th>
                    <th>Delete</th>
<?php } else { ?>
                    <th>Name</th>
                    <th>IP</th>
                    <th>Slots</th>
                    <th>Methods</th>
                    <th>Delete</th>
<?php } ?>
                                        </tr>
                                        <tr>
										<form method="post">
<?php
if ($system == 'api') {
$SQLGetMethods = $odb -> query("SELECT * FROM `api`");
while($getInfo = $SQLGetMethods -> fetch(PDO::FETCH_ASSOC))
{
 $id = $getInfo['id'];
 $api = $getInfo['api'];
 $name = $getInfo['name'];
 $slots = $getInfo['slots'];
 $methods = $getInfo['methods'];
 echo '<tr><td>'.htmlspecialchars($name).'</td><td>'.htmlspecialchars($api).'</td><td>'.htmlspecialchars($slots).'</td><td>'.htmlspecialchars($methods).'</td><td><button type="submit" title="Delete API" name="deleteapi" value="'.htmlspecialchars($id).'" class="btn btn-danger"><i class="fa fa-trash-o"></i></button></td></tr>';
}
} else {
$SQLGetMethods = $odb -> query("SELECT * FROM `servers`");
while($getInfo = $SQLGetMethods -> fetch(PDO::FETCH_ASSOC))
{
 $name = $getInfo['name'];
 $id = $getInfo['id'];
 $ip = $getInfo['ip'];
 $slots = $getInfo['slots'];
 $methods = $getInfo['methods'];
 echo '<tr><td>'.htmlspecialchars($name).'</td><td>'.htmlspecialchars($ip).'</td><td>'.htmlspecialchars($slots).'</td><td>'.htmlspecialchars($methods).'</td><td><button type="submit" title="Delete Server" name="deleteserver" value="'.htmlspecialchars($id).'" class="btn btn-danger"><i class="fa fa-trash-o"></i></button></td></tr>';
}
}
?>
</form>
                                        </tr>                                       
                                    </table>
                                <div class="panel-group" id="accordion">
                                    <div class="panel panel-default">
                                        <div class="panel-heading">
                                            <h4 class="panel-title">
                                                <a data-toggle="collapse" data-parent="#accordion" href="#collapse3">Add a new <?php if ($system == 'api') { echo 'API'; } else { echo 'Server'; } ?></a>
                                            </h4>
                                        </div>
                                        <div id="collapse3" class="panel-collapse collapse">
                                            <div class="panel-body">
                                <div class="block-content controls">
								<form method="post">
<?php if ($system == 'api') { ?>
                                    <div class="row-form">
                                        <div class="col-md-4"><strong>Name:</strong></div>
                                        <div class="col-md-8"><input type="text" class="form-control" name="name"/></div>
                                    </div>
                                    <div class="row-form">
                                        <div class="col-md-4"><strong>API Link:</strong></div>
                                        <div class="col-md-8"><input type="text" class="form-control tip" title="Syntax: [host], [port], [time], [method]" name="api"/></div>
                                    </div>
                                    <div class="row-form">
                                        <div class="col-md-4"><strong>Slots:</strong></div>
                                        <div class="col-md-8"><input type="text" class="form-control" name="slots"/></div>
                                    </div>
                                    <div class="row-form">
                                        <div class="col-md-4"><strong>Allowed Methods:</strong></div>
                                        <div class="col-md-8">
                                            <select class="form-control" name="methods[]" multiple="multiple">
<?php
$SQLGetMethods = $odb -> query("SELECT * FROM `methods`");
while($getInfo = $SQLGetMethods -> fetch(PDO::FETCH_ASSOC))
{
 $name = $getInfo['name'];
 echo '<option value="'.$name.'">'.$name.'</option>';
 }
?>
                                            </select>
										</div>
                                    </div>
									<button name="addapi" class="btn btn-success">Submit</button>
									</form>
									<form method="post">
<?php
}
else
{
?>
                                    <div class="row-form">
                                        <div class="col-md-4"><strong>Name:</strong></div>
                                        <div class="col-md-8"><input type="text" class="form-control" name="name"/></div>
                                    </div>
                                    <div class="row-form">
                                        <div class="col-md-4"><strong>IP:</strong></div>
                                        <div class="col-md-8"><input type="text" class="form-control" name="ip"/></div>
                                    </div>
                                    <div class="row-form">
                                        <div class="col-md-4"><strong>Password:</strong></div>
                                        <div class="col-md-8"><input type="password" class="form-control" name="password"/></div>
                                    </div>
                                    <div class="row-form">
                                        <div class="col-md-4"><strong>Slots:</strong></div>
                                        <div class="col-md-8"><input type="text" class="form-control" name="slots"/></div>
                                    </div>
                                    <div class="row-form">
                                        <div class="col-md-4"><strong>Allowed Methods:</strong></div>
                                        <div class="col-md-8">
                                            <select class="form-control" name="methods[]" multiple="multiple">
<?php
$SQLGetMethods = $odb -> query("SELECT * FROM `methods`");
while($getInfo = $SQLGetMethods -> fetch(PDO::FETCH_ASSOC))
{
 $name = $getInfo['name'];
 echo '<option value="'.$name.'">'.$name.'</option>';
 }
?>
                                            </select>
										</div>
                                    </div>
									<button name="addserver" class="btn btn-success">Submit</button>
<?php
}
?>
</form>
                                </div>                                            </div>
                                        </div>
                                    </div>
								</div>
                                
                            </div>
							</div>
							</div>
</div>
 <div class="row page-toolbar-tab" id="page-tab-3">					
<div class="row">
                            <div class="col-md-10">
                                <div class="block">
                                    <ul class="nav nav-tabs nav-justified">
                                        <li class="active"><a href="#tab1" data-toggle="tab">View Plans</a></li>
                                        <li><a href="#tab2" data-toggle="tab">Create a New Plan</a></li>
                                    </ul>
                                    <div class="block-content tab-content">
                                        <div class="tab-pane active" id="tab1">
<p>
                                    <table class="table table-striped">
                                        <tr>
                        <th>Name</th>
                        <th>Max Boot Time</th>
                        <th>Price</th>
                        <th>Length</th>
                        <th>Concurrents</th>
                        <th>Private</th>
                        <th>Sales</th>
                  </tr>
                </thead>
                <tbody>
				<form method="post">
<?php
$SQLSelect = $odb -> query("SELECT * FROM `plans` ORDER BY `price` ASC");
while ($show = $SQLSelect -> fetch(PDO::FETCH_ASSOC))
{
	$unit = $show['unit'];
	$length = $show['length'];
	$price = $show['price'];
	$concurrents = $show['concurrents'];
	$planName = $show['name'];
	$mbtShow = $show['mbt'];
	$id = $show['ID'];
	if ($show['private'] == 0) { $private = 'No'; } else { $private = 'Yes'; }
	$sales = $odb->query("SELECT COUNT(*) FROM `payments` WHERE `plan` = '$id'")->fetchColumn(0);
	echo '<tr><td><a href="plan.php?id='.$id.'">'.htmlspecialchars($planName).'</a></td><td><center>'.$mbtShow.' Seconds</center></td><td><center>$'.htmlentities($price).'</center></td><td><center>'.htmlentities($length).' '.htmlentities($unit).'</center></td><td><center>'.htmlentities($concurrents).'</center></td><td><center>'.htmlentities($private).'</center></td><td><center>'.$sales.'</center></td></tr>';
}
?>
</form>
                                        </tr>                                       
                                    </table>
</p>
                                        </div>
                                        <div class="tab-pane" id="tab2">
<p>
                                <div class="block-content controls">
								<form method="post">
                                    <div class="row-form">
                                        <div class="col-md-4"><strong>Name:</strong></div>
                                        <div class="col-md-8"><input type="text" class="form-control" name="name"/></div>
                                    </div>
                                    <div class="row-form">
                                        <div class="col-md-4"><strong>Price:</strong></div>
                                        <div class="col-md-8"><input type="text" class="form-control" name="price"/></div>
                                    </div>
                                    <div class="row-form">
                                        <div class="col-md-4"><strong>Max Boot Time:</strong></div>
                                        <div class="col-md-8"><input type="text" class="form-control" name="mbt"/></div>
                                    </div>
                                    <div class="row-form">
                                        <div class="col-md-4"><strong>Concurrent Attacks:</strong></div>
                                        <div class="col-md-8"><input type="text" class="form-control" name="concurrents"/></div>
                                    </div>
                                    <div class="row-form">
                                        <div class="col-md-4"><strong>Membership Length:</strong></div>
                                        <div class="col-md-8"><input type="text" class="form-control" name="length"/></div>
                                    </div>
                                    <div class="row-form">
                                        <div class="col-md-4"><strong>Unit:</strong></div>
                                        <div class="col-md-8">
                                            <select name="unit" class="form-control">
                                                <option value="days">Days</option>
												<option value="weeks">Weeks</option>
                                                <option value="months">Months</option>
												<option value="years">Years</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="row-form">
                                        <div class="col-md-4"><strong>Private:</strong></div>
                                        <div class="col-md-8">
                                            <select name="private" class="form-control">
                                                <option value="1">Yes</option>
												<option value="0">No</option>
                                            </select>
                                        </div>
                                    </div>
									<button name="addplan" class="btn btn-success">Submit</button>
									</form>
                                </div>
</p>
                                        </div>                    
                                    </div>
                                </div>
</div>
</div>
</div>

 <div class="row page-toolbar-tab" id="page-tab-4">					
<div class="row">
<div class="col-md-10">
                                <div class="block">
                                    <ul class="nav nav-tabs nav-justified">
                                        <li class="active"><a href="#tab3" data-toggle="tab">View News</a></li>
                                        <li><a href="#tab4" data-toggle="tab">Create a News Item</a></li>
                                    </ul>
                                    <div class="block-content tab-content">
                                        <div class="tab-pane active" id="tab3">
<p>
                                    <table class="table table-striped">
                                        <tr>
                    <th>Title</th>
                    <th>Date</th>
                    <th>Delete</th>
                  </tr>
                </thead>
                <tbody>
				<form method="post">
			<?php 
			$SQLGetNews = $odb -> query("SELECT * FROM `news` ORDER BY `date` DESC");
			while ($getInfo = $SQLGetNews -> fetch(PDO::FETCH_ASSOC))
			{
				$id = $getInfo['ID'];
				$title = $getInfo['title'];
				$date = date("m-d-Y, h:i:s a" ,$getInfo['date']);
				echo '<tr><td>'.htmlspecialchars($title).'</td><td>'.$date.'</td><td><button name="deletenews" value="'.$id.'"class="btn btn-danger"><i class="fa fa-trash-o"></i></button></td></tr>';
			}
			?>
</form>
                                        </tr>                                       
                                    </table>
</p>
                                        </div>
                                        <div class="tab-pane" id="tab4">
<p>
                                <div class="block-content controls">
								<form method="post">
                                    <div class="row-form">
                                        <div class="col-md-4"><strong>Title:</strong></div>
                                        <div class="col-md-8"><input type="text" class="form-control" name="title"/></div>
                                    </div>
                                    <div class="row-form">
                                        <div class="col-md-4"><strong>Content:</strong></div>
                                        <div class="col-md-8"><input type="text" class="form-control" name="content"/></div>
                                    </div>
							 <div class="row-form">
                                        <div class="col-md-8"><input type="hidden" class="form-control" name="author" value="<?php echo $_SESSION['username']; ?>" /></div>
                                    </div>
									<button name="addnews" class="btn btn-success">Submit</button>
									</form>
                                </div>
</p>
                                        </div>                    
                                    </div>
                                </div>
</div>
</div>
</div>


 <div class="row page-toolbar-tab" id="page-tab-6">					
                    <div class="row">
				  <div class="block">
				  
                        <div class="col-md-3">
						<div class="block-content controls">
						<form method="post">
                                    <div class="row-form">
                                        <div class="col-md-4"><strong>Video1:</strong></div>
                                        <div class="col-md-8"><input type="text" class="form-control" value="<?php echo $video1; ?>" name="video1"/></div>
                                    </div>
                                    <div class="row-form">
                                        <div class="col-md-4"><strong>Date1:</strong></div>
                                        <div class="col-md-8"><input type="text" class="form-control" value="<?php echo $date1; ?>" name="date1"/></div>
                                    </div>
							<button name="video1xD" class="btn btn-success">Update</button>
						</form>
					</div></div>
					<div class="col-md-3">
						<div class="block-content controls">
						<form method="post">
                                    <div class="row-form">
                                        <div class="col-md-4"><strong>Video2:</strong></div>
                                        <div class="col-md-8"><input type="text" class="form-control" value="<?php echo $video2; ?>" name="video2"/></div>
                                    </div>
                                    <div class="row-form">
                                        <div class="col-md-4"><strong>Date2:</strong></div>
                                        <div class="col-md-8"><input type="text" class="form-control" value="<?php echo $date2; ?>"	name="date2"/></div>
                                    </div>
							<button name="video2xD" class="btn btn-success">Update</button>
						</form>
					</div></div>
				</div></div>
			</div>
                    
                </div>
                
            </div>
            <div class="page-sidebar"></div>
        </div>
    </body>
</html>