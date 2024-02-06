$(function () {
  type = $("#backoffice_type").val();
  service_arr = [
    "backwork_list",
    "backlike_list",
    "backcoin_list",
    "backparty_list",
    "backchall_list",
    "backchall_user",
    "backalarm_list",
  ];
  log_arr = [
    "backlog_list",
    "backcomm_list",
    "backin_list",
    "backpenalty_list",
    "backcp_list",
  ];
  info_arr = ["backuser_list", "backcomp_list", "backtuto_list"];
  bro_arr = ["backnote_list","backfaq_list","backsample_list","backmanual_list","backemail_list"];

  var side = $("#sidebarOpen").val();
  if (side == "open") {
    if ($.inArray(type, service_arr) !== -1) {
      console.log("sideOpen");
      $("#collapseLayouts").addClass("show");
    } else if ($.inArray(type, log_arr) !== -1) {
      console.log("sideOpen");
      $("#collapseLayoutsLog").addClass("show");
    } else if ($.inArray(type, info_arr) !== -1) {
      console.log("sideOpen");
      $("#collapseInfo").addClass("show");
    } else if ($.inArray(type, bro_arr) !== -1) {
      console.log("sideOpen");
      $("#collapseBro").addClass("show");
    } else if ($("#total_type").val() || type == "backerror_log") {
      console.log("sideOpen");
      $("#collapsePages").addClass("show");
    }
  } else {
    $(".collapse").removeClass("show");
  }

  textDefult();
  // 페이지 로딩 후 바로 실행
  window.onbeforeunload = function () {
    $(".rewardy_loading_01").css("display", "block");
  };

  

  $(window).load(function () {
    // 에디터 기본 길이 700px로 설정
    editor = $("div[class*='cke_contents']");
    if(editor){
      editor.css("height","700px");    
    }
    //페이지가 로드 되면 로딩 화면을 없애주는 것
    $(".rewardy_loading_01").css("display", "none");
  });

  $("#backcoin_sdate").datepicker({ dateFormat: "yyyy-mm-dd" });
  $("#backcoin_edate").datepicker({ dateFormat: "yyyy-mm-dd" });

  $(".tdw_ins_tab_in ul li button").click(function () {
    $(".tdw_ins_tab_in ul li button").removeClass("on");
    $(this).addClass("on");

    if ($(".select_c_dd").hasClass("on") == true) {
      $(".rew_ins_c_dd").show();
      $(".rew_ins_c_ww").hide();
      $(".rew_ins_c_mm").hide();
      $("#r_work_date").width("110px");

      if ($("#r_work_month").is(":visible") == true) {
        $("#r_work_month").hide();
        $("#r_work_date").show();
      }
    } else if ($(".select_c_ww").hasClass("on") == true) {
      $(".rew_ins_c_ww").show();
      $(".rew_ins_c_dd").hide();
      $(".rew_ins_c_mm").hide();
      $("#r_work_date").width("220px");

      if ($("#r_work_month").is(":visible") == true) {
        $("#r_work_month").hide();
        $("#r_work_date").show();
      }
    } else if ($(".select_c_mm").length) {
      $(".rew_ins_c_mm").show();
      $(".rew_ins_c_dd").hide();
      $(".rew_ins_c_ww").hide();
      $("#r_work_month").width("86px");

      if ($("#r_work_month").is(":visible") == false) {
        $("#r_work_month").show();
        $("#r_work_date").hide();
      }
    } else if ($(".select_l_dd").hasClass("on") == true) {
      $(".rew_ins_l_dd").show();
      $(".rew_ins_l_mm").hide();
      $(".rew_ins_l_ww").hide();
      $("#r_work_date").width("110px");

      if ($("#r_work_month").is(":visible") == true) {
        $("#r_work_month").hide();
        $("#r_work_date").show();
      }
    } else if ($(".select_l_ww").hasClass("on") == true) {
      $(".rew_ins_l_ww").show();
      $(".rew_ins_l_dd").hide();
      $(".rew_ins_l_mm").hide();
      $("#r_work_date").width("220px");

      if ($("#r_work_month").is(":visible") == true) {
        $("#r_work_month").hide();
        $("#r_work_date").show();
      }
    } else if ($(".select_l_mm").hasClass("on") == true) {
      $(".rew_ins_l_mm").show();
      $(".rew_ins_l_dd").hide();
      $(".rew_ins_l_ww").hide();
      $("#r_work_month").width("86px");

      if ($("#r_work_month").is(":visible") == false) {
        $("#r_work_month").show();
        $("#r_work_date").hide();
      }
    } else if ($(".select_p_dd").hasClass("on") == true) {
      $(".rew_ins_p_dd").show();
      $(".rew_ins_p_mm").hide();
      $(".rew_ins_p_ww").hide();
      $("#r_work_date").width("110px");

      if ($("#r_work_month").is(":visible") == true) {
        $("#r_work_month").hide();
        $("#r_work_date").show();
      }
    } else if ($(".select_p_ww").hasClass("on") == true) {
      $(".rew_ins_p_ww").show();
      $(".rew_ins_p_dd").hide();
      $(".rew_ins_p_mm").hide();
      $("#r_work_date").width("220px");

      if ($("#r_work_month").is(":visible") == true) {
        $("#r_work_month").hide();
        $("#r_work_date").show();
      }
    } else if ($(".select_p_mm").hasClass("on") == true) {
      $(".rew_ins_p_mm").show();
      $(".rew_ins_p_dd").hide();
      $(".rew_ins_p_ww").hide();
      $("#r_work_month").width("86px");

      if ($("#r_work_month").is(":visible") == false) {
        $("#r_work_month").show();
        $("#r_work_date").hide();
      }
    }
    r_date_change();
    rank_list();
  });
  $(".rew_conts_list_in ul").sortable({
    axis: "y",
    opacity: 0.7,
    zIndex: 9999,
    //placeholder:"sort_empty",
    cursor: "move",
  });
  $(".rew_conts_list_in ul").disableSelection();

  $("#template_list").sortable({
    opacity: 0.7,
    zIndex: 9999,
    //placeholder:"sort_empty",
    cursor: "move",
    containment: "parent",
  });
  $("#template_list").disableSelection();

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
    $(".rew_box").addClass("on");
  }, 400);

  /*$(".rew_menu_onoff button").click(function(){
        var thisonoff = $(this);
        if(thisonoff.hasClass("on")){
            thisonoff.removeClass("on");
            $(".rew_box").removeClass("on");

        }else{
            thisonoff.addClass("on");
            $(".rew_box").addClass("on");

        }
    });*/

  $(".rew_conts_scroll_04").scroll(function () {
    var rct = $(".rew_cha_list_in").offset().top;
    console.log(rct);
    if (rct < 216) {
      $(".rew_cha_list_func").addClass("pos_fix");
    } else {
      $(".rew_cha_list_func").removeClass("pos_fix");
    }
  });

  $(".rew_cha_list_ul li").each(function () {
    var tis = $(this);
    var tindex = $(this).index();
    setTimeout(function () {
      tis.addClass("sli");
    }, 700 + tindex * 150);
  });

  $(".rew_cha_tab_sort .btn_sort_on").click(function () {
    $(".rew_cha_tab_sort").addClass("on");
  });
  $(".rew_cha_tab_sort").mouseleave(function () {
    $(".rew_cha_tab_sort").removeClass("on");
  });
  $(".rew_cha_tab_sort ul li button").click(function () {
    $(".rew_cha_tab_sort").removeClass("on");
  });

  $(".rew_cha_sort .btn_sort_on").click(function () {
    $(".rew_cha_sort").addClass("on");
  });
  $(".rew_cha_sort").mouseleave(function () {
    $(".rew_cha_sort").removeClass("on");
  });
  $(".rew_cha_sort ul li button").click(function () {
    $(".rew_cha_sort").removeClass("on");
  });

  $(".rew_cha_more button").click(function () {
    $(".rew_cha_list_ul").append(
      '<li class="sli2 category_06 offset0"><a href="./0006.html"><div class="cha_box"><div class="cha_box_t"><span class="cha_cate">기타</span><span class="cha_title">하루에 칭찬 3번을 했을 뿐인데 </span><span class="cha_coin"><strong>1,000</strong>코인</span></div><div class="cha_box_b"><span class="cha_member">18/20 명 도전중</span><span class="cha_dday">D - 7</span></div></div></a></li><li class="sli2 category_01"><a href="./0006.html"><div class="cha_box"><div class="cha_box_t"><span class="cha_cate">업무</span><span class="cha_title">윈도우 업데이트 점검한다면</span><span class="cha_coin"><strong>500</strong>코인</span></div><div class="cha_box_b"><span class="cha_member">12/20 명 도전중</span><span class="cha_dday">D - 20</span></div></div></a></li><li class="sli2 category_02"><a href="./0006.html"><div class="cha_box"><div class="cha_box_t"><span class="cha_cate">생활</span><span class="cha_title">책 읽고 독서메모를 남긴다면</span><span class="cha_coin"><strong>1,500</strong>코인</span></div><div class="cha_box_b"><span class="cha_member">7/20 명 도전중</span><span class="cha_dday">D - 10</span></div></div></a></li><li class="sli2 category_05"><a href="./0006.html"><div class="cha_box"><div class="cha_box_t"><span class="cha_cate">신입사원</span><span class="cha_title">보고서 작성법을 배우면</span><span class="cha_coin"><strong>1,000</strong>코인</span></div><div class="cha_box_b"><span class="cha_member">1/1 명 도전중</span><span class="cha_dday">D - 30</span></div></div></a></li><li class="sli2 category_02"><a href="./0006.html"><div class="cha_box"><div class="cha_box_t"><span class="cha_cate">생활</span><span class="cha_title">캔크러시 챌린지, 그저 밟기만 했을 뿐인데</span><span class="cha_coin"><strong>500</strong>코인</span></div><div class="cha_box_b"><span class="cha_member">12/20 명 도전중</span><span class="cha_dday">D - 60</span></div></div></a></li><li class="sli2 category_05"><a href="./0006.html"><div class="cha_box"><div class="cha_box_t"><span class="cha_cate">신입사원</span><span class="cha_title">비즈니스 명함 예절을 배우면</span><span class="cha_coin"><strong>10,000</strong>코인</span></div><div class="cha_box_b"><span class="cha_member">1/1 명 도전중</span><span class="cha_dday">D - 30</span></div></div></a></li><li class="sli2 category_06"><a href="./0006.html"><div class="cha_box"><div class="cha_box_t"><span class="cha_cate">기타</span><span class="cha_title">하루에 칭찬 3번을 했을 뿐인데 </span><span class="cha_coin"><strong>1,000</strong>코인</span></div><div class="cha_box_b"><span class="cha_member">18/20 명 도전중</span><span class="cha_dday">D - 7</span></div></div></a></li><li class="sli2 category_01"><a href="./0006.html"><div class="cha_box"><div class="cha_box_t"><span class="cha_cate">업무</span><span class="cha_title">윈도우 업데이트 점검한다면</span><span class="cha_coin"><strong>500</strong>코인</span></div><div class="cha_box_b"><span class="cha_member">12/20 명 도전중</span><span class="cha_dday">D - 20</span></div></div></a></li><li class="sli2 category_02"><a href="./0006.html"><div class="cha_box"><div class="cha_box_t"><span class="cha_cate">생활</span><span class="cha_title">책 읽고 독서메모를 남긴다면</span><span class="cha_coin"><strong>1,500</strong>코인</span></div><div class="cha_box_b"><span class="cha_member">7/20 명 도전중</span><span class="cha_dday">D - 10</span></div></div></a></li><li class="sli2 category_05"><a href="./0006.html"><div class="cha_box"><div class="cha_box_t"><span class="cha_cate">신입사원</span><span class="cha_title">보고서 작성법을 배우면</span><span class="cha_coin"><strong>1,000</strong>코인</span></div><div class="cha_box_b"><span class="cha_member">1/1 명 도전중</span><span class="cha_dday">D - 30</span></div></div></a></li><li class="sli2 category_02"><a href="./0006.html"><div class="cha_box"><div class="cha_box_t"><span class="cha_cate">생활</span><span class="cha_title">캔크러시 챌린지, 그저 밟기만 했을 뿐인데</span><span class="cha_coin"><strong>500</strong>코인</span></div><div class="cha_box_b"><span class="cha_member">12/20 명 도전중</span><span class="cha_dday">D - 60</span></div></div></a></li><li class="sli2 category_05"><a href="./0006.html"><div class="cha_box"><div class="cha_box_t"><span class="cha_cate">신입사원</span><span class="cha_title">비즈니스 명함 예절을 배우면</span><span class="cha_coin"><strong>10,000</strong>코인</span></div><div class="cha_box_b"><span class="cha_member">1/1 명 도전중</span><span class="cha_dday">D - 30</span></div></div></a></li>'
    );
    setTimeout(function () {
      var offset = $(".offset0").position();
      $(".rew_conts_scroll_04").animate({ scrollTop: offset.top - 5 }, 700);
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
  });

  $(".cha_jjim").click(function () {
    $(this).toggleClass("on");
  });

  $(".tpl_list_switch .btn_switch").click(function () {
    $(this).toggleClass("on");
  });

  $(".tpl_list_area ul").sortable({
    axis: "y",
    opacity: 0.7,
    zIndex: 9999,
    handle: ".tpl_list_drag",
    //placeholder:"sort_empty",
    cursor: "move",
    items: "li:not(.ui-state-disabled)",
  });

  //그래프 높이
  var irg_t_coin = $(".ins_rank_graph .ir_rank_4 .ir_bar_coin span").text();
  var irg_t_heart = $(".ins_rank_graph .ir_rank_4 .ir_bar_heart span").text();
  var irg_t_power = $(".ins_rank_graph .ir_rank_4 .ir_bar_power span").text();
  var irg_t = irg_t_coin + irg_t_heart + irg_t_power;
  var irg_h = 160;
  var irg_no = /[\{\}\[\]\/?.,;:|\)*~`!^\-_+<>@\#$%&\\\=\(\'\"]/gi;
  var irg_t = irg_t.replace(irg_no, "");
  $(".ins_rank_graph li").each(function () {
    var irg_count_coin = $(this)
      .find(".ir_bar_coin span")
      .text()
      .replace(irg_no, "");
    var irg_count_heart = $(this)
      .find(".ir_bar_heart span")
      .text()
      .replace(irg_no, "");
    var irg_count_power = $(this)
      .find(".ir_bar_power span")
      .text()
      .replace(irg_no, "");
    var irg_count = irg_count_coin + irg_count_heart + irg_count_power;
    var irg_height = (irg_h * irg_count) / irg_t;
    $(this).find(".ir_bar_graph").css({ height: irg_height });
  });

  $(document).on("change", "input[type=checkbox]", function () {
    console.log("test");
    var idx = $(this).val();
    var fdata = new FormData();
    var id = $(this).attr("id");
    stext = id.split("_");

    fdata.append("idx", idx);
    fdata.append("mode", "auth_plus");

    if ($(this).is(":checked")) {
      console.log("check");
      if (stext[0] == "all") {
        console.log(stext[1]);
        $("input[id*=_" + stext[1] + "]").prop("checked", true);
      }
      fdata.append("authcode", "1");
    } else {
      console.log("uncheck");
      if (stext[0] == "all") {
        console.log(stext[1]);
        $("input[id*=_" + stext[1] + "]").removeAttr("checked");
      }
      fdata.append("authcode", "0");
    }

    if (id.indexOf("chall") !== -1) {
      fdata.append("auth", "chall_auth");
    } else if (id.indexOf("admin") !== -1) {
      fdata.append("auth", "admin_auth");
    } else if (id.indexOf("coin") !== -1) {
      fdata.append("auth", "coin_auth");
    } else if (id.indexOf("all") !== -1) {
      fdata.append("auth", "all_auth");
    }

    $.ajax({
      type: "post",
      async: false,
      data: fdata,
      contentType: false,
      processData: false,
      url: "/inc/backoff_process.php",
      success: function (data) {
        console.log("success");
      },
    });
  });

  $(document).on("click", ".collapse a", function () {
    if ($(".collapse").hasClass("show")) {
      setCookie("sideopen", "open", 1);
    } else {
      setCookie("sideopen", "close", 1);
    }
  });
});

function setCookie(cookieName, cookieValue, expirationDays) {
  const date = new Date();
  date.setTime(date.getTime() + expirationDays * 24 * 60 * 60 * 1000);
  const expires = "expires=" + date.toUTCString();
  document.cookie = cookieName + "=" + cookieValue + ";" + expires + ";path=/";
}

//순위날짜 이동
function rank_list() {
  var mode = "rank_list";

  var fdata = new FormData();
  if ($(".select_c_dd").hasClass("on") == true) {
    var rank_type = "c_day";
    var wdate = $("#r_work_date").val();
    fdata.append("rank_wdate", $("#work_wdate").val());
  } else if ($(".select_c_ww").hasClass("on") == true) {
    var rank_type = "c_week";
    var wdate = $("#r_work_date").val();
    fdata.append("rank_wdate", $("#work_wdate").val());
  } else if ($(".select_c_mm").length) {
    var rank_type = "c_month";
    var wdate = $("#r_work_month").val();
    fdata.append("rank_wdate", $("#work_month").val());
  } else if ($(".select_l_dd").hasClass("on") == true) {
    var rank_type = "l_day";
    var wdate = $("#r_work_date").val();
    fdata.append("rank_wdate", $("#work_wdate").val());
  } else if ($(".select_l_ww").hasClass("on") == true) {
    var rank_type = "l_week";
    var wdate = $("#r_work_date").val();
    fdata.append("rank_wdate", $("#work_wdate").val());
  } else if ($(".select_l_mm").hasClass("on") == true) {
    var rank_type = "l_month";
    var wdate = $("#r_work_month").val();
    fdata.append("rank_wdate", $("#work_month").val());
  } else if ($(".select_p_dd").hasClass("on") == true) {
    var rank_type = "p_day";
    var wdate = $("#r_work_date").val();
    fdata.append("rank_wdate", $("#work_month").val());
  } else if ($(".select_p_ww").hasClass("on") == true) {
    var rank_type = "p_week";
    var wdate = $("#r_work_date").val();
    fdata.append("rank_wdate", $("#work_month").val());
  } else if ($(".select_p_mm").hasClass("on") == true) {
    var rank_type = "p_month";
    var wdate = $("#r_work_month").val();
    fdata.append("rank_wdate", $("#work_month").val());
  }

  fdata.append("wdate", wdate);
  fdata.append("mode", mode);
  fdata.append("rank_type", rank_type);

  $.ajax({
    type: "POST",
    data: fdata,
    async: false,
    contentType: false,
    processData: false,
    url: "/inc/insight_process.php",
    success: function (data) {
      if (data) {
        if (rank_type == "c_day") {
          $(".rew_ins_c_dd").html(data);
          $(".rew_ins_c_ww").html("");
          $(".rew_ins_c_mm").html("");
        } else if (rank_type == "c_week") {
          $(".rew_ins_c_ww").html(data);
          $(".rew_ins_c_dd").html("");
          $(".rew_ins_c_mm").html("");
        } else if (rank_type == "c_month") {
          $(".rew_ins_c_mm").html(data);
          $(".rew_ins_c_dd").html("");
          $(".rew_ins_c_ww").html("");
        } else if (rank_type == "l_day") {
          $(".rew_ins_l_dd").html(data);
          $(".rew_ins_l_ww").html("");
          $(".rew_ins_l_mm").html("");
        } else if (rank_type == "l_week") {
          $(".rew_ins_l_ww").html(data);
          $(".rew_ins_l_dd").html("");
          $(".rew_ins_l_mm").html("");
        } else if (rank_type == "l_month") {
          $(".rew_ins_l_mm").html(data);
          $(".rew_ins_l_dd").html("");
          $(".rew_ins_l_ww").html("");
        } else if (rank_type == "p_day") {
          $(".rew_ins_p_dd").html(data);
          $(".rew_ins_p_mm").html("");
          $(".rew_ins_p_ww").html("");
        } else if (rank_type == "p_week") {
          $(".rew_ins_p_ww").html(data);
          $(".rew_ins_p_dd").html("");
          $(".rew_ins_p_mm").html("");
        } else if (rank_type == "p_month") {
          $(".rew_ins_p_mm").html(data);
          $(".rew_ins_p_dd").html("");
          $(".rew_ins_p_ww").html("");
        }
        // $(document).find("input[id^=listdate]").removeClass('hasDatepicker').datepicker();
        graph_h();
      }
    },
  });
}

function graph_h() {
  //그래프 높이
  var irg_t_coin = $(".ins_rank_graph .ir_rank_4 .ir_bar_coin span").text();
  var irg_t_heart = $(".ins_rank_graph .ir_rank_4 .ir_bar_heart span").text();
  var irg_t_power = $(".ins_rank_graph .ir_rank_4 .ir_bar_power span").text();
  var irg_t = irg_t_coin + irg_t_heart + irg_t_power;
  var irg_h = 160;
  var irg_no = /[\{\}\[\]\/?.,;:|\)*~`!^\-_+<>@\#$%&\\\=\(\'\"]/gi;
  var irg_t = irg_t.replace(irg_no, "");
  $(".ins_rank_graph li").each(function () {
    var irg_count_coin = $(this)
      .find(".ir_bar_coin span")
      .text()
      .replace(irg_no, "");
    var irg_count_heart = $(this)
      .find(".ir_bar_heart span")
      .text()
      .replace(irg_no, "");
    var irg_count_power = $(this)
      .find(".ir_bar_power span")
      .text()
      .replace(irg_no, "");
    var irg_count = irg_count_coin + irg_count_heart + irg_count_power;
    var irg_height = (irg_h * irg_count) / irg_t;
    $(this).find(".ir_bar_graph").animate({ height: irg_height });
  });
}

function barChartGraph() {
  var daylen = $("input[class^=bar_today]").length;

  var type = $("#total_type").val();
  if (type == "like_total") {
    label_name = "좋아요";
  } else if (type == "work_total") {
    label_name = "등록업무";
  }

  $("#monthBarChart").remove();
  $("#myBarChart").remove();
  $("#work_graph").append(
    '<canvas id="myBarChart" width="100%" height="50"></canvas>'
  );

  var ctx = document.getElementById("myBarChart");

  day = [];
  day_cnt = [];
  for (var i = daylen - 1; i >= 0; i--) {
    day.push($(".bar_today_" + i).val());
    day_cnt.push($(".bar_cnt_" + i).val());
  }

  var myLineChart = new Chart(ctx, {
    type: "bar",
    data: {
      labels: day,
      datasets: [
        {
          borderWidth: 1,
          radius: 0,
          label: label_name,
          backgroundColor: "#38C9D2",
          borderColor: "#38C9D2",
          data: day_cnt,
        },
      ],
    },
    options: {
      scales: {
        xAxes: [
          {
            time: {
              unit: "day",
            },
            gridLines: {
              display: false,
            },
            ticks: {
              maxTicksLimit: daylen,
            },
          },
        ],
        yAxes: [
          {
            ticks: {
              min: 0,
              max: 300,
              maxTicksLimit: 5,
            },
            gridLines: {
              display: true,
            },
          },
        ],
      },
      legend: {
        display: false,
      },
    },
  });
}

function barMonthGraph() {
  $("#myBarChart").remove();
  $("#work_graph").append(
    '<canvas id="monthBarChart" width="100%" height="50"></canvas>'
  );

  var ctx = document.getElementById("monthBarChart");
  var daylen = $("input[class^=bar_month]").length;
  day = [];
  day_cnt = [];
  for (var i = daylen - 1; i >= 0; i--) {
    day.push($(".bar_month_" + i).val());
    day_cnt.push($(".bar_cnt_month_" + i).val());
  }

  var myLineChart = new Chart(ctx, {
    type: "bar",
    data: {
      labels: day,
      datasets: [
        {
          borderWidth: 1,
          radius: 0,
          label: "등록업무",
          backgroundColor: "#38C9D2",
          borderColor: "#38C9D2",
          data: day_cnt,
        },
      ],
    },
    options: {
      scales: {
        xAxes: [
          {
            time: {
              unit: "month",
            },
            gridLines: {
              display: false,
            },
            ticks: {
              maxTicksLimit: daylen,
            },
          },
        ],
        yAxes: [
          {
            ticks: {
              min: 0,
              max: 6000,
              maxTicksLimit: 5,
            },
            gridLines: {
              display: true,
            },
          },
        ],
      },
      legend: {
        display: false,
      },
    },
  });
}

function doughnutGraph() {
  $("#myPieChart").remove();
  $("#work_doughnut").append(
    '<canvas id="myPieChart" width="100%" height="50"></canvas>'
  );
  var ctx = document.getElementById("myPieChart");
  var pielength = $("#work_doughnut").children("input[id^=pie]").length;

  day = [];
  day_cnt = [];
  for (var i = 0; i < pielength; i++) {
    day.push($("#pie_title_" + i).val());
    day_cnt.push($("#pie_value_" + i).val());
  }

  var myPieChart = new Chart(ctx, {
    type: "doughnut",
    data: {
      labels: day,
      datasets: [
        {
          borderWidth: 1,
          radius: 0,
          data: day_cnt,
          backgroundColor: [
            "#007bff",
            "#dc3545",
            "#ffc107",
            "#28a745",
            "#11ed44",
            "#4cf5ef",
          ],
        },
      ],
    },
  });
}
// 백오피스 작업 - 템플릿 20230824

// 일별 조회
$(document).on("click", "#coin_day", function () {
  location.href = "../choco/coin_day.php";
  return false;
});

// 캘린더 사용
$(document).on(
  "click",
  ".rew_member_sub_func_period #btn_history",
  function () {
    $(".member_list_header_in > div button").removeClass("on");
    var backcoin_sdate = $("#backcoin_sdate").val();
    var backcoin_edate = $("#backcoin_edate").val();

    // $("#reward_inquiry").val(true);

    var fdata = new FormData();

    var code = $("#code").val();
    fdata.append("code", code);

    var list_cnt = $("#list_cnt").val();
    fdata.append("list", list_cnt);

    var tclass = $("#tclass").val();
    fdata.append("tclass", tclass);

    var type = $("#backoffice_type").val();
    // console.log(type+"|"+backcoin_edate+"|"+backcoin_sdate);

    // return false;
    fdata.append("mode", type);
    fdata.append("sdate", backcoin_sdate);
    fdata.append("edate", backcoin_edate);
    fdata.append("p", "1");
    $.ajax({
      type: "post",
      async: false,
      data: fdata,
      contentType: false,
      processData: false,
      url: "/inc/backoff_process.php",
      success: function (data) {
        // console.log(data);
        var tdata = data.split("|");
        list = tdata[3];
        console.log(tdata[2]);
        if (list == "backwork") {
          $("#backoff_table").html(tdata[0]);
          $("#back_pagelist").html(tdata[1]);
        } else {
          $("#backoff_table tbody").html(tdata[0]);
          $("#back_pagelist").html(tdata[1]);
        }
      },
    });
  }
);

//캘린더 초기화
$(document).on(
  "click",
  ".rew_member_sub_func_period #cal_history",
  function () {
    var fdata = new FormData();
    fdata.append("reset", "re");

    var type = $("#backoffice_type").val();
    fdata.append("mode", type);

    var list_cnt = $("#list_cnt").val();
    fdata.append("list", list_cnt);

    var code = $("#code").val();
    fdata.append("code", code);

    $.ajax({
      type: "post",
      async: false,
      data: fdata,
      contentType: false,
      processData: false,
      url: "/inc/backoff_process.php",
      success: function (data) {
        var tdata = data.split("|");
        list = tdata[3];
        if (list == "backwork") {
          $("#backoff_table").html(tdata[0]);
          $("#back_pagelist").html(tdata[1]);
        } else {
          $("#backoff_table tbody").html(tdata[0]);
          $("#back_pagelist").html(tdata[1]);
        }
        $("#backcoin_sdate").val(tdata[4]);
        $("#backcoin_edate").val(tdata[5]);
      },
    });
  }
);

$(document).on("click", "#btn_calendar_l", function () {
  $("#backcoin_sdate").focus();
  $("#backcoin_sdate").datepicker({ dateFormat: "yyyy-mm-dd" });
});

//종료일
$(document).on("click", "#btn_calendar_r", function () {
  $("#backcoin_edate").focus();
  $("#backcoin_edate").datepicker({ dateFormat: "yyyy-mm-dd" });
});

$(document).on("click", "#backcoin_sdate", function () {
  $("#backcoin_sdate").datepicker({ dateFormat: "yyyy-mm-dd" });
});

$(document).on("click", "#backcoin_edate", function () {
  $("#backcoin_edate").datepicker({ dateFormat: "yyyy-mm-dd" });
});

// 검색기능
$(document).on("click", "#backcoin_search_btn", function () {
  backoffice_ajax_list();
});

// 검색창 엔터키 처리
$(document).on("keydown", "#backcoin_search", function (e) {
  console.log(e.keyCode);
  var input_val = $("#backcoin_search").val();

  if (e.keyCode == 13) {
    console.log("keypress");
    $("#backcoin_search_btn").trigger("click");
    return false;
  }

  if (input_val == "") {
    backoffice_ajax_list();
    return false;
  }
});

function backoffice_ajax_list() {
  var type = $("#backoffice_type").val();
  var search = $("#backcoin_search").val();

  var sdate = $("#backoff_sdate").val();
  var edate = $("#backoff_edate").val();

  var fdata = new FormData();

  var code = $("#code").val();
  if (code) {
    fdata.append("code", code);
  }

  if (sdate && edate) {
    fdata.append("sdate", sdate);
    fdata.append("edate", edate);
  }

  var user = $("#user_kind").val();
  console.log(user);
  if (user) {
    fdata.append("user", user);
  }

  var list_cnt = $("#list_cnt").val();
  if (list_cnt) {
    fdata.append("list", list_cnt);
  }
  // var p = $("#page_num").val();
  fdata.append("mode", type);
  if (search) {
    fdata.append("search", search);
  }
  fdata.append("p", "1");
  $.ajax({
    type: "POST",
    data: fdata,
    contentType: false,
    processData: false,
    url: "/inc/backoff_process.php",
    success: function (data) {
      console.log("success");
      var tdata = data.split("|");
      if (tdata[3] == "backwork") {
        $("#backoff_table").html(tdata[0]);
        $("#back_pagelist").html(tdata[1]);
      } else {
        $("#backoff_table tbody").html(tdata[0]);
        $("#back_pagelist").html(tdata[1]);
      }
    },
  });
}

// 오름,내림정렬 coin_list_rewardy
$(document).on("click", "#member_list_header_in > div button", function () {
  $(".member_list_header_in > div button").removeClass("on");
  $(this).addClass("on");
  var fdata = new FormData();
  var code = $("#code").val();
  fdata.append("code", code);

  var val = $(this).parent().parent().find("strong").attr("value");

  var type = $("#backoffice_type").val();
  var tclass = $(this).attr("class").replace(" on", "");
  // console.log(tclass);
  fdata.append("tclass", tclass);
  // return false;
  fdata.append("mode", type);
  fdata.append("kind", val);

  var search = $("#backcoin_search").val();
  if (search) {
    fdata.append("search", search);
  }

  var sdate = $("#backoff_sdate").val();
  var edate = $("#backoff_edate").val();
  if (sdate && edate) {
    fdata.append("sdate", sdate);
    fdata.append("edate", edate);
  }
  fdata.append("p", "1");

  $.ajax({
    type: "POST",
    data: fdata,
    contentType: false,
    processData: false,
    url: "/inc/backoff_process.php",
    success: function (data) {
      console.log("success!");
      var tdata = data.split("|");
      list = tdata[2];
      console.log(tdata[1]);
      console.log(tdata[2]);
      // console.log(tdata[1]);
      if (list == "backcoin") {
        $("#backoff_table tbody").html(tdata[0]);
      } else if (list == "backlike") {
        $("#list_paging").html(tdata[0]);
      } else if (list == "backuser") {
        $("#list_paging").html(tdata[0]);
      } else if (list == "backerror_log") {
        $("#list_paging").html(tdata[0]);
      }
    },
  });
});

// 오름 내림 정렬
$(document).on("click", ".list_arrow", function () {
  $(".list_arrow").removeClass("down");
  $(".list_arrow").removeClass("up");

  // if($(this).hasClass("down")){
  //   $(this).addClass("up");
  // }else{
  //   $(this).addClass("down");
  // }

  var fdata = new FormData();

  var val = $(this).parent().parent().find(".back_sortkind").attr("value");
  // alert(val);
  // return false;
  var list_cnt = $("#list_cnt").val();
  if (list_cnt) {
    fdata.append("list", list_cnt);
  }

  var code = $("#code").val();
  fdata.append("code", code);

  var sort_kind = $(this).val();
  if (sort_kind == "btn_sort_down") {
    fdata.append("tclass", sort_kind);
    $(this).removeClass("up");
    $(this).addClass("down");
    $(this).val("btn_sort_up");
  } else if (sort_kind == "btn_sort_up") {
    fdata.append("tclass", sort_kind);
    $(this).removeClass("down");
    $(this).addClass("up");
    $(this).val("btn_sort_down");
  }

  var type = $("#backoffice_type").val();

  // return false;
  fdata.append("mode", type);
  fdata.append("kind", val);

  var search = $("#backcoin_search").val();
  if (search) {
    fdata.append("search", search);
  }

  var sdate = $("#backoff_sdate").val();
  var edate = $("#backoff_edate").val();
  if (sdate && edate) {
    fdata.append("sdate", sdate);
    fdata.append("edate", edate);
  }
  fdata.append("p", "1");

  $.ajax({
    type: "POST",
    data: fdata,
    contentType: false,
    processData: false,
    url: "/inc/backoff_process.php",
    success: function (data) {
      console.log("success!");
      var tdata = data.split("|");
      list = tdata[3];
      // console.log(tdata[1]);
      if (list == "backwork") {
        $("#backoff_table").html(tdata[0]);
        $("#back_pagelist").html(tdata[1]);
      } else {
        $("#backoff_table tbody").html(tdata[0]);
        $("#back_pagelist").html(tdata[1]);
      }
    },
  });
});

function list_pageing(list, str, page = "1") {
  var fdata = new FormData();

  var search = $("#backcoin_search").val();
  var sdate = $("#backoff_sdate").val();
  var edate = $("#backoff_edate").val();
  var type = $("#backoffice_type").val();
  var kind = $("#kind").val();
  var tclass = $("#tclass").val();
  var list_cnt = $("#list_cnt").val();
  var code = $("#code").val();

  if (list_cnt) {
    fdata.append("list", list_cnt);
  }

  if (search) {
    fdata.append("search", search);
  }

  if (sdate && edate) {
    fdata.append("sdate", sdate);
    fdata.append("edate", edate);
  }

  if (kind) {
    fdata.append("kind", kind);
  }

  if (tclass) {
    fdata.append("tclass", tclass);
  }

  if (code) {
    fdata.append("code", code);
  }
  if (list) {
    //indexval = url.indexOf("customer");
    //console.log("url ::::: " + url);
    fdata.append("mode", type);
    url = "/inc/backoff_process.php";

    // 20230127_코인출금 신청내역에 따른 js 추가 if(list==member,inc)
    // 20230130_멤버별 공용코인_지급,회수에 따른 js 추가 if(list==history)
    // alert(page);
    fdata.append("p", page);

    $.ajax({
      type: "POST",
      data: fdata,
      contentType: false,
      processData: false,
      url: url,
      success: function (data) {
        // console.log(data);
        if (data) {
          var tdata = data.split("|");
          console.log(tdata[1]);
          if (tdata) {
            if (list == "backwork_list") {
              $("#backoff_table").html(tdata[0]);
              $("#back_pagelist").html(tdata[1]);
            } else {
              $("#backoff_table tbody").html(tdata[0]);
              $("#back_pagelist").html(tdata[1]);
              textDefult();
            }
          }
        }
      },
    });
  }
}

//셀렉트박스 - 마우스 오버
$(document).on("mouseover", ".rew_list_char", function () {
  // alert("adfag")
  $("#rew_member_sub_func_sort").addClass("on");
});

//셀렉트박스 - 마우스 리브
$(document).on("mouseleave", ".rew_list_char", function () {
  $("#rew_member_sub_func_sort").removeClass("on");
});

//출력개수설정 - 마우스 오버
$(document).on("mouseover", "#rew_member_sub_func_list", function () {
  $("#rew_member_sub_func_list").addClass("on");
});

//출력개수설정 - 마우스 리브
$(document).on("mouseleave", "#rew_member_sub_func_list", function () {
  $("#rew_member_sub_func_list").removeClass("on");
});

//셀렉트 박스 선택시
$(document).on("click", "#rew_list_char li", function () {
  // return false;
  $("#backoff_search").val("");
  $(".member_list_header_in > div button").removeClass("on");
  $(".rew_list_char_select").removeClass("on");

  var code = $(this).children().val();
  var codetext = $(this).children().text();

  $("#rew_list_char").val(code);
  $("#rew_list_char").find(".rew_list_char_select").find("span").text(codetext);

  var fdata = new FormData();
  var type = $("#backoffice_type").val();
  var sdate = $("#backoff_sdate").val();
  var edate = $("#backoff_edate").val();

  var list_cnt = $("#list_cnt").val();
  if (list_cnt) {
    fdata.append("list", list_cnt);
  }

  fdata.append("mode", type);
  fdata.append("code", code);

  if (sdate && edate) {
    fdata.append("sdate", sdate);
    fdata.append("edate", edate);
  }
  fdata.append("p", "1");
  $.ajax({
    type: "post",
    async: false,
    data: fdata,
    contentType: false,
    processData: false,
    url: "/inc/backoff_process.php",
    success: function (data) {
      var tdata = data.split("|");
      console.log("success");
      list = tdata[3];
      if (list == "backwork") {
        $("#backoff_table").html(tdata[0]);
        $("#back_pagelist").html(tdata[1]);
      } else {
        $("#backoff_table tbody").html(tdata[0]);
        $("#back_pagelist").html(tdata[1]);
      }
    },
  });
});


//출력개수 박스 선택시
$(document).on("click", "#rew_member_sub_func_list ul li", function () {
  $("#backoff_search").val("");
  $(".member_list_header_in > div button").removeClass("on");
  $(".rew_member_sub_func_sort").removeClass("on");

  var list_cnt = $(this).val();

  console.log(list_cnt);
  var codetext = $(this).children().text();

  $("#rew_member_sub_func_list .rew_member_sub_func_sort_in").val(list_cnt);
  $("#rew_member_sub_func_list .rew_member_sub_func_sort_in .btn_sort_on")
    .find("span")
    .text(codetext);

  // alert(codetext+"|"+code);

  var fdata = new FormData();
  var type = $("#backoffice_type").val();
  var sdate = $("#backoff_sdate").val();
  var edate = $("#backoff_edate").val();
  var code = $("#code").val();

  fdata.append("mode", type);
  fdata.append("list", codetext);

  if (sdate && edate) {
    fdata.append("sdate", sdate);
    fdata.append("edate", edate);
  }

  if (code) {
    fdata.append("code", code);
  }

  fdata.append("p", "1");
  $.ajax({
    type: "post",
    async: false,
    data: fdata,
    contentType: false,
    processData: false,
    url: "/inc/backoff_process.php",
    success: function (data) {
      var tdata = data.split("|");
      var list = tdata[3];
      if (list == "backwork") {
        $("#backoff_table").html(tdata[0]);
        $("#back_pagelist").html(tdata[1]);
      } else {
        $("#backoff_table tbody").html(tdata[0]);
        $("#back_pagelist").html(tdata[1]);
      }
    },
  });
});

$(document).on("click", "#now_penalty", function () {
  var fdata = new FormData();
  fdata.append("mode", "penalty_now");
  $.ajax({
    type: "post",
    async: false,
    data: fdata,
    contentType: false,
    processData: false,
    url: "/inc/backoff_process.php",
    success: function (data) {
      // var tdata = data.split("|");
      $("#backoff_table").html(data);
    },
  });
});

////// 누적통계 템플릿 //////
// 캘린더 사용
$(document).on("click", ".rew_member_sub_func_period #btn_total", function () {
  var backcoin_sdate = $("#backcoin_sdate").val();
  var backcoin_edate = $("#backcoin_edate").val();

  var intdate1 = new Date(backcoin_sdate);
  var intdate2 = new Date(backcoin_edate);

  var timeDifference = intdate2.getTime() - intdate1.getTime(); // 밀리초 단위의 차이
  var daysDifference = timeDifference / (1000 * 60 * 60 * 24); // 일 단위로 변환

  if (daysDifference > 30) {
    alert("시작일과 종료일의 차이는 30일을 넘을 수 없습니다.");
    return false;
  }

  // $("#reward_inquiry").val(true)
  var fdata = new FormData();
  var type = $("#total_type").val();
  var code = $("#code").val();
  if (code) {
    fdata.append("code", code);
  }
  fdata.append("totalcnt", daysDifference);
  // return false;
  fdata.append("mode", type);
  fdata.append("sdate", backcoin_sdate);
  fdata.append("edate", backcoin_edate);
  $.ajax({
    type: "post",
    async: false,
    data: fdata,
    contentType: false,
    processData: false,
    url: "/inc/backoff_process.php",
    success: function (data) {
      var tdata = data.split("|");
      bar = tdata[0];
      donut = tdata[1];
      $("#work_graph").html(bar);
      barChartGraph();
      $("#work_doughnut").html(donut);
      doughnutGraph();
    },
  });
});

//누적 통계 날짜 초기화
$(document).on(
  "click",
  ".rew_member_sub_func_calendar #cal_history",
  function () {
    var fdata = new FormData();
    fdata.append("reset", "re");

    var type = $("#total_type").val();
    fdata.append("mode", type);

    var code = $("#code").val();
    if (code) {
      fdata.append("code", code);
    }

    $.ajax({
      type: "post",
      async: false,
      data: fdata,
      contentType: false,
      processData: false,
      url: "/inc/backoff_process.php",
      success: function (data) {
        var tdata = data.split("|");
        bar = tdata[0];
        donut = tdata[1];
        $("#work_graph").html(bar);
        barChartGraph();
        $("#work_doughnut").html(donut);
        doughnutGraph();
        $("#backoff_table tbody tr:eq(1)").html(tdata[2]);
        $("#backcoin_sdate").val(tdata[3]);
        $("#backcoin_edate").val(tdata[4]);
      },
    });
  }
);

// 누적통계 (일간/월간 스위칭)
$(document).on("click", "#btnradio1", function () {
  barChartGraph();
});

$(document).on("click", "#btnradio2", function () {
  barMonthGraph();
});

// 누적통계 유형별 출력
$(document).on("click", "#back_work_total_code li", function () {
  $("#backoff_search").val("");
  $(".member_list_header_in > div button").removeClass("on");
  $(".rew_member_sub_func_sort").removeClass("on");

  var code = $(this).children().val();
  var codetext = $(this).children().children().text();

  $("#back_work_total_code .rew_member_sub_func_sort_in").val(code);
  $("#back_work_total_code .btn_sort_on").find("span").text(codetext);

  // return false;

  var fdata = new FormData();
  var type = $("#total_type").val();
  var sdate = $("#backcoin_sdate").val();
  var edate = $("#backcoin_edate").val();

  fdata.append("mode", type);
  fdata.append("code", code);
  if (sdate && edate) {
    fdata.append("sdate", sdate);
    fdata.append("edate", edate);
  }
  fdata.append("p", "1");
  $.ajax({
    type: "post",
    async: false,
    data: fdata,
    contentType: false,
    processData: false,
    url: "/inc/backoff_process.php",
    success: function (data) {
      var tdata = data.split("|");
      bar = tdata[0];
      donut = tdata[1];
      $("#work_graph").html(bar);
      barChartGraph();
      $("#work_doughnut").html(donut);
      doughnutGraph();
    },
  });
});

//셀렉트박스 - 마우스 오버
$(document).on("mouseover", "#back_work_total_code", function () {
  $("#back_work_total_code").addClass("on");
});

//셀렉트박스 - 마우스 리브
$(document).on("mouseleave", "#back_work_total_code", function () {
  $("#back_work_total_code").removeClass("on");
});

//오늘업무 리스트 수정
$(document).on("click", "#back_edit", function () {
  var idx = $(this).closest(".back_btn").attr("value");
  console.log("idx ::: " + idx);

  $("#back_btn_" + idx)
    .find("#back_edit")
    .hide();
  $("#back_btn_" + idx)
    .find("#back_remove")
    .hide();

  $("#back_btn_e_" + idx)
    .find("#back_enter")
    .show();
  $("#back_btn_e_" + idx)
    .find("#back_cancel")
    .show();

  $(this).closest("tr").find("p").hide();
  $(".content_edit_" + idx).show();
});

//오늘업무 리스트 취소
$(document).on("click", "#back_cancel", function () {
  var idx = $(this).closest(".back_btn").attr("value");
  console.log("idx ::: " + idx);

  $("#back_btn_e_" + idx)
    .find("#back_enter")
    .hide();
  $("#back_btn_e_" + idx)
    .find("#back_cancel")
    .hide();

  $("#back_btn_" + idx)
    .find("#back_edit")
    .show();
  $("#back_btn_" + idx)
    .find("#back_remove")
    .show();

  $(this).closest("tr").find("p").show();
  $(".content_edit_" + idx).hide();
});

//오늘업무 리스트 확인
$(document).on("click", "#back_enter", function () {
  var idx = $(this).closest(".back_btn").attr("value");
  console.log("idx ::: " + idx);

  var contents = $(".content_edit_" + idx).val();

  var fdata = new FormData();
  fdata.append("mode", "backwork_edit");
  fdata.append("idx", idx);
  fdata.append("content", contents);

  $.ajax({
    type: "post",
    async: false,
    data: fdata,
    contentType: false,
    processData: false,
    url: "/inc/backoff_process.php",
    success: function (data) {
      console.log(data);
      var tdata = data.split("|");
      No = tdata[0];
      if (No == "success") {
        $("#back_btn_e_" + idx)
          .find("#back_enter")
          .hide();
        $("#back_btn_e_" + idx)
          .find("#back_cancel")
          .hide();

        $("#back_btn_" + idx)
          .find("#back_edit")
          .show();
        $("#back_btn_" + idx)
          .find("#back_remove")
          .show();

        $("#table_tr_" + idx)
          .find("p")
          .show();
        $("#table_tr_" + idx)
          .find("p")
          .text(contents);
        $(".content_edit_" + idx).hide();
        alert("수정 되었습니다.");
      } else if (No == "not_update") {
        alert("수정할 내용을 입력해주세요.");
        return false;
      } else {
        alert("수정권한이 없습니다.");
        return false;
      }
    },
  });
});

//오늘업무 리스트 삭제
$(document).on("click", "#back_remove", function () {
  var idx = $(this).closest(".back_btn").attr("value");
  console.log("idx ::: " + idx);

  var fdata = new FormData();
  fdata.append("mode", "backwork_remove");
  fdata.append("idx", idx);

  $.ajax({
    type: "post",
    async: false,
    data: fdata,
    contentType: false,
    processData: false,
    url: "/inc/backoff_process.php",
    success: function (data) {
      console.log(data);
      var tdata = data.split("|");
      No = tdata[0];
      if (No == "success") {
        $(".work_state_" + idx).text("9");
      } else if (No == "state9") {
        alert("이미 삭제 되어 있는 데이터 입니다.");
        exit;
      } else {
        alert("수정권한이 없습니다.");
        return false;
      }
    },
  });
});

// 6대서비스 권한부여
function textDefult() {
  var cnt = $("#backoff_table tbody tr").length;
  var spcialChars = ["''", "' '"];
  for (var i = 0; i < cnt; i++) {
    var name = $("#backoff_table tbody")
      .find("tr:eq(" + i + ")")
      .find("td:eq(1)")
      .text();
    for (var j = 0; j < 2; j++) {
      if (name.indexOf(spcialChars[j]) != -1) {
        console.log("tes");
        $("#backoff_table tbody")
          .find("tr:eq(" + i + ")")
          .find("td:eq(1)")
          .html(function (index, oldHtml) { 
            // 특정 문자를 원하는 스타일과 함께 감싼 <span> 요소를생성합니다.
            var newHtml = oldHtml.replace(
              spcialChars[j],
              "<span class='highlight'>" + spcialChars[j] + "</span>"
            );
            return newHtml;
          });
        // name.replace(spcialChars[j], "<span class='highlight'>"+spcialChars[j]+"</span>");
      }
    }
  }
}

// 브로슈어 새로운 공지사항 생성
$(document).on("click", "#new_notice", function () {
  type = $("#backoffice_type").val();
  type_arr = ["backnote_list","backfaq_list","backsample_list","backmanual_list"];
  link_arr = ["bro_notice_write","bro_faq_write","bro_sample_write","bro_manual_write"];

  for(var i=0; i<type_arr.length; i++){
    if(type==type_arr[i]){
      location.href = link_arr[i]+".php";
      return false;
    }
  }
});

// var editor = new Mong9('#content_notice');
// 공지사항 등록 버튼
$(document).on("click", "#notice_btn", function () {
  var contents = CKEDITOR.instances.content_notice.getData();
  console.log(contents);

  type = $("#backwrite_type").val(); // 작성 타입 

  if (!contents) {
    alert("내용을 입력해주세요.");
    return false;
  }

  var title = $("#notice_title").val();
  if (!title) {
    alert("제목을 입력해주세요.");
    return false;
  }

  var fdata = new FormData();
  if(type == "bro_sample_write"){
    title_color = $("#title_color").val();
    if(!title_color){
      alert("타이틀 카드에 사용할 색상을 선택해주세요");
      return false;
    }
    fdata.append("title_color", title_color);

    service = $("#sample_service").val();
    if(!service){
      alert("서비스 유형을 선택해주세요");
      return false;
    }
    fdata.append("service",service);
    
    category = $("#category").val();
    if(!category){
      alert("카테고리를 선택해주세요");
      return false;
    }
    fdata.append("category",category);

    // alert(category+"|"+service+"|"+title_color);
    // return false;
  }

  if(type == "bro_manual_write"){
    manual_kind = $("#manual_kind").val();
    fdata.append("kind",manual_kind);
  }

  var update = $("#notice_up").val();
  if(update){
    fdata.append("update",update);
  }
  fdata.append("content", contents);
  fdata.append("title", title);
  fdata.append("mode", type);
  $.ajax({
    type: "post",
    async: false,
    data: fdata,
    contentType: false,
    processData: false,
    url: "/inc/backoff_process.php",
    success: function (data) {
      console.log(data);
      if (data) {
        var tdata = data.split("|");
        var sql = tdata[0];
        var comp = tdata[1];
        var kind = tdata[2];
        var link = tdata[3];
        console.log(sql);
        if (comp == "success") {
          alert(kind+"이 등록 되었습니다.");
          location.href = ""+link+"";
        } else if (comp == "not_auth") {
          alert(kind+" 등록 권한이 없습니다.");
          return false;
        } else if (comp == "update"){
          alert(kind+"이 수정 되었습니다.");
          location.href = ""+link+"";
        }
      }
    },
  });
});

// 백오피스 - 브로슈어 공지사항 view 페이지 이동
$(document).on("click", "td[id^=backnote_]", function () {
  idx = $(this).attr("id");
  type = $("#bro_view").val();
  // alert(idx);
  no = idx.replace("backnote_", "");
  location.href = "bro_"+type+"_view.php?idx=" + no;
});

// 백오피스 - 브로슈어 공지사항 노출/미노출 버튼 스크립트
$(document).on("click", "button[id^=btnradio]", function () {
  var type = $(this).attr("id").replace("btnradio_", "");
  // var type = type.charAt(0);
  var kind = $("#bro_view").val();
  var idx = $(this).val();

  var fdata = new FormData();
  fdata.append("mode", "backnote_btn");
  fdata.append("idx", idx);
  fdata.append("status", type);
  fdata.append("kind",kind);

  // console.log(idx+"|"+type);
  // return false;
  $.ajax({
    type: "post",
    async: false,
    data: fdata,
    contentType: false,
    processData: false,
    url: "/inc/backoff_process.php",
    success: function (data) {
      console.log(data);
      var tdata = data.split("|");
      var enter = tdata[1];
      if (enter == "complete") {
        if (type == "1") {
          $("#btn_group_" + idx)
            .find("#btnradio_1")
            .attr("class", "btn btn-dark");
          $("#btn_group_" + idx)
            .find("#btnradio_0")
            .attr("class", "btn btn-outline-dark");
        } else if (type == "0") {
          $("#btn_group_" + idx)
            .find("#btnradio_1")
            .attr("class", "btn btn-outline-dark");
          $("#btn_group_" + idx)
            .find("#btnradio_0")
            .attr("class", "btn btn-dark");
        }
      }
    },
  });
});

//백오피스 - 브로슈어 공지사항 삭제 프로세스
$(document).on("click", "button[id^=notedel_]", function () {
  var id_value = $(this).attr("id").replace("notedel_", "");
  var kind = $("#bro_view").val();
  if(confirm("해당 데이터를 삭제하시겠습니까? \n삭제한 데이터는 다시 복원할 수 있습니다.")){
    var fdata = new FormData();
    fdata.append("mode","backnote_btn");
    fdata.append("idx",id_value);
    fdata.append("kind",kind);
    fdata.append("status","9");
    $.ajax({
      type: "post",
      async: false,
      data: fdata,
      contentType: false,
      processData: false,
      url: "/inc/backoff_process.php",
      success: function (data) {
        console.log(data);
        var tdata = data.split("|");
        var enter = tdata[1];
        if (enter == "complete") {
          $("#notedel_"+id_value).closest("tr").remove();
        }
      },
    });
  }else{
    return false;
  }
});

//백오피스 - 브로슈어 공지사항 복원 프로세스
$(document).on("click", "button[id^=noteres_]", function () {
  var id_value = $(this).attr("id").replace("noteres_", "");
  var kind = $("#bro_view").val();
  if(confirm("해당 데이터를 다시 복원합니까?")){
    var fdata = new FormData();
    fdata.append("mode","backnote_btn");
    fdata.append("idx",id_value);
    fdata.append("kind",kind);
    fdata.append("status","0");
    $.ajax({
      type: "post",
      async: false,
      data: fdata,
      contentType: false,
      processData: false,
      url: "/inc/backoff_process.php",
      success: function (data) {
        console.log(data);
        var tdata = data.split("|");
        var enter = tdata[1];
        if (enter == "complete") {
          $("#noteres_"+id_value).closest("tr").remove();
        }
      },
    });
  }else{
    return false;
  }
});

//백오피스 - 브로슈어 업데이트 바로가기
$(document).on("click", "button[id^=noteedit_]", function () {
  var id_value = $(this).attr("id").replace("noteedit_", "");
  var kind = $("#bro_view").val();

  location.href = "/choco/bro_"+kind+"_write.php?idx="+id_value;
  // alert(id_value+"|"+kind);
  return false;
});

// 백오피스 공지사항 수정버튼
$(document).on("click","#notice_edit",function(){
  type = $("#backwrite_type").val();
  var idx = $("#notice_idx").val();

  location.href = "/choco/"+type+".php?idx="+idx;
  return false;
});

//유저리스트 유저 비밀번호 초기화
$(document).on("click", "button[id^=reset]", function(){
  var idx = $(this).val();
  var email = $(this).closest("tr").find(".user_td_email").text();
  
  if(confirm("["+email+"]님의 비밀번호를 초기화 하십니까?")){
    var fdata = new FormData();
    fdata.append("idx",idx);
    fdata.append("mode","pass_reset");
    $.ajax({
      type: "post",
      async: false,
      data: fdata,
      contentType: false,
      processData: false,
      url: "/inc/backoff_process.php",
      success: function (data) {
        console.log(data);
        if(data == "no_ip"){
          alert("허용되지 않은 IP입니다.");
          return false;
        }else if(data == "success"){
          alert("비밀번호가 초기화 됐습니다 \n초기값은 [0000] 입니다.");
          location.reload();
        }else if(data == "no_change"){
          alert("비밀번호가 이미 초기화 되어 있습니다.");
          return false;
        }else if(data == "not_user"){
          alert("확인되는 회원정보가 없습니다.");
          return false;
        }
      },
    });
  }else{
    return false;
  }
});

$(document).on("click","#btn_make_excel",function(){
  var sdate = $("#backoff_sdate").val();  // 시작일자
  var edate = $("#backoff_edate").val();  // 종료일자
  var tclass = $("#tclass").val();
  var type = $("#backoffice_type").val(); // 백오피스 페이지 종류
  var list_cnt = $("#list_cnt").val(); // 몇개 출력할지 
  var search = $("#backcoin_search").val(); // 검색어
  var kind = $("#kind").val();
  var code = $("#code").val(); // 출력 필;
  var page = $("#page_num").val();

  link = "/choco/back_excel.php?type="+type;

  if (sdate && edate) {
    link = link + "&sdate="+sdate+"&edate="+edate;
  }

  if (tclass) {
    link = link + "&tclass="+tclass;
  }

  if (list_cnt) {
    link = link + "&list="+list_cnt;
  }

  if (search) {
    link = link + "&search="+search;
  }

  if(kind) {
    link = link + "&kind="+kind;
  }

  if(code) {
    link = link + "&code="+code;
  }

  if(page) {
    link = link + "&page="+page;
  }

  location.href = link;
  
  return false;
});

// 메일 문의사항 답변하기 
$(document).on("click","tr[id^=email_]",function(){
  idx = $(this).attr("id");
  // alert(idx);
  no = idx.replace("email_", "");
  location.href = "bro_email_write.php?idx=" + no;
});

