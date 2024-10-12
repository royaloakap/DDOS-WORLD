<?php
include("@/header.php");
$paginaname = 'Bitcoin';

	ob_start();
	session_start();
	require_once("@/config.php");
	require_once("@/init.php");
	
		if($_GET['id'] == "")
	{ 
		header("Location: index.php");
	} else {
			
	if(isset($_GET['id']) && Is_Numeric($_GET['id']) && $user -> LoggedIn())
	{
	$id = (int)$_GET['id'];
	$row = $odb -> query("SELECT * FROM `plans` WHERE `ID` = '$id'") -> fetch();
	if($row == "")
	{
	 header("Location: index.php");
	} else {

?>
<!DOCTYPE html>
<!--[if IE 9]>         <html class="no-js lt-ie10"> <![endif]-->
<!--[if gt IE 9]><!--> <html class="no-js"> <!--<![endif]-->
<head>
  <script src="../assets/js/spinner.js"></script>
<div class="page-wrapper">
     <div class="page-content">
	 	<div class="alert alert-fill-primary">	
          <span data-feather="credit-card" class="icon-md text-light mr-2"></span>
	   <span><?php echo htmlspecialchars($paginaname); ?></span>
       </div>
<div class="row">
  <div class="col-md-12">
    <div class="card">
      <div class="card-body">
        <div class="container-fluid d-flex justify-content-between">
          <div class="col-lg-3 pl-0">
            <a href="#" class="noble-ui-logo logo-light d-block mt-3"><?php echo ($sitename)?></a>                 
            <h5 class="mt-5 mb-2 text-muted">Invoice to : <?php echo $_SESSION['username']?></h5>
          </div>
          <div class="col-lg-3 pr-0">
            <h4 class="font-weight-medium text-uppercase text-right mt-4 mb-2">Package</h4>
            <h4 class="text-right mb-5 pb-4"><?php echo $row['name']; ?></h4>
          </div>
        </div>
        <div class="container-fluid mt-5 d-flex justify-content-center w-100">
          <div class="table-responsive w-100">
              <table class="table table-bordered">
                <thead>
                  <tr>
                      <th>Attack Time</th>
                      <th class="text-right">Concurrents</th>
                      <th class="text-right">Lenght</th>
                      <th class="text-right">Price</th>
                    </tr>
                </thead>
                <tbody>
                  <tr class="text-right">                    
                    <td class="text-left"><?php echo $row['mbt']; ?></td>
                    <td><?php echo $row['concurrents']; ?></td>
                    <td><?php echo $row['length']; ?> <?php echo $row['unit']; ?></td>
                    <td>$<?php echo $row['price']; ?></td>
                  </tr>
                </tbody>
              </table>
            </div>
        </div>
        <div class="container-fluid mt-5 w-100">
          <div class="row">
            <div class="col-md-12 ml-auto">
			<p class="alert alert-fill-danger"> Bitcoin adress </p>
<input class="form-control form-control-xs" type="text" onclick="if (!window.__cfRLUnblockHandlers) return false; this.select()" value="<?php echo ($bitcoin)?>" readonly="">
            </div>
          </div>
        </div>
		
<?php
	}
	}
	}
?>
			
               </div>         
          </div>
	</div>

		<?php include("@/script.php"); ?>
    </body>
</html>