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

	// 쿠키 삭제 예외 배열값
	$DelNotCookieArr = array("cid", "id_save");
	if($_COOKIE){
		foreach( $_COOKIE as $key => $value ){

			//쿠키삭제예외
			if(!in_array($key, $DelNotCookieArr)) {
				setcookie( $key, $value, time()-3600 , '/', C_DOMAIN);
				unset($_COOKIE[$key]);
			}
		}
	}

	echo "<script>location.href='/alarm/index.php';</script>";
	exit;
?>