<?php

	/// Require the header that already contains the sidebar and top of the website and head body tags
	$page = "Профиль";
	require_once 'header.php'; 
	require 'includes/vendor/autoload.php';
	$authenticator = new PHPGangsta_GoogleAuthenticator();
	
	$plansql = $odb -> prepare("SELECT `users`.`expire`, `plans`.`name`, `plans`.`concurrents`, `plans`.`mbt` FROM `users`, `plans` WHERE `plans`.`ID` = `users`.`membership` AND `users`.`ID` = :id");
	$plansql -> execute(array(":id" => $_SESSION['ID']));
	$row = $plansql -> fetch(); 
	$date = date("m-d-Y", $row['expire']);
	if (!$user->hasMembership($odb)){
		$row['mbt'] = 0;
		$row['concurrents'] = 0;
		$row['name'] = 'No membership';
		$date = 'No membership';
	}
	
	if(!empty($_POST['update'])){
		
		if(empty($_POST['old']) || empty($_POST['new'])){
			$error = 'Вам нужно ввести оба пароля';
		}

		$SQLCheckCurrent = $odb -> prepare("SELECT COUNT(*) FROM `users` WHERE `ID` = :ID AND `password` = :password");
		$SQLCheckCurrent -> execute(array(':ID' => $_SESSION['ID'], ':password' => SHA1(md5($_POST['old']))));
		$countCurrent = $SQLCheckCurrent -> fetchColumn(0);
	
		if ($countCurrent == 0){
			$error = 'Текущий пароль неверный';
		}
		
		$notify = error($error);
	
		if(empty($error)){
			$SQLUpdate = $odb -> prepare("UPDATE `users` SET `password` = :password WHERE `username` = :username AND `ID` = :id");
			$SQLUpdate -> execute(array(':password' => SHA1(md5($_POST['new'])),':username' => $_SESSION['username'], ':id' => $_SESSION['ID']));
			$notify = success('Пароль успешно изменен');
		}
	
	}
	
	if(isset($_POST['enable2']))
	{
		
			
			$id = $_SESSION['ID'];
			
			$SQLinsert = $odb -> prepare("UPDATE `users` SET `2auth` = '1' WHERE `ID` = :id");
			$SQLinsert -> execute(array( ':id' => $id));
			$notify = success('2х этапная авторизация включена, пожалуйста, отсканируйте QR-код в своем приложении!');
		
		
	}
?>


  <!-- Page Content -->
  <div id="page-wrapper">
    <div class="container-fluid">
      <div class="row bg-title">
        <div class="col-lg-3 col-md-4 col-sm-4 col-xs-12">
          <h4 class="page-title"><?php echo $page; ?></h4>
        </div>
        <div class="col-lg-9 col-sm-8 col-md-8 col-xs-12">
          <ol class="breadcrumb">
            <li><a href="#"><?php echo $sitename; ?></a></li>
            <li class="active"><?php echo $page; ?></li>
          </ol>
        </div>
        <!-- /.col-lg-12 -->
      </div>
	  <?php
		if(isset($notify)){
			echo '<div class="row col-md-12">' . $notify . "</div>";
		}
		?>
	    <div class="row">
        <div class="col-md-4 col-xs-12">
          <div class="white-box">
            <div class="user-bg"> <img width="100%" alt="user" src="includes/theme/plugins/images/large/img1.jpg">
              <div class="overlay-box">
                <div class="user-content"> <a href="javascript:void(0)"><img src="includes/theme/plugins/images/users/avatar.png" class="thumb-lg img-circle" alt="img"></a>
                  <h4 class="text-white"><?php echo $_SESSION['username']; ?></h4>
                  <h5 class="text-white">user@maostress</h5>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="col-md-8 col-xs-12">
          <div class="white-box">
            <ul class="nav nav-tabs tabs customtab">
              <li class="active tab"><a href="#logins" data-toggle="tab"> <span class="visible-xs"><i class="fa fa-user"></i></span> <span class="hidden-xs">Входы</span> </a> </li>
              <li class="tab"><a href="#attacks" data-toggle="tab" aria-expanded="true"> <span class="visible-xs"><i class="fa fa-envelope-o"></i></span> <span class="hidden-xs">Атаки</span> </a> </li>
              <li class="tab"><a href="#settings" data-toggle="tab" aria-expanded="false"> <span class="visible-xs"><i class="fa fa-cog"></i></span> <span class="hidden-xs">Изменить пароль</span> </a> </li>
			  <li class="tab"><a href="#2auth" data-toggle="tab" aria-expanded="false"> <span class="visible-xs"><i class="fa fa-cog"></i></span> <span class="hidden-xs">2Auth</span> </a> </li>
			</ul>
            <div class="tab-content">
              <div class="tab-pane" id="logins">
								<table class="table">
											<thead>
												<tr>
													<th style="font-size: 12px;">IP Адрес</th>
													<th style="font-size: 12px;">Страна</th>
													<th style="font-size: 12px;">Дата</th>
												</tr>
											</thead>
											<tbody style="font-size: 12px;">
												<?php
												$SQLGetLogs = $odb -> query("SELECT * FROM `loginlogs` WHERE `username`='{$_SESSION['username']}' ORDER BY `date` DESC LIMIT 0, 5");
												while($getInfo = $SQLGetLogs -> fetch(PDO::FETCH_ASSOC)){
													$IP = $getInfo['ip'];
													$country = $getInfo['country'];
													$date = date("m-d-Y, h:i:s a" ,$getInfo['date']);
													echo '<tr>
															<td>'.htmlspecialchars($IP).'</td>
															<td>'.$country.'</td>
															<td>'.$date.'</td>
														  </tr>';
												}
												?>
											</tbody>                                     
										</table>
                
              </div>
              <div class="tab-pane" id="attacks">
                <table class="table">
											<thead>
												<tr>
													<th style="font-size: 12px;">Хост</th>
													<th style="font-size: 12px;">Порт</th>
													<th style="font-size: 12px;">Время</th>
													<th style="font-size: 12px;">Метод</th>
													<th style="font-size: 12px;">Дата</th>
												</tr>
											</thead>
											<tbody style="font-size: 12px;">
											<?php
											$SQLGetLogs = $odb -> query("SELECT * FROM `logs` WHERE user='{$_SESSION['username']}' ORDER BY `date` DESC LIMIT 10");
											while($getInfo = $SQLGetLogs -> fetch(PDO::FETCH_ASSOC)){
												$IP = $getInfo['ip'];
												$port = $getInfo['port'];
												$time = $getInfo['time'];
												$method = $odb->query("SELECT `fullname` FROM `methods` WHERE `name` = '{$getInfo['method']}'")->fetchColumn(0);
												$date = date("m-d-Y, h:i:s a" ,$getInfo['date']);
												echo '<tr><td>'.htmlspecialchars($IP).'</td><td>'.$port.'</td><td>'.$time.' seconds</td><td>'.htmlspecialchars($method).'</td><td>'.$date.'</td></tr>';
											}
											?>
											</tbody>                                       
										</table>
              </div>
              <div class="tab-pane" id="settings">
                <form class="form-horizontal push-10-t push-10" method="post">
                                        <div class="form-group">
                                            <div class="col-xs-12">
                                                <div class="form-material">
                                                    <input class="form-control" type="password" id="old" name="old" placeholder="Введите старый пароль..">
                                                    
                                                </div>
                                            </div>
											 </div>
											<div class="form-group">											  
                                            <div class="col-xs-12">
                                                <div class="form-material">
                                                    <input class="form-control" type="password" id="new" name="new" placeholder="Введите новый пароль..">
                                                    
                                                </div>
                                            </div>
                                        </div>                         
                                        <div class="form-group">
                                            <div class="col-xs-12">                                             
												<button class="btn btn-sm btn-danger" name="update" value="change" type="submit">
													<i class="fa fa-plus push-5-r"></i> Изменить
												</button>
											</div>
                                        </div>
                                    </form>
              </div>
			  <div class="tab-pane" id="2auth">
			  <form method="post">
			  <?php 
			  
			 // Enable the area for 2auth
			  
			 // User Info
			$SQLSelect = $odb->prepare("SELECT * FROM `users` WHERE `id` = :renew");
			$SQLSelect -> execute(array(':renew' => $_SESSION['ID']));
			while ($show = $SQLSelect->fetch(PDO::FETCH_ASSOC)) {
				$twoauthstatus   = $show['2auth'];			
			}
					
			// Disanled		
			  if($twoauthstatus == "0")
			  {
				  echo '<button name="enable2" class="btn btn-outline btn-info" type="submit">Enable</button>';
			  }
			  else
			  {
					$website = $_SERVER['SERVER_NAME']; //Your Website
					$qrCodeUrl = $authenticator->getQRCodeGoogleUrl($sitename, $AuthSecret,$website);
					echo '<div class="alert alert-info"> Убедитесь, что вы отсканировали приведенный ниже код, если вы включили 2Auth.</div>';
					echo '<img src="' .$qrCodeUrl. '"></img>';
			  }
			  
			  ?>
               </form>
              </div>
            </div>
          </div>
        </div>
      </div>
	 </div>
	</div>


<?php

	require_once 'footer.php';
	
?>