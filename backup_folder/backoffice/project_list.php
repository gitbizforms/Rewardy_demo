<?
	//header페이지
	$home_dir = str_replace( basename(__DIR__) , "" , __DIR__ );

	include $home_dir . "/inc_lude/back_header.php";
	include $home_dir . "/backoffice/back_common.php";

	//오늘날짜
    $today = TODATE;

	//전월날짜
	$timestamp = strtotime("-1 months");
	$last_week = date("Y-m-d", $timestamp);

    $url = "backparty_list";
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
	
    // $sql = "select count(wc.idx) as cnt from work_todaywork_project as wc, work_member as wm where wc.email = wm.email and wc.state in ('0','1') and wm.state = '0' ";
    // $sql = $sql .= "and (wc.regdate >= '".$last_week."' and wc.regdate <= '".$today."')";

    $sql = "select count(idx) as cnt from work_todaywork_project where state in('0','1') and (regdate >= '".$last_week."' and regdate <= '".$today."') ";
	$history_info_cnt = selectQuery($sql);
	if($history_info_cnt['cnt']){ 
		$total_count = $history_info_cnt['cnt'];
	}

    $sql = "select wc.idx, wc.title, wc.regdate, wc.state, wc.com_coin_pro, wc.email, wc.page_count, (select count(idx) from work_todaywork_project_user as cu where cu.project_idx = wc.idx) as ucnt";
    $sql = $sql .= " from work_todaywork_project as wc where wc.state in ('0','1')";
    $sql = $sql .= " and (wc.regdate >= '".$last_week."' and wc.regdate <= '".$today."')";
    $sql = $sql .= " order by wc.idx desc";
    $sql = $sql .= " limit ".$startnum.",".$pagesize;

    $query = selectAllQuery($sql);

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
                    <div class="container-fluid px-4">
                        <h1 class="mt-4">파티 리스트</h1><br>
                        <!-- <ol class="breadcrumb mb-4">
                            <li class="breadcrumb-item"><a href="index.html">백오피스</a></li>
                            <li class="breadcrumb-item active">코인통계</li>
                        </ol> -->
                        <!-- <div class="card mb-4">
                            <div class="card-body">
                                DataTables is a third party plugin that is used to generate the demo table below. For more information about DataTables, please visit the
                                <a target="_blank" href="https://datatables.net/">official DataTables documentation</a>
                                .
                            </div>
                        </div> -->
                        <div class="card mb-4">
                            <div class="card-body">
								<input type="hidden" id="backoffice_type" value="backparty_list">
								<div class="rew_member_sub_func_tab my-1">
									<div class="rew_member_sub_func_tab_in">
										<div class="rew_member_sub_func_calendar">
											<div class="rew_member_sub_func_period">
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
												<button type="submit" class="btn_inquiry btn_check mx-1" id="btn_history"><span>조회</span></button>
												<button type="submit" class="btn_inquiry btn_reset mx-1" id="cal_history"><span>날짜 초기화</span></button>
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
                                <table class="table table-bordered rounded-1 mt-3" id="backoff_table" class="rounded-1">
                                    <thead>
                                        <tr>
                                            <th><div class="back_sortkind" value="regdate">생성시간<button class="list_arrow" value="btn_sort_down"></button></div></th>
                                            <th><div class="back_sortkind" value="email">생성자<button class="list_arrow" value="btn_sort_down"></button></th>
                                            <th id="party_title"><div class="back_sortkind" value="company">파티명<button class="list_arrow" value="btn_sort_down"></button></div></th>
                                            <th><div class="back_sortkind" value="com_coin_pro">후원코인<button class="list_arrow" value="btn_sort_down"></button></th>
                                            <th><div class="back_sortkind" value="ucnt">인원수<button class="list_arrow" value="btn_sort_down"></button></th>
                                            <th><div class="back_sortkind" value="company">기업<button class="list_arrow" value="btn_sort_down"></button></th>
                                            <th><div class="back_sortkind" value="">현재상태<button class="list_arrow" value="btn_sort_down"></button></div></th>
                                            <th id="chall_maxcoin"><div class="back_sortkind" value="">조회수<button class="list_arrow" value="btn_sort_down"></button></div></th>
                                        </tr>
                                    </thead>
                                    <!-- <tfoot>
                                        <tr>
                                            <th>Name</th>
                                            <th>Position</th>
                                            <th>Office</th>
                                            <th>Age</th>
                                            <th>Start dRate</th>
                                            <th>Salary</th>
                                        </tr>
                                    </tfoot> -->
                                    <tbody>
                                        <? for($i=0; $i<count($query['idx']); $i++){
                                                $sql = "select email, company from work_member where email = '".$query['email'][$i]."' and state = '0' ";
                                                $company = selectQuery($sql);
											?>
											<tr>
												<td><?=$query['regdate'][$i]?></td>
												<td><?=$query['email'][$i]?></td>
                                                <td><?=$query['title'][$i]?></td>
                                                <td><?=number_format($query['com_coin_pro'][$i])?></td>
                                                <td><?=$query['ucnt'][$i]?></td>
                                                <td><?=$company['company']?></td>
                                                <td><?=$query['state'][$i]?></td>
                                                <td><?=$query['page_count'][$i]?></td>
											</tr>
										<?}?>
										<input type="hidden" id="tclass" value="btn_sort_down">
										<input type="hidden" id="kind" value="idx">
                                        <input type="hidden" value="<?=$pagesize?>" id="list_cnt" >
										<input type="hidden" id="backoff_sdate" value="<?=$last_week?>">
										<input type="hidden" id="backoff_edate" value="<?=$today?>">
                                        <input type="hidden" id="query" value="<?=$sql?>">
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
