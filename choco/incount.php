<?
	//header페이지
	$home_dir = str_replace( basename(__DIR__) , "" , __DIR__ );

	include $home_dir . "/inc_lude/back_header.php";

	//오늘날짜
    $today = TODATE;

	//전월날짜
	$timestamp = strtotime("-1 weeks");
	$last_week = date("Y-m-d", $timestamp);

    $url = "backin_list";
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
	
	$code = "all";	

	$sql = "select count(1) as cnt from work_member_login where state = '0' and (workdate >= '".$last_week."' and workdate <= '".$today."') ";
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
    </head>
    <body class="sb-nav-fixed">
		<nav class="sb-topnav navbar navbar-expand navbar-dark bg-dark">
			<?php include "admin_top.php"; ?>
		</nav>
		<div id="layoutSidenav">
            <?php include "admin_sidebar.php"; ?>
            	<div id="layoutSidenav_content">
                    <div class="container-fluid px-4" id="page-screen">
						<h1 class="mt-4">출근기록</h1>
                        <div class="card mb-4">
                            <div class="card-body">
								<div class="rew_member_sub_func_tab my-1">
									<div class="rew_member_sub_func_tab_in">
										<div class="rew_member_sub_func_sort" id="rew_member_sub_func_sort"></div>
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
                                            <th class="backoff_commdate"><div class="back_sortkind" value="regdate">출근시간 <button class="list_arrow" value="btn_sort_down"></button></div></th>
                                            <th class="backoff_inname"><div class="back_sortkind" value="name">이름</div></th>
                                            <th class="backoff_inemail"><div class="back_sortkind" value="email">이메일</div></th>
                                            <th class="backoff_incomp"><div class="back_sortkind" value="company">회사</div></th>
                                            <th class="backoff_part"><div class="back_sortkind" value="part">부서</div></th>
											<th class="backoff_ip"><div class="back_sortkind" value="ip">접속IP</div></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?
											// $sql = "select wr.idx, wr.regdate, wr.email, wr.name, wr.partno, wr.part, wr.work_idx, wr.comment, (select company from work_member where email = wr.email and state = '0') as comp from work_todaywork_review as wr where wr.state = '0' and wr.comment != '' and (wr.workdate >= '".$last_week."' and wr.workdate <= '".$today."') order by wr.idx desc limit ".$startnum.",".$pagesize;

											$sql = "select ml.idx, ml.email, ml.ip, ml.regdate, ml.name, wm.company, wm.part from work_member_login as ml, work_member as wm where ml.email = wm.email and wm.state = '0' and ml.state = '0' order by ml.idx desc limit ".$startnum.",".$pagesize;
											$query = selectAllQuery($sql);
											for($i=0; $i<count($query['idx']); $i++){?>
											<tr>
												<td><?=$query['regdate'][$i]?></td>
												<td><?=$query['name'][$i]?></td>
												<td><?=$query['email'][$i]?></td>
												<td><?=$query['company'][$i]?></td>
												<td><?=$query['part'][$i]?></td>
												<td><?=$query['ip'][$i]?></td>
											</tr>
										<?}?>
										<input type="hidden" id="tclass" value="btn_sort_down">
										<input type="hidden" id="kind" value="idx">
										<input type="hidden" id="code" value="<?=$code?>">
										<input type="hidden" value="<?=$pagesize?>" id="list_cnt" >
										<input type="hidden" id="backoff_sdate" value="<?=$last_week?>">
										<input type="hidden" id="backoff_edate" value="<?=$today?>">
										<input type="hidden" id="backoffice_type" value="backin_list">
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
                            <div class="text-muted">Bizform & Rewardy</div>
                            <div>
                                <!-- <a href="#">Privacy Policy</a>
                                &middot;
                                <a href="#">Terms &amp; Conditions</a> -->
                            </div>
                        </div>
                    </div>
                </footer>
            </div>
        </div>
    </body>
</html>
