<?php

	/// Require the header that already contains the sidebar and top of the website and head body tags
	$page = "Manage Users";
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
	  <?php
		if(isset($done)){
			echo success($done);
		}
		?>
      <div class="row">
	
	     <div class="col-md-12 col-sm-12 col-xs-12">
          <div class="white-box">
            <h3 class="box-title">Manage Users</h3>
				<table id="myTable" class="table table-striped">
							<thead>
								<tr>
									<th class="text-center" style="font-size: 12px;"></th>
									<th style="font-size: 12px;">Name</th>
									<th style="font-size: 12px;">Email</th>
									<th style="font-size: 12px;">2Auth Status</th>
									<th style="font-size: 12px;">Rank</th>
									<th style="font-size: 12px;">Membership</th>
								</tr>
							</thead>
							<tbody style="font-size: 12px;">
							<?php
							$SQLGetUsers = $odb -> query("SELECT * FROM `users` ORDER BY `ID` DESC");
							while ($getInfo = $SQLGetUsers -> fetch(PDO::FETCH_ASSOC)){
								$id = $getInfo['ID'];
								$user = $getInfo['username'];
								$email = $getInfo['email'];
								$twoAuth2 = $getInfo['2auth'];
								if ($getInfo['expire']>time()) {$plan = $odb -> query("SELECT `plans`.`name` FROM `users`, `plans` WHERE `plans`.`ID` = `users`.`membership` AND `users`.`ID` = '$id'") -> fetchColumn(0);} else {$plan='No membership';}
								$rank = $getInfo['rank'];
								$membership = $getInfo['membership'];
								
								$status = $getInfo['status'];	
								$expire = $getInfo['expire'];
									if ($rank == 1)
									{
										$rank = 'Administrator';
									}
									elseif ($rank == 2)
									{
										$rank = 'Supporter';
									}
									else
									{
										$rank = 'Member';
									}
									
									if($twoAuth2 == 1) {$status2 = "Enabled"; } else { $status2 = "Disabled"; }
									
																	echo '<tr>
										<td></td>
										<td><a class="link-effect" href="user.php?id='.$id.'">'.htmlspecialchars($user).'</a></td>
										<td>'.htmlspecialchars($email).'</td>
										<td>'.$status2.'</td>
										<td>'.$rank.'</td>
										<td>'.htmlspecialchars($plan).'</td>
									  </tr>';
							}
							?>	
							</tbody>
						</table>
				

          </div>
        </div>
      </div>
	  
      <!--/.row -->
	  <script>
    $(document).ready(function(){
      $('#myTable').DataTable();
      $(document).ready(function() {
        var table = $('#example').DataTable({
          "columnDefs": [
          { "visible": false, "targets": 2 }
          ],
          "order": [[ 2, 'asc' ]],
          "displayLength": 25,
          "drawCallback": function ( settings ) {
            var api = this.api();
            var rows = api.rows( {page:'current'} ).nodes();
            var last=null;

            api.column(2, {page:'current'} ).data().each( function ( group, i ) {
              if ( last !== group ) {
                $(rows).eq( i ).before(
                  '<tr class="group"><td colspan="5">'+group+'</td></tr>'
                  );

                last = group;
              }
            } );
          }
        } );

    // Order by the grouping
    $('#example tbody').on( 'click', 'tr.group', function () {
      var currentOrder = table.order()[0];
      if ( currentOrder[0] === 2 && currentOrder[1] === 'asc' ) {
        table.order( [ 2, 'desc' ] ).draw();
      }
      else {
        table.order( [ 2, 'asc' ] ).draw();
      }
    } );
  } );
    });
  </script>
<?php

	require_once 'footer.php';
	
?>