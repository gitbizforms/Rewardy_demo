<?php

include str_replace( basename(__DIR__) , "", __DIR__ ) ."inc_lude/conf.php";
include DBCON;
include FUNC;


//결제
$mode =$_POST['mode'];

if($mode == "sendmail"){

	//로컬
	//include_once("D://inbee/bizform.smart/mail/PHPMailer/PHPMailerAutoload.php");

	//실서버
	include_once(__DIR__."\\PHPMailer\\PHPMailerAutoload.php");


	//사용자확인
	$send_user = $_COOKIE['user_id'];

	if($send_user){

		//메일발송수
		$email_total = count($_POST['email']);

		
		$sql = "select idx, email, highlevel, name, company from work_member where state='0' and email='".$send_user."'";
		$res = selectQuery($sql);
		

		if($res['idx']){


			//발신자명
			$send_name = $res['name'];

			//발신자레벨
			$highlevel = $res['highlevel'];

			//발신자이메일주소
			$send_email = $res['email'];
			
			//smtp 메일계정
			$smtp_email = "devmaster@bizforms.co.kr";

			//발신자명
			$company = $res['company'];

			

			//$decrypted = Decrypt($encrypted, $secret_key, $secret_iv);
		//	echo "복호화 문자열 => " .$decrypted. "\n";



			for($i=0; $i<$email_total; $i++){

				$to_email = $_POST['email'][$i];

				//메일발송 내역저장
				$sql = "insert into work_sendmail(state, sender_email, receive_mail, sender_name, company, highlevel, sender_ip)";
				$sql = $sql ." values('0','".$send_email."','".$to_email."','".$send_name."','".$company."','".$highlevel."','".LIP."')";
				$insert_idx = insertIdxQuery($sql);

				$secret = "send_email=".$send_email."&to_email=".$to_email."&sendno=".$insert_idx;
				$encrypted = Encrypt($secret);

				$title = "오늘일 초대 이메일입니다.";
				$contents = "안녕하세요.\n\n".$company."에서 발송되었습니다.\n\n아래 링크를 클릭하여 인증 바랍니다.\n\n<span style=\"color: red\"><a href='http://www.todaywork.co.kr/admin/user.php?".$encrypted."' target=\"_blank\">사용자 인증</a></span>";


				//발신자이름, 발신자이메일, 수신자이메일, 메일제목, 메일내용
				$result = mailer($send_name , $smtp_email , $to_email , $title , $contents);
				//$result = mailer("유상길","devmaster@bizforms.co.kr", $send_email ,"테스트제목입니다.","테스트내용입니다");

				$sql = "select idx from work_sendmail where idx='".$insert_idx."'";
				$res = selectQuery($sql);


				if($result == 1){
					$state = "0";
				}else{
					$state = "2";
				}
				
				if($res['idx']){
					$sql = "update work_sendmail set state='".$state."' where idx='".$insert_idx."'";
					$res = updateQuery($sql);
				}
			}

			if($result){
				echo "ok";
				exit;
			}else{
				echo "fail";
				exit;
			}

		}
	}

	//print_r($_COOKIE);
	//$result = mailer("김상엽","devmaster@bizforms.co.kr", $email1 ,"테스트제목입니다.","테스트내용입니다");
}


function mailer($fname, $fmail, $to, $subject, $content, $type=0, $file="", $cc="", $bcc="")
{
	if ($type != 1) $content = nl2br($content);
	// type : text=0, html=1, text+html=2
	$mail = new PHPMailer(); // defaults to using php "mail()"
	$mail->IsSMTP();
	//   $mail->SMTPDebug = 2;
	$mail->SMTPSecure = "ssl";
	$mail->SMTPAuth = true;
	$mail->Host = "smtp.mailplug.co.kr";
	$mail->Port = 465;
	$mail->Username = "devmaster@bizforms.co.kr";
	$mail->Password = "MailBizdev!@#";
	$mail->CharSet = 'UTF-8';
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

?>