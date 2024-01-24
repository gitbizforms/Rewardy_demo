<?php
$end_date = date("Y-m-d", strtotime("+".repeatday." day", TODAYTIME));
$today_m = date("m", strtotime(TODATE));
$today_d = date("d", strtotime(TODATE));




?>
<head>
  <link rel="stylesheet" href="/html/css/replay_popup.css">
  <script src="/js/replay_popup.js"></script>
</head>

<body>
<div class = "replay_popup" style = "display : none;">
  <div class="background_black"></div>
  <div class="replay_set">
    <div class="replay_tab_navi" value = "day">
      <ul>
        <li class="day on" value = "day"><button><span>매일</span></button></li>
        <li class="week" value = "week"><button><span>매주</span></button></li>
        <li class="month" value = "month"><button><span>매달</span></button></li>
        <li class="year" value = "year"><button><span>매년</span></button></li>
      </ul>
    </div>
    <div class="replay_checkbox" id = "work_replay">
      <div class="replay_day on">
        <div class="replay_title">
          <strong>매일 반복<br>
          </strong>  
        </div>
        <div class="replay_day_setbox replay_setbox">
          <ul>
            <li>
              <div class="replay_day_set">
                <input type="radio" id="check_day_01" name="day" checked><label for="check_day_01">매일
                  반복 </label>
                <input type="radio" id="check_day_02" name="day" value = "noweek"><label for="check_day_02">평일 반복(월~금)</label>
                <input type="radio" id="check_day_03" name="day"><label for="check_day_03">
                  <div class="day_setting">
                     <input type = "text" id = "day_setting">
                  </div>
                  <span>일 간격으로 반복</span>
                </label>
              </div>
            </li>
            <li>
              <div class="replay_set_play">
                <input type="radio" id="check_play_day_02" name="check_play_day" value="check_play_day_02"><label for="check_play_day_02">종료 날짜
                  <div class="check_end_day_select check_end_select">
                  <input type="date" id="closeDateDay"  min = "<?php echo TODATE?>" max = "<?php echo $end_date?>">
                  </div>
                </label>
              </div>
            </li>
          </ul>
        </div>
      </div>
      <div class="replay_week">
        <div class="replay_title_week">
          <strong>반복 설정
          </strong>
        </div>
        <div class="replay_week_setbox replay_setbox">
          <ul>
            <li>
              <div class="replay_week_set">
                <input type="checkbox" id="mon" name="week" value = '1'><label for="mon">월</label>
                <input type="checkbox" id="tue" name="week" value = '2'><label for="tue">화</label>
                <input type="checkbox" id="wed" name="week" value = '3'><label for="wed">수</label>
                <input type="checkbox" id="thu" name="week" value = '4'><label for="thu">목</label>
                <input type="checkbox" id="fri" name="week" value = '5'><label for="fri">금</label>
                <input type="checkbox" id="sat" name="week" value = '6'><label for="sat">토</label>
                <input type="checkbox" id="sun" name="week" value = '0'><label for="sun">일</label>
              </div>
              <div class="week_replay_type">
                <input type="checkbox" id="week" name="count" checked>
                <label for="week">
                  <div class="choice_week">
                    <div class="week_setting">
                      <button class="week_setting_btn on" value = '1'><span>1</span></button>
                      <ul>
                        <li><button value="1"><span>1</span></button></li>
                        <li><button value="2"><span>2</span></button></li>
                        <li><button value="3"><span>3</span></button></li>
                        <li><button value="4"><span>4</span></button></li>
                      </ul>
                    </div>
                </label>
              </div>
              <span>주 간격으로 반복</span>
            </li>
            <li>
             <div class="replay_set_play">
                <input type="radio" id="check_play_week_02" name="check_play_week" value="check_play_week_02"><label for="check_play_week_02">종료 날짜
                  <div class="check_end_week_select check_end_select">
                  <input type="date" id="closeDateWeek"  min = "<?php echo TODATE?>" max = "<?php echo $end_date?>">
                  </div>
                </label>
              </div>
            </li>
            <li>
              <div class="replay_set_cancel">
                <input type="checkbox" id="cancel_week"><label for="cancel_week">반복취소</label>
              </div>
            </li>
          </ul>
        </div>
      </div>
      <div class="replay_month">
        <div class="replay_title">
          <strong>매월 <span class="month_d"></span>일 반복</strong>
        </div>
        <div class="replay_month_setbox replay_setbox">
          <ul>
            <li>
              <div class="replay_month_set">
                <input type="radio" id="check_month_01" name="month" checked><label for="check_month_01">매월 <span class="month_d"></span>일 마다
                  반복</label>
                <input type="radio" id="check_month_02" name="month"><label for="check_month_02">매월 4번째 금요일에 반복</label>
                <input type="radio" id="check_month_03" name="month"><label for="check_month_03">매월 마지막 주 금요일에
                  반복</label>
              </div>
            </li>
            <li>
              <div class="replay_set_play">
                <input type="radio" id="check_play_month_02" name="check_play_month" value="check_play_month_02"><label for="check_play_month_02">종료 날짜
                  <div class="check_end_month_select check_end_select">
                  <input type="date" id="closeDateMonth"   min = "<?php echo TODATE?>" max = "<?php echo $end_date?>">
                  </div>
                </label>
              </div>
            </li>
            <li>
              <div class="replay_set_cancel">
                <input type="checkbox" id="cancel_month"><label for="cancel_month">반복취소</label>
              </div>
            </li>
          </ul>
        </div>
      </div>
      <div class="replay_year">
        <div class="replay_title">
          <strong>매년 <span class="year_m"></span>월 <span class="year_d"></span>일 반복</strong>
        </div>
        <div class="replay_week_setbox replay_setbox">
          <ul>
            <li>
              <div class="replay_year_set">
                <input type="checkbox" id="year" name="year" checked><label for="yaer">매년 <span class="year_m"></span>월 <span class="year_d"></span>일  반복</label>
              </div>
            </li>
            <li>
               <div class="replay_set_play">
                <input type="radio" id="check_play_month_02" name="check_play_month" value="check_play_month_02"><label for="check_play_month_02">종료 날짜
                  <div class="check_end_month_select check_end_select">
                  <input type="date" id="closeDateYear" min = "<?php echo TODATE?>" max = "<?php echo $end_date?>">
                  </div>
                </label>
              </div>
            </li>
            <li>
              <div class="replay_set_cancel">
                <input type="checkbox" id="cancel"><label for="cancel">반복취소</label>
              </div>
            </li>
          </ul>
        </div>
      </div>
    </div>
    <div class="replay_set_button">
      <div class="set_button">
        <ul>
          <li class="cancel_button"><button><span>취소</span></button></li>
          <li class="submit_button"><button><span>적용하기</span></button></li>
        </ul>
      </div>
    </div>
  </div>
</div>
</body>