<?php

$requestUri = $_SERVER[ "REQUEST_URI" ];

$parts = explode('/', trim($requestUri, '/'));
$member_row_info = member_row_info($user_id);
$sql = "select idx, state, service, service_type, title, contents, sub_contents, email, send_yn, workdate, date_format( regdate , '%m/%d/%y %l:%i:%s %p') as reg, work_idx
from work_alarm
where state = '0'
and email = '".$member_row_info['email']."'
and workdate = '".TODATE."'
and (send_yn = 'Y' or send_yn is null)
order by idx desc";
$alarm_info = selectAllQuery($sql);


$sql = "select email, count(1) as cnt 
from work_alarm
where 1=1
and state = '0'
and work_flag = '0'
and email = '".$member_row_info['email']."'
and (send_yn = 'Y' or send_yn is null)
and workdate = '".TODATE."'";
$alarm_count = selectQuery($sql);

// header 총 역량 값 
$sql = "select
			email,
			sum(type1 + type2 + type3 + type4 + type5 + type6) as reward
			from work_cp_reward_list use index(state)
			where 1=1
			and workdate BETWEEN '".$month_first_day."' AND '".$month_last_day."'
			and companyno = '".$companyno."'
			and email = '".$member_row_info['email']."'
			group by email";
			
$reward_info = selectQuery($sql);


// header 총 좋아요 수 
$sql = "select email, count(1) as cnt 
		from work_todaywork_like use index(state)
		where 1=1 
		and state='0' 
		and companyno='".$companyno."' 
		and email = '".$member_row_info['email']."'
		and workdate between '".$month_first_day."' and '".$month_last_day."' 
		group by email"; 
$like_count = selectQuery($sql);

// 쪽지 카운트 수
$sql = "select email, count(1) as cnt from work_member_message where email = '".$user_id."' and state = '0'";
$message_count = selectQuery($sql);

// 쪽지 리스트(받은쪽지)
$sql = "select a.idx, a.email,a.name,a.send_email,a.send_name, b.part, b.profile_type, b.profile_img_idx, c.file_path, c.file_name,
			a.contents,a.workdate,a.regdate 
			from work_member_message a 
			left join work_member b on a.send_email = b.email
			left join work_member_profile_img c on a.send_email = c.email
			where 1=1 and a.email = '".$user_id."' and a.companyno = '".$companyno."' order by idx desc";
$re_message = selectAllQuery($sql);
// 쪽지 리스트(보낸쪽지)
$sql = "select a.idx, a.email,a.name,a.send_email,a.send_name, b.part, b.profile_type, b.profile_img_idx, c.file_path, c.file_name,
			a.contents,a.workdate,a.regdate 
			from work_member_message a 
			left join work_member b on a.email = b.email
			left join work_member_profile_img c on a.email = c.email
			where 1=1 and a.send_email = '".$user_id."' and a.companyno = '".$companyno."' order by idx desc";
$se_message = selectAllQuery($sql);
//회사 로고 이미지
$sql = "select state, companyno, file_ori_path, file_ori_name from work_company_logo_img where companyno = '".$companyno."' and state = '0' ";
$comp_img = selectQuery($sql);
if($comp_img){
	$logo = "http://rewardy.co.kr/".$comp_img['file_ori_path'].$comp_img['file_ori_name'];
}else{
	$logo = "http://rewardy.co.kr/html/images/pre/img_logo.png";
}

// 알림 설정
$sql = "select idx, email, todaywork_alarm, challenges_alarm, party_alarm, reward_alarm, like_alarm, memo_alarm, allselect_alarm from work_member_alarm where email = '".$user_id."' and state = '0'";
$alarm_setting = selectQuery($sql);
if(!$alarm_setting){
	$sql = "insert into work_member_alarm(email,companyno,state,todaywork_alarm,challenges_alarm, party_alarm,reward_alarm,like_alarm, memo_alarm,allselect_alarm,workdate)
	values('".$user_id."','".$companyno."','0','0','0','0','0','0','0','0','".TODATE."')";
   
	$insert_alarm = insertQuery($sql);
}
?>

<html>
	<head>
	<?php if($parts[0] != 'live'){?>
		<link rel="stylesheet" type="text/css" href="/html/css/live_pop_02.css<?php echo VER;?>" />
		<link rel="stylesheet" type="text/css" href="/html/css/live_pop_03.css<?php echo VER;?>" />
		<link rel="stylesheet" type="text/css" href="/html/css/live_pop_04.css<?php echo VER;?>" />
		<link rel="stylesheet" href="/html/css/billboard.css">
		<script src="/js/lives_common.js<?php echo VER;?>"></script>
		<script src="https://d3js.org/d3.v6.min.js"></script>
		<script src="https://cdnjs.cloudflare.com/ajax/libs/billboard.js/3.9.1/billboard.min.js"></script>
	<?}?>
	</head>
<body>
<div class="rew_head">
	<div class="rew_head_in">
		<div class="hamburger_btn">
			<span></span><span></span><span></span>
		</div>
		<div class="rew_head_logo">
			<a href="http://demo.rewardy.co.kr/team/">
              <span class="mobile_chage_img">
                <!-- <img src=<?=$companyno == '3'?"/html/images/pre/logo_bizforms_for_rewardy.jpg":"/html/images/pre/logo_bm.png"?> alt="베러먼데이" />  -->
				<img src="<?=$logo?>" alt="Rewardy_logo" /> 
              </span>
            </a>
		</div>
		<div class="rew_head_my">
		<div class="rew_head_my_mess">
				<div class = "rew_head_my_message">
					<button class="btn_my_mess"><span>쪽지</span>
						<?if($message_count['cnt'] > 0){?>
							<em>N</em>
						<?}?>
					</button>
			 	 </div>
			 	 <div class="layer_my_mess" style = "display:none;">
					<div class="layer_my_mess_in">
						<div class="my_mess_tit">
							<div class="mess_btn">
							<button class="on re_message"><span>받은쪽지</span></button>
							<button class="se_message"><span>보낸쪽지</span></button>
							</div>
							<span>※ 쪽지는 최근 30일간의 내용만 볼 수 있습니다.</span>
						</div>
						<div class="my_mess_list receive">
							<div class="my_mess_list_in">
								<div class="mess_list_head">
									<div class="list_day"><span>날짜</span></div>
									<div class="list_cont"><span>내용</span></div>
									<div class="list_user"><span>보낸사람</span></div>
								</div>
							<div class="mess_list_body">
								<ul>
									<?php for($i =0; $i < count($re_message['idx']); $i++) {
										$re_name =  $re_message['send_name'][$i];
										$re_content =  $re_message['contents'][$i];
										$re_date =  $re_message['regdate'][$i];
										$re_part =  $re_message['part'][$i];
										$re_email =  $re_message['send_email'][$i];
										
										$re_date =  $re_message['regdate'][$i];
										$re_profile = $re_message['file_path'][$i].$re_message['file_name'][$i];
										$re_profile_img =  'http://demo.rewardy.co.kr'.$re_message['file_path'][$i].$re_message['file_name'][$i];
										$dateChange = new DateTime($re_date);
										$ori_date = $dateChange->format('Y.m.d H:i');
										?>
									<li>
										<input type="hidden" value="<?php echo $re_email?>" class="user_email">
										<input type="hidden" value="<?php echo $re_part?>" class="user_part">
										<input type="hidden" value="<?php echo $re_profile?$re_profile_img:"/html/images/pre/img_prof_default.png"?>" class="user_img">
										<div class="list_day"><span><?php echo $ori_date;?></span></div>
										<div class="list_cont"><span><?php echo $re_content;?></span></div>
										<div class="list_user data_user" value = "<?php echo $re_name;?>"><button><span><?php echo $re_name;?></span></button></div>
									</li>
									<? }
									if(!$re_message['idx']){?>
										<div class="mess_list_none"><span>받은 쪽지가 없습니다.</span></div>
									<?}?>
								</ul>
								</div>
							</div>
                  		</div>
						  <div class="my_mess_list send" style = "display:none;">
							<div class="my_mess_list_in">
								<div class="mess_list_head">
									<div class="list_day"><span>날짜</span></div>
									<div class="list_cont"><span>내용</span></div>
									<div class="list_user"><span>받은사람</span></div>
								</div>
							<div class="mess_list_body">
								<ul>
									<?php for($i =0; $i < count($se_message['idx']); $i++) {
										$se_name =  $se_message['name'][$i];
										$se_content =  $se_message['contents'][$i];
										$se_date =  $se_message['regdate'][$i];
										$se_email =  $se_message['email'][$i];
										$se_profile = $se_message['file_path'][$i].$se_message['file_name'][$i];
										$se_profile_img =  'http://demo.rewardy.co.kr'.$se_message['file_path'][$i].$se_message['file_name'][$i];
										$dateChange = new DateTime($re_date);
										$ori_date = $dateChange->format('Y.m.d H:i');
										?>
									<li>
										<input type="hidden" value="<?php echo $se_email?>" class="user_email">
										<input type="hidden" value="<?php echo $se_part?>" class="user_part">
										<input type="hidden" value="<?php echo $se_profile?$se_profile_img:"/html/images/pre/img_prof_default.png"?>" class="user_img">
										<div class="list_day"><span><?php echo $ori_date;?></span></div>
										<div class="list_cont"><span><?php echo $se_content;?></span></div>
										<div class="list_user data_user" value = "<?php echo $se_name;?>"><button><span><?php echo $se_name;?></span></button></div>
									</li>
									<? }
									if(!$se_message['idx']){?>
										<div class="mess_list_none"><span>보낸 쪽지가 없습니다.</span></div>
									<?}?>
								</ul>
								</div>
							</div>
                  		</div>
               		</div>
              	</div>
            </div>

			<div class="rew_head_my_alert" id ="rew_head_my_alert">
				<div class= "rew_head_my_cnt" id ="rew_head_my_cnt">
					<button class="btn_my_alert"><span>알림</span>
						<?php if($alarm_count['cnt'] > 0){?>
							<em><?php echo $alarm_count['cnt'] ?></em>
						<?php }?>
					</button>
				</div>
				<div class="layer_my_alert" style="display:none;">
					<div class="layer_my_alert_in">
						<div class="my_alert_tit">
							<strong>알림</strong>
							<button><span>설정</span></button>
						</div>
						<div class="my_alert_set">
							<div class="alert_set_head">
								<p>전체알림 허용</p>
								<div class="member_list_conts_admin">
									<div class="btn_switch<?=$alarm_setting['allselect_alarm']=="1"?" on":""?>" value="allselect_alarm">
										<strong class="btn_switch_on"></strong>
										<span>버튼</span>
										<strong class="btn_switch_off"></strong>
									</div>
								</div>
							</div>
								<div class="alert_set_body">
								<ul>
									<li>
										<p>오늘업무 알림</p>
										<div class="member_list_conts_admin">
											<div class="btn_switch<?=$alarm_setting['todaywork_alarm']=="1"?" on":""?>" id="setting" value="todaywork_alarm">
												<strong class="btn_switch_on"></strong>
												<span>버튼</span>
												<strong class="btn_switch_off"></strong>
											</div>
										</div>
									</li>
									<li>
										<p>챌린지 알림</p>
										<div class="member_list_conts_admin">
											<div class="btn_switch<?=$alarm_setting['challenges_alarm']=="1"?" on":""?>" id="setting" value="challenges_alarm">
												<strong class="btn_switch_on"></strong>
												<span>버튼</span>
												<strong class="btn_switch_off"></strong>
											</div>
										</div>
									</li>
									<li>
										<p>파티 알림</p>
										<div class="member_list_conts_admin">
											<div class="btn_switch<?=$alarm_setting['party_alarm']=="1"?" on":""?>" id="setting" value="party_alarm">
												<strong class="btn_switch_on"></strong>
												<span>버튼</span>
												<strong class="btn_switch_off"></strong>
											</div>
										</div>
									</li>
									<li>
										<p>좋아요 알림</p>
										<div class="member_list_conts_admin">
											<div class="btn_switch<?=$alarm_setting['like_alarm']=="1"?" on":""?>" id="setting" value="like_alarm">
												<strong class="btn_switch_on"></strong>
												<span>버튼</span>
												<strong class="btn_switch_off"></strong>
											</div>
										</div>
									</li>
									<li>
										<p>코인 알림</p>
										<div class="member_list_conts_admin">
											<div class="btn_switch<?=$alarm_setting['reward_alarm']=="1"?" on":""?>" id="setting" value="reward_alarm">
												<strong class="btn_switch_on"></strong>
												<span>버튼</span>
												<strong class="btn_switch_off"></strong>
											</div>
										</div>
									</li>
									<li>
										<p>메모 알림</p>
										<div class="member_list_conts_admin">
											<div class="btn_switch<?=$alarm_setting['memo_alarm']=="1"?" on":""?>" id="setting" value="memo_alarm">
												<strong class="btn_switch_on"></strong>
												<span>버튼</span>
												<strong class="btn_switch_off"></strong>
											</div>
										</div>
									</li>
								</ul>
							</div>
						</div>
						<div class="my_alert_list" id ="my_alert_list">
							<?php if($alarm_info['idx']){?>
							<ul>
								<?php for($i=0; $i<count($alarm_info['idx']); $i++){
									$alarm_reg = $alarm_info['reg'][$i];
									$work_idx = $alarm_info['work_idx'][$i];
									if($alarm_reg){
									$his_tmp = @explode(" ", $alarm_reg);
									if ($his_tmp['2'] == "PM"){
										$after = "오후 ";
									}else{
										$after = "오전 ";
									}
									$ctime = @explode(":", $his_tmp['1']);
									$work_his = $alarm_info['workdate'][$i] . " " . $after . $ctime['0'] .":". $ctime['1'];
									}

									$img_src = "";
									$click_src = "";
									if($alarm_info['service'][$i]=="work"){
										if($alarm_info['service_type'][$i]=="share"){
											$img_src = "/html/images/pre_m/share_icon.png";
										}else if($alarm_info['service_type'][$i]=="report"){
											$img_src = "/html/images/pre_m/ico_get.png";
										}else if($alarm_info['service_type'][$i]=="req"){
											$img_src = "/html/images/pre_m/arrow_icon.png";
										}
										$click_src = "http://demo.rewardy.co.kr/todaywork/index.php";
									} else if($alarm_info['service'][$i] == "live") {
											$img_src = "/html/images/pre/ico_ht.png";
											$click_src = "http://demo.rewardy.co.kr/team/index.php";
									} else if($alarm_info['service'][$i] == "reward"){
											$img_src = "/html/images/pre/ico_coin_new.png";
											$click_src = "http://demo.rewardy.co.kr/reward/index.php";
									} else if($alarm_info['service'][$i] == "challenge"){
											$img_src = "/html/images/pre_m/ico_bell.png";
											$click_src = "http://demo.rewardy.co.kr/challenge/index.php";
									} else if($alarm_info['service'][$i] == "party"){
											$img_src = "/html/images/pre/ico_bell.png";
											$click_src = "http://demo.rewardy.co.kr/party/index.php";
									} else if($alarm_info['service'][$i] == "penalty"){
											$img_src = "/html/images/pre/ico_pe.png";
											$click_src = "http://demo.rewardy.co.kr/team/index.php";
									} else if($alarm_info['service'][$i] == "memo"){
											$img_src = "/html/images/pre/ico_alarm_memo.png";
											if($alarm_info['service_type'][$i] == "work"){
												$click_src = "http://demo.rewardy.co.kr/todaywork/index.php";
											} else if($alarm_info['service_type'][$i] == "chall"){
												$click_src = "http://demo.rewardy.co.kr/challenge/view.php?idx=<?=$work_idx?>";
											} else if($alarm_info['service_type'][$i] == "party"){
												$click_src = "http://demo.rewardy.co.kr/party/view.php?idx=<?=$work_idx?>";
											}
									}
								?>
									<li>
										<button class="my_alert_box">
											<div class="my_alert_box_tit">
												<img src="<?=$img_src?>" alt="">
												<span onclick="window.open('<?=$click_src?>')"><strong><?php echo $alarm_info['title'][$i]?></strong></span>
											</div>
											<div class="my_alert_box_desc <?=$alarm_info['service'][$i]=='penalty'?"rew_pena":""?>">
												<span onclick="window.open('<?=$click_src?>')"><?php echo $alarm_info['contents'][$i]?></span>
												<? if($alarm_info['service'][$i]=="memo"){?>
													<span onclick="window.open('<?=$click_src?>')"><em><?php echo $alarm_info['sub_contents'][$i]?></em></span>
												<?}?>
											
											</div>
											<div class="my_alert_box_info">
												<span><?php echo $work_his?></span>
												<!-- <span><strong>300</strong> 코인</span> -->
											</div>
										</button>
										<button class="my_alert_close" id = "my_alert_close" value= "<?php echo $alarm_info['idx'][$i]?>"><span>닫기</span></button>
									</li>
								<?php }?>
							</ul>
							<?php }else{?>
							<ul>
								<li>
									<button class="my_alert_box">
										<div class="my_alert_box_tit">
											<span><strong>알림이 없습니다.</strong></span>
										</div>
										<div class="my_alert_box_desc">
										</div>
										<div class="my_alert_box_info">
										</div>
									</button>
								</li>
							</ul>
							<?php }?>
						</div>
					</div>
				</div>
			</div>
			<div class="rew_head_my_info">
				<button class="btn_my_info">
					<span class="user_img" style="background-image:url('<?php echo $member_row_info['profile_use']?$member_row_info['profile_img_src']:"/html/images/pre/img_prof_default.png"?>');" id="profile_character_img"></span>
					<span class="user_name"><?=$member_row_info['name']."님"?></span>
				</button>
				<div class="layer_my_info" style="display:none;">
					<div class="layer_my_info_in">
						<div class="my_info_t">
							<div class="my_info_tl">
								<input type="hidden" value="<?=$member_row_info['idx']?>" class="user_live_value">
								<input type="hidden" value="<?=$member_row_info['email']?>" class="user_live_email">
								<input type="hidden" value="<?=$member_row_info['name']?>" class="user_live_name">
								<input type="hidden" value="<?=$member_row_info['part']?>" class="user_live_part">
								<input type="hidden" value="<?php echo $member_row_info['profile_img_src']?$member_row_info['profile_img_src']:"/html/images/pre/img_prof_default.png"?>" class="user_live_img">
								<div class="user_img" style="background-image:url('<?php echo $member_row_info['profile_img_src']?$member_row_info['profile_img_src']:"/html/images/pre/img_prof_default.png"?>');" id="profile_character_img"></div>
								<div class="setting_btn"><button class="button_prof main_prof"><span>프로필 변경</span></button></div>
								<div class="user_name">
									<strong><?=$member_row_info['name']?>님,안녕하세요!</strong>
									<span><?=$member_row_info['part']?></span>
								</div>
							</div>
							<div class="my_info_tr">
								<ul>
									<li><button id="btn_repass"><span>비밀번호 재설정</span></button></li>
									<li><button id="logout"><span>로그아웃</span></button></label></li>
								</ul>
							</div>
						</div>
						<div class="my_info_b">
							<div class="my_info_bt">
								<ul>
									<li>
										<a href = "http://demo.rewardy.co.kr/reward/index.php">
											<span>공용코인</span>
											<strong><em><?php echo $member_row_info['comcoin']?></em></strong>
										</a>
									</li>
									<li>
										<a href = "http://demo.rewardy.co.kr/reward/index.php">
											<span>내 코인</span>
											<strong><em><?php echo $member_row_info['coin']?></em></strong>
										</a>
									</li>
								</ul>
							</div>
							<div class="my_info_bb">
								<ul>
									<li>
										<button class ="header_cp">
											<span>역량</span>
											<strong><?php echo $reward_info['reward']?$reward_info['reward']:"0"?>점</strong>
										</button>
									</li>
									<li>
										<button class ="header_like">
											<span>좋아요</span>
											<strong><?php echo $like_count['cnt']?$like_count['cnt']:"0"?>개</strong>
										</button>
									</li>
								</ul>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<script type="text/javascript" src = "/js/header_new.js"></script>
<?php
	if($parts[0] != 'live'){
		include $home_dir . "/layer/member_like_layer.php";

		include $home_dir . "/layer/member_radar_layer.php";
	}
?>

</body>
</html>

				