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

				$highlevel = 5;
				$sql = "select idx, email, name from work_member where state='0' and highlevel='".$highlevel."' and email!='".$user_id."' ";
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
                                                    <li><a href="/coins/reward.php" class="tab_reward on"><span>보상하기</span></a></li>
                                                    <li><a href="/coins/list.php" class="tab_list"><span>보상내역</span></a></li>
                                                    <li><a href="javascript:void(0);" class="tab_shop"><span>SHOP</span></a></li>
													<li><a href="javascript:void(0);" class="tab_buy"><span>구매내역</span></a></li>
													<li><a href="/coins/manual.php" class="tab_manual"><span><em>※</em>적립방법</span></a></li>
                                                </ul>
                                            </div>
                                        </div>
                                        <div class="tc_box_tit">
                                            <strong>보상하기</strong>
                                        </div>
                                        <div class="tc_coin_list">
                                            <ul>
                                                <li>
                                                    <div class="tc_input">
                                                        <div class="tc_coin_slc">
                                                            <div class="slc_01">
                                                                <select name="" id="coin_user">
																<option value="">누구에게</option>
																<?for($i=0; $i <count($res['idx']); $i++){
																	$idx = $res['idx'][$i];
																	$email = $res['email'][$i];
																	$name = $res['name'][$i];	
																?>
																		<option value="<?=$email?>"><?=$name?></option>
																<?}?>
															</select>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </li>
                                                <li>
                                                    <div class="tc_input">
                                                        <input type="text" id="f1" name="coin_point" class="input_01" />
                                                        <label for="f1" class="label_01">
														<strong class="label_tit">얼마를</strong>
													</label>
                                                    </div>
                                                </li>
                                                <li>
                                                    <div class="tc_coin_now">
                                                        <strong><?=number_format($user_coin);?></strong>
                                                        <span>coin 보유</span>
                                                    </div>
                                                </li>
                                                <li>
                                                    <div class="tc_box_area">
                                                        <div class="tc_input">
                                                            <textarea id="f2" name="coin_info" class="input_01"></textarea>
                                                            <label for="f2" class="label_01">
															<strong class="label_tit">보상 사유</strong>
														</label>
                                                        </div>
                                                    </div>
                                                </li>
                                            </ul>
                                        </div>

                                        <div class="tc_box_btn">
                                            <a href="javascript:void(0);" id="reward_btn">보상하기</a>
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