<?
	//header페이지
	$home_dir = str_replace( basename(__DIR__) , "" , __DIR__ );
	// include $home_dir . "/inc_lude/header_insight.php";
	include $home_dir . "/inc_lude/header_insight.php";

	//오늘날짜
	$wdate = str_replace("-",".",$today);
	$sel_wdate = str_replace(".", "-" , $wdate);

	//날짜변환
if($wdate){
	$m_wdate = str_replace(".","-",$wdate);
}

$month_tmp = explode(".",$wdate);
if($month_tmp){
	$month_date = $month_tmp[0].".".$month_tmp[1];
}

	//현재일자기준으로 한주간 체크
	$date_tmp = explode("-",$m_wdate);

	if($date_tmp){
		$year = $date_tmp[0];
		$month = $date_tmp[1];
		if(!$date_tmp[2]){
			$date_tmp[2] = "01";
		}
		$day = $date_tmp[2];
	}

	$sel_day = date("t",mktime(0, 0, 0, $month, $day, $year));				// 지정된 달은 몇일까지 있을까요?
	$sel_yoil = date("N",mktime(0, 0, 0, $month, 1, $year));				// 지정된 달의 첫날은 무슨요일일까요?
	$day_line = $sel_yoil%7;												// 지정된 달 1일 앞의 공백 숫자.
	$ra = ($sel_day + $day_line)/7;
	$ra = ceil($ra);
	$ra = $ra-1;															// 지정된 달은 총 몇주로 라인을 그어야 하나?

	//월간업무
	$ym = $year."-".$month;
?>
<script type="text/javascript">
	$(document).ready(function(){

	});
</script>
<input type="hidden" id="work_wdate" value="<?=$wdate?>">
<div class="rew_warp">
	<div class="rew_warp_in">
		<div class="rew_box">
			<div class="rew_box_in">
			<? include $home_dir . "/inc_lude/header_new.php";?>

				<!-- menu -->
				<? include $home_dir . "/inc_lude/menu_insight.php";?>
				<!-- //menu -->

				<!-- 콘텐츠 -->
				<div class="rew_conts">
					<div class="rew_conts_in">
						<div class="rew_ins_func">
							<div class="rew_ins_func_in">
								<div class="rew_ins_func_title">
									<strong>코인킹</strong><!-- 좋아요 : 하트킹 -->
									<span>코인을 많이 획득한 순위</span><!-- 좋아요 : 이달에 하트를 많이 획득한 순위 -->
								</div>

								<div class="rew_ins_calendar">
									<div class="tdw_ins_date">
										<input type="text" class="input_date" value="<?=$wdate?>" maxlength="10" id="r_work_date" style="display:none;">
										<input type="text" class="input_date" id="r_work_month" value="<?=$month_date?>" data-min-view="months" data-view="months" data-date-format="yyyy.mm" readonly="readonly">
									</div>
									<div class="tdw_ins_tab_03">
										<div class="tdw_ins_tab_in">
											<ul>
												<!-- <li>
													<button class="select_c_dd on"><span>일간</span></button>
												</li>
												<li>
													<button class="select_c_ww"><span>주간</span></button>
												</li> -->
												<li>
													<!-- <button class="select_c_mm"><span>월간</span></button> -->
													<button disabled='disabled' class="select_co_mm"><span>월간</span></button>
												</li>
											</ul>
										</div>
									</div>
								</div>
							</div>
						</div>

						<div class="rew_conts_scroll_12">

							<div class="rew_ins">
								<div class="rew_ins_c_dd">
									<div class="ins_rank_top">
						        <ul>
						          <?
						            $sql = "select email, sum(coin) as sum from work_coininfo";
						            $sql = $sql .= " where code in (500,700,900,1000,1100) and DATE_FORMAT(workdate, '%Y-%m') = '".$ym."' and state = 0";
						            $sql = $sql .= " and companyno = '".$companyno."' group by email order by sum desc limit 3";
						            if($user_id == 'adsb12@nate.com'){
						            	echo $sql;
						            }
						            $rank = selectAllQuery($sql);
									var_dump($sql);
						            if($rank){
						              for($i=0; $i<count($rank[email]); $i++){
						                $r_email = $rank[email][$i];
						                $r_sum = $rank[sum][$i];

						            $rank_img_src = profile_img_info($r_email);

						            $sql = "select name,part from work_member where email = '".$r_email."' and state = 0 and companyno = '".$companyno."'";
						            $r_mem = selectQuery($sql);

						            if($r_mem){
						              $r_part = $r_mem[part];
						              $r_name = $r_mem[name];
						            }
						          ?>
						            <li class="ir_rank_<?=$i+1?>">
						              <div class="ins_rank_user">
						                <div class="ir_user_img">
						                  <div class="ir_user_img_in" style="background-image:url('<?=$rank_img_src?>');"></div>
						                </div>
						                <div class="ir_name">
						                  <div class="ir_name_user"><?=$r_name?></div>
						                  <div class="ir_name_team"><?=$r_part?></div>
						                </div>
						                <div class="ir_rank_mark">
						                  <span><?=$i+1?>위</span>
						                </div>
						              </div>
						              <div class="ir_user_coin"><!-- 좋아요 : ir_user_heart -->
						                <span><?=number_format($r_sum)?></span>
						              </div>
						            </li>
						          <?}?>
						         <?}?>
						        </ul>
						      </div>

						      <div class="ins_rank_graph">
						        <div class="ins_rank_graph_in">
						          <ul>
						            <?
									$sql = "select email, sum(coin) as sum from work_coininfo";
									$sql = $sql .= " where  code in (500,700,900,1000,1100) and DATE_FORMAT(workdate, '%Y-%m') = '".$ym."' and state = 0";
									$sql = $sql .= " and companyno = '".$companyno."' group by email order by sum desc limit 3,7";

						              $rank = selectAllQuery($sql);

							           if($rank){
						                for($i=0; $i<count($rank[email]); $i++){
						                  $r_email = $rank[email][$i];
						                  $r_sum = $rank[sum][$i];

						              $rank_img_src = profile_img_info($r_email);

						              $sql = "select name,part from work_member where email = '".$r_email."' and state = 0 and companyno = '".$companyno."'";
						              $r_mem = selectQuery($sql);

						              if($r_mem){
						                $r_part = $r_mem[part];
						                $r_name = $r_mem[name];
						              }
						            ?>
						              <li class="ir_rank_<?=$i+4?>">
						                <div class="ins_rank_bar">
						                  <div class="ir_bar_coin"><!-- 좋아요 : ir_bar_heart -->
						                    <span><?=number_format($r_sum)?></span>
						                  </div>
						                  <div class="ir_bar_rank">
						                    <span><?=$i+4?></span>
						                  </div>
						                  <div class="ir_bar_graph"><span></span></div>
						                </div>
						                <div class="ins_rank_user">
						                  <div class="ir_user_img">
						                    <div class="ir_user_img_in" style="background-image:url('<?=$rank_img_src?>');"></div>
						                  </div>
						                  <div class="ir_name">
						                    <div class="ir_name_user"><?=$r_name?></div>
						                    <div class="ir_name_team"><?=$r_part?></div>
						                  </div>
						                </div>
						              </li>
						            <?}?>
						           <?}?>

						          </ul>
						        </div>
						      </div>
								</div>

								<div class="rew_ins_c_ww" style="display:none"></div>
								<div class="rew_ins_c_mm" style="display:none"></div>

							</div>
						</div>

					</div>
				</div>
				<!-- //콘텐츠 -->
			</div>
		</div>
	</div>
</div>
	<?php
		//튜토리얼 레벨 레이어
		include $home_dir . "loading.php";

		//쪽지보내기 레이어
		include $home_dir . "/layer/mess_pop.php";
		
		include $home_dir . "/layer/pro_pop.php";
	?>
	<?php
		//튜토리얼 시작 레이어
		include $home_dir . "/layer/tutorial_start.php";

		//튜토리얼 시작 레이어
		include $home_dir . "/layer/tutorial_main_level.php";
	?>

<!-- footer start-->
<? include $home_dir . "/inc_lude/footer.php";?>
<!-- footer end-->

<script type="text/javascript">
	$(document).ready(function(){
		window.onbeforeunload = function () { $('.rewardy_loading_01').css('display', 'block'); }
		$(window).load(function () {          //페이지가 로드 되면 로딩 화면을 없애주는 것
            $('.rewardy_loading_01').css('display', 'none');
        });
	});
	window.onpageshow = function(event) {
 	     if ( event.persisted || (window.performance && window.performance.navigation.type == 2)) {
			  $('.rewardy_loading_01').css('display', 'none');
  		  }
		}
</script>
</body>


</html>
