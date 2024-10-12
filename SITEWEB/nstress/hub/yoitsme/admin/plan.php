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
            <div class="page-content">

                <div class="container">
<?php
		if (isset($_POST['update']))
		{
			$updateName = $_POST['nameAdd'];
			$updateUnit = $_POST['unit'];
			$updateLength = $_POST['lengthAdd'];
			$updateMbt = intval($_POST['mbt']);
			$updatePrice = floatval($_POST['price']);
			$updateconcurrents = $_POST['concurrents'];
			$updateprivate = $_POST['private'];
			
			if (empty($updatePrice) || empty($updateName) || empty($updateUnit) || empty($updateLength) || empty($updateMbt) || empty($updateconcurrents))
			{
				$error = 'Fill in all fields';
			}
			if (empty($error))
			{
				$SQLinsert = $odb -> prepare("UPDATE `plans` SET `name` = :name, `mbt` = :mbt, `unit` = :unit, `length` = :length, `price` = :price, `concurrents` = :concurrents, `private` = :private WHERE `ID` = :id");
				$SQLinsert -> execute(array(':name' => $updateName, ':mbt' => $updateMbt, ':unit' => $updateUnit, ':length' => $updateLength, ':price' => $updatePrice, ':concurrents' => $updateconcurrents, ':private' => $updateprivate, ':id' => $_GET['id']));
				echo success('Plan has been updated');
				$currentName = $updateName;
				$currentUnit = $updateUnit;
				$currentMbt = $updateMbt;
				$currentPrice = $updatePrice;
				$currentLength = $updateLength;
				$currentconcurrents = $updateconcurrents;
				$currentprivate = $updateprivate;
			}
			else
			{
				echo error($error);
			}
		}
?>			                    <div class="page-toolbar">
                        
                        <div class="page-toolbar-block">
                            <div class="page-toolbar-title"><?php echo htmlspecialchars($currentName); ?></div>
                        </div>
                        
                        <ul class="breadcrumb">
                            <li><a href="index.php">Dashboard</a></li>
                            <li class="active">View Plan</li>
                        </ul>                        
                        
                    </div>                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="block">
                                <div class="block-content">
                                    <h2><strong>Plan</strong> Settings</h2>
                                </div>
                                <div class="block-content controls">
                                    <form method="post">
                                    <div class="row-form">
                                        <div class="col-md-4"><strong>Name:</strong></div>
                                        <div class="col-md-8"><input type="text" class="form-control" name="nameAdd" value="<?php echo htmlspecialchars($currentName); ?>"/></div>
                                    </div>
                                    <div class="row-form">
                                        <div class="col-md-4"><strong>Max Boot Time:</strong></div>
                                        <div class="col-md-8"><input type="text" class="form-control" name="mbt" value="<?php echo htmlspecialchars($currentMbt); ?>"/></div>
                                    </div>
                                    <div class="row-form">
                                        <div class="col-md-4"><strong>Concurrents:</strong></div>
                                        <div class="col-md-8"><input type="text" class="form-control" name="concurrents" value="<?php echo htmlspecialchars($currentconcurrents); ?>"/></div>
                                    </div>
                                    <div class="row-form">
                                        <div class="col-md-4"><strong>Price:</strong></div>
                                        <div class="col-md-8"><input type="text" class="form-control" name="price" value="<?php echo htmlspecialchars($currentPrice); ?>"/></div>
                                    </div>
                                    <div class="row-form">
                                        <div class="col-md-4"><strong>Membership Length:</strong></div>
                                        <div class="col-md-8"><input type="text" class="form-control" name="lengthAdd" value="<?php echo htmlspecialchars($currentLength); ?>"/></div>
                                    </div>
                                    <div class="row-form">
                                        <div class="col-md-4"><strong>Membership Unit:</strong></div>
                                        <div class="col-md-8">
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
                                        <div class="col-md-8">
                                            <select name="private" class="form-control">
                    <option value="1" <?php echo selectedPrivate(1, $currentprivate); ?>>Yes</option>
                    <option value="0" <?php echo selectedPrivate(0, $currentprivate); ?>>No</option>
                                            </select>
                                        </div>
                                    </div>
									<center><button name="update" class="btn btn-success">Update</button></center>
                                </div>
                                
                            </div>
                        </div>
                    </div>
                    
                </div>
                
            </div>
            <div class="page-sidebar"></div>
        </div>

    </body>
</html>