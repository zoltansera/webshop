<?php
	session_start();

	require_once "admin_config.php";

	// Check user login or not
	if(!isset($_SESSION['adminlogin'])){
		header('Location: index.php');
	}

	// Define variables and initialize with empty values
	$catname = $catimgid = $catdesc = "";
	$catname_err = $catimgid_err = $submit_err = "";
	
	
	// Processing form data when form is submitted
	if($_SERVER["REQUEST_METHOD"] == "POST"){
    // Validate category name
		if(empty(trim($_POST["catname"]))){
			$catname_err = "Please add category name.";
		} else{
			// Prepare a select statement
			$sql = "SELECT catName FROM category WHERE catName = ?";
			if($stmt = mysqli_prepare($con, $sql)){
				// Bind variables to the prepared statement as parameters
				mysqli_stmt_bind_param($stmt, "s", $param_catname);
				// Set parameters
				$param_catname = trim($_POST["catname"]);
				// Attempt to execute the prepared statement
				if(mysqli_stmt_execute($stmt)){
					/* store result */
					mysqli_stmt_store_result($stmt);
					if(mysqli_stmt_num_rows($stmt) == 1){
						$catname_err = "This category name already exists.";
					} else{
						$catname = trim($_POST["catname"]);
					}
				} else{
					echo "Oops! Something went wrong. Please try again later.";
				}
				// Close statement
				mysqli_stmt_close($stmt);
			}
		}
		//get description
		$catdesc = trim($_POST["catdesc"]);
		$catimgid = trim($_POST["catimgid"]);
		
		if ( !empty($catname_err) || !empty($catimgid_err)) {
			$submit_err = "Category creation was unsuccessful.";
		} else {
			// Prepare an insert statement
			$sql = "INSERT INTO category (catName, description, catImgSrc) VALUES ('".$catname."', '".$catdesc."', '".$catimgid."')";
			if($stmt = mysqli_prepare($con, $sql)){
				if(mysqli_stmt_execute($stmt)){
					mysqli_stmt_close($stmt);
					header("location: admin_products.php");
				}
			} else { echo "DB conn err";}
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
				<h2>New product category</h2><br>
				<a href="admin_products.php" class="w3-button w3-light-grey">Cancel</a>
			</div>
			
			<div class="w3-container">
				<hr>
				<form action="" method="post" enctype="multipart/form-data">
				<div>
					<span style="width: 200px;float: left">Category name:</span><input type="text" name="catname" value="" placeholder="Category name">
					<span style="color: red"><?php echo $catname_err; ?></span><br><br>
					<span style="width: 200px;float: left">Category description:</span><textarea name="catdesc" value="" placeholder="Category description" style="width: 410px;height: 100px"></textarea><br><br>
					<span style="width: 200px;float: left">Category image:</span>
					<a onclick="mediaLibOprtn('show')"><img alt='DUMMY' class='imgThumbnail' src="../img/default.png" name="kep" id="kep"></a>
					<input type="text" name="catimgid" id="catimgid" style="visibility: hidden">
					<br>
				</div> 
				<hr><input type="submit" class="w3-button w3-light-grey" value="Create">
				<span style="color: red"><?php echo $submit_err; ?></span>
				</form>
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
				document.getElementById('catimgid').value=ID;
			}
		</script>
	</body>
</html>