<?php
session_start();
// Include config file
require_once "config.php";
 
// Define variables and initialize with empty values
$fullname = $email = $subject = $message = "";
$fullname_err = $email_err = $subject_err = $message_err = "";
$message_sent = $message_not_sent = "";
$captcha_err = "";
 
// Processing form data when form is submitted
if($_SERVER["REQUEST_METHOD"] == "POST"){
 
    // Check if username is empty
    if(empty(trim($_POST["fullname"]))){
        $fullname_err = "Kérlek írd be a nevedet.";
    } else{
        $fullname = trim($_POST["fullname"]);
    }
    
    // Check if email is empty
    if(empty(trim($_POST["email"]))){
        $email_err = "Kérlek add meg az email címedet.";
    } else{
        $email = trim($_POST["email"]);
    }
    
	// Check if subject is empty
    if(empty(trim($_POST["subject"]))){
        $subject_err = "Kérlek add meg a témát.";
    } else{
        $subject = trim($_POST["subject"]);
    }
    
	// Check if message is empty
    if(empty(trim($_POST["message"]))){
        $message_err = "Kérlek írj üzenetet.";
    } else{
        $message = trim($_POST["message"]);
    }

	if (strtolower($_POST["captcha_code"]) !== $_SESSION["captcha"]){
		$captcha_err = "Hibás biztonsági kód.";
    } else {
		echo "NOK";
    }
	
	if( empty($message_err) && empty($message_err) && empty($message_err) && empty($message_err) && empty($captcha_err)  ){
		$res = mail("hello@pafranyszal.eu", $subject, $message, 'From: info@zoltansera.nhely.hu'."\r\n".'Cc: '.$email);
		if($res==1){
			$message_sent = "Köszönöm, hamarosan elolvasom az üzenetet.";
		} else {
			$message_not_sent = "Valami hiba történt, kérlek próbáld meg később.";
		}
	} else {
		//echo "NOK";
	}
    
}
?>
<!DOCTYPE html>
<html>
	<head>
		<?php addHead(); ?>
	</head>
	<body class="fadein">
		<?php addMenu(); ?>
		
		<br>
		<div class="w3-card  w3-animate-left" style="width: 80%; margin: auto">
			<header class="w3-padding w3-center boxheader"><h2>Elérhetőségek</h2></header><br>
			<div class="w3-padding"><p>A következő elérhetőségeken, vagy az alul található kapcsolatfelvételi űrlap segítségével tudsz elérni.</p></div>
			<div class="w3-padding">
				<p>Email címem: hello@pafranyszal.eu</p><br>
				<p>Telefonszám: +3630 179 2019</p><br>
			</div>
		</div>
		<br>
		<div class="w3-card w3-animate-right" style="width: 80%; margin: auto">
			<header class="w3-padding w3-center boxheader"><h2>Üzenet</h2></header><br>
			<form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
				<div class="w3-container">
					<label for="fullname">Név</label><br>
					<input type="text" id="fullname" name="fullname" placeholder="A neved..." style="width:100%;margin:auto;">
					<span class="w3-red"><?php echo $fullname_err; ?></span>
				</div>
				<br>
				<div class="w3-container">
					<label for="mailcim">Email</label><br>
					<input type="text" id="email" name="email" placeholder="Email címed..." style="width:100%;margin:auto;">
					<span class="w3-red"><?php echo $email_err; ?></span>
				</div>
				<br>
				<div class="w3-container">
					<label for="subject">Téma</label><br>
					<input type="text" id="subject" name="subject" placeholder="Mivel kapcsolatban írsz?" style="width:100%;margin:auto;">
					<span class="w3-red"><?php echo $subject_err; ?></span>
				</div>
				<br>
				<div class="w3-container">
					<label for="message">Üzenet</label><br>
					<textarea id="message" name="message" placeholder="Íde írd üzenetedet..." style="height:200px;width:100%;margin:auto;resize: none;"></textarea>
					<span class="w3-red"><?php echo $message_err; ?></span>
				</div>
				<br>
				<div class="w3-container">
					<p>Biztonsági kód</p><br>
					<img src="captcha.php" alt="Biztonsági kód" title="Biztonsági kód">
					<input type="text" name="captcha_code" value="" maxlength="6">
					<span class="w3-red"><?php echo $captcha_err; ?></span>
				</div>
				<br>
				<div class="w3-container">
					<input type="submit"  class="w3-button w3-blue-grey" value="Küldés">
					<span class="w3-green"><?php echo $message_sent; ?></span>
					<span class="w3-red"><?php echo $message_not_sent; ?></span>
					<br><br>
				</div>
			</form>
		</div>
	
		<footer class="footer"><?php include 'footer.php';?></footer>
	</body>
</html>