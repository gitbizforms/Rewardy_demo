<?
	//header페이지
	$home_dir = str_replace( basename(__DIR__) , "" , __DIR__ );
	include $home_dir . "/inc_lude/header_mobile.php";

    $sql = "select device_uuid, push_register_id from push_device_info where mem_id = '".$user_id."' ";
    $device = selectQuery($sql);

    if($user_id){
        $log_class = "ra_footer_logout";
        $log_state = "로그아웃";
    }else{
        $log_class = "ra_footer_login";
        $log_state = "로그인";
    }
    
?>
<html>
    <body>
    <style type="text/css">
	html{font-size:5px;}
	html, body{height:100vh;}
    </style>
        <div class="ra_warp">
            <div class="ra_warp_in">
                <div class="ra_intro">
                    <div class="ra_intro_in">
                        <div class="ra_intro_logo">
                            <img src="img_logo_ra.png" alt="Rewardy"/ >
                        </div>
                        <div class="ra_intro_title">
                            <strong>일 좀 하는 회사의 <br />스마트한 보상 플랫폼</strong>
                            <span>리워디와 함께 성장하세요! </span>
                        </div>
                        <div class="ra_intro_btns">
                            <? if($user_id){?>
                                <button class="ra_btn_login" id="ra_btn_logout" ><span>로그아웃</span></button>
                                <?}else{?>
                                <button class="ra_btn_login" onclick="location.href='login.php'"><span>로그인</span></button>
                                <?}?>
                            <button class="ra_btn_link"><span>둘러보기</span></button>
                            <? if($device['device_uuid']){?>
                                <input type="hidden" value="<?=$device['device_uuid']?>" id="push_device_id">
                            <?}?>
                        </div>
                    </div>
                </div>

				<div class="ra_footer">
                    <div class="ra_footer_in">
                        <div class="ra_footer_btn">
                            <button class="ra_footer_link"><span>처음으로</span></button>
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
    </script>
    </body>
</html>

<?
    include $home_dir . "/inc_lude/footer.php";
    
?>

<script>

    try{
        localStorage.clear();

        var varUA = navigator.userAgent.toLowerCase();
        var regExpAndroid = /softapp_android/gi;
        var regExpIos = /softapp_ios/gi;

        if (varUA.match(regExpAndroid)) {
            window.SoftappAOS.loadingFinish();
            localStorage.setItem("is_android","Y");
            localStorage.setItem("is_app","Y");
            window.SoftappAOS.getFcmInfo();

        } else if (varUA.match(regExpIos)) {//ios 부분입니다.
            localStorage.setItem("is_ios","Y");//ios 여부를 localstorage로 체크합니다.
            localStorage.setItem("is_app","Y");//app 여부를 localstorage로 체크합니다.
            webkit.messageHandlers.getFcmInfo.postMessage(""); // ios 의 (getFcmInfo)라고 생각하시면 편합니다.
        } else {
            localStorage.setItem("is_app","N");
        }
    }catch (e){
        localStorage.setItem("is_app","N");
    }finally{

    }


</script>