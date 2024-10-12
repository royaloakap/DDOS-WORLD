<?php 

	
$param = array('ipn_version' => '1.0',
			'ipn_id' => 'c179bf33768f3dd76f01bcbb40d93a60',
			'ipn_mode' => 'httpauth',
			'merchant' => '45f488eddd350c713b1b81189f13cd58',
			'ipn_type' => 'button',
			'txn_id' => 'PP-b094991603f2f221327cddac419c537792a97d0ad4207ffd',
			'status' => '100',
			'status_text' => 'Complete',
			'currency1' => 'USD',
			'currency2' => 'USD',
			'amount1' => '0.01',
			'amount2' => '0.01',
			'subtotal' => '0.01',
			'shipping' => '0',
			'tax' => '0',
			'fee' => '0.01',
			'net' => '0',
			'send_tx' => '6EC696920M3338216',
			'item_amount' => '0.01',
			'item_name' => 'Game: 19013',
			'quantity' => '1',
			'first_name' => 'fds',
			'last_name' => 'fsdf',
			'email' => 'sluporcom@gmail.com',
			'custom' => '8_8',
			'received_amount' => '0.01',
			'received_confirms' => '1'
		);

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL,"http://stresser.club/panel/gateway/plan_ipn.php");
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($param));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$result = curl_exec($ch);
curl_close ($ch);
echo $result;

?>