<?
	//header페이지
	$home_dir = str_replace( basename(__DIR__) , "" , __DIR__ );
	include $home_dir . "/inc_lude/header.php";

	$tuto = tutorial_chk();
	if($tuto['t_flag']>5){
		alert('해당 단계는 이미 완료하셨습니다!');
		echo "<script>history.back();</script>";
	}else if($tuto['t_flag']<5){
		alert('이전 단계를 먼저 수행해주세요.');
		echo "<script>history.back();</script>";
	}
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

	});

	function cli_next(idx){
		var next_idx = idx + 1;
		console.log(idx+"!@#"+next_idx);
		$(".tuto_mark_01_0"+idx).hide();
		$(".tuto_pop_01_0"+idx).hide();
		$(".tuto_mark_01_0"+next_idx).show();
		$(".tuto_pop_01_0"+next_idx).show();
		var win_w = $(window).width();
		if(win_w < 1401){
			var t00 = $(".tuto_pop_01_0"+next_idx).offset().top;
			$("html, body").animate({scrollTop:(t00-150)},200);
		}
	}

	function cli_prev(idx){
		var prev_idx = idx - 1;
		$(".tuto_mark_01_0"+idx).hide();
		$(".tuto_pop_01_0"+idx).hide();
		$(".tuto_mark_01_0"+prev_idx).show();
		$(".tuto_pop_01_0"+prev_idx).show();
		var win_w = $(window).width();
		if(win_w < 1401){
			var t00 = $(".tuto_pop_01_0"+prev_idx).offset().top;
			$("html, body").animate({scrollTop:(t00-150)},200);
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
				var level = "challenge";
			}else if(che_le == 'm_end'){
				var level = "main";
				// coin = 500;
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
						$(".tuto_end").css("display","block");
					}
				}

			});

		}

		
		function page_loc(sub_level){
			if(sub_level == 'party_v'){
				location.href = "/party/tu_pro_view.php";
			}else if(sub_level == 'party_pre'){
				location.href = "/party/tu_project.php";
			}else if(sub_level == 'party_end'){
				location.href = "/challenges/tu_chall.php";
			}else if(sub_level == 'challenge_v'){
				location.href = "/challenges/tu_chal_view.php?idx=1";
			}else if(sub_level == 'chal_prev'){
				location.href = "/challenges/tu_chall.php"
			}else if(sub_level == 'chal_end'){
				location.href = "/team/tu_team.php";
			}else if(sub_level == 'm_end'){
				location.href = "/team/index.php";
			}
		}
	
</script>	
<style type="text/css">
	@media all and (max-width:1860px){
		.rew_warp_in{height:auto;}
	}
</style>
<div class="tuto_end" style="display:none;">
	<div class="tuto_deam"></div>
	<div class="tuto_end_in">
		<div class="tuto_end_tit">
			<img src="/html/images/pre/img_tuto_tit_02.png" alt="성과가 보인다면 보상은 당연한 것!" />
			<p>보상 600코인이 지급되었습니다.</p>
		</div>
		<div class="tuto_end_coin">
			<strong>600</strong>
		</div>
		<div class="tuto_end_btn">
			<button onclick="page_loc('m_end');"><span>리워디 시작하기</span></button>
		</div>
	</div>
</div>
<div class="rew_tutorial_deam"></div>
	<div class="tuto_mark tuto_mark_01_01"><button><span></span></button></div>
	<div class="tuto_mark tuto_mark_01_02" style="display:none;"><button><span></span></button></div>
	<div class="tuto_mark tuto_mark_01_03" style="display:none;"><button><span></span></button></div>
	<div class="tuto_mark tuto_mark_01_04" style="display:none;"><button><span></span></button></div>
	<div class="tuto_mark tuto_mark_01_05" style="display:none;"><button><span></span></button></div>
	<div class="tuto_mark tuto_mark_01_06" style="display:none;"><button><span></span></button></div>
	<div class="tuto_mark tuto_mark_01_07" style="display:none;"><button><span></span></button></div>
	<div class="tuto_pop tuto_pop_01_01">
		<div class="tuto_in">
			<div class="tuto_tit">메인에 대해 알아보기</div>
			<div class="tuto_pager">1/7</div>
			<div class="tuto_desc">
				<p>출근부터 퇴근까지 진행상황을 타임라인 형태로 볼 수 있어요.</p>
				<p>좋아요를 받거나 보상을 받을 경우에도 표시돼요.</p>
				<p>모든 활동이 이곳에 기록돼요.</p>
			</div>
			<div class="tuto_btns">
				<!-- <button class="tuto_prev"><span>이전</span></button> -->
				<button class="tuto_next" onclick="cli_next(1);"><span>다음</span></button>
			</div>
		</div>
	</div>
	<div class="tuto_pop tuto_pop_01_02" style="display:none;">
		<div class="tuto_in">
			<div class="tuto_tit">메인에 대해 알아보기</div>
			<div class="tuto_pager">2/7</div>
			<div class="tuto_desc">
				<p>내가 보상받은 코인으로 10,000원 이상부터 출금 신청이 가능해요.</p>
				<p>출금 시에는 5%의 출금 수수료가 부가되고, 기재되어 있는 계좌로 바로 입금돼요.</p>
			</div>
			<div class="tuto_btns">
				<button class="tuto_prev" onclick="cli_prev(2);"><span>이전</span></button>
				<button class="tuto_next" onclick="cli_next(2);"><span>다음</span></button>
			</div>
		</div>
	</div>
	<div class="tuto_pop tuto_pop_01_03" style="display:none;">
		<div class="tuto_in">
			<div class="tuto_tit">메인에 대해 알아보기</div>
			<div class="tuto_pager">3/7</div>
			<div class="tuto_desc">
				<p>개인의 역량을 한눈에 볼 수 있어요.</p>
				<p>리워디 안에서의 활동은 모두 역량지수에 영향을 줘요.</p>
				<p>리워디에서 열심히 활동할수록 역량지수가 많이 올라간답니다.</p>
			</div>
			<div class="tuto_btns">
				<button class="tuto_prev" onclick="cli_prev(3);"><span>이전</span></button>
				<button class="tuto_next" onclick="cli_next(3);"><span>다음</span></button>
			</div>
		</div>
	</div>
	<div class="tuto_pop tuto_pop_01_04" style="display:none;">
		<div class="tuto_in">
			<div class="tuto_tit">메인에 대해 알아보기</div>
			<div class="tuto_pager">4/7</div>
			<div class="tuto_desc">
				<p>역량과 좋아요 지수가 올라가면 코인도 함께 보상돼요.</p>
				<p>한달 단위로 적립되며, 익월 첫 영업일에 내 코인으로 보상돼요. 내 코인으로 지급된 코인은 출금도 가능해요.</p>
			</div>
			<div class="tuto_btns">
				<button class="tuto_prev" onclick="cli_prev(4);"><span>이전</span></button>
				<button class="tuto_next" onclick="cli_next(4);"><span>다음</span></button>
			</div>
		</div>
	</div>
	<div class="tuto_pop tuto_pop_01_05" style="display:none;">
		<div class="tuto_in">
			<div class="tuto_tit">메인에 대해 알아보기</div>
			<div class="tuto_pager">5/7</div>
			<div class="tuto_desc">
				<p>공유, 보고, 업무 요청을 성실히 한 동료를 보여주는 공간이에요.</p>
				<p>좋아요 보내기를 클릭하면 바로 좋아요를 보낼 수 있어요.</p>
				<p>성실하고 적극적인 동료를 응원해 보세요.</p>
			</div>
			<div class="tuto_btns">
				<button class="tuto_prev" onclick="cli_prev(5);"><span>이전</span></button>
				<button class="tuto_next" onclick="cli_next(5);"><span>다음</span></button>
			</div>
		</div>
	</div>
	<div class="tuto_pop tuto_pop_01_06" style="display:none;">
		<div class="tuto_in">
			<div class="tuto_tit">메인에 대해 알아보기</div>
			<div class="tuto_pager">6/7</div>
			<div class="tuto_desc">
				<p>출근을 ON해서 동료들에게 나의 상태를 알려주세요.</p>
				<p>집중근무, 자리비움, 퇴근 등의 상태를 표현할 수 있고, 내 상태에서도 함께 볼 수 있어요.</p>
			</div>
			<div class="tuto_btns">
				<button class="tuto_prev" onclick="cli_prev(6);"><span>이전</span></button>
				<button class="tuto_next" onclick="cli_next(6);"><span>다음</span></button>
			</div>
		</div>
	</div>
	<div class="tuto_pop tuto_pop_01_07" style="display:none;">
		<div class="tuto_in">
			<div class="tuto_tit">메인에 대해 알아보기</div>
			<div class="tuto_pager">7/7</div>
			<div class="tuto_desc">
				<p>구성원의 출근 및 퇴근 시간을 확인할 수 있어요.</p>
				<p>출근을 ON한 경우 리스트에 나타나고, 각 구성원의 실시간 상태를 확인 할 수 있는 공간이예요.</p>
			</div>
			<div class="tuto_btns">
				<button class="tuto_prev" onclick="cli_prev(7);"><span>이전</span></button>
				<button class="tuto_next" onclick="tu_end('m_end');"><span>확인</span></button>
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
							<!-- <span class="rew_bar_alert"><em>멤버를 먼저 초대하세요.</em></span> -->
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
									<li class="rew_bar_li_05" title="">
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
						
					</div>
					
				</div>
				<!-- //menu -->

				<!-- 콘텐츠 -->
				<div class="rew_conts">
					<div class="rew_conts_in">

						<div class="rew_conts_scroll_00">
							<div class="rew_mains">
								<div class="rew_mains_in">
									<div class="rew_mains_timeline">
										<div class="rew_mains_timeline_in">
											<div class="rew_timeline">
												<div class="rtl_top">
													<strong class="on tuto tuto_01_01">타임라인</strong>
												</div>
												<div class="rtl_list">
													<div class="rtl_list_in">
														<ul>
															<li>
																<div class="rtl_list_box">
																	<dl>
																		<dt><strong>이선규님의 좋아요</strong></dt>
																		<dd><span>05:23 PM</span></dd>
																	</dl>
																</div>
															</li>
															<li class="rtl_coin">
																<div class="rtl_list_box">
																	<dl>
																		<dt><strong>이선규님에게 코인 보상받음</strong></dt>
																		<dd><span>03:24 PM</span><em>1,000</em></dd>
																	</dl>
																</div>
															</li>
															<li>
																<div class="rtl_list_box">
																	<dl>
																		<dt><strong>메모 작성</strong></dt>
																		<dd><span>03:20 PM</span></dd>
																	</dl>
																</div>
															</li>
															<li>
																<div class="rtl_list_box">
																	<dl>
																		<dt><strong>메모 작성</strong></dt>
																		<dd><span>03:19 PM</span></dd>
																	</dl>
																</div>
															</li>
															<li>
																<div class="rtl_list_box">
																	<dl>
																		<dt><strong>오늘업무 작성</strong></dt>
																		<dd><span>03:01 PM</span></dd>
																	</dl>
																</div>
															</li>
															<li>
																<div class="rtl_list_box">
																	<dl>
																		<dt><strong>메모 작성</strong></dt>
																		<dd><span>02:11 PM</span></dd>
																	</dl>
																</div>
															</li>
															<li>
																<div class="rtl_list_box">
																	<dl>
																		<dt><strong>메모 작성</strong></dt>
																		<dd><span>01:24 PM</span></dd>
																	</dl>
																</div>
															</li>
															<li>
																<div class="rtl_list_box">
																	<dl>
																		<dt><strong>손언영님에게 업무 공유함</strong></dt>
																		<dd><span>11:40 AM</span></dd>
																	</dl>
																</div>
															</li>
															<li>
																<div class="rtl_list_box">
																	<dl>
																		<dt><strong>메모 작성</strong></dt>
																		<dd><span>10:35 AM</span></dd>
																	</dl>
																</div>
															</li>
															<li>
																<div class="rtl_list_box">
																	<dl>
																		<dt><strong>메모 작성</strong></dt>
																		<dd><span>10:33 AM</span></dd>
																	</dl>
																</div>
															</li>
															<li>
																<div class="rtl_list_box">
																	<dl>
																		<dt><strong>이선규님에게 업무 공유받음</strong></dt>
																		<dd><span>10:23 AM</span></dd>
																	</dl>
																</div>
															</li>
															<li>
																<div class="rtl_list_box">
																	<dl>
																		<dt><strong>오늘업무 작성</strong></dt>
																		<dd><span>09:10 AM</span></dd>
																	</dl>
																</div>
															</li>
															<li>
																<div class="rtl_list_box">
																	<dl>
																		<dt><strong>출근</strong></dt>
																		<dd><span>08:45 AM</span></dd>
																	</dl>
																</div>
															</li>
														</ul>
														<ul class="rtl_list_old">
															<li class="old_2d">
																<div class="rtl_list_box">
																	<dl>
																		<dt><strong>2일전 읽지 않은 게시물 3건</strong></dt>
																		<dd><span>2022-09-29</span></dd>
																	</dl>
																</div>
															</li>
															<li class="old_3d">
																<div class="rtl_list_box">
																	<dl>
																		<dt><strong>3일전 읽지 않은 게시물 3건</strong></dt>
																		<dd><span>2022-09-28</span></dd>
																	</dl>
																</div>
															</li>
															<li class="old_5d">
																<div class="rtl_list_box">
																	<dl>
																		<dt><strong>5일전 읽지 않은 게시물 1건</strong></dt>
																		<dd><span>2022-09-26</span></dd>
																	</dl>
																</div>
															</li>
														</ul>
													</div>
												</div>
											</div>
										</div>
									</div>

									<div class="rew_mains_left">
										<div class="rew_mains_left_in">
											<div class="rew_mains_info">
												<div class="rew_mains_info_in">
													<div class="rew_mains_info_me">
														<div class="rew_mains_info_l">
															<div class="tl_prof">
																<div class="tl_prof_box">
																	<div class="tl_prof_img" style="background-image:url(/html/images/pre/img_prof_03.png);"></div>
																	<div class="tl_prof_slc">
																		<div class="tl_prof_slc_in">
																			<button class="button_prof"><span>프로필 변경</span></button>
																			<ul>
																				<li><button id="btn_slc_character"><span>캐릭터 선택</span></button></li>
																				<li>
																					<input type="file" id="prof" class="input_prof" />
																					<label for="prof" class="label_prof"><span>나만의 이미지 선택</span></label>
																				</li>
																				<li><button class="default_on"><span>MBTI 선택</span></button></li>
																			</ul>
																		</div>
																	</div>
																</div>
																<div class="rew_mains_info_name">
																	<strong>리워디님, 안녕하세요!</strong>
																	<span>디자인팀</span>
																</div>
															</div>

															<div class="rew_mypage_coin_box">
																<div class="title_area">
																	<div class="qna">
																		<strong class="title_main on tuto tuto_01_02">내 코인</strong>
																	</div>
																</div>
																<div class="rew_mypage_coin_chall">
																	<strong><span>115,000</span></strong>
																</div>
															</div>
														</div>

														<div class="rew_mains_info_r">
															<div class="rew_mains_chart_graph">
																<div id="radarChart" class=""></div>
																<div class="radar_grade radar_01">
																	<span class="radar_tit">지식</span>
																	<em class="grade_s">S</em>
																	<span class="radar_pt">(6.8)</span>
																</div>
																<div class="radar_grade radar_02">
																	<span class="radar_tit">성장</span>
																	<em class="grade_s">S</em>
																	<span class="radar_pt">(5.6)</span>
																</div>
																<div class="radar_grade radar_03">
																	<span class="radar_tit">성실</span>
																	<em class="grade_b">B</em>
																	<span class="radar_pt">(0.5)</span>
																</div>
																<div class="radar_grade radar_04">
																	<span class="radar_tit">실행</span>
																	<em class="grade_b">B</em>
																	<span class="radar_pt">(1.4)</span>
																</div>
																<div class="radar_grade radar_05">
																	<span class="radar_tit">협업</span>
																	<em class="grade_s">S</em>
																	<span class="radar_pt">(3.3)</span>
																</div>
																<div class="radar_grade radar_06">
																	<span class="radar_tit">성과</span>
																	<em class="grade_a">A</em>
																	<span class="radar_pt">(7.2)</span>
																</div>
																<div class="radar_total">
																	<span class="tuto tuto_01_03">92</span>
																</div>
															</div>
															<div class="rew_mains_chart_state">
																<div class="rew_mains_chart_state_tit qna">
																	<em>AI 알림</em>
																	<div class="rew_mains_chart_state_tit_txt">
																		이달에 적립된 코인을 확인하세요!
																		<!-- <span class="qna_q">?</span> -->
																		<div class="qna_a">
																			<span>이달의 코인은 다음달 1일에 일괄 적립됩니다.</span>
																		</div>
																	</div>
																	
																</div>
																<div class="rew_mains_chart_state_in">
																	<ul>
																		<li>
																			<div class="title_area">
																				<div class="qna">
																					<strong class="title_main">역량</strong>
																					<span class="qna_q open_qna_01">?</span>
																					<!-- <div class="qna_a">
																						<span>역량 역량</span>
																					</div> -->
																				</div>
																			</div>
																			<div class="chart_state_score_coin">
																				<div class="chart_state_score">
																					<span>+ 11점</span>
																				</div>
																				<div class="chart_state_coin">
																					<strong><span>12,400</span></strong>
																					<p class="coin_upon tuto tuto_01_04">Today 2,400 <em>▲</em></p>
																				</div>
																			</div>
																		</li>
																		<li>
																			<div class="title_area">
																				<div class="qna">
																					<strong class="title_main">좋아요</strong>
																					<span class="qna_q open_qna_02">?</span>
																					<!-- <div class="qna_a">
																						<span>좋아요 좋아요</span>
																					</div> -->
																				</div>
																			</div>
																			<div class="chart_state_score_coin">
																				<div class="chart_state_score">
																					<span>+ 8점</span>
																				</div>
																				<div class="chart_state_coin">
																					<strong><span>3,400</span></strong>
																					<p class="coin_up">Today 300 <em>▲</em></p>
																				</div>
																			</div>
																		</li>
																	</ul>
																</div>
															</div>
														</div>
													</div>


													<div class="rew_mains_heart_area">
														<div class="rew_mains_heart_area_in">
															<div class="rew_mains_heart_tit">
																<em>AI 추천</em>
																<span>공유, 보고, 메모를 잘하는 동료, 하트로 응원해 보아요!!</span>
															</div>
															<div class="rew_mains_heart_list">
																<ul>
																	<li>
																		<button class="btn_mains_heart_on">
																			<img src="/html/images/pre/ico_heart_b.png" alt="" />
																			<strong>튜토님</strong>
																			<span>출근 1등</span>
																			<em class="tuto tuto_01_05">좋아요 보내기</em>
																		</button>
																		<button class="btn_mains_heart_close"><span>닫기</span></button>
																	</li>
																	<li>
																		<button class="btn_mains_heart_on">
																			<img src="/html/images/pre/ico_heart_b.png" alt="" />
																			<strong>리워디님</strong>
																			<span>보고 대마왕</span>
																			<em>좋아요 보내기</em>
																		</button>
																		<button class="btn_mains_heart_close"><span>닫기</span></button>
																	</li>
																	<li>
																		<button class="btn_mains_heart_on">
																			<img src="/html/images/pre/ico_heart_b.png" alt="" />
																			<strong>이선규님</strong>
																			<span>불꽃 업무중</span>
																			<em>좋아요 보내기</em>
																		</button>
																		<button class="btn_mains_heart_close"><span>닫기</span></button>
																	</li>
																	<li>
																		<button class="btn_mains_heart_on">
																			<img src="/html/images/pre/ico_heart_b.png" alt="" />
																			<strong>도경백님</strong>
																			<span>업무 해결사</span>
																			<em class="">좋아요 보내기</em>
																		</button>
																		<button class="btn_mains_heart_close"><span>닫기</span></button>
																	</li>
																</ul>
															</div>
														</div>
													</div>
												</div>
											</div>
										</div>
									</div>

									<div class="rew_mains_right">
										<div class="rew_mains_right_in">

											<div class="rew_mains_live">
												<div class="rew_mains_live_tab">
													<div class="rew_live_my">
														<div class="rew_grid_onoff">
															<div class="rew_grid_onoff_inner">
																<ul>
																	<li class="onoff_01">
																		
																																					<em class="on">근무중</em>
																		
																		
																		<div class="btn_switch on tuto tuto_01_06" id="live_1_bt">
																			<strong class="btn_switch_on"></strong>
																			<span>버튼</span>
																			<strong class="btn_switch_off"></strong>
																		</div>
																	</li>
																</ul>
															</div>
														</div>
														<div class="rew_grid_onoff">
															<div class="rew_grid_onoff_in">
																<ul>
																	<li class="onoff_02">
																		<em>집중</em>
																		<div class="btn_switch" id="live_2_bt">
																			<strong class="btn_switch_on"></strong>
																			<span>버튼</span>
																			<strong class="btn_switch_off"></strong>
																		</div>
																	</li>

																	<li class="onoff_03">
																		<em>잠시</em>
																		<div class="btn_switch" id="live_3_bt">
																			<strong class="btn_switch_on"></strong>
																			<span>버튼</span>
																			<strong class="btn_switch_off"></strong>
																		</div>
																	</li>
																	<li class="onoff_04">
																		<em>퇴근</em>
																		<div class="btn_switch" id="live_4_bt" value="2023-04-12">
																			<strong class="btn_switch_on"></strong>
																			<span>버튼</span>
																			<strong class="btn_switch_off"></strong>
																		</div>
																	</li>
																</ul>
															</div>
														</div>
													</div>
													<div class="rew_live_now">
														<em>04/12 09:45</em><button id="reload_index">새로고침</button>
													</div>
												</div>
												<div class="rew_mains_live_list">
													<div class="live_list">
														<ul class="live_list_ul" id="main_live_list">
														
															
															
															<li class="live_list_box sli" id="live_user_list" style="cursor: pointer;">
																<div class="live_list_t">
																	<div class="live_list_user_img">
																		<div class="live_circle circle_01"><canvas width="104" height="104"></canvas></div>
																		<div class="live_list_user_img_bg"></div>
																		<div class="live_list_user_imgs" style="background-image:url('/data/NTPAVuvVtP1655879969/profile/img/20230201094121057_77FsXZyTGv_profile_55.jpg');"></div>
																		<!-- 메인 패널티 추가(김정훈) -->
																																			</div>
																	<div class="live_user_state">
																		<div class="live_user_state_in">
																			<ul>

																				
																				
																				
																																							</ul>
																		</div>
																	</div>
																</div>

																<div class="live_list_m">
																	<div class="live_user_name tuto tuto_01_01">
																		<strong>정현주</strong>
																		<em>9:29</em>
																	</div>
																</div>
															</li>
															
															<li class="live_list_box sli" id="live_user_list">
																<div class="live_list_t">
																	<div class="live_list_user_img">
																		<div class="live_circle circle_01"><canvas width="104" height="104"></canvas></div>
																		<div class="live_list_user_img_bg"></div>
																		<div class="live_list_user_imgs" style="background-image:url('/data/NTPAVuvVtP1655879969/profile/img/20230411093330131_RoqcIMoLK2_profile_58.png');"></div>
																		<!-- 메인 패널티 추가(김정훈) -->
																																			</div>
																	<div class="live_user_state">
																		<div class="live_user_state_in">
																			<ul>

																				
																				
																				
																																							</ul>
																		</div>
																	</div>
																</div>

																<div class="live_list_m">
																	<div class="live_user_name">
																		<strong>박희정</strong>
																		<em>9:28</em>
																	</div>
																</div>
															</li>
															
															<li class="live_list_box sli" id="live_user_list">
																<div class="live_list_t">
																	<div class="live_list_user_img">
																		<div class="live_circle circle_01"><canvas width="104" height="104"></canvas></div>
																		<div class="live_list_user_img_bg"></div>
																		<div class="live_list_user_imgs" style="background-image:url('/data/NTPAVuvVtP1655879969/profile/img/20230317101648206_7Tk7RhdHrm_profile_176.jpg');"></div>
																		<!-- 메인 패널티 추가(김정훈) -->
																																			</div>
																	<div class="live_user_state">
																		<div class="live_user_state_in">
																			<ul>

																				
																				
																				
																																							</ul>
																		</div>
																	</div>
																</div>

																<div class="live_list_m">
																	<div class="live_user_name">
																		<strong>문강산</strong>
																		<em>9:26</em>
																	</div>
																</div>
															</li>
															
															<li class="live_list_box sli" id="live_user_list">
																<div class="live_list_t">
																	<div class="live_list_user_img">
																		<div class="live_circle circle_01"><canvas width="104" height="104"></canvas></div>
																		<div class="live_list_user_img_bg"></div>
																		<div class="live_list_user_imgs" style="background-image:url('/html/images/pre/img_prof_06.png');"></div>
																		<!-- 메인 패널티 추가(김정훈) -->
																																			</div>
																	<div class="live_user_state">
																		<div class="live_user_state_in">
																			<ul>

																				
																				
																				
																																							</ul>
																		</div>
																	</div>
																</div>

																<div class="live_list_m">
																	<div class="live_user_name">
																		<strong>하병호</strong>
																		<em>9:26</em>
																	</div>
																</div>
															</li>
															
															<li class="live_list_box sli" id="live_user_list" style="cursor: pointer;">
																<div class="live_list_t">
																	<div class="live_list_user_img">
																		<div class="live_circle circle_01"><canvas width="104" height="104"></canvas></div>
																		<div class="live_list_user_img_bg"></div>
																		<div class="live_list_user_imgs" style="background-image:url('/data/NTPAVuvVtP1655879969/profile/img/20230310144030926_f8OxD4dW4f_profile_51.png');"></div>
																		<!-- 메인 패널티 추가(김정훈) -->
																																			</div>
																	<div class="live_user_state">
																		<div class="live_user_state_in">
																			<ul>

																				
																				
																				
																																							</ul>
																		</div>
																	</div>
																</div>

																<div class="live_list_m">
																	<div class="live_user_name">
																		<strong>윤지혜</strong>
																		<em>9:25</em>
																	</div>
																</div>
															</li>
															
															<li class="live_list_box sli" id="live_user_list" style="cursor: pointer;">
																<div class="live_list_t">
																	<div class="live_list_user_img">
																		<div class="live_circle circle_01"><canvas width="104" height="104"></canvas></div>
																		<div class="live_list_user_img_bg"></div>
																		<div class="live_list_user_imgs" style="background-image:url('/html/images/pre/img_prof_08.png');"></div>
																		<!-- 메인 패널티 추가(김정훈) -->
																																			</div>
																	<div class="live_user_state">
																		<div class="live_user_state_in">
																			<ul>

																				
																				
																				
																																							</ul>
																		</div>
																	</div>
																</div>

																<div class="live_list_m">
																	<div class="live_user_name">
																		<strong>손언영</strong>
																		<em>9:25</em>
																	</div>
																</div>
															</li>
															
															<li class="live_list_box sli" id="live_user_list">
																<div class="live_list_t">
																	<div class="live_list_user_img">
																		<div class="live_circle circle_01"><canvas width="104" height="104"></canvas></div>
																		<div class="live_list_user_img_bg"></div>
																		<div class="live_list_user_imgs" style="background-image:url('/data/NTPAVuvVtP1655879969/profile/img/20230406181053061_PjCUzdytlS_profile_69.jpg');"></div>
																		<!-- 메인 패널티 추가(김정훈) -->
																																			</div>
																	<div class="live_user_state">
																		<div class="live_user_state_in">
																			<ul>

																				
																				
																				
																																							</ul>
																		</div>
																	</div>
																</div>

																<div class="live_list_m">
																	<div class="live_user_name">
																		<strong>유주원</strong>
																		<em>9:24</em>
																	</div>
																</div>
															</li>
															
															<li class="live_list_box sli" id="live_user_list">
																<div class="live_list_t">
																	<div class="live_list_user_img">
																		<div class="live_circle circle_01"><canvas width="104" height="104"></canvas></div>
																		<div class="live_list_user_img_bg"></div>
																		<div class="live_list_user_imgs" style="background-image:url('/data/NTPAVuvVtP1655879969/profile/img_ori/20230406164531506_wacIz8IE9n_profile_53.jpg');"></div>
																		<!-- 메인 패널티 추가(김정훈) -->
																																			</div>
																	<div class="live_user_state">
																		<div class="live_user_state_in">
																			<ul>

																				
																				
																				
																																							</ul>
																		</div>
																	</div>
																</div>

																<div class="live_list_m">
																	<div class="live_user_name">
																		<strong>김명선</strong>
																		<em>9:23</em>
																	</div>
																</div>
															</li>
															
															<li class="live_list_box sli" id="live_user_list">
																<div class="live_list_t">
																	<div class="live_list_user_img">
																		<div class="live_circle circle_01"><canvas width="104" height="104"></canvas></div>
																		<div class="live_list_user_img_bg"></div>
																		<div class="live_list_user_imgs" style="background-image:url('/data/NTPAVuvVtP1655879969/profile/img/20230201115007466_fiksKgYomK_profile_67.jpg');"></div>
																		<!-- 메인 패널티 추가(김정훈) -->
																																			</div>
																	<div class="live_user_state">
																		<div class="live_user_state_in">
																			<ul>

																				
																				
																				
																																							</ul>
																		</div>
																	</div>
																</div>

																<div class="live_list_m">
																	<div class="live_user_name">
																		<strong>김성희</strong>
																		<em>9:21</em>
																	</div>
																</div>
															</li>

															<li class="live_list_box sli" id="live_user_list" style="cursor: pointer;">
																<div class="live_list_t">
																	<div class="live_list_user_img">
																		<div class="live_circle circle_01"><canvas width="104" height="104"></canvas></div>
																		<div class="live_list_user_img_bg"></div>
																		<div class="live_list_user_imgs" style="background-image:url('/html/images/pre/img_prof_default.png');"></div>
																		<!-- 메인 패널티 추가(김정훈) -->
																																			</div>
																	<div class="live_user_state">
																		<div class="live_user_state_in">
																			<ul>

																				
																				
																				
																																							</ul>
																		</div>
																	</div>
																</div>

																<div class="live_list_m">
																	<div class="live_user_name">
																		<strong>박정헌</strong>
																		<em>9:21</em>
																	</div>
																</div>
															</li>
															
															<li class="live_list_box sli" id="live_user_list">
																<div class="live_list_t">
																	<div class="live_list_user_img">
																		<div class="live_circle circle_01"><canvas width="104" height="104"></canvas></div>
																		<div class="live_list_user_img_bg"></div>
																		<div class="live_list_user_imgs" style="background-image:url('/data/NTPAVuvVtP1655879969/profile/img/20230223151543993_EtyOSBfrst_profile_54.jpg');"></div>
																		<!-- 메인 패널티 추가(김정훈) -->
																																			</div>
																	<div class="live_user_state">
																		<div class="live_user_state_in">
																			<ul>

																				
																				
																				
																																							</ul>
																		</div>
																	</div>
																</div>

																<div class="live_list_m">
																	<div class="live_user_name">
																		<strong>김민경</strong>
																		<em>9:21</em>
																	</div>
																</div>
															</li>
															
															<li class="live_list_box sli" id="live_user_list">
																<div class="live_list_t">
																	<div class="live_list_user_img">
																		<div class="live_circle circle_01"><canvas width="104" height="104"></canvas></div>
																		<div class="live_list_user_img_bg"></div>
																		<div class="live_list_user_imgs" style="background-image:url('/html/images/pre/img_prof_05.png');"></div>
																		<!-- 메인 패널티 추가(김정훈) -->
																																			</div>
																	<div class="live_user_state">
																		<div class="live_user_state_in">
																			<ul>

																				
																				
																				
																																										<li class="state_05">
																							<div class="live_user_state_circle">
																								<strong>미팅</strong>
																							</div>
																						</li>
																																																												</ul>
																		</div>
																	</div>
																</div>

																<div class="live_list_m">
																	<div class="live_user_name">
																		<strong>이선규</strong>
																		<em>9:15</em>
																	</div>
																</div>
															</li>
															
															<li class="live_list_box sli" id="live_user_list">
																<div class="live_list_t">
																	<div class="live_list_user_img">
																		<div class="live_circle circle_01"><canvas width="104" height="104"></canvas></div>
																		<div class="live_list_user_img_bg"></div>
																		<div class="live_list_user_imgs" style="background-image:url('/html/images/pre/img_prof_02.png');"></div>
																		<!-- 메인 패널티 추가(김정훈) -->
																																			</div>
																	<div class="live_user_state">
																		<div class="live_user_state_in">
																			<ul>

																				
																				
																				
																																							</ul>
																		</div>
																	</div>
																</div>

																<div class="live_list_m">
																	<div class="live_user_name">
																		<strong>유상길</strong>
																		<em>9:13</em>
																	</div>
																</div>
															</li>
															
															<li class="live_list_box sli" id="live_user_list">
																<div class="live_list_t">
																	<div class="live_list_user_img">
																		<div class="live_circle circle_01"><canvas width="104" height="104"></canvas></div>
																		<div class="live_list_user_img_bg"></div>
																		<div class="live_list_user_imgs" style="background-image:url('/data/NTPAVuvVtP1655879969/profile/img/20220114132513360_GP5isDbfd7_profile_64.jpg');"></div>
																		<!-- 메인 패널티 추가(김정훈) -->
																																			</div>
																	<div class="live_user_state">
																		<div class="live_user_state_in">
																			<ul>

																				
																				
																				
																																							</ul>
																		</div>
																	</div>
																</div>

																<div class="live_list_m">
																	<div class="live_user_name">
																		<strong>서민정</strong>
																		<em>9:11</em>
																	</div>
																</div>
															</li>
															
															<li class="live_list_box sli" id="live_user_list">
																<div class="live_list_t">
																	<div class="live_list_user_img">
																		<div class="live_circle circle_01"><canvas width="104" height="104"></canvas></div>
																		<div class="live_list_user_img_bg"></div>
																		<div class="live_list_user_imgs" style="background-image:url('/html/images/pre/img_prof_default.png');"></div>
																		<!-- 메인 패널티 추가(김정훈) -->
																																			</div>
																	<div class="live_user_state">
																		<div class="live_user_state_in">
																			<ul>

																				
																				
																				
																																							</ul>
																		</div>
																	</div>
																</div>

																<div class="live_list_m">
																	<div class="live_user_name">
																		<strong>최인준</strong>
																		<em>9:06</em>
																	</div>
																</div>
															</li>
															
															<li class="live_list_box sli" id="live_user_list">
																<div class="live_list_t">
																	<div class="live_list_user_img">
																		<div class="live_circle circle_01"><canvas width="104" height="104"></canvas></div>
																		<div class="live_list_user_img_bg"></div>
																		<div class="live_list_user_imgs" style="background-image:url('/html/images/pre/img_prof_02.png');"></div>
																		<!-- 메인 패널티 추가(김정훈) -->
																																			</div>
																	<div class="live_user_state">
																		<div class="live_user_state_in">
																			<ul>

																				
																				
																				
																																							</ul>
																		</div>
																	</div>
																</div>

																<div class="live_list_m">
																	<div class="live_user_name">
																		<strong>김정훈</strong>
																		<em>9:04</em>
																	</div>
																</div>
															</li>
															
															<li class="live_list_box sli" id="live_user_list">
																<div class="live_list_t">
																	<div class="live_list_user_img">
																		<div class="live_circle circle_01"><canvas width="104" height="104"></canvas></div>
																		<div class="live_list_user_img_bg"></div>
																		<div class="live_list_user_imgs" style="background-image:url('/html/images/pre/img_prof_07.png');"></div>
																		<!-- 메인 패널티 추가(김정훈) -->
																																			</div>
																	<div class="live_user_state">
																		<div class="live_user_state_in">
																			<ul>

																				
																				
																				
																																							</ul>
																		</div>
																	</div>
																</div>

																<div class="live_list_m">
																	<div class="live_user_name">
																		<strong>정혜윤</strong>
																		<em>9:04</em>
																	</div>
																</div>
															</li>
															
															<li class="live_list_box sli" id="live_user_list">
																<div class="live_list_t">
																	<div class="live_list_user_img">
																		<div class="live_circle circle_01"><canvas width="104" height="104"></canvas></div>
																		<div class="live_list_user_img_bg"></div>
																		<div class="live_list_user_imgs" style="background-image:url('/data/NTPAVuvVtP1655879969/profile/img/20221118102107223_ckKwYv47M1_profile_56.png');"></div>
																		<!-- 메인 패널티 추가(김정훈) -->
																																			</div>
																	<div class="live_user_state">
																		<div class="live_user_state_in">
																			<ul>

																				
																				
																				
																																							</ul>
																		</div>
																	</div>
																</div>

																<div class="live_list_m">
																	<div class="live_user_name">
																		<strong>최순영</strong>
																		<em>9:04</em>
																	</div>
																</div>
															</li>
															
															<li class="live_list_box live_none sli" id="live_user_list">
																<div class="live_list_t">
																	<div class="live_list_user_img">
																		<div class="live_circle circle_01"><canvas width="104" height="104"></canvas></div>
																		<div class="live_list_user_img_bg"></div>
																		<div class="live_list_user_imgs" style="background-image:url('/html/images/pre/img_prof_07.png');"></div>
																		<!-- 메인 패널티 추가(김정훈) -->
																																			</div>
																	<div class="live_user_state">
																		<div class="live_user_state_in">
																			<ul>

																																																													
																				
																				
																																							</ul>
																		</div>
																	</div>
																</div>

																<div class="live_list_m">
																	<div class="live_user_name">
																		<strong>김선희</strong>
																		<em></em>
																	</div>
																</div>
															</li>
															
															<li class="live_list_box live_none sli" id="live_user_list">
																<div class="live_list_t">
																	<div class="live_list_user_img">
																		<div class="live_circle circle_01"><canvas width="104" height="104"></canvas></div>
																		<div class="live_list_user_img_bg"></div>
																		<div class="live_list_user_imgs" style="background-image:url('/html/images/pre/img_prof_01.png');"></div>
																		<!-- 메인 패널티 추가(김정훈) -->
																																			</div>
																	<div class="live_user_state">
																		<div class="live_user_state_in">
																			<ul>

																																																													
																				
																				
																																							</ul>
																		</div>
																	</div>
																</div>

																<div class="live_list_m">
																	<div class="live_user_name">
																		<strong>도경백</strong>
																		<em></em>
																	</div>
																</div>
															</li>
															
															<li class="live_list_box live_none sli" id="live_user_list">
																<div class="live_list_t">
																	<div class="live_list_user_img">
																		<div class="live_circle circle_01"><canvas width="104" height="104"></canvas></div>
																		<div class="live_list_user_img_bg"></div>
																		<div class="live_list_user_imgs" style="background-image:url('/data/NTPAVuvVtP1655879969/profile/img/20220211175619465_xP6z3O7IWY_profile_66.jpg');"></div>
																		<!-- 메인 패널티 추가(김정훈) -->
																																			</div>
																	<div class="live_user_state">
																		<div class="live_user_state_in">
																			<ul>

																																																													
																				
																				
																																							</ul>
																		</div>
																	</div>
																</div>

																<div class="live_list_m">
																	<div class="live_user_name">
																		<strong>양정인</strong>
																		<em></em>
																	</div>
																</div>
															</li>
															
															<li class="live_list_box live_none sli" id="live_user_list">
																<div class="live_list_t">
																	<div class="live_list_user_img">
																		<div class="live_circle circle_01"><canvas width="104" height="104"></canvas></div>
																		<div class="live_list_user_img_bg"></div>
																		<div class="live_list_user_imgs" style="background-image:url('/html/images/pre/img_prof_default.png');"></div>
																		<!-- 메인 패널티 추가(김정훈) -->
																																			</div>
																	<div class="live_user_state">
																		<div class="live_user_state_in">
																			<ul>

																																																													
																				
																				
																																							</ul>
																		</div>
																	</div>
																</div>

																<div class="live_list_m">
																	<div class="live_user_name">
																		<strong>이강산</strong>
																		<em></em>
																	</div>
																</div>
															</li>
															
															<li class="live_list_box live_none sli" id="live_user_list">
																<div class="live_list_t">
																	<div class="live_list_user_img">
																		<div class="live_circle circle_01"><canvas width="104" height="104"></canvas></div>
																		<div class="live_list_user_img_bg"></div>
																		<div class="live_list_user_imgs" style="background-image:url('/html/images/pre/img_prof_default.png');"></div>
																		<!-- 메인 패널티 추가(김정훈) -->
																																			</div>
																	<div class="live_user_state">
																		<div class="live_user_state_in">
																			<ul>

																																																													
																				
																				
																																							</ul>
																		</div>
																	</div>
																</div>

																<div class="live_list_m">
																	<div class="live_user_name">
																		<strong>장재필</strong>
																		<em></em>
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
				<!-- //콘텐츠 -->

			</div>
		</div>

	<!-- Step 1) Load D3.js -->
	<script src="https://d3js.org/d3.v6.min.js"></script>
	<!-- Step 2) Load billboard.js with style -->
	<script src="/js/billboard.js"></script>
	<!-- Load with base style -->
	<link rel="stylesheet" href="/html/css/billboard.css">
	<script type="text/javascript">
		$(document).ready(function(){
			var chart = bb.generate({
				data: {
					x: "x",
					columns: [
					["x", "에너지", "성장", "성실", "실행", "협업", "성과"],
					["역량평가 리포트", 80, 95, 50, 60, 85, 70]
					],
					color: "#aaa",
					type: "radar",
					labels: false,
					colors: {
					"역량평가 리포트": "#38c9d2",
					// "역량평가 평균": "#ffaf04"
					}
				},
				size: {
					width: 230,
					height: 230
				},
				radar: {
					axis: {
						max: 100
					},
					level: {
						depth: 4
					},
					direction: {
						clockwise: true
					}
				},
				tooltip: {
					show: false
				},
				point: {
					show: true
				},
				transition: {
				   duration: 500
				},
				bindto: "#radarChart"
			});

			

			

			$(".open_qna_01").click(function(){
				$(".qna_layer_01").show();
				$(".qna_circle_01").circleProgress({
					startAngle: -Math.PI / 4 * 2,
					value: 0,
					thickness : 50,
					size:200,
					emptyFill :'#f7f8f9',
					lineCap: 'rect',
					fill: {color: '#38c9d2'},
					animation: {
					  duration: 1200
					}
				}).on("circle-animation-progress", function(event, progress) {
					$(this).find("strong").html(Math.round(100 - (0 * progress)) + "<i>%</i>");
				});
				setTimeout(function(){
					$(".qna_circle_01").circleProgress('value', 0.40).on("circle-animation-progress", function(event, progress) {
						$(this).find("strong").html(Math.round(100 - (40 * progress)) + "<i>%</i>");
					});
				}, (500+(4 * 40)));
			});
			$(".open_qna_02").click(function(){
				$(".qna_layer_02").show();
				$(".qna_circle_02").circleProgress({
					startAngle: -Math.PI / 4 * 2,
					value: 0,
					thickness : 50,
					size:200,
					emptyFill :'#f7f8f9',
					lineCap: 'rect',
					fill: {color: '#38c9d2'},
					animation: {
					  duration: 1200
					}
				}).on("circle-animation-progress", function(event, progress) {
					$(this).find("strong").html(Math.round(100 - (0 * progress)) + "<i>%</i>");
				});
				setTimeout(function(){
					$(".qna_circle_02").circleProgress('value', 0.25).on("circle-animation-progress", function(event, progress) {
						$(this).find("strong").html(Math.round(100 - (25 * progress)) + "<i>%</i>");
					});
				}, (500+(4 * 25)));
			});
			$(".qna_layer_01").click(function(){
				$(this).hide();
			});
			$(".qna_layer_02").click(function(){
				$(this).hide();
			});

		});
	</script>
	</div>

	<!-- footer start-->
	<? include $home_dir . "/inc_lude/footer.php";?>
	<!-- footer end-->
</body>
</html>
