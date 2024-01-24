<?php

	//header페이지
	$home_dir = str_replace( basename(__DIR__) , "" , __DIR__ );
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
				reward Main


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