<?php

	/// Require the header that already contains the sidebar and top of the website and head body tags
	$page = "FAQ";
	require_once 'header.php'; 
	
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
            <h3 class="box-title">Overview Giftcards</h3>
				<table class="table">
						<tr>
							<th style="font-size: 13px;">Question</th>
								<th style="font-size: 13px;">Answer</th>
								<th class="text-center" style="font-size: 13px;">Delete</th>
						</tr>
						<tr>
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
									</tr>                                       
					</table>
          </div>
        </div>

		<div class="col-md-6 col-sm-12 col-xs-12">
          <div class="white-box">
            <h3 class="box-title">Add new giftcard</h3>
				<form class="form-horizontal push-10-t" method="post">
							<div class="form-group">
								<div class="col-sm-12">
									<div class="form-material">
									<label for="title">Question</label>
										<input class="form-control" type="text" id="title" name="question">
										
									</div>
								</div>
							</div> 
							<div class="form-group">
								<div class="col-sm-12">
									<div class="form-material">
									<label for="content">Answer</label>
										<textarea class="form-control" type="text" id="content" rows="5" name="answer"></textarea>
										
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
	  
<?php

	require_once 'footer.php';
	
?>