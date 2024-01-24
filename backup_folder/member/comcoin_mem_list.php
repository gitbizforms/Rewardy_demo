<?php
	//header페이지
	$home_dir = str_replace( basename(__DIR__) , "" , __DIR__ );
	include $home_dir . "/inc_lude/header.php";
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

	//페이지
	$tclass = $_POST['this_class'];
	$sort_kind = $_POST['sort_kind'];
	// $tclass = str_replace("btn_sort_","",$tclass);

	if($tclass){
		if($tclass =='btn_sort_up'){
			$order = " asc";
		}elseif($tclass == 'btn_sort_down'){
			$order = " desc";
		}
	}else{
		$order = " desc";
	}
	
	//정렬기준
	if($sort_kind){
		$qry = "workdate";
	}else{
		$qry = "regdate";
	}

	$email = $_POST['email'];

	$nday = $_POST['nday'];
	$string = $_POST['string'];
	$type = $_POST['type'];
	
	

	$p = $_POST['p']?$_POST['p']:$_GET['p'];
	if (!$p){
		$p = 1;
	}

	$pagingsize = 5;					//페이징 사이즈
	$pagesize = 10;						//페이지 출력갯수
	$startnum = 0;						//페이지 시작번호
	$endnum = $p * $pagesize;			//페이지 끝번호

	//시작번호
	if ($p == 1){
		$startnum = 0;
	}else{
		$startnum = ($p - 1) * $pagesize;
	}

	if($string){
		parse_str($string, $output);
		$url = $output['page'];
		$sdate = $output['sdate'];
		$edate = $output['edate'];
		$nday = $output['nday'];
		$type = $output['type'];
	}

	$url = "history_nocal";
	$string = "&page=".$url."&sdate=".$sdate."&edate=".$edate."&nday=".$nday."&type=".$type;

	//일주일
	$week7 = date("Y-m-d",strtotime("-1 week", TODAYTIME));

	$where = "where email = '".$email."' and state='0' ";

	$kind = $_POST['kind'];
	if($kind=="all"){
		$where = $where .= " and code in(510,520,600,620,710) ";
		$kindname = "전체보기";
	}elseif($kind=="in"){
		$where = $where .= " and code in (600,510)";
		$kindname = "지급";
	}elseif($kind=="out"){
		$where = $where .= " and code in (520,620,710,630,640)";
		$kindname = "차감";
	}else{
		$kind="all";
		$where = $where .= " and code in(510,520,600,620,710,630,640) ";
		$kindname = "전체보기";
	}

	$sql = "select idx,name,email,reward_user,coin,coin_out,code,workdate,memo";
	$sql = $sql .= " from work_coininfo";
	$sql = $sql .= " ".$where."";
	$sql = $sql .= " order by ". $qry." ". $order;
	$sql = $sql .= " limit ". $startnum.", ".$pagesize;

	$history_info = selectAllQuery($sql);


	$sql = "select count(*) as cnt from work_coininfo ". $where;
	$history_info_cnt = selectQuery($sql);
	if($history_info_cnt['cnt']){ 
		$total_count = $history_info_cnt['cnt'];
	}

	$sql2 = "select name, comcoin, email from work_member where email = '".$email."'";
	$work_member = selectQuery($sql2);

		$sdate = $week7;
		$edate = TODATE;
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
						<div class="rew_member_tab">
							<div class="rew_member_tab_in">
								<ul>
									<li class="member_list"><a href="#"><span>멤버관리</span></a></li>
									<li class="comlogo"><a href="#"><span>홈페이지 설정</span></a></li>
									<li class="comcoin"><a href="#"><span>공용코인 관리</span></a></li>
									<li class="comcoin_member on"><a href="#"><span>멤버별 공용코인</span></a></li>
									<li class="comcoin_out_page"><a href="#"><span>코인출금 신청내역</span></a></li>
								</ul>
							</div>
						</div>
						<div id="rew_conts_scroll_01" class="rew_conts_scroll_01">
							<input type="hidden" id="email_history" value="<?=$email?>">
							<div class="rew_member tyle_coin">
								<div class="rew_member_in" id="rew_member_in">
									<div class="rew_member_func">
										<div class="rew_member_func_in">
											<div class="rew_member_count" id="rew_member_count">
												<span><?=$work_member['name']?>님이 보유중인 공용코인</span>
												<strong><?=number_format($work_member['comcoin'])?> 코인</strong>
											</div>
										</div>
									</div>
									<div class="rew_member_sub_func_tab" >
										<div class="rew_member_sub_func_tab_in">
											<div class="rew_member_sub_func_sort" id="rew_member_sub_func_sort_kind_nocal">
												<div class="rew_member_sub_func_sort_in">
													<button class="btn_sort_on" id="btn_sort_on" value="<?=$kind?>"><span><?=$kindname?></span></button>
													<ul>
														<li><button value="all"><span>전체보기</span></button></li>
														<li><button value="in"><span>지급</span></button></li>
														<li><button value="out"><span>차감</span></button></li>
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
														<input type="text" class="input_date_l" value="<?=$sdate?>" id="comcoin_sdate" />
														<button class="btn_calendar_l" id="btn_calendar_l">달력</button>
														<span>~</span>
														<input type="text" class="input_date_r" value="<?=$edate?>" id="comcoin_edate" />
														<button class="btn_calendar_r" id="btn_calendar_r">달력</button>
													</div>
													<!-- <input type="submit" class="btn_inquiry" value="조회"> -->
													<button type="submit" class="btn_inquiry" id="btn_history"><span>조회</span></button>
													<input type="hidden" id="reward_inquiry"/>
													<button type="submit" class="btn_inquiry" id="cal_history"><span>날짜 초기화</span></button>
													<input type="hidden" id="reward_inquiry"/>
												</div>
											</div>
										</div>
									</div>
									<div id="rew_list_search">
										<div class="rew_member_list">
											<div class="rew_member_list_in">
												<div class="member_list_header">
													<div class="member_list_header_in" id="member_list_history_nocal">
														<input type="hidden" value="idx" id="sort_kind">
														<div class="member_list_header_deposit">
															<strong value="kind">분류</strong>
														</div>
														<div class="member_list_header_date">
															<strong value="workdate" id="sortdate">일자</strong>
															<em>
																<button class="btn_sort_up"  title="오름차순"></button>
																<button class="btn_sort_down" title="내림차순"></button>
															</em>
														</div>
														<div class="member_list_header_deposit">
															<strong value="reward_name">지급/회수자</strong>
														</div>
														<div class="member_list_header_deposit">
															<strong value="name">이름</strong>
														</div>
														<!-- <div class="member_list_header_coin">
															<strong value="comcoin">보유한 공용코인</strong>
														</div> -->
														<div class="member_list_header_coin">
															<strong>지급/회수한 코인</strong>
														</div>
														<div class="member_list_header_deposit">
															<strong value="part">메모</strong>
														</div>
													</div>
												</div>
												<div class="list_paging" id="list_paging">
													<div class="member_list_conts" id="member_list_conts">
													<input type="hidden" id="kind" value="idx">
														<div class="member_list_conts_in">
															<ul class="member_list_conts_ul">
																<?
																if(count($history_info['idx'])!=0){
																	for($i=0; $i<count($history_info['idx']); $i++){
																		$id = $history_info['email'][$i];
																		$name = $history_info['name'][$i];
																		$workdate = $history_info['workdate'][$i];
																		$coin = $history_info['coin'][$i];
																		$memo = $history_info['memo'][$i];
																		if(mb_strlen($memo)>=9){
																			$memo = mb_substr($memo,0,9,'utf-8')."..";
																		}else{
																			$memo = $memo;
																		}
																		$number_coin = number_format($coin);
																		$code = $history_info['code'][$i];

																		$sql = "select email, name, companyno from work_member where email = '".$history_info['reward_user'][$i]."'";
																		$query = selectQuery($sql);
																		$reward_user = $query['name']; 

																		if(in_array($code,array('600','510'))){
																			$kind = "지급";
																		}elseif(in_array($code,array('520','620','710','630','640'))){
																			$kind = "차감";
																		}

																		?>
																		<li>
																			<div class="member_list_conts_deposit">
																				<strong><?=$kind?></strong>
																			</div>
																			<div class="member_list_conts_date">
																				<strong><?=$workdate?></strong>
																			</div>
																			<div class="member_list_conts_deposit">
																				<strong><?=$reward_user?></strong>
																			</div>
																			<div class="member_list_conts_deposit">
																				<strong><?=$name?></strong>
																			</div>
																			<? if(in_array($code,array('510','600'))){?>
																				<div class="member_list_conts_coin">
																					<strong style="color:blue;font-weight:bold;"><?="+".$number_coin?></strong>
																				</div>
																			<?}elseif(in_array($code,array('520','620','710','630','640'))){?>
																				<div class="member_list_conts_coin">
																					<strong style="color:red;font-weight:bold;"><?="-".$number_coin?></strong>
																				</div>
																			<?}?>
																			<div class="member_list_conts_deposit">
																				<strong><?=$memo?></strong>
																			</div>
																		</li>
																	<?}
																}else{?>
																<li class="search_list_none"><span>조회 결과가 없습니다.</span></li>
																<?}?>
															</ul>
														</div>
													</div>
											<?if($history_info['idx']){?>
												<div class="rew_ard_paging" id="rew_ard_paging">
													<div class="rew_ard_paging_in">
														<input type="hidden" id="tclass" value="btn_sort_down">
														<?
															//페이징사이즈, 전체카운터, 페이지출력갯수
															echo pageing($pagingsize, $total_count, $pagesize, $string);

														?>
													</div>
												</div>
											<?}?>
											</div>
										</div>
										<!--페이징 작업 필요-->
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
