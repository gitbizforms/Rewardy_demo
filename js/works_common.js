$(function () {
  const mouseWheel = document.querySelector(".tdw_write_btns ul");
  if (mouseWheel) {
    mouseWheel.addEventListener("wheel", function (e) {
      const race = 15; // How many pixels to scroll

      if (e.deltaY > 0)
        // Scroll right
        mouseWheel.scrollLeft += race;
      // Scroll left
      else mouseWheel.scrollLeft -= race;
      //console.log(mouseWheel.scrollLeft);
      e.preventDefault();
    });
  }

  //허용확장자
  //var format_ext = "\.(gif|jpg|jpeg|png|xls|xlsx|pdf|ppt|pptx|doc|docx|hwp|zip)";
  //var format_ext2 = "\.(asp|php|jsp|xml|html|htm|aspx|exe|exec|java|js|class|as|pl|mm|o|c|h|m|cc|cpp|hpp|cxx|hxx|lib|lbr|ini|py|pyc|pyo|bak|$$$|swp|sym|sys|cfg|chk|log|lo)$";
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
    "lo"
  );
  $(".tdw_write_btns ul").slick({
    slidesToShow: 6,
    slidesToScroll: 4,
    arrows: true,
    infinite: false,
    speed: 500,
    autoplay: false,
    dots: false,
  });

  //$(".tdw_write_btns").touchFlow({
  //    axis: "x"
  //});

  //속성설정
  $(
    "input[id=workdate],input[id=input_cha_date_l],input[id=input_cha_date_r]"
  ).attr("maxlength", 10);

  //속성설정, 챌린지알림
  //검색 키워드
  $("#sl1").attr("autocomplete", "off");

  $(document).on("mouseenter", "#notice_link", function (e) {
    $(this).css("cursor", "pointer");
  });

  //입력제한
  $("#workdate").bind("change keyup input", function (event) {
    if (!(event.keyCode >= 37 && event.keyCode <= 40)) {
      var inputVal = $(this).val();
      $(this).val(inputVal.replace(/[^0-9-]/gi, ""));
    }
  });

  $("#workdate").keyup(function () {
    var date = this.value;
    var RegNotNum = /[^0-9]/g;
    date = date.replace(RegNotNum, ""); // 숫자만 남기기

    if (date == "" || date == null || date.length < 5) {
      this.value = date;
      return;
    }

    var DataFormat;
    var RegPhonNum;

    // 날짜 포맷(yyyy-mm-dd) 만들기
    if (date.length <= 6) {
      DataFormat = "$1-$2"; // 포맷을 바꾸려면 이곳을 변경
      RegPhonNum = /([0-9]{4})([0-9]+)/;
    } else if (date.length <= 8) {
      DataFormat = "$1-$2-$3"; // 포맷을 바꾸려면 이곳을 변경
      RegPhonNum = /([0-9]{4})([0-9]{2})([0-9]+)/;
    }

    date = date.replace(RegPhonNum, DataFormat);

    this.value = date;

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
        alert("잘못된 날짜입니다. \n다시 입력하세요.");
        this.value = "";
        this.focus();
        return;
      }
    }
  });
  $(document).on("click", ".select_report", function () {
    window.open("http://demo.rewardy.co.kr/todaywork/team_index.php", "_blink");
  });


  //업무이동
  $(document).on("click", ".tdw_list ul", function () {
    $(this).sortable({
      axis: "y",
      opacity: 0.7,
      zIndex: 9999,
      handle: ".tdw_list_drag",
      //placeholder:"sort_empty",
      cursor: "move",
      update: function (event, ui) {
        var fdata = new FormData();
        var listsort = $(this).sortable("serialize");
        var wdate = $("#work_date").val();

        fdata.append("wdate", wdate);
        fdata.append("mode", "works_move");
        fdata.append("listsort", listsort);

        if ($(".select_dd").hasClass("on") == true) {
          fdata.append("work_flag", "1");
        } else if ($(".select_ww").hasClass("on") == true) {
          fdata.append("work_flag", "2");
        } else if ($(".select_mm").hasClass("on") == true) {
          fdata.append("work_flag", "3");
        }

        var id = ui.item.attr("id");
        var val = $("#" + id).val();

        fdata.append("id", id);
        fdata.append("val", val);

        $.ajax({
          type: "POST",
          data: fdata,
          contentType: false,
          processData: false,
          url: "/inc/works_process.php",
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

  // $(".tdw_list ul").disableSelection();

  $(".rew_grid_onoff_in .btn_switch").click(function () {
    var switchon = $(this);
    if (switchon.hasClass("on")) {
      $(this).removeClass("on");
      $(this).prev("em").removeClass("on");
    } else {
      $(".btn_switch").not($(this)).removeClass("on");
      $(".btn_switch").not($(this)).prev("em").removeClass("on");
      $(this).addClass("on");
      $(this).prev("em").addClass("on");
    }
  });

  $(".rew_conts_list_in ul li button").click(function () {
    $(this).parent("li").toggleClass("on");
  });

  $(".rew_btn_icons_more").click(function () {
    $(".rew_icons").toggle();
  });

  $(".rew_mypage_tab_04 a").click(function () {
    $(".rew_mypage_tab_04 li").removeClass("on");
    $(this).parent("li").addClass("on");
  });

  setTimeout(function () {
    //$(".rew_box").addClass("on");

    var bar_t = $(".rew_mypage_section .live_list_today_bar strong").text();
    var bar_b = $(".rew_mypage_section .live_list_today_bar span").text();
    var bar_w = (bar_t / bar_b) * 100;
    $(".rew_mypage_section .live_list_today_bar strong").css({
      width: bar_w + "%",
    });
  }, 400);

  //탭 오늘업무
  $("#write_tab_01").click(function () {
    $(".tdw_write_tab_in button").removeClass("on");
    $(this).addClass("on");
    $(".input_write").attr("placeholder", "업무를 입력해 주세요.");
    //$(".input_write").val("");

    if ($(".tdw_write_btns").is(":visible") == false) {
      $(".tdw_write_btns").show();
    }

    $(".tdw_write_btns button").removeClass("on");
    $(".tdw_write_req").hide();
    $(".tdw_write_report").hide();
    $(".tdw_write_text_report").hide();
    $(".tdw_write_user_desc").hide();
    $(".tdw_write_date").show();
    $(".tdw_write_text").show();
    $(".tdw_write_btns").addClass("on");
  });

  //탭 오늘업무
  $("#write_tab_01_old").click(function () {
    $(".tdw_write_tab button").removeClass("on");
    $(this).addClass("on");
    $(".input_write").attr("placeholder", "할 일을 입력해주세요.");
    $(".input_write").val("");
    $(".tdw_write_date").hide();
    $(".tdw_write_btns").removeClass("on");
    $(".tdw_write_req").hide();
  });

  //탭 오늘업무
  $("#write_tab_01_new").click(function () {
    $(".tdw_write_tab button").removeClass("on");
    $(this).addClass("on");
    $(".input_write").attr("placeholder", "업무를 입력해 주세요.");
    $(".input_write").val("");
    $(".tdw_write_btns button").removeClass("on");
    $(".tdw_write_btns").addClass("on");
    $(".tdw_write_req").hide();
  });

  //탭 요청업무
  $("#write_tab_02_new").click(function () {
    $(".tdw_write_tab button").removeClass("on");
    $(this).addClass("on");
    //$(".input_write").attr("placeholder", "요청할 업무를 입력해주세요.");
    //$(".input_write").val("");
    $(".tdw_write_btns").removeClass("on");
    $(".tdw_write_req").show();
  });

  //탭 요청업무
  $("#write_tab_02").click(function () {
    $(".tdw_write_tab_in button").removeClass("on");
    $(this).addClass("on");
    $(".input_write").attr("placeholder", "업무를 입력해 주세요.");
    //$(".input_write").val("");
    $(".tdw_write_btns").css("display","none");
    $(".tdw_write_report").hide();
    $(".tdw_write_text_report").hide();
    $(".tdw_write_date").show();
    $(".tdw_write_req").show();
    $(".tdw_write_text").show();
    //$(".tdw_write_user_desc").show();

    //받을사람선택시
    if ($("#chall_user_chk").val()) {
      if ($("#tdw_write_user_desc").is(":visible") == false) {
        $("#tdw_write_user_desc").show();
      }
    }
  });

  //탭 요청업무
  $("#write_tab_02_old").click(function () {
    $(".tdw_write_tab button").removeClass("on");
    $(this).addClass("on");
    $(".input_write").attr("placeholder", "예약할 업무를 입력해주세요.");
    $(".input_write").val("");
    $(".tdw_write_date").show();
    $(".tdw_write_btns button").removeClass("on");
    mouseWheel.scrollLeft = 0;
    $(".tdw_write_btns").addClass("on");
    $(".tdw_write_req").hide();
  });

  //탭 보고
  $("#write_tab_03").click(function () {
    $(".tdw_write_tab_in button").removeClass("on");
    $(this).addClass("on");
    $(".input_write").attr("placeholder", "보고할 내용을 작성해 주세요.");
    //$(".input_write").val("");
    $(".tdw_write_btns").css("display","none");
    $(".tdw_write_date").hide();
    $(".tdw_write_text").hide();
    $(".tdw_write_req").show();
    $(".tdw_write_report").show();
    $(".tdw_write_text_report").show();

    if ($(".tdw_write_btns").is(":visible") == true) {
      $(".tdw_write_btns").hide();
    }

    //받을사람선택시
    if ($("#chall_user_chk").val()) {
      if ($("#tdw_write_user_desc").is(":visible") == false) {
        $("#tdw_write_user_desc").show();
      }
    }
  });

  //탭 업무요청
  $("#write_tab_03_old").click(function () {
    $(".tdw_write_tab button").removeClass("on");
    $(this).addClass("on");
    $(".input_write").attr("placeholder", "요청할 업무를 입력해주세요.");
    $(".input_write").val("");
    $(".tdw_write_date").hide();
    $(".tdw_write_btns").removeClass("on");
    $(".tdw_write_req").show();
  });

  //탭 공유
  $("#write_tab_04").click(function () {
    $(".tdw_write_tab_in button").removeClass("on");
    $(this).addClass("on");
    $(".input_write").attr("placeholder", "업무를 입력해 주세요.");

    $(".tdw_write_btns").css("display","none");
    $(".tdw_write_report").hide();
    $(".tdw_write_text_report").hide();
    $(".tdw_write_date").show();
    $(".tdw_write_req").show();
    $(".tdw_write_text").show();
    //$(".tdw_write_user_desc").show();

    //받을사람선택시
    if ($("#chall_user_chk").val()) {
      if ($("#tdw_write_user_desc").is(":visible") == false) {
        $("#tdw_write_user_desc").show();
      }
    }
  });

  //일일, 주간, 월간 탭선택
  $(".tdw_date_select button").click(function () {
    $(".tdw_date_select button").removeClass("on");
    $(this).addClass("on");
   
    if ($(".select_dd").hasClass("on") == true) {
      $(".tdw_list_ww").hide();
      $(".tdw_list_mm").hide();
      $(".tdw_list_dd").show();
      $("#work_date").width("110px");

      if ($("#work_month").is(":visible") == true) {
        $("#work_month").hide();
        $("#work_date").show();
      }
    } else if ($(".select_ww").hasClass("on") == true) {
      $(".tdw_list_dd").hide();
      $(".tdw_list_mm").hide();
      $(".tdw_list_ww").show();
      $("#work_date").width("220px");
      if ($("#work_month").is(":visible") == true) {
        $("#work_month").hide();
        $("#work_date").show();
      }
    } else if ($(".select_mm").hasClass("on") == true) {
      $(".tdw_list_dd").hide();
      $(".tdw_list_ww").hide();
      $(".tdw_list_mm").show();
      $("#work_month").width("86px");

      if ($("#work_month").is(":visible") == false) {
        $("#work_month").show();
        $("#work_date").hide();
      }
    }else if($(".select_report").hasClass("on") == true){
      $(".tdw_list_dd").show();
      $(".tdw_list_ww").hide();
      $(".tdw_list_mm").hide();

      if ($("#work_month").is(":visible") == true) {
        $("#work_month").hide();
        $("#work_date").show();
      }
    }

    date_change();
    works_list();
  });

  $(".tdw_new_select button").click(function () {
    $(".tdw_new_select button").removeClass("on");
    $(".tdw_tab_sort").removeClass("on");
    $(this).addClass("on");
    var val = $(this).val();
    $(".dday").text(val);
    if ($(".select_dd").hasClass("on") == true) {
      $(".tdw_list_ww").hide();
      $(".tdw_list_mm").hide();
      $(".tdw_list_dd").show();

      if ($("#work_month").is(":visible") == true) {
        $("#work_month").hide();
        $("#work_date").show();
      }
    } else if ($(".select_ww").hasClass("on") == true) {
      $(".tdw_list_dd").hide();
      $(".tdw_list_mm").hide();
      $(".tdw_list_ww").show();
      if ($("#work_month").is(":visible") == true) {
        $("#work_month").hide();
        $("#work_date").show();
      }
    } else if ($(".select_mm").hasClass("on") == true) {
      $(".tdw_list_dd").hide();
      $(".tdw_list_ww").hide();
      $(".tdw_list_mm").show();

      if ($("#work_month").is(":visible") == false) {
        $("#work_month").show();
        $("#work_date").hide();
      }
    }else if($(".select_report").hasClass("on") == true){
      $(".tdw_list_dd").show();
      $(".tdw_list_ww").hide();
      $(".tdw_list_mm").hide();

      if ($("#work_month").is(":visible") == true) {
        $("#work_month").hide();
        $("#work_date").show();
      }
    }

    
    date_change();
    works_list();
  });

  $(document).on("click", ".btn_tdw_reset", function () {
    var searchVal = $('#search_value').val();

    if(searchVal){
      searchs_list();
    }else{
      works_list();
    }
    });


  $(".tdw_work_select button").click(function () {
    $(".tdw_work_select button").removeClass("on");
    $(".tdw_tab_sort").removeClass("on");
    var val = $(this).val();
    $(".all_work").text(val);
    $(this).addClass("on");
    if ($(".select_dd").hasClass("on") == true) {
      $(".tdw_list_ww").hide();
      $(".tdw_list_mm").hide();
      $(".tdw_list_dd").show();

      if ($("#work_month").is(":visible") == true) {
        $("#work_month").hide();
        $("#work_date").show();
      }
    } else if ($(".select_ww").hasClass("on") == true) {
      $(".tdw_list_dd").hide();
      $(".tdw_list_mm").hide();
      $(".tdw_list_ww").show();
      if ($("#work_month").is(":visible") == true) {
        $("#work_month").hide();
        $("#work_date").show();
      }
    } else if ($(".select_mm").hasClass("on") == true) {
      $(".tdw_list_dd").hide();
      $(".tdw_list_ww").hide();
      $(".tdw_list_mm").show();

      if ($("#work_month").is(":visible") == false) {
        $("#work_month").show();
        $("#work_date").hide();
      }
    }

    date_change();
    works_list();
  });

  //수정하기 열렸을때 닫기처리
  $(document).click(function (e) {
    if (
      !$(e.target).is("textarea[id^=textarea_regi]") &&
      !$(e.target).is("p[id^=tdw_list_edit]")
    ) {
      obj = $("div[id^=tdw_list_regi_edit]");
      //console.log(obj.is(":visible"));
      if (obj.is(":visible") == true) {
        obj.hide();
      }
    }

    //닫기
    if (
      !$(e.target).is("textarea[id^=textarea_wregi]") &&
      !$(e.target).is("p[id^=tdw_wlist_edit]")
    ) {
      obj = $("div[id^=tdw_wlist_regi_edit]");
      //console.log(obj.is(":visible"));
      if (obj.is(":visible") == true) {
        obj.hide();
      }
    }

    //메모닫기
    //if (!$(e.target).is("texrtarea[id^=tdw_comment_edit]")) {
    if (
      !$(e.target).is("textarea[id^=tdw_comment_edit]") &&
      !$(e.target).is("span[id^=tdw_list_memo_conts_txt]")
    ) {
      obj = $("div[id^=tdw_list_memo_regi]");
      //console.log(obj.is(":visible"));
      if (obj.is(":visible") == true) {
        obj.hide();
      }
    }

    //주간업무 메모닫기
    if (
      !$(e.target).is("textarea[id^=tdw_wcomment_edit]") &&
      !$(e.target).is("span[id^=tdw_wlist_memo_conts_txt]")
    ) {
      obj = $("div[id^=tdw_wlist_memo_regi]");
      //console.log(obj.is(":visible"));
      if (obj.is(":visible") == true) {
        obj.hide();
      }
    }

    //보고업무 내용수정 닫기
    if (
      !$(e.target).is("textarea[id^=tdw_report_edit]") &&
      !$(e.target).is("span[id^=tdw_list_report_conts_txt]")
    ) {
      obj = $("div[id^=tdw_list_report_regi]");
      //var edit_textarea = $("textarea[id^=tdw_comment_edit]");
      //console.log(edit_textarea.is(":visible"));

      if (obj.is(":visible") == true) {
        obj.hide();
      }
    }
  });

  $(".tdw_list_desc p").click(function () {
    //    $(this).closest(".tdw_list_desc").children(".tdw_list_regi").show();
  });

  //일일업무 수정하기, 취소버튼
  //$(".tdw_list .tdw_list_desc .tdw_list_regi button").click(function() {
  $(document).on(
    "click",
    ".tdw_list .tdw_list_desc .tdw_list_regi button",
    function () {
      $(this).closest(".tdw_list_regi").hide();
    }
  );

  //주간업무 수정하기, 취소버튼
  $(document).on(
    "click",
    ".tdw_list .tdw_list_desc .tdw_wlist_regi button",
    function () {
      $(this).closest(".tdw_wlist_regi").hide();
    }
  );

  //일일업무 수정하기 - 업무내용클릭시
  $(document).on("click", "p[id^='tdw_list_edit_']", function () {
    $(this).closest(".tdw_list_desc").children(".tdw_list_regi").show();

    var id = $(this).attr("id");
    var no = id.replace("tdw_list_edit_", "");

    if (no) {
      var obj_edit = $("div[id^=tdw_list_regi_edit]");
      var obj_edit_cnt = obj_edit.size();
      var elem = $("textarea[id=textarea_regi_" + no + "]");

      setTimeout(function () {
        var input = elem;
        var v = input.val();
        input.focus().val("").val(v);
      }, 50);

      for (i = 0; i < obj_edit_cnt; i++) {
        obj_edit_id = obj_edit.eq(i).attr("id");
        obj_edit_no = obj_edit
          .eq(i)
          .attr("id")
          .replace("tdw_list_regi_edit_", "");

        if (no != obj_edit_no) {
          $("#" + obj_edit_id).hide();
        }
      }
    }
  });

  //주간업무수정하기 - 업무내용클릭시
  $(document).on("click", "p[id^='tdw_wlist_edit_']", function () {
    $(this).closest(".tdw_list_desc").children(".tdw_list_regi").show();

    var id = $(this).attr("id");
    var no = id.replace("tdw_wlist_edit_", "");

    if (no) {
      var obj_edit = $("div[id^=tdw_wlist_regi_edit]");
      var obj_edit_cnt = obj_edit.size();

      var elem = $("textarea[id=textarea_wregi_" + no + "]");

      setTimeout(function () {
        var input = elem;
        var v = input.val();
        input.focus().val("").val(v);
      }, 50);

      for (i = 0; i < obj_edit_cnt; i++) {
        obj_edit_id = obj_edit.eq(i).attr("id");
        obj_edit_no = obj_edit
          .eq(i)
          .attr("id")
          .replace("tdw_wlist_regi_edit_", "");
        if (no != obj_edit_no) {
          $("#" + obj_edit_id).hide();
        }
      }
    }
  });

  //일일업무 수정하기, 확인버튼
  $(document).on("click", "#btn_regi_submit", function () {
    var val = $(this).val();
    var fdata = new FormData();
    var contents = $("#textarea_regi_" + val).val();

    if(contents==""){
      alert('수정할 내용을 입력해주세요!');
      return false;
    }

    fdata.append("mode", "tdw_regi_edit");
    fdata.append("idx", val);
    fdata.append("contents", contents);

    $.ajax({
      type: "POST",
      data: fdata,
      async: false,
      contentType: false,
      processData: false,
      url: "/inc/works_process.php",
      success: function (data) {
        console.log(data);
        if (data == "complete") {
          works_list();
          return false;
        }
      },
    });
  });

  //주간업무 수정하기, 확인버튼
  $(document).on("click", "#btn_regiw_submit", function(){
    var val = $(this).val();
    var fdata = new FormData();
    var contents = $("#textarea_wregi_" + val).val();

    fdata.append("mode", "tdw_regi_edit");
    fdata.append("idx", val);
    fdata.append("contents", contents);

    $.ajax({
      type: "POST",
      data: fdata,
      async: false,
      contentType: false,
      processData: false,
      url: "/inc/works_process.php",
      success: function (data) {
        console.log(data);
        if (data == "complete") {
          works_list();
          return false;
        }
      },
    });
  });

  $(document).on("click", ".tdw_list_desc", function () {
    //console.log("click");
  });

  //오늘업무완료/해제체크 
  $(document).on("click", "#tdw_dlist_chk", function (e) {
    var val = $(this).val();
    var wdate = $("#work_date").val();
    
    var fdata = new FormData();
    fdata.append("mode", "works_check");
    fdata.append("idx", val);
    fdata.append("wdate", wdate);
    $.ajax({
      type: "POST",
      data: fdata,
      contentType: false,
      processData: false,
      url: "/inc/works_process.php",
      success: function (data) {
        console.log(data);
        if (data == "share"){
          $("#tdw_list_box_" + val).removeClass("on");
          return false;
        } else if (data == "notice"){
          $("#tdw_list_box_" + val).removeClass("on");
          return false;
        } else if (data == "report"){
          $("#tdw_list_box_" + val).removeClass("on");
          return false;
        } else if (data == "request"){
          $("#tdw_list_box_" + val).removeClass("on");
          return false;
        }else if (data == "complete") {
          $("#tdw_list_box_" + val).addClass("on");
          // works_list();
          return false;
        } else if (data == "recomplete") {
          $("#tdw_list_box_" + val).removeClass("on");
          // works_list();
          return false;
        } else if (data == "logout") {
          $(".t_layer").show();
          return false;
        } else if (data == "req_complete") {
          alert("완료는 요청받은 사람만 가능합니다.");
          return false;
        } else if (data == "req_cancel") {
          alert("취소는 요청받은 사람만 가능합니다.");
          return false;
        } else if (data == "unclick") {
          $("#tdw_list_box_" + val).removeClass("on");
          // works_list();
          return false;
        } 
      },
    });
  });

  //업무완료/해제체크(주간)
  $(document).on("click", "#tdw_wlist_chk", function (e) {
    var val = $(this).val();
    var wdate = $("#work_date").val();
    var fdata = new FormData();
    fdata.append("mode", "works_check");
    fdata.append("idx", val);
    fdata.append("wdate", wdate);
    $.ajax({
      type: "POST",
      data: fdata,
      contentType: false,
      processData: false,
      url: "/inc/works_process.php",
      success: function (data) {
        console.log(data);
        if (data == "share"){
          $("#tdw_wlist_box_" + val).removeClass("on");
          return false;
        } else if (data == "notice"){
          $("#tdw_wlist_box_" + val).removeClass("on");
          return false;
        } else if (data == "complete") {
          $("#tdw_wlist_box_" + val).addClass("on");
          // works_list();
          return false;
        } else if (data == "recomplete") {
          $("#tdw_wlist_box_" + val).removeClass("on");
          // works_list();
          return false;
        } else if (data == "logout") {
          $(".t_layer").show();
          return false;
        } else if (data == "req_complete") {
          alert("완료는 요청받은 사람만 가능합니다.");
          return false;
        } else if (data == "req_cancel") {
          alert("취소는 요청받은 사람만 가능합니다.");
          return false;
        } else if (data == "unclick") {
          $("#tdw_wlist_box_" + val).removeClass("on");
          // works_list();
          return false;
        }
      },
    });
  });


  //일정예약(연차,반차...), 업무선택
  $(".tdw_write_btns li button").click(function() {
    var isButtonOn = $(this).hasClass("on");
    var defaultHourFirst = $(".tdw_time_start .time_set .first_set").val();
    var defaultMinFirst = $(".tdw_time_start .time_set .second_set").val();
    var defaultHourSecond = $(".tdw_time_end .time_set .first_set").val();
    var defaultMinSecond = $(".tdw_time_end .time_set .second_set").val();
    $(".tdw_write_btns li button").removeClass("on");
    $(".tdw_write_btns li button").addClass("off");
    $(".tdw_time_set").removeClass("on");
    $("#booking").val(0);
    $("#startHour").val(null);
    $("#startMin").val(null);
    $("#endHour").val(null);
    $("#endMin").val(null);

    if (!isButtonOn) {
        $(this).removeClass("off");
        $(this).addClass("on");
        var val = $(this).val();
        if (val) {
            $("#booking").val(val);
        }
        if(val != '1'){
          $(".tdw_time_set").addClass("on");
          $(".tdw_time_start .first_set span").text(defaultHourFirst);
          $(".tdw_time_start .second_set span").text(defaultMinFirst);
          $(".tdw_time_end .first_set span").text(defaultHourSecond);
          $(".tdw_time_end .second_set span").text(defaultMinSecond);

          $("#startHour").val(defaultHourFirst);
          $("#startMin").val(defaultMinFirst);
          $("#endHour").val(defaultHourSecond);
          $("#endMin").val(defaultMinSecond);
        }
      }

});


  //일정에 따른 시간 체크 
  $(".time_set .tdw_tab_sort_in").click(function(){
		$(this).find('ul').css("display", "block");
	  });
	  $(".time_set .tdw_tab_sort_in").mouseleave(function(){
		$(this).find('ul').css("display", "");

	  });

     // $("#input_todaywork_search").bind("input keyup", function (e) {
  //   var id = $(this).attr("id");
  //   var input_val = $(this).val();
  //   if (input_val) {
  //     if (e.keyCode == 13 || (e.keyCode < 91 && e.keyCode >64)) {
  //       //$(".layer_user_search_box button").trigger("click");
  //       $("#input_todaywork_search_btn").trigger("click");
  //       return false;
  //     }
  //   } else {
  //     layer_user_info_list();
  //     return false;
  //   }
  // });

  // 유저 선택 레이어 즉시반응으로 변경
  $("#input_todaywork_search").bind("input keyup", function (e) {
    var id = $(this).attr("id");
    var input_val = $(this).val();
    if(input_val){
      if (e.keyCode == 13 || (e.keyCode < 91 && e.keyCode >64)) {
        //$(".layer_user_search_box button").trigger("click");
        $("#input_todaywork_search_btn").trigger("click");
        return false;
      }
    } else {
      layer_user_info_list();
      return false;
    }
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

  //받을사람선택
  $(".btn_req").click(function () {
    $(".layer_user").show();
    $("#work_type").val("");
    $("#layer_test_02").hide();
    $("#layer_test_03").hide();
    $("#layer_test_01").show();
    $(".layer_user_info dl").addClass("on");
    //$(".layer_user_info dd button").removeClass("on");
    $(".layer_user_submit").attr("id", "layer_todaywork_user");
    $(".layer_user_search_desc strong").text("업무 받을 사람 선택");
    $("#layer_todaywork_user").text("설정하기");

    $(".layer_user_submit").removeClass("on");
    $(".layer_user_info").animate(
      {
        scrollTop: 0,
      },
      0
    );

    if ($(".layer_user").is(":visible") == true) {
      if ($("#chall_user_chk").val()) {
        if ($("#layer_todaywork_user").hasClass("on") == false) {
          $("#layer_todaywork_user").addClass("on");
        }
      }
    }
    var cuc = $("#chall_user_chk").val();
    if(cuc){
        var fdata = new FormData();
        cuc_arr = cuc.split(",");
        for(var i=0; i<cuc_arr.length; i++){
          var idx = "#udd_"+cuc_arr[i];
           $(idx).find("button").attr("class","on"); 
        }
    }
  });

  //이전날
  $(".calendar_prev").click(function () {
    var fdata = new FormData();

    fdata.append("mode", "wdate_check");
    fdata.append("day_type", "prev");

    if ($(".select_dd").hasClass("on") == true) {
      var works_type = "day";
      var wdate = $("#work_date").val();
    } else if ($(".select_ww").hasClass("on") == true) {
      var works_type = "week";
      var wdate = $("#work_date").val();
    } else if ($(".select_mm").hasClass("on") == true) {
      var works_type = "month";
      var wdate = $("#work_month").val();
    }

    fdata.append("wdate", wdate);
    fdata.append("works_type", works_type);

    $.ajax({
      type: "POST",
      data: fdata,
      async: false,
      contentType: false,
      processData: false,
      url: "/inc/works_process.php",
      success: function (data) {
        //console.log(data);
        if (data) {
          if (works_type == "month") {
            $("#work_wdate").val(data);
            $("#work_month").val(data);
          } else {
            $("#work_wdate").val(data);
            $("#work_date").val(data);
          }

          works_list();
          return false;
        }
      },
    });
  });

  //다음날
  $(".calendar_next").click(function () {
    var fdata = new FormData();

    fdata.append("mode", "wdate_check");
    fdata.append("day_type", "next");

    if ($(".select_dd").hasClass("on") == true) {
      var works_type = "day";
      var wdate = $("#work_date").val();
      fdata.append("wdate", wdate);
    } else if ($(".select_ww").hasClass("on") == true) {
      var works_type = "week";
      var wdate = $("#work_date").val();
      fdata.append("wdate", wdate);
    } else if ($(".select_mm").hasClass("on") == true) {
      var works_type = "month";
      var wdate = $("#work_month").val();
      fdata.append("wdate", wdate);
    }

    fdata.append("works_type", works_type);

    $.ajax({
      type: "POST",
      data: fdata,
      async: false,
      contentType: false,
      processData: false,
      url: "/inc/works_process.php",
      success: function (data) {
        //console.log(data);
        if (data) {
          if (works_type == "month") {
            $("#work_wdate").val(data);
            $("#work_month").val(data);
          } else {
            $("#work_wdate").val(data);
            $("#work_date").val(data);
          }
          works_list();
          return false;
        }
      },
    });
  });


  // 비밀글 아이콘 클릭

  $(document).on("click","#tdw_lock",function(){
    $(this).toggleClass("on"); 
    // alert("준비중입니다.");
  });

  //오늘업무 등록하기 버튼
  //오늘업무 중복 등록방지\
  
    $(document).on("click","#tdw_write",function(){
      var fdata = new FormData();
      var obj = $("#input_write");
      var fileobj = $("input[id='files']");
      if ($("#tdw_lock").hasClass("on") == true) {
        fdata.append("secret_flag", "1");
      }else{
        fdata.append("secret_flag", "0");
      }

      //나의업무
      if ($("#write_tab_01").hasClass("on") == true) {
        fdata.append("work_flag", "2");

        if (obj.val() == "") {
          alert("업무를 입력해 주세요.");
          obj.focus();
          return false;
        }

        if ($("#booking").val()) {
          var sHour = $("#startHour").val();
          var sMin = $("#startMin").val();
          var eHour = $("#endHour").val();
          var eMin = $("#endMin").val();
         
          if ($("#startHour").val() && $("#startMin").val() && $("#endHour").val() && $("#endMin").val()){
            var startTime = sHour+":"+sMin; 
            var endTime = eHour+":"+eMin;

            //시간으로 변경하여 비교하기
            var realStart = timeToMinutes(startTime);
            var realend = timeToMinutes(endTime);

            if(realStart > realend){
              alert("시작 시간은 종료 시간보다 작아야합니다.");
              return false;
            }else if(realStart == realend){
              alert("시작시간은 종료 시간보다 작아야합니다.");
              return false;
            }
          }
          fdata.append("start_time", startTime);
          fdata.append("end_time", endTime);
          fdata.append("decide_flag", $("#booking").val());
        }

       

        if ($("#workdate").val()) {
          fdata.append("workdate", $("#workdate").val());
        }

        //요청업무
      } else if ($("#write_tab_02").hasClass("on") == true) {
        fdata.append("work_flag", "3");

        if (obj.val() == "") {
          alert("요청할 업무를 입력해주세요.");
          obj.focus();
          return false;
        }

        
        if ($("#workdate").val()) {
          fdata.append("workdate", $("#workdate").val());
        }

        if ($("#chall_user_chk").val() == "") {
          alert("업무요청 받을 사람을 선택 해주세요.");
          return false;
        }

        if ($("#chall_user_chk").val()) {
          fdata.append("work_user_chk", $("#chall_user_chk").val());
        }

        //보고업무
      } else if ($("#write_tab_03").hasClass("on") == true) {
        fdata.append("work_flag", "1");

        if (!$("#work_title").val()) {
          alert("제목을 작성해 주세요.");
          $("#work_title").focus();
          return false;
        }

        if (!$("#work_contents").val()) {
          alert("보고할 내용을 작성해 주세요.");
          $("#work_contents").focus();
          return false;
        }

        if ($("#chall_user_chk").val() == "") {
          alert("보고 받을 사람을 선택 해주세요.");
          return false;
        }

        if ($("#work_title").val()) {
          fdata.append("work_title", $("#work_title").val());
        }

        if ($("#work_contents").val()) {
          fdata.append("work_contents", $("#work_contents").val());
        }

        if ($("#chall_user_chk").val()) {
          fdata.append("work_user_chk", $("#chall_user_chk").val());
        }
        //공유업무
      } else if ($("#write_tab_04").hasClass("on") == true) {
        fdata.append("work_flag", "2");
        fdata.append("share_flag", "1");

        if (obj.val() == "") {
          alert("공유할 업무를 입력해주세요.");
          obj.focus();
          return false;
        }

        if ($("#chall_user_chk").val() == "") {
          alert("공유할 사람을 선택해 주세요.");
          return false;
        }

        if ($("#chall_user_chk").val()) {
          fdata.append("work_user_chk", $("#chall_user_chk").val());
        }

        if ($("#workdate").val()) {
          fdata.append("workdate", $("#workdate").val());
        }
      }

      //파티연결이 있을경우
      partyNo = $("#be_party_idx").val();
      if(partyNo) {
        fdata.append("be_party_idx", partyNo);
      }

      //첨부파일이 있을경우
      if (fileobj) {
        var file_cnt = $(".tdw_write_area_in #tdw_write_file_desc").length;
        var file_obj = $("input[id='files']");
        if (fileListArr.length > 0) {
          for (i = 0; i < fileListArr.length; i++) {
            //console.log(fileListArr[i]);
            //console.log(i + "  == " + fileListArr[i]['name']);
            fdata.append("files[]", fileListArr[i]);
          }
        }
      }
      fdata.append("contents",obj.val());
      fdata.append("mode", "works_write");
      $.ajax({
        type: "POST",
        data: fdata,
        contentType: false,
        processData: false,
        url: "/inc/works_process.php",
        beforeSend: function () {
          $('.rewardy_loading_01').css('display', 'block');
        },
        // complete: function (){
        //   $('.rewardy_loading_01').css('display', 'none');
        // },
        success: function (data) {
          console.log(data);
          $(".tdw_write_par").removeClass(" on");
          $(".tdw_write_file_desc").remove(); 
          $(".tdw_write_user_desc").hide(); 
          $(".tdw_lock").removeClass(" on");
          $("#layer_user_slc_list_in ul").html("");
          $(".layer_user_info dd button").removeClass("on");
          $(".layer_user_btn").find(".layer_user_all_slc").removeClass("on");
          $("#chall_user_chk").attr("value","");
          if (data) {
            var tdata = data.split("|"); 
            // work_list() 활성화
            if (tdata) {
              result = tdata[0]; //성공여부
              result1 = tdata[1]; // 업무유형
              result2 = tdata[2]; // 오늘이 아닐경우 날짜
              result3 = tdata[3]; // ajax 적용될 영역
              result4 = tdata[4]; // 점수 오를시 뜨는 문구
              // result5 = tdata[5];

              // console.log(">>>>"+result);
              // console.log(">>>>"+result1);
              // console.log(">>>>"+result2);
              // console.log(">>>>"+result3);
              // console.log(">>>>"+result4);
              // console.log(">>>>"+result5);

              // // if(partyNo){
              //   party_link();
              // }
              
              $("#be_party_idx").val("");

              if ($("#booking").val()) {
                $("#booking").val("");
              }
              
              $(".tdw_time_set").removeClass("on");


              if (result == "files_size_over") {
                alert("첨부파일 사이즈는 100MB 이내로 등록 가능합니다.");
                return false;
              } else if (result == "files_format") {
                alert("첨부할 수 없는 파일입니다.");
                return false;
              } else if (result == "prev_not") {
                alert(
                  "해당 업무를 오늘 날짜보다 이전 날짜로 등록할 수 없습니다."
                );
                return false;
              } else {
                //등록완료
                if (result == "complete") {
                  //첨부파일초기화
                  if (fileListArr.length > 0) {
                    //파일삭제하기
                    //todaywork_file_del(fileListArr);
                    for (i = 0; i < fileListArr.length; i++) {
                      $("#tdw_write_file_desc").remove();
                    }

                    fileListArr = new Array();
                    if ($("input[id='files']").val()) {
                      $("input[id='files']").val("");
                    }
                  }

                  //오늘업무 등록완료
                  if (result4) {
                      $("#rew_popup_in").text(result4);
                      $("#rew_popup").show();
                      setTimeout(function () {
                        $("#rew_popup").hide();
                      }, 4700);
                  }

                  //나의업무
                  if (result1 == "mywork") {
                    if ($("#work_wdate").val()) {
                      var wwdate = $("#work_wdate").val().substr(0, 10);
                      var twdfeeling = wwdate.replace(/\./g, "-");
                      // $("div[id=tdw_feeling_banner_" + twdfeeling + "]").hide();
                    }

                    //날짜 형식으로 포함되었을 경우
                    if (result2.indexOf("-") != -1) {
                      if (
                        confirm(
                          "업무가 등록 되었습니다.\n내용을 확인하시겠습니까?"
                        )
                      ) {
                        $("#work_wdate").val(result2);
                        $("#work_date").val(result2);

                        // $("#workdate").val(get_today());
                        $("#workdate").val(result2);
                        $(".input_write").val("");
                        $("#write_tab_01_new").trigger("click");
                        $(".select_dd").trigger("click");
                        date_change();
                        works_list();
                      } else {
                        $("#workdate").val(result2);
                        $(".input_write").val("");
                        $("#write_tab_01_new").trigger("click");
                        $(".select_dd").trigger("click");
                         works_list();
                      }
                    } else {
                      if ($("#work_wdate").val() != $("#workdate").val()) {
                        $("#work_wdate").val(get_today());
                        // $(".select_dd").trigger("click");
                        $(".input_write").val("");
                        date_change();
                        works_list();
                      }else{

                        $(".input_write").val("");
                        if ($(".select_dd").hasClass("on") == false) {
                          $(".select_dd").trigger("click");
                          // works_list();
                          // date_change();
                        }else{
                          $(".select_dd").trigger("click");
                          // works_list();
                          // date_change();
                        }
                      }
                      if ($("#chall_user_chk").val()) {
                        $("#chall_user_chk").val("");
                      }

                      if ($(".title_desc_01").text()) {
                        $(".title_desc_01").text("");
                      }
                      $(".tdw_write_btns button").removeClass("on");
                      if ($("#layer_test_01").is(":visible") == true) {
                        $(".layer_user_cancel").trigger("click");
                      }

                      //works_list();
                      return false;
                    }
                    //업무요청
                  } else if (result1 == "reqwork") {
                    //if (GetCookie("user_id") == "sadary0@nate.com" || GetCookie("user_id") == "dasani003@nate.com") {
                    //    notify();
                    //}

                    //날짜 형식으로 포함되었을 경우
                    if (result2.indexOf("-") != -1) {
                      if (
                        confirm(
                          "업무 요청이 완료되었습니다.\n내용을 확인하시겠습니까?"
                        )
                      ) {
                        $("#work_wdate").val(result2);
                        $("#work_date").val(result2);

                        // $("#workdate").val(get_today());
                        $("#workdate").val(result2);
                        $(".input_write").val("");

                        if ($("#chall_user_chk").val()) {
                          $("#chall_user_chk").val("");
                        }

                        $("#write_tab_02_new").trigger("click");
                        $(".select_dd").trigger("click");
                      } else {
                        $("#workdate").val(result2);
                        $(".input_write").val("");
                        if ($("#chall_user_chk").val()) {
                          $("#chall_user_chk").val("");
                        }

                        $("#write_tab_02_new").trigger("click");
                        $(".select_dd").trigger("click");
                         works_list();
                      }
                    } else {
                      if ($("#work_wdate").val() != $("#workdate").val()) {
                        $("#work_wdate").val(get_today());
                        $(".input_write").val("");
                        works_list();
                        date_change();
                      }

                      $(".input_write").val("");
                      if ($(".select_dd").hasClass("on") == false) {
                        $(".select_dd").trigger("click");
                      } else {
                        $(".select_dd").trigger("click");
                      }

                      if ($("#chall_user_chk").val()) {
                        $("#chall_user_chk").val("");
                      }

                      if ($(".title_desc_01").text()) {
                        $(".title_desc_01").text("");
                      }
                      $(".tdw_write_btns button").removeClass("on");
                      $(".layer_user_cancel").trigger("click");
                      //works_list();
                      return false;
                    }
                    //보고업무
                  } else if (result1 == "report") {
                    //console.log("report");
                    if (
                      GetCookie("user_id") == "sadary0@nate.com" ||
                      GetCookie("user_id") == "dasani003@nate.com"
                    ) {
                      //    console.log("시작");
                      //    notify();
                      //    console.log("종료");
                    }

                    //제목
                    if ($("#work_title").val()) {
                      $("#work_title").val("");
                    }

                    //보고내용
                    if ($("#work_contents").val()) {
                      $("#work_contents").val("");
                    }

                    if ($(".select_dd").hasClass("on") == false) {
                      $(".select_dd").trigger("click");
                    } else {
                      $(".select_dd").trigger("click");
                    }

                    if ($("#chall_user_chk").val()) {
                      $("#chall_user_chk").val("");
                      $("#tdw_write_user_desc").hide();
                    }

                    if ($(".title_desc_01").text()) {
                      $(".title_desc_01").text("");
                    }
                    $(".tdw_write_btns button").removeClass("on");
                    $(".layer_user_cancel").trigger("click");
                  } else {
                    alert(
                      "업무 등록에 문제가 발생하였습니다.\n\n관리자에게 문의하세요!"
                    );
                    console.log(result1);
                    return false;
                  }
                } else {
                  alert(
                    "업무 등록에 문제가 발생하였습니다.\n\n관리자에게 문의하세요!!!"
                  );
                  console.log(result);
                  return false;
                }
              }
              $('.rewardy_loading_01').css('display', 'none');
            }
            
          }
        },
      });
    });
      
    

  //오늘업무 등록하기 버튼
  $("#tdw_write_btn_new").click(function () {
    var fdata = new FormData();
    var obj = $(".input_write");

    if ($("#write_tab_01_new").hasClass("on") == true) {
      fdata.append("work_flag", "2");

      if (obj.val() == "") {
        alert("할 일을 입력해주세요.");
        obj.focus();
        return false;
      }

      if ($("#booking").val()) {
        fdata.append("decide_flag", $("#booking").val());
      }

      if ($("#workdate").val()) {
        fdata.append("workdate", $("#workdate").val());
      }
    } else if ($("#write_tab_02_new").hasClass("on") == true) {
      fdata.append("work_flag", "3");

      if (obj.val() == "") {
        alert("요청할 업무를 입력해주세요.");
        obj.focus();
        return false;
      }

      if ($("#workdate").val()) {
        fdata.append("workdate", $("#workdate").val());
      }

      if ($("#chall_user_chk").val() == "") {
        alert("업무요청 받을 사람을 선택 해주세요.");
        return false;
      }

      if ($("#chall_user_chk").val()) {
        fdata.append("work_user_chk", $("#chall_user_chk").val());
      }
    }

    fdata.append("work_title", obj.val());
    fdata.append("mode", "works_write");
    $.ajax({
      type: "POST",
      data: fdata,
      contentType: false,
      processData: false,
      url: "/inc/works_process.php",
      success: function (data) {
        //console.log(data);
        if (data) {
          var tdata = data.split("|");
          if (tdata) {
            result = tdata[0];
            result1 = tdata[1];
            result2 = tdata[2];
            result3 = tdata[3];

            if ($("#booking").val()) {
              $("#booking").val("");
            }

            
            if (result == "prev_not") {
              alert(
                "해당 업무를 오늘 날짜보다 이전 날짜로 등록할 수 없습니다."
              );
              return false;
            } else {
              //등록완료
              if (result == "complete") {
                //나의업무
                if (result1 == "mywork") {
                  if ($("#work_wdate").val()) {
                    var wwdate = $("#work_wdate").val().substr(0, 10);
                    var twdfeeling = wwdate.replace(/\./g, "-");
                    $("div[id=tdw_feeling_banner_" + twdfeeling + "]").hide();
                  }

                  //날짜 형식으로 포함되었을 경우
                  if (result2.indexOf("-") != -1) {
                    if (
                      confirm(
                        "업무가 등록 되었습니다.\n내용을 확인하시겠습니까?"
                      )
                    ) {
                      $("#work_wdate").val(result2);
                      $("#work_date").val(result2);

                      $("#workdate").val(get_today());
                      $(".input_write").val("");
                      $("#write_tab_01_new").trigger("click");
                      $(".select_dd").trigger("click");
                    } else {
                      $("#workdate").val(get_today());
                      $(".input_write").val("");
                      $("#write_tab_01_new").trigger("click");
                      $(".select_dd").trigger("click");
                      works_list();
                    }
                  } else {
                    if ($("#work_wdate").val() != $("#workdate").val()) {
                      $("#work_wdate").val(get_today());
                      $(".select_dd").trigger("click");
                    }

                    $(".input_write").val("");
                    if ($(".select_dd").hasClass("on") == false) {
                      $(".select_dd").trigger("click");
                    } else {
                      works_list();
                    }

                    if ($("#chall_user_chk").val()) {
                      $("#chall_user_chk").val("");
                    }

                    if ($(".title_desc_01").text()) {
                      $(".title_desc_01").text("");
                    }
                    $(".tdw_write_btns button").removeClass("on");
                    $(".layer_user_cancel").trigger("click");
                    //works_list();
                    return false;
                  }
                  //업무요청
                } else if (result1 == "reqwork") {
                  //날짜 형식으로 포함되었을 경우
                  if (result2.indexOf("-") != -1) {
                    if (
                      confirm(
                        "업무 요청이 완료되었습니다.\n내용을 확인하시겠습니까?"
                      )
                    ) {
                      $("#work_wdate").val(result2);
                      $("#work_date").val(result2);

                      $("#workdate").val(get_today());
                      $(".input_write").val("");

                      if ($("#chall_user_chk").val()) {
                        $("#chall_user_chk").val("");
                      }

                      $("#write_tab_02_new").trigger("click");
                      $(".select_dd").trigger("click");
                    } else {
                      $("#workdate").val(get_today());
                      $(".input_write").val("");
                      if ($("#chall_user_chk").val()) {
                        $("#chall_user_chk").val("");
                      }

                      $("#write_tab_02_new").trigger("click");
                      $(".select_dd").trigger("click");
                      works_list();
                    }
                  } else {
                    if ($("#work_wdate").val() != $("#workdate").val()) {
                      $("#work_wdate").val(get_today());
                      $(".select_dd").trigger("click");
                    }

                    $(".input_write").val("");
                    if ($(".select_dd").hasClass("on") == false) {
                      $(".select_dd").trigger("click");
                    } else {
                      works_list();
                    }

                    if ($("#chall_user_chk").val()) {
                      $("#chall_user_chk").val("");
                    }

                    if ($(".title_desc_01").text()) {
                      $(".title_desc_01").text("");
                    }
                    $(".tdw_write_btns button").removeClass("on");
                    $(".layer_user_cancel").trigger("click");
                    //works_list();
                    return false;
                  }
                }
              }
            }
          }
        }
      },
    });
  });

  //오늘업무 등록하기 버튼(이전에 사용된 버튼)
  $("#tdw_write_btn_old").click(function () {
    var fdata = new FormData();
    var obj = $(".input_write");

    if ($("#write_tab_01").hasClass("on") == true) {
      fdata.append("work_flag", "1");

      if (obj.val() == "") {
        alert("할 일을 입력해주세요.");
        obj.focus();
        return false;
      }
    } else if ($("#write_tab_02").hasClass("on") == true) {
      fdata.append("work_flag", "2");

      if (obj.val() == "") {
        alert("예약할 업무를 입력해주세요.");
        obj.focus();
        return false;
      }

      if ($("#booking").val()) {
        fdata.append("decide_flag", $("#booking").val());
      }

      if ($("#workdate").val()) {
        fdata.append("workdate", $("#workdate").val());
      }
    } else if ($("#write_tab_03").hasClass("on") == true) {
      fdata.append("work_flag", "3");

      if (obj.val() == "") {
        alert("요청할 업무를 입력해주세요.");
        obj.focus();
        return false;
      }

      if ($("#chall_user_chk").val() == "") {
        alert("업무요청 받을 사람을 선택 해주세요.");
        return false;
      }

      if ($("#chall_user_chk").val()) {
        fdata.append("work_user_chk", $("#chall_user_chk").val());
      }
    }

    fdata.append("contents", obj.val());
    fdata.append("mode", "works_write");
    $.ajax({
      type: "POST",
      data: fdata,
      contentType: false,
      processData: false,
      url: "/inc/works_process.php",
      success: function (data) {
        console.log(data);
        if (data) {
          var tdata = data.split("|");
          if (tdata) {
            result = tdata[0];
            result1 = tdata[1];
            if ($("#booking").val()) {
              $("#booking").val("");
            }

            if (result == "prev_not") {
              alert(
                "해당 업무를 오늘 날짜보다 이전 날짜로 등록할 수 없습니다."
              );
              return false;
            } else {
              //날짜 형식으로 포함되었을 경우
              if (result1) {
                if (result1.indexOf("-") != -1) {
                  if (
                    confirm(
                      "업무 예약이 완료되었습니다.\n내용을 확인하시겠습니까?"
                    )
                  ) {
                    $("#work_wdate").val(result1);
                    $("#work_date").val(result1);

                    $("#workdate").val(get_today());
                    $("#write_tab_02").trigger("click");
                    $(".select_dd").trigger("click");
                  } else {
                    $("#workdate").val(get_today());
                    $("#write_tab_02").trigger("click");
                    $(".select_dd").trigger("click");
                    works_list();
                  }
                } else {
                  if (result == "complete") {
                    if ($("#work_wdate").val() != $("#workdate").val()) {
                      $("#work_wdate").val(get_today());

                      $(".select_dd").trigger("click");
                    }

                    $(".input_write").val("");
                    if ($(".select_dd").hasClass("on") == false) {
                      $(".select_dd").trigger("click");
                    } else {
                      works_list();
                    }

                    if ($("#chall_user_chk").val()) {
                      $("#chall_user_chk").val("");
                    }

                    if ($(".title_desc_01").text()) {
                      $(".title_desc_01").text("");
                    }
                    $(".tdw_write_btns button").removeClass("on");
                    $(".layer_user_cancel").trigger("click");

                    //works_list();
                    return false;
                  } else if (result == "logout") {
                    $(".t_layer").show();
                    return false;
                  }
                }
              }
            }
          }
        }
      },
    });
  });

  //오늘업무 삭제하기
  $(document).on("click", "#notice_list_del", function () {
    var val = $(this).val();

    if (val) {
      if (confirm("알림을 삭제하시겠습니까?")) {
        var wdate = $("#work_date").val();
        var fdata = new FormData();
        fdata.append("mode", "works_notice_del");
        fdata.append("idx", val);
        fdata.append("wdate", wdate);

        $.ajax({
          type: "POST",
          data: fdata,
          contentType: false,
          processData: false,
          url: "/inc/works_process.php",
          success: function (data) {
            console.log(data);
            if (data == "complete") {
              works_list();
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



   //오늘업무 삭제하기 자신의 것만
   $(document).on("click", "#tdw_list_per_del", function () {
    var val = $(this).val();

    if (val) {
      if (confirm("업무내용을 삭제하시겠습니까?")) {
        var wdate = $("#work_date").val();
        var fdata = new FormData();
        fdata.append("mode", "works_per_del");
        fdata.append("idx", val);
        fdata.append("wdate", wdate);

        $.ajax({
          type: "POST",
          data: fdata,
          contentType: false,
          processData: false,
          url: "/inc/works_process.php",
          beforeSend: function () {
            $('.rewardy_loading_01').css('display', 'block');
          },
          // complete: function (){
          //   $('.rewardy_loading_01').css('display', 'none');
          // },
          success: function (data) {
            console.log(data);
            if (data == "complete") {
              works_list();
              return false;
            } else if (data == "logout") {
              $(".t_layer").show();
              return false;
            }
            $('.rewardy_loading_01').css('display', 'none');
          },
        });
      }
    }
  });



  //오늘업무 삭제하기
  $(document).on("click", "#tdw_list_del", function () {
    var val = $(this).val();

    if(val){
      if (confirm("업무내용을 삭제하시겠습니까?")) {
        var wdate = $("#work_date").val();
        var fdata = new FormData();
        fdata.append("mode", "works_del");
        fdata.append("idx", val);
        fdata.append("wdate", wdate);

        $.ajax({
          type: "POST",
          data: fdata,
          contentType: false,
          processData: false,
          url: "/inc/works_process.php",
          beforeSend: function () {
            $('.rewardy_loading_01').css('display', 'block');
          },
          complete: function (){
            $('.rewardy_loading_01').css('display', 'none');
          },
          success: function (data) {
            console.log(data);
            if (data == "complete") {
              works_list();
              return false;
            } else if (data == "logout") {
              $(".t_layer").show();
              return false;
            } else if (data == "work_not_del") {
              alert("요청받은 업무는 삭제할 수 없습니다.");
              return false;
            } else if (data == "share_not_del") {
              alert("공유받은 업무는 삭제할 수 없습니다.");
              return false;
            }
          },
        });
      }
    }
  });

  //공유된 업무 삭제하기
  $(document).on("click", "#tdw_share_del", function () {
    var val = $(this).val();
    if (val) {
      if (confirm("공유된 업무입니다.\n그래도 삭제하시겠습니까?")) {
        var wdate = $("#work_date").val();
        var fdata = new FormData();
        fdata.append("mode", "works_share_del");
        fdata.append("idx", val);
        fdata.append("wdate", wdate);

        $.ajax({
          type: "POST",
          data: fdata,
          contentType: false,
          processData: false,
          url: "/inc/works_process.php",
          success: function (data) {
            console.log(data);
            if (data == "complete") {
              works_list();
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

  //월간 일정 클릭시
  $(document).on("click", ".tdw_list_mm .month_box button ul li", function () {
    var val = $(this).val();
    var tmp = $(this).attr("id").split("tdwlist_");
    if (tmp) {
      $("#work_wdate").val(tmp[1]);
      $("#work_date").val(tmp[1]);
      $(".select_dd").trigger("click");
    }
  });

  //월간 일정 클릭시, 한줄 소감
  $(document).on(
    "click",
    ".tdw_list_mm .month_box strong[id^=tdwlist]",
    function () {
      var val = $(this).val();
      var tmp = $(this).attr("id").split("tdwlist_");
      if (tmp) {
        $("#work_wdate").val(tmp[1]);
        $("#work_date").val(tmp[1]);

        //feeling_banner_reload(tmp[1]);

        $(".select_dd").trigger("click");
      }
    }
  );

  //내일로 미루기
  $(document).on("click", ".tdw_list_tomorrow", function () {
    var val = $(this).val();
    var fdata = new FormData();
    fdata.append("mode", "list_yesterday");
    fdata.append("idx", val);

    if (confirm("해당 업무를 내일로 미루시겠습니까?")) {
      $.ajax({
        type: "POST",
        data: fdata,
        contentType: false,
        processData: false,
        url: "/inc/works_process.php",
        success: function (data) {
          console.log(data);
          if (data == "complete") {
            works_list();
            return false;
          } else if (data == "logout") {
            //  $(".t_layer").show();
            //  return false;
          }
        },
      });
    }
  });

  /*$('#wdate_calendar').daterangepicker({
        opens: 'left',
        timePicker: true,
        showDropdowns: true,
        startDate: moment().startOf('hour'),
        endDate: moment().startOf('hour').add(32, 'hour'),
        locale: {
            "format": 'YYYY-MM-DD',
            "applyLabel": "확인", // 확인 버튼 텍스트
            "cancelLabel": "취소", // 취소 버튼 텍스트
            "daysOfWeek": ["일", "월", "화", "수", "목", "금", "토"],
            "monthNames": ["1월", "2월", "3월", "4월", "5월", "6월", "7월", "8월", "9월", "10월", "11월", "12월"]
        }

    }, function(start, end, label) {
        //console.log("A new date selection was made: " + start.format('YYYY-MM-DD') + ' to ' + end.format('YYYY-MM-DD'));
    });*/

  //일정예약 달력아이콘 클릭
  $(document).on("click", ".tdw_write_date", function () {
    var elem = $("#workdate");
    setTimeout(function () {
      var input = elem;
      var v = input.val();
      input.focus().val("").val(v);
    }, 50);
  });

 

  /*$("input[id^=listdate]").scroll(function() {
        var scrolly = $(this).offset().top;
        var scrolln = $(this).scrollTop();
    });*/

  $(".tdw_list").on("mousewheel", function (e) {
    var wheel = e.originalEvent.wheelDelta;

    //console.log(wheel);

    //$("input[id^=listdate]").datepicker().hide();
  });

  $("input[id^=listdate]").on("mousewheel", function (e) {
    var wheel = e.originalEvent.wheelDelta;
    //스크롤값을 가져온다.
    if (wheel > 0) {
      //스크롤 올릴때
      console.log("올림..");
    } else {
      //스크롤 내릴때
      console.log("내림..");
    }
  });

  //오늘업무 업무 받을사람 선택 설정하기 버튼
  $(document).on("click", "#layer_todaywork_user", function () {
    if ($(this).hasClass("on")) {
      if ($("#chall_user_chk").val()) {
        var input_usrchk = "";

        user_chk_val = $("#chall_user_chk").val();
        console.log(user_chk_val);
        var abt = $(".layer_user_info dd button");

        if (user_chk_val) {
          arr_val = user_chk_val.split(",");
          for (var i = 0; i < arr_val.length; i++) {
            if (i > 0) {
              comma = ",";
            } else {
              comma = "";
            }

            /*
            //console.log(GetCookie("user_id") + " == " + abt.eq(i).find(".user_name").attr("value"));
                        //var v = $(this).parent().parent().parent().find(".layer_user_info user_name").html();

                        //console.log(">> " + $(".layer_user_info li button").hasClass("on"));

                        if ($(".layer_user_info li").hasClass("on") == true) {
                            //console.log(" id :: " + $(".layer_user_info li dt button").html());
                        }

                        for (i = 0; i < $(".layer_user_info li").length; i++) {
                            //console.log($(".layer_user_info li").eq(i).find(".btn_team_slc").hasClass("on"));
                            if ($(".layer_user_info li").eq(i).find(".btn_team_slc").hasClass("on") == true) {
                                var vv = $(".layer_user_info li").eq(i).find(".btn_team_slc").attr("id");
                                var id = $(".layer_user_info li").eq(i).find(".btn_team_slc").parent().parent().find("dd button").attr("id");
                                //    console.log($("button[id=" + id + "]").length);
                            }
                        }*/

            //본인선택시 제외
            if (GetCookie("user_id") == member_clist_id[arr_val[i]]) {
              if ($("#write_tab_03").hasClass("on") == true) {
                alert("본인에게 보고를 할 수 없습니다.");
                $("#chall_user_chk").val("");
                return false;
              } else {
                var altext = $("#layer_todaywork_user").text();
                if (altext == "공유하기") {
                  alert("본인에게 공유할 수 없습니다.");
                } else {
                  alert("본인에게 업무요청을 할 수 없습니다.");
                }
                $("#chall_user_chk").val("");
                return false;
              }
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

      //if ($(".layer_user_search_box input").val()) {
      //    $(".layer_user").hide();

      //} else {
      todayworks_user_name();
      //$(".layer_user").hide();
      //}
    }
  });

  //반복설정 클릭
  $(document).on("click", "#tdw_list_repeat", function () {
    $(this).next(".tdw_list_repeat_list").show();
    
   var feeling = $("button[id^=btn_feeling_banner]").val();
   console.log(feeling);
   if(!feeling){
    var height = $(".tdw_list_ul").height();
    var repeat_height = height + 120;

    // console.log(height);
    $(".tdw_list_ul").css("height",repeat_height+"px");
   }
  });

  //반복설정 포커스 벗어날때
  $(document).on("mouseleave", ".tdw_list .tdw_list_repeat_box", function () {
    $(".tdw_list_repeat_list").hide();

  });

  //반복설정 선택
  $(document).on(
    "click",
    ".tdw_list .tdw_list_repeat_list button",
    function () {
      var this_text = $(this).text();
      var id = $(this).attr("id");
      var val = $(this).val();

      repeat_list(id, val);
      $(this).closest(".tdw_list_repeat_box").addClass("on");
      $(this)
        .closest(".tdw_list_repeat_box")
        .find(".tdw_list_repeat")
        .text(this_text);
      $(".tdw_list_repeat_list").hide();
    }
  );

  

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

  //0831 메모 열기/접기(주간업무)
  // $(document).on("click", "button[id^=btn_list_memo_onoff]", function () {
  //   var memo_onoff = $(this);
  //   var val = $(this).val();

  //   if (val) {
  //     $("#tdw_list_memo_area_in_" + val).toggleClass("off");
  //     //$("#memo_area_list_" + val).toggleClass("off");
  //     $(this).toggleClass("off");

  //     var fdata = new FormData();
  //     fdata.append("mode", "btn_list_memo_onoff");
  //     fdata.append("work_idx", val);

  //     if (memo_onoff.hasClass("off")) {
  //       memo_onoff.removeClass("on");
  //       $("#btn_list_memo_onoff_" + val).removeClass("on");
  //       fdata.append("onoff", "1");
  //     } else {
  //       memo_onoff.addClass("on");
  //       $("#btn_list_memo_onoff_" + val).addClass("on");
  //       fdata.append("onoff", "0");
  //     }

  //     $.ajax({
  //       type: "POST",
  //       data: fdata,
  //       //async: false,
  //       contentType: false,
  //       processData: false,
  //       url: "/inc/works_process.php",
  //       success: function (data) {
  //         console.log(data);
  //         if (data == "complete") {
  //           memo_line_check();
  //         }
  //       },
  //     });
  //   }
  // });

  //0901 메모 열기/접기 보여지는 기준 : 메모 영역 높이기준이라 메모 삭제 시 다시 계산 필요
  setTimeout(function () {
    $(".tdw_list_memo_area_in").each(function () {
      var maih = $(this);
      if (maih.height() > 110) {
        maih.next($(".tdw_list_memo_onoff")).show();
      }
    });
  }, 400);

  //업무내용 열기/접기(일일)
  $(document).on("click", "button[id^=btn_list_work_onoff]", function () {
    var memo_onoff = $(this);
    var val = $(this).val();
    var buttonWithOnClass = document.querySelector('.tdw_new_select .on');

    
    if (val) {
      $("#tdw_list_share_area_in_" + val).toggleClass("off");

      $(this).toggleClass("off");

      var fdata = new FormData();
      fdata.append("mode", "btn_list_work_onoff");
      fdata.append("work_idx", val);
      if(buttonWithOnClass.value == "일일"){
        if ($("#tdw_list_box_" + val).height() == "40") {
          $("#btn_list_work_onoff_" + val).removeClass("off");
          $("#btn_list_work_onoff_" + val).addClass("on");

          $("#tdw_list_box_" + val).removeClass("off");

          fdata.append("onoff", "0");
        } else {
          $("#btn_list_work_onoff_" + val).removeClass("on");
          $("#btn_list_work_onoff_" + val).addClass("off");

          $("#tdw_list_box_" + val).addClass("off");
          fdata.append("onoff", "1");
        }
      }else if(buttonWithOnClass.value == "주간"){
        if (memo_onoff.hasClass("off")) {
          $("#btn_list_work_onoff_" + val).removeClass("off");
          $("#btn_list_work_onoff_" + val).addClass("on");
          $("#tdw_wlist_box_" + val).removeClass("off");
  
          fdata.append("onoff", "0");
          $("#btn_list_work_onoff_" + val).attr("title", "보고 펼치기");
        } else {
          $("#btn_list_work_onoff_" + val).removeClass("on");
          $("#btn_list_work_onoff_" + val).addClass("off");
          $("#tdw_wlist_box_" + val).addClass("off");
  
          fdata.append("onoff", "1");
          $("#btn_list_work_onoff_" + val).attr("title", "보고 접기");
        }
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

   //업무내용 열기/접기(주간)
  //  $(document).on("click", "button[id^=btn_list_work_onoff]", function () {
  //   var memo_onoff = $(this);
  //   var val = $(this).val();
  //   console.log($("#tdw_wlist_box_" + val).height());
  //   if (val) {
  //     var fdata = new FormData();
  //     fdata.append("mode", "btn_list_work_onoff");
  //     fdata.append("work_idx", val);

  //     if (memo_onoff.hasClass("off")) {
  //       $("#btn_list_work_onoff_" + val).removeClass("off");
  //       $("#btn_list_work_onoff_" + val).addClass("on");
  //       $("#tdw_wlist_box_" + val).removeClass("off");

  //       fdata.append("onoff", "0");
  //       $("#btn_list_work_onoff_" + val).attr("title", "보고 펼치기");
  //     } else {
  //       $("#btn_list_work_onoff_" + val).removeClass("on");
  //       $("#btn_list_work_onoff_" + val).addClass("off");
  //       $("#tdw_wlist_box_" + val).addClass("off");

  //       fdata.append("onoff", "1");
  //       $("#btn_list_work_onoff_" + val).attr("title", "보고 접기");
  //     }

  //     $.ajax({
  //       type: "POST",
  //       data: fdata,
  //       //async: false,
  //       contentType: false,
  //       processData: false,
  //       url: "/inc/works_process.php",
  //       success: function (data) {
  //         console.log(data);
  //         if (data == "complete") {
  //           memo_line_check();
  //         }
  //       },
  //     });
  //   }
  // });


  //공유내용 열기/접기(일일)
  $(document).on("click", "button[id^=btn_list_share_onoff]", function () {
    var memo_onoff = $(this);
    var buttonWithOnClass = document.querySelector('.tdw_new_select .on');

    var val = $(this).val();
    if (val) {
      $("#tdw_list_share_area_in_" + val).toggleClass("off");

      $(this).toggleClass("off");

      var fdata = new FormData();
      fdata.append("mode", "btn_list_share_onoff");
      fdata.append("work_idx", val);

      if(buttonWithOnClass.value == "일일"){
          if ($("#tdw_list_box_" + val).height() == "40") {

            $("#btn_list_share_onoff_" + val).removeClass("off");
            $("#btn_list_share_onoff_" + val).addClass("on");

            $("#tdw_list_box_" + val).removeClass("off");

            fdata.append("onoff", "0");
            $("#btn_list_share_onoff_" + val).attr("title", "공유 접기");
          } else {

            $(".tdw_list_share_onoff").removeClass("off");
            $("#btn_list_share_onoff_" + val).removeClass("on");

            $("#tdw_list_box_" + val).addClass("off");

            fdata.append("onoff", "1");

            $("#btn_list_share_onoff_" + val).attr("title", "공유 펼치기");
          }
     }else if(buttonWithOnClass.value == "주간"){
          if ($("#tdw_wlist_box_" + val).height() == "40") {
            $("#btn_list_share_onoff_" + val).removeClass("off");
            $("#btn_list_share_onoff_" + val).addClass("on");

            $("#tdw_wlist_box_" + val).removeClass("off");

            fdata.append("onoff", "0");

            $("#btn_list_share_onoff_" + val).attr("title", "공유 접기");
          } else {
            $(".tdw_list_share_onoff").removeClass("off");
            $("#btn_list_share_onoff_" + val).removeClass("on");

            $("#tdw_wlist_box_" + val).addClass("off");
            fdata.append("onoff", "1");
            $("#btn_list_share_onoff_" + val).attr("title", "공유 펼치기");
          }
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

  //공유내용 열기/접기(주간)
  // $(document).on("click", "button[id^=btn_list_share_onoff]", function () {
  //   var memo_onoff = $(this);
  //   var val = $(this).val();
  //   if (val) {
  //     $("#tdw_list_share_area_in_" + val).toggleClass("off");

  //     $(this).toggleClass("off");
  //     var fdata = new FormData();
  //     fdata.append("mode", "btn_list_share_onoff");
  //     fdata.append("work_idx", val);

  //     if ($("#tdw_wlist_box_" + val).height() == "40") {
  //       $("#btn_list_share_onoff_" + val).removeClass("off");
  //       $("#btn_list_share_onoff_" + val).addClass("on");

  //       $("#tdw_wlist_box_" + val).removeClass("off");

  //       fdata.append("onoff", "0");

  //       $("#btn_list_share_onoff_" + val).attr("title", "공유 접기");
  //     } else {
  //       $(".tdw_list_share_onoff").removeClass("off");
  //       $("#btn_list_share_onoff_" + val).removeClass("on");

  //       $("#tdw_wlist_box_" + val).addClass("off");
  //       fdata.append("onoff", "1");
  //       $("#btn_list_share_onoff_" + val).attr("title", "공유 펼치기");
  //     }

  //     $.ajax({
  //       type: "POST",
  //       data: fdata,
  //       //async: false,
  //       contentType: false,
  //       processData: false,
  //       url: "/inc/works_process.php",
  //       success: function (data) {
  //         console.log(data);
  //         if (data == "complete") {
  //           memo_line_check();
  //         }
  //       },
  //     });
  //   }
  // });


  // -------------------------- 아이템 샵  헤더 ----------------------------
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


  //0921 보고 열기/접기
  $(".tdw_list_report_area").each(function () {
    var rath = $(this);
    var rathle = rath.next(".tdw_list_memo_area").children().length;

    // console.log("rathle :: " + rathle);

    if (rathle > 0) {
      rath.find(".btn_list_report_onoff").addClass("memo_on");
    } else {
      rath.find(".btn_list_report_onoff").removeClass("memo_on");
    }
  });

  //보고업무내용 열기/접기(일일)
  $(document).on("click", "button[id^=btn_list_report_onoff]", function () {
    var memo_onoff = $(this);
    var val = $(this).val();
    var buttonWithOnClass = document.querySelector('.tdw_new_select .on');
    if (val) {
      $("#tdw_list_report_area_in_" + val).toggleClass("off");
      //$("#memo_area_list_" + val).toggleClass("off");
      $(this).toggleClass("off");

      var fdata = new FormData();
      fdata.append("mode", "btn_list_report_onoff");
      fdata.append("work_idx", val);

      if(buttonWithOnClass.value == "일일"){
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
      }else if(buttonWithOnClass.value == "주간"){
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

  //보고업무내용 열기/접기(주간)
  // $(document).on("click", "button[id^=btn_list_report_onoff]", function () {
  //   var memo_onoff = $(this);
  //   var val = $(this).val();
  //   if (val) {
  //     $("#tdw_list_report_area_in_" + val).toggleClass("off");
  //     //$("#memo_area_list_" + val).toggleClass("off");
  //     $(this).toggleClass("off");

  //     var fdata = new FormData();
  //     fdata.append("mode", "btn_list_report_onoff");
  //     fdata.append("work_idx", val);

  //     if (memo_onoff.hasClass("off")) {
  //       memo_onoff.removeClass("on");
  //       $("#btn_list_report_onoff_" + val).removeClass("on");
  //       fdata.append("onoff", "1");
  //       $("#btn_list_report_onoff_" + val).attr("title", "보고 펼치기");
  //     } else {
  //       memo_onoff.addClass("on");
  //       $("#btn_list_report_onoff_" + val).addClass("on");
  //       fdata.append("onoff", "0");

  //       $("#btn_list_report_onoff_" + val).attr("title", "보고 접기");
  //     }

  //     $.ajax({
  //       type: "POST",
  //       data: fdata,
  //       //async: false,
  //       contentType: false,
  //       processData: false,
  //       url: "/inc/works_process.php",
  //       success: function (data) {
  //         console.log(data);
  //         if (data == "complete") {
  //           memo_line_check();
  //         }
  //       },
  //     });
  //   }
  // });

  //요청내용 열기/접기(일일)
  $(document).on("click", "button[id^=btn_list_req_onoff]", function () {
    var memo_onoff = $(this);
    var val = $(this).val();
    var buttonWithOnClass = document.querySelector('.tdw_new_select .on');
    console.log($("#tdw_list_box_" + val).height());
    // $("#tdw_list_box_" + val).toggleClass("off");
    if (val) {
      var fdata = new FormData();
      fdata.append("mode", "btn_list_req_onoff");
      fdata.append("work_idx", val);

      //console.log ( " >>> " + $("#tdw_list_box_"+val).height() );
      if(buttonWithOnClass.value == "일일"){
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
      }else if(buttonWithOnClass.value == "주간"){
        if (memo_onoff.hasClass("off")) {
          $("#btn_list_req_onoff_" + val).removeClass("off");
          $("#btn_list_req_onoff_" + val).addClass("on");
          $("#tdw_wlist_box_" + val).removeClass("off");
  
          fdata.append("onoff", "0");
          $("#btn_list_req_onoff_" + val).attr("title", "보고 펼치기");
        } else {
          $("#btn_list_req_onoff_" + val).removeClass("on");
          $("#btn_list_req_onoff_" + val).addClass("off");
          $("#tdw_wlist_box_" + val).addClass("off");
  
          fdata.append("onoff", "1");
          $("#btn_list_req_onoff_" + val).attr("title", "보고 접기");
        }
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

  //요청업무내용 열기/접기(주간)
  // $(document).on("click", "button[id^=btn_list_req_onoff]", function () {
  //   var memo_onoff = $(this);
  //   var val = $(this).val();
  //   if (val) {
  //     var fdata = new FormData();
  //     fdata.append("mode", "btn_list_req_onoff");
  //     fdata.append("work_idx", val);

  //     if (memo_onoff.hasClass("off")) {
  //       $("#btn_list_req_onoff_" + val).removeClass("off");
  //       $("#btn_list_req_onoff_" + val).addClass("on");
  //       $("#tdw_wlist_box_" + val).removeClass("off");

  //       fdata.append("onoff", "0");
  //       $("#btn_list_req_onoff_" + val).attr("title", "보고 펼치기");
  //     } else {
  //       $("#btn_list_req_onoff_" + val).removeClass("on");
  //       $("#btn_list_req_onoff_" + val).addClass("off");
  //       $("#tdw_wlist_box_" + val).addClass("off");

  //       fdata.append("onoff", "1");
  //       $("#btn_list_req_onoff_" + val).attr("title", "보고 접기");
  //     }

  //     $.ajax({
  //       type: "POST",
  //       data: fdata,
  //       //async: false,
  //       contentType: false,
  //       processData: false,
  //       url: "/inc/works_process.php",
  //       success: function (data) {
  //         console.log(data);
  //         if (data == "complete") {
  //           memo_line_check();
  //         }
  //       },
  //     });
  //   }
  // });

  /*
        if (GetCookie("user_id") == "adsb12@nate.com") {
            //업무내용 열기/접기(일일)
            //보고업무내용 열기/접기(일일)
            $(document).on("click", "button[id^=btn_list_onoff]", function() {

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
                        $("#btn_list_onoff_" + val).removeClass("on");
                        fdata.append("onoff", "1");
                        $("#btn_list_onoff_" + val).attr("title", "보고 펼치기");

                    } else {

                        memo_onoff.addClass("on");
                        $("#btn_list_onoff_" + val).addClass("on");
                        fdata.append("onoff", "0");

                        $("#btn_list_onoff_" + val).attr("title", "보고 접기");
                    }

                    $.ajax({
                        type: "POST",
                        data: fdata,
                        //async: false,
                        contentType: false,
                        processData: false,
                        url: '/inc/works_process.php',
                        success: function(data) {
                            console.log(data);
                            if (data == 'complete') {
                                memo_line_check();
                            }
                        }
                    });
                }
            });
        }
    */

  //보고업무 받을사람변경
  $(document).on("click", "#tdw_report_user", function () {
    //console.log("보고업무 받을사람변경");
    var val = $(this).val();
    var fdata = new FormData();

    //if (GetCookie("user_id") == "sadary0@nate.com") {
    //    fdata.append("mode", "work_user_add");
    //} else {
    fdata.append("mode", "work_report_user_add");
    //}
    fdata.append("idx", val);
    $.ajax({
      type: "POST",
      async: false,
      data: fdata,
      contentType: false,
      processData: false,
      url: "/inc/works_process.php",
      success: function (data) {
        console.log(data);
        if (data) {
          $(".btn_req").trigger("click");
          $(".layer_user_search_desc strong").text("업무 받을 사람 선택");
          $("#layer_todaywork_user").text("설정하기");
          $("#work_type").val("");
          //$("#chall_user_chk").val(result1);
          $(".layer_user").html(data);
          tdata = data.split("|");
          if (tdata[1]) {
            //arr_val.push();
          }

          //console.log("arr_val" + arr_val);
        }
      },
    });
  });

  //업무 받는사람 변경
  $(document).on("click", "button[id^=tdw_send_user]", function () {
    var id = $(this).attr("id");
    if (id) {
      var idx = id.replace("tdw_send_user_", "");
      var fdata = new FormData();

      //if (GetCookie("user_id") == "sadary0@nate.com" || GetCookie("user_id") == "eyson@bizforms.co.kr") {
      fdata.append("mode", "work_user_add");
      //} else {
      //fdata.append("mode", "work_report_user_add");
      //}
      $("#work_idx").val(idx);
      fdata.append("idx", idx);

      console.log(" idx : " + idx);
      console.log(" work_idx : " + $("#work_idx").val());

      $.ajax({
        type: "POST",
        async: false,
        data: fdata,
        contentType: false,
        processData: false,
        url: "/inc/works_process.php",
        success: function (data) {
          console.log(data);
          if (data) {
            tdata = data.split("|");
            if (tdata) {
              $(".btn_req").trigger("click");
              $("#layer_todaywork_user").text("설정하기");
              $("#work_type").val("");
              $(".layer_user").html(tdata[0]);

              if (tdata[1]) {
                //arr_val.push();
                $("#chall_user_chk").val(tdata[1]);
              }
            }
          }
        },
      });
    } else {
      alert("일일업무를 체크해 주세요.");
      return false;
    }
  });

  //공유업무 받을사람변경
  $(document).on("click", "#tdw_share_user", function () {
    console.log("공유업무 받을사람변경");
    var val = $(this).val();
    var fdata = new FormData();
    fdata.append("mode", "work_share_user_add");
    fdata.append("idx", val);
    $.ajax({
      type: "POST",
      async: false,
      data: fdata,
      contentType: false,
      processData: false,
      url: "/inc/works_process.php",
      success: function (data) {
        console.log(data);
        if (data) {
          $(".btn_req").trigger("click");
          $(".layer_user_search_desc strong").text("업무 받을 사람 선택");
          $("#layer_todaywork_user").text("설정하기");
          $("#work_type").val("");
          $(".layer_user").html(data);
          tdata = data.split("|");
          if (tdata[1]) {
            //arr_val.push();
          }
          //console.log("arr_val" + arr_val);
        }
      },
    });
  });

  
    // 시간 수정 상태값 토글 키
    
    $(document).on('click', '.layer_btn ul li', function() {
      // 현재 클릭된 버튼에 'on' 클래스 추가
      $(this).toggleClass('on');
  
      // 다른 버튼들에게 'on' 클래스 제거
      $(this).siblings().removeClass('on');

      // 'on' 클래스가 적용된 버튼이 하나 이상인지 확인
      if ($('.layer_btn ul li').hasClass('on')) {
          $('.layer_time_set_in').removeClass('off');
          $('.layer_time_set_in .time_set .tdw_tab_sort_in').css('pointer-events', 'visible');
      } else {
          $('.layer_time_set_in').addClass('off');
          $('.layer_time_set_in .time_set .tdw_tab_sort_in').css('pointer-events', 'none');
      }
    });

    //시간 변경(업무 작성 시)
    $(document).on('mouseenter', '.layer_time_set_in .time_set .tdw_tab_sort_in', function(){
	  	$(this).find('ul').css("display", "block");
	  });
    $(document).on('mouseleave', '.layer_time_set_in .time_set .tdw_tab_sort_in', function(){
	  	$(this).find('ul').css("display", "");
	  });
  

    
 
  
  // 시간 변경 클릭
  $(document).on("click", ".tdw_list_time", function () {

    var fdata = new FormData();
    if ($(this).val()) {
      $("#work_idx").val($(this).val());
    }
    var checkValue = $(this).val();
    fdata.append('change_idx', checkValue);
    fdata.append('mode', 'change_time');
    $.ajax({
      type: "POST",
      data: fdata,
      contentType: false,
      processData: false,
      url: "/inc/works_process.php",
      success: function (data) {
        $(".layer_time_in").html(data);
        $(".layer_time").show();
        var changeHourFirst = $(".layer_time_set_in .tdw_time_start .time_set .first_set").val();
        var changeMinFirst = $(".layer_time_set_in .tdw_time_start .time_set .second_set").val();
        var changeHourSecond = $(".layer_time_set_in .tdw_time_end .time_set .first_set").val();
        var changeMinSecond = $(".layer_time_set_in .tdw_time_end .time_set .second_set").val();

        $("#sChangeHour").val(changeHourFirst);
        $("#sChangeMin").val(changeMinFirst);
        $("#eChangeHour").val(changeHourSecond);
        $("#eChangeMin").val(changeMinSecond);
        $("#changeIdx").val(checkValue);

      },
    });
  });

  $(document).on("click", ".s_changeTimeHour", function(){
		var val = $(this).attr("value");
    if (val) {
      $("#sChangeHour").val(val);
     }
		$('.layer_time_set_in .tdw_time_start .tdw_time_hour .btn_sort_on span').text(val);
		$('.layer_time_set_in .time_set .tdw_tab_sort_in ul').css("display", "none");
	});
	$(document).on("click", ".s_changeTimeMin", function(){
		var val = $(this).attr("value");
    if (val) {
      $("#sChangeMin").val(val);
     }
		$('.layer_time_set_in .tdw_time_start .tdw_time_min .btn_sort_on span').text(val);
		$('.layer_time_set_in .time_set .tdw_tab_sort_in ul').css("display", "none");
	});
	$(document).on("click", ".e_changeTimeHour", function(){
		var val = $(this).attr("value");
    if (val) {
      $("#eChangeHour").val(val);
     }
		$('.layer_time_set_in .tdw_time_end .tdw_time_hour .btn_sort_on span').text(val);
		$('.layer_time_set_in .time_set .tdw_tab_sort_in ul').css("display", "none");
	});
	$(document).on("click", ".e_changeTimeMin", function(){
		var val = $(this).attr("value");
    if (val) {
      $("#eChangeMin").val(val);
     }
		$('.layer_time_set_in .tdw_time_end .tdw_time_min .btn_sort_on span').text(val);
		$('.layer_time_set_in .time_set .tdw_tab_sort_in ul').css("display", "none");
	});

  // 시간 변경 등록하기
  $(document).on("click", ".layer_time_submit", function () {
    var li_state = $('.layer_btn ul li');
    var decideChangeIdx = $('#changeIdx').val();

    if(li_state.hasClass('on')){
      var filter = li_state.filter('.on');
      var decide = filter.find('span').attr("value");
      var changeHour_f =  $("#sChangeHour").val();
      var changeMin_f =   $("#sChangeMin").val();
      var changeHour_e =   $("#eChangeHour").val();
      var changeMin_e =   $("#eChangeMin").val();

      if ($("#sChangeHour").val() && $("#sChangeMin").val() && $("#eChangeHour").val() && $("#eChangeMin").val()){
        var startTime = changeHour_f+":"+changeMin_f; 
        var endTime = changeHour_e+":"+changeMin_e;

        //시간으로 변경하여 비교하기
        var realStart = timeToMinutes(startTime);
        var realend = timeToMinutes(endTime);

        if(realStart > realend){
          alert("시작 시간은 종료 시간보다 작아야합니다.");
          return false;
        }else if(realStart == realend){
          alert("시작시간은 종료 시간보다 작아야합니다.");
          return false;
        }
      }
    }else{
      var decide = 0;
      var startTime = null; 
      var endTime = null;
    }
    
    

    var fdata = new FormData();
    fdata.append("start_time", startTime);
    fdata.append("end_time", endTime);
    fdata.append('change_idx', decideChangeIdx);
    fdata.append('decide_flag', decide);
    fdata.append('mode', 'change_work_decide');
    
    $.ajax({
      type: "POST",
      data: fdata,
      contentType: false,
      processData: false,
      url: "/inc/works_process.php",
      success: function (data) {
        if(data == 'complete'){
          alert("업무 및 시간이 변경되었습니다.");
          $(".layer_time").hide();
          works_list();
          return false;
        }else if(data == 'fail'){
          alert("변경하려는 업무와 시간이 기존과 동일합니다. \n다시 한번 확인해주시길 바랍니다.");
          return false;
        }else{
          alert("업무 및 시간이 변경되지 않았습니다.")
          return false;
        }
      },
    });
  });

  // 시간 변경 팝업 닫기
  $(document).on("click", ".layer_time_cancel", function () {
    $(".layer_time").hide();
  });
  
  //메모클릭
  $(document).on("click", ".tdw_list_memo", function () {
    if ($(this).val()) {
      $("#work_idx").val($(this).val());
    }
    $(".layer_memo").show();
    //$("#textarea_memo").focus();
    setTimeout(function () {
      $("#textarea_memo").focus();
    }, 100);
  });

  
  // 비밀업무 메모 클릭
  $(document).on("click", ".tdw_list_memo_secret", function () {
    if ($(this).val()) {
      $("#work_idx").val($(this).val());
    }
    $(".layer_memo_secret").show();
    //$("#textarea_memo").focus();
    setTimeout(function () {
      $("#textarea_memo_secret").focus();
    }, 100);
  });

  //메모취소
  $(document).on("click", ".layer_memo_cancel", function () {
    //$(".layer_memo_cancel").click(function() {
    if ($("#textarea_memo").val()) {
      $("#textarea_memo").val("");
    }
    if ($("#textarea_memo_secret").val()) {
      $("#textarea_memo_secret").val("");
    }

    if ($("#layer_memo_submit").hasClass("on") == true) {
      $("#layer_memo_submit").removeClass("on");
    }else if($("#layer_memo_secret_submit").hasClass("on") == true){
      $("#layer_memo_secret_submit").removeClass("on");
    }
    $(".layer_memo").hide();
    $(".layer_memo_secret").hide();
  });

  //메모삭제
  $(document).on("click", ".tdw_list #btn_memo_del", function () {
    if (confirm("작성한 업무 메모를 삭제하시겠습니까?")) {
      var val = $(this).val();
      var fdata = new FormData();
      fdata.append("mode", "work_comment_del");
      fdata.append("idx", val);
      $.ajax({
        type: "POST",
        data: fdata,
        contentType: false,
        processData: false,
        url: "/inc/works_process.php",
        beforeSend: function () {
          $('.rewardy_loading_01').css('display', 'block');
        },
        complete: function (){
          $('.rewardy_loading_01').css('display', 'none');
        },
        success: function (data) {
          console.log(data);
          if (data) {
            tdata = data.split("|");
            result = tdata[0];
            result1 = tdata[1];
            if (result == "complete") {
              $("#comment_list_" + result1).remove();
              memo_line_check();
            }

            //$(this).closest(".tdw_list_memo_desc").remove();
          }
        },
      });
    }
  });

  //일일업무 댓글 수정하기, 확인버튼
  $(document).on("click", "#btn_comment_submit", function () {
    var val = $(this).val();
    var fdata = new FormData();
    var contents = $("#tdw_comment_edit_" + val).val();

    fdata.append("mode", "work_comment_edit");
    fdata.append("idx", val);
    fdata.append("contents", contents);

    $.ajax({
      type: "POST",
      data: fdata,
      contentType: false,
      processData: false,
      url: "/inc/works_process.php",
      success: function (data) {
        console.log(data);
        if (data == "complete") {
          $("#tdw_comment_edit_" + val).val("");
          feeling_banner_reload($("#work_wdate").val());

          works_list();
          return false;
        }
      },
    });
  });

  //일일업무 댓글 수정하기, 확인버튼
  $(document).on("click", "#btn_report_submit", function () {
    var val = $(this).val();
    var fdata = new FormData();
    var contents = $("#tdw_report_edit_" + val).val();

    if(contents==""){
      alert('내용을 입력해주세요!');
      return false;
    }

    fdata.append("mode", "work_report_edit");
    fdata.append("idx", val);
    fdata.append("contents", contents);

    $.ajax({
      type: "POST",
      data: fdata,
      contentType: false,
      processData: false,
      url: "/inc/works_process.php",
      success: function (data) {
        tdata = data.split("|");
        result = tdata[0];
        result1 = tdata[1];

        console.log(data);
        if (result == "complete") {
          $("#tdw_comment_edit_" + val).val("");
          feeling_banner_reload($("#work_wdate").val());

          works_list();
          return false;
        } else if (result == "none_report"){
          $("#tdw_report_edit_" + val).val(result1);
        }
      },
    });
  });

  //주간업무 댓글 수정하기, 확인버튼
  $(document).on("click", "#btn_wcomment_submit", function () {
    var val = $(this).val();
    var fdata = new FormData();
    var contents = $("#tdw_wcomment_edit_" + val).val();

    fdata.append("mode", "work_comment_edit");
    fdata.append("idx", val);
    fdata.append("contents", contents);

    $.ajax({
      type: "POST",
      data: fdata,
      contentType: false,
      processData: false,
      url: "/inc/works_process.php",
      success: function (data) {
        console.log(data);
        if (data == "complete") {
          $("#tdw_comment_edit_" + val).val("");
          feeling_banner_reload($("#work_wdate").val());

          works_list();
          return false;
        }
      },
    });
  });

  //메모 등록하기
  $(".layer_memo_submit").click(function(){
		$(".layer_memo").hide();
		// $(".tdw_list_memo").addClass("on");
	});
  $(".layer_memo_secret_submit").click(function(){
		$(".layer_memo_secret").hide();
		// $(".tdw_list_memo").addClass("on");
	});
  //메모 글내용작성
  $("#textarea_memo").bind("input", function (event) {
    var val = $(this).val();
    if (val) {
      if ($("#layer_memo_submit").hasClass("on") == false) {
        $("#layer_memo_submit").addClass("on");
        $("#textarea_memo").val(val);
      }
    } else {
      $("#layer_memo_submit").removeClass("on");
    }
  });

  $("#textarea_memo_secret").bind("input", function (event) {
    var val = $(this).val();
    if (val) {
      if ($("#layer_memo_secret_submit").hasClass("on") == false) {
        $("#layer_memo_secret_submit").addClass("on");
        $("#textarea_memo_secret").val(val);
      }
    } else {
      $("#layer_memo_secret_submit").removeClass("on");
    }
  });

  //메모등록버튼
  $(document).on("click", ".layer_memo_submit", function () {
    var secret_flag = 0;
    if ($("#textarea_memo").val() == "") {
      alert("메모를 작성해주세요.");
      //$("#textarea_memo").focus();
      /*setTimeout(function() {
              $('#textarea_memo').focus();
            }, 1000);
            */
      
      if ($(".layer_memo").is(":visible") == false) {
        $(".layer_memo").show();
        $("#textarea_memo").focus();
      }
      return false;
    }

    if ($("#textarea_memo").val()) {
      var fdata = new FormData();
      fdata.append("mode", "work_comment");
      fdata.append("work_idx", $("#work_idx").val());
      fdata.append("comment", $("#textarea_memo").val());
      fdata.append("secret_flag", secret_flag);
      var url = "/inc/works_process.php";

      $.ajax({
        type: "POST",
        data: fdata,
        contentType: false,
        processData: false,
        url: url,
        beforeSend: function () {
          $('.rewardy_loading_01').css('display', 'block');
        },
        complete: function (){
          $('.rewardy_loading_01').css('display', 'none');
        },
        success: function (data) {
          console.log(data);
          if (data == "complete") {
            $("#textarea_memo").val("");
            if ($("#work_wdate").val()) {
              feeling_banner_reload($("#work_wdate").val());
            }
            works_list();
            return false;
          } else if (data == "logout") {
            //  $(".t_layer").show();
            //  return false;
          }
        },
      });
    }
  });

  //비밀 메모 등록
  $(document).on("click", ".layer_memo_secret_submit", function () {
    var secret_flag = '1';
    if ($("#textarea_memo_secret").val() == "") {
      alert("메모를 작성해주세요.");
      //$("#textarea_memo").focus();
      /*setTimeout(function() {
              $('#textarea_memo').focus();
            }, 1000);
            */
      
      if ($(".layer_memo_secret").is(":visible") == false) {
        $(".layer_memo_secret").show();
        $("#textarea_memo_secret").focus();
      }
      return false;
    }

    if ($("#textarea_memo_secret").val()) {
      var fdata = new FormData();
      fdata.append("mode", "work_comment");
      fdata.append("work_idx", $("#work_idx").val());
      fdata.append("comment", $("#textarea_memo_secret").val());
      fdata.append("secret_flag", secret_flag);
      var url = "/inc/works_process.php";

      $.ajax({
        type: "POST",
        data: fdata,
        contentType: false,
        processData: false,
        url: url,
        beforeSend: function () {
          $('.rewardy_loading_01').css('display', 'block');
        },
        complete: function (){
          $('.rewardy_loading_01').css('display', 'none');
        },
        success: function (data) {
          console.log(data);
          if (data == "complete") {
            $("#textarea_memo_secret").val("");
            if ($("#work_wdate").val()) {
              feeling_banner_reload($("#work_wdate").val());
            }
            works_list();
            return false;
          } else if (data == "logout") {
            //  $(".t_layer").show();
            //  return false;
          }
        },
      });
    }
  });

  //일일업무 작성한 댓글 내용 클릭
  $(document).on("click", "span[id^=tdw_list_memo_conts_txt]", function () {
    console.log("수정");
    var obj_edit = $("span[id^=tdw_list_memo_conts_txt]");
    var obj_edit_cnt = obj_edit.size();

    var id = $(this).attr("id");
    var no = id.replace("tdw_list_memo_conts_txt_", "");

    if (no) {
      var elem = $("textarea[id=tdw_comment_edit_" + no + "]");
      setTimeout(function () {
        var input = elem;
        var v = input.val();
        input.focus().val("").val(v);
      }, 50);
      $(this).next().next(".tdw_list_memo_regi").show();

      //var memo_width = $(".tdw_list_memo_conts_txt").width();
      //var memo_width = $(this).parent(".tdw_list_memo_conts_txt").width();
      //$(this).next(".tdw_list_memo_regi").css({ "width": memo_width + 199 });
      //console.log("memo_width ::: " + memo_width);

      //해당 글내용 이외에 인풋박스 닫기
      for (i = 0; i < obj_edit_cnt; i++) {
        obj_edit_id = obj_edit.eq(i).attr("id");
        obj_edit_no = obj_edit
          .eq(i)
          .attr("id")
          .replace("tdw_list_memo_conts_txt_", "");
        if (no != obj_edit_no) {
          $("#tdw_list_memo_regi_" + obj_edit_no).hide();
        }
      }
    }
  });

  //일일업무 작성한 댓글 내용 클릭
  $(document).on("click", "div[id^=tdw_list_memo_conts_txt]", function () {
    console.log("수정하기");

    var obj_edit = $("div[id^=tdw_list_memo_conts_txt]");
    var obj_edit_cnt = obj_edit.size();

    var id = $(this).attr("id");
    var no = id.replace("tdw_list_memo_conts_txt_", "");

    console.log(id);
    console.log(no);

    if (no) {
      var elem = $("textarea[id=tdw_comment_edit_" + no + "]");
      setTimeout(function () {
        var input = elem;
        var v = input.val();
        input.focus().val("").val(v);
      }, 50);

      // $(this).next(".tdw_list_memo_regi").show();
      $(this).find(".tdw_list_memo_regi").vv = $(this).find(
        ".tdw_list_memo_regi"
      );
      console.log(vv);

      //var memo_width = $(".tdw_list_memo_conts_txt").width();
      var memo_width = $(this).parent(".tdw_list_memo_conts_txt").width();
      $(this)
        .next(".tdw_list_memo_regi")
        .css({ width: memo_width + 199 });

      //해당 글내용 이외에 인풋박스 닫기
      for (i = 0; i < obj_edit_cnt; i++) {
        obj_edit_id = obj_edit.eq(i).attr("id");
        obj_edit_no = obj_edit
          .eq(i)
          .attr("id")
          .replace("tdw_list_memo_conts_txt_", "");
        if (no != obj_edit_no) {
          $("#tdw_list_memo_regi_" + obj_edit_no).hide();
        }
      }
    }
  });

  //일일업무 작성한 보고내용
  $(document).on("click", "span[id^=tdw_list_report_conts_txt]", function () {
    var obj_edit = $("span[id^=tdw_list_report_conts_txt]");
    var obj_edit_cnt = obj_edit.size();

    var id = $(this).attr("id");
    var no = id.replace("tdw_list_report_conts_txt_", "");

    if (no) {
      var elem = $("textarea[id=tdw_report_edit_" + no + "]");
      setTimeout(function () {
        var input = elem;
        var v = input.val();
        input.focus().val("").val(v);
      }, 50);

      var memo_width = $(this).width();
      if (memo_width < 200) {
        memo_width = memo_width + 320;
      }

      $(this)
        .next()
        .css({ width: memo_width + 50 });
      $(this).next().next(".tdw_list_report_regi").show();

      //해당 글내용 이외에 인풋박스 닫기
      for (i = 0; i < obj_edit_cnt; i++) {
        obj_edit_id = obj_edit.eq(i).attr("id");
        obj_edit_no = obj_edit
          .eq(i)
          .attr("id")
          .replace("tdw_list_report_conts_txt_", "");
        if (no != obj_edit_no) {
          $("#tdw_list_report_regi_" + obj_edit_no).hide();
        }
      }
    }
  });

  //주간업무 작성한 댓글 내용 클릭
  $(document).on("click", "span[id^=tdw_wlist_memo_conts_txt]", function () {
    var obj_edit = $("span[id^=tdw_wlist_memo_conts_txt]");
    var obj_edit_cnt = obj_edit.size();

    var id = $(this).attr("id");
    var no = id.replace("tdw_wlist_memo_conts_txt_", "");

    console.log(id);
    console.log(no);
    if (no) {
      var elem = $("textarea[id=tdw_wcomment_edit_" + no + "]");
      setTimeout(function () {
        var input = elem;
        var v = input.val();
        input.focus().val("").val(v);
      }, 50);
      //$(this).next().next("#tdw_wlist_memo_regi").show();
      $(this)
        .next()
        .next("div[id=tdw_wlist_memo_regi_" + no + "]")
        .show();

      //var memo_width = $("#tdw_wlist_memo_conts_txt").width();
      var memo_width = $("span[id=tdw_wlist_memo_conts_txt_" + no).width();
      $(this)
        .next("#tdw_wlist_memo_regi")
        .css({ width: memo_width + 199 });

      //해당 글내용 이외에 인풋박스 닫기
      for (i = 0; i < obj_edit_cnt; i++) {
        obj_edit_id = obj_edit.eq(i).attr("id");
        obj_edit_no = obj_edit
          .eq(i)
          .attr("id")
          .replace("tdw_wlist_memo_conts_txt_", "");
        if (no != obj_edit_no) {
          $("#tdw_wlist_memo_regi_" + obj_edit_no).hide();
        }
      }
    }
  });

  //작성한 댓글 내용 취소
  $(document).on("click", ".tdw_list .btn_regi_cancel", function () {
    $(this).closest(".tdw_list_memo_regi").hide();
  });

  //작성한 보고업무 내용 취소
  $(document).on("click", "#btn_report_cancel", function () {
    $(this).closest(".tdw_list_report_regi").hide();
  });

  //업무요청 받을사람 선택 - 검색
  //$(".layer_user_search_box input").bind("input keyup", function(e) {
  

  //업무요청 받을사람 선택 - 검색버튼
  $(document).on("click", "#input_todaywork_search_btn", function () {
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


  //230803

  $(document).on("click", ".tdw_list_o", function () {
    var index = $(".tdw_list_o").index(this);
    var menus = $(".tdw_list_1depth");

    // 모든 메뉴를 숨기기
    menus.css("display", "none");

    // 클릭한 버튼에 해당하는 메뉴 열기/닫기
    var targetMenu = menus.eq(index);
    if (targetMenu.css("display") === "none" || targetMenu.css("display") === "") {
        targetMenu.css("display", "block");
    } else {
        targetMenu.css("display", "none");
    }
});

  // $(document).on("click", ".tdw_list_o", function () {
  //   $(this).next(".tdw_list_1depth").show();
  // });
  // $(document).on("click", ".tdw_list_r", function () {
  //   $(this).next(".tdw_list_2depth").show();
  // });
  // 외부영역 클릭 시 팝업 닫기
  // $(document).mouseup(function (e){
  //   var closeMenu = $(".tdw_list_more button");
  //     if(closeMenu.has(e.target).length === 0){
  //       $(".tdw_list_1depth").hide();
  //     }
  // });

  $(document).on("click", ".tdw_list_cancel", function () {
    $(".tdw_list_1depth").hide();
  });
  // $(document).on("click", ".tdw_list_more button", function () {
  //   $(this).not(".tdw_list_r").toggleClass("on");
  // });
  // $(document).on("mouseleave", ".tdw_list_1depth", function () {
  //   $(".tdw_list_1depth").hide();
  //   $(".tdw_list_2depth").hide();
  // });

  $(".tdw_list_1depth > ul > li > button").not(".tdw_list_r").mouseenter(function(){
    $(".tdw_list_2depth").hide();
  });

  var ff_class = "";
  var ff_text = "";

  $(document).on("click", "div[id^=tdw_feeling_banner]", function () {
    var id = $(this).attr("id");
    //$("#" + id).trigger("click");
    //$("#" + id).trigger("click");

    if (id) {
      var chid = id.replace("tdw_feeling_banner_", "");
      console.log("#" + chid);
      //$("#btn_feeling_banner_" + chid).click();
    }
  });

  //한줄소감 클릭
  $(document).on("click", "button[id^=btn_feeling_banner]", function () {
    var val = $(this).val();
    if (val) {
      var fdata = new FormData();
      fdata.append("mode", "todaywork_review_info");
      fdata.append("workdate", val);

      var obj = $(".ff_area ul li");
      var obj_len = obj.length;

      $.ajax({
        type: "post",
        data: fdata,
        contentType: false,
        processData: false,
        url: "/inc/works_process.php",
        success: function (data) {
          //console.log(data);
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
                $("#input_fl").val(comment);
              }
            }
          }
        },
      });
    } else {
      $(".feeling_first").show();
    }
  });

  //아이콘 클릭
  $(".ff_area button").click(function () {
    ff_class = $(this).attr("class");
    var val = $(this).val();
    if (!val) {
      val = $("#icon_idx").val();
    }
    if (val) {
      $("#icon_idx").val(val);
      if ($(this).attr("class") == "btn_ff_01") {
        ff_text = "최고의";
      }
      if ($(this).attr("class") == "btn_ff_02") {
        ff_text = "뿌듯한";
      }
      if ($(this).attr("class") == "btn_ff_03") {
        ff_text = "기분 좋은";
      }
      if ($(this).attr("class") == "btn_ff_04") {
        ff_text = "감사한";
      }
      if ($(this).attr("class") == "btn_ff_05") {
        ff_text = "재밌는";
      }
      if ($(this).attr("class") == "btn_ff_06") {
        ff_text = "수고한";
      }
      if ($(this).attr("class") == "btn_ff_07") {
        ff_text = "무난한";
      }
      if ($(this).attr("class") == "btn_ff_08") {
        ff_text = "지친";
      }
      if ($(this).attr("class") == "btn_ff_09") {
        ff_text = "속상한";
      }
      $(".ff_area button").not(this).removeClass("on");
      $(this).addClass("on");
      $(".ff_bottom button").removeClass("btn_off").addClass("btn_on");
      //console.log(ff_text);
    }
  });

  //한줄소감 닫기버튼
  $(".ff_close button").click(function () {
    $(".feeling_first").hide();
    $(".ff_area button").removeClass("on");
    $(".ff_bottom button").removeClass("btn_on").addClass("btn_off");
  });

  //한줄소감 다음버튼
  $("#ff_bottom_next").click(function () {
    //console.log("다음버튼 ");
    if ($(this).hasClass("btn_on")) {
      var val = $("#icon_idx").val();
      ff_text = ff_text_arr(val);

      $(".feeling_first").hide();
      $(".ff_area button").removeClass("on");
      $(".ff_bottom button").removeClass("btn_on").addClass("btn_off");

      $(".feeling_layer").show();
      $(".fl_area .fl_desc").removeClass().addClass("fl_desc");

      if (val) {
        $(".fl_area .fl_desc").addClass("btn_ff_0" + val);
      }
      $(".fl_area .fl_desc span").text(ff_text);

      var elem = $("#input_fl");
      setTimeout(function () {
        var input = elem;
        var v = input.val();
        input.focus().val("").val(v);
      }, 50);
    } else {
    }
  });

  //한줄소감 등록하기
  $(document).on("click", "#fl_bottom", function () {
    var val = $("#icon_idx").val();
    //$(".fl_area .fl_desc").attr("class");
    if (val) {
      $(".feeling_layer").hide();

      var fdata = new FormData();
      var input_val = $("#input_fl").val();
      var icon_idx = $("#icon_idx").val();
      
      fdata.append("mode", "todaywork_review_write");
      fdata.append("input_val", input_val);
      fdata.append("workdate", $("#review_idx").val());
      fdata.append("icon_idx", icon_idx);
      $.ajax({
        type: "post",
        data: fdata,
        contentType: false,
        processData: false,
        url: "/inc/works_process.php",
        success: function (data) {
          //console.log(data);
          if (data) {
            tdata = data.split("|");
            if (tdata) {
              var result = tdata[0];
              var review_idx = tdata[1];
              var icon_idx = tdata[2];
              var comment = tdata[3];

              console.log("review_idx " + review_idx);

              if (result == "complete") {
                var ff_class = "btn_ff_0" + icon_idx;
                $("#icon_idx").val(icon_idx);
                $("#input_fl").val(input_val);

                $(".tdw_list_dd #feeling_banner_" + review_idx).text(comment);
                $(".tdw_list_dd #tdw_feeling_banner_" + review_idx)
                  .removeClass()
                  .addClass("tdw_feeling_banner");
                $(".tdw_list_dd #tdw_feeling_banner_" + review_idx).addClass(
                  ff_class
                );

                $(".tdw_list_ww #feeling_banner_" + review_idx).text(comment);
                $(".tdw_list_ww #tdw_feeling_banner_" + review_idx)
                  .removeClass()
                  .addClass("tdw_feeling_banner");
                $(".tdw_list_ww #tdw_feeling_banner_" + review_idx).addClass(
                  ff_class
                );
              }
            }
          }
        },
      });
    }
  });

  //한줄소감 닫기
  $(".fl_close button").click(function () {
    $(".feeling_layer").hide();
  });

  //지각 페널티 카드 발동
  $(document).on("click", "button[id=btn_penalty_banner_01]", function () {
    $("#penalty_layer_01").show();
    //페널티 카드 시간 실행
    startTimer();
  });

  //오늘업무 페널티 카드 발동
  $(document).on("click", "button[id=btn_penalty_banner_02]", function () {
    $("#penalty_layer_02").show();
    //페널티 카드 시간 실행
    startTimer();
  });

  //퇴근 페널티 카드 발동
  $(document).on("click", "button[id=btn_penalty_banner_03]", function () {
    $("#penalty_layer_03").show();
    //페널티 카드 시간 실행
    startTimer();
  });

  //지각 페널티 카드 닫기
  $("#pl_close_01").click(function () {
    $("#penalty_layer_01").hide();
  });

  //오늘업무 페널티 카드 닫기
  $("#pl_close_02").click(function () {
    $("#penalty_layer_02").hide();
  });

  //퇴근 페널티 카드 닫기
  $("#pl_close_03").click(function () {
    $("#penalty_layer_03").hide();
  });

  //이미지올리기
  $(
    "input[id='pl_file_01'],input[id='pl_file_02'],input[id='pl_file_03']"
  ).change(penalty_img_preview);

  //페널티 수료증 다운로드
  $("#file_box_01,#file_box_02,#file_box_03").click(function () {
    var url = "/inc/file_download.php";
    var num = "0";
    var idx = $(this).attr("value");
    var params = { idx: idx, num: num };

    console.log(params);
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
  });

  /*
      파일첨부 기능 추가
    */

  //파일첨부가 있을경우
  if ($("input[id='files']").length > 0) {
    //최대파일갯수
    var max_file_cnt = 3;
    //var fileListArr = Array.from($("input[id='files']")[0].files);
    var fileListArr = new Array();
  }

  //첨부파일갯수제한
  $("#tdw_write_label_file").click(function () {
    var file_box_cnt = $(".tdw_write_area_in #tdw_write_file_desc").length;
    if (file_box_cnt >= 3) {
      alert("첨부파일은 3개까지 가능합니다.");
      return false;
    }
  });

  //첨부파일 내용 변경시
  //var fileListArr = new Array();
  $("input[id='files']").change(function () {
    var file_box_cnt = $(".tdw_write_area_in #tdw_write_file_desc").length;
    var file_obj = $(this)[0].files; //파일정보
    
    var file_cnt = file_obj.length; //선택된 첨부파일 개수
    var limit_file_cnt = max_file_cnt - file_box_cnt; // 추가로 첨부가능한 개수

    if (file_cnt > limit_file_cnt) {
      alert("첨부파일은 3개까지 가능합니다.");
    }

    //console.log(" 첨부파일 -> " + Math.min(file_cnt, limit_file_cnt));
    //console.log("####");
    for (var i = 0; i < Math.min(file_cnt, limit_file_cnt); i++) {
      file_name = file_obj[i].name;
      file_size = file_obj[i].size;
      var ext = file_name.split(".").pop().toLowerCase();
      var maxSize = 100 * 1024 * 1024;
      var fileSize = file_size;
      //용량제한
      if (fileSize > maxSize) {
        alert("첨부파일 사이즈는 100MB 이내로 등록 가능합니다.");
        return false;
      }

      //if (!(new RegExp(format_ext, 'i')).test(file_name)) {
      if ($.inArray(ext, format_ext) > 0) {
        alert("첨부할 수 없는 파일입니다.\n파일명 : " + file_name + "");
        return false;
      } else {
        fileListArr.push(file_obj[i]);
        $(".tdw_write_area_in #tdw_write_text").after(
          '<div class="tdw_write_file_desc" id="tdw_write_file_desc"><span>' +
            file_name +
            '</span><button id="work_file_del">삭제</button></div>'
        );
      }
    }

    if (fileListArr.length > 0) {
      fileListArr.reverse();
    }
  });

  //첨부파일삭제
  $(document).on("click", "button[id^=work_file_del]", function () {
    var file_cnt = $(".tdw_write_area_in #tdw_write_file_desc").length;
    var file_obj = $("input[id='files']");
    // var file_form = Array.from(file_obj[0].files);
    //var fileListArr = Array.from(file_form);

    var index = $("#tdw_write_file_desc button").index(this);
    if (fileListArr.length > 0) {
      for (i = 0; i < fileListArr.length; i++) {
        if (index == i) {
          //var f = fileListArr[i];
          //console.log(f);
          fileListArr.splice(index, 1);
          $(this).parent().remove();
        }
      }
    }

    //모두삭제후 빈값으로 처리
    if (fileListArr.length == 0) {
      $("#files").val("");
    }

    if (fileListArr.length > 0) {
      //fileListArr.reverse();
      //var v = $(this).parent().html();
      ///fileListArr.splice(index, 1);
      ///$(this).parent().remove();
      //v = $("input[id='files']")[0].files[index];
      //console.log(v);
    }

    //$("input[id='files']")[0].files
    for (i = 0; i < fileListArr.length; i++) {
      //fileListArr.push(fileArr[i]);
      //    console.log(" files ::" + fileListArr[0].files);
    }

    //console.log("삭제후 : " + fileListArr.length);
    //document.querySelector('#file-input').files = dataTranster.files;
  });

  //보고업무 파일삭제
  $(document).on("click", "button[id=btn_list_fdel]", function () {
    var val = $(this).val();
    if (val) {
      //var work_idx = $(this).parent().parent().parent().find("#btn_report_submit").attr("value");

      //if ($(".select_dd").hasClass("on") == true) {
      var work_idx = $(this)
        .parent()
        .parent()
        .parent()
        .parent()
        .parent()
        .find(".tdw_list_drag")
        .attr("value");
      //} else {
      var work_idx = $(this)
        .parent()
        .parent()
        .parent()
        .parent()
        .parent()
        .find(".tdw_list_drag")
        .attr("value");
      //}

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
              works_list();
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

  //보고업무 파일추가
  $(document).on("click", "button[id^=tdw_file_add]", function () {
    var id = $(this).attr("id");
    var no = id.replace("tdw_file_add_", "");
    var fdata = new FormData();

    fdata.append("work_idx", no);
    fdata.append("mode", "works_files_check");

    $.ajax({
      type: "POST",
      data: fdata,
      contentType: false,
      processData: false,
      url: "/inc/works_process.php",
      success: function (data) {
        console.log(data);
        if (data) {
          var tdata = data.split("|");
          if (tdata) {
            result = tdata[0];
            cnt = tdata[1];
            if (result == "complete") {
              if (cnt >= 3) {
                alert("첨부파일은 3개까지 가능합니다.");
                return false;
              } else {
                //$("#" + id).after('<input type="file" id="files_add" class="tdw_write_input_file">');
                console.log(no);
                $("#files_add_" + no).click();
              }
            }
          }
        }
      },
    });
  });

  //$("input[id='files_add']").change(penalty_img_preview);
  //$("input[id='files_add']").change(function() {
  //$("#files_add").on("propertychange change keyup paste input", function() {
  //$("#files_add").on("propertychange change keyup paste input", function() {

  //$("input[id='files_add']").change(fileadd);
  //files_add_45473
  $(document).on("change", "input[id^=files_add]", fileadd);

  /*
        $("#files_add").on("change", function() {
            var file_obj = $(this)[0].files; //파일정보
            var file_cnt = file_obj.length; //선택된 첨부파일 개수
            //var limit_file_cnt = max_file_cnt - file_box_cnt; // 추가로 첨부가능한 개수
            var id = $(this).parent().parent().find(".tdw_list_share").attr("id");
            var no = id.replace("tdw_file_add_", "");

            //var id = $(this).attr("id");
            ///var no = id.replace("_files_add", "");

            console.log("file_obj == " + $(this)[0].files);



            if (file_obj) {
                //var file_cnt = $("button[id^=btn_list_file").length;
                //if (file_cnt > 3) {
                //    alert("첨부파일은 3개까지 가능합니다.");
                //    return false;
                //}

                var file_name = file_obj[0].name;
                var file_size = file_obj[0].size;
                var ext = file_name.split('.').pop().toLowerCase();
                var maxSize = 20 * 1024 * 1024;
                var fileSize = file_size;
                //용량제한
                if (fileSize > maxSize) {
                    alert("첨부파일 사이즈는 20MB 이내로 등록 가능합니다.");
                    return false;
                }

                if ($.inArray(ext, format_ext) > 0) {
                    alert("첨부할 수 없는 파일입니다.\n파일명 : " + file_name + "");
                    return false;
                } else {
                    //fileListArr.push(file_obj[0]);
                    //works_list();
                    //$(".tdw_write_area_in #tdw_write_text").after('<div class="tdw_write_file_desc" id="tdw_write_file_desc"><span>' + file_name + '</span><button id="work_file_del">삭제</button></div>');

                    var fdata = new FormData();
                    fdata.append("files[]", file_obj[0]);
                    fdata.append("work_idx", no);
                    fdata.append("mode", "works_files_add");


                    console.log("file1 == " + file_obj[0]);
                    console.log("file2 == " + file_obj[0]);
                    console.log("file3 == " + file_obj[0].name);
                    console.log("file4 == " + file_obj[0].size);


                    //$(this).val($("#files_add")[0].files[0]);

                    //$("#files_add").val($("#files_add")[0].files[0]);
                    //console.log("vvv " + $(this).val());

                    $.ajax({
                        type: "POST",
                        data: fdata,
                        contentType: false,
                        processData: false,
                        url: '/inc/works_process.php',
                        success: function(data) {
                            console.log(data);
                            if (data == "complete") {
                                //$(this).val("");
                                //$("#files_add").replaceWith($("#files_add").clone(true));
                                //$("#files_add").remove();
                                works_list();
                                //$("#files_add").val("");
                            }
                        }
                    });
                }
            }
        });

    */

  //공유클릭
  $(document).on("click", "button[id=tdw_list_share]", function () {
    if ($(this).val()) {
      $("#work_idx").val($(this).val());
    }

    $(".layer_user_search_desc strong").text("업무 공유 받을 사람 선택");
    $(".layer_user_submit").attr("id", "layer_todaywork_user");
    $(".layer_user_submit span").text("공유하기");
    $("#work_type").val("share");
    $(".layer_user").show();
    //$(".btn_req").trigger("click");
  });

  //공유취소
  $(document).on("click", "button[id=tdw_list_share_cancel]", function () {
    if ($(this).parent().find(".tdw_list_jjim_clear").hasClass("on") == true) {
      var confirm_txt =
        "공유를 취소하면 좋아요가 회수됩니다.\n취소하시겠습니까?";
    } else {
      var confirm_txt = "공유했던 업무를 모두 취소합니다.\n진행하시겠습니까?";
    }

    if (confirm(confirm_txt)) {
      if ($(this).val()) {
        $("#work_idx").val($(this).val());
      }
      $("#work_type").val("share");
      var fdata = new FormData();
      fdata.append("work_idx", $("#work_idx").val());
      fdata.append("mode", "works_share_cancel");
      $.ajax({
        type: "POST",
        data: fdata,
        contentType: false,
        processData: false,
        url: "/inc/works_process.php",
        success: function (data) {
          console.log(data);
          if (data == "complete") {
            works_list();
            return false;
          }
        },
      });
    }
  });

  //페널티 이미지 미리보기
  $("#pl_img_preview, #pl_preview_01,#pl_preview_02,#pl_preview_03").click(
    function () {
      //var img = $(this).attr("src");
      var id = $(this).attr("id");
      if (id) {
        var img = $("#" + id + " img").attr("src");
        if (img) {
          $("#layer_penalty_img").attr("src", img);
          $(".layer_cha_image").show();
        }
      }
    }
  );

  $(document).on("click", "p[id=notice_link]", function () {
    console.log("링크");
    var val = $(this).attr("value");
    if (val) {
      if (GetCookie("user_id")) {
        location.href = "/challenge/view.php?idx=" + val;
      }
    }
  });

  $(document).on("click", "p[id=party_link]", function () {
    console.log("링크");
    var val = $(this).attr("value");
    if (val) {
      if (GetCookie("user_id")){
        location.href = "/party/view.php?idx=" + val;
      }
    }
  });

  //받는사람 삭제하기
  $(document).on("click", "button[id^=user_chk_del]", function () {
    var val = $(this).val();
    var user_chk_val = $("#chall_user_chk").val();

    if (val) {
      if (user_chk_val) {
        $(this).parent().remove();
        if (val) {
          user_slc_del(val);
          if (!$("#chall_user_chk").val()) {
            $("#tdw_write_user_desc").hide();
            $(".layer_user_cancel").trigger("click");
          }
        }
      } else {
        $("#tdw_write_user_desc").hide();
      }
    }
  });

  //첨부파일 다운로드
  link_href = window.location.href;
    var link_arr = link_href.split("/");
    home_title = link_arr[3];

  // $(document).on("click", "button[id^=btn_list_file]", function () {
  if(home_title == "todaywork"){  
  $(document).on("click", "button[id^=tdw_list_file]", function () {
    var url = "/inc/file_download.php";
    var num;
    var id = $(this).attr("id");

    if (id) {
      var num = id.replace("tdw_list_file_", ""); //$k
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
                });
        */
    }

    /*
            var url = "/inc/file_download.php";
            var num;
            var id = $(this).attr("id");

            if (id) {
                var num = id.replace("btn_list_file_", "");
                var idx = $(this).val();
                var mode = "todaywork";
                var params = { idx: idx, num: num, mode: mode };
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
            }
        */
    });
}

  //공유하기 변경
  $(document).on("click", "#layer_share_user", function () {
    //console.log("보고하기");
    //console.log($("#chall_user_chk").val());
    var idx = $("#work_idx").val();
    var user_chk_val = $("#chall_user_chk").val();

    if (user_chk_val) {
      arr_val = user_chk_val.split(",");
      for (var i = 0; i < arr_val.length; i++) {
        if (i > 0) {
          comma = ",";
        } else {
          comma = "";
        }

        //본인에게 보고 할경우
        if (GetCookie("user_id") == member_clist_id[arr_val[i]]) {
          alert("본인에게 공유를 할 수 없습니다.");
          return false;
        }
      }
    }

    var fdata = new FormData();
    fdata.append("mode", "work_share_add");
    fdata.append("idx", idx);
    fdata.append("user_chk_val", user_chk_val);
    $.ajax({
      type: "POST",
      data: fdata,
      contentType: false,
      processData: false,
      url: "/inc/works_process.php",
      success: function (data) {
        console.log(data);
        if (data == "complete") {
          works_list();
          $(".layer_user").hide();
        }
      },
    });
  });

  //보고하기 보고 받는사람 변경
  $(document).on("click", "#layer_report_user", function () {
    //console.log("보고하기");
    var idx = $("#work_idx").val();
    var user_chk_val = $("#chall_user_chk").val();

    if (user_chk_val) {
      arr_val = user_chk_val.split(",");
      for (var i = 0; i < arr_val.length; i++) {
        if (i > 0) {
          comma = ",";
        } else {
          comma = "";
        }

        //본인에게 보고 할경우
        if (GetCookie("user_id") == member_clist_id[arr_val[i]]) {
          alert("본인에게 보고를 할 수 없습니다.");
          return false;
        }
      }
    }

    var fdata = new FormData();
    fdata.append("mode", "work_report_add");
    fdata.append("idx", idx);
    fdata.append("user_chk_val", user_chk_val);
    $.ajax({
      type: "POST",
      data: fdata,
      contentType: false,
      processData: false,
      url: "/inc/works_process.php",
      success: function (data) {
        console.log(data);
        if (data == "complete") {
          works_list();
          layer_user_reset();
          $(".layer_user").hide();
        } else if (data == "not_user") {
          alert("받을사람을 선택 해주세요.");
          return false;
        } else if (data == "no_member") {
          alert("선택한 회원정보가 잘못되었습니다.");
          console.log(data);
          return false;
        } else {
          alert(
            "받을사람 변경중 문제가 발생하였습니다.\n관리자에게 문의 해주세요."
          );
          console.log(data);
          return false;
        }
      },
    });
  });

  //요청하기 요청받는 사람 변경
  $(document).on("click", "#layer_req_user", function () {
    console.log("요청하기");
    var idx = $("#work_idx").val();
    var user_chk_val = $("#chall_user_chk").val();

    if (user_chk_val) {
      arr_val = user_chk_val.split(",");
      for (var i = 0; i < arr_val.length; i++) {
        if (i > 0) {
          comma = ",";
        } else {
          comma = "";
        }

        //본인에게 요청 할경우
        if (GetCookie("user_id") == member_clist_id[arr_val[i]]) {
          alert("본인에게 요청를 할 수 없습니다.");
          return false;
        }
      }
    }

    var fdata = new FormData();
    fdata.append("mode", "work_req_add");
    fdata.append("idx", idx);
    fdata.append("user_chk_val", user_chk_val);
    $.ajax({
      type: "POST",
      data: fdata,
      contentType: false,
      processData: false,
      url: "/inc/works_process.php",
      success: function (data) {
        console.log(data);
        if (data == "complete") {
          works_list();
          layer_user_reset();
          $(".layer_user").hide();
        } else if (data == "not_user") {
          alert("업무 요청 받을사람을 선택 해주세요.");
          return false;
        } else if (data == "no_member") {
          alert("선택한 회원정보가 잘못되었습니다.");
          console.log(data);
          return false;
        } else {
          alert(
            "받을사람 변경중 문제가 발생하였습니다.\n관리자에게 문의 해주세요."
          );
          console.log(data);
          return false;
        }
      },
    });
  });

  //공유하기
  $("#layer_share_user1111").click(function () {
    if ($(this).hasClass("on")) {
      if ($("#chall_user_chk").val()) {
        var input_usrchk = "";
        user_chk_val = $("#chall_user_chk").val();
        var abt = $(".layer_user_info dd button");

        if (user_chk_val) {
          arr_val = user_chk_val.split(",");
          for (var i = 0; i < arr_val.length; i++) {
            if (i > 0) {
              comma = ",";
            } else {
              comma = "";
            }

            //본인선택시 제외
            if (GetCookie("user_id") == member_clist_id[arr_val[i]]) {
              if ($("#write_tab_03").hasClass("on") == true) {
                alert("본인에게 보고를 할 수 없습니다.");
                return false;
              } else {
                var altext = $("#layer_todaywork_user").text();
                if (altext == "공유하기") {
                  alert("본인에게 공유할 수 없습니다.");
                } else {
                  alert("본인에게 업무요청을 할 수 없습니다.");
                }
                return false;
              }
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
      todayworks_user_name();
    }
  });

  //검색 레이어 보이게
  $(document).on("click", "#btn_tdw_search", function () {
    //입력값초기화
    $("#sl1").val("");
    $("#btn_sort_on").val("");
    $("#input_cha_date_l").val(month_first_day);
    $("#input_cha_date_r").val(month_last_day);

    $("#btn_sort_on").html("<span>전체</span>");

    $("#search_layer").show();
    $("#sl1").focus();
    $(".datepickers-container").css("z-index", 9999);
  });

  $(document).on("click", "#input_cha_date_l,#input_cha_date_r", function () {
    //datepicker 재설정
    //var id = $(this).attr("id");
    //console.log(99);
  });

  $(document).on("click", "#date_area_l", function () {
    //console.log(111);
    //$(document).find("input[id^=listdate]").removeClass('hasDatepicker').datepicker();
    //$("#input_cha_date_l").datepicker();
    //console.log(222);
  });

  //검색 셀렉트 박스
  $(document).on("click", "#sl_slc #btn_sort_on", function () {
    $("#sl_slc").addClass("on");
  });

  //검색 닫기
  $(document).on("click", "#sl_close button", function () {
    $("#sl1").val("");
    $("#search_layer").hide();
  });

  //검색(구분) 셀렉트 박스 선택
  $(document).on("click", "#sl_slc ul li button", function () {
    v = $(this).val();
    if ($(this)) {
      var span_text = $(this).text();
      $("#btn_sort_on span").text(span_text);
      $("#btn_sort_on").val(v);
      $("#sl_slc").removeClass("on");
    }
  });

  //검색(셀렉트박스 벗어날때)
  $(".sl_slc").mouseleave(function () {
    $(".sl_slc").removeClass("on");
  });

  //검색어 입력후 엔터처리
  $("#sl1").bind("input keyup", function (e) {
    var id = $(this).attr("id");
    var input_val = $(this).val();
    if (input_val) {
      if (e.keyCode == 13) {
        $("#sl_btn").trigger("click");
        return false;
      }
    }
  });

  //검색버튼
  $(document).on("click", "#sl_btn", function () {

    var val = $("#sl1").val();
    if (!val) {
      alert("검색어를 입력하세요.");
      $("#sl1").focus();
      return false;
    }

    var btn_sort_on = $("#btn_sort_on").val();
    var sdate = $("#input_cha_date_l").val();
    var edate = $("#input_cha_date_r").val();
    var fdata = new FormData();

    wdate = sdate + " ~ " + edate;
    fdata.append("search", val);
    fdata.append("wdate", wdate);
    fdata.append("search_kind", btn_sort_on);
    fdata.append("works_type", "week");
    fdata.append("mode", "works_list_search");
    $.ajax({
      type: "POST",
      data: fdata,
      contentType: false,
      processData: false,
      url: "/inc/search_process.php",
      beforeSend: function () {
        $('.rewardy_loading_01').css('display', 'block');
      },
      complete: function (){
        $('.rewardy_loading_01').css('display', 'none');
      },
      success: function (data) {

        // console.log(data);
        if (data) {
          tdata = data.split("|");
          $("#work_date").val(wdate);
          $(".tdw_date_select button").removeClass("on");
          $(".select_ww").addClass("on");

          $(".tdw_list_dd").hide();
          $(".tdw_list_mm").hide();
          $(".tdw_list_ww").show();
          $("#work_date").width("220px");
          if ($("#work_month").is(":visible") == true) {
            $("#work_month").hide();
            $("#work_date").show();
          }

          $("#search_layer").hide();

          if (tdata) {
            $(".tdw_list_ww:contains('" + val + "')").each(function () {
              var regex = new RegExp(val, "gi");
              $(this).html(
                $(this)
                  .text()
                  .replace(
                    regex,
                    "<span class='tdw_list_desc'>" + val + "</span>"
                  )
              );
            });

            //var regex = new RegExp(val, 'gi');
            //tdata[0] = tdata[0].replace(regex, "<span class='tdw_list_desc'>" + val + "</span>");

            $(".tdw_list_ww").html(tdata[0]);

            //클릭처리
            $(".tdw_list_desc").trigger("click");

            //datepicker 재설정
            $(document)
              .find("input[id^=listdate]")
              .removeClass("hasDatepicker")
              .datepicker();

            //메모 라인수체크
            memo_line_check();

            return;
          }
        } else if (data == "com") {
          return;
        }
      },
    });
  });

  

  //보상하기
  $(document).on("click", "#tdw_list_100c", function () {
    var val = $(this).val();
    var fdata = new FormData();

    $("#lr_work_idx").val(val);

    $(".lr_btn").removeClass("live");
    $(".lr_btn").removeClass("party");
    $(".lr_btn").addClass("work");
    
    fdata.append("mode", "coin_100c");
    fdata.append("val", val);

    $.ajax({
      type: "post",
      async: false,
      data: fdata,
      contentType: false,
      processData: false,
      url: "/inc/works_process.php",
      success: function (data) {
        //console.log(data);
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
      },
    });
  });

  //오늘업무 코인지급하기
  $(document).on("click", "#btn_req_100c", function () {
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
      url: "/inc/works_process.php",
      success: function (data) {
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
      },
    });
  });

  //보상하기 닫기
  $("#lr_close").click(function () {
    $(".lr_area li button").removeClass("on");
    $("#lr_input").val("");
    $("#lr_input_text").val("");
    $("#layer_reward").hide();
  });

  //보상하기 선택
  $(".lr_area li button").click(function () {
    var val = $(this).val();

    $(".lr_area li button").removeClass("on");
    $(this).addClass("on");
    if (val) {
      $("#lr_val").val(val);
    }
  });

  //보상하기 클릭
  $(".lr_area ul li button").click(function () {
    var coin = $(this).find(".lr_coin em").text();
    var lr_input_text = $(this).find(".lr_txt strong").text();
    $("#lr_input").val(coin);
    $("#lr_input_text").val(lr_input_text);
  });

  //보상하기 버튼
  $(document).on("click", ".lr_btn", function () {

    var path = $(location).attr('pathname');
    var path_arr = path.split("/");
    // console.log(path_arr[1]);
    pathDi = path_arr[1];

    var lr_work_idx = $("#lr_work_idx").val();

    //console.log("lr_work_idx::"+lr_work_idx);

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

    if($('.lr_btn').hasClass("work") == true){
      if (confirm(coin + "코인을 지급 하시겠습니까?")) {
        var fdata = new FormData();
        fdata.append("mode", "coin_reward");

        fdata.append("coin", coin);
        fdata.append("lr_uid", lr_uid);
        fdata.append("lr_val", lr_val);
        fdata.append("lr_input_text", lr_input_text);
        fdata.append("lr_work_idx", lr_work_idx);
        fdata.append("path",pathDi);

        //console.log("lr_input_text::"+lr_input_text);
        $.ajax({
          type: "post",
          async: false,
          data: fdata,
          contentType: false,
          processData: false,
          url: "/inc/lives_process.php",
          success: function (data) {
            console.log(data);
            if (data) {
              if(data == "penalty"){
                alert("코인을 지급할 유저에게 패널티가 적용되어 보낼 수 없습니다.");
                return false;
              } else if (data == "id_same") {
                alert("보상은 본인에게 지급할 수 없습니다. ");
                return false;
              } else if (data == "none") {
                alert(
                  "보유한 공용코인이 지급할 코인보다 작습니다.\n보상할 코인을 확인해 주세요."
                );
                $("#lr_input").focus();
                return false;[]
              } else if (data == "complete") {
                alert(lr_input_text + " " + coin + "코인이 보상 되었습니다.");
                $("#lr_input").val("");
                $("#layer_reward").hide();
              }
              var tdata = data.split("|");
              if (tdata) {
                //var result = tdata[0];
                //var result_cnt = tdata[1];
                //$("#ldl_in_my").html(result);
                //$(".ldl_box").trigger("click");
                //$(".rew_mypage_section em:eq(1)").text(result_cnt);
              }
              works_list();
            }
          },
        });
      }
    }
  });


 // 반복설정 종료 날짜 input checked_event
 $(document).on("click", "input:radio[name='check_play_day']", function () {
  var isChecked = $('.check_end_day_select').hasClass('on');

  if ($(this).val() == 'check_play_day_02') {
    $('.check_end_day_select').toggleClass('on');

    if (!isChecked) {
      $('input:radio[name="check_play_day"]').prop('checked', true);
    }else{
      $('input:radio[name="check_play_day"]').prop('checked', false);
    }
  } else {
    $('.check_end_day_select').removeClass('on');
    $(this).prop('checked', false);
  }
});

 
$(document).on("click", "input:radio[name='check_play_week']", function () {
  var isChecked = $('.check_end_week_select').hasClass('on');

  if ($(this).val() == 'check_play_week_02') {
    $('.check_end_week_select').toggleClass('on');

    if (!isChecked) {
      $('input:radio[name="check_play_week"]').prop('checked', true);
    }else{
      $('input:radio[name="check_play_week"]').prop('checked', false);
    }
  } else {
    $('.check_end_week_select').removeClass('on');
    $(this).prop('checked', false);
  }
});

$(document).on("click", "input:radio[name='check_play_month']", function () {
  var isChecked = $('.check_end_month_select').hasClass('on');
  if ($(this).val() == 'check_play_month_02') {
    $('.check_end_month_select').toggleClass('on');

    if (!isChecked) {
      $('input:radio[name="check_play_month"]').prop('checked', true);
    }else{
      $('input:radio[name="check_play_month"]').prop('checked', false);
    }
  } else {
    $('.check_end_month_select').removeClass('on');
    $(this).prop('checked', false);
  }
});

$(document).on("click", "input:radio[name='check_play_year']", function () {
  var isChecked = $('.check_end_year_select').hasClass('on');
  if ($(this).val() == 'check_play_year_02') {
    $('.check_end_year_select').toggleClass('on');

    if (!isChecked) {
      $('input:radio[name="check_play_year"]').prop('checked', true);
    }else{
      $('input:radio[name="check_play_year"]').prop('checked', false);
    }
  } else {
    $('.check_end_year_select').removeClass('on');
    $(this).prop('checked', false);
  }
});



// 주 반복 체크박스에 따른 스크립트 텍스트 노출
    let labelValue = "";
    $(document).on("click change", "input:checkbox[name='week']", function () {
      const resultDiv = $(".replay_title_week strong");
      let labelText = ""; // 클릭 및 변경 시마다 labelText 초기화
        labelValue = "";
        $("input:checkbox[name='week']:checked").each(function() {
            const labelFor = $(this).attr("id");
            
            labelText += $("label[for='" + labelFor + "']").text() + " ";
            labelValue += $(this).attr("value") + " ";
        });

        resultDiv.text(labelText);
    });

$(document).on("click", ".choice_week .week_setting_btn", function () {
  $(".choice_week").addClass("on");
});

$(document).on("mouseleave", ".choice_week", function () {
  $(".choice_week").removeClass("on");
});



var selectedWeekValue;
$(document).on("click", ".week_setting ul li button", function () {
  selectedWeekValue = $(this).val();
  $(".week_setting_btn").text(selectedWeekValue);
  $(".choice_week").removeClass("on");
  // alert(123);
});
  // 반복설정 탭 열기
  var selectedValue;

  $(document).on("click", "button[id=tdw_list_repeat_new]", function () {
    selectedValue = $(this).attr("value");
    var work_wdate = $("#work_wdate").val();
    var dateArray = work_wdate.split('.');
    var fdata = new FormData();
      fdata.append("mode", 'calendar_event');
      fdata.append("work_idx", selectedValue);
      fdata.append("work_wdate", work_wdate);
      $.ajax({
        type : "post",
        async: false,
        data: fdata,
        contentType: false,
        processData: false,
        url: "/inc/works_process.php",
        success: function (data) {
          if (data) {
            $("#work_replay").html(data);
            if($('input[name="check_play_day"]').is(":checked") == true){
              $('.check_end_day_select').addClass('on');
            }
            var replayValue = $("#cal_cate").attr("value");
            console.log(replayValue);
              $(".replay_tab_navi ul li").removeClass("on");
              $(".replay_day").removeClass("on");
              $(".replay_week").removeClass("on");
              $(".replay_month").removeClass("on");
              $(".replay_year").removeClass("on");
              if (replayValue === 'day') {
                  $(".replay_tab_navi ul li.day").addClass("on");
                  $(".replay_day").addClass("on");
                  $(".week_setting_btn span").text("1");
                  $("#week").prop("checked", false);
                  $(".replay_month_set input").prop("checked", false);
                  $(".replay_year_set input").prop("checked", false);
              } else if (replayValue === 'week') {
                  $(".replay_tab_navi ul li.week").addClass("on");
                  $(".replay_week").addClass("on");
                  $(".replay_day_set input").prop("checked", false);
                  $(".replay_year_set input").prop("checked", false);
                  $(".replay_month_set input").prop("checked", false);
              } else if (replayValue === 'month') {
                  $(".replay_tab_navi ul li.month").addClass("on");
                  $(".replay_month").addClass("on");
                  $(".replay_day_set input").prop("checked", false);
                  $(".replay_year_set input").prop("checked", false);
                  $(".week_setting_btn span").text("1");
                  $("#week").prop("checked", false);
              } else if(replayValue === 'year'){
                  $(".replay_tab_navi ul li.year").addClass("on");
                  $(".replay_year").addClass("on");
                  $(".replay_day_set input").prop("checked", false);
                  $(".week_setting_btn span").text("1");
                  $("#week").prop("checked", false);
                  $(".replay_month_set input").prop("checked", false);
              } else{
                $(".replay_tab_navi ul li.day").addClass("on");
                $(".replay_day").addClass("on");
              }
          }else{
            $('.check_end_day_select').removeClass('on');
            $('input:radio[name="check_play_day"]').prop('checked', false);
            $('input:radio[name="day"]').prop('checked', false);
            $('.replay_set_cancel').parent('li').remove();
            $(".replay_tab_navi ul li").removeClass("on");
            $(".replay_day").removeClass("on");
            $(".replay_week").removeClass("on");
            $(".replay_month").removeClass("on");
            $(".replay_year").removeClass("on");
              if (!replayValue) {
                $(".replay_tab_navi ul li.day").addClass("on");
                $(".replay_day").addClass("on");
                $(".month_d").text(dateArray[2]);
                $(".year_m").text(dateArray[1]);
                $(".year_d").text(dateArray[2]);
              }
          }
          $(".replay_popup").show();
        },
      });
});


$(document).on("click", ".cancel_button", function () {
  $(".replay_popup").css('display', 'none');
});

$(document).on("click", "button[id=tdw_list_repeat_info_new]", function () {
     selectedValue = $(this).attr("value");
      $(".layer_re_info").show();
  });

  $(document).on("click", ".layer_re_info_cancel", function () {
    $(".layer_re_info").css('display', 'none');
  });

  //기존 반복업무 설정 해제
  $(document).on("click", ".layer_re_info_submit", function(){
    var fdata = new FormData();
    var work_id = selectedValue;
    var work_wdate = $("#work_wdate").val();
    console.log(work_id);
    console.log(work_wdate);
    fdata.append("mode", 'prev_repeat');
    fdata.append("work_idx", work_id);
    fdata.append("work_wdate", work_wdate);
        $.ajax({
          type : "post",
          async: false,
          data: fdata,
          contentType: false,
          processData: false,
          url: "/inc/works_process.php",
          success: function (data) {
            console.log(data);
            if (data == 'complete') {
              alert("반복업무가 해제 되었습니다.");
              $(".layer_re_info").hide();
              works_list();
            }else{
              alert("반복업무 해제가 되지 않았습니다.");
              return false;
            }
          },
        });
  });
  
  $(document).on("click", ".submit_button", function () {
    //업무 idx, 현재날짜, 종료날짜
    var work_id = selectedValue;
    var weekCount = selectedWeekValue;
    var work_wdate = $("#work_wdate").val();
    var mode = $(".replay_tab_navi ul li.on").attr("value");
    var closeDate = null;
    
    var checkedTypeDay = document.querySelector('input[name="day"]:checked');
    var checkedTypeWeek = document.querySelector('input[name="week"]:checked');
    var checkedTypeMonth = document.querySelector('input[name="month"]:checked');
    
    var interval = '0';
    var noWeek = '0';
    var repeatType = '0'


    
    if(checkedTypeDay && mode == 'day'){
      var cancelDate = document.querySelector('input[name="cancel_day"]:checked');

      checkedId = checkedTypeDay.id;
      if(checkedId == 'check_day_03'){
        interval = $("#day_setting").val();
        repeatType = '3';

      }else if(checkedId == 'check_day_02'){
        noWeek = $("#check_day_02").val();
        repeatType = '2';
      }else{
        repeatType = '1';
      }
    }
    
    if(checkedTypeMonth && mode == 'month'){
      checkedMonthId = checkedTypeMonth.id;

      if(checkedMonthId == 'check_month_03'){
        repeatType = '3';
      }else if(checkedMonthId == 'check_month_02'){
        repeatType = '2';
      }else{
        repeatType = '1';
      }
    }

    if(mode == 'day'){
        var fdata = new FormData();
        var dateInputDay = document.getElementById('closeDateDay');
        if(dateInputDay.value){
          closeDate = dateInputDay.value;
          console.log(closeDate);
        }
        if(cancelDate){
          cancelDate = cancelDate.id;
          fdata.append("cancel_date", cancelDate);
        }

        if(checkedTypeDay && !interval){
          alert("원하시는 간격 일수를 적어주세요!");
          return false;
        }else if(!checkedTypeDay){
          alert("원하시는 항목을 체크해주세요!");
          return false;
        }

        fdata.append("mode", 'calendar_day');
        fdata.append("repeat_frequency", mode);
        fdata.append("noweek", noWeek);
        fdata.append("work_idx", work_id);
        fdata.append("repeat_type", repeatType);
        fdata.append("work_wdate", work_wdate);
        fdata.append("close_date", closeDate);
        fdata.append("checked_type", checkedId);
        fdata.append("interval", interval);
        $.ajax({
          type : "post",
          async: false,
          data: fdata,
          contentType: false,
          processData: false,
          url: "/inc/works_process.php",
          success: function (data) {
            console.log(data);
            if (data == 'close') {
              alert("취소날짜가 적용되었습니다.");
              $(".replay_popup").hide();
              works_list();
            }else{
              alert("적용되었습니다.");
              $(".replay_popup").hide();
              works_list();
            }
          },
        });

    }else if(mode == 'week'){
      var cancelDate = document.querySelector('input[name="cancel_week"]:checked');
      var labelArray = labelValue.split(' ');
      var weekDay = labelArray.slice(0, -1);
      var dateInputWeek = document.getElementById('closeDateWeek');
      if(dateInputWeek.value){
        closeDate = dateInputWeek.value;
      }
      var fdata = new FormData();
      if(!weekCount){
        weekCount = $(".week_setting_btn").val();
      }
      //수정해야할 부분
      if(!checkedTypeWeek){
        alert("원하시는 반복 요일을 체크해주세요!");
        return false;
      }

      if(cancelDate){
        cancelDate = cancelDate.id;
        fdata.append("cancel_date", cancelDate);
        console.log(cancelDate);

      }
      console.log(closeDate);
      fdata.append("mode", 'calendar_week');
      fdata.append("repeat_frequency", mode);
      fdata.append("work_idx", work_id);
      fdata.append("work_wdate", work_wdate);
      fdata.append("close_date", closeDate);
      fdata.append("week_count", weekCount);
      fdata.append("week_day", weekDay);
      


      $.ajax({
        type : "post",
        async: false,
        data: fdata,
        contentType: false,
        processData: false,
        url: "/inc/works_process.php",
        success: function (data) {
          console.log(data);
          if (data == 'close') {
            alert("취소날짜가 적용되었습니다.");
            $(".replay_popup").hide();
            works_list();
          }else{
            alert("적용되었습니다.");
            $(".replay_popup").hide();
            works_list();
          }
        },
      });
    }else if(mode == 'month'){
       var fdata = new FormData();
       var dateInputMonth = document.getElementById('closeDateMonth');
        if(dateInputMonth.value){
          closeDate = dateInputMonth.value;
        }
       var cancelDate = document.querySelector('input[name="cancel_month"]:checked');
       if(cancelDate){
        cancelDate = cancelDate.id;
        fdata.append("cancel_date", cancelDate);
      }

      if(!checkedTypeMonth){
        alert("원하시는 항목을 체크해주세요!");
        return false;
      }

      fdata.append("mode", 'calendar_month');
      fdata.append("repeat_frequency", mode);
      fdata.append("work_idx", work_id);
      fdata.append("repeat_type", repeatType);
      fdata.append("work_wdate", work_wdate);
      fdata.append("close_date", closeDate);
      $.ajax({
        type : "post",
        async: false,
        data: fdata,
        contentType: false,
        processData: false,
        url: "/inc/works_process.php",
        success: function (data) {
          console.log(data);
          if (data) {
            alert("적용되었습니다.");
            $(".replay_popup").hide();
            works_list();
          }
        },
      });
    }else if(mode == 'year'){
       var fdata = new FormData();
       var dateInputYear = document.getElementById('closeDateYear');
        if(dateInputYear.value){
          closeDate = dateInputYear.value;
          console.log(closeDate);
        }
       var cancelDate = document.querySelector('input[name="cancel_year"]:checked');
       if(cancelDate){
        cancelDate = cancelDate.id;
        fdata.append("cancel_date", cancelDate);
      }
      fdata.append("mode", 'calendar_year');
      fdata.append("repeat_frequency", mode);
      fdata.append("work_idx", work_id);
      fdata.append("repeat_type", repeatType);
      fdata.append("work_wdate", work_wdate);
      fdata.append("close_date", closeDate);
      
      $.ajax({
        type : "post",
        async: false,
        data: fdata,
        contentType: false,
        processData: false,
        url: "/inc/works_process.php",
        success: function (data) {
          console.log(data);
          if (data == 'close') {
            alert("취소날짜가 적용되었습니다.");
            $(".replay_popup").hide();
            works_list();
          }else{
            alert("적용되었습니다.");
            $(".replay_popup").hide();
            works_list();
          }
        },
      });

  }
  });
   
  

  //파티연결 레이어 열기
  $(document).on("click", "button[id=tdw_list_party_link]", function () {
    var val = $(this).val();
    $("#pll_box_party_link").attr("value",val);

    var work_idx = val;
    var work_date = $("#work_date").val();
    var fdata = new FormData();

    fdata.append("mode", "party_layer_open");
    fdata.append("work_idx", work_idx);
    fdata.append("work_date", work_date);
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
          $("#pll_box_party_link").html(data);
          $("#party_link_layer").show();
        }
      },
    });
  });

  //파티연결해제
  $(document).on("click", "#party_link_reset", function () {
    if (confirm("파티 연결을 해제하시겠습니까?")) {
      var fdata = new FormData();
      var len = $("#party_link_layer").find(".ldl_box").length;
      // var work_idx = $("#work_idx").val();
      var work_idx = $("#pll_box_party_link").attr("value");
      var work_date = $("input[id=work_date]").val();

      for (var i = 0; i < len; i++) {
        var ldl_box_st = $("#party_link_layer")
          .find(".ldl_box")
          .eq(i)
          .hasClass("on");
        if (ldl_box_st == true) {
          val = $("#party_link_layer")
            .find(".ldl_box")
            .eq(i)
            .find("#ldl_chk")
            .val();
          fdata.append("party_idx[]", val);
        }
      }

      fdata.append("mode", "party_link_clear");
      fdata.append("work_idx", work_idx);
      fdata.append("work_date", work_date);
      $.ajax({
        type: "post",
        async: false,
        data: fdata,
        contentType: false,
        processData: false,
        url: "/inc/works_process.php",
        beforeSend: function () {
          $('.rewardy_loading_01').css('display', 'block');
        },
        complete: function (){
          $('.rewardy_loading_01').css('display', 'none');
        },
        success: function (data) {
          console.log(data);
          if (data == "complete") {
            alert("파티가 모두 해제 되었습니다.");
            //$(".party_link_layer .ldl_chk button").closest(".ldl_box").removeClass("on");
            $("#party_link_layer").hide();
            works_list();
            return false;
          }
        },
      });
    }
  });

  //파티연결 레이어 닫기
  $(document).on("click", "#pll_close button", function () {
    $("#party_link_layer").hide();
  });

  $(document).on("click", ".layer_party_cancel", function () {
    $("#party_link_layer").hide();
  });

  //파티연결 - 파티 선택하기
  $(document).on("click", ".party_link_layer .ldl_chk button", function () {
    //$(".party_link_layer .ldl_chk button").click(function() {
    $(this).closest(".ldl_box").toggleClass("on");
  });

  //마우스오버
  $(document).on("mouseenter click", "ldl_chk,.ldl_box_in", function (e) {
    $(this).css("cursor", "pointer");
  });

  //파티연결 - 파티 박스 선택시
  $(document).on("click", "#ldl_chk,.ldl_box_in", function () {
    $(this).closest(".ldl_box").toggleClass("on");
    var party_lenght = $(".ldl_box.on").length;

      
      if(party_lenght > 0 ){
        $(".layer_party_submit").addClass("on");
        $("#partycnt").text(", " + party_lenght + "개 선택");
      }else{
        $(".layer_party_submit").removeClass("on");
        $("#partycnt").text("");
      }

      if(!$("#ppl_com_btn").val()){
      if(party_lenght > 0 ){
        var oldId = document.getElementById("party_link_reset");
        if(oldId){
        var newId = "party_link_edit";
        oldId.id = newId;

        $(".layer_party_change").addClass("on");
        
        
        $("#partycnt").text(", " + party_lenght + "개 선택");
        }
      }else if(party_lenght == 0){
        var oldId = document.getElementById("party_link_edit");
        if(oldId){
          var newId = "party_link_reset";
          oldId.id = newId;

        $(".layer_party_change").addClass("on");
        }
      }
     }
  });

  //참여자 전체선택
  // party_

  //파티 연결 버튼
  $(document).on("click", "#ppl_com_btn", function () {
    var be_arr = new Array();

    //var len = $(".ldl_box").length;
    var fdata = new FormData();
    var work_date = $("input[id=work_date]").val();
    var work_idx = $("#pll_box_party_link").attr("value");
  
    var len = $("#party_link_layer").find(".ldl_box").length;
    var party_check = 0;
    for (var i = 0; i < len; i++) {
      var ldl_box_st = $("#party_link_layer")
        .find(".ldl_box")
        .eq(i)
        .hasClass("on");
      if (ldl_box_st == true) {
        val = $("#party_link_layer")
          .find(".ldl_box")
          .eq(i)
          .find("#ldl_chk")
          .val();
        fdata.append("party_idx[]", val);
        be_arr.push(val);
      }
    }

    var len2 = $("#party_link_layer").find(".ldl_box.on").length;
    var party_arr = [];
    if(len2>1){
      
      for(var i=0;i<len2;i++){
        var partyname = $(".on .ldl_box_tit").find('p').eq(i).text();
        party_arr.push(partyname); 
      }
    }else{
      var partyname = $(".on .ldl_box_tit").find('p').eq(0).text();
      party_arr.push(partyname);
    }
   

    fdata.append("mode", "party_add");
    fdata.append("work_idx", work_idx);
    fdata.append("work_date", work_date);
    
    name_chk = $(".tdw_write_file_desc").eq(i).find('span').text();

    $.ajax({
      type: "post",
      async: false,
      data: fdata,
      contentType: false,
      processData: false,
      url: "/inc/works_process.php",
      beforeSend: function () {
        $('.rewardy_loading_01').css('display', 'block');
      },
      complete: function (){
        $('.rewardy_loading_01').css('display', 'none');
      },
      success: function (data) {
        console.log(data);
        tdata = data.split("|");
        result = tdata[1];
        console.log(result);
        if (result == "party_not") {
          alert("파티를 선택해주세요.");
          return false;
        } else if (result == "complete") {
          //$(".party_link_layer .ldl_chk button").closest(".ldl_box").removeClass("on");
          alert("파티가 연결 되었습니다.");
          $("#party_link_layer").hide();
          works_list();
        }else if(result == "be_works_party"){
          $("#be_party_idx").val(be_arr);
          $("#party_link_layer").hide();
          $(".tdw_write_par").addClass("on");
          name_chk = $(".tdw_write_file_desc").length;
          if(name_chk>0){
            $(".tdw_write_function").nextAll(".tdw_write_file_desc").remove();
          }
          for(var i=0; i<len2; i++){
            // name_chk = $("#tdw_write_file_desc_"+i).find('span').text();
              $(".tdw_write_function").after(
                '<div class="tdw_write_file_desc" id="tdw_write_file_desc_'+i+'"><span>' +
                  party_arr[i] +
                  '</span></div>'
              );
          }
        }
      },
    });
  });
  
  // $(document).on("click","#work_party_del",function(){
  //   $(this).parent(".tdw_write_file_desc").remove();
  //   $(this).parent(".ldl_box").removeClass("on");
  // });

    //파티연결 레이어 열기
  $(document).on("click", "button[id=today_party_link]", function () {
    
    var val = $(this).val();
    $("#pll_box_party_link").attr("value",val);
    var work_idx = val;
    var work_date = $("#work_date").val();
    var be_party_idx = $("#be_party_idx").val();
    var fdata = new FormData();

    fdata.append("mode", "party_layer_open");
    fdata.append("work_idx", work_idx);
    fdata.append("work_date", work_date);
    fdata.append("be_party_idx", be_party_idx);
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
          $("#pll_box_party_link").html(data);
          $("#party_link_layer").show();
        }
      },
    });
  });


  //파티 변경 완료 버튼
  $(document).on("click", "#party_link_edit", function () {
    var fdata = new FormData();
    var len = $("#party_link_layer").find(".ldl_box").length;
    var work_idx = $("#pll_box_party_link").attr("value");
    var work_date = $("input[id=work_date]").val();

    var ldl_box_st_cnt = 0;
    for (var i = 0; i < len; i++) {
      var ldl_box_st = $("#party_link_layer")
        .find(".ldl_box")
        .eq(i)
        .hasClass("on");
      if (ldl_box_st == true) {
        val = $("#party_link_layer")
          .find(".ldl_box")
          .eq(i)
          .find("#ldl_chk")
          .val();
        fdata.append("party_idx[]", val);
        ldl_box_st_cnt++;
      }
    }

    if (ldl_box_st_cnt == 0) {
      alert("파티를 선택해주세요.");
      return false;
    }

    if (confirm("파티 연결을 변경하시겠습니까?")) {
      fdata.append("mode", "party_link_edit");
      fdata.append("work_idx", work_idx);
      fdata.append("work_date", work_date);
      $.ajax({
        type: "post",
        async: false,
        data: fdata,
        contentType: false,
        processData: false,
        url: "/inc/works_process.php",
        beforeSend: function () {
          $('.rewardy_loading_01').css('display', 'block');
        },
        complete: function (){
          $('.rewardy_loading_01').css('display', 'none');
        },
        success: function (data) {
          console.log(data);
          if (data == "complete") {
            alert("파티 변경 되었습니다.");
            //$(".party_link_layer .ldl_chk button").closest(".ldl_box").removeClass("on");
            $("#party_link_layer").hide();

            return false;
          }
        },
      });
    }
  });

  setTimeout(function () {
    $("#workslist_last").hide();
  }, 200);

  //클릭처리
  $(".tdw_list_desc").trigger("click");



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
                                    $('.rew_main_anno_in span').text(p_memo);
                                }
                                if(result2){
                                    $(".user_img").css("background-image", "url(" + result2 + ")");
                                }
                                if(result3){
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

//파일삭제하기
function todaywork_file_del(fileListArr) {
  //var file_obj = $("input[id='files']");
  //var fileListArr = Array.from($("input[id='files']")[0].files);

  ///var file_cnt = $(".tdw_write_area_in #tdw_write_file_desc").length;
  var file_obj = $("input[id='files']");

  if (file_obj) {
    file_cnt = file_obj[0].files.length;

    for (i = 0; i < file_cnt; i++) {
      $("#tdw_write_file_desc").remove();
    }

    console.log("fileListArr :: " + fileListArr);
    console.log("fileListArr length :: " + fileListArr.length);

    if (fileListArr.length > 0) {
      fileListArr = [];
      fileListArr = new Array();
      console.log("완전삭제");
    }

    //모두삭제후 빈값으로 처리
    var file_box_cnt = $(".tdw_write_area_in #tdw_write_file_desc").length;
    console.log("file_box_cnt : " + file_box_cnt);
    if (file_box_cnt == 0) {
      if ($("input[id='files']").val()) {
        $("input[id='files']").val("");
        console.log("파일지우기");
      }

      console.log("fileListArr 길이 :: " + fileListArr.length);
    }
    return fileListArr;
  }
}

//받을사람 선택삭제
function user_slc_del(v) {
  if (v) {
    var user_chk_val = $("#chall_user_chk").val();
    arr_val = user_chk_val.split(",");
    var member_total_cnt = $("#chall_user_cnt").val();

    if ($(".layer_user_info ul #udd_" + v + " button").hasClass("on") == true) {
      $(".layer_user_info ul #udd_" + v + " button").removeClass("on");
      arr_val = arr_val.filter((item) => !v.includes(item));
      $("#chall_user_chk").val(arr_val);

      var bid = $(".layer_user_info ul #udd_" + v + " button").attr("id");
      var team_len = $("button[id=" + bid).length;
      var team_no = bid.replace("team_", "");
      var team_bt_cnt = 0;
      for (i = 0; i < team_len; i++) {
        if (
          $(".layer_user_info dd button[id=" + bid)
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
        $("#usercnt").text($("#usercnt").text() + ", " + userchk + "명 선택");
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

    $("#user_" + v).remove();
    //$(this).parent("li").remove();

    if ($(".lm_area .layer_user_slc_list ul li").length > 5) {
      $(".lm_area .layer_user_slc_list").addClass("over_4");
    } else if ($(".lm_area .layer_user_slc_list ul li").length < 3) {
      $(".lm_area .layer_user_slc_list").removeClass("over_4");
    } else {
      $(".lm_area .layer_user_slc_list").removeClass("over_4");
    }
  }
}

//한줄소감 감추기
function feeling_banner_reload(v) {
  if (v) {
    var wwdate = v.substr(0, 10);
    var twdfeeling = wwdate.replace(/\./g, "-");
    $("div[id=tdw_feeling_banner_" + twdfeeling + "]").hide();
  }
}

//타임시작
function startTimer() {
  isPuase = false;
  timers = setInterval(function () {
    penalty_timer();
  });
}

//타임멈춤
function stopTimer() {
  clearInterval(timers);
  isPuase = true;
}

//페널티 시간
function penalty_timer() {
  if (!isPuase) {
    var now = new Date();
    var end = new Date(
      now.getFullYear(),
      now.getMonth(),
      now.getDate(),
      late_etime,
      00,
      00
    );
    var open = new Date(
      now.getFullYear(),
      now.getMonth(),
      now.getDate(),
      late_stime,
      00,
      00
    );

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
      sec = sec - day * 60 * 60 * 24;
      hour = parseInt(sec / 60 / 60);
      sec = sec - hour * 60 * 60;
      min = parseInt(sec / 60);
      sec = parseInt(sec - min * 60);

      if (hour < 10) {
        hour = "0" + hour;
      }
      if (min < 10) {
        min = "0" + min;
      }
      if (sec < 10) {
        sec = "0" + sec;
      }

      //    $(".hours").html(hour);
      //    $(".minutes").html(min);
      //    $(".seconds").html(sec);

      obj.text(hour + ":" + min + ":" + sec);
    } else if (nt > et) {
      //$("p.time-title").html("금일 마감");
      //$(".time").fadeOut();

      //자동 알림발송
      var fdata = new FormData();
      fdata.append("penalty_send", "1");
      fdata.append("mode", "penalty_send_alarm");
      $.ajax({
        type: "POST",
        data: fdata,
        contentType: false,
        processData: false,
        url: "/inc/penalty_process.php",
        success: function (data) {
          console.log(data);
          if (data == "complete") {
            //obj.fadeOut();
            obj.text("종료");
            stopTimer();
            return;
          } else if (data == "com") {
            obj.text("종료");
            stopTimer();
            return;
          }
        },
      });
    } else {
      obj.fadeIn();
      //$("p.time-title").html("금일 마감까지 남은 시간");
      sec = parseInt(et - nt) / 1000;
      day = parseInt(sec / 60 / 60 / 24);
      sec = sec - day * 60 * 60 * 24;
      hour = parseInt(sec / 60 / 60);
      sec = sec - hour * 60 * 60;
      min = parseInt(sec / 60);
      sec = parseInt(sec - min * 60);

      if (hour < 10) {
        hour = "0" + hour;
      }
      if (min < 10) {
        min = "0" + min;
      }
      if (sec < 10) {
        sec = "0" + sec;
      }

      //$(".hours").html(hour);
      //$(".minutes").html(min);
      //$(".seconds").html(sec);
      obj.text(hour + ":" + min + ":" + sec);
    }
  }
}

let isPuase = false;
let timers;

//페널티 이미지 미리보기
function penalty_img_preview() {
  var input = this;
  var id = $(this).attr("id");
  var no = id.replace("pl_file_", "");

  if (input.files && input.files.length && no) {
    var reader = new FileReader();
    this.enabled = false;
    var str = $(this).val();
    var fileName = str.split("\\").pop().toLowerCase();

    //1. 확장자 체크
    var ext = fileName.split(".").pop().toLowerCase();
    if ($.inArray(ext, ["jpg", "jpeg", "gif", "png"]) == -1) {
      alert("이미지 파일만 등록 가능합니다.\n파일을 확인해 주세요.");
      return false;
    }

    reader.onload = function (e) {
      //console.log(e)
      //let img = new Image();
      //img.src = e.target.result;
      //console.log( img.width);

      /*console.log(input.files[0]['name']);
            var img = $("#file_" + no);
            console.log(img.width);*/
      $("#pl_preview_" + no).html(
        ['<img src="', e.target.result, '">'].join("")
      );
    };
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
      url: "/inc/penalty_process.php",
      success: function (data) {
        //console.log("data :: " + data);
        if (data == "complete") {
          var comp_img = "/html/images/pre/img_comp.png";
          $("#pl_img_comp_" + no + " img").attr("src", comp_img);
          $("#btn_penalty_banner_" + no).hide();
          $("#penalty_comp_" + no).show();
          setTimeout(function () {
            $("#pl_img_comp_" + no + " img").show();
          }, 10);
        }
      },
    });
  }
}

//오늘 한 줄 소감 배열값
function ff_text_arr(v) {
  if (v) {
    var ff_text = new Array();
    var result = "";
    ff_text[1] = "최고의";
    ff_text[2] = "뿌듯한";
    ff_text[3] = "기분 좋은";
    ff_text[4] = "감사한";
    ff_text[5] = "재밌는";
    ff_text[6] = "수고한";
    ff_text[7] = "무난한";
    ff_text[8] = "지친";
    ff_text[9] = "속상한";
    result = ff_text[v];
    return result;
  }
}

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
      console.log("리스트 시작하기2");
      console.log("data :: " + data);

      var tdata = data.split("|");
      if (tdata) {
        var html = tdata[0];
        var totcnt = tdata[1].trim();

        if (totcnt > 0) {
          $("#chall_user_cnt").val(totcnt);
          $(".layer_user_info").empty();

          if (userchk > 0) {
            $("#usercnt").text("전체 " + totcnt + "명, " + userchk + "명 선택");
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
    },
  });
}

//반복설정
function repeat_list(id, val) {
  var fdata = new FormData();
  fdata.append("mode", "works_repeat");
  fdata.append("repeat", id);
  fdata.append("val", val);
  $.ajax({
    type: "POST",
    data: fdata,
    contentType: false,
    processData: false,
    url: "/inc/works_process.php",
    success: function (data) {
      console.log(data);
      if (data) {
        var tdata = data.split("|");
        if (tdata) {
          result = tdata[0];
          result1 = tdata[1];
          if (result == "complete") {
            if (result1 == "day") {
              alert("매일 반복되는 업무로 등록되었습니다.");
            } else if (result1 == "week") {
              alert("매주 반복되는 업무로 등록되었습니다.");
            } else if (result1 == "month") {
              alert("매월 반복되는 업무로 등록되었습니다.");
            } else if (result1 == "not") {
              alert("반복설정이 취소되었습니다.");
            }
            works_list();
            return false;
          }
        }
      }
    },
  });
}

// 검색 새로고침
function searchs_list(){
  var val = $("#sl1").val();
  if (!val) {
    alert("검색어를 입력하세요.");
    $("#sl1").focus();
    return false;
  }

  var btn_sort_on = $("#btn_sort_on").val();
  var sdate = $("#input_cha_date_l").val();
  var edate = $("#input_cha_date_r").val();
  var fdata = new FormData();

  wdate = sdate + " ~ " + edate;
  fdata.append("search", val);
  fdata.append("wdate", wdate);
  fdata.append("search_kind", btn_sort_on);
  fdata.append("works_type", "week");
  fdata.append("mode", "works_list_search");
  $.ajax({
    type: "POST",
    data: fdata,
    contentType: false,
    processData: false,
    url: "/inc/search_process.php",
    beforeSend: function () {
      $('.rewardy_loading_01').css('display', 'block');
    },
    complete: function (){
      $('.rewardy_loading_01').css('display', 'none');
    },
    success: function (data) {

      // console.log(data);
      if (data) {
        tdata = data.split("|");
        $("#work_date").val(wdate);
        $(".tdw_date_select button").removeClass("on");
        $(".select_ww").addClass("on");

        $(".tdw_list_dd").hide();
        $(".tdw_list_mm").hide();
        $(".tdw_list_ww").show();
        $("#work_date").width("220px");
        if ($("#work_month").is(":visible") == true) {
          $("#work_month").hide();
          $("#work_date").show();
        }

        $("#search_layer").hide();

        if (tdata) {
          $(".tdw_list_ww:contains('" + val + "')").each(function () {
            var regex = new RegExp(val, "gi");
            $(this).html(
              $(this)
                .text()
                .replace(
                  regex,
                  "<span class='tdw_list_desc'>" + val + "</span>"
                )
            );
          });
          $(".tdw_list_ww").html(tdata[0]);

          //클릭처리
          $(".tdw_list_desc").trigger("click");

          //datepicker 재설정
          $(document)
            .find("input[id^=listdate]")
            .removeClass("hasDatepicker")
            .datepicker();

          //메모 라인수체크
          memo_line_check();

          return;
        }
      } else if (data == "com") {
        return;
      }
    },
  });
}

//오늘업무, 일일리스트
function works_list() {
  var thisfilefullname = document.URL.substring(
    document.URL.lastIndexOf("/") + 1,
    document.URL.length
  );
  //console.log("thisfilefullname :: " + thisfilefullname);
  if (thisfilefullname == "index01.php") {
    var mode = "works_list_new";
  } else {
    var mode = "works_list";
  }

  var fdata = new FormData();
  if ($(".select_dd").hasClass("on") == true) {
    var works_type = "day";
    var wdate = $("#work_date").val();
    var cate = "all";
    fdata.append("work_wdate", $("#work_wdate").val());

    // if ($("#work_wdate").val()) {
    //   feeling_banner_reload($("#work_wdate").val());
    // }
  } else if ($(".select_ww").hasClass("on") == true) {
    var works_type = "week";
    var wdate = $("#work_date").val();
    fdata.append("work_wdate", $("#work_wdate").val());

    // if ($("#work_wdate").val()) {
    //   feeling_banner_reload($("#work_wdate").val());
    // }
  } else if ($(".select_mm").hasClass("on") == true) {
    var works_type = "month";
    var wdate = $("#work_month").val();

    var fee = $("#work_wdate").val();
    feeling_banner_reload(fee);
    fdata.append("work_wdate", $("#work_month").val());
  }
  if ($(".select_report").hasClass("on") == true) {
    var works_type = "day";
    var wdate = $("#work_date").val();
    fdata.append("work_wdate", $("#work_wdate").val());

    // if ($("#work_wdate").val()) {
    //   feeling_banner_reload($("#work_wdate").val());
    // }
  }

  if ($(".all").hasClass("on") == true) {
    var cate = "all";
  } else if ($(".report").hasClass("on") == true) {
    var cate = "report";
  } else if ($(".req").hasClass("on") == true) {
    var cate = "req";
  } else if ($(".user").hasClass("on") == true) {
    var cate = "user";
  } else if ($(".share").hasClass("on") == true) {
    var cate = "share";
  } else if ($(".work").hasClass("on") == true) {
    var cate = "work";
  } 


  fdata.append("wdate", wdate);
  //fdata.append("mode", "works_list");
  fdata.append("mode", mode);
  fdata.append("works_type", works_type);
  fdata.append("cate", cate);

  $.ajax({
    type: "POST",
    data: fdata,
    contentType: false,
    processData: false,
    url: "/inc/works_process.php",
    beforeSend: function () {
      $('.rewardy_loading_01').css('display', 'block');
    },
    complete: function (){
      $('.rewardy_loading_01').css('display', 'none');
    },
    success: function (data) {
      //console.log("data :: " + data);
      if (data) {
        if (works_type == "day") {
          $(".tdw_list_dd").html(data);
          $(".tdw_list_ww").html("");
          $(".tdw_list_mm").html("");
          //$(".tdw_list_ul").html(data);
         } else if (works_type == "week") {
          tdata = data.split("|");
          if (tdata[1]) {
            //    $("#work_date").val(tdata[1]);
          }

          $(".tdw_list_ww").html(data);
          $(".tdw_list_dd").html("");
          $(".tdw_list_mm").html("");
         } else if (works_type == "month") {
          $(".tdw_list_mm").html(data);
          $(".tdw_list_dd").html("");
          $(".tdw_list_ww").html("");
        }

        //클릭처리
        $(".tdw_list_desc").trigger("click");

        //id값이 여러개일때 사용됨
        //datepicker 재설정
        $(document)
          .find("input[id^=listdate]")
          .removeClass("hasDatepicker")
          .datepicker();

        //메모 라인수체크
        memo_line_check();

      length_work = $("div[id^=tdw_list_box_]").length;

			for(var i = 0; i<length_work; i++){
       
          var divElement = $("div[name=onoff_"+i+"]").parent();

          // div 하위의 p 엘리먼트 찾기
          var pElement = divElement.find("p");

          // span 내의 텍스트 가져오기
          var extractedText = pElement.html();
          var brTagCount = (extractedText.match(/<br>/g) || []).length;

          btn_work = $("div[name=onoff_"+i+"]").parent().find("button[id^=btn_list_work_onoff]");
          btn_report = $("div[name=onoff_"+i+"]").parent().find("button[id^=btn_list_report_onoff]");
          btn_share = $("div[name=onoff_"+i+"]").parent().find("button[id^=btn_list_share_onoff]");
          btn_req = $("div[name=onoff_"+i+"]").parent().find("button[id^=btn_list_req_onoff]");
            if(brTagCount < "3"){
              btn_work.css("display","none");
              btn_share.css("display","none");
              btn_report.css("display","none");
              btn_req.css("display", "none");
            }else{
              btn_work.css("display","block");
              btn_share.css("display","block");
              btn_report.css("display","block");
              btn_req.css("display", "block");
            }
        }
      } else {
        $(".tdw_list_ul").html("");
        $(".tdw_list_ww").html("");
      }
    },
  });
}

function todayworks_user_name() {
  var fdata = new FormData();
  if ($("#chall_user_chk").val()) {
    var user_chk_val = $("#chall_user_chk").val();
    fdata.append("work_user_chk", user_chk_val);
    fdata.append("mode", "works_user_name");
    $.ajax({
      type: "POST",
      data: fdata,
      contentType: false,
      processData: false,
      url: "/inc/works_process.php",
      success: function (data) {
        //console.log(data);
        if (data) {
          tdata = data.split("|");
          if (tdata[0] == "complete") {
            if (tdata[1] == "ismy") {
              if ($("#work_type").val() == "share") {
                alert("본인에게 업무공유을 할 수 없습니다.");
                return false;
              } else {
                if ($("#write_tab_03").hasClass("on") == true) {
                  alert("본인에게 보고를 할 수 없습니다.");
                  return false;
                } else {
                  alert("본인에게 업무요청을 할 수 없습니다.");
                  return false;
                }
              }
            } else {
              if ($("#work_type").val() == "share") {
                todayworks_share();
              } else {
                if (user_chk_val) {
                  arr_val = user_chk_val.split(",");
                  if (arr_val.length > 1) {
                    var user_chk = arr_val.length - 1;
                    $(".title_desc_01").text(
                      tdata[1] + " 외 " + user_chk + "명 선택"
                    );
                  } else {
                    $(".title_desc_01").text(tdata[1] + " 선택");
                  }

                  if ($(".layer_user_search_box input").val()) {
                    $(".layer_user_search_box input").val("");
                  }

                  //console.log("user_chk_val :: " + user_chk_val);

                  layer_todayworks_list();
                  $(".layer_user").hide();
                }
              }
            }
          }
        }
      },
    });
  }
}

//업무공유하기
function todayworks_share() {
  if (confirm("업무를 공유합니다.\n계속 진행하시겠습니까?")) {
    var fdata = new FormData();
    var user_chk_val = $("#chall_user_chk").val();
    fdata.append("work_user_chk", user_chk_val);
    fdata.append("work_idx", $("#work_idx").val());
    fdata.append("mode", "works_share");
    $.ajax({
      type: "POST",
      data: fdata,
      contentType: false,
      processData: false,
      url: "/inc/works_process.php",
      success: function (data) {
        console.log(data);
        if (data == "complete") {
          works_list();
        } else {
          alert("업무공유가 되지 않았습니다.");
          return false;
        }
      },
    });

    if ($(".layer_user_search_box input").val()) {
      $(".layer_user_search_box input").val("");
    }
    $("#chall_user_chk").val("");
    $("#select_user_cnt").text("");
    $(".layer_user_info dd button").removeClass("on");
    $(".layer_user").hide();
    $(".layer_user_cancel").trigger("click");
  } else {
    //취소하기
    $(".layer_user_cancel").trigger("click");
  }
}

//업무 받을사람 리스트
function layer_todayworks_list() {
  var input_val = $(".layer_user_search_box input").val();
  var fdata = new FormData();
  var arr_val = new Array();
  var userchk = 0;

  fdata.append("mode", "chall_user_list");
  fdata.append("layer_user_list", "1");
  fdata.append("user_chk_val", $("#chall_user_chk").val());

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
      //console.log(data);
      var tdata = data.split("|");
      if (tdata) {
        var html = tdata[0];
        var totcnt = tdata[1].trim();

        if (totcnt > 0) {
          $("#chall_user_cnt").val(totcnt);
          $(".layer_user_info").empty();

          if (userchk > 0) {
            $("#usercnt").text("전체 " + totcnt + "명, " + userchk + "명 선택");
          } else {
            $("#usercnt").text("전체 " + totcnt + "명");
          }
          $(".layer_user_info").html(html);

          //if(GetCookie("user_id") == 'sadary0@nate.com'){
          user_check_desc();
          //}
        } else {
          $("#chall_user_chk").val("");
          $("#chall_user_cnt").val(totcnt);
          $(".layer_user_info").empty();
          $("#usercnt").text("전체 " + totcnt + "명");
          $(".layer_user_info").html(html);
        }
      }
    },
  });
}

//사용자 선택
function user_check_desc() {
  var user_chk_val = $("#chall_user_chk").val();
  var fdata = new FormData();
  fdata.append("mode", "user_check_desc");
  fdata.append("user_chk_val", user_chk_val);

  $.ajax({
    type: "POST",
    data: fdata,
    async: false,
    contentType: false,
    processData: false,
    url: "/inc/works_process.php",
    success: function (data) {
      //console.log(data);

      $("#tdw_write_user_desc").show();
      $("#tdw_write_user_desc ul").html(data);
    },
  });
}

//일일, 주간, 월간별로 날짜변경
function date_change() {
  var fdata = new FormData();
  var wdate = $("#work_date").val();
  var work_type = "all";
  if ($(".select_dd").hasClass("on") == true) {
    var day_type = "day";
    var work_wdate = $("#work_wdate").val();
  } else if ($(".select_ww").hasClass("on") == true) {
    var day_type = "week";
    var work_wdate = $("#work_wdate").val();
  } else if ($(".select_mm").hasClass("on") == true) {
    var day_type = "month";
    var work_wdate = $("#work_month").val();
  }

  if ($(".all").hasClass("on") == true) {
    var work_type = "all";
  } else if ($(".report").hasClass("on") == true) {
    var work_type = "report";
  } else if ($(".req").hasClass("on") == true) {
    var work_type = "req";
  } else if ($(".user").hasClass("on") == true) {
    var work_type = "user";
  } 
  fdata.append("mode", "date_change");
  fdata.append("wdate", wdate);
  fdata.append("work_wdate", work_wdate);
  fdata.append("day_type", day_type);
  fdata.append("work_type", work_type);
  $.ajax({
    type: "POST",
    data: fdata,
    async: false,
    contentType: false,
    processData: false,
    url: "/inc/works_process.php",
    success: function (data) {
      //console.log(data);
      if (data) {
        $("#work_date").val(data);
        return false;
      }
    },
  });
}


//일정변경(달력에서 일자 변경시 적용됨)
function works_datechange(id, v) {
  if (id && v) {
    var idx = id.replace("listdate_", "");
    var fdata = new FormData();
    fdata.append("mode", "works_date_change");
    fdata.append("idx", idx);
    fdata.append("wdate", v);
    //console.log(" v :: " + v);
    //console.log(" todaydate :: " + get_today());
    var date1 = new Date(v);
    var date2 = new Date(get_today());

    if (date1 < date2) {
      //   alert("해당 업무를 오늘 날짜보다 이전 날짜로 이동할 수 없습니다.");
      //    works_list();
      //    return false;
    }

    //if(confirm("해당 업무를 "+ +" 미루시겠습니까?")){

    $.ajax({
      type: "POST",
      data: fdata,
      async: false,
      contentType: false,
      processData: false,
      url: "/inc/works_process.php",
      success: function (data) {
        console.log(" >>>>>>  " + data);
        if (data == "complete") {
          works_list();
          return false;
        } else if (data == "req_work") {
          alert("요청받은 업무는 요청사람만 이동 가능합니다.");
          return false;
        } else if (data == "prev_not") {
          alert("해당 업무를 오늘 날짜보다 이전 날짜로 이동할 수 없습니다.");
          works_list();
          return false;
        }
      },
    });
    //}
  }
}

//배열 중복정리하여 해당 갯수 리턴
function str_over_filter(str) {
  var arr_str = str.filter(function (item, index) {
    return str.indexOf(item) === index;
  });

  return arr_str;
}

function get_today() {
  var today = new Date();
  var dd = String(today.getDate()).padStart(2, "0");
  var mm = String(today.getMonth() + 1).padStart(2, "0");
  var yyyy = today.getFullYear();

  var result = yyyy + "-" + mm + "-" + dd;
  return result;
}

function noBefore(date) {
  if (date < new Date()) return [false];
  return [true];
}

function fileadd(e) {
  var input = this;
  //var id = file_obj.parent().parent().find(".tdw_list_share").attr("id");
  //var no = id.replace("tdw_file_add_", "");
  //var id = $(this).parent().parent().find(".tdw_list_share").attr("id");
  var id = $(this).attr("id");
  var no = id.replace("files_add_", "");

  //console.log("no :: " + no);
  //console.log(input.files[0]);

  var file_name = input.files[0]["name"];
  var file_size = input.files[0]["size"];
  var ext = file_name.split(".").pop().toLowerCase();
  var maxSize = 100 * 1024 * 1024;
  var fileSize = file_size;
  //용량제한
  if (fileSize > maxSize) {
    alert("첨부파일 사이즈는 100MB 이내로 등록 가능합니다.");
    return false;
  }

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
    "lo"
  );
  if ($.inArray(ext, format_ext) > 0) {
    alert("첨부할 수 없는 파일입니다.\n파일명 : " + file_name + "");
    return false;
  } else {
    //fileListArr.push(file_obj[0]);
    //works_list();
    //$(".tdw_write_area_in #tdw_write_text").after('<div class="tdw_write_file_desc" id="tdw_write_file_desc"><span>' + file_name + '</span><button id="work_file_del">삭제</button></div>');

    var fdata = new FormData();

    var work_date = $("#work_date").val();
    fdata.append("files[]", input.files[0]);
    fdata.append("work_idx", no);
    fdata.append("mode", "works_files_add");
    fdata.append("work_date", work_date);

    //$(this).val($("#files_add")[0].files[0]);
    //$("#files_add").val($("#files_add")[0].files[0]);
    //console.log("vvv " + $(this).val());

    $.ajax({
      type: "POST",
      data: fdata,
      contentType: false,
      processData: false,
      url: "/inc/works_process.php",
      success: function (data) {
        console.log(data);
        if (data == "complete") {
          //$(this).val("");
          //$("#files_add").replaceWith($("#files_add").clone(true));
          //$("#files_add").remove();
          works_list();
          //$("#files_add").val("");
        } else if (data == "failed") {
          alert("첨부파일 등록에 실패 하였습니다.\n\n파일을 확인 해주세요.");
          return false;
        }
      },
    });
  }
}

//시간 함수
function timeToMinutes(timeString) {
  var timeArray = timeString.split(':');
  var hours = parseInt(timeArray[0], 10);
  var minutes = parseInt(timeArray[1], 10);
  return hours * 60 + minutes;
}

//메모 3줄이상일때 메모 접기/펼치기
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

// $(document).on("click",".tdw_list_li",function(){
//   content = $(this).children().find(".tdw_list_desc").height();
//   console.log(content);
// });

//memo_line_check();
// 2차수정
