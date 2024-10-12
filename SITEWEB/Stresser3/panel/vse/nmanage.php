<?php

header("X-XSS-Protection: 1; mode=block");

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


<title><?php echo $sitename; ?> | News Manage</title>
		<div class="page-wrapper" style="display: block;">
			<div class="page-breadcrumb">
				<div class="d-flex align-items-center">
					<h4 class="page-title text-truncate text-white font-weight-medium mb-0">News Manage</h4>
					<div class="ml-auto">
						<nav aria-label="breadcrumb">
							<ol class="breadcrumb m-0 p-0">
								<li class="breadcrumb-item text-sql" aria-current="page"><?php echo $sitename; ?></li>
								<li class="breadcrumb-item text-muted" aria-current="page">News Manage</li>
							</ol>
						</nav>
					</div>
				</div>
			</div>
      </ul>
			<div class="container-fluid">
	  <?php
		if(isset($notify)){
			echo ($notify);
		}
		?>
      <div class="row">
	
	     <div class="col-md-6 col-sm-12 col-xs-12">
          <div class="white-box">
				
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