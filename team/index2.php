<?
	//header페이지
	$home_dir = str_replace( basename(__DIR__) , "" , __DIR__ );
	include $home_dir . "/inc_lude/header.php";
?>
<link rel="stylesheet" type="text/css" href="/html/css/penalty_pop.css<?php echo VER;?>" />
<?
	//로그인 아이디가 없을경우, 메일 발송 여부 체크 하여 가입 유도
	$passwdreset = "";
	if(!$user_id){
		if($_SERVER['QUERY_STRING']){

			@parse_str(Decrypt($_SERVER['QUERY_STRING']));

			//암호화
			//echo Encrypt("send_email=보낸메일&to_email=받은메일&sendno=회원idx");
			if($send_email && $to_email && $sendno){
				//메일수신확인 체크
				$sql = "select idx, email, name, part, company, highlevel from work_member where idx='".$sendno."'";
				$sendmail_info = selectQuery($sql);
				if($sendmail_info['idx']){
					$receive_name = $sendmail_info['name'];
					$receive_part = $sendmail_info['part'];
					$receive_company = $sendmail_info['company'];
				}
			}
		}else{
			echo '<script>location.href="/index.php";</script>';
			exit;
		}
	}else{

		@parse_str(Decrypt($_SERVER['QUERY_STRING']), $output);
		
		if($output){
			//패스워드 리셋
			if($output['send'] == 'passwdreset'){
				//비밀번호 난수 발F생
				$passwdreset = true;
			}
		}
	}

	//메인 전체 회원 리스트
	$member_list_info = member_main_team_list();


	//회원정보
	$member_row_info = member_row_info($user_id);
	//출근전상태
	if($member_row_info['live_1']!='1'){
		$switch_ready = " switch_ready";
	}

	//프로필 캐릭터 사진
	$character_img_info = character_img_info();

	//오늘업무 등록,완료갯수
	$sql = "select email , state, work_flag, decide_flag from work_todaywork where state!='9' and workdate='".TODATE."' group by email, state, work_flag, decide_flag";
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
	}

	//업무 예약
	$work_decide_info = work_decide_info();
	if($work_decide_info['idx']){	
		$work_decide_list = @array_combine($work_decide_info['idx'], $work_decide_info['title']);	
	}

	//타임라인
	$timeline_info = timeline_info();
	
	// 좋아요 갯수
	$like_month_info = like_month_info();

	if ($like_month_info['cnt']){
		$like_month_cnt = $like_month_info['cnt'];
	}else{
		$like_month_cnt = 0;
	}
	
	$like_list_info = like_list_info();

	$main_like_today = main_like_today();
	for($i=0; $i<count($main_like_today['email']); $i++){
		$main_l_today_em = $main_like_today['email'][$i];
		$main_l_today_ki = $main_like_today['like_kind'][$i];
		$main_l_today_arr[$main_l_today_ki] = $main_l_today_em;
	}

	//오늘의 좋아요
	$like_info = like_info();
	for($i=0; $i<count($like_info['idx']); $i++){
		$like_info_workdate = $like_info['workdate'][$i];
		$like_info_kind_flag = $like_info['kind_flag'][$i];
		$today_like[$like_info_workdate][$like_info_kind_flag]++;
	}
	
	//한줄소감
	$review_info = review_info();
	
	//오늘날짜의 업무시작 시간부터 증가
	$work_reward_add = work_reward_add();
	$cp_avg = array(
		'type1' => $work_reward_add['sum_type1'],
		'type2' => $work_reward_add['sum_type2'],
		'type3' => $work_reward_add['sum_type3'],
		'type4' => $work_reward_add['sum_type4'],
		'type5' => $work_reward_add['sum_type5'],
		'type6' => $work_reward_add['sum_type6']
	);

	//업데이트시간
	$updatetime = date("m/d H:i" , TODAYTIME);

	//패널티 카드 적용
	$sql = "select email, sum(incount) as incount, sum(outcount) as outcount, sum(work) as work, sum(challenge) as chall from work_member_penalty where state = '0' and email = '".$user_id."' group by email";
	$query = selectQuery($sql);
	// $incount = $query['incount'] + 1;
	$outcount = $query['outcount'];
	$workcount = $query['work'];
	$challcount = $query['chall'];

	$tp_outcount = "";
	$tp_work = "";
	$sql = "select * from work_member_penalty where email = '".$user_id."' and state = '0' and DATE_FORMAT(updatetime, '%Y-%m-%d') = '".TODATE."' order by idx desc";
	$row = selectAllQuery($sql);

	// for($i=0; $i<count($row['idx']); $i++){
	// 	if($row['work'][$i]=="1"){
	// 		$tp_work = "true";
	// 	}else if($row['outcount'][$i]=="1"){
	// 		$tp_outcount = "true";
	// 	}
	// }

	//역량평가지표
	$work_reward_info = work_reward_info();
	$avg_point = 1;
	
	foreach ($work_reward_info['idx'] as $i => $idx) {
		$reward_email = $work_reward_info['email'][$i];
		$reward_reg = $work_reward_info['workdate'][$i];
	
		for ($j = 1; $j <= 6; $j++) {
			$type = 'type' . $j;
			$reward_cp_type[$reward_email][$type] += $work_reward_info[$type][$i] * $avg_point;
		}
	}

	if($user_id){
		//리워디 역량지표 합계
		//역량지표평가등급
		$reward_cp_sum = @array_sum($reward_cp_type[$user_id]);
		if(!$reward_cp_sum){
			$reward_cp_sum = "0";
		}

		$cp_graph = array();
		$cp_types = array(1 => 'type1', 2 => 'type2', 3 => 'type3', 4 => 'type4', 5 => 'type5', 6 => 'type6');
		$cp_rates = array();
		foreach ($cp_types as $cp_key => $cp_value) {
			$cp_type_info = reward_cp_type_info($reward_cp_type[$user_id][$cp_value], $cp_key);
			${"cp_type$cp_key"} = $cp_type_info['cp_type'];
			${"cp_type${cp_key}_sp"} = $cp_type_info['cp_type_sp'];
			${"cp_graph$cp_key"} = $cp_type_info['cp_graph'];
			${"cp_rate$cp_key"} = cp_rate_info(${"cp_type$cp_key"});
		
		}


		//역량 할당코인
		$work_com_reward = work_com_reward_day($user_id);

		//역량 목표치
		$cp_pro_js = $work_com_reward['cp_per'];


		//좋아요 할당코인
		$work_like_reward = work_like_reward_day($user_id);

		//좋아요 목표치
		$like_pro_js = $work_like_reward['like_per'];

	}else{
		$reward_cp_sum = 0;
	}

	// //전체 획득한 코인
	$com_reward_info = com_reward_info();
	
	if($com_reward_info['tot']){
		$total_reward_coin = number_format($com_reward_info['tot']);
		$com_reward_plus_coin = number_format($com_reward_info['plus_coin']);
	}

	//좋아요 할당된 코인
	$like_reward_info = like_reward_info();

	if($like_reward_info['tot']){
		$total_like_coin = number_format($like_reward_info['tot']);
		$like_reward_plus_coin = number_format($like_reward_info['plus_coin']);
	}


	$img_buy_arr = array();
	//프로필 캐릭터 구입여부
	$sql = "select idx,item_idx from work_item_info where state = '0' and member_email = '".$user_id."'";
	$img_buy_flag = selectAllQuery($sql);

	for($i=0; $i<count($img_buy_flag['idx']); $i++){
		$img_buy_idx = $img_buy_flag['idx'][$i];
		$img_item_idx = $img_buy_flag['item_idx'][$i];
		$img_buy_arr[$img_item_idx] = $img_buy_idx;
	}



?>
<html>
<head>
	<link rel="stylesheet" href="/html/css/billboard.css">
</head>
<body>

<div class="rew_warp">
	<div class="rew_warp_in">
		<div class="rew_box">
			<div class="rew_box_in">
				<!-- menu -->
				<? include $home_dir . "/inc_lude/menu.php";?>
				<!-- //menu -->

				<!-- 콘텐츠 -->
				<div class="rew_conts">
					<div class="rew_conts_in">

						<div class="rew_conts_scroll_00">
							<div class="rew_mains">
								<div class="rew_mains_in">
									<div class="rew_mains_timeline">
										<div class="rew_mains_timeline_in">
											<div class="rew_timeline">
												<div class="rtl_top">
													<strong>타임라인</strong>
												</div>
												<div class="rtl_list">
													<div class="rtl_list_in">
														<ul>
															<?for($i=0; $i<count($timeline_info['idx']); $i++){

																$code = $timeline_info['code'][$i];
																$coin = $timeline_info['coin'][$i];
																$memo = $timeline_info['memo'][$i];
																$tsend_email = $timeline_info['send_email'][$i];
																$tsend_name = $timeline_info['send_name'][$i];
																$reg = $timeline_info['reg'][$i];
																$regdate = $timeline_info['regdate'][$i];
																$service = $timeline_info['service'][$i];
																$tworkdate = $timeline_info['tworkdate'][$i];
																$workdate = $timeline_info['workdate'][$i];

																$regdate_tmp = @explode(" ", $regdate);
																$regdate_ymd = $regdate_tmp[0];
																$regdate_his = $regdate_tmp[1];
																$regdate_apm = $regdate_tmp[2];
																$ymd_tmp = @explode("-", $regdate_ymd);
																$his_tmp = @explode(":", $regdate_his);
																$info_ampm = $regdate_apm;
																$info_hm = (strlen($his_tmp[0])==1?"0":"").$his_tmp[0].":".$his_tmp[1];
																$info_memo = "";
																//공유함
																if(in_array($code, array('4','7','23'))){
																	$info_memo = $tsend_name . "님 " .$memo;
																}else{
																	if($tsend_name){
																		$info_memo = $tsend_name . "님에게 " .$memo;
																	}else{
																		$info_memo = $memo;
																	}
																}

																//보상받음
																if(in_array($code , array('21','24','25'))){
																	$li_class = " class='rtl_coin'";
																	$em_coin = "<em>".number_format($coin)."</em>";
																}else{
																	$li_class = "";
																	$em_coin = "";
																}

																if($service == 'live' || $service == 'main'){
																	$cursor_de = " style='cursor:default;'";
																}else{
																	$cursor_de = "";
																}

																if($tworkdate == TODATE){
																	$tworkdate = 1;
																}

																?>
																<li <?=$li_class?> onclick="link_page('<?=$service?>','<?=$workdate?>')">
																	<div class="rtl_list_box">
																		<dl <?=$cursor_de?>>
																			<dt><strong><?=$info_memo?></strong></dt>
																			<dd><span><?=$info_hm?> <?=$info_ampm?></span><?=$em_coin?></dd>
																		</dl>
																	</div>
																</li>
															<?}?>
														</ul>


														<ul class="rtl_list_old">
															<?for($i=0; $i<count($read_info['workdate']); $i++){

																$read_info_date = $read_info['workdate'][$i];
																$read_info_cnt = $read_info['cnt'][$i];
																$read_info_dd = $read_info['dd'][$i];
																$read_date_val = str_replace("-", ".", $read_info_date);
															?>
																<li class="old_<?=$read_info_dd?>d" id="rtlu_list_<?=$i?>" value="<?=$read_date_val?>">
																	<div class="rtl_list_box">
																		<dl>
																			<dt><strong><?=$read_info_dd?>일전 읽지 않은 게시물 <?=$read_info_cnt?>건</strong></dt>
																			<dd><span><?=$read_info_date?></span></dd>
																		</dl>
																	</div>
																</li>
															<?}?>
														</ul>
													</div>
												</div>
											</div>
										</div>
									</div>

									<div class="rew_mains_left">
										<div class="rew_mains_left_in">
											<div class="rew_mains_info">
												<div class="rew_mains_info_in">
													<div class="rew_mains_info_me">
														<div class="rew_mains_info_l">
															<div class="tl_prof">
																<div class="tl_prof_box">
																	<div class="tl_prof_img" style="background-image:url('<?=$member_row_info['profile_img_src']?$member_row_info['profile_img_src']:"/html/images/pre/img_prof_default.png"?>');" id="profile_character_img"></div>
																	<div class="tl_prof_slc">
																		<div class="tl_prof_slc_in">
																			<button class="button_prof"><span>프로필 변경</span></button>
																			<ul>
																				<li><button id="btn_slc_character"><span>캐릭터 선택</span></button></li>
																				<li>
																					<input type="file" id="prof" class="input_prof" />
																					<label for="prof" class="label_prof" id="profile_img_change"><span>나만의 이미지 선택</span></label>
																				</li>
																				<li><button class="default_on" id="character_default"><span>기본 이미지로 변경</span></button></li>
																			</ul>
																		</div>
																	</div>
																</div>
																<div class="rew_mains_info_name">
																	<strong><?=$member_row_info['name']?>님, 안녕하세요!</strong>
																	<span><?=$member_row_info['part']?></span>
																	<input type="hidden" id="mains_info_uid" value="<?=$user_id?>">
																</div>
															</div>

															<div class="rew_mypage_coin_box">
																<div class="title_area">
																	<div class="qna">
																		<strong class="title_main">내 코인</strong>
																	</div>
																</div>
																<div class="rew_mypage_coin_chall" id="rew_mypage_coin_chall">
																	<strong><span><?=$member_row_info['coin']?></span></strong>
																</div>
															</div>
														</div>


														<!--//에너지1, 성과2, 성장3, 협업4, 성실5, 실행6-->
														<div class="rew_mains_info_r">
															<div class="rew_mains_chart_graph">
															<div id="radarChart"></div>
																<div class="radar_grade radar_01">
																	<span class="radar_tit">에너지</span>
																	<em class="grade_<?=strtolower($cp_rate1)?>"><?=$cp_rate1?></em>
																	<span class="radar_pt"><?=$cp_type1_sp>0?"(".$cp_type1_sp.")":"(0.0)"?></span>
																</div>
																<div class="radar_grade radar_02">
																	<span class="radar_tit">성장</span>
																	<em class="grade_<?=strtolower($cp_rate3)?>"><?=$cp_rate3?></em>
																	<span class="radar_pt"><?=$cp_type3_sp>0?"(".$cp_type3_sp.")":"(0.0)"?></span>
																</div>
																<div class="radar_grade radar_03">
																	<span class="radar_tit">성실</span>
																	<em class="grade_<?=strtolower($cp_rate5)?>"><?=$cp_rate5?></em>
																	<span class="radar_pt"><?=$cp_type5_sp>0?"(".$cp_type5_sp.")":"(0.0)"?></span>
																</div>
																<div class="radar_grade radar_04">
																	<span class="radar_tit">실행</span>
																	<em class="grade_<?=strtolower($cp_rate6)?>"><?=$cp_rate6?></em>
																	<span class="radar_pt"><?=$cp_type6_sp>0?"(".$cp_type6_sp.")":"(0.0)"?></span>
																</div>
																<div class="radar_grade radar_05">
																	<span class="radar_tit">협업</span>
																	<em class="grade_<?=strtolower($cp_rate4)?>"><?=$cp_rate4?></em>
																	<span class="radar_pt"><?=$cp_type4_sp>0?"(".$cp_type4_sp.")":"(0.0)"?></span>
																</div>
																<div class="radar_grade radar_06">
																	<span class="radar_tit">성과</span>
																	<em class="grade_<?=strtolower($cp_rate2)?>"><?=$cp_rate2?></em>
																	<span class="radar_pt"><?=$cp_type2_sp>0?"(".$cp_type2_sp.")":"(0.0)"?></span>
																</div>
																<div class="radar_total">
																	<span><?=$reward_cp_sum?></span>
																</div>
															</div>

															<div class="rew_mains_chart_state">
																<div class="rew_mains_chart_state_tit qna">
																	<em>AI 알림</em>
																	<div class="rew_mains_chart_state_tit_txt">
																		이달에 적립된 코인을 확인하세요!
																	</div>
																</div>

																<div class="rew_mains_chart_state_in">
																	<ul>
																		<li>
																			<div class="title_area">
																				<div class="qna">
																					<strong class="title_main">역량</strong>
																					<span class="qna_q open_qna_01">?</span>
																				</div>
																			</div>
																			<div class="chart_state_score_coin">
																				<div class="chart_state_score">
																					<span><?=$reward_cp_sum>0?"+ ".$reward_cp_sum."":"0"?>점</span>
																				</div>
																				<div class="chart_state_coin">
																					<strong><span><?=$total_reward_coin>0?$total_reward_coin:"0"?></span></strong>
																					<p class="coin_up">Today <?=$com_reward_plus_coin>0?$com_reward_plus_coin:"0"?> <em>▲</em></p>
																				</div>
																			</div>
																		</li>
																		<li>
																			<div class="title_area">
																				<div class="qna">
																					<strong class="title_main">좋아요</strong>
																					<span class="qna_q open_qna_02">?</span>
																				</div>
																			</div>
																			<div class="chart_state_score_coin">
																				<div class="chart_state_score">
																					<span><?=$like_month_cnt>0?"+ ".$like_month_cnt."":"0"?>점</span>
																				</div>
																				<div class="chart_state_coin">
																					<strong><span><?=$total_like_coin>0?$total_like_coin:"0"?></span></strong>
																					<p class="coin_up">Today <?=$like_reward_plus_coin>0?$like_reward_plus_coin:"0"?> <em>▲</em></p>
																				</div>
																			</div>
																		</li>
																	</ul>
																</div>
															</div>

														</div>
													</div>


													<div class="rew_mains_heart_area">
														<div class="rew_mains_heart_area_in">
															<div class="rew_mains_heart_tit">
																<em>AI 추천</em>
																<span>공유, 보고, 메모를 잘하는 동료, 하트로 응원해 보아요!!</span>
															</div>
															<div class="rew_mains_heart_list" id="rew_mains_heart_list">
																<ul>

																<?for($i=0; $i<count($like_list_info['idx']); $i++){

																	$like_idx = $like_list_info['idx'][$i];
																	$like_email = $like_list_info['email'][$i];
																	$like_name = $like_list_info['name'][$i];
																	$like_comment = $like_list_info['memo'][$i];
																	$like_kind = $like_list_info['kind'][$i];

																	if($like_comment == '불꽃 업무중'){

																		$now_date = date("H");

																		if($now_date >= '11'){
																			//접속아이디와 같은경우
																			if($user_id==$like_email){?>
																				<li class="heart_me">
																					<button class="btn_mains_heart_on" id="mains_heart_me_<?=$like_idx?>">
																						<img src="/html/images/pre/ico_heart_b.png" alt="">
																						<strong><?=$like_name?>님</strong>
																						<span><?=$like_comment?></span>
																						<em>좋아요 보내기</em>
																					</button>
																					<button class="btn_mains_heart_close" id="btn_mains_heart_close" value="<?=$like_idx?>"><span>닫기</span></button>
																				</li>
																			<?}else if($main_l_today_arr[$like_kind] == $like_email){?>
																				<li class="heart_me">
																					<button class="btn_mains_heart_on"	 id="mains_heart_today_<?=$like_idx?>">
																						<img src="/html/images/pre/ico_heart_b.png"	 alt="">
																						<strong><?=$like_name?>님</strong>
																						<span><?=$like_comment?></span>
																						<em>좋아요 보내기</em>
																					</button>
																					<button class="btn_mains_heart_close" id="btn_mains_heart_close" value="<?=$like_idx?>"><span>닫기</span></button>
																				</li>
																			<?}else{?>
																				<li>
																					<button class="btn_mains_heart_on"	 id="mains_heart_list_<?=$like_idx?>">
																						<img src="/html/images/pre/ico_heart_b.png"	 alt="">
																						<strong><?=$like_name?>님</strong>
																						<span><?=$like_comment?></span>
																						<em>좋아요 보내기</em>
																					</button>
																					<button class="btn_mains_heart_close" id="btn_mains_heart_close" value="<?=$like_idx?>"><span>닫기</span></button>
																				</li>
																			<?
																			}
																		}
																	}else{
																		//접속아이디와 같은경우
																		if($user_id==$like_email){?>

																			<li class="heart_me">
																				<button class="btn_mains_heart_on" id="mains_heart_me_<?=$like_idx?>">
																					<img src="/html/images/pre/ico_heart_b.png" alt="">
																					<strong><?=$like_name?>님</strong>
																					<span><?=$like_comment?></span>
																					<em>좋아요 보내기</em>
																				</button>
																				<button class="btn_mains_heart_close" id="btn_mains_heart_close" value="<?=$like_idx?>"><span>닫기</span></button>
																			</li>
																		<?}else if($main_l_today_arr[$like_kind] == $like_email){?>
																				<li class="heart_me">
																					<button class="btn_mains_heart_on"	 id="mains_heart_today_<?=$like_idx?>">
																						<img src="/html/images/pre/ico_heart_b.png"	 alt="">
																						<strong><?=$like_name?>님</strong>
																						<span><?=$like_comment?></span>
																						<em>좋아요 보내기</em>
																					</button>
																					<button class="btn_mains_heart_close" id="btn_mains_heart_close" value="<?=$like_idx?>"><span>닫기</span></button>
																				</li>
																			<?}else{?>
																				<li>
																					<button class="btn_mains_heart_on"	 id="mains_heart_list_<?=$like_idx?>">
																						<img src="/html/images/pre/ico_heart_b.png"	 alt="">
																						<strong><?=$like_name?>님</strong>
																						<span><?=$like_comment?></span>
																						<em>좋아요 보내기</em>
																					</button>
																					<button class="btn_mains_heart_close" id="btn_mains_heart_close" value="<?=$like_idx?>"><span>닫기</span></button>
																				</li>
																			<?}?>
																		<?}?>
																	<?}?>
																</ul>
															</div>
														</div>
													</div>
												</div>
											</div>
										</div>
									</div>

									<div class="rew_mains_right">
										<div class="rew_mains_right_in">
											<div class="rew_mains_live">
												<div class="rew_mains_live_tab">
													<div class="rew_live_my">
														<div class="rew_grid_onoff">
															<div class="rew_grid_onoff_inner">
																<ul>
																	<li class="onoff_01">

																		<?if($member_row_info['live_1']=='1'){?>
																			<em <?=($member_row_info['live_1']=='1')?"class='on'":""?>>근무중</em>
																		<?}else{?>
																			<em <?=($member_row_info['live_1']=='1')?"class='on'":""?>>출근</em>
																		<?}?>

																		<div class="btn_switch<?=($member_row_info['live_1']=='1')?" on":"".$switch_ready.""?>" id="live_1_bt">
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
																		<em <?=($member_row_info['live_2']=='1')?"class='on'":""?>>집중</em>
																		<div class="btn_switch<?=($member_row_info['live_2']=='1')?" on":""?>" id="live_2_bt">
																			<strong class="btn_switch_on"></strong>
																			<span>버튼</span>
																			<strong class="btn_switch_off"></strong>
																		</div>
																	</li>

																	<li class="onoff_03">
																		<em <?=($member_row_info['live_3']=='1')?"class='on'":""?>>잠시</em>
																		<div class="btn_switch<?=($member_row_info['live_3']=='1')?" on":""?>" id="live_3_bt">
																			<strong class="btn_switch_on"></strong>
																			<span>버튼</span>
																			<strong class="btn_switch_off"></strong>
																		</div>
																	</li>
																	<li class="onoff_04">
																		<em <?=($member_row_info['live_4']=='1')?"class='on'":""?>>퇴근<?=$member_row_info['live_4_time']?"(".$member_row_info['live_4_time'].")":""?></em>
																		<div class="btn_switch<?=($member_row_info['live_4']=='1')?" on":""?>" id="live_4_bt" value="<?=TODATE?>">
																			<strong class="btn_switch_on"></strong>
																			<span>버튼</span>
																			<strong class="btn_switch_off"></strong>
																		</div>
																	</li>
																</ul>
															</div>
														</div>
													</div>
													<div class="rew_live_now">
														<em><?=$updatetime?></em><button id="reload_index">새로고침</button>
													</div>
												</div>
												<div class="rew_mains_live_list">
													<div class="live_list">
														<ul class="live_list_ul" id="main_live_list">
														<?
														for($i=0; $i<count($member_list_info['idx']); $i++){
															$member_list_email = $member_list_info['email'][$i];
															$gender = $member_list_info['gender'][$i];

															$member_list_name = $member_list_info['name'][$i];
															$member_list_part = $member_list_info['part'][$i];
															$member_list_live_1_time = $member_list_info['live_1_time'][$i];

															$member_list_live_1 = $member_list_info['live_1'][$i];
															$member_list_live_2 = $member_list_info['live_2'][$i];
															$member_list_live_3 = $member_list_info['live_3'][$i];
															$member_list_live_4 = $member_list_info['live_4'][$i];
															$profile_type = $member_list_info['profile_type'][$i];
															$profile_img_idx = $member_list_info['profile_img_idx'][$i];
															$profile_img =  'https://rewardy.co.kr'.$member_list_info['file_path'][$i].$member_list_info['file_name'][$i];

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

															<li class="live_list_box<?=$live_class?>" id="live_user_list">
																<div class="live_list_t">
																	<div class="live_list_user_img">
																		<div class="live_circle circle_01"></div>
																		<div class="live_list_user_img_bg"></div>
																		<div class="live_list_user_imgs" style="background-image:url('<?=$profile_type >= '0'?$profile_img:"/html/images/pre/img_prof_default.png"?>');"></div>
																	</div>
																	<div class="live_user_state">
																		<div class="live_user_state_in">
																			<ul>


																				<?if($member_list_live_2 == "1"){?>
																					<li class="state_01">
																						<div class="live_user_state_circle">
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
																						<div class="live_user_state_circle">
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
															<?}?>
														</ul>
													</div>
												</div>
											</div>

										</div>
									</div>

								</div>
							</div>
						</div>

					</div>
				</div>
				<!-- //콘텐츠 -->

				<?php include $home_dir . "/layer/member_join.php";?>


				<?php
					//페널티 카드
					// include $home_dir . "/layer/member_penalty.php";
				?>

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




	<div class="t_layer rew_layer_join" style="display:none;">
		<div class="tl_deam"></div>
		<div class="tl_in">
			<div class="tl_close">
				<button><span>닫기</span></button>
			</div>
			<div class="tl_login_logo">
				<span>리워디</span>
			</div>
			<div class="tl_tit">
				<strong>가입하기</strong>
				<span>리워디에서 인증을요청합니다. <br />
				리워디와 함께 하세요!</span>
			</div>
			<div class="tl_list">
				<ul>
					<li>
						<div class="tc_input">
							<input type="text" id="z5" name="user_id" class="input_001" placeholder="이메일" />
							<label for="z5" class="label_001">
								<strong class="label_tit">이메일을 입력하세요</strong>
							</label>
						</div>
					</li>
				</ul>
			</div>
			<div class="tl_btn">
				<button><span>인증메일 발송</span></button>
			</div>
			<div class="tl_descript">
				<p>리워디에서 인증을 요청합니다.<br />
				아래 링크를 클릭하셔서, 비밀번호를 설정해 주세요.<br />
				링크가 클릭되지 않으시면 아래 주소를 복사하여 인터넷 브라우저에 붙여<br />
				넣어주세요.<br />
				<br />
				https://www.rewardy.co.kr/<br />
				<br />
				기타 문의사항은 1588-8443으로 문의해 주세요.<br />
				리워디와 함께 해주셔서 감사합니다.
				</p>
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
		//아이템 레이어
		include $home_dir . "/layer/item_img_buy.php";
	?>

</div>


	<div class="qna_layer_01">
		<div class="layer_deam"></div>
		<div class="qna_layer_in">
			<div class="jt_close">
				<button style="border:none;"><span>닫기</span></button>
			</div>
			<div class="qna_layer_tit">
				<p>[역량평가]에 할당된 코인</p>
				<strong><span><?=number_format($work_com_reward['cp_coin'])?></span></strong>
			</div>
			<div class="qna_circle_01">
				<span><strong></strong>남음</span>
			</div>
			<div class="qna_layer_txt">
				<p>※ 이번달에 “역량지표” 점수가 올라갈 경우 <br />획득할 수 있는 코인을 말합니다.</p>
			</div>
		</div>
	</div>

	<div class="qna_layer_02">
		<div class="layer_deam"></div>
		<div class="qna_layer_in">
			<div class="jt_close">
				<button style="border:none;"><span>닫기</span></button>
			</div>			
			<div class="qna_layer_tit">
				<p>[좋아요]에 할당된 코인</p>
				<strong><span><?=number_format($work_like_reward['like_coin'])?></span></strong>
			</div>
			<div class="qna_circle_02">
				<span><strong></strong>남음</span>
			</div>
			<div class="qna_layer_txt">
				<p>※ 이번달에 “좋아요”를 받을 경우 <br />획득할 수 있는 코인을 말합니다.</p>
			</div>
		</div>
	</div>
	<input type="hidden" id="penalty_count_out" value="<?=$outcount?>">
	<input type="hidden" id="penalty_count_work" value="<?=$workcount?>">
	<input type="hidden" id="penalty_count_chall" value="<?=$challcount?>">
	<?php
		//튜토리얼 레벨 레이어
		include $home_dir . "/layer/tutorial_main_level.php";

		//로딩 페이지
		include $home_dir . "loading.php";

		//지각 경고 패널티카드
		include $home_dir . "/layer/penalty_pop_01.php";
	?>

	<!-- footer start-->
	<? include $home_dir . "/inc_lude/footer.php";?>
	<!-- footer end-->
	<script type="text/javascript">
	$(document).ready(function(){
		window.onbeforeunload = function () { $('.rewardy_loading_01').css('display', 'block'); }
		$(window).load(function () {          //페이지가 로드 되면 로딩 화면을 없애주는 것
            $('.rewardy_loading_01').css('display', 'none');
        });
		window.onpageshow = function(event) {
 	     if ( event.persisted || (window.performance && window.performance.navigation.type == 2)) {
			  $('.rewardy_loading_01').css('display', 'none');
  		  }
		}
	});
	</script>
	<!-- Team js -->
	<script src="/team/js/team.js"></script>
	
	<!-- Step 1) Load D3.js -->
	<script src="https://d3js.org/d3.v6.min.js"></script>
	<!-- Step 2) Load billboard.js with style -->
	<script src="/js/billboard.js"></script>
	<!-- Load with base style -->
	<script type="text/javascript">
		$(document).ready(function(){
			<?//에너지?>
			var chart1 = '<?=$cp_graph1?>';

			<?//성과?>
			var chart2 = '<?=$cp_graph2?>';

			<?//성장?>
			var chart3 = '<?=$cp_graph3?>';

			<?//협업?>
			var chart4 = '<?=$cp_graph4?>';

			<?//성실?>
			var chart5 = '<?=$cp_graph5?>';

			<?//실행?>
			var chart6 = '<?=$cp_graph6?>';

			var chart = bb.generate({
				data: {
					x: "x",
					columns: [
					["x", "에너지", "성장", "성실", "실행", "협업", "성과"],
					["역량평가 리포트", chart1, chart3, chart5, chart6, chart4, chart2],
					],
					color: "#aaa",
					type: "radar",
					labels: false,
					colors: {
					"역량평가 리포트": "#38c9d2"
					}
				},
				size: {
					width: 230,
					height: 230
				},
				radar: {
					axis: {
						max: 100
					},
					level: {
						depth: 4
					},
					direction: {
						clockwise: true
					}
				},
				tooltip: {
					show: false
				},
				point: {
					show: true
				},
				transition: {
				   duration: 500
				},
				bindto: "#radarChart"
			});


			<?if($sendno){?>
				$("#rew_layer_setting").show();
			<?}else{?>

				if (GetCookie("user_id") == null) {
					$(".rew_layer_login").show();
				}else{
					<?if($sendmail_up){?>
						$("#rew_layer_setting").show();
					<?}else{?>
						<?if(!$sendno){?>
							if (GetCookie("user_id") == null) {
							//	$(".rew_layer_login").show();
							}
						<?}?>
					<?}?>
				}
			<?}?>

		});
		$(document).ready(function(){

			if($(".tl_profile ul li").hasClass("pos_ht") == true){
				var posi_cnt = $(".pos_ht").length;
				var pos = 0;
				for(i=0; i<posi_cnt; i++){
					p = i + 1;
					var id_name = $(".kp"+p).attr("id");
					pos = (5*(i+1))-1;
					$(".tl_profile ul li").eq(pos).before($("#"+id_name));
				}
			}

			var progress_cp1 = "0.<?=$cp_pro_js?>";
			var progress_cp2 = "<?=$cp_pro_js?>";

			var progress_like1 = "0.<?=$like_pro_js?>";
			var progress_like2 = "<?=$like_pro_js?>";

			//역량 할당된 코인 달성그래프
			$(".open_qna_01").click(function() {
				$(".qna_layer_01").show();
				$(".qna_circle_01").circleProgress({
					startAngle: -Math.PI / 4 * 2,
					value: 0,
					thickness: 50,
					size: 200,
					emptyFill: '#f7f8f9',
					lineCap: 'rect',
					fill: { color: '#38c9d2' },
					animation: {
						duration: 1200
					}
				}).on("circle-animation-progress", function(event, progress) {
					$(this).find("strong").html(Math.round(100 - (0 * progress)) + "<i>%</i>");
				});
				setTimeout(function() {


					$(".qna_circle_01").circleProgress('value', progress_cp1).on("circle-animation-progress", function(event, progress) {
						$(this).find("strong").html(Math.round(100 - (progress_cp2 * progress)) + "<i>%</i>");
					});
				}, (500 + (4 * 40)));
			});


			//좋아요 할당된 코인 달성그래프
			$(".open_qna_02").click(function() {
				$(".qna_layer_02").show();
				$(".qna_circle_02").circleProgress({
					startAngle: -Math.PI / 4 * 2,
					value: 0,
					thickness: 50,
					size: 200,
					emptyFill: '#f7f8f9',
					lineCap: 'rect',
					fill: { color: '#38c9d2' },
					animation: {
						duration: 1200
					}
				}).on("circle-animation-progress", function(event, progress) {
					$(this).find("strong").html(Math.round(100 - (0 * progress)) + "<i>%</i>");
				});
				setTimeout(function() {
					$(".qna_circle_02").circleProgress('value', progress_like1).on("circle-animation-progress", function(event, progress) {
						$(this).find("strong").html(Math.round(100 - (progress_like2 * progress)) + "<i>%</i>");
					});
				}, (500 + (4 * 25)));
			});

			//$(".radar_total").css("margin-top","-42px");
			$(".live_user_state li .live_user_state_circle").mouseenter(function(){
				$(".layer_state").removeClass("on");
				$(this).next(".layer_state").addClass("on");
				$(".live_list_box").removeClass("zindex");
				$(this).closest(".live_list_box").addClass("zindex");
			});
			$(".live_user_state li .live_user_state_circle").mouseleave(function(){
				$(".layer_state").removeClass("on");
				$(".live_list_box").removeClass("zindex");
			});

			$(".input_main").keyup(function(){
				var input_length = $(this).val().length; //입력한 값의 글자수
				if(input_length>0){
					$(".btn_grid_02").addClass("on");
				}else{
					$(".btn_grid_02").removeClass("on");
				}
			});

			$(".btn_grid_02").click(function(){
				if($(".btn_grid_02").hasClass("on")){
					$(".rew_grid_list_none").hide();
					var textspan = $(".input_main").val();
					var text01 = $(".rew_grid_list_in ul li.rew_grid_list_01 span").text();
					var text02 = $(".rew_grid_list_in ul li.rew_grid_list_02 span").text();
					var text03 = $(".rew_grid_list_in ul li.rew_grid_list_03 span").text();
					$(".rew_grid_list_in ul li.rew_grid_list_01 span").text(textspan);
					$(".rew_grid_list_in ul li.rew_grid_list_02 span").text(text01);
					$(".rew_grid_list_in ul li.rew_grid_list_03 span").text(text02);
				}

				if($(".rew_grid_list_in ul li.rew_grid_list_01 span").is(':empty')){

				}else{
					$(".rew_grid_list_in ul li.rew_grid_list_01").addClass("view");
				}
				if($(".rew_grid_list_in ul li.rew_grid_list_02 span").is(':empty')){

				}else{
					$(".rew_grid_list_in ul li.rew_grid_list_02").addClass("view");
				}
				if($(".rew_grid_list_in ul li.rew_grid_list_03 span").is(':empty')){

				}else{
					$(".rew_grid_list_in ul li.rew_grid_list_03").addClass("view");
				}
			});

			$(".rew_grid_list_in ul li button").click(function(){
				$(this).parent("li").toggleClass("on");
			});

			$(".rew_btn_icons_more").click(function(){
				$(".rew_icons").toggle();
			});



			$(".rew_grid_state_in .rew_grid_state_circle").mouseenter(function(){
				$(".layer_state").removeClass("on");
				$(this).next(".layer_state").addClass("on");
			});
			$(".rew_grid_state_in .rew_grid_state_circle").mouseleave(function(){
				$(".layer_state").removeClass("on");
			});

			setTimeout(function(){
				$("#bar_graph_05 strong").animate({"height":70+"%","background-color":"#f7241f"},1400,"linear");
				$("#bar_graph_04 strong").animate({"height":50+"%","background-color":"#334ff9"},1000,"linear");
				$("#bar_graph_03 strong").animate({"height":40+"%","background-color":"#334ff9"},800,"linear");
				$("#bar_graph_02 strong").animate({"height":100+"%","background-color":"#f7241f"},2000,"linear");
				$("#bar_graph_01 strong").animate({"height":60+"%","background-color":"#334ff9"},1200,"linear");
			}, 1400);
			});
			$(document).ready(function(){
				<?
				//비밀번호 재설정
				if ($passwdreset == "true") {?>
					$(".rew_layer_setting").show();
					$("#z11").focus();
				<?}?>

				<? //출근레이어
				if($user_id && (!$member_row_info["live_1_regdate"] || $member_row_info["live_1_regdate"]==null) && (!$member_row_info["live_4_regdate"] || $member_row_info["live_4_regdate"]==null) && ($member_row_info["live_1"]=='0' || $member_row_info["live_1"]==null)){?>
					// 팝업을 보이게 설정하는 함수
				  function showPopup() {
						$("#layer_work").show();
					}

					// 오늘 하루 안보기 버튼 클릭 시 팝업을 숨기고 쿠키를 설정하여 하루 동안 표시하지 않음
					$("#hidePopup").click(function() {
						$("#layer_work").hide();
						// 쿠키 이름을 "popup_hidden"으로 설정하고, 값에 현재 날짜를 추가하여 하루 동안 저장합니다.
						var now = new Date();
						now.setDate(now.getDate() + 1);
						document.cookie = "popup_hidden=true; expires=" + now.toUTCString() + "; path=/";
					});

					// 쿠키를 확인하여 오늘 하루 동안 팝업을 표시하지 않는지 확인
					function checkCookie() {
						var popupHidden = getCookie("popup_hidden");
						if (!popupHidden) {
							showPopup();
						}
					}

					// 쿠키 값을 가져오는 함수
					function getCookie(name) {
						var cookieValue = null;
						if (document.cookie && document.cookie !== "") {
							var cookies = document.cookie.split(";");
							for (var i = 0; i < cookies.length; i++) {
								var cookie = cookies[i].trim();
								if (cookie.substring(0, name.length + 1) === (name + "=")) {
									cookieValue = decodeURIComponent(cookie.substring(name.length + 1));
									break;
								}
							}
						}
						return cookieValue;
					}
					checkCookie();
				<? }?>
			});

			   
			function link_page(service,rwork) {
				//업무 타임라인 링크 이동
				if(service == 'work'){
					if(rwork == 0){
						location.href = "/todaywork/index.php";
					}else{
						var page = "/todaywork/index.php";
						var name = "wdate";
						actsubmit_post(page,name,rwork);

					}

				}// 코인 타임라인 링크 이동
				else if(service == 'reward'){
					location.href = "/reward/index.php";

				}// 챌린지 타임라인 링크 이동
				else if(service == 'challenge'){
					location.href = "/challenge/index.php";
				}
				else if(service == 'party'){
					location.href = "/party/index.php";
				}
				}
		</script>
</body>
</html>
