<?php

	/// Require the header that already contains the sidebar and top of the website and head body tags
	$page = "Manage News";
	require_once 'header.php'; 
	
		
	if (isset($_POST['deletenews']) && is_numeric($_POST['deletenews'])){
		$delete = $_POST['deletenews'];
		$SQL = $odb -> query("DELETE FROM `news` WHERE `ID` = '$delete'");
		$notify = success('News has been removed');
	}

	if (isset($_POST['addnews'])){
		
		if (empty($_POST['title']) || empty($_POST['content'])){
			$notify = error('Please verify all fields');
		}
		elseif($user->safeString($_POST['content']) || $user->safeString($_POST['title'])){
			$notify = error('Unsafe characters set');
		}
		else{
			$SQLinsert = $odb -> prepare("INSERT INTO `news` VALUES(NULL,  :title, :content, UNIX_TIMESTAMP())");
			$SQLinsert -> execute(array(':title' => $_POST['title'], ':content' => $_POST['content']));
			$notify = success('News has been added');
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
            <h3 class="box-title">Manage News</h3>
				
				<table class="table">
						<thead>
							<tr>
								<th style="font-size: 13px;">Title</th>
								<th style="font-size: 13px;">Contnet</th>
								<th style="font-size: 13px;">Date</th>
								<th class="text-center" style="font-size: 13px;">Delete</th>
							</tr>
						</thead>
						<tbody>
							<form method="post">
							<?php 
							$SQLGetNews = $odb -> query("SELECT * FROM `news` ORDER BY `date` DESC");
							while ($getInfo = $SQLGetNews -> fetch(PDO::FETCH_ASSOC)){
								$id = $getInfo['ID'];
								$title = $getInfo['title'];
								$content = $getInfo['content'];
								$date = date("m-d-Y, h:i:s a" ,$getInfo['date']);
								echo '<tr>
										<td style="font-size: 13px;">'.htmlspecialchars($title).'</td>
										<td style="font-size: 13px;">'.$content.'</td>
										<td style="font-size: 13px;">'.$date.'</td>
										<td class="text-center"><button name="deletenews" value="'.$id.'"class="btn btn-danger btn-sm"><i class="fa fa-trash-o"></i></button></td>
									  </tr>';
							}
							?>
							</form>
						</tbody>                                       
                    </table>

          </div>
        </div>
		
		<div class="col-md-6 col-sm-12 col-xs-12">
          <div class="white-box">
            <h3 class="box-title">Manage News</h3>
				
				<form class="form-horizontal push-10-t" method="post">
							<div class="form-group">
								<div class="col-sm-12">
									<div class="form-material">
									<label for="title">Title</label>
										<input class="form-control" type="text" id="title" name="title">
										
									</div>
								</div>
							</div> 
							<div class="form-group">
								<div class="col-sm-12">
									<div class="form-material">
									<label for="content">Content</label>
										<textarea class="form-control" type="text" id="content" rows="5" name="content"></textarea>
										
									</div>
								</div>
							</div>	
							<div class="form-group">
								<div class="col-sm-9">
									<button name="addnews" value="do" class="btn btn-sm btn-primary" type="submit">Submit</button>
								</div>
							</div>
						</form>

          </div>
        </div>
      </div>
	  
<?php

	require_once 'footer.php';
	
?>