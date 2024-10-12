<?php

	if (!isset($_SERVER['HTTP_REFERER'])){
		die;
	}
	
	ob_start();
	require_once '../../../app/config.php';
	require_once '../../../app/init.php'; 

	if (!empty($maintaince)){
		die;
	}
	
	if (!($user -> LoggedIn()) || !($user -> notBanned($odb))){
		die();
	}

	if (!($user->hasMembership($odb))){
		die('You do not have a membership. Please purchase a package in order to use this feature');
	}

	//Check Variables and set
	if (!(isset($_GET['type'])) || empty($_GET['resolve'])) {
		die('Please fill in the field');
	}
	
	$type = $_GET['type'];
	$value = $_GET['resolve'];

	//Skype resolver
	if ($type == 'skype'){
		$SQL = $odb -> prepare("SELECT COUNT(*) FROM `blacklist` WHERE `data` = :skype AND `type` = 'skype'");
		$SQL -> execute(array(':skype' => $value));
		$SQL = $SQL -> fetchColumn(0);
		if ($SQL > 0){
			die('Skype is blacklisted');
		}
		echo @file_get_contents($skype.$value);
	}

	//Domain resolver
	if ($type == 'domain'){
		echo success(gethostbyname($value));
	}

	//Cloudflare resolver
	if ($type == 'cloudflare'){
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
		
		?>
		echo success('<li> Mail - '.$output[0].' </li><li> Direct - '.$output[1].' </li><li> Direct-Connect - '.$output[2].'</li><li>Direct-Connect-Mail - '.$output[3].'</li><li>Cpanel - '.$output[4].'</li><li>FTP - '.$output[5]);
	}
	?>
 <div class="col-md-12 col-sm-12 col-xs-12">
          <div class="white-box">
<table class="table table-striped">
	<tbody>
        <tr>
			<td><strong>mail.<?php echo $value;?></strong></td>
			<td><?php echo $output[0];?></td> 			
		</tr>
        <tr>
			<td><strong>direct.<?php echo $value;?></strong></td>
			<td><?php echo $output[1];?></td> 	
        </tr>
        <tr>
           <td><strong>direct-connect.<?php echo $value;?></strong></td>
			<td><?php echo $output[2];?></td> 	
        </tr>
        <tr>
            <td><strong>direct-connect-mail.<?php echo $value;?></strong></td>
			<td><?php echo $output[3];?></td> 	
        </tr>
		<tr>
            <td><strong>cpanel.<?php echo $value;?></strong></td>
			<td><?php echo $output[4];?></td>
        </tr>
	</tbody>
</table>
</div>
</div>
<?php
	}
	
	//Up or down

	
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
		if (filter_var($value, FILTER_VALIDATE_IP) === FALSE) { echo error('IP address is invalid'); die(); }
			$xml = simplexml_load_file('http://api.ipinfodb.com/v3/ip-city/?key=d8dc071351f3b1882b26d5b6820df28eaf2528a2746d78ea4fcbfbe5fe52089d&format=xml&ip='.$value);
			$status = $xml->statusCode;
			$country = $xml->countryName;
			$region = $xml->regionName;
			$city = $xml->cityName;
			$latitude = $xml->latitude;
			$longitude = $xml->longitude;
			$timezone = $xml->timeZone;
?>
 <div class="col-md-12 col-sm-12 col-xs-12">
          <div class="white-box">
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
</div>
</div>
<?php
	}
?>