<?php

	//header페이지
	$home_dir = str_replace( basename(__DIR__) , "" , __DIR__ );
	include $home_dir  . "inc_lude/header.php";
?>

<div class="todaywork_wrap">
	<div class="t_in">
		<!-- header -->
		<?php
		//top페이지
			include $home_dir . "inc_lude/top.php";

		?>

		<div class="t_contents">
			<div class="tc_in">
				<div class="tc_page">
					<div class="tc_page_in">
						<div class="tc_box_09">
							<div class="tc_box_09_in">
								
								<div class="tc_coin">
									<div class="tc_coin_tab">
										<div class="tc_coin_tab_in">
											<ul>
												<li><a href="/coins/reward.php" class="tab_reward"><span>보상하기</span></a></li>
												<li><a href="/coins/list.php" class="tab_list"><span>보상내역</span></a></li>
												<li><a href="javascript:void(0);" class="tab_shop"><span>SHOP</span></a></li>
												<li><a href="javascript:void(0);" class="tab_buy"><span>구매내역</span></a></li>
												<li><a href="/coins/manual.php" class="tab_manual on"><span><em>※</em>적립방법</span></a></li>
											</ul>
										</div>
									</div>
									<div class="tc_box_tit">
										<strong>적립방법</strong>
									</div>

									<div class="tc_manual_area">
										<div class="tc_manual_tit">
											<span>최초</span>
										</div>
										<div class="tc_manual_list">
											<strong>기본 활용법(튜토리얼)</strong>
											<ul>
												<li>
													<div class="col_01"><span>최초 가입 후 로그인 시 웰컴코인</span></div>
													<div class="col_02"><span>5,000코인</span></div>
													<div class="col_03"><span>최초 1회</span></div>
												</li>
												<li>
													<div class="col_01"><span>최초 1회 오늘업무 등록</span></div>
													<div class="col_02"><span>1,000코인</span></div>
													<div class="col_03"><span>최초 1회</span></div>
												</li>
												<li>
													<div class="col_01"><span>최초 1회 오늘업무 완료</span></div>
													<div class="col_02"><span>1,000코인</span></div>
													<div class="col_03"><span>최초 1회</span></div>
												</li>
												<li>
													<div class="col_01"><span>최초 1회 업무요청 등록</span></div>
													<div class="col_02"><span>2,000코인</span></div>
													<div class="col_03"><span>최초 1회</span></div>
												</li>
												<li>
													<div class="col_01"><span>최초 1회 업무요청 완료</span></div>
													<div class="col_02"><span>2,000코인</span></div>
													<div class="col_03"><span>최초 1회</span></div>
												</li>
												<li>
													<div class="col_01"><span>최초 1회 목표설정 및 달성</span></div>
													<div class="col_02"><span>5,000코인</span></div>
													<div class="col_03"><span>최초 1회</span></div>
												</li>
											</ul>
										</div>

										<div class="tc_manual_tit">
											<span>일일/주간</span>
										</div>
										<div class="tc_manual_list">
											<strong>오늘업무 활성화</strong>
											<ul>
												<li>
													<div class="col_01"><span>업무시작 30분 이내에 오늘업무 등록</span></div>
													<div class="col_02"><span>100코인</span></div>
													<div class="col_03"><span>일일 3건</span></div>
													<em>오늘업무 삭제 시 코인회수</em>
												</li>
												<li>
													<div class="col_01"><span>퇴근 전 오늘업무 완료여부 체크</span></div>
													<div class="col_02"><span>100코인</span></div>
													<div class="col_03"><span>일일 3건</span></div>
													<em>삭제 또는 완료취소 시 코인회수</em>
												</li>
												<li>
													<div class="col_01"><span>업무요청 1건 이상 받아서 처리완료</span></div>
													<div class="col_02"><span>300코인</span></div>
													<div class="col_03"><span>일일 3건</span></div>
													<em>삭제 또는 완료취소 시 코인회수</em>
												</li>
											</ul>
										</div>

										<div class="tc_manual_list">
											<strong>일정관리</strong>
											<ul>
												<li>
													<div class="col_01"><span>주간일정 미리 설정하기</span></div>
													<div class="col_02"><span>1,000코인</span></div>
													<div class="col_03"><span>매주 1회</span></div>
													<em>삭제 또는 완료취소 시 코인회수</em>
												</li>
											</ul>
										</div>

										<div class="tc_manual_list">
											<strong>목표관리</strong>
											<ul>
												<li>
													<div class="col_01"><span>목표 설정 후 달성완료</span></div>
													<div class="col_02"><span>1,000코인</span></div>
													<div class="col_03"><span>일일 1건</span></div>
													<em>삭제 또는 완료취소 시 코인회수</em>
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

		<?php

			//footer페이지
			include $home_dir  . "inc_lude/footer.php";

		?>
	</div>
</div>
<?php
		//login페이지
		include $home_dir  . "inc_lude/login_layer.php";
?>


<div class="t_layer_req">
	<div class="tl_deam"></div>
	<div class="tl_in">
		<div class="tl_close">
			<button><span>닫기</span></button>
		</div>
		<div class="tc_box_08">
			<div class="tc_box_08_in">
				<div class="tc_box_area">
					<div class="tc_area">
						<textarea name="" id="" class="area_01"></textarea>
					</div>
				</div>
				<div class="tc_user_list">
					<div class="chk_all">
						<input type="checkbox" name="" id="chkall" />
						<label for="chkall">전체선택</label>
					</div>
					<ul>
						<li>
							<input type="checkbox" name="" id="chk1" />
							<label for="chk1">이선규<span>(스마트)</span></label>
						</li>
						<li>
							<input type="checkbox" name="" id="chk2" />
							<label for="chk2">손언영<span>(스마트)</span></label>
						</li>
						<li>
							<input type="checkbox" name="" id="chk3" />
							<label for="chk3">윤지혜<span>(스마트)</span></label>
						</li>
						<li>
							<input type="checkbox" name="" id="chk4" />
							<label for="chk4">김명선<span>(스마트)</span></label>
						</li>
						<li>
							<input type="checkbox" name="" id="chk5" />
							<label for="chk5">하병호<span>(스마트)</span></label>
						</li>
						<li>
							<input type="checkbox" name="" id="chk6" />
							<label for="chk6">유상길<span>(스마트)</span></label>
						</li>
						<li>
							<input type="checkbox" name="" id="chk7" />
							<label for="chk7">성지훈<span>(스마트)</span></label>
						</li>
					</ul>
				</div>
				<div class="tc_timer">
					<div class="tc_timer_in">
						<div class="tc_timer_chk">
							<input type="checkbox" name="" id="chk_t" />
							<label for="chk_t">시간 정하기</label>
						</div>
						<div class="tc_timer_box">
							<div class="tc_timer_date">
								<input type="text" id="" name="" class="input_01" value="2021-05-10" />
							</div>
							<div class="tc_timer_before">
								<input type="text" id="" name="" class="input_01" value="10:00" />
							</div>
							<div class="tc_timer_by">
								<span>~</span>
							</div>
							<div class="tc_timer_after">
								<input type="text" id="" name="" class="input_01" value="11:00" />
							</div>
						</div>
					</div>
				</div>
				<div class="tc_box_btn">
					<a href="../works/list.php">요청하기</a>
				</div>
			</div>
		</div>
	</div>
</div>

<div class="t_layer_goal">
	<div class="tl_deam"></div>
	<div class="tl_in">
		<div class="tl_close">
			<button><span>닫기</span></button>
		</div>
		<div class="tc_box_09">
			<div class="tc_box_09_in">
				<div class="tc_goal_select">
					<ul>
						<li><button class="on"><span>일일목표</span></button></li>
						<li><button><span>주간목표</span></button></li>
						<li><button><span>성과목표</span></button></li>
					</ul>
				</div>
				<div class="tc_box_area">
					<div class="tc_area">
						<textarea name="" id="" class="area_01" placeholder="목표"></textarea>
					</div>
				</div>
				<div class="tc_box_area_02">
					<div class="tc_area">
						<textarea name="" id="" class="area_01" placeholder="핵심결과"></textarea>
					</div>
				</div>
				<div class="tc_box_area_03">
					<div class="tc_area">
						<input type="text" id="" name="" class="input_01" placeholder="완료일자" />
					</div>
				</div>
				<div class="tc_box_btn">
					<a href="../works/list.php">작성하기</a>
				</div>
			</div>
		</div>
	</div>
</div>
<script language="JavaScript">
/* FOR BIZ., COM. AND ENT. SERVICE. */
_TRK_CP = "/오늘일"; /* 페이지 이름 지정 Contents Path */
</script>

</body>


</html>
