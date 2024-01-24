<?
	//header페이지
	$home_dir = str_replace( basename(__DIR__) , "" , __DIR__ );

	include $home_dir . "/inc_lude/back_header.php";
	include $home_dir . "/backoffice/back_common.php";

	//오늘날짜
    $today = TODATE;

	//전월날짜
	$timestamp = strtotime("-3 months");
	$last_week = date("Y-m-d", $timestamp);

    $url = "backchall_list";
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
	
    $sql = "select count(wc.idx) as cnt from work_challenges as wc, work_member as wm where wc.email = wm.email and wc.state = '0' and wm.state = '0' and wc.template ='0' and wc.day_type = '0' ";
    $sql = $sql .= "and (wc.sdate >= '".$last_week."' and wc.edate <= '".$today."') ";
	$history_info_cnt = selectQuery($sql);
	if($history_info_cnt['cnt']){ 
		$total_count = $history_info_cnt['cnt'];
	}
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
                        <h1 class="mt-4">챌린지 리스트</h1><br>
                        <div class="card mb-4">
                            <div class="card-body">
								<input type="hidden" id="backoffice_type" value="backchall_list">
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
													<li><button value="10"><span>10</span></button></li>
													<li><button value="15"><span>15</span></button></li>
													<li><button value="30"><span>30</span></button></li>
													<li><button value="50"><span>50</span></button></li>
													<li><button value="100"><span>100</span></button></li>
												</ul>
											</div>
										</div>
									</div>
								</div>
                                <table class="table table-bordered rounded-1 mt-3" id="backoff_table" class="rounded-1">
                                    <thead>
                                        <tr>
                                            <th class="chall_regdate"><div class="back_sortkind" value="regdate">등록시간<button class="list_arrow" value="btn_sort_down"></button></div></th>
                                            <th class="chall_title"><div class="back_sortkind" value="title">타이틀<button class="list_arrow" value="btn_sort_down"></button></div></th>
                                            <th class="chall_comp"><div class="back_sortkind" value="company">기업<button class="list_arrow" value="btn_sort_down"></button></div></th>
                                            <th class="chall_user"><div class="back_sortkind" value="email">제작자<button class="list_arrow" value="btn_sort_down"></button></div></th>
                                            <th class="chall_cnt"><div class="back_sortkind" value="ucnt">대상자<button class="list_arrow" value="btn_sort_down"></button></div></th>
                                            <th class="chall_cnt"<div class="back_sortkind" value="percent">참여자<button class="list_arrow" value="btn_sort_down"></button></div></th>
                                            <th class="chall_coin"><div class="back_sortkind" value="total_max_coin">최대코인<button class="list_arrow" value="btn_sort_down"></button></div></th>
                                            <th class="chall_coin"><div class="back_sortkind" value="total_coin">지급코인 <button class="list_arrow" value="btn_sort_down"></button></div></th>
                                            <th class="chall_date"><div class="back_sortkind" value="keyword">시작일자<button class="list_arrow" value="btn_sort_down"></button></div></th>
                                            <th class="chall_date"><div class="back_sortkind" value="sdate">종료일자<button class="list_arrow" value="btn_sort_down"></button></div></th>
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
                                        <?
                                            $sql = "select  wc.idx, wc.title, wc.regdate, wc.coin, wc.total_max_coin, wc.keyword, wc.email, wc.sdate, wc.edate, wc.pageview, (select count(idx) from work_challenges_user as cu where cu.challenges_idx = wc.idx) as ucnt, (select count(idx) from work_challenges_result as cr where cr.state = '1' and cr.challenges_idx = wc.idx ) as rcnt";
                                            $sql = $sql .= " from work_challenges as wc where wc.state = '0' and wc.day_type = '0' and wc.template ='0'";
                                            $sql = $sql .= " and (wc.sdate >= '".$last_week."' and wc.sdate <= '".$today."')";
                                            $sql = $sql .= " order by wc.idx desc";
                                            $sql = $sql .= " limit ".$startnum.",".$pagesize;

											$query = selectAllQuery($sql);
											for($i=0; $i<count($query['idx']); $i++){
                                                $sql = "select email, company from work_member where email = '".$query['email'][$i]."' and state = '0' ";
                                                $company = selectQuery($sql);
                                                // $champer = ($query['rcnt'][$i] / $query['ucnt'][$i])*100;
                                                // $champer = sprintf('%0.1f',$champer);
                                                $result_coin = $query['coin'][$i] * $query['rcnt'][$i];
											?>
											<tr>
												<td><?=$query['regdate'][$i]?></td>
												<td><?=$query['title'][$i]?></td>
                                                <td><?=$company['company']?></td>
                                                <td><?=$query['email'][$i]?></td>
                                                <td><?=$query['ucnt'][$i]?></td>
                                                <td><?=$query['rcnt'][$i]?></td>
                                                <td><?=number_format($query['total_max_coin'][$i])?></td>
                                                <td><?=number_format($result_coin)?></td>
                                                <td><?=$query['sdate'][$i]?></td>
                                                <td><?=$query['edate'][$i]?></td>
											</tr>
										<?}?>
										<input type="hidden" id="tclass" value="btn_sort_down">
										<input type="hidden" id="kind" value="idx">
                                        <input type="hidden" value="<?=$pagesize?>" id="list_cnt" >
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
