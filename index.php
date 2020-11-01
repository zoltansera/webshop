<?php	require_once "config.php" ?>
<!DOCTYPE html>
<html>
	<head>
		<?php addHead(); ?>
		<style>
			.mySlides {display:none}
		</style>
	</head>
	<body class="fadein">
		<?php addMenu(); ?>	
		<div class="w3-content w3-section" style="width: 80%">
				<img class="mySlides w3-animate-left" src="https://pafranyszal.eu/wp-content/uploads/2020/02/landing_disz_collage.jpg" style="width:100%">
				<img class="mySlides w3-animate-left" src="https://pafranyszal.eu/wp-content/uploads/2020/02/landing_ekszer_collage.jpg" style="width:100%">
		</div>
				
		<footer class="footer"><?php include 'footer.php';?></footer>
	</body>
	<script src="js/slideshow.js"></script>
</html>