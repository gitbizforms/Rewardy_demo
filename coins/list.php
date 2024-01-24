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

			$sql = "select idx, state, email, name, reward_user, reward_name, coin, memo, convert( varchar(16) , regdate, 120) as wdate from work_coininfo";
			$sql = $sql .=" where state != '9' and email='".$user_id."'";
			$sql = $sql .= " order by regdate desc";
			$res = selectAllQuery($sql);
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
												<li><a href="/coins/list.php" class="tab_list on"><span>보상내역</span></a></li>
												<li><a href="javascript:void(0);" class="tab_shop"><span>SHOP</span></a></li>
												<li><a href="javascript:void(0);" class="tab_buy"><span>구매내역</span></a></li>
												<li><a href="/coins/manual.php" class="tab_manual"><span><em>※</em>적립방법</span></a></li>
											</ul>
										</div>
									</div>
									<div class="tc_box_tit">
										<strong>보상내역</strong>
									</div>
									<div class="tc_coin_tbl">
										<div class="tc_coin_tbl_th">
											<ul>
												<li class="th_01"><span>일시</span></li>
												<li class="th_02"><span>구분</span></li>
												<li class="th_05"><span>대상</span></li>
												<li class="th_03"><span>상세</span></li>
												<li class="th_04"><span>coin</span></li>
											</ul>
										</div>
										<div class="tc_coin_tbl_td">
											<?for($i=0; $i <count($res['idx']); $i++){

												
												$state = $res['state'][$i];
												$coin = $res['coin'][$i];

												$name = $res['name'][$i];
												$reward_user = $res['reward_user'][$i];
												$reward_name = $res['reward_name'][$i];
												$memo = $res['memo'][$i];
												$wdate = $res['wdate'][$i];

												if($state == 0){
													$state_text = "적립";
													$span_classs =" class='color_reds'";
													$coin_ex = "+";
													$span_class ="";
													if($reward_name){
														$reward_name = $reward_name . "에게 받음";
													}else{
														$reward_name = "-";
													}

												}else if($state == 1){

													$state_text = "지급";
													$span_classs =" class='color_blues'";
													$reward_name = $reward_name . "에게 보냄";
													
													if($user_id != $reward_user){
														$coin_ex = "-";
														$span_class =" class='color_red'";
													}else{
														$coin_ex = "-";
														$span_class ="";
													}
												}else{
													$state_text = "";
													$coin_ex = "";
													$span_class ="";
													$span_classs ="";
												}
											?>
											<ul>
												<li class="td_01"><span <?=$span_classs?>><?=$wdate?></span></li>
												<li class="td_02"><span <?=$span_classs?>><?=$state_text?></span></li>
												<li class="td_05"><span <?=$span_classs?>><?=$reward_name?></span></li>
												<li class="td_03"><span <?=$span_classs?>><?=$memo?>
													<!--<button>상세보기</button>-->
													<div class="td_detail"><?=$memo?></div>
													</span></li>
												<li class="td_04"><span <?=$span_classs?>><?=$coin_ex?><?=number_format($coin)?></span></li>
											</ul>

											<?}?>
										</div>
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

<div class="t_layer_req">
<script language="JavaScript">
/* FOR BIZ., COM. AND ENT. SERVICE. */
_TRK_CP = "/오늘일"; /* 페이지 이름 지정 Contents Path */
</script>

</body>


</html>
