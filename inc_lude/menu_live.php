<?

//회원 전체 정보가져오기
$member_info = member_list_all();
$member_total_cnt = $member_info['total_cnt'];

//부서별 정렬순
$part_info = member_part_info();

$nowurl = now_url();

?>

<div class="rew_menu">
	<div class="rew_menu_in">
		<div class="rew_bar">
			<?if($user_level == '0'){?>

			<?if(!$member_mail_add_info['idx']){?>
			<?}?>
			<?}?>
			<div class="rew_bar_in">
				<div class="rew_bar_logo">
					<a href="javascript:void(0);"><img src="/images/pre/logo.png" alt="" /></a>
				</div>
				<ul>
					<li class="rew_bar_li_01<?=($get_dirname=="todaywork")?" on":"";?>" title="">
						<a href="javascript:void(0);"><strong>오늘업무</strong></a>
					</li>
					<li class="rew_bar_li_02<?=($get_dirname=="live")?" on":"";?>" title="">
						<a href="javascript:void(0);"><strong>실시간 업무</strong></a>
					</li>
					<li class="rew_bar_li_03<?=($get_dirname=="reward")?" on":"";?>" title="">
						<a href="javascript:void(0);"><strong>보상/코인</strong></a>
					</li>
					<li class="rew_bar_li_04<?=($get_dirname=="challenge")?" on":"";?>" title="">
						<a href="javascript:void(0);"><strong>챌린지</strong></a>
					</li>
					<li class="rew_bar_li_05<?=($get_dirname=="party")?" on":"";?>" title="">
						<a href="javascript:void(0);"><strong>파티</strong></a>
					</li>
					<li class="rew_bar_li_06<?=($get_dirname=="insight")?" on":"";?>" title="">
						<a href="javascript:void(0);"><strong>인사이트</strong></a>
					</li>
				</ul>
				<div class="rew_bar_setting">

					<a href="/itemshop/index.php"
						class="rew_bar_setting_04<?=($get_dirname=="itemshop")?" on":"";?>"><strong>아이템샵</strong></a>
					<?if($user_level == '0'){?>
					<a href="/admin/member_list.php" class="rew_bar_setting_02" id="member_add_in"
						title=""><strong>관리자</strong></a>
					<?}?>

				</div>
			</div>
		</div>


		<?	//좌측메뉴 열기
		if($_COOKIE['onoff'] == '1'){
					$menu_class = "";
				}else{
					$menu_class = "on";
				}
				//라이브 리뉴얼
	if ($get_dirname=="live"){?>
		<div class="rew_mypage_08">
			<div class="rew_mypage_08_in">
			<div class="rew_mypage_close"><button><img src="/html/images/pre_m/rew_mypage_close.png"></button></div>
				<div class="rew_mypage_title">
					<strong><span>📄</span> 실시간 업무</strong>
				</div>
				<div class="rew_now_section">
					<div class="rew_now_timer on">
						<div class="rew_now_timer_in">
							<span>출근 후 업무시간</span>
							<strong>15<em>:</em>35<em>:</em>27</strong>
						</div>
					</div>
					<div class="rew_now_tab">
						<div class="rew_now_tab_in">
							<ul>
								<li>
									<button><span>업무</span><strong><?=$works_realtime_all?></strong></button>
								</li>
								<li>
									<button><span>보고</span><strong><?=$works_realtime_report?></strong></button>
								</li>
								<li>
									<button><span>요청</span><strong><?=$works_realtime_req?></strong></button>
								</li>
								<li>
									<button><span>공유</span><strong><?=$works_realtime_share?></strong></button>
								</li>
							</ul>
						</div>
					</div>
					<div class="rew_now_comp">
						<div class="rnc_in">
							<div class="rnc_func">
								<div class="rnc_func_tit">
									<span>진행중인 업무<strong><?=count($works_realtime_info['idx'])?></strong></span>
								</div>
							</div>
							<div class="rnc_list">
								<?if($works_realtime_info['idx']){?>
								<ul>
									<?for($i=0; $i<count($works_realtime_info['idx']); $i++){
										$works_realtime_idx = $works_realtime_info['idx'][$i];
										$works_realtime_email = $works_realtime_info['email'][$i];
										$works_realtime_name = $works_realtime_info['name'][$i];
										
										$works_realtime_work_flag = $works_realtime_info['work_flag'][$i];
										$works_realtime_work_idx = $works_realtime_info['work_idx'][$i];
										$works_realtime_share_flag = $works_realtime_info['share_flag'][$i];
										$works_realtime_secret_flag = $works_realtime_info['secret_flag'][$i];
										$works_realtime_decide_flag = $works_realtime_info['decide_flag'][$i];
										$works_realtime_work_stime = $works_realtime_info['work_stime'][$i];
										$works_realtime_work_etime = $works_realtime_info['work_etime'][$i];

										$works_realtime_contents = $works_realtime_info['contents'][$i];
										$works_realtime_editdate = $works_realtime_info['editdate'][$i];
										$works_realtime_regdate = $works_realtime_info['regdate'][$i];



										if($works_realtime_decide_flag == '1'){$decide_name = "연차";}else if($works_realtime_decide_flag == '2'){ $decide_name = "반차";}else if($works_realtime_decide_flag == '3'){$decide_name = "외출";}else if($works_realtime_decide_flag == '4'){$decide_name = "조퇴";}
										else if($works_realtime_decide_flag == '5'){$decide_name = "출장";}else if($works_realtime_decide_flag == '6'){$decide_name = "교육";}
										else if($works_realtime_decide_flag == '7'){$decide_name = "미팅";}else if($works_realtime_decide_flag == '8'){$decide_name = "회의";}

										$tmp_ex = @explode(" ", $works_realtime_editdate);
										$complete_time = @substr($tmp_ex[1], 0, 5);

										$tmp_ex2 = @explode(" ", $works_realtime_regdate);
										$complete_time2 = @substr($tmp_ex2[1], 0, 5);

										if($works_realtime_share_flag == '1'){
											$works_realtime_title = "[공유함] ";
										}else if($works_realtime_share_flag == '2'){
											$works_realtime_title = "[공유받음] ";
										}else if($works_realtime_work_flag == '3' && $works_realtime_work_idx == ''){
											$works_realtime_title = "[요청함] ";
										}else if($works_realtime_work_flag == '3' && $works_realtime_work_idx != ''){
											$works_realtime_title = "[요청받음] ";
										}else if($works_realtime_decide_flag == 1){
											$works_realtime_title = "[ " .$decide_name. " ] ";
										}else if($works_realtime_decide_flag > 1){
											$works_realtime_title = "[ ".$decide_name."   ".$works_realtime_work_stime."~".$works_realtime_work_etime." ] ";
										}else{
											$works_realtime_title = " ";
										}
									?>
										<?php if($works_realtime_secret_flag == '1' && $user_id == $works_realtime_email){?>
											<li id="rnc_list_<?=$i?>" class = "<?=$works_realtime_secret_flag == '1'? "lock":""?>" value="<?=$works_realtime_email?>">
												<a href="javascript:void(0);" class="rnc_box">
													<div class="rnc_name"><?=$works_realtime_name?></div>
													<div class="rnc_desc">
															<span><?=$works_realtime_title?></span><?=htmlspecialchars($works_realtime_contents)?></div>
													<div class="rnc_time"><?=$complete_time2?></div>
												</a>
											</li>
										<? }else if($works_realtime_secret_flag != '1'){?>
											<li id="rnc_list_<?=$i?>" value="<?=$works_realtime_email?>">
												<a href="javascript:void(0);" class="rnc_box">
													<div class="rnc_name"><?=$works_realtime_name?></div>
													<div class="rnc_desc">
															<span><?=$works_realtime_title?></span><?=$decide_flag.htmlspecialchars($works_realtime_contents)?></div>
													<div class="rnc_time"><?=$complete_time2?></div>
												</a>
											</li>
										<?}?>
									<?}?>
								</ul>
								<?}else{?>
								<div class="rnc_none">
									<span>완료한 업무가 없습니다.</span>
								</div>
								<?}?>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<?}?>
	</div>

	<?if ($get_dirname!="team" && $get_dirname!="admin"){?>
		<div class="rew_menu_onoff" id="rew_menu_onoff">
			<button class="<?=$menu_class?>">열고 닫기</button>
		</div>
	<?}?>
</div>
<div class="tdw_open_btn">
	<button><span>메뉴 보기</span></button>
</div>