<?php 

//if (basename($_SERVER['SCRIPT_FILENAME']) == basename(__FILE__)) {exit("Disabled");}
ob_start();
require_once '../@/config.php';
require_once '../@/init.php';

$name = $_GET['sv'];


$parameters = array("<script", "alert(", "<iframe", ".css", ".js", "<meta", ">", "UPDATE", "*", ";", "<", "<frame", "<img", "<embed", "<xml", "<IMG", "<SCRIPT", "<IFRAME", "<META", "<FRAME", "<EMBED", "<XML");
		foreach ($parameters as $parameter)
		{
			if (strpos($name,$parameter) !== false)
			{
				die('SUCK MY DIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIICK GUZ <3 <3 BLUNTER IS SHIT AHAHAHAHHAH');
			}
			else 
			{ 
				
			}
		}




if ($system == 'api') {
	$SQLGetInfo = $odb->query("SELECT * FROM `api` ORDER BY `id` DESC");
	} else {
	$SQLGetInfo = $odb->query("SELECT * FROM `servers` ORDER BY `id` DESC");
}

if ($_GET['sv'] == "")
{
	die('Put server name.');
}

$gsent = $odb->prepare("SELECT COUNT(*) FROM `logs` WHERE `handler` LIKE '%$name%' AND `time` + `date` > UNIX_TIMESTAMP() AND `stopped` = 0");
$gsent->execute();
$resultado = $gsent->fetchColumn();

echo $resultado;

	

?>