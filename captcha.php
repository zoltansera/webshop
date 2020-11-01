<?php

	putenv('GDFONTPATH=' . realpath('.'));
	
	if(!isset($_SESSION)){
	session_start();
	}
	
	//phpinfo();
	
	header('Content-Type: image/png');
	
	$ima=imagecreatetruecolor(150,40);
 
	$white=imagecolorallocate($ima,255,255,255);
	$black=imagecolorallocate($ima,0,0,0);
	$grey=imagecolorallocate($ima,125,125,125);
	imagefilledrectangle($ima, 0, 0, 149, 39, $white);
	
	$chars="abcdefhjkmnpqrstuxy345789";
	$str="";
	for ($i=0;$i<6;$i++){
		$rand=rand(0,strlen($chars)-1);
		$str.=$chars[$rand];
	}

	$_SESSION["captcha"]=$str;
	
	$font = 'D:\xampp\htdocs\pafranyszal\tahoma.ttf';
	
	imagettftext($ima,20,0,11,21,$grey,$font,$str);
	imagettftext($ima,20,0,10,20,$black,$font,$str);
	
	imagepng($ima);
	imagedestroy($ima);
?>