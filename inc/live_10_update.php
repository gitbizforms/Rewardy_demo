<?php

	//회원정보 업데이트 처리
	//업무시작 시간 다음날 자정에 정보 업데이트 합니다.

	$home_dir = str_replace( basename(__DIR__) , "" , __DIR__ );
	include $home_dir . "inc_lude/conf_mysqli.php";
	include $home_dir . "inc/SHA256/KISA_SHA256.php";
	include DBCON_MYSQLI;
	include FUNC_MYSQLI;


	$mode = $_GET['mode'];
	if($mode == "lives_10"){

		//회원정보 리스트 : 정상회원(state=0), 출근시간, 퇴근시간, 초기화시간(출근시간 + 1day 자정으로 설정)
		//초기화시간 = 업무시작시간 +1 day 00:00:00
		$sql = "select idx, live_1, live_4, CAST(live_1_regdate AS char) as live_1_regdate, CAST(live_4_regdate AS char) as live_4_regdate, DATE_FORMAT(now(), '%Y-%m-%d %H:%i:%s') as today_time,";
		$sql = $sql .=" DATE_FORMAT(DATE_ADD(DATE_FORMAT(live_1_regdate, '%Y-%m-%d'), INTERVAL 1 DAY), '%Y-%m-%d %H:%i:%s') as reset_time1,";
		$sql = $sql .=" DATE_FORMAT(DATE_ADD(DATE_FORMAT(live_4_regdate, '%Y-%m-%d'), INTERVAL 1 DAY), '%Y-%m-%d %H:%i:%s') as reset_time4 from work_member where state='0' order by live_1_regdate desc";

		echo $sql;
		exit;
		$member_info = selectAllQuery($sql);

	
		if($member_info['idx']){
			for($i=0; $i<count($member_info['idx']); $i++){

				//출근
				$live_1 = $member_info['live_1'][$i];

				//퇴근
				$live_4 = $member_info['live_4'][$i];

				//출근시간
				$live_1_regdate = $member_info['live_1_regdate'][$i];

				//퇴근시간
				$live_4_regdate = $member_info['live_4_regdate'][$i];

				//현재시간
				$today_time = $member_info['today_time'][$i];

				//초기화시간 = 업무시작시간 +1 day 00:00:00
				$reset_time1 = $member_info['reset_time1'][$i];
				
				//초기화시간 = 퇴근시간 +1 day 00:00:00
				$reset_time4 = $member_info['reset_time4'][$i];


				//업무시작 시간 && 현재시간이 업무 초기화시간(다음날 자정:00:00:00) 보다 큰경우 초기화 처리
				if($live_1 == "1" && $live_1_regdate){
					if($today_time > $reset_time1){
						$sql = "update work_member set live_1='0', live_2='0', live_3='0', live_4='0', live_1_regdate=NULL, live_2_regdate=NULL, live_3_regdate=NULL, live_4_regdate=NULL where idx='".$member_info['idx'][$i]."'";
						$up = updateQuery($sql);
						if($up){
							echo "ok";
						}
					}else{
						echo "N1";
					}
				}


				//퇴근한 시간 초기화
				//퇴근시간&& 현재시간이 업무 초기화시간(다음날 자정:00:00:00) 보다 큰 경우 초기화 처리
				if($live_4 == "1" && $live_4_regdate){
					if ($today_time > $reset_time4){
						$sql = "update work_member set live_1='0', live_2='0', live_3='0', live_4='0', live_1_regdate=NULL, live_2_regdate=NULL, live_3_regdate=NULL, live_4_regdate=NULL where idx='".$member_info['idx'][$i]."'";
						$up = updateQuery($sql);
						if($up){
							echo "ok";
						}
					}else{
						echo "N2";
					}
				}
			}
		}


		//메인좋아요 지표
		main_like_cp();

		$time = date("Y-m-d H:i:s" , TODAYTIME);
		$log = "업데이트 시간 : ".$time ." (좋아요 지표)";
		write_log_dir($log , "update");
		exit;
	}
?>