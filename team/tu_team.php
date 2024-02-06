<?
	//header페이지
	$home_dir = str_replace( basename(__DIR__) , "" , __DIR__ );
	include $home_dir . "/inc_lude/header_main.php";

	$member_info = member_row_info($user_id);
	$tuto_flag = $member_info['t_flag'];	

	$tuto = tutorial_chk();
	// if($tuto['t_flag']>5){
	// 	alert('해당 단계는 이미 완료하셨습니다!');
	// 	echo "<script>history.back();</script>";
	// }else 
	if($tuto['t_flag']<5){
		alert('이전 단계를 먼저 수행해주세요.');
		echo "<script>history.back();</script>";
	}
?>
<script src="/js/tutorial_common.js<?php echo VER;?>"></script>
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

			if(che_le == 'p_end'){
				var level = "party";
			}else if(che_le == 'c_end'){
				var level = "challenge";
			}else if(che_le == 'm_end'){
				var level = "main";
			}

			fdata.append("mode", mode);
			fdata.append("level", level);
			
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
<!-- <div class="tuto_end" >
	<div class="tuto_deam"></div>
	<div class="tuto_end_in">
		<div class="tuto_end_tit">
			<img src="/html/images/pre/img_tuto_tit_02.png" alt="성과가 보인다면 보상은 당연한 것!" />
			<p>역량 점수가 10점 증가하였습니다!</p>
			<p>좋아요 7개를 획득하였습니다!</p>
		</div>
		<<div class="tuto_end_coin">
			<strong>600</strong>
		</div> 
		<div class="tuto_end_btn">
			<button onclick="page_loc('m_end');"><span>리워디 시작하기</span></button>
		</div>
	</div>
</div> -->
<div class="tuto_end" style="display:none;">
	<div class="tuto_deam"></div>
	<div class="tuto_end_in">
		<div class="tuto_end_tit">
			<img src="images/pre/img_tuto_tit_cha.png" alt="튜토리얼 알캐릭터" class="tuto_end_cha">
			<img src="images/pre/img_tuto_tit_02.png" alt="성과가 보인다면 보상은 당연한 것!" />
			<!-- <p>보상 500코인이 지급되었습니다.</p> -->
		</div>
		<div class="tuto_end_coin">
			<p>역량 점수가 <span>10점 증가</span>하였습니다!</p>
			<p>좋아요를 <span>7개 획득</span>하였습니다!</p>
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
			<div class="tuto_tit">타임라인</div>
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
			<div class="tuto_tit">내 코인</div>
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
			<div class="tuto_tit">역량 평가지표</div>
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
			<div class="tuto_tit">역량과 좋아요</div>
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
			<div class="tuto_tit">나의 상태</div>
			<div class="tuto_pager">5/7</div>
			<div class="tuto_desc">
				<p>출근을 ON해서 동료들에게 나의 상태를 알려주세요.</p>
				<p>집중근무, 자리비움, 퇴근 등의 상태를 표현할 수 있고, 내 상태에서도 함께 볼 수 있어요.</p>
			</div>
			<div class="tuto_btns">
				<button class="tuto_prev" onclick="cli_prev(5);"><span>이전</span></button>
				<button class="tuto_next" onclick="cli_next(5);"><span>다음</span></button>
			</div>
		</div>
	</div>
	<div class="tuto_pop tuto_pop_01_06" style="display:none;">
		<div class="tuto_in">
			<div class="tuto_tit">구성원 리스트</div>
			<div class="tuto_pager">6/7</div>
			<div class="tuto_desc">
				<p>구성원의 출근 및 퇴근 시간을 확인할 수 있어요.</p>
				<p>출근을 ON한 경우 리스트에 나타나고, +버튼을 누르면 각 구성원의 실시간 상태를 확인 할 수 있는 LIVE 페이지로 이동해요.</p>
			</div>
			<div class="tuto_btns">
				<button class="tuto_prev" onclick="cli_prev(6);"><span>이전</span></button>
				<button class="tuto_next" onclick="cli_next(6);"><span>다음</span></button>
			</div>
		</div>
	</div>

	<div class="tuto_pop tuto_pop_01_07" style="display:none;">
		<div class="tuto_in">
			<div class="tuto_tit">AI추천 좋아요</div>
			<div class="tuto_pager">7/7</div>
			<div class="tuto_desc">
				<p>공유, 보고, 업무 요청을 성실히 한 동료를 보여주는 공간이에요.</p>
				<p>배너를 클릭하면 바로 좋아요를 보낼 수 있어요.</p>
				<p>성실하고 적극적인 동료를 응원해 보세요.</p>
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
				<input type="hidden" value="<?=$tuto_flag?>" id="tutorial_flag">
				<? include $home_dir . "/inc_lude/header_new.php";?>
				<!-- menu -->
				<? include $home_dir . "/inc_lude/menu.php";?>
				<!-- //menu -->
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

															<div class="rew_mypage_coin_box">
																<div class="title_area">
																	<div class="qna">
																		<strong class="title_main on tuto tuto_01_02">내 코인</strong>
																	</div>
																</div>
																<div class="rew_mypage_coin_chall" id="rew_mypage_coin_chall">
																	<strong><span>110,723</span></strong>
																</div>
															</div>

															<div class="rew_mains_chart_state">
																<div class="rew_mains_chart_state_tit qna">
																	<em>AI 알림</em>
																	<div class="rew_mains_chart_state_tit_txt">
																		이달에 적립된 코인을 확인하세요!
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

														<!--//에너지1, 성과2, 성장3, 협업4, 성실5, 실행6-->
														<div class="rew_mains_info_r">
															<div class="rew_main_anno">
																<div class="rew_main_anno_in">
																	<span>상태 메시지를 입력해주세요 :)</span>
																	<em></em>
																</div>
															</div>
															
															<div class="tl_prof">
																<div class="tl_prof_box">
																<div class="tl_prof_img" style="background-image:url('<?=$member_row_info['profile_img_src']?$member_row_info['profile_img_src']:"/html/images/pre/img_prof_default.png"?>');" id="profile_character_img"></div>
																	<div class="tl_prof_slc">
																		<div class="tl_prof_slc_in">
																			<button class="button_prof main_prof"><span>프로필 변경</span></button>
																			
																			<!-- <ul>
																				<li><button id="btn_slc_character"><span>캐릭터 선택</span></button></li>
																				<li>
																					<input type="file" id="prof" class="input_prof" />
																					<label for="prof" class="label_prof" id="profile_img_change"><span>나만의 이미지 선택</span></label>
																				</li>
																				<li><button class="default_on" id="character_default"><span>기본 이미지로 변경</span></button></li>
																			</ul> -->
																			
																		</div>
																	</div>
																</div>
																<div class="rew_mains_info_name">
																	<strong>리워디님, 안녕하세요!</strong>
																	<span>마케팅팀</span>
																	<input type="hidden" id="mains_info_uid" value="<?=$user_id?>">
																</div>
															</div>

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
														</div>
													</div>
													<div class="rew_main_banner_area">
														<div class="rew_main_banner_area_in">
															<div class="banner_img"><img src="/html/images/pre/rew_cha_01.png" alt="알 이미지"></div>
															<em>AI Adviser</em>
															<span class="typing_event" style="font-weight: 400;">
															좋은아침이예요~ 오늘도 우리 같이 화이팅 해요!! 으쌰으쌰~
															</span>
														</div>
													</div>

													<!-- 오늘 업무 카운트-->
													<div class="rew_main_list_area">
														<div class="rew_main_list_area_in">
															<ul>
																<li class="new on">
																	<a href="http://demo.rewardy.co.kr/todaywork/index.php">
																		<em>오늘업무</em>
																		<span>3</span>
																	</a>
																</li>
																<li style="pointer-events: none;">
																	<a href="http://demo.rewardy.co.kr/todaywork/index.php">
																		<em>미완료</em>
																		<span>0</span>
																	</a>
																</li>
																<li style="pointer-events: none;">
																	<a href="http://demo.rewardy.co.kr/todaywork/index.php">
																		<em>메모</em>
																		<span>0</span>
																	</a>
																</li>
																<li class="on">
																	<a href="http://demo.rewardy.co.kr/todaywork/index.php">
																		<em>보고</em>
																		<span>1</span>
																	</a>
																</li>
																<li style="pointer-events: none;">
																	<a href="http://demo.rewardy.co.kr/todaywork/index.php">
																		<em>요청</em>
																		<span>0</span>
																	</a>
																</li>
																<li class="on">
																	<a href="http://demo.rewardy.co.kr/todaywork/index.php">
																		<em>공유</em>
																		<span>1</span>
																	</a>
																</li>
																<li class="on">
																	<a href="http://demo.rewardy.co.kr/party/index.php">
																		<em>파티</em>
																		<span>1</span>
																	</a>
																</li>
																<li class="on">
																	<a href="http://demo.rewardy.co.kr/challenge/index.php">
																		<em>챌린지</em>
																		<span>2</span>
																	</a>
																</li>
															</ul>
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
															<div class="rew_grid_onoff_in">
																<ul>
																	<li class="onoff_01">
																		<em class="on">근무중</em>
																		<div class="btn_switch on  tuto tuto_01_06" id="main_1_bt">
																			<strong class="btn_switch_on"></strong>
																			<span>버튼</span>
																			<strong class="btn_switch_off"></strong>
																		</div>
																	</li>

																	<?
																		$sql = "select idx, email , state, work_flag, decide_flag, work_stime, work_etime from work_todaywork use index(state) where state='0' and email = '".$user_id."' and companyno='".$companyno."' and decide_flag = '8' and share_flag!='2' and workdate='".TODATE."'";
																		$person_work = selectAllQuery($sql);
																		for($i =0; $i < count($person_work['idx']); $i++){
																			if($now_time >= $person_work['work_stime'][$i] && $now_time <= $person_work['work_etime'][$i]){
																				$work_status = "on";
																			}else{
																				$work_status = "off";
																			}
																		}
																	?>
																	<li class="onoff_02">
																		<em >회의</em>
																		<div class="btn_switch" id="live_3_bt">
																			<strong class="btn_switch_on"></strong>
																			<span>버튼</span>
																			<strong class="btn_switch_off"></strong>
																		</div>
																	</li>
																	<li class="onoff_04">
																		<em>퇴근</em>
																		<div class="btn_switch" id="live_4_bt">
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
														<em><?=$updatetime?></em><button id="reload_index">새로고침</button>
													</div>
												</div>
												<div class="rew_mains_live_list">
													<div class="live_list">
														<div class="live_tab">
															<div class="live_tab_in">
																<ul>
																	<li class="on option_count" value = "all"><span>21<em>명</em></span><em>정상근무</em></li>
																	<li class = "option_count" value = "rest" style="pointer-events: none;"><span>0<em>명</em></span><em>연차/반차</em></li>
																	<li class = "option_count" value = "early" style="pointer-events: none;"><span>0<em>명</em></span><em>조퇴/외출</em></li>
																	<li class = "option_count" value = "meet" style="pointer-events: none;"><span>0<em>명</em></span><em>미팅/회의</em></li>
																	<li class = "option_count" value = "business" style="pointer-events: none;"><span>0<em>명</em></span><em>출장</em></li>
																</ul>
															</div>
														</div>
														<ul class="live_list_ul" id="main_live_list">
															<li class="live_list_box<?=$live_class?>" id="live_user_list">
																<div class="live_list_t">
																	<div class="live_list_user_img">
																		<div class="live_circle circle_01"></div>
																		<div class="live_list_user_img_bg"></div>
																		<div class="live_list_user_imgs" style="background-image:url('<?=$profile_file?$profile_img:"/html/images/pre/img_prof_default.png"?>');"></div>
																	</div>
																</div>
																<div class="live_list_m">
																	<div class="live_user_name">
																		<strong>리워디</strong>
																		<em>9:20</em>
																	</div>
																</div>
															</li>
															<li class="live_list_box<?=$live_class?>" id="live_user_list">
																<div class="live_list_t">
																	<div class="live_list_user_img">
																		<div class="live_circle circle_01"></div>
																		<div class="live_list_user_img_bg"></div>
																		<div class="live_list_user_imgs" style="background-image:url('<?=$profile_file?$profile_img:"/html/images/pre/img_prof_default.png"?>');"></div>
																	</div>
																</div>
																<div class="live_list_m">
																	<div class="live_user_name">
																		<strong>리워디</strong>
																		<em>9:20</em>
																	</div>
																</div>
															</li>
															<li class="live_list_box<?=$live_class?>" id="live_user_list">
																<div class="live_list_t">
																	<div class="live_list_user_img">
																		<div class="live_circle circle_01"></div>
																		<div class="live_list_user_img_bg"></div>
																		<div class="live_list_user_imgs" style="background-image:url('<?=$profile_file?$profile_img:"/html/images/pre/img_prof_default.png"?>');"></div>
																	</div>
																</div>
																<div class="live_list_m">
																	<div class="live_user_name">
																		<strong>리워디</strong>
																		<em>9:20</em>
																	</div>
																</div>
															</li>
															<li class="live_list_box<?=$live_class?>" id="live_user_list">
																<div class="live_list_t">
																	<div class="live_list_user_img">
																		<div class="live_circle circle_01"></div>
																		<div class="live_list_user_img_bg"></div>
																		<div class="live_list_user_imgs" style="background-image:url('<?=$profile_file?$profile_img:"/html/images/pre/img_prof_default.png"?>');"></div>
																	</div>
																</div>
																<div class="live_list_m">
																	<div class="live_user_name">
																		<strong>리워디</strong>
																		<em>9:20</em>
																	</div>
																</div>
															</li>
															<li class="live_list_box" id="live_user_list">
																<div class="live_list_more_img tuto tuto_01_07">
																	<div class="live_list_more"></div>
																</div>
															</li>
														</ul>
													</div>
												</div>
											</div>
											<div class="rew_heart_area">
													<div class="rew_heart_area_in">
														<div class="heart_coment">
															<div class="heart_coment_in">
																<em>AI 추천</em>
																<span>공유, 보고, 메모 잘하는 동료, 응원해 보아요!</span>
																<button id="reload_like_index">새로고침</button>
															</div>
														</div>
														<ul class="heart_user_list">
															<li>
																<div class="heart_user">
																	<div class="heart_user_imgs" style="background-image:url('<?=$profile_file?$profile_img:"/html/images/pre/img_prof_default.png"?>');"></div>
																	<div class="heart_user_text">
																		<p>출근 1등</p>
																		<span>리워디님이 <b class="heart_point">1등</b>으로 출근했습니다!</span>
																		<em class="tuto tuto_01_05">08:51 출근</em>
																	</div>
																</div>
															</li>
															<li>
																<div class="heart_user">
																	<div class="heart_user_imgs" style="background-image:url('<?=$profile_file?$profile_img:"/html/images/pre/img_prof_default.png"?>');"></div>
																	<div class="heart_user_text">
																		<p></p>
																		<span>튜토리얼님이 <b class="heart_point">활발하게 협업</b> 중입니다!</span>
																		<em> 리워디 튜토리얼 협업해요!</em>
																	</div>
																</div>
															</li>
															<li>
																<div class="heart_user">
																	<div class="heart_user_imgs" style="background-image:url('<?=$profile_file?$profile_img:"/html/images/pre/img_prof_default.png"?>');"></div>
																	<div class="heart_user_text">
																		<p>불꽃 업무중</p>
																		<span>이선규님이 <b class="heart_point">불꽃 업무</b> 중입니다!</span>
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
				<!-- //콘텐츠 -->

				<?php include $home_dir . "/layer/member_join.php";?>


				<?php
					//페널티 카드
					// include $home_dir . "/layer/member_penalty.php";
				?>

			</div>
		</div>
	</div>


	<!-- 한줄소감 -->
	<div class="feeling_first" style="display:none;">
		<div class="ff_deam"></div>
		<div class="ff_in">
			<div class="ff_box">
				<div class="ff_box_in">
					<div class="ff_close">
						<button><span>닫기</span></button>
					</div>
					<div class="ff_top">
						<strong>오늘 하루는 어땠나요?</strong>
					</div>
					<div class="ff_area">
						<ul>
							<li>
								<button class="btn_ff_01<?=$review_info['work_idx']==1?" on":""?>" value="1">
									<strong></strong>
									<span>최고야</span>
								</button>
							</li>
							<li>
								<button class="btn_ff_02<?=$review_info['work_idx']==2?" on":""?>" value="2">
									<strong></strong>
									<span>뿌듯해</span>
								</button>
							</li>
							<li>
								<button class="btn_ff_03<?=$review_info['work_idx']==3?" on":""?>" value="3">
									<strong></strong>
									<span>기분좋아</span>
								</button>
							</li>

							<li>
								<button class="btn_ff_04<?=$review_info['work_idx']==4?" on":""?>" value="4">
									<strong></strong>
									<span>감사해</span>
								</button>
							</li>
							<li>
								<button class="btn_ff_05<?=$review_info['work_idx']==5?" on":""?>" value="5">
									<strong></strong>
									<span>재밌어</span>
								</button>
							</li>
							<li>
								<button class="btn_ff_06<?=$review_info['work_idx']==6?" on":""?>" value="6">
									<strong></strong>
									<span>수고했어</span>
								</button>
							</li>

							<li>
								<button class="btn_ff_07<?=$review_info['work_idx']==7?" on":""?>" value="7">
									<strong></strong>
									<span>무난해</span>
								</button>
							</li>
							<li>
								<button class="btn_ff_08<?=$review_info['work_idx']==8?" on":""?>" value="8">
									<strong></strong>
									<span>지쳤어</span>
								</button>
							</li>
							<li>
								<button class="btn_ff_09<?=$review_info['work_idx']==9?" on":""?>" value="9">
									<strong></strong>
									<span>속상해</span>
								</button>
							</li>
						</ul>
					</div>
					<div class="ff_bottom">
						<input type="text" id="icon_idx" value="<?=$review_info['work_idx']?>">
						<button class="btn_off">다음</button>
						<input type="hidden" id="review_idx">
					</div>
				</div>
			</div>
		</div>
	</div>

	<div class="feeling_layer" style="display:none;">
		<div class="fl_deam"></div>
		<div class="fl_in">
			<div class="fl_box">
				<div class="fl_box_in">
					<div class="fl_close">
						<button><span>닫기</span></button>
					</div>
					<div class="fl_top">
						<strong>오늘 하루는 어땠나요?</strong>
					</div>
					<div class="fl_area">
						<div class="fl_desc">
							<strong></strong>
							<p><span>최고의</span> 하루였어요!</p>
						</div>
						<div class="fl_input">
							<input type="text" class="input_fl" placeholder="한줄소감을 남겨주세요!" id="input_fl" value="<?=$review_info['comment']?>"/>
						</div>
					</div>
					<div class="fl_bottom" id="fl_bottom">
						<button>퇴근합니다</button>
					</div>
				</div>
			</div>
		</div>
	</div>




	<div class="t_layer rew_layer_join" style="display:none;">
		<div class="tl_deam"></div>
		<div class="tl_in">
			<div class="tl_close">
				<button><span>닫기</span></button>
			</div>
			<div class="tl_login_logo">
				<span>리워디</span>
			</div>
			<div class="tl_tit">
				<strong>가입하기</strong>
				<span>리워디에서 인증을요청합니다. <br />
				리워디와 함께 하세요!</span>
			</div>
			<div class="tl_list">
				<ul>
					<li>
						<div class="tc_input">
							<input type="text" id="z5" name="user_id" class="input_001" placeholder="이메일" />
							<label for="z5" class="label_001">
								<strong class="label_tit">이메일을 입력하세요</strong>
							</label>
						</div>
					</li>
				</ul>
			</div>
			<div class="tl_btn">
				<button><span>인증메일 발송</span></button>
			</div>
			<div class="tl_descript">
				<p>리워디에서 인증을 요청합니다.<br />
				아래 링크를 클릭하셔서, 비밀번호를 설정해 주세요.<br />
				링크가 클릭되지 않으시면 아래 주소를 복사하여 인터넷 브라우저에 붙여<br />
				넣어주세요.<br />
				<br />
				https://www.rewardy.co.kr/<br />
				<br />
				기타 문의사항은 1588-8443으로 문의해 주세요.<br />
				리워디와 함께 해주셔서 감사합니다.
				</p>
			</div>
		</div>
	</div>



	<div class="t_layer rew_layer_character item_prof" style="display:none;">
		<input type='hidden' id='check_profile'>
		<div class="tl_deam"></div>
		<div class="tl_in">
			<div class="tl_close">
				<button><span>닫기</span></button>
			</div>
			<div class="tl_tit">
				<strong>캐릭터 설정</strong>
				<span>리워디에서 기본으로 제공하는 <br />캐릭터입니다.</span>
			</div>
			<div class="tl_profile">
				<ul>
					<?for($i=0; $i<count($character_img_info['idx']); $i++){

						$idx = $character_img_info['idx'][$i];
						$file_path = $character_img_info['file_path'][$i];
						$file_name = $character_img_info['file_name'][$i];
						$fp_flag = $character_img_info['fp_flag'][$i];

						$character_img_src = $file_path.$file_name;

						$posi = $i + 1;

						if($fp_flag == 1){
							$pos_cn = $pos_cn + 1;
							$pos_ht = "class='pos_ht kp$pos_cn'";
						}
					?>
						<li id="posi_<?=$posi?>" <?=$pos_ht?>>
							<div class="tl_profile_box">
								<div class="tl_profile_img" style="background-image:url(<?=$character_img_src?>);">
									<?if($fp_flag == 0 || $img_buy_arr[$idx] != ''){?>
										<button class="btn_profile<?=$member_row_info['profile_type']=='0' && $member_row_info['profile_img_idx']==$idx?" on":""?>" id="profile_img_0<?=$idx?>" value="<?=$idx?>"><span>기본 프로필 이미지1 선택</span></button>
									<?}else{?>
										<button class="btn_profile" id="item_img_0<?=$idx?>" value="<?=$idx?>"><span>기본 프로필 이미지1 선택</span></button>
									<?}?>
								</div>
							</div>
							<?if($fp_flag == 1 && $img_buy_arr[$idx] == ''){?>
								<button class="btn_prof_lock"><span>닫힘</span></button>
							<?}?>
						</li>
					<?}?>
				</ul>
			</div>
			<div class="tl_btn">
				<button id="tl_profile_bt"><span>적용</span></button>
			</div>
		</div>
	</div>

	<?
		//아이템 레이어
		include $home_dir . "/layer/item_img_buy.php";
	?>

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
