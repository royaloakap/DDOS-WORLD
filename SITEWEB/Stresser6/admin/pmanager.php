<?php
include("header.php");
if (!($user -> isAdmin($odb)))
{
	header('location: ../index.php');
	die();
}
?>
<!DOCTYPE html>
<html>

<meta http-equiv="content-type" content="text/html;charset=UTF-12" />								
  <script src="../assets/js/spinner.js"></script>
			 <div class="page-wrapper">
			  <div class="page-content">
			  <?php if (isset($_POST['addplan']))
{
			$name = $_POST['name'];
			$unit = $_POST['unit'];
			$length = $_POST['length'];
			$mbt = intval($_POST['mbt']);
			$price = floatval($_POST['price']);
			$concurrents = $_POST['concurrents'];
			$private = $_POST['private'];
			$premium = $_POST['premium'];
			$errors = array();
			
			if (empty($price) || empty($name) || empty($unit) || empty($length) || empty($mbt) || empty($concurrents))
			{
				$error = 'Fill in all fields';
			}
			if (empty($error))
			{
				$SQLinsert = $odb -> prepare("INSERT INTO `plans` VALUES(NULL, :name, :mbt, :unit, :length, :price, :concurrents, :premium, :private)");
				$SQLinsert -> execute(array(':name' => $name, ':mbt' => $mbt, ':unit' => $unit, ':length' => $length, ':price' => $price, ':concurrents' => $concurrents, ':premium' => $premium, ':private' => $private));
				echo success('Plan has been added');
			}
			else
			{
				echo error($error);
			}
} ?>			  
			<div class="col-md-12 grid-margin stretch-card">
              <div class="card">
			                                <div class="card text-white bg-primary">
                                <div class="card-header text-center">Anvalaible plans</div>
	                            </div>
                 <table class="table table-hover">
                                        <tr>
                        <th>Name</th>
                        <th>Max Boot Time</th>
                        <th>Price</th>
                        <th>Length</th>
                        <th>Concurrents</th>
                        <th>Private</th>
						<th>Premium Methods</th>
                  </tr>
                </thead>
                <tbody>
				<form method="post">
<?php
$SQLSelect = $odb -> query("SELECT * FROM `plans` ORDER BY `price` ASC");
while ($show = $SQLSelect -> fetch(PDO::FETCH_ASSOC))
{
	$unit = $show['unit'];
	$length = $show['length'];
	$price = $show['price'];
	$concurrents = $show['concurrents'];
	if ($show['premium'] == 0) { $premium = 'No'; } else { $premium = 'Yes'; }
	$planName = $show['name'];
	$mbtShow = $show['mbt'];
	$id = $show['ID'];
	if ($show['private'] == 0) { $private = 'No'; } else { $private = 'Yes'; }
	echo '<tr><td><a href="plan.php?id='.$id.'">'.htmlspecialchars($planName).'</a></td><td><center>'.$mbtShow.' Seconds</center></td><td><center>$'.htmlentities($price).'</center></td><td><center>'.htmlentities($length).' '.htmlentities($unit).'</center></td><td><center>'.htmlentities($concurrents).'</center></td><td><center>'.htmlentities($private).'</center></td><td><center>'.$premium.'</center></td></tr>';
}

?>
</form>
   </table>
                                        </div>
</div>											
			<div class="col-md-12 grid-margin stretch-card">
              <div class="card">
			                                <div class="card text-white bg-primary">
                                <div class="card-header text-center">Manager Plan</div>
	                            </div>
								<form method="post">
                                    <div class="row-form">
                                        <div class="col-md-4"><strong>Name:</strong></div>
                                        <div class="col-md-12"><input type="text" class="form-control" name="name"/></div>
                                    </div>
                                    <div class="row-form">
                                        <div class="col-md-4"><strong>Price:</strong></div>
                                        <div class="col-md-12"><input type="text" class="form-control" name="price"/></div>
                                    </div>
									                                   <div class="row-form">
                                        <div class="col-md-4"><strong>Premium:</strong></div>
                                        <div class="col-md-12">
                                            <select name="premium" class="form-control">
                                                <option value="1">Yes</option>
												<option value="0">No</option>
                                            </select>
                                        </div>
                                    </div>
									
                                    <div class="row-form">
                                        <div class="col-md-4"><strong>Max Boot Time:</strong></div>
                                        <div class="col-md-12"><input type="text" class="form-control" name="mbt"/></div>
                                    </div>
                                    <div class="row-form">
                                        <div class="col-md-4"><strong>Concurrent Attacks:</strong></div>
                                        <div class="col-md-12"><input type="text" class="form-control" name="concurrents"/></div>
                                    </div>
                                    <div class="row-form">
                                        <div class="col-md-4"><strong>Membership Length:</strong></div>
                                        <div class="col-md-12"><input type="text" class="form-control" name="length"/></div>
                                    </div>
                                    <div class="row-form">
                                        <div class="col-md-4"><strong>Unit:</strong></div>
                                        <div class="col-md-12">
                                            <select name="unit" class="form-control">
                                                <option value="days">Days</option>
												<option value="weeks">Weeks</option>
                                                <option value="months">Months</option>
												<option value="years">Years</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="row-form">
                                        <div class="col-md-4"><strong>Private:</strong></div>
                                        <div class="col-md-12">
                                            <select name="private" class="form-control">
                                                <option value="1">Yes</option>
												<option value="0">No</option>
                                            </select>
                                        </div>
                                    </div>
									<div class="card-header">
									<div  class="col-xs-4 text-center" >
                                     <button name="addplan" class="btn btn-primary" >Update</button>
                                </div> 
							  </div>
									</form>
                                </div>