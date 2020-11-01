<?php

		require_once "admin_config.php";
		
		$_SESSION = array();
		session_destroy();
		unset($_SESSION['adminlogin']);
		header('Location: index.php');
		exit;
?>