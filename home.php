<?php
session_start();

require_once "config.php";

// Check user login or not
if(!isset($_SESSION['loggedin'])){
    header('Location: index.php');
}

// logout
if(isset($_POST['but_logout'])){
    $_SESSION = array();
	session_destroy();
	//unset($_SESSION['uname']);
    header('Location: index.php');
	exit;
}

if(isset($_POST['but_resetpwd'])){
    header('Location: resetpwd.php');
	exit;
}
?>
<!doctype html>
<html>
<head>
    <?php addHead(); ?>
</head>
<body class="fadein">
	<?php addMenu(); ?>

	<main class="main">			
		<div style="padding: 60px 0px 0px 0px">
			<div>
				<form method='post' action="">
					<input type="submit" value="Kijelentkezés" name="but_logout">
					<input type="submit" value="Jelszó megváltoztatása" name="but_resetpwd">
				</form>
				
				<br>
				<?php
					$sql = "SELECT Email, Fname, Sname, City, Address, Phone, Irsz FROM members WHERE Username = ?";
        
					if($stmt = mysqli_prepare($con, $sql)){
						// Bind variables to the prepared statement as parameters
						mysqli_stmt_bind_param($stmt, "s", $param_username);
            
						// Set parameters
						$param_username = $_SESSION["username"];
            
						// Attempt to execute the prepared statement
						if(mysqli_stmt_execute($stmt)){
						// Store result
						mysqli_stmt_store_result($stmt);
                
							if(mysqli_stmt_num_rows($stmt) == 1){                    
								// Bind result variables
								mysqli_stmt_bind_result($stmt, $mail, $fname, $sname, $city, $addr, $phone, $irsz);
								if(mysqli_stmt_fetch($stmt)){
									// Store data in session variables
									echo "<br>";
									$_SESSION["email"] = $mail;
									$_SESSION["fname"] = $fname;
									$_SESSION["sname"] = $sname;
									$_SESSION["city"] = $city;
									$_SESSION["addr"] = $addr;
									$_SESSION["phone"] = $phone;
									$_SESSION["irsz"] = $irsz;
								}
							}
						}
					} else{
							echo "Oops! Valami elromlott. Nem sikerült az adataidat lekérni az adatbázisból.";
					}
				?>
				<p>Név: <?php echo $_SESSION["fname"]." ".$_SESSION["sname"]; ?></p>
				<p>Email: <?php echo $_SESSION["email"]; ?></p>
				<p>Telefonszám: <?php echo $_SESSION["phone"]; ?></p>
				<p>Szállítási cím: <?php echo $_SESSION["irsz"]." ".$_SESSION["city"].", ".$_SESSION["addr"]; ?></p>
			</div>
		</div>
	</main>
		
		
		
    </body>
</html>