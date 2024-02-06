<?php

$home_dir = str_replace( basename(__DIR__) , "" , __DIR__ );

include $home_dir . "inc_lude/conf_mysqli.php";
include $home_dir . "inc/SHA256/KISA_SHA256.php";
include DBCON_MYSQLI;
include FUNC_MYSQLI;


//실서버
//include_once(__DIR__."\\PHPMailer\\PHPMailerAutoload.php");
include_once($home_dir . "PHPMailer/libphp-phpmailer/PHPMailerAutoload.php");

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


if($user_id=='kikiki798@nate.com'){
//	print "<pre>";
//	print_r($_POST);
//	print "</pre>";
//	exit;
}

if($mode == "member_email_send"){

	$send_result = array(); // 발송결과 리스트 저장.

	if($_POST['input_name']){

		$cnt = count($_POST['input_name']);

		//회원정보
		$mem_row_info = member_row_info($user_id);


		for($i=0; $i<$cnt; $i++){

			$send_row = array(); // 발송결과 행 저장.

			if($_POST['input_email'][$i] == '') continue;

			$input_name = $_POST['input_name'][$i];
			$input_team = $_POST['input_team'][$i];
			$input_email = $_POST['input_email'][$i];
			$input_sw = $_POST['input_sw'][$i];

			$sql = "select idx from work_team where state='0' and partname='".$input_team."' and companyno = '".$companyno."'";
			$team_info = selectQuery($sql);
			if(!$team_info['idx']){
				$sql = "insert into work_team(companyno, partname, ip) values('".$companyno."', '".$input_team."', '".LIP."')";
				$team_idx = insertIdxQuery($sql);
			}else{
				$team_idx = $team_info['idx'];
			}

			$title = "리워디 초대 이메일입니다.";

			//관리자 지정 여부:관리자(0), 일반(5)
			if($input_sw == true){
				$input_sw_val = "0";
			}else{
				$input_sw_val = "5";
			}

			if($mem_row_info['idx']){
				$company = $mem_row_info['company'];
				$partno = $mem_row_info['partno'];

				$team_info = team_info($input_team);

				$send_row['email'] = trim($input_email);

				//가입여부 체크
				//$sql = "select idx from work_member where state!='9' and companyno='".$companyno."' and email='".$input_email."'";
				$sql = "select idx from work_member where state!='9' and email='".$input_email."'";
				$sendmail_info = selectQuery($sql);

				if($sendmail_info['idx']){
					// echo "over|".$i;
					// echo "over";
          			$send_row['over'] = true;
					$send_result[] = $send_row;
					continue; // 가입된 메일은 메일발송 막고 다음메일 실행
					//exit;
				}

				//메일초대중(state=1)
				$sql = "select idx from work_member where state='1' and companyno='".$companyno."' and email='".$input_email."'";
				$sendmail_info = selectQuery($sql);

				if(!$sendmail_info['idx']){

					$sql = "insert into work_member(state, email, name, company, companyno, part, partno, highlevel)";
					$sql = $sql ." values('1','".$input_email."','".$input_name."','".$company."','".$companyno."', '".$input_team."','".$team_info['idx']."','".$input_sw_val."')";
					$insert_idx = insertIdxQuery($sql);

					if($insert_idx){

						$secret = "send_email=".$user_id."&to_email=".$input_email."&sendno=".$insert_idx;
						$encrypted = Encrypt($secret);


						$location_url = $home_url."/team/?".$encrypted;

						//$contents = "안녕하세요.\n\n".$company."에서 발송되었습니다.\n\n아래 링크를 클릭하여 인증 바랍니다.\n\n<span style=\"color: red\"><a href='http://demo.rewardy.co.kr/team/?".$encrypted."' target=\"_blank\">사용자 인증</a></span>";

						/*ob_start();
						include str_replace( basename(__DIR__) , "", __DIR__ ) ."layer/mail_send_about.php";
						$contents = ob_get_contents();
						ob_end_clean();*/
						include str_replace( basename(__DIR__) , "", __DIR__ ) ."layer/mail_send_member.php";
						$contents = $mail_html;

						//발신자이름, 발신자이메일, 수신자이메일, 메일제목, 메일내용
						$result = mailer($user_name, 'devmaster@bizforms.co.kr', $input_email, $title, $contents);

						//메일발송성공
						if($result == '1'){
							// echo "complete";
              				$send_row['complete'] = true;
							$sql = "update work_member set send_mail_cnt = send_mail_cnt + 1 , sender_ip='".LIP."', mail_send_regdate=".DBDATE." where idx='".$insert_idx."'";
							$res = updateQuery($sql);

						}else{
						//메일발송실패
							// echo "faile";
              				$send_row['faile'] = true;
							$state = '2';
							$sql = "update work_member set state='".$state."', send_mail_cnt = send_mail_cnt + 1 , sender_ip='".LIP."', mail_send_regdate=".DBDATE." where idx='".$insert_idx."'";
							$res = updateQuery($sql);

						}
						//exit;
					}
				}
				else{

					$sql = "update work_member set sender_name='".$input_name."', part='".$input_team."', partno='".$team_info['idx']."', highlevel='".$input_sw_val."', sender_ip='".LIP."', send_mail_cnt=send_mail_cnt+1, mail_send_regdate=".DBDATE."  where idx='".$sendmail_info['idx']."'";
					$up = updateQuery($sql);
					if($up){

						$secret = "send_email=".$user_id."&to_email=".$input_email."&sendno=".$sendmail_info['idx'];
						$encrypted = Encrypt($secret);
						//$contents = "안녕하세요.\n\n".$company."에서 발송되었습니다.\n\n아래 링크를 클릭하여 인증 바랍니다.\n\n<span style=\"color: red\"><a href='http://demo.rewardy.co.kr/team/?".$encrypted."' target=\"_blank\">사용자 인증</a></span>";

						$location_url = $home_url."/team/?".$encrypted;

						//파일을 변수로 넣을경우 아래 주석 풀기//
						//ob_start();
						include str_replace( basename(__DIR__) , "", __DIR__ ) ."layer/mail_send_member.php";
						$contents = $mail_html;

						/*
						//파일을 변수로 넣을경우 아래 주석 풀기
						//$contents = ob_get_contents();
						//$contents = preg_replace("/\r\n|\r|\n/","",$contents);
						//ob_end_clean();
						*/

						//발신자이름, 발신자이메일, 수신자이메일, 메일제목, 메일내용
						$result = mailer($user_name, 'devmaster@bizforms.co.kr', $input_email, $title, $contents);
						if($result == '1'){
							// echo "complete";
              				$send_row['complete'] = true;
						}else{
							// echo "faile";
              				$send_row['faile'] = true;
						}
						//exit;
					}
				}

			}
      		$send_result[] = $send_row;


		}// for

	}

  die(json_encode($send_result));
}

?>