<?php
	require_once "admin_config.php";

	// Check user login or not
	if(!isset($_SESSION['adminlogin'])){
		header('Location: index.php');
	} else {
		
		
		
		
		
	}

?>
<!DOCTYPE html>
<html>
	<head>
		<?php addHead(); ?>
	</head>
	<body>
		<?php addMenu(0); ?>
		
		<div class="w3-padding content">
			... page content ...
		</div>
		
		
	</body>
</html>