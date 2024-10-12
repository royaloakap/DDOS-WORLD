<?php
if (isset($_GET['id']))
{
$id = $_GET['id'];
if(is_numeric($id) == false) {
echo "Invalid New ID";
exit;
}
require '@/config.php';
require '@/init.php';
if ($cloudflare == 1)
{
$ip = $_SERVER["HTTP_CF_CONNECTING_IP"];
}
else
{
$ip = $_SERVER['REMOTE_ADDR'];
}
		$SQL = $odb -> prepare('INSERT INTO `iplogs` VALUES(NULL, :id, :ip, UNIX_TIMESTAMP())');
		$SQL -> execute(array(':id' => $id, ':ip' => $ip));
		header('location: http://google.com');
		
}
else
{
	header('location: http://google.com');
}
?>