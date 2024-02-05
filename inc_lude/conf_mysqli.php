<?php

//	$ie = isset($_SERVER['HTTP_USER_AGENT']) && (strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE') !== false || strpos($_SERVER['HTTP_USER_AGENT'], 'Trident') !== false);
//	if($ie){
//		header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
//		header('Pragma: public');
//	}else{
//		header("Content-Type: text/html; charset=utf8");
		header("Cache-Control:max-age=86400, must-revalidate");
		header("Pragma:no-cache");
//}

	//페이지 시작시간
	$start_time = array_sum(explode(' ', microtime()));

	//오류메세지 화면표시 : 1 , 표시안함 : 0
	if (!ini_get('display_errors')){
		ini_set('display_errors', 0);
	}

	//오류메세지 표시
	if (!ini_get('display_startup_errors')){
		ini_set('display_startup_errors', 0);
	}



	//오류메세지 : E_NOTICE 제외하고 모든오류표시(E_WARNING, E_ERROR)
	//오류메세지 : E_NOTICE 제외하고 WARNING 제외하고 모든오류표시(E_WARNING, E_ERROR)
	error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING);
	//rror_reporting(E_ALL);

	//	ini_set("error_log", dirname(__DIR__)."/log/error_log.txt");
	//	echo "log ==> ". dirname(__DIR__)."/log/error_log.txt";

	//ini_set으로 error_log 경로 변경 설정변경
	//log 경로 : D:\inbee\todaywork\user\log\error_log_년도월일.txt

	//echo $home_dir."log\\error_log_".date("Ymd", time()).".txt";
	ini_set("error_log", $home_dir."log\\error_log_".date("Ymd", time()).".txt");


//	error_log("에러가 발생","3",error_log);

	/*	기본경로
	\\10.17.239.71\storageNAS\inbee\todaywork
	*/

	//아이피체크
	if(isset($_SERVER['HTTP_X_FORWARDED_FOR'])){

		if($_SERVER['HTTP_X_FORWARDED_FOR']){
			$common_server_ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
		}else{
			$common_server_ip = $_SERVER['REMOTE_ADDR'];
		}

		if($common_server_ip && strpos($common_server_ip,',') > 0){
			$common_server_ip = trim($common_server_ip);
			$split_server_ip = explode(',',$common_server_ip);

			if(is_array($split_server_ip)){
				if(!empty($split_server_ip[0])){
					$common_server_ip = $split_server_ip[0];
				}
			}
			$change_ip = trim($common_server_ip);
		}else{
			$change_ip = $common_server_ip;
		}

		//X_FORWARDED 체크하여 홈주소 확인
		/*if($_SERVER['HTTP_X_FORWARDED_PROTO'] == "https"){
			$home_url = $_SERVER['HTTP_X_FORWARDED_PROTO'] ."://".$_SERVER['HTTP_HOST'];
		}else{
			$home_url = "https://".$_SERVER['HTTP_HOST'];
		}*/
		
		$home_url = "https://".$_SERVER['HTTP_HOST'];

	} else {
		$change_ip = "59.19.241.15";
	}



	//DB연결정보
	define( "DBCON" , __DIR__ . "/dbcon.php");

	//DB연결정보-mysqli
	define( "DBCON_MYSQLI" , __DIR__ . "/dbcon_mysqli.php");

	//도메인
	define( "T_DOMAIN" , "officeworker.co.kr");

	//함수목록
	define( "FUNC" , __DIR__ . "/func.php");

	//함수목록_mysqli
	define( "FUNC_MYSQLI" , __DIR__ . "/func_mysqli.php");

	//내아이피
	define( "LIP" , $change_ip);

	//사무실아이피1
	define( "OIP" , '59.19.241.15');

	//사무실아이피2
	define( "OIP2" , '59.28.79.123');


	//웹서버 아이피
	define( "WEBIP1" , '14.63.170.139');

	//버전갱신
	define( "VER" , "?v=".date('YmdHis', time()));

	//쿠키기간
	define( "COOKIE_DAY" , 1);

	//쿠키시간
	define( "COOKIE_TIME" , time() + (86400 *  COOKIE_DAY ));

	//쿠키 365일
	define( "COOKIE_MAXTIME" , time() + (86400 * 365 ));

	//쿠키 90일
	define( "COOKIE_90DAYS" , time() + (86400 * 90 ));

	//현재시간
	define( "TODAYTIME" , time() );

	//오늘날짜
	define( "TODATE" , date("Y-m-d" , time()) );

	define( "TODATE2" , '2022-03-22');

	//하루전날
	//define( "YESTERDAY", date('Y-m-d', strtotime(" -1 day")));

	//오늘업무 반복설정 기간
	define( "repeatday" , 365 );

	//파일업로드 최대용량 30M
	define( "MAX_FILE_SIZE" , 50);

	//NAS홈디렉토리
	define( "NAS_HOME_DIR" , "/home/todaywork/rewardyNAS/demo/");

	//파일경로
	//스토리지나스경로 : \\10.17.239.71\storageNAS\todaywork\data
	define( "NAS_DATA", "/home/todaywork/rewardyNAS/user/data");

	//이미지 서브도메인 연결
	//define( "IMG_URL", "http://img.officeworker.co.kr");

	//회사폴더명
	//define("COMFOLDER", "rewardy_");

	//요일
	$week = array("일","월","화","수","목","금","토");
	$weeks = array("일요일","월요일","화요일","수요일","목요일","금요일","토요일");
	$weekday = $week[date('w' , strtotime(TODATE))];
	$weekday_num = date('w' , strtotime(TODATE));


	if($_COOKIE['url'] == "rewardy.co.kr"){
		alert("이건에바지");
	}

	var_dump($_COOKIE['url']);
	//쿠키도메인
	if($_SERVER['HTTP_HOST']){
		// $C_DOMAIN = str_replace("www", "", $_SERVER['HTTP_HOST']);
		// define("C_DOMAIN", $C_DOMAIN);

		define("C_DOMAIN", "demo.rewardy.co.kr");
	}else{
		echo "not domain";
		exit;
	}


	//정시출근시간
	define( "ATTEND_TIME" , "09:30");

	//업무시간
	define( "ATTEND_STIME" , "09");

	//퇴근시간
	define( "ATTEND_ETIME" , "23");

	//지각 횟수
	define( "ATTEND_CNT" , "2");


	//파티영역(원활, 지연 기준설정)
	//원활:7일, 지연:14일
	$party_delay['1'] = 7;
	$party_delay['2'] = 8;
	$party_delay['3'] = 13;
	$party_delay['4'] = 14;


	//PHP 현재시간 : yyyy-mm-dd hh:mi:ss.mmm
	if($_SERVER['REQUEST_TIME_FLOAT']){
		$time_float = explode(".", $_SERVER['REQUEST_TIME_FLOAT']);
		$login_date = date("Y-m-d H:i:s", $_SERVER['REQUEST_TIME_FLOAT']) . ".". substr( $time_float[1],0, 3);
		if($login_date){
			define( "PHPDATE" , $login_date);
		}

		/*
		$micro_date = microtime();
		$date_array = explode(" ",$micro_date);
		$date = date("Y-m-d H:i:s",$date_array[1]);
		echo $date . $date_array[0]."<br>";
		*/
	}

	//MSSQL 현재시간 ODBC 표준 : yyyy-mm-dd hh:mi:ss.mmm
	define( "DBDATE","now()");

	//모바일구분 : 1(모바일), 0(사이트)
	$mAgent = array ("iPhone", "iPod", "BlackBerry", "Android", "Windows CE", "Windows CE;", "LG", "MOT", "SAMSUNG", "Sony", "Mobile", "Symbian", "Opera Mobi", "Opera Mini", "IEmobile","Nokia","PalmOS","SymbianOS","LGTelecom","SKT","Phone","SonyEricsson","webos","SamsungBrowser");

	//사이트
	$chkMobile = '0';
	for($i=0; $i<sizeof($mAgent); $i++){
		if(stripos($_SERVER['HTTP_USER_AGENT'], $mAgent[$i])){
			//모바일
			$chkMobile = '1';
			break;
		}
	}



	$goal_arr = array("3"=>"목표" , "31"=>"일일목표", "32"=>"주간목표", "33"=>"성과목표");

	//챌린지 참여 보상, //챌린지 참여 삭제 , //챌린지 참여 보상으로 차감
	$ch_code = array("0"=>"500", "1"=>"510","2"=>"520");

	//리워디 코드 내역
	$reward_code_arr = array("500"=>"보상", "501"=>"참여취소", "510"=>"보상", "520"=>"차감", "600"=>"보상", "620"=>"차감");
	$reward_type = array("0"=>"todaywork", "1"=>"live", "2"=>"challenge", "3"=>"team","4"=>"reward","5"=>"admin","6"=>"about","7"=>"function","8"=>"customer","9"=>"party","10"=>"insight", "11"=>"alarm", "12"=>"itemshop", "13"=>"choco");

	//서비스 타이틀(좋아요 상세보기 카테고리)
	$service_title_arr = array("live"=>"라이브", "work"=>"오늘업무","challenges"=>"챌린지", "memo"=>"메모", "main"=>"메인", "party"=>"파티");

	//역량지표 계산
	$max_array = array("1"=>"94", "2"=>"88", "3"=>"116", "4"=>"171", "5"=>"110", "6"=>"149");

	// 2/3 낮춤
	//$max_array = array("1"=>"47", "2"=>"44", "3"=>"58", "4"=>"86", "5"=>"73", "6"=>"75");


	//이미지타입
	$image_type_array = array("image/jpeg","image/png","image/gif");


	//파일 허용확장자
	$file_allowed_ext = array("jpg","jpeg","png","gif","pdf","doc","xls","xlsx","xlsm","hwp","pptx","ppt","pptm","zip","tar","tgz","alz","txt","docx","mp4","mp3");


	$img_file_allowed_ext = array("jpg","jpeg","png","gif");



	//챌린지 파일업로드 경로
	$file_save_dir = "data/challenges/files";

	//챌린지 파일다운로드 경로
	$file_save_dir_multidownload = "data/challenges/multi_download";


	//업로드경로 실서버경로
	//$dir_file_path = "\\\\10.17.239.71\storageNAS\inbee\\todaywork\user";
	//$dir_file_path = "\\\\10.17.239.71\storageNAS\\todaywork";
	$dir_file_path = "/home/todaywork/rewardyNAS/user";

	//스토리지나스경로
	$dir_file_Storage_path = "/home/todaywork/rewardyStorageNAS/log";

	//프로필 사진이미지 경로
	$dir_file_profile_path = "/home/todaywork/rewardyNAS/user";

	//업로드경로 로컬경로
	//$dir_file_path = "\\\\biz_nas\inbee_Storage\Bizforms\\todaywork";

	//챌린지 파일업로드 경로
	$file_save_dir_img = "data/challenges/img";

	//챌린지 파일업로드 경로 - 원본이미지
	$file_save_dir_img_ori = "data/challenges/img_ori";

	//회사 로고 이미지 업로드 경로
	$profile_save_dir_img = "data/logo/img";

	//회사 로고 이미지 업로드 경로
	$profile_save_dir_img_ori = "data/logo/img_ori";

	//프로필 파일업로드 경로
	$profile_save_dir_img = "data/profile/img";

	//프로필 파일업로드 경로
	$profile_save_dir_img_ori = "data/profile/img_ori";

	//페널티 이미지 업로드 경로
	$penalty_save_dir_img = "data/penalty/img";

	//프로필 파일업로드 경로
	$penalty_save_dir_img_ori = "data/penalty/img_ori";

	//오늘업무 첨부파일 업로드 경로
	$work_save_dir = "data/work/files";

	//오늘업무 이미지파일 업로드 경로
	$work_save_dir_img = "data/work/img";

	//오늘업무 이미지파일 업로드 경로
	$work_save_dir_img_ori = "data/work/img_ori";


	//getcwd(); //현재 디렉토리명
	//오늘날짜
	$today = date("Y-m-d");

	//년도
	$dir_year = date("Y", TODAYTIME);

	//월
	$dir_month = date("m", TODAYTIME);


	//기간설정 매월 1일 ~ 월 말일까지(30일, 31일)
	$curYear = (int)date('Y');
	$curMonth = (int)date('m');
	$month_first_day = date("Y-m-d", mktime(0, 0, 0, $curMonth , 1, $curYear));
	$month_last_day = date("Y-m-d", mktime(0, 0, 0, $curMonth+1 , 0, $curYear));

	//년월일시분
	$goto_year = date("Y", TODAYTIME);
	$goto_month = date("m", TODAYTIME);
	$goto_day =  date("d", TODAYTIME);
	$goto_gg = date("g", TODAYTIME);
	$goto_hh = date("H", TODAYTIME);
	$goto_ii = date("i", TODAYTIME);

	if($goto_hh > 12){
		$get_time_text = "오후";
	}else{
		$get_time_text = "오전";
	}


	//발신자이메일주소
	$send_email = "manager@rewardy.co.kr";

	//smtp 메일계정
	$smtp_email = "devmaster@bizforms.co.kr";



	// session_name('테스트세션');
    // session_start(); //세션시작

	//	print_r($_SERVER['HTTP_HOST']);
	/*
	ini_set("session.cookie_domain",".bizforms.co.kr");

	session_cache_limiter('no-cache, must-revalidate');
	session_set_cookie_params(0,"/",".bizforms.co.kr");
	session_start();
	*/
	//print_r($_COOKIE);

	/*
	//아이피 차단(외부에서 접근 제한 합니다)
	if( LIP != OIP && LIP != WEBIP1){
		if(LIP == '118.235.42.30' || LIP == '183.103.64.216' || LIP == '59.28.79.123'){
		}else{
			header("location:https://".$_SERVER["HTTP_HOST"]."/404.php");
			exit;
		}
	}
	*/

	$user_id = $user_id ?? NULL;

	//쿠키값 저장
		if($_COOKIE){
			$user_id = $_COOKIE['user_id'];
			$user_name = $_COOKIE['user_name'];
			$user_level = $_COOKIE['user_level'];
			$user_part = $_COOKIE['user_part'];

			if(isset($_COOKIE['user_coin'])){
				$user_coin = $_COOKIE['user_coin'];
			}


			
			
			//회사코드 쿠키 변조값 작업	
			$key = "rewardy";
			$encryptedCookie = $_COOKIE["com_change"] ?? "";

			
			$cipher = "AES-256-CBC";
		
			$encryptedValue = base64_decode($encryptedCookie);
			
			$iv_length = openssl_cipher_iv_length($cipher);
			$iv = substr($encryptedValue, 0, $iv_length);
			
			$encryptedData = substr($encryptedValue, $iv_length);
			
			$decryptedValue = openssl_decrypt($encryptedData, $cipher, $key, 0, $iv);
			if($_COOKIE['com_change']){
				$companyno = $decryptedValue;
			}else{
				$companyno = $_COOKIE['companyno'];
			}
			$comfolder = $_COOKIE['comfolder'];
			$part_name = $_COOKIE['part_name'];
		}



	//일반등급 : 1:관리, 5:일반
	$grade_arr = array("manager"=>"1", "normal"=>"5");


	//템플릿 생성 권한
	//관리자권한: work_member 테이블 필드 : highlevel=1
	$template_auth = false;
	if($user_level == '1' || $user_id == 'sun@bizforms.co.kr' || $user_id == 'earkite.n@gmail.com'){
		$template_auth = true;
	}

	//템플릿 사용하기 권한
	$template_use_auth = false;
	if($user_level == '0'){
		$template_use_auth = true;
	}


	//수정권한 아이디
	$edit_user_arr = array('audtjs2282@naver.com','sadary0@nate.com','earkite.n@gmail.com','mungangsan@gmail.com');



	//HTTP 접속하였을때 HTTPS로 이동
	if(isset($_SERVER["HTTP_X_FORWARDED_PROTO"])){



		if ($_SERVER["HTTP_X_FORWARDED_PROTO"] == "http"){



			//스케줄러 실행을 하기 위해 예외처리함
			//스케줄러 URL : http://rewardy.co.kr/inc/live_10_update.php?mode=lives_10
			if($_SERVER['QUERY_STRING'] == 'mode=lives_10'){

			}else{

				//모든 URL은 HTTPS로 접속 처리
				if ( strstr( $_SERVER['HTTP_HOST'] , "todaywork.co.kr") ){
					header("Location: https://www.rewardy.co.kr");
				}else{



					header("Location: https://".$_SERVER["HTTP_HOST"]);
				}
			}
		}
	}

	if($user_id=='sadary0@nate.com'){

		//echo "###";
		//print "<pre>";
		//print_r($_SERVER);
		//print "</pre>";
		//phpinfo();
		$headers = apache_request_headers();

		foreach ($headers as $header => $value) {
		//	echo "$header: $value <br />\n";
		}

		//getenv('HTTP_X_FORWARDED_FOR');
		//getenv('HTTP_CLIENT_IP');

		//echo ">>>". $headers['X-Forwarded-Proto']."<<<";
		if($headers['X-Forwarded-Proto'] == 'https'){
			//header("Location: https://".$_SERVER["HTTP_HOST"]);
			//header("Location: https://".$_SERVER["HTTP_HOST"]);
			//echo "https://".$_SERVER["HTTP_HOST"];
			//header("Location: https://https://rewardy.co.kr/");
		}
		//exit;

		if($_SERVER['HTTP_SEC_FETCH_SITE'] || $_SERVER['HTTP_SEC_FETCH_MODE'] || $_SERVER['HTTP_SEC_FETCH_USER'] || $_SERVER['HTTP_SEC_FETCH_DEST']){
			//echo "https";
			//header("Location: https://https://rewardy.co.kr/");
			//header("Location: https://rewardy.co.kr");
			//exit;
		}else{
			//echo "http";
			//header("Location: http://https://rewardy.co.kr/");
			//header("Location: http://rewardy.co.kr");
			//exit;
		}
	}
	//47446
	//42445


?>