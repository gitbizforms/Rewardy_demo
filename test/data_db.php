<?php

//phpinfo();

	$home_dir = str_replace( basename(__DIR__) , "", __DIR__ );
	include $home_dir . "inc_lude/conf_mysqli.php";
	include $home_dir . "inc/SHA256/KISA_SHA256.php";

	include DBCON_MYSQLI;
	include FUNC_MYSQLI;

	$user_id='adsb123@naver.com';
	$insert_idx='9999';

	work_cp_reward("main", "0008", $user_id, $insert_idx);

	//work_cp_reward("main", "0003", $user_id, $insert_idx);


?>