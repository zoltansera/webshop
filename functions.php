<?php
	include 'global.php';
	
	function addHead(){
		global $SHOPNAME;
		
		echo '<title>'.$SHOPNAME.'</title><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1">';
		echo '<link href="https://fonts.googleapis.com/css?family=Amiko" rel="stylesheet" type="text/css">';
		echo '<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">';
		echo '<link rel="stylesheet" href="style/style.css">';
		echo '<link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">';
	}
	
	function addMenu(){
		global $logopath, $MENU_ABOUT, $ABOUT_PAGE, $PRODUCTS_PAGE, $MENU_PRODUCTS, $CONTACT_PAGE, $MENU_CONTACT, $LOGIN_PAGE, $MENU_LOGIN, $HOME_PAGE, $MENU_ACCOUNT, $MENU_LOGOUT, $LOGOUT;
		echo '<header class="header">';
		echo '<a href="index.php" class="logo"><img src="'.$logopath.'" class="logoimg"></img></a>';
		echo '<input class="menu-btn" type="checkbox" id="menu-btn" />';
		echo '<label class="menu-icon" for="menu-btn"><span class="navicon"></span></label>';
		echo '<ul class="menu" id="dd">';
		echo '<li><a href="'.$ABOUT_PAGE.'">'.$MENU_ABOUT.'</a></li>';
		echo '<li><a href="'.$PRODUCTS_PAGE.'">'.$MENU_PRODUCTS.'</a></li>';
		echo '<li><a href="'.$CONTACT_PAGE.'">'.$MENU_CONTACT.'</a></li>';
		if(!isset($_SESSION['loggedin'])){
			echo '<li><a href="'.$LOGIN_PAGE.'">'.$MENU_LOGIN.'</a></li>';
		} else{
			echo '<li><a href="'.$HOME_PAGE.'">'.$MENU_ACCOUNT.'</a></li>';
			echo '<li><a href="'.$LOGOUT.'">'.$MENU_LOGOUT.'</a></li>';
		}
		echo '<li><a href="cart.php" class=""><i class="fa fa-shopping-cart" aria-hidden="true"></i>&nbsp;</a></li>';
		echo '</ul></header>';
		//echo '<div class="floatingCart"><a href="cart.php">KOSÁR</a></div>';
	}
	

?>