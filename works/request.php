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
						<div class="tc_box_06">
							<div class="tc_box_06_in">
								<div class="tc_box_tit">
									<strong>업무요청</strong>
								</div>
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
												<input type="text" id="req_date" name="" class="input_01" value="2021-05-10" disabled />
											</div>
											<div class="tc_timer_before">
												<input type="text" id="req_stime" name="" class="input_01" value="10:00" />
											</div>
											<div class="tc_timer_by">
												<span>~</span>
											</div>
											<div class="tc_timer_after">
												<input type="text" id="req_etime" name="" class="input_01" value="11:00" />
											</div>
										</div>
									</div>
								</div>
								<div class="tc_box_btn">
									<a href="/works/list.php">요청하기</a>
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

<script language="JavaScript">
/* FOR BIZ., COM. AND ENT. SERVICE. */
_TRK_CP = "/오늘일"; /* 페이지 이름 지정 Contents Path */
</script>

</body>


</html>
