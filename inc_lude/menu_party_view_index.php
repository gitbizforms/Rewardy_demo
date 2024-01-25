<?

//메인 전체 회원 리스트
$member_list_info = member_main_team_list();
?>				
				
				<div class="rew_menu">
					<div class="rew_menu_in">
						<div class="rew_bar">
							<?if($user_level == '0'){?>

								<?if(!$member_mail_add_info['idx']){?>
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
									
									<a href="/itemshop/index.php" class="rew_bar_setting_04<?=($get_dirname=="itemshop")?" on":"";?>"><strong>아이템샵</strong></a>
									<?if($user_level == '0'){?>
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
						?>
								<div class="rew_mypage_08">
									<div class="rew_mypage_08_in">
									<div class="rew_mypage_close"><button><img src="/html/images/pre_m/rew_mypage_close.png"></button></div>
										<div class="rew_mypage_title" id="rew_part_title" value="<?=$idx?>">
											<a href="#null" onclick="javascript:void(0);"><strong><?=$project_title?></strong></a>
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
																								$project_user_pma = $project_user_info['pma'][$j];
																								$project_user_complete = $project_user_info['complete'][$j];
																								$project_user_work = $project_user_info['work'][$j];
																								$profile_type = $project_user_info['profile_type'][$j];
																								$project_profile_img_idx = $project_user_info['profile_img_idx'][$j];
																								$project_file_path = $project_user_info['file_path'][$j];
																								$project_file_name = $project_user_info['file_name'][$j];
																								// $project_user_img = profile_img_info($project_user_email);
																								$project_pu_heart = $work_like_list[$project_user_idx][$project_user_email];
																								$profile_file =  $project_file_path.$project_file_name;
																								$profile_img =  'http://demo.rewardy.co.kr'.$project_file_path.$project_file_name;
																								//파티장일때 class설정
																								if($project_user_pma=="1"){
																									$part_leader = " party_leader ";
																								}else{
																									$part_leader = " ";
																								}

																								$member_row_info = member_row_info($project_user_email);
																							?>
																<li>
																	<div class="pu_list_conts_name<?=$part_leader?>party_new">
																		<div class="pu_list_conts_name_in">
																			<div class="user_img" style="background-image:url('<?=$profile_file?$profile_img:"/html/images/pre/img_prof_default.png"?>');"></div>
																			<div class="user_name" id="user_name_<?=$project_user_bidx?>">
																				<strong><?=$member_row_info['name']?></strong>
																				<span><?=$member_row_info['part']?></span>
																				<input type="hidden" id="pu_list_id_<?=$project_user_bidx?>"
																					value="<?=$project_user_email?>">
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
																		<button class="btn_pu_coin" id="btn_pu_coin_expire"
																			value="<?=$project_user_bidx?>"><span>코인</span></button>
																		<button class="btn_pu_heart" id="btn_pu_heart_expire"
																			value="<?=$project_user_bidx?>"><span>좋아요</span></button>
																		<?}else{?>

																		<?if($user_id != $project_user_email){?>
																		<button class="btn_pu_coin" id="btn_pu_coin"
																			value="<?=$project_user_bidx?>"><span>코인</span></button>

																		<?if($work_like_send[$project_user_bidx]){?>
																		<button class="btn_pu_heart on" id="btn_pu_heart"
																			value="<?=$project_user_bidx?>"><span>좋아요</span></button>
																		<?}else{?>
																		<button class="btn_pu_heart<?=($user_id==$project_user_email)?"_me":""?>" id="btn_pu_heart"
																			value="<?=$project_user_bidx?>"><span>좋아요</span></button>
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
									<input type="hidden" id="party_close_flag" value="0">
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
									<input type="hidden" id="party_close_flag" value="0">
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
					</div>
					<div class="rew_menu_onoff">
						<button class="">열고 닫기</button>
					</div>
					
				</div>

				<div class="tdw_open_btn">
					<?if($project_make_uid == $user_id){?>
						<button><span>파티 관리하기</span></button>
					<?}else{?>
						<button><span>파티 참여하기</span></button>
					<?}?>
				</div>	