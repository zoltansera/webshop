<?php
// Include config file
require_once "config.php";
 
if(isset($_SESSION['loggedin'])){
	header("location: index.php");
}
 
// Define variables and initialize with empty values
$username = $password = $confirm_password = $fname = $sname = $mailaddr = $phone = $irsz = $city = $addr = $accept = "";
$username_err = $password_err = $confirm_password_err = $fname_err = $sname_err = $mailaddr_err = $phone_err = $irsz_err = $city_err = $addr_err = $accept_err ="";
 
// Processing form data when form is submitted
if($_SERVER["REQUEST_METHOD"] == "POST"){
 
    // Validate username
    if(empty(trim($_POST["usrname"]))){
        $username_err = "Kérlek add meg a felhasználóneved.";
    } else{
        // Prepare a select statement
        $sql = "SELECT MemberID FROM members WHERE Username = ?";
        
        if($stmt = mysqli_prepare($con, $sql)){
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "s", $param_username);
            
            // Set parameters
            $param_username = trim($_POST["usrname"]);
            
            // Attempt to execute the prepared statement
            if(mysqli_stmt_execute($stmt)){
                /* store result */
                mysqli_stmt_store_result($stmt);
                
                if(mysqli_stmt_num_rows($stmt) == 1){
                    $username_err = "Ez a név már foglalt.";
                } else{
                    $username = trim($_POST["usrname"]);
                }
            } else{
                echo "Oops! Valami elromlott. Kérlek próbálkozz később.";
            }

            // Close statement
            mysqli_stmt_close($stmt);
        }
    }
    
    // Validate password
    if(empty(trim($_POST["password"]))){
        $password_err = "Kérlek írj be egy jelszót.";     
    } elseif(strlen(trim($_POST["password"])) < 6){
        $password_err = "A jelszónak legalább 6 karakter hosszúságúnak kell lennie.";
    } else{
        $password = trim($_POST["password"]);
    }
    
    // Validate confirm password
    if(empty(trim($_POST["confirm_password"]))){
        $confirm_password_err = "Kérlek erősítsd meg a jelszavadat.";     
    } else{
        $confirm_password = trim($_POST["confirm_password"]);
        if(empty($password_err) && ($password != $confirm_password)){
            $confirm_password_err = "A jelszavak nem egyeznek!";
        }
    }
    
	//Validate first name
	if(empty(trim($_POST["fname"]))){
        $fname_err = "Kérlek add meg a vezetéknevedet.";     
    } else{
        $fname = trim($_POST["fname"]);
    }
	
	//Validate surname
	if(empty(trim($_POST["sname"]))){
        $sname_err = "Kérlek add meg az utónevedet.";     
    } else{
        $sname = trim($_POST["sname"]);
    }
	
	//Validate email
	if(empty(trim($_POST["mailaddr"]))){
        $mailaddr_err = "Kérlek add meg az email címedet.";     
    } else{
        $mailaddr = trim($_POST["mailaddr"]);
    }
	
	//Validate phone
	if(empty(trim($_POST["phone"]))){
        $phone_err = "Kérlek add meg a telefonszámodat.";     
    } else{
        $phone = trim($_POST["phone"]);
    }
	
	if(empty(trim($_POST["irsz"]))){
        $irsz_err = "Kérlek add meg az irányítószámodat.";     
    } else{
        $irsz = trim($_POST["irsz"]);
    }
	
	if(empty(trim($_POST["city"]))){
        $city_err = "Kérlek add meg a lakhelyedet.";     
    } else{
        $city = trim($_POST["city"]);
    }
	
	if(empty(trim($_POST["addr"]))){
        $addr_err = "Kérlek add meg a szállítási címedet.";     
    } else{
        $addr = trim($_POST["addr"]);
    }
	
	if(!isset($_POST["accept"])){
		$accept_err = "<br>A regisztrációhoz kérlek fogadd el az Adatvédelmi szabályzatban és az ÁSZ-ben foglaltakat.";
	} else{
		$accept_err = "";
	}
	
    // Check input errors before inserting in database
    if(empty($username_err) && empty($password_err) && empty($confirm_password_err) && empty($fname_err) && empty($sname_err) && empty($mailaddr_err) && empty($phone_err) && empty($irsz_err) && empty($city_err) && empty($addr_err) && empty($accept_err)){
        
        // Prepare an insert statement
        $sql = "INSERT INTO members (Username, Passwd, Email, Fname, Sname, City, Address, Phone, Irsz) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
		
        if($stmt = mysqli_prepare($con, $sql)){
            // Bind variables to the prepared statement as parameters
            //mysqli_stmt_bind_param($stmt, "ss", $param_username, $param_password);
			mysqli_stmt_bind_param($stmt, "sssssssss", $param_username, $param_password, $param_email, $param_fname, $param_sname, $param_city, $param_address, $param_phone, $param_irsz);
            
            // Set parameters
            $param_username = $username;
            $param_password = password_hash($password, PASSWORD_DEFAULT); // Creates a password hash
			$param_email = $mailaddr;
			$param_fname = $fname;
			$param_sname = $sname;
			$param_city = $city;
			$param_address = $addr;
			$param_phone = $phone;
			$param_irsz = $irsz;

            // Attempt to execute the prepared statement
            if(mysqli_stmt_execute($stmt)){
                // Redirect to login page
                header("location: login.php");
            } else{
				
                echo "Valami elromlott. Kérlek próbálkozz később.";
            }

            // Close statement
            mysqli_stmt_close($stmt);
        }
    }
    
    // Close connection
    mysqli_close($con);
}
?>
 
<!DOCTYPE html>
<html>
<head>
   <?php addHead(); ?>

    <style type="text/css">
        .wrapper{ width: 350px; padding: 20px; }
    </style>
	
</head>
<body class="fadein">
	<?php addMenu(); ?>	
	

    <main class="main">			
		<div style="padding: 60px 0px 0px 0px">
			<form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
			<div id="div_login">
				<h1>Regisztráció</h1>
				<p>A következő űrlap kitöltésével hozhatod létre a felhasználói fiókodat.</p>
				<div>
					<input type="text" name="usrname" class="textbox" value="<?php echo $username; ?>" placeholder="Felhasználónév">
					<span class="err_mess"><?php echo $username_err; ?></span>
				</div>    
				<div>
					<input type="password" name="password" class="textbox" value="<?php echo $password; ?>"  placeholder="Jelszó">
					<span class="err_mess"><?php echo $password_err; ?></span>
				</div>
				<div >
					<input type="password" name="confirm_password" class="textbox" value="<?php echo $confirm_password; ?>"  placeholder="Jelszó megerősítése">
					<span class="err_mess"><?php echo $confirm_password_err; ?></span>
				</div>
				<div >
					<input type="text" name="fname" class="textbox" value="<?php echo $fname; ?>"  placeholder="Vezetéknév">
					<span class="err_mess"><?php echo $fname_err; ?></span>
				</div>
				<div >
					<input type="text" name="sname" class="textbox" value="<?php echo $sname; ?>"  placeholder="Utónév">
					<span class="err_mess"><?php echo $sname_err; ?></span>
				</div>
				<div >
					<input type="text" name="mailaddr" class="textbox" value="<?php echo $mailaddr; ?>"  placeholder="Email cím">
					<span class="err_mess"><?php echo $mailaddr_err; ?></span>
				</div>
				<div >
					<input type="text" name="phone" class="textbox" value="<?php echo $phone; ?>"  placeholder="Telefonszám">
					<span class="err_mess"><?php echo $phone_err; ?></span>
				</div>
				<p>Szállítási cím</p>
				<div >
					<input type="text" name="irsz" class="textbox" value="<?php echo $irsz; ?>"  placeholder="Irányítószám">
					<span class="err_mess"><?php echo $irsz_err; ?></span>
				</div>
				<div >
					<input type="text" name="city" class="textbox" value="<?php echo $city; ?>"  placeholder="Település">
					<span class="err_mess"><?php echo $city_err; ?></span>
				</div>
				<div >
					<input type="text" name="addr" class="textbox" value="<?php echo $addr; ?>"  placeholder="Cím (Utca, házszám, stb)">
					<span class="err_mess"><?php echo $addr_err; ?></span>
				</div>
				<div >
					<input type="checkbox" name="accept" id="acc">
					<label for="acc">Regisztrációmmal elfogadom az <a>Adatvédelmi szabályzat</a>ot és az <a>ÁSZF</a>-et.</label>
					<span class="err_mess"><?php echo $accept_err; ?></span>
				</div>
				<div >
					<input type="submit" value="Regisztrálok">
					<!-- <input type="button" value="Törlés"> -->
				</div>
				<p>Már van fiókod? <a href="login.php">Jelentkezz be itt</a>.</p>
			</div>
			</form>
    </div>    
	
	<footer class="footer"><?php include 'footer.php';?></footer>
	
</body>
</html>