<?
	//header페이지
	$home_dir = str_replace( basename(__DIR__) , "" , __DIR__ );
	include $home_dir . "/inc_lude/header.php";
?>
<div class="rew_warp">
	<div class="rew_warp_in">
		<div class="rew_box">
			<div class="rew_box_in">
				<!-- menu -->
				<div class="rew_layer_login">
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
						<input type="text" id="z1" name="user_id" class="input_001" placeholder="이메일" <?=$_COOKIE['id_save']?" value=".$_COOKIE['cid']."":""?> />
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
				<?/*<input type="checkbox" name="chk_login" id="chk_login" />
				<label for="chk_login">로그인 상태 유지</label>
				*/?>
				<input type="checkbox" name="id_save" id="id_save" <?=$_COOKIE['id_save']?"checked":""?>/>
				<label for="id_save">아이디 저장</label>
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
				<!-- //menu -->

				<!-- 콘텐츠 -->
				<div class="rew_conts">
					<div class="rew_conts_in">

							<div class="rew_intro">
								<div class="rew_intro_in">
									<div class="rew_intro_bar"></div>
									<div class="rew_intro_box">
										<img src="/html/images/pre/img_intro.png" alt="Rewardy" />
										<strong>우리 회사의 문화를 바꾸다.</strong>
										<?if($user_id){?>
											<button id="logout"><span>로그아웃</span></button>
											<button id="btn_repass"><span>비밀번호 재설정</span></button>
										<?}else{?>
											<button id="login"><span>로그인</span></button>
										<?}?>
									</div>
								</div>
							</div>


					</div>
				</div>
				<!-- //콘텐츠 -->

				<?php
					//비밀번호 재설정
					include $home_dir . "/layer/member_repass.php";
				?>

			</div>
		</div>
	</div>
	<div class="rew_q">
		<a href="01.html" target="_blank">(구)버전</a>
		<a href="002.html" target="_blank">(신)버전</a>
		<a href="0001.html" target="_blank">(리뉴얼)버전</a>
	</div>
	
</div>

	<!-- footer start-->
	<? include $home_dir . "/inc_lude/footer.php";?>
	<!-- footer end-->
</body>
</html>

