<?
	//header페이지
	$home_dir = __DIR__;
	include $home_dir . "/inc_lude/header.php";
?>

<div class="demo_s_wrap">
    <!-- <img src="images/pre/도입부페이지.png" alt="" style="opacity: 0.6; position: fixed; top: 0; left: 0; z-index: 2;"> -->
    <div class="demo_s_wrap_in">
      <div class="demo_s_tit">
        <h1><strong>우리회사</strong>의 <strong>문화</strong>를 바꾸다.</h1>
        <span>세상에서 가장 재미있게 일하는 보상 플랫폼, 리워디에 오신 걸 환영합니다! <br>
          아래의 직무별 캐릭터를 통해서 직접 리워디를 체험해 보세요.</span>
      </div>
      <div class="demo_cha">
        <div class="demo_user_01 demo_user" value = "게스트01@rewardy.co.kr">
          <div class="user_img"><img src="html/images/pre/demo_user_01.png" alt="user_01"></div>
          <div class="user_name">
            <p>김기획</p>
            <span>기획팀/사원</span>  
          </div>
          <button class = "demo_loginbtn"><span>입장하기</span></button>
        </div>
        <div class="demo_user_02 demo_user" value = "게스트03@rewardy.co.kr">
          <div class="user_img"><img src="html/images/pre/demo_user_02.png" alt="user_02"></div>
          <div class="user_name">
            <p>박마케터</p>
            <span>마케팅팀/사원</span>
          </div>
          <button class = "demo_loginbtn"><span>입장하기</span></button>
        </div>
        <div class="demo_user_03 demo_user" value = "게스트02@rewardy.co.kr">
          <div class="user_img"><img src="html/images/pre/demo_user_03.png" alt="user_03"></div>
          <div class="user_name">
            <p>윤디자인</p>
            <span>디자인팀/사원</span>
          </div>
          <button class = "demo_loginbtn"><span>입장하기</span></button>
        </div>
        <div class="demo_user_04 demo_user" value = "게스트05@rewardy.co.kr">
          <div class="user_img"><img src="html/images/pre/demo_user_04.png" alt="user_04"></div>
          <div class="user_name">
          <p>유개발</p>
          <span>개발팀/대리</span>
          </div>
          <button class = "demo_loginbtn"><span>입장하기</span></button>
        </div>
        <div class="demo_user_05 demo_user" value = "게스트04@rewardy.co.kr">
          <div class="user_img"><img src="html/images/pre/demo_user_05.png" alt="user_05"></div>
          <div class="user_name">
          <p>정영업</p>
          <span>영업팀/사원</span>
          </div>
          <button class = "demo_loginbtn"><span>입장하기</span></button>
        </div>
      </div>
      <ul class="demo_slide">
        <li class="demo_slide_01">
          <em>LIVE</em>
          <h2>한눈에 보이는 <br>
            회사현황</h2>
        </li>
        <li class="demo_slide_02">
          <em>REWARD</em>
          <h2>구성원들이 신나게 일하는 <br>
            가장 확실한 방법!</h2>
        </li>
        <li class="demo_slide_03">
          <em>CHALLENGE</em>
          <h2>우리 회사의 문화를 <br>
            긍정적으로 만들어 줍니다</h2>
        </li>
        <li class="demo_slide_04">
          <em>PARTY</em>
          <h2>세상에서 가장 재미있게 <br>
            일하는 방법! </h2>
        </li>
        <li class="demo_slide_05">
          <em>INSIGHT</em>
          <h2>나의 현재를 명확하게 알면 <br>
            내일이 더 좋아집니다 </h2>
        </li>
        <li class="demo_slide_06">
          <em>TODAY WORK</em>
          <h2>스스로 적극적으로 일하는 <br>
            구성원으로 만듭니다</h2>
        </li>
      </ul>
    </div>
  </div>

	<!-- footer start-->
	<? include $home_dir . "/inc_lude/footer.php";?>
	<!-- footer end-->

<script>
    $(document).ready(function(){
      $('.demo_slide').bxSlider({
        slideWidth: 384,
        minSlides: 1,
        maxSlides: 6,
        moveSlides: 1,
        slideMargin: 20,
        pager: false,
        controls: false,
        infiniteLoop: true,
        auto: true,
        speed: 500,
      });
    })
  </script>
</body>
</html>