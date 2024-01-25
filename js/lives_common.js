$(function() {

	
    $('#datepickers-container').css('z-index', 9999);

    $("#lr_input").attr("maxlength", 10);

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


	$(".ldr_user_imgs,.ldr_user_penalty").on("mouseenter", function () {
		$(this).css("cursor", "pointer");
	});


	$(".rew_now_tab button").on("mouseenter", function () {
		$(this).css("cursor", "default");
	});


    //라이브 정렬 셀렉트박스 - 마우스 오버
    $("#rew_live_sort_list").hover(function() {
        $("#rew_live_sort_list").addClass("on");
    });

    //라이브 정렬 셀렉트박스 - 마우스 벗어날때
    $("#rew_live_sort_list").mouseleave(function() {
        $("#rew_live_sort_list").removeClass("on");
    });

    $("input[id=team_input]").attr("autocomplete", "off");


    //라이브 정렬
    $("#rew_live_sort_list ul li button").click(function() {
        var val = $(this).val();
        var page = 1;
        var page_count = parseInt($("#page_count").val());
        var live_sort = new Array();
        live_sort["all"] = '전체보기';
        live_sort["on"] = '접속자보기';
        live_sort["off"] = '미접속자보기';

        if (val) {
            if (live_sort[val]) {
                $("#rew_live_sort").val(val);
                $("#rew_live_sort").text(live_sort[val]);

                var fdata = new FormData();
                fdata.append("mode", "lives_index_list_new");
                fdata.append("list_rank", val);
                fdata.append("gp", page);

                $.ajax({
                    type: "post",
                    data: fdata,
                    contentType: false,
                    processData: false,
                    //async: false,
                    url: '/inc/lives_process.php',
                    success: function(data) {
                        console.log(data);
                        if (data) {
                            var tdata = data.split("|");
                            if (tdata) {

                                if ($("#input_index_search_new").val()) {
                                    $("#input_index_search_new").val("");
                                }
                                var result = tdata[0];
                                var listallcnt = tdata[1];
                                var listcnt = tdata[2];
                                var lastcnt = tdata[3];
                                console.log(lastcnt);
                                $("#pageno").val(page);
                                console.log($("#pageno").val(page));
                                $("#page_count").val(parseInt(listcnt));
                               
                                //$("#ldr").parent().remove();
                                //$("#ldr").find("ldr_li").remove();
                                //$('#ldr').load(location.href + ' #ldr');
                                if (val != 'off') {
                                    $("#live_ldr_me").show();
                                } else {
                                    $("#live_ldr_me").hide();
                                }

                                $("#ldr .ldr_li").remove();
                                $("#ldr").append(result);
                                $(".ldr_li").trigger("click");

                                // $("#lives_list_cnt").html("<span>" + listallcnt + "</span>(" + listcnt + ")");

                                //더보기 버튼
                                setTimeout(function () {
                                    if (page >= lastcnt) {
                                    $(".live_more").hide();
                                    } else {
                                    $(".live_more").show();
                                    }
                                }, 10);
                            }
                        }
                    }
                });
            }
        }
    });

//카운트 정렬
$(document).on('click', '.option_count', function() {
    var val = $(this).attr("value");
    var page = 1;
    var page_count = parseInt($("#page_count").val());
    console.log(val);
    var buttonOn = $(this);
    if (val) {
            $("#rew_live_sort").val(val);

            var fdata = new FormData();
            fdata.append("mode", "lives_index_list_new");
            fdata.append("category", val);
            fdata.append("gp", page);

            $.ajax({
                type: "post",
                data: fdata,
                contentType: false,
                processData: false,
                //async: false,
                url: '/inc/lives_process.php',
                beforeSend: function () {
                    $('.rewardy_loading_01').css('display', 'block');
                },
                success: function(data) {
                    console.log(data);
                    if (data) {
                        var tdata = data.split("|");
                        if (tdata) {

                            if ($("#input_index_search_new").val()) {
                                $("#input_index_search_new").val("");
                            }
                            var result = tdata[0];
                            var listallcnt = tdata[1];
                            var listcnt = tdata[2];
                            var lastcnt = tdata[3];
                            $("#pageno").val(page);
                            $("#page_count").val(parseInt(listcnt));
                            if (val != 'off') {
                                $("#live_ldr_me").show();
                            } else {
                                $("#live_ldr_me").hide();
                            }

                            var meet = $("#meet").val();
                            var early = $("#outing").val();
                            var business = $("#business").val();
                            var rest = $("#rest").val();
                            var all = $("#all").val();
                            $("#ldr .ldr_li").remove();
                            $("#ldr").append(result);
                            $(".ldr_li").trigger("click");

                            //더보기 버튼
                            setTimeout(function () {
                                if (page >= lastcnt || page >= meet || page >= early || page >= business || page >= rest || page >= all) {
                                $(".live_more").hide();
                                } else {
                                $(".live_more").show();
                                }
                            }, 10);

                            $('.rewardy_loading_01').css('display', 'none');
                        }
                    }
                }
            });
    }
});

    //파티 참여하기
    //$(".lp_bottom button").click(function() {
	$("#plus_idx").click(function() {

        if ($(this).hasClass("btn_on")) {
            var me_img = $(".ldr_me .ldr_user_img .ldr_user_imgs").css("background-image").replace(/^url\(['"](.+)['"]\)/, '$1');
            var me_user = $(".ldr_me .ldr_user_desc .ldr_user_name").text();
            var me_team = $(".ldr_me .ldr_user_desc .ldr_user_team").text();

            var val = $(this).val();
            if (val) {
                var fdata = new FormData();
                fdata.append("mode", "project_user_add");
                fdata.append("project_idx", val);

                $.ajax({
                    type: "post",
                    data: fdata,
                    contentType: false,
                    processData: false,
                    url: '/inc/lives_process.php',
                    success: function(data) {
                        console.log(data);
                        var tdata = data.split("|");
                        if (tdata) {
                            var result = tdata[0];
                            var edate = tdata[1];

                            if (result == "over_step1") {
                                alert('이미 참여한 파티입니다.');
                                $("#layer_plus").hide();
                                return false;
                            } else if (result == "over_step2") {
                                alert('이미 참여한 파티입니다.');
                                $("#layer_plus").hide();
                                return false;
                            } else if (result == "complete") {

                                $("#layer_plus").hide();
                                $(".ldl_box_me").css("display", "inline-block");
                                $(".ldl_box_user ul li").removeClass(".ldl_box_me");

                                $(".ldl_box_user ul li").last().removeClass("ldl_box_me");
                                $(".ldl_box_me").last().addClass("ldl_me");
                                $("#ldl_box_time_" + val).text(edate + " 업데이트");
                                $("#ldl_box_out_" + val).show();
                                //$("#ldl_box_id_" + val).addClass("ldl_me");

                            }
                        }
                    }
                });
            }
        }
    });

    $(".live_list .live_list_box").each(function() {
        var live_th = $(this);
        var live_st = live_th.find(".live_list_today .live_list_today_count strong").text();
        var live_sp = live_th.find(".live_list_today .live_list_today_count span").text();
        var live_ca = live_st / live_sp;
        var live_co = 0;

        var email = live_th.find(".live_list_today .live_list_today_count").parent("button").val();
        //var live_hap = live_st / 3;
        //var live_ban = live_sp / 2;
        var live_ban = 0,
            result_ban = 0;

        var result_ca = parseFloat(live_ca).toFixed(2);
        //var result_ban = parseFloat(live_ban).toFixed(1);


        /*console.log("e : " + email + ", " + live_st + " / " + live_sp);
        console.log("st : " + Number(live_st) + ", " + result_ban);
        console.log("result_ca : " + result_ca + "");*/

        //숫자형일때
        if (isNaN(result_ca) == false) {
            if (live_st == live_sp) {
                live_co = '#3ac73b';
            } else {
                if (live_ban == live_st) {
                    live_co = '#f7cc07';
                } else {
                    if (result_ca >= 0.68) {
                        if (Number(live_st) >= result_ban) {
                            live_co = '#3ac73b';
                        } else {
                            live_co = '#ec132e';
                        }
                    } else if (result_ca >= 0.34 && result_ca <= 0.67) {
                        live_co = '#f7cc07';
                    } else if (result_ca <= 0.33) {
                        //if (Number(live_st) >= result_ban) {
                        //    live_co = '#f7cc07';
                        //} else {
                        live_co = '#ec132e';
                        //}
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

    $(".onoff_01 .btn_switch").click(function() {
        $(".onoff_04 .btn_switch").removeClass("on");
        $(".onoff_04 .btn_switch").prev("em").removeClass("on");
        // // $(this).addClass("on");
        // $(this).prev("em").addClass("on");
    });

    // $(".onoff_02 .btn_switch").click(function() {
    //     var switchon = $(this);
    //     if (switchon.hasClass("on")) {
    //         $(this).removeClass("on");
    //         $(this).prev("em").removeClass("on");
    //     } else {
    //         $(".onoff_03 .btn_switch").removeClass("on");
    //         $(".onoff_03 .btn_switch").prev("em").removeClass("on");
    //         $(".onoff_04 .btn_switch").removeClass("on");
    //         $(".onoff_04 .btn_switch").prev("em").removeClass("on");
    //         $(".onoff_01 .btn_switch").addClass("on");
    //         $(".onoff_01 .btn_switch").prev("em").addClass("on");
    //         $(this).addClass("on");
    //         $(this).prev("em").addClass("on");
    //     }
    // });

    $(".onoff_03 .btn_switch").click(function() {
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
                                            if(ff_class == "btn_ff_01"){
                                                ff_text = "최고의";
                                            }else if(ff_class == "btn_ff_02"){
                                                ff_text = "뿌듯한";
                                            }else if(ff_class == "btn_ff_03"){
                                                ff_text = "기분 좋은";
                                            }else if(ff_class == "btn_ff_04"){
                                                ff_text = "감사한";
                                            }else if(ff_class == "btn_ff_05"){
                                                ff_text = "재밌는";
                                            }else if(ff_class == "btn_ff_06"){
                                                ff_text = "수고한";
                                            }else if(ff_class == "btn_ff_07"){
                                                ff_text = "무난한";
                                            }else if(ff_class == "btn_ff_08"){
                                                ff_text = "지친";
                                            }else if(ff_class == "btn_ff_09"){
                                                ff_text = "속상한";
                                            }
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
                                    $("fl_area .fl_desc strong span").val(ff_text);
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
    //챌린지 레이어
    //$(".btn_cha").click(function() {
    $(document).on("click", "#btn_cha", function() {
        console.log("cl");
        $(".layer_challenge .report_cha .rew_cha_list_ul li").removeClass("sli");
        $(".layer_challenge").show();
        $(".layer_challenge .report_cha .rew_cha_list_ul li").each(function() {
            var tis = $(this);
            var tindex = $(this).index();
            setTimeout(function() {
                tis.addClass("sli");
            }, 500 + tindex * 100);
        });
    });


    $(document).on("mouseenter", ".live_user_state li .live_user_state_circle", function() {
        $(".layer_state").removeClass("on");
        $(this).next(".layer_state").addClass("on");
        $(".live_list_box").removeClass("zindex");
        $(this).closest(".live_list_box").addClass("zindex");
    });

    $(document).on("mouseleave", ".live_user_state li .live_user_state_circle", function() {
        $(".layer_state").removeClass("on");
        $(".live_list_box").removeClass("zindex");
    });


    //역량지표 닫기
    $(".rl_close").click(function() {
        $("#radar_layer").hide();
    });


    //레이어 닫기
    $(".layer_result .layer_close button").click(function() {
        $(".layer_result").hide();
    });


    $(".layer_report .layer_close button").click(function() {
        $(".layer_report").hide();
        $(".layer_report").css({ opacity: 0 });
    });


    $(".layer_challenge .layer_close button").click(function() {
        $(".layer_challenge").hide();
        $(".layer_challenge .report_cha .rew_cha_list_ul li").removeClass("sli");
    });


    //오늘업무 닫기
    $(".layer_today .layer_close button").click(function() {

        if ($("#input_user_search").val()) {
            $("#input_user_search").val("");
        }

        if ($("#lives_date").val() != today_date()) {
            $("#lives_date").val(today_date());
        }

        $(".layer_today").hide();
        $(".layer_today .report_cha .rew_cha_list_ul li").removeClass("sli");
    });


    //라이브 리스트 속도
    $(".live_list .live_list_box").each(function() {
        var tis = $(this);
        var tindex = $(this).index();
        var bar_t = tis.find(".live_list_today_bar strong").text();
        var bar_b = tis.find(".live_list_today_bar span").text();
        var bar_w = bar_t / bar_b * 100;
        setTimeout(function() {
            tis.addClass("sli");
            tis.find(".live_list_today_bar strong").css({ width: bar_w + "%" });
        }, 500 + tindex * 100);
    });



    //라이브 상단 오늘업무 클릭 레이어
    $(document).on("click", ".btn_today", function() {
        $(".layer_today .tdw_list_li").removeClass("sli");

        var val = $(this).val();
        $("#user_email").val(val);

        todaywork_list();
        todaywork_user_list();

        setTimeout(function() {
            $(".layer_today").show();
            $(document).find("input[id=lives_date]").removeClass('hasDatepicker').datepicker();
        }, 200);

    });



    //라이브 오늘업무 클릭 레이어
    $(document).on("click", ".ldr_today_num", function() {
        var val = $(this).val();
        $("#user_email").val(val);
        $("#send_userid").val(val);
        todaywork_list();
        todaywork_user_list();

        setTimeout(function() {
            $(".layer_today").show();
            $(document).find("input[id=lives_date]").removeClass('hasDatepicker').datepicker();
        }, 200);

        $(".layer_today .layer_result_right").removeClass("pos_fix");
        $(".layer_result_list.desc_lr_02").css({ opacity: 0 });
        $(".layer_result_list.desc_lr_03").css({ opacity: 0 });
        $(".desc_lr_01 .tdw_list_li").removeClass("sli");
        $(".layer_today").show();
        $(".layer_result_tab li button").removeClass("on");
        $(".layer_result_tab li .btn_lr_01").addClass("on");
        $(".layer_result_list").hide();
        $(".layer_result_list.desc_lr_01").css({ opacity: 1 });
        $(".layer_result_list.desc_lr_01").show();
        $(".desc_lr_01 .tdw_list_li").each(function() {
            var tis = $(this);
            var tindex = $(this).index();
            setTimeout(function() {
                tis.addClass("sli");
            }, 600 + tindex * 200);
        });
    });


    //오늘업무
    $(document).on("click", ".btn_lr_01", function() {
        $(".layer_today .layer_result_right").removeClass("pos_fix");
        $(".layer_today .tdw_list_li").removeClass("sli");
        $(".layer_result_tab li button").removeClass("on");
        $(".layer_result_tab li .btn_lr_01").addClass("on");
        $(".layer_result_list").show();

        $(".layer_result_list.desc_lr_01").css({ opacity: 1 });
        $(".layer_result_list.desc_lr_01").show();
        $(".layer_today .tdw_list_li").each(function() {
            var tis = $(this);
            var tindex = $(this).index();
            setTimeout(function() {
                tis.addClass("sli");
            }, 600 + tindex * 200);
        });
    });



    //챌린지
    $(document).on("click", ".btn_lr_02", function() {
        $(".layer_today .layer_result_right").removeClass("pos_fix");
        $(".report_cha .rew_cha_list_ul li").removeClass("sli");
        $(".layer_result_tab li button").removeClass("on");
        $(".layer_result_tab li .btn_lr_02").addClass("on");
        $(".layer_result_list").hide();
        $(".layer_result_list.desc_lr_02").css({ opacity: 1 });
        $(".layer_result_list.desc_lr_02").show();
        $(".layer_today .report_cha .rew_cha_list_ul li").each(function() {
            var tis = $(this);
            var tindex = $(this).index();
            setTimeout(function() {
                tis.addClass("sli");
            }, 600 + tindex * 200);
        });
    });


    //역량평가
    $(document).on("click", ".btn_lr_03", function() {
        $(".layer_today .layer_result_right").removeClass("pos_fix");
        $(".layer_result_tab li button").removeClass("on");
        $(".layer_result_tab li .btn_lr_03").addClass("on");
        $(".layer_result_list").hide();
        $(".layer_result_list.desc_lr_03").css({ opacity: 1 });
        $(".layer_result_list.desc_lr_03").show();
    });


    //오늘업무, 좌측메뉴 회원클릭
    $(document).on("click", ".layer_today .layer_result_user_in ul li button", function() {
        var list_user = $(this).val();

        var fdata = new FormData();

        if (list_user) {
            $("#user_email").val(list_user);
            fdata.append("user_email", list_user);
            $("#send_userid").val(list_user);
        }

        //좌측 사용자 선택시 class 삭제처리
        if ($(".layer_today .layer_result_user .layer_result_user_in ul li button").hasClass("on") == true) {
            $(".layer_today .layer_result_user .layer_result_user_in ul li button").removeClass("on");
        }
        //좌측 사용자 선택시 class on
        $(this).addClass("on");

        if ($(this).find(".user_name strong").text()) {
            var send_user = $(this).find(".user_name strong").text();

            $(".jf_box_in .jf_top strong span").text(send_user);
            $(".jl_box_in .jl_top strong span").text(send_user);
        }

        fdata.append("lives_date", $("#lives_date").val());
        fdata.append("mode", "todaywork_list");

        $.ajax({
            type: "post",
            data: fdata,
            contentType: false,
            processData: false,
            url: '/inc/lives_process.php',
            success: function(data) {
                var fdata = data.split("|");
                result = fdata[0];
                date = fdata[1];
                console.log(data);
                if (data) {

                    //if ($(".btn_lr_01").hasClass("on") == true) {
                    //$(".btn_lr_01").addClass("on");
                    //}
                    $("#todaywork_zone_list").html(result); 
                    $(document).find("input[id=lives_date]").removeClass('hasDatepicker').datepicker();

                    $(".layer_today .tdw_list_li").each(function() {
                        var tis = $(this);
                        var tindex = $(this).index();
                        setTimeout(function() {
                            tis.addClass("sli");
                        }, 500 + tindex * 100);
                    });
                }
            }
        });

    });


    //달력클릭
    $(document).on("click", "#lives_date", function() {
        $(this).datepicker({
            dateFormat: 'yyyy.mm.dd'
        });
    });


    //라이브 오늘업무 - 검색
    $(".layer_result_search_box input").bind("input keyup", function(e) {
        var id = $(this).attr("id");
        var input_val = $(this).val();
        if (input_val) {
            if (e.keyCode == 13) {
                $(".layer_result_search_box button").trigger("click");
                return false;
            }
        } else {
            todaywork_user_list();
            return false;
        }
    });



    //라이브 메인 검색
    $("#input_index_search").bind("input keyup", function(e) {
        var input_val = $(this).val();
        if (input_val) {
            if (e.keyCode == 13) {
                $(".rew_live_search_box button").trigger("click");
                return false;
            }
        } else {
            lives_index_list();
            return false;
        }
    });

    //라이브 메인 검색
    // $("#input_index_search_new").bind("keyup", function(e) {
    // });

    $(document).on("keyup", "#input_index_search_new", function(e){
        var input_val = $(this).val();
		var ldr_li_len = $(".ldr_li").length;
		var chall_user_cnt = $("#chall_user_cnt").val();
        if (input_val) {
            if (e.keyCode == 13) {
                
                $(".rew_live_search_box button").trigger("click");
                return false;
            }
        } else {

			if($(".tdw_list_none")){
				$(".tdw_list_none").remove();
			}
            lives_index_list_new();
			if ( ldr_li_len < chall_user_cnt ){
				if (GetCookie("user_id") == 'sadary0@nate.com') {
					lives_index_list_new_20230420();
				}
			}
            //lives_index_to_work();
            return false;
        }
    });

    //라이브 메인 검색버튼
    $("#lives_index_search_bt_new").click(function() {

        var page = 1;
        var pcount = parseInt($("#page_count").val());
        var page_count = parseInt($("#page_count").val());
        var lastcnt;

        if ($("#input_index_search_new").val() == "") {
            alert("이름, 부서명을 입력하세요.");
            $("#input_index_search_new").focus();
            return false;
        }

        var fdata = new FormData();
        var input_val = $("#input_index_search_new").val();
        fdata.append("mode", "lives_index_list_new");
        // alert(input_val);
        fdata.append("input_val", input_val);
        fdata.append("gp", page);
        $.ajax({
            type: "post",
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
                        var listallcnt = tdata[1];
                        var listcnt = tdata[2];
                        var lastcnt = tdata[3];
                        $("#pageno").val(page);
                        $("#page_count").val(parseInt(lastcnt));
                        $("#ldr").html(result);
						$(".ldr_li").trigger("click");
                        // $("#lives_list_cnt").html("<span>" + listallcnt + "</span>(" + listcnt + ")");
                        //더보기 버튼
                        setTimeout(function () {
                            if (page >= lastcnt) {
                            $(".live_more").hide();
                            } else {
                            $(".live_more").show();
                            }
                        }, 10);
                    }
                }
            }
        });
    });

    //라이브 오늘업무 레이어 좌측 사용자 검색버튼
    $("#lives_search_bt").click(function() {
        if ($("#input_user_search").val() == "") {
            alert("이름, 부서명을 입력하세요.");
            $("#input_user_search").focus();
            return false;
        } else {

            var fdata = new FormData();
            var input_val = $("#input_user_search").val();
            var lives_date = $("#lives_date").val();


            fdata.append("mode", "todaywork_user_list");
            fdata.append("input_val", input_val);
            fdata.append("lives_date", lives_date);

            $.ajax({
                type: "post",
                data: fdata,
                contentType: false,
                processData: false,
                url: '/inc/lives_process.php',
                success: function(data) {
                    console.log(data);
                    if (data) {
                        $(".layer_result_user .layer_result_user_in ul").html(data);
                    }
                }
            });
        }
    });



    //이전달 선택
    $(document).on("click", "#prev_wdate", function() {
        todaywork_list('prev');

    });


    //다음달 선택
    $(document).on("click", "#next_wdate", function() {
        todaywork_list('next');
    });


    //업무시작 버튼
    $(document).on("click", "#live_1_bt", function(){
            if($(this).hasClass("on")){
                var switch_btn = "off";
                console.log("퇴근");
                $(this).removeClass("on");
            }else{
                var switch_btn = "on";
                console.log("출근");
                $(this).addClass("on");
            }

            var live_1_switch = switch_btn;
            console.log(live_1_switch);
            var fdata = new FormData();
            fdata.append("mode", "live_1_change");
            fdata.append("status", live_1_switch);
            $('.rewardy_loading_01').css('display', 'block');
            $.ajax({
                type: "post",
                async: false,
                data: fdata,
                contentType: false,
                processData: false,
                url: '/inc/lives_process.php',
                 success: function(data) {
                    console.log("data :: " + data);
                    $('.rewardy_loading_01').css('display', 'none');
                    var tdata = data.split("|");
                    if (tdata) {
                        stack = tdata[0];
                        work = tdata[1];
                        outcount = tdata[2];
                        chall = tdata[3];
                        incount = tdata[4];
                        result = tdata[5];
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
                            return false;
                          }else{
                            location.replace("/live/index.php");
                        }
                    }
                }
            });
    });

    //패널티 카드 확인 버튼
    $(document).on("click",".ok_btn",function(){
        kind = $(this).attr("id");
        console.log(kind);
        if(kind == "incount"){
            location.href="/live/index.php";
        }else{
            $(this).closest(".layer_penalty").hide();
        }
        return false;
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
            url: '/inc/lives_process.php',
            success: function(data) {
                //console.log("data :::: " + data);
                if (data) {
                    tdata = data.split("|");
                    if (tdata) {
                        var result = tdata[0];
                        if (result == "true") {
                            lives_index_list_new();
                        }
                    }
                }
            }
        });

    });


   //회의 버튼
//    $(document).on("click", "#live_3_bt", function(){
//         if($(this).hasClass("on")){
//             if(confirm("회의를 종료하시겠습니까?")){
                
//                 var email = $("#user_email").val();
//                 var fdata = new FormData();

//                 fdata.append("mode", "work_state_off");
//                 fdata.append("email", email);

//                 $.ajax({
//                             type: "post",
//                             async: false,
//                             data: fdata,
//                             contentType: false,
//                             processData: false,
//                             url: '/inc/lives_process.php',
//                             success: function(data) {
//                                 console.log("data :: " + data);
//                                 if (data) {
                                    
//                                     // $(this).removeClass("on");
//                                 }
//                             }
//                         });
                
//             }
//         }else{
//             $(".layer_memo").show();
//             $(this).addClass("on");
//         }
//     });
        $(document).on("click", "#live_3_bt", function(){
            var off_state = $(this);
            if($(this).hasClass("on")){
                if(confirm("회의를 종료하시겠습니까?")){
                    var email = $("#user_email").val();
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
    $('.layer_memo_submit').hover(function(){
        $(this).addClass("on");
    });

    $(document).on('mouseleave', '.layer_memo_submit', function(){
        $(this).removeClass("on");
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

    //회의 등록
    $(document).on("click", ".layer_meet_submit", function(){
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
                        alert("시작시간은 종료시간보다 작아야합니다.");
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

    // $("#live_3_bt").click(function() {
    //     if ($(".rew_grid_onoff .rew_grid_onoff_in .onoff_03 em").hasClass("on") == true) {
    //         var live_3_switch = "true";
    //     } else if ($(".rew_grid_onoff .rew_grid_onoff_in .onoff_03 em").hasClass("on") == false) {
    //         var live_3_switch = "false";
    //     }

    //     var fdata = new FormData();
    //     fdata.append("mode", "live_3_change");
    //     fdata.append("status", live_3_switch);
    //     $.ajax({
    //         type: "post",
    //         async: false,
    //         data: fdata,
    //         contentType: false,
    //         processData: false,
    //         url: '/inc/lives_process.php',
    //         success: function(data) {
    //             console.log("data :: " + data);
    //             if (data) {
    //                 tdata = data.split("|");
    //                 if (tdata) {
    //                     var result = tdata[0];
    //                     var result2 = tdata[1];
    //                     //console.log(result2);
    //                     if (result == "true") {
    //                         lives_index_list_new();
    //                     }
    //                 }
    //             }
    //         }
    //     });

    // });



    //업무종료 버튼
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
                        // $("#live_1_bt").addClass("switch_ready");
                        $("#live_1_bt").removeClass("on");
                    }
                }
            }
        }
    });

}



    // $(document).on('click', '.business_submit', function(){
    //     var business_text = $(".live_business").val();

    //     console.log(business_text);
    // });
    //메모클릭
    $(document).on("click", "button[id^=tdw_list_memo]", function() {
        if ($(this).val()) {
            $("#work_idx").val($(this).val());
        }
        $(".layer_memo").show();
        $("#textarea_memo").focus();
    });


    //라이브 메모작성
    //$(".tdw_list .tdw_list_memo").click(function(){
    $(document).on("click", "#tdw_list_memo", function() {
        $(".layer_memo").show();
    });


    //라이브 메모 취소/닫기
    $(document).on("click", "#layer_memo_cancel", function() {
        if ($("#textarea_memo").val()) {
            $("#textarea_memo").val("");
        }
        $(".layer_memo").hide();
    });


    //메모삭제
    $(document).on("click", ".tdw_list .btn_memo_del", function() {

        if (confirm('작성한 업무 메모를 삭제하시겠습니까?')) {
            var val = $(this).val();
            var fdata = new FormData();
            fdata.append("mode", "work_comment_del");
            fdata.append("idx", val);
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
                        result = tdata[0];
                        result1 = tdata[1];
                        if (result == "complete") {
                            $("#comment_list_" + result1).remove();
                            todaywork_list_live();
                        }
                    }
                }
            });
        }
    });


    //일일업무 댓글 수정하기, 확인버튼
    $(document).on("click", "#btn_comment_submit", function() {
        var val = $(this).val();
        var fdata = new FormData();
        var contents = $("#tdw_comment_edit_" + val).val();
        var comment_list = $("#comment_list_" + val).parent().attr("id");
        if (comment_list) {
            var work_idx = comment_list.replace("tdw_list_memo_area_in_", "");
            console.log(work_idx);
            fdata.append("mode", "work_comment_edit");
            fdata.append("idx", val);
            fdata.append("contents", contents);

            $.ajax({
                type: "POST",
                data: fdata,
                contentType: false,
                processData: false,
                url: '/inc/works_process.php',
                success: function(data) {
                    // console.log(data);
                    if (data == "complete") {
                        $("#tdw_comment_edit_" + val).val("");
                        live_memo_list(work_idx);
                        return false;
                    }
                }
            });
        }
    });



    //일일업무 댓글 수정하기, 확인버튼
    $(document).on("click", "#btn_comment_submit_new", function() {
        var val = $(this).val();
        var fdata = new FormData();
        var contents = $("#tdw_comment_edit_" + val).val();
        var comment_list = $("#comment_list_" + val).parent().attr("id");
        if (comment_list) {
            var work_idx = comment_list.replace("memo_area_list_", "");

            fdata.append("mode", "work_comment_edit");
            fdata.append("idx", val);
            fdata.append("contents", contents);

            $.ajax({
                type: "POST",
                data: fdata,
                contentType: false,
                processData: false,
                url: '/inc/works_process.php',
                success: function(data) {
                    console.log(data);
                    if (data == "complete") {
                        $("#tdw_comment_edit_" + val).val("");
                        live_memo_list_new(work_idx);
                        return false;
                    }
                }
            });
        }
    });


    //라이브메모 글내용작성
    // $("#textarea_memo").bind("input", function(event) {
    //     var val = $(this).val();
    //     if (val) {
    //         if ($("#layer_memo_submit").hasClass("on") == false) {
    //             $("#layer_memo_submit").addClass("on");
    //             $("#layer_memo_submit_new").addClass("on");
    //             $("#textarea_memo").val(val);
    //         }
    //     } else {
    //         $("#layer_memo_submit").removeClass("on");
    //     }
    // });


    //라이브 메모등록하기버튼
    // $("#layer_memo_submit").click(function() {

    //     if (!$("#textarea_memo").val()) {
    //         alert("메모를 작성해주세요.");
    //         $("#textarea_memo").focus();
    //         return false;
    //     }

    //     var fdata = new FormData();
    //     var work_idx = $("#work_idx").val();
    //     fdata.append("mode", "work_comment");
    //     fdata.append("work_idx", work_idx);
    //     fdata.append("comment", $("#textarea_memo").val());

    //     //console.log("work_idx :::::: " + work_idx);

    //     $.ajax({
    //         type: "POST",
    //         data: fdata,
    //         contentType: false,
    //         processData: false,
    //         url: '/inc/works_process.php',
    //         success: function(data) {
    //             console.log(data);
    //             if (data == "complete") {
    //                 $("#textarea_memo").val("");
    //                 //works_list();
    //                 if ($("#textarea_memo").val()) {
    //                     $("#textarea_memo").val("");
    //                 }
    //                 $(".layer_memo").hide();


    //                 live_memo_list(work_idx);
    //                 //todaywork_list();
    //                 //todaywork_user_list();
    //                 return false;
    //             } else if (data == "logout") {
    //                 //	$(".t_layer").show();
    //                 //	return false;
    //             }
    //         }
    //     });

    // });


    //작성한 댓글 내용 클릭
    $(document).on("click", "span[id^=tdw_list_memo_conts_txt]", function() {

        var obj_edit = $("span[id^=tdw_list_memo_conts_txt]");
        var obj_edit_cnt = obj_edit.size();

        var id = $(this).attr("id");
        var no = id.replace("tdw_list_memo_conts_txt_", "");

        if (no) {
            var elem = $("textarea[id=tdw_comment_edit_" + no + "]");
            setTimeout(function() {
                var input = elem;
                var v = input.val();
                input.focus().val('').val(v);
            }, 50);
            $(this).next().next(".tdw_list_memo_regi").show();

            //해당 글내용 이외에 인풋박스 닫기
            for (i = 0; i < obj_edit_cnt; i++) {
                obj_edit_id = obj_edit.eq(i).attr("id");
                obj_edit_no = obj_edit.eq(i).attr("id").replace("tdw_list_memo_conts_txt_", "");
                if (no != obj_edit_no) {
                    $("#tdw_list_memo_regi_" + obj_edit_no).hide();
                }
            }
        }
    });


    //작성한 댓글 내용 취소
    $(document).on("click", ".tdw_list .btn_regi_cancel", function() {
        var val = $(this).val();
        if (val) {
            $("#tdw_comment_edit_" + val).val($("#tdw_list_memo_conts_txt_" + val).text());
        }
        $(this).closest(".tdw_list_memo_regi").hide();
    });





    //페널티 지각 미션 수행하기
    $(document).on("click", "#btn_penalty_banner01", function() {
        //console.log($(this).attr("id"));
        if ($(this).val()) {
            var fdata = new FormData();
            fdata.append("mode", "penalty_member");
            fdata.append("penalty_idx", $(this).val());
            console.log("penalty_idx : " + $(this).val());
            $.ajax({
                type: "post",
                data: fdata,
                async: false,
                contentType: false,
                processData: false,
                url: '/inc/lives_process.php',
                success: function(data) {
                    //console.log(data);
                    if (data) {
                        var tdata = data.split("|");
                        if (tdata) {
                            var result = tdata[0];
                            var imgurl = tdata[1];
                            var part = tdata[2];
                            $("#member_row_info_01").css("background-image", "url(" + imgurl + ")");
                            $("#pl_name_01").html("<strong>" + result + "</strong>" + part);

                            $("#penalty_first_01").hide();
                            $("#penalty_layer_01").show();
                            startTimer();
                        }
                    }
                }
            });
        }
    });

    //페널티 지각 미션 수행하기
    $(document).on("click", "#btn_penalty_banner02", function() {
        $("#penalty_first_02").hide();
        $("#penalty_layer_02").show();
        startTimer();
    });

    //페널티 지각 미션 수행하기
    $(document).on("click", "#btn_penalty_banner03", function() {
        $("#penalty_first_03").hide();
        $("#penalty_layer_03").show();
        startTimer();
    });

    //페널티 카드 닫기
    $(".pl_close button").click(function() {
        $(".penalty_layer").hide();
    });


    //페널티 이미지 올리기
    $("input[id='pl_file_01'],input[id='pl_file_02'],input[id='pl_file_03']").change(penalty_img_preview);



    //페널티 수료증 다운로드
    $("#file_box_01,#file_box_02,#file_box_03").click(function() {

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

    //멤버 찜하기
    $(document).on("click", ".mem_jjim_only", function() {

        if($(this).hasClass('star_on')){
            if (confirm("해당 멤버의 찜을 해제하시겠습니까?")) {
                    //mem_idx
                    var val = $(this).attr("value");
                    var fdata = new FormData();
        
                    fdata.append("mode", "mem_lives_like");
                    fdata.append("mem_idx", val);
                    var clickedElement = $(this);
                    $.ajax({
                        type: "POST",
                        data: fdata,
                        contentType: false,
                        processData: false,
                        url: '/inc/lives_process.php',
                        beforeSend: function () {
                            // $('.rewardy_loading_01').css('display', 'block');
                        },
                        success: function(data) {
                            clickedElement.removeClass("star_on");
                            // lives_index_list_new();
                            // $('.rewardy_loading_01').css('display', 'none');
                        }
                    });
                }
        }else{
            if (confirm("해당 멤버를 찜하시겠습니까?")) {
            //mem_idx
            var val = $(this).attr("value");
            var send_user = $(".ldr_user").find("#ldr_user_name_" + val).text();
            var send_userid = $(".ldr_user").find("#ldr_user_id_" + val).val();
            var fdata = new FormData();
            fdata.append("mode", "mem_lives_like");
            fdata.append("mem_idx", val);
            fdata.append("send_user", send_user);
            fdata.append("send_userid", send_userid);
            var clickedElement = $(this);
            $.ajax({
                type: "POST",
                data: fdata,
                contentType: false,
                processData: false,
                url: '/inc/lives_process.php',
                // beforeSend: function () {
                //     $('.rewardy_loading_01').css('display', 'block');
                // },
                success: function(data) {
                    clickedElement.addClass("star_on");
                    console.log(data);
                    // lives_index_list_new();
                    // $('.rewardy_loading_01').css('display', 'none');
                }
            });
        }
        }
    });

    // 좋아요 보내기 (역량 레이어)
    $(document).on("click", ".rl_jjim_only", function() {
        
        if($("#user_penalty_radar").val()=="1"){
            alert("해당 유저에게 페널티가 적용되어 좋아요를 보낼 수 없습니다.");
            return false;
        }

        // var val = $(this).attr("value");
        var val = $('.user_id').val();
        var send_user = $(".rl_name_user").text();
        var send_userid = $(".user_id").val();
    
        $(".jf_box_in .jf_top strong span").text(send_user);
        $(".jl_box_in .jl_top strong span").text(send_user);
    
        $(".jf_close").removeClass("live");
        $(".jf_close").addClass("rader");
    
        var fdata = new FormData();
        var work_idx = $("#work_idx").val();
    
    
        if (send_userid) {
            $("#send_userid").val(send_userid);
        }
    
        fdata.append("mode", "lives_like");
        fdata.append("mem_idx", val);
    
        console.log("val == " + val);
        $.ajax({
            type: "POST",
            data: fdata,
            contentType: false,
            processData: false,
            url: '/inc/works_process.php',
            success: function(data) {
                console.log(data);
                if(data == "penalty_on"){
                    alert("해당 유저에게 페널티가 적용되어 보낼 수 없습니다.");
                    return false;
                }else if(data == "today_like"){
                    $(".jjim_first").show();
                    $(".radar_layer").hide();
                    return false;
                } else{
                    $(".jjim_first").show();
                    $(".radar_layer").hide();
                }
                }
            });
        });
// 찜하기 (좋아요 레이어 - 일반)
$(document).on("click", ".jjim_only", function() {  
    // var val = $(this).attr("value");
    var val = $('.user_id').val();
    var send_user = $(".jg_name_user").text();
    var send_userid = $(".user_id").val();

    $(".jf_box_in .jf_top strong span").text(send_user);
    $(".jl_box_in .jl_top strong span").text(send_user);

    $(".jf_close").removeClass("rader");
    $(".jf_close").addClass("live");

    var fdata = new FormData();
    var work_idx = $("#work_idx").val();


    if (send_userid) {
        $("#send_userid").val(send_userid);
    }

    fdata.append("mode", "lives_like");
    fdata.append("mem_idx", val);

    console.log("val == " + val);
    $.ajax({
        type: "POST",
        data: fdata,
        contentType: false,
        processData: false,
        url: '/inc/works_process.php',
        success: function(data) {
            console.log(data);
            if(data == "penalty_on"){
                alert("해당 유저에게 페널티가 적용되어 보낼 수 없습니다.");
                return false;
            }else if(data == "today_like"){
                $(".jjim_first").show();
                $(".jjim_graph").hide();
                $(".jjim_table").hide();
                return false;
            } else{
                $(".jjim_first").show();
                $(".jjim_graph").hide();
                $(".jjim_table").hide();
            }
            }
        });
    });

    // 찜하기 (좋아요 레이어 - 상세보기)
$(document).on("click", ".jt_jjim_only", function() {
    // var val = $(this).attr("value");
    var val = $('.user_id').val();
    var send_user = $(".jg_name_user").text();
    var send_userid = $(".user_id").val();

    penalty_idx = $("#user_penalty_like").val();
    if(penalty_idx == "1"){
        alert("좋아요를 보내려는 유저에게 페널티가 적용되어 보낼 수 없습니다");
        return false;
    }
    $(".jf_box_in .jf_top strong span").text(send_user);
    $(".jl_box_in .jl_top strong span").text(send_user);

    $(".jf_close").removeClass("rader");
    $(".jf_close").removeClass("live");
    $(".jf_close").addClass("like");

    var fdata = new FormData();
    var work_idx = $("#work_idx").val();

    if (send_userid) {
        $("#send_userid").val(send_userid);
    }

    fdata.append("mode", "lives_like");
    fdata.append("mem_idx", val);

    console.log("val == " + val);
    $.ajax({
        type: "POST",
        data: fdata,
        contentType: false,
        processData: false,
        url: '/inc/works_process.php',
        success: function(data) {
            console.log(data);
            if(data == "penalty_on"){
                alert("해당 유저에게 페널티가 적용되어 보낼 수 없습니다.");
                return false;
            }else if(data == "today_like"){
                $(".jjim_first").show();
                $(".jjim_graph").hide();
                $(".jjim_table").hide();
                return false;
            } else{
                $(".jjim_first").show();
                $(".jjim_graph").hide();
                $(".jjim_table").hide();
            }
            }
        });
    });

    //찜하기
    $(document).on("click", "button[id^=jjim_only]", function() {

        var val = $(this).attr("value");
        var send_user = $(".ldr_user").find("#ldr_user_name_" + val).text();
        var send_userid = $(".ldr_user").find("#ldr_user_id_" + val).val();
        $(".jf_box_in .jf_top strong span").text(send_user);
        $(".jl_box_in .jl_top strong span").text(send_user);

        var fdata = new FormData();
        var work_idx = $("#work_idx").val();

        $(".jf_close").removeClass("on");

        if (send_userid) {
            $("#send_userid").val(send_userid);
        }

        fdata.append("mode", "lives_like");
        fdata.append("mem_idx", val);

        console.log("val == " + val);
        $.ajax({
            type: "POST",
            data: fdata,
            contentType: false,
            processData: false,
            url: '/inc/works_process.php',
            success: function(data) {
                console.log(data);
                if(data == "penalty_on"){
                    alert("해당 유저에게 페널티가 적용되어 보낼 수 없습니다.");
                    return false;
                }else if(data == "today_like"){
                    $(".jjim_first").show();
                    return false;
                } else{
                    $(".jjim_first").show();
                }
			}
		});
    });


    $(document).on("click", "#ldr_popup button", function() {
        var val = $(this).attr("value");

        if ($(this).find("span").text() == '출근 제일 빨리 함') {
            $("#like_flag").val(1);
        } else if ($(this).find("span").text() == '오늘업무 제일 많이 씀') {
            $("#like_flag").val(2);
        }

        var send_user = $(".ldr_user").find("#ldr_user_name_" + val).text();
        var send_userid = $(".ldr_user").find("#ldr_user_id_" + val).val();

        if (send_userid) {
            $("#send_userid").val(send_userid);
        }

        $(".jf_close").removeClass("on");
        var fdata = new FormData();

        fdata.append("mode", "lives_like");
        fdata.append("mem_idx", val);

        console.log("val == " + val);
        $.ajax({
            type: "POST",
            data: fdata,
            contentType: false,
            processData: false,
            url: '/inc/works_process.php',
            success: function(data) {
                console.log(data);
                if(data == "penalty_on"){
                    alert("해당 유저에게 페널티가 적용되어 보낼 수 없습니다.");
                    return false;
                }else if(data == "today_like"){
                    $(".jf_box_in .jf_top strong span").text(send_user);
                    $(".jl_box_in .jl_top strong span").text(send_user);
                    $(".jjim_first").show();
                    // return false;
                } else{
                    $(".jf_box_in .jf_top strong span").text(send_user);
                    $(".jl_box_in .jl_top strong span").text(send_user);
                    $(".jjim_first").show();
                }
			}
		});
    });


    // 좋아요 클릭 레이어 (그래프형)
    $(document).on("click", ".btn_type_graph", function() {

        // var val = $(".user_value").val();
        var send_user = $(".jg_name_user").text();
        var send_user_team = $(".jg_name_team").text();
        var send_user_imgs = $("#jg_user_img").css("background-image").replace(/^url\(['"](.+)['"]\)/, '$1');
        var send_userid = $(".user_value").val();

        var penalty_idx = $("#user_penalty_like").val();
        if(penalty_idx == "1"){
            $("#user_penalty_like").val(penalty_idx);
        }
        // console.log(penalty);

        if (send_userid) {
            $("#send_userid").val(send_userid);
            $("#lr_uid").val(send_userid);
        }


        console.log("send_user :: " + $("#send_userid").val());

        var fdata = new FormData();
        fdata.append("mode", "jg_graph_list");
        //fdata.append("jf_idx", jf_idx);
        //fdata.append("jl_comment", jl_comment);
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
                        
                        $("#jjim_table").hide();
                        $("#jjim_graph").show();
                        $(".btn_type_graph").addClass("on");
                        $(".btn_type_list").removeClass("on");
                    }
                }
            }
        });
    });

    //좋아요 클릭 레이어
    $(document).on("click", "button[id^=ldr_jjim_num]", function() {

        $('.btn_type_list').removeClass("on");
        $('.btn_type_graph').addClass("on");

        var find_class = $(this).parent().parent().parent().parent();
        if(find_class.hasClass("ldr_me")){
            $(".jjim_only").hide();
            $(".jg_user_btn_coin").hide();
            $(".jt_user_mess").hide();
            $(".jt_jjim_only").hide();
            $(".jt_user_btn_coin").hide();
        }else{
            $(".jjim_only").show();
            $(".jg_user_btn_coin").show();
            $(".jt_user_mess").show();
            $(".jt_jjim_only").show();
            $(".jt_user_btn_coin").show();
        }

        var val = $(this).attr("value");
        var send_user = $(".ldr_user").find("#ldr_user_name_" + val).text();
        var send_user_team = $(".ldr_user").find("#ldr_user_team_" + val).text();
        var send_user_imgs = $(".ldr_user").find("#ldr_user_imgs_" + val).css("background-image").replace(/^url\(['"](.+)['"]\)/, '$1');
        var send_userid = $(".ldr_user").find("#ldr_user_id_" + val).val();
        var penalty_idx = $("#user_penalty_"+$(this).val()).val();
       
        // console.log(penalty);
        $("#jg_name_user").text(send_user);
        $("#jg_name_team").text(send_user_team);
        $("#jg_user_img").css("background-image", "url(" + send_user_imgs + ")");

        $("#jg_name_user").val(val);


        //console.log("sssss");
        //console.log("send_userid :: " + send_userid);
        if (send_userid) {
            $("#send_userid").val(send_userid);
            $("#lr_uid").val(send_userid);
        }

        var fdata = new FormData();
        fdata.append("mode", "jg_graph_list");
        //fdata.append("jf_idx", jf_idx);
        //fdata.append("jl_comment", jl_comment);
        fdata.append("send_userid", send_userid);

        $.ajax({
            type: "post",
            async: false,
            data: fdata,
            contentType: false,
            processData: false,
            url: '/inc/lives_process.php',
            success: function(data) {
                // console.log(data);
                if (data) {
                    var tdata = data.split("|");
                    if (tdata) {
                        var result = tdata[0];
                        var cnt = tdata[1];
                        $("#jg_graph_list").html(result);
                        $("#jg_user_heart_all span").text(cnt);
                        $("#user_penalty_like").val(penalty_idx);
                        //그래프 높이설정   
                        var jg_all = $(".jg_user_heart_all").text();
                        $(".jg_graph_list li").each(function() {
                            var jg_txt = $(this).find("span").text();
                            var jg_height = jg_txt / jg_all * 160;
                            $(this).find("strong").css({ "height": jg_height });
                        });
                        $(".user_id").val(send_userid);
                        $(".user_value").val(send_userid);
                        console.log(send_userid);
                        $("#jjim_graph").show();
                    }
                }
            }
        });
    });


// 좋아요 상세보기 리스트 (리스트형)
link_href = window.location.href;
    var link_arr = link_href.split("/");
    home_title = link_arr[3];
    
    if(home_title == "live" || home_title == "todaywork"){
        $(document).on("click", ".btn_type_list", function(){

            if($(".user_value").val){
                var send_userid = $(".user_value").val();
            }else if ($("#lr_uid").val()) {
                var send_userid = $("#lr_uid").val();
            } else if ($("#send_userid").val()) {
                var send_userid = $("#send_userid").val();
            }  
        
            var send_user = $(".jg_name_user").text();
            var send_user_team = $(".jg_name_team").text();
            var send_user_imgs = $("#jg_user_img").css("background-image").replace(/^url\(['"](.+)['"]\)/, '$1');

            // console.log(send_user);
            // console.log(send_user_team);
            // console.log(send_user_imgs);
            // console.log(send_userid);
            var penalty_idx = $("#user_penalty_like").val();
            if(penalty_idx == "1"){
                $("#user_penalty_like").val(penalty_idx);
            }
            
            var fdata = new FormData();
            fdata.append("mode", "jt_table_list");
            fdata.append("send_userid", send_userid);
            $.ajax({
                type: "post",
                async: false,
                data: fdata,
                contentType: false,
                processData: false,
                url: '/inc/lives_process.php',
                success: function(data) {
                    // console.log(data);
                    if (data) {
                        var tdata = data.split("|");
                        if (tdata) {
                            var result = tdata[0];
                            var cnt = tdata[1];

                            $("#jt_table_area").html(result);
                            $("#jt_user_heart_all span").text(cnt);
                            $(".jt_name_user").text(send_user);
                            $(".jt_name_team").text(send_user_team);
                            $("#jt_user_img").css("background-image", 'url("' + send_user_imgs + '")');
                            $(".user_value").val(send_userid);
                            $("#jjim_graph").hide();
                            $("#jjim_table").show();
                        }
                    }
                }
            });
            $("#jjim_graph").hide();
            $("#jjim_table").show();
            $(".btn_type_list").addClass("on");
            $(".btn_type_graph").removeClass("on");
        });
    }
    //좋아요 상세보기 클릭
    $("#jg_bottom").click(function() {

        if ($("#lr_uid").val()) {
            var send_userid = $("#lr_uid").val();
        } else if ($("#send_userid").val()) {
            var send_userid = $("#send_userid").val();
        }

        if($("#user_penalty").val() == '1'){
            var penalty = '1';
        }
        var fdata = new FormData();
        fdata.append("mode", "jt_table_list");
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

                        $("#jt_table_area").html(result);
                        $("#jt_user_heart_all span").text(cnt);

                        $("#user_penalty").val(penalty);
                        $("#jjim_graph").hide();
                        $("#jjim_table").show();
                    }
                }
            }
        });

        $("#jjim_graph").hide();
        $("#jjim_table").show();

    });


    //좋아요 상세보기 클릭
    $(".jg_icon_list li button").click(function() {
        //$("#jg_bottom").click(function() {

        var val = $(this).attr("value");
        var send_userid = $("#send_userid").val();
        var jt_txt = '';
        if (val == 1) { jt_txt = work_reward_title_arr['0']; }
        if (val == 2) { jt_txt = work_reward_title_arr['1']; }
        if (val == 3) { jt_txt = work_reward_title_arr['2']; }
        if (val == 4) { jt_txt = work_reward_title_arr['3']; }
        if (val == 5) { jt_txt = work_reward_title_arr['4']; }
        if (val == 6) { jt_txt = work_reward_title_arr['5']; }

        var fdata = new FormData();
        fdata.append("mode", "jt_table_list");
        fdata.append("send_userid", send_userid);
        fdata.append("kind_flag", val);

        $("#jt_top_btn").attr("class");
        $("#jt_top_btn").removeClass($("#jt_top_btn").attr("class"));
        $("#jt_top_btn").addClass("jg_i0" + val);
        $("#jt_top_btn strong").text(jt_txt);

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

                        $("#jt_table_area").html(result);
                        $("#jt_user_heart_all span").text(cnt);

                        $("#jjim_graph").hide();
                        $("#jjim_table").show();
                    }
                }
            }
        });
    });

    //역량에서 쪽지 키기
    $(document).on("click", ".rl_user_mess", function(){

        $(".textarea_mess").val(null);
        var countElement = $('.message_count');
        countElement.text('0자 / 100자');

        var send_user = $(".rl_name_user").text();
        var send_user_team = $(".rl_name_team").text();
        var send_user_imgs = $(".rl_user_img_in").css("background-image").replace(/^url\(['"](.+)['"]\)/, '$1');

        $(".layer_mess_cancel").removeClass("header");
        $(".layer_mess_cancel").removeClass("live");
        $(".layer_mess_cancel").addClass("rader");
        if ($("#lr_uid").val()) {
            var send_userid = $("#lr_uid").val();
        } else if ($("#send_userid").val()) {
            var send_userid = $("#send_userid").val();
        }
        $(".mess_name_user").text(send_user);
        $(".mess_name_team").text(send_user_team);
        $(".mess_user_img_in").css("background-image", 'url("' + send_user_imgs + '")');

        $(".user_id").val($('.user_id').val());
        $(".layer_mess").show();
        $(".radar_layer").hide();
    });

    $(document).on("click", ".jt_user_mess", function(){

        $(".textarea_mess").val(null);
        var countElement = $('.message_count');
        countElement.text('0자 / 100자');

        var send_user = $(".jg_name_user").text();
        var send_user_team = $(".jg_name_team").text();
        var send_user_imgs = $("#jg_user_img").css("background-image").replace(/^url\(['"](.+)['"]\)/, '$1');

        $(".layer_mess_cancel").removeClass("header");
        $(".layer_mess_cancel").addClass("live");
        if ($("#lr_uid").val()) {
            var send_userid = $("#lr_uid").val();
        } else if ($("#send_userid").val()) {
            var send_userid = $("#send_userid").val();
        }

        $(".mess_name_user").text(send_user);
        $(".mess_name_team").text(send_user_team);
        $(".mess_user_img_in").css("background-image", 'url("' + send_user_imgs + '")');

        $(".user_id").val(send_userid);
        $(".layer_mess").show();
        $(".jjim_graph").hide();
        $(".jjim_table").hide();
    });

    $(".textarea_mess").bind("input", function (event) {
        var val = $(this).val();
        if (val) {
          if ($(".layer_mess_submit").hasClass("on") == false) {
            $(".layer_mess_submit").addClass("on");
            $(".textarea_mess").val(val);
          }
        } else {
          $(".layer_mess_submit").removeClass("on");
        }
      });


    // 좋아요 상세에서 쪽지 보내기(리스트에 각각의 인원)
    $(document).on("click", ".jt_table_body .jt_table_04", function(){
        $(".textarea_mess").val(null);
        var countElement = $('.message_count');
        countElement.text('0자 / 100자');

        var send_user = $(this).find("span").text();
        var parentLi = $(this).closest("li");
        var send_email = $(this).attr("value");
        var send_user_team = parentLi.find(".table_part").val();
        var send_user_imgs = parentLi.find(".table_img").val();

        $(".layer_mess_cancel").removeClass("header");
        $(".layer_mess_cancel").removeClass("rader");
        $(".layer_mess_cancel").addClass("like");
        $(".layer_mess_cancel").addClass("header_like");
        if($(this).attr("value")){
            var send_userid = $(this).attr("value");
        }else if ($("#lr_uid").val()) {
            var send_userid = $("#lr_uid").val();
        } else if ($("#send_userid").val()) {
            var send_userid = $("#send_userid").val();
        }

        $(".mess_name_user").text(send_user);
        $(".mess_name_team").text(send_user_team);
        $(".mess_user_img_in").css("background-image", 'url("' + send_user_imgs + '")');

        $(".user_id").val(send_userid);
        $(".layer_mess").show();
        $(".jjim_graph").hide();
        $(".jjim_table").hide();
    });
    // 쪽지 보내기
    $(document).on("click", ".layer_mess_submit", function(){
        var send_user = $(".mess_name_user").text();
        var send_user_team = $(".mess_name_team").text();
        var send_user_imgs = $(".mess_user_img_in").css("background-image").replace(/^url\(['"](.+)['"]\)/, '$1');
        var user_email  = $(".user_id").val();
        var message_content = $(".textarea_mess").val();
        console.log(user_email);
        console.log(message_content);
        
        if($(".layer_mess_cancel").hasClass("live") == true || $(".layer_mess_cancel").hasClass("rader") == true){
            if(!message_content){
                alert("내용을 작성해주세요.");
                return false;
            }
        
            var fdata = new FormData();
            fdata.append("mode", "mess_live");
            fdata.append("send_email", user_email);
            fdata.append("message", message_content);
            $.ajax({
                type: "post",
                async: false,
                data: fdata,
                contentType: false,
                processData: false,
                url: '/inc/lives_process.php',
                success: function(data) {
                    // console.log(data);
                    if(data == 'complete'){
                        alert("쪽지를 전송하였습니다.");
                        $(".layer_mess").hide();
                        if($('.layer_mess_cancel').hasClass("live")){
                            $(".jjim_graph").show();
                        }else if($('.layer_mess_cancel').hasClass("rader")){
                            $(".radar_layer").show();
                        }
                    }else if(data == 'mem_fail'){
                        alert("해당 회원이 없습니다.");
                        return false;
                    }
                }
            });
        }
    });

    // 쪽지 닫기
    $(document).on("click", ".layer_mess_cancel", function() {
        $(".layer_mess").hide();
        if($(".layer_mess_cancel").hasClass("live") == true){
           $(".jjim_graph").show();
        }else if($(".layer_mess_cancel").hasClass("rader") == true){
            $(".radar_layer").show();
        }else if($(".layer_mess_cancel").hasClass("like") == true){
            $(".jjim_table").show();
        }

    });
    //댓글 좋아요
    $(document).on("click", "button[id^=btn_memo_jjim]", function() {
        console.log("좋아요");
        var val = $(this).val();
        var fdata = new FormData();
        fdata.append("comment_idx", val);
        fdata.append("mode", "work_comment_check");
        $("#service").val("memo");

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
                        var uid = tdata[0];
                        var name = tdata[1];
                        $("#send_userid").val(uid);
                        $(".jf_box_in .jf_top strong span").text(name);
                        $(".jl_box_in .jl_top strong span").text(name);
                        $("#work_idx").val(val);
                        $('.jjim_first').show();
                    }
                }
            }
        });

        //console.log(" aaa: " + aaa);
        //console.log(" val: " + val);
        //var send_user = $(".list_function_left em").text();
        //$(".jf_box_in .jf_top strong span").text(send_user);
        //$(".jl_box_in .jl_top strong span").text(send_user);

        //$("#work_idx").val(val);



    });

	/*
    setTimeout(function() {
        //$(".rew_box").addClass("on");

        var bar_t = $(".rew_mypage_section .live_list_today_bar strong").text();
        var bar_b = $(".rew_mypage_section .live_list_today_bar span").text();
        var bar_w = bar_t / bar_b * 100;
        $(".rew_mypage_section .live_list_today_bar strong").css({ width: bar_w + "%" });

    }, 400);*/

    //상세보기 뒤로가기 버튼
    $(".jt_top button").click(function() {
        $("#jjim_table").hide();
        $("#jjim_graph").show();
    });

    //상세보기 닫기
    $(".jt_close").click(function() {
        $("#jjim_table").hide();
    });

    //좋아요 그래프 닫기
    $(".jg_close").click(function() {
        $("#jjim_graph").hide();
    });


    /*$(".ldl_in").sortable({
        axis: "y",
        opacity: 0.7,
        zIndex: 9999,
        //placeholder:"sort_empty",
        cursor: "move"
    });*/





    //파티 전체파티 이동
    $(document).on("click", "#ldl_in", function() {
        $(this).sortable({
            axis: "y",
            opacity: 0.7,
            zIndex: 9999,
            //placeholder:"sort_empty",
            cursor: "move",
            update: function(event, ui) {

                var fdata = new FormData();
                var listsort = $(this).sortable('serialize');
                var wdate = $("#work_date").val();
                var id = ui.item.attr("id");
                var val = $("#" + id).val();

                fdata.append("id", id);
                fdata.append("val", val);
                fdata.append("mode", "project_move");
                fdata.append("listsort", listsort);

                $.ajax({
                    type: "POST",
                    data: fdata,
                    contentType: false,
                    processData: false,
                    url: '/inc/lives_process.php',
                    success: function(data) {
                        console.log(data);
                        if (data == "complete") {
                            //$(".calendar_num").text(data);
                            //return false;
                        }
                    }
                });

            }
        });
    });


    //파티 나의파티 이동
    $(document).on("click", "#ldl_in_my", function() {
        $(this).sortable({
            axis: "y",
            opacity: 0.7,
            zIndex: 9999,
            //placeholder:"sort_empty",
            cursor: "move",
            update: function(event, ui) {

                var fdata = new FormData();
                var listsort = $(this).sortable('serialize');
                var wdate = $("#work_date").val();
                var id = ui.item.attr("id");
                var val = $("#" + id).val();

                fdata.append("id", id);
                fdata.append("val", val);
                fdata.append("project_my", 1);
                fdata.append("mode", "project_move");
                fdata.append("listsort", listsort);

                $.ajax({
                    type: "POST",
                    data: fdata,
                    contentType: false,
                    processData: false,
                    url: '/inc/lives_process.php',
                    success: function(data) {
                        console.log(data);
                        if (data == "complete") {
                            //$(".calendar_num").text(data);
                            //return false;
                        }
                    }
                });

            }
        });
    });



    //파티 삭제
    //$(document).on("click", "button[id^=ldl_box_close_]", function() {
    $(document).on("click", "button[id^=ldl_box_close_all]", function() {

        if (confirm("파티를 삭제 하시겠습니까?")) {
            var val = $(this).val();
            var id = $(this).attr("id");
            console.log(id);

            if (id && val) {
                var fdata = new FormData();
                fdata.append("mode", "project_del");
                fdata.append("project_idx", val);
                $.ajax({
                    type: "POST",
                    data: fdata,
                    contentType: false,
                    processData: false,
                    url: '/inc/lives_process.php',
                    success: function(data) {
                        console.log(data);
                        if (data == "complete") {
                            $("#ldl_in #" + id).closest(".ldl_box").remove();
                            var ldl_length = Math.abs($(".rew_mypage_section em:eq(0)").text()) - 1;
                            $(".rew_mypage_section em:eq(0)").text(ldl_length);
                        }
                    }
                });
            }
        }
    });


    //나의파티 삭제
    $(document).on("click", "button[id^=ldl_box_close_my]", function() {

        if (confirm("파티를 삭제 하시겠습니까?")) {
            var val = $(this).val();
            var id = $(this).attr("id");
            console.log(id);

            if (id && val) {
                var fdata = new FormData();
                fdata.append("mode", "project_del");
                fdata.append("project_idx", val);
                $.ajax({
                    type: "POST",
                    data: fdata,
                    contentType: false,
                    processData: false,
                    url: '/inc/lives_process.php',
                    success: function(data) {
                        console.log(data);
                        if (data == "complete") {
                            $("#ldl_in_my #" + id).closest(".ldl_box").remove();
                            var ldl_length = Math.abs($(".rew_mypage_section em:eq(0)").text()) - 1;
                            $(".rew_mypage_section em:eq(0)").text(ldl_length);
                        }
                    }
                });
            }
        }
    });


    //파티 나가기
    $(document).on("click", "button[id^=ldl_box_out_]", function() {

        var id = $(this).attr("id");
        var val = $(this).val();

        console.log("val :: " + val);
        if (id && val) {
            if (confirm('현재 파티에서 나가기 하시겠습니까?')) {

                $(this).parent().find(".ldl_me").remove();
                var fdata = new FormData();
                fdata.append("mode", "project_part_out");
                fdata.append("project_idx", val);

                $.ajax({
                    type: "POST",
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
                                var edate = tdata[1];
                                if (result == "complete") {
                                    $("#layer_plus").hide();
                                    //$(".ldl_box_user #ldl_me_" + val).closest(".ldl_me").remove();
                                    //$(".ldl_box_user .ldl_box_me").css("display", "none");


                                    $(this).parent().find(".ldl_me").remove()
                                    $(".ldl_box_user .ldl_box_me").remove();

                                    $("#ldl_box_out_" + val).hide();
                                    $("#ldl_box_time_" + val).text(edate + " 업데이트");
                                }
                            }
                        }
                    }
                });
            }
        }
    });



    // 라이브 드래그 금지 
    // var me_clone = $("#ldr .ldr_me").clone();
    // $(document).on('click','#ldr .ldr_me',function(){
    //     $(this).draggable({
    //         helper: "clone"
    //     });
    //     //refreshPositions: true,
    //     //connectToSortable:"#ldl",
    //     //revert:true,
    //     //revert: "invalid",
    //     //containment: "document",
        
    // });


    // -----------------------
    //마우스 오버 - 그냥 마음전하기
    $(document).on('mouseenter', '.ldr_menu', function(e) {
        $(this).closest(".ldr_li").find(".ldr_popup").show();
    });

    //마우스 벗어날때 - 그냥 마음전하기
    //$(".ldr_popup").mouseleave(function() {
    $(document).on('mouseleave', '.ldr_popup', function(e) {
        $(this).hide();
    });

    $(document).on('mouseenter', '.ldr_today', function(e) {
        $(this).closest(".ldr_li").find(".layer_ldr_today").show();
    });

    $(document).on('mouseleave', '.ldr_today', function(e) {
        $(this).closest(".ldr_li").find(".layer_ldr_today").hide();
    });

    $(document).on('mouseenter', '.ldr_chall', function(e) {
        $(this).closest(".ldr_li").find(".layer_ldr_chall").show();
    });

    $(document).on('mouseleave', '.ldr_chall', function(e) {
        $(this).closest(".ldr_li").find(".layer_ldr_chall").hide();
    });

    $(document).on('mouseenter', '.ldr_jjim', function(e) {
        $(this).closest(".ldr_li").find(".layer_ldr_jjim").show();
    });

    $(document).on('mouseleave', '.ldr_jjim', function(e) {
        $(this).closest(".ldr_li").find(".layer_ldr_jjim").hide();
    });



    //파티 생성
    $(".lt_bottom button").click(function() {
        if ($(this).hasClass("btn_on")) {
            var ltb_t;

			if (!$("#textarea_lm").val()) {
				alert("함께 할 업무를 작성해주세요.");
				$("#textarea_lm").focus();
				return false;
			}

            /*if (!$("#team_input").val()) {
                alert("파티명을 입력해주세요..");
                $("#team_input").focus();
                return false;
            }*/

            ltb_t = confirm("파티를 생성하시겠습니까?");
            if (ltb_t == true) {
                var me_img = $(".ldr_me .ldr_user_img .ldr_user_imgs").css("background-image").replace(/^url\(['"](.+)['"]\)/, '$1');
                var lt_img = $(".lt_ul_01 .ll_li .ll_img_user").css("background-image").replace(/^url\(['"](.+)['"]\)/, '$1');
                var me_user = $(".ldr_me .ldr_user_desc .ldr_user_name").text();
                var lt_user = $(".lt_ul_01 .ll_li .ll_name_user").text();
                var me_team = $(".ldr_me .ldr_user_desc .ldr_user_team").text();
                var lt_team = $(".lt_ul_01 .ll_li .ll_name_team").text();
                //var lt_text = $(".textarea_lt").val();
                var lt_text = $("#textarea_lm").val();
                var lt_id = $("#lt_id").val();

                var fdata = new FormData();
                var input_val = $("#input_index_search_new").val();
                fdata.append("mode", "project_add");
                fdata.append("lt_id", lt_id);
                fdata.append("lt_text", lt_text);

                $.ajax({
                    type: "post",
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
                                var html = tdata[1];
                                if (result == "complete") {
                                    var ldl_length = Math.abs($(".rew_mypage_section em:eq(0)").text()) + 1;
                                    $(".rew_mypage_section em:eq(0)").text(ldl_length);
                                    $("#layer_team").hide();
                                    $(".ldl_in").prepend(html);
                                }

                            }
                        }

                    }
                });
            }
        }
    });


    //참여하기 닫기
    $(".lp_close").click(function() {
        $("#layer_plus").hide();
        $(".ldl_box_me").remove();
    });


    //페널티
    //$("#btn_lr_04").click(function() {
    $(document).on("click", "#btn_lr_04", function() {
        console.log("btn4");
        $(".layer_today .layer_result_right").removeClass("pos_fix");
        $(".layer_result_tab li button").removeClass("on");
        $(".layer_result_tab li .btn_lr_04").addClass("on");
        $(".layer_result_list").hide();
        $("#layer_result_list_pl").show();
        $(".layer_result_list.desc_lr_04").css({ opacity: 1 });
        $(".layer_result_list.desc_lr_04").show();
        penalty_all_list();
    });


    //전체파티
    $(document).on("click", "#project_all", function() {
        $("#project_my").removeClass("on");
        $(this).addClass("on");

        if ($("#ldl_in_my").is(":visible") == true) {
            $("#ldl_in_my").hide();
        }

        var fdata = new FormData();
        fdata.append("mode", "project_all");
        $.ajax({
            type: "post",
            async: false,
            data: fdata,
            contentType: false,
            processData: false,
            url: '/inc/lives_process.php',
            success: function(data) {
                //console.log(data);
                if (data) {
                    var tdata = data.split("|");
                    if (tdata) {
                        var result = tdata[0];
                        var result_cnt = tdata[1];

                        $("#ldl_in").show();
                        $("#ldl_in").html(result);
                        $(".ldl_box").trigger("click");
                        $(".rew_mypage_section em:eq(0)").text(result_cnt);
                        //$("#ldl_in").css("display", "block");
                    }
                }

            }
        });


    });

    //나의파티
    $(document).on("click", "#project_my", function() {
        $("#project_all").removeClass("on");
        $(this).addClass("on");

        if ($("#ldl_in").is(":visible") == true) {
            $("#ldl_in").hide();
        }

        var fdata = new FormData();
        fdata.append("mode", "project_my");

        $.ajax({
            type: "post",
            async: false,
            data: fdata,
            contentType: false,
            processData: false,
            url: '/inc/lives_process.php',
            success: function(data) {
                //console.log(data);
                if (data) {
                    var tdata = data.split("|");
                    if (tdata) {
                        $("#ldl_in_my").show();
                        var result = tdata[0];
                        var result_cnt = tdata[1];
                        $("#ldl_in_my").html(result);
                        $(".ldl_box").trigger("click");
                        $(".rew_mypage_section em:eq(1)").text(result_cnt);
                    }
                }

            }
        });
    });


    //파티만들기
    $(document).on("click", "#btn_mypage_party_make", function() {

        if ($(".layer_user_slc_list_in ul li").length > 0) {
            $(".layer_user_slc_list_in ul li").remove();
        }
        $(".layer_user").show();
    });





    //좋아요 > 코인으로 보상하기 
    $(document).on("click", '.jg_user_btn_coin', function(){

        penalty_idx = $("#user_penalty_like").val();
        if(penalty_idx == '1'){
            alert("해당 유저에게 페널티가 적용되어 코인을 보상할 수 없습니다.");
            return false;
        }

        $(".lr_close").removeClass("rader");
        $(".lr_close").addClass("live");
        var img = $(this).parent().parent().parent().find("#jg_user_img").css("background-image").replace(/^url\(['"](.+)['"]\)/, '$1');
        var lr_idx = $("#jg_name_user").val();
        $("#lr_idx").val(lr_idx);


        $("#jjim_graph").hide();
        $("#layer_reward").show();
    });


    //좋아요 상세보기 > 코인으로 보상하기
    $(document).on("click", '.jt_user_btn_coin', function(){

        var penalty_idx = $("#user_penalty_like").val();
        if(penalty_idx == '1'){
            alert("해당 유저에게 페널티가 적용되어 코인을 보상할 수 없습니다.");
            return false;
        }
        $(".lr_close").removeClass("rader");
        $(".lr_close").removeClass("live");
        $(".lr_close").addClass("like");
        $("#jjim_table").hide();
        $("#layer_reward").show();
    });


    //보상하기 숫자만 입력
    $("#lr_input").bind("change keyup input", function(event) {
        if (!(event.keyCode >= 37 && event.keyCode <= 40)) {
            var inputVal = $(this).val();
            $(this).val(inputVal.replace(/[^0-9]/gi, ''));
        }
    });


    //입력폼 포커스 되었을때
    $(document).on("focus", "input[id^=lr_input]", function() {
        focused(this.id);
    });


    //입력폼 포커스 벗어날때
    $(document).on("blur", "input[id^=lr_input]", function() {
        blured(this.id);
    });

    //보상하기
    $(document).on("click", "#ldr_jjim_pop_in", function() {
        //var id = $(this).parents("div").find(".ldr_jjim_num").attr("id");
        var id = $(this).parent().parent().find(".ldr_jjim_num").attr("id");
        if (id) {
            var no = id.replace("ldr_jjim_num_", "");
            var uid = $("#ldr_user_id_" + no).val();
            $("#lr_uid").val(uid);
        }
        $("#layer_reward").show();
    });


    //보상하기 닫기
    $("#lr_close").click(function() {
        $(".lr_area li button").removeClass("on");
        $("#lr_input").val("");
        $("#lr_input_text").val("");
        $("#layer_reward").hide();
        if($(".lr_close").hasClass("live") == true){
           $(".jjim_graph").show();
         }else if($(".lr_close").hasClass("rader") == true){
            $(".radar_layer").show();
        }else if($(".lr_close").hasClass("like") == true){
            $(".jjim_table").show();
        }
    });

    //보상하기 선택
    $(".lr_area li button").click(function() {
        var val = $(this).val();

        $(".lr_area li button").removeClass("on");
        $(this).addClass("on");
        if (val) {
            $("#lr_val").val(val);
        }
    });

    //보상하기 클릭
    $(".lr_area ul li button").click(function() {
        var coin = $(this).find(".lr_coin em").text();
        var lr_input_text = $(this).find(".lr_txt strong").text();
        $("#lr_input").val(coin);
        $("#lr_input_text").val(lr_input_text);
    });


    
    //보상하기 버튼은 live 페이지에서만 작동하도록 수정
    if(home_title=='live'){
        $(document).on("click", ".lr_btn", function() {
            var path = $(location).attr('pathname');
            var path_arr = path.split("/");
            // console.log(path_arr[1]);
            pathDi = path_arr[1];

            var lr_work_idx = $("#lr_work_idx").val();

            var coin = $("#lr_input").val();
            var lr_uid = $("#lr_uid").val();
            var lr_val = $("#lr_val").val();
            var lr_input_text = $("#lr_input_text").val();
            if (!coin) {
                alert("지급할 코인을 선택 또는 입력해 주세요.");
                $("#lr_input").focus();
                return false;
            }

            if (!lr_input_text) {
                alert("메세지를 입력하세요.");
                $("#lr_input_text").focus();
                return false;
            }

            if($(".lr_close").hasClass("live") == true || $(".lr_close").hasClass("rader") == true || $(".lr_close").hasClass("like") == true){
                if (confirm(coin + "코인을 지급 하시겠습니까?")) {

                    var fdata = new FormData();
                    fdata.append("mode", "coin_reward");

                    fdata.append("coin", coin);
                    fdata.append("lr_uid", lr_uid);
                    fdata.append("lr_val", lr_val);
                    fdata.append("lr_input_text", lr_input_text);
                    fdata.append("lr_work_idx", lr_work_idx);
                    fdata.append("path",pathDi);

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
                                if(data == "penalty"){
                                    alert("코인을 지급할 유저에게 패널티가 적용되어 보낼 수 없습니다.");
                                    return false;
                                } else if (data == "id_same") {
                                    alert("보상은 본인에게 지급할 수 없습니다. ");
                                    return false;
                                } else if (data == "none") {
                                    alert("보유한 공용코인이 지급할 코인보다 작습니다.\n보상할 코인을 확인해 주세요.");
                                    $("#lr_input").focus();
                                    return false;
                                } else if (data == "complete") {
                                    alert($(".btn_lr_01 .lr_txt strong").text() + " " + coin + "코인이 보상 되었습니다.");
                                    $("#lr_input").val("");
                                    $("#layer_reward").hide();
                                    todaywork_list_live();
                                    $("#lr_work_idx").val("");
                                }
                                var tdata = data.split("|");
                                if (tdata) {
                                    //var result = tdata[0];
                                    //var result_cnt = tdata[1];
                                    //$("#ldl_in_my").html(result);
                                    //$(".ldl_box").trigger("click");
                                    //$(".rew_mypage_section em:eq(1)").text(result_cnt);
                                }
                            }
                        }
                    });
                }
            }
        });
    }

    //역량지표레이어
    $(document).on("click", ".ldr_chall_num", function(){

        var find_class = $(this).parent().parent().parent().parent();
        if(find_class.hasClass("ldr_me")){
            $(".rl_jjim_only").hide();
            $(".rl_user_btn_coin").hide();
            $(".rl_user_mess").hide();
        }else{
            $(".rl_jjim_only").show();
            $(".rl_user_btn_coin").show();
            $(".rl_user_mess").show();
        }

        var name = $(this).parent().parent().parent().find(".ldr_user_name").text();
        var team = $(this).parent().parent().parent().find(".ldr_user_team").text();
        var ldr_img = $(this).parent().parent().parent().find(".ldr_user_imgs").css("background-image").replace(/^url\(['"](.+)['"]\)/, '$1');
        var uid = $(this).parent().parent().parent().find(".ldr_today_num").val();
        var v = $(this).parent().parent().parent().html();
        var penalty_idx = $("#user_penalty_"+$(this).val()).val();

		console.log(uid+"|"+name+"|"+team+"|");
        
        var val = $(this).attr("value");
        $(".rl_name_user").text(name);
        $(".rl_name_team").text(team);
        $("#rl_name_id").val(uid);
        $(".rl_user_img_in").css("background-image", "url(" + ldr_img + ")");
        $("#radar_layer").show();
        $(".lr_close").removeClass("live");
        $(".lr_close").addClass("rader");
        var fdata = new FormData();
        fdata.append("mode", "cp_reward_list");
        fdata.append("uid", uid);
        fdata.append("type", 1);

        $.ajax({
            type: "post",
            async: false,
            data: fdata,
            contentType: false,
            processData: false,
            url: '/inc/lives_process.php',
            success: function(data) {
                //console.log(data);
                if (data) {
                    var tdata = data.split("|");
                    if (tdata) {
                        var result = tdata[0];
                        var type1 = tdata[1];
                        var type2 = tdata[2];
                        var type3 = tdata[3];
                        var type4 = tdata[4];
                        var type5 = tdata[5];
                        var type6 = tdata[6];
                        $(".rl_right").html(result);
                        $(".btn_rl_01").trigger("click");
                        $(".user_id").val(uid);
                        $("#user_penalty_radar").val(penalty_idx);
                        $(".user_value").val(val);
                        if ($(".radar_total span").text()) {
                            $(".rl_user_heart_all span").text($(".radar_total span").text());
                        }
                        rl_chart_run(type1, type2, type3, type4, type5, type6);
                        $(".radar_total").css("margin-top", "-42px");
                    }
                }
            }
        });
    })

	//라이브 메인 회원 이미지 클릭시
	$(document).on("click", "div[id^=ldr_user_imgs],.ldr_user_penalty", function() {

		//$(".ldr_chall_num").trigger("click");
        var val = $(this).attr("value");
        var find_class = $(this).parent().parent().parent().parent().parent().parent();
        if(find_class.hasClass("ldr_me")){
            $(".rl_jjim_only").hide();
            $(".rl_user_btn_coin").hide();
            $(".rl_user_mess").hide();
        }else{
            $(".rl_jjim_only").show();
            $(".rl_user_btn_coin").show();
            $(".rl_user_mess").show();
        }

		var name = $(this).parent().parent().parent().find(".ldr_user_name").text();
        var team = $(this).parent().parent().parent().find(".ldr_user_team").text();
        var ldr_img = $(this).parent().parent().parent().find(".ldr_user_imgs").css("background-image").replace(/^url\(['"](.+)['"]\)/, '$1');
        var uid = $(this).parent().parent().parent().find("input[id^=ldr_user_id]").val();
        var penalty = $(this).parent().parent().parent().find("input[id^=user_penalty_]").val();

		$(".rl_name_user").text(name);
        $(".rl_name_team").text(team);
        $("#rl_name_id").val(uid);
        $(".rl_user_img_in").css("background-image", "url(" + ldr_img + ")");
        $("#radar_layer").show();

		var fdata = new FormData();
        fdata.append("mode", "cp_reward_list");
        fdata.append("uid", uid);
        fdata.append("type", 1);

        $.ajax({
            type: "post",
            async: false,
            data: fdata,
            contentType: false,
            processData: false,
            url: '/inc/lives_process.php',
            success: function(data) {
                //console.log(data);
                if (data) {
                    var tdata = data.split("|");
                    if (tdata) {
                        var result = tdata[0];
                        var type1 = tdata[1];
                        var type2 = tdata[2];
                        var type3 = tdata[3];
                        var type4 = tdata[4];
                        var type5 = tdata[5];
                        var type6 = tdata[6];

                        $(".rl_right").html(result);
                        $(".btn_rl_01").trigger("click");
                        $(".user_id").val(uid);
                        $(".user_value").val(val);
                        if ($(".radar_total span").text()) {
                            $(".rl_user_heart_all span").text($(".radar_total span").text());
                        }
                        rl_chart_run(type1, type2, type3, type4, type5, type6);
                        $(".radar_total").css("margin-top", "-42px");
                        $("#user_penalty").val(penalty);
                    }
                }
            }
        });

	});

    //역량 지식, 성장, 성실, 실행, 협업, 성과
    $(".rl_tab li button").click(function() {
        $(".rl_tab li button").removeClass("on");
        $(this).addClass("on");
        var rl_text = "";
        var rl_title = $(this).find("span").text();

        var val = $(this).val();
        /*var fdata = new FormData();
        var uid = $("#rl_name_id").val();
        fdata.append("mode", "cp_reward_list");
        fdata.append("type", val);
        fdata.append("uid", uid);

        $.ajax({
            type: "post",
            async: false,
            data: fdata,
            contentType: false,
            processData: false,
            url: '/inc/lives_process.php',
            success: function(data) {
                console.log(data);
            }
        });*/

        //에너지 type1, //성장 type2, //성실 type3, //실행 type4, //협업 type5, //성과 type6
        //변경 후  //지식 type1, //성장 type2, //성실 type3, //실행 type4, //협업 type5, //성과 type6
        if ($(this).hasClass("btn_rl_01")) {
            rl_text = "에너지 항목의 ♥ 좋아요를 받거나,<br /> 에너지 챌린지 완료 시 지표가 상승합니다.";
            rl_dldt = "주요지표 : 오늘업무, 파티";
            $(".rl_grade_score").text($(".radar_01 .radar_pt").text());
            $(".rl_grade_rank").text($(".radar_01").find("em").text());
        }
        if ($(this).hasClass("btn_rl_02")) {
            rl_text = "성장 항목의 ♥ 좋아요를 받거나,<br /> 성장 챌린지 완료 시 지표가 상승합니다.";
            rl_dldt = "주요지표 : 오늘업무, 챌린지";
            $(".rl_grade_score").text($(".radar_02 .radar_pt").text());
            $(".rl_grade_rank").text($(".radar_02").find("em").text());
        }
        if ($(this).hasClass("btn_rl_03")) {
            rl_text = "성실 항목의 ♥ 좋아요를 받거나,<br /> 성실 챌린지 완료 시 상승합니다.";
            rl_dldt = "주요지표 : 출퇴근, 오늘업무 완료";
            $(".rl_grade_score").text($(".radar_03 .radar_pt").text());
            $(".rl_grade_rank").text($(".radar_03").find("em").text());
        }
        if ($(this).hasClass("btn_rl_04")) {
            rl_text = "실행 항목의 ♥ 좋아요를 받거나,<br /> 실행 챌린지 완료 시 상승합니다.";
            rl_dldt = "주요지표 : 오늘업무, 챌린지, 좋아요, 보상";
            $(".rl_grade_score").text($(".radar_04 .radar_pt").text());
            $(".rl_grade_rank").text($(".radar_04").find("em").text());
        }
        if ($(this).hasClass("btn_rl_05")) {
            rl_text = "협업 항목의 ♥ 좋아요를 받거나,<br /> 협업 챌린지 완료 시 상승합니다.";
            rl_dldt = "주요지표 : 업무공유, 요청완료, 파티, 메모";
            $(".rl_grade_score").text($(".radar_05 .radar_pt").text());
            $(".rl_grade_rank").text($(".radar_05").find("em").text());
        }
        if ($(this).hasClass("btn_rl_06")) {
            rl_text = "성과 항목의 ♥ 좋아요를 받거나,<br /> 성과 챌린지 완료 시 지표가 상승합니다.";
            rl_dldt = "주요지표 : 파티완료, 좋아요, 보상";
            $(".rl_grade_score").text($(".radar_06 .radar_pt").text());
            $(".rl_grade_rank").text($(".radar_06").find("em").text());
        }

        $(".rl_desc dl dt").html(rl_dldt);
        $(".rl_desc dd").html(rl_text);
        $(".rl_grade .rl_grade_title").html(rl_title);
    });


    //보상하기
    $(document).on("click", ".rl_user_btn_coin", function(){
        var user_pidx = $("#user_penalty_radar").val();
        // var penalty = $("#user_penalty").val();
        if(user_pidx == '1'){
            alert("해당 유저에게 페널티가 적용되어 코인을 보상할 수 없습니다.");
            return false;
        }

        $('.rl_close').removeClass("live");
        $('.rl_close').addClass("rader");
        var uid = $("#rl_name_id").val();
        if (uid) {
            $("#lr_uid").val(uid);
        }

        if ($("#radar_layer").is(":visible") == true) {
            $("#radar_layer").hide();
            $("#layer_reward").show();
        }

    });




    setTimeout(function() {
        $(".ldl_box").trigger("click");
    }, 100);


    //클릭처리
    $(".ldl_box_in").trigger("click");

    //라이브업무 코인지급하기
    $(document).on("click", "#btn_req_100c", function() {

        var val = $(this).val();
        var fdata = new FormData();

        $("#lr_work_idx").val(val);

        fdata.append("mode", "coin_req_100c");
        fdata.append("val", val);

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
                        var uid = tdata[1];

                        if (result == "complete") {
                            $("#lr_uid").val(uid);
                            $("#layer_reward").show();
                            $(".btn_lr_01").trigger("click");
                        }
                    }
                }
            }
        });
    });


	//좌측 완료한업무 클릭시 해당 유저의 레이어창 띄움
	$(document).on("click", "li[id^=rnc_list]", function() {
		var val = $(this).attr("value"); // 사용자별 email
		$("#user_email").val(val);
        $("#send_userid").val(val);

        todaywork_list();
        todaywork_user_list(val);

        setTimeout(function() {
            //$(".layer_today").show();
            $(document).find("input[id=lives_date]").removeClass('hasDatepicker').datepicker();
        }, 200);

        $(".layer_today .layer_result_right").removeClass("pos_fix");
        $(".layer_result_list.desc_lr_02").css({ opacity: 0 });
        $(".layer_result_list.desc_lr_03").css({ opacity: 0 });
        $(".desc_lr_01 .tdw_list_li").removeClass("sli");
        $(".layer_today").show();
        $(".layer_result_tab li button").removeClass("on");
        $(".layer_result_tab li .btn_lr_01").addClass("on");
        $(".layer_result_list").hide();
        $(".layer_result_list.desc_lr_01").css({ opacity: 1 });
        $(".layer_result_list.desc_lr_01").show();
        $(".desc_lr_01 .tdw_list_li").each(function() {
            var tis = $(this);
            var tindex = $(this).index();
            setTimeout(function() {
                tis.addClass("sli");
            }, 600 + tindex * 200);
        });
	});


	
    //메모 열고 닫기
    /*$(".rew_menu_onoff button").click(function() {
        var thisonoff = $(this);
        var fdata = new FormData();
        fdata.append("mode", "rew_menu_onoff");
        if (thisonoff.hasClass("on")) {
            thisonoff.removeClass("on");
            $(".rew_box").removeClass("on");
            //setCookie('rew_menu_onoff', '1', '365');
            fdata.append("onoff", "1");

        } else {
            thisonoff.addClass("on");
            $(".rew_box").addClass("on");
            fdata.append("onoff", "0");
        }

        $.ajax({
            type: "POST",
            data: fdata,
            //async: false,
            contentType: false,
            processData: false,
            url: '/inc/process.php',
            success: function(data) {
                console.log(data);
            }
        });
    });*/



    /*
    //새로고침(마우스이동, 키입력)
    var time = new Date().getTime();
    $(document.body).bind("mousemove keydown", function() {
        time = new Date().getTime();
    });

    //3분마다 리로드
    //메모작성, 오늘업무 레이어 띄울때는 동작하지않음
    setInterval(function() {
        if (new Date().getTime() - time >= 180000 && ($("#textarea_memo").is(":visible") == false && $(".layer_today").is(":visible") == false)) {
            window.location.reload(true);
        }
    }, 5000);
	*/

	//now_timer();

    //라이브 리스트 더보기
   $("#live_more").click(function(){
        var fdata = new FormData();
        var page = parseInt($("#pageno").val());
        var pcount = parseInt($("#page_count").val());
        var lastcnt;
    
        //페이지
        if (page > 0) {
            page = page + 1;
            }

        if($("#rew_live_sort").val()){
            fdata.append("list_rank", $("#rew_live_sort").val());
        }
        console.log($("#rew_live_sort").val());
            fdata.append("mode", "lives_index_list_new");
            fdata.append("gp", page);

            $.ajax({
                type: "post",
                data: fdata,
                contentType: false,
                processData: false,
                url: '/inc/lives_process.php',
                success: function(data) {
                    if (data) {

                        var tdata = data.split("|");
                        if (tdata) {
                            var result = tdata[0];
                            var listallcnt = tdata[1];
                            var listcnt = tdata[2];
                            var lastcnt = tdata[3];
                            
                            console.log(tdata);
                            
                        $("#pageno").val(page);
                        $("#page_count").val(parseInt(lastcnt));

                        $(".ldr_ul").append(result);


                            setTimeout(function() {
                                live_circle();
                            }, 1000);
                            //더보기 버튼
                            setTimeout(function () {
                                if (page >= lastcnt) {
                                $(".live_more").hide();
                                } else {
                                $(".live_more").show();
                                }
                        }, 10);
                    }
                }
            }
        });
    });

    // 상태메세지 등록
    $(document).on('click', '.submit_btn .btn_on', function(){
        var p_memo = $(".text_area .layer_text").val();
        var char_val = $("#check_profile").val();
        console.log(p_memo);
        
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
                        // console.log(data);
                        if (data) {
                            tdata = data.split("|");
                            if(tdata){
                                // console.log(tdata);
                                var result = tdata[0];
                                var result2 = tdata[1];
                                var result3 = tdata[2];
                                if(result == 'complete'){
                                    if(p_memo == ''){
                                        $('.rew_main_anno_in span').text("상태 메시지를 입력해주세요.");
                                    }else{
                                        $('.ldr_me .ldr_anno span').text(p_memo);
                                    }
                                    if(result2){
                                        $(".user_img").css("background-image", "url(" + result2 + ")");
                                        $(".live_list_user_imgs:first").css("background-image", "url(" + result2 + ")");
                                        $(".ldr_me .ldr_user_imgs").css("background-image", "url(" + result2 + ")");
                                    }
                                    if(result3){
                                        $(".user_img").css("background-image", "url(" + result3 + ")");
                                        $(".live_list_user_imgs:first").css("background-image", "url(" + result3 + ")");
                                        $(".ldr_me .ldr_user_imgs").css("background-image", "url(" + result2 + ")");
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

function profile_img_preview(event) {
  var input = this;
  var id = $(this).attr("id");

  if (input.files && input.files.length) {
      var reader = new FileReader();
      this.enabled = false
      reader.onload = (function(e) {
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
          }
      });
  }
}
});

//숫자3자리 콤마생성
function addComma(value) {
    //console.log(value);
    value = value.replace(/\B(?=(\d{3})+(?!\d))/g, ",");
    return value;
}



function live_circle_new() {
    $(".live_drop_right .ldr_li").each(function() {
        var live_th = $(this);
        var live_st = live_th.find(".ldr_numbers #ldr_today_num").val();
        var live_sp = live_th.find(".ldr_numbers .ldr_today_num strong").text();
        var live_ca = live_st / live_sp;
        var live_co = '';
        var live_ban = 0,
            result_ban = 0;
        var result_ca = parseFloat(live_ca).toFixed(2);

        if (isNaN(result_ca) == false) {
            if (live_st == live_sp) {
                live_co = '#3ac73b';
            } else {
                if (live_ban == live_st) {
                    live_co = '#f7cc07';
                } else {
                    if (result_ca >= 0.68) {
                        if (Number(live_st) >= result_ban) {
                            live_co = '#3ac73b';
                        } else {
                            live_co = '#ec132e';
                        }
                    } else if (result_ca >= 0.34 && result_ca <= 0.67) {
                        live_co = '#f7cc07';
                    } else if (result_ca <= 0.33) {
                        live_co = '#ec132e';
                    }
                }
            }
        } else {
            live_co = "";
        }

        if (live_co) {

            live_th.find(".circle_01").circleProgress({
                startAngle: -Math.PI / 4 * 2,
                value: live_ca,
                thickness: 2,
                size: 64,
                emptyFill: '#ffffff',
                lineCap: 'round',
                fill: { color: live_co },
                animation: {
                    duration: 1200
                }
            });


        }




    });
}


//페널티 이미지 미리보기
function penalty_img_preview() {

    console.log("%%%");

    var input = this;
    var id = $(this).attr("id");
    var no = id.replace("pl_file_", "");

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
        fdata.append("mode", "penalty_img_upload");
        fdata.append("kind", no);
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
                    }, 10);
                }
            }
        });


    }
}

//페널티 리스트
function penalty_all_list() {

    console.log("444");
    var fdata = new FormData();
    fdata.append("mode", "penalty_list");
    fdata.append("lives_date", $("#lives_date").val());
    if ($("#btn_lr_04").hasClass("on") == true) {
        fdata.append("pl", 1);
    }

    $.ajax({
        type: "post",
        data: fdata,
        contentType: false,
        processData: false,
        url: '/inc/lives_process.php',
        success: function(data) {
            console.log("data : " + data);
            if (data) {
                var tdata = data.split("|");
                if (tdata) {
                    var result = tdata[0];
                    var cnt = tdata[1];

                    $("#penalty_list_desc").html(result);
                    $("#pl_cnt").text(cnt);
                    //$("#memo_area_list_" + v).html(data);
                }
            }
        }
    });

}



function live_memo_list(v) { //word_idx = todaywork.idx

    var fdata = new FormData();
    fdata.append("mode", "live_comment");

    fdata.append("lives_date", $("#lives_date").val());
    fdata.append("idx", v);

    //console.log("v :: " + v);

    $.ajax({
        type: "post",
        data: fdata,
        contentType: false,
        processData: false,
        url: '/inc/lives_process.php',
        success: function(data) {
            console.log("data : " + data);
            if (data) {
                $("#tdw_list_memo_area_in_" + v).html(data);
            }
        }
    });

}



//업무리스트
function todaywork_list(type = "") {

    var user_email = $("#user_email").val();
    var input_val = $("#input_user_search").val();

    var fdata = new FormData();
    fdata.append("mode", "todaywork_list");
    fdata.append("user_email", user_email);

    if (input_val) {
        fdata.append("input_val", input_val);
    }

    if (type) {
        fdata.append("type", type);
    }

    if ($("#lives_date").val()) {
        fdata.append("lives_date", $("#lives_date").val());
    }

    if ($("#btn_lr_04").hasClass("on") == true) {

        $("#btn_lr_04").trigger("click");

    } else {

    }

    $.ajax({
        type: "post",
        data: fdata,
        async: false,
        contentType: false,
        processData: false,
        url: '/inc/lives_process.php',
        success: function(data) {
            // console.log("data : " + data);
            if (data) {
                var tdata = data.split("|");
                if (tdata) {
                    var result = tdata[0];
                    var temp_date = tdata[1];
                    $("#todaywork_zone_list").html(result);
                    // console.log(temp_date);
                    
                    if (temp_date) {
                        $(".list_function_calendar input").val(temp_date);
                        //$("#lives_date").val(temp_date);
                        todaywork_user_list();
                    }

                    $(document).find("input[id=lives_date]").removeClass('hasDatepicker').datepicker();

                    $(".layer_today .tdw_list_li").each(function() {
                        var tis = $(this);
                        var tindex = $(this).index();
                        setTimeout(function() {
                            tis.addClass("sli");
                        }, 500 + tindex * 100);
                    });
                }
                length_work = $("div[class~=no_secret_tdw]").length;
                for(var i = 0; i<length_work; i++){
                var divElement = $("div[name=onoff_"+i+"]").parent();
                // // div 하위의 p 엘리먼트 찾기
                var pElement = divElement.find("p");
                // // span 내의 텍스트 가져오기
                var extractedText = pElement.html();
                if(extractedText){
                    var brTagCount = (extractedText.match(/<br>/g) || []).length;
                 }
                // btn_work = $("div[name=onoff_"+i+"]").parent().find("button[id^=btn_list_work_onoff]");
                btn_report = $("div[name=onoff_"+i+"]").parent().find("button[id^=btn_list_report_onoff]");
                btn_share = $("div[name=onoff_"+i+"]").parent().find("button[id^=btn_list_share_onoff]");
                btn_req = $("div[name=onoff_"+i+"]").parent().find("button[id^=btn_list_req_onoff]");
                    if(brTagCount < "3"){
                        // btn_work.css("display","none");
                        btn_share.css("display","none");
                        btn_report.css("display","none");
                        btn_req.css("display", "none");
                    }else{
                        // btn_work.css("display","block");
                        btn_share.css("display","block");
                        btn_report.css("display","block");
                        btn_req.css("display", "block");
                    }
                }

                length_work = $("div[class~=secret_tdw]").length;
                for(var i = 0; i<length_work; i++){
                var divElement = $("div[name=onoff_"+i+"]").parent();
                // // div 하위의 p 엘리먼트 찾기
                var pElement = divElement.find("p");
                // // span 내의 텍스트 가져오기
                var extractedText = pElement.html();
                var brTagCount = (extractedText.match(/<br>/g) || []).length;

                // btn_work = $("div[name=onoff_"+i+"]").parent().find("button[id^=btn_list_work_onoff]");
                btn_report = $("div[name=onoff_"+i+"]").parent().find("button[id^=btn_list_report_onoff]");
                btn_share = $("div[name=onoff_"+i+"]").parent().find("button[id^=btn_list_share_onoff]");
                btn_req = $("div[name=onoff_"+i+"]").parent().find("button[id^=btn_list_req_onoff]");
                    if(brTagCount < "3"){
                        // btn_work.css("display","none");
                        btn_share.css("display","none");
                        btn_report.css("display","none");
                        btn_req.css("display", "none");
                    }else{
                        // btn_work.css("display","block");
                        btn_share.css("display","block");
                        btn_report.css("display","block");
                        btn_req.css("display", "block");
                    }
                }

            }
        }
    });
}



//업무리스트
function todaywork_list_live(type = "") {

    var user_email = $("#user_email").val();
    var input_val = $("#input_user_search").val();

    var fdata = new FormData();
    fdata.append("mode", "todaywork_list");
    fdata.append("user_email", user_email);

    if (input_val) {
        fdata.append("input_val", input_val);
    }

    if (type) {
        fdata.append("type", type);
    }

    if ($("#lives_date").val()) {
        fdata.append("lives_date", $("#lives_date").val());
    }

    if ($("#btn_lr_04").hasClass("on") == true) {

        $("#btn_lr_04").trigger("click");

    } else {

    }

    $.ajax({
        type: "post",
        data: fdata,
        //async: false,
        contentType: false,
        processData: false,
        url: '/inc/lives_process.php',
        success: function(data) {
            // console.log("data : " + data);
            if (data) {
                var tdata = data.split("|");
                if (tdata) {
                    var result = tdata[0];
                    var temp_date = tdata[1];
                    $("#todaywork_zone_list").html(result);
                    if (temp_date) {
                        $(".list_function_calendar input").val(temp_date);
                        //$("#lives_date").val(temp_date);
                        todaywork_user_list();
                    }

                    $(document).find("input[id=lives_date]").removeClass('hasDatepicker').datepicker();

                    $(".layer_today .tdw_list_li").each(function() {
                        var tis = $(this);
                        var tindex = $(this).index();
                        setTimeout(function() {
                            tis.addClass("sli");
                        }, 500 + tindex * 100);
                    });
                }
              
            }
        }
    });
}



//오늘업무, 좌측 회원 리스트
function todaywork_user_list(id="") {

    var fdata = new FormData();
    var input_val = $("#input_user_search").val();
    fdata.append("mode", "todaywork_user_list");

    if (input_val) {
        fdata.append("input_val", input_val);
    }

    if ($("#lives_date").val()) {
        fdata.append("lives_date", $("#lives_date").val());
    }

	if(id){
		fdata.append("rid", id);
	}

    $.ajax({
        type: "post",
        data: fdata,
        async: false,
        contentType: false,
        processData: false,
        url: '/inc/lives_process.php',
        success: function(data) {
            // console.log(data);
            if (data) {
                $(".layer_result_user .layer_result_user_in ul").html(data);
                $(document).find("input[id=lives_date]").removeClass('hasDatepicker').datepicker();
                $(".layer_today .tdw_list_li").each(function() {
                    var tis = $(this);
                    var tindex = $(this).index();
                    setTimeout(function() {
                        tis.addClass("sli");
                    }, 500 + tindex * 100);
                });

              
            }
        }
    });
}



//라이브 리스트
function lives_index_list_new() {
    var fdata = new FormData();
    var page = 1;
    var pcount = parseInt($("#page_count").val());
    var lastcnt;

    
    if($("#rew_live_sort").val()){
        fdata.append("list_rank", $("#rew_live_sort").val());
    }

    fdata.append("mode", "lives_index_list_new");
    fdata.append("gp", page);
    $.ajax({
        type: "post",
        async: false,
        data: fdata,
        contentType: false,
        processData: false,
        url: '/inc/lives_process.php',
        success: function(data) {
            if (data) {

                var tdata = data.split("|");
                if (tdata) {
                    var result = tdata[0];
                    var listallcnt = tdata[1];
                    var listcnt = tdata[2];
                    var lastcnt = tdata[3];

                    $("#pageno").val(page);
                    $("#page_count").val(parseInt(lastcnt));

					$("#ldr .ldr_li").remove();
                    $("#ldr").append(result);
                    $(".ldr_li").trigger("click");

                    // //집중
                    // if ($("#live_2_bt").hasClass("on") == true) {

                    //     var html = ' <li class="state_01">';
                    //     html = html += ' <div class="ldr_user_state_circle">';
                    //     html += ' <strong>집중</strong>';
                    //     html += ' </div>';
                    //     html += ' <div class="layer_state layer_state_01">';
                    //     html += ' <div class="layer_state_in">';
                    //     html += ' <p>업무에 집중하고 있습니다.</p>';
                    //     html += ' <em></em>';
                    //     html += ' </div>';
                    //     html += ' </div>';
                    //     html += ' </li>';

                    //     setTimeout(function() {
                    //         $("#ldr_me_state").html(html);
                    //     }, 100);
						
                    // } else {
                    //     $("#ldr_me_state").html('');
                    // }

                    // //자리비움
                    // if ($("#live_3_bt").hasClass("on") == true) {
                    //     var html = ' <li class="state_02">';
                    //     html = html += ' <div class="ldr_user_state_circle">';
                    //     html += ' <strong>잠시</strong>';
                    //     html += ' </div>';
                    //     html += ' <div class="layer_state layer_state_02">';
                    //     html += ' <div class="layer_state_in">';
                    //     html += ' <p>잠시 자리를 비웁니다.</p>';
                    //     html += ' <em></em>';
                    //     html += ' </div>';
                    //     html += ' </div>';
                    //     html += ' </li>';
                    //     $("#ldr_me_state").html(html);
                    // } else {
                    //     $("#ldr_me_state").html('');
                    // }

                    // $("#lives_list_cnt").html("<span>" + listallcnt + "</span>(" + listcnt + ")");
                    setTimeout(function() {
                        live_circle_new();
                    }, 100);
                    setTimeout(function () {
                        if (page >= lastcnt) {
                        $(".live_more").hide();
                        } else {
                        $(".live_more").show();
                        }
                }, 10);
                }
            }
        }
    });
}



//라이브 리스트
function lives_index_list_new_20230420() {
    var fdata = new FormData();
    fdata.append("mode", "lives_index_list_new_20230420");

    $.ajax({
        type: "post",
        async: false,
        data: fdata,
        contentType: false,
        processData: false,
        url: '/inc/lives_process_yoo.php',
        success: function(data) {
            console.log(data);
            if (data) {

                var tdata = data.split("|");
                if (tdata) {
                    var result = tdata[0];
                    var listallcnt = tdata[1];
                    var listcnt = tdata[2];

                    //$("#ldr .ldr_li:not(.ldr_me)").remove();
					$("#ldr .ldr_li").remove();
                    $("#ldr").append(result);
                    $(".ldr_li").trigger("click");

                    //집중
                    if ($("#live_2_bt").hasClass("on") == true) {

                        var html = ' <li class="state_01">';
                        html = html += ' <div class="ldr_user_state_circle">';
                        html += ' <strong>집중</strong>';
                        html += ' </div>';
                        html += ' <div class="layer_state layer_state_01">';
                        html += ' <div class="layer_state_in">';
                        html += ' <p>업무에 집중하고 있습니다.</p>';
                        html += ' <em></em>';
                        html += ' </div>';
                        html += ' </div>';
                        html += ' </li>';

                        setTimeout(function() {
                            $("#ldr_me_state").html(html);
                        }, 100);
						
                    } else {
                        $("#ldr_me_state").html('');
                    }

                    //자리비움
                    if ($("#live_3_bt").hasClass("on") == true) {
                        var html = ' <li class="state_02">';
                        html = html += ' <div class="ldr_user_state_circle">';
                        html += ' <strong>잠시</strong>';
                        html += ' </div>';
                        html += ' <div class="layer_state layer_state_02">';
                        html += ' <div class="layer_state_in">';
                        html += ' <p>잠시 자리를 비웁니다.</p>';
                        html += ' <em></em>';
                        html += ' </div>';
                        html += ' </div>';
                        html += ' </li>';
                        $("#ldr_me_state").html(html);
                    } else {
                        $("#ldr_me_state").html('');
                    }

                    // $("#lives_list_cnt").html("<span>" + listallcnt + "</span>(" + listcnt + ")");
                    setTimeout(function() {
                        live_circle_new();
                    }, 100);

                }
            }
        }
    });
}




//라이브 리스트_새로고침_
function lives_index_to_work() {
    var fdata = new FormData();
    fdata.append("mode", "lives_index_list_new");

    $.ajax({
        type: "post",
        data: fdata,
        contentType: false,
        processData: false,
        url: '/inc/lives_process.php',
        success: function(data) {
            //console.log(" ddd :: " + data);
            if (data) {

                var tdata = data.split("|");
                if (tdata) {
                    var result = tdata[0];
                    var listallcnt = tdata[1];
                    var listcnt = tdata[2];


                    //$("#ldr .ldr_li:not(.ldr_me)").remove();
                    $("#ldr").html(result);
                    $(".ldr_li").trigger("click");

                    //집중
                    if ($("#live_2_bt").hasClass("on") == true) {

                        var html = ' <li class="state_01">';
                        html = html += ' <div class="ldr_user_state_circle">';
                        html += ' <strong>집중</strong>';
                        html += ' </div>';
                        html += ' <div class="layer_state layer_state_01">';
                        html += ' <div class="layer_state_in">';
                        html += ' <p>업무에 집중하고 있습니다.</p>';
                        html += ' <em></em>';
                        html += ' </div>';
                        html += ' </div>';
                        html += ' </li>';

                        setTimeout(function() {
                            $("#ldr_me_state").html(html);
                        }, 100);
                    } else {
                        $("#ldr_me_state").html('');
                    }

                    //자리비움
                    if ($("#live_3_bt").hasClass("on") == true) {
                        var html = ' <li class="state_02">';
                        html = html += ' <div class="ldr_user_state_circle">';
                        html += ' <strong>잠시</strong>';
                        html += ' </div>';
                        html += ' <div class="layer_state layer_state_02">';
                        html += ' <div class="layer_state_in">';
                        html += ' <p>잠시 자리를 비웁니다.</p>';
                        html += ' <em></em>';
                        html += ' </div>';
                        html += ' </div>';
                        html += ' </li>';
                        $("#ldr_me_state").html(html);
                    } else {
                        $("#ldr_me_state").html('');
                    }



                    // $("#lives_list_cnt").html("<span>" + listallcnt + "</span>(" + listcnt + ")");

                    ////$(".live_list .live_list_box").each(function() {
                    ////    var tis = $(this);
                    //var tindex = $(this).index();
                    /*var bar_t = tis.find(".live_list_today_bar strong").text();
                        var bar_b = tis.find(".live_list_today_bar span").text();
                        var bar_w = bar_t / bar_b * 100;
						*/

                    /*setTimeout(function() {
                            tis.addClass("slii");
                            //tis.find(".live_list_today_bar strong").css({ width: bar_w + "%" });
                        }, 0);
						*/
                    ////});

                    setTimeout(function() {
                        live_circle_new();
                    }, 1000);

                }
            }
        }
    });
}

function lives_index_list() {
    var fdata = new FormData();
    fdata.append("mode", "lives_index_list");

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
                    var listallcnt = tdata[1];
                    var listcnt = tdata[2];

                    $("#lives_index_list").html(result);
                    // $("#lives_list_cnt").html("<span>" + listallcnt + "</span>(" + listcnt + ")");
                    $(".live_list .live_list_box").each(function() {
                        var tis = $(this);
                        //var tindex = $(this).index();
                        /*var bar_t = tis.find(".live_list_today_bar strong").text();
                        var bar_b = tis.find(".live_list_today_bar span").text();
                        var bar_w = bar_t / bar_b * 100;
						*/

                        setTimeout(function() {
                            tis.addClass("slii");
                            //tis.find(".live_list_today_bar strong").css({ width: bar_w + "%" });
                        }, 0);
                    });

                    setTimeout(function() {
                        live_circle();
                    }, 1000);
                }
            }
        }
    });
}


//라이브 리스트
function lives_index_list() {
    var fdata = new FormData();
    fdata.append("mode", "lives_index_list");

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
                    var listallcnt = tdata[1];
                    var listcnt = tdata[2];

                    $("#lives_index_list").html(result);
                    // $("#lives_list_cnt").html("<span>" + listallcnt + "</span>(" + listcnt + ")");
                    $(".live_list .live_list_box").each(function() {
                        var tis = $(this);
                        //var tindex = $(this).index();
                        /*var bar_t = tis.find(".live_list_today_bar strong").text();
                        var bar_b = tis.find(".live_list_today_bar span").text();
                        var bar_w = bar_t / bar_b * 100;
						*/

                        setTimeout(function() {
                            tis.addClass("slii");
                            //tis.find(".live_list_today_bar strong").css({ width: bar_w + "%" });
                        }, 0);
                    });

                    setTimeout(function() {
                        live_circle();
                    }, 1000);
                }
            }
        }
    });
}


//파티생성
function ltb() {
    $(".lt_bottom button").trigger("click");
}



//오늘날짜
function today_date() {
    var today = new Date();
    var dd = today.getDate();
    var mm = today.getMonth() + 1; //January is 0!
    var yyyy = today.getFullYear();
    if (dd < 10) {
        dd = '0' + dd
    }
    if (mm < 10) {
        mm = '0' + mm
    }
    today = yyyy + '.' + mm + '.' + dd;

    return today;
}


var TimeScript = 1000 * 60 * 60; //1초 * 60 * 60
//var TimeScript = 10000;
//setInterval("jsRealTimePHP()", TimeScript);


//자동실행 1시간 마다 실행 처리
function jsRealTimePHP() {
    var fdata = new FormData();
    fdata.append("mode", "lives_10");
    $.ajax({
        type: "GET",
        data: fdata,
        contentType: false,
        processData: false,
        url: "/inc/live_10_update.php",
        success: function(data) {
            console.log(data);
        }
    });
}



//그래프 다시 재설정하기
function live_circle() {

    $(".live_list .live_list_box").each(function() {
        var live_th = $(this);
        var live_st = live_th.find(".live_list_today .live_list_today_count strong").text();
        var live_sp = live_th.find(".live_list_today .live_list_today_count span").text();
        var live_ca = live_st / live_sp;
        var live_co = 0;

        var email = live_th.find(".live_list_today .live_list_today_count").parent("button").val();
        //var live_hap = live_st / 3;
        //var live_ban = live_sp / 2;
        var live_ban = 0,
            result_ban = 0;

        var result_ca = parseFloat(live_ca).toFixed(2);
        //var result_ban = parseFloat(live_ban).toFixed(1);


        /*
		console.log("e : " + email + ", " + live_st + " / " + live_sp);
        console.log("st : " + Number(live_st) + ", " + result_ban);
        console.log("result_ca : " + result_ca + "");
		*/

        //숫자형일때
        if (isNaN(result_ca) == false) {
            if (live_st == live_sp) {
                live_co = '#3ac73b';
            } else {
                if (live_ban == live_st) {
                    live_co = '#f7cc07';
                } else {
                    if (result_ca >= 0.68) {
                        if (Number(live_st) >= result_ban) {
                            live_co = '#3ac73b';
                        } else {
                            live_co = '#ec132e';
                        }
                    } else if (result_ca >= 0.34 && result_ca <= 0.67) {
                        live_co = '#f7cc07';
                    } else if (result_ca <= 0.33) {
                        //if (Number(live_st) >= result_ban) {
                        //    live_co = '#f7cc07';
                        //} else {
                        live_co = '#ec132e';
                        //}
                    }
                }
            }
        } else {
            live_co = "";
        }

        if (live_co) {

            //console.log(email);
            /*setTimeout(function() {
                live_th.find(".circle_01").circleProgress({
                    startAngle: -Math.PI / 4 * 2,
                    value: live_ca,
                        thickness: 2,
                    size: 104,
                     lineCap: 'round',
                    fill: { color: live_co },
                    animation: {
                        duration: 1200
                    }
                });
            }, 100);*/


            live_th.find(".circle_01").circleProgress({
                startAngle: -Math.PI / 4 * 2,
                value: live_ca,
                size: 104,
                emptyFill: '#e5e5e5',
                fill: { color: live_co }
            });

        }
    });
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

			var penalty_send = '1';
			var end = new Date(now.getFullYear(), now.getMonth(), now.getDate(), late_etime, 59, 59);
			var open = new Date(now.getFullYear(), now.getMonth(), now.getDate(), late_stime, 00, 00);

            var nt = now.getTime();
            var ot = open.getTime();
            var et = end.getTime();
            var obj = $(".pl_right_02 .pl_time strong");


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


function rl_chart_run(t1, t2, t3, t4, t5, t6) {

    var rl_chart = bb.generate({
        data: {
            x: "x",
            columns: [
                ["x", "지식", "성장", "성실", "실행", "협업", "성과"],
                //지식 type1, //성과 type2, //성장 type3, //협업 type4, //성실 type5, //실행 type6
                ["역량평가 리포트", t1, t3, t5, t6, t4, t2]
            ],
            color: "#aaa",
            type: "radar",
            labels: false,
            colors: {
                "역량평가 리포트": "#38c9d2"
            }
        },
        size: {
            //width: 400,
            height: 256
        },
        radar: {
            axis: {
                max: 100
            },
            level: {
                depth: 4
            },
            direction: {
                clockwise: true
            }
        },
        tooltip: {
            show: false
        },
        point: {
            show: true
        },
        transition: {
            duration: 500
        },
        bindto: "#rl_radarChart"
    });
}


//시간표기
function now_clock(){
	var now_date = new Date();
	var now_hours = now_date.getHours();
	var now_minutes = now_date.getMinutes();
	var now_seconds = now_date.getSeconds();
	var hours_00 = now_hours < 10 ? "0"+now_hours : now_hours;
	var minutes_00 = now_minutes < 10 ? "0"+now_minutes : now_minutes;
	var seconds_00 = now_seconds < 10 ? "0"+now_seconds : now_seconds;
	$(".rew_now_timer strong").html(hours_00+"<em>:</em>"+minutes_00+"<em>:</em>"+seconds_00);
	$(".rew_now_timer").addClass("on");
}


//시간표기
function now_timer() {
	//now_clock();
	//setInterval(now_clock, 1000);
}


//라이브 사이클 실행
setTimeout(function() {
    live_circle_new();
}, 1000);

link_href = window.location.href;
var link_arr = link_href.split("/");
home_title = link_arr[3];

 //첨부파일 다운로드

 if(home_title == "live"){
 $(document).on("click", "button[id^=btn_list_file]", function () {
    var url = "/inc/file_download.php";
    var num;
    var id = $(this).attr("id");

    if (id) {
      var num = id.replace("btn_list_file_", ""); //$k
      var idx = $(this).val(); 
      var mode = "todaywork";
      var params = { idx: idx, num: num, mode: mode };
      console.log(num+"|"+idx);
      var fdata = new FormData();
      fdata.append("mode", mode);
      fdata.append("idx", idx);
      fdata.append("num", num);

      $.ajax({
        type: "POST",
        data: fdata,
        contentType: false,
        processData: false,
        url: url,
        success: function (data) {
          console.log(data);
          if (data) {
            var tdata = data.split("|");
            if (tdata) {
              var f_result = tdata[0];
              var f_name = tdata[1];
              var f_url = tdata[2];
              if (f_result == "complete") {
                fdownload(f_name, f_url);
              }
            }
          }
        },
      });
    }
  });
}
  $(document).on("click", "button[id=btn_list_fdel]", function () {
    var val = $(this).val();
    if (val) {
        var work_id_idx = $(this)
        .parent()
        .parent()
        .parent()
        .attr("id");
      var work_idx = work_id_idx.replace("workslist_", "");

      if (confirm("첨부파일을 삭제하시겠습니까?")) {
        //if ($(".select_ww").hasClass("on") == true) {
        //} else {
        var wdate = $("#work_date").val();
        //}

        if (GetCookie("user_id") == "sadary0@nate.com") {
          //console.log("work_idx :: " + work_idx);
          //return false;
        }

        var fdata = new FormData();
        fdata.append("mode", "works_files_del");
        fdata.append("file_idx", val);
        //fdata.append("wdate", wdate);
        fdata.append("work_idx", work_idx);

        $.ajax({
          type: "POST",
          data: fdata,
          contentType: false,
          processData: false,
          url: "/inc/works_process.php",
          success: function (data) {
            console.log(data);
            if (data == "complete") {
              todaywork_list();
              return false;
            } else if (data == "logout") {
              $(".t_layer").show();
              return false;
            }
          },
        });
      }
    }
  });




  if(home_title == "live"){
    $(document).on("click", "button[id^=btn_list_share_onoff]", function () {
        var val = $(this).val(); // work_todayworks.idx
        if (val) {
        $("#tdw_list_share_area_in_" + val).toggleClass("off");

        $(this).toggleClass("off");

        var fdata = new FormData();
        fdata.append("mode", "btn_list_share_onoff");
        fdata.append("work_idx", val);

        if ($("#tdw_list_box_" + val).height() == 40) {
            tdw_height =$("#tdw_list_box_" + val).height();

            $("#btn_list_share_onoff_" + val).removeClass("off");
            $("#btn_list_share_onoff_" + val).addClass("on");

            $("#tdw_list_box_" + val).removeClass("off")

            fdata.append("onoff", "0");
            $("#btn_list_share_onoff_" + val).attr("title", "공유 접기");
        } else {

            $(".tdw_list_share_onoff").removeClass("off");
            $("#btn_list_share_onoff_" + val).removeClass("on");

            $("#tdw_list_box_" + val).addClass("off");

            fdata.append("onoff", "1");

            $("#btn_list_share_onoff_" + val).attr("title", "공유 펼치기");
        }

        $.ajax({
            type: "POST",
            data: fdata,
            //async: false,
            contentType: false,
            processData: false,
            url: "/inc/works_process.php",
            success: function (data) {
                console.log(tdw_height);
            console.log(data);
            if (data == "complete") {
                memo_line_check();
            }
            },
        });
        }
    });

    //요청내용 열기/접기(일일)
    $(document).on("click", "button[id^=btn_list_req_onoff]", function () {
        var memo_onoff = $(this);
        var val = $(this).val();
        
        console.log($("#tdw_list_box_" + val).height());
        // $("#tdw_list_box_" + val).toggleClass("off");
        if (val) {
        var fdata = new FormData();
        fdata.append("mode", "btn_list_req_onoff");
        fdata.append("work_idx", val);

        //console.log ( " >>> " + $("#tdw_list_box_"+val).height() );

        if ($("#tdw_list_box_" + val).height() == "40") {
            $("#btn_list_req_onoff_" + val).removeClass("off");
            $("#btn_list_req_onoff_" + val).addClass("on");

            $("#tdw_list_box_" + val).removeClass("off");
            fdata.append("onoff", "0");
        } else {
            $("#btn_list_req_onoff_" + val).removeClass("on");
            $("#btn_list_req_onoff_" + val).addClass("off");
            
            $("#tdw_list_box_" + val).addClass("off");
            fdata.append("onoff", "1");
        }

        $.ajax({
            type: "POST",
            data: fdata,
            //async: false,
            contentType: false,
            processData: false,
            url: "/inc/works_process.php",
            success: function (data) {
            console.log(data);
            if (data == "complete") {
                memo_line_check();
            }
            },
        });
        }
    });

    //0831 메모 열기/접기(일일업무)
    $(document).on("click", "button[id^=btn_list_memo_onoff]", function () {
        var memo_onoff = $(this);
        var val = $(this).val();

        if (val) {
        $("#tdw_list_memo_area_in_" + val).toggleClass("off");
        //$("#memo_area_list_" + val).toggleClass("off");
        $(this).toggleClass("off");

        var fdata = new FormData();
        fdata.append("mode", "btn_list_memo_onoff");
        fdata.append("work_idx", val);

        if (memo_onoff.hasClass("off")) {
            memo_onoff.removeClass("on");
            $("#btn_list_memo_onoff_" + val).removeClass("on");
            fdata.append("onoff", "1");
            $("#btn_list_memo_onoff_" + val).attr("title", "메모 펼치기");

            //console.log("접기");
        } else {
            memo_onoff.addClass("on");
            $("#btn_list_memo_onoff_" + val).addClass("on");
            fdata.append("onoff", "0");

            $("#btn_list_memo_onoff_" + val).attr("title", "메모 접기");

            //console.log("펼치기");
        }

        $.ajax({
            type: "POST",
            data: fdata,
            //async: false,
            contentType: false,
            processData: false,
            url: "/inc/works_process.php",
            success: function (data) {
            console.log(data);
            if (data == "complete") {
                memo_line_check();
            }
            },
        });
        }
    });

    //보고업무내용 열기/접기(일일)
    $(document).on("click", "button[id^=btn_list_report_onoff]", function () {
        var memo_onoff = $(this);
        var val = $(this).val();
        if (val) {
        $("#tdw_list_report_area_in_" + val).toggleClass("off");
        //$("#memo_area_list_" + val).toggleClass("off");
        $(this).toggleClass("off");

        var fdata = new FormData();
        fdata.append("mode", "btn_list_report_onoff");
        fdata.append("work_idx", val);

        if (memo_onoff.hasClass("off")) {
            memo_onoff.removeClass("on");
            $("#btn_list_report_onoff_" + val).removeClass("on");
            fdata.append("onoff", "1");
            $("#btn_list_report_onoff_" + val).attr("title", "보고 펼치기");
        } else {
            memo_onoff.addClass("on");
            $("#btn_list_report_onoff_" + val).addClass("on");
            fdata.append("onoff", "0");

            $("#btn_list_report_onoff_" + val).attr("title", "보고 접기");
        }

        $.ajax({
            type: "POST",
            data: fdata,
            //async: false,
            contentType: false,
            processData: false,
            url: "/inc/works_process.php",
            success: function (data) {
            console.log(data);
            if (data == "complete") {
                memo_line_check();
            }
            },
        });
        }
    });
    }
 

  function memo_line_check() {
    setTimeout(function () {
      $(".tdw_list_memo_area_in").each(function () {
        var maih = $(this);
        var id = $(this).attr("id");
        //var no = id.replace("tdw_list_memo_area_in_","");
        var id_height = $("#" + id).height();
        //console.log(id);
        //console.log(maih.height());
        if ($(".select_dd").hasClass("on")) {
          no = id.replace("tdw_list_memo_area_in_", "");
  
          if ($("#btn_list_memo_onoff_" + no).hasClass("on")) {
            $("#btn_list_memo_onoff_" + no).attr("title", "메모 접기");
          } else {
            $("#btn_list_memo_onoff_" + no).attr("title", "메모 펼치기");
          }
        } else if ($(".select_ww").hasClass("on")) {
          no = id.replace("tdw_list_memo_area_in_", "");
  
          if ($("#btn_list_memo_onoff_" + no).hasClass("on")) {
            $("#btn_list_memo_onoff_" + no).attr("title", "메모 접기");
          } else {
            $("#btn_list_memo_onoff_" + no).attr("title", "메모 펼치기");
          }
        }
  
        if (id) {
          if (maih.height() > 110) {
            //if (id_height > 110) {
            //maih.next($(".tdw_list_memo_onoff")).show();
            maih.next($("#" + id)).show();
            //$("#" + id).show();
          } else {
            //maih.next($(".tdw_list_memo_onoff")).hide();
            //    maih.next($("#" + id)).hide();
            //$("#" + id).hide();
          }
        }
      });
  
      /*if (GetCookie("user_id") == "sadary0@nate.com"){
        $(".tdw_list_box").each(function() {
          var maih = $(this);
          var id = $(this).attr("id");
          if (id) {
            if (maih.height() > 110) {
              maih.next($("#" + id)).show();
            } else {
              maih.next($("#" + id)).hide();
            }
          }
        });
      }*/
    }, 400);
  }

//   $(document).on("click",".tdw_list_li",function(){
//     content = $(this).children().find(".tdw_list_desc").height();
//     console.log(content);
//   });