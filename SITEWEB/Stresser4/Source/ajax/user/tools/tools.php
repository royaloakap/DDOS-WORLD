<?php

	if (!isset($_SERVER['HTTP_REFERER'])){
		die;
	}

	ob_start();
	require_once '../../../inc/config.php';
	require_once '../../../inc/init.php';
	
	if (!empty($maintaince)){
		die;
	}

	if (!($user -> LoggedIn()) || !($user -> notBanned($odb))){
		die();
	}


	//Check Variables and set
	if (!(isset($_GET['type'])) || empty($_GET['resolve'])) {
		die('Please fill in the field');
	}
	
	$type = $_GET['type'];
	$value = $_GET['resolve'];
	
	//Check Ports
	if($type == "ports")
	{

		$ports = array(21, 22, 23, 25, 53, 80, 110, 1433, 3306, 3785, 9987, 25565, 27015);
	
		$results = array();
		foreach($ports as $port) {
			if($pf = @fsockopen($value, $port, $err, $err_string, 1)) {
				$results[$port] = true;
				fclose($pf);
			} else {
				$results[$port] = false;
			}
		}
	
		echo '
		<div class="table-responsive">
			<table class="table table-striped table-vcenter">
			<thead>
			<tr>
				<th>Type</th>
				<th>Port</th>
				<th class="text-center" style="width: 120px;"><i class="si si-info"></i></th>
			</tr>
			</thead>
			<tbody>

		';
 
		foreach($results as $port=>$val)	{
			$prot = getservbyport($port,"tcp");
			if($port == 53){ $prot = 'dns'; }
			if($port == 3306){ $prot = 'mysql'; }
			if($port == 1433){ $prot = 'mssql'; }
			if($port == 9987){ $prot = 'ts3'; }
			if($port == 25565){ $prot = 'minecraft'; }
			if($port == 3785){ $prot = 'ventrilo'; }
			if($port == 27015){ $prot = 'source engine port'; }
					echo '
					<tr>
						<td>'.$prot.'</td>
						<td>'.$port.'</td>
					';
			if($val) {
				echo '<td>
					<span class="label label-success">Open</span>
				</td></tr>';
			}
			else {
				echo '<td>
					<span class="label label-danger">Closed</span>
				</td></tr>';
			}
		}
	
		echo '
			</tbody>
				</table>
		</div>
			';
	}
	

	//Skype resolver
	if ($type == 'skype'){
		$SQL = $odb -> prepare("SELECT COUNT(*) FROM `blacklist` WHERE `data` = :skype AND `type` = 'skype'");
		$SQL -> execute(array(':skype' => $value));
		$SQL = $SQL -> fetchColumn(0);
		if ($SQL > 0){
			die('Skype is blacklisted');
		} else {
			echo "There is the result: " .file_get_contents("http://api.predator.wtf/resolver/?arguments=".$value."");
		}
	}
	
	if ($type == 'skypedb'){
		$SQL = $odb -> prepare("SELECT COUNT(*) FROM `blacklist` WHERE `data` = :skype AND `type` = 'skype'");
		$SQL -> execute(array(':skype' => $value));
		$SQL = $SQL -> fetchColumn(0);
		if ($SQL > 0){
			die('Skype is blacklisted');
		} else {
			echo "There is the result: " .file_get_contents("http://api.predator.wtf/lookup/?arguments=".$value."");
		}
	}
	
	if ($type == 'ip2skype'){
		$SQL = $odb -> prepare("SELECT COUNT(*) FROM `blacklist` WHERE `data` = :skype AND `type` = 'skype'");
		$SQL -> execute(array(':skype' => $value));
		$SQL = $SQL -> fetchColumn(0);
		if ($SQL > 0){
			die('Skype is blacklisted');
		} else {
			echo "There is the result: " .file_get_contents("http://api.predator.wtf/lookup/?arguments=".$value."");
		}
	}
	if ($type == 'email2skype'){
		$SQL = $odb -> prepare("SELECT COUNT(*) FROM `blacklist` WHERE `data` = :skype AND `type` = 'skype'");
		$SQL -> execute(array(':skype' => $value));
		$SQL = $SQL -> fetchColumn(0);
		if ($SQL > 0){
			die('Skype is blacklisted');
		} else {
			echo "There is the result: " .file_get_contents("http://api.predator.wtf/e2skype/?arguments=".$value."");
		}
	}
	

if ($type == 'skype2email'){
		$SQL = $odb -> prepare("SELECT COUNT(*) FROM `blacklist` WHERE `data` = :skype AND `type` = 'skype'");
		$SQL -> execute(array(':skype' => $value));
		$SQL = $SQL -> fetchColumn(0);
		if ($SQL > 0){
			die('Skype is blacklisted');
		} else {
			die('This tool is deleted');
		}
	}		
	//Cloudflare resolver
	if ($type == 'cloudflaree'){

		function get_host($ip){
			$ptr= implode(".",array_reverse(explode(".",$ip))).".in-addr.arpa";
			$host = dns_get_record($ptr,DNS_PTR);
			if ($host == null) return $ip;
			else return $host[0]['target'];
		} 
		function isCloudflare($ip){
			$host = get_host($ip);
			if($host=="cf-".implode("-", explode(".", $ip)).".cloudflare.com"){
				return true;
			}
			else {
				return false;
			}
		}
		$lookupArr = array("mail.", "direct.", "direct-connect.", "direct-connect-mail.", "cpanel.", "ftp.");
		$output = array();
		foreach ($lookupArr as $lookupKey){
			$lookupHost = $lookupKey . $value;
			$foundHost = gethostbyname($lookupHost);
			if ($foundHost == $lookupHost){
				$output[] = "No DNS Found";
			}
			else{
				$extra = "<font color=\"green\">(Not Cloudflare)</font>";
				if(isCloudflare($foundHost)){
					$extra = "<font color=\"red\">(Cloudflare)</font>";
				}
				$output[] = $foundHost." ".$extra;
			}
		}
		echo '<li> Mail - '.$output[0].' </li><li> Direct - '.$output[1].' </li><li> Direct-Connect - '.$output[2].'</li><li>Direct-Connect-Mail - '.$output[3].'</li><li>Cpanel - '.$output[4].'</li><li>FTP - '.$output[5];
	}
	
	if($type == 'cloudflare')
	{
		$cff = file_get_contents('http://apionly.com/cfresolver.php?domain='.$value.'');
		echo $cff;
	}
	
	if($type == 'ipv4')
	{
			$ping = file_get_contents('http://APIOnly.com/pingipv4.php?ip='.$value.'');
			echo $ping;
	}
	
	if($type == 'ping_domain')
	{
		$cff = file_get_contents('http://APIOnly.com/pingdomain.php?domain='.$value.'');
		echo $cff;
	}

	//Domain
	if ($type == 'domain'){
		if (filter_var($value, FILTER_VALIDATE_IP) === TRUE) { die('Domain is invalid'); }
		$fuckdasystem = gethostbyname($value);
		if (filter_var($fuckdasystem, FILTER_VALIDATE_IP) == TRUE) { die('The website ip is: '.$fuckdasystem); } else { echo 'Can\'t get the IP'; };
	}
	
	//Ping
	if ($type == 'ping'){
		if (!filter_var($value, FILTER_VALIDATE_IP) && (!filter_var(gethostbyname($value), FILTER_VALIDATE_IP))){
			die('invalid host');
		}
		exec("ping -n 1 $value 2>&1", $output, $retval);
		if ($retval != 0) { 
			echo "Host is dead"; 
		} 
		else {
			echo "Host is alive";
		}
	}

	//Geo
	if ($type == 'geo'){
		if (filter_var($value, FILTER_VALIDATE_IP) === FALSE) { die('IP address is invalid'); }
			$xml = simplexml_load_file('http://api.ipinfodb.com/v3/ip-city/?key=d8dc071351f3b1882b26d5b6820df28eaf2528a2746d78ea4fcbfbe5fe52089d&format=xml&ip='.$value);
			$status = $xml->statusCode;
			$country = $xml->countryName;
			$region = $xml->regionName;
			$city = $xml->cityName;
			$latitude = $xml->latitude;
			$longitude = $xml->longitude;
			$timezone = $xml->timeZone;
?>
<table class="table table-striped">
	<tbody>
        <tr>
			<td><strong>IP Address:</strong></td>
			<td><?php echo $value;?></td> 			
		</tr>
        <tr>
			<td><strong>Status:</strong></td>
			<td><?php echo $status;?></td>
        </tr>
        <tr>
            <td><strong>Country:</strong></td>
			<td><?php echo $country;?></td>
        </tr>
        <tr>
            <td><strong>Region:</strong></td>
			<td><?php echo $region;?></td>
        </tr>
		<tr>
            <td><strong>City:</strong></td>
			<td><?php echo $city;?></td>
        </tr>
		<tr>
            <td><strong>Latitude:</strong></td>
			<td><?php echo $latitude;?></td>
        </tr>
		<tr>
			<td><strong>Longitude:</strong></td>
			<td><?php echo $longitude;?></td>
        </tr>
		<tr>
            <td><strong>Timezone:</strong></td>
			<td><?php echo $timezone;?></td>
        </tr>
	</tbody>
</table>
<?php
	}
?>