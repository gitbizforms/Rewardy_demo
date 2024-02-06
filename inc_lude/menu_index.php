<?

//메인 전체 회원 리스트
$member_list_info = member_main_team_list();

	$sql = "select date_format(intime, '%H:%i') as stime, date_format(outtime, '%H:%i') as etime from work_company where idx = '".$companyno."'";
	$time_company = selectQuery($sql);

	list($c_stime_f, $c_etime_f) =  explode(":",$time_company['stime']);
	list($c_stime_e, $c_etime_e) = explode(":",$time_company['etime']);

	
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
									<div class="rew_mypage_title">
										<strong>오늘업무</strong>
									</div>

									<div class="rew_mypage_section">
										<div class="tdw_write">
											<div class="tdw_write_in">
												<div class="tdw_write_tab_04">
													<div class="tdw_write_tab_in">
														<input type="hidden" id="be_party_idx">
														<ul>
															<li>
																<button class="on <?=$tuto_start==true?' tuto tuto_01_01':''?>" id="write_tab_01"><span>나의 업무</span></button>
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
															<input type="text" id="startHour"/>
															<input type="text" id="startMin"/>
															<input type="text" id="endHour"/>
															<input type="text" id="endMin"/>
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

														<div class="tdw_write_user_desc" id="tdw_write_user_desc" style="display: none;">
															<ul>
															</ul>
														</div>
														
														<div class="tdw_write_btns on">
															<ul>
																<?
																if($decide_info['idx']){
																	for($i=0; $i<count($decide_info['idx']); $i++){?>
																		<li><button value="<?=$decide_info['idx'][$i]?>" class="<?=$tuto_start==true&&$decide_info['idx'][$i]=='3'?' tuto tuto_01_02':''?>"id="tdw_write_decide"><span><?=$decide_info['title'][$i]?></span></button></li>
																	<?}?>
																<?}else{?>
																	<li><button style="opacity:0;"></button></li>
																<?}?>
															</ul>
														</div>
														<div class="tdw_time_set">
															<div class="tdw_time_set_in">
																<div class="tdw_time_start">
																	<div class="tdw_time_hour time_set">
																		<div class="tdw_tab_sort_in">
																			<button class="btn_sort_on first_set" value="<?=$c_stime_f?>"><span><?=$c_stime_f?></span></button>
																			<ul>
																				<?for($i=1; $i < 25; $i++){?>
																					<li><button class = "startTimeHour" value = "<?=str_pad($i, 2, '0', STR_PAD_LEFT)?>"><span><?=str_pad($i, 2, '0', STR_PAD_LEFT)?></span></button></li>
																				<?}?>
																			</ul>
																		</div>
																	</div>
																	<div class="tdw_time_min time_set">
																		<div class="tdw_tab_sort_in">
																			<button class="btn_sort_on second_set" value="<?=$c_etime_f?>"><span><?=$c_etime_f?></span></button>
																			<ul>
																				<?for($i = 0; $i <= 50; $i += 10){?>
																					<li><button class = "startTimeMin" value = "<?=($i === 0) ? '00' : $i?>"><span><?=($i === 0) ? '00' : $i?></span></button></li>
																				<?}?>
																			</ul>
																		</div>
																	</div>
																</div>
																<!-- <div class="time_wave"><span>&#126;</span></div> -->
																<div class="tdw_time_end">
																	<div class="tdw_time_hour time_set">
																		<div class="tdw_tab_sort_in">
																			<button class="btn_sort_on first_set" value="<?=$c_stime_e?>"><span><?=$c_stime_e?></span></button>
																			<ul>
																				<?for($i=1; $i < 25; $i++){?>
																					<li><button class = "endTimeHour" value = "<?=str_pad($i, 2, '0', STR_PAD_LEFT)?>"><span><?=str_pad($i, 2, '0', STR_PAD_LEFT)?></span></button></li>
																				<?}?>
																			</ul>
																		</div>
																	</div>
																	<div class="tdw_time_min time_set">
																		<div class="tdw_tab_sort_in">
																			<button class="btn_sort_on second_set" value="<?=$c_etime_e?>"><span><?=$c_etime_e?></span></button>
																			<ul>
																				<?for($i = 0; $i <= 50; $i += 10){?>
																					<li><button class = "endTimeMin" value = "<?=($i === 0) ? '00' : $i?>"><span><?=($i === 0) ? '00' : $i?></span></button></li>
																				<?}?>
																			</ul>
																		</div>
																	</div>
																</div>
															</div>
														</div>
														<div class="tdw_write_function">
															<div class="tdw_write_file_box">
																<input type="file" id="files" class="tdw_write_input_file" multiple>
																<label for="files" class="tdw_write_label_file" id="tdw_write_label_file"><span>첨부 파일</span></label>
															</div>
															<div class="tdw_write_req" style="display: none;">
																<button class="btn_req"><span>받을 사람</span></button>
															</div>
																														<!-- 파티연결 -->
															<div class="tdw_write_par" style="display:block;">
																<button class="btn_par" id="today_party_link"><span>파티 연결</span></button>
															</div>
														</div>

													</div>
												</div>
												
												<div class="tdw_write_btn" id="tdw_write_btn">
													<button class ="tdw_lock" title="비밀글" id = "tdw_lock"><span>비밀글</span></button>
													<button class ="tdw_write" id = "tdw_write"><span>등록하기</span></button>
												</div>
											</div>
										</div>
									</div>
								</div>
							</div>
					</div>
					<?if ($get_dirname!="team" && $get_dirname!="admin"){?>
						<div class="rew_menu_onoff" id="rew_menu_onoff">
							<button class="<?=$menu_class?>">열고 닫기</button>
						</div>
					<?}?>
				</div>
				<div class="tdw_open_btn">
					<button><span>오늘업무 등록</span></button>
				</div>



		