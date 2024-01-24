<?
	//header페이지
	$home_dir = str_replace( basename(__DIR__) , "" , __DIR__ );

	include $home_dir . "/inc_lude/back_header.php";

	//오늘날짜
    $today = TODATE;

	//전월날짜
	$timestamp = strtotime("-1 weeks");
	$last_week = date("Y-m-d", $timestamp);

    $url = "backlog_list";
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
	
	$sql = "select count(1) as cnt from work_data_log where (workdate >= '".$last_week."' and workdate <= '".$today."')";
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
						<h1 class="mt-4">데이터 로그</h1>
                        <div class="card mb-4">
                            <div class="card-body">
								<div class="rew_member_sub_func_tab my-1">
									<div class="rew_member_sub_func_tab_in">
										<div class="rew_member_sub_func_sort" id="rew_member_sub_func_sort"></div>
										<div class="rew_member_sub_func_calendar">
											<div class="rew_member_sub_func_period">
												<div class="rew_list_char" id="rew_list_char">
													<button class="rew_list_char_select on"><span>전체보기</span></button>
													<ul>
														<li><button value="all"><span>전체보기</span></button></li>
														<li><button value="type1"><span>에너지</span></button></li>
														<li><button value="type2"><span>성과</span></button></li>
														<li><button value="type3"><span>성장</span></button></li>
														<li><button value="type4"><span>협업</span></button></li>
														<li><button value="type5"><span>성실</span></button></li>
														<li><button value="type6"><span>실행</span></button></li>
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
                                            <th class="cp_No"><div class="back_sortkind" value="idx">No. <button class="list_arrow" value="btn_sort_down"></button></div></th>
                                            <th class="cp_workidx">work_idx</th>
                                            <th class="cp_email">사용자</th>
                                            <th class="cp_code"><div>서비스 유형</div></th>
											<th class="cp_logmemo"><div>메모</div></th>
											<th class="cp_ip"><div>ip</div></th>
											<th class="cp_logdate"><div>등록일자</div></th>
											<th class="log_company"><div>기업</div></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?

											$sql = "select idx, state, code, work_idx, email, name, memo, ip, workdate, regdate, companyno from work_data_log";
											$sql = $sql .= " where (workdate >= '".$last_week."' and workdate <= '".$today."') order by idx desc";
											$sql = $sql .= " limit ".$startnum.",".$pagesize;
											$query = selectAllQuery($sql);

											for($i=0; $i<count($query['idx']); $i++){
											?>
											<tr>
												<td><?=$i+1?></td>
												<td><?=$query['work_idx'][$i]?></td>
												<td><?=$query['email'][$i]?></td>
												<td><?=$query['code'][$i]?></td>
												<td><?=$query['memo'][$i]?></td>
												<td><?=$query['ip'][$i]?></td>
												<td><?=$query['regdate'][$i]?></td>
												<td><?=$query['companyno'][$i]?></td>
											</tr>
										<?}?>
										<input type="hidden" id="tclass" value="btn_sort_down">
										<input type="hidden" id="kind" value="idx">
										<input type="hidden" id="code" value="all">
										<input type="hidden" value="<?=$pagesize?>" id="list_cnt" >
										<input type="hidden" id="backoff_sdate" value="<?=$last_week?>">
										<input type="hidden" id="backoff_edate" value="<?=$today?>">
										<input type="hidden" id="backoffice_type" value="backlog_list">
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
