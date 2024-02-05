<?php
	//로그인 처리 페이지
	//로그인 후 쿠키 생성 및 로그인 기록 저장

	$home_dir = str_replace( basename(__DIR__) , "", __DIR__ );
	include $home_dir . "inc_lude/conf_mysqli.php";
	include $home_dir . "inc/SHA256/KISA_SHA256.php";

	include DBCON_MYSQLI;
	include FUNC_MYSQLI;
	$mode = $_POST['mode'];
	//로그인
	if($mode == "demo_login"){

		$user_id = trim($_POST['id']);
		$user_pw = "0000";

		if($user_id && $user_pw){
			//아이디 비밀번호 체크
			//KISA_SHA256암호화
			$kisa_user_pw = kisa_encrypt($user_pw);
			$kisa_sha256_chk = true;
			$sql = "select idx, password, name, highlevel, companyno, part, partno, live_1, left(live_1_regdate, 10) as live_1_regdate from work_member where state='0' and email='".$user_id."'";
			$res = selectQuery($sql);
			if($res['idx']){
				//KISA_SHA256암호화 비밀번호 맞으면
				if($kisa_sha256_chk && $res['password'] == $kisa_user_pw ){
					$res["passwd"] = 1;
				}

				//회사코드
				$companyno = trim($res["companyno"]);

				$sql = "select idx, code from work_company where state='0' and idx='".$companyno."'";
				$company_info = selectQuery($sql);
				if($company_info['idx']){
					$comfolder = $company_info['code'];
				}

				//회원정보 없을때
				if(!$res["passwd"]){
					echo "use_deny";
					exit;
				}
				elseif($res["passwd"]=="1"){
					$sql = "update work_member set login_date = ".DBDATE." , login_count = login_count + 1 where companyno='".$companyno."' and email='".$user_id."'";
					$return = updateQuery($sql);


					//회원이름
					$user_name = $res["name"];

					//회원등급
					$highlevel = trim($res["highlevel"]);
					$highlevel = preg_replace("/[^0-9]/", "", $highlevel);


					//회사코드
					$companyno = preg_replace("/[^0-9]/", "", $companyno);

					//부서/팀
					$partno = trim($res["partno"]);
					$partno = preg_replace("/[^0-9]/", "", $partno);
					
					$partname = $res["part"];


					//24시간 쿠키 설정
					$login_year = date("Y", TODAYTIME); 
					$login_month = date("m", TODAYTIME);
					$login_day = date("d", TODAYTIME);
					$login_h = date("H", TODAYTIME);
					$login_i = date("i", TODAYTIME);
					$login_s = date("s", TODAYTIME);

					//$login_tm = mktime($login_h, $login_i, $login_s, $login_month, $login_day, $login_year);	//현재시간
					$login_harutm = mktime(23,59,59, $login_month, $login_day, $login_year);					//제한시간
					//$limit_time = $login_harutm - $login_tm;													//남은시간(오늘시간 - 현재시간)

					//쿠키 제한시간(23시 59분 59초)
					$limit_time = mktime(23,59,59, $login_month, $login_day, $login_year);						//제한시간

					//$cookie_limit_time = TODAYTIME + $limit_time;
					$cookie_limit_time = $limit_time;

					//쿠키 24시간
					//$cookie_limit_time = COOKIE_TIME;
					
					if($chk_login == true){

						// Example usage
						// $key = "rewardy";
						// $valueToEncrypt = $companyno;
						// $encryptedCookie = encryptCookie($valueToEncrypt, $key);

						//회원아이디
						setcookie('user_id', $user_id, COOKIE_90DAYS , '/', C_DOMAIN);

						//회원이름
						setcookie('user_name', $user_name , COOKIE_90DAYS , '/', C_DOMAIN);

						//부서코드
						setcookie('user_part', $partno , COOKIE_90DAYS , '/', C_DOMAIN);

						//부서명
						setcookie('part_name', $partname , COOKIE_90DAYS , '/', C_DOMAIN);

						//회사코드
						setcookie('companyno', $companyno , COOKIE_90DAYS , '/', C_DOMAIN);

						//회사폴더명
						setcookie('comfolder', $comfolder , COOKIE_90DAYS , '/', C_DOMAIN);

						echo "m_";
					}else{

						// Example usage
						$key = "rewardy";
						$valueToEncrypt = $companyno;
						$encryptedCookie = encryptCookie($valueToEncrypt, $key);
						//회원아이디
						setcookie('user_id', $user_id, $cookie_limit_time , '/', C_DOMAIN);

						//회원이름
						setcookie('user_name', $user_name , $cookie_limit_time , '/', C_DOMAIN);

						//부서코드
						setcookie('user_part', $partno , $cookie_limit_time , '/', C_DOMAIN);

						//부서명
						setcookie('part_name', $partname , $cookie_limit_time , '/', C_DOMAIN);

						//회사코드
						setcookie('companyno', $companyno , $cookie_limit_time , '/', C_DOMAIN);

						//회사코드 변조
						setcookie('com_change', $encryptedCookie , $cookie_limit_time , '/', C_DOMAIN);

						//회사폴더명
						setcookie('comfolder', $comfolder , $cookie_limit_time , '/', C_DOMAIN);
					
					}

					//회원등급(숫자일경우만)
					if (is_numeric($highlevel) == true){
						setcookie('user_level', $highlevel , $cookie_limit_time , '/', C_DOMAIN);
					}

					//로그인 아이디 저장 여부
					if($id_save == true){
						setcookie('id_save', $id_save , COOKIE_MAXTIME , '/', C_DOMAIN);
						//회원아이디 저장
						setcookie('cid', $user_id , COOKIE_MAXTIME , '/', C_DOMAIN);
					}else{

						setcookie('id_save', '' , COOKIE_MAXTIME , '/', C_DOMAIN);
						//회원아이디 저장
						setcookie('cid', '' , COOKIE_MAXTIME , '/', C_DOMAIN);
					}
						//회원등급
						if ($highlevel >= 0){

							switch ($highlevel){
								
								//일반회원(1~5)
								case "5":
									echo "use_ok";
									break;

								//관리자(0)
								default :
									echo "ad_ok";
									break;
							}

						}
					exit;
				}
			}
		}
	}

	if($mode == "login"){
		$user_id = trim($_POST['id']);
		$user_pw = trim($_POST['pwd']);
		$chk_login = trim($_POST['chk_login']);
		$id_save = trim($_POST['id_save']);

		$mobile = trim($_POST['mobile']);

		//영/숫자 4~30자이내
		//if (preg_match( "/^[0-9a-zA-Z]{4,30}$/", $user_id ) == false) die();
		

		//패스워드에 공백이 있는 경우
		if(preg_match("/\s/u", $user_pw) == true){
			echo "pempty";
			exit;
		}
		
		if ( !isset($user_id) || !isset($user_pw) ){
			//header("Content-Type: text/html; charset=UTF-8");
			//echo "<script>alert('아이디 또는 비밀번호가 빠졌거나 잘못된 접근입니다.');";
			//echo "window.location.replace('./login.php');</script>";
			echo "user_not";
			exit;
		}

		if($user_id && $user_pw){
			//아이디 비밀번호 체크
			//KISA_SHA256암호화
			$kisa_user_pw = kisa_encrypt($user_pw);
			$kisa_sha256_chk = true;
			$sql = "select idx, password, name, highlevel, companyno, part, partno, live_1, left(live_1_regdate, 10) as live_1_regdate from work_member where state='0' and email='".$user_id."'";
			$res = selectQuery($sql);
			if($res['idx']){
				//KISA_SHA256암호화 비밀번호 맞으면
				if($kisa_sha256_chk && $res['password'] == $kisa_user_pw ){
					$res["passwd"] = 1;
				}

				//회사코드
				$companyno = trim($res["companyno"]);

				$sql = "select idx, code from work_company where state='0' and idx='".$companyno."'";
				$company_info = selectQuery($sql);
				if($company_info['idx']){
					$comfolder = $company_info['code'];
				}

				//회원정보 없을때
				if(!$res["passwd"]){
					echo "use_deny";
					exit;
				}
				elseif($res["passwd"]=="1"){
					$sql = "update work_member set login_date = ".DBDATE." , login_count = login_count + 1 where companyno='".$companyno."' and email='".$user_id."'";
					$return = updateQuery($sql);

					//세션저장:id, name, session_id

				//	$some_name = session_name("some_name"); // must exists like this 
				//	session_set_cookie_params(0, '/', '.todaywork.co.kr');
					// session_start();
					// $_SESSION['user_id'] = $user_id;
					// $_SESSION['user_name'] = $res["name"];
					// $_SESSION['session_id'] = session_id();

					//회원이름
					$user_name = $res["name"];

					//회원등급
					$highlevel = trim($res["highlevel"]);
					$highlevel = preg_replace("/[^0-9]/", "", $highlevel);


					//회사코드
					$companyno = preg_replace("/[^0-9]/", "", $companyno);

					//부서/팀
					$partno = trim($res["partno"]);
					$partno = preg_replace("/[^0-9]/", "", $partno);
					
					$partname = $res["part"];


					//24시간 쿠키 설정
					$login_year = date("Y", TODAYTIME); 
					$login_month = date("m", TODAYTIME);
					$login_day = date("d", TODAYTIME);
					$login_h = date("H", TODAYTIME);
					$login_i = date("i", TODAYTIME);
					$login_s = date("s", TODAYTIME);

					//$login_tm = mktime($login_h, $login_i, $login_s, $login_month, $login_day, $login_year);	//현재시간
					$login_harutm = mktime(23,59,59, $login_month, $login_day, $login_year);					//제한시간
					//$limit_time = $login_harutm - $login_tm;													//남은시간(오늘시간 - 현재시간)

					//쿠키 제한시간(23시 59분 59초)
					$limit_time = mktime(23,59,59, $login_month, $login_day, $login_year);						//제한시간

					//$cookie_limit_time = TODAYTIME + $limit_time;
					$cookie_limit_time = $limit_time;

					//쿠키 24시간
					//$cookie_limit_time = COOKIE_TIME;
					
					if($chk_login == true){

						// Example usage
						// $key = "rewardy";
						// $valueToEncrypt = $companyno;
						// $encryptedCookie = encryptCookie($valueToEncrypt, $key);

						//회원아이디
						setcookie('user_id', $user_id, COOKIE_90DAYS , '/', C_DOMAIN);

						//회원이름
						setcookie('user_name', $user_name , COOKIE_90DAYS , '/', C_DOMAIN);

						//부서코드
						setcookie('user_part', $partno , COOKIE_90DAYS , '/', C_DOMAIN);

						//부서명
						setcookie('part_name', $partname , COOKIE_90DAYS , '/', C_DOMAIN);

						//회사코드
						setcookie('companyno', $companyno , COOKIE_90DAYS , '/', C_DOMAIN);

						//회사폴더명
						setcookie('comfolder', $comfolder , COOKIE_90DAYS , '/', C_DOMAIN);

						echo "m_";
					}else{

						// Example usage
						$key = "rewardy";
						$valueToEncrypt = $companyno;
						$encryptedCookie = encryptCookie($valueToEncrypt, $key);
						//회원아이디
						setcookie('user_id', $user_id, $cookie_limit_time , '/', C_DOMAIN);

						//회원이름
						setcookie('user_name', $user_name , $cookie_limit_time , '/', C_DOMAIN);

						//부서코드
						setcookie('user_part', $partno , $cookie_limit_time , '/', C_DOMAIN);

						//부서명
						setcookie('part_name', $partname , $cookie_limit_time , '/', C_DOMAIN);

						//회사코드
						setcookie('companyno', $companyno , $cookie_limit_time , '/', C_DOMAIN);

						//회사코드 변조
						setcookie('com_change', $encryptedCookie , $cookie_limit_time , '/', C_DOMAIN);

						//회사폴더명
						setcookie('comfolder', $comfolder , $cookie_limit_time , '/', C_DOMAIN);

						//접속 url 체크
						setcookie('url', C_DOMAIN , COOKIE_90DAYS , '/', C_DOMAIN);
					}

					//회원등급(숫자일경우만)
					if (is_numeric($highlevel) == true){
						setcookie('user_level', $highlevel , $cookie_limit_time , '/', C_DOMAIN);
					}


					//로그인 상태유지
					if ($chk_login == true){
						$secret = "user_id=".$user_id."&user_name=".$res["name"]."&user_part=".$partno."&highlevel=".$highlevel;
						$Encrypt_val = Encrypt($secret);
						setcookie('worksinput', $Encrypt_val , COOKIE_MAXTIME , '/', C_DOMAIN);
					}

					//로그인 아이디 저장 여부
					if($id_save == true){
						setcookie('id_save', $id_save , COOKIE_MAXTIME , '/', C_DOMAIN);
						//회원아이디 저장
						setcookie('cid', $user_id , COOKIE_MAXTIME , '/', C_DOMAIN);
					}else{

						setcookie('id_save', '' , COOKIE_MAXTIME , '/', C_DOMAIN);
						//회원아이디 저장
						setcookie('cid', '' , COOKIE_MAXTIME , '/', C_DOMAIN);
					}

					//출근 체크를 안한 사람
					/*$work_member_on = false;
					$sql = "select idx from work_member where state='0' and companyno='".$companyno."' and live_1='0' and live_1_regdate is NULL and email='".$user_id."'";
					$member_res = selectQuery($sql);
					if($member_res['idx']){
						$work_member_on = true;
					}*/

					//출근도장을 안찍었을 경우
					if($res["live_1_regdate"] != TODATE && $res["live_1"] != '1'){

						echo "use_check";
						exit;

					}else{

						//회원등급
						if ($highlevel >= 0){

							switch ($highlevel){
								
								//일반회원(1~5)
								case "5":
									echo "use_ok";
									break;

								//관리자(0)
								default :
									echo "ad_ok";
									break;
							}

						}else{
							echo "use_deny2";
							exit;
						}
					}
					exit;
				}
			}else{
				echo "notuser";
				exit;
			}
		}
	}

	if($mode == "login_mobile"){
		$user_id = trim($_POST['id']);
		$user_pw = trim($_POST['pwd']);
		$chk_login = trim($_POST['chk_login']);
		$device_uuid = trim($_POST['device_uuid']);
		$push_register_id = trim($_POST['push_register_id']);
		$device_platform = trim($_POST['device_platform']);
					
		//패스워드에 공백이 있는 경우
		if(preg_match("/\s/u", $user_pw) == true){
			echo "pempty";
			exit;
		}
		
		if ( !isset($user_id) || !isset($user_pw) ){
			//header("Content-Type: text/html; charset=UTF-8");
			//echo "<script>alert('아이디 또는 비밀번호가 빠졌거나 잘못된 접근입니다.');";
			//echo "window.location.replace('./login.php');</script>";
			echo "user_not";
			exit;
		} // 사용 x

		if($user_id && $user_pw){
			//아이디 비밀번호 체크
			//KISA_SHA256암호화
			$kisa_user_pw = kisa_encrypt($user_pw); // (암호화)
			$kisa_sha256_chk = true;
			$sql = "select idx, password, name, highlevel, companyno, part, partno, live_1, left(live_1_regdate, 10) as live_1_regdate from work_member where state='0' and email='".$user_id."'";
			$res = selectQuery($sql);
			if($res['idx']){
				//KISA_SHA256암호화 비밀번호 맞으면
				if($kisa_sha256_chk && $res['password'] == $kisa_user_pw ){
					$res["passwd"] = 1;
				}
				//회사코드
				$companyno = trim($res["companyno"]);

				$sql = "select idx, code from work_company where state='0' and idx='".$companyno."'";
				$company_info = selectQuery($sql);

				if($company_info['idx']){
					$comfolder = $company_info['code'];
				}

				//회원정보 없을때
				if(!$res["passwd"]){
					echo "use_deny";
					exit;
				}elseif($res["passwd"]=="1"){
					$sql = "update work_member set login_date = ".DBDATE." , login_count = login_count + 1 where companyno='".$companyno."' and email='".$user_id."'";
					$return = updateQuery($sql); 

					//세션저장:id, name, session_id

				//	$some_name = session_name("some_name"); // must exists like this 
				//	session_set_cookie_params(0, '/', '.todaywork.co.kr');
				//	session_start();
				//	$_SESSION['user_id'] = $user_id;
				//	$_SESSION['user_name'] = $res["name"];
				//	$_SESSION['session_id'] = session_id();

					//회원이름
					$user_name = $res["name"];

					//회원등급
					$highlevel = trim($res["highlevel"]);
					$highlevel = preg_replace("/[^0-9]/", "", $highlevel);
					
					//회사코드
					$companyno = preg_replace("/[^0-9]/", "", $companyno);

					//부서/팀
					$partno = trim($res["partno"]);
					$partno = preg_replace("/[^0-9]/", "", $partno);
					
					$partname = $res["part"];
					// //24시간 쿠키 설정
					// $login_year = date("Y", TODAYTIME); 
					// $login_month = date("m", TODAYTIME);
					// $login_day = date("d", TODAYTIME);
					// $login_h = date("H", TODAYTIME);
					// $login_i = date("i", TODAYTIME);
					// $login_s = date("s", TODAYTIME);

					// //$login_tm = mktime($login_h, $login_i, $login_s, $login_month, $login_day, $login_year);	//현재시간
					// $login_harutm = mktime(23,59,59, $login_month, $login_day, $login_year);					//제한시간
					// //$limit_time = $login_harutm - $login_tm;													//남은시간(오늘시간 - 현재시간)

					// //쿠키 제한시간(23시 59분 59초)
					// $limit_time = mktime(23,59,59, $login_month, $login_day, $login_year);						//제한시간

					// //$cookie_limit_time = TODAYTIME + $limit_time;
					// $cookie_limit_time = $limit_time;

					//쿠키 24시간
					//$cookie_limit_time = COOKIE_TIME;

					//회원아이디
					setcookie('user_id', $user_id, COOKIE_MAXTIME , '/', C_DOMAIN);

					//회원이름
					setcookie('user_name', $user_name , COOKIE_MAXTIME , '/', C_DOMAIN);

					//부서코드
					setcookie('user_part', $partno , COOKIE_MAXTIME , '/', C_DOMAIN);

					//부서명
					setcookie('part_name', $partname , COOKIE_MAXTIME , '/', C_DOMAIN);

					//회사코드
					setcookie('companyno', $companyno , COOKIE_MAXTIME , '/', C_DOMAIN);

					//회사폴더명
					setcookie('comfolder', $comfolder , COOKIE_MAXTIME , '/', C_DOMAIN);


					//회원등급(숫자일경우만)
					if (is_numeric($highlevel) == true){
						setcookie('user_level', $highlevel , COOKIE_MAXTIME, '/', C_DOMAIN);
					}

					// 모바일 알림앱 푸시 쿼리
			
					$sql = "select idx, state, device_uuid from push_device_info where device_uuid = '".$device_uuid."' and state = '0' order by idx desc";
					$stmt = selectQuery($sql);
					
					$sql = "select idx,email,companyno from work_member_alarm where email = '".$user_id."' and companyno = '".$companyno."' ";
					$memberQuery = selectQuery($sql);

					if(!$stmt['idx']){
						$sql = "insert into push_device_info set
								device_uuid = '".$device_uuid."',
								push_register_id = '".$push_register_id."',
								device_platform =  '".$device_platform."',
								mem_id = '".$user_id."',
								push_yn = 'Y',
								division = 0"; 
						$stmt2 = insertQuery($sql);
						if(!$memberQuery['idx']){
								$sql = " insert into work_member_alarm set
								email = '".$user_id."',
								companyno = '".$companyno."',
								state = '".$userinfo['state']."',
								todaywork_alarm = '1',
								challenges_alarm = '1',
								party_alarm = '1',
								reward_alarm = '1',
								like_alarm = '1',
								allselect_alarm = '1',
								workdate = '".TODATE."' ";
								$insert_alarm = insertQuery($sql);

								$sql = "update work_member_alarm set state = '9' where email = '".$user_id."' and companyno != '".$companyno."' ";
								$update = updateQuery($sql);
						}
					}else{
						$sql = "update push_device_info set push_register_id = '".$push_register_id."', mem_id = '".$user_id."' where device_uuid = '".$device_uuid."'";
						$stmt2 = updateQuery($sql);
					}

					//출근도장을 안찍었을 경우
					if($res["live_1_regdate"] != TODATE && $res["live_1"] != '1'){

						echo "use_check";
						exit;

					}else{

						//회원등급
						if ($highlevel >= 0){
							switch ($highlevel){
								
								//일반회원(1~5)
								case "5":
									echo "use_ok";
									break;

								//관리자(0)
								case "0" :
									echo "ad_ok";
									break;
							}

						}else{
							echo "use_deny2";
							exit;
						}
					}
					exit;
				}
			}else{
				echo "notuser";
				exit;
			}
		}
	}

	//출근도장
	if($mode == "member_work_check"){
		//아이디 비밀번호 체크
		$sql = "select idx, companyno, live_1, live_1_regdate, penalty_state, DATE_FORMAT(live_1_regdate, '%Y-%m-%d') as live_1_workdate from work_member where state='0' and companyno='".$companyno."' and email='".$user_id."' limit 1";
		$res = selectQuery($sql);
		
		if($res['idx']){
			//업무 최초 시작 처리
			if($res["live_1_workdate"] != TODATE && $res["live_1"] != '1'){
				if($res['penalty_state']>0){ // 패널티 적용 후 다음날 첫 출근시 패널티 1개 차감
					$sql = "update work_member set penalty_state = '0' where state = '0' and companyno='".$companyno."' and email='".$user_id."'";
					$penalty_cancel = updateQuery($sql);
				}

				$sql = "update work_member set live_1='1', live_1_regdate=".DBDATE." where state='0' and companyno='".$companyno."' and email='".$user_id."'";
				$up = updateQuery($sql);

			// 페널티 함수 사용
			member_penalty_add();

			//로그인 로그 저장
			//리턴값없이 저장함
			//inc_lude/func.php 설정됨
			member_login_log();

			if($up){
				echo "|work_check";
				exit;
			}
		}else{
			$sql = "update work_member set live_1='1' where state='0' and companyno='".$companyno."' and email='".$user_id."'";
			$up = updateQuery($sql);
			
			echo "|||||today_check";
			exit;
		}
	}
}
	// 비밀번호 재설정 전 확인프로세스
	if($mode == "new_password"){
		$pw = $_POST['pw'];
		$user_email = $_POST['user_id'];

		if(preg_match("/\s/u", $pw) == true){
			echo "|pempty";
			exit;
		}

		$kisa_user_pw = kisa_encrypt($pw); // (암호화)
		$kisa_sha256_chk = true;
		$sql = "select idx, password, email from work_member where state='0' and email='".$user_email."'";
		$res = selectQuery($sql);

		if($res['idx']){
			if($kisa_sha256_chk && $res['password'] == $kisa_user_pw ){
				$res["passwd"] = 1;
			}

			if($res["passwd"]!=1){
				echo "|not_password";
				exit;
			}else if($res["passwd"]==1){?>
				<div class="tl_deam"></div>
				<div class="tl_in">
					<div class="tl_close">
						<button><span>닫기</span></button>
					</div>
					<div class="tl_login_logo">
						<span>리워디</span>
					</div>
					<div class="tl_tit">
						<strong>비밀번호 재설정</strong>
						<span>새로 변경할 비밀번호를 입력해주세요.</span>
					</div>
					<div class="tl_list">
						<ul>
							<li>
								<div class="tc_input">
									<input type="password" id="new_passwd" name="user_id" class="input_001" placeholder="새로 사용할 비밀번호" value="">
									<label for="new_passwd" class="label_001">
										<strong class="label_tit">새로 사용할 비밀번호를 입력하세요</strong>
									</label>
									<input type="password" id="enter_passwd" name="user_id" class="input_001" placeholder="비밀번호 확인" value="">
									<label for="enter_passwd" class="label_001">
										<strong class="label_tit">비밀번호 확인</strong>
									</label>
									<input type="hidden" id="user_email" value="<?=$user_email?>">
								</div>
							</li>
						</ul>
					</div>
					<div class="tl_btn">
						<button id="enter_new_pw">비밀번호 재설정</button>
					</div>
				</div>	
			<? 	
				echo "|success";
				exit;
			}
		}
	}
	
	// 새 비밀번호 재설정
	if($mode == "enter_password"){
		$pw = $_POST['pw'];
		$user_email = $_POST['user_id'];

		$kisa_user_pw = kisa_encrypt($pw); //암호화

		$sql = "select idx, email from work_member where state = '0' and email = '".$user_email."'";
		$query = selectQuery($sql);

		if($query['idx']){
			$sql = "update work_member set password = '".$kisa_user_pw."' where idx = '".$query['idx']."' ";
			$update = updateQuery($sql); 
			if($update){?>
				<div class="tl_deam"></div>
				<div class="tl_in">	
					<div class="tl_close">
						<button><span>닫기</span></button>
					</div>
					<div class="tl_login_logo">
						<span>리워디</span>
					</div>
					<div class="tl_tit">
						<strong>비밀번호 재설정</strong>
						<span>비밀번호를 변경할 수 있습니다.<br>
						현재 사용중인 비밀번호를 입력해주세요.</span>
					</div>
					<div class="tl_list">
						<ul>
							<li>
								<div class="tc_input">
									<input type="password" id="ori_passwd" name="user_id" class="input_001" placeholder="현재 사용중인 비밀번호" value="">
									<label for="ori_passwd" class="label_001">
										<strong class="label_tit">현재 비밀번호를 입력해주세요</strong>
									</label>
									<input type="hidden" id="user_email" value="<?=$user_id?>">
								</div>
							</li>
						</ul>
					</div>
					<div class="tl_btn">
						<button id="create_new_pw">새 비밀번호 입력</button>
					</div>
				</div>
				<?echo "|success";
			}	
			exit;
		}else{
			echo "|not_user";
			exit;
		}
	}
?>