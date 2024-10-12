<?php

	/// Require the header that already contains the sidebar and top of the website and head body tags
	$page = "Тикет";
	require_once 'header.php'; 
	
	if(is_numeric($_GET['id']) == false) {
		header('Location: support.php');
		exit;
	}

	$SQLGetTickets = $odb -> query("SELECT * FROM `tickets` WHERE `id` = {$_GET['id']}");
	while ($getInfo = $SQLGetTickets -> fetch(PDO::FETCH_ASSOC)){
		$username = $getInfo['username'];
		$subject = $getInfo['subject'];
		$status = $getInfo['status'];
		$original = $getInfo['content'];
		$date = date("m-d-Y, h:i:s a" ,$getInfo['date']);
	}

	if ($username != $_SESSION['username']){
		header('Location: support.php');
		exit;
	}

	if ($user -> safeString($original)){
		header('Location: support.php');
		exit;
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
      <!-- .row -->

      <!--/.row -->
      <!-- .row -->
      <div class="row">
		<div class="col-lg-12" id="div"></div>
		 <div class="col-md-12 col-sm-12 col-xs-12">
          <div class="white-box">
           <h3 class="box-title">Тема: <?php echo $subject; ?></h3>
             
			 <blockquote>
										<h5><?php echo $original; ?></h5>
										<footer><font color="white"><?php echo $username . ' [ ' . $date . ' ]'; ?></font></footer>
									</blockquote>
									<div id="response"></div>
			 
			 	<form class="form-horizontal push-10-t push-10" action="base_forms_premade.html" method="post" onsubmit="return false;">
										<div class="form-group">
											<div class="col-xs-12">
												<div class="form-material floating">
												<label for="reply">Ответить здесь <i style="display: none;" id="image" class="fa fa-cog fa-spin"></i></label>
													<textarea class="form-control" id="reply" rows="4"></textarea>
													
												</div>
											</div>
										</div>                         
                                        <div class="form-group">
                                            <div class="col-xs-12 text-center">                                             
												<button class="btn btn-sm btn-success" onclick="message()">
													<i class="fa fa-plus push-5-r"></i> Ответить на тикет
												</button>
												<button class="btn btn-sm btn-danger" onclick="closeticket()">
													<i class="fa fa-ban push-5-r"></i> Закрыть тикет
												</button>
                                            </div>
                                        </div>
                                    </form>
          </div>
        </div>
      </div>
										<script>
			
			response();
			
			function response(){
				var xmlhttp;
				if (window.XMLHttpRequest) {
					xmlhttp=new XMLHttpRequest();
				}
				else {
					xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
				}
				xmlhttp.onreadystatechange=function() {
					if (xmlhttp.readyState==4 && xmlhttp.status==200) {
						document.getElementById("response").innerHTML=xmlhttp.responseText;
					}
				}
				xmlhttp.open("GET","includes/ajax/user/tickets/tickets.php?id=<?php echo $_GET['id']; ?>",true);
				xmlhttp.send();
			}
			
			function closeticket(){
				document.getElementById("image").style.display="inline"; 
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
						document.getElementById("image").style.display="none";
					}
				}
				xmlhttp.open("GET","includes/ajax/user/tickets/closeticket.php?id=<?php echo $_GET['id']; ?>",true);
				xmlhttp.send();
			}
				
			function message() {
				var reply=$('#reply').val();
				document.getElementById("image").style.display="inline"; 
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
						document.getElementById("image").style.display="none";
						if (xmlhttp.responseText.search("SUCCESS") != -1) {
							response();
						}
					}
				}
				xmlhttp.open("GET","includes/ajax/user/tickets/reply.php?id=<?php echo $_GET['id']; ?>" + "&message=" + reply,true);
				xmlhttp.send();
			}
			
			</script>


      <!--/.row -->
<?php

	require_once 'footer.php';
	
?>