<?php
	//header페이지
	$home_dir = str_replace( basename(__DIR__) , "" , __DIR__ );
	include $home_dir . "/inc_lude/header.php";


	//맴버 추가 횟수
	if ($_POST['number']){
		$number = preg_replace("/[^0-9]/", "", $_POST['number']);
	}
?>

<div class="rew_warp">
	<div class="rew_warp_in">
		<div class="rew_box">
			<div class="rew_box_in">
				<!-- menu -->
				<? include $home_dir . "/inc_lude/menu.php";?>
				<!-- //menu -->

				<!-- 콘텐츠 -->
				<div class="rew_conts">
					<div class="rew_conts_in">
						<!-- <div class="rew_header">
							<div class="rew_header_in">
								<div class="rew_header_notice">
									<span></span>
								</div>
							</div>
						</div> -->

						<div class="rew_member_tab">
							<div class="rew_member_tab_in">
								<ul>
									<li class="on"><a href="#"><span>멤버관리</span></a></li>
									<li><a href="#"><span>공용코인 관리</span></a></li>
									<li><a href="#"><span>멤버별 공용코인</span></a></li>
								</ul>
							</div>
						</div>

						<div class="rew_conts_scroll_02">

							<div class="rew_member">
								<div class="rew_member_in">

									<div class="rew_member_func">
										<div class="rew_member_func_in">
											<div class="rew_member_count">
												<span>멤버정보입력</span>
												<em>기재하신 이메일로 초대장이 발송됩니다.</em>
											</div>

											<div class="rew_member_btns">
												<div class="rew_member_btns_in">
													<button class="btn_excel_upload"><span>엑셀 업로드</span></button>
												</div>
											</div>
										</div>
									</div>

									<div class="rew_member_inputs">
										<div class="rew_member_inputs_in">
											<ul>



											<?for($i=0; $i<$number; $i++){?>
												<li>
													<div class="member_inputs_num">
														<strong><?=($i+1)?></strong>
													</div>
													<div class="member_inputs_name">
														<input type="text" class="inputs_member_name" placeholder="이름" id="member_name<?=$i?>"/>
													</div>
													<div class="member_inputs_team">
														<input type="text" class="inputs_member_team" placeholder="부서명" id="member_part<?=$i?>"/>
													</div>
													<div class="member_inputs_email">
														<input type="text" class="inputs_member_email" placeholder="이메일" id="mail<?=$i?>"/>
														<div class="rew_member_inputs_sort">
															<div class="rew_member_inputs_sort_in">
																<button class="btn_sort_on" id="mail_addr<?=$i?>"><span>직접입력</span></button>
																<ul>
																	<li><button value="1"><span>직접입력</span></button></li>
																	<li><button value="naver.com"><span>naver.com</span></button></li>
																	<li><button value="gmail.com"><span>gmail.com</span></button></li>
																	<li><button value="kakao.com"><span>kakao.com</span></button></li>
																</ul>
															</div>
														</div>
													</div>
												</li>
											<?}?>
												
											</ul>
										</div>
									</div>

									<div class="rew_member_inputs_btns">
										<button class="btn_member_input_back" id="btn_back_list"><span>이전</span></button>
										<button class="btn_member_input_email" id="rewardy_member_sendmail_btn"><span>초대 메일 발송</span></button>
									</div>
								</div>
							</div>
						</div>

					</div>
				</div>
				<!-- //콘텐츠 -->
			</div>
		</div>
	</div>

	<div class="layer_user type_coin" style="display:none;">
		<div class="layer_deam"></div>
		<div class="layer_user_in">
			<div class="layer_user_box">
				<div class="layer_user_search">
					<div class="layer_user_search_desc">
						<strong>보상하기</strong>
						<span>전체 30명, 1명 선택</span>
					</div>
					<div class="layer_user_search_box">
						<input type="text" class="input_search" placeholder="이름, 부서명을 검색" />
						<button><span>검색</span></button>
					</div>
				</div>
				<div class="layer_user_info">
					<ul>
						<li>
							<dl class="on">
								<dt><button><span>고객행복팀 3</span></button></dt>
								<dd>
									<button>
										<div class="user_img">
											<img src="images/pre/img_user_002.png" alt="" />
										</div>
										<div class="user_name">
											<strong>서민정</strong>
											<span>고객행복팀</span>
										</div>
									</button>
								</dd>
								<dd>
									<button>
										<div class="user_img">
											<img src="images/pre/img_user_002.png" alt="" />
										</div>
										<div class="user_name">
											<strong>이수아</strong>
											<span>고객행복팀</span>
										</div>
									</button>
								</dd>
								<dd>
									<button>
										<div class="user_img">
											<img src="images/pre/img_user_002.png" alt="" />
										</div>
										<div class="user_name">
											<strong>정혜윤</strong>
											<span>고객행복팀</span>
										</div>
									</button>
								</dd>
							</dl>
						</li>
						<li>
							<dl class="on">
								<dt><button><span>개발팀 2</span></button></dt>
								<dd>
									<button>
										<div class="user_img">
											<img src="images/pre/img_user_002.png" alt="" />
										</div>
										<div class="user_name">
											<strong>김상엽</strong>
											<span>개발팀</span>
										</div>
									</button>
								</dd>
								<dd>
									<button>
										<div class="user_img">
											<img src="images/pre/img_user_002.png" alt="" />
										</div>
										<div class="user_name">
											<strong>유상길</strong>
											<span>개발팀</span>
										</div>
									</button>
								</dd>
							</dl>
						</li>
						<li>
							<dl class="on">
								<dt><button><span>디자인팀 4</span></button></dt>
								<dd>
									<button>
										<img src="images/pre/ico_me.png" alt="" class="user_me" />
										<div class="user_img">
											<img src="images/pre/img_user_002.png" alt="" />
										</div>
										<div class="user_name">
											<strong>윤지혜</strong>
											<span>디자인팀</span>
										</div>
									</button>
								</dd>
								<dd>
									<button>
										<div class="user_img">
											<img src="images/pre/img_user_002.png" alt="" />
										</div>
										<div class="user_name">
											<strong>이나윤</strong>
											<span>디자인팀</span>
										</div>
									</button>
								</dd>
								<dd>
									<button>
										<div class="user_img">
											<img src="images/pre/img_user_002.png" alt="" />
										</div>
										<div class="user_name">
											<strong>정현주</strong>
											<span>디자인팀</span>
										</div>
									</button>
								</dd>
								<dd>
									<button>
										<div class="user_img">
											<img src="images/pre/img_user_002.png" alt="" />
										</div>
										<div class="user_name">
											<strong>하병호</strong>
											<span>디자인팀</span>
										</div>
									</button>
								</dd>
							</dl>
						</li>
						<li>
							<dl class="on">
								<dt><button><span>마케팅팀 3</span></button></dt>
								<dd>
									<button>
										<div class="user_img">
											<img src="images/pre/img_user_002.png" alt="" />
										</div>
										<div class="user_name">
											<strong>김명선</strong>
											<span>마케팅팀</span>
										</div>
									</button>
								</dd>
								<dd>
									<button>
										<div class="user_img">
											<img src="images/pre/img_user_002.png" alt="" />
										</div>
										<div class="user_name">
											<strong>박정헌</strong>
											<span>마케팅팀</span>
										</div>
									</button>
								</dd>
								<dd>
									<button>
										<div class="user_img">
											<img src="images/pre/img_user_002.png" alt="" />
										</div>
										<div class="user_name">
											<strong>성지훈</strong>
											<span>마케팅팀</span>
										</div>
									</button>
								</dd>
							</dl>
						</li>
					</ul>
				</div>
				<div class="layer_user_coin">
					<div class="layer_user_coin_num">
						<input type="text" placeholder="얼마를" class="input_coin" />
						<span>(현재보유 코인 : <strong>1,100</strong>코인)</span>
					</div>
					<div class="layer_user_coin_reason">
						<input type="text" placeholder="보상 사유를 작성하세요." class="input_reason" />
					</div>
				</div>
			</div>

			<div class="layer_user_btn">
				<button class="layer_user_cancel"><span>취소</span></button>
				<button class="layer_user_submit"><span>보상하기</span></button>
			</div>
		</div>
	</div>

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
				<button id="btn_back_list"><span>이전으로</span></button>
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
							<div class="tl_profile_img" style="background-image:url(images/pre/img_prof_01.png);">
								<button class="btn_profile" id="profile_img_01"><span>기본 프로필 이미지1 선택</span></button>
							</div>
						</div>
					</li>
					<li>
						<div class="tl_profile_box">
							<div class="tl_profile_img" style="background-image:url(images/pre/img_prof_02.png);">
								<button class="btn_profile" id="profile_img_02"><span>기본 프로필 이미지2 선택</span></button>
							</div>
						</div>
					</li>
					<li>
						<div class="tl_profile_box">
							<div class="tl_profile_img" style="background-image:url(images/pre/img_prof_03.png);">
								<button class="btn_profile" id="profile_img_03"><span>기본 프로필 이미지3 선택</span></button>
							</div>
						</div>
					</li>
					<li>
						<div class="tl_profile_box">
							<div class="tl_profile_img" style="background-image:url(images/pre/img_prof_04.png);">
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

	<div class="t_layer rew_layer_member_add" style="display:none;">
		<div class="tl_deam"></div>
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

	<div class="t_layer rew_layer_excel_add" style="display:none;">
		<div class="tl_deam"></div>
		<div class="tl_in">
			<div class="tl_close">
				<button><span>닫기</span></button>
			</div>
			<div class="tl_tit">
				<strong>엑셀 일괄 등록</strong>
				<span>멤버 정보를 엑셀로 일괄 등록이 가능합니다.<br />엑셀 양식 다운로드 후 알맞게 작성하세요!</span>
			</div>
			<div class="tl_list">
				<ul>
					<li>
						<div class="tc_input">
							<div class="file_area">
								<input type="text" class="input_excel" value="입력폼.xlsx" disabled />
								<label for="excel_file" class="label_excel"><span>파일첨부</span></label>
								<input type="file" id="excel_file" class="file_excel" />
							</div>
						</div>
					</li>
				</ul>
			</div>
			<div class="tl_btn">
				<button><span>엑셀 양식 다운로드</span></button>
			</div>
		</div>
	</div>

	<div class="t_layer rew_layer_team_management" style="display:none;">
		<div class="tl_deam"></div>
		<div class="tl_in">
			<div class="tl_close">
				<button><span>닫기</span></button>
			</div>
			<div class="tl_tit">
				<strong>부서명 관리</strong>
				<span>부서명을 등록하고 관리하세요!</span>
			</div>
			<div class="tl_list">
				<ul>
					<li>
						<div class="tc_input">
							<div class="team_area">
								<input type="text" class="input_team" disabled value="콘텐츠팀" />
								<button class="btn_team_regi"><span>변경</span></button>
								<button class="btn_team_del"><span>삭제</span></button>
							</div>
						</div>
					</li>
					<li>
						<div class="tc_input">
							<div class="team_area">
								<input type="text" class="input_team" disabled value="콘텐츠팀" />
								<button class="btn_team_regi"><span>변경</span></button>
								<button class="btn_team_del"><span>삭제</span></button>
							</div>
						</div>
					</li>
					<li>
						<div class="tc_input">
							<div class="team_area">
								<input type="text" class="input_team"disabled  value="콘텐츠팀" />
								<button class="btn_team_regi"><span>변경</span></button>
								<button class="btn_team_del"><span>삭제</span></button>
							</div>
						</div>
					</li>
					<li>
						<div class="tc_input">
							<div class="team_area">
								<input type="text" class="input_team"disabled  value="콘텐츠팀" />
								<button class="btn_team_regi"><span>변경</span></button>
								<button class="btn_team_del"><span>삭제</span></button>
							</div>
						</div>
					</li>
					<li>
						<div class="tc_input">
							<div class="team_area">
								<input type="text" class="input_team" disabled value="콘텐츠팀" />
								<button class="btn_team_regi"><span>변경</span></button>
								<button class="btn_team_del"><span>삭제</span></button>
							</div>
						</div>
					</li>
					<li>
						<div class="tc_input">
							<div class="team_area">
								<input type="text" class="input_team" disabled value="콘텐츠팀" />
								<button class="btn_team_regi"><span>변경</span></button>
								<button class="btn_team_del"><span>삭제</span></button>
							</div>
						</div>
					</li>
				</ul>
			</div>
			<div class="tl_btn_team">
				<input type="text" placeholder="부서명" />
				<button><span>추가하기</span></button>
			</div>
		</div>
	</div>

	<div class="rew_qck">
		<button class="btn_open_join"><span>회원가입</span></button>
		<button class="btn_open_login"><span>로그인</span></button>
		<button class="btn_open_repass"><span>비밀번호 재설정</span></button>
		<button class="btn_open_setting"><span>프로필 변경</span></button>
	</div>
	<script type="text/javascript">
		$(document).ready(function(){
			var list_leng = $(".rew_layer_team_management .tl_list > ul > li").length - 1;
			if(list_leng>4){
				$(".t_layer.rew_layer_team_management").css({"height":619,"marginTop":-320});
			}else{
				var list_lengx = 65*list_leng;
				$(".t_layer.rew_layer_team_management").css({"height":359+list_lengx,"marginTop":-(359+list_lengx)/2});
			}
			$(".team_area .btn_team_del").click(function(){
				$(this).closest(".rew_layer_team_management .tl_list > ul > li").remove();
				var list_leng = $(".rew_layer_team_management .tl_list > ul > li").length - 1;
				if(list_leng>4){
					$(".t_layer.rew_layer_team_management").css({"height":619,"marginTop":-320});
				}else{
					var list_lengx = 65*list_leng;
					$(".t_layer.rew_layer_team_management").css({"height":359+list_lengx,"marginTop":-(359+list_lengx)/2});
				}
			});
			$(".team_area .btn_team_regi").click(function(){
				$(this).prev(".input_team").attr("disabled", false);
			});

			$(".btn_open_join").click(function(){
				$(".rew_layer_join").show();
			});
			$(".btn_open_login").click(function(){
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
</div>
	<!-- footer start-->
	<? include $home_dir . "/inc_lude/footer.php";?>
	<!-- footer end-->

</body>


</html>
