<?
	//header페이지
	$home_dir = str_replace( basename(__DIR__) , "" , __DIR__ );

	include $home_dir . "/inc_lude/back_header.php";

	//오늘날짜
    $today = TODATE;

	//전월날짜
	$timestamp = strtotime("-1 weeks");
	$last_week = date("Y-m-d", $timestamp);

    $url = "backcomm_list";
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

	$sql = "select count(idx) as cnt from work_todaywork_review where state = '0' and comment != '' and (workdate >= '".$last_week."' and workdate <= '".$today."') ";
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
						<h1 class="mt-4">퇴근/소감</h1>
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
														<li><button value="1"><span>최고야</span></button></li>
														<li><button value="2"><span>뿌듯해</span></button></li>
														<li><button value="3"><span>기분좋아</span></button></li>
														<li><button value="4"><span>감사해</span></button></li>
														<li><button value="5"><span>재밌어</span></button></li>
														<li><button value="6"><span>수고했어</span></button></li>
														<li><button value="7"><span>무난해</span></button></li>
														<li><button value="8"><span>지쳤어</span></button></li>
														<li><button value="9"><span>속상해</span></button></li>
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
												<button type="submit" class="btn_inquiry btn_check mx-1" id="btn_make_excel"><span>Excel</span></button>
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
                                            <th class="backoff_commdate"><div class="back_sortkind" value="regdate">퇴근시간 <button class="list_arrow" value="btn_sort_down"></button></div></th>
                                            <th class="backoff_commname"><div class="back_sortkind" value="name">Name. <button class="list_arrow" value="btn_sort_down"></button></div></th>
                                            <th class="backoff_company"><div class="back_sortkind" value="company">회사 <button class="list_arrow" value="btn_sort_down"></button></div></th>
                                            <th class="backoff_part"><div class="back_sortkind" value="part">부서</div></th>
                                            <th class="backoff_feel"><div class="back_sortkind" value="work_idx">기분</div></th>
											<th class="backoff_comment"><div class="" value="code">코멘트</div></th>
                                            <!-- <th><div class="back_sortkind" value="company">기업 <button class="list_arrow" value="btn_sort_down"></button></div></th> -->
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
											$sql = "select wr.idx, wr.regdate, wr.email, wr.name, wr.partno, wr.part, wr.work_idx, wr.comment, (select company from work_member where email = wr.email and state = '0') as comp from work_todaywork_review as wr where wr.state = '0' and wr.comment != '' and (wr.workdate >= '".$last_week."' and wr.workdate <= '".$today."') order by wr.idx desc limit ".$startnum.",".$pagesize;
											$query = selectAllQuery($sql);

											for($i=0; $i<count($query['idx']); $i++){
												$comm_idx = $query['idx'][$i];
												$feelkind = $query['work_idx'][$i];
												$feel_arr = ['1','2','3','4','5','6','7','8','9'];
												$title_arr = ['최고야','뿌듯해','기분좋아','감사해','재밌어','수고했어','무난해','지쳤어','속상해'];
												for($j=0;$j<count($feel_arr);$j++){
													if($feelkind == $feel_arr[$j]){
														$feel_kind = $title_arr[$j];
													}
												}
											?>
											<tr>
												<td><?=$query['regdate'][$i]?></td>
												<td><?=$query['name'][$i]."(".$query['email'][$i].")"?></td>
												<td><?=$query['comp'][$i]?></td>
												<td><?=$query['part'][$i]?></td>
												<td class="backoff_memo"><?=$feel_kind?></td>
												<td class="backoff_comment"><p><?=$query['comment'][$i]?></p></td>
											</tr>
										<?}?>
										<input type="hidden" id="tclass" value="btn_sort_down">
										<input type="hidden" id="kind" value="idx">
										<input type="hidden" id="code" value="<?=$code?>">
										<input type="hidden" value="<?=$pagesize?>" id="list_cnt" >
										<input type="hidden" id="backoff_sdate" value="<?=$last_week?>">
										<input type="hidden" id="backoff_edate" value="<?=$today?>">
										<input type="hidden" id="backoffice_type" value="backcomm_list">
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
