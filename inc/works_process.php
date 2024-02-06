<?php

$home_dir = str_replace( basename(__DIR__) , "" , __DIR__ );
include $home_dir . "inc_lude/conf_mysqli.php";
include DBCON_MYSQLI;
include FUNC_MYSQLI;
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
	$part_name = $_COOKIE['part_name'];
}


//이전날, 다음날
if($mode == "wdate_check"){

	$wdate = $_POST['wdate'];
	$day_type = $_POST['day_type'];
	$works_type = $_POST['works_type'];

	//일일
	if($works_type == "day"){

		if($wdate){

			if(strpos($wdate, ".") !== false) {

				$replace_wdate = str_replace(".", "-", $wdate);
				$time_wdate = strtotime($replace_wdate);

				if($day_type == "next"){

					$ch_date = date("Y.m.d", strtotime("+1 day", $time_wdate));

				}else if($day_type == "prev"){

					$ch_date = date("Y.m.d",strtotime("-1 day", $time_wdate));
				}

				echo $ch_date;
			}
		}

	//주간
	}else if($works_type == "week"){

		/*print "<pre>";
		print_r($_POST);
		print "</pre>";*/

		if($wdate){

			if(strpos($wdate, " ") !== false) {
				$wdate = str_replace(" ", "", $wdate);
				$tmp = explode("~", $wdate);
				$date1 = $tmp[0];
				$date2 = $tmp[1];
				$date1 = str_replace(".", "-", $date1);
				$date2 = str_replace(".", "-", $date2);

				if($day_type == "prev"){

					$date1 = strtotime($date1);
					$date1 = date("Y-m-d", strtotime("-7 day", $date1));

					$ret = week_day("$date1");


					if(strpos($ret['month'], "-") !== false) {
						$ret['month'] = str_replace("-", ".", $ret['month']);
					}

					if(strpos($ret['sunday'], "-") !== false) {
						$ret['sunday'] = str_replace("-", ".", $ret['sunday']);
					}

					$result = $ret['month'] . " ~ " .$ret['sunday'];
					echo $result;


				}else if($day_type == "next"){

					$date1 = strtotime($date1);
					$date1 = date("Y-m-d", strtotime("+7 day", $date1));
					$ret = week_day("$date1");

					if(strpos($ret['month'], "-") !== false) {
						$ret['month'] = str_replace("-", ".", $ret['month']);
					}

					if(strpos($ret['sunday'], "-") !== false) {
						$ret['sunday'] = str_replace("-", ".", $ret['sunday']);
					}

					$result = $ret['month'] . " ~ " .$ret['sunday'];
					echo $result;
				}
			}
		}
	//월간
	}else if($works_type == "month"){

		if($day_type == "prev"){
			$wdate = str_replace(".", "-", $wdate .".01");
			$wdate = strtotime($wdate);
			echo date("Y.m", strtotime("-1 month", $wdate));

		}else if($day_type == "next"){
			$wdate = str_replace(".", "-", $wdate .".01");
			$wdate = strtotime($wdate);
			echo date("Y.m", strtotime("+1 month", $wdate));

		}

	}
	exit;
}



//오늘업무 작성완료
if($mode == "works_write"){

	$name = $user_name;					//회원이름
	$user_level = $user_level;			//회원레벨

	$part_flag = $user_part;			//부서/팀별(1:경영지원팀, 2:운영기획팀, 3:고객지원팀, 4:마케팅팀, 5:콘텐츠팀, 6:디자인팀, 7:개발팀, 8:기타)
	$work_flag = $_POST['work_flag'];	//업무구분(0:기본, 2:업무예약, 3:업무요청)
	$decide_flag = $_POST['decide_flag'];
	$start_time = $_POST['start_time'];
	$end_time = $_POST['end_time'];
	

	
	$workdate = $_POST['workdate'];
	$work_title = $_POST['work_title'];
	$work_contents = $_POST['work_contents'];

	
	//비밀글 상태 
	$secret_flag = $_POST['secret_flag'];

	//참여자 일부(선택한사람)
	$work_user_chk = $_POST['work_user_chk'];

	//공유업무
	$share_flag = $_POST['share_flag'];
	$share_flag = preg_replace("/[^0-9]/", "", $share_flag);

	//파티 idx
	$be_party_idx = $_POST['be_party_idx'];
	$be_party_arr = explode(",",$be_party_idx);
	$be_party_cnt = count($be_party_arr);

	if($work_flag == '1'){
		$table = 'report';
	}else if($work_flag == '3'){
		$table = 'user';
	}else if($share_flag == '1'){
		$table = 'share';
	}
	//날짜가 없을때 오늘날짜로 등록
	if(!$_POST['workdate']){
		$workdate = $today;
	}

	//로그인 체크
	if(!$user_id){
		echo "logout|";
		exit;
	}

	//보고업무
	$res_report_insert = false;

	//요청업무
	$res_req_insert = false;

	//공유업무
	$res_share_insert = false;


	//첨부파일이 있는경우 최대용량 30M 체크
	//MAX_FILE_SIZE : inc_lude/conf.php 설정됨
	if ($_FILES){

		//업로드 제한 확장자
		$format_ext = array('asp', 'php', 'jsp', 'xml', 'html', 'htm', 'aspx', 'exe', 'exec', 'java', 'js', 'class', 'as', 'pl', 'mm', 'o', 'c', 'h', 'm', 'cc', 'cpp', 'hpp', 'cxx', 'hxx', 'lib', 'lbr', 'ini', 'py', 'pyc', 'pyo', 'bak', '$$$', 'swp', 'sym', 'sys', 'cfg', 'chk', 'log', 'lo');
		for($i=0; $i<count($_FILES['files']['name']); $i++){
			$max_file_size = MAX_FILE_SIZE * 1024 * 1024;
			$files_for_size = $_FILES['files']['size'][$i];
			$files_for_name = $_FILES['files']['name'][$i];

			if($files_for_size > $max_file_size){
				echo "files_size_over|";
				exit;
			}

			$ext = @end(explode('.', $files_for_name));
			if(in_array($ext, $format_ext)){
				echo "files_format";
				exit;
			}
		}
	}

	//부서별정보
	$sql = "select idx, email, name, part, partno, highlevel from work_member where state='0' and companyno='".$companyno."' and email='".$user_id."'";
	$mem_info = selectQuery($sql);
	if ($mem_info['idx']){
		$mem_info_part = $mem_info['part'];
		$mem_info_partno = $mem_info['partno'];
		$mem_info_highlevel = $mem_info['highlevel'];
	}

	//$companyno 쿠키값
	if($work_user_chk){
		$work_mem_idx = trim($work_user_chk);
		$sql = "select idx, email, name, part, partno, highlevel from work_member where state='0' and companyno='".$companyno."' and idx in (".$work_mem_idx.")";
		$work_mem_info = selectAllQuery($sql);

		if($work_mem_info['idx']){

			//부서명
			$work_mem_part = @array_combine($work_mem_info['email'] , $work_mem_info['part']);

			//부서번호
			$work_mem_partno = @array_combine($work_mem_info['email'] , $work_mem_info['partno']);

			//회원레벨
			$work_mem_highlevel = @array_combine($work_mem_info['email'] , $work_mem_info['highlevel']);

			//부서명
			$mem_part = $work_mem_part[$user_id];

			//부서번호
			$mem_partno = $work_mem_partno[$user_id];

			$mem_highlevel = $work_mem_highlevel[$user_id];
		}
	}


	//보고업무
	if($work_flag == "1"){

		//보고제목
		$work_title = replace_text($work_title);

		//보고내용
		$work_contents = replace_text($work_contents);

		//$contents = nl2br($work_contents);
		$contents = $work_contents;

		//홑따옴표 때문에 아래와 같이 처리
		$contents = replace_text($contents);

	}else{

		$work_title = null;
		$contents = $_POST['contents'];

		//홑따옴표 때문에 아래와 같이 처리
		$contents = replace_text($contents);
	}



	//첨부파일 체크
	if($_FILES){
		$file_flag = "1";
	}else{
		$file_flag = "0";
	}


	//튜토리얼일 경우
	$strstr = strstr( $_SERVER['HTTP_REFERER'], 'tutorial.php');
	if($strstr == 'tutorial.php'){

		//업무구분(나의업무:2, 보고업무:1, 요청업무:3, 공유업무:2 && share_flag=1)
		if($today != $workdate){

			//나의업무
			if($work_flag=='2'){
				echo "complete|mywork|".$workdate;
	

			//요청업무
			}else if($work_flag=='3'){

				echo "complete|reqwork|".$workdate."|";

			//보고업무
			}else if($work_flag=='1'){
				echo "complete|report||";
			}


		}else{

			//나의업무
			if($work_flag=='2'){

				echo "complete|mywork|";

			//요청업무
			}else if($work_flag=='3'){

				echo "complete|reqwork||";

			//보고업무
			}else if($work_flag=='1'){
				echo "complete|report||";
			}
		}
		exit;
	}


	//업무등록
	$sql = "insert into work_todaywork(companyno, email, name, highlevel, type_flag, work_flag, part_flag, part, title, decide_flag, work_stime , work_etime, secret_flag, file_flag, contents, workdate, ip)";
	if($start_time == null || $end_time == null){
		$sql = $sql .=" values('".$companyno."', '".$user_id."','".$name."','".$mem_info_highlevel."','".$type_flag."','".$work_flag."','".$mem_info_partno."','".$mem_info_part."', '".$work_title."', '".$decide_flag."',  NULL , NULL , '".$secret_flag."' , '".$file_flag."', '".$contents."','".$workdate."','".LIP."')";
	}else{
		$sql = $sql .=" values('".$companyno."', '".$user_id."','".$name."','".$mem_info_highlevel."','".$type_flag."','".$work_flag."','".$mem_info_partno."','".$mem_info_part."', '".$work_title."', '".$decide_flag."',  '".$start_time."' , '".$end_time."' , '".$secret_flag."' , '".$file_flag."', '".$contents."','".$workdate."','".LIP."')";
	}
	// $sql .= impode(",", $valueSettings);
	$res_idx = insertIdxQuery($sql);
	if($res_idx){
	//파일이 있을경우
		if($_FILES['files']){
			//$res_idx = 1;
			//파일정보, 서비스명, 번호
			$upload_result = work_upload_files($_FILES, "work", $res_idx);
			if($upload_result){

				for($i=0; $i<count($upload_result['result']); $i++){
					$upload_res = $upload_result['result'][$i];
					$upload_num = $upload_result['num'][$i];
					$upload_format = $upload_result['format'][$i];
					$upload_work_idx = $upload_result['work_idx'][$i];
					$upload_file_path = $upload_result['file_path'][$i];
					$upload_file_name = $upload_result['file_name'][$i];
					$upload_file_real_name = addslashes($upload_result['file_real_name'][$i]);
					$upload_file_type = $upload_result['file_type'][$i];
					$upload_file_size = $upload_result['file_size'][$i];
					$upload_file_width = $upload_result['file_width'][$i];
					$upload_file_height = $upload_result['file_height'][$i];

					$sql = "select idx from work_filesinfo_todaywork where state='0' and companyno='".$companyno."' and num='".$upload_num."' and work_idx='".$upload_work_idx."' and email='".$user_id."'";
					$work_filesinfo = selectQuery($sql);
					if(!$work_filesinfo['idx']){

						//파일형식:file, 이미지형식:img
						if($upload_format == "file"){

							$upload_file_query = ",file_format";
							$upload_file_value = ",'".$upload_format."'";

						}else if($upload_format == "img"){

							$upload_file_query = ",file_format,file_width,file_height";
							$upload_file_value = ",'".$upload_format."','".$upload_file_width."','".$upload_file_height."'";

						}

						$sql = "insert into work_filesinfo_todaywork(work_idx,num,companyno,email,file_path,file_name,file_real_name,file_size,file_type".$upload_file_query.",type_flag,ip,workdate)";
						$sql = $sql .= " values('".$upload_work_idx."','".$upload_num."','".$companyno."','".$user_id."','".$upload_file_path."','".$upload_file_name."','".$upload_file_real_name."','".$upload_file_size."','".$upload_file_type."'".$upload_file_value.",'".$type_flag."','".LIP."','".$workdate."')";
						insertIdxQuery($sql);

					}
				}
			}
		}

		//업무요청, 업무보고, 공유업무
		if ($work_mem_info['idx']){

			//요청 받는사람수
			$work_req_cnt = count($work_mem_info['idx']);

			//요청 받는사람이 1명보다 큰경우
			if($work_req_cnt > 1){
				$work_req_change_cnt = $work_req_cnt - 1;

				//요청업무
				if($work_flag == "3"){
					//타임라인(요청함)
					work_data_multi_log('0','7', $res_idx, $user_id, $user_name, $work_mem_info['email'][0], $work_mem_info['name'][0], $work_req_change_cnt);
				}

				//보고업무
				if($work_flag == "1"){
					//타임라인(보고함)
					work_data_multi_log('0','23', $res_idx, $user_id, $user_name, $work_mem_info['email'][0], $work_mem_info['name'][0], $work_req_change_cnt);
				}

				if($share_flag=="1"){
					//타임라인(공유함)
					work_data_multi_log('0','4', $res_idx, $user_id, $user_name, $work_mem_info['email'][0], $work_mem_info['name'][0], $work_req_change_cnt);
				}
			}

			// 업무 등록
			$insertQuery = "INSERT INTO work_todaywork(companyno, email, name, highlevel, type_flag, work_flag, share_flag, part_flag, secret_flag, part, work_idx, title, contents, workdate, ip) 
							VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

			$insertStmt = $conn->prepare($insertQuery);

			// 공유업무
			$insertServeQuery = "INSERT INTO work_todaywork_{$table}(companyno, work_idx, work_email, work_name, email, name, workdate, ip) 
							VALUES (?, ?, ?, ?, ?, ?, ?, ?)";

			$insertServeStmt = $conn->prepare($insertServeQuery);

			// 타임라인 작업

			$insertDatalogQuery = "INSERT INTO work_data_log(state,code,work_idx,link_idx,companyno,email,name,send_email,send_name,coin,memo,type_flag,ip,workdate) 
							VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

			$insertDatalogStmt = $conn->prepare($insertDatalogQuery);

			// 실시간 업무 작업
			$insertRealtimeQuery = "INSERT INTO work_todaywork_realtime(companyno, email, name, work_flag, kind_flag, work_cnt, workdate) 
							VALUES (?, ?, ?, ?, ?, ?, ?)";

			$insertRealtimeStmt = $conn->prepare($insertRealtimeQuery);

			for ($i = 0; $i < count($work_mem_info['idx']); $i++) {
				$work_mem_email = $work_mem_info['email'][$i];
				$work_mem_name = $work_mem_info['name'][$i];
				$mem_part = $work_mem_part[$work_mem_email];
				$mem_partno = $work_mem_partno[$work_mem_email];
				$mem_highlevel = $work_mem_highlevel[$work_mem_email];
				
				$share_send_flag = ($share_flag == '1') ? '2' : '0';
		
				if($work_flag == "1"){
					$work_title_token = $name."님의 업무 보고";
					$codeNum = '22';
					$codeSend = '23';
					$realState = 'report';
				}elseif($work_flag == "3"){
					$work_title_token = $name."님의 업무 요청";
					$codeNum = '6';
					$codeSend = '7';
					$realState = 'req';
				}else{
					$work_title_token = $name."님의 업무 공유";
					$codeNum = '3';
					$codeSend = '4';
					$realState = 'share';
				}

				
				$insertData = array(
					$companyno,$work_mem_email,$work_mem_name,$mem_highlevel,
					$type_flag,$work_flag,$share_send_flag,$mem_partno,$secret_flag,$mem_part,
					$res_idx,$work_title,$contents,$workdate,LIP 
				);
				
				// bind_param : i-> '정수', s -> '문자열'/
				// 앞의 ...은 splat operator -> 함수 또는 메서드에 대한 개별 인수로 배열의 요소를 압축 해제하는 데 사용.
				$insertStmt->bind_param("issssssssssssss", ...$insertData); 
				$insertStmt->execute();

				
				$sql = "select idx from work_todaywork_{$table} where state='0' and companyno='$companyno' and work_idx='$res_idx' and email='$work_mem_email'";
				$work_row = selectQuery($sql);
				if(!$work_row['idx']){
					$serveData = array(
						$companyno,$res_idx,$user_id,$user_name,$work_mem_email,
						$work_mem_name,$workdate,LIP 
					);
				}
				$insertServeStmt->bind_param("isssssss", ...$serveData); 
				$insertServeStmt->execute();

				$sql = "select idx, memo from work_data_code where state='0' and idx='".$codeNum."'";
				$info = selectQuery($sql);
				$memo = $info['memo'];
				// 로그데이터 수집
				$logData = array(
					'0',$codeNum,$res_idx,'0',$companyno,$work_mem_email,$work_mem_name,
					$user_id,$user_name,'0',$memo,'0',LIP,TODATE
				);
				$insertDatalogStmt->bind_param("isssssssssssss", ...$logData); 
				$insertDatalogStmt->execute();

				
				if($work_req_cnt == 1){
					//타임라인(업무요청함)
					work_data_log('0',$codeSend, $res_idx, $user_id, $user_name, $work_mem_email, $work_mem_name);
				}
				if ($share_flag == "1") {
					$sql = "UPDATE work_todaywork SET share_flag='$share_flag', work_idx='$res_idx' WHERE state='0' and companyno='$companyno' and idx='$res_idx'";
					updateQuery($sql);
				}
				
				pushToken($work_title_token,$contents,$work_mem_email,$realState,$codeNum,$user_id,$user_name,$res_idx,null,$realState);
			}
			$insertStmt->close();
			$insertServeStmt->close();
		}

		
		//work_flag
		//업무구분(나의업무:2, 보고업무:1, 요청업무:3, 공유업무:2 && share_flag=1)
		
		if($work_flag=='2'){
			//업무작성
			if($secret_flag != '1'){
			works_realtime('works', 'add', '0', $user_id, $user_name, $workdate);
			}
			//오늘업무 다시 조회(삭제업무, 챌린지알림 제외함)
			$sql = "select count(idx) as cnt from work_todaywork where state!='9' and companyno='".$companyno."' and decide_flag='0' and share_flag='0' and workdate='".$workdate."' and email='".$user_id."'";
			$todayworks_info = selectQuery($sql);

			if($todayworks_info['cnt']){
				$todayworks_cnt = $todayworks_info['cnt'];
			}else{
				$todayworks_cnt = 0;
			}
			
			if($today != $workdate){
				// 오늘업무 타임라인에 보이게(김정훈)
				work_data_log('0','2', $res_idx, $user_id, $user_name);
				
				if($secret_flag != '1'){
					main_like_cp_works('works');
				}
				echo "complete|mywork|".$workdate."|".$todayworks_cnt;
				
			}else{
				// 오늘업무 타임라인에 보이게(김정훈)
				work_data_log('0','2', $res_idx, $user_id, $user_name);
			
				
				if($secret_flag != '1'){
					main_like_cp_works('works');
				}

				echo "complete|mywork|".$todayworks_cnt."|";
				//메인 불꽃업무, 실시간 업무수 체크
				
			}

			if( empty($share_flag) ){
				//일반업무
				//역량평가지표(오늘업무 작성), work, 0001, 사용자아이디, 업무번호, 받는사람아이디, 요청업무idx
				if($secret_flag != '1'){
					$result_cp_reward = work_cp_reward("work", "0001", $user_id, $res_idx, "", "");
				}
				echo "|".$result_cp_reward;
			}else if($share_flag=='1'){
				//공유업무 작성(공유업무 받는사람 입력된 경우)
				if($res_share_insert==false){
					//역량평가지표(공유업무 보내기), work, 0007, 사용자아이디, 업무번호, 받는사람아이디, 요청업무idx
					if($secret_flag != '1'){
						$result_cp_reward = work_cp_reward("work", "0007", $user_id, $res_idx);
					}

					$sql = "select count(1) as cnt from work_todaywork where state in (0,1) and email = '".$user_id."' and work_flag = '2' and share_flag = '1' and workdate = '".TODATE."'";
					$cp_share_cnt = selectQuery($sql);
					if($cp_share_cnt['cnt'] >= 2){
						$result_cp_reward = work_cp_reward("main_like","0003", $user_id, $res_idx);
					}
				}
				//레이어추가(신규)
				if($secret_flag != '1'){
					main_like_cp_works('share');
				}
				echo "|".$result_cp_reward;
				
			}

		//요청업무
		}else if($work_flag=='3'){
			
			//요청업무 작성(요청업무 받는사람 입력된 경우)
			if($res_req_insert){
				//역량평가지표(업무요청 보내기), work, 0004, 사용자아이디, 업무번호, 받는사람아이디, 요청업무idx
				if($secret_flag != '1'){
					$result_cp_reward = work_cp_reward("work", "0004", $user_id, $res_idx);
				}
			}

			if($today != $workdate){
				echo "complete|reqwork|".$workdate."|";
			}else{
				echo "complete|reqwork||";
			}
			echo "|".$result_cp_reward;

		//보고업무
		}else if($work_flag=='1'){
			//보고업무 작성(보고업무 받는사람 입력된 경우)
			if($res_report_insert){
				//역량평가지표(업무보고 하기), work, 0010, 사용자아이디, 업무번호, 받는사람아이디, 요청업무idx
				if($secret_flag != '1'){
					$result_cp_reward = work_cp_reward("work", "0010", $user_id, $res_idx);
				}
			}

			if($secret_flag != '1'){
				main_like_cp_works('report');
			}

			//레이어추가(신규)
			echo "complete|report||";
			echo "|".$result_cp_reward;
			
		}

		//불꽃 업무중
		//등록시 갱신 처리함

		$complete_cnt = "0";
		//선택한 파티별로 데이터 삽입
		for($i=0; $i<$be_party_cnt; $i++){
			$be_party_idx = $be_party_arr[$i];

			//파티 정보 체크
			$sql = "select idx, email, name, part, title from work_todaywork_project where state='0' and companyno='".$companyno."' and idx='".$be_party_idx."'";
			$party_ti_info = selectQuery($sql);

			//파티 참여자 회원정보
			$mem_user_info = member_row_info($user_id);
			$mem_user_no = $mem_user_info['idx'];

			if($party_ti_info['idx']){

				$party_uid = $party_ti_info['email'];
				$party_uname = $party_ti_info['name'];
				$party_part = $party_ti_info['part'];
				$party_part_flag = $party_ti_info['part_flag'];
				$party_title = $party_ti_info['title'];

				//파티생성자 회원정보
				$member_info = member_row_info($party_uid);
				$partno = $member_info['partno'];

				//파티 연결된 최초 정보
				$sql = "select idx, party_link from work_todaywork_project_info where state='0' and companyno='".$companyno."' and party_idx='".$be_party_idx."' order by idx asc limit 1";
				$party_pj_info = selectQuery($sql);
				if($party_pj_info['idx']){
					$party_link = $party_pj_info['party_link'];
				}else{
					$party_link = party_link_create();
				}

				
				$sql = "select idx from work_todaywork_project_user where state = 0 and companyno = '".$companyno."' and project_idx = '".$be_party_idx."' and party_read_flag = 0";
				$party_read = selectAllQuery($sql);

				if($party_read){
					for($i=0; $i<count($party_read['idx']); $i++){
						$party_read_idx = $party_read['idx'][$i];
					//파티 미확인 업데이트
					$sql = "update work_todaywork_project_user set party_read_flag = 1, party_read_date = ".DBDATE." where idx = '".$party_read_idx."'";
					$read = updateQuery($sql);
					}
				}
				

				//파티 공통 데이터 체크
				$sql = "select idx from work_todaywork_project_info where state='0' and companyno='".$companyno."' and party_idx='".$be_party_idx."' and party_link='".$party_link."' and work_idx='".$work_info['idx']."'";
				$party_info = selectQuery($sql);
				if(!$party_info['idx']){
					//시분초 + 업무번호
					//업무와 파티연결키생성
					//파티 통합정보 저장
					$sql = "insert into work_todaywork_project_info(party_idx, party_link, party_uid, party_uname, party_upart, party_upartno, companyno, party_title, mem_no, mem_email, mem_name, mem_part, work_idx, workdate, ip)";
					$sql = $sql .= " values('".$be_party_idx."','".$party_link."', '".$party_uid."','".$party_uname."','".$party_part."','".$partno."','".$companyno."','".$party_title."','".$mem_user_no."','".$user_id."','".$user_name."','".$user_part."','".$res_idx."','".$workdate."','".LIP."')";
					$party_info_insert_idx = insertIdxQuery($sql);
					if($party_info_insert_idx){

						//파티 연결후 업데이트 시간 갱신
						$sql = "update work_todaywork_project set editdate=".DBDATE." where idx='".$party_ti_info['idx']."'";
						$up = updateQuery($sql);
						$complete_cnt++;
					}
				}else{
					//데이터가 있을때 카운터처리함
					$complete_cnt++;
				}
			}
		}

		$sql = "update work_todaywork set party_link='".$party_link."', party_idx='".$be_party_idx."' where idx='".$res_idx."'";
		$up = updateQuery($sql);
		
		if($be_party_idx){
			//역량평가지표(받은 업무요청 완료), work, 0005, 사용자아이디, 업무번호, 받는사람아이디, 요청업무idx
			work_cp_reward("work", "0012", $user_id, $res_idx);
			main_like_cp_works('party');
		}
		
	}
	exit;
}




//업무리스트
if($mode =="works_list"){

	
	//현재날짜
	$wdate = $_POST['wdate'];
	$works_type = $_POST['works_type'];
	$cate = $_POST['cate'];

	//날짜변환
	if($wdate){
		$wdate = str_replace(".","-",$wdate);
	}

	if($cate == 'share'){
		$cate_where = "and share_flag in ('1','2')";
		$cate_where1 = "and a.share_flag in ('1','2')";
	}else if($cate == 'report'){
		$cate_where = "and work_flag = '1'";
		$cate_where1 = "and a.work_flag = '1'";
	}else if($cate == 'req'){
		$cate_where = "and work_flag = '3'";
		$cate_where1 = "and a.work_flag = '3'";
	}else if($cate == 'work'){
		$cate_where = "and work_flag = '2' and share_flag = '0'";
		$cate_where1 = "and a.work_flag = '2' and share_flag = '0'";
	}

	//예약업무 예약기능
	$sql = "select idx, title, type_flag from work_decide where state='0' and companyno='".$companyno."' order by sort asc";
	$decide_info = selectAllQuery($sql);


	//알림기능
	$sql = "select idx, title from work_notice where state='0'  order by sort asc";
	$notice_info = selectAllQuery($sql);
	for($i=0; $i<count($notice_info['idx']); $i++){
		$idx = $notice_info['idx'][$i];
		$title = $notice_info['title'][$i];
		$notice_list[$idx] = $title;
	}

	if($user_id=="sadary0@nate.com"){
		//$user_id="eyson@bizforms.co.kr";
		//$user_id="sun@bizforms.co.kr";
		//$user_id="qohse@nate.com";
		//$user_id="zhowlsk2@nate.com";
		//	$user_id="adsb12@nate.com";
		//	$user_id="audtjs2282@nate.com";
		//$user_id="chdk1001@nate.com";
	}

	//일일업무
	if($works_type=="day"){

		//업무보고 받는사람, 보고보낸사람 정보
		$work_report_user = work_report_user($wdate);

		//업무요청 받는사람, 요청보낸사람 정보
		$work_req_user = work_req_user($wdate);

		//업무공유 받는사람, 공유보낸사람 정보
		$work_share_user = work_share_user($wdate);

		//첨부파일정보 불러오기
		$tdf_files = work_files_linfo($wdate);

		//업무 댓글
		$sql = "select a.idx as cidx, a.link_idx, a.work_idx, a.email, a.name, a.comment, a.cmt_flag, CASE WHEN a.editdate is not null then date_format(a.editdate, '%Y-%m-%d') WHEN a.editdate is null then date_format(a.regdate, '%Y-%m-%d') end as ymd,";
		$sql = $sql .= " CASE WHEN a.editdate is not null then date_format( a.editdate , '%m/%d/%y %l:%i:%s %p') WHEN a.editdate is null then date_format( a.regdate , '%m/%d/%y %l:%i:%s %p') end as regdate, b.idx from work_todaywork_comment as a left join work_todaywork as b on(a.link_idx=b.idx) where a.state='0' and a.companyno='".$companyno."' and b.workdate='".$wdate."' order by a.regdate desc";
		$works_comment_info = selectAllQuery($sql);

		for($i=0; $i<count($works_comment_info['idx']); $i++){
			$works_comment_info_idx = $works_comment_info['cidx'][$i];
			$works_comment_info_link_idx = $works_comment_info['link_idx'][$i];
			$works_comment_info_work_idx = $works_comment_info['work_idx'][$i];
			$works_comment_info_email = $works_comment_info['email'][$i];
			$works_comment_info_name = $works_comment_info['name'][$i];
			$works_comment_info_ymd = $works_comment_info['ymd'][$i];
			$works_comment_info_regdate = $works_comment_info['regdate'][$i];
			$works_comment_info_comment = $works_comment_info['comment'][$i];
			$works_comment_info_comment_strip = strip_tags($works_comment_info['comment'][$i]);
			$works_comment_info_cmt_flag = $works_comment_info['cmt_flag'][$i];

			if($works_comment_info_link_idx){
				$comment_list[$works_comment_info_link_idx]['cidx'][] = $works_comment_info_idx;
				$comment_list[$works_comment_info_link_idx]['name'][] = $works_comment_info_name;
				$comment_list[$works_comment_info_link_idx]['email'][] = $works_comment_info_email;
				$comment_list[$works_comment_info_link_idx]['ymd'][] = $works_comment_info_ymd;
				$comment_list[$works_comment_info_link_idx]['regdate'][] = $works_comment_info_regdate;
				$comment_list[$works_comment_info_link_idx]['comment'][] = $works_comment_info_comment;
				$comment_list[$works_comment_info_link_idx]['comment_strip'][] = $works_comment_info_comment_strip;
				$comment_list[$works_comment_info_link_idx]['cmt_flag'][] = $works_comment_info_cmt_flag;
			}
		}

		//좋아요 리스트
		$like_flag_list = array();
		$sql = "select idx, email,service, work_idx, send_email, like_flag from work_todaywork_like where state='0' and companyno='".$companyno."' and send_email='".$user_id."' and workdate='".$wdate."'";
		$like_info = selectAllQuery($sql);
		for($i=0; $i<count($like_info['idx']); $i++){
			$like_info_idx = $like_info['idx'][$i];
			$like_info_email = $like_info['email'][$i];
			$like_info_work_idx = $like_info['work_idx'][$i];
			$like_info_like_flag = $like_info['like_flag'][$i];
			$like_info_send_email = $like_info['send_email'][$i];
			$work_like_list[$like_info_work_idx] = $like_info_idx;
		}


		//좋아요 받은내역
		$work_like_receive = array();
		$sql = "select idx, email,service, work_idx, send_email, like_flag from work_todaywork_like where state='0' and email='".$user_id."' and workdate='".$wdate."'";
		$like_info = selectAllQuery($sql);
		for($i=0; $i<count($like_info['idx']); $i++){
			$like_info_idx = $like_info['idx'][$i];
			$like_info_email = $like_info['email'][$i];
			$like_info_work_idx = $like_info['work_idx'][$i];
			$like_info_like_flag = $like_info['like_flag'][$i];
			$like_info_send_email = $like_info['send_email'][$i];
			$work_like_receive[$like_info_work_idx] = $like_info_idx;
		}

		//오늘한줄소감
		$sql = "select idx, work_idx, comment from work_todaywork_review where state='0' and companyno='".$companyno."' and email='".$user_id."' and workdate='".$wdate."'";
		$review_info = selectQuery($sql);


		//파티연결정보
		$sql = "select work_idx, party_uid, party_link from work_todaywork_project_info where state='0' and companyno='".$companyno."' and workdate='".$wdate."'";
		$project_data_info = selectAllQuery($sql);
		$project_link_info = @array_combine($project_data_info['work_idx'], $project_data_info['party_link']);

		
		//오늘업무 리스트
		// $sql = "select idx, state, work_flag, decide_flag, email, name, work_idx, repeat_work_idx, repeat_flag, notice_flag, share_flag, memo_view, contents_view, title, contents, workdate, date_format( regdate , '%m/%d/%y %l:%i:%s %p') as reg, date_format(regdate, '%H:%i') as his from work_todaywork where state!='9'";
		// $sql = $sql .=" and companyno='".$companyno."' and email='".$user_id."' and workdate = '".$wdate."'";
		// $sql = $sql .= " order by sort asc, idx desc";
		$sql = "select * from (select a.idx, a.state, b.state as calstate, a.work_flag, a.decide_flag, a.secret_flag, a.email, a.name, a.work_idx, a.repeat_work_idx, a.repeat_flag, a.notice_flag, a.share_flag, a.memo_view, a.contents_view, a.title, a.contents, a.workdate, a.work_stime, a.work_etime, ";
		$sql = $sql .= "date_format( a.regdate , '%m/%d/%y %l:%i:%s %p') as reg, date_format(a.regdate, '%H:%i') as his, a.party_link, a.sort ";
		$sql = $sql .= "from work_todaywork a ";
		$sql = $sql .= "left join calendar_events b on a.idx = b.work_idx ";
		$sql = $sql .= "where 1=1 and ((a.state NOT IN ('9', '99') AND (b.state IS NULL OR b.state <> '9')) OR (b.state = '0' AND a.workdate <> '".$wdate."' )) and a.companyno='".$companyno."' and a.email='".$user_id."' and (a.workdate = '".$wdate."' or b.start_date = '".$wdate."') and a.notice_flag = 0 $cate_where";
		$sql = $sql .= " group by idx";
		$sql = $sql .= ") as subquery1 union select * from ( ";
		$sql = $sql .= "select a.idx, a.state, b.state as calstate, a.work_flag, a.decide_flag, a.secret_flag, a.email, a.name, a.work_idx, a.repeat_work_idx, a.repeat_flag, a.notice_flag, a.share_flag, a.memo_view, a.contents_view, a.title, a.contents, a.workdate, a.work_stime, a.work_etime, ";
		$sql = $sql .= "date_format( a.regdate , '%m/%d/%y %l:%i:%s %p') as reg, date_format(a.regdate, '%H:%i') as his, party_link, sort ";
		$sql = $sql .= "from work_todaywork a join work_challenges b on (a.work_idx = b.idx) left join (select challenges_idx,email, state from work_challenges_result where email = '".$user_id."') as c on (a.work_idx = c.challenges_idx) where a.notice_flag = 1 ";
		$sql = $sql .= "and b.sdate <= '".$wdate."' and b.edate >= '".$wdate."' and b.companyno = '".$companyno."' and a.email = '".$user_id."' $cate_where1 and a.state = 0 and ((c.email = '".$user_id."' and c.state != 1) or c.email is null)) as subquery2 order by sort asc, idx desc";
		$works_info = selectAllQuery($sql);
		//보고업무
		$sql = "select idx, state, work_flag, decide_flag, email, name, work_idx, repeat_flag, notice_flag, share_flag, title, contents, workdate, date_format( regdate , '%m/%d/%y %l:%i:%s %p') as reg, date_format(regdate, '%H:%i') as his from work_todaywork where state!='9'";
		$sql = $sql .=" and companyno='".$companyno."' and work_flag='1' and work_idx is null and workdate='".$wdate."'";
		$sql = $sql .= " order by sort asc, idx desc";
		$works_report_info = selectAllQuery($sql);

		for($i=0; $i<count($works_report_info['idx']); $i++){
			$work_report_idx = $works_report_info['idx'][$i];
			$work_report_title = $works_report_info['title'][$i];
			$work_report_contents = $works_report_info['contents'][$i];
			$work_report_email = $works_report_info['email'][$i];
			$work_report_name = $works_report_info['name'][$i];

			$work_report_list[$work_report_idx]['title'] = $work_report_title;
			$work_report_list[$work_report_idx]['contents'] = $work_report_contents;
			$work_report_list[$work_report_idx]['email'] = $work_report_email;
			$work_report_list[$work_report_idx]['name'] = $work_report_name;
		}

		?>

		<ul class="tdw_list_ul">
		<?
		$sql = "select idx, penalty_state, email from work_member where state = '0' and email = '".$user_id."'";
		$query = selectQuery($sql);

		if($query['penalty_state']=='1'){?>
		<div class="tdw_penalty_banner">
			<div class="tdw_pb_in">
				<img src="/html/images/pre/img_penalty.png" alt="" />
				<p><span>[긴급]</span>페널티 카드가 발동했습니다.</p>
			</div>
		</div>
		<?}
		if($works_info['idx']){
			for($i=0; $i<count($works_info['idx']); $i++){
				$idx = $works_info['idx'][$i];
				$work_idx = $works_info['work_idx'][$i];
				$calstate = $works_info['calstate'][$i];
				$state = $works_info['state'][$i];
				$work_email = $works_info['email'][$i];
				$work_name = $works_info['name'][$i];
				$contents = $works_info['contents'][$i];
				$title = $works_info['title'][$i];
				$work_his = $works_info['his'][$i];
				$work_reg = $works_info['reg'][$i];

				$repeat_work_idx = $works_info['repeat_work_idx'][$i];
				$decide_flag = $works_info['decide_flag'][$i];
				$work_date = $works_info['workdate'][$i];
				$work_stime = $works_info['work_stime'][$i];
				$work_etime = $works_info['work_etime'][$i];
				$work_flag = $works_info['work_flag'][$i];
				$repeat_flag = $works_info['repeat_flag'][$i];
				$notice_flag = $works_info['notice_flag'][$i];
				$secret_flag = $works_info['secret_flag'][$i];
				$share_flag = $works_info['share_flag'][$i];
				$memo_view =  $works_info['memo_view'][$i];
				$contents_view = $works_info['contents_view'][$i];

				if($decide_flag == '1'){$decide_name = "연차";}else if($decide_flag == '2'){ $decide_name = "반차";}else if($decide_flag == '3'){$decide_name = "외출";}else if($decide_flag == '4'){$decide_name = "조퇴";}
				else if($decide_flag == '5'){$decide_name = "출장";}else if($decide_flag == '6'){$decide_name = "교육";}
				else if($decide_flag == '7'){$decide_name = "미팅";}else if($decide_flag == '8'){$decide_name = "회의";}

				if($repeat_flag == 1){
					$repeat_text = "매일반복";
				}else if($repeat_flag == 2){
					$repeat_text = "매주반복";
				}else if($repeat_flag == 3){
					$repeat_text = "매월반복";
				}else if($repeat_flag == 4){
					$repeat_text = "반복안함";
				}else{
					$repeat_text = "반복설정";
				}

				$memo_view_bt_style = "";
				//메모 접기/펼치기(0:펼치기, 1:접기)
				if($memo_view == '1'){
					$memo_view_in = " off";
					$memo_view_bt = " off memo_on";
					$memo_view_bt_style = " style=\"display: block;\"";

				}else{
					$memo_view_in = "";
					$memo_view_bt = " on";
					$memo_view_bt_style = "";
				}


				$report_view_bt_style = "";
				//보고업무 내용 접기/펼치기(0:펼치기, 1:접기)
				if($contents_view == '1'){
					$report_view_in = " off";
					$report_view_bt = " off memo_on";
					$report_view_bt_style = " style=\"display: block;\"";

				}else{
					$report_view_in = "";
					$report_view_bt = " on memo_on";
					$report_view_bt_style = "";
				}


				$share_view_bt_style = "";
				//공유업무 내용 접기/펼치기(0:펼치기, 1:접기)
				if($contents_view == '1'){
					$share_view_in = " off";
					$share_view_bt = " off";
					$share_view_bt_style = " off";

				}else{
					$share_view_in = "";
					$share_view_bt = " on";
					$share_view_bt_style = "";
				}

				$work_view_bt_style = "";
				//오늘업무 내용 접기/펼치기(0:펼치기, 1:접기)
				if($contents_view == '1'){
					$work_view_in = " off";
					$work_view_bt = " off";
					$work_view_bt_style = " off";

				}else{
					$work_view_in = "";
					$work_view_bt = " on";
					$work_view_bt_style = "";
				}

				$req_view_bt_style = "";
				//공유업무 내용 접기/펼치기(0:펼치기, 1:접기)
				if($contents_view == '1'){
					$req_view_in = " off";
					$req_view_bt = " off";
					$req_view_bt_style = " off";

				}else{
					$req_view_in = "";
					$req_view_bt = " on";
					$req_view_bt_style = "";
				}

				//공유함($share_flag=1), 공유취소($share_flag=2), 요청받은업무($work_flag=3) 아이콘 변경
				//$tdw_list(완료체크여부) : true, false
				$li_class = "";
				$tdw_list = false;

				//읽음표시
				//요청업무
				$read_req_text="";
				$work_req_read_reading="";

				//보고업무
				$read_report_text="";
				$work_report_read_reading="";

				//공유업무
				$read_share_text="";
				$work_share_read_reading="";

				//공유한 업무(share_flag=1, 공유받은 업무:share_flag=2)
				if($share_flag=="1"){
					$li_class = " share";
					$tdw_list = true;
				}else if($share_flag=="2" && $work_idx){
					$li_class = " share";
					$tdw_list = false;
				}else if($work_flag == "2" && $share_flag == "0" && $notice_flag !="1"){
					$li_class = " work";
					$tdw_list = false;
				}else{

					//notice_flag=1 챌린지알림,
					//$work_flag=3 요청업무, $work_idx=null 요청보낸업무
					//$work_flag=3 요청업무, $work_idx 요청받은업무
					if($work_flag=='3' && $work_idx){
						$li_class = " req_get";
						$tdw_list = true;
					}else if($work_flag=='3' && $work_idx==null){
						$li_class = " req";
						$tdw_list = "";
					}else if($work_flag=='0' && $work_idx!=null){
						$li_class = " getreq";
						$tdw_list = true;
					}else if($work_flag=='1'){

						//보고받음
						if($work_idx){
							$li_class = " report_get";
							$tdw_list = false;
						}else{
							//보고함
							$li_class = " report";
							$tdw_list = false;
						}
					}else if($work_flag=="4"){
						$li_class = " challenges";
						$tdw_list = false;
					}else{
						if($notice_flag=="1"){
							$li_class = " challenges";
							$tdw_list = false;
						}else{
							$li_class = "";
							$tdw_list = true;
						}
					}
				}

				if($work_reg){
					$work_reg = str_replace("  "," ", $work_reg);
					$his_tmp = @explode(" ", $work_reg);
					if ($his_tmp['2'] == "PM"){
						$after = "오후 ";
					}else{
						$after = "오전 ";
					}
					$ctime = @explode(":", $his_tmp['1']);
					$work_his = $work_date . " " . $after . $ctime['0'] .":". $ctime['1'];
				}

				//요청 및 공유, 보고
				if($work_idx){
					$work_com_idx = $work_idx;
				}else{
					$work_com_idx = $idx;
				}

			?>

				<li class="tdw_list_li<?=$li_class?>" id="workslist_<?=$idx?>">
				
					<div class="tdw_list_box<?=($state=='1' || $calstate=='1')?" on":""?><?=$share_view_bt_style?>" id="tdw_list_box_<?=$idx?>" name="onoff_<?=$i?>">
						<div class="tdw_list_chk">
							<button class="btn_tdw_list_chk" <?if($work_flag!='1'){?>value="<?=$idx?>"<?}?> id="tdw_dlist_chk"><span>완료체크</span></button>
						</div>
						<div class="tdw_list_desc <?=$secret_flag == '1'?"lock":""?>">


						<?//업무요청
							$work_title = "";

							if($notice_flag){
								$work_title = "[".$notice_list[$notice_flag] ."]";?>
								<p id="notice_link" value="<?=$work_idx?>"><?=$contents?></p>

							<?}else{


								if($work_flag == "1"){
									//보고받은 업무
									if($work_idx){
										$work_to_name = $work_report_user['receive'][$work_idx];
										$work_title = "[".$work_to_name ."님에게 보고받음]";

									}else{

										//보고 1명 이상인 경우
										if($work_report_user['send_cnt'][$idx] > 1){
											$work_user_count = $work_report_user['send_cnt'][$idx] - 1;
											$work_report_user_title = $work_report_user['send'][$idx][0]. "님 외 ". $work_user_count . "명에게 보고함";
											$work_title = "[". $work_report_user_title. "]";
										}else{
											$work_report_user_title = $work_report_user['send'][$idx][0];
											$work_title = "[". $work_report_user_title. "님에게 보고함]";
										}

										$work_report_read_all = $work_report_user['read'][$idx]['all'];
										$work_report_read_cnt = $work_report_user['read'][$idx]['read'];
										$work_report_read_reading = $work_report_read_cnt;

										//읽지않은사용자
										if($work_report_read_reading>0){
											$read_report_text = "&nbsp;&nbsp;<em class=\"tdw_read\">읽음 ".$work_report_read_reading."</em>";
										}else{
											$read_report_text = "&nbsp;&nbsp;<em class=\"tdw_read\">읽음 0</em>";
										}
									}

									//업무수정
									if($work_idx == null && $user_id == $work_email){?>
										<p id="tdw_list_edit_<?=$idx?>"><span><?=$work_title?></span><?=$title?><?=$read_report_text?></p>
									<?}else{?>
										<p><span><?=$work_title?></span><?=$title?><?=$read_report_text?></p>
									<?}

									$edit_content = $title;

								//요청업무
								}else if($work_flag == "3"){
									//$work_user_name = "";
									//for($j=0; $j<count($work_user_list[$work_com_idx]); $j++){
									//	$work_user_name .= $work_user_list[$work_com_idx][$j] . ", ";
									//}

									if($work_idx){
										
										//$work_to_name = $work_to_user_list['work_name'][$work_idx];
										$work_req_name = $work_req_user['receive'][$work_idx];
										$work_title = "[".$work_req_name ."님에게 요청받음]";

									}else{

										//업무요청 1명 이상인 경우
										if($work_req_user['send_cnt'][$work_com_idx] > 1){
											$work_user_count = $work_req_user['send_cnt'][$work_com_idx] - 1;
											$work_req_title = $work_req_user['send'][$work_com_idx][0]. "님 외 ". $work_user_count . "명에게 요청함";
											$work_title = "[". $work_req_title. "]";
										}else{
											$work_req_title = $work_req_user['send'][$work_com_idx][0];
											$work_title = "[". $work_req_title. "님에게 요청함]";
										}

										$work_req_read_all = $work_req_user['read'][$work_com_idx]['all'];
										$work_req_read_cnt = $work_req_user['read'][$work_com_idx]['read'];
										$work_req_read_reading = $work_req_read_cnt;

										if($work_req_read_reading>0){
											$read_req_text = "&nbsp;&nbsp;<em class=\"tdw_read\">읽음 ".$work_req_read_reading."</em>";
										}else{
											$read_req_text = "&nbsp;&nbsp;<em class=\"tdw_read\">읽음 0</em>";
										}
									}

									//업무수정
									if($work_idx == null && $user_id == $work_email){?>
										<p id="tdw_list_edit_<?=$idx?>"><span><?=$work_title?></span><?=textarea_replace($contents)?><?=$read_req_text?></p>
									<?}else{?>
										<p><span><?=$work_title?></span><?=textarea_replace($contents)?><?=$read_req_text?></p>
									<?}

									$edit_content = $contents;

								}else if($work_flag == '4'){?>
									<p id="party_link" value="<?=$work_idx?>"><?=$contents?></p>
								<?}else{
									//받은 업무가 있을경우
									$edit_id = "";
									if($work_idx){

										if($share_flag == "1"){
											$edit_id = " id='tdw_list_edit_".$idx."'";

											if($work_share_user['send_cnt'][$idx] > 1){
												$work_user_count = $work_share_user['send_cnt'][$idx] - 1;
												$work_req_user_title = $work_share_user['send'][$idx][0]. "님 외 ". $work_user_count . "명에게 공유함";
												$work_title = "[". $work_req_user_title. "]";
											}else{
												$work_req_user_title = $work_share_user['send'][$idx][0];
												$work_title = "[". $work_req_user_title. "님에게 공유함]";
											}

											$work_share_read_all = $work_share_user['read'][$work_idx]['all'];
											$work_share_read_cnt = $work_share_user['read'][$work_idx]['read'];
											$work_share_read_reading = $work_share_read_cnt;

											//읽지않은사용자
											if($work_share_read_reading>0){
												$read_share_text = "&nbsp;&nbsp;<em class=\"tdw_read\">읽음 ".$work_share_read_reading."</em>";
											}else{
												$read_share_text = "&nbsp;&nbsp;<em class=\"tdw_read\">읽음 0</em>";
											}

										}else if($share_flag == "2"){
											$work_to_name = $work_share_user['receive'][$work_idx];
											$work_title = "[".$work_to_name ."님에게 공유받음]";
										}else{
											$work_to_name = $work_req_user['receive'][$work_idx];
											$work_title = "[".$work_to_name ."님에게 요청받음]";
										}
									?>
										<p <?=$edit_id?>><?=$work_title?"<span>".$work_title."</span>":""?><?=textarea_replace($contents)?><?=$read_share_text?></p>
									<?
									//일반업무
									}else{
										if($decide_flag > 0 && $work_stime != null && $work_etime != null){
											if($decide_flag == 1){
												$work_title = "<span>[ ".$decide_name." ]</span>";
											}else if($decide_flag > 1){
												$work_title = "<span>[ ".$decide_name."   ".$work_stime."~".$work_etime." ]</span>";
											}
										}
									?>
										<p id="tdw_list_edit_<?=$idx?>"><?=$work_title?><?=textarea_replace($contents)?></p>
									<?}

									$edit_content = $contents;
								}
							}

						$sql = "select share_flag, email, work_idx, idx from work_todaywork where work_idx = '".$work_idx."' ";
						$party_icon = selectQuery($sql);

						?>
							<div class="tdw_list_regi" id="tdw_list_regi_edit_<?=$idx?>">
								<strong>수정중</strong>
								<textarea name="" class="textarea_regi" id="textarea_regi_<?=$idx?>"><?=strip_tags($edit_content)?></textarea>
								<div class="btn_regi_box">
									<button class="btn_regi_submit" id="btn_regi_submit" value="<?=$idx?>"><span>확인</span></button>
									<button class="btn_regi_cancel"><span>취소</span></button>
								</div>
							</div>
						</div>
						
						<div class="tdw_list_function new_type">
							<div class="tdw_list_function_in">
								<?
								//받은업무
								//보고, 공유
								if($work_flag=="1" && $work_idx || $share_flag=='2' && $work_idx){?>
									<button class="tdw_list_100 tdw_list_100c" title="코인 보내기" id="tdw_list_100c" value="<?=$idx?>"><span>100</span></button>
								<?}?>
								

								<?
								//보고받은 업무
								if($work_flag=="1" && $work_idx){?>
									<button class="tdw_list_h tdw_list_reported_hart<?=$work_like_list[$work_idx]>0?" on":""?>" title="좋아요" <?=$work_like_list[$work_idx]>0?"":" id=\"tdw_list_jjim\""?> value="<?=$work_idx?>"><span>좋아요</span></button>
								<?
								//공유받음
								}else if($share_flag=='2' && $work_idx){?>
									<button class="tdw_list_h tdw_list_shared_hart<?=$work_like_list[$work_idx]>0?" on":""?>" title="좋아요" <?=$work_like_list[$work_idx]>0?"":" id=\"tdw_list_jjim\""?> value="<?=$work_idx?>"><span>좋아요</span></button>
								<?}else{?>

									<?//공유 보낸 업무?>
									<?if($share_flag=="1" && $work_idx){?>
										<?if($work_like_receive[$work_idx]){?>
											<button class="tdw_list_jjim_clear<?=$work_like_receive[$work_idx]>0?" on":""?>" title="좋아요" value="<?=$work_idx?>"><span>좋아요</span></button>
										<?}?>
									<?}?>

									<?//보고업무 보낸 업무?>
									<?if($work_flag=="1" && $work_idx==null){?>
										<?if($work_like_receive[$idx]){?>
											<button class="tdw_list_jjim_clear<?=$work_like_receive[$idx]>0?" on":""?>" title="좋아요"  value="<?=$work_idx?>"><span>좋아요</span></button>
										<?}?>
									<?}?>
								<?}?>
								<div class="tdw_list_drag" title="순서 변경" value="<?=$idx?>"><span>드래그 드랍 기능</span></div>
								<div class="tdw_list_more">
									<?if($work_flag != '4'){?>
									<button class="tdw_list_o" title="메뉴열기" id=""><span>메뉴열기</span></button>
									<?}?>
									<div class="tdw_list_1depth">
										<ul>
										<?if(($notice_flag=='0' || $decide_flag=='0') && $share_flag!=='2' && $notice_flag!='1' && $work_flag!='4'){?>
										<li>
											<button class="tdw_list_p tdw_list_party_link <?=$project_link_info[$idx]?"on":""?>" id="tdw_list_party_link" value="<?=$idx?>" title="파티연결"><span>파티연결</span></button>
										</li>
									<?}?>
									<?//공유하기?>
									<?//공유한 업무?>
									<?if($share_flag=='1' && $work_idx){?>
										<li>
											<button class="tdw_list_share_cancel tdw_list_s" id="tdw_list_share_cancel" value="<?=$idx?>" title="공유취소"><span>공유취소</span></button>
										</li>
									<?}else{?>
										<?//나의업무작성, 공유업무작성?>
										<?if(($work_flag=='2' && $work_idx==null) || ($share_flag=='1' && $work_idx==null)){?>
										<li>
											<button class="tdw_list_share tdw_list_s" id="tdw_list_share" value="<?=$idx?>" title="공유하기"><span>공유하기</span></button>
										</li>
											<?}?>
									<?}?>
									
									<?//파일첨부?>
									<?//파일첨부(나의업무, 공유업무작성, 보고업무작성, 요청업무작성)?>
									<?if(($work_flag=='2' && $work_idx==null) || ($share_flag=='1' && $work_idx) || ($work_flag=='1' && $work_idx==null) || ($work_flag=='3' && $work_idx==null)){?>
										<li>
											<button class="tdw_list_files tdw_list_f" id="tdw_file_add_<?=$idx?>" title="파일추가"><span>파일추가</span></button>
											<input type="file" id="files_add_<?=$idx?>" style="display:none;">
										</li>
									<?}?>
									
									<?//사람선택?>
									<?//공유업무작성, 보고업무작성, 요청업무작성?>
									<?if(($share_flag=='1' && $work_idx) || ($work_flag=='1' &&  $work_idx==null) || ($work_flag=='3' && $work_idx==null)){?>
										<li>
											<button class="tdw_list_user tdw_list_u" id="tdw_send_user_<?=$idx?>" value="<?=$idx?>" title="사람추가"><span>사람추가</span></button>
										</li>
									<?}?>
										<?//메모작성?>
									<? if($notice_flag!='1' && $work_flag!='4'){?>
										<?php if($secret_flag == '1'){?>
											<li>
												<button class="tdw_list_memo_secret tdw_list_m" id="tdw_list_memo" value="<?=$idx?>" title="메모하기"><span>메모하기</span></button>
											</li>
										<?php }else{ ?>
											<li>
												<button class="tdw_list_memo tdw_list_m" id="tdw_list_memo" value="<?=$idx?>" title="메모하기"><span>메모하기</span></button>
											</li>
										<?php } ?>	
										<?}?>
									<?if(($work_flag=='2' && $work_idx==null) || ($work_flag=='3' && $work_idx==null)){?>
										<? if(($repeat_flag && ($work_date < '2023-09-19')) || $repeat_work_idx != null){ ?>
											<li>
												<button class="tdw_list_r <?=$repeat_flag?" on":""?>" id="tdw_list_repeat_info_new" value="<?php echo $idx?>"><span>반복설정</span></button>
											</li>
										<?php }else{?>
											<li>
												<button class="tdw_list_r <?=$repeat_flag?" on":""?>" id="tdw_list_repeat_new" value="<?php echo $idx?>"><span>반복설정</span></button>
											</li>
										<?php } ?>
									<?php } ?>
									
									<?//일정변경?>
									<?//나의업무, 공유업무작성, 보고업무작성, 요청업무작성?>
									<?if(($work_flag=='2' && $work_idx==null) || ($share_flag=='1' && $work_idx==null) || ($work_flag=='1' && $work_idx==null) || ($work_flag=='3' && $work_idx==null)){?>
										<li>
											<div class ="tdw_list_c">
												<input class="tdw_list_date tdw_list_cc" type="text" id="listdate_<?=$idx?>" value="날짜변경" readonly>
											</div>
										</li>
									<?}?>
									<?//일정변경?>
									<?//나의업무, 공유업무작성, 보고업무작성, 요청업무작성?>
									<?if(($work_stime && $work_etime && $work_flag == '2' && $share_flag == '0' && $state == '0' && $decide_flag > '1')){?>
										<li>
											<button class="tdw_list_time tdw_list_t" id="tdw_list_time" value="<?=$idx?>" title="시간변경"><span>시간변경</span></button>
										</li>
									<?}?>
									<li>
										<?if($work_flag!='4'){
											if($notice_flag){?>
												<?if($user_id == $work_email){?>
													<button class="tdw_list_del tdw_list_d" title="삭제" id="notice_list_del" value="<?=$idx?>"><span>삭제</span></button>
												<?}else{?>
													<button class="tdw_list_del tdw_list_d" title="삭제" value="<?=$idx?>"><span>삭제</span></button>
												<?}?>
											<?}else{?>
											<?//업무글삭제?>
												<?if($user_id == $work_email && $share_flag == 0 && $work_flag == 2){?>
													<button class="tdw_list_del tdw_list_d" title="삭제" id="tdw_list_per_del" value="<?=$idx?>"><span>삭제</span></button>
												<?}else if($user_id == $work_email){?>
													<button class="tdw_list_del tdw_list_d" title="삭제" id="tdw_list_del" value="<?=$idx?>"><span>삭제</span></button>
												<?}else{?>
													<button class="tdw_list_del tdw_list_d" title="삭제" value="<?=$idx?>"><span>삭제</span></button>
												<?}?>
											<?}
											}
										?>
									</li>
									<li>
										<button class="tdw_list_cancel" id="tdw_list_cancel" title="닫기"><span>닫기</span></button>
									</li>
										</ul>
									</div>
								</div>
							</div>
						</div>
						
						<?//첨부파일 정보
						//나의업무, 요청업무
						if(in_array($work_flag, array('2','3'))){
							if($tdf_files[$work_com_idx]['file_path']){?>
								<div class="tdw_list_file">
									<?for($k=0; $k<count($tdf_files[$work_com_idx]['file_path']); $k++){?>
										<div class="tdw_list_file_box">
											<button class="btn_list_file" id="tdw_list_file_<?=$tdf_files[$work_com_idx]['num'][$k]?>" value="<?=$tdf_files[$work_com_idx]['idx'][$k]?>"><span><?=$tdf_files[$work_com_idx]['file_real_name'][$k]?></span></button>
											<?//보고업무 작성한 사용자만 삭제
											if($user_id==$tdf_files[$work_com_idx]['email'][$k]){?>
												<button class="btn_list_file_del" id="btn_list_fdel" value="<?=$tdf_files[$work_com_idx]['idx'][$k]?>" title="삭제"><span>삭제</span></button>
											<?}?>
										</div>
									<?}?>
								</div>
							<?}
						}?>


					</div>


					<?//보고업무
					if($work_flag=='1'){

						if($work_idx == null){
							$report_email = $work_report_list[$idx]['email'];
							$report_name =$work_report_list[$idx]['name'];
							$report_contents =$work_report_list[$idx]['contents'];

						}else{
							$report_email = $work_report_list[$work_idx]['email'];
							$report_name =$work_report_list[$work_idx]['name'];
							$report_contents =$work_report_list[$work_idx]['contents'];
						}

					?>

						<div class="tdw_list_report_area">
							<div class="tdw_list_report_area_in<?=$report_view_in?>" id="tdw_list_report_area_in_<?=$idx?>">
								<div class="tdw_list_report_desc">
									<div class="tdw_list_report_conts">
										<?if($user_id==$report_email){?>
											<span class="tdw_list_report_conts_txt" id="tdw_list_report_conts_txt_<?=$idx?>"><?=textarea_replace($report_contents)?></span>
										<?}else{?>
											<span class="tdw_list_report_conts_txt"><?=textarea_replace($report_contents)?></span>
										<?}?>
										<em class="tdw_list_report_conts_date"><?=$work_his?></em>
										<div class="tdw_list_report_regi" id="tdw_list_report_regi_<?=$idx?>">
											<textarea name="" class="textarea_regi" id="tdw_report_edit_<?=$idx?>"><?=strip_tags($report_contents)?></textarea>
											<div class="btn_regi_box">
												<button class="btn_regi_submit" id="btn_report_submit" value="<?=$idx?>"><span>확인</span></button>
												<button class="btn_regi_cancel"><span>취소</span></button>
											</div>
										</div>
									</div>
								</div>

								<?//첨부파일 정보
								if($tdf_files[$work_com_idx]['file_path']){?>
									<div class="tdw_list_file">
										<?for($k=0; $k<count($tdf_files[$work_com_idx]['file_path']); $k++){?>
											<div class="tdw_list_file_box">
												<button class="btn_list_file" id="tdw_list_file_<?=$tdf_files[$work_com_idx]['num'][$k]?>" value="<?=$tdf_files[$work_com_idx]['idx'][$k]?>"><span><?=$tdf_files[$work_com_idx]['file_real_name'][$k]?></span></button>

												<?//보고업무 작성한 사용자만 삭제
												if($user_id==$report_email){?>
													<button class="btn_list_file_del" id="btn_list_fdel" value="<?=$tdf_files[$work_com_idx]['idx'][$k]?>" title="삭제"><span>삭제</span></button>
												<?}?>

											</div>
										<?}?>
									</div>
								<?}?>
							</div>

							<div class="tdw_list_report_onoff"<?=$report_view_bt_style?>>
								<button class="btn_list_report_onoff<?=$report_view_bt?>" id="btn_list_report_onoff_<?=$idx?>" value="<?=$idx?>" <?if(trim($report_view_bt)=="on"){ echo "title='보고 접기'"; }else{ echo "title='보고 펼치기'"; }?>><span>보고 접기/펼치기</span></button>
							</div>
						</div>
					<?}?>

					<?if($work_flag=='2' && $share_flag=='0' && $notice_flag=='0'){?>
						<div class="tdw_list_work_onoff"<?=$share_view_bt_style?>>
							<button class="btn_list_work_onoff <?=($contents_view=="1"? " off": "")?>" id="btn_list_work_onoff_<?=$idx?>" value="<?=$idx?>"><span>업무 접기/펼치기</span></button>
						</div>
					<?}?>

					<?if($work_flag=='3'){?>
						<div class="tdw_list_req_onoff"<?=$req_view_bt_style?>>
							<button class="btn_list_req_onoff<?=($comment_list[$work_com_idx]?" memo_on": "");?><?=$req_view_bt?>" id="btn_list_req_onoff_<?=$idx?>" value="<?=$idx?>" <?if(trim($req_view_bt)=="on"){ echo "title='요청 접기'";}else{ echo "title='요청 펼치기'"; }?>><span>요청 접기/펼치기</span></button>
						</div>
					<?}?>

					<?if($share_flag && $work_idx){?>
						<div class="tdw_list_share_onoff"<?=$share_view_bt_style?>>
							<button class="btn_list_share_onoff<?=($comment_list[$work_com_idx]?" memo_on": "");?><?=$share_view_bt?>" id="btn_list_share_onoff_<?=$idx?>" value="<?=$idx?>" <?if(trim($share_view_bt)=="on"){ echo "title='공유 접기'"; }else{ echo "title='공유 펼치기'"; }?>><span>공유 접기/펼치기</span></button>
						</div>
					<?}?>

					<div class="tdw_list_memo_area">
						<div class="tdw_list_memo_area_in<?=$memo_view_in?>" id="tdw_list_memo_area_in_<?=$idx?>">
							<?
							//댓글리스트
							//요청업무
							if($work_flag == '3'){?>
								<?if($comment_list[$work_com_idx]){?>
									<?for($k=0; $k<count($comment_list[$work_com_idx]['cidx']); $k++){
										$comment_idx = $comment_list[$work_com_idx]['cidx'][$k];

										$chis = $comment_list[$work_com_idx]['regdate'][$k];
										$ymd = $comment_list[$work_com_idx]['ymd'][$k];
										$cmt_flag = $comment_list[$work_com_idx]['cmt_flag'][$k];
										if($chis){
											$chis = str_replace("  "," ", $chis);
											$chis_tmp = @explode(" ", $chis);
											if ($chis_tmp['2'] == "PM"){
												$after = "오후 ";
											}else{
												$after = "오전 ";
											}
											$ctime = @explode(":", $chis_tmp['1']);
											$chiss = $ymd . " " . $after . $ctime['0'] .":". $ctime['1'];
										}

										$sql = "select a.name as name,a.comment as comment,a.send_name as send from work_todaywork_like a join work_todaywork_comment b";
										$sql = $sql." on a.com_idx = b.idx where a.state != 9 and a.com_idx = '".$comment_idx."'";
										$work_give_list = selectQuery($sql);

										if($work_give_list){
											$work_give_like_name = $work_give_list['name'];
											$work_give_like_comment = $work_give_list['comment'];
											$work_send_like_name = $work_give_list['send'];
										}

										// 코인보상 레이어 업무 요청한 사람만 보이게(김정훈)
										$sql = "select idx from work_todaywork where idx = '".$idx."' and work_idx is null";
										$work_link_coin = selectQuery($sql);

										$sql = "select a.idx from work_todaywork_like a join work_todaywork_comment b on a.work_idx = b.link_idx";
										$sql = $sql." where b.idx = '".$comment_idx."' and a.ai_like_idx = b.ai_like_idx and send_email = '".$user_id."'";
										$click_like = selectQuery($sql);

										$sql = "select a.idx from work_todaywork_like a join work_todaywork_comment b on a.work_idx = b.idx where b.idx = '".$comment_idx."'";
										$sql = $sql." and a.state = 0 and a.send_email = '".$user_id."'";
										$cli_like = selectQuery($sql);

										$sql = "select idx from work_todaywork_comment where idx = '".$comment_idx."' and email = '".$user_id."'";
										$my_like = selectQuery($sql);

										$sql = "select idx from work_todaywork_comment where idx = '".$comment_idx."' and like_email = '".$user_id."'";
										$my_coin_like = selectQuery($sql);

										//코인보상 표기(요청받음)
										$sql = "select link_idx from work_todaywork_comment where cmt_flag=1 and link_idx != work_idx and idx = '".$comment_idx."'";

										$coin_work = selectAllQuery($sql);

										if($coin_work){
											for($co_i=0; $co_i<count($coin_work['link_idx']); $co_i++){
												$coin_work_idx = $coin_work['link_idx'][$co_i];

												$sql = "select idx, email, reward_user, reward_name, coin,memo,date_format(regdate , '%m/%d/%y %l:%i:%s %p') regdate from work_coininfo";
												$sql = $sql." where state != 9 and code = 700";
												$sql = $sql." and coin_work_idx='".$coin_work_idx."' order by regdate desc";

												$coin_info_comment = selectAllQuery($sql);

												if($coin_info_comment){
													for($co_j=0; $co_j<count($coin_info_comment['idx']); $co_j++){

													$coin_info_r_idx = $coin_info_comment['idx'][$co_j];
													$coin_info_email = $coin_info_comment['email'][$co_j];
													$coin_info_r_email = $coin_info_comment['reward_user'][$co_j];
													$coin_info_r_name = $coin_info_comment['reward_name'][$co_j];
													$coin_info_r_coin = $coin_info_comment['coin'][$co_j];
													$coin_info_r_memo = $coin_info_comment['memo'][$co_j];
													$coin_info_r_regdate = $coin_info_comment['regdate'][$co_j];

													if($coin_info_r_coin>0){
														$coin_info_r_coin = number_format($coin_info_r_coin);
													}

													$coin_date = date("Y-m-d",strtotime($coin_info_r_regdate));
													$hour = date("H", strtotime($coin_info_r_regdate));
													$min = date("i", strtotime($coin_info_r_regdate));

													if($hour > 12){
														$hour = $hour - 12;
														$coin_info_r_time = $coin_date." 오후 ".$hour.":".$min;
													}else{
														$coin_info_r_time = $coin_date." 오전 ".$hour.":".$min;
													}
													?>

													<div class="tdw_list_memo_desc" id="comment_list_<?=$comment_idx?>" >
														<div class="tdw_list_memo_name"><?=$coin_info_r_name?></div>
														<p class="btn_req_100c" id="btn_req_100c" title="100코인"></p>
														<div class="tdw_list_memo_conts">
															<span class="tdw_list_memo_conts_txt"><?=$coin_info_r_coin?> <?=$coin_info_r_memo?></span>
															<em class="tdw_list_memo_conts_date"><?=$coin_info_r_time?>
															</em>
														</div>
													</div>

													<?
													}
												}
											}
										}

									?>
										<div class="tdw_list_memo_desc" id="comment_list_<?=$comment_idx?>" >

											<?if($cmt_flag == 1){?>
												<!-- 좋아요 변경으로 인한 코드 -->
												<?if($work_give_list){?>
													<div class="tdw_list_memo_name"><?=$work_send_like_name?></div>
												<?}else{?>
													<div class="tdw_list_memo_name ai">AI</div>
												<?}?>
											<?}else{?>
												<?if($cmt_flag != 2){?>
													<div class="tdw_list_memo_name"><?=$comment_list[$work_com_idx]['name'][$k]?></div>
												<?}?>
											<?}?>

											<!-- 좋아요 변경으로 인한 코드(김정훈) -->
											<?if($cmt_flag == 1){?>
												<?//좋아요 보낸 내역이 있을때
												if($work_give_list){?>
													<button class="btn_memo_jjim on" value="<?=$comment_idx?>"><span>좋아요</span></button>
												<?}?>
											<?}?>

											<div class="tdw_list_memo_conts">

												<?if(!$cmt_flag && $user_id==$comment_list[$work_com_idx]['email'][$k]){?>
													<span class="tdw_list_memo_conts_txt" id="tdw_list_memo_conts_txt_<?=$comment_idx?>"><?=textarea_replace($comment_list[$work_com_idx]['comment'][$k])?></span>
												<?}else if($cmt_flag == 1 && $work_give_list){?>
													<span class="tdw_list_memo_conts_txt"><?=textarea_replace($comment_list[$work_com_idx]['comment'][$k])?></span>
												<?}else{?>
													<?if($cmt_flag != 2){?>
														<span class="tdw_list_memo_conts_txt"><?=textarea_replace($comment_list[$work_com_idx]['comment'][$k])?></span>
													<?}?>
												<?}?>

												<?if($cmt_flag != 2){?>
													<em class="tdw_list_memo_conts_date"><?=$chiss?>
												<?}?>

													<?//ai글 일때, 공유요청한 사람만 뜨게
													if($cmt_flag == 1 && $work_link_coin && !$my_coin_like){?>
														<button class="btn_req_100c" id="btn_req_100c" title="100코인" value="<?=$comment_list[$work_com_idx]['cidx'][$k]?>"><span>100코인</span></button>
													<?}?>

													<?//자동 ai댓글?>
													<?if($cmt_flag == 1){?>

														<?if($work_link_coin && !$my_coin_like){?>
															<?if($click_like){?>
																<button class="btn_memo_jjim on" value="<?=$comment_idx?>"><span>좋아요</span></button>
															<?}else{?>
																<button class="btn_memo_jjim" id="btn_memo_jjim_<?=$comment_idx?>" value="<?=$comment_idx?>"><span>좋아요</span></button>
															<?}?>
														<?}?>

													<?}else{?>
														<?if($cmt_flag != 2){?>
															<?if(!$my_like){?>
																<?if($cli_like){?>
																	<button class="btn_memo_jjim on" value="<?=$comment_idx?>"><span>좋아요</span></button>
																<?}else{?>
																	<button class="btn_memo_jjim" id="btn_memo_jjim_<?=$comment_idx?>" value="<?=$comment_idx?>"><span>좋아요</span></button>
																<?}?>
															<?}?>
														<?}?>
													<?}?>

												<?if(!$cmt_flag && $user_id==$comment_list[$work_com_idx]['email'][$k]){?>
													<button class="btn_memo_del" id="btn_memo_del" value="<?=$comment_idx?>"><span>삭제</span></button>
												<?}?>

												<?if($cmt_flag != 2){?>
													</em>
												<?}?>

												<div class="tdw_list_memo_regi" id="tdw_list_memo_regi_<?=$comment_idx?>">
													<textarea name="" class="textarea_regi" id="tdw_comment_edit_<?=$comment_idx?>"><?=$comment_list[$work_com_idx]['comment_strip'][$k]?></textarea>
													<div class="btn_regi_box">
														<button class="btn_regi_submit" id="btn_comment_submit" value="<?=$comment_idx?>"><span>확인</span></button>
														<button class="btn_regi_cancel" id="btn_regi_cancel" value="<?=$comment_idx?>"><span>취소</span></button>
													</div>
												</div>
											</div>
										</div>

									<?}?>
								<?}?>

							<?}else{?>
								<?//받은업무
								if ($work_idx){?>
									<?

										//코인보상 표기(오늘업무idx번호, 코멘트idx번호)
										//(보고받음, 공유받음)
										work_memo_list($work_idx, $comment_idx);

									?>

									<?if($comment_list[$work_idx]){?>
										<?for($k=0; $k<count($comment_list[$work_idx]['cidx']); $k++){
											$comment_idx = $comment_list[$work_idx]['cidx'][$k];
											$chis = $comment_list[$work_idx]['regdate'][$k];
											$ymd = $comment_list[$work_idx]['ymd'][$k];
											$cmt_flag = $comment_list[$work_idx]['cmt_flag'][$k];

											if($chis){
												$chis = str_replace("  "," ", $chis);
												$chis_tmp = @explode(" ", $chis);
												if ($chis_tmp['2'] == "PM"){
													$after = "오후 ";
												}else{
													$after = "오전 ";
												}
												$ctime = @explode(":", $chis_tmp['1']);
												$chiss = $ymd . " " . $after . $ctime['0'] .":". $ctime['1'];
											}

											$sql = "select a.name as name,a.comment as comment,a.send_name as send from work_todaywork_like a join work_todaywork_comment b";
											$sql = $sql." on a.com_idx = b.idx where a.state != 9 and a.com_idx = '".$comment_idx."'";
											$work_give_list = selectQuery($sql);

											if($work_give_list){
												$work_give_like_name = $work_give_list['name'];
												$work_give_like_comment = $work_give_list['comment'];
												$work_send_like_name = $work_give_list['send'];
											}

											$sql = "select a.idx from work_todaywork_like a join work_todaywork_comment b on a.work_idx = b.idx where b.idx = '".$comment_idx."'";
											$sql = $sql." and a.state = 0 and a.send_email = '".$user_id."'";
											$cli_like = selectQuery($sql);

											?>

											<div class="tdw_list_memo_desc" id="comment_list_<?=$comment_idx?>" >

												<?if($cmt_flag == 1){?>
													<!-- 좋아요 변경으로 인한 코드 -->
													<?if($work_give_list){?>
														<div class="tdw_list_memo_name"><?=$work_send_like_name?></div>
													<?}else{?>
														<div class="tdw_list_memo_name ai">AI</div>
													<?}?>
												<?}else{?>
													<?if($cmt_flag != 2){?>
														<div class="tdw_list_memo_name"><?=$comment_list[$work_idx]['name'][$k]?></div>
													<?}?>
												<?}?>

												<!-- 좋아요 변경으로 인한 코드(김정훈) -->
												<?if($cmt_flag == 1){?>
													<?//좋아요 보낸 내역이 있을때
													if($work_give_list){?>
														<button class="btn_memo_jjim on" value="<?=$comment_idx?>"><span>좋아요</span></button>
													<?}?>
												<?}?>

												<div class="tdw_list_memo_conts">

													<?if(!$cmt_flag && $user_id==$comment_list[$work_idx]['email'][$k]){?>
														<!-- 일반 메모 -->
														<span class="tdw_list_memo_conts_txt" id="tdw_list_memo_conts_txt_<?=$comment_idx?>"><?=textarea_replace($comment_list[$work_idx]['comment'][$k])?></span>
													<?}else if($cmt_flag == 1 && $work_give_list){?>
														<!-- 좋아요 받았을 때 문장 -->
														<span class="tdw_list_memo_conts_txt"><?=textarea_replace($comment_list[$work_idx]['comment'][$k])?></span>
													<?}else{?>
														<?if($cmt_flag != 2){?>
															<!-- AI 문장 -->
															<span class="tdw_list_memo_conts_txt"><?=textarea_replace($comment_list[$work_idx]['comment'][$k])?></span>
														<?}?>
													<?}?>

													<?if($cmt_flag != 2){?>
														<em class="tdw_list_memo_conts_date"><?=$chiss?>
													<?}?>

													<?//자동 ai댓글?>
													<?if($cmt_flag == 1){?>

													<?}else{?>
														<?if($cmt_flag != 2){?>
															<?if($user_id!=$comment_list[$work_idx]['email'][$k]){?>
																<?if($cli_like){?>
																	<button class="btn_memo_jjim on" value="<?=$comment_idx?>"><span>좋아요</span></button>
																<?}else{?>
																	<button class="btn_memo_jjim" id="btn_memo_jjim_<?=$comment_idx?>" value="<?=$comment_idx?>"><span>좋아요</span></button>
																<?}?>
															<?}?>
														<?}?>
													<?}?>

													<?if(!$cmt_flag && $user_id==$comment_list[$work_idx]['email'][$k]){?>
														<button class="btn_memo_del" id="btn_memo_del" value="<?=$comment_idx?>"><span>삭제</span></button>
													<?}?>

													<?if($cmt_flag != 2){?>
														</em>
													<?}?>

													<div class="tdw_list_memo_regi" id="tdw_list_memo_regi_<?=$comment_idx?>">
														<textarea name="" class="textarea_regi" id="tdw_comment_edit_<?=$comment_idx?>"><?=$comment_list[$work_idx]['comment_strip'][$k]?></textarea>
														<div class="btn_regi_box">
															<button class="btn_regi_submit" id="btn_comment_submit" value="<?=$comment_idx?>"><span>확인</span></button>
															<button class="btn_regi_cancel" id="btn_regi_cancel" value="<?=$comment_idx?>"><span>취소</span></button>
														</div>
													</div>
												</div>
											</div>

										<?}?>

									<?}?>

								<?}else{?>
									<?

										//코인보상 표기(오늘업무idx번호, 코멘트idx번호)
										//(보고받음, 공유받음)
										work_memo_list($work_idx, $comment_idx);

									?>

									<?
									//일반업무
									if($comment_list[$idx]){?>

										<?for($k=0; $k<count($comment_list[$idx]['cidx']); $k++){
											$comment_idx = $comment_list[$idx]['cidx'][$k];
											$chis = $comment_list[$idx]['regdate'][$k];
											$ymd = $comment_list[$idx]['ymd'][$k];
											$cmt_flag = $comment_list[$idx]['cmt_flag'][$k];
											if($chis){
												$chis = str_replace("  "," ", $chis);
												$chis_tmp = @explode(" ", $chis);
												if ($chis_tmp['2'] == "PM"){
													$after = "오후 ";
												}else{
													$after = "오전 ";
												}
												$ctime = @explode(":", $chis_tmp['1']);
												$chiss = $ymd . " " . $after . $ctime['0'] .":". $ctime['1'];
											}

											$sql = "select a.name as name,a.comment as comment,a.send_name as send from work_todaywork_like a join work_todaywork_comment b";
											$sql = $sql." on a.com_idx = b.idx where a.state != 9 and a.com_idx = '".$comment_idx."'";
											$work_give_list = selectQuery($sql);

											if($work_give_list){
												$work_give_like_name = $work_give_list['name'];
												$work_give_like_comment = $work_give_list['comment'];
												$work_send_like_name = $work_give_list['send'];
											}

											$sql = "select a.idx from work_todaywork_like a join work_todaywork_comment b on a.work_idx = b.idx where b.idx = '".$comment_idx."'";
											$sql = $sql." and a.state = 0 and a.send_email = '".$user_id."'";
											$cli_like = selectQuery($sql);
										?>

										<div class="tdw_list_memo_desc" id="comment_list_<?=$comment_idx?>" >

											<?if($cmt_flag == 1){?>
												<!-- 좋아요 변경으로 인한 코드 -->
												<?if($work_give_list){?>
													<div class="tdw_list_memo_name"><?=$work_send_like_name?></div>
												<?}else{?>
													<div class="tdw_list_memo_name ai">AI</div>
												<?}?>
											<?}else{?>
												<?if($cmt_flag != 2){?>
													<div class="tdw_list_memo_name"><?=$comment_list[$idx]['name'][$k]?></div>
												<?}?>
											<?}?>

											<!-- 좋아요 변경으로 인한 코드(김정훈) -->
											<?if($cmt_flag == 1){?>
												<?//좋아요 보낸 내역이 있을때
												if($work_give_list){?>
													<button class="btn_memo_jjim on" value="<?=$comment_idx?>"><span>좋아요</span></button>
												<?}?>
											<?}?>


											<div class="tdw_list_memo_conts">

												<?if(!$cmt_flag && $user_id==$comment_list[$idx]['email'][$k]){?>
													<!-- 일반 메모 -->
													<span class="tdw_list_memo_conts_txt" id="tdw_list_memo_conts_txt_<?=$comment_idx?>"><?=textarea_replace($comment_list[$idx]['comment'][$k])?></span>
												<?}else if($cmt_flag == 1 && $work_give_list){?>
													<!-- 좋아요 받았을 때 문장 -->
													<span class="tdw_list_memo_conts_txt"><?=textarea_replace($comment_list[$idx]['comment'][$k])?></span>
												<?}else{?>
													<?if($cmt_flag != 2){?>
														<!-- AI 문장 -->
														<span class="tdw_list_memo_conts_txt"><?=textarea_replace($comment_list[$idx]['comment'][$k])?></span>
													<?}?>
												<?}?>

												<?if($cmt_flag != 2){?>
													<em class="tdw_list_memo_conts_date"><?=$chiss?>
												<?}?>

													<?//자동 ai댓글?>
													<?if($cmt_flag == 1){?>

													<?}else{?>
														<?if($cmt_flag != 2){?>
															<?if($user_id!=$comment_list[$idx]['email'][$k]){?>
																<?if($cli_like){?>
																	<button class="btn_memo_jjim on" value="<?=$comment_idx?>"><span>좋아요</span></button>
																<?}else{?>
																	<button class="btn_memo_jjim" id="btn_memo_jjim_<?=$comment_idx?>" value="<?=$comment_idx?>"><span>좋아요</span></button>
																<?}?>
															<?}?>
														<?}?>
													<?}?>

												<?if(!$cmt_flag && $user_id==$comment_list[$idx]['email'][$k]){?>
													<button class="btn_memo_del" id="btn_memo_del" value="<?=$comment_idx?>"><span>삭제</span></button>
												<?}?>

												<?if($cmt_flag != 2){?>
													</em>
												<?}?>

												<div class="tdw_list_memo_regi" id="tdw_list_memo_regi_<?=$comment_idx?>">
													<textarea name="" class="textarea_regi" id="tdw_comment_edit_<?=$comment_idx?>"><?=strip_tags($comment_list[$idx]['comment'][$k])?></textarea>
													<div class="btn_regi_box">
														<button class="btn_regi_submit" id="btn_comment_submit" value="<?=$comment_idx?>"><span>확인</span></button>
														<button class="btn_regi_cancel"><span>취소</span></button>
													</div>
												</div>
											</div>
										</div>
										<?}?>

									<?}?>
								<?}?>
							<?}?>
						</div>

						<?if($comment_list[$work_com_idx]){?>
							<div class="tdw_list_memo_onoff" <?=$memo_view_bt_style?>>
								<button class="btn_list_memo_onoff<?=$memo_view_bt?>" id="btn_list_memo_onoff_<?=$idx?>" value="<?=$idx?>" <?//if(trim($memo_view_bt)=="on"){ echo "title='메모 접기@@'"; }else{ echo "title='메모 펼치기@@'"; }?>><span>메모 접기/펼치기</span></button>
							</div>
						<?}?>
					</div>
				</li>
			<?php
			}
			?>
		</ul>
		<?php
		}else{
		?>
			<div class="tdw_list_none">
				<strong><span>현재 등록된 오늘업무가 없습니다.</span></strong>
			</div>
	<?php
		}

		//오늘한줄소감
		//if($review_info['idx']){?>
		<?if($wdate <= TODATE){?>
			<div class="tdw_feeling_banner<?=$review_info['idx']?" btn_ff_0".$review_info['work_idx']."":""?>" id="tdw_feeling_banner_<?=$wdate?>">
				<div class="tdw_fb_in">
					<strong></strong>
					<p id="feeling_banner_<?=$wdate?>"><?=$review_info['idx']?"".$review_info['comment']."":"오늘 하루는 어떤가요?"?></p>
					<button class="btn_feeling_banner" id="btn_feeling_banner_<?=$wdate?>" value="<?=$wdate?>"><span>오늘 한 줄 소감</span></button>
				</div>
			</div>
		<?}?>
		<?php
		//}


		//일일읽음처리
		work_read_check($user_id, "day", $wdate, "");
	//주간업무
	}else if($works_type=="week"){
		
		if($cate == 'share'){
			$cate_where = "and share_flag in ('1','2')";
		}else if($cate == 'report'){
			$cate_where = "and work_flag = '1'";
		}else if($cate == 'req'){
			$cate_where = "and work_flag = '3'";
		}

		php_timer();
		if(strpos($wdate, "~") !== false) {
			$wdate = trim($wdate);
			$tmp = explode("~", $wdate);
			$monthday = trim($tmp['0']);
			$sunday = trim($tmp['1']);
			$month = strtotime($monthday);
		}else{

			$date_tmp = explode("-",$wdate);
			$year = $date_tmp[0];
			$month = $date_tmp[1];
			$day = $date_tmp[2];
			$ret = week_day("$year-$month-$day");
			if($ret){

				//월요일
				$monthday = $ret['month'];

				//금요일
				$friday = $ret['friday'];

				//일요일
				$sunday = $ret['sunday'];

				//월요일, 타임으로
				$month = strtotime($monthday);


				if(strpos($monthday, "-") !== false) {
					$ex_monthday = str_replace("-", ".", $monthday);
				}

				if(strpos($sunday, "-") !== false) {
					$ex_sunday = str_replace("-", ".", $sunday);
				}

				$ex_wdate = $ex_monthday ." ~ ". $ex_sunday;
			}
		}


		//날짜 차이 계산
		$s_date = new DateTime($monthday);
		$e_date = new DateTime($sunday);
		$d_diff = date_diff($s_date, $e_date);
		$d_day = $d_diff->days + 1;


		//업무보고 받는사람, 보고보낸사람 정보
		$work_report_user = work_report_user($wdate);

		//업무요청 받는사람, 요청보낸사람 정보
		$work_req_user = work_req_user($wdate);

		//업무공유 받는사람, 공유보낸사람 정보
		$work_share_user = work_share_user($wdate);

		//첨부파일정보 불러오기
		$tdf_files = work_files_linfo($wdate);

		//업무 댓글
		$where = " and b.workdate between '".$monthday."' and '".$sunday."'";
		$sql = "select a.idx as cidx, a.link_idx, a.work_idx, a.email, a.name, a.comment, a.cmt_flag, CASE WHEN a.editdate is not null then date_format(a.editdate, '%Y-%m-%d') WHEN a.editdate is null then date_format(a.regdate, '%Y-%m-%d') end as ymd,";
		$sql = $sql .= " CASE WHEN a.editdate is not null then date_format( a.editdate , '%m/%d/%y %l:%i:%s %p') WHEN a.editdate is null then date_format( a.regdate , '%m/%d/%y %l:%i:%s %p') end as regdate, b.idx from work_todaywork_comment as a left join work_todaywork as b on(a.link_idx=b.idx) where a.state='0' and a.companyno='".$companyno."' ".$where." order by a.regdate desc";
		$works_comment_info = selectAllQuery($sql);

	
		for($i=0; $i<count($works_comment_info['idx']); $i++){
			$works_comment_info_idx = $works_comment_info['cidx'][$i];
			$works_comment_info_link_idx = $works_comment_info['link_idx'][$i];
			$works_comment_info_work_idx = $works_comment_info['work_idx'][$i];
			$works_comment_info_email = $works_comment_info['email'][$i];
			$works_comment_info_name = $works_comment_info['name'][$i];
			$works_comment_info_ymd = $works_comment_info['ymd'][$i];
			$works_comment_info_regdate = $works_comment_info['regdate'][$i];
			$works_comment_info_comment = $works_comment_info['comment'][$i];
			$works_comment_info_comment_strip = strip_tags($works_comment_info['comment'][$i]);
			$works_comment_info_cmt_flag = $works_comment_info['cmt_flag'][$i];

			if($works_comment_info_link_idx){
				$comment_list[$works_comment_info_link_idx]['cidx'][] = $works_comment_info_idx;
				$comment_list[$works_comment_info_link_idx]['name'][] = $works_comment_info_name;
				$comment_list[$works_comment_info_link_idx]['email'][] = $works_comment_info_email;
				$comment_list[$works_comment_info_link_idx]['ymd'][] = $works_comment_info_ymd;
				$comment_list[$works_comment_info_link_idx]['regdate'][] = $works_comment_info_regdate;
				$comment_list[$works_comment_info_link_idx]['comment'][] = $works_comment_info_comment;
				$comment_list[$works_comment_info_link_idx]['comment_strip'][] = $works_comment_info_comment_strip;
				$comment_list[$works_comment_info_link_idx]['cmt_flag'][] = $works_comment_info_cmt_flag;
			}
		}

		//오늘한줄소감
		$sql = "select idx, work_idx, comment, workdate, DATE_FORMAT( regdate , '%Y%m%d') as wdate from work_todaywork_review where state='0' and companyno='".$companyno."' and email='".$user_id."' and workdate between '".$monthday."' and '".$sunday."'";
		$review_info = selectAllQuery($sql);

		for($i=0; $i<count($review_info['idx']); $i++){
			$review_info_idx = $review_info['idx'][$i];
			$review_info_workdate = $review_info['workdate'][$i];
			$review_info_comment = $review_info['comment'][$i];
			$review_info_work_idx = $review_info['work_idx'][$i];
			$review_info_wdate = $review_info['wdate'][$i];

			$review_info_arr[$review_info_workdate]['idx'] = $review_info_idx;
			$review_info_arr[$review_info_workdate]['comment'] = $review_info_comment;
			$review_info_arr[$review_info_workdate]['work_idx'] = $review_info_work_idx;
			$review_info_arr[$review_info_workdate]['workdate'] = $review_info_workdate;
		}


		//파티연결정보
		$sql = "select work_idx, party_link from work_todaywork_project_info where state='0' and companyno='".$companyno."' and workdate between '".$monthday."' and '".$sunday."'";
		$project_data_info = selectAllQuery($sql);
		$project_link_info = @array_combine($project_data_info['work_idx'], $project_data_info['party_link']);




		//조건절
		$where = " and workdate between '".$monthday."' and '".$sunday."'";

		//보고업무
		$sql = "select idx, state, work_flag, decide_flag, email, name, work_idx, repeat_flag, notice_flag, share_flag, title, contents, workdate, date_format( regdate , '%m/%d/%y %l:%i:%s %p') as reg, date_format(regdate, '%H:%i') as his from work_todaywork where state!='9'";
		$sql = $sql .=" and companyno='".$companyno."' and work_flag='1' and work_idx is null".$where;
		$sql = $sql .= " order by sort asc, idx desc";
		$works_report_info = selectAllQuery($sql);

		for($i=0; $i<count($works_report_info['idx']); $i++){
			$work_report_idx = $works_report_info['idx'][$i];
			$work_report_title = $works_report_info['title'][$i];
			$work_report_contents = $works_report_info['contents'][$i];
			$work_report_email = $works_report_info['email'][$i];
			$work_report_name = $works_report_info['name'][$i];
			$work_report_workdate = $works_report_info['workdate'][$i];
			$work_report_reg = $works_report_info['reg'][$i];

			$work_report_list[$work_report_idx]['title'] = $work_report_title;
			$work_report_list[$work_report_idx]['contents'] = $work_report_contents;
			$work_report_list[$work_report_idx]['email'] = $work_report_email;
			$work_report_list[$work_report_idx]['name'] = $work_report_name;
			$work_report_list[$work_report_idx]['workdate'] = $work_report_workdate;
			$work_report_list[$work_report_idx]['reg'] = $work_report_reg;
		}


		//검색관련
		$search = $_POST['search'];
		$search_kind = $_POST['search_kind'];

		//검색어 있을때
		if($search){
			//검색 조건이 없는경우 전체로
			if(!$search_kind){
				$search_kind = "all";
			}else{
				$search_kind = trim($search_kind);
			}
		}

		//검색(업무종류)
		//works : 오늘업무,
		echo "\n\n";
		echo "search_kind === ". $search_kind;
		echo "\n\n";
		$search_work = " and (a.workdate between '".$monthday."' and '".$sunday."' or b.start_date between '".$monthday."' and '".$sunday."')";
		if($search_kind){
			switch($search_kind){

				//오늘업무
				case "works" :
					$search_work = " and work_flag='2' and contents like '%".$search."%'";
					//조건
					$search_work = $search_work .= " and workdate between '".$monthday."' and '".$sunday."'";
					break;

				//보고업무
				case "report" :
					$search_work = " and work_flag='1' and contents like '%".$search."%'";
					//조건
					$search_work = $search_work .= " and workdate between '".$monthday."' and '".$sunday."'";
					break;

				//요청업무
				case "req" :
					$search_work = " and work_flag='3' and contents like '%".$search."%'";
					//조건
					$search_work = $search_work .= " and workdate between '".$monthday."' and '".$sunday."'";
					break;

				//공유업무
				case "share" :
					$search_work = " and share_flag in(1,2) and contents like '%".$search."%'";
					//조건
					$search_work = $search_work .= " and workdate between '".$monthday."' and '".$sunday."'";
					break;

				//첨부파일
				case "file" :
					$search_work = " and a.file_flag='1' and b.file_real_name like '%".$search."%'";
					//조건
					$search_work = $search_work .= " and b.workdate between '".$monthday."' and '".$sunday."'";
					break;

				//메모
				case "memo" :
					$search_work = " and work_flag='2' and contents like '%".$search."%'";
					//조건
					$search_work = $search_work .= " and workdate between '".$monthday."' and '".$sunday."'";
					break;


				//전체
				case "all" :
					$search_work = " and a.contents like '%".$search."%'";
					//조건
					$search_work = $search_work .= " and (a.workdate between '".$monthday."' and '".$sunday."' or b.start_date between '".$monthday."' and '".$sunday."')";
					break;

				//전체
				default :
					$search_work = " and contents like '%".$search."%'";
					echo "########";
					break;
			}
		}


			//주간업무
			$sql ="select a.idx, a.state, b.state as calstate, a.work_flag, b.start_date, a.work_stime, a.secret_flag, a.work_etime, a.part_flag, a.decide_flag, a.work_idx, a.repeat_flag, a.notice_flag, a.share_flag, date_format( a.regdate , '%m/%d/%y %l:%i:%s %p') as reg, date_format(a.regdate, '%H:%i') as his, a.memo_view, a.contents_view, a.title, a.contents, a.contents1, ";
			$sql .=" a.email, a.name, a.req_date, a.workdate, a.regdate ";
			$sql .= " from work_todaywork a left join calendar_events b on a.idx = b.work_idx";
			$sql .= " where 1=1  and ((a.state NOT IN ('9', '99') AND (b.state IS NULL OR b.state <> '9')) OR (b.state = '0')) "; 
			$sql .= " and companyno='".$companyno."' and email='".$user_id."' ";
			$sql .= "$search_work";
			$sql .= " order by sort asc, idx desc";
		$week_info = selectAllQuery($sql);


		//결과가 없을때
		if(!$week_info['idx']){
			//검색 키워드, 검색 분류가 있을경우
			if($search && $search_kind){
				$list_result_text = "검색어로 입력한 `".$search."`에 대한 업무가 없습니다.";
			}else{
				$list_result_text = "현재 등록된 주간업무가 없습니다.";
			}
		}

		if($week_info['idx']){

			for($i=0; $i<count($week_info['idx']); $i++){
				$idx = $week_info['idx'][$i];
				$state = $week_info['state'][$i];
				$calstate = $week_info['calstate'][$i];
				$work_email = $week_info['email'][$i];
				$work_name = $week_info['name'][$i];
				$work_flag = $week_info['work_flag'][$i];
				$work_idx = $week_info['work_idx'][$i];
				$repeat_flag = $week_info['repeat_flag'][$i];
				$share_flag = $week_info['share_flag'][$i];
				$secret_flag = $week_info['secret_flag'][$i];
				$memo_view = $week_info['memo_view'][$i];
				$contents_view = $week_info['contents_view'][$i];
				$work_stime = $week_info['work_stime'][$i];
				$work_etime = $week_info['work_etime'][$i];

				$decide_flag = $week_info['decide_flag'][$i];
				$notice_flag = $week_info['notice_flag'][$i];
				if($week_info['start_date'][$i] == null){
					$workdate = $week_info['workdate'][$i];
				}else{
					$workdate = $week_info['start_date'][$i];
				}
				
				if($decide_flag == '1'){$decide_name = "연차";}else if($decide_flag == '2'){ $decide_name = "반차";}else if($decide_flag == '3'){$decide_name = "외출";}else if($decide_flag == '4'){$decide_name = "조퇴";}
				else if($decide_flag == '5'){$decide_name = "출장";}else if($decide_flag == '6'){$decide_name = "교육";}
				else if($decide_flag == '7'){$decide_name = "미팅";}else if($decide_flag == '8'){$decide_name = "회의";}

				$title = $week_info['title'][$i];
				$contents = $week_info['contents'][$i];
				$contents_edit = strip_tags($week_info['contents'][$i]);

				$his = $week_info['his'][$i];
				$ymd = $week_info['ymd'][$i];

				$week_works[$workdate]['idx'][] = $idx;
				$week_works[$workdate]['state'][] = $state;
				$week_works[$workdate]['calstate'][] = $calstate;
				$week_works[$workdate]['his'][] = $his;

				if ($ymd){
					$ymd_tmp = explode(".",$ymd);
					$ymd_change = $ymd_tmp[1].".".$ymd_tmp[2];
				}

				//요청 및 공유, 보고
				if($work_idx){
					$work_com_idx = $work_idx;
				}else{
					$work_com_idx = $idx;
				}

				$week_works[$workdate]['ymd'][] = $ymd_change;
				$week_works[$workdate]['title'][] = $title;
				$week_works[$workdate]['contents'][] = $contents;
				$week_works[$workdate]['contents_edit'][] = $contents_edit;
				$week_works[$workdate]['email'][] = $work_email;
				$week_works[$workdate]['decide_flag'][] = $decide_flag;
				$week_works[$workdate]['work_stime'][] = $work_stime;
				$week_works[$workdate]['work_etime'][] = $work_etime;
				$week_works[$workdate]['work_flag'][] = $work_flag;
				$week_works[$workdate]['work_idx'][] = $work_idx;
				$week_works[$workdate]['repeat_flag'][] = $repeat_flag;
				$week_works[$workdate]['notice_flag'][] = $notice_flag;
				$week_works[$workdate]['share_flag'][] = $share_flag;
				$week_works[$workdate]['secret_flag'][] = $secret_flag;
				$week_works[$workdate]['work_com_idx'][] = $work_com_idx;
				$week_works[$workdate]['memo_view'][] = $memo_view;
				$week_works[$workdate]['contents_view'][] = $contents_view;
			}

			//좋아요 보낸사람 리스트
			$sql = "select a.com_idx as com_idx, a.send_name as send,a.companyno,a.workdate from (select send_name,com_idx,state,companyno,workdate from work_todaywork_like where state != 9) a";
			$sql = $sql." join work_todaywork_comment b on a.com_idx = b.idx where a.companyno='".$companyno."' and a.workdate>='".$monthday."'";
			$work_give_list = selectAllQuery($sql);
			if($work_give_list){
				$work_send_like_name = array();
				$work_give_cnt = count($work_give_list[com_idx]);
				for($i=0; $i< $work_give_cnt; $i++){
					$com_idx = $work_give_list[com_idx][$i];
					$send = $work_give_list[send][$i];
					$work_send_like_name[$com_idx][send] = $send;
				}
				unset($com_idx);
				unset($send);
			}


			$sql = "select idx,link_idx from work_todaywork_comment where cmt_flag=1 and link_idx != work_idx and companyno='".$companyno."' and workdate>='".$monthday."'";
			$coin_work_l = selectAllQuery($sql);
			if($coin_work_l){
				$coin_work_cnt = count($coin_work_l[idx]);
				for($i=0; $i<$coin_work_cnt; $i++){
					$coin_work_idx = $coin_work_l[idx][$i];
					$coin_work_link_idx = $coin_work_l[link_idx][$i];
					$coin_work_arr_l[$coin_work_idx] = $coin_work_link_idx;
				}
				unset($coin_work_idx);
				unset($coin_work_link_idx);
			}

		

			$sql = "select idx,coin_work_idx, reward_user, reward_name,memo,coin";
			$sql = $sql." ,date_format( regdate , '%m/%d/%y %l:%i:%s %p') regdate from work_coininfo";
			$sql = $sql." where state != 9 and code = 700";
			$sql = $sql." and companyno='".$companyno."' and workdate between '".$monthday."' and '".$sunday."'";
			$sql = $sql." and coin_work_idx is not null order by regdate desc";
			$coin_info_comment = selectAllQuery($sql);
			for($j=0; $j<count($coin_info_comment[idx]); $j++){

				$coin_info_r_idx = $coin_info_comment[idx][$j];
				$coin_info_r_work_idx = $coin_info_comment[coin_work_idx][$j];
				$coin_info_email = $coin_info_comment[email][$j];
				$coin_info_r_email = $coin_info_comment[reward_user][$j];
				$coin_info_r_name = $coin_info_comment[reward_name][$j];
				$coin_info_r_coin = $coin_info_comment[coin][$j];
				if($coin_info_r_coin>0){
					$coin_info_r_coin = number_format($coin_info_r_coin);
				}
				$coin_info_r_memo = $coin_info_comment[memo][$j];
				$coin_info_r_regdate = $coin_info_comment[regdate][$j];

				$coin_info_arr[$coin_info_r_work_idx][idx][] = $coin_info_r_idx;
				$coin_info_arr[$coin_info_r_work_idx][coin_work_idx][] = $coin_info_r_work_idx;
				$coin_info_arr[$coin_info_r_work_idx][email][] = $coin_info_email;
				$coin_info_arr[$coin_info_r_work_idx][reward_user][] = $coin_info_r_email;
				$coin_info_arr[$coin_info_r_work_idx][reward_name][] = $coin_info_r_name;
				$coin_info_arr[$coin_info_r_work_idx][coin][] = $coin_info_r_coin;
				$coin_info_arr[$coin_info_r_work_idx][memo][] = $coin_info_r_memo;
				$coin_info_arr[$coin_info_r_work_idx][regdate][] = $coin_info_r_regdate;
			}

			
			for ($i=1, $day=$month; $i<=$d_day; $i++, $day += 86400){

				$workdate = date("Y-m-d", $day);
				$day_list = $week[date("w", $day)];

				$day_tmp = @explode(".", date("Y.m.d", $day));
				if($day_tmp){
					$week_day = $day_tmp[1].".".$day_tmp[2];
				}

				?>
				<div class="tdw_list_ww_box">
					<strong class="tdw_list_title_date"><?=$week_day?> (<?=$day_list?>)</strong>
					<ul class="tdw_list_ul">
						<?
						$week_works_cnt = count($week_works[$workdate][contents]);
						for($j=0; $j < $week_works_cnt; $j++){

							$idx = $week_works[$workdate][idx][$j];
							$work_flag = $week_works[$workdate][work_flag][$j];
							$work_idx = $week_works[$workdate][work_idx][$j];
							$state = $week_works[$workdate][state][$j];
							$repeat_flag = $week_works[$workdate][repeat_flag][$j];

							$work_wtitle = $week_works[$workdate][title][$j];
							$work_contents = $week_works[$workdate][contents][$j];
							$work_wtitle = $week_works[$workdate][title][$j];
							$work_contents_edit = $week_works[$workdate][contents_edit][$j];
							$work_email = $week_works[$workdate][email][$j];

							//반복설정
							$decide_flag = $week_works[$workdate][decide_flag][$j];

							$work_stime = $week_works[$workdate][work_stime][$j];
							$work_etime = $week_works[$workdate][work_etime][$j];

							//알림설정
							$notice_flag = $week_works[$workdate][notice_flag][$j];

							$share_flag = $week_works[$workdate][share_flag][$j];
							$secret_flag = $week_works[$workdate][secret_flag][$j];
							$work_com_idx = $week_works[$workdate][work_com_idx][$j];
							$memo_view = $week_works[$workdate][memo_view][$j];
							$contents_view = $week_works[$workdate][contents_view][$j];

							if($decide_flag == '1'){$decide_name = "연차";}else if($decide_flag == '2'){ $decide_name = "반차";}else if($decide_flag == '3'){$decide_name = "외출";}else if($decide_flag == '4'){$decide_name = "조퇴";}
							else if($decide_flag == '5'){$decide_name = "출장";}else if($decide_flag == '6'){$decide_name = "교육";}
							else if($decide_flag == '7'){$decide_name = "미팅";}else if($decide_flag == '8'){$decide_name = "회의";}

							$comment_list_work_com_cnt = count($comment_list[$work_com_idx][cidx]);
							$comment_list_work_cnt = count($comment_list[$work_idx][cidx]);
							$comment_list_cnt = count($comment_list[$idx][cidx]);

							if($repeat_flag == 1){
								$repeat_text = "매일반복";
							}else if($repeat_flag == 2){
								$repeat_text = "매주반복";
							}else if($repeat_flag == 3){
								$repeat_text = "매월반복";
							}else if($repeat_flag == 4){
								$repeat_text = "반복안함";
							}else{
								$repeat_text = "반복설정";
							}

							$memo_view_bt_style = "";
							//메모 접기/펼치기(0:펼치기, 1:접기)
							if($memo_view == '1'){
								$memo_view_in = " off";
								$memo_view_bt = " off memo_on";
								$memo_view_bt_style = " style=\"display: block;\"";

							}else{
								$memo_view_in = "";
								$memo_view_bt = " on";
								$memo_view_bt_style = "";
							}

							$report_view_bt_style = "";
							//보고업무 내용 접기/펼치기(0:펼치기, 1:접기)
							if($contents_view == '1'){
								$report_view_in = " off";
								$report_view_bt = " off memo_on";
								$report_view_bt_style = " style=\"display: block;\"";

							}else{
								$report_view_in = "";
								$report_view_bt = " on memo_on";
								$report_view_bt_style = "";
							}


							$share_view_bt_style = "";
							//공유업무 내용 접기/펼치기(0:펼치기, 1:접기)
							if($contents_view == '1'){
								$share_view_in = " off";
								$share_view_bt = " off";
								$share_view_bt_style = " off";

							}else{
								$share_view_in = "";
								$share_view_bt = " on";
								$share_view_bt_style = "";
							}

							$work_view_bt_style = "";
							//오늘업무 내용 접기/펼치기(0:펼치기, 1:접기)
							if($contents_view == '1'){
								$work_view_in = " off";
								$work_view_bt = " off";
								$work_view_bt_style = " off";

							}else{
								$work_view_in = "";
								$work_view_bt = " on";
								$work_view_bt_style = "";
							}

							$req_view_bt_style = "";
							//공유업무 내용 접기/펼치기(0:펼치기, 1:접기)
							if($contents_view == '1'){
								$req_view_in = " off";
								$req_view_bt = " off";
								$req_view_bt_style = " off";

							}else{
								$req_view_in = "";
								$req_view_bt = " on";
								$req_view_bt_style = "";
							}

							//읽음표시
							//요청업무
							$read_text="";
							$work_read_reading="";


							//챌린지 알림( notice_flag:1 )
							if($notice_flag){
								$work_title = "[".$notice_list[$notice_flag] ."]";
							}else{

								//보고업무
								if($work_flag == "1"){
									///$work_title = "";

									//보고받은 업무
									if($work_idx){
										$work_to_name = $work_report_user['receive'][$work_idx];
										$work_title = "[".$work_to_name ."님에게 보고받음]";

									}else{

										//보고 1명 이상인 경우
										$work_report_user_list_cnt = $work_report_user['send_cnt'][$work_com_idx];
										if($work_report_user_list_cnt > 1){
											$work_user_count = $work_report_user_list_cnt - 1;
											$work_report_user_title = $work_report_user['send'][$work_com_idx][0]. "님 외 ". $work_user_count . "명에게 보고함";
											$work_title = "[". $work_report_user_title. "]";
										}else{
											$work_report_user_title = $work_report_user['send'][$work_com_idx][0];
											$work_title = "[". $work_report_user_title. "님에게 보고함]";
										}

										$work_report_read_all = $work_report_user['read'][$idx][all];
										$work_report_read_cnt = $work_report_user['read'][$idx][read];
										$work_read_reading = $work_report_read_cnt;

										//읽지않은사용자
										if($work_read_reading>0){
											$read_text = "&nbsp;&nbsp;<em class=\"tdw_read\">읽음 ".$work_read_reading."</em>";
										}else{
											$read_text = "&nbsp;&nbsp;<em class=\"tdw_read\">읽음 0</em>";
										}

									}

									$work_contents = $work_wtitle;
									$work_contents_edit = $work_wtitle;

								//오늘업무
								}else if($work_flag == "2"){
									if($decide_list[$decide_flag]){
										$work_title = "<span>[".$decide_list[$decide_flag]."]</span>";
									}else{
										$work_title = "";
									}

									$work_contents = $work_contents;
									$work_contents_edit = $work_contents_edit;


								//업무요청
								}else if($work_flag == "3"){
									if($work_idx && $work_email == $user_id){
										$work_req_user_title = $work_req_user['receive'][$work_idx];
										$work_title = "[".$work_req_user_title ."님에게 요청받음]";
									}else{
										$work_user_list_cnt = $work_req_user['send_cnt'][$work_com_idx];
										if($work_user_list_cnt > 1){
											$work_user_count = $work_user_list_cnt - 1;
											$work_req_user_title = $work_req_user['send'][$work_com_idx][0]. "님 외 ". $work_user_count . "명에게 요청함";
											$work_title = "[". $work_req_user_title. "]";
										}else{
											$work_req_user_title = $work_req_user['send'][$work_com_idx][0];
											$work_title = "[". $work_req_user_title. "님에게 요청함]";
										}

										$work_req_read_all = $work_req_user['read'][$work_com_idx][all];
										$work_req_read_cnt = $work_req_user['read'][$work_com_idx][read];
										$work_read_reading = $work_req_read_cnt;

										if($work_read_reading>0){
											$read_text = "&nbsp;&nbsp;<em class=\"tdw_read\">읽음 ".$work_read_reading."</em>";
										}else{
											$read_text = "&nbsp;&nbsp;<em class=\"tdw_read\">읽음 0</em>";
										}

									}

									$work_contents = $work_contents;
									$work_contents_edit = $work_contents;
								}


								if($work_idx){
									$edit_id = "";

									//공유한 업무
									if($share_flag=='1'){
										$work_share_send_list_cnt = $work_share_user['send_cnt'][$idx];
										if($work_share_send_list_cnt > 1){
											$work_user_count = $work_share_send_list_cnt - 1;
											$work_share_user_title = $work_share_user['send'][$idx][0]. "님 외 ". $work_user_count . "명에게 공유함";
											$work_title = "[". $work_share_user_title. "]";
										}else{
											$work_share_user_title = $work_share_user['send'][$idx][0];
											$work_title = "[". $work_share_user_title. "님에게 공유함]";
										}


										$work_share_read_all = $work_share_user['read'][$idx][all];
										$work_share_read_cnt = $work_share_user['read'][$idx][read];
										$work_read_reading = $work_share_read_cnt;

										//읽지않은사용자
										if($work_read_reading>0){
											$read_text = "&nbsp;&nbsp;<em class=\"tdw_read\">읽음 ".$work_read_reading."</em>";
										}else{
											$read_text = "&nbsp;&nbsp;<em class=\"tdw_read\">읽음 0</em>";
										}

										$edit_id = " id='tdw_wlist_edit_".$idx."'";

									}else if($share_flag=='2'){

										$work_to_name = $work_share_user['receive'][$work_idx];
										$work_title = "[".$work_to_name."님에게 공유받음]";
									}
								}

							}

							//공유함($share_flag=1), 공유취소($share_flag=2), 요청받은업무($work_flag=3) 아이콘 변경
							$li_class = "";
							$tdw_list = false;
							if($share_flag=="1"){
								$li_class = " share";
							}else if($share_flag=="2"){
								$li_class = " share_cancel";
							}else if($work_flag == "2" && $share_flag == "0" && $notice_flag !="1"){
								$li_class = " work";
								$tdw_list = false;
							}else{
								//notice_flag=1 챌린지알림,
								//$work_flag=3 요청업무, $work_idx=null 요청보낸업무
								//$work_flag=3 요청업무, $work_idx 요청받은업무
								if($work_flag=='3' && $work_idx){
									$li_class = " req_get";
									$tdw_list = true;
								}else if($work_flag=='3' && $work_idx==null){
									$li_class = " req";
									$tdw_list = "";
								}else if($work_flag=='0' && $work_idx!=null){
									$li_class = " getreq";
									$tdw_list = true;
								}else if($work_flag == "1"){

									//보고받음
									if($work_idx){
										$li_class = " report_get";
										$tdw_list = false;
									}else{
										//보고함
										$li_class = " report";
										$tdw_list = false;
									}
								}else{

									//알림글
									if($notice_flag=="1"){
										$li_class = " challenges";
										$tdw_list = false;
									}else{
										$li_class = "";
										$tdw_list = true;
									}
								}
							}
						?>
							<li class="tdw_list_li<?=$li_class?>" id="workslist_<?=$idx?>">
								<div value= "<?=$week_works[$workdate]['state'][$j]?>" class="tdw_list_box<?=$week_works[$workdate]['state'][$j]=='1' || $week_works[$workdate]['calstate'][$j]=='1'?" on":""?><?=$share_view_bt_style?>" id="tdw_wlist_box_<?=$idx?>">
									<div class="tdw_list_chk">
										<button class="btn_tdw_list_chk" <?if($work_flag!='1'){?>value="<?=$idx?>"<?}?> id="tdw_wlist_chk"><span>완료체크</span></button>
									</div>
									<div class="tdw_list_desc <?=$secret_flag == '1'?"lock":""?>">

										<?if($work_idx){?>
											<?if($notice_flag=="1"){?>
												<p <?=$edit_id?> id="notice_link" value="<?=$work_idx?>">
											<?}else{?>
												<p <?=$edit_id?>>
											<?}?>
												<?=$work_title?"<span>".$work_title."</span>":""?><?=textarea_replace($work_contents)?><?=$read_text?>
											</p>
										<?}else{?>
											<?//보고업무
											if($work_flag == "1"){?>
												<p id="tdw_wlist_edit_<?=$idx?>">
												<?if($work_title){?>
													<span><?=$work_title?></span>
												<?}?><?=textarea_replace($work_contents)?><?=$read_text?></p>
											<?}else if($decide_flag > 0 && $work_stime != null && $work_etime != null){?>
												<p id="tdw_wlist_edit_<?=$idx?>">
													<?if($decide_flag == 1){?>
														<span> <?= "[ ".$decide_name." ]" ?></span><?=textarea_replace($work_contents)?>
													<?}else if($decide_flag > 1){?>
														<span> <?= "[ ".$decide_name."   ".$work_stime."~".$work_etime." ]" ?></span><?=textarea_replace($work_contents)?>
													<?}?>
												</p>
											<?}else{?>
												<p id="tdw_wlist_edit_<?=$idx?>">
												<?if($work_title){?>
													<span><?=$work_title?></span>
												<?}?><?=textarea_replace($work_contents)?><?=$read_text?></p>
											<?}?>
										<?}?>
									
									</div>
									
										<div class ="tdw_list_function">
											<div class="tdw_list_function_in">
												<?
												//받은업무
												//보고, 공유
												if($work_flag=="1" && $work_idx || $share_flag=='2' && $work_idx){?>
													<button class="tdw_list_100 tdw_list_100c" title="코인 보내기" id="tdw_list_100c" value="<?=$idx?>"><span>100</span></button>
												<?}?>
												

												<?
												//보고받은 업무
												if($work_flag=="1" && $work_idx){?>
													<button class="tdw_list_h tdw_list_reported_hart<?=$work_like_list[$work_idx]>0?" on":""?>" title="좋아요" <?=$work_like_list[$work_idx]>0?"":" id=\"tdw_list_jjim\""?> value="<?=$work_idx?>"><span>좋아요</span></button>
												<?
												//공유받음
												}else if($share_flag=='2' && $work_idx){?>
													<button class="tdw_list_h tdw_list_shared_hart<?=$work_like_list[$work_idx]>0?" on":""?>" title="좋아요" <?=$work_like_list[$work_idx]>0?"":" id=\"tdw_list_jjim\""?> value="<?=$work_idx?>"><span>좋아요</span></button>
												<?}else{?>

													<?//공유 보낸 업무?>
													<?if($share_flag=="1" && $work_idx){?>
														<?if($work_like_receive[$work_idx]){?>
															<button class="tdw_list_jjim_clear<?=$work_like_receive[$work_idx]>0?" on":""?>" title="좋아요" value="<?=$work_idx?>"><span>좋아요</span></button>
														<?}?>
													<?}?>

													<?//보고업무 보낸 업무?>
													<?if($work_flag=="1" && $work_idx==null){?>
														<?if($work_like_receive[$idx]){?>
															<button class="tdw_list_jjim_clear<?=$work_like_receive[$idx]>0?" on":""?>" title="좋아요"  value="<?=$work_idx?>"><span>좋아요</span></button>
														<?}?>
													<?}?>
												<?}?>
												<div class="tdw_list_drag" title="순서 변경" value="<?=$idx?>"><span>드래그 드랍 기능</span></div>
												<div class="tdw_list_more">
													<?if($work_flag != '4'){?>
													<button class="tdw_list_o" title="메뉴열기" id=""><span>메뉴열기</span></button>
													<?}?>
													<div class="tdw_list_1depth">
														<ul>
														<?if(($notice_flag=='0' || $decide_flag=='0') && $share_flag!=='2' && $notice_flag!='1' && $work_flag!='4'){?>
															<li>
																<button class="tdw_list_p tdw_list_party_link <?=$project_link_info[$idx]?"on":""?>" id="tdw_list_party_link" value="<?=$idx?>" title="파티연결"><span>파티연결</span></button>
															</li>
														<?}?>
														<?//공유하기?>
														<?//공유한 업무?>
														<?if($share_flag=='1' && $work_idx){?>
															<li>
																<button class="tdw_list_share_cancel tdw_list_s" id="tdw_list_share_cancel" value="<?=$idx?>" title="공유취소"><span>공유취소</span></button>
															</li>
														<?}else{?>
															<?//나의업무작성, 공유업무작성?>
															<?if(($work_flag=='2' && $work_idx==null) || ($share_flag=='1' && $work_idx==null)){?>
															<li>
																<button class="tdw_list_share tdw_list_s" id="tdw_list_share" value="<?=$idx?>" title="공유하기"><span>공유하기</span></button>
															</li>
																<?}?>
														<?}?>
														
														<?//파일첨부?>
														<?//파일첨부(나의업무, 공유업무작성, 보고업무작성, 요청업무작성)?>
														<?if(($work_flag=='2' && $work_idx==null) || ($share_flag=='1' && $work_idx) || ($work_flag=='1' && $work_idx==null) || ($work_flag=='3' && $work_idx==null)){?>
															<li>
																<button class="tdw_list_files tdw_list_f" id="tdw_file_add_<?=$idx?>" title="파일추가"><span>파일추가</span></button>
																<input type="file" id="files_add_<?=$idx?>" style="display:none;">
															</li>
														<?}?>
														
														<?//사람선택?>
														<?//공유업무작성, 보고업무작성, 요청업무작성?>
														<?if(($share_flag=='1' && $work_idx) || ($work_flag=='1' &&  $work_idx==null) || ($work_flag=='3' && $work_idx==null)){?>
															<li>
																<button class="tdw_list_user tdw_list_u" id="tdw_send_user_<?=$idx?>" value="<?=$idx?>" title="사람추가"><span>사람추가</span></button>
															</li>
														<?}?>
															<?//메모작성?>
														<? if($notice_flag!='1' && $work_flag!='4'){?>
															<?php if($secret_flag == '1'){?>
																<li>
																	<button class="tdw_list_memo_secret tdw_list_m" id="tdw_list_memo" value="<?=$idx?>" title="메모하기"><span>메모하기</span></button>
																</li>
															<?php }else{ ?>
																<li>
																	<button class="tdw_list_memo tdw_list_m" id="tdw_list_memo" value="<?=$idx?>" title="메모하기"><span>메모하기</span></button>
																</li>
															<?php } ?>	
															<?}?>
														<?if(($work_flag=='2' && $work_idx==null) || ($work_flag=='3' && $work_idx==null)){?>
															<? if(($repeat_flag && ($work_date < '2023-09-19')) || $repeat_work_idx != null){ ?>
																<li>
																	<button class="tdw_list_r <?=$repeat_flag?" on":""?>" id="tdw_list_repeat_info_new" value="<?php echo $idx?>"><span>반복설정</span></button>
																</li>
															<?php }else{?>
																<li>
																	<button class="tdw_list_r <?=$repeat_flag?" on":""?>" id="tdw_list_repeat_new" value="<?php echo $idx?>"><span>반복설정</span></button>
																</li>
															<?php } ?>
														<?php } ?>
														
														<?//일정변경?>
														<?//나의업무, 공유업무작성, 보고업무작성, 요청업무작성?>
														<?if(($work_flag=='2' && $work_idx==null) || ($share_flag=='1' && $work_idx==null) || ($work_flag=='1' && $work_idx==null) || ($work_flag=='3' && $work_idx==null)){?>
															<li>
																<div class ="tdw_list_c">
																	<input class="tdw_list_date tdw_list_cc" type="text" id="listdate_<?=$idx?>" value="날짜변경" readonly>
																</div>
															</li>
														<?}?>
														<?//일정변경?>
														<?//나의업무, 공유업무작성, 보고업무작성, 요청업무작성?>
														<?if(($work_stime && $work_etime && $work_flag == '2' && $share_flag == '0' && $state == '0' && $decide_flag > '1')){?>
															<li>
																<button class="tdw_list_time tdw_list_t" id="tdw_list_time" value="<?=$idx?>" title="시간변경"><span>시간변경</span></button>
															</li>
														<?}?>
														<li>
															<?if($work_flag!='4'){
																if($notice_flag){?>
																	<?if($user_id == $work_email){?>
																		<button class="tdw_list_del tdw_list_d" title="삭제" id="notice_list_del" value="<?=$idx?>"><span>삭제</span></button>
																	<?}else{?>
																		<button class="tdw_list_del tdw_list_d" title="삭제" value="<?=$idx?>"><span>삭제</span></button>
																	<?}?>
																<?}else{?>
																<?//업무글삭제?>
																	<?if($user_id == $work_email && $share_flag == 0 && $work_flag == 2){?>
																		<button class="tdw_list_del tdw_list_d" title="삭제" id="tdw_list_per_del" value="<?=$idx?>"><span>삭제</span></button>
																	<?}else if($user_id == $work_email){?>
																		<button class="tdw_list_del tdw_list_d" title="삭제" id="tdw_list_del" value="<?=$idx?>"><span>삭제</span></button>
																	<?}else{?>
																		<button class="tdw_list_del tdw_list_d" title="삭제" value="<?=$idx?>"><span>삭제</span></button>
																	<?}?>
																<?}
																}
															?>
														</li>
														<li>
															<button class="tdw_list_cancel" id="tdw_list_cancel" title="닫기"><span>닫기</span></button>
														</li>
														</ul>
													</div>
												</div>
											</div>
										</div>
									
									<?//첨부파일 정보
									//나의업무, 요청업무
									if(in_array($work_flag, array('2','3'))){
										if($tdf_files[$work_com_idx][file_path]){?>
											<div class="tdw_list_file">
												<?
												$tdf_files_cnt = count($tdf_files[$work_com_idx][file_path]);
												for($k=0; $k<$tdf_files_cnt; $k++){?>
													<div class="tdw_list_file_box">
														<button class="btn_list_file" id="tdw_list_file_<?=$tdf_files[$work_com_idx][num][$k]?>" value="<?=$tdf_files[$work_com_idx][idx][$k]?>"><span><?=$tdf_files[$work_com_idx][file_real_name][$k]?></span></button>
														<?//보고업무 작성한 사용자만 삭제
														if($user_id==$tdf_files[$work_com_idx][email][$k]){?>
															<button class="btn_list_file_del" id="btn_list_fdel" value="<?=$tdf_files[$work_com_idx][idx][$k]?>" title="삭제"><span>삭제</span></button>
														<?}?>
													</div>
												<?}?>
											</div>
										<?}?>
									<?}?>
								</div>


								<?//보고업무
								if($work_flag=='1'){

									if($work_idx == null){
										$report_email = $work_report_list[$idx]['email'];
										$report_name = $work_report_list[$idx]['name'];
										$report_contents = $work_report_list[$idx]['contents'];
										$report_workdate = $work_report_list[$idx]['workdate'];
										$report_reg = $work_report_list[$idx]['reg'];

										if($report_reg){
											$report_reg = str_replace("  "," ", $report_reg);
											$his_tmp = @explode(" ", $report_reg);
											if ($his_tmp['2'] == "PM"){
												$after = "오후 ";
											}else{
												$after = "오전 ";
											}
											$ctime = @explode(":", $his_tmp['1']);
											$work_his = $report_workdate . " " . $after . $ctime['0'] .":". $ctime['1'];
										}

									}else{
										$report_email = $work_report_list[$work_idx]['email'];
										$report_name = $work_report_list[$work_idx]['name'];
										$report_contents = $work_report_list[$work_idx]['contents'];
										$report_workdate = $work_report_list[$work_idx]['workdate'];
										$report_reg = $work_report_list[$work_idx]['reg'];

										if($report_reg){
											$report_reg = str_replace("  "," ", $report_reg);
											$his_tmp = @explode(" ", $report_reg);
											if ($his_tmp['2'] == "PM"){
												$after = "오후 ";
											}else{
												$after = "오전 ";
											}
											$ctime = @explode(":", $his_tmp['1']);
											$work_his = $report_workdate . " " . $after . $ctime['0'] .":". $ctime['1'];
										}

									}

								?>

									<div class="tdw_list_report_area">
										<div class="tdw_list_report_area_in<?=$report_view_in?>" id="tdw_list_report_area_in_<?=$idx?>">
											<div class="tdw_list_report_desc">
												<div class="tdw_list_report_conts">
													<?if($user_id==$report_email){?>
														<span class="tdw_list_report_conts_txt" id="tdw_list_report_conts_txt_<?=$idx?>"><?=textarea_replace($report_contents)?></span>
													<?}else{?>
														<span class="tdw_list_report_conts_txt"><?=textarea_replace($report_contents)?></span>
													<?}?>
													<em class="tdw_list_report_conts_date"><?=$work_his?></em>
													<div class="tdw_list_report_regi" id="tdw_list_report_regi_<?=$idx?>">
														<textarea name="" class="textarea_regi" id="tdw_report_edit_<?=$idx?>"><?=strip_tags($report_contents)?></textarea>
														<div class="btn_regi_box">
															<button class="btn_regi_submit" id="btn_report_submit" value="<?=$idx?>"><span>확인</span></button>
															<button class="btn_regi_cancel"><span>취소</span></button>
														</div>
													</div>
												</div>
											</div>

											<?//첨부파일 정보
											if($tdf_files[$work_com_idx]['file_path']){?>
												<div class="tdw_list_file">
													<?for($k=0; $k<count($tdf_files[$work_com_idx]['file_path']); $k++){?>
														<div class="tdw_list_file_box">
															<button class="btn_list_file" id="tdw_list_file_<?=$k?>" value="<?=$tdf_files[$work_com_idx]['idx'][$k]?>"><span><?=$tdf_files[$work_com_idx]['file_real_name'][$k]?></span></button>
															<?//보고업무 작성한 사용자만 삭제
															if($user_id==$report_email){?>
																<button class="btn_list_file_del" id="btn_list_fdel" value="<?=$tdf_files[$work_com_idx]['idx'][$k]?>" title="삭제"><span>삭제</span></button>
															<?}?>
														</div>
													<?}?>
												</div>
											<?}?>

										</div>


										<div class="tdw_list_report_onoff"<?=$report_view_bt_style?>>
											<button class="btn_list_report_onoff<?=$report_view_bt?>" id="btn_list_report_onoff_<?=$idx?>" value="<?=$idx?>" <?if(trim($report_view_bt)=="on"){ echo "title='보고 접기'"; }else{ echo "title='보고 펼치기'"; }?>><span>보고 접기/펼치기</span></button>
										</div>
									</div>
								<?}?>
								
								<?if($share_flag && $work_idx){?>
									<div class="tdw_list_share_onoff"<?=$share_view_bt_style?>>
										<button class="btn_list_share_onoff<?=($comment_list[$work_com_idx]?" memo_on": "");?><?=$share_view_bt?>" id="btn_list_share_onoff_<?=$idx?>" value="<?=$idx?>" <?if(trim($share_view_bt)=="on"){ echo "title='공유 접기'"; }else{ echo "title='공유 펼치기'"; }?>><span>공유 접기/펼치기</span></button>
									</div>
								<?}?>
								<!--
								 <?if($work_flag=='2' && $share_flag=='0' && $notice_flag == '0'){?>
									<div class="tdw_list_work_onoff"<?=$work_view_bt_style?>>
										<button class="btn_list_work_onoff<?=($comment_list[$work_com_idx]?" memo_on": "");?><?=$work_view_bt?>" id="btn_list_work_onoff_<?=$idx?>" value="<?=$idx?>" <?if(trim($work_view_bt)=="on"){ echo "title='공유 접기'";}else{ echo "title='업무 펼치기'"; }?>><span>업무 접기/펼치기</span></button>
									</div>
								<?}?> 
								 -->
								<?if($work_flag=='3'){?>
									<div class="tdw_list_req_onoff"<?=$req_view_bt_style?>>
										<button class="btn_list_req_onoff<?=($comment_list[$work_com_idx]?" memo_on": "");?><?=$req_view_bt?>" id="btn_list_req_onoff_<?=$idx?>" value="<?=$idx?>" <?if(trim($req_view_bt)=="on"){ echo "title='요청 접기'";}else{ echo "title='요청 펼치기'"; }?>><span>요청 접기/펼치기</span></button>
									</div>
								<?}?>
								<div class="tdw_list_memo_area">
									<div class="tdw_list_memo_area_in<?=$memo_view_in?>" id="tdw_list_memo_area_in_<?=$idx?>">

										<?//댓글리스트

										//요청업무
										if($work_flag == '3'){?>
											<?if($comment_list[$work_com_idx]){?>
												<?
												for($k=0; $k<$comment_list_work_com_cnt; $k++){
													$comment_idx = $comment_list[$work_com_idx][cidx][$k];

													$chis = $comment_list[$work_com_idx][regdate][$k];
													$ymd = $comment_list[$work_com_idx][ymd][$k];
													$cmt_flag = $comment_list[$work_com_idx][cmt_flag][$k];

													if($chis){
														$chis = str_replace("  "," ", $chis);
														$chis_tmp = @explode(" ", $chis);
														if ($chis_tmp[2] == "PM"){
															$after = "오후 ";
														}else{
															$after = "오전 ";
														}
														$ctime = @explode(":", $chis_tmp[1]);
														$chiss = $ymd . " " . $after . $ctime[0] .":". $ctime[1];
													}

													$coin_work_list_l = array();
													$coin_work_list_l = $coin_work_arr_l[$comment_idx];

													if($coin_info_arr[$coin_work_list_l]){
														$coin_info_cnt = count($coin_work_list_l);

														for($co_kj=0; $co_kj<$coin_info_cnt; $co_kj++){
															$coin_info_r_idx = $coin_info_arr[$coin_work_list_l][idx][$co_kj];
															$coin_info_r_email = $coin_info_arr[$coin_work_list_l][reward_user][$co_kj];
															$coin_info_r_name = $coin_info_arr[$coin_work_list_l][reward_name][$co_kj];
															$coin_info_r_coin = $coin_info_arr[$coin_work_list_l][coin][$co_kj];
															$coin_info_r_memo = $coin_info_arr[$coin_work_list_l][memo][$co_kj];
															$coin_info_r_regdate = $coin_info_arr[$coin_work_list_l][regdate][$co_kj];
															$coin_date = date("Y-m-d",strtotime($coin_info_r_regdate));
															$hour = date("H", strtotime($coin_info_r_regdate));
															$min = date("i", strtotime($coin_info_r_regdate));

															if($hour > 12){
																$hour = $hour - 12;
																$coin_info_r_time = $coin_date." 오후 ".$hour.":".$min;
															}else{
																$coin_info_r_time = $coin_date." 오전 ".$hour.":".$min;
															}
															?>
															<div class="tdw_list_memo_desc" id="comment_list_<?=$comment_idx?>#" >
																<div class="tdw_list_memo_name"><?=$coin_info_r_name?></div>
																<p class="btn_req_100c" id="btn_req_100c" title="100코인"></p>
																<div  class="tdw_list_memo_conts">
																	<span class="tdw_list_memo_conts_txt"><?=$coin_info_r_coin?> <?=$coin_info_r_memo?></span>
																	<em class="tdw_list_memo_conts_date"><?=$coin_info_r_time?></em>
																</div>
															</div>
														<?
														}
													}
												if($cmt_flag != 2){
												?>
													<div class="tdw_list_memo_desc" id="comment_list_<?=$comment_idx?>" >

														<?if($cmt_flag){?>
															<!-- 좋아요 변경으로 인한 코드 -->
															<?if($work_send_like_name[$comment_idx][send]){?>
																<div class="tdw_list_memo_name"><?=$work_send_like_name[$comment_idx][send]?></div>
															<?}else{?>
																<div class="tdw_list_memo_name ai">AI</div>
															<?}?>
														<?}else{?>
															<div class="tdw_list_memo_name" id="3"><?=$comment_list[$work_com_idx][name][$k]?></div>
														<?}?>

														<!-- 좋아요 변경으로 인한 코드(김정훈) -->
														<?if($cmt_flag){?>
															<?//좋아요 보낸 내역이 있을때
															if($work_send_like_name[$comment_idx][send]){?>
																<button class="btn_memo_jjim on" value="<?=$comment_idx?>"><span>좋아요</span></button>
															<?}?>
														<?}?>

														<div class="tdw_list_memo_conts">
															<?if(!$cmt_flag && $user_id==$comment_list[$work_com_idx][email][$k]){?>
																<span class="tdw_list_memo_conts_txt" id="tdw_list_memo_conts_txt_<?=$comment_idx?>"><?=textarea_replace($comment_list[$work_com_idx][comment][$k])?></span>
															<?}else if($cmt_flag && $work_send_like_name[$comment_idx][send]){?>
																<span class="tdw_list_memo_conts_txt"><?=textarea_replace($comment_list[$work_com_idx][comment][$k])?></span>
															<?}else{?>
																<span class="tdw_list_memo_conts_txt"><?=textarea_replace($comment_list[$work_com_idx][comment][$k])?></span>
															<?}?>

															<em class="tdw_list_memo_conts_date"><?=$chiss?>

																<?//ai글 일때, 공유요청한 사람만 뜨게
																if($cmt_flag && $work_link_coin_arr[$idx] && !$my_like_arr[$comment_idx]){?>
																	<button class="btn_req_100c" id="btn_req_100c" title="100코인" value="<?=$comment_list[$work_com_idx][cidx][$k]?>"><span>100코인</span></button>
																<?}?>

																<?//자동 ai댓글?>
																<?if($cmt_flag){?>

																	<?if($work_link_coin_arr[$idx] && !$my_like_arr[$comment_idx]){?>
																		<?if($click_like_arr[$comment_idx]){?>
																			<button class="btn_memo_jjim on" value="<?=$comment_idx?>"><span>좋아요</span></button>
																		<?}else{?>
																			<button class="btn_memo_jjim" id="btn_memo_jjim_<?=$comment_idx?>" value="<?=$comment_idx?>"><span>좋아요</span></button>
																		<?}?>
																	<?}?>

																<?}else{?>
																	<?if(!$my_like_arr[$comment_idx]){?>
																		<?if($cli_like_arr[$comment_idx]){?>
																			<button class="btn_memo_jjim on" value="<?=$comment_idx?>"><span>좋아요</span></button>
																		<?}else{?>
																			<button class="btn_memo_jjim" id="btn_memo_jjim_<?=$comment_idx?>" value="<?=$comment_idx?>"><span>좋아요</span></button>
																		<?}?>
																	<?}?>
																<?}?>

															<?if(!$cmt_flag && $user_id==$comment_list[$work_com_idx][email][$k]){?>
																<button class="btn_memo_del" id="btn_memo_del" value="<?=$comment_idx?>"><span>삭제</span></button>
															<?}?>
															</em>

															<div class="tdw_list_memo_regi" id="tdw_wlist_memo_regi_<?=$comment_idx?>">
																<textarea name="" class="textarea_regi" id="tdw_wcomment_edit_<?=$comment_idx?>"><?=$comment_list[$work_com_idx][comment_strip][$k]?></textarea>
																<div class="btn_regi_box">
																	<button class="btn_regi_submit" id="btn_wcomment_submit" value="<?=$comment_idx?>"><span>확인</span></button>
																	<button class="btn_regi_cancel" id="btn_wregi_cancel" value="<?=$comment_idx?>"><span>취소</span></button>
																</div>
															</div>
														</div>
													</div>
													<?}?>
												<?}?>
											<?}?>

										<?}else{?>

											<?//받은업무
												if ($work_idx){?>

														<?
														
														//코인보상 표기(오늘업무idx번호, 코멘트idx번호)
														//(보고받음, 공유받음)
														work_memo_list($work_idx, $comment_idx);

														if($comment_list[$work_idx]){

															for($k=0; $k<$comment_list_work_cnt; $k++){
																$comment_idx = $comment_list[$work_idx][cidx][$k];

																$chis = $comment_list[$work_idx][regdate][$k];
																$ymd = $comment_list[$work_idx][ymd][$k];
																$cmt_flag = $comment_list[$work_idx][cmt_flag][$k];
																if($chis){
																	$chis = str_replace("  "," ", $chis);
																	$chis_tmp = @explode(" ", $chis);
																	if ($chis_tmp[2] == "PM"){
																		$after = "오후 ";
																	}else{
																		$after = "오전 ";
																	}
																	$ctime = @explode(":", $chis_tmp[1]);
																	$chiss = $ymd . " " . $after . $ctime[0] .":". $ctime[1];
																}
															?>
															<div class="tdw_list_memo_desc" id="comment_list_<?=$comment_idx?>" >

																<?if($cmt_flag){?>
																	<!-- 좋아요 변경으로 인한 코드 -->
																	<?if($work_send_like_name[$comment_idx][send]){?>
																		<div class="tdw_list_memo_name"><?=$work_send_like_name[$comment_idx][send]?></div>
																	<?}else{?>
																		<div class="tdw_list_memo_name ai">AI</div>
																	<?}?>
																<?}else{?>
																	<div class="tdw_list_memo_name" id="2"><?=$comment_list[$work_idx][name][$k]?></div>
																<?}?>

																<!-- 좋아요 변경으로 인한 코드(김정훈) -->
																<?if($cmt_flag){?>
																	<?//좋아요 보낸 내역이 있을때
																	if($work_send_like_name[$comment_idx][send]){?>
																		<button class="btn_memo_jjim on" value="<?=$comment_idx?>"><span>좋아요</span></button>
																	<?}?>
																<?}?>

																<div class="tdw_list_memo_conts">
																	<?if(!$cmt_flag && $user_id==$comment_list[$work_idx][email][$k]){?>
																		<!-- 일반 메모 -->
																		<span class="tdw_list_memo_conts_txt" id="tdw_list_memo_conts_txt_<?=$comment_idx?>"><?=textarea_replace($comment_list[$work_idx][comment][$k])?></span>
																	<?}else if($cmt_flag && $work_send_like_name[$comment_idx][send]){?>
																		<!-- 좋아요 받았을 때 문장 -->
																		<span class="tdw_list_memo_conts_txt"><?=textarea_replace($comment_list[$work_idx][comment][$k])?></span>
																	<?}else{?>
																		<!-- AI 문장 -->
																		<span class="tdw_list_memo_conts_txt"><?=textarea_replace($comment_list[$work_idx][comment][$k])?></span>
																	<?}?>

																	<em class="tdw_list_memo_conts_date"><?=$chiss?>

																		<?//자동 ai댓글?>
																		<?if($cmt_flag){?>

																		<?}else{?>

																			<?if($user_id!=$comment_list[$work_idx][email][$k]){?>
																				<?if($cli_like_arr[$comment_idx]){?>
																					<button class="btn_memo_jjim on" value="<?=$comment_idx?>"><span>좋아요</span></button>
																				<?}else{?>
																					<button class="btn_memo_jjim" id="btn_memo_jjim_<?=$comment_idx?>" value="<?=$comment_idx?>"><span>좋아요</span></button>
																				<?}?>
																			<?}?>

																		<?}?>

																	<?if($user_id==$comment_list[$work_idx][email][$k]){?>
																		<button class="btn_memo_del" id="btn_memo_del" value="<?=$comment_idx?>"><span>삭제</span></button>
																	<?}?>
																	</em>

																	<div class="tdw_list_memo_regi" id="tdw_wlist_memo_regi_<?=$comment_idx?>">
																		<textarea name="" class="textarea_regi" id="tdw_wcomment_edit_<?=$comment_idx?>"><?=$comment_list[$work_idx][comment_strip][$k]?></textarea>
																		<div class="btn_regi_box">
																			<button class="btn_regi_submit" id="btn_wcomment_submit" value="<?=$comment_idx?>"><span>확인</span></button>
																			<button class="btn_regi_cancel" id="btn_wregi_cancel" value="<?=$comment_idx?>"><span>취소</span></button>
																		</div>
																	</div>
																</div>
															</div>
														<?}?>
													<?}?>

											<?}else{?>

												<?
												//일반업무
												if($comment_list[$idx]){?>
													<?
													for($k=0; $k<$comment_list_cnt; $k++){
														$comment_idx = $comment_list[$idx][cidx][$k];
														$chis = $comment_list[$idx][regdate][$k];
														$ymd = $comment_list[$idx][ymd][$k];
														$cmt_flag = $comment_list[$idx][cmt_flag][$k];
														if($chis){
															$chis = str_replace("  "," ", $chis);
															$chis_tmp = @explode(" ", $chis);
															if ($chis_tmp[2] == "PM"){
																$after = "오후 ";
															}else{
																$after = "오전 ";
															}
															$ctime = @explode(":", $chis_tmp[1]);
															$chiss = $ymd . " " . $after . $ctime[0] .":". $ctime[1];
														}

													?>

													<?
														$coin_work_list_i = array();
														$coin_work_list_i = $coin_work_arr_i[$idx];


														if($coin_info_arr[$coin_work_list_i]){
															$coin_info_cnt = count($coin_info_arr[$coin_work_list_i][idx]);

															for($co_kj=0; $co_kj<$coin_info_cnt; $co_kj++){
																$coin_info_r_idx = $coin_info_arr[$coin_work_list_i][idx][$co_kj];
																$coin_info_r_email = $coin_info_arr[$coin_work_list_i][reward_user][$co_kj];
																$coin_info_r_name = $coin_info_arr[$coin_work_list_i][reward_name][$co_kj];
																$coin_info_r_coin = $coin_info_arr[$coin_work_list_i][coin][$co_kj];
																$coin_info_r_memo = $coin_info_arr[$coin_work_list_i][memo][$co_kj];
																$coin_info_r_regdate = $coin_info_arr[$coin_work_list_i][regdate][$co_kj];

																$coin_date = date("Y-m-d",strtotime($coin_info_r_regdate));

																$hour = date("H", strtotime($coin_info_r_regdate));
																$min = date("i", strtotime($coin_info_r_regdate));

																if($hour > 12){
																	$hour = $hour - 12;
																	$coin_info_r_time = $coin_date." 오후 ".$hour.":".$min;
																}else{
																	$coin_info_r_time = $coin_date." 오전 ".$hour.":".$min;
																}
														?>
														<div class="tdw_list_memo_desc" id="comment_list_<?=$comment_idx?>!@" >
															<div class="tdw_list_memo_name"><?=$coin_info_r_name?></div>
															<p class="btn_req_100c" id="btn_req_100c" title="100코인"></p>
															<div  class="tdw_list_memo_conts">
																<span class="tdw_list_memo_conts_txt"><?=$coin_info_r_coin?> <?=$coin_info_r_memo?></span>
																<em class="tdw_list_memo_conts_date"><?=$coin_info_r_time?></em>
															</div>
														</div>
													<?
													}
												}

												$coin_work_list_w = array();
												$coin_work_list_w = $coin_work_arr_w[$idx];

												if($coin_info_arr[$coin_work_list_w][idx]){
													$coin_info_cnt = count($coin_info_arr[$coin_work_list_w][idx]);

													for($co_kj=0; $co_kj<$coin_info_cnt; $co_kj++){
														$coin_info_r_idx = $coin_info_arr[$coin_work_list_w][idx][$co_kj];
														$coin_info_r_email = $coin_info_arr[$coin_work_list_w][reward_user][$co_kj];
														$coin_info_r_name = $coin_info_arr[$coin_work_list_w][reward_name][$co_kj];
														$coin_info_r_coin = $coin_info_arr[$coin_work_list_w][coin][$co_kj];
														$coin_info_r_memo = $coin_info_arr[$coin_work_list_w][memo][$co_kj];
														$coin_info_r_regdate = $coin_info_arr[$coin_work_list_w][regdate][$co_kj];

														$coin_date = date("Y-m-d",strtotime($coin_info_r_regdate));

														$hour = date("H", strtotime($coin_info_r_regdate));
														$min = date("i", strtotime($coin_info_r_regdate));

														if($hour > 12){
															$hour = $hour - 12;
															$coin_info_r_time = $coin_date." 오후 ".$hour.":".$min;
														}else{
															$coin_info_r_time = $coin_date." 오전 ".$hour.":".$min;
														}

														?>
														<div class="tdw_list_memo_desc" id="comment_list_<?=$comment_idx?>!" >
															<div class="tdw_list_memo_name"><?=$coin_info_r_name?></div>
															<p class="btn_req_100c" id="btn_req_100c" title="100코인"></p>
															<div  class="tdw_list_memo_conts">
																<span class="tdw_list_memo_conts_txt"><?=$coin_info_r_coin?> <?=$coin_info_r_memo?></span>
																<em class="tdw_list_memo_conts_date"><?=$coin_info_r_time?></em>
															</div>
														</div>
													<?
													}
												}

													?>
														<div class="tdw_list_memo_desc" id="comment_list_<?=$comment_idx?>" >

															<?if($cmt_flag){?>
																<!-- 좋아요 변경으로 인한 코드 -->
																<?if($work_send_like_name[$comment_idx][send]){?>
																	<div class="tdw_list_memo_name"><?=$work_send_like_name[$comment_idx][send]?></div>
																<?}else{?>
																	<div class="tdw_list_memo_name ai">AI</div>
																<?}?>
															<?}else{?>
																<div class="tdw_list_memo_name" id="1"><?=$comment_list[$idx][name][$k]?></div>
															<?}?>

															<!-- 좋아요 변경으로 인한 코드(김정훈) -->
															<?if($cmt_flag){?>
																<?//좋아요 보낸 내역이 있을때
																if($work_send_like_name[$comment_idx][send]){?>
																	<button class="btn_memo_jjim on" value="<?=$comment_idx?>"><span>좋아요</span></button>
																<?}?>
															<?}?>

															<div class="tdw_list_memo_conts">
																<?if(!$cmt_flag && $user_id==$comment_list[$idx][email][$k]){?>
																	<!-- 일반 메모 -->
																	<span class="tdw_list_memo_conts_txt" id="tdw_list_memo_conts_txt_<?=$comment_idx?>"><?=textarea_replace($comment_list[$idx][comment][$k])?></span>
																<?}else if($cmt_flag && $work_send_like_name[$comment_idx][send]){?>
																	<!-- 좋아요 받았을 때 문장 -->
																	<span class="tdw_list_memo_conts_txt"><?=textarea_replace($comment_list[$idx][comment][$k])?></span>
																<?}else{?>
																	<!-- AI 문장 -->
																	<span class="tdw_list_memo_conts_txt"><?=textarea_replace($comment_list[$idx][comment][$k])?></span>
																<?}?>

																<em class="tdw_list_memo_conts_date"><?=$chiss?>

																	<?//자동 ai댓글?>
																	<?if($cmt_flag){?>

																	<?}else{?>

																		<?if($user_id!=$comment_list[$idx][email][$k]){?>
																			<?if($cli_like_arr[$comment_idx]){?>
																				<button class="btn_memo_jjim on" value="<?=$comment_idx?>"><span>좋아요</span></button>
																			<?}else{?>
																				<button class="btn_memo_jjim" id="btn_memo_jjim_<?=$comment_idx?>" value="<?=$comment_idx?>"><span>좋아요</span></button>
																			<?}?>
																		<?}?>

																	<?}?>

																<?if($user_id==$comment_list[$idx][email][$k]){?>
																	<button class="btn_memo_del" id="btn_memo_del" value="<?=$comment_idx?>"><span>삭제</span></button>
																<?}?>
																</em>

																<div class="tdw_list_memo_regi" id="tdw_wlist_memo_regi_<?=$comment_idx?>">
																	<textarea name="" class="textarea_regi" id="tdw_wcomment_edit_<?=$comment_idx?>"><?=$comment_list[$idx][comment_strip][$k]?></textarea>
																	<div class="btn_regi_box">
																		<button class="btn_regi_submit" id="btn_wcomment_submit" value="<?=$comment_idx?>"><span>확인</span></button>
																		<button class="btn_regi_cancel" id="btn_wregi_cancel" value="<?=$comment_idx?>"><span>취소</span></button>
																	</div>
																</div>
															</div>
														</div>
													<?}?>
												<?}?>
											<?}?>
										<?}?>
									</div>

									<?if($comment_list[$work_com_idx]){?>
										<div class="tdw_list_memo_onoff" <?=$memo_view_bt_style?>>
											<button class="btn_list_memo_onoff<?=$memo_view_bt?>" id="btn_list_memo_onoff_<?=$idx?>" value="<?=$idx?>" <?//if(trim($memo_view_bt)=="on"){ echo "title='메모 접기!'"; }else{ echo "title='메모 펼치기!'"; }?>><span>메모 접기/펼치기</span></button>
										</div>
									<?}?>
								</div>
							</li>
						<?}?>
					</ul>

					<?
					//오늘한줄소감(오늘까지만 노출되게함)
					//echo $workdate." === " . $review_info_arr[$review_info_workdate]['workdate'];
					//if($review_info_arr[$workdate]['work_idx']){?>
					<?if(!$search){?>
						<?if($workdate <= TODATE){?>
							<div class="tdw_feeling_banner<?=$review_info_arr[$workdate]['work_idx']?" btn_ff_0".$review_info_arr[$workdate]['work_idx']."":""?>" id="tdw_feeling_banner_<?=$workdate?>">
								<div class="tdw_fb_in">
									<strong></strong>
									<p id="feeling_banner_<?=$workdate?>"><?=$review_info_arr[$workdate]['comment']?"".$review_info_arr[$workdate]['comment']."":"오늘 하루는 어떤가요?"?></p>
									<button class="btn_feeling_banner" id="btn_feeling_banner_<?=$workdate?>" value="<?=$workdate?>"><span>오늘 한 줄 소감</span></button>
								</div>
							</div>
						<?}?>
					<?}?>
				</div>

			<?php
			echo "timer:::".php_timer();
			}

			echo "|".$ex_wdate;
		}else{?>
			<div class="tdw_list_none">
				<strong><span><?=$list_result_text?></span></strong>
			</div>

		<?php

		}

		//주간읽음처리
		work_read_check($user_id, "week", $monthday, $sunday);

	//월간업무
	}else if($works_type=="month"){


		//현재일자기준으로 한주간 체크
		$date_tmp = explode("-",$wdate);

		if($date_tmp){
			$year = $date_tmp[0];
			$month = $date_tmp[1];
			if(!$date_tmp[2]){
				$date_tmp[2] = "01";
			}
			$day = $date_tmp[2];
		}


		//$date = "$year-$month-$day"; // 현재 날짜
		//$time = strtotime($date); // 현재 날짜의 타임스탬프
		//$start_week = date('w', $time); // 1. 시작 요일
		//$total_day = date('t', $time); // 2. 현재 달의 총 날짜
		//$total_week = ceil(($total_day + $start_week) / 7);  // 3. 현재 달의 총 주차


	/*	$sel_day = date("t", mktime(0, 0, 0, $month, $day, $year));		// 지정된 달은 몇일까지 있을까요?
		$sel_yoil = date("N", mktime(0, 0, 0, $month, 1, $year));			// 지정된 달의 첫날은 무슨요일일까요?
		$day_null = $sel_yoil % 7; // 지정된 달 1일 앞의 공백 숫자.

		$day_line = ($sel_day + $day_null) / 7;
		$day_line = ceil($day_line);
		$day_line = $day_line-1; // 지정된 달은 총 몇주로 라인을 그어야 하나?
	*/

		$sel_day = date("t",mktime(0, 0, 0, $month, $day, $year));				// 지정된 달은 몇일까지 있을까요?
		$sel_yoil = date("N",mktime(0, 0, 0, $month, 1, $year));				// 지정된 달의 첫날은 무슨요일일까요?
		$day_line = $sel_yoil%7;												// 지정된 달 1일 앞의 공백 숫자.
		$ra = ($sel_day + $day_line)/7;
		$ra = ceil($ra);
		$ra = $ra-1;															// 지정된 달은 총 몇주로 라인을 그어야 하나?


		//챌린지 참여자 일부
		$chall_user_chk = $_POST['chall_user_chk'];

		//월간업무
		$ym = $year."-".$month;

		//오늘한줄소감
		$sql = "select idx, work_idx, comment, workdate as reg from work_todaywork_review where state='0' and companyno='".$companyno."' and email='".$user_id."' and date_format(workdate , '%Y-%m') ='".$ym."'";
		$review_info = selectAllQuery($sql);
		for($i=0; $i<count($review_info['idx']); $i++){
			$review_info_regdate = $review_info['reg'][$i];
			$review_info_comment = $review_info['comment'][$i];
			$review_info_work_idx = $review_info['work_idx'][$i];
			$review_info_arr[$review_info_regdate]['comment'] = $review_info_comment;
			$review_info_arr[$review_info_regdate]['work_idx'] = $review_info_work_idx;
		}

		$where = " and (date_format(a.workdate , '%Y-%m') ='".$ym."' or date_format(b.start_date , '%Y-%m') ='".$ym."')";
		$sql ="select a.idx, a.state, a.work_flag, b.state as calstate, b.start_date, a.part_flag, date_format(a.regdate , '%Y-%m-%d') ymd, date_format(a.regdate , '%H-%i') his, a.contents, a.contents1,";
		$sql .=" a.email, a.name, a.req_date, a.workdate, a.regdate ";
		$sql .= " FROM work_todaywork a left join calendar_events b on a.idx = b.work_idx ";
		$sql .= " where 1=1 and ((a.state NOT IN ('9', '99') AND (b.state IS NULL OR b.state <> '9')) OR (b.state = '0')) ";
		$sql .= " and companyno='".$companyno."' and email='".$user_id."'".$where."";
		$sql .= " order by idx desc";
		$month_info = selectAllQuery($sql);
		if($month_info['idx']){

			for($i=0; $i<count($month_info['idx']); $i++){

				$idx = $month_info['idx'][$i];
				$state = $month_info['state'][$i];
				$calstate = $month_info['calstate'][$i];
				if($month_info['start_date'][$i] == null){
					$workdate = $month_info['workdate'][$i];
				}else{
					$workdate = $month_info['start_date'][$i];
				}
				$contents = $month_info['contents'][$i];
				$ymd = $month_info['ymd'][$i];

				$month_works[$workdate]['idx'][] = $idx;
				$month_works[$workdate]['state'][] = $state;
				$month_works[$workdate]['calstate'][] = $calstate;
				$month_works[$workdate]['contents'][] = $contents;
				$month_works[$workdate]['ymd'][] = $ymd;
				$month_works[$workdate]['workdate'][] = $workdate;
			}
		}


	?>

			<div class="month_week">
				<ul>
					<li class="sun_day"><span>일</span></li>
					<li><span>월</span></li>
					<li><span>화</span></li>
					<li><span>수</span></li>
					<li><span>목</span></li>
					<li><span>금</span></li>
					<li><span>토</span></li>
				</ul>
			</div>
			<div class="month_in">
			<?php
				for($r=0; $r<=$ra; $r++){?>
					<?php
					for($z=1; $z<=7; $z++){?>
						<div class="month_box">
					<?
						$rv = 7 * $r + $z;
						$ru = $rv - $day_line;															// 칸에 번호를 매겨줍니다. 1일이 되기전 공백들 부터 마이너스 값으로 채운 뒤 ~
						$s = date("Y-m-d",mktime(0, 0, 0, $month, $ru, $year));							// 현재칸의 날짜
						$yoil = date("w", strtotime($s));
					?>
						<strong class="month_num<?=$yoil==0?" sun_day":""?>">
						<?php
						if($ru<=0 || $ru>$sel_day){?>
							</strong>
						<?php
						} // 딱 그달에 맞는 숫자가 아님 표시하지 말자
						else{
						?>
							<?=$ru?> </strong>
							<?if ($month_works[$s]['contents']){?>
								<button class="month_link">
									<ul>
										<?
										// 월간 숫자 안맞는 오류 수정(김정훈)
										$more = 0;
										for($i=0; $i<count($month_works[$s]['contents']); $i++){
											if ($i > 3){
												$more++;
											}else{?>
												<li <?=($month_works[$s]['state'][$i]=='1' || $month_works[$s]['calstate'][$i]=='1')?'class="comp"':''?> id="tdwlist_<?=$month_works[$s]['workdate'][$i]?>"><span><?=$month_works[$s]['contents'][$i]?></span></li>
											<?}?>
										<?}?>
										<?if($more > 0){?>
											<li class="month_more" id="tdwlist_<?=$month_works[$s]['workdate'][0]?>"><span>+<?=$more+1?></span></li>
										<?}?>
									</ul>
								</button>
								<?
								//오늘한줄소감
								if($review_info_arr[$month_works[$s]['workdate'][0]]['work_idx']){?>
									<strong id="tdwlist_<?=$month_works[$s]['workdate'][0]?>" class="month_feeling btn_ff_0<?=$review_info_arr[$month_works[$s]['workdate'][0]]['work_idx']?>"></strong>
								<?}?>
							<?}?>

					<?php
						}
					?>
						</div>
					<?php
					}?>
				<?php
				}
				?>
			</div>
	<?php
	}

	if($_COOKIE['read_date']){
		setcookie('read_date', '', time()-3600 , '/', C_DOMAIN);
	}


	exit;
}


//업무이동
if($mode == "works_move"){

	/*
	print "<pre>";
	print_r($_POST);
	print "</pre>";
	*/
	//workslist[]=617&workslist[]=615&workslist[]=614&workslist[]=613&workslist[]=616&workslist[]=612&workslist[]=611&workslist[]=610&workslist[]=609&workslist[]=608
	$wdate = $_POST['wdate'];
	$work_flag = $_POST['work_flag'];
	if($wdate){
		$wdate = str_replace(".", "-", $wdate);
	}

	if($_POST['listsort']){

		//일일업무
		//if($work_flag=="1"){
			$item = explode("&",$_POST['listsort']);
			$i=1;
			foreach ($item as $value) {
				$value = str_replace("workslist[]=", "", $value);
				$sql = "update work_todaywork set sort='".$i."' where idx='".$value."' and companyno='".$companyno."' and email='".$user_id."'";
				$up[] = updateQuery($sql);
				$i++;
			}
		//}
		/*//주간업무
		else if($work_flag=="2"){

			$item = explode("&",$_POST['listsort']);
			$i=1;
			foreach ($item as $value) {
				$value = str_replace("workslist[]=", "", $value);
				$sql = "update work_todaywork set week_sort='".$i."' where idx='".$value."' and email='".$user_id."'";
				$up[] = updateQuery($sql);
				$i++;
			}

		}else if($work_flag=="3"){

		}*/
	}

	if(count($up) == $i){
		echo "complete";
	}

	exit;
}


//오늘업무 알림삭제
if($mode == "works_notice_del"){

	$idx = $_POST['idx'];
	$idx = preg_replace("/[^0-9]/", "", $idx);

	//날짜변환
	$wdate = $_POST['wdate'];
	if($wdate){
		$wdate = str_replace(".", "-", $wdate);
	}

	if($idx){
		$sql = "select idx, state, work_idx , work_flag, notice_flag from work_todaywork where idx='".$idx."' and companyno='".$companyno."' and email='".$user_id."'";
		$res = selectQuery($sql);
		if($res['idx']){
			$res_idx = $res['idx'];

			if($res['notice_flag']){
				$sql = "update work_todaywork set state='9', editdate=".DBDATE." where companyno='".$companyno."' and idx='".$res_idx."'";
				$res = updateQuery($sql);
				if($res){
					echo "complete";
					exit;
				}
			}else{
				echo "del_not";
				exit;
			}
		}
	}
	exit;
}

// 오늘업무 삭제(자신 업무만 삭제)
if($mode == "works_per_del"){

	$idx = $_POST['idx'];
	$idx = preg_replace("/[^0-9]/", "", $idx);

	$wdate = $_POST['wdate'];
	if($wdate){
		$wdate = str_replace(".", "-", $wdate);
	}
	
	$sql = "update work_todaywork set state='9', editdate=".DBDATE." where companyno='".$companyno."' and idx='".$idx."'";
	$res = updateQuery($sql);

	$sql = "update calendar_events set state='9' where work_idx='".$idx."' and start_date = '".$wdate."'";
	$cal_up = updateQuery($sql);
	//삭제처리
	$sql = "update work_data_log set state='9', editdate=".DBDATE." where state='0' and companyno='".$companyno."' and work_idx='".$idx."'";
	$up = updateQuery($sql);
	echo "complete";
	exit;
}

//오늘업무삭제(보고,요청,공유 포함)
if($mode == "works_del"){

	$idx = $_POST['idx'];
	$idx = preg_replace("/[^0-9]/", "", $idx);

	$sql = "update work_alarm set state = '9' where work_idx = '".$idx."'";
	$query = updateQuery($sql);

	//날짜변환
	$wdate = $_POST['wdate'];
	if($wdate){
		$wdate = str_replace(".", "-", $wdate);
	}

	if($idx){
		$sql = "select idx, state, work_idx, work_flag, share_flag, file_flag, email, name, workdate from work_todaywork where idx='".$idx."' and companyno='".$companyno."' and email = '".$user_id."'";
		$res = selectQuery($sql);
		if($res['idx']){
			$res_idx = $res['idx'];
			$work_idx = $res['work_idx'];
			$work_flag = $res['work_flag'];
			$share_flag = $res['share_flag'];
			$file_flag = $res['file_flag'];
			$work_email = $res['email'];
			$work_name = $res['name'];
			$workdate = $res['workdate'];

			//공유받은사람
			if($share_flag=='2'){

				//튜토리얼 공유받은 업무 삭제하기
				$sql = "select idx from work_todaywork where state='99' and companyno='".$companyno."' and idx='".$res_idx."'";
				$tutorial_res = selectQuery($sql);
				if($tutorial_res['idx']){
					$sql = "update work_todaywork set state='9', editdate=".DBDATE." where companyno='".$companyno."' and idx='".$res_idx."'";
					$up = updateQuery($sql);
					if($up){
						//삭제성공
						echo "complete";
					}
					exit;
				}

				echo "share_not_del";
				exit;
			}

			//공유한업무
			if($share_flag=="1"){
				$sql = "select count(1) as cnt from work_todaywork where state ='0' and email = '".$user_id."' and workdate = '".TODATE."' and work_flag = '2' and share_flag = '1'";
				$cnt = selectQuery($sql);
				if($cnt['cnt'] <= 2){
					$sql = "update work_main_like set state = '9' where email = '".$user_id."' and kind = 'share' and workdate = '".TODATE."'";
					$main_up = updateQuery($sql);
				}

				//공유한 업무삭제
				$sql = "update work_todaywork set state='9', editdate=".DBDATE." where companyno='".$companyno."' and idx='".$res_idx."'";
				$up = updateQuery($sql);
				if($up){
					
					works_realtime_del('share', 'del', '1', $work_email, $work_name, $workdate);
					//공유받은 사람들 업무글삭제
					$sql = "update work_todaywork set state='9', editdate=".DBDATE." where companyno='".$companyno."' and work_idx='".$res_idx."'";
					$up_work = updateQuery($sql);

					if($up_work){
						//공유 받은 사용자 삭제
						$sql = "select idx, work_idx, email, name from work_todaywork_share where state='0' and companyno='".$companyno."' and work_idx='".$res_idx."'";
						$work_req_list = selectAllQuery($sql);

						if($work_req_list['idx'][0]){
							$sql = "update work_todaywork_share set state='9', editdate=".DBDATE." where companyno='".$companyno."' and work_idx='".$res_idx."'";
							$up = updateQuery($sql);
							if($up){
								//공유함
								work_data_log_del('9','4', $res_idx, $user_id, $user_name);
								//공유받음
								work_data_log_del('9','3', $res_idx, $user_id, $user_name);
							}
						}

						for($i=0; $i<count($work_req_list['idx']); $i++){
							//공유받은 업무 갯수 차감
							works_realtime_del('share', 'del', '1', $work_req_list['email'][$i], $work_req_list['name'][$i], $workdate);
						}

						//삭제성공
						echo "complete";
						exit;
					}
				}
			}else{
				//요청받은업무 삭제 안되도록
				if($work_flag=="3" && $work_idx){
					echo "work_not_del";
					exit;
				}else{
					if($work_flag=="1" && $work_idx){
						// echo "work_bogo_del";
						$sql = "update work_todaywork set state='9', editdate=".DBDATE." where companyno='".$companyno."' and idx='".$res_idx."'";
						$up_req = updateQuery($sql);
						echo "complete";
						exit;
					}
					//요청업무글, 보고, 나의업무
					if(in_array($work_flag, array("1","3"))){
						if($work_flag == "1"){
							$sql = "select count(1) as cnt from work_todaywork where state ='0' and email = '".$user_id."' and workdate = '".TODATE."' and work_flag = '1' and work_idx is null";
							$cnt = selectQuery($sql);
							if($cnt['cnt'] <= 1){
								$sql = "update work_main_like set state = '9' where email = '".$user_id."' and kind = 'report' and workdate = '".TODATE."'";
								$main_up = updateQuery($sql);
							}
						}

						//보고, 요청, 공유된 업무가 있을경우
						$sql = "update work_todaywork set state='9', editdate=".DBDATE." where companyno='".$companyno."' and idx='".$res_idx."'";
						$up_req = updateQuery($sql);
						if($up_req){
							//요청업무글 사용자
							$sql = "select idx, work_idx from work_todaywork_user where state='0' and companyno='".$companyno."' and work_idx='".$res_idx."'";
							$work_req_list = selectAllQuery($sql);
							if($work_req_list['idx'][0]){
								$sql = "update work_todaywork_user set state='9', editdate=".DBDATE." where companyno='".$companyno."' and work_idx='".$res_idx."'";
								$up = updateQuery($sql);
								if($up){
									//타임라인 삭제처리(요청받음)
									work_data_log_del('9','6', $res_idx, $user_id, $user_name);

									//타임라인 삭제처리(요청함)
									work_data_log_del('9','7', $res_idx, $user_id, $user_name);
								}
							}


							//보고업무 사용자
							$sql = "select idx, work_idx from work_todaywork_report where state='0' and companyno='".$companyno."' and work_idx='".$res_idx."'";
							$work_req_list = selectAllQuery($sql);
							if($work_req_list['idx'][0]){
								$sql = "update work_todaywork_report set state='9', editdate=".DBDATE." where companyno='".$companyno."' and work_idx='".$res_idx."'";
								$up_report = updateQuery($sql);
								if($up_report){
									//타임라인 삭제처리(보고받음)
									work_data_log_del('9','22', $res_idx, $user_id, $user_name);

									//타임라인 삭제처리(보고함)
									work_data_log_del('9','23', $res_idx, $user_id, $user_name);

								}
							}
						}
 
						//요청, 보고업무 정상삭제시 원본 업무글 삭제처리
						if($up_req || $up_report){
							//일일업무 삭제
							$sql = "update work_todaywork set state='9', editdate=".DBDATE." where companyno='".$companyno."' and work_idx='".$res_idx."'";
							$up = updateQuery($sql);
						}

						//삭제완료
						if($up){
							echo "complete";
							exit;
						}
							//일일업무 삭제
						//	$sql = "update work_todaywork set state='9', editdate=".DBDATE." where companyno='".$companyno."' and idx='".$res_idx."'";
						//	$up = updateQuery($sql);

					}else{
						$sql = "select count(1) as cnt from work_todaywork where state ='0' and email = '".$user_id."' and workdate = '".TODATE."' and work_flag = '2' and share_flag = '0'";
						$cnt = selectQuery($sql);
						if($cnt['cnt'] <= 7){
							$sql = "update work_main_like set state = '9' where email = '".$user_id."' and kind = 'works' and workdate = '".TODATE."'";
							$main_up = updateQuery($sql);
						}
						
						//일일업무 삭제
						$sql = "update work_todaywork set state='9', editdate=".DBDATE." where companyno='".$companyno."' and idx='".$res_idx."'";
						$res = updateQuery($sql);
						if($res){

							//타임라인(오늘업무삭제)
							work_data_log_del('9','2', $res_idx, $user_id, $user_name);

							echo "complete";
							exit;
						}
					}
				}
			}
		}
	}
	exit;
}


//오늘공유업무삭제
if($mode == "works_share_del"){

	$idx = $_POST['idx'];
	$idx = preg_replace("/[^0-9]/", "", $idx);

	//날짜변환
	$wdate = $_POST['wdate'];
	if($wdate){
		$wdate = str_replace(".", "-", $wdate);
	}

	if($idx){
		$sql = "select idx, state, work_idx, email, name, work_flag from work_todaywork where idx='".$idx."' and companyno='".$companyno."' and email='".$user_id."'";
		$res = selectQuery($sql);
		if($res['idx']){
			$res_idx = $res['idx'];

			//요청업무글
			/*if($res['work_flag'] == '3'){
				$sql = "select idx from work_todaywork where work_idx='".$res_idx."'";
				$work_info = selectQuery($sql);
				if($work_info['idx']){
					$sql = "update work_todaywork set state='9', editdate=".DBDATE." where work_idx='".$res_idx."'";
					$up = updateQuery($sql);

					//요청업무글 사용자
					$sql = "update work_todaywork_user set state='9', editdate=".DBDATE." where work_idx='".$res_idx."'";
					$up = updateQuery($sql);
				}
			}*/

			$sql = "update work_todaywork set state='9', editdate=".DBDATE." where companyno='".$companyno."' and idx='".$res_idx."'";
			$res = updateQuery($sql);
			if($res){

				//타임라인(업무공유함 삭제)
				work_data_log('9','4', $res_idx, $user_id, $user_name, $res['email'], $res['name']);

				//타임라인(업무공유받음 삭제)
				work_data_log('9','3', $res_idx, $res['email'], $res['name'], $user_id, $user_name );


				echo "complete";
				exit;
			}

		}
	}
	exit;
}


//업무완료체크
if($mode == "works_check"){

	$idx = $_POST['idx'];
	$idx = preg_replace("/[^0-9]/", "", $idx);

	//날짜변환
	$wdate = $_POST['wdate'];
	if($wdate){
		$wdate = str_replace(".", "-", $wdate);
	}

	if(!$user_id){
		echo "logout";
		exit;
	}

	if($idx){
		$sql = "select idx, state, work_flag, work_idx, share_flag, notice_flag, email from work_todaywork where idx='".$idx."' and companyno='".$companyno."' and email='".$user_id."'";
		$res = selectQuery($sql);
		//업무글이 있으면
		if($res['idx']){

			$idx = $res['idx'];
			$work_idx = $res['work_idx'];				//요청업무idx
			$work_flag = $res['work_flag'];
			$share_flag = $res['share_flag'];
			$notice_flag = $res['notice_flag'];
			
			$array = [1,2];
			if($work_flag == '2' && in_array($share_flag,$array)){
				echo "share";
				exit;
			}

			if($notice_flag == '1'){
				echo "notice";
				exit;
			}

			if($work_flag == '1'){
				echo "report";
				exit;
			}

			if($work_flag == '3' && $work_idx == ''){
				echo "request";
				exit;
			}

			//요청한 사용자가 완료 및 취소 처리시
			if ($work_flag=='3' && $work_idx==null){

				if($res['state']=='0'){
					echo "req_complete";
					exit;
				}else if($res['state']=='1'){
					echo "req_cancel";
					exit;
				}
				exit;
			}


			//요청 받은사용자
			if ($res['work_idx']){

				//공유한 업무
				if($res['share_flag']=='1'){
					$sql = "select idx, work_idx, work_email, work_name, email, name from work_todaywork_share where state='0' and companyno='".$companyno."' and work_idx='".$work_idx."' and work_email='".$user_id."'";
					$work_to_user_info = selectAllQuery($sql);
					if($work_to_user_info['idx']){
						$work_to_user_list['work_email'] = @array_combine($work_to_user_info['work_idx'], $work_to_user_info['work_email']);
						$work_to_user_list['email'] = @array_combine($work_to_user_info['work_idx'], $work_to_user_info['email']);
						$work_email_chk = $work_to_user_list['work_email'][$work_idx];
					}

				}else{

					//요청업무
					if($work_flag=='3'){
						$sql = "select idx, work_idx, work_email, work_name, email, name from work_todaywork_user where state='0' and companyno='".$companyno."' and work_idx='".$work_idx."' and email='".$user_id."'";
						$work_to_user_info = selectAllQuery($sql);
						if($work_to_user_info['idx']){
							$work_to_user_list['work_email'] = @array_combine($work_to_user_info['work_idx'], $work_to_user_info['work_email']);
							$work_to_user_list['email'] = @array_combine($work_to_user_info['work_idx'], $work_to_user_info['email']);
							$work_email_chk = $work_to_user_list['email'][$work_idx];
						}
					}
				}

				//요청받은 사람만 완료처리
				if ($user_id == $work_email_chk){

					if($res['state']=='1'){
						$sql = "update work_todaywork set state='0', editdate=".DBDATE." where companyno='".$companyno."' and idx='".$idx."'";
						$res = updateQuery($sql);

						//역량평가지표 업무요청 완료 해제
						work_cp_unreward("work","0006", $work_idx);

						// if($user_id=='adsb123@naver.com'){
						// 	echo "\n\n";
						// 	echo "work_idx ::: ". $idx;
						// 	echo "\n\n";
						// }
						//ai메모삭제
						memo_ai_del($idx);

						$work_req_info = work_req_info($idx);
						if($work_req_info){
							//업무요청 총갯수, 업무요청 완료수 다르면 요청했던 최초 업무글 업데이트 처리
							if ($work_req_info['req_tot'] != $work_req_info['req_com']){
								$sql = "update work_todaywork set state='0', editdate=".DBDATE." where state='1' and companyno='".$companyno."' and idx='".$work_req_info['work_idx']."'";
								$res2 = updateQuery($sql);
								if($res2){

									//역량평가지표(업무요청 완료 해제)
									work_cp_unreward("work","0006", $work_idx);
									echo "recomplete";
									exit;
								}
							}
						}

						if($res){
							//업무완료 해제
							echo "unclick";
							exit;
						}
					}else if($res['state']=='0'){
						$sql = "update work_todaywork set state='1', editdate=".DBDATE." where companyno='".$companyno."' and idx='".$idx."'";
						$res = updateQuery($sql);
						if($res){

							$req_user_id = $work_to_user_list['work_email'][$work_idx];

							//역량평가지표(받은 업무요청 완료), work, 0005, 사용자아이디, 업무번호, 받는사람아이디, 요청업무idx
							work_cp_reward("work", "0005", $user_id, $idx, $req_user_id, $work_idx);

							if($work_flag=='3'){
								$req_user_id = $work_to_user_list['work_email'][$work_idx];

								//메모작성(ai)
								memo_ai_write($idx, $work_idx, $user_id, $req_user_id, "" , "req","");
							}

						}

						//업무요청 총갯수, 업무요청 완료수 맞으면 업데이트 처리
						$work_req_info = work_req_info($idx);
						if($work_req_info){
							if ($work_req_info['req_tot'] == $work_req_info['req_com']){
								$sql = "update work_todaywork set state='1', editdate=".DBDATE." where state='0' and companyno='".$companyno."' and idx='".$work_idx."'";
								$res2 = updateQuery($sql);
								if($res2){
									//역량평가지표 업무요청 완료
									//work_cp_reward("work", "0004", $work_idx);

									//타임라인(업무요청완료)
									work_data_log('0','11', $work_idx, $user_id, $user_name);

									main_like_cp_works('works_complete');

									echo "complete";
									exit;
								}
							}
						}

						if($res){
							//업무완료 체크
							main_like_cp_works('works_complete');
							echo "complete";
							exit;
						}

					}else{
						echo "not";
						exit;
					}
				}

			}else{
				$sql = "select b.work_idx, b.state from work_todaywork a left join calendar_events b on a.idx = b.work_idx where 1=1 and b.work_idx = '".$idx."' and a.companyno='".$companyno."' and b.start_date = '".$wdate."'";
				$cal = selectQuery($sql);
				if($cal){		
					if($cal['state']=='1'){
						$sql = "update calendar_events set state='0' where work_idx='".$idx."' and start_date='".$wdate."'";
						$res = updateQuery($sql);
						if($res){

							//역량평가지표(오늘업무 완료 해제)
							work_cp_unreward("work","0002", $idx);

							//업무완료 해제
							echo "unclick";
							exit;
						}
					}else if($cal['state']=='0'){
						$sql = "update calendar_events set state='1' where work_idx='".$idx."' and start_date='".$wdate."'";
						$res = updateQuery($sql);
						if($res){

							//역량평가지표(오늘업무 완료), work, 0002, 사용자아이디, 업무번호, 받는사람아이디, 요청업무idx
							work_cp_reward("work", "0002", $user_id, $idx, "", "");

							//업무완료 체크
							main_like_cp_works('works_complete');
							echo "complete";
							exit;
						}
					}else{
						echo "not";
						exit;
					}
				}else{
					if($res['state']=='1'){
						$sql = "update work_todaywork set state='0', editdate=".DBDATE." where companyno='".$companyno."' and idx='".$idx."'";
						$res = updateQuery($sql);
						if($res){

							//역량평가지표(오늘업무 완료 해제)
							work_cp_unreward("work","0002", $idx);

							//업무완료 해제
							echo "unclick";
							exit;
						}
					}else if($res['state']=='0'){
						$sql = "update work_todaywork set state='1', editdate=".DBDATE." where companyno='".$companyno."'and idx='".$idx."'";
						$res = updateQuery($sql);
						if($res){

							//역량평가지표(오늘업무 완료), work, 0002, 사용자아이디, 업무번호, 받는사람아이디, 요청업무idx
							work_cp_reward("work", "0002", $user_id, $idx, "", "");

							//업무완료 체크
							main_like_cp_works('works_complete');
							echo "complete";
							exit;
						}
					}else{
						echo "not";
						exit;
					}
				}
			}
		}
	}
	exit;
}

//받을사람선택 이름 출력
if($mode == "works_user_name"){

	$work_user_chk = $_POST['work_user_chk'];
	if($work_user_chk){
		$work_mem_idx = trim($work_user_chk);
		if($work_mem_idx){
			$sql = "select idx, email, name from work_member where state='0' and companyno='".$companyno."' and idx in (".$work_mem_idx.") order by name asc";
			$work_mem_info = selectAllQuery($sql);
			if($work_mem_info['idx']){
				if(@in_array($user_id, $work_mem_info['email'])){
					echo "complete|ismy";
					exit;
				}else{
					$works_user_name = $work_mem_info['name'][0];
					echo "complete|".$works_user_name;
					exit;
				}
			}
		}
	}
	exit;
}


//받을사람 선택
if($mode == "user_check_desc"){

	$user_chk_val = $_POST['user_chk_val'];
	if($user_chk_val){

		$user_chk_ex = explode(",",$user_chk_val);
		$html = "";
		for($i=0; $i<count($user_chk_ex); $i++){
			$mem_idx = $user_chk_ex[$i];

			$mem_info = member_rowidx_info($mem_idx);
			$html = $html .= "<li><span>".$mem_info['name']."</span>";
			$html = $html .= "<button id='user_chk_del_".$mem_info['idx']."' value='".$mem_info['idx']."'>삭제</button>";
			$html = $html .= "</li>";
		}

		echo $html;
	}

	exit;
}

//주간날짜
if($mode == "date_change"){


	$wdate = $_POST['wdate'];
	$work_wdate = $_POST['work_wdate'];
	$day_type = $_POST['day_type'];
	$work_type = $_POST['work_type'];

	//날짜변환
	if($wdate){
		if(strpos($wdate, ".") !== false) {
			$wdate = str_replace(".", "-", $wdate);
		}
	}

	if($work_wdate){
		if(strpos($work_wdate, ".") !== false) {
			$work_wdate = str_replace(".", "-", $work_wdate);
		}
	}

	//일일
	if($day_type == "day"){
		if($work_wdate){
			if(strpos($work_wdate, "~") !== false) {
				$wdate = str_replace(" ","",$work_wdate);
				$work_wdate = str_replace("-", ".", $work_wdate);
				$tmp = explode("~", $work_wdate);

				$date1 = $tmp[0];
				$date2 = $tmp[1];
				$result = $date1;

			}else{
				$result = date("Y.m.d", strtotime($work_wdate));
			}

		}else{

			$result = date("Y.m.d", time());
		}

		echo $result;

	//주간
	}else if($day_type == "week"){



		$wdate = $work_wdate;

		if(strpos($wdate, "~") !== false) {

			$wdate = str_replace(" ","",$wdate);
			$tmp = explode("~", $wdate);
			$date1 = $tmp[0];
			$date2 = $tmp[1];
			$result = $date1 ." ~ ". $date2;
			echo $result;

		}else{

			//echo ">> ".$wdate;



			$ret = week_day($wdate);
			if($ret){

				$ret['month'] = str_replace("-", ".", $ret['month']);
				$ret['sunday'] = str_replace("-", ".", $ret['sunday']);

				//월요일
				$monthday = $ret['month'];

				//일요일
				$sunday = $ret['sunday'];

				$result = $monthday . " ~ " . $sunday;

				echo $result;
			}
		}

	//월간
	}else if($day_type == "month"){


		echo date("Y.m", time());
	}

	exit;
}



//오늘업무수정하기
if($mode == "tdw_regi_edit"){

	$idx = $_POST['idx'];
	$idx = preg_replace("/[^0-9]/", "", $idx);
	$contents = $_POST['contents'];
	//$contents = stripslashes(nl2br($contents));
	//$contents = nl2br($contents);

	//홑따옴표 때문에 아래와 같이 처리
	$contents = replace_text($contents);

	//if(strpos($contents, "'") !== false) {
	//	$contents = str_replace("'", "''", $contents);
	//}


	if($idx){
		$sql = "select idx, work_idx, work_flag, share_flag from work_todaywork where companyno='".$companyno."' and email='".$user_id."' and idx='".$idx."'";
		$res_info = selectQuery($sql);
		if($res_info['idx']){

			//보고업무:1, 나의업무:2, 요청:3
			$work_flag = $res_info['work_flag'];

			//보고업무
			if($work_flag =='1'){
				$sql = "update work_todaywork set title='".$contents."' where  companyno='".$companyno."' and email='".$user_id."' and idx='".$res_info['idx']."'";
				$res = updateQuery($sql);
			}else{
				$sql = "update work_todaywork set contents='".$contents."' where  companyno='".$companyno."' and email='".$user_id."' and idx='".$res_info['idx']."'";
				$res = updateQuery($sql);
			}

			//보고, 요청업무 업데이트 처리
			if(in_array($res_info['work_flag'] , array("1","3"))){
				$sql = "select idx from work_todaywork_user where companyno='".$companyno."' and work_idx='".$res_info['idx']."'";
				$list_user_info = selectAllQuery($sql);

				if($list_user_info['idx']){
					for($i=0; $i<count($list_user_info['idx']); $i++){
						$sql = "update work_todaywork_user set editdate=".DBDATE." where companyno='".$companyno."' and work_idx='".$list_user_info['idx'][$i]."'";
						$up2 = updateQuery($sql);

						$sql = "select idx from work_todaywork where companyno='".$companyno."' and work_idx='".$res_info['idx']."'";
						$req_info = selectQuery($sql);
						if($req_info['idx']){

							if($work_flag =='1'){
								$sql = "update work_todaywork set title='".$contents."', editdate=".DBDATE." where companyno='".$companyno."' and work_idx='".$res_info['idx']."'";
								$up3 = updateQuery($sql);
							}else{
								$sql = "update work_todaywork set contents='".$contents."', editdate=".DBDATE." where companyno='".$companyno."' and work_idx='".$res_info['idx']."'";
								$up3 = updateQuery($sql);

							}

						}
					}
				}
			}



			//공유한 업무 내용 수정
			if($res_info['share_flag'] == '1'){
				$sql = "select idx, work_idx from work_todaywork where companyno='".$companyno."' and work_idx='".$res_info['work_idx']."'";
				$work_info = selectAllQuery($sql);
				for($i=0; $i<count($work_info['idx']); $i++){
					$sql = "update work_todaywork set contents='".$contents."', editdate=".DBDATE." where companyno='".$companyno."' and work_idx='".$work_info['idx'][$i]."'";
					$up3 = updateQuery($sql);
				}
			}


			if($res){
				echo "complete";
				exit;
			}
		}
	}

	exit;
}



//업무 내일로 미루기
if($mode == "list_yesterday"){

	$idx = $_POST['idx'];
	$idx = preg_replace("/[^0-9]/", "", $idx);
	if($idx){
		$sql = "select idx, email, work_flag, work_idx, change_date, workdate from work_todaywork where companyno='".$companyno."' and idx='".$idx."' and email='".$user_id."'";
		$work_info = selectQuery($sql);
		if($work_info['idx']){
			//요청받은 업무
			$work_idx = $work_info['work_idx'];

			//업무 날짜 변경(1일 추가)
			$update = date("Y-m-d", strtotime("+1 day", strtotime($work_info['workdate'])));

			//요청업무 작성자가 업데이트 처리
			if($work_info['work_flag']=='3'){

				//업무 날짜 변경
				//$sql = "update work_todaywork set state='0', editdate=".DBDATE.", workdate = CONVERT(CHAR(10), dateadd(dd, 1, workdate) , 120) where idx='".$work_info['idx']."'";
				$sql = "update work_todaywork set state='0', editdate=".DBDATE.", workdate='".$update."' where companyno='".$companyno."' and idx='".$work_info['idx']."'";
				$up = updateQuery($sql);

				$sql = "select idx from work_todaywork_user where companyno='".$companyno."' and work_idx='".$work_info['idx']."'";
				$list_user_info = selectAllQuery($sql);
				if($list_user_info['idx']){
					for($i=0; $i<count($list_user_info['idx']); $i++){
						$sql = "update work_todaywork_user set workdate='".$update."', editdate=".DBDATE." where companyno='".$companyno."' and idx='".$list_user_info['idx'][$i]."'";
						$up2 = updateQuery($sql);
					}

					//요청한 업무 변경
					$sql = "select idx from work_todaywork where companyno='".$companyno."' and work_idx='".$work_info['idx']."'";
					$req_info = selectQuery($sql);
					if($req_info['idx']){
						$sql = "update work_todaywork set workdate='".$update."', editdate=".DBDATE." where companyno='".$companyno."' and work_idx='".$work_info['idx']."'";
						$up3 = updateQuery($sql);
					}
				}
			}else{

				//요청받은업무
				if($work_idx){

					//날짜가 다를경우
					//해당업무 날짜가 업데이트 할 날짜와 다른 경우만 처리
					if( $work_info['workdate'] != $update){

						//변경된 날짜의 데이터 조회
						$sql = "select idx from work_todaywork where state='0' and companyno='".$companyno."' and idx='".$work_idx."' and workdate='".$update."'";
						$list_row = selectQuery($sql);

						//변경된 날짜의 데이터 조회결과 없을 경우
						if(!$list_row['idx']){

							//기존의 데이터 조회
							$sql = "select idx, state, email, name, highlevel, type_flag, work_flag, part_flag, decide_flag, contents from work_todaywork where companyno='".$companyno."' and idx='".$work_idx."'";
							$data_row = selectQuery($sql);

							if($data_row['idx']){
								//해당 업무 날짜 변경을 1회 시도한 업무경우 업데이트
								if($work_info['change_date'] > 0){

									//요청한 업무 업데이트(최초 작성자)
									$sql = "update work_todaywork set workdate='".$update."', editdate=".DBDATE." where companyno='".$companyno."' and idx='".$work_idx."'";
									$up = updateQuery($sql);

									//요청받은 업무글
									$sql = "update work_todaywork set state='0', editdate=".DBDATE.", workdate ='".$update."', change_date=change_date + 1 where companyno='".$companyno."' and idx='".$work_info['idx']."'";
									$up = updateQuery($sql);

									//요청한 사용자 업데이트
									$sql = "select idx from work_todaywork_user where state='0' and companyno='".$companyno."' and work_idx='".$work_idx."' and email='".$user_id."'";
									$list_user_info = selectQuery($sql);
									if ($list_user_info['idx']){
										$sql = "update work_todaywork_user set work_idx='".$work_idx."', workdate='".$update."', editdate=".DBDATE." where companyno='".$companyno."' and idx='".$list_user_info['idx']."'";
										$up = updateQuery($sql);
									}

								}else{

									//요청한 사람내용 추가
									//날짜 추가로 인하여
									$sql = "insert into work_todaywork(companyno, state, email, name, highlevel, type_flag, work_flag, part_flag, decide_flag, contents, workdate, ip)";
									$sql = $sql .=" values('".$companyno."','".$data_row['state']."','".$data_row['email']."','".$data_row['name']."','".$data_row['highlevel']."','".$data_row['type_flag']."','".$data_row['work_flag']."','".$data_row['part_flag']."','".$data_row['decide_flag']."', '".$data_row['contents']."','".$update."','".LIP."')";
									$insert_idx = insertIdxQuery($sql);

									if($insert_idx){
										$sql = "select idx from work_todaywork_user where state='0' and companyno='".$companyno."' and work_idx='".$work_idx."' and email='".$user_id."'";
										$list_user_info = selectQuery($sql);
										if($list_user_info['idx']){
											$sql = "update work_todaywork_user set work_idx='".$insert_idx."', workdate='".$update."', editdate=".DBDATE." where companyno='".$companyno."' and idx='".$list_user_info['idx']."'";
											$up = updateQuery($sql);
										}

										//해당업무 업데이트처리(업무날짜, 업무날짜변경시간, 날짜변경횟수)
										$sql = "update work_todaywork set state='0', work_idx='".$insert_idx."', editdate=".DBDATE.", workdate = '".$update."', change_date=change_date + 1 where companyno='".$companyno."' and idx='".$work_info['idx']."'";
										$up = updateQuery($sql);
									}
								}
							}
						}

						if($up){
							echo "complete";
							exit;
						}
					}

				//일반업무
				}else{

					//업무 날짜 변경
					$sql = "update work_todaywork set state='0', editdate=".DBDATE.", workdate = DATE_FORMAT(date_add(workdate, INTERVAL 1 DAY), '%Y-%m-%d') where companyno='".$companyno."' and idx='".$work_info['idx']."'";
					$up = updateQuery($sql);

					//날짜변경시 업데이트처리
					//works_realtime('works', 'add', '0', $user_id, $user_name, $wdate);
				}
			}


			if($up){
				echo "complete";
				exit;
			}
		}
	}

}



//업무 일자 변경
if($mode == "works_date_change"){

	$idx = $_POST['idx'];
	$wdate = $_POST['wdate'];
	if($wdate){
		$tmp = explode("-", $wdate);
		$year = $tmp[0];
		$month = $tmp[1];
		$day = $tmp[2];

		//존재하는 날짜인지 체크
		$ret = checkdate($month, $day, $year);
	}

	$idx = preg_replace("/[^0-9]/", "", $idx);
	if($idx && $ret){
		$sql = "select idx, state, work_flag, work_idx, workdate, change_date from work_todaywork where idx='".$idx."' and companyno='".$companyno."' and email='".$user_id."'";
		$work_info = selectQuery($sql);
		if($work_info['idx']){

			//요청받은 업무
			$work_idx = $work_info['work_idx'];

			//오늘업무를 제외한 나머지 업무는 오늘 날짜 보다 이전으로 변경을 하지 못하게
			/*if($work_info['work_flag']!='1'){
				if( $wdate < TODATE ){
					//echo "prev_not";
					//exit;
				}
			}*/

			//요청업무 작성자 업데이트 처리
			if($work_info['work_flag']=='3'){
				//업무 날짜 변경
				$sql = "update work_todaywork set state='0', editdate=".DBDATE.", workdate='".$wdate."' where companyno='".$companyno."' and idx='".$work_info['idx']."'";
				$up = updateQuery($sql);

				//요청자 목록 변경
				$sql = "select idx from work_todaywork_user where companyno='".$companyno."' and work_idx='".$work_info['idx']."'";
				$list_user_info = selectAllQuery($sql);
				if($list_user_info['idx']){
					for($i=0; $i<count($list_user_info['idx']); $i++){
						$sql = "update work_todaywork_user set workdate='".$wdate."', editdate=".DBDATE." where companyno='".$companyno."' and idx='".$list_user_info['idx'][$i]."'";
						$up2 = updateQuery($sql);
					}

					//요청한 업무 변경
					$sql = "select idx from work_todaywork where companyno='".$companyno."' and work_idx='".$work_info['idx']."'";
					$req_info = selectQuery($sql);
					if($req_info['idx']){
						$sql = "update work_todaywork set workdate='".$wdate."', editdate=".DBDATE." where companyno='".$companyno."' and work_idx='".$work_info['idx']."'";
						$up3 = updateQuery($sql);
					}
				}

			}else{

				//요청받은업무
				if($work_idx){

					//날짜가 다를경우
					//해당업무 날짜가 업데이트 할 날짜와 다른 경우만 처리

					if( $work_info['workdate'] != $wdate){

						//변경된 날짜의 데이터 조회
						$sql = "select idx from work_todaywork where state='0' and companyno='".$companyno."' and idx='".$work_idx."' and workdate='".$wdate."'";
						$list_row = selectQuery($sql);

						//변경된 날짜의 데이터 조회결과 없을 경우
						if(!$list_row['idx']){

							//기존의 데이터 조회
							$sql = "select idx, state, email, name, highlevel, type_flag, work_flag, part_flag, decide_flag, contents from work_todaywork where state='0' and companyno='".$companyno."' and idx='".$work_idx."'";
							$data_row = selectQuery($sql);

							if($data_row['idx']){
								//해당 업무 날짜 변경을 1회 시도한 업무경우 업데이트
								if($work_info['change_date'] > 0){

									//요청한 업무 업데이트(최초 작성자)
									$sql = "update work_todaywork set workdate='".$wdate."', editdate=".DBDATE." where companyno='".$companyno."' and idx='".$work_idx."'";
									$up = updateQuery($sql);

									//요청받은 업무글
									$sql = "update work_todaywork set state='0', editdate=".DBDATE.", workdate='".$wdate."', change_date=change_date + 1 where companyno='".$companyno."' and idx='".$work_info['idx']."'";
									$up = updateQuery($sql);


									//공유한 사용자 업데이트
									$sql = "select idx from work_todaywork_share where state='0' and companyno='".$companyno."' and work_idx='".$work_idx."' and work_email='".$user_id."'";
									$list_user_info = selectQuery($sql);
									if ($list_user_info['idx']){
										$sql = "update work_todaywork_share set workdate='".$wdate."', editdate=".DBDATE." where companyno='".$companyno."' and idx='".$list_user_info['idx']."'";
										$up = updateQuery($sql);
										write_log_dir($sql , "update");
									}




								}else{

									//요청한 사람내용 추가
									//날짜 추가로 인하여
									$sql = "insert into work_todaywork(companyno, state, email, name, highlevel, type_flag, work_flag, part_flag, decide_flag, contents, workdate, ip)";
									$sql = $sql .=" values('".$companyno."','".$data_row['state']."','".$data_row['email']."','".$data_row['name']."','".$data_row['highlevel']."','".$data_row['type_flag']."','".$data_row['work_flag']."','".$data_row['part_flag']."','".$data_row['decide_flag']."', '".$data_row['contents']."','".$wdate."','".LIP."')";
									$insert_idx = insertIdxQuery($sql);

									if($insert_idx){
										$sql = "select idx from work_todaywork_user where state='0' and companyno='".$companyno."' and work_idx='".$work_idx."' and email='".$user_id."'";
										$list_user_info = selectQuery($sql);
										if($list_user_info['idx']){
											$sql = "update work_todaywork_user set work_idx='".$insert_idx."', workdate='".$wdate."', editdate=".DBDATE." where companyno='".$companyno."' and idx='".$list_user_info['idx']."'";
											$up = updateQuery($sql);
										}

										//해당업무 업데이트처리(업무날짜, 업무날짜변경시간, 날짜변경횟수)
										$sql = "update work_todaywork set state='0', work_idx='".$insert_idx."', editdate=".DBDATE.", workdate='".$wdate."', change_date=change_date + 1 where companyno='".$companyno."' and idx='".$work_info['idx']."'";
										$up = updateQuery($sql);
									}
								}
							}
						}

						if($up){
							echo "complete";
							exit;
						}
					}

				//일반업무
				}else{
					//업무 날짜 변경
					$sql = "update work_todaywork set editdate=".DBDATE.", workdate='".$wdate."' where companyno='".$companyno."' and idx='".$work_info['idx']."'";
					$up = updateQuery($sql);
				}
			}

			if($up){
				echo "complete";
				exit;
			}
		}
	}
	exit;
}

//업무 반복설정
if($mode == "works_repeat"){

	//작성한 업무 매일 반복설정
	$repeat = $_POST['repeat']; // repeat1,2,3,4
	$val = $_POST['val']; // todayworks_idx , 반복업무할 업무의 idx번호
	$repeat_idx = str_replace("repeat","", $repeat); //1,2,3,4만 남김
	$repeat_idx = preg_replace("/[^0-9]/", "", $repeat_idx);
	$val = preg_replace("/[^0-9]/", "", $val);

	if($val){
		$sql = "select idx, highlevel, day_flag, work_flag, share_flag, part_flag, type_flag, decide_flag, email, name, part, contents, workdate from work_todaywork where state in('0','1') and companyno='".$companyno."' and idx='".$val."'";
		$work_list = selectQuery($sql);
		// echo $val;
		// echo $work_list['idx'];
		if($work_list['idx']){ 
			$workidx = $work_list['idx']; 
			$workdate = $work_list['workdate'];
			$work_flag = $work_list['work_flag'];
			
			//1년 기간(conf.php 설정되어있음 : repeatday=365일 설정)
			$end_date = date("Y-m-d", strtotime("+".repeatday." day", TODAYTIME));
			///////
			//$sql = "select idx, email, name, part, partno, highlevel from work_member where state='0' and companyno='".$companyno."' and work_idx='".$work_list['idx']."'";
			//공유된 업무 반복설정 안됨
			if($share_flag=='1' || $share_flag=='2'){
				// echo "share_not";
				exit;
			}
			//요청업무
			if($work_flag=="3"){
				//요청한 사용자 내역
				$sql = "select idx, email, name from work_todaywork_user where state='0' and companyno='".$companyno."' and work_email='".$user_id."' and work_idx='".$workidx."' order by idx asc";
				$work_user_req_info = selectAllQuery($sql);

			//보고업무
			}else if($work_flag=="1"){
				$sql = "select idx, email, name from work_todaywork_report where state='0' and companyno='".$companyno."' and work_email='".$user_id."' and work_idx='".$workidx."' order by idx asc";
				$work_user_report_info = selectAllQuery($sql);

				//공유업무
			}else if($share_flag){
				$sql = "select idx, email, name from work_todaywork_share where state='0' and companyno='".$companyno."' and work_email='".$user_id."' and work_idx='".$workidx."' order by idx asc";
				$work_user_share_info = selectAllQuery($sql);
			}


			//매일반복
			if($repeat_idx == '1'){

				//시작일자
				$start_date = new DateTime($workdate);

				//종료일자
				$end_date = new DateTime($end_date);

				//시작일자와 종료일자간의 기간차이
				$diff_days = date_diff($start_date, $end_date);

				//기간 일수
				$diffdays = $diff_days->days;

				//반복설정 매일
				$repeat_type = "day";

				//반복 설정이 있는경우 삭제 처리 후 반복설정
				$sql = "select idx, repeat_flag, repeat_work_idx, workdate from work_todaywork where state='0' and companyno='".$companyno."' and repeat_flag!='0' and workdate='".$workdate."' and idx='".$work_list['idx']."'";
				$work_row = selectQuery($sql);
				if($work_row['idx']){
					//반복설정된 업무
					$sql = "select idx from work_todaywork where state='0' and companyno='".$companyno."' and repeat_work_idx='".$work_row['idx']."' and workdate > '".$work_row['workdate']."'";
					$work_info = selectQuery($sql);
					if($work_info['idx']){
						$sql = "update work_todaywork set state='9', editdate=".DBDATE." where state='0' and companyno='".$companyno."' and repeat_work_idx='".$work_row['idx']."' and workdate > '".$work_row['workdate']."'";
						$insert_idx = updateQuery($sql);
					}

					//$sql = "delete from work_todaywork where state='0' and repeat_flag='".$work_row['repeat_flag']."' and repeat_work_idx='".$work_row['idx']."' and workdate > '".$work_row['workdate']."'";
					//$insert_idx = updateQuery($sql);
				}

				$k=1;
				for($i=0; $i<$diffdays; $i++){
					$create_date = date('Y-m-d',strtotime('+'.$k.' day', TODAYTIME));
					$yoil_day = date('w', strtotime($create_date));
					//토,일 제외
					//if (!in_array($yoil_day , array('0','6'))){
						$sql = "select idx from work_todaywork where state='0' and companyno='".$companyno."' and workdate='".$create_date."' and contents='".$work_list['contents']."' ";
						$work_row = selectQuery($sql);
						if(!$work_row['idx']){
							$sql = "insert into work_todaywork(companyno, highlevel, day_flag, work_flag, part_flag, type_flag, decide_flag, repeat_flag, repeat_work_idx, email, name, part, contents, workdate, ip)values(";
							$sql = $sql .= "'".$companyno."','".$work_list['highlevel']."','".$work_list['day_flag']."','".$work_flag."','".$work_list['part_flag']."','".$work_list['type_flag']."','".$work_list['decide_flag']."',";
							$sql = $sql .= "'".$repeat_idx."','".$work_list['idx']."','".$work_list['email']."','".$work_list['name']."','".$work_list['part']."','".$work_list['contents']."','".$create_date."','".LIP."')";
							$insert_idx = insertIdxQuery($sql);

							if($insert_idx){

								//요청업무
								if($work_flag=="3"){
									//요청한 사용자 내역 저장
									if($work_user_req_info['idx']){

										for($j=0; $j<count($work_user_req_info['idx']); $j++){
											$work_user_req_info_email = $work_user_req_info['email'][$j];
											$work_user_req_info_name = $work_user_req_info['name'][$j];

											//등록한 사용자
											$sql = "select idx from work_todaywork_user where state='0' and companyno='".$companyno."' and email='".$work_user_req_info_email."' and work_idx='".$insert_idx."' and workdate='".$create_date."'";
											$work_req_user_list = selectQuery($sql);
											if(!$work_req_user_list['idx']){
												$sql = "insert into work_todaywork_user(work_idx,companyno,work_email,work_name,email,name,ip,workdate) values(";
												$sql = $sql .= "'".$insert_idx."','".$companyno."','".$user_id."','".$user_name."','".$work_user_req_info_email."','".$work_user_req_info_name."','".LIP."','".$create_date."')";
												insertQuery($sql);
											}

											//요청받은 사용자
											$sql = "select idx from work_todaywork where state='0' and companyno='".$companyno."' and work_flag='".$work_flag."' and contents='".$work_list['contents']."' and email='".$work_user_req_info_email."' and workdate='".$create_date."'";
											$work_todaywork_info = selectQuery($sql);
											if(!$work_todaywork_info['idx']){

												$sql = "insert into work_todaywork(companyno, highlevel, day_flag, work_flag, part_flag, type_flag, decide_flag, repeat_flag, repeat_work_idx, work_idx, email, name, part, contents, workdate, ip)values(";
												$sql = $sql .= "'".$companyno."','".$work_list['highlevel']."','".$work_list['day_flag']."','".$work_flag."','".$work_list['part_flag']."','".$work_list['type_flag']."','".$work_list['decide_flag']."',";
												$sql = $sql .= "'".$repeat_idx."','".$workidx."', '".$insert_idx."','".$work_user_req_info_email."','".$work_user_req_info_name."','".$work_list['part']."','".$work_list['contents']."','".$create_date."','".LIP."')";
												$insert_work_idx = insertIdxQuery($sql);
											}
										}


									}
								//보고업무
								}else if($work_flag=="1"){


									if($work_user_report_info['idx']){

										for($j=0; $j<count($work_user_report_info['idx']); $j++){
											$work_user_report_info_email = $work_user_report_info['email'][$j];
											$work_user_report_info_name = $work_user_report_info['name'][$j];

											//등록한 사용자
											$sql = "select idx from work_todaywork_report where state='0' and companyno='".$companyno."' and email='".$work_user_report_info_email."' and work_idx='".$insert_idx."' and workdate='".$create_date."'";
											$work_req_user_list = selectQuery($sql);
											if(!$work_req_user_list['idx']){
												$sql = "insert into work_todaywork_report(work_idx,companyno,work_email,work_name,email,name,ip,workdate) values(";
												$sql = $sql .= "'".$insert_idx."','".$companyno."','".$user_id."','".$user_name."','".$work_user_report_info_email."','".$work_user_report_info_name."','".LIP."','".$create_date."')";
												insertQuery($sql);
											}

											//요청받은 사용자
											$sql = "select idx from work_todaywork where state='0' and companyno='".$companyno."' and work_flag='".$work_flag."' and contents='".$work_list['contents']."' and email='".$work_user_report_info_email."' and workdate='".$create_date."'";
											$work_todaywork_info = selectQuery($sql);
											if(!$work_todaywork_info['idx']){

												$sql = "insert into work_todaywork(companyno, highlevel, day_flag, work_flag, part_flag, type_flag, decide_flag, repeat_flag, repeat_work_idx, work_idx, email, name, part, contents, workdate, ip)values(";
												$sql = $sql .= "'".$companyno."','".$work_list['highlevel']."','".$work_list['day_flag']."','".$work_flag."','".$work_list['part_flag']."','".$work_list['type_flag']."','".$work_list['decide_flag']."',";
												$sql = $sql .= "'".$repeat_idx."','".$workidx."', '".$insert_idx."','".$work_user_report_info_email."','".$work_user_report_info_name."','".$work_list['part']."','".$work_list['contents']."','".$create_date."','".LIP."')";
												$insert_work_idx = insertIdxQuery($sql);
											}
										}
									}
								}else if($share_flag){

									if($work_user_share_info['idx']){

										for($j=0; $j<count($work_user_share_info['idx']); $j++){
											$work_user_share_info_email = $work_user_share_info['email'][$j];
											$work_user_share_info_name = $work_user_share_info['name'][$j];

											//등록한 사용자
											$sql = "select idx from work_todaywork_share where state='0' and companyno='".$companyno."' and email='".$work_user_share_info_email."' and work_idx='".$insert_idx."' and workdate='".$create_date."'";
											$work_req_user_list = selectQuery($sql);
											if(!$work_req_user_list['idx']){
												$sql = "insert into work_todaywork_share(work_idx,companyno,work_email,work_name,email,name,ip,workdate) values(";
												$sql = $sql .= "'".$insert_idx."','".$companyno."','".$user_id."','".$user_name."','".$work_user_share_info_email."','".$work_user_share_info_name."','".LIP."','".$create_date."')";
												insertQuery($sql);
											}

											//요청받은 사용자
											$sql = "select idx from work_todaywork where state='0' and companyno='".$companyno."' and work_flag='".$work_flag."' and contents='".$work_list['contents']."' and email='".$work_user_report_info_email."' and workdate='".$create_date."'";
											$work_todaywork_info = selectQuery($sql);
											if(!$work_todaywork_info['idx']){

												$sql = "insert into work_todaywork(companyno, highlevel, day_flag, work_flag, part_flag, type_flag, decide_flag, repeat_flag, repeat_work_idx, work_idx, email, name, part, contents, workdate, ip)values(";
												$sql = $sql .= "'".$companyno."','".$work_list['highlevel']."','".$work_list['day_flag']."','".$work_flag."','".$work_list['part_flag']."','".$work_list['type_flag']."','".$work_list['decide_flag']."',";
												$sql = $sql .= "'".$repeat_idx."','".$workidx."', '".$insert_idx."','".$work_user_share_info_email."','".$work_user_share_info_name."','".$work_list['part']."','".$work_list['contents']."','".$create_date."','".LIP."')";
												$insert_work_idx = insertIdxQuery($sql);
											}
										}
									}
								}
							}
						}
					//}
					$k++;
				}

			//매주반복
			}else if($repeat_idx == '2'){

				//시작일자
				$start_date = new DateTime($workdate);

				//주단위 설정
				$start_w = date('w', strtotime($workdate));

				$end_date = new DateTime($end_date);
				$diff_days = date_diff($start_date, $end_date);

				//기간
				$diffdays = $diff_days->days;

				//반복설정 매주
				$repeat_type = "week";

				//반복 설정이 있는경우 삭제 처리 후 반복설정
				$sql = "select idx, repeat_flag, repeat_work_idx, workdate from work_todaywork where state='0' and companyno='".$companyno."' and repeat_flag!='0' and workdate='".$workdate."' and idx='".$work_list['idx']."'";
				$work_row = selectQuery($sql);
				if($work_row['idx']){
					//반복설정된 업무
					$sql = "select idx from work_todaywork where state='0' and companyno='".$companyno."' and repeat_work_idx='".$work_row['idx']."' and workdate > '".$work_row['workdate']."'";
					$work_info = selectQuery($sql);
					if($work_info['idx']){
						$sql = "update work_todaywork set state='9', editdate=".DBDATE." where state='0' and companyno='".$companyno."' and repeat_work_idx='".$work_row['idx']."' and workdate > '".$work_row['workdate']."'";
						$insert_idx = updateQuery($sql);
					}
				}

				if($work_row['repeat_work_idx']){
					$sql = "select idx from work_todaywork where state='0' and companyno='".$companyno."' and repeat_work_idx='".$work_row['repeat_work_idx']."' and workdate > '".$work_row['workdate']."'";
					$work_info = selectQuery($sql);
					if($work_info['idx']){
						$sql = "update work_todaywork set state='9', editdate=".DBDATE." where state='0' and companyno='".$companyno."' and repeat_work_idx='".$work_row['repeat_work_idx']."' and workdate > '".$work_row['workdate']."'";
						$insert_idx = updateQuery($sql);
					}
				}

				$k=1;
				for($i=0; $i<$diffdays; $i++){
					$create_date = date("Y-m-d", strtotime("+".$k." day", TODAYTIME));
					$yoil_day = date('w', strtotime($create_date));

					//주가 같으면
					if ($start_w == $yoil_day){
						$sql = "select idx from work_todaywork where state='0' and companyno='".$companyno."' and workdate='".$create_date."' and contents='".$work_list['contents']."' ";
						$work_row = selectQuery($sql);
						if(!$work_row['idx']){
							$sql = "insert into work_todaywork(companyno, highlevel, day_flag, work_flag, part_flag, type_flag, decide_flag, repeat_flag, repeat_work_idx, email, name, part, contents, workdate, ip)values(";
							$sql = $sql .= "'".$companyno."','".$work_list['highlevel']."','".$work_list['day_flag']."','".$work_list['work_flag']."','".$work_list['part_flag']."','".$work_list['type_flag']."','".$work_list['decide_flag']."',";
							$sql = $sql .= "'".$repeat_idx."','".$work_list['idx']."','".$work_list['email']."','".$work_list['name']."','".$work_list['part']."','".$work_list['contents']."','".$create_date."','".LIP."')";
							$insert_idx = insertIdxQuery($sql);

							if($work_flag=="3"){
								//요청한 사용자 내역 저장
								if($work_user_req_info['idx']){

									for($j=0; $j<count($work_user_req_info['idx']); $j++){
										$work_user_req_info_email = $work_user_req_info['email'][$j];
										$work_user_req_info_name = $work_user_req_info['name'][$j];

										//등록한 사용자
										$sql = "select idx from work_todaywork_user where state='0' and companyno='".$companyno."' and email='".$work_user_req_info_email."' and work_idx='".$insert_idx."' and workdate='".$create_date."'";
										$work_req_user_list = selectQuery($sql);
										if(!$work_req_user_list['idx']){
											$sql = "insert into work_todaywork_user(work_idx,companyno,work_email,work_name,email,name,ip,workdate) values(";
											$sql = $sql .= "'".$insert_idx."','".$companyno."','".$user_id."','".$user_name."','".$work_user_req_info_email."','".$work_user_req_info_name."','".LIP."','".$create_date."')";
											insertQuery($sql);
										}

										//요청받은 사용자
										$sql = "select idx from work_todaywork where state='0' and companyno='".$companyno."' and work_flag='".$work_flag."' and contents='".$work_list['contents']."' and email='".$work_user_req_info_email."' and workdate='".$create_date."'";
										$work_todaywork_info = selectQuery($sql);
										if(!$work_todaywork_info['idx']){

											$sql = "insert into work_todaywork(companyno, highlevel, day_flag, work_flag, part_flag, type_flag, decide_flag, repeat_flag, repeat_work_idx, work_idx, email, name, part, contents, workdate, ip)values(";
											$sql = $sql .= "'".$companyno."','".$work_list['highlevel']."','".$work_list['day_flag']."','".$work_flag."','".$work_list['part_flag']."','".$work_list['type_flag']."','".$work_list['decide_flag']."',";
											$sql = $sql .= "'".$repeat_idx."','".$workidx."', '".$insert_idx."','".$work_user_req_info_email."','".$work_user_req_info_name."','".$work_list['part']."','".$work_list['contents']."','".$create_date."','".LIP."')";
											$insert_work_idx = insertIdxQuery($sql);
										}
									}
								}
								//보고업무
							}else if($work_flag=="1"){

								if($work_user_report_info['idx']){

									for($j=0; $j<count($work_user_report_info['idx']); $j++){
										$work_user_report_info_email = $work_user_report_info['email'][$j];
										$work_user_report_info_name = $work_user_report_info['name'][$j];

										//등록한 사용자
										$sql = "select idx from work_todaywork_report where state='0' and companyno='".$companyno."' and email='".$work_user_report_info_email."' and work_idx='".$insert_idx."' and workdate='".$create_date."'";
										$work_req_user_list = selectQuery($sql);
										if(!$work_req_user_list['idx']){
											$sql = "insert into work_todaywork_report(work_idx,companyno,work_email,work_name,email,name,ip,workdate) values(";
											$sql = $sql .= "'".$insert_idx."','".$companyno."','".$user_id."','".$user_name."','".$work_user_report_info_email."','".$work_user_report_info_name."','".LIP."','".$create_date."')";
											insertQuery($sql);
										}

										//요청받은 사용자
										$sql = "select idx from work_todaywork where state='0' and companyno='".$companyno."' and work_flag='".$work_flag."' and contents='".$work_list['contents']."' and email='".$work_user_report_info_email."' and workdate='".$create_date."'";
										$work_todaywork_info = selectQuery($sql);
										if(!$work_todaywork_info['idx']){

											$sql = "insert into work_todaywork(companyno, highlevel, day_flag, work_flag, part_flag, type_flag, decide_flag, repeat_flag, repeat_work_idx, work_idx, email, name, part, contents, workdate, ip)values(";
											$sql = $sql .= "'".$companyno."','".$work_list['highlevel']."','".$work_list['day_flag']."','".$work_flag."','".$work_list['part_flag']."','".$work_list['type_flag']."','".$work_list['decide_flag']."',";
											$sql = $sql .= "'".$repeat_idx."','".$workidx."', '".$insert_idx."','".$work_user_report_info_email."','".$work_user_report_info_name."','".$work_list['part']."','".$work_list['contents']."','".$create_date."','".LIP."')";
											$insert_work_idx = insertIdxQuery($sql);
										}
									}
								}
							}else if($share_flag){

								if($work_user_share_info['idx']){

									for($j=0; $j<count($work_user_share_info['idx']); $j++){
										$work_user_share_info_email = $work_user_share_info['email'][$j];
										$work_user_share_info_name = $work_user_share_info['name'][$j];

										//등록한 사용자
										$sql = "select idx from work_todaywork_share where state='0' and companyno='".$companyno."' and email='".$work_user_share_info_email."' and work_idx='".$insert_idx."' and workdate='".$create_date."'";
										$work_req_user_list = selectQuery($sql);
										if(!$work_req_user_list['idx']){
											$sql = "insert into work_todaywork_share(work_idx,companyno,work_email,work_name,email,name,ip,workdate) values(";
											$sql = $sql .= "'".$insert_idx."','".$companyno."','".$user_id."','".$user_name."','".$work_user_share_info_email."','".$work_user_share_info_name."','".LIP."','".$create_date."')";
											insertQuery($sql);
										}

										//요청받은 사용자
										$sql = "select idx from work_todaywork where state='0' and companyno='".$companyno."' and work_flag='".$work_flag."' and contents='".$work_list['contents']."' and email='".$work_user_report_info_email."' and workdate='".$create_date."'";
										$work_todaywork_info = selectQuery($sql);
										if(!$work_todaywork_info['idx']){

											$sql = "insert into work_todaywork(companyno, highlevel, day_flag, work_flag, part_flag, type_flag, decide_flag, repeat_flag, repeat_work_idx, work_idx, email, name, part, contents, workdate, ip)values(";
											$sql = $sql .= "'".$companyno."','".$work_list['highlevel']."','".$work_list['day_flag']."','".$work_flag."','".$work_list['part_flag']."','".$work_list['type_flag']."','".$work_list['decide_flag']."',";
											$sql = $sql .= "'".$repeat_idx."','".$workidx."', '".$insert_idx."','".$work_user_share_info_email."','".$work_user_share_info_name."','".$work_list['part']."','".$work_list['contents']."','".$create_date."','".LIP."')";
											$insert_work_idx = insertIdxQuery($sql);
										}
									}
								}
							}
						}
					}
					$k++;
				}

			//매월반복
			}else if($repeat_idx == '3'){

				//종료일자
				//$end_date = date("Y-m-d", strtotime("+".repeatday." day", strtotime($workdate)));

				$repeat_day = repeatday / 30;
				$repeat_day = round($repeat_day);

				//반복설정 매월
				$repeat_type = "month";

				//반복 설정이 있는경우 삭제 처리 후 반복설정
				$sql = "select idx, repeat_flag, workdate from work_todaywork where state='0' and companyno='".$companyno."' and repeat_flag!='0' and workdate='".$workdate."' and idx='".$work_list['idx']."'";
				$work_row = selectQuery($sql);
				if($work_row['idx']){
					//반복설정된 업무
					$sql = "select idx from work_todaywork where state='0' and companyno='".$companyno."' and repeat_work_idx='".$work_row['idx']."' and workdate > '".$work_row['workdate']."'";
					$work_info = selectQuery($sql);
					if($work_info['idx']){
						$sql = "update work_todaywork set state='9', editdate=".DBDATE." where state='0' and companyno='".$companyno."' and repeat_work_idx='".$work_row['idx']."' and workdate > '".$work_row['workdate']."'";
						$insert_idx = updateQuery($sql);
					}
				}

				if($work_row['repeat_work_idx']){
					$sql = "select idx from work_todaywork where state='0' and companyno='".$companyno."' and repeat_work_idx='".$work_row['repeat_work_idx']."' and workdate > '".$work_row['workdate']."'";
					$work_info = selectQuery($sql);
					if($work_info['idx']){
						$sql = "update work_todaywork set state='9', editdate=".DBDATE." where state='0' and companyno='".$companyno."' and repeat_work_idx='".$work_row['repeat_work_idx']."' and workdate > '".$work_row['workdate']."'";
						$insert_idx = updateQuery($sql);
					}
				}

				for($i=1; $i<=$repeat_day; $i++){
					$create_date = date('Y-m-d',strtotime('+'.$i.' month', strtotime($workdate)));
					if ($create_date){
						$sql = "select idx from work_todaywork where state='0' and companyno='".$companyno."' and workdate='".$create_date."' and contents='".$work_list['contents']."'";
						$work_row = selectQuery($sql);
						if(!$work_row['idx']){
							$sql = "insert into work_todaywork(companyno, highlevel, day_flag, work_flag, part_flag, type_flag, decide_flag, repeat_flag, repeat_work_idx, email, name, part, contents, workdate, ip)values(";
							$sql = $sql .= "'".$companyno."','".$work_list['highlevel']."','".$work_list['day_flag']."','".$work_list['work_flag']."','".$work_list['part_flag']."','".$work_list['type_flag']."','".$work_list['decide_flag']."',";
							$sql = $sql .= "'".$repeat_idx."','".$work_list['idx']."','".$work_list['email']."','".$work_list['name']."','".$work_list['part']."','".$work_list['contents']."','".$create_date."','".LIP."')";
							$insert_idx = insertQuery($sql);
							if($insert_idx){
								//요청업무
								if($work_flag=="3"){
									$sql = "select idx from work_todaywork_user where state='0' and work_idx='".$insert_idx."'";
								}
							}
						}
					}else{
						// echo "\n";
					}
				}

			//반복안함
			}else if($repeat_idx == '4'){
				$repeat_type ="not";
				$sql = "select idx, repeat_flag, repeat_work_idx, workdate from work_todaywork where state in('0','1') and companyno='".$companyno."' and repeat_flag!='0' and workdate='".$workdate."' and idx='".$work_list['idx']."' ";
				$work_row = selectQuery($sql);
				if($work_row['repeat_work_idx']){
					$sql = "select idx from work_todaywork where state in('0','1')  and companyno='".$companyno."' and repeat_work_idx='".$work_row['repeat_work_idx']."'";
					$work_info = selectQuery($sql);
			
					if($work_info['idx']){
						$repeat_work_idx = $work_row['repeat_work_idx'];
						$sql = "update work_todaywork set state='9', editdate=".DBDATE." where state in('0','1') and companyno='".$companyno."' and repeat_work_idx='".$repeat_work_idx."' and workdate > '".$work_row['workdate']."'";
						$insert_idx = updateQuery($sql);
						$repeat_idx = '0';
						$sql = "update work_todaywork set repeat_flag='".$repeat_idx."' where companyno='".$companyno."' and idx='".$repeat_work_idx."'";
						$up = updateQuery($sql);
						if($up){
							echo "complete|".$repeat_type;
						}
					}
				}
			}

			
			if($repeat_idx!='4'&&$repeat_idx!='0'){
				$sql = "update work_todaywork set repeat_flag='".$repeat_idx."' where companyno='".$companyno."' and idx='".$work_list['idx']."'";
				$up = updateQuery($sql);
				if($up){
					echo "complete|".$repeat_type;
				}
			}
			
	}
	exit;
}

}

//메모작성
if($mode == "work_comment"){

//	ini_set ( 'mssql.textlimit' , '2147483647' );
//	ini_set ( 'mssql.textsize' , '2147483647' );

	$comment = $_POST['comment'];
	$work_idx = $_POST['work_idx'];
	$work_idx = preg_replace("/[^0-9]/", "", $work_idx);
	//$comment = nl2br($comment);

	// 비밀 메모 작업
	$secret_flag = $_POST['secret_flag'];
	//홑따옴표 때문에 아래와 같이 처리
	$comment = replace_text($comment);

	//회원정보 추출
	$member_info = member_row_info($user_id);

	$sql = "select idx, work_idx, work_flag, email, share_flag from work_todaywork where state!='9' and companyno='".$companyno."' and idx='".$work_idx."'";
	$work_info = selectQuery($sql);
	if($work_info['idx']){

		$work_flag = $work_info['work_flag'];
		//요청된 업무가 있을경우
		if($work_info['work_idx']){
			$link_idx = $work_info['work_idx'];
		}else{
			$link_idx = $work_info['idx'];
		}

		//오늘업무 작성한 사용자 확인
		$sql = "select idx, email, contents, name from work_todaywork where state='0' and companyno='".$companyno."' and idx='".$link_idx."'";
		$work_real_info = selectQuery($sql);

		//메모작성
		$sql = "insert into work_todaywork_comment(link_idx, work_idx, companyno, email, name, part, partno, comment, type_flag, secret_flag, workdate, ip) values";
		$sql = $sql .= "('".$link_idx."','".$work_info['idx']."','".$companyno."','".$user_id."','".$user_name."','".$member_info['part']."','".$member_info['partno']."','".$comment."','".$type_flag."', '".$secret_flag."', '".TODATE."','".LIP."')";
		$res_idx = insertIdxQuery($sql);
		if($res_idx){

			//타임라인(메모작성)
			work_data_log('0','5', $res_idx, $user_id, $user_name);

			//메모 2개이상 메인 좋아요
			main_like_cp_works("memo",$link_idx);

			//보고업무 메모작성
			if($work_flag=='1'){

				//역량평가지표(보고업무 메모작성)
				work_cp_reward("work", "0011", $user_id, $work_info['idx'], "", "");


			}else if($work_flag=='3'){
				//요청업무 메모작성
				//역량평가지표(업무요청 메모작성)
				work_cp_reward("work", "0006", $user_id, $work_info['idx'], "", $work_info['work_idx']);

			}else{

				if($work_info['share_flag'] == '2'){

					//역량평가지표(업무공유 메모작성)
					work_cp_reward("work", "0008", $user_id, $work_info['idx'], "", $work_info['work_idx']);

				}else{

					//역량평가지표(타인 오늘업무 메모작성)
					if($work_real_info['email'] != $user_id){
						work_cp_reward("work", "0004", $user_id, $work_info['idx'], "", $work_info['work_idx']);
					}
				}
			}

			$memo_title = $user_name."님의 메모"; 
			if($work_real_info['email'] != $user_id){
				pushToken($memo_title,$comment,$work_real_info['email'],'memo','37',$user_id,$user_name,$link_idx,$work_real_info['contents'], 'work');
			}
			echo "complete";



			exit;

		}else{

			echo "not";
			exit;

		}
	}
	exit;
}


//메모삭제
if($mode == "work_comment_del"){
	$idx = $_POST['idx'];
	$idx = preg_replace("/[^0-9]/", "", $idx);
	if($idx){

		$sql = "select idx, link_idx from work_todaywork_comment where idx='".$idx."' and companyno='".$companyno."' and email='".$user_id."'";
		$res = selectQuery($sql);
		if($res['idx']){

			$sql = "select idx, work_flag, share_flag from work_todaywork use index(state) where state='0' and companyno='".$companyno."' and idx='".$res['link_idx']."'";
			$work_info = selectQuery($sql);

			$sql = "update work_todaywork_comment set state='9' where companyno='".$companyno."' and idx='".$res['idx']."'";
			$up = updateQuery($sql);
			if($up){

				//타임라인(메모삭제)
				work_data_log_del('9','5', $res['idx'], $user_id, $user_name);
 

				if( $work_info['work_flag']=='3'){

					//역량평가지표(업무요청 메모작성 삭제)
					work_cp_unreward("work","0007", $res['link_idx']);

				}else if($work_info['share_flag']=='2'){

					//역량평가지표(업무공유 메모작성 삭제)
					work_cp_unreward("work","0010", $res['link_idx']);
				}else{

					//역량평가지표(오늘업무 메모작성 삭제)
					work_cp_unreward("work","0003", $res['link_idx']);

				}

				echo "complete|".$res['idx'];
				exit;
			}
		}
	}
	exit;
}



//오늘업무 댓글 수정하기
if($mode == "work_comment_edit"){

	$idx = $_POST['idx'];
	$idx = preg_replace("/[^0-9]/", "", $idx);
	$contents = $_POST['contents'];
	//$contents = stripslashes(nl2br($contents));
	//$contents = nl2br($contents);

	if($idx){
		$sql = "select idx from work_todaywork_comment where companyno='".$companyno."' and email='".$user_id."' and idx='".$idx."'";
		$res_info = selectQuery($sql);
		if($res_info['idx']){

			$sql = "update work_todaywork_comment set comment='".$contents."', editdate=".DBDATE." where companyno='".$companyno."' and email='".$user_id."' and idx='".$res_info['idx']."'";
			$res = updateQuery($sql);

			echo "complete";
			exit;
		}
	}

	exit;
}


//보고업무 내용수정
if($mode == "work_report_edit"){

	$idx = $_POST['idx'];
	$idx = preg_replace("/[^0-9]/", "", $idx);
	$contents = $_POST['contents'];
	//$contents = stripslashes(nl2br($contents));
	//$contents = nl2br($contents);

	

	if($idx){
		$sql = "select idx,contents from work_todaywork where state='0' and companyno='".$companyno."' and email='".$user_id."' and idx='".$idx."'";
		$res_info = selectQuery($sql);

		if($contents==""){
			echo "none_report|".$res_info['contents'];
			exit;
		}

		if($res_info['idx']){

			$work_idx = $res_info['idx'];
			$sql = "update work_todaywork set contents='".$contents."', editdate=".DBDATE." where idx='".$res_info['idx']."'";
			$up = updateQuery($sql);
			if($up){

				//보고한 업무들 업데이트
				$sql = "select idx from work_todaywork where state='0' and companyno='".$companyno."' and work_idx='".$idx."'";
				$work_report_info = selectAllQuery($sql);
				for($i=0; $i<count($work_report_info['idx']); $i++){
					$sql = "update work_todaywork set contents='".$contents."' where state='0' and companyno='".$companyno."' and idx='".$work_report_info['idx'][$i]."'";
					$up = updateQuery($sql);
				}
			}

			if($up){
				echo "complete";
				exit;
			}
		}
	}

	exit;
}



//한줄소감등록하기
if($mode == "todaywork_review_write"){

	$input_val = $_POST['input_val'];
	$workdate = $_POST['workdate'];
	$icon_idx = $_POST['icon_idx'];
	$icon_idx = preg_replace("/[^0-9]/", "", $icon_idx);

	//회원정보 추출
	$member_info = member_row_info($user_id);
	if($workdate){
		//한줄소감 입력
		$sql = "select idx from work_todaywork_review where state='0' and companyno='".$companyno."' and email='".$user_id."' and workdate='".$workdate."'";
		$review_info = selectQuery($sql);
		if($review_info['idx']){
			//한줄소감 수정
			$sql = "update work_todaywork_review set work_idx='".$icon_idx."', comment='".$input_val."', editdate=".DBDATE." where state='0' and companyno='".$companyno."' and email='".$user_id."' and idx='".$review_info['idx']."'";
			$up = updateQuery($sql);
			if($up){

				//완료|인덱스번호|아이콘번호|한줄소감
				echo "complete|".$workdate."|".$icon_idx."|".$input_val;
				exit;
			}
		}else{

			//한줄소감 입력
			$sql = "insert into work_todaywork_review(companyno, work_idx, email, name, part, partno, comment, type_flag, workdate, ip) values(";
			$sql = $sql .= "'".$companyno."','".$icon_idx."', '".$user_id."', '".$user_name."', '".$member_info['part']."', '".$member_info['partno']."', '".$input_val."','".$type_flag."','".$workdate."','".LIP."')";
			$insert_idx = insertIdxQuery($sql);
			if($insert_idx){
				//역량지표(한줄소감)
				work_cp_reward("main", "0008", $user_id, $insert_idx, "", "");

				//타임라인(한줄소감작성)
				work_data_log('0','12', $insert_idx, $user_id, $user_name);

				//완료|인덱스번호|아이콘번호|한줄소감
				echo "complete|".$workdate."|".$icon_idx."|".$input_val;
				exit;
			}
		}
	}

	exit;
}


//한줄소감 내용 불러오기
if($mode == "todaywork_review_info"){

	$workdate = $_POST['workdate'];
	//$review_idx = $_POST['review_idx'];
	//$review_idx = preg_replace("/[^0-9]/", "", $review_idx);

	/*print "<pre>";
	print_r($_POST);
	print "</pre>";*/

	if($workdate){

		/*if($review_idx){
			$sql = "select idx, work_idx, comment from work_todaywork_review where state='0' and email='".$user_id."' and idx='".$review_idx."'";
			$review_info = selectQuery($sql);
			if($review_info['idx']){
				//완료|인덱스번호|아이콘번호|한줄소감
				echo "complete|".$review_idx."|".$review_info['work_idx']."|".$review_info['comment'];
				exit;
			}
		}else{*/

			$sql = "select idx, work_idx, comment from work_todaywork_review where state='0' and companyno='".$companyno."' and email='".$user_id."' and workdate='".$workdate."'";
			$review_info = selectQuery($sql);
			if($review_info['idx']){
				//완료|인덱스번호|아이콘번호|한줄소감
				echo "complete|".$workdate."|".$review_info['work_idx']."|".$review_info['comment'];
				exit;
			}else{

				//완료|인덱스번호|아이콘번호|한줄소감
				echo "complete|".$workdate."|";
				exit;
			}
		//}
	}
	exit;
}



//업무공유하기
if($mode == "works_share"){

	$work_user_chk = $_POST['work_user_chk'];
	$work_idx = $_POST['work_idx'];
	$work_idx = preg_replace("/[^0-9]/", "", $work_idx);
	$work_mem_idx = trim($work_user_chk);

	//공유하는 업무
	$share_flag_get = "1";

	//공유받는 업무
	$share_flag_run = "2";

	//$work_flag = "2";

	//삭제 되지 않은 업무
	$sql = "select idx, state, title, contents, workdate, secret_flag, work_flag from work_todaywork where state!='9' and companyno='".$companyno."' and idx='".$work_idx."' and email='".$user_id."'";
	$work_row = selectQuery($sql);

	$sql = "select idx, email, name, part, partno, highlevel from work_member where state='0' and companyno='".$companyno."' and idx in (".$work_mem_idx.") order by name asc";
	$work_mem_info = selectAllQuery($sql);


	//공유한 회원수
	$work_share_cnt = count($work_mem_info['idx']);

	//공유자가 1명보다 큰경우
	if($work_share_cnt > 1){
		$work_share_change_cnt = $work_share_cnt - 1;

		//타임라인(업무공유함)
		work_data_multi_log('0','4', $work_idx, $user_id, $user_name, $work_mem_info['email'][0], $work_mem_info['name'][0], $work_share_change_cnt);
	}

	for($i=0; $i<count($work_mem_info['idx']); $i++){

		$mem_id = $work_mem_info['email'][$i];
		$mem_name = $work_mem_info['name'][$i];
		$mem_part = $work_mem_info['part'][$i];
		$mem_partno = $work_mem_info['partno'][$i];
		$mem_highlevel = $work_mem_info['highlevel'][$i];

		if($work_row['idx']){
			$work_state = $work_row['state'];
			$title = $work_row['title'];
			$contents = $work_row['contents'];
			$workdate = $work_row['workdate'];
			$work_flag = $work_row['work_flag'];
			$secret_flag = $work_row['secret_flag'];

			//홑따옴표 때문에 아래와 같이 처리
			if(strpos($contents, "'") !== false) {
				$contents = str_replace("'", "''", $contents);
			}


			//보고업무
			if($work_flag=='1'){

				/*
				//공유받는 업무 저장
				$sql = "insert into work_todaywork(companyno,highlevel,work_flag,part_flag,type_flag,share_flag,work_idx,email,name,part,title,contents,workdate,ip) values(";
				$sql = $sql .= "'".$companyno."','".$mem_highlevel."','".$work_flag."','".$mem_partno."','".$type_flag."','".$share_flag_run."','".$work_row['idx']."','".$mem_id."','".$mem_name."','".$mem_part."','".$title."','".$contents."','".$workdate."','".LIP."')";
				$inser_report_idx = insertIdxQuery($sql);



				//공유하는 업무 업데이트
				//$sql = "update work_todaywork set state='".$work_state."', share_flag='".$share_flag_get."' where companyno='".$companyno."' and idx='".$work_row['idx']."'";
				//$up[] = updateQuery($sql);
				//state='".$work_state."', share_flag='".$share_flag_get."' where companyno='".$companyno."' and idx='".$work_row['idx']."'";

				$sql = "select idx from work_todaywork_share where state='0' and companyno='".$companyno."' and work_idx='".$work_idx."' and email='".$mem_id."' and workdate='".$workdate."'";
				$work_share_info = selectQuery($sql);
				if(!$work_share_info['idx']){
					$sql = "insert into work_todaywork_share(companyno,work_idx, work_email, work_name, email, name, workdate, ip) values('".$companyno."','".$work_idx."','".$user_id."','".$user_name."','".$mem_id."','".$mem_name."','".$workdate."','".LIP."')";
					$inser_idx2[] = insertIdxQuery($sql);
				}
				*/

			}else{

				//공유받는 업무 저장
				$sql = "insert into work_todaywork(companyno,highlevel,work_flag,part_flag,type_flag,share_flag,secret_flag,work_idx,email,name,part,contents,workdate,ip) values('".$companyno."','".$mem_highlevel."','".$work_flag."','".$mem_partno."','".$type_flag."','".$share_flag_run."','".$secret_flag."','".$work_row['idx']."','".$mem_id."','".$mem_name."','".$mem_part."','".$contents."','".$workdate."','".LIP."')";
				$inser_idx1[] = insertIdxQuery($sql);

				//공유하는 업무 업데이트
				$sql = "update work_todaywork set share_flag='".$share_flag_get."', work_idx='".$work_row['idx']."' where companyno='".$companyno."' and idx='".$work_row['idx']."'";
				$up[] = updateQuery($sql);

				$sql = "select idx from work_todaywork_share where state='0' and companyno='".$companyno."' and work_idx='".$work_idx."' and email='".$mem_id."'";
				$work_share_info = selectQuery($sql);
				if(!$work_share_info['idx']){
					$sql = "insert into work_todaywork_share(companyno,work_idx, work_email, work_name, email, name, workdate, ip) values('".$companyno."','".$work_idx."','".$user_id."','".$user_name."','".$mem_id."','".$mem_name."','".$workdate."','".LIP."')";
					$inser_idx2[] = insertIdxQuery($sql);
				}
			}

			//역량평가지표(오늘업무 완료)
			work_cp_reward("work", "0002", $user_id, $work_row['idx']);

			//공유한 회원수
			if($work_share_cnt == '1'){
				//타임라인(업무공유함)
				work_data_log('0','4', $work_idx, $user_id, $user_name, $mem_id, $mem_name);
			}

			//타임라인(업무공유받음)
			work_data_log('0','3', $work_idx, $mem_id, $mem_name, $user_id, $user_name );

			//역량평가지표(업무공유 보내기)
			work_cp_reward("work", "0007", $user_id, $work_idx);
		}
	}

	//보고업무를 공유 했을경우
	if($inser_report_idx){
		//공유하는 업무
		//$sql = "insert into work_todaywork(companyno,highlevel,work_flag,part_flag,type_flag,share_flag,work_idx,email,name,part,title,contents,workdate,ip) values(";
		//$sql = $sql .= "'".$companyno."','".$user_level."','".$work_flag."','".$user_part."','".$type_flag."','".$share_flag_get."','".$work_idx."','".$user_id."','".$user_name."','".$part_name."','".$title."','".$contents."','".$workdate."','".LIP."')";
		//insertIdxQuery($sql);

	}

	if(count($work_mem_info['idx']) && $inser_idx2){




		echo "complete";
		exit;
	}

	exit;
}

//업무공유취소하기
if($mode == "works_share_cancel"){

	$work_idx = $_POST['work_idx'];
	$work_idx = preg_replace("/[^0-9]/", "", $work_idx);

	//공유한 업무
	$share_flag_get = '1';

	//공유받은 업무
	$share_flag_run = '2';

	if($work_idx){

		//$user_id='eyson@bizforms.co.kr';
		$sql = "select idx from work_todaywork where state!='9' and companyno='".$companyno."' and idx='".$work_idx."' and email='".$user_id."'";
		$work_info = selectQuery($sql);
		if($work_info['idx']){

			//공유한 업무 업데이트
			$sql = "update work_todaywork set share_flag='0', work_idx=null where companyno='".$companyno."' and idx='".$work_info['idx']."'";
			$up1 = updateQuery($sql);


			//공유받은 업무 업데이트(삭제)
			$sql = "select idx, email, name from work_todaywork where state='0' and companyno='".$companyno."' and share_flag='".$share_flag_run."' and work_idx='".$work_idx."'";
			$work_list_info = selectAllQuery($sql);

			for($i=0; $i<count($work_list_info['idx']); $i++){

				$sql = "update work_todaywork set state='9', editdate=".DBDATE." where companyno='".$companyno."' and idx='".$work_list_info['idx'][$i]."'";
				$up2[] = updateQuery($sql);

				$sql = "select idx from work_todaywork_share where state='0' and companyno='".$companyno."' and work_email='".$user_id."' and work_idx='".$work_idx."'";
				$work_share_info = selectQuery($sql);
				if($work_share_info['idx']){
					$sql = "update work_todaywork_share set state='9', editdate=".DBDATE." where companyno='".$companyno."' and idx='".$work_share_info['idx']."'";
					$up3[] = updateQuery($sql);
				}


				//타임라인(업무공유함 삭제)
				work_data_log('9','4', $work_idx, $user_id, $user_name, $work_list_info['email'][$i], $work_list_info['name'][$i]);

				//타임라인(업무공유받음 삭제)
				work_data_log('9','3', $work_idx, $work_list_info['email'][$i], $work_list_info['name'][$i], $user_id, $user_name);

			}


			//좋아요 횟수 처리
			$sql = "select idx from work_todaywork_like where state='0' and companyno='".$companyno."' and email='".$user_id."' and work_idx='".$work_idx."'";
			$wokr_like_info = selectQuery($sql);
			if($work_like_info['idx']){
				$sql = "update work_todaywork_like set state='9', editdate=".DBDATE." where idx='".$work_like_info['idx']."'";
				$up = updateQuery($sql);
				if($up){
					$sql = "select idx from work_data_log where companyno='".$companyno."' and idx='".$work_like_info['idx']."'";
					$work_data_info = selectQuery($sql);
					if($work_data_info['idx']){
						$sql = "update work_data_log set state='9', editdate=".DBDATE." where work_idx='".$work_data_info['idx']."'";
						$up = updateQuery($sql);
					}
				}
			}


			//업데이트가 성공일때 처리
			if(count($work_list_info['idx']) == count($up2)){
				echo "complete";
				exit;
			}
		}
	}
	exit;
}



//보고업무 파일삭제처리
if($mode == "works_files_del"){

	$wdate = $_POST['wdate'];
	$work_idx = $_POST['work_idx'];
	$file_idx = $_POST['file_idx'];

	$file_idx = preg_replace("/[^0-9]/", "", $file_idx);
	$work_idx = preg_replace("/[^0-9]/", "", $work_idx);

	//파일idx, 업무idx
	if(strpos($wdate, ".") !== false) {
		$wdate = str_replace(".", "-", $wdate);
	}

	if($file_idx && $work_idx){

		//업무리스트 체크
		$sql = "select idx from work_todaywork where companyno='".$companyno."' and email='".$user_id."' and idx='".$work_idx."'";
		$work_info = selectQuery($sql);
		if($work_info['idx']){

			//파일정보체크
			$sql = "select idx, file_path, file_name from work_filesinfo_todaywork where state='0' and idx='".$file_idx."'";
			$file_info = selectQuery($sql);
			if($file_info['idx']){
				$work_file_path = $file_info['file_path'];
				$work_file_name = $file_info['file_name'];
				$work_file_unlink = unlink($dir_file_path. $work_file_path. $work_file_name);

				//삭제처리
				if($work_file_unlink){
					//첨부파일삭제처리
					$sql = "update work_filesinfo_todaywork set state='9', editdate=".DBDATE." where idx='".$file_info['idx']."'";
					$up = updateQuery($sql);
					if($up){
						echo "complete";
						exit;
					}
				}
			}
		}
	}
	exit;
}



//보고업무내용삭제
if($mode == "work_report_del"){

	$idx = $_POST['idx'];
	$idx = preg_replace("/[^0-9]/", "", $idx);

	//날짜변환
	$wdate = $_POST['wdate'];
	if($wdate){
		$wdate = str_replace(".", "-", $wdate);
	}

	if($idx){
		$sql = "select idx, state, work_idx, work_flag, file_flag from work_todaywork where idx='".$idx."' and companyno='".$companyno."' and email='".$user_id."'";
		$res = selectQuery($sql);
		if($res['idx']){

			$work_idx = $res['idx'];
			//보고내용삭제
			$sql = "update work_todaywork set state='9', editdate=".DBDATE." where companyno='".$companyno."' and idx='".$res_idx."'";
			$res = updateQuery($sql);
			if($res){
				//coin_del($idx , $state)

				//첨부파일이 있는경우 삭제 처리
				if($file_flag == "1"){
					$sql = "select idx, file_path, file_name from work_filesinfo_todaywork where state='0' and work_idx='".$res_idx."'";
					$work_file_info = selectAllQuery($sql);
					for($i=0; $i<count($work_file_info['idx']); $i++){
						$work_file_path = $work_file_info['file_path'][$i];
						$work_file_name = $work_file_info['file_name'][$i];
						$work_file_unlink = unlink($dir_file_path. $work_file_path. $work_file_name);
					}

					//삭제처리
					if($work_file_unlink){
						//첨부파일삭제처리
						$sql = "update work_filesinfo_todaywork set state='9', editdate=".DBDATE." where work_idx='".$res_idx."'";
						updateQuery($sql);
					}
				}

				//타임라인(오늘업무삭제)
				work_data_log('9','2', $res_idx, $user_id, $user_name);

				//역량 평가 지표 삭제 처리(work, 0001, 게시물idx)
				work_cp_delreward("work", $res_idx);
				echo "complete";
				exit;
			}

		}
	}
	exit;


}

//코멘트 작성자 확인
if($mode == "work_comment_check"){
	$comment_idx = $_POST['comment_idx'];
	$comment_idx = preg_replace("/[^0-9]/", "", $comment_idx);

	if($comment_idx){
		$sql = "select idx, cmt_flag, work_idx, name, email from work_todaywork_comment where companyno='".$companyno."' and idx='".$comment_idx."'";
		$comment_info = selectQuery($sql);

		if($comment_info['idx']){
			$sql = "select idx,state,penalty_state from work_member where state = '0' and email = '".$comment_info['email']."' ";
			$member = selectQuery($sql);

			if($member['penalty_state']=='1'){
				echo $comment_info['email']."|".$comment_info['name']."|penalty_on";
				exit;
			}
			if($comment_info['cmt_flag']=='1'){
				$sql = "select idx, email, name from work_todaywork where state!='9' and companyno='".$companyno."' and idx='".$comment_info['work_idx']."'";
				$comment_info = selectQuery($sql);
				if($comment_info['idx']){
					echo $comment_info['email']."|".$comment_info['name']."|".$comment_info['idx'];
					exit;
				}
			}else{
				echo $comment_info['email']."|".$comment_info['name']."|".$comment_info['idx'];
				exit;
			}
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

			$send_email = $work_info['email'];
			$send_name = $work_info['name'];

			$sql = "select idx, state, penalty_state from work_member where email = '".$send_email."' and state = '0' ";
			$member = selectQuery($sql);

			if($member['penalty_state']=='1'){
				echo $send_email."|".$send_name."|penalty_on";
				exit;
			}

			//좋아요 이력조회
			//$sql = "select * from work_todaywork_like where work_idx='".$work_info['idx']."'";
			$sql = "select idx from work_todaywork_like where state='0' and companyno='".$companyno."' and work_idx='".$work_info['idx']."' and send_email='".$send_email."'";
			$work_like_info = selectQuery($sql);
			if($work_like_info['idx']){
				echo $send_email."|".$send_name."|like_check";
				exit;
			}else{
				echo $send_email."|".$send_name."|";
				exit;
			}
		}
	}
	exit;
}




//좋아요보내기
if($mode == "lives_like"){
	// $service = $_POST['service'];
	// $jf_idx = $_POST['jf_idx'];
	// $jf_idx = preg_replace("/[^0-9]/", "", $jf_idx);


	// $work_idx = $_POST['work_idx'];
	// $work_idx = preg_replace("/[^0-9]/", "", $work_idx);

	// $like_flag = $_POST['like_flag'];
	// $like_flag = preg_replace("/[^0-9]/", "", $like_flag);
	// if(!$like_flag){
	// 	$like_flag = '0';
	// }

	// $jl_comment = $_POST['jl_comment'];
	// $send_userid = $_POST['send_userid'];
	// $send_info = member_row_info($send_userid);

	// if($jf_idx){
	// 	$sql = "select idx from work_todaywork_like where state='0' and companyno='".$companyno."' and service='".$service."' and like_flag='".$like_flag."' and email='".$send_info['email']."' and send_email='".$user_id."' and workdate='".TODATE."'";
	// 	$like_info = selectQuery($sql);
	// 	//if(!$like_info['idx']){
	// 		$sql = "insert into work_todaywork_like(companyno, kind_flag, service, work_idx, like_flag, email, name, send_email, send_name, comment, type_flag, ip, workdate) values(";
	// 		$sql = $sql .= "'".$companyno."','".$jf_idx."', '".$service."', '".$work_idx."', '".$like_flag."', '".$send_info['email']."', '".$send_info['name']."', '".$user_id."', '".$user_name."', '".$jl_comment."', '".$type_flag."', '".LIP."', '".TODATE."')";
	// 		$insert_idx = insertIdxQuery($sql);
	// 		if($insert_idx){

	// 			//타임라인(좋아요)
	// 			work_data_log('0','8', $insert_idx, $user_id, $user_name, $send_info['email'], $send_info['name']);

	// 			//타임라인(좋아요 받음)
	// 			work_data_log('0','10', $insert_idx, $send_info['email'], $send_info['name'], $user_id, $user_name);


	// 			//1:인정하기, 2:응원하기, 3:칭찬하기, 4:격려하기, 5:축하하기, 6:감사하기

	// 			if($jf_idx == '1'){

	// 				//역량평가지표(좋아요 인정하기)
	// 				work_cp_reward("like", "0002", $send_info['email'], $insert_idx);

	// 			}else if($jf_idx == '2'){

	// 				//역량평가지표(좋아요 응원하기)
	// 				work_cp_reward("like", "0003", $send_info['email'], $insert_idx);

	// 			}else if($jf_idx == '3'){

	// 				//역량평가지표(좋아요 칭찬하기)
	// 				work_cp_reward("like", "0004", $send_info['email'], $insert_idx);

	// 			}else if($jf_idx == '4'){

	// 				//역량평가지표(좋아요 격려하기)
	// 				work_cp_reward("like", "0005", $send_info['email'], $insert_idx);

	// 			}else if($jf_idx == '5'){

	// 				//역량평가지표(좋아요 축하하기)
	// 				work_cp_reward("like", "0006", $send_info['email'], $insert_idx);

	// 			}else if($jf_idx == '6'){

	// 				//역량평가지표(좋아요 감사하기)
	// 				work_cp_reward("like", "0007", $send_info['email'], $insert_idx);

	// 			}

	// 			//역량평가지표(좋아요 보내기)
	// 			work_cp_reward("like", "0001", $send_info['email'], $insert_idx);


	// 			echo "complete";
	// 			exit;
	// 		}
	// 	//}
	// }
}

if($mode == "lives_like"){
	$idx = $_POST['mem_idx'];

	$sql = "select idx, state, penalty_state, email from work_member where idx = '".$idx."' and state = '0' ";
	$member = selectQuery($sql);

	if($member['penalty_state'] == '1'){
		echo "penalty_on";
		exit;
	}

	$sql = "select idx, state, email, workdate from work_todaywork_like where email = '".$member['email']."' and state = '0' and workdate = '".TODATE."' and service = 'live'";
	$enter = selectQuery($sql);

	if($enter['idx']){
		echo "today_like";
		exit;
	}
}

//보고업무 받을사람변경
if($mode == "work_report_user_add"){

	$idx = $_POST['idx'];
	$idx = preg_replace("/[^0-9]/", "", $idx);
	if($idx){

		//보고업무
		$sql = "select idx, work_idx from work_todaywork where state='0' and companyno='".$companyno."' and work_flag='1' and email='".$user_id."' and idx='".$idx."'";
		$work_info = selectQuery($sql);
		if($work_info['idx']){

			$report_user = array();
			$sql = "select idx, email from work_todaywork_report where state='0' and companyno='".$companyno."' and work_idx='".$work_info['idx']."'";
			$info = selectAllQuery($sql);
			for($i=0; $i<count($info['idx']); $i++){
				$report_user_id[$info['email'][$i]] = $info['email'][$i];
				//if($info['idx'][$i] && $info['idx'][$i] != end($info['idx'])){
				//	echo ",";
				//}
			}
			//보고받은사람
			if($report_user_id){
				$whereid = @implode("','",$report_user_id);
				if($whereid){
					//보고받은 사람
					$sql = "select idx, email, name from work_member where state='0' and companyno='".$companyno."'  and email in('".$whereid."')";
					$member_report_info = selectAllQuery($sql);

					$chall_user_chk = @implode(",",$member_report_info['idx']);

					//회원 전체 정보가져오기
					$member_info = member_list_all();
					$member_total_cnt = $member_info['total_cnt'];

					//부서별 정렬순
					$sql = "select part, partno from work_member as a left join work_team as b on (a.partno=b.idx) where b.state='0' and a.companyno='".$companyno."' group by partno, part order by part asc";
					$part_info = selectAllQuery($sql);

					?>
						<div class="layer_deam"></div>
						<div class="layer_user_in">
							<div class="layer_user_box none" id="layer_test_01">
								<div class="layer_user_search">
									<div class="layer_user_search_desc">
										<strong>업무 받을 사람 선택</strong>
										<span id="usercnt">전체 <?=$member_total_cnt?>명, <?=count($member_report_info['idx']);?>명 선택</span>
									</div>
									<div class="layer_user_search_box">
										<input type="text" class="input_search" placeholder="이름, 부서명을 검색" id="input_todaywork_search"/>
										<button id="input_todaywork_search_btn"><span>검색</span></button>
									</div>
								</div>
								<div class="layer_user_slc_list">
									<div class="layer_user_slc_list_in">
										<ul>
											<?for($i=0; $i<count($member_report_info['idx']); $i++){
												$mem_report_idx = $member_report_info['idx'][$i];
												$mem_report_email = $member_report_info['email'][$i];
												$mem_report_name = $member_report_info['name'][$i];
												$report_profile_img = profile_img_info($mem_report_email);
											?>
												<li id="user_<?=$mem_report_idx?>">
													<div class="user_img" style="background-image:url(<?=$report_profile_img?>);" title="<?=$mem_report_name?>"></div>
													<div class="user_name"><strong><?=$mem_report_name?></strong></div>
													<button class="user_slc_del" value="<?=$mem_report_idx?>" title="삭제"><span>삭제</span></button>
												</li>
											<?}?>
										</ul>
									</div>
								</div>

								<div class="layer_user_info">
									<ul>
									<?
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
											$partno 	= $member_info['partno'][$i];
											$j = 0;
											foreach($mem_name[$partno] as $key=>$val)
											{
												unset($mem_name[$partno][$key]);
												$new_key = $j;
												$mem_name[$partno][$new_key] = $val;
												$j++;
											}

											$j = 0;
											foreach($mem_idx[$partno] as $key=>$val)
											{
												unset($mem_idx[$partno][$key]);
												$new_key = $j;
												$mem_idx[$partno][$new_key] = $val;
												$j++;
											}

											$j = 0;
											foreach($mem_uid[$partno] as $key=>$val)
											{
												unset($mem_uid[$partno][$key]);
												$new_key = $j;
												$mem_uid[$partno][$new_key] = $val;
												$j++;
											}

										}

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
													$profile_main_img_src = profile_img_info($mem_uid[$partno][$j]);
													if($report_user_id[$mem_uid[$partno][$j]] == $mem_uid[$partno][$j]){

														$mem_btn_class=" class='on'";
													}else{
														$mem_btn_class="";
													}

													?>
													<dd id="udd_<?=$mem_idx[$partno][$j]?>">
														<button value="<?=$mem_idx[$partno][$j]?>" id="team_<?=$partno?>"<?=$mem_btn_class?>>
															<?=($user_id == $mem_uid[$partno][$j]?"<img src=\"/html/images/pre/ico_me.png\" alt=\"\" class=\"user_me\" />":"");?>
														<div class="user_img" style="background-image:url('<?=$profile_main_img_src?>');" id="profile_character_img"></div>
														<div class="user_name" value="<?=$mem_uid[$partno][$j]?>">
															<strong><?=$mem_name[$partno][$j]?></strong>
															<span><?=$part_info['part'][$i]?></span>
														</div>
													</button>
													</dd>
												<?}?>
											</dl>
										</li>
									<?}?>
									</ul>
								</div>
							</div>

							<div class="layer_user_btn">
								<button class="layer_user_all_slc" id="layer_user_all_slc"><span>전체선택</span></button>
								<button class="layer_user_cancel" id="layer_report_cancel"><span>취소</span></button>
								<button class="layer_user_submit<?=count($member_report_info['idx'])>0?" on":""?>" id="layer_report_user"><span>보고하기</span></button>
							</div>
						</div>
				<?php
				echo "|".$chall_user_chk;
				}
			}


		}




	}
	exit;
}



//보고업무 받을사람변경
if($mode == "work_user_add"){

	$idx = $_POST['idx'];
	$idx = preg_replace("/[^0-9]/", "", $idx);

	if($idx){

		$work_user_id = array();
		$layer_btn_name = "";

		//오늘업무
		$sql = "select idx, work_idx, work_flag, share_flag from work_todaywork where state!='9' and companyno='".$companyno."' and email='".$user_id."' and idx='".$idx."'";
		$work_info = selectQuery($sql);

		if($work_info['idx']){

			//공유업무
			if($work_info['share_flag']=='1'){

				$layer_title_desc = "업무 공유 받을 사람 선택";
				$layer_submit_desc ="공유하기";
				$layer_btn_name = "share";
				$sql = "select idx, email from work_todaywork_share where state='0' and companyno='".$companyno."' and work_idx='".$work_info['idx']."'";
				$mem_user_info = selectAllQuery($sql);
				for($i=0; $i<count($mem_user_info['idx']); $i++){
					$work_user_id[$mem_user_info['email'][$i]] = $mem_user_info['email'][$i];
				}

			}else{

				//보고업무
				if($work_info['work_flag']=='1'){

					$layer_title_desc = "업무 보고 받을 사람 선택";
					$layer_submit_desc ="보고하기";
					$layer_btn_name = "report";
					$sql = "select idx, email from work_todaywork_report where state='0' and companyno='".$companyno."' and work_idx='".$work_info['idx']."'";
					$mem_user_info = selectAllQuery($sql);
					for($i=0; $i<count($mem_user_info['idx']); $i++){
						$work_user_id[$mem_user_info['email'][$i]] = $mem_user_info['email'][$i];
					}

				//요청업무
				}else if($work_info['work_flag']=='3'){

					$layer_title_desc = "업무 요청 받을 사람 선택";
					$layer_submit_desc ="요청하기";
					$layer_btn_name = "req";
					$sql = "select idx, email from work_todaywork_user where state='0' and companyno='".$companyno."' and work_idx='".$work_info['idx']."'";
					$mem_user_info = selectAllQuery($sql);
					for($i=0; $i<count($mem_user_info['idx']); $i++){
						$work_user_id[$mem_user_info['email'][$i]] = $mem_user_info['email'][$i];
					}
				}
			}


			if($work_user_id){
				$whereid = @implode("','",$work_user_id);
				if($whereid){

					//회원정보 받기
					$sql = "select idx, email, name from work_member where state='0' and companyno='".$companyno."' and email in('".$whereid."')";
					$member_user_info = selectAllQuery($sql);

					$user_chk_cnt = @implode(",",$member_user_info['idx']);

					//회원 전체 정보가져오기
					$member_info = member_list_all();
					$member_total_cnt = $member_info['total_cnt'];


					//부서별 정렬순
					$part_info = member_part_info();

					?>
						<div class="layer_deam"></div>
						<div class="layer_user_in">
							<div class="layer_user_box none" id="layer_test_01">
								<div class="layer_user_search">
									<div class="layer_user_search_desc">
										<strong><?=$layer_title_desc?></strong>
										<span id="usercnt">전체 <?=$member_total_cnt?>명, <?=count($member_user_info['idx']);?>명 선택</span>
									</div>
									<div class="layer_user_search_box">
										<input type="text" class="input_search" placeholder="이름, 부서명을 검색" id="input_todaywork_search"/>
										<button id="input_todaywork_search_btn"><span>검색</span></button>
									</div>
								</div>

								<div class="layer_user_slc_list">
									<div class="layer_user_slc_list_in">
										<ul>
											<?for($i=0; $i<count($member_user_info['idx']); $i++){
												$mem_user_idx = $member_user_info['idx'][$i];
												$mem_user_email = $member_user_info['email'][$i];
												$mem_user_name = $member_user_info['name'][$i];
												$mem_user_profile_img = profile_img_info($mem_user_email);
											?>
												<li id="user_<?=$mem_user_idx?>">
													<div class="user_img" style="background-image:url(<?=$mem_user_profile_img?>);" title="<?=$mem_user_name?>"></div>
													<div class="user_name"><strong><?=$mem_user_name?></strong></div>
													<button class="user_slc_del" value="<?=$mem_user_idx?>" title="삭제"><span>삭제</span></button>
												</li>
											<?}?>

										</ul>
									</div>
								</div>

								<div class="layer_user_info">
									<ul>
									<?
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
											$partno 	= $member_info['partno'][$i];
											$j = 0;
											foreach($mem_name[$partno] as $key=>$val)
											{
												unset($mem_name[$partno][$key]);
												$new_key = $j;
												$mem_name[$partno][$new_key] = $val;
												$j++;
											}

											$j = 0;
											foreach($mem_idx[$partno] as $key=>$val)
											{
												unset($mem_idx[$partno][$key]);
												$new_key = $j;
												$mem_idx[$partno][$new_key] = $val;
												$j++;
											}

											$j = 0;
											foreach($mem_uid[$partno] as $key=>$val)
											{
												unset($mem_uid[$partno][$key]);
												$new_key = $j;
												$mem_uid[$partno][$new_key] = $val;
												$j++;
											}

										}

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
													$profile_main_img_src = profile_img_info($mem_uid[$partno][$j]);
													if($work_user_id[$mem_uid[$partno][$j]] == $mem_uid[$partno][$j]){

														$mem_btn_class=" class='on'";
													}else{
														$mem_btn_class="";
													}

													?>
													<dd id="udd_<?=$mem_idx[$partno][$j]?>">
														<button value="<?=$mem_idx[$partno][$j]?>" id="team_<?=$partno?>"<?=$mem_btn_class?>>
															<?=($user_id == $mem_uid[$partno][$j]?"<img src=\"/html/images/pre/ico_me.png\" alt=\"\" class=\"user_me\" />":"");?>
														<div class="user_img" style="background-image:url('<?=$profile_main_img_src?>');" id="profile_character_img"></div>
														<div class="user_name" value="<?=$mem_uid[$partno][$j]?>">
															<strong><?=$mem_name[$partno][$j]?></strong>
															<span><?=$part_info['part'][$i]?></span>
														</div>
													</button>
													</dd>
												<?}?>
											</dl>
										</li>
									<?}?>
									</ul>
								</div>
							</div>

							<div class="layer_user_btn">
								<button class="layer_user_all_slc" id="layer_user_all_slc"><span>전체선택</span></button>
								<button class="layer_user_cancel" id="layer_report_cancel"><span>취소</span></button>

								<?if($layer_btn_name=="share"){?>
									<button class="layer_user_submit<?=count($mem_user_info['idx'])>0?" on":""?>" id="layer_share_user"><span><?=$layer_submit_desc?></span></button>
								<?}else if($layer_btn_name=="report"){?>
									<button class="layer_user_submit<?=count($mem_user_info['idx'])>0?" on":""?>" id="layer_report_user"><span><?=$layer_submit_desc?></span></button>
								<?}else if($layer_btn_name=="req"){?>
									<button class="layer_user_submit<?=count($mem_user_info['idx'])>0?" on":""?>" id="layer_req_user"><span><?=$layer_submit_desc?></span></button>
								<?}else{?>
									<button class="layer_user_submit<?=count($mem_user_info['idx'])>0?" on":""?>" id="layer_req_user"><span><?=$layer_submit_desc?></span></button>
								<?}?>
							</div>
						</div><?echo "|".$user_chk_cnt;?>
					<?
				}
			}
		}
		exit;

		//사용안함
		if($work_info['idx']){
			$report_user = array();
			$sql = "select idx, email from work_todaywork_report where state='0' and companyno='".$companyno."' and work_idx='".$work_info['idx']."'";
			$info = selectAllQuery($sql);
			for($i=0; $i<count($info['idx']); $i++){
				$report_user_id[$info['email'][$i]] = $info['email'][$i];
			}

			//보고받은사람
			if($report_user_id){
				$whereid = @implode("','",$report_user_id);

				if($whereid){

					//보고받은 사람
					$sql = "select idx, email, name from work_member where state='0' and companyno='".$companyno."' and email in('".$whereid."')";
					$member_report_info = selectAllQuery($sql);

					$chall_user_chk = @implode(",",$member_report_info['idx']);

					//회원 전체 정보가져오기
					$member_info = member_list_all();
					$member_total_cnt = $member_info['total_cnt'];


					//부서별 정렬순
					$part_info = member_part_info();

					?>
						<div class="layer_deam"></div>
						<div class="layer_user_in">
							<div class="layer_user_box none" id="layer_test_01">
								<div class="layer_user_search">
									<div class="layer_user_search_desc">
										<strong>업무 받을 사람 선택</strong>
										<span id="usercnt">전체 <?=$member_total_cnt?>명, <?=count($member_report_info['idx']);?>명 선택</span>
									</div>
									<div class="layer_user_search_box">
										<input type="text" class="input_search" placeholder="이름, 부서명을 검색" id="input_todaywork_search"/>
										<button id="input_todaywork_search_btn"><span>검색</span></button>
									</div>
								</div>

								<div class="layer_user_slc_list">
									<div class="layer_user_slc_list_in">
										<ul>
											<?for($i=0; $i<count($member_report_info['idx']); $i++){
												$mem_report_idx = $member_report_info['idx'][$i];
												$mem_report_email = $member_report_info['email'][$i];
												$mem_report_name = $member_report_info['name'][$i];
												$report_profile_img = profile_img_info($mem_report_email);
											?>
												<li id="user_<?=$mem_report_idx?>">
													<div class="user_img" style="background-image:url(<?=$report_profile_img?>);" title="<?=$mem_report_name?>"></div>
													<div class="user_name"><strong><?=$mem_report_name?></strong></div>
													<button class="user_slc_del" value="<?=$mem_report_idx?>" title="삭제"><span>삭제</span></button>
												</li>
											<?}?>

										</ul>
									</div>
								</div>

								<div class="layer_user_info">
									<ul>
									<?
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
											$partno 	= $member_info['partno'][$i];
											$j = 0;
											foreach($mem_name[$partno] as $key=>$val)
											{
												unset($mem_name[$partno][$key]);
												$new_key = $j;
												$mem_name[$partno][$new_key] = $val;
												$j++;
											}

											$j = 0;
											foreach($mem_idx[$partno] as $key=>$val)
											{
												unset($mem_idx[$partno][$key]);
												$new_key = $j;
												$mem_idx[$partno][$new_key] = $val;
												$j++;
											}

											$j = 0;
											foreach($mem_uid[$partno] as $key=>$val)
											{
												unset($mem_uid[$partno][$key]);
												$new_key = $j;
												$mem_uid[$partno][$new_key] = $val;
												$j++;
											}

										}

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
													$profile_main_img_src = profile_img_info($mem_uid[$partno][$j]);
													if($report_user_id[$mem_uid[$partno][$j]] == $mem_uid[$partno][$j]){

														$mem_btn_class=" class='on'";
													}else{
														$mem_btn_class="";
													}

													?>
													<dd id="udd_<?=$mem_idx[$partno][$j]?>">
														<button value="<?=$mem_idx[$partno][$j]?>" id="team_<?=$partno?>"<?=$mem_btn_class?>>
															<?=($user_id == $mem_uid[$partno][$j]?"<img src=\"/html/images/pre/ico_me.png\" alt=\"\" class=\"user_me\" />":"");?>
														<div class="user_img" style="background-image:url('<?=$profile_main_img_src?>');" id="profile_character_img"></div>
														<div class="user_name" value="<?=$mem_uid[$partno][$j]?>">
															<strong><?=$mem_name[$partno][$j]?></strong>
															<span><?=$part_info['part'][$i]?></span>
														</div>
													</button>
													</dd>
												<?}?>
											</dl>
										</li>
									<?}?>
									</ul>
								</div>
							</div>

							<div class="layer_user_btn">
								<button class="layer_user_all_slc" id="layer_user_all_slc"><span>전체선택</span></button>
								<button class="layer_user_cancel" id="layer_report_cancel"><span>취소</span></button>
								<button class="layer_user_submit<?=count($member_report_info['idx'])>0?" on":""?>" id="layer_report_user"><span>보고하기</span></button>
							</div>
						</div>
				<?php
				echo "|".$chall_user_chk;
				}
			}


		}

	}
	exit;
}




//공유 받을사람 변경하기 - 완료
if($mode == "work_share_add"){

	$idx = $_POST['idx'];
	$idx = preg_replace("/[^0-9]/", "", $idx);
	$user_chk_val = $_POST['user_chk_val'];
	$result = null;

	if($user_chk_val && $idx){
		$work_mem_idx = trim($user_chk_val);

		//공유업무
		$sql = "select idx, state, work_flag, decide_flag, email, name, work_idx, repeat_flag, notice_flag, share_flag, type_flag, file_flag, title, contents, workdate from work_todaywork where state!='9'";
		$sql = $sql .=" and companyno='".$companyno."' and share_flag='1' and idx='".$idx."' and email='".$user_id."'";
		$sql = $sql .= " order by sort asc, idx desc";
		$works_share_info = selectQuery($sql);

		//회원정보 내역
		$sql = "select idx, email, name, highlevel, part, partno from work_member where state='0' and companyno='".$companyno."' and idx in (".$work_mem_idx.")";
		$work_mem_info = selectAllQuery($sql);

		//요청 받는사람이 1명보다 큰경우
		$work_req_cnt = count($work_mem_info['idx']);
		if($work_req_cnt > 1){
			$work_req_change_cnt = $work_req_cnt - 1;
			//타임라인(보고함)
			work_data_multi_log('0','23', $works_share_info['idx'], $user_id, $user_name, $work_mem_info['email'][0], $work_mem_info['name'][0], $work_req_change_cnt);
		}

		if($work_mem_info['idx']){
			if($works_share_info['idx']){
				$work_idx = $works_share_info['idx'];
				$work_title = $works_share_info['title'];
				$work_contents = $works_share_info['contents'];
				$work_workdate = $works_share_info['workdate'];
				$work_decide_flag = $works_share_info['decide_flag'];
				$work_type_flag = $works_share_info['type_flag'];
				$work_work_flag = $works_share_info['work_flag'];
				$work_file_flag = $works_share_info['file_flag'];

				for($i=0; $i<count($work_mem_info['idx']); $i++){
					$work_mem_email = $work_mem_info['email'][$i];
					$work_mem_name = $work_mem_info['name'][$i];
					$work_mem_highlevel = $work_mem_info['highlevel'][$i];
					$work_mem_part = $work_mem_info['part'][$i];
					$work_mem_partno = $work_mem_info['partno'][$i];


					//보고자 내역체크
					$sql = "select idx from work_todaywork_share where state='0' and companyno='".$companyno."' and work_idx='".$idx."' and email='".$work_mem_email."'";
					$work_row = selectQuery($sql);
					if(!$work_row['idx']){

						//보고자 내역 저장 및 보고업무 저장
						$sql = "insert into work_todaywork_share(companyno, work_idx, work_email, work_name, email, name, workdate, ip) values(";
						$sql = $sql .= "'".$companyno."','".$idx."','".$user_id."','".$user_name."','".$work_mem_email."','".$work_mem_name."','".TODATE."','".LIP."')";
						$insert_idx = insertIdxQuery($sql);
						if($insert_idx){
							$result = true;
							// 이름 안나오는 오류 share_flag = 2 추가(김정훈)
							$sql = "insert into work_todaywork(companyno, email, name, highlevel, type_flag, work_flag, part_flag, part, decide_flag, file_flag, share_flag, work_idx, title, contents, workdate, ip)";
							$sql = $sql .=" values('".$companyno."', '".$work_mem_email."','".$work_mem_name."','".$work_mem_highlevel."','".$work_type_flag."','".$work_work_flag."','".$work_mem_partno."','".$work_mem_part."','".$work_decide_flag."','".$work_file_flag."',2,'".$work_idx."','".$work_title."', '".$work_contents."','".$work_workdate."','".LIP."')";
							$res_idx = insertIdxQuery($sql);

							if($work_req_cnt == 1){
								//타임라인(보고함)
								work_data_log('0','23', $works_share_info['idx'], $user_id, $user_name, $work_mem_email, $work_mem_name, $res_idx);
							}

							//타임라인(보고받음)
							work_data_log('0','22', $works_share_info['idx'], $work_mem_email, $work_mem_name, $user_id, $user_name, $res_idx);
						}
					}
				}

				//보고자에서 제외된경우 삭제 처리
				$whereid = @implode("','", $work_mem_info['email']);
				$sql = "select idx, work_idx, work_email, work_name, email, name from work_todaywork_share where state='0' and companyno='".$companyno."' and work_idx='".$idx."' and email not in ('".$whereid."')";
				$work_mem_not_info = selectAllQuery($sql);
				if($work_mem_not_info['idx']){
					for($i=0; $i<count($work_mem_not_info['idx']); $i++){

						//삭제처리
						$sql = "update work_todaywork_share set state='9', editdate=".DBDATE." where state='0' and idx='".$work_mem_not_info['idx'][$i]."'";
						if($user_id=='sadary0@nate.com'){
						//	echo $sql;
						//	echo "\n\n";
						}
						$up = updateQuery($sql);
						if($up){
							$result = true;
							$sql = "select idx from work_todaywork where state='0' and companyno='".$companyno."' and work_idx='".$work_idx ."' and email='".$work_mem_not_info['email'][$i]."'";
							if($user_id=='sadary0@nate.com'){
							//	echo $sql;
							//	echo "\n\n";
							}
							$work_info = selectQuery($sql);
							if($work_info['idx']){
								$sql = "update work_todaywork set state='9', editdate=".DBDATE." where state='0' and idx='".$work_info['idx']."'";
								if($user_id=='sadary0@nate.com'){
								//	echo $sql;
								//	echo "\n\n";
								}
								$up = updateQuery($sql);
							}

							//타임라인(공유함-삭제)
							work_data_log('9','4', $work_mem_not_info['work_idx'][$i], $user_id, $user_name, $work_mem_not_info['work_idx'][$i]);

							//타임라인(공유받음-삭제)
							work_data_log('9','3', $work_mem_not_info['work_idx'][$i], $work_mem_not_info['work_email'][$i], $work_mem_not_info['work_name'][$i], $work_mem_not_info['work_idx'][$i]);

							//역량평가지표(업무공유 하기-삭제)
							work_cp_delreward("work", $work_mem_not_info['work_idx'][$i]);
						}
					}
				}
			}

			if($result){
				echo "complete";
				exit;
			}

		}
	}
}



//보고자 변경
if($mode == "work_report_add"){

	$idx = $_POST['idx'];
	$idx = preg_replace("/[^0-9]/", "", $idx);
	$user_chk_val = $_POST['user_chk_val'];
	$result = null;

	if($user_chk_val && $idx){
		$work_mem_idx = trim($user_chk_val);

		//회원정보 내역
		$sql = "select idx, email, name, highlevel, part, partno from work_member where state='0' and companyno='".$companyno."' and idx in (".$work_mem_idx.")";
		$work_mem_info = selectAllQuery($sql);

		//보고업무
		$sql = "select idx, state, work_flag, decide_flag, email, name, work_idx, repeat_flag, notice_flag, share_flag, type_flag, file_flag, title, contents, workdate from work_todaywork where state!='9'";
		$sql = $sql .=" and companyno='".$companyno."' and work_flag='1' and work_idx is null and idx='".$idx."' and email='".$user_id."'";
		$sql = $sql .= " order by sort asc, idx desc";
		$works_report_info = selectQuery($sql);


		//보고 받는사람이 1명보다 큰경우
		$work_req_cnt = count($work_mem_info['idx']);

		if($work_req_cnt > 1){
			$work_req_change_cnt = $work_req_cnt - 1;

			//타임라인(요청함)
			work_data_multi_log('0','23', $works_report_info['idx'], $user_id, $user_name, $work_mem_info['email'][0], $work_mem_info['name'][0], $work_req_change_cnt);
		}


		$result = false;
		if($work_mem_info['idx']){
			if($works_report_info['idx']){
				$work_idx = $works_report_info['idx'];
				$work_title = $works_report_info['title'];
				$work_contents = $works_report_info['contents'];
				$work_workdate = $works_report_info['workdate'];
				$work_decide_flag = $works_report_info['decide_flag'];
				$work_type_flag = $works_report_info['type_flag'];
				$work_work_flag = $works_report_info['work_flag'];
				$work_file_flag = $works_report_info['file_flag'];

				for($i=0; $i<count($work_mem_info['idx']); $i++){
					$work_mem_email = $work_mem_info['email'][$i];
					$work_mem_name = $work_mem_info['name'][$i];
					$work_mem_highlevel = $work_mem_info['highlevel'][$i];
					$work_mem_part = $work_mem_info['part'][$i];
					$work_mem_partno = $work_mem_info['partno'][$i];


					//보고자 내역체크
					$sql = "select idx from work_todaywork_report where state='0' and companyno='".$companyno."' and work_idx='".$idx."' and email='".$work_mem_email."'";
					$work_row = selectQuery($sql);

					if(!$work_row['idx']){
						//보고자 내역 저장 및 보고업무 저장
						$sql = "insert into work_todaywork_report(companyno, work_idx, work_email, work_name, email, name, workdate, ip) values(";
						$sql = $sql .= "'".$companyno."','".$idx."','".$user_id."','".$user_name."','".$work_mem_email."','".$work_mem_name."','".TODATE."','".LIP."')";
						$insert_idx = insertIdxQuery($sql);
						if($insert_idx){
							$result = true;
							$sql = "insert into work_todaywork(companyno, email, name, highlevel, type_flag, work_flag, part_flag, part, decide_flag, file_flag, work_idx, title, contents, workdate, ip)";
							$sql = $sql .=" values('".$companyno."', '".$work_mem_email."','".$work_mem_name."','".$work_mem_highlevel."','".$work_type_flag."','".$work_work_flag."','".$work_mem_partno."','".$work_mem_part."','".$work_decide_flag."','".$work_file_flag."','".$work_idx."','".$work_title."', '".$work_contents."','".$work_workdate."','".LIP."')";
							$res_idx = insertIdxQuery($sql);

							if($work_req_cnt == 1){
								//타임라인(보고함)
								work_data_log('0','23', $works_report_info['idx'], $user_id, $user_name, $work_mem_email, $work_mem_name, $res_idx);
							}

							//타임라인(보고받음)
							work_data_log('0','22', $works_report_info['idx'], $work_mem_email, $work_mem_name, $user_id, $user_name, $res_idx);

						}
					}else{

						//보고자변경이 없을때
						$result = true;
					}
				}


				//보고자에서 제외된경우 삭제 처리
				$whereid = @implode("','", $work_mem_info['email']);
				$sql = "select idx, work_idx, work_email, work_name, email, name from work_todaywork_report where state='0' and companyno='".$companyno."' and work_idx='".$idx."' and email not in ('".$whereid."')";
				$work_mem_not_info = selectAllQuery($sql);
				if($work_mem_not_info['idx']){
					for($i=0; $i<count($work_mem_not_info['idx']); $i++){

						//삭제처리
						$sql = "update work_todaywork_report set state='9', editdate=".DBDATE." where state='0' and idx='".$work_mem_not_info['idx'][$i]."'";
						$up = updateQuery($sql);
						if($up){
							$result = true;
							$sql = "select idx from work_todaywork where state='0' and companyno='".$companyno."' and work_idx='".$work_idx ."' and email='".$work_mem_not_info['email'][$i]."'";
							$work_info = selectQuery($sql);
							if($work_info['idx']){
								$sql = "update work_todaywork set state='9', editdate=".DBDATE." where state='0' and idx='".$work_info['idx']."'";
								$up = updateQuery($sql);
							}

							//타임라인(보고함-삭제)
							work_data_log('9','23', $work_mem_not_info['work_idx'][$i], $user_id, $user_name, $work_mem_not_info['work_idx'][$i]);

							//타임라인(보고받음-삭제)
							work_data_log('9','22', $work_mem_not_info['work_idx'][$i], $work_mem_not_info['work_email'][$i], $work_mem_not_info['work_name'][$i], $work_mem_not_info['work_idx'][$i]);

							//역량평가지표(업무보고 하기-삭제)
							work_cp_delreward("work", $work_mem_not_info['work_idx'][$i]);
						}
					}
				}
			}

			if($result){
				echo "complete";
				exit;
			}
		}else{
			echo "no_member";
			exit;
		}
	}else{
		echo "not_user";
		exit;
	}
}



//요청 받는사람 변경
if($mode == "work_req_add"){

	$idx = $_POST['idx'];
	$idx = preg_replace("/[^0-9]/", "", $idx);
	$user_chk_val = $_POST['user_chk_val'];
	$result = null;

	if($user_chk_val && $idx){
		$work_mem_idx = trim($user_chk_val);

		//회원정보 내역
		$sql = "select idx, email, name, highlevel, part, partno from work_member where state='0' and companyno='".$companyno."' and idx in (".$work_mem_idx.")";
		$work_mem_info = selectAllQuery($sql);

		//보고업무
		$sql = "select idx, state, work_flag, decide_flag, email, name, work_idx, repeat_flag, notice_flag, share_flag, type_flag, file_flag, title, contents, workdate from work_todaywork where state!='9'";
		$sql = $sql .=" and companyno='".$companyno."' and work_flag='3' and work_idx is null and idx='".$idx."' and email='".$user_id."'";
		$sql = $sql .= " order by sort asc, idx desc";
		$works_report_info = selectQuery($sql);


		//요청 받는사람이 1명보다 큰경우
		$work_req_cnt = count($work_mem_info['idx']);

		if($work_req_cnt > 1){
			$work_req_change_cnt = $work_req_cnt - 1;

			//타임라인(요청함)
			work_data_multi_log('0','7', $work_idx, $user_id, $user_name, $work_mem_info['email'][0], $work_mem_info['name'][0], $work_req_change_cnt);
		}


		$result = false;
		if($work_mem_info['idx']){
			if($works_report_info['idx']){
				$work_idx = $works_report_info['idx'];
				$work_title = $works_report_info['title'];
				$work_contents = $works_report_info['contents'];
				$work_workdate = $works_report_info['workdate'];
				$work_decide_flag = $works_report_info['decide_flag'];
				$work_type_flag = $works_report_info['type_flag'];
				$work_work_flag = $works_report_info['work_flag'];
				$work_file_flag = $works_report_info['file_flag'];

				for($i=0; $i<count($work_mem_info['idx']); $i++){
					$work_mem_email = $work_mem_info['email'][$i];
					$work_mem_name = $work_mem_info['name'][$i];
					$work_mem_highlevel = $work_mem_info['highlevel'][$i];
					$work_mem_part = $work_mem_info['part'][$i];
					$work_mem_partno = $work_mem_info['partno'][$i];


					//요청 받는 사람 조회
					$sql = "select idx from work_todaywork_user where state='0' and companyno='".$companyno."' and work_idx='".$idx."' and email='".$work_mem_email."'";
					$work_row = selectQuery($sql);

					if(!$work_row['idx']){
						//보고자 내역 저장 및 보고업무 저장
						$sql = "insert into work_todaywork_user(companyno, work_idx, work_email, work_name, email, name, workdate, ip) values(";
						$sql = $sql .= "'".$companyno."','".$idx."','".$user_id."','".$user_name."','".$work_mem_email."','".$work_mem_name."','".TODATE."','".LIP."')";
						$insert_idx = insertIdxQuery($sql);
						if($insert_idx){
							$result = true;
							$sql = "insert into work_todaywork(companyno, email, name, highlevel, type_flag, work_flag, part_flag, part, decide_flag, file_flag, work_idx, title, contents, workdate, ip)";
							$sql = $sql .=" values('".$companyno."', '".$work_mem_email."','".$work_mem_name."','".$work_mem_highlevel."','".$work_type_flag."','".$work_work_flag."','".$work_mem_partno."','".$work_mem_part."','".$work_decide_flag."','".$work_file_flag."','".$work_idx."','".$work_title."', '".$work_contents."','".$work_workdate."','".LIP."')";
							$res_idx = insertIdxQuery($sql);

							//요청한사람이 1명일때
							if($work_req_cnt == 1){
								//타임라인(요청함)
								work_data_log('0','7', $works_report_info['idx'], $user_id, $user_name, $work_mem_email, $work_mem_name, $res_idx);
							}

							//타임라인(요청받음)
							work_data_log('0','6', $works_report_info['idx'], $work_mem_email, $work_mem_name, $user_id, $user_name, $res_idx);

						}
					}else{

						//보고자변경이 없을때
						$result = true;
					}
				}


				//요청 받는자가 제외된경우 삭제 처리
				$whereid = @implode("','", $work_mem_info['email']);
				$sql = "select idx, work_idx, work_email, work_name, email, name from work_todaywork_user where state='0' and companyno='".$companyno."' and work_idx='".$idx."' and email not in ('".$whereid."')";
				$work_mem_not_info = selectAllQuery($sql);
				if($work_mem_not_info['idx']){
					for($i=0; $i<count($work_mem_not_info['idx']); $i++){

						//삭제처리
						$sql = "update work_todaywork_user set state='9', editdate=".DBDATE." where state='0' and idx='".$work_mem_not_info['idx'][$i]."'";
						$up = updateQuery($sql);
						if($up){
							$result = true;
							$sql = "select idx from work_todaywork where state='0' and companyno='".$companyno."' and work_idx='".$work_idx ."' and email='".$work_mem_not_info['email'][$i]."'";
							$work_info = selectQuery($sql);
							if($work_info['idx']){
								$sql = "update work_todaywork set state='9', editdate=".DBDATE." where state='0' and idx='".$work_info['idx']."'";
								$up = updateQuery($sql);
							}

							//타임라인(보고함-삭제)
							work_data_log('9','7', $work_mem_not_info['work_idx'][$i], $user_id, $user_name, $work_mem_not_info['work_idx'][$i]);

							//타임라인(보고받음-삭제)
							work_data_log('9','6', $work_mem_not_info['work_idx'][$i], $work_mem_not_info['work_email'][$i], $work_mem_not_info['work_name'][$i], $work_mem_not_info['work_idx'][$i]);

							//역량평가지표(업무요청 하기-삭제)
							work_cp_delreward("work", $work_mem_not_info['work_idx'][$i]);
						}
					}
				}
			}

			if($result){
				echo "complete";
				exit;
			}
		}else{
			echo "no_member";
			exit;
		}
	}else{
		echo "not_user";
		exit;
	}
}


//파일추가
if($mode == "works_files_add"){

	$work_idx = $_POST['work_idx'];
	$work_date = $_POST['work_date'];
	$work_idx = preg_replace("/[^0-9]/", "", $work_idx);
	$work_date = str_replace(".", "-", $work_date);


	if ($work_idx && $_FILES){

		//업로드 제한 확장자
		$format_ext = array('asp', 'php', 'jsp', 'xml', 'html', 'htm', 'aspx', 'exe', 'exec', 'java', 'js', 'class', 'as', 'pl', 'mm', 'o', 'c', 'h', 'm', 'cc', 'cpp', 'hpp', 'cxx', 'hxx', 'lib', 'lbr', 'ini', 'py', 'pyc', 'pyo', 'bak', '$$$', 'swp', 'sym', 'sys', 'cfg', 'chk', 'log', 'lo');
		for($i=0; $i<count($_FILES['files']['name']); $i++){
			$max_file_size = MAX_FILE_SIZE * 1024 * 1024;
			$files_for_size = $_FILES['files']['size'][$i];
			$files_for_name = $_FILES['files']['name'][$i];

			if($files_for_size > $max_file_size){
				echo "files_size_over|";
				exit;
			}

			$ext = @end(explode('.', $files_for_name));
			if(in_array($ext, $format_ext)){
				echo "files_format";
				exit;
			}


			//파일이 있을경우

			//$res_idx = 1;
			//파일정보, 서비스명, 번호
			$upload_result = work_upload_files($_FILES, "work", $work_idx);
			if($upload_result){

				for($i=0; $i<count($upload_result['result']); $i++){
					$upload_res = $upload_result['result'][$i];
					$upload_num = $upload_result['num'][$i];
					$upload_format = $upload_result['format'][$i];
					$upload_work_idx = $upload_result['work_idx'][$i];
					$upload_file_path = $upload_result['file_path'][$i];
					$upload_file_name = $upload_result['file_name'][$i];
					$upload_file_real_name = addslashes($upload_result['file_real_name'][$i]);
					$upload_file_type = $upload_result['file_type'][$i];
					$upload_file_size = $upload_result['file_size'][$i];
					$upload_file_width = $upload_result['file_width'][$i];
					$upload_file_height = $upload_result['file_height'][$i];

					$sql = "select idx from work_filesinfo_todaywork where state='0' and companyno='".$companyno."' and num='".$upload_num."' and work_idx='".$upload_work_idx."' and email='".$user_id."'";
					$work_filesinfo = selectQuery($sql);
					if(!$work_filesinfo['idx']){

						//파일형식:file, 이미지형식:img
						if($upload_format == "file"){

							$upload_file_query = ",file_format";
							$upload_file_value = ",'".$upload_format."'";

						}else if($upload_format == "img"){

							$upload_file_query = ",file_format,file_width,file_height";
							$upload_file_value = ",'".$upload_format."','".$upload_file_width."','".$upload_file_height."'";
						}

						$sql = "insert into work_filesinfo_todaywork(work_idx,num,companyno,email,file_path,file_name,file_real_name,file_size,file_type".$upload_file_query.",type_flag,ip,workdate)";
						$sql = $sql .= " values('".$upload_work_idx."','','".$companyno."','".$user_id."','".$upload_file_path."','".$upload_file_name."','".$upload_file_real_name."','".$upload_file_size."','".$upload_file_type."'".$upload_file_value.",'".$type_flag."','".LIP."','".$work_date."')";
						$insert_idx = insertIdxQuery($sql);
						if($insert_idx){
							echo "complete";
							exit;
						}else{
							echo "failed";
							exit;
						}
					}
				}
			}

		}

	}
	exit;
}



//오늘업무 파일 등록갯수 체크
if($mode == "works_files_check"){

	$work_idx = $_POST['work_idx'];
	$work_idx = preg_replace("/[^0-9]/", "", $work_idx);

	if($work_idx){
		$sql = "select count(1) as cnt from work_filesinfo_todaywork where state='0' and companyno='".$companyno."' and email='".$user_id."' and work_idx='".$work_idx."'";
		$info = selectQuery($sql);
		if($info['cnt']){
			$cnt = $info['cnt'];
		}else{
			$cnt = 0;
		}
		echo "complete|".$cnt;
	}
	exit;

}


//메모 열고닫기
if($mode == "btn_list_memo_onoff"){

	$onoff = $_POST['onoff'];
	$work_idx = $_POST['work_idx'];
	$onoff = preg_replace("/[^0-9]/", "", $onoff);
	$work_idx = preg_replace("/[^0-9]/", "", $work_idx);

	if($work_idx){
		$sql = "select idx from work_todaywork where state!='9' and companyno='".$companyno."' and email='".$user_id."' and idx='".$work_idx."'";
		$work_info = selectQuery($sql);
		if($work_info['idx']){
			$sql = "update work_todaywork set memo_view='".$onoff."' where idx='".$work_info['idx']."'";
			$up = updateQuery($sql);
			if($up){
				echo "complete";
				exit;
			}
		}
	}
	exit;
}


//보고내용 열고닫기
if($mode == "btn_list_report_onoff"){

	$onoff = $_POST['onoff'];
	$work_idx = $_POST['work_idx'];
	$onoff = preg_replace("/[^0-9]/", "", $onoff);
	$work_idx = preg_replace("/[^0-9]/", "", $work_idx);

	if($work_idx){
		$sql = "select idx from work_todaywork where state!='9' and companyno='".$companyno."' and email='".$user_id."' and idx='".$work_idx."'";
		$work_info = selectQuery($sql);
		if($work_info['idx']){
			$sql = "update work_todaywork set contents_view='".$onoff."' where idx='".$work_info['idx']."'";
			$up = updateQuery($sql);
			if($up){
				echo "complete";
				exit;
			}
		}
	}
	exit;
}

//오늘업무 열고닫기
if($mode == "btn_list_report_onoff"){

	$onoff = $_POST['onoff'];
	$work_idx = $_POST['work_idx'];
	$onoff = preg_replace("/[^0-9]/", "", $onoff);
	$work_idx = preg_replace("/[^0-9]/", "", $work_idx);

	if($work_idx){
		$sql = "select idx from work_todaywork where state!='9' and companyno='".$companyno."' and email='".$user_id."' and idx='".$work_idx."'";
		$work_info = selectQuery($sql);
		if($work_info['idx']){
			$sql = "update work_todaywork set contents_view='".$onoff."' where idx='".$work_info['idx']."'";
			$up = updateQuery($sql);
			if($up){
				echo "complete";
				exit;
			}
		}
	}
	exit;
}

//공유내용 열고닫기
if($mode == "btn_list_share_onoff"){

	$onoff = $_POST['onoff'];
	$work_idx = $_POST['work_idx'];
	$onoff = preg_replace("/[^0-9]/", "", $onoff);
	$work_idx = preg_replace("/[^0-9]/", "", $work_idx);

	if($work_idx){
		$sql = "select idx from work_todaywork where state!='9' and companyno='".$companyno."'  and idx='".$work_idx."'";
		$work_info = selectQuery($sql);
		if($work_info['idx']){
			$sql = "update work_todaywork set contents_view='".$onoff."' where idx='".$work_info['idx']."'";
			$up = updateQuery($sql);
			if($up){
				echo "complete";
				exit;
			}
		}
	}
	exit;
}

//업무내용 열고닫기(2023.06.28)
if($mode == "btn_list_work_onoff"){

	$onoff = $_POST['onoff'];
	$work_idx = $_POST['work_idx'];
	$onoff = preg_replace("/[^0-9]/", "", $onoff);
	$work_idx = preg_replace("/[^0-9]/", "", $work_idx);

	if($work_idx){
		$sql = "select idx from work_todaywork where state!='9' and companyno='".$companyno."'  and idx='".$work_idx."'";
		$work_info = selectQuery($sql);
		if($work_info['idx']){
			$sql = "update work_todaywork set contents_view='".$onoff."' where idx='".$work_info['idx']."'";
			$up = updateQuery($sql);
			if($up){
				echo "complete";
				exit;
			}
		}
	}
	exit;
}

if($mode == "btn_list_req_onoff"){

	$onoff = $_POST['onoff'];
	$work_idx = $_POST['work_idx'];
	$onoff = preg_replace("/[^0-9]/", "", $onoff);
	$work_idx = preg_replace("/[^0-9]/", "", $work_idx);

	if($work_idx){
		$sql = "select idx from work_todaywork where state!='9' and companyno='".$companyno."'  and idx='".$work_idx."'";
		$work_info = selectQuery($sql);
		if($work_info['idx']){
			$sql = "update work_todaywork set contents_view='".$onoff."' where idx='".$work_info['idx']."'";
			$up = updateQuery($sql);
			if($up){
				echo "complete";
				exit;
			}
		}
	}
	exit;
}


//검색
if($mode == "works_search"){

	print "<pre>";
	print_r($_POST);
	print "</pre>";

	$search_kind = $_POST['search_kind'];
	$search = $_POST['search'];
	$sdate = $_POST['sdate'];
	$edate = $_POST['edate'];


	//검색(업무종류)
	//works : 오늘업무,
	if($search_kind == "works"){

		$search_where = " and work_flag='2' and contents like '%".$search."%'";

	}

	//검색기간
	$search_where = $search_where .= " and workdate between '".$sdate."' and '".$edate."'";



	//오늘업무 리스트
	$sql = "select idx, state, work_flag, decide_flag, email, name, work_idx, repeat_flag, notice_flag, share_flag, memo_view, contents_view, title, contents, workdate, DATE_FORMAT(regdate, '%Y-%m-%d %H:%i:%s') regdate, DATE_FORMAT(regdate, '%H:%i') his from work_todaywork where state!='9'";
	$sql = $sql .=" and companyno='".$companyno."' and email='".$user_id."'".$search_where."";
	$sql = $sql .= " order by sort asc, idx desc";
	$works_info = selectAllQuery($sql);

	echo $sql;
}



//보상하기
if($mode == "coin_100c"){

	$val = $_POST['val'];
	$val = preg_replace("/[^0-9]/", "", $val);
	if($val){
		//오늘업무 조회
		$sql = "select idx, work_idx from work_todaywork where state='0' and companyno='".$companyno."' and email='".$user_id."' and idx='".$val."'";
		$work_info = selectQuery($sql);
		if($work_info['work_idx']){

			//업무 최초 작성자 체크
			$sql = "select idx, email from work_todaywork where state!='9' and companyno='".$companyno."' and idx='".$work_info['work_idx']."'";
			$info = selectQuery($sql);
			if($info['email']){
				echo "complete|".$info['email'];
				exit;
			}
		}
	}
}


//보상하기
if($mode == "coin_req_100c"){

	$val = $_POST['val'];
	$val = preg_replace("/[^0-9]/", "", $val);
	if($val){
		//오늘업무 조회 -AI 댓글조회
		$sql = "select idx, work_idx from work_todaywork_comment where state='0' and cmt_flag='1' and companyno='".$companyno."' and idx='".$val."'";
		$work_info = selectQuery($sql);
		if($work_info['work_idx']){

			//업무 최초 작성자 체크
			$sql = "select idx, email from work_todaywork where state!='9' and companyno='".$companyno."' and idx='".$work_info['work_idx']."'";
			$info = selectQuery($sql);
			if($info['email']){
				echo "complete|".$info['email'];
				exit;
			}
		}
	}
}


//파티 연결 레이어 오픈
if($mode == "party_layer_open"){

	/*
	print "<pre>";
	print_r($_POST);
	print "</pre>";
	*/

	$work_idx = $_POST['work_idx'];
	$work_idx = preg_replace("/[^0-9]/", "", $work_idx);

	$work_date = $_POST['work_date'];
	if($work_date){
		if(strpos($work_date, ".") !== false) {
			$work_date = str_replace(".", "-", $work_date);
		}
	}

	//파티 전체 갯수
	$sql = "select count(1) as cnt from work_todaywork_project as a where a.state='0' and a.companyno='".$companyno."'";
	$project_row = selectQuery($sql);
	if($project_row){
		$total_count = $project_row['cnt'];
	}

		//파티 idx
	$be_party_idx = $_POST['be_party_idx'];
	$be_party_arr = explode(",",$be_party_idx);
	$be_party_cnt = count($be_party_arr);
	$project_link_btn = false;
	
	//오늘업무 체크
	$sql = "select idx from work_todaywork where state!='9' and companyno='".$companyno."' and email='".$user_id."' and idx='".$work_idx."'";
	$work_info = selectQuery($sql);
	if($work_info['idx']){
		$sql = "select party_idx, party_link from work_todaywork_project_info where state='0' and companyno='".$companyno."' and work_idx='".$work_idx."'";
		$project_data_info = selectAllQuery($sql);
		$project_link_info = @array_combine($project_data_info['party_idx'], $project_data_info['party_link']);
		
		if(count($project_link_info)>0){
			$project_link_btn = true;
		}else{
			$project_link_btn = false;
		}
	}

		//전체 파티리스트
		$project_info = party_list();

		//전체 파티리스트 (내 파티 우선순위)
     	$project_my_info = my_party_list();

		//회사별 파티 회원리스트
		$project_user_list = member_party_user_list();

		//전체 프로젝트 내역
		/*$sql = "select b.idx, b.project_idx, b.email, b.name, b.part from work_todaywork_project as a left join work_todaywork_project_user as b on(a.idx=b.project_idx)";
		$sql = $sql .= " where a.state='0' and b.state='0' and a.companyno='".$companyno."' order by a.idx asc";
		//echo $sql;
		$project_user_info = selectAllQuery($sql);
		for($i=0; $i<count($project_user_info['idx']); $i++){
			$project_user_idx = $project_user_info['project_idx'][$i];
			$project_user_email = $project_user_info['email'][$i];
			$project_user_name = $project_user_info['name'][$i];
			$project_user_part = $project_user_info['part'][$i];
			$project_user_list[$project_user_idx]['email'][] = $project_user_email;
			$project_user_list[$project_user_idx]['name'][] = $project_user_name;
			$project_user_list[$project_user_idx]['part'][] = $project_user_part;
			$project_use[$project_user_idx][] = $project_user_email;
		}*/

		?>
			<div class="pll_box_in">
				<div class="pll_close" id="pll_close">
					<button><span>닫기</span></button>
					<input type="hidden" id="work_idx">
				</div>
				<div class="pll_top">
					<strong>파티 연결</strong>
					<span>전체 <?=number_format($total_count);?>개 <em id="partycnt"></em></span>
				</div>
				<div class="pll_search">
						<div class="pll_search_box">
							<input type="text" class="input_search" placeholder="파티명 검색" id="input_part_search_work" onkeyup="enterkey()">
							<button id="input_search_btn_work"><span>검색</span></button>
						</div>
				</div>
				<div class="live_drop_left">
					<div class="ldl_in">
				<?php

				for($i=0; $i<count($project_my_info['idx']); $i++){
						$project_wdate = "";
						$project_idx = $project_my_info['idx'][$i];
						$project_info_title = $project_my_info['title'][$i];
						$project_info_sdate = $project_my_info['sdate'][$i];

						$project_ex_date = @explode("-", $project_info_sdate);
						$project_ex_year = $project_ex_date[0];
						$project_ex_month = $project_ex_date[1];
						$project_ex_day = $project_ex_date[2];

						$project_ex_time = @explode(":", $project_info_his);
						$project_ex_hh = $project_ex_time[0];
						$project_ex_ii = $project_ex_time[1];
						$project_wdate = $project_ex_month ."/". $project_ex_day;

						$cli_on = "";

						if($be_party_arr){
							if(in_array($project_idx,$be_party_arr)){
								$cli_on = "on";
							}

						}

					?>
					<div value="<?=$project_idx?>" class="ldl_box <?=$cli_on?><?=$project_link_info[$project_idx]?" on":""?>" id="ldl_box<?=$project_idx?>"  >
						<div class="ldl_box_in">
							<div class="ldl_chk"><button id="ldl_chk" value="<?=$project_idx?>"><span>선택</span></button></div>
							<div class="ldl_box_tit">
								<p><?=$project_info_title?></p>
							</div>
							<div class="ldl_box_time"><?=$project_wdate?></div>

							<div class="ldl_box_user">
								<ul>
									<?for($j=0; $j<count($project_user_list[$project_idx]['email']); $j++){
										$user_cnt = 0;

										$project_user_list_email = $project_user_list[$project_idx]['email'][$j];
										$project_user_list_name = $project_user_list[$project_idx]['name'][$j];
										$project_user_list_part = $project_user_list[$project_idx]['part'][$j];
										$project_user_list_profile_img = profile_img_info($project_user_list_email);
										if($user_id==$project_user_list_email){
											$li_class = ' class="ldl_me"';
										}else{
											$li_class = '';
										}

										if($j>1){
											$user_more_cnt = count($project_user_list[$project_idx]['email'])-1;
											$user_more ="<div class=\"ldl_box_img cha_user_more\">+".$user_more_cnt."</div>";
											$user_cnt = 1;
										}	
										?>
										<?if($j<1){?>
											<li <?=$li_class?>>
												<div class="ldl_box_img" style="background-image:url(<?=$project_user_list_profile_img?>)" title="<?=$project_user_list_name?>"></div>
												<div class="ldl_box_user">
													<strong><?=$project_user_list_name?></strong>
													<span><?=$project_user_list_part?></span>
												</div>
											</li>
										<?}?>
									<?}?>
									<?if($user_cnt == 1){?>
										<li <?=$li_class?>>
											<?=$user_more?>
										</li>
									<?}?>
								</ul>
							</div>
						</div>
					</div>
				<?php
				}?>
				</div>
					</div>
					<div class="layer_party_btn">
						<?if($project_link_btn==true){?>
							<button class="layer_party_cancel"><span>취소</span></button>
							<button style="display:;" id="party_link_edit" class="layer_party_change"><span>변경 완료</span></button>
						<?}else{?>
							<!-- <button class="layer_party_all_slc"><span>전체선택</span></button> -->
							<button class="layer_party_cancel"><span>취소</span></button>
							<button class="layer_party_submit" id="ppl_com_btn" value = "submit"><span>연결하기</span></button>
						<?}?>
					</div>
				</div>
	<?php

	exit;
}


//파티연결해제
if($mode == "party_link_clear"){

	$work_idx = $_POST['work_idx'];
	$work_idx = preg_replace("/[^0-9]/", "", $work_idx);

	//날짜
	$work_date = $_POST['work_date'];
	if($work_date){
		if(strpos($work_date, ".") !== false) {
			$work_date = str_replace(".", "-", $work_date);
		}
	}

	//오늘업무 정보 체크
	$sql = "select idx from work_todaywork where state!='9' and companyno='".$companyno."' and email='".$user_id."' and idx='".$work_idx."' and workdate='".$work_date."'";
	$work_info = selectQuery($sql);
	if($work_info['idx']){

		$up = array();
		$sql = "select idx, party_idx, party_link from work_todaywork_project_info where state='0' and companyno='".$companyno."' and work_idx='".$work_idx."' and workdate='".$work_date."'";
		$project_data_info = selectAllQuery($sql);
		if($project_data_info['party_idx']){
			for($i=0; $i<count($project_data_info['idx']); $i++){
				$project_idx = $project_data_info['idx'][$i];
				$sql = "update work_todaywork_project_info set state='9', editdate=".DBDATE." where idx='".$project_idx."'";
				$up[] = updateQuery($sql);
			}

			//오늘업무 연결해제
			$sql = "update work_todaywork set party_link=null, party_idx=null where idx='".$work_info['idx']."'";
			$up1[] = updateQuery($sql);
		}

		if( count($up) == count($project_data_info['idx'])){
			echo "complete";
			exit;
		}else{
			echo "party_check";
			exit;
		}

	}else{

		echo "work_check";
		exit;
	}

	exit;
}



//파티연결 변경하기
if($mode == "party_link_edit"){


	$work_idx = $_POST['work_idx'];
	$work_idx = preg_replace("/[^0-9]/", "", $work_idx);

	$work_date = $_POST['work_date'];
	$party_idx = $_POST['party_idx'];
	if($work_date){
		if(strpos($work_date, ".") !== false) {
			$work_date = str_replace(".", "-", $work_date);
		}
	}

	if( is_array($_POST['party_idx'])){
		//파티갯수
		$party_cnt = count($_POST['party_idx']);
		$party_idx_arr = @implode("','",$_POST['party_idx']);
	
	}


	$sql = "select idx from work_todaywork where state!='9' and companyno='".$companyno."' and email='".$user_id."' and idx='".$work_idx."' and workdate='".$work_date."'";
	$work_info = selectQuery($sql);
	if($work_info['idx']){

		$up = array();

		//파티정보
		for($i=0; $i<$party_cnt; $i++){

			$party_link_idx = $party_idx[$i];

			//업무가 연결된 파티정보찾기
			$sql = "select idx, party_link from work_todaywork_project_info where state='0' and companyno='".$companyno."' and party_idx='".$party_link_idx."' order by idx asc limit 1";
			$project_info_data = selectQuery($sql);
			if($project_info_data['idx']){
				$party_link = $project_info_data['party_link'];
			}else{
				$party_link = party_link_create();
			}


			//파티 정보 체크
			$sql = "select idx, email, name, part, title from work_todaywork_project where state='0' and companyno='".$companyno."' and idx='".$party_link_idx."'";
			$party_ti_info = selectQuery($sql);

			//파티 참여자 회원정보
			$mem_user_info = member_row_info($user_id);
			$mem_user_no = $mem_user_info['idx'];

			if($party_ti_info['idx']){

				$party_uid = $party_ti_info['email'];
				$party_uname = $party_ti_info['name'];
				$party_part = $party_ti_info['part'];
				$party_part_flag = $party_ti_info['part_flag'];
				$party_title = $party_ti_info['title'];
				
				//파티생성자 회원정보
				$member_info = member_row_info($party_uid);
				$partno = $member_info['partno'];

				//파티 공통 데이터 체크
				$sql = "select idx from work_todaywork_project_info where state='0' and companyno='".$companyno."' and work_idx='".$work_info['idx']."' and party_link='".$party_link."' ";
				$party_info = selectQuery($sql);
				if(!$party_info['idx']){
					//시분초 + 업무번호
					//업무와 파티연결키생성
					//파티 통합정보 저장
					$sql = "insert into work_todaywork_project_info(party_idx, party_link, party_uid, party_uname, party_upart, party_upartno, companyno, party_title, mem_no, mem_email, mem_name, mem_part, work_idx, workdate, ip)";
					$sql = $sql .= " values('".$party_link_idx."','".$party_link."', '".$party_uid."','".$party_uname."','".$party_part."','".$partno."','".$companyno."','".$party_title."','".$mem_user_no."','".$user_id."','".$user_name."','".$user_part."','".$work_idx."','".$work_date."','".LIP."')";
					$party_info_insert_idx = insertIdxQuery($sql);
					if($party_info_insert_idx){
						$sql = "update work_todaywork set party_link='".$party_link."' where idx='".$work_info['idx']."'";
						$up = updateQuery($sql);
					}
				}
			}
		}


		//삭제되는 정보 조회
		$sql = "select idx from work_todaywork_project_info where state='0' and companyno='".$companyno."' and work_idx='".$work_info['idx']."' and party_idx not in('".$party_idx_arr."')";
		$work_party_info = selectAllQuery($sql);
		//파티정보
		for($i=0; $i<count($work_party_info['idx']); $i++){
			$sql = "update work_todaywork_project_info set state='9', editdate=".DBDATE." where idx='".$work_party_info['idx'][$i]."'";
			$up = updateQuery($sql);
		}

		//해당 업무가 파티로 연결된 최종 갯수조회
		$sql = "select count(1) cnt from work_todaywork_project_info where state='0' and companyno='".$companyno."' and work_idx='".$work_idx."' and party_idx in('".$party_idx_arr."')";
		$pj_info = selectQuery($sql);

		if($pj_info['cnt'] == $party_cnt){
			echo "complete";
			exit;
		}else{
			echo "party_check";
			exit;
		}

	}else{

		echo "work_check";
		exit;
	}

	exit;

}


//파티연결(오늘업무와 파티를 연결합니다.)
if($mode == "party_add"){

	/*
	print "<pre>";
	print_r($_POST);
	print "</pre>";
	*/
	
	//업무번호
	$work_idx = $_POST['work_idx'];
	$work_idx = preg_replace("/[^0-9]/", "", $work_idx);

	//파티갯수
	$party_cnt = count($_POST['party_idx']);

	//기존에 있었던 파티 갯수
	$defalut_cnt = $_POST['defalut_cnt'];
	//파티갯수가 없을때
	if(!$party_cnt && $defalut_cnt == 0){
		echo "|party_not";
		exit;
	}else if(!$party_cnt && $defalut_cnt != 0){
		echo "|party_zero";
		exit;
	}

	//업무일자
	$work_date = $_POST['work_date'];

	if($work_date){
		if(strpos($work_date, ".") !== false) {
			$work_date = str_replace(".", "-", $work_date);
		}
	}


	//오늘업무 체크
	$sql = "select idx from work_todaywork where state!='9' and companyno='".$companyno."' and idx='".$work_idx."' and email='".$user_id."'";
	$work_info = selectQuery($sql);

	

	if($work_info['idx']){

		$complete_cnt = "0";
		//선택한 파티별로 데이터 삽입
		for($i=0; $i<$party_cnt; $i++){
			$party_idx = $_POST['party_idx'][$i];
			$party_idx = preg_replace("/[^0-9]/", "", $party_idx);
			
			//파티 정보 체크
			$sql = "select idx, email, name, part, title from work_todaywork_project where state='0' and companyno='".$companyno."' and idx='".$party_idx."'";
			$party_ti_info = selectQuery($sql);

			//파티 참여자 회원정보
			$mem_user_info = member_row_info($user_id);
			$mem_user_no = $mem_user_info['idx'];

			if($party_ti_info['idx']){

				$party_uid = $party_ti_info['email'];
				$party_uname = $party_ti_info['name'];
				$party_part = $party_ti_info['part'];
				$party_part_flag = $party_ti_info['part_flag'];
				$party_title = $party_ti_info['title'];

				//파티생성자 회원정보
				$member_info = member_row_info($party_uid);
				$partno = $member_info['partno'];

				//파티 연결된 최초 정보
				$sql = "select idx, party_link from work_todaywork_project_info where state='0' and companyno='".$companyno."' and party_idx='".$party_idx."' order by idx asc limit 1";
				$party_pj_info = selectQuery($sql);
				if($party_pj_info['idx']){
					$party_link = $party_pj_info['party_link'];
				}else{
					$party_link = party_link_create();
				}

				
				$sql = "select idx from work_todaywork_project_user where state = 0 and companyno = '".$companyno."' and project_idx = '".$party_idx."' and party_read_flag = 0";
				$party_read = selectAllQuery($sql);

				if($party_read){
					for($i=0; $i<count($party_read['idx']); $i++){
						$party_read_idx = $party_read['idx'][$i];
					//파티 미확인 업데이트
					$sql = "update work_todaywork_project_user set party_read_flag = 1, party_read_date = ".DBDATE." where idx = '".$party_read_idx."'";
					$read = updateQuery($sql);
					}
				}
				

				//파티 공통 데이터 체크
				$sql = "select idx from work_todaywork_project_info where state='0' and companyno='".$companyno."' and party_idx='".$party_idx."' and party_link='".$party_link."' and work_idx='".$work_info['idx']."'";
				$party_info = selectQuery($sql);
				if(!$party_info['idx']){
					//시분초 + 업무번호
					//업무와 파티연결키생성
					//파티 통합정보 저장
					$sql = "insert into work_todaywork_project_info(party_idx, party_link, party_uid, party_uname, party_upart, party_upartno, companyno, party_title, mem_no, mem_email, mem_name, mem_part, work_idx, workdate, ip)";
					$sql = $sql .= " values('".$party_idx."','".$party_link."', '".$party_uid."','".$party_uname."','".$party_part."','".$partno."','".$companyno."','".$party_title."','".$mem_user_no."','".$user_id."','".$user_name."','".$user_part."','".$work_idx."','".$work_date."','".LIP."')";
					$party_info_insert_idx = insertIdxQuery($sql);
					if($party_info_insert_idx){

						//파티 연결후 업데이트 시간 갱신
						$sql = "update work_todaywork_project set editdate=".DBDATE." where idx='".$party_ti_info['idx']."'";
						$up = updateQuery($sql);
						$complete_cnt++;
					}
				}else{
					//데이터가 있을때 카운터처리함
					$complete_cnt++;
				}
			}
		}

		$sql = "update work_todaywork set party_link='".$party_link."', party_idx='".$party_idx."' where idx='".$work_info['idx']."'";
		$up = updateQuery($sql);

		if( $party_cnt == $complete_cnt){
			echo "|complete";
		}
	}else{
		echo "|be_works_party";
	}
}

if($mode == "profile_character"){

	$profile_no = $_POST['profile_no'];
	$profile_no = preg_replace("/[^0-9]/", "", $profile_no);

	if($profile_no){

		$sql = "select idx from work_member where state='0' and email='".$user_id."'";
		$mem_info = selectQuery($sql);
		if($mem_info['idx']){
			$sql = "update work_member set profile_type='0', profile_img_idx='".$profile_no."' where idx='".$mem_info['idx']."'";
			$up = updateQuery($sql);
			if($up){

				//아이템추가로 인한 쿼리
				$sql = "select file_path, file_name from work_member_character_img where idx = '".$profile_no."'";
				$item_file_f_path = selectQuery($sql);

				if($item_file_f_path){
					$item_file_path = $item_file_f_path['file_path'];
					$item_file_name = $item_file_f_path['file_name'];

					$item_file_full_path = $item_file_path.$item_file_name;
				}

				$sql = "select idx from work_member_profile_img where state='0' and email='".$user_id."'";
				$profile_info = selectQuery($sql);
				if($profile_info['idx']){

					$sql = "update work_member_profile_img set file_path='".$item_file_path."', file_name='".$item_file_name."', editdate=".DBDATE." where state='0' and idx='".$profile_info['idx']."'";
					$up = updateQuery($sql);
					if($up){
						$img = $item_file_full_path;
						echo "complete|".$img;
						exit;
					}
				}else{

					$sql = "insert into work_member_profile_img(email, file_path, file_name, ip ) values('".$user_id."', '".$item_file_path."','".$item_file_name."','".LIP."')"; 
					$res_idx = insertQuery($sql);
					if($res_idx){
						$img = $item_file_full_path;
						echo "complete|".$img;
					}
				}
			}
		}
	}
	exit;
}

if($mode == "my_alert_close"){

	//회원정보
	$member_row_info = member_row_info($user_id);

	$idx = $_POST['val'];
	$idx = preg_replace("/[^0-9]/", "", $idx);

	if($idx){
		$sql = "select idx from work_alarm where state='0' and companyno='".$companyno."' and idx='".$idx."'";
		$like_info = selectQuery($sql);
		if($like_info['idx']){
			$sql = "update work_alarm set state='9' where email = '".$_COOKIE['user_id']."' and  idx='".$like_info['idx']."'";
			$up = updateQuery($sql);
			if($up){
				echo "complete|";


				$sql = "select email, count(1) as cnt 
					from work_alarm
					where 1=1
					and state = '0'
					and work_flag = '0'
					and email = '".$member_row_info['email']."'
					and workdate = '".TODATE."'";

					$alarm_count = selectQuery($sql);
				$sql = "select email, count(1) as cnt 
					from work_alarm
					where 1=1
					and state = '0'
					and email = '".$member_row_info['email']."'
					and workdate = '".TODATE."'";

					$alarm_count2 = selectQuery($sql);
					?>
					<button class="btn_my_alert"><span>알림</span>
					<?php if($alarm_count['cnt'] < 0){?>
						<em><?php echo $alarm_count['cnt'] ?></em>
					<?php }?>
					</button>
				<?php echo "|";
				
				$sql = "select idx, state, service, title, contents, email, workdate, regdate
						from work_alarm
						where 1=1
						and state = '0'
						and email = '".$member_row_info['email']."'
						and workdate = '".TODATE."'
						order by idx desc";
				$alarm_info = selectAllQuery($sql);
				if($alarm_count2['cnt'] > 0){
					for($i=0; $i<count($alarm_info['idx']); $i++){
						$alarm_reg = $alarm_info['reg'][$i];
						if($alarm_reg){
						$his_tmp = @explode(" ", $alarm_reg);
						if ($his_tmp['2'] == "PM"){ 
							$after = "오후 ";
						}else{
							$after = "오전 ";
						}
						$ctime = @explode(":", $his_tmp['1']);
						$work_his = $alarm_info['workdate'][$i] . " " . $after . $ctime['0'] .":". $ctime['1'];
						}
						?>
						<li>
							<button class="my_alert_box">
								<div class="my_alert_box_tit">
									<?php if($alarm_info['service'][$i] == 'work'){?>
										<img src="/html/images/pre_m/arrow_icon.png" alt="" />
										<span onclick="window.open('https://rewardy.co.kr/todaywork/index.php')"><strong><?php echo $alarm_info['title'][$i]?></strong></span>
									<?php }else if($alarm_info['service'][$i] == 'live'){?>
										<img src="/html/images/pre/ico_ht.png" alt="" />
										<span onclick="window.open('https://rewardy.co.kr/team/index.php')"><strong><?php echo $alarm_info['title'][$i]?></strong></span>
									<?php }else if($alarm_info['service'][$i] == 'reward'){?>
										<img src="/html/images/pre/ico_coin_new.png" alt="" />
										<span onclick="window.open('https://rewardy.co.kr/reward/index.php')"><strong><?php echo $alarm_info['title'][$i]?></strong></span>
									<?php }else if($alarm_info['service'][$i] == 'challenge'){?>
										<img src="/html/images/pre_m/ico_bell.png" alt="" />
										<span onclick="window.open('https://rewardy.co.kr/challenge/view.php?idx=<?php echo $alarm_info['work_idx'][$i]?>')"><strong><?php echo $alarm_info['title'][$i]?></strong></span>
									<?php }else if($alarm_info['service'][$i] == 'party'){?>
										<img src="/html/images/pre_m/ico_bell.png" alt="" />
										<span onclick="window.open('https://rewardy.co.kr/party/view.php?idx=<?php echo $alarm_info['work_idx'][$i]?>')"><strong><?php echo $alarm_info['title'][$i]?></strong></span>
									<?php } ?>
								</div>
								<div class="my_alert_box_desc">
									<span><?php echo $alarm_info['contents'][$i]?></span>
								</div>
								<div class="my_alert_box_info">
									<span><?php echo $alarm_info['regdate'][$i]?></span>
									<!-- <span><strong>300</strong> 코인</span> -->
								</div>
							</button>
							<button class="my_alert_close" id = "my_alert_close" value= "<?php echo $alarm_info['idx'][$i]?>"><span>닫기</span></button>
						</li>
					<?php } 
					}else{?>
						<button class="my_alert_box">
									<div class="my_alert_box_tit">
										<span><strong>알림이 없습니다.</strong></span>
									</div>
									<div class="my_alert_box_desc">
									</div>
									<div class="my_alert_box_info">
									</div>
								</button>
						</li>
					<?php }
				exit;
			}
		}
	}
	exit;
}

if($mode == "my_alert_all"){

	//회원정보
	$member_row_info = member_row_info($user_id);

	$idx = $_POST['val'];
	$idx = preg_replace("/[^0-9]/", "", $idx);
	
	if($idx){

		$sql = "select idx from work_alarm where state='0' and work_flag='0' and companyno='".$companyno."' and email = '".$_COOKIE['user_id']."' and workdate = '".TODATE."'";
		$like_info = selectAllQuery($sql);

		if($like_info['idx']){
			$sql = "update work_alarm set work_flag='1' where email = '".$_COOKIE['user_id']."' and workdate = '".TODATE."'";
			$up = updateQuery($sql);
			if($up){
				echo "complete|";

				$sql = "select email, count(1) as cnt 
					from work_alarm
					where 1=1
					and state = '0'
					and work_flag = '0'
					and email = '".$member_row_info['email']."'
					and workdate = '".TODATE."'";

					$alarm_count = selectQuery($sql);
					?>
					<button class="btn_my_alert"><span>알림</span>
						<?php if($alarm_count['cnt'] < 0){?>
							<em><?php echo $alarm_count['cnt'] ?></em>
						<?php }?>
					</button>
					<?php echo "|";
			}
		}
	}
	exit;
}

if($mode == "message_all"){

	//회원정보
	$member_row_info = member_row_info($user_id);

	$idx = $_POST['val'];
	// $idx = preg_replace("/[^0-9]/", "", $idx);
	
	if($idx){

		$sql = "update work_member_message set state='1' where email = '".$user_id."'";
		$up = updateQuery($sql);
		if($up){
			echo "complete|";
				?>
				<button class="btn_my_mess"><span>쪽지</span></button>
				<?php echo "|";
		}
	}
	exit;
}

// 매일 반복
if($mode == 'calendar_day'){
	
	$work_cal_idx = $_POST['work_idx'];
	$cal_close_date = $_POST['close_date'];
	$checked_type = $_POST['checked_type'];
	$repeat_frequency = $_POST['repeat_frequency'];
	$work_wdate = $_POST['work_wdate'];
	$repeat_interval = $_POST['interval'];
	$repeat_type = $_POST['repeat_type'];
	$noWeek = $_POST['noweek'];
	$cal_cancel_date = $_POST['cancel_date'];

	$replace_wdate = str_replace(".", "-", $work_wdate);
	$end_date = date("Y-m-d", strtotime("+".repeatday." day", TODAYTIME));
	
	$sql = "select idx, workdate from work_todaywork where 1=1 and companyno = '".$companyno."' and idx = '".$work_cal_idx."'";
	$repeat_query = selectQuery($sql);
	
	$sql = "select work_idx, start_date, repeat_frequency, repeat_type, close_date from calendar_events where work_idx = '".$work_cal_idx."' and state = '0' order by idx asc limit 1";
	$close_select = selectQuery($sql);
	$close_select_date = date('Y-m-d', strtotime($close_select['close_date']));
	$close_date = date('Y-m-d', strtotime($cal_close_date));
	
	if($close_select['repeat_frequency'] == $repeat_frequency){
		// 수정하려는 종료일이 기존의 종료일보다 길 경우
		$state = '';
		if ($close_date > $close_select_date) {
			$state = "'0'";
			$close = "close_date";
		// 수정하려는 종료일이 기존의 종료일보다 짧은 경우
		} elseif ($close_date < $close_select_date) {
			$state = "STATE"; // 공백 문자열
			$close = "null";
		} else {
			$state = "STATE"; // $STATE 변수의 값을 따옴표로 묶어 사용
			$close = "close_date";
		}
		if($close_select['repeat_type'] != $repeat_type){
			$sql = "UPDATE calendar_events
				SET state = 
				CASE 
					WHEN start_date >= '".$replace_wdate."' THEN '9'
					ELSE '1'
				END
				WHERE work_idx = '".$work_cal_idx."'";
			$change_repeat = updateQuery($sql);
		}
	}else if($close_select['repeat_frequency'] != $repeat_frequency){
		$sql = "UPDATE calendar_events
				SET state = 
				CASE 
					WHEN start_date >= '".$replace_wdate."' THEN '9'
					ELSE '1'
				END
				WHERE work_idx = '".$work_cal_idx."'";
		$change_repeat = updateQuery($sql);
	}


	if($repeat_query){
		if($cal_cancel_date){

			$sql = "update calendar_events set state = '9' where state = '0' and work_idx = '".$work_cal_idx."' and start_date > '".$replace_wdate."'";
			$repeat_del_update = updateQuery($sql);
			echo "complete";
		}else{
			$sql = "update work_todaywork set repeat_flag = '1' where companyno = '".$companyno."' and idx = '".$work_cal_idx."'";
			$repeat_update = updateQuery($sql);
			//시작일자
			$start_date = new DateTime($replace_wdate);
			//종료일자
			$end_date = new DateTime($end_date);
			//시작일자와 종료일자간의 기간차이
			// 설정 시 close_date가 있는 경우
			if($cal_close_date != 'null'){	
				$cal_close_date =date('Y-m-d', strtotime($cal_close_date));
				$diff_close = new DateTime($cal_close_date);
				$diff_days = date_diff($start_date, $diff_close);
				$diffdays = $diff_days->days + 1;

				if($close_select){
					$sql = "UPDATE calendar_events
					SET close_date = CASE
						WHEN start_date <= '".$close_date."' THEN '".$close_date."'
						ELSE $close
					END,
					state = CASE
						WHEN start_date > '".$close_date."' THEN '9'
						ELSE $state
					END
					WHERE work_idx = '".$work_cal_idx."' and repeat_frequency = '".$repeat_frequency."'";
						
					$close_update = updateQuery($sql);
					
					echo "close";
					echo exit();
				}
				
			}else{
				$diff_days = date_diff($start_date, $end_date);
				$diffdays = $diff_days->days;
				$cal_close_date = null;
			}
			// 매일반복 등록
			
			$insertQuery = "INSERT INTO calendar_events(work_idx, state, repeat_frequency, repeat_interval, repeat_type, start_date, close_date) 
							VALUES (?, ?, ?, ?, ?, ?, ?)";

			$insertStmt = $conn->prepare($insertQuery);

			$day_date = strtotime($replace_wdate);

				//주말 제외
				if($noWeek){
					for($i=0; $i< $diffdays; $i++){
						$create_date = date('Y-m-d',strtotime('+'.$i.' day', $day_date));

						$yoil_day = date('w', strtotime($create_date));				
						//토,일 제외
							if (!in_array($yoil_day , array('0','6'))){

							$insertData = array(
								$work_cal_idx, '0', $repeat_frequency, $repeat_interval, $repeat_type,
								$create_date, $cal_close_date
							);
							
							$insertStmt->bind_param("issssss", ...$insertData); 
							$insertStmt->execute();

						}
					}

				// 일수 간격(주말은 제외)
				}else if($repeat_type == '3'){
					for($i=0; $i< $diffdays; $i += $repeat_interval){
						$create_date = date('Y-m-d',strtotime('+'.$i.' day', $day_date));

						$yoil_day = date('w', strtotime($create_date));		

						if (!in_array($yoil_day , array('0','6'))){
						$insertData = array(
							$work_cal_idx, '0', $repeat_frequency, $repeat_interval, $repeat_type,
							$create_date, $cal_close_date 
						);

						$insertStmt->bind_param("issssss", ...$insertData); 
						$insertStmt->execute();
						}
					}
				// 일반 매일 반복
				}else{
					for($i=0; $i< $diffdays; $i++){
						$create_date = date('Y-m-d',strtotime('+'.$i.' day', $day_date));

						$insertData = array(
							$work_cal_idx, '0', $repeat_frequency, $repeat_interval, $repeat_type,
							$create_date, $cal_close_date 
						);

						$insertStmt->bind_param("issssss", ...$insertData); 
						$insertStmt->execute();
					}
				}
				echo "complete";
		}
	}

	
// 주말 반복
}else if($mode == 'calendar_week'){

	$calendar_week_day = $_POST['week_day'];
	$work_cal_idx = $_POST['work_idx'];
	$cal_close_date = $_POST['close_date'];
	$work_wdate = $_POST['work_wdate'];
	$cal_cancel_date = $_POST['cancel_date'];
	$week_interval = $_POST['week_count'];
	$repeat_frequency = $_POST['repeat_frequency'];

	$replace_wdate = str_replace(".", "-", $work_wdate);
	$end_date = date("Y-m-d", strtotime("+".repeatday." day", TODAYTIME));

	$weekArray = explode(',', $calendar_week_day);


	$sql = "select idx, workdate from work_todaywork where 1=1 and companyno = '".$companyno."' and idx = '".$work_cal_idx."'";
	
	$repeat_query = selectQuery($sql);

	$sql = "select work_idx, start_date, repeat_frequency, repeat_type, close_date from calendar_events where work_idx = '".$work_cal_idx."' and state = '0' order by idx asc limit 1";
	$close_select = selectQuery($sql);
	$close_select_date = date('Y-m-d', strtotime($close_select['close_date']));
	$close_date = date('Y-m-d', strtotime($cal_close_date));
	
	if($close_select['repeat_frequency'] == $repeat_frequency){
	
		// 수정하려는 종료일이 기존의 종료일보다 길 경우]
		$state = '';
		if ($close_date > $close_select_date) {
			$state = "'0'";
			$close = "close_date";
		// 수정하려는 종료일이 기존의 종료일보다 짧은 경우
		} elseif ($close_date < $close_select_date) {
			$state = "STATE"; // 공백 문자열
			$close = "null";
		} else {
			$state = "STATE"; // $STATE 변수의 값을 따옴표로 묶어 사용
			$close = "close_date";
		}
		if($cal_cancel_date){
			$sql = "update calendar_events set state = '9' where state = '0' and work_idx = '".$work_cal_idx."' and start_date > '".$replace_wdate."'";
			$repeat_del_update = updateQuery($sql);

			echo "complete";
		}else if($close_select['repeat_type'] != $repeat_type){
			$sql = "UPDATE calendar_events
			SET state = 
			CASE 
				WHEN start_date >= '".$replace_wdate."' THEN '9'
				ELSE '1'
			END
			WHERE work_idx = '".$work_cal_idx."'";
			$change_repeat = updateQuery($sql);
		}
	}else if($close_select['repeat_frequency'] != $repeat_frequency){
		$sql = "UPDATE calendar_events
				SET state = 
				CASE 
					WHEN start_date >= '".$replace_wdate."' THEN '9'
					ELSE '1'
				END
				WHERE work_idx = '".$work_cal_idx."'";
		$change_repeat = updateQuery($sql);
	}

	if($repeat_query){

			$sql = "update work_todaywork set repeat_flag = '1' where companyno = '".$companyno."' and idx = '".$work_cal_idx."'";
			$repeat_update = updateQuery($sql);

			$start_date = new DateTime($replace_wdate);
			$end_date = new DateTime($end_date);

			if($cal_close_date != 'null'){
				$cal_close_date =date('Y-m-d', strtotime($cal_close_date));
				$diff_close = new DateTime($cal_close_date);
				$diff_days = date_diff($start_date, $diff_close);
				$diffdays = $diff_days->days + 1;
				if($close_select){
					$sql = "UPDATE calendar_events
					SET close_date = CASE
						WHEN start_date <= '".$close_date."' THEN '".$close_date."'
						ELSE $close
					END,
					state = CASE
						WHEN start_date > '".$close_date."' THEN '9'
						ELSE $state
					END
					WHERE work_idx = '".$work_cal_idx."'";
						
					$close_update = updateQuery($sql);
					
					echo "close";
					echo exit();
				}
			}else{
				$diff_days = date_diff($start_date, $end_date);
				$diffdays = $diff_days->days;
				$cal_close_date = null;
			}

			$insertQuery = "INSERT INTO calendar_events(work_idx, state, repeat_frequency, repeat_interval, repeat_type, start_date, close_date) 
							VALUES (?, ?, ?, ?, ?, ?, ?)";
			$insertStmt = $conn->prepare($insertQuery);
			
			$week_date = strtotime($replace_wdate);
			
				if(!$week_interval){
					$week_interval = 1;
				}
				$first_date = null;

				for ($i = 0; $i < $diffdays; $i++) {
					$create_date = date('Y-m-d', strtotime('+' . $i . ' day', $week_date));
					$yoil_day = date('w', strtotime($create_date));
				
					if ($i > 0 && $yoil_day < date('w', $week_date)) {
						$create_date = date('Y-m-d', strtotime('+' . $i . ' day', $week_date));
					}

					if (in_array($yoil_day, $weekArray)) {
						if ($first_date === null || $create_date < $first_date) {
							$first_date = $create_date;
						}
				
						$date_diff = date_diff(new DateTime($first_date), new DateTime($create_date))->days;
				
						// 다음주 처리가 문제일 것으로 예상
						if ($date_diff % (7 * $week_interval) < 7) {
							$insertData = array(
								$work_cal_idx, '0', $repeat_frequency, $week_interval, 1,
								$create_date, $cal_close_date
							);
				
							// 여기서 데이터베이스에 데이터를 삽입하는 코드를 추가하면 됩니다.
							$insertStmt->bind_param("issssss", ...$insertData); 
							$insertStmt->execute();
				
							// var_dump($create_date); // 데이터가 정상적으로 생성되는지 확인하기 위해 변수를 출력합니다.
						}
					}
				}
			echo "complete";
	}

}else if($mode == 'calendar_month'){
	$work_cal_idx = $_POST['work_idx'];
	$cal_close_date = $_POST['close_date'];
	$repeat_frequency = $_POST['repeat_frequency'];
	$work_wdate = $_POST['work_wdate'];
	$repeat_type = $_POST['repeat_type'];
	$cal_cancel_date = $_POST['cancel_date'];
	
	$replace_wdate = str_replace(".", "-", $work_wdate);
	$end_date = date("Y-m-d", strtotime("+".repeatday." day", TODAYTIME));

	$sql = "select idx, workdate from work_todaywork where 1=1 and companyno = '".$companyno."' and idx = '".$work_cal_idx."'";
	
	$repeat_query = selectQuery($sql);

	$sql = "select work_idx, start_date, repeat_frequency, repeat_type, close_date from calendar_events where work_idx = '".$work_cal_idx."' and state = '0' order by idx asc limit 1";
	$close_select = selectQuery($sql);
	$close_select_date = date('Y-m-d', strtotime($close_select['close_date']));
	$close_date = date('Y-m-d', strtotime($cal_close_date));
	
	if($close_select['repeat_frequency'] == $repeat_frequency){
		// 수정하려는 종료일이 기존의 종료일보다 길 경우
		$state = '';
		if ($close_date > $close_select_date) {
			$state = "'0'";
			$close = "close_date";
		// 수정하려는 종료일이 기존의 종료일보다 짧은 경우
		} elseif ($close_date < $close_select_date) {
			$state = "STATE"; // 공백 문자열
			$close = "null";
		} else {
			$state = "STATE"; // $STATE 변수의 값을 따옴표로 묶어 사용
			$close = "close_date";
		}
		if($close_select['repeat_type'] != $repeat_type){
			
			
			$sql = "UPDATE calendar_events
				SET state = 
				CASE 
					WHEN start_date >= '".$replace_wdate."' THEN '9'
					ELSE '1'
				END
				WHERE work_idx = '".$work_cal_idx."'";
			$change_repeat = updateQuery($sql);
		}
	}else if($close_select['repeat_frequency'] != $repeat_frequency){
		$sql = "UPDATE calendar_events
				SET state = 
				CASE 
					WHEN start_date >= '".$replace_wdate."' THEN '9'
					ELSE '1'
				END
				WHERE work_idx = '".$work_cal_idx."'";
		$change_repeat = updateQuery($sql);
	}
	
	if($repeat_query){
		if($cal_cancel_date){
			// $sql = "update work_todaywork set repeat_flag = '0' where companyno = '".$companyno."' and idx = '".$work_cal_idx."'";
			// $repeat_work_update = updateQuery($sql);
			$sql = "update calendar_events set state = '9' where state = '0' and work_idx = '".$work_cal_idx."' and start_date > '".$replace_wdate."'";
			$repeat_del_update = updateQuery($sql);

			echo "complete";
		}else{
			$sql = "update work_todaywork set repeat_flag = '1' where companyno = '".$companyno."' and idx = '".$work_cal_idx."'";
			$repeat_update = updateQuery($sql);
			//시작일자
			$start_date = new DateTime($replace_wdate);
			//종료일자
			$end_date = new DateTime($end_date);
			//시작일자와 종료일자간의 기간차이
			// 설정 시 close_date가 있는 경우
			// 4번쨰 enddate 구하는 수식
			$end_date_week = date("Y-m-d", strtotime("+".repeatday." day", TODAYTIME));
			$repeat_interval = 0;
			if($cal_close_date != 'null'){
				$cal_close_date =date('Y-m-d', strtotime($cal_close_date));
				$diff_close = new DateTime($cal_close_date);
				$diff_days = date_diff($start_date, $diff_close);
				$diffdays = $diff_days->days + 1;

				if($close_select){
					$sql = "UPDATE calendar_events
					SET close_date = CASE
						WHEN start_date <= '".$close_date."' THEN '".$close_date."'
						ELSE $close
					END,
					state = CASE
						WHEN start_date > '".$close_date."' THEN '9'
						ELSE $state
					END
					WHERE work_idx = '".$work_cal_idx."' and repeat_frequency = '".$repeat_frequency."'";
					$close_update = updateQuery($sql);
				}
			}else{
				$diff_days = date_diff($start_date, $end_date);
				$diffdays = $diff_days->days;
				$diffMonth = $diffdays / 
				$cal_close_date = null;
			}
			// 매일반복 등록
			$insertQuery = "INSERT INTO calendar_events(work_idx, state, repeat_frequency, repeat_interval, repeat_type, start_date, close_date) 
							VALUES (?, ?, ?, ?, ?, ?, ?)";
			$insertStmt = $conn->prepare($insertQuery);

				if($repeat_type == '1'){
				// 시작 날짜 설정 (현재 날짜를 기준으로)
				$startDate = $start_date;
				$endDate = $end_date;
					while ($startDate <= $endDate) {
						$insertData = array(
							$work_cal_idx,
							'0',
							$repeat_frequency,
							$repeat_interval,
							$repeat_type,
							$startDate->format('Y-m-d'),
							$cal_close_date
						);
						$insertStmt->bind_param("issssss", ...$insertData); 
						$insertStmt->execute();
						// 다음 월로 이동
						if ($startDate->format('d') == '31') {
							$startDate->add(new DateInterval('P1M'));
							$startDate->modify('last day of this month');
						} else if ($startDate->format('d') == '30') {
							// 2월인 경우 제외
							if ($startDate->format('m') != '02') {
								$startDate->add(new DateInterval('P1M'));
								$startDate->setDate($startDate->format('Y'), $startDate->format('m'), 30);
							} else {
								$startDate->add(new DateInterval('P1M'));
							}
						} else {
							$startDate->add(new DateInterval('P1M'));
						}
					}
				}else if($repeat_type == '2'){
					$currentYear = date('Y');
					$startDate = strtotime($replace_wdate);
					$endDate = strtotime($end_date_week);

					for ($currentDate = $startDate; $currentDate < $endDate; $currentDate = strtotime('+1 month', $currentDate)) {
						$year = date('Y', $currentDate);
						$month = date('n', $currentDate);
						
						$date = new DateTime();
						$date->setDate($year, $month, 1);

						$week = 4; // 4번째 주 
						$weekday = 5; // 금요일 숫자로 계산
						
						while ($date->format('N') != $weekday) {
							$date->modify('+1 day');
						}
						
						for ($i = 1; $i < $week; $i++) {
							$date->modify('+7 days');
						}
						
						$create_date = $date->format('Y-m-d');
						
						$insertData = array(
							$work_cal_idx, '0', $repeat_frequency, $repeat_interval, $repeat_type,
							$create_date, $cal_close_date
						);

						$insertStmt->bind_param("issssss", ...$insertData); 
						$insertStmt->execute();
					}
				}else if($repeat_type == '3'){	
					$currentYear = date('Y');
					$startDate = strtotime($replace_wdate);
					$endDate = strtotime($end_date_week);
					// 매월 마지막주 금요일
					for ($currentDate = $startDate; $currentDate <= $endDate; $currentDate = strtotime('+1 month', $currentDate)) {
						$year = date('Y', $currentDate);
						$month = date('n', $currentDate);
						
						$lastDayOfMonth = date('t', $currentDate);
						
						$lastDay = new DateTime();
						$lastDay->setDate($year, $month, $lastDayOfMonth);
						
						while ($lastDay->format('N') != 5) { 
							$lastDay->modify('-1 day');
						}
						
						$create_date = $lastDay->format('Y-m-d');
						
						$insertData = array(
							$work_cal_idx, '0', $repeat_frequency, $repeat_interval, $repeat_type,
							$create_date, $cal_close_date
						);
						$insertStmt->bind_param("issssss", ...$insertData); 
						$insertStmt->execute();
					}
				}
			echo "complete";
		}
	}
}else if($mode == 'calendar_year'){
	
	$work_cal_idx = $_POST['work_idx'];
	$cal_close_date = $_POST['close_date'];
	$repeat_frequency = $_POST['repeat_frequency'];
	$work_wdate = $_POST['work_wdate'];
	$repeat_type = $_POST['repeat_type'];
	$cal_cancel_date = $_POST['cancel_date'];

	
	$replace_wdate = str_replace(".", "-", $work_wdate);
	$end_date = date("Y-m-d", strtotime("+".repeatday." day", TODAYTIME));

	$sql = "select idx, workdate from work_todaywork where 1=1 and companyno = '".$companyno."' and idx = '".$work_cal_idx."'";
	
	$repeat_query = selectQuery($sql);

	$sql = "select work_idx, start_date, repeat_frequency, close_date from calendar_events where work_idx = '".$work_cal_idx."' and state = '0' order by idx asc limit 1";
	$close_select = selectQuery($sql);
	$close_select_date = date('Y-m-d', strtotime($close_select['close_date']));
	$close_date = date('Y-m-d', strtotime($cal_close_date));
	
	if($close_select['repeat_frequency'] == $repeat_frequency){
		// 수정하려는 종료일이 기존의 종료일보다 길 경우
		$state = '';
		if ($close_date > $close_select_date) {
			$state = "'0'";
			$close = "close_date";
		// 수정하려는 종료일이 기존의 종료일보다 짧은 경우
		} elseif ($close_date < $close_select_date) {
			$state = "STATE"; // 공백 문자열
			$close = "null";
		} else {
			$state = "STATE"; // $STATE 변수의 값을 따옴표로 묶어 사용
			$close = "close_date";
		}

	}else if($close_select['repeat_frequency'] != $repeat_frequency){
		$sql = "UPDATE calendar_events
				SET state = 
				CASE 
					WHEN start_date >= '".$replace_wdate."' THEN '9'
					ELSE '1'
				END
				WHERE work_idx = '".$work_cal_idx."'";
		$change_repeat = updateQuery($sql);
	}
	
	if($repeat_query){
		if($cal_cancel_date){
			// $sql = "update work_todaywork set repeat_flag = '0' where companyno = '".$companyno."' and idx = '".$work_cal_idx."'";
			// $repeat_work_update = updateQuery($sql);

			$sql = "update calendar_events set state = '9' where state = '0' and work_idx = '".$work_cal_idx."' and start_date > '".$replace_wdate."'";
			$repeat_del_update = updateQuery($sql);

			echo "complete";
		}else{

			$sql = "update work_todaywork set repeat_flag = '1' where companyno = '".$companyno."' and idx = '".$work_cal_idx."'";
			$repeat_update = updateQuery($sql);
			//시작일자
			$start_date = new DateTime($replace_wdate);
			//종료일자
			$end_date = new DateTime($end_date);
			//시작일자와 종료일자간의 기간차이
			// 설정 시 close_date가 있는 경우

			$year_interval = 11;
			if($cal_close_date != 'null'){
				$cal_close_date =date('Y-m-d', strtotime($cal_close_date));
				$diff_close = new DateTime($cal_close_date);
				$diff_days = date_diff($start_date, $diff_close);
				$diffdays = $diff_days->days + 1;

				if($close_select){
					$sql = "UPDATE calendar_events
					SET close_date = CASE
						WHEN start_date <= '".$close_date."' THEN '".$close_date."'
						ELSE $close
					END,
					state = CASE
						WHEN start_date > '".$close_date."' THEN '9'
						ELSE $state
					END
					WHERE work_idx = '".$work_cal_idx."'";
						
					$close_update = updateQuery($sql);

					echo "close";
					echo exit();
				}

			}else{
				$diff_days = date_diff($start_date, $end_date);
				$diffdays = $diff_days->days;
				$cal_close_date = null;
			}
			// 매일반복 등록
			$insertQuery = "INSERT INTO calendar_events(work_idx, state, repeat_frequency, repeat_interval, repeat_type, start_date, close_date) 
							VALUES (?, ?, ?, ?, ?, ?, ?)";

			$insertStmt = $conn->prepare($insertQuery);

			
			$year_date = strtotime($replace_wdate);
				for($i=0; $i< $year_interval; $i++){
					$create_date = date('Y-m-d',strtotime('+'.$i.' year', $year_date));
					$insertData = array(
						$work_cal_idx, '0', $repeat_frequency, $year_interval, $repeat_type,
						$create_date, $cal_close_date 
					);
					$insertStmt->bind_param("issssss", ...$insertData); 
					$insertStmt->execute();
				}
			echo "complete";
		}
	}
}

if($mode == 'calendar_event'){

	
	function getDayOfWeek($dateString) {
		$daysOfWeek = ['일', '월', '화', '수', '목', '금', '토'];
		$date = new DateTime($dateString);
		$dayOfWeekIndex = (int)$date->format('w');
		$dayOfWeek = $daysOfWeek[$dayOfWeekIndex];
		return $dayOfWeek;
	}
	

	$work_idx = $_POST['work_idx'];
	$work_wdate = $_POST['work_wdate']; 
	$replace_wdate = str_replace(".", "-", $work_wdate);
	$end_date = date("Y-m-d", strtotime("+".repeatday." day", TODAYTIME));

	$sql = "select a.idx, b.work_idx, a.state, a.workdate, a.repeat_flag, b.start_date, b.repeat_type, b.close_date, b.repeat_interval, b.repeat_frequency
			from 
		work_todaywork a
			left join calendar_events b on a.idx = b.work_idx
		where 1=1
		and a.email = '".$user_id."' 
		and a.companyno = '".$companyno."'
		and b.repeat_frequency = 'week'
		and a.repeat_flag = '1'
		and b.state = '0'
		and a.idx = '".$work_idx."'"; 
	$for_date = selectAllQuery($sql);
	$for_date_count = count($for_date['idx']);
	$uniqueDays = []; // 중복되지 않은 요일을 저장할 배열
	$daysOfWeek = ['일', '월', '화', '수', '목', '금', '토'];
	for ($i = 0; $i < $for_date_count; $i++) {
		$dayOfWeek = getDayOfWeek($for_date['start_date'][$i]);
		if (!in_array($dayOfWeek, $uniqueDays)) {
			$uniqueDays[] = $dayOfWeek;
		}
	}

	$sql ="select a.idx, a.state, b.work_idx, a.workdate, a.repeat_flag, b.start_date, b.repeat_type, b.close_date, b.repeat_interval, b.repeat_frequency
		from 
		  work_todaywork a
		left join calendar_events b on a.idx = b.work_idx
		where 1=1
		and a.email = '".$user_id."' 
		and a.companyno = '".$companyno."'
		and a.repeat_flag = '1'
		and b.state = '0'
		and a.idx = '".$work_idx."'
		limit 0, 1"; 
	   $first_date = selectAllQuery($sql);
	  
	   if($first_date){ 
		?>
			<div class="replay_day on" id = "cal_cate" value = "<?php echo $first_date['repeat_frequency']['0']?>">
				<div class="replay_title">
				<strong>매일 반복<br> <span>(<?php echo $first_date['workdate']['0']?>)에 시작</span>
				</strong>  
				</div>
				<div class="replay_day_setbox replay_setbox">
				<ul>
					<li>
					<div class="replay_day_set">
						<input type="radio" id="check_day_01" name="day" <?php echo $first_date['repeat_type']['0'] == '1'?"checked":""?>><label for="check_day_01">매일
						반복 </label>
						<input type="radio" id="check_day_02" name="day" value = "noweek" <?php echo $first_date['repeat_type']['0'] == '2'?"checked":""?>><label for="check_day_02">평일 반복(월~금)</label>
						<input type="radio" id="check_day_03" name="day" <?php echo $first_date['repeat_type']['0'] == '3'?"checked":""?>><label for="check_day_03">
						<div class="day_setting">
							<input type = "text" id = "day_setting" value= "<?php echo $first_date['repeat_interval']['0']?$first_date['repeat_interval']['0']:""?>">
						</div>
						<span>일 간격으로 반복</span>
						</label>
					</div>
					</li>
					<li>
					<div class="replay_set_play">
						<input type="radio" id="check_play_day_02" name="check_play_day" value="check_play_day_02" <?php echo $first_date['close_date']['0']?"checked":""?>><label for="check_play_day_02">종료 날짜
						<div class="check_end_day_select check_end_select">
						<input type="date" id="closeDateDay" min = "<?php echo TODATE?>" max = "<?php echo $end_date?>" value ="<?php echo $first_date['close_date']['0']?date('Y-m-d', strtotime($first_date['close_date']['0'])):""?>">
						</div>
						</label>
					</div>
					</li>
					<li>
					<div class="replay_set_cancel">
						<input type="checkbox" id="cancel_day" name="cancel_day"><label for="cancel_day">반복취소</label>
					</div>
					</li>
				</ul>
				</div>
			</div>
			<div class="replay_week" id = "cal_cate" value = "<?php echo $first_date['repeat_frequency']['0']?>">
				<div class="replay_title_week" value = "<?php echo $first_date['repeat_frequency']['0']?>">
				<strong>반복 설정 </strong>
				</div>
				<div class="replay_week_setbox replay_setbox">
				<ul>
					<li> 
					<?php if($for_date){
						echo '<div class="replay_week_set">';
						foreach ($daysOfWeek as $index => $day) {
							$isChecked = in_array($day, $uniqueDays) ? 'checked' : '';
							$inputId = strtolower($day);
							echo '<input type="checkbox" id="' . $inputId . '" name="week" value="' . $index . '" ' . $isChecked . '>';
							echo '<label for="' . $inputId . '">' . $day . '</label>';
						}
						echo '</div>';
					}else{
					?>
						<div class="replay_week_set">
							<input type="checkbox" id="mon" name="week" value = '1'><label for="mon">월</label>
							<input type="checkbox" id="tue" name="week" value = '2'><label for="tue">화</label>
							<input type="checkbox" id="wed" name="week" value = '3'><label for="wed">수</label>
							<input type="checkbox" id="thu" name="week" value = '4'><label for="thu">목</label>
							<input type="checkbox" id="fri" name="week" value = '5'><label for="fri">금</label>
							<input type="checkbox" id="sat" name="week" value = '6'><label for="sat">토</label>
							<input type="checkbox" id="sun" name="week" value = '0'><label for="sun">일</label>
						</div>
					<?php }?>
					<div class="week_replay_type">
						<input type="checkbox" id="week" name="count" checked>
						<label for="week">
						<div class="choice_week">
							<div class="week_setting">
							<button class="week_setting_btn on"><span><?php echo $first_date['repeat_interval']['0']?$first_date['repeat_interval']['0'] : '1'?></span></button>
							<ul>
								<li><button value="1"><span>1</span></button></li>
								<li><button value="2"><span>2</span></button></li>
								<li><button value="3"><span>3</span></button></li>
								<li><button value="4"><span>4</span></button></li>
							</ul>
							</div>
						</label>
					</div>
					<span>주 간격으로 반복</span>
					</li>
					<li>
					<div class="replay_set_play">
						<input type="radio" id="check_play_week_02" name="check_play_week" value="check_play_week_02"><label for="check_play_week_02">종료 날짜
						<div class="check_end_week_select check_end_select">
						<input type="date" id="closeDateWeek"  min = "<?php echo TODATE?>" max = "<?php echo $end_date?>">
						</div>
						</label>
					</div>
					</li>
					<li>
					<div class="replay_set_cancel">
						<input type="checkbox" id="cancel_week" name="cancel_week"><label for="cancel_week">반복취소</label>
					</div>
					</li>
				</ul>
				</div>
			</div>
			<div class="replay_month" id = "cal_cate" value = "<?php echo $first_date['repeat_frequency']['0']?>">
				<div class="replay_title">
				<strong>매월 <?php echo date("d", strtotime($replace_wdate));?>일 반복<br><span>(계속 반복)</span></strong>
				</div>
				<div class="replay_month_setbox replay_setbox">
				<ul>
					<li>
					<div class="replay_month_set">
						<input type="radio" id="check_month_01" name="month" <?php echo $first_date['repeat_type']['0'] == '1'?"checked":""?>><label for="check_month_01">매월 <?php echo date("d", strtotime($replace_wdate));?> 마다
						반복</label>
						<input type="radio" id="check_month_02" name="month" <?php echo $first_date['repeat_type']['0'] == '2'?"checked":""?>><label for="check_month_02">매월 4번째 금요일에 반복</label>
						<input type="radio" id="check_month_03" name="month" <?php echo $first_date['repeat_type']['0'] == '3'?"checked":""?>><label for="check_month_03">매월 마지막 주 금요일에
						반복</label>
					</div>
					</li>
					<li>
					<div class="replay_set_play">
						<input type="radio" id="check_play_month_02" name="check_play_month" value="check_play_month_02"><label for="check_play_month_02">종료 날짜
						<div class="check_end_month_select check_end_select">
						<input type="date" id="closeDateMonth"  min = "<?php echo TODATE?>" max = "<?php echo $end_date?>">
						</div>
						</label>
					</div>
					</li>
					<li>
					<div class="replay_set_cancel">
						<input type="checkbox" id="cancel_month" name="cancel_month"><label for="cancel_month">반복취소</label>
					</div>
					</li>
				</ul>
				</div>
			</div>
			<div class="replay_year"  id = "cal_cate" value = "<?php echo $first_date['repeat_frequency']['0']?>">
				<div class="replay_title">
				<strong>매년 <?php echo date("m", strtotime($replace_wdate));?>월 <?php echo date("d", strtotime($replace_wdate));?>일 반복</strong>
				</div>
				<div class="replay_week_setbox replay_setbox">
				<ul>
					<li>
					<div class="replay_year_set">
						<input type="checkbox" id="year" name="year" checked><label for="yaer">매년 <?php echo date("m", strtotime($replace_wdate));?>월 <?php echo date("d", strtotime($replace_wdate));?>일 반복</label>
					</div>
					</li>
					<li>
					<div class="replay_set_play">
						<input type="radio" id="check_play_year_02" name="check_play_year" value="check_play_year_02"><label for="check_play_year_02">종료 날짜
						<div class="check_end_year_select check_end_select">
						<input type="date" id="closeDateYear"  min = "<?php echo TODATE?>" max = "<?php echo $end_date?>">
						</div>
						</label>
					</div>
					</li>
					<li>
					<div class="replay_set_cancel">
						<input type="checkbox" id="cancel" name="cancel_year"><label for="cancel">반복취소</label>
					</div>
					</li>
				</ul>
				</div>
			</div>
	   <?php }
}

if($mode == 'prev_repeat'){
	$work_idx = $_POST['work_idx'];
	$work_wdate = $_POST['work_wdate'];
	$replace_wdate = str_replace(".", "-", $work_wdate);
	// $update_idx_repeat = updateQuery($sql);

	$sql = "select state, repeat_work_idx, idx, repeat_flag from work_todaywork  where companyno = '".$companyno."' and idx = '".$work_idx."'";
	$repeat_result = selectQuery($sql);
	if($repeat_result){
		$sql = "update work_todaywork set repeat_flag = '0', repeat_work_idx = 'NULL' where companyno = '".$companyno."' and idx = '".$work_idx."'";
		$ori_up = updateQuery($sql);
		if($repeat_result['repeat_work_idx']){
			$sql = "update work_todaywork set state = '9', repeat_flag = '0', repeat_work_idx = 'NULL' where state = '0' and companyno = '".$companyno."' and repeat_work_idx = '".$repeat_result['repeat_work_idx']."' and workdate > '".$replace_wdate."'";
			$up1 = updateQuery($sql);
		}else{
			$sql = "update work_todaywork set state = '9', repeat_flag = '0', repeat_work_idx = 'NULL' where state = '0' and companyno = '".$companyno."' and repeat_work_idx = '".$work_idx."' and workdate > '".$replace_wdate."'";
			$up2 = updateQuery($sql);
		}
		if($up1 || $up2){
			echo "complete";
		}
	}
	
}

if($mode == 'change_time'){
	$change_idx = $_POST['change_idx'];
	$sql = "select idx,companyno, state, name, decide_flag, email, work_stime,work_etime from work_todaywork where idx = '".$change_idx."' and companyno = '".$companyno."' and email = '".$user_id."'";
	$change_status = selectQuery($sql);

	list($c_stime_f, $c_etime_f) =  explode(":",$change_status['work_stime']);
	list($c_stime_e, $c_etime_e) = explode(":",$change_status['work_etime']);

	if($change_status){ ?>
		<div class="layer_title">
		<div class="layer_title_in">
			<p>일정 변경하기</p>
			<em>일정 및 시간을 변경합니다.</em>
		</div>
		</div>
		<div class="layer_btn">
			<ul>
				<li class="<?=$change_status['decide_flag'] == '2'?"on":""?>"><button><span value = "2">반차</span></button></li>
				<li class="<?=$change_status['decide_flag'] == '3'?"on":""?>"><button><span value = "3">외출</span></button></li>
				<li class="<?=$change_status['decide_flag'] == '4'?"on":""?>"><button><span value = "4">조퇴</span></button></li>
				<li class="<?=$change_status['decide_flag'] == '5'?"on":""?>"><button><span value = "5">출장</span></button></li>
				<li class="<?=$change_status['decide_flag'] == '6'?"on":""?>"><button><span value = "6">교육</span></button></li>
				<li class="<?=$change_status['decide_flag'] == '7'?"on":""?>"><button><span value = "7">미팅</span></button></li>
				<li class="<?=$change_status['decide_flag'] == '8'?"on":""?>"><button><span value = "8">회의</span></button></li>
				
			</ul>
		</div>
		<div class="layer_time_set">
			<input type="hidden" id="sChangeHour"/>
			<input type="hidden" id="sChangeMin"/>
			<input type="hidden" id="eChangeHour"/>
			<input type="hidden" id="eChangeMin"/>
			<input type="hidden" id="changeIdx"/>
			<div class="layer_time_set_in">
				<div class="tdw_time_start">
				<div class="tdw_time_hour time_set">
					<div class="tdw_tab_sort_in">
					<button class="btn_sort_on first_set" value="<?=$c_stime_f?>"><span><?=$c_stime_f?></span></button>
						<ul>
						<?for($i=1; $i < 25; $i++){?>
							<li><button class = "s_changeTimeHour" value = "<?=$i?>"><span><?=$i?></span></button></li>
						<?}?>
						</ul>
					</div>
				</div>
				<div class="tdw_time_min time_set">
					<div class="tdw_tab_sort_in">
					<button class="btn_sort_on second_set" value="<?=$c_etime_f?>"><span><?=$c_etime_f?></span></button>
						<ul>
						<?for($i = 0; $i <= 50; $i += 10){?>
							<li><button class = "s_changeTimeMin" value = "<?=($i === 0) ? '00' : $i?>"><span><?=$i?></span></button></li>
						<?}?>
						</ul>
					</div>
				</div>
				</div>
				<!-- <div class="time_wave"><span>&#126;</span></div> -->
				<div class="tdw_time_end">
				<div class="tdw_time_hour time_set">
					<div class="tdw_tab_sort_in">
					<button class="btn_sort_on first_set" value="<?=$c_stime_e?>"><span><?=$c_stime_e?></span></button>
						<ul>
						<?for($i=1; $i < 25; $i++){?>
							<li><button class = "e_changeTimeHour" value = "<?=$i?>"><span><?=$i?></span></button></li>
						<?}?>
						</ul>
					</div>
				</div>
				<div class="tdw_time_min time_set">
					<div class="tdw_tab_sort_in">
					<button class="btn_sort_on second_set" value="<?=$c_etime_e?>"><span><?=$c_etime_e?></span></button>
						<ul>
						<?for($i = 0; $i <= 50; $i += 10){?>
							<li><button class = "e_changeTimeMin" value = "<?=($i === 0) ? '00' : $i?>"><span><?=$i?></span></button></li>
						<?}?>
						</ul>
					</div>
				</div>
				</div>
			</div>
		</div>
		<div class="layer_time_btn">
			<button class="layer_time_cancel"><span>취소</span></button>
			<button class="layer_time_submit on"><span>등록하기</span></button>
		</div>
	<?
	}
}

if($mode == "change_work_decide"){
	$idx = $_POST['change_idx'];
	$decide = $_POST['decide_flag'];
	$start_time = $_POST['start_time'];
	$end_time = $_POST['end_time'];

	$sql = "select idx, state, email, name, companyno, decide_flag, work_stime, work_etime from work_todaywork where 1=1 and email = '".$user_id."' and companyno = '".$companyno."' and idx = '".$idx."'";
	$decide_work = selectQuery($sql);
	if($decide_work){
		if($decide_work['decide_flag'] == $decide && $decide_work['work_stime'] == $start_time && $decide_work['work_etime'] == $end_time){
			echo "fail";
		}
		else{
			if($decide == '0'){
				$sql = "update work_todaywork set decide_flag = '".$decide."', work_stime = NULL , work_etime = NULL  where idx = '".$idx."' and companyno = '".$companyno."' and email = '".$user_id."'";
			}else{
				$sql = "update work_todaywork set decide_flag = '".$decide."', work_stime = '".$start_time."', work_etime = '".$end_time."'  where idx = '".$idx."' and companyno = '".$companyno."' and email = '".$user_id."'";
			}
			$up_decide = updateQuery($sql);
			if($up_decide){
				echo "complete";
			}
		}
	}
}
?>