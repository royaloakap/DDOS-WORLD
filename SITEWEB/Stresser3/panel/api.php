<?php

header("X-XSS-Protection: 1; mode=block");

    $page = "API System";
	/// Require the header that already contains the sidebar and top of the website and head body tags
	require_once 'header.php';
	
	if(!$user->hasAPI($odb))
	{
	    Header("Location: plan.php");
	    die();
	}
	
	
	if(isset($_POST['gen_key'])){
		if(isset($_SESSION['username'])){
			genKey($_SESSION['username'], $odb);
			header('Location: api.php');
		}
	}
	if(isset($_POST['disable_key'])){
		if(isset($_SESSION['username'])){
			disableKey($_SESSION['username'], $odb);
			header('Location: api.php');
		}
	}

	function genKey($username, $odb){
		$newkey = generateRandomString(16);
		$stmt2 = $odb->query("UPDATE users SET apikey='$newkey' WHERE username='$username'");
	}
	function disableKey($username, $odb){
		$stmt2 = $odb->query("UPDATE users SET apikey='0' WHERE username='$username'");
	}
	function generateRandomString($length = 10){
		$characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';
		$charactersLength = strlen($characters);
		$randomString = '';
		for($i=0;$i<$length;$i++){
			$randomString .= $characters[rand(0, $charactersLength - 1)];
		}
		return $randomString;
	}
	
	$stmt = $odb->prepare("SELECT apikey FROM users WHERE username=:login");
	$stmt->bindParam("login", $_SESSION['username'], PDO::PARAM_STR);
	$stmt->execute();
	$key = $stmt->fetchColumn(0);
	
?>

<html>
		<div class="page-wrapper" style="display: block;">
			<div class="page-breadcrumb">
				<div class="d-flex align-items-center">
					<h4 class="page-title text-truncate text-white font-weight-medium mb-0"><?php echo $page; ?></h4>
					<div class="ml-auto">
						<nav aria-label="breadcrumb">
							<ol class="breadcrumb m-0 p-0">
								<li class="breadcrumb-item text-primary active" aria-current="page"><?= $sitename; ?> </li>
								<li class="breadcrumb-item text-muted" aria-current="page">API</li>
							</ol>
						</nav>
					</div>
				</div>
			</div>
      </ul>
<div class="container-fluid">
 <div class="row">
            <div class="col-md-9 col-lg-9">
                <div class="card">
                  <div class="card-body">
                      <h4 class="card-title">API <i style="display: none;" id="icon"></i></h4>
            		  	<form method="POST">
            		  		<?php if($key == '0'){?>
            	            <input class="form-control text-white" type="text" value="API is unavailable or api-key is disabled! Click 'Generate new api-key'." readonly="" style="color:black;">
            	            <?php }else{?>
            				<input class="form-control text-white" type="text" value="https://vuestress.ru/api/?key=<?php echo $key;?>&host=[host]&port=[port]&time=[time]&method=[method]&vip=0" readonly="" style="color:black;">
            				<?php }?>
            	            <br><button type="submit" class="btn btn-primary" name="gen_key">Generate new api-key</button> <button type="submit" class="btn btn-danger" name="disable_key">Disable api-key</button>
            	        </form>
                    </div>
                  </div>
                </div>
            <div class="col-md-4 col-lg-4">
                 <div class="card">
                        <div class="card-header">
                            <p><strong>Layer4</strong></p>
                        </div>
                        <div class="card-body mx-auto">
                                    <?php 
                                    
                                    $SQL = $odb -> prepare("SELECT * FROM `methods` WHERE `type` = 'layer4' ORDER BY `id` ASC");
                                    if($SQL->execute())
                                    {
                                        foreach($SQL->fetchAll() as $row)
                                        {
                                            if(!empty($row["nickname"]))
                                            {
                                               echo '<button class="btn btn-primary ml-2 mt-2">' . $row["nickname"] . "</button>\n"; 
                                            }
                                            else
                                            {
                                                echo '<button class="btn btn-primary ml-2 mt-2">' .  $row["name"] . "</button>\n";
                                            }
                                        }
                                    }
                                    
                                    ?>
                                </div>
                        </div>
              </div>
            <div class="col-md-4 col-lg-4">
                 <div class="card">
                        <div class="card-header">
                            <p><strong>Layer7</strong></p>
                        </div>
                        <div class="card-body mx-auto">
                                    <?php 
                                    
                                    $SQL = $odb -> prepare("SELECT * FROM `methods` WHERE `type` = 'layer7' ORDER BY `id` ASC");
                                    if($SQL->execute())
                                    {
                                        foreach($SQL->fetchAll() as $row)
                                        {
                                            if(!empty($row["nickname"]))
                                            {
                                               echo '<button class="btn btn-primary ml-2 mt-2">' . $row["nickname"] . "</button>"; 
                                            }
                                            else
                                            {
                                                echo '<button class="btn btn-primary ml-2 mt-2">' .  $row["name"] . "</button>";
                                            }
                                        }
                                    }
                                    
                                    ?>
                                </div>
                        </div>
              </div>
        </div>
    </div>
  </div>
			<script>
			function redeemCode() {
				var code = $('#code').val();
				document.getElementById("icon").style.display="inline"; 
				var xmlhttp;
				if (window.XMLHttpRequest) {
					xmlhttp=new XMLHttpRequest();
				}
				else {
					xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
				}
				xmlhttp.onreadystatechange=function() {
					if (xmlhttp.readyState==4 && xmlhttp.status==200) {
						document.getElementById("icon").style.display="none";
						document.getElementById("div").innerHTML=xmlhttp.responseText;
						if (xmlhttp.responseText.search("SUCCESS") != -1) {
							inbox();
						}
					}
				}
				xmlhttp.open("GET","vvv/files/user/giftcodes/redeem.php?user=<?php echo $_SESSION['ID']; ?>" + "&code=" + code,true);
				xmlhttp.send();
			}
			</script>

</div>
</html>