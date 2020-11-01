<?php
	
	if(!isset($_SESSION)){
		session_start();
	}
	
	require_once "admin_functions.php";
	
	//DB Connection
	$host = "localhost"; /* Host name */
	$user = "homeg_usr"; /* User */
	$password = "12345678"; /* Password */
	$dbname = "homeg"; /* Database name */
	
	$con = mysqli_connect($host, $user, $password,$dbname);
	mysqli_query($con, 'SET NAMES UTF-8');
	mysqli_query($con, "SET character_set_results=utf8");
	mysqli_set_charset($con, 'utf-8');

	// Check connection
	if (!$con) {
		die("Connection failed: " . mysqli_connect_error());
	}
	
	
?>