<div class="t_layer rew_layer_login">
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
				<input type="checkbox" name="chk_login" id="chk_login" style="display:none" />
				<label for="chk_login" id="chk_login_label" style="display:none">로그인 상태 유지</label>
				<input type="checkbox" name="id_save" id="id_save" <?=$_COOKIE['id_save']?"checked":""?>/>
				<label for="id_save" id="id_save_label" >아이디 저장</label>
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