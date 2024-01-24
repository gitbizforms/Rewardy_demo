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
				
		//인사이트
		?>
		<div class="rew_mypage_08">
			<div class="rew_mypage_08_in">
			<div class="rew_mypage_close"><button><img src="/html/images/pre_m/rew_mypage_close.png"></button></div>
				<div class="rew_mypage_title">
					<strong>인사이트</strong>
				</div>

				<div class="rew_mypage_ins">
					<div class="rew_mypage_ins_in">
						<ul>
							<li>
								<a href="./rank_c.php"><span>🏆</span> 코인킹</a>
							</li>
							<li>
								<a href="./rank_l.php"><span>💖</span> 좋아요</a>
							</li>
							<li>
								<a href="./rank_p.php"><span>💪🏻</span> 역량</a>
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