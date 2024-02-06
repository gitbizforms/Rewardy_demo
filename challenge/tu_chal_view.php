<?php
	//header페이지
	$home_dir = str_replace( basename(__DIR__) , "" , __DIR__ );
	//define('DB_CHARSET', 'utf8mb4');
	include $home_dir . "/inc_lude/header.php";
	// include $home_dir . "/inc_lude/header_index.php";
	// include $home_dir . "/challenge/challenges_header.php";
	$member_info = member_row_info($user_id);
	$tuto_flag = $member_info['t_flag'];	

	$tuto = tutorial_chk();
	// if($tuto['t_flag']>4){
	// 	alert('해당 단계는 이미 완료하셨습니다!');
	// 	echo "<script>history.back();</script>";
	// }else 
	if($tuto['t_flag']<4){
		alert('이전 단계를 먼저 수행해주세요.');
		echo "<script>history.back();</script>";
	}


	//로그인아이디
	//echo $user_id;

	$today_d = date("Y.m.d");
	$today_m = date("Y.m.d",strtotime("+1 months"));

	$template = '0';

?>


<!-- <link href="https://stackpath.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css" rel="stylesheet"> -->
<script src="https://stackpath.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
<link href="/editor/summernote/summernote-lite.css<?php echo VER;?>" rel="stylesheet">
<script src="/js/tutorial_common.js<?php echo VER;?>"></script>
<script src="/editor/summernote/summernote-lite.js<?php echo VER;?>"></script>
<script src="/editor/summernote/lang/summernote-ko-KR.min.js<?php echo VER;?>"></script>
<script>
	$('#chall_contents').summernote('fontName', 'Noto Sans KR');
	$('#chall_contents').summernote('fontSize', '12');
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

			
			tuto_flag = $("#tutorial_flag").val();
			if(tuto_flag > 4){
				fdata.append("not_reward","1");
			}
			
			$.ajax({
				type: "POST",
				data: fdata,
				contentType: false,
				processData: false,
				url: url,
				success: function(data){
					console.log(data);
					if(data == "complete"){
						$(".tuto_mark_01_01").hide();
						$(".tuto_pop_01_01").hide();
						$(".phase_05").removeClass("tuto_on");
						$(".phase_05").addClass("tuto_clear");
						if(tuto_flag==4){
							$(".phase_06").addClass("tuto_on");
						}
						$(".phase_06 button").attr("onclick","location.href='/team/tu_team.php'");
						$(".tuto_phase").css("display","block");
					}
				}

			});

		}

		$(document).on("click","#cha_lo",function(){
			location.href = "/challenge/tu_chall.php";
		});

		
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

		function save_end(){
			location.href = "/team/index.php";
		}
	</script>
<style>
	
	@import url(//fonts.googleapis.com/earlyaccess/nanumgothic.css);
	.nanumgothic * {
		font-family: 'Nanum Gothic';
	}

	.img-box { border:1px solid; padding:10px; width:200px;height:120px; }

	.remove_img_preview {
		position:relative;
		top:-25px;
		right:5px;
		background:black;
		color:white;
		border-radius:50px;
		font-size:0.9em;
		padding: 0 0.3em 0;
		text-align:center;
		cursor:pointer;
	}

	.thumb {
		width: 100%;
		height: 100%;
		margin: 0.2em -0.7em 0 0;
	}

	.note-editable p {
		margin: 0;
	}

	.note-editable hr {
		border: 1px solid #c1c1c1;
	}
	
</style>

<div class="rew_tutorial_deam"></div>
<div class="tuto_mark tuto_mark_01_01"><button><span></span></button></div>
<div class="tuto_pop tuto_pop_01_01">
	<div class="tuto_in">
		<div class="tuto_tit">챌린지 도전</div>
		<div class="tuto_pager">1/1</div>
		<div class="tuto_desc">
			<p>도전하기를 클릭해서 챌린지에 도전할 수 있어요</p>
			<p>인증파일을 등록하거나, 메시지를 남기면 챌린지 도전이 완료돼요.</p>
			<p>도전 완료 즉시 코인이 지급돼요.</p>
		</div>
		<div class="tuto_btns">
			<button class="tuto_prev" onclick="page_loc('chal_prev');"><span>이전</span></button>
			<button class="tuto_next" onclick="tu_end('c_end');"><span>완료</span></button>
		</div>
	</div>
</div>
<div class="rew_warp">
	<div class="rew_warp_in">
		<div class="rew_box">
			<div class="rew_box_in">
				<input type="hidden" value="<?=$tuto_flag?>" id="tutorial_flag">
				<?include $home_dir . "/inc_lude/header_new.php";?>
				<!-- menu -->
				<? include $home_dir . "/inc_lude/menu_party_view_index.php";?>
				<div class="rew_conts">
					<div class="rew_conts_in" id="rew_conts_in">
						<div class="rew_conts_scroll_06">
							<div class="rew_cha_view">
								<div class="rew_cha_view_in">
									<div class="rew_cha_view_box">
										<div class="rew_cha_view_header">
											<div class="rew_cha_view_header_in">
												<div class="view_left">
													<div class="view_title">[보안] 해킹 방지의 첫 걸음! 윈도우 업데이트인걸 아시나요?</div>
													<div class="view_coin">
														<strong>500</strong>
														<span>코인</span>
													</div>
													<div class="view_info">
														<ul>
															<li><span>하루 1회</span></li>
															<li><span>최대 1회</span></li>
															<?if($holiday_chk){?>
															<li><span>공휴일제외</span></li>
															<?}?>
															<li><span>역량 UP</span></li>
															<li><span class="view_date"><?=$today_d?> ~ <?=$today_m?></span></li>
														</ul>
													</div>
												</div>
												<div class="view_right">
													<div class="view_user">
														<ul>
															<li><button><img src="/html/images/pre/ico_user_001.png" alt="" /></button></li>
															<li><button><img src="/html/images/pre/ico_user_002.png" alt="" /></button></li>
															<li><button><img src="/html/images/pre/ico_user_003.png" alt="" /></button></li>
															<li><button><span>+ 117</span></button></li>
														</ul>
													</div>
													<div class="view_writer">
														<span>editor.</span>
														<strong><?=$name?><?=$chall_editor?"(".$chall_editor.")":""?></strong>
													</div>
												</div>
											</div>
										</div>

										<div class="rew_cha_view_editor">
											<div class="rew_cha_view_editor_in">
											✏️챌린지 참여방법<br>
												1. Windows 업데이트를 검색하고 업데이트 확인합니다.<br>
												▶ 설정 > Windows 업데이트 설정 > 업데이트 확인<br>
												2. 오늘까지 정상적인 업데이트를 진행한 후 최신 상태임을 캡처하고 업로드해 주세요
											</div>
										</div>

										<input type="hidden" id="view_idx" value="<?=$idx?>">
										<input type="hidden" id="service">
										<input type="hidden" id="attend_type" value="<?=$ch_info['attend_type']?>">

										

										<?if($file_info['idx']){?>
											<div class="rew_cha_view_file">
												<div class="rew_cha_view_file_in">
													<div class="title_area">
														<strong class="title_main">첨부파일을 확인하세요!</strong>
													</div>
													<ul>
														<?for($i=0; $i<count($file_info['idx']); $i++){?>
														<li>
															<div class="file_box">
																<div class="file_desc" id="file_down<?=$i?>" value="<?=$i?>">
																	<span><?=$file_info['file_real_name'][$i]?></span>
																	<strong>다운로드</strong>
																</div>
															</div>
														</li>
														<?}?>
														<!-- <li>
															<div class="file_box">
																<div class="file_desc">
																	<span>인사챌린지_참고2222222.ppt</span>
																	<strong>다운로드</strong>
																</div>
															</div>
														</li> -->
													</ul>
												</div>
											</div>
										<?}?>

										<?if($img_info['idx']){?>
											<div class="rew_cha_view_img">
												<div class="rew_cha_view_img_in">
													<div class="title_area">
														<strong class="title_main">[인증샷 예시] 이렇게 찍어주세요!</strong>
													</div>
													<ul>
													<?for($i=0; $i<count($img_info['idx']); $i++){
												
														$resize = $img_info['resize'][$i];
														if($resize == '0'){
															$file_path = $img_info['file_ori_path'][$i];
															$file_name = $img_info['file_ori_name'][$i];
														}else{
															$file_path = $img_info['file_ori_path'][$i];
															//$file_path = $img_info['file_path'][$i];
															$file_name = $img_info['file_name'][$i];
														}
														$file_real_name = $file_path.$file_name;
													?>
														<li>
															<div class="file_box">
																<div class="file_desc">
																	<span><img src="<?=$file_real_name?>" alt="" /></span>
																</div>
															</div>
														</li>
													<?}?>
													</ul>
												</div>
											</div>
										<?}?>
									</div>
									<?
									//챌린지 샘플인경우
									if($template =='1'){?>
										<div class="cha_view_btn">
											<button class="btn_white" id="btn_back_list">이전</button>
											<!-- <button class="btn_gray" id="">숨기기 OFF</button> -->
											<?if($template_auth || $ch_info['email'] == $user_id){?>

												<?if($view_flag == '1'){?>
													<button class="btn_ok" id="view_hide">숨기기 ON</button>
												<?}else{?>
													<button class="btn_gray" id="view_hide">숨기기 OFF</button>
												<?}?>
												<button class="btn_gray" id="chall_delete">삭제하기</button>
												<button class="btn_black" id="chall_edit">수정하기</button>
											<?}?>

											<?if($template_auth || $template_use_auth){?>
												<button class="btn_ok" id="template_btn"><span>사용하기</span></button>
											<?}?>
										</div> 

									<?}else if($template == "0"){
									//챌린지 샘플이 아닌경우
										//인증메시지형
										if(in_array($ch_info['attend_type'], array(1))){?>
											<div class="rew_cha_view_masage">
												<div class="rew_cha_view_masage_in" id="view_masage_in">
													<div class="title_area">
														<strong class="title_main">인증 메시지</strong>
														<span class="title_point"><?=$list_cnt?></span>
														<?/*<a href="#" class="title_more"><span>더보기</span></a>*/?>
													</div>
													<div class="masage_zone">
														<?
														if ($masage_list_ymd_top3){
															$k=0;
															for($i=0; $i<count($masage_list_ymd_top3); $i++){
																$date_ymd = trim($masage_list_ymd_top3[$i]);
															?>
																<div class="masage_date">
																	<span><?=$masage_list_top3[$date_ymd][$k]['md']?> <?=$masage_list_top3[$date_ymd][$k]['yoil']?></span>
																</div>
																<?for($j=0; $j<count($masage_list_top3[$date_ymd]); $j++){

																	$chall_masage_email = $masage_list_top3[$date_ymd][$k]['email'];
																	$profile_main_img_src = profile_img_info($chall_masage_email);
																?>

																<div class="masage_area">
																	<div class="masage_img" style="background-image:url('<?=$profile_main_img_src?>');"></div>
																	<div class="masage_info">
																		<div class="masage_user">
																			<strong><?=$masage_list_top3[$date_ymd][$k]['name']?></strong>
																			<span><?=$masage_list_top3[$date_ymd][$k]['part']?></span>
																		</div>
																		<div class="masage_box">
																			<p class="masage_txt"><?=$masage_list_top3[$date_ymd][$k]['comment']?></p>
																			<span class="masage_time"><?=$masage_list_top3[$date_ymd][$k]['hi']?></span>

																			<?if($user_id!=$masage_list_top3[$date_ymd][$k]['email']){?>
																				<button class="masage_jjim<?=$work_like_list[$masage_list_top3[$date_ymd][$k]['idx']]>0?" on":""?>" id="masage_jjim" value="<?=$masage_list_top3[$date_ymd][$k]['idx']?>"><span>좋아요</span></button>
																			<?}?>
																		</div>

																		<?/* if($masage_list_top3[$date_ymd][$k]['state']=="2"){?>
																			<div class="masage_warning">
																				<span>무효 후 코인 회수 처리되었습니다.</span>
																			</div>
																		<?}*/?>

																	</div>
																</div>
																<?
																$k++;
																}
															}?>

														

														<?}else{?>

														<div class="rew_none">
															등록된 인증 메시지가 없습니다.
														</div>

														<?}?>
													</div>
												</div>

												<?if ($masage_list_ymd_top3){?>
													<div class="rew_cha_more" id="masage_more">
														<button><span>more</span></button>
													</div>
												<?}?>

											</div>
										<?}?>

										<?//인증파일형
										if(in_array($ch_info['attend_type'], array(2))){?>
											<div class="rew_cha_view_result">
												<div class="rew_cha_view_result_in">
													<div class="title_area">
														<strong class="title_main">인증 파일</strong>
														<span class="title_point"><?=$chamyeo_file_cnt?></span>
														<?/*<a href="#" class="title_more"><span>더보기</span></a>*/?>
													</div>

													<?if($chall_file_info['idx']){?>

														<ul>
															<?for($i=0; $i<5; $i++){
																$chall_file_idx = $chall_file_info['idx'][$i];
																$chall_file_type = $chall_file_info['file_type'][$i];

																if($chall_file_info['resize'][$i]=='0'){
																	$chall_file_path = $chall_file_info['file_ori_path'][$i];
																	$chall_file_name = $chall_file_info['file_ori_name'][$i];
																}else{
																	$chall_file_path = $chall_file_info['file_ori_path'][$i];
																	//$chall_file_path = $chall_file_info['file_path'][$i];
																	$chall_file_name = $chall_file_info['file_name'][$i];
																}

																$file_url = $chall_file_path . $chall_file_name;

																if($chall_file_type){
																	if (@in_array($chall_file_type, $image_type_array)){?>
																		<li>
																			<button>
																				<img src="<?=$file_url?>" />
																				<span>더보기</span>
																			</button>
																		</li>
											
																	<?}else{?>

																		<li>
																			<button>
																				<img src="/html/images/pre/ico_list_file.png" alt="" />
																				<span>더보기</span>
																			</button>
																		</li>
																	<?}
																}
														
															}?>
														</ul>

														

													<?}else{?>
														<div class="rew_none">
															등록된 인증 파일이 없습니다.
														</div>
													<?}?>
												</div>

												<?if($chall_file_info['idx']){?>
													<div class="rew_cha_more" id="file_more">
														<button><span>more</span></button>
													</div>
												<?}?>

											</div>
										<?}?>


										<?//혼합형
										if(in_array($ch_info['attend_type'], array(3))){?>

											<div class="rew_cha_view_mix">
												<div class="rew_cha_view_mix_in" id="rew_cha_view_mix_in">
													<div class="title_area">
														<strong class="title_main">인증파일 + 인증메시지</strong>
														<span class="title_point"><?=$chamyeo_file_cnt?></span>
														<?/*<a href="#" class="title_more" ><span>더보기</span></a>*/?>
													</div>
													<div class="mix_zone">
	
													<?if ($view_list_ymd_top3){

														$k=0;
														for($i=0; $i<count($view_list_ymd_top3); $i++){
															$date_ymd = trim($view_list_ymd_top3[$i]);
														?>
															<div class="mix_date">
																<span><?=$view_list_top3[$date_ymd][$k]['com_md']?> <?=$view_list_top3[$date_ymd][$k]['com_yoil']?></span>
															</div>

															<?for($j=0; $j<count($view_list_top3[$date_ymd]); $j++){
																
																$chall_mix_email = $view_list_top3[$date_ymd][$k]['email'];
																$profile_main_img_src = profile_img_info($chall_mix_email);

																?>
																<div class="mix_area">
																	<div class="mix_img" style="background-image:url('<?=$profile_main_img_src?>');"></div>
																	<div class="mix_info">
																		<div class="mix_user">
																		<strong><?=$view_list_top3[$date_ymd][$k]['name']?></strong>
																			<span><?=$view_list_top3[$date_ymd][$k]['part']?></span>
																		</div>
																		<div class="mix_box">
																			<p class="mix_txt"><?=$view_list_top3[$date_ymd][$k]['comment']?></p>
																			<span class="mix_time"><?=$view_list_top3[$date_ymd][$k]['com_hi']?></span>
																			
																			<?if($user_id!=$view_list_top3[$date_ymd][$k]['email'] && $user_id=='qohse@nate.com' || $user_id=='sadary0@nate.com'){?>
																				<button class="mix_jjim<?=$work_like_list[$view_list_top3[$date_ymd][$k]['idx']]>0?" on":""?>" id="mix_jjim" value="<?=$view_list_top3[$date_ymd][$k]['idx']?>"><span>좋아요</span></button>
																			<?}?>
																			
																		</div>
																	</div>
																	
																	<?if($view_list_top3[$date_ymd][$k]['files']){?>
																		<div class="mix_imgs">
																			<div class="mix_imgs_box">
																				<img src="<?=$view_list_top3[$date_ymd][$k]['files']?>" alt=""/>
																			</div>
																		</div>
																	<?}?>
																</div>
															<?
															$k++;
															}
														}
													}?>

														

													</div>
												</div>

												<?if ($view_list_ymd_top3){?>
													<div class="rew_cha_more" id="mix_more">
														<button><span>more</span></button>
													</div>
												<?}?>

											</div>
										<?}?>

										<div class="cha_view_btn">
											<button class="btn_white" id="btn_back_list">이전</button>
											<button class="btn_ok on tuto tuto_01_01" id="btn_challenge">도전하기</button>
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


	
	

	<div class="layer_result" style="display:none;">
		<div class="layer_deam"></div>
		<div class="layer_result_in">
			<div class="layer_result_box">
				<div class="layer_result_left">
					<div class="layer_result_search">
						<div class="layer_result_search_box">
							<input type="text" class="input_search" id="input_userfile" placeholder="이름, 부서명을 검색" />
							<button id="file_search_bt"><span>검색</span></button>
						</div>
					</div>

					<div class="layer_result_user">
						<div class="layer_result_user_in">
							<ul>

								<li>
									<button class="on" value="all">
										<div class="user_img" style="background-image:url('/html/images/pre/ico_user_all.png');"></div>
										<div class="user_name">
											<strong>전체</strong>
										</div>
										<span class="user_num<?=($user_file_cnt > 0)?"":"user_num_0"?>">
											<span><?=$user_file_cnt?></span>
										</span>
									</button>
								</li>
								
								<?for($i=0; $i<count($user_info['idx']); $i++){
									$user_list_cnt = $user_file_count[$user_info['email'][$i]];
									if($user_list_cnt > 0){
										$user_num = number_format($user_list_cnt);
										$user_num_class = "";
									}else{
										$user_num = "0";
										$user_num_class = " user_num_0";
									}

									//프로필 캐릭터,사진
									$profile_main_img_src = profile_img_info($user_info['email'][$i]);

									?>
									<li>
										<button value="<?=$user_info['email'][$i]?>">
											<?=($user_id == $user_info['email'][$i])?"<img src=\"/html/images/pre/ico_me.png\" alt=\"\" class=\"user_me\" />":"";?>
											<div class="user_img" style="background-image:url('<?=$profile_main_img_src?>');"></div>
											<div class="user_name">
												<strong><?=$user_info['name'][$i]?></strong>
												<?=$user_info['part'][$i]?>
											</div>
											<span class="user_num<?=$user_num_class?>">
												<span><?=$user_num?>
											</span>
											</span>
										</button>
									</li>
								<?}?>

							</ul>
						</div>
					</div>
				</div>

				<div class="layer_result_right" id="file_zone_list2">
					<div class="layer_close">
						<button><span>닫기</span></button>
					</div>
					<div class="layer_result_top">
						<strong>인증 파일</strong>
					</div>
					<div class="layer_result_list" style="opacity:1">
						<div class="layer_result_list_in" id="file_zone_list">
							<div class="list_function">
								<div class="list_function_in">
									<div class="list_function_left">
										전체 <span></span><strong><?=$chamyeo_file_cnt?></strong>
									</div>
									<div class="list_function_right">
										<div class="list_function_sort">
											<div class="list_function_sort_in">
		
											
											
												<input type="hidden" id="user_email" value="all">
												<input type="hidden" id="user_date" value="all">
												<button class="btn_sort_on" id="auth_file_date" value="all"><span>전체보기</span></button>
												<ul>
													<li><button id="file_reg0" value="all"><span>전체보기</span></button></li>
													<?for($i=0; $i<count($date_file_info['ymd']); $i++){?>
														<li><button id="file_reg<?=($i+1)?>" value="<?=$date_file_info['ymd'][$i]?>"><span><?=$date_file_info['ymd'][$i]?></span></button></li>
													<?}?>
												</ul>
											</div>



										</div>
										<div class="list_function_type">
											<button class="type_list"><span>리스트형</span></button>
											<button class="type_img on"><span>이미지형</span></button>
										</div>
									</div>
								</div>
							</div>


							<?if($chall_file_list_ymd){?>
								<div class="list_area" id="list_area_auth">
									<div class="list_area_in">

									<?	$k=0;
										for($i=0; $i<count($chall_file_list_ymd); $i++){
											$date_ymd = trim($chall_file_list_ymd[$i]);
									?>

											<div class="list_box">
												<div class="list_date">
													<span><?=$chall_file_list[$date_ymd][$k]['reg']?> <?=$chall_file_list[$date_ymd][$k]['yoil']?></span>
												</div>


												<div class="list_conts type_img">
													<ul class="list_ul">

													<?for($j=0; $j<count($chall_file_list[$date_ymd]); $j++){
														
														$files_name = $chall_file_list[$date_ymd][$k]['file_real_name'];
														$files_url = $chall_file_list[$date_ymd][$k]['files'];

														$files_name_ext = array_pop(explode(".", strtolower($files_name)));
														$files_full_name = current(explode('.', $files_name)); 

													?>
														<li>
															<div class="list_thumb_wrap<?=($chall_file_list[$date_ymd][$k]['state']=="2")?" lrt_none":""?>" id="file_list_chk<?=$k?>" value="<?=$chall_file_list[$date_ymd][$k]['idx']?>">
																<div class="list_thumb">
																	<?
																	//이미지일때
																	if (@in_array($chall_file_list[$date_ymd][$k]['file_type'], $image_type_array)){?>
																			<div class="list_thumb_img" id="list_thumb_img">
																				<img src="<?=$files_url?>" alt="" />
																			</div>
																		<?if($chall_file_list[$date_ymd][$k]['state']=="2"){?>
																			<div class="list_thumb_none">
																				<span></span>
																				<strong>취소상태</strong>
																			</div>
																			<div class="list_thumb_cover">
																				<span></span>
																				<button class="list_thumb_select"><strong>선택하기</strong></button>
																				<button class="list_thumb_preview" value="<?=$chall_file_list[$date_ymd][$k]['idx']?>"><strong>미리보기</strong></button>
																			</div>
																		<?}else{?>
																			<div class="list_thumb_cover">
																				<span></span>
																				<button class="list_thumb_select"><strong>선택하기</strong></button>
																				<button class="list_thumb_preview" value="<?=$chall_file_list[$date_ymd][$k]['idx']?>"><strong>미리보기</strong></button>
																			</div>
																		<?}?>
																	<?}else{?>
																		<div class="list_thumb_cover">
																			<span></span>
																			<button class="list_thumb_select"><strong>선택하기</strong></button>
																			<button class="list_thumb_preview" value="<?=$chall_file_list[$date_ymd][$k]['idx']?>"><strong>미리보기</strong></button>
																		</div>
																	<?}?>
																</div>
																<div class="list_desc">
																	<div class="list_title">
																		<strong><?=$files_full_name?>.</strong>
																		<span><?=$files_name_ext?></span>
																		<?if($user_id!=$chall_file_list[$date_ymd][$k]['email']){?>
																			<button class="masage_jjim<?=$work_like_list[$chall_file_list[$date_ymd][$k]['idx']]>0?" on":""?>" id="file_jjim" value="<?=$chall_file_list[$date_ymd][$k]['idx']?>"><span>좋아요</span></button>
																		<?}?>
																	</div>
																	<div class="list_user">
																		<span><?=$chall_file_list[$date_ymd][$k]['name']?> <?=$chall_file_list[$date_ymd][$k]['part']?></span>
																	</div>
																</div>
																<div class="list_time">
																	<span><?=$chall_file_list[$date_ymd][$k]['hi']?></span>
																</div>
															</div>
														</li>
													<?
													$k++;
													}
													?>
													</ul>
												</div>
											</div>
										<?}?>
									</div>
								</div>
							<?}else{?>
								<div class="list_area_none">
									<strong>등록된 인증 파일이 없습니다.</strong>
								</div>
							<?}?>

						</div>
					</div>
					<div class="layer_result_btns">
						<div class="layer_result_btns_in">
							<div class="btns_left" id="file_list_sel" style="display:none;">
								<button class="btns_cancel" id="file_sel_cancel"><span>취소</span></button>
								<strong>5 개 선택</strong>
							</div>
							<div class="btns_right">

								<?//if($ch_info['email'] == $user_id){?>
									<button class="btns_del" id="file_user_del" style="display:none;"><span>삭제</span></button>
								<?//}?>

								<button class="btns_down"><span>다운로드</span></button>

								<?if($ch_info['email'] == $user_id){?>
									<button class="btns_coin" id="file_user_dcoin" style="display:none;"><span>무효 후 코인 회수</span></button>
									<button class="btns_re_coin" id="file_user_rcoin" style="display:none;"><span>코인 다시 지급</span></button>
								<?}?>

							</div>
						</div>
					</div>
				</div>

			</div>
		</div>
	</div>


	<div class="layer_masage" style="display:none;">
		<div class="layer_deam"></div>
		<div class="layer_result_in">
			<div class="layer_result_box">
				<div class="layer_result_left">
					<div class="layer_result_search">
						<div class="layer_result_search_box">
							<input type="text" class="input_search" id="input_masage" placeholder="이름, 부서명을 검색" />
							<button id="masage_search_bt"><span>검색r</span></button>
						</div>
					</div>

					<div class="layer_result_user">
						<div class="layer_result_user_in">
							<ul>
								<li>
									<button class="on" value="all">
										<div class="user_img">
											<img src="/html/images/pre/ico_user_all.png" alt="" />
										</div>
										<div class="user_name">
											<strong>전체</strong>
										</div>
										<span class="user_num<?=($user_masage_cnt > 0)?"":"user_num_0"?>">
											<span><?=$user_masage_cnt?></span>
										</span>
									</button>
								</li>

								<?for($i=0; $i<count($user_info['idx']); $i++){
									$user_list_cnt = $user_list_count[$user_info['email'][$i]];
									if($user_list_cnt > 0){
										$user_num = number_format($user_list_cnt);
										$user_num_class = "";
									}else{
										$user_num = "0";
										$user_num_class = " user_num_0";
									}
									$profile_main_img_src = profile_img_info($user_info['email'][$i]);

									?>
								<li>
									<button value="<?=$user_info['email'][$i]?>">
										<?=($user_id == $user_info['email'][$i])?"<img src=\"/html/images/pre/ico_me.png\" alt=\"\" class=\"user_me\" />":"";?>
										<div class="user_img" style="background-image:url('<?=$profile_main_img_src?>');"></div>
										<div class="user_name">
											<strong><?=$user_info['name'][$i]?></strong>
											<?=$user_info['part'][$i]?>
										</div>
										<span class="user_num<?=$user_num_class?>">
											<span><?=$user_num?>
										</span>
										</span>
									</button>
								</li>
								<?}?>
							</ul>
						</div>
					</div>
				</div>

				<div class="layer_result_right">
					<div class="layer_close">
						<button><span>닫기</span></button>
					</div>
					<div class="layer_result_top">
						<strong>인증 메시지</strong>
					</div>
					<div class="layer_result_list" style="opacity:1">
						<div class="layer_result_list_in" id="masage_zone_list">
							<div class="list_function">
								<div class="list_function_in">
									<div class="list_function_left">
										전체 <span></span><strong><?=$user_masage_cnt?></strong>
									</div>
									<div class="list_function_right">
										<div class="list_function_sort">
											<div class="list_function_sort_in">
												<input type="hidden" id="user_email" value="all">
												<input type="hidden" id="user_date" value="all">
												<button class="btn_sort_on" id="auth_masage_date"><span>전체보기</span></button>
												<ul>
													<li><button id="comment_reg0" value="all"><span>전체보기</span></button></li>
													<?for($i=0; $i<count($date_masage_info['ymd']); $i++){?>
														<li><button id="comment_reg<?=($i+1)?>" value="<?=$date_masage_info['ymd'][$i]?>"><span><?=$date_masage_info['ymd'][$i]?></span></button></li>
													<?}?>
												</ul>
											</div>
										</div>
									</div>
								</div>
							</div>

							<?
							if($masage_list_ymd){?>
								<div class="masage_zone">
									<div class="masage_zone_in">
									<?
										$k=0;
										for($i=0; $i<count($masage_list_ymd); $i++){
												$date_ymd = trim($masage_list_ymd[$i]);
										?>
											<div class="masage_date">
												<span><?=$masage_list[$date_ymd][$k]['md']?> <?=$masage_list[$date_ymd][$k]['yoil']?></span>
											</div>

											<?
											for($j=0; $j<count($masage_list[$date_ymd]); $j++){
												
												$chall_masage_email = $masage_list[$date_ymd][$k]['email'];
												
												if ($user_id == $chall_masage_email){
													$div_class = "masage_area_in";
												}else{
													$div_class = "";
												}

												$profile_main_img_src = profile_img_info($chall_masage_email);
											?>
												<div class="masage_area">
													<div class="masage_area_in<?=($masage_list[$date_ymd][$k]['state']=="2")?" chk_none":""?>" id="masage_list_chk<?=$k?>" value="<?=$masage_list[$date_ymd][$k]['idx']?>">
														<div class="masage_img" style="background-image:url('<?=$profile_main_img_src?>');">

															<?//if($user_id == $masage_list[$date_ymd][$k]['email']){?>
																<strong class="masage_chk"><span>선택</span></strong>
															<?//}?>

														</div>
														<div class="masage_info">
															<div class="masage_user">
																<strong><?=$masage_list[$date_ymd][$k]['name']?></strong>
																<span><?=$masage_list[$date_ymd][$k]['part']?></span>
															</div>
															<div class="masage_box">
																<p class="masage_txt"><?=$masage_list[$date_ymd][$k]['comment']?></p>
																<span class="masage_time"><?=$masage_list[$date_ymd][$k]['hi']?></span>
																<?if($user_id!=$masage_list[$date_ymd][$k]['email'] && $user_id=='qohse@nate.com' || $user_id=='sadary0@nate.com'){?>
																	<button class="masage_jjim<?=$work_like_list[$masage_list[$date_ymd][$k]['idx']]>0?" on":""?>" id="masage_jjim" value="<?=$masage_list[$date_ymd][$k]['idx']?>"><span>좋아요</span></button>
																<?}?>
															</div>

															<? if($masage_list[$date_ymd][$k]['state']=="2"){?>
																<div class="masage_warning">
																	<span>무효 후 코인 회수 처리되었습니다.</span>
																</div>
															<?}?>

														</div>
													</div>
												</div>
											<?
											$k++;
											}
										}
									?>
									</div>
								</div>

							<?}else{?>
								<div class="masage_area_none">
									<strong>등록된 메시지가 없습니다.</strong>
								</div>
							<?}?>

						</div>
					</div>
					<div class="layer_result_btns" style="display:none;">
						<div class="layer_result_btns_in">
							<div class="btns_left" id="masage_list_sel">
								<button class="btns_cancel" id="masage_sel_cancel"><span>취소</span></button>
								<strong>5 개 선택</strong>
							</div>
							<div class="btns_right">
								<button class="btns_del" id="masage_user_del" style="display:none;"><span>삭제</span></button>

								<?if($ch_info['email'] == $user_id){?>
									<button class="btns_coin" id="masage_user_dcoin" style="display:none;"><span>무효 후 코인 회수</span></button>
									<button class="btns_re_coin" id="masage_user_rcoin" style="display:none;"><span>코인 다시 지급</span></button>
								<?}?>
							</div>
						</div>
					</div>
				</div>

			</div>
		</div>
	</div>
	
	

	<div class="layer_mix" style="display:none;">
		<div class="layer_deam"></div>
		<div class="layer_result_in">
			<div class="layer_result_box">
				<div class="layer_result_left">
					<div class="layer_result_search">
						<div class="layer_result_search_box">
							<input type="text" class="input_search" id="input_mix" placeholder="이름, 부서명을 검색" />
							<button id="mix_search_bt"><span>검색</span></button>
						</div>
					</div>

					<div class="layer_result_user">
						<div class="layer_result_user_in">
							<ul>
								<li>
									<button class="on" value="all">
										<div class="user_img" style="background-image:url('/html/images/pre/img_prof_default.png');"></div>
										<div class="user_name">
											<strong>전체</strong>
										</div>
										<span class="user_num">
											<span><?=$user_masage_cnt?></span>
										</span>
									</button>
								</li>

								<?for($i=0; $i<count($user_info['idx']); $i++){
									$user_list_cnt = $user_file_count[$user_info['email'][$i]];
									if($user_list_cnt > 0){
										$user_num = number_format($user_list_cnt);
										$user_num_class = "";
									}else{
										$user_num = "0";
										$user_num_class = " user_num_0";
									}

										//프로필 캐릭터,사진
										$profile_main_img_src = profile_img_info($user_info['email'][$i]);
									?>
									<li>
										<button value="<?=$user_info['email'][$i]?>">
											<?=($user_id == $user_info['email'][$i])?"<img src=\"/html/images/pre/ico_me.png\" alt=\"\" class=\"user_me\" />":"";?>
											<div class="user_img" style="background-image:url('<?=$profile_main_img_src?>');"></div>
											<div class="user_name">
												<strong><?=$user_info['name'][$i]?></strong>
												<?=$user_info['part'][$i]?>
											</div>
											<span class="user_num<?=$user_num_class?>">
												<span><?=$user_num?>
											</span>
											</span>
										</button>
									</li>
								<?}?>
							</ul>
						</div>
					</div>
				</div>

				<div class="layer_result_right">
					<div class="layer_close">
						<button><span>닫기</span></button>
					</div>
					<div class="layer_result_top">
						<strong>인증파일 + 인증메시지</strong>
					</div>
					<div class="layer_result_list" style="opacity:1">
						<div class="layer_result_list_in" id="mix_zone_list">
							<div class="list_function">
								<div class="list_function_in">
									<div class="list_function_left">
										전체 <span></span><strong><?=$list_cnt?></strong>
									</div>
									<div class="list_function_right">
										<div class="list_function_sort">
											<div class="list_function_sort_in">
												<input type="hidden" id="user_email" value="all">
												<input type="hidden" id="user_date" value="all">
												<button class="btn_sort_on"><span>전체보기</span></button>
												<ul>
													<li><button id="mix_reg0" value="all"><span>전체보기</span></button></li>
													<?for($i=0; $i<count($date_mix_info['ymd']); $i++){?>
														<li><button id="mix_reg<?=($i+1)?>" value="<?=$date_mix_info['ymd'][$i]?>"><span><?=$date_mix_info['ymd'][$i]?></span></button></li>
													<?}?>
												</ul>
											</div>
										</div>
									</div>
								</div>
							</div>


							<?if($chall_mix_list_ymd){?>
								<div class="mix_zone">
									<div class="mix_zone_in">

									<?	$k=0;
										for($i=0; $i<count($chall_mix_list_ymd); $i++){
											$date_ymd = trim($chall_mix_list_ymd[$i]);

											$view_date = $chall_mix_list[$date_ymd][$k]['date'];

											$tmp_date = explode("-",$view_date);
											if($tmp_date){
												//$tmp_month = preg_replace('/(0)(\d)/','$2', $tmp_date[1]);
												//$tmp_day = preg_replace('/(0)(\d)/','$2', $tmp_date[2]);
												$tmp_month = (int)$tmp_date[1];
												$tmp_day = (int)$tmp_date[2];

												$real_date = $tmp_date[0] . "년 " .$tmp_month."월 " .$tmp_day."일 ";
											}
										?>

											<div class="mix_date">
												<span><?=$real_date?><?=$chall_mix_list[$date_ymd][$k]['com_yoil']?></span>
											</div>

											<?for($j=0; $j<count($chall_mix_list[$date_ymd]); $j++){
												$idx = $chall_mix_list[$date_ymd][$k]['idx'];

												$chall_mix_email = $chall_mix_list[$date_ymd][$k]['email'];
												

												//프로필 캐릭터,사진
												$profile_main_img_src = profile_img_info($chall_mix_email);

											?>
												<div class="mix_area">
													<div class="mix_area_in<?=($chall_mix_list[$date_ymd][$k]['state']=="2")?" chk_none":""?>" id="mix_list_chk<?=$k?>" value="<?=$chall_mix_list[$date_ymd][$k]['idx']?>">
														<div class="mix_img" style="background-image:url('<?=$profile_main_img_src?>');">
															<button class="mix_chk"><span>선택</span></button>
														</div>
														<div class="mix_info">
															<div class="mix_user">
																<strong><?=$chall_mix_list[$date_ymd][$k]['name']?></strong>
																<span><?=$chall_mix_list[$date_ymd][$k]['part']?></span>
															</div>
															<div class="mix_box">
																<p class="mix_txt"><?=$chall_mix_list[$date_ymd][$k]['comment']?></p>
																<span class="mix_time"><?=$chall_mix_list[$date_ymd][$k]['file_hi']?></span>
																<?if($user_id!=$chall_mix_list[$date_ymd][$k]['email'] && $user_id=='qohse@nate.com' || $user_id=='sadary0@nate.com'){?>
																	<button class="mix_jjim<?=$work_like_list[$chall_mix_list[$date_ymd][$k]['idx']]>0?" on":""?>" id="mix_jjim" value="<?=$chall_mix_list[$date_ymd][$k]['idx']?>"><span>좋아요</span></button>
																<?}?>
															</div>
															<? if($chall_mix_list[$date_ymd][$k]['state']=="2"){?>
																<div class="mix_warning">
																	<span>무효 후 코인 회수 처리되었습니다.</span>
																</div>
															<?}?>
														</div>
														<div class="mix_imgs">
															<div class="mix_imgs_box" id="mix_imgs_box_<?=$idx?>">
																<img src="<?=$chall_mix_list[$date_ymd][$k]['files']?>" alt="" />
															</div>
														</div>
													</div>
												</div>
											<?
											$k++;
											}?>
										<?}?>
									</div>
								</div>
							<?}?>

							<div class="mix_area_none">
								<strong>등록된 메시지가 없습니다.</strong>
							</div>

						</div>
					</div>
					<div class="layer_result_btns" style="display: none;">
						<div class="layer_result_btns_in">
							<div class="btns_left" id="mix_list_sel">
								<button class="btns_cancel"id="mix_sel_cancel" style="display: none;"><span>취소</span></button>
								<strong style="display: none;">5 개 선택</strong>
							</div>
							<div class="btns_right">
								<button class="btns_del" id="mix_user_del" style="display:none;"><span>삭제</span></button>
								<button class="btns_down" id="btns_down" style="display:none;"><span>다운로드</span></button>

								<?if($ch_info['email'] == $user_id){?>
									<button class="btns_coin" id="mix_user_dcoin" style="display:none;"><span>무효 후 코인 회수</span></button>
									<button class="btns_re_coin" id="mix_user_rcoin" style="display:none;"><span>코인 다시 지급</span></button>
								<?}?>
							</div>
						</div>
					</div>
				</div>

			</div>
		</div>
	</div>


	<div class="layer_cha_join join_type_file" style="display:none;">
		<div class="layer_deam"></div>
		<div class="layer_cha_join_in">
			<div class="layer_cha_join_box">
				<div class="layer_cha_join_title">
					<strong>챌린지 참여하기</strong>
					<span>사진 및 파일로 챌린지를 인증하세요!</span>
				</div>
				<div class="layer_cha_join_area">
					<div class="layer_cha_join_file">
						<div class="file_box">
							<input type="file" id="ch_file_01" class="input_file" />
							<input type="hidden" id="input_type_idx">
							<label for="ch_file_01" class="label_file"><span>파일첨부</span></label>
							<div id="ch_file_desc_01">
							<div class="file_desc">
								<span>인사챌린지_참고01.hwp</span>
								<button>삭제</button>
							</div>
							</div>
						</div>
					</div>
				</div>
				<div class="layer_result_btns">
					<div class="layer_result_btns_in">
						<div class="btns_right">
							<button class="btns_cha_cancel"><span>취소</span></button>
							<button class="btns_cha_join"><span>참여하기</span></button>
							<!-- <button class="btns_cha_join on"><span>참여하기</span></button> -->
						</div>
					</div>
				</div>

			</div>
		</div>
	</div>

	<div class="layer_cha_join join_type_masage" style="display:none;">
		<div class="layer_deam"></div>
		<div class="layer_cha_join_in">
			<div class="layer_cha_join_box">
				<div class="layer_cha_join_title">
					<strong>챌린지 참여하기</strong>
					<span>메시지 작성으로 챌린지를 인증하세요!</span>
				</div>
				<div class="layer_cha_join_area">
					<div class="layer_cha_join_input">
						<textarea name="" id="input_type_masage" placeholder="메시지를 작성하세요."></textarea>
						<input type="hidden" id="input_type_idx">
					</div>
				</div>
				<div class="layer_result_btns">
					<div class="layer_result_btns_in">
						<div class="btns_right">
							<button class="btns_cha_cancel"><span>취소</span></button>
							<button class="btns_cha_join"><span>참여하기</span></button>
							<!-- <button class="btns_cha_join on"><span>참여하기</span></button> -->
						</div>
					</div>
				</div>

			</div>
		</div>
	</div>

	<div class="layer_cha_join join_type_mix" style="display:none;">
		<div class="layer_deam"></div>
		<div class="layer_cha_join_in">
			<div class="layer_cha_join_box">
				<div class="layer_cha_join_title">
					<strong>챌린지 참여하기</strong>
					<span>인증 메시지를 작성하고 사진 및 파일을 등록해 챌린지를 인증하세요!</span>
				</div>
				<div class="layer_cha_join_area">
					<div class="layer_cha_join_input">
						<textarea name="" placeholder="메시지를 작성하세요." id="input_type_mix"><?=strip_tags($chall_comment_contents)?></textarea>
						<input type="hidden" id="input_type_idx">
					</div>
					<div class="layer_cha_join_file">
						<div class="file_box">
							<input type="hidden" id="mix_file_name" value="<?=$chall_files_info['file_real_img_name']?>" />
							<input type="file" id="mix_file_01" class="input_file" />
							<label for="mix_file_01" class="label_file"><span>파일첨부</span></label>
							<div id="mix_file_desc_01">
								<div class="file_desc"<?=$chall_files_info['file_real_img_name']?' style="display: block;"':''?>>
									<span><?=$chall_files_info['file_real_img_name']?></span>
									<button <?=$chall_files_info['file_real_img_name']?' id="mix_file_del_01"':''?>>삭제</button>
								</div>
							</div>

						</div>
					</div>
				</div>
				<div class="layer_result_btns">
					<div class="layer_result_btns_in">
						<div class="btns_right">
							<button class="btns_cha_cancel"><span>취소</span></button>
							<button class="btns_cha_join"><span>참여하기</span></button>
							<!-- <button class="btns_cha_join on"><span>참여하기</span></button> -->
						</div>
					</div>
				</div>

			</div>
		</div>
	</div>

	<div class="layer_cha_image" style="display:none;">
		<div class="layer_deam"></div>
		<div class="layer_cha_image_in">
			<div class="layer_cha_image_box">
				<div class="layer_cha_image_box_in">
					<img src="" alt="" id="layer_cha_img"/>
				</div>
			</div>
		</div>
	</div>

	<?php
		//좋아요 레이어
		include $home_dir . "/layer/member_jjim.php";

		//튜토리얼 시작 레이어
		include $home_dir . "/layer/tutorial_main_level.php";
	?>


</div>
	<?php
		include $home_dir . "/inc_lude/footer.php";
	?>
</body>
</html>