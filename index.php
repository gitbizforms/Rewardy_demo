<?
	//header페이지
	$home_dir = __DIR__;
	include $home_dir . "/inc_lude/header.php";
?>

	<div class="rew_warp" style="background:#fff;">
		<div class="rew_intro">
			<div class="rew_intro_in">
				<div class="rew_intro_box">
					<img src="/html/images/pre/img_intro.png" alt="Rewardy" />
					<strong>우리 회사의 문화를 바꾸다.</strong>
					<button id="rewardy_team"><span>접속하기</span></button>
				</div>
			</div>
		</div>


		<div class="t_layer rew_layer_join" style="display:none;" id="rewardy_layer_join">
			<div class="tl_deam"></div>
			<div class="tl_in">
				<div class="tl_close">
					<button><span>닫기</span></button>
				</div>
				<div class="tl_login_logo">
					<span>리워디</span>
				</div>
				<div class="tl_tit">
					<strong>가입하기</strong>
					<span>리워디에서 인증을요청합니다. <br />
					리워디와 함께 하세요!</span>
				</div>
				<div class="tl_list">
					<ul>
						<li>
							<div class="tc_input">
								<input type="text" id="rewardy_join_id" name="user_id" class="input_001" placeholder="이메일" />
								<label for="z5" class="label_001">
									<strong class="label_tit">이메일을 입력하세요</strong>
								</label>
							</div>
						</li>
					</ul>
				</div>
				<div class="tl_btn">
					<button id="rewardy_join_btn"><span>인증메일 발송</span></button>
				</div>
				<div class="tl_descript">
					<p>리워디에서 인증을 요청합니다.<br />
					아래 링크를 클릭하셔서, 비밀번호를 설정해 주세요.<br />
					링크가 클릭되지 않으시면 아래 주소를 복사하여 인터넷 브라우저에<br />
					붙여넣어주세요.<br />
					<br />
					https://www.rewardy.co.kr/team/<br />
					<br />
					기타 문의사항은 1588-8443으로 문의해 주세요.<br />
					리워디와 함께 해주셔서 감사합니다.
					</p>
				</div>
			</div>
		</div>
	</div>

	<!-- footer start-->
	<? include $home_dir . "/inc_lude/footer.php";?>
	<!-- footer end-->
</body>
</html>
