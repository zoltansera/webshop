<?php

		require_once "config.php";
		
		$_SESSION = array();
		session_destroy();
		unset($_SESSION['loggedin']);
		header('Location: index.php');
		exit;
?>