<?php

	//업무목표작성
	//일일목표, 주간목표, 성과목표

	$highlevel = 5;
	$sql = "select idx, name, part from work_member where state='0' and highlevel='".$highlevel."' order by idx asc";
	$res = selectAllQuery($sql);
?>
<div class="t_layer_date">
	<div class="tl_deam"></div>
	<div class="tl_in">
		<div class="tl_close">
			<button><span>닫기</span></button>
		</div>
		<div class="tl_tit">
			<strong>일정</strong>
		</div>
		<div class="tc_box_08">
			<div class="tc_box_08_in">
				<div class="tc_box_area">
					<div class="tc_area">
						<textarea name="wdate_contents" id="wdate_contents" class="area_01"></textarea>
					</div>
				</div>

				<div class="tc_timer">
					<div class="tc_timer_in">
						<div class="tc_timer_chk">
							<input type="checkbox" name="chk_date" id="chk_date" />
							<label for="chk_date">시간 정하기</label>
						</div>
						<div class="tc_timer_box">
							<div class="tc_timer_date">
								<input type="text" id="date_sdate" name="" class="input_01" value="2021-05-10" />
							</div>
							<div class="tc_timer_before">
								<input type="text" id="date_stime" name="" class="input_01" value="10:00" />
							</div>

						</div>
					</div>
				</div>
				<div class="tc_box_btn">
					<a href="javascript:void(0);" id="date_write">작성하기</a>
				</div>
			</div>
		</div>
	</div>
</div>