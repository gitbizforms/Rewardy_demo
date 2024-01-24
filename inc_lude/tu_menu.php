<?

//회원 전체 정보가져오기
$member_info = member_list_all();
$member_total_cnt = $member_info['total_cnt'];

//부서별 정렬순
$part_info = member_part_info();

?>
<div class="rew_menu">
					<div class="rew_menu_in">
						<div class="rew_bar">
							<?if($user_level == '0'){?>

								<?if(!$member_mail_add_info['idx']){?>
									<!-- <span class="rew_bar_alert"><em>멤버를 먼저 초대하세요.</em></span> -->
								<?}?>
							<?}?>
							<div class="rew_bar_in">
								<div class="rew_bar_logo">
									<a href="javascript:void(0);"><img src="/images/pre/logo.png" alt="" /></a>
								</div>
								<ul>
									<li class="rew_bar_li_01<?=($get_dirname=="todaywork")?" on":"";?>" title="">
										<a href="javascript:void(0);"><strong>오늘업무</strong></a>
									</li>
									<li class="rew_bar_li_02<?=($get_dirname=="live")?" on":"";?>" title="">
										<a href="javascript:void(0);"><strong>실시간 업무</strong></a>
									</li>
									<li class="rew_bar_li_03<?=($get_dirname=="reward")?" on":"";?>" title="">
										<a href="javascript:void(0);"><strong>보상/코인</strong></a>
									</li>
									<li class="rew_bar_li_04<?=($get_dirname=="challenge")?" on":"";?>" title="">
										<a href="javascript:void(0);"><strong>챌린지</strong></a>
									</li>
									<li class="rew_bar_li_05<?=($get_dirname=="party")?" on":"";?>" title="">
										<a href="javascript:void(0);"><strong>파티</strong></a>
									</li>
									<li class="rew_bar_li_06<?=($get_dirname=="insight")?" on":"";?>" title="">
										<a href="javascript:void(0);"><strong>인사이트</strong></a>
									</li>
									
								</ul>
								<div class="rew_bar_setting">

									<!-- <?if(@in_array($user_id, array('sadary0@nate.com','qohse@nate.com','yoonjh8932@naver.com','adsb12@nate.com','ansrkdtks2@naver.com'))){?>
										<a href="/todaywork/tu_works.php" class="rew_bar_setting_03" title="" id="tutorial"><strong>튜토리얼</strong></a>
									<?}?> -->

									<?if($user_level == '0' || $user_id=='qohse@nate.com'){?>
										<a href="/admin/member_list.php" class="rew_bar_setting_02" id="member_add_in" title=""><strong>관리자</strong></a>
									<?}?>

								</div>
							</div>
						</div>


						<?	//좌측메뉴 열기
						if($_COOKIE['onoff'] == '1'){
							$menu_class = "";
						}else{
							$menu_class = "on";
						}

						//라이브 리뉴얼
						if ($get_dirname=="live"){?>

								<div class="rew_mypage_07">
									<div class="rew_mypage_07_in">
										<div class="rew_mypage_title">
											<strong><span>📄</span> 실시간 업무</strong>
										</div>
										<div class="rew_now_section">
											<div class="rew_now_timer on">
												<div class="rew_now_timer_in">
													<span>출근 후 업무시간</span>
													<strong>15<em>:</em>35<em>:</em>27</strong>
												</div>
											</div>
											<div class="rew_now_tab">
												<div class="rew_now_tab_in">
													<ul>
														<li>
															<button><span>업무</span><strong><?=$works_realtime_all?></strong></button>
														</li>
														<li>
															<button><span>보고</span><strong><?=$works_realtime_report?></strong></button>
														</li>
														<li>
															<button><span>요청</span><strong><?=$works_realtime_req?></strong></button>
														</li>
														<li>
															<button><span>공유</span><strong><?=$works_realtime_share?></strong></button>
														</li>
													</ul>
												</div>
											</div>

											<div class="rew_now_comp">
												<div class="rnc_in">
													<div class="rnc_func">
														<div class="rnc_func_tit">
															<span>진행중인 업무<strong><?=count($works_realtime_info['idx'])?></strong></span>
														</div>
													</div>

													<div class="rnc_list">
														<?if($works_realtime_info['idx']){?>
														<ul>
															<?for($i=0; $i<count($works_realtime_info['idx']); $i++){
																
																$works_realtime_idx = $works_realtime_info['idx'][$i];
																$works_realtime_email = $works_realtime_info['email'][$i];
																$works_realtime_name = $works_realtime_info['name'][$i];
																
																$works_realtime_work_flag = $works_realtime_info['work_flag'][$i];
																$works_realtime_work_idx = $works_realtime_info['work_idx'][$i];
																$works_realtime_share_flag = $works_realtime_info['share_flag'][$i];

																$works_realtime_contents = $works_realtime_info['contents'][$i];
																$works_realtime_editdate = $works_realtime_info['editdate'][$i];
																$works_realtime_regdate = $works_realtime_info['regdate'][$i];

																$tmp_ex = @explode(" ", $works_realtime_editdate);
																$complete_time = @substr($tmp_ex[1], 0, 5);

																$tmp_ex2 = @explode(" ", $works_realtime_regdate);
																$complete_time2 = @substr($tmp_ex2[1], 0, 5);

																//요청업무
																if($works_realtime_work_flag==3){
																	if($works_realtime_work_idx){
																		$work_to_name = $work_req_user['receive'][$works_realtime_work_idx];

																		$work_req_user_title = $work_req_user['receive'][$work_idx];

																		$works_realtime_title = "[".$work_to_name ."님에게 요청받음]";
																		$read_all_cnt = $work_req_user['receive_cnt'][$works_realtime_work_idx];
																	}else{
																		if($work_req_user['send_cnt'][$works_realtime_idx] > 1){
																			$work_user_count = $work_req_user['send_cnt'][$works_realtime_idx] - 1;
																			$work_user_req = $work_req_user['send'][$works_realtime_idx][0]. "님 외 ". $work_user_count . "명에게 요청함";
																			$works_realtime_title = "[". $work_user_req. "]";
																		}else{
																			$work_user_req = $work_req_user['send'][$works_realtime_idx][0];
																			$works_realtime_title = "[". $work_user_req. "님에게 요청함]";
																		}
																	}
																//공유업무
																}else if($works_realtime_work_flag==2 || in_array($works_realtime_share_flag, array(1,2))){

																	//공유함
																	if($works_realtime_share_flag==1){
																		if($work_share_user['send_cnt'][$works_realtime_idx] > 1){
																			$work_user_count = $work_share_user['send_cnt'][$works_realtime_idx] - 1;
																			$work_share_title = $work_share_user['send'][$works_realtime_idx][0]. "님 외 ". $work_user_count . "명에게 공유함";
																			$works_realtime_title = "[".$work_share_title. "]";

																		}else{
																			$work_share_title = $work_share_user['send'][$works_realtime_idx][0];
																			$works_realtime_title = "[". $work_share_title. "님에게 공유함]";
																		}
																	//공유받기
																	}else if($works_realtime_share_flag==2){

																		$work_to_name = $work_share_user['receive'][$works_realtime_work_idx];
																		$works_realtime_title = "[".$work_to_name ."님에게 공유받음]";

																	}else{
																		$works_realtime_title ="";
																	}

																}else{

																	$works_realtime_title="";
																}

															?>
																<li id="rnc_list_<?=$i?>" value="<?=$works_realtime_email?>">
																	<a href="javascript:void(0);" class="rnc_box">
																		<div class="rnc_name"><?=$works_realtime_name?></div>
																		<div class="rnc_desc"><span><?=$works_realtime_title?></span><?=$works_realtime_contents?></div>
																		<div class="rnc_time"><?=$complete_time2?></div>
																	</a>
																</li>
															<?}?>
														</ul>
														<?}else{?>
															<div class="rnc_none">
																<span>완료한 업무가 없습니다.</span>
															</div>
														<?}?>
													</div>

												</div>
											</div>
										</div>
									</div>
								</div>

							<?
							/* 2023-03-29 변경함
								<div class="rew_mypage_07">
									<div class="rew_mypage_07_in">
										<div class="rew_mypage_title">
											<strong><span>🤝</span> 함께해요</strong>
										</div>
										<div class="rew_mypage_section">
											<div class="tdw_live_tab">
												<div class="tdw_live_tab_in">
													<ul>
														<li>
															<button class="on" id="project_all"><span>전체 파티</span><em><?=count($project_info['idx'])?></em></button>
														</li>
														<li>
															<button id="project_my"><span>나의 파티</span><em><?=count($project_myinfo['idx'])?></em></button>
														</li>
													</ul>
												</div>
											</div>

											<div class="live_drop_left">
												<div class="ldl_in" id="ldl_in">
													<?

													if ($project_info['idx']){
														for($i=0; $i<count($project_info['idx']); $i++){
															$project_idx = $project_info['idx'][$i];
															$project_title = $project_info['title'][$i];
															$project_user_email = $project_info['email'][$i];
															$project_user_name = $project_info['name'][$i];
															$project_user_part = $project_info['part'][$i];
															$project_user_regdate = $project_info['regdate'][$i];
															$project_user_reg = $project_info['reg'][$i];
															$project_user_rdate = $project_info['rdate'][$i];
															$project_user_edate = $project_info['edate'][$i];

															if($project_user_edate){
																$project_user_edate_tmp = explode("-", $project_user_edate);
																if($project_user_edate_tmp){
																	$pr_user_wdate = $project_user_edate_tmp[1]."/".$project_user_edate_tmp[2];
																}
															}else{
																$project_user_rdate_tmp = explode("-", $project_user_rdate);
																if($project_user_rdate_tmp){
																	$pr_user_wdate = $project_user_rdate_tmp[1]."/".$project_user_rdate_tmp[2];
																}
															}


															if($project_user_reg > 60){
																$ldl_box_time = $pr_user_wdate . " 업데이트";
															}else{
																$ldl_box_time = "최신 업데이트";
															}

														?>
															<div class="ldl_box" id="listsort_<?=$project_idx?>" value="<?=$project_idx?>">
																<div class="ldl_box_in">

																	<?if($user_id == $project_part_info_auth[$project_idx]){?>
																		<button class="ldl_box_close" id="ldl_box_close_all_<?=$project_idx?>" value="<?=$project_idx?>">닫기</button>
																	<?}?>

																	<div class="ldl_box_tit">
																		<p><?=$project_info['title'][$i]?></p>
																		<div class="ldl_box_tit_regi" style="display:none">
																			<textarea name="" class="ldl_textarea_regi"><?=$project_info['title'][$i]?></textarea>
																			<div class="ldl_btn_regi_box">
																				<button class="ldl_btn_regi_submit"><span>확인</span></button>
																				<button class="ldl_btn_regi_cancel"><span>취소</span></button>
																			</div>
																		</div>

																	</div>
																	<div class="ldl_box_time" id="ldl_box_time_<?=$project_idx?>"><?=$ldl_box_time?></div>
																	<div class="ldl_box_user">
																		<ul>
																			<?
																			$part_out = false;
																			for($j=0; $j<count($project_user_list[$project_idx]['email']); $j++){

																				$project_user_list_email = $project_user_list[$project_idx]['email'][$j];
																				$project_user_list_profile_img = profile_img_info($project_user_list_email);

																				if($user_id==$project_user_list_email){
																					//$part_out = true;
																					$li_class = ' class="ldl_me"';
																				}else{
																					$li_class = '';
																				}

																			?>
																				<li <?=$li_class?>>
																					<div class="ldl_box_img" style="background-image:url(<?=$project_user_list_profile_img?>)" title="<?=$project_user_list[$project_idx]['name'][$j]?>"></div>
																					<div class="ldl_box_user">
																						<strong><?=$project_user_list[$project_idx]['name'][$j]?></strong>
																						<span><?=$project_user_list[$project_idx]['part'][$j]?></span>
																					</div>
																				</li>
																			<?}?>
																		</ul>
																	</div>

																	<?//접속한 아이디 != 파티생성한 아이디
																	if($user_id!=$project_user_email && in_array($user_id , $project_user_list['use'][$project_idx])){?>
																		<button class="ldl_box_out" id="ldl_box_out_<?=$project_idx?>" value="<?=$project_idx?>">
																			<span>파티에서 나가기</span>
																		</button>
																	<?}else{?>
																		<button class="ldl_box_out" id="ldl_box_out_<?=$project_idx?>" value="<?=$project_idx?>" style="display:none;">
																			<span>파티에서 나가기</span>
																		</button>
																	<?}?>
																</div>
															</div>
														<?}?>
													<?}else{?>
														<div class="ldl_list_none">
															<strong><span>현재 생성된 파티가 없습니다.</span></strong>
														</div>
													<?}?>
												</div>


												<div class="ldl_in" id="ldl_in_my" style="display:none;">

													<?
													if($project_myinfo['idx']){

														for($i=0; $i<count($project_myinfo['idx']); $i++){
															$project_idx = $project_myinfo['idx'][$i];
															$project_title = $project_myinfo['title'][$i];
															$project_user_email = $project_myinfo['email'][$i];
															$project_user_name = $project_myinfo['name'][$i];
															$project_user_part = $project_myinfo['part'][$i];
															$project_user_regdate = $project_myinfo['regdate'][$i];
															$project_user_reg = $project_myinfo['reg'][$i];
															$project_user_rdate = $project_myinfo['rdate'][$i];
															$project_user_edate = $project_myinfo['edate'][$i];

															if($project_user_edate){
																$project_user_edate_tmp = explode("-", $project_user_edate);
																if($project_user_edate_tmp){
																	$pr_user_wdate = $project_user_edate_tmp[1]."/".$project_user_edate_tmp[2];
																}
															}else{
																$project_user_rdate_tmp = explode("-", $project_user_rdate);
																if($project_user_rdate_tmp){
																	$pr_user_wdate = $project_user_rdate_tmp[1]."/".$project_user_rdate_tmp[2];
																}
															}


															if($project_user_reg > 60){
																$ldl_box_time = $pr_user_wdate . " 업데이트";
															}else{
																$ldl_box_time = "최신 업데이트";
															}

															//$profile_main_img_src = profile_img_info($project_user_email);


														?>
															<div class="ldl_box" id="listsort_<?=$project_idx?>" value="<?=$project_idx?>">
																<div class="ldl_box_in">

																	<?if($user_id == $project_part_info_auth[$project_idx]){?>
																		<button class="ldl_box_close" id="ldl_box_close_my_<?=$project_idx?>" value="<?=$project_idx?>">닫기</button>
																	<?}?>

																	<div class="ldl_box_tit"><?=$project_title?></div>
																	<div class="ldl_box_time" id="ldl_box_time_<?=$project_idx?>"><?=$ldl_box_time?></div>
																	<div class="ldl_box_user">
																		<ul>
																			<?
																			$part_out = false;
																			for($j=0; $j<count($project_user_list[$project_idx]['email']); $j++){

																				$project_user_list_email = $project_user_list[$project_idx]['email'][$j];
																				$project_user_list_profile_img = profile_img_info($project_user_list_email);

																				if($user_id==$project_user_list_email){
																					//$part_out = true;
																					$li_class = ' class="ldl_me"';
																				}else{
																					$li_class = '';
																				}

																			?>
																				<li <?=$li_class?>>
																					<div class="ldl_box_img" style="background-image:url(<?=$project_user_list_profile_img?>)"></div>
																					<div class="ldl_box_user">
																						<strong><?=$project_user_list[$project_idx]['name'][$j]?></strong>
																						<span><?=$project_user_list[$project_idx]['part'][$j]?></span>
																					</div>
																				</li>
																			<?}?>
																		</ul>
																	</div>

																	<?//접속한 아이디 != 파티생성한 아이디
																	if($user_id!=$project_user_email && in_array($user_id , $project_user_list['use'][$project_idx])){?>
																		<button class="ldl_box_out" id="ldl_box_out_<?=$project_idx?>" value="<?=$project_idx?>">
																			<span>파티에서 나가기</span>
																		</button>
																	<?}else{?>
																		<button class="ldl_box_out" id="ldl_box_out_<?=$project_idx?>" value="<?=$project_idx?>" style="display:none;">
																			<span>파티에서 나가기</span>
																		</button>
																	<?}?>
																</div>
															</div>
														<?}?>
													<?}else{?>
														<div class="ldl_list_none">
															<strong><span>현재 생성된 파티가 없습니다.</span></strong>
														</div>
													<?}?>
												</div>

											</div>
										</div>
									</div>

									<div class="rew_mypage_party_make">
										<button class="btn_mypage_party_make" id="btn_mypage_party_make"><span>파티 만들기</span></button>
									</div>

								</div>
							*/?>
						<?}?>

						<?
							//챌린지
							if ($get_dirname=="challenge"){

								if(get_filename($_SERVER['PHP_SELF']=="write") || get_filename($_SERVER['PHP_SELF']=="edit")){
									$menu_class = "";
								}else{
								//	$menu_class = "on";
								}

								//챌린지 템플릿
								if(get_filename() == "template"){?>
									<div class="rew_mypage_04_tpl">
										<div class="rew_mypage_04_tpl_in">
											<div class="rew_mypage_title">
												<a href="/challenge/index.php"><strong>챌린지</strong></a>
											</div>

											<?if($template_auth){?>
												<em>일반계정</em>
												<?
												/*
												<div class="rew_mypage_tpl_write" id="rew_mypage_tpl_write">
													<a href="javascript:void(0);"><span>템플릿 생성</span></a>
												</div>
												*/
												?>
											<?}?>

											<div class="rew_mypage_tpl_write" id="rew_mypage_tpl_write">
											<a href="javascript:void(0);"><span>직접 만들기</span></a>
											</div>

											<div class="rew_mypage_tpl_list">
												<div class="rew_mypage_tpl_list_in">
													<div class="tpl_list_zzim" id="tpl_list_zzim">
														<a href="#"><span>💖</span> 내가 찜한 챌린지</a>
													</div>
													<div class="tpl_list_cc" id="thema_rec">
														<a href="#"><span>🏆</span> 추천테마</a>
													</div>
													<!-- <div class="ui-state-disabled" id="thema_all">
														<a href="#"><strong>전체</strong></a>
													</div> -->
													<div class="tpl_list_area">
														<ul id="tpl_list_area_ul">
															<li class="ui-state-disabled" id="thema_all"><a href="javascript:void(0);"><strong>전체</strong></a></li>
															<?
															//관리권한일때
															if($template_auth){?>
																<?for($i=0; $i<count($thema_user_info['thema_idx']); $i++){?>
																	<li id="themasort_<?=$thema_user_info['thema_idx'][$i]?>" value="<?=$i+1?>">
																		<a href="#"><strong id="tpl_list_title<?=$thema_user_info['thema_idx'][$i]?>"><?=$thema_user_info['title'][$i]?></strong></a>
																		<div class="tpl_list_drag">
																			<span>드래그 드랍 기능</span>
																		</div>
																		<div class="tpl_list_switch">
																			<em>추천</em>
																			<div class="btn_switch<?=$thema_user_info['recom'][$i]=='1'?" on":""?>" id="btn_switch_<?=$thema_user_info['thema_idx'][$i]?>" value="<?=$thema_user_info['thema_idx'][$i]?>">
																				<strong class="btn_switch_on"></strong>
																				<span>버튼</span>
																				<strong class="btn_switch_off"></strong>
																			</div>
																		</div>
																	</li>
																<?}?>

															<?//일반계정일때
															}else{

																for($i=0; $i<count($thema_user_info['thema_idx']); $i++){?>
																	<li class="themasort_user" id="themasort_<?=$thema_user_info['thema_idx'][$i]?>" value="<?=$i+1?>">
																		<a href="#"><strong id="tpl_list_title<?=$thema_user_info['thema_idx'][$i]?>"><?=$thema_user_info['title'][$i]?></strong></a>
																		<div class="tpl_list_drag">
																			<span>드래그 드랍 기능</span>
																		</div>
																		<div class="tpl_list_switch" style="display:none;">
																			<em>추천</em>
																			<div class="btn_switch">
																				<strong class="btn_switch_on"></strong>
																				<span>버튼</span>
																				<strong class="btn_switch_off"></strong>
																			</div>
																		</div>
																	</li>
																<?}?>
															<?}?>
														</ul>
													</div>
												</div>
											</div>

											<div class="rew_mypage_tab_02">
											<input type="hidden" id="temp_chall" value="<?=$chall_temp_cnt?>">
												<ul>
													<li>
														<span>내가 만든 챌린지</span>
														<strong><?=$chall_create_cnt?></strong>
													</li>
													<li>
														<span>임시저장 챌린지</span>
														<strong><?=$chall_temp_cnt?></strong>
													</li>
												</ul>
											</div>
										</div>
									</div>

								<?}else{?>

									<div class="rew_mypage_04">
										<div class="rew_mypage_04_in">
											<div class="rew_mypage_title">
												<strong><a href="/challenge/index.php">챌린지</a></strong>
											</div>

											<div class="rew_mypage_tab_03">
												<ul>
													<li>
														<span>도전가능</span>
														<strong><?=$chall_po_cnt?></strong>
													</li>
													<li>
														<span>도전중</span>
														<strong><?=$chall_ing_cnt?></strong>
													</li>
													<li>
														<span>도전완료</span>
														<strong><?=$chall_com_cnt?></strong>
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
															<strong><?=number_format($member_info['coin'][$user_id])?></strong>
														</li>
														<li>
															<span>획득 가능한 코인</span>
															<strong><?=$get_chall_coin?></strong>
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
														<?
														for($i=0; $i<count($chall_deadline_list['idx']); $i++){
															$deadline_idx = $chall_deadline_list['idx'][$i];
															$day_type = $chall_deadline_list['day_type'][$i];
															$cate = "[".$chall_category[$chall_deadline_list['cate'][$i]]."]";
															$title = $chall_deadline_list['title'][$i];
															$coin = $chall_deadline_list['coin'][$i];
															$maxcoin = $chall_deadline_list['maxcoin'][$i];

															if($day_type == '1'){
																$coin_max = "최대";
															}else{
																$coin_max = "";
															}
															if($maxcoin > 0){
																$coin = number_format($maxcoin);
															}else{
																$coin = number_format($coin);
															}

															$title = urldecode($title);
															$chllday = $chall_deadline_list['chllday'][$i];
															if($chllday=='0'){
																$chllday_txt = "Day";
															}else{
																$chllday_txt = $chllday;
															}

														?>

															<li>
																<a href="/challenge/view.php?idx=<?=$deadline_idx?>">
																	<span class="chall_ing_title"><?=$cate?> <?=$title?></span>
																	<span class="chall_ing_coin"><strong><?=$coin_max?> <?=$coin?></strong>코인</span>
																	<span class="chall_ing_dday"><strong>D - <?=$chllday_txt?></strong></span>
																</a>
															</li>
														<?}?>
													</ul>
												</div>
											</div>
										</div>

										<div class="rew_mypage_cha_write">
											<a href="/challenge/template.php" class="btn_mypage_cha_write"><span>챌린지 만들기</span></a>
										</div>
									</div>
								<?}?>
						<?}?>


						<?
						//오늘업무
						if ($get_dirname=="todaywork"){
							?>
							
							<div class="rew_mypage_08">
								<div class="rew_mypage_08_in">
									<div class="rew_mypage_title">
										<strong>오늘업무</strong>
									</div>

									<div class="rew_mypage_section">
										<div class="tdw_write">
											<div class="tdw_write_in">
												<div class="tdw_write_tab_04">
												<?/*<div class="tdw_write_tab_03">*/?>
													<div class="tdw_write_tab_in">
														<ul>
															<li>
																<button class="on tuto tuto_01_01" id="write_tab_01"><span>나의 업무</span></button>
															</li>
															<li>
																<button id="write_tab_03"><span>보고</span></button>
															</li>
															<li>
																<button id="write_tab_02"><span>요청</span></button>
															</li>
															<li>
																<button id="write_tab_04"><span>공유</span></button>
															</li>
														</ul>
													</div>
												</div>
												<div class="tdw_write_area">
													<div class="tdw_write_area_in">
														<div class="tdw_write_date">
															<input type="text" class="input_date" value="<?=$sel_wdate?>" id="workdate" />
															<input type="text" id="booking"/>
														</div>

														<div class="tdw_write_report" style="display: none;">
															<textarea class="input_report" placeholder="제목을 작성해 주세요." id="work_title"></textarea>
														</div>

														<div class="tdw_write_text_report" style="display: none;">
															<textarea class="input_write" placeholder="보고할 내용을 작성해 주세요." id="work_contents"></textarea>
														</div>

														<div class="tdw_write_text" id="tdw_write_text">
															<textarea class="input_write" placeholder="업무를 입력해 주세요." id="input_write"></textarea>
														</div>

														<?/*<div class="tdw_write_file_desc" id="tdw_write_file_desc" style="display: none;">
															<span>인사챌린지_참고01.hwp</span>
															<button>삭제</button>
														</div>
														*/?>

														<div class="tdw_write_user_desc" id="tdw_write_user_desc" style="display: none;">
															<ul>
															</ul>
														</div>


														<div class="tdw_write_btns on">
														<ul>
															<li><button><span>연차</span></button></li>
															<li><button><span>반차</span></button></li>
															<li><button><span>외출</span></button></li>
															<li><button><span>조퇴</span></button></li>
															<li><button><span>출장</span></button></li>
															<li><button class="tuto tuto_01_02"><span>교육</span></button></li>
															<li><button><span>미팅</span></button></li>
															<li><button><span>회의</span></button></li>
														</ul>
													</div>

														<div class="tdw_write_function">
															<div class="tdw_write_file_box">
																<input type="file" id="files" class="tdw_write_input_file" multiple>
																<label for="files" class="tdw_write_label_file" id="tdw_write_label_file"><span>첨부 파일</span></label>
															</div>
															<div class="tdw_write_req" style="display: none;">
																<button class="btn_req"><span>받을 사람</span></button>
															</div>
														</div>

														<!-- <div class="tdw_write_req" style="display:none;">
															<button class="btn_req"><span>받을 사람 선택</span></button>
															<span class="title_desc_01"></span>
														</div> -->
													</div>
												</div>

												<div class="tdw_write_btn" id="tdw_write_btn">
													<button><span>등록하기</span></button>
												</div>
											</div>
										</div>
									</div>
								</div>
							</div>
						<?}?>


						<?if ($get_dirname=="lives_old"){?>
							<div class="rew_mypage_07">
								<div class="rew_mypage_07_in">
									<div class="rew_mypage_title">
										<strong>LIVE</strong>
									</div>

									<div class="rew_mypage_section">
										<div class="live_list_box">
											<div class="live_list_desc">
												<div class="live_list_t">
													<div class="live_list_user_img">
														<img src="/html/images/pre/ico_user_001.png" alt="" />
													</div>
													<div class="live_user_name">
														<strong>윤지혜</strong>
														<span>디자인팀</span>
													</div>
													<div class="live_user_state">
														<div class="live_user_state_in">
															<ul>
																<li class="state_01">
																	<div class="live_user_state_circle">
																		<strong>집중<br />모드</strong>
																	</div>
																	<div class="layer_state layer_state_01">
																		<div class="layer_state_in">
																			<p>업무에 집중하고 있습니다.<br />급한 업무는 메신저를 남겨주세요.</p>
																			<em></em>
																		</div>
																	</div>
																</li>
																<li class="state_04">
																	<div class="live_user_state_circle">
																		<strong>일정<br />있음</strong>
																		<span>2</span>
																	</div>
																	<div class="layer_state layer_state_04">
																		<div class="layer_state_in">
																			<p><span>반차, 미팅</span> 일정이 있습니다.<br />상세일정은 오늘할일을 참조해 주세요.</p>
																			<em></em>
																		</div>
																	</div>
																</li>
															</ul>
														</div>
													</div>
												</div>
												<div class="live_list_m">
													<div class="live_list_today">
														<div class="live_list_today_tit">오늘업무</div>
														<div class="live_list_today_bar">
															<strong>3</strong>
															<span>4</span>
														</div>
														<div class="live_list_today_count">3/4</div>
													</div>
													<div class="live_list_cha">
														<div class="live_list_cha_tit">챌린지</div>
														<div class="live_list_cha_count">1건 참여완료</div>
													</div>
												</div>
												<div class="live_list_b">
													<div class="live_eval">
														<button class="btn_eval"><span>나의 역량평가 리포트</span><strong>2</strong></button>
													</div>
												</div>
											</div>
										</div>
									</div>

									<div class="rew_mypage_section">
										<div class="rew_mypage_section_title">
											<strong><span>📢</span> 나의 업무현황 표시</strong>
										</div>
										<div class="rew_grid_onoff">
											<div class="rew_grid_onoff_in">
												<ul>
													<li class="onoff_01">
														<em>집중모드</em>
														<div class="btn_switch">
															<strong class="btn_switch_on"></strong>
															<span>버튼</span>
															<strong class="btn_switch_off"></strong>
														</div>
													</li>
													<li class="onoff_02">
														<em>자리비움</em>
														<div class="btn_switch">
															<strong class="btn_switch_on"></strong>
															<span>버튼</span>
															<strong class="btn_switch_off"></strong>
														</div>
													</li>
													<li class="onoff_03">
														<em>퇴근</em>
														<div class="btn_switch">
															<strong class="btn_switch_on"></strong>
															<span>버튼</span>
															<strong class="btn_switch_off"></strong>
														</div>
													</li>
												</ul>
											</div>
										</div>
									</div>

									<div class="rew_mypage_section">
										<div class="rew_mypage_section_title">
											<strong><span>💡</span> [2021년 10월 20일] 나의 요약 리포트</strong>
											<a href="#"><span>더보기</span></a>
										</div>
										<div class="rew_my_report">
											<div class="report_now">
												<ul>
													<li>
														<strong>노하우</strong>
														<span>100%</span>
														<em>😀</em>
													</li>
													<li class="report_now_low">
														<strong>자기계발</strong>
														<span>0%</span>
														<em>😔</em>
													</li>
													<li>
														<strong>협업능력</strong>
														<span>100%</span>
														<em>😀</em>
													</li>
													<li>
														<strong>성실</strong>
														<span>100%</span>
														<em>😀</em>
													</li>
													<li class="report_now_low">
														<strong>적극성</strong>
														<span>0%</span>
														<em>😔</em>
													</li>
												</ul>
											</div>
										</div>
									</div>
								</div>
							</div>
						<?}?>


						<?if ($get_dirname=="reward"){?>
							<div class="rew_mypage_09">
								<div class="rew_mypage_09_in">
									<div class="rew_mypage_title">
										<strong>보상/코인</strong>
									</div>

									<!-- <div class="rew_mypage_tab_04">
										<ul>
											<li class="tab_reward_01">
												<button><span>출금신청</span></button>
											</li>
											<li class="tab_reward_02">
												<button><span>코인 이용안내</span></button>
											</li>
											<li class="tab_reward_03">
												<button><span>보상하기</span></button>
											</li>
											<li class="tab_reward_04">
												<button><span>충전하기</span></button>
											</li>
										</ul>
									</div> -->

									<div class="rew_mypage_section">
										<div class="rew_mypage_coin_box">
											<div class="title_area">
												<div class="qna">
													<strong class="title_main">내가 획득한 코인</strong>
													<span class="qna_q">?</span>
													<div class="qna_a">
														<span>챌린지 성공, 동료의 보상으로 획득한<br />코인으로, 현금으로 출금 가능한 코인<br />(출금 시 수수료율 5% 적용)</span>
													</div>
												</div>
											</div>
											<div class="rew_mypage_coin_chall">
												<strong><span><?=number_format($member_info['coin'][$user_id])?></span></strong>
											</div>
											<div class="rew_mypage_btn" id="rew_withdraw_btn">
												<button><span>출금 신청</span></button>
											</div>
										</div>
									</div>

									<div class="rew_mypage_section">
										<div class="rew_mypage_coin_box">
											<div class="title_area">
												<div class="qna">
													<strong class="title_main">사용 가능한 공용 코인</strong>
													<span class="qna_q">?</span>
													<div class="qna_a">
														<span>챌린지 만들기, 보상하기 등을 위해 회사에서<br />각 개인들에게 지급한 코인으로, 출금이 불가능한 코인<br />(공용 코인은 회사에서 회수할 수 있습니다.)</span>
													</div>
												</div>
											</div>
											<div class="rew_mypage_coin_chall">
												<strong><span><?=number_format($member_info['comcoin'][$user_id])?></span></strong>
											</div>
											<div class="rew_mypage_btns">
												<button id="btn_coin_reward"><span>보상하기</span></button>
												<button id="btn_challenges_create"><span>챌린지 만들기</span></button>
											</div>
										</div>
									</div>

									<div class="rew_mypage_section">
										<div class="rew_mypage_section_title">
											<strong><span>🔍</span> 코인 이용 안내</strong>
										</div>
										<div class="rew_mypage_coin_use">
											<ul>
												<li>
													<strong>내가 획득한 코인</strong>
													<p>챌린지 성공, 동료의 보상으로 획득한 코인으로 현금으로 출금 가능한 코인 (출금 시 수수료율 5% 적용)</p>
												</li>
												<li>
													<strong>공용 코인</strong>
													<p>챌린지 만들기, 보상하기 등을 위해 회사에서 각 개인들에게 지급한 코인으로 출금이 불가능한 코인 (공용 코인은 회사에서 회수할 수 있습니다.)</p>
												</li>
											</ul>
										</div>
									</div>
								</div>
							</div>
						<?}?>

						<?
							//파티, 프로젝트
							if ($get_dirname=="party"){

								if($_SERVER['PHP_SELF']=="/party/index.php" || $_SERVER['PHP_SELF']=="/party/tu_project.php"){
							?>
									<div class="rew_mypage_10">
										<div class="rew_mypage_10_in">
											<div class="rew_mypage_title">
												<strong>파티</strong>
											</div>
											<div class="rew_party_section">
												<div class="rew_party_tab">
													<div class="rew_party_tab_in">
														<ul>
															<li id="party_tab_my">
																<button><span>내 파티</span><strong><?=count($project_myinfo['idx'])?></strong></button>
															</li>
															<li id="party_tab_1">
																<button><span>원활</span><strong><?=$project_ing_cnt?></strong></button>
															</li>
															<li id="party_tab_3">
																<button><span>보통</span><strong><?=$project_normal_cnt?></strong></button>
															</li>
															<li id="party_tab_7">
																<button><span>지연</span><strong><?=$project_delay_cnt?></strong></button>
															</li>
														</ul>
													</div>
												</div>

												<div class="rew_party_un">
													<div class="rpu_in">
														<div class="rpu_func">
															<div class="rpu_func_tit">
																<span>미확인 업무<strong><?=$pj_read_delay_sum?></strong></span>
															</div>
														</div>

														<div class="rpu_list">

															<?//미확인 업무
															if($project_read_info['idx']){

																for($p_i=0; $p_i<count($project_read_info['idx']); $p_i++){
																	if($project_read_info['cnt'][$p_i] == '1'){
																		$pj_read_delay = '1';
																	}else if($project_read_info['cnt'][$p_i] > '1' && $project_read_info['cnt'][$p_i] <= '3'){
																		$pj_read_delay = '3';
																	}else{
																		$pj_read_delay = '7';
																	}

																	$link_page = $project_read_info['party_idx'][$p_i];
																?>
																	<div class="rpu_box delay_<?=$pj_read_delay?>" onclick="link()">
																		<div class="rpu_box_in">
																			<div class="rpu_box_tit">
																				<span><?=$project_read_info['party_title'][$p_i]?></span>
																			</div>
																			<div class="rpu_box_date">
																				<span>마지막 업데이트 : <?=$project_read_info['editdate'][$p_i]?></span>
																			</div>
																			<div class="rpu_box_num">
																				<span>미확인 <?=$project_read_info['cnt'][$p_i]?>건</span>
																			</div>
																		</div>
																	</div>
																<?}?>
															<?}else{?>
																<div class="rpu_none">
																	<span>미확인한 업무가 없습니다.</span>
																</div>
															<?}?>
														</div>
													</div>
												</div>
											</div>

										</div>

										<div class="rew_mypage_party_make">
											<button class="btn_mypage_party_make" id="btn_mypage_party_make"><span>파티 만들기</span></button>
										</div>
									</div>
								<?}else if($_SERVER['PHP_SELF']=="/party/view.php" || $_SERVER['PHP_SELF']=="/party/tu_pro_view.php"){?>
									<div class="rew_mypage_10_view">
										<div class="rew_mypage_10_view_in">
											<div class="rew_mypage_title" id="rew_part_title" value="<?=$idx?>">
												<? 
												$sql = "select * from work_todaywork_project where idx = '".$party_idx."' and companyno = '".$companyno."' and state != '9'";
												$query = selectQuery($sql);
												if($user_id == $query['email']){ ?>
													<p id="party_title_edit" class="party_title_edit"><span>✏️</span><strong class="party_title_text"><?=textarea_replace($project_title)?></strong></p>
													<? }else{ ?>
													<a href="#null" onclick="javascript:void(0);"><strong><?=$project_title?></strong></a>
												<?}?>
												<input type="hidden" value="<?=$user_id?>">
												<input type="hidden" value="<?=$party_idx?>">
												<div class="tdw_list_regi" id="tdw_list_regi_edit_<?=$idx?>" >
													<!-- <strong>수정중</strong> -->
													<textarea class="textarea_regi" id="textarea_regi_<?=$idx?>"><?=strip_tags($project_title)?></textarea>
													<div class="btn_regi_box">
														<button class="btn_regi_submit" id="btn_regi_submit" value="<?=$party_idx?>"><span>확인</span></button>
														<button class="btn_regi_cancel"><span>취소</span></button>
													</div>
												</div>
											</div>
											<div class="rew_mypage_party">
												<div class="party_accrue_coin">
													<span>파티 누적 코인</span>
													<strong><?=number_format($project_coin)?></strong>
													<?//파티종료된 경우
													if($project_state=='1'){?>
														<button class="btn_admin_coin_pop end" id="party_coin_expire" value="<?=$idx?>">코인 보내기</button>
													<?}else{?>
														<button class="btn_admin_coin_pop" id="party_coin" value="<?=$idx?>">코인 보내기</button>
													<?}?>
												</div>
												<!-- <div class="rew_mypage_coin_pop">
													<button class="btn_admin_coin_pop"><span>코인으로 응원하기</span></button>
												</div> -->
												<div class="party_tabs">
													<div class="party_tabs_in">
														<ul>
															<li>
																<dl>
																	<dt>D+<?=$project_diff_day?></dt>
																	<dd>경과일</dd>
																</dl>
															</li>
															<li>
																<dl>
																	<dt><?=$project_up_cnt?></dt>
																	<dd>업데이트</dd>
																</dl>
															</li>
															<li>
																<dl>
																	<dt><?=$project_heart_cnt?></dt>
																	<dd>좋아요</dd>
																</dl>
															</li>
															<li>
																<dl>
																	<dt><?=($work_all_cnt)?></dt>
																	<dd>전체업무</dd>
																</dl>
															</li>
														</ul>
													</div>
												</div>
												<div class="party_list_title">
													<span>파티 구성원<strong><?=$project_user_cnt?></strong></span>
												</div>
												<div class="party_user_list">
													<div class="party_user_list_in">
														<div class="pu_list_header">
															<div class="pu_list_header_in">
																<div class="pu_list_header_name">
																	<button class="btn_pu_reset" id="btn_pu_reset"><span>초기화</span></button>
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
															<div class="pu_list_conts_in" id="pu_list_conts_in">
																<ul>
																	<?
																	for($j=0; $j<count($project_user_info['idx']); $j++){
																		$project_user_idx = $project_user_info['idx'][$j];
																		$project_user_bidx = $project_user_info['bidx'][$j];
																		$project_user_email = $project_user_info['email'][$j];
																		$project_user_name = $project_user_info['name'][$j];
																		$project_user_part = $project_user_info['part'][$j];
																		$project_user_pma = $project_user_info['pma'][$j];
																		$project_user_complete = $project_user_info['complete'][$j];
																		$project_user_work = $project_user_info['work'][$j];
																		$project_user_profile_type = $project_user_info['profile_type'][$j];
																		$project_user_img_idx = $project_user_info['profile_img_idx'][$j];
																		$project_user_file_path = $project_user_info['file_path'][$j];
																		$project_user_file_name = $project_user_info['file_name'][$j];
																		$project_pu_heart = $work_like_list[$project_user_idx][$project_user_email];

																		$profile_img =  'https://rewardy.co.kr'.$project_user_file_path.$project_user_file_name;


																		$member_row_info = member_row_info($project_user_email);

																		//파티장일때 class설정
																		if($project_user_pma=="1"){
																			$part_leader = " party_leader ";
																		}else{
																			$part_leader = " ";
																		}
																	?>
																		<li>
																			<div class="pu_list_conts_name<?=$part_leader?>party_new">
																				<div class="pu_list_conts_name_in">
																					<div class="user_img" style="background-image:url('<?=$project_user_profile_type >= '0'?$profile_img:"/html/images/pre/img_prof_default.png"?>');"></div>
																					<div class="user_name" id="user_name_<?=$project_user_bidx?>">
																						<strong><?=$project_user_name?></strong>
																						<span><?=$member_row_info['part']?></span>
																						<input type="hidden" id="pu_list_id_<?=$project_user_bidx?>" value="<?=$project_user_email?>">
																						<?/*<em>N</em>*/?>
																					</div>
																				</div>
																			</div>
																			<div class="pu_list_conts_count">
																				<span><?=$project_user_complete?>/<?=$project_user_work?></span>
																			</div>
																			<div class="pu_list_conts_heart">


																				<?//파티종료
																				if($project_state=='1'){?>
																					<button class="btn_pu_coin" id="btn_pu_coin_expire" value="<?=$project_user_bidx?>"><span>코인</span></button>
																					<button class="btn_pu_heart" id="btn_pu_heart_expire" value="<?=$project_user_bidx?>"><span>좋아요</span></button>
																				<?}else{?>

																					<?if($user_id != $project_user_email){?>
																						<button class="btn_pu_coin" id="btn_pu_coin" value="<?=$project_user_bidx?>"><span>코인</span></button>

																						<?if($work_like_send[$project_user_bidx]){?>
																							<button class="btn_pu_heart on" id="btn_pu_heart" value="<?=$project_user_bidx?>"><span>좋아요</span></button>
																						<?}else{?>
																							<button class="btn_pu_heart<?=($user_id==$project_user_email)?"_me":""?>" id="btn_pu_heart" value="<?=$project_user_bidx?>"><span>좋아요</span></button>
																						<?}?>
																					<?}?>
																				<?}?>
																			</div>
																		</li>
																	<?}?>
																</ul>
															</div>
														</div>
													</div>
												</div>
											</div>
										</div>

										<div class="rew_mypage_party_btns">
											<?
											//파티종료일때
											if($project_state=='1'){?>

												<?if($project_make_uid == $user_id){?>
													<button class="btn_mypage_party_admin end" id="btn_mypage_party_admin_expire"><span>파티원 관리</span></button>

													<?//업무가 없을경우 삭제 가능
													if(!$work_all_cnt){?>
														<button class="btn_mypage_party_end end" id="btn_mypage_party_del_expire"><span>파티삭제</span></button>
													<?}else{?>
														<button class="btn_mypage_party_end end" id="btn_mypage_party_end_expire"><span>파티종료</span></button>
													<?}?>
			
												<?}else{?>
													<?
													//파티에 본인이 포함된 경우일때
													if(@in_array($user_id, $project_user_info['email'])){?>
														<button class="btn_mypage_party_out end" id="btn_mypage_party_out_expire"><span>파티에서 나가기</span></button>
													<?}else{?>
														<button class="btn_mypage_party_in end" id="btn_mypage_party_in_expire"><span>파티 참여하기</span></button>
													<?}?>
												<?}?>

											<?}else{?>

												<?if($project_make_uid == $user_id){?>
													<button class="btn_mypage_party_admin" id="btn_mypage_party_admin"><span>파티원 관리</span></button>

													<?//업무가 없을경우 삭제 가능
													if(!$work_all_cnt){?>
														<button class="btn_mypage_party_end" id="btn_mypage_party_del"><span>파티삭제</span></button>
													<?}else{?>
														<button class="btn_mypage_party_end" id="btn_mypage_party_end"><span>파티종료</span></button>
													<?}?>
			
												<?}else{?>
													<?
													//파티에 본인이 포함된 경우일때
													if(@in_array($user_id, $project_user_info['email'])){?>
														<button class="btn_mypage_party_out" id="btn_mypage_party_out"><span>파티에서 나가기</span></button>
													<?}else{?>
														<button class="btn_mypage_party_in" id="btn_mypage_party_in"><span>파티 참여하기</span></button>
													<?}?>
												<?}?>
											<?}?>

										</div>
									</div>
								<?}?>
						<?}?>

						<?
							//파티, 프로젝트
							if ($get_dirname=="insight"){
							?>

							<div class="rew_mypage_12">
								<div class="rew_mypage_12_in">
									<div class="rew_mypage_title">
										<strong>인사이트</strong>
									</div>

									<div class="rew_mypage_ins">
										<div class="rew_mypage_ins_in">
											<ul>
												<li>
													<a href="./rank_c.php"><span>🏆</span> 코인킹</a>
												</li>
												<li>
													<a href="./rank_l.php"><span>💖</span> 좋아요</a>
												</li>
												<li>
													<a href="./rank_p.php"><span>💪🏻</span> 역량</a>
												</li>
											</ul>
										</div>
									</div>

								</div>
							</div>

						<?}?>

					</div>

					<?if ($get_dirname!="team" && $get_dirname!="admin"){?>
						<div class="rew_menu_onoff" id="rew_menu_onoff">
							<button class="<?=$menu_class?>">열고 닫기</button>
						</div>
					<?}?>

				</div>

<script>
	$(document).ready(function(){
		$(".tdw_list_regi").hide();
		console.log("kang");
	});

	$(document).on("click", "#party_title_edit", function () {
  $(this).closest(".rew_mypage_title").children(".tdw_list_regi").show();
});

$(document).on("click",".btn_regi_cancel",function(){
  $(this).closest(".rew_mypage_title").children(".tdw_list_regi").hide();
  $(this).closest(".rew_mypage_title").children(".party_title_edit").show();
});

$(document).click(function (e) {
    if(!$(e.target).hasClass('textarea_regi')&&!$(e.target).hasClass('party_title_edit')&&!$(e.target).hasClass('party_title_text')){
      $(".tdw_list_regi").hide();
      $(".party_title_edit").show();
    }
});
	
$(document).on("click",".btn_regi_submit",function(){
  var idx =  $(this).val();
  var up_title = $(".textarea_regi").val();
  console.log(idx);	
  // console.log(idx);
  var fdata = new FormData();
  fdata.append("project_idx",idx);
  fdata.append("title",up_title);
  fdata.append("mode","project_title_update");

  $.ajax({
    type: "post",
    data: fdata,
    contentType: false,
    processData: false,
    url: "/inc/project_process.php",
    success: function (data) {
      //console.log(data);
      if (data) {
		var tdata = data.split("|");
		var box = tdata[2];
         $("#rew_part_title").html(box);
      }
    },
  })
});
</script>