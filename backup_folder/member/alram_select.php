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

	$p = $_POST['p']?$_POST['p']:$_GET['p'];
	if (!$p){
		$p = 1;
	}

	$pagingsize = 5;					//페이징 사이즈
	$pagesize = 20;						//페이지 출력갯수
	$startnum = 0;						//페이지 시작번호
	$endnum = $p * $pagesize;			//페이지 끝번호

	//시작번호
	if ($p == 1){
		$startnum = 0;
	}else{
		$startnum = ($p - 1) * $pagesize;
	}

	$url = "member_list";
	$string = "&page=".$url;

	//맴버리스트: 일반회원:state=0, 메일발송된 회원 : state=1
	$sql = "select count(*) as cnt from work_member where state in('0','1') and companyno='".$companyno."' and highlevel!='1'";
	$member_count_info = selectQuery($sql);
	
	if($member_count_info['cnt']){
		$total_count = $member_count_info['cnt'];
	}

	$sql = "select idx, state, email, name, highlevel, company, part, partno, date_format(regdate, '%Y-%m-%d') as reg from work_member where state in('0','1') and companyno='".$companyno."' and highlevel!='1' order by idx asc";
	$member_info_cnt = selectAllQuery($sql);
	$sql = $sql .= " limit ". $startnum.", ".$pagesize;
	$member_info = selectAllQuery($sql);

	//부서관리
	$sql = "select idx, partname from work_team where state in('0','1') and companyno='".$companyno."' order by idx desc";
	$team_info = selectAllQuery($sql);

?>
<script>
var team_info_arr = new Array();
<?
for($i=0; $i<count($team_info['idx']); $i++){?>
	team_info_arr['<?=$team_info['idx'][$i]?>'] = '<?=$team_info['partname'][$i]?>';
<?}?>

</script>

									<div class="rew_member_func">
										<div class="rew_member_func_in">
											<div class="rew_member_count">
												<span>알람설정</span>
												<!-- <strong><?=$member_count_info['cnt']?></strong> -->
												<strong><?=count($member_info_cnt['idx'])?></strong>
											</div>
										</div>
									</div>

									<div class="rew_member_list">
										<div class="rew_member_list_in">
											<div class="member_list_header">
												<div class="member_list_header_in" id="member_list_header_in">
													<div class="member_list_header_name">
														<strong value="name">이름</strong>
													</div>
													<div class="member_list_header_admin">
														<strong value="auth">관리자 지정</strong>
													</div>
												</div>
											</div>
											<div class="list_paging" id="list_paging">
												<div class="member_list_conts">
													<div class="member_list_conts_in">
														<ul id="member_list_conts_ul">
															<li id="li_0">
																<div class="member_list_conts_name" id="member_list_conts_name_0">
																	<strong>알람1</strong>
																</div>
																<div class="member_list_conts_admin">
																	<div class="btn_switch<?=$highlevel=="0"?" on":""?>" value="0" id="sw_idx">
																		<strong class="btn_switch_on"></strong>
																		<span>버튼</span>
																		<strong class="btn_switch_off"></strong>
																	</div>
																</div>
															</li>
                                                            <li id="li_1">
																<div class="member_list_conts_name" id="member_list_conts_name_1">
																	<strong>알람1</strong>
																</div>
																<div class="member_list_conts_admin">
																	<div class="btn_switch<?=$highlevel=="0"?" on":""?>" value="1" id="sw_idx">
																		<strong class="btn_switch_on"></strong>
																		<span>버튼</span>
																		<strong class="btn_switch_off"></strong>
																	</div>
																</div>
															</li>
                                                            <li id="li_2">
																<div class="member_list_conts_name" id="member_list_conts_name_2">
																	<strong>알람1</strong>
																</div>
																<div class="member_list_conts_admin">
																	<div class="btn_switch<?=$highlevel=="0"?" on":""?>" value="2" id="sw_idx">
																		<strong class="btn_switch_on"></strong>
																		<span>버튼</span>
																		<strong class="btn_switch_off"></strong>
																	</div>
																</div>
															</li>
                                                            <li id="li_3">
																<div class="member_list_conts_name" id="member_list_conts_name_3">
																	<strong>알람1</strong>
																</div>
																<div class="member_list_conts_admin">
																	<div class="btn_switch<?=$highlevel=="0"?" on":""?>" value="3" id="sw_idx">
																		<strong class="btn_switch_on"></strong>
																		<span>버튼</span>
																		<strong class="btn_switch_off"></strong>
																	</div>
																</div>
															</li>
                                                            <li id="li_4">
																<div class="member_list_conts_name" id="member_list_conts_name_4">
																	<strong>알람1</strong>
																</div>
																<div class="member_list_conts_admin">
																	<div class="btn_switch<?=$highlevel=="0"?" on":""?>" value="4" id="sw_idx">
																		<strong class="btn_switch_on"></strong>
																		<span>버튼</span>
																		<strong class="btn_switch_off"></strong>
																	</div>
																</div>
															</li>
                                                            <li id="li_5">
																<div class="member_list_conts_name" id="member_list_conts_name_5">
																	<strong>알람1</strong>
																</div>
																<div class="member_list_conts_admin">
																	<div class="btn_switch<?=$highlevel=="0"?" on":""?>" value="5" id="sw_idx">
																		<strong class="btn_switch_on"></strong>
																		<span>버튼</span>
																		<strong class="btn_switch_off"></strong>
																	</div>
																</div>
															</li>
														</ul>
													</div>
												
				<!-- //콘텐츠 -->
			</div>
		</div>
	</div>

	<div class="t_layer rew_layer_team_management" id="rew_layer_team_management" style="display:none;">
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

	<div class="t_layer rew_layer_team_management" id="rew_layer_team_select" style="display:none;">
		<div class="tl_deam"></div>
		<div class="tl_in">
			<div class="tl_close">
				<button><span>닫기</span></button>
			</div>
			<div class="tl_tit">
				<strong>부서명 관리</strong>
				<span>부서명을 선택해 주세요!</span>
			</div>
			<div class="tl_list" id="tl_list">
				<ul>
					<?for($i=0; $i<count($team_info['idx']); $i++){
						$part_idx = $team_info['idx'][$i];
						$partname = $team_info['partname'][$i];
					?>
					<li>
						<div class="tc_input" value="<?=$part_idx?>">
							<div class="team_area" id="team_area_<?=$part_idx?>" value="<?=$part_idx?>">
								<input type="text" class="input_team" id="input_team_<?=$part_idx?>" readonly value="<?=$partname?>" />
								<input type="hidden" id="input_team_select"/>
							</div>
						</div>
					</li>
					<?}?>
				</ul>
			</div>
		</div>
	</div>
</div>
	<!-- footer start-->
	<? include $home_dir . "/inc_lude/footer.php";?>
	<!-- footer end-->

</body>


</html>
