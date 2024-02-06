<div class="layer_memo" style = "display:none;">
  <div class="layer_deam"></div>
  <div class="layer_memo_in">
    <div class="layer_title">
      <div class="layer_title_in">
        <p>회의 주제 작성하기</p>
        <em>오늘업무에 회의 주제를 등록합니다.</em>
      </div>
    </div>
    <div class="layer_memo_box">
      <textarea name="" class="textarea_memo" placeholder="회의를 작성해주세요."></textarea>
    </div>
<div class="tdw_time_set">
		<input type="hidden" id="startHour"/>
		<input type="hidden" id="startMin"/>
		<input type="hidden" id="endHour"/>
		<input type="hidden" id="endMin"/>
		<div class="tdw_time_set_in">
			<div class="tdw_time_start">
				<div class="tdw_time_hour time_set">
					<div class="tdw_tab_sort_in">
						<button class="btn_sort_on first_set"><span>09</span></button>
						<ul>
							<?for($i=1; $i < 25; $i++){?>
								<li><button class = "startTimeHour" value = "<?=str_pad($i, 2, '0', STR_PAD_LEFT)?>"><span><?=str_pad($i, 2, '0', STR_PAD_LEFT)?></span></button></li>
							<?}?>
						</ul>
					</div>
				</div>
				<div class="tdw_time_min time_set">
					<div class="tdw_tab_sort_in">
						<button class="btn_sort_on second_set"><span>00</span></button>
						<ul>
							<?for($i = 0; $i <= 50; $i += 10){?>
								<li><button class = "startTimeMin" value = "<?=($i === 0) ? '00' : $i?>"><span><?=($i === 0) ? '00' : $i?></span></button></li>
							<?}?>
						</ul>
					</div>
				</div>
			</div>
			<!-- <div class="time_wave"><span>&#126;</span></div> -->
			<div class="tdw_time_end">
				<div class="tdw_time_hour time_set">
					<div class="tdw_tab_sort_in">
						<button class="btn_sort_on first_set"><span>09</span></button>
						<ul>
							<?for($i=1; $i < 25; $i++){?>
								<li><button class = "endTimeHour" value = "<?=str_pad($i, 2, '0', STR_PAD_LEFT)?>"><span><?=str_pad($i, 2, '0', STR_PAD_LEFT)?></span></button></li>
							<?}?>
						</ul>
					</div>
				</div>
				<div class="tdw_time_min time_set">
					<div class="tdw_tab_sort_in">
						<button class="btn_sort_on second_set"><span>00</span></button>
						<ul>
							<?for($i = 0; $i <= 50; $i += 10){?>
								<li><button class = "endTimeMin" value = "<?=($i === 0) ? '00' : $i?>"><span><?=($i === 0) ? '00' : $i?></span></button></li>
							<?}?>
						</ul>
					</div>
				</div>
			</div>
		</div>
	</div>
    <div class="layer_memo_btn">
      <button class="layer_memo_cancel"><span>취소</span></button>
      <button class="layer_memo_submit layer_meet_submit"><span>등록하기</span></button>
      <!-- <button class="layer_memo_submit on"><span>등록하기</span></button> -->
    </div>
  </div>
</div>