<?php

header("X-XSS-Protection: 1; mode=block");

    $page = "Hub L4";
	include 'header.php';

	if (!($user->hasMembership($odb)) && $testboots == 0) {
		header('location: plan.php');
		exit;
	}
?>

<?php

	/// Require the header that already contains the sidebar and top of the website and head body tags
	require_once 'header.php'; 
	
	/// Querys for the stats below
	$TodayAttacks = $odb->query("SELECT COUNT(id) FROM `logs` WHERE `date` BETWEEN DATE_SUB(CURDATE(), INTERVAL '-1' DAY) AND UNIX_TIMESTAMP()")->fetchColumn(0);
	$MonthAttack = $odb->query("SELECT COUNT(id) FROM `logs` WHERE `date` BETWEEN DATE_SUB(CURDATE(), INTERVAL '-30' DAY) AND UNIX_TIMESTAMP()")->fetchColumn(0);
	$TotalAttacks = $odb->query("SELECT COUNT(*) FROM `logs`")->fetchColumn(0);
	$RunningAttacks = $odb->query("SELECT COUNT(*) FROM `logs` WHERE `time` + `date` > UNIX_TIMESTAMP() AND `stopped` = 0")->fetchColumn(0);
	
	$testattacks = $odb->query("SELECT COUNT(*) FROM `logs` WHERE `time` + `date` > UNIX_TIMESTAMP() AND `stopped` = 0")->fetchColumn(0);
	$load    = round($testattacks / $maxattacks * 100, 2);
?>

<?php
	$plansql = $odb -> prepare("SELECT `users`.`expire`, `plans`.`name`, `plans`.`concurrents`, `plans`.`mbt` FROM `users`, `plans` WHERE `plans`.`ID` = `users`.`membership` AND `users`.`ID` = :id");
	$plansql -> execute(array(":id" => $_SESSION['ID']));
	$row = $plansql -> fetch(); 
	$date = date("d/m/Y", $row['expire']);
	if (!$user->hasMembership($odb)){
		$row['mbt'] = 'No membership';
		$row['concurrents'] = 'No membership';
		$row['name'] = 'No membership';
		$date = 'No membership';
	}
?>


<html>
		<div class="page-wrapper" style="display: block;">
			<div class="page-breadcrumb">
			<script src="assets/js/modal.js"></script>
				<div class="d-flex align-items-center">
					<h4 class="page-title text-truncate text-white font-weight-medium mb-0">Hub L4</h4>
					<div class="ml-auto">
						<nav aria-label="breadcrumb">
							<ol class="breadcrumb m-0 p-0">
								<li class="breadcrumb-item text-sql" aria-current="page"><?php echo $sitename; ?></li>
								<li class="breadcrumb-item text-muted" aria-current="page">Hub L4</li>
							</ol>
						</nav>
					</div>
				</div>
			</div>
      </ul>
      <div class="container-fluid">
                <div class="row">
                    <div class="col-md-4 col-lg-4">
						   <div id="div"></div>
                        <div class="card">
                            <div class="card-body">
							<script>
											function negri1(val)
											{
												updateTime();
												document.getElementById('current_attacktime').textContent=val;
											}
											
										    function updateTime()
											{
												var time=$('#time').val();
												const finish = (time)
												document.getElementById('current_time').textContent=finish;
											}
											</script>
													<script>
													function fuckingl7(){
														const method=$('#method').val();
														const startt = encodeURIComponent($('#method option[value="'+method+'"]').parent().data("tag"));
														if(startt == "startl7"){
															document.getElementById("emulationdiv").style.display = "block";
															document.getElementById("l4").style.display = "none";
															document.getElementById("l7").style.display = "block";
															document.getElementById("postdiv").style.display = "block";
															document.getElementById("refererdiv").style.display = "block";
														}else{
															document.getElementById("emulationdiv").style.display = "none";
															document.getElementById("l4").style.display = "block";
															document.getElementById("l7").style.display = "none";
															document.getElementById("postdiv").style.display = "none";
															document.getElementById("refererdiv").style.display = "none";
														}
													}
												</script>
			<div id="l4" name="l4" style="display: block;">
		    <div class="form-group">
                <label for="host" class="col-md-14 text-white">Target</label>
                <div class="col-md-14">
                  <input class="form-control" type="text" id="target" name="target" placeholder="1.1.1.1" data-toggle="tooltip" data-placement="bottom" title="" data-original-title="IPv4 address of the target">
                </div>
              </div>
			  <div class="form-group">
			  	<label for="port" class="col-md-14 text-white">Port</label>
                <div class="col-md-14">
                  <input class="form-control" type="text" id="port" name="port" placeholder="80" data-toggle="tooltip" data-placement="bottom" title="" data-original-title="IPv4 address of the target">
                </div>
				</div>
				</div>
              <div class="form-group">
                <label class="col-md-14 text-white" for="current_time"><span class="text-primary"></i></span> Time: <span id="current_time">60</span></label>
                <div class="col-md-14">
                  <input type="range" class="custom-range" id="time" name="time" min="60" max="<?php echo $row['mbt']; ?>" onchange="negri1(this.value);" oninput="negri1(this.value);" value="60" step="60">
                </div>
              </div>
              <div class="form-group">
                <label for="method" class="col-md-14 text-white">Flood type</label>
                <div class="col-md-14">
                  <select class="form-control" id="method" name="method" onchange="fuckingl7();">
                                                        <optgroup data-tag="startl4" label="Layer 4">
                                                        <?php
                                                        $stmt = $odb->query("SELECT * FROM methods WHERE type='Layer4'");
                                                        
                                                        foreach($stmt->fetchAll() as $row)
                                                        {
                                                            echo '<option value='.$row["name"].'>'.$row["name"].'</option>';
                                                        }
                                                        
                                                        ?>													
														</optgroup>
													</select>
												</div>
											</div>
                <div class="col-lg-12 text-center">
                  <button class="btn btn-purple" onclick="start(this)" type="submit">
													<i class=""></i> Send Attack
												</button>
												<?php 
												// Check if user has an API with us.
												$userID = $_SESSION['ID'];
												
													$SQL = $odb -> prepare("SELECT COUNT(userID) FROM `users_api` WHERE `userID` = :userID");
													$SQL -> execute(array(':userID' => $userID));
													$status = $SQL -> fetchColumn(0);
													if ($status == 1){
													
														echo '
												<button class="btn btn-outline btn-warning" data-toggle="modal" data-target="#manageapi" type="button"><i class="fa fa-wrench"></i> Manage API</button>';
													
													}
												?>
                </div>
              </div>
            </form>
          </div>
        </div>
					<div class="col-md-6 col-lg-8">
						<div class="card">
							<div class="card-body">
								<div class="mt-2 activity">
		     <i style="display: none;" id="manage"></i>
			<div id="attacksdiv" style="display:inline-block;width:100%"></div>
          </div>
        </div>
      </div>
   	 <script>
		function negri1(val)
		{
			updateTime();
		document.getElementById('current_attacktime').textContent=val;
		}
											
		function updateTime()
		{
		var time=$('#time').val();
		const finish = (time)
		document.getElementById('current_time').textContent=finish;
		}
      </script>
				<script>
					attacks();
			
					function attacks() {
						document.getElementById("attacksdiv").style.display = "none";
						document.getElementById("manage").style.display = "inline"; 
						var xmlhttp;
						if (window.XMLHttpRequest) {
							xmlhttp = new XMLHttpRequest();
						}
						else {
							xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
						}
						xmlhttp.onreadystatechange=function() {
							if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
								document.getElementById("attacksdiv").innerHTML = xmlhttp.responseText;
								document.getElementById("manage").style.display = "none";
								document.getElementById("attacksdiv").style.display = "inline-block";
								document.getElementById("attacksdiv").style.width = "100%";
								if (document.getElementById("ajax") != null)
									eval(document.getElementById("ajax").innerHTML);

								var elements = document.getElementsByName("ststst");
								if (document.getElementById("stopallbtn") != null) {
									if(elements.length > 1){
										document.getElementById("stopallbtn").style.display = "none";
									}else{
										document.getElementById("stopallbtn").style.display = "block";
									}
								}
							}
						}
						xmlhttp.open("GET","vvv/files/attacks.php",true);
						xmlhttp.send();
					}
		
		            function start(btn) {
		                btn.disabled = true;
		                
		                
			            const host=encodeURIComponent($('#host').val());
						const port=encodeURIComponent($('#port').val());
						const target=encodeURIComponent($('#target').val());
			            const time=$('#time').val();
			            const postdata=encodeURIComponent($('#postdata').val()) || "/";
			            const cookie=encodeURIComponent($('#cookie').val()) || "https://google.com/";
			            const referer=encodeURIComponent($('#referer').val()) || "false";
			            const mode=encodeURIComponent($('#mode').val());
						const emulation=encodeURIComponent($('#emulation').val());
			            const method=$('#method').val();
						const startt = encodeURIComponent($('#method option[value="'+method+'"]').parent().data("tag"));
			            const rmethod=$('#rmethod').val() || "GET";
			            document.getElementById("div").style.display="none";			
			
			               var xmlhttp;
			               if (window.XMLHttpRequest) {
			        	   xmlhttp = new XMLHttpRequest();
			               }
			                else {
				           xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
			                     }
			               xmlhttp.onreadystatechange=function() {
				           if (xmlhttp.readyState==4 && xmlhttp.status==200) {
				           document.getElementById("div").innerHTML=xmlhttp.responseText;
					       document.getElementById("div").style.display="inline";
					       btn.disabled = false;
					       if (xmlhttp.responseText.search("success") != -1) {
    						     attacks();
    						     window.setInterval(ping(host),10000);
					          }
				           }
			           }
			           xmlhttp.open("GET",`vvv/files/hub_api.php?type=startl4&host=${startt == "startl7" ? host : target}&port=${port}&time=${time}&method=${method}&rmethod=${rmethod}&postdata=${postdata}&cookie=${cookie}&referer=${referer}&mode=${mode}&emulation=${emulation}`,true);
			           xmlhttp.send();
						
		            }
		
					function stop(id) {
						document.getElementById("manage").style.display="inline"; 
						document.getElementById("div").style.display="none"; 
						var xmlhttp;
						if (window.XMLHttpRequest) {
							xmlhttp=new XMLHttpRequest();
						}
						else {
							xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
						}
						xmlhttp.onreadystatechange=function() {
							if (xmlhttp.readyState==4 && xmlhttp.status==200) {
								document.getElementById("div").innerHTML=xmlhttp.responseText;
								document.getElementById("div").style.display="inline";
								document.getElementById("manage").style.display="none";
								if (xmlhttp.responseText.search("success") != -1) {
									attacks();
								}
							}
						}
						xmlhttp.open("GET",`vvv/files/hub_api.php?type=stop&id=${id}`,true);
						xmlhttp.send();
					}

					function stopall(id) {
						document.getElementById("manage").style.display="inline"; 
						document.getElementById("div").style.display="none"; 
						var xmlhttp;
						if (window.XMLHttpRequest) {
							xmlhttp=new XMLHttpRequest();
						}
						else {
							xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
						}
						xmlhttp.onreadystatechange=function() {
							if (xmlhttp.readyState==4 && xmlhttp.status==200) {
								document.getElementById("div").innerHTML=xmlhttp.responseText;
								document.getElementById("div").style.display="inline";
								document.getElementById("manage").style.display="none";
								if (xmlhttp.responseText.search("success") != -1) {
									attacks();
								}
							}
						}
						xmlhttp.open("GET",`vvv/files/hub_api.php?type=stopall&id=${id}`,true);
						xmlhttp.send();
					}
				</script>	