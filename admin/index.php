<?php
	//header페이지
	$home_dir = str_replace( basename(__DIR__) , "" , __DIR__ );
	include $home_dir . "/inc_lude/header.php";


	//if( $user_level != '0'){
	//	alertMove("접속권한이 없습니다.");
	//	exit;
	//}

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
	$team_info = member_part_info();

?>
<script>
var team_info_arr = new Array();
<?
for($i=0; $i<count($team_info['partno']); $i++){?>
	team_info_arr['<?=$team_info['partno'][$i]?>'] = '<?=$team_info['part'][$i]?>';
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
									<li><a href="/admin/comcoin.php"><span>공용코인 관리</span></a></li>
									<li><a href="/admin/comcoin_mem.php"><span>멤버별 공용코인</span></a></li>
									<li><a href="/admin/comcoin_out.php"><span>코인출금 신청내역</span></a></li>
								</ul>
							</div>
						</div>
						<div class="rew_conts_scroll_01">
							<div class="rew_member">
								<div class="rew_member_in">
									<div class="rew_member_func">
										<div class="rew_member_func_in">
											<div class="rew_member_count">
												<span>멤버관리</span>
												<!-- <strong><?=$member_count_info['cnt']?></strong> -->
												<strong><?=count($member_info_cnt['idx'])?></strong>
											</div>
											<div class="rew_member_sort" id="rew_member_sort">
												<div class="rew_member_sort_in">
													<button class="btn_sort_on" id="btn_sort_on" value="name"><span>이름 순</span></button>
													<ul>
														<li><button value="name"><span>이름 순</span></button></li>
														<li><button value="part"><span>부서 순</span></button></li>
														<li><button value="email"><span>이메일 순</span></button></li>
													</ul>
												</div>
											</div>
											<div class="rew_member_search">
												<div class="rew_member_search_box">
													<input type="text" class="input_search" placeholder="이름, 부서명을 검색" id="member_search"/>
													<button id="member_search_btn"><span>검색</span></button>
												</div>
											</div>
											<div class="rew_member_btns">
												<div class="rew_member_btns_in">
													<button class="btn_member_add" id="member_add_btn"><span>멤버 추가</span></button>
													<button class="btn_team_management" id="btn_team_management"><span>부서 관리</span></button>
												</div>
											</div>
										</div>
									</div>

									<div class="rew_member_list">
										<div class="rew_member_list_in">
											<div class="member_list_header">
												<div class="member_list_header_in" id="member_list_header_in">
													<div class="member_list_header_name">
														<strong value="name">이름</strong>
														<em>
															<button class="btn_sort_up" title="오름차순"></button>
															<button class="btn_sort_down" title="내림차순"></button>
														</em>
													</div>
													<div class="member_list_header_team">
														<strong value="part">부서</strong>
														<em>
															<button class="btn_sort_up" title="오름차순"></button>
															<button class="btn_sort_down" title="내림차순"></button>
														</em>
													</div>
													<div class="member_list_header_email">
														<strong value="email">이메일</strong>
														<em>
															<button class="btn_sort_up" title="오름차순"></button>
															<button class="btn_sort_down" title="내림차순"></button>
														</em>
													</div>
													<div class="member_list_header_date">
														<strong value="reg">등록일</strong>
														<em>
															<button class="btn_sort_up" title="오름차순"></button>
															<button class="btn_sort_down" title="내림차순"></button>
														</em>
													</div>
													<div class="member_list_header_admin">
														<strong value="auth">관리자 지정</strong>
														<em>
															<button class="btn_sort_up" title="오름차순"></button>
															<button class="btn_sort_down" title="내림차순"></button>
														</em>
													</div>
													<div class="member_list_header_function">
														<strong>수정/삭제/초기화</strong>
													</div>
												</div>
											</div>
											<div class="list_paging" id="list_paging">
												<input id="page_num" value="<?=$p?>">
												<div class="member_list_conts">
													<div class="member_list_conts_in">
														<ul id="member_list_conts_ul">
														<?for($i=0; $i<count($member_info['idx']); $i++){
															$mem_idx = $member_info['idx'][$i];
															$mem_state = $member_info['state'][$i];
															$highlevel = $member_info['highlevel'][$i];
															$reg = $member_info['reg'][$i];
															$partno = $member_info['partno'][$i];
														?>
															<li id="li_<?=$mem_idx?>">
																<div class="member_list_conts_name" id="member_list_conts_name_<?=$mem_idx?>">
																	<strong><?=$member_info['name'][$i]?></strong>
																</div>
																<div class="member_list_conts_team" id="member_list_conts_team_<?=$mem_idx?>">
																	<strong><?=$member_info['part'][$i]?></strong>
																	<div class="rew_member_list_sort" id="rew_member_list_sort_<?=$mem_idx?>" style="display:none;">
																		<div class="rew_member_list_sort_in">
																			<button class="btn_sort_on" id="member_team_<?=$mem_idx?>" value="<?=$partno?>"><span><?=$member_info['part'][$i]?></span></button>
																			<input type="hidden" id="ch_part_no_<?=$mem_idx?>" value="<?=$partno?>">
																			<ul>
																				<?for($j=0; $j<count($team_info['partno']); $j++){
																					$part_idx = $team_info['partno'][$j];
																					$partname = $team_info['part'][$j];
																				?>
																				<li><button value="<?=$part_idx?>"><span><?=$partname?></span></button></li>
																				<?}?>
																			</ul>
																		</div>
																	</div>
																</div>
																<div class="member_list_conts_email" id="member_list_conts_email_<?=$mem_idx?>">
																	<strong><?=$member_info['email'][$i]?></strong>
																</div>
																<?if($mem_state=='0'){?>
																	<div class="member_list_conts_date">
																		<strong><?=$reg?></strong>
																	</div>
																<?}else if($mem_state=='1'){?>
																	<div class="member_list_conts_date">
																		<strong>초대중</strong>
																		<button class="btn_list_email" id="btn_list_email_<?=$mem_idx?>"><span>메일 재발송</span></button>
																	</div>
																<?}?>
																<div class="member_list_conts_admin">
																	<div class="btn_switch<?=$highlevel=="0"?" on":""?>" value="<?=$mem_idx?>" id="sw_idx">
																		<strong class="btn_switch_on"></strong>
																		<span>버튼</span>
																		<strong class="btn_switch_off"></strong>
																	</div>
																</div>
																<div class="member_list_conts_function">
																	<button class="btn_list_regi" id="btn_list_regi_<?=$mem_idx?>" value="<?=$mem_idx?>"><span>수정</span></button>
																	<?php
																		// 2022-12-06 : 관리자로 지정되있는 계정 삭제버튼 가림.
																		$view_btn_list_del = '';
																		if($highlevel=="0"){
																			$view_btn_list_del = 'style="display:none"';
																		}
																	?>
																	<button class="btn_list_del" id="btn_list_del_<?=$mem_idx?>" value="<?=$mem_idx?>" <?=$view_btn_list_del?>><span>삭제</span></button>
																	<button class="btn_list_reset" id="btn_list_reset_<?=$mem_idx?>" value="<?=$mem_idx?>" ><span>초기화</span></button>
																	<div class="btn_member_list" id="btn_member_list_<?=$mem_idx?>" style="display:none">
																		<button class="btn_list_ok" id="btn_list_ok"><span>확인</span></button>
																		<button class="btn_list_cancel" id="btn_list_cancel"><span>취소</span></button>
																	</div>
																</div>
															</li>
															<?}?>
														</ul>
													</div>
												</div>
												<?if($member_info['idx']){?>
												<div class="rew_ard_paging" id="rew_ard_paging">
													<div class="rew_ard_paging_in">
														<input type="hidden" id="this_class" value="<?=$tclass?>">
														<?
															//페이징사이즈, 전체카운터, 페이지출력갯수
															echo pageing($pagingsize, $total_count, $pagesize, $string);
														?>
													</div>
												</div>
											<?}?>
											</div>
										</div>
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
					<?for($i=0; $i<count($team_info['partno']); $i++){
						$part_idx = $team_info['partno'][$i];
						$partname = $team_info['part'][$i];
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
					<?for($i=0; $i<count($team_info['partno']); $i++){
						$part_idx = $team_info['partno'][$i];
						$partname = $team_info['part'][$i];
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
