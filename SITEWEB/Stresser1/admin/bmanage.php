<?php

	/// Require the header that already contains the sidebar and top of the website and head body tags
	$page = "Blacklist Manager";
	require_once 'header.php'; 
	
		
	// Blacklist
	if (isset($_POST['deleteblacklist'])){
		$delete = $_POST['deleteblacklist'];
		$SQL = $odb -> query("DELETE FROM `blacklist` WHERE `ID` = '$delete'");
		$notify = success('Blacklist has been removed');
	}
	
	if (isset($_POST['addblacklist'])){
	
		if (empty($_POST['value'])){
			$error = 'Please verify all fields';
		}

		$value = $_POST['value'];
		$type = $_POST['type'];

		if (empty($error)){	
			$SQLinsert = $odb -> prepare("INSERT INTO `blacklist` VALUES(NULL, :value, :type)");
			$SQLinsert -> execute(array(':value' => $value, ':type' => $type));
			$notify = success('Blacklist has been added');
		}
		else{
			$notify = error($error);
		}
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
			echo ($notify);
		}
		?>
      <div class="row">
	
	     <div class="col-md-6 col-sm-12 col-xs-12">
          <div class="white-box">
            <h3 class="box-title">Blacklist Manager</h3>
				
				<table class="table">
						<tr>
							<th style="font-size: 12px;">Value</th>
							<th style="font-size: 12px;">Type</th>
							<th style="font-size: 12px;">Delete</th>
						</tr>
						<tr>
							<form method="post">
								<?php
								$SQLGetMethods = $odb -> query("SELECT * FROM `blacklist`");
								while($getInfo = $SQLGetMethods -> fetch(PDO::FETCH_ASSOC)){
									$id = $getInfo['ID'];
									$value = $getInfo['data'];
									$type = $getInfo['type'];
									echo '<tr>
											<td style="font-size: 12px;">'.htmlspecialchars($value).'</td>
											<td style="font-size: 12px;">'.htmlspecialchars($type).'</td>
											<td style="font-size: 12px;"><button name="deleteblacklist" value="'.$id.'" class="btn btn-danger btn-sm"><i class="fa fa-trash-o"></i></button></td>
										</tr>';
								}
								if(empty($SQLGetMethods)){
									echo error('No Blacklists');
								}
								?>
							</form>
						</tr>                                       
					</table>

          </div>
        </div>
		
		<div class="col-md-6 col-sm-12 col-xs-12">
          <div class="white-box">
            <h3 class="box-title">Add Blacklist</h3>
				
				<form class="form-horizontal push-10-t" method="post">
							<div class="form-group">
								<div class="col-sm-12">
									<div class="form-material">
									<label for="name">Value</label>
										<input class="form-control" type="text" id="name" name="value">
										
									</div>
								</div>
							</div> 
							<div class="form-group">
								<div class="col-sm-12">
									<div class="form-material">
									<label for="type">Type</label>
										<select class="form-control" id="type" name="type" size="1">
											<option value="victim">Host</option>
										</select>
										
									</div>
								</div>
							</div>
							<div class="form-group">
								<div class="col-sm-9">
									<button name="addblacklist" value="do" class="btn btn-sm btn-primary" type="submit">Submit</button>
								</div>
							</div>
						</form>

          </div>
        </div>
      </div>
	  
<?php

	require_once 'footer.php';
	
?>