<?php

	//업무목표작성
	//일일목표, 주간목표, 성과목표

	$highlevel = 5;
	$sql = "select idx, name, part from work_member where state='0' and highlevel='".$highlevel."' and companyno='".$companyno."' and email != '".$user_id."' order by idx asc";
	$res = selectAllQuery($sql);

?>
<div class="t_layer_req">
	<div class="tl_deam"></div>
	<div class="tl_in">
		<div class="tl_close">
			<button><span>닫기</span></button>
		</div>
		<div class="tl_tit">
			<strong>업무요청</strong>
		</div>
		<div class="tc_box_08">
			<div class="tc_box_08_in">
				<div class="tc_box_area">
					<div class="tc_area">
						<textarea name="req_contents" id="req_contents" class="area_01"></textarea>
					</div>
				</div>
				<div class="tc_user_list">
					<div class="chk_all">
						<input type="checkbox" name="chkall" id="chkall" />
						<label for="chkall">전체선택</label>
					</div>
					<ul>

						<?php
							for($i=0; $i<count($res['idx']); $i++){?>
						<li>
							<input type="checkbox" name="chk" id="chk<?=$i?>" value="<?=$res['idx'][$i]?>"/>
							<label for="chk<?=$i?>"><?=$res['name'][$i]?><span>(<?=$res['part'][$i]?>)</span></label>
						</li>

						<?}?>
					</ul>
				</div>
				<div class="tc_timer">
					<div class="tc_timer_in">
						<div class="tc_timer_chk">
							<input type="checkbox" name="chk_t" id="chk_t" />
							<label for="chk_t">시간 정하기</label>
						</div>
						<div class="tc_timer_box">
							<div class="tc_timer_date">
								<input type="text" id="req_date" name="" class="input_01" value="2021-05-10" />
							</div>
							<div class="tc_timer_before">
								<input type="text" id="req_stime" name="" class="input_01" value="10:00" />
							</div>

						</div>
					</div>
				</div>
				<div class="tc_box_btn">
					<a href="javascript:void(0);" id="req_write">요청하기</a>
				</div>
			</div>
		</div>
	</div>
</div>