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
	$sql = "select count(idx) as cnt from work_account_info ".$where."";
	$comcoin_cnt_info = selectQuery($sql);
	if($comcoin_cnt_info['cnt']){
		$total_count = $comcoin_cnt_info['cnt'];
	}
		$sql = "select idx, state, bank_name, bank_num, workdate, code, email, name, part, reward_user, reward_name, coin, coin_out, coin_type, memo, DATE_FORMAT(regdate, '%Y.%m.%d') AS ymd, DATE_FORMAT(regdate, '%h:%i:%s') AS his, regdate, commission, amount";
		$sql = $sql .= " from work_account_info";
		$sql = $sql .= " ".$where."";
		$sql = $sql .= " order by idx desc";
		$sql = $sql .= " limit ". $startnum.", ".$pagesize;
		// $sql = "select idx, state, name, bank_name, bank_num, coin, workdate from work_account_info where companyno = '".$companyno."' and workdate >= '".$sdate."' order by idx desc ";
		$comcoin_info = selectAllQuery($sql);

		$sql = "select idx, name from work_bank where state='0' order by rank asc";
		$bank_info = selectAllQuery($sql);
?>
<link rel="stylesheet" type="text/css" href="/html/css/set_head.css<?php echo VER;?>" />
<link rel="stylesheet" type="text/css" href="/html/css/set_05_01.css<?php echo VER;?>" />
<style>
	.rew_member_list {
		width: 1100px;
	}

	.member_list_header_in {
		width: 1100px;
	}

	.member_list_conts {
		width: 1100px;
	}
</style>
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
							<div class="rew_member tyle_money">
								<div class="rew_member_in" id="rew_member_in">
									<!-- <div class="rew_member_tab_03">
										<div class="rew_member_tab_03_in">
											<ul>
												<li><a href="#"><span>공용코인 충전</span></a></li>
												<li><a href="/admin/comcoin_account.php"><span>공용코인 출금</span></a></li>
												<li class="on"><a href="#"><span>입출금 내역</span></a></li>
											</ul>
										</div>
									</div> -->
									<div class="rew_member_sub_func_tab">
										<div class="rew_member_sub_count">
											<span>출금신청 내역 <em>34</em></span>
										</div>
										<div class="rew_member_sub_func_tab_in">
											<div class="rew_member_sub_func_calendar">

												<div class="rew_member_sub_func_period">
													<div class="rew_member_sub_func_period_box">
														<input type="text" class="input_date" value="<?=$wdate?>" maxlength="10" id="coin_work_date"
															style="display:none;">
														<input type="text" class="input_date" id="coin_work_month" value="<?=$month_date?>"
															data-min-view="months" data-view="months" data-date-format="yyyy.mm" readonly="readonly">
													</div>
												</div>
											</div>
											<div class="rew_member_sub_func_sort" id="rew_member_sub_func_list">
												<div class="rew_member_sub_func_sort_in">
													<button class="btn_sort_on" id="btn_sort_on"><span>30개 보기</span></button>
													<ul>
														<li value="10"><button><span>10개 보기</span></button></li>
														<li value="15"><button><span>15개 보기</span></button></li>
														<li value="30"><button><span>30개 보기</span></button></li>
														<li value="50"><button><span>50개 보기</span></button></li>
														<li value="100"><button><span>100개 보기</span></button></li>
													</ul>
												</div>
											</div>
											<div class="rew_member_sub_func_btns">
												<button class="coin_all_excel">엑셀 다운로드</button>
												<button class="coin_all_pay">선택 입금완료</button>
											</div>
										</div>

									</div>
									<div id="rew_list_search">
										<div class="rew_member_list">
											<div class="rew_member_list_in">
												<div class="member_list_header">
													<div class="member_list_header_in" id="member_list_comcoin_out_nocal">
														<div class="member_list_header_choice">
															<input type="checkbox" id="select-all-checkbox">
															<label for="select-all-checkbox">선택</label>
														</div>
														<div class="member_list_header_date">
															<strong value="workdate">날짜</strong>
															<em>
																<button class="btn_sort_up" title="오름차순"></button>
																<button class="btn_sort_down" title="내림차순"></button>
															</em>
														</div>
														<div class="member_list_header_deposit">
															<strong value="sortname">이름</strong>
															<em>
																<button class="btn_sort_up" title="오름차순"></button>
																<button class="btn_sort_down" title="내림차순"></button>
															</em>
														</div>
														<div class="member_list_header_part">
															<strong value="part">부서</strong>
															<em>
																<button class="btn_sort_up" title="오름차순"></button>
																<button class="btn_sort_down" title="내림차순"></button>
															</em>
														</div>
														<div class="member_list_header_coin">
															<strong value="sub_coin">신청금액</strong>
															<em>
																<button class="btn_sort_up" title="오름차순"></button>
																<button class="btn_sort_down" title="내림차순"></button>
															</em>
														</div>
														<div class="member_list_header_state">
															<strong value = "sub_state">상태</strong>
															<em>
																<button class="btn_sort_up" title="오름차순"></button>
																<button class="btn_sort_down" title="내림차순"></button>
															</em>
														</div>
														<div class="member_list_header_manager">
															<strong>관리</strong>
														</div>
													</div>
												</div>
												<div class="list_paging" id="list_paging">
													<div class="member_list_conts">
														<div class="member_list_conts_in">
															<ul class="member_list_conts_ul">
																<? 
																if(count($comcoin_info['idx'])>0){
																	for($i=0; $i<count($comcoin_info['idx']); $i++){
																	$name = $comcoin_info['name'][$i];
																	$work_date = $comcoin_info['workdate'][$i];
																	$email = $comcoin_info['email'][$i];
																	// $bank_name = $comcoin_info['bank_name'][$i];
																	// $bank_num = $comcoin_info['bank_num'][$i];
																	$amount = $comcoin_info['amount'][$i];
																	$part = $comcoin_info['part'][$i];

																	if($comcoin_info['state'][$i]==0){
																		$state = '입금 확인중';
																	}elseif($comcoin_info['state'][$i]==1){
																		$state = '출금 대기';
																	}elseif($comcoin_info['state'][$i]==9){
																		$state = '입금 완료';
																	}elseif($comcoin_info['state'][$i]==3){
																		$state = '출금 반려';
																	}

																?>
																<li>
																	<div class="member_list_conts_choice">
																		<input type="hidden" class="mem_idx" value="<?=$comcoin_info['idx'][$i]?>">
																		<input type="checkbox" name="selected_comcoin[]" id = "selected_comcoin<?=$i?>" value="<?=$comcoin_info['email'][$i]?>">
																		<label for="selected_comcoin<?=$i?>"></label>
																	</div>
																	<div class="member_list_conts_date">
																		<strong><?=$work_date?></strong>
																	</div>
																	<div class="member_list_conts_deposit color_n">
																		<strong><?=$name?></strong>
																	</div>
																	<div class="member_list_conts_part">
																		<strong><?=$part?></strong>
																	</div>
																	<div class="member_list_conts_coin">
																		<strong><?=number_format($comcoin_info['coin'][$i])?></strong>
																	</div>

																	<div class="member_list_conts_state">
																		<strong style="<?= ($comcoin_info['state'][$i]==0?"color:#f10006":"color:grey")?>"><?=$state?></strong>
																	</div>

																	<div class="member_list_conts_manager">
																		<strong><button class="coin_mem_pay <?= $comcoin_info['state'][$i]==0?"on":"off"?>" value="<?= $comcoin_info['email'][$i]?>"><?= $comcoin_info['state'][$i]==0?"입금확인":"입금완료"?></button></strong>
																	</div>

																</li>
																<?}
																}else{?>
																<li class="search_list_none"><span>코인출금 신청내역이 없습니다.</span></li>
																<?}?>
															</ul>
														</div>
													</div>
													<?if($comcoin_info['idx']){?>
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
														<button><span>❗ 출금 코인의 세금 처리 방법?</span></button>
													</div>
													<input type="hidden" value="workdate" id="kind">
													<input type="hidden" value="btn_sort_down" id="tclass">
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

	<div class="coin_faq_layer" style="display:none;">
		<div class="cfl_deam"></div>
		<div class="cfl_in">
			<div class="cfl_box">
				<div class="cfl_box_in">
					<div class="cfl_close">
						<button><span>닫기</span></button>
					</div>
					<div class="cfl_top">
						<strong>코인 출금 관련 FAQ</strong>
					</div>
					<div class="cfl_area">
						<ul>
							<li>
								<dt>Q. 개인이 출금한 코인은 근로소득으로 잡아야 하나요?</dt>
								<dd>네 맞습니다. <br />
									원천징수 다운로드 자료를 다운로드 받으신 뒤, 급여명세서에 <br />
									[리워디 수당] 등의 항목을 신규로 추가하고, 회사가 정한 기간에 <br />
									따라 근로소득으로 신고해 주시면 됩니다.</dd>
							</li>
							<li>
								<dt>Q. 출금수수료는 무엇이며, 누가 내나요?</dt>
								<dd>출금수수료는 개인이 코인을 찾을 때 발생하며, <br />
									출금금액의 5 %입니다. 해당 금액은 출금을 위해 필요한 수수료, <br />
									시스템 및 인력 등을 활용하는 데 쓰입니다.</dd>
							</li>
						</ul>
					</div>
					<div class="cfl_bottom">
						<button>더 많은 FAQ 보기</button>
					</div>
				</div>
			</div>
		</div>
	</div>

	<div class="t_layer rew_layer_team_management" style="display:none;">
		<div class="tl_deam"></div>
		<div class="tl_in">
			<div class="tl_close">
				<button><span>닫기</span></button>
			</div>
			<div class="tl_tit">
				<strong>부서명 관리</strong>
				<span>부서명을 등록하고 관리하세요!</span>
			</div>
			<div class="tl_list" id="tl_list">
				<ul>
					<?for($i=0; $i<count($team_info['idx']); $i++){
						$part_idx = $team_info['idx'][$i];
						$partname = $team_info['partname'][$i];
					?>
					<li>
						<div class="tc_input" value="<?=$i?>">
							<div class="team_area" id="team_area_<?=$i?>" value="<?=$part_idx?>">
								<input type="text" class="input_team" id="input_team_<?=$i?>" disabled value="<?=$partname?>" />
								<button class="btn_team_regi" id="btn_team_regi_<?=$i?>"><span>변경</span></button>
								<button class="btn_team_del" id="btn_team_del_<?=$i?>"><span>삭제</span></button>
							</div>
						</div>
					</li>
					<?}?>
				</ul>
			</div>
			<div class="tl_btn_team">
				<input type="text" placeholder="부서명" id="team_add" />
				<input type="hidden" id="team_real" />
				<button id="tl_btn_team_add"><span>추가하기</span></button>
			</div>
		</div>
	</div>

	<script>
		// JavaScript for Select All functionality
		document.getElementById('select-all-checkbox').addEventListener('change', function () {
			var checkboxes = document.querySelectorAll('input[name="selected_comcoin[]"]');
			checkboxes.forEach(function (checkbox) {
				checkbox.checked = this.checked;
			}.bind(this));
		});
	</script>
	<script type="text/javascript">
		$(document).ready(function () {

			var list_leng = $(".rew_layer_team_management .tl_list > ul > li").length - 1;
			if (list_leng > 4) {
				$(".t_layer.rew_layer_team_management").css({
					"height": 619,
					"marginTop": -320
				});
			} else {
				var list_lengx = 65 * list_leng;
				$(".t_layer.rew_layer_team_management").css({
					"height": 359 + list_lengx,
					"marginTop": -(359 + list_lengx) / 2
				});
			}

			$(".btn_open_join").click(function () {
				$(".rew_layer_join").show();
			});
			$(".btn_open_login").click(function () {
				$(".rew_layer_login").show();
			});
			$(".btn_open_repass").click(function () {
				$(".rew_layer_repass").show();
			});
			$(".btn_open_setting").click(function () {
				$(".rew_layer_setting").show();
			});
			$(".tl_close button").click(function () {
				$(this).closest(".t_layer").hide();
			});
			$(".rew_member_sub_text button").click(function () {
				$(".coin_faq_layer").show();
			});
			$(".cfl_close button").click(function () {
				$(".coin_faq_layer").hide();
			});

		});

		$(document).ready(function () {
			window.onbeforeunload = function () {
				$('.rewardy_loading_01').css('display', 'block');
			}
			$(window).load(function () { //페이지가 로드 되면 로딩 화면을 없애주는 것
				$('.rewardy_loading_01').css('display', 'none');
			});
		});
		window.onpageshow = function (event) {
			if (event.persisted || (window.performance && window.performance.navigation.type == 2)) {
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