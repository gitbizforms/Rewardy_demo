<?php
	//header페이지
	$home_dir = str_replace( basename(__DIR__) , "" , __DIR__ );
	include $home_dir . "/inc_lude/header.php";

	if($user_level != '0'){
		header("Location:http://demo.rewardy.co.kr/index.php");
		exit;
	}

	if($user_id=='sadary0@nate.com'){
		if( $user_level != '0'){
			//alertMove("접속권한이 없습니다.");
			//exit;
		}
	}
	
	//페이지
	$p = $_POST['p']?$_POST['p']:$_GET['p'];
	if (!$p){
		$p = 1;
	}

	$pagingsize = 5;					//페이징 사이즈
	$pagesize = 20;						//페이지 출력갯수
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
	$where = " where state='0' and companyno='".$companyno."'";
	//$where = $where .= " and code in('800','900')";

	//전체 카운터수
	$sql = "select count(idx) as cnt from work_account_info ".$where."";
	$comcoin_cnt_info = selectQuery($sql);
	if($comcoin_cnt_info['cnt']){
		$total_count = $comcoin_cnt_info['cnt'];
	}
		$sql = "select idx, state, bank_name, bank_num, workdate, code, email, name, reward_user, reward_name, coin, coin_out, coin_type, memo, DATE_FORMAT(regdate, '%Y.%m.%d') AS ymd, DATE_FORMAT(regdate, '%h:%i:%s') AS his, regdate, commission, amount";
		$sql = $sql .= " from work_account_info";
		$sql = $sql .= " ".$where."";
		$sql = $sql .= " order by idx desc";
		$sql = $sql .= " limit ". $startnum.", ".$pagesize;
		// $sql = "select idx, state, name, bank_name, bank_num, coin, workdate from work_account_info where companyno = '".$companyno."' and workdate >= '".$sdate."' order by idx desc ";
		$comcoin_info = selectAllQuery($sql);

		$sql = "select idx, name from work_bank where state='0' order by rank asc";
		$bank_info = selectAllQuery($sql);
?>

<style>
	.rew_member_list {
		width: 1350px;
	}

	.member_list_header_in {
    	width: 1350px;
	}

	.member_list_conts {
     	width: 1350px;
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
							<div class="rew_member tyle_coin">
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
									<div class="rew_member_sub_func_tab" >
										<div class="rew_member_sub_func_tab_in">
											<div class="rew_member_sub_func_calendar">
												<div class="rew_member_sub_func_btns">
													<button class="on"><span>1주일</span></button>
													<button><span>1개월</span></button>
													<button><span>3개월</span></button>
												</div>
												<div class="rew_member_sub_func_period">
													<div class="rew_member_sub_func_period_box">
														<input type="text" class="input_date_l" value="<?=$week7?>" id="comcoin_sdate" />
														<button class="btn_calendar_l" id="btn_calendar_l">달력</button>
														<span>~</span>
														<input type="text" class="input_date_r" value="<?=TODATE?>" id="comcoin_edate" />
														<button class="btn_calendar_r" id="btn_calendar_r">달력</button>
													</div>
													<!-- <input type="submit" class="btn_inquiry" value="조회"> -->
													<button type="submit" class="btn_inquiry" id="btn_inquiry"><span>조회</span></button>
													<input type="hidden" id="reward_inquiry"/>
													<button type="submit" class="btn_inquiry" id="cal_inquiry"><span>날짜 초기화</span></button>
													<input type="hidden" id="calendar_inquiry"/>
												</div>
											</div>
										</div>
									</div>
									<div id="rew_list_search">
										<div class="rew_member_list">
											<div class="rew_member_list_in">
												<div class="member_list_header">
													<div class="member_list_header_in" id="member_list_comcoin_out_nocal">
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
														<div class="member_list_header_deposit">
															<strong>은행명</strong>
														</div>
														<div class="member_list_header_coin">
															<strong>계좌번호</strong>
														</div>
														<div class="member_list_header_coin">
															<strong>신청금액</strong>
														</div>
														<div class="member_list_header_coin">
															<strong>수수료</strong>
														</div>
														<div class="member_list_header_coin">
															<strong>지급할금액</strong>
														</div>
														<div class="member_list_header_deposit">
															<strong>상태</strong>
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
																	$bank_name = $comcoin_info['bank_name'][$i];
																	$bank_num = $comcoin_info['bank_num'][$i];
																	$amount = $comcoin_info['amount'][$i];

																	if($comcoin_info['state'][$i]==0){
																		$state = '확인중';
																	}elseif($comcoin_info['state'][$i]==1){
																		$state = '출금 대기';
																	}elseif($comcoin_info['state'][$i]==9){
																		$state = '출금 완료';
																	}elseif($comcoin_info['state'][$i]==3){
																		$state = '출금 반려';
																	}

																?>
																	<li>
																		<div class="member_list_conts_date">
																			<strong><?=$work_date?></strong>
																		</div>
																		<div class="member_list_conts_deposit">
																			<strong><?=$name?></strong>
																		</div>
																		<div class="member_list_conts_deposit">
																			<strong><?=$bank_name?></strong>
																		</div>
																		<div class="member_list_conts_coin">
																			<strong><?=$bank_num?></strong>
																		</div>
																		<div class="member_list_conts_coin">
																			<strong><?=number_format($comcoin_info['coin'][$i])?></strong>
																		</div>
																		<div class="member_list_conts_coin">
																			<strong><?=number_format($comcoin_info['commission'][$i])?></strong>
																		</div>
																		<div class="member_list_conts_coin">
																			<strong><?=number_format($amount)?></strong>
																		</div>
																		<div class="member_list_conts_deposit">
																			<strong style="color:grey"><?=$state?></strong>
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
				<input type="text" placeholder="부서명" id="team_add"/>
				<input type="hidden" id="team_real"/>
				<button id="tl_btn_team_add"><span>추가하기</span></button>
			</div>
		</div>
	</div>
	
	<?php
		//로딩 페이지
		include $home_dir . "loading.php";
	?>						
	<!-- <div class="rew_qck">
		<button class="btn_open_join"><span>회원가입</span></button>
		<button class="btn_open_login"><span>로그인</span></button>
		<button class="btn_open_repass"><span>비밀번호 재설정</span></button>
		<button class="btn_open_setting"><span>프로필 변경</span></button>
	</div> -->
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

			$(".button_prof").click(function(){
				$(".tl_prof_slc ul").show();
			});
			$("#btn_slc_character").click(function(){
				$(".rew_layer_character").show();
			});
			$(".rew_layer_character .tl_btn").click(function(){
				$(".rew_layer_character").hide();
			});
			$(".btn_profile").click(function(){
				$(".btn_profile").removeClass("on");
				$(this).addClass("on");
			});
			$(".tl_prof_slc").mouseleave(function(){
				$(".tl_prof_slc ul").hide();
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
