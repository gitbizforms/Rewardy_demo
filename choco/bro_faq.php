<?
	//header페이지
	$home_dir = str_replace( basename(__DIR__) , "" , __DIR__ );

	include $home_dir . "/inc_lude/back_header.php";

    $url = "backfaq_list";
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
	
	$sql = "select count(idx) as cnt from bro_faq where state in ('0','1') order by idx desc limit 0,15";
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
    </head>
    <body class="sb-nav-fixed">
		<nav class="sb-topnav navbar navbar-expand navbar-dark bg-dark">
			<?php include "admin_top.php"; ?>
		</nav>
		<div id="layoutSidenav">
            <?php include "admin_sidebar.php"; ?>
            	<div id="layoutSidenav_content">
                    <div class="container-fluid px-4" id="page-screen">
						<h1 class="mt-4">브로슈어 FAQs</h1>
						<input type="hidden" id="backoffice_type" value="backfaq_list">
						<input type="hidden" id="bro_view" value="faq">
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
														<li><button value="1"><span>노출</span></button></li>
														<li><button value="0"><span>미노출</span></button></li>
														<li><button value="9"><span>삭제된 공지</span></button></li>
													</ul>
												</div>		
												<div class="rew_cha_search_box mx-1">
													<input type="text" class="input_search" id="backcoin_search" placeholder="키워드">
													<button id="backcoin_search_btn"><img src="../html/images/pre/ico_c_search.png" alt=""></button>
												</div>
												<button type="submit" class="btn_inquiry btn_reset mx-1" id="new_notice"><span>작성✏️</span></button>
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
                                            <th class="note_no"><div class="back_sortkind" value="idx">No.<button class="list_arrow" value="btn_sort_down"></button></div></th>
											<th class="note_title"><div class="back_sortkind" value="title">타이틀<button class="list_arrow" value="btn_sort_down"></button></div></th>
                                            <th class="note_state"><div class="back_sortkind" value="state">노출여부</div></th>
                                            <th class="note_user"><div class="back_sortkind" value="name">작성자</div></th>
                                            <th class="note_date"><div class="back_sortkind" value="regdate">등록일자<button class="list_arrow" value="btn_sort_down"></button></div></th>
											<th class="note_delete"><div class="back_sortkind" value="regdate">삭제</div></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?
											$sql = "select idx, email, title, regdate, state, (select name from work_member where email = bro_faq.email and state = '0') as name from bro_faq where state in ('0','1') order by idx desc";	
											$query = selectAllQuery($sql);

											for($i=0; $i<count($query['idx']); $i++){
												$idx = $query['idx'][$i];
											?>
											<tr>
												<td><?=$i+1?></td>
												<td class="title_list" id="backnote_<?=$idx?>"><?=$query['title'][$i]?></td>
												<td>
													<div class="btn-group btn-group-sm border border-dark" id="btn_group_<?=$idx?>" role="group" aria-label="">
														<button class="btn <?=$query['state'][$i]=='1'?"btn-dark":"btn-outline-dark"?>" id="btnradio_1" value="<?=$idx?>">노출</button>
														<button class="btn <?=$query['state'][$i]=='0'?"btn-dark":"btn-outline-dark"?>" id="btnradio_0" value="<?=$idx?>">미노출</button>
													</div>
												</td>
												<td><?=$query['name'][$i]?></td>
												<td><?=$query['regdate'][$i]?></td>
												<td><button type="button" class="btn btn-outline-dark btn-sm" id="notedel_<?=$idx?>"><i class="fa-solid fa-trash-can"></i></button></td>
											</tr>
										<?}?>
										<input type="hidden" id="tclass" value="btn_sort_down">
										<input type="hidden" id="kind" value="idx">
										<input type="hidden" id="code" value="<?=$code?>">
										<input type="hidden" value="<?=$pagesize?>" id="list_cnt" >
										<!-- <input type="hidden" id="backoff_sdate" value="<?=$last_week?>">
										<input type="hidden" id="backoff_edate" value="<?=$today?>"> -->
										<input type="hidden" id="user_kind" value="">
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
 