<?
	//header페이지
	$home_dir = str_replace( basename(__DIR__) , "" , __DIR__ );
	include $home_dir . "/inc_lude/header_live.php";
	date_default_timezone_set('Asia/Seoul');
	$now_time = date("H:i");
	$now_time_obj = DateTime::createFromFormat('H:i', $now_time);
	
	?>
	<link rel="stylesheet" type="text/css" href="/html/css/penalty_pop.css<?php echo VER;?>" />
	<?//페이지수
	if(!$gp) {
		$gp = 1;
	}

	$pagesize = 35;						//페이지 출력갯수
	$startnum = 0;						//페이지 시작번호
	$endnum = $gp * $pagesize;			//페이지 끝번호

	//시작번호
	if ($gp == 1){
		$startnum = 1;
	}else{
		$startnum = ($gp - 1) * $pagesize + 1;
	}


	//프로필 캐릭터 사진
	$character_img_info = character_img_info();

	//회원전체

	$sql = "select email, name from work_member_zzim_list where email = '".$user_id."'";
	$check_zzim = selectQuery($sql);

	if($check_zzim){
		$where_zzim = "and c.email = '".$user_id."'";
	}else{
		$where_zzim = "and (c.email = '".$user_id."' or c.email != '' or c.email is null)";
	}
	//메인 전체 회원 리스트
	$sql = "select a.idx, a.email, a.name, a.part, a.partno, a.gender, a.profile_type, a.profile_img_idx, b.file_path, b.file_name,  a.memo, a.memo_editdate, a.live_1, a.live_2, a.live_3, a.live_4, a.penalty_state,";
		$sql .= " DATE_FORMAT(a.live_1_regdate, '%H:%i') as live_1_time,";
		$sql .= " a.live_1_regdate";
		$sql .= " from work_member a";
		$sql .= " left join work_member_profile_img b on a.email = b.email";
		$sql .= " left join work_member_zzim_list c on a.idx = c.mem_idx";
		$sql .= " where 1=1 and a.state='0' $where_zzim";

		//관리권한은 제외처리
		if($user_level == 1){
			$sql .= " and a.email!='".$user_id."'";
		}else{
			$sql .= " and a.companyno='".$companyno."'";
			$sql .= " and a.highlevel!='1'";
		}

		$sql .= " group by a.name";
		$sql .= " order by";
		$sql .= " CASE WHEN a.email='".$user_id."' THEN a.email END DESC,";
		$sql .= " CASE WHEN c.state = '1' and c.email = '".$user_id."' THEN c.idx END DESC,";
		// $sql .= " CASE WHEN a.partno='".$user_part."' then  a.partno END DESC,";
		$sql .= " CASE WHEN a.memo is not null and a.memo != '' then  a.email END DESC,";
		$sql .= " CASE WHEN a.live_1_regdate is not null THEN a.live_1_regdate END ASC";
		$sql .= " limit ". ($gp-1)*$startnum.", ".$endnum;
		$member_list_info_live = selectAllQuery($sql);

	$curYear = (int)date('Y');
	$curMonth = (int)date('m');
	$month_first_day = date("Y-m-d", mktime(0, 0, 0, $curMonth , 1, $curYear));
	$month_last_day = date("Y-m-d", mktime(0, 0, 0, $curMonth+1 , 0, $curYear));
	
	//정렬기준
	//1. 자기 자신은 제일 앞으로 정렬함
	//2. 퇴근한 회원
	//3. 집중모드 ON
	//4. 자리비움 ON
	//5. 집중모드 OFF
	//6. 자리비움 OFF
	//7. 출근을 하지 않은경우 이름순으로 정렬, 

	// if($member_list_info['idx']){
	// 	$member_all_cnt = number_format(count($member_list_info['idx']));
	// }else{
	// 	$member_all_cnt = 0;
	// }
	$sql = "select count(1) as cnt from work_member where state='0' and companyno = '".$companyno."' and highlevel!='1'";
	$total_count = selectQuery($sql);

	if($total_count){
		$member_all_cnt = number_format($total_count['cnt']);
	}else{
		$member_all_cnt = 0;
	}
	
	$sql = "select count(1) as cnt from work_member where state='0' and companyno = '".$companyno."' and highlevel!='1' and live_1 = '1'";
	$live_count = selectQuery($sql);

	if($live_count){
		$member_live_cnt = number_format($live_count['cnt']);
	}else{
		$member_live_cnt = 0;
	}
	
	//페이징 갯수
	if ( ($member_all_cnt % $pagesize) > 0 ){
		$page_count = floor($member_all_cnt/$pagesize)+1;
	}else{
		$page_count = floor($member_all_cnt/$pagesize);
	}
	//회원정보
	$member_one_info =  member_row_char_info($user_id);

	if($member_one_info['idx']){
		$part_name = $member_list_all['partname'][$member_one_info['email']];
		$member_info_email = $member_one_info['email'];
		$member_info_name = $member_one_info['name'];
		$live_1 = $member_one_info['live_1'];
		$live_2 = $member_one_info['live_2'];
		$live_3 = $member_one_info['live_3'];
		$live_4 = $member_one_info['live_4'];
		$live_1_regdate = $member_one_info['live_1_regdate'];
		
		$live_1_time = $member_one_info['live_1_time'];
		if($live_1_time){
			$tmp_live_1_time = explode(":", $live_1_time);
			if($tmp_live_1_time){
				$ex_live_1_time = (int)$tmp_live_1_time[0].":" .$tmp_live_1_time[1] ."";
			}
		}
		
		$profile_type = $member_one_info['profile_type'];
		$profile_img_idx = $member_one_info['profile_img_idx'];
		$profile_img =  'https://rewardy.co.kr'.$member_one_info['file_path'].$member_one_info['file_name'];
		$member_info_coin = number_format($member_info_coin);


		if($live_1!='1' && $live_1_regdate == NULL){
			$switch_ready = " switch_ready";
		}else{
			$switch_ready = "";
		}

	}


	// 지나간 업무 update

	$sql = "select idx , email , state, work_flag, decide_flag, work_stime, work_etime from work_todaywork use index(state) where state='0' and companyno='".$companyno."' and email = '".$user_id."' and decide_flag > 1 and share_flag!='2' and workdate='".TODATE."'";
	$works_up = selectAllQuery($sql);
	if($works_up['email']){
		for($i=0; $i<count($works_up['email']); $i++){
			$works_up_email = $works_up['email'][$i];
			$works_up_decide = $works_up['decide_flag'][$i];
			$works_up_idx = $works_up['idx'][$i];
			$member_decide_stime_obj = DateTime::createFromFormat('H:i', $works_up['work_stime'][$i]);
			$member_decide_etime_obj = DateTime::createFromFormat('H:i', $works_up['work_etime'][$i]);
			if($works_up_decide > 0 && $member_decide_stime_obj != null && $member_decide_etime_obj != null){
				if($now_time_obj  > $member_decide_etime_obj){
					$sql = "update work_todaywork set state = '1' where email = '".$works_up_email."' and decide_flag = '".$works_up_decide."' and idx = '".$works_up_idx."'";	
					$up_decide = updateQuery($sql);
				}
			}
		}
	}

	// 업무일정
	$sql = "select idx, state, email, work_flag, decide_flag, work_stime, work_etime from work_todaywork use index(state) where state='0' and companyno='".$companyno."' and share_flag!='2' and decide_flag != '0' and workdate='".TODATE."' group by email, state, work_flag, decide_flag";
	$works_myinfo = selectAllQuery($sql);
	
	if($works_myinfo['email']){
		for($i=0; $i<count($works_myinfo['email']); $i++){

			$works_myinfo_email = $works_myinfo['email'][$i];
			if($works_myinfo['work_flag'][$i] =='2'&& $works_myinfo['decide_flag'][$i] != '0' && $works_myinfo['state'][$i]=='0'){
				$work_flag_list[TODATE][$works_myinfo_email][] = $works_myinfo['decide_flag'][$i];
				$work_stime_list[TODATE][$works_myinfo_email][] = $works_myinfo['work_stime'][$i];
				$work_etime_list[TODATE][$works_myinfo_email][] = $works_myinfo['work_etime'][$i];
			}
		}
	}
	//업무예약
	$sql = "select idx, title from work_decide where state='0' and type_flag='0'";
	$work_decide_info = selectAllQuery($sql);
	if($work_decide_info['idx']){
		$work_decide_list = @array_combine($work_decide_info['idx'], $work_decide_info['title']);
	}

	//코인보상
	$sql = "select idx,code,coin,icon,memo from work_coin_reward_info where state='0' and kind='live' order by idx asc";
	$coin_reward_info = selectAllQuery($sql);
	

	$main_work_count = main_work_count();
	$meet_count = 0;
	$early_count = 0;
	$rest_count = 0;
	$business_count = 0;

for ($i = 0; $i < count($main_work_count['idx']); $i++) {
    if ($main_work_count['decide_flag'][$i] == '7' || $main_work_count['decide_flag'][$i] == '8') {
        $meet_count += count($main_work_count['idx'][$i]);
    } 
    if ($main_work_count['decide_flag'][$i] == '3' || $main_work_count['decide_flag'][$i] == '4') {
        $early_count += count($main_work_count['idx'][$i]);
    } 
    if ($main_work_count['decide_flag'][$i] == '1' || $main_work_count['decide_flag'][$i] == '2') {
        $rest_count += count($main_work_count['idx'][$i]);
    }
    if ($main_work_count['decide_flag'][$i] == '5') {
        $business_count += count($main_work_count['idx'][$i]);
    }
}

	//업무갯수 조회
	$works_realtime_cnt = works_realtime_cnt();

	//오늘업무
	$works_realtime_all = $works_realtime_cnt['work'] + $works_realtime_cnt['work_complete'];
	
	//보고업무
	$works_realtime_report = $works_realtime_cnt['work_report'][0] + $works_realtime_cnt['work_report'][1];
	
	//요청업무
	$works_realtime_req = $works_realtime_cnt['work_req'][0] + $works_realtime_cnt['work_req'][1];

	//공유업무
	$works_realtime_share = $works_realtime_cnt['work_share'][0] + $works_realtime_cnt['work_share'][1];

	//완료업무갯수
	$works_realtime_complete = ($works_realtime_cnt['work_complete']>0)?$works_realtime_cnt['work_complete']:0;


	//완료한 업무 조회
	$sql = "select a.idx, a.email, a.name, a.work_flag, a.decide_flag, a.work_idx, a.share_flag, a.secret_flag, a.title, a.work_stime, a.work_etime, a.contents, a.editdate, a.regdate from work_todaywork as a use index(state)";
	$sql = $sql .= " where a.state not in('9','99','1') and a.share_flag != '2' and repeat_flag = 0  and a.companyno='".$companyno."' and a.notice_flag='0' and a.workdate='".TODATE."' and (a.work_idx is null or a.share_flag = 1) order by a.regdate desc";
	$works_realtime_info = selectAllQuery($sql);
	//관리권한은 제외처리
	if($user_level == 1){
		$where_sql = $where_sql .= " and email !='".$user_id."'";
	}else{
		$where_sql = $where_sql .= " and companyno='".$companyno."'";
	}

	// 개인 업무 총 갯수
	$sql = "select email, state, count(1) as cnt from work_todaywork use index(state) where workdate = '".TODATE."' and state != 9 and (share_flag != '2' OR (share_flag = '1' AND work_idx IS NOT NULL)) and (work_idx IS NULL OR share_flag = '1') and companyno = '".$companyno."' and notice_flag = '0' group by state, email;";
	$works_all_info = selectAllQuery($sql);
	for($i=0; $i<count($works_all_info['email']); $i++){
		$works_email = $works_all_info['email'][$i];
		$works_state = $works_all_info['state'][$i];
		$works_cnt = $works_all_info['cnt'][$i];
		$work_live[$works_email][$works_state] += $works_cnt;
	}


	//오늘날짜
	$wdate = str_replace("-",".",$today);

	//오늘업무 제일 많이 씀
	$sql = "select email, name, count(1) as cnt from work_todaywork where state!='9' and share_flag!='2' and notice_flag='0' and workdate='".TODATE."'";
	$sql = $sql .= " group by email, name order by count(1) desc limit 1";
	$works_top_info = selectQuery($sql);
	if($works_top_info['email']){
		$works_top_arr[TODATE] = $works_top_info['email'];
	}

	//오늘 출근 제일 빠르게한 회원 조회
	$sql = "select email, name, count(1) as cnt, live_1_regdate from work_member where state='0' and companyno='".$companyno."' and live_1='1' and DATE_FORMAT(live_1_regdate, '%Y-%m-%d')='".TODATE."' group by email, name, live_1_regdate";
	$sql = $sql .= " order by live_1_regdate asc limit 1";

	$mem_top_info = selectQuery($sql);
	if($mem_top_info['email']){
		$mem_top_arr[TODATE] = $mem_top_info['email'];
	}

	//오늘 마음전하기
	$sql = "select email, name, count(1) as cnt from work_todaywork_like where state='0' and service='live' and like_flag='0' and workdate='".TODATE."' group by email, name limit 1";
	$like_top_info = selectQuery($sql);

	if($like_top_info['email']){
		$like_top_arr[TODATE] = $like_top_info['email'];
	}

	//역량지표 좋아요 타이틀
	$work_reward_title = work_reward_like();

	//역량지표
	$avg_point = 1;

	//and idx>='6529'
		$sql = "SELECT
				email,
				SUM(type1) AS type1,
				SUM(type2) AS type2,
				SUM(type3) AS type3,
				SUM(type4) AS type4,
				SUM(type5) AS type5,
				SUM(type6) AS type6
			FROM work_cp_reward_list
			WHERE
				state = '0'
				AND companyno = ?
				AND workdate BETWEEN ? AND ?
			GROUP BY email";

			// Prepare the statement
			$stmt = $conn->prepare($sql);

			// Bind parameters
			$stmt->bind_param("sss", $companyno, $month_first_day, $month_last_day);

			// Execute the query
			$stmt->execute();

			// Get the result set
			$result = $stmt->get_result();

			// Process the results and calculate the reward scores
			$reward_cp_sum = [];
			while ($row = $result->fetch_assoc()) {
				$reward_email = $row['email'];
				$reward_type1 = $row['type1'];
				$reward_type2 = $row['type2'];
				$reward_type3 = $row['type3'];
				$reward_type4 = $row['type4'];
				$reward_type5 = $row['type5'];
				$reward_type6 = $row['type6'];

				// Calculate the reward score for each email
				$reward_type_sum = $reward_type1 + $reward_type2 + $reward_type3 + $reward_type4 + $reward_type5 + $reward_type6;
				$reward_cp_sum[$reward_email] = $reward_type_sum;
			}

			// Close the statement and free up resources
			$stmt->close();


	//좋아요 갯수 조회
	$sql = "select email, count(1) as cnt from work_todaywork_like where state='0' and companyno='".$companyno."' and workdate between '".$month_first_day."' and '".$month_last_day."' group by email";
	$like_info = selectAllQuery($sql);
	for($i=0; $i<count($like_info['email']); $i++){
		$link_info_email = $like_info['email'][$i];
		$link_info_cnt = $like_info['cnt'][$i];
		$like_list[$link_info_email] = $link_info_cnt;
	}

	//좋아요 리스트
	$like_flag_list = array();
	$sql = "select idx, email, send_email, like_flag from work_todaywork_like where state='0' and companyno='".$companyno."' and send_email='".$user_id."' and workdate='".TODATE."'";
	$like_flag_info = selectAllQuery($sql);
	for($i=0; $i<count($like_flag_info['idx']); $i++){
		$like_flage_info_idx = $like_flag_info['idx'][$i];
		$like_flage_info_email = $like_flag_info['email'][$i];
		$like_flage_info_like_flag = $like_flag_info['like_flag'][$i];
		$like_flage_info_send_email = $like_flag_info['send_email'][$i];
		$like_flag_list[$like_flage_info_email][$like_flage_info_like_flag][] = $like_flage_info_idx;
	}
	//멤버 찜 리스트
	$mem_like_flag_list = array();
	$sql = "select idx, mem_idx, email, zzim_email from work_member_zzim_list where state='1' and companyno='".$companyno."' and email='".$user_id."'";
	$mem_like_flag_info = selectAllQuery($sql);
	for($i=0; $i<count($mem_like_flag_info['idx']); $i++){
		$mem_like_flage_info_idx = $mem_like_flag_info['idx'][$i];
		$mem_like_flage_info_email = $mem_like_flag_info['email'][$i];
		$mem_like_flage_info_mem_idx = $mem_like_flag_info['mem_idx'][$i];
		$mem_like_flage_info_zzim_email = $mem_like_flag_info['zzim_email'][$i];
		$mem_like_flag_list[$mem_like_flage_info_zzim_email][$mem_like_flage_info_mem_idx] = $mem_like_flage_info_idx;
	}
	//코인 보상 대상 추출
	$sql = "select idx, kind, sort, cnt, day_flag, day, memo from work_cp_reward_info where state='0' order by sort asc";
	$work_cp_reward_info = selectAllQuery($sql);
	for($i=0; $i<count($work_cp_reward_info['idx']); $i++){
		$work_cp_reward_info_kind = $work_cp_reward_info['kind'][$i];
		$work_cp_reward_info_sort = $work_cp_reward_info['sort'][$i];
		$work_cp_reward_info_cnt = $work_cp_reward_info['cnt'][$i];
		$work_cp_reward_info_day = $work_cp_reward_info['day'][$i];
		$work_cp_reward_info_day_flag = $work_cp_reward_info['day_flag'][$i];
		$work_cp_reward_info_memo = $work_cp_reward_info['memo'][$i];
		
		//if($work_cp_reward_info_day != 0){
			$reward_cp_info[$work_cp_reward_info_sort][$work_cp_reward_info_kind][$work_cp_reward_info_day_flag]['memo'] = $work_cp_reward_info_memo;
			$reward_cp_info[$work_cp_reward_info_sort][$work_cp_reward_info_kind][$work_cp_reward_info_day_flag]['cnt'] = $work_cp_reward_info_cnt;

			$reward_cp_info2[$work_cp_reward_info_sort]['memo'] = $work_cp_reward_info_memo;
			$reward_cp_info2[$work_cp_reward_info_sort]['cnt'] = $work_cp_reward_info_cnt;

	}

	$yesterday = date("Y-m-d", strtotime(TODATE." -1 day"));
	
	//한줄소감
	$review_info = review_info();
?>

<script type="text/javascript">
	var work_reward_title_arr = new Array();
	<?
	//역량지표 타이틀
	foreach($work_reward_title as $key => $val){?>
		work_reward_title_arr["<?=$key?>"] = "<?=$val?>";
	<?}?>
	
	<?if($member_total_cnt > 0){?>
		var member_total_cnt = '<?=number_format($member_total_cnt)?>';
	<?}?>
</script>
<html>
<body>
	<head>
	<link rel="stylesheet" href="/html/css/billboard.css">
	</head>
<div class="rew_warp live_warp">
	<div class="rew_warp_in">
		<div class="rew_box">
			<div class="rew_box_in">
				<? include $home_dir . "/inc_lude/header_new.php";?>
				<!-- menu -->
				<? include $home_dir . "/inc_lude/menu_live.php";?>
				<!-- //menu -->

				<!-- 콘텐츠 -->
				<div class="rew_conts">
					<div class="rew_conts_in">
						<div class="rew_conts_scroll_07">
							<div class="rew_live_my">
								<div class="rew_live_my_in">
									<div class="rew_live_my_left">
										<div class="rew_live_my_list_today" style="display:none;">
											<input type="hidden" id="user_email" value="all">
										</div>
										<div class="live_tab">
											<div class="live_tab_in">
												<input type="hidden" id="pageno" value="<?=$gp?>">
												<input type="hidden" id="page_count" value="<?=$page_count?>">
												<input type="hidden" id="all" value="<?=$member_live_cnt?>">
												<input type="hidden" id="meet" value="<?=$meet_count?>">
												<input type="hidden" id="outing" value="<?=$early_count?>">
												<input type="hidden" id="business" value="<?=$business_count?>">
												<input type="hidden" id="rest" value="<?=$rest_count?>">
												<input type="hidden" id="user_email" value="all">
												<ul>
													<li class="on option_count" id="lives_list_cnt" value = "all"><span><?=$member_live_cnt?> / <?=$member_all_cnt?><em>명</em></span><em>정상근무</em></li>
													<li id="lives_list_cnt" class = "option_count" value = "rest" <?if ($rest_count == 0) echo 'style="pointer-events: none;"'; ?>><span><?=$rest_count?><em>명</em></span><em>연차/반차</em></li>
													<li id="lives_list_cnt" class = "option_count" value = "early" <?if ($early_count == 0) echo 'style="pointer-events: none;"'; ?>><span><?=$early_count?><em>명</em></span><em>조퇴/외출</em></li>
													<li id="lives_list_cnt" class = "option_count" value = "meet" <?if ($meet_count == 0) echo 'style="pointer-events: none;"'; ?>><span><?=$meet_count?><em>명</em></span><em>미팅/회의</em></li>
													<li id="lives_list_cnt" class = "option_count" value = "business" <?if ($business_count == 0) echo 'style="pointer-events: none;"'; ?>><span><?=$business_count?><em>명</em></span><em>출장</em></li>
												</ul>
											</div>
										</div>
									</div>

									<div class="rew_live_my_right">
										<div class="rew_live_my_tit">
											<span>나의 상태</span>
										</div>
										<div class="rew_grid_onoff">
											<div class="rew_grid_onoff_in">
												<ul>
													<li class="onoff_01">
														<?if($live_1=='1'){?>
															<em <?=($live_1=='1')?"class='on'":""?>>출근</em>
														<?}else{?>
															<em>출근</em>
														<?}?>
														<div class="btn_switch<?=($live_1=='1')?" on":""?>" id="live_1_bt">
															<strong class="btn_switch_on"></strong>
															<span>버튼</span>
															<strong class="btn_switch_off"></strong>
														</div>
													</li>
													
													<?
														$sql = "select idx, email , state, work_flag, decide_flag, work_stime, work_etime from work_todaywork use index(state) where state='0' and email = '".$user_id."' and companyno='".$companyno."' and decide_flag = '8' and share_flag!='2' and workdate='".TODATE."'";
														$person_work = selectAllQuery($sql);
														for($i =0; $i < count($person_work['idx']); $i++){
															if($now_time >= $person_work['work_stime'][$i] && $now_time <= $person_work['work_etime'][$i]){
																$work_status = "on";
															}else{
																$work_status = "off";
															}
														}
													?>
													
													<li class="onoff_02">
														<em>회의</em>
														<div class="btn_switch <?=$work_status?>" id="live_3_bt">
															<strong class="btn_switch_on"></strong>
															<span>버튼</span>
															<strong class="btn_switch_off"></strong>
														</div>
													</li>
														
													<li class="onoff_04">
														<em <?=($live_4=='1')?"class='on'":""?>>퇴근</em>
														<div class="btn_switch<?=($live_4=='1')?" on":""?>" id="live_4_bt" value="<?=TODATE?>">
															<strong class="btn_switch_on"></strong>
															<span>버튼</span>
															<strong class="btn_switch_off"></strong>
														</div>
													</li>
												</ul>
											</div>
										</div>
										<div class="rew_live_search">
											<div class="rew_live_search_box">
												<input type="text" class="input_search" placeholder="이름, 부서명을 검색" id="input_index_search_new"/>
												<button id="lives_index_search_bt_new"><span>검색</span></button>
											</div>
										</div>
									</div>
								</div>
							</div>

							<div class="rew_live">
								<div class="rew_live_in">

									<div class="live_drop_right">
										<div class="ldr_in">
											<ul class="ldr_ul" id="ldr">

												<?
												for($i=0; $i<count($member_list_info_live['idx']); $i++){
													
													$like_jjim=0;

													$works_like_cnt = 0;
												
													//마음전하기
													$like_layer0 = false;

													//출근
													$like_layer1 = false;

													//오늘업무
													$like_layer2 = false;
													
													//
													$ldr_li = false;
													
													$member_list_idx = $member_list_info_live['idx'][$i];
													$member_list_email = $member_list_info_live['email'][$i];

													$member_list_name = $member_list_info_live['name'][$i];
													$member_list_part = $member_list_info_live['part'][$i];
													$member_list_memo = $member_list_info_live['memo'][$i];
	
													$member_list_penalty = $member_list_info_live['penalty_state'][$i];

													$live_1_time = $member_list_info_live['live_1_time'][$i];
													if($live_1_time){
														$tmp_live_1_time = explode(":", $live_1_time);
														if($tmp_live_1_time){
															$ex_live_1_time = (int)$tmp_live_1_time[0].":" .$tmp_live_1_time[1] ."";
														}
													}else{
														$ex_live_1_time = "";
													}

													$member_list_live_1 = $member_list_info_live['live_1'][$i];
													$member_list_live_2 = $member_list_info_live['live_2'][$i];
													$member_list_live_3 = $member_list_info_live['live_3'][$i];
													$member_list_live_4 = $member_list_info_live['live_4'][$i];
													$profile_type = $member_list_info_live['profile_type'][$i];
													$profile_img_idx = $member_list_info_live['profile_img_idx'][$i];
													$profile_file = $member_list_info_live['file_path'][$i].$member_list_info_live['file_name'][$i];
													$profile_img =  'https://rewardy.co.kr'.$member_list_info_live['file_path'][$i].$member_list_info_live['file_name'][$i];
													//프로필 케릭터 사진
													// $profile_img_src = profile_img_info($member_list_email);

													$member_work0 = $work_live[$member_list_email][0];
													$member_work1 = $work_live[$member_list_email][1];
													$member_work_all = $member_work0 + $member_work1;

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
													
													//오늘 출근 제일 빨리함
													if(!$like_flag_list[$member_list_email][1][0] && $mem_top_arr[TODATE] == $member_list_email){
														$like_jjim++;
													}

													//오늘업무 제일 많이씀
													if(!$like_flag_list[$member_list_email][2][0] && $works_top_arr[TODATE] == $member_list_email){
														$like_jjim++;
													}

													if($like_flag_list[$member_list_email][0][0]){
														$like_layer0 = true;
													}else{
														$like_layer0 = false;
													}

													if ($like_flag_list[$member_list_email][1][0]){
														$like_layer1 = true;
													}else{
														$like_layer1 = false;
													}

													if ($like_flag_list[$member_list_email][2][0]){
														$like_layer2 = true;
													}else{
														$like_layer2 = false;
													}

													if(!$like_layer0 && !$like_layer1 && !$like_layer2){
														$ldr_li = false;
													}else{
														$ldr_li = true;
													}

													//하트 채우기
													//출근 제일 빨리 함
													if( $like_flag_list[$member_list_email][1][0] && $mem_top_arr[TODATE] == $member_list_email){
														$like_class_on = " on";
													//업무 제일 많이 씀
													}else if($like_flag_list[$member_list_email][2][0] && $works_top_arr[TODATE] == $member_list_email){
														$like_class_on = " on";
													}else{
														//그냥 마음전하기
														if($like_flag_list[$member_list_email][0][0]){
															$like_class_on = " on";
														}else{
															$like_class_on = "";
														}
													}
													if($mem_like_flag_list[$member_list_email]){
														$mem_class_on = " star_on";
													}else{
														$mem_class_on = "";
													}
												?>
													<li class="ldr_li<?=$user_id==$member_list_email?" ldr_me":""?><?=$member_list_live_1=='0'?" live_none":""?>"<?=$user_id==$member_list_email?" id='live_ldr_me'":""?>>
														<div class="ldr_li_in">
															<div class="ldr_function">
																<div class="ldr_function_in">
																	<?if($user_id!=$member_list_email){?>
																		<button class="star_only mem_jjim_only<?=$mem_class_on?>" <?=$user_id!=$member_list_email && $ldr_li==false?"id='mem_jjim_only_".$member_list_idx."'":""?> value="<?=$member_list_idx?>"><span>즐겨찾기</span></button>
																	<?}?>
																	
																	<?if($user_id!=$member_list_email){?>
																		<button class="ldr_menu<?=$like_jjim?"":" jjim_only"?><?=$like_class_on?>"<?=$user_id!=$member_list_email && $ldr_li==false?"id='jjim_only_".$member_list_idx."'":""?> value="<?=$member_list_idx?>"><span>좋아요</span></button>
																	<?}?>
																	
																</div>
															</div>

															
															<?

															//자기자신은 좋아요 기능 제한함
															if($user_id != $member_list_email){?>

																<?if($like_jjim){?>

																	<div class="ldr_popup" id="ldr_popup">
																		<div class="ldr_popup_in">
																			<ul>
																				<?if(!$like_flag_list[$member_list_email][1][0] && $mem_top_arr[TODATE] == $member_list_email){?>
																					<li><button value="<?=$member_list_idx?>"><span>출근 제일 빨리 함</span></button></li>
																				<?}?>

																				<?if(!$like_flag_list[$member_list_email][2][0] && $works_top_arr[TODATE] == $member_list_email){?>
																					<li><button value="<?=$member_list_idx?>"><span>오늘업무 제일 많이 씀</span></button></li>
																				<?}?>

																				<?//if(!$like_flag_list[$member_list_email][0][0]){?>
																					<li><button value="<?=$member_list_idx?>"><span>그냥 마음전하기</span></button></li>
																				<?//}?>
																			</ul>
																		</div>
																	</div>

																<?}else{?>

																	<?//if(!$like_flag_list[$member_list_email][0][0]){?>
																		<div class="ldr_popup" id="ldr_popup">
																			<div class="ldr_popup_in">
																				<ul>
																					<li><button value="<?=$member_list_idx?>"><span>그냥 마음전하기</span></button></li>
																				</ul>
																			</div>
																		</div>
																	<?//}?>
																<?}?>
															<?}?>

															<div class="ldr_user">
																<div class="ldr_user_in">
																	<div class="ldr_user_desc">
																		<div class="ldr_user_name" id="ldr_user_name_<?=$member_list_idx?>"><?=$member_list_name?></div>
																		<div class="ldr_user_team" id="ldr_user_team_<?=$member_list_idx?>"><?=$member_list_part?></div>
																		<div class="ldr_time"><?=$ex_live_1_time?></div>
																		<input type="hidden" id="ldr_user_id_<?=$member_list_idx?>" value="<?=$member_list_email?>">
																	</div>
																	<div class="ldr_user_style">
																		<div class="ldr_user_img">
																			<div class="ldr_circle circle_01"><canvas width="64" height="64"></canvas></div>
																			<div class="ldr_user_img_bg"></div>
																			<div class="ldr_user_imgs" id="ldr_user_imgs_<?=$member_list_idx?>" value = "<?=$member_list_idx?>" style="background-image:url('<?=$profile_file?$profile_img:"/html/images/pre/img_prof_default.png"?>');"></div>
																			<?if($member_list_penalty=='1'){?>
																				<div class="ldr_user_penalty"><span></span></div>
																			<?}?>
																		</div>
																		<div class="ldr_user_state">
																			<div class="ldr_user_state_in">
																				<ul <?=$user_id==$member_list_email?" id='ldr_me_state'":""?>>
																					<?if($member_list_live_4 == "1"){?>
																						<li class="state_03">
																							<div class="ldr_user_state_circle">
																								<strong>퇴근</strong>
																							</div>
																							<div class="layer_state layer_state_03">
																								<div class="layer_state_in">
																									<p>업무를 모두 종료 하였습니다.</p>
																									<em></em>
																								</div>
																							</div>
																						</li>
																					<?}else if($member_decide_flag){?>
																						
																						<?if( count($member_decide_flag) == '1'){?>
																							<li class="state_04">
																								<div class="ldr_user_state_circle">
																									<strong><?=$work_decide_list[$work_flag_list[$today][$member_list_email][0]]?></strong>
																								</div>
																								<div class="layer_state layer_state_04">
																									<div class="layer_state_in">
																										<p><span><?=$work_decide_list[$work_flag_list[$today][$member_list_email][0]]?></span> 일정이 있습니다.</p>
																										<em></em>
																									</div>
																								</div>
																							</li>
																						<?}else{?>
																							<li class="state_04">
																								<div class="ldr_user_state_circle">
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
																										}?>
																										</span> 일정이 있습니다.</p>
																										<em></em>
																									</div>
																								</div>
																							</li>


																						<?}?>
																					<?}?>
																				</ul>
																			</div>
																		</div>
																	</div>
																</div>
															</div>
															
															<div class="ldr_anno" <?=$user_id==$member_list_email?" style = 'cursor : pointer;' ":""?>>
																<div class="ldr_anno_in">
																	<span>
																	<?if($member_list_memo){?>
																		<?= $member_list_memo;?>
																	<?}else{?>
																		-
																	<?}?>
																	</span>
																</div>
															</div>
															<div class="ldr_numbers">
																<?if($member_list_penalty=='1'){?>
																	<input type="hidden" value="<?=$member_list_penalty?>" id="user_penalty_<?=$member_list_idx?>">
																<?}?>
																<div class="ldr_today<?=$member_work_all>0?"":" today_num_none"?>" id="ldr_today">
																	<button class="ldr_today_num" value="<?=$member_list_email?>">
																		<strong><?=number_format($member_work_all)?></strong>
																		<span>업무</span>
																		<input type="hidden" id="ldr_today_num" value="<?=number_format($member_work1)?>">
																	</button>
																</div>
																<div class="ldr_chall<?=$reward_cp_sum[$member_list_email]>0?"":" challenges_num_none"?>">
																	<button class="ldr_chall_num" value="<?=$member_list_idx?>">
																		<strong><?=$reward_cp_sum[$member_list_email]>0?$reward_cp_sum[$member_list_email]:"0"?></strong>
																		<span>역량</span>
																	</button>
																</div>
																<div class="ldr_jjim<?=$like_list[$member_list_email]>0?"":" jjim_num_none"?>">
																	<button class="ldr_jjim_num" id="ldr_jjim_num_<?=$member_list_idx?>" value="<?=$member_list_idx?>">
																		<strong><?=$like_list[$member_list_email]?$like_list[$member_list_email]:"0"?></strong>
																		<span>좋아요</span>
																	</button>
																</div>
															</div>
														</div>
													</li>
												<?}?>

											</ul>
											<?if($gp >= $page_count){?>
											<div class="live_more" id="live_more" style="display:none">
												<button><span>more</span></button>
											</div>
											<?}else{?>
												<div class="live_more" id="live_more">
													<button><span>more</span></button>
												</div>
											<?}?>
										</div>
									</div>


								</div>
							</div>
						</div>

					</div>
				</div>
				<!-- //콘텐츠 -->
			</div>
		</div>
	</div>

	<!-- 한줄소감 -->
	<div class="feeling_first" style="display:none;">
		<div class="ff_deam"></div>
		<div class="ff_in">
			<div class="ff_box">
				<div class="ff_box_in">
					<div class="ff_close">
						<button><span>닫기</span></button>
					</div>
					<div class="ff_top">
						<strong>오늘 하루는 어땠나요?</strong>
					</div>
					<div class="ff_area">
						<ul>
							<li>
								<button class="btn_ff_01<?=$review_info['work_idx']==1?" on":""?>" value="1">
									<strong></strong>
									<span>최고야</span>
								</button>
							</li>
							<li>
								<button class="btn_ff_02<?=$review_info['work_idx']==2?" on":""?>" value="2">
									<strong></strong>
									<span>뿌듯해</span>
								</button>
							</li>
							<li>
								<button class="btn_ff_03<?=$review_info['work_idx']==3?" on":""?>" value="3">
									<strong></strong>
									<span>기분좋아</span>
								</button>
							</li>

							<li>
								<button class="btn_ff_04<?=$review_info['work_idx']==4?" on":""?>" value="4">
									<strong></strong>
									<span>감사해</span>
								</button>
							</li>
							<li>
								<button class="btn_ff_05<?=$review_info['work_idx']==5?" on":""?>" value="5">
									<strong></strong>
									<span>재밌어</span>
								</button>
							</li>
							<li>
								<button class="btn_ff_06<?=$review_info['work_idx']==6?" on":""?>" value="6">
									<strong></strong>
									<span>수고했어</span>
								</button>
							</li>

							<li>
								<button class="btn_ff_07<?=$review_info['work_idx']==7?" on":""?>" value="7">
									<strong></strong>
									<span>무난해</span>
								</button>
							</li>
							<li>
								<button class="btn_ff_08<?=$review_info['work_idx']==8?" on":""?>" value="8">
									<strong></strong>
									<span>지쳤어</span>
								</button>
							</li>
							<li>
								<button class="btn_ff_09<?=$review_info['work_idx']==9?" on":""?>" value="9">
									<strong></strong>
									<span>속상해</span>
								</button>
							</li>
						</ul>
					</div>
					<div class="ff_bottom">
						<input type="text" id="icon_idx" value="<?=$review_info['work_idx']?>">
						<button class="btn_off">다음</button>
						<input type="hidden" id="review_idx">
					</div>
				</div>
			</div>
		</div>
	</div>

	<div class="feeling_layer" style="display:none;">
		<div class="fl_deam"></div>
		<div class="fl_in">
			<div class="fl_box">
				<div class="fl_box_in">
					<div class="fl_close">
						<button><span>닫기</span></button>
					</div>
					<div class="fl_top">
						<strong>오늘 하루는 어땠나요?</strong>
					</div>
					<div class="fl_area">
						<div class="fl_desc">
							<strong></strong>
							<p><span>최고의</span> 하루였어요!</p>
						</div>
						<div class="fl_input">
							<input type="text" class="input_fl" placeholder="한줄소감을 남겨주세요!" id="input_fl" value="<?=$review_info['comment']?>"/>
						</div>
					</div>
					<div class="fl_bottom" id="fl_bottom">
						<button>퇴근합니다</button>
					</div>
				</div>
			</div>
		</div>
	</div>

	<div class="t_layer rew_layer_character item_prof" style="display:none;">
		<input type='hidden' id='check_profile'>
		<div class="tl_deam"></div>
		<div class="tl_in">
			<div class="tl_close">
				<button><span>닫기</span></button>
			</div>
			<div class="tl_tit">
				<strong>캐릭터 설정</strong>
				<span>리워디에서 기본으로 제공하는 <br />캐릭터입니다.</span>
			</div>
			<div class="tl_profile">
				<ul>
					<?for($i=0; $i<count($character_img_info['idx']); $i++){

						$idx = $character_img_info['idx'][$i];
						$file_path = $character_img_info['file_path'][$i];
						$file_name = $character_img_info['file_name'][$i];
						$fp_flag = $character_img_info['fp_flag'][$i];

						$character_img_src = $file_path.$file_name;

						$posi = $i + 1;

						if($fp_flag == 1){
							$pos_cn = $pos_cn + 1;
							$pos_ht = "class='pos_ht kp$pos_cn'";
						}
					?>
						<li id="posi_<?=$posi?>" <?=$pos_ht?>>
							<div class="tl_profile_box">
								<div class="tl_profile_img" style="background-image:url(<?=$character_img_src?>);">
									<?if($fp_flag == 0 || $img_buy_arr[$idx] != ''){?>
										<button class="btn_profile<?=$member_row_info['profile_type']=='0' && $member_row_info['profile_img_idx']==$idx?" on":""?>" id="profile_img_0<?=$idx?>" value="<?=$idx?>"><span>기본 프로필 이미지1 선택</span></button>
									<?}else{?>
										<button class="btn_profile" id="item_img_0<?=$idx?>" value="<?=$idx?>"><span>기본 프로필 이미지1 선택</span></button>
									<?}?>
								</div>
							</div>
							<?if($fp_flag == 1 && $img_buy_arr[$idx] == ''){?>
								<button class="btn_prof_lock"><span>닫힘</span></button>
							<?}?>
						</li>
					<?}?>
				</ul>
			</div>
			<div class="tl_btn">
				<button id="tl_profile_bt"><span>적용</span></button>
			</div>
		</div>
	</div>

	<?	
		//튜토리얼 시작 레이어
		include $home_dir . "/layer/tutorial_start.php";

		//튜토리얼 시작 레이어
		include $home_dir . "/layer/tutorial_main_level.php";

		//좋아요 레이어
		include $home_dir . "/layer/member_jjim.php";

		//아이템 레이어
		include $home_dir . "/layer/item_img_buy.php";

		//코인보상하기 레이어
		include $home_dir . "/layer/member_reward.php";

		//쪽지보내기 레이어
		include $home_dir . "/layer/mess_pop.php";

		//회의 팝업
		include $home_dir . "/layer/meeting_pop.php";
		
		//프로필 팝업
		include $home_dir . "/layer/pro_pop.php";

		include $home_dir . "/layer/char_pop.php";
	?>
	<?php
	//비밀번호 재설정
		include $home_dir . "/layer/member_repass.php";
	?>
	
	<?php
	 //좋아요 그래프 / 상세보기
		include $home_dir . "/layer/member_like_layer.php";
	?>

	<?php
		// 라이브 메뉴 레이어
		include $home_dir . "/layer/live_layer.php";
	?>
	<?php
		// 멤버 역량 레이어
		include $home_dir . "/layer/member_radar_layer.php";
	?>


	<?php
	//지각 경고 패널티카드
	include $home_dir . "/layer/penalty_pop_01.php";
								
	//로딩 페이지
	include $home_dir . "loading.php";
	?>

</div>


	<!-- footer start-->
	<? include $home_dir . "/inc_lude/footer.php";?>
	<!-- footer end-->
	<script type="text/javascript">
		$(document).ready(function(){
			window.onbeforeunload = function () { $('.rewardy_loading_01').css('display', 'block'); }
			$(window).load(function () {          //페이지가 로드 되면 로딩 화면을 없애주는 것
				$('.rewardy_loading_01').css('display', 'none');
			});
		});	
		window.onpageshow = function(event) {
 	     if ( event.persisted || (window.performance && window.performance.navigation.type == 2)) {
			  $('.rewardy_loading_01').css('display', 'none');
  		  }
		}

		
	</script>
	<script src= "/live/js/lives.js"></script>
	<!-- Step 1) Load D3.js -->
	<script src="https://d3js.org/d3.v6.min.js"></script>
	<!-- Step 2) Load billboard.js with style -->
	<script src="https://cdnjs.cloudflare.com/ajax/libs/billboard.js/3.9.1/billboard.min.js"></script>
	<!-- Load with base style -->
</body>
</html>