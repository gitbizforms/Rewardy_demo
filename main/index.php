<?php

	//header페이지
	$home_dir = str_replace( basename(__DIR__) , "" , __DIR__ );
	include $home_dir  . "inc_lude/header.php";

//	$some_name = session_name("some_name"); // must exists like this 
//	session_set_cookie_params(0, '/', '.todaywork.co.kr');
//	session_start();
//	print_r($_COOKIE);

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
				<div class="tc_main">
					<div class="tc_main_in">
						<div class="tc_box_01">
							<div class="tc_box_01_in">
								<div class="tc_box_tit">
									<strong>오늘일</strong>
								</div>
								<div class="tc_box_btn">
									<a href="/admin/join.php"><span>서비스 가입</span></a>
									<a href="/works/write.php"><span>오늘할일</span></a>
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