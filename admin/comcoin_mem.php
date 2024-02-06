<?php
	//header페이지
	$home_dir = str_replace( basename(__DIR__) , "" , __DIR__ );
	include $home_dir . "/inc_lude/header_index.php";
	///home/todaywork/rewardyNAS/user/

	if($user_level != '0'){
		header("Location:https://rewardy.co.kr/index.php");
		exit;
	}

	if($user_id=='sadary0@nate.com'){
		if( $user_level != '0'){
			//alertMove("접속권한이 없습니다.");
			//exit;
		}
	}

	$sql = "select idx, email, name, companyno from work_member where email = '".$user_id."'";
	$member_idx = selectQuery($sql);
	$mem_idx = $member_idx['idx'];
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

	//$pageurl = get_dirname();
	$pageurl = "comcoin_mem";
	//echo get_dirname();
	
	$string = "&page=".$pageurl."&sdate=".$sdate."&edate=".$edate."&nday=".$nday."&type=".$type;

	//공용코인
	$where = " where state='0' and companyno='".$companyno."' and highlevel!='1'";

	//정렬
	$orderby = " order by idx asc";

	//전체 카운터수
	$sql = "select count(1) as cnt from work_member ".$where."";
	$comcoin_cnt_info = selectQuery($sql);
	if($comcoin_cnt_info['cnt']){
		$total_count = $comcoin_cnt_info['cnt'];
	}


	//공용코인내역정보
		$sql = "select idx, email, name, part, partno, comcoin, highlevel from work_member";
		$sql = $sql .= " ".$where."";
		$sql = $sql .= " ".$orderby."";
		$sql = $sql .= " limit ". $startnum.", ".$pagesize;

		$comcoin_mem_info = selectAllQuery($sql);

	// 회사 보유 코인
		$sql = "select idx, comcoin from work_company where idx = '".$companyno."'";
		$com_all_coin = selectQuery($sql);
?>
<link rel="stylesheet" type="text/css" href="/html/css/set_head.css<?php echo VER;?>" />
<link rel="stylesheet" type="text/css" href="/html/css/set_04.css<?php echo VER;?>" />
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
						<div id="rew_conts_scroll_01" class="rew_conts_scroll_01">
							<div class="rew_member type_member_coin">
								<div class="rew_member_in" id="rew_member_in">
									<div class="rew_member_banner">
										<div class="rew_member_banner_in">
											<p>우리 회사 전체 공용코인 : <strong><?=number_format($com_all_coin['comcoin'])?></strong></p></br>
											<p>구성원에게 분배한 공용코인 : <strong><?=number_format($company_comcoin)?></strong></p></br>
											<p>사용가능한 공용코인 : <strong><?=number_format($com_all_coin['comcoin'] - $company_comcoin)?></strong></p>
											<button><span>코인 충전하기</span></button>
										</div>
									</div>
									<div class="rew_member_func">
										<div class="rew_member_func_in">
											<div class="rew_member_count" id="rew_member_count">
												<span>멤버관리</span>
												<strong><?=$total_count?></strong>
											</div>
											<div class="rew_member_search">
												<div class="rew_member_search_box">
													<input type="text" class="input_search" placeholder="이름, 부서명을 검색" id="comcoin_search">
													<button id="comcoin_search_btn"><span>검색</span></button>
												</div>
											</div>
										</div>
									</div>
									<div class="rew_member_list" id="rew_member_list">
										<div class="rew_member_list_in" >
											<input id="page_num" value="<?=$p?>">
											<div class="member_list_header">
												<div class="member_list_header_in" id="member_list_comcoin">
													<input type="hidden" value="idx" id="kind">
													<input type="hidden" value="up" id="tclass">
													<input type="hidden" id="cli_kind">
													<div class="member_list_header_name">
														<strong value="name">이름</strong>
														<em>
															<button class="btn_sort_up" title="오름차순"></button>
															<button class="btn_sort_down" title="내림차순"></button>
														</em>
													</div>
													<div class="member_list_header_team">
														<strong value="part">부서</strong>
														<em>
															<button class="btn_sort_up" title="오름차순"></button>
															<button class="btn_sort_down" title="내림차순"></button>
														</em>
													</div>
													<div class="member_list_header_email">
														<strong value="email">이메일</strong>
														<em>
															<button class="btn_sort_up" title="오름차순"></button>
															<button class="btn_sort_down" title="내림차순"></button>
														</em>
													</div>
													<div class="member_list_header_have_coin">
														<strong value="comcoin">보유한 코인</strong>
														<em>
															<button class="btn_sort_up" title="오름차순"></button>
															<button class="btn_sort_down" title="내림차순"></button>
														</em>
													</div>
													<div class="member_list_header_give">
														<strong>지급/회수</strong>
													</div>
													<div class="member_list_header_history">
														<strong>내역</strong>
													</div>
												</div>
											</div>
											<div class="member_list_conts">
												<div class="member_list_conts_in">
													<ul id="member_list_conts_ul">
														<input type="hidden" value="<?=$mem_idx?>" id="member_idx">
														<?for($i=0; $i<count($comcoin_mem_info['idx']); $i++){
															$comcoin_idx = $comcoin_mem_info['idx'][$i];
															$comcoin_email = $comcoin_mem_info['email'][$i];
															$comcoin_name = $comcoin_mem_info['name'][$i];
															$comcoin_part = $comcoin_mem_info['part'][$i];
															$comcoin_highlevel = $comcoin_mem_info['highlevel'][$i];
															// $sql = "select count(idx) as cnt from work_coininfo where state='0' and email = '".$comcoin_email."' and reward_user = '".$user_id."' and (code = 600 or code = 620)";
															// $use_list = selectQuery($sql);
															// $use = $use_list['cnt'];
															$comcoin_comcoin = $comcoin_mem_info['comcoin'][$i];
														?>
															<li>
																<div class="member_list_conts_name">
																	<strong style="width: 47px;"><?=$comcoin_name?></strong>
																</div>
																<div class="member_list_conts_team">
																	<strong style="width: 72px;"><?=$comcoin_part?></strong>
																</div>
																<div class="member_list_conts_email">
																	<strong style="width: 177px;"><?=$comcoin_email?></strong>
																</div>
																<div class="member_list_conts_have_coin">
																	<strong><?=number_format($comcoin_comcoin)?></strong>
																</div>
																<div class="member_list_conts_give">
																	<button class="btn_list_give" id="btn_list_give" value="<?=$comcoin_idx?>"><span>지급</span></button>
																	<button class="btn_list_debt" id="btn_list_debt" value="<?=$comcoin_idx?>"><span>회수</span></button>
																</div>
																<div class="member_list_conts_history">
																	<form action="comcoin_mem_list.php" method="POST">
																		<input type="hidden" id="history_idx" name="email" class="history_idx" value="<?=$comcoin_email?>">
																		<?  if($comcoin_comcoin>=0){?>
																			<button type="submit" id="btn_list_history" class="btn_list_history<?=$comcoin_comcoin?>" value=<?=$comcoin_email?> ><span>내역</span></button>
																		<?}else{?>
																			<button type="submit" id="btn_list_history" class="btn_list_history<?=$comcoin_comcoin==0?" zero":""?>" value=<?=$comcoin_email?> disabled style="cursor:default"><span>내역</span></button>
																		<?}?>
																	</form>
																</div>
															</li>
														<?}?>
													</ul>
												</div>
											</div>
										</div>
									</div>

									<?if($comcoin_mem_info['idx']){?>
										<div class="rew_ard_paging" id="rew_ard_paging">
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

	


	<div class="coin_debt" id="coin_debt" style="display:none;">
		<div class="cd_deam"></div>
		<div class="cd_in">
			<div class="cd_box">
				<div class="cd_box_in">
					<div class="cd_close">
						<button><span>닫기</span></button>
					</div>
					<div class="cd_top">
						<strong>공용코인 회수</strong>
					</div>
					<div class="cd_area">
						<div class="cd_user_area">
							<div class="cd_user_img">
								<div class="cd_user_img_in" style="background-image:url(images/pre/img_prof_02.png);"></div>
							</div>
							<div class="cd_name">
								<div class="cd_name_user">윤지혜</div>
								<div class="cd_name_team">디자인팀</div>
							</div>
						</div>

						<div class="cd_box_area">
							<div class="cd_box_area_in">
								<div class="cd_box_calc">
									<ul>
										<li>
											<span class="cd_box_tit">보유한 공용코인</span>
											<strong class="cd_box_coin">18,000</strong>
											<span class="cd_box_txt">코인</span>
										</li>
										<li>
											<span class="cd_box_tit">회수할 공용코인</span>
											<input type="text" class="cd_box_input" value="-10,000" />
											<span class="cd_box_txt">코인</span>
										</li>
										<li>
											<span class="cd_box_tit">합계</span>
											<strong class="cd_box_coin">8,000</strong>
											<span class="cd_box_txt">코인</span>
										</li>
									</ul>
								</div>
								<div class="cd_box_final">
									<dl>
										<dt>보유한 공용코인</dt>
										<dd>631,800</dd>
									</dl>
									<dl>
										<dt>회수 후 공용코인</dt>
										<dd class="cd_up"><strong>641,800</strong></dd>
									</dl>
								</div>
							</div>
						</div>

					</div>

					<div class="cd_bottom">
						<button class="on">회수하기</button>
					</div>

				</div>
			</div>
		</div>
	</div>

	<div class="coin_give" id="coin_give" style="display:none;">
		<div class="cg_deam"></div>
		<div class="cg_in">
			<div class="cg_box">
				<div class="cg_box_in">
					<div class="cg_close">
						<button><span>닫기</span></button>
					</div>
					<div class="cg_top">
						<strong>공용코인 지급</strong>
					</div>
					<div class="cg_area">
						<div class="cg_user_area">
							<div class="cg_user_img">
								<div class="cg_user_img_in" style="background-image:url(images/pre/img_prof_02.png);"></div>
							</div>
							<div class="cg_name">
								<div class="cg_name_user">윤지혜</div>
								<div class="cg_name_team">디자인팀</div>
							</div>
						</div>

						<div class="cg_box_area">
							<div class="cg_box_area_in">
								<div class="cg_box_calc">
									<ul>
										<li>
											<span class="cg_box_tit">보유한 공용코인</span>
											<strong class="cg_box_coin">123,000</strong>
											<span class="cg_box_txt">코인</span>
										</li>
										<li>
											<span class="cg_box_tit">지급할 공용코인</span>
											<input type="text" class="cg_box_input" id="cg_box_input" value="10,000"/>
											<span class="cg_box_txt">코인</span>
										</li>
										<li>
											<span class="cg_box_tit">합계</span>
											<strong class="cg_box_coin" id="give_tot_coin">133,000</strong>
											<span class="cg_box_txt">코인</span>
										</li>
									</ul>
								</div>
								<div class="cg_box_final">
									<dl>
										<dt>보유한 공용코인</dt>
										<dd>631,800</dd>
									</dl>
									<dl>
										<dt>지급 후 공용코인</dt>
										<dd class="cg_down"><strong>621,800</strong></dd>
									</dl>
								</div>
							</div>
						</div>

					</div>

					<div class="cg_bottom" id="cg_bottom">
						<button class="on">지급하기</button>
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
