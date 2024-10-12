<?php

// this file is a disaster

$allowedMethods = array("paypal", "stripe", "bitcoin");

error_reporting(E_ALL);
ini_set("display_errors", "on");

if(isset($_GET['id'], $_GET['method']) && is_string($_GET['method']) &&
	in_array($_GET['method'], $allowedMethods) && Is_Numeric($_GET['id']))
{
	ob_start();
	session_start();
	require_once("controls/database.php");
	function getPath()
	{
		$temp = "http://".$_SERVER['HTTP_HOST'];
		if($_SERVER['PHP_SELF'][strlen($_SERVER['PHP_SELF'])-1] == "/")
		{
			$temp.=$_SERVER['PHP_SELF'];
		} else {
			$temp.=dirname($_SERVER['PHP_SELF']);
		}
		if($temp[strlen($temp)-1]=="/")
		{
			$temp = substr($temp, 0, strlen($temp)-1);
		}
		return dirname($temp);
	}
	$id = (int)$_GET['id'];
	$paypalemail = $odb -> query("SELECT `email` FROM `gateway` LIMIT 1") -> fetchColumn(0);
	$plansql = $odb -> prepare("SELECT * FROM `plans` WHERE `ID` = :id");
	$plansql -> execute(array(":id" => $id));
	$row = $plansql -> fetch();
	if($row === NULL) { die("Bad ID"); }

	if ($_GET['method'] == "paypal") {
		$paypalurl = "https://www.paypal.com/cgi-bin/webscr?cmd=_xclick&amount=".urlencode($row['price'])."&business=".urlencode($paypalemail)."&item_name=".urlencode($row['name'])."&item_number=".urlencode($row['ID']."_".$_SESSION['ID'])."&return=http://gigastress.com/purchase.php"."&rm=2&notify_url=http://gigastress.com/gateway/paypalipn.php"."&cancel_return=http://gigastress.com/purchase.php"."&no_note=1&currency_code=USD";
		header("Location: ".$paypalurl);
		die();
	} else if ($_GET['method'] == "stripe") {
		if (isset($_POST['stripeToken'])) {
			try {
				include "controls/Stripe/Stripe.php";
				Stripe::setApiKey($stripe[$stripe['mode']]['secret']);
				$ui = $user->fetchUserInfo();
				
				Stripe_Charge::create(array(
					"amount" => ($row['price']*100),	// Convert into cents, ie $420.69 to 42069
					"currency" => "usd",
					"card" => $_POST['stripeToken'],
					"description" => $row['name'] . " ($" . $row['price'] . ")",
					"metadata" => array(
						"u_id" => $ui['ID'],
						"email" => $ui['email'],
						"item_id" => $row['ID'],
						"key" => $secret,
					)
				));
				echo "Payment should have gone through??";
			} catch (Exception $ex) {
				echo $ex->getMessage();
			}
		}
?>
<script type="text/javascript" src="https://js.stripe.com/v1/"></script>
<!-- jQuery is used only for this example; it isn't required to use Stripe -->
<script type="text/javascript">  // this identifies your website in the createToken call below

	if(typeof jQuery=='undefined') {
		var jqTag = document.createElement('script');
		jqTag.type = 'text/javascript';
		jqTag.src = 'https://ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js';
		document.appendChild(jqTag);
	}

	Stripe.setPublishableKey('<?php echo $stripe[$stripe['mode']]['public']; ?>');
	function stripeResponseHandler(status, response) {
		if (response.error) {
			// re-enable the submit button
			$('.submit-button').removeAttr("disabled");
			// show the errors on the form
			$(".payment-errors").css("display","block");
			$(".payment-errors").html(response.error.message);
		} else {
		
			$(".payment-errors").css("display","none");
			var form$ = $("#payment-form");
			// token contains id, last4, and card type
			var token = response['id'];
			// insert the token into the form so it gets submitted to the server
			form$.append("<input type='hidden' name='stripeToken' value='" + token + "' />");
			// and submit
			form$.get(0).submit();
		}
	}
	
	$(document).ready(function() {
		$("#payment-form").submit(function(event) {
			// disable the submit button to prevent repeated clicks
			$('.submit-button').attr("disabled", "disabled");
			// createToken returns immediately - the supplied callback submits the form if there are no errors
			Stripe.createToken({
				number: $('.card-number').val(),
				cvc: $('.card-cvc').val(),
				exp_month: $('.card-expiry-month').val(),
				exp_year: $('.card-expiry-year').val()
			}, stripeResponseHandler);
			return false; // submit from callback
		});
	});
</script> 
<div class="pay-container">
<div style="width:300px;margin:0 auto;">
<?php echo "<h3 style='font-size:16pt;font-weight:bold'>Purchase " . $row['name'] . " for $" . $row['price'] . " USD</h3>"; ?>
<div class="alert alert-error payment-errors" style='display:none;'></div>
 <form action="" method="POST" id="payment-form">
	<div class="form-row">
		<input type="text" size="20" autocomplete="off" class="card-number" placeholder="Credit Card Number" />
	</div>
	<div class="form-row">
		<input type="text" size="4" autocomplete="off" class="card-cvc" placeholder="CVC" />
	</div>
	<div class="form-row">
		<input type="text" size="2" class="card-expiry-month" placeholder="MM" />
		<span> / </span>
		<input type="text" size="4" class="card-expiry-year"placeholder="YYYY" />
	</div>
	<button type="submit" class="submit-button btn blue inverse">Submit Payment</button>
</form>

</div>
</div>

		<?php
	} else if ($_GET['method'] == "bitcoin") {
		include "includes/Coinbase.php";
		$cb = Coinbase::withApiKey($coinbase['api'], $coinbase['secret']);
		$button = $cb->createButton($row['name'], $row['price'], "USD", json_encode(array(
			"description"  => $row['description'],
			"cancel_url"   => "http://example.org/cancel",
			"success_url"  => "http://example.org/success",
			"callback_url" => "http://gigastress/gateway/bitcoin_ipn.php",
			"key"          => $secret,
			
			"u_id"			=> $_SESSION['ID'],
			"item_id"		=> $row['ID'],
		)));
		
		echo "<div class='pay-container'><div style='width:200px;margin:0 auto;'><h3 style='font-size:16pt;font-weight:bold'>Purchase " . $row['name'] . " for $" . $row['price'] . " USD</h3>";
		
		if ($button->success == 1) {
			echo $button->embedHtml;
		} else {
			echo "Failed to create button payment";
		}
		
		echo "</div></div>";
	
	} else {
		echo "haxor";
	}
}

?>

<style>
.pay-container {
	font-family: Arial;
	color: #000;
}
</style>

