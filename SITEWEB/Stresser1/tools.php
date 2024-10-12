<?php

	/// Require the header that already contains the sidebar and top of the website and head body tags
	$page = "Инструменты";
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
	  <div class="col-lg-12" id="resolverz"></div>
      <div class="row">
	     <div class="col-md-8 col-sm-12 col-xs-12">
          <div class="white-box">
		  <h3 class="box-title">Инструменты <i style="display: none;" id="toolz" class="fa fa-cog fa-spin"></i></h3>
           <form class="form-horizontal push-10-t push-10" action="base_forms_premade.html" method="post" onsubmit="return false;">
                                        <div class="form-group">
                                            <div class="col-xs-12">
											<label for="tool">Значение</label>
                                                    <input class="form-control" type="text" name="value" id="tool">
                                                    
                                            </div>
                                        </div>         
                                        <div class="form-group">
                                            <div class="col-xs-12">
											<label for="tool">Инструмент</label>
                                                    <select class="form-control" id="resolver" name="resolver" size="1">
														<option value="domain">Domain</option>
														<option value="geo">Geolocation</option>
                                                    </select>
                                            </div>
                                        </div>                   
                                        <div class="form-group">
                                            <div class="col-xs-12">
                                                <button onclick="tools()" class="btn btn-sm btn-info"><i class="fa fa-plus"></i> Найти результаты</button>
                                            </div>
                                        </div>
									
                                    </form>
          </div>
        </div>
		
		 <div class="col-md-4 col-sm-12 col-xs-12">
          <div class="white-box">
           <h3 class="box-title">Информация об инструментах</h3>
                <dl>
                  <dt>Резолвер Domain</dt>
                  <dd>Этот инструмент попытается найти IP-адрес, скрытый за доменом.</dd>
<br/>
<dt>Инструмент Geolocation</dt>
                  <dd>Этот инструмент попытается найти расположение сервера, находящегося за данным айпи адресом.</dd>
<br/> 
                </dl>
          </div>
        </div>
      </div>
									
			<script>
			
			function tools() {
			var tool=$('#tool').val();
			var resolver=$('#resolver').val();
			document.getElementById("toolz").style.display="inline"; 
			var xmlhttp;
			if (window.XMLHttpRequest) {
				xmlhttp=new XMLHttpRequest();
			}
			else {
				xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
			}
			xmlhttp.onreadystatechange=function() {
				if (xmlhttp.readyState==4 && xmlhttp.status==200) {
					document.getElementById("toolz").style.display="none";
					document.getElementById("resolverz").innerHTML=xmlhttp.responseText;
				}
			}
			xmlhttp.open("GET","includes/ajax/user/tools/tools.php?type=" + resolver + "&resolve=" + tool,true);
			xmlhttp.send();
		}
			</script>
			</div>
      <!--/.row -->
<?php

	require_once 'footer.php';
	
?>