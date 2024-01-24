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

			//오늘일자
			$todays = date("Y.m.d", time());

		?>

		<div class="t_contents">
			<div class="tc_in">
				<div class="tc_page">
					<div class="tc_page_in">
						<div class="tc_box_06">
							<div class="tc_box_06_in">
								<div class="tc_box_tit">
									<strong>오늘업무</strong>
									<span><?=$todays?></span>
								</div>
								<div class="tc_box_area">
									<div class="tc_area">
										<textarea name="contents" id="contents" class="area_01"></textarea>
									</div>
								</div>

								<div id="works_append"></div>

								<div class="tc_box_plus">
									<button id="works_add_btn"><span>업무 추가하기</span></button>
								</div>
								<div class="tc_box_btn">
									<a href="javascript:void(0);" id="write_btn">완료</a>
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
