<?
//header페이지
$home_dir = str_replace( basename(__DIR__) , "" , __DIR__ );
include $home_dir . "/inc_lude/header_about.php";
?>
<div class="rb_main fp-notransition">
	<div class="rb_main_sub_img">
		<div class="join_wrap">
			<div class="join_in">
				<div class="join_slogan">
					<strong>무료체험을 시작해 볼까요?</strong>
				</div>
				<div class="join_box">
					<div class="jb_list">
						<ul>
							<li>
								<div class="tc_input">
									<input type="text" id="rewardy_join_a_com" name="company" class="input_001" placeholder="회사명" />
									<label for="j1" class="label_001">
										<strong class="label_tit">회사명</strong>
									</label>
								</div>
							</li>
							<li>
								<div class="tc_input">
									<input type="text" id="rewardy_join_a_id" name="user_id" class="input_001" placeholder="이메일 주소" />
									<label for="j2" class="label_001">
										<strong class="label_tit">이메일 주소</strong>
									</label>
								</div>
							</li>
						</ul>
					</div>
					<div class="jb_btn">
						<button id="rewardy_join_btn_a"><span>이메일 인증하기</span></button>
					</div>
					<div class="jb_descript">
						<p>리워디에서 인증을 요청합니다.<br />
						아래 링크를 클릭하셔서, 비밀번호를 설정해 주세요.<br />
						링크가 클릭되지 않으시면 아래 주소를 복사하여 인터넷 브라우저에 붙여<br />
						넣어주세요.<br />
						<br />
						https://www.rewardy.co.kr/<br />
						<br />
						기타 문의사항은 1588-8443으로 문의해 주세요.<br />
						리워디와 함께 해주셔서 감사합니다.
						</p>
					</div>
				</div>
			</div>
		</div>
	</div>
<?
//footer 페이지
include $home_dir . "/inc_lude/footer_about.php";
?>
