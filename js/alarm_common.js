function responseGetFcmInfo(token, id, kind) {
  $(document).on("click", "#ra_btn_login_mo", function () {
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
    fdata.append("mode", "login_mobile");
    fdata.append("id", $("#z1").val());
    fdata.append("pwd", $("#z2").val());
    fdata.append("chk_login", $("#chk_login").val());
    fdata.append("device_uuid", id);
    fdata.append("push_register_id", token);
    fdata.append("device_platform", kind);

    $.ajax({
      type: "POST",
      data: fdata,
      contentType: false,
      processData: false,
      url: "/inc/login_ok.php",
      success: function (data) {
        console.log(data);
        if (data == "use_ok") {
          alert("adf");
          if (window.location.pathname == "/alarm/login.php") {
            location.replace("/alarm/alarm_list.php");
          } else {
            location.reload();
          }
        } else if (data == "ad_ok") {
          alert("adf");
          if (window.location.pathname == "/alarm/login.php") {
            location.replace("/alarm/alarm_list.php");
          } else {
            location.reload();
          }
        } else if (data == "use_deny") {
          alert("이메일 주소 및 비밀번호를 확인 해주세요.");
          return false;
        } else if (data == "notuser") {
          alert(
            "아이디 또는 비밀번호가 유효하지 않습니다.\n아이디와 비밀번호를 정확히 입력해 주세요."
          );
          return false;
        } else if (data == "use_check") {
          if (window.location.pathname == "/team/") {
            $(".tl_close").trigger("click");
            first_login_time();
            //$("#layer_work").show();
          } else {
            location.reload();
          }
        } else {
          alert(
            "아이디 또는 비밀번호가 유효하지 않습니다(모바일).\n아이디와 비밀번호를 정확히 입력해 주세요."
          );
          return false;
        }
      },
    });
  });
}


$(document).ready(function(){
  $(".ra_setting_all .btn_switch").click(function(){
    var switchon = $(this);
    $(this).toggleClass("on");

    if($("#all_chk_btn").hasClass("on")) {
      $("button[id=sw_idx]").addClass("on");
    } else {
      $("button[id=sw_idx]").removeClass("on");
    }			
  });

  $(".ra_setting_list .btn_switch").click(function(){
    var switchon = $(this);
    switchon.toggleClass("on");
    
    if($("#sw_idx.btn_switch.on").length != 6){
      $("button[id=all_chk_btn]").removeClass("on");
    }else{
      $("button[id=all_chk_btn]").addClass("on");
    }
  });
});

$(document).on("click","#option",function(){
  location.href = 'App-Prefs:root=NOTIFICATIONS_ID';
});

//알림설정 하기
$(document).on("click", ".ra_setting_all .btn_switch", function () {
  var fdata = new FormData();
  var onf = $(this).hasClass("on");
  var val = $(this).parent().find("#all_chk_btn").attr("value");

  fdata.append("mode", "alarm_change");
  fdata.append("sw_val", val); //value = todaywork,like,challenges..
  fdata.append("onf", onf);

  $.ajax({
    type: "POST",
    data: fdata,
    contentType: false,
    processData: false,
    url: "/inc/alarm_process.php",
    success: function (data) {
      console.log(data);
    },
  });
});

$(document).on("click", ".ra_setting_list .btn_switch", function () {
  var fdata = new FormData();
  var onf = $(this).hasClass("on");
  var val = $(this).parent().find("#sw_idx").attr("value");

  fdata.append("mode", "alarm_change");
  fdata.append("sw_val", val);
  fdata.append("onf", onf);

  $.ajax({
    type: "POST",
    data: fdata,
    contentType: false,
    processData: false,
    url: "/inc/alarm_process.php",
    success: function (data) {
      console.log(data);
    },
  });
});

$(document).on("click", ".ra_btn_link", function () {
  location.href = "link:http://demo.rewardy.co.kr/team/index.php";
});

// 모바일 화면 하단부
$(document).on("click", ".ra_footer_btn .ra_footer_link", function () {
  location.href = "/alarm/index.php";
  return false;
});

$(document).on("click", ".ra_footer_btn .ra_footer_list", function () {
  location.href = "/alarm/alarm_list.php";
  return false;
});

$(document).on("click", ".ra_footer_btn .ra_footer_setting", function () {
  location.href = "/alarm/alarm_select.php";
  return false;
});

$(document).on("click", ".ra_footer_btn .ra_footer_admin", function () {
  location.href = "/alarm/alarm_admin.php";
  return false;
});

$(document).on(
  "click",
  ".ra_footer_btn .ra_footer_logout,.ra_intro_btns #ra_btn_logout",
  function () {
    location.href = "/alarm/logout.php";
    return false;
  }
);

// 알림 읽기 처리하기
$(document).on("click", ".ra_alert_box", function () {
  
  // var idxno = $(".alarm_idx").val();
  var service = $(this).find("#service_alarm").attr("value");
  var idxno = $(this).find("#alarm_idx").attr("value");

  var fdata = new FormData();
  fdata.append("mode", "alarm_enter");
  fdata.append("idx", idxno);

  $.ajax({
    type: "POST",
    data: fdata,
    contentType: false,
    processData: false,
    url: "/inc/alarm_process.php",
    success: function (data) {
      console.log(data);
      if (service == "work") {
        location.href = "http://demo.rewardy.co.kr/todaywork/index.php";
      } // 코인 타임라인 링크 이동
      else if (service == "reward") {
        location.href = "http://demo.rewardy.co.kr/reward/index.php";
      } // 챌린지 타임라인 링크 이동
      else if (service == "challenge") {
        location.href = "http://demo.rewardy.co.kr/challenge/index.php";
      } else if (service == "party") {
        location.href = "http://demo.rewardy.co.kr/party/index.php";
      } else if (service == "live") {
        location.href = "http://demo.rewardy.co.kr/team/index.php";
      }
    },
  });
});

//뒤로 가기
$(document).on("click",".ra_header_back", function(){
  window.history.back();
});

// 알림 삭제
$(document).on("click", "#alarm_del", function () {
  var delNo = $(this).parent().find("#alarm_idx").attr("value");
  
  var fdata = new FormData();
  fdata.append("mode", "alarm_del");
  fdata.append("idx", delNo);

  $.ajax({
    type: "POST",
    data: fdata,
    contentType: false,
    processData: false,
    url: "/inc/alarm_process.php",
    success: function (data) {
      console.log(data);
      $("#alarm_list_"+delNo).remove();
    },
  });
});
