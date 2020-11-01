<?php
	session_start();

	require_once "admin_config.php";

	// Check user login or not
	if(!isset($_SESSION['adminlogin'])){
		header('Location: index.php');
	} else {
	
			if(isset($_GET['cat'])){
				$shownCat = $_GET['cat'];
				$cId = $cName = $cDesc = $cImg = '';
				
				$sql = "SELECT * FROM category WHERE catName=?";
				if($stmt = mysqli_prepare($con, $sql)){
					mysqli_stmt_bind_param($stmt, "s", $shownCat);
					mysqli_stmt_execute($stmt);
					mysqli_stmt_store_result($stmt);
					mysqli_stmt_bind_result($stmt, $cId, $cName, $cDesc, $cImgId);
					mysqli_stmt_fetch($stmt);
				}
				
				$sql = "SELECT url, thumbUrl FROM images WHERE id=".$cImgId;
				if($stmtt = mysqli_prepare($con, $sql)){
					if(mysqli_stmt_execute($stmtt)){
						mysqli_stmt_store_result($stmtt);
						mysqli_stmt_bind_result($stmtt, $cImg, $cThumb);
						if(mysqli_stmt_fetch($stmtt)){
							//custom action can be added later if needed
						}else{
							$cImg=$cThumb='../img/default.png';
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
					
					//Update the category entry
					$sql = "UPDATE category SET catName=?, description=?, catImgSrc=? WHERE catName=?";
					if($stmt = mysqli_prepare($con, $sql)){
						mysqli_stmt_bind_param($stmt, "ssss", $newName, $newDesc, $newImgSrc, $oldName);
						mysqli_stmt_execute($stmt);
					}
					
					//Update products in category
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
				<h2>Category management</h2><hr>
				<a href="admin_products.php" class="w3-button w3-light-grey">Back to categories</a>
				<span id='editCatSpan'><a class='w3-button w3-light-grey' onclick='editCat()' id='editCat_btn'>Edit</a></span>
				<br><br>
				<?php 
				echo "<div class='w3-responsive w3-card'><table class='w3-table w3-bordered'>";
				echo "<tr><td style='width: 20%'>Name:</td><td id='catNameTd' colspan='2'>".$cName."</td></tr>";
				echo "<tr><td style='width: 20%'>Description:</td><td id='catDescTd' colspan='2'>".$cDesc."</td></tr>";
				echo "<tr><td style='width: 20%;vertical-align: middle''>Image:</td><td style='vertical-align: middle'><a target='_blank' href='".$cImg."'><img src='".$cThumb."' alt='' class='imgThumbnail' id='thmbImg'></a></td><td style='vertical-align: middle' id='selImg'></td></tr>";
				echo "</table></div><hr>";
				echo "<input type='text' name='imgId' id='imgId' style='visibility: hidden' value='".$cImgId."'>";
				?>
				
				
				<h3><?php echo $cName; ?> products</h3><br>
				<a href="admin_add_product.php?cat=<?php echo $cName; ?>" class="w3-button w3-light-grey">Add product</a>
				<a href="" class="w3-button w3-light-grey" onclick="deleteProducts()">Delete selected</a><br><br>
				<?php
				if(isset($_GET['cat'])){
					$sql = "SELECT * FROM products WHERE ProductType=?";
					if($stmt = mysqli_prepare($con, $sql)){
						mysqli_stmt_bind_param($stmt, "s", $shownCat);
						mysqli_stmt_execute($stmt);
						mysqli_stmt_store_result($stmt);
						for( $i = 0; $i < mysqli_stmt_num_rows($stmt); $i++ ){   
							mysqli_stmt_bind_result($stmt, $pId, $pName, $pType, $pDesc, $pStock, $pPrice, $pImg);
							if(mysqli_stmt_fetch($stmt)){
								
								$sql = "SELECT url, thumbUrl FROM images WHERE id=?";
								if($stmtt = mysqli_prepare($con, $sql)){
									mysqli_stmt_bind_param($stmtt, "s", $pImg);
									if(mysqli_stmt_execute($stmtt)){
										mysqli_stmt_store_result($stmtt);
										mysqli_stmt_bind_result($stmtt, $pImgUrl, $pThumbUrl);
										if(mysqli_stmt_fetch($stmtt)){
											//custom action can be added later if needed
										}else{
											$pImgUrl=$pThumbUrl='../img/default.png';
										}
									} else {
										echo "Error";//custom action can be added later if needed
									}
								} else {
									echo "Error";//custom action can be added later if needed
								}
								
								echo "<div class='w3-card-4'>";
									echo "<header class='w3-container w3-grey w3-padding'>";
									echo "<span class='w3-mobile category_chkBox'><input type='checkbox' class='w3-check' id='".$pId."' name='chkBox' /></span>";
									echo "<span class='w3-mobile w3-large category_title'>".$pName."</span><button onclick='manageProduct(\"".$shownCat."\",\"".$pId."\")'>Manage</button><br>
												</header>
												<div class='w3-container'>
													<img src='".$pThumbUrl."' class='w3-image w3-hide-small category_ImgThumbnail'>
													<p>".$pDesc."</p>";
								
									if($pStock==0){
										echo "<p class='w3-red w3-third w3-border w3-center'>Stock: ".$pStock."</p>";
									} else {
										echo "<p class='w3-blue-grey w3-third w3-border w3-center'>Stock: ".$pStock."</p>";
									}
									echo "<p class='w3-blue-grey w3-third w3-border w3-center'>Price: ".$pPrice."</p></div></div><br>";
							}
						}
					}
					
				}
				?>
				
				<!-- Hidden image selector window -->
				<div style='position:absolute;left:200px;width:100%;height:100%;background-color:#888;opacity: 0.5;visibility: hidden'  id='editPup'></div>
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
		var currName = '<?php echo $cName; ?>';
		var currDesc = '<?php echo $cDesc; ?>';
		var currImgId = document.getElementById('imgId').value;
		var newImgId = currImgId;
		
		function editCat(){
			if(document.getElementById('editCat_btn').innerHTML!="Save"){
				
				document.getElementById('catNameTd').innerHTML="<input type='text' id='nName_tbox' style='border: 1px solid #ddd' value='"+currName+"'/>";
				document.getElementById('catDescTd').innerHTML="<textarea id='nDesc_txt' style='border: 1px solid #ddd;width: 100%' >"+currDesc+"</textarea>";
				document.getElementById('editCat_btn').innerHTML="Save";
				document.getElementById('editCatSpan').innerHTML+="&nbsp;<a class='w3-button w3-light-grey' onclick='editCatCancel()' id='editCat_btn_cancl'>Cancel</a>";
				document.getElementById('selImg').innerHTML="<a class='w3-button w3-light-grey' id='selectImgBtn' onclick='selImg(\"show\")'>Select from Media library</a>";
				
			} else {
				var newName = document.getElementById('nName_tbox').value;
				var newDesc = document.getElementById('nDesc_txt').value;
				
				if( (currName!=newName) || (currDesc!=newDesc) || (currImgId!=newImgId) ){
					if( confirm("Do you really want to save changes?") ){
						var xhttp = new XMLHttpRequest();
						xhttp.onreadystatechange = function() {
										if (xhttp.readyState == 4 && xhttp.status == 200) {
											window.location.href = "admin_category_management.php?cat="+newName;
										}
									}
									xhttp.open("GET","admin_category_management.php?oldCatName="+currName+"&catNameUpd="+newName+"&catDesc="+newDesc+"&catImg="+newImgId, true);						
									xhttp.send();
					} else { editCatCancel(); }
				} else { editCatCancel(); }
				
			}
		}
		
		function editCatCancel(){
			document.getElementById('catNameTd').innerHTML=currName;
			document.getElementById('catDescTd').innerHTML=currDesc;
			document.getElementById('editCat_btn').innerHTML="Edit";
			document.getElementById('editCat_btn_cancl').remove();
			document.getElementById('selectImgBtn').remove();
		}
		
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
		
		function manageProduct(catName, prodID){
			window.location.href = "admin_product_management.php?cat="+catName+"&prod="+prodID;
		}
		
	</script>
	</body>
</html>