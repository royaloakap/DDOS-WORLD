<?php
include("header.php");
if (!($user -> isAdmin($odb)))
{
	header('location: ../index.php');
	die();
}
?>
<!DOCTYPE html>
<meta http-equiv="content-type" content="text/html;charset=UTF-12" />								
  <script src="../assets/js/spinner.js"></script>
			 <div class="page-wrapper">
			  <div class="page-content">
			  <?php include("fuctions/pinit.php"); ?>				  
			<div class="col-md-12 grid-margin stretch-card">
              <div class="card"> 		  
                               <div class="row">
  <div class="col-md-12 grid-margin stretch-card">
    <div class="card">
      <div class="card-body">
		<h6 class="alert alert-fill-primary">User Manager</h6>
		<div class="card">
      <div class="card-body">
	  <div class="row">
		<div class="col-lg-3">
<div class="form-group">
<input type="text" class="form-control" id="searchuser" name="searchuser" value="<?= htmlentities($_GET['username']); ?>" placeholder="User ID" style="    background-color: #1e242a;
    border: 1px solid #42a5f5;
    color: #FFF;">
</div>
 </div>
 	  <div class="col-lg-2">
	  <button type="button" onclick="window.location = 'user.php?id=' + document.getElementById('searchuser').value;" class="btn btn-square btn-outline-primary min-width-125 mb-10"><i class="fa fa-search"></i> Search</button>	
</div>
 </div>
  </div>
    </div>
  
        <div class="table-responsive">
          <table id="dataTableExample" class="table">
                                        <thead>
                                            <tr>
                                                <th>ID</th>
                                                <th>Username</th>
                                                <th>Email</th>
                                                <th>Rank</th>
                                                <th>Membership</th>                                    
                                            </tr>
                                        </thead>
                                        <tbody>
<?php
$SQLGetUsers = $odb -> query("SELECT * FROM `users` ORDER BY `ID` DESC");
while ($getInfo = $SQLGetUsers -> fetch(PDO::FETCH_ASSOC))
{
	$id = $getInfo['ID'];
	$user = $getInfo['username'];
	$email = $getInfo['email'];
	if ($getInfo['expire']>time()) {$plan = $odb -> query("SELECT `plans`.`name` FROM `users`, `plans` WHERE `plans`.`ID` = `users`.`membership` AND `users`.`ID` = '$id'") -> fetchColumn(0);} else {$plan='No membership';}
	$rank = $getInfo['rank'];
		if ($rank == 1)
		{
			$rank = 'Admin';
		}
		elseif ($rank == 2)
		{
			$rank = 'Supporter';
		}
		else
		{
			$rank = 'Member';
		}
	echo '<tr><td>'.htmlspecialchars($id).'</td><td><a href="user.php?id='.$id.'">'.htmlspecialchars($user).'</a></td><td>'.htmlspecialchars($email).'</td><td>'.$rank.'</td><td>'.htmlspecialchars($plan).'</td></tr>';
}
?>											
                                        </tbody>
                                    </table>                                                                        
                                </div>
                            </div>