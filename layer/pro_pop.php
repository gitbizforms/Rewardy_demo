<div class="layer_pro" style = "display:none;">
  <div class="layer_deam"> </div>
  <div class="layer_pro_in">

    <div class="layer_close">
      <button><span>닫기</span></button>
    </div>

    <div class="layer_top">
      <strong class="layer_tit">
        프로필
      </strong>
    </div>

    <div class="layer_area">

      <div class="tl_prof_box">
	  <div class="tl_prof_img" style="background-image:url('<?=$member_row_info['profile_img_src']?$member_row_info['profile_img_src']:"/html/images/pre/img_prof_default.png"?>');" id="profile_character_img1"></div>
        <div class="tl_prof_slc">
          <div class="tl_prof_slc_in">
            <button class="char_prof"><span>프로필 변경</span></button>
            <ul>
				<li><button id="btn_slc_character"><span>캐릭터 선택</span></button></li>
				<li>
					<input type="file" id="prof" class="input_prof" />
					<label for="prof" class="label_prof" id="profile_img_change"><span>나만의 이미지 선택</span></label>
				</li>
				<li><button class="default_on" id="character_default"><span>기본 이미지로 변경</span></button></li>
			</ul>
          </div>
        </div>
      </div>

      <div class="text_area">
        <div class="text_lengh">
          <p class="text_count">0자 / 28자</p>
        </div>
        <input type="text" class="layer_text" placeholder="상태 메시지를 입력해 주세요." maxlength='28' oninput="checkLength(this)" value="<?=($member_row_info['memo'])?$member_row_info['memo']:""?>"/>
      </div>

    </div>

    <div class="submit_btn">
      <button class="btn_on">확인</button>
    </div>
  </div>
  <script>
    $(document).ready(function() {
    // 페이지 로드 시 초기 글자 수 표시
      var inputs = $('.layer_text');
      inputs.each(function () {
        var maxLength = parseInt($(this).attr('maxlength'));
        var currentLength = $(this).val().length;
        var countElement = $(this).parent().find('.text_count');
        countElement.text(currentLength + '자 / ' + maxLength + '자');
        });
      });

    function checkLength(input) {
      var maxLength = parseInt(input.getAttribute('maxlength'));
      var currentLength = input.value.length;

      // 업데이트된 글자 수 표시
      var countElement = $(input).parent().find('.text_count');
      countElement.text(currentLength + '자 / ' + maxLength + '자');

      // 최대 글자 수에 도달하면 알림 표시
      if (currentLength === maxLength) {
        alert('최대 ' + maxLength + '자까지 입력 가능합니다.');
      }
   } 
</script>

</div>