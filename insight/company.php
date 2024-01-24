<?
	//header페이지
	$home_dir = str_replace( basename(__DIR__) , "" , __DIR__ );
	include $home_dir . "/inc_lude/header.php";

	//오늘날짜
	$yday = date("Y-m-d", strtotime($today." -1 day"));
	$wdate = str_replace("-",".",$today);
	$ydate = str_replace("-",".",$yday);
	$sel_wdate = str_replace(".", "-" , $wdate);
	$sel_ydate = str_replace(".", "-" , $ydate);

	//날짜변환
if($wdate){
	$m_wdate = str_replace(".","-",$wdate);
}

$month_tmp = explode(".",$wdate);
if($month_tmp){
	$month_date = $month_tmp[0].".".$month_tmp[1];
}

	//현재일자기준으로 한주간 체크
	$date_tmp = explode("-",$m_wdate);

	if($date_tmp){
		$year = $date_tmp[0];
		$month = $date_tmp[1];
		if(!$date_tmp[2]){
			$date_tmp[2] = "01";
		}
		$day = $date_tmp[2];
	}

	$sel_day = date("t",mktime(0, 0, 0, $month, $day, $year));				// 지정된 달은 몇일까지 있을까요?
	$sel_yoil = date("N",mktime(0, 0, 0, $month, 1, $year));				// 지정된 달의 첫날은 무슨요일일까요?
	$day_line = $sel_yoil%7;												// 지정된 달 1일 앞의 공백 숫자.
	$ra = ($sel_day + $day_line)/7;
	$ra = ceil($ra);
	$ra = $ra-1;															// 지정된 달은 총 몇주로 라인을 그어야 하나?

	//월간업무
	$ym = $year."-".$month;

	$sql = "select 
	b.email as email,
    count(case when (b.work_flag = '2' and b.share_flag = '0' and b.notice_flag = '0') then 1 end) as work,
    count(case when (b.work_flag = '1' and b.work_idx is null) then 1 end) as report_to,
    count(case when (b.work_flag = '1' and b.work_idx is not null) then 1 end) as report_from,
    count(case when (b.work_flag = '3'and b.work_idx is null) then 1 end) as request_to,
	count(case when (b.work_flag = '3'and b.work_idx is not null) then 1 end) as request_from,
    count(case when b.share_flag = '1' then 1 end) as share_to,
    count(case when b.share_flag = '2' then 1 end) as share_from,
    (select count(*) from work_todaywork_comment where email = '".$user_id."' and companyno = '".$companyno."'  and comment is not null and workdate = '".$sel_wdate."') as comment,
    a.coin as coin,
    a.comcoin as comcoin,
    (select count(*) from work_todaywork_like where email = '".$user_id."' and companyno = '".$companyno."' and comment is not null and workdate = '".$sel_wdate."') as great_to,
    (select count(*) from work_todaywork_like where send_email = '".$user_id."' and companyno = '".$companyno."' and comment is not null and workdate = '".$sel_wdate."') as great_from,
	(SELECT count(idx) FROM work_coininfo where email = '".$user_id."' and code in ('500','600','700','900','1000','1100') and companyno = '".$companyno."' and workdate = '".$sel_wdate."') as reward_to,
	(SELECT count(idx) FROM work_coininfo where email = '".$user_id."' and code not in ('500','600','700','900','1000','1100') and companyno = '".$companyno."' and workdate = '".$sel_wdate."') as reward_from
    from
    work_todaywork b 
    left join work_member a on b.email = a.email
    left join calendar_events c on b.idx = c.work_idx
    where 1=1
    and a.email = '".$user_id."'
    and a.state = '0'
	and b.state != '9'
	and (c.state != '9' or c.state is null)
	and (b.workdate = '".$sel_wdate."' or c.start_date = '".$sel_wdate."')
    and b.companyno = '".$companyno."'";
	$new_chart = selectQuery($sql);

	$sql = "select 
	b.email as email,
    floor(COUNT(CASE WHEN (b.work_flag = '2' and b.share_flag = '0' and b.notice_flag = '0') THEN 1 END) / 
	count(DISTINCT CASE WHEN (b.work_flag = '2' and b.share_flag = '0' and b.notice_flag = '0') THEN b.email END)) as work,
	floor(count(case when (b.work_flag = '1' and b.work_idx is null) then 1 end) / 
	count(distinct case when (b.work_flag = '1' and b.work_idx is null) then b.email end)) as report_to,
	floor(count(case when (b.work_flag = '1' and b.work_idx is not null) then 1 end) / 
	count(distinct case when (b.work_flag = '1' and b.work_idx is not null) then b.email end)) as report_from,
	floor(count(case when (b.work_flag = '3' and b.work_idx is null) then 1 end) / 
	count(distinct case when (b.work_flag = '3' and b.work_idx is null) then b.email end)) as request_to,
	floor(count(case when (b.work_flag = '3' and b.work_idx is not null) then 1 end) / 
	count(distinct case when (b.work_flag = '3' and b.work_idx is not null) then b.email end)) as request_from,
	floor(count(case when b.share_flag = '1' then 1 end) / 
	count(distinct case when b.share_flag = '1' then b.email end)) as share_to,
	floor(count(case when b.share_flag = '2' then 1 end) / 
	count(distinct case when b.share_flag = '2' then b.email end)) as share_from,
    (select floor(count(idx) / count(distinct(email))) from work_todaywork_comment where companyno = '".$companyno."'  and comment is not null and workdate = '".$sel_wdate."') as comment,
    (select sum(coin) from work_member where state = '0' and companyno = '1') as coin,
    (select sum(comcoin) from work_member where state = '0' and companyno = '1') as comcoin,
    (select floor(count(idx) / count(distinct(email))) from work_todaywork_like where companyno = '".$companyno."' and comment is not null and workdate = '".$sel_wdate."') as great_to,
	(select floor(count(idx) / count(distinct(send_email))) from work_todaywork_like where companyno = '".$companyno."' and comment is not null and workdate = '".$sel_wdate."') as great_from,
    (SELECT floor(count(idx) / count(distinct(email))) FROM work_coininfo where code in ('500','600','700','900','1000','1100') and  companyno = '".$companyno."' and workdate = '".$sel_wdate."') as reward_to,
	(SELECT floor(count(idx) / count(distinct(email))) FROM work_coininfo where code not in ('500','600','700','900','1000','1100') and  companyno = '".$companyno."' and workdate = '".$sel_wdate."') as reward_from
	from
    work_todaywork b 
    left join work_member a on b.email = a.email
    where 1=1
    and a.state = '0'
	and b.state != '9'
	and b.workdate = '".$sel_wdate."'
    and b.companyno = '".$companyno."'";
	$new_chart_all = selectQuery($sql);
	$sql = "select DATE_FORMAT(
		SEC_TO_TIME(
			AVG(TIME_TO_SEC(STR_TO_DATE(worktime, '%H:%i')))
		),
		'%H:%i'
	) as work_in from work_member_login where companyno = '".$companyno."' and  workdate = '".$sel_wdate."'";
	$avg_work_in_all = selectQuery($sql);

	$sql = "select worktime
	as work_in from work_member_login where companyno = '".$companyno."' and email = '".$user_id."' and  workdate = '".$sel_wdate."'";
	$avg_work_in = selectQuery($sql);

	$sql = "select DATE_FORMAT(
		SEC_TO_TIME(
			AVG(TIME_TO_SEC(STR_TO_DATE(date_format(regdate, '%H:%i'), '%H:%i')))
		),
		'%H:%i'
	) as work_out FROM work_member_logoff where companyno = '".$companyno."' and  workdate = '".$sel_ydate."'";
	$avg_work_out_all = selectQuery($sql);
	
	$sql = "select date_format(regdate, '%H:%i')
	as work_out from work_member_logoff where companyno = '".$companyno."' and email = '".$user_id."' and  workdate = '".$sel_ydate."'";
	$avg_work_out = selectQuery($sql);


	// $hour1과 $minute1 변수에 시간과 분 값이 할당됩니다

	// ':'를 구분자로 사용하여 시간 및 분 추출
	list($hour1, $minute1) = explode(':', $avg_work_in_all['work_in']);
	list($hour2, $minute2) = explode(':', $avg_work_out_all['work_out']);
	list($hour3, $minute3) = explode(':', $avg_work_in['work_in']);
	list($hour4, $minute4) = explode(':', $avg_work_out['work_out']);
	
	// 시간 및 분을 정수로 변환
	$hour1 = (int)$hour1;
	$minute1 = (int)$minute1;
	$hour2 = (int)$hour2;
	$minute2 = (int)$minute2;
	$hour3 = (int)$hour3;
	$minute3 = (int)$minute3;
	$hour4 = (int)$hour4;
	$minute4 = (int)$minute4;
	
	// 두 시간 간의 차이 계산
	$hour_difference_all = $hour2 - $hour1;
	$minute_difference_all = $minute2 - $minute1;
	$hour_difference = $hour4 - $hour3;
	$minute_difference = $minute4 - $minute3;
	
	if ($hour_difference_all < 0 ) {
		$hour_difference_all += 24; // 음수 시간 차이 보정
	}else if($hour_difference < 0){
    $hour_difference += 24;
	}

	// 분 차이 보정
	if ($minute_difference_all < 0) {
	$minute_difference_all += 60;
	$hour_difference_all -= 1; // 음수 시간 차이 보정
	}else if($minute_difference < 0){
	$minute_difference += 60;
	$hour_difference -= 1; // 음수 시간 차이 보정
	}

?>
<script type="text/javascript">
	$(document).ready(function(){

	});
</script>
<input type="hidden" id="r_work_wdate" value="<?=$wdate?>">
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
						<div class="rew_ins_func">
							<div class="rew_ins_func_in">
								<div class="rew_ins_func_title">
									<strong>평균</strong><!-- 좋아요 : 하트킹 -->
								</div>

								<div class="rew_ins_calendar">
								<div class="tdw_ins_date">
										<input type="text" class="input_date" value="<?=$wdate?>" maxlength="10" id="r_work_date">
										<input type="text" class="input_date" id="r_work_month" value="<?=$month_date?>" data-min-view="months" data-view="months" data-date-format="yyyy.mm" readonly="readonly" style="display:none;">
									</div>
									<div class="tdw_ins_tab_03">
										<div class="tdw_ins_tab_in">
											<ul>
												<li>
													<button class="select_c_dd on"><span>일간</span></button>
												</li>
												<li>
													<button class="select_c_ww"><span>주간</span></button>
												</li>
												<li>
													<button class="select_c_mm"><span>월간</span></button>
												</li>
											</ul>
										</div>
									</div>
								</div>
							</div>
						</div>

						<div class="rew_conts_scroll_12">

						<div class="rew_ins">
								<div class ="rew_ins_c_dd">
								<div class="ins_rank_graph">
									<div class="ins_rank_graph_in">
									<ul style = "padding-top : 20px;">
										<li class="ir_rank">
												<div class="ir_name_user"><?=$avg_work_in['work_in']?></div>
												<div class="ir_name_team">출근 시간</div>
										</li>
										<li class="ir_rank">
												<div class="ir_name_user"><?=$avg_work_out['work_out']?$avg_work_out['work_out']:"없음"?></div>
												<div class="ir_name_team">퇴근 시간</div>
										</li>
										<li class="ir_rank">
												<div class="ir_name_user"><?=$avg_work_out['work_out']?$hour_difference.".".$minute_difference."시간":"없음"?></div>
												<div class="ir_name_team">근무 시간</div>
										</li>
										<li class="ir_rank">
												<div class="ir_name_user"><?=$new_chart['work']?></div>
												<div class="ir_name_team">업무</div>
										</li>
										<li class="ir_rank">
												<div class="ir_name_user"><?=$new_chart['report_to']." | ".$new_chart['report_from']?></div>
												<div class="ir_name_team">보고</div>
										</li>
										<li class="ir_rank">
												<div class="ir_name_user"><?=$new_chart['request_to']." | ".$new_chart['request_from']?></div>
												<div class="ir_name_team">요청</div>
										</li>
										<li class="ir_rank">
												<div class="ir_name_user"><?=$new_chart['share_to']." | ".$new_chart['share_from']?></div>
												<div class="ir_name_team">공유</div>
										</li>
										<li class="ir_rank">
												<div class="ir_name_user"><?=$new_chart['comment']?></div>
												<div class="ir_name_team">댓글</div>
										</li>
										<li class="ir_rank">
												<div class="ir_name_user"><?=number_format($new_chart['coin'])?> 코인</div>
												<div class="ir_name_team">내 코인</div>
										</li>
										<li class="ir_rank">
												<div class="ir_name_user"><?=number_format($new_chart['comcoin'])?> 코인</div>
												<div class="ir_name_team">공용 코인</div>
										</li>
										<li class="ir_rank">
												<div class="ir_name_user"><?=$new_chart['great_to']." | ".$new_chart['great_from']?></div>
												<div class="ir_name_team">좋아요</div>
										</li>
										<li class="ir_rank">
												<div class="ir_name_user"><?=$new_chart['reward_to']." | ".$new_chart['reward_from']?>회</div>
												<div class="ir_name_team">보상지급</div>
										</li>
									</ul>
									<ul style = "padding-top : 100px;">
										<li class="ir_rank">
												<div class="ir_name_user"><?=$avg_work_in_all['work_in']?></div>
												<div class="ir_name_team">출근 시간</div>
										</li>
										<li class="ir_rank">
												<div class="ir_name_user"><?=$avg_work_out_all['work_out']?$avg_work_out_all['work_out']:"없음"?></div>
												<div class="ir_name_team">퇴근 시간</div>
										</li>
										<li class="ir_rank">
												<div class="ir_name_user"><?=$hour_difference_all.".".$minute_difference_all?>시간</div>
												<div class="ir_name_team">근무 시간</div>
										</li>
										<li class="ir_rank">
												<div class="ir_name_user"><?=$new_chart_all['work']?$new_chart_all['work']:"0"?></div>
												<div class="ir_name_team">업무</div>
										</li>
										<li class="ir_rank">
												<div class="ir_name_user"><?=$new_chart_all['report_to']?$new_chart_all['report_to']:"0"?> | <?=$new_chart_all['report_from']?$new_chart_all['report_from']:"0"?></div>
												<div class="ir_name_team">보고</div>
										</li>
										<li class="ir_rank">
												<div class="ir_name_user"><?=$new_chart_all['request_to']?$new_chart_all['request_to']:"0"?> | <?=$new_chart_all['request_from']?$new_chart_all['request_from']:"0"?></div>
												<div class="ir_name_team">요청</div>
										</li>
										<li class="ir_rank">
												<div class="ir_name_user"><?=$new_chart_all['share_to']?$new_chart_all['share_to']:"0"?> | <?=$new_chart_all['share_from']?$new_chart_all['share_from']:"0"?></div>
												<div class="ir_name_team">공유</div>
										</li>
										<li class="ir_rank">
												<div class="ir_name_user"><?=$new_chart_all['comment']?$new_chart_all['comment']:"0"?></div>
												<div class="ir_name_team">댓글</div>
										</li>
										<li class="ir_rank">
												<div class="ir_name_user"><?=number_format($new_chart_all['coin'])?> 코인</div>
												<div class="ir_name_team">내 코인</div>
										</li>
										<li class="ir_rank">
												<div class="ir_name_user"><?=number_format($new_chart_all['comcoin'])?> 코인</div>
												<div class="ir_name_team">공용 코인</div>
										</li>
										<li class="ir_rank">
												<div class="ir_name_user"><?=$new_chart_all['great_to']?$new_chart_all['great_to']:"0"?> | <?=$new_chart_all['great_from']?$new_chart_all['great_from']:"0"?></div>
												<div class="ir_name_team">좋아요</div>
										</li>
										<li class="ir_rank">
												<div class="ir_name_user"><?=$new_chart_all['reward_to']?$new_chart_all['reward_to']:"0"?> | <?=$new_chart_all['reward_from']?$new_chart_all['reward_from']:"0"?>회</div>
												<div class="ir_name_team">보상 지급</div>
										</li>
									</ul>
									</div>
								</div>
								</div>
								<div class="rew_ins_c_ww" style="display:none"></div>
								<div class="rew_ins_c_mm" style="display:none"></div>
						</div>

					</div>
				</div>
				<!-- //콘텐츠 -->
			</div>
		</div>
	</div>
</div>
	<?php
		//튜토리얼 레벨 레이어
		include $home_dir . "loading.php";
	?>
	<?php
		//튜토리얼 레벨 레이어
		include $home_dir . "/layer/tutorial_main_level.php";
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
	});
	window.onpageshow = function(event) {
 	     if ( event.persisted || (window.performance && window.performance.navigation.type == 2)) {
			  $('.rewardy_loading_01').css('display', 'none');
  		  }
		}
</script>
</body>


</html>
