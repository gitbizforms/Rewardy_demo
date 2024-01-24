<?php

$home_dir = str_replace( basename(__DIR__) , "", __DIR__ );
// if($_SERVER['HTTP_HOST'] == "officeworker.co.kr"){

include $home_dir . "inc_lude/conf_mysqli.php";
include $home_dir . "inc/SHA256/KISA_SHA256.php";
include DBCON_MYSQLI;
include FUNC_MYSQLI;

// }else{
// 	include $home_dir . "inc_lude/conf.php";
// 	include DBCON;
// 	include FUNC;
// }

//실서버
//include_once(__DIR__."\\PHPMailer\\PHPMailerAutoload.php");
include_once($home_dir."/PHPMailer/libphp-phpmailer/PHPMailerAutoload.php");

$mode = $_POST["mode"];					//mode값 전달받음
$type_flag = ($chkMobile)?1:0;				//구분(0:사이트, 1:모바일)

if($_COOKIE){
	$user_id = $_COOKIE['user_id'];
	$user_name = $_COOKIE['user_name'];
	$user_level = $_COOKIE['user_level'];
	$user_part = $_COOKIE['user_part'];
}

if($mode == "alarm_change"){
	$sw_val = $_POST['sw_val'];
	$onf = $_POST['onf'];

	// $sql = "select idx, email, companyno from work_member_alarm where email = '".$user_id."' and companyno = '".$companyno."'";
    // $member = selectQuery($sql);

    if($sw_val == "allselect_alarm"){
        if($onf == 'true'){
            $sql = "update work_member_alarm set todaywork_alarm = '1',challenges_alarm = '1',party_alarm = '1',reward_alarm = '1', like_alarm = '1', memo_alarm = '1', allselect_alarm = '1' where email = '".$user_id."' and state = '0'";
            // echo $sql;
        }elseif($onf == 'false'){
            $sql = "update work_member_alarm set todaywork_alarm = '0',challenges_alarm = '0',party_alarm = '0',reward_alarm = '0', like_alarm = '0', memo_alarm = '0', allselect_alarm = '0' where  email = '".$user_id."' and state = '0'";
            // echo $sql;
        }
        $update_alarm = updateQuery($sql);
        if($update_alarm){
            echo "|success";
        }
    }else{
        if($onf == 'true'){
            $sql = "update work_member_alarm set ".$sw_val." = '1' where email = '".$user_id."' and state = '0'";
            // echo $sql;
        }elseif($onf == 'false'){
            $sql = "update work_member_alarm set ".$sw_val." = '0', allselect_alarm = '0' where email = '".$user_id."' and state = '0'";
            // echo $sql;
        }
        $update_alarm = updateQuery($sql);
        if($update_alarm){
            echo "|success";
        }
       
    }

    $sql = "select idx from work_member_alarm where email = '".$user_id."' and companyno = '".$companyno."' and todaywork_alarm = '1' and challenges_alarm = '1' and reward_alarm = '1' and like_alarm = '1' and party_alarm = '1' and allselect_alarm = '0' ";
    $member_all = selectQuery($sql);

    if($member_all['idx']){
        $sql = "update work_member_alarm set allselect_alarm = '1' where email = '".$user_id."' and companyno = '".$companyno."'";
        $update_alarm_all = updateQuery($sql);
    }
}

if($mode == "alarm_enter"){
	$idx = $_POST['idx'];
    // 0: 읽지 않음, 1:읽음
    $sql = "update work_alarm set state = '0' where email = '".$user_id."' and companyno = '".$companyno."' and idx = '".$idx."'";
    $update_alarm = updateQuery($sql);
    echo "success";
    exit;
}

if($mode == "alarm_del"){
	$idx = $_POST['idx'];
    // 0: 읽지 않음, 1:읽음
    $sql = "update work_alarm set state = '1' where email = '".$user_id."' and companyno = '".$companyno."' and idx = '".$idx."'";
    $update_alarm = updateQuery($sql);

    exit;
}
?>