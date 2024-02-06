<?
	//header페이지
	$home_dir = str_replace( basename(__DIR__) , "" , __DIR__ );
	include $home_dir . "/inc_lude/header_mobile.php";

	if($user_id){
        $log_class = "ra_footer_logout";
        $log_state = "로그아웃";
    }else{
        $log_class = "ra_footer_login";
        $log_state = "로그인";
    }
?>

<style type="text/css">
	.rew_menu_onf{display:none !important;}
</style>

<body>
<style type="text/css">
	html{font-size:5px;}
	html, body{height:100vh;}
</style>
<div class="ra_warp">
	<div class="ra_warp_in">
		<div class="ra_header">
			<div class="ra_header_in">
				<a href="#" class="ra_header_back"><span>뒤로가기</span></a>
				<div class="ra_header_logo">
					<img src="img_logo_ra.png" alt="Rewardy"/ >
				</div>
			</div>
		</div>
		<div class="ra_contents">
			<div class="ra_contents_in">
				<div class="ra_login">
					<div class="ra_login_in">
						<div class="ra_login_tit">
							<? if($user_id){?>
								<span>안녕하세요 : ) <br />리워디를 시작해보세요.</span>
							<?}else{?>
								<span>안녕하세요 : ) <br />로그인을 부탁드려요.</span>
							<?}?>
						</div>
						<? if($user_id){?>
						
						<?}else{?>
							<div class="ra_login_list">
								<ul>
									<li>
										<div class="ra_login_input">
											<input type="text" id="z1" name="user_id" class="input_082" placeholder="이메일" />
											<label for="z1" class="label_082">
												<strong class="label_tit">이메일을 입력하세요</strong>
											</label>
										</div>
									</li>
									<li>
										<div class="tc_input">
											<input type="password" id="z2" name="user_pwd" class="input_082" placeholder="비밀번호" />
											<label for="z2" class="label_082">
												<strong class="label_tit">비밀번호를 입력하세요</strong>
											</label>
										</div>
									</li>
								</ul>
							</div>
							<div class="ra_login_chk">
								<div class="ra_login_chk_in">
									<input type="checkbox" name="chk_login" id="chk_login" />
									<label for="chk_login">로그인 상태유지</label>
								</div>
							</div>
							<div class="ra_login_btn">
								<button id="ra_btn_login_mo" class="ra_btn_login"><span>로그인</span></button>
							</div>
							<?}?>
						</div>
					</div>
				</div>
			</div>
		<div class="ra_footer">
			<div class="ra_footer_in">
				<div class="ra_footer_btn">
					<button class="ra_footer_link"><span>접속하기</span></button>
					<button class="ra_footer_list"><span>알림리스트</span></button>
					<button class="ra_footer_setting"><span>알림설정</span></button>
					<button class="<?=$log_class?>"><span><?=$log_state?></span></button>
				</div>
			</div>
		</div>
	</div>
</div>
<script language="JavaScript">
/* FOR BIZ., COM. AND ENT. SERVICE. */
	_TRK_CP = "/Rewardy"; /* 페이지 이름 지정 Contents Path */

	try{
    var varUA = navigator.userAgent.toLowerCase(); 
    var regExpAndroid = /softapp_android/gi; // 안드로이드 로 접근 구분자
    var regExpIos = /softapp_ios/gi; // IOS  로 접근 구분자

    if (varUA.match(regExpAndroid)) {//안드로이드의 경우
        window.SoftappAOS.getFcmInfo();
    } else if (varUA.match(regExpIos)) {//IOS 의 경우
			localStorage.setItem("is_ios","Y");
            localStorage.setItem("is_app","Y");
            webkit.messageHandlers.getFcmInfo.postMessage("");
    } else {
		localStorage.setItem("is_app","N");
    }
    }catch (e){
		localStorage.setItem("is_app","N");

        }finally{
			
}
</script>

</body>
<!-- footer start-->
<? include $home_dir . "/inc_lude/footer.php";?>
<!-- footer end-->
