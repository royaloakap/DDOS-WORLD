<?php

	include 'header.php';

	if(!$user->isAdmin($odb)){
		header('home.php');
		exit;
	}
	
	if (isset($_POST['deletenews']) && is_numeric($_POST['deletenews'])){
		$delete = $_POST['deletenews'];
		$SQL = $odb -> query("DELETE FROM `news` WHERE `ID` = '$delete'");
		$notify = success('News has been removed');
	}

	if (isset($_POST['addnews'])){
		
		if (empty($_POST['title']) || empty($_POST['content']) || empty($_POST['icon'])){
			$notify = error('Please verify all fields');
		}
		elseif($user->safeString($_POST['content']) || $user->safeString($_POST['title']) || $user->safeString($_POST['icon']) || $user->safeString($_POST['color'])){
			$notify = error('Unsafe characters set');
		}
		else{
			$SQLinsert = $odb -> prepare("INSERT INTO `news` VALUES(NULL, :color, :icon, :title, :content, UNIX_TIMESTAMP())");
			$SQLinsert -> execute(array(':color' => $_POST['color'], ':icon' => $_POST['icon'], ':title' => $_POST['title'], ':content' => $_POST['content']));
			$notify = success('News has been added');
		}
	}
	
?>
<main id="main-container" style="min-height: 404px;"> 
	<div class="content bg-gray-lighter">
		<div class="row items-push">
			<div class="col-sm-8">
				<h1 class="page-heading">
					Settings <small>Manage News</small>
				</h1>
			</div>
			<div class="col-sm-4 text-right hidden-xs">
				<ol class="breadcrumb push-10-t">
					<li>Settings</li>
					<li><a class="link-effect" href="news.php">News</a></li>
				</ol>
			</div>
		</div>
	</div>
	<div class="content content-narrow">
		<?php
		if(isset($notify)){
			echo '<div class="row col-md-12">' . $notify . "</div>";
		}
		?>
		<div class="row">
			<div class="col-md-8">
				<div class="block">
					<div class="block-header bg-primary">
						<h3 class="block-title">Manage News</h3>
					</div>
					<table class="table">
						<thead>
							<tr>
								<th style="font-size: 13px;">Color</th>
								<th style="font-size: 13px;">Icon</th>
								<th style="font-size: 13px;">Title</th>
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
								$color = $getInfo['color'];
								$icon = $getInfo['icon'];
								$title = $getInfo['title'];
								$date = date("m-d-Y, h:i:s a" ,$getInfo['date']);
								echo '<tr>
										<td style="font-size: 13px;">'.htmlspecialchars($color).'</td>
										<td style="font-size: 13px;">'.htmlspecialchars($icon).'</td>
										<td style="font-size: 13px;">'.htmlspecialchars($title).'</td>
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
			<div class="col-md-4">
				<div class="block">
					<div class="block-header bg-primary">
						<h3 class="block-title">Add News</h3>
					</div>
					<div class="block-content block-content-narrow">
						<form class="form-horizontal push-10-t" method="post">
							<div class="form-group">
								<div class="col-sm-12">
									<div class="form-material">
										<input class="form-control" type="text" id="title" name="title">
										<label for="title">Title</label>
									</div>
								</div>
							</div> 
							<div class="form-group">
								<div class="col-sm-12">
									<div class="form-material">
										<textarea class="form-control" type="text" id="content" rows="5" name="content"></textarea>
										<label for="content">Content</label>
									</div>
								</div>
							</div>
							<div class="form-group">
								<div class="col-sm-12">
									<div class="form-material">
										<select class="form-control" name="icon" size="1">
											<option value="fa fa-check">Tick</option>
											<option value="fa fa-warning">Warning</option>
											<option value="fa fa-close">Cross</option>
										</select>
										<label for="icon">Icon</label>
									</div>
								</div>
							</div> 		
							<div class="form-group">
								<div class="col-sm-12">
									<div class="form-material">
										<select class="form-control" name="color" size="1">
											<option value="bg-city">Light Red</option>
											<option value="bg-danger">Dark Red</option>
											<option value="bg-success">Green</option>
											<option value="bg-primary">Primary</option>
											<option value="bg-warning">Orange</option>
										</select>
										<label for="color">Background</label>
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
		</div>     
	</div>
</main>
<?php

	include 'footer.php';

?>