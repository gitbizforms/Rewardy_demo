<?
	//header페이지
	$home_dir = str_replace( basename(__DIR__) , "" , __DIR__ );
	include $home_dir . "/inc_lude/back_header.php";
	include $home_dir . "/choco/back_common.php";
	
	//오늘날짜
    // $today = TODATE;

	//전월날짜
	// $timestamp = strtotime("-1 weeks");
	// $last_week = date("Y-m-d", $timestamp);

    $url = "backcomp_list";
	$string = "&page=".$url;

    $p = $_POST['p']?$_POST['p']:$_GET['p'];
	if (!$p){
		$p = 1;
	}

	$pagingsize = 5;					//페이징 사이즈
	$pagesize = 5;						//페이지 출력갯수
	$startnum = 0;						//페이지 시작번호
	$endnum = $p * $pagesize;			//페이지 끝번호

	//시작번호
	if ($p == 1){
		$startnum = 0;
	}else{
		$startnum = ($p - 1) * $pagesize;
	}

    $sql = "select count(idx) as cnt from work_company where state in('0','9')";
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
                        <h1 class="mt-4">기업 리스트</h1>
                        <!-- <div class="card mb-4">
                            <div class="card-body">
                                DataTables is a third party plugin that is used to generate the demo table below. For more information about DataTables, please visit the
                                <a target="_blank" href="https://datatables.net/">official DataTables documentation</a>
                                .
                            </div>
                        </div> -->
                        <div class="card mb-4">
                            <div class="card-body">
								<input type="hidden" id="backoffice_type" value="backcomp_list">
								<div class="rew_member_sub_func_tab my-1">
									<div class="rew_member_sub_func_tab_in">
										<div class="rew_member_sub_func_calendar">
											<div class="rew_member_sub_func_period">
                                                <div class="rew_cha_search_box mx-1">
													<input type="text" class="input_search" id="backcoin_search" placeholder="키워드">
													<button id="backcoin_search_btn"><img src="../html/images/pre/ico_c_search.png" alt=""></button>
												</div>
											</div>
										</div>
									</div>
								</div>
                                <table class="table table-bordered rounded-1" id="backoff_table" class="rounded-1">
                                    <thead>
                                        <tr>
                                            <th><div class="back_sortkind" value="company">기업명<button class="list_arrow" value="btn_sort_down"></button></div></th>
                                            <th><div class="back_sortkind" value="idx">index<button class="list_arrow" value="btn_sort_down"></button></div></th>
                                            <th><div class="back_sortkind" value="usercnt">인원수<button class="list_arrow" value="btn_sort_down"></button></div></th>
                                            <th><div class="back_sortkind" value="regdate">등록일자<button class="list_arrow" value="btn_sort_down"></button></div></th>
                                            <!-- <th><div class="back_sortkind" value="comcoin">보유코인<button class="list_arrow" value="btn_sort_down"></button></div></th> -->
                                            <th><div class="back_sortkind" value="comcoin">보유 공용코인<button class="list_arrow" value="btn_sort_down"></button></div></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?
											$sql = "select idx, state, company, comcoin, regdate, penalty, intime, outtime,";
                                            $sql = $sql .= " (select count(1) from work_member where companyno = work_company.idx and state = '0') as usercnt";
                                            $sql = $sql .= " from work_company where state in ('0','9') order by idx asc limit ".$startnum.",".$pagesize;
                                            $query = selectAllQuery($sql);

											for($i=0; $i<count($query['idx']); $i++){
											?>
											<tr>
												<td><?=$query['company'][$i]?></td>
												<td><?=$query['idx'][$i]?></td>
												<td><?=$query['usercnt'][$i]?></td>
												<td><?=$query['regdate'][$i]?></td>
												<td><?=number_format($query['comcoin'][$i])?></td>
											</tr>
										<?}?>
										<input type="hidden" id="tclass" value="btn_sort_up">
										<input type="hidden" id="kind" value="idx">
										<!-- <input type="hidden" id="code" value="<?=$code?>"> -->
                                    </tbody>
                                </table>
								<div id="back_pagelist">
									<?php echo back_pageing($pagingsize, $total_count, $pagesize, $string)?>
								</div>
                                <table class="table table-bordered rounded-1" id="backoff_table" style="display:none;"></table>
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
                                <a href="#">Bizforms &amp; BETTERMONDAY</a>
                            </div>
                        </div>
                    </div>
                </footer>
            </div>
        </div>
    </body>
</html>