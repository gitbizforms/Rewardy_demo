$(function() {


    //속성변경
    $("#a1").css("ime-mode", "inactive");

    $(".tab_shop").attr("disabled", false);
    $(".tab_shop").css('color', '#ccc');

    $(".tab_buy").attr("disabled", false);
    $(".tab_buy").css('color', '#ccc');


    //챌린지버튼
    //$("button[id^='chall_days']").css("display","none");

    //챌린지 참여자목록
    $("#add_user_list").css("display", "none");
    //$(".works_today").css('z-index', 9999);

    $("#h5").attr("maxlength", 2);
    $("input[name=date1],input[name=date2],input[name=date_02],input[name=date_03],input[name=date_04]").attr("maxlength", 10);
    $("input[name=coin_point],input[name=works_today]").attr("maxlength", 10);


    //$(".works_today").zindex($(document).zindex() + 999);

    //$(document).on('propertychange change keyup paste input', 'input[name=works_today]', function(event) {

    $("#works_today").on("propertychange change keyup paste input", function() {

        //$("input[name=works_today]").change(function() {
        //$("input[name=works_today]").on("propertychange change keyup paste input", function() {
        //$("input[name=works_today]").change("change keyup input", function(event) {

        //console.log(" wdate :: " + $(this).val());
        console.log(999999999);

    });


    $(document).on('keyup', 'input[name=coin_point],input[id=h4],input[id=h5]', function(event) {
        this.value = this.value.replace(/[^0-9]/g, ''); // 입력값이 숫자가 아니면 공백
        this.value = this.value.replace(/,/g, ''); // ,값 공백처리
        this.value = this.value.replace(/\B(?=(\d{3})+(?!\d))/g, ","); // 정규식을 이용해서 3자리 마다 , 추가 	
    });


    if ($(window).width() > 640) {
        $(".menu_open").mouseenter(function() {
            if ($(".menu_box").hasClass("off")) {

            } else {
                $(".menu_box").show();
            }
        });

        $(".menu_list").mouseleave(function() {
            $(".menu_box").removeClass("off");
            $(".menu_box").hide();
        });
    } else {

    }

    $(".menu_open").click(function() {
        $(".menu_box").removeClass("off");
        $(".menu_box").show();
    });

    $(".menu_close").click(function() {
        $(".menu_box").addClass("off");
        $(".menu_box").hide();
    });

    $(window).resize(function() {
        if ($(window).width() > 640) {
            $(".menu_open").mouseenter(function() {
                if ($(".menu_box").hasClass("off")) {

                } else {
                    $(".menu_box").show();
                }
            });

            $(".menu_list").mouseleave(function() {
                $(".menu_box").removeClass("off");
                $(".menu_box").hide();
            });
        } else {

        }
    });


    //로그인하기
    $("input").keypress(function(e) {
        var id = $(this).attr("id");
        if (id == "z1" || id == "z2") {
            if (e.keyCode == 13) {
                $("#loginbtn").trigger("click");
            }
        }
    });


    //$(document).on("click", ".tc_request strong", function() {
    //});

    $("input[name=works_today]").bind("change keyup input", function(event) {
        if (!(event.keyCode >= 37 && event.keyCode <= 40)) {
            var inputVal = $(this).val();
            $(this).val(inputVal.replace(/[^0-9.]/gi, ''));
        }
    });


    $("input[name=coin_point], #h4,#h5").bind("change keyup input", function(event) {
        if (!(event.keyCode >= 37 && event.keyCode <= 40)) {
            var inputVal = $(this).val();
            $(this).val(inputVal.replace(/[^0-9,]/gi, ''));
        }
    });


    $("#date1,#date2,#date_02,#date_03,#date_04,#goal3,input[id^=workdate]").bind("change keyup input", function(event) {
        if (!(event.keyCode >= 37 && event.keyCode <= 40)) {
            var inputVal = $(this).val();
            $(this).val(inputVal.replace(/[^0-9-]/gi, ''));
        }
    });

    $("#req_date,#date_sdate").bind("change keyup input", function(event) {
        if (!(event.keyCode >= 37 && event.keyCode <= 40)) {
            var inputVal = $(this).val();
            $(this).val(inputVal.replace(/[^0-9-]/gi, ''));
        }
    });

    $("#req_stime,#req_etime,#date_stime").bind("change keyup input", function(event) {
        if (!(event.keyCode >= 37 && event.keyCode <= 40)) {
            var inputVal = $(this).val();
            $(this).val(inputVal.replace(/[^0-9:]/gi, ''));
        }
    });

    //서비스가입버튼
    $("#joinchk").click(function() {

        if (!$("#a1").val()) {
            alert("이메일을 입력해주세요.");
            $("#a1").focus();
            return false;
        }

        if (!$("#a2").val()) {
            alert("이름을 입력해주세요.");
            $("#a2").focus();
            return false;
        }

        if (!$("#a3").val()) {
            alert("비밀번호를 입력해주세요.");
            $("#a3").focus();
            return false;
        }

        if (!$("#a4").val()) {
            alert("비밀번호를 입력해주세요.");
            $("#a3").focus();
            return false;
        }

        if ($("#a3").val() != $("#a4").val()) {
            alert("비밀번호가 일치하지 않습니다.");
            $("#a3").focus();
            return false;
        }

        if (!$("#a5").val()) {
            alert("회사명을 입력해주세요.");
            $("#a5").focus();
            return false;
        }

        if (!$("#a6").val()) {
            alert("부서명을 입력해주세요.");
            $("#a6").focus();
            return false;
        }

        if (confirm("입력하신 내용으로 서비스 가입을 하시겠습니까?")) {

            var fdata = new FormData();
            fdata.append("mode", "join");
            fdata.append("email", $("#a1").val());
            fdata.append("name", $("#a2").val());
            fdata.append("password", $("#a3").val());
            fdata.append("password_chek", $("#a4").val());
            fdata.append("corp", $("#a5").val());
            fdata.append("part", $("#a6").val());

            $.ajax({
                type: "POST",
                data: fdata,
                contentType: false,
                processData: false,
                url: '/inc/process.php',
                success: function(data) {

                    if (data == "complete") {
                        alert("가입되었습니다.");
                        location.href = "/admin/pay.php";
                        return false;

                    } else if (data == "rejoin") {
                        alert("이미 가입된 정보 입니다.");
                    }
                }
            });
        }
    });


    //사용자인증체크
    $("#userchk").click(function() {

        if (!$("#d2").val()) {
            alert("이메일을 입력해주세요.");
            $("#d2").focus();
            return false;
        }

        if (!$("#d3").val()) {
            alert("이름을 입력해주세요.");
            $("#d3").focus();
            return false;
        }

        if (!$("#d6").val()) {
            alert("부서명을 입력해주세요.");
            $("#d6").focus();
            return false;
        }

        if (!$("#d4").val()) {
            alert("비밀번호를 입력해주세요.");
            $("#d4").focus();
            return false;
        }

        if (!$("#d5").val()) {
            alert("비밀번호를 입력해주세요.");
            $("#d5").focus();
            return false;
        }

        if ($("#d4").val() != $("#d5").val()) {
            alert("비밀번호가 일치하지 않습니다.");
            $("#d4").focus();
            return false;
        }

        if (confirm("입력하신 내용으로 사용자 인증 하시겠습니까?")) {

            var fdata = new FormData();
            fdata.append("mode", "user");
            fdata.append("corp", $("#d1").val());
            fdata.append("name", $("#d3").val());
            fdata.append("email", $("#d2").val());
            fdata.append("part", $("#d6").val());
            fdata.append("password", $("#d4").val());
            fdata.append("password_chek", $("#d5").val());

            $.ajax({
                type: "POST",
                data: fdata,
                contentType: false,
                processData: false,
                url: '/inc/process.php',
                success: function(data) {
                    if (data == "complete") {
                        alert("사용자 인증이 완료 되었습니다.");
                        location.href = "/index.php";
                        return false;

                    } else if (data == "reuser") {
                        alert("이미 인증된 정보 입니다.");
                    }
                }
            });
        }
    });


    //결제버튼
    $("#paybtn").click(function() {

        if (confirm("선택한 " + $("#paycnt").val() + "명의 사용자에게 초대 이메일을 발송하시겠습니까?")) {
            var fdata = new FormData();
            fdata.append("mode", "pay");
            fdata.append("paycnt", $("#paycnt").val());

            $.ajax({
                type: "POST",
                data: fdata,
                contentType: false,
                processData: false,
                url: '/inc/process.php',
                success: function(data) {

                    console.log(data);
                    var tmp = data.split("|");
                    if (tmp[0] == "ok") {

                        var number = tmp[1];
                        //location.href = "/admin/member.php";

                        var form = document.createElement("form");
                        var hiddenField = document.createElement("input");
                        hiddenField.setAttribute('type', 'hidden');
                        hiddenField.setAttribute('name', 'number'); // 받을 네이밍
                        hiddenField.setAttribute('value', number); // 넘길 파라메터
                        form.appendChild(hiddenField);
                        form.setAttribute("method", "POST");
                        form.setAttribute('action', "/admin/member.php"); // URL
                        document.body.appendChild(form);
                        form.submit();
                        return false;
                    }
                }
            });
        }
    });


    $("#z1,#z2").blur(function() {
        var val = $(this).val();
        if (val) {
            $(this).parent().addClass("now_focus");
        } else {
            $(this).parent().removeClass("now_focus");
        }
    });


    //로그인버튼
    $("#loginbtn").click(function() {

        //setTimeout(function() { $('input[name="user_id"]').focus() }, 500);

        if (!$("#z1").val()) {
            alert("이메일 입력하세요.");
            $("#z1").focus();
            return false;
        }

        if (!$("#z2").val()) {
            alert("비밀번호를 입력하세요.");
            $("#z2").focus();
            return false;
        }

        var fdata = new FormData();
        fdata.append("mode", "login");
        fdata.append("id", $("#z1").val());
        fdata.append("pwd", $("#z2").val());

        if ($("input[name='chk_login']").is(":checked") == true) {
            fdata.append("chk_login", true);
        }

        $.ajax({
            type: "POST",
            data: fdata,
            contentType: false,
            processData: false,
            url: '/inc/login_ok.php',
            success: function(data) {
                console.log(data);
                //return false;
                if (data == "use_ok") {
                    //location.replace("/index.php");
                    location.reload();
                    return false;
                } else if (data == "ad_ok") {
                    location.replace("/admin/pay.php");
                    return false;
                } else if (data == "use_deny") {
                    alert("이메일 주소 및 비밀번호를 확인 해주세요.");
                    return false;
                }
            }
        });
    });


    //로그아웃버튼
    $("#logout_btn").click(function() {
        //location.replace("/inc/logout.php");

        var fdata = new FormData();
        fdata.append("mode", "logout");
        $.ajax({
            type: "POST",
            data: fdata,
            contentType: false,
            processData: false,
            url: '/inc/logout.php',
            success: function(data) {
                if (data == "ok") {
                    location.replace("/index.php");;
                    return false;
                }
            }
        });
    });


    //메일발송버튼
    $("#sendmail").click(function() {

        var fdata = new FormData();
        var mail_cnt = $("input[id^='mail']").length;
        for (var i = 0; i < mail_cnt; i++) {
            if (!$("input[id=mail" + i + "]").val()) {
                alert("이메일을 입력하세요.");
                $("input[id=mail" + i + "]").focus();
                return false;
            }

            fdata.append("email[" + i + "]", $("input[id=mail" + i + "]").val());
        }

        if (confirm("입력한 " + mail_cnt + "개의 초대 이메일을 발송하시겠습니까?")) {
            fdata.append("mode", "sendmail");
            $.ajax({
                type: "POST",
                data: fdata,
                contentType: false,
                processData: false,
                url: '/inc/sendmail.php',
                success: function(data) {
                    console.log(data);
                    if (data == "ok") {
                        alert("메일이 정상 발송되었습니다.");
                        location.replace("/index.php");
                    } else if (data == "fail") {
                        alert("메일 발송이 되지 않았습니다.");
                        return false;
                    }
                }
            });
        }
    });


    //할일
    $(".tab_work").click(function() {
        $(".tc_index_tab button").removeClass("on");
        $(this).addClass("on");
        $(".tc_index_box").hide();

        if ($("textarea[name='wdate_contents']").val()) {
            $("textarea[name='wdate_contents']").val('');
            $(".tc_input").removeClass("now_focus");
        }

        if ($("textarea[name='req_contents']").val()) {
            $("textarea[name='req_contents']").val('');
            $(".tc_input").removeClass("now_focus");
        }

        if ($("textarea[name='goal1']").val()) {
            $("textarea[name='goal1']").val('');
            $(".tc_input").removeClass("now_focus");
        }

        if ($("textarea[name='goal2']").val()) {
            $("textarea[name='goal2']").val('');
            $(".tc_input").removeClass("now_focus");
        }

        $("#tab_work").show();
    });


    //일정
    $(".tab_date").click(function() {
        $(".tc_index_tab button").removeClass("on");
        $(this).addClass("on");
        $(".tc_index_box").hide();

        if ($("textarea[name='contents']").val()) {
            $("textarea[name='contents']").val('');
            $(".tc_input").removeClass("now_focus");
        }

        if ($("textarea[name='wdate_contents']").val()) {
            $("textarea[name='wdate_contents']").val('');
            $(".tc_input").removeClass("now_focus");
        }

        if ($("textarea[name='req_contents']").val()) {
            $("textarea[name='req_contents']").val('');
            $(".tc_input").removeClass("now_focus");
        }

        if ($("textarea[name='goal1']").val()) {
            $("textarea[name='goal1']").val('');
            $(".tc_input").removeClass("now_focus");
        }

        if ($("textarea[name='goal2']").val()) {
            $("textarea[name='goal2']").val('');
            $(".tc_input").removeClass("now_focus");
        }

        $("#tab_date").show();
    });


    //업무요청 요청자 변경시
    $(document).on("click", ".tc_request strong", function() {

        if ($(".tc_request_user").is(":visible") == true) {
            $(".tc_request_user").hide();
            //$("input[name^='chkuseall']").prop("checked", false);
            //$("input[name^='requsechk']").prop("checked", false);
        }
        $(this).parent().parent().next(".tc_request_user").show();

    });


    //업무요청 요청자 수정
    $(document).on("click", ".tc_request_user button", function() {
        //$(".tc_request_user").hide();
        var fdata = new FormData();
        var req_idx = $(this).val();
        if (req_idx) {

            chkobj = $("input[name=requsechk" + req_idx + "]");
            var checkCount = chkobj.size();

            fdata.append("mode", "req_user_edit");
            fdata.append("editidx", $(this).val());

            for (var i = 0; i < checkCount; i++) {
                if (chkobj.eq(i).is(":checked")) {
                    var rno = chkobj.eq(i).attr("id").replace("requsechk", "");
                    fdata.append("requsechk[]", $("#requsechk" + rno).val());
                }
            }

            $.ajax({
                type: "POST",
                data: fdata,
                contentType: false,
                processData: false,
                url: '/inc/process.php',
                success: function(data) {
                    //console.log(data);
                    if (data == "complete") {
                        if ($(".tab_day").hasClass("on") == true) {
                            $(".tab_day").trigger("click");
                        } else if ($(".tab_week").hasClass("on") == true) {
                            $(".tab_week").trigger("click");
                        }
                        return false;
                    }
                }
            });
        }
    });


    //업무요청
    $(".tab_request").click(function() {
        $(".tc_index_tab button").removeClass("on");
        $(this).addClass("on");
        $(".tc_index_box").hide();

        if ($("textarea[name='contents']").val()) {
            $("textarea[name='contents']").val('');
            $(".tc_input").removeClass("now_focus");
        }

        if ($("textarea[name='wdate_contents']").val()) {
            $("textarea[name='wdate_contents']").val('');
            $(".tc_input").removeClass("now_focus");
        }

        if ($("textarea[name='goal1']").val()) {
            $("textarea[name='goal1']").val('');
            $(".tc_input").removeClass("now_focus");
        }

        if ($("textarea[name='goal2']").val()) {
            $("textarea[name='goal2']").val('');
            $(".tc_input").removeClass("now_focus");
        }

        $("#tab_request").show();
    });

    //목표
    $(".tab_goal").click(function() {
        $(".tc_index_tab button").removeClass("on");
        $(this).addClass("on");
        $(".tc_index_box").hide();

        if ($("textarea[name='contents']").val()) {
            $("textarea[name='contents']").val('');
            $(".tc_input").removeClass("now_focus");
        }

        if ($("textarea[name='wdate_contents']").val()) {
            $("textarea[name='wdate_contents']").val('');
            $(".tc_input").removeClass("now_focus");
        }

        if ($("textarea[name='req_contents']").val()) {
            $("textarea[name='req_contents']").val('');
            $(".tc_input").removeClass("now_focus");
        }

        $("#tab_goal").show();
    });



    //오늘할일 등록하기
    $("#write_btn").click(function() {

        var obj = $("textarea[name='contents']");

        //alert( "  ::: " + $("#contents").val() );
        //alert( " val " + CKEDITOR.instances.contents.getData() );

        var fdata = new FormData();
        //if (CKEDITOR.instances.contents.getData().length < 1){
        //	alert("ckckckckck");
        //}

        if (!obj.val()) {
            alert("오늘 할 일을 입력해 주세요.");
            obj.focus();
            return false;
        }



        fdata.append("contents[]", obj.val());

        if ($(".tab_date").hasClass("on") == true) {
            fdata.append("work_flag", "1");

        } else if ($(".tab_goal").hasClass("on") == true) {
            fdata.append("work_flag", "2");

        } else if ($(".tab_request").hasClass("on") == true) {
            fdata.append("work_flag", "3");

        } else {
            fdata.append("work_flag", "0");
        }

        fdata.append("mode", "works_write");
        fdata.append("wdate", $("#works_today").val());

        $.ajax({
            type: "POST",
            data: fdata,
            contentType: false,
            processData: false,
            url: '/inc/process.php',
            success: function(data) {
                console.log(data);
                if (data == "complete") {

                    obj.val('');
                    $(".tc_input").removeClass("now_focus");
                    if ($(".tab_day").hasClass("on") == true) {
                        $(".tab_day").trigger("click");
                    } else if ($(".tab_week").hasClass("on") == true) {
                        $(".tab_week").trigger("click");
                    }
                    //location.reload();
                    return false;
                }
            }
        });
    });


    //업무예약 등록하기
    $("#date_write").click(function() {
        var obj = $("textarea[name='wdate_contents']");
        var size = obj.size();
        var fdata = new FormData();

        for (var i = 0; i < size; i++) {
            if (!obj.eq(i).val()) {
                alert("예약할 업무 내용을 입력해 주세요.");
                obj.eq(i).focus();
                return false;
            }
            fdata.append("contents[]", obj.eq(i).val());
        }

        var chkobj = $("input[name=chk]");
        var checkCount = chkobj.size();

        for (var i = 0; i < checkCount; i++) {
            if (chkobj.eq(i).is(":checked")) {
                var rno = chkobj.eq(i).attr("id").replace("chk", "");
                fdata.append("chk[]", $("#chk" + rno).val());
            }
        }
        fdata.append("date_sdate", $("#date_02").val());
        fdata.append("wdate", $("#works_today").val());
        fdata.append("mode", "date_write");

        $.ajax({
            type: "POST",
            data: fdata,
            contentType: false,
            processData: false,
            url: '/inc/process.php',
            success: function(data) {
                console.log(data);
                if (data == "complete") {

                    obj.val('');
                    $(".tc_input").removeClass("now_focus");
                    if ($(".tab_day").hasClass("on") == true) {
                        $(".tab_day").trigger("click");
                    } else if ($(".tab_week").hasClass("on") == true) {
                        $(".tab_week").trigger("click");
                    }
                    //location.reload();
                    return false;
                }
            }
        });
    });


    //목표 등록하기
    $("#goal_write").click(function() {

        var fdata = new FormData();
        if (!$("#goal1").val()) {
            alert("목표를 입력하세요.");
            $("#goal1").focus();
            return false;
        }

        if (!$("#goal2").val()) {
            alert("핵심결과를 입력하세요.");
            $("#goal2").focus();
            return false;
        }

        if (!$("#date_04").val()) {
            alert("완료일자를 입력하세요.");
            $("#date_04").focus();
            return false;
        }

        fdata.append("mode", "goal_write");
        fdata.append("goal1", $("#goal1").val());
        fdata.append("goal2", $("#goal2").val());
        fdata.append("goal3", $("#date_04").val());
        fdata.append("wdate", $("#works_today").val());

        $.ajax({
            type: "POST",
            data: fdata,
            contentType: false,
            processData: false,
            url: '/inc/process.php',
            success: function(data) {
                console.log(data);
                if (data == "complete") {

                    $("#goal1").val('');
                    $("#goal2").val('');
                    $(".tc_input").removeClass("now_focus");
                    if ($(".tab_day").hasClass("on") == true) {
                        $(".tab_day").trigger("click");
                    } else if ($(".tab_week").hasClass("on") == true) {
                        $(".tab_week").trigger("click");
                    }
                    //    alert("입력한 내용으로 업무요청 되었습니다.");
                    //location.reload();
                    return false;
                }
            }
        });
    });




    //오늘업무 추가버튼
    $("#works_add_btn").click(function() {
        var html = "<div class=\"tc_box_area\">";
        html += "<div class=\"tc_area\">";
        html += "<textarea name=\"contents\" id=\"contents\" class=\"area_01\">";
        html += "</textarea></div>";
        html += "</div>";
        $("#works_append").append(html);
    });


    //오늘일 업무추가
    $("#list_add_btn").click(function() {
        location.href = '/works/write.php';
        return false;
    });


    //오늘일 완료버튼
    $(document).on("click", "button[id^='list_complete']", function() {
        var id = $(this).attr("id");
        var fdata = new FormData();
        fdata.append("idx", $("#" + id).val());
        fdata.append("mode", "list_complete");

        $.ajax({
            type: "POST",
            data: fdata,
            contentType: false,
            processData: false,
            url: '/inc/process.php',
            success: function(data) {
                //console.log("data :::::: " + data);
                if (data == "complete") {
                    $(".tab_day").trigger("click");
                    return false;
                }
            }
        });
    });


    //오늘일 완료 변경버튼
    $(document).on("click", "button[id^='list_recomplete']", function() {

        var id = $(this).attr("id");
        var fdata = new FormData();
        fdata.append("idx", $("#" + id).val());
        fdata.append("mode", "list_recomplete");

        $.ajax({
            type: "POST",
            data: fdata,
            contentType: false,
            processData: false,
            url: '/inc/process.php',
            success: function(data) {
                console.log(data);
                if (data == "complete") {
                    //location.reload();
                    $(".tab_day").trigger("click");
                    return false;
                }
            }
        });
    });


    //오늘일 내일로미루기 버튼
    $(document).on("click", "button[id^='list_yesterday']", function() {

        var id = $(this).attr("id");
        var fdata = new FormData();
        fdata.append("idx", $("#" + id).val());
        fdata.append("mode", "list_yesterday");

        $.ajax({
            type: "POST",
            data: fdata,
            contentType: false,
            processData: false,
            url: '/inc/process.php',
            success: function(data) {
                console.log(data);
                if (data == "complete") {
                    //location.reload();
                    $(".tab_day").trigger("click");
                    return false;
                }
            }
        });
    });


    //오늘일 삭제버튼
    $(document).on("click", "button[id^='list_del']", function() {
        var id = $(this).attr("id");
        if (confirm("선택한 오늘일을 삭제 하시겠습니까?")) {
            var fdata = new FormData();
            fdata.append("idx", $("#" + id).val());
            fdata.append("mode", "list_del");

            $.ajax({
                type: "POST",
                data: fdata,
                contentType: false,
                processData: false,
                url: '/inc/process.php',
                success: function(data) {
                    console.log(data);
                    if (data == "complete") {
                        //location.reload();
                        $(".tab_day").trigger("click");
                        return false;
                    }
                }
            });
        }
    });


    //오늘할일 버튼
    $("#works_list").click(function() {
        if (GetCookie("user_id") != null) {
            location.href = "/works/list.php";
            return false;
        } else {
            //$(this).attr("id");
            $("#login_btn").trigger("click");
        }
    });


    //일일버튼
    $(".tc_tab .tab_day").click(function() {
        $(".tc_index_list_week").hide();
        $(".tc_index_list_month").hide();
        $(".tc_index_list").show();
        $(".tc_tab button").removeClass("on");
        $(this).addClass("on");


        var fdata = new FormData();
        fdata.append("dtab", "day");
        fdata.append("type", "my");
        fdata.append("wdate", $("#works_today").val());

        $.ajax({
            type: "POST",
            data: fdata,
            contentType: false,
            processData: false,
            url: '/works/list_day.php',
            success: function(data) {
                console.log(data);
                $(".tc_index_list").html(data);
                if (data == "complete") {
                    //    alert("입력한 내용으로 업무요청 되었습니다.");
                    //	location.reload();
                    //	return false;
                }
            }
        });
    });


    //주간버튼
    $(".tc_tab .tab_week").click(function() {
        $(".tc_index_list_month").hide();
        $(".tc_index_list").hide();
        $(".tc_index_list_week").show();
        $(".tc_tab button").removeClass("on");
        $(this).addClass("on");

        var fdata = new FormData();
        fdata.append("dtab", "week");
        fdata.append("type", "my");
        fdata.append("wdate", $("#works_today").val());

        $.ajax({
            type: "POST",
            data: fdata,
            contentType: false,
            processData: false,
            url: '/works/list_week.php',
            success: function(data) {
                console.log(data);
                $(".tc_index_list_week").html(data);
                if (data == "complete") {
                    //    alert("입력한 내용으로 업무요청 되었습니다.");
                    //	location.reload();
                    //	return false;
                }
            }
        });
    });


    //한달버튼
    $(".tc_tab .tab_month").click(function() {
        /*    $(".tc_index_list").hide();
        $(".tc_index_list_week").hide();
        $(".tc_index_list_month").show();
        $(".tc_tab button").removeClass("on");
        $(this).addClass("on");
	*/
    });


    //업무요청버튼
    $(".tab_request").click(function() {

        if ($(".tab_work").hasClass("on") == true) {
            $(".tab_work").removeClass("on");
        }

        if ($(".tab_date").hasClass("on") == true) {
            $(".tab_date").removeClass("on");
        }

        if ($(".tab_goal").hasClass("on") == true) {
            $(".tab_goal").removeClass("on");
        }

        $(".tab_request").addClass("on");
        $(".t_layer_req").show();
    });


    //할일버튼
    $(".tab_work").click(function() {

        if ($(".tab_work").hasClass("on") == true) {
            $(".tab_work").removeClass("on");
        }

        if ($(".tab_date").hasClass("on") == true) {
            $(".tab_date").removeClass("on");
        }

        if ($(".tab_goal").hasClass("on") == true) {
            $(".tab_goal").removeClass("on");
        }

        $(".tab_work").addClass("on");

    });


    //일정버튼
    $(".tab_date").click(function() {

        if ($(".tab_work").hasClass("on") == true) {
            $(".tab_work").removeClass("on");
        }

        if ($(".tab_goal").hasClass("on") == true) {
            $(".tab_goal").removeClass("on");
        }

        if ($(".tab_request").hasClass("on") == true) {
            $(".tab_request").addClass("on");
        }

        $(".tab_date").addClass("on");
        $(".t_layer_date").show();

    });


    //목표버튼
    $(".tab_goal").click(function() {

        if ($(".tab_work").hasClass("on") == true) {
            $(".tab_work").removeClass("on");
        }

        if ($(".tab_date").hasClass("on") == true) {
            $(".tab_date").removeClass("on");
        }

        if ($(".tab_request").hasClass("on") == true) {
            $(".tab_request").addClass("on");
        }

        $(".tab_goal").addClass("on");
        $(".t_layer_goal").show();
    });


    //목표버튼 > 주간목표
    $(".tab_goal").click(function() {

        if ($(".tab_work").hasClass("on") == true) {
            $(".tab_work").removeClass("on");
        }

        if ($(".tab_date").hasClass("on") == true) {
            $(".tab_date").removeClass("on");
        }

        if ($(".tab_request").hasClass("on") == true) {
            $(".tab_request").addClass("on");
        }

        $(".tab_goal").addClass("on");
        $(".t_layer_goal").show();
    });


    //목표
    var $item = $('.tc_goal_select button').on('click', function() {
        var idx = $item.index(this);

        //일일목표
        if (idx == 0) {
            $('.tc_goal_select button').eq(0).addClass("on");
            $('.tc_goal_select button').eq(1).removeClass("on");
            $('.tc_goal_select button').eq(2).removeClass("on");

            //주간목표
        } else if (idx == 1) {

            $('.tc_goal_select button').eq(0).removeClass("on");
            $('.tc_goal_select button').eq(1).addClass("on");
            $('.tc_goal_select button').eq(2).removeClass("on");

            //성과목표
        } else if (idx == 2) {
            $('.tc_goal_select button').eq(0).removeClass("on");
            $('.tc_goal_select button').eq(1).removeClass("on");
            $('.tc_goal_select button').eq(2).addClass("on");
        }
    });





    //나의업무
    $(".tc_tab_slc_in").click(function() {
        $(".tc_tab_slc ul").show();
    });

    $(".tc_tab_slc ul button").click(function() {
        var slc_this = $(this);
        $(".tc_tab_slc_in button span").text(slc_this.text());
        $(".tc_tab_slc ul").hide();
    });


    //업무요청 닫기
    $(".tl_close, .tl_deam").click(function() {
        $(".tab_request, .tab_goal, .tab_date").removeClass("on");


        $('.tc_goal_select button').eq(0).addClass("on");
        $('.tc_goal_select button').eq(1).removeClass("on");
        $('.tc_goal_select button').eq(2).removeClass("on");

        if ($("#goal1").val()) {
            $("#goal1").val('');
        }

        if ($("#goal2").val()) {
            $("#goal2").val('');
        }

        if ($("#goal3").val()) {
            $("#goal3").val('');
        }

        $(".t_layer, .t_layer_req, .t_layer_goal, .t_layer_date").hide();
        $(".tab_work").addClass("on");

        $('.tc_box_08_in').find(':input').each(function() {
            //$(".tc_area").each(function() {
            $(this).val('');

        });

        $("input[name=chk]").prop("checked", false);
        $("input[name=chkall]").prop("checked", false);
        $("input[name=chk_t]").prop("checked", false);

        //prop("checked", true);


        //업무요청 오늘날짜
        if ($("#date_03").length) {
            $("#req_date").val(getTodayType());
        }

        //일정 오늘날짜
        if ($("#date_sdate").length) {
            $("#date_sdate").val(getTodayType());
        }

        //업무요청 현재시간
        if ($("#req_stime").length) {
            var today = new Date();
            var hours = today.getHours(); // 시
            var minutes = today.getMinutes(); // 분

            if (hours < 10) hours = "0" + hours;
            if (minutes < 10) minutes = "0" + minutes;

            $("#req_stime").val(hours + ":" + minutes);
            $("#req_etime").val(hours + ":" + minutes);
        }


        //일정 현재시간
        if ($("#date_stime").length) {
            var today = new Date();
            var hours = today.getHours(); // 시
            var minutes = today.getMinutes(); // 분

            if (hours < 10) hours = "0" + hours;
            if (minutes < 10) minutes = "0" + minutes;

            $("#date_stime").val(hours + ":" + minutes);
        }


        //업무요청 시간 정하기
        $("#req_date").attr("disabled", true).attr("readonly", false); //입력불가, 값 안넘어감
        $("#req_date").css("color", "#ccc");

        $("#req_stime").attr("disabled", true).attr("readonly", false); //입력불가, 값 안넘어감
        $("#req_stime").css("color", "#ccc");

        $("#req_etime").attr("disabled", true).attr("readonly", false); //입력불가, 값 안넘어감
        $("#req_etime").css("color", "#ccc");


        //일정 시간 정하기
        $("#date_sdate").attr("disabled", true).attr("readonly", false); //입력불가, 값 안넘어감
        $("#date_sdate").css("color", "#ccc");

        $("#date_stime").attr("disabled", true).attr("readonly", false); //입력불가, 값 안넘어감
        $("#date_stime").css("color", "#ccc");

    });


    //전체선택
    $(document).on("click", "input[id='chkall'],input[id^='chkuseall']", function() {
        if ($("#chkall").is(":checked")) {
            $("input[name=chk]").prop("checked", true);
            $("input[name=chkall]").prop("checked", true);
        } else {
            $("input[name=chk]").prop("checked", false);
            $("input[name=chkall]").prop("checked", false);
        }

        //업무요청 전체선택
        var id = $(this).attr("id");
        if (id) {
            var no = change_num(id);
            if (no) {
                if ($("input[id='" + id + "']").is(":checked")) {
                    $("input[name^=requsechk" + no + "]").prop("checked", true);
                    $("input[name^=" + id + "]").prop("checked", true);
                } else {
                    $("input[name^=requsechk" + no + "]").prop("checked", false);
                    $("input[name^=" + id + "]").prop("checked", false);
                }
            }
        }
    });


    //체크박스 선택시 체크박스와 체크on 박스가 갯수가 다를경우 전체선택해제
    $(document).on("click", "input[name='chk'],input[id^='requsechk']", function() {
        var chk_cnt = $("input[name='chk']").size();
        var chk_true = $('input:checkbox[name=chk]:checked').length;

        if ($("input[name='chkall']").is(":checked") == true) {
            if (chk_cnt != chk_true) {
                $("input[name=chkall]").prop("checked", false);
            }
        }


        //업무요청
        var id = $(this).attr("id");
        var name = $(this).attr("name");
        if (id && name) {
            var no = change_num(name);
            if (no) {
                var chk_req_cnt = $("input[name='" + name + "']").size();
                var chk_req_true = $('input:checkbox[name=' + name + ']:checked').length;

                if ($("input[name='chkuseall" + no + "']").is(":checked")) {
                    if (chk_req_cnt != chk_req_true) {
                        $("input[name='chkuseall" + no + "']").prop("checked", false);
                    }
                }
            }
        }

    });


    //업무요청하기
    $("#req_write").click(function() {
        var obj = $("textarea[name='req_contents']");
        var size = obj.size();
        var fdata = new FormData();

        for (var i = 0; i < size; i++) {
            if (!obj.eq(i).val()) {
                alert("요청할 업무를 입력해주세요.");
                obj.eq(i).focus();
                return false;
            }
            fdata.append("contents[]", obj.eq(i).val());
        }

        var chkobj = $("input[name=chk]");
        var checkCount = chkobj.size();

        for (var i = 0; i < checkCount; i++) {
            if (chkobj.eq(i).is(":checked")) {
                var rno = chkobj.eq(i).attr("id").replace("chk", "");
                fdata.append("chk[]", $("#chk" + rno).val());
            }
        }

        if (!$('input:checkbox[name=chk]:checked').length) {
            alert('업무를 요청할 대상을 선택해 주세요.');
            return false;
        }

        fdata.append("req_date", $("#date_03").val());
        fdata.append("mode", "req_write");

        $.ajax({
            type: "POST",
            data: fdata,
            contentType: false,
            processData: false,
            url: '/inc/process.php',
            success: function(data) {
                console.log("data :: " + data);
                if (data == "complete") {

                    obj.val('');
                    $("input:checkbox[name=chk]").prop("checked", false);
                    $(".tc_input").removeClass("now_focus");
                    if ($(".tab_day").hasClass("on") == true) {
                        $(".tab_day").trigger("click");
                    } else if ($(".tab_week").hasClass("on") == true) {
                        $(".tab_week").trigger("click");
                    }

                    //alert("입력한 내용으로 업무요청 되었습니다.");
                    //location.reload();
                    return false;
                }
            }
        });

    });





    //.label_tit
    //#contents
    //$(document).on("click", "#chall_sdate,#chall_edate", function() {

    //챌린지 날짜 아이콘
    $(".tc_date_calendar").click(function() {
        $("#date1").focus();
    });

    //챌린지 날짜입력란 클릭
    $("#date1,#date2,#date_02,#date_03,#date_04").click(function() {
        $(this).focus();
    });


    $(document).on("click", "input[id='date1']", function() {
        //$(document).on("click", ".tc_request strong", function() {
        //console.log($(this).text());
        //<input type="hidden" id="date_03" name="" class="input_01" value="<?=TODATE?>" />
        //$(this).datepicker();
        //$(".tc_request strong").width(100);

        //var id = $(this).attr("id");
        //var val = id.replace("workdate", "");


        //console.log("id :: " + id);
        //var wdate = $("#workdate_" + val).val();

        //$('#wdate').width('75px');
        //$("#workdate_" + val).css('border', '1px solid #fff');
        //$("#workdate_" + val).html("<input type=\"text\" id=\"wdate\" autocomplete=\"off\" value='" + wdate + "'>");
        //$("#workdate_" + val).html("<input type=\"text\" id=\"wdate\" autocomplete=\"off\" >");
        //$("#wdate").datepicker();
        //$('.datepicker').css('width', '40%').css('height', '50px').css('font-size', '40px');

        //$('#wdate').width('75px');

        $(this).attr("autocomplete", "off");

        console.log("ssss");
        if ($(".tc_request_user").is(":visible") == true) {
            $(".tc_request_user").hide();
        }

        $(this).datepicker({
            dateFormat: 'yyyy-mm-dd',
            onSelect: function(date) {
                if (date) {
                    $(this).val(date);

                    var fdata = new FormData();
                    fdata.append("mode", "workdate");
                    fdata.append("workdate", date);
                    fdata.append("listidx", $("#listidx").val());

                    $.ajax({
                        type: "POST",
                        data: fdata,
                        contentType: false,
                        processData: false,
                        url: '/inc/process.php',
                        success: function(data) {
                            console.log(data);
                            if (data == "complete") {
                                if ($(".tab_day").hasClass("on") == true) {
                                    $(".tab_day").trigger("click");
                                } else if ($(".tab_week").hasClass("on") == true) {
                                    $(".tab_week").trigger("click");
                                }
                                return false;
                            }
                        }
                    });
                }
            }
        });
    });






    //챌린지 날짜 선택 포커스아웃
    $("#date1").focusout(function() {
        $("#date2").focus();
    });

    //날짜 선택후  
    $("#date1,#date2,#date_02,#date_03,#date_04").keyup(function() {
        if ($(this).val()) {
            clickdate($(this).val());
        }
    });



    /*$('#goal3').datetimepicker({
        inline: true,
    });*/


    //업무요청 수정하기
    //$("#list_desc").click(function() {
    //$("li[id^=\"list_li\"]").click(function() {
    //$("div[id^=tc_dec]").click(function() {

    /*
    	$(document).on("click", "div[id^='tc_dec']", function() {
            var id = $(this).attr("id");
            //console.log("id  : " + id);
            if (id) {
                var val = id.replace("tc_dec_", "");
                var obj = $("div[id^=tc_area]");
                var obj_cnt = obj.size();

                for (i = 0; i < obj_cnt; i++) {
                    obj_id = obj.eq(i).attr("id");

                    console.log("obj_id :: " + id);

                    //console.log(" :: " + obj_id);
                    area_no = obj.eq(i).attr("id").replace("tc_area_", "");
                    if (area_no != val) {
                        $("#" + obj_id).hide();
                    } else {
                        $("#" + obj_id).show();
                        //$("#" + obj_id).focus();
                    }
                }
            }
        });
    	*/


    //수정하기 textarea
    $(document).on("click", "span[id^='contents1']", function() {

        var id = $(this).attr("id");
        var no = id.replace("contents1_", "");
        if (no) {
            var obj = $("div[id^=edit_content1_" + no + "]");

            //요청자
            if ($(".tc_request_user").is(":visible") == true) {
                $(".tc_request_user").hide();
            }


            console.log("st : " + $("strong[id^=workdate_]").is(":visible"));

            if ($("strong[id^=workdate_]").is(":visible") == true) {
                //$("strong[id^=workdate_]").hide();


                //<strong id="workdate_<?=$idx?>"><?=$workdate?></strong>


            }


            obj.show();

            var obj_edit = $("div[id^=edit_content1]");
            var obj_edit_cnt = obj_edit.size();
            for (i = 0; i < obj_edit_cnt; i++) {
                obj_edit_id = obj_edit.eq(i).attr("id");
                obj_edit_no = obj_edit.eq(i).attr("id").replace("edit_content1_", "");
                obj_change_id = obj_edit_id.replace("edit_content1_", "edit_content2_");

                if (no != obj_edit_no) {
                    $("#" + obj_edit_id).hide();
                    $("#" + obj_change_id).hide();
                }
            }
        }
    });

    //수정
    $(document).on("click", "span[id^='contents2']", function() {
        var id = $(this).attr("id");
        var no = id.replace("contents1_", "");
        if (no) {
            var obj = $("div[id^=edit_content2_" + no + "]");
            obj.show();

            var obj_edit = $("div[id^=edit_content2]");
            var obj_edit_cnt = obj_edit.size();
            for (i = 0; i < obj_edit_cnt; i++) {
                obj_edit_id = obj_edit.eq(i).attr("id");
                obj_edit_no = obj_edit.eq(i).attr("id").replace("edit_content2_", "");
                obj_change_id = obj_edit_id.replace("edit_content2_", "edit_content1_");

                if (no != obj_edit_no) {
                    $("#" + obj_edit_id).hide();
                    $("#" + obj_change_id).hide();
                }
            }
        }
    });

    $(document).on("click", "span[id^='contents2']", function() {
        var id = $(this).attr("id");
        var no = id.replace("contents2_", "");
        if (no) {
            var obj = $("div[id^=edit_content2_" + no + "]");
            obj.show();
        }
    });


    //수정닫기
    $(document).click(function(e) {
        //div[id^=edit_content1],
        if (!$(e.target).is('textarea[name^=contents1],textarea[name^=contents2],span[id^="contents1"],span[id^="contents2"]')) {
            obj = $("div[id^=edit_content]");
            if (obj.is(":visible") == true) {
                obj.hide();
            }
        }


        if (!$(e.target).is('div[class^=tc_request_user_in]')) {
            //    console.log(999);
        }



        if (!$(e.target).is('div[id^=req_user]')) {

            //console.log(" >> " + $(".tc_request_user").is(":visible"));

            /*   obj = $(".tc_request_user");
            if (obj.is(":visible") == true) {
                obj.hide();
            }
			*/
        }

        if (!$(e.target).is('strong[id^=workdate_]')) {
            //console.log($(this).attr("id"));

            //$("#workdate_").html("");
        }

    });


    //수정하기버튼
    $(document).on("click", "#edit", function() {
        //$(document).on("click", $("button[id^='edit']"), function(e) {
        var val = $(this).val();
        var fdata = new FormData();
        fdata.append("mode", "edit");
        fdata.append("idx", val);

        if ($("textarea[name^=contents1_" + val + "]").val()) {
            fdata.append("contents1", $("textarea[name^=contents1_" + val + "]").val());
        }

        if ($("textarea[name^=contents2_" + val + "]").val()) {
            fdata.append("contents2", $("textarea[name^=contents2_" + val + "]").val());
        }

        $.ajax({
            type: "POST",
            data: fdata,
            contentType: false,
            processData: false,
            url: '/inc/process.php',
            success: function(data) {
                console.log(data);
                if (data == "complete") {
                    if ($(".tab_day").hasClass("on") == true) {
                        $(".tab_day").trigger("click");
                    } else if ($(".tab_week").hasClass("on") == true) {
                        $(".tab_week").trigger("click");
                    }
                    //location.reload();
                    return false;
                }
            }
        });
    });


    //내용클릭 했을때 수정하기로 전환
    $(document).on("click", $("div[id^=tc_area]"), function(e) {
        if ($("div[id^=tc_area]").is(":visible") == true) {
            //console.log(" :::: " + $('.tc_modify').css('display'));
            if ($('.tc_modify').css('display') == "none") {
                //$("div[id^=tc_area]").hide();
                //console.log(111);
                //$('.tc_modify').css('display')
            }
        }
    });






    //$(document).mousedown(function(e) {
    //닫기
    //$('html').click(function(e) {
    //$(document).on("click", ".todaywork_wrap", function(e) {
    $(document).on("click", ".tc_modify", function(e) {
        //if (!$(e.target).hasClass("area_02")) {

        if ($("div[id^=tc_area]").is(":visible") == true) {
            //$("div[id^=tc_area]").hide();
        }


        if ($(".tc_modify").is(":visible") == true) {
            //$("div[id^=tc_area]").hide();
        }

        var obj = $("div[id^=tc_area]");
        var obj_cnt = obj.size();
        for (i = 0; i < obj_cnt; i++) {
            obj_id = obj.eq(i).attr("id");
            //console.log(" :: " + obj_id);
            area_no = obj.eq(i).attr("id").replace("tc_area_", "");
            //if (area_no != val) {

            //    console.log($("#" + obj_id).css("display"));

            if ($("#" + obj_id).css("display") == "block") {
                //    console.log(obj_id);

                //$("#" + obj_id).hide();
                //$("#" + obj_id).css("display", "");
                //$("#" + obj_id).css("display", "none");
            }
        }

        //}
    });



    $(document).on("click", ".todaywork_wrap", function(e) {


        // console.log(" >> " + $("div[id^=tc_area]").is(":visible"));

        //if ($(e.target).hasClass("[class^='area_02']")) {
        //  console.log(88);
        //}
        //if (isNotInMyArea([$(".tc_modify"), $("#tc_area")])) {


        //$("#tc_area").hide();
        //$("div[id^=tc_area]").hide();
        // }
    });

    //$('body').on('click', '.todaywork_wrap', function() {
    //if ($("div[id^=tc_area]").is(":visible") == true) {
    //    console.log($('.tc_modify').css('display'));
    //}
    //});


    //수정하기 상태일때 사라지도록
    if ($("div[id^=tc_area_]").is(":visible") == true) {
        //console.log(1);
        $('body').on('click', '.todaywork_wrap', function() {
            //console.log(11111);
            //code

            //console.log(2);
        });

        //if ($("body").is(":click")) {
        //console.log("okokokokk");
        //}
    }
    //});

    //$('li[id^="list_li"]').bind(function() {
    //   console.log(1111);
    //});


    //$("#req_write").click(function() {

    //$(document).on("click", $("#edit"), function(e) {



    //업무요청 시간 정하기
    $("#req_date").attr("disabled", true).attr("readonly", false); //입력불가, 값 안넘어감
    $("#req_date").css("color", "#ccc");

    $("#req_stime").attr("disabled", true).attr("readonly", false); //입력불가, 값 안넘어감
    $("#req_stime").css("color", "#ccc");

    $("#req_etime").attr("disabled", true).attr("readonly", false); //입력불가, 값 안넘어감
    $("#req_etime").css("color", "#ccc");



    //일정 시간 정하기
    $("#date_sdate").attr("disabled", true).attr("readonly", false); //입력불가, 값 안넘어감
    $("#date_sdate").css("color", "#ccc");

    $("#date_stime").attr("disabled", true).attr("readonly", false); //입력불가, 값 안넘어감
    $("#date_stime").css("color", "#ccc");


    //업무요청 오늘날짜
    if ($("#req_date").length) {
        $("#req_date").val(getTodayType());
    }


    //일정 오늘날짜
    if ($("#date_sdate").length) {
        $("#date_sdate").val(getTodayType());
    }


    //일정 현재시간
    if ($("#date_stime").length) {
        var today = new Date();
        var hours = today.getHours(); // 시
        var minutes = today.getMinutes(); // 분

        if (hours < 10) hours = "0" + hours;
        if (minutes < 10) minutes = "0" + minutes;

        $("#date_stime").val(hours + ":" + minutes);
        //$("#req_etime").val(hours + ":" + minutes);
    }


    //일정 현재시간
    if ($("#date_stime").length) {
        var today = new Date();
        var hours = today.getHours(); // 시
        var minutes = today.getMinutes(); // 분

        if (hours < 10) hours = "0" + hours;
        if (minutes < 10) minutes = "0" + minutes;

        $("#date_stime").val(hours + ":" + minutes);
        //$("#req_etime").val(hours + ":" + minutes);
    }


    //일정 시간정하기 클릭
    $("#chk_date").change(function() {

        if ($("input[id=chk_date]").is(":checked") == true) {
            $("#date_sdate").attr("maxlength", 10);
            $("#date_stime").attr("maxlength", 5);

            $("#date_sdate").css("color", "#333");
            $("#date_sdate").attr("disabled", false).attr("readonly", false); //입력가능

            $("#date_stime").css("color", "#333");
            $("#date_stime").attr("disabled", false).attr("readonly", false); //입력가능

            $("#req_etime").css("color", "#333");
            $("#req_etime").attr("disabled", false).attr("readonly", false); //입력가능
        } else {
            $("#date_sdate").css("color", "#ccc");
            $("#date_sdate").attr("disabled", true).attr("readonly", false); //입력불가, 값 안넘어감

            $("#date_stime").css("color", "#ccc");
            $("#date_stime").attr("disabled", true).attr("readonly", false); //입력불가, 값 안넘어감
        }
    });



    //업무요청 시간정하기 클릭
    $("#chk_t").change(function() {

        if ($("input[id=chk_t]").is(":checked") == true) {
            $("#req_date").attr("maxlength", 10);
            $("#req_stime").attr("maxlength", 5);
            $("#req_etime").attr("maxlength", 5);

            $("#req_date").css("color", "#333");
            $("#req_date").attr("disabled", false).attr("readonly", false); //입력가능

            $("#req_stime").css("color", "#333");
            $("#req_stime").attr("disabled", false).attr("readonly", false); //입력가능

            $("#req_etime").css("color", "#333");
            $("#req_etime").attr("disabled", false).attr("readonly", false); //입력가능
        } else {
            $("#req_date").css("color", "#ccc");
            $("#req_date").attr("disabled", true).attr("readonly", false); //입력불가, 값 안넘어감

            $("#req_stime").css("color", "#ccc");
            $("#req_stime").attr("disabled", true).attr("readonly", false); //입력불가, 값 안넘어감

            $("#req_etime").css("color", "#ccc");
            $("#req_etime").attr("disabled", true).attr("readonly", false); //입력불가, 값 안넘어감
        }
    });


    //시간 정하기 날짜입력
    $("#req_date,#date_sdate,#date1,#date2,#date_02,#date_03,#date_04").keyup(function() {
        if (this.value.length > 10) {
            this.value = this.value.substr(0, 10);
        }
        var val = this.value.replace(/\D/g, '');
        var original = this.value.replace(/\D/g, '').length;
        var conversion = '';
        for (i = 0; i < 2; i++) {
            if (val.length > 4 && i === 0) {
                conversion += val.substr(0, 4) + '-';
                val = val.substr(4);
            } else if (original > 6 && val.length > 2 && i === 1) {
                conversion += val.substr(0, 2) + '-';
                val = val.substr(2);
            }
        }
        conversion += val;
        this.value = conversion;
    });


    //시간정하기 시간입력
    $("#req_stime,#req_etime,#date_stime").keyup(function(e) {
        var val = $(this).val();
        val = val.replace(/[^0-9\s]/g, '');
        if (e.keyCode != 8) {
            if (val.length && val.length == 2) {
                val = val.substr(0, 2) + ":";
            } else {
                val = val.substr(0, 2) + ":" + val.substr(2, 2);
            }
        }
        $(this).val(val);
    });


    //날짜 이전날
    $(".btn_yesterday").click(function() {
        /*var data = $("#works_today").val();
        wdate = get_date(data, "prev");

        if ($(".tab_week").hasClass("on") == true) {
            //$("input[name='tabval']").val("week");
            var tabval = "week";
        } else {
            //$("input[name='tabval']").val("day");
            var tabval = "day";
        }

        if (wdate) {
            var url = $(location).attr('pathname');
            sch = location.search;
            var params = new URLSearchParams(sch);
            params.set('wdate', wdate);
            params.set('dtab', tabval);
            params_str = params.toString();
            location.href = url + "?" + params_str;
        }*/
    });


    //날짜 다음날
    $(".btn_tomorrow").click(function() {
        /*var data = $("#works_today").val();
        wdate = get_date(data, "next");

        if ($(".tab_week").hasClass("on") == true) {
            var tabval = "week";
        } else {
            var tabval = "day";
        }

        if (wdate) {
            var url = $(location).attr('pathname');
            sch = location.search;
            var params = new URLSearchParams(sch);
            params.set('wdate', wdate);
            params.set('dtab', tabval);
            params_str = params.toString();
            location.href = url + "?" + params_str;
        }*/
    });



    //업무 구분별 리스트
    //나의업무
    $(".tab_my").click(function() {

        var url, get_html, tab_day_works, tab_type_works, wdate, works_no;
        var fdata = new FormData();

        if ($(".tab_day").hasClass("on") == true) {
            tab_day_works = "day";
        } else if ($(".tab_week").hasClass("on") == true) {
            tab_day_works = "week";
        }

        if ($("#works_today").val()) {
            var wdate = $("#works_today").val();
        }

        works_no = $(".tc_tab_slc button").index(this);
        tab_type_works = "my";

        fdata.append("dtab", tab_day_works);
        fdata.append("type", tab_type_works);
        fdata.append("wdate", wdate);

        if ($(".tab_day").hasClass("on") == true) {
            url = "/works/list_day.php";
            get_html = ".tc_index_list";
        } else if ($(".tab_week").hasClass("on") == true) {
            url = "/works/list_week.php";
            get_html = ".tc_index_list_week";
        }

        $.ajax({
            type: "POST",
            data: fdata,
            contentType: false,
            processData: false,
            url: url,
            success: function(data) {
                console.log(data);
                $(get_html).html(data);
                if (data == "complete") {
                    //    alert("입력한 내용으로 업무요청 되었습니다.");
                    //	location.reload();
                    //	return false;
                }
            }
        });
    });


    //팀별업무
    $(".tab_team").click(function() {
        var form = document.createElement("form");
        var hiddenField = document.createElement("input");
        hiddenField.setAttribute('type', 'hidden');
        hiddenField.setAttribute('name', 'type'); // 받을 네이밍
        hiddenField.setAttribute('value', "team_works"); // 넘길 파라메터
        form.appendChild(hiddenField);

        if ($("#works_today").val()) {
            var wdate = chage_getdate($("#works_today").val());
            var hiddenField2 = document.createElement("input");
            hiddenField2.setAttribute('type', 'hidden');
            hiddenField2.setAttribute('name', 'wdate'); // 받을 네이밍
            hiddenField2.setAttribute('value', wdate); // 넘길 파라메터
            form.appendChild(hiddenField2);
        }

        form.setAttribute("method", "get");
        form.setAttribute('action', "/works/list.php"); // URL
        document.body.appendChild(form);
        //  form.submit();

        var url, get_html, tab_day_works, tab_type_works, wdate, works_no;
        var fdata = new FormData();

        if ($(".tab_day").hasClass("on") == true) {
            tab_day_works = "day";
        } else if ($(".tab_week").hasClass("on") == true) {
            tab_day_works = "week";
        }

        if ($("#works_today").val()) {
            var wdate = $("#works_today").val();
        }

        works_no = $(".tc_tab_slc button").index(this);
        tab_type_works = "team";

        fdata.append("dtab", tab_day_works);
        fdata.append("type", tab_type_works);
        fdata.append("wdate", wdate);

        if ($(".tab_day").hasClass("on") == true) {
            url = "/works/list_day.php";
            get_html = ".tc_index_list";
        } else if ($(".tab_week").hasClass("on") == true) {
            url = "/works/list_week.php";
            get_html = ".tc_index_list_week";
        }

        $.ajax({
            type: "POST",
            data: fdata,
            contentType: false,
            processData: false,
            url: url,
            success: function(data) {
                console.log(data);

                $(get_html).html(data);

                if (data == "complete") {
                    //    alert("입력한 내용으로 업무요청 되었습니다.");
                    //	location.reload();
                    //	return false;
                }
            }
        });

    });


    //전체업무
    $(".tab_all").click(function() {

        var form = document.createElement("form");
        var hiddenField = document.createElement("input");
        hiddenField.setAttribute('type', 'hidden');
        hiddenField.setAttribute('name', 'type'); // 받을 네이밍
        hiddenField.setAttribute('value', "all_works"); // 넘길 파라메터
        form.appendChild(hiddenField);

        if ($("#works_today").val()) {
            var wdate = chage_getdate($("#works_today").val());
            var hiddenField2 = document.createElement("input");
            hiddenField2.setAttribute('type', 'hidden');
            hiddenField2.setAttribute('name', 'wdate'); // 받을 네이밍
            hiddenField2.setAttribute('value', wdate); // 넘길 파라메터
            form.appendChild(hiddenField2);
        }

        form.setAttribute("method", "get");
        form.setAttribute('action', "/works/list.php"); // URL
        document.body.appendChild(form);
        //form.submit();


        var url, get_html, tab_day_works, tab_type_works, wdate, works_no;
        var fdata = new FormData();

        if ($("#works_today").val()) {
            var wdate = $("#works_today").val();
        }

        if ($(".tab_day").hasClass("on") == true) {
            tab_day_works = "day";
        } else if ($(".tab_week").hasClass("on") == true) {
            tab_day_works = "week";
        }

        works_no = $(".tc_tab_slc button").index(this);
        tab_type_works = "all";

        fdata.append("dtab", tab_day_works);
        fdata.append("type", tab_type_works);
        fdata.append("wdate", wdate);


        if ($(".tab_day").hasClass("on") == true) {
            url = "/works/list_day.php";
            get_html = ".tc_index_list";
        } else if ($(".tab_week").hasClass("on") == true) {
            url = "/works/list_week.php";
            get_html = ".tc_index_list_week";
        }


        $.ajax({
            type: "POST",
            data: fdata,
            contentType: false,
            processData: false,
            url: url,
            success: function(data) {
                console.log(data);

                $(get_html).html(data);

                if (data == "complete") {
                    //    alert("입력한 내용으로 업무요청 되었습니다.");
                    //	location.reload();
                    //	return false;
                }
            }
        });

    });



    //보상하기버튼
    $("#reward_btn").click(function() {

        if (!$("#coin_user option:selected").val()) {
            alert("보상 대상을 선택하세요.");
            return false;
        }

        if (!$("input[name=coin_point]").val()) {
            alert("보상 코인을 입력하세요.");
            $("input[name=coin_point]").focus();
            return false;
        }

        if (!$("textarea[name=coin_info]").val()) {
            alert("보상 사유를 입력 입력하세요.");
            $("textarea[name=coin_info]").focus();
            return false;
        }

        var fdata = new FormData();
        fdata.append("mode", "coin_reward");
        fdata.append("coin_user", $("#coin_user option:selected").val());
        fdata.append("coin_point", $("input[name=coin_point]").val());
        fdata.append("coin_info", $("textarea[name=coin_info]").val());

        $.ajax({
            type: "POST",
            data: fdata,
            contentType: false,
            processData: false,
            url: '/inc/process.php',
            success: function(data) {
                console.log(data);

                if (data == "complete") {
                    alert("보상이 지급 되었습니다.");
                    location.href = "/coins/list.php";
                    return false;
                } else if (data == "coin_min") {
                    alert("보유한 코인이 부족하여 보상지급 안됩니다.\n\n보상 코인을 조정하여 주세요.");
                    return false;
                }
            }
        });
    });


    //챌린지 카테고리 선택
    $(document).on("click", "button[id^='chall_cate']", function() {

        var id = $(this).attr("id");
        var no = $("#" + id).val()
        var cnt = $("button[id^='chall_cate']").length;
        for (var i = 1; i <= cnt; i++) {
            if (i == no) {
                if ($("button[id^='chall_cate" + i + "']").hasClass("on") == false) {
                    $("button[id^='chall_cate" + i + "']").addClass("on");
                }
            } else {
                $("button[id^='chall_cate" + i + "']").removeClass("on");
            }

        }
    });


    //챌린지 기간내 선택 한번만
    $("#chall_one").click(function() {
        $("#chall_one").addClass("on");
        $("#chall_day").removeClass("on");
        $("button[id^='chall_days']").removeClass("on");

    });


    //챌린지 기간내 선택 매일
    $("#chall_day").click(function() {
        $("#chall_day").addClass("on");
        $("#chall_one").removeClass("on");
        $("button[id^='chall_days']").removeClass("on");
        //$("button[id^='chall_days']").css("display","");
    });


    //챌린지 기간 내, 매일 요일 선택시
    $(document).on("click", "button[id^='chall_days']", function() {

        var id = $(this).attr("id");
        var no = id.replace("chall_days", "");
        $("#chall_day").removeClass("on");

        if ($("#" + id).hasClass("on") == false) {
            $("#" + id).addClass("on");
            $("#chall_one").removeClass("on");
        } else if ($("#" + id).hasClass("on") == true) {
            $("#" + id).removeClass("on");
        }

        /*if ($("#"+id).hasClass("on") == true){
        	$("#"+id).removeClass("on");
        }else if ($("#"+id).hasClass("on") == false) {
        	$("#"+id).addClass("on");
        }*/

        /*if(no){
        	for (var i = 0; i < 7; i++) {
        		if(i == no){
        			$("#"+id).addClass("on");
        			//$("#dayis").val(no);
        		}else{
        			$("#chall_days"+i).removeClass("on");
        		}
        	}
        }*/

    });


    //참여자선택 전체
    $("#user_all").click(function() {
        $("#user_all").addClass("on");
        $("#user_one").removeClass("on");
        $("#add_user_list").css("display", "none");
        $("input[name^=chk]").prop("checked", false);
    });

    //참여자선택 일부
    $("#user_one").click(function() {
        $("#user_one").addClass("on");
        $("#user_all").removeClass("on");
        $("#add_user_list").css("display", "");
    });

    //챌린지 게시물 노출여부 - 노출
    $("#list_view").click(function() {
        $("#list_view").addClass("on");
        $("#list_hidden").removeClass("on");
    });

    //챌린지 게시물 노출여부 - 숨김
    $("#list_hidden").click(function() {
        $("#list_hidden").addClass("on");
        $("#list_view").removeClass("on");
    });


    //var file_cnt = 1;
    //$(document).on("click", ".tc_chall_coin em:eq(1)", function() {
    //$("#div_file_add").on("click", function(){
    //$(document).on("click", ".tc_chall_coin em:eq(1)", function() {

    //$(document).on("click", "em[id='div_file_add']", function() {
    $(document).on("click", "li[id^='div_file']", function() {

        var file_cnt = $("li[id^='div_file']").length;
        if (file_cnt < 3) {
            var add_input = '<li id="div_file' + file_cnt + '" onclick="file_del(' + file_cnt + ')"><div class="tc_chall_coin"><em id="div_file_del">첨부파일 <Br>- </em><div class="tc_box_btns_upload"><input type="file" id="files" name="files"/></div></div></li>';
            $("#div_file").append(add_input);
            //file_cnt++;
        }
    });



    //챌린지 파일첨부
    $("#file_add_bt").click(addFileForm);


    //챌린지 파일첨부 삭제
    $(document).on('click', '#file_del', function(event) {
        //	$(this).parent().remove();
    });


    //미리보기
    $("input[id^='img_file']").change(handleFileSelect);

    $("div[id^='preview']").click(function(e) {
        var id = $(this).attr("id");
        var no = id.replace("preview", "");
        $("#img_file" + no).trigger("click");
    });

    //미리보기 이미지삭제
    $("em[id^='img_del']").click(function() {
        var id = $(this).attr("id");
        var no = id.replace("img_del", "");

        if (no == "1") {
            no = "3";
        } else if (no == "3") {
            no = "1";
        }
        $("#preview" + no).empty();
        $("#img_file" + no).val("");
    });

    //챌린지작성, 챌린지목록
    $("#challenges_w,#challenges_l").on('mouseenter', function() {
        $(this).css("cursor", "pointer");
    });

    //챌린지작성
    $("#challenges_w").click(function() {
        location.href = "/challenge/write.php";
    });

    //챌린지목록
    $("#challenges_l").click(function() {
        location.href = "/challenge/list.php";
    });


    $("input[id='files1']").change(function() {
        var id = $(this).attr("id");
        var no = id.replace("files", "");

        $("#delfile1").html("<button id='file_del1'> X </button>");
    });


    $("input[id='files2']").change(function() {
        var id = $(this).attr("id");
        var no = id.replace("files", "");

        $("#delfile2").html("<button id='file_del2'> X </button>");
    });


    $("input[id='files3']").change(function() {
        var id = $(this).attr("id");
        var no = id.replace("files", "");

        $("#delfile3").html("<button id='file_del3'> X </button>");
    });


    //$("button[id^='file_del']").click(function() {
    $(document).on("click", "button[id^='file_del']", function(event) {
        var id = $(this).attr("id");
        var no = id.replace("file_del", "");
        $("#files" + no).val("");
        $("#file_del" + no).css("display", "none");
    });


    //챌린지 만들기
    $(".tab_chall_01").click(function() {

        if (GetCookie("user_id") != null) {

            location.href = "/challenge/write.php";

        } else {
            $(".t_layer").show();
        }

    });



    //챌린지 생성하기
    $("#challenges_write").click(function() {


        /*if (CKEDITOR.instances.chall_contents.getData().length > 1){
        	var contents = CKEDITOR.instances.chall_contents.getData();
        }*/

        if (!$("#h1").val()) {
            alert("챌린지명을 입력하세요.");
            $("#h1").focus();
            return false;
        }

        if (!$("#date1").val()) {
            alert("기간을 입력하세요.");
            $("#date1").focus();
            return false;
        }

        if (!$("#date2").val()) {
            alert("기간을 입력하세요.");
            $("#date2").focus();
            return false;
        }

        if (!$("#chall_contents").val()) {
            alert("내용을 입력하세요.");
            $("#chall_contents").focus();
            return false;
        }

        /*if (!$("#action1").val()) {
            alert("행동지침 내용을 입력하세요.");
            $("#action1").focus();
            return false;
        }

        if (!$("#action2").val()) {
            alert("행동지침 내용을 입력하세요.");
            $("#action2").focus();
            return false;
        }*/


        if (!$("#h4").val()) {
            alert("보상할 코인을 입력하세요.");
            $("#h4").focus();
            return false;
        }

        var fdata = new FormData();
        fdata.append("mode", "chall_write");
        fdata.append("title", $("#h1").val());
        fdata.append("date1", $("#date1").val());
        fdata.append("date2", $("#date2").val());
        fdata.append("contents", $("#chall_contents").val());


        //카테고리
        if ($("button[id^='chall_cate']").hasClass("on") == true) {
            var cateCount = $("button[id^='chall_cate']").size();

            for (var i = 0; i <= cateCount; i++) {
                if ($("button[id^='chall_cate" + i + "']").hasClass("on") == true) {
                    fdata.append("cate", $("button[id^='chall_cate" + i + "']").val());
                }
            }
        }

        //참여자 전체선택
        if ($("#user_all").hasClass("on") == true) {
            fdata.append("user", "all");
        } else {
            fdata.append("user", "sel");

            var chkobj = $("input[name=chk]");
            var checkCount = chkobj.size();
            for (var i = 0; i < checkCount; i++) {
                if (chkobj.eq(i).is(":checked")) {
                    var rno = chkobj.eq(i).attr("id").replace("chk", "");
                    fdata.append("chk[]", $("#chk" + rno).val());
                }
            }
        }

        //게시물 노출여부
        if ($("#list_view").hasClass("on") == true) {
            fdata.append("view", "yes");
        } else {
            fdata.append("view", "no");
        }

        if ($("input[name='chk01']").is(":checked") == true) {
            fdata.append("outputchk", "1");
        }

        //기간내
        if ($("#chall_one").hasClass("on") == true) {
            fdata.append("chall_day", "one");
        } else if ($("#chall_day").hasClass("on") == true) {

            if ($("button[id^='chall_days']").hasClass("on") == true) {
                //	fdata.append("dayis", $("#dayis").val());
            }
            fdata.append("chall_day", "daily");
        }

        //요일선택
        if ($("button[id^='chall_days']").hasClass("on") == true) {
            var cnt = $("button[id^='chall_days']").length;
            for (var i = 0; i < cnt; i++) {
                if ($("button[id^='chall_days" + i + "']").hasClass("on") == true) {
                    fdata.append("day_chk[]", i);
                }
            }
        }

        if ($("#action1").val()) {
            fdata.append("action1", $("#action1").val());
        }

        if ($("#action2").val()) {
            fdata.append("action2", $("#action2").val());
        }

        if ($("#h4").val()) {
            fdata.append("h4", $("#h4").val());
        }



        /* 		var inputfile = $("input[name='files']");
        		var files = inputfile[0].files;
        		console.log(files.length);
        		if (files.length > 0){
        			for (var i=0; i < files.length; i++){

        				console.log( files[i] );

        				fdata.append("files", files[i]);
        			}
        		}
        return false;
        */

        var file_cnt = $("input[id^='files']").length;
        if (file_cnt > 0) {
            //var no = replace("files", "");
            //console.log("no " + no);
            /*	$($("#files")[0].files).each(function(index, file) {
            	formData.append("files[]", file);
            });*/
        }

        if ($("input[id='files1']").val()) {
            fdata.append("files[]", $("input[id='files1']")[0].files[0]);
        }

        if ($("input[id='files2']").val()) {
            fdata.append("files[]", $("input[id='files2']")[0].files[0]);
        }

        if ($("input[id='files3']").val()) {
            fdata.append("files[]", $("input[id='files3']")[0].files[0]);
        }


        /*var file_cnt = $("input[id^='img_file']").length;
        if(file_cnt > 0){
        	for (var i=0; i < file_cnt; i++){
        		fdata.append("files_img[]", $("input[id^='img_file']")[0].files[0]);
        	}
        }
        */

        if ($("#img_file1").val()) {
            fdata.append("files_img[]", $("#img_file1")[0].files[0]);
        }

        if ($("#img_file2").val()) {
            fdata.append("files_img[]", $("#img_file2")[0].files[0]);
        }

        if ($("#img_file3").val()) {
            fdata.append("files_img[]", $("#img_file3")[0].files[0]);
        }

        $.ajax({
            type: "POST",
            data: fdata,
            contentType: false,
            processData: false,
            url: '/inc/challenges_process.php',
            success: function(data) {
                console.log(data);
                if (data == "complete") {
                    location.href = '/challenge/list.php';
                    return false;
                }
                /*else if(data == "ext_file"){
                					alert("허용되지 않는 확장자입니다.\r\n첨부파일을 다시 등록해주세요.");
                					$("#files").val('');
                					return false;
                				}*/
            }
        });

    });



    //챌린지 수정하기
    $("#challenges_edit").click(function() {
        if (!$("#h1").val()) {
            alert("챌린지명을 입력하세요.");
            $("#h1").focus();
            return false;
        }

        if (!$("#date1").val()) {
            alert("기간을 입력하세요.");
            $("#date1").focus();
            return false;
        }

        if (!$("#date2").val()) {
            alert("기간을 입력하세요.");
            $("#date2").focus();
            return false;
        }

        if (!$("#chall_contents").val()) {
            alert("내용을 입력하세요.");
            $("#chall_contents").focus();
            return false;
        }

        /*if (!$("#action1").val()) {
            alert("행동지침 내용을 입력하세요.");
            $("#action1").focus();
            return false;
        }

        if (!$("#action2").val()) {
            alert("행동지침 내용을 입력하세요.");
            $("#action2").focus();
            return false;
        }*/

        if (!$("#h4").val()) {
            alert("보상할 코인을 입력하세요.");
            $("#h4").focus();
            return false;
        }

        var fdata = new FormData();
        fdata.append("mode", "chall_edit");
        fdata.append("chall_idx", $("#chall_idx").val());
        fdata.append("title", $("#h1").val());
        fdata.append("date1", $("#date1").val());
        fdata.append("date2", $("#date2").val());
        fdata.append("contents", $("#chall_contents").val());

        if ($("#chall_one").hasClass("on") == true) {
            fdata.append("chall_day", "one");
        } else if ($("#chall_day").hasClass("on") == true) {
            fdata.append("chall_day", "daily");
        }

        if ($("#action1").val()) {
            fdata.append("action1", $("#action1").val());
        }

        if ($("#action2").val()) {
            fdata.append("action2", $("#action2").val());
        }

        if ($("#h4").val()) {
            fdata.append("h4", $("#h4").val());
        }

        if ($("input[name='chk01']").is(":checked") == true) {
            fdata.append("chk01", '1');
        } else {
            fdata.append("chk01", '0');
        }

        $.ajax({
            type: "POST",
            data: fdata,
            contentType: false,
            processData: false,
            url: '/inc/challenges_process.php',
            success: function(data) {
                console.log(data);

                if (data == "complete") {
                    location.href = '/challenge/list.php';
                    return false;
                }
            }
        });
    });


    //결과물 올리기
    $("input[name=file2]").on("change", function() {
        var file = this.files[0],
            fileName = file.name,
            fileSize = file.size;
        console.log(file);
        if (fileName) {
            $("#chall_file_txt").text("결과물로 올린 파일명 : " + fileName);
        }
    });



    /*$("input[name=files]").on("change", function() {
        var file = this.files[0],
            fileName = file.name,
            fileSize = file.size;
        //	console.log(file);
        //	console.log(fileName);
        if (fileName) {
            //$("#chall_file_txt").text("결과물로 올린 파일명 : " + fileName);
        }
    });*/


    //챌린지 완료하기
    $("#challenges_complete").click(function() {
        var fdata = new FormData();
        fdata.append("mode", "challenges_complete");
        fdata.append("chall_idx", $(this).val());


        //var form = jQuery("ajaxFrom")[0];
        //var formData = new FormData(form);
        fdata.append("message", "ajax로 파일 전송하기");
        if ($("#file2").val()) {
            fdata.append("file", $("#file2")[0].files[0]);
        }

        if (confirm("챌린지를 완료 하시겠습니까?")) {

            $.ajax({
                type: "POST",
                data: fdata,
                contentType: false,
                processData: false,
                url: '/inc/process.php',
                success: function(data) {
                    console.log(data);
                    if (data == "not_id") {
                        alert("본인이 등록한 챌린지는 완료를 할 수 없습니다.");
                        return false;
                    } else if (data == "comment") {
                        alert("해당 챌린지에 참여 댓글을 작성해주세요.");
                        return false;

                    } else if (data == "expire_day") {
                        alert("챌린지가 종료 되었습니다.");
                        return false;
                    } else if (data == "complete") {
                        location.href = '/challenge/list.php';
                        return false;
                    } else if (data == "coin_not") {
                        alert("챌린지를 완료할 수 없습니다.");
                        return false;
                    } else if (data == "chall_complete") {
                        alert("해당 챌린지는 이미 완료하였습니다.");
                        return false;
                    } else if (data == "chall_ready") {
                        alert("해당 챌린지는 시작되지 않아 완료할 수 없습니다.");
                        return false;
                    } else if (data == "not_files1") {
                        alert("해당 챌린지는 결과물이 없어 완료하실 수 없습니다.\n챌린지 결과물을 등록하시기 바랍니다.");
                        return false;
                    } else if (data == "not_files2") {
                        alert("파일이 업로드 되지 않아 챌린지를 완료 하실수 없습니다.\n첨부한 파일을 확인 하시기 바랍니다.");
                        return false;
                    }
                }
            });
        }

    });



    //챌린지 수정하기
    $(document).on("click", "button[id^='chall_edit']", function() {
        var val = $(this).val();
        if (val) {
            location.href = '/challenge/edit.php?idx=' + val;
        }
    });


    //챌린지 삭제하기
    $(document).on("click", "button[id='chall_del']", function() {
        var val = $(this).val();

        var fdata = new FormData();
        fdata.append("mode", "challenges_del");
        fdata.append("chall_idx", $(this).val());
        if (confirm("챌린지를 삭제 하시겠습니까?")) {
            $.ajax({
                type: "POST",
                data: fdata,
                contentType: false,
                processData: false,
                url: '/inc/process.php',
                success: function(data) {
                    console.log(data);
                    if (data == "complete") {
                        location.href = '/challenge/list.php';
                        return false;
                    }
                }
            });
        }
    });


    //댓글등록하기
    $(document).on("click", "button[id='comment_btn']", function() {

        if (!$("#comment").val()) {
            alert("댓글을 입력해주세요.");
            $("#comment").focus();
            return false;
        }

        var fdata = new FormData();
        fdata.append("mode", "challenges_comment");
        fdata.append("idx", $(this).val());
        fdata.append("comment", $("#comment").val());

        $.ajax({

            type: "post",
            data: fdata,
            contentType: false,
            processData: false,
            url: '/inc/process.php',
            success: function(data) {
                console.log(data);
                if (data == "complete") {
                    //location.href = '/challenge/list.php';
                    location.reload();
                    return false;
                }
            }

        });
    });


    //챌린지
    if ($(location).attr('pathname').indexOf("/challenge/") == 0) {

        var path = window.location.pathname;
        var pagename = path.split("/").pop();

        var page = parseInt($("#pageno").val());
        var page_count = parseInt($("#page_count").val());

        if ($("#rank").val() == "") {
            $("#rank").val("4");
        }

        $(".rew_cha_more").hide();

        if ($(".rew_conts_in").length > 0) {

            //챌린지
            $(".rew_conts_list_in ul").sortable({
                axis: "y",
                opacity: 0.7,
                zIndex: 9999,
                //placeholder:"sort_empty",
                cursor: "move"
            });
            $(".rew_conts_list_in ul").disableSelection();

            $(".rew_conts_list_in ul li button").click(function() {
                $(this).parent("li").toggleClass("on");
            });

            $(".rew_btn_icons_more").click(function() {
                $(".rew_icons").toggle();
            });

            //챌린지 좌측메뉴선택
            /*$(".rew_mypage_tab_04 a").click(function() {
                $(".rew_mypage_tab_04 li").removeClass("on");
                $(this).parent("li").addClass("on");
            });*/


            $(".rew_mypage_tab_04 a").click(function() {

                /*$(this).each(function(index, item) {
                    if ($(this).eq(index).hasClass("on") == true) {
                        //$(this).eq(index).addClass("on");
                    } else {
                        //$(this).eq(index).addClass("on");
                    }
                    //console.log(index);
                });*/

                /*$(this).parent("li").each(function(index, item) {
                    var no = $(this).index();
                    if ($(this).hasClass("on") == false) {
                        $(this).addClass("on");
                    } else {
                        $(this).removeClass("on");
                    }
                });*/
            });


            //챌린지 좌측메뉴선택
            $(".rew_mypage_tab_04 a").click(function() {
                $(".rew_mypage_tab_04 ul li").each(function(index, item) {
                    var no = $(this).index();
                    /*if ($(".rew_mypage_tab_04 ul li").eq(no).hasClass("on") == true) {
                        $(".rew_mypage_tab_04 ul li").eq(no).removeClass("on");
                    } else {
                        $(".rew_mypage_tab_04 ul li").eq(no).addClass("on");
                    }*/
                    //var a = $(this).parent("a").
                });

                $(this).find('ul li').each(function() {
                    //    var no = $(this).index();
                    //    console.log(no);
                });


                $(this).parent("li").each(function() {

                    var no = $(this).index();
                    var loop = $(".rew_mypage_tab_04 a").size();

                    for (var i = 0; i < loop; i++) {
                        if (i == no) {
                            if ($(".rew_mypage_tab_04 ul li").eq(i).hasClass("on") == true) {
                                $(".rew_mypage_tab_04 ul li").eq(no).removeClass("on");
                            } else {
                                $(".rew_mypage_tab_04 ul li").eq(no).addClass("on");
                            }
                        } else {
                            $(".rew_mypage_tab_04 ul li").eq(i).removeClass("on");
                        }
                    }
                });
            });




            //챌린지 카테고리선택
            $(".rew_cha_tab .rew_cha_tab_in button").click(function() {
                $(".rew_cha_tab .rew_cha_tab_in ul li").removeClass("on");
                $(this).parent("li").addClass("on");
                challenges_ajax_list();
            });

            setTimeout(function() {
                $(".rew_box").addClass("on");
            }, 400);


            //challenges_ajax_list();

            $(".rew_menu_onoff button").click(function() {
                var thisonoff = $(this);
                if (thisonoff.hasClass("on")) {
                    thisonoff.removeClass("on");
                    $(".rew_box").removeClass("on");

                } else {
                    thisonoff.addClass("on");
                    $(".rew_box").addClass("on");

                }


            });

            $(".rew_conts_scroll_04").scroll(function() {
                var rct = $(".rew_cha_list_in").offset().top;

                if (rct < 155) {
                    $(".rew_cha_tab").addClass("pos_fix");
                } else {
                    $(".rew_cha_tab").removeClass("pos_fix");
                }
            });

            $(".rew_cha_list_ul li").each(function() {
                var tis = $(this);
                var tindex = $(this).index();
                setTimeout(function() {
                    tis.addClass("sli");
                }, 700 + tindex * 200);
            });


            //챌린지 더보기
            $(".rew_cha_more button").click(function() {

                var fdata = new FormData();
                var cate = 0;
                var rank = $("#rank").val();


                //더보기 숨김
                if ($('.rew_cha_more').css('display') == "block") {
                    $('.rew_cha_more').hide();
                }

                var li_totcnt = parseInt($(".rew_cha_list_ul li").length);
                if (li_totcnt >= 0) {
                    var li_cnt = li_totcnt;
                }

                //페이지
                if (page > 0) {
                    page = page + 1;
                }

                //카테고리선택
                $(".rew_cha_tab .rew_cha_tab_in ul li").each(function(index, item) {
                    var no = $(this).index();
                    if ($(".rew_cha_tab .rew_cha_tab_in ul li").eq(no).hasClass("on") == true) {
                        cate = no;
                    }
                });

                fdata.append("mode", "challenges_list");
                fdata.append("gp", page);
                fdata.append("cate", cate);
                fdata.append("rank", rank);

                $.ajax({
                    type: "post",
                    data: fdata,
                    contentType: false,
                    processData: false,
                    url: '/inc/process.php',
                    success: function(data) {
                        console.log(data);

                        if (data) {
                            tdata = data.split("|");
                            if (tdata) {
                                var html = tdata[0];
                                var totcnt = tdata[1];
                                var listcnt = tdata[3];

                                $("#page_count").val(parseInt(listcnt));

                                $(".rew_cha_list_ul").append(html);

                                $(".rew_cha_list_ul li").each(function(index, item) {
                                    //console.log($(".rew_cha_list_ul li:last").index());
                                    var tis = $(this);
                                    var tindex = $(this).index();

                                    //var tindex = li_cnt + Number(index + 1);
                                    //console.log(tindex);
                                    //tis = $(".rew_cha_list_ul li").eq(tindex);
                                    //console.log("tindex " + $(".rew_cha_list_ul li").size());
                                    setTimeout(function() {
                                        tis.addClass("sli");
                                    }, 700 + tindex * 200 - 3000);
                                });

                                console.log("page :: " + page);


                                setTimeout(function() {

                                    var offset = $(".offset" + page).position();
                                    //console.log("pageno :: " + pageno);
                                    //console.log("top :: " + offset.top + 285);

                                    if (page > 2) {
                                        var scrollmove = 285;
                                    } else {
                                        var scrollmove = 285;
                                    }
                                    $(".rew_conts_scroll_04").animate({ scrollTop: offset.top + scrollmove }, 700);
                                    $(".offset" + page).removeClass("offset" + page);

                                    if (page >= page_count) {
                                        $(".rew_cha_more").hide();
                                    }

                                }, 700);


                                /*setTimeout(function(){
                                	var offset = $(".offset0").offset();
                                	$(".rew_conts_scroll_04").animate({scrollTop : offset.top + 285}, 700);
                                	$(".offset0").removeClass("offset0");
                                	$(".rew_cha_more").hide();
                                },700);*/


                                //더보기 버튼
                                setTimeout(function() {
                                    if (page >= $("#page_count").val()) {
                                        $(".rew_cha_more").hide();
                                    } else {
                                        $(".rew_cha_more").show();
                                    }
                                }, 3000);

                                return false;
                            }
                        }
                    }

                });


                //$(".rew_cha_list_ul").append('<li class="category_06 offset0"><a href="#"><div class="cha_box"><div class="cha_box_t"><span class="cha_cate">기타</span><span class="cha_title">하루에 칭찬 3번을 했을 뿐인데 </span><span class="cha_coin"><strong>1,000</strong>코인</span></div><div class="cha_box_b"><span class="cha_member">18/20 명 도전중</span><span class="cha_dday">D - 7</span></div></div></a></li><li class="category_01"><a href="#"><div class="cha_box"><div class="cha_box_t"><span class="cha_cate">업무</span><span class="cha_title">윈도우 업데이트 점검한다면</span><span class="cha_coin"><strong>500</strong>코인</span></div><div class="cha_box_b"><span class="cha_member">12/20 명 도전중</span><span class="cha_dday">D - 20</span></div></div></a></li><li class="category_02"><a href="#"><div class="cha_box"><div class="cha_box_t"><span class="cha_cate">생활</span><span class="cha_title">책 읽고 독서메모를 남긴다면</span><span class="cha_coin"><strong>1,500</strong>코인</span></div><div class="cha_box_b"><span class="cha_member">7/20 명 도전중</span><span class="cha_dday">D - 10</span></div></div></a></li><li class="category_05"><a href="#"><div class="cha_box"><div class="cha_box_t"><span class="cha_cate">신입사원</span><span class="cha_title">보고서 작성법을 배우면</span><span class="cha_coin"><strong>1,000</strong>코인</span></div><div class="cha_box_b"><span class="cha_member">1/1 명 도전중</span><span class="cha_dday">D - 30</span></div></div></a></li><li class="category_02"><a href="#"><div class="cha_box"><div class="cha_box_t"><span class="cha_cate">생활</span><span class="cha_title">캔크러시 챌린지, 그저 밟기만 했을 뿐인데</span><span class="cha_coin"><strong>500</strong>코인</span></div><div class="cha_box_b"><span class="cha_member">12/20 명 도전중</span><span class="cha_dday">D - 60</span></div></div></a></li><li class="category_05"><a href="#"><div class="cha_box"><div class="cha_box_t"><span class="cha_cate">신입사원</span><span class="cha_title">비즈니스 명함 예절을 배우면</span><span class="cha_coin"><strong>10,000</strong>코인</span></div><div class="cha_box_b"><span class="cha_member">1/1 명 도전중</span><span class="cha_dday">D - 30</span></div></div></a></li><li class="category_06"><a href="#"><div class="cha_box"><div class="cha_box_t"><span class="cha_cate">기타</span><span class="cha_title">하루에 칭찬 3번을 했을 뿐인데 </span><span class="cha_coin"><strong>1,000</strong>코인</span></div><div class="cha_box_b"><span class="cha_member">18/20 명 도전중</span><span class="cha_dday">D - 7</span></div></div></a></li><li class="category_01"><a href="#"><div class="cha_box"><div class="cha_box_t"><span class="cha_cate">업무</span><span class="cha_title">윈도우 업데이트 점검한다면</span><span class="cha_coin"><strong>500</strong>코인</span></div><div class="cha_box_b"><span class="cha_member">12/20 명 도전중</span><span class="cha_dday">D - 20</span></div></div></a></li><li class="category_02"><a href="#"><div class="cha_box"><div class="cha_box_t"><span class="cha_cate">생활</span><span class="cha_title">책 읽고 독서메모를 남긴다면</span><span class="cha_coin"><strong>1,500</strong>코인</span></div><div class="cha_box_b"><span class="cha_member">7/20 명 도전중</span><span class="cha_dday">D - 10</span></div></div></a></li><li class="category_05"><a href="#"><div class="cha_box"><div class="cha_box_t"><span class="cha_cate">신입사원</span><span class="cha_title">보고서 작성법을 배우면</span><span class="cha_coin"><strong>1,000</strong>코인</span></div><div class="cha_box_b"><span class="cha_member">1/1 명 도전중</span><span class="cha_dday">D - 30</span></div></div></a></li><li class="category_02"><a href="#"><div class="cha_box"><div class="cha_box_t"><span class="cha_cate">생활</span><span class="cha_title">캔크러시 챌린지, 그저 밟기만 했을 뿐인데</span><span class="cha_coin"><strong>500</strong>코인</span></div><div class="cha_box_b"><span class="cha_member">12/20 명 도전중</span><span class="cha_dday">D - 60</span></div></div></a></li><li class="category_05"><a href="#"><div class="cha_box"><div class="cha_box_t"><span class="cha_cate">신입사원</span><span class="cha_title">비즈니스 명함 예절을 배우면</span><span class="cha_coin"><strong>10,000</strong>코인</span></div><div class="cha_box_b"><span class="cha_member">1/1 명 도전중</span><span class="cha_dday">D - 30</span></div></div></a></li>');

            });



            //챌린지 더보기
            //챌린지 내가 만든챌린지
            $(".tab_chall_02,.tab_chall_03,.tab_chall_04").click(function() {
                challenges_ajax_list();
            });


            //더보기 버튼
            setTimeout(function() {
                if (page >= $("#page_count").val()) {
                    $(".rew_cha_more").hide();
                } else {
                    $(".rew_cha_more").show();
                }
            }, 3000);

            //챌린지
        }

        if (pagename == "index.php") {

            //    challenges_ajax_list();
        }


        //챌린지정렬
        $(".rew_cha_sort .rew_cha_sort_in button").click(function() {


            var rank = $(this).val();
            var fdata = new FormData();
            var page = parseInt($("#pageno").val());
            var page_count = parseInt($("#page_count").val());
            var cate;


            //더보기 숨김
            if ($('.rew_cha_more').css('display') == "block") {
                $('.rew_cha_more').hide();
            }

            $("#rank").val(rank);

            //카테고리선택
            $(".rew_cha_tab .rew_cha_tab_in ul li").each(function(index, item) {
                var no = $(this).index();
                if ($(".rew_cha_tab .rew_cha_tab_in ul li").eq(no).hasClass("on") == true) {
                    cate = no;
                }
            });

            fdata.append("mode", "challenges_list");
            fdata.append("gp", page);
            fdata.append("cate", cate);
            fdata.append("rank", rank);


            $.ajax({
                type: "post",
                data: fdata,
                contentType: false,
                processData: false,
                url: '/inc/process.php',
                success: function(data) {
                    console.log(data);
                    if (data) {
                        tdata = data.split("|");
                        if (tdata) {
                            var html = tdata[0];
                            var totcnt = tdata[1];
                            var listcnt = tdata[2];

                            //페이지수
                            $("#page_count").val(parseInt(listcnt));

                            var html = data;
                            $(".rew_cha_list_ul").html(html);


                            if (rank == 1) {

                                $(".rew_cha_sort .rew_cha_sort_in button:eq(0)").text("참여자 많은 순");
                            } else if (rank == 2) {
                                $(".rew_cha_sort .rew_cha_sort_in button:eq(0)").text("기간 짧은 순");
                            } else if (rank == 3) {
                                $(".rew_cha_sort .rew_cha_sort_in button:eq(0)").text("코인 높은 순");
                            } else if (rank == 4) {
                                $(".rew_cha_sort .rew_cha_sort_in button:eq(0)").text("최신 순");
                            }

                            $(".rew_cha_list_ul li").each(function() {
                                var tis = $(this);
                                var tindex = $(this).index();
                                setTimeout(function() {
                                    tis.addClass("sli");
                                }, 100 + tindex * 200);
                            });


                            //더보기 버튼
                            setTimeout(function() {
                                if (page >= $("#page_count").val()) {
                                    $(".rew_cha_more").hide();
                                } else {
                                    $(".rew_cha_more").show();
                                }
                            }, 3000);

                        }
                    }
                }
            });

        });

    }





    setTimeout(function() {
        $(".tabs_on").addClass("now_01");
    }, 1100);

    $(".btn_next_step_02").click(function() {
        $(".rew_cha_step_01").addClass("step_z");
        $(".rew_cha_step_02").addClass("step_z");
        $(".rew_cha_step_03").removeClass("step_z");
        $(".rew_cha_step_01").animate({ "left": -100 + "%" }, 700);
        $(".rew_cha_step_02").animate({ "left": 0 + "%" }, 700);
        $(".rew_cha_step_03").animate({ "left": 100 + "%" }, 700);

        setTimeout(function() {
            $(".rew_cha_step_01").animate({ scrollTop: 0 }, 0);
            $(".rew_cha_step_02").animate({ scrollTop: 0 }, 0);
            $(".rew_cha_step_03").animate({ scrollTop: 0 }, 0);
        }, 200);

        setTimeout(function() {
            $(".tabs_on").addClass("now_02");
        }, 1100);
    });
    $(".btn_prev_step_01").click(function() {
        $(".rew_cha_step_01").addClass("step_z");
        $(".rew_cha_step_02").addClass("step_z");
        $(".rew_cha_step_03").removeClass("step_z");
        $(".rew_cha_step_01").animate({ "left": 0 + "%" }, 700);
        $(".rew_cha_step_02").animate({ "left": 100 + "%" }, 700);
        $(".rew_cha_step_03").animate({ "left": -100 + "%" }, 700);

        setTimeout(function() {
            $(".rew_cha_step_01").animate({ scrollTop: 0 }, 0);
            $(".rew_cha_step_02").animate({ scrollTop: 0 }, 0);
            $(".rew_cha_step_03").animate({ scrollTop: 0 }, 0);
        }, 200);

        setTimeout(function() {
            $(".tabs_on").removeClass("now_02");
        }, 1100);
    });
    $(".btn_next_step_03").click(function() {
        $(".rew_cha_step_01").removeClass("step_z");
        $(".rew_cha_step_02").addClass("step_z");
        $(".rew_cha_step_03").addClass("step_z");
        $(".rew_cha_step_01").animate({ "left": 100 + "%" }, 700);
        $(".rew_cha_step_02").animate({ "left": -100 + "%" }, 700);
        $(".rew_cha_step_03").animate({ "left": 0 + "%" }, 700);

        setTimeout(function() {
            $(".rew_cha_step_01").animate({ scrollTop: 0 }, 0);
            $(".rew_cha_step_02").animate({ scrollTop: 0 }, 0);
            $(".rew_cha_step_03").animate({ scrollTop: 0 }, 0);
        }, 200);

        setTimeout(function() {
            $(".tabs_on").addClass("now_03");
        }, 1100);
    });


    $(".rew_menu_onoff button").click(function() {
        var thisonoff = $(this);
        if (thisonoff.hasClass("on")) {
            thisonoff.removeClass("on");
            $(".rew_box").removeClass("on");

        } else {
            thisonoff.addClass("on");
            $(".rew_box").addClass("on");

        }
    });


    //챌린지작성하기
    $(".rew_cha_step_01").scroll(function() {
        var rct = $(".rew_cha_write_type").offset().top;
        if (rct < 175) {
            $(".rew_cha_write_tabs").addClass("pos_fix");
            $(".rew_cha_write_in").addClass("pos_fix");
        } else {
            $(".rew_cha_write_tabs").removeClass("pos_fix");
            $(".rew_cha_write_in").removeClass("pos_fix");
        }
    });
    $(".rew_cha_step_02").scroll(function() {
        var rct = $(".rew_cha_setting_date").offset().top;
        if (rct < 175) {
            $(".rew_cha_write_tabs").addClass("pos_fix");
            $(".rew_cha_write_in").addClass("pos_fix");
        } else {
            $(".rew_cha_write_tabs").removeClass("pos_fix");
            $(".rew_cha_write_in").removeClass("pos_fix");
        }
    });





    $(window).on("load", function() {

        //일주일
        if ($(".tc_tab .tab_week").hasClass("on") == true) {
            $(".tc_index_list_month").hide();
            $(".tc_index_list").hide();
            $(".tc_index_list_week").show();
            $(".tc_tab button").removeClass("on");
            $(".tc_tab .tab_week").addClass("on");
        }

        //오늘
        if ($(".tc_tab .tab_day").hasClass("on") == true) {
            $(".tc_index_list_week").hide();
            $(".tc_index_list_month").hide();
            $(".tc_index_list").show();
            $(".tc_tab button").removeClass("on");
            $(".tc_tab .tab_day").addClass("on");
        }


        //console.log("tab :: " + $("input[name='tabval']").val());

        if ($(".tab_week").hasClass("on") == true) {
            //$("input[name='tabval']").val("");
            $(".tab_week").trigger("click");
        } else {
            $(".tab_day").trigger("click");
        }




    });

});




function challenges_ajax_list() {

    var fdata = new FormData();
    var page = parseInt($("#pageno").val());
    var cate = 0;
    var page_count = parseInt($("#page_count").val());


    //더보기 숨김
    if ($('.rew_cha_more').css('display') == "block") {
        $('.rew_cha_more').hide();
    }

    //카테고리선택
    $(".rew_cha_tab .rew_cha_tab_in ul li").each(function(index, item) {
        var no = $(this).index();
        if ($(".rew_cha_tab .rew_cha_tab_in ul li").eq(no).hasClass("on") == true) {
            cate = no;
        }
    });

    //내가만든챌린지
    if ($(".tab_chall_04").hasClass("on") == true) {
        fdata.append("chmy", 1);
    } else {
        fdata.append("chmy", "");
    }

    //정복한 챌린지
    if ($(".tab_chall_03").hasClass("on") == true) {
        fdata.append("chcomplete", 1);
    } else {
        fdata.append("chcomplete", "");
    }


    fdata.append("mode", "challenges_list");
    fdata.append("cate", cate);
    fdata.append("gp", page);

    $.ajax({
        type: "post",
        data: fdata,
        contentType: false,
        processData: false,
        url: '/inc/process.php',
        success: function(data) {
            console.log(data);
            if (data) {
                tdata = data.split("|");
                if (tdata) {
                    var html = tdata[0];
                    var totcnt = tdata[1];
                    var listcnt = tdata[2];

                    //페이지수
                    $("#page_count").val(parseInt(listcnt));

                    $(".rew_cha_count strong").text(totcnt);
                    //    $(".rew_cha_list_ul").empty();

                    $(".rew_cha_list_ul").html("");

                    $(".rew_cha_list_ul").append(html);
                    $(".rew_cha_list_ul li").each(function() {
                        var tis = $(this);
                        var tindex = $(this).index();
                        setTimeout(function() {
                            tis.addClass("sli");
                        }, 100 + tindex * 200);
                    });

                    //더보기 버튼
                    setTimeout(function() {
                        if (page >= $("#page_count").val()) {
                            $(".rew_cha_more").hide();
                        } else {
                            $(".rew_cha_more").show();
                        }
                    }, 4000);

                }
                return false;
            }
        }

    });

}




function chall_more() {

    var page = parseInt($("#pageno").val());
    var page_count = parseInt($("#page_count").val());


    console.log(page);
    console.log(page_count);

    //더보기 버튼
    setTimeout(function() {
        if (page >= page_count) {
            $(".rew_cha_more").hide();
        } else {
            $(".rew_cha_more").show();
        }
    }, 3000);

}






//챌린지 파일첨부 추가
function addFileForm() {
    var cnt = $("div[id^='files_add']").length;
    var chk_input = $("div[id^='files_add']").last().attr("id");
    if (chk_input != undefined) {
        tmp_no = parseInt(chk_input.replace("files_add", ""));
        no = tmp_no + 1;
    } else {
        no = 1;
    }

    var html = "<div class='' id='files_add" + no + "'>";
    html += "<input type='file' id='files" + no + "' name='files' />";
    html += "<button id='file_del'> X </button></div>";

    if (cnt < 3) {
        $("#file_add").append(html);
    }
}


//챌린지 이미지 미리보기
function handleFileSelect(event) {
    var input = this;
    var id = $(this).attr("id");
    var no = id.replace("img_file", "");

    //console.log(input.files)
    if (input.files && input.files.length) {
        var reader = new FileReader();
        this.enabled = false
        reader.onload = (function(e) {
            //console.log(e)

            //let img = new Image();
            //img.src = e.target.result;
            //console.log( img.width);

            /*				console.log( input.files[0]['name']);
            				var img = $("#img_file"+no);
            				console.log( img.width );
            */

            $("#preview" + no).html(['<img class="thumb" src="', e.target.result, '" title="', escape(e.name), '"/>'].join(''))
        });
        reader.readAsDataURL(input.files[0]);
    }
}


function file_del(n) {
    console.log(" del : " + n);
    //$("#div_file" + n + "").remove();
    //$("#files" + n + "").val('');
    //$("li[id='div_file"+n+"']").remove();
}


function chage_getdate(str) {

    var wdate_today = str;
    var yyyy = wdate_today.substr(0, 4);
    var mm = wdate_today.substr(5, 2);
    var dd = wdate_today.substr(8, 2);
    var wdate = yyyy + "-" + mm + "-" + dd;
    return wdate;

}


//날짜 치환 : get_date(년월일 , prev or next)
function get_date(v, str = "next") {

    var getdata = v;
    var yyyy = getdata.substr(0, 4);
    var mm = getdata.substr(5, 2);
    var dd = getdata.substr(8, 2);

    var now = new Date();
    var hh = now.getHours();
    var ii = now.getMinutes();
    var ss = now.getSeconds();

    var d = new Date(yyyy + "/" + mm + "/" + dd + " " + hh + ":" + ii + ":" + ss);

    //var yesterday = new Date(d.setDate(d.getDate() - 1));
    // Feb 18 2021 00:00:00 GMT+0827( 대한민국 표준시 )

    console.log(yyyy + "/" + mm + "/" + dd + " " + hh + ":" + ii + ":" + ss);

    //이전날:prev , 다음날:next
    if (str == "prev") {
        //newdate = new Date(d.setDate(d.getDate() - 1));
        newdate = new Date(d.getFullYear(), d.getMonth(), d.getDate() - 1, hh, ii, ss); //.toLocaleDateString();
        //var newdate = new Date(d.setDate(d.getDate() - 1)); 
        //var newdate = new Date(Date.parse(d) - 1 * 1000 * 60 * 60 * 24);
    } else if (str == "next") {
        //newdate = new Date(d.setDate(d.getDate() + 1)); // (new Date(Date.parse(d) + 1 * 1000 * 60 * 60 * 24));
        newdate = new Date(d.getFullYear(), d.getMonth(), d.getDate() + 1, hh, ii, ss); //.toLocaleDateString();
    }


    //    console.log(newdate);



    var year = newdate.getFullYear();
    var month = newdate.getMonth();
    var day = newdate.getDate();


    /*console.log(year);
    console.log("month " + month);
    console.log(day);
	*/
    if (month < 10) month = "0" + month;
    if (day < 10) day = "0" + day;

    if (year && month && day) {
        var tday = year + "-" + month + "-" + day;
        return tday;
    }
}




//쿠키값체크
function GetCookie(name) {
    var result = null;
    var myCookie = " " + document.cookie + ";";
    var searchName = " " + name + "=";
    var startOfCookie = myCookie.indexOf(searchName);
    var endOfCookie;
    if (startOfCookie != -1) {
        startOfCookie += searchName.length;
        endOfCookie = myCookie.indexOf(";", startOfCookie);
        result = unescape(myCookie.substring(startOfCookie, endOfCookie));
    }
    return result;
}


//날짜(년-월-일)
function getTodayType() {
    var date = new Date();
    return date.getFullYear() + "-" + ("0" + (date.getMonth() + 1)).slice(-2) + "-" + ("0" + date.getDate()).slice(-2);
}


function wdate_link(d) {

    var url = "/works/list.php";

    if ($(".tab_week").hasClass("on") == true) {
        var dtab = "week";
    } else {
        var dtab = "day";
    }

    if (d) {
        var q = "?wdate=" + d + "&dtab=" + dtab;
        location.href = url + q;
    }
}


//메인페이지 오늘업무
function location_works(v) {
    if (GetCookie("user_id") != null) {
        if (v == "works") {
            location.href = "/works/list.php";
        } else if (v == "challenge") {
            location.href = "/challenge/list.php";
        } else if (v == "reward") {
            location.href = "/coins/index.php";
        } else {
            location.href = "/works/list.php";
        }
        return false;
    } else {
        //$(this).attr("id");
        $("#login_btn").trigger("click");
    }
}



//날짜 체크
function clickdate(date) {
    var date = date;
    var DataFormat;
    var RegPhonNum;

    //date = date.replace(RegNotNum, ''); // 숫자만 남기기
    date = date.replace(/[^0-9-]/g, '').replace(/(\..*)\./g, '$1');

    if (date == "" || date == null || date.length < 5) {
        this.value = date;
        return;
    }

    // 날짜 포맷(yyyy-mm-dd) 만들기 
    if (date.length <= 6) {
        DataFormat = "$1-$2"; // 포맷을 바꾸려면 이곳을 변경
        RegPhonNum = /([0-9]{4})([0-9]+)/;
    } else if (date.length <= 8) {
        DataFormat = "$1-$2-$3"; // 포맷을 바꾸려면 이곳을 변경
        RegPhonNum = /([0-9]{4})([0-9]{2})([0-9]+)/;
    }

    date = date.replace(RegPhonNum, DataFormat);
    //this.value = date;
    // 모두 입력됐을 경우 날짜 유효성 확인
    if (date.length == 10) {
        var isVaild = true;
        if (isNaN(Date.parse(date))) {
            // 유효 날짜 확인 여부
            isVaild = false;
        } else {
            // 년, 월, 일 0 이상 여부 확인
            var date_sp = date.split("-");
            date_sp.forEach(function(sp) {
                if (parseInt(sp) == 0) {
                    isVaild = false;
                }
            });

            // 마지막 일 확인
            var last = new Date(new Date(date).getFullYear(), new Date(date).getMonth() + 1, 0);
            // 일이 달의 마지막날을 초과했을 경우 다음달로 자동 전환되는 현상이 있음 (예-2월 30일 -> 3월 1일)
            if (parseInt(date_sp[1]) != last.getMonth() + 1) {
                var date_sp2 = date_sp.slice(0);
                date_sp2[2] = '01';
                var date2 = date_sp2.join("-");
                last = new Date(new Date(date2).getFullYear(), new Date(date2).getMonth() + 1, 0);
            }

            if (last.getDate() < parseInt(date_sp[2])) {
                isVaild = false;
            }
        }

        if (!isVaild) {
            alert("입력하신 날짜가 잘못 되었습니다. \n다시 입력하세요.");
            return;
        } else {
            var tmp;
            tmp = date.substr(0, 4);
            tmp += '-';
            tmp += date.substr(5, 2);
            tmp += '-';
            tmp += date.substr(8);
            //    $("#date1").val(tmp);
            return;
        }
    }
}


//인덱스번호추출
function change_num(v) {
    if (v) {
        var regex = /[^0-9]/g;
        var result = v.replace(regex, "");
        if (result) {
            return result;
        }
    }
}