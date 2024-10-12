<?php

header("X-XSS-Protection: 1; mode=block");

	/// Require the header that already contains the sidebar and top of the website and head body tags
	require_once 'header.php'; 
	
	
?>


<title><?php echo $sitename; ?> | Users</title>
		<div class="page-wrapper" style="display: block;">
			<div class="page-breadcrumb">
				<div class="d-flex align-items-center">
					<h4 class="page-title text-truncate text-white font-weight-medium mb-0">Users</h4>
					<div class="ml-auto">
						<nav aria-label="breadcrumb">
							<ol class="breadcrumb m-0 p-0">
								<li class="breadcrumb-item text-sql" aria-current="page"><?php echo $sitename; ?></li>
								<li class="breadcrumb-item text-muted" aria-current="page">Users</li>
							</ol>
						</nav>
					</div>
				</div>
			</div>
      </ul>


  <!-- Page Content -->
                 <div class="container-fluid">
                    <div class="row">
                        <div class="col-md-6 col-lg-12">
	  <?php
		if(isset($done)){
			echo success($done);
		}
		?>
			</div>
				<table id="myTable" class="table table-striped">
							<thead>
								<tr>
									<th class="text-center" style="font-size: 12px;"></th>
									<th style="font-size: 12px;">Name</th>
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
									
											echo '<tr>
										<td></td>
										<td><a class="link-effect" href="user.php?id='.$id.'">'.htmlspecialchars($user).'</a></td>
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