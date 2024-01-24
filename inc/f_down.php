<?php

	$home_dir = str_replace( basename(__DIR__) , "" , __DIR__ );

	include $home_dir . "inc_lude/conf_mysqli.php";
	include $home_dir . "inc/SHA256/KISA_SHA256.php";
	include DBCON_MYSQLI;
	include FUNC_MYSQLI;

	$filename = $_GET['filename'];
	$filepath = $_GET['filepath'];
	$length = filesize($filepath);

	header("Content-Type: application/octet-stream");

	header("Content-Length: $length");

	header("Content-Disposition: attachment; filename=".iconv('utf-8','euc-kr',$filename));

	header("Content-Transfer-Encoding: binary");



	$fh = fopen($filepath, "r");

	fpassthru($fh);

	exit;

	


?>