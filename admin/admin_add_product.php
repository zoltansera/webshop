<?php
	session_start();

	require_once "admin_config.php";

	// Check user login or not
	if(!isset($_SESSION['adminlogin'])){
		header('Location: index.php');
	}
	
	if(isset($_GET['cat'])){
		$category = $_GET['cat'];
	} else {
		$category = 'Egyéb';
	}
	// Define variables and initialize with empty values
	$prodname = $prodimgid = $proddesc = $price = "";
	$prodname_err = $prodimgid_err = $price_err = $submit_err = "";
	
	
	// Processing form data when form is submitted
	if($_SERVER["REQUEST_METHOD"] == "POST"){
    // Validate category name
		if(empty(trim($_POST["prodname"]))){
			$prodname_err = "Please name your product.";
		} else{
			$prodname = trim($_POST["prodname"]);
			$proddesc = trim($_POST["proddesc"]);
			$prodimgid = trim($_POST["prodimgid"]);
			$stock = trim($_POST["amount"]);
			$price = trim($_POST["prodprice"]);
			
			if ( !empty($catname_err) || !empty($catimgid_err)) {
				$submit_err = "Category creation was unsuccessful.";
			} else {
				// Prepare an insert statement
				$sql = "INSERT INTO products (ProductName, ProductType, Description, Stock, Price, ImageSource) VALUES ('".$prodname."', '".$category."', '".$proddesc."', '".$stock."', '".$price."', '".$prodimgid."')";
				if($stmt = mysqli_prepare($con, $sql)){
					if(mysqli_stmt_execute($stmt)){
						mysqli_stmt_close($stmt);
						header("location: admin_category_management.php?cat=".$category);
					}
				} else { echo "DB conn err";}
			}	
		}
	}
	
	
	
	
?>
<!DOCTYPE html>
<html>
	<head>
		<?php addHead(); ?>
		<style>
			.imgThumbnail{
				border: 1px solid #ddd; /* Gray border */
				border-radius: 4px;  /* Rounded border */
				padding: 5px; /* Some padding */
				width: 150px; /* Set a small width */
			}
			.imgThumbnail:hover {
				box-shadow: 0 0 2px 1px rgba(0, 140, 186, 0.5);
			}
		</style>
	</head>
	<body>
		<?php addMenu(1); ?>
		<div class="w3-padding" style="margin-left: 200px">
			<div class="w3-container">
				<h2>New product</h2>
			</div>
			
			<div class="w3-container">
				<hr>
				<div class="w3-card-4">
					<div class="w3-container w3-blue-grey">
						<h2>Product properties</h2>
					</div>
					<form class="w3-container" action="" method="post" enctype="multipart/form-data">
						<br>
						<label>Product name</label>
						<input class="w3-input w3-border" type="text" name="prodname" value="" placeholder="Product name">
						<span style="color: red"><?php echo $prodname_err; ?></span><br>
						
						<label>Product description</label>
						<textarea class="w3-input w3-border" name="proddesc" value="" placeholder="Product description" style="height: 100px"></textarea><br>
						
						<label>Product image</label><br>
						<a onclick="mediaLibOprtn('show')"><img alt='DUMMY' class='imgThumbnail' src="../img/default.png" name="kep" id="kep"></a>
						<input type="text" name="prodimgid" id="prodimgid" style="visibility: hidden"><br><br>
						
						<label>Stock</label>
						<input class="w3-input w3-border" type="text" id="amount" name="amount" style="width: 50px" value="1">
						<br>
						
						<label>Price</label>
						<input class="w3-input w3-border" type="text" name="prodprice" value="" placeholder="" style="width: 100px">
						<hr><input type="submit" class="w3-button w3-light-grey" value="Create">
						<span style="color: red"><?php echo $submit_err; ?></span>
						&nbsp;&nbsp;&nbsp;<a href="<?php if(isset($_GET['cat'])){ $category = $_GET['cat']; echo 'admin_category_management.php?cat='.$category; } else { echo 'admin_products.php'; } ?>" class="w3-button w3-light-grey">Cancel</a>
						<br><br>
					</form>
				</div> 
			</div>
			
			<div style='position:absolute;top:0px;left:200px;width:100%;height:100%;background-color: #888;opacity: 0.5;visibility: hidden' id='addPupBg'></div>
			<div class='w3-card-4 w3-display-middle w3-center w3-mobile' style='height: 80%;overflow: scroll;position:absolte;visibility: hidden'  id='addPup'>
				<header class="w3-container w3-blue-grey"><h4>Select image from media library<span class="w3-right w3-red"><a style="cursor: pointer;margin-left: 10px;margin-right: 10px" onclick="mediaLibOprtn('hide')">X</a></span></h4></header>
				<div class="w3-container w3-white">
				<?php 
					$sql = "SELECT * FROM images";
					if($stmt = mysqli_prepare($con, $sql)){
						// Attempt to execute the prepared statement
						if(mysqli_stmt_execute($stmt)){
							/* store result */
							mysqli_stmt_store_result($stmt);
							for($i=0;$i<mysqli_stmt_num_rows($stmt);$i++){
								mysqli_stmt_bind_result($stmt, $iId, $iTitle, $iAlt, $iUrl, $iThumb);
								mysqli_stmt_fetch($stmt);
								echo '<div class="w3-mobile w3-card-4" style="width: 220px; float: left;margin: 10px">';
								echo '<img src="'.$iThumb.'" class="w3-image" style="width: 100%;height: 160px;object-fit: cover"><div class="w3-container w3-center w3-blue-grey w3-border-top"><h6>'.$iTitle.'</h6><button class="w3-button w3-light-grey" onclick="selectImg('.$iId.',\''.$iThumb.'\')">Select</button><br><br></div></div>';
							} 
						} else{
							echo "Oops! Something went wrong. Please try again later.";
						}
						// Close statement
						mysqli_stmt_close($stmt);
					}
				?>
				</div>
			</div>
		</div>
		
		<script>
			function mediaLibOprtn(oprtn){
				switch(oprtn){
					case 'show':
						document.getElementById('addPupBg').style.visibility='visible';
						document.getElementById('addPup').style.visibility='visible';
						break;
					case 'hide':
						document.getElementById('addPupBg').style.visibility='hidden';
						document.getElementById('addPup').style.visibility='hidden';
						break;
				}
			}
			
			function selectImg(ID, URL){
				mediaLibOprtn('hide');
				document.getElementById('kep').src=URL;
				document.getElementById('prodimgid').value=ID;
			}
			
		</script>
	</body>
</html>