<?php
	function addHead(){
		echo '<title>Webshop administration</title><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1">';
		echo '<link href="https://fonts.googleapis.com/css?family=Amiko" rel="stylesheet" type="text/css">';
		echo '<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">';
		echo '<link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">';
		echo '<link rel="stylesheet" href="admin_style.css">';
	}
	
	function addMenu($active){
		$selected = 'w3-grey';
		
		echo '<div class="w3-sidebar w3-light-grey w3-border-right w3-bar-block" style="width: 200px">';
		echo '<h3 class="w3-bar-item w3-blue-grey">Menu</h3>';
		echo '<a href="admin.php" class="w3-bar-item w3-button '.($active==0?$selected:"").'">Dashboard</a>';
		echo '<a href="admin_medialib.php" class="w3-bar-item w3-button '.($active==3?$selected:"").'"">Media library</a>';
		echo '<a href="admin_products.php" class="w3-bar-item w3-button '.($active==1?$selected:"").'"">Warehouse</a>';
		echo '<a href="#" class="w3-bar-item w3-button '.($active==2?$selected:"").'"">Settings</a>';
		echo '<a href="admin_logout.php" class="w3-bar-item w3-button">Log out</a>';
		echo '</div>';
	}
	

?>