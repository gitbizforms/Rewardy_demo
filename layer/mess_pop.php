<div class="layer_mess" style = "display:none;">
    <div class="layer_deam"></div>
		<input type="hidden" value="0" class="user_id">
    <div class="layer_mess_in">
      <div class="mess_user">
        <div class="mess_user_img">
          <div class="mess_user_img_in" style="background-image:url(images/pre/img_prof_02.png);"></div>
        </div>
        <div class="mess_name">
          <div class="mess_name_user">윤지혜</div>
          <div class="mess_name_team">디자인팀</div>
        </div>
      </div>
      <div class="layer_mess_box">
        <textarea name="textarea_mess" class="textarea_mess" placeholder="보낼 메시지를 작성해주세요." id="textarea_mess" maxlength = '100' oninput="checkLengthMess(this)" ></textarea>
        <span class = "message_count">0자 / 100자</span>
        <p>※쪽지는 받는 사람만 볼 수 있습니다.</p>
      </div>
      <div class="layer_mess_btn">
        <button class="layer_mess_cancel"><span>취소</span></button>
        <button class="layer_mess_submit"><span>보내기</span></button>
      </div>
    </div>
      <script>
          function checkLengthMess(input) {
            var maxLength = parseInt(input.getAttribute('maxlength'));
            var currentLength = input.value.length;

            // 업데이트된 글자 수 표시
            var countElement = $(input).parent().find('.message_count');
            countElement.text(currentLength + '자 / ' + maxLength + '자');

            // 최대 글자 수에 도달하면 알림 표시
            if (currentLength === maxLength) {
              alert('최대 ' + maxLength + '자까지 입력 가능합니다.');
            }
        } 
      </script>
  </div>
