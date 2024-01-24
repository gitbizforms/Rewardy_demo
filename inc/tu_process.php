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

	// 도전내역 있는지
	$sql = "select idx, t_flag from work_member where state = '0' and companyno='".$companyno."' and email = '".$user_id."'";
	$t_use_flag = selectQuery($sql);


	if($t_use_flag['t_flag']==null){
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
		}
		echo "complete";
		exit;
	}else{
		echo $t_use_flag['t_flag'];
		exit;
	}
	exit;
}


//튜토리얼 업데이트
if($mode == "update"){

	//튜토리얼 레벨
	$level = $_POST['level'];

	//코인금액
	$coin = 100;

	//레벨값 배열선언
	//오늘업무=1, 좋아요=2, 코인=3, 파티=4, 챌린지=5, 메인=6
	$level_num = 
	array(
		'work'			=>	1
		,'like'			=>	2
		,'coin'			=>	3
		,'party'		=>	4
		,'challenge'	=>	5
		,'main'			=>	6
	);

	//레벨값이 있을경우
	if($level){

		$t_flag = $level_num[$level];
		$t_flag = preg_replace("/[^0-9]/", "", $t_flag);

		$sql = "select idx from work_member where state='0' and companyno='".$companyno."' and email='".$user_id."'";
		$member_info = selectQuery($sql);

		//타임스탬프 현재 날짜
		$time_stamp = date('Y-m-d H:i:s');
		
		//보상한 아이디
		$user_reward_id = 'marketing@bizforms.co.kr';

		//보상한 이름
		$user_reward_name = '리워디';

		//튜토리얼 보상 코드값(테이블 work_coin_reward 참고)
		$code ='1130';

		$reward_type = 'tutorial';
		if($member_info['idx']){
			$sql = "update work_member set t_flag='".$t_flag."', t_time = '".$time_stamp."' where idx='".$member_info['idx']."'";
			$res = updateQuery($sql);
			if($res){

				//오늘업무 완료
				if($t_flag == 1){

					$coin_info = '튜토리얼 오늘업무 완료';
					//역량평가지표(실행)
					work_cp_reward("tutorial","0001", $user_id , $member_info['idx']);

				//좋아요 완료
				}else if($t_flag == 2){

					$coin_info = '튜토리얼 좋아요 완료';
					//역량평가지표(실행)
					// work_cp_reward("tutorial","0002", $user_id , $member_info['idx']);

					//타임라인(좋아요)
					work_data_log('0','8', $member_info['idx'], $user_reward_id, $user_reward_name, $user_id, $user_name);

					//타임라인(좋아요 받음)
					work_data_log('0','10', $member_info['idx'], $user_id, $user_name, $user_reward_id, $user_reward_name);

					$sql = "insert into work_todaywork_like (state, companyno, kind_flag, service, work_idx, like_flag, email, name, send_email, send_name, comment, type_flag, ip, workdate, regdate)"; 
					$sql = $sql .= " values('0', '".$companyno."', '2', 'tutorial', '0', '0', '".$user_id."', '".$user_name."', '".$user_reward_id."', '".$user_reward_name."', '튜토리얼을 응원합니다!', '0', '".LIP."', '".TODATE."', ".DBDATE.")";
					$insert_like = insertIdxQuery($sql);
					
				//코인보상 완료
				}else if($t_flag == 3){

					$coin_info = '튜토리얼 코인보상 완료';
					//역량평가지표(실행)
					work_cp_reward("tutorial","0003", $user_id , $member_info['idx']);

				//파티체험 완료
				}else if($t_flag == 4){

					$coin_info = '튜토리얼 파티체험 완료';
					//역량평가지표(실행)
					work_cp_reward("tutorial","0004", $user_id , $member_info['idx']);

				//챌린지 도전 완료
				}else if($t_flag == 5){

					$coin_info = '튜토리얼 챌린지도전 완료';
					//역량평가지표(실행)
					work_cp_reward("tutorial","0005", $user_id , $member_info['idx']);
					
				//메인 완료
				}else if($t_flag == 6){

					$coin_info = '튜토리얼 메인 완료';
					//역량평가지표(실행)
					// work_cp_reward("tutorial","0006", $user_id , $member_info['idx']);

					//타임라인(좋아요)
					work_data_log('0','8', $member_info['idx'], $user_reward_id, $user_reward_name, $user_id, $user_name);

					//타임라인(좋아요 받음)
					work_data_log('0','10', $member_info['idx'], $user_id, $user_name, $user_reward_id, $user_reward_name);

					$sql = "insert into work_todaywork_like (state, companyno, kind_flag, service, work_idx, like_flag, email, name, send_email, send_name, comment, type_flag, ip, workdate, regdate)"; 
					$sql = $sql .= " values('0', '".$companyno."', '2', 'tutorial', '0', '0', '".$user_id."', '".$user_name."', '".$user_reward_id."', '".$user_reward_name."', '잘 하셨습니다! 이제 본격적으로 리워디를 이용해보세요', '0', '".LIP."', '".TODATE."', ".DBDATE.")";
					$insert_like = insertIdxQuery($sql);
				}

				//코인내역저장 + 회원 코인 지급
				$sql = "insert into work_coininfo(state, companyno, code, reward_type, email, name, reward_user, reward_name, coin, memo, workdate, ip) values('0', '".$companyno."', '".$code."', '".$reward_type."', '".$user_id."', '".$user_name."', '".$user_reward_id."', '".$user_reward_name."','".$coin."','".$coin_info."','".TODATE."','".LIP."')";
				$coininfo = insertQuery($sql);
				if($coininfo){
					$sql = "update work_member set coin = coin + ".$coin." where idx = '".$member_info['idx']."'";
					$res = updateQuery($sql);
				}
			}

			echo "complete";
			exit;
		}
	}
}else if($mode == "location"){

	$sql = "select idx,t_flag from work_member where state = '0' and companyno='".$companyno."' and email='".$user_id."'";
	$level_info = selectQuery($sql);

	if($level_info['t_flag']){
		echo $level_info['t_flag'];
	}else{
		echo "none";
	}
}
?>

