<?php

header("X-XSS-Protection: 1; mode=block");

    $page = "Activate Code";
	/// Require the header that already contains the sidebar and top of the website and head body tags
	require_once 'header.php'; 
	
?>

<html>
		<div class="page-wrapper" style="display: block;">
			<div class="page-breadcrumb">
				<div class="d-flex align-items-center">
					<h4 class="page-title text-truncate text-dark font-weight-medium mb-0">Activate GiftCode</h4>
					<div class="ml-auto">
						<nav aria-label="breadcrumb">
							<ol class="breadcrumb m-0 p-0">
								<li class="breadcrumb-item text-primary active" aria-current="page"><?= $sitename; ?> </li>
								<li class="breadcrumb-item text-muted" aria-current="page">Activate</li>
							</ol>
						</nav>
					</div>
				</div>
			</div>
      </ul>
<div class="container-fluid">
 <div class="row">
    <div class="col-md-6 col-lg-6">
     <div class="card">
      <div class="card-body">
          <h4 class="card-title">Activate Code <i style="display: none;" id="icon"></i></h4>
          <form class="form-horizontal" method="post" onsubmit="return false;">
		  <div id="div"></div>
            <div class="form-group">
              <div class="col-sm-12">
                <input type="text" class="form-control" name="code" id="code" placeholder="XXXXXX">
              </div>
              <br>
              <div class="form-group m-b-0">
                <div class="col-sm-offset-3 col-sm-3">
                  <button id="launch" onclick="redeemCode()" class="btn btn-outline btn-info">Redeem Code</button>
                </div>
              </div>
            </div>
          </form>
        </div>
      </div>
    </div>
	<div class="col-md-6 col-lg-6">
      <div class="card">
         <div class="card-body">
            <p class="alert alert-fill-primary text-center" >Is it automatic?</p>
          </div>
          <div id="collapse-6" class="collapse show" data-parent="#accordion2" style="">
            <div class="card-body">
              After purchasing a package you will receive a code to your email, just enter it and click redeem, the desired plan will be automatically added to your account.
            </div>
          </div>
        </div>
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
				xmlhttp.open("GET","vvv/files/user/giftcodes/redeem.php?user=<?php echo $_SESSION['ID']; ?>" + "&code=" + code,true);
				xmlhttp.send();
			}
			</script>

</div>
</html>