<?php 
error_reporting(0);
	/*
	
		DDoS Protection - By CyberAttack.CC
	*/
	if(!empty($_COOKIE['ddos_key']))
	{		
			header('Location: index.php');
	}	
	if(!empty($_POST['continue']) && (!empty($_POST['g-recaptcha-response']))){

		if(empty($_COOKIE['ddos_key']))
			{	
				$length = 8;
				$randomString = substr(str_shuffle("0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, $length);
				// Sets a 30 Mintue Session Before Renew
				setcookie("ddos_key", MD5($randomString), time() + 3600);
				header('Location: index.php');	
			}
	
}	

?>
<html>

<head>
    <title>CYBERATTACK</title>
    <meta name=viewport content="width=device-width,initial-scale=1">
    <style class=cp-pen-styles>
        .grecaptcha-badge {
            visibility: collapse !important;
        }
        
        .spinner {
            width: 500px;
            height: 190px;
            background: rgba(0, 0, 0, 0) url(https://i.imgur.com/aOPvPFV.png) no-repeat scroll center center;
            margin: 10px auto;
            -webkit-animation: sk-beat 1.2s infinite ease-in-out;
            animation: sk-beat 1.5s infinite ease-in-out;
        }
        
        @-webkit-keyframes sk-beat {
            0% {
                transform: scale(1)
            }
            15% {
                transform: scale(1.15)
            }
            35% {
                transform: scale(1)
            }
            65% {
                transform: scale(1.15)
            }
        }
        
        @keyframes sk-beat {
            0% {
                transform: scale(1)
            }
            15% {
                transform: scale(1.15)
            }
            35% {
                transform: scale(1)
            }
            65% {
                transform: scale(1.15)
            }
        }
        
        body {
            background-color: #181824;
        }
        
        body {
            font-family: Helvetica, Arial, sans-serif;
            font-size: 120%;
        }
        
        h1 {
            font-size: 1.35em;
            color: #ffffff;
            text-align: center;
        }
        
        #wrapper {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -75%);
            -ms-transform: translate(-50%, -75%);
            -webkit-transform: translate(-50%, -75%);
        }
        
        p {
            font-size: 0.9em;
            color: #ffffff;
            text-align: center;
            margin: 10px 0 0 0;
        }
        
        a {
            text-decoration: none
        }
    </style>
</head>

<body>
      <td align="center" valign="middle">
          <div class="cf-browser-verification cf-im-under-attack">
  <noscript><h1 data-translate="turn_on_js" style="color:#bd2426;">Please turn JavaScript on and reload the page.</h1></noscript>
  <div id="cf-content" style="display: block;">
    <div id=wrapper>
        <div class=spinner></div>
	<br>
	<form method="POST">
				<div class="form-group">
					<div class="jumbotron">
					<div class="form-group">
                                                    <div class="col-xs-12">
                                                        <center> <div class="g-recaptcha" data-callback="enableBtn" data-sitekey=6LcCkPgUAAAAAPZCdNuvXrrPErpYr37GIf4El8BE></div> </center>
                                                    </div>
                                                </div>
		<center><input type="submit" class="button button4"  class="orange-flat-button"  name="continue" ></input><center> 
	<div class="loader"></div>
		
	
					</div> 
				</div>
			</form>
		<br>
        <h1>Powered by | <a href=https://cyberattack.cc><font color="cyan">1MP4C7 || The Cyber Criminal</font></h1>
		</a>
        <p id=p style=display:none;>The check is taking longer than usual, please hold on..</p>
            <h1>DDoS Protection By | <a href=https://nooder.net><font color="RebeccaPurple">Nooder</font></h1>
			</a>
    </div>
	  <form id="challenge-form" action="/cdn-cgi/l/chk_jschl" method="get">
    <input type="hidden" name="jschl_vc" value="e5f419997991637f387549ddb064342f">
    <input type="hidden" name="pass" value="1532148221.005-kRfKoSgx61">
    <input type="hidden" id="jschl-answer" name="jschl_answer">
  </form>
        <style>
            .button {
                background-color: #4CAF50; /* Green */
                border: none;
                color: white;
                padding: 16px 32px;
                text-align: center;
                text-decoration: none;
                display: inline-block;
                font-size: 16px;
                margin: 4px 2px;
                -webkit-transition-duration: 0.4s; /* Safari */
                transition-duration: 0.4s;
                cursor: pointer;
            }

            .button1 {
                background-color: white; 
                color: black; 
                border: 2px solid #4CAF50;
            }

            .button1:hover {
                background-color: #4CAF50;
                color: white;
            }

            .button2 {
                background-color: white; 
                color: black; 
                border: 2px solid #008CBA;
            }

            .button2:hover {
                background-color: #008CBA;
                color: white;
            }

            .button3 {
                background-color: white; 
                color: black; 
                border: 2px solid #f44336;
            }

            .button3:hover {
                background-color: #f44336;
                color: white;
            }

            .button4 {
                background-color: white;
                color: black;
                border: 2px solid #e7e7e7;
            }

            .button4:hover {background-color: #e7e7e7;}

            .button5 {
                background-color: #111111;
                /*//color: black;*/
                border: 2px solid #ff0000;
            }

            .button5:hover {
                background-color: #ff0000;
                color: white;
            }
        </style>
  <script src='https://www.google.com/recaptcha/api.js' type="1bbf4e6903ae8a2a4f92755f-text/javascript">
        </script>
        <script src='https://www.google.com/recaptcha/api.js' type="1bbf4e6903ae8a2a4f92755f-text/javascript">
        </script>
        <script type="1bbf4e6903ae8a2a4f92755f-text/javascript">
//            $("form").submit(function (e) {
//                e.preventDefault();
//                if (jQuery("#button5").attr('data-valid') === 'false') {
//                    Swal.fire({
//                        type: 'error',
//                        title: 'Oops...',
//                        text: 'Verify re-capcha'
//                    })
//                } else {
//
//                    $(this).unbind('submit').submit()
//                }
//
//            });
////            document.getElementById("button5").disabled = true;
//
            function enableBtn() {
                jQuery('#button5').show();
//                jQuery("#challenge-form").submit();
//                $("#button5").closest('form').on('submit', function (event) {
//                    this.submit(); //now submit the form
//                });

//                jQuery("#button5").attr('data-valid', 'true');
//                document.getElementById("button5").disabled = false;
            }
        </script>
		<script src="https://ajax.cloudflare.com/cdn-cgi/scripts/95c75768/cloudflare-static/rocket-loader.min.js" data-cf-settings="1bbf4e6903ae8a2a4f92755f-|49" defer=""></script>
</body>

</html>