<?php
//튜토리얼 데이터 저장하기 페이지 입니다.
//데이터는 오늘업무, 공유업무 2가지 등록 됩니다.

$home_dir = str_replace( basename(__DIR__) , "" , __DIR__ );
include $home_dir . "inc_lude/conf_mysqli.php";
include DBCON_MYSQLI;
include FUNC_MYSQLI;

//mode값이 없을경우 중지처리
if(!$_POST["mode"]){
//	echo "out";
//	exit;
}

$mode = $_POST['mode'];

//튜토리얼 인서트
if($mode == "insert"){

	//튜토리얼 상태값
	$state = "99";

	$type_flag = ($chkMobile)?1:0;				//구분(0:사이트, 1:모바일)

	//업무구분(나의업무:2, 보고업무:1, 요청업무:3, 공유업무:2 && share_flag=1)
	$work_flag = '2';

	//제목
	$work_title = null;

	//내용
	$contents = '튜토리얼 오늘업무를 시작 합니다.';

	//연차, 반차
	$decide_flag = '0';

	//파일 플래그
	$file_flag = '0';

	$sql = "select idx from work_todaywork where state='".$state."' and work_flag='".$work_flag."' and email='".$user_id."' and workdate='".TODATE."' limit 1";
	$work_info = selectQuery($sql);
	if(!$work_info['idx']){
		$sql = "insert into work_todaywork(state,companyno, email, name, highlevel, type_flag, work_flag, part_flag, part, title, decide_flag, file_flag, contents, workdate, ip)";
		$sql = $sql .=" values('".$state."','".$companyno."', '".$user_id."','".$user_name."','".$user_level."','".$type_flag."','".$work_flag."','".$user_part."','".$part_name."', '".$work_title."', '".$decide_flag."','".$file_flag."', N'".$contents."','".$workdate."','".LIP."')";
		$res_idx = insertIdxQuery($sql);
	}


	//업무구분(나의업무:2, 보고업무:1, 요청업무:3, 공유업무:2 && share_flag=1)
	$work_flag='2';


	//제목
	$work_title = null;

	//내용
	$contents = '튜토리얼 오늘업무 공유하기 입니다.';

	//연차, 반차
	$decide_flag = '0';

	//파일 플래그
	$file_flag = '0';

	$workdate = TODATE;

	//공유받기
	$share_flag = 2;
	$sql = "select idx from work_todaywork where state='".$state."' and work_flag='".$work_flag."' and share_flag='".$share_flag."' and work_idx is not null and email='".$user_id."' and workdate='".TODATE."' limit 1";
	$work_info = selectQuery($sql);
	if(!$work_info['idx']){

		$sql = "insert into work_todaywork(state,companyno, email, name, highlevel, type_flag, work_flag, share_flag, part_flag, part, title, decide_flag, file_flag, contents, workdate, ip)";
		$sql = $sql .=" values('".$state."','".$companyno."', '".$user_id."','".$user_name."','".$user_level."','".$type_flag."','".$work_flag."','".$share_flag."','".$user_part."','".$part_name."', '".$work_title."', '".$decide_flag."','".$file_flag."', N'".$contents."','".$workdate."','".LIP."')";
		$res_idx = insertIdxQuery($sql);

		//공유받음 사용자
		$work_user_chk = '30';

		if($work_user_chk){
			$work_mem_idx = trim($work_user_chk);
			$sql = "select idx, email, name, part, partno, highlevel from work_member where state='0' and companyno='".$companyno."' and idx in (".$work_mem_idx.")";
			$work_mem_info = selectAllQuery($sql);
		}


		$sql = "update work_todaywork set work_idx='".$res_idx."' where idx='".$res_idx."'";
		updateQuery($sql);

		//공유하기
		$share_send_flag = 1;

		for($i=0; $i<count($work_mem_info['idx']); $i++){

			//회원이메일
			$work_mem_email = $work_mem_info['email'][$i];

			//회원이름
			$work_mem_name = $work_mem_info['name'][$i];

			//부서명
			$mem_part = $work_mem_part[$work_mem_email];

			//부서번호
			$mem_partno = $work_mem_partno[$work_mem_email];

			//회원레벨
			$mem_highlevel = $work_mem_highlevel[$work_mem_email];


			$sql = "select idx from work_todaywork_share where state='0' and companyno='".$companyno."' and work_idx='".$res_idx."' and email='".$work_mem_email."'";
			$work_row = selectQuery($sql);
			if(!$work_row['idx']){
				$sql = "insert into work_todaywork_share(companyno, work_idx, work_email, work_name, email, name, workdate, ip) values('".$companyno."','".$res_idx."','".$work_mem_email."','".$work_mem_name."','".$user_id."','".$user_name."','".$workdate."','".LIP."')";
				$res_share_insert = insertIdxQuery($sql);
			}


			$sql = "insert into work_todaywork(state, companyno, email, name, highlevel, type_flag, work_flag, share_flag, part_flag, part, work_idx, title, contents, workdate, ip)";
			$sql = $sql .=" values('".$state."', '".$companyno."','".$work_mem_email."','".$work_mem_name."','".$mem_highlevel."','".$type_flag."','".$work_flag."','".$share_send_flag."','".$mem_partno."','".$mem_part."', '".$res_idx."','".$work_title."', N'".$contents."','".$workdate."','".LIP."')";
			$insert_idx = insertIdxQuery($sql);


		}

		//완료처리
		if($res_idx){
			echo "complete";
			exit;
		}

	}

}
?>