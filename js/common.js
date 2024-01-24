$(function () {
  var path = window.location.pathname;
  var pagename = path.split("/").pop();

  if ($(location).attr("pathname").indexOf("/live/") == 0) {
    $(document).on(
      "mousemove",
      ".rew_warp_in,.layer_result_box,.layer_deam",
      function (e) {
        //    console.log("마우스이동");
        //    stop_page_reload();
      }
    );

    //메모작성,메모수정,레이어(이름/부서명검색),전체(이름/부서명검색)
    $(document).on(
      "keyup",
      "#textarea_memo,textarea[id^='tdw_comment_edit'],#input_user_search,#input_index_search_new",
      function (e) {
        //    console.log("입력");
        //    stop_page_reload();
      }
    );
  }

  //1. 시작페이지가 아닌경우
  //2. 쿼리스트링 전달 받지 않은경우만
  //3. 로그인 되지 않았을 때 로그인 레이어 띄우기
  if ($(location).attr("pathname") == "/index.php") {
    if (!window.location.search) {
      if (GetCookie("user_id") == null) {
        $(".rew_layer_login").show();
      }
    }
  }

  //좌측메뉴숨김
  if ($(location).attr("pathname").indexOf("/challenge/") == 0) {
  } else if ($(location).attr("pathname").indexOf("/reward/") == 0) {
    //console.log("g3");
    $(".rew_box").addClass("on");
  } else if ($(location).attr("pathname").indexOf("/party/") == 0) {
    $(".rew_box").addClass("on");
    //console.log("project");
  } else if ($(location).attr("pathname").indexOf("/insight/") == 0) {
    $(".rew_box").addClass("on");
    //console.log("insight");
  }

  if (GetCookie("onoff") == "1") {
    if ($(".rew_warp_in .rew_box").hasClass("on") == true) {
      console.log("gg");
      $(".rew_warp_in .rew_box").removeClass("on");
    }
  } else {
    if ($(location).attr("pathname").indexOf("/team/") != 0) {
      $(".rew_warp_in .rew_box").addClass("on");
    }
  }

  $(".rew_menu_onoff").hasClass("on");

  if (GetCookie("rew_menu_onoff") == "1") {
    //$(".rew_box").removeClass("on");
    //console.log(" rew_box :: " + $(".rew_box").hasClass("on"));
    //console.log(1111111);
  }

  //속성변경
  $("#a1").css("ime-mode", "inactive");

  $(".tab_shop").attr("disabled", false);
  $(".tab_shop").css("color", "#ccc");

  $(".tab_buy").attr("disabled", false);
  $(".tab_buy").css("color", "#ccc");

  //챌린지 혼합형 이미지 사이즈 조정
  $(".mix_imgs img").css("width", "300px");

  //코인설정
  var setcoin = 100;

  //챌린지버튼
  //$("button[id^='chall_days']").css("display","none");

  //챌린지 참여자목록
  $("#add_user_list").css("display", "none");
  //$(".works_today").css('z-index', 9999);

  $("#h5").attr("maxlength", 2);
  $(
    "input[name=date1],input[name=date2],input[name=date_02],input[name=date_03],input[name=date_04]"
  ).attr("maxlength", 10);
  $("input[name=coin_point],input[name=works_today],.input_count").attr(
    "maxlength",
    10
  );
  $(".input_count").attr("maxlength", 2);
  $(".input_coin").attr("maxlength", 8);

  $(
    "input[name=date1],input[name=date2],input[id=input_userfile],.input_search"
  ).attr("autocomplete", "off");

  //$(".works_today").zindex($(document).zindex() + 999);

  //$(document).on('propertychange change keyup paste input', 'input[name=works_today]', function(event) {

  $("#works_today").on("propertychange change keyup paste input", function () {
    //$("input[name=works_today]").change(function() {
    //$("input[name=works_today]").on("propertychange change keyup paste input", function() {
    //$("input[name=works_today]").change("change keyup input", function(event) {
    //console.log(" wdate :: " + $(this).val());
  });

  //숫자만입력
  $(document).on(
    "keyup",
    "input[name=coin_point],input[id=h4],input[id=h5],.input_count,.input_coin",
    function (event) {
      this.value = this.value.replace(/[^0-9]/g, ""); // 입력값이 숫자가 아니면 공백
      this.value = this.value.replace(/,/g, ""); // ,값 공백처리
      this.value = this.value.replace(/\B(?=(\d{3})+(?!\d))/g, ","); // 정규식을 이용해서 3자리 마다 , 추가
    }
  );

  /*
    $(document).on("keyup", ".input_coin", function(event) {
      //var tmpValue = $(obj).val().replace(/[^0-9,]/g, '');
      //tmpValue = tmpValue.replace(/[,]/g, '');
      //tmpValue = $(this).val()
      //        $(this).val(addComma($(this).val()));
    });
    */

  //템플릿 생성
  $("#rew_mypage_tpl_write").click(function () {
    //setCookie('chall_tpl', '1', '1');
    location.href = "/challenge/write.php";
  });

  //챌린지 템플릿
  $(document).on("click", "#tab_chall_02", function () {
    if ($("#tab_chall_02").hasClass("on") == false) {
      $("#tab_chall_02").addClass("on");
    }

    if ($("#tab_chall_03").hasClass("on") == true) {
      $("#tab_chall_03").removeClass("on");
    }

    if ($("#tab_chall_04").hasClass("on") == true) {
      $("#tab_chall_04").removeClass("on");
    }
    $("#chall_type").val("template");
    challenges_ajax_list();
  });

  //내가만든 챌린지
  $(document).on("click", "#tab_chall_03", function () {
    /*if ($("#tab_chall_03").hasClass("on") == false) {
        $("#tab_chall_03").addClass("on");
      }

      if ($("#tab_chall_02").hasClass("on") == true) {
        $("#tab_chall_02").removeClass("on");
      }

      if ($("#tab_chall_04").hasClass("on") == true) {
        $("#tab_chall_04").removeClass("on");
      }
      $("#chall_type").val("chmy");
      challenges_ajax_list();
    */
    location.href = "/challenge/make_list.php";
  });

  //임시저장 챌린지
  $(document).on("click", "#tab_chall_04", function () {
    /*if ($("#tab_chall_04").hasClass("on") == false) {
        $("#tab_chall_04").addClass("on");
      }

      if ($("#tab_chall_02").hasClass("on") == true) {
        $("#tab_chall_02").removeClass("on");
      }

      if ($("#tab_chall_03").hasClass("on") == true) {
        $("#tab_chall_03").removeClass("on");
      }

      $("#chall_type").val("tempflag");
      challenges_ajax_list();*/
    location.href = "/challenge/tmpsave_list.php";
  });

  if ($(window).width() > 640) {
    $(".menu_open").mouseenter(function () {
      if ($(".menu_box").hasClass("off")) {
      } else {
        $(".menu_box").show();
      }
    });

    $(".menu_list").mouseleave(function () {
      $(".menu_box").removeClass("off");
      $(".menu_box").hide();
    });
  } else {
  }

  $(".menu_open").click(function () {
    $(".menu_box").removeClass("off");
    $(".menu_box").show();
  });

  $(".menu_close").click(function () {
    $(".menu_box").addClass("off");
    $(".menu_box").hide();
  });

  $(window).resize(function () {
    if ($(window).width() > 640) {
      $(".menu_open").mouseenter(function () {
        if ($(".menu_box").hasClass("off")) {
        } else {
          $(".menu_box").show();
        }
      });

      $(".menu_list").mouseleave(function () {
        $(".menu_box").removeClass("off");
        $(".menu_box").hide();
      });
    } else {
    }
  });

  //로그인하기
  $("input").keypress(function (e) {
    var id = $(this).attr("id");
    if (id == "z1" || id == "z2") {
      if (e.keyCode == 13) {
        $("#loginbtn").trigger("click");
      }
    }
  });

  //챌린지 뷰페이지 이미지 확대 닫기
  $(".layer_cha_image .layer_cha_image_in, .layer_cha_image .layer_deam").click(
    function () {
      $(".layer_cha_image").hide();
    }
  );

  //$(document).on("click", ".tc_request strong", function() {
  //});

  $("input[name=works_today]").bind("change keyup input", function (event) {
    if (!(event.keyCode >= 37 && event.keyCode <= 40)) {
      var inputVal = $(this).val();
      $(this).val(inputVal.replace(/[^0-9.]/gi, ""));
    }
  });

  //숫자만
  $("input[name=coin_point], #h4,#h5,.input_count,.input_coin").bind(
    "change keyup input",
    function (event) {
      if (!(event.keyCode >= 37 && event.keyCode <= 40)) {
        var inputVal = $(this).val();
        $(this).val(inputVal.replace(/[^0-9,]/gi, ""));
      }
    }
  );

  $("#date1,#date2,#date_02,#date_03,#date_04,#goal3,input[id^=workdate]").bind(
    "change keyup input",
    function (event) {
      if (!(event.keyCode >= 37 && event.keyCode <= 40)) {
        var inputVal = $(this).val();
        $(this).val(inputVal.replace(/[^0-9-]/gi, ""));
      }
    }
  );

  $("#req_date,#date_sdate").bind("change keyup input", function (event) {
    if (!(event.keyCode >= 37 && event.keyCode <= 40)) {
      var inputVal = $(this).val();
      $(this).val(inputVal.replace(/[^0-9-]/gi, ""));
    }
  });

  $("#req_stime,#req_etime,#date_stime").bind(
    "change keyup input",
    function (event) {
      if (!(event.keyCode >= 37 && event.keyCode <= 40)) {
        var inputVal = $(this).val();
        $(this).val(inputVal.replace(/[^0-9:]/gi, ""));
      }
    }
  );

  //서비스가입버튼
  $("#joinchk").click(function () {
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
        url: "/inc/process.php",
        success: function (data) {
          if (data == "complete") {
            alert("가입되었습니다.");
            location.href = "/admin/pay.php";
            return false;
          } else if (data == "rejoin") {
            alert("이미 가입된 정보 입니다.");
          }
        },
      });
    }
  });

  //사용자인증체크
  $("#userchk").click(function () {
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
        url: "/inc/process.php",
        success: function (data) {
          if (data == "complete") {
            alert("사용자 인증이 완료 되었습니다.");
            location.href = "/index.php";
            return false;
          } else if (data == "reuser") {
            alert("이미 인증된 정보 입니다.");
          }
        },
      });
    }
  });

  //결제버튼
  $("#paybtn").click(function () {
    if (
      confirm(
        "선택한 " +
          $("#paycnt").val() +
          "명의 사용자에게 초대 이메일을 발송하시겠습니까?"
      )
    ) {
      var fdata = new FormData();
      fdata.append("mode", "pay");
      fdata.append("paycnt", $("#paycnt").val());

      $.ajax({
        type: "POST",
        data: fdata,
        contentType: false,
        processData: false,
        url: "/inc/process.php",
        success: function (data) {
          console.log(data);
          var tmp = data.split("|");
          if (tmp[0] == "ok") {
            var number = tmp[1];
            //location.href = "/admin/member.php";

            var form = document.createElement("form");
            var hiddenField = document.createElement("input");
            hiddenField.setAttribute("type", "hidden");
            hiddenField.setAttribute("name", "number"); // 받을 네이밍
            hiddenField.setAttribute("value", number); // 넘길 파라메터
            form.appendChild(hiddenField);
            form.setAttribute("method", "POST");
            form.setAttribute("action", "/admin/member.php"); // URL
            document.body.appendChild(form);
            form.submit();
            return false;
          }
        },
      });
    }
  });

  $("#z1,#z2").blur(function () {
    var val = $(this).val();
    if (val) {
      $(this).parent().addClass("now_focus");
    } else {
      $(this).parent().removeClass("now_focus");
    }
  });

  //로그인버튼
  $("#loginbtn").click(function () {
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

    if ($("input[name='id_save']").is(":checked") == true) {
      fdata.append("id_save", true);
    }

    if ($("#loginbtn").hasClass("ra_btn_login_mo")) {
      fdata.append("mobile", "1");
    }

    $.ajax({
      type: "POST",
      data: fdata,
      contentType: false,
      processData: false,
      url: "/inc/login_ok.php",
      success: function (data) {
        console.log(data);
        if (data == "use_ok" || data == "m_use_ok") {
          if (window.location.pathname == "/myinfo/index.php") {
            location.replace("/team/");
          } else {
            // location.reload();
            location.replace("/team/index.php");
          }
          return false;
        } else if (data == "ad_ok" || data == "m_ad_ok") {
          //location.replace("/admin/pay.php");
          //location.href = "/admin/member_list.php";
          if (
            window.location.pathname == "/myinfo/index.php" ||
            window.location.pathname == "/index.php"
          ) {
            location.replace("/team/");
          } else {
            location.reload();
          }
          return false;
        } else if (data == "use_deny") {
          alert("이메일 주소 및 비밀번호를 확인 해주세요.");
          return false;
        } else if (data == "notuser") {
          alert(
            "아이디 또는 비밀번호가 유효하지 않습니다.\n아이디와 비밀번호를 정확히 입력해 주세요."
          );
          return false;
        } else if (data == "use_check" || data == "m_use_check") {
          if (window.location.pathname == "/team/") {
            $(".tl_close").trigger("click");
            location.replace("/team/index.php");
            // first_login_time();
            // //$("#layer_work").show();
          } else {
            location.replace("/team/index.php");
          }
        } else {
          alert(
            "아이디 또는 비밀번호가 유효하지 않습니다.\n아이디와 비밀번호를 정확히 입력해 주세요."
          );
          return false;
        }
      },
    });
  });

  $(document).on("click", "#create_new_pw", function () {
    pw = $("#ori_passwd").val();
    if (!pw) {
      alert("비밀번호를 입력하세요");
      return false;
    }

    user_id = $("#user_email").val();
    var fdata = new FormData();
    fdata.append("mode", "new_password");
    fdata.append("pw", pw);
    fdata.append("user_id", user_id);
    $.ajax({
      type: "POST",
      data: fdata,
      contentType: false,
      processData: false,
      url: "/inc/login_ok.php",
      success: function (data) {
        console.log(data);
        var tdata = data.split("|");
        if (tdata[1] == "pempty") {
          alert("비밀번호 입력란에 공백을 지워주십시오.");
          return false;
        } else if (tdata[1] == "not_password") {
          alert("확인되지 않은 비밀번호 입니다.");
          return false;
        } else if (tdata[1] == "success") {
          $(".rew_layer_repass").html(tdata[0]);
        }
      },
    });
  });

  $(document).on("click", "#enter_new_pw", function () {
    new_pw = $("#new_passwd").val();
    if (!new_pw) {
      alert("새 비밀번호를 입력하세요");
      return false;
    }

    enter_pw = $("#enter_passwd").val();
    if (!enter_pw) {
      alert("비밀번호 확인을 입력하세요");
      return false;
    }

    if (new_pw != enter_pw) {
      alert("비밀번호 확인이 올바르지 않습니다. 다시 입력하십시오");
      return false;
    }

    user_id = $("#user_email").val();

    var fdata = new FormData();
    fdata.append("mode", "enter_password");
    fdata.append("pw", new_pw);
    fdata.append("user_id", user_id);
    $.ajax({
      type: "POST",
      data: fdata,
      contentType: false,
      processData: false,
      url: "/inc/login_ok.php",
      success: function (data) {
        console.log(data);
        var tdata = data.split("|");
        if (tdata[1] == "not_user") {
          alert("유효하지 않은 회원 정보 입니다.");
          return false;
        } else if (tdata[1] == "success") {
          alert("비밀번호 재설정에 성공 했습니다.");
          // $(".rew_layer_repass").html(tdata[0]);
          // $(".rew_layer_repass").hide();
          location.reload();
          return false;
        }
      },
    });
  });

  $(document).on("click", "#ra_header_back", function () {
    location.href = document.referrer;
  });

  //출근하기 취소 버튼
  $(document).on("click", "#lw_off", function () {
    //$(this).closest("#layer_work").hide();
    //location.reload();
    if (window.location.pathname == "/myinfo/index.php") {
      location.replace("/team/");
    } else {
      if ($("#layer_work").is(":visible") == true) {
        $("#layer_work").hide();
      }
      //location.reload();
    }
    return false;
  });

  //출근하기 버튼
  $(document).on("click", "#lw_on", function () {
    console.log("출근하기");

    // alert(tp_work+"|"+tp_outcount);
    // return false;
    var fdata = new FormData();
    fdata.append("mode", "member_work_check");

    $.ajax({
      type: "POST",
      data: fdata,
      contentType: false,
      processData: false,
      url: "/inc/login_ok.php",
      success: function (data) {
        console.log(data);
        var tdata = data.split("|");
        if (tdata) {
          stack = tdata[0];
          work = tdata[1];
          outcount = tdata[2];
          chall = tdata[3];
          incount = tdata[4];
          result = tdata[5];
          if (stack == "penalty") {
            $(".layer_work").hide();
            $("#main_1_bt").removeClass("switch_ready");
            $("#main_1_bt").addClass("on");
            $(".onoff_01 em").text("근무중");
            if (work >= 3) {
              $("#penalty_work").show();
            }
            if (outcount >= 3) {
              $("#penalty_out").show();
            }
            if (chall >= 3) {
              $("#penalty_chall").show();
            }
            if (incount >= 3) {
              $("#penalty_in").show();
            }
            return false;
          } else {
            if (result == "work_check" || result == "today_check") {
              if (window.location.pathname == "/myinfo/index.php") {
                location.replace("/team/");
              } else {
                location.reload();
              }
              return false;
            }
          }
        }
      },
    });
  });

  //근무중 처리
  $(".onoff_01 .btn_switch").click(function () {
    $(".onoff_04 .btn_switch").removeClass("on");
    $(".onoff_04 .btn_switch").prev("em").removeClass("on");
    if ($(this).hasClass("on")) {
    } else if ($(this).hasClass("switch_ready")) {
      $(this).removeClass("switch_ready");
      $(this).addClass("on");
      $(this).prev("em").addClass("on");
      $(this).prev("em").html("근무중");
    } else {
      $(this).addClass("switch_ready");
    }
  });

  //회원맴버관리 맴버추가버튼
  $("#member_add_btn").click(function () {
    $(".rew_layer_member_add").show();
  });

  //회원맴버 인원 수 등록
  $("#member_list_add_submit").click(function () {
    if ($("#member_list_cnt").val()) {
      if (confirm("맴버 인원을 추가 하시겠습니까?")) {
        var fdata = new FormData();
        fdata.append("mode", "rewardy_member_add");
        fdata.append("paycnt", $("#member_list_cnt").val());

        $.ajax({
          type: "POST",
          data: fdata,
          contentType: false,
          processData: false,
          url: "/inc/process.php",
          success: function (data) {
            console.log(data);
            var tmp = data.split("|");
            if (tmp[0] == "ok") {
              var number = tmp[1];
              //location.href = "/admin/member.php";

              var form = document.createElement("form");
              var hiddenField = document.createElement("input");
              hiddenField.setAttribute("type", "hidden");
              hiddenField.setAttribute("name", "number"); // 받을 네이밍
              hiddenField.setAttribute("value", number); // 넘길 파라메터
              form.appendChild(hiddenField);
              form.setAttribute("method", "POST");
              form.setAttribute("action", "/admin/member_list_user_mail.php"); // URL
              document.body.appendChild(form);
              form.submit();
              return false;
            }
          },
        });
      }
    }
  });

  //맴버 인원수 등록 추가 플러스 버튼
  var pass = false;
  $("#member_list_plus").on({
    mouseup: function () {
      pass = false;
    },
    mousedown: function () {
      pass = true;
      count_member_list_ticket("1");
    },
  });

  $("#member_list_minus").on({
    mouseup: function () {
      pass = false;
    },
    mousedown: function () {
      pass = true;
      count_member_list_ticket("-1");
    },
  });

  function count_member_list_ticket(su) {
    var max_number = 30;
    if (pass) {
      if ($("#member_list_cnt").val()) {
        var input_count = parseInt($("#member_list_cnt").val());
      } else {
        input_count = 0;
      }

      if (su == "1") {
        //플러스
        if (input_count < max_number) {
          input_count = input_count + 1;
          $("#member_list_cnt").val(input_count);

          $("#member_list_minus").removeClass("count_limit");
          if (input_count == max_number) {
            $("#member_list_plus").addClass("count_limit");
          }
        } else {
          $("#member_list_plus").addClass("count_limit");
        }
      } else {
        //마이너스
        if (max_number > 0 && input_count > 1) {
          input_count = input_count - 1;
          $("#member_list_cnt").val(input_count);

          if (input_count == 1) {
            $("#member_list_minus").addClass("count_limit");
          } else {
            $("#member_list_plus").removeClass("count_limit");
          }
        }
      }

      setTimeout(function () {
        count_member_list_ticket(su);
      }, 400);
    }
  }

  $(".rew_member_inputs_sort .btn_sort_on").click(function () {
    $(".rew_member_inputs_sort").removeClass("on");
    $(this).closest(".rew_member_inputs_sort").addClass("on");
  });

  $(".rew_member_inputs_sort").mouseleave(function () {
    $(".rew_member_inputs_sort").removeClass("on");
  });

  $(".rew_member_inputs_sort ul li button").click(function () {
    $(".rew_member_inputs_sort").removeClass("on");
  });

  //로그아웃버튼
  $("#logout_btn").click(function () {
    //location.replace("/inc/logout.php");

    var fdata = new FormData();
    fdata.append("mode", "logout");
    $.ajax({
      type: "POST",
      data: fdata,
      contentType: false,
      processData: false,
      url: "/inc/logout.php",
      success: function (data) {
        if (data == "ok") {
          location.replace("/index.php");
          return false;
        }
      },
    });
  });

  //메일발송버튼
  $("#sendmail").click(function () {
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

    if (
      confirm("입력한 " + mail_cnt + "개의 초대 이메일을 발송하시겠습니까?")
    ) {
      fdata.append("mode", "sendmail");
      $.ajax({
        type: "POST",
        data: fdata,
        contentType: false,
        processData: false,
        url: "/inc/sendmail.php",
        success: function (data) {
          console.log(data);
          if (data == "ok") {
            alert("메일이 정상 발송되었습니다.");
            location.replace("/index.php");
          } else if (data == "fail") {
            alert("메일 발송이 되지 않았습니다.");
            return false;
          }
        },
      });
    }
  });

  //맴버정보 입력 메일주소 선택
  $(document).on("click", "button[id^='mail_addr']", function () {
    var val = $(this).val();

    //console.log(val);
  });

  $(".btn_sort_on ul li button")
    .eq(0)
    .click(function () {
      var val = $(this).val();
      console.log("kk : " + val);
    });

  //할일
  $(".tab_work").click(function () {
    $(".tc_index_tab button").removeClass("on");
    $(this).addClass("on");
    $(".tc_index_box").hide();

    if ($("textarea[name='wdate_contents']").val()) {
      $("textarea[name='wdate_contents']").val("");
      $(".tc_input").removeClass("now_focus");
    }

    if ($("textarea[name='req_contents']").val()) {
      "textarea[name='req_contents']".val("");
      $(".tc_input").removeClass("now_focus");
    }

    if ($("textarea[name='goal1']").val()) {
      $("textarea[name='goal1']").val("");
      $(".tc_input").removeClass("now_focus");
    }

    if ($("textarea[name='goal2']").val()) {
      $("textarea[name='goal2']").val("");
      $(".tc_input").removeClass("now_focus");
    }

    $("#tab_work").show();
  });

  //일정
  $(".tab_date").click(function () {
    $(".tc_index_tab button").removeClass("on");
    $(this).addClass("on");
    $(".tc_index_box").hide();

    if ($("textarea[name='contents']").val()) {
      $("textarea[name='contents']").val("");
      $(".tc_input").removeClass("now_focus");
    }

    if ($("textarea[name='wdate_contents']").val()) {
      $("textarea[name='wdate_contents']").val("");
      $(".tc_input").removeClass("now_focus");
    }

    if ($("textarea[name='req_contents']").val()) {
      $("textarea[name='req_contents']").val("");
      $(".tc_input").removeClass("now_focus");
    }

    if ($("textarea[name='goal1']").val()) {
      $("textarea[name='goal1']").val("");
      $(".tc_input").removeClass("now_focus");
    }

    if ($("textarea[name='goal2']").val()) {
      $("textarea[name='goal2']").val("");
      $(".tc_input").removeClass("now_focus");
    }

    $("#tab_date").show();
  });

  //업무요청 요청자 변경시
  $(document).on("click", ".tc_request strong", function () {
    if ($(".tc_request_user").is(":visible") == true) {
      $(".tc_request_user").hide();
      //$("input[name^='chkuseall']").prop("checked", false);
      //$("input[name^='requsechk']").prop("checked", false);
    }
    $(this).parent().parent().next(".tc_request_user").show();
  });

  //업무요청 요청자 수정
  $(document).on("click", ".tc_request_user button", function () {
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
        url: "/inc/process.php",
        success: function (data) {
          //console.log(data);
          if (data == "complete") {
            if ($(".tab_day").hasClass("on") == true) {
              $(".tab_day").trigger("click");
            } else if ($(".tab_week").hasClass("on") == true) {
              $(".tab_week").trigger("click");
            }
            return false;
          }
        },
      });
    }
  });

  //업무요청
  $(".tab_request").click(function () {
    $(".tc_index_tab button").removeClass("on");
    $(this).addClass("on");
    $(".tc_index_box").hide();

    if ($("textarea[name='contents']").val()) {
      $("textarea[name='contents']").val("");
      $(".tc_input").removeClass("now_focus");
    }

    if ($("textarea[name='wdate_contents']").val()) {
      $("textarea[name='wdate_contents']").val("");
      $(".tc_input").removeClass("now_focus");
    }

    if ($("textarea[name='goal1']").val()) {
      $("textarea[name='goal1']").val("");
      $(".tc_input").removeClass("now_focus");
    }

    if ($("textarea[name='goal2']").val()) {
      $("textarea[name='goal2']").val("");
      $(".tc_input").removeClass("now_focus");
    }

    $("#tab_request").show();
  });

  //목표
  $(".tab_goal").click(function () {
    $(".tc_index_tab button").removeClass("on");
    $(this).addClass("on");
    $(".tc_index_box").hide();

    if ($("textarea[name='contents']").val()) {
      $("textarea[name='contents']").val("");
      $(".tc_input").removeClass("now_focus");
    }

    if ($("textarea[name='wdate_contents']").val()) {
      $("textarea[name='wdate_contents']").val("");
      $(".tc_input").removeClass("now_focus");
    }

    if ($("textarea[name='req_contents']").val()) {
      $("textarea[name='req_contents']").val("");
      $(".tc_input").removeClass("now_focus");
    }

    $("#tab_goal").show();
  });

  //오늘할일 등록하기
  $("#write_btn").click(function () {
    var obj = $("textarea[name='contents']");

    //alert( "  ::: " + $("#contents").val() );
    //alert( " val " + CKEDITOR.instances.contents.getData() );

    var fdata = new FormData();
    //if (CKEDITOR.instances.contents.getData().length < 1){
    //  alert("ckckckckck");
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
      url: "/inc/process.php",
      success: function (data) {
        console.log(data);
        if (data == "complete") {
          obj.val("");
          $(".tc_input").removeClass("now_focus");
          if ($(".tab_day").hasClass("on") == true) {
            $(".tab_day").trigger("click");
          } else if ($(".tab_week").hasClass("on") == true) {
            $(".tab_week").trigger("click");
          }
          //location.reload();
          return false;
        }
      },
    });
  });

  //업무예약 등록하기
  $("#date_write").click(function () {
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
      url: "/inc/process.php",
      success: function (data) {
        console.log(data);
        if (data == "complete") {
          obj.val("");
          $(".tc_input").removeClass("now_focus");
          if ($(".tab_day").hasClass("on") == true) {
            $(".tab_day").trigger("click");
          } else if ($(".tab_week").hasClass("on") == true) {
            $(".tab_week").trigger("click");
          }
          //location.reload();
          return false;
        }
      },
    });
  });

  //목표 등록하기
  $("#goal_write").click(function () {
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
      url: "/inc/process.php",
      success: function (data) {
        console.log(data);
        if (data == "complete") {
          $("#goal1").val("");
          $("#goal2").val("");
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
      },
    });
  });

  //오늘업무 추가버튼
  $("#works_add_btn").click(function () {
    var html = '<div class="tc_box_area">';
    html += '<div class="tc_area">';
    html += '<textarea name="contents" id="contents" class="area_01">';
    html += "</textarea></div>";
    html += "</div>";
    $("#works_append").append(html);
  });

  //오늘일 업무추가
  $("#list_add_btn").click(function () {
    location.href = "/works/write.php";
    return false;
  });

  //오늘일 완료버튼
  $(document).on("click", "button[id^='list_complete']", function () {
    var id = $(this).attr("id");
    var fdata = new FormData();
    fdata.append("idx", $("#" + id).val());
    fdata.append("mode", "list_complete");

    $.ajax({
      type: "POST",
      data: fdata,
      contentType: false,
      processData: false,
      url: "/inc/process.php",
      success: function (data) {
        //console.log("data :::::: " + data);
        if (data == "complete") {
          $(".tab_day").trigger("click");
          return false;
        }
      },
    });
  });

  //오늘일 완료 변경버튼
  $(document).on("click", "button[id^='list_recomplete']", function () {
    var id = $(this).attr("id");
    var fdata = new FormData();
    fdata.append("idx", $("#" + id).val());
    fdata.append("mode", "list_recomplete");

    $.ajax({
      type: "POST",
      data: fdata,
      contentType: false,
      processData: false,
      url: "/inc/process.php",
      success: function (data) {
        console.log(data);
        if (data == "complete") {
          //location.reload();
          $(".tab_day").trigger("click");
          return false;
        }
      },
    });
  });

  //오늘일 내일로미루기 버튼
  $(document).on("click", "button[id^='list_yesterday']", function () {
    var id = $(this).attr("id");
    var fdata = new FormData();
    fdata.append("idx", $("#" + id).val());
    fdata.append("mode", "list_yesterday");

    $.ajax({
      type: "POST",
      data: fdata,
      contentType: false,
      processData: false,
      url: "/inc/process.php",
      success: function (data) {
        console.log(data);
        if (data == "complete") {
          //location.reload();
          $(".tab_day").trigger("click");
          return false;
        }
      },
    });
  });

  //오늘일 삭제버튼
  $(document).on("click", "button[id^='list_del']", function () {
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
        url: "/inc/process.php",
        success: function (data) {
          console.log(data);
          if (data == "complete") {
            //location.reload();
            $(".tab_day").trigger("click");
            return false;
          }
        },
      });
    }
  });

  //오늘할일 버튼
  $("#works_list").click(function () {
    if (GetCookie("user_id") != null) {
      location.href = "/works/list.php";
      return false;
    } else {
      //$(this).attr("id");
      $("#login_btn").trigger("click");
    }
  });

  //일일버튼
  $(".tc_tab .tab_day").click(function () {
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
      url: "/works/list_day.php",
      success: function (data) {
        console.log(data);
        $(".tc_index_list").html(data);
        if (data == "complete") {
          //    alert("입력한 내용으로 업무요청 되었습니다.");
          //  location.reload();
          //  return false;
        }
      },
    });
  });

  //주간버튼
  $(".tc_tab .tab_week").click(function () {
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
      url: "/works/list_week.php",
      success: function (data) {
        console.log(data);
        $(".tc_index_list_week").html(data);
        if (data == "complete") {
          //    alert("입력한 내용으로 업무요청 되었습니다.");
          //  location.reload();
          //  return false;
        }
      },
    });
  });

  //한달버튼
  $(".tc_tab .tab_month").click(function () {
    /*    $(".tc_index_list").hide();
      $(".tc_index_list_week").hide();
      $(".tc_index_list_month").show();
      $(".tc_tab button").removeClass("on");
      $(this).addClass("on");
    */
  });

  //업무요청버튼
  $(".tab_request").click(function () {
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
  $(".tab_work").click(function () {
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
  $(".tab_date").click(function () {
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
  $(".tab_goal").click(function () {
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
  $(".tab_goal").click(function () {
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
  var $item = $(".tc_goal_select button").on("click", function () {
    var idx = $item.index(this);

    //일일목표
    if (idx == 0) {
      $(".tc_goal_select button").eq(0).addClass("on");
      $(".tc_goal_select button").eq(1).removeClass("on");
      $(".tc_goal_select button").eq(2).removeClass("on");

      //주간목표
    } else if (idx == 1) {
      $(".tc_goal_select button").eq(0).removeClass("on");
      $(".tc_goal_select button").eq(1).addClass("on");
      $(".tc_goal_select button").eq(2).removeClass("on");

      //성과목표
    } else if (idx == 2) {
      $(".tc_goal_select button").eq(0).removeClass("on");
      $(".tc_goal_select button").eq(1).removeClass("on");
      $(".tc_goal_select button").eq(2).addClass("on");
    }
  });

  //나의업무
  $(".tc_tab_slc_in").click(function () {
    $(".tc_tab_slc ul").show();
  });

  $(".tc_tab_slc ul button").click(function () {
    var slc_this = $(this);
    $(".tc_tab_slc_in button span").text(slc_this.text());
    $(".tc_tab_slc ul").hide();
  });

  //업무요청 닫기
  $(document).on("click", ".tl_close, .tl_deam", function () {
    $(".tab_request, .tab_goal, .tab_date").removeClass("on");

    $(".tc_goal_select button").eq(0).addClass("on");
    $(".tc_goal_select button").eq(1).removeClass("on");
    $(".tc_goal_select button").eq(2).removeClass("on");

    if ($("#goal1").val()) {
      $("#goal1").val("");
    }

    if ($("#goal2").val()) {
      $("#goal2").val("");
    }

    if ($("#goal3").val()) {
      $("#goal3").val("");
    }

    $(".t_layer, .t_layer_req, .t_layer_goal, .t_layer_date").hide();
    $(".tab_work").addClass("on");

    $(".tc_box_08_in")
      .find(":input")
      .each(function () {
        //$(".tc_area").each(function() {
        $(this).val("");
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

    //console.log(999);

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
  $(document).on(
    "click",
    "input[id='chkall'],input[id^='chkuseall']",
    function () {
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
    }
  );

  //체크박스 선택시 체크박스와 체크on 박스가 갯수가 다를경우 전체선택해제
  $(document).on(
    "click",
    "input[name='chk'],input[id^='requsechk']",
    function () {
      var chk_cnt = $("input[name='chk']").size();
      var chk_true = $("input:checkbox[name=chk]:checked").length;

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
          var chk_req_true = $(
            "input:checkbox[name=" + name + "]:checked"
          ).length;

          if ($("input[name='chkuseall" + no + "']").is(":checked")) {
            if (chk_req_cnt != chk_req_true) {
              $("input[name='chkuseall" + no + "']").prop("checked", false);
            }
          }
        }
      }
    }
  );

  //업무요청하기
  $("#req_write").click(function () {
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

    if (!$("input:checkbox[name=chk]:checked").length) {
      alert("업무를 요청할 대상을 선택해 주세요.");
      return false;
    }

    fdata.append("req_date", $("#date_03").val());
    fdata.append("mode", "req_write");

    $.ajax({
      type: "POST",
      data: fdata,
      contentType: false,
      processData: false,
      url: "/inc/process.php",
      success: function (data) {
        //console.log(data);
        if (data == "complete") {
          obj.val("");
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
      },
    });
  });

  //.label_tit
  //#contents
  //$(document).on("click", "#chall_sdate,#chall_edate", function() {

  //챌린지 날짜 아이콘
  $(".tc_date_calendar").click(function () {
    $("#date1").focus();
  });

  //챌린지 날짜입력란 클릭
  $("#date1,#date2,#date_02,#date_03,#date_04").click(function () {
    $(this).focus();
  });

  $(document).on("click", "input[id='date1']", function () {
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
    //$(this).attr("autocomplete", "off");

    if ($(".tc_request_user").is(":visible") == true) {
      $(".tc_request_user").hide();
    }

    $(this).datepicker({
      dateFormat: "yyyy-mm-dd",
      onSelect: function (date) {
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
            url: "/inc/process.php",
            success: function (data) {
              console.log(data);
              if (data == "complete") {
                if ($(".tab_day").hasClass("on") == true) {
                  $(".tab_day").trigger("click");
                } else if ($(".tab_week").hasClass("on") == true) {
                  $(".tab_week").trigger("click");
                }
                return false;
              }
            },
          });
        }
      },
    });
  });

  //챌린지 날짜 선택 포커스아웃
  $("#date1").focusout(function () {
    $("#date2").focus();
  });

  //날짜 선택후
  $("#date1,#date2,#date_02,#date_03,#date_04").keyup(function () {
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
  $(document).on("click", "span[id^='contents1']", function () {
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
  $(document).on("click", "span[id^='contents2']", function () {
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

  $(document).on("click", "span[id^='contents2']", function () {
    var id = $(this).attr("id");
    var no = id.replace("contents2_", "");
    if (no) {
      var obj = $("div[id^=edit_content2_" + no + "]");
      obj.show();
    }
  });

  //수정닫기
  $(document).click(function (e) {
    //div[id^=edit_content1],
    if (
      !$(e.target).is(
        'textarea[name^=contents1],textarea[name^=contents2],span[id^="contents1"],span[id^="contents2"]'
      )
    ) {
      obj = $("div[id^=edit_content]");
      if (obj.is(":visible") == true) {
        obj.hide();
      }
    }

    /*if (!$(e.target).is("input[id=input_thema_title]")) {
        obj = $("div[id=thema_title_edit]");
        //console.log(obj.is(":visible"));
        if (obj.is(":visible") == true) {
          //    obj.hide();
        }
      }*/

    if (!$(e.target).is("div[class^=tc_request_user_in]")) {
      //    console.log(999);
    }

    if (!$(e.target).is("div[id^=req_user]")) {
      //console.log(" >> " + $(".tc_request_user").is(":visible"));
      /*   obj = $(".tc_request_user");
        if (obj.is(":visible") == true) {
          obj.hide();
        }
        */
    }

    if (!$(e.target).is("strong[id^=workdate_]")) {
      //console.log($(this).attr("id"));
      //$("#workdate_").html("");
    }
  });

  //수정하기버튼
  $(document).on("click", "#edit", function () {
    //$(document).on("click", $("button[id^='edit']"), function(e) {
    var val = $(this).val();
    var fdata = new FormData();
    fdata.append("mode", "edit");
    fdata.append("idx", val);

    if ($("textarea[name^=contents1_" + val + "]").val()) {
      fdata.append(
        "contents1",
        $("textarea[name^=contents1_" + val + "]").val()
      );
    }

    if ($("textarea[name^=contents2_" + val + "]").val()) {
      fdata.append(
        "contents2",
        $("textarea[name^=contents2_" + val + "]").val()
      );
    }

    $.ajax({
      type: "POST",
      data: fdata,
      contentType: false,
      processData: false,
      url: "/inc/process.php",
      success: function (data) {
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
      },
    });
  });

  //내용클릭 했을때 수정하기로 전환
  $(document).on("click", $("div[id^=tc_area]"), function (e) {
    if ($("div[id^=tc_area]").is(":visible") == true) {
      //console.log(" :::: " + $('.tc_modify').css('display'));
      if ($(".tc_modify").css("display") == "none") {
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
  $(document).on("click", ".tc_modify", function (e) {
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

  $(document).on("click", ".todaywork_wrap", function (e) {
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
    $("body").on("click", ".todaywork_wrap", function () {
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
  $("#chk_date").change(function () {
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
  $("#chk_t").change(function () {
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
  $("#req_date,#date_sdate,#date1,#date2,#date_02,#date_03,#date_04").keyup(
    function () {
      if (this.value.length > 10) {
        this.value = this.value.substr(0, 10);
      }
      var val = this.value.replace(/\D/g, "");
      var original = this.value.replace(/\D/g, "").length;
      var conversion = "";
      for (i = 0; i < 2; i++) {
        if (val.length > 4 && i === 0) {
          conversion += val.substr(0, 4) + "-";
          val = val.substr(4);
        } else if (original > 6 && val.length > 2 && i === 1) {
          conversion += val.substr(0, 2) + "-";
          val = val.substr(2);
        }
      }
      conversion += val;
      this.value = conversion;
    }
  );

  //시간정하기 시간입력
  $("#req_stime,#req_etime,#date_stime").keyup(function (e) {
    var val = $(this).val();
    val = val.replace(/[^0-9\s]/g, "");
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
  $(".btn_yesterday").click(function () {
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
  $(".btn_tomorrow").click(function () {
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
  $(".tab_my").click(function () {
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
      success: function (data) {
        console.log(data);
        $(get_html).html(data);
        if (data == "complete") {
          //    alert("입력한 내용으로 업무요청 되었습니다.");
          //  location.reload();
          //  return false;
        }
      },
    });
  });

  //팀별업무
  $(".tab_team").click(function () {
    var form = document.createElement("form");
    var hiddenField = document.createElement("input");
    hiddenField.setAttribute("type", "hidden");
    hiddenField.setAttribute("name", "type"); // 받을 네이밍
    hiddenField.setAttribute("value", "team_works"); // 넘길 파라메터
    form.appendChild(hiddenField);

    if ($("#works_today").val()) {
      var wdate = chage_getdate($("#works_today").val());
      var hiddenField2 = document.createElement("input");
      hiddenField2.setAttribute("type", "hidden");
      hiddenField2.setAttribute("name", "wdate"); // 받을 네이밍
      hiddenField2.setAttribute("value", wdate); // 넘길 파라메터
      form.appendChild(hiddenField2);
    }

    form.setAttribute("method", "get");
    form.setAttribute("action", "/works/list.php"); // URL
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
      success: function (data) {
        console.log(data);

        $(get_html).html(data);

        if (data == "complete") {
          //    alert("입력한 내용으로 업무요청 되었습니다.");
          //  location.reload();
          //  return false;
        }
      },
    });
  });

  //전체업무
  $(".tab_all").click(function () {
    var form = document.createElement("form");
    var hiddenField = document.createElement("input");
    hiddenField.setAttribute("type", "hidden");
    hiddenField.setAttribute("name", "type"); // 받을 네이밍
    hiddenField.setAttribute("value", "all_works"); // 넘길 파라메터
    form.appendChild(hiddenField);

    if ($("#works_today").val()) {
      var wdate = chage_getdate($("#works_today").val());
      var hiddenField2 = document.createElement("input");
      hiddenField2.setAttribute("type", "hidden");
      hiddenField2.setAttribute("name", "wdate"); // 받을 네이밍
      hiddenField2.setAttribute("value", wdate); // 넘길 파라메터
      form.appendChild(hiddenField2);
    }

    form.setAttribute("method", "get");
    form.setAttribute("action", "/works/list.php"); // URL
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
      success: function (data) {
        console.log(data);

        $(get_html).html(data);

        if (data == "complete") {
          //    alert("입력한 내용으로 업무요청 되었습니다.");
          //  location.reload();
          //  return false;
        }
      },
    });
  });

  //보상하기버튼
  $("#reward_btn").click(function () {
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
      url: "/inc/process.php",
      success: function (data) {
        console.log(data);

        if (data == "complete") {
          alert("보상이 지급 되었습니다.");
          location.href = "/coins/list.php";
          return false;
        } else if (data == "coin_min") {
          alert(
            "보유한 코인이 부족하여 보상지급 안됩니다.\n\n보상 코인을 조정하여 주세요."
          );
          return false;
        }
      },
    });
  });

  //클릭시 일부 내용 삭제처리
  $(document).on("click", ".note-editable", function () {
    var chall_contents = $("#chall_contents").summernote("code");
    exp =
      /챌린지 내용을 작성해주세요.|챌린지 참여방법을 작성해주세요.|챌린지 유의사항을 입력해주세요/gi;
    res = chall_contents.match(exp);
    if (res) {
      chall_contents = chall_contents.replace(
        /챌린지 내용을 작성해주세요.|챌린지 참여방법을 작성해주세요.|챌린지 유의사항을 입력해주세요./g,
        ""
      );
      $("#chall_contents").summernote("code", chall_contents);
    }
  });

  //챌린지 카테고리 선택
  $(document).on("click", "button[id^='chall_cate']", function () {
    var id = $(this).attr("id");
    var no = $("#" + id).val();
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
  $("#chall_one").click(function () {
    $("#chall_one").addClass("on");
    $("#chall_day").removeClass("on");
    $("button[id^='chall_days']").removeClass("on");
  });

  //챌린지 기간내 선택 매일
  $("#chall_day").click(function () {
    $("#chall_day").addClass("on");
    $("#chall_one").removeClass("on");
    $("button[id^='chall_days']").removeClass("on");
    //$("button[id^='chall_days']").css("display","");
  });

  //챌린지 기간 내, 매일 요일 선택시
  $(document).on("click", "button[id^='chall_days']", function () {
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
  $("#user_all").click(function () {
    $("#user_all").addClass("on");
    $("#user_one").removeClass("on");
    $("#add_user_list").css("display", "none");
    $("input[name^=chk]").prop("checked", false);
  });

  //참여자선택 일부
  $("#user_one").click(function () {
    $("#user_one").addClass("on");
    $("#user_all").removeClass("on");
    $("#add_user_list").css("display", "");
  });

  //챌린지 게시물 노출여부 - 노출
  $("#list_view").click(function () {
    $("#list_view").addClass("on");
    $("#list_hidden").removeClass("on");
  });

  //챌린지 게시물 노출여부 - 숨김
  $("#list_hidden").click(function () {
    $("#list_hidden").addClass("on");
    $("#list_view").removeClass("on");
  });

  //var file_cnt = 1;
  //$(document).on("click", ".tc_chall_coin em:eq(1)", function() {
  //$("#div_file_add").on("click", function(){
  //$(document).on("click", ".tc_chall_coin em:eq(1)", function() {

  //$(document).on("click", "em[id='div_file_add']", function() {
  $(document).on("click", "li[id^='div_file']", function () {
    var file_cnt = $("li[id^='div_file']").length;
    if (file_cnt < 3) {
      var add_input =
        '<li id="div_file' +
        file_cnt +
        '" onclick="file_del(' +
        file_cnt +
        ')"><div class="tc_chall_coin"><em id="div_file_del">첨부파일 <Br>- </em><div class="tc_box_btns_upload"><input type="file" id="files" name="files"/></div></div></li>';
      $("#div_file").append(add_input);
      //file_cnt++;
    }
  });

  //챌린지 파일첨부
  $("#file_add_bt").click(addFileForm);

  //챌린지 파일첨부 삭제
  $(document).on("click", "#file_del", function (event) {
    //  $(this).parent().remove();
  });

  //미리보기
  $("input[id^='img_file']").change(handleFileSelect);

  $("div[id^='preview']").click(function (e) {
    var id = $(this).attr("id");
    var no = id.replace("preview", "");
    $("#img_file" + no).trigger("click");
  });

  //미리보기 이미지삭제
  $("em[id^='img_del']").click(function () {
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

  //챌린지작성, 챌린지목록, 출근하기, 출근하기취소버튼
  //.rew_mypage_tab_02
  $(
    "#challenges_w,#challenges_l,#mycoin,#rew_mypage_coin_chall,#attend_over,#lw_off,#lw_on,.rew_mypage_tab_02 ul li:eq(1)"
  ).on("mouseenter", function () {
    $(this).css("cursor", "pointer");
  });

  //챌린지작성
  $("#challenges_w").click(function () {
    location.href = "/challenge/write.php";
  });

  //챌린지목록
  $("#challenges_l").click(function () {
    location.href = "/challenge/list.php";
  });

  //챌린지파일첨부
  $("input[id='file_01'],input[id='file_02'],input[id='file_03']").change(
    function () {
      var id = $(this).attr("id");
      var no = id.replace("file_", "");
      //val = $(this).val();
      var fileobj = $("input[id='file_" + no + "']").val();
      var fileheader = fileobj.lastIndexOf("\\");
      var filemiddle = fileobj.lastIndexOf(".");
      var fileend = fileobj.length;
      var filename = fileobj.substring(fileheader + 1, filemiddle);
      var extname = fileobj.substring(filemiddle + 1, fileend);
      var file_name = filename + "." + extname;

      $("#file_desc_" + no + "").html(
        '<div class="file_desc"><span>' +
          file_name +
          '</span><button id="file_del_' +
          no +
          '">삭제</button></div>'
      );

      //$("#delfile1").html("<button id='file_del1'> X </button>");
    }
  );

  //챌린지참여, 파일첨부형
  $(document).on("change", "input[id='ch_file_01']", function (event) {
    var id = $(this).attr("id");
    var no = id.replace("ch_file_", "");

    var fileobj = $("input[id='ch_file_" + no + "']").val();
    var fileheader = fileobj.lastIndexOf("\\");
    var filemiddle = fileobj.lastIndexOf(".");
    var fileend = fileobj.length;
    var filename = fileobj.substring(fileheader + 1, filemiddle);
    var extname = fileobj.substring(filemiddle + 1, fileend);
    var file_name = filename + "." + extname;

    //console.log(no);
    //console.log('<div class="file_desc"><span>' + file_name + '</span><button id="ch_file_del_' + no + '">삭제</button></div>');
    $("#ch_file_desc_" + no + "").html(
      '<div class="file_desc"><span>' +
        file_name +
        '</span><button id="ch_file_del_' +
        no +
        '">삭제</button></div>'
    );
    $(".file_desc").show();

    if ($(".btns_cha_join").hasClass("on") == false) {
      $(".btns_cha_join").addClass("on");
    }
  });

  //챌린지참여, 파일첨부 삭제버튼
  $(document).on("click", "button[id^='ch_file_del_']", function (event) {
    var id = $(this).attr("id");
    var no = id.replace("ch_file_del_", "");
    $("#ch_file_desc_" + no).html("");
    $("#ch_file_" + no).val("");
  });

  $("input[id='file_04'],input[id='file_05'],input[id='file_06']").change(
    function () {
      var id = $(this).attr("id");
      var no = id.replace("preview", "");
      $("#file_" + no).trigger("click");
    }
  );

  $("input[id='file_04'],input[id='file_05'],input[id='file_06']").change(
    challenges_img_preview
  );

  //챌린지파일첨부삭제
  $(document).on("click", "button[id^='file_del_']", function (event) {
    var id = $(this).attr("id");
    var no = id.replace("file_del_", "");
    $("#file_desc_" + no).html("");
    $("#file_" + no).val("");
  });

  //챌린지임시저장
  $(".rew_cha_write_btn .btn_gray").click(function () {
    var fdata = new FormData();
    fdata.append("mode", "chall_save");
    fdata.append("temp_save", "1");

    if ($("#chall_idx").val()) {
      fdata.append("chall_idx", $("#chall_idx").val());
    }
    if ($("#write_type_01").is(":checked") == true) {
      fdata.append("write_type", "1");
    } else if ($("#write_type_02").is(":checked") == true) {
      fdata.append("write_type", "2");
    } else if ($("#write_type_03").is(":checked") == true) {
      fdata.append("write_type", "3");
    }

    if ($("#cate_title").val()) {
      fdata.append("cate", $("#cate_title").val());
    }

    if ($("#write_title").val()) {
      fdata.append("title", $("#write_title").val());
    }

    var chall_contents = $("#chall_contents").summernote("code");
    if (chall_contents) {
      fdata.append("contents", chall_contents);
    }

    if ($("input[id='file_01']").val()) {
      var file_name01 = $("input[id='file_01']").val();

      //파일1 확장자 체크
      if (uploadFile(file_name01) == "ok") {
        alert("지원하지않는 파일확장자입니다.");
        $(".btn_prev_step_01").trigger("click");
        return false;
      }

      //파일1 사이즈 체크
      if ($("input[id='file_01']")[0].files[0].size >= "52428800") {
        alert("파일용량이 50M를 초과합니다.\n 파일은 50M 미만으로 올려주세요.");
        $(".btn_prev_step_01").trigger("click");
        return false;
      }
      fdata.append("files[]", $("input[id='file_01']")[0].files[0]);
    }

    if ($("input[id='file_02']").val()) {
      var file_name02 = $("input[id='file_02']").val();

      //파일2 확장자 체크
      if (uploadFile(file_name02) == "ok") {
        alert("지원하지않는 파일확장자입니다.");
        $(".btn_prev_step_01").trigger("click");
        return false;
      }

      //파일2 사이즈 체크
      if ($("input[id='file_02']")[0].files[0].size >= "52428800") {
        alert("파일용량이 50M를 초과합니다.\n 파일은 50M 미만으로 올려주세요.");
        return false;
      }
      fdata.append("files[]", $("input[id='file_02']")[0].files[0]);
    }

    if ($("input[id='file_03']").val()) {
      var file_name03 = $("input[id='file_03']").val();

      //파일3 확장자 체크
      if (uploadFile(file_name03) == "ok") {
        alert("지원하지않는 파일확장자입니다.");
        $(".btn_prev_step_01").trigger("click");
        return false;
      }

      //파일3 사이즈 체크
      if ($("input[id='file_03']")[0].files[0].size >= "52428800") {
        alert("파일용량이 50M를 초과합니다.\n 파일은 50M 미만으로 올려주세요.");
        $(".btn_prev_step_01").trigger("click");
        return false;
      }
      fdata.append("files[]", $("input[id='file_03']")[0].files[0]);
    }

    if ($("input[id='file_04']").val()) {
      var file_name04 = $("input[id='file_04']").val();

      //파일4 확장자 체크
      if (img_uploadFile(file_name04) == "ok") {
        alert("지원하지않는 파일확장자입니다.");
        $(".btn_prev_step_01").trigger("click");
        return false;
      }

      //파일4 사이즈 체크
      if ($("input[id='file_04']")[0].files[0].size >= "52428800") {
        alert("파일용량이 50M를 초과합니다.\n 파일은 50M 미만으로 올려주세요.");
        $(".btn_prev_step_01").trigger("click");
        return false;
      }
      fdata.append("files_img[]", $("input[id='file_04']")[0].files[0]);
    }

    if ($("input[id='file_05']").val()) {
      var file_name05 = $("input[id='file_05']").val();

      //파일5 확장자 체크
      if (img_uploadFile(file_name05) == "ok") {
        alert("지원하지않는 파일확장자입니다.");
        $(".btn_prev_step_01").trigger("click");
        return false;
      }

      //파일5 사이즈 체크
      if ($("input[id='file_05']")[0].files[0].size >= "52428800") {
        alert("파일용량이 50M를 초과합니다.\n 파일은 50M 미만으로 올려주세요.");
        $(".btn_prev_step_01").trigger("click");
        return false;
      }
      fdata.append("files_img[]", $("input[id='file_05']")[0].files[0]);
    }

    if ($("input[id='file_06']").val()) {
      var file_name06 = $("input[id='file_06']").val();

      //파일6 확장자 체크
      if (img_uploadFile(file_name06) == "ok") {
        alert("지원하지않는 파일확장자입니다.");
        $(".btn_prev_step_01").trigger("click");
        return false;
      }

      //파일6 사이즈 체크
      if ($("input[id='file_06']")[0].files[0].size >= "52428800") {
        alert("파일용량이 50M를 초과합니다.\n 파일은 50M 미만으로 올려주세요.");
        $(".btn_prev_step_01").trigger("click");
        return false;
      }
      fdata.append("files_img[]", $("input[id='file_06']")[0].files[0]);
    }

    if ($("#sdate").val()) {
      fdata.append("sdate", $("#sdate").val());
    }

    if ($("#edate").val()) {
      fdata.append("edate", $("#edate").val());
    }

    if ($(".input_count").val()) {
      fdata.append("input_count", $(".input_count").val());
    }

    if ($("#ch_once").hasClass("btn_on") == true) {
      fdata.append("ch_once", "1");
    }

    if ($("#ch_daily").hasClass("btn_on") == true) {
      fdata.append("ch_daily", "1");
    }

    if ($("#ch_holiday").hasClass("btn_chk_on") == true) {
      fdata.append("ch_holiday", "1");
    }

    //참여자설정 - 전체
    if (
      $(".rew_cha_setting_user_area button").eq(0).hasClass("btn_on") == true
    ) {
      fdata.append("user", "all");
    } else if (
      $(".rew_cha_setting_user_area button").eq(1).hasClass("btn_on") == true
    ) {
      //챌린지설정 - 일부
      fdata.append("user", "sel");
      fdata.append("chall_user_chk", $("#chall_user_chk").val());
    }

    if ($(".input_coin").val()) {
      fdata.append("input_coin", $(".input_coin").val());
    }

    if ($("#not_coin_ico").hasClass("btn_chk_on") == true) {
      fdata.append("ch_not_coin", true);
    }

    $.ajax({
      type: "POST",
      data: fdata,
      contentType: false,
      processData: false,
      url: "/inc/challenges_process.php",
      success: function (data) {
        console.log(data);

        if (data) {
          tdata = data.split("|");
          if (tdata) {
            var result = tdata[0];
            var temp_idx = tdata[1];

            if (temp_idx) {
              $("#chall_idx").val(temp_idx);
            }

            if (result == "temp_complete") {
              alert("임시저장 되었습니다.");
              return false;
            }
          }
        }
      },
    });
  });

  //파일첨부1
  $("input[id='files1']").change(function () {
    var id = $(this).attr("id");
    var no = id.replace("files", "");

    $("#delfile1").html("<button id='file_del1'> X </button>");
  });

  //파일첨부2
  $("input[id='files2']").change(function () {
    var id = $(this).attr("id");
    var no = id.replace("files", "");

    $("#delfile2").html("<button id='file_del2'> X </button>");
  });

  //파일첨부3
  $("input[id='files3']").change(function () {
    var id = $(this).attr("id");
    var no = id.replace("files", "");

    $("#delfile3").html("<button id='file_del3'> X </button>");
  });

  //파일첨부삭제
  $(document).on("click", "button[id^='file_del']", function (event) {
    var id = $(this).attr("id");
    var no = id.replace("file_del", "");
    $("#files" + no).val("");
    $("#file_del" + no).css("display", "none");
  });

  //챌린지 만들기
  $(".tab_chall_01").click(function () {
    if (GetCookie("user_id") != null) {
      setCookie("chall_tpl", "", "1");
      location.href = "/challenge/write.php";
    } else {
      $(".t_layer").show();
    }
  });

  //챌린지뷰페이지
  //$(".rew_cha_list ul li a").click(function() {
  $(document).on("click", "#rew_cha_list ul li a", function (event) {
    var val = $(this).parent().val();
    var cate = $(".themasort_user.on").find("#chall_category").val(); // 카테고리 넘버
    // console.log(val);
    // return false;
    var temp_auth = $("#template_auth").val();

    if (val) {
      if (GetCookie("user_id") != null) {
        location.href =
          "/challenge/view.php?idx=" +
          val +
          "&cate=" +
          cate +
          "&temp_auth=" +
          temp_auth;
        // $("#view_link_"+val).trigger("click");
      } else {
        $(".t_layer").show();
      }
    }
  });

  $(document).on("click", "#rew_mypage_chall_ing ul li a", function (event) {
    var val = $(this).parent().val();
    if (val) {
      if (GetCookie("user_id") != null) {
        location.href = "/challenge/view.php?idx=" + val;
      } else {
        $(".t_layer").show();
      }
    }
  });

  //메인 리스트 선택
  $(".rew_cha_list_in ul li a").click(function () {
    //console.log($(this).parent().val());
  });

  //결과물 올리기
  $("input[name=file2]").on("change", function () {
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
      //  console.log(file);
      //  console.log(fileName);
      if (fileName) {
        //$("#chall_file_txt").text("결과물로 올린 파일명 : " + fileName);
      }
    });*/

  //챌린지 완료하기
  $("#challenges_complete").click(function () {
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
        url: "/inc/process.php",
        success: function (data) {
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
            location.href = "/challenge/list.php";
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
            alert(
              "해당 챌린지는 결과물이 없어 완료하실 수 없습니다.\n챌린지 결과물을 등록하시기 바랍니다."
            );
            return false;
          } else if (data == "not_files2") {
            alert(
              "파일이 업로드 되지 않아 챌린지를 완료 하실수 없습니다.\n첨부한 파일을 확인 하시기 바랍니다."
            );
            return false;
          }
        },
      });
    }
  });

  //챌린지 숨기기
  $(document).on("click", "button[id='view_hide']", function () {
    var fdata = new FormData();
    fdata.append("mode", "challenges_hide");
    fdata.append("chall_idx", $("#view_idx").val());

    if ($(this).hasClass("btn_ok") == false) {
      $(this).addClass("btn_ok");
      $(this).text("숨기기 ON");
      fdata.append("view_flag", "1");
    } else if ($(this).hasClass("btn_ok") == true) {
      $(this).removeClass("btn_ok");
      $(this).addClass("btn_gray");
      $(this).text("숨기기 OFF");
      fdata.append("view_flag", "0");
    }

    $.ajax({
      type: "POST",
      data: fdata,
      contentType: false,
      processData: false,
      url: "/inc/process.php",
      success: function (data) {
        console.log(data);
        if (data == "complete") {
          //location.href = '/challenge/list.php';
          return false;
        }
      },
    });
  });

  //챌린지 수정하기
  // $(document).on("click", "button[id^='chall_edit']", function () {
  //   var val = $(this).val();
  //   if (val) {
  //     location.href = "/challenge/edit.php?idx=" + val;
  //   }
  // });

  //챌린지 삭제하기
  $(document).on("click", "button[id='chall_del']", function () {
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
        url: "/inc/process.php",
        success: function (data) {
          console.log(data);
          if (data == "complete") {
            location.href = "/challenge/list.php";
            return false;
          }
        },
      });
    }
  });

  //댓글등록하기
  $(document).on("click", "button[id='comment_btn']", function () {
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
      url: "/inc/process.php",
      success: function (data) {
        console.log(data);
        if (data == "complete") {
          //location.href = '/challenge/list.php';
          location.reload();
          return false;
        }
      },
    });
  });

  //////////////////챌린지//////////////////
  if ($(location).attr("pathname").indexOf("/challenge/") == 0) {
    var page = parseInt($("#pageno").val());
    var page_count = parseInt($("#page_count").val());

    if ($("#rank").val() == "") {
      $("#rank").val("4");
    }

    //$(".rew_cha_more").hide();

    if ($(".rew_conts_in").length > 0) {
      //챌린지
      $(".rew_conts_list_in ul").sortable({
        axis: "y",
        opacity: 0.7,
        zIndex: 9999,
        //placeholder:"sort_empty",
        cursor: "move",
      });
      //$(".rew_conts_list_in ul").disableSelection();

      $(".rew_conts_list_in ul li button").click(function () {
        $(this).parent("li").toggleClass("on");
      });

      $(".rew_btn_icons_more").click(function () {
        $(".rew_icons").toggle();
      });

      //챌린지 좌측메뉴선택
      /*$(".rew_mypage_tab_04 a").click(function() {
          $(".rew_mypage_tab_04 li").removeClass("on");
          $(this).parent("li").addClass("on");
        });*/

      $(".rew_mypage_tab_04 a").click(function () {
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
      $(".rew_mypage_tab_04 a").click(function () {
        $(".rew_mypage_tab_04 ul li").each(function (index, item) {
          var no = $(this).index();
          /*if ($(".rew_mypage_tab_04 ul li").eq(no).hasClass("on") == true) {
              $(".rew_mypage_tab_04 ul li").eq(no).removeClass("on");
            } else {
              $(".rew_mypage_tab_04 ul li").eq(no).addClass("on");
            }*/
          //var a = $(this).parent("a").
        });

        $(this)
          .find("ul li")
          .each(function () {
            //    var no = $(this).index();
            //    console.log(no);
          });

        $(this)
          .parent("li")
          .each(function () {
            var no = $(this).index();
            var loop = $(".rew_mypage_tab_04 a").size();

            /*for (var i = 0; i < loop; i++) {
              if (i == no) {
                if ($(".rew_mypage_tab_04 ul li").eq(i).hasClass("on") == true) {
                  $(".rew_mypage_tab_04 ul li").eq(no).removeClass("on");
                } else {
                  $(".rew_mypage_tab_04 ul li").eq(no).addClass("on");
                }
              } else {
                $(".rew_mypage_tab_04 ul li").eq(i).removeClass("on");
              }
            }*/
          });
      });

      //챌린지 카테고리선택
      $(".rew_cha_tab .rew_cha_tab_in button").click(function () {
        $(".rew_cha_tab .rew_cha_tab_in ul li").removeClass("on");
        $(this).parent("li").addClass("on");
        $("#chall_cate").val();

        challenges_ajax_list();
      });

      setTimeout(function () {
        //$(".rew_box").addClass("on");
      }, 400);

      //challenges_ajax_list();

      //마우스 이동시
      $(".rew_conts_scroll_04").scroll(function () {
        var rct = $(".rew_cha_list_in").offset().top;

        if (rct < 155) {
          $(".rew_cha_tab").addClass("pos_fix");
        } else {
          $(".rew_cha_tab").removeClass("pos_fix");
        }
      });

      //챌린지 스크롤 위치
      $(".rew_conts_scroll_04").scroll(function () {
        var rct = $(".rew_cha_list_in").offset().top;
        //console.log(rct);
        if (rct < 216) {
          $(".rew_cha_list_func").addClass("pos_fix");
        } else {
          $(".rew_cha_list_func").removeClass("pos_fix");
        }
      });

      //챌린지 리스트
      $(".rew_cha_list_ul li").each(function () {
        var tis = $(this);
        var tindex = $(this).index();
        setTimeout(function () {
          tis.addClass("sli");
        }, 700 + tindex * 150);
      });

      //챌린지 뷰페이지 더보기(+more)
      $("#mix_more").click(function () {
        $(".mix_zone").trigger("click");
      });

      //챌린지 인증 메시지 뷰페이지 더보기(+more)
      $("#masage_more").click(function () {
        $(".masage_zone").trigger("click");
      });

      // 챌린지 파일 뷰페이지 더보기(+more)
      $("#file_more").click(function () {
        $(".list_area_in").trigger("click");
      });

      //챌린지 더보기
      //$(".rew_cha_more button").click(function() {
      $(document).on("click", "#rew_cha_more", function () {
        var fdata = new FormData();
        var cate = 0;
        var rank = $("#rank").val();
        var page = parseInt($("#pageno").val());
        var lastcnt;

        // if ($(".rew_cha_more").css("display") == "block") {
        //   $(".rew_cha_more").hide();
        // }

        var li_totcnt = parseInt($(".rew_cha_list_ul li").length);
        if (li_totcnt >= 0) {
          var li_cnt = li_totcnt;
        }

        //페이지
        if (page > 0) {
          page = page + 1;
        }

        //카테고리선택
        /*$(".rew_cha_tab .rew_cha_tab_in ul li").each(function(index, item) {
        var no = $(this).index();
        if ($(".rew_cha_tab .rew_cha_tab_in ul li").eq(no).hasClass("on") == true) {
          cate = no;
        }
      });*/

        //카테고리
        if ($(".rew_cha_tab_sort .rew_cha_tab_sort_in button:eq(0)").val()) {
          cate = $(".rew_cha_tab_sort .rew_cha_tab_sort_in button:eq(0)").val();
        }

        //챌린지 종류
        if ($("#tab_chall_03").hasClass("on") == true) {
          fdata.append("chall_type", "chmy");
        } else if ($("#tab_chall_04").hasClass("on") == true) {
          fdata.append("chall_type", "tempflag");
        }

        //챌린지 전체
        if ($(".rew_cha_chk_tab .chk_tab input:eq(0)").is(":checked") == true) {
          chk_tab = "all";
          fdata.append("chk_tab0", chk_tab);
        }

        //챌린지 도전가능한 챌린지
        if ($(".rew_cha_chk_tab .chk_tab input:eq(1)").is(":checked") == true) {
          chk_tab = "1";
          fdata.append("chk_tab1", chk_tab);
        }

        //챌린지 도전중인 챌린지
        if ($(".rew_cha_chk_tab .chk_tab input:eq(2)").is(":checked") == true) {
          chk_tab = "2";
          fdata.append("chk_tab2", chk_tab);
        }

        //챌린지 내가완료한 챌린지
        if ($(".rew_cha_chk_tab .chk_tab input:eq(3)").is(":checked") == true) {
          chk_tab = "3";
          fdata.append("chk_tab3", chk_tab);
        }

        //챌린지 종료한 챌린지
        if ($(".rew_cha_chk_tab .chk_tab input:eq(4)").is(":checked") == true) {
          chk_tab = "4";
          fdata.append("chk_tab4", chk_tab);
        }

        fdata.append("mode", "challenges_list");
        fdata.append("gp", page);
        fdata.append("cate", cate);
        fdata.append("rank", rank);

        $.ajax({
          type: "post",
          data: fdata,
          contentType: false,
          processData: false,
          url: "/inc/process.php",
          success: function (data) {
            //console.log(data);

            if (data) {
              tdata = data.split("|");
              if (tdata) {
                var html = tdata[1];
                var totcnt = tdata[2];
                var listcnt = tdata[3];
                var lastcnt = tdata[4];

                console.log(lastcnt);
                $("#pageno").attr("value", lastcnt);
                $("#page_count").val(parseInt(listcnt));

                $(".rew_cha_list_ul").append(html);

                //console.log(html);

                setTimeout(function () {
                  var offset = $(".offset0").position();
                  //if (offset) {
                  $(".rew_conts_scroll_04").animate(
                    { scrollTop: offset.top - 5 },
                    100
                  );
                  //}
                }, 100);

                setTimeout(function () {
                  $(".offset0").removeClass("offset0");
                }, 100);

                $(".rew_cha_list_ul li:not('.sli')").each(function (aa) {
                  var tis = $(this);
                  var tindex = $(this).index();
                  //alert(tindex);
                  setTimeout(function () {
                    tis.addClass("sli");
                  }, 300 + (aa + 1) * 50);
                });

                //더보기 버튼
                setTimeout(function () {
                  if (lastcnt >= $("#page_count").val()) {
                    $(".rew_cha_more").hide();
                  } else {
                    $(".rew_cha_more").show();
                  }
                }, 500);

                return false;
              }
            }
          },
        });
      });

      //챌린지 테마 더보기
      $(document).on("click", "#template_more", function () {
        var fdata = new FormData();
        var rank = $("#btn_sort_on").val();
        var lastcnt;

        var cate = $(".themasort_user .on").find("#chall_category").val();

        // if ($("#template_more").css("display") == "block") {
        //   $("#template_more").hide();
        // }

        var li_totcnt = parseInt($(".rew_cha_list_ul li").length);
        if (li_totcnt >= 0) {
          var li_cnt = li_totcnt;
        }

        var page = parseInt($("#pageno").val());

        //페이지
        if (page > 0) {
          page = page + 1;
        }

        //카테고리
        if ($("#btn_sort_on").val()) {
          //    sort = $("#btn_sort_on").val();
        }

        fdata.append("mode", "challenges_template_list_more");
        fdata.append("chall_type", $("#chall_type").val());
        fdata.append("gp", page);
        fdata.append("rank", rank);
        fdata.append("cate", cate);

        if ($("#thema_idx").val()) {
          fdata.append("thema_idx", $("#thema_idx").val());
        }

        //전체
        if ($("#cha_template_tab_all").is(":checked") == true) {
          fdata.append("viewchk_all", "1");
          //임시저장챌린지
        }

        if ($("#cha_chk_tab_save").is(":checked") == true) {
          fdata.append("viewchk_save", "1");
          //숨김챌린지
        }

        if ($("#cha_chk_tab_hide").is(":checked") == true) {
          fdata.append("viewchk_hide", "1");
        }

        if (
          $("#cha_template_tab_all").is(":checked") == false &&
          $("#cha_chk_tab_save").is(":checked") == false &&
          $("#cha_chk_tab_hide").is(":checked") == false
        ) {
          fdata.append("viewchk", "0");
        }

        console.log("템플릿 더보기");

        $.ajax({
          type: "post",
          data: fdata,
          contentType: false,
          processData: false,
          url: "/inc/template_process.php",
          success: function (data) {
            console.log(page);
            if (data) {
              tdata = data.split("|");
              if (tdata) {
                var html = tdata[0];
                var totcnt = tdata[1];
                var listcnt = tdata[2];
                var lastcnt = tdata[3];
                console.log(html);
                // $("#page_count").val(parseInt(listcnt));
                $("#template_list").append(html);
                $("#pageno").val(lastcnt);
                // console.log(html);

                setTimeout(function () {
                  var offset = $(".offset0").position();
                  $(".rew_conts_scroll_04").animate(
                    { scrollTop: offset.top - 5 },
                    700
                  );
                }, 400);

                setTimeout(function () {
                  $(".offset0").removeClass("offset0");
                }, 1100);

                $(".rew_cha_list_ul li:not('.sli')").each(function (aa) {
                  var tis = $(this);
                  var tindex = $(this).index();
                  //alert(tindex);
                  setTimeout(function () {
                    tis.addClass("sli");
                  }, 700 + (aa + 1) * 150);
                });

                // 더보기 버튼
                if (parseInt(lastcnt) >= parseInt($("#page_count").val())) {
                  console.log(lastcnt + "::" + $("#page_count").val());
                  $(".rew_cha_more").hide();
                } else {
                  $(".rew_cha_more").show();
                }
                // setTimeout(function () {
                //   if (lastcnt >= $("#page_count").val()) {
                //     console.log("alot");
                //     $(".rew_cha_more").hide();
                //   } else {
                //     $(".rew_cha_more").show();
                //   }
                // }, 500);
                return false;
              }
            }
          },
        });
      });

      //더보기 버튼
      /*setTimeout(function() {
          if (lastcnt >= $("#page_count").val()) {
            $(".rew_cha_more").hide();
          } else {
            $(".rew_cha_more").show();
          }
        }, 3000);
        //챌린지*/
    }

    //챌린지 더보기
    //챌린지 내가 만든챌린지
    $(".tab_chall_02, .tab_chall_03, .tab_chall_04").click(function () {
      //작성하기페이지
      if (
        pagename.indexOf("write") == 0 ||
        pagename.indexOf("template_write") == 0 ||
        pagename.indexOf("view") == 0 ||
        pagename.indexOf("edit") == 0
      ) {
        var no = $(this).index();
        var pgn = new Array("write", "template", "chcom", "chmy");
        //location.href = "/challenge/index.php?pgn=" + pgn[no];
        actsubmit("/challenge/index.php", "pgn", pgn[no]);
      }
      //else {
      //                challenges_ajax_list();
      //}
    });

    $("#tab_chall_02_template").click(function () {
      location.href = "/challenge/template.php";
      return false;
    });

    if (pagename == "index.php") {
      //    challenges_ajax_list();
    }

    //챌린지 카테고리 셀렉트박스 - 마우스 오버
    $(".rew_cha_tab_sort").hover(function () {
      $(".rew_cha_tab_sort").addClass("on");
    });

    //챌린지 카테고리 셀렉트박스 - 마우스 벗어날때
    $(".rew_cha_tab_sort").mouseleave(function () {
      $(".rew_cha_tab_sort").removeClass("on");
    });

    //챌린지 정렬순 셀렉트박스 - 마우스 오버
    $(".rew_cha_sort").hover(function () {
      $(".rew_cha_sort").addClass("on");
    });

    //챌린지 정렬순 셀렉트박스 - 마우스 벗어날때
    $(".rew_cha_sort").mouseleave(function () {
      $(".rew_cha_sort").removeClass("on");
    });

    //챌린지 정렬 셀렉트박스 - 마우스 오버
    //$(".rew_cha_sort").hover(function() {
    //$(document).on("mouseenter", "#template_sort ul li button", function() {
    //$("#template_sort").on('mouseenter', function() {

    $("#template_sort").on("mouseenter", function (e) {
      //var target = e.target || e.srcElement;
      //console.log(target);
      //$(".rew_cha_sort").addClass("on");
    });

    //챌린지 셀렉트박스 - 마우스오버
    $(document).on("mouseenter click", "#template_sort", function (e) {
      $(".rew_cha_sort").addClass("on");
    });

    //챌린지 셀렉트박스 - 마우스 벗어날때
    $(document).on("mouseleave click", "#template_sort", function (e) {
      $(".rew_cha_sort").removeClass("on");
    });

    //챌린지 정렬 셀렉트박스 - 마우스 벗어날때
    $(".rew_cha_sort").mouseleave(function () {
      $(".rew_cha_sort").removeClass("on");
    });

    //챌린지정렬
    $(".rew_cha_sort .rew_cha_sort_in button").click(function () {
      //$(".rew_cha_sort").addClass("on");
    });

    //챌린지카테고리 선택
    $(".rew_cha_tab_sort .rew_cha_tab_sort_in ul li button").click(function () {
      // if ($(".rew_cha_tab_sort").hasClass("on") == true) {
      var fdata = new FormData();
      var val = $(this).val();
      var page = parseInt($("#pageno").val());
      var cate;
      var page_count = parseInt($("#page_count").val());

      fdata.append("mode", "challenges_list");
      fdata.append("cate", val);
      fdata.append("gp", "1");

      //더보기 숨김
      $(".rew_cha_more").hide();

      if (val) {
        $(".rew_cha_tab_sort .rew_cha_tab_sort_in button:eq(0)").text(
          $(this).text()
        );
        $(".rew_cha_tab_sort .rew_cha_tab_sort_in button:eq(0)").val(val);
      }

      //챌린지 전체
      if ($(".rew_cha_chk_tab .chk_tab input:eq(0)").is(":checked") == true) {
        chk_tab = "all";
        fdata.append("chk_tab0", chk_tab);
      }

      //챌린지 도전가능한 챌린지
      if ($(".rew_cha_chk_tab .chk_tab input:eq(1)").is(":checked") == true) {
        chk_tab = "1";
        fdata.append("chk_tab1", chk_tab);
      }

      //챌린지 도전중인 챌린지
      if ($(".rew_cha_chk_tab .chk_tab input:eq(2)").is(":checked") == true) {
        chk_tab = "2";
        fdata.append("chk_tab2", chk_tab);
      }

      //챌린지 내가완료한 챌린지
      if ($(".rew_cha_chk_tab .chk_tab input:eq(3)").is(":checked") == true) {
        chk_tab = "3";
        fdata.append("chk_tab3", chk_tab);
      }

      //챌린지 종료한 챌린지
      if ($(".rew_cha_chk_tab .chk_tab input:eq(4)").is(":checked") == true) {
        chk_tab = "4";
        fdata.append("chk_tab4", chk_tab);
      }

      if ($("#tab_chall_02").hasClass("on") == true) {
        fdata.append("chall_type", "template");
      } else if ($("#tab_chall_03").hasClass("on") == true) {
        fdata.append("chall_type", "chcom");
      } else if ($("#tab_chall_04").hasClass("on") == true) {
        fdata.append("chall_type", "chmy");
      }

      fdata.append("rank", $("#rank").val());

      $.ajax({
        type: "post",
        data: fdata,
        contentType: false,
        processData: false,
        url: "/inc/process.php",
        success: function (data) {
          if (data) {
            tdata = data.split("|");
            if (tdata) {
              var html = tdata[1];
              var totcnt = tdata[2];
              var listcnt = tdata[3];
              var pageno = tdata[4];
              var sql = tdata[5];
              console.log(sql);

              //페이지수
              $("#pageno").val(parseInt(pageno));
              $("#page_count").val(listcnt);
              //카운터수
              $(".rew_cha_count strong").text(totcnt);

              var html = data;
              $(".rew_cha_list_ul").html(html);

              $(".rew_cha_tab_sort").removeClass("on");
              $(".rew_cha_list_ul li").each(function () {
                var tis = $(this);
                var tindex = $(this).index();
                setTimeout(function () {
                  tis.addClass("sli");
                }, 100 + tindex * 200);
              });

              setTimeout(function () {
                //  $(".rew_cha_sort").removeClass("on");
              }, 200);

              console.log("page :: " + page);
              console.log("pagecount :: " + listcnt);
              //더보기 버튼
              setTimeout(function () {
                if ($("#page_count").val()) {
                  if (pageno >= $("#page_count").val()) {
                    $(".rew_cha_more").hide();
                  } else {
                    $(".rew_cha_more").show();
                  }
                }
              }, 500);
            }
          }
        },
      });
      // }
    });

    //챌린지정렬
    $("#rew_cha_sort_list ul li button").click(function () {
      var val = $(this).val();
      if ($(".rew_cha_sort").hasClass("on") == true) {
        //if (val != 1) {
        //$(".rew_cha_sort").addClass("on");

        var category_sort = new Array();
        category_sort["1"] = "참여자 많은 순";
        category_sort["2"] = "기간 짧은 순";
        category_sort["3"] = "코인 높은 순";
        category_sort["4"] = "최신 순";

        var rank = $(this).val();

        if (rank) {
          console.log("rank " + rank);
          console.log("category_sort[rank] " + category_sort[rank]);

          if (category_sort[rank]) {
            //console.log(category_sort[rank]);
            $("#rank").val(rank);
            //$("#rank_title").text(category_sort[rank]);
            //$(".rew_cha_sort").removeClass("on");

            var fdata = new FormData();
            var rank = $(this).val();
            var page = parseInt($("#pageno").val());
            var page_count = parseInt($("#page_count").val());
            var cate;

            $("#pageno").attr("value", "1");

            fdata.append("mode", "challenges_list");

            //챌린지 전체
            if (
              $(".rew_cha_chk_tab .chk_tab input:eq(0)").is(":checked") == true
            ) {
              chk_tab = "all";
              fdata.append("chk_tab0", chk_tab);
            }

            //챌린지 도전가능한 챌린지
            if (
              $(".rew_cha_chk_tab .chk_tab input:eq(1)").is(":checked") == true
            ) {
              chk_tab = "1";
              fdata.append("chk_tab1", chk_tab);
            }

            //챌린지 도전중인 챌린지
            if (
              $(".rew_cha_chk_tab .chk_tab input:eq(2)").is(":checked") == true
            ) {
              chk_tab = "2";
              fdata.append("chk_tab2", chk_tab);
            }

            //챌린지 내가완료한 챌린지
            if (
              $(".rew_cha_chk_tab .chk_tab input:eq(3)").is(":checked") == true
            ) {
              chk_tab = "3";
              fdata.append("chk_tab3", chk_tab);
            }

            //챌린지 종료한 챌린지
            if (
              $(".rew_cha_chk_tab .chk_tab input:eq(4)").is(":checked") == true
            ) {
              chk_tab = "4";
              fdata.append("chk_tab4", chk_tab);
            }

            //챌린지별로 구분
            if ($("#tab_chall_02").hasClass("on") == true) {
              fdata.append("chall_type", "template");
            } else if ($("#tab_chall_03").hasClass("on") == true) {
              fdata.append("chall_type", "chcom");
            } else if ($("#tab_chall_04").hasClass("on") == true) {
              fdata.append("chall_type", "chmy");
            }

            fdata.append("gp", "1");
            fdata.append("rank", rank);

            //카테고리선택
            /*$(".rew_cha_tab .rew_cha_tab_in ul li").each(function(index, item) {
                    var no = $(this).index();
                    if ($(".rew_cha_tab .rew_cha_tab_in ul li").eq(no).hasClass("on") == true) {
                      cate = no;
                    }
                  });*/

            //console.log("rank :: " + rank);

            //카테고리
            if (
              $(".rew_cha_tab_sort .rew_cha_tab_sort_in button:eq(0)").val()
            ) {
              fdata.append(
                "cate",
                $(".rew_cha_tab_sort .rew_cha_tab_sort_in button:eq(0)").val()
              );
            }

            $.ajax({
              type: "post",
              data: fdata,
              contentType: false,
              processData: false,
              url: "/inc/process.php",
              success: function (data) {
                // console.log(data);
                if (data) {
                  tdata = data.split("|");
                  if (tdata) {
                    var html = tdata[1];
                    var totcnt = tdata[2];
                    var listcnt = tdata[3];

                    //페이지수
                    var html = data;
                    $(".rew_cha_list_ul").html(html);
                    $("#page_count").val(listcnt);
                    if (rank == 1) {
                      $(".rew_cha_sort .rew_cha_sort_in button:eq(0)").text(
                        "참여자 많은 순"
                      );
                    } else if (rank == 2) {
                      $(".rew_cha_sort .rew_cha_sort_in button:eq(0)").text(
                        "기간 짧은 순"
                      );
                    } else if (rank == 3) {
                      $(".rew_cha_sort .rew_cha_sort_in button:eq(0)").text(
                        "코인 높은 순"
                      );
                    } else if (rank == 4) {
                      $(".rew_cha_sort .rew_cha_sort_in button:eq(0)").text(
                        "최신 순"
                      );
                    }
                    $(".rew_cha_sort .rew_cha_sort_in button:eq(0)").val(rank);
                    $(".rew_cha_sort").removeClass("on");

                    $(".rew_cha_list_ul li").each(function () {
                      var tis = $(this);
                      var tindex = $(this).index();
                      setTimeout(function () {
                        tis.addClass("sli");
                      }, 100 + tindex * 200);
                    });

                    setTimeout(function () {
                      //  $(".rew_cha_sort").removeClass("on");
                    }, 200);

                    //더보기 버튼
                    setTimeout(function () {
                      if (page > $("#page_count").val()) {
                        $(".rew_cha_more").hide();
                      } else {
                        $(".rew_cha_more").show();
                      }
                    }, 500);
                  }
                }
              },
            });
          }
        }
        //}
      } else {
        console.log("AAAAA");
      }
    });

    //검색버튼
    $("#input_search_btn").click(function () {
      var input_search = $("#input_search").val();
      if (!input_search) {
        alert("챌린지명을 입력해주세요.");
        $("#input_search").focus();
        return false;
      }

      var fdata = new FormData();
      //카테고리
      if ($(".rew_cha_tab_sort .rew_cha_tab_sort_in button:eq(0)").val()) {
        fdata.append(
          "cate",
          $(".rew_cha_tab_sort .rew_cha_tab_sort_in button:eq(0)").val()
        );
      }

      //정렬
      if ($(".rew_cha_sort .rew_cha_sort_in button:eq(0)").val()) {
        fdata.append(
          "rank",
          $(".rew_cha_sort .rew_cha_sort_in button:eq(0)").val()
        );
      }

      if ($("#tab_chall_02").hasClass("on") == true) {
        fdata.append("chall_type", "template");
      }

      //챌린지 전체
      if ($(".rew_cha_chk_tab .chk_tab input:eq(0)").is(":checked") == true) {
        chk_tab = "all";
        fdata.append("chk_tab0", chk_tab);
      }

      //챌린지 도전가능한 챌린지
      if ($(".rew_cha_chk_tab .chk_tab input:eq(1)").is(":checked") == true) {
        chk_tab = "1";
        fdata.append("chk_tab1", chk_tab);
      }

      //챌린지 도전중인 챌린지
      if ($(".rew_cha_chk_tab .chk_tab input:eq(2)").is(":checked") == true) {
        chk_tab = "2";
        fdata.append("chk_tab2", chk_tab);
      }

      //챌린지 내가완료한 챌린지
      if ($(".rew_cha_chk_tab .chk_tab input:eq(3)").is(":checked") == true) {
        chk_tab = "3";
        fdata.append("chk_tab3", chk_tab);
      }

      //챌린지 종료한 챌린지
      if ($(".rew_cha_chk_tab .chk_tab input:eq(4)").is(":checked") == true) {
        chk_tab = "4";
        fdata.append("chk_tab4", chk_tab);
      }

      fdata.append("mode", "challenges_list");
      fdata.append("search", input_search);

      $.ajax({
        type: "post",
        data: fdata,
        contentType: false,
        processData: false,
        url: "/inc/process.php",
        success: function (data) {
          console.log(data);
          if (data) {
            tdata = data.split("|");
            if (tdata) {
              var html = tdata[1];
              var totcnt = tdata[2];
              var listcnt = tdata[3];

              //페이지수
              $("#page_count").val(parseInt(listcnt));

              //카운터수
              $(".rew_cha_count strong").text(totcnt);

              var html = data;
              $(".rew_cha_list_ul").html(html);

              $(".rew_cha_list_ul li").each(function () {
                var tis = $(this);
                var tindex = $(this).index();
                setTimeout(function () {
                  tis.addClass("sli");
                }, 100 + tindex * 200);
              });

              setTimeout(function () {
                //  $(".rew_cha_sort").removeClass("on");
              }, 200);

              //더보기 버튼
              setTimeout(function () {
                if (page >= $("#page_count").val()) {
                  $(".rew_cha_more").hide();
                } else {
                  $(".rew_cha_more").show();
                }
              }, 3000);
            }
          }
        },
      });
    });

    //테마 검색
    $("#input_thema_search_btn").click(function () {
      var input_search = $("#input_thema_search").val();
      if (!input_search) {
        alert("테마명을 입력해주세요.");
        $("#input_thema_search").focus();
        return false;
      }

      var fdata = new FormData();
      fdata.append("mode", "challenges_thema_list");
      fdata.append("search", input_search);

      $.ajax({
        type: "post",
        data: fdata,
        contentType: false,
        processData: false,
        url: "/inc/process.php",
        success: function (data) {
          console.log(data);
          if (data) {
            tdata = data.split("|");
            if (tdata) {
              var html = tdata[0];
              var totcnt = tdata[1];
              $("#thema_list_add").html(html);
              $("#thema_list_cnt").text("전체 " + totcnt + "개");
            }
          }
        },
      });
    });

    // 검색창 엔터키 처리
    $(document).on("keydown", "#input_search_thema", function (e) {
      console.log(e.keyCode);
      var input_val = $("#input_search_thema").val();

      if (e.keyCode == 13) {
        console.log("keypress");
        $("#input_search_thema_btn").trigger("click");
        return false;
      }
    });

    //챌린지 테마 리스트 검색 버튼
    $(document).on("click", "#input_search_thema_btn", function () {
      var input_search = $("#input_search_thema").val();
      if (!input_search) {
        alert("키워드명을 입력해주세요.");
        $("#input_search_thema").focus();
        return false;
      }

      var fdata = new FormData();
      fdata.append("mode", "challenges_template_list");
      fdata.append("search", input_search);

      if ($("#thema_idx").val()) {
        fdata.append("thema_idx", $("#thema_idx").val());
      }

      if ($("#template_auth").val()) {
        fdata.append("temp_auth", $("#template_auth").val());
        $("#template_auth").val("1");
      }

      if ($("#thema_zzim").val()) {
        fdata.append("zzim", 1);
      }

      $.ajax({
        type: "post",
        data: fdata,
        contentType: false,
        processData: false,
        url: "/inc/template_process.php",
        success: function (data) {
          if (data) {
            tdata = data.split("|");
            if (tdata) {
              var html = tdata[0];
              var totcnt = tdata[1];
              var listcnt = tdata[2];
              console.log(html);
              $("#page_count").val(parseInt(listcnt));
              $(".rew_cha_count strong").text(totcnt);

              //$("#template_list").html("");
              //$("#template_list").append(html);
              $("#rew_conts_in").html("");
              $("#rew_conts_in").html(html);

              $(".rew_cha_list_ul li:not('.sli')").each(function (aa) {
                var tis = $(this);
                var tindex = $(this).index();
                //alert(tindex);
                setTimeout(function () {
                  tis.addClass("sli");
                }, 100 + tindex * 200);
              });

              //더보기 버튼
              setTimeout(function () {
                if (listcnt >= $("#page_count").val()) {
                  $(".rew_cha_more").hide();
                } else {
                  $(".rew_cha_more").show();
                }
              }, 3000);

              return false;
            }
          }
        },
      });
    });

    //챌린지 템플릿
    /*$(".tpl_list_area ul").sortable({
        axis: "y",
        opacity: 0.7,
        zIndex: 9999,
        handle: ".tpl_list_drag",
        //placeholder:"sort_empty",
        cursor: "move"
      });*/

    //전체선택(챌린지)
    $(document).on("click", "#cha_template_tab_all", function () {
      //전체선택
      if ($("#cha_template_tab_all").is(":checked") == true) {
        var chk_cnt = $(".rew_cha_chk_tab .chk_tab input").length;
        for (var i = 0; i < chk_cnt; i++) {
          if (
            $(".rew_cha_chk_tab .chk_tab input:eq(" + i + ")").is(":checked") ==
            false
          ) {
            $(".rew_cha_chk_tab .chk_tab input:eq(" + i + ")").prop(
              "checked",
              true
            );
          }
        }
      } else {
        var chk_cnt = $(".rew_cha_chk_tab .chk_tab input").length;
        for (var i = 0; i < chk_cnt; i++) {
          if (
            $(".rew_cha_chk_tab .chk_tab input:eq(" + i + ")").is(":checked") ==
            true
          ) {
            $(".rew_cha_chk_tab .chk_tab input:eq(" + i + ")").prop(
              "checked",
              false
            );
          }
        }
      }
      //challenges_ajax_list();
    });

    //챌린지 템플릿 선택 모두 선택 되었을때 일부 체크 해제시 전체선택을 해제 함
    $(document).on("click", "input[name=cha_template_tab]", function () {
      var chk_cnt = $(".rew_cha_chk_tab .chk_tab input").length;
      $("#pageno").val(1);
      for (var i = 0; i < chk_cnt; i++) {
        if (
          $(".rew_cha_chk_tab .chk_tab input:eq(" + i + ")").is(":checked") ==
          false
        ) {
          $(".rew_cha_chk_tab .chk_tab input:eq(0)").prop("checked", false);
        }
      }

      if ($(".rew_cha_chk_tab .chk_tab input:eq(1)").is(":checked") == true) {
        var chk1 = true;
      } else {
        var chk1 = false;
      }

      if ($(".rew_cha_chk_tab .chk_tab input:eq(2)").is(":checked") == true) {
        var chk2 = true;
      } else {
        var chk2 = false;
      }

      if (chk1 && chk2) {
        if (
          $(".rew_cha_chk_tab .chk_tab input:eq(0)").is(":checked") == false
        ) {
          $(".rew_cha_chk_tab .chk_tab input:eq(0)").prop("checked", true);
        }
      }

      challenges_thema_ajax_list_check();
    });

    $(document).on("click", "input[name=cha_template_tab]", function () {
      tab_id = $(this).attr("id");
      if ($(this).closest(".chk_tab").hasClass("on") == true) {
        $(this).closest(".chk_tab").removeClass("on");
      } else {
        $(this).closest(".chk_tab").addClass("on");
      }
    });

    //테마리스트 찜하기 및 찜하기 해제
    $(document).on("click", "button[id^=cha_zzim]", function () {
      if ($(this).hasClass("on") == true) {
        //해제 할때
        if (confirm("해당 챌린지를 찜하기 해제 하시겠습니까?")) {
          $(this).toggleClass("on");
          var fdata = new FormData();
          var zzim = 1;
          fdata.append("mode", "thema_zzim");
          val = $(this).parent().attr("value");
          fdata.append("val", val);
          fdata.append("zzim", zzim);

          $.ajax({
            type: "post",
            data: fdata,
            contentType: false,
            processData: false,
            url: "/inc/template_process.php",
            success: function (data) {
              //console.log(data);
              if (data == "complete") {
                //$("#tpl_list_zzim").trigger("click");
              }
            },
          });
        }
      } else {
        $(this).toggleClass("on");
        var fdata = new FormData();
        var zzim = 0;
        fdata.append("mode", "thema_zzim");
        val = $(this).parent().attr("value");
        fdata.append("val", val);
        fdata.append("zzim", zzim);

        $.ajax({
          type: "post",
          data: fdata,
          contentType: false,
          processData: false,
          url: "/inc/template_process.php",
          success: function (data) {
            console.log(data);
            if (data) {
            }
          },
        });
      }
    });

    //내가 찜한 챌린지
    $(document).on("click", "#tpl_list_zzim", function () {
      console.log("찜한챌린지");

      var fdata = new FormData();
      fdata.append("mode", "challenges_thema_zzim_list_all");
      fdata.append("zzim", "1");
      $("#thema_zzim").val("1");

      $.ajax({
        type: "post",
        data: fdata,
        contentType: false,
        processData: false,
        url: "/inc/template_process.php",
        success: function (data) {
          console.log(data);
          if (data) {
            tdata = data.split("|");
            if (tdata) {
              var html = tdata[0];
              var totcnt = tdata[1];
              var listcnt = tdata[2];

              $("#page_count").val(parseInt(listcnt));
              $("#thema_title").text("내가 찜한 챌린지");
              $(".rew_cha_count strong").text(totcnt);
              $("#rew_conts_in").html("");
              $("#rew_conts_in").append(html);

              $(".rew_cha_list_ul li:not('.sli')").each(function (aa) {
                var tis = $(this);
                var tindex = $(this).index();
                //alert(tindex);
                setTimeout(function () {
                  tis.addClass("sli");
                }, 100 + tindex * 200);
              });

              //더보기 버튼
              setTimeout(function () {
                if (listcnt >= $("#page_count").val()) {
                  $(".rew_cha_more").hide();
                } else {
                  $(".rew_cha_more").show();
                }
              }, 3000);

              return false;
            }
          }
        },
      });
    });

    //챌린지 테마 제목 수정
    $(document).on("click", "#thema_title", function () {
      if ($("#thema_idx").val()) {
        $("#thema_title_edit").show();

        //포커스마지막으로 이동
        var elem = $("input[id=input_thema_title]");
        setTimeout(function () {
          var input = elem;
          var v = input.val();
          input.focus().val("").val(v);
        }, 50);
      }
    });

    //제목수정시 엔터 입력시
    $("#input_thema_title").bind("input keyup", function (e) {
      var input_val = $(this).val();
      if (input_val) {
        if (e.keyCode == 13) {
          $("#btn_thema_submit").trigger("click");
          return false;
        }
      }
    });

    //챌린지 테마 제목 수정하기 확인
    $(document).on("click", "#btn_thema_submit", function () {
      if ($("#thema_title_edit").is(":visible") == true) {
        var elem = $("input[id=input_thema_title]");
        var thema_idx = $("#thema_idx").val();

        if (elem && thema_idx) {
          var val = elem.val();
          var fdata = new FormData();
          fdata.append("mode", "thema_title_edit");
          fdata.append("val", val);
          fdata.append("thema_idx", thema_idx);

          $.ajax({
            type: "post",
            data: fdata,
            contentType: false,
            processData: false,
            url: "/inc/template_process.php",
            success: function (data) {
              console.log(data);
              if (data) {
                if (data == "complete") {
                  if ($("#thema_title_edit").is(":visible") == true) {
                    $("#thema_title").text(val);
                    $("#input_thema_title").val(val);
                    $("#tpl_list_title" + thema_idx).text(val);
                    $("#thema_title_edit").hide();
                  }
                }
              }
            },
          });
        }
      }
    });

    //챌린지 테마 제목 수정하기 취소
    $(document).on("click", "#btn_thema_cancel", function () {
      if ($("#thema_title_edit").is(":visible") == true) {
        $("#input_thema_title").val($("#thema_title").text());
        $("#thema_title_edit").hide();
      }
    });

    //챌린지 테마 전체
    $(document).on("click", "#thema_all", function () {
      $("#thema_title").text("전체");
      $("#thema_idx").val("");
      $("#thema_zzim").val("");
      $("#thema_temp").val("");
      challenges_thema_ajax_list();
    });

    //챌린지 추천테마
    $("#thema_rec").click(function () {
      var fdata = new FormData();
      fdata.append("mode", "thema_recom_list");

      $.ajax({
        type: "post",
        data: fdata,
        contentType: false,
        processData: false,
        url: "/inc/template_process.php",
        success: function (data) {
          console.log(data);
          if (data) {
            $("#rew_conts_in").html("");
            $("#rew_conts_in").html(data);

            $(".rew_cha_list_ul li").each(function () {
              var tis = $(this);
              var tindex = $(this).index();
              setTimeout(function () {
                tis.addClass("sli");
              }, 100 + tindex * 200);
            });

            //더보기 버튼
            setTimeout(function () {
              if (page >= $("#page_count").val()) {
                $(".rew_cha_more").hide();
              } else {
                $(".rew_cha_more").show();
              }
            }, 10);
          }
        },
      });
    });

    //챌린지 좌측 테마별 선택
    $(document).on("click", "strong[id^=tpl_list_title]", function () {
      //$("#tpl_list_title")

      var id = $(this).attr("id");
      var no = id.replace("tpl_list_title", "");

      if ($("#template_sort").is(":visible") == false) {
        $("#template_sort").show();
      }

      if ($("#rew_cha_search").is(":visible") == false) {
        $("#rew_cha_search").show();
      }

      if ($("#rew_cha_chk_tab").is(":visible") == false) {
        $("#rew_cha_chk_tab").show();
      }

      $(".rew_mypage_tpl_list .tpl_list_area li").removeClass("on");
      $(this).parent().parent().addClass("on");

      if (no) {
        $("#thema_zzim").val("");
        $("#thema_temp").val("");
        if ($("#themasort_" + no + " strong").text()) {
          $("#thema_title").text($("#themasort_" + no + " strong").text());
          $("#input_thema_title").val($("#themasort_" + no + " strong").text());
        }
        $("#thema_idx").val(no);
        challenges_thema_ajax_list();
      } else {
        $("#thema_idx").val("");
      }
    });

    //챌린지 좌측 테마별 선택
    /*$(document).on("click", "li[id^=themasort]", function() {
        var id = $(this).attr("id");
        var no = id.replace("themasort_", "");
        if (no) {
          $("#thema_zzim").val("");
          if ($("#themasort_" + no + " strong").text()) {
            $("#thema_title").text($("#themasort_" + no + " strong").text());
            $("#input_thema_title").val($("#themasort_" + no + " strong").text());
          }
          $("#thema_idx").val(no);

          challenges_thema_ajax_list();
        }
      });*/

    //테마리스트 드래그, 테마이동
    $(document).on("click", ".tpl_list_area ul", function () {
      $(this).sortable({
        axis: "y",
        opacity: 0.7,
        zIndex: 9999,
        handle: ".tpl_list_drag",
        //placeholder:"sort_empty",
        cursor: "move",
        update: function (event, ui) {
          var fdata = new FormData();
          var listsort = $(this).sortable("serialize");
          fdata.append("mode", "thema_move");
          fdata.append("listsort", listsort);
          $.ajax({
            type: "POST",
            data: fdata,
            contentType: false,
            processData: false,
            url: "/inc/template_process.php",
            success: function (data) {
              console.log(data);
              if (data == "complete") {
                //$(".calendar_num").text(data);
                return false;
              }
            },
          });
        },
      });
    });

    //전체선택(챌린지)
    $(document).on("click", "#cha_chk_tab_all", function () {
      //전체선택
      if ($("#cha_chk_tab_all").is(":checked") == true) {
        var chk_cnt = $(".rew_cha_chk_tab .chk_tab input").length;
        for (var i = 0; i < chk_cnt; i++) {
          if (
            $(".rew_cha_chk_tab .chk_tab input:eq(" + i + ")").is(":checked") ==
            false
          ) {
            $(".rew_cha_chk_tab .chk_tab input:eq(" + i + ")").prop(
              "checked",
              true
            );
          }
        }
      } else {
        var chk_cnt = $(".rew_cha_chk_tab .chk_tab input").length;
        for (var i = 0; i < chk_cnt; i++) {
          if (
            $(".rew_cha_chk_tab .chk_tab input:eq(" + i + ")").is(":checked") ==
            true
          ) {
            $(".rew_cha_chk_tab .chk_tab input:eq(" + i + ")").prop(
              "checked",
              false
            );
          }
        }
      }
      challenges_ajax_list();
    });

    //챌린지 선택 모두 선택 되었을때 일부 체크 해제시 전체선택을 해제 함
    //$(document).on("click", ".rew_cha_chk_tab .chk_tab input", function() {
    $(document).on("click", "input[name=cha_chk_tab]", function () {
      var chk_cnt = $(".rew_cha_chk_tab .chk_tab input").length;
      for (var i = 0; i < chk_cnt; i++) {
        if (
          $(".rew_cha_chk_tab .chk_tab input:eq(" + i + ")").is(":checked") ==
          false
        ) {
          $(".rew_cha_chk_tab .chk_tab input:eq(0)").prop("checked", false);
        }
      }
      $("#pageno").val(1);

      if ($(".rew_cha_chk_tab .chk_tab input:eq(1)").is(":checked") == true) {
        var chk1 = true;
      } else {
        var chk1 = false;
      }

      if ($(".rew_cha_chk_tab .chk_tab input:eq(2)").is(":checked") == true) {
        var chk2 = true;
      } else {
        var chk2 = false;
      }

      if ($(".rew_cha_chk_tab .chk_tab input:eq(3)").is(":checked") == true) {
        var chk3 = true;
      } else {
        var chk3 = false;
      }

      if ($(".rew_cha_chk_tab .chk_tab input:eq(4)").is(":checked") == true) {
        var chk4 = true;
      } else {
        var chk4 = false;
      }

      if (chk1 && chk2 && chk3 && chk4) {
        if (
          $(".rew_cha_chk_tab .chk_tab input:eq(0)").is(":checked") == false
        ) {
          $(".rew_cha_chk_tab .chk_tab input:eq(0)").prop("checked", true);
        }
      }

      challenges_ajax_list();
    });

    //챌린지뷰페이지
    if (pagename.indexOf("view") == 0) {
      if ($(".view_user").offset()) {
        var vut = $(".view_user").offset().top;
        if (vut > 0) {
          setTimeout(function () {
            $(".view_user").addClass("on");
          }, 1300);
        } else {
          $(".view_user").removeClass("on");
        }
      }

      $(".rew_conts_scroll_06").scroll(function () {
        var vbt = $(".rew_cha_view").offset().top;
        if (vbt < 0) {
          $(".rew_cha_view_top").addClass("pos_fix");
        } else {
          $(".rew_cha_view_top").removeClass("pos_fix");
        }

        var vut = $(".view_user").offset().top;
        if (vut > 0) {
          $(".view_user").addClass("on");
        } else {
          $(".view_user").removeClass("on");
        }

        var sc6h = $(".rew_conts_scroll_06").height();

        if ($(".rew_cha_view_result ul")[0] != undefined) {
          var vrt = $(".rew_cha_view_result ul").offset().top;
          if (vrt < sc6h) {
            $(".rew_cha_view_result ul").addClass("on");
          } else {
            $(".rew_cha_view_result ul").removeClass("on");
          }
        }

        //인증메시지 내용 나오게하기
        $(".masage_zone > div").each(function () {
          var mzthis = $(this);
          var mzt = $(this).offset().top;
          var sc6h = $(".rew_conts_scroll_06").height();
          if (mzt < sc6h) {
            mzthis.addClass("on");
          } else {
            mzthis.removeClass("on");
          }
        });

        $(".rew_cha_view_mix .mix_zone > div").each(function () {
          var mzthis = $(this);
          var mzt = $(this).offset().top;
          var sc6h = $(".rew_conts_scroll_06").height();
          if (mzt < sc6h) {
            mzthis.addClass("on");
          } else {
            mzthis.removeClass("on");
          }
        });

        /*var offset01 = $(".rew_conts_scroll_06").offset();
        var offset02 = $(".rew_cha_view_result").position();
        var offset03 = $(".rew_cha_view_masage").position();

        if (offset01.top < (offset02.top - 150)) {
          $(".rew_cha_view_top .view_top_nav li button").removeClass("on");
          $("#go_view_01").addClass("on");
        }

        if (offset01.top > (offset02.top - 150)) {
          $(".rew_cha_view_top .view_top_nav li button").removeClass("on");
          $("#go_view_02").addClass("on");
        }

        if (offset01.top > (offset03.top - 150)) {
          $(".rew_cha_view_top .view_top_nav li button").removeClass("on");
          $("#go_view_03").addClass("on");
        }*/
      });

      //////////////////////

      /*$("#go_view_01").click(function() {
        var offset1 = $(".rew_conts_scroll_06").offset();
        $(".rew_conts_scroll_06").animate({ scrollTop: 0 }, 400 + (offset1.top / 2));
      });
      var offset2 = $(".rew_cha_view_result").position();
      $("#go_view_02").click(function() {
        var offset1 = $(".rew_conts_scroll_06").offset();
        var offset_sum = Math.abs(offset1.top - offset2.top);
        setTimeout(function() {
          $(".rew_conts_scroll_06").animate({ scrollTop: offset2.top - 155 }, offset_sum / 4 + 200);
        }, 100);
      });

      var offset3 = $(".rew_cha_view_masage").position();
      $("#go_view_03").click(function() {
        var offset1 = $(".rew_conts_scroll_06").offset();
        //console.log(offset2.top);
        var offset_sum = Math.abs(offset1.top - offset3.top);
        setTimeout(function() {
          $(".rew_conts_scroll_06").animate({ scrollTop: offset3.top - 155 }, offset_sum / 4 + 200);
        }, 100);
      });*/

      $(".conts_tab ul li button").click(function () {
        var drM_li = $(this).parent("li").index();

        var offt = $(".conts_main > div:eq(" + drM_li + ")").offset();
        var tabt = $(".conts_tab").offset();
        var matht = Math.abs(tabt.top - offt.top);
        $("html, body").animate({ scrollTop: offt.top - 101 }, matht / 8 + 200);

        return false;
      });

      $(".layer_user_info dt button").click(function () {
        $(this).parent().parent("dl").toggleClass("on");
      });

      $(".layer_user_info dd button").click(function () {
        $(this).toggleClass("on");
        if ($(".layer_user_info dd button").hasClass("on")) {
          $(".layer_user_submit").addClass("on");
        } else {
          $(".layer_user_submit").removeClass("on");
        }
      });
    }
    //챌린지뷰페이지

    //클릭처리
    $("ul[id=tpl_list_area_ul]").trigger("click");
  }

  //좌측메뉴 열고 닫기
  // $(document).on("click",".rew_menu_onoff button", function(){
  //   var thisonoff = $(this);
  //   var fdata = new FormData();
  //   fdata.append("mode", "rew_menu_onoff");
  //   console.log(" :: " + thisonoff.hasClass("on"));

  //   if (thisonoff.hasClass("on")) {
  //     thisonoff.removeClass("on");
  //     $(".rew_box").removeClass("on");
  //     //setCookie('rew_menu_onoff', '1', '365');
  //     fdata.append("onoff", "1");
  //   } else {
  //     thisonoff.addClass("on");
  //     $(".rew_box").addClass("on");
  //     fdata.append("onoff", "0");
  //   }

  //   $.ajax({
  //     type: "POST",
  //     data: fdata,
  //     //async: false,
  //     contentType: false,
  //     processData: false,
  //     url: "/inc/process.php",
  //     success: function (data) {
  //       console.log(data);
  //     },
  //   });
  // });

  $(document).on("click", ".rew_menu_onoff button", function () {
    if ($(".rew_menu_onoff button").hasClass("on")) {
      setCookie("onoff", "1", 365);
      $(".rew_menu_onoff button").removeClass("on");
      $(".rew_box").removeClass("on");
    } else {
      setCookie("onoff", "0", 365);
      $(".rew_menu_onoff button").addClass("on");
      $(".rew_box").addClass("on");
    }
  });

  //참여횟수설정 - 매일
  $("#ch_daily").click(function () {
    var sdate = $("#sdate").val();
    var edate = $("#edate").val();
    var get_diff = dateDiff(sdate, edate);

    if (get_diff < 0) {
      get_diff = 1;
    }

    if ($(".input_count").val()) {
      var input_count = parseInt($(".input_count").val());
      //if (input_count > 1) {

      if ($("#ch_daily").hasClass("btn_on") == true) {
        $("#ch_daily").removeClass("btn_on");

        if ($("#ch_holiday").hasClass("btn_chk_on") == true) {
          $("#ch_holiday").removeClass("btn_chk_on");
        }
      } else {
        $("#ch_daily").addClass("btn_on");

        console.log("get_diff :: " + get_diff);

        if ($(".input_count").val() != get_diff) {
          $(".input_count").val(get_diff);
          //$(".rew_cha_setting_count .title_area .qna .title_desc").text("1일 " + get_diff + "회 참여할 수 있어요.");
        }

        if ($("#ch_once").hasClass("btn_on") == true) {
          $("#ch_once").removeClass("btn_on");
        }
      }
      challenges_reward_coin();
      //}
    }
  });

  $("#ch_once").click(function () {
    if ($("#ch_once").hasClass("btn_on") == false) {
      $("#ch_once").addClass("btn_on");

      if ($("#ch_daily").hasClass("btn_on") == true) {
        $("#ch_daily").removeClass("btn_on");
      }

      $(".input_count").val(1);
      $(".rew_cha_setting_count .title_area .qna .title_desc").text(
        "1일 1회 참여할 수 있어요."
      );

      challenges_reward_coin();
    } else {
      if ($(".input_count").val()) {
        var input_count = parseInt($(".input_count").val());
        if (input_count > 1) {
          if ($("#ch_once").hasClass("btn_on") == true) {
            $("#ch_once").removeClass("btn_on");

            if ($("#ch_holiday").hasClass("btn_chk_on") == true) {
              $("#ch_holiday").removeClass("btn_chk_on");
            }

            if ($("#ch_daily").hasClass("btn_on") == true) {
              $("#ch_daily").removeClass("btn_on");
            }
          } else {
            $("#ch_once").addClass("btn_on");

            if ($("#ch_holiday").hasClass("btn_chk_on") == true) {
              $("#ch_holiday").removeClass("btn_chk_on");
            }

            if ($("#ch_daily").hasClass("btn_on") == true) {
              $("#ch_daily").removeClass("btn_on");
            }
          }
        } else {
          if ($("#ch_once").hasClass("btn_on") == false) {
            $("#ch_once").addClass("btn_on");
          }
        }
        challenges_reward_coin();
      }
    }
  });

  //$(".input_count").bind("change keyup input", function(event) {

  //$("#sdate,#edate").bind("change keyup input", function(event) {
  //$("#sdate,#edate").on("propertychange change keyup paste input", function() {
  $("#sdate").change(function () {
    //console.log(222222222222);
  });

  //참여횟수설정 - 공휴일
  $("#ch_holiday").click(function () {
    if ($(".input_count").val()) {
      var input_count = parseInt($(".input_count").val());
      if (input_count > 1) {
        if ($("#ch_daily").hasClass("btn_on") == true) {
          if ($("#ch_holiday").hasClass("btn_chk_on") == true) {
            $("#ch_holiday").removeClass("btn_chk_on");

            var sdate = $("#sdate").val();
            var edate = $("#edate").val();
            date_diff = dateDiff(sdate, edate);
            if (date_diff) {
              $(".input_count").val(date_diff);
            }

            //console.log("sdt : " + sdt);
            //console.log("edt : " + edt);
            //console.log("off : " + dateDiff);
            //최대사용코인
            var max_coin = maxcoin();
            if (max_coin) {
              $("#maxcoin1").text(addComma(max_coin));
            }
          } else {
            $("#ch_holiday").addClass("btn_chk_on");
            var sdate = $("#sdate").val();
            var edate = $("#edate").val();
            var retday = calcDate(sdate, edate);
            if (retday) {
              $(".input_count").val(retday);
            }

            var max_coin = maxcoin();
            if (max_coin) {
              $("#maxcoin1").text(addComma(max_coin));
            }
          }
        }
      }
    }
  });

  //챌린지 코인사용안함
  $(document).on("click", "#not_coin_ico", function () {
    if ($("#max_coin_ico").hasClass("btn_chk_on") == true) {
      $("#max_coin_ico").removeClass("btn_chk_on");
    }

    if ($("#not_coin_ico").hasClass("btn_chk_on") == true) {
      $("#not_coin_ico").removeClass("btn_chk_on");

      if ($(".input_coin").val() == 0) {
        $(".input_coin").val("100");
      }

      $(".input_coin").attr("disabled", false);

      challenges_reward_coin();
      if ($("#rew_cha_setting_coin_calc").is(":visible") == false) {
        //    $("#rew_cha_setting_coin_calc").show();
      }
    } else {
      $("#not_coin_ico").addClass("btn_chk_on");
      if ($(".input_coin").val()) {
        $(".input_coin").val("0");
        $(".input_coin").attr("disabled", true);
      }

      if ($("#rew_cha_setting_coin_calc").is(":visible") == true) {
        //    $("#rew_cha_setting_coin_calc").hide();
      }
      challenges_reward_coin();
    }
  });

  //챌린지 보상코인설정 - 최대사용
  $("#max_coin_ico").click(function () {
    $(".input_coin").attr("disabled", false);

    if ($("#max_coin_ico").hasClass("btn_chk_on") == true) {
      $("#max_coin_ico").removeClass("btn_chk_on");
      $("#max_coin_ico").addClass("btn_chk_off");
      $(".input_coin").val(setcoin);
      challenges_reward_coin();

      //코인사용 안함 체크됨
      if ($("#not_coin_ico").hasClass("btn_chk_on") == true) {
        $("#not_coin_ico").removeClass("btn_chk_on");

        if ($("#rew_cha_setting_coin_calc").is(":visible") == false) {
          $("#rew_cha_setting_coin_calc").show();
        }
      }

      if ($(".input_coin").val() == setcoin) {
        $(".btn_coin_minus").addClass("coin_limit");

        var n_coin = $(".rew_cha_setting_coin_calc .calc_03 strong").text();
        var n_coin = reNumber(n_coin);
        if (n_coin <= 0) {
          $(".rew_cha_setting_coin_calc .calc_03 strong").css(
            "color",
            "#f10006"
          );
        } else {
          $(".rew_cha_setting_coin_calc .calc_03 strong").css(
            "color",
            "#252525"
          );
        }
      }

      //체크 해제 했을때
    } else {
      if ($("#not_coin_ico").hasClass("btn_chk_on") == true) {
        $("#not_coin_ico").removeClass("btn_chk_on");

        if ($("#rew_cha_setting_coin_calc").is(":visible") == false) {
          $("#rew_cha_setting_coin_calc").show();
        }
      }

      /*var sdate = $("#sdate").val();
      var edate = $("#edate").val();
      var input_count = unComma($(".input_count").val());
      var input_coin = unComma($(".input_coin").val());
      var common_coin = unComma($("#common_coin").text());

      if ($(".rew_cha_setting_user_area button").eq(0).hasClass("btn_on") == true) {
        var member_cnt = member_total_cnt;

      }else if ($(".rew_cha_setting_user_area button").eq(1).hasClass("btn_on") == true) {
        //챌린지설정 - 일부
        if ($("#chall_user_chk").val()) {
          user_chk_val = $("#chall_user_chk").val();
          arr_val = user_chk_val.split(",");
          if (arr_val.length > 0) {
            userchk = arr_val.length;

          } else {
            userchk = 0;
          }
          var member_cnt = userchk;
        }
      }

      //횟수 * 참여자수
      rcoin = input_count * member_cnt;
      acoin = unComma($("#common_coin").text());

      //지급예상코인
      bcoin = rcoin * 100;

      ccoin = acoin - bcoin;
      dcoin = Math.ceil(ccoin / rcoin);
      ecoin = Math.ceil(dcoin/ 100)*100;
      if(ecoin){
        $(".input_coin").val(addComma(ecoin));
      }*/
      var max_coin = maxcoin();
      if (max_coin) {
        $(".input_coin").val(addComma(max_coin));
        $("#maxcoin1").text(addComma(max_coin));
        challenges_reward_coin();

        var n_coin = $(".rew_cha_setting_coin_calc .calc_03 strong").text();
        var n_coin = reNumber(n_coin);
        if (n_coin <= 0) {
          $(".rew_cha_setting_coin_calc .calc_03 strong").css(
            "color",
            "#f10006"
          );
        } else {
          $(".rew_cha_setting_coin_calc .calc_03 strong").css(
            "color",
            "#252525"
          );
        }
      }

      if (max_coin > setcoin) {
        $(".btn_coin_minus").removeClass("coin_limit");
      }

      $("#max_coin_ico").removeClass("btn_chk_off");
      $("#max_coin_ico").addClass("btn_chk_on");
    }
  });

  function maxcoin() {
    var input_count = unComma($(".input_count").val());
    var input_coin = unComma($(".input_coin").val());
    var common_coin = unComma($("#common_coin").text());
    var member_cnt = 0;

    //참여자설정 - 전체
    if (
      $(".rew_cha_setting_user_area button").eq(0).hasClass("btn_on") == true
    ) {
      var member_cnt = member_total_cnt;
    } else if (
      $(".rew_cha_setting_user_area button").eq(1).hasClass("btn_on") == true
    ) {
      //참여자설정 - 일부
      if ($("#chall_user_chk").val()) {
        user_chk_val = $("#chall_user_chk").val();
        arr_val = user_chk_val.split(",");
        if (arr_val.length > 0) {
          member_cnt = arr_val.length;
        } else {
          member_cnt = 0;
        }
      }
    }

    /*  1. 154000-30000 = 124,000
        2. 31x10 = 310
        3. 124,000 / 310 = 400
        30000 = 154000 - ((25*10) * X);
      */

    //횟수 * 참여자수
    rcoin = input_count * member_cnt;
    acoin = common_coin;

    //지급예상코인
    bcoin = rcoin * setcoin;

    ccoin = acoin - bcoin;
    dcoin = Math.ceil(ccoin / rcoin);
    ecoin = Math.ceil(dcoin / setcoin) * setcoin;
    if (ecoin) {
      return ecoin;
    }
  }

  //챌린지 참여 횟수 설정
  var pass = false;
  $(".btn_count_plus").on({
    mouseup: function () {
      pass = false;
    },
    mousedown: function () {
      pass = true;
      count_ticket("1");
    },
  });

  $(".btn_count_minus").on({
    mouseup: function () {
      pass = false;
    },
    mousedown: function () {
      pass = true;
      count_ticket("-1");
    },
  });

  //챌린지 보상 코인 설정
  var pass = false;
  $(".btn_coin_plus").on({
    mouseup: function () {
      pass = false;
    },
    mousedown: function () {
      pass = true;
      count_coin_ticket(setcoin);
    },
  });

  $(".btn_coin_minus").on({
    mouseup: function () {
      pass = false;
    },
    mousedown: function () {
      pass = true;
      count_coin_ticket(-setcoin);
    },
  });

  $(".input_count").focusout("change keyup input", function (event) {
    coin_data = challenges_reward_coin();
    if (coin_data) {
      if (coin_data[1] <= 0) {
        /*    $(".btn_count_minus").addClass("count_limit");
            $(".rew_cha_setting_count .title_area .qna .title_desc").text("");
            $(this).val("");
            return false;
            */
      }
    }
  });

  //챌린지 참여횟수 설정 - 인풋박스
  $(".input_count").bind("input change", function (event) {
    var val = $(this).val();
    if (val) {
      val = parseInt(val);
      if (val == 0) {
        $(this).val("");
      } else if (val >= 1) {
        var n_coin = $(".rew_cha_setting_coin_calc .calc_03 strong").text();
        var n_coin = reNumber(n_coin);
        if (n_coin <= 0) {
          //$(this).val($(this).val().replace($(this).val().substr(1, 2), ""));
          //return false;
        }

        if (val == 1) {
          if ($("#ch_daily").hasClass("btn_on") == true) {
            $("#ch_daily").removeClass("btn_on");
          }

          if ($("#ch_once").hasClass("btn_on") == false) {
            $("#ch_once").addClass("btn_on");
          }
        } else {
          if ($("#ch_once").hasClass("btn_on") == true) {
            $("#ch_once").removeClass("btn_on");
          }

          if ($("#ch_daily").hasClass("btn_on") == false) {
            $("#ch_daily").addClass("btn_on");
          }
        }

        coin_data = challenges_reward_coin();

        //$(".rew_cha_setting_count .title_area .qna .title_desc").text("1일 " + $(this).val() + "회 참여할 수 있어요.");
        //$("#ch_once").removeClass("btn_on");

        if (val > 31) {
          $(this).val($(this).val().replace($(this).val().substr(1, 2), ""));
          if ($(this).val() == 1) {
            //$(".rew_cha_setting_count .title_area .qna .title_desc").text("1일 " + $(this).val() + "회 참여할 수 있어요.");

            if ($("#ch_daily").hasClass("btn_on") == true) {
              $("#ch_daily").removeClass("btn_on");
            }

            if ($("#ch_once").hasClass("btn_on") == false) {
              $("#ch_once").addClass("btn_on");
            }
          } else {
            //$(".rew_cha_setting_count .title_area .qna .title_desc").text("1일 " + $(this).val() + "회 참여할 수 있어요.");
          }
          return false;
        }

        if (coin_data) {
          if (coin_data[1] <= 0) {
            $(".rew_cha_setting_coin_calc .calc_03 strong").css(
              "color",
              "#f10006"
            );
          } else {
            $(".rew_cha_setting_coin_calc .calc_03 strong").css(
              "color",
              "#252525"
            );
          }
          $(".rew_cha_setting_coin_calc .calc_02 strong").text(
            addComma(coin_data[0])
          );
          $(".rew_cha_setting_coin_calc .calc_03 strong").text(
            addComma(coin_data[1])
          );
        }
      } else {
        //한번
        if ($("#ch_once").hasClass("btn_on") == false) {
          $("#ch_once").addClass("btn_on");
        }

        //매일
        if ($("#ch_daily").hasClass("btn_on") == true) {
          $("#ch_daily").removeClass("btn_on");
        }

        //공휴일
        if ($("#ch_holiday").hasClass("btn_chk_on") == true) {
          $("#ch_holiday").removeClass("btn_chk_on");
        }

        $(".rew_cha_setting_count .title_area .qna .title_desc").text("");
        $(".btn_count_minus").addClass("count_limit");
      }
    } else {
      if ($("#ch_once").hasClass("btn_on") == true) {
        $("#ch_once").removeClass("btn_on");
      }
      $(".btn_count_minus").addClass("count_limit");
      $(".rew_cha_setting_count .title_area .qna .title_desc").text("");
    }
  });

  //챌린지 보상코인설정 - 인풋박스
  $(".input_coin").bind("change keyup input", function (event) {
    var val = $(this).val();
    if (val) {
      val = parseInt(val);
      coin_data = challenges_reward_coin();
      val_mod = parseInt(val / setcoin);

      if (val_mod != 0) {
      }

      if (val == 0) {
        $(this).val("");
      }

      if (coin_data) {
        if (coin_data[1] <= 0) {
          $(".rew_cha_setting_coin_calc .calc_03 strong").css(
            "color",
            "#f10006"
          );
        } else {
          $(".rew_cha_setting_coin_calc .calc_03 strong").css(
            "color",
            "#252525"
          );
        }
        $(".rew_cha_setting_coin_calc .calc_02 strong").text(
          addComma(coin_data[0])
        );
        $(".rew_cha_setting_coin_calc .calc_03 strong").text(
          addComma(coin_data[1])
        );
      }
    }
  });

  //챌린지작성하기 - 참여자횟수설정
  function count_ticket(su) {
    var max_number = 7;
    if (pass) {
      if ($(".btn_count_minus").hasClass("count_limit") == true) {
        //    return false;
      }

      if ($(".btn_count_plus").hasClass("count_limit") == true) {
        //    console.log(22222222);
        //    return false;
      }

      if ($(".input_count").val()) {
        var input_count = parseInt($(".input_count").val());
      } else {
        input_count = 0;
      }

      var common_coin = unComma($("#common_coin").text());

      if (su == "1") {
        //플러스
        if (input_count < max_number) {
          input_count = input_count + 1;
          $(".input_count").val(input_count);
          coin_data = challenges_reward_coin();

          $(".btn_count_minus").removeClass("count_limit");
          $(".rew_cha_setting_count .title_area .qna .title_desc").text(
            "최대 " + input_count + "번까지 참여가능. 1일 1회 한정"
          );
          $("#ch_once").removeClass("btn_on");

          if (input_count == max_number) {
            $(".btn_count_plus").addClass("count_limit");
          }

          if (coin_data) {
            if (coin_data[1] <= 0) {
              $(".rew_cha_setting_coin_calc .calc_03 strong").css(
                "color",
                "#f10006"
              );
            } else {
              $(".rew_cha_setting_coin_calc .calc_03 strong").css(
                "color",
                "#252525"
              );
            }
            $(".rew_cha_setting_coin_calc .calc_02 strong").text(
              addComma(coin_data[0])
            );
            $(".rew_cha_setting_coin_calc .calc_03 strong").text(
              addComma(coin_data[1])
            );
          }

          if ($("#max_coin_ico").hasClass("btn_chk_on") == true) {
            $("#max_coin_ico").removeClass("btn_chk_on");
          }

          //최대사용코인
          var max_coin = maxcoin();
          if (max_coin) {
            $("#maxcoin1").text(addComma(max_coin));
          }
        } else {
          $(".btn_count_plus").addClass("count_limit");
        }
      } else {
        //마이너스
        if (max_number > 0 && input_count > 1) {
          var n_coin = $(".rew_cha_setting_coin_calc .calc_03 strong").text();
          var n_coin = reNumber(n_coin);
          if (n_coin <= 0) {
            $(".btn_count_minus").addClass("count_limit");
            return false;
          }

          input_count = input_count - 1;
          $(".input_count").val(input_count);
          coin_data = challenges_reward_coin();

          if (input_count == 1) {
            $(".btn_count_minus").addClass("count_limit");
            $(".rew_cha_setting_count .title_area .qna .title_desc").text("");
            $("#ch_once").addClass("btn_on");

            if ($("#ch_daily").hasClass("btn_on") == true) {
              $("#ch_daily").removeClass("btn_on");
            }

            if ($("#ch_holiday").hasClass("btn_chk_on") == true) {
              $("#ch_holiday").removeClass("btn_chk_on");
            }
          } else {
            $(".btn_count_plus").removeClass("count_limit");
            $(".rew_cha_setting_count .title_area .qna .title_desc").text(
              "최대 " + input_count + "번까지 참여가능. 1일 1회 한정"
            );
            $("#ch_once").removeClass("btn_on");
          }

          if (coin_data) {
            if (coin_data[1] <= 0) {
              $(".rew_cha_setting_coin_calc .calc_03 strong").css(
                "color",
                "#f10006"
              );
            } else {
              $(".rew_cha_setting_coin_calc .calc_03 strong").css(
                "color",
                "#252525"
              );
            }

            $(".rew_cha_setting_coin_calc .calc_02 strong").text(
              addComma(coin_data[0])
            );
            $(".rew_cha_setting_coin_calc .calc_03 strong").text(
              addComma(coin_data[1])
            );
          }

          var max_coin = maxcoin();
          if (max_coin) {
            $("#maxcoin1").text(addComma(max_coin));
          }

          if ($("#max_coin_ico").hasClass("btn_chk_on") == true) {
            $("#max_coin_ico").removeClass("btn_chk_on");
          }
        } else {
        }
      }

      setTimeout(function () {
        count_ticket(su);
      }, 400);
    }
  }

  //챌린지 보상코인 설정
  function count_coin_ticket(su) {
    if ($("#user_count").val()) {
      alert("이미 참여자가 있으므로 코인 수정이 불가능 합니다!");
      return false;
    }

    if (pass) {
      var max_number = 1000000;

      if ($(".input_coin").val()) {
        input_coin = $(".input_coin").val();
        input_coin = input_coin.replace(/,/g, "");
        var input_coin = parseInt(input_coin);
      } else {
        input_coin = 0;
      }

      if (su == "100") {
        //플러스
        if (input_coin < max_number) {
          input_coin = input_coin + setcoin;
          input_coin = addComma(input_coin);

          $(".input_coin").val(input_coin);
          $(".btn_coin_minus").removeClass("coin_limit");

          coin_data = challenges_reward_coin();
          if (coin_data) {
            if (coin_data[1] <= 0) {
              $(".rew_cha_setting_coin_calc .calc_03 strong").css(
                "color",
                "#f10006"
              );
            } else {
              $(".rew_cha_setting_coin_calc .calc_03 strong").css(
                "color",
                "#252525"
              );
            }
            $(".rew_cha_setting_coin_calc .calc_02 strong").text(
              addComma(coin_data[0])
            );
            $(".rew_cha_setting_coin_calc .calc_03 strong").text(
              addComma(coin_data[1])
            );
          }

          if ($("#max_coin_ico").hasClass("btn_chk_on") == true) {
            $("#max_coin_ico").removeClass("btn_chk_on");
          }
        }
      } else {
        //마이너스
        if (max_number > 0 && input_coin > setcoin) {
          input_coin = input_coin - setcoin;
          input_coin = addComma(input_coin);
          $(".input_coin").val(input_coin);

          coin_data = challenges_reward_coin();

          if (input_coin <= setcoin) {
            $(".btn_coin_minus").addClass("coin_limit");
          }

          if (coin_data) {
            if (coin_data[1] <= 0) {
              $(".rew_cha_setting_coin_calc .calc_03 strong").css(
                "color",
                "#f10006"
              );
            } else {
              $(".rew_cha_setting_coin_calc .calc_03 strong").css(
                "color",
                "#252525"
              );
            }
            $(".rew_cha_setting_coin_calc .calc_02 strong").text(
              addComma(coin_data[0])
            );
            $(".rew_cha_setting_coin_calc .calc_03 strong").text(
              addComma(coin_data[1])
            );
          }

          if ($("#max_coin_ico").hasClass("btn_chk_on") == true) {
            $("#max_coin_ico").removeClass("btn_chk_on");
          }
        } else {
        }
      }
      setTimeout(function () {
        count_coin_ticket(su);
      }, 400);
    }
  }

  setTimeout(function () {
    $(".tabs_on").addClass("now_01");
  }, 1100);

  $(".btn_next_step_02").click(function () {
    $(".rew_cha_step_01").addClass("step_z");
    $(".rew_cha_step_02").addClass("step_z");
    $(".rew_cha_step_03").removeClass("step_z");
    $(".rew_cha_step_01").animate({ left: -100 + "%" }, 700);
    $(".rew_cha_step_02").animate({ left: 0 + "%" }, 700);
    $(".rew_cha_step_03").animate({ left: 100 + "%" }, 700);
    challenges_reward_coin();

    var max_coin = maxcoin();
    if (max_coin) {
      $("#maxcoin1").text(addComma(max_coin));
    }

    setTimeout(function () {
      $(".rew_cha_step_01").animate({ scrollTop: 0 }, 0);
      $(".rew_cha_step_02").animate({ scrollTop: 0 }, 0);
      $(".rew_cha_step_03").animate({ scrollTop: 0 }, 0);
    }, 200);

    setTimeout(function () {
      $(".tabs_on").addClass("now_02");
    }, 1100);
  });

  $(".btn_prev_step_01").click(function () {
    $(".rew_cha_step_01").addClass("step_z");
    $(".rew_cha_step_02").addClass("step_z");
    $(".rew_cha_step_03").removeClass("step_z");
    $(".rew_cha_step_01").animate({ left: 0 + "%" }, 700);
    $(".rew_cha_step_02").animate({ left: 100 + "%" }, 700);
    $(".rew_cha_step_03").animate({ left: -100 + "%" }, 700);

    setTimeout(function () {
      $(".rew_cha_step_01").animate({ scrollTop: 0 }, 0);
      $(".rew_cha_step_02").animate({ scrollTop: 0 }, 0);
      $(".rew_cha_step_03").animate({ scrollTop: 0 }, 0);
    }, 200);

    setTimeout(function () {
      $(".tabs_on").removeClass("now_02");
    }, 1100);
  });

  //챌린지 수정완료
  $("#ed_com").click(function () {
    //console.log($('#chall_contents').summernote('code'));

    if ($("#chall_template").val() != "1" && $("#chall_auth").val() != "1") {
      var common_coin = $("#common_coin").text();
      if (common_coin <= 0) {
        alert("현재 사용 가능 코인이 없습니다.\n코인을 확인해 주세요.");
        return false;
      }

      var ncoin = unComma($(".calc_03 strong").text());

      //코인사용안함 체크가 아닐때
      if ($("#not_coin_ico").hasClass("btn_chk_on") != true) {
        if (ncoin < 0) {
          alert(
            "현재 코인이 지급예상 코인보다 많습니다.\n참여횟수 설정 및 보상코인 설정을 확인해주세요."
          );
          return false;
        }
      }
    }
    ch_temp = $("#chall_template").val();

    if ($("#write_title").val() == "") {
      alert("챌린지 제목을 입력해주세요.");
      $(".btn_prev_step_01").trigger("click");
      $("#file_01").focus();
      return false;
    }

    if ($("#cate_title").val() == "") {
      alert("챌린지 카테고리를 선택해주세요.");
      $(".btn_prev_step_01").trigger("click");
      return false;
    }

    var mode = "chall_edit";
    var fdata = new FormData();
    var totalcoin = unComma($(".calc_02 strong").text());
    fdata.append("total_coin", totalcoin);
    fdata.append("ch_temp", ch_temp);
    fdata.append("mode", mode);
    fdata.append("temp_save", "0");
    console.log("mode : " + mode);

    if ($("#chall_idx").val()) {
      fdata.append("chall_idx", $("#chall_idx").val());
    }

    if ($("#write_type_01").is(":checked") == true) {
      fdata.append("write_type", "1");
    } else if ($("#write_type_02").is(":checked") == true) {
      fdata.append("write_type", "2");
    } else if ($("#write_type_03").is(":checked") == true) {
      fdata.append("write_type", "3");
    }

    if ($("#cate_title").val()) {
      fdata.append("cate", $("#cate_title").val());
    }

    //console.log(" cate_title :: " + $("#cate_title").val());

    // return false;
    if ($("#write_title").val()) {
      fdata.append("title", $("#write_title").val());
    }

    var chall_contents = $("#chall_contents").summernote("code");
    if (chall_contents) {
      fdata.append("contents", chall_contents);
    }

    if ($("input[id='file_01']").val()) {
      var file_name01 = $("input[id='file_01']").val();

      //파일1 확장자 체크
      if (uploadFile(file_name01) == "ok") {
        alert("지원하지않는 파일확장자입니다.");
        $(".btn_prev_step_01").trigger("click");
        return false;
      }

      //파일1 사이즈 체크
      if ($("input[id='file_01']")[0].files[0].size >= "52428800") {
        alert("파일용량이 50M를 초과합니다.\n 파일은 50M 미만으로 올려주세요.");
        $(".btn_prev_step_01").trigger("click");
        return false;
      }

      fdata.append("files[]", $("input[id='file_01']")[0].files[0]);
    }

    if ($("input[id='file_02']").val()) {
      var file_name02 = $("input[id='file_02']").val();

      //파일2 확장자 체크
      if (uploadFile(file_name02) == "ok") {
        alert("지원하지않는 파일확장자입니다.");
        $(".btn_prev_step_01").trigger("click");
        return false;
      }

      //파일2 사이즈 체크
      if ($("input[id='file_02']")[0].files[0].size >= "52428800") {
        alert("파일용량이 50M를 초과합니다.\n 파일은 50M 미만으로 올려주세요.");
        return false;
      }
      fdata.append("files[]", $("input[id='file_02']")[0].files[0]);
    }

    if ($("input[id='file_03']").val()) {
      var file_name03 = $("input[id='file_03']").val();

      //파일3 확장자 체크
      if (uploadFile(file_name03) == "ok") {
        alert("지원하지않는 파일확장자입니다.");
        $(".btn_prev_step_01").trigger("click");
        return false;
      }

      //파일3 사이즈 체크
      if ($("input[id='file_03']")[0].files[0].size >= "52428800") {
        alert("파일용량이 50M를 초과합니다.\n 파일은 50M 미만으로 올려주세요.");
        $(".btn_prev_step_01").trigger("click");
        return false;
      }
      fdata.append("files[]", $("input[id='file_03']")[0].files[0]);
    }

    if ($("#sdate").val()) {
      fdata.append("sdate", $("#sdate").val());
    }

    if ($("#edate").val()) {
      fdata.append("edate", $("#edate").val());
    }

    if ($(".input_count").val()) {
      fdata.append("input_count", $(".input_count").val());
    }

    if ($("#ch_once").hasClass("btn_on") == true) {
      fdata.append("ch_once", "1");
    }

    if ($("#ch_daily").hasClass("btn_on") == true) {
      fdata.append("ch_daily", "1");
    }

    if ($("#ch_holiday").hasClass("btn_chk_on") == true) {
      fdata.append("ch_holiday", "1");
    } else {
      fdata.append("ch_holiday", "0");
    }

    //테마추가
    if ($("#chall_thema_chk").val()) {
      fdata.append("chall_thema_chk", $("#chall_thema_chk").val());
    }

    //참여자설정 - 전체
    if (
      $(".rew_cha_setting_user_area button").eq(0).hasClass("btn_on") == true
    ) {
      fdata.append("user", "all");
    } else if (
      $(".rew_cha_setting_user_area button").eq(1).hasClass("btn_on") == true
    ) {
      //챌린지설정 - 일부
      fdata.append("user", "sel");
      fdata.append("chall_user_chk", $("#chall_user_chk").val());
    }

    if ($(".input_coin").val()) {
      fdata.append("input_coin", $(".input_coin").val());
    }

    if ($("#not_coin_ico").hasClass("btn_chk_on") == true) {
      fdata.append("ch_not_coin", true);
    }

    if ($("#write_keyword").val()) {
      fdata.append("write_keyword", $("#write_keyword").val());
    }

    if ($("#template_flag").val()) {
      fdata.append("template", $("#template_flag").val());
    }
    //console.log("chall_contents : " + chall_contents);

    $.ajax({
      type: "POST",
      data: fdata,
      //async: false,
      contentType: false,
      processData: false,
      url: "/inc/challenges_process.php",
      success: function (data) {
        console.log(data);

        if (data) {
          tdata = data.split("|");
          if (tdata) {
            var result = tdata[0];
            var temp_idx = tdata[1];

            if (temp_idx) {
              $("#chall_idx").val(temp_idx);
            }
            if (result == "complete") {
              //alert("임시저장 되었습니다.");

              $(".rew_cha_step_01").removeClass("step_z");
              $(".rew_cha_step_02").addClass("step_z");
              $(".rew_cha_step_03").addClass("step_z");
              $(".rew_cha_step_01").animate({ left: 100 + "%" }, 700);
              $(".rew_cha_step_02").animate({ left: -100 + "%" }, 700);
              $(".rew_cha_step_03").animate({ left: 0 + "%" }, 700);

              setTimeout(function () {
                $(".rew_cha_step_01").animate({ scrollTop: 0 }, 0);
                $(".rew_cha_step_02").animate({ scrollTop: 0 }, 0);
                $(".rew_cha_step_03").animate({ scrollTop: 0 }, 0);
              }, 200);

              setTimeout(function () {
                $(".tabs_on").addClass("now_03");
              }, 1100);

              return false;
            }
          }
        }
      },
    });
  });

  //챌린지 작성완료
  $("#wr_com").click(function () {
    //console.log($('#chall_contents').summernote('code'));
    user_id_secret = $("#user_id_secret").val();
    if ($("#chall_template").val() != "1" && $("#chall_auth").val() != "1") {
      var common_coin = $("#common_coin").text();
      if (common_coin <= 0) {
        alert("현재 사용 가능 코인이 없습니다.\n코인을 확인해 주세요.");
        return false;
      }

      var ncoin = unComma($(".calc_03 strong").text());

      //코인사용안함 체크가 아닐때
      if ($("#not_coin_ico").hasClass("btn_chk_on") != true) {
        if (ncoin < 0) {
          alert(
            "현재 코인이 지급예상 코인보다 많습니다.\n참여횟수 설정 및 보상코인 설정을 확인해주세요."
          );
          return false;
        }
      }
    }

    if ($("#write_title").val() == "") {
      alert("챌린지 제목을 입력해주세요.");
      $(".btn_prev_step_01").trigger("click");
      $("#write_title").focus();
      return false;
    }

    if ($("#cate_title").val() == "") {
      alert("챌린지 카테고리를 선택해주세요.");
      $(".btn_prev_step_01").trigger("click");
      return false;
    }

    var totalcoin = unComma($(".calc_02 strong").text());

    var fdata = new FormData();

    fdata.append("mode", "chall_save");
    fdata.append("temp_save", "0");
    fdata.append("total_coin", totalcoin);

    //선착순 체크
    if ($("#not_count_in").hasClass("btn_limit_on") == true) {
      fdata.append("limit_count", $("#limit_count").val());
    }

    if ($("#chall_idx").val()) {
      fdata.append("chall_idx", $("#chall_idx").val());
    }

    fdata.append("write_type", "3");

    if ($("#cate_title").val()) {
      fdata.append("cate", $("#cate_title").val());
    }

    if ($("#write_title").val()) {
      fdata.append("title", $("#write_title").val());
    }

    if ($("#write_keyword").val()) {
      fdata.append("write_keyword", $("#write_keyword").val());
    }

    if ($("#cha_template").is(":checked") == true) {
      fdata.append("template", "1");
    }

    var chall_contents = $("#chall_contents").summernote("code");
    if (chall_contents) {
      fdata.append("contents", chall_contents);
    }

    if ($("input[id='file_01']").val()) {
      var file_name01 = $("input[id='file_01']").val();

      //파일1 확장자 체크
      if (uploadFile(file_name01) == "ok") {
        console.log(file_name01);
        alert("지원하지않는 파일확장자입니다.");
        $(".btn_prev_step_01").trigger("click");
        return false;
      }

      //파일1 사이즈 체크
      if ($("input[id='file_01']")[0].files[0].size >= "52428800") {
        alert("파일용량이 50M를 초과합니다.\n 파일은 50M 미만으로 올려주세요.");
        return false;
      }
      fdata.append("files[]", $("input[id='file_01']")[0].files[0]);
    }

    if ($("input[id='file_02']").val()) {
      var file_name02 = $("input[id='file_02']").val();

      //파일2 확장자 체크
      if (uploadFile(file_name02) == "ok") {
        alert("지원하지않는 파일확장자입니다.");
        $(".btn_prev_step_01").trigger("click");
        return false;
      }

      //파일2 사이즈 체크
      if ($("input[id='file_02']")[0].files[0].size >= "52428800") {
        alert("파일용량이 50M를 초과합니다.\n 파일은 50M 미만으로 올려주세요.");
        $(".btn_prev_step_01").trigger("click");
        return false;
      }
      fdata.append("files[]", $("input[id='file_02']")[0].files[0]);
    }

    if ($("input[id='file_03']").val()) {
      var file_name03 = $("input[id='file_03']").val();

      //파일3 확장자 체크
      if (uploadFile(file_name03) == "ok") {
        alert("지원하지않는 파일확장자입니다.");
        $(".btn_prev_step_01").trigger("click");
        return false;
      }

      //파일3 사이즈 체크
      if ($("input[id='file_03']")[0].files[0].size >= "52428800") {
        alert("파일용량이 50M를 초과합니다.\n 파일은 50M 미만으로 올려주세요.");
        $(".btn_prev_step_01").trigger("click");
        return false;
      }
      fdata.append("files[]", $("input[id='file_03']")[0].files[0]);
    }

    if ($("#sdate").val()) {
      fdata.append("sdate", $("#sdate").val());
    }

    if ($("#edate").val()) {
      fdata.append("edate", $("#edate").val());
    }

    if ($(".input_count").val()) {
      fdata.append("input_count", $(".input_count").val());
    }

    if ($("#ch_once").hasClass("btn_on") == true) {
      fdata.append("ch_once", "1");
    }

    if ($("#ch_daily").hasClass("btn_on") == true) {
      fdata.append("ch_daily", "1");
    }

    if ($("#ch_holiday").hasClass("btn_chk_on") == true) {
      fdata.append("ch_holiday", "1");
    } else {
      fdata.append("ch_holiday", "0");
    }

    if ($("#not_coin_ico").hasClass("btn_chk_on") == true) {
      fdata.append("ch_not_coin", true);
    }

    //테마추가
    if ($("#chall_thema_chk").val()) {
      fdata.append("chall_thema_chk", $("#chall_thema_chk").val());
    }
    //참여자설정 - 전체
    if (
      $(".rew_cha_setting_user_area button").eq(0).hasClass("btn_on") == true
    ) {
      fdata.append("user", "all");
    } else if (
      $(".rew_cha_setting_user_area button").eq(1).hasClass("btn_on") == true
    ) {
      //챌린지설정 - 일부
      fdata.append("user", "sel");
      fdata.append("chall_user_chk", $("#chall_user_chk").val());
    }

    if ($(".input_coin").val()) {
      fdata.append("input_coin", $(".input_coin").val());
    }

    //템플릿생성
    if (GetCookie("chall_tpl") == "1") {
      fdata.append("chall_tpl", 1);
    } else {
      fdata.append("chall_tpl", "");
    }

    $.ajax({
      type: "POST",
      data: fdata,
      contentType: false,
      processData: false,
      url: "/inc/challenges_process.php",
      success: function (data) {
        console.log(data);
        if (data) {
          tdata = data.split("|");
          if (tdata) {
            var result = tdata[0];
            var temp_idx = tdata[1];

            if (temp_idx) {
              $("#chall_idx").val(temp_idx);
            }
            if (result == "complete") {
              //alert("임시저장 되었습니다.");
              console.log(result);
              $(".rew_cha_step_01").removeClass("step_z");
              $(".rew_cha_step_02").addClass("step_z");
              $(".rew_cha_step_03").addClass("step_z");
              $(".rew_cha_step_01").animate({ left: 100 + "%" }, 700);
              $(".rew_cha_step_02").animate({ left: -100 + "%" }, 700);
              $(".rew_cha_step_03").animate({ left: 0 + "%" }, 700);

              setTimeout(function () {
                $(".rew_cha_step_01").animate({ scrollTop: 0 }, 0);
                $(".rew_cha_step_02").animate({ scrollTop: 0 }, 0);
                $(".rew_cha_step_03").animate({ scrollTop: 0 }, 0);
              }, 200);

              setTimeout(function () {
                $(".tabs_on").addClass("now_03");
              }, 1100);

              return false;
            } else if (result == "usernot") {
              alert("본인 이외 다른 참여자를 추가 선택 해주세요.");
              return false;
            }
          }
        }
      },
    });
  });

  //$(document).on("click", ".list_function_sort_in button:eq(0)", function() {
  //     console.log(99999);
  //});

  //$('#write_keyword').keydown(function() {
  $("#write_title").bind("input keyup", function (e) {
    var rows = $("#write_title").val().split("\n").length;
    var maxRows = 2;
    if (rows > maxRows) {
      alert("챌린지 제목은 " + maxRows + "줄까지만 입력 가능합니다.");
      modifiedText = $("#write_title").val().split("\n").slice(0, maxRows);
      $("#write_title").val(modifiedText.join("\n"));
    }
  });

  //챌린지 메인페이지 - 카테고리선택
  //$(document).on("click", ".btn_sort_on", function() {
  $(document).on("click", "#auth_masage_date", function () {
    ///$(".btn_sort_on").click(function() {
    $(".rew_cha_write_cate").addClass("on");
    $(".list_function_sort").addClass("on");
  });

  //챌린지 메인페이지 - 카테고리선택
  //$(document).on("click", ".btn_sort_on", function() {
  $(document).on("click", "#auth_file_date", function () {
    ///$(".btn_sort_on").click(function() {
    $(".rew_cha_write_cate").addClass("on");
    $(".list_function_sort").addClass("on");
  });

  //챌린지 인증 메시지 날짜검색
  $(document).on("click", "button[id^='comment_reg']", function () {
    var val = $(this).val();
    var list_idx = $(".layer_masage .layer_result_user_in ul li button").val();
    var idx = $("#view_idx").val();
    var fdata = new FormData();

    fdata.append("mode", "auth_masage_list");
    fdata.append("list_idx", list_idx);
    fdata.append("idx", idx);
    fdata.append("user_date", val);

    if (val) {
      //    $("#user_email").val(val);
      $("#user_date").val(val);
    }

    if ($("#input_masage").val()) {
      fdata.append("input_val", $("#input_masage").val());
    }

    $.ajax({
      type: "post",
      data: fdata,
      contentType: false,
      processData: false,
      url: "/inc/process.php",
      success: function (data) {
        console.log("data  :::" + data);
        if (data) {
          $("#masage_zone_list").html(data);
        }
      },
    });
  });

  //챌린지 인증 파일 날짜검색
  $(document).on("click", "button[id^='file_reg']", function () {
    console.log("날짜선택");

    var val = $(this).val();
    var list_idx = $(".layer_result .layer_result_user_in ul li button").val();
    var idx = $("#view_idx").val();
    var fdata = new FormData();

    console.log("idx :: " + idx);
    // console.log(list_idx);
    // console.log(idx);

    fdata.append("mode", "auth_file_list");
    fdata.append("list_idx", list_idx);
    fdata.append("idx", idx);
    fdata.append("user_date", val);

    if (val) {
      //    $("#user_email").val(val);
      $("#user_date").val(val);
    }

    if ($("#input_userfile").val()) {
      fdata.append("input_val", $("#input_userfile").val());
    }

    $.ajax({
      type: "post",
      data: fdata,
      contentType: false,
      processData: false,
      url: "/inc/process.php",
      success: function (data) {
        console.log(data);
        if (data) {
          file_user_list();
          $("#file_zone_list").html(data);
        }
      },
    });
  });

  ///$(".list_function_sort").mouseleave(function() {
  //$(document).on("mouseleave", ".btn_sort_on", function() {
  //    $(".list_function_sort").removeClass("on");
  //});
  //$(".list_function_sort ul li button").click(function() {
  //    $(".list_function_sort").removeClass("on");
  //});

  //챌린지 작성하기(카테고리선택) - 마우스 벗어날때 사라짐
  $(".rew_cha_write_cate").mouseleave(function () {
    $(".rew_cha_write_cate").removeClass("on");
  });

  //챌린지 작성하기(카테고리클릭)
  $(".rew_cha_write_cate .rew_cha_write_cate_in button").click(function () {
    $(".rew_cha_write_cate").addClass("on");
  });

  //챌린지 작성하기(카테고리선택)
  $(".rew_cha_write_cate ul li button").click(function () {
    $(".rew_cha_write_cate").removeClass("on");

    var v = $(this).val();
    if (category_title[v]) {
      var cate_title = category_title[v];
      $("#cate_title").text(cate_title);
      $("#cate_title").val(v);
    }
  });

  //챌린지작성하기
  $(".rew_cha_step_01").scroll(function () {
    var rct = $(".rew_cha_write_type").offset().top;
    if (rct < 175) {
      $(".rew_cha_write_tabs").addClass("pos_fix");
      $(".rew_cha_write_in").addClass("pos_fix");
    } else {
      $(".rew_cha_write_tabs").removeClass("pos_fix");
      $(".rew_cha_write_in").removeClass("pos_fix");
    }
  });
  $(".rew_cha_step_02").scroll(function () {
    var rct = $(".rew_cha_setting_date").offset().top;
    if (rct < 175) {
      $(".rew_cha_write_tabs").addClass("pos_fix");
      $(".rew_cha_write_in").addClass("pos_fix");
    } else {
      $(".rew_cha_write_tabs").removeClass("pos_fix");
      $(".rew_cha_write_in").removeClass("pos_fix");
    }
  });

  $(".rew_cha_comple_box a")
    .eq(1)
    .click(function () {
      var idx = $("#chall_idx").val();
      if (idx) {
        location.href = "/challenge/view.php?idx=" + idx;
        return false;
      }
    });

  //챌린지 참여자 설정 팀별 숨기기/보이기
  $(document).on("click", "#btn_team_toggle", function () {
    $(this).parent().parent("dl").toggleClass("on");
  });

  //챌린지 참여자 설정 팀별 숨기기/보이기
  //$(document).on("click", ".layer_user_info dt button", function() {
  //    $(this).parent().parent("dl").toggleClass("on");
  //});

  //챌린지 참여자설정 - 검색
  //$(".layer_user_search_box input").bind("input keyup", function(e) {
  $("#input_search_chamyeo").bind("input keyup", function (e) {
    var id = $(this).attr("id");
    var input_val = $(this).val();
    if (input_val) {
      if (e.keyCode == 13) {
        //$(".layer_user_search_box button").trigger("click");
        $("#input_search_chamyeo_btn").trigger("click");
        return false;
      }
    } else {
      layer_user_info_list();
      return false;
    }
  });

  //챌린지 참여자설정 - 검색
  //$(".layer_user_search_box input").bind("input keyup", function(e) {
  $("#input_search_chamyeo").bind("input keyup", function (e) {
    var id = $(this).attr("id");
    var input_val = $(this).val();
    if (input_val) {
      if (e.keyCode == 13) {
        //$(".layer_user_search_box button").trigger("click");
        $("#input_search_chamyeo_btn").trigger("click");
        return false;
      }
    } else {
      layer_user_info_list();
      return false;
    }
  });

  //참여자설정(라이브-파티만들기)
  $("#input_todaywork_search").bind("input keyup", function (e) {
    var id = $(this).attr("id");
    var input_val = $(this).val();
    if (input_val) {
      if (e.keyCode == 13) {
        //$(".layer_user_search_box button").trigger("click");
        $("#input_todaywork_search_btn").trigger("click");
        return false;
      }
    } else {
      layer_user_info_list();
      return false;
    }
  });

  //챌린지 검색
  $(document).on("keyup", ".input_search", function (e) {
    var input_val = $(this).val();
    if (input_val) {
      if (e.keyCode == 13) {
        $("#input_search_btn").trigger("click");
        return false;
      }
    } else {
      challenges_ajax_list();
      return false;
    }
  });

  //챌린지 테마 검색 - 작성하기
  $(document).on("input keyup", "#input_thema_search", function (e) {
    var input_val = $(this).val();
    if (input_val) {
      if (e.keyCode == 13) {
        $("#input_thema_search_btn").trigger("click");
        return false;
      }
    } else {
      challenges_thema_list();
      return false;
    }
  });

  //챌린지 테마 전체 리스트 - 검색
  $(document).on("keyup", "#input_search_thema", function () {
    var input_val = $(this).val();
    if (input_val) {
      // console.log(input_val);
      // console.log(e.keyCode);
      if (e.keyCode == 13) {
        $("#input_search_thema_btn").trigger("click");
        return false;
      }
    } else {
      console.log("kep");
      challenges_thema_ajax_list();
      return false;
    }
  });

  //챌린지 참여자리스트
  function layer_user_info_list() {
    var input_val = $(".layer_user_search_box input").val();
    var fdata = new FormData();
    var arr_val = new Array();
    var userchk = 0;

    fdata.append("mode", "chall_user_list");
    fdata.append("layer_user_list", "1");
    fdata.append("user_chk_val", $("#chall_user_chk").val());

    //console.log("chall_user_chk  :: " + $("#chall_user_chk").val());

    if ($("#chall_user_chk").val()) {
      user_chk_val = $("#chall_user_chk").val();
      arr_val = user_chk_val.split(",");
      if (arr_val.length > 0) {
        userchk = arr_val.length;
      } else {
        userchk = 0;
      }
    }

    fdata.append("input_val", input_val);
    $.ajax({
      type: "post",
      data: fdata,
      contentType: false,
      processData: false,
      url: "/inc/process.php",
      success: function (data) {
        //console.log("리스트 시작하기");
        //console.log(data);
        if (data) {
          var tdata = data.split("|");
          if (tdata) {
            var html = tdata[0];
            var totcnt = tdata[1].trim();

            if (totcnt > 0) {
              $("#chall_user_cnt").val(totcnt);
              $(".layer_user_info").empty();

              if (userchk > 0) {
                $("#usercnt").text(
                  "전체 " + totcnt + "명, " + userchk + "명 선택"
                );
              } else {
                $("#usercnt").text("전체 " + totcnt + "명");
              }
              $(".layer_user_info").html(html);
            } else {
              $("#chall_user_chk").val("");
              $("#chall_user_cnt").val(totcnt);
              $(".layer_user_info").empty();
              $("#usercnt").text("전체 " + totcnt + "명");
              $(".layer_user_info").html(html);
            }
          }
        }
      },
    });
  }

  //참여자설정 선택, 팀별선택, 그룹별
  var userchk = 0;
  var arr_val = new Array();
  var arr_del = new Array();
  $(document).on("click", ".layer_user_info dt .btn_team_slc", function () {
    var ddbt = $(this).parent().parent("dl").find("dd button");
    var abt = $(".layer_user_info dd button");

    if ($(".layer_user_slc_list_in").is(":visible") == false) {
      $(".layer_user_slc_list_in").show();
    }

    if ($(this).hasClass("on")) {
      //console.log("해제하기");

      $(this).removeClass("on");
      $(this).parent().parent("dl").find("dd button").removeClass("on"); // 개별 button에 class = on 삭제
      for (var i = 0; i < ddbt.length; i++) {
        arr_del[i] = ddbt.eq(i).val();
        $(".layer_user_slc_list_in #user_" + arr_del[i] + "").remove();
      }
      arr_val = arr_val.filter((item) => !arr_del.includes(item));

      //전체선택
      if ($("#layer_user_all_slc").hasClass("on")) {
        $("#layer_user_all_slc").removeClass("on");
      }

      //설정하기
      if (arr_val.length == 0) {
        if ($("#layer_todaywork_user").hasClass("on")) {
          $("#layer_todaywork_user").removeClass("on");
        }
      }
      if ($(".layer_user_info dd button").hasClass("on")) {
        $(".layer_user_submit").addClass("on");
      } else {
        $(".layer_user_submit").removeClass("on");
      }
    } else {
      //값비우기
      //console.log("선택하기");
      //console.log("arr_val == " + arr_val);

      if (arr_val) {
        for (var i = 0; i < arr_val.length; i++) {
          arr_val.splice($.inArray(arr_val[i], arr_val), 1);
        }
      }

      $(this).addClass("on");
      $(".layer_user_submit").addClass("on");
      $(this).parent().parent("dl").find("dd button").addClass("on");

      for (var i = 0; i < abt.length; i++) {
        abt_val = abt.eq(i).val();

        if (abt.eq(i).hasClass("on")) {
          //본인은 선택에서 제외처리
          if (
            GetCookie("user_id") == abt.eq(i).find(".user_name").attr("value")
          ) {
            abt.eq(i).removeClass("on");
            $(this).removeClass("on");
          } else {
            arr_val.push(abt.eq(i).val());
          }
        }
      }
    }

    //중복제거
    arr_result = str_over_filter(arr_val);
    if (arr_result.length) {
      //console.log("선택자 :::  " + arr_result);
      //console.log("몇명 " + arr_result.length);

      for (var i = 0; i < arr_result.length; i++) {
        if ($("#user_" + arr_result[i]).html() == undefined) {
          var use_img = $(
            ".layer_user_info ul #udd_" + arr_result[i] + " button"
          )
            .find(".user_img")
            .css("background-image")
            .replace(/^url\(['"](.+)['"]\)/, "$1");
          var use_name = $(
            ".layer_user_info ul #udd_" + arr_result[i] + " button"
          )
            .find(".user_name strong")
            .text();
          var use_html =
            '<li id="user_' +
            arr_result[i] +
            '"><div class="user_img" style="background-image:url(' +
            use_img +
            ');"></div><div class="user_name"><strong>' +
            use_name +
            '</strong></div><button class="user_slc_del" value="' +
            arr_result[i] +
            '" title="삭제"><span>삭제</span></button></li>';
          $(".layer_user_slc_list_in ul").append(use_html);
        }
      }

      if ($(".layer_user_box").hasClass("none") == true) {
        $(".layer_user_box").removeClass("none");
      }

      $("#chall_user_chk").val(arr_result);
      $("#usercnt").text(
        "전체 " +
          $("#chall_user_cnt").val() +
          "명, " +
          arr_result.length +
          "명 선택"
      );

      if ($("#layer_todaywork_user").hasClass("on") == false) {
        $("#layer_todaywork_user").addClass("on");
      }

      if ($("#chall_user_cnt").val() == arr_result.length) {
        if ($("#layer_user_all_slc").hasClass("on") == false) {
          $("#layer_user_all_slc").addClass("on");
        }
      }
    } else {
      //$(this).parent("li").remove();

      if ($(".layer_user_box").hasClass("none") == false) {
        $(".layer_user_box").addClass("none");
      }

      $("#chall_user_chk").val("");
      $("#usercnt").text("전체 " + $("#chall_user_cnt").val() + "명");
    }
  });

  var che_cnt = 0;
  //참여자 설정 - 일부 선택
  $(document).on("click", ".layer_user_info dd button", function () {
    console.log("work");
    var idx = $(this).val();
    // console.log(idx);
    var dd_length = $(".layer_user_info dd button.on").length;
    var dt_length = $(".user_slc_del").length;

    if ($(this).hasClass("on") == true) {
      dd_length = dd_length - 1;
      dt_length = dt_length - 1;
    } else {
      dd_length = dd_length + 1;
      dt_length = dt_length + 1;
    }
    // console.log(dd_length+"|"+dt_length);
    if (dd_length > 0) {
      $(".layer_user_box").removeClass("none");
    } else if (dd_length == 0) {
      if (dt_length == 0) {
        $(".layer_user_box").addClass("none");
      }
    }

    $(".layer_user_submit").addClass("on");

    if ($(".layer_user_slc_list_in").is(":visible") == false) {
      $(".layer_user_slc_list_in").show();
      $(".layer_user_box").removeClass("none");
    }

    var input_val = $(".layer_user_search_box input").val();
    var member_total_cnt = $("#chall_user_cnt").val();
    var user_val = $(this).val();
    var comma = "";
    var input_usrchk = "";
    $(this).toggleClass("on");

    //검색단어 있을경우
    if (input_val) {
      var arr_val = new Array();
      var user_chk_val = $("#chall_user_chk").val();
      if (user_chk_val) {
        arr_val = user_chk_val.split(",");
      }
      /*var user_select = new Array();
      var arr_val = new Array();
      var user_info_list_cnt = $(".layer_user_info dd button").length;
      k = 0;
      for (var i = 0; i < user_info_list_cnt; i++) {
        if ($(".layer_user_info dd button").eq(i).hasClass("on") == true) {
          if ($(".layer_user_info dd button").eq(i).val()) {
            user_select[k] = $(".layer_user_info dd button").eq(i).val();
            arr_val.push(user_select[k]);
            k++;
          }
        }
      }*/

      var search_list_cnt = $(".layer_user_info dd button").length;
      search_check = 0;
      for (var i = 0; i < search_list_cnt; i++) {
        if ($(".layer_user_info dd button").eq(i).hasClass("on") == true) {
          search_check++;
        }
      }

      if (search_check >= 0) {
        $("#usercnt").text("전체 " + member_total_cnt + "명");
        if (search_check == 0) {
          $("#usercnt").text($("#usercnt").text());
        } else {
          $("#usercnt").text(
            $("#usercnt").text() + ", " + search_check + "명 선택"
          );
        }

        if ($(this).hasClass("on") == true) {
          if (user_val) {
            arr_val.push(user_val);
          }

          var use_img = $(this)
            .find(".user_img")
            .css("background-image")
            .replace(/^url\(['"](.+)['"]\)/, "$1");
          var use_name = $(this).find(".user_name strong").text();
          var use_html =
            '<li id="user_' +
            $(this).val() +
            '"><div class="user_img" style="background-image:url(' +
            use_img +
            ');"></div><div class="user_name"><strong>' +
            use_name +
            '</strong></div><button class="user_slc_del" value="' +
            user_val +
            '" title="삭제"><span>삭제</span></button></li>';
          $(".layer_user_slc_list_in ul").append(use_html);
        } else {
          $(".layer_user_slc_list_in #user_" + user_val + "").remove();
          arr_val.splice($.inArray(user_val, arr_val), 1);
        }

        for (var i = 0; i < arr_val.length; i++) {
          if (i > 0) {
            comma = ",";
          } else {
            comma = "";
          }
          input_usrchk += comma + arr_val[i];
        }

        //console.log(" input_usrchk ::: " + input_usrchk);

        if (input_usrchk) {
          $("#chall_user_chk").val(input_usrchk);

          //전체선택
          if (member_total_cnt == arr_val.length) {
            if ($("#layer_user_all_slc").hasClass("on") == false) {
              $("#layer_user_all_slc").addClass("on");
            }
          } else {
            if ($("#layer_user_all_slc").hasClass("on") == true) {
              $("#layer_user_all_slc").removeClass("on");
            }
          }

          //설정하기버튼
          if ($(".layer_user_submit").hasClass("on") == false) {
            $(".layer_user_submit").addClass("on");
          }
        } else {
          $("#chall_user_chk").val("");

          if ($(".layer_user_info dd button").hasClass("on") == true) {
            $(".layer_user_submit").removeClass("on");
          }

          //설정하기버튼
          if ($(".layer_user_submit").hasClass("on") == true) {
            $(".layer_user_submit").removeClass("on");
          }
        }
      }
    } else {
      if ($(this).hasClass("on") == true) {
        var user_select = new Array();
        var arr_val = new Array();
        var user_info_list_cnt = $(".layer_user_info dd button").length;
        k = 0;
        for (var i = 0; i < user_info_list_cnt; i++) {
          if ($(".layer_user_info dd button").eq(i).hasClass("on") == true) {
            if ($(".layer_user_info dd button").eq(i).val()) {
              user_select[k] = $(".layer_user_info dd button").eq(i).val();
              arr_val.push(user_select[k]);
              k++;
            }
          }
        }
        che_cnt = k;

        if (user_select) {
          var combineText = user_select.join(",");
          $("#chall_user_chk").val(combineText);
        }

        //arr_val.push(user_val);
        //if (user_val) {
        //    arr_val.push(user_val);
        //}

        var use_img = $(this)
          .find(".user_img")
          .css("background-image")
          .replace(/^url\(['"](.+)['"]\)/, "$1");
        var use_name = $(this).find(".user_name strong").text();
        var use_html =
          '<li id="user_' +
          $(this).val() +
          '"><div class="user_img" style="background-image:url(' +
          use_img +
          ');"></div><div class="user_name"><strong>' +
          use_name +
          '</strong></div><button class="user_slc_del" value="' +
          user_val +
          '" title="삭제"><span>삭제</span></button></li>';
        $(".layer_user_slc_list_in ul").append(use_html);
      } else {
        var user_select = new Array();
        var arr_val = new Array();
        var user_info_list_cnt = $(".layer_user_info dd button").length;
        $(".layer_user_slc_list_in #user_" + user_val + "").remove();

        k = 0;
        for (var i = 0; i < user_info_list_cnt; i++) {
          if ($(".layer_user_info dd button").eq(i).hasClass("on") == true) {
            if ($(".layer_user_info dd button").eq(i).val()) {
              user_select[k] = $(".layer_user_info dd button").eq(i).val();
              arr_val.push(user_select[k]);
              k++;
            }
          }
        }

        che_cnt = che_cnt - 1;

        if (user_select) {
          var combineText = user_select.join(",");
          $("#chall_user_chk").val(combineText);
        }

        if (k == 0) {
          //    arr_val = new Array();
        }

        //console.log("선택된사람 : " + $("#chall_user_chk").val());
        if (arr_val) {
          //    arr_val.splice($.inArray(user_val, arr_val), 1);
        }
      }

      //console.log("arr :: " + arr_val);
      arr_val = str_over_filter(arr_val);
      for (var i = 0; i < arr_val.length; i++) {
        if (i > 0) {
          comma = ",";
        } else {
          comma = "";
        }
        input_usrchk += comma + arr_val[i];
      }

      if (arr_val.length > 0) {
        userchk = arr_val.length;
      } else {
        userchk = 0;
      }

      if (input_usrchk) {
        $("#chall_user_chk").val(input_usrchk);
      } else {
        $("#chall_user_chk").val("");
      }

      if ($(".layer_user_info dd button").hasClass("on")) {
        $(".layer_user_submit").addClass("on");
      } else {
        $(".layer_user_submit").removeClass("on");
      }

      var team_id = $(this).attr("id");
      var team_len = $("button[id=" + team_id).length;
      var team_no = team_id.replace("team_", "");
      var team_bt_cnt = 0;
      var member_total_cnt = $("#chall_user_cnt").val();

      //console.log("team_id " + team_id);

      for (i = 0; i < team_len; i++) {
        if (
          $(".layer_user_info dd button[id=" + team_id)
            .eq(i)
            .hasClass("on")
        ) {
          team_bt_cnt++;
        }
      }

      if (team_bt_cnt == team_len) {
        if ($("#btn_team_slc_" + team_no).hasClass("on") == false) {
          $("#btn_team_slc_" + team_no).addClass("on");
        }
      } else {
        $("#btn_team_slc_" + team_no).removeClass("on");
      }

      if (userchk > 0) {
        if ($(".layer_user_box").hasClass("none") == true) {
          $(".layer_user_box").removeClass("none");
        }

        $("#usercnt").text("전체 " + member_total_cnt + "명");
        // 참여자 업카운트
        $("#usercnt").text($("#usercnt").text() + ", " + userchk + "명 선택");

        if (member_total_cnt == userchk) {
          if ($("#layer_user_all_slc").hasClass("on") == false) {
            $("#layer_user_all_slc").addClass("on");
          }
        } else {
          if ($("#layer_user_all_slc").hasClass("on") == true) {
            $("#layer_user_all_slc").removeClass("on");
          }
        }
      } else {
        if ($(".layer_user_box").hasClass("none") == false) {
          $(".layer_user_box").addClass("none");
        }
        $("#usercnt").text("전체 " + member_total_cnt + "명");
      }
    }
  });

  //참여자 선택삭제(X버튼클릭)
  $(document).on("click", ".layer_user_slc_list .user_slc_del", function () {
    // console.log($(this).parent("li").attr("id"));
    var slc_del_id = $(this).parent("li").attr("id");
    var slc_val = slc_del_id.replace("user_", "");
    var member_total_cnt = $("#chall_user_cnt").val();
    //console.log("ID ::: " + slc_del_id);
    //console.log("slc_val :: " + slc_val);

    che_cnt = che_cnt - 1;

    if (slc_val) {
      var user_chk_val = $("#chall_user_chk").val();
      arr_val = user_chk_val.split(",");

      // if ($(".layer_user_info ul #udd_" + slc_val + " button").hasClass("on")) {
      if (arr_val) {
        $(".layer_user_info ul #udd_" + slc_val + " button").removeClass("on");
        arr_val = arr_val.filter((item) => !slc_val.includes(item));
        $("#chall_user_chk").val(arr_val);

        if (!$(".layer_user_search_box input").val()) {
          var bid = $(".layer_user_info ul #udd_" + slc_val + " button").attr(
            "id"
          );

          var team_len = $("button[id=" + bid).length;
          var team_no = bid.replace("team_", "");
          // var team_bt_cnt = 0;
          // for (i = 0; i < team_len; i++) {
          //   if (
          //     $(".layer_user_info dd button[id=" + bid)
          //       .eq(i)
          //       .hasClass("on")
          //   ) {
          //     team_bt_cnt++;
          //   }
          // }

          if (che_cnt == team_len) {
            if ($("#btn_team_slc_" + team_no).hasClass("on") == false) {
              $("#btn_team_slc_" + team_no).addClass("on");
            }
          } else {
            $("#btn_team_slc_" + team_no).removeClass("on");
          }
        }

        if (arr_val.length > 0) {
          userchk = arr_val.length;
        } else {
          userchk = 0;
        }

        if (userchk > 0) {
          if ($(".layer_user_box").hasClass("none") == true) {
            $(".layer_user_box").removeClass("none");
          }

          //참여자설정, 전체선택
          if ($(".layer_user_info ul dd button").length == userchk) {
            if ($("#layer_user_all_slc").hasClass("on") == false) {
              $("#layer_user_all_slc").addClass("on");
            }
          } else {
            if ($("#layer_user_all_slc").hasClass("on") == true) {
              $("#layer_user_all_slc").removeClass("on");
            }
          }

          $("#usercnt").text(
            "전체 " + $(".layer_user_info ul dd button").length + "명"
          );
          // $("#usercnt").text($("#usercnt").text() + ", " + userchk + "명 선택");
        } else {
          if ($(".layer_user_box").hasClass("none") == false) {
            $(".layer_user_box").addClass("none");
          }

          if ($(".layer_user_submit").hasClass("on") == true) {
            $(".layer_user_submit").removeClass("on");
          }

          $("#usercnt").text("전체 " + member_total_cnt + "명");
        }
      }
    }

    if ($(this).val()) {
      console.log($(this).val());
      $("#user_" + $(this).val()).remove();
      $(this).parent("li").remove();
    }

    if ($(".lm_area .layer_user_slc_list ul li").length > 5) {
      $(".lm_area .layer_user_slc_list").addClass("over_4");
    } else if ($(".lm_area .layer_user_slc_list ul li").length < 3) {
      $(".lm_area .layer_user_slc_list").removeClass("over_4");
      //$(".lm_area .user_slc_del").hide();
    } else {
      $(".lm_area .layer_user_slc_list").removeClass("over_4");
    }
  });

  //마우스힐
  $(".layer_user_slc_list_in ul").mousewheel(function (event, delta) {
    this.scrollLeft -= delta * 30;
    event.preventDefault();
  });

  //참여자 전체선택
  $(document).on("click", "#layer_user_all_slc", function () {
    var abt = $(".layer_user_info dd button");
    var ddbt = $(".layer_user_info dt .btn_team_slc");
    console.log(abt);

    if ($(this).hasClass("on")) {
      $(this).removeClass("on");

      for (var i = 0; i < abt.length; i++) {
        if (abt.eq(i).hasClass("on") == true) {
          abt.eq(i).removeClass("on");
          arr_val.splice($.inArray(abt.eq(i).val(), arr_val), 1);
          $(".layer_user_slc_list_in #user_" + abt.eq(i).val() + "").remove();
        }
      }

      for (var i = 0; i < ddbt.length; i++) {
        if (ddbt.eq(i).hasClass("on") == true) {
          ddbt.eq(i).removeClass("on");
        }
      }

      if ($(".layer_user_box").hasClass("none") == false) {
        $(".layer_user_box").addClass("none");
      }

      $("#chall_user_chk").val("");
      $("#usercnt").text("전체 " + $("#chall_user_cnt").val() + "명");

      $(".layer_user_submit").removeClass("on");
    } else {
      $(this).addClass("on");
      for (var i = 0; i < abt.length; i++) {
        //abt_val = abt.eq(i).val();
        if (abt.eq(i).hasClass("on") == false) {
          //챌린지에서는 모두 선택
          if ($(location).attr("pathname").indexOf("/challenge/") == 0) {
            abt.eq(i).addClass("on");
            arr_val.push(abt.eq(i).val());
          } else {
            //본인제외
            if (
              GetCookie("user_id") == abt.eq(i).find(".user_name").attr("value")
            ) {
              abt.eq(i).removeClass("on");
            } else {
              abt.eq(i).addClass("on");
              arr_val.push(abt.eq(i).val());
              console.log(abt.eq(i).val());
            }
          }
        }
      }

      arr_result = str_over_filter(arr_val);

      console.log("전체 선택 :::::: " + arr_result);

      // for (var i = 0; i < arr_result.length; i++) {
      //   if ($("#user_" + arr_result[i]).html() == undefined) {
      //     //console.log(arr_result[i]);
      //     var use_img = $(
      //       ".layer_user_info ul #udd_" + arr_result[i] + " button"
      //     )
      //       .find(".user_img")
      //       .css("background-image")
      //       .replace(/^url\(['"](.+)['"]\)/, "$1");
      //     var use_name = $(
      //       ".layer_user_info ul #udd_" + arr_result[i] + " button"
      //     )
      //       .find(".user_name strong")
      //       .text();
      //     var use_html =
      //       '<li id="user_' +
      //       arr_result[i] +
      //       '"><div class="user_img" style="background-image:url(' +
      //       use_img +
      //       ');"></div><div class="user_name"><strong>' +
      //       use_name +
      //       '</strong></div><button class="user_slc_del" title="삭제" value="' +
      //       arr_result[i] +
      //       '"><span>삭제</span></button></li>';
      //     $(".layer_user_slc_list_in ul").append(use_html);
      //   }
      // }

      // if ($(".layer_user_box").hasClass("none") == true) {
      //   $(".layer_user_box").removeClass("none");
      // }

      // for (var i = 0; i < ddbt.length; i++) {
      //   if (ddbt.eq(i).hasClass("on") == false) {
      //     ddbt.eq(i).addClass("on");
      //   }
      // }

      if (arr_result) {
        $("#chall_user_chk").val(arr_result);
        $("#usercnt").text(
          "전체 " +
            $("#chall_user_cnt").val() +
            "명, " +
            arr_result.length +
            "명 선택"
        );
        $(".layer_user_submit").addClass("on");
      }
    }
  });

  //참여자 설정하기 버튼
  $(document).on("click", "#layer_todaywork_user", function () {
    //console.log("설정하기");
    //설정하기 class on 상태일때
    if ($(".layer_user_submit").hasClass("on") == true) {
      //console.log("설정하기");
      var fdata = new FormData();
      fdata.append("mode", "project_user_create");
      fdata.append("user_chk_val", $("#chall_user_chk").val());

      $.ajax({
        type: "post",
        data: fdata,
        contentType: false,
        processData: false,
        url: "/inc/lives_process.php",
        success: function (data) {
          console.log(data);
          if (data) {
            if ($("#chall_user_chk").val()) {
              var user_chk_val = $("#chall_user_chk").val();
              arr_val = user_chk_val.split(",");
              if (arr_val.length > 0) {
                userchk = arr_val.length;
              } else {
                userchk = 0;
              }
            }

            if (userchk > 4) {
              if (
                $(".lm_area .layer_user_slc_list").hasClass("over_4") == false
              ) {
                $(".lm_area .layer_user_slc_list").addClass("over_4");
              }
            } else {
              $(".lm_area layer_user_slc_list").removeClass("over_4");
            }

            $("#layer_user_slc_list_in ul").html(data);
            $(".layer_user").hide();
            $("#layer_make").show();
          }
        },
      });
    }
  });

  //파티만들기 닫기버튼
  $(".lm_close").click(function () {
    var abt = $(".layer_user_info dd button");
    var ddbt = $(".layer_user_info dt .btn_team_slc");

    //layer_user_info_list();
    //$("#select_user_cnt").text("");
    //$(".title_desc_01").text("");

    for (var i = 0; i < abt.length; i++) {
      if (abt.eq(i).hasClass("on") == true) {
        abt.eq(i).removeClass("on");
        arr_val.splice($.inArray(abt.eq(i).val(), arr_val), 1);
        $(".layer_user_slc_list_in #user_" + abt.eq(i).val() + "").remove();
      }
    }

    for (var i = 0; i < ddbt.length; i++) {
      if (ddbt.eq(i).hasClass("on") == true) {
        ddbt.eq(i).removeClass("on");
      }
    }

    //참여자 선택 해제
    if ($(".layer_user_box").hasClass("none") == false) {
      $(".layer_user_box").addClass("none");
    }

    //참여자설정, 전체선택
    if ($("#layer_user_all_slc").hasClass("on")) {
      $("#layer_user_all_slc").removeClass("on");
    }

    //설정하기
    if ($("#layer_todaywork_user").hasClass("on")) {
      $("#layer_todaywork_user").removeClass("on");
    }

    $("#usercnt").text("전체 " + abt.length + "명");
    $(".layer_user_search_box input").val("");
    $(".layer_user_slc_list_in ul li").html("");
    $("#chall_user_chk").val("");
    $("#textarea_lm").val("");

    layer_user_info_list();

    $("#layer_make").hide();
  });

  //파티만들기 - 함께할업무를 작성해주세요.
  $("#textarea_lm").bind("input", function (event) {
    var val = $(this).val();
    if (val) {
      if ($("#lm_bottom").hasClass("btn_off") == true) {
        $("#lm_bottom").removeClass("btn_off");
        $("#lm_bottom").addClass("btn_on");
        //$("#textarea_memo").val(val);
      }
    } else {
      $("#lm_bottom").removeClass("btn_on");
      $("#lm_bottom").addClass("btn_off");
    }
  });

  //파티만들기 - 확인버튼
  $(document).on("click", "#lm_bottom", function () {
    if (!$("#textarea_lm").val()) {
      alert("함께 할 업무를 작성해주세요.");
      $("#textarea_lm").focus();
      return false;
    }

    console.log("프로젝트 생성");

    ltb_t = confirm("파티를 생성하시겠습니까?");
    if (ltb_t == true) {
      var fdata = new FormData();
      fdata.append("mode", "project_create");
      fdata.append("user_chk_val", $("#chall_user_chk").val());
      fdata.append("textarea_lm", $("#textarea_lm").val());

      $.ajax({
        type: "post",
        data: fdata,
        contentType: false,
        processData: false,
        url: "/inc/lives_process.php",
        beforeSend: function () {
          $(".rewardy_loading_01").css("display", "block");
        },
        complete: function () {
          $(".rewardy_loading_01").css("display", "none");
        },
        success: function (data) {
          console.log(data);

          if (data) {
            var tdata = data.split("|");
            if (tdata) {
              var result = tdata[0];
              var html = tdata[1];
              if (result == "complete") {
                var ldl_length =
                  Math.abs($(".rew_mypage_section em:eq(0)").text()) + 1;

                $(".rew_mypage_section em:eq(0)").text(ldl_length);
                $("#layer_team").hide();
                $(".ldl_in").prepend(html);
                $("#layer_make").hide();

                //파티경우
                if ($(location).attr("pathname").indexOf("/party/") == 0) {
                  project_ajax_list();
                }
              } else {
                alert(result);
                $("#textarea_lm").focus();
                return false;
              }
            }

            //$("#layer_user_slc_list_in ul").html(data);
            //$(".layer_user").hide();
            //$("#layer_make").show();
          }
        },
      });
    }
  });

  //챌린지 참여자설정 검색버튼
  //$(".layer_user_search_box button").click(function() {
  $("#input_search_chamyeo_btn,#input_todaywork_search_btn").click(function () {
    if ($(".layer_user_search_box input").val() == "") {
      alert("이름, 부서명을 입력하세요.");
      return false;
    } else {
      var fdata = new FormData();
      var input_val = $(".layer_user_search_box input").val();

      fdata.append("mode", "chall_chamyeo_search");
      fdata.append("input_val", input_val);
      fdata.append("user_chk_val", $("#chall_user_chk").val());

      $.ajax({
        type: "post",
        data: fdata,
        contentType: false,
        processData: false,
        url: "/inc/process.php",
        success: function (data) {
          console.log(data);

          var tdata = data.split("|");
          var search_chk;
          if (tdata) {
            var html = tdata[0];
            var totcnt = tdata[1].trim();
            if (tdata[2]) {
              search_chk = tdata[2].trim();
            }

            if (totcnt > 0) {
              if ($("#chall_user_chk").val()) {
                var user_chk_val = $("#chall_user_chk").val();
                arr_val = user_chk_val.split(",");
                if (arr_val.length > 0) {
                  userchk = arr_val.length;
                } else {
                  userchk = 0;
                }
              }

              $("#chall_user_cnt").val(totcnt);
              $(".layer_user_info").empty();

              if (search_chk > 0) {
                $("#usercnt").text("전체 " + totcnt + "명");
                $("#usercnt").text(
                  $("#usercnt").text() + ", " + search_chk + "명 선택"
                );
              } else {
                $("#usercnt").text("전체 " + totcnt + "명");
              }

              $(".layer_user_info").html(html);
            } else {
              //$("#chall_user_chk").val('');
              $("#chall_user_cnt").val("");
              $(".layer_user_info").empty();
              $("#usercnt").text("전체 " + totcnt + "명");
              $(".layer_user_info").html(html);
            }
          }
        },
      });
    }
  });

  //챌린지 참여, 인증 메시지 검색버튼
  //$(".layer_result_search_box button #masage_search").click(function() {
  $("#masage_search_bt").click(function () {
    //console.log("검색버튼");
    //console.log(" 검색어 : " + $("#input_masage").val());

    if ($("#input_masage").val() == "") {
      alert("이름, 부서명을 입력하세요.");
      $("#input_masage").focus();
      return false;
    } else {
      var fdata = new FormData();
      var input_val = $("#input_masage").val();

      var idx = $("#view_idx").val();

      fdata.append("mode", "chall_masage_user_search");
      fdata.append("input_val", input_val);
      fdata.append("idx", idx);

      $.ajax({
        type: "post",
        data: fdata,
        contentType: false,
        processData: false,
        url: "/inc/process.php",
        success: function (data) {
          console.log(data);
          if (data) {
            $(".layer_result_user .layer_result_user_in ul").html(data);
          }
        },
      });
    }
  });

  //챌린지 참여, 인증 파일 검색버튼
  $("#file_search_bt").click(function () {
    if ($("#input_userfile").val() == "") {
      alert("이름, 부서명을 입력하세요.");
      $("#input_userfile").focus();
      return false;
    } else {
      var fdata = new FormData();
      var input_val = $("#input_userfile").val();

      var idx = $("#view_idx").val();

      fdata.append("mode", "file_user_list");
      fdata.append("input_val", input_val);
      fdata.append("idx", idx);

      $.ajax({
        type: "post",
        data: fdata,
        contentType: false,
        processData: false,
        url: "/inc/process.php",
        success: function (data) {
          console.log(data);
          if (data) {
            $(".layer_result_user .layer_result_user_in ul").html(data);
          }
        },
      });
    }
  });

  //챌린지 참여, 혼합형 검색버튼
  $("#mix_search_bt").click(function () {
    if ($("#input_mix").val() == "") {
      alert("이름, 부서명을 입력하세요.");
      $("#input_mix").focus();
      return false;
    } else {
      var fdata = new FormData();
      var input_val = $("#input_mix").val();

      var idx = $("#view_idx").val();

      fdata.append("mode", "mix_user_list");
      fdata.append("input_val", input_val);
      fdata.append("idx", idx);
      console.log(idx);
      console.log(input_val);
      $.ajax({
        type: "post",
        data: fdata,
        contentType: false,
        processData: false,
        url: "/inc/process.php",
        success: function (data) {
          console.log(data);
          if (data) {
            $(".layer_result_user .layer_result_user_in ul").html(data);
          }
        },
      });
    }
  });

  //챌린지참여 인증 메시지 검색어
  $("#input_masage").bind("input keyup", function (e) {
    var id = $(this).attr("id");
    var input_val = $(this).val();
    if (input_val) {
      if (e.keyCode == 13) {
        $("#masage_search_bt").trigger("click");
        return false;
      }
    } else {
      masage_user_list();
      return false;
    }
  });

  //챌린지참여 인증 파일 검색어
  $("#input_userfile").bind("input keyup", function (e) {
    var id = $(this).attr("id");
    var input_val = $(this).val();
    if (input_val) {
      if (e.keyCode == 13) {
        $("#file_search_bt").trigger("click");
        return false;
      }
    } else {
      file_user_list();
      return false;
    }
  });

  //챌린지 혼합형 이름, 부서명 검색
  $("#input_mix").bind("input keyup", function (e) {
    var id = $(this).attr("id");
    var input_val = $(this).val();
    if (input_val) {
      if (e.keyCode == 13) {
        $("#mix_search_bt").trigger("click");
        return false;
      }
    } else {
      mix_user_list();
      return false;
    }
  });

  //스크롤감지
  $(window).bind("mousewheel", function (event) {
    //파일형
    if ($(".list_area_in").position()) {
      var rct = $(".list_area_in").position().top;
      if (rct < 59) {
        $(".layer_result_right").addClass("pos_fix");
      } else {
        $(".layer_result_right").removeClass("pos_fix");
      }
    }

    //메시지형 스크롤 감지
    if ($(".masage_zone_in").position()) {
      var rct = $(".masage_zone_in").position().top;
      if (rct < 59) {
        $(".layer_result_right").addClass("pos_fix");
      } else {
        $(".layer_result_right").removeClass("pos_fix");
      }
    }

    //혼합형
    if ($("#mix_zone_list .mix_zone_in").position()) {
      var rct = $(".mix_zone_in").position().top;
      if (rct < 59) {
        $(".layer_result_right").addClass("pos_fix");
      } else {
        $(".layer_result_right").removeClass("pos_fix");
      }
    }

    //챌린지 테마 스크롤 위치

    if ($("#rew_conts_scroll_04").position()) {
      //$("#rew_conts_scroll_04").scroll(function() {
      //var rct = $(".rew_conts_scroll_04").position().top;
      var rct = $(".rew_cha_list").position().top;
      //console.log(rct);
      //console.log(Math.round(rct));

      //console.log("테마1 ::" + rct);
      if (rct < 0) {
        $(".rew_cha_list_func").addClass("pos_fix");
      } else {
        $(".rew_cha_list_func").removeClass("pos_fix");
      }
    }

    if ($("#rew_conts_scroll_04_list").position()) {
      //$("#rew_conts_scroll_04").scroll(function() {
      var rct = $(".rew_cha_list").position().top;
      //console.log("테마2 ::" + rct);
      if (rct < 0) {
        $(".rew_cha_list_func").addClass("pos_fix");
      } else {
        $(".rew_cha_list_func").removeClass("pos_fix");
      }

      /*if (rct < 135) {
          $(".rew_cha_list_func").addClass("pos_fix");
        } else {
          console.log(3333333);
          $(".rew_cha_list_func").removeClass("pos_fix");
        }*/
    }
  });

  //보고하기 취소
  $(document).on("click", "#layer_report_cancel", function () {
    //선택된 내용 모두 제거 처리
    var abt = $(".layer_user_info dd button");
    for (var i = 0; i < abt.length; i++) {
      if (abt.eq(i).hasClass("on") == true) {
        abt.eq(i).removeClass("on");
        //arr_val.splice($.inArray(abt.eq(i).val(), arr_val), 1);
        $(".layer_user_slc_list_in #user_" + abt.eq(i).val() + "").remove();
      }
    }

    $("#chall_user_chk").val("");
    $("#usercnt").text("전체 " + $("#chall_user_cnt").val() + "명");

    $(".layer_user_submit").attr("id", "layer_todaywork_user");
    $(".layer_user_submit span").text("설정하기");
    $(".layer_user").hide();
  });

  //챌린지 참여자설정 취소
  $(document).on("click", ".layer_user_cancel", function () {
    arr_val = new Array();
    remo_arr = new Array();
    $remo = $("#chall_user_chk").val();
    $(".layer_user_submit").removeClass("on");
    $remo_arr = $remo.split(",");

    for (var i = 0; i < $remo_arr.length; i++) {
      $(".layer_user_slc_list_in #user_" + $remo_arr[i] + "").remove();
    }

    $(".layer_user_search_box input").val("");
    $("#chall_user_chk").val("");

    layer_user_info_list();

    $("#select_user_cnt").text("");
    $(".title_desc_01").text("");

    if (
      $(".rew_cha_setting_user_area button").eq(1).hasClass("btn_on") == true
    ) {
      $(".rew_cha_setting_user_area button").eq(1).removeClass("btn_on");
      $(".rew_cha_setting_user_area button").eq(1).addClass("btn_off");
      $(".rew_cha_setting_user_area button").eq(0).removeClass("btn_off");
      $(".rew_cha_setting_user_area button").eq(0).addClass("btn_on");
    }

    var abt = $(".layer_user_info dd button");
    var ddbt = $(".layer_user_info dt .btn_team_slc");
    for (var i = 0; i < abt.length; i++) {
      if (abt.eq(i).hasClass("on") == true) {
        abt.eq(i).removeClass("on");
        arr_val.splice($.inArray(abt.eq(i).val(), arr_val), 1);
        $(".layer_user_slc_list_in #user_" + abt.eq(i).val() + "").remove();
      }
    }

    for (var i = 0; i < ddbt.length; i++) {
      if (ddbt.eq(i).hasClass("on") == true) {
        ddbt.eq(i).removeClass("on");
      }
    }

    if ($(".layer_user_box").hasClass("none") == false) {
      $(".layer_user_box").addClass("none");
    }

    /*$("#chall_user_chk").val("");
      $("#usercnt").text("전체 " + $("#chall_user_cnt").val() + "명");

      $(".layer_user_submit").removeClass("on");
      */

    //참여자 선택 해제
    if ($(".layer_user_box").hasClass("none") == false) {
      $(".layer_user_box").addClass("none");
    }

    //참여자설정, 전체선택
    if ($("#layer_user_all_slc").hasClass("on")) {
      $("#layer_user_all_slc").removeClass("on");
    }

    //설정하기
    if ($("#layer_todaywork_user").hasClass("on")) {
      $("#layer_todaywork_user").removeClass("on");
    }

    if ($("#tdw_write_user_desc").is(":visible") == true) {
      $("#tdw_write_user_desc").hide();
    }
    $(".layer_user").hide();
  });

  //챌린지 참여하기
  $("#layer_challenges_user").click(function () {
    if ($(this).hasClass("on")) {
      if ($("#chall_user_chk").val()) {
        var input_usrchk = "";
        user_chk_val = $("#chall_user_chk").val();
        if (user_chk_val) {
          arr_val = user_chk_val.split(",");
          for (var i = 0; i < arr_val.length; i++) {
            if (i > 0) {
              comma = ",";
            } else {
              comma = "";
            }
            input_usrchk += comma + arr_val[i];
          }

          if (input_usrchk) {
            $("#chall_user_chk").val(input_usrchk);

            $("#select_user_cnt").text(
              "전체 " + member_total_cnt + "명, " + arr_val.length + "명 선택"
            );
          } else {
            $("#chall_user_chk").val("");
            $("#select_user_cnt").text("");
          }
        }
      }

      if ($(".layer_user_search_box input").val()) {
        layer_user_info_list();
        $(".layer_user").hide();
      } else {
        //오늘업무 경우
        if ($(location).attr("pathname").indexOf("/todaywork/") == 0) {
          //    todayworks_user_name();
        }
        $(".layer_user").hide();
      }

      $("#rew_cha_limit_cnt #limit_count").val(arr_val.length);
      // $("#rew_cha_limit_cnt #btn_limit_minus").removeClass("coin_limit");

      challenges_reward_coin();
    }
  });

  //챌린지 참여자 전체선택
  $(".rew_cha_setting_user_area button")
    .eq(0)
    .click(function () {
      arr_val = new Array();
      $(".layer_user_search_box input").val("");
      $("#chall_user_chk").val("");
      $("#usercnt").text("전체 " + member_total_cnt + "명");

      if (
        $(".rew_cha_setting_user_area button").eq(1).hasClass("btn_on") == true
      ) {
        $(".rew_cha_setting_user_area button").eq(1).removeClass("btn_on");
        $(".rew_cha_setting_user_area button").eq(1).addClass("btn_off");

        $(".rew_cha_setting_user_area button").eq(0).removeClass("btn_off");

        $(".rew_cha_setting_user_area button").eq(0).addClass("btn_on");

        if ($("#select_user_cnt").text()) {
          if (
            $(".rew_cha_setting_user_area button").eq(0).hasClass("btn_on") ==
            true
          ) {
            $("#select_user_cnt").text("전체 " + member_total_cnt + "명 선택");
            //$("#select_user_cnt").text("");
          } else {
            $("#select_user_cnt").text("");
          }
        }
      } else {
        if (
          $(".rew_cha_setting_user_area button").eq(0).hasClass("btn_on") ==
          true
        ) {
          $("#select_user_cnt").text("전체 " + member_total_cnt + "명 선택");
        }
      }

      challenges_reward_coin();
    });

  //챌린지 참여자 일부 선택
  $("#open_layer_user").click(function () {
    $(".layer_user").show();
    var search_list_cnt = $(".layer_user_info dd button").length;
    $("#chall_user_chk").val("");
    $(".rew_cha_setting_user_area button").eq(0).removeClass("btn_on");
    $(".rew_cha_setting_user_area button").eq(0).addClass("btn_off");
    $(".rew_cha_setting_user_area button").eq(1).addClass("btn_on");

    $("#layer_test_02").hide();
    $("#layer_test_03").hide();
    $("#layer_test_01").show();
    $(".layer_user_info dl").addClass("on");
    if (search_list_cnt == 0) {
      $(".layer_user_submit").removeClass("on");
    }
    $(".layer_user_info").animate({ scrollTop: 0 }, 0);

    if ($("#chall_user_chk").val()) {
      user_chk_val = $("#chall_user_chk").val();
      if (user_chk_val) {
        arr_val = user_chk_val.split(",");
        for (var i = 0; i < search_list_cnt; i++) {
          var val = $(".layer_user_info dd button").eq(i).val().trim();
          if ($.inArray(val, arr_val) >= 0) {
            $(".layer_user_info dd button").eq(i).addClass("on");
            if ($(".layer_user_info dd button").eq(i).hasClass("on") == false) {
              $(".layer_user_info dd button").eq(i).addClass("on");
            }
          }
        }

        $(".layer_user_submit").addClass("on");
      }
    }
  });

  //챌린지 인증 파일 리스트형
  $(document).on("click", ".list_function_type .type_list", function (event) {
    //$(".list_function_type .type_list").click(function() {
    $(".list_function_type button").removeClass("on");
    $(this).addClass("on");
    $(".list_box .list_conts").removeClass("type_img");
    $(".list_box .list_conts").removeClass("type_on");
    $(".list_box .list_conts").addClass("type_list");
    $(".list_box .list_conts").addClass("type_on");
  });

  //챌린지 인증 파일 이미지형
  $(document).on("click", ".list_function_type .type_img", function (event) {
    //$(".list_function_type .type_img").click(function() {
    $(".list_function_type button").removeClass("on");
    $(this).addClass("on");
    $(".list_box .list_conts").removeClass("type_list");
    $(".list_box .list_conts").removeClass("type_on");
    $(".list_box .list_conts").addClass("type_img");
    $(".list_box .list_conts").addClass("type_on");
  });

  $(".rew_cha_view_result .title_area .title_more").click(function () {
    $(".layer_result").show();
  });

  //인증 파일 전체레이어
  $(".rew_cha_view_result li button").click(function () {
    if ($(".layer_result").css("display") == "none") {
      file_user_list();
      $("#file_list_sel button").hide();
      $("#file_list_sel strong").hide();
      $("#file_user_del").hide();
      $(".layer_result").show();
    }
  });

  //뷰페이지 상단 레이어
  $(".rew_cha_view_header .view_user li button").click(function () {
    //$(".layer_result").show();
  });

  //혼합형 레이어 닫기
  $(".layer_result_right .layer_close button").click(function () {
    $(".layer_result_user .layer_result_user_in ul li button")
      .eq(0)
      .trigger("click");

    if ($("#mix_list_sel button").is(":visible") == true) {
      $("#mix_list_sel button").hide();
    }

    if ($("#mix_list_sel strong").is(":visible") == true) {
      $("#mix_list_sel strong").hide();
    }

    $(".layer_result_right").removeClass("pos_fix");

    $(".layer_mix").hide();
  });
  $(document).on("click", ".mix_zone", function (e) {
    if ($(e.target).attr("id") == "mix_jjim") {
      var val = $(e.target).attr("value");
      $(".layer_mix").css("z-index", "100");
      var fdata = new FormData();
      fdata.append("mix_idx", val);
      fdata.append("chall_idx", $("#view_idx").val());
      fdata.append("mode", "chall_mix_check");
      $("#service").val("challenge");

      $.ajax({
        type: "post",
        async: false,
        data: fdata,
        contentType: false,
        processData: false,
        url: "/inc/process.php",
        success: function (data) {
          console.log(" dd" + data);
          if (data) {
            var tdata = data.split("|");
            if (tdata) {
              var uid = tdata[0];
              var name = tdata[1];
              $("#send_userid").val(uid);
              $(".jf_box_in .jf_top strong span").text(name);
              $(".jl_box_in .jl_top strong span").text(name);
              $("#work_idx").val(val);
              $(".jjim_first").show();
            }
          }
        },
      });
    } else {
      $(".layer_mix").show();
    }
  });

  $(document).on("click", ".layer_mix .btn_list_file", function () {
    var url = "/inc/file_download.php";
    var num;
    var id = $(this).attr("id");

    if (id) {
      var num = id.replace("btn_list_file_", ""); //$k
      var idx = $(this).val();
      var mode = "challenges_file_down_new";
      var params = { idx: idx, num: num, mode: mode };
      console.log(num + "|" + idx);
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

  //혼합형 레이어 보이기
  $(document).on("click", "#file_jjim", function (e) {
    var val = $(e.target).attr("value");
    var fdata = new FormData();
    fdata.append("file_idx", val);
    fdata.append("chall_idx", $("#view_idx").val());
    fdata.append("mode", "chall_file_check");
    $("#service").val("challenge");

    $.ajax({
      type: "post",
      async: false,
      data: fdata,
      contentType: false,
      processData: false,
      url: "/inc/process.php",
      success: function (data) {
        console.log(" dd" + data);
        if (data) {
          var tdata = data.split("|");
          if (tdata) {
            var uid = tdata[0];
            var name = tdata[1];
            $("#send_userid").val(uid);
            $(".jf_box_in .jf_top strong span").text(name);
            $(".jl_box_in .jl_top strong span").text(name);
            $("#work_idx").val(val);
            $(".jjim_first").show();
          }
        }
      },
    });
  });

  //setInterval("scroll_top_show()", 10);
  /*var checkIt = setInterval(function() {
      if ($('body').find('.mix_zone').length > 0) {
        clearInterval(checkIt);
        jQuery('.mix_zone').on('scroll', function() {
          console.log(1)
        });
      }
    }, 100)*/

  $(".layer_result .layer_close button").click(function () {
    if ($(".btns_down").hasClass("on") == true) {
      $(".btns_down").removeClass("on");
    }

    if ($(".list_conts .list_ul li button").hasClass("on") == true) {
      $(".list_conts .list_ul li button").removeClass("on");
    }

    if ($(".btns_left").css("display") == "inline-block") {
      $(".btns_left").hide();
    }

    $(".layer_result").hide();
  });

  $(".layer_result_user li button").click(function () {
    $(".layer_result_user li button").removeClass("on");
    $(this).addClass("on");
  });

  //챌린지 인증 메시지 더보기 레이어 오픈
  $(".rew_cha_view_masage .title_area .title_more").click(function () {
    $("#masage_list_sel button").hide();
    $("#masage_list_sel strong").hide();
    $(".layer_masage").show();
  });

  //챌린지 혼합형 더보기 레이어 오픈
  $(".rew_cha_view_mix .title_area .title_more").click(function () {
    $(".layer_mix").show();
  });

  //챌린지 혼합형 이미지 확대보기
  $(document).on("click", "div[id^='mix_imgs_box']", function (event) {
    // var id = $(this).attr("id");
    // var no = id.replace("mix_imgs_box_", "");
    // if (no) {
    //   var img = ori_img_src[no];
    //   $("#layer_cha_img").attr("src", img);
    //   $(".layer_cha_image").show();
    // }
  });

  //챌린지 인증 메시지 레이어 오픈
  $(document).on("click", ".masage_zone", function (e) {
    if ($(e.target).attr("id") == "masage_jjim") {
      var val = $(e.target).attr("value");
      var fdata = new FormData();
      fdata.append("masage_idx", val);
      fdata.append("chall_idx", $("#view_idx").val());
      fdata.append("mode", "chall_masage_check");
      $("#service").val("challenge");

      $.ajax({
        type: "post",
        async: false,
        data: fdata,
        contentType: false,
        processData: false,
        url: "/inc/process.php",
        success: function (data) {
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
              $(".jjim_first").show();
            }
          }
        },
      });
    } else {
      console.log(2);
      masage_user_list();
      $("#masage_list_sel button").hide();
      $("#masage_list_sel strong").hide();
      $("#masage_user_del").hide();
      $(".layer_masage").show();
    }
  });

  //오늘업무 좋아요
  $(document).on(
    "click",
    "button[id=tdw_list_jjim],#tdw_list_reward",
    function () {
      //console.log("좋아요!!");
      var val = $(this).val();
      var fdata = new FormData();
      fdata.append("work_idx", val);
      fdata.append("mode", "work_todaywork_check");
      $("#service").val("work");

      $.ajax({
        type: "post",
        async: false,
        data: fdata,
        contentType: false,
        processData: false,
        url: "/inc/works_process.php",
        success: function (data) {
          console.log(data);
          if (data) {
            var tdata = data.split("|");
            if (tdata) {
              //좋아요 이력이 있을때 아무런동작 하지않음
              if (tdata[2] == "penalty_on") {
                alert(
                  "좋아요를 보내려는 유저에게 페널티가 적용되어 보낼 수 없습니다."
                );
                return false;
              } else if (tdata[2] == "like_check") {
                return false;
              } else {
                var uid = tdata[0];
                var name = tdata[1];
                $("#send_userid").val(uid);
                $(".jf_box_in .jf_top strong span").text(name);
                $(".jl_box_in .jl_top strong span").text(name);
                $("#work_idx").val(val);
                $(".jjim_first").show();
              }
            }
          }
        },
      });
    }
  );

  //댓글 좋아요
  $(document).on("click", "button[id^=btn_memo_jjim]", function () {
    console.log("좋아요");
    if ($(this).hasClass("on") == true) {
      return false;
    }
    var val = $(this).val();
    var fdata = new FormData();
    fdata.append("comment_idx", val);
    fdata.append("mode", "work_comment_check");
    $("#service").val("memo");
    $(".comment_idx").val(val);
    $.ajax({
      type: "post",
      async: false,
      data: fdata,
      contentType: false,
      processData: false,
      url: "/inc/works_process.php",
      success: function (data) {
        console.log(data);
        if (data) {
          var tdata = data.split("|");
          if (tdata) {
            if (tdata[2] == "penalty_on") {
              alert("해당 유저에게 페널티가 적용되어 보낼 수 없습니다.");
              return false;
            } else {
              var uid = tdata[0];
              var name = tdata[1];
              $("#send_userid").val(uid);
              $(".jf_box_in .jf_top strong span").text(name);
              $(".jl_box_in .jl_top strong span").text(name);
              $("#work_idx").val(val);
              $(".jjim_first").show();
            }
          }
        }
      },
    });
  });

  $(document).on("click", "button[id^=tdw_list_jjim_2]", function () {
    var val = $(this).val();
    var fdata = new FormData();
    fdata.append("work_idx", val);
    fdata.append("mode", "work_todaywork_check");
    $("#service").val("work");

    $.ajax({
      type: "post",
      async: false,
      data: fdata,
      contentType: false,
      processData: false,
      url: "/inc/works_process.php",
      success: function (data) {
        console.log(data);
        if (data) {
          var tdata = data.split("|");
          if (tdata) {
            //좋아요 이력이 있을때 아무런동작 하지않음
            if (tdata[2] == "penalty_on") {
              alert(
                "좋아요를 보내려는 유저에게 페널티가 적용되어 보낼 수 없습니다."
              );
              return false;
            } else if (tdata[2] == "like_check") {
              alert("이미 오늘 해당 유저에게 좋아요를 보냈습니다!");
              return false;
            } else {
              $("#tuto_pop_01").show();
            }
          }
        }
      },
    });
  });

  //마음을 전하세요. 클릭시
  var ff_class = "";
  var jf_text = "";
  $(".jf_area button").click(function () {
    var val = $(this).val();
    if (val) {
      $("#jf_idx").val(val);
      jf_class = $(this).attr("class");
      if ($(this).attr("class") == "btn_jf_01") {
        jf_text = "성과에 감탄합니다.";
      }
      if ($(this).attr("class") == "btn_jf_02") {
        jf_text = "성장을 응원합니다.";
      }
      if ($(this).attr("class") == "btn_jf_03") {
        jf_text = "에너지에 감동합니다.";
      }
      if ($(this).attr("class") == "btn_jf_04") {
        jf_text = "성실함을 응원합니다.";
      }
      if ($(this).attr("class") == "btn_jf_05") {
        jf_text = "실행에 감탄합니다.";
      }
      if ($(this).attr("class") == "btn_jf_06") {
        jf_text = "협업에 감사합니다.";
      }
      if ($(this).attr("class") == "btn_jf_07") {
        jf_text = "무난한";
      }
      if ($(this).attr("class") == "btn_jf_08") {
        jf_text = "지친";
      }
      if ($(this).attr("class") == "btn_jf_09") {
        jf_text = "속상한";
      }
      $(".jf_area button").not(this).removeClass("on");
      $(this).addClass("on");
      $(".jf_bottom button").removeClass("btn_off").addClass("btn_on");
      $("#jl_comment").val(jf_text);
    }
  });

  //마을을 전하세요 닫기  (체크해봐야함)
  $(".jf_close button").click(function () {
    $(".jjim_first").hide();
    $(".jf_area button").removeClass("on");
    $(".jf_bottom button").removeClass("btn_on").addClass("btn_off");
    if ($(".jf_close").hasClass("live") == true) {
      $(".jjim_graph").show();
    } else if ($(".jf_close").hasClass("rader") == true) {
      $(".radar_layer").show();
    } else if ($(".jf_close").hasClass("like") == true) {
      $(".jjim_table").show();
    }
  });

  //마음을 전하세요. 다음 버튼
  //$(".jf_bottom button").click(function() {
  //$("#jf_bottom").click(function() {
  $("#jf_bottom")
    .off("click")
    .on("click", function () {
      var btc = $(".jf_area button").attr("class");
      var jf_idx = $("#jf_idx").val();
      if (jf_idx) {
        //    console.log(jf_idx);
      }
      //console.log("jf_idx :: " + jf_idx);
      //if ($(this).hasClass("btn_on")) {
      if ($("#jf_bottom button").hasClass("btn_on")) {
        $(".jjim_first").hide();
        $(".jf_area button").removeClass("on");
        $(".jf_bottom button").removeClass("btn_on").addClass("btn_off");
        $(".jjim_layer").show();
        $(".jl_area .jl_desc").removeClass().addClass("jl_desc");
        $(".jl_area .jl_desc").addClass(jf_class);
        $(".jl_area .jl_desc p").text(jf_text);
        $(".jl_area .input_jl").val(jf_text);
        //$(".jl_area .input_jl").val("");
        //$(".jl_area .input_jl").attr("placeholder", jf_text);
      }
    });

  //좋아요 입력창(내용 입력시)
  $("#jl_comment").bind("input keyup", function (e) {
    if (!$(this).val()) {
      if ($("#jl_bottom button").hasClass("btn_on")) {
        $("#jl_bottom button").removeClass("btn_on");
        $("#jl_bottom button").addClass("btn_off");
      }
    } else {
      if ($("#jl_bottom button").hasClass("btn_off")) {
        $("#jl_bottom button").removeClass("btn_off");
        $("#jl_bottom button").addClass("btn_on");
      }
    }
  });

  //마음전하기 클릭시 한번만 내용비우기
  //var jl_comment = 1;
  $("#jl_comment").click(function () {
    if (jl_comment == 1) {
      //$(this).val("");
      //jl_comment++;
    }
  });

  //마음을 전하세요. 보내기 버튼
  $("#jl_bottom")
    .off("click")
    .on("click", function () {
      if (!$("#jl_comment").val()) {
        alert("마음을 전할 내용을 입력하세요.");
        $("#jl_comment").focus();
        return false;
      }

      var jf_idx = $("#jf_idx").val();
      var send_userid = $("#send_userid").val();
      var jl_comment = $("#jl_comment").val();
      var like_flag = $("#like_flag").val();
      var service = $("#service").val();

      if (!service) {
        //챌린지
        if ($(location).attr("pathname").indexOf("/challenge/") == 0) {
          service = "challenge";
          //라이브
        } else if ($(location).attr("pathname").indexOf("/live/") == 0) {
          service = "live";
          //파티
        } else if ($(location).attr("pathname").indexOf("/party/") == 0) {
          service = "party";
        } else {
          //오늘업무가 열렸을때
          if ($(".layer_today").is(":visible") == true) {
            if ($(".btn_lr_01").hasClass("on") == true) {
              service = "work";
            } else if ($(".btn_lr_02").hasClass("on") == true) {
              service = "challenge";
            }
          } else {
            service = $("#service").val();
          }
        }
      }

      console.log(service);
      if (jf_idx) {
        var fdata = new FormData();
        fdata.append("mode", "lives_like");
        fdata.append("jf_idx", jf_idx);
        fdata.append("jl_comment", jl_comment);
        fdata.append("send_userid", send_userid);
        fdata.append("like_flag", like_flag);
        fdata.append("service", service);

        var attend_type = $("#attend_type").val();

        if ($("#work_idx").val()) {
          fdata.append("work_idx", $("#work_idx").val());
        } else if ($("#btn_pu_heart").val()) {
          fdata.append("work_idx", $("#btn_pu_heart").val());
          alert($("#btn_pu_heart").val());
        }
        $.ajax({
          type: "post",
          //async: false,
          data: fdata,
          contentType: false,
          processData: false,
          url: "/inc/process.php",
          success: function (data) {
            console.log(data);
            if (data) {
              tdata = data.split("|");
              // penalty = tdata[0];
              result = tdata[0];
              insertidx = tdata[1];
              // if(penalty == "penalty"){
              //   alert('패널티로 인하여 해당 유저에게 좋아요를 보낼 수 없습니다!');
              //   return false;
              // }else{

              // mode 에서 패널티에 대한 값을 정의하는 것이 없음 2023-11-20
              if (result == "complete") {
                if (service == "live") {
                  todaywork_list_live();
                  lives_index_list_new();
                } else if (service == "work" || service == "memo") {
                  //라이브의 오늘업무일때
                  if ($(location).attr("pathname").indexOf("/live/") == 0) {
                    todaywork_list_live();
                    lives_index_list_new();
                  } else {
                    works_list();
                  }
                } else if (service == "challenge") {
                  if (attend_type == 1) {
                    view_masage_in();
                    masage_list();
                    setTimeout(function () {
                      $(".masage_zone > div").addClass("on");
                    }, 300);
                  } else if (attend_type == 2) {
                    auth_file_list();
                  } else if (attend_type == 3) {
                    view_mix_in();
                    mix_list();
                    setTimeout(function () {
                      $(".mix_zone > div").addClass("on");
                    }, 300);
                  }
                } else if (service == "party") {
                  project_like(insertidx);
                }
                if ($("#btn_jf_0" + jf_idx).hasClass("on") == true) {
                  $("#btn_jf_0" + jf_idx).remove("on");
                }
                $("#jl_comment").val("");
                $(".jjim_first").hide();
                // 버튼 on클래스 남아있어서 제거(김정훈)
                $(".jf_area button").removeClass("on");
              }
              // }
            }
          },
        });
      }
    });

  //마음을 전하세요. 닫기 버튼
  $(".jl_close button").click(function () {
    $(".jjim_layer").hide();
    jl_comment = 1;
  });

  //챌린지 인증 메시지 선택
  $(document).on("click", ".masage_area_in", function (event) {
    $(this).toggleClass("on");
    var masage_list_cnt = $("div[id^='masage_list_chk']").length;
    var masage_chk = 0;
    var auth_masage_idx = [];
    for (var i = 0; i < masage_list_cnt; i++) {
      if ($("div[id='masage_list_chk" + i + "']").hasClass("on") == true) {
        if ($("div[id='masage_list_chk" + i + "']").attr("value")) {
          auth_masage_idx.push(
            $("div[id='masage_list_chk" + i + "']").attr("value")
          );
          masage_chk++;
        }
      }
    }

    challenges_state_check(auth_masage_idx);

    //선택시 활성화
    if (masage_chk) {
      $(".layer_result_btns").show();
      $("#masage_list_sel strong").text(masage_chk + " 개 선택");
      //$("#masage_user_del").show();
      $("#masage_list_sel strong").show();
      $("#masage_list_sel button").show();
    } else {
      //$("#masage_user_del").hide();
      $(".layer_result_btns").hide();
      $("#masage_list_sel button").hide();
      $("#masage_list_sel strong").hide();
    }
  });

  $("#list_thumb_img").click(function () {
    //console.log(111);
    $(".layer_cha_image").show();
  });

  //인증파일 이미지 확대보기, 파일 다운로드
  $(document).on(
    "click",
    ".list_conts .list_ul li .list_thumb_preview",
    function (event) {
      var no = $(this).val();
      if (no) {
        var img = file_ori_img_src[no];
        if (img) {
          $("#layer_cha_img").attr("src", img);
          $(".layer_cha_image").show();
        } else {
          view_file_download(no);
        }
      }
    }
  );

  //챌린지 뷰페이지 이미지 확대
  $(document).on(
    "click",
    ".layer_result_box .mix_zone .mix_imgs_box img",
    function () {
      // var img = $(this).attr("src");
      var img_idx = $(this).attr("value");
      // var img_idx = $(this).val();
      console.log(img_idx);
      // if (img) {
      //   //img = img.replace("img", "img_ori");
      //   $("#layer_cha_img").attr("src", img);
      //   console.log("img :: " + img);
      //   $(".layer_cha_image").show();
      // }

      var fdata = new FormData();
      fdata.append("mode", "img_slice");
      fdata.append("img_idx", img_idx);

      $.ajax({
        type: "POST",
        data: fdata,
        contentType: false,
        processData: false,
        url: "/inc/challenges_process.php",
        success: function (data) {
          console.log("data ==> " + data);
          var tdata = data.split("|");
          html = tdata[0];
          cnt = tdata[1];
          $(".layer_cha_slide .btn_off").hide();
          if (cnt == 1) {
            $(".layer_cha_slide_btn").hide();
          }
          $(".layer_cha_img").show();
          $(".layer_cha_slide").html(html);
        },
      });
    }
  );

  $(document).on("click", ".layer_cha_img .layer_deam", function () {
    $(".layer_cha_img").hide();
    return false;
  });

  $(document).on("click", ".layer_cha_img .slide_btn_prev", function () {
    listcnt = $("li[id*=imgList_]").length;

    var listNo = $(".btn_on").attr("id");
    no = listNo.replace("imgList_", "");
    no = parseInt(no);

    prev = no - 1;
    console.log(prev);
    $(".btn_on").hide();
    $("#imgList_" + no).removeClass("btn_on");

    if (prev == 0) {
      $("#imgList_" + listcnt).show();
      $("#imgList_" + listcnt).addClass("btn_on");
    } else {
      $("#imgList_" + prev).show();
      $("#imgList_" + prev).addClass("btn_on");
    }
    // $("#imgList_"+prev).addClass("btn_on");
    return false;
  });

  $(document).on("click", ".layer_cha_img .slide_btn_next", function () {
    listcnt = $("li[id*=imgList_]").length;

    var listNo = $(".btn_on").attr("id");
    no = listNo.replace("imgList_", "");
    no = parseInt(no);

    next = no + 1;
    console.log(next);
    $(".btn_on").hide();
    $("#imgList_" + no).removeClass("btn_on");

    max = listcnt + 1;
    if (next == max) {
      $("#imgList_1").show();
      $("#imgList_1").addClass("btn_on");
    } else {
      $("#imgList_" + next).show();
      $("#imgList_" + next).addClass("btn_on");
    }
    // $("#imgList_"+prev).addClass("btn_on");
    return false;
  });

  //챌린지 뷰페이지 이미지 확대
  $(document).on(
    "click",
    ".layer_result_box .mix_zone .mix_imgs_box img",
    function () {
      // var img_src = $(this).attr("src");
      // $("#layer_cha_img").attr("src",img_src);
      // $(".layer_cha_image").show();
    }
  );

  $(document).on("click", ".layer_cha_img .layer_deam", function () {
    $(".layer_cha_image").hide();
  });

  //인증파일 목록보기
  //$(document).on('click', 'div[id^=file_list_chk]', function(event) {
  //});

  //인증파일 선택
  $(document).on(
    "click",
    ".list_conts .list_ul li .list_thumb_select",
    function (event) {
      $(this).closest(".list_thumb_wrap").toggleClass("on");
      var auth_file_chk = 0;
      var auth_file_idx = [];
      var file_list_cnt = $("div[id^='file_list_chk']").length;
      for (var i = 0; i < file_list_cnt; i++) {
        if ($("div[id='file_list_chk" + i + "']").hasClass("on") == true) {
          //fdata.append("auth_file_idx[]", $("button[id^='file_list_chk" + i + "']").val());
          if ($("div[id='file_list_chk" + i + "']").attr("value")) {
            auth_file_idx.push(
              $("div[id='file_list_chk" + i + "']").attr("value")
            );
            auth_file_chk++;
          }
        }
      }

      challenges_state_check(auth_file_idx);

      //선택시 활성화
      if (auth_file_chk) {
        $(".layer_result_btns").show();
        $("#file_list_sel").show();
        $("#file_list_sel strong").text(auth_file_chk + " 개 선택");
        $("#file_list_sel strong").show();
        $("#file_list_sel button").show();
        $(".btns_down").addClass("on");
      } else {
        $(".layer_result_btns").hide();
        $("#file_list_sel button").hide();
        $("#file_list_sel strong").hide();
        $(".btns_down").removeClass("on");
      }
    }
  );

  //챌린지 인증 파일선택
  $(document).on("click", ".list_conts .list_ul li button", function (event) {
    /*    var v = $(this).val();
      //$(this).toggleClass("on");

      var auth_file_chk = 0;
      var auth_file_idx = [];
      var file_list_cnt = $("div[id^='file_list_chk']").length;
      for (var i = 0; i < file_list_cnt; i++) {
        if ($("div[id='file_list_chk" + i + "']").hasClass("on") == true) {
          //fdata.append("auth_file_idx[]", $("button[id^='file_list_chk" + i + "']").val());
          auth_file_idx.push($("div[id='file_list_chk" + i + "']").val());
          auth_file_chk++;
        }
      }

      challenges_state_check(auth_file_idx);

      //선택시 활성화
      if (auth_file_chk) {
        $("#file_list_sel").show();
        $("#file_list_sel strong").text(auth_file_chk + " 개 선택");
        $("#file_list_sel strong").show();
        $("#file_list_sel button").show();
        $(".btns_down").addClass("on");

      } else {
        $("#file_list_sel button").hide();
        $("#file_list_sel strong").hide();
        $(".btns_down").removeClass("on");
      }
    */
  });

  //챌린지 혼합형 선택
  $(document).on("click", ".mix_chk", function (event) {
    $(this).closest(".mix_area_in").toggleClass("on");
    var mix_list_cnt = $("div[id^='mix_list_chk']").length;
    var mix_chk = 0;
    var auth_mix_idx = new Array();

    for (var i = 0; i < mix_list_cnt; i++) {
      if ($("div[id='mix_list_chk" + i + "']").hasClass("on") == true) {
        if ($("div[id='mix_list_chk" + i + "']").attr("value")) {
          auth_mix_idx.push($("div[id='mix_list_chk" + i + "']").attr("value"));
          mix_chk++;
        }
      }
    }

    challenges_state_check(auth_mix_idx);

    //선택시 활성화
    if (mix_chk) {
      $(".layer_result_btns").show();
      $("#mix_list_sel strong").text(mix_chk + " 개 선택");
      $("#mix_list_sel strong").show();
      $("#mix_list_sel button").show();

      $("#btns_down").show();
      if ($(".btns_down").hasClass("on") == false) {
        $(".btns_down").addClass("on");
      }
    } else {
      $(".layer_result_btns").hide();
      $("#mix_list_sel button").hide();
      $("#mix_list_sel strong").hide();
      $("#btns_down").hide();
    }
  });

  //선택한 인증 메시지, 인증 파일 체크
  function challenges_state_check(v) {
    if (v) {
      var chll_idx = $("#view_idx").val();
      var fdata = new FormData();

      fdata.append("mode", "challenges_state_check");
      fdata.append("chll_idx", chll_idx);
      fdata.append("data_idx", v);

      $.ajax({
        type: "POST",
        data: fdata,
        contentType: false,
        processData: false,
        url: "/inc/process.php",
        success: function (data) {
          //console.log("data ==> " + data);
          if (data) {
            tdata = data.split("|");

            var ctype = tdata[0];
            var cdel = tdata[1];
            var cdcoin = tdata[2];
            var cstate = tdata[3];

            //인증 메시지
            if (ctype == "1") {
              if (cdel == "y") {
                $("#masage_user_del").show();
              } else if (cdel == "n") {
                $("#masage_user_del").hide();
                $("#masage_user_dcoin").hide();
                $("#masage_user_rcoin").hide();
              }

              //상태값
              if (cdcoin == "dcoin") {
                $("#masage_user_dcoin").show();
                $("#masage_user_rcoin").hide();
              }

              if (cdcoin == "rcoin") {
                $("#masage_user_rcoin").show();
                $("#masage_user_dcoin").hide();
              }
            } else if (ctype == "2") {
              if (cdel == "y") {
                $("#file_user_del").show();
              } else if (cdel == "n") {
                $("#file_user_del").hide();
                $("#file_user_dcoin").hide();
                $("#file_user_rcoin").hide();
              }

              //상태값
              if (cdcoin == "dcoin") {
                $("#file_user_dcoin").show();
                $("#file_user_rcoin").hide();
              }

              if (cdcoin == "rcoin") {
                $("#file_user_rcoin").show();
                $("#file_user_dcoin").hide();
              }
            } else if (ctype == "3") {
              //$(".layer_mix").css("display");

              if (cdel == "y") {
                $("#mix_user_del").show();
              } else if (cdel == "n") {
                $("#mix_user_del").hide();
                $("#mix_user_dcoin").hide();
                $("#mix_user_rcoin").hide();
              }

              //상태값
              if (cdcoin == "dcoin") {
                $("#mix_user_dcoin").show();
                $("#mix_user_rcoin").hide();

                /*if ($(".layer_masage").css("display") == "block") {
                  $("#mix_user_dcoin").show();
                  $("#mix_user_rcoin").hide();
                }*/
              }

              if (cdcoin == "rcoin") {
                //if ($(".layer_result").css("display") == "block") {
                $("#mix_user_rcoin").show();
                $("#mix_user_dcoin").hide();
                //}

                /*if ($(".layer_masage").css("display") == "block") {
                  $("#mix_user_dcoin").show();
                  $("#mix_user_rcoin").hide();
                }*/
              }
            } else {
              $("#mix_user_del").hide();
              $("#mix_user_dcoin").hide();
              $("#mix_user_rcoin").hide();
            }
          } else {
            $("#masage_user_del").hide();
            $("#masage_user_dcoin").hide();
            $("#masage_user_rcoin").hide();

            $("#file_user_del").hide();
            $("#file_user_dcoin").hide();
            $("#file_user_rcoin").hide();

            $("#mix_user_del").hide();
            $("#mix_user_dcoin").hide();
            $("#mix_user_rcoin").hide();
          }
        },
      });
    }
  }

  //챌린지 인증 파일, 삭제
  $("#file_user_del").click(function () {
    var file_list_cnt = $("div[id^='file_list_chk']").length;
    var fdata = new FormData();
    var chll_idx = $("#view_idx").val();
    var auth_file_chk = 0;
    var auth_file_idx = [];

    for (var i = 0; i < file_list_cnt; i++) {
      if ($("div[id^='file_list_chk" + i + "']").hasClass("on") == true) {
        if ($("div[id='file_list_chk" + i + "']").attr("value")) {
          auth_file_idx.push(
            $("div[id='file_list_chk" + i + "']").attr("value")
          );
          auth_file_chk++;
        }
      }
    }

    if (auth_file_chk > 0) {
      if (
        confirm(
          "선택한 " + auth_file_chk + "개의 인증 파일을 삭제 하시겠습니까?"
        )
      ) {
        fdata.append("mode", "challenges_file_del");
        fdata.append("user_email", $("#user_email").val());
        fdata.append("chll_idx", chll_idx);
        fdata.append("data_idx", auth_file_idx);

        $.ajax({
          type: "POST",
          data: fdata,
          contentType: false,
          processData: false,
          url: "/inc/process.php",
          success: function (data) {
            console.log(data);
            if (data == "complete") {
              alert("인증 파일 삭제 및 코인 회수 처리 되었습니다.");
              auth_file_list();
              auth_file_list_top();
              file_user_list();

              if ($(".btns_left").css("display") == "block") {
                $(".btns_left").hide();
              }

              if ($(".btns_down").hasClass("on") == true) {
                $(".btns_down").removeClass("on");
              }

              location.reload();

              $("#file_user_del").hide();
              return false;
            } else if (data == "masage_not") {
              alert("본인 인증 메시지만 삭제 하실수 있습니다.");
              return false;
            } else if (data == "coininfo_not") {
              alert("코인 지급 받은 내역이 없어 삭제 하실수 없습니다.");
              return false;
            }
          },
        });
      }
    }
  });

  //챌린지 인증 메시지, 삭제
  $("#masage_user_del").click(function () {
    var masage_list_cnt = $("div[id^='masage_list_chk']").length;
    var fdata = new FormData();
    var masage_chk = 0;
    for (var i = 0; i < masage_list_cnt; i++) {
      if ($("div[id='masage_list_chk" + i + "']").hasClass("on") == true) {
        if ($("div[id='masage_list_chk" + i + "']").attr("value")) {
          fdata.append(
            "masage_idx[]",
            $("div[id='masage_list_chk" + i + "']").attr("value")
          );
          masage_chk++;
        }
      }
    }

    if (masage_chk > 0) {
      if (
        confirm(
          "선택한 인증 메시지 삭제 및 코인 회수 처리 됩니다.\n그래도 삭제 진행하시겠습니까?"
        )
      ) {
        var idx = $("#view_idx").val();
        fdata.append("mode", "challenges_masage_del");
        fdata.append("user_email", $("#user_email").val());
        fdata.append("idx", idx);
        $.ajax({
          type: "POST",
          data: fdata,
          contentType: false,
          processData: false,
          url: "/inc/process.php",
          success: function (data) {
            console.log(data);

            if (data == "complete_del") {
              alert("인증 메시지 삭제 처리 되었습니다.");
              location.reload();
            } else if (data == "complete") {
              alert("인증 메시지 삭제 및 코인 회수 처리 되었습니다.");

              location.reload();

              masage_list();
              view_masage_in();
              $("#masage_list_sel button").hide();
              $("#masage_list_sel strong").hide();
              $("#masage_user_del").hide();
              $(".layer_masage").show();
              return false;
            } else if (data == "masage_not") {
              alert("본인 인증 메시지만 삭제 하실수 있습니다.");
              return false;
            } else if (data == "coininfo_not") {
              alert("코인 지급 받은 내역이 없어 삭제 하실수 없습니다.");
              return false;
            }
          },
        });
      }
    } else {
      alert("삭제할 메시지를 선택해 주세요.");
      return false;
    }
  });

  //챌린지 혼합형, 삭제
  $("#mix_user_del").click(function () {
    var mix_list_cnt = $("div[id^='mix_list_chk']").length;
    var fdata = new FormData();
    var chll_idx = $("#view_idx").val();
    var auth_mix_chk = 0;
    var auth_mix_idx = new Array();

    for (var i = 0; i < mix_list_cnt; i++) {
      if ($("div[id='mix_list_chk" + i + "']").hasClass("on") == true) {
        if ($("div[id='mix_list_chk" + i + "']").attr("value")) {
          auth_mix_idx.push($("div[id='mix_list_chk" + i + "']").attr("value"));
          auth_mix_chk++;
        }
      }
    }

    if (auth_mix_chk > 0) {
      if (confirm("선택한 " + auth_mix_chk + "개의 내용을 삭제하시겠습니까?")) {
        fdata.append("mode", "challenges_mix_del");
        fdata.append("user_email", $("#user_email").val());
        fdata.append("chll_idx", chll_idx);
        fdata.append("data_idx", auth_mix_idx);

        $.ajax({
          type: "POST",
          data: fdata,
          contentType: false,
          processData: false,
          url: "/inc/process.php",
          success: function (data) {
            console.log(data);
            if (data == "complete") {
              alert("인증 파일 삭제 및 코인 회수 처리 되었습니다.");
              auth_file_list();
              auth_file_list_top();
              file_user_list();

              if ($(".btns_left").css("display") == "block") {
                $(".btns_left").hide();
              }

              if ($(".btns_down").hasClass("on") == true) {
                $(".btns_down").removeClass("on");
              }

              location.reload();

              $("#file_user_del").hide();
              return false;
            } else if (data == "masage_not") {
              alert("본인 인증 메시지만 삭제 하실수 있습니다.");
              return false;
            } else if (data == "coininfo_not") {
              alert("코인 지급 받은 내역이 없어 삭제 하실수 없습니다.");
              return false;
            }
          },
        });
      }
    }
  });

  //챌린지 인증 파일, 무효 후 코인 회수
  $(document).on("click", "#file_user_dcoin", function (event) {
    var file_list_cnt = $("div[id^='file_list_chk']").length;
    var fdata = new FormData();
    var file_chk = 0;
    for (var i = 0; i < file_list_cnt; i++) {
      if ($("div[id='file_list_chk" + i + "']").hasClass("on") == true) {
        if ($("div[id='file_list_chk" + i + "']").attr("value")) {
          fdata.append(
            "select_idx[]",
            $("div[id='file_list_chk" + i + "']").attr("value")
          );
          file_chk++;
        }
      }
    }

    if (file_chk > 0) {
      if (confirm("선택한 인증 파일을 무효 후 코인 회수하시겠습니까?")) {
        var idx = $("#view_idx").val();
        fdata.append("mode", "challenges_dcoin");
        fdata.append("user_email", $("#user_email").val());
        fdata.append("idx", idx);
        $.ajax({
          type: "POST",
          data: fdata,
          contentType: false,
          processData: false,
          url: "/inc/process.php",
          success: function (data) {
            console.log(data);
            if (data == "complete") {
              alert("무효 후 코인 회수 처리 되었습니다.");
              auth_file_list();
              auth_file_list_top();
              file_user_list();

              if ($(".btns_left").css("display") == "block") {
                $(".btns_left").hide();
              }

              if ($(".btns_down").hasClass("on") == true) {
                $(".btns_down").removeClass("on");
              }

              return false;
            } else if (data == "coin_minus") {
              alert("회원이 보유한 코인이 부족하여 코인회수가 되지 않습니다.");
              return false;
            } else if (data == "not") {
              alert("이미 코인이 회수처리 된 메시지 입니다.");
              return false;
            } else if (data == "coin_info_not") {
              alert("코인 지급 받은 내역이 없어 처리가 되지 않습니다.");
              return false;
            }
          },
        });
      }
    } else {
      alert("인증 메시지를 선택 해주세요.");
      return false;
    }
  });

  //챌린지 혼합형, 무효 후 코인 회수
  $(document).on("click", "#mix_user_dcoin", function (event) {
    var mix_list_cnt = $("div[id^='mix_list_chk']").length;
    var fdata = new FormData();
    var mix_chk = 0;
    for (var i = 0; i < mix_list_cnt; i++) {
      if ($("div[id='mix_list_chk" + i + "']").hasClass("on") == true) {
        if ($("div[id='mix_list_chk" + i + "']").attr("value")) {
          fdata.append(
            "select_idx[]",
            $("div[id='mix_list_chk" + i + "']").attr("value")
          );
          mix_chk++;
        }
      }
    }

    if (mix_chk > 0) {
      if (confirm("선택한 내용을 무효 후 코인 회수하시겠습니까?")) {
        var idx = $("#view_idx").val();
        fdata.append("mode", "challenges_dcoin");
        fdata.append("user_email", $("#user_email").val());
        fdata.append("idx", idx);
        $.ajax({
          type: "POST",
          data: fdata,
          contentType: false,
          processData: false,
          url: "/inc/process.php",
          success: function (data) {
            console.log(data);
            if (data == "complete") {
              alert("무효 후 코인 회수 처리 되었습니다.");
              auth_file_list();
              auth_file_list_top();
              file_user_list();

              if ($(".btns_left").css("display") == "block") {
                $(".btns_left").hide();
              }

              if ($(".btns_down").hasClass("on") == true) {
                $(".btns_down").removeClass("on");
              }

              return false;
            } else if (data == "coin_minus") {
              alert("회원이 보유한 코인이 부족하여 코인회수가 되지 않습니다.");
              return false;
            } else if (data == "not") {
              alert("이미 코인이 회수처리 된 메시지 입니다.");
              return false;
            } else if (data == "coin_info_not") {
              alert("코인 지급 받은 내역이 없어 처리가 되지 않습니다.");
              return false;
            }
          },
        });
      }
    } else {
      alert("인증 메시지를 선택 해주세요.");
      return false;
    }
  });

  //챌린지 인증 파일, 코인 다시 지급
  $(document).on("click", "#file_user_rcoin", function (event) {
    var file_list_cnt = $("div[id^='file_list_chk']").length;
    var fdata = new FormData();
    var file_chk = 0;
    for (var i = 0; i < file_list_cnt; i++) {
      if ($("div[id='file_list_chk" + i + "']").hasClass("on") == true) {
        if ($("div[id='file_list_chk" + i + "']").attr("value")) {
          fdata.append(
            "select_idx[]",
            $("div[id='file_list_chk" + i + "']").attr("value")
          );
          file_chk++;
        }
      }
    }

    if (file_chk > 0) {
      if (confirm("선택한 인증 파일 코인을 다시 지급 하시겠습니까?")) {
        var idx = $("#view_idx").val();
        fdata.append("mode", "challenges_rcoin");
        fdata.append("user_email", $("#user_email").val());
        fdata.append("idx", idx);
        $.ajax({
          type: "POST",
          data: fdata,
          contentType: false,
          processData: false,
          url: "/inc/process.php",
          success: function (data) {
            console.log(data);
            if (data == "complete") {
              alert("코인 지급처리 되었습니다.");
              auth_file_list();
              auth_file_list_top();
              file_user_list();

              if ($(".btns_left").css("display") == "block") {
                $(".btns_left").hide();
              }

              if ($(".btns_down").hasClass("on") == true) {
                $(".btns_down").removeClass("on");
              }
              return false;
            } else if (data == "coin_minus") {
              alert("회원이 보유한 코인이 부족하여 코인회수가 되지 않습니다.");
              return false;
            } else if (data == "not") {
              alert("코인이 지급이 안된 메시지 입니다.");
              return false;
            } else if (data == "coin_info_not") {
              alert("코인 지급 받은 내역이 없어 처리가 되지 않습니다.");
              return false;
            }
          },
        });
      }
    } else {
      alert("메시지를 선택 해주세요.");
      return false;
    }
  });

  //챌린지 혼합형, 코인 다시 지급
  $(document).on("click", "#mix_user_rcoin", function (event) {
    var mix_list_cnt = $("div[id^='mix_list_chk']").length;
    var fdata = new FormData();
    var mix_chk = 0;
    for (var i = 0; i < mix_list_cnt; i++) {
      if ($("div[id='mix_list_chk" + i + "']").hasClass("on") == true) {
        if ($("div[id='mix_list_chk" + i + "']").attr("value")) {
          fdata.append(
            "select_idx[]",
            $("div[id='mix_list_chk" + i + "']").attr("value")
          );
          mix_chk++;
        }
      }
    }

    if (mix_chk > 0) {
      if (confirm("선택한 내용의 코인을 다시 지급 하시겠습니까?")) {
        var idx = $("#view_idx").val();
        fdata.append("mode", "challenges_rcoin");
        fdata.append("user_email", $("#user_email").val());
        fdata.append("idx", idx);
        $.ajax({
          type: "POST",
          data: fdata,
          contentType: false,
          processData: false,
          url: "/inc/process.php",
          success: function (data) {
            console.log(data);
            if (data == "complete") {
              alert("코인 지급처리 되었습니다.");
              auth_file_list();
              auth_file_list_top();
              file_user_list();

              if ($(".btns_left").css("display") == "block") {
                $(".btns_left").hide();
              }

              if ($(".btns_down").hasClass("on") == true) {
                $(".btns_down").removeClass("on");
              }
              return false;
            } else if (data == "coin_minus") {
              alert("회원이 보유한 코인이 부족하여 코인회수가 되지 않습니다.");
              return false;
            } else if (data == "not") {
              alert("코인이 지급이 안된 메시지 입니다.");
              return false;
            } else if (data == "coin_info_not") {
              alert("코인 지급 받은 내역이 없어 처리가 되지 않습니다.");
              return false;
            }
          },
        });
      }
    } else {
      alert("메시지를 선택 해주세요.");
      return false;
    }
  });

  //챌린지 인증 메시지, 무효 후 코인 회수
  $(document).on("click", "#masage_user_dcoin", function (event) {
    var masage_list_cnt = $("div[id^='masage_list_chk']").length;
    var fdata = new FormData();
    var masage_chk = 0;
    for (var i = 0; i < masage_list_cnt; i++) {
      if ($("div[id='masage_list_chk" + i + "']").hasClass("on") == true) {
        if ($("div[id='masage_list_chk" + i + "']").attr("value")) {
          fdata.append(
            "select_idx[]",
            $("div[id='masage_list_chk" + i + "']").attr("value")
          );
          masage_chk++;
        }
      }
    }

    if (masage_chk > 0) {
      if (confirm("선택한 인증 메시지를 무효 후 코인 회수하시겠습니까?")) {
        var idx = $("#view_idx").val();
        fdata.append("mode", "challenges_dcoin");
        fdata.append("user_email", $("#user_email").val());
        fdata.append("idx", idx);
        $.ajax({
          type: "POST",
          data: fdata,
          contentType: false,
          processData: false,
          url: "/inc/process.php",
          success: function (data) {
            console.log(data);
            if (data == "complete") {
              alert("무효 후 코인 회수 처리 되었습니다.");

              masage_list();
              view_masage_in();
              $("#masage_list_sel button").hide();
              $("#masage_list_sel strong").hide();
              $("#masage_user_rcoin").hide();
              $("#masage_user_dcoin").hide();
              $("#masage_user_del").hide();
              $(".layer_masage").show();

              if ($(".btns_left").css("display") == "block") {
                $(".btns_left").hide();
              }

              return false;
            } else if (data == "coin_minus") {
              alert("회원이 보유한 코인이 부족하여 코인회수가 되지 않습니다.");
              return false;
            } else if (data == "not") {
              alert("이미 코인이 회수처리 된 메시지 입니다.");
              return false;
            } else if (data == "coin_info_not") {
              alert("코인 지급 받은 내역이 없어 처리가 되지 않습니다.");
              return false;
            }
          },
        });
      }
    } else {
      alert("인증 메시지를 선택 해주세요.");
      return false;
    }
  });

  //챌린지 인증 메시지, 코인 다시 지급
  $(document).on("click", "#masage_user_rcoin", function (event) {
    var masage_list_cnt = $("div[id^='masage_list_chk']").length;
    var fdata = new FormData();
    var masage_chk = 0;
    for (var i = 0; i < masage_list_cnt; i++) {
      if ($("div[id='masage_list_chk" + i + "']").hasClass("on") == true) {
        if ($("div[id='masage_list_chk" + i + "']").attr("value")) {
          fdata.append(
            "select_idx[]",
            $("div[id='masage_list_chk" + i + "']").attr("value")
          );
          masage_chk++;
        }
      }
    }

    if (masage_chk > 0) {
      if (confirm("선택한 인증 메시지 코인을 다시 지급 하시겠습니까?")) {
        var idx = $("#view_idx").val();
        fdata.append("mode", "challenges_rcoin");
        fdata.append("user_email", $("#user_email").val());
        fdata.append("idx", idx);
        $.ajax({
          type: "POST",
          data: fdata,
          contentType: false,
          processData: false,
          url: "/inc/process.php",
          success: function (data) {
            console.log(data);
            if (data == "complete") {
              alert("코인 지급처리 되었습니다.");
              masage_list();
              view_masage_in();
              $("#masage_list_sel button").hide();
              $("#masage_list_sel strong").hide();
              $("#masage_user_del").hide();
              $("#masage_user_rcoin").hide();
              $(".layer_masage").show();
              return false;
            } else if (data == "coin_minus") {
              alert("회원이 보유한 코인이 부족하여 코인회수가 되지 않습니다.");
              return false;
            } else if (data == "not") {
              alert("코인이 지급이 안된 메시지 입니다.");
              return false;
            } else if (data == "coin_info_not") {
              alert("코인 지급 받은 내역이 없어 처리가 되지 않습니다.");
              return false;
            }
          },
        });
      }
    } else {
      alert("메시지를 선택 해주세요.");
      return false;
    }
  });

  //챌린지 인증 메시지, 선택해제
  $(document).on("click", "#masage_sel_cancel", function (event) {
    var masage_list_cnt = $("div[id^='masage_list_chk']").length;
    for (var i = 0; i < masage_list_cnt; i++) {
      if ($("div[id='masage_list_chk" + i + "']").hasClass("on") == true) {
        $("div[id='masage_list_chk" + i + "']").removeClass("on");
      }
    }

    $(".layer_result_btns").hide();
    $("#masage_list_sel button").hide();
    $("#masage_list_sel strong").hide();

    $("#masage_user_dcoin").hide();
    $("#masage_user_rcoin").hide();
    $("#masage_user_del").hide();
  });

  //챌린지 인증 파일, 선택해제
  $(document).on("click", "#file_sel_cancel", function (event) {
    var file_list_cnt = $("div[id^='file_list_chk']").length;
    for (var i = 0; i < file_list_cnt; i++) {
      if ($("div[id='file_list_chk" + i + "']").hasClass("on") == true) {
        $("div[id='file_list_chk" + i + "']").removeClass("on");
      }
    }

    $(".layer_result_btns").hide();
    $("#file_list_sel button").hide();
    $("#file_list_sel strong").hide();
    $("#file_user_del").hide();
    $(".btns_down").removeClass("on");
  });

  //챌린지 혼합형, 선택해제
  $(document).on("click", "#mix_sel_cancel", function (event) {
    var file_list_cnt = $("div[id^='mix_list_chk']").length;
    for (var i = 0; i < file_list_cnt; i++) {
      if ($("div[id='mix_list_chk" + i + "']").hasClass("on") == true) {
        $("div[id='mix_list_chk" + i + "']").removeClass("on");
      }
    }
    $("#mix_list_sel button").hide();
    $("#mix_list_sel strong").hide();

    $("#mix_user_dcoin").hide();
    $("#mix_user_rcoin").hide();

    $(".layer_result_btns").hide();
    $("#mix_user_del").hide();
    $("#btns_down").hide();
    $(".btns_down").removeClass("on");
  });

  //챌린지 인증 파일, 다운로드
  $(document).on("click", ".btns_down", function (event) {
    var file_list_cnt = $("div[id^='file_list_chk']").length;
    var fdata = new FormData();
    var chll_idx = $("#view_idx").val();
    var auth_file_chk = 0;
    var auth_file_idx = [];
    for (var i = 0; i < file_list_cnt; i++) {
      if ($("div[id='file_list_chk" + i + "']").hasClass("on") == true) {
        //fdata.append("auth_file_idx[]", $("button[id^='file_list_chk" + i + "']").val());
        if ($("div[id='file_list_chk" + i + "']").attr("value")) {
          auth_file_idx.push(
            $("div[id='file_list_chk" + i + "']").attr("value")
          );
          auth_file_chk++;
        }
      }
    }
    console.log(auth_file_idx);
    if (auth_file_chk > 0) {
      if (
        confirm(
          "선택한 " + auth_file_chk + "개의 인증 파일을 다운로드 하시겠습니까?"
        )
      ) {
        var params = {
          mode: "file_multi_download",
          chll_idx: chll_idx,
          auth_file_idx: auth_file_idx,
        };
        var url = "/inc/file_multi_download.php";

        /*$.fileDownload(url, {
          httpMethod: "post",
          data: params,
          successCallback: function(d) {
            console.log(d);
          },
          failCallback: function(e) {
            console.log(e);
            return false;
          }
        });*/

        // if (GetCookie("user_id") != '') {
        //모바일인 경우
        //if (MobileChk() == true) {

        var fdata = new FormData();
        fdata.append("mode", "file_multi_download");
        fdata.append("chll_idx", chll_idx);
        fdata.append("auth_file_idx", auth_file_idx);

        $.ajax({
          type: "POST",
          data: fdata,
          contentType: false,
          processData: false,
          url: url,
          success: function (data) {
            //console.log(data);
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
        //} else {

        /*    $.fileDownload(url, {
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
            */
        //}

        /*$.ajax({
              type: "POST",
              data: fdata,
              contentType: false,
              processData: false,
              url: '/inc/file_multi_download.php',
              success: function(data) {
                console.log(data);
              }
            });*/
      }
    }
  });

  //챌린지 혼합형, 다운로드
  $(document).on("click", "#btns_down", function (event) {
    var mix_list_cnt = $("div[id^='mix_list_chk']").length;
    var fdata = new FormData();
    var chll_idx = $("#view_idx").val();
    var auth_mix_chk = 0;
    var auth_file_idx = new Array();
    for (var i = 0; i < mix_list_cnt; i++) {
      if ($("div[id='mix_list_chk" + i + "']").hasClass("on") == true) {
        if ($("div[id='mix_list_chk" + i + "']").attr("value")) {
          auth_file_idx.push(
            $("div[id='mix_list_chk" + i + "']").attr("value")
          );
          auth_mix_chk++;
        }
      }
    }

    if (auth_mix_chk > 0) {
      if (
        confirm("선택한 " + auth_mix_chk + "개의 파일을 다운로드 하시겠습니까?")
      ) {
        var params = {
          mode: "file_multi_download",
          chll_idx: chll_idx,
          auth_file_idx: auth_file_idx,
        };
        var url = "/inc/file_multi_download.php";

        $.fileDownload(url, {
          httpMethod: "post",
          data: params,
          successCallback: function (d) {
            console.log(d);
          },
          failCallback: function (e) {
            console.log(e);
            return false;
          },
        });
      }
    }
  });

  //챌린지 참여, 인증 메시지 닫기
  $(".layer_masage .layer_close button").click(function () {
    if ($("#input_masage").val()) {
      $("#input_masage").val("");
    }

    $(".layer_masage").hide();
    $("#masage_user_del").hide();
    $("#masage_list_sel button").hide();
    $("#masage_list_sel strong").hide();
    $(".layer_masage .layer_result_user_in ul li button")
      .eq(0)
      .trigger("click");
  });

  // $(".join_type_masage .btns_cha_cancel").click(function(){
  //  $(".join_type_masage").hide();
  // });

  //챌린지참여하기, 혼합형 닫기
  $("#layer_cha_join #btns_cha_cancel").click(function () {
    var idx = $("#view_idx").val();
    var fdata = new FormData();
    fdata.append("mode", "chal_cancel");
    fdata.append("idx", idx);

    $.ajax({
      type: "POST",
      data: fdata,
      contentType: false,
      processData: false,
      url: "/inc/process.php",
      success: function (data) {
        console.log(data);
        if (data == "complete") {
          $("textarea[id=input_type_mix]").val("");
          $("input[id='mix_file_01']").val("");
          $("#mix_file_desc_01").html("");
          $("#mix_file_01").val("");
          $(".btns_cha_join").removeClass("on");
          $(".join_type_mix").hide();
        }
      },
    });
  });

  $(".btn_join_ok").click(function () {
    //  $(".layer_cha_join").show();
  });

  //샘플페이지
  $("#template_btn").click(function () {
    var idx = $("#view_idx").val();
    if (idx) {
      //location.href = "/challenge/sample_write.php?idx=" + idx;
      location.href = "/challenge/write.php?idx=" + idx;
      return false;
    }
  });

  //챌린지참여하기, 혼합형(메시지+파일첨부형) 작성하기 버튼활성화
  $("input[id=file_01]").change(function () {
    var format_ext = new Array(
      "asp",
      "php",
      "jsp",
      "xml",
      "html",
      "htm",
      "aspx",
      "exe",
      "exec",
      "java",
      "js",
      "class",
      "as",
      "pl",
      "mm",
      "o",
      "c",
      "h",
      "m",
      "cc",
      "cpp",
      "hpp",
      "cxx",
      "hxx",
      "lib",
      "lbr",
      "ini",
      "py",
      "pyc",
      "pyo",
      "bak",
      "$$$",
      "swp",
      "sym",
      "sys",
      "cfg",
      "chk",
      "log",
      "lo",
      "mp4"
    );
    enter = $("#layer_update_list").val();
    // thispar = $(this).parent().par ent();
    if (enter == "value") {
      thispar = $(".layer_cha_join:eq(1)");
    } else {
      thispar = $(".layer_cha_join:eq(0)");
    }

    var file_box_cnt = thispar.find(
      ".layer_cha_join_file_desc .file_desc"
    ).length;
    var update = $("#chamyeo_idx").val();
    var upkind = thispar.attr("id");

    console.log(upkind);
    // return false;
    var up_length = $("#layer_update").length;

    if (update) {
      //수정일때
      if (up_length == 0) {
        fileListArr = [];
      }
    } else {
      //처음등록할 때
      if (file_box_cnt == 0) {
        fileListArr = [];
      }
    }

    var file_obj = $(this)[0].files; //파일정보
    console.log(file_obj);
    max_file_cnt = 5;

    var file_cnt = file_obj.length; //선택된 첨부파일 개수
    var limit_file_cnt = max_file_cnt - file_box_cnt; // 추가로 첨부가능한 개수
    console.log(file_box_cnt + "|" + file_cnt);

    if (file_cnt > limit_file_cnt) {
      alert("첨부파일은 5개까지 가능합니다.");
      return false;
    }

    for (var i = 0; i < Math.min(file_cnt, limit_file_cnt); i++) {
      file_name = file_obj[i].name;
      file_size = file_obj[i].size;
      // console.log(file_name+"."+file_size);
      file_number = file_box_cnt + i;
      var ext = file_name.split(".").pop().toLowerCase();
      var maxSize = 100 * 1024 * 1024;
      var fileSize = file_size;
      //용량제한
      if (fileSize > maxSize) {
        alert("첨부파일 사이즈는 100MB 이내로 등록 가능합니다.");
        return false;
      }

      if ($.inArray(ext, format_ext) > 0) {
        alert("첨부할 수 없는 파일입니다.\n파일명 : " + file_name + "");
        return false;
      } else {
        if (update) {
          fileListArr.push(file_obj[i]);
          $("#layer_cha_update .layer_cha_join_file_desc").append(
            '<div class="file_desc" id="chall_file_desc_' +
              file_number +
              '">' +
              '<input type="hidden" id="layer_update">' +
              "<span>" +
              file_name +
              '</span><button id="mix_file_del_' +
              file_number +
              '">삭제</button></div>'
          );
          $("#layer_cha_update_list .layer_cha_join_file_desc").append(
            '<div class="file_desc" id="chall_file_desc_' +
              file_number +
              '">' +
              '<input type="hidden" id="layer_update">' +
              "<span>" +
              file_name +
              '</span><button id="mix_file_del_' +
              file_number +
              '">삭제</button></div>'
          );
        } else {
          fileListArr.push(file_obj[i]);
          $("#layer_cha_join .layer_cha_join_file_desc").append(
            '<div class="file_desc" id="chall_file_desc_' +
              file_number +
              '">' +
              "<span>" +
              file_name +
              '</span><button id="mix_file_del_' +
              file_number +
              '">삭제</button></div>'
          );
        }
      }
    }
    $(".file_desc").show();

    if (input_type_mix || fileobj) {
      $(".btns_cha_join").addClass("on");
    } else {
      $(".btns_cha_join").removeClass("on");
    }
  });

  //챌린지참여, 파일첨부 삭제버튼
  $(document).on(
    "click",
    "#layer_cha_join button[id^='mix_file_del_']",
    function (event) {
      var id = $(this).attr("id");
      var no = id.replace("mix_file_del_", "");
      if (fileListArr.length > 0) {
        for (i = 0; i < fileListArr.length; i++) {
          if (no == i) {
            //var f = fileListArr[i];
            //console.log(f);
            fileListArr.splice(no, 1);
            // $(this).parent().remove();
          }
        }
      }
      var input_type_mix = $("textarea[id=input_type_mix]").val();
      $("#chall_file_desc_" + no).remove();
      // $("#mix_file_" + no).val("");
      var file_box_cnt = $(".layer_cha_join_file_desc .file_desc").length;
      if (!input_type_mix && file_box_cnt == 0) {
        $(".btns_cha_join").removeClass("on");
      }
    }
  );

  //챌린지참여하기, 혼합형
  $(document).on("click", ".join_type_mix #btns_cha_join", function (event) {
    // console.log("혼합형 참여");

    var mix_file_val = $("#file_01").val();
    var fileobj = $("#file_01");
    var input_type_mix_val = $("#input_type_mix").val();

    if (input_type_mix_val == "" && $("#mix_file_name").val() == "") {
      if (mix_file_val == "") {
        alert("해당 챌린지에 참여 하려면 메시지나 파일을 첨부해 주세요.");
        return false;
      }
    }

    if (confirm("챌린지에 참여 하시겠습니까?")) {
      var idx = $("#view_idx").val();
      var fdata = new FormData();

      // if ($("input[id='file_01']").val()) {
      //   fdata.append("files[]", $("input[id='file_01']")[0].files[0]);
      // }

      if (fileobj.val()) {
        if (fileListArr.length > 0) {
          for (i = 0; i < fileListArr.length; i++) {
            // console.log(fileListArr[i]);
            fdata.append("files[]", fileListArr[i]);
          }
        }
        // return false;
      }

      fdata.append("idx", idx);
      fdata.append("message", input_type_mix_val);
      fdata.append("chamyeo_idx", $("#input_type_idx").val());

      fdata.append("mode", "challenges_mix");

      console.log("message : " + input_type_mix_val);
      console.log("chamyeo_idx == " + $("#input_type_idx").val());
      console.log($("#mix_file_name").val());
      console.log(mix_file_val);
      console.log("혼합형 참여결과 ");

      $.ajax({
        type: "POST",
        data: fdata,
        contentType: false,
        processData: false,
        url: "/inc/challenges_process.php",
        beforeSend: function () {
          $(".rewardy_loading_01").css("display", "block");
        },
        complete: function () {
          $(".rewardy_loading_01").css("display", "none");
        },
        success: function (data) {
          console.log(data);
          if (data) {
            tdata = data.split("|");
            if (tdata) {
              var result = tdata[0];
              var cnt = tdata[1];
              if (result == "complete") {
                alert("참여가 완료 되었습니다.");
                location.reload();
                return false;
              } else if (result == "file_info") {
                alert("이미 참여 하였습니다.");
                return false;
              } else if (result == "coin_info") {
                alert("코인 지급 받은 내역이 있어 참여하실수 없습니다.");
                return false;
              } else if (result == "day_expire") {
                alert("참여 기간이 만료되었습니다.");
                return false;
              } else if (result == "file_not_upload") {
                alert(
                  "파일이 첨부되지 않았습니다.\n파일을 다시 확인해 주세요."
                );
                return false;
              } else if (result == "ch_notuser") {
                alert("해당 챌린지는 참여대상이 아닙니다.");
                return false;
              } else if (result == "chamyeo_max") {
                alert("챌린지 참여를 최대 " + cnt + "회 모두 참여 하였습니다.");
                return false;
              } else if (result == "mem_state") {
                alert("정상적인 회원이 아닙니다.\n회원정보를 확인 해주세요.");
                return false;
              } else if (result == "file_max_size") {
                alert(
                  "업로드한 파일용량이 5M초과 하였습니다.\n파일 용량을 다시 확인 해주세요."
                );
                return false;
              } else if (result == "not_query1") {
                alert("쿼리에러1");
                return false;
              } else if (result == "not_query2") {
                alert("쿼리에러2");
                return false;
              } else if (result == "not_query3") {
                alert("쿼리에러3");
                return false;
              }
            }
          }
        },
      });
    }
  });

  //챌린지참여, 파일첨부 삭제버튼
  // $(document).on("click", "button[id^='mix_file_del_']", function (event) {
  //   var id = $(this).attr("id");
  //   var no = id.replace("mix_file_del_", "");
  //   var input_type_mix = $("textarea[id=input_type_mix]").val();
  //   $("#mix_file_desc_" + no).html("");
  //   $("#mix_file_" + no).val("");
  //   if(!input_type_mix){
  //     $(".btns_cha_join").removeClass("on");
  //   }

  // });

  ////챌린지참여취소하기
  $(".btn_join_cancel").click(function () {
    //console.log("취소하기");
    if (confirm("해당 챌린지 참여 취소 하시겠습니까?")) {
      var idx = $("#view_idx").val();
      var fdata = new FormData();
      fdata.append("mode", "challenges_cancel");
      fdata.append("idx", idx);

      $.ajax({
        type: "POST",
        data: fdata,
        contentType: false,
        processData: false,
        url: "/inc/challenges_process.php",
        success: function (data) {
          console.log(data);
          if (data == "complete") {
            alert("챌린지 참여가 취소 되었습니다.");
            location.reload();
            return false;
          }
        },
      });
    }
  });

  //챌린지 수정하기
  $("#chall_edit").click(function () {
    var idx = $("#view_idx").val();
    var fdata = new FormData();
    fdata.append("mode", "challenges_edit_check");
    fdata.append("chall_idx", idx);
    $.ajax({
      type: "POST",
      data: fdata,
      contentType: false,
      processData: false,
      url: "/inc/process.php",
      success: function (data) {
        console.log(data);
        if (data == "complete") {
          location.href = "/challenge/write.php?idx=" + idx + "&edit=1";
          return false;
        } else if (data == "not") {
          alert("수정하기 권한이 없어 수정하실 수 없습니다.");
          return false;
        }
      },
    });
  });

  //챌린지 삭제하기
  $("#chall_delete").click(function () {
    if (confirm("챌린지를 삭제 하시겠습니까?")) {
      var idx = $("#view_idx").val();
      var fdata = new FormData();
      fdata.append("mode", "challenges_del");
      fdata.append("chall_idx", idx);

      $.ajax({
        type: "POST",
        data: fdata,
        contentType: false,
        processData: false,
        url: "/inc/process.php",
        success: function (data) {
          console.log(data);
          if (data) {
            tdata = data.split("|");
            if (tdata) {
              var result = tdata[0];
              var temp_idx = tdata[1];
              if (result == "complete") {
                if (temp_idx == "1") {
                  alert("삭제 처리 되었습니다.");
                  location.href = "/challenge/template.php";
                  return false;
                } else {
                  alert("삭제 처리 되었습니다.");
                  location.href = "/challenge/index.php";
                  return false;
                }
              } else if (result == "datarow") {
                alert("참여한 사용자가 있어 삭제를 할 수 없습니다.");
                return false;
              } else if (result == "not") {
                alert("챌린지 정보가 없어 삭제 할수 없습니다.");
                return false;
              }
            }
          }
        },
      });
    }
    console.log("삭제");
  });

  //템플릿 삭제하기
  $(document).on("click", "#template_delete", function () {
    if (confirm("해당 템플릿을 삭제 하시겠습니까?")) {
      var idx = $("#view_idx").val();
      var template_idx = $("#template_idx").val();

      // alert(idx+"|"+template_idx);
      // return false;

      var fdata = new FormData();
      fdata.append("mode", "challenges_del");
      fdata.append("chall_idx", idx);
      fdata.append("template_idx", template_idx);
      $.ajax({
        type: "POST",
        data: fdata,
        contentType: false,
        processData: false,
        url: "/inc/process.php",
        success: function (data) {
          console.log(data);
          if (data) {
            tdata = data.split("|");
            if (tdata) {
              var result = tdata[0];
              var temp_idx = tdata[1];
              if (result == "complete") {
                if (temp_idx == "1") {
                  alert("삭제 처리 되었습니다.");
                  location.href = "/challenge/template.php";
                  return false;
                } else {
                  alert("삭제 처리 되었습니다.");
                  location.href = "/challenge/index.php";
                  return false;
                }
              } else if (result == "datarow") {
                alert("참여한 사용자가 있어 삭제를 할 수 없습니다.");
                return false;
              } else if (result == "not") {
                alert("챌린지 정보가 없어 삭제 할수 없습니다.");
                return false;
              }
            }
          }
        },
      });
    }
    console.log("삭제");
  });

  // 템플릿 수정하기
  $(document).on("click", "#template_edit", function () {
    var idx = $("#view_idx").val();
    // var template_idx = $("#template_idx").val();

    var fdata = new FormData();
    fdata.append("mode", "challenges_edit_check");
    fdata.append("chall_idx", idx);

    $.ajax({
      type: "POST",
      data: fdata,
      contentType: false,
      processData: false,
      url: "/inc/process.php",
      success: function (data) {
        console.log(data);
        if (data == "complete") {
          location.href = "/challenge/write.php?idx=" + idx + "&edit=1";
          return false;
        } else if (data == "not") {
          alert("수정하기 권한이 없어 수정하실 수 없습니다.");
          return false;
        }
      },
    });
  });

  //뷰페이지 뒤로가기
  // $(".btn_back_list,#btn_back_list").click(function () {
  //   history.back(-1);
  // });

  $(document).on("click", "#btn_back_list", function () {
    var cate = $("#chall_cate").val();
    console.log(cate);

    var previousPage = document.referrer;
    // alert(previousPage);
    if (previousPage.indexOf("template") !== -1) {
      // alert("tempalte");
      location.href = "/challenge/template.php?cate=" + cate;
    } else {
      // alert("normal");
      history.back();
    }
    // var fdata = new FormData();
    // fdata.append("mode","back_chall");
    // fdata.append("idx",cate);

    // $.ajax({
    //   type: "POST",
    //   data: fdata,
    //   contentType: false,
    //   processData: false,
    //   url: "/inc/process.php",
    //   success: function (data) {
    //     location.href = "/challenge/template.php";
    //   },
    // });
  });

  //인증메시지 도전완료
  $("#btn_challenge_com").click(function () {
    alert("챌린지 도전을 이미 완료하였습니다.");
    return false;
  });

  //챌린지 도전하기
  $("#btn_challenge").click(function () {
    var idx = $("#view_idx").val();
    var fdata = new FormData();
    fdata.append("mode", "view_challenges");
    fdata.append("idx", idx);
    console.log(idx);
    $.ajax({
      type: "POST",
      data: fdata,
      contentType: false,
      processData: false,
      url: "/inc/process.php",
      success: function (data) {
        console.log(data);
        if (data) {
          tdata = data.split("|");
          if (tdata) {
            var result = tdata[0];
            var temp_idx = tdata[1];
            if (result == "complete") {
              $("#input_type_idx").val(temp_idx);
              $("#layer_cha_join").show();
            } else if (result == "row") {
              alert("참여내역 있음");
              return false;
            } else if (result == "exday") {
              alert("챌린지 참여기간이 아닙니다.");
              return false;
            } else if (result == "rxday") {
              alert("예정된 챌린지입니다. 시작일을 확인해 주세요.");
              return false;
            } else if (result == "holiday") {
              alert("이 챌린지는 공휴일은 도전하실수 없습니다.");
              return false;
            } else if (result == "not_chll") {
              alert("챌린지 대상자가 아닙니다.");
              return false;
            }
          }
        }
      },
    });
  });

  //챌린지뷰페이지 파일다운로드
  $("div[id^='file_down']").click(function () {
    var id = $(this).attr("id");
    var num = id.replace("file_down", "");
    var idx = $("#view_idx").val();
    var params = { idx: idx, num: num, page: "view" };

    var url = "/inc/file_download.php";
    var fdata = new FormData();
    fdata.append("idx", idx);
    fdata.append("num", num);

    $.ajax({
      type: "POST",
      data: fdata,
      contentType: false,
      processData: false,
      url: url,
      success: function (data) {
        //console.log(data);
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

    /*
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
  });*/
  });

  //로그아웃
  $("#logout").click(function () {
    logout();
  });

  //로그인
  $("#login").click(function () {
    if (GetCookie("user_id") == null) {
      $(".rew_layer_login").show();
    }
  });

  setTimeout(function () {
    $(".rew_cha_view_quick").addClass("on");
  }, 1100);

  //좌측메뉴 오늘업무버튼
  $(".rew_bar_li_01").click(function () {
    var id = $(this).attr("id");
    // if(id=='tutorial'){
    //  tutorial_insert();
    //  return false;
    // }else{

    if (GetCookie("user_id") != null) {
      location.href = "/todaywork/index.php";
    } else {
      if (location.search) {
        $("#rew_layer_setting").show();
      } else {
        $(".rew_layer_login").show();
      }
    }
    // }
  });

  //좌측메뉴 라이브
  $(".rew_bar_li_02").click(function () {
    if (GetCookie("user_id") != null) {
      location.href = "/live/index.php";
    } else {
      if (location.search) {
        $("#rew_layer_setting").show();
      } else {
        $(".rew_layer_login").show();
      }
    }
  });

  //좌측 로그
  $(".rew_bar_logo").click(function () {
    if (GetCookie("user_id") != null) {
      location.href = "/team/";
    } else {
      if (location.search) {
        $("#rew_layer_setting").show();
      } else {
        $(".rew_layer_login").show();
      }
    }
  });

  //좌측메뉴 보상
  $(".rew_bar_li_03").click(function () {
    if (GetCookie("user_id") != null) {
      location.href = "/reward/index.php";
    } else {
      if (location.search) {
        $("#rew_layer_setting").show();
      } else {
        $(".rew_layer_login").show();
      }
    }
  });

  //좌측메뉴 오늘업무버튼
  $(".rew_bar_li_04").click(function () {
    if (GetCookie("user_id") != null) {
      location.href = "/challenge/index.php";
    } else {
      if (location.search) {
        //location.reload();
        $("#rew_layer_setting").show();
      } else {
        $(".rew_layer_login").show();
      }
    }
  });

  //좌측메뉴 파티버튼
  $(".rew_bar_li_05").click(function () {
    if (GetCookie("user_id") != null) {
      location.href = "/party/index.php";
    } else {
      if (location.search) {
        //location.reload();
        $("#rew_layer_setting").show();
      } else {
        $(".rew_layer_login").show();
      }
    }
  });

  //좌측메뉴 파티버튼
  $(".rew_bar_li_06").click(function () {
    if (GetCookie("user_id") != null) {
      location.href = "/insight/rank_c.php";
    } else {
      if (location.search) {
        //location.reload();
        $("#rew_layer_setting").show();
      } else {
        $(".rew_layer_login").show();
      }
    }
  });

  ////////////////////오늘업무////////////////////

  ////////////////////오늘업무////////////////////

  ////////////////////라이브////////////////////

  setTimeout(function () {
    //$(".rew_box").addClass("on");
    var bar_t = $(".rew_mypage_section .live_list_today_bar strong").text();
    var bar_b = $(".rew_mypage_section .live_list_today_bar span").text();
    var bar_w = (bar_t / bar_b) * 100;
    $(".rew_mypage_section .live_list_today_bar strong").css({
      width: bar_w + "%",
    });
  }, 400);

  $(".rew_conts_scroll_07").scroll(function () {
    var lbt = $(".rew_live").offset().top;
    if (lbt < 120) {
      $(".rew_live_tab").addClass("pos_fix");
    } else {
      $(".rew_live_tab").removeClass("pos_fix");
    }
  });

  $(".rew_conts_scroll_08").scroll(function () {
    var tbt = $(".rew_todaywork").offset().top;
    if (tbt < 140) {
      $(".tdw_date").addClass("pos_fix");
    } else {
      $(".tdw_date").removeClass("pos_fix");
    }
  });

  $(".layer_report .report_area").scroll(function () {
    var rct = $(".layer_report .report_area_in").position().top;
    if (rct < 60) {
      $(".layer_report .layer_result_right").addClass("pos_fix");
    } else {
      $(".layer_report .layer_result_right").removeClass("pos_fix");
    }
  });

  $(".layer_challenge .report_area").scroll(function () {
    var rat = $(".layer_challenge .report_area_in").position().top;
    if (rat < 50) {
      $(".layer_challenge .layer_result_right").addClass("pos_fix");
    } else {
      $(".layer_challenge .layer_result_right").removeClass("pos_fix");
    }
  });

  $(".btn_eval").click(function () {
    $(".layer_report").css({ opacity: 1 });
    $(".layer_report").show();
  });

  //헤더 작업 햄버거,각 메누
  $(".rew_mypage_close").click(function () {
    $(".rew_box").removeClass("on");
    $(".tdw_open_btn").show();
    $(".rew_menu").css({ top: "100%" });
  });

  $(".tdw_open_btn button").click(function () {
    let windowWidth = $(window).width();

    $(".rew_box").addClass("on");

    if (windowWidth < 540) {
      $(".rew_menu").css({ top: "calc(100% - 475px)" });
    } else {
      $(".rew_menu").css({ top: "calc(100% - 490px)" });
    }

    if ($(".rew_box").hasClass("on") === true) {
      $(".tdw_open_btn").css({ display: "none" });
    } else {
      $(".tdw_open_btn").css({ display: "block" });
    }
  });

  $(document).on("click", ".hamburger_btn", function () {
    $(this).toggleClass("on");

    if ($(this).hasClass("on")) {
      $(".rew_bar").css({ left: "0%" });
      $(".rew_bg_black").fadeIn();
    } else {
      $(".rew_bar").css({ left: "-55%" });
      $(".rew_bg_black").fadeOut();
    }
  });

  $(document).on("click", ".rew_bg_black", function () {
    $(".hamburger_btn").removeClass("on");
    $(".rew_bar").css({ left: "-55%" });
    $(this).fadeOut();
  });

  /*$(".live_list_cha_tit, .live_list_cha_count").click(function() {
      $(".layer_challenge .report_cha .rew_cha_list_ul li").removeClass("sli");
      $(".layer_challenge").show();
      $(".layer_challenge .report_cha .rew_cha_list_ul li").each(function() {
        var tis = $(this);
        var tindex = $(this).index();
        setTimeout(function() {
          tis.addClass("sli");
        }, 600 + tindex * 200);
      });
    });



    $(".live_list .live_list_box").each(function() {
      var tis = $(this);
      var tindex = $(this).index();
      var bar_t = tis.find(".live_list_today_bar strong").text();
      var bar_b = tis.find(".live_list_today_bar span").text();
      var bar_w = bar_t / bar_b * 100;
      setTimeout(function() {
        tis.addClass("sli");
        tis.find(".live_list_today_bar strong").css({ width: bar_w + "%" });
      }, 600 + tindex * 200);
    });*/

  // $(".live_user_state li .live_user_state_circle").mouseenter(function () {
  //   $(".layer_state").removeClass("on");
  //   $(this).next(".layer_state").addClass("on");
  //   $(".live_list_box").removeClass("zindex");
  //   $(this).closest(".live_list_box").addClass("zindex");
  // });
  // $(".live_user_state li .live_user_state_circle").mouseleave(function () {
  //   $(".layer_state").removeClass("on");
  //   $(".live_list_box").removeClass("zindex");
  // });

  $(".layer_report").hide();

  $(".layer_result .layer_close button").click(function () {
    $(".layer_result").hide();
  });

  //라이브 레이어 닫기
  $(".layer_report .layer_close button").click(function () {
    $(".layer_report").hide();
    $(".layer_report").css({ opacity: 0 });
  });

  ////////////////////라이브////////////////////

  ////////////////////리워디 가입하기////////////////////
  //접속하기 클릭
  $("#rewardy_team").click(function () {
    //location.href = "/team/";
    if (GetCookie("user_id") == null) {
      $(".rew_layer_login").show();
    } else {
      location.href = "/team/index.php";
    }
  });

  //접속하기 클릭
  $("#rewardy_join").click(function () {
    $("#rewardy_layer_join").show();
    $("#rewardy_join_id").focus();
  });

  //리워디 회사 가입하기
  $("#rewardy_join_btn").click(function () {
    if ($("#rewardy_join_id").val() == "") {
      alert("가입할 이메일을 입력해주세요.");
      $("#rewardy_join_id").focus();
      return false;
    }

    var fdata = new FormData();
    fdata.append("mail[]", $("#rewardy_join_id").val());

    if (confirm("입력하신 이메일로 인증메일을 발송 하시겠습니까?")) {
      fdata.append("mode", "sendmail");
      $.ajax({
        type: "POST",
        data: fdata,
        contentType: false,
        processData: false,
        url: "/inc/sendmail_rewardy.php",
        success: function (data) {
          console.log(data);
          if (data == "ok") {
            alert("메일이 정상 발송되었습니다.");
            location.replace("/");
          } else if (data == "fail") {
            alert("메일 발송이 되지 않았습니다.");
            return false;
          }
        },
      });
    }
  });

  //메일발송버튼
  $("#rewardy_member_sendmail_btn").click(function () {
    var fdata = new FormData();
    var mail_cnt = $("input[id^='mail']").length;

    for (var i = 0; i < mail_cnt; i++) {
      //console.log(" :: " + $("input[id=mail_name" + i + "]").val());

      if (!$("input[id=member_name" + i + "]").val()) {
        alert("이름을 입력하세요.");
        $("input[id=member_name" + i + "]").focus();
        return false;
      }

      if (!$("input[id=member_part" + i + "]").val()) {
        alert("부서명을 입력하세요.");
        $("input[id=member_part" + i + "]").focus();
        return false;
      }

      if (!$("input[id=mail" + i + "]").val()) {
        alert("이메일을 입력하세요.");
        $("input[id=mail" + i + "]").focus();
        return false;
      }

      fdata.append(
        "member_name[" + i + "]",
        $("input[id=member_name" + i + "]").val()
      );
      fdata.append(
        "member_part[" + i + "]",
        $("input[id=member_part" + i + "]").val()
      );
      fdata.append("mail[" + i + "]", $("input[id=mail" + i + "]").val());
    }

    fdata.append("highlevel", "5");

    if (
      confirm("입력한 " + mail_cnt + "개의 초대 이메일을 발송하시겠습니까?")
    ) {
      fdata.append("mode", "sendmail");
      $.ajax({
        type: "POST",
        data: fdata,
        contentType: false,
        processData: false,
        url: "/inc/sendmail_rewardy.php",
        success: function (data) {
          console.log(data);
          if (data == "ok") {
            alert("메일이 정상 발송되었습니다.");
            location.replace("/admin/member_list.php");
          } else if (data == "fail") {
            alert("메일 발송이 되지 않았습니다.");
            return false;
          }
        },
      });
    }
  });

  //리워디 회원 가입하기
  $("#rewardy_member_join_btn").click(function () {
    if (!$("#z8").val()) {
      alert("이름을 입력해주세요.");
      $("#z8").focus();
      return false;
    }

    if (!$("#z9").val()) {
      alert("부서명을 입력해주세요.");
      $("#z9").focus();
      return false;
    }

    if (!$("#z10").val()) {
      alert("회사명을 입력해주세요.");
      $("#z10").focus();
      return false;
    }

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
      $("#z11").focus();
      return false;
    }

    if (confirm("입력하신 내용으로 가입 하시겠습니까?")) {
      var fdata = new FormData();
      fdata.append("mode", "rewardy_join");

      fdata.append("email", $("#z7").val());
      fdata.append("name", $("#z8").val());
      fdata.append("part", $("#z9").val());
      fdata.append("corp", $("#z10").val());
      fdata.append("corp_join", "1");
      fdata.append("password", $("#z11").val());
      fdata.append("password_chek", $("#z12").val());

      $.ajax({
        type: "POST",
        data: fdata,
        contentType: false,
        processData: false,
        url: "/inc/process.php",
        success: function (data) {
          console.log(data);
          if (data == "complete") {
            alert("가입되었습니다.");
            location.href = "/admin/member_list.php";
            return false;
          } else if (data == "rejoin") {
            alert("이미 가입된 정보 입니다.");
            return false;
          } else if (data == "member_same") {
            alert("이미 가입되어 있는 이메일 주소입니다.");
            return false;
          }
        },
      });
    }
  });

  //리워디 회원 가입하기(메일 확인 후 버튼)
  $("#rewardy_member_add_join_btn").click(function () {
    if (!$("#z8").val()) {
      alert("이름을 입력해주세요.");
      $("#z8").focus();
      return false;
    }

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
      $("#z11").focus();
      return false;
    }

    if (confirm("입력하신 내용으로 가입 하시겠습니까?")) {
      var fdata = new FormData();
      fdata.append("mode", "rewardy_join");

      fdata.append("email", $("#z7").val());
      fdata.append("name", $("#z8").val());
      fdata.append("part", $("#z9").val());
      fdata.append("corp", $("#z10").val());
      fdata.append("password", $("#z11").val());
      fdata.append("password_chek", $("#z12").val());
      fdata.append("highlevel", $("#highlevel").val());

      $.ajax({
        type: "POST",
        data: fdata,
        contentType: false,
        processData: false,
        url: "/inc/process.php",
        success: function (data) {
          console.log(data);
          if (data) {
            data = data.trim();
            if (data == "complete") {
              alert("가입되었습니다.");
              location.href = "/team/";
            } else if (data == "rejoin") {
              alert("이미 가입된 정보 입니다.");
            }
          }
        },
      });
    }
  });

  $("#uptest").click(function () {
    alert("UP");
    var val = $("#ch_file_01").val();
    if (val == "") {
      alert("해당 챌린지에 참여 할려면 파일을 첨부해 주세요.");
      return false;
    } else {
      if (confirm("챌린지에 참여 하시겠습니까?")) {
        var idx = $("#view_idx").val();
        var fdata = new FormData();
        fdata.append("mode", "challenges_file");

        if ($("input[id='ch_file_01']").val()) {
          fdata.append("files[]", $("input[id='ch_file_01']")[0].files[0]);
        }
        fdata.append("idx", idx);
        fdata.append("chamyeo_idx", $("#input_type_idx").val());

        $.ajax({
          type: "POST",
          data: fdata,
          contentType: false,
          processData: false,
          url: "/test/uptest.php",
          success: function (data) {
            console.log(data);
          },
        });
      }
    }
  });

  //링크연결(내코인)
  $("#mycoin,#rew_mypage_coin_chall").click(function () {
    if (GetCookie("user_id") != null) {
      location.href = "/reward/index.php";
    } else {
      $(".rew_layer_login").show();
    }
  });

  //테마선택 생성버튼
  $("#btn_thema").click(function () {
    $(".layer_thema").show();
  });

  //테마 레이어 닫기
  $(".layer_thema .layer_user_cancel").click(function () {
    var thema_list_check = $(".btn_tdw_list_chk").length;
    var check_thema = $("#chall_thema_chk").val();

    if (check_thema) {
      var check_thema_arr = check_thema.split(",");
    }

    var check_total_cnt = $("#thema_info_cnt").val();

    if (!check_thema_arr) {
      for (var i = 0; i < thema_list_check; i++) {
        if ($(".btn_tdw_list_chk").eq(i).hasClass("on") == true) {
          $(".btn_tdw_list_chk").eq(i).removeClass("on");
        }
      }
    } else {
      for (var i = 0; i < check_total_cnt; i++) {
        eq_i = i + 1;
        if (
          $.inArray($(".btn_tdw_list_chk").eq(i).val(), check_thema_arr) == -1
        ) {
          $(".btn_tdw_list_chk").eq(i).removeClass("on");
        } else {
          $(".btn_tdw_list_chk").eq(i).addClass("on");
        }
      }
    }

    $(".layer_thema").hide();
  });

  //테마 제목 클릭시 수정
  //$(".layer_thema .tdw_list .tdw_list_desc p").click(function() {
  //$(document).on("click", "#btn_list_thema_del", function() {
  $(document).on("click", "p[id^=tdw_list_desc_thema]", function () {
    var obj_edit = $("p[id^=tdw_list_desc_thema]");
    var obj_edit_cnt = obj_edit.size();

    var id = $(this).attr("id");
    var no = id.replace("tdw_list_desc_thema_", "");

    if (no) {
      var elem = $("textarea[id=textarea_regi_thema_" + no + "]");
      setTimeout(function () {
        var input = elem;
        var v = input.val();
        input.focus().val("").val(v);
      }, 50);
      $(this).next().next(".tdw_list_regi").show();

      var memo_width = $("#textarea_regi_thema_" + no + "").width();
      $(this)
        .next("#textarea_regi_thema_" + no + "")
        .css({ width: memo_width + 199 });

      //해당 글내용 이외에 인풋박스 닫기
      for (i = 0; i < obj_edit_cnt; i++) {
        obj_edit_id = obj_edit.eq(i).attr("id");
        obj_edit_no = obj_edit
          .eq(i)
          .attr("id")
          .replace("tdw_list_desc_thema_", "");
        if (no != obj_edit_no) {
          $("#tdw_list_regi_thema_" + obj_edit_no).hide();
        }
      }
    }

    //$(this).next().next(".tdw_list_regi").show();
  });

  //테마 삭제버튼
  $(document).on("click", "#btn_list_thema_del", function () {
    var val = $(this).val();
    if (val) {
      if (confirm("선택한 테마를 삭제 하시겠습니까?")) {
        var fdata = new FormData();
        fdata.append("mode", "challenges_thema_del");
        fdata.append("thema_idx", val);

        $.ajax({
          type: "post",
          data: fdata,
          contentType: false,
          processData: false,
          url: "/inc/process.php",
          success: function (data) {
            console.log(data);
            if (data) {
              tdata = data.split("|");
              if (tdata) {
                //var html = tdata[0];
                var result = tdata[0];
                var totcnt = tdata[1];

                console.log("result :: " + result);
                console.log("val :: " + val);

                if (result == "complete") {
                  $("#thema_list_cnt").text("전체 " + totcnt + "개");
                  $("#tdw_list_desc_thema_" + val)
                    .closest("li")
                    .remove();
                }
              }
            }
          },
        });
      }
    }
  });

  //테마 선택 등록 하기
  $(document).on("click", "#thema_select_btn", function () {
    $("#thema_che_list").html("");
    var thema_check = 0;
    var thema_check_idx = new Array();
    var thema_list_check = $(".btn_tdw_list_chk").length;
    var check_thema = new Array();
    $("#thema_che_list").css("display", "block");
    for (var i = 0; i < thema_list_check; i++) {
      if ($(".btn_tdw_list_chk").eq(i).hasClass("on") == true) {
        che_t = $(".btn_tdw_list_chk").eq(i).val();
        che_text = $("#tdw_list_desc_thema_" + che_t).text();
        che_text =
          "<strong style='margin-right:10px'>" + che_text + "</strong>";
        thema_check++;
        thema_check_idx.push(che_t);
        $("#thema_che_list").append(che_text);
      }
    }

    if (thema_check == 0) {
      alert("등록할 테마를 선택해 주세요.");
      return false;
    }

    if (thema_check_idx) {
      $("#chall_thema_chk").val(thema_check_idx);

      // for (var i = 0; i < thema_list_check; i++) {
      //   if ($(".btn_tdw_list_chk").eq(i).hasClass("on") == true) {
      //     $(".btn_tdw_list_chk").eq(i).removeClass("on");
      //   }
      // }
      $(".layer_thema").hide();
    }
  });

  //테마 수정 취소 버튼
  $(document).on("click", "#btn_regi_thema_cancel", function () {
    var val = $(this).val();
    if (val) {
      $("#textarea_regi_thema_" + val).val(
        $("#tdw_list_desc_thema_" + val).text()
      );
    }

    $(this).closest(".tdw_list_regi").hide();
  });

  //테마 체크 선택
  $(document).on("click", "#btn_tdw_list_thema_chk", function () {
    $(this).toggleClass("on");

    thema_check = 0;
    var thema_list_check = $("button[id='btn_tdw_list_thema_chk']").length;

    for (var i = 0; i < thema_list_check; i++) {
      if (
        $("button[id='btn_tdw_list_thema_chk']").eq(i).hasClass("on") == true
      ) {
        thema_check++;
      }
    }

    if ($("#thema_info_cnt").val()) {
      thema_list_cnt = $("#thema_info_cnt").val();
    }

    if (thema_check > 0) {
      $("#thema_list_cnt").text("전체 " + thema_list_cnt + "개");
      $("#thema_list_cnt").text(
        $("#thema_list_cnt").text() + ", " + thema_check + "개 선택"
      );

      if ($("#thema_select_btn").hasClass("on") == false) {
        $("#thema_select_btn").addClass("on");
      }
    } else {
      $("#thema_list_cnt").text("전체 " + thema_list_cnt + "개");
      if ($("#thema_select_btn").hasClass("on") == true) {
        $("#thema_select_btn").removeClass("on");
      }
    }
  });

  // 내 정보 코인페이지 이동
//  

  //테마 추가
  $("#thema_add").click(function () {
    var input_search = $("#input_thema_search").val();
    if (!input_search) {
      alert("새로 추가할 테마명을 입력해주세요.");
      $("#input_thema_search").focus();
      return false;
    }

    var fdata = new FormData();
    fdata.append("mode", "challenges_thema_add");
    fdata.append("thema_title", input_search);

    $.ajax({
      type: "post",
      data: fdata,
      contentType: false,
      processData: false,
      url: "/inc/process.php",
      success: function (data) {
        console.log(data);

        if (data) {
          tdata = data.split("|");
          if (tdata) {
            var html = tdata[0];
            var totcnt = tdata[1];
            var result = tdata[2];

            if (html) {
              $("#thema_list_add").html(html);
            } else {
              if (totcnt == "thema_over") {
                alert(
                  "현재 테마명(" +
                    result +
                    ")이 존재 합니다.\n새로 추가할 테마명을 입력해주세요."
                );
                $("#input_thema_search").focus();
                return false;
              }
            }
          }
        }
      },
    });
  });

  //테마 제목 수정
  $(document).on("click", "#btn_regi_thema_submit", function () {
    var val = $(this).val();
    var fdata = new FormData();
    var contents = $("#textarea_regi_thema_" + val).val();

    fdata.append("mode", "thema_title_edit");
    fdata.append("idx", val);
    fdata.append("contents", contents);

    $.ajax({
      type: "POST",
      data: fdata,
      contentType: false,
      processData: false,
      url: "/inc/process.php",
      success: function (data) {
        console.log(data);
        if (data == "complete") {
          challenges_thema_list();
          return false;
        }
      },
    });
  });

  //추후 수정이 필요한 스크립트
  //####챌린지 좌측 메뉴-임시저장챌린지
  $(document).on("click", ".rew_mypage_tab_02 ul li:eq(1)", function () {
    //임시저장 챌린지 1이상 검색
    var temp_chall = $("#temp_chall").val();

    if (temp_chall == 0) {
      return false;
    }

    var fdata = new FormData();
    console.log("####### page " + $("#pageno").val());

    var page = parseInt($("#pageno").val());
    var page_count = parseInt($("#page_count").val());
    var rank = $("#btn_sort_on").val();
    var thema_idx = parseInt($("#thema_idx").val());

    if (thema_idx) {
      fdata.append("thema_idx", thema_idx);
    }

    //if ($("#input_search_thema").val()) {
    //    fdata.append("search", $("#input_search_thema").val());
    //}

    if ($("#thema_zzim").val()) {
      fdata.append("zzim", 1);
    }

    fdata.append("temp", 1);

    fdata.append("rank", rank);
    fdata.append("page", page);
    fdata.append("page_count", page_count);
    fdata.append("mode", "challenges_template_list_check");

    //전체
    //if ($("#cha_template_tab_all").is(":checked") == true) {
    //  fdata.append("viewchk", "all");
    //임시저장챌린지
    //} else if ($("#cha_chk_tab_save").is(":checked") == true) {
    fdata.append("viewchk", "1");
    //숨김챌린지
    //} else if ($("#cha_chk_tab_hide").is(":checked") == true) {
    //  fdata.append("viewchk", "2");
    //} else {
    //  fdata.append("viewchk", "");
    //}

    //$(".rew_cha_count strong").text("");

    $.ajax({
      type: "post",
      data: fdata,
      contentType: false,
      processData: false,
      url: "/inc/template_process.php",
      success: function (data) {
        console.log(data);
        if (data) {
          tdata = data.split("|");
          if (tdata) {
            var html = tdata[0];
            var totcnt = tdata[1];
            var listcnt = tdata[2];
            var lastcnt = tdata[3];

            if (totcnt == 0) {
              $("#template_more").hide();
            }

            $("#page_count").val(parseInt(listcnt));
            $("#thema_title").text("임시저장 챌린지");
            $(".rew_cha_count strong").text(totcnt);
            $(".rew_cha_thema_tit strong").text(totcnt);

            $(".rew_cha_thema_tit").eq(1).html("");
            $(".rew_cha_list").eq(1).html("");
            $(".rew_cha_sort").html("");
            $(".rew_cha_search").html("");

            //$("#template_list").html("");
            //$("#template_list").append(html);
            $("#rew_conts_in").html("");
            $("#rew_conts_in").html(html);

            $(".rew_cha_list_ul li:not('.sli')").each(function (aa) {
              var tis = $(this);
              var tindex = $(this).index();
              //alert(tindex);
              setTimeout(function () {
                tis.addClass("sli");
              }, 100 + tindex * 200);
            });

            console.log("lastcnt :: " + lastcnt);

            //더보기 버튼
            setTimeout(function () {
              if (lastcnt >= $("#page_count").val()) {
                //console.log(11);
                $(".rew_cha_more").hide();
              } else {
                //console.log(22);
                $(".rew_cha_more").show();
              }
            }, 3000);

            return false;
          }
        }
      },
    });
  });

  $(document).on("click", "#attend_over", function () {
    console.log("attned");
    location.href = "/challenge/view.php?idx=701";
  });

  $(document).on("click", "#tutorial_start", function () {
    var mode = "location";
    var url = "/inc/tu_process.php";

    var fdata = new FormData();
    fdata.append("mode", mode);
    fdata.append("url", url);

    $.ajax({
      type: "POST",
      url: url,
      data: fdata,
      contentType: false,
      processData: false,
      success: function (data) {
        console.log(data);
        data = data.trim();
        if (data == "none") {
          location.href = "/todaywork/tu_works.php";
        }
      },
    });
  });

  $(".tuto_phase_pause button").click(function () {
    $(".tuto_phase").hide();
  });

  $("#write_title").keyup(function () {
    check_input(this);
  });

  //좌측 로고클릭
  /*$(".rew_bar_logo a").click(function() {
      if (GetCookie("user_id") != null) {
        location.href = "/todaywork/index.php";
      } else {
        $(".t_layer").show();
      }
      //location.href = "/team/";
    });*/

  //좌측선택 - 테마리스트 정렬 : sortable
  if (GetCookie("user_id") == "marketing@bizforms.co.kr") {
    $(document).on("click", "#template_list", function () {
      $(this).sortable({
        opacity: 0.7,
        zIndex: 9999,
        //handle: ".tdw_list_drag",
        cursor: "move",
        containment: "parent",
        update: function (event, ui) {
          var fdata = new FormData();
          var listsort = $(this).sortable("serialize");
          fdata.append("mode", "thema_list_move");
          fdata.append("thema_idx", $("#thema_idx").val());
          fdata.append("listsort", listsort);
          $.ajax({
            type: "POST",
            data: fdata,
            contentType: false,
            processData: false,
            url: "/inc/template_process.php",
            success: function (data) {
              console.log(data);
              if (data == "complete") {
                //$(".calendar_num").text(data);
                return false;
              }
            },
          });
        },
      });
    });
    $("#template_list").disableSelection();
  }

  $("#btn_challenges_create").click(function () {
    location.href = "/challenge/template.php";
    return false;
  });

  //비밀번호 재설정
  $("#btn_repass").click(function () {
    $(".rew_layer_repass").show();
    $("#z3").focus();
  });

  //비밀번호 재설정
  $("#tl_sendmail_btn").click(function () {
    var obj = $("#z3");
    var send_email = obj.val();
    if (!send_email) {
      alert("이메일을 입력해주세요.");
      obj.focus();
      return false;
    }

    if (
      confirm(
        "입력한 메일 주소로 비밀번호를 초기화할 수 있는 링크를 전송합니다."
      )
    ) {
      var fdata = new FormData();
      fdata.append("mode", "tl_repass");
      fdata.append("send_email", send_email);

      $.ajax({
        type: "post",
        async: false,
        data: fdata,
        contentType: false,
        processData: false,
        url: "/inc/main_process.php",
        success: function (data) {
          console.log(data);
          data_str = data.trim();
          if (data_str == "complete") {
            alert("비밀번호 초기화 메일이 발송 되었습니다.");
            $("#z3").val("");
            $(".rew_layer_repass").hide();
            return false;
          } else if (data_str == "not") {
            alert(
              "입력하신 이메일 주소와 일치하는 정보가 없습니다.\n이메일 주소를 확인 해주세요."
            );
            obj.focus();
            return false;
          } else {
            console.log(data_str);
          }
        },
      });
    }
  });

  /*
    $("#template_list").sortable({
      opacity: 0.7,
      zIndex: 9999,
      //placeholder:"sort_empty",
      cursor: "move",
      containment: 'parent'
    });
    */

  //$("#template_list").disableSelection();

  $(window).on("load", function (e) {
    if ($(e.target).is("#textarea_memo")) {
      //    console.log("222");
    }

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

    if ($(".input_count").val() == "") {
      var sdate = $("#sdate").val();
      var edate = $("#edate").val();
      if (sdate && edate) {
        date_diff = dateDiff(sdate, edate);
        if (date_diff) {
          $(".input_count").val(date_diff);
        }
      }
    }
  });

  //좌측메뉴 열고 닫기처리
  //맴버관리, myinfo - 닫기
  if (
    $(location).attr("pathname").indexOf("/admin/") == 0 ||
    $(location).attr("pathname").indexOf("/myinfo/") == 0
  ) {
    if ($(".rew_warp_in .rew_box").hasClass("on") == true) {
      $(".rew_warp_in .rew_box").removeClass("on");
    }
  } else {
    if (GetCookie("onoff") == "1") {
      if ($(".rew_warp_in .rew_box").hasClass("on") == true) {
        //console.log("gg");
        $(".rew_warp_in .rew_box").removeClass("on");
      }
    }
  }

  $(document).on("click", ".btn_tuto_link_close", function () {
    var fdata = new FormData();
    var mode = "tuto_close";

    fdata.append("mode", mode);
    $.ajax({
      type: "post",
      data: fdata,
      contentType: false,
      processData: false,
      url: "/inc/process.php",
      success: function (data) {
        console.log(data);
        $(".tuto_link").css("display", "none");
      },
    });
  });
});

//배열 중복정리하여 해당 갯수 리턴
function str_over_filter(str) {
  var arr_str = str.filter(function (item, index) {
    return str.indexOf(item) === index;
  });

  return arr_str;
}

//챌린지뷰페이지 파일다운로드
function view_file_download(n) {
  //if (GetCookie("user_id") == 'sadary0@nate.com') {

  var idx = n;
  var mode = "challenges_file_down";
  var params = { idx: idx, page: "view", mode: "challenges_file_down" };
  //console.log(params);
  var fdata = new FormData();
  fdata.append("mode", mode);
  fdata.append("idx", idx);

  var url = "/inc/file_download.php";

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

  //아래 스크립트 동작안됨
  /*} else {
        var idx = n;
        var params = { idx: idx, page: "view", mode: "challenges_file_down" };
        var url = "/inc/file_download.php";
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
    }*/
}

function logout() {
  var fdata = new FormData();
  fdata.append("mode", "logout");
  $.ajax({
    type: "POST",
    data: fdata,
    contentType: false,
    processData: false,
    url: "/inc/logout.php",
    success: function (data) {
      if (data == "ok") {
        location.replace("/index.php");
        return false;
      }
    },
  });
}

//챌린지참여, 좌측 참여회원 리스트(인증 메시지)
function masage_user_list() {
  var idx = $("#view_idx").val();
  var fdata = new FormData();
  fdata.append("mode", "masage_user_list");
  fdata.append("idx", idx);

  $.ajax({
    type: "post",
    data: fdata,
    contentType: false,
    processData: false,
    url: "/inc/process.php",
    success: function (data) {
      console.log(data);
      if (data) {
        $(".layer_result_user .layer_result_user_in ul").html(data);
        //좌측 회원 전체 클릭
        //$(".layer_masage .layer_result_user_in ul li button").eq(0).trigger("click");
      }
    },
  });
}

//챌린지참여, 좌측 참여회원 리스트(인증 파일)
function file_user_list() {
  var idx = $("#view_idx").val();
  var fdata = new FormData();
  fdata.append("mode", "file_user_list");
  fdata.append("idx", idx);

  if ($("#user_date").val()) {
    fdata.append("user_date", $("#user_date").val());
  }

  $.ajax({
    type: "post",
    data: fdata,
    contentType: false,
    processData: false,
    url: "/inc/process.php",
    success: function (data) {
      console.log(data);
      if (data) {
        $(".layer_result_user .layer_result_user_in ul").html(data);
        //좌측 회원 전체 클릭
        //$(".layer_masage .layer_result_user_in ul li button").eq(0).trigger("click");
      }
    },
  });
}

//챌린지참여, 좌측 참여회원 리스트(혼합형)
function mix_user_list() {
  var idx = $("#view_idx").val();
  var fdata = new FormData();
  fdata.append("mode", "mix_user_list");
  fdata.append("idx", idx);

  if ($("#user_date").val()) {
    fdata.append("user_date", $("#user_date").val());
  }

  $.ajax({
    type: "post",
    data: fdata,
    contentType: false,
    processData: false,
    url: "/inc/process.php",
    success: function (data) {
      console.log(data);
      if (data) {
        $(".layer_result_user .layer_result_user_in ul").html(data);
        //좌측 회원 전체 클릭
        //$(".layer_masage .layer_result_user_in ul li button").eq(0).trigger("click");
      }
    },
  });
}

//챌린지 인증 파일 리스트
function auth_file_list() {
  //var val = $(this).val();
  var list_idx = $(".layer_result .layer_result_user_in ul li button").val();
  var idx = $("#view_idx").val();
  var fdata = new FormData();

  console.log("인증파일 리스트!!!");

  fdata.append("mode", "auth_file_list");
  fdata.append("list_idx", list_idx);
  fdata.append("idx", idx);
  //fdata.append("user_date", val);

  //if (val) {
  //   $("#user_date").val(val);
  //}

  if ($("#input_userfile").val()) {
    fdata.append("input_val", $("#input_userfile").val());
  }

  $.ajax({
    type: "post",
    data: fdata,
    contentType: false,
    processData: false,
    url: "/inc/process.php",
    success: function (data) {
      console.log("data  :::" + data);
      if (data) {
        $("#file_zone_list").html(data);
      }
    },
  });
}

//챌린지 인증 메시지, 리스트
function masage_list() {
  var idx = $("#view_idx").val();
  var user_date = $("#user_date").val();
  var user_email = $("#user_email").val();
  var fdata = new FormData();

  fdata.append("mode", "auth_masage_list");
  fdata.append("idx", idx);
  fdata.append("user_date", user_date);
  fdata.append("list_idx", user_email);

  $.ajax({
    type: "post",
    data: fdata,
    contentType: false,
    processData: false,
    url: "/inc/process.php",
    success: function (data) {
      console.log(data);
      if (data) {
        masage_user_list();
        $("#masage_zone_list").html(data);

        //$(".layer_result_user .layer_result_user_in ul").html(data);
        //좌측 회원 전체 클릭
        //$(".layer_masage .layer_result_user_in ul li button").eq(0).trigger("click");
      }
    },
  });
}

//챌린지 인증 파일, 리스트
function auth_file_list_top() {
  var idx = $("#view_idx").val();
  var user_date = $("#user_date").val();
  var user_email = $("#user_email").val();
  var fdata = new FormData();

  fdata.append("mode", "auth_file_list_top");
  fdata.append("idx", idx);
  fdata.append("user_date", user_date);
  fdata.append("list_idx", user_email);

  $.ajax({
    type: "post",
    data: fdata,
    contentType: false,
    processData: false,
    url: "/inc/process.php",
    success: function (data) {
      //console.log(data);
      if (data) {
        $(".rew_cha_view_result_in").html(data);
      }
    },
  });
}

//챌린지 인증 메시지, 리스트 상위3개
function view_masage_in() {
  var idx = $("#view_idx").val();
  var user_date = $("#user_date").val();
  var user_email = $("#user_email").val();
  var fdata = new FormData();

  fdata.append("mode", "masage_list_top3");
  fdata.append("idx", idx);
  fdata.append("user_date", user_date);
  fdata.append("list_idx", user_email);

  $.ajax({
    type: "post",
    data: fdata,
    contentType: false,
    processData: false,
    url: "/inc/process.php",
    success: function (data) {
      console.log(data);
      if (data) {
        //masage_user_list();
        $("#view_masage_in").html(data);
      }
    },
  });
}

//챌린지 혼합형 인증파일+인증메시지, 리스트 상위3개
function view_mix_in() {
  var idx = $("#view_idx").val();
  var fdata = new FormData();
  fdata.append("mode", "mix_list_top3");
  fdata.append("idx", idx);

  $.ajax({
    type: "post",
    data: fdata,
    contentType: false,
    processData: false,
    url: "/inc/process.php",
    success: function (data) {
      console.log("ddd :: " + data);
      if (data) {
        $("#rew_cha_view_mix_in").html(data);
      }
    },
  });
}

//챌린지 혼합형 인증파일+인증메시지, 리스트
function mix_list() {
  console.log("혼합형 리스트");

  var idx = $("#view_idx").val();
  var user_date = $("#user_date").val();
  var user_email = $("#user_email").val();
  var fdata = new FormData();

  fdata.append("idx", idx);
  fdata.append("user_date", user_date);
  fdata.append("list_idx", user_email);
  fdata.append("mode", "auth_mix_list");

  $.ajax({
    type: "post",
    data: fdata,
    contentType: false,
    processData: false,
    url: "/inc/process.php",
    success: function (data) {
      console.log(data);
      if (data) {
        $("#mix_zone_list").html(data);
      }
    },
  });
}

//URL, 받을이름, 넘길파라메터값:GET
function actsubmit(page, name, value) {
  var form = document.createElement("form");
  var hiddenField = document.createElement("input");
  hiddenField.setAttribute("type", "hidden");
  hiddenField.setAttribute("name", name);
  hiddenField.setAttribute("value", value);
  form.appendChild(hiddenField);

  form.setAttribute("method", "GET");
  form.setAttribute("action", page);
  document.body.appendChild(form);
  form.submit();
}

//URL, 받을이름, 넘길파라메터값:GET
function actsubmit_post(page, name, value) {
  var form = document.createElement("form");
  var hiddenField = document.createElement("input");
  hiddenField.setAttribute("type", "hidden");
  hiddenField.setAttribute("name", name);
  hiddenField.setAttribute("value", value);
  form.appendChild(hiddenField);

  form.setAttribute("method", "POST");
  form.setAttribute("action", page);
  document.body.appendChild(form);
  form.submit();
}

//챌린지 리스트
function challenges_ajax_list() {
  var fdata = new FormData();

  console.log("pageno ::::::: " + $("#pageno").val());

  var page = parseInt($("#pageno").val());
  var cate = 0;
  var page_count = parseInt($("#page_count").val());
  var chk_tab = "";

  //더보기 숨김
  if ($(".rew_cha_more").css("display") == "block") {
    $(".rew_cha_more").hide();
  }

  //카테고리선택
  $(".rew_cha_tab .rew_cha_tab_in ul li").each(function (index, item) {
    var no = $(this).index();
    if ($(".rew_cha_tab .rew_cha_tab_in ul li").eq(no).hasClass("on") == true) {
      cate = no;
    }
  });

  //카테고리선택 없을때 전체
  if (cate == 0) {
    cate = "all";
  }

  //챌린지 전체
  if ($(".rew_cha_chk_tab #cha_chk_tab_all").is(":checked") == true) {
    chk_tab_all = "all";
    fdata.append("chk_tab0", chk_tab_all);
    // chk_tab = "all";
    // fdata.append("chk_tab0", chk_tab);
  }

  //챌린지 도전가능한 챌린지
  if ($(".rew_cha_chk_tab #cha_chk_tab_wait").is(":checked") == true) {
    chk_tab_wait = "1";
    fdata.append("chk_tab1", chk_tab_wait);
    // chk_tab = "1";
    // fdata.append("chk_tab1", chk_tab);
  }

  //챌린지 도전중인 챌린지
  if ($(".rew_cha_chk_tab #cha_chk_tab_ing").is(":checked") == true) {
    chk_tab_ing = "2";
    fdata.append("chk_tab2", chk_tab_ing);
    // chk_tab = "2";
    // fdata.append("chk_tab2", chk_tab);
  }

  //챌린지 내가완료한 챌린지
  if ($(".rew_cha_chk_tab #cha_chk_tab_comp").is(":checked") == true) {
    chk_tab_comp = "3";
    fdata.append("chk_tab3", chk_tab_comp);
    // chk_tab = "3";
    // fdata.append("chk_tab3", chk_tab);
  }

  //챌린지 종료한 챌린지
  if ($(".rew_cha_chk_tab #cha_chk_tab_end").is(":checked") == true) {
    chk_tab_end = "4";
    fdata.append("chk_tab4", chk_tab_end);
    // chk_tab = "4";
    // fdata.append("chk_tab4", chk_tab);
  }

  //cate = "all";
  $(".rew_cha_tab_sort .rew_cha_tab_sort_in button:eq(0)").text("전체");

  //내가만든챌린지
  if ($("#chall_type").val()) {
    fdata.append("chall_type", $("#chall_type").val());
  }

  if ($("#tab_chall_02").hasClass("on") == true) {
    fdata.append("chall_type", "template");
  }

  // console.log(chk_tab);
  // return false;

  //console.log("cccc  ::" + $("#tab_chall_02").hasClass("on"));

  fdata.append("mode", "challenges_list");
  fdata.append("cate", cate);
  fdata.append("gp", page);
  fdata.append("rank", "4");
  $.ajax({
    type: "post",
    data: fdata,
    contentType: false,
    processData: false,
    url: "/inc/process.php",
    success: function (data) {
      console.log(data);

      if (data) {
        tdata = data.split("|");
        if (tdata) {
          // console.log(tdata[0]);
          var html = tdata[1];
          var totcnt = tdata[2];
          var listcnt = tdata[3];

          //페이지수
          $("#page_count").val(parseInt(listcnt));
          $(".rew_cha_count strong").text(totcnt);
          //    $(".rew_cha_list_ul").empty();

          $(".rew_cha_list_ul").html("");

          $(".rew_cha_list_ul").append(html);

          if (GetCookie("user_id") != "marketing@bizforms.co.kr") {
            $(".rew_cha_list_ul li").each(function () {
              var tis = $(this);
              var tindex = $(this).index();
              setTimeout(function () {
                tis.addClass("sli");
              }, 100 + tindex * 200);
            });
          }

          //더보기 버튼
          setTimeout(function () {
            if (page >= $("#page_count").val()) {
              $(".rew_cha_more").hide();
            } else {
              $(".rew_cha_more").show();
            }
          }, 10);
        }
        return false;
      }
    },
  });
}

//챌린지 테마 리스트 - 테마
function challenges_thema_ajax_list() {
  var fdata = new FormData();
  var page = parseInt($("#pageno").val());
  var page_count = parseInt($("#page_count").val());
  var rank = $("#btn_sort_on").val();
  var thema_idx = parseInt($("#thema_idx").val());

  //console.log("thema_idx ---- " + $("#thema_idx").val());
  //console.log("thema_idx ::::::: " + thema_idx);

  if (thema_idx) {
    fdata.append("thema_idx", thema_idx);
  }

  // if ($("#input_search_thema").val()) {
  //   fdata.append("search", $("#input_search_thema").val());
  // }

  if ($("#thema_zzim").val()) {
    fdata.append("zzim", 1);
  }

  //임시저장 정렬
  if ($("#thema_temp").val() == "1") {
    fdata.append("temp", 1);
  }

  temp_auth = $("#template_auth").val();
  if (temp_auth == "1") {
    fdata.append("temp_auth", "1");
  }

  fdata.append("rank", rank);
  fdata.append("page", page);
  fdata.append("page_count", page_count);
  fdata.append("mode", "challenges_template_list");

  //전체
  if ($("#cha_template_tab_all").is(":checked") == true) {
    fdata.append("viewchk_all", "1");
    //임시저장챌린지
  }

  if ($("#cha_chk_tab_save").is(":checked") == true) {
    fdata.append("viewchk_save", "1");
    //숨김챌린지
  }

  if ($("#cha_chk_tab_hide").is(":checked") == true) {
    fdata.append("viewchk_hide", "1");
  }

  if (
    $("#cha_template_tab_all").is(":checked") == false &&
    $("#cha_chk_tab_save").is(":checked") == false &&
    $("#cha_chk_tab_hide").is(":checked") == false
  ) {
    fdata.append("viewchk", "0");
  }

  //$("#thema_title").text("");
  //$(".rew_cha_count strong").text("");

  $.ajax({
    type: "post",
    data: fdata,
    contentType: false,
    processData: false,
    url: "/inc/template_process.php",
    success: function (data) {
      console.log(data);
      if (data) {
        tdata = data.split("|");
        if (tdata) {
          var html = tdata[0];
          var totcnt = tdata[1];
          var listcnt = tdata[2];
          var lastcnt = tdata[3];

          if (totcnt == 0) {
            $("#template_more").hide();
          }

          $("#page_count").val(parseInt(listcnt));
          $(".rew_cha_count strong").text(totcnt);

          //$("#template_list").html("");
          //$("#template_list").append(html);
          $("#rew_conts_in").html("");
          $("#rew_conts_in").html(html);

          if (GetCookie("user_id") == "marketing@bizforms.co.kr") {
            $("#template_list").trigger("click");
          }

          //if (GetCookie("user_id") == 'marketing@bizforms.co.kr') {

          /*    var tis = $(this);
                  var tindex = $(this).index();
                  //alert(tindex);
                  setTimeout(function() {
                    tis.addClass("sli");
                  }, 100 + tindex * 200);
                */

          //} else {

          $(".rew_cha_list_ul li:not('.sli')").each(function (aa) {
            var tis = $(this);
            var tindex = $(this).index();
            //alert(tindex);
            setTimeout(function () {
              tis.addClass("sli");
            }, 100 + tindex * 200);
          });

          //}

          //console.log("lastcnt :: " + lastcnt);

          //더보기 버튼
          setTimeout(function () {
            if (lastcnt >= $("#page_count").val()) {
              //console.log(11);
              $(".rew_cha_more").hide();
            } else {
              //console.log(22);
              $(".rew_cha_more").show();
            }
          }, 3000);

          $("#input_search_thema").focus();
          return false;
        }
      }
    },
  });
}

//챌린지 테마 리스트(전체/임시저장/숨김챌린지)
function challenges_thema_ajax_list_check() {
  var fdata = new FormData();

  console.log("####### page " + $("#pageno").val());

  var page = parseInt($("#pageno").val());
  var page_count = parseInt($("#page_count").val());
  var rank = $("#btn_sort_on").val();
  var thema_idx = parseInt($("#thema_idx").val());

  if (thema_idx) {
    fdata.append("thema_idx", thema_idx);
  }

  if ($("#input_search_thema").val()) {
    fdata.append("search", $("#input_search_thema").val());
  }

  if ($("#thema_zzim").val()) {
    fdata.append("zzim", 1);
  }

  fdata.append("rank", rank);
  fdata.append("page", page);
  fdata.append("page_count", page_count);
  fdata.append("mode", "challenges_template_list_check");

  //전체
  if ($("#cha_template_tab_all").is(":checked") == true) {
    fdata.append("viewchk_all", "1");
    //임시저장챌린지
  }

  if ($("#cha_chk_tab_save").is(":checked") == true) {
    fdata.append("viewchk_save", "1");
    //숨김챌린지
  }

  if ($("#cha_chk_tab_hide").is(":checked") == true) {
    fdata.append("viewchk_hide", "1");
  }

  if (
    $("#cha_template_tab_all").is(":checked") == false &&
    $("#cha_chk_tab_save").is(":checked") == false &&
    $("#cha_chk_tab_hide").is(":checked") == false
  ) {
    fdata.append("viewchk", "0");
  }

  //$("#thema_title").text("");
  //$(".rew_cha_count strong").text("");

  $.ajax({
    type: "post",
    data: fdata,
    contentType: false,
    processData: false,
    url: "/inc/template_process.php",
    success: function (data) {
      console.log(data);
      if (data) {
        tdata = data.split("|");
        if (tdata) {
          var html = tdata[1];
          var totcnt = tdata[2];
          var listcnt = tdata[3];
          var lastcnt = tdata[4];

          if (totcnt == 0) {
            $("#template_more").hide();
          }

          $("#page_count").val(parseInt(listcnt));
          $(".rew_cha_count strong").text(totcnt);

          $("#template_list").html("");
          $("#template_list").append(html);
          //$("#rew_conts_in").html("");
          //$("#rew_conts_in").html(html)

          $(".rew_cha_list_ul li:not('.sli')").each(function (aa) {
            var tis = $(this);
            var tindex = $(this).index();
            //alert(tindex);
            setTimeout(function () {
              tis.addClass("sli");
            }, 100 + tindex * 200);
          });

          console.log("lastcnt :: " + lastcnt);

          //더보기 버튼
          setTimeout(function () {
            if (lastcnt >= $("#page_count").val()) {
              //console.log(11);
              $(".rew_cha_more").hide();
            } else {
              //console.log(22);
              $(".rew_cha_more").show();
            }
          }, 3000);
          return false;
        }
      }
    },
  });
}

//챌린지 테마리스트
function challenges_thema_list() {
  var fdata = new FormData();
  var chk_tab = "";

  fdata.append("mode", "challenges_thema_list");
  //fdata.append("cate", cate);
  //fdata.append("gp", page);

  $.ajax({
    type: "post",
    data: fdata,
    contentType: false,
    processData: false,
    url: "/inc/process.php",
    success: function (data) {
      console.log(data);
      if (data) {
        tdata = data.split("|");
        if (tdata) {
          var html = tdata[0];
          var totcnt = tdata[1];
          $("#thema_list_cnt").text("전체 " + totcnt + "개");
          $("#thema_list_add").html(html);
        }
      }
    },
  });
}

function chall_more() {
  var page = parseInt($("#pageno").val());
  var page_count = parseInt($("#page_count").val());

  //console.log(page);
  //console.log(page_count);

  //더보기 버튼
  if ($(".rew_cha_more").css("display") == "block") {
    $(".rew_cha_more").hide();
  }
  /*setTimeout(function() {
      if (page >= page_count) {
        $(".rew_cha_more").hide();
      } else {
        $(".rew_cha_more").show();
      }
    }, 100);*/
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
function challenges_img_preview(event) {
  var input = this;
  var id = $(this).attr("id");
  var no = id.replace("file_", "");

  // console.log(input.files)
  if (input.files && input.files.length) {
    var reader = new FileReader();
    this.enabled = false;
    reader.onload = function (e) {
      //console.log(e)
      //let img = new Image();
      //img.src = e.target.result;
      //console.log( img.width);

      /*console.log(input.files[0]['name']);
      var img = $("#file_" + no);
      console.log(img.width);*/

      $("#file_desc_" + no).html(
        [
          '<div class="file_desc"><span><img src="',
          e.target.result,
          '"></span><button id="file_del_' + no + '">삭제</button></div>',
        ].join("")
      );
    };
    reader.readAsDataURL(input.files[0]);
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
    this.enabled = false;
    reader.onload = function (e) {
      //console.log(e)
      //let img = new Image();
      //img.src = e.target.result;
      //console.log( img.width);

      /*  console.log( input.files[0]['name']);
      var img = $("#img_file"+no);
      console.log( img.width );
    */

      $("#preview" + no).html(
        [
          '<img class="thumb" src="',
          e.target.result,
          '" title="',
          escape(e.name),
          '"/>',
        ].join("")
      );
    };
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

  //console.log(yyyy + "/" + mm + "/" + dd + " " + hh + ":" + ii + ":" + ss);

  //이전날:prev , 다음날:next
  if (str == "prev") {
    //newdate = new Date(d.setDate(d.getDate() - 1));
    newdate = new Date(
      d.getFullYear(),
      d.getMonth(),
      d.getDate() - 1,
      hh,
      ii,
      ss
    ); //.toLocaleDateString();
    //var newdate = new Date(d.setDate(d.getDate() - 1));
    //var newdate = new Date(Date.parse(d) - 1 * 1000 * 60 * 60 * 24);
  } else if (str == "next") {
    //newdate = new Date(d.setDate(d.getDate() + 1)); // (new Date(Date.parse(d) + 1 * 1000 * 60 * 60 * 24));
    newdate = new Date(
      d.getFullYear(),
      d.getMonth(),
      d.getDate() + 1,
      hh,
      ii,
      ss
    ); //.toLocaleDateString();
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

function setCookie(name, value, exdays) {
  var exdate = new Date();
  exdays = Number(exdays);
  if (exdays == 1) {
    exdate = new Date(
      Date.UTC(
        exdate.getFullYear(),
        exdate.getMonth(),
        exdate.getDate(),
        23,
        59,
        59
      )
    );
  } else {
    exdate.setDate(exdate.getDate() + exdays);
  }
  var c_value =
    escape(value) + (exdays == null ? "" : "; expires=" + exdate.toUTCString());
  document.cookie = name + "=" + c_value;
}

//날짜(년-월-일)
function getTodayType() {
  var date = new Date();
  return (
    date.getFullYear() +
    "-" +
    ("0" + (date.getMonth() + 1)).slice(-2) +
    "-" +
    ("0" + date.getDate()).slice(-2)
  );
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
  date = date.replace(/[^0-9-]/g, "").replace(/(\..*)\./g, "$1");

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
      date_sp.forEach(function (sp) {
        if (parseInt(sp) == 0) {
          isVaild = false;
        }
      });

      // 마지막 일 확인
      var last = new Date(
        new Date(date).getFullYear(),
        new Date(date).getMonth() + 1,
        0
      );
      // 일이 달의 마지막날을 초과했을 경우 다음달로 자동 전환되는 현상이 있음 (예-2월 30일 -> 3월 1일)
      if (parseInt(date_sp[1]) != last.getMonth() + 1) {
        var date_sp2 = date_sp.slice(0);
        date_sp2[2] = "01";
        var date2 = date_sp2.join("-");
        last = new Date(
          new Date(date2).getFullYear(),
          new Date(date2).getMonth() + 1,
          0
        );
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
      tmp += "-";
      tmp += date.substr(5, 2);
      tmp += "-";
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

//천단위 콤마
/*function addComma(val) {
    var input = val;
    var input = input.replace(/[\D\s\._\-]+/g, "");
    input = input ? parseInt(input, 10) : 0;
    return (input === 0) ? "" : input.toLocaleString("en-US");
}
*/

//콤마추가
function addComma(v) {
  if (v) {
    v = String(v);
    val = v.replace(/\B(?=(\d{3})+(?!\d))/g, ",");
    return val;
  }
}

//콤마제거
function unComma(v) {
  if (v) {
    v = String(v);
    val = v.replace(/,/g, "");
    return val;
  }
}

//숫자만추출
function reNumber(str) {
  if (str) {
    str = str.replace(/[^0-9]/g, "");
    return str;
  }
}

//챌린지 지급예상코인
function challenges_reward_coin() {
  //console.log(55555);
  var input_count = unComma($(".input_count").val());
  var input_coin = unComma($(".input_coin").val());
  var common_coin = unComma($("#common_coin").text());
  var sdate = $("#sdate").val();
  var edate = $("#edate").val();

  if (input_count) {
    input_count = Number(input_count);
  }

  if (input_coin) {
    input_coin = Number(input_coin);
  }

  date_diff = dateDiff(sdate, edate);
  var add_reward_coin;

  //전체
  if ($(".rew_cha_setting_user_area button").eq(0).hasClass("btn_on") == true) {
    if (date_diff >= 1) {
      //참여횟수 1보다 큰경우
      //if (input_count > 1) {

      //횟수 * 참여자수
      rcoin = input_count * member_total_cnt;

      //공용코인/(횟수*참여자수)
      //pcoin = Math.ceil(common_coin / (rcoin));

      //vpcoin = pcoin;
      vpcoin = rcoin * input_coin;

      //mod_coin = common_coin - vpcoin;

      //남은보유코인
      mod_coin = common_coin - vpcoin;

      //지급예상코인
      if (vpcoin) {
        vpcoin = vpcoin;
      } else {
        vpcoin = 0;
      }

      if (mod_coin <= 0) {
        $(".rew_cha_setting_coin_calc .calc_03 strong").css("color", "#f10006");
      } else {
        $(".rew_cha_setting_coin_calc .calc_03 strong").css("color", "#252525");
      }

      if ($("#not_coin_ico").hasClass("btn_chk_on") == true) {
        $(".rew_cha_setting_coin_calc .calc_02 strong").text("0");
        $(".rew_cha_setting_coin_calc .calc_03 strong").text(
          addComma(mod_coin)
        );
      } else {
        $(".rew_cha_setting_coin_calc .calc_02 strong").text(addComma(vpcoin));
        $(".rew_cha_setting_coin_calc .calc_03 strong").text(
          addComma(mod_coin)
        );
      }

      var coin = new Array();
      coin["0"] = vpcoin;
      coin["1"] = mod_coin;
      return coin;
    } else {
    }
  } else if (
    $(".rew_cha_setting_user_area button").eq(1).hasClass("btn_on") == true
  ) {
    //일부
    if ($("#chall_user_chk").val()) {
      user_chk_val = $("#chall_user_chk").val();
      arr_val = user_chk_val.split(",");
      if (arr_val.length > 0) {
        userchk = arr_val.length;
      } else {
        userchk = 0;
      }
    }

    if (date_diff > 1) {
      //참여횟수 1보다 큰경우
      //if (input_count > 1) {

      //횟수 * 참여자수
      rcoin = input_count * userchk;

      //공용코인/(횟수*참여자수)
      //pcoin = Math.ceil(common_coin / (rcoin));

      //vpcoin = pcoin;
      vpcoin = rcoin * input_coin;

      //mod_coin = common_coin - vpcoin;

      //남은보유코인
      mod_coin = common_coin - vpcoin;

      //지급예상코인
      if (vpcoin) {
        vpcoin = vpcoin;
      } else {
        vpcoin = 0;
      }

      if ($("#not_coin_ico").hasClass("btn_chk_on") == true) {
        $(".rew_cha_setting_coin_calc .calc_02 strong").text("0");
        $(".rew_cha_setting_coin_calc .calc_03 strong").text(
          addComma(common_coin)
        );
      } else {
        $(".rew_cha_setting_coin_calc .calc_02 strong").text(addComma(vpcoin));
        $(".rew_cha_setting_coin_calc .calc_03 strong").text(
          addComma(mod_coin)
        );
      }

      // $(".rew_cha_setting_coin_calc .calc_02 strong").text(addComma(vpcoin));
      // $(".rew_cha_setting_coin_calc .calc_03 strong").text(addComma(mod_coin));

      var coin = new Array();
      coin["0"] = vpcoin;
      coin["1"] = mod_coin;
      return coin;
    }
  } else {
    //console.log("xxxxxxxxx");
  }
}

function dateDiff(sdate, edate) {
  var sdt = new Date(sdate);
  var edt = new Date(edate);
  var daydiff =
    Math.ceil((edt.getTime() - sdt.getTime()) / (1000 * 3600 * 24)) + 1;

  var yyyy_s1 = sdt.getFullYear();
  var mm_s1 = sdt.getMonth();
  var ddd_s1 = sdt.getDate();

  var yyyy_e1 = edt.getFullYear();
  var mm_e1 = edt.getMonth();
  var ddd_e1 = edt.getDate();
  if (sdt == edt) {
    daydiff = 1;
  }

  return daydiff;
}

//날짜계산 공휴일제외
function calcDate(sdate, edate) {
  //var date1 = new Date(2017, 10, 30); // 2017-11-30
  //var date2 = new Date(2017, 11, 6); // 2017-12-6
  var date1 = new Date(sdate);
  var date2 = new Date(edate);

  var count = 0;
  while (true) {
    var temp_date = date1;
    if (temp_date.getTime() > date2.getTime()) {
      //console.log("count : " + count);
      return count;
      break;
    } else {
      var tmp = temp_date.getDay();
      if (tmp == 0 || tmp == 6) {
        // 주말
        //console.log("주말");
      } else {
        // 평일
        //console.log("평일");
        count++;
      }
      temp_date.setDate(date1.getDate() + 1);
    }
  }
}

function get_pagename() {
  var path = window.location.pathname;
  var page = path.split("/").pop();
  return page;
}

function get_date_diff() {
  var sdate = $("#sdate").val();
  var edate = $("#edate").val();

  if (sdate && edate) {
    var stmp = sdate.split("-");
    var syyyy = stmp[0];
    var smm = stmp[1];
    var sdd = stmp[2];

    var etmp = edate.split("-");
    var eyyyy = etmp[0];
    var emm = etmp[1];
    var edd = etmp[2];

    //var now = new Date(syyyy, smm, sdd);
    //var year = now.getFullYear(); // 연도
    //var month = now.getMonth() + 1; // 월
    //var day = now.getDate(); // 일

    var stDate = new Date(syyyy, smm, sdd);
    var endDate = new Date(eyyyy, emm, edd);

    var btMs = endDate.getTime() - stDate.getTime();
    var result = btMs / (1000 * 60 * 60 * 24) + 1;
    if (result == 0) {
      result = 1;
    }
    return result;
  }
}

function get_date_title_desc() {
  var get_day = get_date_diff();
  if (get_day) {
    //$(".rew_cha_setting_count .title_area .qna .title_desc").text("1일 " + get_day + "회 참여할 수 있어요.");
  }
}

$(document).ready(function () {
  $(".slider").slick({
    // Customize Slick options here
    autoplay: false,
    // autoplaySpeed: 2000,
    // dots: true,
    // arrows : true,
    // appendArrows:$('.slick-arrow'),
    prevArrow: $(".slick_prev"),
    nextArrow: $(".slick_next"),
    // More options: https://kenwheeler.github.io/slick/
  });

  if (navigator.userAgent.match(/Android/i)) {
    $("#loginbtn").addClass("ra_btn_login_mo");
    $("#chk_login").show();
    $("#chk_login_label").show();
  } else if (navigator.userAgent.match(/iPhone|iPad|iPod/i)) {
    $("#loginbtn").addClass("ra_btn_login_mo");
    $("#chk_login").show();
    $("#chk_login_label").show();
  } else if (navigator.userAgent.match(/Windows/i)) {
    $("#loginbtn").addClass("windowcheck");
  }
});

function check_input(obj) {
  var maxByte = 60; //최대 입력 바이트 수
  var str = obj.value;
  var str_len = str.length;

  var rbyte = 0;
  var rlen = 0;
  var one_char = "";
  var str2 = "";

  for (var i = 0; i < str_len; i++) {
    one_char = str.charAt(i);

    if (escape(one_char).length > 4) {
      rbyte += 2; //한글2Byte
    } else {
      rbyte++; //영문 등 나머지 1Byte
    }

    if (rbyte <= maxByte) {
      rlen = i + 1; //return할 문자열 갯수
    }
  }

  if (rbyte > maxByte) {
    alert(
      "한글 " +
        maxByte / 2 +
        "자 / 영문 " +
        maxByte +
        "자를 초과 입력할 수 없습니다."
    );
    str2 = str.substr(0, rlen); //문자열 자르기
    obj.value = str2;
    check_input(obj, maxByte);
  } else {
    //document.getElementById('byteInfo').innerText = rbyte;
    console.log(rbyte);
  }
}

//페이징 리스트
function list_pageing(list, str, page = "1") {
  var fdata = new FormData();

  var val = $("#kind").val();
  var tclass = $("#tclass").val();
  fdata.append("kind", val);
  fdata.append("this_class", tclass);
  fdata.append("string", str);

  mem_sea = $("#member_search").val();
  if (mem_sea) {
    fdata.append("member_search", mem_sea);
  }

  //캘린더 있을 경우
  var comcoin_sdate = $("#comcoin_sdate").val();
  var comcoin_edate = $("#comcoin_edate").val();

  if (comcoin_sdate && comcoin_edate) {
    fdata.append("sdate", comcoin_sdate);
    fdata.append("edate", comcoin_edate);
  }
  //특수필터
  var sort_kind = $("#sort_kind").val();
  if (sort_kind) {
    fdata.append("sort_kind", sort_kind);
  }
  // 이메일 있을 경우
  var member_email = $("#email_history").val();
  if (member_email) {
    fdata.append("email", member_email);
  }
  url = "/inc/member_process.php";
  //console.log(" str : " + str);
  if (list) {
    //indexval = url.indexOf("customer");
    //console.log("url ::::: " + url);
    if (list == "reward") {
      fdata.append("mode", "reward_list");
      var comcoin_sdate = $("#reward_sdate").val();
      var comcoin_edate = $("#reward_edate").val();
      fdata.append("sdate", comcoin_sdate);
      fdata.append("edate", comcoin_edate);
      url = "/inc/reward_process.php";
    } else if (list == "comcoin_mem") {
      fdata.append("mode", "member_comcoin_list");
      //멤버별 공용코인
    } else if (list == "member") {
      fdata.append("mode", "comcoin_list");
    } else if (list == "member_nocal") {
      fdata.append("mode", "comcoin_list_nocal");
    } else if (list == "inc") {
      fdata.append("mode", "comcoin_list");
    } else if (list == "history") {
      fdata.append("mode", "history_list");
    } else if (list == "history_nocal") {
      var member_email = $("#email_history").val();
      fdata.append("mode", "history_list_nocal");
    } else if (list == "member_list") {
      fdata.append("mode", "member_list");
      var member_sort = $("#btn_sort_on").val();
      fdata.append("member_sort", member_sort);
    } else if (list == "party") {
      fdata.append("mode", "party_view");
      fdata.append("party_idx", $("#party_idx").val());
      var comcoin_sdate = $("#project_sdate").val();
      var comcoin_edate = $("#project_edate").val();
      fdata.append("sdate", comcoin_sdate);
      fdata.append("edate", comcoin_edate);
      url = "/inc/project_process.php";
    }

    // 20230127_코인출금 신청내역에 따른 js 추가 if(list==member,inc)
    // 20230130_멤버별 공용코인_지급,회수에 따른 js 추가 if(list==history)
    fdata.append("p", page);

    $.ajax({
      type: "POST",
      data: fdata,
      contentType: false,
      processData: false,
      url: url,
      success: function (data) {
        var array = [
          "member_list",
          "history_nocal",
          "history",
          "member_nocal",
          "member",
        ];
        console.log(data);
        if (data) {
          var tdata = data.split("|");
          if (tdata) {
            var result = tdata[0];
            var result1 = tdata[1];
            var result2 = tdata[2];
            if (list == "reward") {
              $("#rew_ard_in").html(result);
            } else if (list == "comcoin_mem") {
              $("#rew_member_list").html(result);
              $("#rew_ard_paging").html(result1);
              $("#rew_member_count strong").text(result2);
            } else if (list == "inc") {
              $("#rew_list_search").html(result);
            } else if (list == "party") {
              $("#tdw_list_ww").html(result);
              $(".rew_ard_paging").html(result1);
            } else if (array.includes(list)) {
              $("#list_paging").html(result);
            }
          }
        }
      },
    });
  }
}

//입력폼 벗어날때
function blured(id) {
  var input = $("#" + id).val();
  var v = addComma(input);
  $("#" + id).prop("type", "text");
  $("#" + id).val(v);
}

//입력폼 포커스
function focused(id) {
  var input = $("#" + id).val();
  var v = input.replace(/,/gi, "");
  $("#" + id).val(v);
}

//받은사람 레이어 초기화
function layer_user_reset() {
  //선택된 내용 모두 제거 처리
  var abt = $(".layer_user_info dd button");
  for (var i = 0; i < abt.length; i++) {
    if (abt.eq(i).hasClass("on") == true) {
      abt.eq(i).removeClass("on");
      $(".layer_user_slc_list_in #user_" + abt.eq(i).val() + "").remove();
    }
  }
  $("#chall_user_chk").val("");

  $(".layer_user_slc_list_in").hide();
  $(".layer_user_box").addClass("none");

  $("#usercnt").text("전체 " + $("#chall_user_cnt").val() + "명");
}

//파일다운로드
function fdownload(filename, filepath) {
  var element = document.createElement("a");
  var ht = window.location.protocol;
  var file_path = filepath;

  if (ht == "http:") {
    var file_path = filepath.replace("https://", "http://");
  }

  element.setAttribute("target", "_blank");
  element.setAttribute("href", file_path);
  element.setAttribute("download", filename);
  // element.setAttribute("id","click");
  document.body.appendChild(element);
  element.click();
  // $("#click").trigger("click");
  //document.body.removeChild(element);
}

function uploadFile(filepath) {
  if (filepath != "") {
    var ext = filepath.split(".").pop().toLowerCase(); //확장자분리
    console.log(ext);
    //아래 확장자가 있는지 체크
    if (
      $.inArray(ext, [
        "jpg",
        "jpeg",
        "png",
        "gif",
        "pdf",
        "doc",
        "xls",
        "xlsx",
        "xlsm",
        "hwp",
        "pptx",
        "ppt",
        "pptm",
        "zip",
        "tar",
        "tgz",
        "alz",
        "txt",
      ]) == -1
    ) {
      //alert('지원되지않는 파일확장자입니다.');
      return "ok";
    }
  }
}

function img_uploadFile(filepath) {
  if (filepath != "") {
    var ext = filepath.split(".").pop().toLowerCase(); //확장자분리
    //아래 확장자가 있는지 체크
    if ($.inArray(ext, ["jpg", "jpeg", "png", "gif"]) == -1) {
      //alert('지원되지않는 파일확장자입니다.');
      return "ok";
    }
  }
}

//모바일 유무체크함수
function MobileChk() {
  if (navigator.userAgent) {
    return /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(
      navigator.userAgent
    );
  } else {
    return false;
  }
}

// 최초 로그인시
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
    success: function (data) {
      $("#layer_lw_time").html(data);
      $("#layer_work").show();
    },
  });
}

//튜토리얼 데이터 삽입
function tutorial_insert() {
  var fdata = new FormData();
  fdata.append("mode", "insert");

  $.ajax({
    type: "POST",
    data: fdata,
    contentType: false,
    processData: false,
    url: "/inc/tu_process.php",
    success: function (data) {
      $(".tuto_phase").show();
      console.log(data);
      if (data == "complete") {
        $(".tuto_start").css("display", "block");
        return false;
      } else if (data == 6) {
        alert("이미 모든 튜토리얼을 진행하셨습니다.");
        return false;
      } else {
        var next_level = Number(data) + 1;
        $(".tuto_phase").css("display", "block");
        if (next_level == 1) {
          $(".phase_01 button").attr(
            "onclick",
            "location.href='/todaywork/tu_works.php'"
          );
          $(".phase_01").addClass("tuto_on");
        } 

        for (i = 1; i <= data; i++) {
          $(".phase_0" + i).addClass("tuto_clear");
          $(".phase_0" + next_level).addClass("tuto_on");
        }

        if (next_level == 2) {
          $(".phase_02 button").attr(
            "onclick",
            "location.href='/todaywork/tu_works_like.php'"
          );
        } else if (next_level == 3) {
          $(".phase_03 button").attr(
            "onclick",
            "location.href='/todaywork/tu_works_coin.php'"
          );
        } else if (next_level == 4) {
          $(".phase_04 button").attr(
            "onclick",
            "location.href='/party/tu_project.php'"
          );
        } else if (next_level == 5) {
          $(".phase_05 button").attr(
            "onclick",
            "location.href='/challenge/tu_chall.php'"
          );
        } else if (next_level == 6) {
          $(".phase_06 button").attr(
            "onclick",
            "location.href='/team/tu_team.php'"
          );
        }
      }
    },
  });
}

function numberCommas(number) {
  return number.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
}
/*****
let isPuase = false;
let timers;
function start_page_reload() {
    isPuase = false;
    timers = setInterval(function() {
        page_load();
    });
}

function stop_page_reload() {
    clearInterval(timers);
    isPuase = true;
}

function page_load() {
    if (!isPuase) {
        //setTimeout(function() {
        //location.reload();
        console.log("새로고침");
        //}, 300);
    } else {
        console.log("nn");
    }
}
*****/

//setInterval(checkmove, 1000);
//setInterval(start_page_reload, 100);

/*****
    var timer = 0;

    function doMove() {
      timer = 0;
    } //마우스가 움직일때마다 타이버를 초기화

    function checkmove() { //1초마다 이함수를 호출하여 타이머를 증가
      timer++;
      if (timer == 10) { //초단위로 값을 셋팅하고 timer가 해당 값이 될 경우 함수호출
        funcSetScript();
        timer = 0; //함수를 호출하고 타이머 초기화
      }
    }

    function funcSetScript() {
      //location.href=\"http://www.naver.com\"
    }
*****/

if (
  $(".tabs_on").hasClass("now_01") == true &&
  $(".tabs_on").hasClass("now_02") == true
) {
  //console.log("xxxxxxxxxx");
}
