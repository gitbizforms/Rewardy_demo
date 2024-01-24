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

    $url = "backuser_list";
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

    $code = "0";

    $sql = "select count(idx) as cnt from work_member where state = '0'";
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
                        <h1 class="mt-4">유저 리스트</h1>
                        <!-- <div class="card mb-4">
                            <div class="card-body">
                                DataTables is a third party plugin that is used to generate the demo table below. For more information about DataTables, please visit the
                                <a target="_blank" href="https://datatables.net/">official DataTables documentation</a>
                                .
                            </div>
                        </div> -->
                        <div class="card mb-4">
                            <div class="card-body">
								<input type="hidden" id="backoffice_type" value="backuser_list">
								<div class="rew_member_sub_func_tab my-1">
									<div class="rew_member_sub_func_tab_in">
										<div class="rew_member_sub_func_calendar">
											<div class="rew_member_sub_func_period">
                                                <div class="rew_list_char" id="rew_list_char">
													<button class="rew_list_char_select on"><span>이용중인 회원</span></button>
													<ul>
														<li><button value="0"><span>이용중인 회원</span></button></li>
														<li><button value="9"><span>탈퇴한 회원</span></button></li>
													</ul>
												</div>	
                                                <div class="rew_cha_search_box mx-1">
													<input type="text" class="input_search" id="backcoin_search" placeholder="키워드">
													<button id="backcoin_search_btn"><img src="../html/images/pre/ico_c_search.png" alt=""></button>
												</div>
                                                <button type="submit" class="btn_inquiry btn_check mx-1" id="btn_make_excel"><span>Excel</span></button>
											</div>
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
                                <table class="table table-bordered rounded-1" id="backoff_table" class="rounded-1">
                                    <thead>
                                        <tr>
                                            <th class="user_no"><div class="back_sortkind" value="idx">Idx<button class="list_arrow" value="btn_sort_down"></button></div></th>
                                            <th class="user_name"><div class="back_sortkind" value="name">이름<button class="list_arrow" value="btn_sort_down"></button></div></th>
                                            <th class="user_id"><div class="back_sortkind" value="email">ID<button class="list_arrow" value="btn_sort_down"></button></div></th>
                                            <th class="user_company"><div class="back_sortkind" value="company">회사<button class="list_arrow" value="btn_sort_down"></button></div></th>
                                            <th class="user_coin"><div class="back_sortkind" value="comcoin">공용코인<button class="list_arrow" value="btn_sort_down"></button></div></th>
                                            <th class="user_coin"><div class="back_sortkind" value="coin">코인<button class="list_arrow" value="btn_sort_down"></button></div></th>
                                            <th class="user_login"><div class="back_sortkind" value="login_count">누적 로그인<button class="list_arrow" value="btn_sort_down"></button></div></th>
                                            <th class="user_last"><div class="back_sortkind" value="login_date">마지막 로그인시간<button class="list_arrow" value="btn_sort_down"></button></div></th>
                                            <th class="user_pass"><div class="back_sortkind" value="login_date">PW 초기화</div></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?
											$sql = "select idx,regdate, name, email, company, comcoin, coin, login_count, login_date from work_member where state = '0' order by idx desc limit ".$startnum.",".$pagesize;
                                            $query = selectAllQuery($sql);

											for($i=0; $i<count($query['idx']); $i++){
												$mem_idx = $query['idx'][$i];
                                                $no = $i+1;
											?>
											<tr>
												<td><?=$mem_idx?></td>
												<td><?=$query['name'][$i]?></td>
												<td class="user_td_email"><?=$query['email'][$i]?></td>
												<td><?=$query['company'][$i]?></td>
												<td><?=number_format($query['comcoin'][$i])?></td>
												<td><?=number_format($query['coin'][$i])?></td>
                                                <td><?=$query['login_count'][$i]?></td>
                                                <td><?=$query['login_date'][$i]?></td>
                                                <td><button type="button" class="btn btn-outline-dark btn-sm" id="reset_<?=$mem_idx?>" value="<?=$mem_idx?>"><i class="fa-solid fa-rotate-right"></i></button></td>
											</tr>
										<?}?>
										<input type="hidden" id="tclass" value="btn_sort_down">
										<input type="hidden" id="kind" value="idx">
										<input type="hidden" id="code" value="<?=$code?>">
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
                                <a href="#">Bizforms &amp; BETTERMONDAY</a>
                            </div>
                        </div>
                    </div>
                </footer>
            </div>
        </div>
    </body>
</html>