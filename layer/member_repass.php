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
			<span>비밀번호를 변경할 수 있습니다.<br>
			현재 사용중인 비밀번호를 입력해주세요.</span>
		</div>
		<div class="tl_list">
			<ul>
				<li>
					<div class="tc_input">
						<input type="password" id="ori_passwd" name="user_id" class="input_001" placeholder="현재 사용중인 비밀번호" value="">
						<label for="z3" class="label_001">
							<strong class="label_tit">현재 비밀번호를 입력해주세요</strong>
						</label>
						<input type="hidden" id="user_email" value="<?=$user_id?>">
					</div>
				</li>
			</ul>
		</div>
		<div class="tl_btn">
			<!-- <button id="tl_sendmail_btn"><span>비밀번호 재설정 하기</span></button> -->
			<button id="create_new_pw">새 비밀번호 입력</button>
		</div>
		<!-- <div class="tl_back">
			<button><span>이전으로</span></button>
		</div> -->
	</div>
</div>