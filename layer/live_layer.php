<div class="layer_today">
		<div class="layer_deam"></div>
		<div class="layer_result_in">
			<div class="layer_result_box">
				<div class="layer_result_left">
					<div class="layer_result_search">
						<div class="layer_result_search_box">
							<input type="text" class="input_search" placeholder="이름, 부서명을 검색" id="input_user_search"/>
							<button id="lives_search_bt"><span>검색</span></button>
						</div>
					</div>

					<div class="layer_result_user">
						<div class="layer_result_user_in">
							<ul>
								<li>
									<button class="on">
										<img src="/html/images/pre/ico_me.png" alt="" class="user_me" />
										<div class="user_img">
											<img src="/html/images/pre/ico_user_005.png" alt="" />
										</div>
										<div class="user_name">
											<strong>윤지혜</strong>
											디자인팀
										</div>
										<span class="user_num">
											<span>21</span>
										</span>
									</button>
								</li>

								<li>
									<button>
										<div class="user_img">
											<img src="/html/images/pre/ico_user_005.png" alt="" />
										</div>
										<div class="user_name">
											<strong>김광재</strong>
											콘텐츠팀
										</div>
										<span class="user_num">
											<span>2</span>
										</span>
									</button>
								</li>

								<li>
									<button>
										<div class="user_img">
											<img src="/html/images/pre/ico_user_005.png" alt="" />
										</div>
										<div class="user_name">
											<strong>김명선</strong>
											마케팅팀
										</div>
										<span class="user_num user_num_0">
											<span>0</span>
										</span>
									</button>
								</li>

								<li>
									<button>
										<div class="user_img">
											<img src="/html/images/pre/ico_user_005.png" alt="" />
										</div>
										<div class="user_name">
											<strong>김민경</strong>
											경영지원팀
										</div>
										<span class="user_num">
											<span>17</span>
										</span>
									</button>
								</li>

								<li>
									<button>
										<div class="user_img">
											<img src="/html/images/pre/ico_user_005.png" alt="" />
										</div>
										<div class="user_name">
											<strong>김상엽</strong>
											개발팀
										</div>
										<span class="user_num">
											<span>4</span>
										</span>
									</button>
								</li>

								<li>
									<button>
										<div class="user_img">
											<img src="/html/images/pre/ico_user_005.png" alt="" />
										</div>
										<div class="user_name">
											<strong>박정헌</strong>
											마케팅팀
										</div>
										<span class="user_num">
											<span>9</span>
										</span>
									</button>
								</li>

								<li>
									<button>
										<div class="user_img">
											<img src="/html/images/pre/ico_user_005.png" alt="" />
										</div>
										<div class="user_name">
											<strong>박희정</strong>
											콘텐츠팀
										</div>
										<span class="user_num">
											<span>12</span>
										</span>
									</button>
								</li>

								<li>
									<button>
										<div class="user_img">
											<img src="/html/images/pre/ico_user_005.png" alt="" />
										</div>
										<div class="user_name">
											<strong>서민정</strong>
											고객행복팀
										</div>
										<span class="user_num">
											<span>20</span>
										</span>
									</button>
								</li>

								<li>
									<button>
										<div class="user_img">
											<img src="/html/images/pre/ico_user_005.png" alt="" />
										</div>
										<div class="user_name">
											<strong>성지훈</strong>
											마케팅팀
										</div>
										<span class="user_num">
											<span>2</span>
										</span>
									</button>
								</li>

							</ul>
						</div>
					</div>
				</div>

				<div class="layer_result_right">
					<div class="layer_close">
						<button><span>닫기</span></button>
					</div>
					<div class="layer_result_top">
						<div class="layer_result_tab">
							<ul>
								<li><button class="btn_lr_01 on" ><span>오늘업무</span></button></li>
							</ul>
						</div>
					</div>
					<div class="layer_result_list desc_lr_01">
						<div class="layer_result_list_in" id="todaywork_zone_list">
							<div class="list_function">
								<div class="list_function_in">
									<div class="list_function_left">
										윤지혜 <span>디자인팀</span><strong>4</strong>
									</div>
									<div class="list_function_right">
										<div class="list_function_calendar">
										<button class="calendar_prev" id="prev_wdate"><span>이전</span></button>
										<input type="text" class="calendar_num" value="<?=$wdate?>" id="lives_date" readonly="readonly"/>
											<button class="calendar_next" id="next_wdate"><span>다음</span></button>
										</div>
									</div>
								</div>
							</div>
							<div class="report_area">
								<div class="report_area_in">
									<div class="report_cha">
										
										<div class="tdw_penalty_banner">
											<div class="tdw_pb_in">
												<img src="/html/images/pre/img_penalty.png" alt="" />
												<p><span>[긴급]</span>지각 페널티 카드가 발동했습니다.</p>
												<button class="btn_penalty_banner" style="display:none;"><span>미션 수행하기</span></button>
												<strong class="penalty_comp"><span>미션 완료</span></strong><!-- 미션 완료 -->
											</div>
										</div>

										<div class="tdw_list">
											<div class="tdw_list_in">
												<ul class="tdw_list_ul">
													<li class="tdw_list_li">
														<div class="tdw_list_box">
															<div class="tdw_list_chk">
																<button class="btn_tdw_list_chk"><span>완료체크</span></button>
															</div>
															<div class="tdw_list_desc">
																<p><span>[15:00]</span>엑셀 뷰페이지 추천서식 영역 디자인 교체</p>
															</div>
															<div class="tdw_list_func">
																<button class="tdw_list_memo" id="tdw_list_memo" value="<?=$idx?>"><span>메모</span></button>
																<!-- <button class="tdw_list_memo"><span>메모</span></button> -->
															</div>
														</div>

														<div class="tdw_list_memo_area">
															<div class="tdw_list_memo_area_in" id="memo_area_list_8360"></div>
														</div>
													</li>

													<li class="tdw_list_li">
														<div class="tdw_list_box">
															<div class="tdw_list_chk">
																<button class="btn_tdw_list_chk"><span>완료체크</span></button>
															</div>
															<div class="tdw_list_desc">
																<p>비즈폼 리워드 뷰페이지 시안 확인 및 수정사항 체크</p>
															</div>
														</div>
													</li>

													<li class="tdw_list_li">
														<div class="tdw_list_box">
															<div class="tdw_list_chk">
																<button class="btn_tdw_list_chk"><span>완료체크</span></button>
															</div>
															<div class="tdw_list_desc">
																<p><span>[2021-01-01 14:00 반차]</span>오후 반차</p>
															</div>
														</div>
													</li>

													<li class="tdw_list_li">
														<div class="tdw_list_box">
															<div class="tdw_list_chk">
																<button class="btn_tdw_list_chk"><span>완료체크</span></button>
															</div>
															<div class="tdw_list_desc">
																<p><span>[최순영 → 김상엽 업무요청]</span>월결제 관련 통계 자료 추출</p>
															</div>
														</div>
													</li>

													<li class="tdw_list_li">
														<div class="tdw_list_box">
															<div class="tdw_list_chk">
																<button class="btn_tdw_list_chk"><span>완료체크</span></button>
															</div>
															<div class="tdw_list_desc">
																<p><span>[2021-01-01 14:00 반차]</span>오후 반차</p>
															</div>
														</div>
													</li>

													<li class="tdw_list_li">
														<div class="tdw_list_box">
															<div class="tdw_list_chk">
																<button class="btn_tdw_list_chk"><span>완료체크</span></button>
															</div>
															<div class="tdw_list_desc">
																<p><span>[최순영 → 김상엽 업무요청]</span>월결제 관련 통계 자료 추출</p>
															</div>
														</div>
													</li>

													<li class="tdw_list_li">
														<div class="tdw_list_box">
															<div class="tdw_list_chk">
																<button class="btn_tdw_list_chk"><span>완료체크</span></button>
															</div>
															<div class="tdw_list_desc">
																<p><span>[2021-01-01 14:00 반차]</span>오후 반차</p>
															</div>
														</div>
													</li>

													<li class="tdw_list_li">
														<div class="tdw_list_box">
															<div class="tdw_list_chk">
																<button class="btn_tdw_list_chk"><span>완료체크</span></button>
															</div>
															<div class="tdw_list_desc">
																<p><span>[최순영 → 김상엽 업무요청]</span>월결제 관련 통계 자료 추출</p>
															</div>
														</div>
													</li>

													<li class="tdw_list_li">
														<div class="tdw_list_box">
															<div class="tdw_list_chk">
																<button class="btn_tdw_list_chk"><span>완료체크</span></button>
															</div>
															<div class="tdw_list_desc">
																<p><span>[2021-01-01 14:00 반차]</span>오후 반차</p>
															</div>
														</div>
													</li>

													<li class="tdw_list_li">
														<div class="tdw_list_box">
															<div class="tdw_list_chk">
																<button class="btn_tdw_list_chk"><span>완료체크</span></button>
															</div>
															<div class="tdw_list_desc">
																<p><span>[최순영 → 김상엽 업무요청]</span>월결제 관련 통계 자료 추출</p>
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

						<div class="layer_result_btns">
							<div class="layer_result_btns_in">
								<div class="btns_right">
									<button class="btns_write" onclick="location.href='/todaywork/index.php'"><span>작성하러 가기</span></button>
								</div>
							</div>
						</div>
					</div>
				</div>

			</div>

			<div class="layer_memo" style="display:none;">
				<input type="hidden" id="work_idx">
				<div class="layer_deam"></div>
				<div class="layer_memo_in">
					<div class="layer_memo_box">
						<textarea name="" class="textarea_memo" placeholder="메모를 작성해주세요." id="textarea_memo"></textarea>
					</div>
					<div class="layer_memo_btn">
						<button class="layer_memo_cancel" id="layer_memo_cancel"><span>취소</span></button>
						<button class="layer_memo_submit" id="layer_memo_submit"><span>등록하기</span></button>
					</div>
				</div>
			</div>

		</div>
	</div>