<?php
	$home_dir = str_replace( basename(__DIR__) , "", __DIR__ );
	include $home_dir . "inc_lude/conf_mysqli.php";
	include $home_dir . "inc/SHA256/KISA_SHA256.php";

	include DBCON_MYSQLI;
	include FUNC_MYSQLI;


/*
	setcookie('user_id', '', time()-3600, '/', C_DOMAIN);
	setcookie('user_name', '', time()-3600, '/', C_DOMAIN);
	setcookie('user_level', '', time()-3600, '/', C_DOMAIN);
*/

	
?>