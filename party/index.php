<?
	//header페이지
	$home_dir = str_replace( basename(__DIR__) , "" , __DIR__ );
	include $home_dir . "/inc_lude/header_party.php";

	//파티 전체 갯수
	$sql = "select count(1) as cnt from work_todaywork_project as a where a.state!='9' and a.companyno='".$companyno."'";
	$project_row = selectQuery($sql);
	if($project_row){
		$total_count = $project_row['cnt'];
	}
	
	//페이지수
	if(!$gp) {
		$gp = 1;
	}

	$pagesize = 12;						//페이지 출력갯수
	$startnum = 0;						//페이지 시작번호
	$endnum = $gp * $pagesize;			//페이지 끝번호

	//시작번호
	if ($gp == 1){
		$startnum = 1;
	}else{
		$startnum = ($gp - 1) * $pagesize + 1;
	}

	
	//페이징 갯수
	if ( ($total_count % $pagesize) > 0 ){
		$page_count = floor($total_count/$pagesize)+1;
	}else{
		$page_count = floor($total_count/$pagesize);
	}

	//나의파티
	$project_myinfo = member_party_user_mylist();

	//회원정보
	$member_row_info = member_row_info($user_id);


	//전체 파티리스트

	$sql = "select a.idx, a.state, a.title, a.email, com_coin_pro, date_format(a.regdate, '%Y-%m-%d %H:%i') as sdate, date_format(a.editdate, '%Y-%m-%d %H:%i') as udate, ";
	$sql .= "date_format(a.enddate, '%Y-%m-%d %H:%i') as edate, case when a.editdate is null then datediff(now(), a.regdate) when a.editdate is not null then datediff(a.editdate , a.regdate) end as reg, ";
	$sql .= "(select count(p.idx) from work_todaywork_project_info as p, work_todaywork as t where p.work_idx = t.idx and p.party_idx=a.idx and p.state='0' and t.state != '9') as work, b.state as bstate ";
	$sql .= "from work_todaywork_project as a left join ";
	$sql .= "(select state, project_idx from work_project_like where state = 1 and email = '".$user_id."' and companyno='".$companyno."') ";
	$sql .= "as b on (a.idx = b.project_idx) where a.state!='9' and a.companyno='".$companyno."' ";
	$sql .= "order by a.state asc, b.state desc,CASE WHEN a.state='0' THEN a.idx END desc, CASE WHEN a.state='1' THEN enddate END ASC";
	$sql .= " limit ". ($gp-1)*$startnum.", ".$endnum;

	$project_info = selectAllQuery($sql);

	//업무건수
	$project_work_cnt = array_combine($project_info['idx'], $project_info['work']);


	//종료된 파티 갯수
	$project_end_info = project_end_info();
	$project_end_cnt = $project_end_info['cnt'];

	//원활
	$project_ing_info = project_ing_info();
	$project_ing_cnt = $project_ing_info['cnt'];


	//보통
	$project_normal_info = project_normal_info();
	$project_normal_cnt = $project_normal_info['cnt'];


	//지연
	$project_delay_info = project_delay_info();
	$project_delay_cnt = $project_delay_info['cnt'];

	//미확인 업무
	$project_read_info = project_read_info();
	$pj_read_delay_sum = array_sum($project_read_info['cnt']);

	//전체 프로젝트 내역

	$sql = "select a.idx, a.project_idx, a.name, a.email, a.part, b.profile_type, b.profile_img_idx, c.file_path, c.file_name
	from work_todaywork_project_user a
    left join work_member b on a.email = b.email
    left join work_member_profile_img c on a.email = c.email
    where 1=1
	and a.state!='9' 
    and a.companyno='".$companyno."'
    and b.state != '9'";
	// $sql = $sql .= " group by a.project_idx, a.email";
	$sql = $sql .= " order by a.idx asc";

	$project_user_info = selectAllQuery($sql);

	for($i=0; $i<count($project_user_info['idx']); $i++){
		$project_user_idx = $project_user_info['project_idx'][$i];
		$project_user_email = $project_user_info['email'][$i];
		$project_user_name = $project_user_info['name'][$i];
		$project_user_part = $project_user_info['part'][$i];
		$project_user_profile_type = $project_user_info['profile_type'][$i];
		$project_user_profile_img_idx = $project_user_info['profile_img_idx'][$i];
		$project_user_file_path = $project_user_info['file_path'][$i];
		$project_user_file_name = $project_user_info['file_name'][$i];
		$project_user_list[$project_user_idx]['email'][] = $project_user_email;
		$project_user_list[$project_user_idx]['name'][] = $project_user_name;
		$project_user_list[$project_user_idx]['part'][] = $project_user_part;
		$project_user_list[$project_user_idx]['profile_type'][] = $project_user_profile_type;
		$project_user_list[$project_user_idx]['profile_img_idx'][] = $project_user_profile_img_idx;
		$project_user_list[$project_user_idx]['file_path'][] = $project_user_file_path;
		$project_user_list[$project_user_idx]['file_name'][] = $project_user_file_name;


		$profile_img =  'http://demo.rewardy.co.kr'.$project_user_file_path.$project_user_file_name;
		$project_use[$project_user_idx][] = $project_user_email;
	}
	//프로필 캐릭터 사진
	$character_img_info = character_img_info();

	$img_buy_arr = array();
	//프로필 캐릭터 구입여부
	$sql = "select idx,item_idx from work_item_info where state = '0' and member_email = '".$user_id."'";
	$img_buy_flag = selectAllQuery($sql);

	for($i=0; $i<count($img_buy_flag['idx']); $i++){
		$img_buy_idx = $img_buy_flag['idx'][$i];
		$img_item_idx = $img_buy_flag['item_idx'][$i];
		$img_buy_arr[$img_item_idx] = $img_buy_idx;
	}
?>
<div class="rew_warp">
	<div class="rew_warp_in">
		<div class="rew_box">
			<div class="rew_box_in">
			<?include $home_dir . "/inc_lude/header_new.php";?>
				<!-- //상단 -->
				<!-- menu -->
				<? include $home_dir . "/inc_lude/menu_party.php";?>
				<!-- //menu -->

				<!-- 콘텐츠 -->
				<div class="rew_conts">
					<div class="rew_conts_in">

						<div class="rew_cha_list_func rew_party_wrap">
							<div class="rew_cha_list_func_in">
								<div class="rew_cha_count">
									<span>전체</span>
									<strong><?=number_format($total_count);?></strong>
									<input type="hidden" id="pageno" value="<?=$gp?>">
									<input type="button" id="page_count" value="<?=$page_count?>">
									<input type="hidden" id="page_delay">
									<input type="hidden" id="page_sort">
									<input type="hidden" id="chall_user_chk">
									<input type="hidden" id="chall_user_cnt" value="<?=$member_total_cnt?>">
									<input type="hidden" id="user_my">
								</div>
								<div class="rew_party_sort" id = "rew_party_sort" >
									<div class="rew_party_sort_in">
										<button class="btn_sort_on" id="btn_on"><span>파티 생성일 순</span></button>
										<ul>
											<li><button class="btn_sort_0" id="btn_sort_0" value="created"><span>파티 생성일 순</span></button></li>
											<li><button class="btn_sort_1" id="btn_sort_1" value="updated"><span>업데이트 순</span></button></li>
											<li><button class="btn_sort_2" id="btn_sort_2" value="p_desc"><span>업무 많은 순</span></button></li>
											<li><button class="btn_sort_3" id="btn_sort_3" value="p_asc"><span>업무 적은 순</span></button></li>
											<li><button class="btn_sort_4" id="btn_sort_4" value="c_desc"><span>코인 많은 순</span></button></li>
										</ul>
									</div>
								</div>
								<div class="rew_cha_search" style="right:10px">
									<div class="rew_cha_search_box">
										<input type="text" class="input_search" placeholder="파티명 검색" id="input_part_search" />
										<button id="input_search_btn"><span>검색</span></button>
									</div>
								</div>
								<div class="rew_cha_chk_tab">
									<ul>
										<li>
											<div class="chk_tab">
												<input type="checkbox" name="cha_chk_tab" id="cha_chk_tab_my" class = "cha_chk_tab_my">
												<label for="cha_chk_tab_my">내 파티(<?=count($project_myinfo['idx'])?>)</label>
											</div>
										</li>
									</ul>
								</div>
								<div class="rew_btn_delay">
									<button class="btn_delay_0" id="btn_delay_0"><span>전체</span></button>
									<button class="btn_delay_1" id="btn_delay_1"><span>원활</span></button>
									<button class="btn_delay_3" id="btn_delay_3"><span>보통</span></button>
									<button class="btn_delay_7" id="btn_delay_7"><span>지연</span></button>
									<button class="btn_delay_end" id="btn_delay_end"><span>종료</span></button>
								</div>
								<div class="rew_type_slc">
									<button class="btn_type_box on"><span>박스형</span></button>
									<button class="party_btn_type_list"><span>리스트형</span></button>
								</div>
							</div>
						</div>

						<div class="rew_conts_scroll_10">

							<div class="rew_cha_list rew_party_wrap" id="rew_part_list">
								<div class="rew_cha_list_in">
									<ul class="rew_cha_list_ul">

										<?for($i=0; $i<count($project_info['idx']); $i++){
											$project_idx = $project_info['idx'][$i];
											$project_state = $project_info['state'][$i];
											$project_title = $project_info['title'][$i];
											$project_sdate = $project_info['sdate'][$i];
											$project_udate = $project_info['udate'][$i];
											$project_edate = $project_info['edate'][$i];
											$com_coin_pro = $project_info['com_coin_pro'][$i];
											$project_bstate = $project_info['bstate'][$i];

											if(!$com_coin_pro){
												$com_coin_pro = 0;
											}

											//시작일자
											$project_sdate = substr($project_sdate,0, 10);

											//업데이트일자
											$project_udate = substr($project_udate,0, 10);

											//종료일자
											$project_edate = substr($project_edate,0, 10);
										
											//완료된 파티
											if($project_state == '1'){
												$project_state_in = " cha_dend";
												$project_state_text = "종료";
												$project_date = $project_sdate . "~" . $project_edate;
											}else{

												//날짜차이체크
												$project_now = new DateTime( date("Y-m-d H:i", time()) );
												$project_start = new DateTime($project_sdate); // 20120101 같은 포맷도 잘됨
												

												if($project_udate){
													$project_update = new DateTime($project_udate);
													$project_diff = date_diff($project_update, $project_now);
													$project_diff_day = $project_diff->days; // 284



													//날짜차이가 1,3,7
													if($project_udate && $project_diff_day>=0 && $project_diff_day<=$party_delay['1']){
														$project_state_text = "원활";
														$project_li_class = "list_delay_1";
													}else if($project_udate && $project_diff_day>=$party_delay['2'] && $project_diff_day<=$party_delay['3']){
														$project_state_text = "보통";
														$project_li_class = "list_delay_3";
													}else if($project_udate && $project_diff_day>=$party_delay['4'] || !$project_update){
														$project_li_class = "list_delay_7";
														$project_state_text = "지연";
													}else{
														$project_li_class = "list_delay_7";
														$project_state_text = "지연";
													}

												}else{
													$project_n_diff = date_diff($project_start, $project_now);
													$project_n_diff_day = $project_n_diff->days;

													//날짜차이가 1,3,7
													if($project_sdate && $project_n_diff_day>=0 && $project_n_diff_day<=$party_delay['1']){
														$project_state_text = "원활";
														$project_li_class = "list_delay_1";
													}else if($project_sdate && $project_n_diff_day>=$party_delay['2'] && $project_n_diff_day<=$party_delay['3']){
														$project_state_text = "보통";
														$project_li_class = "list_delay_3";
													}else if($project_sdate && $project_n_diff_day>=$party_delay['4'] || !$project_update){
														$project_li_class = "list_delay_7";
														$project_state_text = "지연";
													}else{
														$project_li_class = "list_delay_7";
														$project_state_text = "지연";
													}

												}

												$project_date = $project_sdate ."~";


												$project_state_in = "";
											}

											$pro_like_cli = "";

											if($project_bstate == 1){
												$pro_like_cli = "on";
											}

										?>
											<li class="<?=$project_li_class?><?=$project_state_in?>" value="<?=$project_idx?>">
												<button class="cha_fav <?=$pro_like_cli?>" id="cha_fav_<?=$project_idx?>" onclick="pro_like(<?=$project_idx?>);"><span>즐겨찾기</span></button>
												<a href="#null" onclick="javascript:void(0);">
												<div class="cha_box">
												
													<div class="cha_delay">
														<span><?=$project_state_text?></span>
													</div>
														<span class="cha_tit"><?=$project_title?></span>
													<div class="cha_date">
														<span><?=$project_date?></span>
													</div>
													<div class="cha_bar">
														<div class="cha_num">
															<span>업무 <strong><?=$project_work_cnt[$project_idx]?></strong></span>
														</div>
														<div class="cha_coin_all">
															<span><?=number_format($com_coin_pro);?></span>
														</div>
													</div>
													<div class="cha_user_box">
													<?for($j=0; $j<count($project_user_list[$project_idx]['email']); $j++){
														$user_cnt = 0;

														$project_user_list_email = $project_user_list[$project_idx]['email'][$j];
														$project_user_list_profile_type = $project_user_list[$project_idx]['profile_type'][$j];
														$project_user_list_profile_img_idx = $project_user_list[$project_idx]['profile_img_idx'][$j];
														$project_user_list_file_path = $project_user_list[$project_idx]['file_path'][$j];
														$project_user_list_file_name = $project_user_list[$project_idx]['file_name'][$j];

														$profile_img =  'http://demo.rewardy.co.kr'.$project_user_list_file_path.$project_user_list_file_name;

														if($project_state==0 && $user_id==$project_user_list_email){
															$li_class = ' cha_user_me';
														}else{
															$li_class = '';
														}

														if($j>6){
															$user_more_cnt = count($project_user_list[$project_idx]['email'])-6;
															$user_more ="<div class=\"cha_user_more\">+".$user_more_cnt."</div>";
															$user_cnt = 1;
														}
													?>
														<?if($j<6){?>
															<div class="cha_user_img<?=$li_class?>" style="background-image:url('<?=$project_user_list_profile_type >= '0'?$profile_img:"/html/images/pre/img_prof_default.png"?>');"></div>
														<?}?>
													<?}?>
													<?if($user_cnt == 1){?>
														<?=$user_more?>
													<?}?>
													</div>
													<div class="cha_user_list" style = "display:none;">
													<?for($j=0; $j<count($project_user_list[$project_idx]['email']); $j++){
																							$user_cnt = 0;

														$project_user_list_email = $project_user_list[$project_idx]['email'][$j];
														$project_user_list_profile_type = $project_user_list[$project_idx]['profile_type'][$j];
														$project_user_list_profile_img_idx = $project_user_list[$project_idx]['profile_img_idx'][$j];
														$project_user_list_file_path = $project_user_list[$project_idx]['file_path'][$j];
														$project_user_list_file_name = $project_user_list[$project_idx]['file_name'][$j];

														$profile_img =  'http://demo.rewardy.co.kr'.$project_user_list_file_path.$project_user_list_file_name;

														if($project_state==0 && $user_id==$project_user_list_email){
															$li_class = ' cha_user_me';
														}else{
															$li_class = '';
														}

														if($j>1){
															$user_more_cnt = count($project_user_list[$project_idx]['email'])-1;
															$user_more ="<div class=\"cha_user_more\">+".$user_more_cnt."</div>";
															$user_cnt = 1;
														}
													?>
														<?if($j<1){?>
															<div class="cha_user_img<?=$li_class?>" style="background-image:url('<?=$project_user_list_profile_type >= '0'?$profile_img:"/html/images/pre/img_prof_default.png"?>');"></div>
														<?}?>
													<?}?>
													<?if($user_cnt == 1){?>
														<?=$user_more?>
													<?}?>
													</div>
													</div>
												</a>
											</li>
										<?}?>
									</ul>
									<?if($gp >= $page_count){?>
										<div class="project_more" id="project_more" style="display:none">
											<button><span>more</span></button>
										</div>
									<?}else{?>
										<div class="project_more" id="project_more">
											<button><span>more</span></button>
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


	<div class="layer_work" style="display:none;">
		<div class="lw_deam"></div>
		<div class="lw_in">
			<div class="lw_box">
				<div class="lw_box_in">
					<div class="lw_top">
						<strong>좋은아침입니다!</strong>
						<p>2022년 11월 01일 오전 09:00</p>
					</div>
					<div class="lw_btn">
						<button class="lw_off"><span>취소</span></button>
						<button class="lw_on"><span>출근하기</span></button>
					</div>
				</div>
			</div>
		</div>
	</div>

	<div class="party_link_layer" style="display: none;">
		<div class="pll_deam"></div>
		<div class="pll_in">
			<div class="pll_box">
				<div class="pll_box_in">
					<div class="pll_close">
						<button><span>닫기</span></button>
					</div>
					<div class="pll_top">
						<strong>파티 연결</strong>
					</div>
					<div class="live_drop_left">
						<div class="ldl_in">
							<div class="ldl_box on">
								<div class="ldl_box_in">
									<div class="ldl_chk"><button><span>선택</span></button></div>
									<button class="ldl_box_close" style="display:none;"><span>닫기</span></button>
									<div class="ldl_box_tit">
										<p>네이버페이, 카카오페이 결제 개발</p>
									</div>
									<div class="ldl_box_time">08/29 13:33 업데이트</div>
									<div class="ldl_box_user">
										<ul>
											<li>
												<div class="ldl_box_img" style="background-image:url(images/pre/img_prof_08.png)" title="손언영"></div>
												<div class="ldl_box_user">
													<strong>손언영</strong>
													<span>운영기획팀</span>
												</div>
											</li>
											<li>
												<div class="ldl_box_img" style="background-image:url(images/pre/img_prof_04.png)" title="김민경"></div>
												<div class="ldl_box_user">
													<strong>김민경</strong>
													<span>경영지원팀</span>
												</div>
											</li>
										</ul>
									</div>
								</div>
							</div>
							<div class="ldl_box">
								<div class="ldl_box_in">
									<div class="ldl_chk"><button><span>선택</span></button></div>
									<button class="ldl_box_close"><span>닫기</span></button>
									<div class="ldl_box_tit">
										<p>네이버페이, 카카오페이 결제 개발</p>
									</div>
									<div class="ldl_box_time">08/29 13:33 업데이트</div>
									<div class="ldl_box_user">
										<ul>
											<li class="ldl_me">
												<div class="ldl_box_img" style="background-image:url(images/pre/img_prof_08.png)" title="손언영"></div>
												<div class="ldl_box_user">
													<strong>손언영</strong>
													<span>운영기획팀</span>
												</div>
											</li>
											<li>
												<div class="ldl_box_img" style="background-image:url(images/pre/img_prof_04.png)" title="김민경"></div>
												<div class="ldl_box_user">
													<strong>김민경</strong>
													<span>경영지원팀</span>
												</div>
											</li>
										</ul>
									</div>
								</div>
							</div>
							<div class="ldl_box">
								<div class="ldl_box_in">
									<div class="ldl_chk"><button><span>선택</span></button></div>
									<button class="ldl_box_close" style="display:none;"><span>닫기</span></button>
									<div class="ldl_box_tit">
										<p>네이버페이, 카카오페이 결제 개발</p>
									</div>
									<div class="ldl_box_time">08/29 13:33 업데이트</div>
									<div class="ldl_box_user">
										<ul>
											<li>
												<div class="ldl_box_img" style="background-image:url(images/pre/img_prof_08.png)" title="손언영"></div>
												<div class="ldl_box_user">
													<strong>손언영</strong>
													<span>운영기획팀</span>
												</div>
											</li>
											<li>
												<div class="ldl_box_img" style="background-image:url(images/pre/img_prof_04.png)" title="김민경"></div>
												<div class="ldl_box_user">
													<strong>김민경</strong>
													<span>경영지원팀</span>
												</div>
											</li>
										</ul>
									</div>
								</div>
							</div>
							<div class="ldl_box">
								<div class="ldl_box_in">
									<div class="ldl_chk"><button><span>선택</span></button></div>
									<button class="ldl_box_close" style="display:none;"><span>닫기</span></button>
									<div class="ldl_box_tit">
										<p>네이버페이, 카카오페이 결제 개발</p>
									</div>
									<div class="ldl_box_time">08/29 13:33 업데이트</div>
									<div class="ldl_box_user">
										<ul>
											<li>
												<div class="ldl_box_img" style="background-image:url(images/pre/img_prof_08.png)" title="손언영"></div>
												<div class="ldl_box_user">
													<strong>손언영</strong>
													<span>운영기획팀</span>
												</div>
											</li>
											<li>
												<div class="ldl_box_img" style="background-image:url(images/pre/img_prof_04.png)" title="김민경"></div>
												<div class="ldl_box_user">
													<strong>김민경</strong>
													<span>경영지원팀</span>
												</div>
											</li>
										</ul>
									</div>
								</div>
							</div>
							<div class="ldl_box">
								<div class="ldl_box_in">
									<div class="ldl_chk"><button><span>선택</span></button></div>
									<button class="ldl_box_close" style="display:none;"><span>닫기</span></button>
									<div class="ldl_box_tit">
										<p>네이버페이, 카카오페이 결제 개발</p>
									</div>
									<div class="ldl_box_time">08/29 13:33 업데이트</div>
									<div class="ldl_box_user">
										<ul>
											<li>
												<div class="ldl_box_img" style="background-image:url(images/pre/img_prof_08.png)" title="손언영"></div>
												<div class="ldl_box_user">
													<strong>손언영</strong>
													<span>운영기획팀</span>
												</div>
											</li>
											<li>
												<div class="ldl_box_img" style="background-image:url(images/pre/img_prof_04.png)" title="김민경"></div>
												<div class="ldl_box_user">
													<strong>김민경</strong>
													<span>경영지원팀</span>
												</div>
											</li>
										</ul>
									</div>
								</div>
							</div>
						</div>
					</div>
					<div class="pll_btn">
						<button><span>선택 완료</span></button>
						<button style="display:none;"><span>연결 해제</span></button>
						<button style="display:none;"><span>변경 완료</span></button>
					</div>
				</div>
			</div>
		</div>
	</div>
	


	<?php
		//사용자 레이어(오늘업무(보고,공유,요청),라이브, 챌린지-참여자설정,파티구성원)
		include $home_dir . "/layer/member_user_layer.php";
	?>


	<!-- 파티만들기 -->
	<div class="layer_make" id="layer_make" style="display:none;">
				<div class="lm_deam"></div>
				<div class="lm_in">
					<div class="lm_box">
						<div class="lm_box_in">
							<div class="lm_close">
								<button><span>닫기</span></button>
							</div>
							<div class="lm_top">
								<strong class="lm_tit">
									파티 만들기
								</strong>
							</div>
							<div class="lm_area">
								<div class="layer_user_slc_list">
									<div class="layer_user_slc_list_in" id="layer_user_slc_list_in">
										<ul>
										</ul>
									</div>
								</div>
								<div class="lm_ul_02">
									<input type="text" class="textarea_lm" placeholder="함께 할 업무를 작성해주세요." onkeyup="if(window.event.keyCode==13){ltb()}" id="textarea_lm" />
								</div>
							</div>
							<div class="lm_bottom">
								<button class="btn_off" id="lm_bottom">확인</button>
							</div>
						</div>
					</div>
				</div>
			</div>

	<div class="t_layer rew_layer_character item_prof" style="display:none;">
		<input type='hidden' id='check_profile'>
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
					<?for($i=0; $i<count($character_img_info['idx']); $i++){

						$idx = $character_img_info['idx'][$i];
						$file_path = $character_img_info['file_path'][$i];
						$file_name = $character_img_info['file_name'][$i];
						$fp_flag = $character_img_info['fp_flag'][$i];

						$character_img_src = $file_path.$file_name;

						$posi = $i + 1;

						if($fp_flag == 1){
							$pos_cn = $pos_cn + 1;
							$pos_ht = "class='pos_ht kp$pos_cn'";
						}
					?>
						<li id="posi_<?=$posi?>" <?=$pos_ht?>>
							<div class="tl_profile_box">
								<div class="tl_profile_img" style="background-image:url(<?=$character_img_src?>);">
									<?if($fp_flag == 0 || $img_buy_arr[$idx] != ''){?>
										<button class="btn_profile<?=$member_row_info['profile_type']=='0' && $member_row_info['profile_img_idx']==$idx?" on":""?>" id="profile_img_0<?=$idx?>" value="<?=$idx?>"><span>기본 프로필 이미지1 선택</span></button>
									<?}else{?>
										<button class="btn_profile" id="item_img_0<?=$idx?>" value="<?=$idx?>"><span>기본 프로필 이미지1 선택</span></button>
									<?}?>
								</div>
							</div>
							<?if($fp_flag == 1 && $img_buy_arr[$idx] == ''){?>
								<button class="btn_prof_lock"><span>닫힘</span></button>
							<?}?>
						</li>
					<?}?>
				</ul>
			</div>
			<div class="tl_btn">
				<button id="tl_profile_bt"><span>적용</span></button>
			</div>
		</div>
	</div>	
</div>

<?php
	//튜토리얼 시작 레이어
	include $home_dir . "/layer/tutorial_start.php";

	//튜토리얼 시작 레이어
	include $home_dir . "/layer/tutorial_main_level.php";

	//비밀번호 재설정
	include $home_dir . "/layer/member_repass.php";

	//쪽지보내기 레이어
	include $home_dir . "/layer/mess_pop.php";
	//아이템 레이어
	include $home_dir . "/layer/item_img_buy.php";
	//프로필 팝업
	include $home_dir . "/layer/pro_pop.php";
	//캐릭터 팝업
	include $home_dir . "/layer/char_pop.php";
?>
<?php
	//로딩 페이지
	include $home_dir . "loading.php";
?>

<!-- footer start-->
<? include $home_dir . "/inc_lude/footer.php";?>
<!-- footer end-->
<!-- Project js -->
<script>
function link(pro_idx) {

//미확인 업무로 이동
location.href = "/party/view.php?idx="+pro_idx;

}
</script>
<script src="/party/js/project.js"></script>
<script type="text/javascript">
		$(document).ready(function(){
			window.onbeforeunload = function () { $('.rewardy_loading_01').css('display', 'block'); }
			$(window).load(function () {          //페이지가 로드 되면 로딩 화면을 없애주는 것
				$('.rewardy_loading_01').css('display', 'none');
			});
		});
		// 뒤로가기로 페이지 이동 시 로딩 스크립트 금지
		window.onpageshow = function(event) {
 	     if ( event.persisted || (window.performance && window.performance.navigation.type == 2)) {
			  $('.rewardy_loading_01').css('display', 'none');
  		  }
		}
	</script>
</body>


</html>
