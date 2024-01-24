<?
	//header페이지
	$home_dir = str_replace( basename(__DIR__) , "" , __DIR__ );

	include $home_dir . "/inc_lude/back_header.php";
	include $home_dir . "/choco/back_common.php";

	//오늘날짜
    $today = TODATE;

	//전월날짜
	$timestamp = strtotime("-1 weeks");
	$last_week = date("Y-m-d", $timestamp);

    $url = "backwork_list";
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
    $sql = "select count(wc.idx) as cnt from work_todaywork as wc, work_member as wm where wc.email = wm.email";
	$sql = $sql .= " and wm.state = '0' and work_flag = '2' and share_flag = '0' and (wc.workdate >= '".$last_week."' and wc.workdate <= '".$today."')";
	$history_info_cnt = selectQuery($sql);
	if($history_info_cnt['cnt']){ 
		$total_count = $history_info_cnt['cnt'];
	}
	$code = "work";

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
					<h1 class="mt-4">오늘업무 리스트</h1>
					<div class="card mb-4">
						<div class="card-body">
							<div class="rew_member_sub_func_tab my-1">
								<div class="rew_member_sub_func_tab_in">
									<div class="rew_member_sub_func_sort" id="rew_member_sub_func_sort"></div>
									<div class="rew_member_sub_func_calendar">
										<div class="rew_member_sub_func_period">
											<div class="rew_list_char" id="rew_list_char">
												<button class="rew_list_char_select" id="btn_sort_on"><span>업무</span></button>
												<ul>
													<li><button value="work"><span>업무</span></button></li>
													<li><button value="share"><span>공유</span></button></li>
													<li><button value="request"><span>요청</span></button></li>
													<li><button value="report"><span>보고</span></button></li>
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
										<th class="work_regdate"><div class="back_sortkind" value="regdate">등록시간 <button class="list_arrow" value="btn_sort_down"></button></div></th>
										<th class="work_user"><div class="back_sortkind" value="name">작성자<button class="list_arrow" value="btn_sort_down"></button></div></th>
										<th class="work_state"><div class="back_sortkind" value="state">state</div></th>
										<th class="work_company"><div class="back_sortkind" value="company">기업<button class="list_arrow" value="btn_sort_down"></button></div></th>
										<th class="work_content" id="work_contents"><div class="back_sortkind" value="contents">작성 내용<button class="list_arrow" value="btn_sort_down"></button></div></th>
										<th class="work_again"><div class="back_sortkind" value="repeat_flag">반복 설정<button class="list_arrow" value="btn_sort_down"></button></div></th>
										<th class="work_date"><div class="back_sortkind" value="workdate">업무수행일<button class="list_arrow" value="btn_sort_down"></button></div></th>
										<th class="work_edit"><div>수정/삭제</div></th>
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
										$sql = "select wc.idx, wc.state, wc.regdate, wc.name, wc.email, wc.workdate, wm.company, wc.contents, wc.work_flag, wc.repeat_flag, wc.repeat_work_idx from work_todaywork as wc, work_member as wm where wc.email = wm.email and wm.state = '0' and work_flag = '2' and share_flag = '0' and (wc.workdate >= '".$last_week."' and wc.workdate <= '".$today."') order by wc.idx desc limit ".$startnum.",".$pagesize;
										$query = selectAllQuery($sql);

										for($i=0; $i<count($query['idx']); $i++){
											$repeat = $query['repeat_flag'][$i];
											$code_arr = ['0','1','2','3'];
											$title_arr = ['설정 안함','매일반복','매주반복','매월반복'];
											$idx = $query['idx'][$i];
											for($j=0;$j<count($code_arr);$j++){
												if($repeat == $code_arr[$j]){
													$repeat_flag = $title_arr[$j];
												}		
											}
										?>
										<tr id="table_tr_<?=$idx?>">
											<td><?=$query['regdate'][$i]?></td> 
											<td><?=$query['name'][$i]."(".$query['email'][$i].")"?></td>
											<td class="work_state_<?=$idx?>"><?=$query['state'][$i]?></td> 
											<td><?=$query['company'][$i]?></td>
											<td class="work_content">
												<p><?=$query['contents'][$i]?></p>
												<textarea class="form-control form-control-sm content_edit_<?=$idx?>" style="width:95%;display:none;" rows="1"><?=$query['contents'][$i]?></textarea>
											</td>
											<td><?=$repeat_flag?></td>
											<td><?=$query['workdate'][$i]?></td>
											<td>
												<div class="btn-group btn-group-sm d-flex back_btn" id="back_btn_<?=$idx?>" value="<?=$idx?>" role="group" style="position:relative;z-index:1;">
													<button type="button" class="btn btn-outline-dark" id="back_edit">수정</button>
													<button type="button" class="btn btn-outline-dark" id="back_remove">삭제</button>
												</div>
												<div class="btn-group btn-group-sm d-flex back_btn" id="back_btn_e_<?=$idx?>" value="<?=$idx?>" role="group" style="position:relative;z-index:1;">
													<button style="display:none;" type="button" class="btn btn-outline-dark" id="back_enter">확인</button>
													<button style="display:none;" type="button" class="btn btn-outline-dark" id="back_cancel">취소</button>
												</div>
											</td>
										</tr>
									<?}?>
									<input type="hidden" id="backoffice_type" value="backwork_list">
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
                        <div class="d-flex align-items-center justify-content-between small"></div>
                    </div>
                </footer>
            </div>
        </div>
    </body>
</html>