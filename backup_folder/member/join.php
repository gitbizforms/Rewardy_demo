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
				<div class="tc_join">
					<div class="tc_join_in">
						<div class="tc_box_02">
							<div class="tc_box_02_in">
								<div class="tc_box_tit">
									<strong>서비스 가입</strong>
								</div>
								<div class="tc_box_list">
									<ul>
										<li>
											<div class="tc_input">
												<input type="text" id="a1" name="" class="input_01" maxlength="50"/>
												<label for="a1" class="label_01">
													<strong class="label_tit">이메일</strong>
												</label>
											</div>
										</li>
										<li>
											<div class="tc_input">
												<input type="text" id="a2" name="" class="input_01" maxlength="6"/>
												<label for="a2" class="label_01">
													<strong class="label_tit">이름</strong>
												</label>
											</div>
										</li>
										<li>
											<div class="tc_input">
												<input type="password" id="a3" name="" class="input_01" />
												<label for="a3" class="label_01">
													<strong class="label_tit">비밀번호</strong>
												</label>
											</div>
										</li>
										<li>
											<div class="tc_input">
												<input type="password" id="a4" name="" class="input_01" />
												<label for="a4" class="label_01">
													<strong class="label_tit">비밀번호 확인</strong>
												</label>
											</div>
										</li>
										<li>
											<div class="tc_input">
												<input type="text" id="a5" name="" class="input_01" maxlength="50"/>
												<label for="a5" class="label_01">
													<strong class="label_tit">회사명</strong>
												</label>
											</div>
										</li>
										<li>
											<div class="tc_input">
												<input type="text" id="a6" name="" class="input_01" maxlength="50"/>
												<label for="a6" class="label_01">
													<strong class="label_tit">부서명</strong>
												</label>
											</div>
										</li>
									</ul>
								</div>
								<div class="tc_box_btn" id="joinchk">
									<a href="javascript:void(0);" id="join1">가입</a>
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
