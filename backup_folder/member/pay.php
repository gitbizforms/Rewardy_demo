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
						<div class="tc_box_03">
							<div class="tc_box_03_in">
								<div class="tc_box_tit">
									<strong>결제</strong>
								</div>
								<div class="tc_box_list">
									<ul>
										<li>
											<div class="tc_slc">
												<label for="b1" class="label_01">사용자수</label>
												<div class="slc_01">
													<select name="paycnt" id="paycnt">
														<?for($i=1; $i<21; $i++){?>
														<option value="<?=$i?>"><?=$i?>명</option>
														<?}?>
													</select>
												</div>
											</div>
										</li>
									</ul>
								</div>
								<div class="tc_box_btn">
									<a href="javascript:void(0);" id="paybtn">결제</a>
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
