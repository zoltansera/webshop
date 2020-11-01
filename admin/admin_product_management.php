<?php
	session_start();

	require_once "admin_config.php";

	$prodname_err = $submit_err = '';
	
	// Check user login or not
	if(!isset($_SESSION['adminlogin'])){
		header('Location: index.php');
	} else {
	
			if(isset($_GET['cat']) && isset($_GET['prod'])){
				$shownCat = $_GET['cat'];
				$shownProdID = $_GET['prod'];
				
				$sql = "SELECT * FROM products WHERE ProductID=?";
				if($stmt = mysqli_prepare($con, $sql)){
					mysqli_stmt_bind_param($stmt, "s", $shownProdID);
					mysqli_stmt_execute($stmt);
					mysqli_stmt_store_result($stmt);
					mysqli_stmt_bind_result($stmt, $pId, $pName, $pType, $pDesc, $pStock, $pPrice, $pImgId);
					mysqli_stmt_fetch($stmt);
				}
				
				$sql = "SELECT url, thumbUrl FROM images WHERE id=".$pImgId;
				if($stmtt = mysqli_prepare($con, $sql)){
					if(mysqli_stmt_execute($stmtt)){
						mysqli_stmt_store_result($stmtt);
						mysqli_stmt_bind_result($stmtt, $pImg, $pThumb);
						if(mysqli_stmt_fetch($stmtt)){
							//custom action can be added later if needed
						}else{
							$pImg=$pThumb='../img/default.png';
						}
					} else {
						//custom action can be added later if needed
					}
				}
				
				
			}
			
			if(isset($_GET['oldCatName']) && isset($_GET['catNameUpd']) && isset($_GET['catDesc']) && isset($_GET['catImg'])){
				if( $_GET['oldCatName']!='' && $_GET['catNameUpd']!='' && $_GET['catDesc']!='' && $_GET['catImg']!='' ){
					$oldName = $_GET['oldCatName'];
					$newName = $_GET['catNameUpd'];
					$newDesc = $_GET['catDesc'];
					$newImgSrc = $_GET['catImg'];
					
					//Update product
					$sql = "UPDATE products SET ProductType=? WHERE ProductType=?";
					if($stmt = mysqli_prepare($con, $sql)){
						mysqli_stmt_bind_param($stmt, "ss", $newName, $oldName);
						mysqli_stmt_execute($stmt);
					}
				}
				
			}
	
			function showLib(){
				global $con;
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
							echo '<img src="'.$iThumb.'" class="w3-image" style="width: 100%;height: 160px;object-fit: cover"><div class="w3-container w3-center w3-blue-grey w3-border-top"><h6>'.$iTitle.'</h6><button class="w3-button w3-light-grey" onclick="selImg(\'select\',\''.$iId.'\',\''.$iThumb.'\')">Select</button><br><br></div></div>';
						} 
					} else{
						echo "Oops! Something went wrong. Please try again later.";
					}
					// Close statement
					mysqli_stmt_close($stmt);
				}
			}
		
			//Delete product
			if(isset($_GET['del'])){
				$id = $_GET['del'];
				$sql = "DELETE FROM products WHERE ProductID=".$id;
				if($stmt = mysqli_prepare($con, $sql)){
					mysqli_stmt_execute($stmt);
				}
				$stmt->close();
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

		<div class="w3-padding content">
			<div class="w3-container">
			<div style='position:absolute;left:200px;width:100%;height:100%;background-color:#888;opacity: 0.5;visibility: hidden' id='editPup'></div>
				<h2>Product management</h2><hr>
				<div class="w3-card-4">
					<div class="w3-container w3-blue-grey">
						<h4>Product properties</h4>
					</div>
					<form class="w3-container" action="" method="post" enctype="multipart/form-data">
						<br>
						<label>Product name</label>
						<input class="w3-input w3-border" type="text" name="prodname" value="<?php echo $pName;?>" placeholder="Product name">
						<span style="color: red"><?php echo $prodname_err; ?></span><br>
						
						<label>Product description</label>
						<textarea class="w3-input w3-border" name="proddesc" placeholder="Product description" style="height: 50px"><?php echo $pDesc;?></textarea><br>
						
						<label>Product image</label><br>
						<a onclick="selImg('show')"><img alt='' class='imgThumbnail' src="<?php echo $pThumb; ?>" name="kep" id="kep"></a>
						<input type="text" name="prodimgid" id="prodimgid" style="visibility: hidden"><br><br>
						
						<label>Stock</label>
						<input class="w3-input w3-border" type="text" id="amount" name="amount" style="width: 50px" value="<?php echo $pStock;?>">
						<br>
						
						<label>Price</label>
						<input class="w3-input w3-border" type="text" name="prodprice" value="<?php echo $pPrice;?>" placeholder="" style="width: 100px;">
						<hr><input type="submit" class="w3-button w3-light-grey" value="Save">
						<span style="color: red"><?php echo $submit_err; ?></span>
						&nbsp;&nbsp;&nbsp;<a href="admin_category_management.php?cat=<?php echo $shownCat; ?>" class="w3-button w3-light-grey">Cancel</a>
						<br><br>
					</form>
				</div> 
				
				
				<!-- Hidden image selector window -->
				
				<div class='w3-card-4 w3-display-middle w3-center w3-mobile' style='position:absolute;height: 80%;overflow: scroll;visibility:hidden' id='editPup_window'>
					<header class="w3-container w3-blue-grey"><h4>Select image from media library<span class="w3-right w3-red"><a style="cursor: pointer;margin-left: 10px;margin-right: 10px" onclick="selImg('hide')">X</a></span></h4></header>
					<div class="w3-container w3-white">
					<?php showLib(); ?>
					</div>
				</div>
				</div>

			
			<div class="w3-container">
			<br>
			
			</div>
			
		</div>
		
	<script>
		
		function selImg(p_oprtn,p_imgId,p_thumb){
			switch(p_oprtn){
				case "show":
					document.getElementById('editPup').style.visibility='visible';
					document.getElementById('editPup_window').style.visibility='visible';
					break;
				case "hide":
					document.getElementById('editPup').style.visibility='hidden';
					document.getElementById('editPup_window').style.visibility='hidden';
					break;
				case "select":
					document.getElementById('thmbImg').src = p_thumb;
					newImgId = p_imgId;
					selImg('hide');
					break;
			}
			
			
		}
		
		function deleteProducts(){
			var chkBoxes = document.getElementsByName("chkBox");
				var chkdArr = [];
						
				for (var i=0;i<chkBoxes.length;i++){
					if(chkBoxes[i].checked){
						chkdArr.push(chkBoxes[i].id);
					}
				}
				
				if(chkdArr.length==0){
					alert("Nothing was selected");
				} else {
					if(confirm("Do you really want to delete selected products?")){
						for (var i=0;i<chkdArr.length;i++){
							var xhttp = new XMLHttpRequest();
							xhttp.onreadystatechange = function() {
								if (xhttp.readyState == 4 && xhttp.status == 200) {
									location.reload();
								}
							}
							xhttp.open("GET","admin_category_management.php?del="+chkdArr[i], true);						
							xhttp.send();
							
						}
					} else {location.reload();}
				}
		}
		
		
	</script>
	</body>
</html>