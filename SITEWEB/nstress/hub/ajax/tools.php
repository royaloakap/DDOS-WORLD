<?php
if (!isset($_SERVER['HTTP_REFERER'])) {die;}
//Header
require_once '../@/config.php';
require_once '../@/init.php';

$type = $_GET['type'];
function get_data($url)
{
	$ch = curl_init();
	$timeout = 5;
	curl_setopt($ch,CURLOPT_URL,$url);
	curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
	curl_setopt($ch,CURLOPT_CONNECTTIMEOUT,$timeout);
	$data = curl_exec($ch);
	curl_close($ch);
	return $data;
} 
				
//Skype resolver
if ($type == 'skype')
{
$skypeName = $_POST['skypeName'];
if($skypeName == "")
{
	echo "Please verify all fields";
} else {
$skypeResolve = get_data('http://google.commmm/api.php?key=WXG1F-7XZVI-AWBZS-YVUQN&action=resolve&string='.$skypeName.'');
echo $skypeResolve;
}
}

//Skype resolver last ip
if ($type == 'skypelast')
{
$skypeName = $_POST['skypeName'];
if($skypeName == "")
{
	echo "Please verify all fields";
} else {
$skypeResolve = get_data('http://google.commmmm'.$skypeName.'');
echo $skypeResolve;
}
}


//Domain resolver
if ($type == 'http')
{
$webName = $_POST['webName'];
if($webName == "")
{
	echo "Please verify all fields";
} else {
$webResolve = get_data('https://api.apithis.net/host2ip.php?hostname='.$webName.'');
if(filter_var($webResolve, FILTER_VALIDATE_IP)) {
echo $webResolve;
} else {
	echo "Invalid domain";
}
}
}

// Geo
if ($type == 'geo')
{
$geoName = $_POST['geoName'];
if($geoName == "")
{
	echo "Please verify all fields";
} else {

if(filter_var($geoName, FILTER_VALIDATE_IP)) {
$geoResolve = get_data('https://api.apithis.net/geoip.php?ip='.$geoName.'');
echo $geoResolve;
} else {
	echo "Put a valid IP Address";
}
}
}

// DB
if ($type == 'db')
{
$dbName = $_POST['dbName'];
if($dbName == "")
{
	echo "Please verify all fields";
} else {

$dbResolve = get_data('https://api.apithis.net/skypedb.php?username='.$dbName.'');
echo $dbResolve;
}
}

//Cloudflare resolver
if ($type == 'cloudflare')
{
function get_host($ip){
        $ptr= implode(".",array_reverse(explode(".",$ip))).".in-addr.arpa";
        $host = dns_get_record($ptr,DNS_PTR);
        if ($host == null) return $ip;
        else return $host[0]['target'];
} 
function isCloudflare($ip)
{
$host = get_host($ip);
if($host=="cf-".implode("-", explode(".", $ip)).".cloudflare.com")
{
return true;
} else {
return false;
}
}
$lookupArr = array("mail.", "direct.", "direct-connect.", "direct-connect-mail.", "cpanel.", "ftp.");
$output = array();
foreach ($lookupArr as $lookupKey)
{
$lookupHost = $lookupKey . $value;
$foundHost = gethostbyname($lookupHost);

if ($foundHost == $lookupHost)
{
$output[] = "No DNS Found";
}
else
{
$extra = "<font color=\"green\">(Not Cloudflare)</font>";
if(isCloudflare($foundHost))
{
$extra = "<font color=\"red\">(Cloudflare)</font>";
}
$output[] = $foundHost." ".$extra;
}
}
echo '<li> Mail - '.$output[0].' </li><li> Direct - '.$output[1].' </li><li> Direct-Connect - '.$output[2].'</li><li>Direct-Connect-Mail - '.$output[3].'</li><li>Cpanel - '.$output[4].'</li><li>FTP - '.$output[5];
}

//Ping
if ($type == 'ping')
{
if (!filter_var($value, FILTER_VALIDATE_IP) && (!filter_var(gethostbyname($value), FILTER_VALIDATE_IP)))
{
die('invalid host');
}
exec("ping -n 1 $value 2>&1", $output, $retval);
if ($retval != 0) { 
echo "Host is dead"; 
} 
else 
{
echo "Host is alive";
}
}

?>