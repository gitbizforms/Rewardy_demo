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

$mode = $_POST["mode"];					//mode값 전달받음
$type_flag = ($chkMobile)?1:0;				//구분(0:사이트, 1:모바일)

if($_COOKIE){
	$user_id = $_COOKIE['user_id'];
	$user_name = $_COOKIE['user_name'];
	$user_level = $_COOKIE['user_level'];
	$user_part = $_COOKIE['user_part'];
}


if($mode == "main_live_list"){

	$input_val = $_POST['input_val'];
	$live_2_switch = $_POST['live_2_switch'];
	$live_3_switch = $_POST['live_3_switch'];
	$category = $_POST['category'];
	//이름, 부서명 검색
	if($input_val){
		$where = " and (name like '%".$input_val."%' or part like '%".$input_val."%')";
	}
	//메인 전체 회원 리스트
	$member_list_info = member_main_cate_team_list($category);
	//페널티 횟수체크
	//$penalty_info_cnt = penalty_info_cnt();

	if($member_list_info['idx']){
		$member_all_cnt = number_format(count($member_list_info['idx']));
	}else{
		$member_all_cnt = 0;
	}


	//오늘 업무 작성 회원수
	$sql = "select email from work_todaywork where state in('0','1') and work_flag!='2' and workdate=DATE_FORMAT(now(), '%Y-%m-%d')".$where." group by email order by min(regdate) asc";
	$member_live_info = selectAllQuery($sql);
	if($member_live_info['email']){
		$member_live_cnt = number_format(count($member_live_info['email']));
	}else{
		$member_live_cnt = 0;
	}
	
	//오늘업무 등록,완료갯수
	$sql = "select email , state, work_flag, decide_flag, count(1) as cnt from work_todaywork where state!='9' and workdate='".TODATE."' group by email, state, work_flag, decide_flag";
	$works_myinfo = selectAllQuery($sql);
	if($works_myinfo['email']){
		for($i=0; $i<count($works_myinfo['email']); $i++){

			$works_myinfo_email = $works_myinfo['email'][$i];
			$work_list[TODATE][$works_myinfo_email][$works_myinfo['state'][$i]] = $works_myinfo['cnt'][$i];
			

			//업무예약일정- 일정이 있을때 완료가 아닌경우에만 나오도록 처리
			if($works_myinfo['work_flag'][$i] =='2' && $works_myinfo['decide_flag'][$i] != '0' && $works_myinfo['state'][$i]=='0'){
				$work_flag_list[TODATE][$works_myinfo_email][] = $works_myinfo['decide_flag'][$i];
			}
		}

		//오늘업무 전체갯수
		$work_all_list = number_format($work_list[TODATE][$user_id][0] + $work_list[TODATE][$user_id][1]);

		//오늘업무 완료갯수
		$work_com_list = number_format($work_list[TODATE][$user_id][1]);

	}else{
		$work_all_list = 0;
		$work_com_list = 0;
	}


	//업무예약
	$sql = "select idx, title from work_decide where state='0' and type_flag='0'";
	$work_decide_info = selectAllQuery($sql);
	if($work_decide_info['idx']){
		$work_decide_list = @array_combine($work_decide_info['idx'], $work_decide_info['title']);
	}


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


	if($member_list_info['idx']){
		for($i=0; $i<count($member_list_info['idx']); $i++){

			$member_list_email = $member_list_info['email'][$i];
			$gender = $member_list_info['gender'][$i];

			$member_list_name = $member_list_info['name'][$i];
			$member_list_part = $member_list_info['part'][$i];
			
			$live_1_time = $member_list_info['live_1_time'][$i];
			$ex_live_1_time = "";
			if($live_1_time){
				$tmp_live_1_time = explode(":", $live_1_time);
				if($tmp_live_1_time){
					$ex_live_1_time = (int)$tmp_live_1_time[0].":" .$tmp_live_1_time[1] ."";
				}
			}

			$member_list_live_1 = $member_list_info['live_1'][$i];
			$member_list_live_2 = $member_list_info['live_2'][$i];
			$member_list_live_3 = $member_list_info['live_3'][$i];
			$member_list_live_4 = $member_list_info['live_4'][$i];
			$member_list_live_1_time = $member_list_info['live_1_time'][$i];

			$profile_type = $member_list_info['profile_type'][$i];
			$profile_img_idx = $member_list_info['profile_img_idx'][$i];
			$profile_file = $member_list_info['file_path'][$i].$member_list_info['file_name'][$i];
			$profile_img =  'http://demo.rewardy.co.kr'.$member_list_info['file_path'][$i].$member_list_info['file_name'][$i];


			//퇴근
			$ex_tmp_member_list_live_1_time = "";
			if($member_list_live_1=='0'){
				if($member_list_live_4=='1'){
					$ex_tmp_member_list_live_1_time = "";
				}
			}else{
				if($member_list_live_1_time){
					$tmp_member_list_live_1_time = explode(":", $member_list_live_1_time);
					if($tmp_member_list_live_1_time){
						$ex_tmp_member_list_live_1_time = (int)$tmp_member_list_live_1_time[0].":" .$tmp_member_list_live_1_time[1] ."";
					}
				}
			}

			//업무일정이 있는경우
			if ($work_flag_list[$today][$member_list_email]){
				$member_decide_cnt = count($work_flag_list[$today][$member_list_email]);
				for($j=0; $j<$member_decide_cnt; $j++){
					$member_decide_flag[$j] = $work_flag_list[$today][$member_list_email][$j];
				}
			}else{
				$member_decide_flag = null;
				$member_decide_cnt = 0;
			}

			if ($member_list_live_1=="0" || $member_list_live_1==null){
																
				if($member_list_live_4 == "1"){
					$live_class = "";
				}else{
					$live_class = " live_none";
				}

			}else{
				$live_class = "";
			}
		?>

			<?if($i < 4){?>
			<li class="live_list_box<?=$live_class?>" id="live_user_list">
				<div class="live_list_t">
					<div class="live_list_user_img">
						<div class="live_circle circle_01"></div>
						<div class="live_list_user_img_bg"></div>
						<div class="live_list_user_imgs" style="background-image:url('<?=$profile_file?$profile_img:"/html/images/pre/img_prof_default.png"?>');"></div>
					</div>
					<div class="live_user_state">
						<div class="live_user_state_in">
							<ul>
								<?if($member_list_live_4 == "1"){?>
									<li class="state_03">
										<div class="live_user_state_circle">
											<strong>퇴근</strong>
										</div>
										<div class="layer_state layer_state_03">
											<div class="layer_state_in">
												<p>업무를 끝내고 퇴근했습니다.</p>
												<em></em>
											</div>
										</div>
									</li>
								<?}else if ($member_decide_flag){
									if($category == 'all'){
										//일정이 1개 일때
										if( count($member_decide_flag) == '1'){?>
											<li class="state_05">
												<div class="live_user_state_circle">
													<strong><?=$work_decide_list[$work_flag_list[$today][$member_list_email][0]]?></strong>
												</div>
											</li>
										<?}else{?>
											<li class="state_04">
												<div class="live_user_state_circle">
													<strong>일정</strong>
													<span><?=$member_decide_cnt?></span>
												</div>
												<div class="layer_state layer_state_04">
													<div class="layer_state_in">
														<p><span>
															<?for($k=0; $k<$member_decide_cnt; $k++){
																echo $work_decide_list[$work_flag_list[$today][$member_list_email][$k]];
																if($work_flag_list[$today][$member_list_email][$k] != end($work_flag_list[$today][$member_list_email])){
																	$comma = ", ";
																}else{
																	$comma = "";
																}
																echo $comma;
															}?></span> 일정이 있습니다.</p>
														<em></em>
													</div>
												</div>
											</li>
										<?}?>
									<?}?>
								<?}?>
							</ul>
						</div>
					</div>
				</div>

				<div class="live_list_m">
					<div class="live_user_name">
						<strong><?=$member_list_name?></strong>
						<em><?=$ex_tmp_member_list_live_1_time?></em>
					</div>
				</div>
			</li>
		<?}else{?>
			<li class="live_list_box" id="live_user_list">
				<div class="live_list_more_img">
					<div class="live_list_more"></div>
				</div>
			</li>
		<?}?>
		<?php
		}?>|<?=$member_live_cnt?>|<?=$member_all_cnt?><?php
	}else{?>	
			<div class="tdw_list_none">
				<strong><span>현재 검색된 결과가 없습니다.</span></strong>
			</div>|0|0<?php }
	exit;
}



//업무시작
if($mode == "live_1_change"){
	//현재 업무 작성 경고 회수
	$work_count = $_POST['work_count'];

	//현재 퇴근소감 경고 회수	
	$out_count = $_POST['out_count'];

	//현재 퇴근소감 경고 회수	
	$chall_count = $_POST['chall_count'];

	$status = $_POST['status'];
	
	//현재시간
	$now_time = date("H:i:s"); 

	//현재날짜
	$now_date = date("Y-m-d H:i:s");

	//현재 년월일
	$now_day = date("Y-m-d");

	// 지각 적용 시간
	$penalty_time = "09:31:00";

	//업무시작 ON
	if($status == "true"){
		$sql = "select idx, live_1, penalty_state, DATE_FORMAT(regdate, '%H:%i') as reg, DATE_FORMAT(live_1_regdate, '%Y-%m-%d %H:%i:%s') as live_1_regdate,  DATE_FORMAT(live_1_regdate, '%Y-%m-%d') as live_1_workdate from work_member where state='0' and companyno='".$companyno."' and email='".$user_id."' and live_1='0'";
		$mem_info = selectQuery($sql);

		$sql = "select * from work_member_penalty where companyno='".$companyno."' and email='".$user_id."'";
		$pen_info = selectQuery($sql);

		// 회사 패널티 사용 여부 체크
		$sql = "select idx, state, penalty, penalty_in, intime from work_company where idx = '".$companyno."' and state ='0' and penalty = '1' and penalty_in = '1' ";
		$com_penalty = selectQuery($sql);

		// 지각 적용 시간
		$time = $com_penalty['intime'];
		$penalty_time = date("H:i:s", strtotime($time.'+1 minute')); 

		if($mem_info['idx']){

			//오늘 출근 시간
			$member_login_time = member_login_log_chk();
			if($member_login_time){
				$mem_login_time = $member_login_time['reg'];
				
				$ex_time = explode(":", $member_login_time['regtime']);
				if($ex_time){
					$time_s = (int)$ex_time[0];
					$time_e = (int)$ex_time[1];
					$login_time = $time_s .":". $time_e;
				}

				$sql = "update work_member set live_1='1', live_4='0', live_1_regdate=CAST('".$mem_login_time."' AS datetime), live_4_regdate=null where idx='".$mem_info['idx']."'";
				$up = updateQuery($sql);
			}else{

				//업무 최초 시작 처리
				if($mem_info["live_1_workdate"] != TODATE && $mem_info["live_1"] != '1'){
					if($mem_info['penalty_state']>0){ // 패널티 적용 후 다음날 출근시 해제
						$sql = "update work_member set penalty_state = penalty_state - 1 where state = '0' and companyno='".$companyno."' and email='".$user_id."'";
						$penalty_cancel = updateQuery($sql);
					}

					if($work_count >= 3 && $com_penalty['penalty_work']=='1'){// 업무 2회이상 미작성 3회 누적시 패널티 발동
						$sql = "update work_member set penalty_state = penalty_state + 1 where email = '".$user_id."' and state = '0' and companyno = '".$companyno."' ";
						$update_penalty = updateQuery($sql);
	
						$sql = "update work_member_penalty set state = '9' where work = '1' and email = '".$user_id."' and companyno = '".$companyno."' and idx > 0 ";
						$update_workcount = updateQuery($sql);
					}
					
					if($out_count >= 3 && $com_penalty['penalty_out']=='1'){// 퇴근소감 미작성 3회 누적시 패널티 발동
						$sql = "update work_member set penalty_state = penalty_state + 1 where email = '".$user_id."' and state = '0' and companyno = '".$companyno."' ";
						$update_penalty = updateQuery($sql);
	
						$sql = "update work_member_penalty set state = '9' where outcount = '1' and email = '".$user_id."' and companyno = '".$companyno."' and idx > 0";
						$update_outcount = updateQuery($sql);
					}

					if($chall_count >= 3 && $com_penalty['penalty_out']=='1'){// 퇴근소감 미작성 3회 누적시 패널티 발동
						$sql = "update work_member set penalty_state = penalty_state + 1 where email = '".$user_id."' and state = '0' and companyno = '".$companyno."' ";
						$update_penalty = updateQuery($sql);
	
						$sql = "update work_member_penalty set state = '9' where outcount = '1' and email = '".$user_id."' and companyno = '".$companyno."' and idx > 0";
						$update_chall = updateQuery($sql);
					}
					
					$sql = "update work_member set live_1='1', live_4='0', live_1_regdate=".DBDATE." , live_4_regdate=null where idx='".$mem_info['idx']."'";
					$up = updateQuery($sql);

					if($up&&$com_penalty['penalty_in']=='1'){//해당 기업이 패널티를 사용할 경우 패널티로그 기록
						if($now_time > $penalty_time){
							// 매일 첫 출근시 출근 시간 이후에 출근할 경우 (2023.11.10)
								$sql = "insert into work_member_penalty (email, name, state, companyno, incount, updatetime) values ('".$user_id."', '".$user_name."', '0', '".$companyno."', '1', '".$now_date."')";
								$penalty_in = insertIdxQuery($sql);

								$sql = "select idx, email, name, sum(incount) as incount from work_member_penalty where incount = '1' and state = '0' and email = '".$user_id."' and companyno = '".$companyno."' ";
								$query = selectQuery($sql);
								if($query['incount']<3){
									$inTitle = "[앗! 출근시간!]";
									$tokenInMessage = "최근 5일간 출근도장을 ".$query['incount']."번 빠뜨리셨어요. ㅠㅠ 3회 이상이면, 페널티를 받을 수 있으니 조심하세요~";
									pushToken($inTitle,$tokenInMessage,$query['email'],'incount','29',$query['email'],$query['name'],$query['idx']);
								}else if($query['incount']>=3){
									$sql = "update work_member set penalty_state = penalty_state + 1 where email = '".$user_id."' and state = '0' and companyno = '".$companyno."' ";
									$update_penalty = updateQuery($sql);

									$sql = "update work_member_penalty set state = '9' where incount = '1' and email = '".$user_id."' and companyno = '".$companyno."' ";
									$update_incount = updateQuery($sql);

									echo "stack";
								}
								// if($penalty_in){
							// 	echo "stack";
							// }
						}
					}

					// 알림 추가 작업	
					$sql = " select idx, name, email, sum(outcount) as outcount, sum(work) as work, sum(challenge) as challenge from work_member_penalty where ";
					$sql = $sql .= "companyno = '".$companyno."' and email = '".$user_id."' and state = '0'";
					$penalty_test = selectQuery($sql);
					$sql = $sql .= " and DATE_FORMAT(updatetime, '%Y-%m-%d') = '".$now_day."'";
					$today_pen = selectQuery($sql);

					$workTitle = "[압! 오늘업무!]";
					$outTitle = "[헙! 퇴근기록]";
					$challTitle = "[아차! 챌린지]";

					if($penalty_test['work'] > 0 && $today_pen['work'] > 0) {
							$tokenWorkMessage = "최근 5일간 오늘업무 작성을 ".$penalty_test['work']."번 빠뜨리셨어요. ㅠㅠ 3번 이상이면 페널티를 받을 수 있으니 조심하세요~";
							pushToken($workTitle,$tokenWorkMessage,$penalty_test['email'],'work','30',$penalty_test['email'],$penalty_test['name'],$penalty_test['idx']);
					}else if($penalty_test['outcount'] > 0 && $today_pen['outcount'] > 0){
							$tokenOutMessage = "최근 5일간 퇴근소감을 ".$penalty_test['outcount']."번 빠뜨리셨어요. ㅠㅠ 3번 이상이면 페널티를 받을 수 있으니 조심하세요~";
							pushToken($outTitle,$tokenOutMessage,$penalty_test['email'],'outcount','31',$penalty_test['email'],$penalty_test['name'],$penalty_test['idx']);
					}else if($penalty_test['challenge'] > 0 && $today_pen['challenge'] > 0){
							$tokenChallMessage = "최근 5일간 참여하지 않은 챌린지가 ".$penalty_test['challenge']."개가 있어요~ 참여하지 않은 챌린지가 3개가 되면 페널티를 받을 수 있으니 조심하세요~";
							pushToken($challTitle,$tokenChallMessage,$penalty_test['email'],'challenge','32',$penalty_test['email'],$penalty_test['name'],$penalty_test['idx']);
					}
				}

				//로그인 로그 저장
				//리턴값없이 저장함
				//inc_lude/func.php 설정됨
				member_login_log();
			}

			if($up){
				//역량 평가 지표 처리(live 업무시작, 0001, 회원idx)
				work_cp_reward("live","0001", $user_id, $mem_info['idx']);
				echo "|true|". $login_time;
				exit;
			}
		}

	//업무시작 OFF
	}else if($status == "false"){
		$sql = "select idx, live_1 from work_member where state='0' and companyno='".$companyno."' and email='".$user_id."' and live_1='1'";
		$mem_info = selectQuery($sql);
		if($mem_info['idx']){
			$sql = "update work_member set live_1='0', live_4='0', live_1_regdate=null, live_4_regdate=null where idx='".$mem_info['idx']."'";
			$up = updateQuery($sql);
			if($up){
				echo "true|".$sql;
				exit;
			}
		}
	}
	exit;
}



//집중모드
if($mode == "live_2_change"){

	$status = $_POST['status'];
	//집중모드 ON
	if($status == "true"){
		$sql = "select idx, live_1 from work_member where state='0' and companyno='".$companyno."' and email='".$user_id."'";
		$mem_info = selectQuery($sql);
		if($mem_info['idx']){
			$sql = "update work_member set live_1='1', live_2='1', live_3='0', live_4='0', live_2_regdate=".DBDATE.", live_3_regdate=null, live_4_regdate=null where idx='".$mem_info['idx']."'";
			$up = updateQuery($sql);
			if($up){

				//역량 평가 지표 처리(live 집중, 0001, 회원idx)
				work_cp_reward("live","0002", $user_id, $mem_info['idx']);
				echo "true|";
				exit;
			}
		}

	//집중모드 OFF
	}else if($status == "false"){
		$sql = "select idx from work_member where state='0' and companyno='".$companyno."' and email='".$user_id."'";
		$mem_info = selectQuery($sql);
		if($mem_info['idx']){
			$sql = "update work_member set live_1='1', live_2='0', live_3='0', live_4='0', live_2_regdate=null, live_3_regdate=null, live_4_regdate=null where idx='".$mem_info['idx']."'";
			$up = updateQuery($sql);
			if($up){
				echo "true|";
				exit;
			}
		}
	}
	exit;
}



//자리비움
if($mode == "live_3_change"){

	$status = $_POST['status'];

	//자리비움 ON
	if($status == "true"){
		$sql = "select idx, live_1 from work_member where state='0' and companyno='".$companyno."' and email='".$user_id."'";
		$mem_info = selectQuery($sql);
		if($mem_info['idx']){
			$sql = "update work_member set live_1='1', live_2='0', live_3='1', live_2_regdate=null, live_3_regdate=".DBDATE." where idx='".$mem_info['idx']."'";
			$up = updateQuery($sql);
			if($up){

				//역량 평가 지표 처리(live 자리비움, 0001, 회원idx)
				work_cp_reward("live","0003",$user_id, $mem_info['idx']);
				echo "true|".$sql;
				exit;
			}
		}

	//자리비움 OFF
	}else if($status == "false"){
		$sql = "select idx, live_1 from work_member where state='0' and companyno='".$companyno."' and email='".$user_id."'";
		$mem_info = selectQuery($sql);
		if($mem_info['idx']){
			$sql = "update work_member set live_1='1', live_2='0', live_3='0', live_2_regdate=null, live_3_regdate=null where idx='".$mem_info['idx']."'";
			$up = updateQuery($sql);
			if($up){
				echo "true|".$sql;
				exit;
			}
		}
	}
	exit;
}


//업무종료
if($mode == "live_4_change"){

	$status = $_POST['status'];

	//업무종료 OFF
	if($status == "true"){
		$sql = "select idx, live_1 from work_member where state='0' and companyno='".$companyno."' and email='".$user_id."'";
		$mem_info = selectQuery($sql);
		if($mem_info['idx']){
			$sql = "update work_member set live_1='0',live_2='0', live_3='0', live_4='0', live_1_regdate=null, live_2_regdate=null, live_3_regdate=null, live_4_regdate=".DBDATE." where idx='".$mem_info['idx']."'";
			$up = updateQuery($sql);
			if($up){
				echo "true|";
				exit;
			}
		}

	//업무종료 ON
	}else if($status == "false"){
		$sql = "select idx, live_1 from work_member where state='0' and companyno='".$companyno."' and email='".$user_id."' and live_4_regdate is null";
		$mem_info = selectQuery($sql);
		if($mem_info['idx']){
			$sql = "update work_member set live_1='0',live_2='0', live_3='0', live_4='1', live_1_regdate=null, live_2_regdate=null, live_3_regdate=null, live_4_regdate=".DBDATE." where idx='".$mem_info['idx']."'";
			$up = updateQuery($sql);
			if($up){

				//퇴근 로그 저장
				//리턴값없이 저장함
				//inc_lude/func.php 설정됨 
				member_logoff_log();

				$sql = "select idx, DATE_FORMAT(live_4_regdate, '%H:%i') as live_4_time from work_member where state='0' and companyno='".$companyno."' and email='".$user_id."'";
				$mem_upinfo = selectQuery($sql);

				echo "true|".$mem_upinfo['live_4_time'];
				exit;
			}
		}
	}
	exit;
}



//최종업데이트
if($mode == "reload_index"){

	$updatetime = date("m/d H:i" , TODAYTIME);

	echo "complete|".$updatetime;
	exit;
}



//케릭터 선택
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

				//테이블 work_member_profile_img idx값 5번이 디폴트 이미지로 저장되어 -1을 하여 profile_no 을 변경함
				// if($profile_no > 5){
				// 	$profile_no = $profile_no - 1;
				// }

				// $file_path = "/html/images/pre/";
				// $file_name = "img_prof_0".$profile_no.".png";

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
				//$img = "/html/images/pre/img_prof_0".$profile_no.".png";
				//echo "complete|".$img;
				
			}
		}
	}
	exit;
}


//프로필 이미지변경
if($mode == "main_profile_change"){


	//파일체크
	$file_upload_check = false;
				
	//파일갯수
	$fileimg_upload_cnt = count($_FILES['files']['name']);

	//첨부파일 이미지
	if ($fileimg_upload_cnt > 0){

		//파일첨부 여부
		$file_upload_check = true;

		//파일순번
		$file_img_num = 1;
		
		//파일확장자 추출
		$filename = $_FILES['files']['name'][0];
		$ext = array_pop(explode(".", strtolower($filename)));

		//허용확장자체크
		if( !in_array($ext, $img_file_allowed_ext) ) {
			echo "ext_file2";
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


		$sql = "select idx from work_member where state='0' and email='".$user_id."'";
		$mem_info = selectQuery($sql);
		if($mem_info['idx']){
			$mem_info_idx = $mem_info['idx'];
		}

		//랜덤번호
		$rand_id = name_random();

		//변경되는 파일명
		list($microtime,$timestamp) = explode(' ',microtime());
		$time = $timestamp.substr($microtime, 2, 3);
		$datetime = date("YmdHis", $timestamp).substr($microtime, 2, 3);

		//$renamefile = date("YmdHis")."_{$rand_id}_challenges_{$res_idx}.{$ext}";
		$renamefile = "{$datetime}_{$rand_id}_profile_{$mem_info_idx}.{$ext}";

		//년도
		$dir_year = date("Y", TODAYTIME);

		//월
		$dir_month = date("m", TODAYTIME);

		//업로드 디렉토리 -/data/회사고유번호/(회사폴더명)/profile/img/년/월/
		$upload_path = $dir_file_profile_path."/".$profile_save_dir_img."/";
		$upload_path = str_replace($profile_save_dir_img , "data/".$companyno."/".$comfolder."/"."profile/img" , $upload_path);

		//업로드 디렉토리 - /data/회사고유번호/(회사폴더명)/profile/img_ori/년/월/
		$upload_path_ori = $dir_file_profile_path."/".$profile_save_dir_img_ori."/";
		$upload_path_ori = str_replace($profile_save_dir_img_ori , "data/".$companyno."/".$comfolder."/"."profile/img_ori" , $upload_path_ori);




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

		if($user_id=='sadary0@nate.com'){
			echo $upload_files_ori;
			echo "\n\n";
		}

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
			$file_path = str_replace($dir_file_profile_path , "" , $upload_path);

			//원본이미지 경로
			$file_path_ori = str_replace($dir_file_profile_path , "" , $upload_path_ori);

			//리사이즈 경우
			if($rezie_img == true){
				$rezie_file_path = $file_path;
				$rezie_renamefile = $renamefile;
				$resize_file = $file_resize;
				$resize_val = "1";
			}else{
				$rezie_file_path = $file_path_ori;
				$rezie_renamefile = $renamefile;
				$resize_file = $file_size;
				$resize_val = "0";
			}

			$sql = "select idx, file_path, file_name, file_ori_path, file_ori_name from work_member_profile_img where state='0' and email='".$user_id."'";
			$profile_info = selectQuery($sql);
			if($profile_info['idx']){

				if($profile_info['file_path'] && $profile_info['file_name']){
					@unlink($dir_file_path.$profile_info['file_path'].$profile_info['file_name']);
				}

				if($profile_info['file_ori_path'] && $profile_info['file_ori_name']){
					@unlink($dir_file_path.$profile_info['file_ori_path'].$profile_info['file_ori_name']);
				}

				$sql = "update work_member_profile_img set companyno='".$companyno."', resize='".$resize_val."', file_path='".$rezie_file_path."', file_name='".$rezie_renamefile."', file_size='".$resize_file."', file_ori_path='".$file_path_ori."', file_ori_name='".$renamefile."', file_ori_size='".$file_size."', file_real_name='".$file_real_name."',file_type='".$file_type."', editdate=".DBDATE." where idx='".$profile_info['idx']."'";
				$up = updateQuery($sql);
				if($up){
					$sql = "update work_member set profile_type='1', profile_img_idx='".$profile_info['idx']."' where state='0' and email='".$user_id."'";
					$mem_up = updateQuery($sql);
					if($mem_up){
						echo "complete";
						exit;
					}
				}

			}else{

				$sql = "insert into work_member_profile_img(companyno, email, resize, file_path, file_name, file_size, file_ori_path, file_ori_name, file_ori_size, file_real_name, file_type, ip) values(";
				$sql = $sql .="'".$companyno."', '".$user_id."','".$resize_val."','".$rezie_file_path."','".$rezie_renamefile."','".$resize_file."','".$file_path_ori."','".$renamefile."','".$file_size."','".$file_real_name."','".$file_type."','".LIP."')";
				$files_idx = insertIdxQuery($sql);
				if($files_idx){
					$sql = "update work_member set profile_type='1', profile_img_idx='".$files_idx."' where state='0' and email='".$user_id."'";
					$mem_up = updateQuery($sql);
					if($mem_up){
						echo "complete";
						exit;
					}
				}
			}

		}else{
			echo "file_not";
			exit;
		}
	}
	exit;
}



//프로필 이미지변경
if($mode == "main_profile_change_default"){

	$sql = "select idx, file_path, file_name from work_member_character_img where idx='5'";
	$character_img_info = selectQuery($sql);
	
	$sql = "select idx, file_path, file_name, file_ori_path, file_ori_name from work_member_profile_img where state='0' and email='".$user_id."'";
	$profile_info = selectQuery($sql);
	if($profile_info['idx']){

		if($profile_info['file_path'] && $profile_info['file_name']){
			@unlink($dir_file_path.$profile_info['file_path'].$profile_info['file_name']);
		}

		if($profile_info['file_ori_path'] && $profile_info['file_ori_name']){
			@unlink($dir_file_path.$profile_info['file_ori_path'].$profile_info['file_ori_name']);
		}

		if($character_img_info['idx']){

			$sql = "update work_member_profile_img set resize='0', file_path='".$character_img_info['file_path']."', file_name='".$character_img_info['file_name']."', file_size='0', file_ori_path=NULL, file_ori_name=NULL, file_ori_size='0', file_real_name=NULL, file_type=NULL, editdate=".DBDATE." where email='".$user_id."'";
			$up = updateQuery($sql);

			if($up){
				$sql = "update work_member set profile_type='0', profile_img_idx='".$character_img_info['idx']."' where state='0' and email='".$user_id."'";
				$mem_up = updateQuery($sql);
				if($mem_up){
					echo "complete";
					exit;
				}
			}
		}

	}else{

		$sql = "insert into work_member_profile_img(email,  resize, file_path, file_name, file_size, file_ori_path, file_ori_name, file_ori_size, file_real_name, file_type, ip) values(";
		$sql = $sql .="'".$user_id."','0','".$character_img_info['file_path']."','".$character_img_info['file_name']."','0',NULL,NULL,'0',NULL,NULL,'".LIP."')";
		$files_idx = insertIdxQuery($sql);
		if($files_idx){
			$sql = "update work_member set profile_type='0', profile_img_idx='".$character_img_info['idx']."' where state='0' and email='".$user_id."'";
			$mem_up = updateQuery($sql);
			if($mem_up){
				echo "complete";
				exit;
			}
		}
	}


	exit;
}

if($mode == "cp_new"){

	$cp1 = $_POST['cp1'];
	$cp2 = $_POST['cp2'];
	$cp3 = $_POST['cp3'];
	$cp4 = $_POST['cp4'];
	$cp5 = $_POST['cp5'];
	$cp6 = $_POST['cp6'];



	$sql = "select idx from work_cp_reward_list_new where state='0' and email='".$user_id."' and DATE_FORMAT(regdate, '%Y-%m-%d')='".TODATE."'";
	$cp_info = selectQuery($sql);

	if($cp_info['idx']){
		$sql = "update work_cp_reward_list_new set type1='".$cp1."', type2='".$cp2."', type3='".$cp3."', type4='".$cp4."', type5='".$cp5."', type6='".$cp6."' where email='".$user_id."' and idx='".$cp_info['idx']."'";
		$up = updateQuery($sql);
		if($up){
			$insert_idx = $cp_info['idx'];
		}
	}else{
		$qstring = ",type1,type2,type3,type4,type5,type6";
		$qstring_val = ", '".$cp1."', '".$cp2."', '".$cp3."', '".$cp4."', '".$cp5."', '".$cp6."'";
		$sql = "insert into work_cp_reward_list_new(email,name,work_idx,service,act,ip".$qstring.") values";
		$sql = $sql .= " ('".$user_id."','".$user_name."','".$work_info['idx']."','".$work."','','".LIP."'".$qstring_val.")";
		$insert_idx = insertQuery($sql);
	}

	if($insert_idx){
		echo "complete";
	}
	exit;
}


//페널티 닫기
if($mode == "penalty_close"){

	$kind_flag = $_POST['kind_flag'];
	$kind_flag = preg_replace("/[^0-9]/", "", $kind_flag);
	$week_day = week_day(TODATE);

	if($user_id=='sadary0@nate.com'){
		//$user_id='sun@bizforms.co.kr';
		//echo get_filename(); //main_process
	}

	//페널티 로그 조회
	$sql = "select count(1) as cnt from work_penalty_list_log where state='0' and kind_flag='".$kind_flag."' and email='".$user_id."' and workdate between '".$week_day['month']."' and '".$week_day['friday']."'";
	$penalty_list_info = selectQuery($sql);
	if ($penalty_list_info['cnt'] > 0){
		
		//페널티 횟수
		$penalty_cnt = $penalty_list_info['cnt'];
		$sql = "select idx from work_penalty_list where state='0' and kind_flag='".$kind_flag."' and email='".$user_id."' and workdate='".TODATE."'";
		$pl_info = selectQuery($sql);

		if(!$pl_info['idx']){
			$sql = "insert into work_penalty_list(email, name, companyno, kind_flag, penalty_cnt, workdate, closedate, ip)";
			$sql = $sql .=" values('".$user_id."','".$user_name."','".$companyno."','".$kind_flag."','".$penalty_cnt."','".TODATE."',".DBDATE.",'".LIP."') ";
			$insert_idx = insertIdxQuery($sql);
			if($insert_idx){
				echo "complete";
				exit;
			}
		}else{
			$sql = "update work_penalty_list set closedate=".DBDATE." where idx='".$pl_info['idx']."'";
			$up = updateQuery($sql);
			if($up){
				echo "complete";
				exit;
			}
		}
	}else{
	
	}
	exit;
}


//비밀번호 재설정
if($mode == "tl_repass"){

	//메일발송 라이브러리
	include_once($home_dir . "PHPMailer/libphp-phpmailer/PHPMailerAutoload.php");
	
	$send_email = $_POST['send_email'];
	if($send_email){
		//실서버
		$sql = "select idx, email, name, company, companyno from work_member where state='0' and companyno='".$companyno."' and email='".$send_email."'";
		$mem_info = selectQuery($sql);
		if($mem_info['idx']){

			$secret = "send_email=".$send_email."&companyno=".$companyno."&send=passwdreset";
			$encrypted = Encrypt($secret);

			//회사명
			$to_company_name = $mem_info['company'];

			//받는사람 이메일주소
			$to_email = $mem_info['email'];

			//발신자이메일주소
			$send_email = "manager@rewardy.co.kr";
					
			//smtp 메일계정
			$smtp_email = "devmaster@bizforms.co.kr";

			//발신자명
			$send_name = "리워디";

			//메일 제목
			$title = "리워디 비밀번호 초기화 이메일입니다.";
			//$contents = "안녕하세요.\n\n".$send_name."에서 발송되었습니다.\n\n아래 링크를 클릭하면 비밀번호가 초기화 처리 됩니다.\n\n<span style=\"color: red\"><a href='http://rewardy.co.kr/team/?".$encrypted."' target=\"_blank\">비밀번호 초기화</a></span>";

			//비밀번호 재설정 URL
			$reset_url = "http://rewardy.co.kr/team/?".$encrypted."";
			include str_replace( basename(__DIR__) , "", __DIR__ ) ."layer/mail_send_pa_reset.php";
			$contents = $mail_html;

			//발신자이름, 발신자이메일, 수신자이메일, 메일제목, 메일내용
			$result = mailer($send_name, $smtp_email, $to_email, $title, $contents);
			//$result = sendMail($to_email, $smtp_email, $send_name, $title, $contents);
			if($result == '1'){
				echo "complete";
				exit;
			}else{
				echo "fail";
				exit;
			}
		}else{
			echo "not";
			exit;
		}
	}
	exit;
}


//비밀번호 재설정
if($mode == "member_pass_edit"){

	$passwd1 = $_POST['passwd1'];
	$passwd2 = $_POST['passwd2'];

	//비밀번호 불일치
	if($passwd1 != $passwd2){
		echo "not";
		exit;
	}

	//비밀번호 변경
	if($passwd1 && $passwd2){

		//KISA_SHA256암호화
		$kisa_user_pw = kisa_encrypt($passwd1);

		$sql = "select idx, password from work_member where state='0' and companyno='".$companyno."' and email='".$user_id."'";
		$mem_info = selectQuery($sql);
		if($mem_info['idx']){
			//$sql = "update work_member set password=pwdencrypt('".$passwd1."') where state='0' and idx='".$mem_info['idx']."'";
			$sql = "update work_member set password='".$kisa_user_pw."' where idx='".$mem_info['idx']."'";
			$up = updateQuery($sql);
			if($up){
				echo "ok";
				exit;
			}else{
				if($mem_info['password'] == $kisa_user_pw){
					echo "same";
					exit;
				}else{
					echo "faie";
					exit;
				}
			}
		}
	}
	exit;
}


//읽지 않은 게시물
if($mode == "read_date"){


	$read_date = $_POST['read_date'];
	if($read_date){
		setcookie('read_date', $read_date, COOKIE_TIME , '/', C_DOMAIN);
	}
	exit;

}


if($mode == "main_like_list"){

	$idx = $_POST['val'];
	$idx = preg_replace("/[^0-9]/", "", $idx);

	if($idx){
		$sql = "select idx from work_main_like where state='0' and companyno='".$companyno."' and idx='".$idx."'";
		$like_info = selectQuery($sql);
		if($like_info['idx']){

			$sql = "select idx from work_todaywork_main_like where state='0' and companyno='".$companyno."' and work_idx='".$like_info['idx']."' and email='".$user_id."'";
			$main_like_info = selectQuery($sql);
			if($main_like_info['idx']){
				$sql = "update work_todaywork_main_like set state='9', editdate=".DBDATE." where idx='".$main_like_info['idx']."'";
				$up = updateQuery($sql);
				if($up){

					echo "complete";
					exit;
				}
			}
		}
	}
	exit;
}



//메인 좋아요 보내기
if($mode == "main_like_send"){
	$idx = $_POST['val'];
	$idx = preg_replace("/[^0-9]/", "", $idx);

	if($idx){
		$sql = "select idx, type, email, name, memo, kind from work_main_like where state='0' and companyno='".$companyno."' and idx='".$idx."'";
		$main_info = selectQuery($sql);
		if($main_info['idx']){
			$main_info_idx = $main_info['idx'];
			$main_info_type = $main_info['type'];
			$main_info_email = $main_info['email'];
			$main_info_name = $main_info['name'];
			$main_info_memo = $main_info['memo'];
			$main_info_kind = $main_info['kind'];

			$penalty = member_penalty($main_info_email);

			if($penalty['penalty_state']>0){
				echo "penalty";
				exit;
			}

			$sql = "select idx from work_todaywork_main_like where state='1' and companyno='".$companyno."' and like_kind = '".$main_info_kind."' and email='".$user_id."' and workdate = '".TODATE."' and work_idx = '".$main_info_idx."'";
			$main_like_today = selectQuery($sql);
				
			if(!$main_like_today['idx']){
				
				$sql = "select idx from work_todaywork_main_like where state='0' and companyno='".$companyno."' and work_idx='".$main_info['idx']."' and email='".$user_id."'";
				$main_like_info = selectQuery($sql);
				if($main_like_info['idx']){
					$sql = "update work_todaywork_main_like set state='1', editdate=".DBDATE." where idx='".$main_like_info['idx']."'";
					$up = updateQuery($sql);
					if($up){

						$cate = challenges_category();
						$jf_idx = $cate['idx'][$main_info_type];
						$service = "main";
						$jl_comment = $main_info_memo;


						//타임라인(좋아요 보냄)
						work_data_log('0','8', $main_info_idx, $user_id, $user_name, $main_info_email, $main_info_name);

						//타임라인(좋아요 받음)
						work_data_log('0','10', $main_info_idx, $main_info_email, $main_info_name, $user_id, $user_name);
						

						//exit;
						//좋아요 내역저장
						$sql = "select idx from work_todaywork_like where state='0' and companyno='".$companyno."' and service='".$service."' and like_flag='0' and email='".$main_info_email."' and send_email='".$user_id."' and workdate='".TODATE."'";
						$like_info = selectQuery($sql);

						if(!$like_info['idx']){
							work_cp_reward("like", $main_info_type, $main_info_email, $insert_idx);
						}

						$sql = "insert into work_todaywork_like(companyno, kind_flag, service, work_idx, like_flag, email, name, send_email, send_name, comment, type_flag, ip, workdate) values(";
						$sql = $sql .= "'".$companyno."','".$jf_idx."', '".$service."', '".$main_like_info['idx']."', '0', '".$main_info_email."', '".$main_info_name."', '".$user_id."', '".$user_name."', '".$jl_comment."', '".$type_flag."', '".LIP."', '".TODATE."')";
						$insert_idx = insertIdxQuery($sql);
						
						$tokenTitle = $user_name."님이 좋아요를 보냈어요";
						pushToken($tokenTitle,$jl_comment,$main_info_email,'live','10',$user_id,$user_name,$jf_idx,null,'live');
						if($insert_idx){
							echo "|complete|";
						}
						

						$sql = "select a.idx, count(1) as cnt
							from work_main_like as a left join work_todaywork_main_like b on(a.idx=b.work_idx)
							where 1=1
							and a.companyno = '".$companyno."'
							and a.workdate = '".TODATE."'
							and b.email = '".$user_id."'
							and b.state = '0'";

							$count_like = selectQuery($sql);

						if($count_like['cnt'] == 0){
							
							echo $count_like['cnt']."|"?>
						
							<div class="list_none">
								<div class="none_q">
									<span>곧 이 자리에 새로운 추천 동료들이 나타날 거예요! <br>
										제가 빠르게 찾아드릴게요!</span>
								</div>
								<img src="/html/images/pre/rew_cha_02.png" alt="알 캐릭터">
							</div>
						<?}

					}
				}
			}
		}
	}
	exit;
}


//첫로그인 레이어 시간표기
if($mode == "first_login"){

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

	echo $goto_year."년 ". $goto_month."월 ".$goto_day."일 ".$get_time_text." ".$goto_gg.":".$goto_ii;
	exit;
}

if($mode == "item_img_buy"){

	date_default_timezone_set('Asia/Seoul');
	$this_time = date("Y-m-d H:i:s", time());
	$img_idx = $_POST['img_idx'];
	$item_code = '1200';
	$item_type = 'item';
	$reward_user = 'rewardy';
	$reward_nm = '리워디';
	$reward_memo = '아이템 구매 차감';

	$sql = "select idx, end_date from work_item_info where member_email = '".$user_id."' and item_idx = '".$img_idx."' and state = 0";
	$img_info = selectQuery($sql);

	if($img_info){
		$item_end_date = $img_info['end_date'];

		$sql = "select item_date from work_member_character_img where idx = '".$img_idx."'";
		$img_date = selectQuery($sql);

		if($img_date['item_date'] == 0){
			echo "exist";
			exit;
		}else{
			if($this_time < $item_end_date){
				echo "date_be";
				exit;
			}else{
				echo "complete";
				exit;
			}
		}
	}

	$sql = "select idx, item_price, item_date, item_effect, kind_flag from work_member_character_img where idx = '".$img_idx."'";
	$img_idx_rs = selectQuery($sql);

	if($img_idx_rs['idx']){
		$img_price = $img_idx_rs['item_price'];
		$item_kind = $img_idx_rs['item_effect'];
		$item_date = $img_idx_rs['item_date'];
		$item_kind_flag = $img_idx_rs['kind_flag'];
		$time = date("Y-m-d H:i:s",time());;

		if($item_date != 0){
			$time = strtotime("+".$item_date." days");
			$time = date("Y-m-d H:i:s",$time);
		}

		$sql = "select idx,coin from work_member where state = '0' and companyno = '".$companyno."' and email = '".$user_id."'";
		$mem_coin = selectQuery($sql);

		if($img_price > $mem_coin['coin']){
			echo "exp";
			exit;
		}else{
			$sql = "update work_member set profile_type = 0, profile_img_idx = '".$img_idx."', coin = coin - '".$img_price."' where idx = '".$mem_coin['idx']."'";
			$buy_item_sql = updateQuery($sql);

			$sql = "insert into work_item_info (member_email, item_idx, item_kind, end_date, item_kind_flag) values ('".$user_id."', '".$img_idx_rs['idx']."', '".$item_kind."','".$time."','".$item_kind_flag."')";
			$buy_item_info = insertQuery($sql);

			$sql = "insert into work_coininfo (code, work_idx, reward_type, companyno, email, name, reward_user, reward_name, coin, memo, ip, workdate) values ";
			$sql = $sql .="('".$item_code."','".$img_idx_rs['item_price']."','".$item_type."','".$companyno."','".$user_id."','".$user_name."','".$reward_user."','".$reward_nm."','".$img_price."','".$reward_memo."','".LIP."','".TODATE."')";
			$buy_coin_info = insertQuery($sql);

			echo "complete";
			exit;
		}
	}

}

if($mode == "item_img_layer"){

	$img_idx = $_POST['img_idx'];

	$sql = "select file_path, file_name, item_price, item_title, item_date from work_member_character_img where idx = '".$img_idx."'";
	$img_info = selectQuery($sql);

	if($img_info){
		$img_file_path = $img_info['file_path'];
		$img_file_name = $img_info['file_name'];
		$img_price = $img_info['item_price'];
		$img_title = $img_info['item_title'];
		$img_date = $img_info['item_date'];

		if($img_date == 0){
			$img_date_text = '영구소장';
		}

		$img_full_path = $img_file_path.$img_file_name;
	}

	?>
	<div class="is_layer_deam"></div>
		<div class="is_layer_in">
			<div class="is_layer_tit">
				<strong>Rewardy 아이템샵</strong>
			</div>
			<div class="is_layer_area">
				<div class="is_layer_list">
					<ul class="is_layer_ul">
						<li class="is_layer_li">
							<div class="is_profile_box">
								<div class="is_profile_img" style="background-image:url(<?=$img_full_path?>);"></div>
							</div>
							<div class="is_coin">
								<strong><span><?=$img_price?></span></strong>
							</div>
							<div class="is_desc">
								<ul>
									<li>아이템 : <?=$img_title?></li>
									<li>가격 : <?=$img_price?> Coin</li>
									<li>효과 : 내 프로필 캐릭터 변경</li>
									<li>사용기간 : <?=$img_date_text?></li>
								</ul>
							</div>
						</li>
					</ul>
				</div>
			</div>
			<div class="is_layer_btns">
				<button class="is_layer_btn_off"><span>취소</span></button>
				<button class="is_layer_btn_on" id="item_img_buy"><span>구매하기</span></button>
			</div>
		</div>
	<?
}
if($mode == "team_memo"){

	$p_memo = $_POST['p_memo'];
	$sql = "select name,email,memo from work_member where email = '".$user_id."' and state = '0' and companyno = '".$companyno."'";

	$user_check = selectQuery($sql);
	if($user_check){
		$sql = "update work_member set memo = '".$p_memo."', memo_editdate = '".DBDATE."' where email = '".$user_check['email']."'and companyno = '".$companyno."'";
		$memo_update = updateQuery($sql);


		if($memo_update){
			works_memo('add', $user_check['email'], $user_check['name'], $p_memo);

			
			work_cp_reward("mian", "0009", $user_check['email'], $user_check['idx']);

			echo "complete";
		}
	}
}	

if($mode == "main_meeting"){
	$type_flag = ($chkMobile)?1:0;
	$contents = $_POST['contents'];
	$decide_flag = $_POST['decide_flag'];
	$work_flag = $_POST['work_flag'];
	$work_stime = $_POST['work_stime'];
	$work_etime = $_POST['work_etime'];
	//업무등록
	$sql = "insert into work_todaywork(companyno, email, name, highlevel, type_flag, work_flag, part_flag, part, title, decide_flag, work_stime , work_etime , secret_flag, file_flag, contents, workdate, ip)";
	$sql = $sql .=" values('".$companyno."', '".$user_id."','".$user_name."','".$user_level."','".$type_flag."','".$work_flag."','".$user_part."','".$part_name."', '', '".$decide_flag."', '".$work_stime."' , '".$work_etime."' , '0' , '0', '".$contents."','".TODATE."','".LIP."')";
	// $sql .= impode(",", $valueSettings);
	$res_idx = insertIdxQuery($sql);

	if($res_idx){
		echo "complete";
	}
}
?>