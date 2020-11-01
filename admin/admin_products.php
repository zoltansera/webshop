<?php
	session_start();

	require_once "admin_config.php";

	// Check user login or not
	if(!isset($_SESSION['adminlogin'])){
		header('Location: index.php');
	}

	//Delete category
	if(isset($_GET['del'])){
		$id = $_GET['del'];
		$cName = '';
		
		//Get category name
		$sql = "SELECT catName FROM category WHERE catID=?";
		if($stmt = mysqli_prepare($con, $sql)){
			mysqli_stmt_bind_param($stmt, "s", $id);
			mysqli_stmt_execute($stmt);
			mysqli_stmt_store_result($stmt);
			mysqli_stmt_bind_result($stmt, $cName);
			mysqli_stmt_fetch($stmt);
		}
		
		//Update products ProductType which were in the deleted category to "Other"
		$sql = "UPDATE products SET ProductType='Egyéb' WHERE ProductType='".$cName."'";
		if($stmt = mysqli_prepare($con, $sql)){
			mysqli_stmt_execute($stmt);
		}
		
		//Delete category
		$sql = "DELETE FROM category WHERE catID=".$id;
		if($stmt = mysqli_prepare($con, $sql)){
			mysqli_stmt_execute($stmt);
		}
		
		$stmt->close();
	}
	
?>
<!DOCTYPE html>
<html>
	<head>
		<?php addHead(); ?>
	</head>
	<body>
		<?php addMenu(1); ?>

		<div class="w3-padding content">
			<div class="w3-container">
				<h2>Product administration - Category level</h2><br>
				<a href="admin_add_cat.php" class="w3-button w3-light-grey">Add category</a>
				<a class="w3-button w3-light-grey" onclick="deleteCategory()">Delete selected</a>
			</div>
			
			<div class="w3-container">
				<hr>
				
				<?php 
					$sql = "SELECT * FROM category";
        
					if($stmt = mysqli_prepare($con, $sql)){
						// Attempt to execute the prepared statement
						if(mysqli_stmt_execute($stmt)){
						// Store result
						mysqli_stmt_store_result($stmt);
							for( $i = 0; $i < mysqli_stmt_num_rows($stmt); $i++ ){                    
								// Bind result variables
								mysqli_stmt_bind_result($stmt, $catID, $catName, $pDesc, $catImg);
								if(mysqli_stmt_fetch($stmt)){
									$sql = "SELECT url FROM images WHERE id=".$catImg;
									if($stmtt = mysqli_prepare($con, $sql)){
										if(mysqli_stmt_execute($stmtt)){
											mysqli_stmt_store_result($stmtt);
											mysqli_stmt_bind_result($stmtt, $catImg);
											if(mysqli_stmt_fetch($stmtt)){
							
											}else{$catImg='../img/default.png';}
										}
									}
									echo "<div class='w3-card-4'>";
									echo "<header class='w3-container w3-grey w3-padding'>";
									if($catName!="Egyéb"){
										echo "<span class='w3-mobile category_chkBox'><input type='checkbox' class='w3-check' id='".$catID."' name='chkBox' /></span>";
									} else {
										echo "<span class='w3-mobile category_chkBox'><input type='checkbox' class='w3-check' id='".$catID."' name='chkBox' disabled/></span>";
									}
									echo "<span class='w3-mobile w3-large category_title'>".$catName."</span><button onclick='manageCategory(\"".$catName."\")'>Manage</button><br>
												</header>
												<div class='w3-container'>
													<img src='".$catImg."' class='w3-hide-small category_ImgThumbnail'>
													<span>".$pDesc."</span>
												</div>
											</div><br>";
								} else {
									echo "Hiba: 1";
								}
							}
						} else {
							echo "Hiba: 2";
						}
						
					} else{
							echo "Oops! Valami elromlott. Nem sikerült az adataidat lekérni az adatbázisból.";
					}
				?>
				
			</div>
			
		</div>
		
		<script>
			function deleteCategory(){
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
					if(confirm("Do you really want to delete selected categories? (All products in category will be moved to Other)")){
						for (var i=0;i<chkdArr.length;i++){
							var xhttp = new XMLHttpRequest();
							xhttp.onreadystatechange = function() {
								if (xhttp.readyState == 4 && xhttp.status == 200) {
									location.reload();
								}
							}
							xhttp.open("GET","admin_products.php?del="+chkdArr[i], true);						
							xhttp.send();
							
						}
					} else {location.reload();}
				}
			}
			
			function manageCategory(categoryName){
				window.location.href = "admin_category_management.php?cat="+categoryName;
			}
		
		</script>
	</body>
</html>