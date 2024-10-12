<?
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
	
$pingip = $_POST['pingip'];

if($pingip == null){	
			echo '
			<div class="alert alert-info">
                                <strong>Enter IP Address!</strong>
                 
                            </div>
			';
			} else {
			$shh = get_data('http://apionly.com/pingipv4.php?ip='.$pingip.'');
			echo '<div class="well">
				'.$shh.'
				</div>';
			}
		
	
			
?>