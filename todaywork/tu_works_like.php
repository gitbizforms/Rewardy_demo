<?
	//header페이지
	$url = __DIR__;
	$urlWithoutLastPath = rtrim($url, '/');
	$lastPath = basename($urlWithoutLastPath);
	$home_dir = rtrim($urlWithoutLastPath, $lastPath);
	
	include $home_dir . "/inc_lude/header_index_new.php";

	$member_info = member_row_info($user_id);
	$tuto_flag = $member_info['t_flag'];	

	$tuto = tutorial_chk();
	// if($tuto['t_flag']>1){
	// 	alert('해당 단계는 이미 완료하셨습니다!');
	// 	echo "<script>history.back();</script>";
	// }else 
	if($tuto['t_flag']<1){
		alert('이전 단계를 먼저 수행해주세요.');
		echo "<script>history.back();</script>";
	}

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
			var tt_p = $(".tuto_pop_02_0"+i+"").height();
			var tt_ph = tt_p + tt_y;
			if(tt_x > tt_r){
				$(".tuto_pop_02_0"+i+"").css({
					left:"auto",
					right:70,
					opacity:1
				});
				$(".tuto_pop_02_0"+i+"").removeClass("tuto_l tuto_r").addClass("tuto_r");
			}else{
				$(".tuto_pop_02_0"+i+"").css({
					left:(tt_x-47),
					opacity:1
				});
				$(".tuto_pop_02_0"+i+"").removeClass("tuto_l tuto_r").addClass("tuto_l");
			}
			if(tt_ph > (win_h - 70)){
				$(".tuto_pop_02_0"+i+"").css({
					top:(tt_t-tt_p-24),
				});
				$(".tuto_pop_02_0"+i+"").removeClass("tuto_t tuto_b").addClass("tuto_t");
			}else{
				$(".tuto_pop_02_0"+i+"").css({
					top:(tt_y+42),
				});
				$(".tuto_pop_02_0"+i+"").removeClass("tuto_t tuto_b").addClass("tuto_b");
			}
			$(".tuto_mark_02_0"+i+"").css({
				left:tt_x,
				top:tt_y,
				opacity:1
			});
		});
	}
$(document).ready(function(){
$(".rew_box").removeClass("on");
$(".rew_menu_onoff button").removeClass("on");

setTimeout(function(){
	tuto_position();
},1300);

$(window).resize(function(){
	tuto_position();
}); 

$(window).scroll(function(){
	tuto_position();
}); 

});


$(document).on("click",".tuto_pop_02_01 .tuto_next",function(){
	$(".tuto_mark_02_01").hide();
	$(".tuto_pop_02_01").hide();

	$(".jjim_first").show();
	tuto_position();

	$(".tuto_mark_02_02").show();
	$(".tuto_pop_02_02").show();
});

$(document).on("click",".tuto_pop_02_02 .tuto_prev",function(){
$(".tuto_mark_02_02").hide();
$(".tuto_pop_02_02").hide();
$(".jjim_first").hide();

$(".tuto_mark_02_01").show();
$(".tuto_pop_02_01").show();
});

$(document).on("click",".tuto_pop_02_02 .tuto_next",function(){
$(".jjim_first").hide();
var fdata = new FormData();
fdata.append("mode","update");
fdata.append("level","like");
tuto_flag = $("#tutorial_flag").val();
if(tuto_flag > 1){
	fdata.append("not_reward","1");
}

$.ajax({
	type: "POST",
	data: fdata,
	contentType: false,
	processData: false,
	url: "/inc/tu_process.php",
	success: function (data) {
		console.log(data);
		$(".tuto_mark_02_02").hide();
		$(".tuto_pop_02_02").hide();
		// $(".phase_03").addClass("tuto_on");
		if(tuto_flag==1){
			$(".phase_03").addClass("tuto_on");
		}
		$(".phase_02").addClass("tuto_clear");
		$(".phase_02").removeClass("tuto_on");
		$(".phase_03 button").attr("onclick","location.href='/todaywork/tu_works_coin.php'");
		$(".tuto_phase").show();
	},
  });
});

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
var month_last_day = '<?=$month_last_day?>'
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
																<button class="btn_tdw_list_chk"><span>완료체크</span></button>
															</div>
															<div class="tdw_list_desc">
																<p>리워디 업무 튜토리얼을 진행중 입니다.</p>
															</div>
															<div class="tdw_list_function new_type">
																<div class="tdw_list_function_in">
																	<button class="tdw_list_100" title="코인" id=""><span>100</span></button>
																	<button class="tdw_list_h tuto tuto_02_01" title="좋아요" id=""><span>좋아요</span></button>
																	<div class="tdw_list_more">
																		<button class="tdw_list_o" title="메뉴열기" id=""><span>메뉴열기</span></button>
																		<div class="tdw_list_1depth" id="tdw_list_1depth" style="display:none">
																			<ul>
															
																			</ul>
																		</div>
																	</div>
																</div>
															</div>
														</div>
													</li>
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
		//좋아요 레이어
		include $home_dir . "/layer/member_tu_jjim.php";
									
		//튜토리얼 시작 레이어
		include $home_dir . "/layer/tutorial_start.php";

		//튜토리얼 시작 레이어
		include $home_dir . "/layer/tutorial_main_level.php";

		//레이어
		include $home_dir . "/layer/tutorial_like_layer.php";
	?>

	

	<div class="rew_popup" id="rew_popup">
		<div class="rew_popup_in" id="rew_popup_in">오늘업무를 작성하여 실행 1점이 올라갔습니다.</div>
	</div>
</div>

<?
		include $home_dir . "/inc_lude/work_notice.php";
	?>

	<!-- footer start-->
	<? include $home_dir . "/inc_lude/footer.php";?>
	<!-- footer end-->
	<script type="text/javascript" src = "/js/index_new.js"></script>
</body>
</html>
