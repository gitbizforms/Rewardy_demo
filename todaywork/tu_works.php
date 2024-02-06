<?
	//header페이지
	$url = __DIR__;
	$urlWithoutLastPath = rtrim($url, '/');
	$lastPath = basename($urlWithoutLastPath);
	$home_dir = rtrim($urlWithoutLastPath, $lastPath);
	
	include $home_dir . "/inc_lude/header_index_new.php";

	$tuto = tutorial_chk();
	// if($tuto['t_flag']>0){
	// 	alert('해당 단계는 이미 완료하셨습니다!');
	// 	echo "<script>history.back();</script>";
	// }

$member_info = member_row_info($user_id);
$tuto_flag = $member_info['t_flag'];	

$wdate = str_replace("-",".",$wdate);

$tuto_start = strstr($_SERVER['PHP_SELF'],'tu_');

if(!$wdate || $wdate == 1){
	//오늘날짜
	$wdate = str_replace("-",".",$today);
}
$sel_wdate = str_replace(".", "-" , $wdate);

$month_tmp = explode(".",$wdate);
if($month_tmp){
	$month_date = $month_tmp[0].".".$month_tmp[1];
}

$curYear = (int)date('Y');
$curMonth = (int)date('m');
$month_first_day = date("Y-m-d", mktime(0, 0, 0, $curMonth , 1, $curYear));
$month_last_day = date("Y-m-d", mktime(0, 0, 0, $curMonth+1 , 0, $curYear));


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


//프로필 캐릭터 사진
$character_img_info = character_img_info();

$img_buy_arr = array();
	//프로필 캐릭터 구입여부
$sql = "select idx,item_idx from work_item_info where state = '0' and member_email = '".$user_id."'";
$img_buy_flag = selectAllQuery($sql);

for($i=0; $i<count($img_buy_flag['idx']); $i++){
	$img_buy_idx = $img_buy_flag['idx'][$i];
	$img_item_idx = $img_buy_flag['item_idx'][$i];
	$img_buy_arr[$img_item_idx] = $img_buy_idx;
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


//좋아요 리스트
$like_flag_list = array();
$sql = "select idx, email,service, work_idx, send_email, like_flag from work_todaywork_like where state='0' and companyno='".$companyno."' and send_email='".$user_id."' and workdate='".$sel_wdate."'";
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
$sql = "select idx, email,service, work_idx, send_email, like_flag from work_todaywork_like where state='0' and email='".$user_id."' and workdate='".$sel_wdate."'";
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

//파티 - 연결된 업무
$project_data_info = project_data_info();
$project_link_info = @array_combine($project_data_info['work_idx'], $project_data_info['party_link']);
?>



<script>
$(document).ready(function(){
	$(".rew_box").addClass("on");
	$(".rew_menu_onoff button").addClass("on");
});

function tuto_position(){
	$(".tuto").each(function(i){
		var i = i+1;
		var tuto = $(this);
		var tt_l = tuto.offset().left;
		var tt_t = tuto.offset().top;
		var tt_w = tuto.width() / 2;
		var tt_h = tuto.height() / 2;
		var tt_x = tt_l + tt_w;
		var tt_y = tt_t + tt_h;
		var win_w = $(window).width();
		var win_h = $(window).height();
		var win_h2 = $(window).height() / 2;
		var tt_r = win_w - 400;
		var tt_p = $(".tuto_pop_01_0"+i+"").height();
		var tt_ph = tt_p + tt_y;
			if(tt_x > tt_r){
			$(".tuto_pop_01_0"+i+"").css({
				left:"auto",
				right:70,
				opacity:1
			});
			$(".tuto_pop_01_0"+i+"").removeClass("tuto_l tuto_r").addClass("tuto_r");
			}else{
			$(".tuto_pop_01_0"+i+"").css({
				left:(tt_x-47),
				opacity:1
			});
			$(".tuto_pop_01_0"+i+"").removeClass("tuto_l tuto_r").addClass("tuto_l");
			}
			if(tt_ph > (win_h - 70)){
			$(".tuto_pop_01_0"+i+"").css({
				top:(tt_t-tt_p-24),
			});
			$(".tuto_pop_01_0"+i+"").removeClass("tuto_t tuto_b").addClass("tuto_t");
			}else{
			$(".tuto_pop_01_0"+i+"").css({
				top:(tt_y+42),
			});
			$(".tuto_pop_01_0"+i+"").removeClass("tuto_t tuto_b").addClass("tuto_b");
			}
			$(".tuto_mark_01_0"+i+"").css({
		left:tt_x,
		top:tt_y,
		opacity:1
		});
	});
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
<html>
	<head>
		<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
		<script src="/js/tutorial_common.js<?php echo VER;?>"></script>
	</head>
<body>
<input type="hidden" value="<?=$tuto_flag?>" id="tutorial_flag">
<div class="rew_warp">
	<div class="rew_warp_in">
		<div class="rew_box">
			<div class="rew_box_in">
				<? include $home_dir . "/inc_lude/header_new.php";?>
				<!-- //상단 -->
				<!-- menu -->
				<? include $home_dir . "/inc_lude/menu_index.php";?>
				<!-- //menu -->

				<!-- 콘텐츠 -->
				<div class="rew_conts">
					<div class="rew_conts_in">

					<div class="tdw_tab">
						<div class="tdw_tab_in">
							<div class="tdw_tab_left">
								<div class="tdw_tab_sort sort_01">
									<div class="tdw_tab_sort_in">
										<button class="btn_sort_on select_dd dday" value="일일"><span>일일</span></button>
										<ul class="tdw_new_select">
											<li><button class="select_dd on" value="일일"><span>일일</span></button></li>
											<li><button class="select_ww" value="주간"><span>주간</span></button></li>
											<li><button class="select_mm" value="월간"><span>월간</span></button></li>
										</ul>
									</div>
								</div>
								<div class="tdw_tab_sort sort_02">
									<div class="tdw_tab_sort_in">
										<button class="btn_sort_on all_work" value="업무 전체보기"><span>업무 전체보기</span></button>
										<ul class="tdw_work_select">
											<li><button class= "all on" value="업무 전체보기"><span>업무 전체보기</span></button></li>
											<li><button class= "work" value="업무"><span>업무</span></button></li>
											<li><button class= "report" value="보고"><span>보고</span></button></li>
											<li><button class= "req" value="요청"><span>요청</span></button></li>
											<li><button class= "share" value="공유"><span>공유</span></button></li>
										</ul>
									</div>
								</div>
							</div>
							<div class="tdw_date_calendar">
								<button class="calendar_prev"><span>이전</span></button>
								<input type="text" id="work_date" class="calendar_num" value="<?=$wdate?>" />
								<input type="text" id="work_month" class="calendar_num" value="<?=$month_date?>" data-min-view="months" data-view="months" data-date-format="yyyy.mm" readonly="readonly" style="display:none;">
								<button class="calendar_next"><span>다음</span></button>
							</div>

							<div class="tdw_tab_right">
								<div class="tdw_tab_report">
									<button class="select_report"><span>주간리포트</span></button>
								</div>
								<div class="tdw_tab_search">
									<button class="btn_tdw_search" id="btn_tdw_search"><span>검색</span></button>
								</div>
								<div class="tdw_tab_reset">
									<button class="btn_tdw_reset"><span>새로고침</span></button>
								</div>
							</div>
						</div>
					</div>
						<div class="rew_conts_scroll_08">
							<div class="rew_todaywork">
								<div class="rew_todaywork_in">
									<!-- <div class="tdw_penalty_banner" style = "display:none;">
										<div class="tdw_pb_in">
											<img src="/html/images/pre/img_penalty.png" alt="" />
											<p><span>[긴급]</span>페널티 카드가 발동했습니다.</p>
											<button class="btn_penalty_banner"><span>미션 수행하기</span></button>
											<strong class="penalty_comp" style="display:none;"><span>미션 완료</span></strong>
										</div>
									</div> -->

									<div class="tdw_list">
										<div class="tdw_list_in">
											<div class="tdw_list_dd">
												<ul class="tdw_list_ul">
												<?
													$sql = "select idx, penalty_state, email from work_member where state = '0' and email = '".$user_id."'";
													$query = selectQuery($sql);?>
													<li class="tdw_list_li">
														<div class="tdw_list_box">
															<div class="tdw_list_chk">
																<button class="btn_tdw_list_chk tuto tuto_01_03"><span>완료체크</span></button>
															</div>
															<div class="tdw_list_desc">
																<p>리워디 업무 튜토리얼을 진행중 입니다.</p>
															</div>
															<div class="tdw_list_function new_type">
																<div class="tdw_list_function_in">
																	<div class="tdw_list_more">
																		<button class="tdw_list_o" title="메뉴열기" id=""><span>메뉴열기</span></button>
																		<div class="tdw_list_1depth" id="tdw_list_1depth" style="display:none">
																			<ul>
																				<li>
																					<button class="tdw_list_p on"><span>파티연결</span></button>
																				</li>
																				<li>
																					<button class="tdw_list_s on"><span>공유하기</span></button>
																				</li>
																				<li>
																					<button class="tdw_list_f tuto tuto_01_04"><span>파일추가</span></button>
																				</li>
																				<li>
																					<button class="tdw_list_m"><span>메모하기</span></button>
																				</li>
																				<li>
																					<button class="tdw_list_r on"><span>반복설정</span></button>
																					<div class="tdw_list_2depth">
																						<ul>
																							<li>
																								<button class="tdw_list_r_d on"><span>매일반복</span></button>
																							</li>
																							<li>
																								<button class="tdw_list_r_w"><span>매주반복</span></button>
																							</li>
																							<li>
																								<button class="tdw_list_r_m"><span>매월반복</span></button>
																							</li>
																							<li>
																								<button class="tdw_list_r_n"><span>반복안함</span></button>
																							</li>
																						</ul>
																					</div>
																				</li>
																				<li>
																					<button class="tdw_list_c"><span>일정변경</span></button>
																				</li>
																			</ul>
																		</div>
																	</div>
																</div>
															</div>
														</div>
													</li>
												<?	if($works_info['idx']){?>
														<?for($i=0; $i<count($works_info['idx']); $i++){
															$idx = $works_info['idx'][$i];
															$state = $works_info['state'][$i];
															$calstate = $works_info['calstate'][$i];
															$work_idx = $works_info['work_idx'][$i];
															$work_email = $works_info['email'][$i];
															$work_name = $works_info['name'][$i];
															$work_flag = $works_info['work_flag'][$i];
															$work_date = $works_info['workdate'][$i];
															$work_stime = $works_info['work_stime'][$i];
															$work_etime = $works_info['work_etime'][$i];
															$work_date = $works_info['workdate'][$i];
															$work_reg = $works_info['reg'][$i];
															$work_his = $works_info['his'][$i];
															$title = $works_info['title'][$i];
															$contents = $works_info['contents'][$i];
															$repeat_work_idx = $works_info['repeat_work_idx'][$i];
															$decide_flag = $works_info['decide_flag'][$i];
															$repeat_flag = $works_info['repeat_flag'][$i];
															$notice_flag = $works_info['notice_flag'][$i];
															$secret_flag = $works_info['secret_flag'][$i];
															$memo_view = $works_info['memo_view'][$i];
															$contents_view = $works_info['contents_view'][$i];
															$work_party_link = $works_info['party_link'][$i];

															//공유업무(1:공유한업무, 공유받은업무:2)
															$share_flag = $works_info['share_flag'][$i];

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

															$req_view_bt_style = "";
															//요청업무 내용 접기/펼치기(0:펼치기, 1:접기)
															if($contents_view == '1'){
																$req_view_in = " off";
																$req_view_bt = " off";
																$req_view_bt_style = " off";

															}else{
																$req_view_in = "";
																$req_view_bt = " on";
																$req_view_bt_style = "";
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
																$li_class = " share";
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
																<div class="tdw_list_box<?=($state=='1' || $calstate== '1')?" on":""?><?=$share_view_bt_style?>" id="tdw_list_box_<?=$idx?>" name="onoff_<?=$i?>">
																	<div class="tdw_list_chk">
																		<?if($work_flag=='1'){?>
																			<button class="btn_tdw_list_chk" value="<?=$idx?>" id="tdw_dlist_chk"><span>완료체크</span></button>
																		<?}else{?>
																			<button class="btn_tdw_list_chk tuto" value="<?=$idx?>" id="tdw_dlist_chk"><span>완료체크</span></button>
																		<?}?>
																	</div>
																	<div class="tdw_list_desc <?=$secret_flag == '1'?"lock":""?>">
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
																				<?}else{
																					if($work_stime != null && $work_stime != null){?>
																						<p id="tdw_list_edit_<?=$idx?>">
																							<?if($decide_flag == 1){?>
																								<span> <?= "[ ".$decide_name." ]" ?></span><?=textarea_replace($contents)?>
																							<?}else if($decide_flag > 1){?>
																								<span> <?= "[ ".$decide_name."   ".$work_stime."~".$work_etime." ]" ?></span><?=textarea_replace($contents)?>
																							<?}?>
																						</p>
																						<?}else{?>
																						<p id="tdw_list_edit_<?=$idx?>"><?=textarea_replace($contents)?></p>
																					<?}?>
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

																	<div class="tdw_list_function new_type">
																		<div class="tdw_list_function_in">
																			<?
																			//받은업무
																			//보고, 공유
																			if($work_flag=="1" && $work_idx || $share_flag=='2' && $work_idx){?>
																				<button class="tdw_list_100c" title="100코인" id="tdw_list_100c" value="<?=$idx?>"><span>100</span></button>
																			<?}?>
																			

																			<?
																			//보고받은 업무 
																			if($work_flag=="1" && $work_idx){?>
																				<button class="tdw_list_h tdw_list_reported_hart<?=$work_like_list[$work_idx]>0?" on":""?>" title="좋아요" <?=$work_like_list[$work_idx]>0?"":" id=\"tdw_list_jjim\""?> value="<?=$work_idx?>"><span>좋아요</span></button>
																			<?
																			//공유받음
																			}else if($share_flag=='2' && $work_idx){?>
																				<button class="tdw_list_h tdw_list_shared_hart<?=$work_like_list[$work_idx]>0?" on":""?>" title="좋아요" <?=$work_like_list[$work_idx]>0?"":" id=\"tdw_list_jjim\""?> value="<?=$work_idx?>"><span>좋아요</span></button>
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
																			<div class="tdw_list_drag" title="순서 변경" value="<?=$idx?>"><span>드래그 드랍 기능</span></div>
																			<div class="tdw_list_more">
																				<button class="tdw_list_o" title="메뉴열기" id=""><span>메뉴열기</span></button>
																				<div class="tdw_list_1depth">
																					<ul>
																						<li>
																							<?if(($notice_flag=='0' || $decide_flag=='0') && $share_flag!=='2' && $notice_flag!='1' && $work_flag!='4'){?>
																								<button class="tdw_list_p tdw_list_party_link <?=$project_link_info[$idx]?"on":""?>" id="tdw_list_party_link" value="<?=$idx?>" title="파티연결"><span>파티연결</span></button>
																							<?}?>
																						</li>
																						<li>
																						<?//공유하기?>
																							<?//공유한 업무?>
																							<?if($share_flag=='1' && $work_idx){?>
																								<button class="tdw_list_share_cancel tdw_list_s" id="tdw_list_share_cancel" value="<?=$idx?>" title="공유취소"><span>공유취소</span></button>
																							<?}else{?>
																								<?//나의업무작성, 공유업무작성?>
																								<?if(($work_flag=='2' && $work_idx==null) || ($share_flag=='1' && $work_idx==null)){?>
																									<button class="tdw_list_share tdw_list_s" id="tdw_list_share" value="<?=$idx?>" title="공유하기"><span>공유하기</span></button>
																								<?}?>
																							<?}?>
																						</li>
																						<li>
																							<?//파일첨부?>
																							<?//파일첨부(나의업무, 공유업무작성, 보고업무작성, 요청업무작성)?>
																							<?if(($work_flag=='2' && $work_idx==null) || ($share_flag=='1' && $work_idx) || ($work_flag=='1' && $work_idx==null) || ($work_flag=='3' && $work_idx==null)){?>
																								<button class="tdw_list_files tdw_list_f" id="tdw_file_add_<?=$idx?>" title="파일추가"><span>파일추가</span></button>
																								<input type="file" id="files_add_<?=$idx?>" style="display:none;">
																							<?}?>
																						</li>
																						<li>
																							<?//사람선택?>
																							<?//공유업무작성, 보고업무작성, 요청업무작성?>
																							<?if(($share_flag=='1' && $work_idx) || ($work_flag=='1' &&  $work_idx==null)){?>
																								<button class="tdw_list_user tdw_list_u" id="tdw_send_user_<?=$idx?>" value="<?=$idx?>" title="사람추가"><span>사람추가</span></button>
																							<?}?>

																							<?//사람선택?>
																							<?//요청업무작성?>

																							<?if($work_flag=='3' && $work_idx==null){?>
																								<button class="tdw_list_user tdw_list_u" id="tdw_send_user_<?=$idx?>" value="<?=$idx?>" title="사람추가"><span>사람추가</span></button>
																							<?}?>
																						</li>
																						<li>
																							<?//메모작성?>
																							<? if($notice_flag!='1' && $work_flag!='4'){?>
																								<?php if($secret_flag == '1'){?>
																									<button class="tdw_list_memo_secret tdw_list_m" id="tdw_list_memo" value="<?=$idx?>" title="메모하기"><span>메모하기</span></button>
																								<?php }else{ ?>
																									<button class="tdw_list_memo tdw_list_m" id="tdw_list_memo" value="<?=$idx?>" title="메모하기"><span>메모하기</span></button>
																								<?php } ?>	
																							<?}?>
																						</li>
																						<?if(($work_flag=='2' && $work_idx==null) || ($work_flag=='3' && $work_idx==null)){?>
																							<? if(($repeat_flag && ($work_date < '2023-09-19')) || $repeat_work_idx != null){ ?>
																								<li>
																									<button class="tdw_list_r <?=$repeat_flag?" on":""?>" id="tdw_list_repeat_info_new" value="<?php echo $idx?>"><span>반복설정</span></button>
																								</li>
																							<?php }else{?>
																								<li>
																									<button class="tdw_list_r <?=$repeat_flag?" on":""?>" id="tdw_list_repeat_new" value="<?php echo $idx?>"><span>반복설정</span></button>
																								</li>
																							<?php } ?>
																						<?php } ?>
																						<li>
																						<?//일정변경?>
																							<?//나의업무, 공유업무작성, 보고업무작성, 요청업무작성?>
																							<?if(($work_flag=='2' && $work_idx==null) || ($share_flag=='1' && $work_idx==null) || ($work_flag=='1' && $work_idx==null) || ($work_flag=='3' && $work_idx==null)){?>
																								<div class ="tdw_list_c">
																									<input class="tdw_list_date tdw_list_cc" type="text" id="listdate_<?=$idx?>" value="날짜변경" readonly>
																								</div>
																							<?}?>
																						</li>
																						<li>
																						<?//일정변경?>
																							<?//나의업무, 공유업무작성, 보고업무작성, 요청업무작성?>
																							<?if(($work_stime && $work_etime && $work_flag == '2' && $share_flag == '0' && $state == '0' && $decide_flag > '1')){?>
																								<button class="tdw_list_time tdw_list_t" id="tdw_list_time" value="<?=$idx?>" title="시간변경"><span>시간변경</span></button>
																							<?}?>
																						</li>
																						<li>
																							<?if($work_flag!='4'){
																								if($notice_flag){?>
																									<?if($user_id == $work_email){?>
																										<button class="tdw_list_del tdw_list_d" title="삭제" id="notice_list_del" value="<?=$idx?>"><span>삭제</span></button>
																									<?}else{?>
																										<button class="tdw_list_del tdw_list_d" title="삭제" value="<?=$idx?>"><span>삭제</span></button>
																									<?}?>
																								<?}else{?>
																								<?//업무글삭제?>
																									<?if($user_id == $work_email && $share_flag == 0 && $work_flag == 2){?>
																										<button class="tdw_list_del tdw_list_d" title="삭제" id="tdw_list_per_del" value="<?=$idx?>"><span>삭제</span></button>
																									<?}else if($user_id == $work_email){?>
																										<button class="tdw_list_del tdw_list_d" title="삭제" id="tdw_list_del" value="<?=$idx?>"><span>삭제</span></button>
																									<?}else{?>
																										<button class="tdw_list_del tdw_list_d" title="삭제" value="<?=$idx?>"><span>삭제</span></button>
																									<?}?>
																								<?}
																								}
																							?>
																						</li>
																						<li>
																							<button class="tdw_list_cancel" id="tdw_list_cancel" title="닫기"><span>닫기</span></button>
																						</li>
																					</ul>
																				</div>
																			</div>
																		</div>
																	</div>
																	

																	<?//첨부파일 정보
																	//나의업무, 요청업무
																	if(in_array($work_flag, array('2','3'))){
																		if($tdf_files[$work_com_idx]['file_path']){?>
																			<div class="tdw_list_file">
																				<?for($k=0; $k<count($tdf_files[$work_com_idx]['file_path']); $k++){?>
																					<div class="tdw_list_file_box">
																						<button class="btn_list_file" id="tdw_list_file_<?=$k?>" value="<?=$tdf_files[$work_com_idx]['idx'][$k]?>"><span><?=$tdf_files[$work_com_idx]['file_real_name'][$k]?></span></button>

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
																							<button class="btn_list_file" id="tdw_list_file_<?=$k?>" value="<?=$tdf_files[$work_com_idx]['idx'][$k]?>"><span><?=$tdf_files[$work_com_idx]['file_real_name'][$k]?></span></button>

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

																<?if($share_flag && $work_idx){?>
																	<div class="tdw_list_share_onoff"<?=$share_view_bt_style?>>
																		<button class="btn_list_share_onoff<?=($comment_list[$work_com_idx]?" memo_on": "");?><?=$share_view_bt?>" id="btn_list_share_onoff_<?=$idx?>" value="<?=$idx?>" <?if(trim($share_view_bt)=="on"){ echo "title='공유 접기'"; }else{ echo "title='공유 펼치기'"; }?>><span>공유 접기/펼치기</span></button>
																	</div> 
																<?}?>
																
																<?if($work_flag == '3'){?>
																	<div class="tdw_list_req_onoff"<?=$req_view_bt_style?>>
																		<button class="btn_list_req_onoff<?=($comment_list[$work_com_idx]?" memo_on": "");?><?=$req_view_bt?>" id="btn_list_req_onoff_<?=$idx?>" value="<?=$idx?>" <?if(trim($req_view_bt)=="on"){ echo "title='요청 접기'"; }else{ echo "title='요청 펼치기'"; }?>><span>요청 접기/펼치기</span></button>
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
											<?=
											//일일읽음처리
												work_read_check($user_id, "day", $wdate, "");
												?>
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
							<input type="text" class="input_fl" id="input_fl" placeholder="한줄소감을 남겨주세요!" />
						</div>
					</div>
					<div class="fl_bottom" id="fl_bottom">
						<button>등록하기</button>
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


	<?php
		//튜토리얼 시작 레이어
		include $home_dir . "/layer/tutorial_start.php";

		//튜토리얼 시작 레이어
		include $home_dir . "/layer/tutorial_main_level.php";

		//레이어
		include $home_dir . "/layer/tutorial_works_layer.php";
	?>

	

	<div class="rew_popup" id="rew_popup">
		<div class="rew_popup_in" id="rew_popup_in">오늘업무를 작성하여 실행 1점이 올라갔습니다.</div>
	</div>
</div>

<?//알림
		include $home_dir . "/inc_lude/work_notice.php";
	?>

	<!-- footer start-->
	<? include $home_dir . "/inc_lude/footer.php";?>
	<!-- footer end-->
	<script type="text/javascript" src = "/js/index_new.js"></script>
	<script type="text/javascript">
		$(document).ready(function(){
		
			window.onbeforeunload = function () { $('.rewardy_loading_01').css('display', 'block'); }
			$(window).load(function () {          //페이지가 로드 되면 로딩 화면을 없애주는 것
				$('.rewardy_loading_01').css('display', 'none');
			});

			length_work = $("div[id^=tdw_list_box_]").length;

			for(var i = 0; i<length_work; i++){
				btn_work = $("div[name=onoff_"+i+"]").parent().find("button[id^=btn_list_work_onoff]");
        		btn_report = $("div[name=onoff_"+i+"]").parent().find("button[id^=btn_list_report_onoff]");
				btn_share = $("div[name=onoff_"+i+"]").parent().find("button[id^=btn_list_share_onoff]");
				btn_req = $("div[name=onoff_"+i+"]").parent().find("button[id^=btn_list_req_onoff]");
				// text_1에서 특정 div 찾기
					var divElement = $("div[name=onoff_"+i+"]").parent();

					// div 하위의 p 엘리먼트 찾기
					var pElement = divElement.find("p");

					var spanElement = divElement.find(".tdw_list_report_conts_txt");

					// span 내의 텍스트 가져오기
					var extractedText = pElement.html();
					var extractedSpan = spanElement.html();
					var brTagCount = (extractedText.match(/<br>/g) || []).length;
					if(extractedSpan){
						var brTagReCount = (extractedSpan.match(/<br>/g) || []).length;
					}
				if(brTagCount < "3"){
					btn_work.css("display","none");
					btn_share.css("display","none");
					btn_req.css("display", "none");
				}else{
					btn_work.css("display","block");
          			btn_share.css("display","block");
					btn_req.css("display", "block");
				}

				if(brTagReCount < "3"){
					btn_report.css("display", "none");
				}else{
					btn_report.css("display", "block");
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
		<script>
		function toggleInputAndButton() {
			var inputElement = document.getElementById('listdate_input');
			var buttonElement = document.getElementById('listdate_button');
			
			if (inputElement.style.display === 'none') {
				inputElement.style.display = 'inline-block';
				buttonElement.style.display = 'none';
			} else {
				inputElement.style.display = 'none';
				buttonElement.style.display = 'inline-block';
			}
			}
		</script>
</body>
</html>
