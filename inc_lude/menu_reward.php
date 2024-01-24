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
				//리워드 메뉴?>
		<div class="rew_mypage_08">
			<div class="rew_mypage_08_in">
			<div class="rew_mypage_close"><button><img src="/html/images/pre_m/rew_mypage_close.png"></button></div>
				<div class="rew_mypage_title">
					<strong>보상/코인</strong>
				</div>



				<div class="rew_mypage_section">
					<div class="rew_mypage_coin_box">
						<div class="title_area">
							<div class="qna">
								<strong class="title_main">내가 획득한 코인</strong>
								<span class="qna_q">?</span>
								<div class="qna_a">
									<span>챌린지 성공, 동료의 보상으로 획득한<br />코인으로, 현금으로 출금 가능한 코인<br />(출금 시 수수료율 5% 적용)</span>
								</div>
							</div>
						</div>

						<div class="rew_mypage_coin_chall">
							<strong><span><?=$reward_user_info['coin']?></span></strong>
						</div>


						<div class="rew_mypage_btn" id="rew_withdraw_btn">
							<button><span>출금 신청</span></button>
						</div>
					</div>
				</div>

				<div class="rew_mypage_section">
					<div class="rew_mypage_coin_box">
						<div class="title_area">
							<div class="qna">
								<strong class="title_main">사용 가능한 공용 코인</strong>
								<span class="qna_q">?</span>
								<div class="qna_a">
									<span>챌린지 만들기, 보상하기 등을 위해 회사에서<br />각 개인들에게 지급한 코인으로, 출금이 불가능한 코인<br />(공용 코인은 회사에서 회수할 수
										있습니다.)</span>
								</div>
							</div>
						</div>
						<div class="rew_mypage_coin_chall">
							<strong><span><?=number_format($member_info['comcoin'][$user_id])?></span></strong>
						</div>
						<div class="rew_mypage_btns">
							<button id="btn_coin_reward"><span>보상하기</span></button>
							<button id="btn_challenges_create"><span>챌린지 만들기</span></button>
						</div>
					</div>
				</div>

				<div class="rew_mypage_section">
					<div class="rew_mypage_section_title">
						<strong><span>🔍</span> 코인 이용 안내</strong>
					</div>
					<div class="rew_mypage_coin_use">
						<ul>
							<li>
								<strong>내가 획득한 코인</strong>
								<p>챌린지 성공, 동료의 보상으로 획득한 코인으로 현금으로 출금 가능한 코인 (출금 시 수수료율 5% 적용)</p>
							</li>
							<li>
								<strong>공용 코인</strong>
								<p>챌린지 만들기, 보상하기 등을 위해 회사에서 각 개인들에게 지급한 코인으로 출금이 불가능한 코인 (공용 코인은 회사에서 회수할 수 있습니다.)</p>
							</li>
						</ul>
					</div>
				</div>
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
	<button><span>메뉴 보기</span></button>
</div>