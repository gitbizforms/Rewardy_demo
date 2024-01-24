<?
//join, center파일에만 있는 레이어
$join = "join.php";
$cen = "center.php";
if (basename($_SERVER['PHP_SELF']) == $join || basename($_SERVER['PHP_SELF']) == $cen){

?>

	<div class="t_layer rew_layer_ask" style="display:none;">
		<div class="tl_deam"></div>
		<div class="tl_in">
			<div class="tl_close">
				<button><span>닫기</span></button>
			</div>
			<div class="tl_login_logo">
				<span>리워디</span>
			</div>
			<div class="tl_tit">
				<strong>문의하기</strong>
			</div>
			<div class="tl_list">
				<ul>
					<li class="col_50">
						<div class="tc_input">
							<input type="text" id="k1" class="input_001" placeholder="이름" />
							<label for="k1" class="label_001">
								<strong class="label_tit">이름</strong>
							</label>
						</div>
					</li>
					<li class="col_50">
						<div class="tc_input">
							<input type="text" id="k2" class="input_001" placeholder="연락처" />
							<label for="k2" class="label_001">
								<strong class="label_tit">연락처</strong>
							</label>
						</div>
					</li>
					<li>
						<div class="tc_input">
							<input type="text" id="k3" class="input_001" placeholder="이메일" />
							<label for="k3" class="label_001">
								<strong class="label_tit">이메일</strong>
							</label>
						</div>
					</li>
					<li>
						<div class="tc_input">
							<input type="text" id="k4" class="input_001" placeholder="제목" />
							<label for="k4" class="label_001">
								<strong class="label_tit">제목</strong>
							</label>
						</div>
					</li>
					<li>
						<div class="tc_input">
							<textarea id="k5" class="input_001" placeholder="문의사항"></textarea>
							<label for="k5" class="label_001">
								<strong class="label_tit">문의사항</strong>
							</label>
						</div>
					</li>
				</ul>
			</div>
			<div class="tl_chk_wrap">
				<div class="tl_chk">
					<input type="checkbox" name="chk_agree" id="chk_agree" />
					<label for="chk_agree"><span>개인정보처리방침</span>에 동의합니다.</label>
				</div>
				<div class="tl_descript tl_agree_box">
					<p>개인정보처리방침 내용입니다. 개인정보처리방침 내용입니다.<br />
					개인정보처리방침 내용입니다. 개인정보처리방침 내용입니다. 개인정보처리방침 내용입니다.</p>
				</div>
			</div>
			<div class="tl_btn">
				<button id=""><span>문의하기</span></button>
			</div>
		</div>
	</div>

	<div class="t_layer rew_layer_repass" style="display:none;">
		<div class="tl_deam"></div>
		<div class="tl_in">
			<div class="tl_close">
				<button><span>닫기</span></button>
			</div>
			<div class="tl_login_logo">
				<span>리워디</span>
			</div>
			<div class="tl_tit">
				<strong>비밀번호 재설정</strong>
				<span>비밀번호를 초기화할 수 있는 링크를 보내드립니다.<br />
				리워디에 가입한 이메일 주소를 입력해 주세요.</span>
			</div>
			<div class="tl_list">
				<ul>
					<li>
						<div class="tc_input">
							<input type="text" id="z3" name="user_id" class="input_001" placeholder="이메일" />
							<label for="z3" class="label_001">
								<strong class="label_tit">이메일을 입력하세요</strong>
							</label>
						</div>
					</li>
				</ul>
			</div>
			<div class="tl_btn">
				<button><span>비밀번호 재설정 메일 보내기</span></button>
			</div>
			<div class="tl_back">
				<button><span>이전으로</span></button>
			</div>
		</div>
	</div>

	<div class="t_layer rew_layer_reserv" style="display:none;">
		<div class="tl_deam"></div>
		<div class="tl_in">
			<div class="tl_close">
				<button><span>닫기</span></button>
			</div>
			<div class="tl_login_logo">
				<span>리워디</span>
			</div>
			<div class="tl_tit">
				<strong>전화상담 예약</strong>
			</div>
			<div class="tl_list">
				<ul>
					<li>
						<div class="tc_input">
							<input type="text" id="r1" name="" class="input_001" placeholder="이름" />
							<label for="r1" class="label_001">
								<strong class="label_tit">이름</strong>
							</label>
						</div>
					</li>
					<li>
						<div class="tc_input">
							<input type="text" id="r2" name="" class="input_001" placeholder="연락처" />
							<label for="r2" class="label_001">
								<strong class="label_tit">연락처</strong>
							</label>
						</div>
					</li>
				</ul>
			</div>
			<div class="tl_btn">
				<button><span>예약하기</span></button>
			</div>
			<div class="tl_descript">
				<p>※ 오전 10~11시, 오후 2~4시에는 전화상담이 많아 연결이 원활하지 못하니 성함과 연락처를 남겨 주시면 빠른 시간 안에 연락드리겠습니다.<br />
				<br />※ 평일 오후 6시 이후, 토/일/공휴일에 전화상담을 예약하시면 업무 복귀 후 바로 연락드리게 됩니다. 양해 바랍니다.</p>
			</div>
		</div>
	</div>
<? } ?>

<div class="t_layer rew_layer_join" style="display:none;">
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
							<input type="text" id="z5" name="user_id" class="input_001" placeholder="이메일" />
							<label for="z5" class="label_001">
								<strong class="label_tit">이메일을 입력하세요</strong>
							</label>
						</div>
					</li>
				</ul>
			</div>
			<div class="tl_btn">
				<button><span>인증메일 발송</span></button>
			</div>
			<div class="tl_descript">
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

	<div class="t_layer rew_layer_repass" style="display:none;">
		<div class="tl_deam"></div>
		<div class="tl_in">
			<div class="tl_close">
				<button><span>닫기</span></button>
			</div>
			<div class="tl_login_logo">
				<span>리워디</span>
			</div>
			<div class="tl_tit">
				<strong>비밀번호 재설정</strong>
				<span>비밀번호를 초기화할 수 있는 링크를 보내드립니다.<br />
				리워디에 가입한 이메일 주소를 입력해 주세요.</span>
			</div>
			<div class="tl_list">
				<ul>
					<li>
						<div class="tc_input">
							<input type="text" id="z3" name="user_id" class="input_001" placeholder="이메일" />
							<label for="z3" class="label_001">
								<strong class="label_tit">이메일을 입력하세요</strong>
							</label>
						</div>
					</li>
				</ul>
			</div>
			<div class="tl_btn">
				<button><span>비밀번호 재설정 메일 보내기</span></button>
			</div>
			<div class="tl_back">
				<button><span>이전으로</span></button>
			</div>
		</div>
	</div>

	<div class="t_layer rew_layer_login" style="display:none;">
		<div class="tl_deam"></div>
		<div class="tl_in">
			<div class="tl_close">
				<button><span>닫기</span></button>
			</div>
			<div class="tl_login_logo">
				<span>리워디</span>
			</div>
			<div class="tl_tit">
				<strong>로그인</strong>
				<span>안녕하세요. <br />로그인을 부탁드려요!</span>
			</div>
			<div class="tl_list">
				<ul>
					<li>
						<div class="tc_input">
							<input type="text" id="z1" name="user_id" class="input_001" placeholder="이메일" />
							<label for="z1" class="label_001">
								<strong class="label_tit">이메일을 입력하세요</strong>
							</label>
						</div>
					</li>
					<li>
						<div class="tc_input">
							<input type="password" id="z2" name="user_pwd" class="input_001" placeholder="비밀번호" />
							<label for="z2" class="label_001">
								<strong class="label_tit">비밀번호를 입력하세요</strong>
							</label>
						</div>
					</li>
				</ul>
			</div>
			<div class="tl_chk_wrap">
				<div class="tl_chk">
					<input type="checkbox" name="chk_login" id="chk_login" />
					<label for="chk_login">로그인 상태 유지</label>
				</div>
				<ul>
					<li>
						<button>가입하기</button>
					</li>
					<li>
						<button>비밀번호 재설정</button>
					</li>
				</ul>
			</div>
			<div class="tl_btn">
				<button id="loginbtn"><span>로그인</span></button>
			</div>
		</div>
	</div>

	<div class="t_layer rew_layer_setting" style="display:none;">
		<div class="tl_deam"></div>
		<div class="tl_in">
			<div class="tl_close">
				<button><span>닫기</span></button>
			</div>
			<div class="tl_login_logo">
				<span>리워디</span>
			</div>
			<div class="tl_prof">
				<div class="tl_prof_box">
					<div class="tl_prof_img">
					</div>
					<div class="tl_prof_slc">
						<div class="tl_prof_slc_in">
							<button class="button_prof"><span>프로필 변경</span></button>
							<ul>
								<li>
									<input type="file" id="prof" class="input_prof" />
									<label for="prof" class="label_prof"><span>사진 변경</span></label>
								</li>
								<li><button id="btn_slc_character"><span>캐릭터 선택</span></button></li>
								<li><button class="default_on"><span>기본 이미지로 변경</span></button></li>
							</ul>
						</div>
					</div>
				</div>
			</div>

			<div class="tl_list">
				<ul>
					<li>
						<div class="tc_input">
							<input type="text" id="z7" name="user_id" class="input_002" disabled value="young@bizforms.co.kr" />
							<label for="z7" class="label_001">
								<strong class="label_tit">이메일</strong>
							</label>
						</div>
					</li>
					<li>
						<div class="tc_input tc_50">
							<input type="text" id="z8" name="user_name" class="input_002" disabled placeholder="윤지혜" />
							<label for="z8" class="label_001">
								<strong class="label_tit">이름을 입력하세요</strong>
							</label>
						</div>
						<div class="tc_input tc_50">
							<input type="text" id="z9" name="user_name" class="input_002" disabled placeholder="디자인팀" />
							<label for="z9" class="label_001">
								<strong class="label_tit">이름을 입력하세요</strong>
							</label>
						</div>
					</li>
					<li>
						<div class="tc_input">
							<input type="password" id="z10" name="user_pwd" class="input_001" placeholder="비밀번호" />
							<label for="z10" class="label_001">
								<strong class="label_tit">비밀번호를 입력하세요</strong>
							</label>
						</div>
					</li>
					<li>
						<div class="tc_input">
							<input type="password" id="z11" name="user_repwd" class="input_001" placeholder="비밀번호 재확인" />
							<label for="z11" class="label_001">
								<strong class="label_tit">비밀번호를 확인하세요</strong>
							</label>
						</div>
					</li>
				</ul>
			</div>

			<div class="tl_btn">
				<button><span>가입하기</span></button>
			</div>
		</div>
	</div>

	<div class="t_layer rew_layer_character" style="display:none;">
		<div class="tl_deam"></div>
		<div class="tl_in">
			<div class="tl_close">
				<button><span>닫기</span></button>
			</div>
			<div class="tl_tit">
				<strong>캐릭터 설정</strong>
				<span>리워디에서 기본으로 제공하는 <br />캐릭터입니다.</span>
			</div>
			<div class="tl_profile">
				<ul>
					<li>
						<div class="tl_profile_box">
							<div class="tl_profile_img" style="background-image:url(/about/images/pre/img_prof_01.png);">
								<button class="btn_profile" id="profile_img_01"><span>기본 프로필 이미지1 선택</span></button>
							</div>
						</div>
					</li>
					<li>
						<div class="tl_profile_box">
							<div class="tl_profile_img" style="background-image:url(/about/images/pre/img_prof_02.png);">
								<button class="btn_profile" id="profile_img_02"><span>기본 프로필 이미지2 선택</span></button>
							</div>
						</div>
					</li>
					<li>
						<div class="tl_profile_box">
							<div class="tl_profile_img" style="background-image:url(/about/images/pre/img_prof_03.png);">
								<button class="btn_profile" id="profile_img_03"><span>기본 프로필 이미지3 선택</span></button>
							</div>
						</div>
					</li>
					<li>
						<div class="tl_profile_box">
							<div class="tl_profile_img" style="background-image:url(/about/images/pre/img_prof_04.png);">
								<button class="btn_profile" id="profile_img_04"><span>기본 프로필 이미지4 선택</span></button>
							</div>
						</div>
					</li>
				</ul>
			</div>
			<div class="tl_btn">
				<button><span>적용</span></button>
			</div>
		</div>
	</div>

	<div class="rew_qck" style="display:none;">
		<button class="btn_open_join"><span>회원가입</span></button>
		<button class="btn_open_login"><span>로그인</span></button>
		<button class="btn_open_repass"><span>비밀번호 재설정</span></button>
		<button class="btn_open_setting"><span>프로필 변경</span></button>
	</div>

<script type="text/javascript">
	$(document).ready(function(){

		<?
		//join, center파일에만 있는 레이어 js
		if (basename($_SERVER['PHP_SELF']) == $join || basename($_SERVER['PHP_SELF']) == $cen){
		?>
			$(".btn_layer_ask").click(function(){
				$(".rew_layer_ask").show();
			});
			$(".btn_layer_reserv").click(function(){
				$(".rew_layer_reserv").show();
			});
		<?}?>

		$(".btn_open_join, .open_layer_join").click(function(){
			$(".rew_layer_join").show();
		});
		$(".btn_open_login, .open_layer_login").click(function(){
			$(".rew_layer_login").show();
		});
		$(".btn_open_repass").click(function(){
			$(".rew_layer_repass").show();
		});
		$(".btn_open_setting").click(function(){
			$(".rew_layer_setting").show();
		});
		$(".tl_close button").click(function(){
			$(this).closest(".t_layer").hide();
		});

		$(".button_prof").click(function(){
			$(".tl_prof_slc ul").show();
		});
		$("#btn_slc_character").click(function(){
			$(".rew_layer_character").show();
		});
		$(".rew_layer_character .tl_btn").click(function(){
			$(".rew_layer_character").hide();
		});
		$(".btn_profile").click(function(){
			$(".btn_profile").removeClass("on");
			$(this).addClass("on");
		});
		$(".tl_prof_slc").mouseleave(function(){
			$(".tl_prof_slc ul").hide();
		});
	});
</script>
