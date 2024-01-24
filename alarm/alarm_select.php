<?php
	//header페이지
	$home_dir = str_replace( basename(__DIR__) , "" , __DIR__ );
	include $home_dir . "/inc_lude/header_mobile.php";


	if($user_id=='sadary0@nate.com'){
	}else{
		//if( $user_level != '0'){
		//	alertMove("접속권한이 없습니다.");
		//	exit;
		//}
	}
	$sql = "select idx, email, todaywork_alarm, challenges_alarm, party_alarm, reward_alarm, like_alarm, memo_alarm, allselect_alarm from work_member_alarm where email = '".$user_id."' and state = '0'";
	$alarm_info = selectQuery($sql);

	$alarm_name = array("전체설정","오늘 업무","좋아요","챌린지","코인보상","파티","메모");
	$alarm_value = array("allselect_alarm","todaywork_alarm","like_alarm","challenges_alarm","reward_alarm","party_alarm","memo_alarm");

	$sql = "select state, email, name, companyno from work_member where email = '".$user_id."'";
    $userinfo = selectQuery($sql);
    $companyno = $userinfo['companyno'];

    $sql = "select * from work_member_alarm where email = '".$user_id."'";
    $query = selectQuery($sql);

	if($user_id&&!$query['email']){
        $sql = "insert into work_member_alarm set
                    email = '".$user_id."',
                    companyno = '".$companyno."',
                    state = '".$userinfo['state']."',
                    todaywork_alarm = '1',
                    challenges_alarm = '1',
                    party_alarm = '1',
                    reward_alarm = '1',
                    like_alarm = '1',
					memo_alarm = '1',
                    allselect_alarm = '1',
                    workdate = '".TODATE."'
        	";
        $insert_alarm = insertQuery($sql);
    }
?>
<script src="/js/common.js<?php echo VER;?>"></script>
<style type="text/css">
	html{font-size:5px;}
	html, body{height:100vh;}
</style>
<div class="ra_warp">
	<div class="ra_warp_in">
		<div class="ra_header">
			<div class="ra_header_in">
				<?/*<button class="ra_header_back" id="ra_header_back"><span>뒤로가기</span></button>*/?>
				<div class="ra_header_logo">
				<a href="/alarm/index.php"><img src="img_logo_ra.png" alt="Rewardy"/ ></a>
				</div>
			</div>
		</div>
		<div class="ra_contents">
			<div class="ra_contents_in">
				<div class="ra_setting large">
					<div class="ra_setting_in">
						<div class="ra_setting_tit">
							<span>알림 설정</span>
						</div>
						<div class="ra_setting_all">
							<dl>
								<dt>전체 알림 허용</dt>
								<dd>
									<button class="btn_switch<?=$alarm_info['allselect_alarm']=="1"?" on":""?>" id="all_chk_btn" value="allselect_alarm">
										<span>버튼</span>
									</button>
								</dd>
							</dl>
						</div>
						<div class="ra_setting_list">
							<dl>
								<dt>오늘업무 알림</dt>
								<dd>
									<button class="btn_switch<?=$alarm_info['todaywork_alarm']=="1"?" on":""?>" value="todaywork_alarm" id="sw_idx">
										<span>버튼</span>
									</button>
								</dd>
							</dl>
							<dl>
								<dt>챌린지 알림</dt>
								<dd>
									<button class="btn_switch<?=$alarm_info['challenges_alarm']=="1"?" on":""?>" value="challenges_alarm" id="sw_idx">
										<span>버튼</span>
									</button>
								</dd>
							</dl>
							<dl>
								<dt>파티 알림</dt>
								<dd>
									<button class="btn_switch<?=$alarm_info['party_alarm']=="1"?" on":""?>" value="party_alarm" id="sw_idx">
										<span>버튼</span>
									</button>
								</dd>
							</dl>
							<dl>
								<dt>좋아요 알림</dt>
								<dd>
									<button class="btn_switch<?=$alarm_info['like_alarm']=="1"?" on":""?>" value="like_alarm" id="sw_idx">
										<span>버튼</span>
									</button>
								</dd>
							</dl>
							<dl>
								<dt>코인 알림</dt>
								<dd>
									<button class="btn_switch<?=$alarm_info['reward_alarm']=="1"?" on":""?>" value="reward_alarm" id="sw_idx">
										<span>버튼</span>
									</button>
								</dd>
							</dl>
							<dl>
								<dt>메모 알림</dt>
								<dd>
									<button class="btn_switch<?=$alarm_info['memo_alarm']=="1"?" on":""?>" value="memo_alarm" id="sw_idx">
										<span>버튼</span>
									</button>
								</dd>
							</dl>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="ra_footer">
			<div class="ra_footer_in">
				<div class="ra_footer_btn">
					<button class="ra_footer_link"><span>처음으로</span></button>
					<button class="ra_footer_list"><span>알림리스트</span></button>
					<button class="ra_footer_setting"><span>알림설정</span></button>
					<button class="ra_footer_logout"><span>로그아웃</span></button>
				</div>
			</div>
		</div>
	</div>
</div>
<script language="JavaScript">
/* FOR BIZ., COM. AND ENT. SERVICE. */
_TRK_CP = "/Rewardy"; /* 페이지 이름 지정 Contents Path */
</script>

<script type="text/javascript">
	
</script>

