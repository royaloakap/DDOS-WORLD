<?php
//Variables de la page
$p="404";
$pageTitle="Erreur 404";
//End
require_once("../require.php");



include("../include/header.php");
?>

<div class="container mt-3">
	<div class="row">
		<div class='col-sm-12'>
			<div class='card shadow bg-transparent'>
				<div class='card-header text-center bg-stressing'>
					<h4 class='text-white'>Error 404</h4>
				</div>
				<div class='card-body text-center bg-white'>
					The page you are trying to load does not exist.
				</div>
			</div>
		</div>
	</div>
</div>

<?php include("../include/footer.php"); ?>