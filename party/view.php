<?
	//header페이지
	$home_dir = str_replace( basename(__DIR__) , "" , __DIR__ );
	include $home_dir . "/inc_lude/header_party_view.php";
	

	$idx = $_GET['idx'];
	$party_idx = preg_replace("/[^0-9]/", "", $idx);
	if($party_idx && $user_id){
		$sql = "update work_todaywork_project set page_count = page_count+1 where idx = '".$party_idx."' and state in (0,1) ";
		$update = updateQuery($sql);
	}
	
	if($party_idx){
		$sql = "select idx from work_todaywork_project_user where state != 9 and email = '".$user_id."' and companyno = '".$companyno."' and project_idx = '".$party_idx."' and party_read_flag = 1";
		$project_read = selectQuery($sql);

		if($project_read){
			$pro_read_idx = $project_read['idx'];
			$sql = "update work_todaywork_project_user set party_read_flag = 0, party_read_date = now() where idx = '".$pro_read_idx."' and email = '".$user_id."'";
			$up_read = updateQuery($sql);
		}


		$sql = "select idx, state, title, date_format(regdate, '%Y-%m-%d') as sdate, com_coin_pro from work_todaywork_project where companyno='".$companyno."' and idx='".$party_idx."'";
		$project_info = selectQuery($sql);
		if($project_info['idx']){
			$project_title = $project_info['title'];
			$project_state = $project_info['state'];
			$sdate = $project_info['sdate'];
			$project_start = new DateTime($sdate);
			$project_end = new DateTime(TODATE);
			$project_diff = date_diff($project_start, $project_end);
			$project_diff_day = $project_diff->days;
			$project_coin = $project_info['com_coin_pro'];
			if(!$project_coin){
				$project_coin = 0;
			}
		}
		//삭제 되었을경우 경고창
		if($project_state == '9'){
			alertMove('삭제된 파티 입니다.', '');
			exit;
		}
	
	

		//파티링크정보
		$sql = "select idx, party_link from work_todaywork_project_info where state='0' and companyno='".$companyno."' and party_idx='".$party_idx."' order by idx asc limit 1";
		$project_allinfo = selectQuery($sql);
		$party_link = $project_allinfo['party_link'];

		//파티장-파티생성자
		$sql = "select idx, email, name, part from work_todaywork_project where state!='9' and companyno='".$companyno."' and idx='".$party_idx."'";
		$project_make_info = selectQuery($sql);
		if($project_make_info['idx']){
			$project_make_uid = $project_make_info['email'];
		}

		//파티구성원
		$sql = "select a.idx, b.email, b.idx as bidx, b.name, b.part, c.profile_type, c.profile_img_idx, d.file_path, d.file_name, case when a.email=b.email then 1 when a.email!=b.email then 0 end as pma";
		$sql .= " ,(select count(1) from work_todaywork as w left join work_todaywork_project_info as f on(w.idx=f.work_idx) where w.state='1' and f.state='0' and f.party_link='".$party_link."' and f.party_idx='".$party_idx."' and w.companyno='".$companyno."' and w.email=b.email and w.editdate!='') as up";
		$sql .= " ,(select count(1) from work_todaywork as w left join work_todaywork_project_info as f on(w.idx=f.work_idx) where w.state='1' and f.state='0' and f.party_link='".$party_link."' and f.party_idx='".$party_idx."' and w.companyno='".$companyno."' and w.email=b.email) as complete";
		$sql .= " ,(select count(1) from work_todaywork as w left join work_todaywork_project_info as f on(w.idx=f.work_idx) where w.state!='9' and f.state='0' and f.party_link='".$party_link."' and f.party_idx='".$party_idx."' and w.companyno='".$companyno."' and w.email=b.email) as work";
		$sql .= " from work_todaywork_project as a left join work_todaywork_project_user as b on(a.idx=b.project_idx)";
		$sql .= " left join work_member as c on b.email = c.email";
		$sql .= " left join work_member_profile_img as d on b.email = d.email";
		$sql .= " where a.state!='9' and b.state='0' and a.companyno='".$companyno."' and c.companyno = '".$companyno."' and a.idx='".$party_idx."' and c.state != '9'";
		$sql .= " order by";
		$sql .= " case when a.email=b.email then a.email end desc,";
		$sql .= " case when a.email!=b.email then b.idx end asc";
		$project_user_info = selectAllQuery($sql);
		//파티구성원 전체인원수
		if($project_user_info['idx']){
			$project_user_cnt = count($project_user_info['idx']);
			$project_up_cnt = @array_sum($project_user_info['up']);
			
		}else{
			$project_user_cnt = 0;
			$project_up_cnt = 0;
		}
		
		$r_coin = $project_coin/$project_user_cnt;
		$r_coin = round($r_coin, 0);
		//좋아요수
		$sql = "select count(1) as cnt from work_todaywork_like where state='0' and companyno='".$companyno."' and work_idx='".$party_idx."' and service='party'";
		$project_heart_info = selectQuery($sql);
		if($project_heart_info){
			$project_heart_cnt = $project_heart_info['cnt'];
		}else{
			$project_heart_cnt = 0;
		}

		$date_tmp = explode("-",TODATE);
		$year = $date_tmp[0];
		$month = $date_tmp[1];
		$day = $date_tmp[2];
		$ret = week_day("$year-$month-$day");
		if($ret){

			//월요일
			$monthday = $ret['month'];

			//금요일
			$friday = $ret['friday'];

			//일요일
			$sunday = $ret['sunday'];
		}

		$week7 = date("Y-m-d",strtotime("-1 week", TODAYTIME));
		
		
		$sc_email = trim(@implode("','", $project_user_info['email']));
		//$where = " and workdate between '".$monthday."' and '".$sunday."'";
		$where = $where .= " and a.email in('".$sc_email."')";

		//업무리스트
		$sql ="select a.idx, a.state, b.state as bstate, a.work_flag, a.part_flag, a.decide_flag, a.secret_flag, a.work_idx, a.repeat_flag, a.notice_flag, a.share_flag, date_format(a.regdate, '%Y.%m.%d') as ymd";
		$sql = $sql .= ", date_format(a.regdate, '%H:%i') as his, a.memo_view, a.contents_view, a.title, a.contents, a.email, a.name, a.req_date, a.workdate, a.regdate";
		$sql = $sql .= " FROM work_todaywork as a 
						left join work_todaywork_project_info as b on(a.idx=b.work_idx)
						";
		$sql = $sql .= " where a.state!='9' and b.state='0'";
		$sql = $sql .= " and b.party_link='".$party_link."' and b.party_idx='".$party_idx."'";
		$sql = $sql .= " and a.companyno='".$companyno."' and b.party_link is not null".$where."";
		$week_info = selectAllQuery($sql);

		//echo "\n\n";
		
		//전체업무수
		$sql = "select count(1) as cnt FROM work_todaywork as a left join work_todaywork_project_info as b on(a.idx=b.work_idx)";
		$sql = $sql .= " where a.state!='9' and b.state='0' and a.companyno='".$companyno."' and b.party_link='".$party_link."' and b.party_idx='".$party_idx."'".$where."";
		//echo $sql;
		$work_cnt_info = selectQuery($sql);
		$work_all_cnt = $work_cnt_info['cnt'];


		for($i=0; $i<count($week_info['idx']); $i++){

			$idx = $week_info['idx'][$i];
			$state = $week_info['state'][$i];
			$work_email = $week_info['email'][$i];
			$send = $week_info['send'][$i];
			$work_name = $week_info['name'][$i];
			$work_flag = $week_info['work_flag'][$i];
			$work_idx = $week_info['work_idx'][$i];
			$repeat_flag = $week_info['repeat_flag'][$i];
			$share_flag = $week_info['share_flag'][$i];
			$memo_view = $week_info['memo_view'][$i];
			$contents_view = $week_info['contents_view'][$i];

			$decide_flag = $week_info['decide_flag'][$i];
			$notice_flag = $week_info['notice_flag'][$i];
			$secret_flag = $week_info['secret_flag'][$i];
			$workdate = $week_info['workdate'][$i];
			$title = $week_info['title'][$i];
			$contents = $week_info['contents'][$i];
			$contents_edit = strip_tags($week_info['contents'][$i]);


			//검색된 단어가 있을경우
			if($search){
				$contents = keywordHightlight($search, $contents);
				$title = keywordHightlight($search, $title);
			}

			$his = $week_info['his'][$i];
			$ymd = $week_info['ymd'][$i];

			$week_works[$workdate]['idx'][] = $idx;
			$week_works[$workdate]['state'][] = $state;
			$week_works[$workdate]['his'][] = $his;

			if ($ymd){
				$ymd_tmp = explode(".",$ymd);
				$ymd_change = $ymd_tmp[1].".".$ymd_tmp[2];
			}

			//요청 및 공유, 보고
			if($work_idx){
				$work_com_idx = $work_idx;
			}else{
				$work_com_idx = $idx;
			}

			$week_works[$workdate]['ymd'][] = $ymd_change;
			$week_works[$workdate]['title'][] = $title;
			$week_works[$workdate]['contents'][] = $contents;
			$week_works[$workdate]['contents_edit'][] = $contents_edit;
			$week_works[$workdate]['email'][] = $work_email;
			$week_works[$workdate]['send'][] = $send;
			$week_works[$workdate]['name'][] = $work_name;
			$week_works[$workdate]['decide_flag'][] = $decide_flag;
			$week_works[$workdate]['work_flag'][] = $work_flag;
			$week_works[$workdate]['work_idx'][] = $work_idx;
			$week_works[$workdate]['repeat_flag'][] = $repeat_flag;
			$week_works[$workdate]['notice_flag'][] = $notice_flag;
			$week_works[$workdate]['share_flag'][] = $share_flag;
			$week_works[$workdate]['secret_flag'][] = $secret_flag;
			$week_works[$workdate]['work_com_idx'][] = $work_com_idx;
			$week_works[$workdate]['memo_view'][] = $memo_view;
			$week_works[$workdate]['contents_view'][] = $contents_view;
		}


		//중복제거
		$week_unique = array_unique($week_info['workdate']);
		$key = 0;
		$new_arr = array();
		foreach($week_unique as $var) {
			$new_arr[$key] = $var;
			$key++;
		}
		rsort($new_arr);

		if(!$search_date){
			$date_tmp = explode("-", TODATE);
			$year = $date_tmp[0];
			$month = $date_tmp[1];
			$day = $date_tmp[2];
			$ret = week_day("$year-$month-$day");
			if($ret){
				//월요일
				$monthday = $ret['month'];

				//금요일
				$friday = $ret['friday'];

				//일요일
				$sunday = $ret['sunday'];

				//월요일, 타임으로
				$month = strtotime($monthday);
			}

		}


		
		//프로필 캐릭터 사진
		$sql = "select idx, file_path, file_name from work_member_character_img where state='0' order by idx asc";
		$character_img_info = selectAllQuery($sql);
		if($character_img_info['idx']){
			for($i=0; $i<count($character_img_info['idx']); $i++){
				$file_path = $character_img_info['file_path'][$i];
				$file_name = $character_img_info['file_name'][$i];
				$profile_character_info[$character_img_info['idx'][$i]] = $file_path.$file_name;
			}
		}

		//회원정보 추출
		for($i=0; $i<count($member_info['idx']); $i++){

			$member_info_email = $member_info['email'][$i];
			$member_info_profile_type = $member_info['profile_type'][$i];
			$member_info_profile_img_idx = $member_info['profile_img_idx'][$i];
			if(!$member_info_profile_img_idx){
				$member_info_profile_img_idx = 5;
			}
			$profile_img_src['type'][$member_info_email] = $member_info_profile_type;
			$profile_img_src['imgidx'][$member_info_email] = $member_info_profile_img_idx;
		}



		//등록된 업무가 있을경우
		if($new_arr){
			$new_arr_edate = end($new_arr);
			$new_arr_sdate = $new_arr[0];

			//조건절
			$where = " and workdate between '".$new_arr_edate."' and '".$new_arr_sdate."'";
			$where2 = " and b.workdate between '".$new_arr_edate."' and '".$new_arr_sdate."'";


			//업무 댓글
			//$sql = "select idx, link_idx, work_idx, email, name, comment, regdate from work_todaywork_comment where state='0' order by idx desc";
			$sql = "select a.idx as cidx, a.link_idx, a.work_idx, a.email, a.name, a.comment, a.cmt_flag, a.secret_flag, CASE WHEN a.editdate is not null then date_format(a.editdate , '%Y-%m-%d') WHEN a.editdate is null then date_format(a.regdate , '%Y-%m-%d') end as ymd,";
			$sql = $sql .= " CASE WHEN a.editdate is not null then date_format(a.editdate , '%m/%d/%y %l:%i:%s %p') WHEN a.editdate is null then date_format(a.regdate , '%m/%d/%y %l:%i:%s %p') end as regdate";
			$sql = $sql .= " ,b.idx"; 
			$sql = $sql .= " from work_todaywork_comment as a left join work_todaywork as b on(a.link_idx=b.idx) 
								where a.state=0 and a.companyno='".$companyno."'".$where2." order by a.regdate desc";
								$works_comment_info = selectAllQuery($sql);
			for($i=0; $i<count($works_comment_info['idx']); $i++){
				$works_comment_info_idx = $works_comment_info['cidx'][$i];
				$works_comment_info_link_idx = $works_comment_info['link_idx'][$i];
				$works_comment_info_work_idx = $works_comment_info['work_idx'][$i];
				$works_comment_info_email = $works_comment_info['email'][$i];
				$works_comment_info_send = $works_comment_info['send'][$i];
				$works_comment_info_name = $works_comment_info['name'][$i];
				$works_comment_info_comment = $works_comment_info['comment'][$i];
				$works_comment_info_comment_strip = strip_tags($works_comment_info['comment'][$i]);
				$works_comment_info_ymd = $works_comment_info['ymd'][$i];
				$works_comment_info_regdate = $works_comment_info['regdate'][$i];
				$works_comment_info_cmt_flag = $works_comment_info['cmt_flag'][$i];
				$works_comment_info_secret_flag = $works_comment_info['secret_flag'][$i];

				if($works_comment_info_link_idx){
					$comment_list[$works_comment_info_link_idx]['cidx'][] = $works_comment_info_idx;
					$comment_list[$works_comment_info_link_idx]['work_idx'][] = $works_comment_info_work_idx;
					$comment_list[$works_comment_info_link_idx]['name'][] = $works_comment_info_name;
					$comment_list[$works_comment_info_link_idx]['email'][] = $works_comment_info_email;
					$comment_list[$works_comment_info_link_idx]['send'][] = $works_comment_info_send;
					$comment_list[$works_comment_info_link_idx]['comment'][] = $works_comment_info_comment;
					$comment_list[$works_comment_info_link_idx]['comment_strip'][] = $works_comment_info_comment_strip;
					$comment_list[$works_comment_info_link_idx]['ymd'][] = $works_comment_info_ymd;
					$comment_list[$works_comment_info_link_idx]['regdate'][] = $works_comment_info_regdate;
					$comment_list[$works_comment_info_link_idx]['cmt_flag'][] = $works_comment_info_cmt_flag;
					$comment_list[$works_comment_info_link_idx]['secret_flag'][] = $works_comment_info_secret_flag;
				}
			}
		}

				
		//첨부파일정보
		$sql = "select idx, work_idx, email, num, file_path, file_name, file_real_name, workdate from work_filesinfo_todaywork where state='0' and companyno='".$companyno."' order by idx asc";
		$todaywork_file_info = selectAllQuery($sql);
		for($i=0; $i<count($todaywork_file_info['idx']); $i++){

			$tdf_idx = $todaywork_file_info['idx'][$i];
			$tdf_num = $todaywork_file_info['num'][$i];
			$tdf_email = $todaywork_file_info['email'][$i];
			$tdf_work_idx = $todaywork_file_info['work_idx'][$i];
			$tdf_file_path = $todaywork_file_info['file_path'][$i];
			$tdf_file_name = $todaywork_file_info['file_name'][$i];
			$tdf_file_real_name = $todaywork_file_info['file_real_name'][$i];
			$tdf_file_workdate = $todaywork_file_info['workdate'][$i];

			$tdf_files[$tdf_work_idx]['idx'][] = $tdf_idx;
			$tdf_files[$tdf_work_idx]['num'][] = $tdf_num;
			$tdf_files[$tdf_work_idx]['email'][] = $tdf_email;
			$tdf_files[$tdf_work_idx]['file_path'][] = $tdf_file_path;
			$tdf_files[$tdf_work_idx]['tdf_file_name'][] = $tdf_file_name;
			$tdf_files[$tdf_work_idx]['file_real_name'][] = $tdf_file_real_name;
		}


		$sql = "select email,title from work_todaywork_project where state = 0 and companyno = '".$companyno."' and idx = '".$party_idx."'";
		$p_info = selectQuery($sql);

		if($p_info['email']){
			if($user_id == $p_info['email']){
				$party_close = 0;
			}else{
				$party_close = 1;
			}
		}

	}
	//회원정보
	$member_row_info_my = member_row_info($user_id);
	$character_head_img_info = character_img_info();
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
<input type="hidden" id="chall_user_cnt" value="<?=$member_total_cnt?>">
<input type="hidden" id="chall_user_chk">
<div class="rew_warp">
	<div class="rew_warp_in">
		<div class="rew_box">
		<input type="hidden" id="imsi" value="<?=$user['name']?>">
			<div class="rew_box_in">
			<?include $home_dir . "/inc_lude/header_new.php";?>
				<!-- menu -->
				<? include $home_dir . "/inc_lude/menu_party_view_index.php";?>
				<!-- //menu -->

				<!-- 콘텐츠 -->
				<div class="rew_conts">
					<div class="rew_conts_in">

						<div class="rew_conts_scroll_10v">

							<div class="rew_todaywork rew_party_wrap">
								<div class="rew_todaywork_in">

									<? 
									if($project_make_uid == $user_id){?>
										<input type="hidden" value="0" id="party_close_flag"/>
									<?}
									?>
									<div class="rew_member_sub_func_tab">
										<div class="rew_member_sub_func_tab_in">
											<div class="rew_cha_count">
												<span>전체</span>
												<strong><?= $work_all_cnt?></strong>
											</div>
											<div class="rew_member_sub_func_period">
												<div class="rew_cha_search_box">
													<input type="text" class="input_search" id="party_input_search" placeholder="키워드">
													<button id="btn_input_search"><span>검색</span></button>
												</div>
												<button class="btn_inquiry" id="btn_party_search" style ="display:none;"><span>조회</span></button>
												<input type="hidden" id="party_idx" value="<?=$party_idx?>">
											</div>
										</div>
									</div>
									
									<div class="tdw_list">
										<div class="tdw_list_in">
											<div class="tdw_list_ww" id="tdw_list_ww">
											<?
												if(count($new_arr)>0) {
												
													for($i=0; $i<count($new_arr); $i++){
														$workdate = $new_arr[$i];
														$day_list = $week[date("w", strtotime($workdate))];
														$day_tmp = @explode("-", $workdate);
														if($day_tmp){
															$week_day = $day_tmp[1].".".$day_tmp[2];
														}

													?>
														<div class="tdw_list_ww_box">
															<strong class="tdw_list_title_date"><?=$week_day?> (<?=$day_list?>)</strong>
															
															<ul class="tdw_list_ul">

																<?for($j=0; $j < count($week_works[$workdate]['contents']); $j++){
																	$week_works_idx = $week_works[$workdate]['idx'][$j];
																	$work_idx = $week_works[$workdate]['work_idx'][$j];
																	$week_works_state = $week_works[$workdate]['state'][$j];
																	$week_works_his = $week_works[$workdate]['his'][$j];
																	$week_works_email = $week_works[$workdate]['email'][$j];
																	$week_works_name = $week_works[$workdate]['name'][$j];
																	$work_flag = $week_works[$workdate]['work_flag'][$j];
																	$secret_flag = $week_works[$workdate]['secret_flag'][$j];
																	$send = $week_works[$workdate]['send'][$j];

																	$work_wtitle = $week_works[$workdate]['title'][$j];
																	$work_contents = $week_works[$workdate]['contents'][$j];

																	//알림설정
																	$notice_flag = $week_works[$workdate]['notice_flag'][$j];

																	//공유설정
																	$share_flag = $week_works[$workdate]['share_flag'][$j];



																	//요청 및 공유, 보고
																	if($work_idx){
																		$work_com_idx = $work_idx;
																	}else{
																		$work_com_idx = $week_works_idx;
																	}
																	
																?>
																	<? if(($secret_flag == '1' && $week_works_email == $user_id)){?>
																		<li class="tdw_list_li<?=$li_class?>" id="workslist_<?=$week_works_idx?>" value="<?=($j+1)?>">
																		<div class="tdw_list_box<?=($week_works_state=='1')?" on":""?>" id="tdw_list_box_<?=$week_works_idx?>">
																			<?
																				$sql = "select a.idx, a.email, a.name, a.part, a.partno, a.gender, a.profile_type, a.profile_img_idx, b.file_path, b.file_name 
																				        from work_member a 
																						left join work_member_profile_img b on a.email = b.email
																						where 1=1
																						and a.state = '0' and a.email = '".$week_works_email."'";
																				$user_char = selectQuery($sql);
																				$profile_type = $user_char['profile_type'];
																				$profile_img_idx = $user_char['profile_img_idx'];
																				$profile_img =  'https://rewardy.co.kr'.$user_char['file_path'].$user_char['file_name'];
																				?>
																				<div class="tdw_list_chk">
																					<?if($week_works_email != $user_id){?>
																							<button class="btn_tdw_list_chk_user" value="<?=$week_works_idx?>" <?=$tdw_list?" id='tdw_dlist_chk'":""?> style="background-image:url('<?=$week_works_email != $user_id ?$profile_img:""?>'); cursor:unset;"><span>완료체크</span></button>
																					<?}else{?>
																						<button class="btn_tdw_list_chk" value="<?=$week_works_idx?>" <?=$tdw_list?" id='tdw_dlist_chk'":""?>><span>완료체크</span></button>
																					<?}?>
																				</div>
																			<div class="tdw_list_desc <?=$secret_flag == '1'?"lock":""?>">
																				<?/*<p><span></span><?=$week_works_contents?></p>*/?>

																				<?if($work_idx){?>
																					<?if($notice_flag=="1"){?>
																						<p <?=$edit_id?> id="notice_link" value="<?=$work_idx?>">
																					<?}else{?>
																						<p <?=$edit_id?>>
																					<?}?>
																						<?=textarea_replace($work_contents)?>
																					</p>

																					<?}else{?>

																					<!--보고업무-->
																						<p id="tdw_wlist_edit_<?=$week_works_idx?>">
																						<?=textarea_replace($work_contents)?></p>
																					<?}?>

																			</div>
																			<div class="tdw_list_function">
																				<? if($user_id == $week_works_email){?>
																					<div class="tdw_list_function_in">
																						<input type="hidden" id="work_idx" value="<?=$week_works_idx?>">
																						<input type="hidden" id="work_date" value="<?=$workdate?>">
																						<button class="tdw_list_party_date"><span><?=$week_works_his?></span></button>
																						<button class="tdw_list_party_name"><span><?=$week_works_name?></span></button>
																						<?php if($secret_flag == '1'){?>
																							<button class="tdw_list_party_memo_secret" id="tdw_list_party_memo" value="<?=$week_works_idx?>"><span>메모</span></button>
																						<?php }else if($user_id != 'sun@bizforms.co.kr' || $user_id != 'yoonjh8932@naver.com' || $user_id != 'ansrkdtks2@naver.com' || $user_id != 'bapzelo1020@gmail.com' || $user_id != 'earkite.n@gmail.com'){ ?>
																							<button class="tdw_list_party_memo_secret" id="tdw_list_party_memo" value="<?=$week_works_idx?>"><span>메모</span></button>
																						<?php }else{ ?>
																							<button class="tdw_list_party_memo" id="tdw_list_party_memo" value="<?=$week_works_idx?>"><span>메모</span></button>
																						<?php } ?>
																						<button class="tdw_list_party_link<?=($user_id==$week_works_email)?" on":""?>" id="tdw_list_party_link" value="<?=$week_works_idx?>"><span>파티연결</span></button>
																					</div>
																				<?}else{
																					$sql = "select idx,state,companyno,service,work_idx,email,send_email,workdate from work_todaywork_like where state = '0' and companyno = '".$companyno."' and send_email = '".$user_id."' and work_idx = '".$week_works_idx."'";
																					$like_coma = selectQuery($sql);
																					$sql = "select * from work_todaywork where state = '0' and companyno = '".$companyno."' and idx = '".$week_works_idx."'";
																					$work_kind = selectQuery($sql);?>
																					<div class="tdw_list_function_in">
																						<input type="hidden" id="work_flag_<?=$week_works_idx?>" value="<?=$work_kind['work_flag']?>">
																						<input type="hidden" id="work_idx" value="<?=$week_works_idx?>">
																						<input type="hidden" id="work_date" value="<?=$workdate?>">
																						<input type="hidden" id="pu_list_id_<?=$week_works_idx?>" value="<?=$week_works_email?>">
																						<button class="tdw_list_party_date"><span><?=$week_works_his?></span></button>
																						<button class="tdw_list_party_name"><span><?=$week_works_name?></span></button>
																						<?php if($secret_flag == '1'){?>
																							<button class="tdw_list_party_memo_secret" id="tdw_list_party_memo" value="<?=$week_works_idx?>"><span>메모</span></button>
																						<?php }else if($user_id != 'sun@bizforms.co.kr' || $user_id != 'yoonjh8932@naver.com' || $user_id != 'ansrkdtks2@naver.com' || $user_id != 'bapzelo1020@gmail.com' || $user_id != 'earkite.n@gmail.com'){ ?>
																							<button class="tdw_list_party_memo_secret" id="tdw_list_party_memo" value="<?=$week_works_idx?>"><span>메모</span></button>
																						<?php }else{ ?>
																							<button class="tdw_list_party_memo" id="tdw_list_party_memo" value="<?=$week_works_idx?>"><span>메모</span></button>
																						<?php } ?>
																						<button class="tdw_list_100c" title="100코인" id="coin_reward" value="<?=$week_works_idx?>"><span>100</span></button>
																						<button class="tdw_list_party_heart<?=$like_coma>0?" on":""?>" id="tdw_list_party_heart_<?=$week_works_idx?>" value="<?=$week_works_idx?>"><span>좋아요</span></button>
																					</div>
																					
																				<?}?>
																			</div>
																			<?//첨부파일 정보
																			//나의업무, 요청업무
																			if(in_array($work_flag, array('1','2','3'))){
																				if($tdf_files[$work_com_idx]['file_path']){?>
																					<div class="tdw_list_file">
																						<?for($k=0; $k<count($tdf_files[$work_com_idx]['file_path']); $k++){?>
																							<div class="tdw_list_file_box">
																								<button class="btn_list_file" id="btn_list_file_<?=$k?>" value="<?=$tdf_files[$work_com_idx]['idx'][$k]?>"><span><?=$tdf_files[$work_com_idx]['file_real_name'][$k]?></span></button>
																								<?//보고업무 작성한 사용자만 삭제
																								if($user_id==$tdf_files[$work_com_idx]['email'][$k]){?>
																									<button class="btn_list_file_del" id="btn_list_fdel" value="<?=$tdf_files[$work_com_idx]['idx'][$k]?>" title="삭제"><span>삭제</span></button>
																								<?}?>
																							</div>
																						<?}?>
																					</div>
																				<?}
																			}?>
																			
																		</div>

																		<?//댓글내용?>
																		<div class="tdw_list_memo_area"><!--작업-->
																			<?/*<div class="tdw_list_memo_area_in<?=$memo_view_in?>" id="memo_area_list_<?=$week_works_idx?>">*/?>
																			<div class="tdw_list_memo_area_in<?=$memo_view_in?>" id="tdw_list_memo_area_in_<?=$week_works_idx?>">
																				<?//댓글리스트
																				//요청업무
																				if($work_flag == '3'){?>
																					<?if($comment_list[$work_com_idx]){?>
																						<?for($k=0; $k<count($comment_list[$work_com_idx]['cidx']); $k++){
																							$comment_idx = $comment_list[$work_com_idx]['cidx'][$k];

																							$chis = $comment_list[$work_com_idx]['regdate'][$k];
																							$ymd = $comment_list[$work_com_idx]['ymd'][$k];
																							$cmt_flag = $comment_list[$work_com_idx]['cmt_flag'][$k];
																							if($chis){
																								$chis = str_replace("  "," ", $chis);
																								$chis_tmp = @explode(" ", $chis);
																								if ($chis_tmp['2'] == "PM"){
																									$after = "오후 ";
																								}else{
																									$after = "오전 ";
																								}
																								$ctime = @explode(":", $chis_tmp['1']);
																								$chiss = $ymd . " " . $after . $ctime['0'] .":". $ctime['1'];
																							}

																							$sql = "select a.name as name,a.comment as comment,a.send_name as send from work_todaywork_like a join work_todaywork_comment b";
																							$sql = $sql." on a.com_idx = b.idx where a.state != 9 and a.com_idx = '".$comment_idx."'";
																							$work_give_list = selectQuery($sql);

																							if($work_give_list){
																								$work_give_like_name = $work_give_list['name'];
																								$work_give_like_comment = $work_give_list['comment'];
																								$work_send_like_name = $work_give_list['send'];
																							}

																							// 코인보상 레이어 업무 요청한 사람만 보이게(김정훈)
																							$sql = "select idx from work_todaywork where idx = '".$week_works_idx."' and work_idx is null";
																							$work_link_coin = selectQuery($sql);

																							$sql = "select a.idx from work_todaywork_like a join work_todaywork_comment b on a.work_idx = b.link_idx";
																							$sql = $sql." where b.idx = '".$comment_idx."' and a.ai_like_idx = b.ai_like_idx and send_email = '".$user_id."'";
																							$click_like = selectQuery($sql);

																							$sql = "select a.idx from work_todaywork_like a join work_todaywork_comment b on a.work_idx = b.idx where b.idx = '".$comment_idx."'";
																							$sql = $sql." and a.state = 0 and a.send_email = '".$user_id."'";
																							$cli_like = selectQuery($sql);

																							$sql = "select idx from work_todaywork_comment where idx = '".$comment_idx."' and email = '".$user_id."'";
																							$my_like = selectQuery($sql);

																							$sql = "select idx from work_todaywork_comment where idx = '".$comment_idx."' and like_email = '".$user_id."'";
																							$my_coin_like = selectQuery($sql);


																						?>

																						<div class="tdw_list_memo_desc" id="comment_list_<?=$comment_idx?>" >
																							<?if($cmt_flag == 1){?>
																								<!-- 좋아요 변경으로 인한 코드 -->
																								<?if($work_give_list){?>
																									<div class="tdw_list_memo_name"><?=$work_send_like_name?></div>
																								<?}else{?>
																									<div class="tdw_list_memo_name ai">AI</div>
																								<?}?>
																							<?}else{?>
																								<?if($cmt_flag != 2){?>
																									<div class="tdw_list_memo_name"><?=$comment_list[$work_com_idx]['name'][$k]?></div>
																								<?}?>
																							<?}?>

																							<!-- 좋아요 변경으로 인한 코드(김정훈) -->
																							<?if($cmt_flag == 1){?>
																								<?//좋아요 보낸 내역이 있을때
																								if($work_give_list){?>
																									<button class="btn_memo_jjim on" value="<?=$comment_idx?>"><span>좋아요</span></button>
																								<?}?>
																							<?}?>

																							<div class="tdw_list_memo_conts">

																								<?if(!$cmt_flag && $user_id==$comment_list[$work_com_idx]['email'][$k]){?>
																									<span class="tdw_list_memo_conts_txt" id="tdw_list_memo_conts_txt_<?=$comment_idx?>"><?=textarea_replace($comment_list[$work_com_idx]['comment'][$k])?></span>
																								<?}else if($cmt_flag == 1 && $work_give_list){?>
																									<span class="tdw_list_memo_conts_txt"><?=textarea_replace($comment_list[$work_com_idx]['comment'][$k])?></span>
																								<?}else{?>
																									<?if($cmt_flag != 2){?>
																										<span class="tdw_list_memo_conts_txt"><?=textarea_replace($comment_list[$work_com_idx]['comment'][$k])?></span>
																									<?}?>
																								<?}?>
																									<?if($cmt_flag != 2){?>
																										<em class="tdw_list_memo_conts_date"><?=$chiss?>
																									<?}?>

																								<?//자동 ai댓글?>
																								<?if($cmt_flag == 1){?>
																									<?if($work_link_coin && !$my_coin_like){?>
																										<?if($click_like){?>
																											<button class="btn_memo_jjim on" value="<?=$comment_idx?>"><span>좋아요</span></button>
																										<?}else{?>
																											<button class="btn_memo_jjim" id="btn_memo_jjim_<?=$comment_idx?>" value="<?=$comment_idx?>"><span>좋아요</span></button>
																										<?}?>
																									<?}?>
																								<?}else{?>
																									<?if($cmt_flag != 2){?>
																										<?if(!$my_like){?>
																											<?if($cli_like){?>
																												<button class="btn_memo_jjim on" value="<?=$comment_idx?>"><span>좋아요</span></button>
																												
																											<?}else{?>
																												<button class="btn_memo_jjim" id="btn_memo_jjim_<?=$comment_idx?>" value="<?=$comment_idx?>"><span>좋아요</span></button>
																												
																											<?}?>
																											<input type="hidden" value="<?=$comment_list[$work_com_idx]['email'][$k]?>" id="comment_idx_<?=$comment_idx?>">
																										<?}?>
																									<?}?>
																								<?}?>

																								<?if(!$cmt_flag && $user_id==$comment_list[$work_com_idx]['email'][$k]){?>
																									<button class="btn_memo_del" id="btn_memo_del" value="<?=$comment_idx?>"><span>삭제</span></button>
																								<?}?>

																								<?if($cmt_flag != 2){?>
																									</em>
																								<?}?>
																							</div>
																						</div>
																					<?}?>
																				<?}?>

																				<?}else{?>
																					<?//받은업무
																					if ($work_idx){?>

																						<?if($comment_list[$work_idx]){?>
																							<?for($k=0; $k<count($comment_list[$work_idx]['cidx']); $k++){
																								$comment_idx = $comment_list[$work_idx]['cidx'][$k];
																								$chis = $comment_list[$work_idx]['regdate'][$k];
																								$ymd = $comment_list[$work_idx]['ymd'][$k];
																								$cmt_flag = $comment_list[$work_idx]['cmt_flag'][$k];

																								if($chis){
																									$chis = str_replace("  "," ", $chis);
																									$chis_tmp = @explode(" ", $chis);
																									if ($chis_tmp['2'] == "PM"){
																										$after = "오후 ";
																									}else{
																										$after = "오전 ";
																									}
																									$ctime = @explode(":", $chis_tmp['1']);
																									$chiss = $ymd . " " . $after . $ctime['0'] .":". $ctime['1'];
																								}

																								$sql = "select a.name as name,a.comment as comment,a.send_name as send from work_todaywork_like a join work_todaywork_comment b";
																								$sql = $sql." on a.com_idx = b.idx where a.state != 9 and a.com_idx = '".$comment_idx."'";
																								$work_give_list = selectQuery($sql);

																								if($work_give_list){
																									$work_give_like_name = $work_give_list['name'];
																									$work_give_like_comment = $work_give_list['comment'];
																									$work_send_like_name = $work_give_list['send'];
																								}

																								$sql = "select a.idx from work_todaywork_like a join work_todaywork_comment b on a.work_idx = b.idx where b.idx = '".$comment_idx."'";
																								$sql = $sql." and a.state = 0 and a.send_email = '".$user_id."'";
																								$cli_like = selectQuery($sql);
																								?>
	
																								<div class="tdw_list_memo_desc" id="comment_list_<?=$comment_idx?>" >

																									<?if($cmt_flag == 1){?>
																										<!-- 좋아요 변경으로 인한 코드 -->
																										<?if($work_give_list){?>
																											<div class="tdw_list_memo_name"><?=$work_send_like_name?></div>
																										<?}else{?>
																											<div class="tdw_list_memo_name ai">AI</div>
																										<?}?>
																									<?}else{?>
																										<?if($cmt_flag !=2){?>
																											<div class="tdw_list_memo_name"><?=$comment_list[$work_idx]['name'][$k]?></div>
																										<?}?>
																									<?}?>

																									<!-- 좋아요 변경으로 인한 코드(김정훈) -->
																									<?if($cmt_flag == 1){?>
																										<?//좋아요 보낸 내역이 있을때
																										if($work_give_list){?>
																											<button class="btn_memo_jjim on" value="<?=$comment_idx?>"><span>좋아요</span></button>
																										<?}?>
																									<?}?>

																									<div class="tdw_list_memo_conts">

																										<?if(!$cmt_flag && $user_id==$comment_list[$work_idx]['email'][$k]){?>
																											<!-- 일반 메모 -->
																											<span class="tdw_list_memo_conts_txt" id="tdw_list_memo_conts_txt_<?=$comment_idx?>"><?=textarea_replace($comment_list[$work_idx]['comment'][$k])?></span>
																										<?}else if($cmt_flag == 1 && $work_give_list){?>
																											<!-- 좋아요 받았을 때 문장 -->
																											<span class="tdw_list_memo_conts_txt"><?=textarea_replace($comment_list[$work_idx]['comment'][$k])?></span>
																										<?}else{?>
																											<?if($cmt_flag != 2){?>
																												<!-- AI 문장 -->
																												<span class="tdw_list_memo_conts_txt"><?=textarea_replace($comment_list[$work_idx]['comment'][$k])?></span>
																											<?}?>
																										<?}?>

																										<?if($cmt_flag != 2){?>
																											<em class="tdw_list_memo_conts_date"><?=$chiss?>
																										<?}?>

																										<?//자동 ai댓글?>
																										<?if($cmt_flag == 1){?>

																										<?}else{?>
																											<?if($cmt_flag != 2){?>
																												<?if($user_id!=$comment_list[$work_idx]['email'][$k]){?>
																													<?if($cli_like){?>
																														<button class="btn_memo_jjim on" value="<?=$comment_idx?>"><span>좋아요</span></button>
																														
																													<?}else{?>
																														<button class="btn_memo_jjim" id="btn_memo_jjim_<?=$comment_idx?>" value="<?=$comment_idx?>"><span>좋아요</span></button>
																													<?}?>
																													<input type="hidden" value="<?=$comment_list[$work_com_idx]['email'][$k]?>" id="comment_idx_<?=$comment_idx?>">
																												<?}?>
																											<?}?>
																										<?}?>

																										<?if(!$cmt_flag && $user_id==$comment_list[$work_idx]['email'][$k]){?>
																											<button class="btn_memo_del" id="btn_memo_del" value="<?=$comment_idx?>"><span>삭제</span></button>
																										<?}?>
																										</em>
																									</div>
																								</div>

																							<?}?>

																						<?}?>

																					<?}else{?>
																						
																						<?
																						//일반업무
																						if($comment_list[$week_works_idx]){?>
																							<?for($k=0; $k<count($comment_list[$week_works_idx]['cidx']); $k++){
																								$comment_idx = $comment_list[$week_works_idx]['cidx'][$k];

																								$chis = $comment_list[$week_works_idx]['regdate'][$k];
																								$ymd = $comment_list[$week_works_idx]['ymd'][$k];
																								$cmt_flag = $comment_list[$week_works_idx]['cmt_flag'][$k];

																								if($chis){
																									$chis = str_replace("  "," ", $chis);
																									$chis_tmp = @explode(" ", $chis);
																									if ($chis_tmp['2'] == "PM"){
																										$after = "오후 ";
																									}else{
																										$after = "오전 ";
																									}
																									$ctime = @explode(":", $chis_tmp['1']);
																									$chiss = $ymd . " " . $after . $ctime['0'] .":". $ctime['1'];
																								}

																								$sql = "select a.name as name,a.comment as comment,a.send_name as send from work_todaywork_like a join work_todaywork_comment b";
																								$sql = $sql." on a.com_idx = b.idx where a.state != 9 and a.com_idx = '".$comment_idx."'";
																								$work_give_list = selectQuery($sql);

																								if($work_give_list){
																									$work_give_like_name = $work_give_list['name'];
																									$work_give_like_comment = $work_give_list['comment'];
																									$work_send_like_name = $work_give_list['send'];
																								}

																								$sql = "select a.idx from work_todaywork_like a join work_todaywork_comment b on a.work_idx = b.idx where b.idx = '".$comment_idx."'";
																								$sql = $sql." and a.state = 0 and a.send_email = '".$user_id."'";
																								$cli_like = selectQuery($sql);

																							?>
																							<div class="tdw_list_memo_desc" id="comment_list_<?=$comment_idx?>" >
																								<?if($cmt_flag == 1){?>
																									<!-- 좋아요 변경으로 인한 코드 -->
																									<?if($work_give_list){?>
																										<div class="tdw_list_memo_name"><?=$work_send_like_name?></div>
																									<?}else{?>
																										<div class="tdw_list_memo_name ai">AI</div>
																									<?}?>
																								<?}else{?>
																									<?if($cmt_flag != 2){?>
																										<div class="tdw_list_memo_name"><?=$comment_list[$week_works_idx]['name'][$k]?></div>
																									<?}?>
																								<?}?>

																								<!-- 좋아요 변경으로 인한 코드(김정훈) -->
																								<?if($cmt_flag == 1){?>
																									<?//좋아요 보낸 내역이 있을때
																									if($work_give_list){?>
																										<button class="btn_memo_jjim on" value="<?=$comment_idx?>"><span>좋아요</span></button>
																									<?}?>
																								<?}?>


																								<div class="tdw_list_memo_conts">
																									<?if(!$cmt_flag && $user_id==$comment_list[$week_works_idx]['email'][$k]){?>
																										<!-- 일반 메모 -->
																										<span class="tdw_list_memo_conts_txt" id="tdw_list_memo_conts_txt_<?=$comment_idx?>"><?=textarea_replace($comment_list[$week_works_idx]['comment'][$k])?></span>
																									<?}else if($cmt_flag == 1 && $work_give_list){?>
																										<!-- 좋아요 받았을 때 문장 -->
																										<span class="tdw_list_memo_conts_txt"><?=textarea_replace($comment_list[$week_works_idx]['comment'][$k])?></span>
																									<?}else{?>
																										<?if($cmt_flag != 2){?>
																											<!-- AI 문장 -->
																											<span  class="tdw_list_memo_conts_txt"><?=textarea_replace($comment_list[$week_works_idx]['comment'][$k])?></span>
																										<?}?>
																									<?}?>

																									<?if($cmt_flag != 2){?>
																										<em class="tdw_list_memo_conts_date"><?=$chiss?>
																									<?}?>

																										<?//자동 ai댓글?>
																										<?if($cmt_flag == 1){?>

																										<?}else{?>
																											<?if($cmt_flag != 2){?>
																												<?if($user_id!=$comment_list[$week_works_idx]['email'][$k]){?>
																													<?if($cli_like){?>
																														<button class="btn_memo_jjim on" value="<?=$comment_idx?>"><span>좋아요</span></button>
																													<?}else{?>
																														<button class="btn_memo_jjim" id="btn_memo_jjim_<?=$comment_idx?>" value="<?=$comment_idx?>"><span>좋아요</span></button>
																													<?}?>
																													<input type="hidden" value="<?=$comment_list[$work_com_idx]['email'][$k]?>" id="comment_idx_<?=$comment_idx?>">
																												<?}?>
																											<?}?>
																										<?}?>

																									<?if(!$cmt_flag && $user_id==$comment_list[$week_works_idx]['email'][$k]){?>
																										<button class="btn_memo_del" id="btn_memo_del" value="<?=$comment_idx?>"><span>삭제</span></button>
																									<?}?>

																									<?if($cmt_flag != 2){?>
																										</em>
																									<?}?>

																								</div>
																							</div>
																							<?}?>
																						<?}?>
																					<?}?>
																				<?}?>
																			</div>

																			<div class="tdw_list_memo_onoff" <?=$memo_view_bt_style?>>
																				<button class="btn_list_memo_onoff<?=$memo_view_bt?>" id="btn_list_memo_onoff_<?=$week_works_idx?>" value="<?=$week_works_idx?>" <?if(trim($memo_view_bt)=="on"){ echo "title='메모 접기'"; }else{ echo "title='메모 펼치기'"; }?>><span>메모 접기/펼치기</span></button>
																			</div>

																		</div>

																		</li>
																	<? }else if($secret_flag != '1'){?>
																		<li class="tdw_list_li<?=$li_class?>" id="workslist_<?=$week_works_idx?>" value="<?=($j+1)?>">
																		<div class="tdw_list_box<?=($week_works_state=='1')?" on":""?>" id="tdw_list_box_<?=$week_works_idx?>">
																			<div class="tdw_list_chk">
																				<?
																				$sql = "select a.idx, a.email, a.name, a.part, a.partno, a.gender, a.profile_type, a.profile_img_idx, b.file_path, b.file_name 
																				        from work_member a 
																						left join work_member_profile_img b on a.email = b.email
																						where 1=1
																						and a.state = '0' and a.email = '".$week_works_email."'";
																				$user_char = selectQuery($sql);
																				$profile_type = $user_char['profile_type'];
																				$profile_img_idx = $user_char['profile_img_idx'];
																				$profile_img =  'https://rewardy.co.kr'.$user_char['file_path'].$user_char['file_name'];
																				?>
																					<?if($week_works_email != $user_id){?>
																							<button class="btn_tdw_list_chk_user" value="<?=$week_works_idx?>" <?=$tdw_list?" id='tdw_dlist_chk'":""?> style="background-image:url('<?=$week_works_email != $user_id ?$profile_img:""?>'); cursor:unset;"><span>완료체크</span></button>
																					<?}else{?>
																							<button class="btn_tdw_list_chk" value="<?=$week_works_idx?>" <?=$tdw_list?" id='tdw_dlist_chk'":""?>><span>완료체크</span></button>
																					<?}?>
																			</div>
																			<div class="tdw_list_desc">
																				<?/*<p><span></span><?=$week_works_contents?></p>*/?>

																				<?if($work_idx){?>
																					<?if($notice_flag=="1"){?>
																						<p <?=$edit_id?> id="notice_link" value="<?=$work_idx?>">
																					<?}else{?>
																						<p <?=$edit_id?>>
																					<?}?>
																						<?=textarea_replace($work_contents)?>
																					</p>

																					<?}else{?>

																					<!--보고업무-->
																						<p id="tdw_wlist_edit_<?=$week_works_idx?>">
																						<?=textarea_replace($work_contents)?></p>
																					<?}?>

																			</div>
																			<div class="tdw_list_function">
																				<? if($user_id == $week_works_email){?>
																					<div class="tdw_list_function_in">
																						<input type="hidden" id="work_idx" value="<?=$week_works_idx?>">
																						<input type="hidden" id="work_date" value="<?=$workdate?>">
																						<button class="tdw_list_party_date"><span><?=$week_works_his?></span></button>
																						<button class="tdw_list_party_name"><span><?=$week_works_name?></span></button>
																						<?php if($secret_flag == '1'){?>
																							<button class="tdw_list_party_memo_secret" id="tdw_list_party_memo" value="<?=$week_works_idx?>"><span>메모</span></button>
																						<?php }else{ ?>
																							<button class="tdw_list_party_memo" id="tdw_list_party_memo" value="<?=$week_works_idx?>"><span>메모</span></button>
																						<?php } ?>
																						<button class="tdw_list_party_link<?=($user_id==$week_works_email)?" on":""?>" id="tdw_list_party_link" value="<?=$week_works_idx?>"><span>파티연결</span></button>
																					</div>
																				<?}else{
																					$sql = "select idx,state,companyno,service,work_idx,email,send_email,workdate from work_todaywork_like where state = '0' and companyno = '".$companyno."' and send_email = '".$user_id."' and work_idx = '".$week_works_idx."'";
																					$like_coma = selectQuery($sql);
																					$sql = "select * from work_todaywork where state = '0' and companyno = '".$companyno."' and idx = '".$week_works_idx."'";
																					$work_kind = selectQuery($sql);?>
																					<div class="tdw_list_function_in">
																						<input type="hidden" id="work_flag_<?=$week_works_idx?>" value="<?=$work_kind['work_flag']?>">
																						<input type="hidden" id="work_idx" value="<?=$week_works_idx?>">
																						<input type="hidden" id="work_date" value="<?=$workdate?>">
																						<input type="hidden" id="pu_list_id_<?=$week_works_idx?>" value="<?=$week_works_email?>">
																						<button class="tdw_list_party_date"><span><?=$week_works_his?></span></button>
																						<button class="tdw_list_party_name"><span><?=$week_works_name?></span></button>
																						<?php if($secret_flag == '1'){?>
																							<button class="tdw_list_party_memo_secret" id="tdw_list_party_memo" value="<?=$week_works_idx?>"><span>메모</span></button>
																						<?php }else{ ?>
																							<button class="tdw_list_party_memo" id="tdw_list_party_memo" value="<?=$week_works_idx?>"><span>메모</span></button>
																						<?php } ?>
																						<button class="tdw_list_100c" title="100코인" id="coin_reward" value="<?=$week_works_idx?>"><span>100</span></button>
																						<button class="tdw_list_party_heart<?=$like_coma>0?" on":""?>" id="tdw_list_party_heart_<?=$week_works_idx?>" value="<?=$week_works_idx?>"><span>좋아요</span></button>
																					</div>
																					
																				<?}?>
																			</div>
																			<?//첨부파일 정보
																			//나의업무, 요청업무
																			if(in_array($work_flag, array('1','2','3'))){
																				if($tdf_files[$work_com_idx]['file_path']){?>
																					<div class="tdw_list_file">
																						<?for($k=0; $k<count($tdf_files[$work_com_idx]['file_path']); $k++){?>
																							<div class="tdw_list_file_box">
																								<button class="btn_list_file" id="btn_list_file_<?=$k?>" value="<?=$tdf_files[$work_com_idx]['idx'][$k]?>"><span><?=$tdf_files[$work_com_idx]['file_real_name'][$k]?></span></button>
																								<?//보고업무 작성한 사용자만 삭제
																								if($user_id==$tdf_files[$work_com_idx]['email'][$k]){?>
																									<button class="btn_list_file_del" id="btn_list_fdel" value="<?=$tdf_files[$work_com_idx]['idx'][$k]?>" title="삭제"><span>삭제</span></button>
																								<?}?>
																							</div>
																						<?}?>
																					</div>
																				<?}
																			}?>
																			
																		</div>

																		<?//댓글내용?>
																		<div class="tdw_list_memo_area"><!--작업-->
																			<?/*<div class="tdw_list_memo_area_in<?=$memo_view_in?>" id="memo_area_list_<?=$week_works_idx?>">*/?>
																			<div class="tdw_list_memo_area_in<?=$memo_view_in?>" id="tdw_list_memo_area_in_<?=$week_works_idx?>">
																				<?//댓글리스트
																				//요청업무
																				if($work_flag == '3'){?>
																					<?if($comment_list[$work_com_idx]){?>
																						<?for($k=0; $k<count($comment_list[$work_com_idx]['cidx']); $k++){
																							$comment_idx = $comment_list[$work_com_idx]['cidx'][$k];

																							$chis = $comment_list[$work_com_idx]['regdate'][$k];
																							$ymd = $comment_list[$work_com_idx]['ymd'][$k];
																							$cmt_flag = $comment_list[$work_com_idx]['cmt_flag'][$k];
																							if($chis){
																								$chis = str_replace("  "," ", $chis);
																								$chis_tmp = @explode(" ", $chis);
																								if ($chis_tmp['2'] == "PM"){
																									$after = "오후 ";
																								}else{
																									$after = "오전 ";
																								}
																								$ctime = @explode(":", $chis_tmp['1']);
																								$chiss = $ymd . " " . $after . $ctime['0'] .":". $ctime['1'];
																							}

																							$sql = "select a.name as name,a.comment as comment,a.send_name as send from work_todaywork_like a join work_todaywork_comment b";
																							$sql = $sql." on a.com_idx = b.idx where a.state != 9 and a.com_idx = '".$comment_idx."'";
																							$work_give_list = selectQuery($sql);

																							if($work_give_list){
																								$work_give_like_name = $work_give_list['name'];
																								$work_give_like_comment = $work_give_list['comment'];
																								$work_send_like_name = $work_give_list['send'];
																							}

																							// 코인보상 레이어 업무 요청한 사람만 보이게(김정훈)
																							$sql = "select idx from work_todaywork where idx = '".$week_works_idx."' and work_idx is null";
																							$work_link_coin = selectQuery($sql);

																							$sql = "select a.idx from work_todaywork_like a join work_todaywork_comment b on a.work_idx = b.link_idx";
																							$sql = $sql." where b.idx = '".$comment_idx."' and a.ai_like_idx = b.ai_like_idx and send_email = '".$user_id."'";
																							$click_like = selectQuery($sql);

																							$sql = "select a.idx from work_todaywork_like a join work_todaywork_comment b on a.work_idx = b.idx where b.idx = '".$comment_idx."'";
																							$sql = $sql." and a.state = 0 and a.send_email = '".$user_id."'";
																							$cli_like = selectQuery($sql);

																							$sql = "select idx from work_todaywork_comment where idx = '".$comment_idx."' and email = '".$user_id."'";
																							$my_like = selectQuery($sql);

																							$sql = "select idx from work_todaywork_comment where idx = '".$comment_idx."' and like_email = '".$user_id."'";
																							$my_coin_like = selectQuery($sql);


																						?>

																						<div class="tdw_list_memo_desc" id="comment_list_<?=$comment_idx?>" >
																							<?if($cmt_flag == 1){?>
																								<!-- 좋아요 변경으로 인한 코드 -->
																								<?if($work_give_list){?>
																									<div class="tdw_list_memo_name"><?=$work_send_like_name?></div>
																								<?}else{?>
																									<div class="tdw_list_memo_name ai">AI</div>
																								<?}?>
																							<?}else{?>
																								<?if($cmt_flag != 2){?>
																									<div class="tdw_list_memo_name"><?=$comment_list[$work_com_idx]['name'][$k]?></div>
																								<?}?>
																							<?}?>

																							<!-- 좋아요 변경으로 인한 코드(김정훈) -->
																							<?if($cmt_flag == 1){?>
																								<?//좋아요 보낸 내역이 있을때
																								if($work_give_list){?>
																									<button class="btn_memo_jjim on" value="<?=$comment_idx?>"><span>좋아요</span></button>
																								<?}?>
																							<?}?>

																							<div class="tdw_list_memo_conts">

																								<?if(!$cmt_flag && $user_id==$comment_list[$work_com_idx]['email'][$k]){?>
																									<!-- 일반 메모 -->
																									<span class="tdw_list_memo_conts_txt" id="tdw_list_memo_conts_txt_<?=$comment_idx?>"><?=textarea_replace($comment_list[$work_com_idx]['comment'][$k])?></span>
																								<?}else if($cmt_flag == 1 && $work_give_list){?>
																									<!-- 좋아요 받았을 때 문장 -->
																									<span class="tdw_list_memo_conts_txt"><?=textarea_replace($comment_list[$work_com_idx]['comment'][$k])?></span>
																								<?}else{?>
																									<?if($cmt_flag != 2){?>
																										<!-- AI 문장 -->
																										<span class="tdw_list_memo_conts_txt"><?=textarea_replace($comment_list[$work_com_idx]['comment'][$k])?></span>
																									<?}?>
																								<?}?>
																									<?if($cmt_flag != 2){?>
																										<em class="tdw_list_memo_conts_date"><?=$chiss?>
																									<?}?>

																								<?//자동 ai댓글?>
																								<?if($cmt_flag == 1){?>
																									<?if($work_link_coin && !$my_coin_like){?>
																										<?if($click_like){?>
																											<button class="btn_memo_jjim on" value="<?=$comment_idx?>"><span>좋아요</span></button>
																										<?}else{?>
																											<button class="btn_memo_jjim" id="btn_memo_jjim_<?=$comment_idx?>" value="<?=$comment_idx?>"><span>좋아요</span></button>
																										<?}?>
																									<?}?>
																								<?}else{?>
																									<?if($cmt_flag != 2){?>
																										<?if(!$my_like){?>
																											<?if($cli_like){?>
																												<button class="btn_memo_jjim on" value="<?=$comment_idx?>"><span>좋아요</span></button>
																												
																											<?}else{?>
																												<button class="btn_memo_jjim" id="btn_memo_jjim_<?=$comment_idx?>" value="<?=$comment_idx?>"><span>좋아요</span></button>
																												
																											<?}?>
																											<input type="hidden" value="<?=$comment_list[$work_com_idx]['email'][$k]?>" id="comment_idx_<?=$comment_idx?>">
																										<?}?>
																									<?}?>
																								<?}?>

																								<?if(!$cmt_flag && $user_id==$comment_list[$work_com_idx]['email'][$k]){?>
																									<button class="btn_memo_del" id="btn_memo_del" value="<?=$comment_idx?>"><span>삭제</span></button>
																								<?}?>

																								<?if($cmt_flag != 2){?>
																									</em>
																								<?}?>
																							</div>
																						</div>
																					<?}?>
																				<?}?>

																				<?}else{?>
																					<?//받은업무
																					if ($work_idx){?>

																						<?if($comment_list[$work_idx]){?>
																							<?for($k=0; $k<count($comment_list[$work_idx]['cidx']); $k++){
																								$comment_idx = $comment_list[$work_idx]['cidx'][$k];
																								$chis = $comment_list[$work_idx]['regdate'][$k];
																								$ymd = $comment_list[$work_idx]['ymd'][$k];
																								$cmt_flag = $comment_list[$work_idx]['cmt_flag'][$k];

																								if($chis){
																									$chis = str_replace("  "," ", $chis);
																									$chis_tmp = @explode(" ", $chis);
																									if ($chis_tmp['2'] == "PM"){
																										$after = "오후 ";
																									}else{
																										$after = "오전 ";
																									}
																									$ctime = @explode(":", $chis_tmp['1']);
																									$chiss = $ymd . " " . $after . $ctime['0'] .":". $ctime['1'];
																								}

																								$sql = "select a.name as name,a.comment as comment,a.send_name as send from work_todaywork_like a join work_todaywork_comment b";
																								$sql = $sql." on a.com_idx = b.idx where a.state != 9 and a.com_idx = '".$comment_idx."'";
																								$work_give_list = selectQuery($sql);

																								if($work_give_list){
																									$work_give_like_name = $work_give_list['name'];
																									$work_give_like_comment = $work_give_list['comment'];
																									$work_send_like_name = $work_give_list['send'];
																								}

																								$sql = "select a.idx from work_todaywork_like a join work_todaywork_comment b on a.work_idx = b.idx where b.idx = '".$comment_idx."'";
																								$sql = $sql." and a.state = 0 and a.send_email = '".$user_id."'";
																								$cli_like = selectQuery($sql);
																								?>
	
																								<div class="tdw_list_memo_desc" id="comment_list_<?=$comment_idx?>" >

																									<?if($cmt_flag == 1){?>
																										<!-- 좋아요 변경으로 인한 코드 -->
																										<?if($work_give_list){?>
																											<div class="tdw_list_memo_name"><?=$work_send_like_name?></div>
																										<?}else{?>
																											<div class="tdw_list_memo_name ai">AI</div>
																										<?}?>
																									<?}else{?>
																										<?if($cmt_flag !=2){?>
																											<div class="tdw_list_memo_name"><?=$comment_list[$work_idx]['name'][$k]?></div>
																										<?}?>
																									<?}?>

																									<!-- 좋아요 변경으로 인한 코드(김정훈) -->
																									<?if($cmt_flag == 1){?>
																										<?//좋아요 보낸 내역이 있을때
																										if($work_give_list){?>
																											<button class="btn_memo_jjim on" value="<?=$comment_idx?>"><span>좋아요</span></button>
																										<?}?>
																									<?}?>

																									<div class="tdw_list_memo_conts">

																										<?if(!$cmt_flag && $user_id==$comment_list[$work_idx]['email'][$k]){?>
																											<!-- 일반 메모 -->
																											<span class="tdw_list_memo_conts_txt" id="tdw_list_memo_conts_txt_<?=$comment_idx?>"><?=textarea_replace($comment_list[$work_idx]['comment'][$k])?></span>
																										<?}else if($cmt_flag == 1 && $work_give_list){?>
																											<!-- 좋아요 받았을 때 문장 -->
																											<span class="tdw_list_memo_conts_txt"><?=textarea_replace($comment_list[$work_idx]['comment'][$k])?></span>
																										<?}else{?>
																											<?if($cmt_flag != 2){?>
																												<!-- AI 문장 -->
																												<span class="tdw_list_memo_conts_txt"><?=textarea_replace($comment_list[$work_idx]['comment'][$k])?></span>
																											<?}?>
																										<?}?>

																										<?if($cmt_flag != 2){?>
																											<em class="tdw_list_memo_conts_date"><?=$chiss?>
																										<?}?>

																										<?//자동 ai댓글?>
																										<?if($cmt_flag == 1){?>

																										<?}else{?>
																											<?if($cmt_flag != 2){?>
																												<?if($user_id!=$comment_list[$work_idx]['email'][$k]){?>
																													<?if($cli_like){?>
																														<button class="btn_memo_jjim on" value="<?=$comment_idx?>"><span>좋아요</span></button>
																														
																													<?}else{?>
																														<button class="btn_memo_jjim" id="btn_memo_jjim_<?=$comment_idx?>" value="<?=$comment_idx?>"><span>좋아요</span></button>
																													<?}?>
																													<input type="hidden" value="<?=$comment_list[$work_idx]['email'][$k]?>" id="comment_idx_<?=$comment_idx?>">
																												<?}?>
																											<?}?>
																										<?}?>

																										<?if(!$cmt_flag && $user_id==$comment_list[$work_idx]['email'][$k]){?>
																											<button class="btn_memo_del" id="btn_memo_del" value="<?=$comment_idx?>"><span>삭제</span></button>
																										<?}?>
																										</em>
																									</div>
																								</div>

																							<?}?>

																						<?}?>

																					<?}else{?>
																						
																						<?
																						//일반업무
																						if($comment_list[$week_works_idx]){?>
																							<?for($k=0; $k<count($comment_list[$week_works_idx]['cidx']); $k++){
																								$comment_idx = $comment_list[$week_works_idx]['cidx'][$k];

																								$chis = $comment_list[$week_works_idx]['regdate'][$k];
																								$ymd = $comment_list[$week_works_idx]['ymd'][$k];
																								$cmt_flag = $comment_list[$week_works_idx]['cmt_flag'][$k];

																								if($chis){
																									$chis = str_replace("  "," ", $chis);
																									$chis_tmp = @explode(" ", $chis);
																									if ($chis_tmp['2'] == "PM"){
																										$after = "오후 ";
																									}else{
																										$after = "오전 ";
																									}
																									$ctime = @explode(":", $chis_tmp['1']);
																									$chiss = $ymd . " " . $after . $ctime['0'] .":". $ctime['1'];
																								}

																								$sql = "select a.name as name,a.comment as comment,a.send_name as send from work_todaywork_like a join work_todaywork_comment b";
																								$sql = $sql." on a.com_idx = b.idx where a.state != 9 and a.com_idx = '".$comment_idx."'";
																								$work_give_list = selectQuery($sql);

																								if($work_give_list){
																									$work_give_like_name = $work_give_list['name'];
																									$work_give_like_comment = $work_give_list['comment'];
																									$work_send_like_name = $work_give_list['send'];
																								}

																								$sql = "select a.idx from work_todaywork_like a join work_todaywork_comment b on a.work_idx = b.idx where b.idx = '".$comment_idx."'";
																								$sql = $sql." and a.state = 0 and a.send_email = '".$user_id."'";
																								$cli_like = selectQuery($sql);

																							?>
																							<div class="tdw_list_memo_desc" id="comment_list_<?=$comment_idx?>" >
																								<?if($cmt_flag == 1){?>
																									<!-- 좋아요 변경으로 인한 코드 -->
																									<?if($work_give_list){?>
																										<div class="tdw_list_memo_name"><?=$work_send_like_name?></div>
																									<?}else{?>
																										<div class="tdw_list_memo_name ai">AI</div>
																									<?}?>
																								<?}else{?>
																									<?if($cmt_flag != 2){?>
																										<div class="tdw_list_memo_name"><?=$comment_list[$week_works_idx]['name'][$k]?></div>
																									<?}?>
																								<?}?>

																								<!-- 좋아요 변경으로 인한 코드(김정훈) -->
																								<?if($cmt_flag == 1){?>
																									<?//좋아요 보낸 내역이 있을때
																									if($work_give_list){?>
																										<button class="btn_memo_jjim on" value="<?=$comment_idx?>"><span>좋아요</span></button>
																									<?}?>
																								<?}?>


																								<div class="tdw_list_memo_conts">
																									<?if(!$cmt_flag && $user_id==$comment_list[$week_works_idx]['email'][$k]){?>
																										<!-- 일반 메모 -->
																										<span class="tdw_list_memo_conts_txt" id="tdw_list_memo_conts_txt_<?=$comment_idx?>"><?=textarea_replace($comment_list[$week_works_idx]['comment'][$k])?></span>
																									<?}else if($cmt_flag == 1 && $work_give_list){?>
																										<!-- 좋아요 받았을 때 문장 -->
																										<span class="tdw_list_memo_conts_txt"><?=textarea_replace($comment_list[$week_works_idx]['comment'][$k])?></span>
																									<?}else{?>
																										<?if($cmt_flag != 2){?>
																											<!-- AI 문장 -->
																											<span class="tdw_list_memo_conts_txt"><?=textarea_replace($comment_list[$week_works_idx]['comment'][$k])?></span>
																										<?}?>
																									<?}?>

																									<?if($cmt_flag != 2){?>
																										<em class="tdw_list_memo_conts_date"><?=$chiss?>
																									<?}?>

																										<?//자동 ai댓글?>
																										<?if($cmt_flag == 1){?>

																										<?}else{?>
																											<?if($cmt_flag != 2){?>
																												<?if($user_id!=$comment_list[$week_works_idx]['email'][$k]){?>
																													<?if($cli_like){?>
																														<button class="btn_memo_jjim on" value="<?=$comment_idx?>"><span>좋아요</span></button>
																													<?}else{?>
																														<button class="btn_memo_jjim" id="btn_memo_jjim_<?=$comment_idx?>" value="<?=$comment_idx?>"><span>좋아요</span></button>
																													<?}?>
																													<input type="hidden" value="<?=$comment_list[$work_com_idx]['email'][$k]?>" id="comment_idx_<?=$comment_idx?>">
																												<?}?>
																											<?}?>
																										<?}?>

																									<?if(!$cmt_flag && $user_id==$comment_list[$week_works_idx]['email'][$k]){?>
																										<button class="btn_memo_del" id="btn_memo_del" value="<?=$comment_idx?>"><span>삭제</span></button>
																									<?}?>

																									<?if($cmt_flag != 2){?>
																										</em>
																									<?}?>

																								</div>
																							</div>
																							<?}?>
																						<?}?>
																					<?}?>
																				<?}?>
																			</div>

																			<div class="tdw_list_memo_onoff" <?=$memo_view_bt_style?>>
																				<button class="btn_list_memo_onoff<?=$memo_view_bt?>" id="btn_list_memo_onoff_<?=$week_works_idx?>" value="<?=$week_works_idx?>" <?if(trim($memo_view_bt)=="on"){ echo "title='메모 접기'"; }else{ echo "title='메모 펼치기'"; }?>><span>메모 접기/펼치기</span></button>
																			</div>

																		</div>

																		</li>
																	<?}?>
																<?}?>
															</ul>
														</div>
													<?}?>

												<?}else{?>
													<div class="tdw_list_search_none">
														<strong><span>파티에 연결된 업무가 없습니다.</span></strong>
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

	<?php
		//로딩 페이지
		include $home_dir . "loading.php";

		//튜토리얼 시작 레이어
		include $home_dir . "/layer/tutorial_start.php";

		//튜토리얼 시작 레이어
		include $home_dir . "/layer/tutorial_main_level.php";
	?>
	<?php
		//사용자 레이어
		include $home_dir . "/layer/member_user_layer.php";
	?>
	<?php
	//비밀번호 재설정
		include $home_dir . "/layer/member_repass.php";
	?>
	<?php
		//파티 메모 레이어
		include $home_dir . "/layer/todaywork_memo.php";
	?>
	<?
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
		//좋아요 레이어
		include $home_dir . "/layer/member_jjim.php";
	?>

	<?php
		//코인보상하기 레이어
		include $home_dir . "/layer/member_reward.php";
	?>
	
	<?php
		// 파티코인 나누기 레이어
		include $home_dir . "/layer/party_coin_reward.php";
	?>
	
	
</div>
	
<!-- footer start-->
<? include $home_dir . "/inc_lude/footer.php";?>
<!-- footer end-->
<script type="text/javascript" src ="/party/js/project.js"></script>
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

