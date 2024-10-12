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

if (isset($_POST['newaddon']))
{
			$addon = $_POST['addon'];
			$price = floatval($_POST['price']);
			$errors = array();
			
			if (empty($price) || empty($addon))
			{
				$error = 'Fill in all fields';
			}
			if (empty($error))
			{
				$SQLinsert = $odb -> prepare("INSERT INTO `addon` VALUES(NULL, :addons, :price)");
				$SQLinsert -> execute(array(':addon' => $addon, ':price' => $price));
				echo success('Addons has been added');
			}
			else
			{
				echo error($error);
			}
}

			if (isset($_POST['addondrop']))
               {
             $delete = $_POST['addondrop'];
             $SQL = $odb -> prepare("DELETE FROM `addons` WHERE `id` = :id");
             $SQL -> execute(array(':id' => $delete));
             echo success('Addon has been removed <meta http-equiv="refresh" content="3;url=addons.php">');
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


?>