<?
	//header페이지
	$home_dir = str_replace( basename(__DIR__) , "" , __DIR__ );
	include $home_dir . "/inc_lude/back_header.php";

	//오늘날짜
    $today = TODATE;
   
	//전주날짜
	$timestamp = strtotime("-1 weeks");
	$last_week = date("Y-m-d", $timestamp);

    //월간날짜
    $timestamp2 = strtotime("-11 months");
    $month_time = date("Y-m", $timestamp2);
    $tomonth = date("Y-m");
    // 날짜 수치로 변환(사용중지 09.08)
    // $date1 = new DateTime($today);
    // $date2 = new DateTime($last_week);

    // $interval = $date2->diff($date1);
    // $date_cnt = $interval->days;
    $url = "backlike_total";
    
    //일간 막대그래프
    $sql = "select count(idx) as cnt, workdate from work_todaywork_like where state = '0' and workdate >= '".$last_week."' and workdate <= '".$today."' group by workdate order by workdate desc ";
    $daycount = selectAllQuery($sql);

    //월간 막대그래프
    $sql = "select count(idx) as cnt, DATE_FORMAT(workdate, '%Y-%m') as fworkdate from work_todaywork_like where state = '0' and workdate >= '".$month_time."' and workdate <= '".$today."' group by fworkdate order by fworkdate desc ";
    $monthcount = selectAllQuery($sql);

    //오늘 업무 수치
    $sql = "select count(idx) as cnt from work_todaywork_like where state = '0' and service = 'main' ";
    $query = selectQuery($sql);
    $nocal_main = $query['cnt'];
    $sql = $sql .= "and (workdate >= '".$last_week."' and workdate <= '".$today."')";
    $query = selectQuery($sql);
    $main = $query['cnt'];

    //오늘 보고 수치
    $sql = "select count(idx) as cnt from work_todaywork_like where state = '0' and service = 'live' ";
    $query = selectQuery($sql);
    $nocal_live = $query['cnt'];
    $sql = $sql .= "and (workdate >= '".$last_week."' and workdate <= '".$today."')";
    $query = selectQuery($sql);
    $live = $query['cnt'];

    //오늘 요청 수치
    $sql = "select count(idx) as cnt from work_todaywork_like where state = '0' and service = 'memo' ";
    $query = selectQuery($sql);
    $nocal_memo = $query['cnt'];
    $sql = $sql .= "and (workdate >= '".$last_week."' and workdate <= '".$today."')";
    $query = selectQuery($sql);
    $memo = $query['cnt'];
    
    //오늘 공유 수치
    $sql = "select count(idx) as cnt from work_todaywork_like where state = '0' and service = 'party' ";
    $query = selectQuery($sql);
    $nocal_project = $query['cnt'];
    $sql = $sql .= "and (workdate >= '".$last_week."' and workdate <= '".$today."')";
    $query = selectQuery($sql);
    $project = $query['cnt'];
    
    //오늘 공유 수치
    $sql = "select count(idx) as cnt from work_todaywork_like where state = '0' and service = 'work' ";
    $query = selectQuery($sql);
    $nocal_work = $query['cnt'];
    $sql = $sql .= "and (workdate >= '".$last_week."' and workdate <= '".$today."')";
    $query = selectQuery($sql);
    $work = $query['cnt'];

    $all_total = $nocal_main + $nocal_live + $nocal_work + $nocal_memo + $nocal_project;
    $week_total = $main + $live + $project + $memo + $work;
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
        <meta name="description" content="" />
        <meta name="author" content="" />
        <title>RewardyBackoffice</title>
    </svg>
    </head>
    <body class="sb-nav-fixed">
		<nav class="sb-topnav navbar navbar-expand navbar-dark bg-dark">
			<?php include "admin_top.php"; ?>
		</nav>
		<div id="layoutSidenav">
            <?php include "admin_sidebar.php"; ?>
            <div id="layoutSidenav_content">
                    <div class="container-fluid px-4" id="page-screen">
                        <h1 class="mt-4">좋아요 누계 차트</h1>
                        <!-- <div class="card mb-4">
                            <div class="card-body">
                                DataTables is a third party plugin that is used to generate the demo table below. For more information about DataTables, please visit the
                                <a target="_blank" href="https://datatables.net/">official DataTables documentation</a>
                                .
                            </div>
                        </div> -->
                        <div class="card mb-4">
                            <div class="card-body">
								<div class="rew_member_sub_func_tab my-1">
									<div class="rew_member_sub_func_tab_in">
										<div class="rew_member_sub_func_sort" id="back_work_total_code"></div>
										<div class="rew_member_sub_func_calendar">
											<div class="rew_member_sub_func_period">
                                                <div class="rew_list_char" id="back_work_total_code">
                                                    <button class="rew_list_char_select on" id="btn_sort_on"><span>전체</span></button>
                                                    <ul>
                                                        <li><button value="all"><span>전체보기</span></button></li>
														<li><button value="main"><span>메인페이지</span></button></li>
														<li><button value="live"><span>라이브</span></button></li>
														<li><button value="memo"><span>메모</span></button></li>
														<li><button value="party"><span>파티</span></button></li>
														<li><button value="work"><span>오늘업무</span></button></li>
                                                    </ul>
                                                </div>
												<div class="rew_member_sub_func_period_box">
													<button class="btn_calendar_l" id="btn_calendar_l">달력</button>
													<input type="text" class="input_date_l" value="<?=$last_week?>" id="backcoin_sdate">
													<span>~</span>
													<input type="text" class="input_date_r" value="<?=$today?>" id="backcoin_edate">
												</div>
												<!-- <div class="rew_cha_search_box mx-1">
													<input type="text" class="input_search" id="backcoin_search" placeholder="키워드">
													<button id="backcoin_search_btn"><span>검색</span></button>
												</div> -->
												<button type="submit" class="btn_inquiry btn_check" id="btn_total"><span>조회</span></button>
												<button type="submit" class="btn_inquiry btn_reset" id="cal_total"><span>날짜 초기화</span></button>
											</div>
										</div>
									</div>
								</div>
                                <div class="container-fluid ">
                                    <input type="hidden" value="like_total" id="total_type">
                                    <div class="row position-relative">
                                        <div class="" style="width:48%">
                                            <div class="card mt-2">
                                                <div class="card-header" style="height:30px;">
                                                    <i class="fas fa-chart-bar me-1"></i>
                                                    <span class="align-middle">일주일간 좋아요 통계 </span>
                                                    <div class="btn-group btn-group-sm float-end" id="backwork_radio" role="group" aria-label="Basic radio toggle button group">
                                                        <input type="radio" class="btn-check" name="btnradio" id="btnradio1" value="day" autocomplete="off" checked>
                                                        <label class="btn btn-outline-dark" for="btnradio1">일간</label>
                                                        <input type="radio" class="btn-check" name="btnradio" id="btnradio2" value="month" autocomplete="off">
                                                        <label class="btn btn-outline-dark" for="btnradio2">월간</label>
                                                    </div>
                                                </div>
                                                <div class="card-body" id="work_graph">
                                                    <canvas id="myBarChart" width="100%" height="50" style="display:hidden"></canvas>
                                                    <? for($i=0;$i<count($daycount['workdate']);$i++){?>
                                                    <input type="hidden" class="bar_cnt_<?=$i?>" value="<?=$daycount['cnt'][$i]?>"> 
                                                    <input type="hidden" class="bar_today_<?=$i?>" value="<?=$daycount['workdate'][$i]?>">
                                                    <?}?>
                                                    <!-- <canvas id="monthBarChart" width="100%" height="50" ></canvas> -->
                                                    <? for($i=0;$i<=11;$i++){?>
                                                    <input type="hidden" class="bar_cnt_month_<?=$i?>" value="<?=$monthcount['cnt'][$i]?>"> 
                                                    <input type="hidden" class="bar_month_<?=$i?>" value="<?=$monthcount['fworkdate'][$i]?>">
                                                    <?}?>
                                                    <input type="hidden" id="code" value="all">
                                                </div>
                                                <div class="card-footer small text-muted">업데이트 일자 : <?=TODATE?></div>
                                            </div>
                                        </div>
                                        <div class="" style="width:48%">
                                            <div class="card mt-2 rew_pie_chart">
                                                <div class="card-header" style="height:30px;">
                                                    <i class="fas fa-chart-pie me-1"></i>
                                                    <span class="align-middle">업무 유형별 비율</span>
                                                </div>
                                                <div class="card-body" id="work_doughnut">
                                                    <canvas id="myPieChart" width="100%" height="50"></canvas>
                                                    <input type="hidden" id="pie_value_0" value="<?=$main?>">
                                                    <input type="hidden" id="pie_value_1" value="<?=$live?>">
                                                    <input type="hidden" id="pie_value_2" value="<?=$memo?>">
                                                    <input type="hidden" id="pie_value_3" value="<?=$project?>">
                                                    <input type="hidden" id="pie_value_4" value="<?=$work?>">
                                                </div>
                                                <div class="card-footer small text-muted">업데이트 일자 : <?=TODATE?></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <table class="table mx-2 table-bordered rounded mt-3 " id="backoff_table" style="width:70%">
                                    <thead>
                                        <tr>
                                            <th><div class="back_sortkind" value="">전체</div></th>
                                            <th><div class="back_sortkind" value="main">메인<input type="hidden" id="pie_title_0" value="메인"></div></div></th>
                                            <th><div class="back_sortkind" value="live">라이브<input type="hidden" id="pie_title_1" value="라이브"></div></th>
                                            <th><div class="back_sortkind" value="memo">메모<input type="hidden" id="pie_title_2" value="메모"></div></th>
                                            <th><div class="back_sortkind" value="party">파티<input type="hidden" id="pie_title_3" value="파티"></div></th>
                                            <th><div class="back_sortkind" value="work">업무<input type="hidden" id="pie_title_4" value="업무"></div></th>
                                            <th><div class="back_sortkind" value="all">통계</div></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?
											?>
											<tr>
												<td>전체누적</td>
												<td><?=number_format($nocal_main)?></td>
												<td><?=number_format($nocal_live)?></td>
												<td><?=number_format($nocal_memo)?></td>
												<td><?=number_format($nocal_project)?></td>
                                                <td><?=number_format($nocal_work)?></td>
												<td><?=number_format($all_total)?></td>
											</tr>
                                            <tr>
												<td><?=$last_week."-".$today?></td>
												<td><?=number_format($main)?></td>
												<td><?=number_format($live)?></td>
												<td><?=number_format($memo)?></td>
												<td><?=number_format($project)?></td>
												<td><?=number_format($work)?></td>
                                                <td><?=number_format($week_total)?></td>
											</tr>        
                                    </tbody>
                                </table>
                            </div>
                            
                        </div>
                    </div>
                <footer class="py-4 bg-light mt-auto">
                    <div class="container-fluid px-4">
                        <div class="d-flex align-items-center justify-content-between small">
                            <div class="text-muted">Bizforms &copy; BETTERMONDAY</div>
                        </div>
                    </div>
                </footer>
            </div>
        </div> 
    </body>
    <!-- <script>
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
    </script> -->
    <?php
        //로딩 페이지
        include "../loading.php";
    ?>
    <script>
        $(document).ready(function(){
            window.onbeforeunload = function () { $('.rewardy_loading_01').css('display', 'block'); }
            $(window).load(function () {          //페이지가 로드 되면 로딩 화면을 없애주는 것
                $('.rewardy_loading_01').css('display', 'none');
            });
            
            doughnutGraph();
            barChartGraph();
        });
        window.onpageshow = function(event) {
        if ( event.persisted || (window.performance && window.performance.navigation.type == 2)) {
            $('.rewardy_loading_01').css('display', 'none');
        }
        }
    </script>
</html>