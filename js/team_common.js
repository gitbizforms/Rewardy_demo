$(function() {
    //속성설정
    $('#datepickers-container').css('z-index', 9999);
    $("#pl_file").css("visibility", "hidden");

    //챌린지 최근 2개 
    $(document).on("click", "a[id^='link_challanges']", function() {
        if (GetCookie("user_id") != null) {
            var val = $(this).attr("value");
            if (val) {
                location.href = "/challenge/view.php?idx=" + val;
            }
        } else {
            $(".rew_layer_login").show();
        }
    });


    //오늘업무 리스트
    $(document).on("click", "#todaywork_main_memo,#todaywork_main_req,#todaywork_main_share", function() {
        if (GetCookie("user_id") != null) {
            location.href = "/todaywork/index.php";
        } else {
            $(".rew_layer_login").show();
        }
    });

    //챌린지 리스트
    $(document).on("click", "#challenges_main", function() {
        if (GetCookie("user_id") != null) {
            location.href = "/challenge/index.php";
        } else {
            $(".rew_layer_login").show();
        }
    });

    //오늘업무 리스트
    $(document).on("click", "a[id^='challenges_main']", function() {
        if (GetCookie("user_id") != null) {
            location.href = "/challenge/index.php";
        } else {
            $(".rew_layer_login").show();
        }
    });


    $(".circle_01").circleProgress({
        startAngle: -Math.PI / 4 * 2,
        value: 0,
        thickness: 2,
        size: 104,
        emptyFill: '#e5e5e5',
        lineCap: 'round',
        fill: { color: '#e5e5e5' },
        animation: {
            duration: 1200
        }
    });

    $(".rew_conts_scroll_07").scroll(function() {
        var lbt = $(".rew_live").offset().top;
        if (lbt < 120) {
            $(".rew_live_my").addClass("pos_fix");
        } else {
            $(".rew_live_my").removeClass("pos_fix");
        }
    });


    //메인 리스트 속도
    $(".rew_mains_live_list .live_list .live_list_box").each(function() {
        var tis = $(this);
        var tindex = $(this).index();
        setTimeout(function() {
            tis.addClass("sli");
        }, 500 + tindex * 100);
    });



    $(".live_list .live_list_box").each(function() {
        var live_th = $(this);
        var live_st = live_th.find(".live_list_today .live_list_today_count strong").text();
        var live_sp = live_th.find(".live_list_today .live_list_today_count span").text();
        var live_ca = live_st / live_sp;
        var live_co = 0;

        var email = live_th.find(".live_list_today .live_list_today_count").parent("button").val();
        var live_hap = live_st / 3;
        var live_ban = live_sp / 2;
        var result_ca = parseFloat(live_hap).toFixed(2);
        var result_ban = parseFloat(live_ban).toFixed(1);


        //숫자형일때
        if (isNaN(result_ca) == false) {
            if (live_st == live_sp) {
                live_co = '#3ac73b';
            } else {
                if (live_ban == live_st) {
                    live_co = '#f7cc07';
                } else {
                    if (result_ca >= 0.67) {
                        if (Number(live_st) >= result_ban) {
                            live_co = '#3ac73b';
                        } else {
                            live_co = '#ec132e';
                        }
                    } else if (result_ca >= 0.34 && result_ca <= 0.66) {
                        live_co = '#f7cc07';
                    } else if (result_ca <= 0.33) {
                        if (Number(live_st) >= result_ban) {
                            live_co = '#f7cc07';
                        } else {
                            live_co = '#ec132e';
                        }
                    }
                }
            }
        } else {
            live_co = "";
        }

        setTimeout(function() {
            live_th.find(".circle_01").circleProgress({
                value: live_ca,
                fill: { color: live_co }
            });
        }, 2400);
    });


    //업무시작
    $(".onoff_01 .btn_switch").click(function() {
        if (GetCookie("user_id") != null) {
            $(".onoff_04 .btn_switch").removeClass("on");
            $(".onoff_04 .btn_switch").prev("em").removeClass("on");
            $(this).addClass("on");
            $(this).prev("em").addClass("on");
        } else {
            $(".rew_layer_login").show();
        }

    });

    //자리비움
    $(".onoff_03 .btn_switch").click(function() {
        if (GetCookie("user_id") != null) {
            var switchon = $(this);
            if (switchon.hasClass("on")) {
                $(this).removeClass("on");
                $(this).prev("em").removeClass("on");
            } else {
                $(".onoff_02 .btn_switch").removeClass("on");
                $(".onoff_02 .btn_switch").prev("em").removeClass("on");
                $(".onoff_04 .btn_switch").removeClass("on");
                $(".onoff_04 .btn_switch").prev("em").removeClass("on");
                $(".onoff_01 .btn_switch").addClass("on");
                $(".onoff_01 .btn_switch").prev("em").addClass("on");
                $(this).addClass("on");
                $(this).prev("em").addClass("on");
            }
        } else {
            $(".rew_layer_login").show();
        }
    });

    //퇴근
    $(".onoff_04 .btn_switch").click(function() {
        if (GetCookie("user_id") != null) {
            var switchon = $(this);

            if (switchon.hasClass("on")) {
                alert("이미 퇴근 하셨습니다.");
                return false;
                //$(this).removeClass("on");
                //$(this).prev("em").removeClass("on");
            } else {

                var fdata = new FormData();
                var val = $(this).attr("value");
                fdata.append("mode", "todaywork_review_info");
                fdata.append("workdate", val);
                $.ajax({
                    type: "post",
                    data: fdata,
                    contentType: false,
                    processData: false,
                    url: '/inc/works_process.php',
                    success: function(data) {
                        console.log("data ::: " + data);
                        var obj = $(".ff_area ul li");
                        var obj_len = obj.length;

                        if (data) {
                            tdata = data.split("|");
                            if (tdata) {
                                var result = tdata[0];
                                var review_idx = tdata[1];
                                var icon_idx = tdata[2];
                                var comment = tdata[3];
                                if (result == "complete") {

                                    $(".feeling_first").show();

                                    for (i = 1; i <= obj_len; i++) {
                                        if (i == icon_idx) {
                                            $(".btn_ff_0" + i).addClass("on");
                                            ff_class = "btn_ff_0" + i;
                                        } else {
                                            $(".btn_ff_0" + i).removeClass("on");
                                        }
                                    }

                                    $("#review_idx").val(review_idx);
                                    $("#icon_idx").val(icon_idx);
                                    if ($(".ff_bottom button").hasClass("btn_off") == true) {
                                        $(".ff_bottom button").removeClass("btn_off");
                                        $(".ff_bottom button").addClass("btn_on");
                                    }

                                    $(".fl_area .fl_desc").removeClass().addClass("fl_desc");
                                    $(".fl_area .fl_desc").addClass(ff_class);
                                    $("#input_fl").val(comment);
                                }
                            }
                        }
                    }
                });
            }
        } else {
            $(".rew_layer_login").show();
        }
    });



    //업무시작 버튼
    $("#main_1_bt").click(function() {
        if (GetCookie("user_id") == null) {
            if (location.search) {
                $("#rew_layer_setting").show();
            } else {
                $(".rew_layer_login").show();
            }
            return false;
        }
		console.log(" ## " + $(".rew_grid_onoff_inner ul li em").hasClass("on"));
        if ($("#main_1_bt").hasClass("on") == true) {
            var live_1_switch = "true";
            var fdata = new FormData();
            fdata.append("mode", "live_1_change");
            fdata.append("status", live_1_switch);
            
            $.ajax({
                type: "post",
                async: false,
                data: fdata,
                contentType: false,
                processData: false,
                url: '/inc/main_process.php',
                success: function(data) {
                    console.log("data :: " + data);
                    if (data) {
                        tdata = data.split("|");
                        if (tdata) {
                            stack = tdata[0];
                            work = tdata[1];
                            outcount = tdata[2];
                            chall = tdata[3];
                            incount = tdata[4];
                            result = tdata[5];
                            if (result == "true") {
                                if(stack == "penalty"){
                                    if(work>=3){
                                        $("#penalty_work").show();
                                    }
                                    if(outcount>=3){
                                        $("#penalty_out").show(); 
                                    }
                                    if(chall>=3){
                                        $("#penalty_chall").show(); 
                                    }
                                    if(incount>=3){
                                        $("#penalty_in").show();
                                    }
                                }
                                //$(".onoff_01 em").text("출근(" + result2 + ")");
                                //$(".onoff_01 em").text("근무중");
                                //$(".onoff_04 em").text("퇴근");

                                $("#main_1_bt").removeClass("switch_ready");
                                $("#main_1_bt").addClass("on");
                                //$(this).prev("em").addClass("on");
                                $(".onoff_01 em").text("근무중");
                                main_live_list();
                            }
                        }
                    }
                }
            });
        }
    });



    //집중 버튼
    $("#live_2_bt").click(function() {

        if ($(".rew_grid_onoff .rew_grid_onoff_in .onoff_02 em").hasClass("on") == true) {
            var live_2_switch = "true";
        } else if ($(".rew_grid_onoff .rew_grid_onoff_in .onoff_02 em").hasClass("on") == false) {
            var live_2_switch = "false";
        }

        var fdata = new FormData();
        fdata.append("mode", "live_2_change");
        fdata.append("status", live_2_switch);
        $.ajax({
            type: "post",
            async: false,
            data: fdata,
            contentType: false,
            processData: false,
            url: '/inc/main_process.php',
            success: function(data) {
                console.log("data :: " + data);
                if (data) {
                    tdata = data.split("|");
                    if (tdata) {
                        var result = tdata[0];
                        var result2 = tdata[1];
                        //console.log(result2);
                        if (result == "true") {
                            main_live_list();
                        }
                    }
                }
            }
        });

    });


    //회의 버튼
    $(document).on("click", "#live_3_bt", function(){
        var off_state = $(this);
        if($(this).hasClass("on")){
            if(confirm("회의를 종료하시겠습니까?")){
                var email = $("#mains_info_uid").val();
                var fdata = new FormData();
                // console.log(email);
                fdata.append("mode", "work_state_off");
                fdata.append("email", email);

                $.ajax({
                            type: "post",
                            async: false,
                            data: fdata,
                            contentType: false,
                            processData: false,
                            url: '/inc/lives_process.php',
                            success: function(data) {
                                console.log(data);    
                                if (data == "complete") {
                                    alert("현재 시간 전 회의는 종료되었습니다.");
                                    off_state.removeClass("on");
                                }
                            }
                        });
            }
        }else{
            $(".layer_memo").show();
            var currentTime = new Date();
            var currentHour = currentTime.getHours();
            var currentMin = Math.ceil(currentTime.getMinutes() / 10) * 10; // 가장 가까운 10분 단위로 반올림
            // 60분인 경우 00분으로 바꾸고 시간을 1시간 추가
            if (currentMin === 60) {
                currentMin = 0;
                currentHour += 1;
            }

            // 시간이 24시를 넘어가면 00시로 설정
            if (currentHour === 24) {
                currentHour = 0;
            }

            currentMin = (currentMin < 10) ? '0' + currentMin : currentMin;
            
            $(".tdw_time_start .first_set span").val(currentHour);
            $(".tdw_time_start .second_set span").val(currentMin);
            $(".tdw_time_end .first_set span").val(currentHour);
            $(".tdw_time_end .second_set span").val(currentMin);
            
            $(".tdw_time_start .first_set span").text(currentHour);
            $(".tdw_time_start .second_set span").text(currentMin);
            $(".tdw_time_end .first_set span").text(currentHour);
            $(".tdw_time_end .second_set span").text(currentMin);
            
            $("#startHour").val(currentHour);
            $("#startMin").val(currentMin);
            $("#endHour").val(currentHour);
            $("#endMin").val(currentMin);
            // $(this).addClass("on");
        }
    });
    //회의 팝업 취소 버튼
    $(document).on("click", ".layer_memo_cancel", function(){
        $(".layer_memo").hide();
    });

    // 회의 시간 체크
     //일정에 따른 시간 체크 
    $(".time_set .tdw_tab_sort_in").click(function(){

        
        $(this).find('ul').css("display", "block");


    });
    $(".time_set .tdw_tab_sort_in").mouseleave(function(){
        $(this).find('ul').css("display", "");

    });

    $(document).on("click", ".startTimeHour", function(){
        var val = $(this).attr("value");
    if (val) {
    $("#startHour").val(val);
    }
        $('.tdw_time_start .tdw_time_hour .btn_sort_on span').text(val);
        $('.time_set .tdw_tab_sort_in ul').css("display", "none");
    });
    $(document).on("click", ".startTimeMin", function(){
        var val = $(this).attr("value");
    if (val) {
    $("#startMin").val(val);
    }
        $('.tdw_time_start .tdw_time_min .btn_sort_on span').text(val);
        $('.time_set .tdw_tab_sort_in ul').css("display", "none");
    });
    $(document).on("click", ".endTimeHour", function(){
        var val = $(this).attr("value");
    if (val) {
    $("#endHour").val(val);
    }
        $('.tdw_time_end .tdw_time_hour .btn_sort_on span').text(val);
        $('.time_set .tdw_tab_sort_in ul').css("display", "none");
    });
    $(document).on("click", ".endTimeMin", function(){
        var val = $(this).attr("value");
    if (val) {
    $("#endMin").val(val);
    }
        $('.tdw_time_end .tdw_time_min .btn_sort_on span').text(val);
        $('.time_set .tdw_tab_sort_in ul').css("display", "none");
    });

    // 회의 팝업 등록 
    
    $('.layer_memo_submit').hover(function(){
        $(this).addClass("on");
    });

    $(document).on('mouseleave', '.layer_memo_submit', function(){
        $(this).removeClass("on");
    });


    //회의 등록
    $(document).on("click", ".layer_memo_submit", function(){
        var fdata = new FormData();
        var text_val = $(".textarea_memo").val();

       
        if(!text_val){
            alert("회의 내용을 작성해주세요.");
        }else{
            var sHour = $("#startHour").val();
            var sMin = $("#startMin").val();
            var eHour = $("#endHour").val();
            var eMin = $("#endMin").val();
                if ($("#startHour").val() && $("#startMin").val() && $("#endHour").val() && $("#endMin").val()){
                    var startTime = sHour+":"+sMin; 
                    var endTime = eHour+":"+eMin;
                    if(startTime > endTime){
                        alert("시작시간은 끝 시간보다 작아야합니다.");
                        return false;
                    }else if(startTime == endTime){
                        alert("시작시간과 종료시간은 같을 수 없습니다.");
                        return false;
                    }
                }

                fdata.append('contents', text_val);
                fdata.append('decide_flag', 8);
                fdata.append('work_flag', 2);
                fdata.append('work_stime', startTime);
                fdata.append('work_etime', endTime);
                fdata.append('mode', 'main_meeting');

                $.ajax({
                    type: "post",
                    data: fdata,
                    contentType: false,
                    processData: false,
                    url: '/inc/main_process_new.php',
                    beforeSend: function () {
                        $('.rewardy_loading_01').css('display', 'block');
                      },
                    success: function(data) {
                        console.log(data);
                        if (data == "complete") {
                            alert("회의가 작성되었습니다.");
                              $(".layer_memo").hide();
                              $('.rewardy_loading_01').css('display', 'none');
                        }else{
                            console.log("작성에 실패하였습니다.");
                        }
                    }
                });
        }
    });

    //오늘 한 줄 소감
    //닫기 버튼
    var ff_class = "";
    var ff_text = "";

    $(".ff_area button").click(function() {
        ff_class = $(this).attr("class");
        var val = $(this).val();
        if (!val) {
            val = $("#icon_idx").val();
        }

        if (val) {
            $("#icon_idx").val(val);
            if ($(this).attr("class") == "btn_ff_01") { ff_text = "최고의"; }
            if ($(this).attr("class") == "btn_ff_02") { ff_text = "뿌듯한"; }
            if ($(this).attr("class") == "btn_ff_03") { ff_text = "기분 좋은"; }
            if ($(this).attr("class") == "btn_ff_04") { ff_text = "감사한"; }
            if ($(this).attr("class") == "btn_ff_05") { ff_text = "재밌는"; }
            if ($(this).attr("class") == "btn_ff_06") { ff_text = "수고한"; }
            if ($(this).attr("class") == "btn_ff_07") { ff_text = "무난한"; }
            if ($(this).attr("class") == "btn_ff_08") { ff_text = "지친"; }
            if ($(this).attr("class") == "btn_ff_09") { ff_text = "속상한"; }
            $(".ff_area button").not(this).removeClass("on");
            $(this).addClass("on");
            $(".ff_bottom button").removeClass("btn_off").addClass("btn_on");
        }
    });

    //닫기 버튼
    $(".ff_close button").click(function() {
        $(".feeling_first").hide();
        $(".ff_area button").removeClass("on");
        $(".ff_bottom button").removeClass("btn_on").addClass("btn_off");
    });

    $(".ff_bottom button").click(function() {
        if ($(this).hasClass("btn_on")) {
            $(".feeling_first").hide();
            $(".ff_area button").removeClass("on");
            $(".ff_bottom button").removeClass("btn_on").addClass("btn_off");
            $(".feeling_layer").show();
            $(".fl_area .fl_desc").removeClass().addClass("fl_desc");
            $(".fl_area .fl_desc").addClass(ff_class);
            $(".fl_area .fl_desc span").text(ff_text);
        } else {

        }
    });
    
    $(document).on('click', '.rew_main_anno_in', function(){
        var p_memo = $(".text_area .layer_text").val();
        if(p_memo){
            $('.rew_main_anno_in span').text(p_memo);
        }
        $('.layer_pro').show();
    });
    // 상태메세지 등록
    $(document).on('click', '.submit_btn .btn_on', function(){
        var p_memo = $(".text_area .layer_text").val();
        var char_val = $("#check_profile").val();
        var fdata = new FormData();
        fdata.append("p_memo", p_memo);
        fdata.append("profile_no", char_val);

        fdata.append("mode", "team_memo");
            $.ajax({
                type: "post",
                    data: fdata,
                    contentType: false,
                    processData: false,
                    url: '/inc/main_process.php',
                    success: function(data) {
                        console.log(data);
                        
                        if (data) {
                            tdata = data.split("|");
                            if(tdata){
                                console.log(tdata);
                                var result = tdata[0];
                                var result2 = tdata[1];
                                var result3 = tdata[2];
                                if(result == 'complete'){
                                    if(p_memo == ''){
                                        $('.rew_main_anno_in span').text("상태 메시지를 입력해주세요.");
                                    }else{
                                        $('.rew_main_anno_in span').text(p_memo);
                                    }
                                    if(result2){
                                        $(".tl_prof_img").css("background-image", "url(" + result2 + ")");
                                        $(".live_list_user_imgs:first").css("background-image", "url(" + result2 + ")");
                                        $(".user_img").css("background-image", "url(" + result2 + ")");
                                    }
                                    if(result3){
                                        $(".tl_prof_img").css("background-image", "url(" + result3 + ")");
                                        $(".live_list_user_imgs:first").css("background-image", "url(" + result3 + ")");
                                        $(".user_img").css("background-image", "url(" + result3 + ")");
                                    }
                                }
                            }
                            $('.layer_pro').hide();
                        }else{
                            $('.layer_pro').hide();
                        }
                    }
            });
    });



    //한줄 소감 등록하기
    $("#fl_bottom").click(function() {

        //var val = $(".fl_area .fl_desc").attr("class");
        var val = $("#icon_idx").val();
        if (val) {
            //var icon_val = val.substr(-1);
            //var ff_class = "btn_ff_0" + val;

            var fdata = new FormData();
            var input_val = $("#input_fl").val();

            if (!input_val) {
                alert("한줄소감을 남겨주세요.");
                $("#input_fl").focus();
                return false;
            }

            $(".feeling_layer").hide();
            $(".onoff_01 .btn_switch").removeClass("on");
            $(".onoff_01 .btn_switch").prev("em").removeClass("on");
            $(".onoff_02 .btn_switch").removeClass("on");
            $(".onoff_02 .btn_switch").prev("em").removeClass("on");
            $(".onoff_03 .btn_switch").removeClass("on");
            $(".onoff_03 .btn_switch").prev("em").removeClass("on");
            $(".onoff_04 .btn_switch").addClass("on");
            $(".onoff_04 .btn_switch").prev("em").addClass("on");

            fdata.append("mode", "todaywork_review_write");


            fdata.append("input_val", input_val);
            fdata.append("workdate", $("#review_idx").val());
            fdata.append("icon_idx", $("#icon_idx").val());



            $.ajax({
                type: "post",
                data: fdata,
                contentType: false,
                processData: false,
                url: '/inc/works_process.php',
                success: function(data) {
                    //console.log("dddd :: " + data);
                    if (data) {
                        tdata = data.split("|");
                        if (tdata) {
                            var result = tdata[0];
                            var review_idx = tdata[1];
                            var icon_idx = tdata[2];
                            var comment = tdata[3];
                            if (result == "complete") {
                                //if (GetCookie("user_id") == 'sadary0@nate.com') {
                                //    console.log("NN");
                                //} else {
                                live_4_bt_func();
                                //}
                            }
                        }
                    }
                }
            });
        }
    });

    //한줄소감 닫기
    $(".fl_close button").click(function() {
        $(".feeling_layer").hide();
    });





    //메인 업데이트
    $("#reload_index").click(function() {

        if (GetCookie("user_id") != null) {
            var fdata = new FormData();
            fdata.append("mode", "reload_index");

            $.ajax({
                type: "post",
                async: false,
                data: fdata,
                contentType: false,
                processData: false,
                url: '/inc/main_process_new.php',
                success: function(data) {
                    console.log("data :: " + data);
                    if (data) {
                        tdata = data.split("|");
                        if (tdata) {
                            var result = tdata[0];
                            var result2 = tdata[1];
                            //console.log(result2);
                            if (result == "complete") {
                                $(".rew_mains_live_title span em").text(result2);
                                main_live_list();
                            }
                        }
                    }
                }
            });
        } else {
            $(".rew_layer_login").show();
        }
    });

    //메인 좋아요 업데이트
    $("#reload_like_index").click(function() {

        if (GetCookie("user_id") != null) {
            var fdata = new FormData();
            fdata.append("mode", "reload_like_index");

            $.ajax({
                type: "post",
                async: false,
                data: fdata,
                contentType: false,
                processData: false,
                url: '/inc/main_process.php',
                success: function(data) {
                    console.log("data :: " + data);
                    $(".rew_heart_area_in ul").html(data);
                }
            });
        } else {
            $(".rew_layer_login").show();
        }
    });


    //오늘업무등록
    $("#today_work_bt").click(function() {

        if (GetCookie("user_id") == null) {
            $(".rew_layer_login").show();
            return false;
        }

        if ($("#main_today_work").val() == "") {
            alert("할 일을 입력해주세요.");
            $("#main_today_work").focus();
            return false;
        }

        var fdata = new FormData();
        var obj = $(".input_write");
        fdata.append("work_flag", "2");

        fdata.append("contents", $("#main_today_work").val());
        fdata.append("mode", "works_write");


        $.ajax({
            type: "POST",
            data: fdata,
            contentType: false,
            processData: false,
            url: '/inc/works_process.php',
            success: function(data) {
                console.log(data);
                if (data) {
                    tdata = data.split("|");
                    if (tdata) {
                        var result = tdata[0];
                        var result1 = tdata[1];
                        var result2 = tdata[2];
                        if (result == "complete") {
                            /*if (confirm("업무가 등록 되었습니다.\n내용을 확인하시겠습니까?")) {
                                $("#main_today_work").val("");
                                location.href = "/todaywork/index.php";
                                return false;
                            } else {*/
                            alert("업무가 등록 되었습니다.");
                            $("#main_today_work").val("");


                            if (get_pagename() == "index.php") {
                                if ($("#mains_todaywork_cnt")) {
                                    $("#mains_todaywork_cnt").text(result2);
                                }
                            } else if (get_pagename() == "index.php") {
                                $(".rtl_list_in ul").append(result1);
                                $("#mains_todaywork").addClass("state_new");
                                $("#mains_todaywork button strong").text(result2);
                            }
                        }
                    }
                }
            }
        });
    });

    $(document).on("click","button[id^=profile_img_0]", function(){
        var val = $(this).val();
        $("#check_profile").val(val);
    });

    //프로필 케릭터 선택
    $("#tl_profile_bt").click(function() {

        var val;

        val = $("#check_profile").val();

        var fdata = new FormData();
        fdata.append("mode", "profile_character");
        fdata.append("profile_no", val);

        $.ajax({
            type: "POST",
            data: fdata,
            contentType: false,
            processData: false,
            url: '/inc/main_process.php',
            success: function(data) {
                console.log(data);
                if (data) {
                    tdata = data.split("|");
                    if (tdata) {
                        var result = tdata[0];
                        var result2 = tdata[1];
                        if (result == "complete") {
                            // $("#profile_character_img").css("background-image", "url(" + result2 + ")");
                            $("#profile_character_img1").css("background-image", "url(" + result2 + ")");
                            // main_live_list();
                        }
                    }
                }
            }
        });


    });


    //프로필사진변경
    $("input[id='prof']").change(profile_img_preview);


    //프로필사진변경 디폴드
    $(document).on('click', '#character_default', function() {

        img = "/html/images/pre/img_prof_default.png";

        var fdata = new FormData();
        fdata.append("mode", "main_profile_change_default");

        $.ajax({
            type: "POST",
            data: fdata,
            contentType: false,
            processData: false,
            url: '/inc/main_process.php',
            success: function(data) {
                console.log(data);
                $("#profile_character_img").css("background-image", "url(" + img + ")");
                main_live_list();
            }
        });

    });



    $(document).on('click', '#live_user_list', function() {
        location.href = "/live/index.php";
    });


    //오늘업무로 이동
    $("#mains_todaywork").click(function() {

        if (GetCookie("user_id") != null) {
            location.href = "/todaywork/index.php";
        } else {
            $(".rew_layer_login").show();
        }
    });



    //$(".penalty_first").hide();
    //페널티 카드 발동

    $("#penalty_bt_01").click(function() {
        $("#penalty_first_01").hide();
        $("#penalty_layer_01").show();
        startTimer();

    });

    //오늘업무 페널티 카드 발동
    $("#penalty_bt_02").click(function() {
        $("#penalty_first_02").hide();
        $("#penalty_layer_02").show();
        startTimer();
    });


    //퇴근 페널티 카드 발동
    $("#penalty_bt_03").click(function() {
        $("#penalty_first_03").hide();
        $("#penalty_layer_03").show();
        startTimer();
    });


    //페널티 카드 닫기
    $(".pl_close button").click(function() {
        $(".penalty_layer").hide();
    });


    //페널티 이미지 올리기
    $("#pl_img_btn").click(function() {
        //$('#pl_file').click();
    });

    //페널티 이미지 미리보기
    $("#pl_img_preview, #pl_preview_01,#pl_preview_02,#pl_preview_03").click(function() {
        //var img = $(this).attr("src");
        var id = $(this).attr("id");
        if (id) {
            var img = $("#" + id + " img").attr("src");
            if (img) {
                $("#layer_penalty_img").attr("src", img);
                $(".layer_cha_image").show();
            }
        }
    });

    //페널티 수료증 다운로드
    $("#file_box_01,#file_box_02, #file_box_03").click(function() {

        var url = "/inc/file_download.php";
        var num = '0';
        var idx = $(this).attr("value");
        var params = { idx: idx, num: num };

        console.log(params);
        $.fileDownload(url, {
            httpMethod: "post",
            data: params,
            successCallback: function(d) {
                console.log(d);
            },
            failCallback: function(e) {
                console.log(e);
                return false;
            }
        });
    });


    //이미지 올리기
    $("input[id='pl_file_01'],input[id='pl_file_02'],input[id='pl_file_03']").click(function() {

        //로그인 했을경우
        if (GetCookie("user_id")) {
            var mains_info_uid = $("#mains_info_uid").val();
            //mains_info_uid = 'sadary0nate.com11';

            //본인만 이미지 올리기 가능함
            if (mains_info_uid != GetCookie("user_id")) {
                return false;
            }
        }
    });


    //이미지 올리기
    $("input[id='pl_file_01'],input[id='pl_file_02'],input[id='pl_file_03']").change(penalty_img_preview);


    //마우스오버
    $(document).on("mouseover", "#live_user_list", function() {
        $(this).css("cursor", "pointer");
    });

    //마우스오버
    $(document).on("mouseover", "#mains_todaywork", function() {
        $(this).css("cursor", "pointer");
    });


    //지각 페널티 카드
    $("#pf_close_01").click(function() {

        if ($("#more_01").is(":checked") == true) {
            if (GetCookie('pf_close_01') == null) {
                $("#penalty_first_01").show();
                setCookie('pf_close_01', '1', '1');
            }
        }

        var fdata = new FormData();
        fdata.append("mode", "penalty_close");
        fdata.append("kind_flag", "0");
        $.ajax({
            type: "post",
            async: false,
            data: fdata,
            contentType: false,
            processData: false,
            url: '/inc/main_process.php',
            success: function(data) {
                console.log("data :: " + data);
                if (data == "complete") {
                    //location.reload();
                    //닫기
                    $("#penalty_first_01").hide();
                }
            }
        });
    });

    //오늘업무 페널티 카드
    $("#pf_close_02").click(function() {
        if ($("#more_02").is(":checked") == true) {
            if (GetCookie('pf_close_02') == null) {
                $("#penalty_first_02").show();
                setCookie('pf_close_02', '1', '1');
            }
        }
        var fdata = new FormData();
        fdata.append("mode", "penalty_close");
        fdata.append("kind_flag", "1");
        $.ajax({
            type: "post",
            async: false,
            data: fdata,
            contentType: false,
            processData: false,
            url: '/inc/main_process.php',
            success: function(data) {
                console.log("data :: " + data);
                if (data == "complete") {
                    //location.reload();
                    //닫기
                    $("#penalty_first_02").hide();
                }
            }
        });
    });

    //퇴근 페널티 카드
    $("#pf_close_03").click(function() {
        if ($("#more_03").is(":checked") == true) {
            if (GetCookie('pf_close_03') == null) {
                $("#penalty_first_03").show();
                setCookie('pf_close_03', '1', '1');
            }
        }
        var fdata = new FormData();
        fdata.append("mode", "penalty_close");
        fdata.append("kind_flag", "2");
        $.ajax({
            type: "post",
            async: false,
            data: fdata,
            contentType: false,
            processData: false,
            url: '/inc/main_process.php',
            success: function(data) {
                console.log("data :: " + data);
                if (data == "complete") {
                    //location.reload();
                    //닫기
                    $("#penalty_first_03").hide();
                }
            }
        });
    });



    $("#rew_cp_btn").click(function() {
        console.log("등록하기");

        var cp1 = $("#cp1").val();
        var cp2 = $("#cp2").val();
        var cp3 = $("#cp3").val();
        var cp4 = $("#cp4").val();
        var cp5 = $("#cp5").val();
        var cp6 = $("#cp6").val();

        var fdata = new FormData();
        fdata.append("mode", "cp_new");
        fdata.append("cp1", cp1);
        fdata.append("cp2", cp2);
        fdata.append("cp3", cp3);
        fdata.append("cp4", cp4);
        fdata.append("cp5", cp5);
        fdata.append("cp6", cp6);

        $.ajax({
            type: "post",
            async: false,
            data: fdata,
            contentType: false,
            processData: false,
            url: '/inc/main_process.php',
            success: function(data) {
                console.log("data :: " + data);
                if (data == "complete") {
                    location.reload();
                }
            }
        });

    });


    //좋아요 클릭 레이어
    $(document).on("click", ".jjim_01,.jjim_02,.jjim_03,.jjim_04,.jjim_05,.jjim_06", function() {

        if (GetCookie("user_id") == 'sadary0@nate.com') {
            var val = $(".jjim_01").attr("value");
            var send_user = $(".rew_mains_info_name strong").text().replace("님, 안녕하세요!", "");
            var send_user_team = $(".rew_mains_info_name span").text();
            var send_user_imgs = $(".tl_prof_img").css("background-image").replace(/^url\(['"](.+)['"]\)/, '$1');;
            var send_userid = $("#mains_info_uid").val();


            $("#jg_name_user").text(send_user);
            $("#jg_name_team").text(send_user_team);
            $("#jg_user_img").css("background-image", "url(" + send_user_imgs + ")");


            $("#jt_name_user").text(send_user);
            $("#jt_name_team").text(send_user_team);
            $("#jt_user_img").css("background-image", "url(" + send_user_imgs + ")");

            if (send_userid) {
                $("#send_userid").val(send_userid);
            }

            var fdata = new FormData();
            fdata.append("mode", "jg_graph_list");
            fdata.append("send_userid", send_userid);

            $.ajax({
                type: "post",
                async: false,
                data: fdata,
                contentType: false,
                processData: false,
                url: '/inc/lives_process.php',
                success: function(data) {
                    console.log(data);
                    if (data) {
                        var tdata = data.split("|");
                        if (tdata) {
                            var result = tdata[0];
                            var cnt = tdata[1];
                            $("#jg_graph_list").html(result);
                            $("#jg_user_heart_all span").text(cnt);

                            //그래프 높이설정
                            var jg_all = $(".jg_user_heart_all").text();
                            $(".jg_graph_list li").each(function() {
                                var jg_txt = $(this).find("span").text();
                                var jg_height = jg_txt / jg_all * 160;
                                $(this).find("strong").css({ "height": jg_height });
                            });

                            $("#jjim_graph").show();
                        }
                    }
                }
            });
        }
    });


    //좋아요 레이어 닫기
    $("#jg_close").click(function() {
        $("#jjim_graph").hide();
    });









    //비밀번호 수정하기
    $("#member_passed_btn").click(function() {

        if (!$("#z11").val()) {
            alert("비밀번호를 입력해주세요.");
            $("#z11").focus();
            return false;
        }

        if (!$("#z12").val()) {
            alert("비밀번호를 입력해주세요.");
            $("#z12").focus();
            return false;
        }

        if ($("#z11").val() != $("#z12").val()) {
            alert("비밀번호가 일치하지 않습니다.");
            $("#z12").focus();
            return false;
        }


        if (confirm("입력하신 정보로 수정 하시겠습니까?")) {

            var fdata = new FormData();
            fdata.append("mode", "member_pass_edit");
            fdata.append("passwd1", $("#z11").val());
            fdata.append("passwd2", $("#z12").val());

            $.ajax({
                type: "POST",
                data: fdata,
                contentType: false,
                processData: false,
                url: '/inc/main_process.php',
                success: function(data) {
                    //console.log(data);
                    if (data == "not") {
                        alert("입력한 비밀번호가 일치 하지 않습니다.");
                        return false;
                    } else if (data == "ok") {
                        alert("비밀번호가 수정되었습니다.");
                        location.href = '/team/';
                        return false;
                    } else if (data == "same") {
                        alert("현재 비밀번호와 동일합니다.\n비밀번호를 다시 입력해 주세요.");
                        $("#z11").focus();
                        return false();
                    }
                }
            });
        }
    });


    //타임라인 읽지 않은 게시물
    $(document).on("click", ".rtl_list_old li", function() {
        //v = $(this).val();
        //id = $(this).attr("id");
        index = $(this).index();

        //val = $(this).parent().find(".rtl_list_box dd").eq(index).text();

        val = $(this).parent().find("li").eq(index).attr("value");

        //location.href = "/todaywork/index.php?workdate=" + val;

        /*setTimeout(function() {
            console.log("44444");
            $("#work_wdate").val(val);
            date_change();
            works_list();
        }, 2000);*/

        //actsubmit_post("/todaywork/index.php", "read_date", val);

        var fdata = new FormData();
        fdata.append("mode", "read_date");
        fdata.append("read_date", val);
        $.ajax({
            type: "post",
            async: false,
            data: fdata,
            contentType: false,
            processData: false,
            url: '/inc/main_process.php',
            success: function(data) {
                location.href = "/todaywork/index.php";
            }
        });
    });



    //공용코인
    if ($(".rew_mains_company_coin p strong").attr("value")) {
        var mains_company_coin = $(".rew_mains_company_coin p strong").attr("value");
        setTimeout(function() {
            $(".rew_mains_company_coin p strong").text(mains_company_coin);
            $(".rew_mains_company_coin p strong").counterUp({
                delay: 35,
                time: 1600
            });
        }, 100);
    }





    //역량 할당코인 닫기
    $(".qna_layer_01").click(function() {
        $(this).hide();
    });

    //좋아요 할당코인 닫기
    $(".qna_layer_02").click(function() {
        $(this).hide();
    });



    //메인 좋아요 지표 닫기
    // $(document).on("click", "#btn_mains_heart_close", function() {

    //     if (confirm("좋아요 내역을 삭제하시겠습니까?")) {
    //         var fdata = new FormData();
    //         var val = $(this).val();

    //         //$(this).parent().remove();
    //         fdata.append("val", val);
    //         fdata.append("mode", "main_like_list");
    //         $.ajax({
    //             type: "post",
    //             async: false,
    //             data: fdata,
    //             contentType: false,
    //             processData: false,
    //             url: '/inc/main_process.php',
    //             success: function(data) {
    //                 //console.log(data);
    //                 if (data) {
    //                     tdata = data.split("|");
    //                     if (tdata) {
    //                         var result = tdata[0];
    //                         var html = tdata[1];
    //                         if (result == "complete") {
    //                             $("#rew_mains_heart_list ul").html(html);
    //                         }
    //                     }
    //                 }
    //             }
    //         });
    //     }
    // });

    //수정된 메인 좋아요 지표 닫기
    $(document).on("click", ".heart_close", function() {

            var fdata = new FormData();

            // 일반 좋아요 idx값
            // var h_val = $(this).closest('.heart_user_hover').find('.send_heart button').attr('id');
            // var heart_idx_val = h_val.replace("mains_new_heart_list_", "");
            // 챌린지 바로가기 idx 값
             var c_val = $(this).closest('.heart_user_hover').find('.chall_all').attr('value');
             
             var m_val = $(this).closest('.heart_me_hover').attr('value');
            if(c_val){
                var heart_idx = c_val;  
            }else if(m_val){
                var heart_idx = m_val;  
            }else{
                var h_val = $(this).closest('.heart_user_hover').find('.send_heart button').attr('id');
                var heart_idx_val = h_val.replace("mains_new_heart_list_", "");
                var heart_idx = heart_idx_val;
            }
            fdata.append("val", heart_idx);
            fdata.append("mode", "main_like_list");
            $.ajax({
                type: "post",
                async: false,
                data: fdata,
                contentType: false,
                processData: false,
                url: '/inc/main_process_new.php',
                success: function(data) {
                    //console.log(data);
                    if (data) {
                        console.log("성공");
                    }
                }
            });
    });
});


//메인 좋아요 보내기 (index1)
$(document).on("click", "button[id^='mains_heart_list']", function() {
    var id = $(this).attr("id");
    if (id) {
        var val = id.replace("mains_heart_list_", "");
        var name = $(this).parent().find("strong").text();
        // var name = $(".send_heart").attr("value");
        if (confirm(name + "에게 좋아요를 보내시겠습니까?")) {
            var fdata = new FormData();
            fdata.append("val", val);
            fdata.append("mode", "main_like_send");
            $.ajax({
                type: "post",
                async: false,
                data: fdata,
                contentType: false,
                processData: false,
                url: '/inc/main_process.php',
                success: function(data) {
                    console.log(data);
                    if (data) {
                        tdata = data.split("|");
                        if (tdata) {
                            var penalty = tdata[0];
                            var result = tdata[1];
                            var html = tdata[2];
                            if(penalty == "penalty"){
                                alert("패널티로 인하여 좋아요를 보낼 수 없습니다.");
                                location.reload();
                            }else{
                                if (result == "complete") {
                                    location.reload();
                                    //$("#rew_mains_heart_list ul").html(html);
                                }
                            }
                        }
                    }
                }
            });
        }
    }
});


//메인 좋아요 보내기 (index2)
$(document).on("click", "button[id^='mains_new_heart_list_']", function() {
    var id = $(this).attr("id");
    if (id) {
        var val = id.replace("mains_new_heart_list_", "");
        var event_class = $(this);
            var fdata = new FormData();
            fdata.append("val", val);
            fdata.append("mode", "main_like_send");
            $.ajax({
                type: "post",
                async: false,
                data: fdata,
                contentType: false,
                processData: false,
                url: '/inc/main_process_new.php',
                success: function(data) {
                    console.log(data);
                    if (data) {
                        tdata = data.split("|");
                        if (tdata) {
                            var penalty = tdata[0];
                            var result = tdata[1];
                            var cnt = tdata[2];
                            var html = tdata[3];
                            // console.log(result);
                            // console.log(tdata);
                            
                            if(penalty == "penalty"){
                                alert("패널티로 인하여 좋아요를 보낼 수 없습니다.");
                                location.reload();
                            }else{
                                if (result == "complete") {
                                        console.log("성공");
                                    if(cnt == 0){
                                        setTimeout(() => {
                                            $(".rew_heart_area_in ul").html(html);
                                        }, 3000)
                                    }
                                    
                                }
                            }
                        }
                    }
                }
            });
    }
});

//메인 좋아요 보내기
$(document).on("click", "button[id^='mains_heart_me']", function() {
    alert("본인에게는 좋아요를 보낼 수 없습니다.");
    return false;
});

//메인 좋아요 보내기
$(document).on("click", "button[id^='mains_heart_today']", function() {
    alert("좋아요를 이미 보냈습니다.");
    return false;
});

$(document).on("click", "button[id^='item_img_0']", function(){
    var val = $(this).val();
    $("#img_idx").val(val);

    var fdata = new FormData();

    fdata.append("mode", "item_img_layer");
    fdata.append("img_idx",val);
    
    $.ajax({
        type: "post",
        async: false,
        data: fdata,
        contentType: false,
        processData: false,
        url: '/inc/main_process.php',
        success: function(data){
            console.log(data);
            $(".is_layer").html(data);
            $(".item_prof").hide();
            $(".is_layer").show();
        }
    });

});

$(document).on("click", ".is_layer_btn_off", function(){
    $(".is_layer").hide();
    $(".item_prof").show();
});

$(document).on("click", "#item_img_buy", function(){

    if(confirm("아이템을 구매 하시겠습니까?") == false){
        return false;
    }

    var val = $("#img_idx").val();
    var fdata = new FormData();

    fdata.append("mode","item_img_buy");
    fdata.append("img_idx",val);

    $.ajax({
        type: "post",
        async: false,
        data: fdata,
        contentType: false,
        processData: false,
        url: '/inc/main_process.php',
        success: function(data){
            console.log(data);
            if(data == "exp"){
                alert("코인이 부족합니다.");
                return false;
            }else if(data == "complete"){
                alert("구매가 완료되었습니다.");
                location.reload();
                return false;
            }
        }
    });
});


let isPuase = false;
let timers;

//페널티 이미지 미리보기
function penalty_img_preview(event) {
    var input = this;
    var id = $(this).attr("id");
    var no = id.replace("pl_file_", "");
    // console.log(input.files)

    if (input.files && input.files.length && no) {
        var reader = new FileReader();
        this.enabled = false;
        var str = $(this).val();
        var fileName = str.split('\\').pop().toLowerCase();

        //1. 확장자 체크
        var ext = fileName.split('.').pop().toLowerCase();
        if ($.inArray(ext, ['jpg', 'jpeg', 'gif', 'png']) == -1) {
            alert("이미지 파일만 등록 가능합니다.\n파일을 확인 해주세요.");
            return false;
        }

        reader.onload = (function(e) {
            //console.log(e)
            //let img = new Image();
            //img.src = e.target.result;
            //console.log( img.width);

            /*console.log(input.files[0]['name']);
            var img = $("#file_" + no);
            console.log(img.width);*/
            $("#pl_preview_" + no).html(['<img src="', e.target.result, '">'].join(''));
        });
        reader.readAsDataURL(input.files[0]);

        var pl_file = $("input[id='pl_file_" + no + "']")[0].files[0];
        var fdata = new FormData();
        fdata.append("kind", no);
        fdata.append("mode", "penalty_img_upload");
        fdata.append("pl_file", pl_file);
        $.ajax({
            type: "post",
            async: false,
            data: fdata,
            contentType: false,
            processData: false,
            url: '/inc/penalty_process.php',
            success: function(data) {
                console.log("data :: " + data);
                if (data == "complete") {
                    var comp_img = "/html/images/pre/img_comp.png";
                    $("#pl_img_comp_" + no + " img").attr("src", comp_img);
                    $("#btn_penalty_banner_" + no).hide();
                    $("#penalty_comp_" + no).show();
                    setTimeout(function() {
                        $("#pl_img_comp_" + no + " img").show();
                        $(".label_file span").html("<span>이미지 재등록</span>");
                    }, 10);
                }
            }
        });
    }
}



//파일명 체크
function check_filename(file) {

    //1. 확장자 체크
    var ext = file.split('.').pop().toLowerCase();
    if ($.inArray(ext, ['jpg', 'jpeg', 'gif', 'png']) == -1) {
        alert("이미지 파일만 등록 가능합니다.\n파일을 확인 해주세요.");
        return false;
    }


    /*if ($.inArray(ext, ['bmp', 'hwp', 'jpg', 'pdf', 'png', 'xls', 'zip', 'pptx', 'xlsx', 'jpeg', 'doc', 'gif']) == -1) {
        alert(ext + '파일은 업로드 하실 수 없습니다.');
    }
	*/

    //2. 파일명에 특수문자 체크
    var pattern = /[\{\}\/?,;:|*~`!^\+<>@\#$%&\\\=\'\"]/gi;
    if (pattern.test(file)) {
        //alert("파일명에 허용된 특수문자는 '-', '_', '(', ')', '[', ']', '.' 입니다.");
        alert('파일명에 특수문자를 제거해주세요.');
        return false;
    }
}


//업무종료 버튼
//$("#live_4_bt").click(function() {
function live_4_bt_func() {

    if ($(".rew_grid_onoff .rew_grid_onoff_in .onoff_04 em").hasClass("on") == true) {
        var live_4_switch = "false";
    } else if ($(".rew_grid_onoff .rew_grid_onoff_in .onoff_04 em").hasClass("on") == false) {
        var live_4_switch = "true";
    }

    var fdata = new FormData();
    fdata.append("mode", "live_4_change");
    fdata.append("status", live_4_switch);
    $.ajax({
        type: "post",
        async: false,
        data: fdata,
        contentType: false,
        processData: false,
        url: '/inc/main_process.php',
        success: function(data) {
            console.log("data :: " + data);
            if (data) {
                tdata = data.split("|");
                if (tdata) {
                    var result = tdata[0];
                    var result2 = tdata[1];
                    //console.log(result2);
                    if (result == "true") {
                        $(".onoff_04 em").text("퇴근(" + result2 + ")");
                        $(".onoff_01 em").text("출근");
                        main_live_list();
                    }
                }
            }
        }
    });

}


//카운트 정렬
$(document).on('click', '.option_count', function() {
    var val = $(this).attr("value");
    if (val) {
            var fdata = new FormData();
            fdata.append("mode", "main_live_list");
            fdata.append("category", val);

            $.ajax({
                type: "post",
                data: fdata,
                contentType: false,
                processData: false,
                //async: false,
                url: '/inc/main_process_new.php',
                success: function(data) {
                    if (data) {
                        console.log("data:::"+data);
                        var tdata = data.split("|");
                        if (tdata) {
                            var result = tdata[0];
                            $("#main_live_list").html(result);
                            $(".live_list .live_list_box").each(function() {
                                var tis = $(this);
                                setTimeout(function() {
                                    tis.addClass("slii");
                                }, 0);
                            });
                        }
                        }
                    }
            });
    }
});
//메인 라이브 리스트
function main_live_list() {

    var fdata = new FormData();
    fdata.append("mode", "main_live_list");

	var live_2_switch = $(".rew_grid_onoff .rew_grid_onoff_in .onoff_02 em").hasClass("on");
	var live_3_switch = $(".rew_grid_onoff .rew_grid_onoff_in .onoff_03 em").hasClass("on");

	fdata.append("live_2_switch", live_2_switch);
	fdata.append("live_3_switch", live_3_switch);
    $.ajax({
        type: "post",
        async: false,
        data: fdata,
        contentType: false,
        processData: false,
        url: '/inc/main_process_new.php',
        success: function(data) {
            console.log(data);
            if (data) {

                var tdata = data.split("|");
                if (tdata) {
                    var result = tdata[0];
                    $("#main_live_list").html(result);
                    $(".live_list .live_list_box").each(function() {
                        var tis = $(this);
                        setTimeout(function() {
                            tis.addClass("slii");
                        }, 0);
                    });

                }
            }
        }
    });

}



//챌린지 이미지 미리보기
function profile_img_preview(event) {
    var input = this;
    var id = $(this).attr("id");


    /*console.log(id);
    console.log(input.files);
    console.log(input.files.length);*/

    if (input.files && input.files.length) {
        var reader = new FileReader();
        this.enabled = false
        reader.onload = (function(e) {
            //console.log(e)
            //let img = new Image();
            //img.src = e.target.result;
            //console.log( img.width);

            /*console.log(input.files[0]['name']);
            var img = $("#file_" + no);
            console.log(img.width);*/
            // $("#profile_character_img").css("background-image", "url(" + e.target.result + ")");
            $("#profile_character_img1").css("background-image", "url(" + e.target.result + ")");
        });
        reader.readAsDataURL(input.files[0]);

        var fdata = new FormData();
        fdata.append("mode", "main_profile_change");
        fdata.append("files[]", $("input[id='prof']")[0].files[0]);
        $.ajax({
            type: "POST",
            data: fdata,
            contentType: false,
            processData: false,
            url: '/inc/main_process.php',
            success: function(data) {
                console.log("ddddd ::: " + data);
                // main_live_list();
            }
        });
    }
}


//챌린지 이미지 디폴트
function profile_img_default(event) {
    var input = this;
    var id = $(this).attr("id");
    /*console.log(id);
    console.log(input.files);
    console.log(input.files.length);*/

    console.log(23);
    if (input.files && input.files.length) {
        var reader = new FileReader();
        this.enabled = false
        reader.onload = (function(e) {
            //console.log(e)
            //let img = new Image();
            //img.src = e.target.result;
            //console.log( img.width);

            /*console.log(input.files[0]['name']);
            var img = $("#file_" + no);
            console.log(img.width);*/

            e.target.result = "/html/images/pre/img_prof_default.png";

            $("#profile_character_img").css("background-image", "url(" + e.target.result + ")");
        });
        reader.readAsDataURL(input.files[0]);

        var fdata = new FormData();
        fdata.append("mode", "main_profile_change_default");

        $.ajax({
            type: "POST",
            data: fdata,
            contentType: false,
            processData: false,
            url: '/inc/main_process.php',
            success: function(data) {
                main_live_list();
            }
        });
    }
}


function daily_penalty_time(t) {

    var timer = t;
    var hours, minutes, seconds;
    var interval = setInterval(function() {
        hours = parseInt(timer / 3600, 10);
        minutes = parseInt(timer / 60 % 60, 10);
        seconds = parseInt(timer % 60, 10);

        hours = hours < 10 ? "0" + hours : hours;
        minutes = minutes < 10 ? "0" + minutes : minutes;
        seconds = seconds < 10 ? "0" + seconds : seconds;

        //document.getElementById("time-hour").innerHTML = hours;
        //document.getElementById("time-min").innerHTML = minutes;
        //document.getElementById("time-sec").innerHTML = seconds;

        $(".pl_right_02 .pl_time strong").text(minutes + ":" + seconds);

        if (minutes == "00" && seconds == "00") {
            console.log("실패");
        }

        if (--timer < 0) {
            timer = 0;
            clearInterval(interval);
        }
    }, 1000);
}



function startTimer() {
    isPuase = false;
    timers = setInterval(function() {
        penalty_timer();
    });
}

function stopTimer() {
    clearInterval(timers);
    isPuase = true;
}

function penalty_timer() {

    if (!isPuase) {
        var now = new Date();
        var end = new Date(now.getFullYear(), now.getMonth(), now.getDate(), late_etime, 59, 59);
        var open = new Date(now.getFullYear(), now.getMonth(), now.getDate(), late_stime, 00, 00);

        if (late_stime >= '09') {

            if (GetCookie("user_id") == 'sadary0@nate.com') {
                var penalty_send = '2';

                //console.log("late_stime : " + late_stime);
                //console.log("late_etime : " + late_etime);
                if (late_stime >= '09') {
                    //    console.log("시 : " + now.getHours());
                    //    console.log("분 : " + now.getMinutes());
                    //    console.log("초 : " + now.getSeconds());
                    //    stopTimer();
                    //    return false;
                }

                late_etime = "23";
                late_stime = "09";
                var penalty_send = '1';
                var end = new Date(now.getFullYear(), now.getMonth(), now.getDate(), late_etime, 59, 59);
                var open = new Date(now.getFullYear(), now.getMonth(), now.getDate(), late_stime, 00, 00);

            } else {

                var penalty_send = '1';
                var end = new Date(now.getFullYear(), now.getMonth(), now.getDate(), late_etime, 59, 59);
                var open = new Date(now.getFullYear(), now.getMonth(), now.getDate(), late_stime, 00, 00);
            }




            var nt = now.getTime();
            var ot = open.getTime();
            var et = end.getTime();
            var obj = $(".pl_right_02 .pl_time strong");

            //console.log(nt + " > " + et);

            //var interval = setInterval(function() {
            if (nt < ot) {
                obj.fadeIn();
                //$("p.time-title").html("금일 오픈까지 남은 시간");
                sec = parseInt(ot - nt) / 1000;
                day = parseInt(sec / 60 / 60 / 24);
                sec = (sec - (day * 60 * 60 * 24));
                hour = parseInt(sec / 60 / 60);
                sec = (sec - (hour * 60 * 60));
                min = parseInt(sec / 60);
                sec = parseInt(sec - (min * 60));

                if (hour < 10) { hour = "0" + hour; }
                if (min < 10) { min = "0" + min; }
                if (sec < 10) { sec = "0" + sec; }

                //    $(".hours").html(hour);
                //    $(".minutes").html(min);
                //    $(".seconds").html(sec);

                obj.text(hour + ":" + min + ":" + sec);

            } else if (nt > et) {
                //$("p.time-title").html("금일 마감");
                //$(".time").fadeOut();

                //자동 알림발송
                var fdata = new FormData();
                fdata.append("penalty_send", penalty_send);
                fdata.append("mode", "penalty_send_alarm");
                $.ajax({
                    type: "POST",
                    data: fdata,
                    contentType: false,
                    processData: false,
                    url: '/inc/penalty_process.php',
                    success: function(data) {
                        console.log(data);
                        if (data) {
                            tdata = data.split("|");
                            if (tdata) {
                                var result = tdata[0];

                                console.log(result);
                                if (result == "complete") {
                                    //obj.fadeOut();
                                    obj.text("종료");
                                    //$(".pl_upload .file_box input").attr("disabled", true);
                                    stopTimer();
                                    return;
                                } else if (result == "com") {
                                    obj.text("종료");
                                    // $(".pl_upload .file_box input").attr("disabled", true);
                                    stopTimer();
                                    return;
                                }
                            }
                        }

                    }
                });

            } else {
                obj.fadeIn();
                //$("p.time-title").html("금일 마감까지 남은 시간");
                sec = parseInt(et - nt) / 1000;
                day = parseInt(sec / 60 / 60 / 24);
                sec = (sec - (day * 60 * 60 * 24));
                hour = parseInt(sec / 60 / 60);
                sec = (sec - (hour * 60 * 60));
                min = parseInt(sec / 60);
                sec = parseInt(sec - (min * 60));

                if (hour < 10) { hour = "0" + hour; }
                if (min < 10) { min = "0" + min; }
                if (sec < 10) { sec = "0" + sec; }

                //$(".hours").html(hour);
                //$(".minutes").html(min);
                //$(".seconds").html(sec);
                obj.text(hour + ":" + min + ":" + sec);
            }
        }
    }
}

//최초 로그인 레이어
function first_login_time() {
    var fdata = new FormData();
    var mode = "first_login";
    fdata.append("mode", mode);
    var url = "/inc/main_process.php";
    $.ajax({
        type: "POST",
        data: fdata,
        contentType: false,
        processData: false,
        url: url,
        success: function(data) {
            $("#layer_lw_time").html(data);
            $("#layer_work").show()
        }
    });
}

//setInterval(penalty_timer, 10);

//패널티 카드 확인 버튼
$(document).on("click",".ok_btn",function(){
    kind = $(this).attr("id");
    // count = $(this).parent().find(".penalty").val();
    // console.log(count);

    // return false;
    // if(count >= 3){
    //     var fdata = new FormData();
    //     fdata.append("mode","penalty_on");
    //     fdata.append("kind",kind);
    //     $.ajax({
    //         type: "POST",
    //         data: fdata,
    //         contentType: false,
    //         processData: false,
    //         url: "/inc/process.php",
    //         success: function(data) {
    //             console.log(data);
    //         }
    //     });
    // }
    if(kind == "incount"){
        location.replace("/team/");
    }else{
        $(this).closest(".layer_penalty").hide();
    }
    return false;
});
