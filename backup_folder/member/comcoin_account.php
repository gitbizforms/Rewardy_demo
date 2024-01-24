<?php
	//header페이지
	$home_dir = str_replace( basename(__DIR__) , "" , __DIR__ );
	include $home_dir . "/inc_lude/header.php";

	if($user_id=='sadary0@nate.com'){
		if( $user_level != '0'){
			//alertMove("접속권한이 없습니다.");
			//exit;
		}
	}

	//페이지
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
		$startnum = 1;
	}else{
		$startnum = ($p - 1) * $pagesize + 1;
	}

	//일주일
	$week7 = date("Y-m-d",strtotime("-1 week", TODAYTIME));


	$pageurl = get_dirname();

	$string = "&page=".$pageurl."&sdate=".$sdate."&edate=".$edate."&nday=".$nday."&type=".$type;



	//공용코인
	$where = " where state='0' and companyno='".$companyno."' and coin_type='1'";
	$where = $where .= " and code in('800','900')";


	//전체 카운터수
	$sql = "select count(1) as cnt from work_coininfo ".$where."";
	$comcoin_cnt_info = selectQuery($sql);
	if($comcoin_cnt_info['cnt']){
		$total_count = $comcoin_cnt_info['cnt'];
	}


	//공용코인내역정보
	// $sql = "select * from";
	// $sql = $sql .= " (select ROW_NUMBER() over(order by idx desc) as r_num, idx, code, work_idx, email, name, reward_user, reward_name, coin, coin_out, coin_type, memo, CONVERT(CHAR(10), regdate, 102) as ymd, CONVERT(CHAR(8), regdate, 108) as his, convert(varchar, regdate , 120) as regdate from work_coininfo";
	// $sql = $sql .= " ".$where.")";
	// $sql = $sql .= " as a where r_num between ". $startnum ." and " .$endnum ." order by idx desc";
	$sql = "select idx, code, work_idx, email, name, reward_user, reward_name, coin, coin_out, coin_type, memo, DATE_FORMAT(regdate, '%Y.%m.%d') AS ymd, DATE_FORMAT(regdate, '%h:%i:%s') AS his, regdate";
	$sql = $sql .= " from work_coininfo";
	$sql = $sql .= " ".$where."";
	$sql = $sql .= " order by idx desc";
	$sql = $sql .= " limit ". ($p-1)*$startnum.", ".$endnum;
	$comcoin_info = selectAllQuery($sql);


	//은행명
	$sql = "select idx, name from work_bank where state='0' order by rank asc";
	$bank_info = selectAllQuery($sql);

	//회원정보 불러오기
	$reward_user_info = member_row_info($user_id);
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

						<div class="rew_member_tab">
							<div class="rew_member_tab_in">
								<ul>
									<li><a href="/member/member_list.php"><span>멤버관리</span></a></li>
									<li class="on"><a href="#"><span>공용코인 관리</span></a></li>
									<li><a href="/member/comcoin_mem.php"><span>멤버별 공용코인</span></a></li>
									<li><a href="/member/comcoin_out.php"><span>코인출금 신청내역</span></a></li>
								</ul>
							</div>
						</div>

						<div class="rew_conts_scroll_01">

							<div class="rew_member tywe_coin">
								<div class="rew_member_in" id="rew_member_in">

									<div class="rew_member_tab_03">
										<div class="rew_member_tab_03_in">
											<ul>
												<li><a href="#"><span>공용코인 충전</span></a></li>
												<li class="on"><a href="/member/comcoin_account.php"><span>공용코인 출금</span></a></li>
												<li><a href="/member/comcoin.php"><span>입출금 내역</span></a></li>
											</ul>
										</div>
									</div>



									<div class="rew_member_withdraw_in">
										<div class="rew_member_withdraw_box">
											<div class="rew_member_withdraw_top">
												<div class="rew_member_withdraw_qna">
													<div class="qna">
														<strong class="rew_member_withdraw_qna_tit">남은 공용 코인</strong>
														<span class="qna_q">?</span>
														<div class="qna_a">
															<span>남은 공용 코인</span>
														</div>
													</div>
												</div>
												<div class="rew_member_withdraw_coin" id="rew_member_withdraw_coin">
													<strong><span><?=$reward_user_info['comcoin']?></span></strong>
												</div>
											</div>
											<div class="rew_member_withdraw_mid">
												<div class="rew_member_withdraw_btns" id="rew_member_withdraw_btns">
													<ul>
														<li><button value="10000"><span>+ 1만</span></button></li>
														<li><button value="30000"><span>+ 3만</span></button></li>
														<li><button value="50000"><span>+ 5만</span></button></li>
														<li><button value="100000"><span>+ 10만</span></button></li>
														<li><button value="1000000"><span>+ 100만</span></button></li>
														<li><button><span>전액</span></button></li>
													</ul>
													<button class="btn_coin_reset" id="btn_coin_reset"><span>초기화</span></button>
												</div>
												<div class="rew_member_withdraw_input">
													<input type="text" class="input_withdraw" placeholder="출금할 금액을 입력하세요." id="withdraw_coin">
												</div>
												<div class="rew_member_withdraw_info">
													<div class="rew_member_withdraw_bank" id="rew_member_withdraw_bank">
														<div class="rew_member_withdraw_bank_in">
															<button class="btn_bank_on" id="btn_bank_on"><span>은행</span></button>
															<ul>
															<?for($i=0;$i<count($bank_info['idx']); $i++){?>
																<li><button value="<?=$bank_info['idx'][$i]?>"><span><?=$bank_info['name'][$i]?></span></button></li>
															<?}?>
															</ul>
														</div>
													</div>
													<input type="text" class="input_bank_num" placeholder="계좌번호" id="input_bank_num">
													<input type="text" class="input_bank_user" placeholder="예금주" id="input_bank_user">
												</div>
												<div class="rew_member_withdraw_desc">
													<ul>
														<li>출금 신청한 코인은 돌아오는 주 화요일에 일괄 지급됩니다.</li>
														<li>작성해 주신 계좌로 입금되며, 오기입으로 인해 잘못 송금된 코인에 대해서는 책임지지 않습니다.</li>
													</ul>
												</div>
												<div class="rew_member_withdraw_btn">
													<button class="btn_withdraw_on" id="btn_withdraw_on"><span>출금 신청하기</span></button>
												</div>
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
