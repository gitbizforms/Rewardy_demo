<?php

$home_dir = str_replace( basename(__DIR__) , "" , __DIR__ );

include $home_dir . "inc_lude/conf_mysqli.php";
include $home_dir . "inc/SHA256/KISA_SHA256.php";
include DBCON_MYSQLI;
include FUNC_MYSQLI;
date_default_timezone_set('Asia/Seoul');
$now_time = date("H:i");
$now_time_obj = DateTime::createFromFormat('H:i', $now_time);

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


//챌린지 참여, 인증메시지 리스트
if($mode == "todaywork_list"){

	//$idx = $_POST['idx'];
	//$idx = preg_replace("/[^0-9]/", "", $idx);
	$user_email = $_POST['user_email'];
	$lives_date = $_POST['lives_date'];
	$type = $_POST['type'];

	//날짜 변경
	if($lives_date){
		if(strpos($lives_date, ".") !== false){
			$lives_date_ch = $lives_date;
			$lives_date = str_replace(".", "-",$lives_date);
		}
	}

	//이전 일자
	if($type =='prev'){
		$lives_date = date("Y-m-d", strtotime($lives_date." -1 day"));
		$echo_date = str_replace("-", ".",$lives_date);
	//다음 일자
	}else if($type =='next'){
		$lives_date = date("Y-m-d", strtotime($lives_date." +1 day"));
		$echo_date = str_replace("-", ".",$lives_date);
	}

	if($lives_date){
		$echo_date = str_replace("-", ".",$lives_date);
	}

	//회원 이름, 부서명


	$sql = "select idx, name, part from work_member where state='0'";

	//관리권한은 제외처리
	if($user_level == 1){
		$sql = $sql .= " and email!='".$user_id."'";
	}else{
		$sql = $sql .= " and companyno='".$companyno."' and email='".$user_email."'";
	}

	$member_info = selectQuery($sql);
	if($member_info['idx']){
		$member_info_name = $member_info['name'];
		$member_info_part = $member_info['part'];
	}

	//일일업무
	$sql = "(select idx, state, work_flag, decide_flag, secret_flag, email, name, work_idx, repeat_work_idx, repeat_flag, notice_flag, share_flag, memo_view, contents_view, title, contents, workdate, work_stime, work_etime, ";
	$sql = $sql .= "date_format( regdate , '%m/%d/%y %l:%i:%s %p') as reg, date_format(regdate, '%H:%i') as his, party_link, sort ";
	$sql = $sql .= "from work_todaywork where state !='9' and companyno='".$companyno."' and email='".$user_email."' and workdate = '".$lives_date."' and notice_flag = 0 and (share_flag != '2' OR (share_flag = '1' AND work_idx IS NOT NULL)) and (work_idx IS NULL OR share_flag = '1') ";
	$sql = $sql .= ")union( ";
	$sql = $sql .= "select a.idx, a.state, a.work_flag, a.decide_flag, a.secret_flag, a.email, a.name, a.work_idx, a.repeat_work_idx, a.repeat_flag, a.notice_flag, a.share_flag, a.memo_view, a.contents_view, a.title, a.contents, a.workdate, a.work_stime, a.work_etime,";
	$sql = $sql .= "date_format( a.regdate , '%m/%d/%y %l:%i:%s %p') as reg, date_format(a.regdate, '%H:%i') as his, party_link, sort ";
	$sql = $sql .= "from work_todaywork a join work_challenges b on (a.work_idx = b.idx) left join (select challenges_idx,email, state from work_challenges_result where email = '".$user_email."') as c on (a.work_idx = c.challenges_idx) where a.notice_flag = 1 ";
	$sql = $sql .= "and b.sdate <= '".$lives_date."' and b.edate >= '".$lives_date."' and b.companyno = '".$companyno."' and a.email = '".$user_email."' and a.state = 0 and ((c.email = '".$user_email."' and c.state != 1) or c.email is null)) order by sort asc, idx desc";
	$works_info = selectAllQuery($sql);


	//보고업무
	$sql = "select idx, state, work_flag, decide_flag, email, name, work_idx, repeat_flag, notice_flag, share_flag, title, contents, workdate, date_format(regdate , '%m/%d/%y %l:%i:%s %p') as reg, date_format(regdate, '%H:%i') as his from work_todaywork where state !='9'";
	$sql = $sql .=" and companyno='".$companyno."' and work_flag='1' and work_idx is null and workdate = '".$lives_date."'";
	$sql = $sql .= " order by sort asc, idx desc";
	$works_report_info = selectAllQuery($sql);

	for($i=0; $i<count($works_report_info['idx']); $i++){
		$work_report_idx = $works_report_info['idx'][$i];
		$work_report_title = $works_report_info['title'][$i];
		$work_report_contents = $works_report_info['contents'][$i];
		$work_report_email = $works_report_info['email'][$i];
		$work_report_name = $works_report_info['name'][$i];

		//$work_report_contents = str_replace("<br />","<br>", $work_report_contents);

		$work_report_list[$work_report_idx]['title'] = $work_report_title;
		$work_report_list[$work_report_idx]['contents'] = $work_report_contents;
		$work_report_list[$work_report_idx]['email'] = $work_report_email;
		$work_report_list[$work_report_idx]['name'] = $work_report_name;
	}


	//회원정보
	$member_row_info = member_row_info($user_id);

	//$sql = "select idx, link_idx, work_idx, email, name, comment, regdate from work_todaywork_comment where state='0' order by idx desc";
	$sql = "select a.idx as cidx, a.link_idx, a.work_idx, a.email, a.name, a.comment, a.cmt_flag, a.secret_flag, CASE WHEN a.editdate is not null then date_format(a.editdate , '%Y-%m-%d') WHEN a.editdate is null then date_format(a.regdate , '%Y-%m-%d') end as ymd,";
	$sql = $sql .= " CASE WHEN a.editdate is not null then date_format(a.editdate , '%m/%d/%y %l:%i:%s %p') WHEN a.editdate is null then date_format(a.regdate , '%m/%d/%y %l:%i:%s %p') end as regdate";
	$sql = $sql .= " ,b.idx from work_todaywork_comment as a left join work_todaywork as b on(a.link_idx=b.idx) where a.state=0 and a.companyno='".$companyno."' and b.workdate='".$lives_date."' order by a.regdate desc";
	$works_comment_info = selectAllQuery($sql);

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
			$works_comment_info_secret_flag = $works_comment_info['secret_flag'][$i];
			$works_comment_info_secret_send = $works_comment_info['send'][$i];

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
			$comment_list[$works_comment_info_link_idx]['secret_flag'][] = $works_comment_info_secret_flag;
			$comment_list[$works_comment_info_link_idx]['send'][] = $works_comment_info_secret_send;
		}
	}

	//예약업무 예약기능
	$sql = "select idx, title, type_flag from work_decide where state='0' order by sort asc";
	$decide_info = selectAllQuery($sql);


	//알림기능
	$sql = "select idx, title from work_notice where state='0' order by sort asc";
	$notice_info = selectAllQuery($sql);
	for($i=0; $i<count($notice_info['idx']); $i++){
		$idx = $notice_info['idx'][$i];
		$title = $notice_info['title'][$i];
		$notice_list[$idx] = $title;
	}

	//업무보고 받는사람, 보고보낸사람 정보
	$work_report_user = work_report_user($lives_date);

	//업무요청 받는사람, 요청보낸사람 정보
	$work_req_user = work_req_user($lives_date);

	//업무공유 받는사람, 공유보낸사람 정보
	$work_share_user = work_share_user($lives_date);

	//첨부파일정보 불러오기
	$tdf_files = work_files_linfo($lives_date);

	//한줄소감
	$sql = "select idx, work_idx, comment from work_todaywork_review where state='0' and email='".$user_email."' and workdate='".$lives_date."'";
	$review_info = selectQuery($sql);

	//지각 페널티 카드
	// $penalty_attend_info = penalty_info_check_date(0, $user_email, $lives_date);
	// if($penalty_attend_info){
	// 	//지각 페널티 갯수
	// 	$penalty_attend_cnt = $penalty_attend_info['penalty_cnt'];
	// }

	// //오늘업무 페널티
	// $penalty_work_info = penalty_info_check_date(1, $user_email, $lives_date);
	// if($penalty_work_info){
	// 	//오늘업무 페널티수
	// 	$penalty_work_cnt = $penalty_work_info['penalty_cnt'];
	// }

	// //퇴근 페널티
	// $penalty_logoff_info = penalty_info_check_date(2, $user_email, $lives_date);
	// if($penalty_logoff_info){
	// 	//퇴근 페널티수
	// 	$penalty_logoff_cnt = $penalty_logoff_info['penalty_cnt'];
	// }

	//지각 페널티카드 : kind=0
	// $penalty_complete0 = penalty_complete(0, $user_email, $lives_date);
	// $penalty_complete1 = penalty_complete(1, $user_email, $lives_date);
	// $penalty_complete2 = penalty_complete(2, $user_email, $lives_date);


	//좋아요 리스트
	$like_flag_list = array();
	 $sql = "select idx, email,service, work_idx, send_email, like_flag from work_todaywork_like where state='0' and send_email='".$user_email."' and workdate='".$lives_date."'";
	$like_info = selectAllQuery($sql);
	for($i=0; $i<count($like_info['idx']); $i++){
		$like_info_idx = $like_info['idx'][$i];
		$like_info_email = $like_info['email'][$i];
		$like_info_work_idx = $like_info['work_idx'][$i];
		$like_info_like_flag = $like_info['like_flag'][$i];
		$like_info_send_email = $like_info['send_email'][$i];
		$work_like_list[$like_info_work_idx] = $like_info_idx;
	}

	
	//좋아요 받은내역
	$work_like_receive = array();
	 $sql = "select idx, email,service, work_idx, send_email, like_flag from work_todaywork_like where state='0' and email='".$user_email."' and workdate='".$lives_date."'";
	$like_info = selectAllQuery($sql);
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


	//전체 프로젝트 내역
	$sql = "select b.idx, b.project_idx, b.email, b.name, b.part from work_todaywork_project as a left join work_todaywork_project_user as b on(a.idx=b.project_idx)";
	$sql = $sql .= " where a.state='0' and a.companyno='".$companyno."' order by a.idx asc";
	//echo $sql;
	$project_user_info = selectAllQuery($sql);
	for($i=0; $i<count($project_user_info['idx']); $i++){
		$project_user_idx = $project_user_info['project_idx'][$i];
		$project_user_email = $project_user_info['email'][$i];
		$project_user_name = $project_user_info['name'][$i];
		$project_user_part = $project_user_info['part'][$i];
		$project_user_list[$project_user_idx]['email'][] = $project_user_email;
		$project_user_list[$project_user_idx]['name'][] = $project_user_name;
		$project_user_list[$project_user_idx]['part'][] = $project_user_part;
		$project_use[$project_user_idx][] = $project_user_email;
	}


	//파티 - 연결된 업무
	$sql = "select work_idx, party_link from work_todaywork_project_info where state='0' and companyno='".$companyno."' and workdate='".$lives_date."'";
	$project_data_info = selectAllQuery($sql);
	$project_link_info = @array_combine($project_data_info['work_idx'], $project_data_info['party_link']);

	$sql = "select count(1) as cnt from work_todaywork where email = '".$user_email."' and workdate = '".$lives_date."' and state != 9 and notice_flag = '0' and (share_flag != '2' OR (share_flag = '1' AND work_idx IS NOT NULL)) and (work_idx IS NULL OR share_flag = '1')";
	$total_cnt_project = selectQuery($sql);

	?>

	<div class="list_function">
		<div class="list_function_in">
			<div class="list_function_left">
			<em><?=$member_info_name?></em><span><?=$member_info_part?></span><strong><?=number_format($total_cnt_project['cnt'])?></strong>
			</div>
			<div class="list_function_right">
				<div class="list_function_calendar">
					<button class="calendar_prev" id="prev_wdate" title="이전"><span>이전</span></button>
					<input type="text" class="calendar_num" value="<?=$lives_date_ch?>" id="lives_date" readonly="readonly"/>
					<button class="calendar_next" id="next_wdate" title="다음"><span>다음</span></button>
				</div>
			</div>
		</div>
	</div>
	<div class="report_area">
		<div class="report_area_in">
			<div class="report_cha">
				<div class="tdw_list">
					<div class="tdw_list_in">
						<ul class="tdw_list_ul">
							<?php
							if($works_info['idx']){
								for($i=0; $i<count($works_info['idx']); $i++){
									$idx = $works_info['idx'][$i];
									$work_idx = $works_info['work_idx'][$i];
									$state = $works_info['state'][$i];
									$work_email = $works_info['email'][$i];
									$work_name = $works_info['name'][$i];
									$work_stime = $works_info['work_stime'][$i];
									$work_etime = $works_info['work_etime'][$i];
									$contents = $works_info['contents'][$i];
									$title = $works_info['title'][$i];
									$work_his = $works_info['his'][$i];
									$work_reg = $works_info['reg'][$i];

									$repeat_work_idx = $works_info['repeat_work_idx'][$i];
									$decide_flag = $works_info['decide_flag'][$i];
									$work_date = $works_info['workdate'][$i];
									$work_flag = $works_info['work_flag'][$i];
									$repeat_flag = $works_info['repeat_flag'][$i];
									$notice_flag = $works_info['notice_flag'][$i];
									$share_flag = $works_info['share_flag'][$i];
									$secret_flag = $works_info['secret_flag'][$i];
									$memo_view =  $works_info['memo_view'][$i];
									$contents_view = $works_info['contents_view'][$i];

									if($decide_flag == '1'){$decide_name = "연차";}else if($decide_flag == '2'){ $decide_name = "반차";}else if($decide_flag == '3'){$decide_name = "외출";}else if($decide_flag == '4'){$decide_name = "조퇴";}
									else if($decide_flag == '5'){$decide_name = "출장";}else if($decide_flag == '6'){$decide_name = "교육";}
									else if($decide_flag == '7'){$decide_name = "미팅";}else if($decide_flag == '8'){$decide_name = "회의";}
									

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
										$memo_view_bt = " off memo_on";
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
										$report_view_bt = " off memo_on";
										$report_view_bt_style = " style=\"display: block;\"";

									}else{
										$report_view_in = "";
										$report_view_bt = " on memo_on";
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

									$req_view_bt_style = "";
									//공유업무 내용 접기/펼치기(0:펼치기, 1:접기)
									if($contents_view == '1'){
										$req_view_in = " off";
										$req_view_bt = " off";
										$req_view_bt_style = " off";

									}else{
										$req_view_in = "";
										$req_view_bt = " on";
										$req_view_bt_style = "";
									}
									//공유함($share_flag=1), 공유취소($share_flag=2), 요청받은업무($work_flag=3) 아이콘 변경
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
										}else if($work_flag=='1'){

											//보고받음
											if($work_idx){
												$li_class = " report_get";
												$tdw_list = false;
											}else{
												//보고함
												$li_class = " report";
												$tdw_list = false;
											}
										}else{

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
									<? if($secret_flag == '1' && $work_email == $user_id){?>
										<li class="tdw_list_li<?=$li_class?>" id="workslist_<?=$idx?>">
											<div class="tdw_list_box<?=($state=='1')?" on":"" ?><?=$share_view_bt_style?> secret_tdw" id="tdw_list_box_<?=$idx?>"  name="onoff_<?=$i?>">
												<div class="tdw_list_chk">
													<button class="btn_tdw_list_chk" <?if($work_flag!='1'){?>value="<?=$idx?>"<?}?> <?=$tdw_list?" id='tdw_dlist_chk'":""?>><span>완료체크</span></button>
												</div>
												<div class="tdw_list_desc <?=$secret_flag == '1'?"lock":""?>">


												<?//업무요청
													$work_title = "";

													if($notice_flag){
														$work_title = "[".$notice_list[$notice_flag] ."]";?>
														<p id="notice_link" value="<?=$work_idx?>"><span><?=$work_title?></span><?=$contents?></p>

													<?}else{


														if($work_flag == "1"){
															//보고받은 업무
															if($work_idx){
																$work_to_name = $work_report_user['receive'][$work_idx];
																$work_title = "[".$work_to_name ."님에게 보고받음]";

															}else{

																//보고 1명 이상인 경우
																if($work_report_user['send_cnt'][$idx] > 1){
																	$work_user_count = $work_report_user['send_cnt'][$idx] - 1;
																	$work_report_user_title = $work_report_user['send'][$idx][0]. "님 외 ". $work_user_count . "명에게 보고함";
																	$work_title = "[". $work_report_user_title. "]";
																}else{
																	$work_report_user_title = $work_report_user['send'][$idx][0];
																	$work_title = "[". $work_report_user_title. "님에게 보고함]";
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

															//업무수정 및 비밀글 설정(보고 타이틀)
															if($secret_flag == '1'){?>
																<?php if($work_email == $user_id){?>
																	<p><span><?=$work_title?></span><?=$title?><?=$read_report_text?></p>
																<?php }else{?>
																	<p><span><?=$work_title?></span><img src = "/html/images/pre/ico_tr_l.png">비밀글 입니다.<?=$read_report_text?></p>
																	<?php }?>
															<?php }else{?>
																<p><span><?=$work_title?></span><?=$title?><?=$read_report_text?></p>
															<?php }
															$edit_content = $title;

														//요청업무
														}else if($work_flag == "3"){
															//$work_user_name = "";
															//for($j=0; $j<count($work_user_list[$work_com_idx]); $j++){
															//	$work_user_name .= $work_user_list[$work_com_idx][$j] . ", ";
															//}

															if($work_idx){
																
																//$work_to_name = $work_to_user_list['work_name'][$work_idx];
																$work_req_name = $work_req_user['receive'][$work_idx];
																$work_title = "[".$work_req_name ."님에게 요청받음]";

															}else{

																//업무요청 1명 이상인 경우
																if($work_req_user['send_cnt'][$work_com_idx] > 1){
																	$work_user_count = $work_req_user['send_cnt'][$work_com_idx] - 1;
																	$work_req_title = $work_req_user['send'][$work_com_idx][0]. "님 외 ". $work_user_count . "명에게 요청함";
																	$work_title = "[". $work_req_title. "]";
																}else{
																	$work_req_title = $work_req_user['send'][$work_com_idx][0];
																	$work_title = "[". $work_req_title. "님에게 요청함]";
																}

																$work_req_read_all = $work_req_user['read'][$work_com_idx]['all'];
																$work_req_read_cnt = $work_req_user['read'][$work_com_idx]['read'];
																$work_req_read_reading = $work_req_read_cnt;

																if($work_req_read_reading>0){
																	$read_req_text = "&nbsp;&nbsp;<em class=\"tdw_read\">읽음 ".$work_req_read_reading."</em>";
																}else{
																	$read_req_text = "&nbsp;&nbsp;<em class=\"tdw_read\">읽음 0</em>";
																}
															}

															//업무수정
															if($secret_flag == '1'){
																if($work_email == $user_id){?>
																	<p><span><?=$work_title?></span><?=textarea_replace($contents)?><?=$read_req_text?></p>
																<?}else{?>
																	<p><span><?=$work_title?></span><img src = "/html/images/pre/ico_tr_l.png">비밀글 입니다.<?=$read_req_text?></p>
																<?}
															}else{?>
																<p><span><?=$work_title?></span><?=textarea_replace($contents)?><?=$read_req_text?></p>
															<?}
															$edit_content = $contents;

														}else{
															//받은 업무가 있을경우
															$edit_id = "";
															if($work_idx){

																if($share_flag == "1"){
																	$edit_id = " id='tdw_list_edit_".$idx."'";

																	if($work_share_user['send_cnt'][$idx] > 1){
																		$work_user_count = $work_share_user['send_cnt'][$idx] - 1;
																		$work_req_user_title = $work_share_user['send'][$idx][0]. "님 외 ". $work_user_count . "명에게 공유함";
																		$work_title = "[". $work_req_user_title. "]";
																	}else{
																		$work_req_user_title = $work_share_user['send'][$idx][0];
																		$work_title = "[". $work_req_user_title. "님에게 공유함]";
																	}

																	$work_share_read_all = $work_share_user['read'][$work_idx]['all'];
																	$work_share_read_cnt = $work_share_user['read'][$work_idx]['read'];
																	$work_share_read_reading = $work_share_read_cnt;

																	//읽지않은사용자
																	if($work_share_read_reading>0){
																		$read_share_text = "&nbsp;&nbsp;<em class=\"tdw_read\">읽음 ".$work_share_read_reading."</em>";
																	}else{
																		$read_share_text = "&nbsp;&nbsp;<em class=\"tdw_read\">읽음 0</em>";
																	}

																}else if($share_flag == "2"){
																	$work_to_name = $work_share_user['receive'][$work_idx];
																	$work_title = "[".$work_to_name ."님에게 공유받음]";
																}else{
																	$work_to_name = $work_req_user['receive'][$work_idx];
																	$work_title = "[".$work_to_name ."님에게 요청받음]";
																}
																?>
																<p <?=$edit_id?>><?=$work_title?"<span>".$work_title."</span>":""?><?=textarea_replace($contents)?><?=$read_share_text?></p>
															<?
															//일반업무
															}else{
																if($work_stime != null && $work_stime != null){
															?>	
																<p id="tdw_list_edit_<?=$idx?>">
																	<?if($decide_flag == 1){?>
																		<span> <?= "[ ".$decide_name." ]" ?></span><?=textarea_replace($contents)?>
																	<?}else if($decide_flag > 1){?>
																		<span> <?= "[ ".$decide_name."   ".$work_stime."~".$work_etime." ]" ?></span><?=textarea_replace($contents)?>
																	<?}?>
																</p>
															<?}else{?>
																<p id="tdw_list_edit_<?=$idx?>"><?=textarea_replace($contents)?></p>
															<?}}

															$edit_content = $contents;
														}
													}

												?>
												</div>

												<?//첨부파일 정보
												//나의업무, 요청업무
												if($$secret_flag == '0'){
													if(in_array($work_flag, array('2','3'))){ 
														if($tdf_files[$work_com_idx]['file_path']){?>
															<div class="tdw_list_file">
																<?for($k=0; $k<count($tdf_files[$work_com_idx]['file_path']); $k++){?>
																	<div class="tdw_list_file_box">
																		<button class="btn_list_file" id="btn_list_file_<?=$tdf_files[$work_com_idx]['num'][$k]?>" value="<?=$tdf_files[$work_com_idx]['idx'][$k]?>"><span><?=$tdf_files[$work_com_idx]['file_real_name'][$k]?></span></button>
																		<?//보고업무 작성한 사용자만 삭제
																		if($user_id==$tdf_files[$work_com_idx]['email'][$k]){?>
																			<button class="btn_list_file_del" id="btn_list_fdel" value="<?=$tdf_files[$work_com_idx]['idx'][$k]?>" title="삭제"><span>삭제</span></button>
																		<?}?>
																	</div>
																<?}?>
															</div>
														<?}
													}
												}?>


											</div>

											<?//보고업무
											if($work_flag=='1'){
												if($work_idx == null){
													$report_email = $work_report_list[$idx]['email'];
													$report_name =$work_report_list[$idx]['name'];
													$report_contents =$work_report_list[$idx]['contents'];

												}else{
													$report_email = $work_report_list[$work_idx]['email'];
													$report_name =$work_report_list[$work_idx]['name'];
													$report_contents =$work_report_list[$work_idx]['contents'];
												}
											?>

												<div class="tdw_list_report_area">
													<div class="tdw_list_report_area_in<?=$report_view_in?>" id="tdw_list_report_area_in_<?=$idx?>">
														<div class="tdw_list_report_desc">
															<div class="tdw_list_report_conts">
																<?if($user_email==$report_email){?>
																	<span class="tdw_list_report_conts_txt" id="tdw_list_report_conts_txt_<?=$idx?>"><?=textarea_replace($report_contents)?></span>
																<?}else{?>
																	<span class="tdw_list_report_conts_txt"><?=textarea_replace($report_contents)?></span>
																<?}?>

																	<em class="tdw_list_report_conts_date"><?=$work_his?></em>

																</div>
														</div>

														<?//첨부파일 정보
														if($tdf_files[$work_com_idx]['file_path']){?>
															<div class="tdw_list_file">
																<?for($k=0; $k<count($tdf_files[$work_com_idx]['file_path']); $k++){?>
																	<div class="tdw_list_file_box">
																		<button class="btn_list_file" id="btn_list_file_<?=$tdf_files[$work_com_idx]['num'][$k]?>" value="<?=$tdf_files[$work_com_idx]['idx'][$k]?>"><span><?=$tdf_files[$work_com_idx]['file_real_name'][$k]?></span></button>
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


											<!-- <?if($work_flag=='2' && $share_flag=='0' && $notice_flag=='0'){?>
												<div class="tdw_list_work_onoff"<?=$share_view_bt_style?>>
													<button class="btn_list_work_onoff <?=($contents_view=="1"? " off": "")?>" id="btn_list_work_onoff_<?=$idx?>" value="<?=$idx?>"><span>업무 접기/펼치기</span></button>
												</div>
											<?}?> -->
											<?if($share_flag && $work_idx){?>
												<div class="tdw_list_share_onoff"<?=$share_view_bt_style?>>
													<button class="btn_list_share_onoff<?=($comment_list[$work_com_idx]?" memo_on": "");?><?=$share_view_bt?>" id="btn_list_share_onoff_<?=$idx?>" value="<?=$idx?>" <?if(trim($share_view_bt)=="on"){ echo "title='공유 접기'"; }else{ echo "title='공유 펼치기'"; }?>><span>공유 접기/펼치기</span></button>
												</div>
											<?}?>

											<?if($work_flag=='3'){?>
												<div class="tdw_list_req_onoff"<?=$req_view_bt_style?>>
													<button class="btn_list_req_onoff<?=($comment_list[$work_com_idx]?" memo_on": "");?><?=$req_view_bt?>" id="btn_list_req_onoff_<?=$idx?>" value="<?=$idx?>" <?if(trim($req_view_bt)=="on"){ echo "title='요청 접기'";}else{ echo "title='요청 펼치기'"; }?>><span>요청 접기/펼치기</span></button>
												</div>
											<?}?>
											<div class="tdw_list_memo_area">
												<div class="tdw_list_memo_area_in<?=$memo_view_in?>" id="tdw_list_memo_area_in_<?=$idx?>">
													<?
													//댓글리스트
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
																$sql = $sql." where b.idx = '".$comment_idx."' and a.ai_like_idx = b.ai_like_idx and send_email = '".$user_email."'";
																$click_like = selectQuery($sql);

																$sql = "select a.idx from work_todaywork_like a join work_todaywork_comment b on a.work_idx = b.idx where b.idx = '".$comment_idx."'";
																$sql = $sql." and a.state = 0 and a.send_email = '".$user_email."'";
																$cli_like = selectQuery($sql);

																$sql = "select idx from work_todaywork_comment where idx = '".$comment_idx."' and email = '".$user_email."'";
																$my_like = selectQuery($sql);

																$sql = "select idx from work_todaywork_comment where idx = '".$comment_idx."' and like_email = '".$user_email."'";
																$my_coin_like = selectQuery($sql);

																//코인보상 표기(요청받음)
																$sql = "select link_idx from work_todaywork_comment where cmt_flag=1 and link_idx != work_idx and idx = '".$comment_idx."'";

																$coin_work = selectAllQuery($sql);

																if($coin_work){
																	for($co_i=0; $co_i<count($coin_work['link_idx']); $co_i++){
																		$coin_work_idx = $coin_work['link_idx'][$co_i];

																		$sql = "select idx, email, reward_user, reward_name, coin,memo,date_format(regdate , '%m/%d/%y %l:%i:%s %p') regdate from work_coininfo";
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

																			if($coin_info_r_coin>0){
																				$coin_info_r_coin = number_format($coin_info_r_coin);
																			}

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
																		<?if(!$cmt_flag && $user_email==$comment_list[$work_com_idx]['email'][$k]){?>
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
																	</div>
																</div>

															<?}?>
														<?}?>

													<?}else{?>
														<?//받은업무
														if ($work_idx){?>
															<?
																//코인보상 표기(오늘업무idx번호, 코멘트idx번호)
																//(보고받음, 공유받음, 요청받음)
																work_memo_list($idx, $comment_idx);
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
																	$sql = $sql." and a.state = 0 and a.send_email = '".$user_email."'";
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
																		<?if(!$cmt_flag && $user_email==$comment_list[$work_idx]['email'][$k]){?>
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

																			<?if($cmt_flag != 2){?>
																				</em>
																			<?}?>

																		</div>
																	</div>

																<?}?>

															<?}?>

														<?}else{?>
															<?

																//코인보상 표기(오늘업무idx번호, 코멘트idx번호)
																//(보고받음, 공유받음)
																work_memo_list($idx, $comment_idx);

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
																	$sql = $sql." and a.state = 0 and a.send_email = '".$user_email."'";
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
																		<?if(!$cmt_flag && $user_email==$comment_list[$idx]['email'][$k]){?>
																			<!-- 일반 메모 -->
																			<span class="tdw_list_memo_conts_txt" id="tdw_list_memo_conts_txt_<?=$comment_idx?>"><?=textarea_replace($comment_list[$idx]['comment'][$k])?></span>
																		<?}else if($cmt_flag == 1 && $work_give_list){?>
																			<!-- 좋아요 받았을 때 문장 -->
																			<span class="tdw_list_memo_conts_txt"><?=textarea_replace($comment_list[$idx]['comment'][$k])?></span>
																		<?}else{?>
																			<?if($cmt_flag != 2){?>
																				<!-- AI 문장 -->
																				<span class="tdw_list_memo_conts_txt"><?=textarea_replace($comment_list[$idx]['comment'][$k])?></span>
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

												<?if($comment_list[$work_com_idx]){?>
													<div class="tdw_list_memo_onoff" <?=$memo_view_bt_style?>>
														<button class="btn_list_memo_onoff<?=$memo_view_bt?>" id="btn_list_memo_onoff_<?=$idx?>" value="<?=$idx?>" <?//if(trim($memo_view_bt)=="on"){ echo "title='메모 접기@@'"; }else{ echo "title='메모 펼치기@@'"; }?>><span>메모 접기/펼치기</span></button>
													</div>
												<?}?>
											</div>
										</li>
									<? }else if($secret_flag != '1'){?>
										<li class="tdw_list_li<?=$li_class?>" id="workslist_<?=$idx?>">
											<div class="tdw_list_box<?=($state=='1')?" on":""?><?=$share_view_bt_style?> no_secret_tdw" id="tdw_list_box_<?=$idx?>" name="onoff_<?=$i?>">
												<div class="tdw_list_chk">
													<button class="btn_tdw_list_chk" <?if($work_flag!='1'){?>value="<?=$idx?>"<?}?> <?=$tdw_list?" id='tdw_dlist_chk'":""?>><span>완료체크</span></button>
												</div>
												<div class="tdw_list_desc">


												<?//업무요청
													$work_title = "";

													if($notice_flag){
														$work_title = "[".$notice_list[$notice_flag] ."]";?>
														<p id="notice_link" value="<?=$work_idx?>"><span><?=$work_title?></span><?=$contents?></p>

													<?}else{


														if($work_flag == "1"){
															//보고받은 업무
															if($work_idx){
																$work_to_name = $work_report_user['receive'][$work_idx];
																$work_title = "[".$work_to_name ."님에게 보고받음]";

															}else{

																//보고 1명 이상인 경우
																if($work_report_user['send_cnt'][$idx] > 1){
																	$work_user_count = $work_report_user['send_cnt'][$idx] - 1;
																	$work_report_user_title = $work_report_user['send'][$idx][0]. "님 외 ". $work_user_count . "명에게 보고함";
																	$work_title = "[". $work_report_user_title. "]";
																}else{
																	$work_report_user_title = $work_report_user['send'][$idx][0];
																	$work_title = "[". $work_report_user_title. "님에게 보고함]";
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
															
															//업무수정
															if($work_idx == null && $user_email == $work_email){?>
																<p id="tdw_list_edit_<?=$idx?>"><span><?=$work_title?></span><?=$title?><?=$read_report_text?></p>
															<?}else{?>
																<p><span><?=$work_title?></span><?=$title?><?=$read_report_text?></p>
															<?}
															$edit_content = $title;

														//요청업무
														}else if($work_flag == "3"){
															//$work_user_name = "";
															//for($j=0; $j<count($work_user_list[$work_com_idx]); $j++){
															//	$work_user_name .= $work_user_list[$work_com_idx][$j] . ", ";
															//}

															if($work_idx){
																
																//$work_to_name = $work_to_user_list['work_name'][$work_idx];
																$work_req_name = $work_req_user['receive'][$work_idx];
																$work_title = "[".$work_req_name ."님에게 요청받음]";

															}else{

																//업무요청 1명 이상인 경우
																if($work_req_user['send_cnt'][$work_com_idx] > 1){
																	$work_user_count = $work_req_user['send_cnt'][$work_com_idx] - 1;
																	$work_req_title = $work_req_user['send'][$work_com_idx][0]. "님 외 ". $work_user_count . "명에게 요청함";
																	$work_title = "[". $work_req_title. "]";
																}else{
																	$work_req_title = $work_req_user['send'][$work_com_idx][0];
																	$work_title = "[". $work_req_title. "님에게 요청함]";
																}

																$work_req_read_all = $work_req_user['read'][$work_com_idx]['all'];
																$work_req_read_cnt = $work_req_user['read'][$work_com_idx]['read'];
																$work_req_read_reading = $work_req_read_cnt;

																if($work_req_read_reading>0){
																	$read_req_text = "&nbsp;&nbsp;<em class=\"tdw_read\">읽음 ".$work_req_read_reading."</em>";
																}else{
																	$read_req_text = "&nbsp;&nbsp;<em class=\"tdw_read\">읽음 0</em>";
																}
															}

															//업무수정
															if($work_idx == null && $user_email == $work_email){?>
																<p id="tdw_list_edit_<?=$idx?>"><span><?=$work_title?></span><?=textarea_replace($contents)?><?=$read_req_text?></p>
															<?}else{?>
																<p><span><?=$work_title?></span><?=textarea_replace($contents)?><?=$read_req_text?></p>
															<?}
															$edit_content = $contents;

														}else{
															//받은 업무가 있을경우
															$edit_id = "";
															if($work_idx){

																if($share_flag == "1"){
																	$edit_id = " id='tdw_list_edit_".$idx."'";

																	if($work_share_user['send_cnt'][$idx] > 1){
																		$work_user_count = $work_share_user['send_cnt'][$idx] - 1;
																		$work_req_user_title = $work_share_user['send'][$idx][0]. "님 외 ". $work_user_count . "명에게 공유함";
																		$work_title = "[". $work_req_user_title. "]";
																	}else{
																		$work_req_user_title = $work_share_user['send'][$idx][0];
																		$work_title = "[". $work_req_user_title. "님에게 공유함]";
																	}

																	$work_share_read_all = $work_share_user['read'][$work_idx]['all'];
																	$work_share_read_cnt = $work_share_user['read'][$work_idx]['read'];
																	$work_share_read_reading = $work_share_read_cnt;

																	//읽지않은사용자
																	if($work_share_read_reading>0){
																		$read_share_text = "&nbsp;&nbsp;<em class=\"tdw_read\">읽음 ".$work_share_read_reading."</em>";
																	}else{
																		$read_share_text = "&nbsp;&nbsp;<em class=\"tdw_read\">읽음 0</em>";
																	}

																}else if($share_flag == "2"){
																	$work_to_name = $work_share_user['receive'][$work_idx];
																	$work_title = "[".$work_to_name ."님에게 공유받음]";
																}else{
																	$work_to_name = $work_req_user['receive'][$work_idx];
																	$work_title = "[".$work_to_name ."님에게 요청받음]";
																}
																?>
																<p <?=$edit_id?>><?=$work_title?"<span>".$work_title."</span>":""?><?=textarea_replace($contents)?><?=$read_share_text?></p>
															<?
															//일반업무
															}else{
																if($work_stime != null && $work_stime != null){
															?>	
																<p id="tdw_list_edit_<?=$idx?>">
																	<?if($decide_flag == 1){?>
																		<span> <?= "[ ".$decide_name." ]" ?></span><?=textarea_replace($contents)?>
																	<?}else if($decide_flag > 1){?>
																		<span> <?= "[ ".$decide_name."   ".$work_stime."~".$work_etime." ]" ?></span><?=textarea_replace($contents)?>
																	<?}?>
																</p>
															<?}else{?>
																<p id="tdw_list_edit_<?=$idx?>"><?=textarea_replace($contents)?></p>
															<?}}

															$edit_content = $contents;
														}
													}

												?>
												</div>

												<?//첨부파일 정보
												//나의업무, 요청업무
												if($secret_flag == '0'){
													if(in_array($work_flag, array('2','3'))){ 
														if($tdf_files[$work_com_idx]['file_path']){?>
															<div class="tdw_list_file">
																<?for($k=0; $k<count($tdf_files[$work_com_idx]['file_path']); $k++){?>
																	<div class="tdw_list_file_box">
																		<button class="btn_list_file" id="btn_list_file_<?=$tdf_files[$work_com_idx]['num'][$k]?>" value="<?=$tdf_files[$work_com_idx]['idx'][$k]?>"><span><?=$tdf_files[$work_com_idx]['file_real_name'][$k]?></span></button>
																		<?//보고업무 작성한 사용자만 삭제
																		if($user_id==$tdf_files[$work_com_idx]['email'][$k]){?>
																			<button class="btn_list_file_del" id="btn_list_fdel" value="<?=$tdf_files[$work_com_idx]['idx'][$k]?>" title="삭제"><span>삭제</span></button>
																		<?}?>
																	</div>
																<?}?>
															</div>
														<?}
													}
												}?>


											</div>

											<?//보고업무
											if($work_flag=='1'){
												if($work_idx == null){
													$report_email = $work_report_list[$idx]['email'];
													$report_name =$work_report_list[$idx]['name'];
													$report_contents =$work_report_list[$idx]['contents'];

												}else{
													$report_email = $work_report_list[$work_idx]['email'];
													$report_name =$work_report_list[$work_idx]['name'];
													$report_contents =$work_report_list[$work_idx]['contents'];
												}
											?>

												<div class="tdw_list_report_area">
													<div class="tdw_list_report_area_in<?=$report_view_in?>" id="tdw_list_report_area_in_<?=$idx?>">
														<div class="tdw_list_report_desc">
															<div class="tdw_list_report_conts">
																<?if($user_email==$report_email){?>
																	<span class="tdw_list_report_conts_txt" id="tdw_list_report_conts_txt_<?=$idx?>"><?=textarea_replace($report_contents)?></span>
																<?}else{?>
																	<span class="tdw_list_report_conts_txt"><?=textarea_replace($report_contents)?></span>
																<?}?>

																	<em class="tdw_list_report_conts_date"><?=$work_his?></em>

																</div>
														</div>

														<?//첨부파일 정보
														if($tdf_files[$work_com_idx]['file_path']){?>
															<div class="tdw_list_file">
																<?for($k=0; $k<count($tdf_files[$work_com_idx]['file_path']); $k++){?>
																	<div class="tdw_list_file_box">
																		<button class="btn_list_file" id="btn_list_file_<?=$tdf_files[$work_com_idx]['num'][$k]?>" value="<?=$tdf_files[$work_com_idx]['idx'][$k]?>"><span><?=$tdf_files[$work_com_idx]['file_real_name'][$k]?></span></button>
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


											<!-- <?if($work_flag=='2' && $share_flag=='0' && $notice_flag=='0'){?>
												<div class="tdw_list_work_onoff"<?=$share_view_bt_style?>>
													<button class="btn_list_work_onoff <?=($contents_view=="1"? " off": "")?>" id="btn_list_work_onoff_<?=$idx?>" value="<?=$idx?>"><span>업무 접기/펼치기</span></button>
												</div>
											<?}?> -->
											<?if($share_flag && $work_idx){?>
												<div class="tdw_list_share_onoff"<?=$share_view_bt_style?>>
													<button class="btn_list_share_onoff<?=($comment_list[$work_com_idx]?" memo_on": "");?><?=$share_view_bt?>" id="btn_list_share_onoff_<?=$idx?>" value="<?=$idx?>" <?if(trim($share_view_bt)=="on"){ echo "title='공유 접기'"; }else{ echo "title='공유 펼치기'"; }?>><span>공유 접기/펼치기</span></button>
												</div>
											<?}?>

											<?if($work_flag=='3'){?>
												<div class="tdw_list_req_onoff"<?=$req_view_bt_style?>>
													<button class="btn_list_req_onoff<?=($comment_list[$work_com_idx]?" memo_on": "");?><?=$req_view_bt?>" id="btn_list_req_onoff_<?=$idx?>" value="<?=$idx?>" <?if(trim($req_view_bt)=="on"){ echo "title='요청 접기'";}else{ echo "title='요청 펼치기'"; }?>><span>요청 접기/펼치기</span></button>
												</div>
											<?}?>

											<div class="tdw_list_memo_area">
												<div class="tdw_list_memo_area_in<?=$memo_view_in?>" id="tdw_list_memo_area_in_<?=$idx?>">
													<?
													//댓글리스트
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
																$sql = $sql." where b.idx = '".$comment_idx."' and a.ai_like_idx = b.ai_like_idx and send_email = '".$user_email."'";
																$click_like = selectQuery($sql);

																$sql = "select a.idx from work_todaywork_like a join work_todaywork_comment b on a.work_idx = b.idx where b.idx = '".$comment_idx."'";
																$sql = $sql." and a.state = 0 and a.send_email = '".$user_email."'";
																$cli_like = selectQuery($sql);

																$sql = "select idx from work_todaywork_comment where idx = '".$comment_idx."' and email = '".$user_email."'";
																$my_like = selectQuery($sql);

																$sql = "select idx from work_todaywork_comment where idx = '".$comment_idx."' and like_email = '".$user_email."'";
																$my_coin_like = selectQuery($sql);

																//코인보상 표기(요청받음)
																$sql = "select link_idx from work_todaywork_comment where cmt_flag=1 and link_idx != work_idx and idx = '".$comment_idx."'";

																$coin_work = selectAllQuery($sql);

																if($coin_work){
																	for($co_i=0; $co_i<count($coin_work['link_idx']); $co_i++){
																		$coin_work_idx = $coin_work['link_idx'][$co_i];

																		$sql = "select idx, email, reward_user, reward_name, coin,memo,date_format(regdate , '%m/%d/%y %l:%i:%s %p') regdate from work_coininfo";
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

																			if($coin_info_r_coin>0){
																				$coin_info_r_coin = number_format($coin_info_r_coin);
																			}

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
																		<?if(!$cmt_flag && $user_email==$comment_list[$work_com_idx]['email'][$k]){?>
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
																	</div>
																</div>

															<?}?>
														<?}?>

													<?}else{?>
														<?//받은업무
														if ($work_idx){?>
															<?
																//코인보상 표기(오늘업무idx번호, 코멘트idx번호)
																//(보고받음, 공유받음, 요청받음)
																work_memo_list($idx, $comment_idx);
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
																	$sql = $sql." and a.state = 0 and a.send_email = '".$user_email."'";
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
																			<?if(!$cmt_flag && $user_email==$comment_list[$work_idx]['email'][$k]){?>
																				<span class="tdw_list_memo_conts_txt" id="tdw_list_memo_conts_txt_<?=$comment_idx?>"><?=textarea_replace($comment_list[$work_idx]['comment'][$k])?></span>
																			<?}else if($cmt_flag == 1 && $work_give_list){?>
																				<span class="tdw_list_memo_conts_txt"><?=textarea_replace($comment_list[$work_idx]['comment'][$k])?></span>
																			<?}else{?>
																				<?if($cmt_flag != 2){?>
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

																			<?if($cmt_flag != 2){?>
																				</em>
																			<?}?>

																		</div>
																	</div>

																<?}?>

															<?}?>

														<?}else{?>
															<?

																//코인보상 표기(오늘업무idx번호, 코멘트idx번호)
																//(보고받음, 공유받음)
																work_memo_list($idx, $comment_idx);

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
																	$sql = $sql." and a.state = 0 and a.send_email = '".$user_email."'";
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
																		<?if(!$cmt_flag && $user_email==$comment_list[$idx]['email'][$k]){?>
																			<span class="tdw_list_memo_conts_txt" id="tdw_list_memo_conts_txt_<?=$comment_idx?>"><?=textarea_replace($comment_list[$idx]['comment'][$k])?></span>
																		<?}else if($cmt_flag == 1 && $work_give_list){?>
																			<span class="tdw_list_memo_conts_txt"><?=textarea_replace($comment_list[$idx]['comment'][$k])?></span>
																		<?}else{?>
																			<?if($cmt_flag != 2){?>
																				<span class="tdw_list_memo_conts_txt"><?=textarea_replace($comment_list[$idx]['comment'][$k])?></span>
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

												<?if($comment_list[$work_com_idx]){?>
													<div class="tdw_list_memo_onoff" <?=$memo_view_bt_style?>>
														<button class="btn_list_memo_onoff<?=$memo_view_bt?>" id="btn_list_memo_onoff_<?=$idx?>" value="<?=$idx?>" <?//if(trim($memo_view_bt)=="on"){ echo "title='메모 접기@@'"; }else{ echo "title='메모 펼치기@@'"; }?>><span>메모 접기/펼치기</span></button>
													</div>
												<?}?>
											</div>
										</li>
									<? }?>
								<?}?>


							<?//}else{?>
								<!-- <div class="tdw_list_none">
									<strong><span>현재 등록된 오늘업무가 없습니다.</span></strong>
								</div> -->
							<?}?>
						</ul>
					</div>
				</div>
			</div>
		</div>
	</div>|<?=$echo_date?>
<?php
	exit;
}


//오늘업무 좌측 회원리스트
if($mode == "todaywork_user_list"){

	$wdate = $_POST['lives_date'];
	$input_val = $_POST['input_val'];
	$rid = $_POST['rid'];

	
	print "<pre>";
	print_r($_POST);
	print "</pre>";
	

	if(!$wdate){
		$wdate = TODATE;
	}

	if($wdate){

		if(strpos($wdate, ".") !== false){
			$lives_date_ch = $wdate;
			$wdate = str_replace(".", "-",$wdate);
		}

		//이름, 부서명 검색
		if($input_val){
			$where = " and (name like '%".$input_val."%' or part like '%".$input_val."%')";
		}


		//오늘업무
		$sql ="select email, count(idx) as cnt from work_todaywork where state!='9' and notice_flag='0' and (share_flag != '2' OR (share_flag = '1' AND work_idx IS NOT NULL)) and (work_idx IS NULL OR share_flag = '1') and workdate='".$wdate."'".$where." group by email";
		$user_list_info = selectAllQuery($sql);
		if($user_list_info['email']){
			$user_list_count = @array_combine($user_list_info['email'], $user_list_info['cnt']); // email 유저별 업무개수 
		}


		//검색어가 있을때
		if($input_val){
			//회원정보 리스트
			$sql = "select idx, email, name, part, profile_type, profile_img_idx from work_member where state='0'";


			//관리권한은 제외처리
			if($user_level == 1){
				$sql = $sql .= "and email!='".$user_id."'";
			}else{
				$sql = $sql .= "and companyno='".$companyno."'";
			}
			$sql = $sql .=" and highlevel!='".$grade_arr['manager']."'";
			$sql = $sql .= " ".$where."";
			$sql = $sql .= " order by name asc";
			$user_info = selectAllQuery($sql);

		}else{

			//회원정보 리스트
			$sql = "select idx, email, name, part, profile_type, profile_img_idx from work_member where state='0'";

			//관리권한은 제외처리
			if($user_level == 1){
				$sql = $sql .=" and email!='".$user_id."'";
			}else{
				$sql = $sql .=" and companyno='".$companyno."'";
			}


			$sql = $sql .=" and highlevel!='".$grade_arr['manager']."'";
			$sql = $sql .=" order by";
			$sql = $sql .=" CASE WHEN email = '".$user_id."' THEN email END DESC, CASE WHEN email <> '".$user_id."' THEN name end asc";
			$user_info = selectAllQuery($sql);
		}

		if($user_info['idx']){
			$total_cnt = number_format(count($user_info['idx']));

			for($i=0; $i<count($user_info['idx']); $i++){

				$member_email = $user_info['email'][$i];

				$user_list_cnt = $user_list_count[$member_email];
				if($user_list_cnt > 0){
					$user_num = number_format($user_list_cnt);
					$user_num_class = "";
				}else{
					$user_num = "0";
					$user_num_class = " user_num_0";
				}

				//프로필 캐릭터,사진
				$profile_main_img_src = profile_img_info($member_email);


			?>
				<li>
					<button value="<?=$member_email?>" <?=$rid==$member_email?" class=\"on\"":""?>>
						<?=($user_id == $member_email)?"<img src=\"/html/images/pre/ico_me.png\" alt=\"\" class=\"user_me\" />":"";?>
						<div class="user_img" style="background-image:url('<?=$profile_main_img_src?>');"></div></div>
						<div class="user_name">
							<strong><?=$user_info['name'][$i]?></strong>
							<?=$user_info['part'][$i]?>
						</div>
						<span class="user_num<?=$user_num_class?>">
							<span><?=$user_num?>
						</span>
						</span>
					</button>
				</li>
			<?php
			}?>|<?=count($user_info['idx'])?>
		<?php
		}else{?>
			<div class="layer_user_no"><strong>검색결과가 없습니다.</strong></div>|0
		<?php
		}
	}
	exit;
}

// 라이브 상태값에 따른 리스트
if($mode == "lives_count_list"){
}
//라이브 메인 페이지 리스트 리뉴얼
if($mode == "lives_index_list_new"){
	$gp = $_POST['gp'];

	if(!$gp) {
		$gp = 1;
	}

	$pagesize = 35;						//페이지 출력갯수
	$startnum = 0;						//페이지 시작번호
	$endnum = $gp * $pagesize;			//페이지 끝번

	//시작번호
	if ($gp == 1){
		$startnum = 0;
	}else{
		$startnum = ($gp - 1) * $pagesize;
	}

	
	//실행속도
	$start = get_time();

	//어제날짜
	$yesterday = date("Y-m-d", strtotime(TODATE." -1 day"));
	$input_val = $_POST['input_val'];
	$list_rank = $_POST['list_rank'];
	$category = $_POST['category'];

	$where = "";
	//관리권한은 제외처리
	if($user_level == 1){
		//$sql = $sql .= " and email !='".$user_id."'";
	}else{
		$where .= " and highlevel != '1'";
		$where_c .= " and a.highlevel != '1'";
	}

	// 연차/반차:rest , 조퇴/외출: outing , 미팅/회의 : meet, 출장 : business 
	if($category){
		if($category == 'rest'){
			$where_cate = "and b.decide_flag in (1,2)";
			$where_date = "and b.workdate = '".TODATE."'";
		}else if($category == 'early'){
			$where_cate = "and b.decide_flag in (3,4)";
			$where_date = "and b.workdate = '".TODATE."'";
		}else if($category == 'meet'){
			$where_cate = "and b.decide_flag in (7,8)";
			$where_date = "and b.workdate = '".TODATE."'";
		}else if($category == 'business'){
			$where_cate = "and b.decide_flag = '5'";
			$where_date = "and b.workdate = '".TODATE."'";
		}else if($category == 'all'){
		}
	}

	//전체보기:all, 접속자보기:on, 미접속자보기:off
	if($list_rank){
		if($list_rank == 'on'){
			$where .= " and live_1='1'";
		}else if($list_rank == 'off'){
			$where .= " and live_1='0'";
		}
	}

	$where_search = "";
	//이름, 부서명 검색
	if($input_val){
		$where_search .= " and (name like '%".$input_val."%' or part like '%".$input_val."%')";
		$where_search_d .= " and (a.name like '%".$input_val."%' or a.part like '%".$input_val."%')";
	}

	$sql = "select count(1) as cnt from work_member where state='0' and companyno = '".$companyno."' and highlevel!='1'";
	$sql .= $where.$where_search;
	$total_count = selectQuery($sql);

	if($total_count){
		$member_all_cnt = number_format($total_count['cnt']);
	}else{
		$member_all_cnt = 0;
	}

		//페이징 갯수
		if ( ($member_all_cnt % $pagesize) > 0 ){
			$page_count = floor($member_all_cnt/$pagesize)+1;
		}else{
			$page_count = floor($member_all_cnt/$pagesize);
		}
	
	// //라이브 카운터

	$sql = "select count(1) as cnt from work_member where state='0' and companyno = '".$companyno."' and highlevel!='1'";
	$sql .= $where.$where_search;
	$live_count = selectQuery($sql);

	if($live_count){
		$member_live_cnt = number_format($live_count['cnt']);
	}else{
		$member_live_cnt = 0;
	}
	

	// 개인 업무 총 갯수
	$sql = "select email, state, count(1) as cnt from work_todaywork where workdate = '".TODATE."' and state != 9 and companyno = '".$companyno."' and notice_flag = '0' group by state, email;";
	$works_all_info = selectAllQuery($sql);

	for($i=0; $i<count($works_all_info['email']); $i++){
		$works_email = $works_all_info['email'][$i];
		$works_state = $works_all_info['state'][$i];
		$works_cnt = $works_all_info['cnt'][$i];
		$work_live[$works_email][$works_state] += $works_cnt;
	}

	//회원전체

	$sql = "select email, name from work_member_zzim_list where email = '".$user_id."'";
	$check_zzim = selectQuery($sql);

	if($check_zzim){
		$where_zzim = "and c.email = '".$user_id."'";
	}else{
		$where_zzim = "and (c.email = '".$user_id."' or c.email != '' or c.email is null)";
	}

	if($check_zzim && $category == 'all'){
		$sql = "select a.idx, a.email, a.name, a.part,a.gender, a.partno, a.profile_type, a.profile_img_idx, a.live_1, a.live_2, a.live_3, a.live_4, a.memo, a.memo_editdate, DATE_FORMAT(a.live_1_regdate, '%H:%i') as live_1_time, a.live_1_regdate, a.penalty_state";
		$sql .= " from work_member a USE INDEX(state)"; 
		$sql .= " left join work_member_zzim_list c on a.idx = c.mem_idx";
		$sql .= " where a.state='0' $where_zzim";
		$sql .= " and a.companyno='".$companyno."' $where_date";
		$sql .= $where_c.$where_search;
		$sql .= " group by a.name";
		$sql .= " order by ";
		$sql .= " CASE WHEN a.email='".$user_id."' THEN a.email END DESC,";
		$sql .= " CASE WHEN c.state = '1' and c.email = '".$user_id."'  THEN c.idx END DESC,";
		// $sql .= " CASE WHEN a.partno='".$user_part."' then  a.partno END DESC";
		$sql .= " CASE WHEN a.memo is not null and a.memo != '' then  a.email END DESC,";
		$sql .= " CASE WHEN a.live_1_regdate is not null THEN a.live_1_regdate END ASC";
		$sql .= " limit ".$startnum.", ".$pagesize;
	}else if($category){
		//회원전체
		$sql = "select a.idx, a.email, a.name, a.part,a.gender, a.partno, a.profile_type, a.profile_img_idx, a.live_1, a.live_2, a.live_3, a.live_4, a.memo, DATE_FORMAT(a.live_1_regdate, '%H:%i') as live_1_time, a.live_1_regdate, a.penalty_state";
		$sql .= " from work_member a USE INDEX(state)"; 
		$sql .= " left join work_todaywork b on a.email = b.email "; 
		$sql .= " left join work_member_zzim_list c on a.email = c.email";
		$sql .= " where a.state='0'";
		$sql .= " and b.state = '0' and a.companyno='".$companyno."' $where_date and b.notice_flag = '0' $where_cate";
		$sql .= $where_c.$where_search;
		$sql .= " group by a.name";
		$sql .= " order by ";
		$sql .= " CASE WHEN a.email='".$user_id."' THEN a.email END DESC,";
		$sql .= " CASE WHEN c.state = '1' and c.email = '".$user_id."'  THEN c.idx END DESC,";
		$sql .= " CASE WHEN a.partno='".$user_part."' then  a.partno END DESC";
		$sql .= " limit ".$startnum.", ".$pagesize;
	}else{
		$sql = "select a.idx, a.email, a.name, a.part, a.gender, a.partno, a.profile_type, a.profile_img_idx, a.live_1, a.live_2, a.live_3, a.live_4, a.memo, a.memo_editdate, DATE_FORMAT(a.live_1_regdate, '%H:%i') as live_1_time, a.live_1_regdate, a.penalty_state";
		$sql .= " from work_member a USE INDEX(state)"; 
		$sql .= " left join work_member_zzim_list c on a.idx = c.mem_idx";
		$sql .= " where a.state='0' $where_zzim";
		$sql .= " and a.companyno='".$companyno."' $where_date";
		$sql .= $where_c.$where_search_d;
		$sql .= " group by a.name";
		$sql .= " order by ";
		$sql .= " CASE WHEN a.email='".$user_id."' THEN a.email END DESC,";
		$sql .= " CASE WHEN c.state = '1' and c.email = '".$user_id."'  THEN c.idx END DESC,";
		// $sql .= " CASE WHEN a.partno='".$user_part."' then  a.partno END DESC";
		$sql .= " CASE WHEN a.memo is not null and a.memo != '' then  a.email END DESC,";
		$sql .= " CASE WHEN a.live_1_regdate is not null THEN a.live_1_regdate END ASC";
		$sql .= " limit ".$startnum.", ".$pagesize;
	}
	$member_list_info = selectAllQuery($sql);
	

	// 지나간 업무 update
	$sql = "select idx, email , state, work_flag, decide_flag, work_stime, work_etime from work_todaywork use index(state) where state='0' and companyno='".$companyno."' and decide_flag != '0' and share_flag!='2' and workdate='".TODATE."'";
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
					$sql = "update work_todaywork set state = '1' where idx = '".$works_up_idx."' and email = '".$works_up_email."' and decide_flag = '".$works_up_decide."'";	
					$up_decide = updateQuery($sql);
				}
			}
		}
	}
	// 업무일정
	$sql = "select idx, email, work_flag, decide_flag, count(1) as cnt, work_stime, work_etime from work_todaywork use index(state) where state='0' and companyno='".$companyno."' and share_flag!='2' and decide_flag != '0' and workdate='".TODATE."' group by email, state, work_flag, decide_flag";
	$works_myinfo = selectAllQuery($sql);
	
	if($works_myinfo['email']){
		for($i=0; $i<count($works_myinfo['email']); $i++){

			$works_myinfo_email = $works_myinfo['email'][$i];
			$work_list[TODATE][$works_myinfo_email][$works_myinfo['state'][$i]] = $works_myinfo['cnt'][$i];
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


	//관리권한은 제외처리
	if($user_level == 1){
		$where_sql = $sql .= " and a.email !='".$user_id."'";
	}

	//좋아요 갯수
	$sql = "select email, count(1) as cnt from work_todaywork_like where state='0' and companyno='".$companyno."' and workdate between '".$month_first_day."' and '".$month_last_day."' group by email";
	$like_info = selectAllQuery($sql);
	for($i=0; $i<count($like_info['email']); $i++){
		$link_info_email = $like_info['email'][$i];
		$link_info_cnt = $like_info['cnt'][$i];
		$like_list[$link_info_email] = $link_info_cnt;
	}

	//오늘업무 제일 많이 씀
	$sql = "select email, name, count(1) as cnt from work_todaywork where state!='9' and companyno='".$companyno."' and share_flag!='2' and notice_flag='0' and workdate='".TODATE."'";
	$sql = $sql .= " group by email, name order by count(1) desc limit 1";
	$works_top_info = selectQuery($sql);
	if($works_top_info['email']){
		$works_top_arr[TODATE] = $works_top_info['email'];
	}

	//오늘 출근 제일 빨리 함
	$sql = "select email, live_1_regdate from work_member where state='0' and companyno='".$companyno."' and live_1='1' order by live_1_regdate asc limit 1";
	$mem_top_info = selectQuery($sql);
	if($mem_top_info['email']){
		$mem_top_arr[TODATE] = $mem_top_info['email'];
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

		$reward_cp_info[$work_cp_reward_info_sort][$work_cp_reward_info_kind][$work_cp_reward_info_day_flag]['memo'] = $work_cp_reward_info_memo;
		$reward_cp_info[$work_cp_reward_info_sort][$work_cp_reward_info_kind][$work_cp_reward_info_day_flag]['cnt'] = $work_cp_reward_info_cnt;

		$reward_cp_info2[$work_cp_reward_info_sort]['memo'] = $work_cp_reward_info_memo;
		$reward_cp_info2[$work_cp_reward_info_sort]['cnt'] = $work_cp_reward_info_cnt;

	}

	//역량지표 갯수
	$sql = "SELECT email, SUM(type1) AS type1, SUM(type2) AS type2, SUM(type3) AS type3, SUM(type4) AS type4, SUM(type5) AS type5, SUM(type6) AS type6
			FROM work_cp_reward_list WHERE
				state = '0'
				AND companyno = ?
				AND workdate BETWEEN ? AND ?
			GROUP BY email";
			$stmt = $conn->prepare($sql);
			$stmt->bind_param("sss", $companyno, $month_first_day, $month_last_day);
			$stmt->execute();
			$result = $stmt->get_result();
			$reward_cp_sum = [];
			while ($row = $result->fetch_assoc()) {
				$reward_email = $row['email'];
				$reward_type1 = $row['type1'];
				$reward_type2 = $row['type2'];
				$reward_type3 = $row['type3'];
				$reward_type4 = $row['type4'];
				$reward_type5 = $row['type5'];
				$reward_type6 = $row['type6'];

				$reward_type_sum = $reward_type1 + $reward_type2 + $reward_type3 + $reward_type4 + $reward_type5 + $reward_type6;
				$reward_cp_sum[$reward_email] = $reward_type_sum;
			}
			$stmt->close();



	//회원 전체 리스트
	if($member_list_info['idx']){

		for($i=0; $i<count($member_list_info['idx']); $i++){

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

			$member_list_idx = $member_list_info['idx'][$i];
			$member_list_email = $member_list_info['email'][$i];
			$gender = $member_list_info['gender'][$i];

			$member_list_name = $member_list_info['name'][$i];
			$member_list_part = $member_list_info['part'][$i];
			$member_list_memo = $member_list_info['memo'][$i];
			$member_list_penalty = $member_list_info['penalty_state'][$i];
	
			$member_list_live_1 = $member_list_info['live_1'][$i];
			$member_list_live_2 = $member_list_info['live_2'][$i];
			$member_list_live_3 = $member_list_info['live_3'][$i];
			$member_list_live_4 = $member_list_info['live_4'][$i];

			$profile_type = $member_list_info['profile_type'][$i];
			$profile_img_idx = $member_list_info['profile_img_idx'][$i];

			//프로필 케릭터 사진
			$profile_img_src = profile_img_info($member_list_email);



			$member_work0 = $work_live[$member_list_email][0];
			$member_work1 = $work_live[$member_list_email][1];
			$member_work_all = $member_work0 + $member_work1;

			if($gender=='0'){
				$img_prof_no = rand(1,2);
			}else if($gender=='1'){
				$img_prof_no = rand(3,4);
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

			//오늘 출근 제일 빨리함
			if(!$like_flag_list[$member_list_email][1][0] && $mem_top_arr[TODATE] == $member_list_email){
				$like_jjim++;
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

			<li class="ldr_li<?=$user_id==$member_list_email?" ldr_me":""?><?=$member_list_live_1=='0'?" live_none":""?>">
				<div class="ldr_li_in">
					<div class="ldr_function">
						<div class="ldr_function_in">
							<?if($user_id!=$member_list_email){?>
								<button class="star_only mem_jjim_only<?=$mem_class_on?>" <?=$user_id!=$member_list_email && $ldr_li==false?"id='mem_jjim_only_".$member_list_idx."'":""?> value="<?=$member_list_idx?>"><span>즐겨찾기</span></button>
							<?}?>

							<?if($user_id!=$member_list_email){?>
								<button class="ldr_menu<?=$like_jjim?"":" jjim_only"?><?=$like_class_on?>"<?=$user_id!=$member_list_email && $ldr_li==false?" id='jjim_only_".$member_list_idx."'":""?> value="<?=$member_list_idx?>"><span>좋아요</span></button>
							<?}?>
						</div>
					</div>

					<?if($user_id != $member_list_email){?>
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
									<div class="ldr_user_imgs" id="ldr_user_imgs_<?=$member_list_idx?>" style="background-image:url(<?=$profile_img_src?>);"></div>
									<?if($member_list_penalty=='1'){?>
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
					<div class="ldr_anno">
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
						<div class="ldr_today<?= $member_work_all > 0 ? "" : " today_num_none" ?>" id="ldr_today">
							<button class="ldr_today_num" value="<?= $member_list_email ?>">
								<strong><?= number_format($member_work_all) ?></strong>
								<span>업무</span>
								<input type="hidden" id="ldr_today_num" value="<?= number_format($member_work1) ?>">
							</button>
						</div>

						<div class="ldr_chall<?= $reward_cp_sum[$member_list_email] > 0 ? "" : " challenges_num_none" ?>">
							<button class="ldr_chall_num" value="<?= $member_list_idx ?>">
								<strong><?= $reward_cp_sum[$member_list_email] > 0 ? $reward_cp_sum[$member_list_email] : "0" ?></strong>
								<span>역량</span>
							</button>
						</div>

						<div class="ldr_jjim<?= $like_list[$member_list_email] > 0 ? "" : " jjim_num_none" ?>">
							<button class="ldr_jjim_num" id="ldr_jjim_num_<?= $member_list_idx ?>" value="<?= $member_list_idx ?>">
								<strong><?= $like_list[$member_list_email] ? $like_list[$member_list_email] : "0" ?></strong>
								<span>좋아요</span>
							</button>
						</div>
					</div>
				</div>
			</li><?php }?>|<?=$member_all_cnt?>|<?=$member_live_cnt?>|<?=$page_count?><?php
	}else{?>
		<div class="tdw_list_none">
			<strong><span>현재 검색된 결과가 없습니다.</span></strong>
		</div>|0|0<?php
	}

	exit;
}




//업무시작
if($mode == "live_1_change"){
	$status = $_POST['status'];
	
	$sql = "select idx, live_1, name, live_1_regdate, DATE_FORMAT(live_1_regdate, '%Y-%m-%d') as live_1_workdate, penalty_state from work_member where email = '".$user_id."' and companyno = '".$companyno."' and state = '0'";
	$member = selectQuery($sql);

	//업무시작 ON
	if($status == "on"){
		if($member['live_1_workdate'] != TODATE){ // 하루 최초 출근시 기록
			if($member['penalty_state'] == '1'){
				$sql = "update work_member set penalty_state = '0' where email = '".$user_id."' and state = '0'";
				$query = updateQuery($sql); // 페널티가 있으면 초기화
			}

			$sql = "update work_member set live_1='1', live_1_regdate=".DBDATE." where idx='".$member['idx']."'";
			$up = updateQuery($sql); // live_1_regdate 최초 등록

			// 페널티 함수 사용
			member_penalty_add();

			if($up){
				//역량 평가 지표 처리(live 업무시작, 0001, 회원idx)
				work_cp_reward("live","0001", $user_id, $member['idx']);

				echo "|true|";
				exit;
			}
		}else{
			$sql = "update work_member set live_1='1', live_4='0' where idx='".$member['idx']."'";
			$up = updateQuery($sql);
			if($up){
				//역량 평가 지표 처리(live 업무시작, 0001, 회원idx)
				work_cp_reward("live","0001", $user_id, $member['idx']);

				echo "|||||true|";
				exit;
			}
		}
	//업무시작 OFF
	}else if($status == "off"){
		$sql = "select idx, live_1 from work_member where state='0' and companyno='".$companyno."' and email='".$user_id."'";
		$mem_info = selectQuery($sql);
		if($mem_info['idx']){
			$sql = "update work_member set live_1='0', live_4='0' where idx='".$mem_info['idx']."'";
			$up = updateQuery($sql);
			if($up){
				echo "|||||true|";
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
		$sql = "select idx, live_1, live_1_regdate from work_member where state='0' and companyno='".$companyno."' and email='".$user_id."'";
		$mem_info = selectQuery($sql);
		if($mem_info['idx']){

			if($mem_info['live_1_regdate'] == null){
				$up_query = ", live_1_regdate=".DBDATE."";
			}

			$sql = "update work_member set live_1='1', live_2='1', live_3='0', live_4='0', live_2_regdate=".DBDATE.", live_3_regdate=null, live_4_regdate=null ".$up_query." where idx='".$mem_info['idx']."'";
			$up = updateQuery($sql);
			if($up){

				//역량 평가 지표 처리(live 업무시작, 0001, 회원idx)
				work_cp_reward("live","0002", $user_id, $mem_info['idx']);

				echo "true|";
				exit;
			}
		}

	//집중모드 OFF
	}else if($status == "false"){
		$sql = "select idx, live_1, live_1_regdate from work_member where state='0' and companyno='".$companyno."' and email='".$user_id."'";
		$mem_info = selectQuery($sql);
		if($mem_info['idx']){

			if($mem_info['live_1_regdate'] == null){
				$up_query = ", live_1_regdate=".DBDATE."";
			}

			$sql = "update work_member set live_1='1', live_2='0', live_3='0', live_4='0', live_2_regdate=null, live_3_regdate=null, live_4_regdate=null ".$up_query." where idx='".$mem_info['idx']."'";
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
				//역량 평가 지표 처리(live 업무시작, 0001, 회원idx)
				work_cp_reward("live","0003", $user_id, $mem_info['idx']);

				echo "true|";
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
				echo "true|";
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


//라이브 댓글 리스트
//요청업무글, 일반업무글 나누어서 리스트 출력됨
if($mode == "live_comment"){

	//날짜
	$lives_date = $_POST['lives_date'];
	if($lives_date){
		if(strpos($lives_date, ".") !== false){
			$lives_date_ch = $lives_date;
			$lives_date = str_replace(".", "-",$lives_date);
			$wdate = $lives_date;
		}else{
			$wdate = $lives_date;
		}
	}

	$idx = $_POST['idx'];
	$idx = preg_replace("/[^0-9]/", "", $idx);
	
	if($idx){

		$sql = "select idx, work_idx from work_todaywork where state!='9' and idx='".$idx."' and workdate='".$wdate."'";
		$work_info = selectQuery($sql);
		if($work_info['idx']){

			//요청받은글,공유글
			if($work_info['work_idx']){
				$work_idx = $work_info['work_idx'];
				$sql = "select a.idx as cidx, a.link_idx, a.work_idx, a.email, a.name, a.comment, CASE WHEN a.editdate is not null then DATE_FORMAT(a.editdate, '%Y-%m-%d') WHEN a.editdate is null then DATE_FORMAT(a.regdate, '%Y-%m-%d') end as ymd,";
				$sql = $sql .=" CASE WHEN a.editdate is not null then DATE_FORMAT(a.editdate, '%Y-%m-%d %H:%i:%s') WHEN a.editdate is null then DATE_FORMAT(a.regdate, '%Y-%m-%d %H:%i:%s') end as regdate, b.idx from work_todaywork_comment as a left join work_todaywork as b on(a.link_idx=b.idx) where a.state=0 and a.link_idx='".$work_info['work_idx']."' and b.workdate='".$wdate."' order by a.regdate desc";
				$comment_info = selectAllQuery($sql);

				for($i=0; $i<count($comment_info['idx']); $i++){
					$comment_info_idx = $comment_info['cidx'][$i];
					$comment_info_link_idx = $comment_info['link_idx'][$i];
					$comment_info_work_idx = $comment_info['work_idx'][$i];
					$comment_info_email = $comment_info['email'][$i];
					$comment_info_name = $comment_info['name'][$i];
					$comment_info_comment = $comment_info['comment'][$i];
					$comment_info_ymd = $comment_info['ymd'][$i];
					$comment_info_regdate = $comment_info['regdate'][$i];
					$comment_info_comment_strip = strip_tags($comment_info['comment'][$i]);

					$comment_list[$comment_info_link_idx]['cidx'][] = $comment_info_idx;
					$comment_list[$comment_info_link_idx]['name'][] = $comment_info_name;
					$comment_list[$comment_info_link_idx]['email'][] = $comment_info_email;
					$comment_list[$comment_info_link_idx]['comment'][] = $comment_info_comment;
					$comment_list[$comment_info_link_idx]['ymd'][] = $comment_info_ymd;
					$comment_list[$comment_info_link_idx]['regdate'][] = $comment_info_regdate;
					$comment_list[$comment_info_link_idx]['comment_strip'][] = $comment_info_comment_strip;

					//$comment_list[$work_idx]['email'][$k]
				}

			}else{


				//업무 원본 리스트
				$sql = "select a.idx as cidx, a.link_idx, a.work_idx, a.email, a.name, a.comment, CASE WHEN a.editdate is not null then DATE_FORMAT(a.editdate, '%Y-%m-%d') WHEN a.editdate is null then DATE_FORMAT(a.regdate, '%Y-%m-%d') end as ymd,";
				$sql = $sql .=" CASE WHEN a.editdate is not null then DATE_FORMAT(a.editdate, '%Y-%m-%d %H:%i:%s') WHEN a.editdate is null then DATE_FORMAT(a.regdate, '%Y-%m-%d %H:%i:%s') end as regdate, b.idx from work_todaywork_comment as a left join work_todaywork as b on(a.link_idx=b.idx) where a.state=0 and a.link_idx='".$work_info['idx']."' and b.workdate='".$wdate."' order by a.regdate desc";
				$comment_info = selectAllQuery($sql);
				$work_idx = $work_info['idx'];

				for($i=0; $i<count($comment_info['idx']); $i++){
					$comment_info_idx = $comment_info['cidx'][$i];
					$comment_info_link_idx = $comment_info['link_idx'][$i];
					$comment_info_work_idx = $comment_info['work_idx'][$i];
					$comment_info_email = $comment_info['email'][$i];
					$comment_info_name = $comment_info['name'][$i];
					$comment_info_ymd = $comment_info['ymd'][$i];
					$comment_info_regdate = $comment_info['regdate'][$i];
					$comment_info_comment = $comment_info['comment'][$i];
					$comment_info_comment_strip = strip_tags($comment_info['comment'][$i]);

					$comment_list[$comment_info_link_idx]['cidx'][] = $comment_info_idx;
					$comment_list[$comment_info_link_idx]['name'][] = $comment_info_name;
					$comment_list[$comment_info_link_idx]['email'][] = $comment_info_email;
					$comment_list[$comment_info_link_idx]['ymd'][] = $comment_info_ymd;
					$comment_list[$comment_info_link_idx]['regdate'][] = $comment_info_regdate;
					$comment_list[$comment_info_link_idx]['comment'][] = $comment_info_comment;
					$comment_list[$comment_info_link_idx]['comment_strip'][] = $comment_info_comment_strip;
				}
			}
		}

		for($k=0; $k<count($comment_list[$work_idx]['cidx']); $k++){
			$comment_idx = $comment_list[$work_idx]['cidx'][$k];

			$chis = $comment_list[$work_idx]['regdate'][$k];
			$ymd = $comment_list[$work_idx]['ymd'][$k];
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

		?>

			<div class="tdw_list_memo_desc" id="comment_list_<?=$comment_idx?>" value="<?=$comment_list[$work_idx]['email'][$k]?>">
				<div class="tdw_list_memo_name"><?=$comment_list[$work_idx]['name'][$k]?></div>
				<div class="tdw_list_memo_conts">

					<?if($user_id==$comment_list[$work_idx]['email'][$k]){?>
						<span class="tdw_list_memo_conts_txt" id="tdw_list_memo_conts_txt_<?=$comment_idx?>"><?=$comment_list[$work_idx]['comment'][$k]?></span>
					<?}else{?>
						<span class="tdw_list_memo_conts_txt"><?=$comment_list[$work_idx]['comment'][$k]?></span>
					<?}?>
						
					<em class="tdw_list_memo_conts_date"><?=$chiss?>

					<?if($user_id!=$comment_list[$work_idx]['email'][$k]){?>
						<button class="btn_memo_jjim"><span>좋아요</span></button>
					<?}?>

					
					</em>

					<!-- <div class="tdw_list_memo_regi" id="tdw_list_memo_regi_<?=$comment_idx?>">
						<textarea name="" class="textarea_regi" id="tdw_comment_edit_<?=$comment_idx?>"><?=strip_tags($comment_list[$work_idx]['comment_strip'][$k])?></textarea>
						<div class="btn_regi_box">
							<button class="btn_regi_submit" id="btn_comment_submit" value="<?=$comment_idx?>"><span>확인</span></button>
							<button class="btn_regi_cancel" id="btn_regi_cancel" value="<?=$comment_idx?>"><span>취소</span></button>
						</div>
					</div> -->
				</div>
			</div>

		<?php
		}
	}
	exit;
}


//페널티 리스트
if($mode == "penalty_list"){

	$lives_date = $_POST['lives_date'];

	//날짜 변경
	if($lives_date){
		if(strpos($lives_date, ".") !== false){
			$lives_date_ch = $lives_date;
			$lives_date = str_replace(".", "-",$lives_date);
		}
	}

	$penalty_list = array();
	$week_day = week_day(TODATE);
	$sql = "select idx, kind_flag, email, name, workdate, DATE_FORMAT(comdate, '%Y-%m-%d %H:%i:%s') as comdate, DATE_FORMAT(regdate, '%Y-%m-%d %H:%i:%s') as regdate from work_penalty_list_log where state='0' and workdate='".$lives_date."' order by idx desc";
	$penalty_info = selectAllQuery($sql);
	if($penalty_info['idx']){
		for($i=0; $i<count($penalty_info['idx']); $i++){
			$penalty_info_idx = $penalty_info['idx'][$i];
			$penalty_info_kind_flag = $penalty_info['kind_flag'][$i];
			$penalty_info_email = $penalty_info['email'][$i];
			$penalty_info_name = $penalty_info['name'][$i];
			$penalty_info_workdate = $penalty_info['workdate'][$i];
			$penalty_info_comdate = $penalty_info['comdate'][$i];
			$penalty_info_regdate = $penalty_info['regdate'][$i];

			//경고 1회일때
			if ($penalty_list[$penalty_info_email] == '1'){
				$penalty_info_comdate = $penalty_info_regdate;
			}else{
				if($penalty_info_comdate){
					$penalty_info_comdate = $penalty_info_comdate;
				}else if($penalty_info_regdate){
					$penalty_info_comdate = $penalty_info_regdate;
				}else{
					$penalty_info_comdate = "미참여";
				}
			}
			if($penalty_info_kind_flag=='0'){
				$kind_flag='지각 페널티';
			}else if($penalty_info_kind_flag=='1'){
				$kind_flag='오늘업무 페널티';
			}else if($penalty_info_kind_flag=='2'){
				$kind_flag='퇴근 페널티';
			}
		?>
			<li>
				<span class="rew_ard_desc_date"><?=$penalty_info_name?></span>
				<span class="rew_ard_desc_tit"><?=$kind_flag?></span>
				<span class="rew_ard_desc_coin"><?=$penalty_info_workdate?></span>
				<span class="rew_ard_desc_type"><?=$penalty_info_comdate?></span>
			</li>
		<?php
		}
		echo "|".count($penalty_info['idx']);

	}else{?>

		<li>
			<strong><span>페널티 내역이 없습니다.</span></strong>
		</li>|0

	<?php
	}
	exit;
}


//페널티 정보 가져오기
if($mode == "penalty_member"){

	$penalty_idx = $_POST['penalty_idx'];
	$penalty_idx = preg_replace("/[^0-9]/", "", $penalty_idx);
	if($penalty_idx){

		$sql = "select idx, email from work_penalty_list where state='0' and idx='".$penalty_idx."'";
		$penalty_mem_info = selectQuery($sql);
		if($penalty_mem_info['idx']){
			$member_id = $penalty_mem_info['email'];
			$member_row_info = member_row_info($member_id);
			if($member_row_info['idx']){
				echo $member_row_info['name']."|".$member_row_info['profile_img_src']."|".$member_row_info['part'];
				exit;
			}

		}

	}

}




if($mode == "jg_graph_list"){
	/*print "<pre>";
	print_r($_POST);
	print "</pre>";*/

	$curYear = (int)date('Y');
	$curMonth = (int)date('m');
	$month_first_day = date("Y-m-d", mktime(0, 0, 0, $curMonth , 1, $curYear));
	$month_last_day = date("Y-m-d", mktime(0, 0, 0, $curMonth+1 , 0, $curYear));

	$send_userid = $_POST['send_userid'];
	$sql = "select email, kind_flag, count(1) as cnt from work_todaywork_like where state='0' and email='".$send_userid."' and workdate between '".$month_first_day."' and '".$month_last_day."' group by email, kind_flag";
	$like_info = selectAllQuery($sql);
	if($like_info['email']){
		for($i=0; $i<count($like_info['email']); $i++){
			$kind_flag = $like_info['kind_flag'][$i];
			$cnt = $like_info['cnt'][$i];
			$like_graph[$kind_flag] = $cnt;
			$like_graph['hap'][] =+ $cnt;
		}
	?>
		<li class="jg_g01">
			<span><?=$like_graph[6]?$like_graph[6]:0?></span>
			<strong></strong>
		</li>
		<li class="jg_g02">
			<span><?=$like_graph[3]?$like_graph[3]:0?></span>
			<strong></strong>
		</li>
		<li class="jg_g03">
			<span><?=$like_graph[5]?$like_graph[5]:0?></span>
			<strong></strong>
		</li>
		<li class="jg_g04">
			<span><?=$like_graph[4]?$like_graph[4]:0?></span>
			<strong></strong>
		</li>
		<li class="jg_g05">
			<span><?=$like_graph[1]?$like_graph[1]:0?></span>
			<strong></strong>
		</li>
		<li class="jg_g06">
			<span><?=$like_graph[2]?$like_graph[2]:0?></span>
			<strong></strong>
		</li>|<?=array_sum($like_graph['hap'])?>
		<?php
	}else{?>

		<li class="jg_g01">
			<span>0</span>
			<strong></strong>
		</li>
		<li class="jg_g02">
			<span>0</span>
			<strong></strong>
		</li>
		<li class="jg_g03">
			<span>0</span>
			<strong></strong>
		</li>
		<li class="jg_g04">
			<span>0</span>
			<strong></strong>
		</li>
		<li class="jg_g05">
			<span>0</span>
			<strong></strong>
		</li>
		<li class="jg_g06">
		<span>0</span>
			<strong></strong>
		</li>|0
	<?php
	}
	exit;
}


//좋아요 상세보기 리스트
if($mode == "jt_table_list"){

	$curYear = (int)date('Y');
	$curMonth = (int)date('m');
	$month_first_day = date("Y-m-d", mktime(0, 0, 0, $curMonth , 1, $curYear));
	$month_last_day = date("Y-m-d", mktime(0, 0, 0, $curMonth+1 , 0, $curYear));
	$send_userid = $_POST['send_userid'];

	if($send_userid){

		?>
			<div class="jt_table_area_in">
				<div class="jt_table_head">
					<div class="jt_table_01"><span>카테고리</span></div>
					<div class="jt_table_02"><span>구분</span></div>
					<div class="jt_table_03"><span>내용</span></div>
					<div class="jt_table_04"><span>보낸이</span></div>
					<div class="jt_table_05"><span>일시</span></div>
				</div>
				<div class="jt_table_body">
					<ul>
		<?php


		//카테고리명
		$sql = "select idx, name from work_category where state='0' order by rank asc";
		$work_cate_info = selectAllQuery($sql);
		for($i=0; $i<count($work_cate_info['idx']); $i++){
			$work_cate_idx = $work_cate_info['idx'][$i];
			$work_cate_name = $work_cate_info['name'][$i];
			$work_cate_title[$work_cate_idx] = $work_cate_name;
		}


		$sql = "select a.idx, a.email, a.kind_flag, a.service, a.comment, a.name, a.send_email, a.send_name, DATE_FORMAT(a.regdate, '%Y-%m-%d %H:%i') as reg,
				b.part, c.file_path, c.file_name
				from work_todaywork_like a use index(state)
				left join work_member b on a.send_email = b.email
				left join work_member_profile_img c on a.send_email = c.email  
				where 1=1 and a.state='0' and a.email='".$send_userid."' and a.workdate between '".$month_first_day."' and '".$month_last_day."' order by idx desc";
		//and kind_flag='".$kind_flag."'
		$like_info = selectAllQuery($sql);
		if($like_info['idx']){
			for($i=0; $i<count($like_info['idx']); $i++){

				$kind_flag = $like_info['kind_flag'][$i];
				$service = $like_info['service'][$i];
				$comment = $like_info['comment'][$i];
				$email = $like_info['send_email'][$i];
				$send_name = $like_info['send_name'][$i];
				$reg = $like_info['reg'][$i];
				$user_part = $like_info['part'][$i];
				$profile_check = $like_info['file_path'][$i].$like_info['file_name'][$i];
				$profile_img = 'http://demo.rewardy.co.kr'.$like_info['file_path'][$i].$like_info['file_name'][$i];

				if(strpos($reg, "-") !== false) {
					$reg = str_replace("-", ".", $reg);
				}



				//카테고리
				$service_txt = $service_title_arr[$service];

				$kind_flag_txt = $work_cate_title[$kind_flag];

				/*if($kind_flag == 1){
					$kind_flag_txt = "에너지";
				}else if($kind_flag == 2){
					$kind_flag_txt = "응원하기";
				}else if($kind_flag == 3){
					$kind_flag_txt = "인정하기";
				}else if($kind_flag == 4){
					$kind_flag_txt = "칭찬하기";
				}else if($kind_flag == 5){
					$kind_flag_txt = "열정하기";
				}else if($kind_flag == 6){
					$kind_flag_txt = "감사하기";
				}*/
			?>

				<li>
					<input type="hidden" value="<?php echo $user_part?>" class="table_part">
					<input type="hidden" value="<?=$profile_check?$profile_img:"/html/images/pre/img_prof_default.png"?>" class="table_img">
					
					<div class="jt_table_01"><span><?=$service_txt?></span></div>
					<div class="jt_table_02"><span><?=$kind_flag_txt?></span></div>
					<div class="jt_table_03"><span><?=$comment?></span></div>
					<div class="jt_table_04" value="<?php echo $email?>"><span><?=$send_name?></span></div>
					<div class="jt_table_05"><span><?=$reg?></span></div>
				</li>
			<?php
			}?>


				</ul>
			</div>
		</div>|<?=count($like_info['idx'])?>
		<?php
		}else{?>
			<li>
				<div class="jt_area_none"><span>좋아요 내역이 없습니다.</span></div>
			</li>|0
		<?php
		}
	}

	exit;
}


//프로젝트 추가
if($mode == "project_add"){

	$lt_id = $_POST['lt_id'];
	$lt_text = $_POST['lt_text'];
	$lt_id_info = member_row_info($lt_id);

	//$sql = "select a.idx from work_todaywork_project as a left join work_todaywork_project_user as b on(a.idx=b.project_idx)";
	//$sql = $sql .= " where a.state='0' and a.email='".$user_id."' and b.email='".$lt_id."' and a.title='".$lt_text."'";


	$sql = "select idx from work_todaywork_project where state='0' and companyno='".$companyno."' and title='".$lt_text."' and email='".$user_id."'";
	$project_info = selectQuery($sql);
	if(!$project_info['idx']){

		//파티
		$sql = "select max(idx)+1 as maxnum from work_todaywork_project";
		$project_max_info = selectQuery($sql);
		$project_maxno = $project_max_info['maxnum'];
		$party_link = date("His").$project_maxno;

		$sql = "insert into work_todaywork_project(companyno, part_flag, email, name, part, title, ip, party_link)";
		$sql = $sql .= " values('".$companyno."','".$user_part."','".$user_id."','".$user_name."','".$part_name."','".$lt_text."','".LIP."', '".$party_link."')";
		$project_insert_idx = insertIdxQuery($sql);
		if($project_insert_idx){

			//회원전체 리스트
			$member_list_info = member_alist_info();
			for($i=0; $i<count($member_list_info['idx']); $i++){
				$mem_idx = $member_list_info['idx'][$i];
				$mem_id = $member_list_info['email'][$i];
				$mem_name = $member_list_info['name'][$i];
				$mem_part = $member_list_info['part'][$i];
				$mem_partno = $member_list_info['partno'][$i];

				//회원별 프로젝트 추가
				$sql = "select email from work_todaywork_project_sort where state='0' and companyno='".$companyno."' and project_idx='".$project_insert_idx."' and email='".$mem_id."'";
				$project_user_info = selectQuery($sql);
				if(!$project_user_info['idx']){
					$sql = "insert into work_todaywork_project_sort(project_idx, companyno, part_flag, email, name, part, ip)";
					$sql = $sql .= " values('".$project_insert_idx."','".$companyno."','".$mem_partno."','".$mem_id."','".$mem_name."','".$mem_part."','".LIP."')";
					$insert_idx = insertIdxQuery($sql);
				}
			}

			//본인데이터
			$sql = "select idx from work_todaywork_project_user where state='0' and project_idx='".$project_insert_idx."' and email='".$user_id."'";
			$user_info = selectQuery($sql);
			if(!$user_info['idx']){
				$sql = "insert into work_todaywork_project_user(project_idx, email, name, companyno, part_flag, part, ip) values(";
				$sql = $sql .= " '".$project_insert_idx."','".$user_id."','".$user_name."','".$companyno."','".$user_part."','".$part_name."','".LIP."')";
				$insert_idx_my = insertIdxQuery($sql);
			}

			//파티원 데이터
			$sql = "select idx from work_todaywork_project_user where state='0' and project_idx='".$project_insert_idx."' and email='".$lt_id."'";
			$user_info = selectQuery($sql);
			if(!$user_info['idx']){
				$sql = "insert into work_todaywork_project_user(project_idx, email, name, companyno, part_flag, part, ip) values(";
				$sql = $sql .= " '".$project_insert_idx."','".$lt_id_info['email']."','".$lt_id_info['name']."','".$lt_id_info['companyno']."','".$lt_id_info['partno']."','".$lt_id_info['part']."','".LIP."')";
				$insert_idx_part = insertIdxQuery($sql);
			}

			if($insert_idx_my && $insert_idx_part){
				$project_profile_img1 = profile_img_info($user_id);
				$project_profile_img2 = profile_img_info($lt_id);

				//타임라인(파티만들기)
				work_data_log('0','17', $project_insert_idx, $user_id, $user_name);

				$html = '';
				$html = $hml .= '<div class="ldl_box" id="listsort_'.$project_insert_idx.'">';
				$html = $hml .= '	<div class="ldl_box_in">';
				$html = $hml .= '		<button class="ldl_box_close" id="ldl_box_close_all_'.$project_insert_idx.'" value="'.$project_insert_idx.'">닫기</button>';
				$html = $hml .= '		<div class="ldl_box_tit"><p>'.$lt_text.'</p></div>';
				$html = $hml .= '		<div class="ldl_box_time" id="ldl_box_time_'.$project_insert_idx.'">최신 업데이트</div>';
				$html = $hml .= '		<div class="ldl_box_user">';
				$html = $hml .= '			<ul>';
				$html = $hml .= '				<li class="ldl_me">';
				$html = $hml .= '					<div class="ldl_box_img" style="background-image:url('.$project_profile_img1.')"></div>';
				$html = $hml .= '					<div class="ldl_box_user"><strong>'.$user_name.'</strong><span>'.$part_name.'</span></div>';
				$html = $hml .= '				</li>';
				$html = $hml .= '				<li>';
				$html = $hml .= '					<div class="ldl_box_img" style="background-image:url('.$project_profile_img2.')"></div>';
				$html = $hml .= '					<div class="ldl_box_user"><strong>'.$lt_id_info['name'].'</strong><span>'.$lt_id_info['part'].'</span></div>';
				$html = $hml .= '				</li>';
				$html = $hml .= '			</ul>';
				$html = $hml .= '		</div>';

				$html = $hml .= '		<button class="ldl_box_out" id="ldl_box_out_'.$project_insert_idx.'" value="'.$project_insert_idx.'" style="display:none;">';
				$html = $hml .= '			<span>파티에서 나가기</span>';
				$html = $hml .= '		</button>';
				$html = $hml .= '	</div>';
				$html = $hml .= '</div>';

				$result = "complete";
				echo $result."|".$html;
			}

			exit;
		}

	}else{

		echo "같은 이름의 파티명이 있습니다.";
		exit;
	}
	exit;

	$sql = "select count(a.idx) from work_todaywork_project as a left join work_todaywork_project_sort as b on(a.idx=project_idx) where a.state='0'";
	$sql = $sql .=" and b.email='".$user_id."'";
	$project_info = selectQuery($sql);
	if(!$project_info['cnt']){
		$sql = "insert into work_todaywork_project(companyno, part_flag, email, name, part, title, ip)";
		$sql = $sql .= " values('".$companyno."','".$user_part."','".$user_id."','".$user_name."','".$part_name."','".$lt_text."','".LIP."')";
		$project_insert_idx = insertIdxQuery($sql);
		if($project_insert_idx){

			$sql = "insert into work_todaywork_project_sort(project_idx, companyno, part_flag, email, name, part, ip)";
			$sql = $sql .= " values('".$project_insert_idx."','".$companyno."','".$user_part."','".$user_id."','".$user_name."','".$part_name."','".LIP."')";
			insertIdxQuery($sql);

			//본인데이터
			$sql = "select idx from work_todaywork_project_user where state='0' and project_idx='".$project_insert_idx."' and email='".$user_id."'";
			$user_info = selectQuery($sql);
			if(!$user_info['idx']){
				$sql = "insert into work_todaywork_project_user(project_idx, email, name, companyno, part_flag, part, ip) values(";
				$sql = $sql .= " '".$project_insert_idx."','".$user_id."','".$user_name."','".$companyno."','".$user_part."','".$part_name."','".LIP."')";
				$insert_idx = insertIdxQuery($sql);
			}

			//파티원 데이터
			$sql = "select idx from work_todaywork_project_user where state='0' and project_idx='".$project_insert_idx."' and email='".$lt_id."'";
			$user_info = selectQuery($sql);
			if(!$user_info['idx']){
				$sql = "insert into work_todaywork_project_user(project_idx, email, name, companyno, part_flag, part, ip) values(";
				$sql = $sql .= " '".$project_insert_idx."','".$lt_id_info['email']."','".$lt_id_info['name']."','".$lt_id_info['companyno']."','".$lt_id_info['partno']."','".$lt_id_info['part']."','".LIP."')";
				$insert_idx = insertIdxQuery($sql);
				if($insert_idx){
					$result = "complete";
				}
			}
		}

	}else{

		$sql = "insert into work_todaywork_project(companyno, part_flag, email, name, part, title, ip)";
		$sql = $sql .= " values('".$companyno."','".$user_part."','".$user_id."','".$user_name."','".$part_name."','".$lt_text."','".LIP."')";
		$project_insert_idx = insertIdxQuery($sql);
		if($project_insert_idx){

			//본인데이터
			$sql = "select idx from work_todaywork_project_user where state='0' and project_idx='".$project_insert_idx."' and email='".$user_id."'";
			$user_info = selectQuery($sql);
			if(!$user_info['idx']){
				$sql = "insert into work_todaywork_project_user(project_idx, email, name, companyno, part_flag, part, ip) values(";
				$sql = $sql .= " '".$project_insert_idx."','".$user_id."','".$user_name."','".$companyno."','".$user_part."','".$part_name."','".LIP."')";
				$insert_idx = insertIdxQuery($sql);
			}

			//파티원 데이터
			$sql = "select idx from work_todaywork_project_user where state='0' and project_idx='".$project_insert_idx."' and email='".$lt_id."'";
			$user_info = selectQuery($sql);
			if(!$user_info['idx']){
				$sql = "insert into work_todaywork_project_user(project_idx, email, name, companyno, part_flag, part, ip) values(";
				$sql = $sql .= " '".$project_insert_idx."','".$lt_id_info['email']."','".$lt_id_info['name']."','".$lt_id_info['companyno']."','".$lt_id_info['partno']."','".$lt_id_info['part']."','".LIP."')";
				$insert_idx = insertIdxQuery($sql);
			}

			//파티 정렬순서 데이터
			$sql = "insert into work_todaywork_project_sort(project_idx, companyno, part_flag, email, name, part, ip)";
			$sql = $sql .= " values('".$project_insert_idx."','".$companyno."','".$user_part."','".$user_id."','".$user_name."','".$part_name."','".LIP."')";
			$insert_idx = insertIdxQuery($sql);
			if($insert_idx){
				$result = "complete";
			}
		}
	}

	$project_profile_img1 = profile_img_info($user_id);
	$project_profile_img2 = profile_img_info($lt_id);

	$html = '';
	$html = $hml .= '<div class="ldl_box" id="listsort_'.$project_insert_idx.'">';
	$html = $hml .= '	<div class="ldl_box_in">';
	$html = $hml .= '		<button class="ldl_box_close" id="ldl_box_close_all_'.$project_insert_idx.'" value="'.$project_insert_idx.'">닫기</button>';
	$html = $hml .= '		<div class="ldl_box_tit">'.$lt_text.'</div>';
	$html = $hml .= '		<div class="ldl_box_time" id="ldl_box_time_'.$project_insert_idx.'">최신 업데이트</div>';
	$html = $hml .= '		<div class="ldl_box_user">';
	$html = $hml .= '			<ul>';
	$html = $hml .= '				<li class="ldl_me">';
	$html = $hml .= '					<div class="ldl_box_img" style="background-image:url('.$project_profile_img1.')"></div>';
	$html = $hml .= '					<div class="ldl_box_user"><strong>'.$user_name.'</strong><span>'.$part_name.'</span></div>';
	$html = $hml .= '				</li>';
	$html = $hml .= '				<li>';
	$html = $hml .= '					<div class="ldl_box_img" style="background-image:url('.$project_profile_img2.')"></div>';
	$html = $hml .= '					<div class="ldl_box_user"><strong>'.$lt_id_info['name'].'</strong><span>'.$lt_id_info['part'].'</span></div>';
	$html = $hml .= '				</li>';
	$html = $hml .= '			</ul>';
	$html = $hml .= '		</div>';

	$html = $hml .= '		<button class="ldl_box_out" id="ldl_box_out_'.$project_insert_idx.'" value="'.$project_insert_idx.'" style="display:none;">';
	$html = $hml .= '			<span>파티에서 나가기</span>';
	$html = $hml .= '		</button>';
	$html = $hml .= '	</div>';
	$html = $hml .= '</div>';

	echo $result."|".$html;
	exit;
}


//프로젝트 이동
if($mode == "project_move"){

	$project_my = $_POST['project_my'];
	$project_my = preg_replace("/[^0-9]/", "", $project_my);

	if($project_my){
		if($_POST['listsort']){
			$item = explode("&",$_POST['listsort']);
			$i = 1;
			foreach ($item as $value) {
				$value = str_replace("listsort[]=", "", $value);

				//사용자별 정렬값이 있는지 체크
				$sql = "select idx from work_todaywork_project_user where state='0' and email='".$user_id."' and project_idx='".$value."'";
				$project_user_info = selectAllQuery($sql);
				if(!$project_user_info['idx']){
					$sql = "select idx, sort from work_todaywork_project where state='0' order by idx asc";
					$project_info = selectAllQuery($sql);
					for($i=0; $i<count($project_info['idx']); $i++){
						$project_idx = $project_info['idx'][$i];
						$sql = "insert into work_todaywork_project_user(companyno, part_flag, project_idx, email, name, part, ip) values(";
						$sql = $sql .= "'".$companyno."', '".$user_part."', '".$project_idx."','".$user_id."','".$user_name."','".$part_name."','".LIP."')";
						$insert_idx = insertIdxQuery($sql);
					}
				}

				if($_POST['listsort']){
					$item = explode("&",$_POST['listsort']);
					$i = 1;
					foreach ($item as $value) {
						$value = str_replace("listsort[]=", "", $value);
						$sql = "update work_todaywork_project_user set sort='".$i."', editdate=".DBDATE." where email='".$user_id."' and project_idx='".$value."'";
						$up[] = updateQuery($sql);
						$i++;
					}

					if( count($up) == count($item)){
						echo "complete";
						exit;
					}
				}
				exit;
			}
		}

	}else{

		if($_POST['listsort']){
			$item = explode("&",$_POST['listsort']);
			$i = 1;
			foreach ($item as $value) {
				$value = str_replace("listsort[]=", "", $value);

				//사용자별 정렬값이 있는지 체크
				$sql = "select idx from work_todaywork_project_sort where state='0' and email='".$user_id."' and project_idx='".$value."'";
				$project_user_info = selectQuery($sql);
				if(!$project_user_info['idx']){

					//프로젝트 조회
					$sql = "select idx, sort, title from work_todaywork_project where state='0' and idx='".$value."'";
					$project_info = selectQuery($sql);
					if($project_info['idx']){
						$project_idx = $project_info['idx'];

						//사용자별 프로젝트 정렬값 저장
						$sql = "insert into work_todaywork_project_sort(companyno, part_flag, project_idx, email, name, part, ip) values(";
						$sql = $sql .= "'".$companyno."', '".$user_part."', '".$project_idx."','".$user_id."','".$user_name."','".$part_name."','".LIP."')";
						$insert_idx = insertIdxQuery($sql);
					}
				}

				//정렬값 업데이트
				$sql = "update work_todaywork_project_sort set sort='".$i."', editdate=".DBDATE." where email='".$user_id."' and project_idx='".$value."'";
				$up[] = updateQuery($sql);
				$i++;
			}

			if( count($up) == count($item)){
				echo "complete";
				exit;
			}
		}
		exit;

		//}else{
		/*
			$sql = "select idx from work_todaywork_project_sort where state='0' and email='".$user_id."'";
			$project_sort_info = selectAllQuery($sql);
			if(!$project_sort_info['idx']){
				$sql = "select idx, sort from work_todaywork_project where state='0' order by idx asc";
				$project_info = selectAllQuery($sql);
				for($i=0; $i<count($project_info['idx']); $i++){
					$project_idx = $project_info['idx'][$i];
					$sql = "insert into work_todaywork_project_sort(companyno, part_flag, project_idx, email, name, part, ip) values(";
					$sql = $sql .= "'".$companyno."', '".$user_part."', '".$project_idx."','".$user_id."','".$user_name."','".$part_name."','".LIP."')";
					$insert_idx = insertIdxQuery($sql);
				}
			}

			if($_POST['listsort']){
				$item = explode("&",$_POST['listsort']);
				$i = 1;
				foreach ($item as $value) {
					$value = str_replace("listsort[]=", "", $value);
					$sql = "update work_todaywork_project_sort set sort='".$i."', editdate=".DBDATE." where email='".$user_id."' and project_idx='".$value."'";
					$up[] = updateQuery($sql);
					$i++;
				}

				if( count($up) == count($item)){
					echo "complete";
					exit;
				}
			}
			exit;
		}*/
	}
}


//프로젝트 삭제하기
//프로젝트 사용자별 정렬 리스트 삭제(work_todaywork_project_sort)
//삭제 : state=9
if($mode == "project_del"){

	$project_idx = $_POST['project_idx'];
	$project_idx = preg_replace("/[^0-9]/", "", $project_idx);

	if($project_idx){

		//종료된 파티인 경우
		$sql = "select idx from work_todaywork_project where state='1' and idx='".$project_idx."'";
		$pro_info = selectQuery($sql);
		if($pro_info['idx']){
			$sql = "select idx from work_todaywork_project_info where state='0' and party_idx='".$project_idx."'";
			$project_row_info =	selectQuery($sql);
			if(!$project_row_info['idx']){

				//파티 삭제 처리
				$sql = "select idx from work_todaywork_project where state='1' and idx='".$project_idx."' and email='".$user_id."' and companyno='".$companyno."'";
				$project_info = selectQuery($sql);
				if($project_info['idx']){
					$sql = "update work_todaywork_project set state='9', editdate=".DBDATE." where idx='".$project_info['idx']."'";
					$up = updateQuery($sql);

					//파티 정렬기준 삭제
					$sql = "update work_todaywork_project_sort set state='9', editdate=".DBDATE." where project_idx='".$project_info['idx']."' and companyno='".$companyno."'";
					$up2 = updateQuery($sql);

					//파티 구성원 삭제
					$sql = "update work_todaywork_project_user set state='9', editdate=".DBDATE." where project_idx='".$project_info['idx']."' and companyno='".$companyno."'";
					$up3 = updateQuery($sql);

					if($up && $up2 && $up3){
						echo "complete";
						exit;
					}
				}
			}else{

				echo "party_del_not";
				exit;
			}
		}else{

			$sql = "select idx from work_todaywork_project where state='0' and idx='".$project_idx."' and email='".$user_id."' and companyno='".$companyno."'";
			$project_info = selectQuery($sql);
			if($project_info['idx']){
				$sql = "update work_todaywork_project set state='9', editdate=".DBDATE." where idx='".$project_info['idx']."'";
				$up = updateQuery($sql);

				//파티 정렬기준 삭제
				$sql = "update work_todaywork_project_sort set state='9', editdate=".DBDATE." where project_idx='".$project_info['idx']."' and companyno='".$companyno."'";
				$up2 = updateQuery($sql);

				//파티 구성원 삭제
				$sql = "update work_todaywork_project_user set state='9', editdate=".DBDATE." where project_idx='".$project_info['idx']."' and companyno='".$companyno."'";
				$up3 = updateQuery($sql);

				//오늘업무 쪽에 간 알림 삭제
				$sql = "update work_todaywork set state = '9', editdate=".DBDATE." where work_idx='".$project_info['idx']."' and companyno='".$companyno."' ";
				$up4 = updateQuery($sql);

				if($up && $up2 && $up3){
					echo "complete";
					exit;
				}
			}
		}
	}
	exit;
}


//프로젝트 사용자 추가
if($mode == "project_user_add"){
	/*
	print "<pre>";
	print_r($_POST);
	print "</pre>";
	*/

	$project_idx = $_POST['project_idx'];
	$project_idx = preg_replace("/[^0-9]/", "", $project_idx);
	if($project_idx){

		$sql = "select idx, email from work_todaywork_project where state='0' and idx='".$project_idx."'";
		$project_info = selectQuery($sql);
		if($project_info['idx']){

			//프로젝트 생성한 사용자와 같을경우
			if($user_id == $project_info['email']){
				echo "over_step1";
				exit;
			}else{

				//프로젝트 사용자에 내역이 있는 경우
				$sql = "select idx from work_todaywork_project_user where state='0' and project_idx='".$project_info['idx']."' and companyno='".$companyno."' and email='".$user_id."'";
				$project_user_info = selectQuery($sql);
				if($project_user_info['idx']){
					echo "over_step2";
					exit;
				}else{

					$sql = "insert into work_todaywork_project_user(project_idx, companyno, part_flag, email, name, part, ip) values(";
					$sql = $sql .= "'".$project_info['idx']."','".$companyno."','".$user_part."','".$user_id."','".$user_name."','".$part_name."','".LIP."')";
					$insert_idx = insertIdxQuery($sql);

					if($insert_idx){
						$sql = "update work_todaywork_project set editdate=".DBDATE." where state='0' and companyno='".$companyno."' and idx='".$project_info['idx']."'";
						$up = updateQuery($sql);

						//타임라인(파티참여하기)
						work_data_log('0','18', $insert_idx, $user_id, $user_name);

						//파티 업데이트 시간 가져오기
						$sql = "select DATE_FORMAT(editdate, '%Y-%m-%d %H:%i') as edate from work_todaywork_project where state='0' and companyno='".$companyno."' and idx='".$project_info['idx']."'";
						$project_edit_info = selectQuery($sql);
						if($project_edit_info['edate']){
							$edate = $project_edit_info['edate'];
							$edate_tmp = explode("-", $edate);
							$edate_tmp2 = explode(":", $edate);
							if($edate_tmp){
								$edate_result1 = $edate_tmp[1]."/".$edate_tmp[2];
							}
						}
						echo "complete|".$edate_result1;
						exit;
					}
				}
			}
		}
	}
	exit;
}


//파티 나가기
if($mode == "project_part_out"){


	/*print "<pre>";
	print_r($_POST);
	print "</pre>";*/

	$project_idx = $_POST['project_idx'];
	$project_idx = preg_replace("/[^0-9]/", "", $project_idx);
	if($project_idx){

		$sql = "select idx, project_idx from work_todaywork_project_user where state='0' and companyno='".$companyno."' and email='".$user_id."' and project_idx='".$project_idx."'";
		$project_user_info = selectQuery($sql);
		if($project_user_info['idx']){

			$sql = "update work_todaywork_project_user set state='9', editdate=".DBDATE." where state='0' and idx='".$project_user_info['idx']."'";
			$up = updateQuery($sql);
			if($up){

				$sql = "update work_todaywork_project set editdate=".DBDATE." where state='0' and companyno='".$companyno."' and idx='".$project_user_info['project_idx']."'";
				$up = updateQuery($sql);

				//타임라인(파티나가기)
				work_data_log('0','19', $project_user_info['project_idx'], $user_id, $user_name);

				//파티 업데이트 시간 가져오기
				$sql = "select DATE_FORMAT(editdate, '%Y-%m-%d %H:%i') as edate from work_todaywork_project where state='0' and companyno='".$companyno."' and idx='".$project_user_info['project_idx']."' ";
				$project_edit_info = selectQuery($sql);
				if($project_edit_info['edate']){
					$edate = $project_edit_info['edate'];
					$edate_tmp = explode("-", $edate);
					$edate_tmp2 = explode(":", $edate);
					if($edate_tmp){
						$edate_result1 = $edate_tmp[1]."/".$edate_tmp[2];
					}
				}
				echo "complete|".$edate_result1;
				exit;
			}
		}
	}
	exit;
}


//전체파티
if($mode == "project_all"){

	//회사별 파티 회원리스트
	$project_user_list = member_party_user_list();
	
	//좌측메뉴 프로젝트 리스트(사용자별 정렬 리스트)
	$sql = "select a.idx, a.title, a.email, b.sort, DATE_FORMAT(a.editdate, '%Y-%m-%d %H:%i') as edate, DATE_FORMAT(a.regdate, '%Y-%m-%d %H:%i') as rdate,";
	$sql = $sql .= " case when a.editdate is null then TIMESTAMPDIFF(minute, a.regdate, ".DBDATE.") when a.editdate is not null then TIMESTAMPDIFF(minute, a.regdate, a.editdate)";
	$sql = $sql .= " end as reg from work_todaywork_project as a left join work_todaywork_project_sort as b on(a.idx=project_idx)";
	$sql = $sql .=" where a.state!='9' and a.companyno='".$companyno."' and b.state='0' and b.email='".$user_id."' order by b.sort asc";
	$project_info = selectAllQuery($sql);
	if(!$project_info['idx']){
		$sql = "select idx, sort, email, name, part, title, DATE_FORMAT(editdate, '%Y-%m-%d %H:%i') as edate, DATE_FORMAT(regdate, '%Y-%m-%d %H:%i') as rdate, DATE_FORMAT(editdate, '%Y-%m-%d'), DATE_FORMAT(editdate, '%H:%i:%s'),";
		$sql = $sql .= " case when editdate is null then TIMESTAMPDIFF(minute, regdate, ".DBDATE.") when editdate is not null then TIMESTAMPDIFF(minute, editdate, ".DBDATE.")";
		$sql = $sql .= " end as reg";
		$sql = $sql .= " from work_todaywork_project where state='0' and companyno='".$companyno."' order by sort asc";
		$project_info = selectAllQuery($sql);
	}

	//프로젝트 생성한 아이디 내역
	$sql = "select idx, email from work_todaywork_project where state='0' and companyno='".$companyno."' order by sort asc";
	$project_part_info = selectAllQuery($sql);
	for($i=0; $i<count($project_part_info['idx']); $i++){
		$project_part_info_idx = $project_part_info['idx'][$i];
		$project_part_info_email = $project_part_info['email'][$i];
		$project_part_info_auth[$project_part_info_idx] = $project_part_info_email;
	}

	for($i=0; $i<count($project_info['idx']); $i++){
		$project_idx = $project_info['idx'][$i];
		$project_title = $project_info['title'][$i];
		$project_user_email = $project_info['email'][$i];
		$project_user_name = $project_info['name'][$i];
		$project_user_part = $project_info['part'][$i];
		$project_user_regdate = $project_info['regdate'][$i];
		$project_user_reg = $project_info['reg'][$i];
		$project_user_rdate = $project_info['rdate'][$i];
		$project_user_edate = $project_info['edate'][$i];

		if($project_user_edate){
			$project_user_edate_tmp = explode("-", $project_user_edate);
			if($project_user_edate_tmp){
				$pr_user_wdate = $project_user_edate_tmp[1]."/".$project_user_edate_tmp[2];
			}
		}else{
			$project_user_rdate_tmp = explode("-", $project_user_rdate);
			if($project_user_rdate_tmp){
				$pr_user_wdate = $project_user_rdate_tmp[1]."/".$project_user_rdate_tmp[2];
			}
		}


		if($project_user_reg > 60){
			$ldl_box_time = $pr_user_wdate . " 업데이트";
		}else{
			$ldl_box_time = "최신 업데이트";
		}

		$profile_main_img_src = profile_img_info($project_user_email);


	?>
		<div class="ldl_box" id="listsort_<?=$project_idx?>" value="<?=$project_idx?>">
			<div class="ldl_box_in">
				<?if($user_id == $project_part_info_auth[$project_idx]){?>
					<button class="ldl_box_close" id="ldl_box_close_all_<?=$project_idx?>" value="<?=$project_idx?>">닫기</button>
				<?}?>

				<div class="ldl_box_tit"><p><?=$project_info['title'][$i]?></p></div>
				<div class="ldl_box_tit_regi" style="display:none">
					<textarea name="" class="ldl_textarea_regi"><?=$project_info['title'][$i]?></textarea>
					<div class="ldl_btn_regi_box">
						<button class="ldl_btn_regi_submit"><span>확인</span></button>
						<button class="ldl_btn_regi_cancel"><span>취소</span></button>
					</div>
				</div>
				<div class="ldl_box_time" id="ldl_box_time_<?=$project_idx?>"><?=$ldl_box_time?></div>
				<div class="ldl_box_user">
					<ul>
						<!-- <li <?=$user_id==$project_user_email?" class=\"ldl_me\"":""?>>
							<div class="ldl_box_img" style="background-image:url(<?=$profile_main_img_src?>)"></div>
							<div class="ldl_box_user">
								<strong><?=$project_user_name?></strong>
								<span><?=$project_user_part?></span>
							</div>
						</li> -->
						<?
						$part_out = false;
						for($j=0; $j<count($project_user_list[$project_idx]['email']); $j++){

							$project_user_list_email = $project_user_list[$project_idx]['email'][$j];
							$project_user_list_profile_img = profile_img_info($project_user_list_email);

							if($user_id==$project_user_list_email){
								//$part_out = true;
								$li_class = ' class="ldl_me"';
							}else{
								$li_class = '';
							}

						?>
							<li <?=$li_class?>>
								<div class="ldl_box_img" style="background-image:url(<?=$project_user_list_profile_img?>)" title="<?=$project_user_list[$project_idx]['name'][$j]?>"></div>
								<div class="ldl_box_user">
									<strong><?=$project_user_list[$project_idx]['name'][$j]?></strong>
									<span><?=$project_user_list[$project_idx]['part'][$j]?></span>
								</div>
							</li>
						<?}?>
					</ul>
				</div>

				<?//접속한 아이디 != 파티생성한 아이디
				if($user_id!=$project_user_email && in_array($user_id , $project_user_list['use'][$project_idx])){?>
					<button class="ldl_box_out" id="ldl_box_out_<?=$project_idx?>" value="<?=$project_idx?>">
						<span>파티에서 나가기</span>
					</button>
				<?}else{?>
					<button class="ldl_box_out" id="ldl_box_out_<?=$project_idx?>" value="<?=$project_idx?>" style="display:none;">
						<span>파티에서 나가기</span>
					</button>
				<?}?>
			</div>
		</div>
		<?
	}

	if($project_info['idx']){
		echo "|".count($project_info['idx']);
	}else{?>
		<div class="ldl_list_none">
			<strong><span>현재 생성된 파티가 없습니다.</span></strong>
		</div>|0
	<?php
	}
}


//나의파티
if($mode == "project_my"){


	//회사별 파티 회원리스트
	$project_user_list = member_party_user_list();

	//나의 파티 내역
	$project_myinfo = member_party_user_mylist();

	//프로젝트 생성한 아이디 내역
	$sql = "select idx, email from work_todaywork_project where state='0' and companyno='".$companyno."' order by sort asc";
	$project_part_info = selectAllQuery($sql);
	for($i=0; $i<count($project_part_info['idx']); $i++){
		$project_part_info_idx = $project_part_info['idx'][$i];
		$project_part_info_email = $project_part_info['email'][$i];
		$project_part_info_auth[$project_part_info_idx] = $project_part_info_email;
	}


	for($i=0; $i<count($project_myinfo['idx']); $i++){
		$project_idx = $project_myinfo['idx'][$i];
		$project_title = $project_myinfo['title'][$i];
		$project_user_email = $project_myinfo['email'][$i];
		$project_user_name = $project_myinfo['name'][$i];
		$project_user_part = $project_myinfo['part'][$i];
		$project_user_regdate = $project_myinfo['regdate'][$i];
		$project_user_reg = $project_myinfo['reg'][$i];
		$project_user_rdate = $project_myinfo['rdate'][$i];
		$project_user_edate = $project_myinfo['edate'][$i];

		if($project_user_edate){
			$project_user_edate_tmp = explode("-", $project_user_edate);
			if($project_user_edate_tmp){
				$pr_user_wdate = $project_user_edate_tmp[1]."/".$project_user_edate_tmp[2];
			}
		}else{
			$project_user_rdate_tmp = explode("-", $project_user_rdate);
			if($project_user_rdate_tmp){
				$pr_user_wdate = $project_user_rdate_tmp[1]."/".$project_user_rdate_tmp[2];
			}
		}


		if($project_user_reg > 60){
			$ldl_box_time = $pr_user_wdate . " 업데이트";
		}else{
			$ldl_box_time = "최신 업데이트";
		}

		$profile_main_img_src = profile_img_info($project_user_email);


	?>
		<div class="ldl_box" id="listsort_<?=$project_idx?>" value="<?=$project_idx?>">
			<div class="ldl_box_in">

				<?if($user_id == $project_part_info_auth[$project_idx]){?>
					<button class="ldl_box_close" id="ldl_box_close_my_<?=$project_idx?>" value="<?=$project_idx?>">닫기</button>
				<?}?>

				<div class="ldl_box_tit"><p><?=$project_title?></p></div>
				<div class="ldl_box_tit_regi" style="display:none">
					<textarea name="" class="ldl_textarea_regi"><?=$project_title?></textarea>
					<div class="ldl_btn_regi_box">
						<button class="ldl_btn_regi_submit"><span>확인</span></button>
						<button class="ldl_btn_regi_cancel"><span>취소</span></button>
					</div>
				</div>
				<div class="ldl_box_time" id="ldl_box_time_<?=$project_idx?>"><?=$ldl_box_time?></div>
				<div class="ldl_box_user">
					<ul>
						<?
						$part_out = false;
						for($j=0; $j<count($project_user_list[$project_idx]['email']); $j++){

							$project_user_list_email = $project_user_list[$project_idx]['email'][$j];
							$project_user_list_profile_img = profile_img_info($project_user_list_email);

							if($user_id==$project_user_list_email){
								//$part_out = true;
								$li_class = ' class="ldl_me"';
							}else{
								$li_class = '';
							}

						?>
							<li <?=$li_class?>>
								<div class="ldl_box_img" style="background-image:url(<?=$project_user_list_profile_img?>)" title="<?=$project_user_list[$project_idx]['name'][$j]?>"></div>
								<div class="ldl_box_user">
									<strong><?=$project_user_list[$project_idx]['name'][$j]?></strong>
									<span><?=$project_user_list[$project_idx]['part'][$j]?></span>
								</div>
							</li>
						<?}?>
					</ul>
				</div>

				<?//접속한 아이디 != 파티생성한 아이디
				if($user_id!=$project_user_email && in_array($user_id , $project_user_list['use'][$project_idx])){?>
					<button class="ldl_box_out" id="ldl_box_out_<?=$project_idx?>" value="<?=$project_idx?>">
						<span>파티에서 나가기</span>
					</button>
				<?}else{?>
					<button class="ldl_box_out" id="ldl_box_out_<?=$project_idx?>" value="<?=$project_idx?>" style="display:none;">
						<span>파티에서 나가기</span>
					</button>
				<?}?>
			</div>
		</div>
	<?php
	}
	if($project_myinfo['idx']){
		echo "|".count($project_myinfo['idx']);
	}else{?>
		<div class="ldl_list_none">
			<strong><span>현재 생성된 파티가 없습니다.</span></strong>
		</div>|0
	<?php
	}
	exit;
}


//파티만들기
if($mode == "project_user_create"){

	/*
	print "<pre>";
	print_r($_POST);
	print "</pre>";
	*/

	$user_chk_val = $_POST['user_chk_val'];
	if($user_chk_val){
		$user_chk_val = str_replace(" ","", $user_chk_val);
		$user_chk_val_ex = @explode(",", $user_chk_val);
		if($user_chk_val){
			$sql = "select idx, email, name from work_member where state='0' and companyno='".$companyno."' and idx in(".$user_chk_val.")";
			if($get_dirname == 'party'){
				$sql = $sql .= " or (state='0' and companyno='".$companyno."' and email='".$user_id."')";
			}
			//$sql = "select idx, email, name from work_member where state='0' and companyno='".$companyno."' and idx in(".$user_chk_val.")";
			$sql = $sql .= " order by ";
			$sql = $sql .= " CASE WHEN email='".$user_id."' THEN email END DESC,";
			$sql = $sql .= " CASE WHEN live_1_regdate is null THEN name END ASC";
			$member_info = selectAllQuery($sql);

			for($i=0; $i<count($member_info['idx']); $i++){
				$member_info_idx = $member_info['idx'][$i];
				$member_info_email = $member_info['email'][$i];
				$member_info_name = $member_info['name'][$i];

				//프로필 캐릭터,사진
				$profile_main_img_src = profile_img_info($member_info_email);
			?>
				<li <?=$user_id==$member_info_email?' class="lm_me"':''?> id="user_<?=$member_info_idx?>">
					<?=$user_id==$member_info_email?"<img src=\"/html/images/pre/ico_me.png\" alt=\"\" class=\"user_me\" />":""?>
					<div class="user_img" style="background-image:url(<?=$profile_main_img_src?>);"></div>
					<div class="user_name"><strong><?=$member_info_name?></strong></div>
					<button class="user_slc_del" value="<?=$member_info_idx?>" title="삭제"><span>삭제</span></button>
				</li>

			<?php
			}
		}
	}

	exit;
}


//파티만들기
if($mode == "project_create"){

	/*print "<pre>";
	print_r($_POST);
	print "</pre>";
	*/

	$textarea_lm = addslashes($_POST['textarea_lm']);
	$user_chk_val = $_POST['user_chk_val']; // 파티 멤버
	$user_chk_val = str_replace(" ","", $user_chk_val);
	$user_chk_val_ex = @explode(",", $user_chk_val); // 파티멤버를 배열에 담음



	$party_link = date("His").$work_idx;
	$sql = "select idx from work_todaywork_project where state='0' and companyno='".$companyno."' and title='".$textarea_lm."' and email='".$user_id."'";
	$project_info = selectQuery($sql);
	if(!$project_info['idx']){
		//파티
		$sql = "select max(idx)+1 as maxnum from work_todaywork_project";
		$project_max_info = selectQuery($sql);
		$project_maxno = $project_max_info['maxnum'];
		$party_link = date("His").$project_maxno;

		$sql = "insert into work_todaywork_project(companyno, part_flag, email, name, part, title, ip, party_link)";
		$sql = $sql .= " values('".$companyno."','".$user_part."','".$user_id."','".$user_name."','".$part_name."','".$textarea_lm."','".LIP."', '".$party_link."')";
		$project_insert_idx = insertIdxQuery($sql);
		if($project_insert_idx){
			
			//회원전체 리스트
			$member_list_info = member_alist_info();
			
			for($i=0; $i<count($member_list_info['idx']); $i++){
				$mem_idx = $member_list_info['idx'][$i];
				$mem_id = $member_list_info['email'][$i];
				$mem_name = $member_list_info['name'][$i];
				$mem_part = $member_list_info['part'][$i];
				$mem_partno = $member_list_info['partno'][$i];

				//회원별 프로젝트 추가
				$sql = "select email from work_todaywork_project_sort where state='0' and companyno='".$companyno."' and project_idx='".$project_insert_idx."' and email='".$mem_id."'";
				
				$project_user_info = selectQuery($sql);
				if(!$project_user_info['idx']){
					$sql = "insert into work_todaywork_project_sort(project_idx, companyno, part_flag, email, name, part, ip)";
					$sql = $sql .= " values('".$project_insert_idx."','".$companyno."','".$mem_partno."','".$mem_id."','".$mem_name."','".$mem_part."','".LIP."')";
					$insert_idx = insertIdxQuery($sql);
				}
				
			}

			//본인데이터
			$sql = "select idx from work_todaywork_project_user where state='0' and project_idx='".$project_insert_idx."' and email='".$user_id."'";			
			$user_info = selectQuery($sql);
			if(!$user_info['idx']){
				$sql = "insert into work_todaywork_project_user(project_idx, email, name, companyno, part_flag, part, ip) values(";
				$sql = $sql .= " '".$project_insert_idx."','".$user_id."','".$user_name."','".$companyno."','".$user_part."','".$part_name."','".LIP."')";
				$insert_idx_my = insertIdxQuery($sql);
			}

			//파티원 데이터
			$sql = "select idx, email, name, companyno, part, partno, highlevel from work_member where state='0' and idx in($user_chk_val)";
			$project_user_create = selectAllQuery($sql);
			
			$sql = "select idx, email, name, companyno, part, partno, highlevel	 from work_member where state='0' and idx in($user_chk_val) and email!='".$user_id."'";
			$project_not_me = selectAllQuery($sql);

			$notice_flag = '0';

			for($i=0; $i<count($project_user_create['idx']); $i++){

				$project_user_create_id = $project_user_create['email'][$i];
				$project_user_create_name = $project_user_create['name'][$i];
				$project_user_create_companyno = $project_user_create['companyno'][$i];
				$project_user_create_part = $project_user_create['part'][$i];
				$project_user_create_partno = $project_user_create['partno'][$i];

				$sql = "select idx from work_todaywork_project_user where state='0' and project_idx='".$project_insert_idx."' and email='".$project_user_create_id."'";
				$user_info = selectQuery($sql);
				if(!$user_info['idx']){
					$sql = "insert into work_todaywork_project_user(project_idx, email, name, companyno, part_flag, part, ip) values(";
					$sql = $sql .= " '".$project_insert_idx."','".$project_user_create_id."','".$project_user_create_name."','".$project_user_create_companyno."','".$project_user_create_partno."','".$project_user_create_part."','".LIP."')";
					$insert_idx_part = insertIdxQuery($sql);
				}
				
				$tokenTitle = "파티 생성";
				$tokenComment = $user_name."님이 [".$textarea_lm."] 파티에 초대했습니다."; 
				pushToken($tokenTitle, $tokenComment, $project_user_create['email'][$i],'party','17',$user_id,$user_name,$project_insert_idx);

				$sql = "insert into work_todaywork(highlevel, work_flag, part_flag, part, type_flag, notice_flag, work_idx, email, name, contents, companyno, ip, workdate) ";
				$sql = $sql .= " values('".$project_user_create['highlevel'][$i]."', '4', '".$project_user_create['partno'][$i]."' ,'".$project_user_create['part'][$i]."', '".$type_flag."', '".$notice_flag."', '".$project_insert_idx."', '".$project_user_create['email'][$i]."', '".$project_user_create['name'][$i]."', '".$tokenComment."','".$companyno."','".LIP."','".TODATE."')";
				$work_insert = insertQuery($sql);
			}

			//나의파티 또는 파티원데이터(파티원을 삭제하고 본인만 만들때)
			if($insert_idx_my || $insert_idx_part){

				//타임라인(파티만들기)
				work_data_log('0','17', $project_insert_idx, $user_id, $user_name);

				//파티 만들기 역량 추가

				//만든사람
				$project_profile_img1 = profile_img_info($user_id);

				$html = '';
				$html = $hml .= '<div class="ldl_box" id="listsort_'.$project_insert_idx.'">';
				$html = $hml .= '	<div class="ldl_box_in">';
				$html = $hml .= '		<button class="ldl_box_close" id="ldl_box_close_all_'.$project_insert_idx.'" value="'.$project_insert_idx.'">닫기</button>';
				$html = $hml .= '		<div class="ldl_box_tit"><p>'.$textarea_lm.'</p></div>';
				$html = $hml .= '		<div class="ldl_box_time" id="ldl_box_time_'.$project_insert_idx.'">최신 업데이트</div>';
				$html = $hml .= '		<div class="ldl_box_user">';
				$html = $hml .= '			<ul>';
				$html = $hml .= '				<li class="ldl_me">';
				$html = $hml .= '					<div class="ldl_box_img" style="background-image:url('.$project_profile_img1.')" title="'.$user_name.'"></div>';
				$html = $hml .= '					<div class="ldl_box_user"><strong>'.$user_name.'</strong><span>'.$part_name.'</span></div>';
				$html = $hml .= '				</li>';


				//참여자들
				for($i=0; $i<count($project_not_me['idx']); $i++){

					$project_not_me_id = $project_not_me['email'][$i];
					$project_not_me_name = $project_not_me['name'][$i];
					$project_not_me_part = $project_not_me['part'][$i];
					$project_profile_img2 = profile_img_info($project_not_me_id);

					$html = $hml .= '				<li>';
					$html = $hml .= '					<div class="ldl_box_img" style="background-image:url('.$project_profile_img2.')" title="'.$project_not_me_name.'"></div>';
					$html = $hml .= '					<div class="ldl_box_user"><strong>'.$project_not_me_name.'</strong><span>'.$project_not_me_part.'</span></div>';
					$html = $hml .= '				</li>';
				}

				$html = $hml .= '			</ul>';
				$html = $hml .= '		</div>';

				$html = $hml .= '		<button class="ldl_box_out" id="ldl_box_out_'.$project_insert_idx.'" value="'.$project_insert_idx.'" style="display:none;">';
				$html = $hml .= '			<span>파티에서 나가기</span>';
				$html = $hml .= '		</button>';
				$html = $hml .= '	</div>';
				$html = $hml .= '</div>';

				$result = "complete";
				echo $result."|".$html;
			}

			//파티 만들기 역량 추가
			work_cp_reward("party","0001",$user_id,$project_insert_idx);
			main_like_cp_works('party_create');
			exit;
		}

	}else{

		echo "같은 이름의 파티명이 있습니다.";
		exit;
	}

}


//보상 코인
if($mode == "coin_reward"){

	//디렉토리 = type
	$reward_type = $_POST['path'];

	//work_idx
	$lr_work_idx = $_POST['lr_work_idx'];

	//보상 대상 회원번호
	$lr_uid = $_POST['lr_uid'];

	//보상 코인정보
	$lr_val = $_POST['lr_val'];

	//보상 코인
	$coin = $_POST['coin'];

	//보상메시지
	$lr_input_text = $_POST['lr_input_text'];

	$replace_input_text = replace_text($lr_input_text);

	$coin = preg_replace("/[^0-9]/", "", $coin);

	$lr_val = preg_replace("/[^0-9]/", "", $lr_val);

	if($lr_work_idx){
		$sql = "select link_idx from work_todaywork_comment where idx = '".$lr_work_idx."' limit 1";
		$lr_work_idx_info = selectQuery($sql);

		if($lr_work_idx_info){
			$lr_work_idx = $lr_work_idx_info['link_idx'];
		}else{
			$sql = "select idx from work_todaywork where idx = '".$lr_work_idx."' limit 1";
			$lr_work_idx_info = selectQuery($sql);

			$lr_work_idx = $lr_work_idx;
		}
	}

	if($coin && $lr_uid && $lr_val){

		$send_info = member_row_info($lr_uid);
		$penalty = member_penalty($send_info['email']);
			
		if($penalty['penalty_state']>0){
			echo "penalty";
			exit;
		}

		$sql = "select idx, coin, comcoin from work_member where state='0' and companyno='".$companyno."' and email='".$user_id."' limit 1";
		$mb_info = selectQuery($sql);
		if($mb_info['idx']){

			//본인에게 보상 지급 안됨
			if($user_id == $send_info['email']){

				echo "id_same";
				exit;

			//공용코인이 지급할 코인보다 작을경우
			}else if($mb_info['comcoin'] < $coin){

				echo "none";
				exit;

			}else{

				$sql = "select idx, code, coin, memo from work_coin_reward_info where state='0' and kind='live' and idx='".$lr_val."'";
				$coin_reward_info = selectQuery($sql);
				if($coin_reward_info['idx']){

					//입력한 보상 메시지
					if($replace_input_text){
						$coin_info = $replace_input_text;
					}else{
						$coin_info = $coin_reward_info['memo'];
					}

					//work_coin_reward code값 확인
					//코인차감내역
					// $reward_type = "live";
					$sql = "insert into work_coininfo(state, code, work_idx, reward_type, companyno, email, name, reward_user, reward_name, coin, memo, workdate, ip, coin_work_idx) values('0','710','".$coin_reward_info['idx']."','".$reward_type."','".$companyno."','".$user_id."','".$user_name."','".$send_info['email']."','".$send_info['name']."','".$coin."','".$coin_info."','".TODATE."','".LIP."','".$lr_work_idx."')";
					$coininfo_chagam = insertIdxQuery($sql);
					if($coininfo_chagam){
						//코인차감
						$sql = "update work_member set comcoin = comcoin - '".$coin."' where state='0' and companyno='".$companyno."' and email='".$user_id."'";
						$res_comcoin = updateQuery($sql);

						//타임라인(코인 보상함)
						work_data_log('0','20', $coininfo_chagam, $user_id, $user_name, $send_info['email'], $send_info['name'],'0', $coin);
						
						//역량평가지표(보상하기)
						work_cp_reward("reward", "0001", $user_id, $coininfo_chagam);


					}

					//코인정보 comment에 저장
					$sql = "insert into work_todaywork_comment(cmt_flag,link_idx,work_idx,companyno,comment,ip,regdate)";
					$sql = $sql .= " values(2,'".$lr_work_idx."','".$lr_work_idx."','".$companyno."','".$coin_info."'";
					$sql = $sql .= ",'".LIP."',now())";
					$coin_comment_idx = insertIdxQuery($sql);

					//코인 적립 내역
					$sql = "insert into work_coininfo(state, code, work_idx, reward_type, companyno, email, name, reward_user, reward_name, coin, memo, workdate, ip, coin_work_idx) values('0','700','".$coin_reward_info['idx']."','".$reward_type."','".$companyno."','".$send_info['email']."','".$send_info['name']."','".$user_id."','".$user_name."','".$coin."','".$coin_info."','".TODATE."','".LIP."','".$lr_work_idx."')";
					$coininfo_idx = insertIdxQuery($sql);
					if($coininfo_idx){
						$sql = "update work_member set coin = coin + '".$coin."' where state='0' and companyno='".$companyno."' and email='".$send_info['email']."'";
						$res_coin = updateQuery($sql);

						//타임라인(코인 보상받음)

						work_data_log('0','21', $coininfo_idx, $send_info['email'], $send_info['name'], $user_id, $user_name,'0', $coin);

						//역량평가지표(보상받기)
						work_cp_reward("reward", "0002", $send_info['email'], $coininfo_idx);

						$tokenTitle = $user_name."님이 ".$coin."코인을 보냈어요";
						pushToken($tokenTitle,$coin_info,$send_info['email'],'reward','21',$user_id,$user_name,$coin_reward_info['idx'],null,'reward');
					}

					if($coininfo_chagam && $coininfo_idx && $res_comcoin && $res_coin){

						//코인보상으로 가산점
						work_cp_reward_plus("cp", "0007", $coininfo_idx, $send_info['email'], "reward");
						echo "complete";
						exit;
					}
				}
			}
		}
	}else{
		echo "not";
		exit;

	}

	exit;
}

//보상하기(신규)
if($mode == "coin_req_100c"){

	$val = $_POST['val'];
	$val = preg_replace("/[^0-9]/", "", $val);
	if($val){
		//오늘업무 조회 -AI 댓글조회
		$sql = "select idx, work_idx from work_todaywork_comment where state='0' and cmt_flag='1' and companyno='".$companyno."' and idx='".$val."'";
		$work_info = selectQuery($sql);
		if($work_info['work_idx']){

			//업무 최초 작성자 체크
			$sql = "select idx, email from work_todaywork where state!='9' and companyno='".$companyno."' and idx='".$work_info['work_idx']."'";
			$info = selectQuery($sql);
			if($info['email']){
				echo "complete|".$info['email'];
				exit;
			}
		}
	}
}



//역량지표
if($mode == "cp_reward_list"){

	$type = $_POST['type'];
	$uid = $_POST['uid'];
	$type = preg_replace("/[^0-9]/", "", $type);

	$curYear = (int)date('Y');
	$curMonth = (int)date('m');
	$month_first_day = date("Y-m-d", mktime(0, 0, 0, $curMonth , 1, $curYear));
	$month_last_day = date("Y-m-d", mktime(0, 0, 0, $curMonth+1 , 0, $curYear));

	if($type){

		$sql = "select email, sum( type1 ) as type1, sum( type2) as type2, sum( type3) as type3, sum( type4) as type4, sum( type5) as type5, sum( type6) as type6";
		$sql = $sql .= " , sum( type1 ) + sum( type2) + sum( type3) + sum( type4) + sum( type5) + sum( type6) as tot";
		$sql = $sql .= " from work_cp_reward_list where state='0' and companyno='".$companyno."' and workdate between '".$month_first_day."' and '".$month_last_day."' and email='".$uid."' group by email";
		$reward_tot_info = selectAllQuery($sql);
		///echo $sql;
		for($i=0; $i<count($reward_tot_info['email']); $i++){

			$reward_tot_info_type1 = $reward_tot_info['type1'][$i];
			$reward_tot_info_type2 = $reward_tot_info['type2'][$i];
			$reward_tot_info_type3 = $reward_tot_info['type3'][$i];
			$reward_tot_info_type4 = $reward_tot_info['type4'][$i];
			$reward_tot_info_type5 = $reward_tot_info['type5'][$i];
			$reward_tot_info_type6 = $reward_tot_info['type6'][$i];

			$reward_tot_info_email = $reward_tot_info['email'][$i];
			$reward_tot_info_tot = $reward_tot_info['tot'][$i];
			$reward_tot_info_list[$reward_tot_info_email] = $reward_tot_info_tot;

			$reward_cp_cnt['type1'][$reward_tot_info_email] = $reward_tot_info_type1;
			$reward_cp_cnt['type2'][$reward_tot_info_email] = $reward_tot_info_type2;
			$reward_cp_cnt['type3'][$reward_tot_info_email] = $reward_tot_info_type3;
			$reward_cp_cnt['type4'][$reward_tot_info_email] = $reward_tot_info_type4;
			$reward_cp_cnt['type5'][$reward_tot_info_email] = $reward_tot_info_type5;
			$reward_cp_cnt['type6'][$reward_tot_info_email] = $reward_tot_info_type6;
		}

		//
		$cp_type1 = reward_cp_type_info($reward_cp_cnt['type1'][$uid], 1);
		$cp_type2 = reward_cp_type_info($reward_cp_cnt['type2'][$uid], 2);
		$cp_type3 = reward_cp_type_info($reward_cp_cnt['type3'][$uid], 3);
		$cp_type4 = reward_cp_type_info($reward_cp_cnt['type4'][$uid], 4);
		$cp_type5 = reward_cp_type_info($reward_cp_cnt['type5'][$uid], 5);
		$cp_type6 = reward_cp_type_info($reward_cp_cnt['type6'][$uid], 6);


		//역량지표평가등급
		$cp_rate1 = cp_rate_info($cp_type1['cp_type']);
		$cp_rate2 = cp_rate_info($cp_type2['cp_type']);
		$cp_rate3 = cp_rate_info($cp_type3['cp_type']);
		$cp_rate4 = cp_rate_info($cp_type4['cp_type']);
		$cp_rate5 = cp_rate_info($cp_type5['cp_type']);
		$cp_rate6 = cp_rate_info($cp_type6['cp_type']);

		//지식 type1, //성과 type2, //성장 type3, //협업 type4, //성실 type5, //실행 type6

		?>
			<div class="rew_mains_info_r">
				<div class="rew_mains_chart_graph">
					<div id="rl_radarChart"></div>
					<div class="radar_grade radar_01">
						<span class="radar_tit">에너지</span>
						<em class="grade_<?=strtolower($cp_rate1)?>"><?=$cp_rate1?></em>
						<span class="radar_pt">(<?=$cp_type1['cp_type_sp']?$cp_type1['cp_type_sp']:0?>)</span>
					</div>
					<div class="radar_grade radar_02">
						<span class="radar_tit">성장</span>
						<em class="grade_<?=strtolower($cp_rate3)?>"><?=$cp_rate3?></em>
						<span class="radar_pt">(<?=$cp_type3['cp_type_sp']?>)</span>
					</div>
					<div class="radar_grade radar_03">
						<span class="radar_tit">성실</span>
						<em class="grade_<?=strtolower($cp_rate5)?>"><?=$cp_rate5?></em>
						<span class="radar_pt">(<?=$cp_type5['cp_type_sp']?>)</span>
					</div>
					<div class="radar_grade radar_04">
						<span class="radar_tit">실행</span>
						<em class="grade_<?=strtolower($cp_rate6)?>"><?=$cp_rate6?></em>
						<span class="radar_pt">(<?=$cp_type6['cp_type_sp']?>)</span>
					</div>
					<div class="radar_grade radar_05">
						<span class="radar_tit">협업</span>
						<em class="grade_<?=strtolower($cp_rate4)?>"><?=$cp_rate4?></em>
						<span class="radar_pt">(<?=$cp_type4['cp_type_sp']?>)</span>
					</div>
					<div class="radar_grade radar_06">
						<span class="radar_tit">성과</span>
						<em class="grade_<?=strtolower($cp_rate2)?>"><?=$cp_rate2?></em>
						<span class="radar_pt">(<?=$cp_type2['cp_type_sp']?>)</span>
					</div>
					<div class="radar_total">
						<span><?=$reward_tot_info_tot?$reward_tot_info_tot:0?></span>
					</div>
				</div>
			</div>|<?=$cp_type1['cp_graph']."|".$cp_type2['cp_graph']."|".$cp_type3['cp_graph']."|".$cp_type4['cp_graph']."|".$cp_type5['cp_graph']."|".$cp_type6['cp_graph']?>
	<?php
	}
	exit;
}

//출근후 업무시간
if($mode == "lives_works_time"){

	//회원별 정보 조회	
	$member_login_time = member_row_info($user_id);
	if($member_login_time){
		//퇴근시간 했을경우 퇴근시간으로 지정
		$member_login_attend = false;

		if($user_id=='sadary0@nate.com'){
			//$member_login_time['live_4_regdate'] = '2023-03-29 12:00:00';
		}

		//퇴근을 했을 경우
		if($member_login_time['live_4_regdate']){

			$now_time = $member_login_time['live_1_regdate'];
			$live_regdate = $member_login_time['live_4_regdate'];
			$member_login_attend = false;

			$time_check = strtotime($live_regdate) - strtotime($now_time);
			$total_time = $time_check;

		}else{

			//출근시간
			$now_time = date('Y-m-d H:i:s');
			$live_regdate = $member_login_time['live_1_regdate'];
			$member_login_attend = true;

			$time_check = strtotime($now_time) - strtotime($live_regdate);
			$total_time = $time_check;
		}

		//일
		$days = floor($total_time/86400); 
		$time = $total_time - ($days*86400); 

		//시간
		$hours = floor($time/3600); 
		$time = $time - ($hours*3600); 
		
		//분
		$min = floor($time/60); 
		
		//초
		$sec = $time - ($min*60); 


		$hours = $hours < 10 ? "0".$hours:$hours;
		$min = $min < 10 ? "0".$min:$min;
		$sec = $sec < 10 ? "0".$sec:$sec;

		
		echo "complete|".$hours."|".$min."|".$sec;
		//	echo "out|".$hours."|".$min."|".$sec;
		exit;
	}
}


if($mode == "mem_lives_like"){
	$mem_idx = $_POST['mem_idx'];
	$send_user = $_POST['send_user'];
	$send_userid = $_POST['send_userid'];
	
	//멤버 찜하기 로그

	$sql = "select idx, mem_idx, state, companyno, email, zzim_email  from work_member_zzim_list  where companyno = '".$companyno."' and email = '".$user_id."' and mem_idx = '".$mem_idx."'";
	$sel_zzim = selectQuery($sql);
	if($sel_zzim['idx']){
		if($sel_zzim['state'] == '1'){
			$sql = "update work_member_zzim_list set state = '9' where companyno = '".$companyno."' and email = '".$user_id."' and mem_idx = '".$mem_idx."'";
			$up_zzim = updateQuery($sql);

			if($up_zzim){
				echo "complete";
			}
		}else if($sel_zzim['state'] == '9'){
			$sql = "update work_member_zzim_list set state = '1' where companyno = '".$companyno."' and email = '".$user_id."' and mem_idx = '".$mem_idx."'";
			$up_zzim = updateQuery($sql);

			if($up_zzim){
				echo "complete";
			}
		}
	}else{
		$sql = "insert into work_member_zzim_list(state, companyno, mem_idx, name,  email, zzim_name, zzim_email, ip) values('1', '".$companyno."', '".$mem_idx."', '".$user_name."', '".$user_id."', '".$send_user."', '".$send_userid."', '".LIP."')";
		$insert_zzim = insertQuery($sql);

		if($insert_zzim){
			$sql = "select idx, name, email from work_member where companyno = '".$companyno."' and email != '".$send_userid."' and state = '0'";
			$un_zzim = selectAllQuery($sql);
			
			
			if($un_zzim){
				// var_dump($un_zzim);
				// 찜 off 등록
				$insertQuery = "INSERT INTO work_member_zzim_list(state, companyno, mem_idx, name,  email, zzim_name, zzim_email, ip) 
				VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
				
				$insertStmt = $conn->prepare($insertQuery);
				for($i=0; $i < count($un_zzim['idx']); $i++){
					$un_zzim_name = $un_zzim['name'][$i];
					$un_zzim_email = $un_zzim['email'][$i];
					$un_zzim_idx = $un_zzim['idx'][$i];
					$insertData = array(
						'9',$companyno,$un_zzim_idx,$user_name,
						$user_id,$un_zzim_name,$un_zzim_email,LIP
					);
					$insertStmt->bind_param("isssssss", ...$insertData); 
					$insertStmt->execute();
				}
				$insertStmt->close();
			}

			echo "complete";
		}
	}
}

if($mode == "work_state_off"){
	$work_email = $_POST['email'];
	
	$sql = "select idx, state, decide_flag, email, work_stime, workdate from work_todaywork where decide_flag = '8' and state != '9' and email = '".$work_email."' and workdate = '".TODATE."'";
	$status_work = selectAllQuery($sql);
	if($status_work){
		for($i=0; $i < count($status_work['idx']); $i++){
			if($now_time >= $status_work['work_stime'][$i]){
				$sql = "update work_todaywork set state = '1' where workdate =  '".TODATE."' and companyno = '".$companyno."' and email = '".$work_email."' and idx = '".$status_work['idx'][$i]."'";
				$up_status = updateQuery($sql);
			}
		}
		echo "complete";
	}	
}

// 쪽지 보내기
if($mode == "mess_live"){
	$send_mail = $_POST['send_email'];
	$send_message = $_POST['message'];

	if($_POST['alarm']){
		$service = $_POST['alarm'];
	}else{
		$service = 'live';
	}

	$sql = "select idx, email, state, name from work_member where state = '0' and email = '".$send_mail."' and companyno = '".$companyno."'";
	$mess_mem = selectQuery($sql);
	if(!$mess_mem){
		echo "mem_fail";
	}else{
		$sql = "insert into work_member_message(state,service,contents,companyno,email,name,send_email,send_name,workdate)
				values('0','".$service."','".$send_message."','".$companyno."','".$send_mail."','".$mess_mem['name']."', '".$user_id."','".$user_name."','".TODATE."')";
				$mess_in = insertIdxQuery($sql);

		if($mess_in){

			$message_title = $user_name."님이 쪽지 보냄"; 
			pushToken($message_title,$send_message,$send_mail,'message','39',$user_id,$user_name,null,null,$null);
			echo "complete";
		}
	}
}
?>
