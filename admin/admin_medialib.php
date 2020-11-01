<?php
	require_once "admin_config.php";
	
	$submit_err = '';	
	
	function addDebugNote($note){
		$myfile = fopen("debug.txt", "a");
		fwrite($myfile, $note."\r\n");
		fclose($myfile);
	}
	
		
	//TODO: Generalize function
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
						echo '<img src="'.$iThumb.'" class="w3-image" style="width: 100%;height: 160px;object-fit: cover"><div class="w3-container w3-center w3-blue-grey w3-border-top"><h6>'.$iTitle.'</h6><button class="w3-button w3-light-grey" onclick="imgOprtn(\'edit\','.$iId.',\''.$iUrl.'\',\''.$iTitle.'\',\''.$iAlt.'\')">Edit</button>&nbsp;&nbsp;<button class="w3-button w3-light-grey" onclick="delImg('.$iId.')">Delete</button><br><br></div></div>';
					} 
				} else{
					echo "Oops! Something went wrong. Please try again later.";
				}
				// Close statement
				mysqli_stmt_close($stmt);
			}
	}
	
	function resizeImg($filename, $max_width, $max_height){
		list($orig_width, $orig_height) = getimagesize($filename);
		$width = $orig_width;
		$height = $orig_height;

		# taller
		if ($height > $max_height) {
			$width = ($max_height / $height) * $width;
			$height = $max_height;
		}

		# wider
		if ($width > $max_width) {
			$height = ($max_width / $width) * $height;
			$width = $max_width;
		}

		$image_p = imagecreatetruecolor($width, $height);

		$image = imagecreatefromjpeg($filename);

		imagecopyresampled($image_p, $image, 0, 0, 0, 0, $width, $height, $orig_width, $orig_height);

		return $image_p;
	}	
	
	
	
	// Check user login
	if(!isset($_SESSION['adminlogin'])){
		header('Location: index.php');
	} else {
			if($_SERVER["REQUEST_METHOD"] == "POST"){
				$title = $_POST['title'];
				$alt = $_POST['alt'];
				$target_dir = "../img/user_library/";
				
				$uploadCheck = 1;
				$filename = basename($_FILES["catimgpath"]["name"]);
				$target_file = $target_dir . basename($_FILES["catimgpath"]["name"]);
				$imgFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
				if(!empty($filename)){
					//Check if image file is a real image file
					$check = getimagesize($_FILES["catimgpath"]["tmp_name"]);
				}
				else {
					$submit_err .= "No image file was selected. ";
					$check = 0;
					$uploadCheck = 0;
				}
				
				if($check !== false){
					$uploadCheck = 1;
				} else {
					$submit_err .= "The selected file is not supported. ";
					$uploadCheck = 0;
				}
				
		
				// Check if file already exists
				if (file_exists($target_file)) {
					$incr = '_new';
					while(file_exists($target_dir . str_replace('.'.$imgFileType, '', basename($_FILES["catimgpath"]["name"])).$incr.'.'.$imgFileType)){
						$incr .= '_new';
					}
					$filename = str_replace('.'.$imgFileType, '', basename($_FILES["catimgpath"]["name"])).$incr.'.'.$imgFileType;
					$target_file = $target_dir . $filename;
				}
			
				if ($target_file == $target_dir){
					$submit_err .= "No file selected. ";
					$uploadCheck = 0;
				}
				
				// Check file size
				if ($_FILES["catimgpath"]["size"] > 5000000) {
					$submit_err .= "Image file is too large. Max: 5Mb allowed ";
					$uploadCheck = 0;
				}
				
				// Allow certain file formats
				if($imgFileType != "jpg" && $imgFileType != "png" && $imgFileType != "jpeg" && $imgFileType != "gif" ) {
					$submit_err .= "Only JPG, JPEG, PNG & GIF files are allowed. ";
					$uploadCheck = 0;
				}
				
				// Check if $uploadCheck is set to 0 by an error
				if ( ($uploadCheck == 0) ) {
					$submit_err .= "Category creation was unsuccessful.";
					echo "<script>alert('".$submit_err."');</script>";
				// if everything is ok, try to upload file
				} else {
					// Prepare an insert statement
					$p_catimg = "http://localhost/pafranyszal/img/user_library/".$filename;
					$p_thumb = "http://localhost/pafranyszal/img/user_library/thumbnails/thumb_".$filename;
					if(empty($submit_err)){
						if (move_uploaded_file($_FILES["catimgpath"]["tmp_name"], $target_file)) {
							//header("location: admin_products.php");
							$sql = "INSERT INTO images (title, alt, url, thumbUrl) VALUES (?, ?, ?, ?)";
							if($stmt = mysqli_prepare($con, $sql)){
								mysqli_stmt_bind_param($stmt, "ssss", $title, $alt, $p_catimg, $p_thumb);
								mysqli_stmt_execute($stmt);
							}
							
							//create thumbnail for small image display
							$thumbnail = resizeImg("../img/user_library/".$filename, 300, 240);
							imagejpeg($thumbnail, "../img/user_library/thumbnails/thumb_".$filename);
							
							
						} else {
							$submit_err .= "Category creation was unsuccessful.";
						}
					}
				}
				$submit_err ='';
			}
			
			if(isset($_GET['delID'])){
				$sql = "SELECT url, thumbUrl FROM images WHERE id=".$_GET['delID'];
				if($stmt = mysqli_prepare($con, $sql)){
					if(mysqli_stmt_execute($stmt)){
						mysqli_stmt_store_result($stmt);
						mysqli_stmt_bind_result($stmt, $iUrl, $iThumb);
						mysqli_stmt_fetch($stmt);
					} else{
						echo "Oops! Something went wrong. Please try again later.";
					}
					mysqli_stmt_close($stmt);
				}
				$delfile = str_replace('http://localhost/pafranyszal/img/user_library/', '../img/user_library/', $iUrl);
				unlink($delfile);
				$delfile = str_replace('http://localhost/pafranyszal/img/user_library/thumbnails/', '../img/user_library/thumbnails/', $iThumb);
				unlink($delfile);
				
				$sql = "DELETE FROM images WHERE id=".$_GET['delID'];
				if($stmt = mysqli_prepare($con, $sql)){
					mysqli_stmt_execute($stmt);
				}
				$stmt->close();
			}
			
			if(isset($_GET['edtitle']) && isset($_GET['edalt']) && isset($_GET['edid'])){
				$sql = "UPDATE images SET title='".$_GET['edtitle']."', alt='".$_GET['edalt']."' WHERE id=".$_GET['edid'];
				if($stmt = mysqli_prepare($con, $sql)){
					if(mysqli_stmt_execute($stmt)){
					} else{
						echo "Oops! Something went wrong. Please try again later.";
					}
					mysqli_stmt_close($stmt);
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
		<?php addMenu(3); ?>
		
		<div class="w3-container w3-padding content"  id="contentDiv">

			<div class="w3-container">
				<h2>Media library</h2><br>
				<a class="w3-button w3-light-grey" onclick="imgOprtn('add')">Add image</a>
				<!-- <a class="w3-button w3-light-grey" onclick="deleteSelected()">Delete selected</a> -->
			</div><hr>
			
			<div style='position:absolute;top:0px;left:200px;width:100%;height:100%;background-color: #888;opacity: 0.5;visibility: hidden' id='addPupBg'></div>
			<div class='w3-card-4 w3-display-middle w3-center' style='position:absolute;width:400px;visibility: hidden'  id='addPup'>
				<header class='w3-container w3-teal'><h4>Upload image</h4></header>
				<div class='w3-container w3-white'>
					<form action="" method="post" enctype="multipart/form-data"><br>
					<img id='imgPreview' src='http://localhost/pafranyszal/img/default.png' class='imgThumbnail'><br><br>
					Choose image to upload&nbsp;&nbsp;<input type='file' accept='image/png, image/jpeg' name='catimgpath' id='catimgpath'  class='inp' style='visibility: hidden'>
					<input type='button' class='w3-button w3-light-grey' value='Browse...' onclick='document.getElementById("catimgpath").click();' /><br><br>
					Image title&nbsp;&nbsp;<input type='text' id='title' name='title'/><br><br>
					Image alt&nbsp;&nbsp;&nbsp;&nbsp;<input type='text' id='alt' name='alt'/><br><br>
					<input type='submit' class='w3-button w3-light-grey' value='Upload' />&nbsp;&nbsp;&nbsp;
					<input type='button' class='w3-button w3-light-grey' value='Cancel' onclick='cancelimgOprtn()' /><br><br>
					<span style="color: red"><?php echo $submit_err; ?><br><br></span>
					</form>
				</div>
			</div>
			
			<div class='w3-card-4 w3-display-middle w3-center' style='position:absolute;width:400px;visibility: hidden'  id='editPup'>
				<header class='w3-container w3-teal'><h4>Edit image</h4></header>
				<div class='w3-container w3-white'>
					<form action="" method="get"><br>
						<img id='editImgPreview' src='http://localhost/pafranyszal/img/default.png' class='imgThumbnail'><br><br>
						<label for="edtitle">Image title</label>
						<input type='text' id='edtitle' name='edtitle'/><br><br>
						<label for="edalt">Image alt</label>
						<input type='text' id='edalt' name='edalt'/><br><br>
						<input type='submit' class='w3-button w3-light-grey' value='Save' />&nbsp;&nbsp;&nbsp;
						<input type='button' class='w3-button w3-light-grey' value='Cancel' onclick='cancelimgOprtn()' /><br><br>
						<input type='text' id='edid' name='edid' style='visibility: hidden'/>
					</form>
				</div>
			</div>
			
			
				<?php showLib(); ?>	
			
		</div>
		
		<script>
			const input = document.querySelector('.inp');
			input.addEventListener('change', updateImageDisplay);
			var selected = 0;
			
			function imgOprtn(oprtn, what, url, title, alt){
				document.getElementById("addPupBg").style.visibility="visible";
				switch(oprtn){
					case 'add':
						document.getElementById("addPup").style.visibility="visible";
						break;
					case 'edit':
						document.getElementById("editImgPreview").src=url;
						document.getElementById("edtitle").value=title;
						document.getElementById("edalt").value=alt;
						document.getElementById("edid").value=what;
						document.getElementById("editPup").style.visibility="visible";
						break;
				}
			}
			
			function cancelimgOprtn(){
				document.getElementById("addPup").style.visibility="hidden";
				document.getElementById("editPup").style.visibility="hidden";
				document.getElementById("addPupBg").style.visibility="hidden";
				document.getElementById("imgPreview").src="http://localhost/pafranyszal/img/default.png";
				document.getElementById("title").value="";
				document.getElementById("alt").value="";
				selected = 0;
			}
			
			function updateImageDisplay(){
				const newImg = input.files[0];
				document.getElementById('imgPreview').src = URL.createObjectURL(newImg);
				selected = 1;
			}
			
			function delImg(ID){
				var xhttp = new XMLHttpRequest();
				xhttp.onreadystatechange = function() {
					if (xhttp.readyState == 4 && xhttp.status == 200) {
						window.location.href = "admin_medialib.php";
					}
				}
				xhttp.open("GET","admin_medialib.php?delID="+ID, true);						
				xhttp.send();
			}
			
				
			
			
		</script>
	</body>
</html>