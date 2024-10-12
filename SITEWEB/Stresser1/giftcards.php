<?php

	/// Require the header that already contains the sidebar and top of the website and head body tags
	$page = "Подарочные купоны";
	require_once 'header.php'; 
	
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
	  <div id="div"></div>
	     <div class="col-md-8 col-sm-12 col-xs-12">
          <div class="white-box">
		  <h3 class="box-title">Активировать код <i style="display: none;" id="icon" class="fa fa-cog fa-spin"></i></h3>
			<form class="form-horizontal" method="post" onsubmit="return false;"  >
              <div class="form-group">
                <label for="GiftCode" class="col-sm-3 control-label">Подарочный код</label>
                <div class="col-sm-9">
                  <input type="text" class="form-control" name="code" id="code" placeholder="XXXXXX">
                </div>
              </div>
              <div class="form-group m-b-0">
                <div class="col-sm-offset-3 col-sm-9">
                  <button  onclick="redeemCode()" class="btn btn-outline btn-info">Активировать код</button>
                </div>
              </div>
            </form>
          </div>
        </div>
	
		
		 <div class="col-md-4 col-sm-12 col-xs-12">
          <div class="white-box">
           <h3 class="box-title">Подарочные купоны</h3>
                <dl>
                  <dt>Как я могу активировать его?</dt>
                  <dd>Вы просто активируете его, поместив код в текстовое поле слева и нажав «Активировать», и план будет назначен вашей учетной записи!</dd>
<br/>
                  <dt>Что я получу, активировав этот код?</dt>
                  <dd>Подарочные коды назначаются планам. Когда вы активируете код, вы получите план, как если бы платили за него.</dd>
				  <br/>
                 
                  
                </dl>
          </div>
        </div>
      </div>
									
			<script>
			function redeemCode() {
				var code = $('#code').val();
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
							inbox();
						}
					}
				}
				xmlhttp.open("GET","includes/ajax/user/giftcodes/redeem.php?user=<?php echo $_SESSION['ID']; ?>" + "&code=" + code,true);
				xmlhttp.send();
			}
			</script>
			
			</div>
      <!--/.row -->
<?php

	require_once 'footer.php';
	
?>