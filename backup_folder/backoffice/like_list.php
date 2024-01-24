<?
	//header페이지
	$home_dir = str_replace( basename(__DIR__) , "" , __DIR__ );

	include $home_dir . "/inc_lude/back_header.php";
	include $home_dir . "/backoffice/back_common.php";

	//오늘날짜
    $today = TODATE;

	//전월날짜
	$timestamp = strtotime("-1 weeks");
	$last_week = date("Y-m-d", $timestamp);

    $url = "backlike_list";
	$string = "&page=".$url;

    $p = $_POST['p']?$_POST['p']:$_GET['p'];
	if (!$p){
		$p = 1;
	}

	$pagingsize = 5;					//페이징 사이즈
	$pagesize = 15;						//페이지 출력갯수
	$startnum = 0;						//페이지 시작번호
	$endnum = $p * $pagesize;			//페이지 끝번호

	//시작번호
	if ($p == 1){
		$startnum = 0;
	}else{
		$startnum = ($p - 1) * $pagesize;
	}
    $where = "";
    $sql = "select count(wc.idx) as cnt from work_todaywork_like as wc, work_member as wm where wc.email = wm.email and wc.state ='0' and wm.state = '0' and (wc.workdate >= '".$last_week."' and wc.workdate <= '".$today."')";
	$history_info_cnt = selectQuery($sql);
	if($history_info_cnt['cnt']){ 
		$total_count = $history_info_cnt['cnt'];
	}
	$code = "all";
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
                        <h1 class="mt-4">좋아요 리스트</h1>
                        <!-- <div class="card mb-4">
                            <div class="card-body">
                                DataTables is a third party plugin that is used to generate the demo table below. For more information about DataTables, please visit the
                                <a target="_blank" href="https://datatables.net/">official DataTables documentation</a>
                                .
                            </div>
                        </div> -->
                        <div class="card mb-4">
                            <div class="card-body">
								<input type="hidden" id="backoffice_type" value="backlike_list">
								<div class="rew_member_sub_func_tab my-1">
									<div class="rew_member_sub_func_tab_in">
										<div class="rew_member_sub_func_sort" id="rew_member_sub_func_sort"></div>
										<div class="rew_member_sub_func_calendar">
											<div class="rew_member_sub_func_period">
												<div class="rew_list_char" id="rew_list_char">
													<button class="rew_list_char_select on" id="btn_sort_on"><span>전체보기</span></button>
													<ul>
														<li><button value="all"><span>전체보기</span></button></li>
														<li><button value="main"><span>메인페이지</span></button></li>
														<li><button value="lives"><span>라이브</span></button></li>
														<li><button value="memo"><span>메모</span></button></li>
														<li><button value="project"><span>파티</span></button></li>
														<li><button value="work"><span>오늘업무</span></button></li>
													</ul>
												</div>
												<div class="rew_member_sub_func_period_box">
													<button class="btn_calendar_l" id="btn_calendar_l">달력</button>
													<input type="text" class="input_date_l" value="<?=$last_week?>" id="backcoin_sdate">
													<span>~</span>
													<input type="text" class="input_date_r" value="<?=$today?>" id="backcoin_edate">
												</div>
												<div class="rew_cha_search_box mx-1">
													<input type="text" class="input_search" id="backcoin_search" placeholder="키워드">
													<button id="backcoin_search_btn"><img src="../html/images/pre/ico_c_search.png" alt=""></button>
												</div>
												<button type="submit" class="btn_inquiry btn_check" id="btn_history"><span>조회</span></button>
												<button type="submit" class="btn_inquiry btn_reset" id="cal_history"><span>날짜 초기화</span></button>
											</div>
										</div>
										<div class="rew_member_sub_func_sort" id="rew_member_sub_func_list">
											<div class="rew_member_sub_func_sort_in">
												<button class="btn_sort_on" id="btn_sort_on"><span>15</span></button>
												<ul>
													<li value="10"><button><span>10</span></button></li>
													<li value="15"><button><span>15</span></button></li>
													<li value="30"><button><span>30</span></button></li>
													<li value="50"><button><span>50</span></button></li>
													<li value="100"><button><span>100</span></button></li>
												</ul>
											</div>
										</div>
									</div>	
								</div>
                                <table class="table table-bordered rounded-1" id="backoff_table" class="rounded-1">
                                    <thead>
                                        <tr>
                                            <th class="like_date"><div class="back_sortkind" value="regdate">등록시간<button class="list_arrow" value="btn_sort_down"></button></div></th>
                                            <th class="like_user"><div class="back_sortkind" value="send_name">보낸사람<button class="list_arrow" value="btn_sort_down"></button></div></th>
                                            <th class="like_user"><div class="back_sortkind" value="name">받은사람<button class="list_arrow" value="btn_sort_down"></button></div></th>
                                            <th class="like_service"><div class="back_sortkind" value="service">서비스유형<button class="list_arrow" value="btn_sort_down"></button></div></th>
                                            <th class="like_memo"><div class="back_sortkind" value="comment">메모<button class="list_arrow" value="btn_sort_down"></button></div></th>
                                            <th class="like_comp"><div class="back_sortkind" value="company">회사<button class="list_arrow" value="btn_sort_down"></button></div></th>
                                        </tr>
                                    </thead>
                                    <!-- <tfoot>
                                        <tr>
                                            <th>Name</th>
                                            <th>Position</th>
                                            <th>Office</th>
                                            <th>Age</th>
                                            <th>Start date</th>
                                            <th>Salary</th>
                                        </tr>
                                    </tfoot> -->
                                    <tbody>
                                        <?
											$sql = "select wc.idx, wc.regdate, wc.send_name, wc.send_email, wc.name, wc.email, wc.service, wm.company, wc.comment from work_todaywork_like as wc, work_member as wm where wc.email = wm.email and wc.state ='0' and wm.state = '0' and (wc.workdate >= '".$last_week."' and wc.workdate <= '".$today."') order by wc.idx desc limit ".$startnum.",".$pagesize;
                                            $query = selectAllQuery($sql);

											for($i=0; $i<count($query['idx']); $i++){
												$mem_idx = $query['idx'][$i];
												$likekind = $query['service'][$i];
												$code_arr = ['main','lives','memo','project','work','challenge'];
												$title_arr = ['메인페이지','라이브','메모','파티','오늘업무','챌린지'];
												for($j=0;$j<count($code_arr);$j++){
													if($likekind == $code_arr[$j]){
														$like_kind = $title_arr[$j];
													}		
												}
											?>
											<tr>
												<td><?=$query['regdate'][$i]?></td>
												<td><?=$query['send_name'][$i]."(".$query['send_email'][$i].")"?></td>
												<td><?=$query['name'][$i]."(".$query['email'][$i].")"?></td>
												<td><?=$like_kind?></td>
												<td class="like_memo"><p><?=$query['comment'][$i]?></p></td>
												<td><?=$query['company'][$i]?></td>
											</tr>
										<?}?>
										<input type="hidden" id="tclass" value="btn_sort_down">
										<input type="hidden" value="<?=$pagesize?>" id="list_cnt" >
										<input type="hidden" id="kind" value="idx">
										<input type="hidden" id="code" value="<?=$code?>">
										<input type="hidden" id="backoff_sdate" value="<?=$last_week?>">
										<input type="hidden" id="backoff_edate" value="<?=$today?>">
                                    </tbody>
                                </table>
								<div id="back_pagelist">
									<?php echo back_pageing($pagingsize, $total_count, $pagesize, $string)?>
								</div>
                            </div>
                        </div>
                    </div>
                <footer class="py-4 bg-light mt-auto">
                    <div class="container-fluid px-4">
                        <div class="d-flex align-items-center justify-content-between small">
                            <div class="text-muted">Copyright &copy; Your Website 2023</div>
                            <div>
                                <a href="#">Privacy Policy</a>
                                &middot;
                                <a href="#">Terms &amp; Conditions</a>
                            </div>
                        </div>
                    </div>
                </footer>
            </div>
        </div>
    </body>
</html>