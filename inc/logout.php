<?php
	$home_dir = str_replace( basename(__DIR__) , "", __DIR__ );
	include $home_dir . "inc_lude/conf_mysqli.php";
	include DBCON_MYSQLI;
	include FUNC_MYSQLI;

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

	//쿠키가 없을때
	if(!$_COOKIE['user_id']){
		echo "ok";
	}
	exit;
?>