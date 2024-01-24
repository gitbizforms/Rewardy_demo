<?
	//header페이지
	$home_dir = str_replace( basename(__DIR__) , "" , __DIR__ );

	include $home_dir . "/inc_lude/back_header.php";
	include $home_dir . "/choco/back_common.php";

	//오늘날짜
    $today = TODATE;

	//전월날짜
	$timestamp = strtotime("-3 months");
	$last_week = date("Y-m-d", $timestamp);

    $url = "backchall_user_list";
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
	
    $sql = "select count(1) as cnt from work_challenges_result ";
    $sql = $sql .= "where ( DATE_FORMAT(comment_regdate, '%Y-%m-%d') >= '".$last_week."' and  DATE_FORMAT(comment_regdate, '%Y-%m-%d') <= '".$today."') ";
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
								<input type="hidden" id="backoffice_type" value="backchall_user">
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
                                            <th class="backoff_time"><div class="back_sortkind" value="regdate">참여시간<button class="list_arrow" value="btn_sort_down"></button></div></th>
                                            <th class="backoff_ctitle"><div class="back_sortkind" value="title">참여한 챌린지<button class="list_arrow" value="btn_sort_down"></button></div></th>
                                            <th class="backoff_cuser"><div class="back_sortkind" value="name">참여자<button class="list_arrow" value="btn_sort_down"></button></div></th>
                                            <th class="backoff_cham_comp"><div class="back_sortkind" value="company">소속<button class="list_arrow" value="btn_sort_down"></button></div></th>
                                            <th class="backoff_file"><div class="back_sortkind" value="email">첨부파일</div></th>
                                            <th class="backoff_givecoin"><div class="back_sortkind" value="coin">지급 코인<button class="list_arrow" value="btn_sort_down"></button></div></th>
                                            <th class="backoff_cham_comment"><div class="back_sortkind" value="comment">코멘트<button class="list_arrow" value="btn_sort_down"></button></div></th>
                                            <!-- <th class="backoff_coin"><div class="back_sortkind" value="total_coin">지급코인 <button class="list_arrow" value="btn_sort_down"></button></div></th>
                                            <th class="backoff_date"><div class="back_sortkind" value="keyword">시작일자<button class="list_arrow" value="btn_sort_down"></button></div></th>
                                            <th class="backoff_date"><div class="back_sortkind" value="sdate">종료일자<button class="list_arrow" value="btn_sort_down"></button></div></th> -->
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
                                            $sql = "select cr.idx, cr.email, cr.comment_regdate, cr.comment, cr.part, wm.company, wm.name, wc.title, wc.coin ";
                                            $sql = $sql .= ", (select count(1) from work_challenges_file_info as cf where cf.challenges_idx = cr.idx) as cnt";
                                            $sql = $sql .= " from work_challenges_result as cr, work_member as wm, work_challenges as wc ";
                                            $sql = $sql .= " where cr.email = wm.email and cr.challenges_idx = wc.idx and wm.state = '0' and ";
                                            $sql = $sql .= " (DATE_FORMAT(comment_regdate, '%Y-%m-%d')>= '".$last_week."' and  DATE_FORMAT(comment_regdate, '%Y-%m-%d') <= '".$today."')";
                                            $sql = $sql .= " order by cr.idx desc";
                                            $sql = $sql .= " limit ".$startnum.",".$pagesize;

											$query = selectAllQuery($sql);
											for($i=0; $i<count($query['idx']); $i++){
											?>
											<tr>
												<td><?=$query['comment_regdate'][$i]?></td>
												<td><?=$query['title'][$i]?></td>
                                                <td><?=$query['name'][$i]?></td>
                                                <td><?=$query['company'][$i]."/".$query['part'][$i]?></td>
                                                <td><?=$query['cnt'][$i]?></td>
                                                <td><?=$query['coin'][$i]?></td>
                                                <td class="backoff_cham_comment"><p><?=$query['comment'][$i]?></p></td>
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
