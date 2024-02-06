<?
//가입 메일을 받았을때
if($sendno){?>
	<div class="t_layer rew_layer_setting<?=($sendmail_info['highlevel']=='0')?" rew_layer_setting_02":""?>" id="rew_layer_setting" style="display:none;">
		<div class="ttl_deam"></div>
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
							<input type="text" id="z7" class="input_002" disabled value="<?=$to_email?>" />
							<label for="z7" class="label_001">
								<strong class="label_tit">이메일</strong>
							</label>
						</div>
					</li>
					<?if($sendmail_info['highlevel']=='0'){?>
						<li>
							<div class="tc_input tc_50">
								<input type="text" id="z8" class="input_002" placeholder="이름" autocomplete="false" required/>
								<label for="z8" class="label_001">
									<strong class="label_tit">이름을 입력하세요</strong>
								</label>
							</div>
							<div class="tc_input tc_50">
								<input type="text" id="z9" class="input_002" placeholder="부서명" />
								<label for="z9" class="label_001">
									<strong class="label_tit">부서명 입력하세요</strong>
								</label>
							</div>
						</li>

						<li>
							<div class="tc_input">
								<input type="text" id="z10" class="input_001" value="<?=$sendmail_info['company']?>" readonly placeholder="회사명" />
								<label for="z10" class="label_001">
									<strong class="label_tit">회사명</strong>
								</label>
							</div>
						</li>

					<?}else{?>

						<input type="hidden" id="z10" value="<?=$receive_company?>" />
						<input type="hidden" id="highlevel" value="<?=$sendmail_info['highlevel']?>" />
						<li>
							<div class="tc_input tc_50">
								<input type="text" id="z8" class="input_002" placeholder="이름" disabled value="<?=$receive_name?>" />
								<label for="z8" class="label_001">
									<strong class="label_tit">이름을 입력하세요</strong>
								</label>
							</div>
							<div class="tc_input tc_50">
								<input type="text" id="z9" class="input_002" placeholder="부서명" disabled value="<?=$receive_part?>" />
								<label for="z9" class="label_001">
									<strong class="label_tit">부서명 입력하세요</strong>
								</label>
							</div>
						</li>
					<?}?>


					<li>
						<div class="tc_input">
							<input type="password" id="z11" class="input_001" placeholder="비밀번호" autocomplete="false" required/>
							<label for="z11" class="label_001">
								<strong class="label_tit">비밀번호를 입력하세요</strong>
							</label>
						</div>
					</li>
					<li>
						<div class="tc_input">
							<input type="password" id="z12" class="input_001" placeholder="비밀번호 재확인" />
							<label for="z12" class="label_001">
								<strong class="label_tit">비밀번호를 확인하세요</strong>
							</label>
						</div>
					</li>
				</ul>
			</div>

			<div class="tl_btn">
				<?if($sendmail_info['highlevel']=='0'){?>
					<button id="rewardy_member_join_btn"><span>가입하기</span></button>
				<?}else{?>
					<button id="rewardy_member_add_join_btn"><span>가입하기</span></button>
				<?}?>
			</div>
		</div>
	</div>
<?}?>


<?
//비밀번호 재설정 메일을 받았을때
if($output){?>

	<div class="t_layer rew_layer_setting<?=($sendmail_info['highlevel']=='0')?" rew_layer_setting_02":""?>" id="rew_layer_setting" style="display:none;">
		<div class="ttl_deam"></div>
		<div class="tl_in">
			<div class="tl_close">
				<button><span>닫기</span></button>
			</div>
			<div class="tl_login_logo">
				<span>리워디</span>
			</div>
			<div class="tl_prof">
				<div class="tl_prof_box">
					<div class="tl_prof_img" style="background-image:url('<?=$member_row_info['profile_img_src']?>');" id="profile_character_img">
					</div>
					<div class="tl_prof_slc">
						<div class="tl_prof_slc_in">
							<!-- <button class="button_prof"><span>프로필 변경</span></button> -->
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
							<input type="text" id="z7" name="user_id" class="input_002" disabled value="<?=$user_id?>" />
							<label for="z7" class="label_001">
								<strong class="label_tit">이메일</strong>
							</label>
						</div>
					</li>

					<input type="hidden" id="z10" value="<?=$receive_company?>" />
					<input type="hidden" id="highlevel" value="<?=$sendmail_info['highlevel']?>" />
					<li>
						<div class="tc_input tc_50">
							<input type="text" id="z8" name="user_name" class="input_002" placeholder="이름" disabled value="<?=$user_name?>" />
							<label for="z8" class="label_001">
								<strong class="label_tit">이름을 입력하세요</strong>
							</label>
						</div>
						<div class="tc_input tc_50">
							<input type="text" id="z9" name="user_name" class="input_002" placeholder="부서명" disabled value="<?=$part_name?>" />
							<label for="z9" class="label_001">
								<strong class="label_tit">부서명 입력하세요</strong>
							</label>
						</div>
					</li>


					<li>
						<div class="tc_input">
							<input type="password" id="z11" name="user_pwd" class="input_001" placeholder="비밀번호" />
							<label for="z11" class="label_001">
								<strong class="label_tit">비밀번호를 입력하세요</strong>
							</label>
						</div>
					</li>
					<li>
						<div class="tc_input">
							<input type="password" id="z12" name="user_repwd" class="input_001" placeholder="비밀번호 재확인" />
							<label for="z12" class="label_001">
								<strong class="label_tit">비밀번호를 확인하세요</strong>
							</label>
						</div>
					</li>
				</ul>
			</div>

			<div class="tl_btn">
				<button id="member_passed_btn"><span>수정하기</span></button>
			</div>
		</div>
	</div>

<?}?>


<div class="t_layer rew_layer_member_add" style="display:none;">
	<div class="ttl_deam"></div>
	<div class="tl_in">
		<div class="tl_close">
			<button><span>닫기</span></button>
		</div>
		<div class="tl_tit">
			<strong>멤버 인원 수 등록</strong>
			<span>서비스를 이용할 인원수를 입력하세요.</span>
		</div>
		<div class="tl_list">
			<ul>
				<li>
					<div class="tc_input">
						<div class="count_area">
							<button class="btn_count_minus count_limit"><span>빼기</span></button>
							<input type="text" class="input_count" value="1" />
							<button class="btn_count_plus"><span>더하기</span></button>
						</div>
					</div>
				</li>
			</ul>
		</div>
		<div class="tl_btn">
			<button><span>확인</span></button>
		</div>
	</div>
</div>
