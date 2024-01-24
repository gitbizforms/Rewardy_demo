<?
	//header페이지
	$home_dir = str_replace( basename(__DIR__) , "" , __DIR__ );
	include $home_dir . "/inc_lude/header.php";
	//예정업무날짜  -- 나중에 지워야 할 코드
	$wdate = $_POST['wdate'];
	$wdate = str_replace("-",".",$wdate);

	if(!$wdate || $wdate == 1){
		//오늘날짜
		$wdate = str_replace("-",".",$today);
	}
	$sel_wdate = str_replace(".", "-" , $wdate);

	$month_tmp = explode(".",$wdate);
	if($month_tmp){
		$month_date = $month_tmp[0].".".$month_tmp[1];
	}


	//일일업무
	$works_info = works_info();

	//보고업무
	$works_report_info = works_report_info();

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


	//회원정보
	$member_row_info = member_row_info($user_id);

	//업무 댓글
	$works_comment_info = works_comment_info();

	for($i=0; $i<count($works_comment_info['idx']); $i++){
		$works_comment_info_idx = $works_comment_info['cidx'][$i];
		$works_comment_info_link_idx = $works_comment_info['link_idx'][$i];
		$works_comment_info_work_idx = $works_comment_info['work_idx'][$i];
		$works_comment_info_email = $works_comment_info['email'][$i];
		$works_comment_info_name = $works_comment_info['name'][$i];
		$works_comment_info_comment = $works_comment_info['comment'][$i];
		$works_comment_info_comment_strip = strip_tags($works_comment_info['comment'][$i]);
		$works_comment_info_ymd = $works_comment_info['ymd'][$i];
		$works_comment_info_regdate = $works_comment_info['regdate'][$i];
		$works_comment_info_cmt_flag = $works_comment_info['cmt_flag'][$i];

		if($works_comment_info_link_idx){
			$comment_list[$works_comment_info_link_idx]['cidx'][] = $works_comment_info_idx;
			$comment_list[$works_comment_info_link_idx]['work_idx'][] = $works_comment_info_work_idx;
			$comment_list[$works_comment_info_link_idx]['name'][] = $works_comment_info_name;
			$comment_list[$works_comment_info_link_idx]['email'][] = $works_comment_info_email;
			$comment_list[$works_comment_info_link_idx]['comment'][] = $works_comment_info_comment;
			$comment_list[$works_comment_info_link_idx]['comment_strip'][] = $works_comment_info_comment_strip;
			$comment_list[$works_comment_info_link_idx]['ymd'][] = $works_comment_info_ymd;
			$comment_list[$works_comment_info_link_idx]['regdate'][] = $works_comment_info_regdate;
			$comment_list[$works_comment_info_link_idx]['cmt_flag'][] = $works_comment_info_cmt_flag;
		}
	}

	//현재일자기준으로 한주간 체크
	$date_tmp = explode("-",$sel_wdate);
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
	}



	//예약업무 예약기능
	$decide_info = decide_info();


	//알림기능
	$notice_info = notice_info();
	for($i=0; $i<count($notice_info['idx']); $i++){
		$idx = $notice_info['idx'][$i];
		$title = $notice_info['title'][$i];
		$notice_list[$idx] = $title;
	}

	//업무보고 받는사람, 보고보낸사람 정보
	$work_report_user = work_report_user($sel_wdate);

	//업무요청 받는사람, 요청보낸사람 정보
	$work_req_user = work_req_user($sel_wdate);

	//업무공유 받는사람, 공유보낸사람 정보
	$work_share_user = work_share_user($sel_wdate);

	//첨부파일정보 불러오기
	$tdf_files = work_files_linfo($sel_wdate);

	//한줄소감
	$review_info = review_info_sel();

	//지각 페널티 카드
	$penalty_attend_info = penalty_info_check_date(0, $user_id, $sel_wdate);
	if($penalty_attend_info){
		//지각 페널티 갯수
		$penalty_attend_cnt = $penalty_attend_info['penalty_cnt'];
	}

	//오늘업무 페널티
	$penalty_work_info = penalty_info_check_date(1, $user_id, $sel_wdate);
	if($penalty_work_info){
		//오늘업무 페널티수
		$penalty_work_cnt = $penalty_work_info['penalty_cnt'];
	}

	//퇴근 페널티
	$penalty_logoff_info = penalty_info_check_date(2, $user_id, $sel_wdate);
	if($penalty_logoff_info){
		//퇴근 페널티수
		$penalty_logoff_cnt = $penalty_logoff_info['penalty_cnt'];
	}


	//지각 페널티카드 : kind=0
	$penalty_complete0 = penalty_complete(0, $user_id, $sel_wdate);
	$penalty_complete1 = penalty_complete(1, $user_id, $sel_wdate);
	$penalty_complete2 = penalty_complete(2, $user_id, $sel_wdate);


	//좋아요 리스트
	$like_flag_list = array();
	$like_info = like_info_send();
	for($i=0; $i<count($like_info['idx']); $i++){
		$like_info_idx = $like_info['idx'][$i];
		$like_info_email = $like_info['email'][$i];
		$like_info_work_idx = $like_info['work_idx'][$i];
		$like_info_like_flag = $like_info['like_flag'][$i];
		$like_info_send_email = $like_info['send_email'][$i];
		$like_flag_list[$like_info_work_idx] = $like_info_idx;
	}


	//좋아요 받은내역
	$work_like_receive = array();
	$like_info = like_info_receive();
	for($i=0; $i<count($like_info['idx']); $i++){
		$like_info_idx = $like_info['idx'][$i];
		$like_info_email = $like_info['email'][$i];
		$like_info_work_idx = $like_info['work_idx'][$i];
		$like_info_like_flag = $like_info['like_flag'][$i];
		$like_info_send_email = $like_info['send_email'][$i];
		$work_like_receive[$like_info_work_idx] = $like_info_idx;
	}



	//회원전체정보 불러오기
	$member_clist_info = member_clist_info();
	if($member_clist_info){
		$member_clist_id = @array_combine($member_clist_info['idx'], $member_clist_info['email']);
	}


	//전체 파티리스트
	$project_info = party_list();

	//파티 - 연결된 업무
	$project_data_info = project_data_info();
	$project_link_info = @array_combine($project_data_info['work_idx'], $project_data_info['party_link']);

?>


<input type="hidden" id="chall_user_cnt" value="<?=$member_total_cnt?>">
<input type="hidden" id="chall_user_chk">
<input type="hidden" id="work_wdate" value="<?=$wdate?>">
<input type="hidden" id="work_sdate">
<input type="hidden" id="work_edate">
<input type="hidden" id="work_type">
<input type="hidden" id="service">
<html>
	<head>
		<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
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

						<div class="tdw_date">
							<div class="tdw_date_in">
								<ul class="tdw_date_select">
									<li><button class="select_dd on"><span>일일</span></button></li>
									<li><button class="select_ww"><span>주간</span></button></li>
									<li><button class="select_mm"><span>월간</span></button></li>
									<li><button class="select_report"><span>리포팅</span></button></li>
								</ul>
								<div class="tdw_date_calendar">
									<button class="calendar_prev"><span>이전</span></button>
									<input type="text" id="work_date" value="<?=$wdate?>" readonly="readonly">
									<input type="text" id="work_month" value="<?=$month_date?>" data-min-view="months" data-view="months" data-date-format="yyyy.mm" readonly="readonly" style="display:none;">
									<button class="calendar_next"><span>다음</span></button>
								</div>

								<div class="tdw_search">
									<button class="btn_tdw_search" id="btn_tdw_search"><span>검색</span></button>
								</div>

							</div>
						</div>

						<div class="rew_conts_scroll_08">

							<div class="rew_todaywork">
								<div class="rew_todaywork_in">

									<?if ($penalty_attend_cnt > 1){?>
										<?if(!$penalty_info['idx']){?>
											<div class="tdw_penalty_banner">
												<div class="tdw_pb_in">
													<img src="/html/images/pre/img_penalty.png" alt="">
													<p><span>[긴급]</span>지각 페널티 카드가 발동했습니다.</p>
													<?if($penalty_complete0){?>
														<strong class="penalty_comp" id="penalty_comp_01"><span>미션 완료</span></strong><!-- 미션 완료 -->
													<?}else{?>
														<button class="btn_penalty_banner" id="btn_penalty_banner_01"><span>미션 수행하기</span></button>
														<strong class="penalty_comp" id="penalty_comp_01" style="display:none;"><span>미션 완료</span></strong><!-- 미션 완료 -->
													<?}?>
												</div>
											</div>
										<?}?>
									<?}?>


									<?if($penalty_work_cnt > 1){?>
										<div class="tdw_penalty_banner">
											<div class="tdw_pb_in">
												<img src="/html/images/pre/img_penalty.png" alt="">
												<p><span>[긴급]</span>오늘업무 페널티 카드가 발동했습니다.</p>
												<?if($penalty_complete1){?>
													<strong class="penalty_comp" id="penalty_comp_02"><span>미션 완료</span></strong><!-- 미션 완료 -->
												<?}else{?>
													<button class="btn_penalty_banner" id="btn_penalty_banner_02"><span>미션 수행하기</span></button>
													<strong class="penalty_comp" id="penalty_comp_02" style="display:none;"><span>미션 완료</span></strong><!-- 미션 완료 -->
												<?}?>
											</div>
										</div>
									<?}?>


									<?if($penalty_logoff_cnt > 1){?>
										<div class="tdw_penalty_banner">
											<div class="tdw_pb_in">
												<img src="/html/images/pre/img_penalty.png" alt="">
												<p><span>[긴급]</span>퇴근 페널티 카드가 발동했습니다.</p>
												<?if($penalty_complete2){?>
													<strong class="penalty_comp" id="penalty_comp_03"><span>미션 완료</span></strong><!-- 미션 완료 -->
												<?}else{?>
													<button class="btn_penalty_banner" id="btn_penalty_banner_03"><span>미션 수행하기</span></button>
													<strong class="penalty_comp" id="penalty_comp_03" style="display:none;"><span>미션 완료</span></strong><!-- 미션 완료 -->
												<?}?>
											</div>
										</div>
									<?}?>


									<div class="tdw_list">
										<div class="tdw_list_in">
											<div class="tdw_list_dd">
												<ul class="tdw_list_ul">

													<?if($works_info['idx']){?>
														<?for($i=0; $i<count($works_info['idx']); $i++){
															$idx = $works_info['idx'][$i];
															$state = $works_info['state'][$i];
															$work_idx = $works_info['work_idx'][$i];
															$work_email = $works_info['email'][$i];
															$work_name = $works_info['name'][$i];
															$work_flag = $works_info['work_flag'][$i];
															$work_date = $works_info['workdate'][$i];
															$work_reg = $works_info['reg'][$i];
															$work_his = $works_info['his'][$i];
															$title = $works_info['title'][$i];
															$contents = $works_info['contents'][$i];
															$repeat_work_idx = $works_info['repeat_work_idx'][$i];
															$decide_flag = $works_info['decide_flag'][$i];
															$repeat_flag = $works_info['repeat_flag'][$i];
															$notice_flag = $works_info['notice_flag'][$i];
															$memo_view = $works_info['memo_view'][$i];
															$contents_view = $works_info['contents_view'][$i];
															$work_party_link = $works_info['party_link'][$i];

															//공유업무(1:공유한업무, 공유받은업무:2)
															$share_flag = $works_info['share_flag'][$i];

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
																$memo_view_bt = " off";
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
																$report_view_bt = " off";
																$report_view_bt_style = " style=\"display: block;\"";

															}else{
																$report_view_in = "";
																$report_view_bt = " on";
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


															//공유함($share_flag=1), 공유받음($share_flag=2), 요청받은업무($work_flag=3) 아이콘 변경
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
																$li_class = " share_to";
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
																}else if($work_flag=="4"){
																		$li_class = " challenges";
																		$tdw_list = false;
																}else{
																	//알림글(work_flag = 2)
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
															<li class="tdw_list_li<?=$li_class?>" id="workslist_<?=$idx?>" value="<?=($i+1)?>">
																<div class="tdw_list_box<?=($state=='1')?" on":""?><?=$share_view_bt_style?>" id="tdw_list_box_<?=$idx?>" name="onoff_<?=$i?>">
																	<div class="tdw_list_chk">
																		<?if($work_flag=='1'){?>
																			<button class="btn_tdw_list_chk" value="<?=$idx?>" id="tdw_dlist_chk"><span>완료체크</span></button>
																		<?}else{?>
																			<button class="btn_tdw_list_chk" value="<?=$idx?>" id="tdw_dlist_chk"><span>완료체크</span></button>
																		<?}?>
																	</div>
																	<div class="tdw_list_desc">
																		<?
																		$work_title = "";
																		//나의업무 : work_flag=2
																		if($work_flag == "2"){?>

																			<?//알림글 : notice_flag=1
																			if($notice_flag){?>
																				<p id="notice_link" value="<?=$work_idx?>">
																					<?if($notice_list[$decide_flag]){?>
																						<span>[<?=$notice_list[$decide_flag]?>]</span>
																					<?}?>
																					<?=$contents?>
																				</p>
																			<?}else{?>

																				<?//공유함 : share_flag=1
																				if($share_flag == '1'){

																					//업무공유보냄 1명 이상인 경우
																					if($work_share_user['send_cnt'][$idx] > 1){
																						$work_user_count = $work_share_user['send_cnt'][$idx] - 1;
																						$work_share_title = $work_share_user['send'][$idx][0]. "님 외 ". $work_user_count . "명에게 공유함";
																						$work_title = "[".$work_share_title. "]";

																					}else{
																						$work_share_title = $work_share_user['send'][$idx][0];
																						$work_title = "[". $work_share_title. "님에게 공유함]";
																					}

																						$work_share_read_all = $work_share_user['read'][$idx]['all'];
																						$work_share_read_cnt = $work_share_user['read'][$idx]['read'];
																						$work_share_read_reading = $work_share_read_cnt;

																						//읽지않은사용자
																						if($work_share_read_reading>0){
																							$read_share_text = "&nbsp;&nbsp;<em class=\"tdw_read\">읽음 ".$work_share_read_reading."</em>";
																						}else{
																							$read_share_text = "&nbsp;&nbsp;<em class=\"tdw_read\">읽음 0</em>";
																						}
																						$edit_id = " id='tdw_list_edit_".$idx."'";

																					?>

																					<p <?=$edit_id?>><?=$work_title?"<span>".$work_title."</span>":""?><?=textarea_replace($contents)?><?=$read_share_text?></p>

																				<?//공유받음 : share_flag=2
																				}else if($share_flag == '2'){

																					$work_to_name = $work_share_user['receive'][$work_idx];
																					$work_title = "[".$work_to_name ."님에게 공유받음]";
																					$edit_id = "";

																					?>
																					<p <?=$edit_id?>><?=$work_title?"<span>".$work_title."</span>":""?><?=textarea_replace($contents)?></p>
																				<?}else{?>
																						<p id="tdw_list_edit_<?=$idx?>"><?=textarea_replace($contents)?></p>
																				<?}?>
																			<?}
																				//내용
																				$edit_content = $contents;

																		//요청업무
																		}else if($work_flag == "3"){?>

																			<?//요청받은업무 : work_flag=3
																			if($work_idx){

																				$work_to_name = $work_req_user['receive'][$work_idx];
																				$work_title = "[".$work_to_name ."님에게 요청받음]";
																				$read_all_cnt = $work_req_user['receive_cnt'][$work_idx];

																			}else{

																				//업무요청 1명 이상인 경우
																				if($work_req_user['send_cnt'][$idx] > 1){
																					$work_user_count = $work_req_user['send_cnt'][$idx] - 1;
																					$work_user_req = $work_req_user['send'][$idx][0]. "님 외 ". $work_user_count . "명에게 요청함";
																					$work_title = "[". $work_user_req. "]";
																				}else{
																					$work_user_req = $work_req_user['send'][$idx][0];
																					$work_title = "[". $work_user_req. "님에게 요청함]";
																				}

																				$work_req_read_all = $work_req_user['read'][$idx]['all'];
																				$work_req_read_cnt = $work_req_user['read'][$idx]['read'];
																				$work_req_read_reading = $work_req_read_cnt;

																				if($work_req_read_reading>0){
																					$read_req_text = "&nbsp;&nbsp;<em class=\"tdw_read\">읽음 ".$work_req_read_reading."</em>";
																				}else{
																					$read_req_text = "&nbsp;&nbsp;<em class=\"tdw_read\">읽음 0";
																				}
																			}


																			//업무수정
																			if($work_idx == null && $user_id == $work_email){?>
																				<p id="tdw_list_edit_<?=$idx?>"><span><?=$work_title?></span><?=textarea_replace($contents)?><?=$read_req_text?></p>
																			<?}else{?>
																				<p><span><?=$work_title?></span><?=textarea_replace($contents)?><?=$read_req_text?></p>
																			<?}

																			//내용
																			$edit_content = $contents;

																		//보고업무
																		}else if($work_flag == "1"){?>

																			<?
																			//공유먼저 체크
																			//공유 보냄
																			if($share_flag == '1'){


																			//공유받음
																			}else if($share_flag == '2'){

																				$work_to_name = $work_share_user['receive'][$work_idx];
																				$work_title = "[".$work_to_name ."님에게 공유받음]";
																				$edit_id = "";

																			}else{


																				//보고받은 업무 : work_flag=1
																				if($work_idx){

																					$report_email = $work_report_list[$work_idx]['email'];
																					$report_name =$work_report_list[$work_idx]['name'];
																					$report_title =$work_report_list[$work_idx]['title'];
																					$report_contents =$work_report_list[$work_idx]['contents'];

																					$work_to_name = $work_report_user['receive'][$work_idx];
																					$work_title = "[".$work_to_name ."님에게 보고받음]";

																				}else{

																					$report_email = $work_report_list[$idx]['email'];
																					$report_name =$work_report_list[$idx]['name'];
																					$report_title =$work_report_list[$idx]['title'];
																					$report_contents =$work_report_list[$idx]['contents'];

																					//보고 1명 이상인 경우
																					if($work_report_user['send_cnt'][$idx]  > 1){
																						$work_user_count = $work_report_user['send_cnt'][$idx] - 1;
																						$work_user_report = $work_report_user['send'][$idx][0]. "님 외 ". $work_user_count . "명에게 보고함";
																						$work_title = "[". $work_user_report. "]";
																					}else{
																						$work_user_report = $work_report_user['send'][$idx][0];
																						$work_title = "[". $work_user_report. "님에게 보고함]";
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
																			}


																			//업무수정
																			if($work_idx == null && $user_id == $work_email){?>
																				<p id="tdw_list_edit_<?=$idx?>"><span><?=$work_title?></span><?=$title?><?=$read_report_text?></p>
																			<?}else{?>
																				<p><span><?=$work_title?></span><?=$title?><?=$read_report_text?></p>
																			<?}

																			//보고제목
																			$edit_content = $report_title;
																		}else if($work_flag == '4'){?>
																			<p id="party_link" value="<?=$work_idx?>"><?=$contents?></p>
																		<?}else{?>
																		<?}?>

																		<div class="tdw_list_regi" id="tdw_list_regi_edit_<?=$idx?>">
																			<strong>수정중</strong>
																			<textarea name="" class="textarea_regi" id="textarea_regi_<?=$idx?>"><?=strip_tags($edit_content)?></textarea>
																			<div class="btn_regi_box">
																				<button class="btn_regi_submit" id="btn_regi_submit" value="<?=$idx?>"><span>확인</span></button>
																				<button class="btn_regi_cancel"><span>취소</span></button>
																			</div>
																		</div>
																	</div>


																	<div class="tdw_list_function">
																		<div class="tdw_list_function_in">

																			<?//나의업무 : work_flag=2
																			//알림글 : notice_flag=1
																			//공유함 : share_flag=1
																			//공유받음 : share_flag=2
																			//요청받은업무 : work_flag=3
																			//요청한업무 : work_flag=3
																			//보고한업무
																			//보고받은?>

																			<?
																			//받은업무
																			//보고, 공유
																			if($work_flag=="1" && $work_idx || $share_flag=='2' && $work_idx){?>
																				<button class="tdw_list_100c" title="100코인" id="tdw_list_100c" value="<?=$idx?>"><span>100</span></button>
																			<?}?>
																			
																			<?if(($notice_flag=='0' || $decide_flag=='0') && $share_flag!=='2' && $notice_flag!='1' && $work_flag!='4'){?>
																				<button class="tdw_list_party_link<?=$project_link_info[$idx]?" on":""?>" id="tdw_list_party_link" value="<?=$idx?>" title="파티연결"><span>파티연결</span></button>
																			<?}?>

																			<?
																			//보고받은 업무
																			if($work_flag=="1" && $work_idx){?>
																				<button class="tdw_list_reported_hart<?=$work_like_list[$work_idx]>0?" on":""?>" title="좋아요" <?=$work_like_list[$work_idx]>0?"":" id=\"tdw_list_jjim\""?> value="<?=$work_idx?>"><span>좋아요</span></button>
																			<?
																			//공유받음
																			}else if($share_flag=='2' && $work_idx){?>
																				<button class="tdw_list_shared_hart<?=$work_like_list[$work_idx]>0?" on":""?>" title="좋아요" <?=$work_like_list[$work_idx]>0?"":" id=\"tdw_list_jjim\""?> value="<?=$work_idx?>"><span>좋아요</span></button>
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


																			<?//공유한 업무는 취소 가능, 공유한업무(share_flag=1), 공유받은업무(share_flag=2)?>

																			<?//공유하기?>
																			<?//공유한 업무?>
																			<?if($share_flag=='1' && $work_idx){?>
																				<button class="tdw_list_share_cancel" id="tdw_list_share_cancel" value="<?=$idx?>" title="공유취소"><span>공유취소</span></button>
																			<?}else{?>
																				<?//나의업무작성, 공유업무작성?>
																				<?if(($work_flag=='2' && $work_idx==null) || ($share_flag=='1' && $work_idx==null)){?>
																					<button class="tdw_list_share" id="tdw_list_share" value="<?=$idx?>" title="공유"><span>공유</span></button>
																				<?}?>
																			<?}?>


																			<?//파일첨부?>
																			<?//파일첨부(나의업무, 공유업무작성, 보고업무작성, 요청업무작성)?>
																			<?if(($work_flag=='2' && $work_idx==null) || ($share_flag=='1' && $work_idx) || ($work_flag=='1' && $work_idx==null) || ($work_flag=='3' && $work_idx==null)){?>
																				<button class="tdw_list_files" id="tdw_file_add_<?=$idx?>" title="파일추가"><span>파일추가</span></button>
																				<input type="file" id="files_add_<?=$idx?>" style="display:none;">
																			<?}?>


																			<?//사람선택?>
																			<?//공유업무작성, 보고업무작성, 요청업무작성?>
																			<?if(($share_flag=='1' && $work_idx) || ($work_flag=='1' &&  $work_idx==null)){?>
																				<button class="tdw_list_user" id="tdw_send_user_<?=$idx?>" value="<?=$idx?>" title="받을사람"><span>받을사람</span></button>
																			<?}?>

																			<?//사람선택?>
																			<?//요청업무작성?>

																				<?if($work_flag=='3' && $work_idx==null){?>
																					<button class="tdw_list_user" id="tdw_send_user_<?=$idx?>" value="<?=$idx?>" title="받을사람"><span>받을사람</span></button>
																				<?}?>

																			<?//메모작성?>
																			<? if($notice_flag!='1' && $work_flag!='4'){?>
																			<button class="tdw_list_memo" id="tdw_list_memo" value="<?=$idx?>" title="메모"><span>메모</span></button>
																			<?}?>

																			<?//나의업무, 요청업무 작성자만 반복설정?>
																			<?if(($work_flag=='2' && $work_idx==null) || ($work_flag=='3' && $work_idx==null)){?>
																				<div class="tdw_list_repeat_box<?=$repeat_flag?" on":""?>" title="반복설정">
																					<button class="tdw_list_repeat" id="tdw_list_repeat" value="<?php echo $idx?>"><span><?=$repeat_text?></span></button>
																					<div class="tdw_list_repeat_list">
																						<div><button id="repeat1" value="<?=$idx?>" >매일반복</button></div>
																						<div><button id="repeat2" value="<?=$idx?>">매주반복</button></div>
																						<div><button id="repeat3" value="<?=$idx?>">매월반복</button></div>
																						<div><button id="repeat4" value="<?=$idx?>">반복안함</button></div>
																					</div>
																				</div>
																			<?}?>


																			<?//일정변경?>
																			<?//나의업무, 공유업무작성, 보고업무작성, 요청업무작성?>
																			<?if(($work_flag=='2' && $work_idx==null) || ($share_flag=='1' && $work_idx==null) || ($work_flag=='1' && $work_idx==null) || ($work_flag=='3' && $work_idx==null)){?>
																				<input class="tdw_list_date" type="image" title="일정 변경" id="listdate_<?=$idx?>">
																			<?}?>


																			<?//삭제?>
																			<?//알림글삭제?>
																			<?if($work_flag!='4'){
																				if($notice_flag){?>
																					<?if($user_id == $work_email){?>
																						<button class="tdw_list_del" title="삭제" id="notice_list_del" value="<?=$idx?>"><span>삭제</span></button>
																					<?}else{?>
																						<button class="tdw_list_del" title="삭제" value="<?=$idx?>"><span>삭제</span></button>
																					<?}?>
																				<?}else{?>
																				<?//업무글삭제?>
																					<?if($user_id == $work_email && $share_flag == 0 && $work_flag == 2){?>
																						<button class="tdw_list_del" title="삭제" id="tdw_list_per_del" value="<?=$idx?>"><span>삭제</span></button>
																					<?}else if($user_id == $work_email){?>
																						<button class="tdw_list_del" title="삭제" id="tdw_list_del" value="<?=$idx?>"><span>삭제</span></button>
																					<?}else{?>
																						<button class="tdw_list_del" title="삭제" value="<?=$idx?>"><span>삭제</span></button>
																					<?}?>
																				<?}
																				}
																			?>
																			<?//업무이동 드래그앤드랍?>
																			<div class="tdw_list_drag" title="순서 변경" value="<?=$idx?>"><span>드래그 드랍 기능</span></div>
																			<?/*2022-01-12 주석처리 <button class="tdw_list_tomorrow" title="내일로 미루기" value="<?=$idx?>"><span>내일로 미루기</span></button>*/?>
																		</div>
																	</div>


																	

																	<?//첨부파일 정보
																	//나의업무, 요청업무
																	if(in_array($work_flag, array('2','3'))){
																		if($tdf_files[$work_com_idx]['file_path']){?>
																			<div class="tdw_list_file">
																				<?for($k=0; $k<count($tdf_files[$work_com_idx]['file_path']); $k++){?>
																					<div class="tdw_list_file_box">
																						<button class="btn_list_file" id="btn_list_file_<?=$k?>" value="<?=$tdf_files[$work_com_idx]['idx'][$k]?>"><span><?=$tdf_files[$work_com_idx]['file_real_name'][$k]?></span></button>

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
																if($work_flag=='1'){?>

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
																							<button class="btn_regi_cancel" id="btn_report_cancel"><span>취소</span></button>
																						</div>
																					</div>
																				</div>
																			</div>

																			<?//첨부파일 정보
																			if($tdf_files[$work_com_idx]['file_path']){?>
																				<div class="tdw_list_file">
																					<?for($k=0; $k<count($tdf_files[$work_com_idx]['file_path']); $k++){?>
																						<div class="tdw_list_file_box">
																							<button class="btn_list_file" id="btn_list_file_<?=$k?>" value="<?=$tdf_files[$work_com_idx]['idx'][$k]?>"><span><?=$tdf_files[$work_com_idx]['file_real_name'][$k]?></span></button>

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
																	<div class="tdw_list_req_onoff"<?=$share_view_bt_style?>>
																		<button class="btn_list_req_onoff <?=($contents_view=="1"? " off": "")?>" id="btn_list_req_onoff_<?=$idx?>" value="<?=$idx?>"><span>업무 접기/펼치기</span></button>
																	</div>
																<?}?>

																<?if($share_flag && $work_idx){?>
																	<div class="tdw_list_share_onoff"<?=$share_view_bt_style?>>
																		<button class="btn_list_share_onoff<?=($comment_list[$work_com_idx]?" memo_on": "");?><?=$share_view_bt?>" id="btn_list_share_onoff_<?=$idx?>" value="<?=$idx?>" <?if(trim($share_view_bt)=="on"){ echo "title='공유 접기'"; }else{ echo "title='공유 펼치기'"; }?>><span>공유 접기/펼치기</span></button>
																	</div> 
																<?}?>


																<div class="tdw_list_memo_area"> <!--작업-->
																	<?/*<div class="tdw_list_memo_area_in<?=$memo_view_in?>" id="memo_area_list_<?=$idx?>">*/?>
																	<div class="tdw_list_memo_area_in<?=$memo_view_in?>" id="tdw_list_memo_area_in_<?=$idx?>">
																		<?//댓글리스트
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

																							$sql = "select idx, email, reward_user, reward_name, coin,memo,coin,DATE_FORMAT(regdate, '%Y-%m-%d %H:%i:%s') as regdate from work_coininfo";
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
																					//메모관련 코인 지급 받은 내역 리스트
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
																								<?if($cmt_flag !=2){?>
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
																								</em>

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
																					//코인보상 표기(보고함)
																					$sql = "select idx from work_todaywork where work_idx = '".$idx."'";

																					$coin_work = selectAllQuery($sql);

																					if(!$coin_work){
																						$sql = "select idx from work_todaywork where idx = '".$idx."'";
																						$coin_work = selectAllQuery($sql);
																					}

																					if($coin_work){
																						for($co_i=0; $co_i<count($coin_work['idx']); $co_i++){
																							$coin_work_idx = $coin_work['idx'][$co_i];

																							$sql = "select idx, reward_user, reward_name, coin,memo,coin,date_format(regdate , '%m/%d/%y %l:%i:%s %p') regdate from work_coininfo";
																							$sql = $sql." where state != 9 and code = 700";
																							$sql = $sql." and coin_work_idx='".$coin_work_idx."' order by regdate desc";

																							$coin_info_comment = selectAllQuery($sql);

																							if($coin_info_comment){
																								for($co_j=0; $co_j<count($coin_info_comment['idx']); $co_j++){
																									$coin_info_r_idx = $coin_info_comment['idx'][$co_j];
																									$coin_info_r_email = $coin_info_comment['reward_user'][$co_j];
																									$coin_info_r_name = $coin_info_comment['reward_name'][$co_j];
																									$coin_info_r_coin = $coin_info_comment['coin'][$co_j];
																									$coin_info_r_memo = $coin_info_comment['memo'][$co_j];
																									$coin_info_r_regdate = $coin_info_comment['regdate'][$co_j];

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
																										<div  class="tdw_list_memo_conts">
																											<span class="tdw_list_memo_conts_txt"><?=$coin_info_r_coin?> <?=$coin_info_r_memo?></span>
																											<em class="tdw_list_memo_conts_date"><?=$coin_info_r_time?></em>
																										</div>
																									</div>
																								<?
																								}
																							}
																						}
																					}
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
																									<span  class="tdw_list_memo_conts_txt"><?=textarea_replace($comment_list[$idx]['comment'][$k])?></span>
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

																	<div class="tdw_list_memo_onoff" <?=$memo_view_bt_style?>>
																		<button class="btn_list_memo_onoff<?=$memo_view_bt?>" id="btn_list_memo_onoff_<?=$idx?>" value="<?=$idx?>" <?if(trim($memo_view_bt)=="on"){ echo "title='메모 접기'"; }else{ echo "title='메모 펼치기'"; }?>><span>메모 접기/펼치기</span></button>
																	</div>

																</div>
															</li>
														<?}?>


													<?}?>
												</ul>

												<?
												//한줄소감
												//if($review_info['idx']){?>
													<div class="tdw_feeling_banner<?=$review_info['idx']?" btn_ff_0".$review_info['work_idx']."":""?>" id="tdw_feeling_banner_<?=$sel_wdate?>">
														<div class="tdw_fb_in">
															<strong></strong>
															<p id="feeling_banner_<?=$sel_wdate?>"><?=$review_info['idx']?"".$review_info['comment']."":"오늘 하루는 어떤가요?"?></p>
															<button class="btn_feeling_banner" id="btn_feeling_banner_<?=$sel_wdate?>" value="<?=$sel_wdate?>"><span>오늘 한 줄 소감</span></button>
														</div>
													</div>
												<?//}?>

											</div>

											<div class="tdw_list_ww" style="display:none"></div>
											<div class="tdw_list_mm" style="display:none"></div>
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
						<button class="<?=$review_info['idx']?"btn_on":"btn_off"?>" id="ff_bottom_next">다음</button>
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
						<button>등록하기</button>
					</div>
				</div>
			</div>
		</div>
	</div>



	<div class="layer_memo" style="display:none;">
		<input type="hidden" id="work_idx">
		<div class="layer_deam"></div>
		<div class="layer_memo_in">
			<div class="layer_memo_box">
				<textarea name="textarea_memo" class="textarea_memo" placeholder="메모를 작성해주세요." id="textarea_memo"></textarea>
			</div>
			<div class="layer_memo_btn">
				<button class="layer_memo_cancel" id="layer_memo_cancel"><span>취소</span></button>
				<button class="layer_memo_submit" id="layer_memo_submit"><span>등록하기</span></button>
			</div>
		</div>
	</div>

	<?php
		// 로딩 액션
		include $home_dir . "loading.php";
	?>

	<?php
		//사용자 레이어(오늘업무(보고,공유,요청),챌린지-참여자설정,파티구성원)
		include $home_dir . "/layer/member_user_layer.php";
	?>

	<?php
		//검색 레이어
		include $home_dir . "/layer/todaywork_search.php";
	?>
    <?php
		//반복설정 레이어
		include $home_dir . "/layer/replay_popup.php";
		
	?>
	<?php
		//좋아요 레이어
		include $home_dir . "/layer/member_jjim.php";
	?>

	<?php
		//코인보상하기 레이어
		include $home_dir . "/layer/member_reward.php";
	?>

	<?php
		//페널티 카드
		include $home_dir . "/layer/member_penalty.php";
	?>

	<?php
		//파티연결 레이어
		include $home_dir . "/layer/todaywork_party_link.php";
	?>
	
	<?php
		//튜토리얼 레벨 레이어
		include $home_dir . "/layer/tutorial_main_level.php";
	?>

	

	<div class="rew_popup" id="rew_popup">
		<div class="rew_popup_in" id="rew_popup_in">오늘업무를 작성하여 실행 1점이 올라갔습니다.</div>
	</div>

	<div class="rew_q">
		<a href="01.html" target="_blank">(구)버전</a>
		<a href="002.html" target="_blank">(신)버전</a>
		<a href="0001.html" target="_blank">(리뉴얼)버전</a>
	</div>

</div>

	<?//알림
		include $home_dir . "/inc_lude/work_notice.php";
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

			length_work = $("div[id^=tdw_list_box_]").length;

			for(var i = 0; i<length_work; i++){
				btn_work = $("div[name=onoff_"+i+"]").parent().find("button[id^=btn_list_work_onoff]");
				btn_req = $("div[name=onoff_"+i+"]").parent().find("button[id^=btn_list_req_onoff]");
        		btn_share = $("div[name=onoff_"+i+"]").parent().find("button[id^=btn_list_share_onoff]");
				if($("div[name=onoff_"+i+"]").height() < "40"){
					btn_work.css("display","none");
          			btn_share.css("display","none");
					btn_req.css("display","none");
					// console.log($("div[name=onoff_"+i+"]").height());
				}else if($("div[name=onoff_"+i+"]").height() >= "40"){
					// console.log($("div[name=onoff_"+i+"]").height());
				}
			}
		});	
		window.onpageshow = function(event) {
 	     if ( event.persisted || (window.performance && window.performance.navigation.type == 2)) {
			  $('.rewardy_loading_01').css('display', 'none');
  		  }
		}

		var member_clist_id = new Array();
		<?
			foreach($member_clist_id as $key => $val){
			?>
				member_clist_id["<?=$key?>"] = "<?=$val?>";
			<?
			}
		?>

		<?if($member_total_cnt > 0){?>
			var member_total_cnt = '<?=number_format($member_total_cnt)?>';
		<?}?>


		<?if(ATTEND_STIME){?>
			var late_stime = "<?=ATTEND_STIME?>";
		<?}?>

		<?if(ATTEND_ETIME){?>
			var late_etime = "<?=ATTEND_ETIME?>";
		<?}?>

		var month_first_day = '<?=$month_first_day?>';
		var month_last_day = '<?=$month_last_day?>';

		</script>
		<script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
		<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
</body>
</html>

