<div class="layer_time" style = "display:none;">
  <div class="layer_deam"></div>
  <div class="layer_time_in">
    <div class="layer_title">
      <div class="layer_title_in">
        <p>일정 변경하기</p>
        <em>일정 및 시간을 변경합니다.</em>
      </div>
    </div>
    <div class="layer_btn">
      <ul>
        <li><button><span value = "2">반차</span></button></li>
        <li><button><span value = "3">외출</span></button></li>
        <li><button><span value = "4">조퇴</span></button></li>
        <li><button><span value = "6">교육</span></button></li>
        <li><button><span value = "7">미팅</span></button></li>
      </ul>
    </div>
    <div class="layer_time_set">
      <div class="layer_time_set_in off">
        <div class="tdw_time_start">
          <div class="tdw_time_hour time_set">
            <div class="tdw_tab_sort_in">
              <button class="btn_sort_on first_set"><span>09</span></button>
                <ul>
                  <?for($i=1; $i < 25; $i++){?>
                    <li><button class = "startTimeHour" value = "<?=$i?>"><span><?=$i?></span></button></li>
                  <?}?>
                </ul>
            </div>
          </div>
          <div class="tdw_time_min time_set">
            <div class="tdw_tab_sort_in">
              <button class="btn_sort_on second_set"><span>00</span></button>
                <ul>
                  <?for($i = 0; $i <= 50; $i += 10){?>
                    <li><button class = "startTimeMin" value = "<?=($i === 0) ? '00' : $i?>"><span><?=$i?></span></button></li>
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
                    <li><button class = "endTimeHour" value = "<?=$i?>"><span><?=$i?></span></button></li>
                  <?}?>
                </ul>
            </div>
          </div>
          <div class="tdw_time_min time_set">
            <div class="tdw_tab_sort_in">
              <button class="btn_sort_on second_set"><span>00</span></button>
                <ul>
                  <?for($i = 0; $i <= 50; $i += 10){?>
                    <li><button class = "endTimeMin" value = "<?=($i === 0) ? '00' : $i?>"><span><?=$i?></span></button></li>
                  <?}?>
                </ul>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="layer_time_btn">
      <button class="layer_time_cancel"><span>취소</span></button>
      <button class="layer_time_submit"><span>일정취소</span></button>
      <button class="layer_time_submit on"><span>등록하기</span></button>
    </div>
  </div>
</div>