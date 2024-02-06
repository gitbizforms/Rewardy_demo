<?
	//header페이지
	$home_dir = str_replace( basename(__DIR__) , "" , __DIR__ );
	include $home_dir . "/inc_lude/header.php";
	
	//페이지수
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


	//회원전체

	//메인 전체 회원 리스트
	$sql = "select a.idx, a.email, a.name, a.part, a.partno, a.gender, a.profile_type, a.profile_img_idx, b.file_path, b.file_name,  a.live_1, a.live_2, a.live_3, a.live_4,";
		$sql .= " DATE_FORMAT(a.live_1_regdate, '%H:%i') as live_1_time,";
		$sql .= " a.live_1_regdate";
		$sql .= " from work_member a";
		$sql .= " left join work_member_profile_img b on a.email = b.email";
		$sql .= " where 1=1 and a.state='0'";

		//관리권한은 제외처리
		if($user_level == 1){
			$sql .= " and a.email!='".$user_id."'";
		}else{
			$sql .= " and a.companyno='".$companyno."'";
			$sql .= " and a.highlevel!='1'";
		}
		
		$sql .= " order by";
		$sql .= " CASE WHEN a.email='".$user_id."' THEN a.email WHEN a.partno='".$user_part."' then  a.partno END DESC,";
		if($val1=='true' || $val2=='true'){
		}else{
			$sql .= " CASE WHEN a.live_2='1' or a.live_3='1' THEN a.email END DESC,";
		}

		$sql .= " CASE WHEN a.live_4 = '1' THEN a.live_4_regdate END desc,";
		$sql .= " CASE WHEN a.live_2 = '1' THEN a.live_2_regdate END desc,";
		$sql .= " CASE WHEN a.live_3 = '1' THEN a.live_3_regdate END desc,";
		$sql .= " CASE WHEN a.live_2 = '0' THEN a.live_1_regdate END desc,";
		$sql .= " CASE WHEN a.live_3 = '0' THEN a.live_1_regdate END desc,";
		$sql .= " CASE WHEN a.live_1_regdate is null THEN a.name END asc,";
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
		
	
		$profile_type = $member_one_info['profile_type'];
		$profile_img_idx = $member_one_info['profile_img_idx'];
		$profile_img =  'http://demo.rewardy.co.kr'.$member_one_info['file_path'].$member_one_info['file_name'];
		$member_info_coin = number_format($member_info_coin);


		if($live_1!='1' && $live_1_regdate == NULL){
			$switch_ready = " switch_ready";
		}else{
			$switch_ready = "";
		}

	}



	//코인보상
	$sql = "select idx,code,coin,icon,memo from work_coin_reward_info where state='0' and kind='live' order by idx asc";
	$coin_reward_info = selectAllQuery($sql);
	
	// 업무일정
	$sql = "select email , state, work_flag, decide_flag from work_todaywork use index(state) where state='0' and companyno='".$companyno."' and share_flag!='2' and workdate='".TODATE."' group by email, state, work_flag, decide_flag";
	$works_myinfo = selectAllQuery($sql);
	if($works_myinfo['email']){
		for($i=0; $i<count($works_myinfo['email']); $i++){

			$works_myinfo_email = $works_myinfo['email'][$i];
			$work_list[TODATE][$works_myinfo_email][$works_myinfo['state'][$i]] = $works_myinfo['cnt'][$i];

			//업무예약일정
			if($works_myinfo['work_flag'][$i] =='2'&& $works_myinfo['decide_flag'][$i] != '0'){
				$work_flag_list[TODATE][$works_myinfo_email][] = $works_myinfo['decide_flag'][$i];
			}
		}
	}
	//업무예약
	$sql = "select idx, title from work_decide where state='0' and type_flag='0'";
	$work_decide_info = selectAllQuery($sql);
	if($work_decide_info['idx']){
		$work_decide_list = @array_combine($work_decide_info['idx'], $work_decide_info['title']);
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
	$sql = "select a.idx, a.email, b.name, a.work_flag, a.work_idx, a.share_flag, a.secret_flag, a.title, a.contents, a.editdate, a.regdate from work_todaywork as a use index(state) left join work_member as b on(a.email=b.email)";
	$sql = $sql .= " where a.state not in('9','99','1') and a.share_flag != '2' and repeat_flag = 0  and a.companyno='".$companyno."' and a.notice_flag='0' and a.workdate='".TODATE."' and b.state = 0 and (a.work_idx is null or a.share_flag = 1) order by a.regdate desc";
	$works_realtime_info = selectAllQuery($sql);

	//관리권한은 제외처리
	if($user_level == 1){
		$where_sql = $where_sql .= " and email !='".$user_id."'";
	}else{
		$where_sql = $where_sql .= " and companyno='".$companyno."'";
	}

	// 개인 업무 총 갯수
	$sql = "select email, state, count(1) as cnt from work_todaywork use index(state) where workdate = '".TODATE."' and state != 9 and companyno = '".$companyno."' and notice_flag = '0' group by state, email;";
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
				<!-- menu -->
				<? include $home_dir . "/inc_lude/menu.php";?>
				<!-- //menu -->

				<!-- 콘텐츠 -->
				<div class="rew_conts">
					<div class="rew_conts_in">

						<div class="rew_live_my">
							<div class="rew_live_my_in">
								<div class="rew_live_my_left">
									<div class="rew_live_my_user_img">
										<div class="rew_live_my_user_img_bg"></div>
										<div class="rew_live_my_user_imgs" style="background-image:url('<?=$profile_type >= '0'?$profile_img:"/html/images/pre/img_prof_default.png"?>');"></div>
									</div>
									<div class="rew_live_my_user_name">
										<strong><?=$member_info_name?></strong>
										<span><?=$part_name?></span>
										<input type="hidden" id="chall_user_cnt" value="<?=$member_total_cnt?>">
										<input type="hidden" id="chall_user_chk">
									</div>
									<div class="rew_live_my_list_today" style="display:none;">
										<input type="hidden" id="user_email" value="all">
										<button class="btn_today" value="<?=$member_info_email?>">
											<div class="rew_live_my_list_today_tit">오늘업무</div>
											<div class="rew_live_my_list_today_count"><strong><?=$work_com_list?></strong> / <?=$work_all_list?></div>
										</button>
									</div>
									<div class="rew_live_my_list_cha" style="display:none;">
										<button class="btn_cha" id="btn_cha_layer">
											<div class="rew_live_my_list_cha_tit">챌린지</div>
											<div class="rew_live_my_list_cha_count"><strong></strong></div>
										</button>
									</div>
								</div>

								<div class="rew_live_my_right">
									<div class="rew_live_my_tit">
										<span>나의 상태</span>
									</div>
									<div class="rew_grid_onoff">
										<div class="rew_grid_onoff_inner">
											<ul>
												<li class="onoff_01">
													<?if($live_1=='1'){?>
														<em <?=($live_1=='1')?"class='on'":""?>>근무중</em>
													<?}else{?>
														<em>출근</em>
													<?}?>
													<div class="btn_switch<?=($live_1=='1')?" on":" ".$switch_ready.""?>" id="live_1_bt">
														<strong class="btn_switch_on"></strong>
														<span>버튼</span>
														<strong class="btn_switch_off"></strong>
													</div>
												</li>
											</ul>
										</div>
									</div>

									<div class="rew_grid_onoff">
										<div class="rew_grid_onoff_in">
											<ul>
												<li class="onoff_02">
													<em <?=($live_2=='1')?"class='on'":""?>>집중</em>
													<div class="btn_switch<?=($live_2=='1')?" on":""?>" id="live_2_bt">
														<strong class="btn_switch_on"></strong>
														<span>버튼</span>
														<strong class="btn_switch_off"></strong>
													</div>
												</li>
												<li class="onoff_03">
													<em <?=($live_3=='1')?"class='on'":""?>>자리비움</em>
													<div class="btn_switch<?=($live_3=='1')?" on":""?>" id="live_3_bt">
														<strong class="btn_switch_on"></strong>
														<span>버튼</span>
														<strong class="btn_switch_off"></strong>
													</div>
												</li>
												<?
												?>
											</ul>
										</div>
									</div>
								</div>
							</div>
						</div>

						<div class="rew_conts_scroll_07">

							<div class="rew_live">
								<div class="rew_live_in">

									<div class="rew_live_func">
										<div class="rew_live_func_in">
											<div class="rew_live_count">
												<span>LIVE</span>
												<strong id="lives_list_cnt"><span><?=$member_live_cnt?></span>(<?=$member_all_cnt?>)</strong>
													<input type="hidden" id="pageno" value="<?=$gp?>">
													<input type="hidden" id="page_count" value="<?=$page_count?>">
											</div>

											<div class="rew_live_sort" id="rew_live_sort_list">
												<div class="rew_live_sort_in">
													<button class="btn_sort_on" id="rew_live_sort"><span>전체보기</span></button>
													<ul>
														<li><button value="all"><span>전체보기</span></button></li>
														<li><button value="on"><span>접속자보기</span></button></li>
														<li><button value="off"><span>미접속자보기</span></button></li>
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
	
													$member_list_live_1 = $member_list_info_live['live_1'][$i];
													$member_list_live_2 = $member_list_info_live['live_2'][$i];
													$member_list_live_3 = $member_list_info_live['live_3'][$i];
													$member_list_live_4 = $member_list_info_live['live_4'][$i];
													$profile_type = $member_list_info_live['profile_type'][$i];
													$profile_img_idx = $member_list_info_live['profile_img_idx'][$i];
													$profile_img =  'http://demo.rewardy.co.kr'.$member_list_info_live['file_path'][$i].$member_list_info_live['file_name'][$i];
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
														$member_decide_flag = "";
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

												?>
													<li class="ldr_li<?=$user_id==$member_list_email?" ldr_me":""?><?=$member_list_live_1=='0'?" live_none":""?>"<?=$user_id==$member_list_email?" id='live_ldr_me'":""?>>
														<div class="ldr_li_in">
															<div class="ldr_function">
																<div class="ldr_function_in">
																	<div class="ldr_time"><?=$ex_live_1_time?></div>
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
																		<input type="hidden" id="ldr_user_id_<?=$member_list_idx?>" value="<?=$member_list_email?>">
																	</div>
																	<div class="ldr_user_style">
																		<div class="ldr_user_img">
																			<div class="ldr_circle circle_01"><canvas width="64" height="64"></canvas></div>
																			<div class="ldr_user_img_bg"></div>
																			<div class="ldr_user_imgs" id="ldr_user_imgs_<?=$member_list_idx?>" style="background-image:url('<?=$profile_type >= '0'?$profile_img:"/html/images/pre/img_prof_default.png"?>');"></div>
																			<?if($penalty_info_cnt[$member_list_email]){?>
																				<div class="ldr_user_penalty"><span><?=$penalty_info_cnt[$member_list_email]?$penalty_info_cnt[$member_list_email]:""?></span></div>
																			<?}?>

																		</div>
																		<div class="ldr_user_state">
																			<div class="ldr_user_state_in">
																				<ul <?=$user_id==$member_list_email?" id='ldr_me_state'":""?>>
																					<?if($member_list_live_2 == "1"){?>
																						<li class="state_01">
																							<div class="ldr_user_state_circle">
																								<strong>집중</strong>
																							</div>
																							<div class="layer_state layer_state_01">
																								<div class="layer_state_in">
																									<p>업무에 집중하고 있습니다.</p>
																									<em></em>
																								</div>
																							</div>
																						</li>
																					<?}?>

																					<?if($member_list_live_3 == "1"){?>
																						<li class="state_02">
																							<div class="ldr_user_state_circle">
																								<strong>잠시</strong>
																							</div>
																							<div class="layer_state layer_state_02">
																								<div class="layer_state_in">
																									<p>잠시 자리를 비웁니다.</p>
																									<em></em>
																								</div>
																							</div>
																						</li>
																					<?}?>

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
																					<?}?>


																					<?if ($member_decide_flag){?>
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
																						<?}?>
																					<?}?>
																				</ul>
																			</div>
																		</div>
																	</div>
																</div>
															</div>

															<div class="ldr_numbers">
																<div class="ldr_today<?=$member_work_all>0?"":" today_num_none"?>" id="ldr_today">
																	<button class="ldr_today_num" value="<?=$member_list_email?>">
																		<strong><?=number_format($member_work_all)?></strong>
																		<span>업무</span>
																		<input type="hidden" id="ldr_today_num" value="<?=number_format($member_work1)?>">
																	</button>
																</div>
																<div class="ldr_chall<?=$reward_cp_sum[$member_list_email]>0?"":" challenges_num_none"?>">
																	<button class="ldr_chall_num">
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

	

	<?	//좋아요 레이어
		include $home_dir . "/layer/member_jjim.php";

		//코인보상하기 레이어
		include $home_dir . "/layer/member_reward.php";
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
		//튜토리얼 레벨 레이어
		include $home_dir . "/layer/tutorial_main_level.php";
	?>
	<?php
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