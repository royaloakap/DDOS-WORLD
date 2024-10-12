<?php
include("header.php");
$id = $_GET['id'];
if(!is_numeric($id)) {
die('lol');
}

$SQLGetInfo = $odb -> prepare("SELECT * FROM `plans` WHERE `ID` = :id LIMIT 1");
$SQLGetInfo -> execute(array(':id' => $_GET['id']));
$planInfo = $SQLGetInfo -> fetch(PDO::FETCH_ASSOC);
$currentName = $planInfo['name'];
$currentMbt = $planInfo['mbt'];
$currentUnit = $planInfo['unit'];
$currentPrice = $planInfo['price'];
$currentLength = $planInfo['length'];
$currentconcurrents = $planInfo['concurrents'];
$currentprivate = $planInfo['private'];
function selectedUnit($check, $currentUnit)
{
	if ($currentUnit == $check)
	{
		return 'selected="selected"';
	}
}

function selectedPrivate($check, $currentprivate)
{
	if ($currentprivate == $check)
	{
		return 'selected="selected"';
	}
}
if (!($user -> isAdmin($odb)))
{
	header('location: ../index.php');
	die();
}
?>
			                    
                        <meta http-equiv="content-type" content="text/html;charset=UTF-12" />								
  <script src="../assets/js/spinner.js"></script>
			 <div class="page-wrapper">
			  <div class="page-content">
			  <?php include("fuctions/pinit.php"); ?>				  
			<div class="col-md-12 grid-margin stretch-card">
              <div class="card">                
                   
                             <div class="card text-white bg-primary">
                                <div class="card-header text-center">Plan Manager</div>
	                              </div>
                                <div class="block-content controls">
                                    <form method="post">
                                    <div class="row-form">
                                        <div class="col-md-4"><strong>Name:</strong></div>
                                        <div class="col-md-12"><input type="text" class="form-control" name="nameAdd" value="<?php echo htmlspecialchars($currentName); ?>"/></div>
                                    </div>
                                    <div class="row-form">
                                        <div class="col-md-4"><strong>Max Boot Time:</strong></div>
                                        <div class="col-md-12"><input type="text" class="form-control" name="mbt" value="<?php echo htmlspecialchars($currentMbt); ?>"/></div>
                                    </div>
                                    <div class="row-form">
                                        <div class="col-md-4"><strong>Concurrents:</strong></div>
                                        <div class="col-md-12"><input type="text" class="form-control" name="concurrents" value="<?php echo htmlspecialchars($currentconcurrents); ?>"/></div>
                                    </div>
                                    <div class="row-form">
                                        <div class="col-md-4"><strong>Price:</strong></div>
                                        <div class="col-md-12"><input type="text" class="form-control" name="price" value="<?php echo htmlspecialchars($currentPrice); ?>"/></div>
                                    </div>
                                    <div class="row-form">
                                        <div class="col-md-4"><strong>Membership Length:</strong></div>
                                        <div class="col-md-12"><input type="text" class="form-control" name="lengthAdd" value="<?php echo htmlspecialchars($currentLength); ?>"/></div>
                                    </div>
                                    <div class="row-form">
                                        <div class="col-md-4"><strong>Membership Unit:</strong></div>
                                        <div class="col-md-12">
                                            <select name="unit" class="form-control">
                    <option value="Days" <?php echo selectedUnit('Days',$currentUnit); ?> >Days</option>
                    <option value="Weeks" <?php echo selectedUnit('Weeks', $currentUnit); ?> >Weeks</option>
                    <option value="Months" <?php echo selectedUnit('Months', $currentUnit); ?> >Months</option>
                    <option value="Years" <?php echo selectedUnit('Years', $currentUnit); ?> >Years</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="row-form">
                                        <div class="col-md-4"><strong>Private:</strong></div>
                                        <div class="col-md-12">
                                            <select name="private" class="form-control">
                    <option value="1" <?php echo selectedPrivate(1, $currentprivate); ?>>Yes</option>
                    <option value="0" <?php echo selectedPrivate(0, $currentprivate); ?>>No</option>
                                            </select>
                                        </div>
                                    </div>
									<div class="card-header">
									 <div  class="col-xs-4 text-center" >
                                       <button name="update" class="btn btn-success">Update</button>
                                </div>
								 </div>                             
                                   </div>
                                    </div>
                                      </div>


    </body>
</html>