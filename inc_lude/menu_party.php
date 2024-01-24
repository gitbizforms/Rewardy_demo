<?

//회원 전체 정보가져오기
$member_info = member_list_all();
$member_total_cnt = $member_info['total_cnt'];

$member_list_info = member_main_team_list();

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

		//파티, 프로젝트
		?>
		<div class="rew_mypage_08">
			<div class="rew_mypage_08_in">
			<div class="rew_mypage_close"><button><img src="/html/images/pre_m/rew_mypage_close.png"></button></div>
				<div class="rew_mypage_title">
					<strong>파티</strong>
				</div>
				<div class="rew_party_section">
					<div class="rew_party_tab">
						<div class="rew_party_tab_in">
							<ul>
								<li id="party_tab_my">
									<button><span>내 파티</span><strong><?=count($project_myinfo['idx'])?></strong></button>
								</li>
								<li id="party_tab_1">
									<button><span>원활</span><strong><?=$project_ing_cnt?></strong></button>
								</li>
								<li id="party_tab_3">
									<button><span>보통</span><strong><?=$project_normal_cnt?></strong></button>
								</li>
								<li id="party_tab_7">
									<button><span>지연</span><strong><?=$project_delay_cnt?></strong></button>
								</li>
							</ul>
						</div>
					</div>

					<div class="rew_party_un">
						<div class="rpu_in">
							<div class="rpu_func">
								<div class="rpu_func_tit">
									<span>미확인 업무<strong><?=$pj_read_delay_sum?></strong></span>
								</div>
							</div>

							<div class="rpu_list">

								<?//미확인 업무
								if($project_read_info['idx']){

									for($p_i=0; $p_i<count($project_read_info['idx']); $p_i++){
										if($project_read_info['cnt'][$p_i] == '1'){
											$pj_read_delay = '1';
										}else if($project_read_info['cnt'][$p_i] > '1' && $project_read_info['cnt'][$p_i] <= '3'){
											$pj_read_delay = '3';
										}else{
											$pj_read_delay = '7';
										}

										$link_page = $project_read_info['party_idx'][$p_i];
									?>
								<div class="rpu_box delay_<?=$pj_read_delay?>"
									onclick="link(<?=$project_read_info['project_idx'][$p_i]?>)">
									<div class="rpu_box_in">
										<div class="rpu_box_tit">
											<span><?=$project_read_info['p_title'][$p_i]?></span>
										</div>
										<div class="rpu_box_date">
											<span>마지막 업데이트 : <?=$project_read_info['p_reg'][$p_i]?></span>
										</div>
										<div class="rpu_box_num">
											<span>미확인 <?=$project_read_info['cnt'][$p_i]?>건</span>
										</div>
									</div>
								</div>
								<?}?>
								<?}else{?>
								<div class="rpu_none">
									<span>미확인한 업무가 없습니다.</span>
								</div>
								<?}?>
							</div>
						</div>
					</div>
				</div>

			</div>

			<div class="rew_mypage_party_make">
				<button class="btn_mypage_party_make" id="btn_mypage_party_make"><span>파티 만들기</span></button>
			</div>
		</div>

	</div>

	<?if ($get_dirname!="team" && $get_dirname!="admin"){?>
		<div class="rew_menu_onoff" id="rew_menu_onoff">
			<button class="<?=$menu_class?>">열고 닫기</button>
		</div>
	<?}?>
	
</div>

<div class="tdw_open_btn">
		<button><span>파티 만들기</span></button>
	</div>