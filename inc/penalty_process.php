<?php

//지각, 퇴근, 업무작성 페널티 관련 내용입니다.

include str_replace( basename(__DIR__) , "", __DIR__ ) ."inc_lude/conf.php";
include DBCON;
include FUNC;



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

$mode = $_POST["mode"];						//mode값 전달받음
$type_flag = ($chkMobile)?1:0;				//구분(0:사이트, 1:모바일)

if($_COOKIE){
	$user_id = $_COOKIE['user_id'];
	$user_name = $_COOKIE['user_name'];
	$user_level = $_COOKIE['user_level'];
	$user_part = $_COOKIE['user_part'];
}


if($user_id=='sadary0@nate.com'){

	print "<pre>";
	print_r($_POST);

	print_r($_FILES);

	print "</pre>";

	//exit;

}

//이미지 등록 처리
if($mode == "penalty_img_upload"){

	//파일명
	$file_name = $_FILES['pl_file']['name'];

	//파일타입
	$file_type = $_FILES['pl_file']['type'];

	//파일위치
	$file_tmp_name = $_FILES['pl_file']['tmp_name'];

	//파일사이즈
	$file_size = $_FILES['pl_file']['size'];

	//
	$kind = $_POST["kind"];	
	$kind = preg_replace("/[^0-9]/", "", $kind);

	//지각 페널티
	if($kind == '01'){
		$kind = "0";

	//오늘업무
	}else if($kind == '02'){
		$kind = "1";

	//퇴근 페널티
	}else if($kind == '03'){
		$kind = "2";
	}

	if($file_name){

		$sql = "select ISNULL(max(idx), 0) + 1 as idx from work_filesinfo_img_penalty where state='0' and kind='".$kind."' and email='".$user_id."' and convert(char(10) , regdate, 120) ='".TODATE."'";
		$penalty_info = selectQuery($sql);
		$penalty_info_idx = $penalty_info['idx'];

		echo $sql;

		//확장자 추출
		$ext = array_pop(explode(".", strtolower($file_name)));
		$get_file_info = getimagesize($file_tmp_name);
		$get_file_info_type = $get_file_info['mime']; 

		$new_width = $get_file_info[0];
		$new_height = $get_file_info[1];
		$width = $get_file_info[0];
		$height = $get_file_info[1];

		//랜덤번호
		$rand_id = name_random();

		//변경되는 파일명
		list($microtime,$timestamp) = explode(' ',microtime());
		$time = $timestamp.substr($microtime, 2, 3);
		$datetime = date("YmdHis", $timestamp).substr($microtime, 2, 3);

		//업로드 파일명
		$renamefile = "{$datetime}_{$rand_id}_pl_{$penalty_info_idx}.{$ext}";

		//년도
		$dir_year = date("Y", TODAYTIME);

		//월
		$dir_month = date("m", TODAYTIME);

		//회사별 폴더명
		$comfolder = $comfolder;

		//업로드 디렉토리 - /rewardy_1(회사코드)/data/challenges/files/년/월/
		$upload_path = $dir_file_path."/".$penalty_save_dir_img."/".$dir_year."/".$dir_month."/";
		$upload_path = str_replace($penalty_save_dir_img , "data/".$companyno."/".$comfolder."/"."penalty/img" , $upload_path);

		//업로드 디렉토리 - /rewardy_1(회사코드)/data/challenges/files/년/월/
		$upload_path_ori = $dir_file_path."/".$penalty_save_dir_img_ori."/".$dir_year."/".$dir_month."/";
		$upload_path_ori = str_replace($penalty_save_dir_img_ori , "data/".$companyno."/".$comfolder."/"."penalty/img_ori" , $upload_path_ori);


		//리사이즈한 업로드될 파일경로/파일명
		$upload_files = $upload_path.$renamefile;

		//원본 업로드될 파일경로/파일명
		$upload_files_ori = $upload_path_ori.$renamefile;

		//디렉토리 없는 경우 권한 부여 및 생성
		if ( !is_dir ( $upload_path ) ){
			mkdir( $upload_path , 0777, true);
		}

		//디렉토리 없는 경우 권한 부여 및 생성 - 원본폴더
		if ( !is_dir ( $upload_path_ori ) ){
			mkdir( $upload_path_ori , 0777, true);
		}

		/*print "<pre>";
		print_r($file_info);
		print "</pre>";
		Array
		(
			[0] => 2305
			[1] => 2305
			[2] => 2
			[3] => width="2305" height="2305"
			[bits] => 8
			[channels] => 3
			[mime] => image/jpeg
		)

		*/

		// 저용량 jpg 파일을 생성합니다
		if ($get_file_info_type == 'image/png'){

			//배경이 투명일때 처리
			//$image = imagecreatefrompng($file_tmp_name);
			list($width, $height) = getimagesize($file_tmp_name);
			//$new_width = "300";
			//$new_height = "300";
			$new_width = $width;
			$new_height = $height;

			//if($width>$new_width && $height>$new_height)
			//{
				$image_p = imagecreatetruecolor($new_width, $new_height);
				imagealphablending($image_p, false);
				imagesavealpha($image_p, true);
				$image_png = imagecreatefrompng($file_tmp_name);
				imagecopyresampled($image_p, $image_png, 0, 0, 0, 0, $new_width, $new_height, $width, $height);
				
				$img_png = 1;
			//}

		//이미지파일 gif 처리
		}else if ($get_file_info_type == 'image/gif'){

			

			//움직이는 이미지가 아닐때만
			if(!is_ani($file_tmp_name)){
				$image = imagecreatefromgif($file_tmp_name);
			}else{
				/*
				gifresizer 사용으로 변환 속도가 많이 느림
				include_once str_replace( basename(__DIR__) , "", __DIR__ ) ."inc_lude/gifresizer.php";
				$gr = new gifresizer;
				//$gr->temp_dir = "frames";
				$gr->temp_dir = "frames";
				$gr->resize($file_tmp_name, $upload_path."2.gif", $width, $height);
				*/
			}


		}else if ($get_file_info_type == 'image/jpeg'){

			$exif = exif_read_data($file_tmp_name);
			$image = imagecreatefromjpeg($file_tmp_name);//<임시 리소스 생성
			if($exif['Orientation']){
				//값에 따라 회전
				switch($exif['Orientation']){
					case 8 : $image = imagerotate($image,90,0); break;
					case 3 : $image = imagerotate($image,180,0); break;
					case 6 : $image = imagerotate($image,-90,0); break;
				}
			}

		}else{

			if($ext == "tiff"){
				$exif = exif_read_data($file_tmp_name);
				$image = imagecreatefromjpeg($file_tmp_name);//<임시 리소스 생성
				if($exif['Orientation']){
					//값에 따라 회전
					switch($exif['Orientation']){
						case 8 : $image = imagerotate($image,90,0); break;
						case 3 : $image = imagerotate($image,180,0); break;
						case 6 : $image = imagerotate($image,-90,0); break;
					}
				}

			}else{
				$image = null;
			}
		}

		// 파일 압축 및 업로드
		if (isset($image)) {
			$return = imagejpeg($image, $upload_files, 50);
			//$rezie_img = fn_imagejpeg($image, $upload_files, $new_file_width, $new_file_height, $file_width, $file_height, $new_quality);

			//리사이즈 파일 용량
			$file_resize = filesize($upload_files); 
		}



		//리사이즈이미지 경로
		$file_path = str_replace($dir_file_path , "" , $upload_path);

		//원본이미지 경로
		$file_path_ori = str_replace($dir_file_path , "" , $upload_path_ori);

		//원본이미지 업로드
		$return = move_uploaded_file($file_tmp_name, $upload_files_ori);
		if($return){

			//이미지 변환이 없는경우는 복사처리
			if(!$image){

				//png경우
				if($img_png == 1){
					imagepng($image_p, $upload_files, 9);
					$file_resize = filesize($upload_files);
				}else{
					$r = copy($upload_files_ori, $upload_files);
					$file_resize = filesize($upload_files_ori);
				}
			}

			$sql = "select idx from work_penalty_list where state='0' and kind_flag='".$kind."' and companyno='".$companyno."' and email='".$user_id."' and convert(char(10), regdate, 120)='".TODATE."'";
			$pl_info = selectQuery($sql);
			if($pl_info['idx']){
				$sql = "update work_penalty_list set state='1', comdate=".DBDATE." where state='0' and idx='".$pl_info['idx']."'";
				$up = updateQuery($sql);
			}

			$sql = "select idx, file_path, file_name, file_ori_path, file_ori_name from work_filesinfo_img_penalty where state='0' and link_idx='".$pl_info['idx']."' and kind='".$kind."' and email='".$user_id."' and convert(char(10), regdate , 120)='".TODATE."'";
			$penalty_info = selectQuery($sql);
			if($penalty_info['idx']){

				//파일 삭제
				if ($penalty_info['file_path'] && $penalty_info['file_name']){
					@unlink($dir_file_path.$penalty_info['file_path'].$penalty_info['file_name']);
				}

				//원본 파일 삭제
				if($penalty_info['file_ori_path'] && $penalty_info['file_ori_name']){
					@unlink($dir_file_path.$penalty_info['file_ori_path'].$penalty_info['file_ori_name']);
				}

				$sql = "update work_filesinfo_img_penalty set link_idx='".$pl_info['idx']."', file_path='".$file_path."', file_name='".$renamefile."', file_size='".$file_resize."', file_ori_path='".$file_path_ori."', file_ori_name='".$renamefile."', file_ori_size='".$file_size."', file_real_name='".$file_name."', file_type='".$file_type."', editdate=".DBDATE." where idx='".$penalty_info['idx']."'";
				$up = updateQuery($sql);
				if($up){
					echo "complete";
					exit;
				}

			}else{

				$sql = "select idx from work_penalty_list where state='0' and kind_flag='".$kind."' and companyno='".$companyno."' and email='".$user_id."' and convert(char(10), regdate, 120)='".TODATE."'";
				$pl_info = selectQuery($sql);
				if($pl_info['idx']){
					$sql = "update work_penalty_list set state='1', comdate=".DBDATE." where state='0' and idx='".$pl_info['idx']."'";
					$up = updateQuery($sql);
				}

				$sql = "insert into work_filesinfo_img_penalty(state, kind, link_idx, email, file_path, file_name, file_size, file_ori_path, file_ori_name, file_ori_size, file_real_name, file_type, type_flag, ip)";
				$sql = $sql .= " values('0','".$kind."','".$pl_info['idx']."', '".$user_id."','".$file_path."','".$renamefile."','".$file_resize."' ,'".$file_path_ori."','".$renamefile."','".$file_size."','".$file_name."','".$file_type."','".$type_flag."','".LIP."')";
				$files_idx = insertIdxQuery($sql);
				if($files_idx){
					echo "complete";
					exit;
				}
			}
		}
	}

	exit;
}


//페널티 알림 발송
if($mode == "penalty_send_alarm"){

	$penalty_send = $_POST['penalty_send'];
	if($penalty_send == '1'){
		//penalty_info_notice();
		
		//알림 보낼 아이디 조회
		$highlevel = '0';

		//관리권한 아이디 조회
		//$sql = "select idx, email, part, partno, companyno from work_member where state='0' and highlevel='".$highlevel."'";
		$sql = "select idx, email, name, part, partno from work_member where state='0' and idx='47'";
		$work_meminfo = selectAllQuery($sql);

		//회원정보 가져옴
		$to_user_info = member_row_info($user_id);


		//회원 전체 목록
		for($i=0; $i<count($work_meminfo['idx']); $i++){
			$arr_user_id = $work_meminfo['email'][$i];
			$arr_user_name = $work_meminfo['name'][$i];

			$arr_part = $work_meminfo['part'][$i];
			$arr_partno = $work_meminfo['partno'][$i];

			$notice_flag = '2';
			$sql = "select idx from work_todaywork where notice_flag='".$notice_flag."' and workdate='".TODATE."' and email='".$arr_user_id."'";
			$info = selectQuery($sql);
			$contents = $to_user_info['name'] ."님이 지각페널티 카드를 완료하지 않았습니다.";
			if(!$info['idx']){
				$sql = "insert into work_todaywork(highlevel, work_flag, part_flag, notice_flag, work_idx, email, name, part, contents, ip, workdate) values(";
				$sql = $sql .= "'".$highlevel."','2','".$arr_partno."','".$notice_flag."','".$to_user_info['idx']."','".$arr_user_id."','".$arr_user_name."','".$arr_part."','".$contents."','".LIP."','".TODATE."')";
				$insert_idx = insertIdxQuery($sql);
				if($insert_idx){
					echo "complete|".$sql;
					exit;
				}
			}else{
				echo "com|";
				exit;
			}
		}
	/*}else if($penalty_send == '2'){


		//알림 보낼 아이디 조회
		$highlevel = '0';

		//관리권한 아이디 조회
		//$sql = "select idx, email, part, partno, companyno from work_member where state='0' and highlevel='".$highlevel."'";
		$sql = "select idx, email, name, part, partno from work_member where state='0' and idx='47'";
		$work_meminfo = selectAllQuery($sql);

		//회원정보 가져옴
		$to_user_info = member_row_info($user_id);

		//회원 전체 목록
		for($i=0; $i<count($work_meminfo['idx']); $i++){
			$arr_user_id = $work_meminfo['email'][$i];
			$arr_user_name = $work_meminfo['name'][$i];

			$arr_part = $work_meminfo['part'][$i];
			$arr_partno = $work_meminfo['partno'][$i];

			$notice_flag = '2';
			$sql = "select idx from work_todaywork where state='0' and notice_flag='".$notice_flag."' and workdate='".TODATE."' and email='".$arr_user_id."'";
			$info = selectQuery($sql);
			$contents = $to_user_info['name'] ."님이 지각페널티 카드를 완료하지 않았습니다.";
			if(!$info['idx']){
				$sql = "insert into work_todaywork(highlevel, work_flag, part_flag, notice_flag, work_idx, email, name, part, contents, ip, workdate) values(";
				$sql = $sql .= "'".$highlevel."','2','".$arr_partno."','".$notice_flag."','".$to_user_info['idx']."','".$arr_user_id."','".$arr_user_name."','".$arr_part."','".$contents."','".LIP."','".TODATE."')";
				$insert_idx = insertIdxQuery($sql);
				if($insert_idx){
					echo "complete|".$sql;
					exit;
				}
			}else{
				echo "com|";
				exit;
			}
		}
	*/

	}
	
}



?>