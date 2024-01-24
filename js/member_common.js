$(function () {
  //숫자만입력
  $(document).on(
    "keyup",
    "input[id^=cg_box_input],input[id^=cd_box_input]",
    function (_event) {
      //this.value = this.value.replace(/[^0-9]/g, ''); // 입력값이 숫자가 아니면 공백
      //this.value = this.value.replace(/,/g, ''); // ,값 공백처리
      //this.value = this.value.replace(/\B(?=(\d{3})+(?!\d))/g, ","); // 정규식을 이용해서 3자리 마다 , 추가

      $(this).val(
        addComma(
          $(this)
            .val()
            .replace(/[^0-9]/g, "")
        )
      );
    }
  );

  //자동완성 off
  $(
    "input[id=withdraw_coin],input[id=input_bank_num],input[id=input_bank_user]"
  ).attr("autocomplete", "off");

  //출금신청 금액 길이제한
  $("#withdraw_coin").attr("maxlength", 12);

  //출금신청 계좌번호 길이제한
  $("#input_bank_num").attr("maxlength", 16);

  //출금신청 예금주 길이제한
  $("#input_bank_user").attr("maxlength", 10);

  //공용코인 지급
  $(document).on("input keydown", "input[id^=cg_box_input]", function (event) {
    $(this).attr("maxlength", 10);
    var inputVal = $(this).val();
    var id = $(this).attr("id");
    var no = id.replace("cg_box_input_", "");
    mem_comcoin = $("#cg_box_coin_" + no).text();

    if (inputVal) {
      $("#cg_bottom button").addClass("on");

      //if (event.key == 'ArrowLeft') {
      //    return;
      //}

      //console.log("event.keyCode :: " + event.keyCode);

      //ArrowLeft,ArrowRight,ArrowUp,Home,End
      if (!(event.keyCode >= 37 && event.keyCode <= 40)) {
        give_coin = $(this).val();
        var cg_company_comcoin = $(".rew_member_banner_in strong").text();
        cg_company_comcoin = unComma(cg_company_comcoin);
        cg_company_comcoin = Number(cg_company_comcoin);

        give_coin = unComma(give_coin);
        give_coin = Number(give_coin);

        if (give_coin > cg_company_comcoin) {
          alert("우리 회사 고용코인을 초과 하였습니다.");
          $("#cg_box_input_" + no).val("");
          $("#give_tot_coin_" + no).text($("#cg_box_coin_" + no).text());
          $("#cg_comcoin_" + no).text($(".rew_member_banner_in strong").text());
          $("#cg_bottom button").removeClass("on");
          return false;
        }

        $(this).val(inputVal.replace(/[^0-9,]/gi, ""));
        coin_give_input($(this).val(), mem_comcoin, no);
        //}
      }
    } else {
      $("#cg_bottom button").removeClass("on");
      coin_give_input($(this).val(), mem_comcoin, no);
    }
  });

  //공용코인 회수하기
  $(document).on("input keydown", "input[id^=cd_box_input]", function (event) {
    $(this).attr("maxlength", 10);
    var inputVal = $(this).val();
    var id = $(this).attr("id");
    var no = id.replace("cd_box_input_", "");
    mem_comcoin = $("#cd_box_coin_" + no).text();

    if (inputVal) {
      $("#cd_bottom button").addClass("on");

      //if (event.key == 'ArrowLeft') {
      //    return;
      //}

      //console.log("event.keyCode :: " + event.keyCode);

      //ArrowLeft,ArrowRight,ArrowUp,Home,End
      if (!(event.keyCode >= 37 && event.keyCode <= 40)) {
        debt_coin = $(this).val();
        var cg_company_comcoin = $(".rew_member_banner_in strong").text();
        cg_company_comcoin = unComma(cg_company_comcoin);
        cg_company_comcoin = Number(cg_company_comcoin);

        debt_coin = unComma(debt_coin);
        debt_coin = Number(debt_coin);

        if (debt_coin > cg_company_comcoin) {
          alert("우리 회사 고용코인을 초과 하였습니다.");
          $("#cd_box_input_" + no).val("");
          $("#debt_tot_coin_" + no).text($("#cd_box_coin_" + no).text());

          $("#cd_comcoin_" + no).text(mem_comcoin);
          $("#cd_bottom button").removeClass("on");
          return false;
        }

        $(this).val(inputVal.replace(/[^0-9,]/gi, ""));
        coin_debt_input($(this).val(), mem_comcoin, no);
      }
    } else {
      $("#cd_bottom button").removeClass("on");
      coin_debt_input($(this).val(), mem_comcoin, no);
    }
  });

  //공용코인지급하기
  $(document).on("click", "#cg_bottom", function (_event) {
    var id = $(this).parent().find(".cg_box_input").attr("id");
    if (!$("#" + id).val()) {
      alert("지급할 공용코인을 입력해주세요.");
      $("#" + id).focus();
      return false;
    }

    var cg_name_email = $("#cg_name_email").val();
    var comcoin = $("#" + id).val();
    if (confirm("공용코인을 지급 하시겠습니까?")) {
      var fdata = new FormData();
      fdata.append("mode", "comcoin_add");
      fdata.append("cg_name_email", cg_name_email);
      fdata.append("comcoin", comcoin);

      $.ajax({
        type: "post",
        async: false,
        data: fdata,
        contentType: false,
        processData: false,
        url: "/inc/member_process.php",
        success: function (data) {
          console.log(data);
          if (data == "complete") {
            $("#coin_give").hide();
            comcoin_member_ajax_list();
          } else if (data == "small") {
            alert("보유한 공용코인이 부족합니다.");
            return false;
          }
        },
      });
    }
  });

  //공용코인 회수하기
  $(document).on("click", "#cd_bottom", function (_event) {
    var id = $(this).parent().find(".cd_box_input").attr("id");

    var no = id.replace("cd_box_input_", "");
    mem_comcoin = $("#cd_box_coin_" + no).text();
    var cd_name_email = $("#cd_name_email").val();
    var comcoin = $("#" + id).val();

    if (!$("#" + id).val()) {
      alert("회수할 공용코인을 입력해주세요.");
      $("#" + id).focus();
      return false;
    }

    if (mem_comcoin == 0) {
      alert("보유한 공용 코인이 없어 회수할 수 없습니다.");
      return false;
    }

    if (confirm("공용코인을 회수 하시겠습니까?")) {
      var fdata = new FormData();
      fdata.append("mode", "comcoin_remove");
      fdata.append("cd_name_email", cd_name_email);
      fdata.append("comcoin", comcoin);

      $.ajax({
        type: "post",
        async: false,
        data: fdata,
        contentType: false,
        processData: false,
        url: "/inc/member_process.php",
        success: function (data) {
          console.log(data);
          if (data == "complete") {
            $("#coin_debt").hide();
            comcoin_member_ajax_list();
          } else if (data == "small") {
            alert("보유한 공용코인이 부족합니다.");
            return false;
          }
        },
      });
    }
  });

  //부서관리
  $("#btn_team_management").click(function () {
    $("#rew_layer_team_management").show();
  });

  //셀렉트박스 - 마우스 오버
  $("#rew_member_sort").hover(function () {
    $("#rew_member_sort").addClass("on");
  });

  //셀렉트박스 마우스 벗어날때
  $("#rew_member_sort").mouseleave(function () {
    $("#rew_member_sort").removeClass("on");
  });

  //공용코인지급 포인트 기본으로 변경(지급/회수)
  $(document).on("mouseenter click", "#btn_list_give_no, #btn_list_debt_no",
    function (e) {
      //$("#btn_list_give_no,#btn_list_debt_no").hover(function () {
      $(this).css("cursor", "default");
    }
  );

  //부서셀렉트박스 클릭
  //$(document).on("click", ".rew_member_list_sort .btn_sort_on", function() {
  //$(document).on("hover", "rew_member_list_sort", function() {

  //마우스 오버했을때
  $(document).on("mouseenter click", ".rew_member_list_sort", function (_e) {
    //$(".rew_member_list_sort").addClass("on");
  });

  //부서명 클릭 했을때
  $(document).on("click", ".rew_member_list_sort", function (_e) {
    //input_team_select

    var id = $(this).attr("id");
    var no = id.replace("rew_member_list_sort_", "");
    $("#input_team_select").val(no);
    $("#rew_layer_team_select").show();
  });

  //부서명 관리 > 클릭했을때
  //$(document).on('click', '.input_team', function(e) {
  $(document).on("click", "input[id^=input_team]", function (_e) {
    var id = $(this).attr("id");
    var val = $(this).val();
    var no = $("#input_team_select").val();

    console.log("val ::" + val);
    console.log("no ::" + no);
    console.log("id ::" + id);

    if (id) {
      ch_id = id.replace("input_team_", "");

      console.log(ch_id);

      console.log(no);

      if (no) {
        //vv = $("#team_area_" + no).val();
        //console.log("vv" + vv);

        $("#member_team_" + no + " span").text(val);

        $("#ch_part_no_" + no).val(ch_id);
        $("#rew_layer_team_select").hide();
      }
    }
  });

  //부서변경 마우스오버시
  $(document).on("mouseenter", "#tl_list ul li div input", function (_e) {
    $(this).css("cursor", "pointer");
  });

  //마우스 벗어날때
  $(document).on("mouseleave click", ".rew_member_list_sort", function (_e) {
    $(".rew_member_list_sort").removeClass("on");
  });

  //마우스 벗어날때(멤버관리)
  $(document).on(
    "mouseleave click",
    "div[id^=rew_member_inputs_sort]",
    function (_e) {
      var val = $(this).val();
      var id = $(this).attr("id");
      var no = id.replace("rew_member_inputs_sort_", "");
      if (no) {
        $("#rew_member_inputs_sort_" + no).removeClass("on");
      }
    }
  );

  //마우스 오버했을때(멤버관리)
  $(document).on(
    "mouseenter",
    "div[id^=rew_member_inputs_sort]",
    function (_e) {
      //$(".rew_member_inputs_sort").addClass("on");

      var val = $(this).val();
      var id = $(this).attr("id");
      var no = id.replace("rew_member_inputs_sort_", "");
      $("#rew_member_inputs_sort_" + no).addClass("on");

      var inputs_sort = new Array();
      inputs_sort["input"] = "직접입력";
      inputs_sort["naver"] = "naver.com";
      inputs_sort["gmail"] = "gmail.com";
      inputs_sort["kakao"] = "kakao.com";
      if (val) {
        if (inputs_sort[val]) {
          $("#btn_sort_on span").text(inputs_sort[val]);
        }
      }
    }
  );

  //클릭했을때(멤버관리)
  $(document).on(
    "click",
    ".rew_member_inputs_sort ul li button",
    function (_e) {
      var val = $(this).val();
      var id = $(this).attr("id");
      var no = id.replace("btn_sort_li_", "");

      var inputs_sort = new Array();
      inputs_sort["input"] = "직접입력";
      inputs_sort["naver"] = "naver.com";
      inputs_sort["gmail"] = "gmail.com";
      inputs_sort["kakao"] = "kakao.com";

      if (val) {
        //직접입력으로 no값 1씩 차감
        if (no) {
          elem_no = no - 1;
        }

        if (val == "input") {
          var elem = $("input[id=inputs_member_email]").eq(elem_no);
          setTimeout(function () {
            var input = elem;
            var v = input.val();
            input.focus().val("").val(v);
          }, 50);
        }

        if (inputs_sort[val]) {
          $("#btn_sort_on_" + no + " span").text(inputs_sort[val]);
          $("#btn_sort_on_" + no + "").val(val);
          $("#rew_member_inputs_sort_" + no).removeClass("on");
        }
      }
    }
  );

  //부서 셀렉트 선택
  $(document).on("click", ".rew_member_list_sort ul li button", function () {
    var val = $(this).val();
    var member_team_id = $(this)
      .parent()
      .parent()
      .parent()
      .find(".btn_sort_on")
      .attr("id");
    var no = member_team_id.replace("member_team_", "");

    if (no) {
      $("#member_team_" + no).val(val);
      $("#member_team_" + no + " span").text(team_info_arr[val]);
    }

    $(".rew_member_list_sort").removeClass("on");
  });

  //확인
  $(document).on("click", "#btn_list_ok", function () {
    list_name = $(this)
      .parent()
      .parent()
      .parent()
      .find(".input_member_list_name")
      .attr("id");
    console.log("list_name" + list_name);

    var no = list_name.replace("input_member_list_name_", "");
    var name = $("#input_member_list_name_" + no).val();
    var part = $("#member_team_" + no + " span").text();
    var part_no = $("#ch_part_no_" + no).val();

    var fdata = new FormData();
    fdata.append("mode", "member_edit");
    fdata.append("mem_idx", no);
    fdata.append("name", name);
    fdata.append("part", part);
    fdata.append("part_no", part_no);

    $.ajax({
      type: "post",
      async: false,
      data: fdata,
      contentType: false,
      processData: false,
      url: "/inc/member_process.php",
      success: function (data) {
        console.log(data);
        if (data == "complete") {
          alert("멤버 정보가 수정되었습니다.");
          member_ajax_list();
        } else if (data == "not_auth") {
          alert("권한이 없어 수정이 되지 않습니다.");
          member_ajax_list();
        }
      },
    });
  });

  //취소
  $(document).on("click", "#btn_list_cancel", function () {
    var list_name = $(this)
      .parent()
      .parent()
      .parent()
      .find(".input_member_list_name")
      .attr("id");
    var no = list_name.replace("input_member_list_name_", "");

    $("button[id^=btn_list_regi]").show();
    $("#btn_list_del_" + no).show();

    if (no) {
      if ($("#li_" + no).hasClass("on") == true) {
        $("#li_" + no).removeClass("on");
      }
      $("#rew_member_list_sort_" + no).css("display", "none");
      $("#btn_member_list_" + no).css("display", "none");
    }
  });

  //공용코인관리
  //셀렉트박스 - 마우스 오버- 전체보기,입금,출금
  // $("#rew_member_sub_func_sort").hover(function () {
  //   $("#rew_member_sub_func_sort").addClass("on");
  // });

  $(document).on("mouseenter", "#rew_member_sub_func_sort", function () {
    $("#rew_member_sub_func_sort").addClass("on");
  });

  //셀렉트박스 마우스 벗어날때- 전체보기,입금,출금
  // $("#rew_member_sub_func_sort").mouseleave(function () {
  //   $("#rew_member_sub_func_sort").removeClass("on");
  // });

  $(document).on("mouseleave click", "#rew_member_sub_func_sort", function () {
    $("#rew_member_sub_func_sort").removeClass("on");
  });

  //셀렉트박스 항목선택했을때
  $("#rew_member_sub_func_sort ul li button").click(function () {
    var val = $(this).val();
    var comcoin_sdate = "";
    var comcoin_edate = "";
    var n = "";
    var string = "";
    var comcoin_sort = new Array();
    comcoin_sort["all"] = "전체보기";
    comcoin_sort["in"] = "입금";
    comcoin_sort["out"] = "출금";

    if (val) {
      if (comcoin_sort[val]) {
        $("#rew_member_sub_func_sort").removeClass("on");
        $("#btn_sort_on").text(comcoin_sort[val]);

        if ($("#comcoin_inquiry").val()) {
          var comcoin_sdate = $("#comcoin_sdate").val();
          var comcoin_edate = $("#comcoin_edate").val();
          if ($(".rew_member_sub_func_btns button").eq(0).hasClass("on")) {
            var n = 7;
          } else if (
            $(".rew_member_sub_func_btns button").eq(1).hasClass("on")
          ) {
            var n = 30;
          } else if (
            $(".rew_member_sub_func_btns button").eq(2).hasClass("on")
          ) {
            var n = 90;
          }
        }

        var fdata = new FormData();
        var string =
          "&page=comcoin&sdate=" +
          comcoin_sdate +
          "&edate=" +
          comcoin_edate +
          "&nday=" +
          n +
          "&type=" +
          val;
        fdata.append("mode", "comcoin_list");
        fdata.append("sdate", comcoin_sdate);
        fdata.append("edate", comcoin_edate);
        fdata.append("string", string);
        fdata.append("nday", n);
        fdata.append("type", val);

        $.ajax({
          type: "post",
          async: false,
          data: fdata,
          contentType: false,
          processData: false,
          url: "/inc/member_process.php",
          success: function (data) {
            console.log(data);
            $(".rew_member_list").remove();
            $(".rew_ard_paging").remove();
            $(".rew_member_in .rew_member_sub_func_tab").after(data);
          },
        });
      }
    }
  });


  //코인출금 신청내역 캘린더 사용
  $(document).on("click", "#btn_inquiry", function () {
    $("#member_list_comcoin_out_nocal").attr("id", "member_list_comcoin_out");
    $(".member_list_header_in > div button").removeClass("on");

    var comcoin_sdate = $("#comcoin_sdate").val();
    var comcoin_edate = $("#comcoin_edate").val();
    var val = $("#kind").val();
    var tclass = $("#tclass").val();

    $("#reward_inquiry").val(true);

    if ($(".rew_member_sub_func_btns button").eq(0).hasClass("on")) {
      var n = 7;
    } else if ($(".rew_member_sub_func_btns button").eq(1).hasClass("on")) {
      var n = 30;
    } else if ($(".rew_member_sub_func_btns button").eq(2).hasClass("on")) {
      var n = 90;
    }
    var type = $("#rew_member_sub_func_btns ul li button:eq(0)").hasClass("on");

    var string =
      "&page=member&sdate=" +
      comcoin_sdate +
      "&edate=" +
      comcoin_edate +
      "&nday=" +
      n +
      "&kind=" +
      val +
      "&tclass=" +
      tclass +
      "&type=" +
      type;

    var fdata = new FormData();
    fdata.append("mode", "comcoin_list");
    fdata.append("sdate", comcoin_sdate);
    fdata.append("edate", comcoin_edate);
    fdata.append("string", string);
    fdata.append("type", type);
    fdata.append("nday", n);
    fdata.append("kind", val);
    fdata.append("this_class", tclass);

    $.ajax({
      type: "post",
      async: false,
      data: fdata,
      contentType: false,
      processData: false,
      url: "/inc/member_process.php",
      success: function (data) {
        console.log(data);
        $("#list_paging").html(data);
      },
    });
  });

  // 멤버별 공용코인 내역에서 날짜 필터링
  $(document).on("click", "#btn_history", function () {
    $("#member_list_history_nocal").attr("id", "member_list_history");
    $("#rew_member_sub_func_sort_kind_nocal").attr("id", "rew_member_sub_func_sort_kind");
    var comcoin_sdate = $("#comcoin_sdate").val();
    var comcoin_edate = $("#comcoin_edate").val();
    var email = $("#email_history").val();
    var kind = $(".rew_member_sub_func_sort_in .btn_sort_on").val();

    $("#reward_inquiry").val(true);

    if ($(".rew_member_sub_func_btns button").eq(0).hasClass("on")) {
      var n = 7;
    } else if ($(".rew_member_sub_func_btns button").eq(1).hasClass("on")) {
      var n = 30;
    } else if ($(".rew_member_sub_func_btns button").eq(2).hasClass("on")) {
      var n = 90;
    }
    var type = $("#rew_member_sub_func_btns ul li button:eq(0)").hasClass("on");

    var string =
      "&page=comcoin_mem&sdate=" +
      comcoin_sdate +
      "&edate=" +
      comcoin_edate +
      "&nday=" +
      n +
      "&type=" +
      type;

    var fdata = new FormData();
    fdata.append("mode", "history_list");
    fdata.append("email", email);
    fdata.append("sdate", comcoin_sdate);
    fdata.append("edate", comcoin_edate);
    fdata.append("string", string);
    fdata.append("type", type);
    fdata.append("nday", n);
    fdata.append("kind",kind);
    $.ajax({
      type: "post",
      async: false,
      data: fdata,
      contentType: false,
      processData: false,
      url: "/inc/member_process.php",
      success: function (data) {
        console.log(data);
        $("#list_paging").html(data);
      },
    });
  });

  // 멤버별 공용코인에서 내역 클릭시
  $(document).on("click", "#btn_list_history", function () {
    // var member_email = $(".btn_list_history").val();
    var member_email = $(this).parent().find(".btn_list_history").attr("value");
    var string = "&page=comcoin_mem";
    var fdata = new FormData();
    fdata.append("mode", "history_list");
    fdata.append("email", member_email);
    fdata.append("string", string);

    $.ajax({
      type: "POST",
      data: fdata,
      contentType: false,
      processData: false,
      url: "/inc/member_process.php",
      success: function (data) {
        console.log(data);
        // $("#rew_conts_scroll_01").html(data);
      },
    });
  });

  //부서명관리 > 변경버튼 (20230302_오류수정)
  $(document).on("click", ".team_area .btn_team_regi", function () {
    var li_length = $(".rew_layer_team_management ul li").length;

    $(this).prev(".input_team").attr("disabled", false);
    no = $(this).parent().parent().parent().find(".tc_input").attr("value");
    no = Number(no);

    if ($("#btn_team_regi_" + no).hasClass("on") == false) {
      $("#btn_team_regi_" + no).attr("id", "btn_team_enter_" + no);
      $("#btn_team_enter_" + no).addClass("on");
      $("#btn_team_enter_" + no).text("확인");
      $("#btn_team_enter_" + no)
        .next("button")
        .attr("id", "btn_team_cancel_" + no);
    }

    if (
      $("#btn_team_enter_" + no)
        .next("button")
        .hasClass("off") == false
    ) {
      $("#btn_team_enter_" + no)
        .next("button")
        .addClass("off");
      $("#btn_team_enter_" + no)
        .next("button")
        .text("취소");
    }

    var elem = $("#input_team_" + no); // input_item_0

    let input_val = elem.val(); //  input_item_0
    setTimeout(function () {
      var input = elem;
      var v = input.val();
      input.focus().val("").val(v);
    }, 50);

    $("#team_real").val(input_val);

    for (i = 0; i < li_length; i++) {
      if (i != no) {
        if ($("#btn_team_enter_" + i).hasClass("on") == true) {
          $("#btn_team_enter_" + i).removeClass("on");
          $("#btn_team_enter_" + i).text("변경");
          $("#btn_team_enter_" + i)
            .next()
            .text("삭제");
          $("#btn_team_enter_" + i).attr("id", "btn_team_regi_" + i);

          $("#btn_team_cancel_" + i).attr("id", "btn_team_del_" + i);
          $("#btn_team_del_" + i).removeClass("off");
          $("#input_team_" + i).attr("disabled", true);
        }
      }
    }
  });

  //부서명관리 > 취소버튼
  $(document).on("click", "button[id^=btn_team_cancel]", function () {
    var no = $(this).parent().parent().parent().find(".tc_input").attr("value");
    if (no) {
      var team_real = $("#team_real").val();
      $("#btn_team_enter_" + no).attr("id", "btn_team_regi_" + no);
      if ($("#btn_team_regi_" + no).hasClass("on") == true) {
        $("#btn_team_regi_" + no).removeClass("on");
        $("#btn_team_regi_" + no).text("변경");

        $("#btn_team_regi_" + no)
          .next()
          .text("삭제");
        $("#btn_team_cancel_" + no).attr("id", "btn_team_del_" + no);

        $("#btn_team_del_" + no).removeClass("off");
        $("#input_team_" + no).attr("disabled", true);

        $("#input_team_" + no).val(team_real);
      }
    }
  });

  //부서명관리 > 확인버튼
  $(document).on("click", "button[id^=btn_team_enter]", function () {
    var no = $(this).parent().parent().parent().find(".tc_input").attr("value");
    if (no) {
      var input_team = $("#input_team_" + no).val();
      var team_idx = $("#team_area_" + no).attr("value");
      var fdata = new FormData();
      fdata.append("mode", "member_team");
      fdata.append("no", no);
      fdata.append("input_team", input_team);
      fdata.append("team_idx", team_idx);

      $.ajax({
        type: "POST",
        data: fdata,
        contentType: false,
        processData: false,
        url: "/inc/member_process.php",
        success: function (data) {
          console.log(data);
          if (data == "complete") {
            if ($("#btn_team_enter_" + no).hasClass("on") == true) {
              $("#btn_team_enter_" + no).removeClass("on");
              $("#btn_team_enter_" + no).text("변경");
              $("#btn_team_enter_" + no).attr("id", "btn_team_regi_" + no);

              $("#btn_team_regi_" + no)
                .next()
                .text("삭제");
              $("#btn_team_cancel_" + no).attr("id", "btn_team_del_" + no);

              $("#btn_team_del_" + no).removeClass("off");
              $("#input_team_" + no).attr("disabled", true);
              member_ajax_list();
            }
          }
        },
      });
    }
  });

  //부서명관리 > 삭제버튼
  $(document).on("click", "button[id^=btn_team_del]", function () {
    // var no = $(this).parent().parent().parent().find(".tc_input").attr("value");
    // var team_idx = $("#team_area_" + no).attr("value");
    var team_idx = $(this).val();
    var part = $(this).parent().find(".input_team").val();
    var id = $(this).attr("id");

    // alert(team_idx);
    // return false;

    if (team_idx && part && id) {
      if (confirm("" + part + "을 삭제 하시겠습니까?")) {
        var fdata = new FormData();
        fdata.append("mode", "team_del");
        fdata.append("team_idx", team_idx);
        $.ajax({
          type: "POST",
          data: fdata,
          contentType: false,
          processData: false,
          url: "/inc/member_process.php",
          success: function (data) {
            var tdata = data.split("|")
            if(tdata){
              result1 = tdata[0];
              result2 = tdata[1];
              if(result1 == "complete"){
                $("#rew_layer_team_management").html(result2);
              }
            }
            // if (data == "complete") {
            //   $("#" + id)
            //     .closest(".rew_layer_team_management .tl_list > ul > li")
            //     .remove();
            //   var list_leng =
            //     $(".rew_layer_team_management .tl_list > ul > li").length - 1;
            //   if (list_leng > 4) {
            //     $(".t_layer.rew_layer_team_management").css({
            //       height: 619,
            //       marginTop: -320,
            //     });
            //   } else {
            //     var list_lengx = 65 * list_leng;
            //     $(".t_layer.rew_layer_team_management").css({
            //       height: 359 + list_lengx,
            //       marginTop: -(359 + list_lengx) / 2,
            //     });
            //   }
            // }
          },
        });
      }
    }
  });

  //부서명관리 > 취소버튼
  $(document).on("click", ".team_area #btn_team_cancel", function () {
    if ($(this).hasClass("off") == true) {
      $(this).removeClass("off");
      $(this).text("삭제");
      $(this).attr("id", "btn_team_del");
    }

    if ($(this).prev().hasClass("on") == true) {
      $(this).prev().removeClass("on");
      $(this).prev().text("변경");
      $(this).prev().attr("id", "btn_team_regi");
    }
  });

  //맴버관리 > 삭제버튼
  $(document).on("click", "button[id^=btn_list_del]", function () {
    var name = $(this)
      .parent()
      .parent()
      .find(".member_list_conts_name strong")
      .text();
    var team = $(this)
      .parent()
      .parent()
      .find(".member_list_conts_team strong")
      .text();
    var email = $(this)
      .parent()
      .parent()
      .find(".member_list_conts_email strong")
      .text();
    var mem_idx = $(this).val();
    //var	mem_idx = $(this).parent().parent().find("#sw_idx").attr("value");
    var v = $(this).parent().parent().html();

    if (name && team && mem_idx) {
      if (confirm(team + " " + name + " 회원을 삭제 하시겠습니까?")) {
        var fdata = new FormData();
        fdata.append("mode", "member_del");
        fdata.append("mem_idx", mem_idx);
        $.ajax({
          type: "POST",
          data: fdata,
          contentType: false,
          processData: false,
          url: "/inc/member_process.php",
          success: function (data) {
            console.log(data);
            if (data == "complete") {
              member_ajax_list();
              return false;
            } else if (data == "auth_not") {
              alert("권한을 확인 해주세요.");
              return false;
            }
          },
        });
      }
    } else {
      if (confirm(email + " 회원을 삭제 하시겠습니까?")) {
        var fdata = new FormData();
        fdata.append("mode", "member_del");
        fdata.append("mem_idx", mem_idx);
        $.ajax({
          type: "POST",
          data: fdata,
          contentType: false,
          processData: false,
          url: "/inc/member_process.php",
          success: function (data) {
            console.log(data);
            if (data == "complete") {
              member_ajax_list();
              return false;
            } else if (data == "auth_not") {
              alert("권한을 확인 해주세요.");
              return false;
            }
          },
        });
      }
    }
  });

  //셀렉트박스 항목선택했을때
  $("#rew_member_sort ul li button").click(function () {
    $("#member_list_header_in").find("button[class^=btn_sort_]").removeClass("on");
    var val = $(this).val();
    var member_search = $("#member_search").val();
    var member_sort = new Array();
    member_sort["name"] = "이름 순";
    member_sort["part"] = "부서 순";
    member_sort["email"] = "이메일 순";
    tclass = "btn_sort_up";
    if (val) {
      console.log("val :: " + val);
      $("#btn_sort_on").val(val);

      if (member_sort[val]) {
        $("#rew_member_sort").removeClass("on");
        $("#btn_sort_on").text(member_sort[val]);

        var fdata = new FormData();
        fdata.append("mode", "member_list");
        fdata.append("this_class", tclass);
        fdata.append("kind", val);
        if (member_search) {
          fdata.append("member_search", member_search);
        }
        $.ajax({
          type: "POST",
          data: fdata,
          contentType: false,
          processData: false,
          url: "/inc/member_process.php",
          success: function (data) {
            console.log(data);
            if (data) {
              var tdata = data.split("|");
              if (tdata) {
                var html = tdata[0];
                var cnt = tdata[1];

                $("#list_paging").html(html);
                $(".rew_member_count strong").text(cnt);
              }
            }
          },
        });
      }
    }
  });

  // 공용코인 내역 지급 or 회수

  $(document).on("mouseenter", "#rew_member_sub_func_sort_kind", function () {
    $("#rew_member_sub_func_sort_kind").addClass("on");
  });

  $(document).on("mouseleave click","#rew_member_sub_func_sort_kind", function () {
      $("#rew_member_sub_func_sort_kind").removeClass("on");
    }
  );

  // 공용코인 내역 지급/차감 캘린더o
  $(document).on("click","#rew_member_sub_func_sort_kind ul li button",function () {
      $(".member_list_header_in").find("button[class^=btn_sort_]").removeClass("on");
      var val = $(this).val();
      $("#member_list_history_nocal").attr("id", "member_list_history");
      var comcoin_sdate = $("#comcoin_sdate").val();
      var comcoin_edate = $("#comcoin_edate").val();
      var email = $("#email_history").val();

      var member_sort = new Array();
      member_sort["all"] = "전체보기";
      member_sort["in"] = "지급";
      member_sort["out"] = "차감";

      if (val) {
        console.log("val :: " + val);
        $("#btn_sort_on").val(val);

        if (member_sort[val]) {
          $("#rew_member_sort").removeClass("on");
          $("#btn_sort_on").text(member_sort[val]);

          var fdata = new FormData();
          fdata.append("mode", "history_list");
          fdata.append("kind", val);
          fdata.append("sdate", comcoin_sdate);
          fdata.append("edate", comcoin_edate);
          fdata.append("email", email);

          $.ajax({
            type: "POST",
            data: fdata,
            contentType: false,
            processData: false,
            url: "/inc/member_process.php",
            success: function (data) {
              console.log(data);
              if (data) {
                var tdata = data.split("|");
                if (tdata) {
                  console.log(data);
                  $("#list_paging").html(data);
                }
              }
            },
          });
        }
      }
    }
  );

  $(document).on("mouseenter", "#rew_member_sub_func_sort_kind_nocal", function () {
    $("#rew_member_sub_func_sort_kind_nocal").addClass("on");
  });

  $(document).on("mouseleave click","#rew_member_sub_func_sort_kind_nocal", function () {
      $("#rew_member_sub_func_sort_kind_nocal").removeClass("on");
    }
  );

  //코인출금 신청내역 날짜 초기화
  $(document).on("click","#cal_inquiry",function(){
    $(".rew_member_sub_func_btns").find("button").removeClass("on");
    var fdata = new FormData();
    fdata.append("mode", "comcoin_list_nocal");

    $.ajax({
      type: "POST",
      data: fdata,
      contentType: false,
      processData: false,
      url: "/inc/member_process.php",
      success: function (data) {
        console.log(data);
        if (data) {
            console.log(data);
            $("#list_paging").html(data);
        }
      },
    });
  });

  //공용코인 내역 날짜 초기화
  $(document).on("click","#cal_history",function(){
    $(".rew_member_sub_func_btns").find("button").removeClass("on");
    var member_email = $("#email_history").val();
    var kind = $(".rew_member_sub_func_sort_in .btn_sort_on").val();
    var fdata = new FormData();
    fdata.append("mode", "history_list_nocal");
    fdata.append("email",member_email);
    fdata.append("kind",kind);
    $.ajax({
      type: "POST",
      data: fdata,
      contentType: false,
      processData: false,
      url: "/inc/member_process.php",
      success: function (data) {
        console.log(data);
        if (data) {
            $("#list_paging").html(data);
        }
      },
    });
  });

  // 공용코인 내역 지급/차감 캘린더x
  $(document).on("click","#rew_member_sub_func_sort_kind_nocal ul li button", function () {
    $(".member_list_header_in").find("button[class^=btn_sort_]").removeClass("on");
    var val = $(this).val();
    var email = $("#email_history").val();

    var member_sort = new Array();
    member_sort["all"] = "전체보기";
    member_sort["in"] = "지급";
    member_sort["out"] = "차감";

    if (val) {
      console.log("val :: " + val);
      $("#btn_sort_on").val(val);

      if (member_sort[val]) {
        $("#rew_member_sort").removeClass("on");
        $("#btn_sort_on").text(member_sort[val]);

        var fdata = new FormData();
        fdata.append("mode", "history_list_nocal");
        fdata.append("kind", val);
        fdata.append("email", email);

        $.ajax({
          type: "POST",
          data: fdata,
          contentType: false,
          processData: false,
          url: "/inc/member_process.php",
          success: function (data) {
            console.log(data);
            if (data) {
              var tdata = data.split("|");
              if (tdata) {
                console.log(data);
                $("#list_paging").html(data);
              }
            }
          },
        });
      }
    }
  }
);

  //이름, 부서명검색
  $(document).on("click","#member_search_btn",function(){
    member_ajax_list();
  });

  //이름, 부서명 입력란
  $("#member_search").bind("input keyup", function (e) {
    var input_val = $(this).val();
    if (input_val) {
      if (e.keyCode == 13) {
        $("#member_search_btn").trigger("click");
        return false;
      }
    } else {
      member_ajax_list();
      return false;
    }
  });

  //추가하기
  $(document).on("click","#tl_btn_team_add",function(){
    var val = $("#team_add").val();
    if (val) {
      var fdata = new FormData();
      fdata.append("mode", "team_add");
      fdata.append("team_name", val);
      $.ajax({
        type: "POST",
        data: fdata,
        contentType: false,
        processData: false,
        url: "/inc/member_process.php",
        success: function (data) {
          console.log(data);

          $("#tl_list ul").html(data);
        },
      });
    } else {
      alert("부서명을 입력해 주세요.");
      $("#team_add").focus();
      return false;
    }
  });

  //멤버별 공용코인 리스트 , 정렬기준
  $(document).on("click", "#member_list_comcoin > div button", function () {
    $(".member_list_header_in > div button").removeClass("on");
    $(this).addClass("on");
    var fdata = new FormData();
    val = $(this).parent().parent().find("strong").attr("value");
    var string = "&page=comcoin_mem&kind=" + val;
    var member_search = $("#member_search").val();
    tclass = $(this).attr("class").replace(" on", "");
    fdata.append("mode", "member_comcoin_list");
    fdata.append("kind", val);
    fdata.append("this_class", tclass);
    fdata.append("string", string);
    if (member_search) {
      fdata.append("member_search", member_search);
    }
    $.ajax({
      type: "POST",
      data: fdata,
      contentType: false,
      processData: false,
      url: "/inc/member_process.php",
      success: function (data) {
        console.log(data);
        if (data) {
          var tdata = data.split("|");

          if (tdata) {
            var result = tdata[0];
            var result1 = tdata[1];
            var result2 = tdata[2];

            $("#rew_member_list").html(result);
            $("#rew_ard_paging").html(result1);
            $("#rew_member_count strong").text(result2);
            $("#cli_kind").val(val);
          }
        }
      },
    });
  });

  //멤버별 공용코인 -> 내역 날짜별 정렬
  $(document).on("click", "#member_list_history > div button", function () {
    $(".member_list_header_in > div button").removeClass("on");
    var comcoin_sdate = $("#comcoin_sdate").val();
    var comcoin_edate = $("#comcoin_edate").val();
    var kind = $("#kindname").val();
    var type = $("#sortdate").val();

    $(this).addClass("on");

    var fdata = new FormData();
    var email = $("#email_history").val();
    val = $(this).parent().parent().find("strong").attr("value");
    // var string = "&page=history&sort_kind=" + val;
    var string =
      "&page=history&sdate=" +
      comcoin_sdate +
      "&edate=" +
      comcoin_edate +
      "&type=" +
      type;
    // tclass = $("#tclass_1").val();
    tclass = $(this).attr("class").replace(" on", "");

    fdata.append("mode", "history_list");
    fdata.append("sort_kind", "workdate");
    fdata.append("this_class", tclass);
    fdata.append("string", string);
    fdata.append("sdate", comcoin_sdate);
    fdata.append("edate", comcoin_edate);
    fdata.append("email", email);
    fdata.append("kind", kind);

    $.ajax({
      type: "POST",
      data: fdata,
      contentType: false,
      processData: false,
      url: "/inc/member_process.php",
      success: function (data) {
        console.log(data);
        if (data) {
          $("#list_paging").html(data);
        }
      },
    });
  });
  
   //멤버별 공용코인 -> 내역 날짜별 정렬 캘린더 적용 x
  $(document).on("click","#member_list_history_nocal > div button",
    function () {
      $(".member_list_header_in > div button").removeClass("on");
      var kind = $("#kindname").val();
      var type = $("#sortdate").val();

      $(this).addClass("on");

      var fdata = new FormData();
      var email = $("#email_history").val();
      val = $(this).parent().parent().find("strong").attr("value");
      // var string = "&page=history&sort_kind=" + val;
      var string = "&page=history_nocal" + "&type=" + type;
      // tclass = $("#tclass_1").val();
      tclass = $(this).attr("class").replace(" on", "");

      fdata.append("mode", "history_list_nocal");
      fdata.append("sort_kind", "workdate");
      fdata.append("this_class", tclass);
      fdata.append("string", string);
      fdata.append("email", email);
      fdata.append("kind", kind);

      $.ajax({
        type: "POST",
        data: fdata,
        contentType: false,
        processData: false,
        url: "/inc/member_process.php",
        success: function (data) {
          console.log(data);
          if (data) {
            $("#list_paging").html(data);
          }
        },
      });
    }
  );

  // 코인출금 신청내역 -> 날짜 포함 오름,내림정렬
  $(document).on("click", "#member_list_comcoin_out > div button", function () {
    $(".member_list_header_in > div button").removeClass("on");
    $(this).addClass("on");
    var comcoin_sdate = $("#comcoin_sdate").val();
    var comcoin_edate = $("#comcoin_edate").val();

    var fdata = new FormData();
    var val = $(this).parent().parent().find("strong").attr("value");
    var tclass = $(this).attr("class").replace(" on", "");

    var string =
      "&page=member&sdate=" +
      comcoin_sdate +
      "&edate=" +
      comcoin_edate +
      "&kind=" +
      val +
      "&tclass=" +
      tclass;

    fdata.append("mode", "comcoin_list");
    fdata.append("kind", val);
    fdata.append("this_class", tclass);
    fdata.append("sdate", comcoin_sdate);
    fdata.append("edate", comcoin_edate);
    fdata.append("string", string);

    $.ajax({
      type: "POST",
      data: fdata,
      contentType: false,
      processData: false,
      url: "/inc/member_process.php",
      success: function (data) {
        console.log(data);
        $("#list_paging").html(data);
      },
    });
  });

  // 코인출금 신청내역 -> 날짜 제외 오름,내림정렬
  $(document).on(
    "click",
    "#member_list_comcoin_out_nocal > div button",
    function () {
      $(".member_list_header_in > div button").removeClass("on");
      $(this).addClass("on");
      var fdata = new FormData();
      var val = $(this).parent().parent().find("strong").attr("value");
      var tclass = $(this).attr("class").replace(" on", "");

      var string = "&page=member" + "&kind=" + val + "&tclass=" + tclass;

      fdata.append("mode", "comcoin_list_nocal");
      fdata.append("kind", val);
      fdata.append("this_class", tclass);
      fdata.append("string", string);

      // alert(val);
      // alert(tclass);
      // alert(comcoin_sdate);
      // alert(comcoin_edate);
      $.ajax({
        type: "POST",
        data: fdata,
        contentType: false,
        processData: false,
        url: "/inc/member_process.php",
        success: function (data) {
          console.log(data);
          $("#list_paging").html(data);
        },
      });
    }
  );

  //맴버관리> 리스트 정렬순
  $(document).on("click", "#member_list_header_in > div button", function () {
    //$("#member_list_header_in > div button").click(function() {
    $(".member_list_header_in > div button").removeClass("on");
    $(this).addClass("on");

    console.log("ㅈㅈㅈㅈ");
    var fdata = new FormData();
    //var val = $(this).parent().find("#sw_idx").attr("value");
    var member_search = $("#member_search").val();
    val = $(this).parent().parent().find("strong").attr("value");

    tclass = $(this).attr("class").replace(" on", "");
    fdata.append("member_search", member_search);
    fdata.append("mode", "member_list");
    fdata.append("kind", val);
    fdata.append("this_class", tclass);
    $.ajax({
      type: "POST",
      data: fdata,
      contentType: false,
      processData: false,
      url: "/inc/member_process.php",
      success: function (data) {
        console.log(data);
        if (data) {
          var tdata = data.split("|");
          if (tdata) {
            var html = tdata[0];
            var cnt = tdata[1];

            $("#list_paging").html(html);
            $(".rew_member_count strong").text(cnt);
            $("#cli_kind").val(val);
          }
        }
      },
    });
  });

  //이름, 부서명 검색
  $("#comcoin_search").bind("input keyup", function (e) {
    var id = $(this).attr("id");
    var input_val = $(this).val();
    if (input_val) {
      if (e.keyCode == 13) {
        comcoin_member_ajax_list();
        return false;
      }
    } else {
      comcoin_member_ajax_list();
      return false;
    }
  });

  //멤버별 공용코인 - 검색버튼
  $("#comcoin_search_btn").click(function () {
    if (!$("#comcoin_search").val()) {
      alert("이름, 부서명을 입력해주세요.");
      $("#comcoin_search").focus();
    }
    comcoin_member_ajax_list();
  });

  //관리자지정 스위치 on,off
  $(document).on("click", ".member_list_conts_admin .btn_switch", function () {
    $(this).toggleClass("on");
    
    var fdata = new FormData();
    var onf = $(this).hasClass("on");
    var val = $(this).parent().find("#sw_idx").attr("value"); 
    fdata.append("mode", "auth_change");
    fdata.append("sw_val", val);
    fdata.append("onf", onf);
  
    $.ajax({
      type: "POST",
      data: fdata,
      contentType: false,
      processData: false,
      url: "/inc/member_process.php",
      success: function (data) {
        console.log(data);
      },
    });
  });

  //멤버관리 수정
  $(document).on("click", "button[id^='btn_list_regi']", function () {
    var val = $(this).val();

    $("#rew_member_list_team_" + val).hide();
    $("#rew_member_list_name_" + val).hide();
    $("#btn_list_del_" + val).hide();
    $(this).hide();

    var h = $(this).parent().parent().find(".member_list_conts_name").html();

    //h = $(this).parent().parents().find(".member_list_conts_name").parent().html();
    var name = $(this).parent().parent().find(".member_list_conts_name").text();

    name = $.trim(name);
    html = '<strong style="width: 47px;">' + name + "</strong>";
    html = html +=
      '<input type="text" class="input_member_list_name" id="input_member_list_name_' +
      val +
      '" value="' +
      name +
      '">';

    //var mem_cnt = $("#member_list_conts_ul li").length;
    var mem_cnt = $(".rew_member_count strong").text();

    for (i = 0; i < mem_cnt; i++) {
      var li_id = $("li[id^=li]").eq(i).attr("id");

      if (li_id) {
        var no = li_id.replace("li_", "");

        if (no == val) {
          if ($("#li_" + val).hasClass("on") == false) {
            $("#li_" + val).addClass("on");
          }
          $("#rew_member_list_sort_" + val).css("display", "");
          $("#btn_member_list_" + val).css("display", "");
        } else {
          $("#li_" + no).removeClass("on");
          $("#rew_member_list_sort_" + no).css("display", "none");
          $("#btn_member_list_" + no).css("display", "none");
        }
      }
    }

    if ($("#li_" + val).hasClass("on") == false) {
      //	$("#li_"+val).addClass("on")
    }

    $(this).parent().parent().find(".member_list_conts_name").html(html);
    var elem = $("input[id=input_member_list_name_" + val + "]");
    setTimeout(function () {
      var input = elem;
      var v = input.val();
      input.focus().val("").val(v);
    }, 50);
  });

  //시작일
  $(document).on("click", "#btn_calendar_l", function () {
    $(".btn_calendar_l").focus();
    $(".btn_calendar_l").datepicker({ dateFormat: "yyyy-mm-dd" });
  });

  //종료일
  $(document).on("click", "#btn_calendar_r", function () {
    $(".btn_calendar_r").focus();
    $(".btn_calendar_r").datepicker({ dateFormat: "yyyy-mm-dd" });
  });

  //멤버별 공용코인 내역에서 캘린더 사용시
  $(document).on("click", "#comcoin_sdate", function () {
    $("#comcoin_sdate").datepicker({ dateFormat: "yyyy-mm-dd" });
  });

  $(document).on("click", "#comcoin_edate", function () {
    $("#comcoin_edate").datepicker({ dateFormat: "yyyy-mm-dd" });
  });

  //날짜선택(1주일,1개월,3개월)
  // $(".rew_member_sub_func_btns button").click(function () {
  $(document).on("click", ".rew_member_sub_func_btns button", function () {
    var index = $(this).index();
    //console.log("click");
    // console.log(index);

    var btn_cnt = $(".rew_member_sub_func_btns button").length;
    for (var i = 0; i < btn_cnt; i++) {
      if (index == i) {
        $(".rew_member_sub_func_btns button").eq(i).addClass("on");
      } else {
        $(".rew_member_sub_func_btns button").eq(i).removeClass("on");
      }
    }

    if ($(".rew_member_sub_func_btns button").eq(0).hasClass("on")) {
      var n = 7;
    } else if ($(".rew_member_sub_func_btns button").eq(1).hasClass("on")) {
      var n = 30;
    } else if ($(".rew_member_sub_func_btns button").eq(2).hasClass("on")) {
      var n = 90;
    }

    var m = 0;
    var date = new Date();
    var start = new Date(Date.parse(date) - n * 1000 * 60 * 60 * 24);
    var today = new Date(Date.parse(date) - m * 1000 * 60 * 60 * 24);

    var yyyy = start.getFullYear();
    var mm = start.getMonth() + 1;
    var dd = start.getDate();

    if (mm < 10) {
      mm = "0" + mm;
    }
    if (dd < 10) {
      dd = "0" + dd;
    }

    var t_yyyy = today.getFullYear();
    var t_mm = today.getMonth() + 1;
    var t_dd = today.getDate();

    if (t_mm < 10) {
      t_mm = "0" + t_mm;
    }
    if (t_dd < 10) {
      t_dd = "0" + t_dd;
    }

    var reward_sdate = yyyy + "-" + mm + "-" + dd;
    var reward_edate = t_yyyy + "-" + t_mm + "-" + t_dd;
    $("#comcoin_sdate").val(reward_sdate);
    $("#comcoin_edate").val(reward_edate);
  });

  //공용코인 지급
  $(document).on("click", "#btn_list_give", function () {
    var login_idx = $("#member_idx").val();
    var give_email = $(this).parent().parent().find(".member_list_conts_email strong").text();
    var no = $(this).val();

    if(login_idx == no){
      alert('본인에게 공용코인을 지급할 수 없습니다!');
      return false;
    }

    if (no) {
      var fdata = new FormData();
      fdata.append("give_email", give_email);
      fdata.append("mode", "give");

      $.ajax({
        type: "POST",
        data: fdata,
        contentType: false,
        processData: false,
        url: "/inc/member_process.php",
        success: function (data) {
          //console.log(data);
          $("#coin_give").html(data);
          $("#coin_give").show();
          $("#cg_box_input_" + no).focus();
          $("input[id^=cg_box_input]").attr("autocomplete", "off");
        },
      });
    }
  });

  //공용코인 회수
  $(document).on("click", "#btn_list_debt", function () {

    var login_idx = $("#member_idx").val();
    var give_email = $(this).parent().parent().find(".member_list_conts_email strong").text();
    var no = $(this).val();

    if(login_idx == no){
      alert('본인의 공용코인을 회수할 수 없습니다!');
      return false;
    }

    if (no) {
      var fdata = new FormData();
      fdata.append("give_email", give_email);
      fdata.append("mode", "debt");

      $.ajax({
        type: "POST",
        data: fdata,
        contentType: false,
        processData: false,
        url: "/inc/member_process.php",
        success: function (data) {
          //console.log(data);
          $("#coin_debt").html(data);
          $("#coin_debt").show();
          $("#cd_box_input_" + no).focus();
          $("input[id^=cd_box_input]").attr("autocomplete", "off");
        },
      });
    }
  });

  //멤버관리 이전(뒤로가기)
  $("#btn_member_input_back").click(function () {
    history.back(-1);
  });

  //멤버관리 초대메일발송 TEST
  $("#btn_member_input_email2").click(function () {
    //console.log(`------ #btn_member_input_email2 start -------`);

    var fdata = new FormData();
    fdata.append("mode", "member_email_send");

    var len = $(".inputs_member_name").length;
    if (len > 0) {
      if (!$(".inputs_member_name:eq(0)").val()) {
        alert("이름을 입력해주세요.");
        $(".inputs_member_name:eq(0)").focus();
        return false;
      }

      if (!$(".inputs_member_team:eq(0)").val()) {
        alert("부서명을 입력해주세요.");
        $(".inputs_member_team:eq(0)").focus();
        return false;
      }

      if (!$(".inputs_member_email:eq(0)").val()) {
        alert("이메일을 입력해주세요.");
        $(".inputs_member_email:eq(0)").focus();
        return false;
      }

      for (i = 0; i < len; i++) {
        etc_no = i + 1;
        var input_name = $(".inputs_member_name:eq(" + i + ")").val();
        var input_team = $(".inputs_member_team:eq(" + i + ")").val();
        var input_email = $(".inputs_member_email:eq(" + i + ")").val();
        etc_val = $("#btn_sort_on_" + etc_no + "").val();
        if (!etc_val) {
          etc_val = "input";
        }
        var input_email_etc = $("#btn_sort_on_" + etc_no + " span").text();

        if (etc_val == "input") {
          input_email_val = input_email;
        } else {
          input_email_val = input_email + "@" + input_email_etc;
        }

        fdata.append("input_name[]", input_name);
        fdata.append("input_team[]", input_team);
        fdata.append("input_email[]", input_email_val);
        fdata.append("etc_val[]", etc_val);
      }

      if (confirm("초대 메일을 발송 하시겠습니까?")) {
        $.ajax({
          type: "POST",
          data: fdata,
          dataType: "html",
          contentType: false,
          processData: false,
          url: "/inc/member_process_temp.php",
          success: function (data) {
            console.log(data);

            let list = JSON.parse(data.trim());
            let resultMessage = "";
            console.log(list);
            list.map((row, key) => {
              let send_result = "";
              if (row.complete === true) {
                send_result = "발송 성공";
              } else if (row.faile === true) {
                send_result = "발송 실패";
              } else if (row.over === true) {
                send_result = "이미 가입된 이메일입니다.";
              } else {
                send_result = "관리자 문의";
              }

              resultMessage += ` ${key + 1}. ${row.email} :  ${send_result} \n`;
            });

            let completeTotal = list.filter((row) => row.complete === true);
            let faileTotal = list.filter((row) => row.faile === true);
            let overTotal = list.filter((row) => row.over === true);

            console.log(
              `전체 : ${list.length}, 성공: ${completeTotal.length}, 실패: ${faileTotal.length}, 가입중: ${overTotal.length} `
            );

            alert(resultMessage);
            // location.reload();

            //발송성공
            if (completeTotal.length) {
              location.href = "/admin/member_list.php";
              return false;
            }
          },
          error: function (_xhr, _ajaxOptions, thrownError) {
            console.log(thrownError);
          },
          complete: function () {
            console.log(`------ #btn_member_input_email2 end -------`);
          },
        });
      }
    }

    return false;
  });

  //멤버관리 초대메일발송
  $("#btn_member_input_email").click(function () {
    var fdata = new FormData();
    fdata.append("mode", "member_email_send");

    var len = $(".inputs_member_name").length;
    if (len > 0) {
      if (!$(".inputs_member_name:eq(0)").val()) {
        alert("이름을 입력해주세요.");
        $(".inputs_member_name:eq(0)").focus();
        return false;
      }

      if (!$(".inputs_member_team:eq(0)").val()) {
        alert("부서명을 입력해주세요.");
        $(".inputs_member_team:eq(0)").focus();
        return false;
      }

      if (!$(".inputs_member_email:eq(0)").val()) {
        alert("이메일을 입력해주세요.");
        $(".inputs_member_email:eq(0)").focus();
        return false;
      }

      for (i = 0; i < len; i++) {
        etc_no = i + 1;
        var input_name = $(".inputs_member_name:eq(" + i + ")").val();
        var input_team = $(".inputs_member_team:eq(" + i + ")").val();
        var input_email = $(".inputs_member_email:eq(" + i + ")").val();
        etc_val = $("#btn_sort_on_" + etc_no + "").val();
        if (!etc_val) {
          etc_val = "input";
        }
        var input_email_etc = $("#btn_sort_on_" + etc_no + " span").text();

        if (etc_val == "input") {
          input_email_val = input_email;
        } else {
          input_email_val = input_email + "@" + input_email_etc;
        }

        fdata.append("input_name[]", input_name);
        fdata.append("input_team[]", input_team);
        fdata.append("input_email[]", input_email_val);
      }

      if (confirm("초대 메일을 발송하시겠습니까?")) {
        $.ajax({
          type: "POST",
          data: fdata,
          dataType: "html",
          contentType: false,
          processData: false,
          url: "/inc/member_process.php",
          success: function (data) {
            console.log('성공');
            /*if (data) {
                  var tdata = data.split("|");
                  if (tdata) {
                      if (tdata[0] == "over") {
                          no = tdata[1];
                          console.log(" : " + no);
                          $(".inputs_member_email:eq(" + no + ")").focus();

                      }

                  }

              }*/
            if (data == "complete") {
              alert("초대 메일을 발송하였습니다.");
              location.reload();
              return false;
            } else if (data == "faile") {
              alert("초대 메일 발송을 실패 하였습니다.");
              location.reload();
              return false;
            } else if (data == "over") {
              alert("입력한 메일주소는 이미 가입되었습니다.");
              //location.reload();
              return false;
            }
          },
          error: function (_xhr, _ajaxOptions, thrownError) {
            console.log(thrownError);
          },
        });
      }
    }
  });

  //메일 재발송
  $(document).on("click", "button[id^=btn_list_email]", function () {
    var id = $(this).attr("id");
    var no = id.replace("btn_list_email_", "");

    if (no) {
      var name = $(this)
        .parent()
        .parent()
        .find("#member_list_conts_name_" + no + " strong")
        .text();
      var team = $(this)
        .parent()
        .parent()
        .find("#member_list_conts_team_" + no + " strong")
        .text();
      var email = $(this)
        .parent()
        .parent()
        .find("#member_list_conts_email_" + no + " strong")
        .text();
      var sw_status = $(this)
        .parent()
        .parent()
        .find(".btn_switch")
        .hasClass("on");
      if (confirm(name + "님에게 메일 재발송을 하시겠습니까?")) {
        var fdata = new FormData();
        fdata.append("input_name[]", name);
        fdata.append("input_team[]", team);
        fdata.append("input_email[]", email);
        if (sw_status == true) {
          fdata.append("input_sw[]", true);
        } else {
          fdata.append("input_sw[]", false);
        }

        fdata.append("mode", "member_email_send");
        $.ajax({
          type: "POST",
          data: fdata,
          contentType: false,
          processData: false,
          url: "/inc/member_process.php",
          success: function (data) {
            console.log(data);
            data = data.trim();
            if (data == "complete") {
              alert("초대 메일을 발송하였습니다.");
              location.reload();
              return false;
            }
          },
        });
      }
    }
  });

  //업로드레이어
  $("#btn_excel_upload_layer").click(function () {
	$("#rew_layer_excel_add").show();
  });

  $("#sample_file_download").click(function () {

	//fdownload(f_name, f_url);
	var fdata = new FormData();
	var mode = "sample_file_download";
	var url = "/inc/file_download.php";
	fdata.append("mode", mode);
	

	$.ajax({
		type: "POST",
		data: fdata,
		contentType: false,
		processData: false,
		url: url,
		success: function(data) {
			if (data) {
				var tdata = data.split("|");
				if (tdata) {
					var f_name = tdata[0];
					var f_url = tdata[1];
					fdownload(f_name, f_url);
				}
			}
		}
	});
  });


  
  //엑셀파일업로드
  $("#btn_excel_upload").click(function () {
    $("#excel_file").click();
  });

  //엑셀파일 업로드
  $("input[id='excel_file']").change(excel_file_upload);

  //멤버추가
  $("#member_add_btn").click(function () {
    location.href = "/admin/member_add.php";
  });

  //코인지급 닫기
  $(document).on("click", ".cg_close button", function () {
    $("#coin_give").hide();
  });

  //코인회수 닫기
  $(document).on("click", ".cd_close button", function () {
    $("#coin_debt").hide();
  });

  //초기화버튼
  $("#btn_coin_reset").click(function () {
    var obj = $("#withdraw_coin");
    if (obj.val()) {
      obj.val("");
      obj.focus();
      return false;
    }
  });

  //출금금액 숫자만입력
  $("#withdraw_coin,#input_bank_num").bind(
    "change keyup input",
    function (event) {
      if (!(event.keyCode >= 37 && event.keyCode <= 40)) {
        var inputVal = $(this).val();
        $(this).val(inputVal.replace(/[^0-9]/gi, ""));
      }

      if ($("#withdraw_coin").val()) {
        var with_coin = $("#withdraw_coin").val();
        var account_coin = $("#rew_member_withdraw_coin strong").text();
        account_coin = unComma(account_coin);

        if (with_coin && account_coin) {
          coin_as_color(with_coin, account_coin);
        }
      } else {
        coin_as_color(0, 0);
      }
    }
  );

  //입력폼 포커스 되었을때
  $(document).on("focus", "input[id^=withdraw_coin]", function () {
    focused(this.id);
  });

  //입력폼 포커스 벗어날때
  $(document).on("blur", "input[id^=withdraw_coin]", function () {
    blured(this.id);
  });

  //출금신청 > 은행선택하기 클릭
  $("#rew_member_withdraw_bank #btn_bank_on").click(function () {
    $("#rew_member_withdraw_bank").addClass("on");
  });

  //출금신청 > 마우스벗어날때
  $("#rew_member_withdraw_bank").mouseleave(function () {
    $("#rew_member_withdraw_bank").removeClass("on");
  });

  //출금신청 > 은행선택 했을때
  $("#rew_member_withdraw_bank ul li button").click(function () {
    var val = $(this).val();
    var name = $(this).text();
    if (val && name) {
      $("#btn_bank_on").text(name);
      $("#btn_bank_on").val(val);
      $("#rew_member_withdraw_bank").removeClass("on");
    }
  });

  //금액선택
  $("#rew_member_withdraw_btns ul li button").click(function () {
    var val = $(this).val();
    var account_coin = $("#rew_member_withdraw_coin strong").text();
    account_coin = unComma(account_coin);

    if (val) {
      input_coin = $("#withdraw_coin").val();
      if (input_coin) {
        input_coin = unComma(input_coin);
        input_coin = Number(input_coin);
        with_coin = input_coin + Number(val);
        coin_as_color(with_coin, account_coin);
        $("#withdraw_coin").val(with_coin);
        blured("withdraw_coin");
      } else {
        input_coin = Number(0);
        with_coin = input_coin + Number(val);
        coin_as_color(with_coin, account_coin);
        $("#withdraw_coin").val(with_coin);
        blured("withdraw_coin");
      }
    } else {
      if (account_coin) {
        coin_as_color(0, account_coin);
        $("#withdraw_coin").val(account_coin);
        blured("withdraw_coin");
      } else {
        coin_as_color(0, 0);
        $("#withdraw_coin").val("");
      }
    }
  });

  //출금신청하기
  $("#btn_withdraw_on").click(function () {
    var obj_coin = $("#withdraw_coin");
    var obj_bank = $("#btn_bank_on");
    var obj_num = $("#input_bank_num");
    var obj_user = $("#input_bank_user");

    if (!obj_coin.val()) {
      alert("출금할 금액을 입력해 주세요.");
      obj_coin.focus();
      return false;
    }

    if (!obj_bank.val()) {
      alert("은행을 선택해 주세요.");
      return false;
    }

    if (!obj_num.val()) {
      alert("계좌번호를 입력해 주세요.");
      obj_num.focus();
      return false;
    }

    if (!obj_user.val()) {
      alert("예금주를 입력해 주세요.");
      obj_user.focus();
      return false;
    }

    if (confirm("입력한 내용으로 출금 신청하시겠습니까?")) {
      var fdata = new FormData();
      fdata.append("mode", "withdraw_add");
      fdata.append("coin", obj_coin.val());
      fdata.append("bank_name", obj_bank.val());
      fdata.append("bank_num", obj_num.val());
      fdata.append("bank_user", obj_user.val());

      $.ajax({
        type: "post",
        async: false,
        data: fdata,
        contentType: false,
        processData: false,
        url: "/inc/member_process.php",
        success: function (data) {
          console.log(data);
          if (data == "over") {
            alert(
              "출금 신청한 코인이 보유한 공용코인 보다 많습니다.\n남은 공용 코인을 확인 해주세요."
            );
            return false;
          } else if (data == "not") {
            alert("보유한 공용코인이 부족하여 출금신청을 할수 없습니다.");
            return false;
          } else if (data == "complete") {
            alert("출금 신청이 완료되었습니다.");
            location.reload();
            return false;
          }
        },
      });
    }
  });

  // 초기 설정 멤버, 역량, 코인 분배 작업
  $(document).on("click", ".share_mem button",function(){
    var mem_data = $(".share_mem_input").val();
    var memCoin = parseInt(mem_data.replace(/,/g, ''), 10);

    var fdata = new FormData();

    fdata.append("mode", "mem_charge");
    fdata.append("mem_coin", memCoin);

    $.ajax({
      type: "post",
      async: false,
      data: fdata,
      contentType: false,
      processData: false,
      url: "/inc/member_process.php",
      success: function (data) {
        console.log(data);
        if(data == "fail"){
          alert("보유하고 계신 코인이 부족합니다. 다시 확인해주세요.");
          $(".share_mem_input").val("");
          return false;
        }else{
          var tdata = data.split("|");
          console.log(tdata);
          if (tdata) {
            var result = tdata[0];
            var result1 = tdata[1];
            $(".coin_input").html(number_format(result1));
            $(".coin_input").attr('placeholder', number_format(result1));
            $(".share_mem_input").val("");
            alert("설정를 완료하였습니다.");
            return false;
          }
        }
      },
    });

  });
  $(document).on("click", ".share_en button",function(){
    var en_data = $(".share_en_input").val();
    var enCoin = parseInt(en_data.replace(/,/g, ''), 10);

    var fdata = new FormData();

    fdata.append("mode", "en_charge");
    fdata.append("en_coin", enCoin);

    $.ajax({
      type: "post",
      async: false,
      data: fdata,
      contentType: false,
      processData: false,
      url: "/inc/member_process.php",
      success: function (data) {
        console.log(data);
        if(data == "fail"){
          alert("보유하고 계신 코인이 부족합니다. 다시 확인해주세요.");
          $(".share_en_input").val("");
          return false;
        }else{
          var tdata = data.split("|");
          if (tdata) {
            var result = tdata[0];
            var result1 = tdata[1];
            $(".coin_input").html(number_format(result1));
            $(".coin_input").attr('placeholder', number_format(result1));
            $(".share_en_input").val("");
            alert("설정를 완료하였습니다.");
            return false;
          }
        }
      },
    });
  });
  $(document).on("click", ".share_like button",function(){
    var like_data = $(".share_like_input").val();
    var likeCoin = parseInt(like_data.replace(/,/g, ''), 10);

    var fdata = new FormData();

    fdata.append("mode", "like_charge");
    fdata.append("like_coin", likeCoin);

    $.ajax({
      type: "post",
      async: false,
      data: fdata,
      contentType: false,
      processData: false,
      url: "/inc/member_process.php",
      success: function (data) {
        console.log(data);
        if(data == "fail"){
          alert("보유하고 계신 코인이 부족합니다. 다시 확인해주세요.");
          $(".share_like_input").val("");
          return false;
        }else{
          var tdata = data.split("|");
          if (tdata) {
            var result = tdata[0];
            var result1 = tdata[1];
            $(".coin_input").html(number_format(result1));
            $(".coin_input").attr('placeholder', number_format(result1));
            $(".share_like_input").val("");
            alert("설정를 완료하였습니다.");
            return false;
          }
        }
      },
    });
  });

  

});
function number_format(num){
  return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g,',');
}

function excel_file_upload() {
  var input = this;
  var v = $(this).val();

  if (input.files && input.files.length) {
    var fdata = new FormData();
    fdata.append("mode", "member_add");
    fdata.append("files[]", $("input[id='excel_file']")[0].files[0]);

    $.ajax({
      type: "POST",
      data: fdata,
      contentType: false,
      processData: false,
      url: "/inc/member_process.php",
      success: function (data) {
        console.log(data);
        if (data) {
			if($("#rew_layer_excel_add").is(":visible")){
				$("#rew_layer_excel_add").hide();
			}
          $(".rew_member_inputs_in ul").html(data);
        }
      },
    });
  }
}

//멤버별 공용코인 검색
function comcoin_member_ajax_list() {
  var member_search = $("#comcoin_search").val();
  var p = $("#page_num").val();
  var fdata = new FormData();
  var string = "&page=comcoin_mem";
  var tclass = $("#tclass").val();
  var cli_kind = $("#cli_kind").val();

  fdata.append("tclass", tclass);
  fdata.append("kind", cli_kind);
  fdata.append("member_search", member_search);
  fdata.append("mode", "member_comcoin_list");
  fdata.append("p",p);
  fdata.append("string", string);

  $.ajax({
    type: "POST",
    data: fdata,
    contentType: false,
    processData: false,
    url: "/inc/member_process.php",
    success: function (data) {
      console.log(data);
      if (data) {
        var tdata = data.split("|");
        if (tdata) {
          var result = tdata[0];
          var result1 = tdata[1];
          var result2 = tdata[2];
          $(".paging_num").addClass("on");
          $("#rew_member_list").html(result);
          $("#rew_ard_paging").html(result1);
          $("#rew_member_count strong").text(result2);
        }
      }
    },
  });
}

//회원리스트
function member_ajax_list() {
  var member_search = $("#member_search").val();
  var member_sort = $("#btn_sort_on").val();
  var p = $("#page_num").val(); 
  var tclass = $("#tclass").val();
  var cli_kind = $("#cli_kind").val();
  var fdata = new FormData();

  fdata.append("tclass", tclass);
  fdata.append("kind", cli_kind);
  fdata.append("mode", "member_list");
  fdata.append("member_search", member_search);
  fdata.append("member_sort", member_sort);
  fdata.append("p",p);

  $.ajax({
    type: "POST",
    data: fdata,
    contentType: false,
    processData: false,
    url: "/inc/member_process.php",
    success: function (data) {
      console.log(data);

      if (data) {
        var tdata = data.split("|");
        if (tdata) {
          var html = tdata[0];
          var cnt = tdata[1];
          $(".paging_num").addClass("on");
          $("#list_paging").html(html);
          $(".rew_member_count strong").text(cnt);
        }
      }
    },
  });
}

//멤버별 공용코인 계산
function coin_give_input(give_coin, comcoin, no) {
  //회사공용코인
  var cg_company_comcoin = $(".rew_member_banner_in strong").text();
  cg_company_comcoin = unComma(cg_company_comcoin);
  cg_company_comcoin = Number(cg_company_comcoin);

  if (give_coin) {
    give_coin = unComma(give_coin);
    comcoin = unComma(comcoin);

    //console.log(typeof give_coin);
    //console.log(typeof comcoin);
    give_coin = Number(give_coin);
    comcoin = Number(comcoin);

    tot_comcoin = parseInt(give_coin) + parseInt(comcoin);
    tot_comcoin = Number(tot_comcoin);

    //tot_cg_comcoin = cg_company_comcoin - give_coin;
    tot_cg_comcoin = give_coin + comcoin;

    //지급할 공용코인
    $("#give_tot_coin_" + no).text(addComma(tot_comcoin));

    //지급후 공용코인
    $("#cg_comcoin_" + no).text(addComma(tot_cg_comcoin));
  } else {
    tot_comcoin = comcoin;
    //tot_cg_comcoin = cg_company_comcoin;
    tot_cg_comcoin = comcoin;

    //지급할 공용코인
    $("#give_tot_coin_" + no).text(addComma(tot_comcoin));

    //지급후 공용코인
    $("#cg_comcoin_" + no).text(addComma(tot_cg_comcoin));
  }
}

//멤버별 공용코인 회수 계산
function coin_debt_input(debt_coin, comcoin, no) {
  //회사공용코인
  var cg_company_comcoin = $(".rew_member_banner_in strong").text();
  cg_company_comcoin = unComma(cg_company_comcoin);
  cg_company_comcoin = Number(cg_company_comcoin);

  if (debt_coin) {
    debt_coin = unComma(debt_coin);
    comcoin = unComma(comcoin);

    //console.log(typeof give_coin);
    //console.log(typeof comcoin);
    debt_coin = Number(debt_coin);
    comcoin = Number(comcoin);

    tot_comcoin = parseInt(comcoin) - parseInt(debt_coin);
    tot_comcoin = Number(tot_comcoin);

    //        console.log("tot_comcoin " + tot_comcoin);
    //tot_cg_comcoin = cg_company_comcoin + debt_coin;
    tot_cg_comcoin = comcoin - debt_coin;

    if (tot_comcoin == 0) {
      $("#debt_tot_coin_" + no).text(0);

      //회수 후 공용코인
      $("#cd_comcoin_" + no).text(0);
    } else {
      //회수할 공용코인
      $("#debt_tot_coin_" + no).text(addComma(tot_comcoin));
    }

    //회수 후 공용코인
    $("#cd_comcoin_" + no).text(addComma(tot_cg_comcoin));
  } else {
    tot_comcoin = comcoin;
    //tot_cg_comcoin = cg_company_comcoin;
    tot_cg_comcoin = comcoin;

    //회수할 공용코인
    $("#debt_tot_coin_" + no).text(addComma(tot_comcoin));

    //회수 후 공용코인
    $("#cd_comcoin_" + no).text(addComma(tot_cg_comcoin));
  }
}

//출금 신청금액 비교하여 초과시 붉은색, 이하인경우 검정색
function coin_as_color(all_coin, account_coin) {
  all_coin = Number(all_coin);
  account_coin = Number(account_coin);

  if (all_coin > account_coin) {
    $("#withdraw_coin").css("color", "#f10006");
  } else {
    $("#withdraw_coin").css("color", "#858585");
  }
}

//상단 메뉴 링크 스크립트
$(document).on("click", ".rew_member_tab_in .member_list", function () {
  location.href = "/admin/member_list.php";
  return false;
});

$(document).on("click", ".rew_member_tab_in .comcoin", function () {
  location.href = "/admin/comcoin.php";
  return false;
});

$(document).on("click", ".rew_member_tab_in .comcoin_member", function () {
  location.href = "/admin/comcoin_mem.php";
  return false;
});

$(document).on("click", ".rew_member_tab_in .comcoin_out_page", function () {
  location.href = "/admin/comcoin_out.php";
  return false;
});

$(document).on("click", ".rew_member_tab_in .comlogo", function () {
  location.href = "/admin/admin_setting.php";
  return false;
});

// ---------------- 패널티 및 기업별 어드민세팅 -------------------//
$(document).on("click",".logo_file", function(){
  $("#files").click();
});

$(document).on("click",".member_list_conts_setting.list .btn_switch", function(){
  
  if($("#admin_penalty").hasClass("on")==false){
    alert("패널티설정 활성화 후에 시도해주세요.");
    return false;
  }

  $(this).toggleClass("on");
  var fdata = new FormData();
  companyno = $("#comp_no").val();
  fdata.append("companyno",companyno);
  var id = $(this).attr("id");
  
  if(id == "admin_penalty"){
      set_kind = "penalty";
  }else if(id == "admin_in"){
      set_kind = "penalty_in";
  }else if(id == "admin_work"){
      set_kind = "penalty_work";
  }else if(id == "admin_out"){
      set_kind = "penalty_out";
  }else if(id == "admin_chall"){
      set_kind = "penalty_challenge";
  }
  
  fdata.append("mode","admin_penalty");
  fdata.append("setkind",set_kind);

  if($(this).hasClass("on")==true){
      fdata.append("onoff","1");
  }else{
      fdata.append("onoff","0");
  }

  $.ajax({
    type: "POST",
    data: fdata,
    contentType: false,
    processData: false,
    url: "/inc/member_process.php",
    success: function (data) {
      console.log(data);
    },
  });
});

$(document).on("click",".member_list_conts_setting.all .btn_switch", function(){

  // alert(123);
  $(this).toggleClass("on");
  var fdata = new FormData();
  companyno = $("#comp_no").val();
  fdata.append("companyno",companyno);

  var id = $(this).attr("id");
  
  if(id == "admin_penalty"){
      set_kind = "penalty";
  }
  
  fdata.append("mode","admin_penalty");
  fdata.append("setkind",set_kind);

  if($(this).hasClass("on")==true){
      fdata.append("onoff","1");
      $(".set_list_pena ul").show();
  }else{
      fdata.append("onoff","0");
      $(".set_list_pena ul").hide();
      $(".member_list_conts_setting.list .btn_switch").removeClass("on");
  }

  $.ajax({
    type: "POST",
    data: fdata,
    contentType: false,
    processData: false,
    url: "/inc/member_process.php",
    success: function (data) {
      console.log(data);
    },
  });
});

$(document).on("click", ".logo_btn", function(){
  var fdata = new FormData();
  var company = $(".logo_btn").attr("value");
  console.log(company);
var fileobj = $("input[id='files']")[0].files; 
  if (fileobj) {
    if (fileListArr.length > 0) {
  fdata.append("files[]", $("input[id='files")[0].files[0]);
    }
  }

  fdata.append("mode", "logo_upload"); 
  fdata.append("company", company); 
  $.ajax({
    type: "POST",
    data: fdata,
    contentType: false,
    processData: false,  
    url: "/inc/member_process.php",
    success: function (data) {
      if (data) {
        // work_list() 활성화
      console.log(data);
      // 파일 업로드 완료 후, fileListArr 초기화
      }
    },
  });
});

$(document).on("click",".logo_down",function(){
  $('#rew_logo').trigger('click');
});

$(document).on("change","#rew_logo",function(){
  var format_ext = new Array("asp","php","jsp","xml","html","htm","aspx","exe","exec","java","js","class","as","pl","mm","o","c","h","m","cc","cpp","hpp","cxx","hxx","lib","lbr","ini","py","pyc","pyo","bak","$$$","swp","sym","sys","cfg","chk","log","lo","mp4","mp3","gif","zip","hwp","xlsx","ppt","show");

  fileList = [];
  const img = $("#previewImage");

  const file_obj = $(this)[0].files; //파일정보

  file_name = file_obj[0].name;
  file_size = file_obj[0].size;
  // console.log(file_name+"."+file_size);
  var ext = file_name.split(".").pop().toLowerCase();
  var maxSize = 100 * 1024 * 1024;
  var fileSize = file_size;
  fileList.push(file_obj[0]);
  if ($.inArray(ext, format_ext) > 0) {
    alert("첨부할 수 없는 파일입니다.\n파일명 : " + file_name + "");
    return false;
  }
  const reader = new FileReader();
  reader.onload = function(e){
    img.attr("src",e.target.result);
  };
  reader.readAsDataURL(file_obj[0]);

  console.log(file_name);
  console.log(file_size);
  console.log(ext);
  $(".file_name").text(file_name);
  return false;
});
 
$(document).on("click", ".file_down", function(){
  var fdata = new FormData();
  var company = $("#comp_no").val();
  var fileobj = $("input[id='rew_logo']")[0].files; 
    if (fileobj) {
    fdata.append("files[]", $("input[id='rew_logo']")[0].files[0]);
    }else{
      alert("로고로 사용할 이미지를 등록해주세요.");
      return false;
    }

   fdata.append("mode", "logo_upload");
   fdata.append("company", company); 
  // fdata.append("files", fileList[0]);

  $.ajax({
    type: "POST",
    data: fdata,
    contentType: false,
    processData: false,  
    url: "/inc/member_process.php",
    success: function (data) {
      if (data) {
      console.log(data);
        if(data == "complete"){
          alert("회사 이미지 로고가 변경 됐습니다!");
        }
      }
    },
  });
});

$(document).on("click","#time_save",function(){
    var in_time = $("#in_hour span").text() + ":" + $("#in_minite span").text();
    var out_time = $("#out_hour span").text() + ":" + $("#out_minite span").text();
    console.log(in_time);

    var fdata = new FormData();
    fdata.append("intime", in_time);
    fdata.append("outtime", out_time);
    fdata.append("mode", "time_set");

    $.ajax({
      type: "POST",
      data: fdata,
      contentType: false,
      processData: false,  
      url: "/inc/member_process.php",
      success: function (data) {
        if (data) {
        console.log(data);
          if(data == "success"){
            alert("출/퇴근 시간 설정이 완료 됐습니다.");
          }
        }
      },
    });
});

$(document).on("click", ".time_work .rew_member_sort_in ul li, .time_end .rew_member_sort_in ul li", function(){
  var time = $(this).find("span").text();
  var time = String(time).padStart(2, '0');

  set = $(this).parent().parent();
  sert = set.find(".btn_sort_on span");
  sert.text(time);
});