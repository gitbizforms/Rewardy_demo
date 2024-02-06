<?php
	//header페이지
	$home_dir = str_replace( basename(__DIR__) , "" , __DIR__ );
	include $home_dir . "/inc_lude/header_index.php";
	include $home_dir. "/inc/PHPExcel-1.8/Classes/PHPExcel.php";

	if($user_level != '0'){
		header("Location:https://rewardy.co.kr/index.php");
		exit;
	}

	//오늘날짜
	$wdate = str_replace("-",".",$today);
	$sel_wdate = str_replace(".", "-" , $wdate);

	//날짜변환
	if($wdate){
		$m_wdate = str_replace(".","-",$wdate);
	}

	$month_tmp = explode(".",$wdate);
	if($month_tmp){
		$month_date = $month_tmp[0].".".$month_tmp[1];
	}
	
	
	//페이지
	$p = $_POST['p']?$_POST['p']:$_GET['p'];
	if (!$p){
		$p = 1;
	}

	$pagingsize = 5;					//페이징 사이즈
	$pagesize = 30;						//페이지 출력갯수
	$startnum = 0;						//페이지 시작번호
	$endnum = $p * $pagesize;			//페이지 끝번호

	//시작번호
	if ($p == 1){
		$startnum = 0;
	}else{
		$startnum = ($p - 1) * $pagesize;
	}

	//일주일
	$week7 = date("Y-m-d",strtotime("-1 week", TODAYTIME));
	
	// $pageurl = $_SERVER['PHP_SELF'];
	$pageurl = "member_nocal";
	$string = "&page=".$pageurl."&sdate=".$sdate."&edate=".$edate."&nday=".$nday."&type=".$type;

	//공용코인
	$where = " where companyno='".$companyno."' and DATE_FORMAT(regdate, '%Y.%m') = '".$month_date."' ";
	//$where = $where .= " and code in('800','900')";

	//전체 카운터수
	$sql = "select count(idx) as cnt from payment_log ".$where."";
	$pay_cnt_info = selectQuery($sql);
	if($pay_cnt_info['cnt']){
		$total_count = $pay_cnt_info['cnt'];
	}
		$sql = "select idx, goodName, TotPrice, payMethod, regdate";
		$sql = $sql .= " from payment_log";
		$sql = $sql .= " ".$where."";
		$sql = $sql .= " order by idx desc";
		$sql = $sql .= " limit ". $startnum.", ".$pagesize;
		$pay_info = selectAllQuery($sql);
?>
<link rel="stylesheet" type="text/css" href="/html/css/set_head.css<?php echo VER;?>" />
<link rel="stylesheet" type="text/css" href="/html/css/set_05_02.css<?php echo VER;?>" />

<div class="rew_warp">
	<div class="rew_warp_in">
		<div class="rew_box">
			<div class="rew_box_in">
				<!-- menu -->
				<? include $home_dir . "/inc_lude/menu.php";?>
				<!-- //menu -->

				<!-- 콘텐츠 -->
				<div class="rew_conts">
					<div class="rew_conts_in">
						<? include $home_dir . "/admin/admin_menubar.php";?>
						<div class="rew_conts_scroll_01">
							<div class="rew_member tyle_coin">
								<div class="rew_member_in" id="rew_member_in">
									<div class="rew_member_sub_func_tab" >
										<div class="rew_member_sub_count">
											<span>결제 내역</span>
										</div>
										<div class="rew_member_sub_func_tab_in">
											<div class="rew_member_sub_func_calendar">
											<div class="rew_member_sub_func_period">
													<div class="rew_member_sub_func_period_box">
														<input type="text" class="input_date" value="<?=$wdate?>" maxlength="10" id="coin_work_date" style="display:none;">
														<input type="text" class="input_date" id="pay_work_month" value="<?=$month_date?>" data-min-view="months" data-view="months" data-date-format="yyyy.mm" readonly="readonly">
													</div>
											
												</div>
											</div>
											<div class="rew_member_sub_func_sort sort_pay" id="rew_member_sub_func_list">
												<div class="rew_member_sub_func_sort_in">
													<button class="btn_sort_on" id="btn_sort_on"><span>30</span></button>
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
									<div id="rew_list_search">
										<div class="rew_member_list">
											<div class="rew_member_list_in">
												<div class="member_list_header">
													<div class="member_list_header_in" id="member_list_comcoin_out_nocal">
														
														<div class="member_list_header_datetime">
															<strong value="workdate">날짜</strong>
															<em>
															<button class="btn_sort_up" title="오름차순"></button>
															<button class="btn_sort_down" title="내림차순"></button>
															</em>
														</div>
														
														<div class="member_list_header_detail">
															<strong>내용</strong>
														</div>
														<div class="member_list_header_payment">
															<strong>결제금액</strong>
														</div>
														<div class="member_list_header_payment_plan">
															<strong>결제방식</strong>
														</div>
														<div class="member_list_header_tax">
															<strong>증빙서류</strong>
														</div>
													</div>
												</div>
												<div class="list_paging" id="list_paging">
													<div class="member_list_conts">
														<div class="member_list_conts_in">
															<ul class="member_list_conts_ul">
																<? 
																if(count($pay_info['idx'])>0){
																	for($i=0; $i<count($pay_info['idx']); $i++){
																	$method = $pay_info['payMethod'][$i];
																	$content = $pay_info['goodName'][$i];
																	$price = $pay_info['TotPrice'][$i];
																	$regDate = $pay_info['regdate'][$i];

																?>
																	<li>
																		<div class="member_list_conts_datetime">
																			<strong><?=$regDate?></strong>
																		</div>
																		<div class="member_list_conts_detail">
																			<strong><?=$content?></strong>
																		</div>
																		<div class="member_list_conts_payment">
																			<strong><?=number_format($price)?></strong>
																		</div>
																		<div class="member_list_conts_payment_plan">
																			<strong><?=$method?></strong>
																		</div>
																		
																		<div class="member_list_conts_tax">
																			<strong><button class = "tax">세금계산서 발행</button></strong>
																		</div>
																		
																	</li>
																<?}
																}else{?>
																	<li class="search_list_none"><span>결제내역이 없습니다.</span></li>
																<?}?>
															</ul>
														</div>
													</div>
												<?if($pay_info['idx']){?>
													<div class="rew_ard_paging">
														<div class="rew_ard_paging_in">
															<?
																//페이징사이즈, 전체카운터, 페이지출력갯수
																echo pageing($pagingsize, $total_count, $pagesize, $string);
															?>
														</div>
													</div>
												<?}?>
												<div class="rew_member_sub_text">
													<span>* 카카오페이/네이버페이는 각 업체에서 현금영수증을 받으실 수 있습니다.</span>
												</div>
												
											</div>
										</div>
									
									
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
				<!-- //콘텐츠 -->
			</div>
		</div>
	</div>

	
	<script type="text/javascript">
		$(document).ready(function(){
			
			var list_leng = $(".rew_layer_team_management .tl_list > ul > li").length - 1;
			if(list_leng>4){
				$(".t_layer.rew_layer_team_management").css({"height":619,"marginTop":-320});
			}else{
				var list_lengx = 65*list_leng;
				$(".t_layer.rew_layer_team_management").css({"height":359+list_lengx,"marginTop":-(359+list_lengx)/2});
			}

			$(".btn_open_join").click(function(){
				$(".rew_layer_join").show();
			});
			$(".btn_open_login").click(function(){
				$(".rew_layer_login").show();
			});
			$(".btn_open_repass").click(function(){
				$(".rew_layer_repass").show();
			});
			$(".btn_open_setting").click(function(){
				$(".rew_layer_setting").show();
			});
			$(".tl_close button").click(function(){
				$(this).closest(".t_layer").hide();
			});
			$(".rew_member_sub_text button").click(function(){
			$(".coin_faq_layer").show();
			});
			$(".cfl_close button").click(function(){
				$(".coin_faq_layer").hide();
			});

		});

		$(document).ready(function(){
		window.onbeforeunload = function () { $('.rewardy_loading_01').css('display', 'block'); }
		$(window).load(function () {          //페이지가 로드 되면 로딩 화면을 없애주는 것
            $('.rewardy_loading_01').css('display', 'none');
        	});
		});
		window.onpageshow = function(event) {
			if ( event.persisted || (window.performance && window.performance.navigation.type == 2)) {
				$('.rewardy_loading_01').css('display', 'none');
			}
		}
	</script>
</div>
	<!-- footer start-->
	<? include $home_dir . "/inc_lude/footer.php";?>
	<!-- footer end-->

</body>


</html>
