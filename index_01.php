<?php

	//header페이지
	$home_dir = __DIR__;
	include $home_dir  . "/inc_lude/header.php";

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
			include $home_dir . "/inc_lude/top.php";

			
			if ($coin_info['idx']){

				//$coin_info['name']
				//number_format($coin_info['coin']);
			}
			
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
								<div class="tc_mains">
									<div class="tc_main_01">
										<ul>
											<li>
												<a href="#">
													<i class="far fa-bell"></i>
													<span>출퇴근</span>
												</a>
											</li>
											<li>
												<a href="javascript:void(0);" onclick="location_works('works');">
													<i class="far fa-clock"></i>
													<span>오늘업무</span>
												</a>
											</li>
											<li>
												<a href="#">
													<i class="fas fa-laptop"></i>
													<span>회의록</span>
												</a>
											</li>
											<li>
												<a href="#">
													<i class="far fa-comments"></i>
													<span>업무공유</span>
												</a>
											</li>
										</ul>
									</div>

									<div class="tc_main_02">
										<ul>
											<li>
												<a href="javascript:void(0);" onclick="location_works('challenge');">
													<i class="fas fa-medal"></i>
													<span>챌린지</span>
												</a>
											</li>
											<li>
												<a href="javascript:void(0);" onclick="location_works('reward');">
													<i class="fas fa-gift"></i>
													<span>보상</span>
												</a>
											</li>
										</ul>
									</div>

									<?/*<div class="tc_main_03">
										<a href="/coins/list.php">
											<span class="tc_main_normal">coin</span>
											<strong class="tc_main_coin">1,250</strong>
											<span class="tc_main_arrow arrow_down">▼</span>
											<span class="tc_main_num">10</span>
											<span class="tc_main_popup">
												<em class="tc_main_new">New</em>
												<strong class="tc_main_desc"><?=$coin_new_name?>님이 <?=$coin_new_coin?> coin을 보내셨습니다!</strong>
											</span>
											<span class="tc_main_more"><i class="fas fa-cog"></i></span>
										</a>
									</div>
									*/?>

								</div>

							</div>
						</div>
					</div>
				</div>
			</div>

		</div>

		<?php
			//footer페이지
			include $home_dir  . "/inc_lude/footer.php";
		?>

	</div>
</div>

	<?php
		//login페이지
		include $home_dir  . "/inc_lude/login_layer.php";
	?>

<script language="JavaScript">
/* FOR BIZ., COM. AND ENT. SERVICE. */
_TRK_CP = "/오늘일"; /* 페이지 이름 지정 Contents Path */
</script>

</body>
</html>