<?php
	//header페이지
	$home_dir = str_replace( basename(__DIR__) , "" , __DIR__ );
	include $home_dir . "/inc_lude/header.php";


	if($user_id=='sadary0@nate.com'){
	}else{
		//if( $user_level != '0'){
		//	alertMove("접속권한이 없습니다.");
		//	exit;
		//}
	}

	//맴버리스트
	$sql = "select idx, email, name, highlevel, company, part, partno, DATE_FORMAT(regdate, '%Y-%m-%d') as reg from work_member where state!='9' and companyno='".$companyno."' and highlevel!='1' order by idx asc";
	$member_info = selectAllQuery($sql);


	//부서관리
	$sql = "select idx, partname from work_team where state!='9' and companyno='".$companyno."' order by idx desc";
	$team_info = selectAllQuery($sql);

?>
<script>
var team_info_arr = new Array();
<?
for($i=0; $i<count($team_info['idx']); $i++){?>
	team_info_arr['<?=$team_info['idx'][$i]?>'] = '<?=$team_info['partname'][$i]?>';
<?}?>

</script>

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
						<? include $home_dir . "/admin/admin_menubar.php";?>
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
													<button class="btn_excel_upload" id="btn_excel_upload_layer"><span>엑셀 업로드</span></button>
													<?/*<button class="btn_excel_upload" id="btn_excel_upload"><span>엑셀 업로드</span></button>
													<input id="excel_file" type="file" style="display:none;" />*/?>
													
												</div>
											</div>
										</div>
									</div>

									<div class="rew_member_inputs">
										<div class="rew_member_inputs_in">
											<ul>

											<?for($i=1; $i<6; $i++){?>
												<li>
													<div class="member_inputs_num">
														<strong><?=$i?></strong>
													</div>
													<div class="member_inputs_name">
														<input type="text" class="inputs_member_name" placeholder="이름" id="inputs_member_name"/>
													</div>
													<div class="member_inputs_team">
														<input type="text" class="inputs_member_team" placeholder="부서명" id="inputs_member_team"/>
													</div>
													<div class="member_inputs_email">
														<input type="text" class="inputs_member_email" placeholder="이메일" id="inputs_member_email"/>
														<div class="rew_member_inputs_sort" id="rew_member_inputs_sort_<?=$i?>">
															<div class="rew_member_inputs_sort_in">
																<button class="btn_sort_on" id="btn_sort_on_<?=$i?>"><span>직접입력</span></button>
																<ul>
																	<li><button id="btn_sort_li_<?=$i?>" value="input"><span>직접입력</span></button></li>
																	<li><button id="btn_sort_li_<?=$i?>" value="naver"><span>naver.com</span></button></li>
																	<li><button id="btn_sort_li_<?=$i?>" value="gmail"><span>gmail.com</span></button></li>
																	<li><button id="btn_sort_li_<?=$i?>" value="kakao"><span>kakao.com</span></button></li>
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
										<button class="btn_member_input_back" id="btn_member_input_back"><span>이전</span></button>
										<button class="btn_member_input_email" id="btn_member_input_email2"><span>초대 메일 발송</span></button>
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

	<div class="t_layer rew_layer_excel_add" id="rew_layer_excel_add" style="display:none;">
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
								<input type="text" class="input_excel" value="엑셀 양식 다운로드.xlsx" disabled />
								<label for="excel_file" class="label_excel"><span>파일첨부</span></label>
								<input type="file" id="excel_file" class="file_excel" />
							</div>
						</div>
					</li>
				</ul>
			</div>
			<div class="tl_btn">
				<button id="sample_file_download"><span>엑셀 양식 다운로드</span></button>
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
			<div class="tl_list" id="tl_list">
				<ul>
					<?for($i=0; $i<count($team_info['idx']); $i++){
						$part_idx = $team_info['idx'][$i];
						$partname = $team_info['partname'][$i];
					?>
					<li>
						<div class="tc_input" value="<?=$i?>">
							<div class="team_area" id="team_area_<?=$i?>" value="<?=$part_idx?>">
								<input type="text" class="input_team" id="input_team_<?=$i?>" disabled value="<?=$partname?>" />
								<button class="btn_team_regi" id="btn_team_regi_<?=$i?>"><span>변경</span></button>
								<button class="btn_team_del" id="btn_team_del_<?=$i?>"><span>삭제</span></button>
							</div>
						</div>
					</li>
					<?}?>
				</ul>
			</div>
			<div class="tl_btn_team">
				<input type="text" placeholder="부서명" id="team_add"/>
				<input type="hidden" id="team_real"/>
				<button id="tl_btn_team_add"><span>추가하기</span></button>
			</div>
		</div>
	</div>

	<!-- <div class="rew_qck">
		<button class="btn_open_join"><span>회원가입</span></button>
		<button class="btn_open_login"><span>로그인</span></button>
		<button class="btn_open_repass"><span>비밀번호 재설정</span></button>
		<button class="btn_open_setting"><span>프로필 변경</span></button>
	</div> -->
	<script type="text/javascript">
		$(document).ready(function(){
			var list_leng = $(".rew_layer_team_management .tl_list > ul > li").length - 1;
			if(list_leng>4){
				$(".t_layer.rew_layer_team_management").css({"height":619,"marginTop":-320});
			}else{
				var list_lengx = 65*list_leng;
				$(".t_layer.rew_layer_team_management").css({"height":359+list_lengx,"marginTop":-(359+list_lengx)/2});
			}

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
