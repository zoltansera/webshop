<?php 
	include 'admin_config.php';

	$username = 'admin';
	$password = '11111111';
	$mailaddr = 'teszt@tesz.teszt';
	$fname = 'Séra';
	$sname= 'Zoltán';	
	
// Prepare an insert statement
        $sql = "INSERT INTO admins (admname, admpass, admmail, fname, sname) VALUES (?, ?, ?, ?, ?)";
		
        if($stmt = mysqli_prepare($con, $sql)){
            // Bind variables to the prepared statement as parameters
			mysqli_stmt_bind_param($stmt, "sssss", $p_username, $p_password, $p_mail, $p_fname, $p_sname);
            
            // Set parameters
            $p_username = $username;
            $p_password = password_hash($password, PASSWORD_DEFAULT); // Creates a password hash
			$p_mail = $mailaddr;
			$p_fname = $fname;
			$p_sname = $sname;

            // Attempt to execute the prepared statement
            if(mysqli_stmt_execute($stmt)){
                // Redirect to login page
                header("location: index.php");
            } else{
				
                echo "Valami elromlott. Kérlek próbálkozz később.";
            }

            // Close statement
            mysqli_stmt_close($stmt);
        }

?>