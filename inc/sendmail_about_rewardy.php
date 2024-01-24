<?php

	$home_dir = str_replace( basename(__DIR__) , "" , __DIR__ );

	//연결된 도메인으로 분리
	include $home_dir . "inc_lude/conf_mysqli.php";
	include $home_dir . "inc/SHA256/KISA_SHA256.php";
	include DBCON_MYSQLI;
	include FUNC_MYSQLI;

	//로컬
	//include_once("D://inbee/bizform.smart/mail/PHPMailer/PHPMailerAutoload.php");

	//실서버
	include_once($home_dir . "PHPMailer/libphp-phpmailer/PHPMailerAutoload.php");


	//가입자 이메일
	$to_email = $_POST['mail'];

	//발신자명
	$send_name = "리워디";

	//발신자레벨
	$highlevel = "0";

	//발신자이메일주소 
	//inc_lude/conf.php 설정
	$send_email = $send_email;

	//smtp 메일계정
	$smtp_email = $smtp_email;

	//발신자명
	$company = $_POST['company'];

	$send_company = "(주)비즈폼";

	//$decrypted = Decrypt($encrypted, $secret_key, $secret_iv);
	//	echo "복호화 문자열 => " .$decrypted. "\n";


	//메일주소
	$sql = "select idx from work_member where state='0' and email='".$to_email."'";
	$mem_info = selectQuery($sql);
	if($mem_info['idx']){
		echo "over";
		exit;
	}



	//데이터저장
	$sql = "insert into work_member(state, email, company, companyno, highlevel)";
	$sql = $sql ." values('1','".$to_email."','".$company."','0', '".$highlevel."')";
	$insert_idx = insertIdxQuery($sql);
	if($insert_idx){

		$title = "리워디 인증 이메일입니다.";
		$secret = "send_email=".$send_email."&to_email=".$to_email."&sendno=".$insert_idx;
		$encrypted = Encrypt($secret);


		//include str_replace( basename(__DIR__) , "", __DIR__ ) ."layer/mail_send_about.php";
		//$contents = $mail_html;
		
		//$contents = "안녕하세요.\n\n".$send_name."에서 발송되었습니다.\n\n아래 링크를 클릭하여 인증 바랍니다.\n\n<span style=\"color: red\"><a href='".$home_url."/team/?".$encrypted."' target=\"_blank\">사용자 인증</a></span>";
		$location_url = $home_url."/team/?".$encrypted;
		
		include str_replace( basename(__DIR__) , "", __DIR__ ) ."layer/mail_send_join_auth.php";
		$contents = $mail_html;

		//발신자이름, 발신자이메일, 수신자이메일, 메일제목, 메일내용
		$result = mailer($send_name, $smtp_email, $to_email, $title, $contents);

		//발송결과
		if($result == '1'){
			echo "ok";
			$state = "1";
		}else{
			echo "fail";
			$state = "2";
		}

		//메일전송 결과 업데이트, 메일발송횟수, 메일발송IP, 메일발송일자
		$sql = "update work_member set state='".$state."', send_mail_cnt = send_mail_cnt + 1 , sender_ip='".LIP."', mail_send_regdate=".DBDATE." where idx='".$insert_idx."'";
		$res = updateQuery($sql);
		exit;
	}

?>
