<?php

header("X-XSS-Protection: 1; mode=block");

	/// Require the header that already contains the sidebar and top of the website and head body tags
	$page = "Manage TOS";
	require_once 'header.php'; 
	
		
	if (isset($_POST['tosUpdate'])){
		
			$input = $_POST['tos-archive'];
			
			if(empty($input))
			{
				$notify = error('There was no input made to Terms of Service!');
			}
			$SQL = $odb -> prepare("UPDATE `tos` SET `archive` = :archive");
			$SQL -> execute(array(':archive' => $input));
			$notify = success('Terms of services updated!');
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
	
	     <div class="col-md-12 col-sm-12 col-xs-12">
          <div class="white-box">
            <h3 class="box-title">Manage Terms of Services</h3>
				<?php 

						$SQLGetNews = $odb -> query("SELECT * FROM `tos`");
						while ($getInfo = $SQLGetNews -> fetch(PDO::FETCH_ASSOC)){
							$tos = $getInfo['archive'];
						}

						?>
			
			<form method="post">
              <div class="form-group">
                <textarea class="textarea_editor form-control" rows="15" name="tos-archive" placeholder="<?php echo $tos; ?>"></textarea>
              </div>
			  <div class="form-group">
                <button class="btn btn-outline btn-success" type="submit" name="tosUpdate"> <i class="fa fa-plus"></i> Update!</button>
              </div>
            </form>

						

          </div>
        </div>
		

      </div>
	  
<?php

	require_once 'footer.php';
	
?>