<?php
 
// Check if the user is already logged in, if yes then redirect him to welcome page
if(isset($_SESSION["adminlogin"]) && $_SESSION["adminlogin"] === true){
    header("location: admin.php");
    exit;
}
 
// Include config file
require_once "admin_config.php";
 
// Define variables and initialize with empty values
$username = $password = "";
$username_err = $password_err = "";
 
// Processing form data when form is submitted
if($_SERVER["REQUEST_METHOD"] == "POST"){
 
    // Check if username is empty
    if(empty(trim($_POST["txt_uname"]))){
        $username_err = "Please enter admin username.";
    } else{
        $username = trim($_POST["txt_uname"]);
    }
    
    // Check if password is empty
    if(empty(trim($_POST["txt_pwd"]))){
        $password_err = "Please enter admin password.";
    } else{
        $password = trim($_POST["txt_pwd"]);
    }
    
    // Validate credentials
    if(empty($username_err) && empty($password_err)){
        // Prepare a select statement
        $sql = "SELECT admname, admpass FROM admins WHERE admname = ?";
        
        if($stmt = mysqli_prepare($con, $sql)){
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "s", $param_username);
            
            // Set parameters
            $param_username = $username;
            
            // Attempt to execute the prepared statement
            if(mysqli_stmt_execute($stmt)){
                // Store result
                mysqli_stmt_store_result($stmt);
                
                // Check if username exists, if yes then verify password
                if(mysqli_stmt_num_rows($stmt) == 1){                    
                    // Bind result variables
                    mysqli_stmt_bind_result($stmt, $username, $hashed_password);
                    if(mysqli_stmt_fetch($stmt)){
                        if(password_verify($password, $hashed_password)){
                            // Password is correct, so start a new session
                            session_start();
                            
                            // Store data in session variables
                            $_SESSION["adminlogin"] = true;
                            //$_SESSION["adminid"] = $id;
                            
                            // Redirect user to welcome page
                            header("location: admin.php");
                        } else{
                            // Display an error message if password is not valid
                            $password_err = "Incorrect username or password.";
                        }
                    }
                } else{
                    // Display an error message if username doesn't exist
                    $username_err = "Incorrect username or password.";
                }
            } else{
                echo "Oops! Something went wrong. Please try again later.";
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
	
</head>
<body class="fadein">
		<br>
		<div class="w3-card" style="max-width: 400px; margin: auto">
			<header class="w3-padding boxheader"><h2>Admin login</h2></header><br>
			<form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
					<div class="w3-container">
						<input type="text" class="textbox" id="txt_uname" name="txt_uname" placeholder="Username" style="width:100%;margin:auto;"/>
						<span class="w3-red"><?php echo $username_err; ?></span>
					</div>
					<br>
					<div class="w3-container">
						<input type="password" class="textbox" id="txt_pwd" name="txt_pwd" placeholder="Password" style="width:100%;margin:auto;"/>
						<span class="w3-red"><?php echo $password_err; ?></span>
					</div>
					<br>
					<div class="w3-container">
						<input type="submit" value="Log in" class="w3-button w3-blue-grey" name="but_submit" id="but_submit" />
						<br><br>
					</div>
			</form>
		</div>
</body>
</html>