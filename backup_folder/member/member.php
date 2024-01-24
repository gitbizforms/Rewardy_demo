<?php

	//header페이지
	$home_dir = str_replace( basename(__DIR__) , "" , __DIR__ );
	include $home_dir  . "inc_lude/header.php";
	

	if($_POST['number']){
		$number = $_POST['number'];
	}
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
						<div class="tc_box_04">
							<div class="tc_box_04_in">
								<div class="tc_box_tit">
									<strong>초대 이메일</strong>
								</div>
								<div class="tc_box_list">
									<ul>

									<?for($i=0; $i<$number; $i++){?>
										<li>
											<div class="tc_input">
												<input type="text" id="mail<?=$i?>" name="" class="input_01" />
												<label for="mail<?=$i?>" class="label_01">
													<strong class="label_tit">이메일을 입력하세요</strong>
												</label>
											</div>
										</li>
									<?}?>

									</ul>
								</div>
								<div class="tc_box_btn">
									<a href="javascript:void(0);" id="sendmail">발송</a>
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
