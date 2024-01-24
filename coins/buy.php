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
												<li><a href="javascript:void(0);" class="tab_buy on"><span>구매내역</span></a></li>
												<li><a href="/coins/manual.php" class="tab_manual"><span><em>※</em>적립방법</span></a></li>
											</ul>
										</div>
									</div>
									<div class="tc_box_tit">
										<strong>구매내역</strong>
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


<script language="JavaScript">
/* FOR BIZ., COM. AND ENT. SERVICE. */
_TRK_CP = "/오늘일"; /* 페이지 이름 지정 Contents Path */
</script>

</body>


</html>
