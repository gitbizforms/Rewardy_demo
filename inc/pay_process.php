<?php

$home_dir = str_replace( basename(__DIR__) , "" , __DIR__ );
//연결된 도메인으로 분리
include $home_dir . "inc_lude/conf_mysqli.php";
include $home_dir . "inc/SHA256/KISA_SHA256.php";
include DBCON_MYSQLI;
include FUNC_MYSQLI;

$mode = $_POST["mode"];					//mode값 전달받음
$type_flag = ($chkMobile)?1:0;				//구분(0:사이트, 1:모바일)

if($_COOKIE){
	$user_id = $_COOKIE['user_id'];
	$user_name = $_COOKIE['user_name'];
	$user_level = $_COOKIE['user_level'];
	$user_part = $_COOKIE['user_part'];
}


if($mode == 'num_check'){

	$num = $_POST['com_num'];

	$sql = "select idx,company,company_num from work_company where state = '0' and company_num = '".$num."'";
	$com_num_check = selectQuery($sql);

	if($com_num_check['idx']){
		echo "fail";
	}else{
		echo "complete";
	}
}
if($mode == 'email_check'){

	$email = $_POST['com_email'];

	$sql = "select idx,email,state from work_member where state = '0' and email = '".$email."'";
	$com_email_check = selectQuery($sql);

	if($com_email_check['idx']){
		echo "fail";
	}else{
		echo "complete";
	}
}
?>