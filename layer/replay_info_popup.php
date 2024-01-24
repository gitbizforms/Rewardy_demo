<?php
$end_date = date("Y-m-d", strtotime("+".repeatday." day", TODAYTIME));
$today_m = date("m", strtotime(TODATE));
$today_d = date("d", strtotime(TODATE));




?>
<head>
  <link rel="stylesheet" href="/html/css/replay_info_popup.css">
  <script src="/js/replay_popup.js"></script>
</head>

<body>
  <div class="layer_re_info" style = "display:none;">
    <div class="layer_deam"></div>
    <div class="layer_re_info_in">
      <div class="layer_re_info_tit"><strong>반복설정 관련 안내</strong></div>
      <div class="layer_re_info_box">
        <span>
          반복설정 옵션의 기능이 업데이트 됨에 따라</br>
          ‘리뉴얼 이전에 설정한 반복 기능’은</br>
          설정값을 해제한 후 수정이 가능합니다.</br>
        </br>
          해당 업무의 반복 옵션 수정이 필요한 경우</br>
          현재 레이어에서 [반복 해제]를 누른 후,</br>
          다시 반복설정을 진행하실 수 있습니다.
        </span>
      </div>
      <div class="layer_re_info_btn">
        <button class="layer_re_info_cancel"><span>취소</span></button>
        <button class="layer_re_info_submit"><span>반복해제</span></button>
      </div>
    </div>
  </div>
</body>