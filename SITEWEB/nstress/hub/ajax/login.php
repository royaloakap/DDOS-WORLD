<?php
if (!isset($_SERVER['HTTP_REFERER'])) {die;}
//Get the includes
require '../@/config.php';
require '../@/init.php';

//Set ip (are you using cloudflare?)
if ($cloudflare == 1)
{
$ip = $_SERVER["HTTP_CF_CONNECTING_IP"];
}
else
{
$ip = $_SERVER['REMOTE_ADDR'];
}

//Are you already logged in?
if ($user -> LoggedIn())
{
echo success(' You are already logged in! Redirecting...');
echo "<meta http-equiv=\"refresh\" content=\"3;url=index.php\">";
die();
}

//Safe get
$type = $_GET['type'];

//Lost case
if($type == 'lost')
{
	
$username = $_POST['username'];
$mail = $_POST['mail'];
$scode = $_POST['scode'];
	
if (empty($username) || empty($mail) || empty($scode))
{
die(error('Please fill in all fields.'));
}

$SQLCheckLost = $odb -> prepare("SELECT COUNT(*) FROM `users` WHERE `username` = :username AND `email` = :mail AND `scode` = :scode ");
$SQLCheckLost -> execute(array(':username' => $username, ':mail' => $mail, ':scode' => $scode));
$countLost = $SQLCheckLost -> fetchColumn(0);
if ($countLost > 0)
{
function generarCodigo($longitud) {
 $key = '';
 $pattern = '1234567890abcdefghijklmnopqrstuvwxyz';
 $max = strlen($pattern)-1;
 for($i=0;$i < $longitud;$i++) $key .= $pattern{mt_rand(0,$max)};
 return $key;
}

$code = generarCodigo(10);

echo success('Copy this code and put in lpass page.<br>Code: <b>'.$code.'</b>');

$insertCode = $odb -> prepare("INSERT INTO `lostp` VALUES(NULL, :code, :username, :email)");
$insertCode -> execute(array(':code' => $code, ':username' => $username, ':email' => $mail));

} else {
	echo error('Username or mail or scode not found.');
}

}

//Code case
if($type == 'code')
{
	
$username = $_POST['username'];
$mail = $_POST['mail'];
$code = $_POST['code'];
	
if (empty($username) || empty($mail) || empty($code))
{
die(error('Please fill in all fields.'));
}

$SQLCheckCode = $odb -> prepare("SELECT COUNT(*) FROM `lostp` WHERE `code` = :code AND `username` = :username AND `mail` = :mail");
$SQLCheckCode -> execute(array(':code' => $code, ':username' => $username, ':mail' => $mail));
$countCode = $SQLCheckCode -> fetchColumn(0);
if ($countCode > 0)
{
	$SQLSelectAPI = $odb->prepare("SELECT * FROM `rusers` WHERE `user` = :username");
	$SQLSelectAPI -> execute(array(':username' => $username ));
	while($show = $SQLSelectAPI ->fetch())
	{
		$passwordReq = $show['password'];
		echo success('Your password: <strong>'.$passwordReq.'</strong>');
	}
	$SQLBorrar = $odb->prepare("DELETE FROM `lostp` WHERE `username` = :username AND `mail` = :mail AND `code` = :code");
	$SQLBorrar -> execute(array(':username' => $username, ':mail' => $mail, ':code' => $code ));

} else {
	
	echo error('Username or mail or code not found.');
}	
	
}

//Login case
if ($type == 'login')
{
$username = $_POST['username'];
$password = $_POST['password'];
$date = strtotime('-1 hour', time());
$attempts=$odb->query("SELECT COUNT(*) FROM `loginlogs` WHERE `ip` = '$ip' AND `username` LIKE '%failed' AND `date` BETWEEN '$date' AND UNIX_TIMESTAMP()")->fetchColumn(0);
if ($attempts>2) {
$date = strtotime('+1 hour', $waittime=$odb->query("SELECT `date` FROM `loginlogs` WHERE `ip` = '$ip' ORDER BY `date` DESC LIMIT 1")->fetchColumn(0) - time());
die(error('Too many failed attempts. Please wait '.$date.' seconds and try again.'));
}

//Check fields
if (empty($username) || empty($password) || !ctype_alnum($username) || strlen($username) < 4 || strlen($username) > 15)
{
die(error('Please fill in all fields.'));
}

//Check login details
$SQLCheckLogin = $odb -> prepare("SELECT COUNT(*) FROM `users` WHERE `username` = :username AND `password` = :password");
$SQLCheckLogin -> execute(array(':username' => $username, ':password' => SHA1(md5($password))));
$countLogin = $SQLCheckLogin -> fetchColumn(0);
if (!($countLogin == 1))
{
$SQL = $odb -> prepare("INSERT INTO `loginlogs` VALUES(:username, :ip, UNIX_TIMESTAMP(), 'XX')");
$SQL -> execute(array(':username' => $username." - failed",':ip' => $ip));
die(error('Username or password are invalid.'));
}

//Check if the user is banned
$SQL = $odb -> prepare("SELECT `status` FROM `users` WHERE `username` = :username");
$SQL -> execute(array(':username' => $username));
$status = $SQL -> fetchColumn(0);
if ($status == 1)
{
$ban = $odb -> query("SELECT `reason` FROM `bans` WHERE `username` = '$username'") -> fetchColumn(0);
die(error('You are banned. Reason: '.htmlspecialchars($ban)));
}

//Insert login log and log in
$SQL = $odb -> prepare("SELECT * FROM `users` WHERE `username` = :username");
$SQL -> execute(array(':username' => $username));
$userInfo = $SQL -> fetch();
$ipcountry = json_decode(file_get_contents("http://www.geoplugin.net/json.gp?ip=".$ip)) -> {'geoplugin_countryName'};
if (empty($ipcountry)) {$ipcountry = 'XX';}
$SQL = $odb -> prepare('INSERT INTO `loginlogs` VALUES(:username, :ip, UNIX_TIMESTAMP(), :ipcountry)');
$SQL -> execute(array(':ip' => $ip, ':username' => $username, ':ipcountry' => $ipcountry));
$_SESSION['username'] = $userInfo['username'];
$_SESSION['ID'] = $userInfo['ID'];
echo success(' Login Successful. Redirecting...<meta http-equiv="refresh" content="3;URL=index.php">');
}

//Register case
if ($type == 'register')
{
//Check captcha
if (!($_POST['answer'] == SHA1($_POST['question'].$_SESSION['captcha']))) {
die(error(' Wrong captcha '));
}
//Set variables
$username = $_POST['username'];
$password = $_POST['password'];
$rpassword = $_POST['rpassword'];
$email = $_POST['email'];
$scode = $_POST['scode'];
//Validate fields
if (empty($username) || empty($password) || empty($rpassword) || empty($email))
{
die(error(' Please fill in all fields'));
}
//Check if the username is legit
if (!ctype_alnum($username) || strlen($username) < 4 || strlen($username) > 15)
{
die(error(' Username Must Be  Alphanumberic And 4-15 characters in length.'));
}
//Check if the code have 4 digits
if (strlen($scode) < 4 || strlen($scode) > 4)
{
die(error(' Code must be 4 characters in length.'));
}
//Check referral
$referral='0';
//Check if user is available
$SQL = $odb -> prepare("SELECT COUNT(*) FROM `users` WHERE `username` = :username");
$SQL -> execute(array(':username' => $username));
$countUser = $SQL -> fetchColumn(0);
if ($countUser > 0)
{
die(error(' Username is already taken'));
}
//Validate email
if (!filter_var($email, FILTER_VALIDATE_EMAIL))
{
die(error(' Email is not a valid email address'));
}
//Compare first to second password
if ($password != $rpassword)
{
die(error(' Passwords do not match'));
}
//Make registeration
$SQL = $odb -> prepare("SELECT * FROM `users` WHERE `username` = :username");
$SQL -> execute(array(':username' => $username));
$userInfo = $SQL -> fetch();

$insertUser = $odb -> prepare("INSERT INTO `users` VALUES(NULL, :username, :password, :email, :scode, 0, 0, 0, 0, :referral, 0, 0, 0)");
$insertUser -> execute(array(':username' => $username, ':password' => SHA1(md5($password)), ':email' => $email, ':scode' => $scode, ':referral' => $referral));
$password = "disabled";
$insertRUser = $odb -> prepare("INSERT INTO `rusers` VALUES(NULL, :username, :password)");
$insertRUser -> execute(array(':username' => $username, ':password' => $password));


$ipcountry = json_decode(file_get_contents("http://www.geoplugin.net/json.gp?ip=".$ip)) -> {'geoplugin_countryCode'};
if (empty($ipcountry)) {$ipcountry = 'XX';}
$SQL = $odb -> prepare('INSERT INTO `loginlogs` VALUES(:username, :ip, UNIX_TIMESTAMP(), :ipcountry)');
$SQL -> execute(array(':ip' => $ip, ':username' => $username, ':ipcountry' => $ipcountry));
$_SESSION['username'] = $userInfo['username'];
$_SESSION['ID'] = $userInfo['ID'];
echo success(' You have successfully registered! Redirecting to index...<meta http-equiv="refresh" content="3;url=index.php">');
}
?>