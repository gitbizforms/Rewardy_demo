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
		$startnum = 1;
	}else{
		$startnum = ($p - 1) * $pagesize + 1;
	}

	//일주일
	$week7 = date("Y-m-d",strtotime("-1 week", TODAYTIME));


	$pageurl = get_dirname();

	$string = "&page=".$pageurl."&sdate=".$sdate."&edate=".$edate."&nday=".$nday."&type=".$type;



	//공용코인
	$where = " where state in ('1','2') and companyno='".$companyno."' and coin_type='1'";
	//$where = $where .= " and code in('800','900')";


	//전체 카운터수
	$sql = "select count(1) as cnt from work_coininfo ".$where."";
	$comcoin_cnt_info = selectQuery($sql);
	if($comcoin_cnt_info['cnt']){
		$total_count = $comcoin_cnt_info['cnt'];
	}


	//공용코인내역정보
	// if($_SERVER['HTTP_HOST'] == T_DOMAIN){
		$sql = "select idx, state, code, email, name, reward_user, reward_name, coin, coin_out, coin_type, memo, DATE_FORMAT(regdate, '%Y.%m.%d') AS ymd, DATE_FORMAT(regdate, '%h:%i:%s') AS his, regdate";
		$sql = $sql .= " from work_account_info";
		$sql = $sql .= " ".$where."";
		$sql = $sql .= " order by idx desc";
		$sql = $sql .= " limit ". ($p-1)*$startnum.", ".$endnum;
	// }else{
	// 	$sql = "select * from";
	// 	$sql = $sql .= " (select ROW_NUMBER() over(order by idx desc) as r_num, idx, state, code, email, name, reward_user, reward_name, coin, coin_out, coin_type, memo, CONVERT(CHAR(10), regdate, 102) as ymd, CONVERT(CHAR(8), regdate, 108) as his, convert(varchar, regdate , 120) as regdate";
	// 	$sql = $sql .= " from work_account_info";
	// 	$sql = $sql .= " ".$where.")";
	// 	$sql = $sql .= " as a where r_num between ". $startnum ." and " .$endnum ." order by idx desc";
	// }
	$comcoin_info = selectAllQuery($sql);

?>


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

									<div class="rew_member_tab_03">
										<div class="rew_member_tab_03_in">
											<ul>
												<li><a href="#"><span>공용코인 충전</span></a></li>
												<li><a href="/admin/comcoin_account.php"><span>공용코인 출금</span></a></li>
												<li class="on"><a href="#"><span>입출금 내역</span></a></li>
											</ul>
										</div>
									</div>

									<div class="rew_member_sub_func_tab" >
										<div class="rew_member_sub_func_tab_in">
											<div class="rew_member_sub_func_sort" id="rew_member_sub_func_sort">
												<div class="rew_member_sub_func_sort_in">
													<button class="btn_sort_on" id="btn_sort_on"><span>전체보기</span></button>
													<ul>
														<li><button value="all"><span>전체보기</span></button></li>
														<li><button value="in"><span>입금</span></button></li>
														<li><button value="out"><span>출금</span></button></li>
													</ul>
												</div>
											</div>
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
													<button class="btn_inquiry" id="btn_inquiry"><span>조회</span></button>
													<input type="hidden" id="reward_inquiry"/>
												</div>
											</div>
										</div>
									</div>

									<div class="rew_member_list">
										<div class="rew_member_list_in">
											<div class="member_list_header">
												<div class="member_list_header_in">
													<div class="member_list_header_datetime">
														<strong>일시</strong>
													</div>
													<div class="member_list_header_deposit">
														<strong>입금</strong>
													</div>
													<div class="member_list_header_withdraw">
														<strong>출금</strong>
													</div>
													<div class="member_list_header_coin">
														<strong>남은 코인</strong>
													</div>
													<div class="member_list_header_admin_user">
														<strong>관리자</strong>
													</div>
													<div class="member_list_header_detail">
														<strong>상세내용</strong>
													</div>
												</div>
											</div>
											<div class="member_list_conts">
												<div class="member_list_conts_in">
													<ul class="member_list_conts_ul">
														<? 
														if(count($comcoin_info['idx'])>0){
															for($i=0; $i<count($comcoin_info['idx']); $i++){

															$code = $comcoin_info['code'][$i];
															$memo = $comcoin_info['memo'][$i];
															$email = $comcoin_info['email'][$i];
															$name = $comcoin_info['name'][$i];
															$coin = $comcoin_info['coin'][$i];
															$coin_out = $comcoin_info['coin_out'][$i];

															$reward_user = $comcoin_info['reward_user'][$i];
															$reward_name = $comcoin_info['reward_name'][$i];
															$coin_type = $comcoin_info['coin_type'][$i];

															$ymd = $comcoin_info['ymd'][$i];
															$his = $comcoin_info['his'][$i];

															$reg_date = $ymd . " ". $his;
															$mem_info = member_row_info($email);

															if($state==0){
																$state_text = "신청중";
															}else if($state==1){
																$state_text = "-";
															}else if($state==2){
																$state_text = "신청완료";
															}
															$name = "-";
															?>
															<li>
																<div class="member_list_conts_datetime">
																	<strong><?=$reg_date?></strong>
																</div>
																<div class="member_list_conts_deposit">
																	<strong><?=number_format($coin)?></strong>
																</div>
																<div class="member_list_conts_withdraw">
																	<strong><?=number_format($coin_out)?></strong>
																</div>
																<div class="member_list_conts_coin">
																	<strong><?=number_format($mem_info['comcoin'])?></strong>
																</div>
																<div class="member_list_conts_admin_user">
																	<strong><?=$name?></strong>
																</div>
																<div class="member_list_conts_detail">
																	<strong><?=$memo?> (<?=$state_text?>)</strong>
																</div>
															</li>
															<?}
															}else{?>
															<li class="search_list_none"><span>입출금 내역이 없습니다.</span></li>
														<?}?>
													</ul>
												</div>
											</div>
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
