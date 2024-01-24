<?
	//header페이지
	$home_dir = str_replace( basename(__DIR__) , "" , __DIR__ );
	include $home_dir . "/inc_lude/header.php";

?>

	<script type="text/javascript">
		$(document).ready(function(){
			setTimeout(function(){
				tuto_position();
			},1300);

			$(window).resize(function(){
				tuto_position();
			}); 

			$(window).scroll(function(){
				tuto_position();
			}); 

			$(".tuto_phase_pause button").click(function(){
				$(".tuto_phase").hide();
			});

			$(".rew_box").addClass("on");
			$(".rew_menu_onoff button").addClass("on");

		});


		function cli_next(idx,level){
			var next_idx = idx + 1;
			$(".tuto_mark_01_0"+idx).hide();
			$(".tuto_pop_01_0"+idx).hide();

			if(level == 'party'){
				if(idx == 1){
					$(".rew_box").removeClass("on");
					$(".rew_menu_onoff button").removeClass("on");

					setTimeout(function(){
						tuto_position();
						$(".tuto_mark_01_0"+next_idx).show();
						$(".tuto_pop_01_0"+next_idx).show();
					},1100);
				}else{
					$(".tuto_mark_01_0"+next_idx).show();
					$(".tuto_pop_01_0"+next_idx).show();	
				}
			}else if(level == 'part_v'){
				if(idx == 2){
					$(".rew_box").removeClass("on");
					$(".rew_menu_onoff button").removeClass("on");

					setTimeout(function(){
						tuto_position();
						$(".tuto_mark_01_0"+next_idx).show();
						$(".tuto_pop_01_0"+next_idx).show();
					},1100);
				}else{
					$(".tuto_mark_01_0"+next_idx).show();
					$(".tuto_pop_01_0"+next_idx).show();	
				}
			}else{
				$(".tuto_mark_01_0"+next_idx).show();
				$(".tuto_pop_01_0"+next_idx).show();
			}
		}

		function cli_prev(idx,level){
			var next_idx = idx - 1;
			$(".tuto_mark_01_0"+idx).hide();
			$(".tuto_pop_01_0"+idx).hide();

			if(level == 'party'){
				if(idx == 2){
					$(".rew_box").addClass("on");
					$(".rew_menu_onoff button").addClass("on");

					setTimeout(function(){
						tuto_position();
						$(".tuto_mark_01_0"+next_idx).show();
						$(".tuto_pop_01_0"+next_idx).show();
					},1100);
				}else{
					$(".tuto_mark_01_0"+next_idx).show();
					$(".tuto_pop_01_0"+next_idx).show();	
				}
			}else if(level == 'part_v'){
				if(idx == 3){
					$(".rew_box").addClass("on");
					$(".rew_menu_onoff button").addClass("on");

					setTimeout(function(){
						tuto_position();
						$(".tuto_mark_01_0"+next_idx).show();
						$(".tuto_pop_01_0"+next_idx).show();
					},1100);
				}else{
					$(".tuto_mark_01_0"+next_idx).show();
					$(".tuto_pop_01_0"+next_idx).show();	
				}
			}else{
				$(".tuto_mark_01_0"+next_idx).show();
				$(".tuto_pop_01_0"+next_idx).show();
			}
		}

		function tuto_position(){
			$(".tuto").each(function(i){
				var i = i+1;
				var tuto = $(this);
				var tt_l = tuto.offset().left;
				var tt_t = tuto.offset().top;
				var tt_w = tuto.width() / 2;
				var tt_h = tuto.height() / 2;
				var tt_x = tt_l + tt_w;
				var tt_y = tt_t + tt_h;
				var win_w = $(window).width();
				var win_h = $(window).height();
				var win_h2 = $(window).height() / 2;
				var tt_r = win_w - 400;
				var tt_p = $(".tuto_pop_01_0"+i+"").height();
				var tt_ph = tt_p + tt_y;
				if(tt_x > tt_r){
					$(".tuto_pop_01_0"+i+"").css({
						left:"auto",
						right:70,
						opacity:1
					});
					$(".tuto_pop_01_0"+i+"").removeClass("tuto_l tuto_r").addClass("tuto_r");
				}else{
					$(".tuto_pop_01_0"+i+"").css({
						left:(tt_x-47),
						opacity:1
					});
					$(".tuto_pop_01_0"+i+"").removeClass("tuto_l tuto_r").addClass("tuto_l");
				}
				if(tt_ph > (win_h - 70)){
					$(".tuto_pop_01_0"+i+"").css({
						top:(tt_t-tt_p-24),
					});
					$(".tuto_pop_01_0"+i+"").removeClass("tuto_t tuto_b").addClass("tuto_t");
				}else{
					$(".tuto_pop_01_0"+i+"").css({
						top:(tt_y+42),
					});
					$(".tuto_pop_01_0"+i+"").removeClass("tuto_t tuto_b").addClass("tuto_b");
				}
				$(".tuto_mark_01_0"+i+"").css({
					left:tt_x,
					top:tt_y,
					opacity:1
				});
			});
		}


		function tu_end(che_le){
			var fdata = new FormData();
			var mode = "update";
			var url = '/inc/tu_process.php';
			var coin = 100;

			if(che_le == 'p_end'){
				var level = "party";
			}else if(che_le == 'c_end'){
				var level = "challenges";
			}else if(che_le == 'm_end'){
				var level = "main";
				coin = 500;
			}

			fdata.append("mode", mode);
			fdata.append("level", level);
			fdata.append("coin", coin);
			
			$.ajax({
				type: "POST",
				data: fdata,
				contentType: false,
				processData: false,
				url: url,
				success: function(data){

					console.log(data);
					if(data == "complete"){
						$(".tuto_phase").css("display","block");
					}
				}

			});

		}

		function page_loc(sub_level){
			if(sub_level == 'party_v'){
				location.href = "/project/tu_pro_view.php";
			}else if(sub_level == 'party_pre'){
				location.href = "/project/tu_project.php";
			}else if(sub_level == 'party_end'){
				location.href = "/challenges/tu_chall.php";
			}else if(sub_level == 'challenge_v'){
				location.href = "/challenges/tu_chal_view.php?idx=1";
			}else if(sub_level == 'chal_prev'){
				location.href = "/challenges/tu_chall.php"
			}else if(sub_level == 'chal_end'){
				location.href = "/team/tu_team.php";
			}
		}

		function save_end(){
			location.href = "/team/index.php";
		}
	</script>

<div class="tuto_phase" style="display:none;">
		<div class="tuto_phase_deam"></div>
		<div class="tuto_phase_in">
			<div class="tuto_phase_tit">
				<strong>튜토리얼로 보상받기</strong>
				<span>단계별 튜토리얼을 진행하고 코인으로 보상 받아가세요!</span>
			</div>
			<div class="tuto_phase_list">
				<div class="tuto_phase_box phase_01 tuto_clear">
					<p>1</p>
					<button>
						<dl>
							<dt>오늘업무</dt>
							<dd>
								<span>역량</span>
								<strong>1</strong>
							</dd>
							<dd>
								<span>코인</span>
								<strong>100</strong>
							</dd>
						</dl>
						<em>도전하기</em>
					</button>
				</div>
				<div class="tuto_phase_box phase_02 tuto_clear">
					<p>2</p>
					<button>
						<dl>
							<dt>좋아요</dt>
							<dd>
								<span>역량</span>
								<strong>1</strong>
							</dd>
							<dd>
								<span>코인</span>
								<strong>100</strong>
							</dd>
						</dl>
						<em>도전하기</em>
					</button>
				</div>
				<div class="tuto_phase_box phase_03 tuto_clear">
					<p>3</p>
					<button>
						<dl>
							<dt>코인 보상</dt>
							<dd>
								<span>좋아요</span>
								<strong>1</strong>
							</dd>
							<dd>
								<span>코인</span>
								<strong>100</strong>
							</dd>
						</dl>
						<em>도전하기</em>
					</button>
				</div>
				<div class="tuto_phase_box phase_06">
					<p>6</p>
					<button>
						<dl>
							<dt>메인</dt>
							<dd>
								<span>좋아요</span>
								<strong>1</strong>
							</dd>
							<dd>
								<span>코인</span>
								<strong>100</strong>
							</dd>
						</dl>
						<em>도전하기</em>
					</button>
				</div>
				<div class="tuto_phase_box phase_05 tuto_on">
					<p>5</p>
					<button onclick="page_loc('party_end');">
						<dl>
							<dt>챌린지 도전</dt>
							<dd>
								<span>역량</span>
								<strong>1</strong>
							</dd>
							<dd>
								<span>코인</span>
								<strong>100</strong>
							</dd>
						</dl>
						<em>도전하기</em>
					</button>
				</div>
				<div class="tuto_phase_box phase_04 tuto_clear">
					<p>4</p>
					<button>
						<dl>
							<dt>파티 체험</dt>
							<dd>
								<span>역량</span>
								<strong>1</strong>
							</dd>
							<dd>
								<span>코인</span>
								<strong>100</strong>
							</dd>
						</dl>
						<em>도전하기</em>
					</button>
				</div>
			</div>
			<div class="tuto_phase_pause">
				<button onclick="save_end();">다음에 이어하기</button>
			</div>
		</div>
	</div>
<div class="rew_tutorial_deam"></div>
	<div class="tuto_mark tuto_mark_01_01"><button><span></span></button></div>
	<div class="tuto_mark tuto_mark_01_02" style="display:none;"><button><span></span></button></div>
	<div class="tuto_mark tuto_mark_01_03" style="display:none;"><button><span></span></button></div>
	<div class="tuto_mark tuto_mark_01_04" style="display:none;"><button><span></span></button></div>
	<div class="tuto_pop tuto_pop_01_01">
		<div class="tuto_in">
			<div class="tuto_tit">파티에 대해 알아보기</div>
			<div class="tuto_pager">1/3</div>
			<div class="tuto_desc">
				<p>해당 파티에 코인을 보상할 수 있어요.</p>
				<p>누적된 코인은 파티 종료 시, 파티 구성원에게 똑같이 배분돼요.</p>
				<p>공용코인이 있는 누구나 코인을 보낼 수 있어요.</p>
			</div>
			<div class="tuto_btns">
				<button class="tuto_prev" onclick="page_loc('party_pre');"><span>이전</span></button>
				<button class="tuto_next" onclick="cli_next(1);"><span>다음</span></button>
			</div>
		</div>
	</div>
	<div class="tuto_pop tuto_pop_01_02" style="display:none;">
		<div class="tuto_in">
			<div class="tuto_tit">파티에 대해 알아보기</div>
			<div class="tuto_pager">2/3</div>
			<div class="tuto_desc">
				<p>파티에 참여한 구성원을 모두 볼 수 있는 영역이에요.</p>
				<p>파티 구성원 개개인에게 보상하거나 좋아요를 보낼 수 있어요.</p>
			</div>
			<div class="tuto_btns">
				<button class="tuto_prev" onclick="cli_prev(2);"><span>이전</span></button>
				<button class="tuto_next" onclick="cli_next(2,'part_v');"><span>다음</span></button>
			</div>
		</div>
	</div>
	<div class="tuto_pop tuto_pop_01_03" style="display:none;">
		<div class="tuto_in">
			<div class="tuto_tit">파티에 대해 알아보기</div>
			<div class="tuto_pager">3/3</div>
			<div class="tuto_desc">
				<p>파티에 연결된 업무를 한 눈에 볼 수 있어요.</p>
				<p>오늘업무에 업무를 작성하고 '파티연결' 을 통해 파티에 연결해 보세요.</p>
			</div>
			<div class="tuto_btns">
				<button class="tuto_prev" onclick="cli_prev(3,'part_v');"><span>이전</span></button>
				<button class="tuto_next" onclick="tu_end('p_end');"><span>확인</span></button>
			</div>
		</div>
	</div>
<div class="rew_warp">
	<div class="rew_warp_in">
		<div class="rew_box">
			<div class="rew_box_in">
				<!-- menu -->
				<div class="rew_menu">
					<div class="rew_menu_in">
						<div class="rew_bar">
							<span class="rew_bar_alert" style="display: none;"><em>멤버를 먼저 초대하세요.</em></span>
							<div class="rew_bar_in">
								<div class="rew_bar_logo">
									<a href="javascript:void(0);"><img src="/images/pre/logo.png" alt=""></a>
								</div>
								<ul>
									<li class="rew_bar_li_01" title="">
										<a href="javascript:void(0);"><strong>오늘업무</strong></a>
									</li>
									<li class="rew_bar_li_02" title="">
										<a href="javascript:void(0);"><strong>실시간 업무</strong></a>
									</li>
									<li class="rew_bar_li_03" title="">
										<a href="javascript:void(0);"><strong>보상/코인</strong></a>
									</li>
									<li class="rew_bar_li_04" title="">
										<a href="javascript:void(0);"><strong>챌린지</strong></a>
									</li>
									<li class="rew_bar_li_05 on" title="">
										<a href="javascript:void(0);"><strong>파티</strong></a>
									</li>
									<li class="rew_bar_li_06" title="">
										<a href="javascript:void(0);"><strong>인사이트</strong></a>
									</li>
									
								</ul>
								<div class="rew_bar_setting">

																			<a href="/todaywork/tu_works.php" class="rew_bar_setting_03" title="" id="tutorial"><strong>튜토리얼</strong></a>
									
																			<a href="/admin/member_list.php" class="rew_bar_setting_02" id="member_add_in" title=""><strong>관리자</strong></a>
									
								</div>
							</div>
						</div>

						<div class="rew_mypage_10_view">
							<div class="rew_mypage_10_view_in">
								<div class="rew_mypage_title">
									<a href="./0010.html"><strong>비즈폼 매출분석 및 특이사항 공유</strong></a>
								</div>
								<div class="rew_mypage_party">
									<div class="party_accrue_coin">
										<span>파티 누적 코인</span>
										<strong>1,000,000</strong>
										<button class="btn_admin_coin_pop on tuto tuto_01_01">코인 보내기</button>
									</div>
									<!-- <div class="rew_mypage_coin_pop">
										<button class="btn_admin_coin_pop"><span>코인으로 응원하기</span></button>
									</div> -->
									<div class="party_tabs">
										<div class="party_tabs_in">
											<ul>
												<li>
													<dl>
														<dt>D+35</dt>
														<dd>경과일</dd>
													</dl>
												</li>
												<li>
													<dl>
														<dt>4</dt>
														<dd>업데이트</dd>
													</dl>
												</li>
												<li>
													<dl>
														<dt>42</dt>
														<dd>좋아요</dd>
													</dl>
												</li>
												<li>
													<dl>
														<dt>192</dt>
														<dd>전체업무</dd>
													</dl>
												</li>
											</ul>
										</div>
									</div>
									<!-- 
									<div class="rew_party_tab_v">
										<div class="rew_party_tab_in">
											<ul>
												<li>
													<button><span>경과일</span><strong>D+35</strong></button>
												</li>
												<li>
													<button><span>업데이트</span><strong>4</strong></button>
												</li>
												<li>
													<button><span>좋아요</span><strong>42</strong></button>
												</li>
												<li>
													<button><span>전체업무</span><strong>192</strong></button>
												</li>
											</ul>
										</div>
									</div> 
									-->
									<div class="party_list_title">
										<span>파티 구성원<strong>8</strong></span>
									</div>
									<div class="party_user_list">
										<div class="party_user_list_in">
											<div class="pu_list_header">
												<div class="pu_list_header_in">
													<div class="pu_list_header_name">
														<button class="btn_pu_reset"><span>초기화</span></button>
														<strong>전체</strong>
														<em>
															<button class="btn_sort_up"></button>
															<button class="btn_sort_down"></button>
														</em>
													</div>
													<div class="pu_list_header_count">
														<strong>업무 수</strong>
														<em>
															<button class="btn_sort_up"></button>
															<button class="btn_sort_down"></button>
														</em>
													</div>
													<div class="pu_list_header_heart">
														<strong>좋아요</strong>
														<em>
															<button class="btn_sort_up"></button>
															<button class="btn_sort_down"></button>
														</em>
													</div>
												</div>
											</div>

											<div class="pu_list_conts">
												<div class="pu_list_conts_in">
													<ul>
														<li>
															<div class="pu_list_conts_name party_leader party_new">
																<div class="pu_list_conts_name_in">
																	<div class="user_img" style="background-image:url('/html/images/pre/img_prof_01.png');"></div>
																	<div class="user_name">
																		<strong>이선규</strong>
																		<span>대표팀</span>
																		<em>N</em>
																	</div>
																</div>
															</div>
															<div class="pu_list_conts_count">
																<span>42/42</span>
															</div>
															<div class="pu_list_conts_heart">
																<button class="btn_pu_coin on"><span>코인</span></button>
																<button class="btn_pu_heart on tuto tuto_01_02"><span>좋아요</span></button>
															</div>
														</li>
														<li class="on">
															<div class="pu_list_conts_name party_new">
																<div class="pu_list_conts_name_in">
																	<div class="user_img" style="background-image:url('/html/images/pre/img_prof_02.png');"></div>
																	<div class="user_name">
																		<strong>리워디</strong>
																		<span>리워디팀</span>
																		<em>N</em>
																	</div>
																</div>
															</div>
															<div class="pu_list_conts_count">
																<span><strong>38</strong>/39</span>
															</div>
															<div class="pu_list_conts_heart">
																<button class="btn_pu_coin"><span>코인</span></button>
																<button class="btn_pu_heart"><span>좋아요</span></button>
															</div>
														</li>
														<li>
															<div class="pu_list_conts_name party_new">
																<div class="pu_list_conts_name_in">
																	<div class="user_img" style="background-image:url('/html/images/pre/img_prof_03.png');"></div>
																	<div class="user_name">
																		<strong>튜토리얼</strong>
																		<span>튜토리얼팀</span>
																		<em>N</em>
																	</div>
																</div>
															</div>
															<div class="pu_list_conts_count">
																<span>15/15</span>
															</div>
															<div class="pu_list_conts_heart">
																<button class="btn_pu_coin"><span>코인</span></button>
																<button class="btn_pu_heart on"><span>좋아요</span></button>
															</div>
														</li>
													</ul>
												</div>
											</div>
										</div>
									</div>
								</div>
							</div>

							<div class="rew_mypage_party_btns">
								<!-- <button class="btn_mypage_party_in"><span>파티 참여하기</span></button>
								<button class="btn_mypage_party_out"><span>파티에서 나가기</span></button> -->
								<button class="btn_mypage_party_admin"><span>파티관리</span></button>
								<button class="btn_mypage_party_end"><span>파티종료</span></button>
							</div>
						</div>

					</div>
					<div class="rew_menu_onoff">
						<button class="">열고 닫기</button>
					</div>
				</div>
				<!-- //menu -->

				<!-- 콘텐츠 -->
				<div class="rew_conts">
					<div class="rew_conts_in">
						<!-- <div class="rew_header">
							<div class="rew_header_in">
								<div class="rew_header_notice">
									<span></span>
								</div>
							</div>
						</div> -->

						<div class="rew_conts_scroll_10v">

							<div class="rew_todaywork rew_party_wrap">
								<div class="rew_todaywork_in">

									<!-- <div class="rew_admin_coin_pop">
										<dl>
											<dt>지금까지 총 <strong>100,000</strong> <span>코인</span>을 보상받았습니다.</dt>
											<dt>코인 보상으로 파티를 응원해주세요!</dt>
											<dd><button class="btn_admin_coin_pop"><span>코인으로 응원하기</span></button></dd>
										</dl>
									</div> -->
									<div class="rew_member_sub_func_tab">
										<div class="rew_member_sub_func_tab_in">
											<div class="rew_member_sub_func_sort" id="rew_member_sub_func_sort">
												<div class="rew_member_sub_func_sort_in">
													<button class="btn_sort_on" id="btn_sort_on"><span>전체보기</span></button>
													<ul>
														<li><button value="all"><span>전체보기</span></button></li>
														<li><button value="todaywork"><span>오늘업무</span></button></li>
														<li><button value="report"><span>보고</span></button></li>
														<li><button value="share"><span>공유</span></button></li>
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
														<button class="btn_calendar_l" id="btn_calendar_l">달력</button>
														<input type="text" class="input_date_l" value="<?=$week7?>" id="project_sdate">
														<span>~</span>
														<input type="text" class="input_date_r" value="<?=TODATE?>" id="project_edate">
													</div>
													<div class="rew_cha_search_box">
														<input type="text" class="input_search" id="party_input_search" placeholder="키워드">
														<button id="btn_input_search"><span>검색</span></button>
													</div>
													<button class="btn_inquiry" id="btn_party_search"><span>조회</span></button>
													<input type="hidden" id="party_idx" value="<?=$party_idx?>">
												</div>
											</div>
										</div>
									</div>
									
									<div class="tdw_list">
										<div class="tdw_list_in">
											<div class="tdw_list_ww">
												<div class="tdw_list_ww_box">
													<strong class="tdw_list_title_date">11.08 (월)</strong>
													<ul class="tdw_list_ul">
														<li class="tdw_list_li coin_on">
															<div class="tdw_list_box">
																<div class="tdw_list_chk">
																	<button class="btn_tdw_list_chk"><span>완료체크</span></button>
																</div>
																<div class="tdw_list_desc">
																	<p><span>[이선규 → 파티 코인 100,000 코인 보상]</span>아자 아자! 모두 힘내세요!</p>
																</div>
															</div>
														</li>

														<li class="tdw_list_li">
															<div class="tdw_list_box">
																<div class="tdw_list_chk">
																	<button class="btn_tdw_list_chk"><span>완료체크</span></button>
																</div>
																<div class="tdw_list_desc">
																	<p><span>[15:00]</span>엑셀 뷰페이지 추천서식 영역 디자인 교체</p>
																</div>
																<div class="tdw_list_function">
																	<div class="tdw_list_function_in">
																		<button class="tdw_list_party_date"><span>14:55</span></button>
																		<button class="tdw_list_party_name"><span>튜토리얼</span></button>
																		<button class="tdw_list_party_memo"><span>메모</span></button>
																		<button class="tdw_list_party_link on tuto tuto_01_03"><span>파티연결</span></button>
																		<button class="tdw_list_party_heart off"><span>좋아요</span></button>
																	</div>
																</div>
															</div>
														</li>

														<li class="tdw_list_li no_read">
															<div class="tdw_list_box">
																<div class="tdw_list_chk">
																	<button class="btn_tdw_list_chk"><span>완료체크</span></button>
																</div>
																<div class="tdw_list_desc">
																	<p>비즈폼 리워드 뷰페이지 시안 확인 및 수정사항 체크, 전달 및 커뮤니케이션</p>
																</div>
																<div class="tdw_list_function">
																	<div class="tdw_list_function_in">
																		<button class="tdw_list_party_date"><span>14:55</span></button>
																		<button class="tdw_list_party_name"><span>리워디</span></button>
																		<button class="tdw_list_party_memo"><span>메모</span></button>
																		<button class="tdw_list_party_coin"><span>100코인</span></button>
																		<button class="tdw_list_party_heart"><span>좋아요</span></button>
																	</div>
																</div>
															</div>
														</li>

														<li class="tdw_list_li">
															<div class="tdw_list_box on">
																<div class="tdw_list_chk">
																	<button class="btn_tdw_list_chk"><span>완료체크</span></button>
																</div>
																<div class="tdw_list_desc">
																	<p><span>[2021-01-01 14:00 반차]</span>오후 반차</p>
																</div>
																<div class="tdw_list_function">
																	<div class="tdw_list_function_in">
																		<button class="tdw_list_party_date"><span>14:55</span></button>
																		<button class="tdw_list_party_name"><span>리워디</span></button>
																		<button class="tdw_list_party_memo"><span>메모</span></button>
																		<button class="tdw_list_party_coin"><span>100코인</span></button>
																		<button class="tdw_list_party_heart"><span>좋아요</span></button>
																	</div>
																</div>
															</div>
														</li>

														<li class="tdw_list_li">
															<div class="tdw_list_box">
																<div class="tdw_list_chk">
																	<button class="btn_tdw_list_chk"><span>완료체크</span></button>
																</div>
																<div class="tdw_list_desc">
																	<p><span>[리워디 → 튜토리얼 업무요청]</span>월결제 관련 통계 자료 추출</p>
																</div>
																<div class="tdw_list_function">
																	<div class="tdw_list_function_in">
																		<button class="tdw_list_party_date"><span>14:55</span></button>
																		<button class="tdw_list_party_name"><span>리워디</span></button>
																		<button class="tdw_list_party_memo"><span>메모</span></button>
																		<button class="tdw_list_party_coin"><span>100코인</span></button>
																		<button class="tdw_list_party_heart"><span>좋아요</span></button>
																	</div>
																</div>
															</div>
														</li>
													</ul>
												</div>

												<div class="tdw_list_ww_box">
													<strong class="tdw_list_title_date">11.09 (화)</strong>
													<ul class="tdw_list_ul">
														<li class="tdw_list_li">
															<div class="tdw_list_box">
																<div class="tdw_list_chk">
																	<button class="btn_tdw_list_chk"><span>완료체크</span></button>
																</div>
																<div class="tdw_list_desc">
																	<p><span>[15:00]</span>엑셀 뷰페이지 추천서식 영역 디자인 교체</p>
																</div>
																<div class="tdw_list_function">
																	<div class="tdw_list_function_in">
																		<button class="tdw_list_party_date"><span>14:55</span></button>
																		<button class="tdw_list_party_name"><span>리워디</span></button>
																		<button class="tdw_list_party_memo"><span>메모</span></button>
																		<button class="tdw_list_party_coin"><span>100코인</span></button>
																		<button class="tdw_list_party_heart"><span>좋아요</span></button>
																	</div>
																</div>
															</div>
														</li>
													</ul>
												</div>
											</div>

										</div>
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


	</div>
</div>
<!-- footer start-->
<? include $home_dir . "/inc_lude/footer.php";?>
<!-- footer end-->

</body>


</html>

