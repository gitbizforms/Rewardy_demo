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

//print_r($_POST);
//exit;

$mode = $_POST["mode"];					//mode값 전달받음
$type_flag = ($chkMobile)?1:0;				//구분(0:사이트, 1:모바일)

if($_COOKIE){
	$user_id = $_COOKIE['user_id'];
	$user_name = $_COOKIE['user_name'];
	$user_level = $_COOKIE['user_level'];
	$user_part = $_COOKIE['user_part'];
}


//서비스가입
if($mode == "join"){

	$email = $_POST["email"];
	$name = $_POST["name"];
	$password = $_POST["password"];
	$password_chek = $_POST["password_chek"];
	$company = $_POST["corp"];
	$partname = $_POST["part"];


	//부서명체크
	if($partname){
		$sql = "select idx, partname from work_team where state='0' and companyno='".$companyno."' and partname='".$partname."'";
		$res = selectQuery($sql);
		if($res['idx']){
			$partno = $res['part'];
			$partname = $res['partname'];
		}else{
			$sql = "insert into work_team(partname,companyno) values('".$partname."','".$companyno."')";
			$res_idx = insertIdxQuery($sql);
			if($res_idx){
				$partno = $res_idx;
			}
		}
	}

	$sql = "select idx from work_member where companyno='".$companyno."' and email = '".$email."'";
	$res = selectQuery($sql);


	//회사명체크
	if($company){
		$sql = "select idx, company from work_company where state='0' and company='".$company."'";
		$res = selectQuery($sql);
		if($res['idx']){

			$companyno = $res['idx'];
			$company = $res['company'];
		}else{

			//랜덤난수 10자리 + 시간(time())
			$code = name_random_time(10);
			$sql = "insert into work_company(company,code) values('".$company."','".$code."')";
			$res_idx = insertIdxQuery($sql);
			if($res_idx){
				$companyno = $res_idx;
			}
		}

	}



	if(!$res['idx']){

		//pwdencrypt
		//pwdcompare
		$password = kisa_encrypt($password);
		$sql = "INSERT INTO work_member(email, name, password, company, companyno, highlevel, part, partno, ip, login_count) values(";
		$sql = $sql ."'".$email."',	'".$name."','".$password."','".$company."','".$companyno."','0','".$partname."','".$partno."','".LIP."','0')";
		$res = insertQuery($sql);
		if($res){

			//로그인처리
			$sql = "update work_member set login_date = ".DBDATE." , login_count = login_count + 1 where companyno='".$companyno."' and email='".$email."'";
			$return = updateQuery($sql);

			$user_id = $email;
			$user_level = '0';
			//회원아이디
			setcookie('user_id', $email, COOKIE_TIME , '/', C_DOMAIN);

			//회원이름
			setcookie('user_name', $name , COOKIE_TIME , '/', C_DOMAIN);

			//부서명
			setcookie('user_part', $partno , COOKIE_TIME , '/', C_DOMAIN);

			//회원등급(숫자일경우만)
			setcookie('user_level', $user_level , COOKIE_TIME , '/', C_DOMAIN);

			//회원회사정보
			setcookie('companyno', $companyno , COOKIE_TIME , '/', C_DOMAIN);

			//회원코인
			setcookie('user_coin', '0' , COOKIE_TIME , '/', C_DOMAIN);

			//회원부서명
			setcookie('part_name', $partname , COOKIE_TIME , '/', C_DOMAIN);

			echo "complete";
			exit;
		}

	}else{

		//가입내역 있을때
		echo "rejoin";
		exit;
	}
	exit;
}


//사용자 인증(가입처리)
if($mode == "user"){

	$company = $_POST["corp"];
	$email = $_POST["email"];
	$name = $_POST["name"];
	$partname = $_POST["part"];
	$password = $_POST["password"];
	$password_chek = $_POST["password_chek"];

	$sql = "select idx from work_member where companyno='".$companyno."' and email = '".$email."'";
	$res_info = selectQuery($sql);


	//부서명 체크
	$sql = "select idx, partname from work_team where state='0' and companyno='".$companyno."' and partname='".$partname."'";
	$res = selectQuery($sql);
	if($res['idx']){
		$partno = $res['idx'];
		$partname = $res['partname'];
	}else{
		$sql = "insert into work_team(partname,companyno) values('".$partname."','".$companyno."')";
		$res_idx = insertIdxQuery($sql);
		if($res_idx){
			$partno = $res_idx;
		}
	}


	//회사명 체크
	$sql = "select idx, company from work_company where state='0' and company='".$company."'";
	$res = selectQuery($sql);
	if($res['idx']){
		$companyno = $res['idx'];
		$company = $res['company'];
	}else{

		//랜덤난수 10자리 + 시간(time())
		$code = name_random_time(10);
		$sql = "insert into work_company(company,code) values('".$company."','".$code."')";
		$res_idx = insertIdxQuery($sql);
		if($res_idx){
			$companyno = $res_idx;
		}
	}



	if(!$res_info['idx']){

		//pwdencrypt
		//pwdcompare
		$user_level = 5;
		$password = kisa_encrypt($password);
		$sql = "INSERT INTO work_member(email, name, password, company, companyno, highlevel, part, partno, ip, login_count) values(";
		$sql = $sql ."'".$email."',	'".$name."','".$password."','".$company."', '".$companyno."', '".$user_level."','".$partname."','".$partno."','".LIP."','0')";
		$res = insertQuery($sql);

		if($res){

			//로그인처리
			$sql = "update work_member set login_date = ".DBDATE." , login_count = login_count + 1 where companyno='".$companyno."' and email='".$email."'";
			$return = updateQuery($sql);

			$user_id = $email;
			//회원아이디
			setcookie('user_id', $email, COOKIE_TIME , '/', C_DOMAIN);

			//회원이름
			setcookie('user_name', $name , COOKIE_TIME , '/', C_DOMAIN);

			//부서명
			setcookie('user_part', $partno , COOKIE_TIME , '/', C_DOMAIN);

			//회원등급(숫자일경우만)
			setcookie('user_level', $user_level , COOKIE_TIME , '/', C_DOMAIN);

			//회원회사정보
			setcookie('companyno', $companyno , COOKIE_TIME , '/', C_DOMAIN);

			//회원코인
			setcookie('user_coin', '0' , COOKIE_TIME , '/', C_DOMAIN);

			//회원부서명
			setcookie('part_name', $partname , COOKIE_TIME , '/', C_DOMAIN);


			//코인지급
			coin_add("login");

			echo "complete";
			exit;
		}

	}else{

		//인증내역 있을때
		echo "reuser";
		exit;
	}
	exit;
}


//결제
if($mode == "pay"){

	$paycnt = $_POST['paycnt'];
	if($paycnt > 0 ){
		echo "ok|".$paycnt;
		exit;
	}

	exit;
}



////////////////////////리워디 가입처리/////////////////////


//서비스가입
if($mode == "rewardy_join"){

	$email = $_POST["email"];
	$name = $_POST["name"];
	$password = $_POST["password"];
	$password_chek = $_POST["password_chek"];
	$company = $_POST["corp"];
	$corp_join = $_POST["corp_join"];
	$partname = $_POST["part"];
	$highlevel = $_POST["highlevel"];


	//회사가입 관련
	if($corp_join){

		/*$sql = "select idx, company, code from work_company where state='0' and company='".$company."'";
		$res = selectQuery($sql);
		if($res['idx']){
			$companyno = $res['idx'];
			$company = $res['company'];
			$code = $res['code'];

			//회사코드 업데이트
			//$sql = "update work_member set companyno='".$companyno."' where state='1' and companyno='0' and email='".$email."'";
			//$up = updateQuery($sql);
		*/
		//}else{


			//이메일 중복 체크
			$sql = "select idx from work_member where state='0' and email='".$email."'";
			$work_member_info = selectQuery($sql);
			if($work_member_info['idx']){
				echo "member_same";
				exit;
			}


			//랜덤난수 10자리 + 시간(time())
			$code = name_random_time(10);
			$sql = "select idx from work_company where state='0' and company='".$company."' and code='".$code."'";
			$company_info = selectQuery($sql);
			if(!$company_info['idx']){
				$sql = "insert into work_company(company,code) values('".$company."','".$code."')";
				$res_idx = insertIdxQuery($sql);
				if($res_idx){
					$companyno = $res_idx;
					//회사코드 업데이트
					$sql = "update work_member set companyno='".$companyno."' where state='1' and companyno='0' and email='".$email."'";
					$up = updateQuery($sql);
				}
			}else{
				$code = name_random_time(10);
				$sql = "select idx from work_company where state='0' and company='".$company."' and code='".$code."'";
				$company_info = selectQuery($sql);
				if(!$company_info['idx']){
					$sql = "insert into work_company(company,code) values('".$company."','".$code."')";
					$res_idx = insertIdxQuery($sql);
					if($res_idx){
						$companyno = $res_idx;
						//회사코드 업데이트
						$sql = "update work_member set companyno='".$companyno."' where state='1' and companyno='0' and email='".$email."'";
						$up = updateQuery($sql);
					}
				}
			}

			//echo $sql;
			//echo "\n";

		//}
	}else{

		//회사에 소속되는 경우
		$sql = "select idx, company, code from work_company where state='0' and company='".$company."'";
		$res = selectQuery($sql);
		if($res['idx']){
			$companyno = $res['idx'];
			$company = $res['company'];
			$code = $res['code'];

			//회사코드 업데이트
			$sql = "update work_member set companyno='".$companyno."' where state='1' and companyno='0' and email='".$email."'";
			$up = updateQuery($sql);

		}
	}

	//부서명체크
	if($partname){
		$sql = "select idx, partname from work_team where state='0' and companyno='".$companyno."' and partname='".$partname."'";
		$res = selectQuery($sql);
		if($res['idx']){
			$partno = $res['idx'];
			$partname = $res['partname'];
		}else{
			$sql = "insert into work_team(partname,companyno) values('".$partname."','".$companyno."')";
			$res_idx = insertIdxQuery($sql);
			if($res_idx){
				$partno = $res_idx;
			}
		}
	}


	$sql = "select idx from work_member where state='0' and companyno='".$companyno."' and email='".$email."'";
	$member_info = selectQuery($sql);
	if(!$member_info['idx']){

		//pwdencrypt
		//pwdcompare
		if(!$highlevel){
			$highlevel = '0';
			$user_level = '0';
		}else{
			$user_level = $highlevel;
		}

		$password = kisa_encrypt($password);
		//정보 업데이트(메일 받은 데이터 체크)
		$sql = "select idx from work_member where state='1' and companyno='".$companyno."' and email='".$email."'";
		$mb_info = selectQuery($sql);
		if($mb_info['idx']){

			$sql = "update work_member set state='0', name='".$name."', part='".$partname."', partno='".$partno."', password='".$password."', login_count='0', ip='".LIP."', regdate=".DBDATE." where idx='".$mb_info['idx']."'";
			$up = updateQuery($sql);

		}else{

			//신규가입처리
			$sql = "INSERT INTO work_member(email, name, password, company, companyno, highlevel, part, partno, ip, login_count) values(";
			$sql = $sql ."'".$email."',	'".$name."','".$password."','".$company."','".$companyno."','".$highlevel."','".$partname."','".$partno."','".LIP."','0')";
			$up = insertQuery($sql);
		}


		//가입 완료시
		if($up){

			//로그인처리 및 메일수신 완료처리
			$sql = "update work_member set login_date = ".DBDATE." , login_count = login_count + 1, mail_chk_date=".DBDATE." ,receive_name='".$name."', receive_ip='".LIP."' where companyno='".$companyno."' and email='".$email."'";
			$up = updateQuery($sql);


			//업무 최초 시작 처리
			/*$sql = "select top 1 idx, name, live_1, CONVERT(varchar(10), live_1_regdate, 120) as live_1_regdate from work_member where state='0' and companyno='".$companyno."' and email='".$user_id."'";
			$member_join_info = selectQuery($sql);

			if($member_join_info["live_1_regdate"] != TODATE && $member_join_info["live_1"] != '1'){
				$sql = "update work_member set live_1='1', live_1_regdate=".DBDATE." where state='0' and companyno='".$companyno."' and email='".$email."'";
				$up = updateQuery($sql);
				if($up){

					//역량 평가 지표 처리(live, 0001, 회원idx)
					work_cp_reward("live","0001", $email, $member_join_info['idx']);
				}
			}*/


			//실서버
			
			include_once($home_dir."/PHPMailer/libphp-phpmailer/PHPMailerAutoload.php");

			//가입안내
			include str_replace( basename(__DIR__) , "", __DIR__ ) ."layer/mail_join_complete.php";
			$contents = $mail_html;

			$title = "리워디 가입안내";
			//발신자이름, 발신자이메일, 수신자이메일, 메일제목, 메일내용
			$result = mailer($name, $smtp_email, $email, $title, $contents);


			//회원아이디
			setcookie('user_id', $email, COOKIE_TIME , '/', C_DOMAIN);

			//회원이름
			setcookie('user_name', $name , COOKIE_TIME , '/', C_DOMAIN);

			//회원이름
			setcookie('user_part', $partno , COOKIE_TIME , '/', C_DOMAIN);

			//부서명
			setcookie('part_name', $partname , COOKIE_TIME , '/', C_DOMAIN);

			//회원등급(숫자일경우만)
			setcookie('companyno', $companyno , COOKIE_TIME , '/', C_DOMAIN);

			//회원등급(숫자일경우만)
			setcookie('user_level', $user_level , COOKIE_TIME , '/', C_DOMAIN);

			//회사폴더명
			setcookie('comfolder', $code , COOKIE_TIME , '/', C_DOMAIN);

			echo "complete";
			exit;
		}

	}else{

		//가입내역 있을때
		echo "rejoin";
		exit;
	}
	exit;
}






//리워디 맴버 추가 메일 발송
if($mode == "rewardy_member_add"){

	$paycnt = $_POST['paycnt'];
	if($paycnt > 0 ){
		echo "ok|".$paycnt;
		exit;
	}

	exit;
}












//수정하기
if($mode == "edit"){

	$email = $user_id;					//회원이메일
	$name = $user_name;					//회원이름
	$highlevel = $highlevel;			//회원레벨

	$idx = $_POST['idx'];
	$idx = preg_replace("/[^0-9]/", "", $idx);

	$contents1 = $_POST['contents1'];
	$contents1 = stripslashes(nl2br($contents1));

	$contents2 = $_POST['contents2'];
	$contents2 = stripslashes(nl2br($contents2));

	$query = "";
	if($contents1){
		$query .= "contents='".$contents1."',";
	}

	if($contents2){
		$query .= "contents1='".$contents2."',";
	}

	$sql = "select idx from work_todaywork where idx='".$idx."' and companyno='".$companyno."' and email='".$email."'";
	$res = selectQuery($sql);



	if($res['idx']){
		$sql = "update work_todaywork set ".$query." editdate=".DBDATE." where companyno='".$companyno."' and idx='".$res['idx']."'";
		$res = updateQuery($sql);
		if($res){
			echo "complete";
			exit;
		}
	}

	exit;
}




//일정 완료
if($mode == "date_write"){

	$email = $user_id;				//회원이메일
	$name = $user_name;				//회원이름

	$highlevel = $highlevel;		//회원레벨
	$work_flag = "1";				//업무구분(0:기본, 1:일정, 2:업무요청, 3:목표)
	$part_flag = $user_part;		//부서/팀별(1:경영지원팀, 2:운영기획팀, 3:고객지원팀, 4:마케팅팀, 5:콘텐츠팀, 6:디자인팀, 7:개발팀, 8:기타)

	$date_sdate = $_POST['date_sdate'];
	$date_stime = $_POST['date_stime'];

	if($date_sdate){
		$wdate = $date_sdate;
		$wdate = str_replace(".","-",$wdate);
	}else{
		$wdate = $_POST['wdate'];
		$wdate = str_replace(".","-",$wdate);
	}

	if(!$email){
		echo "logout";
		exit;
	}

	for($i=0; $i<count($_POST['contents']); $i++){
		$contents = $_POST['contents'][$i];
		$contents = nl2br($contents);
		$sql = "insert into work_todaywork(companyno, email, name, highlevel, type_flag, work_flag, part_flag, contents, req_date, req_stime, workdate, ip)";
		$sql = $sql .=" values('".$companyno."','".$email."','".$name."','".$highlevel."','".$type_flag."','".$work_flag."','".$part_flag."','".$contents."','".$date_sdate."','".$date_stime."','".$wdate."','".LIP."')";
		$res = insertQuery($sql);
	}

	if($res){
		echo "complete";
		exit;
	}

	exit;
}


if($mode == "workdate"){

	if(!$_POST['workdate']){
		echo 'workdate_not';
		exit;
	}

	$listidx = $_POST['listidx'];
	$workdate = $_POST['workdate'];
	$listidx = preg_replace("/[^0-9]/", "", $listidx);

	if($listidx && $workdate && $user_id){

		$sql = "select idx from work_todaywork where state='0' and companyno='".$companyno."' and idx='".$listidx."' and email='".$user_id."'";
		$work_res = selectQuery($sql);
		if($work_res['idx']){
			$sql = "update work_todaywork set workdate='".$workdate."' where companyno='".$companyno."' and idx='".$work_res['idx']."'";
			$res = updateQuery($sql);

			if($res){
				echo "complete";
				exit;
			}
		}
	}else{

		echo "data_not";
		exit;
	}

	exit;
}


//오늘업무 업무요청
if($mode == "req_write"){

	$email = $user_id;				//회원이메일
	$name = $user_name;				//회원이름

	$highlevel = $highlevel;		//회원레벨
	$work_flag = "2";				//업무구분(0:기본, 1:일정, 2:업무요청, 31:일일목표 , 32:주간목표, 33:성과목표)
	$part_flag = $user_part;		//부서/팀별(1:경영지원팀, 2:운영기획팀, 3:고객지원팀, 4:마케팅팀, 5:콘텐츠팀, 6:디자인팀, 7:개발팀, 8:기타)

	$req_date = $_POST['req_date'];
	$req_stime = $_POST['req_stime'];
	$req_etime = $_POST['req_etime'];

	if ($req_date){
		$req_date = str_replace(".","-",$req_date);
	}

	if(!$email){
		echo "logout";
		exit;
	}

	for($i=0; $i<count($_POST['contents']); $i++){
		$contents = $_POST['contents'][$i];
		$contents = nl2br($contents);
		$sql = "insert into work_todaywork(companyno, email, name, highlevel, type_flag, work_flag, part_flag, contents, workdate, ip)";
		$sql = $sql .=" values('".$companyno."','".$email."','".$name."','".$highlevel."','".$type_flag."','".$work_flag."','".$part_flag."','".$contents."','".$req_date."','".LIP."')";
		$res_idx = insertIdxQuery($sql);
	}


	//배열값확인
	if (is_array($_POST['chk']) == true){

		//회원정보 확인
		$work_mem_idx = @implode("','",$_POST['chk']);
		$sql = "select idx, email, name from work_member where state='0' and companyno='".$companyno."' and idx in ('".$work_mem_idx."')";
		$res = selectAllQuery($sql);

		for($i=0; $i<count($res['idx']); $i++){
			$req_mem_idx[] = $res['idx'][$i];
			$req_mem_email[] = $res['email'][$i];
			$req_mem_name[] = $res['name'][$i];
		}

		$req_memidx_arr = implode(",", $req_mem_idx);
		$req_email_arr = implode(",", $req_mem_email);
		$req_name_arr = implode(",", $req_mem_name);

		$sql = "insert into work_req_write(work_idx, user_idx, type_flag, email, name, req_date, req_stime, req_etime, ip)";
		$sql = $sql .= " values('".$res_idx."','".$req_memidx_arr."','".$type_flag."','".$req_email_arr."','".$req_name_arr."','".$req_date."','".$req_stime."','".$req_etime."','".LIP."');";
		//$req_res = insertQuery($sql);
		$req_idx = insertIdxQuery($sql);

	}

	if($req_idx){

		//업무요청등록
		coin_add("works_req_write", $req_idx);
		echo "complete";
		exit;
	}

	exit;
}



//목표 작성완료
if($mode == "goal_write"){

	$email = $user_id;					//회원이메일
	$name = $user_name;					//회원이름

	$highlevel = $highlevel;			//회원레벨
	$work_flag = "3";					//업무구분(0:기본, 1:일정, 2:업무요청, 3:목표, 31:일일목표, 32:주간목표, 33:성과목표)
	$part_flag = $user_part;			//부서/팀별(1:경영지원팀, 2:운영기획팀, 3:고객지원팀, 4:마케팅팀, 5:콘텐츠팀, 6:디자인팀, 7:개발팀, 8:기타)

	$goal1 = $_POST['goal1'];			//목표
	$goal2 = $_POST['goal2'];			//핵심결과
	$goal2 = nl2br($goal2);
	$goal3 = $_POST['goal3'];			//완료일자
	$wdate = $_POST['wdate'];
	if ($wdate){
		$wdate = str_replace(".","-",$wdate);
	}

	$sql = "insert into work_todaywork(companyno, email, name, highlevel, type_flag, work_flag, part_flag, contents, contents1, contents2, workdate, ip)";
	$sql = $sql .=" values('".$companyno."','".$email."','".$name."','".$highlevel."','".$type_flag."','".$work_flag."','".$part_flag."','".$goal1."','".$goal2."','".$goal3."', '".$wdate."','".LIP."')";
	//$res = insertQuery($sql);
	$res_idx = insertIdxQuery($sql);

	if($res_idx){
		//목표설정 및 달성
		coin_add("works_goal", $res_idx);
		echo "complete";
		exit;
	}
	exit;
}


//업무요청자 업데이트
if ($mode == "req_user_edit"){

/*
	print "<pre>";
	print_r($_POST);
	print "</pre>";
*/
	//$work_mem_idx = @implode("','",$_POST['chk']);

	$idx = $_POST['editidx'];
	$idx = preg_replace("/[^0-9]/", "", $idx);
	$requsechk = @implode("','",$_POST['requsechk']);

	//업무요청 선택회원idx
	if($idx && $requsechk){
		$sql = "select idx, email, name from work_member where companyno='".$companyno."' and idx in ('".$requsechk."')";
		$mem_info = selectAllQuery($sql);

		if($mem_info['idx']){

			$mem_idx = @implode(",",$mem_info['idx']);
			$mem_email = @implode(",",$mem_info['email']);
			$mem_name = @implode(",",$mem_info['name']);

			//$sql = "select idx, from work_req_write where state='0' and work_idx='".$idx."'";
			$sql = "select a.idx, a.email from work_todaywork a left join work_req_write b on (a.idx=b.work_idx) where a.idx='".$idx."' and a.companyno='".$companyno."' and a.email='".$user_id."'";
			$res_work_info = selectQuery($sql);
			if($res_work_info['idx']){
				$sql = "update work_req_write set user_idx='".$mem_idx."', email='".$mem_email."', name='".$mem_name."', editdate=".DBDATE." where work_idx='".$res_work_info['idx']."'";
				$res = updateQuery($sql);
				if($res){
					echo "complete";
					exit;
				}
			}
		}
	}
	exit;
}




//오늘일 완료하기
if($mode == "list_complete"){

	$idx = $_POST['idx'];
	$idx = preg_replace("/[^0-9]/", "", $idx);
	if($idx){
		$sql = "select idx, work_flag, email from work_todaywork where state='0' and companyno='".$companyno."' and idx='".$idx."'";
		$res = selectQuery($sql);

		//업무글이 있으면
		if($res['idx']){

			//업무요청
			if($res['work_flag'] == '2'){

				$sql = "select idx, email from work_req_write where state='0' and work_idx='".$res['idx']."'";
				$req_res = selectQuery($sql);
				if($req_res['idx']){

					//로그인아이디와 업무요청 받은 아이디가 같으면 업데이트 진행
					//요쳥받은사람이 여러명 일때 콤마 로 구분함
					if(strpos($req_res['email'], ",") !== false){
						$req_email2 = explode(",",$req_res['email']);
						if(@in_array($user_id ,$req_email2)){
							$sql = "update work_todaywork set state='1', editdate=".DBDATE." where idx='".$idx."'";
							$res = updateQuery($sql);
							if($res){
								//업무요청 완료
								coin_add("works_req_complete1" , $idx);
								echo "complete";
								exit;
							}
						}

					}else{

						//로그인아이디와 업무요청 받은 아이디가 같으면 업데이트 진행
						if( $user_id == trim($req_res['email'])){
							$sql = "update work_todaywork set state='1', editdate=".DBDATE." where idx='".$idx."'";
							$res = updateQuery($sql);
							if($res){
								//업무요청 완료
								coin_add("works_req_complete1" , $idx);
								echo "complete";
								exit;
							}
						}
					}
				}

			}else if($res['work_flag'] == '3'){
				//목표완료

				$sql = "update work_todaywork set state='1', editdate=".DBDATE." where state='0' and idx='".$idx."' and email='".$user_id."'";
				$res = updateQuery($sql);

				if($res){
					$sql = "select count(1) as cnt from work_todaywork where state ='1' and work_flag='3' and email='".$user_id."'";
					$sql = $sql .=" and DATE_FORMAT(workdate, '%Y-%m-%d') = DATE_FORMAT(now(), '%Y-%m-%d')";
					$work_info = selectQuery($sql);
					if($work_info['cnt'] == '1'){
						coin_add("works_goal_complete", $idx);
						echo "complete";
						exit;
					}

					echo "complete";
					exit;
				}

			}else{

				$sql = "update work_todaywork set state='1', editdate=".DBDATE." where idx='".$idx."' and email='".$user_id."'";
				$res = updateQuery($sql);
				if($res){
					//업무완료
					coin_add("works_complete", $idx);
					echo "complete";
					exit;
				}
			}
		}
	}
	exit;
}


//오늘일 완료 해제 하기
if($mode == "list_recomplete"){

	$idx = $_POST['idx'];
	$idx = preg_replace("/[^0-9]/", "", $idx);
	if($idx){
		$sql = "select idx, work_flag, email from work_todaywork where state='1' and idx='".$idx."'";
		$res = selectQuery($sql);
		if($res['idx']){

			//업무요청
			if($res['work_flag'] == 2){
				$sql = "select idx, email from work_req_write where state='0' and work_idx='".$res['idx']."'";
				$req_res = selectQuery($sql);
				if($req_res['idx']){
					//로그인 아이디와 업무요청 받은 아이디가 동일아이디면 업데이트진행
					//요쳥받은사람이 여러명 일때 콤마 로 구분함
					if(strpos($req_res['email'], ",") !== false){
						$req_email2 = explode(",",$req_res['email']);
						if(@in_array($user_id ,$req_email2)){
							$sql = "update work_todaywork set state='0', editdate=".DBDATE." where idx='".$idx."'";
							$res = updateQuery($sql);
							if($res){
								//업무요청 완료
								coin_del($idx);
								echo "complete";
								exit;
							}
						}
					}else{

						if( $user_id == trim($req_res['email'])){
							$sql = "update work_todaywork set state='0', editdate=".DBDATE." where idx='".$idx."'";
							$res = updateQuery($sql);
							if($res){
								//업무요청 완료
								coin_del($idx);
								echo "complete";
								exit;
							}
						}

					}
				}

			}else{
				$sql = "update work_todaywork set state='0', editdate=".DBDATE." where idx='".$res['idx']."' and email='".$user_id."'";
				$res = updateQuery($sql);
				if($res){
					coin_del($idx);
					echo "complete";
					exit;
				}
			}
		}
	}
	exit;
}




//오늘일 삭제하기
if($mode == "list_del"){

	$idx = $_POST['idx'];
	$idx = preg_replace("/[^0-9]/", "", $idx);
	if($idx){
		$sql = "select idx, state from work_todaywork where idx='".$idx."' and email='".$user_id."'";
		$res = selectQuery($sql);
		if($res['idx']){

			$state = $res['state'];
			$sql = "update work_todaywork set state='9' where idx='".$res['idx']."'";
			$res = updateQuery($sql);

			if($res){
				coin_del($idx , $state);
				echo "complete";
				exit;
			}
		}
	}

	exit;
}


//오늘일 내일로 미루기
if($mode == "list_yesterday"){

	$idx = $_POST['idx'];
	$idx = preg_replace("/[^0-9]/", "", $idx);
	if($idx){
		$sql = "select idx from work_todaywork where idx='".$idx."' and email='".$user_id."'";
		$res = selectQuery($sql);
		if($res['idx']){
			$sql = "update work_todaywork set state='0', editdate=".DBDATE.", workdate = DATE_FORMAT(date_add(workdate, INTERVAL 1 DAY), '%Y-%m-%d') where idx='".$res['idx']."'";
			$res = updateQuery($sql);

			if($res){
				echo "complete";
				exit;
			}
		}
	}

	exit;

}


//코인보상하기
if($mode == "coin_reward"){

	$coin_user = $_POST['coin_user'];
	$coin_point = $_POST['coin_point'];
	$coin_info = $_POST['coin_info'];
	$coin_point = preg_replace("/[^0-9]/", "", $coin_point);

	$sql = "select idx, name from work_member where state='0' and email='".$coin_user."'";
	$mem_info = selectQuery($sql);
	if($mem_info['idx']){
		$coin_name = $mem_info['name'];
	}

	//오늘 코인보상횟수
	//$sql = "select count(1) as coin_cnt from work_coininfo where state='0' and email='".$coin_user."' and convert(char(10), regdate, 120) = convert(char(10),getdate(), 120)";
	//$res = selectQuery($sql);
	//echo " cnt :: " . $res['coin_cnt'];

	//일반권한
	if($user_level == 5){

		$sql = "select idx, coin from work_member where state='0' and companyno='".$companyno."' and email='".$user_id."'";
		$res = selectQuery($sql);
		if($res['idx'] && $res['coin'] > 0 ){
			$user_coin = $res['coin'];

			if($user_coin >= $coin_point){

				//코인차감내역
				$sql = "insert into work_coininfo(state, companyno, email, name, reward_user, reward_name, coin, memo, workdate, ip) values('0', '".$companyno."', '".$user_id."', '".$user_name."', '".$coin_user."','".$coin_name."','".$coin_point."','".$coin_info."','".TODATE."','".LIP."')";
				$coininfo = insertQuery($sql);
				if($coininfo){
					//코인차감
					$sql = "update work_member set coin = coin - '".$coin_point."' where state='0' and email='".$user_id."'";
					$res = updateQuery($sql);
				}

				//코인적립
				$sql = "insert into work_coininfo(state, companyno, email, name, reward_user, reward_name, coin, memo, workdate, ip) values('0', '".$companyno."', '".$coin_user."', '".$coin_name."', '".$user_id."', '".$user_name."','".$coin_point."','".$coin_info."','".TODATE."','".LIP."')";
				$coininfo = insertQuery($sql);
				if($coininfo){
					$sql = "update work_member set coin = coin + '".$coin_point."' where state='0' and email='".$coin_user."'";
					$res = updateQuery($sql);
					echo "complete";
					exit;
				}
			}else{
				echo "coin_min";
				exit;
			}
		}

	//관리권한
	}else if($user_level == 0){

		$sql = "insert into work_coininfo(state, companyno, email, reward_user, reward_name, coin, memo, workdate, ip) values('0', '".$companyno."', '".$user_id."', '".$coin_user."', '".$user_name."', '".$coin_point."', '".$coin_info."','".TODATE."','".LIP."')";
		$coininfo = insertQuery($sql);
		if($coininfo){
			$sql = "update work_member set coin = coin + '".$coin_point."' where state='0' and email='".$coin_user."'";
			$res = updateQuery($sql);

			echo "complete";
			exit;
		}
		exit;
	}

	exit;
}



//챌린지 수정하기 권한체크
if($mode == "challenges_edit_check"){
	
	$chall_idx = $_POST['chall_idx'];
	$chall_idx = preg_replace("/[^0-9]/", "", $chall_idx);
	if($chall_idx){
		$sql = "select idx from work_challenges where state='0' and companyno='".$companyno."' and idx='".$chall_idx."'";
		$res = selectQuery($sql);

		if($res['idx']){
			echo "complete";
			exit;
		}else{


			if(@in_array($user_id , $edit_user_arr)){
				echo "complete";
				exit;
			}else{
				echo "not";
				exit;
			}
		}
	}
	exit;
}



//챌린지등록
if($mode == "chall_write"){

/*
(
    [mode] => chall_write
    [title] => 챌린지명
    [date1] => 2021-21-12
    [date2] => 2112-11-21
    [contents] => 챌린지 내용
    [chall_day] => daily
    [action1] => 행동지침1
    [action2] => 행동지침2
    [h4] => 2000
)*/

	$title = $_POST['title'];
	$date1 = $_POST['date1'];
	$date2 = $_POST['date2'];

	$contents = nl2br($_POST['contents']);
	$chall_day = $_POST['chall_day'];

	if($chall_day == "one"){
		$day_type="0";
	}else if($chall_day == "daily"){
		$day_type="1";

	}

	$action1 = $_POST['action1'];
	$action2 = $_POST['action2'];
	$coin = $_POST['h4'];

	$coin = preg_replace("/[^0-9]/", "", $coin);

	$sql = "insert into work_challenges(email, name, title, sdate, edate, day_type, contents, action1, action2, coin, ip)";
	$sql = $sql .= " values('".$user_id."','".$user_name."', '".$title."','".$date1."','".$date2."','".$day_type."','".$contents."','".$action1."','".$action2."','".$coin."','".LIP."')";

	$res = insertQuery($sql);
	if($res){
		echo "complete";
		exit;
	}
	exit;
}



//챌린지수정
if($mode == "chall_edit"){
/*
(
	[mode] => chall_edit
	[title] => 챌린지명
	[date1] => 2021-21-12
	[date2] => 2112-11-21
	[contents] => 챌린지 내용
	[chall_day] => daily
	[action1] => 행동지침1
	[action2] => 행동지침2
	[h4] => 2000
)*/


	$chall_title = $_POST['title'];
	$chall_idx = $_POST['chall_idx'];
	$date1 = $_POST['date1'];
	$date2 = $_POST['date2'];

	$chall_contents = nl2br($_POST['contents']);
	$chall_day = $_POST['chall_day'];

	if($chall_day == "one"){
		$day_type="0";
	}else if($chall_day == "daily"){
		$day_type="1";

	}

	$chall_action1 = $_POST['action1'];
	$chall_action2 = $_POST['action2'];
	$chall_coin = $_POST['h4'];
	$chall_chk = $_POST['chk01'];

	$chall_idx = preg_replace("/[^0-9]/", "", $chall_idx);
	$chall_coin = preg_replace("/[^0-9]/", "", $chall_coin);

	$sql = "select idx from work_challenges where idx='".$chall_idx."'";
	$challenges_info = selectQuery($sql);

	if($challenges_info['idx']){
		$sql = "update work_challenges set title='".$chall_title."', sdate='".$date1."', edate='".$date2."', day_type='".$day_type."', contents='".$chall_contents."'";
		$sql = $sql .= ", action1='".$chall_action1."', action2='".$chall_action2."', outputchk='".$chall_chk."', coin='".$chall_coin."' where idx='".$chall_idx."' and email='".$user_id."'";
		$res = updateQuery($sql);
	}

	if($res){
		echo "complete";
		exit;
	}

	exit;
}


//챌린지 완료하기
if($mode =="challenges_complete"){

	$chall_idx = $_POST['chall_idx'];
	$chall_idx = preg_replace("/[^0-9]/", "", $chall_idx);
/*
	print "<pre>";
	print_r($_FILES);
	print "</pre>";

	<pre>Array
(
    [file] => Array
        (
            [name] => 68f58ed0-90f6-11eb-9aea-e98b2a754893.jpg
            [type] => image/jpeg
            [tmp_name] => C:\Windows\Temp\php9A1C.tmp
            [error] => 0
            [size] => 7316
        )

)
</pre>


	$uploadfile = $_FILES['upload']['name'];
	if(move_uploaded_file($_FILES['upload']['tmp_name'],$uploadfile)){
	 echo "파일이 업로드 되었습니다.<br />";
	 echo "<img src ={$_FILES['upload']['name']}> <p>";
	 echo "1. file name : {$_FILES['upload']['name']}<br />";
	 echo "2. file type : {$_FILES['upload']['type']}<br />";
	 echo "3. file size : {$_FILES['upload']['size']} byte <br />";
	 echo "4. temporary file size : {$_FILES['upload']['size']}<br />";
	} else {
	 echo "파일 업로드 실패 !! 다시 시도해주세요.<br />";
	}

*/
	if($chall_idx){


		$sql = "select idx from work_comment where state='0' and companyno='".$companyno."' and work_idx='".$chall_idx."' and email='".$user_id."'";
		$comment_row = selectQuery($sql);
		if(!$comment_row['idx']){
			echo "comment";
			exit;

		}


		$sql = "select idx, email, name, coin, title, sdate, edate, day_type, outputchk from work_challenges where state='0' and companyno='".$companyno."' and idx='".$chall_idx."'";
		$challenges_info = selectQuery($sql);

		if($challenges_info['idx']){
			if ($challenges_info['email'] == $user_id){
				echo "not_id";
				exit;
			}

			//챌린지 결과물 등록 체크
			if($challenges_info['outputchk'] == '1'){

				$file_upload_check = false;

				if (!$_FILES['file']['name']){
					echo "not_files1";
					exit;
				}else{

					//파일명
					$uploadfile = $_FILES['file']['name'];

					//확장자
					$ext = @end(explode('.', $uploadfile));

					//파일저장위치
					//년월일시분초_이메일_challenges_고유번호.확장자
					$renamefile = date("YmdHis")."_{$user_id}_challenges_{$chall_idx}.{$ext}";
					$file_save_dir = "..\\data\\challenges\\";

					$file_upload_check = true;

				}
			}

			//챌린지보상내역
			$coin_info = "[챌린지] ".$challenges_info['title'];

			//챌린지를 작성 아이디의 코인정보
			$mem_coin = email_coin($challenges_info['email']);

			//챌린지 기간내에 참여일때
			if( TODATE >= $challenges_info['sdate'] && TODATE <= $challenges_info['edate'] ){

				//기간내 한번만
				if($challenges_info['day_type'] == '0'){

					//챌린지를 작성한 회원코인이 보상코인보다 크거나 같은경우
					if($mem_coin >= $challenges_info['coin']){

						$sql = "select idx from work_challenges_com where state='1' and companyno='".$companyno."' and email='".$user_id."' and challenges_idx='".$chall_idx."'";
						$res = selectQuery($sql);
						if(!$res['idx']){

							if($file_upload_check == true){
								$result = file_upload_send( $_FILES['file']['tmp_name'], $file_save_dir. $renamefile );

								if(!$result){
									echo "not_files2";
									exit;
								}

							}

							$sql = "insert into work_challenges_com(email, name, state, challenges_idx, ip)";
							$sql = $sql .= " values('".$user_id."', '".$user_name."', '1', '".$chall_idx."', '".LIP."')";
							$res = insertQuery($sql);
							if($res){

								//챌린지 작성한 회원의 코인차감
								$sql = "insert into work_coininfo(state, work_idx, companyno, email, name, reward_user, reward_name, coin, memo, workdate, ip) values('0', '".$chall_idx."', '".$companyno."','".$challenges_info['email']."', '".$challenges_info['name']."', '".$user_id."', '".$user_name."', '".$challenges_info['coin']."', '".$coin_info."','".TODATE."','".LIP."')";
								$coin_res = insertQuery($sql);
								if($coin_res){
									$sql = "update work_member set coin = coin - '".$challenges_info['coin']."' where state='0' and email='".$challenges_info['email']."'";
									$res_info1 = updateQuery($sql);
								}

								//챌린지 완료한 회원의 코인적립
								$sql = "insert into work_coininfo(state, work_idx, companyno, email, name, reward_user, reward_name, coin, memo, workdate, ip) values('0', '".$chall_idx."', '".$companyno."','".$user_id."', '".$user_name."', '".$challenges_info['email']."', '".$challenges_info['name']."', '".$challenges_info['coin']."', '".$coin_info."','".TODATE."','".LIP."')";
								$coin_res = insertQuery($sql);
								if($coin_res){
									$sql = "update work_member set coin = coin + '".$challenges_info['coin']."' where state='0' and email='".$user_id."'";
									$res_info2 = updateQuery($sql);
								}

								if($res_info1 && $res_info2){
									echo "complete";
									exit;
								}
							}
						}else{
							echo "chall_complete";
							exit;
						}

					}else{
						echo 'coin_not';
						exit;
					}

				}else if($challenges_info['day_type'] == '1'){
				//매일

					$sql = "select idx from work_challenges_com where state='1' and email='".$user_id."' and challenges_idx='".$chall_idx."'";
					$sql = $sql .=" and DATE_FORMAT(regdate, '%Y-%m-%d') = DATE_FORMAT(now(), '%Y-%m-%d')";
					$res = selectQuery($sql);
					if(!$res['idx']){
						$sql = "insert into work_challenges_com(email, name, state, challenges_idx, ip)";
						$sql = $sql .= " values('".$user_id."', '".$user_name."', '1', '".$chall_idx."', '".LIP."')";
						$res = insertQuery($sql);
						if($res){

							//챌린지 작성한 회원의 코인차감
							$sql = "insert into work_coininfo(state, work_idx, companyno, email, name, reward_user, reward_name, coin, memo, workdate, ip) values('0', '".$chall_idx."', '".$companyno."', '".$challenges_info['email']."', '".$challenges_info['name']."', '".$user_id."', '".$user_name."', '".$challenges_info['coin']."', '".$coin_info."','".TODATE."','".LIP."')";
							$coin_res = insertQuery($sql);
							if($coin_res){
								$sql = "update work_member set coin = coin - '".$challenges_info['coin']."' where state='0' and email='".$challenges_info['email']."'";
								$res_info1 = updateQuery($sql);
							}

							//챌린지 완료한 회원의 코인적립
							$sql = "insert into work_coininfo(state, work_idx, companyno, email, name, reward_user, reward_name, coin, memo, workdate, ip) values('0', '".$chall_idx."', '".$companyno."', '".$user_id."', '".$user_name."', '".$challenges_info['email']."', '".$challenges_info['name']."','".$challenges_info['coin']."', '".$coin_info."','".TODATE."','".LIP."')";
							$coin_res = insertQuery($sql);
							if($coin_res){
								$sql = "update work_member set coin = coin + '".$challenges_info['coin']."' where state='0' and email='".$user_id."'";
								$res_info2 = updateQuery($sql);
							}

							if($res_info1 && $res_info2){
								echo "complete";
								exit;
							}
						}
					}else{
						echo "chall_complete";
						exit;
					}
				}

			}else if( TODATE <= $challenges_info['sdate'] ){

				//시작날짜가 오늘날짜보다 작은경우
				//준비중인 챌린지
				echo "chall_ready";
				exit;
			}else{
				echo "expire_day";
				exit;
			}
		}
	}
	exit;
}


//챌린지 삭제하기
if($mode == "challenges_del2"){

	$chall_idx = $_POST['chall_idx'];
	$chall_idx = preg_replace("/[^0-9]/", "", $chall_idx);

	$sql = "select idx, email, name, coin, title, sdate, edate, day_type from work_challenges where state='0' and idx='".$chall_idx."'";
	$challenges_info = selectQuery($sql);

	$sql = "select idx from work_challenges_com where state='0' and challenges_idx='".$chall_idx."' and email='".$user_id."'";
	$chall_com = selectAllQuery($sql);

	if(!$chall_com['idx']){
		if($user_id == $challenges_info['email']){

			//챌린지목록
			$sql = "update work_challenges set state='9' where idx='".$chall_idx."' and email='".$user_id."'";
			$res1 = updateQuery($sql);

			//챌린지 참여자대상
			$sql = "update work_challenges_user set state='9' where state='0' and challenges_idx='".$chall_idx."'";
			$res2 = updateQuery($sql);

			//기간내 매일일 경우
			if($challenges_info['type']=='1'){
				$sql = "select idx from work_challenges_day where state='0' and challenges_idx='".$chall_idx."'";
				$res_info = selectAllQuery($sql);
				if($res_info['idx']){
					//챌린지 기간(요일)
					$sql = "update work_challenges_day set state='9' where state='0' and challenges_idx='".$chall_idx."'";
					$res3 = updateQuery($sql);
				}
			}

			if(($res1 && $res2 ) || $res3){
				echo "complete";
				exit;
			}

		}else{
			echo "not_id";
			exit;
		}
	}
	exit;
}


//챌린지 삭제하기
if($mode == "challenges_del"){

	$chall_idx = $_POST['chall_idx'];
	$chall_idx = preg_replace("/[^0-9]/", "", $chall_idx);

	$template_idx = $_POST['template_idx'];

	$sql = "select idx, email, sdate, edate, attend_type, template, total_max_coin from work_challenges where state='0' and companyno='".$companyno."' and idx='".$chall_idx."' ";
	// $sql = $sql .= "and email='".$user_id."'";
	$challenges_info = selectQuery($sql);
	if($challenges_info['idx']){

		//테마여부 체크
		$template = $challenges_info['template'];

		//챌린지 인증메시지
		$sql = "select idx from work_challenges_result where state in ('1','2') and companyno='".$companyno."' and challenges_idx='".$chall_idx."'";
		$chall_comment = selectQuery($sql);

		if(!$chall_comment['idx']){

			//챌린지 참여자정보 삭제
			$sql = "update work_challenges_user set state='9' where state='0' and companyno='".$companyno."' and challenges_idx='".$chall_idx."'";
			$res_user = updateQuery($sql);

			//챌린지 참여 인증메시지 삭제
			$sql = "update work_challenges_result set state='9' where companyno='".$companyno."' and challenges_idx='".$chall_idx."'";
			$res_comment = updateQuery($sql);

			$sql = "update work_challenges set state='9', editdate=".DBDATE." where companyno='".$companyno."' and idx='".$chall_idx."'";
			$res_chall = updateQuery($sql);

			//챌린지 파일첨부 삭제
			$sql = "update work_filesinfo_file set state='9' where companyno='".$companyno."' and idx='".$chall_idx."'";
			$res_chall_file = updateQuery($sql);

			//챌린지 파일첨부 삭제
			$sql = "update work_filesinfo_img set state='9' where companyno='".$companyno."' and idx='".$chall_idx."'";
			$res_chall_img = updateQuery($sql);

			//챌린지로 인한 코인 환급
			$sql = "update work_coininfo set state='9' where companyno='".$companyno."' and work_idx='".$chall_idx."' ";
			$res_chall_coin = updateQuery($sql);

			$sql = "select comcoin from work_member where state='0' and companyno='".$companyno."' and email = '".$user_id."'";
			$query = selectQuery($sql);
			$del_coin = $challenges_info['total_max_coin'] + $query['comcoin'];

			$sql = "update work_member set comcoin = '".$del_coin."' where companyno='".$companyno."' and email = '".$user_id."' ";
			$res_chall_coin2 = updateQuery($sql);

			if($template_idx=='1'){
				$sql = "update work_challenges_thema_list set state = '9' where challenges_idx = '".$chall_idx."'";
				$thema_disable = updateQuery($sql);
			}

			//챌린지 알림삭제
			$sql = "select idx from work_todaywork where state='0' and companyno='".$companyno."' and notice_flag='1' and work_idx='".$chall_idx."'";
			$work_info = selectQuery($sql);
			if($work_info['idx']){
				$sql = "update work_todaywork set state='9', editdate=".DBDATE." where work_idx='".$chall_idx."'";
				$up = updateQuery($sql);
			}

			if($res_user && $res_chall){
				echo "complete|".$template;
				exit;
			}
		}else{
			echo "datarow";
			exit;
		}

	}else{
		echo "not";
		exit;
	}



	if(!$chall_com['idx']){
		if($user_id == $challenges_info['email']){

			//챌린지목록
			$sql = "update work_challenges set state='9' where idx='".$chall_idx."' and companyno='".$companyno."' and email='".$user_id."'";
			$res1 = updateQuery($sql);

			//챌린지 참여자대상
			$sql = "update work_challenges_user set state='9' where state='0' and companyno='".$companyno."' and challenges_idx='".$chall_idx."'";
			$res2 = updateQuery($sql);

			//기간내 매일일 경우
			if($challenges_info['type']=='1'){
				$sql = "select idx from work_challenges_day where state='0' and challenges_idx='".$chall_idx."'";
				$res_info = selectAllQuery($sql);
				if($res_info['idx']){
					//챌린지 기간(요일)
					$sql = "update work_challenges_day set state='9' where state='0' and challenges_idx='".$chall_idx."'";
					$res3 = updateQuery($sql);
				}
			}

			if(($res1 && $res2 ) || $res3){
				echo "complete";
				exit;
			}

		}else{
			echo "not_id";
			exit;
		}
	}
	exit;
}



if($mode == "challenges_comment"){

	//print_r($_POST);
	$idx = $_POST['idx'];
	$comment = $_POST['comment'];
	$type = "2";

	$sql = "select idx from work_challenges where companyno='".$companyno."' and idx='".$idx."'";
	$challenges_info = selectQuery($sql);

	if ($challenges_info['idx']){

		$emoji = "0";
		preg_match("/[\x{10000}-\x{10FFFF}]/u", $comment, $ret);
		if($ret){
			$comment = urlencode($comment);
			$emoji = "1";
		}

		$sql = "select idx from work_comment where state='0' and companyno='".$companyno."' and email='".$user_id."' and work_idx='".$idx."'";
		$comment_info = selectQuery($sql);
		//if ($comment_info['idx']){
			$sql = "insert into work_comment(type_flag, email, name, type, work_idx, emoji, contents, ip) values(";
			$sql = $sql .="'".$type_flag."','".$user_id."', '".$user_name."', '".$type."', '".$idx."', '".$emoji."', '".$comment."', '".LIP."')";
			$res = insertQuery($sql);
			if($res){
				echo "complete";
			}
		//}
	}

	exit;
}

if($mode == "circle_rank"){

	print_r($_POST);
	exit;
}




//챌린지 리스트
if($mode == "challenges_list"){

	//전체선택 탭
	$chk_tab0 = $_POST['chk_tab0']; //all : all
	$chk_tab1 = $_POST['chk_tab1']; //wait : 1
	$chk_tab2 = $_POST['chk_tab2']; //ing : 2 
	$chk_tab3 = $_POST['chk_tab3']; //comp : 3 
	$chk_tab4 = $_POST['chk_tab4']; //end : 4

	// echo $chk_tab0."|".$chk_tab1."|".$chk_tab2."|".$chk_tab3."|".$chk_tab4;
	// exit;

	//카테고리
	$cate = $_POST['cate'];

	//페이지
	$gp = $_POST['gp']; 

	//정렬
	$rank = $_POST['rank']; //참여자 많은 순 = 1, 기간 짧은 순 = 2, 코인 높은 순 = 3 , 최신 순 = 4


	//정복한 챌린지
	//내가 만든 챌린지
	$chall_type = $_POST['chall_type'];

	//검색
	$search = $_POST['search'];

	//샘플사용
	//$template = $_POST['template'];

	/*if($template){
		$template_type = "1";
	}else{
		$template_type = "0";
	}*/

	if(!$gp) {
		$gp = 1;
	}

	$pagesize = 12;						//페이지 출력갯수
	$startnum = 0;						//페이지 시작번호
	$endnum = $gp * $pagesize;			//페이지 끝번호


	//정시 출근시간(09:00) 2회이상 일경우
	//incl_ude/func.php 설정됨
	$coaching_chk = member_coaching_chk();
	//조건절
	$where = "where a.state in ('0','1')";

	$que = "";
	$que1 = "";
	$que1_group = "";


	//if($chk_tab == "all"){
	//	$where .= "";
	//}else{

		//완료한 챌린지 체크
		$sql = "select challenges_idx, state, count(1) as cnt from work_challenges_result where state='1' and companyno='".$companyno."' and email='".$user_id."' group by challenges_idx, state";
		$chall_result = selectAllQuery($sql); 
		for($i=0; $i<count($chall_result['challenges_idx']); $i++){
			$chall_result_arr[$chall_result['challenges_idx'][$i]] = $chall_result['cnt'][$i];
		}

		//if($chall_result['challenges_idx']){
		//	$chall_result_arr = @array_combine($chall_result['challenges_idx'], $chall_result['state']);
		//}

		if($chk_tab0 || $chk_tab1 || $chk_tab2 || $chk_tab3 || $chk_tab4){
			$where .= " ";
			//전체챌린지
			if($chk_tab0 == "all"){
				$where .= "";
			}else{

				$where .= " AND ( ";
				//도전가능한 챌린지
				if($chk_tab1 == "1"){
					$where_chk[] = "(TIMESTAMPDIFF(DAY, DATE_FORMAT(now(), '%Y-%m-%d'), DATE_FORMAT(a.edate, '%Y-%m-%d')) >= 0)";

					//챌린지 결과 조인
					$que = " left join work_challenges_result as b on (a.idx=b.challenges_idx) ";
					//$que1 = ", b.state as bstate";
					//$que1_group = ", b.state";
				}

				//도전중인 챌린지
				if($chk_tab2 == "2"){

					//$query_etc = ",b.state as bstate";
					//$query_group = ",b.state";
					$tab_where = " OR ";
					$where_chk[] = "(b.state='0' and b.email='".$user_id."' and TIMESTAMPDIFF(DAY, DATE_FORMAT(now(), '%Y-%m-%d'), DATE_FORMAT(a.edate, '%Y-%m-%d')) >= 0)";

					$que = " left join work_challenges_result as b on (a.idx=b.challenges_idx) ";
					$sql = "select idx, challenges_idx from work_challenges_result where state='0' and companyno='".$companyno."' and email='".$user_id."'";
					$challenges_list_info = selectAllQuery($sql);
					$challenges_list_arr = @array_combine($challenges_list_info['challenges_idx'], $challenges_list_info['idx']);

				}

				//내가완료한 챌린지
				if($chk_tab3 == "3"){
					$tab_where = " OR ";
					//$where_chk[] = "(b.state='1' and b.email ='".$user_id."')";
					$where_chk[] = "(b.state='1' and a.attend=(select count(1) from work_challenges_result where state='1' and challenges_idx=a.idx and email='".$user_id."'))";

					//챌린지 결과 조인
					$que = " left join work_challenges_result as b on (a.idx=b.challenges_idx) ";
					//$que1 = ", b.state as bstate";
					//$que1_group = ", b.state";

				}

				//종료된 챌린지
				if($chk_tab4 == "4"){
					$tab_where = " OR ";
					$where_chk[] = "(TIMESTAMPDIFF(DAY, DATE_FORMAT(now(), '%Y-%m-%d'), DATE_FORMAT(a.edate, '%Y-%m-%d')) < 0)";
				}

				$where_loop = " OR ";
				for($i=0; $i<count($where_chk); $i++){

					if($where_chk[$i] == end($where_chk)){
						$where_loop = "";
					}

					$where .= $where_chk[$i] .$where_loop;
				}
				$where .= ")";
			}
		}else{
			$where = "where a.state in ('1','0')";
		}
	//}


	//내가만든 챌린지
	if($chall_type == "chmy"){
		$where .= " and a.email ='".$user_id."'";

		if($companyno){
			$where .= " and a.companyno ='".$companyno."'";
		}

	//임시저장
	}else if($chall_type == "tempflag"){

		//숨김챌린지
		$where .= " and a.view_flag='0'";

		//임시저장
		$where .= " and a.temp_flag='1'";

		$where .= " and a.email ='".$user_id."'";

		if($companyno){
			$where .= " and a.companyno ='".$companyno."'";
		}

	}else{

		if($coaching_chk){
			$where .= "";
		}else{
			$where .= " and a.coaching_chk='0'";
		}

		//숨김챌린지
		$where .= " and a.view_flag='0'";

		$where .= " and a.temp_flag='0'";
		$where .= " and a.template='0'";
		if($companyno){
			$where .= " and a.companyno ='".$companyno."'";
		}

		//$where .= " and edate >= convert(varchar(10), getdate(), 120)";
	}



	//카테고리
	$sql = "select idx, name, rank from work_category where state='0' order by rank asc";
	$cate_info = selectAllQuery($sql);
	for($i=0; $i<count($cate_info['idx']); $i++){
		$category[$cate_info['idx'][$i]] = $cate_info['name'][$i];
		$category_val[$cate_info['idx'][$i]] = $cate_info['idx'][$i];
	}

	//카테고리
	if($cate){
		if($category_val[$cate]){
			$where .= " and a.cate ='".$category_val[$cate]."'";
		}
	}else{
		$where .= "";
	}

	if($search){

		//$search = urlencode($search);
		$where .= " and a.title like '%".$search."%'";
	}


	//챌린지전체갯수
	$sql = "select count(1) cnt from ( select a.idx from work_challenges as a ".$que. $where." group by a.idx ) as c";
	$list_row = selectQuery($sql);
	if($list_row){
		//$total_count = count($chall_info['idx']);
		$total_count = $list_row['cnt'];
	}else{
		$total_count = 0;
	}


	//시작번호
	if ($gp == 1){
		$startnum = 0;
	}else{
		$startnum = ($gp - 1) * $pagesize;
	}

	//페이징 갯수
	if ( ($total_count % $pagesize) > 0 ){
		$page_count = floor($total_count/$pagesize)+1;
	}else{
		$page_count = floor($total_count/$pagesize);
	}


	//정렬
	if($rank){
		switch($rank){
			case "1":
				$orderby = "order by a.challenge desc";
				break;
	  		case "2":
				//$orderby = "order by a.chllday asc";
				$orderby = "order by CASE WHEN a.chllday > 0 THEN a.chllday END DESC, CASE WHEN a.chllday < 0 THEN a.chllday end desc";
				break;
	  		case "3":
				//코인높은순(매일참여일때 maxcoin 으로 정렬, 매일참여가 아니면 일반코인으로 정렬)
				$orderby = "order by maxcoin desc";
				break;
	  		case "4":
				$orderby = "order by CASE WHEN a.chllday >= 0 THEN a.chllday END DESC, CASE WHEN a.chllday < 0 THEN a.chllday end desc";
				break;
			default :
				$orderby = "order by a.idx desc";
				break;
		}
	}else{
		$orderby = " order by CASE WHEN chllday >= 0 THEN chllday END desc , CASE WHEN state in ('0','1') THEN idx END desc";
		// $orderby = " order by a.idx desc";
	}



	$sql = "select * from (";
	$sql = $sql .=" select a.idx, a.state, a.day_type, a.attend_type, a.attend, a.cate, a.title, a.companyno, a.email, a.keyword, a.sdate, a.edate, TIMESTAMPDIFF(DAY, DATE_FORMAT(now(), '%Y-%m-%d'), DATE_FORMAT(a.edate, '%Y-%m-%d')) as chllday, temp_flag,";
	$sql = $sql .=" (SELECT count(idx) FROM work_challenges_user WHERE state='0' and challenges_idx = a.idx) AS chamyeo, a.coin,";
	$sql = $sql .=" (CASE WHEN a.day_type='1' THEN a.coin * a.attend WHEN a.day_type='0' THEN a.coin END ) as maxcoin,";
	$sql = $sql .=" (CASE WHEN a.attend_type ='1' THEN (SELECT count(DISTINCT email) FROM work_challenges_result WHERE state='1' and comment!='' and challenges_idx = a.idx)";
	$sql = $sql .=" WHEN a.attend_type ='2' THEN (SELECT count(DISTINCT email) FROM work_challenges_result WHERE state='1' and file_path!='' and file_name!='' and challenges_idx = a.idx)";
	$sql = $sql .=" WHEN a.attend_type ='3' THEN (SELECT count(DISTINCT email) FROM work_challenges_result WHERE state='1' and challenges_idx = a.idx) end) as challenge";
	$sql = $sql .=" from work_challenges as a left join work_challenges_result as b on(a.idx=b.challenges_idx) ".$where."";
	$sql = $sql .=" group by a.idx, a.state, a.attend_type, a.cate, a.title, a.coin, a.companyno, a.email, a.day_type, attend, a.temp_flag, a.keyword, a.sdate, a.edate, TIMESTAMPDIFF(DAY, a.sdate, a.edate)";
	$sql = $sql .=" ) as a";
	$sql = $sql .= " ".$orderby."";
	$sql = $sql .= " limit ". $startnum.", ".$pagesize;

	echo $where."|";

	$chall_info = selectAllQuery($sql);




	$html = "";
	if($chall_info['idx']){
		for($i=0; $i<count($chall_info['idx']); $i++){

			$idx = $chall_info['idx'][$i];
			$state = $chall_info['state'][$i];
			$bstate = $chall_info['bstate'][$i];
			$cate = $chall_info['cate'][$i];
			//$title = addslashes($chall_info['title'][$i]);
			$title = $chall_info['title'][$i];
			$temp_flag = $chall_info['temp_flag'][$i];
			$chllday = $chall_info['chllday'][$i];
			$chamyeo = number_format($chall_info['chamyeo'][$i]);
			$challenge = number_format($chall_info['challenge'][$i]);
			$attend = $chall_info['attend'][$i];
			$keyword = $chall_info['keyword'][$i];
			$title = urldecode($title);

			if($chllday == 0){
				$chlldays = "D - Day";
			}else if($chllday < 0){
				$chlldays = "종료";
			}else{
				$chlldays = "D - ".$chllday;
			}

			if($chall_info['day_type'][$i] == '1'){
				$coin = number_format($chall_info['maxcoin'][$i]);
			}else{
				$coin = number_format($chall_info['coin'][$i]);
			}

			if($startnum > 1 && $i==0){
				$offset = " offset0";
			}else{
				$offset ="";
			}


			$html = $html .= '<li class="sli2'.($chllday<0?" cha_dend":"").' category_0'.$cate.''.$offset.'" value="'.$chall_info['idx'][$i].'">';
			$html = $html .= '	<a href="#null" onclick="javascript:void(0);">';
			$html = $html .= '		<div class="cha_box">';
			$html = $html .= '			<div class="cha_box_m">';
			$html = $html .= '				<div class="cha_info">';
			if($keyword){
				$html = $html .= '				<span class="cha_cate">'.$keyword.'</span>';
			}

			//임시저장
			if($temp_flag == '1'){
				$html = $html .= '					<span class="cha_save">임시저장</span>';
			}

			//echo "######### "  .$challenges_list_arr[$chall_info['idx'][$i]];

			//도전중
			if($challenges_list_arr[$chall_info['idx'][$i]]){
				//$html = $html .= '					<span class="cha_ing">도전중</span>';
				$html_to = "도전중";
			}else{

				//완료한수와 참여수가 같으면 도전성공
				if($chall_result_arr[$idx] == $attend){
					//$html = $html .= '				<span class="cha_comp">도전성공</span>';
					$html_to = "도전완료";
				}else{
					if($chllday < 0 ){
					//	$html = $html .= '			<span class="cha_comp">도전실패</span>';
						$html_to = "";
					}
				}
			}

			$html = $html .= '				</div>';
			$html = $html .= '				<span class="cha_coin"><strong>'.$coin.'</strong>코인</span>';
			$html = $html .= '			</div>';
			$html = $html .= '			<div class="cha_box_t">';
			$html = $html .= '				<span class="cha_title">'.$title.'</span>';
			$html = $html .= '			</div>';
			$html = $html .= '			<div class="cha_box_b">';
			$html = $html .= '				<span class="cha_member"><strong>'.$challenge.'</strong>/'.$chamyeo.'명'.($html_to?'('.$html_to.')':'').'</span>';
			$html = $html .= '				<span class="cha_dday">'.$chlldays.'</span>';
			$html = $html .= '			</div>';
			$html = $html .= '		</div>';
			$html = $html .= '	</a>';
			$html = $html .= '</li>';

		}
	}else{

			$html = $html .='	<div class="tdw_list_none">';
			$html = $html .='		<strong><span>등록된 챌린지가 없습니다.</span></strong>';
			$html = $html .='	</div>';
	}

	echo $html."|".number_format($total_count)."|".$page_count."|".$gp."|".$sql;
	exit;
}


//챌린지 참여자 검색
if($mode == "chall_chamyeo_search"){

	$user_chk_val = $_POST['user_chk_val']; 
	$user_tmp_arr = "";
	if($user_chk_val){
		$user_chk_val = trim($user_chk_val);
		$user_tmp_arr = explode(",",$user_chk_val);
	}

	$input_val = $_POST['input_val'];


	//회원정보
	if($input_val){
		$where = " and (a.name like '%".$input_val."%' or a.part like '%".$input_val."%')";
	}

	$sql = "select a.idx, email, name, part, partno, profile_type, profile_img_idx";
	$sql = $sql .=" from work_member as a left join work_team as b on(a.partno = b.idx)";
	$sql = $sql .=" where a.state='0' and a.companyno='".$companyno."' and highlevel!='".$grade_arr['manager']."'".$where." order by name asc";
	$member_info = selectAllQuery($sql);

	/*$sql = "select a.idx, name, part, partno , (SELECT count(idx) FROM work_member WHERE partno = b.idx ) AS cnt";
	$sql = $sql .=" from work_member as a left join work_team as b on(a.partno = b.idx)";
	$sql = $sql .=" where a.state='0' and companyno='".$companyno."' and highlevel='".$highlevel."' order by name collate Korean_Wansung_CI_AS asc";
	*/


	//회원정보내역으로 회원이름 = [name][부서번호][순번]
	for($i=0; $i<count($member_info['idx']); $i++){
		$member_idx = $member_info['idx'][$i];
		$member_uid = $member_info['email'][$i];
		$member_name = $member_info['name'][$i];
		$partno 	= $member_info['partno'][$i];

		$mem_uid[$partno][$member_idx] = $member_uid;
		$mem_name[$partno][$member_idx] = $member_name;
		$mem_idx[$partno][$member_idx] = $member_info['idx'][$i];
	}


	if(count($member_info['idx']) > 0){

		//회원정보 키값 초기화처리
		for($i=0; $i<count($member_info['idx']); $i++){

			$member_idx = $member_info['idx'][$i];
			$member_name = $member_info['name'][$i];
			$member_email = $member_info['email'][$i];
			$partno 	= $member_info['partno'][$i];

			//키값 초기화처리
			$mem_uid[$partno] = array_key_reset($mem_uid[$partno]);
			$mem_name[$partno] = array_key_reset($mem_name[$partno]);
			$mem_idx[$partno] = array_key_reset($mem_idx[$partno]);
		}

		?>
		<ul>
			<li>
				<dl class="on">
				<dt></dt>
				<?php
					$search_chk = 0;
					$profile_main_img_src = "";
					for($i=0; $i<count($member_info['partno']); $i++){
						$partno = $member_info['partno'][$i];
						$member_info_email = $member_info['email'][$i];
						$part_cnt = count($mem_name[$partno]);

						if(@in_array($member_info['idx'][$i], $user_tmp_arr)){
							$search_chk = $search_chk + 1;
						}

						//프로필 캐릭터,사진
						$profile_main_img_src = profile_img_info($member_info_email);
					?>
						<dd id="udd_<?=$member_info['idx'][$i]?>">
							<button value="<?=$member_info['idx'][$i]?>" id="team_<?=$partno?>" <?=@in_array($member_info['idx'][$i], $user_tmp_arr)?" class=\"on\"":""?>>
								<?=($user_id == $member_info['email'][$i]?"<img src=\"/html/images/pre/ico_me.png\" alt=\"\" class=\"user_me\" />":"");?> <!--20230215추가--> 
								<div class="user_img" style="background-image:url('<?=$profile_main_img_src?>');"></div>
								<div class="user_name" value="<?=$member_info_email?>">
									<strong><?=$member_info['name'][$i]?></strong>
									<span><?=$member_info['part'][$i]?></span>
								</div>
							</button>
						</dd>
					<?php
					}
					?>

				</dl>
			</li>
		</ul>|<?echo count($member_info['idx'])."|".$search_chk;
		exit;
		}else{?>
			<div class="layer_user_no">
				<strong>검색결과가 없습니다.</strong>
			</div>|0
	<?php
		}
	exit;

}


//챌린지 참여자 리스트 초기화
if($mode == "chall_user_list"){

	$user_chk_val = $_POST['user_chk_val'];
	$user_tmp_arr = "";
	if($user_chk_val){
		$user_chk_val = trim($user_chk_val);
		$user_tmp_arr = explode(",",$user_chk_val);
	}

	//부서별 정렬순
	$part_info = member_part_info();

	//회원정보
	$sql = "select a.idx, email, name, part, partno, profile_type, profile_img_idx, (SELECT count(idx) FROM work_member WHERE partno = b.idx ) AS cnt";
	$sql = $sql .=" from work_member as a left join work_team as b on(a.partno = b.idx)";
	$sql = $sql .=" where a.state='0' and a.companyno='".$companyno."' and highlevel!='1' order by name asc";
	$member_info = member_list_all();
	//$member_info = selectAllQuery($sql);



	//프로필 캐릭터 사진
	$sql = "select idx, file_path, file_name from work_member_character_img where state='0' order by idx asc";
	$character_img_info = selectAllQuery($sql);
	if($character_img_info['idx']){
		for($i=0; $i<count($character_img_info['idx']); $i++){
			$file_path = $character_img_info['file_path'][$i];
			$file_name = $character_img_info['file_name'][$i];
			$profile_character_info[$character_img_info['idx'][$i]] = $file_path.$file_name;
		}
	}

	//프로필 사진
	$sql = "select idx, file_path, file_name from work_member_profile_img where state='0' order by idx asc";
	$profile_img_list = selectAllQuery($sql);
	if($profile_img_list['idx']){
		for($i=0; $i<count($profile_img_list['idx']); $i++){
			$file_path = $profile_img_list['file_path'][$i];
			$file_name = $profile_img_list['file_name'][$i];
			$profile_img_list_info[$profile_img_list['idx'][$i]] = $file_path.$file_name;
		}
	}


	//회원정보 추출
	for($i=0; $i<count($member_info['idx']); $i++){

		$member_info_email = $member_info['email'][$i];
		$member_info_profile_type = $member_info['profile_type'][$i];
		$member_info_profile_img_idx = $member_info['profile_img_idx'][$i];
		if(!$member_info_profile_img_idx){
			$member_info_profile_img_idx = 5;
		}
		$profile_img_src['type'][$member_info_email] = $member_info_profile_type;
		$profile_img_src['imgidx'][$member_info_email] = $member_info_profile_img_idx;
	}


	if($member_info['idx']){
		$member_total_cnt = count($member_info['idx']);


		//회원정보내역으로 회원이름 = [name][부서번호][순번]
		for($i=0; $i<count($member_info['idx']); $i++){
			$member_idx = $member_info['idx'][$i];
			$member_uid = $member_info['email'][$i];
			$member_name = $member_info['name'][$i];
			$partno 	= $member_info['partno'][$i];

			$mem_uid[$partno][$member_idx] = $member_uid;
			$mem_name[$partno][$member_idx] = $member_name;
			$mem_idx[$partno][$member_idx] = $member_info['idx'][$i];

		}

		//회원정보 키값 초기화처리
		for($i=0; $i<count($member_info['idx']); $i++){

			$member_idx = $member_info['idx'][$i];
			$member_name = $member_info['name'][$i];
			$partno 	= $member_info['partno'][$i];

			//키값 초기화처리
			$mem_uid[$partno] = array_key_reset($mem_uid[$partno]);
			$mem_name[$partno] = array_key_reset($mem_name[$partno]);
			$mem_idx[$partno] = array_key_reset($mem_idx[$partno]);
		}

		echo "<ul>";

		for($i=0; $i<count($part_info['partno']); $i++){
			$partno = $part_info['partno'][$i];
			$part_cnt = count($mem_name[$partno]);
			?>
			<li>
				<dl class="on">
					<dt>
						<button class="btn_team_slc" id="btn_team_slc_<?=($partno)?>"><span><?=$part_info['part'][$i]?> <?=$part_cnt?></span></button>
						<button class="btn_team_toggle" id="btn_team_toggle"><span>열고닫기</span></button>
					</dt>

					<?for($j=0; $j<$part_cnt; $j++){
						//프로필 캐릭터,사진
						$profile_main_img_src = profile_img_info($mem_uid[$partno][$j]);
					?>
					<dd id="udd_<?=$mem_idx[$partno][$j]?>">
						<button value="<?=$mem_idx[$partno][$j]?>" id="team_<?=$partno?>" <?=@in_array($mem_idx[$partno][$j], $user_tmp_arr)?"class=\"on\"":""?>>
						<?=($user_id == $mem_uid[$partno][$j]?"<img src=\"/html/images/pre/ico_me.png\" alt=\"\" class=\"user_me\" />":"");?>
						<div class="user_img" style="background-image:url('<?=$profile_main_img_src?>');"></div>
						<div class="user_name" value="<?=$mem_uid[$partno][$j]?>">
							<strong><?=$mem_name[$partno][$j]?></strong>
							<span><?=$part_info['part'][$i]?></span>
						</div>
					</button>
					</dd>
					<?}?>
				</dl>
			</li>
		<?php
		}
		echo "</ul>";
		?>|<?=count($member_info['idx'])?>
	<?}else{?>
		<div class="layer_user_no"><strong>검색결과가 없습니다.</strong></div>|0
	<?php

	}
	exit;
}


//챌린지참여, 메시지형 검색
if($mode == "chall_masage_user_search"){

	$input_val = $_POST['input_val'];
	$idx = $_POST['idx'];
	$idx = preg_replace("/[^0-9]/", "", $idx);

	if($input_val){
		$where1 = " and (a.name like '%".$input_val."%' or b.part like '%".$input_val."%')";
		$where2 = " and (name like '%".$input_val."%' or part like '%".$input_val."%')";
	}

	if($idx){

		//챌린지 참여 메시지형
		$sql ="select email, count(idx) as cnt from work_challenges_result where state in('1','2') and companyno='".$companyno."' and comment!='' and challenges_idx='".$idx."' group by email";
		$user_list_info = selectAllQuery($sql);
		if($user_list_info['email']){
			$user_list_count = @array_combine($user_list_info['email'], $user_list_info['cnt']);
			$user_all_cnt = count($user_list_count);

		}

		//챌린지 참여 메시지 전체
		$sql ="select count(idx) as cnt from work_challenges_result where state in('1','2') and companyno='".$companyno."' and comment!='' and challenges_idx='".$idx."'".$where2."";
		$search_info = selectQuery($sql);
		if($search_info['cnt']){
			$search_all_cnt = number_format($search_info['cnt']);
		}else{
			$search_all_cnt = 0;
		}


		$sql = "select a.idx, a.email, a.name, b.part, b.partno, profile_type, profile_img_idx from work_challenges_user as a left join work_member as b on(a.email=b.email)";
		$sql = $sql .=" where b.state='0' and a.companyno='".$companyno."' and a.challenges_idx='".$idx."'".$where1." order by a.name asc";
		$user_info = selectAllQuery($sql);
		if($user_info['idx']){
			$total_cnt = number_format(count($user_info['idx']));
			$arr_email = @implode(",", $user_info['email']);
		?>
			<li>
				<button class="on" value="all">
					<div class="user_img">
						<img src="/html/images/pre/ico_user_all.png" alt="" />
					</div>
					<div class="user_name">
						<strong>전체</strong>
					</div>
					<span class="user_num<?=($search_all_cnt > 0)?"":" user_num_0"?>">
						<span><?=$search_all_cnt?></span>
					</span>
				</button>
			</li>

			<?php
			for($i=0; $i<count($user_info['idx']); $i++){
				$member_email = $user_info['email'][$i];
				$user_list_cnt = $user_list_count[$member_email];
				if($user_list_cnt > 0){
					$user_num = number_format($user_list_cnt);
					$user_num_class = "";
				}else{
					$user_num = "0";
					$user_num_class = " user_num_0";
				}

				//프로필 캐릭터,사진
				$profile_main_img_src = profile_img_info($member_email);
			?>
				<li>
					<button value="<?=$member_email?>">
						<?=($user_id == $member_email)?"<img src=\"/html/images/pre/ico_me.png\" alt=\"\" class=\"user_me\" />":"";?>
						<div class="user_img" style="background-image:url('<?=$profile_main_img_src?>');"></div>
						<div class="user_name">
							<strong><?=$user_info['name'][$i]?></strong>
							<?=$user_info['part'][$i]?>
						</div>
						<span class="user_num<?=$user_num_class?>">
							<span><?=$user_num?>
						</span>
						</span>
					</button>
				</li>
			<?php
			}?>|<?=count($user_info['idx'])?>
		<?php
		}else{?>
			<div class="layer_user_no"><strong>검색결과가 없습니다.</strong></div>|0
		<?php
		}
	}
	exit;
}


//챌린지참여, 파일첨부형 검색
if($mode == "chall_file_user_search"){

	$input_val = $_POST['input_val'];
	$idx = $_POST['idx'];
	$idx = preg_replace("/[^0-9]/", "", $idx);

	if($input_val){
		$where1 = " and (a.name like '%".$input_val."%' or b.part like '%".$input_val."%')";
		$where2 = " and (name like '%".$input_val."%' or part like '%".$input_val."%')";
	}

	if($idx){

		//챌린지 참여 메시지형
		$sql ="select email, count(idx) as cnt from work_challenges_result where state in('1','2') and companyno='".$companyno."' and comment!='' and challenges_idx='".$idx."' group by email";
		$user_list_info = selectAllQuery($sql);
		if($user_list_info['email']){
			$user_list_count = @array_combine($user_list_info['email'], $user_list_info['cnt']);
			$user_all_cnt = count($user_list_count);

		}

		//챌린지 참여 메시지 전체
		$sql ="select count(idx) as cnt from work_challenges_result where state in('1','2') and companyno='".$companyno."' and comment!='' and challenges_idx='".$idx."'".$where2."";
		$search_info = selectQuery($sql);
		if($search_info['cnt']){
			$search_all_cnt = number_format($search_info['cnt']);
		}else{
			$search_all_cnt = 0;
		}

		$sql = "select a.idx, a.email, a.name, b.part, b.partno from work_challenges_user as a left join work_member as b on(a.email=b.email)";
		$sql = $sql .=" where b.state='0' and a.companyno='".$companyno."' and a.challenges_idx='".$idx."'".$where1." order by a.name asc";
		$user_info = selectAllQuery($sql);
		if($user_info['idx']){
			$total_cnt = number_format(count($user_info['idx']));
			$arr_email = @implode(",", $user_info['email']);
		?>
			<li>
				<button class="on" value="all">
					<div class="user_img">
						<img src="/html/images/pre/ico_user_all.png" alt="" />
					</div>
					<div class="user_name">
						<strong>전체</strong>
					</div>
					<span class="user_num<?=($search_all_cnt > 0)?"":" user_num_0"?>">
						<span><?=$search_all_cnt?></span>
					</span>
				</button>
			</li>

			<?php
			for($i=0; $i<count($user_info['idx']); $i++){
				$user_list_cnt = $user_list_count[$user_info['email'][$i]];
				if($user_list_cnt > 0){
					$user_num = number_format($user_list_cnt);
					$user_num_class = "";
				}else{
					$user_num = "0";
					$user_num_class = " user_num_0";
				}
			?>
				<li>
					<button value="<?=$user_info['email'][$i]?>">
						<?=($user_id == $user_info['email'][$i])?"<img src=\"/html/images/pre/ico_me.png\" alt=\"\" class=\"user_me\" />":"";?>
						<div class="user_img">
							<img src="/html/images/pre/ico_user_005.png" alt="" />
						</div>
						<div class="user_name">
							<strong><?=$user_info['name'][$i]?></strong>
							<?=$user_info['part'][$i]?>
						</div>
						<span class="user_num<?=$user_num_class?>">
							<span><?=$user_num?>
						</span>
						</span>
					</button>
				</li>
			<?php
			}?>|<?=count($user_info['idx'])?>
		<?php
		}else{?>
			<div class="layer_user_no"><strong>검색결과가 없습니다.</strong></div>|0
		<?php
		}
	}
	exit;
}




//챌린지참여, 회원리스트
if($mode == "masage_user_list"){


	$idx = $_POST['idx'];
	$idx = preg_replace("/[^0-9]/", "", $idx);

	if($idx){
		$sql = "select count(1) as cnt from work_challenges_result where state in('1','2') and companyno='".$companyno."' and comment!='' and challenges_idx='".$idx."'";
		$user_tlist_info = selectQuery($sql);
		if($user_tlist_info['cnt']){
			$user_tlist_cnt = number_format($user_tlist_info['cnt']);
		}else{
			$user_tlist_cnt = 0;
		}

		//챌린지 참여 메시지형
		$sql ="select email, count(idx) as cnt from work_challenges_result where state in ('1','2') and companyno='".$companyno."' and comment!='' and challenges_idx='".$idx."' group by email";
		$user_list_info = selectAllQuery($sql);
		if($user_list_info['email']){
			$user_list_count = @array_combine($user_list_info['email'], $user_list_info['cnt']);
		}

		$sql = "select a.idx, a.email, a.name, b.part, b.partno, profile_type, profile_img_idx from work_challenges_user as a left join work_member as b on(a.email=b.email)";
		$sql = $sql .=" where b.state='0' and a.companyno='".$companyno."' and a.challenges_idx='".$idx."' order by CASE WHEN a.email = '".$user_id."' THEN a.email END DESC, CASE WHEN a.email <> '".$user_id."' THEN a.name  end asc";
		$user_info = selectAllQuery($sql);
		if($user_info['idx']){
			$total_cnt = number_format(count($user_info['idx']));
		?>
			<li>
				<button class="on" value="all">
					<div class="user_img">
						<img src="/html/images/pre/ico_user_all.png" alt="" />
					</div>
					<div class="user_name">
						<strong>전체</strong>
					</div>
					<span class="user_num<?=($user_tlist_cnt > 0)?"":" user_num_0"?>">
						<span><?=$user_tlist_cnt?></span>
					</span>
				</button>
			</li>

			<?php
			for($i=0; $i<count($user_info['idx']); $i++){
				$member_email = $user_info['email'][$i];
				$user_list_cnt = $user_list_count[$member_email];
				if($user_list_cnt > 0){
					$user_num = number_format($user_list_cnt);
					$user_num_class = "";
				}else{
					$user_num = "0";
					$user_num_class = " user_num_0";
				}

				//프로필 캐릭터,사진
				$profile_main_img_src = profile_img_info($member_email);

			?>
				<li>
					<button value="<?=$member_email?>">
						<?=($user_id == $member_email)?"<img src=\"/html/images/pre/ico_me.png\" alt=\"\" class=\"user_me\" />":"";?>
						<div class="user_img" style="background-image:url('<?=$profile_main_img_src?>');"></div>
						<div class="user_name">
							<strong><?=$user_info['name'][$i]?></strong>
							<?=$user_info['part'][$i]?>
						</div>
						<span class="user_num<?=$user_num_class?>">
							<span><?=$user_num?>
						</span>
						</span>
					</button>
				</li>
			<?php
			}?>|<?=count($user_info['idx'])?>
		<?php
		}else{?>
			<div class="layer_user_no"><strong>검색결과가 없습니다^^^.</strong></div>|0
		<?}
	}
	exit;
}


//챌린지참여, 좌측 회원리스트(인증파일)
if($mode == "file_user_list"){

	$input_val = $_POST['input_val'];
	$idx = $_POST['idx'];
	$idx = preg_replace("/[^0-9]/", "", $idx);
	$user_date = $_POST['user_date'];

	//검색어
	if($input_val){
		$where1 = " and (a.name like '%".$input_val."%' or b.part like '%".$input_val."%')";
		$where2 = " and (name like '%".$input_val."%' or part like '%".$input_val."%')";
	}

	if($user_date && $user_date!="all"){
		$where2 = $where2 .= " and DATE_FORMAT(file_regdate, '%Y-%m-%d') ='".$user_date."' ";
		$text_date = $user_date;
	}else{
		$text_date = "전체보기";
	}

	if($idx){
		$sql = "select count(1) as cnt from work_challenges_result where state in('1','2') and companyno='".$companyno."' and file_path!='' and file_name!='' and challenges_idx='".$idx."'".$where2."";
		$user_tlist_info = selectQuery($sql);
		if($user_tlist_info['cnt']){
			$user_tlist_cnt = number_format($user_tlist_info['cnt']);
		}else{
			$user_tlist_cnt = 0;
		}

		//챌린지 참여 인증파일
		$sql ="select email, count(idx) as cnt from work_challenges_result where state in ('1','2') and companyno='".$companyno."' and file_path!='' and file_name!='' and challenges_idx='".$idx."'".$where2." group by email";
		$user_list_info = selectAllQuery($sql);
		if($user_list_info['email']){
			$user_list_count = @array_combine($user_list_info['email'], $user_list_info['cnt']);
		}


		$sql = "select a.idx, a.email, a.name, b.part, b.partno, profile_type, profile_img_idx from work_challenges_user as a left join work_member as b on(a.email=b.email)";
		$sql = $sql .=" where b.state='0' and b.companyno='".$companyno."' and a.challenges_idx='".$idx."'".$where1." order by  CASE WHEN a.email = '".$user_id."' THEN a.email END DESC, CASE WHEN a.email <> '".$user_id."' THEN a.name  end asc";
		$user_info = selectAllQuery($sql);

		if($user_info['idx']){
			$total_cnt = number_format(count($user_info['idx']));
		?>
			<li>
				<button class="on" value="all">
					<div class="user_img"  style="background-image:url('/html/images/pre/ico_user_all.png');"></div>
					<div class="user_name">
						<strong>전체</strong>
					</div>
					<span class="user_num<?=($user_tlist_cnt > 0)?"":" user_num_0"?>">
						<span><?=$user_tlist_cnt?></span>
					</span>
				</button>
			</li>

			<?php
			for($i=0; $i<count($user_info['idx']); $i++){
				$member_email = $user_info['email'][$i];
				$user_list_cnt = $user_list_count[$member_email];
				if($user_list_cnt > 0){
					$user_num = number_format($user_list_cnt);
					$user_num_class = "";
				}else{
					$user_num = "0";
					$user_num_class = " user_num_0";
				}

				//프로필 캐릭터,사진
				$profile_main_img_src = profile_img_info($member_email);
			?>
				<li>
					<button value="<?=$member_email?>">
						<?=($user_id == $member_email)?"<img src=\"/html/images/pre/ico_me.png\" alt=\"\" class=\"user_me\" />":"";?>
						<div class="user_img" style="background-image:url('<?=$profile_main_img_src?>');"></div>
						<div class="user_name">
							<strong><?=$user_info['name'][$i]?></strong>
							<?=$user_info['part'][$i]?>
						</div>
						<span class="user_num<?=$user_num_class?>">
							<span><?=$user_num?>
						</span>
						</span>
					</button>
				</li>
			<?php
			}?>|<?php echo count($user_info['idx'])?>

		<?}else{?>

			<div class="layer_user_no">
				<strong>검색결과가 없습니다.</strong>
			</div>|0

		<?php
		}
	}
	exit;
}



//챌린지참여, 좌측 회원리스트(혼합형)
if($mode == "mix_user_list"){

	$input_val = $_POST['input_val'];
	$idx = $_POST['idx'];
	$idx = preg_replace("/[^0-9]/", "", $idx);
	$user_date = $_POST['user_date'];

	//검색어
	if($input_val){
		$where1 = " and (a.name like '%".$input_val."%' or b.part like '%".$input_val."%')";
		$where2 = " and (name like '%".$input_val."%' or part like '%".$input_val."%')";
	}

	if($user_date && $user_date!="all"){
		$where2 = $where2 .= " and DATE_FORMAT(file_regdate, '%Y-%m-%d') ='".$user_date."' ";
		$text_date = $user_date;
	}else{
		$text_date = "전체보기";
	}


	if($idx){

		//회원별 건수 조회
		$sql = "select count(1) as cnt from work_challenges_result where state in('1','2') and companyno='".$companyno."' and comment!='' and challenges_idx='".$idx."'".$where2."";
		$user_tlist_info = selectQuery($sql);
		if($user_tlist_info['cnt']){
			$user_tlist_cnt = number_format($user_tlist_info['cnt']);
		}else{
			$user_tlist_cnt = 0;
		}

		//챌린지 참여수
		$sql ="select email, count(idx) as cnt from work_challenges_result where state in ('1','2') and companyno='".$companyno."' and comment!='' and challenges_idx='".$idx."'".$where2." group by email";
		$user_list_info = selectAllQuery($sql);
		if($user_list_info['email']){
			$user_list_count = @array_combine($user_list_info['email'], $user_list_info['cnt']);
		}

		//좌측 회원리스트
		$sql = "select a.idx, a.email, a.name, b.part, b.partno, b.profile_type, b.profile_img_idx from work_challenges_user as a left join work_member as b on(a.email=b.email)";
		$sql = $sql .=" where b.state='0' and a.companyno='".$companyno."' and a.challenges_idx='".$idx."'".$where1." order by  CASE WHEN a.email = '".$user_id."' THEN a.email END DESC, CASE WHEN a.email <> '".$user_id."' THEN a.name  end asc";
		$user_info = selectAllQuery($sql);

		if($user_info['idx']){
			$total_cnt = number_format(count($user_info['idx']));
		?>
			<li>
				<button class="on" value="all">
					<div class="user_img">
						<img src="/html/images/pre/ico_user_all.png" alt="" />
					</div>
					<div class="user_name">
						<strong>전체</strong>
					</div>
					<span class="user_num<?=($user_tlist_cnt > 0)?"":" user_num_0"?>">
						<span><?=$user_tlist_cnt?></span>
					</span>
				</button>
			</li>

			<?php
			for($i=0; $i<count($user_info['idx']); $i++){
				$member_email = $user_info['email'][$i];
				$user_list_cnt = $user_list_count[$member_email];
				if($user_list_cnt > 0){
					$user_num = number_format($user_list_cnt);
					$user_num_class = "";
				}else{
					$user_num = "0";
					$user_num_class = " user_num_0";
				}

				//프로필 캐릭터,사진
				$profile_main_img_src = profile_img_info($member_email);
			?>
				<li>
					<button value="<?=$member_email?>">
						<?=($user_id == $member_email)?"<img src=\"/html/images/pre/ico_me.png\" alt=\"\" class=\"user_me\" />":"";?>
						<div class="user_img" style="background-image:url('<?=$profile_main_img_src?>');"></div>
						<div class="user_name">
							<strong><?=$user_info['name'][$i]?></strong>
							<?=$user_info['part'][$i]?>
						</div>
						<span class="user_num<?=$user_num_class?>">
							<span><?=$user_num?>
						</span>
						</span>
					</button>
				</li>
			<?php
			}?>|<?php echo count($user_info['idx'])?>

		<?}else{?>

			<div class="layer_user_no">
				<strong>검색결과가 없습니다.</strong>
			</div>|0

		<?php
		}
	}
	exit;
}



//챌린지 참여, 인증파일
if($mode == "auth_file_list"){

	/*print "<pre>";
	print_r($_POST);
	print "<pre>";*/


	$idx = $_POST['idx'];
	$idx = preg_replace("/[^0-9]/", "", $idx);

	$list_idx = $_POST['list_idx'];
	$list_idx = trim($list_idx);

	$input_val = $_POST['input_val'];
	$user_date = $_POST['user_date'];

	if($idx){

		//전체
		if($list_idx == "all"){
				if($input_val){
				$where = " and (name like '%".$input_val."%' or part like '%".$input_val."%')";
			}else{
				$where = "";
			}

		}else{
			$tmp_email = @explode(",", $list_idx);
			$tmp_cnt = count($tmp_email);

			if($tmp_cnt > 1){
				$imp_email = @implode("','",$tmp_email);
				$where = " and email in ('".$imp_email."')";
			}else{
				$where = " and email='".$list_idx."'";
			}
		}

		if($user_date && $user_date!="all"){
			$where = $where .= " and DATE_FORMAT(file_regdate, '%Y-%m-%d')='".$user_date."' ";
			$text_date = $user_date;
		}else{
			$text_date = "전체보기";
		}

		$sql = "select count(1) as cnt from work_challenges_result where state in('1','2') and companyno='".$companyno."' and file_path!='' and file_name!='' and challenges_idx='".$idx."'".$where."";
		$user_tlist_info = selectQuery($sql);
		if($user_tlist_info['cnt']){
			$user_file_cnt = number_format($user_tlist_info['cnt']);
		}else{
			$user_file_cnt = 0;
		}

		//챌린지 참여 파일첨부형
		$sql ="select email, count(idx) as cnt from work_challenges_result where state in('1','2') and companyno='".$companyno."' and file_path!='' and file_name!='' and challenges_idx='".$idx."' group by email";
		$user_list_file = selectAllQuery($sql);
		if($user_list_file['email']){
			$user_file_count = @array_combine($user_list_file['email'], $user_list_file['cnt']);
		}

		$sql = "select idx, state, email, name, part, DATE_FORMAT(file_regdate, '%Y-%m-%d %H:%i:%s') reg, DATE_FORMAT(file_regdate, '%Y-%m-%d') as ymd, DATE_FORMAT(file_regdate, '%Y-%m-%d %H:%i:%s') as ddd, file_path, file_name, file_ori_path, file_ori_name, file_real_name, file_real_img_name, file_type from work_challenges_result where state in ('1','2') and companyno='".$companyno."' and challenges_idx='".$idx."' and file_path!='' and file_name!=''".$where." order by reg desc";
		$chall_file_info = selectAllQuery($sql);
		if($chall_file_info['idx']){
			$chamyeo_file_cnt = count($chall_file_info['idx']);
		}else{
			$chamyeo_file_cnt = 0;
		}

		//오늘 참여한 내역 체크
		$sql = "select idx from work_challenges_result where state in('1','2') and challenges_idx='".$idx."' and companyno='".$companyno."' and file_path!='' and file_name!='' and email='".$user_id."' and DATE_FORMAT(file_regdate, '%Y-%m-%d')='".TODATE."'";
		$ch_file_info = selectQuery($sql);

		//참여횟수체크
		if($chamyeo_file_cnt >= $attend || !$ch_file_info['idx']){
			$chamyeo_chk = false;
		}else{
			$chamyeo_chk = true;
		}

		$sql ="select DATE_FORMAT(file_regdate, '%Y-%m-%d') as ymd from work_challenges_result where state in ('1','2') and companyno='".$companyno."' and challenges_idx='".$idx."' and file_path!='' and file_name!=''";
		$sql = $sql .=" group by DATE_FORMAT(file_regdate, '%Y-%m-%d') order by DATE_FORMAT(file_regdate, '%Y-%m-%d') desc";
		$date_info = selectAllQuery($sql);

		if($user_date && $user_date!="all"){
			$text_date = $user_date;
		}else{
			$text_date = "전체보기";
		}


		for($i=0; $i<count($chall_file_info['idx']); $i++){

			$cidx = $chall_file_info['idx'][$i];
			$cemail = $chall_file_info['email'][$i];
			$cname = $chall_file_info['name'][$i];
			$cpart = $chall_file_info['part'][$i];
			$creg = $chall_file_info['reg'][$i];
			$cymd = $chall_file_info['ymd'][$i];
			$chis = $chall_file_info['ddd'][$i];

			$state = $chall_file_info['state'][$i];

			$file_path = $chall_file_info['file_ori_path'][$i];
			$file_name = $chall_file_info['file_name'][$i];
			$file_type = $chall_file_info['file_type'][$i];
			$file_real_name = $chall_file_info['file_real_name'][$i];
			$file_real_img_name = $chall_file_info['file_real_img_name'][$i];

			$cfiles = $file_path.$file_name;

			//요일구함
			$reg_date = explode("-", $cymd);
			$int_reg = date("w", strtotime($creg));
			$date_yoil =  $weeks[$int_reg];

			$chall_reg = $reg_date[0]."년 ".$reg_date[1]."월 ".$reg_date[2];

			//시간 오전,오후
			if($chis){
				$chis_tmp = explode(" ", $chis);
				if ($chis_tmp['3'] == "PM"){
					$after = "오후 ";
				}else{
					$after = "오전 ";
				}
				$ctime = explode(":", $chis_tmp['2']);
				$chiss = $after . $ctime['0'] .":". $ctime['1'];
			}


			//전체내역
			$chall_file_list[$cymd][$i]['date'] = $cymd;
			$chall_file_list[$cymd][$i]['idx'] = $cidx;
			$chall_file_list[$cymd][$i]['eamil'] = $cemail;
			$chall_file_list[$cymd][$i]['name'] = $cname;
			$chall_file_list[$cymd][$i]['part'] = $cpart;
			$chall_file_list[$cymd][$i]['files'] = $cfiles;
			$chall_file_list[$cymd][$i]['hi'] = $chiss;

			//이미지형, 파일형
			if(@in_array($file_type , $image_type_array)){
				$chall_file_list[$cymd][$i]['file_real_name'] = $file_real_img_name;
			}else{
				$chall_file_list[$cymd][$i]['file_real_name'] = $file_real_name;
			}


			$chall_file_list[$cymd][$i]['file_type'] = $file_type;
			$chall_file_list[$cymd][$i]['yoil'] = $date_yoil;
			$chall_file_list[$cymd][$i]['reg'] = $chall_reg;
			$chall_file_list[$cymd][$i]['state'] = $state;
			$chall_file_list_ymd[]= $cymd;
		}

		//배열키값 중복제거
		$chall_file_list_ymd = array_unique($chall_file_list_ymd);

		//배열키값 리셋
		$chall_file_list_ymd = array_key_reset($chall_file_list_ymd);


		//이메일 선택시
		if($list_idx == "all"){

			$user_tlist_cnt = count($list_info['idx']);
			$select_name = "전체";

		}else{

			$user_tlist_cnt = count($list_info['idx']);
			$sql = "select idx, name, part from work_member where state='0' and companyno='".$companyno."' and email='".$list_idx."'";
			$mem_info = selectQuery($sql);
			if($mem_info['idx']){
				$select_name = $mem_info['name'];
				$select_part = $mem_info['part'];
			}
		}

		if(!$select_name && count($list_info['idx'])==0){
			$select_name = "전체";
		}

		//좋아요 리스트
		$like_flag_list = array();
		$sql = "select idx, email,service, work_idx, send_email, like_flag from work_todaywork_like where state='0' and companyno='".$companyno."' and send_email='".$user_id."' and workdate='".TODATE."'";
		$like_info = selectAllQuery($sql);
		for($i=0; $i<count($like_info['idx']); $i++){
			$like_info_idx = $like_info['idx'][$i];
			$like_info_email = $like_info['email'][$i];
			$like_info_work_idx = $like_info['work_idx'][$i];
			$like_info_like_flag = $like_info['like_flag'][$i];
			$like_info_send_email = $like_info['send_email'][$i];

			$work_like_list[$like_info_work_idx] = $like_info_idx;
		}

	?>

		<div class="list_function">
			<div class="list_function_in">
				<div class="list_function_left">
					<?=$select_name?> <span><?=$select_part?></span><strong><?=$chamyeo_file_cnt?></strong>
				</div>
				<div class="list_function_right">
					<div class="list_function_sort">
						<div class="list_function_sort_in">
							<input type="hidden" id="user_email" value="<?=$list_idx?>">
							<input type="hidden" id="user_date" value="<?=$user_date?>">
							<button class="btn_sort_on" id="auth_file_date" value="all"><span><?=$text_date?></span></button>
							<ul>
								<li><button id="file_reg0" value="all"><span>전체보기</span></button></li>
								<?for($i=0; $i<count($date_info['ymd']); $i++){?>
									<li><button id="file_reg<?=($i+1)?>" value="<?=$date_info['ymd'][$i]?>"><span><?=$date_info['ymd'][$i]?></span></button></li>
								<?}?>
							</ul>
						</div>
					</div>
					<div class="list_function_type">
						<button class="type_list"><span>리스트형</span></button>
						<button class="type_img on"><span>이미지형</span></button>
					</div>
				</div>
			</div>
		</div>

		<?if($chall_file_list_ymd){?>
			<div class="list_area" id="list_area_auth">
				<div class="list_area_in">

				<?	$k=0;
					for($i=0; $i<count($chall_file_list_ymd); $i++){
						$date_ymd = trim($chall_file_list_ymd[$i]);
				?>

						<div class="list_box">
							<div class="list_date">
								<span><?=$chall_file_list[$date_ymd][$k]['reg']?> <?=$chall_file_list[$date_ymd][$k]['yoil']?></span>
							</div>


							<div class="list_conts type_img">
								<ul class="list_ul">

								<?for($j=0; $j<count($chall_file_list[$date_ymd]); $j++){

									$files_name = $chall_file_list[$date_ymd][$k]['file_real_name'];
									$files_url = $chall_file_list[$date_ymd][$k]['files'];

									$files_name_ext = array_pop(explode(".", strtolower($files_name)));
									$files_full_name = current(explode('.', $files_name));

								?>
									<li>
										<div class="list_thumb_wrap<?=($chall_file_list[$date_ymd][$k]['state']=="2")?" lrt_none":""?>" id="file_list_chk<?=$k?>" value="<?=$chall_file_list[$date_ymd][$k]['idx']?>">
											<div class="list_thumb">

												<?
												//이미지일때
												if (@in_array($chall_file_list[$date_ymd][$k]['file_type'], $image_type_array)){?>
														<div class="list_thumb_img" id="list_thumb_img">
															<img src="<?=$files_url?>" alt="" />
														</div>
														<?if($chall_file_list[$date_ymd][$k]['state']=="2"){?>
															<div class="list_thumb_none">
																<span></span>
																<strong>취소상태</strong>
															</div>
															<div class="list_thumb_cover">
																<span></span>
																<button class="list_thumb_select"><strong>선택하기</strong></button>
																<button class="list_thumb_preview" value="<?=$chall_file_list[$date_ymd][$k]['idx']?>"><strong>미리보기</strong></button>
															</div>
														<?}else{?>
															<div class="list_thumb_cover">
																<span></span>
																<button class="list_thumb_select"><strong>선택하기</strong></button>
																<button class="list_thumb_preview" value="<?=$chall_file_list[$date_ymd][$k]['idx']?>"><strong>미리보기</strong></button>
															</div>
														<?}?>

												<?}else{?>

													<div class="list_thumb_cover">
														<span></span>
														<button class="list_thumb_select"><strong>선택하기</strong></button>
														<button class="list_thumb_preview" value="<?=$chall_file_list[$date_ymd][$k]['idx']?>"><strong>미리보기</strong></button>
													</div>

												<?}?>
											</div>
											<div class="list_desc">
												<div class="list_title">
													<strong><?=$files_full_name?>.</strong>
													<span><?=$files_name_ext?></span>

													<?if($user_id!=$chall_file_list[$date_ymd][$k]['email']){?>
														<button class="masage_jjim<?=$work_like_list[$chall_file_list[$date_ymd][$k]['idx']]>0?" on":""?>" id="file_jjim" value="<?=$chall_file_list[$date_ymd][$k]['idx']?>"><span>좋아요</span></button>
													<?}?>

												</div>
												<div class="list_user">
													<span><?=$chall_file_list[$date_ymd][$k]['name']?> <?=$chall_file_list[$date_ymd][$k]['part']?></span>
												</div>
											</div>
											<div class="list_time">
												<span><?=$chall_file_list[$date_ymd][$k]['hi']?></span>
											</div>
										</button>
									</li>
								<?
								$k++;
								}
								?>
								</ul>
							</div>
						</div>
					<?}?>
				</div>
			</div>



		<?}else{?>

			<div class="list_area_none">
				<strong>등록된 인증 파일이 없습니다.</strong>
			</div>

		<?}
	}
	exit;
}



//챌린지 참여, 인증메시지 리스트
if($mode == "auth_masage_list"){

	$idx = $_POST['idx'];
	$list_idx = $_POST['list_idx'];
	$idx = preg_replace("/[^0-9]/", "", $idx);
	$list_idx = trim($list_idx);
	$input_val = $_POST['input_val'];
	$user_date = $_POST['user_date'];

	if($idx){

		//전체
		if($list_idx == "all"){

			if($input_val){
				$where = " and (name like '%".$input_val."%' or part like '%".$input_val."%')";
			}else{
				$where = "";
			}

		}else{
			$tmp_email = @explode(",", $list_idx);
			$tmp_cnt = count($tmp_email);

			if($tmp_cnt > 1){
				$imp_email = @implode("','",$tmp_email);
				$where = " and email in ('".$imp_email."')";
			}else{
				$where = " and email='".$list_idx."'";
			}
		}

		$sql ="select DATE_FORMAT(comment_regdate, '%Y-%m-%d') as ymd from work_challenges_result where state in ('1','2') and companyno='".$companyno."' and comment!='' and challenges_idx='".$idx."'".$where."";
		$sql = $sql .=" group by DATE_FORMAT(comment_regdate, '%Y-%m-%d') order by DATE_FORMAT(comment_regdate, '%Y-%m-%d') desc";
		$date_info = selectAllQuery($sql);

		if($user_date && $user_date!="all"){
			$where = $where .= " and DATE_FORMAT(comment_regdate, '%Y-%m-%d')='".$user_date."' ";
			$text_date = $user_date;
		}else{
			$text_date = "전체보기";
		}


		$sql ="select idx, state, email, name, part, comment, DATE_FORMAT(comment_regdate, '%Y-%m-%d %H:%i:%s') as reg, DATE_FORMAT(comment_regdate, '%Y-%m-%d') as ymd, DATE_FORMAT(comment_regdate, '%Y-%m-%d %H:%i:%s') as ddd";
		$sql = $sql.= " from work_challenges_result where state in ('1','2') and comment!='' and companyno='".$companyno."' and challenges_idx='".$idx."'".$where." order by reg desc";
		/*$sql = "select idx, state, name, part, contents, convert(varchar(20), regdate, 120) as reg, convert(varchar(10), regdate, 23) as ymd, CONVERT(varchar(20), regdate, 22) as ddd";
		$sql = $sql .= " from work_challenges_comment where state='1' and challenges_idx='".$idx."'".$where."";
		$sql = $sql .= " order by name collate Korean_Wansung_CI_AS asc, idx desc";*/
		$list_info = selectAllQuery($sql);

		for($i=0; $i<count($list_info['idx']); $i++){

			$cidx = $list_info['idx'][$i];
			$cemail = $list_info['email'][$i];
			$cname = $list_info['name'][$i];
			$cpart = $list_info['part'][$i];
			$creg = $list_info['reg'][$i];
			$cymd = $list_info['ymd'][$i];
			$chis = $list_info['ddd'][$i];
			$state = $list_info['state'][$i];
			$comment = urldecode($list_info['comment'][$i]);

			//요일구함
			$reg_date = explode("-", $cymd);
			$int_reg = date("w", strtotime($creg));
			$date_yoil =  $weeks[$int_reg];

			if($reg_date){
				$reg_date_m = $reg_date[1];
				$reg_date_d = preg_replace('/(0)(\d)/','$2', $reg_date[2]);
				$date_md = $reg_date_m."월 ". $reg_date_d."일";
			}

			//시간 오전,오후
			if($chis){
				$chis = str_replace("  "," ",$chis);
				$chis_tmp = explode(" ", $chis);
				if ($chis_tmp['2'] == "PM"){
					$after = "오후 ";
				}else{
					$after = "오전 ";
				}
				$ctime = @explode(":", $chis_tmp['1']);
				$chiss = $after . $ctime['0'] .":". $ctime['1'];
			}

			//전체내역
			$masage_list[$cymd][$i]['date'] = $cymd;
			$masage_list[$cymd][$i]['idx'] = $cidx;
			$masage_list[$cymd][$i]['email'] = $cemail;
			$masage_list[$cymd][$i]['name'] = $cname;
			$masage_list[$cymd][$i]['part'] = $cpart;
			$masage_list[$cymd][$i]['comment'] = $comment;
			$masage_list[$cymd][$i]['yoil'] = $date_yoil;
			$masage_list[$cymd][$i]['md'] = $date_md;
			$masage_list[$cymd][$i]['hi'] = $chiss;
			$masage_list[$cymd][$i]['state'] = $state;
			$masage_list_ymd[]= $cymd;
		}

		//배열키값 중복제거
		$masage_list_ymd = array_unique($masage_list_ymd);

		//배열키값 리셋
		$masage_list_ymd = array_key_reset($masage_list_ymd);


		//이메일 선택시
		if($list_idx == "all"){

			$user_tlist_cnt = count($list_info['idx']);
			$select_name = "전체";

		}else{

			$user_tlist_cnt = count($list_info['idx']);
			$sql = "select idx, name, part from work_member where state='0' and companyno='".$companyno."' and email='".$list_idx."'";
			$mem_info = selectQuery($sql);
			if($mem_info['idx']){
				$select_name = $mem_info['name'];
				$select_part = $mem_info['part'];
			}
		}

		if(!$select_name && count($list_info['idx'])==0){
			$select_name = "전체";
		}


		//좋아요 리스트
		$like_flag_list = array();
		$sql = "select idx, email,service, work_idx, send_email, like_flag from work_todaywork_like where state='0' and companyno='".$companyno."' and send_email='".$user_id."' and workdate='".TODATE."'";
		$like_info = selectAllQuery($sql);
		for($i=0; $i<count($like_info['idx']); $i++){
			$like_info_idx = $like_info['idx'][$i];
			$like_info_email = $like_info['email'][$i];
			$like_info_work_idx = $like_info['work_idx'][$i];
			$like_info_like_flag = $like_info['like_flag'][$i];
			$like_info_send_email = $like_info['send_email'][$i];

			$work_like_list[$like_info_work_idx] = $like_info_idx;
		}

		?>

		<div class="list_function">
			<div class="list_function_in">
				<div class="list_function_left">
				<?=$select_name?> <span><?=$select_part?></span><strong><?=$user_tlist_cnt?></strong>
				</div>
				<div class="list_function_right">
					<div class="list_function_sort">
						<div class="list_function_sort_in">
							<input type="hidden" id="user_email" value="<?=$list_idx?>">
							<input type="hidden" id="user_date" value="<?=$user_date?>">
							<button class="btn_sort_on" id="auth_masage_date"><span><?=$text_date?></span></button>
							<ul>
								<li><button id="comment_reg0" value="all"><span>전체보기</span></button></li>
								<?for($i=0; $i<count($date_info['ymd']); $i++){?>
									<li><button id="comment_reg<?=($i+1)?>" value="<?=$date_info['ymd'][$i]?>"><span><?=$date_info['ymd'][$i]?></span></button></li>
								<?}?>
							</ul>
						</div>
					</div>
				</div>
			</div>
		</div>
		<?php

			if($masage_list_ymd){?>
			<div class="masage_zone">
				<div class="masage_zone_in">
				<?
					$k=0;
					for($i=0; $i<count($masage_list_ymd); $i++){
							$date_ymd = trim($masage_list_ymd[$i]);
					?>
						<div class="masage_date">
							<span><?=$masage_list[$date_ymd][$k]['md']?> <?=$masage_list[$date_ymd][$k]['yoil']?></span>
						</div>

						<?
						for($j=0; $j<count($masage_list[$date_ymd]); $j++){

							$chall_masage_email = $masage_list[$date_ymd][$k]['email'];
							if ($user_id == $masage_list[$date_ymd][$k]['email']){
								$div_class = "masage_area_in";
							}else{
								$div_class = "";
							}

							//프로필 캐릭터,사진
							$profile_main_img_src = profile_img_info($chall_masage_email);
						?>
							<div class="masage_area">
								<div class="masage_area_in<?=($masage_list[$date_ymd][$k]['state']=="2")?" chk_none":""?>" id="masage_list_chk<?=$k?>" value="<?=$masage_list[$date_ymd][$k]['idx']?>">
									<div class="masage_img" style="background-image:url('<?=$profile_main_img_src?>');">
										<button class="masage_chk"><span>선택</span></button>
									</div>
									<div class="masage_info">
										<div class="masage_user">
											<strong><?=$masage_list[$date_ymd][$k]['name']?></strong>
											<span><?=$masage_list[$date_ymd][$k]['part']?></span>
										</div>
										<div class="masage_box">
											<p class="masage_txt"><?=$masage_list[$date_ymd][$k]['comment']?></p>
											<span class="masage_time"><?=$masage_list[$date_ymd][$k]['hi']?></span>
											<?if($user_id!=$masage_list[$date_ymd][$k]['email']){?>
												<button class="masage_jjim<?=$work_like_list[$masage_list[$date_ymd][$k]['idx']]>0?" on":""?>" id="masage_jjim" value="<?=$masage_list[$date_ymd][$k]['idx']?>"><span>좋아요</span></button>
											<?}?>
										</div>
										<? if($masage_list[$date_ymd][$k]['state']=="2"){?>
											<div class="masage_warning">
												<span>무효 후 코인 회수 처리되었습니다.</span>
											</div>
										<?}?>
									</div>
								</div>
							</div>
						<?php
						$k++;
						}
					}
				?>
				</div>
			</div>

		<?php
		}else{?>
			<div class="masage_area_none">
				<strong>등록된 인증메시지가 없습니다.</strong>
			</div>
		<?php
		}?>

	<?}?>


	<?exit;

}



//혼합형 리스트
if($mode == "auth_mix_list"){

/*	print "<pre>";
	print_r($_POST);
	print "</pre>";
*/

	$idx = $_POST['idx'];
	$idx = preg_replace("/[^0-9]/", "", $idx);

	$list_idx = $_POST['list_idx'];
	$list_idx = trim($list_idx);

	$input_val = $_POST['input_val'];
	$user_date = $_POST['user_date'];

	if($idx){

		//전체
		if($list_idx == "all"){
				if($input_val){
				$where = " and (name like '%".$input_val."%' or part like '%".$input_val."%')";
			}else{
				$where = "";
			}

		}else{
			$tmp_email = @explode(",", $list_idx);
			$tmp_cnt = count($tmp_email);

			if($tmp_cnt > 1){
				$imp_email = @implode("','",$tmp_email);
				$where = " and email in ('".$imp_email."')";
			}else{
				$where = " and email='".$list_idx."'";
			}
		}

		if($user_date && $user_date!="all"){
			$where = $where .= " and DATE_FORMAT(file_regdate, '%Y-%m-%d')='".$user_date."' ";
			$text_date = $user_date;
		}else{
			$text_date = "전체보기";
		}

		$sql = "select count(1) as cnt from work_challenges_result where state in('1','2') and companyno='".$companyno."' and challenges_idx='".$idx."'".$where."";
		$user_tlist_info = selectQuery($sql);
		if($user_tlist_info['cnt']){
			$user_file_cnt = number_format($user_tlist_info['cnt']);
		}else{
			$user_file_cnt = 0;
		}

		//챌린지 참여 파일첨부형
		$sql ="select email, count(idx) as cnt from work_challenges_result where state in('1','2') and companyno='".$companyno."' and (comment!='' or (file_path!='' and file_name!='')) and challenges_idx='".$idx."' group by email";
		$user_list_file = selectAllQuery($sql);
		if($user_list_file['email']){
			$user_file_count = @array_combine($user_list_file['email'], $user_list_file['cnt']);
		}

		//$sql = "select idx, state, email, name, part, convert(varchar(20), file_regdate, 120) as reg, convert(varchar(10), file_regdate, 23) as ymd, CONVERT(varchar(20), file_regdate, 22) as ddd, file_path, file_name, file_real_name, file_real_img_name, file_type from work_challenges_result where state in ('1','2') and challenges_idx='".$idx."' and comment!='' and file_path!='' and file_name!=''".$where." order by reg desc";
		$sql ="select idx, state, email, name, part, comment, DATE_FORMAT(comment_regdate, '%Y-%m-%d %H:%i:%s') as com_reg, DATE_FORMAT(comment_regdate, '%Y-%m-%d') as com_ymd, DATE_FORMAT(comment_regdate, '%Y-%m-%d %H:%i:%s') as com_ddd, DATE_FORMAT(comment_regdate, '%Y-%m-%d %H:%i:%s') as file_reg, DATE_FORMAT(comment_regdate, '%Y-%m-%d') as file_ymd, DATE_FORMAT(comment_regdate, '%Y-%m-%d %H:%i:%s') as file_ddd, DATE_FORMAT(comment_regdate, '%l:%i %p') as com_time";
		$sql = $sql .=", resize, file_path, file_name, file_real_name, file_ori_path, file_ori_name, file_real_img_name, file_type from work_challenges_result where state in ('1','2') and companyno='".$companyno."' and challenges_idx='".$idx."'".$where." order by com_reg desc";
		$chall_mix_info = selectAllQuery($sql);
		if($chall_mix_info['idx']){
			$chamyeo_file_cnt = count($chall_mix_info['idx']);
		}else{
			$chamyeo_file_cnt = 0;
		}

		//오늘 참여한 내역 체크
		$sql = "select idx from work_challenges_result where state in('1','2') and companyno='".$companyno."' and challenges_idx='".$idx."' and file_path!='' and file_name!='' and email='".$user_id."' and DATE_FORMAT(file_regdate, '%Y-%m-%d')='".TODATE."'";
		$ch_file_info = selectQuery($sql);

		//참여횟수체크
		if($chamyeo_file_cnt >= $attend || !$ch_file_info['idx']){
			$chamyeo_chk = false;
		}else{
			$chamyeo_chk = true;
		}

		$sql ="select DATE_FORMAT(comment_regdate, '%Y-%m-%d') as ymd from work_challenges_result where state='1' and companyno='".$companyno."' and challenges_idx='".$idx."'";
		$sql = $sql .=" group by DATE_FORMAT(comment_regdate, '%Y-%m-%d') order by DATE_FORMAT(comment_regdate, '%Y-%m-%d') desc";
		$date_mix_info = selectAllQuery($sql);



		$sql ="select idx, state, email, name, part, comment, DATE_FORMAT(comment_regdate, '%Y-%m-%d %H:%i:%s') as com_reg, DATE_FORMAT(comment_regdate, '%Y-%m-%d') as com_ymd, DATE_FORMAT(comment_regdate, '%Y-%m-%d %H:%i:%s') as com_ddd, DATE_FORMAT(file_regdate, '%Y-%m-%d %H:%i:%s') as file_reg, DATE_FORMAT(file_regdate, '%Y-%m-%d') as file_ymd, DATE_FORMAT(file_regdate, '%Y-%m-%d %H:%i:%s') as file_ddd";
		$sql = $sql .=", resize, file_path, file_name, file_real_name, file_ori_path, file_ori_name, file_real_img_name, file_type from work_challenges_result where state in ('1','2') and companyno='".$companyno."' and comment!='' and file_path!='' and file_name!='' and challenges_idx='".$idx."' order by com_reg desc";
		$list_info = selectAllQuery($sql);
		if($list_info['idx']){
			$list_cnt = number_format(count($list_info['idx']));
		}


		if($user_date && $user_date!="all"){
			$text_date = $user_date;
		}else{
			$text_date = "전체보기";
		}


		for($i=0; $i<count($chall_mix_info['idx']); $i++){

			$cidx = $chall_mix_info['idx'][$i];
			$cemail = $chall_mix_info['email'][$i];
			$cname = $chall_mix_info['name'][$i];
			$cpart = $chall_mix_info['part'][$i];
			$state = $chall_mix_info['state'][$i];

			//인증메시지
			$com_reg = $chall_mix_info['com_reg'][$i];
			$com_ymd = $chall_mix_info['com_ymd'][$i];
			$com_his = $chall_mix_info['com_ddd'][$i];
			$com_time = $chall_mix_info['com_time'][$i];

			//인증파일
			$file_reg = $chall_mix_info['file_reg'][$i];
			$file_ymd = $chall_mix_info['file_ymd'][$i];
			$file_his = $chall_mix_info['file_ddd'][$i];

			$comment = urldecode($chall_mix_info['comment'][$i]);
			$resize = $chall_mix_info['resize'][$i];
			$file_type = $chall_mix_info['file_type'][$i];

			if($file_type == "video/mp4"){
				$file_path = $list_info['file_path'][$i];
				$file_name = $list_info['file_name'][$i];
			}else if($resize == '0'){
				$file_path = $chall_mix_info['file_ori_path'][$i];
				$file_name = $chall_mix_info['file_ori_name'][$i];
			}else{
				$file_path = $chall_mix_info['file_path'][$i];
				$file_name = $chall_mix_info['file_name'][$i];
			}

			$file_real_name = $chall_mix_info['file_real_name'][$i];
			$file_real_img_name = $chall_mix_info['file_real_img_name'][$i];
			$cfiles = $file_path.$file_name;

			$ori_img_src = $chall_mix_info['file_ori_path'][$i].$chall_mix_info['file_ori_name'][$i];


			//인증메시지 요일구함
			$com_reg_date = explode("-", $com_ymd);
			$int_reg = date("w", strtotime($com_reg));
			$com_date_yoil =  $weeks[$int_reg];
			if($com_reg_date){
				$com_reg_date_m = $com_reg_date[1];
				$com_reg_date_d = preg_replace('/(0)(\d)/','$2', $com_reg_date[2]);
				$com_date_md = $com_reg_date_m."월 ". $com_reg_date_d."일";
			}

			//인증파일 요일구함
			$file_reg_date = explode("-", $file_ymd);
			$int_reg = date("w", strtotime($file_reg));
			$file_date_yoil =  $weeks[$int_reg];

			if($file_reg_date){
				$file_reg_date_m = $file_reg_date[1];
				$file_reg_date_d = preg_replace('/(0)(\d)/','$2', $file_reg_date[2]);
				$file_date_md = $file_reg_date_m."월 ". $file_reg_date_d."일";
			}

			//전체내역
			$chall_mix_list[$com_ymd][$i]['date'] = $com_ymd;
			$chall_mix_list[$com_ymd][$i]['idx'] = $cidx;
			$chall_mix_list[$com_ymd][$i]['email'] = $cemail;
			$chall_mix_list[$com_ymd][$i]['name'] = $cname;
			$chall_mix_list[$com_ymd][$i]['part'] = $cpart;
			$chall_mix_list[$com_ymd][$i]['comment'] = $comment;
			$chall_mix_list[$file_ymd][$i]['files'] = $cfiles;

			$chall_mix_list[$com_ymd][$i]['com_yoil'] = $com_date_yoil;
			$chall_mix_list[$com_ymd][$i]['com_md'] = $com_date_md;
			$chall_mix_list[$com_ymd][$i]['com_hi'] = $com_time;

			$chall_mix_list[$file_ymd][$i]['file_yoil'] = $file_date_yoil;
			$chall_mix_list[$file_ymd][$i]['file_md'] = $file_date_md;
			$chall_mix_list[$file_ymd][$i]['file_hi'] = $file_chiss;
			$chall_mix_list[$file_ymd][$i]['file_type'] = $file_type;

			$chall_mix_list[$cidx]['ori_img'] = $ori_img_src;
			$chall_mix_list[$com_ymd][$i]['state'] = $state;



			$chall_mix_list_ymd[]= $com_ymd;

			//이미지형, 파일형
			if(@in_array($file_type , $image_type_array)){
				$chall_mix_list[$file_ymd][$i]['file_real_name'] = $file_real_img_name;
			}else{
				$chall_mix_list[$file_ymd][$i]['file_real_name'] = $file_real_name;
			}
		}

		//배열키값 중복제거
		$chall_mix_list_ymd = array_unique($chall_mix_list_ymd);

		//배열키값 리셋
		$chall_mix_list_ymd = array_key_reset($chall_mix_list_ymd);


		/*print "<pre>";
		print_r($chall_mix_list);
		print "</pre>";
		*/

		//이메일 선택시
		if($list_idx == "all"){

			$user_tlist_cnt = count($chall_mix_info['idx']);
			$select_name = "전체";
		}else{
			$user_tlist_cnt = count($chall_mix_info['idx']);
			$sql = "select idx, name, part from work_member where state='0' and companyno='".$companyno."' and email='".$list_idx."'";
			$mem_info = selectQuery($sql);
			if($mem_info['idx']){
				$select_name = $mem_info['name'];
				$select_part = $mem_info['part'];
			}
		}

		if(!$select_name && count($chall_mix_info['idx'])==0){
			$select_name = "전체";
		}


		//좋아요 리스트
		$like_flag_list = array();
		$sql = "select idx, email,service, work_idx, send_email, like_flag from work_todaywork_like where state='0' and companyno='".$companyno."' and send_email='".$user_id."' and workdate='".TODATE."'";
		$like_info = selectAllQuery($sql);
		for($i=0; $i<count($like_info['idx']); $i++){
			$like_info_idx = $like_info['idx'][$i];
			$like_info_email = $like_info['email'][$i];
			$like_info_work_idx = $like_info['work_idx'][$i];
			$like_info_like_flag = $like_info['like_flag'][$i];
			$like_info_send_email = $like_info['send_email'][$i];

			$work_like_list[$like_info_work_idx] = $like_info_idx;
		}
	?>

		<div class="list_function">
			<div class="list_function_in">
				<div class="list_function_left">
				<?=$select_name?> <span><?=$select_part?></span><strong><?=$chamyeo_file_cnt?></strong>
				</div>
				<div class="list_function_right">
					<div class="list_function_sort">
						<div class="list_function_sort_in">
						<input type="hidden" id="user_email" value="<?=$list_idx?>">
							<input type="hidden" id="user_date" value="<?=$user_date?>">
							<button class="btn_sort_on" id="auth_mix_date"><span><?=$text_date?></span></button>
							<ul>
								<li><button id="mix_reg0" value="all"><span>전체보기</span></button></li>
								<?for($i=0; $i<count($date_mix_info['ymd']); $i++){?>
									<li><button id="mix_reg<?=($i+1)?>" value="<?=$date_mix_info['ymd'][$i]?>"><span><?=$date_mix_info['ymd'][$i]?></span></button></li>
								<?}?>
							</ul>
						</div>
					</div>
				</div>
			</div>
		</div>

		<?if($chall_mix_list_ymd){?>
			<div class="mix_zone">
				<div class="mix_zone_in">

				<?	$k=0;
					for($i=0; $i<count($chall_mix_list_ymd); $i++){
						$date_ymd = trim($chall_mix_list_ymd[$i]);

						$view_date = $chall_mix_list[$date_ymd][$k]['date'];

						$tmp_date = explode("-",$view_date);
						if($tmp_date){
							//$tmp_month = preg_replace('/(0)(\d)/','$2', $tmp_date[1]);
							//$tmp_day = preg_replace('/(0)(\d)/','$2', $tmp_date[2]);
							$tmp_month = (int)$tmp_date[1];
							$tmp_day = (int)$tmp_date[2];

							$real_date = $tmp_date[0] . "년 " .$tmp_month."월 " .$tmp_day."일 ";
						}
					?>

						<div class="mix_date">
							<span><?=$real_date?><?=$chall_mix_list[$date_ymd][$k]['com_yoil']?></span>
						</div>

						<?for($j=0; $j<count($chall_mix_list[$date_ymd]); $j++){
							$idx = $chall_mix_list[$date_ymd][$k]['idx'];
							$chall_mix_email = $chall_mix_list[$date_ymd][$k]['email'];

							//프로필 캐릭터,사진
							$profile_main_img_src = profile_img_info($chall_mix_email);

						?>
							<div class="mix_area">
								<div class="mix_area_in<?=($chall_mix_list[$date_ymd][$k]['state']=="2")?" chk_none":""?>" id="mix_list_chk<?=$k?>" value="<?=$chall_mix_list[$date_ymd][$k]['idx']?>">
									<div class="mix_img" style="background-image:url('<?=$profile_main_img_src?>');">
										<button class="mix_chk"><span>선택</span></button>
									</div>
									<div class="mix_info">
										<div class="mix_user">
											<strong><?=$chall_mix_list[$date_ymd][$k]['name']?></strong>
											<span><?=$chall_mix_list[$date_ymd][$k]['part']?></span>
										</div>
										<div class="mix_box">
											<?if($chall_mix_list[$date_ymd][$k]['comment']){?>
												
												<p class="mix_txt"><?=textarea_replace($chall_mix_list[$date_ymd][$k]['comment'])?></p>
												<span class="mix_time"><?=$chall_mix_list[$date_ymd][$k]['com_hi']?></span>
												<?if($user_id!=$chall_mix_list[$date_ymd][$k]['email']){
													$cham_idx = $chall_mix_list[$date_ymd][$k]['idx'] ?>
													<button class="mix_jjim<?=$work_like_list[$cham_idx]>0?" on":""?>" id="mix_jjim_<?=$cham_idx?>" value="<?=$cham_idx?>"><span>좋아요</span></button>
												<?}?>
											<?}?>	
											<button class="mix_memo chall_view_memo" value="<?=$idx?>" id=""><span>메모하기</span></button>	
										</div>
										<? if($chall_mix_list[$date_ymd][$k]['state']=="2"){?>
											<div class="mix_warning">
												<span>무효 후 코인 회수 처리되었습니다.</span>
											</div>
										<?}?>
									</div>$idx
									<div class="mix_imgs">
										<? $sql = "select idx,file_ori_path, file_ori_name, state, file_real_img_name from work_challenges_file_info where state = '1' and challenges_idx = '".$idx."' and file_type like '%image%' order by idx desc ";
										$query = selectAllQuery($sql); ?>
										<div class="mix_imgs_box">
										<?	for($z=0;$z<count($query['idx']);$z++){
											$file_print = $query['file_ori_path'][$z].$query['file_ori_name'][$z];
											?>
											<img src="<?=$file_print?>" value="<?=$query['idx'][$z]?>" id="img_<?=$z?>" alt=""/>
										<?}?>
										</div>
										<div class="tdw_list_file_box">
										<?
										$sql = "select idx, file_path, file_name, state, file_real_img_name from work_challenges_file_info where state = '1' and challenges_idx = '".$idx."' and file_type not like '%image%' order by idx desc ";
										$query2 = selectAllQuery($sql);
										for($u=0;$u<count($query2['idx']);$u++){
											$file_real = $query2['file_path'][$u].$query2['file_name'][$u];?>
											<button class="btn_list_file" id="btn_list_file_<?=$k?>" value="<?=$query2['idx'][$u]?>"><span><?=$query2['file_real_img_name'][$u]?></span></button>
											
										<?}?>
										</div>
										
										<?if(!$chall_mix_list[$date_ymd][$k]['comment']){?>
											<span class="mix_time"><?=$chall_mix_list[$date_ymd][$k]['com_hi']?></span>
											<?if($user_id!=$chall_mix_list[$date_ymd][$k]['email']){
												$cham_idx = $chall_mix_list[$date_ymd][$k]['idx'];?>
											<button class="mix_jjim<?=$work_like_list[$cham_idx]>0?" on":""?>" id="mix_jjim_<?=$cham_idx?>" value="<?=$cham_idx?>"><span>좋아요</span></button>
											<?}?>
										<?}?>
										<?if($chall_mix_list[$date_ymd][$k]['email']==$user_id){?>
											<div class="mesage_btn">
												<input type="hidden" value="<?=$chall_mix_list[$date_ymd][$k]['idx']?>" id="chall_view_update">
												<button class="mesage_corr" id="mesage_corr_list"><span>수정</span></button>
												<button class="mesage_del" id="mesage_del_list"><span>삭제</span></button>
											</div>
											<!-- <div class="layer_cha_join join_type_mix" id="layer_cha_update_list" style="display:none;">
												<input type="hidden" id="chamyeo_idx" value="<?=$chall_mix_list[$date_ymd][$k]['idx']?>">
												<div class="layer_deam"></div>
												<div class="layer_cha_join_in">
													<div class="layer_cha_join_box">
														<div class="layer_cha_join_title">
															<strong>챌린지 참여 수정</strong>
															<span>확인 메시지와 사진을 수정할 수 있습니다.</span>
														</div>
														<div class="layer_cha_join_area">
															<div class="layer_cha_join_input">
															<? $nl2br_arr = array("<br />","<br/>","<br/><br />");
															$result = str_replace($nl2br_arr,"",$chall_mix_list[$date_ymd][$k]['comment']); ?>	
																<textarea name="" id="cham_comment"><?=$result?></textarea>
															</div>
															<div class="layer_cha_join_file_desc" >
																<input type="hidden" id="mix_file_name" value="<?=$chall_files_info['file_real_img_name']?>" />
																<? for($z=0;$z<count($query['idx']);$z++){?>
																	<div class="file_desc" id="chall_file_desc_<?=$z?>">
																		<input type="hidden" id="mix_file_idx_<?=$z?>" value="<?=$query['idx'][$z]?>">
																		<span><?=$query['file_real_img_name'][$z]?></span>
																		<button id="mix_file_del_<?=$z?>">삭제</button>
																	</div>
																<?}?>
															</div>
															<div class="layer_cha_join_file">
																<div class="file_box">
																	<input type="file" id="file_01" class="input_file" multiple/>
																	<label for="file_01" class="label_file"><span>파일첨부</span></label>
																</div>
															</div>
														</div>
														<div class="layer_result_btns">
															<div class="layer_result_btns_in">
																<div class="btns_right">
																	<button class="btns_cha_cancel" id="btns_chamyeo_cancel"><span>취소</span></button>
																	<button class="btns_cha_join on" id="btns_chamyeo_update"><span>수정하기</span></button>
																</div>
															</div>
														</div>
													</div>
												</div>
											</div> -->
										<?}?>
									</div>
								</div>
								<?
									$sql = "select idx,email,name,comment,regdate from work_challenges_comment where state = '1' and result_idx = '".$cidx."' order by idx desc";
									$query = selectAllQuery($sql);
								?>
									<div class="tdw_list_memo_area" id="chall_memo_area_<?=$query['idx'][$p]?>">
										<div class="tdw_list_memo_area_in" id="memo_area_in_<?=$cidx?>">
										<? for($p=0; $p<count($query['idx']); $p++){?>
										<div class="tdw_list_memo_desc" id="resultCo_<?=$query['idx'][$p]?>" value="<?=$query['idx'][$p]?>">
											<div class="tdw_list_memo_name"><?=$query['name'][$p]?></div>
											<input type="hidden" id="memo_id" value="<?=$query['email'][$p]?>">
											<div class="tdw_list_memo_conts">
												<div class="tdw_list_memo_conts_txt" id="chall_memo_<?=$query['idx'][$p]?>">
													<strong><?=$query['comment'][$p]?></strong>
													<div class="tdw_list_memo_regi" id="chall_memo_<?=$query['idx'][$p]?>">
														<textarea name="" class="textarea_regi"><?=$query['comment'][$p]?></textarea>
														<div class="btn_regi_box">
															<button class="btn_regi_submit" value="<?=$query['idx'][$p]?>"><span>확인</span></button>
															<button class="btn_regi_cancel" value="<?=$query['idx'][$p]?>"><span>취소</span></button>
														</div>
													</div>
												</div>
												<em class="tdw_list_memo_conts_date"><?=$query['regdate'][$p]?>
													<? if($query['email'][$p] == $user_id){?>
														<button class="btn_memo_del" id="layer_memo_delete" value="<?=$query['idx'][$p]?>"><span>삭제</span></button>
													<?}?>
												</em>
											</div>
										</div>
										<?}?>
									</div>
								</div>
							</div>
						<?
						$k++;
						}?>
					<?}?>
				</div>
			</div>
		<?}else{?>
			<div class="mix_area_none">
				<strong>등록된 인증파일 + 인증메시지가 없습니다.</strong>
			</div>
		<?}
	}
	exit;
}



//챌린지 참여, 인증메시지 리스트
if($mode == "masage_list_top3"){

	$idx = $_POST['idx'];
	$list_idx = $_POST['list_idx'];
	$idx = preg_replace("/[^0-9]/", "", $idx);
	$list_idx = trim($list_idx);
	$input_val = $_POST['input_val'];
	$user_date = $_POST['user_date'];

	if($idx){

		$sql ="select idx, state, email, name, part, comment, DATE_FORMAT(comment_regdate, '%Y-%m-%d %H:%i:%s') as reg, DATE_FORMAT(comment_regdate, '%Y-%m-%d') as ymd, DATE_FORMAT(comment_regdate, '%Y-%m-%d %H:%i:%s') as ddd from work_challenges_result where state in('1','2') and companyno='".$companyno."' and comment!='' and challenges_idx='".$idx."' order by reg desc";
		$list_info = selectAllQuery($sql);
		if($list_info['idx']){
			$list_cnt = number_format(count($list_info['idx']));
		}

		for($i=0; $i<count($list_info['idx']); $i++){

			$cidx = $list_info['idx'][$i];
			$cemail = $list_info['email'][$i];
			$cname = $list_info['name'][$i];
			$cpart = $list_info['part'][$i];
			$creg = $list_info['reg'][$i];
			$cymd = $list_info['ymd'][$i];
			$chis = $list_info['ddd'][$i];
			$state = $list_info['state'][$i];
			$comment = urldecode($list_info['comment'][$i]);

			//요일구함
			$reg_date = explode("-", $cymd);
			$int_reg = date("w", strtotime($creg));
			$date_yoil =  $weeks[$int_reg];

			if($reg_date){
				$reg_date_m = $reg_date[1];
				$reg_date_d = preg_replace('/(0)(\d)/','$2', $reg_date[2]);
				$date_md = $reg_date_m."월 ". $reg_date_d."일";
			}

			//시간 오전,오후
			if($chis){
				$chis_tmp = explode(" ", $chis);
				if ($chis_tmp['3'] > "AM"){
					$after = "오후 ";
				}else{
					$after = "오전 ";
				}
				$ctime = explode(":", $chis_tmp['2']);
				$chiss = $after . $ctime['0'] .":". $ctime['1'];
			}


			//최근 3건의 내역
			if($i < 3){
				$masage_list_top3[$cymd][$i]['date'] = $cymd;
				$masage_list_top3[$cymd][$i]['idx'] = $cidx;
				$masage_list_top3[$cymd][$i]['email'] = $cemail;
				$masage_list_top3[$cymd][$i]['name'] = $cname;
				$masage_list_top3[$cymd][$i]['part'] = $cpart;
				$masage_list_top3[$cymd][$i]['comment'] = $comment;
				$masage_list_top3[$cymd][$i]['yoil'] = $date_yoil;
				$masage_list_top3[$cymd][$i]['md'] = $date_md;
				$masage_list_top3[$cymd][$i]['hi'] = $chiss;
				$masage_list_top3[$cymd][$i]['state'] = $state;
				$masage_list_ymd_top3[]= $cymd;
			}


			//전체내역
			$masage_list[$cymd][$i]['date'] = $cymd;
			$masage_list[$cymd][$i]['idx'] = $cidx;
			$masage_list[$cymd][$i]['email'] = $cemail;
			$masage_list[$cymd][$i]['name'] = $cname;
			$masage_list[$cymd][$i]['part'] = $cpart;
			$masage_list[$cymd][$i]['comment'] = $comment;
			$masage_list[$cymd][$i]['yoil'] = $date_yoil;
			$masage_list[$cymd][$i]['md'] = $date_md;
			$masage_list[$cymd][$i]['hi'] = $chiss;
			$masage_list[$cymd][$i]['state'] = $state;
			$masage_list_ymd[]= $cymd;
		}


		//배열키값 중복제거
		$masage_list_ymd_top3 = array_unique($masage_list_ymd_top3);

		//배열키값 리셋
		$masage_list_ymd_top3 = array_key_reset($masage_list_ymd_top3);


		//배열키값 중복제거
		$masage_list_ymd = array_unique($masage_list_ymd);

		//배열키값 리셋
		$masage_list_ymd = array_key_reset($masage_list_ymd);


		//이메일 선택시
		if($list_idx == "all"){

			$user_tlist_cnt = count($list_info['idx']);
			$select_name = "전체";

		}else{

			$user_tlist_cnt = count($list_info['idx']);
			$sql = "select idx, name, part from work_member where state='0' and companyno='".$companyno."' and email='".$list_idx."'";
			$mem_info = selectQuery($sql);
			if($mem_info['idx']){
				$select_name = $mem_info['name'];
				$select_part = $mem_info['part'];
			}
		}

		if(!$select_name && count($list_info['idx'])==0){
			$select_name = "전체";
		}

		$sql ="select idx, state, name, part, comment, DATE_FORMAT(comment_regdate, '%Y-%m-%d %H:%i:%s') as reg, DATE_FORMAT(comment_regdate, '%Y-%m-%d') as ymd, DATE_FORMAT(comment_regdate, '%Y-%m-%d %H:%i:%s') as ddd from work_challenges_result where state='1' and comment!='' and companyno='".$companyno."' and challenges_idx='".$idx."' order by reg desc";
		$list_info = selectAllQuery($sql);
		if($list_info['idx']){
			$list_cnt = number_format(count($list_info['idx']));
		}


		//좋아요 리스트
		$like_flag_list = array();
		$sql = "select idx, email,service, work_idx, send_email, like_flag from work_todaywork_like where state='0' and companyno='".$companyno."' and send_email='".$user_id."' and workdate='".TODATE."'";
		$like_info = selectAllQuery($sql);
		for($i=0; $i<count($like_info['idx']); $i++){
			$like_info_idx = $like_info['idx'][$i];
			$like_info_email = $like_info['email'][$i];
			$like_info_work_idx = $like_info['work_idx'][$i];
			$like_info_like_flag = $like_info['like_flag'][$i];
			$like_info_send_email = $like_info['send_email'][$i];

			$work_like_list[$like_info_work_idx] = $like_info_idx;
		}

		?>

		<div class="title_area">
			<strong class="title_main">인증메시지</strong>
			<span class="title_point"><?=$list_cnt?></span>
			<a href="#" class="title_more"><span>더보기</span></a>
		</div>
		<div class="masage_zone">
			<?
			if ($masage_list_ymd_top3){
				$k=0;
				for($i=0; $i<count($masage_list_ymd_top3); $i++){
						$date_ymd = trim($masage_list_ymd_top3[$i]);
				?>

					<div class="masage_date">
						<span><?=$masage_list_top3[$date_ymd][$k]['md']?> <?=$masage_list_top3[$date_ymd][$k]['yoil']?></span>
					</div>

					<?for($j=0; $j<count($masage_list_top3[$date_ymd]); $j++){?>

					<div class="masage_area">
						<div class="masage_img">
							<img src="/html/images/pre/ico_user_001.png" alt="" />
						</div>
						<div class="masage_info">
							<div class="masage_user">
								<strong><?=$masage_list_top3[$date_ymd][$k]['name']?></strong>
								<span><?=$masage_list_top3[$date_ymd][$k]['part']?></span>
							</div>
							<div class="masage_box">
								<p class="masage_txt"><?=$masage_list_top3[$date_ymd][$k]['comment']?></p>
								<span class="masage_time"><?=$masage_list_top3[$date_ymd][$k]['hi']?></span>
								<?if($user_id!=$masage_list_top3[$date_ymd][$k]['email']){?>
									<button class="masage_jjim<?=$work_like_list[$masage_list_top3[$date_ymd][$k]['idx']]>0?" on":""?>" id="masage_jjim" value="<?=$masage_list_top3[$date_ymd][$k]['idx']?>"><span>좋아요</span></button>
								<?}?>
							</div>
						</div>
					</div>
					<?
					$k++;
					}
				}
			}?>
		</div>
	<?}?>

	<?exit;
}




//혼합형 상위3개
if($mode == "mix_list_top3"){

	print "<pre>";
	print_r($_POST);
	print "</pre>";

	$idx = $_POST['idx'];
	$idx = preg_replace("/[^0-9]/", "", $idx);

	if($idx){

		//참여가능기간
		$where = " and (DATE_FORMAT(comment_regdate, '%Y-%m-%d')>='".$sdate."' and DATE_FORMAT(comment_regdate, '%Y-%m-%d')<='".$edate."' or DATE_FORMAT(file_regdate, '%Y-%m-%d')>='".$sdate."' and DATE_FORMAT(file_regdate, '%Y-%m-%d')<='".$edate."')";

		//챌린지 참여 횟수가 한번만 참여
		if ($day_type == "0"){

			//완료한 인증메시지 + 인증파일 체크
			$sql = "select idx, comment from work_challenges_result where state='1' and companyno='".$companyno."' and comment!='' and file_path!='' and file_name!='' and challenges_idx='".$idx."' and email='".$user_id."'".$where." order by idx desc limit 1";
			$chall_mix_info = selectQuery($sql);
			if($chall_mix_info['idx']){
				$chamyeo_btn = true;
				$chall_mix_idx = $chall_mix_info['idx'];
				$chall_comment_contents = urldecode($chall_mix_info['comment']);
			}else{

				//도전중인 인증메시지 체크
				$sql = "select idx from work_challenges_result where state='0' and companyno='".$companyno."' and challenges_idx='".$idx."' and comment is null and file_path is null and file_name is null and email='".$user_id."'".$where." order by idx desc limit 1";
				$chall_mix_info = selectQuery($sql);
				if($chall_mix_info['idx']){
					$chall_mix_idx = $chall_mix_info['idx'];
				}
			}

			$chall_list_mix_idx = $chall_mix_idx;

		}else if ($day_type == "1"){

			//하루 한번 참여가능
			$where = " and (DATE_FORMAT(comment_regdate, '%Y-%m-%d')='".TODATE."' or DATE_FORMAT(file_regdate, '%Y-%m-%d')='".TODATE."')";
			$sql = "select idx, comment from work_challenges_result where state='1' and companyno='".$companyno."' and challenges_idx='".$idx."' and comment!=' and file_path!='' and file_name!='' and email='".$user_id."'".$where." order by idx desc limit 1";
			$chall_mix_info = selectQuery($sql);
			if($chall_mix_info['idx']){
				$chamyeo_btn = true;
				$chall_mix_idx = $chall_mix_info['idx'];
				$chall_comment_contents = urldecode($chall_mix_info['comment']);
			}else{
				//도전중인 혼합형 체크
				$sql = "select idx from work_challenges_result where state='0' and companyno='".$companyno."' and challenges_idx='".$idx."' and comment!=' and file_path!='' and file_name!='' and email='".$user_id."'".$where." order by idx desc limit 1";
				$chall_mix_info = selectQuery($sql);
				if($chall_mix_info['idx']){
					$chall_mix_idx = $chall_mix_info['idx'];
				}
			}

			$chall_list_mix_idx = $chall_mix_idx;


			//챌린지 참여회수 체크
			$sql = "select count(a.idx) as cnt from work_challenges_result";
			$sql = $sql .= " where state='1' and companyno='".$companyno."' and challenges_idx='".$idx."' and comment!='' and file_path!='' and file_name!='' and email='".$user_id."'".$where."";
			$chall_files_info = selectQuery($sql);
			if ($chall_files_info['cnt'] >= $ch_info['attend']){
				$chamyeo_btn = true;
			}
		}


		$sql = "select count(1) as cnt from work_challenges_result where state in('1','2') and companyno='".$companyno."' and challenges_idx='".$idx."'";
		$user_tlist_info = selectQuery($sql);
		if($user_tlist_info['cnt']){
			$user_masage_cnt = number_format($user_tlist_info['cnt']);
		}else{
			$user_masage_cnt = 0;
		}

		//챌린지 참여 메시지형
		$sql ="select email, count(idx) as cnt from work_challenges_result where state in('1','2') and companyno='".$companyno."' and comment!='' and challenges_idx='".$idx."' group by email";
		$user_list_info = selectAllQuery($sql);
		if($user_list_info['email']){
			$user_list_count = @array_combine($user_list_info['email'], $user_list_info['cnt']);
		}


		//혼합형
		$sql ="select idx, state, email, name, part, comment, DATE_FORMAT(comment_regdate, '%Y-%m-%d %H:%i:%s') as com_reg, DATE_FORMAT(comment_regdate, '%Y-%m-%d') as com_ymd, DATE_FORMAT(comment_regdate, '%Y-%m-%d %H:%i:%s') as com_ddd, DATE_FORMAT(file_regdate, '%Y-%m-%d %H:%i:%s') as file_reg, DATE_FORMAT(file_regdate, '%Y-%m-%d') as file_ymd, DATE_FORMAT(file_regdate, '%Y-%m-%d %H:%i:%s') as file_ddd";
		$sql = $sql .=", resize, file_path, file_name, file_real_name, file_ori_path, file_ori_name, file_real_img_name, file_type from work_challenges_result where state in ('1','2') and companyno='".$companyno."' and comment!='' and file_path!='' and file_name!='' and challenges_idx='".$idx."' order by com_reg desc";
		$list_info = selectAllQuery($sql);
		if($list_info['idx']){
			$list_cnt = number_format(count($list_info['idx']));
		}

		//$sql = "select idx, state, email, name, part, convert(varchar(20), file_regdate, 120) as reg, convert(varchar(10), file_regdate, 23) as ymd, CONVERT(varchar(20), file_regdate, 22) as ddd";
		//$sql = $sql .=", resize, file_path, file_name, file_real_name, file_ori_path, file_ori_name, file_real_img_name, file_type from work_challenges_result where state in('1','2') and challenges_idx='".$idx."' order by reg desc";

		for($i=0; $i<count($list_info['idx']); $i++){

			$cidx = $list_info['idx'][$i];
			$cemail = $list_info['email'][$i];
			$cname = $list_info['name'][$i];
			$cpart = $list_info['part'][$i];
			$state = $list_info['state'][$i];

			//인증메시지
			$com_reg = $list_info['com_reg'][$i];
			$com_ymd = $list_info['com_ymd'][$i];
			$com_his = $list_info['com_ddd'][$i];

			//인증파일
			$file_reg = $list_info['file_reg'][$i];
			$file_ymd = $list_info['file_ymd'][$i];
			$file_his = $list_info['file_ddd'][$i];

			$comment = urldecode($list_info['comment'][$i]);
			$resize = $list_info['resize'][$i];
			if($resize == '0'){
				$file_path = $list_info['file_ori_path'][$i];
				$file_name = $list_info['file_ori_name'][$i];
			}else{
				$file_path = $list_info['file_path'][$i];
				$file_name = $list_info['file_name'][$i];
			}

			$file_type = $list_info['file_type'][$i];
			$file_real_name = $list_info['file_real_name'][$i];
			$file_real_img_name = $list_info['file_real_img_name'][$i];
			$cfiles = $file_path.$file_name;

			$ori_img_src = $list_info['file_ori_path'][$i].$list_info['file_ori_name'][$i];

			//인증메시지 요일구함
			$com_reg_date = explode("-", $com_ymd);
			$int_reg = date("w", strtotime($com_reg));
			$com_date_yoil =  $weeks[$int_reg];
			if($com_reg_date){
				$com_reg_date_m = (int)$com_reg_date[1];
				$com_reg_date_d = preg_replace('/(0)(\d)/','$2', $com_reg_date[2]);
				$com_date_md = $com_reg_date_m."월 ". $com_reg_date_d."일 ";
			}
			//시간 오전,오후
			if($com_his){
				$com_his = str_replace("  "," ",$com_his);
				$com_his_tmp = @explode(" ", $com_his);

				if ($com_his_tmp[1] > '12:00:00'){
					$com_his_tmp_a = @explode(":",$com_his_tmp[1]);
					if($com_his_tmp[1] > '13:00:00'){
						$com_his_tmp_h = $com_his_tmp_a[0]-'12';
					}else{
						$com_his_tmp_h = $com_his_tmp_a[0];
					}
					
					$com_his_tmp_h = $com_his_tmp_h .":". $com_his_tmp_a[1];
					$after = "오후 ";
				}else{
					$com_his_tmp_h = $com_his_tmp[1];
					$after = "오전 ";
				}
				$com_ctime = @explode(":", $com_his_tmp_h);
				$com_chiss = $after . $com_ctime['0'] .":". $com_ctime['1'];
			}


			//인증파일 요일구함
			$file_reg_date = explode("-", $file_ymd);
			$int_reg = date("w", strtotime($file_reg));
			$file_date_yoil =  $weeks[$int_reg];

			if($file_reg_date){
				$file_reg_date_m = $file_reg_date[1];
				$file_reg_date_d = preg_replace('/(0)(\d)/','$2', $file_reg_date[2]);
				$file_date_md = $file_reg_date_m."월 ". $file_reg_date_d."일 ";
			}

			//시간 오전,오후
			if($file_his){
				$file_his = str_replace("  "," ",$file_his);
				$file_chis_tmp = @explode(" ", $file_his);

				if ($file_chis_tmp['2'] == "PM"){
					$after = "오후 ";
				}else{
					$after = "오전 ";
				}
				$file_ctime = @explode(":", $file_chis_tmp['1']);
				$file_chiss = $after . $file_ctime['0'] .":". $file_ctime['1'];
			}


			//최근 3건의 내역
			if($i < 3){

				//완료된건만 표기
				if($state == 1){
					$view_list_top3[$com_ymd][$i]['date'] = $com_ymd;
					$view_list_top3[$com_ymd][$i]['idx'] = $cidx;
					$view_list_top3[$com_ymd][$i]['email'] = $cemail;
					$view_list_top3[$com_ymd][$i]['name'] = $cname;
					$view_list_top3[$com_ymd][$i]['part'] = $cpart;

					$view_list_top3[$com_ymd][$i]['comment'] = $comment;
					$view_list_top3[$file_ymd][$i]['files'] = $cfiles;

					$view_list_top3[$com_ymd][$i]['com_yoil'] = $com_date_yoil;
					$view_list_top3[$com_ymd][$i]['com_md'] = $com_date_md;
					$view_list_top3[$com_ymd][$i]['com_hi'] = $com_chiss;

					$view_list_top3[$file_ymd][$i]['file_yoil'] = $file_date_yoil;
					$view_list_top3[$file_ymd][$i]['file_md'] = $file_date_md;
					$view_list_top3[$file_ymd][$i]['file_hi'] = $file_chiss;

					$view_list_top3[$com_ymd][$i]['state'] = $state;
					$view_list_ymd_top3[]= $com_ymd;
				}
			}
		}

		//배열키값 중복제거
		$view_list_ymd_top3 = array_unique($view_list_ymd_top3);

		//배열키값 리셋
		$view_list_ymd_top3 = array_key_reset($view_list_ymd_top3);


		//챌린지참여 전체 인증파일 참여횟수
		$sql = "select idx, state, email, name, part, DATE_FORMAT(file_regdate, '%Y-%m-%d %H:%i:%s') as reg, DATE_FORMAT(file_regdate, '%Y-%m-%d') as ymd,DATE_FORMAT(file_regdate, '%Y-%m-%d %H:%i:%s') as ddd";
		$sql = $sql .=", resize, file_path, file_name, file_real_name, file_ori_path, file_ori_name, file_real_img_name, file_type from work_challenges_result where state in('1','2') and companyno='".$companyno."' and challenges_idx='".$idx."' order by reg desc";
		$chall_file_info = selectAllQuery($sql);
		if($chall_file_info['idx']){
			$chamyeo_file_cnt = number_format(count($chall_file_info['idx']));
		}else{
			$chamyeo_file_cnt = "";
		}

		//좋아요 리스트
		$like_flag_list = array();
		$sql = "select idx, email,service, work_idx, send_email, like_flag from work_todaywork_like where state='0' and companyno='".$companyno."' and send_email='".$user_id."' and workdate='".TODATE."'";
		$like_info = selectAllQuery($sql);
		for($i=0; $i<count($like_info['idx']); $i++){
			$like_info_idx = $like_info['idx'][$i];
			$like_info_email = $like_info['email'][$i];
			$like_info_work_idx = $like_info['work_idx'][$i];
			$like_info_like_flag = $like_info['like_flag'][$i];
			$like_info_send_email = $like_info['send_email'][$i];

			$work_like_list[$like_info_work_idx] = $like_info_idx;
		}

		?>

			<div class="title_area">
				<strong class="title_main">인증파일 + 인증메시지</strong>
				<span class="title_point"><?=$chamyeo_file_cnt?></span>
				<a href="#" class="title_more" ><span>더보기</span></a>
			</div>
			<div class="mix_zone">

			<?if ($view_list_ymd_top3){

				$k=0;
				for($i=0; $i<count($view_list_ymd_top3); $i++){
					$date_ymd = trim($view_list_ymd_top3[$i]);
				?>
					<div class="mix_date">
						<span><?=$view_list_top3[$date_ymd][$k]['com_md']?> <?=$view_list_top3[$date_ymd][$k]['com_yoil']?></span>
					</div>

					<?for($j=0; $j<count($view_list_top3[$date_ymd]); $j++){

						$chall_mix_email = $view_list_top3[$date_ymd][$k]['email'];
						$profile_main_img_src = profile_img_info($chall_mix_email);

						?>
						<div class="mix_area">
							<div class="mix_img" style="background-image:url('<?=$profile_main_img_src?>');"></div>
							<div class="mix_info">
								<div class="mix_user">
								<strong><?=$view_list_top3[$date_ymd][$k]['name']?></strong>
									<span><?=$view_list_top3[$date_ymd][$k]['part']?></span>
								</div>
								<div class="mix_box">
									<p class="mix_txt"><?=textarea_replace($view_list_top3[$date_ymd][$k]['comment'])?></p>
									<span class="mix_time"><?=$view_list_top3[$date_ymd][$k]['com_hi']?></span>

									<?if($user_id!=$view_list_top3[$date_ymd][$k]['email']){
										$cham_idx = $view_list_top3[$date_ymd][$k]['idx'] ?>
										<button class="mix_jjim<?=$work_like_list[$cham_idx]>0?" on":""?>" id="mix_jjim_<?=$cham_idx?>" value="<?=$cham_idx?>"><span>좋아요</span></button>
									<?}?>

								</div>
							</div>

							<?if($view_list_top3[$date_ymd][$k]['files']){?>
								<div class="mix_imgs">
									<div class="mix_imgs_box">
										<img src="<?=$view_list_top3[$date_ymd][$k]['files']?>" value="<?=$query['idx'][$z]?>" id="img_<?=$z?>" alt="" style="width: 300px;"/>
									</div>
								</div>
							<?}?>
						</div>
					<?
					$k++;
					}
				}
			}
	}

	exit;
}








//챌린지 인증파일 삭제
if($mode == "challenges_file_del"){

	$chll_idx = $_POST['chll_idx'];
	$chll_idx = preg_replace("/[^0-9]/", "", $chll_idx);

	/*
	[mode] => challenges_file_del
    [user_email] => all
    [chll_idx] => 337
    [data_idx] => 42,31,26
	*/
	/*
	print "</pre>";
	print_r($_POST);
	print "<pre>";*/


	if($chll_idx){
		$data_idx = trim($_POST['data_idx']);
		$sql = "select idx, coin, coin_not, email, name, attend_type from work_challenges where state='0' and companyno='".$companyno."' and idx='".$chll_idx."'";
		$res_info = selectQuery($sql);
		if($res_info['idx']){

			$dcoin = $res_info['coin'];
			$coin_not = $res_info['coin_not'];

			$ch_email = $res_info['email'];
			$ch_name = $res_info['name'];

			$coin_info_text = "챌린지 참여 삭제";

			$data_idx = trim($_POST['data_idx']);
			$data_tmp = @explode(",", $data_idx);
			$im_data = @implode("','",$data_tmp);

			if($data_idx){
				$ret = "";
				for($i=0; $i<count($data_tmp); $i++){
					if($data_tmp[$i]){
						$sql = "select idx, name, resize, file_path, file_name, file_ori_path, file_ori_name from work_challenges_result where state in('1','2') and companyno='".$companyno."' and file_path!='' and file_name!='' and challenges_idx='".$chll_idx."' and idx='".$data_tmp[$i]."' and email='".$user_id."'";
						$file_info = selectQuery($sql);

						//본인파일 삭제
						if($file_info['idx']){

							$file_ori_path = $file_info['file_ori_path'];
							$file_ori_name = $file_info['file_ori_name'];
							$file_path = $file_info['file_path'];
							$file_name = $file_info['file_name'];

							$sql = "select idx, coin from work_coininfo where state='0' and code='".$ch_code[0]."' and companyno='".$companyno."' and email='".$user_id."' and work_idx='".$chll_idx."' and reward_type='".$reward_type[2]."' and auth_file_idx='".$file_info['idx']."'";
							$coin_info = selectQuery($sql);

							write_log("본인파일삭제");
							write_log("\n");
							write_log($sql);

							if($coin_info['idx']){

								//코인지급이 된 경우
								if ($coin_not != '1'){
									//회원코인정보 불러오기
									$mem_coin = email_coin($user_id);

									write_log("코인 :: " .$user_id. " , " . $mem_coin );

									if($mem_coin >= $dcoin){

										//파일삭제
										if($file_path && $file_name){
											@unlink($dir_file_path.$file_path.$file_name);
										}

										//파일삭제
										if($file_ori_path && $file_ori_name){
											@unlink($dir_file_path.$file_ori_path.$file_ori_name);
										}

										//코인회수 내역저장
										$sql = "insert into work_coininfo(state, code, work_idx, reward_type, auth_file_idx, companyno, email, name, reward_user, reward_name, coin, memo, workdate, ip)";
										$sql = $sql .=" values('0', '".$ch_code[1]."', '".$chll_idx."','".$reward_type[2]."', '".$file_info['idx']."', '".$companyno."', '".$user_id."', '".$file_info['name']."', '".$user_id."', '".$user_name."', '".$dcoin."', '".$coin_info_text."','".TODATE."','".LIP."')";
										$coin_res = insertQuery($sql);

										write_log("코인회수 내역저장");
										write_log($sql);

										$sql = "update work_challenges_result set state='9', file_path=null, file_name=null, file_size='0', file_ori_path=null, file_ori_name=null, file_ori_size='0', file_real_img_name=null, file_real_name=null, file_editdate=".DBDATE." where challenges_idx='".$chll_idx."' and companyno='".$companyno."' and idx='".$data_tmp[$i]."'";
										$file_up = updateQuery($sql);

										write_log("챌린지 업데이트");
										write_log($sql);

										if($coin_res && $file_up){
											//회원코인차감
											$sql = "update work_member set coin=coin-'".$dcoin."' where state='0' and companyno='".$companyno."' and email='".$user_id."'";
											$mem_up = updateQuery($sql);
											write_log("코인 회수 업데이트");
											write_log($sql);

											if($mem_up){

												$coin_info_text = "무효 후 코인 지급";
												//코인 다시 지급
												$sql = "insert into work_coininfo(state, code, work_idx, reward_type, auth_file_idx, companyno, email, name, reward_user, reward_name, coin, memo, workdate, ip)";
												$sql = $sql .=" values('0', '".$ch_code[0]."', '".$chll_idx."','".$reward_type[2]."', '".$file_info['idx']."', '".$companyno."', '".$ch_email."', '".$ch_name."', '".$user_id."', '".$user_name."', '".$dcoin."', '".$coin_info_text."','".TODATE."','".LIP."')";
												$coin_up = insertQuery($sql);
												write_log("코인 내역 회수 업데이트");
												write_log($sql);

												if($coin_up){
													//챌린지 만든 사용자에게 공용코인 다시지급
													$sql = "update work_member set comcoin=comcoin + '".$dcoin."' where state='0' and companyno='".$companyno."' and email='".$ch_email."'";
													$up_coin = updateQuery($sql);
													write_log("코인 다시 지급 업데이트");
													write_log($sql);
												}
												$ret[] = true;
											}
										}

									}else{
										echo "coin_minus";
										exit;
									}

								//코인지급을 안한경우
								}else{

									//무료 지급 된 내역 삭제
									$sql = "select idx from work_coininfo where state='0' and companyno='".$companyno."' and work_idx='".$chll_idx."' and reward_type='challenge' and auth_file_idx='".$file_info['idx']."'";
									$coin_info = selectQuery($sql);
									if($coin_info['idx']){

										//지급된 내역 삭제
										$sql = "update work_coininfo set state='9' where state='0' and companyno='".$companyno."' and reward_type='challenge' and auth_file_idx='".$file_info['idx']."' ";
										$up = updateQuery($sql);
										if($up){

											//파일삭제
											if($file_path && $file_name){
												@unlink($dir_file_path.$file_path.$file_name);
											}

											//파일삭제
											if($file_ori_path && $file_ori_name){
												@unlink($dir_file_path.$file_ori_path.$file_ori_name);
											}

											$sql = "update work_challenges_result set state='9', file_path=null, file_name=null, file_size='0', file_ori_path=null, file_ori_name=null, file_ori_size='0', file_real_img_name=null, file_real_name=null, file_editdate=".DBDATE." where challenges_idx='".$chll_idx."' and companyno='".$companyno."' and idx='".$data_tmp[$i]."'";
											$file_up = updateQuery($sql);
											if($file_up){
												$ret[] = true;
											}
										}
									}
								}
							}
						}
					}
				}

				//선택한 갯수와 삭제갯수가 같으면 정상처리
				if($ret && count($ret) == count($data_tmp)){
					echo "complete";
					exit;
				}

			}
		}
	}

	exit;
}



//챌린지 인증메시지, 날짜검색
if($mode == "challenges_masage_del"){

	$idx = $_POST['idx'];
	$chll_idx = preg_replace("/[^0-9]/", "", $idx);

	/*
	[mode] => challenges_masage_del
    [user_email] => all
    [chll_idx] => 337
    [data_idx] => 42,31,26
	*/

	if($user_id=='sadary0@nate.com'){
	//	$user_id = "audtjs2282@nate.com";
	}

	if($chll_idx){
		$masage_idx = $_POST['masage_idx'];
		$sql = "select idx, coin, email, attend_type from work_challenges where state='0' and companyno='".$companyno."' and idx='".$chll_idx."'";
		$res_info = selectQuery($sql);
		if($res_info['idx']){

			$dcoin = $res_info['coin'];
			$coin_info_text = "무효 후 코인 회수";


			if($masage_idx){
				//챌린지 등록한 사람이면 삭제 처리

				if($res_info['email'] == $user_id){
					$ret = "";
					for($i=0; $i<count($masage_idx); $i++){
						if($masage_idx[$i]){

							$sql = "select idx, state, email from work_challenges_result where comment!='' and challenges_idx='".$chll_idx."' and companyno='".$companyno."' and idx='".$masage_idx[$i]."'";
							$comment_info = selectQuery($sql);

							if($comment_info['state']=='1'){
								$sql = "select idx, coin from work_coininfo where state='0' and code='".$ch_code[0]."' and companyno='".$companyno."' and email='".$comment_info['email']."' and work_idx='".$chll_idx."' and reward_type='".$reward_type[2]."' and auth_comment_idx='".$comment_info['idx']."'";
								$coin_info = selectQuery($sql);
								if($coin_info['idx']){

									//회원코인정보 불러오기
									$mem_coin = email_coin($comment_info['email']);
									if($mem_coin >= $dcoin){

										//코인회수 내역저장
										$sql = "insert into work_coininfo(state, code, work_idx, reward_type, auth_comment_idx, companyno, email, name, reward_user, reward_name, coin, memo, workdate, ip)";
										$sql = $sql .=" values('0', '".$ch_code[1]."', '".$chll_idx."','".$reward_type[2]."', '".$comment_info['idx']."', '".$companyno."', '".$comment_info['email']."', '".$comment_info['name']."', '".$user_id."', '".$user_name."', '".$dcoin."', '".$coin_info_text."','".TODATE."','".LIP."')";
										$coin_res = insertQuery($sql);

										//챌린지 인증메시지 삭제처리
										$sql = "update work_challenges_result set state='9', comment_editdate=".DBDATE." where state='1' and challenges_idx='".$chll_idx."' and companyno='".$companyno."' and idx='".$masage_idx[$i]."'";
										$comment_up = updateQuery($sql);

										if($coin_res && $comment_up){
											//회원코인차감
											$sql = "update work_member set coin = coin - '".$dcoin."' where state='0' and companyno='".$companyno."' and email='".$comment_info['email']."'";
											$mem_up = updateQuery($sql);
											if($mem_up){
												$ret[] = true;
											}
										}

									}else{
										echo "coin_minus";
										exit;
									}
								}
							//코인 회수 처리된 글인면 삭제처리
							}else if($comment_info['state']=='2'){

								//챌린지 인증메시지 삭제처리
								$sql = "update work_challenges_result set state='9', comment_editdate=".DBDATE." where challenges_idx='".$chll_idx."' and companyno='".$companyno."' and idx='".$masage_idx[$i]."'";
								$comment_up = updateQuery($sql);
								if($comment_up){
									$ret[] = true;
								}
							}
						}
					}

					//선택한 갯수와 삭제갯수가 같으면 정상처리
					if($ret && count($ret) == count($masage_idx)){
						echo "complete";
						exit;
					}
				}else{

					//메시지를 작성한사람이 삭제
					$ret = "";
					for($i=0; $i<count($masage_idx); $i++){
						if($masage_idx[$i]){

							$sql = "select idx, state, email, name from work_challenges_result where state in ('1','2') and challenges_idx='".$chll_idx."' and idx='".$masage_idx[$i]."' and companyno='".$companyno."' and email='".$user_id."'";
							$comment_info = selectQuery($sql);

							if($comment_info['idx']){


								//회수된 인증메시지
								if($comment_info['state'] == '2'){

									$sql = "select idx, coin from work_coininfo where state='0' and code='".$ch_code[1]."' and email='".$comment_info['email']."'and companyno='".$companyno."' and work_idx='".$chll_idx."' and reward_type='".$reward_type[2]."' and auth_comment_idx='".$comment_info['idx']."' limit 1";
									$coin_info = selectQuery($sql);

									if($coin_info['idx']){

										//챌린지 인증메시지 삭제처리
										$sql = "update work_challenges_result set state='9', comment_editdate=".DBDATE." where state in('1','2') and companyno='".$companyno."' and challenges_idx='".$chll_idx."' and idx='".$masage_idx[$i]."'";
										$comment_up = updateQuery($sql);

										if($comment_up){
											echo "complete_del";
											exit;
										}
									}

									exit;
								}else{

									$sql = "select idx, coin from work_coininfo where state='0' and companyno='".$companyno."' and code='".$ch_code[0]."' and email='".$comment_info['email']."'and work_idx='".$chll_idx."' and reward_type='".$reward_type[2]."' and auth_comment_idx='".$comment_info['idx']."' limit 1";
									$coin_info = selectQuery($sql);


									if($coin_info['idx']){

										//회원코인정보 불러오기
										$mem_coin = email_coin($comment_info['email']);
										if($mem_coin >= $dcoin){

											//코인회수 내역저장
											$sql = "insert into work_coininfo(state, code, work_idx, reward_type, auth_comment_idx, companyno, email, name, reward_user, reward_name, coin, memo, workdate, ip)";
											$sql = $sql .=" values('0', '".$ch_code[1]."', '".$chll_idx."','".$reward_type[2]."','".$comment_info['idx']."', '".$companyno."', '".$comment_info['email']."', '".$comment_info['name']."', '".$user_id."', '".$user_name."', '".$dcoin."', '".$coin_info_text."','".TODATE."','".LIP."')";
											$coin_res = insertQuery($sql);


											//챌린지 인증메시지 삭제처리
											$sql = "update work_challenges_result set state='9', comment_editdate=".DBDATE." where state in('1','2') and companyno='".$companyno."' and challenges_idx='".$chll_idx."' and idx='".$masage_idx[$i]."'";
											$comment_up = updateQuery($sql);


											if($coin_res && $comment_up){
												//회원코인차감
												$sql = "update work_member set coin = coin - '".$dcoin."' where state='0' and companyno='".$companyno."' and email='".$comment_info['email']."'";
												$mem_up = updateQuery($sql);
												if($mem_up){
													$ret[] = true;
												}
											}
										}else{
											echo "coin_minus";
											exit;
										}
									}else{
										echo "coininfo_not";
										exit;
									}
								}

							}else{

								echo "masage_not";
								exit;
							}
						}
					}

					if($ret){
						echo "complete";
						exit;
					}
				}
			}
		}
	}

	exit;
}



//챌린지 인증파일 삭제
if($mode == "challenges_mix_del"){

	$chll_idx = $_POST['chll_idx'];
	$chll_idx = preg_replace("/[^0-9]/", "", $chll_idx);

	/*
	print "</pre>";
	print_r($_POST);
	print "<pre>";
	*/

	if($chll_idx){
		$data_idx = trim($_POST['data_idx']);
		$sql = "select idx, coin, email, attend_type from work_challenges where state='0' and companyno='".$companyno."' and idx='".$chll_idx."'";
		$res_info = selectQuery($sql);
		if($res_info['idx']){

			$dcoin = $res_info['coin'];
			$coin_info_text = "챌린지 참여 삭제";

			$data_idx = trim($_POST['data_idx']);
			$data_tmp = @explode(",", $data_idx);
			$im_data = @implode("','",$data_tmp);

			if($data_idx){
				$ret = "";
				for($i=0; $i<count($data_tmp); $i++){
					if($data_tmp[$i]){
						$sql = "select idx, name, file_path, file_name, file_ori_path, file_ori_name from work_challenges_result where state in('1','2') and companyno='".$companyno."' and comment!='' and file_path!='' and file_name!='' and challenges_idx='".$chll_idx."' and idx='".$data_tmp[$i]."' and email='".$user_id."'";
						$file_info = selectQuery($sql);

						//본인파일 삭제
						if($file_info['idx']){

							$file_path = $file_info['file_path'];
							$file_name = $file_info['file_name'];

							$file_ori_path = $file_info['file_ori_path'];
							$file_ori_name = $file_info['file_ori_name'];

							$sql = "select idx, coin from work_coininfo where state='0' and companyno='".$companyno."' and code='".$ch_code[0]."' and email='".$user_id."' and work_idx='".$chll_idx."' and reward_type='".$reward_type[2]."'";
							$coin_info = selectQuery($sql);
							if($coin_info['idx']){

								//회원코인정보 불러오기
								$mem_coin = email_coin($user_id);
								if($mem_coin >= $dcoin){

									//파일삭제
									if($file_path && $file_name){
										unlink($dir_file_path.$file_path.$file_name);
									}

									//파일삭제
									if($file_ori_path && $file_ori_name){
										unlink($dir_file_path.$file_ori_path.$file_ori_name);
									}

									//코인회수 내역저장
									$sql = "insert into work_coininfo(state, code, work_idx, reward_type, auth_file_idx, companyno, email, name, reward_user, reward_name, coin, memo, workdate, ip)";
									$sql = $sql .=" values('0', '".$ch_code[1]."', '".$chll_idx."','".$reward_type[2]."', '".$file_info['idx']."', '".$companyno."', '".$user_id."', '".$file_info['name']."', '".$user_id."', '".$user_name."', '".$dcoin."', '".$coin_info_text."','".TODATE."','".LIP."')";
									$coin_res = insertQuery($sql);

									//참여내역삭제
									$sql = "update work_challenges_result set state='9', comment=null, file_path=null, file_name=null, file_size='0', file_ori_path=null, file_ori_name=null, file_ori_size='0', file_real_img_name=null, file_real_name=null, editdate=".DBDATE." where challenges_idx='".$chll_idx."' and companyno='".$companyno."' and idx='".$data_tmp[$i]."'";
									$file_up = updateQuery($sql);


									if($coin_res && $file_up){
										//회원코인차감
										$sql = "update work_member set coin = coin - '".$dcoin."' where state='0' and companyno='".$companyno."' and email='".$user_id."'";
										$mem_up = updateQuery($sql);
										if($mem_up){
											$ret[] = true;
										}
									}

								}else{
									echo "coin_minus";
									exit;
								}
							}
						}
					}
				}

				//선택한 갯수와 삭제갯수가 같으면 정상처리
				if($ret && count($ret) == count($data_tmp)){
					echo "complete";
					exit;
				}

			}
		}
	}
	exit;
}



//무효 후 코인 회수 처리
if($mode == "challenges_dcoin"){

	$idx = $_POST['idx'];
	$idx = preg_replace("/[^0-9]/", "", $idx);
	$select_idx = $_POST['select_idx'];

	if($idx){
		$sql = "select idx, coin, attend_type from work_challenges where state='0' and companyno='".$companyno."' and idx='".$idx."'";
		$challenges_info = selectQuery($sql);
		if($challenges_info['idx']){

			$chll_idx = $challenges_info['idx'];
			$dcoin = $challenges_info['coin'];
			$coin_info_text = "무효 후 코인 회수";


			if($challenges_info['attend_type']=='1'){
				//인증메시지 사용자
				$ret = "";
				for($i=0; $i<count($select_idx); $i++){
					if($select_idx[$i]){
						$sql = "select idx, email, name from work_challenges_result where state='1' and companyno='".$companyno."' and challenges_idx='".$idx."' and idx='".$select_idx[$i]."'";
						$comment_info = selectQuery($sql);

						//코인지급된 내역만 처리
						if($comment_info['idx']){

							$sql = "select idx, coin from work_coininfo where state='0' and companyno='".$companyno."' and code='".$ch_code[0]."' and email='".$comment_info['email']."'and work_idx='".$chll_idx."'";
							$coin_info = selectQuery($sql);
							if($coin_info['idx']){

								//회원코인정보 불러오기
								$mem_coin = email_coin($comment_info['email']);


								write_log("코인" .$comment_info['email'] . " , " . $mem_coin );

								if($mem_coin >= $dcoin){

									//코인회수 내역저장
									$sql = "insert into work_coininfo(state, code, work_idx, reward_type, auth_comment_idx, companyno, email, name, reward_user, reward_name, coin, memo, workdate, ip)";
									$sql = $sql .=" values('0', '".$ch_code[1]."', '".$chll_idx."', '".$reward_type[2]."', '".$comment_info['idx']."', '".$companyno."', '".$comment_info['email']."', '".$comment_info['name']."', '".$user_id."', '".$user_name."', '".$dcoin."', '".$coin_info_text."','".TODATE."','".LIP."')";
									$coin_res = insertQuery($sql);

									$sql = "update work_challenges_result set state='2' where state='1' and challenges_idx='".$idx."' and companyno='".$companyno."' and idx='".$select_idx[$i]."'";
									$res_info1 = updateQuery($sql);

									if($coin_res && $res_info1){
										//회원코인차감
										$sql = "update work_member set coin = coin - '".$dcoin."' where state='0' and companyno='".$companyno."' and email='".$comment_info['email']."'";
										$res_info2 = updateQuery($sql);
										if($coin_res && $res_info1 && $res_info2){
											$ret = true;
										}
									}

								}else{
									echo "coin_minus";
									exit;
								}
							}else{

								echo "coin_info_not";
								exit;
							}
						}
					}
				}

				if($ret == true ){
					echo "complete";
					exit;
				}

			}else if($challenges_info['attend_type']=='2'){
				//인증파일 사용자
				$ret = "";
				for($i=0; $i<count($select_idx); $i++){
					if($select_idx[$i]){
						$sql = "select idx, email, name from work_challenges_result where state='1' and companyno='".$companyno."' and challenges_idx='".$idx."' and idx='".$select_idx[$i]."'";
						$file_info = selectQuery($sql);

						if($file_info['idx']){

							$sql = "select idx, coin from work_coininfo where state='0' and companyno='".$companyno."' and code='".$ch_code[0]."' and email='".$file_info['email']."'and work_idx='".$chll_idx."' and auth_file_idx='".$select_idx[$i]."'";
							$coin_info = selectQuery($sql);
							if($coin_info['idx']){

								//회원코인정보 불러오기
								$mem_coin = email_coin($file_info['email']);
								if($mem_coin >= $dcoin){

									//코인회수 내역저장
									$sql = "insert into work_coininfo(state, code, work_idx, reward_type, auth_file_idx, companyno, email, name, reward_user, reward_name, coin, memo, workdate, ip)";
									$sql = $sql .=" values('0', '".$ch_code[1]."', '".$chll_idx."', '".$reward_type[2]."', '".$file_info['idx']."', '".$companyno."', '".$file_info['email']."', '".$file_info['name']."', '".$user_id."', '".$user_name."', '".$dcoin."', '".$coin_info_text."','".TODATE."','".LIP."')";
									$coin_res = insertQuery($sql);

									$sql = "update work_challenges_result set state='2', editdate=".DBDATE." where state='1' and companyno='".$companyno."' and challenges_idx='".$idx."' and idx='".$select_idx[$i]."'";
									$res_info1 = updateQuery($sql);
									if($coin_res && $res_info1){
										//회원코인차감
										$sql = "update work_member set coin = coin - '".$dcoin."' where state='0' and companyno='".$companyno."' and email='".$file_info['email']."'";
										$res_info2 = updateQuery($sql);
										if($coin_res && $res_info1 && $res_info2){
											$ret = true;
										}
									}

								}else{
									echo "coin_minus";
									exit;
								}
							}else{

								echo "coin_info_not";
								exit;
							}
						}
					}
				}

				if($ret == true ){
					echo "complete";
					exit;
				}

			}else if($challenges_info['attend_type']=='3'){

				//인증파일 사용자
				$ret = "";
				for($i=0; $i<count($select_idx); $i++){
					if($select_idx[$i]){
						$sql = "select idx, email, name from work_challenges_result where state='1' and companyno='".$companyno."' and challenges_idx='".$idx."' and idx='".$select_idx[$i]."'";
						$file_info = selectQuery($sql);

						if($file_info['idx']){

							$sql = "select idx, coin from work_coininfo where state='0' and companyno='".$companyno."' and code='".$ch_code[0]."' and email='".$file_info['email']."'and work_idx='".$chll_idx."' and auth_file_idx='".$select_idx[$i]."'";
							$coin_info = selectQuery($sql);
							if($coin_info['idx']){

								//회원코인정보 불러오기
								$mem_coin = email_coin($file_info['email']);
								if($mem_coin >= $dcoin){

									//코인회수 내역저장
									$sql = "insert into work_coininfo(state, code, work_idx, reward_type, auth_file_idx, companyno, email, name, reward_user, reward_name, coin, memo, workdate, ip)";
									$sql = $sql .=" values('0', '".$ch_code[1]."', '".$chll_idx."', '".$reward_type[2]."', '".$file_info['idx']."', '".$companyno."', '".$file_info['email']."', '".$file_info['name']."', '".$user_id."', '".$user_name."', '".$dcoin."', '".$coin_info_text."','".TODATE."','".LIP."')";
									$coin_res = insertQuery($sql);

									$sql = "update work_challenges_result set state='2', editdate=".DBDATE." where state='1' and companyno='".$companyno."' and challenges_idx='".$idx."' and idx='".$select_idx[$i]."'";
									$res_info1 = updateQuery($sql);
									if($coin_res && $res_info1){
										//회원코인차감
										$sql = "update work_member set coin = coin - '".$dcoin."' where state='0' and companyno='".$companyno."' and email='".$file_info['email']."'";
										$res_info2 = updateQuery($sql);
										if($coin_res && $res_info1 && $res_info2){
											$ret = true;
										}
									}

								}else{
									echo "coin_minus";
									exit;
								}
							}else{

								echo "coin_info_not";
								exit;
							}
						}
					}
				}
			}


			//인증메시지 갯수 = 업데이트처리갯수 동일하면 정상처리
			if($ret && count($select_idx) == count($ret) ){
				echo "complete";
				exit;
			}
		}
	}

	exit;
}


if($mode == "challenges_rcoin"){

	$idx = $_POST['idx'];
	$idx = preg_replace("/[^0-9]/", "", $idx);

	$select_idx = $_POST['select_idx'];
	if($idx){

		$sql = "select idx, coin, attend_type from work_challenges where state='0' and companyno='".$companyno."' and idx='".$idx."'";
		$challenges_info = selectQuery($sql);
		if($challenges_info['idx']){

			$chll_idx = $challenges_info['idx'];
			$dcoin = $challenges_info['coin'];
			$coin_info_text = "코인 다시 지급";

			if($challenges_info['attend_type']=='1'){

				//인증메시지 사용자 내역체크
				$ret = "";
				for($i=0; $i<count($select_idx); $i++){
					if($select_idx[$i]){

						//무효 후 코인 회수 내역
						$sql = "select idx, email, name from work_challenges_result where state='2' and companyno='".$companyno."' and challenges_idx='".$idx."' and idx='".$select_idx[$i]."'";
						$comment_info = selectQuery($sql);

						if($comment_info['idx']){

							$sql = "select idx, coin from work_coininfo where state='0' and companyno='".$companyno."' and code='".$ch_code[1]."' and email='".$comment_info['email']."'and work_idx='".$chll_idx."'";
							$coin_info = selectQuery($sql);
							if($coin_info['idx']){

								//회원코인정보 불러오기
								//$mem_coin = email_coin($comment_info['email']);


								//코인지급 내역저장
								$sql = "insert into work_coininfo(state, code, work_idx, reward_type, auth_comment_idx, companyno, email, name, reward_user, reward_name, coin, memo, workdate, ip)";
								$sql = $sql .=" values('0', '".$ch_code[0]."', '".$chll_idx."', '".$reward_type[2]."', '".$comment_info['idx']."', '".$companyno."', '".$comment_info['email']."', '".$comment_info['name']."', '".$user_id."', '".$user_name."', '".$dcoin."', '".$coin_info_text."','".TODATE."','".LIP."')";
								$coin_res = insertQuery($sql);

								$sql = "update work_challenges_result set state='1' where state='2' and companyno='".$companyno."' and challenges_idx='".$idx."' and idx='".$select_idx[$i]."'";
								$res_info1 = updateQuery($sql);

								if($coin_res && $res_info1){
									//회원코인차감
									$sql = "update work_member set coin = coin + '".$dcoin."' where state='0' and companyno='".$companyno."' and email='".$comment_info['email']."'";
									$res_info2 = updateQuery($sql);
									if($coin_res && $res_info1 && $res_info2){
										$ret[] = true;
									}
								}

							}else{

								echo "coin_info_not";
								exit;
							}
						}else{

							echo "not";
							exit;
						}
					}
				}


			}else if($challenges_info['attend_type']=='2'){

				//인증메시지 사용자 내역체크
				$ret = "";
				for($i=0; $i<count($select_idx); $i++){
					if($select_idx[$i]){

						//무효 후 코인 회수 내역
						$sql = "select idx, email, name from work_challenges_result where state='2' and companyno='".$companyno."' and challenges_idx='".$idx."' and idx='".$select_idx[$i]."'";
						$file_info = selectQuery($sql);

						if($file_info['idx']){

							$sql = "select idx, coin from work_coininfo where state='0' and companyno='".$companyno."' and code='".$ch_code[1]."' and email='".$file_info['email']."'and work_idx='".$chll_idx."' and auth_file_idx='".$select_idx[$i]."'";
							$coin_info = selectQuery($sql);
							if($coin_info['idx']){

								//회원코인정보 불러오기
								//$mem_coin = email_coin($comment_info['email']);


								//코인지급 내역저장
								$sql = "insert into work_coininfo(state, code, work_idx, reward_type, auth_file_idx, companyno, email, name, reward_user, reward_name, coin, memo, workdate, ip)";
								$sql = $sql .=" values('0', '".$ch_code[1]."', '".$chll_idx."', '".$reward_type[2]."', '".$file_info['idx']."','".$companyno."','".$file_info['email']."', '".$file_info['name']."', '".$user_id."', '".$user_name."', '".$dcoin."', '".$coin_info_text."','".TODATE."','".LIP."')";
								$coin_res = insertQuery($sql);

								$sql = "update work_challenges_result set state='1' where state='2' and companyno='".$companyno."' and challenges_idx='".$idx."' and idx='".$select_idx[$i]."'";
								$res_info1 = updateQuery($sql);

								if($coin_res && $res_info1){
									//회원코인차감
									$sql = "update work_member set coin = coin + '".$dcoin."' where state='0' and companyno='".$companyno."' and email='".$file_info['email']."'";
									$res_info2 = updateQuery($sql);
									if($coin_res && $res_info1 && $res_info2){
										$ret[] = true;
									}
								}

							}else{

								echo "coin_info_not";
								exit;
							}
						}else{

							echo "not";
							exit;
						}
					}
				}


			}else if($challenges_info['attend_type']=='3'){

				//혼합형 사용자 내역체크
				$ret = "";
				for($i=0; $i<count($select_idx); $i++){
					if($select_idx[$i]){

						//무효 후 코인 회수 내역
						$sql = "select idx, email, name from work_challenges_result where state='2' and companyno='".$companyno."' and challenges_idx='".$idx."' and idx='".$select_idx[$i]."'";
						$file_info = selectQuery($sql);

						if($file_info['idx']){

							$sql = "select idx, coin from work_coininfo where state='0' and companyno='".$companyno."' and code='".$ch_code[1]."' and email='".$file_info['email']."'and work_idx='".$chll_idx."' and auth_file_idx='".$select_idx[$i]."'";
							$coin_info = selectQuery($sql);
							if($coin_info['idx']){

								//코인지급 내역저장
								$sql = "insert into work_coininfo(state, code, work_idx, reward_type, auth_file_idx, companyno, email, name, reward_user, reward_name, coin, memo, workdate, ip)";
								$sql = $sql .=" values('0', '".$ch_code[1]."', '".$chll_idx."', '".$reward_type[2]."', '".$file_info['idx']."','".$companyno."', '".$file_info['email']."', '".$file_info['name']."', '".$user_id."', '".$user_name."', '".$dcoin."', '".$coin_info_text."','".TODATE."','".LIP."')";
								$coin_res = insertQuery($sql);

								$sql = "update work_challenges_result set state='1' where state='2' and companyno='".$companyno."' and challenges_idx='".$idx."' and idx='".$select_idx[$i]."'";
								$res_info1 = updateQuery($sql);

								if($coin_res && $res_info1){
									//회원코인차감
									$sql = "update work_member set coin = coin + '".$dcoin."' where state='0' and companyno='".$companyno."' and email='".$file_info['email']."'";
									$res_info2 = updateQuery($sql);
									if($coin_res && $res_info1 && $res_info2){
										$ret[] = true;
									}
								}

							}else{

								echo "coin_info_not";
								exit;
							}
						}else{

							echo "not";
							exit;
						}
					}
				}

			}

			//인증메시지 갯수 = 업데이트처리갯수 동일하면 정상처리
			if($ret && count($select_idx) == count($ret) ){
				echo "complete";
				exit;
			}
		}
	}



	exit;
}


//챌린지 참여가능 여부 체크
if($mode == "challenges_check"){

	/*print "<pre>";
	print_r($_POST);
	print "</pre>";*/

	$chall_idx = $_POST['idx'];
	$chall_idx = preg_replace("/[^0-9]/", "", $chall_idx);
	if($chall_idx){
		$sql = "select idx, email, name, coin, title, sdate, edate, attend_type, attend, day_type, holiday_chk, outputchk from work_challenges where state='0' and companyno='".$companyno."' and idx='".$chall_idx."' order by idx desc limit 1";
		$challenges_info = selectQuery($sql);
		if($challenges_info['idx']){


			$attend_type = $challenges_info['attend_type'];
			$attend = $challenges_info['attend'];
			$day_type = $challenges_info['day_type'];

			//챌린지 참여 기간 체크
			if( (TODATE >= $challenges_info['sdate']) && (TODATE <= $challenges_info['edate']) ){

			}else{
				//참여종료됨
				echo "exday";
				exit;
			}

			//공휴일 제외체크
			if ($challenges_info['holiday_chk'] == "1"){
				if(@in_array($weekday_num, array('0','6'))){
					echo "holiday";
					exit;
				}
			}


			//챌린지 타입, 1:메시지형, 2:파일첨부형, 3:혼합형(메시지형 + 파일첨부형)
			if($attend_type=='1'){


				//참여 기간내(0:한번, 1:매일)
				if($day_type =='0'){

					$sql = "select idx from work_challenges_result where state='1' and companyno='".$companyno."' and challenges_idx='".$challenges_info['idx']."' and email='".$user_id."' ";
					$sql = $sql .= " and DATE_FORMAT(file_regdate, '%Y-%m-%d') >= '".$challenges_info['sdate']."' and DATE_FORMAT(file_regdate, '%Y-%m-%d') <= '".$challenges_info['edate']."'";
					$ch_comment_info = selectQuery($sql);
					if ($ch_comment_info['idx']){
						echo "chamyeo";
						exit;
					}

				}else if($day_type =='1'){

					//챌린지 참여 전체횟수
					$sql = "select idx from work_challenges_result where state='1' and companyno='".$companyno."' and challenges_idx='".$challenges_info['idx']."' and email='".$user_id."' ";
					$ch_tot_info = selectQuery($sql);

					//챌린지 참여 오늘참여횟수
					$sql = "select idx from work_challenges_result where state='1' and companyno='".$companyno."' and challenges_idx='".$challenges_info['idx']."' and email='".$user_id."' ";
					$sql = $sql .= " and DATE_FORMAT(comment_regdate, '%Y-%m-%d')='".TODATE."'";
					$ch_comment_info = selectQuery($sql);

					if ($ch_comment_info['idx'] || count($ch_tot_info['idx']) >= $attend ){
						echo "chamyeo";
						exit;
					}
				}

			//파일첨부형
			}else if($attend_type=='2'){


				if($day_type =='0'){

					$sql = "select idx from work_challenges_result where state='1' and companyno='".$companyno."' and challenges_idx='".$challenges_info['idx']."' and email='".$user_id."' ";
					$sql = $sql .= " and DATE_FORMAT(file_regdate, '%Y-%m-%d') >= '".$challenges_info['sdate']."' and DATE_FORMAT(file_regdate, '%Y-%m-%d') <= '".$challenges_info['edate']."'";
					$ch_file_info = selectQuery($sql);
					if ($ch_file_info['idx']){
						echo "chamyeo";
						exit;
					}

				}else if($day_type =='1'){

					//챌린지 참여 전체횟수
					$sql = "select idx from work_challenges_result where state='1' and companyno='".$companyno."' and challenges_idx='".$challenges_info['idx']."' and email='".$user_id."'";
					$ch_tot_info = selectQuery($sql);

					//챌린지 참여 오늘참여횟수
					$sql = "select idx from work_challenges_result where state='1' and companyno='".$companyno."' and challenges_idx='".$challenges_info['idx']."' and email='".$user_id."'";
					$sql = $sql .= " and DATE_FORMAT(file_regdate, '%Y-%m-%d')='".TODATE."'";
					$ch_file_info = selectQuery($sql);

					if ($ch_file_info['idx'] || count($ch_tot_info['idx']) >= $attend ){
						echo "chamyeo";
						exit;
					}
				}

			//혼합형 인증메시지, 파일첨부형
			}else if($attend_type=='3'){

				//참여 기간내(0:한번, 1:매일)
				if($day_type =='0'){

					$sql = "select idx from work_challenges_result where state='1' and companyno='".$companyno."' and challenges_idx='".$challenges_info['idx']."' and email='".$user_id."' ";
					$sql = $sql .= " and DATE_FORMAT(file_regdate, '%Y-%m-%d') >= '".$challenges_info['sdate']."' and DATE_FORMAT(file_regdate, '%Y-%m-%d') <= '".$challenges_info['edate']."'";
					$ch_comment_info = selectQuery($sql);

					$sql = "select idx from work_challenges_result where state='1' and companyno='".$companyno."' and challenges_idx='".$challenges_info['idx']."' and email='".$user_id."' ";
					$sql = $sql .= " and DATE_FORMAT(file_regdate, '%Y-%m-%d') >= '".$challenges_info['sdate']."' and DATE_FORMAT(file_regdate, '%Y-%m-%d') <= '".$challenges_info['edate']."'";
					$ch_file_info = selectQuery($sql);

					if ($ch_comment_info['idx'] && $ch_file_info['idx']){
						echo "chamyeo";
						exit;
					}

				}else if($day_type =='1'){

					//챌린지 참여 인증 메시지 전체횟수
					$sql = "select idx from work_challenges_result where state='1' and companyno='".$companyno."' and challenges_idx='".$challenges_info['idx']."' and email='".$user_id."' ";
					$ch_comment_tot_info = selectQuery($sql);

					//챌린지 참여 인증 메시지 오늘참여횟수
					$sql = "select idx from work_challenges_result where state='1' and companyno='".$companyno."' and challenges_idx='".$challenges_info['idx']."' and email='".$user_id."' ";
					$sql = $sql .= " and DATE_FORMAT(comment_regdate, '%Y-%m-%d')='".TODATE."'";
					$ch_comment_info = selectQuery($sql);


					//챌린지 참여 파일 인증 전체횟수
					$sql = "select idx from work_challenges_result where state='1' and companyno='".$companyno."' and challenges_idx='".$challenges_info['idx']."' and email='".$user_id."'";
					$ch_file_tot_info = selectQuery($sql);

					//챌린지 참여 파일 인증 오늘참여횟수
					$sql = "select idx from work_challenges_result where state='1' and companyno='".$companyno."' and challenges_idx='".$challenges_info['idx']."' and email='".$user_id."'";
					$sql = $sql .= " and DATE_FORMAT(file_regdate, '%Y-%m-%d')='".TODATE."'";
					$ch_file_info = selectQuery($sql);


					if ($ch_comment_info['idx'] || count($ch_comment_tot_info['idx']) >= $attend ){
						echo "chamyeo";
						exit;
					}

					if ($ch_file_info['idx'] || count($ch_file_tot_info['idx']) >= $attend ){
						echo "chamyeo";
						exit;
					}
				}
			}

		}
	}

	exit;
}


//챌린지 선택 상태체크
if($mode == "challenges_state_check"){

	if($user_id=='sadary0@nate.com'){
	//	$user_id = "audtjs2282@nate.com";
	}
	$data_idx = trim($_POST['data_idx']);
	$chall_idx = $_POST['chll_idx'];
	$chall_idx = preg_replace("/[^0-9]/", "", $chall_idx);
	$data_tmp = @explode(",", $data_idx);
	$im_data = @implode("','",$data_tmp);

	if($chall_idx && $data_idx){
		$sql = "select idx, attend_type, email from work_challenges where state='0' and companyno='".$companyno."' and idx='".$chall_idx."'";
		$res_info = selectQuery($sql);
		if($res_info['idx']){

			//인증메시지
			if($res_info['attend_type']=='1'){

				$sql = "select idx, state, email from work_challenges_result where state in ('1','2') and companyno='".$companyno."' and challenges_idx='".$chall_idx."' and idx in('".$im_data."')";
				$info = selectAllQuery($sql);

				for($i=0; $i<count($info['idx']); $i++){

					//인증 메시지 상태값
					$ret_state = $info['state'][$i];

					//챌린지 작성자와 로그인 사용자가 동일하면
					if($res_info['email'] == $user_id){
						$ret_dcoin[] = $ret_state;
						$ret_del = "y";
					}else{
						//인증 메시지 작성자와 로그인 사용자가 동일하면
						if($info['email'][$i] == $user_id){
							if(count($info['idx']) == count($data_tmp)){
								$ret_del = "y";
							}else{
								$ret_del = "n";

							}
						}else{
							$ret_del = "n";
							break;
						}
					}
				}

				$dcoin = "";
				if( @in_array("1", $ret_dcoin)){
					$dcoin = "dcoin";
				}else{
					$dcoin = "rcoin";
				}

				//챌린지타입|삭제여부|코인회수
				echo $res_info['attend_type']."|".$ret_del."|".$dcoin;
				exit;
			}else if($res_info['attend_type']=='2'){

				$ret_dcoin = array();
				$sql = "select idx, state, email from work_challenges_result where state in ('1','2') and companyno='".$companyno."' and challenges_idx='".$chall_idx."' and idx in('".$im_data."')";
				$info = selectAllQuery($sql);

				for($i=0; $i<count($info['idx']); $i++){

					//인증 메시지 상태값
					$ret_state = $info['state'][$i];

					//챌린지 작성자와 로그인 사용자가 동일하면
					if($res_info['email'] == $user_id){
						$ret_dcoin[] = $ret_state;
					}

					//인증 메시지 작성자와 로그인 사용자가 동일하면
					if($info['email'][$i] == $user_id){
						if(count($info['idx']) == count($data_tmp)){
							$ret_del = "y";
						}else{
							$ret_del = "n";
						}
					}else{
						$ret_del = "n";
						break;
					}
				}

				$dcoin = "";
				if( @in_array("1", $ret_dcoin)){
					$dcoin = "dcoin";
				}else{
					$dcoin = "rcoin";
				}

				//챌린지타입|삭제여부|코인회수
				echo $res_info['attend_type']."|".$ret_del."|".$dcoin;
				exit;
			}else if($res_info['attend_type']=='3'){

				$ret_dcoin = array();
				$sql = "select idx, state, email from work_challenges_result where state in ('1','2') and companyno='".$companyno."' and challenges_idx='".$chall_idx."' and comment!='' and file_path!='' and file_name!='' and idx in('".$im_data."')";
				$mix_info = selectAllQuery($sql);

				if($mix_info['idx']){
					for($i=0; $i<count($mix_info['idx']); $i++){

						//인증 메시지 상태값
						$ret_state = $mix_info['state'][$i];
						//챌린지 작성자와 로그인 사용자가 동일하면
						if($res_info['email'] == $user_id){

							$ret_dcoin[] = $ret_state;
						}
						//인증 메시지 작성자와 로그인 사용자가 동일하면
						if($mix_info['email'][$i] == $user_id){
							if(count($mix_info['idx']) == count($data_tmp)){
								$ret_del = "y";
							}else{
								$ret_del = "n";
							}
						}else{
							$ret_del = "n";
							break;
						}
					}

					$dcoin = "";
					if( @in_array("1", $ret_dcoin)){
						$dcoin = "dcoin";
					}else{
						$dcoin = "rcoin";
					}


				}/*else if($file_info['idx']){
					for($i=0; $i<count($file_info['idx']); $i++){
						//인증 파일 상태값
						$ret_state = $file_info['state'][$i];

						//챌린지 작성자와 로그인 사용자가 동일하면
						if($res_info['email'] == $user_id){
							$ret_dcoin[] = $ret_state;
						}
						//인증 파일 작성자와 로그인 사용자가 동일하면
						if($file_info['email'][$i] == $user_id){
							if(count($file_info['idx']) == count($data_tmp)){
								$ret_del = "y";
							}else{
								$ret_del = "n";
							}
						}else{
							$ret_del = "n";
							break;
						}
					}

					$dcoin = "";
					if( @in_array("1", $ret_dcoin)){
						$dcoin = "dcoin";
					}else{
						$dcoin = "rcoin";
					}

				}*/

				//챌린지타입|삭제여부|코인회수
				echo $res_info['attend_type']."|".$ret_del."|".$dcoin;
				exit;


			}
		}
	}
	exit;
}


if($mode == "auth_file_list_top"){

	$idx = $_POST['idx'];
	$list_idx = $_POST['list_idx'];
	$idx = preg_replace("/[^0-9]/", "", $idx);
	$list_idx = trim($list_idx);
	$input_val = $_POST['input_val'];
	$user_date = $_POST['user_date'];

	$sql = "select idx, state, email, name, part, DATE_FORMAT(file_regdate, '%Y-%m-%d %H:%i:%s') as reg, DATE_FORMAT(file_regdate, '%Y-%m-%d') as ymd, DATE_FORMAT(file_regdate, '%Y-%m-%d %H:%i:%s') as ddd, resize, file_path, file_name, file_real_name, file_ori_path, file_ori_name, file_real_img_name, file_type from work_challenges_result where state in('1','2') and companyno='".$companyno."' and challenges_idx='".$idx."' order by reg desc";
	$chall_file_info = selectAllQuery($sql);
	if($chall_file_info['idx']){
		$chamyeo_file_cnt = count($chall_file_info['idx']);
	}else{
		$chamyeo_file_cnt = "";
	}

	for($i=0; $i<count($chall_file_info['idx']); $i++){

		$cidx = $chall_file_info['idx'][$i];
		$cemail = $chall_file_info['email'][$i];
		$cname = $chall_file_info['name'][$i];
		$cpart = $chall_file_info['part'][$i];
		$creg = $chall_file_info['reg'][$i];
		$cymd = $chall_file_info['ymd'][$i];
		$chis = $chall_file_info['ddd'][$i];
		$resize = $chall_file_info['resize'][$i];

		$state = $chall_file_info['state'][$i];

		if($resize == '0'){
			$file_path = $chall_file_info['file_ori_path'][$i];
			$file_name = $chall_file_info['file_ori_name'][$i];
		}else{
			$file_path = $chall_file_info['file_path'][$i];
			$file_name = $chall_file_info['file_name'][$i];
		}

		$file_type = $chall_file_info['file_type'][$i];
		$file_real_name = $chall_file_info['file_real_name'][$i];
		$file_real_img_name = $chall_file_info['file_real_img_name'][$i];

		$cfiles = $file_path.$file_name;

		//요일구함
		$reg_date = explode("-", $cymd);
		$int_reg = date("w", strtotime($creg));
		$date_yoil =  $weeks[$int_reg];

		$chall_reg = $reg_date[0]."년 ".$reg_date[1]."월 ".$reg_date[2];

		//시간 오전,오후
		if($chis){
			$chis_tmp = explode(" ", $chis);
			if ($chis_tmp['3'] > "AM"){
				$after = "오후 ";
			}else{
				$after = "오전 ";
			}
			$ctime = explode(":", $chis_tmp['2']);
			$chiss = $after . $ctime['0'] .":". $ctime['1'];
		}


		//전체내역
		$chall_file_list[$cymd][$i]['date'] = $cymd;
		$chall_file_list[$cymd][$i]['idx'] = $cidx;
		$chall_file_list[$cymd][$i]['eamil'] = $cemail;
		$chall_file_list[$cymd][$i]['name'] = $cname;
		$chall_file_list[$cymd][$i]['part'] = $cpart;
		$chall_file_list[$cymd][$i]['files'] = $cfiles;
		$chall_file_list[$cymd][$i]['hi'] = $chiss;

		//이미지형, 파일형
		if(@in_array($file_type , $image_type_array)){
			$chall_file_list[$cymd][$i]['file_real_name'] = $file_real_img_name;
		}else{
			$chall_file_list[$cymd][$i]['file_real_name'] = $file_real_name;
		}


		$chall_file_list[$cymd][$i]['file_type'] = $file_type;
		$chall_file_list[$cymd][$i]['yoil'] = $date_yoil;
		$chall_file_list[$cymd][$i]['reg'] = $chall_reg;
		$chall_file_list[$cymd][$i]['state'] = $state;
		$chall_file_list_ymd[]= $cymd;
	}

	//배열키값 중복제거
	$chall_file_list_ymd = array_unique($chall_file_list_ymd);

	//배열키값 리셋
	$chall_file_list_ymd = array_key_reset($chall_file_list_ymd);

	//챌린지 인증메시지, 날짜별
	$sql ="select DATE_FORMAT(file_regdate, '%Y-%m-%d') as ymd from work_challenges_result where state='1' and companyno='".$companyno."' and challenges_idx='".$idx."'";
	$sql = $sql .=" group by DATE_FORMAT(file_regdate, '%Y-%m-%d') order by DATE_FORMAT(file_regdate, '%Y-%m-%d') desc";
	$date_file_info = selectAllQuery($sql);

	?>
		<div class="rew_cha_view_result">
			<div class="rew_cha_view_result_in">
				<div class="title_area">
					<strong class="title_main">인증 파일</strong>
					<span class="title_point"><?=$chamyeo_file_cnt?></span>
					<a href="#" class="title_more"><span>더보기</span></a>
				</div>

				<?if($chall_file_info['idx']){?>

					<ul>
						<?for($i=0; $i<5; $i++){
							$chall_file_idx = $chall_file_info['idx'][$i];
							$chall_file_type = $chall_file_info['file_type'][$i];

							if($chall_file_info['resize'][$i]=='0'){
								$chall_file_path = $chall_file_info['file_ori_path'][$i];
								$chall_file_name = $chall_file_info['file_ori_name'][$i];
							}else{
								$chall_file_path = $chall_file_info['file_path'][$i];
								$chall_file_name = $chall_file_info['file_name'][$i];
							}

							$file_url = $chall_file_path . $chall_file_name;

							if($chall_file_type){
								if (@in_array($chall_file_type, $image_type_array)){?>
									<li>
										<button>
											<img src="<?=$file_url?>" />
											<span>더보기</span>
										</button>
									</li>

								<?}else{?>

									<li>
										<button>
											<img src="/html/images/pre/ico_list_file.png" alt="" />
											<span>더보기</span>
										</button>
									</li>
								<?}
							}

						}?>
					</ul>
				<?}else{?>
					<div class="rew_none">
						등록된 인증 파일이 없습니다.
					</div>
				<?}?>
			</div>
		</div>
<?
	exit;
}



//챌린지 도전하기
if($mode == "view_challenges"){
	$idx = $_POST['idx'];
	
	$idx = preg_replace("/[^0-9]/", "", $idx);
	// echo $idx."|";
	/*print "</pre>";
	print_r($_POST);
	print "<pre>";
	exit;*/
	if($idx){
		//챌린지 정보 확인
		$sql = "select idx, email, name, coin, title, sdate, edate, attend_type, attend, day_type, holiday_chk, outputchk from work_challenges where state='0' and companyno='".$companyno."' and idx='".$idx."'";
		$challenges_info = selectQuery($sql);

	    // echo $challenges_info['idx']."|";
		if($challenges_info['idx']){
			$sql = "select idx from work_challenges_user where challenges_idx = '".$idx."' and state = '0' and email = '".$user_id."' limit 0,1";
			$check_chl = selectQuery($sql);

			if(!$check_chl){
				echo "not_chll";
				exit;
			}

			if( (TODATE < $challenges_info['sdate'])){
				echo "rxday";
				exit;
			}else if((TODATE > $challenges_info['edate'])){
				//참여종료됨
				echo "exday";
				exit;
			}

			//공휴일 제외체크
			if ($challenges_info['holiday_chk'] == "1"){
				if(@in_array($weekday_num, array('0','6'))){
					echo "holiday";
					exit;
				}
			}

			//부서별정보
			if($user_part){
				$sql = "select idx, partname from work_team where state='0' and companyno='".$companyno."' and idx='".$user_part."'";
				$team_info = selectQuery($sql);
				if ($team_info['idx']){
					$partname = $team_info['partname'];
				}
			}


    		if($challenges_info['attend_type']=='3'){
				
				//혼합형 체크
				//한번만
				if($challenges_info['day_type'] == '0'){
					
					$sql = "select idx from work_challenges_result where state='0' and companyno='".$companyno."' and challenges_idx='".$idx."' and email='".$user_id."' and DATE_FORMAT(comment_regdate, '%Y-%m-%d') >= '". $challenges_info['sdate']."' and DATE_FORMAT(comment_regdate, '%Y-%m-%d') <= '". $challenges_info['edate']."'";
					$info_comment_row = selectQuery($sql);
					// if(!$info_comment_row['idx']){
					// 	$sql = "insert into work_challenges_result(state, companyno, challenges_idx, email, name, part, partno, type_flag, ip, comment_regdate)";
					// 	$sql = $sql ." values ('0', '".$companyno."', '".$idx."', '".$user_id."', '".$user_name."', '".$partname."', '".$user_part."', '".$type_flag."', '".LIP."', ".DBDATE.")";
					// 	$result_idx = insertIdxQuery($sql);
					// 	// echo $result_idx."|";
					// }

				//매일체크
				}else if($challenges_info['day_type'] == '1'){
					// echo $challenges_info['day_type'];
					//인증메시지
					$sql = "select idx from work_challenges_result where state='0' and companyno='".$companyno."' and challenges_idx='".$idx."' and email='".$user_id."' and DATE_FORMAT(comment_regdate, '%Y-%m-%d') = '".TODATE."'";
					$info_comment_row = selectQuery($sql);
					// echo "|".$info_comment_row['idx'];
					// if(!$info_comment_row['idx']){
					// 	$sql = "insert into work_challenges_result(state, companyno, challenges_idx, email, name, part, partno, type_flag, ip, comment_regdate)";
					// 	$sql = $sql ." values ('0', '".$companyno."', '".$idx."', '".$user_id."', '".$user_name."', '".$partname."', '".$user_part."', '".$type_flag."', '".LIP."', ".DBDATE.")";
					// 	$result_idx = insertIdxQuery($sql);
					// 	// echo $result_idx."|";
					// }
				}


				//도전등록처리
				// if($result_idx){
				// 	//타임라인(챌린지도전)
				// 	work_data_log('0','13', $result_idx, $user_id, $user_name);

				// 	echo "complete|".$result_idx;
				// 	exit;
				// }else{
					echo "complete|";
					exit;
				// }
			}
		}
	}
	exit;
}

if($mode == "chal_cancel"){
	$chal_idx = $_POST['idx'];

	$sql = "select idx challenges_idx from work_challenges_result where email = '".$user_id."' and companyno = '".$companyno."' and state = '0' order by idx desc";
	$chal_res_idx = selectQuery($sql);

	if($chal_res_idx){
		$ch_idx = $chal_res_idx['challenges_idx'];

		$sql = "update work_challenges_result set state = 9 where idx = '".$ch_idx."'";
		$chal_res_can = updateQuery($sql);

		if($chal_res_can){
			echo "complete";
			exit;
		}
	}
}


//테마리스트
if($mode == "challenges_thema_list"){

	$search = $_POST['search'];
	$where = "where state='0' and companyno='".$companyno."'";
	if($search){
		$where = $where .= " and title like '%".$search."%'";
	}

	$sql = "select idx, title from work_challenges_thema ".$where ." order by idx desc";
	$thema_info = selectAllQuery($sql);

	$thema_info_cnt = number_format(count($thema_info['idx']));
	if(!$thema_info_cnt){
		$thema_info_cnt = 0;
	}

	if($thema_info['idx']){

		for($i=0; $i<count($thema_info['idx']); $i++){

			$thema_idx = $thema_info['idx'][$i];
			$thema_title = $thema_info['title'][$i];

		?>
			<li>
				<div class="tdw_list_box">
					<div class="tdw_list_chk">
						<button class="btn_tdw_list_chk" id="btn_tdw_list_thema_chk" value="<?=$thema_idx?>"><span>완료체크</span></button>
					</div>
					<div class="tdw_list_desc">
						<p id="tdw_list_desc_thema_<?=$thema_idx?>"><?=$thema_title?></p>
						<button class="btn_list_del" id="btn_list_thema_del" value="<?=$thema_idx?>"><span>삭제</span></button>
						<div class="tdw_list_regi" id="tdw_list_regi_thema_<?=$thema_idx?>">
							<textarea name="" class="textarea_regi" id="textarea_regi_thema_<?=$thema_idx?>"><?=$thema_title?></textarea>
							<div class="btn_regi_box">
								<button class="btn_regi_submit" id="btn_regi_thema_submit" value="<?=$thema_idx?>"><span>확인</span></button>
								<button class="btn_regi_cancel" id="btn_regi_thema_cancel" value="<?=$thema_idx?>"><span>취소</span></button>
							</div>
						</div>
					</div>
				</div>
			</li>
		<?

		}
		echo "|".$thema_info_cnt;
	}else{?>
		<li>
			<div class="layer_user_no">
				<strong>등록된 테마가 없습니다.</strong>
			</div>
		</li>
	<?
		echo "|".$thema_info_cnt;
	}
	exit;
}


//테마 추가
if($mode == "challenges_thema_add"){

	$thema_title = $_POST['thema_title'];
	$sql = "select idx, title from work_challenges_thema where state='0' and companyno='".$companyno."' and title='".$thema_title."' order by idx desc";
	$thema_info = selectQuery($sql);
	if(!$thema_info['idx']){

		$sql = "insert into work_challenges_thema(title, companyno, ip) values('".$thema_title."', '".$companyno."','".LIP."')";
		$result_idx = insertIdxQuery($sql);
		if($result_idx){


			$sql = "select idx, title from work_challenges_thema where state='0' and companyno='".$companyno."' order by idx desc";
			$thema_list = selectAllQuery($sql);
			for($i=0; $i<count($thema_list['idx']); $i++){

				$thema_idx = $thema_list['idx'][$i];
				$thema_title = $thema_list['title'][$i];

				?>
					<li>
						<div class="tdw_list_box">
							<div class="tdw_list_chk">
								<button class="btn_tdw_list_chk" id="btn_tdw_list_thema_chk" value="<?=$thema_idx?>"><span>완료체크</span></button>
							</div>
							<div class="tdw_list_desc">
								<p id="tdw_list_desc_thema_<?=$thema_idx?>"><?=$thema_title?></p>
								<button class="btn_list_del" id="btn_list_thema_del" value="<?=$thema_idx?>"><span>삭제</span></button>
								<div class="tdw_list_regi" id="tdw_list_regi_thema_<?=$thema_idx?>">
									<textarea name="" class="textarea_regi" id="textarea_regi_thema_<?=$thema_idx?>"><?=$thema_title?></textarea>
									<div class="btn_regi_box">
										<button class="btn_regi_submit" id="btn_regi_thema_submit"><span>확인</span></button>
										<button class="btn_regi_cancel" id="btn_regi_thema_cancel"><span>취소</span></button>
									</div>
								</div>
							</div>
						</div>
					</li>
				<?
			}
			echo "|".count($thema_list['idx']);
		}
	}else{
		echo "|thema_over|".$thema_title;
		exit;
	}

	exit;
}


//테마 선택 삭제
if($mode == "challenges_thema_del"){

	$thema_idx = $_POST['thema_idx'];
	$thema_idx = preg_replace("/[^0-9]/", "", $thema_idx);
	if($thema_idx){
		$sql = "select idx from work_challenges_thema where state='0' and companyno='".$companyno."' and idx='".$thema_idx."' and companyno='".$companyno."'";
		$thema_info = selectQuery($sql);
		if($thema_info['idx']){

			$sql = "update work_challenges_thema set state='9', editdate=".DBDATE." where companyno='".$companyno."' and idx='".$thema_info['idx']."'";
			$return = updateQuery($sql);
			if($return){

				//개별 테마리스트 삭제처리
				$sql = "select idx from work_challenges_thema_user_list where state='0' and companyno='".$companyno."' and thema_idx='".$thema_info['idx']."'";
				$thema_user_list_info = selectQuery($sql);
				if($thema_user_list_info['idx']){
					$sql = "update work_challenges_thema_user_list set state='9' where state='0' and companyno='".$companyno."' and thema_idx='".$thema_info['idx']."'";
					$up1 = updateQuery($sql);
				}

				//챌린지 테마 추천 리스트 삭제
				$sql = "select idx from work_challenges_thema_recom_list where state='0' and companyno='".$companyno."' and thema_idx='".$thema_info['idx']."'";
				$thema_recom_lis_info = selectQuery($sql);
				if($thema_recom_lis_info['idx']){
					$sql = "update work_challenges_thema_recom_list set state='9' where state='0' and companyno='".$companyno."' and thema_idx='".$thema_info['idx']."'";
					$up2 = updateQuery($sql);
				}

				//챌린지 테마설정 삭제
				$sql = "select idx from work_challenges_thema_list where state='0' and companyno='".$companyno."' and thema_idx='".$thema_info['idx']."'";
				$challenges_thema_list_info = selectQuery($sql);
				if($challenges_thema_list_info['idx']){
					$sql = "update work_challenges_thema_list set state='9' where state='0' and companyno='".$companyno."' and thema_idx='".$thema_info['idx']."'";
					$up3 = updateQuery($sql);
				}


				$sql = "select count(1) as cnt from work_challenges_thema where state='0' and companyno='".$companyno."'";
				$thema_info = selectQuery($sql);
				if($thema_info){
					$cnt = number_format($thema_info['cnt']);
					echo "complete|".$cnt;
				}else{
					echo "complete|0";
				}
				exit;
			}
		}
	}
	exit;
}

//테마 제목 수정
if($mode == "thema_title_edit"){

	$idx = $_POST['idx'];
	$idx = preg_replace("/[^0-9]/", "", $idx);
	$contents = $_POST['contents'];
	//$contents = stripslashes(nl2br($contents));
	$contents = nl2br($contents);

	//홑따옴표 때문에 아래와 같이 처리
	if(strpos($contents, "'") !== false) {
		$contents = str_replace("'", "''", $contents);
	}

	if($idx){
		$sql = "select idx from work_challenges_thema where state='0' and companyno='".$companyno."' and idx='".$idx."'";
		$thema_info = selectQuery($sql);
		if($thema_info['idx']){
			$sql = "update work_challenges_thema set title='".$contents."' where state='0' and companyno='".$companyno."' and idx='".$thema_info['idx']."'";
			$res = updateQuery($sql);
			if($res){
				echo "complete";
				exit;
			}
		}
	}
	exit;
}


//챌린지 숨기기
if($mode == "challenges_hide"){

	$chall_idx = $_POST['chall_idx'];
	$view_flag = $_POST['view_flag'];

	$chall_idx = preg_replace("/[^0-9]/", "", $chall_idx);
	$view_flag = preg_replace("/[^0-9]/", "", $view_flag);


	if($chall_idx){
		$sql = "select idx, email from work_challenges where state='0' and companyno='".$companyno."' and idx='".$chall_idx."'";
		$info = selectQuery($sql);
		if($info['idx']){
			$sql = "update work_challenges set view_flag='".$view_flag."', editdate=".DBDATE." where companyno='".$companyno."' and idx='".$info['idx']."'";
			$res = updateQuery($sql);
			if($res){
				echo "complete";
				exit;
			}
		}
	}
	exit;
}


//좌측메뉴 열고닫기
if($mode == "rew_menu_onoff"){

	print "<pre>";
	print_r($_POST);
	print "</pre>";

	$onoff = $_POST['onoff'];
	$onoff = preg_replace("/[^0-9]/", "", $onoff);

	//print " onoff :: " .$onoff;
	//echo "###\n\n";
	//echo date("Y-m-d H:i:s", COOKIE_MAXTIME);

	if($onoff=='1'){
		if ( isset($_COOKIE['onoff_reg']) && $_COOKIE['onoff_reg']-time() < 86400 * 7 ) { // 쿠키가 있고, 유효 시간이 한 달 미만이면
			setcookie('onoff_reg', COOKIE_MAXTIME, COOKIE_MAXTIME, '/', C_DOMAIN);
			setcookie('onoff', $onoff, COOKIE_MAXTIME , '/', C_DOMAIN);
		}else{
			setcookie('onoff_reg', COOKIE_MAXTIME, COOKIE_MAXTIME, '/', C_DOMAIN);
			setcookie('onoff', $onoff, COOKIE_MAXTIME , '/', C_DOMAIN);
		}
	}else if($onoff=='0'){
		setcookie('onoff_reg', time()-3600 , time()-3600, '/', C_DOMAIN);
		setcookie('onoff', $onoff, time()-3600 , '/', C_DOMAIN);
	}

	exit;
}

//코멘트 작성자 확인
if($mode == "work_comment_check"){

	$comment_idx = $_POST['comment_idx'];
	$comment_idx = preg_replace("/[^0-9]/", "", $comment_idx);

	if($comment_idx){
		$sql = "select idx, work_idx, name, email from work_todaywork_comment where companyno='".$companyno."' and idx='".$comment_idx."'";
		$comment_info = selectQuery($sql);
		if($comment_info['idx']){
			echo $comment_info['email']."|".$comment_info['name'];
			exit;
		}
	}
	exit;
}


//업무 작성자 확인
if($mode == "work_todaywork_check"){

	$work_idx = $_POST['work_idx'];
	$work_idx = preg_replace("/[^0-9]/", "", $work_idx);

	if($work_idx){
		$sql = "select idx, work_idx, name, email from work_todaywork where companyno='".$companyno."' and idx='".$work_idx."'";
		$work_info = selectQuery($sql);
		if($work_info['idx']){
			echo $work_info['email']."|".$work_info['name'];
			exit;
		}
	}
	exit;
}


//좋아요 인증메시지 체크
if($mode == "chall_masage_check"){

	$masage_idx = $_POST['masage_idx'];
	$masage_idx = preg_replace("/[^0-9]/", "", $masage_idx);

	$chall_idx = $_POST['chall_idx'];
	$chall_idx = preg_replace("/[^0-9]/", "", $chall_idx);

	if($chall_idx && $masage_idx){
		$sql = "select idx, email, name from work_challenges_result where state='1' and companyno='".$companyno."' and attend_type='1' and challenges_idx='".$chall_idx."' and idx='".$masage_idx."'";
		$work_info = selectQuery($sql);
		if($work_info['idx']){
			echo $work_info['email']."|".$work_info['name'];
			exit;
		}
	}
	exit;
}


//좋아요 인증파일 체크
if($mode == "chall_file_check"){

	$file_idx = $_POST['file_idx'];
	$file_idx = preg_replace("/[^0-9]/", "", $file_idx);

	$chall_idx = $_POST['chall_idx'];
	$chall_idx = preg_replace("/[^0-9]/", "", $chall_idx);

	if($chall_idx && $file_idx){
		$sql = "select idx, email, name from work_challenges_result where state='1' and companyno='".$companyno."' and attend_type='2' and challenges_idx='".$chall_idx."' and idx='".$file_idx."'";
		$work_info = selectQuery($sql);
		if($work_info['idx']){
			echo $work_info['email']."|".$work_info['name'];
			exit;
		}
	}
	exit;
}


//좋아요 인증파일 + 인증메시지 체크
if($mode == "chall_mix_check"){

	$mix_idx = $_POST['mix_idx'];
	$mix_idx = preg_replace("/[^0-9]/", "", $mix_idx);

	$chall_idx = $_POST['chall_idx'];
	$chall_idx = preg_replace("/[^0-9]/", "", $chall_idx);

	if($chall_idx && $mix_idx){
		$sql = "select idx, email, name from work_challenges_result where state='1' and companyno='".$companyno."' and attend_type='3' and challenges_idx='".$chall_idx."' and idx='".$mix_idx."'";
		$work_info = selectQuery($sql);
		if($work_info['idx']){
			echo $work_info['email']."|".$work_info['name'];
			exit;
		}
	}
	exit;
}




//좋아요
if($mode == "lives_like"){

	//$start = get_time();
	$service = $_POST['service'];
	$jf_idx = $_POST['jf_idx'];
	$link_idx = $_POST['link_idx'];
	$jf_idx = preg_replace("/[^0-9]/", "", $jf_idx);


	$work_idx = $_POST['work_idx'];
	$work_idx = preg_replace("/[^0-9]/", "", $work_idx);


	//0:기본, 1:출근제일빨리함, 2:오늘업무제일많이씀
	$like_flag = $_POST['like_flag'];
	$like_flag = preg_replace("/[^0-9]/", "", $like_flag);
	if(!$like_flag){
		$like_flag = '0';
	}

	//좋아요 코멘트 내용
	$jl_comment = $_POST['jl_comment'];

	//좋아요 받는아이디
	$send_userid = $_POST['send_userid'];
	$send_info = member_row_info($send_userid);

	//일일 최다 좋아요 횟수 체크
	$limit_like = limit_like_check($send_info['email']);
	if($limit_like['cnt'] > 5){
		echo "limit_like";
		exit;
	}


	if($jf_idx){

		//자동 ai 댓글 조회, ai댓글인경우 work_idx=업무idx번호로 교체
		$sql = "select idx, link_idx from work_todaywork_comment where state='0' and companyno='".$companyno."' and cmt_flag='1' and idx='".$work_idx."'";
		$cmt_ai_info = selectQuery($sql);

		$data_check = false;
		//ai댓글 좋아요 체크(1회만)

		//자동 ai 댓글일경우
		if($cmt_ai_info['idx']){
			$work_idx = $cmt_ai_info['link_idx'];
			$sql = "select idx from work_todaywork_like where state='0' and companyno='".$companyno."' and service='".$service."' and like_flag='".$like_flag."' and email='".$send_info['email']."' and send_email='".$user_id."' and work_idx='".$work_idx."' and workdate='".TODATE."'";
			$like_info = selectQuery($sql);
			if($like_info['idx']){
				// $data_check = true;
			}
		}else{
			//좋아요 횟수 제한
			$sql = "select idx from work_todaywork_like where state='0' and companyno='".$companyno."' and service='".$service."' and like_flag='".$like_flag."' and email='".$send_info['email']."' and send_email='".$user_id."' and work_idx='".$work_idx."' and workdate='".TODATE."'";
			$like_info = selectQuery($sql);
			if($like_info['idx']){
				// $data_check = true;
			}

		}


		//if(!$like_info['idx']){
		if($data_check==false){


			// //2023-11-17 수정
			// $penalty = member_penalty($send_info['email']);
			// if($penalty['penalty_state']>0){
			// 	echo "penalty";
			// 	exit;
			// }

			$sql = "insert into work_todaywork_like(companyno,kind_flag, service, work_idx, like_flag, email, name, send_email, send_name, comment, type_flag, ip, workdate) values(";
			$sql = $sql .= "'".$companyno."','".$jf_idx."', '".$service."', '".$work_idx."', '".$like_flag."', '".$send_info['email']."', '".$send_info['name']."', '".$user_id."', '".$user_name."', '".$jl_comment."', '".$type_flag."', '".LIP."', '".TODATE."')";
			$insert_idx = insertIdxQuery($sql);

			$tid = $send_info['email'];
			$tokenTitle = $user_name."님이 좋아요를 보냈어요";
			pushToken($tokenTitle,$jl_comment,$tid,'live','10',$user_id,$user_name,$work_idx,null,'live');

			/*
			if($user_id=='adsb123@naver.com'){
				echo "sadary_sql:::".$sql;
			}
			*/

			/*
			if($user_id == 'fpa5023@nate.com' || $user_id == 'fpa5023@naver.com'){
				echo "link_idx".$link_idx;
			}
			*/

			if($insert_idx){

				$sql = "select idx from work_todaywork_like where work_idx = '".$work_idx."' and send_email = '".$user_id."'";
				$sql = $sql." and kind_flag = '".$jf_idx."' and companyno = '".$companyno."' and email = '".$send_info['email']."' order by regdate desc limit 1";
				$like_idx = selectQuery($sql);

				$like_com_idx = $like_idx['idx'];

				//if($user_id=='sadary0@nate.com' || $user_id=='eyson@bizforms.co.kr'){
				//메모작성(ai)
					//memo_ai_write($work_idx, $insert_idx, $user_id, $send_info['email'], $jf_idx);
				//좋아요 보내기 ai 메모 작성
				//memo_ai_write($work_idx, $insert_idx, $user_id, $send_info['email'], "like");

				memo_ai_write($work_idx, $insert_idx, $user_id, $send_userid, $jf_idx, "like",$jl_comment,$like_com_idx);
				//}


				//타임라인(좋아요)
				work_data_log('0','8', $insert_idx, $user_id, $user_name, $send_info['email'], $send_info['name']);

				//타임라인(좋아요 받음)
				work_data_log('0','10', $insert_idx, $send_info['email'], $send_info['name'], $user_id, $user_name);

				//1:인정하기, 2:응원하기, 3:칭찬하기, 4:격려하기, 5:축하하기, 6:감사하기

				if($jf_idx == '1'){

					//역량평가지표(좋아요 인정하기), 실행
					work_cp_reward("like","0006", $send_info['email'], $insert_idx);

				}else if($jf_idx == '2'){

					//역량평가지표(좋아요 응원하기), 협업
					work_cp_reward("like","0007", $send_info['email'], $insert_idx);

				}else if($jf_idx == '3'){

					//역량평가지표(좋아요 칭찬하기), 성장
					work_cp_reward("like","0003", $send_info['email'], $insert_idx);

				}else if($jf_idx == '4'){

					//역량평가지표(좋아요 격려하기), 성실
					work_cp_reward("like","0005", $send_info['email'], $insert_idx);

				}else if($jf_idx == '5'){

					//역량평가지표(좋아요 축하하기), 에너지
					work_cp_reward("like","0004", $send_info['email'], $insert_idx);

				}else if($jf_idx == '6'){

					//역량평가지표(좋아요 감사하기), 성과
					work_cp_reward("like","0002", $send_info['email'], $insert_idx);

				}

				//역량평가지표(좋아요 보내기)
				work_cp_reward("like", "0001", $user_id, $insert_idx);

				//좋아요 누르면 자신의 협업점수 +1점
				work_cp_reward("like","0007", $user_id, $insert_idx);

				echo "|complete|".$insert_idx;
				exit;
			}
		}
	}
}

if($mode == "tuto_close"){
		setcookie('tuto_close', 1, time() + 86400 * 1 , '/', C_DOMAIN);
	echo "complete";
}


if($mode == "party_like"){
	$idx = $_POST['idx'];
	$comment = $_POST['comment'];
	$like_idx = $_POST['like_idx'];

	$sql = "select idx, work_idx, send_name, workdate, com_idx from work_todaywork_like where idx = '".$like_idx."' ";
	$query = selectQuery($sql);

	if($query['idx']){
		echo "complete|";
	}else{
		echo "notQuery|";
		exit;
	}

	$workdate = $query['workdate'];

	$time = date("H:i",time());
	if ($time > '12:00'){
		$com_time = @explode(":",$time);
		if($time > '13:00'){
			$com_his_tmp_h = $com_time[0]-'12';
		}else{
			$com_his_tmp_h = $com_time[0];
		}
		
		$com_his_tmp_h = $com_his_tmp_h .":". $com_time[1];
		$after = " 오후 ";
	}else{
		$com_his_tmp_h = $time;
		$after = " 오전 ";
	}
	$time_stamp = $workdate.$after.$com_his_tmp_h;
?>
	<div class="tdw_list_memo_desc" id="comment_list_<?=$idx?>">
		<div class="tdw_list_memo_name" style="user-select:auto;"><?=$user_name?></div>
		<button class="btn_memo_jjim on" value="<?=$query['com_idx']?>"><span>좋아요</span></button>
		<div class="tdw_list_memo_conts">
			<span class="tdw_list_memo_conts_txt"><?=$comment?></span>
			<em class="tdw_list_memo_conts_date"><?=$time_stamp?></em>
		</div>
	</div>
<?
	exit;
}

if($mode == "chamyeo_update"){
	$idx = $_POST['idx']; //인덱스넘버$idx = $_POST['idx'];
	$chll_idx = preg_replace("/[^0-9]/", "", $idx);
	$status = $_POST['status']; // del : 삭제 , up : 수정
	$comment = $_POST['comment'];
	// $comment = nl2br($comment);
	// $comment = addslashes($comment);
	$comment = replace_text($comment);
	$regdate = date('Y-m-d H:i:s');

	$delArray = $_POST['del'];
	if($delArray){
		$sql = "update work_challenges_file_info set state = '9' where idx in (".$delArray.") and challenges_idx = '".$idx."' and email = '".$user_id."' ";
		// echo $sql."|";
		$updateDel = updateQuery($sql);		
	}

	if($status == "del"){
		$sql = "select challenges_idx,email,name from work_challenges_result where idx = '".$idx."' ";
		$query = selectQuery($sql);
		$email = $query['email'];
		$name = $query['name'];

		$sql = "select coin from work_challenges where idx = '".$query['challenges_idx']."'";
		$query = selectQuery($sql);
		$coin = $query['coin'];

		$sql = "update work_member set coin = coin - '".$coin."' where email = '".$email."' and companyno = '".$companyno."'";
		$member = updateQuery($sql);

		$sql = "select idx from work_coininfo where work_idx = '".$chll_idx."' and code = '500' and state = '0'";
		$enter_coin = selectQuery($sql);

		if($enter_coin['idx']){
			$sql = "insert into work_coininfo (state, code, work_idx, reward_type, companyno, email, name, coin, memo, ip, workdate, regdate) values(";
			$sql = $sql .= "'1', '510', '".$chll_idx."', 'challenge', '".$companyno."', '".$email."', '".$name."', '".$coin."', '챌린지 참여 취소로 코인회수' ,'".LIP."', '".TODATE."', '".$regdate."') ";
			$coin_minus = insertIdxQuery($sql);
		}

		$sql = "update work_challenges_result set state = '9' where idx = '".$idx."' and email = '".$user_id."' ";
		$del = updateQuery($sql);

		$sql = "update work_cp_reward_list set state = '9' where link_idx = '".$idx."' and email = '".$user_id."' ";
		$del_cp = updateQuery($sql);

		echo "deleteSuccess";
	}else if($status == "update"){
		$sql = "select idx from work_challenges_result where state = '1' and idx = '".$chll_idx."' ";
		$query = selectQuery($sql);

		if($query['idx']){
			$sql = "update work_challenges_result set comment = '".$comment."' where idx = '".$chll_idx."' and email = '".$user_id."' and state = '1'";
			$updateCo = updateQuery($sql);
		//파일첨부
		if($_FILES){
			for($i=0;$i<count($_FILES['files']['name']);$i++){
				//파일타입(이미지파일:true, 일반파일:false)
				$file_type_img = false;

				//파일명
				$filename = $_FILES['files']['name'][$i];
				
				$sql = "select idx,file_real_img_name from work_challenges_file_info where challenges_idx = '".$query['idx']."' and email = '".$user_id."' order by num desc ";
				$enter = selectAllQuery($sql);
				// for($j=0;$j<count($enter['idx']);$j++){
				// 		if($filename = $enter['file_real_img_name'][$j]){
				// 			$hasfile = true;
				// 			break;
				// 	}
				// }
				// if($hasfile != true){
					$ext = array_pop(explode(".", strtolower($filename)));
					//이미지파일 허용확장자
					if(in_array($ext , $img_file_allowed_ext)){
						$file_type_img = true;
					}
					//이미지 처리부분
					if($file_type_img){
						//파일순번
						$file_img_num = 1;

						//파일확장자 추출
						$filename = $_FILES['files']['name'][$i];
						$ext = array_pop(explode(".", strtolower($filename)));

						//허용확장자체크
						if( !in_array($ext, $img_file_allowed_ext) ) {
							echo "ext_file";
							exit;
						}

						//파일타입
						$file_type = $_FILES['files']['type'][$i];

						//파일사이즈
						$file_size = $_FILES['files']['size'][$i];

						//임시파일명
						$file_tmp_name = $_FILES['files']['tmp_name'][$i];

						//파일명
						$file_real_name = $filename;

						$file_source	= $file_tmp_name; //파일명
						//$file_ext		= array_pop(explode('.', $filename)); //확장자 추출 (array_pop : 배열의 마지막 원소를 빼내어 반환)
						$file_info		= getimagesize($file_tmp_name);
						$file_width		= $file_info[0]; //이미지 가로 사이즈
						$file_height	= $file_info[1]; //이미지 세로 사이즈
						$file_type		= $file_type;


						//라사이즈 
						$rezie_file_path = "";
						$rezie_renamefile = "";
						$resize_file = "";
						$resize_val = "0";


						//랜덤번호
						$rand_id = name_random();

						//변경되는 파일명
						list($microtime,$timestamp) = explode(' ',microtime());
						$time = $timestamp.substr($microtime, 2, 3);
						$datetime = date("YmdHis", $timestamp).substr($microtime, 2, 3);

						//$renamefile = date("YmdHis")."_{$rand_id}_challenges_{$res_idx}.{$ext}";
						$renamefile = "{$datetime}_{$rand_id}_challenges_{$chll_idx}.{$ext}";

						//년도
						$dir_year = date("Y", TODAYTIME);

						//월
						$dir_month = date("m", TODAYTIME);

						//업로드 디렉토리 - /data/challenges/files/년/월/
						$upload_path = $dir_file_path."/".$file_save_dir_img."/".$dir_year."/".$dir_month."/";
						$upload_path = str_replace($file_save_dir_img , "data/".$companyno."/".$comfolder."/"."challenges/img" , $upload_path);

						//업로드 디렉토리 - /data/challenges/files/년/월/
						$upload_path_ori = $dir_file_path."/".$file_save_dir_img_ori."/".$dir_year."/".$dir_month."/";
						$upload_path_ori = str_replace($file_save_dir_img_ori , "data/".$companyno."/".$comfolder."/"."challenges/img_ori" , $upload_path_ori);

						///echo $_SERVER['DOCUMENT_ROOT'];
						//echo "<Br>";
						//echo $upload_path;
						//echo "<Br>";
						//echo $upload_path_ori;

						//exit;

						//디렉토리 없는 경우 권한 부여 및 생성
						if ( !is_dir ( $upload_path ) ){
							mkdir( $upload_path , 0777, true);
						}

						//디렉토리 없는 경우 권한 부여 및 생성 - 원본폴더
						if ( !is_dir ( $upload_path_ori ) ){
							mkdir( $upload_path_ori , 0777, true);
						}

						
						//리사이즈한 업로드될 파일경로/파일명
						$upload_files = $upload_path.$renamefile;

						//원본 업로드될 파일경로/파일명
						$upload_files_ori = $upload_path_ori.$renamefile;


						$new_file_width = 5000; //이미지 가로 사이즈 지정
						$rate = $new_file_width / $file_width; //이미지 세로 사이즈 및 파일 사이즈(quality) 조절을 위한 비율 
						$new_file_height = (int)($file_height * $rate); 
						$new_quality = (int)($file_size * $rate);


						//이미지 가로사이즈가 250보다 크면 사이즈 조절
						if ($file_width > $new_file_width){
							switch($file_type){
								case "image/jpeg" :
									$image = imagecreatefromjpeg($file_source);
									break;
								case "image/gif" :
									$image = imagecreatefromgif($file_source);
									break;
								case "image/png" :
									$image = imagecreatefrompng($file_source);
									break;
								default:	
									$image = "";
									break;
							}

							//사이즈 조정으로 이미지가 회전하는걸 막기위함
							$degrees = "-90";
							$image = imagerotate($image, $degrees, 0);

							//리사이즈
							$rezie_img = fn_imagejpeg($image, $upload_files, $new_file_width, $new_file_height, $file_width, $file_height, $new_quality);
							

							//원본이미지
							$return = move_uploaded_file($file_tmp_name, $upload_files_ori);

							//리사이즈 파일 용량
							$file_resize = filesize($upload_files); 


						}else{

							$rezie_img = "";
							//파일 업로드
							$return = move_uploaded_file($file_tmp_name, $upload_files_ori);

						}

						if($return){
							//리사이즈이미지 경로
							$file_path = str_replace($dir_file_path , "" , $upload_path);

							//원본이미지 경로
							$file_path_ori = str_replace($dir_file_path , "" , $upload_path_ori);
							//리사이즈 조정이 안된경우
							if($rezie_img == true){
								$rezie_file_path = $file_path;
								$rezie_renamefile = $renamefile;
								$resize_file = $file_resize;
								$resize_val = "1";
							}else{
								$rezie_file_path = "";
								$rezie_renamefile = "";
								$resize_file = "";
								$resize_val = "0";
							}
							$sql = "insert into work_challenges_file_info (state, challenges_idx, num, companyno,  email, partno, resize, file_path, file_name, file_size, file_ori_path, file_ori_name, file_ori_size, file_real_img_name, file_type, ip, file_regdate) values(";
							$sql = $sql .="'1','".$chll_idx."', '".$i."', '".$companyno."',  '".$user_id."', '".$user_part."', '".$resize_val."', '".$file_path."', '".$renamefile."', '".$file_size."','".$file_path_ori."','".$renamefile."','".$file_size."','".$file_real_name."', '".$file_type."', '".LIP."', '".DBDATE."')";
							$file_idx = insertIdxQuery($sql);
						}

					//일반파일처리
					}else{
						$filename = $_FILES['files']['name'][$i];
						$ext = array_pop(explode(".", strtolower($filename)));

						//허용확장자체크
						if(!in_array($ext, $file_allowed_ext)) {
							echo $ext;
							exit;
						}

						//파일타입
						$file_type = $_FILES['files']['type'][$i];

						//파일사이즈
						$file_size = $_FILES['files']['size'][$i];

						//임시파일명
						$file_tmp_name = $_FILES['files']['tmp_name'][$i];

						//파일명
						$file_real_name = $filename;


						//랜덤번호
						$rand_id = name_random();

						//변경되는 파일명
						list($microtime,$timestamp) = explode(' ',microtime());
						$time = $timestamp.substr($microtime, 2, 3);
						$datetime = date("YmdHis", $timestamp).substr($microtime, 2, 3);

						//$renamefile = date("YmdHis")."_{$rand_id}_challenges_{$res_idx}.{$ext}";
						$renamefile = "{$datetime}_{$rand_id}_challenges_{$chll_idx}.{$ext}";

						//년도
						$dir_year = date("Y", TODAYTIME);

						//월
						$dir_month = date("m", TODAYTIME);

						//업로드 디렉토리 - /data/challenges/files/년/월/
						$upload_path = $dir_file_path."/".$file_save_dir."/".$dir_year."/".$dir_month."/";
						$upload_path = str_replace($file_save_dir , "data/".$companyno."/".$comfolder."/"."challenges/file" , $upload_path);
						//디렉토리 없는 경우 권한 부여 및 생성
						if ( !is_dir ( $upload_path ) ){
							mkdir( $upload_path , 0777, true);
						}

						$result = file_upload_send( $file_tmp_name, $upload_path. $renamefile );
						$file_path = str_replace($dir_file_path , "" , $upload_path);
						if(!$result){
							echo "not_files2";
							exit;
						}else{
							$sql = "insert into work_challenges_file_info (state, challenges_idx, num, companyno,  email, partno, resize, file_path, file_name, file_size,  file_real_img_name, file_type, ip, file_regdate) values(";
							$sql = $sql .="'1','".$chll_idx."', '".$i."', '".$companyno."',  '".$user_id."', '".$user_part."', '".$resize_val."', '".$file_path."', '".$renamefile."', '".$file_size."','".$file_real_name."', '".$file_type."', '".LIP."', '".DBDATE."')";
							$file_idx = insertIdxQuery($sql);
						}
					}		
				}
			}//for문 끝나는 곳
		// }
	}
	echo "updateSuccess";
	exit;
	}
}

if($mode == "layer_file_del"){
	$idx = $_POST['idx'];
	
	// $sql = "update work_challenges_file_info set state = '9' where idx = '".$idx."' and email = '".$user_id."' ";
	// $updateQuery = updateQuery($sql);

	$sql = "select idx from work_challenges_file_info where idx = '".$idx."' and email = '".$user_id."'";
	$selectQuery = selectQuery($sql);

	if($selectQuery['idx']){
		echo "delSuccess";
	}
	exit;
}

if($mode == "update_cancel"){
	$cham_idx = $_POST['cham_idx'];
	$delArray = $_POST['del'];
	$del_list = explode("," , $delArray);

	// $sql = "update work_challenges_file_info set state = '1' where challenges_idx = '".$cham_idx."' and email = '".$user_id."' ";
	// $updateQuery = updateQuery($sql);
	if($delArray){
		for($i=0;$i<count($del_list);$i++){
			$sql = "select idx, file_real_img_name from work_challenges_file_info where idx = '".$del_list[$i]."' and challenges_idx = '".$cham_idx."' and email = '".$user_id."'";
			$query = selectQuery($sql);
			?>
			<div class="file_desc" id="chall_file_desc_<?=$i?>">
				<input type="hidden" id="mix_file_idx_<?=$i?>" value="<?=$query['idx']?>">
				<span><?=$query['file_real_img_name']?></span>
				<button id="mix_file_del_<?=$i?>">삭제</button>
			</div>
		<?}
	}
	echo "|cancelSuccess";
	exit;
}

//패널티ON
if($mode == "penalty_on"){
	$kind = $_POST['kind'];

	//현재날짜
	$now_date = date("Y-m-d H:i:s");

	$sql = "update work_member set penalty_state = penalty_state + 1 where email = '".$user_id."' and state = '0' and companyno = '".$companyno."' ";
	$update_penalty = updateQuery($sql);
	
	if($kind == "incount"){
		// $sql = "update work_member_penalty set state = '9',updatetime = '".DBDATE."' where email = '".$user_id."' and incount = '1' ";
		// $update = updateQuery($sql);
	}else if($kind == "workcount"){
		// $sql = "update work_member_penalty set state = '9',updatetime = '".DBDATE."' where email = '".$user_id."' and work = '1' ";
		// $update = updateQuery($sql);
	}else if($kind == "outcount"){
		// $sql = "update work_member_penalty set state = '9',updatetime = '".DBDATE."' where email = '".$user_id."' and outcount = '1' ";
		// $update = updateQuery($sql);
	}

	echo "penaltyOn";
	exit;
}

?>
