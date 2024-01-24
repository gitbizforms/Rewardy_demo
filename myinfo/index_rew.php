<?
	//header페이지
	$home_dir = str_replace( basename(__DIR__) , "" , __DIR__ );
	include $home_dir . "/inc_lude/header.php";
?>

<style type="text/css">
	.rew_menu_onoff{display:none !important;}
</style>
<div class="rew_warp">
	<div class="rew_warp_in">
		<div class="rew_box">
			<div class="rew_box_in">
				<!-- menu -->
				<? include $home_dir . "/inc_lude/menu.php";?>
				<!-- //menu -->

				<!-- 콘텐츠 -->
				<div class="rew_conts">
					<div class="rew_conts_in">

							<div class="rew_intro">
								<div class="rew_intro_in">
									<div class="rew_intro_bar"></div>
									<div class="rew_intro_box">
										<img src="/html/images/pre/img_intro.png" alt="Rewardy" />
										<strong>우리 회사의 문화를 바꾸다.</strong>
										<button id="logout"><span>로그아웃</span></button>
									</div>
								</div>
							</div>


					</div>
				</div>
				<!-- //콘텐츠 -->
			</div>
		</div>
	</div>
	<div class="rew_q">
		<a href="01.html" target="_blank">(구)버전</a>
		<a href="002.html" target="_blank">(신)버전</a>
		<a href="0001.html" target="_blank">(리뉴얼)버전</a>
	</div>
	
</div>

	<!-- footer start-->
	<? include $home_dir . "/inc_lude/footer.php";?>
	<!-- footer end-->
</body>
</html>