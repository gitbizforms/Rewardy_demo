<?php

$home_dir = str_replace( basename(__DIR__) , "" , __DIR__ );

include $home_dir . "inc_lude/conf_mysqli.php";
include $home_dir . "inc/SHA256/KISA_SHA256.php";
include DBCON_MYSQLI;
include FUNC_MYSQLI;

//최대 실행시간으로 변경
//ini_get('max_execution_time'); // 30
set_time_limit(0);
//ini_get('max_execution_time'); // 100
//메모리 제한 풀기
ini_set('memory_limit','-1');


$mode = $_POST["mode"];									////mode값 전달받음
$type_flag = ($chkMobile)?1:0;							//구분(0:사이트, 1:모바일)

if($_COOKIE){

	$user_id = $_COOKIE['user_id'];
	$user_name = $_COOKIE['user_name'];
	$user_level = $_COOKIE['user_level'];
	$user_part = $_COOKIE['user_part'];

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

	$cate = $_POST['cate'];
	$title = $_POST['title'];
	$date1 = $_POST['date1'];
	$date2 = $_POST['date2'];
	$day_chk = $_POST['day_chk'];
	
	//$contents = nl2br($_POST['contents']);
	$contents = $_POST['contents'];
	$chall_day = $_POST['chall_day'];

	//기간내 한번만, 매일
	if($chall_day == "one"){
		$day_type="0";
	}else if($chall_day == "daily"){
		$day_type="1";
	}

	//키워드
	$write_keyword = $_POST['write_keyword'];


	$user = $_POST['user'];
	$view = $_POST['view'];
	$dayis = $_POST['dayis'];

	//참여자대상 -일부만
	if($user == "sel"){
		$work_mem_idx = @implode("','",$_POST['chk']);
		$sql = "select idx, email, name from work_member where state='0' and companyno='".$companyno."' and idx in ('".$work_mem_idx."')";
		$chall_mem_info = selectAllQuery($sql);
	}else if($user == "all"){

		//참여자 전체
		$sql = "select idx, email, name from work_member where state='0' and companyno='".$companyno."'";
		$chall_mem_info = selectAllQuery($sql);
	}

	//노출여부 - 숨김
	if($view == "no"){
		$view_flag = "1";
	}else if($view == "yes"){
		$view_flag = "0";
	}

	$coin = $_POST['h4'];
	$coin = preg_replace("/[^0-9]/", "", $coin);

	$title = addslashes($title);
	$contents = addslashes($contents);


	$sql = "insert into work_challenges(email, name, cate, title, sdate, edate, day_type, week, type_flag, view_flag, coin, ip, keyword)";
	$sql = $sql .= " values('".$user_id."','".$user_name."', '".$cate."', '".$title."','".$date1."','".$date2."','".$day_type."', '".$dayis."', '".$type_flag."',";
	$sql = $sql .= "'".$view_flag."','".$coin."','".LIP."','".$write_keyword."')";
	//$res = insertQuery($sql);
	$res_idx = insertIdxQuery($sql);

	if($res_idx){
		$kind = "2"; //챌린지

		//참여자등록
		for($i=0; $i<count($chall_mem_info['idx']); $i++){
			$sql = "select idx from work_challenges_user where state='0' and challenges_idx='".$res_idx."' and email='".$chall_mem_info['email'][$i]."'";
			$chall_row = selectQuery($sql);

			if(!$chall_row['idx']){
				$sql = "insert into work_challenges_user(challenges_idx,email,name,ip) values('".$res_idx."','".$chall_mem_info['email'][$i]."','".$chall_mem_info['name'][$i]."','".LIP."')";
				$res = insertQuery($sql);
			}
		}

		//참여요일
		if (is_array($day_chk) == true){
			for($i=0; $i<count($day_chk); $i++){
	
				$sql = "select idx from work_challenges_day where state='0' and challenges_idx='".$res_idx."' and email='".$user_id."'";
				$day_row = selectQuery($sql);
				if(!$day_row['idx']){
					$sql = "insert into work_challenges_day(challenges_idx, email, name, day, weekit, ip)";
					$sql .= " values('".$res_idx."', '".$user_id."', '".$user_name."','".$day_chk[$i]."', '".$week[$day_chk[$i]]."','".LIP."')";
					$res = insertQuery($sql);
				}
			}
		}

		$sql = "insert into work_contents( kind, work_idx, contents) values('".$kind."','".$res_idx."','".$contents."')";
		$res = insertQuery($sql);


		//파일체크
		$file_upload_check = false;
		
		//파일갯수
		$file_upload_cnt = count($_FILES['files']['name']);

		//파일갯수
		$fileimg_upload_cnt = count($_FILES['files_img']['name']);

		if ($file_upload_cnt > 0 ){

			//파일첨부 여부
			$file_upload_check = true;

			for($i=0; $i<$file_upload_cnt; $i++){

				$filename = $_FILES['files']['name'][$i];
				$ext = array_pop(explode(".", strtolower($filename)));

				//허용확장자체크
				if( !in_array($ext, $file_allowed_ext) ) {
					//echo "ext_file1";
					//exit;
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
				$renamefile = "{$datetime}_{$rand_id}_challenges_{$res_idx}.{$ext}";

				//년도
				$dir_year = date("Y", TODAYTIME);

				//월
				$dir_month = date("m", TODAYTIME);



				//업로드 디렉토리 - /data/challenges/files/년/월/
				$upload_path = $dir_file_path."/".$file_save_dir."/".$dir_year."/".$dir_month."/";

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

					$sql = "select idx from work_filesinfo_file where state='0' and work_idx='".$res_idx."' and email='".$user_id."'";
					$fileinfo = selectAllQuery($sql);
					if ( count($fileinfo['idx']) < 4){
						$sql = "insert into work_filesinfo_file(work_idx, email, kind, file_path, file_name, file_size, file_type, ip) values(";
						$sql = $sql .="'".$res_idx."', '".$user_id."', '".$kind."', '".$file_path."', '".$renamefile."', '".$file_size."',  '".$file_type."', '".LIP."')";
						//$res_file = insertQuery($sql);
						$files_idx = insertIdxQuery($sql);

					}
				}

			}
		}


		//첨부파일 이미지
		if ($fileimg_upload_cnt > 0){

			//파일첨부 여부
			$file_upload_check = true;

			for($i=0; $i<$fileimg_upload_cnt; $i++){

				//파일확장자 추출
				$filename = $_FILES['files_img']['name'][$i];
				$ext = array_pop(explode(".", strtolower($filename)));

				//허용확장자체크
				if( !in_array($ext, $img_file_allowed_ext) ) {
					echo "ext_file2";
					exit;
				}

				//파일타입
				$file_type = $_FILES['files_img']['type'][$i];

				//파일사이즈
				$file_size = $_FILES['files_img']['size'][$i];

				//임시파일명
				$file_tmp_name = $_FILES['files_img']['tmp_name'][$i];

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
				$renamefile = "{$datetime}_{$rand_id}_challenges_{$res_idx}.{$ext}";

				//년도
				$dir_year = date("Y", TODAYTIME);

				//월
				$dir_month = date("m", TODAYTIME);

				//업로드 디렉토리 - /data/challenges/files/년/월/
				$upload_path = $dir_file_path."/".$file_save_dir_img."/".$dir_year."/".$dir_month."/";

				//업로드 디렉토리 - /data/challenges/files/년/월/
				$upload_path_ori = $dir_file_path."/".$file_save_dir_img_ori."/".$dir_year."/".$dir_month."/";

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


				$new_file_width = 250; //이미지 가로 사이즈 지정
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


					$sql = "select idx from work_filesinfo_img where state='0' and work_idx='".$res_idx."' and email='".$user_id."'";
					$fileinfo = selectAllQuery($sql);
					if ( count($fileinfo['idx']) < 7){

						$sql = "insert into work_filesinfo_img(work_idx, email, kind, resize, file_path, file_name, file_size, file_ori_path, file_ori_name, file_ori_size, file_real_name, file_type, ip) values(";
						$sql = $sql .="'".$res_idx."','".$user_id."','".$kind."','".$resize_val."','".$rezie_file_path."','".$rezie_renamefile."','".$resize_file."','".$file_path_ori."','".$renamefile."','".$file_size."','".$file_real_name."','".$file_type."','".LIP."')";
						//$res_file = insertQuery($sql);
						$files_idx = insertIdxQuery($sql);

						//if($files_idx){
						//	$sql = "update work_challenges set files_idx='".$files_idx."', files_name='".$renamefile."' where state='0' and idx='".$res_idx."'";
						//	updateQuery($sql);
						//}
					}
				

				}else{
					echo "file_not";
					exit;
				}


			}

		}

	}

	if($res_idx && $res){
		echo "complete";
		exit;
	}

	exit;
}


	
//챌린지 완료하기
if($mode =="challenges_complete"){

	$chall_idx = $_POST['chall_idx'];
	$chall_idx = preg_replace("/[^0-9]/", "", $chall_idx);

	if($chall_idx){



		$sql = "select idx, email, name, coin, title, sdate, edate, day_type, outputchk from work_challenges where state='0' and idx='".$chall_idx."'";
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

						$sql = "select idx from work_challenges_com where state='1' and email='".$user_id."' and challenges_idx='".$chall_idx."'";
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


								//챌린지 작성자 회원정보
								$sql = "select idx, highlevel from work_member where state='0' and companyno='".$companyno."' and email = '".$challenges_info['email']."'";
								$mem_write_info = selectQuery($sql);

								//챌린지 작성한 회원의 코인차감
								$sql = "insert into work_coininfo(state, work_idx, companyno, email, name, reward_user, reward_name, coin, memo, workdate, ip) values('1', '".$chall_idx."', '".$companyno."', '".$challenges_info['email']."', '".$challenges_info['name']."', '".$user_id."', '".$user_name."', '".$challenges_info['coin']."', '".$coin_info."','".TODATE."','".LIP."')";
								$coin_res = insertQuery($sql);
								if($coin_res){

									//관리자 권한
									if($mem_write_info['highlevel'] == '0'){
										$sql = "update work_member set comcoin = comcoin - '".$challenges_info['coin']."' where state='0' and email='".$challenges_info['email']."'";
										$res_info1 = updateQuery($sql);

									//일반사용자
									}else if($mem_write_info['highlevel'] == '5'){
										$sql = "update work_member set comcoin = comcoin - '".$challenges_info['coin']."' where state='0' and email='".$challenges_info['email']."'";
										$res_info1 = updateQuery($sql);
									}
								}

								//챌린지 완료한 회원의 코인적립
								$sql = "insert into work_coininfo(state, work_idx, companyno, email, name, reward_user, reward_name, coin, memo, workdate, ip) values('0', '".$chall_idx."', '".$companyno."', '".$user_id."', '".$user_name."', '".$challenges_info['email']."', '".$challenges_info['name']."', '".$challenges_info['coin']."', '".$coin_info."','".TODATE."','".LIP."')";
								$coin_res = insertQuery($sql);
								if($coin_res){
									$sql = "update work_member set coin = coin + '".$challenges_info['coin']."' where state='0' and companyno='".$companyno."' and email='".$user_id."'";
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

							//챌린지 작성자 회원정보
							$sql = "select idx, highlevel from work_member where state='0' and companyno='".$companyno."' and email = '".$challenges_info['email']."'";
							$mem_write_info = selectQuery($sql);

							//챌린지 작성한 회원의 코인차감
							$sql = "insert into work_coininfo(state, work_idx, companyno, email, name, reward_user, reward_name, coin, memo, workdate, ip) values('1', '".$chall_idx."', '".$companyno."', '".$challenges_info['email']."', '".$challenges_info['name']."', '".$user_id."', '".$user_name."', '".$challenges_info['coin']."', '".$coin_info."','".TODATE."','".LIP."')";
							$coin_res = insertQuery($sql);
							if($coin_res){

								//관리자 권한
								if($mem_write_info['highlevel'] == '0'){
									$sql = "update work_member set comcoin = comcoin - '".$challenges_info['coin']."' where state='0' and companyno='".$companyno."' and email='".$challenges_info['email']."'";
									$res_info1 = updateQuery($sql);
								//일반사용자
								}else if($mem_write_info['highlevel'] == '5'){
									$sql = "update work_member set comcoin = comcoin - '".$challenges_info['coin']."' where state='0' and companyno='".$companyno."' and email='".$challenges_info['email']."'";
									$res_info1 = updateQuery($sql);
								}
							}

							//챌린지 완료한 회원의 코인적립
							$sql = "insert into work_coininfo(state, work_idx, companyno, email, name, reward_user, reward_name, coin, memo, workdate, ip) values('0', '".$chall_idx."', '".$companyno."', '".$user_id."', '".$user_name."', '".$challenges_info['email']."', '".$challenges_info['name']."','".$challenges_info['coin']."', '".$coin_info."','".TODATE."','".LIP."')";
							$coin_res = insertQuery($sql);
							if($coin_res){
								$sql = "update work_member set coin = coin + '".$challenges_info['coin']."' where state='0' and companyno='".$companyno."' and email='".$user_id."'";
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
if($mode == "challenges_del"){

	$chall_idx = $_POST['chall_idx'];
	$chall_idx = preg_replace("/[^0-9]/", "", $chall_idx);

	$sql = "select idx, email, name, coin, title, sdate, edate, day_type from work_challenges where state='0' and idx='".$chall_idx."'";
	$challenges_info = selectQuery($sql);

	$sql = "select idx from work_challenges_com where state='0' and challenges_idx='".$chall_idx."' and email='".$user_id."'";
	$chall_com = selectAllQuery($sql);

	if(!$chall_com['idx']){
		if($user_id == $challenges_info['email']){
			$sql = "update work_challenges set state='9' where idx='".$chall_idx."' and email='".$user_id."'";
			$res = updateQuery($sql);
			if($res){
				echo "complete";
				exit;
			}

		}else{
			echo "not_id";
			exit;
		}
	}
}


//챌린지작성완료 또는 임시저장
if($mode == "chall_edit"){

	// 템플릿 여부
	$ch_temp = $_POST['ch_temp'];

	//챌린지번호
	$chall_idx = $_POST['chall_idx'];

	//챌린지 참여 형태( 1:메시지형, 2:파일첨부형, 3:혼합형)
	$write_type = 3;

	//챌린지카테고리
	$cate = $_POST['cate'];

	//챌린지제목
	$title = $_POST['title'];
	$title = addslashes($title);

	//챌린지내용
	$contents = $_POST['contents'];
	$contents = addslashes($contents);	
	//$contents = urlencode($contents);

	//챌린지 임시저장
	$temp_save = $_POST['temp_save'];

	//챌린지 시작날짜
	$sdate = $_POST['sdate'];

	//챌린지 종료날짜
	$edate = $_POST['edate'];

	//챌린지 참여자
	$input_count = $_POST['input_count'];

	//챌린지 선착순 제한
	$limit_cnt = $_POST['limit_count'];

	//챌린지 참여자(전체:all, 일부:sel)
	$user = $_POST['user'];

	//챌린지 참여자 일부
	$chall_user_chk = $_POST['chall_user_chk'];

	//챌린지 코인
	$input_coin = $_POST['input_coin'];

	//챌린지 참여횟수 설정(한번)
	$ch_once = $_POST['ch_once'];

	//챌린지 참여횟수 설정(매일)
	$ch_daily = $_POST['ch_daily'];

	//챌린지 참여횟수 공휴일제외
	$ch_holiday = $_POST['ch_holiday'];

	//챌린지 참여자 설정(all:전체, sel:일부)
	$user = $_POST['user'];

	//챌린지 코인사용 안함(0:사용, 1:사용안함)
	$ch_not_coin = $_POST['ch_not_coin'];

	//챌린지 전체 예상 지급코인
	$total_coin = $_POST['total_coin'];

	//템플릿 여부
	$template_enter = $_POST['template'];

	// print_r($_POST);
	if($ch_not_coin == true){
		$coin_not = 1;
	}else{
		$coin_not = 0;
	}

	if($template_enter == "1"){
		$template = "1";
	}else{
		$template = "0";
	}

	//챌린지 테마선택
	$chall_thema_chk = trim($_POST['chall_thema_chk']);
	$thema_idx = @explode(",",$chall_thema_chk);
	@asort($thema_idx);

	//키워드가 있을때 업데이트
	$write_keyword = $_POST['write_keyword'];
	if($write_keyword){
		$que = ", keyword='".$write_keyword."'";
	}

	$cate = preg_replace("/[^0-9]/", "", $cate);
	// $write_type = preg_replace("/[^0-9]/", "", $write_type);
	$temp_save = preg_replace("/[^0-9]/", "", $temp_save);
	$input_count = preg_replace("/[^0-9]/", "", $input_count);
	$input_coin = preg_replace("/[^0-9]/", "", $input_coin);
	$ch_holiday = preg_replace("/[^0-9]/", "", $ch_holiday);

	//임시저장여부
	if($temp_save =="1"){
		$temp_flag = "1";
	}else{
		$temp_flag = "0";
	}

	if($ch_once == 1){
		$day_type = "0";
	}else{
		if($ch_daily == 1){
			$day_type = "1";
		}
	}

	//챌린지제목
	//preg_match("/[\x{10000}-\x{10FFFF}]/u", $title, $ret);

	//개행문자가 있을때 nl2br처리
	if(strpos($title, "\n") !== false) {
		$title = nl2br($title);
	}
	//$title = urlencode($title);

	//챌린지 참여자 설정(all:전체, sel:일부)
	//참여자대상 -일부만
	if($user == "all"){
		//참여자 전체
		//관리자 권한은 제외
		$attend_chk = "0";
		$sql = "select idx, email, name, highlevel, partno, part from work_member where state='0' and companyno='".$companyno."' and highlevel!='1'";
		$chall_mem_info = selectAllQuery($sql);

	}else if($user == "sel"){
		//참여자 일부
		$attend_chk = "1";
		//$work_mem_idx = @implode("','",$chall_user_chk);
		$work_mem_idx = trim($chall_user_chk);
		$sql = "select idx, email, name, highlevel, partno, part from work_member where state='0' and companyno='".$companyno."' and idx in (".$work_mem_idx.")";
		$chall_mem_info = selectAllQuery($sql);
	}

	if($chall_idx){
		$sql = "select idx, day_type, attend, template, coin, total_max_coin, email from work_challenges where idx='".$chall_idx."' ";
		// $sql = $sql .= "and email='".$user_id."'"; // -> 수정시 자신이 작성한 템플릿 아니여도 수정할 수 있게 변경 
		$chall_info = selectQuery($sql);
		if($chall_info['idx']){

			//챌린지 관리권한
			//if($template_auth == '1'){
			//테마를 등록 했을경우는 챌린지를 테마에 등록하기
			//테마를 등록 안하면 일반 챌린지로 등록하기
			// if($chall_thema_chk || $_COOKIE['chall_tpl'] =='1' || $chall_info['template']=='1'){
			// 	$template = $template_auth;
			// }else{
			// 	$template = '0';
			// }

			$sql = "select coin from work_coininfo where work_idx = '".$res_idx."' and email = '".$user_id."' ";
			$coin_info = selectQuery($sql);
			$update_coin_info = $total_coin - $chall_info['total_max_coin']; // 새로 수정한 코인 - 기존에 등록된 코인

			$sql = "select comcoin from work_member where email = '".$chall_info['email']."'";
			$user_comcoin = selectQuery($sql);

			if($update_coin_info > 0){
				
				$sql = "update work_coininfo set coin = '".$total_coin."' where work_idx = '".$chall_idx."' and code = '530'";
				$cos = updateQuery($sql);

				$updateCoin = $user_comcoin['comcoin'] - $update_coin_info;
				$sql = "update work_member set comcoin = '".$updateCoin."' where email = '".$chall_info['email']."' and state = '0'";
				$cos2 = updateQuery($sql);
			}

			$sql = "update work_challenges set attend_type='".$write_type."', cate='".$cate."', title='".$title."', temp_flag='".$temp_flag."',template='".$template."'";
			$sql = $sql .= ", type_flag='".$type_flag."',sdate='".$sdate."', edate='".$edate."', attend='".$input_count."', day_type='".$day_type."', holiday_chk='".$ch_holiday."', attend_chk='".$attend_chk."', view_flag='".$view_flag."', coin='".$input_coin."', coin_not='".$coin_not."'";
			$sql = $sql .= ", companyno='".$companyno."', editdate=".DBDATE."".$que." , total_max_coin = '".$total_coin."' , limit_count = '".$limit_cnt."' where idx='".$chall_info['idx']."'";
			// echo $sql."!@#!@#";
			$res = updateQuery($sql);
			
			if($res){
				$res_idx = $chall_info['idx'];
			}
						
		}
	}
	
	//등록된 idx값체크
	if($res_idx){
		$kind = "2"; //챌린지

		//챌린지 내용저장
		$sql = "select idx from work_contents where state='0' and kind='".$kind."' and work_idx='".$res_idx."'";
		$content_info = selectQuery($sql);
		if($content_info['idx']){
			$sql = "update work_contents set contents='".$contents."', editdate=".DBDATE." where work_idx='".$res_idx."'";
			$res = updateQuery($sql);
		}


		//기존 테마설정이 있을경우 삭제처리
		$sql = "select idx, sort from work_challenges_thema_list where state='0' and companyno='".$companyno."' and challenges_idx='".$res_idx."'";
		$thema_info = selectQuery($sql);
		if($thema_info['idx']){
			$thema_info_sort = $thema_info['sort'];
			$sql = "update work_challenges_thema_list set state='9' where state='0' and companyno='".$companyno."' and challenges_idx='".$res_idx."'";
			$up = updateQuery($sql);
		}

		//챌린지 테마등록
		if($thema_idx){
			for($i=0; $i<count($thema_idx); $i++){
				$sql = "select idx from work_challenges_thema_list where state='0' and companyno='".$companyno."' and challenges_idx='".$res_idx."' and thema_idx='".$thema_idx[$i]."'";
				$thema_list_info = selectQuery($sql);
				if(!$thema_list_info['idx']){
					$sql = "insert into work_challenges_thema_list(challenges_idx, thema_idx, sort, companyno, ip) values('".$res_idx."','".$thema_idx[$i]."','".$thema_info_sort."','".$companyno."','".LIP."')";
					$res = insertQuery($sql);
				}
			}
		}

		
		//참여자 재설정
		$sql = "delete from work_challenges_user where challenges_idx='".$res_idx."'";
		updateQuery($sql);

		for($i=0; $i<count($chall_mem_info['idx']); $i++){
			$sql = "select idx from work_challenges_user where state='0' and companyno='".$companyno."' and challenges_idx='".$res_idx."' and email='".$chall_mem_info['email'][$i]."'";
			$chall_row = selectQuery($sql);

			if(!$chall_row['idx']){
				$sql = "insert into work_challenges_user(challenges_idx,email,name,companyno,ip) values('".$res_idx."','".$chall_mem_info['email'][$i]."','".$chall_mem_info['name'][$i]."','".$companyno."','".LIP."')";
				$res = insertQuery($sql);
			}


			//임시저장이 아닌것만 오늘업무에 알림으로 설정
			//챌린지 템플릿이 아닌것만 알림설정
			// if($temp_save=="0" && $template == "0"){

			// 	//템플릿이 아닌것만 알림으로 등록
			// 	if($chall_info['template']=='0'){
			// 		//내용이 있을시 삭제처리
			// 		//공지 테이블 work_notice : 1:챌린지알림 
			// 		$notice_flag = '1';
			// 		$sql = "select idx from work_todaywork where state='0' and work_flag='2' and notice_flag='".$notice_flag."' and email='".$chall_mem_info['email'][$i]."' and work_idx='".$res_idx."' and workdate='".TODATE."'";
			// 		$todaywork_info = selectQuery($sql);
			// 		if($todaywork_info['idx']){
			// 			$sql = "update work_todaywork set state='9' where state='0' and work_flag='2' and notice_flag='".$notice_flag."' and email='".$chall_mem_info['email'][$i]."' and work_idx='".$res_idx."' and workdate='".TODATE."'";
			// 			updateQuery($sql);
			// 		}

			// 		//챌린지 등록시 오늘업무에 등록 처리
			// 		$sql = "select idx from work_todaywork where state='0' and work_flag='2' and notice_flag='".$notice_flag."' and email='".$chall_mem_info['email'][$i]."' and work_idx='".$res_idx."' and workdate='".TODATE."'";
			// 		$todaywork_info = selectQuery($sql);
			// 		if(!$todaywork_info['idx']){

			// 			//매일참여
			// 			if ($chall_info['day_type'] == '1'){
			// 				$cal_coin = $input_coin * $chall_info['attend'];
			// 				$work_contents = $_POST['title'] . " (최대 ".number_format($cal_coin). " 코인 획득 가능)";
			// 			}else{
			// 				$cal_coin = $input_coin;
			// 				$work_contents = $_POST['title'] . " (".number_format($cal_coin). " 코인 획득 가능)";
			// 			}

			// 			$sql = "insert into work_todaywork(highlevel, work_flag, part_flag, part, type_flag, notice_flag, work_idx, email, name, contents, companyno, ip, workdate) ";
			// 			$sql = $sql .= " values('".$chall_mem_info['highlevel'][$i]."', '2', '".$chall_mem_info['partno'][$i]."' ,'".$chall_mem_info['part'][$i]."', '".$type_flag."', '".$notice_flag."', '".$res_idx."', '".$chall_mem_info['email'][$i]."', '".$chall_mem_info['name'][$i]."', '".$work_contents."','".$companyno."','".LIP."','".TODATE."')";
			// 			$work_insert = insertQuery($sql);
			// 		}
			// 	}
			// }
			// 2023.06.08 알림 중복 방지를 위해 주석처리 : 수정시에는 알림이 안가도록 변경
		}


		//파일체크
		$file_upload_check = false;
				
		//파일갯수
		$file_upload_cnt = count($_FILES['files']['name']);

		//파일갯수
		$fileimg_upload_cnt = count($_FILES['files_img']['name']);


		if ($file_upload_cnt > 0 ){

			//파일첨부 여부
			$file_upload_check = true;

			for($i=0; $i<$file_upload_cnt; $i++){

				$filename = $_FILES['files']['name'][$i];
				$ext = array_pop(explode(".", strtolower($filename)));

				//허용확장자체크
				if( !in_array($ext, $file_allowed_ext) ) {
					//echo "ext_file1|";
					//exit;
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
				$renamefile = "{$datetime}_{$rand_id}_challenges_{$res_idx}.{$ext}";

				//년도
				$dir_year = date("Y", TODAYTIME);

				//월
				$dir_month = date("m", TODAYTIME);


				//업로드 디렉토리 - /data/(회사고유번호)/(회사폴더명)/challenges/files/년/월/
				$upload_path = $dir_file_path."/".$file_save_dir."/".$dir_year."/".$dir_month."/";
				$upload_path = str_replace($file_save_dir , "data/".$companyno."/".$comfolder."/"."challenges/files" , $upload_path);
				


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

					$sql = "select idx from work_filesinfo_file where state='0' and work_idx='".$res_idx."'  and num='".$i."'";
					// $sql = $sql .= " and email='".$user_id."'";
					$fileinfo = selectQuery($sql);


					if ($fileinfo['idx']){
						
						$sql = "update work_filesinfo_file set file_path='".$file_path."', file_name='".$renamefile."', file_size='".$file_size."', file_real_name='".$file_real_name."',file_type='".$file_type."' where num='".$i."' and idx='".$fileinfo['idx']."'";
						$res = updateQuery($sql);

					}else{
						$sql = "insert into work_filesinfo_file(work_idx, email, num, kind, file_path, file_name, file_size, file_real_name, file_type, ip) values(";
						$sql = $sql .="'".$res_idx."', '".$user_id."', '".$i."','".$kind."', '".$file_path."', '".$renamefile."', '".$file_size."', '".$file_real_name."', '".$file_type."', '".LIP."')";
						//$res_file = insertQuery($sql);
						$files_idx = insertIdxQuery($sql);

						//if($files_idx){
						//	$sql = "update work_challenges set files_idx='".$files_idx."', files_name='".$renamefile."' where state='0' and idx='".$res_idx."'";
						//	updateQuery($sql);
						//}
					}

					//파일갯수
					//if ( count($fileinfo['idx']) < 4){
					//}
				}
			}
		}


		//첨부파일 이미지
		if ($fileimg_upload_cnt > 0){

			//파일첨부 여부
			$file_upload_check = true;

			//파일순번
			$file_img_num = 1;
			for($i=0; $i<$fileimg_upload_cnt; $i++){


				//파일확장자 추출
				$filename = $_FILES['files_img']['name'][$i];
				$ext = array_pop(explode(".", strtolower($filename)));

				//허용확장자체크
				if( !in_array($ext, $file_allowed_ext) ) {
					//echo "ext_file2|";
					//exit;
				}

				//파일타입
				$file_type = $_FILES['files_img']['type'][$i];

				//파일사이즈
				$file_size = $_FILES['files_img']['size'][$i];

				//임시파일명
				$file_tmp_name = $_FILES['files_img']['tmp_name'][$i];

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
				$renamefile = "{$datetime}_{$rand_id}_challenges_{$res_idx}.{$ext}";

				//년도
				$dir_year = date("Y", TODAYTIME);

				//월
				$dir_month = date("m", TODAYTIME);

				//업로드 디렉토리 - /data/회사고유/회사랜덤폴더명/challenges/files/년/월/
				$upload_path = $dir_file_path."/".$file_save_dir_img."/".$dir_year."/".$dir_month."/";
				$upload_path = str_replace($file_save_dir_img , "data/".$companyno."/".$comfolder."/"."challenges/img" , $upload_path);

				//업로드 디렉토리 - /data/회사고유/회사랜덤폴더명/challenges/files/년/월/
				$upload_path_ori = $dir_file_path."/".$file_save_dir_img_ori."/".$dir_year."/".$dir_month."/";
				$upload_path_ori = str_replace($file_save_dir_img_ori , "data/".$companyno."/".$comfolder."/"."challenges/img_ori" , $upload_path_ori);

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


				$new_file_width = 250; //이미지 가로 사이즈 지정
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

					$sql = "select idx from work_filesinfo_img where state='0' and work_idx='".$res_idx."' and num='".$i."' ";
					// $sql = $sql .= "and email='".$user_id."'";
					$fileinfo = selectQuery($sql);

					if($fileinfo['idx']){

						$sql = "update work_filesinfo_img set resize='".$resize_val."', file_path='".$rezie_file_path."', file_name='".$rezie_renamefile."', file_size='".$resize_file."', file_ori_path='".$file_path_ori."', file_ori_name='".$renamefile."', file_ori_size='".$file_size."', file_real_name='".$file_real_name."',file_type='".$file_type."' where num='".$i."' and idx='".$fileinfo['idx']."'";
						$res = updateQuery($sql);

					}else{

						$sql = "insert into work_filesinfo_img(work_idx, email, num, kind, resize, file_path, file_name, file_size, file_ori_path, file_ori_name, file_ori_size, file_real_name, file_type, ip) values(";
						$sql = $sql .="'".$res_idx."','".$user_id."','".$i."','".$kind."','".$resize_val."','".$rezie_file_path."','".$rezie_renamefile."','".$resize_file."','".$file_path_ori."','".$renamefile."','".$file_size."','".$file_real_name."','".$file_type."','".LIP."')";
						//$res_file = insertQuery($sql);
						$files_idx = insertIdxQuery($sql);

						//if($files_idx){
						//	$sql = "update work_challenges set files_idx='".$files_idx."', files_name='".$renamefile."' where state='0' and idx='".$res_idx."'";
						//	updateQuery($sql);
						//}

					}

					//if ( count($fileinfo['idx']) < 7){
					//}

				}else{
					echo "file_not";
					exit;
				}
			}
		}

		//temp_flag(임시저장:1, 0:등록완료)
		if($temp_flag == 1){
			echo "temp_complete|".$res_idx;
		}else{
			echo "complete|".$res_idx;
		}
	}else{

		echo "not!!!|".$chall_idx;
	}

	exit;
}



//챌린지작성완료 또는 임시저장
if($mode == "chall_save"){
	//print_r($_POST);
	//print_r($_FILES);

	//챌린지번호
	$chall_idx = $_POST['chall_idx'];

	//챌린지 참여 형태( 1:메시지형, 2:파일첨부형, 3:혼합형)
	$write_type = 3;

	//챌린지카테고리(역량점수)
	$cate = $_POST['cate']; 

	//챌린지제목
	//$title = addslashes($_POST['title']);
	//$title = mssql_escape($_POST['title']);

	//챌린지제목 이모지
	$title = $_POST['title'];
	$title = addslashes($title);
	
	//챌린지내용
	$contents = $_POST['contents'];
	$contents = addslashes($contents);

	//$contents = urlencode($contents);

	//챌린지 임시저장
	$temp_save = $_POST['temp_save'];

	//챌린지 시작날짜
	$sdate = $_POST['sdate'];

	//챌린지 종료날짜
	$edate = $_POST['edate'];

	//챌린지 참여자
	$input_count = $_POST['input_count'];

	//챌린지 참여자(전체:all, 일부:sel)
	$user = $_POST['user'];

	//챌린지 선착순 제한
	$limit_cnt = $_POST['limit_count'];

	//챌린지 참여자 일부
	$chall_user_chk = $_POST['chall_user_chk'];

	//챌린지 코인
	$input_coin = $_POST['input_coin'];

	//챌린지 참여횟수 설정(한번)
	$ch_once = $_POST['ch_once'];

	//챌린지 참여횟수 설정(매일)
	$ch_daily = $_POST['ch_daily'];

	//챌린지 참여횟수 공휴일제외
	$ch_holiday = $_POST['ch_holiday'];

	//챌린지 참여자 설정(all:전체, sel:일부)
	$user = $_POST['user'];

	//챌린지 지급예상 전체코인
	$total_coin = $_POST['total_coin'];

	//키워드
	$write_keyword = $_POST['write_keyword'];

	// 템플릿 여부
	$template_enter = $_POST['template'];

	//챌린지 코인사용 안함(0:사용, 1:사용안함)
	$ch_not_coin = $_POST['ch_not_coin'];
	if($ch_not_coin == true){
		$coin_not = 1;
	}else{
		$coin_not = 0;
	}

	//챌린지 테마선택
	$chall_thema_chk = trim($_POST['chall_thema_chk']);
	$thema_idx = @explode(",",$chall_thema_chk);

	//챌린지 관리권한
	//if($template_auth == '1'){
	//테마를 등록 했을경우는 챌린지를 테마에 등록하기
	//테마를 등록 안하면 일반 챌린지로 등록하기
	// if($chall_thema_chk || $_COOKIE['chall_tpl'] =='1'){
	// 	$template = $template_auth;
	// }else{
	// 	$template = '0';
	// }

	if($template_enter == "1"){
		$template = '1';
		$user_name = "리워디";
	}else{
		$template = '0';
	}


	if($companyno){
		//$where = " and company='".$companyno."'";
	}


	$cate = preg_replace("/[^0-9]/", "", $cate);
	// $write_type = preg_replace("/[^0-9]/", "", $write_type);
	$temp_save = preg_replace("/[^0-9]/", "", $temp_save);
	$input_count = preg_replace("/[^0-9]/", "", $input_count);
	$input_coin = preg_replace("/[^0-9]/", "", $input_coin);
	$ch_holiday = preg_replace("/[^0-9]/", "", $ch_holiday);

	//임시저장여부
	if($temp_save =="1"){
		$temp_flag = "1";
	}else{
		$temp_flag = "0";
	}

	if($ch_once == 1){
		$day_type = "0";
	}else{
		if($ch_daily == 1){
			$day_type = "1";
		}
	}


	//챌린지제목
	//preg_match("/[\x{10000}-\x{10FFFF}]/u", $title, $ret);
	//챌린지 제목에 코칭이 있는경우 코칭으로 등록
	$coaching_chk = 0;
	if($title){
		if(strpos($title, "코칭") !== false) {
			$coaching_chk = 1;
		}
	}

	//개행문자가 있을때 nl2br처리
	if(strpos($title, "\n") !== false) {
		$title = nl2br($title);
	}

	//$title = urlencode($title);

	//챌린지 참여자 설정(all:전체, sel:일부)
	//참여자대상 -일부만
	if($user == "all"){
		//참여자 전체
		$attend_chk = "0";
		$sql = "select idx, email, name, highlevel, part, partno from work_member where state='0' and companyno='".$companyno."' and highlevel!='1'";
		$chall_mem_info = selectAllQuery($sql);

	}else if($user == "sel"){
		//참여자 일부
		$attend_chk = "1";
		//$work_mem_idx = @implode("','",$chall_user_chk);
		$work_mem_idx = trim($chall_user_chk);
		$sql = "select idx, email, name, highlevel, part, partno from work_member where state='0' and companyno='".$companyno."' and idx in (".$work_mem_idx.")";
		$chall_mem_info = selectAllQuery($sql);


		if (count($chall_mem_info['idx']) == 1){
			if($user_id == $chall_mem_info['email'][0]){
				echo "usernot";
				exit;
			}
		}
	}

	if($chall_idx){
		$sql = "select idx,total_max_coin,email,coin from work_challenges where idx='".$chall_idx."' and email='".$user_id."'";
		$chall_info = selectQuery($sql);
		if($chall_info['idx']){

			$sql = "update work_challenges set attend_type='".$write_type."', cate='".$cate."', title='".$title."', temp_flag='".$temp_flag."'";
			$sql = $sql .= " , type_flag='".$type_flag."',sdate='".$sdate."', edate='".$edate."', attend='".$input_count."', day_type='".$day_type."',holiday_chk='".$ch_holiday."', attend_chk='".$attend_chk."', view_flag='".$view_flag."', coin='".$input_coin."', coin_not='".$coin_not."',coaching_chk='".$coaching_chk."'";
			$sql = $sql .= " , companyno='".$companyno."', total_max_coin = '".$total_coin."', limit_count = '".$limit_cnt."' ";

			if($write_keyword){
				$sql = $sql .= " , keyword='".$write_keyword."'";
			}
			//임시저장된 챌린지 등록완료시 등록일자 갱신처리
			if($temp_flag == 1){
				$sql = $sql .= ", regdate=".DBDATE."";
			}

			$sql = $sql .= " where idx='".$chall_info['idx']."'";
			$res = updateQuery($sql);
			$res_idx = $chall_info['idx'];

			$sql = "select coin from work_coininfo where work_idx = '".$res_idx."' and email = '".$user_id."' ";
			$coin_info = selectQuery($sql);
			$update_coin_info = $total_coin - $chall_info['total_max_coin']; // 새로 수정한 코인 - 기존에 등록된 코인

			$sql = "select comcoin from work_member where email = '".$chall_info['email']."'";
			$user_comcoin = selectQuery($sql);

			if($update_coin_info > 0){
				$updateCoin = $user_comcoin - $update_coin_info;
				$sql = "update work_coininfo set coin = '".$total_coin."' where work_idx = '".$res_idx."' and code = '530'";
				$cos = updateQuery($sql);

				$sql = "update work_member set comcoin = '".$updateCoin."' where email = '".$chall_info['email']."' and state = '0'";
				$cos2 = updateQuery($sql);
			}			
		}
	}else{

		$sql = "insert into work_challenges(email, name, attend_type, cate, title, temp_flag, type_flag, sdate, edate, template, attend, day_type, holiday_chk, attend_chk, view_flag, coin, coin_not, coaching_chk, companyno, ip, keyword, total_max_coin, limit_count)";
		$sql = $sql .= " values('".$user_id."','".$user_name."', '".$write_type."', '".$cate."', '".$title."','".$temp_flag."','".$type_flag."'";
		$sql = $sql .= ", '".$sdate."','".$edate."','".$template."','".$input_count."', '".$day_type."','".$ch_holiday."','".$attend_chk."','".$view_flag."','".$input_coin."','".$coin_not."','".$coaching_chk."','".$companyno."','".LIP."','".$write_keyword."','".$total_coin."', '".$limit_cnt."')";
		$res_idx = insertIdxQuery($sql);

		$sql = "insert into work_coininfo(state, code, work_idx, reward_type, companyno, email, name, coin, memo, workdate, ip)";
		$sql = $sql .= " values('0','530','".$res_idx."','challenge', '".$companyno."', '".$user_id."', '".$user_name."', '".$total_coin."', '챌린지 개설로 인한 전체예상지급 코인 차감','".TODATE."','".LIP."')";
		$total_insert = insertQuery($sql);

		$sql = "select comcoin from work_member where state = '0' and email = '".$user_id."' ";
		$user_coin = selectQuery($sql);

		$up_coin = $user_coin['comcoin'] - $total_coin;

		$sql = "update work_member set comcoin = '".$up_coin."' where state = '0' and email = '".$user_id."'";
		$update_usercoin = updateQuery($sql);
		
	}


	//등록된 idx값체크
	if($res_idx){
		$kind = "2"; //챌린지

		//챌린지 내용저장
		$sql = "select idx from work_contents where state='0' and kind='2' and work_idx='".$res_idx."'";
		$content_info = selectQuery($sql);
		if(!$content_info['idx']){
			$sql = "insert into work_contents( kind, work_idx, companyno, contents) values('".$kind."','".$res_idx."','".$companyno."','".$contents."')";
			$res = insertQuery($sql);
		}


		//기존 테마설정이 있을경우 삭제처리
		$sql = "select idx from work_challenges_thema_list where state='0' and companyno='".$companyno."' and challenges_idx='".$res_idx."'";
		$thema_info = selectQuery($sql);
		if($thema_info['idx']){
			$sql = "update work_challenges_thema_list set state='9' where state='0' and companyno='".$companyno."' and challenges_idx='".$res_idx."'";
			$up = updateQuery($sql);
		}

		//챌린지 테마등록
		if($thema_idx){
			for($i=0; $i<count($thema_idx); $i++){
				$sql = "select idx from work_challenges_thema_list where state='0' and companyno='".$companyno."' and challenges_idx='".$res_idx."' and thema_idx='".$thema_idx[$i]."'";
				$thema_list_info = selectQuery($sql);
				if(!$thema_list_info['idx']){
					$sql = "insert into work_challenges_thema_list(challenges_idx, thema_idx, companyno,ip) values('".$res_idx."','1','".$companyno."','".LIP."')";
					$res = insertQuery($sql);
				}
			}
		}

		$nl2br_arr = array("<br />","<br/>","<br/><br />");
		$title2 = str_replace($nl2br_arr,"",$title);
		//참여자등록
		for($i=0; $i<count($chall_mem_info['idx']); $i++){
			$sql = "select idx from work_challenges_user where state='0' and challenges_idx='".$res_idx."' and companyno='".$companyno."' and email='".$chall_mem_info['email'][$i]."'";
			$chall_row = selectQuery($sql);

			if(!$chall_row['idx']){
				$sql = "insert into work_challenges_user (challenges_idx,email,name,companyno,ip) values('".$res_idx."','".$chall_mem_info['email'][$i]."','".$chall_mem_info['name'][$i]."','".$companyno."','".LIP."')";
				$res = insertQuery($sql);
			}

			//임시저장이 아닌것만 오늘업무에 알림으로 설정
			//챌린지 템플릿이 아닌것만 알림설정
			if($temp_flag=="0" && $template == "0"){
				//챌린지 등록시 오늘업무에 등록 처리
				//공지 테이블 work_notice : 1:챌린지알림
				$notice_flag = '1';
				$sql = "select idx from work_todaywork where state='0' and work_flag='2' and notice_flag='".$notice_flag."' and email='".$chall_mem_info['email'][$i]."' and work_idx='".$res_idx."' and workdate='".TODATE."'";
				$todaywork_info = selectQuery($sql);

				$alarmtitle = "챌린지";
				$memo = "[".$title2."]";
				pushToken($alarmtitle,$memo,$chall_mem_info['email'][$i],'challenge','26',$user_id,$user_name,$res_idx);

				//매일참여
				if ($day_type == '1'){
					$cal_coin = $input_coin * $input_count;
					$work_contents = $title . " (최대 ".number_format($cal_coin). " 코인 획득 가능)";
				}else{
					$cal_coin = $input_coin;
					$work_contents = $title . " (".number_format($cal_coin). " 코인 획득 가능)";
				}

				if(!$todaywork_info['idx']){
					$sql = "insert into work_todaywork(highlevel, work_flag, part_flag, part, type_flag, notice_flag, work_idx, email, name, contents, companyno, ip, workdate) ";
					$sql = $sql .= " values('".$chall_mem_info['highlevel'][$i]."', '2', '".$chall_mem_info['partno'][$i]."' ,'".$chall_mem_info['part'][$i]."', '".$type_flag."', '".$notice_flag."', '".$res_idx."', '".$chall_mem_info['email'][$i]."', '".$chall_mem_info['name'][$i]."', '".$work_contents."','".$companyno."','".LIP."','".TODATE."')";
					$work_insert = insertQuery($sql);
				}
			}

		}

		//파일체크
		$file_upload_check = false;
				
		//파일갯수
		$file_upload_cnt = count($_FILES['files']['name']);

		//파일갯수
		$fileimg_upload_cnt = count($_FILES['files_img']['name']);

		if ($file_upload_cnt > 0 ){

			//파일첨부 여부
			$file_upload_check = true;


			for($i=0; $i<$file_upload_cnt; $i++){

				$filename = $_FILES['files']['name'][$i];
				$ext = array_pop(explode(".", strtolower($filename)));

				//허용확장자체크
				if( !in_array($ext, $file_allowed_ext) ) {
					//echo "ext_file1";
					//exit;
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
				$renamefile = "{$datetime}_{$rand_id}_challenges_{$res_idx}.{$ext}";

				//년도
				$dir_year = date("Y", TODAYTIME);

				//월
				$dir_month = date("m", TODAYTIME);



				//업로드 디렉토리 - /data/회사고유번호/회사폴더/challenges/files/년/월/
				$upload_path = $dir_file_path."/".$file_save_dir."/".$dir_year."/".$dir_month."/";
				$upload_path = str_replace($file_save_dir , "data/".$companyno."/".$comfolder."/"."challenges/files" , $upload_path);

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

					$sql = "select idx from work_filesinfo_file where state='0' and work_idx='".$res_idx."' and email='".$user_id."' and num='".$i."'";
					$fileinfo = selectQuery($sql);


					if ($fileinfo['idx']){
						
						$sql = "update work_filesinfo_file set file_path='".$file_path."', file_name='".$renamefile."', file_size='".$file_size."', file_real_name='".$file_real_name."',file_type='".$file_type."' where num='".$i."' and idx='".$fileinfo['idx']."'";
						$res = updateQuery($sql);

					}else{
						$sql = "insert into work_filesinfo_file(work_idx, email, num, kind, file_path, file_name, file_size, file_real_name, file_type, companyno, ip) values(";
						$sql = $sql .="'".$res_idx."', '".$user_id."', '".$i."','".$kind."', '".$file_path."', '".$renamefile."', '".$file_size."', '".$file_real_name."', '".$file_type."','".$companyno."', '".LIP."')";
						//$res_file = insertQuery($sql);
						$files_idx = insertIdxQuery($sql);

						//if($files_idx){
						//	$sql = "update work_challenges set files_idx='".$files_idx."', files_name='".$renamefile."' where state='0' and idx='".$res_idx."'";
						//	updateQuery($sql);
						//}
					}

					//파일갯수
					//if ( count($fileinfo['idx']) < 4){
					//}
				}
			}
		}


		//첨부파일 이미지
		if ($fileimg_upload_cnt > 0){

			//파일첨부 여부
			$file_upload_check = true;

			//파일순번
			$file_img_num = 1;
			for($i=0; $i<$fileimg_upload_cnt; $i++){


				//파일확장자 추출
				$filename = $_FILES['files_img']['name'][$i];
				$ext = array_pop(explode(".", strtolower($filename)));

				//허용확장자체크
				if( !in_array($ext, $img_file_allowed_ext) ) {
					echo "ext_file2";
					exit;
				}

				//파일타입
				$file_type = $_FILES['files_img']['type'][$i];

				//파일사이즈
				$file_size = $_FILES['files_img']['size'][$i];

				//임시파일명
				$file_tmp_name = $_FILES['files_img']['tmp_name'][$i];

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
				$renamefile = "{$datetime}_{$rand_id}_challenges_{$res_idx}.{$ext}";

				//년도
				$dir_year = date("Y", TODAYTIME);

				//월
				$dir_month = date("m", TODAYTIME);

				//업로드 디렉토리 - /data/회사고유번호/회사랜덤폴더/challenges/files/년/월/
				$upload_path = $dir_file_path."/".$file_save_dir_img."/".$dir_year."/".$dir_month."/";
				$upload_path = str_replace($file_save_dir_img , "data/".$companyno."/".$comfolder."/"."challenges/img" , $upload_path);

				//업로드 디렉토리 - /data/회사고유번호/회사랜덤폴더/challenges/files/년/월/
				$upload_path_ori = $dir_file_path."/".$file_save_dir_img_ori."/".$dir_year."/".$dir_month."/";
				$upload_path = str_replace($file_save_dir_img_ori , "data/".$companyno."/".$comfolder."/"."challenges/img_ori" , $upload_path_ori);

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


				$new_file_width = 250; //이미지 가로 사이즈 지정
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

					$sql = "select idx from work_filesinfo_img where state='0' and work_idx='".$res_idx."' and email='".$user_id."' and companyno='".$companyno."' and num='".$i."'";
					$fileinfo = selectQuery($sql);

					if($fileinfo['idx']){

						$sql = "update work_filesinfo_img set resize='".$resize_val."', file_path='".$rezie_file_path."', file_name='".$rezie_renamefile."', file_size='".$resize_file."', file_ori_path='".$file_path_ori."', file_ori_name='".$renamefile."', file_ori_size='".$file_size."', file_real_name='".$file_real_name."',file_type='".$file_type."' where num='".$i."' and idx='".$fileinfo['idx']."'";
						$res = updateQuery($sql);

					}else{

						$sql = "insert into work_filesinfo_img(work_idx, email, num, kind, resize, file_path, file_name, file_size, file_ori_path, file_ori_name, file_ori_size, file_real_name, file_type, companyno, ip) values(";
						$sql = $sql .="'".$res_idx."','".$user_id."','".$i."','".$kind."','".$resize_val."','".$rezie_file_path."','".$rezie_renamefile."','".$resize_file."','".$file_path_ori."','".$renamefile."','".$file_size."','".$file_real_name."','".$file_type."','".$companyno."','".LIP."')";
						//$res_file = insertQuery($sql);
						$files_idx = insertIdxQuery($sql);

						//if($files_idx){
						//	$sql = "update work_challenges set files_idx='".$files_idx."', files_name='".$renamefile."' where state='0' and idx='".$res_idx."'";
						//	updateQuery($sql);
						//}

					}

					//if ( count($fileinfo['idx']) < 7){
					//}

				}else{
					echo "file_not";
					exit;
				}
			}
		}

		//temp_flag(임시저장:1, 0:등록완료)
		if($temp_flag == 1){
			
			//타임라인 메모 추가
			work_data_log('0','27', $res_idx, $user_id, $user_name);

			echo "temp_complete|".$res_idx;
		}else{

			//역량평가지표(챌린지 만들기)
			work_cp_reward("challenge", "0001", $user_id, $res_idx);

			main_like_cp_works('challenges_create');

			//타임라인 메모 추가
			work_data_log('0','26', $res_idx, $user_id, $user_name);

			echo "complete|".$res_idx;
			
		}
	}

	exit;
}




//챌린지 참여취소하기
if($mode == "challenges_cancel"){

	$code = "500";
	$code1 = "501";
	$state = "1";
	$memo = "챌린지 참여 취소";
	$chall_idx = $_POST['idx'];
	$chall_idx = preg_replace("/[^0-9]/", "", $chall_idx);

	if($chall_idx){

		//챌린지 확인
		$sql = "select idx, attend_type, email, name, coin, title, sdate, edate, day_type, outputchk from work_challenges where state='0' and idx='".$chall_idx."'";
		$challenges_info = selectQuery($sql);
		if($challenges_info['idx']){

			//메시지형
			if($challenges_info['attend_type'] == "1"){

				$sql = "select idx from work_challenges_result where state='1' and challenges_idx='".$chall_idx."' and comment!='' and email='".$user_id."' and DATE_FORMAT(comment_regdate, '%Y-%m-%d') ='".TODATE."'";
				$chall_comment_info = selectQuery($sql);
				if($chall_comment_info['idx']){

					//챌린지 메시지형 참여 취소
					$sql = "update work_challenges_result set state='9' where state='1' and challenges_idx='".$chall_idx."' and companyno='".$companyno."' and email='".$user_id."'";
					$up1 = updateQuery($sql);

					$sql = "select idx, coin, reward_user, reward_name, reward_type, auth_comment_idx from work_coininfo where state='0' and companyno='".$companyno."' and code='".$code."' and reward_type='".$reward_type[2]."' and work_idx='".$chall_idx."' and email='".$user_id."' and workdate ='".TODATE."'";
					$reward_info = selectQuery($sql);
					if ($reward_info['idx']){

						//$sql = "update work_coininfo set state='0', code='".$ch_code[1]."' where idx='".$reward_info['idx']."'";
						//$up2 = updateQuery($sql);

						$reward_user = $reward_info['reward_user'];
						$reward_name = $reward_info['reward_name'];
						$sql = "insert into work_coininfo(code, work_idx, reward_type, auth_comment_idx, companyno, email, name, reward_user, reward_name, coin, memo, workdate, ip)";
						$sql = $sql .= " values('".$ch_code[1]."','".$chall_idx."','".$reward_type[2]."', '".$chall_comment_info['idx']."', '".$companyno."', '".$user_id."', '".$user_name."','".$reward_user."', '".$reward_name."', '".$reward_info['coin']."', '".$memo."','".TODATE."','".LIP."')";
						$up2 = insertQuery($sql);


						//챌린지 코인 회수
						$sql = "select idx, highlevel, coin from work_member where state='0' and companyno='".$companyno."' and email='".$user_id."'";
						$mem_info = selectQuery($sql);
						if($mem_info['idx']){
							$sql = "update work_member set coin = coin - '".$reward_info['coin']."' where state='0' and idx='".$mem_info['idx']."'";
							$up3 = updateQuery($sql);
						}
					}

					if($up1 && $up2 && $up3){
						echo "complete";
						exit;
					}
				}
			//파일첨부형
			}else if($challenges_info['attend_type'] == "2"){
				$sql = "select idx from work_challenges_result where state='1' and companyno='".$companyno."' and challenges_idx='".$chall_idx."' and file_path!='' and file_name!='' and email='".$user_id."' and DATE_FORMAT(file_regdate, '%Y-%m-%d') ='".TODATE."'";
				$chall_file_info = selectQuery($sql);
				if($chall_file_info['idx']){

					//챌린지 메시지형 참여 취소
					$sql = "update work_challenges_result set state='9' where state='1' and companyno='".$companyno."' and challenges_idx='".$chall_idx."' and email='".$user_id."'";
					$up1 = updateQuery($sql);

					$sql = "select idx, coin, reward_user, reward_name, reward_type, auth_file_idx from work_coininfo where state='0' and code='".$ch_code[0]."' and reward_type='".$reward_type[2]."' and work_idx='".$chall_idx."' and companyno='".$companyno."' and email='".$user_id."' and workdate='".TODATE."'";
					$reward_info = selectQuery($sql);
					if ($reward_info['idx']){

						//$sql = "update work_coininfo set state='0', code='".$ch_code[1]."' where idx='".$reward_info['idx']."'";
						//$up2 = updateQuery($sql);

						$reward_user = $reward_info['reward_user'];
						$reward_name = $reward_info['reward_name'];
						$sql = "insert into work_coininfo(code, work_idx, reward_type, auth_file_idx, companyno, email, name, reward_user, reward_name, coin, memo, workdate, ip)";
						$sql = $sql .= " values('".$ch_code[1]."','".$chall_idx."','".$reward_type[2]."', '".$chall_file_info['idx']."', '".$companyno."', '".$user_id."', '".$user_name."','".$reward_user."', '".$reward_name."', '".$reward_info['coin']."', '".$memo."','".TODATE."','".LIP."')";
						$up2 = insertQuery($sql);


						//챌린지 코인 회수
						$sql = "select idx, coin,comcoin from work_member where state='0' and companyno='".$companyno."' and email='".$user_id."'";
						$mem_info = selectQuery($sql);
						if($mem_info['idx']){
							$sql = "update work_member set coin = coin - '".$reward_info['coin']."' where state='0' and idx='".$mem_info['idx']."'";
							$up3 = updateQuery($sql);
						}
					}

					if($up1 && $up2 && $up3){
						echo "complete";
						exit;
					}
				}
			//혼합형
			}else if($challenges_info['attend_type'] == "3"){


				$sql = "select idx from work_challenges_result where state='1' and challenges_idx='".$chall_idx."' and comment!='' and file_path!='' and file_name!='' and companyno='".$companyno."' and email='".$user_id."' and DATE_FORMAT(file_regdate, '%Y-%m-%d') ='".TODATE."'";
				$chall_comment_info = selectQuery($sql);
				if($chall_comment_info['idx']){
					//챌린지 메시형 참여 취소
					$sql = "update work_challenges_result set state='9' where state='1' and challenges_idx='".$chall_idx."' and companyno='".$companyno."' and email='".$user_id."'";
					$up1 = updateQuery($sql);

					if($up1){

						$sql = "select idx, coin, reward_user, reward_name, reward_type, auth_comment_idx from work_coininfo where state='0' and code='".$code."' and reward_type='".$reward_type[2]."' and work_idx='".$chall_idx."' and companyno='".$companyno."' and email='".$user_id."' and workdate='".TODATE."'";
						$reward_info = selectQuery($sql);
						if ($reward_info['idx']){
							$reward_user = $reward_info['reward_user'];
							$reward_name = $reward_info['reward_name'];
							$sql = "insert into work_coininfo(code, work_idx, reward_type, auth_comment_idx, companyno, email, name, reward_user, reward_name, coin, memo, workdate, ip)";
							$sql = $sql .= " values('".$ch_code[1]."','".$chall_idx."','".$reward_type[2]."', '".$chall_comment_info['idx']."', '".$companyno."', '".$user_id."', '".$user_name."','".$reward_user."', '".$reward_name."', '".$reward_info['coin']."', '".$memo."','".TODATE."','".LIP."')";
							$up2 = insertQuery($sql);
		
							//챌린지 코인 회수
							$sql = "select idx, coin,comcoin from work_member where state='0' and companyno='".$companyno."' and email='".$user_id."'";
							$mem_info = selectQuery($sql);
							if($mem_info['idx']){
								$sql = "update work_member set coin = coin - '".$reward_info['coin']."' where state='0' and companyno='".$companyno."' and idx='".$mem_info['idx']."'";
								$up3 = updateQuery($sql);
							}
						}
						if($up1 && $up2 && $up3){
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


//챌린지참여 메시지형
if($mode == "challenges_message"){

	$idx = $_POST['idx'];
	$chamyeo_idx = $_POST['chamyeo_idx'];
	$message = $_POST['message'];
	$message = nl2br($message);

	$idx = preg_replace("/[^0-9]/", "", $idx);
	$chamyeo_idx = preg_replace("/[^0-9]/", "", $chamyeo_idx);
	if($idx){

		//챌린지 확인(정상적인 챌린지: state=0)
		$sql = "select idx, attend_type, cate, email, name, coin, coin_not, title, sdate, edate, day_type, attend, outputchk from work_challenges where state='0' and idx='".$idx."'";
		$challenges_info = selectQuery($sql);
		if($challenges_info['idx']){

			$code = "500";
			$coin = $challenges_info['coin'];
			$memo = "챌린지 참여 보상";

			$reward_user = $challenges_info['email'];
			$reward_name = $challenges_info['name'];
			$chall_cate = $challenges_info['cate'];
			$chall_coin_not = $challenges_info['coin_not'];
			$chall_title = $challenges_info['title'];
			$category_act = challenges_category();


			//부서별정보
			if($user_part){
				$sql = "select idx, partname from work_team where state='0' and idx='".$user_part."'";
				$team_info = selectQuery($sql);
				if ($team_info['idx']){
					$partname = $team_info['partname'];
				}
			}


			//챌린지 참여 기간내에 참여 할수 있도록 처리
			if( TODATE >= $challenges_info['sdate'] && TODATE <= $challenges_info['edate'] ){

				//챌린지 참여자체크
				$sql = "select idx, state from work_challenges_user where state='0' and challenges_idx='".$idx."' and companyno='".$companyno."' and email='".$user_id."'";
				$chall_user_info = selectQuery($sql);
				if(!$chall_user_info['idx']){
					echo "ch_notuser";
					exit;
				}


				//메시지형
				if($attend_type == "1"){

					//참여가능기간
					$where = " and DATE_FORMAT(comment_regdate, '%Y-%m-%d')>='".$challenges_info['sdate']."' and DATE_FORMAT(comment_regdate, '%Y-%m-%d')<='".$challenges_info['edate']."'";

					//챌린지 참여 횟수가 한번만 참여
					if ($challenges_info['day_type'] == "0"){

						$sql = "select count(idx) as cnt from work_challenges_result where state='1' and challenges_idx='".$idx."' and comment!='' and  companyno='".$companyno."' and email='".$user_id."'".$where."";
						$chall_comment_info = selectQuery($sql);
						if($chall_comment_info['cnt'] >= 1){
							echo "chamyeo|";
							exit;
						}
					//챌린지 참여 횟수 매일참여
					}else if ($challenges_info['day_type'] == "1"){

						//하루 한번 참여가능
						$sql = "select idx from work_challenges_result where state='1' and challenges_idx='".$idx."' and comment!='' and companyno='".$companyno."' and email='".$user_id."' and DATE_FORMAT(comment_regdate, '%Y-%m-%d')='".TODATE."'";
						$chall_comment_info = selectQuery($sql);
						if($chall_comment_info['idx']){
							//참여완료
							echo "chamyeo|";
							exit;
						}

						//챌린지 참여회수 체크
						$sql = "select count(idx) as cnt from work_challenges_result where state='1' and challenges_idx='".$idx."' and comment!='' and companyno='".$companyno."' and email='".$user_id."'".$where."";
						$chall_comment_info = selectQuery($sql);
						if ($chall_comment_info['cnt'] >= $challenges_info['attend']){
							echo "chamyeo_max|".$challenges_info['attend'];
							exit;
						}
					}

					//챌린지 도전중 일때 참여 처리 하기
					$sql = "select idx from work_challenges_result where state='0' and challenges_idx='".$idx."' and comment is null and companyno='".$companyno."' and email='".$user_id."' and idx='".$chamyeo_idx."'";
					$chall_comment_info = selectQuery($sql);
					if($chall_comment_info['idx']){
						
						//$message = urlencode($message);
						$sql = "update work_challenges_result set attend_type='".$attend_type."', state='1', comment='".$message."', type_flag='".$type_flag."', comment_regdate=".DBDATE." where idx='".$chall_comment_info['idx']."'";
						$res_idx = updateQuery($sql);
						if($res_idx){

							//회원정보 - 정상일때
							$sql = "select idx from work_member where state='0' and companyno='".$companyno."' and email='".$user_id."'";
							$mem_info = selectQuery($sql);
							if($mem_info['idx']){

								//챌린지 참여로 지급받은 내역있는지 체크
								$sql = "select idx from work_coininfo where state='0' and companyno='".$companyno."' and code='".$ch_code[0]."' and work_idx='".$idx."' and reward_type='".$reward_type[2]."' and auth_comment_idx='".$chall_comment_info['idx']."' and workdate='".TODATE."'";
								$reward_info = selectQuery($sql);
								if (!$reward_info['idx']){


									//챌린지 작성자 코인 차감
									$sql = "select coin, highlevel, comcoin from work_member where state='0' and  companyno='".$companyno."' and email='".$challenges_info['email']."' order by idx desc limit 1";
									$mem_info_coin = selectQuery($sql);
									if($mem_info_coin['coin']){

										if($chall_coin_not!='1'){
											//작성한자의 코인이 지급할 코인보다 클경우만
											if($mem_info_coin['coin'] && $mem_info_coin['coin'] >= $coin){

												$memo_min = "챌린지 참여 보상으로 차감";
												$sql = "insert into work_coininfo(code, work_idx, reward_type, auth_comment_idx, companyno, email, name, reward_user, reward_name, coin, memo, workdate, ip)";
												$sql = $sql .= " values('".$ch_code[2]."','".$idx."', '".$reward_type[2]."', '".$chall_comment_info['idx']."', '".$companyno."', '".$reward_user."', '".$reward_name."','".$user_id."', '".$user_name."', '".$coin."', '".$memo_min."','".TODATE."','".LIP."')";
												$coin_min_info = insertQuery($sql);
												if($coin_min_info){

													//관리자권한
													if($mem_info_coin['highlevel']=='0'){
														//챌린지 작성자 코인 차감
														$sql = "update work_member set comcoin = comcoin - '".$coin."' where state='0' and companyno='".$companyno."' and email='".$reward_user."'";
														$up = updateQuery($sql);
													//일반사용자
													}else if($mem_info_coin['highlevel']=='5'){
														//챌린지 작성자 코인 차감
														$sql = "update work_member set comcoin = comcoin - '".$coin."' where state='0' and companyno='".$companyno."' and email='".$reward_user."'";
														$up = updateQuery($sql);
													}


													//챌린지 참여자 코인 지급내역저장
													$sql = "insert into work_coininfo(code, work_idx, reward_type, auth_comment_idx, companyno, email, name, reward_user, reward_name, coin, memo, workdate, ip)";
													$sql = $sql .= " values('".$ch_code[0]."','".$idx."', '".$reward_type[2]."', '".$chall_comment_info['idx']."', '".$companyno."', '".$user_id."', '".$user_name."','".$reward_user."', '".$reward_name."', '".$coin."', '".$memo."','".TODATE."','".LIP."')";
													$coin_add_info = insertIdxQuery($sql);
													
													//챌린지 참여자 코인 지급
													$sql = "update work_member set coin = coin + '".$coin."' where state='0' and companyno='".$companyno."' and email='".$user_id."'";
													$up = updateQuery($sql);

													$tokenTitle = "챌린지 참여 보상";
													$tokenComment = "[".$chall_title."]보상으로 ".$coin."코인을 지급 받았습니다.";

													pushToken($tokenTitle,$tokenComment,$user_id,'reward','21','none','none',$idx);

													//챌린지 참여시 라이브ON 상태변경
													$sql = "select idx from work_member where state='0' and live_1='0' and live_1_regdate is null and companyno='".$companyno."' and email='".$user_id."'";
													$mem_live_info = selectQuery($sql);
													if($mem_live_info['idx']){
														$sql = "update work_member set live_1='1', live_1_regdate=".DBDATE." where idx='".$mem_live_info['idx']."'";
														$live_up = updateQuery($sql);
													}
												}
											//코인이 없을때 
											}else{

											}
										}
									}else{

										echo "not_coin";
									}



									//코인사용일 경우
									if($chall_coin_not!='1'){
										if($coin_add_info && $up){

											//타임라인(챌린지도전완료)
											work_data_log('0','14', $idx, $user_id, $user_name);

											//역량평가지표(지식:0001, 성과:0002, 성장:0003, 협업:0004, 성실:0005, 실행:0006)
											if($category_act['act'][$chall_cate]){
												work_cp_reward("challenge", $category_act['act'][$chall_cate], $user_id, $idx);
											}

											//타임라인(코인적립)
											work_data_log('0','15', $coin_add_info, $user_id, $user_name);

											echo "complete";
											exit;
										}
									}else{
										//코인사용안함 일경우
										//역량평가지표(지식:0001, 성과:0002, 성장:0003, 협업:0004, 성실:0005, 실행:0006)
										if($category_act['act'][$chall_cate]){
											work_cp_reward("challenge", $category_act['act'][$chall_cate], $user_id, $idx);

											//타임라인(챌린지도전완료)
											work_data_log('0','14', $idx, $user_id, $user_name);

											echo "complete";
											exit;
										}
									}
								}
							}
						}else{
							echo "update_err";
							exit;
						}
					}
				}
			}else{
				echo "day_expire";
				exit;
			}
		}
	}
	exit;
}


//챌린지참여 파일첨부형
if($mode == "challenges_file"){

	$idx = $_POST['idx'];
	$chll_idx = preg_replace("/[^0-9]/", "", $idx);
	$chamyeo_idx = $_POST['chamyeo_idx'];
	$chamyeo_idx = preg_replace("/[^0-9]/", "", $chamyeo_idx);
	$attend_type = $_POST['attend_type'];

	if($chll_idx){

		//챌린지 확인
		$sql = "select idx, attend_type, cate, email, name, coin, coin_not, title, sdate, edate, day_type, attend, outputchk from work_challenges where state='0' and companyno='".$companyno."' and idx='".$chll_idx."'";
		$challenges_info = selectQuery($sql);
		if($challenges_info['idx']){
			$coin = $challenges_info['coin'];
			$memo = "챌린지 참여 보상";
			$reward_user = $challenges_info['email'];
			$reward_name = $challenges_info['name'];
			$chall_cate = $challenges_info['cate'];
			$chall_coin_not = $challenges_info['coin_not'];
			$chall_title = $challenges_info['title'];
			$category_act = challenges_category();

			/*print "<pre>";
			print_r($_POST);
			print_r($_FILES);
			print "</pre>";*/

			//챌린지 참여 기간내에 참여 할수 있도록 처리
			if( TODATE >= $challenges_info['sdate'] && TODATE <= $challenges_info['edate'] ){

				//참여가능기간
				$where = " and DATE_FORMAT(file_regdate, '%Y-%m-%d')>='".$challenges_info['sdate']."' and DATE_FORMAT(file_regdate, '%Y-%m-%d')<='".$challenges_info['edate']."'";
				//챌린지 참여 횟수가 한번만 참여
				if ($challenges_info['day_type'] == "0"){

					$sql = "select count(idx) as cnt from work_challenges_result where state='1' and challenges_idx='".$chll_idx."' and file_path!='' and file_name!='' and companyno='".$companyno."' and email='".$user_id."'".$where."";
					$chall_file_info = selectQuery($sql);
					if($chall_file_info['cnt'] >= 1){
						echo "chamyeo|";
						exit;
					}

				}else if ($challenges_info['day_type'] == "1"){

					//하루 한번 참여가능
					$sql = "select idx from work_challenges_result where state='1' and challenges_idx='".$chll_idx."' and file_path!='' and file_name!='' and companyno='".$companyno."' and email='".$user_id."' and DATE_FORMAT(file_regdate, '%Y-%m-%d')='".TODATE."'";
					$chall_file_info = selectQuery($sql);
					if($chall_file_info['idx']){
						//참여완료
						echo "chamyeo|";
						exit;
					}

					//챌린지 참여회수 체크
					$sql = "select count(idx) as cnt from work_challenges_result where state='1' and challenges_idx='".$chll_idx."' and file_path!='' and file_name!='' and companyno='".$companyno."' and email='".$user_id."'".$where."";
					$chall_file_info = selectQuery($sql);
					if ($chall_file_info['cnt'] >= $challenges_info['attend']){
						echo "chamyeo_max|".$challenges_info['attend'];
						exit;
					}
				}

				//첨부한 파일이 있을경우
				if($_FILES){

					//파일타입(이미지파일:true, 일반파일:false)
					$file_type_img = false;

					//파일명
					$filename = $_FILES['files']['name'][0];
					$ext = array_pop(explode(".", strtolower($filename)));

					//이미지파일 허용확장자
					if(in_array($ext , $img_file_allowed_ext)){
						$file_type_img = true;
					}

					//부서별정보
					if($user_part){
						$sql = "select idx, partname from work_team where state='0' and companyno='".$companyno."' and idx='".$user_part."'";
						$team_info = selectQuery($sql);
						if ($team_info['idx']){
							$partname = $team_info['partname'];
						}
					}


					//이미지 처리부분
					if($file_type_img){

						//파일순번
						$file_img_num = 1;

						//파일확장자 추출
						$filename = $_FILES['files']['name'][0];
						$ext = array_pop(explode(".", strtolower($filename)));

						//허용확장자체크
						if( !in_array($ext, $img_file_allowed_ext) ) {
							echo "ext_file";
							exit;
						}

						//파일타입
						$file_type = $_FILES['files']['type'][0];

						//파일사이즈
						$file_size = $_FILES['files']['size'][0];

						//임시파일명
						$file_tmp_name = $_FILES['files']['tmp_name'][0];

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

						//업로드 디렉토리 - /data/회사고유번호/회사랜덤폴더/challenges/files/년/월/
						$upload_path = $dir_file_path."/".$file_save_dir_img."/".$dir_year."/".$dir_month."/";
						$upload_path = str_replace($file_save_dir_img , "data/".$companyno."/".$comfolder."/"."challenges/img" , $upload_path);



						//업로드 디렉토리 - /data/회사고유번호/회사랜덤폴더/challenges/files/년/월/
						$upload_path_ori = $dir_file_path."/".$file_save_dir_img_ori."/".$dir_year."/".$dir_month."/";
						$upload_path_ori = str_replace($file_save_dir_img_ori , "data/".$companyno."/".$comfolder."/"."challenges/img" , $upload_path_ori);

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
						//	$degrees = "-90";
						//	$image = imagerotate($image, $degrees, 0);

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

							//참여완료하기
							$sql = "select idx from work_challenges_result where state='0' and challenges_idx='".$chll_idx."' and companyno='".$companyno."' and email='".$user_id."' and idx='".$chamyeo_idx."'";
							$chall_file_info = selectQuery($sql);
							if($chall_file_info['idx']){


								$sql = "update work_challenges_result set attend_type='".$attend_type."', state='1', resize='".$resize_val."', file_path='".$file_path."', file_name='".$renamefile."', file_size='".$file_size."',file_ori_path='".$file_path_ori."', file_ori_name='".$renamefile."', file_ori_size='".$file_size."', file_real_img_name='".$file_real_name."' , file_type='".$file_type."', file_regdate=".DBDATE." where challenges_idx='".$chll_idx."' and companyno='".$companyno."' and idx='".$chall_file_info['idx']."'";
								write_log($sql);
								$res_idx = updateQuery($sql);

								if($res_idx){

									//회원정보 - 정상
									$sql = "select idx from work_member where state='0' and companyno='".$companyno."' and email='".$user_id."'";
									$mem_info = selectQuery($sql);
									if($mem_info['idx']){

										//챌린지에서 지급받은 내역있는 체크
										$sql = "select idx from work_coininfo where state='0' and companyno='".$companyno."' and code='".$ch_code[0]."' and work_idx='".$idx."' and reward_type='".$reward_type[2]."' and auth_file_idx='".$chall_file_info['idx']."' and workdate='".TODATE."'";
										$reward_info = selectQuery($sql);
										if (!$reward_info['idx']){


											//챌린지 작성자 코인 차감
											$sql = "select coin, highlevel, comcoin from work_member where state='0' and companyno='".$companyno."' and email='".$challenges_info['email']."' order by idx desc limit 1";
											$mem_info_coin = selectQuery($sql);
											if($mem_info_coin['coin']){

												//코인사용인 경우
												if($chall_coin_not!='1'){
													//작성한자의 코인이 지급할 코인보다 클경우만
													if($mem_info_coin['comcoin'] >= $coin){

														$memo_min = "챌린지 참여 보상으로 차감";
														$sql = "insert into work_coininfo(code, work_idx, reward_type, auth_file_idx, companyno, email, name, reward_user, reward_name, coin, memo, workdate, ip)";
														$sql = $sql .= " values('".$ch_code[2]."','".$idx."', '".$reward_type[2]."', '".$chall_file_info['idx']."', '".$companyno."', '".$reward_user."', '".$reward_name."','".$user_id."', '".$user_name."', '".$coin."', '".$memo_min."','".TODATE."','".LIP."')";
														write_log($sql);

														$coin_min_info = insertQuery($sql);
														if($coin_min_info){

															//관리자권한
															if($mem_info_coin['highlevel']=='0'){
																//챌린지 작성자 코인 차감
																$sql = "update work_member set comcoin = comcoin - '".$coin."' where state='0' and companyno='".$companyno."' and email='".$reward_user."'";
																$up = updateQuery($sql);
															//일반사용자
															}else if($mem_info_coin['highlevel']=='5'){
																//챌린지 작성자 코인 차감
																$sql = "update work_member set comcoin = comcoin - '".$coin."' where state='0' and companyno='".$companyno."' and email='".$reward_user."'";
																$up = updateQuery($sql);
															}

															write_log($sql);

															//챌린지 참여자 코인 지급
															$sql = "insert into work_coininfo(code, work_idx, reward_type, auth_file_idx, companyno, email, name, reward_user, reward_name, coin, memo, workdate, ip)";
															$sql = $sql .= " values('".$ch_code[0]."','".$chll_idx."', '".$reward_type[2]."', '".$chall_file_info['idx']."', '".$companyno."', '".$user_id."', '".$user_name."','".$reward_user."', '".$reward_name."', '".$coin."', '".$memo."','".TODATE."','".LIP."')";
															$coin_add_info = insertIdxQuery($sql);
															write_log($sql);

															$tokenTitle = "챌린지 참여 보상";
															$tokenComment = "[".$chall_title."]보상으로 ".$coin."코인을 지급 받았습니다.";

															pushToken($tokenTitle,$tokenComment,$user_id,'reward','21','none','none',$chll_idx);

															//챌린지 참여자 코인 지급
															$sql = "update work_member set coin = coin + '".$coin."' where state='0' and companyno='".$companyno."' and email='".$user_id."'";
															$up = updateQuery($sql);
															write_log($sql);

															//챌린지 참여시 라이브ON 상태변경
															$sql = "select idx from work_member where state='0' and companyno='".$companyno."' and live_1='0' and live_1_regdate is null and email='".$user_id."'";
															$mem_live_info = selectQuery($sql);
															if($mem_live_info['idx']){
																$sql = "update work_member set live_1='1', live_1_regdate=".DBDATE." where idx='".$mem_live_info['idx']."'";
																$live_up = updateQuery($sql);
															}

															write_log("챌린지 참여!!!");
															write_log(" >>> ". $coin_add_info. " && ". $up);
															write_log($category_act['act'][$chall_cate]);

															if($coin_add_info && $up){

																//타임라인(챌린지도전완료)
																work_data_log('0','14', $chll_idx, $user_id, $user_name);

																//역량평가지표(지식:0001, 성과:0002, 성장:0003, 협업:0004, 성실:0005, 실행:0006)
																if($category_act['act'][$chall_cate]){
																	work_cp_reward("challenge", $category_act['act'][$chall_cate], $user_id, $chll_idx);
																}

																//타임라인(코인적립)
																work_data_log('0','15', $coin_add_info, $user_id, $user_name);

																echo "complete";
																exit;
															}
														}
													}
												}else{

													//코인사용 안함 경우
													//역량평가지표 나의업무 완료
													$memo_min = "챌린지 참여 보상으로 차감";
													$sql = "insert into work_coininfo(code, work_idx, reward_type, auth_file_idx, companyno, email, name, reward_user, reward_name, coin, memo, workdate, ip)";
													$sql = $sql .= " values('".$ch_code[2]."','".$idx."', '".$reward_type[2]."', '".$chall_file_info['idx']."', '".$companyno."', '".$reward_user."', '".$reward_name."','".$user_id."', '".$user_name."', '".$coin."', '".$memo_min."','".TODATE."','".LIP."')";
													$coin_min_info = insertQuery($sql);
													if($coin_min_info){
														//챌린지 참여자 코인 지급
														$sql = "insert into work_coininfo(code, work_idx, reward_type, auth_file_idx, companyno, email, name, reward_user, reward_name, coin, memo, workdate, ip)";
														$sql = $sql .= " values('".$ch_code[0]."','".$chll_idx."', '".$reward_type[2]."', '".$chall_file_info['idx']."', '".$companyno."', '".$user_id."', '".$user_name."','".$reward_user."', '".$reward_name."', '".$coin."', '".$memo."','".TODATE."','".LIP."')";
														$coin_add_info = insertQuery($sql);
													}

													//역량평가지표(지식:0001, 성과:0002, 성장:0003, 협업:0004, 성실:0005, 실행:0006)
													if($category_act['act'][$chall_cate]){
														work_cp_reward("challenge", $category_act['act'][$chall_cate], $user_id, $chll_idx);
													}

													//무료코인 지급 완료 처리
													if($coin_min_info && $coin_add_info){

														//타임라인(챌린지도전완료)
														work_data_log('0','14', $chll_idx, $user_id, $user_name);

														echo "complete";
														exit;
													}

												}
											}
										}else{
											echo "coin_info";
											exit;
										}
									}
								}else{
									echo "update_err";
									exit;
								}
							}

						}else{
							echo "file_not_upload";
							exit;
						}
					
					//일반파일처리
					}else{

						$filename = $_FILES['files']['name'][0];
						$ext = array_pop(explode(".", strtolower($filename)));

						//허용확장자체크
						if( !in_array($ext, $file_allowed_ext) ) {
							//echo "ext_file1";
							//exit;
						}

						//파일타입
						$file_type = $_FILES['files']['type'][0];

						//파일사이즈
						$file_size = $_FILES['files']['size'][0];

						//임시파일명
						$file_tmp_name = $_FILES['files']['tmp_name'][0];

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



						//업로드 디렉토리 - /data/회사고유번호/회사랜덤폴더/challenges/files/년/월/
						$upload_path = $dir_file_path."/".$file_save_dir."/".$dir_year."/".$dir_month."/";
						$upload_path = str_replace($file_save_dir , "data/".$companyno."/".$comfolder."/"."challenges/files" , $upload_path);

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

							$sql = "select idx from work_challenges_result where state='0' and challenges_idx='".$chll_idx."' and companyno='".$companyno."' and email='".$user_id."' and idx='".$chamyeo_idx."'";
							$chall_file_info = selectQuery($sql);
							if($chall_file_info['idx']){
								//참여완료하기
								$state = "1";
								$sql = "update work_challenges_result set attend_type='".$attend_type."', state='1', resize='".$resize_val."', file_path='".$file_path."', file_name='".$renamefile."', file_size='".$file_size."', file_real_name='".$file_real_name."' , file_type='".$file_type."', file_regdate=".DBDATE." where challenges_idx='".$chll_idx."' and idx='".$chall_file_info['idx']."'";
								$res_idx = updateQuery($sql);
								if($res_idx){

									//회원정보 - 정상
									$sql = "select idx from work_member where state='0' and companyno='".$companyno."' and email='".$user_id."'";
									$mem_info = selectQuery($sql);
									if($mem_info['idx']){

										//챌린지에서 지급받은 내역있는 체크
										$sql = "select idx from work_coininfo where state='0' and companyno='".$companyno."' and code='".$ch_code[0]."' and work_idx='".$idx."' and reward_type='".$reward_type[2]."' and auth_file_idx='".$res_idx."' and workdate='".TODATE."'";
										$reward_info = selectQuery($sql);
										if (!$reward_info['idx']){


											//챌린지 작성자 코인 차감
											$sql = "select coin, highlevel, comcoin from work_member where state='0' and companyno='".$companyno."' and email='".$challenges_info['email']."' order by idx desc limit 1";
											$mem_info_coin = selectQuery($sql);
											if($mem_info_coin['coin']){


												//코인사용인 경우
												if($chall_coin_not!='1'){

													//작성한자의 코인이 지급할 코인보다 클경우만
													if($mem_info_coin['comcoin'] >= $coin){
												
														$memo_min = "챌린지 참여 보상으로 차감";
														$sql = "insert into work_coininfo(code, work_idx, reward_type, auth_file_idx, companyno, email, name, reward_user, reward_name, coin, memo, workdate, ip)";
														$sql = $sql .= " values('".$ch_code[2]."','".$idx."', '".$reward_type[2]."', '".$chall_file_info['idx']."', '".$companyno."', '".$reward_user."', '".$reward_name."','".$user_id."', '".$user_name."', '".$coin."', '".$memo_min."','".TODATE."','".LIP."')";
														$coin_min_info = insertQuery($sql);
														if($coin_min_info){
												
															//관리자권한
															if($mem_info_coin['highlevel']=='0'){
																//챌린지 작성자 코인 차감
																$sql = "update work_member set comcoin = comcoin - '".$coin."' where state='0' and companyno='".$companyno."' and email='".$reward_user."'";
																$up = updateQuery($sql);
															//일반사용자
															}else if($mem_info_coin['highlevel']=='5'){
																//챌린지 작성자 코인 차감
																$sql = "update work_member set comcoin = comcoin - '".$coin."' where state='0' and companyno='".$companyno."' and email='".$reward_user."'";
																$up = updateQuery($sql);
															}
															

															//챌린지 참여자 코인 지급
															$sql = "insert into work_coininfo(code, work_idx, reward_type, auth_file_idx, companyno, email, name, reward_user, reward_name, coin, memo, workdate, ip)";
															$sql = $sql .= " values('".$ch_code[0]."','".$chll_idx."', '".$reward_type[2]."', '".$chall_file_info['idx']."', '".$companyno."', '".$user_id."', '".$user_name."','".$reward_user."', '".$reward_name."', '".$coin."', '".$memo."','".TODATE."','".LIP."')";
															$coin_add_info = insertIdxQuery($sql);

															//챌린지 참여자 코인 지급
															$sql = "update work_member set coin = coin + '".$coin."' where state='0' and companyno='".$companyno."' and email='".$user_id."'";
															$up = updateQuery($sql);

															//챌린지 참여시 라이브ON 상태변경
															$sql = "select idx, live_1 from work_member where state='0' and live_1='0' and live_1_regdate is null and companyno='".$companyno."' and email='".$user_id."'";
															$mem_live_info = selectQuery($sql);
															if($mem_live_info['idx']){
																$sql = "update work_member set live_1='1', live_1_regdate=".DBDATE." where companyno='".$companyno."' and idx='".$mem_live_info['idx']."'";
																$live_up = updateQuery($sql);
															}

															if($coin_add_info && $up){

																//타임라인(챌린지도전완료)
																work_data_log('0','14', $chll_idx, $user_id, $user_name);

																//역량평가지표(지식:0001, 성과:0002, 성장:0003, 협업:0004, 성실:0005, 실행:0006)
																if($category_act['act'][$chall_cate]){
																	work_cp_reward("challenge", $category_act['act'][$chall_cate], $user_id, $chll_idx);
																}

																//타임라인(코인적립)
																work_data_log('0','15', $coin_add_info, $user_id, $user_name);

																echo "complete";
																exit;
															}
														}
													}
												}else{

													//코인사용 안함 경우
													//역량평가지표(지식:0001, 성과:0002, 성장:0003, 협업:0004, 성실:0005, 실행:0006)
													if($category_act['act'][$chall_cate]){
														work_cp_reward("challenge", $category_act['act'][$chall_cate], $user_id, $chll_idx);
													}

													//타임라인(챌린지도전완료)
													work_data_log('0','14', $chll_idx, $user_id, $user_name);

													echo "complete";
													exit;
												}
											}
										}else{
											echo "coin_info";
											exit;
										}
									}
								}else{
									echo "file_info";
									exit;
								}

							}
						}
					}
				}
			}else{
				echo "day_expire";
				exit;
			}
		}
	}
	exit;
}


//챌린지참여 혼합형
if($mode == "challenges_mix"){
	$todatetime = date('Y-m-d H:i:s');
	$idx = $_POST['idx'];
	$chll_idx = preg_replace("/[^0-9]/", "", $idx);
	$chamyeo_idx = $_POST['chamyeo_idx'];
	$chamyeo_idx = preg_replace("/[^0-9,]/", "", $chamyeo_idx);

	$chamyeo_idx_tmp = @explode(",",$chamyeo_idx);
	if($chamyeo_idx_tmp){
		$chamyeo_com_idx = $chamyeo_idx_tmp[0];
		$chamyeo_file_idx = $chamyeo_idx_tmp[1];
	}

	$message = $_POST['message'];
	// $message = nl2br($message);
	// $message = addslashes($message);

	//홑따옴표 때문에 아래와 같이 처리
	$message = replace_text($message);

	/*
	print "<pre>";
	print_r($_FILES);
	print "</pre>";
	exit;*/
	


	//부서별정보
	if($user_part){
		$sql = "select idx, partname from work_team where state='0' and companyno='".$companyno."' and idx='".$user_part."'";
		$team_info = selectQuery($sql);
		if ($team_info['idx']){
			$partname = $team_info['partname'];
		}
	}


	if($chll_idx){

		//챌린지 확인
		$link_idx = array();
		$sql = "select idx, attend_type, email, name, coin, coin_not, title, cate, sdate, edate, day_type, attend, outputchk from work_challenges where state='0' and companyno='".$companyno."' and idx='".$chll_idx."'";
		$challenges_info = selectQuery($sql);

		if($challenges_info['idx']){
			$coin = $challenges_info['coin'];
			$memo = "챌린지 참여 보상";
			$reward_user = $challenges_info['email'];
			$reward_name = $challenges_info['name'];
			$chall_cate = $challenges_info['cate'];
			$chall_coin_not = $challenges_info['coin_not'];
			$category_act = challenges_category();
			/*print "<pre>";
			print_r($_POST);
			print_r($_FILES);
			print "</pre>";*/

			//echo filesize_check($_FILES['files']['size'][0]);
			//업로드한 파일 5M 이상 업로드시 제한하기

			if($_FILES['files']['size'][0]){
				$userfile_size = $_FILES['files']['size'][0];
				$maxsize = 20 * 1024 * 1024;			//20MB 용량 제한
				if($userfile_size >= $maxsize){
					echo "file_max_size";
					exit;
				}
			}


			//챌린지 참여 기간내에 참여 할수 있도록 처리
			if( TODATE >= $challenges_info['sdate'] && TODATE <= $challenges_info['edate'] ){

				//챌린지 참여자체크
				$sql = "select idx, state from work_challenges_user where state='0' and challenges_idx='".$chll_idx."' and companyno='".$companyno."' and email='".$user_id."'";
				$chall_user_info = selectQuery($sql);
				if(!$chall_user_info['idx']){
					echo "ch_notuser";
					exit;
				}

				$where = " and ((DATE_FORMAT(comment_regdate, '%Y-%m-%d')>='".$challenges_info['sdate']."' and DATE_FORMAT(comment_regdate, '%Y-%m-%d')<='".$challenges_info['edate']."')";
				$where = $where .= " or (DATE_FORMAT(file_regdate, '%Y-%m-%d')>='".$challenges_info['sdate']."' and DATE_FORMAT(file_regdate, '%Y-%m-%d')<='".$challenges_info['edate']."'))";

				//챌린지 참여 횟수가 한번만 참여
				if ($challenges_info['day_type'] == "0"){
					//완료체크
					$sql = "select idx, comment from work_challenges_result where state='1' and companyno='".$companyno."' and challenges_idx='".$chll_idx."' and (comment!='' or (file_path!='' and file_name!='')) and email='".$user_id."'".$where." order by idx desc limit 1";
					$chall_comment_info = selectQuery($sql);
					if($chall_comment_info['idx']){
						$com_idx = $chall_comment_info['idx'];
					}

				}else if ($challenges_info['day_type'] == "1"){

					//하루 한번 참여가능
					$sql = "select idx, comment from work_challenges_result where state='1' and companyno='".$companyno."' and challenges_idx='".$chll_idx."' and (comment!='' or (file_path!='' and file_name!='')) and email='".$user_id."' and (DATE_FORMAT(comment_regdate, '%Y-%m-%d')='".TODATE."' or DATE_FORMAT(file_regdate, '%Y-%m-%d')='".TODATE."') order by idx desc limit 1";
					$chall_comment_info = selectQuery($sql);
					if($chall_comment_info['idx']){
						$com_idx = $chall_comment_info['idx'];
					}

					//챌린지 참여회수 체크
					$sql = "select count(idx) as cnt from work_challenges_result";
					$sql = $sql .=" where state='1' and companyno='".$companyno."' and challenges_idx='".$chll_idx."' and (comment!='' or (file_path!='' and file_name!='')) and email='".$user_id."'".$where."";
					$chall_comment_info = selectQuery($sql);
					if ($chall_comment_info['cnt'] >= $challenges_info['attend']){
						echo "chamyeo_max|".$challenges_info['attend'];
						exit;
					}

				}

				if($chamyeo_idx){
					//챌린지 도전중 일때 참여 처리 하기
					$sql = "select idx from work_challenges_result where state='0' and companyno='".$companyno."' and challenges_idx='".$chll_idx."' and comment is null and email='".$user_id."' and idx='".$chamyeo_idx."' order by idx desc limit 1";
					$chall_comment_info = selectQuery($sql);
					if($chall_comment_info['idx']){
						//$message = urlencode($message);
						$sql = "update work_challenges_result set attend_type='".$challenges_info['attend_type']."', comment='".$message."', type_flag='".$type_flag."', comment_regdate=".DBDATE." where companyno='".$companyno."' and idx='".$chall_comment_info['idx']."'";
						$res_idx = updateQuery($sql);
						if($res_idx){
							$com_idx = $chall_comment_info['idx'];
						}
					}
				}

				if($chamyeo_idx){
					$sql = "select idx from work_challenges_result where challenges_idx='".$chll_idx."' and companyno='".$companyno."' and file_path is null and file_name is null and email='".$user_id."' and idx='".$chamyeo_idx."'";
					$chall_file_info = selectQuery($sql);
					if($chall_file_info['idx']){
						$sql = "update work_challenges_result set attend_type='".$challenges_info['attend_type']."', file_regdate=".DBDATE." where challenges_idx='".$chll_idx."' and idx='".$chall_file_info['idx']."'";
						$res_idx = updateQuery($sql);
						if($res_idx){
							$file_idx = $chall_file_info['idx'];
						}
					}
				}else{ 
					$sql = "select idx from work_challenges_result where state='0' and companyno='".$companyno."' and challenges_idx='".$chll_idx."' and email='".$user_id."' and DATE_FORMAT(file_regdate, '%Y-%m-%d')='".TODATE."'";
					$chall_file_info = selectQuery($sql);
					if(!$chall_file_info['idx']){
						//참여완료하기
						$sql = "insert into work_challenges_result(state, challenges_idx, companyno, email, name, part, partno, comment, comment_regdate, ip, file_regdate) values(";
						$sql = $sql .="'1','".$chll_idx."', '".$companyno."', '".$user_id."', '".$user_name."', '".$partname."', '".$user_part."', '".$message."', '".$todatetime."', '".LIP."', '".$todatetime."')";
						$file_idx = insertIdxQuery($sql);
						$chamyeo_idx = $file_idx;
					}else{

						$sql = "update work_challenges_result set attend_type='".$challenges_info['attend_type']."', state='1' where challenges_idx='".$chll_idx."' and idx='".$chall_file_info['idx']."'";
						$up = updateQuery($sql);
						if($up){
							$file_idx = $chall_file_info['idx'];
						}
					}
				}

				//파일첨부
				if($_FILES){
					for($i=0;$i<count($_FILES['files']['name']);$i++){
						//파일타입(이미지파일:true, 일반파일:false)
						$file_type_img = false;
		
						//파일명
						$filename = $_FILES['files']['name'][$i];
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
		
							//업로드 디렉토리 - /data/회사고유번호/회사랜덤폴더/challenges/files/년/월/
							$upload_path = $dir_file_path."/".$file_save_dir_img."/".$dir_year."/".$dir_month."/";
							$upload_path = str_replace($file_save_dir_img , "data/".$companyno."/".$comfolder."/"."challenges/img" , $upload_path);

							//업로드 디렉토리 - /data/회사고유번호/회사랜덤폴더/challenges/files/년/월/
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
								$sql = $sql .="'1','".$chamyeo_idx."', '".$i."', '".$companyno."',  '".$user_id."', '".$user_part."', '".$resize_val."', '".$file_path."', '".$renamefile."', '".$file_size."','".$file_path_ori."','".$renamefile."','".$file_size."','".$file_real_name."', '".$file_type."', '".LIP."', '".$todatetime."')";
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

							//업로드 디렉토리 - /data/회사고유번호/회사랜덤폴더/challenges/files/년/월/
							$upload_path = $dir_file_path."/".$file_save_dir."/".$dir_year."/".$dir_month."/";
							$upload_path = str_replace($file_save_dir , "data/".$companyno."/".$comfolder."/"."challenges/file" , $upload_path);
							//디렉토리 없는 경우 권한 부여 및 생성

							if (!is_dir( $upload_path )){
								if(mkdir( $upload_path , 0777, true)){
									continue;
								}else{
									echo "failed";
									exit;
								}
							}

							$result = file_upload_send( $file_tmp_name, $upload_path. $renamefile );
							$file_path = str_replace($dir_file_path , "" , $upload_path);
							if(!$result){
								echo "not_files2";
								exit;
							}else{
								$sql = "insert into work_challenges_file_info (state, challenges_idx, num, companyno,  email, partno, resize, file_path, file_name, file_size,  file_real_img_name, file_type, ip, file_regdate) values(";
								$sql = $sql .="'1','".$chamyeo_idx."', '".$i."', '".$companyno."',  '".$user_id."', '".$user_part."', '".$resize_val."', '".$file_path."', '".$renamefile."', '".$file_size."','".$file_real_name."', '".$file_type."', '".LIP."', '".$todatetime."')";
								$file_idx = insertIdxQuery($sql);
							}
						}	
					}//for문 끝나는 곳
				}


				//메시지, 파일첨부등록 완료처리
				if($com_idx || $file_idx){
					//완료처리
					$sql = "update work_challenges_result set state='1' where idx='".$chamyeo_idx."'";
					updateQuery($sql);

					//회원정보 - 정상
					$sql = "select idx from work_member where state='0' and companyno='".$companyno."' and email='".$user_id."'";
					$mem_info = selectQuery($sql);
					if($mem_info['idx']){

						//$link_idx_arr = @implode(",",$link_idx);

						//챌린지에서 지급받은 내역있는 체크
						$sql = "select idx from work_coininfo where state='0' and companyno='".$companyno."' and code='".$ch_code[0]."' and work_idx='".$chll_idx."' and reward_type='".$reward_type[2]."' and auth_file_idx='".$file_idx."' and auth_comment_idx='".$com_idx."' and workdate='".TODATE."'";
						$reward_info = selectQuery($sql);
						if (!$reward_info['idx']){


							//챌린지 작성자 코인 차감
							$sql = "select coin, highlevel, comcoin from work_member where state='0' and companyno='".$companyno."' and email='".$challenges_info['email']."' order by idx desc limit 1";
							$mem_info_coin = selectQuery($sql);
							if($mem_info_coin['coin']){
								//코인사용인 경우
								if($chall_coin_not!='1'){

									$sql = "insert into work_coininfo(code, work_idx, reward_type, auth_comment_idx, auth_file_idx, companyno, email, name, reward_user, reward_name, coin, memo, workdate, ip)";
									$sql = $sql .= " values('".$ch_code[0]."','".$chamyeo_idx."', '".$reward_type[2]."', '".$com_idx."', '".$file_idx."', '".$companyno."', '".$user_id."', '".$user_name."','".$reward_user."', '".$reward_name."', '".$coin."', '".$memo."','".TODATE."','".LIP."')";
									$coininfo_ins = insertIdxQuery($sql);

									$sql = "select idx,title from work_challenges where idx = '".$chll_idx."'";
									$query = selectQuery($sql);
									
									$message = $query['title'];
									$message = nl2br($message);
									// $message = str_replace("</br>", "", $message);
									$nl2br_arr = array("<br />","<br/>","<br/><br />");
									$message = str_replace($nl2br_arr,"",$message);
									$message = addslashes($message);

									$tokenTitle = "챌린지 참여 보상";
									$tokenComment = "[".$message."] 참여로 ".$coin."코인을 보상 받았습니다.";
									pushToken($tokenTitle,$tokenComment,$user_id,'reward','21','none','none',$chamyeo_idx,null,'challenge');

									//작성한자의 코인이 지급할 코인보다 클경우만
									

										// $memo_min = "챌린지 참여 보상으로 차감";
										// $sql = "insert into work_coininfo(code, work_idx, reward_type, auth_comment_idx, auth_file_idx, companyno, email, name, reward_user, reward_name, coin, memo, workdate, ip)";
										// $sql = $sql .= " values('".$ch_code[2]."','".$idx."', '".$reward_type[2]."', '".$com_idx."', '".$file_idx."', '".$companyno."', '".$reward_user."', '".$reward_name."','".$user_id."', '".$user_name."', '".$coin."', '".$memo_min."','".TODATE."','".LIP."')";
										// $coin_min_info = insertQuery($sql);
									
								
											//관리자권한
											// if($mem_info_coin['highlevel'] == '0'){
											// 	//챌린지 작성자 코인 차감
											// 	$sql = "update work_member set comcoin = comcoin - '".$coin."' where state='0' and companyno='".$companyno."' and email='".$reward_user."'";
											// 	$up = updateQuery($sql);

											// //일반사용자
											// }else if($mem_info_coin['highlevel'] == '5'){
											// 	//챌린지 작성자 코인 차감
											// 	$sql = "update work_member set comcoin = comcoin - '".$coin."' where state='0' and companyno='".$companyno."' and email='".$reward_user."'";
											// 	$up = updateQuery($sql);
											// }

											
											//챌린지 코인 지급
											$sql = "update work_member set coin = coin + '".$coin."' where state='0' and companyno='".$companyno."' and email='".$user_id."'";
											$coin_up = updateQuery($sql);

											//챌린지 참여시 라이브ON 상태변경
											$sql = "select idx from work_member where state='0' and live_1='0' and live_1_regdate is null and companyno='".$companyno."' and email='".$user_id."'";
											$mem_live_info = selectQuery($sql);
											if($mem_live_info['idx']){
												$sql = "update work_member set live_1='1', live_1_regdate=".DBDATE." where idx='".$mem_live_info['idx']."'";
												$live_up = updateQuery($sql);
											}

											if($coininfo_ins && $coin_up){
												

												//타임라인(챌린지도전완료)
												work_data_log('0','14', $chll_idx, $user_id, $user_name);
											
												//역량평가지표(지식:0001, 성과:0002, 성장:0003, 협업:0004, 성실:0005, 실행:0006)
												if($category_act['act'][$chall_cate]){
													work_cp_reward("challenge", $category_act['act'][$chall_cate], $user_id, $chll_idx,'',$chamyeo_idx);
												}

												//타임라인(코인적립)
												work_data_log('0','15', $coininfo_ins, $user_id, $user_name);

												echo "complete";
												exit;
											}
										
									
								}else{

									//코인사용 안함 경우
									//역량평가지표(지식:0001, 성과:0002, 성장:0003, 협업:0004, 성실:0005, 실행:0006)
									if($category_act['act'][$chall_cate]){
										work_cp_reward("challenge", $category_act['act'][$chall_cate], $user_id, $chll_idx);
									}

									//타임라인(챌린지도전완료)
									work_data_log('0','14', $chll_idx, $user_id, $user_name);

									echo "complete";
									exit; 
								}
							}
						}else{
							echo "coin_info";
							exit;
						}
					}else{
						echo "mem_state";
						exit;
					}
					


					exit;
				}else{

					echo "111111";
					exit;
				}

			}else{
				echo "day_expire";
				exit;
			}		
		}
	}
	exit;
}

if($mode == "challenges_like"){
	// 카테고리, 역량 지표
	$cate_num = $_POST["cateno"];

	// 챌린지 참여자 idx 
	$result_idx = $_POST["result_idx"];

	// 챌린지 idx
	$challenges_idx = $_POST["challenges_idx"];

	$todatetime = date('Y-m-d H:i:s');

	$sql = "select email, name, companyno, part, partno from work_challenges_result where idx = '".$result_idx."' ";
	$query = selectQuery($sql);

	$companyno = $query['companyno'];
	$email = $query['email'];
	$name = $query['name'];
	$part = $query['part'];

	$penalty = member_penalty($query['email']);
	if($penalty['penalty_state']>0){
		echo "penalty";
		exit;
	}

	//일일 최다 좋아요 횟수 체크
	$limit_like = limit_like_check($send_info['email']);
	if($limit_like['cnt'] > 5){
		echo "limit_like";
		exit;
	}

	$sql = "insert into work_todaywork_like (state, companyno, kind_flag, service, work_idx, like_flag, email, name, send_email, send_name, comment, type_flag, ip, workdate, regdate) values";
	$sql = $sql .= " ('0','".$companyno."','".$cate_num."','challenges','".$result_idx."','0','".$email."','".$name."','".$user_id."','".$user_name."','챌린지를 응원합니다.','".$type_flag."','".LIP."','".TODATE."','".$todatetime."')";
	$likeQuery = insertIdxQuery($sql);
	
	if($likeQuery){
		echo "|success";

		//타임라인(좋아요)
		work_data_log('0','8', $insert_idx, $user_id, $user_name, $email, $name);

		//타임라인(좋아요 받음)
		work_data_log('0','10', $likeQuery, $email, $name, $user_id, $user_name);

				//1:인정하기, 2:응원하기, 3:칭찬하기, 4:격려하기, 5:축하하기, 6:감사하기

				if($cate_cum == '1'){

					//역량평가지표(좋아요 인정하기), 실행
					work_cp_reward("like","0006", $email, $likeQuery);

				}else if($cate_num == '2'){

					//역량평가지표(좋아요 응원하기), 협업
					work_cp_reward("like","0007", $email, $likeQuery);

				}else if($cate_num  == '3'){

					//역량평가지표(좋아요 칭찬하기), 성장
					work_cp_reward("like","0003", $email, $likeQuery);

				}else if($cate_num  == '4'){

					//역량평가지표(좋아요 격려하기), 성실
					work_cp_reward("like","0005", $email, $likeQuery);

				}else if($cate_num  == '5'){

					//역량평가지표(좋아요 축하하기), 에너지
					work_cp_reward("like","0004", $email, $likeQuery);

				}else if($cate_num  == '6'){

					//역량평가지표(좋아요 감사하기), 성과
					work_cp_reward("like","0002", $email, $likeQuery);

				}

				//역량평가지표(좋아요 보내기)
				work_cp_reward("like", "0001", $user_id, $likeQuery);

				//좋아요 누르면 자신의 협업점수 +1점
				work_cp_reward("like","0007", $user_id, $likeQuery);

				//pushToken("알림제목","알림내용","받는사람의 아이디","알림종류","종류에 따른 코드","알림 보낸사람 아이디","알림 보낸사람 이름","idx")
				$title = $user_name."님이 좋아요를 보냈어요";
				$content = "챌린지를 응원합니다!";
				pushToken($title, $content, $query['email'], 'live', '10', $user_id, $user_name, $likeQuery);
	}else{
		echo"|failed";
	}
	exit;
}

if($mode == "layer_show"){
	// 챌린지도전 idx
	$challenges_idx = $_POST['result_idx'];

	$sql = "select idx, challenges_idx, email, name, comment from work_challenges_result where idx = '".$challenges_idx."' and email = '".$user_id."'";
	$chall = selectQuery($sql);

	$sql = "select idx, challenges_idx, file_real_img_name, email from work_challenges_file_info where state = '1' and  challenges_idx = '".$chall['idx']."' and email = '".$chall['email']."' order by idx desc";
	$query = selectAllQuery($sql);
	?>
	<input type="hidden" id="chamyeo_idx" value="<?=$chall['idx']?>">
	<div class="layer_deam"></div>
	<div class="layer_cha_join_in">
		<div class="layer_cha_join_box">
			<div class="layer_cha_join_title">
				<strong>챌린지 참여 수정</strong>
				<span>확인 메시지와 사진을 수정할 수 있습니다.</span>
			</div>
			<div class="layer_cha_join_area">
				<div class="layer_cha_join_input">
				<?  $nl2br_arr = array("<br />","<br/>","<br/><br />");
					$result = str_replace($nl2br_arr,"",$chall['comment']); ?>
					<textarea name="" id="cham_comment"><?=$result?></textarea>
				</div>
				<div class="layer_cha_join_file_desc" >
					<!-- <input type="hidden" id="mix_file_name" value="<?=$chall_files_info['file_real_img_name']?>" /> -->

					<? for($z=0;$z<count($query['idx']);$z++){?>
						<div class="file_desc" id="chall_file_desc_<?=$z?>">
							<input type="hidden" id="mix_file_idx_<?=$z?>" value="<?=$query['idx'][$z]?>">
							<span><?=$query['file_real_img_name'][$z]?></span>
							<button id="mix_file_del_<?=$z?>">삭제</button>
						</div>
					<?}?>
				</div>
				<div class="layer_cha_join_file" id="layer_update_list" value="update">
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

	<? echo "|success|".$chall['idx'];
}

if($mode == "chall_comment"){
	//챌린지 넘버
	$challenges_idx = $_POST['view_idx'];

	//도전 리스트 넘버
	$result_idx = $_POST['result_idx'];

	//코멘트 내용
	$comment = $_POST['comment'];

	//오늘날짜 : 현재시간
	$todatetime = date('Y-m-d H:i:s');
	
	// 코멘트 입력자 정보
	$sql = "select email, name, companyno, part, partno from work_member where email = '".$user_id."' and state = '0' order by idx desc limit 0,1";
	$userinfo = selectQuery($sql);

	// 기존 챌린지 정보 

	$sql = "select idx, title from work_challenges where idx = '".$challenges_idx."'";
	$chall_list = selectQuery($sql);
    // 코멘트 본문의 작성자
	$sql = "select idx, email from work_challenges_result where idx = '".$result_idx."'";
	$like = selectQuery($sql);

	$sql = "insert into work_challenges_comment(state, challenges_idx, result_idx, email, name, companyno, part, partno, comment, ip, workdate, regdate, like_email) values";
	$sql = $sql .= "('1','".$challenges_idx."','".$result_idx."','".$user_id."','".$user_name."','".$userinfo['companyno']."', '".$userinfo['part']."', '".$userinfo['partno']."', '".$comment."', '".LIP."', '".TODATE."', '".$todatetime."', '".$like['email']."' )";
	$insert = insertIdxQuery($sql);

	if($insert){
		$chall_memo_title = $user_name."님의 메모"; 
		pushToken($chall_memo_title,$comment,$like['email'],'memo','37',$user_id,$user_name,$challenges_idx,$chall_list['title'], 'chall');
	}

	?>

	<div class="tdw_list_memo_desc" id="resultCo_<?=$insert?>" value="<?=$insert?>">
		<div class="tdw_list_memo_name"><?=$userinfo['name']?></div>
		<input type="hidden" id="memo_id" value="<?=$userinfo['email']?>">
		<div class="tdw_list_memo_conts">
			<div class="tdw_list_memo_conts_txt">
				<strong><?=$comment?></strong>
				<div class="tdw_list_memo_regi" id="chall_memo_<?=$insert?>">
					<textarea name="" class="textarea_regi"><?=$comment?></textarea>
					<div class="btn_regi_box">
						<button class="btn_regi_submit" value="<?=$insert?>"><span>확인</span></button>
						<button class="btn_regi_cancel" value="<?=$insert?>"><span>취소</span></button>
					</div>
				</div>
			</div>
			<em class="tdw_list_memo_conts_date"><?=$todatetime?>
				<button class="btn_memo_del" id="layer_memo_delete" value="<?=$insert?>"><span>삭제</span></button>
			</em>
		</div>
	</div>
	<?

	echo "|complete";
	exit;
}

if($mode == "chall_comment_del"){
	//챌린지 넘버
	$challenges_idx = $_POST['view_idx'];

	//댓글 넘버
	$memo_idx = $_POST['memo_idx'];

	$sql = "select idx, email from work_challenges_comment where idx = '".$memo_idx."' and email = '".$user_id."' and challenges_idx = '".$challenges_idx."' ";
	$del_memo = selectQuery($sql);

	if(!$del_memo['idx']){
		echo "Not memo";
		exit;
	}else{
		$sql = "update work_challenges_comment set state = '9' where idx = '".$memo_idx."' and email = '".$user_id."'";
		$delete = updateQuery($sql);

		echo "complete";
		exit;
	}
}

if($mode == "chall_comment_update"){
	//챌린지 넘버
	$challenges_idx = $_POST['view_idx'];

	//코멘트 내용
	$comment = $_POST['comment'];

	//챌린지 메모 넘버
	$memo_idx = $_POST['memo_idx'];

	//오늘날짜 : 현재시간
	$todatetime = date('Y-m-d H:i:s');
	
	// 코멘트 입력자 정보
	$sql = "select email, name, companyno, part, partno from work_challenges_comment where email = '".$user_id."' and idx = '".$memo_idx."'";
	$userinfo = selectQuery($sql);

	$sql = "update work_challenges_comment set comment = '".$comment."', editdate = '".$todatetime."' where idx = '".$memo_idx."' and state = '1' and email = '".$user_id."'";
	$update = updateQuery($sql);
	?>
		<div class="tdw_list_memo_name"><?=$userinfo['name']?></div>
		<input type="hidden" id="memo_id" value="<?=$userinfo['email']?>">
		<div class="tdw_list_memo_conts">
			<div class="tdw_list_memo_conts_txt">
				<strong><?=$comment?></strong>
				<div class="tdw_list_memo_regi" id="chall_memo_<?=$memo_idx?>">
					<textarea name="" class="textarea_regi"><?=$comment?></textarea>
					<div class="btn_regi_box">
						<button class="btn_regi_submit" value="<?=$memo_idx?>"><span>확인</span></button>
						<button class="btn_regi_cancel" value="<?=$memo_idx?>"><span>취소</span></button>
					</div>
				</div>
			</div>
			<em class="tdw_list_memo_conts_date"><?=$todatetime?>
				<button class="btn_memo_del" id="layer_memo_delete" value="<?=$insert?>"><span>삭제</span></button>
			</em>
		</div>
	<?

	echo "|complete";
	exit;
}

if($mode == "img_slice"){
	// img_idx
	$img_idx = $_POST['img_idx'];

	$sql = "select idx, challenges_idx from work_challenges_file_info where state = '1' and idx = '".$img_idx."' ";
	$query = selectQuery($sql);
	// echo $sql."|";

	$sql = "select idx, state, file_ori_path, file_ori_name ,file_real_img_name, challenges_idx, num from work_challenges_file_info where state = '1' and challenges_idx = '".$query['challenges_idx']."' order by idx desc";
	$file_info = selectAllQuery($sql);
	
	$sql = "select count(1) as cnt from work_challenges_file_info where state = '1' and challenges_idx = '".$query['challenges_idx']."'";
	$count = selectQuery($sql);

	 for($i=0;$i<count($file_info['idx']);$i++){
	 	$file_print = $file_info['file_ori_path'][$i].$file_info['file_ori_name'][$i]; ?>
		<li id="imgList_<?=$i+1?>" class=<?=$file_info['idx'][$i]==$img_idx?"btn_on":"btn_off"?> style=<?=$file_info['idx'][$i]==$img_idx?"":"display:none"?>><img src="<?=$file_print?>" alt="img" value="<?=$file_info['idx'][$i]?>" name="<?=$file_info['file_real_img_name'][$i]?>"></li>
	<?}
	echo "|".$count['cnt'];
	exit;
}
?>
