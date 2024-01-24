<?
	//header페이지
	$home_dir = str_replace( basename(__DIR__) , "" , __DIR__ );
	include $home_dir . "/inc_lude/header.php";

	$tuto = tutorial_chk();
	if($tuto['t_flag']>3){
		alert('해당 단계는 이미 완료하셨습니다!');
		echo "<script>history.back();</script>";
	}else if($tuto['t_flag']<3){
		alert('이전 단계를 먼저 수행해주세요.');
		echo "<script>history.back();</script>";
	}

	$month = date("m");
	$month = (int)$month;

	$day = date("d");
	$day = (int)$day;

	$rnd_d = rand(0,$day);
	$rnd_m = rand(0,$month);

	$today_i = date("Y-m-d H:i");
	$today_r_imd = date("Y-m-d H:i", strtotime("-".$rnd_m." month -".$rnd_m."days -750 minutes"));
	$today_r_im = date("Y-m-d H:i", strtotime("-".$rnd_m." month -750 minutes"));
	$today_r_id = date("Y-m-d H:i", strtotime("-".$rnd_d." days"));
	$today_d = date("Y-m-d");
	$today_r_dmd = date("Y-m-d", strtotime("-".$rnd_d." month -".$rnd_m."days"));
	$today_r_dm = date("Y-m-d", strtotime("-".$rnd_m." month"));
	$today_r_dd = date("Y-m-d", strtotime("-".$rnd_d." days"));
	$today_m = date("Y-m-d",strtotime("+1 months"));
?>
<script type="text/javascript">
	$(document).ready(function(){

		$(".rew_mypage_10").click(function (e) {
			if (!$(e.target).is(".rew_mypage_10 *")) {
				$(".rew_box").removeClass("on");
				$(".rew_menu_onoff button").removeClass("on");
			}
		});

		$(".rew_conts_list_in ul").sortable({
			axis: "y",
            opacity: 0.7,
			zIndex: 9999,
			//placeholder:"sort_empty",
			cursor: "move"
		});
		$(".rew_conts_list_in ul").disableSelection();

		$(".rew_conts_list_in ul li button").click(function(){
			$(this).parent("li").toggleClass("on");
		});

		$(".rew_btn_icons_more").click(function(){
			$(".rew_icons").toggle();
		});

		$(".rew_mypage_tab_04 a").click(function(){
			$(".rew_mypage_tab_04 li").removeClass("on");
			$(this).parent("li").addClass("on");
		});


		$(".rew_conts_scroll_04").scroll(function(){
			var rct = $(".rew_cha_list_in").offset().top;
			console.log(rct);
			if(rct<216){
				$(".rew_cha_list_func").addClass("pos_fix");
			}else{
				$(".rew_cha_list_func").removeClass("pos_fix");
			}
		});

		$(".rew_cha_list_ul li").each(function(){
			var tis = $(this);
			var tindex = $(this).index();
			setTimeout(function(){
				tis.addClass("sli");
			},700+tindex*150);
		});

		$(".rew_cha_tab_sort .btn_sort_on").click(function(){
			$(".rew_cha_tab_sort").addClass("on");
		});
		$(".rew_cha_tab_sort").mouseleave(function(){
			$(".rew_cha_tab_sort").removeClass("on");
		});
		$(".rew_cha_tab_sort ul li button").click(function(){
			$(".rew_cha_tab_sort").removeClass("on");
		});

		$(".rew_cha_sort .btn_sort_on").click(function(){
			$(".rew_cha_sort").addClass("on");
		});
		$(".rew_cha_sort").mouseleave(function(){
			$(".rew_cha_sort").removeClass("on");
		});
		$(".rew_cha_sort ul li button").click(function(){
			$(".rew_cha_sort").removeClass("on");
		});

		


	});

	function link() {

		location.href = "/todaywork/index.php";

		//미확인 업무로 이동
		// location.href = "/party/view.php?idx="+page;

	}

	
</script>

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
	<div class="tuto_mark tuto_mark_01_02" style="display:none;"><button><span></span></button></div>
	<div class="tuto_mark tuto_mark_01_03" style="display:none;"><button><span></span></button></div>
	<div class="tuto_mark tuto_mark_01_04" style="display:none;"><button><span></span></button></div>
	<div class="tuto_pop tuto_pop_01_01">
		<div class="tuto_in">
			<div class="tuto_tit">파티에 대해 알아보기</div>
			<div class="tuto_pager">1/3</div>
			<div class="tuto_desc">
				<p>함께 프로젝트를 하는 구성원과 파티를 만들수 있어요.</p>
				<p>파티 만들기를 통해 구성원과 업무를 모아 볼 수 있어요.</p>
			</div>
			<div class="tuto_btns">
				<!-- <button class="tuto_prev"><span>이전</span></button> -->
				<button class="tuto_next" onclick="cli_next(1,'party');"><span>다음</span></button>
			</div>
		</div>
	</div>
	<div class="tuto_pop tuto_pop_01_02" style="display:none;">
		<div class="tuto_in">
			<div class="tuto_tit">파티에 대해 알아보기</div>
			<div class="tuto_pager">2/3</div>
			<div class="tuto_desc">
				<p>파티의 진행 상황을 한눈에 볼 수 있어요.</p>
				<p>7일 이상 파티에 연결되는 업무가 없을 때 지연으로 표시돼요.</p>
			</div>
			<div class="tuto_btns">
				<button class="tuto_prev" onclick="cli_prev(2,'party');"><span>이전</span></button>
				<button class="tuto_next" onclick="cli_next(2);"><span>다음</span></button>
			</div>
		</div>
	</div>
	<div class="tuto_pop tuto_pop_01_03" style="display:none;">
		<div class="tuto_in">
			<div class="tuto_tit">파티에 대해 알아보기</div>
			<div class="tuto_pager">3/3</div>
			<div class="tuto_desc">
				<p>등록된 파티를 클릭하면 상세보기 페이지로 이동해요.</p>
				<p>내가 참여한 파티는 즐겨찾기를 통해 위로 올릴 수 있어요.</p>
			</div>
			<div class="tuto_btns">
				<button class="tuto_prev" onclick="cli_prev(3);"><span>이전</span></button>
				<button class="tuto_next" onclick="page_loc('party_v');"><span>확인</span></button>
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
							<span class="rew_bar_alert" style="display:none;"><em>멤버를 먼저 초대하세요.</em></span>
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
									
																			<a href="/member/member_list.php" class="rew_bar_setting_02" id="member_add_in" title=""><strong>관리자</strong></a>
									
								</div>
							</div>
						</div>

						<div class="rew_mypage_10">
							<div class="rew_mypage_10_in">
								<div class="rew_mypage_title">
									<strong>파티</strong>
								</div>
								<div class="rew_party_section">
									<div class="rew_party_tab">
										<div class="rew_party_tab_in">
											<ul>
												<li>
													<button><span>내 파티</span><strong>4</strong></button>
												</li>
												<li>
													<button><span>원활</span><strong>2</strong></button>
												</li>
												<li>
													<button><span>보통</span><strong>1</strong></button>
												</li>
												<li>
													<button><span>지연</span><strong>1</strong></button>
												</li>
											</ul>
										</div>
									</div>

									<div class="rew_party_un">
										<div class="rpu_in">
											<div class="rpu_func">
												<div class="rpu_func_tit">
													<span>미확인 업무<strong>6</strong></span>
												</div>
											</div>

											<div class="rpu_list">
												<div class="rpu_box delay_1">
													<div class="rpu_box_in">
														<div class="rpu_box_tit">
															<span>(제휴)싸인오케이</span>
														</div>
														<div class="rpu_box_date">
															<span>마지막 업데이트 : <?=$today_i?></span>
														</div>
														<div class="rpu_box_num">
															<span>미확인 3건</span>
														</div>
													</div>
												</div>

												<div class="rpu_box delay_3">
													<div class="rpu_box_in">
														<div class="rpu_box_tit">
															<span>[어른문방구] 해외문구 수입</span>
														</div>
														<div class="rpu_box_date">
															<span>마지막 업데이트 : <?=$today_r_im?></span>
														</div>
														<div class="rpu_box_num">
															<span>미확인 1건</span>
														</div>
													</div>
												</div>

												<div class="rpu_box delay_1">
													<div class="rpu_box_in">
														<div class="rpu_box_tit">
															<span>파티 만들기 기능수정</span>
														</div>
														<div class="rpu_box_date">
															<span>마지막 업데이트 : <?=$today_r_id?></span>
														</div>
														<div class="rpu_box_num">
															<span>미확인 1건</span>
														</div>
													</div>
												</div>

												<div class="rpu_box delay_1">
													<div class="rpu_box_in">
														<div class="rpu_box_tit">
															<span>리워디 내부서비스(ver.5.0)</span>
														</div>
														<div class="rpu_box_date">
															<span>마지막 업데이트 : <?=$today_r_imd?></span>
														</div>
														<div class="rpu_box_num">
															<span>미확인 1건</span>
														</div>
													</div>
												</div>

												<div class="rpu_box delay_7">
													<div class="rpu_box_in">
														<div class="rpu_box_tit">
															<span>어른달력</span>
														</div>
														<div class="rpu_box_date">
															<span>마지막 업데이트 : <?=$today_r_id?></span>
														</div>
														<div class="rpu_box_num">
															<span>미확인 1건</span>
														</div>
													</div>
												</div>
												<div class="rpu_box delay_7">
													<div class="rpu_box_in">
														<div class="rpu_box_tit">
															<span>2023 어른달력</span>
														</div>
														<div class="rpu_box_date">
															<span>마지막 업데이트 : <?=$today_r_id?></span>
														</div>
														<div class="rpu_box_num">
															<span>미확인 1건</span>
														</div>
													</div>
												</div>
												<div class="rpu_box delay_7">
													<div class="rpu_box_in">
														<div class="rpu_box_tit">
															<span>2023 어른달력</span>
														</div>
														<div class="rpu_box_date">
															<span>마지막 업데이트 : <?=$today_r_im?></span>
														</div>
														<div class="rpu_box_num">
															<span>미확인 1건</span>
														</div>
													</div>
												</div>
												<div class="rpu_box delay_7">
													<div class="rpu_box_in">
														<div class="rpu_box_tit">
															<span>2023 어른달력</span>
														</div>
														<div class="rpu_box_date">
															<span>마지막 업데이트 : <?=$today_i?></span>
														</div>
														<div class="rpu_box_num">
															<span>미확인 1건</span>
														</div>
													</div>
												</div>
												<div class="rpu_none">
													<span>미확인한 업무가 없습니다.</span>
												</div>

											</div>

										</div>
									</div>
								</div>

							</div>

							<div class="rew_mypage_party_make">
								<button class="btn_mypage_party_make on tuto tuto_01_01"><span>파티 만들기</span></button>
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

						<div class="rew_cha_list_func rew_party_wrap">
							<div class="rew_cha_list_func_in">
								<div class="rew_cha_count">
									<span>전체</span>
									<strong>9</strong>
								</div>
								<div class="rew_btn_delay">
									<button class="btn_delay_0 on tuto tuto_01_02"><span>전체</span></button>
									<button class="btn_delay_1"><span>원활</span></button>
									<button class="btn_delay_3"><span>보통</span></button>
									<button class="btn_delay_7"><span>지연</span></button>
								</div>
								<div class="rew_cha_search" style="right:10px">
									<div class="rew_cha_search_box">
										<input type="text" class="input_search" placeholder="파티명 검색" />
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
												<input type="checkbox" name="cha_chk_tab" id="cha_chk_tab_close">
												<label for="cha_chk_tab_close">종료된 파티(2)</label>
											</div>
										</li>
										<li>
											<div class="chk_tab">
												<input type="checkbox" name="cha_chk_tab" id="cha_chk_tab_my">
												<label for="cha_chk_tab_my">내 파티(4)</label>
											</div>
										</li>
									</ul>
								</div>
							</div>
						</div>

						<div class="rew_conts_scroll_10">

							<div class="rew_cha_list rew_party_wrap">
								<div class="rew_cha_list_in">
									<ul class="rew_cha_list_ul">

										<li class="list_delay_1">
											<a href="#null" onclick="javascript:void(0);">
												<div class="cha_box">
													<button class="cha_fav on tuto tuto_01_03"><span>즐겨찾기</span></button>
													<div class="cha_delay">
														<span>원할</span>
													</div>
													<span class="cha_tit">리워디 내부서비스(ver.5.0)</span>
													<div class="cha_date">
													<span><?=$today_r_dm?></span>
													</div>
													<div class="cha_bar">
														<div class="cha_num">
															<span>업무 <strong>23</strong></span>
														</div>
														<div class="cha_coin_all">
															<span>12,200</span>
														</div>
													</div>
													<div class="cha_user_box">
														<div class="cha_user_img" style="background-image:url(/html/images/pre/img_prof_03.png)"></div>
														<div class="cha_user_img" style="background-image:url(/html/images/pre/img_prof_04.png)"></div>
														<div class="cha_user_img cha_user_me" style="background-image:url(/html/images/pre/img_prof_02.png)"></div>
														<div class="cha_user_img" style="background-image:url(/html/images/pre/img_prof_03.png)"></div>
														<div class="cha_user_img" style="background-image:url(/html/images/pre/img_prof_04.png)"></div>
														<div class="cha_user_img" style="background-image:url(/html/images/pre/img_prof_02.png)"></div>
														<div class="cha_user_more">+3</div>
													</div>
												</div>
											</a>
										</li>

										<li class="list_delay_1">
											<a href="#null" onclick="javascript:void(0);">
												<div class="cha_box">
													<button class="cha_fav on"><span>즐겨찾기</span></button>
													<div class="cha_delay">
														<span>원할</span>
													</div>
													<span class="cha_tit">튜토리얼 테스트</span>
													<div class="cha_date">
													<span><?=$today_r_dm?></span>
													</div>
													<div class="cha_bar">
														<div class="cha_num">
															<span>업무 <strong>53</strong></span>
														</div>
														<div class="cha_coin_all">
															<span>7,500</span>
														</div>
													</div>
													<div class="cha_user_box">
														<div class="cha_user_img" style="background-image:url(/html/images/pre/img_prof_03.png)"></div>
														<div class="cha_user_img" style="background-image:url(/html/images/pre/img_prof_04.png)"></div>
														<div class="cha_user_img cha_user_me" style="background-image:url(/html/images/pre/img_prof_02.png)"></div>
														<div class="cha_user_img" style="background-image:url(/html/images/pre/img_prof_03.png)"></div>
													</div>
												</div>
											</a>
										</li>

										<li class="list_delay_3">
											<a href="#null" onclick="javascript:void(0);">
												<div class="cha_box">
													<button class="cha_fav on"><span>즐겨찾기</span></button>
													<div class="cha_delay">
														<span>보통</span>
													</div>
													<span class="cha_tit">리워디는 어떠신가요</span>
													<div class="cha_date">
													<span><?=$today_r_dm?></span>
													</div>
													<div class="cha_bar">
														<div class="cha_num">
															<span>업무 <strong>2</strong></span>
														</div>
														<div class="cha_coin_all">
															<span>450</span>
														</div>
													</div>
													<div class="cha_user_box">
														<div class="cha_user_img" style="background-image:url(/html/images/pre/img_prof_04.png)"></div>
														<div class="cha_user_img cha_user_me" style="background-image:url(/html/images/pre/img_prof_02.png)"></div>
														<div class="cha_user_img" style="background-image:url(/html/images/pre/img_prof_03.png)"></div>
													</div>
												</div>
											</a>
										</li>

										<li class="list_delay_3">
											<a href="#null" onclick="javascript:void(0);">
												<div class="cha_box">
													<button class="cha_fav on"><span>즐겨찾기</span></button>
													<div class="cha_delay">
														<span>보통</span>
													</div>
													<span class="cha_tit">급할수록 도라에몽</span>
													<div class="cha_date">
													<span><?=$today_r_dm?></span>
													</div>
													<div class="cha_bar">
														<div class="cha_num">
															<span>업무 <strong>23</strong></span>
														</div>
														<div class="cha_coin_all">
															<span>1,200</span>
														</div>
													</div>
													<div class="cha_user_box">
														<div class="cha_user_img" style="background-image:url(/html/images/pre/img_prof_04.png)"></div>
														<div class="cha_user_img" style="background-image:url(/html/images/pre/img_prof_02.png)"></div>
													</div>
												</div>
											</a>
										</li>

										<li class="list_delay_4 cha_dend">
											<a href="#null" onclick="javascript:void(0);">
												<div class="cha_box">
													<button class="cha_fav on"><span>즐겨찾기</span></button>
													<div class="cha_delay">
														<span>완료</span>
													</div>
													<span class="cha_tit">오즈의 맙소사</span>
													<div class="cha_date">
													<span><?=$today_r_dm?></span>
													</div>
													<div class="cha_bar">
														<div class="cha_num">
															<span>업무 <strong>102</strong></span>
														</div>
														<div class="cha_coin_all">
															<span>1,200</span>
														</div>
													</div>
													<div class="cha_user_box">
														<div class="cha_user_img" style="background-image:url(/html/images/pre/img_prof_03.png)"></div>
														<div class="cha_user_img" style="background-image:url(/html/images/pre/img_prof_04.png)"></div>
														<div class="cha_user_img" style="background-image:url(/html/images/pre/img_prof_02.png)"></div>
														<div class="cha_user_img" style="background-image:url(/html/images/pre/img_prof_03.png)"></div>
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


	<div class="layer_work" style="display:none;">
		<div class="lw_deam"></div>
		<div class="lw_in">
			<div class="lw_box">
				<div class="lw_box_in">
					<div class="lw_top">
						<strong>좋은아침입니다!</strong>
						<p>2022년 11월 01일 오전 09:00</p>
					</div>
					<div class="lw_btn">
						<button class="lw_off"><span>취소</span></button>
						<button class="lw_on"><span>출근하기</span></button>
					</div>
				</div>
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
