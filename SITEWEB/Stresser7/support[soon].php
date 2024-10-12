<?php
include("@/header.php");
$paginaname = 'Support Ticket';


?>
<!DOCTYPE html>
<!--[if IE 9]>         <html class="no-js lt-ie10"> <![endif]-->
<!--[if gt IE 9]><!--> <html class="no-js"> <!--<![endif]-->

  <div class="page-wrapper">
     <div class="page-content">
	 	<div class="alert alert-fill-primary">	
          <span data-feather="tag" class="icon-md text-light mr-2"></span>
	   <span><?php echo htmlspecialchars($paginaname); ?></span>
       </div>
	   
<script>
        			inbox();

        			function inbox() {
                        Codebase.blocks('#ticketsblock', 'state_loading');
        				var xmlhttp;
        				if (window.XMLHttpRequest) {
        					xmlhttp = new XMLHttpRequest();
        				}
        				else {
        					xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
        				}
        				xmlhttp.onreadystatechange = function(){
        					if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
                                Codebase.blocks('#ticketsblock', 'state_normal');
        						document.getElementById("messages").innerHTML = xmlhttp.responseText;
        					}
        				}
        				xmlhttp.open("GET","tickets/inbox",true);
        				xmlhttp.send();
        			}



        			function unread() {
                        Codebase.blocks('#ticketsblock', 'state_loading');
        				var xmlhttp;
        				if (window.XMLHttpRequest) {
        					xmlhttp = new XMLHttpRequest();
        				}
        				else {
        					xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
        				}
        				xmlhttp.onreadystatechange = function(){
        					if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
                                Codebase.blocks('#ticketsblock', 'state_normal');
        						document.getElementById("messages").innerHTML = xmlhttp.responseText;
        					}
        				}
        				xmlhttp.open("GET","tickets/unread",true);
        				xmlhttp.send();
        			}
					
					function history() {
                        Codebase.blocks('#ticketsblock', 'state_loading');
        				var xmlhttp;
        				if (window.XMLHttpRequest) {
        					xmlhttp = new XMLHttpRequest();
        				}
        				else {
        					xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
        				}
        				xmlhttp.onreadystatechange = function(){
        					if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
                                Codebase.blocks('#ticketsblock', 'state_normal');
        						document.getElementById("messages").innerHTML = xmlhttp.responseText;
        					}
        				}
        				xmlhttp.open("GET","tickets/history",true);
        				xmlhttp.send();
        			}
					
					function newticket() {
        				var subject = $('#subject').val();
						var dep = $('#dep').val();
						var pri = $('#priority').val();
        				var content = $('#content').val();
        				document.getElementById("icon").style.display="inline";
        				var xmlhttp;
        				if (window.XMLHttpRequest) {
        					xmlhttp=new XMLHttpRequest();
        				}
        				else {
        					xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
        				}
        				xmlhttp.onreadystatechange=function() {
        					if (xmlhttp.readyState==4 && xmlhttp.status==200) {
        						document.getElementById("icon").style.display="none";
        						document.getElementById("div").innerHTML=xmlhttp.responseText;
        						if (xmlhttp.responseText.search("SUCCESS") != -1) {
								swal(
								  'Ticket Sent!',
								  'The ticket has been successfully sent!',
								  'success'
								  )
        							inbox();

								} else {
								  swal(
								  'Oops...',
								  xmlhttp.responseText,
								  'error'
								  )
								}
        					}
        				}
        				xmlhttp.open("POST","tickets/newticket.php",true);
						xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        				xmlhttp.send("subject=" + subject + "&content=" + content + "&department=" + dep + "&priority=" + pri);
        			}
</script>
<div class="page-content">
        <div class="row inbox-wrapper">
  <div class="col-lg-12">
    <div class="card">
      <div class="card-body">
        <div class="row">
          <div class="col-lg-3 email-aside border-lg-right">
            <div class="aside-content">
              <div class="aside-compose"><a class="btn btn-primary btn-block" href="#">Open Ticket</a></div>
              <div class="aside-nav collapse">
                <ul class="nav">
                  <li class="active"><a href="#"><span class="icon"><i data-feather="inbox"></i></span>Inbox<span class="badge badge-danger-muted text-white font-weight-bold float-right"></span></a></li>
              </div>
            </div>
          </div>
          <div class="col-lg-9 email-content">
            <div class="email-inbox-header">
              <div class="row align-items-center">
                <div class="col-lg-6">
                  <div class="email-title mb-2 mb-md-0"><span class="icon"><i data-feather="inbox"></i></span> Inbox <span class="new-messages"></span> </div>
                </div>
              </div>
            </div>
            <div class="email-list">
              <div class="email-list-item">
                <div class="email-list-actions">
                  <a class="favorite" href="#"><span><i data-feather="star"></i></span></a>
                </div>
                <a href="#" class="email-list-detail">
                  <div>
                    <span class="from">TEST</span>
                    <p class="msg">TEST</p>
                  </div>
                  <span class="date">
                    <span class="icon"><i data-feather="paperclip"></i> </span>
                    28 Feb
                  </span>
                </a>
              </div>
            </div>
          </div>
        </div>
        
      </div>
    </div>
  </div>
</div>
      </div>
  </div>

    <!-- base js -->
    <script src="../js/app.js"></script>
    <script src="../assets/plugins/feather-icons/feather.min.js"></script>
    <script src="../assets/plugins/perfect-scrollbar/perfect-scrollbar.min.js"></script>
    <!-- end base js -->

    <!-- plugin js -->
        <!-- end plugin js -->

    <!-- common js -->
    <script src="../assets/js/template.js"></script>
    <!-- end common js -->

    </body>

<!-- Mirrored from www.nobleui.com/laravel/template/dark/email/inbox by HTTrack Website Copier/3.x [XR&CO'2014], Fri, 07 Feb 2020 02:14:48 GMT -->
</html>
