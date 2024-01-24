<?
	//header페이지
	$home_dir = str_replace( basename(__DIR__) , "" , __DIR__ );
	$home_dir = str_replace( "yNAS" , "rewardyNAS" , $home_dir );
	include $home_dir . "inc_lude/header_reward.php";

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
		$startnum = 0;
	}else{
		$startnum = ($p - 1) * $pagesize;
	}

	//일주일
	$week7 = date("Y-m-d",strtotime("-1 week", TODAYTIME));

	//시작일
	/*
	if(!$sdate){
		$sdate = $week7;
	}

	//종료일
	if(!$edate){
		$edate = TODATE;
	}

	if(!$nday){
		$nday = '7';
	}*/

	// $pageurl = get_dirname();
	$pageurl = "reward";
	$string = "&page=".$pageurl."&sdate=".$sdate."&edate=".$edate."&nday=".$nday."&type=".$type;

	//조건절
	if($user_id=='sadary0@nate.com'){
	//	$user_id = "eyson@bizforms.co.kr";
	//	$where = " where state='0' and companyno='".$companyno."'";
	}

	$where = " where state!='9' and email='".$user_id."' and companyno='".$companyno."'";

	//전체 카운터수
	$sql = "select count(1) as cnt from work_coininfo ".$where."";
	$workcoin_info = selectQuery($sql);
	if($workcoin_info['cnt']){
		$total_count = $workcoin_info['cnt'];
	}



	//코인내역정보
	$sql = "select idx, code, coin_auto, work_idx, reward_user, reward_type ,reward_name, coin, coin_type, memo";
	$sql = $sql .", DATE_FORMAT(regdate, '%Y-%m-%d') as ymd, DATE_FORMAT(regdate, '%H:%i:%s') as his";
	$sql = $sql .", DATE_FORMAT(regdate, '%Y-%m-%d %H:%i:%s') as regdate from work_coininfo";
	$sql = $sql .= " ".$where;
	$sql = $sql .= " order by idx desc";
	$sql = $sql .= " limit $startnum ".",".$pagesize;
	$reward_info_list = selectAllQuery($sql);
	//echo $sql;


	//챌린지 정보
	$sql = "select idx, title from work_challenges where state='0' and template='0' and temp_flag='0' order by idx desc";
	$chall_info = selectAllQuery($sql);
	if($chall_info['idx']){
		//$chall_info_list = @array_combine($chall_info['idx'], $chall_info['tilte']);
		for($i=0; $i<count($chall_info['idx']); $i++){
			$cidx = $chall_info['idx'][$i];
			$ctitle = urldecode($chall_info['title'][$i]);
			$chall_info_list[$cidx] = $ctitle;
		}
	}

	$sql = "select idx, kind, code, memo from work_coin_reward where state='0' and code is not null order by idx desc";
	$coin_info = selectAllQuery($sql);
	if($coin_info['idx']){
		for($i=0; $i<count($coin_info['idx']); $i++){

			$kind = $coin_info['kind'][$i];
			$code = $coin_info['code'][$i];
			$memo = $coin_info['memo'][$i];
			$reward_list[$code]['memo'] = $memo;
			$reward_list[$code]['code'] = $kind;
		}

	}


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
			<? include $home_dir . "/inc_lude/header_new.php";?>
				<!-- menu -->
				<? include $home_dir . "/inc_lude/menu_reward.php";?>
				<!-- //menu -->

				<!-- 콘텐츠 -->
				<div class="rew_conts">
					<div class="rew_conts_in">

						<div class="rew_ard_tab">
							<div class="rew_ard_tab_in">
								<div class="rew_ard_sort" id="rew_ard_sort">
									<div class="rew_ard_sort_in">
										<button class="btn_sort_on" id="btn_sort_on"><span>전체보기</span></button>
										<ul>
											<li><button value="all"><span>전체보기</span></button></li>
											<li><button value="add"><span>보상</span></button></li>
											<li><button value="out"><span>차감</span></button></li>
										</ul>
									</div>
								</div>
								<div class="rew_ard_calendar">
									<div class="rew_ard_btns">
										<button class="on"><span>1주일</span></button>
										<button><span>1개월</span></button>
										<button><span>3개월</span></button>
									</div>
									<div class="rew_ard_period">
										<div class="rew_ard_period_box">
											<input type="text" class="input_date_l" value="<?=$week7?>" id="reward_sdate" />
											<button class="btn_calendar_l" id="btn_calendar_l">달력</button>
											<span>~</span>
											<input type="text" class="input_date_r" value="<?=TODATE?>" id="reward_edate"/>
											<button class="btn_calendar_r" id="btn_calendar_r">달력</button>
										</div>
										<button class="btn_inquiry" id="btn_inquiry"><span>조회</span></button>
										<input type="hidden" id="reward_inquiry"/>
									</div>
								</div>
							</div>
						</div>
						<div class="rew_conts_scroll_09">
							<div class="rew_ard">
								<div class="rew_ard_in" id="rew_ard_in">
									<div class="rew_ard_list">
										<div class="rew_ard_list_in">
											<div class="rew_ard_list_tab">
												<span class="rew_ard_tab_tit">상세내용</span>
												<span class="rew_ard_tab_coin">코인</span>
												<span class="rew_ard_tab_date">일시</span>
												<span class="rew_ard_tab_kind">유형</span>
												<span class="rew_ard_tab_type">구분</span>
											</div>
											<div class="rew_ard_list_desc">
												<ul>
													<?
													for($i=0; $i<count($reward_info_list['idx']); $i++){
														$idx = $reward_info_list['idx'][$i];
														$code = $reward_info_list['code'][$i];
														$coin_auto = $reward_info_list['coin_auto'][$i];
														$memo = $reward_info_list['memo'][$i];
														$coin = $reward_info_list['coin'][$i];
														$coin_type = $reward_info_list['coin_type'][$i];
														$work_idx = $reward_info_list['work_idx'][$i];
														$ymd = $reward_info_list['ymd'][$i];
														$his = $reward_info_list['his'][$i];

														$reg_date = $ymd . " ". $his;
	
														$reward_user = $reward_info_list['reward_user'][$i];
														$reward_name = $reward_info_list['reward_name'][$i];
														$reward_type = $reward_info_list['reward_type'][$i];

														if($reward_type=="challenge"){
															$reward_type_name = "챌린지";
														}elseif($reward_type=="party"){
															$reward_type_name = "파티";
														}elseif($reward_type=="reward"){
															$reward_type_name = "리워드";
														}else if($reward_type=="party_reward"){
															$reward_type_name = "파티보상";
														}else if($reward_type=="live"){
															$reward_type_name = "라이브";
														}else if($reward_type=="account"){
															$reward_type_name = "출금신청";
														}else if($reward_type=="tutorial"){
															$reward_type_name = "튜토리얼";
														}else if($reward_type == "item"){
															$reward_type_name = "아이템 구입";
														}else if($reward_type == "todaywork"){
															$reward_type_name = "업무보상";
														}else{
															$reward_type_name = "공용코인";
														}

														$chall_title = $chall_info_list[$work_idx];

														//공용코인
														if($coin_type == '1'){

															if ($reward_list[$code]['code'] == "plus"){
																$span_class = "coin_plus";
																$span_ho = "+";
																$resard_list_memo = "보상";
																$reward_desc = $reward_list[$code]['memo'];

															}else if($reward_list[$code]['code'] == "minus"){
																$span_class = "coin_minus";
																$span_ho = "-";
																$resard_list_memo = "차감";
																$reward_desc = $reward_list[$code]['memo'];
															}

															$coin_type_text = "공용코인";

														//일반코인
														}else{

															if($reward_list[$code] && !$reward_user){

																if($reward_list[$code]['code']== "plus"){
																	$span_ho = "+";
																	$span_class = "coin_plus";
																	
																}else if($reward_list[$code]['code'] == "minus"){
																	$span_ho = "-";
																	$span_class = "coin_minus";
																	
																}
																if($coin_auto=='1'){
																	$reward_desc = "자동적립";
																}else{
																	$reward_desc = "-";
																	$reward_desc = $reward_list[$code]['memo'];

																}

															}else if ($reward_list[$code]['code']== "plus"){
																$span_class = "coin_plus";
																$span_ho = "+";
																$resard_list_memo = "보상";
																$reward_desc = $resard_list_memo ."(". $reward_name.")";

															}else if($reward_list[$code]['code'] == "minus"){
																$span_class = "coin_minus";
																$span_ho = "-";
																$resard_list_memo = "차감";
																$reward_desc = $resard_list_memo ."(". $reward_name.")";

															}else{

																$reward_desc = $reward_list[$code]['memo'];
															}
															$coin_type_text = "코인";
														}
													?>
													<li>
														<span class="rew_ard_desc_tit"><a href="#"><?=$memo?><?=$chall_title?"(".$chall_title.")":""?><?=$coin_type_text?" (".$coin_type_text.")":""?></a></span>
														<span class="rew_ard_desc_coin <?=$span_class?>"><?=$span_ho?><?=number_format($coin)?></span>
														<span class="rew_ard_desc_date"><?=$reg_date?></span>
														<span class="rew_ard_desc_kind"><?=$reward_type_name?></span>
														<span class="rew_ard_desc_type"><?=$reward_desc?></span>
													</li>
													<?}?>
												</ul>
											</div>
										</div>
									</div>

									<?if($reward_info_list['idx']){?>
										<div class="rew_ard_paging">
											<div class="rew_ard_paging_in">

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
				<!-- //콘텐츠 -->
			</div>
		</div>
	</div>


	<div class="layer_user type_coin" style="display:none;">
		<div class="layer_deam"></div>
		<div class="layer_user_in">
			<div class="layer_user_box none">
				<div class="layer_user_search">
					<div class="layer_user_search_desc">
						<strong>보상하기</strong>
						<span id="usercnt">전체 <?=$member_total_cnt?>명</span>
						<input type="hidden" id="chall_user_cnt" value="<?=$member_total_cnt?>">
						<input type="hidden" id="chall_user_chk">
					</div>
					<div class="layer_user_search_box">
						<input type="text" class="input_search" placeholder="이름, 부서명을 검색" id="input_todaywork_search" />
						<button id="input_todaywork_search_btn"><span>검색</span></button>
					</div>
				</div>

				<div class="layer_user_slc_list">
					<div class="layer_user_slc_list_in">
						<ul>

						</ul>
					</div>
				</div>

				<div class="layer_user_info">
					<ul>
					<?
						//회원정보내역으로 회원이름 = [name][부서번호][순번]
						for($i=0; $i<count($member_info['idx']); $i++){
							$member_idx = $member_info['idx'][$i];
							$member_uid = $member_info['email'][$i];
							$member_name = $member_info['name'][$i];
							$partno 	= $member_info['partno'][$i];

							$mem_uid[$partno][$member_idx] = $member_uid;
							$mem_name[$partno][$member_idx] = $member_name;
							$mem_idx[$partno][$member_idx] = $member_info['idx'][$i];
						}

						//회원정보 키값 초기화처리
						for($i=0; $i<count($member_info['idx']); $i++){

							$member_idx = $member_info['idx'][$i];
							$member_name = $member_info['name'][$i];
							$member_email = $member_info['email'][$i];
							$partno 	= $member_info['partno'][$i];

							$j = 0;
							foreach($mem_name[$partno] as $key=>$val)
							{
								unset($mem_name[$partno][$key]);
								$new_key = $j;  
								$mem_name[$partno][$new_key] = $val;
								$j++;
							}

							$j = 0;
							foreach($mem_idx[$partno] as $key=>$val)
							{
								unset($mem_idx[$partno][$key]);
								$new_key = $j;  
								$mem_idx[$partno][$new_key] = $val;
								$j++;
							}

							$j = 0;
							foreach($mem_uid[$partno] as $key=>$val)
							{
								unset($mem_uid[$partno][$key]);
								$new_key = $j;  
								$mem_uid[$partno][$new_key] = $val;
								$j++;
							}
						}

						for($i=0; $i<count($part_info['partno']); $i++){
							$partno = $part_info['partno'][$i];
							$part_cnt = count($mem_name[$partno]);
							?>
							<li>
								<dl class="on">
									<dt>
										<button class="btn_team_slc" id="btn_team_slc_<?=($partno)?>"><span><?=$part_info['part'][$i]?> <?=$part_cnt?></span></button>
										<button class="btn_team_toggle" id="btn_team_toggle"><span>열고닫기</span></button>
									</dt>

									<?for($j=0; $j<$part_cnt; $j++){
										//프로필 케릭터 사진
										$profile_img_src = profile_img_info($mem_uid[$partno][$j]);
									?>
										<dd id="udd_<?=$mem_idx[$partno][$j]?>">
											<button value="<?=$mem_idx[$partno][$j]?>" id="team_<?=$partno?>">
												<?=($user_id == $mem_uid[$partno][$j]?"<img src=\"/html/images/pre/ico_me.png\" alt=\"\" class=\"user_me\" />":"");?>
											<div class="user_img" style="background-image:url('<?=$profile_img_src?>');"></div>
											<div class="user_name">
												<strong><?=$mem_name[$partno][$j]?></strong>
												<span><?=$part_info['part'][$i]?></span>
											</div>
										</button>
										</dd>
									<?}?>
								</dl>
							</li>
						<?}?>
					</ul>
				</div>
				
				<div class="layer_user_coin">
					<div class="layer_user_coin_num">
						<input type="text" placeholder="얼마를" class="input_coin" id="input_coin"/>
						<input type="hidden" id="common_coin" value="<?=$common_coin?>"/>
						<span>(현재보유 공용코인 : <strong><?=$common_coin?></strong>코인)</span>
					</div>
					<div class="layer_user_coin_reason">
						<input type="text" placeholder="보상 사유를 작성하세요." class="input_reason" id="input_reason"/>
					</div>
				</div>
			</div>

			<div class="layer_user_btn">
			<button class="layer_user_all_slc" id="layer_user_all_slc"><span>전체선택</span></button>
				<button class="layer_user_cancel" id="layer_user_cancel"><span>취소</span></button>
				<button class="layer_user_submit" id="layer_user_submit"><span>보상하기</span></button>
			</div>
		</div>
	</div>

	<?
	
	//코인보상하기 레이어
	// include $home_dir . "/layer/member_reward.php";

	//출금하기 레이어
	include $home_dir . "/layer/member_withdraw.php";
	
	?>

	<?php
		//튜토리얼 레벨 레이어
		include $home_dir . "/layer/tutorial_main_level.php";
	?>
	<?php
	//비밀번호 재설정
		include $home_dir . "/layer/member_repass.php";
	?>
</div>
	<?php
		//로딩 페이지
		include $home_dir . "loading.php";
	?>									
	<!-- footer start-->
	<? include $home_dir . "/inc_lude/footer.php";?>
	<!-- footer end-->
	<script type="text/javascript">
	$(document).ready(function(){
		window.onbeforeunload = function () { $('.rewardy_loading_01').css('display', 'block'); }
		$(window).load(function () {          //페이지가 로드 되면 로딩 화면을 없애주는 것
            $('.rewardy_loading_01').css('display', 'none');
        });
	});
	window.onpageshow = function(event) {
 	     if ( event.persisted || (window.performance && window.performance.navigation.type == 2)) {
			  $('.rewardy_loading_01').css('display', 'none');
		}
	}
</script>
</body>
</html>