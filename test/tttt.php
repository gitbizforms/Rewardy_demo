<?php

$home_dir = str_replace( basename(__DIR__) , "" , __DIR__ );

include $home_dir . "inc_lude/conf_mysqli.php";
include $home_dir . "inc/SHA256/KISA_SHA256.php";
include DBCON_MYSQLI;
include FUNC_MYSQLI;


/*
print "<pre>";
print_r($_SERVER);
print "</pre>";
*/

//mode값이 없을경우 중지처리
if(!$_POST["mode"]){
//	echo "out";
//	exit;
}

$mode = $_POST["mode"];					//mode값 전달받음
$type_flag = ($chkMobile)?1:0;				//구분(0:사이트, 1:모바일)

if($_COOKIE){
	$user_id = $_COOKIE['user_id'];
	$user_name = $_COOKIE['user_name'];
	$user_level = $_COOKIE['user_level'];
	$user_part = $_COOKIE['user_part'];
}



	//include_once(__DIR__."\\PHPMailer\\PHPMailerAutoload.php");
	include_once("/home/todaywork/rewardyNAS/user/PHPMailer/libphp-phpmailer/PHPMailerAutoload.php");
	//메일발송

	/* NikoBellic's IT·Game Blog
	 * $to : 받는 사람 메일 주소
	 * $from : 보내는 사람 메일 주소
	 * $from_name : 보내는 사람 이름
	 * $subject : 메일 제목
	 * $body : 메일 내용
	 */

//발신자이름, 발신자이메일, 수신자이메일, 메일제목, 메일내용
	function sendMail($to, $from, $from_name, $subject, $body){

		/*
		echo $to;
		echo "<br>";
		echo $from;
		echo "<br>";
		echo $from_name;
		echo "<br>";
		echo $subject;
		echo "<br>";
		echo $body;
		*/

		$mail             = new PHPMailer();
		$mail->IsSMTP();                           // telling the class to use SMTP
		$mail->SMTPDebug  = 2;                     // enables SMTP debug information (for testing)
												   // 0 = 아무것도 표시하지 않음
												   // 1 = errors and messages
												   // 2 = messages only
		$mail->CharSet    = "utf-8";
		$mail->SMTPAuth   = true;                  // enable SMTP authentication
		$mail->SMTPSecure = "ssl";                 // sets the prefix to the servier (TLS는 tls 입력)
		$mail->Host       = "smtp.mailplug.co.kr";      // sets GMAIL as the SMTP server
		$mail->Port       = 465;                   // set the SMTP port for the GMAIL server (TLS는 587 입력)
		$mail->Username   = "devmaster@bizforms.co.kr";            // GMAIL username
		$mail->Password   = "MailBizdev!@";            // GMAIL password

		$mail->SetFrom($from, $from_name);

		$mail->AddReplyTo($from, $from_name);

		$mail->Subject   = $subject;

		$mail->MsgHTML($body);

		$address = $to;
		$mail->AddAddress($address);


		echo ">>>> " .$address.  $mail->Send(). "<<<<";



		if(!$mail->Send()) {
		  //echo "발송 실패: " . $mail->ErrorInfo;
		  return false;
		} else {
		  //echo "발송 완료";
		  return true;
		}
	}




	function mailer_ps_change($fname, $fmail, $to, $subject, $content, $type=0, $file="", $cc="", $bcc="")
	{
		if ($type != 1) $content = nl2br($content);
		// type : text=0, html=1, text+html=2
		$mail = new PHPMailer(true); // defaults to using php "mail()"
		$mail->IsSMTP();
		// $mail->SMTPDebug = 2;
		$mail->SMTPSecure = "ssl";
		$mail->SMTPAuth = true;
		$mail->Host = "smtp.mailplug.co.kr";
		$mail->Port = 465;
		$mail->Username = "devmaster@bizforms.co.kr";
		$mail->Password = "MailBizdev!@";
	//	$mail->CharSet = 'UTF-8';
		$mail->From = $fmail;
		$mail->FromName = $fname;
		$mail->Subject = $subject;
		$mail->AltBody = ""; // optional, comment out and test

		$auth_str = 'abcdefghijkmnopqrstuvwxyz23456789';
		$email_numbers = substr(str_shuffle($auth_str),-6); // 중복 없는 6자리 문자열

		//if (!preg_match("/^[A-Z0-9._-]+@[A-Z0-9][A-Z0-9.-]{0,61}[A-Z0-9]\.[A-Z.]{2,6}$/i",$to)) alert ("이메일 주소가 올바른지 확인하세요.");
		//$content="안녕하세요.\n"."### 운영자 입니다.\n"."아래 인증 코드를 복사하여 가입 창 E-mail Check란에 넣어주십시오.\n\n"."<b>E-mail Check: <span style=\"color: red\">$email_numbers</span></b>\n\n"."E-mail Check를 타이핑하기 힘들때는 마우스로 코드를 더블클릭 후 Ctrl-C 를 눌러서 복사한후,\n"."E-mail Check란에서 Ctrl-V를 눌러서 붙여 넣기 하시면됩니다.";
		///$content="안녕하세요.\\n\\n인증메일이 발송되었습니다.\\n\\n <span style=\"color: red\"><a href='http://www.todaywork.co.kr/admin/user.php' target=\"_blank\">사용자 인증</a></span>으로 이동해 주세요.";


		$mail->msgHTML($content);
		$mail->addAddress($to);
		if ($cc){
			$mail->addCC($cc);
		}

		if ($bcc){
			$mail->addBCC($bcc);
		}
		if ($file != "") {
			foreach ($file as $f) {
				$mail->addAttachment($f['path'], $f['name']);
			}
		}
		if ( $mail->send() ){
			return true;
		}else{
			return false;
		}
	}
	
	$send_email = $user_id;

	$sql = "select idx, email, name, company, companyno from work_member where state='0' and companyno='".$companyno."' and email='".$send_email."'";
	$mem_info = selectQuery($sql);

	if($mem_info['idx']){

		$secret = "send_email=".$send_email."&companyno=".$companyno."&send=passwdreset";
		$encrypted = Encrypt($secret);


		//받는사람 이메일주소
		$to_email = $mem_info['email'];

		//발신자이메일주소
		$send_email = "manager@rewardy.co.kr";
				
		//smtp 메일계정
		$smtp_email = "devmaster@bizforms.co.kr";

		//발신자명
		$send_name = "리워디";

		//메일 제목
		$title = "리워디 비밀번호 초기화 이메일입니다.";
		$contents = "안녕하세요.\n\n".$send_name."에서 발송되었습니다.\n\n아래 링크를 클릭하면 비밀번호가 초기화 처리 됩니다.\n\n<span style=\"color: red\"><a href='http://rewardy.co.kr/team/?".$encrypted."' target=\"_blank\">비밀번호 초기화</a></span>";

		//발신자이름, 발신자이메일, 수신자이메일, 메일제목, 메일내용
		//$result = mailer($send_name, $smtp_email, $to_email, $title, $contents);

/*
		 * $to : 받는 사람 메일 주소
		 * $from : 보내는 사람 메일 주소
		 * $from_name : 보내는 사람 이름
		 * $subject : 메일 제목
		 * $body : 메일 내용
			 */

		//$result = sendMail($send_name, $smtp_email, $to_email, $title, $contents);
		$result = sendMail($to_email, $smtp_email, $send_name, $title, $contents);
		if($result == 1){
			echo "ok";
			exit;
		}else{
			echo "fail";
			exit;
		}

	}else{

		echo "not";
		exit;
	}



?>