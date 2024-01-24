<?
	//header페이지
	$home_dir = str_replace( basename(__DIR__) , "" , __DIR__ );
	include $home_dir . "/inc_lude/header_mobile.php";

    $sql = "select idx, code, service, coin from work_alarm where state = '0' and alarm_flag = '1' and companyno = '".$companyno."' and email = '".$user_id."' and workdate = '".TODATE."' order by idx desc limit 0, 100";

    $sql_alarm = "select a.idx, a.state, a.service, a.service_name, a.service_type, a.title, a.contents, a.send_email, a.code, a.workdate, a.regdate as reg, date_format( a.regdate , '%m/%d/%y %l:%i:%s %p') as regdate";
    $sql_alarm = $sql_alarm .= ",b.email, b.todaywork_alarm, b.challenges_alarm, b.party_alarm, b.reward_alarm, b.like_alarm, b.allselect_alarm, b.memo_alarm ";
    $sql_alarm = $sql_alarm .= " from work_alarm as a , work_member_alarm as b where a.email = b.email ";
    $sql_alarm = $sql_alarm .= " and a.alarm_flag = '1' and a.state = '0' and b.state = '0' and a.companyno = '".$companyno."' and a.workdate = '".TODATE."' and a.email = '".$user_id."' and a.idx = '".$timeline_info['idx'][$i]."' ";

    $timeline_info = selectAllQuery($sql);
?>
<html>
    <body>
    <style type="text/css">
        .rew_menu_onf{display:none !important;}

        html{font-size:5px;}
        html, body{height:100vh;}

        .ra_header_back:hover {
            background-color: #f0f0f0; /* 커서가 요소 위에 있을 때 배경색 변경 */
            color: #333; /* 커서가 요소 위에 있을 때 글자색 변경 */
            cursor: pointer; /* 커서 스타일 변경 (예: 손가락 포인터) */
        }
    </style>
    <div class="ra_warp">
        <div class="ra_warp_in">
            <div class="ra_header">
                <div class="ra_header_in">
                    <a class="ra_header_back"><span>뒤로가기</span></a>
                    <div class="ra_header_logo">
                        <a href="/alarm/index.php"><img src="img_logo_ra.png" alt="Rewardy"/></a>
                    </div>
                </div>
            </div>
            <div class="ra_contents">
                <div class="ra_contents_in">
                    <div class="ra_box">
                        <div class="ra_box_in">
                            <div class="ra_box_tit">
                                <span>알림 리스트</span>
                            </div>
                            <!-- css 추가 -->
                            <div class="ra_alert_list">
                                <ul>
                                <?for($i=0; $i<count($timeline_info['idx']); $i++){
                                    $code = $timeline_info['code'][$i];
                                    // $sql = "select * from work_member_alarm where email = '".$user_id."' ";
                                    // $alarm_filter = selectQuery($sql);

                                    $sql_alarm = "select a.idx, a.state, a.service, a.service_name, a.service_type, a.title, a.contents, a.send_email, a.code, a.workdate, a.regdate as reg, date_format( a.regdate , '%m/%d/%y %l:%i:%s %p') as regdate, (select name from work_member where email = a.send_email and state = '0') as name";
                                    // $sql_alarm = $sql_alarm .= ",b.email, b.todaywork_alarm, b.challenges_alarm, b.party_alarm, b.reward_alarm, b.like_alarm, b.allselect_alarm, b.memo_alarm ";
                                    $sql_alarm = $sql_alarm .= " from work_alarm as a , work_member_alarm as b where a.email = b.email ";
                                    $sql_alarm = $sql_alarm .= " and a.alarm_flag = '1' and a.state = '0' and b.state = '0' and a.companyno = '".$companyno."' and a.workdate = '".TODATE."' and a.email = '".$user_id."' and a.idx = '".$timeline_info['idx'][$i]."' ";

                                    if(in_array($code, array('3','6','22'))){
                                        $sql_alarm = $sql_alarm .= " and b.todaywork_alarm = '1'";
                                    }
                                    if(in_array($code, array('10'))){
                                        $sql_alarm = $sql_alarm .= " and b.like_alarm = '1'";
                                    }
                                    if(in_array($code, array('14','26'))){
                                        $sql_alarm = $sql_alarm .= " and b.challenges_alarm = '1'";
                                    }
                                    if(in_array($code, array('28','17'))){
                                        $sql_alarm = $sql_alarm .= " and b.party_alarm = '1'";
                                    }
                                    if(in_array($code, array('21','24','25'))){
                                        $sql_alarm = $sql_alarm .= " and b.reward_alarm = '1'";
                                    }
                                    if(in_array($code, array('37'))){
                                        $sql_alarm = $sql_alarm .= " and b.memo_alarm = '1'";
                                    }
                                    $alarm_info = selectQuery($sql_alarm);

                                    if($alarm_info['idx']){
                                        $code = $alarm_info['code'];
                                        $coin = $alarm_info['coin'];
                                        $memo = $alarm_info['title'];
                                        $tsend_email = $alarm_info['send_email'];
                                        $reg = $alarm_info['reg'];
                                        $regdate = $alarm_info['regdate'];
                                        $service = $alarm_info['service'];
                                        $service_type = $alarm_info['service_type'];
                                        $contents = $alarm_info['contents'];
                                        $tsend_name = $alarm_info['name'];

                                        $regdate_tmp = @explode(" ", $regdate);
                                        $regdate_ymd = $regdate_tmp[0];
                                        $regdate_his = $regdate_tmp[1];
                                        $regdate_apm = $regdate_tmp[2];
                                        $ymd_tmp = @explode("-", $regdate_ymd);
                                        $his_tmp = @explode(":", $regdate_his);
                                        $info_ampm = $regdate_apm;
                                        $info_hm = (strlen($his_tmp[0])==1?"0":"").$his_tmp[0].":".$his_tmp[1];
                                        $info_memo = "";

                                        $message = "";
                                            if($service == "live"){
                                                // $alarm_kind = "heart";
                                                $img_src = "../html/images/pre/alarm_ico_heart_app.png";
                                                $memo = "<strong>".$tsend_name."</strong>님에게 <strong>좋아요</strong> 받음";
                                            }elseif($service == "challenge"){
                                                // $alarm_kind = "chall";
                                                $img_src = "../html/images/pre/alarm_ico_bell.png";
                                            }elseif($service == "reward"){
                                                // $alarm_kind = "coin";
                                                $img_src = "../html/images/pre/alarm_ico_coin_new.png";
                                                if($service_type == "party" || $service_type == "challenge"){
                                                    $memo = $memo;
                                                }else{
                                                    $memo = "<strong>".$tsend_name."</strong>님에게 <strong>".$timeline_info['coin'][$i]."코인</strong> 받음";
                                                }
                                                $message = "<strong>".$timeline_info['coin'][$i]."</strong>코인";
                                            }elseif($service == "party"){
                                                // $alarm_kind = "party";
                                                $img_src = "../html/images/pre/alarm_ico_bell.png";
                                            }elseif($service == "work" && $service_type == "share"){
                                                // $alarm_kind = "today";
                                                $memo = "<strong>".$tsend_name."</strong>님으로부터 공유 받음";
                                                $img_src = "../html/images/pre/alarm_share_icon.png";
                                            }elseif($service == "work" && $service_type == "req"){
                                                // $alarm_kind = "today";
                                                $memo = "<strong>".$tsend_name."</strong>님으로부터 요청 받음";
                                                $img_src = "../html/images/pre/alarm_arrow_icon.png";
                                            }elseif($service == "work" && $service_type == "report"){
                                                // $alarm_kind = "today";
                                                $memo = "<strong>".$tsend_name."</strong>님으로부터 보고 받음";
                                                $img_src = "../html/images/pre/alarm_ico_get.png";
                                            }elseif($service == "memo"){
                                                // $alarm_kind = "today";
                                                $memo = "<strong>".$tsend_name."</strong>님으로부터 메모 받음";
                                                $img_src = "../html/images/pre/alarm_ico_memo.png";
                                            }elseif($service == "penalty"){
                                                $img_src = "../html/images/pre/ico_pe.png";
                                            } ?>

                                        <li id="alarm_list_<?=$timeline_info['idx'][$i]?>">
                                            <a class="ra_alert_box" id="alarm_link_page">
                                                <input type="hidden" value="<?=$timeline_info['idx'][$i]?>" id="alarm_idx">
                                                <input type="hidden" value="<?=$timeline_info['service'][$i]?>" id="service_alarm">  
                                                <div class="ra_alert_box_tit">
                                                    <img src="<?=$img_src?>" alt="" />
                                                    <span><?=$memo?></span>
                                                </div>
                                                <div class="ra_alert_box_desc">
                                                    <span><?=$contents?></span>
                                                </div>
                                                <div class="ra_alert_box_info">
                                                    <span><?=TODATE?> <?=$info_ampm?> <?=$info_hm?></span>
                                                    <span><?=$message?></span>
                                                </div>
                                            </a>
                                            <button class="ra_alert_close" id="alarm_del"><span>닫기</span></button>
                                        </li>
                                    <? }
                                    }
                                        if(!$timeline_info['idx']){?>
                                            <li class="ra_alert_box">
                                                <a class="ra_box_area">
                                                    <div class="ra_alert_box_tit">
                                                        <span><?=$memo?></span>
                                                    </div>
                                                    <div class="ra_alert_box_desc">
                                                        <span>알림이 없습니다.</span>
                                                    </div>
                                                    <div class="ra_alert_box_info">
                                                        <strong><?=$alarm_info['service_name']?></strong>
                                                        <span><?=$info_ampm?> <?=$info_hm?> </span>
                                                    </div>
                                                </a>
                                            </li>
                                            <?}?>
                                        </ul>
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
        $(document).ready(function() {
            // Function to refresh the page when scrolling to the top
            function refreshOnScrollTop() {
                if ($(window).scrollTop() === 0) {
                location.reload();
                }
            }

            // Attach the scroll event listener
            $(window).scroll(refreshOnScrollTop);
        });
    /* FOR BIZ., COM. AND ENT. SERVICE. */
    _TRK_CP = "/Rewardy"; /* 페이지 이름 지정 Contents Path */
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
            webkit.messageHandlers.getFcmInfo.postMessage(""); // ios 의 ( getFcmInfo)라고 생각하시면 편합니다.
        } else {
            localStorage.setItem("is_app","N");
        }
    }catch (e){
        localStorage.setItem("is_app","N");
    }finally{

    }
    </script>
    </body>
</html>