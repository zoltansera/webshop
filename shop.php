<?php
	require_once "config.php";
?>
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
		<br>
		<div class="w3-container" style="margin: auto">
				<?php 
						if(!isset($_GET['cat'])){
							/*echo "<div class='w3-container' style='width: 80%;margin: auto'>
											<p>Ezen a lapon választhatsz a kategóriák közül</p>
									</div>
									<br>";*/
								
								
							$sql = "SELECT * FROM category";
						
							if($stmt = mysqli_prepare($con, $sql)){
								// Attempt to execute the prepared statement
								if(mysqli_stmt_execute($stmt)){
									// Store result
									mysqli_stmt_store_result($stmt);
									
									if(mysqli_stmt_num_rows($stmt)==1){
											header("location: shop.php?cat=Egyéb");
									} else {
									
										for( $i = 0; $i < mysqli_stmt_num_rows($stmt); $i++ ){                    
											// Bind result variables
											mysqli_stmt_bind_result($stmt, $catID, $catName, $catDesc, $catImg);
											if(mysqli_stmt_fetch($stmt)){
												echo "<form method='get' name='form' action='$PRODUCTS_PAGE'>";
												if($i%2==0){
													echo "<div class='w3-row w3-card w3-padding-24 w3-animate-left' style='width: 80%;margin: auto'>
																<div class='w3-half w3-container'>
																	<img src='".$catImg."' class='w3-image'>
																</div>
																<div class='w3-half w3-container w3-center'>
																	<h2>".$catName."</h2>
																	<p class='shortDesc'>".$catDesc."</p><br><br>
																	<button class='w3-button w3-blue-grey' name='cat' value='$catName'>Megnézem</button><br><br>
																</div>
															</div><br>";
												} else {
													echo "<div class='w3-row w3-card w3-padding-24 w3-animate-right' style='width: 80%;margin: auto'>
																<div class='w3-half w3-container  w3-center'>
																	<h2>".$catName."</h2>
																	<p class='shortDesc'>".$catDesc."</p><br><br>
																	<button class='w3-button w3-blue-grey'  name='cat' value='$catName'>Megnézem</button><br><br>
																</div>
																<div class='w3-half w3-container'>
																	<img src='".$catImg."' class='w3-image'>
																</div>
															</div><br><br>";
												}
												echo "</form>";
											} else {
												echo "Adatbázis hiba 1";
											}
										}		
									}	
									
									
									
								} else {
									echo "Adatbázis hiba 2";
								}
							} else {
								echo "Adatbázis hiba 3";
							}
						} else {
							/*echo "<div class='w3-container' style='width: 80%;margin: auto'>
											<p>Ezen a lapon láthatod az $catch kategóriájú termékeket</p>
									</div>
									<br>
									<div class='w3-cell-row' style='width: 80%;margin: auto'>";*/
							
							$catch = $_GET['cat'];
							//$catch = $_REQUEST['cat'];
							
							$sql = "SELECT ProductID, ProductName, ProductType, Description, Price, ImageSource FROM products WHERE ProductType = ?";
							if($stmt = mysqli_prepare($con, $sql)){
								// Bind variables to the prepared statement as parameters
								mysqli_stmt_bind_param($stmt, "s", $param_pType);
            
								// Set parameters
								$param_pType = $catch;
								// Attempt to execute the prepared statement
								if(mysqli_stmt_execute($stmt)){
									// Store result
									mysqli_stmt_store_result($stmt);
									for( $i = 0; $i < mysqli_stmt_num_rows($stmt); $i++ ){                    
										// Bind result variables
										mysqli_stmt_bind_result($stmt, $pID, $pName, $pType, $pDesc, $pPrice, $pImgSrc);
										if(mysqli_stmt_fetch($stmt)){
											echo "<form method='get' name='form' action='$PRODUCTS_PAGE'>";
											//<div class='w3-card w3-padding' style='text-align: center;margin: auto'>
											echo "<div class='w3-container w3-cell w3-cell-middle w3-third w3-padding w3-mobile'>
														<div class='w3-container w3-padding' style='text-align: center;margin: auto;border: 1px solid #dddddd'>
														<img class='w3-image' src='$pImgSrc'>
														<h3 class='w3-hide-medium'>$pName</h3>
														<h6 class='w3-hide-small w3-hide-large'>$pName</h6>
														<button class='w3-button w3-blue-grey'  name='prod' value='$pID'>Bőveben</button><br><br>
														</div>
													</div>";
											echo "</form>";
											
										} else {
											echo "Termék adatbázis hiba 1";
										}
									}
									echo "</div>";
								} else {
									echo "Termék adatbázis hiba 2";
								}
							} else{
								echo "Termék adatbázis hiba 3";
							}
						}
				
				
				?>
				
			</div>
				
		<footer class="footer"><?php include 'footer.php';?></footer>
	</body>
	
</html>
