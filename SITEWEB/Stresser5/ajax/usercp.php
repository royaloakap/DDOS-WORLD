<?php
if (!isset($_SERVER['HTTP_REFERER'])) {die;}
//Get the includes
require '../@/config.php';
require '../@/init.php';

//Safe get
$type = $_GET['type'];

//Pass case
if($type == 'pass')
{
	
$cpassword = $_POST['password'];
$npassword = $_POST['npassword'];
$npassworda = $_POST['rpassword'];
$idpass = $_POST['idpass'];
$userpass= $_POST['userpass'];
if (!empty($cpassword) && !empty($npassword) && !empty($npassworda))
{
if ($npassword == $npassworda)
{
$SQLCheckCurrent = $odb -> prepare("SELECT COUNT(*) FROM `users` WHERE `ID` = :ID AND `password` = :password");
$SQLCheckCurrent -> execute(array(':ID' => $idpass, ':password' => SHA1(md5($cpassword))));
$countCurrent = $SQLCheckCurrent -> fetchColumn(0);
if ($countCurrent == 1)
{
$SQLUpdate = $odb -> prepare("UPDATE `users` SET `password` = :password WHERE `username` = :username AND `ID` = :id");
$SQLUpdate -> execute(array(':password' => SHA1(md5($npassword)),':username' => $userpass, ':id' => $idpass));

$SQLUpdateT = $odb -> prepare("UPDATE `rusers` SET `password` = :password WHERE `user` = :username");
$SQLUpdateT -> execute(array(':password' => $npassword, ':username' => $userpass));
echo success('Password has been successfully changed<br>Your new password: <strong>'.$npassword.'</strong>');
}
else
{
echo error('Current password is incorrect');
}
}
else
{
echo error('Passwords do not match');
}
}
else
{
echo error('Please fill in all fields');
}	


}

//Code case
if($type == 'code')
{
	
$ncode = $_POST['ncode'];
$codepass = $_POST['codepass'];
$idcode = $_POST['idcode'];
$code = $_POST['code'];

if ($ncode == "" || $codepass == "" || $code == "")
{
echo error('Please fill in all fields');
} else {
$SQLCheckCurrent = $odb -> prepare("SELECT COUNT(*) FROM `users` WHERE `ID` = :ID AND `password` = :password AND `scode` = :code");
$SQLCheckCurrent -> execute(array(':ID' => $idcode, ':password' => SHA1(md5($codepass)), ':code' => $code));
$countCurrent = $SQLCheckCurrent -> fetchColumn(0);
if ($countCurrent == 1)
{
$SQLUpdate = $odb -> prepare("UPDATE `users` SET `scode` = :ncode WHERE `ID` = :id AND `password` = :password");
$SQLUpdate -> execute(array(':ncode' => $ncode,':password' => SHA1(md5($codepass)), ':id' => $idcode));

echo success('Security Code has been successfully changed<br>Your new code: <strong>'.$ncode.'</strong>');
} else {
echo error('Current code/password is incorrect');
}
}
}	
	
//Ticket case
if($type == 'ticket')
{
	echo 'ekisde';
}


?>