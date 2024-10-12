<?php

	include 'header.php';

	if(!$user->isAdmin($odb)){
		header('home.php');
		exit;
	}
	
	if (isset($_POST['deletefaq']) && is_numeric($_POST['deletefaq'])){
		$delete = $_POST['deletefaq'];
		$SQL = $odb -> query("DELETE FROM `faq` WHERE `id` = '$delete'");
		$notify = success('FAQ has been removed');
	}

	if (isset($_POST['addfaq'])){
		
		if (empty($_POST['question']) || empty($_POST['answer'])){
			$notify = error('Please verify all fields');
		}
		elseif($user->safeString($_POST['question']) || $user->safeString($_POST['answer'])){
			$notify = error('Unsafe characters set');
		}
		else{
			$SQLinsert = $odb -> prepare("INSERT INTO `faq` VALUES(NULL, :question, :answer)");
			$SQLinsert -> execute(array(':question' => $_POST['question'], ':answer' => $_POST['answer']));
			$notify = success('FAQ has been added');
		}
	}
	
?>
<main id="main-container" style="min-height: 404px;"> 
	<div class="content bg-gray-lighter">
		<div class="row items-push">
			<div class="col-sm-8">
				<h1 class="page-heading">
					Settings <small>Manage FAQ</small>
				</h1>
			</div>
			<div class="col-sm-4 text-right hidden-xs">
				<ol class="breadcrumb push-10-t">
					<li>Settings</li>
					<li><a class="link-effect" href="faq.php">FAQ</a></li>
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
						<h3 class="block-title">Manage FAQ</h3>
					</div>
					<table class="table">
						<thead>
							<tr>
								<th style="font-size: 13px;">Question</th>
								<th style="font-size: 13px;">Answer</th>
								<th class="text-center" style="font-size: 13px;">Delete</th>
							</tr>
						</thead>
						<tbody>
							<form method="post">
							<?php 
							$SQLGetfaq = $odb -> query("SELECT * FROM `faq` ORDER BY `id` DESC");
							while ($getInfo = $SQLGetfaq -> fetch(PDO::FETCH_ASSOC)){
								$id = $getInfo['id'];
								$question = $getInfo['question'];
								$answer = $getInfo['answer'];
								echo '<tr>
										<td style="font-size: 13px;">'.htmlspecialchars($question).'</td>
										<td style="font-size: 13px;">'.htmlspecialchars($answer).'</td>
										<td class="text-center"><button name="deletefaq" value="'.$id.'"class="btn btn-danger btn-sm"><i class="fa fa-trash-o"></i></button></td>
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
						<h3 class="block-title">Add FAQ</h3>
					</div>
					<div class="block-content block-content-narrow">
						<form class="form-horizontal push-10-t" method="post">
							<div class="form-group">
								<div class="col-sm-12">
									<div class="form-material">
										<input class="form-control" type="text" id="title" name="question">
										<label for="title">Question</label>
									</div>
								</div>
							</div> 
							<div class="form-group">
								<div class="col-sm-12">
									<div class="form-material">
										<textarea class="form-control" type="text" id="content" rows="5" name="answer"></textarea>
										<label for="content">Answer</label>
									</div>
								</div>
							</div> 
							<div class="form-group">
								<div class="col-sm-9">
									<button name="addfaq" value="do" class="btn btn-sm btn-primary" type="submit">Submit</button>
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