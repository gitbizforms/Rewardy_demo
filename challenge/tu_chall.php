<?
	//header페이지
	$home_dir = str_replace( basename(__DIR__) , "" , __DIR__ );
	
	include $home_dir . "/inc_lude/header.php";
	// include $home_dir . "/challenge/challenges_header_main.php";

	$tuto = tutorial_chk();
	// if($tuto['t_flag']>4){
	// 	alert('해당 단계는 이미 완료하셨습니다!');
	// 	echo "<script>history.back();</script>";
	// }else
	 if($tuto['t_flag']<4){
		alert('이전 단계를 먼저 수행해주세요.');
		echo "<script>history.back();</script>";
	}
?>
<head>
	<script src="/js/tutorial_common.js<?php echo VER;?>"></script>
</head>
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

			$(".rew_box").removeClass("on");
			$(".rew_menu_onoff button").removeClass("on");
			setTimeout(function(){
				tuto_position();
				$(".tuto_mark_01_01").show();
				$(".tuto_pop_01_01").show();
			},1100);

		});

		function cli_next(idx){
			var next_idx = idx + 1;
			$(".tuto_mark_01_0"+idx).hide();
			$(".tuto_pop_01_0"+idx).hide();
			$(".tuto_mark_01_0"+next_idx).show();
			$(".tuto_pop_01_0"+next_idx).show();
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

		
		function page_loc(sub_level){
			if(sub_level == 'party_v'){
				location.href = "/party/tu_pro_view.php";
			}else if(sub_level == 'party_pre'){
				location.href = "/party/tu_project.php";
			}else if(sub_level == 'party_end'){
				location.href = "/challenge/tu_chall.php";
			}else if(sub_level == 'challenge_v'){
				location.href = "/challenge/tu_chal_view.php?idx=1";
			}else if(sub_level == 'chal_prev'){
				location.href = "/challenge/tu_chall.php"
			}else if(sub_level == 'chal_end'){
				location.href = "/team/tu_team.php";
			}
		}
	</script>
	<div class="rew_tutorial_deam"></div>
	<div class="tuto_mark tuto_mark_01_01"><button><span></span></button></div>
	<div class="tuto_pop tuto_pop_01_01">
		<div class="tuto_in">
			<div class="tuto_tit">챌린지</div>
			<div class="tuto_pager">1/1</div>
			<div class="tuto_desc">
				<p>활성화되어 있는 챌린지에 도전할 수 있어요.</p>
				<p>보상 코인, 도전한 인원, 남은 기간 등을 한눈에 볼 수 있어요.</p>
			</div>
			<div class="tuto_btns">
				<!-- <button class="tuto_prev"><span>이전</span></button> -->
				<button class="tuto_next" onclick="page_loc('challenge_v')"><span>확인</span></button>
			</div>
		</div>
	</div>
<div class="rew_warp">
	<div class="rew_warp_in">
		<div class="rew_box">
			<div class="rew_box_in">
				<? include $home_dir . "/inc_lude/header_new.php";?>
				<!-- //상단 -->
				<? include $home_dir . "/inc_lude/menu.php";?>
				<!-- //menu -->
				<div class="rew_menu">
					<div class="rew_menu_in">
						<div class="rew_bar">
							<span class="rew_bar_alert"><em>멤버를 먼저 초대하세요.</em></span>
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
									<li class="rew_bar_li_04 on" title="">
										<a href="javascript:void(0);"><strong>챌린지</strong></a>
									</li>
									<li class="rew_bar_li_05" title="">
										<a href="javascript:void(0);"><strong>파티</strong></a>
									</li>
									<li class="rew_bar_li_06" title="">
										<a href="javascript:void(0);"><strong>인사이트</strong></a>
									</li>
									
								</ul>
								<div class="rew_bar_setting">
									<a href="/todaywork/tu_works.php" class="rew_bar_setting_03" title="" id="tutorial"><strong>튜토리얼</strong></a>
								</div>
							</div>
						</div>

						<div class="rew_mypage_04">
							<div class="rew_mypage_04_in">
								<div class="rew_mypage_title">
									<strong><a href="./0004.html">챌린지</a></strong>
								</div>

								<div class="rew_mypage_tab_04">
									<ul>
										<li class="tab_chall_01">
											<a href="./0005.html"><span>챌린지 만들기</span></a>
										</li>
										<li class="tab_chall_02">
											<a href="./0004t.html"><span>챌린지 템플릿</span></a>
										</li>
										<li class="tab_chall_03">
											<a href="./0004.html"><span>내가 만든 챌린지</span></a>
										</li>
										<li class="tab_chall_04">
											<a href="./0004.html"><span>임시저장 챌린지</span></a>
										</li>
									</ul>
								</div>

								<div class="rew_mypage_section">
									<div class="rew_mypage_section_title">
										<strong><span>🏆</span> 코인 현황</strong>
									</div>
									<div class="rew_mypage_coin_now">
										<ul>
											<li>
												<span>내 코인</span>
												<strong>120,000</strong>
											</li>
											<li>
												<span>획득 가능한 코인</span>
												<strong>0</strong>
											</li>
										</ul>
									</div>
								</div>

								<div class="rew_mypage_section">
									<div class="rew_mypage_section_title">
										<strong><span>📢</span> 마감임박 챌린지</strong>
									</div>
									<div class="rew_mypage_chall_ing">
										<ul>
											<li>
												<a href="./0006.html">
													<span class="chall_ing_title">[신입사원] 보고서 작성법을 배우면</span>
													<span class="chall_ing_coin"><strong>1,000</strong>코인</span>
													<span class="chall_ing_dday"><strong>D - 1</strong></span>
												</a>
											</li>
											<li>
												<a href="./0006.html">
													<span class="chall_ing_title">[생활] 캔크러시 챌린지, 그저 밟기만 했을 뿐인데</span>
													<span class="chall_ing_coin"><strong>500</strong>코인</span>
													<span class="chall_ing_dday"><strong>D - 2</strong></span>
												</a>
											</li>
											<li>
												<a href="./0006.html">
													<span class="chall_ing_title">[생활] 책 읽고 독서메모를 남긴다면</span>
													<span class="chall_ing_coin"><strong>1,500</strong>코인</span>
													<span class="chall_ing_dday"><strong>D - 2</strong></span>
												</a>
											</li>
										</ul>
									</div>
								</div>
							</div>
						</div>

					</div>
					<div class="rew_menu_onoff">
						<button class="on">열고 닫기</button>
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

						<!-- <div class="rew_cha_tab">
							<div class="rew_cha_tab_in">
								<ul>
									<li class="on"><button><span>전체</span></button></li>
									<li><button><span>업무</span></button></li>
									<li><button><span>생활</span></button></li>
									<li><button><span>교육</span></button></li>
									<li><button><span>문화</span></button></li>
									<li><button><span>신입사원</span></button></li>
									<li><button><span>기타</span></button></li>
								</ul>
							</div>
						</div> -->
						<div class="rew_cha_list_func">
							<div class="rew_cha_list_func_in">
								<div class="rew_cha_count">
									<span>전체</span>
									<strong>182</strong>
								</div>
								<div class="rew_cha_tab_sort" style="right:406px">
									<div class="rew_cha_tab_sort_in">
										<button class="btn_sort_on"><span>전체</span></button>
										<ul>
											<li><button><span>전체</span></button></li>
											<li><button><span>업무</span></button></li>
											<li><button><span>생활</span></button></li>
											<li><button><span>교육</span></button></li>
											<li><button><span>문화</span></button></li>
											<li><button><span>신입사원</span></button></li>
											<li><button><span>기타</span></button></li>
										</ul>
									</div>
								</div>
								<div class="rew_cha_sort" style="right:240px">
									<div class="rew_cha_sort_in">
										<button class="btn_sort_on"><span>최신 순</span></button>
										<ul>
											<li><button><span>최신 순</span></button></li>
											<li><button><span>참여자 많은 순</span></button></li>
											<li><button><span>코인 높은 순</span></button></li>
											<li><button><span>기간 짧은 순</span></button></li>
										</ul>
									</div>
								</div>
								<div class="rew_cha_search" style="right:10px">
									<div class="rew_cha_search_box">
										<input type="text" class="input_search" placeholder="이름, 부서명을 검색" />
										<button><span>검색</span></button>
									</div>
								</div>
								<div class="rew_cha_chk_tab">
									<ul>
										<li>
											<div class="chk_tab">
												<input type="checkbox" name="cha_chk_tab" id="cha_chk_tab_all" checked />
												<label for="cha_chk_tab_all">전체</label>
											</div>
										</li>
										<li>
											<div class="chk_tab">
												<input type="checkbox" name="cha_chk_tab" id="cha_chk_tab_wait">
												<label for="cha_chk_tab_wait">도전가능한 챌린지</label>
											</div>
										</li>
										<li>
											<div class="chk_tab">
												<input type="checkbox" name="cha_chk_tab" id="cha_chk_tab_ing">
												<label for="cha_chk_tab_ing">도전중인 챌린지</label>
											</div>
										</li>
										<li>
											<div class="chk_tab">
												<input type="checkbox" name="cha_chk_tab" id="cha_chk_tab_comp">
												<label for="cha_chk_tab_comp">내가 완료한 챌린지</label>
											</div>
										</li>
										<li>
											<div class="chk_tab">
												<input type="checkbox" name="cha_chk_tab" id="cha_chk_tab_end">
												<label for="cha_chk_tab_end">종료된 챌린지</label>
											</div>
										</li>
									</ul>
								</div>
							</div>
						</div>
						<div class="rew_conts_scroll_04">
							<div class="rew_cha_list">
								<div class="rew_cha_list_in">
									<ul class="rew_cha_list_ul">
										<li class="category_01">
											<a href="./0006.html">
												<div class="cha_box">
													<div class="cha_box_m">
														<div class="cha_info">
															<span class="cha_cate">보안</span>
														</div>
														<span class="cha_coin"><strong>500</strong>코인</span>
													</div>
													<div class="cha_box_t">
														<span class="cha_title on tuto tuto_01_01">해킹 방지의 첫 걸음! 윈도우 업데이트인걸 아시나요?</span>
													</div>
													<div class="cha_box_b">
														<span class="cha_member"><strong>93</strong>/120명 도전</span>
														<span class="cha_dday">D - 3</span>
													</div>
												</div>
											</a>
										</li>
										<li class="category_01">
											<a href="./0006.html">
												<div class="cha_box">
													<div class="cha_box_m">
														<div class="cha_info">
															<span class="cha_cate">업무</span>
														</div>
														<span class="cha_coin"><strong>500</strong>코인</span>
													</div>
													<div class="cha_box_t">
														<span class="cha_title">튜토리얼 챌린지</span>
													</div>
													<div class="cha_box_b">
														<span class="cha_member"><strong>74</strong>/120명 도전</span>
														<span class="cha_dday">D - 31</span>
													</div>
												</div>
											</a>
										</li>
										<li class="category_01">
											<a href="./0006.html">
												<div class="cha_box">
													<div class="cha_box_m">
														<div class="cha_info">
															<span class="cha_cate">생활</span>
														</div>
														<span class="cha_coin"><strong>1,500</strong>코인</span>
													</div>
													<div class="cha_box_t">
														<span class="cha_title">하루 10분! 이 자세면 생활이 바뀐다</span>
													</div>
													<div class="cha_box_b">
														<span class="cha_member"><strong>85</strong>/120명 도전</span>
														<span class="cha_dday">D - 7</span>
													</div>
												</div>
											</a>
										</li>
										<li class="category_01">
											<a href="./0006.html">
												<div class="cha_box">
													<div class="cha_box_m">
														<div class="cha_info">
															<span class="cha_cate">공유</span>
														</div>
														<span class="cha_coin"><strong>50</strong>코인</span>
													</div>
													<div class="cha_box_t">
														<span class="cha_title">맛잘알 여러분! 맛집 추천 해주세요</span>
													</div>
													<div class="cha_box_b">
														<span class="cha_member"><strong>13</strong>/20명 도전</span>
														<span class="cha_dday">D - 2</span>
													</div>
												</div>
											</a>
										</li>
										<li class="cha_dend category_01">
											<a href="./0006.html">
												<div class="cha_box">
													<div class="cha_box_m">
														<div class="cha_info">
															<span class="cha_cate">반차</span>
														</div>
														<span class="cha_coin"><strong>10</strong>코인</span>
													</div>
													<div class="cha_box_t">
														<span class="cha_title">연휴에 반차쓰기? 어렵지 않아요</span>
													</div>
													<div class="cha_box_b">
														<span class="cha_member"><strong>40</strong>/40명 도전</span>
														<span class="cha_dday">종료</span>
													</div>
												</div>
											</a>
										</li>
										<li class="cha_dend category_01">
											<a href="./0006.html">
												<div class="cha_box">
													<div class="cha_box_m">
														<div class="cha_info">
															<span class="cha_cate">업무</span>
														</div>
														<span class="cha_coin"><strong>10</strong>코인</span>
													</div>
													<div class="cha_box_t">
														<span class="cha_title">[신입사원] 보고서에도 형식이 있다는 사실 알고 있나요?</span>
													</div>
													<div class="cha_box_b">
														<span class="cha_member"><strong>10</strong>/10명 도전</span>
														<span class="cha_dday">종료</span>
													</div>
												</div>
											</a>
										</li>
									</ul>
									<div class="rew_cha_more">
										<button><span>more</span></button>
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
	</script>
</div>

<!-- footer start-->
<? include $home_dir . "/inc_lude/footer.php";?>
<!-- footer end-->

</body>


</html>
