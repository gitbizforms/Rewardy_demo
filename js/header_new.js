$(document).ready(function(){
  //231012 헤더 동작 추가
$(document).on("click", ".btn_my_alert", function () {
  $(this).toggleClass("on");
  $(".layer_my_info").hide();
  $(".layer_my_alert").toggle();
  $(".layer_my_mess").hide();
});

$(document).on("mouseleave", ".layer_my_alert", function () {
$(".btn_my_alert").removeClass("on");
$(".layer_my_alert").hide();
});

$(document).on("click", ".btn_my_mess", function(event){
  $(".layer_my_mess").toggle();
  $(".layer_my_info").hide();
  $(".layer_my_alert").hide();
});

$(document).on("mouseleave", ".rew_head_my_mess", function () {
  $(".layer_my_mess").hide();
  });

$(document).on("click", ".re_message", function(){
  $(this).addClass("on");
  $(".se_message").removeClass("on");

  $(".send").hide();
  $(".receive").show();
 
});

$(document).on("click", ".se_message", function(){
  $(this).addClass("on");
  $(".re_message").removeClass("on");

  $(".receive").hide();
  $(".send").show();
});

$(document).on("click", ".btn_my_info", function () {
$(".layer_my_alert").hide();
$(".layer_my_info").toggle();
});

$(document).on("mouseleave", ".layer_my_info", function () {
$(".layer_my_info").hide();
});

$(document).on("mouseleave", ".rew_head_my", function () {
$(".btn_my_mail").removeClass("on");
$(".btn_my_alert").removeClass("on");
$(".layer_my_alert").hide();
$(".layer_my_info").hide();
});

//캐릭터 팝업
$(document).on("click", '.char_prof', function(){
  $(".tl_prof_slc ul").show();
});
$(document).on("click", '.main_prof', function(){
  $("#check_profile").val(null);
  $(".layer_pro").show();
});
$(document).on("click", '.layer_close', function(){
  $(".layer_pro").hide();
});
$(document).on('click', '#btn_slc_character', function(){
  $(".rew_layer_character").show();
});

$(document).on("click", ".rew_layer_character .tl_btn", function () {
  $(".rew_layer_character").hide();
});
$(document).on("click", ".btn_profile", function () {
  $(".btn_profile").removeClass("on");
  $(this).addClass("on");
});
$(document).on("mouseleave", ".tl_prof_slc", function () {
  $(".tl_prof_slc ul").hide();
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

// 알림 카운트 없애기
$(document).on("click", ".btn_my_alert", function() {
  var fdata = new FormData();
  var val = $(".btn_my_alert em").text();

  if(val > 0){
    fdata.append("val", val);
    fdata.append("mode", "my_alert_all");
    $.ajax({
        type: "post",
        async: false,
        data: fdata,
        contentType: false,
        processData: false,
        url: '/inc/works_process.php',
        success: function(data) {
          // console.log(data);
            if (data) {
                tdata = data.split("|");
                console.log(tdata);
                if (tdata) {
                    var result = tdata[0];
                    var cnt = tdata[1];
                    if (result == "complete") {
                        $("#rew_head_my_cnt").html(cnt);
                    }
                }
            }
        }
    });
 }
});

//쪽지 N 없애기
$(document).on("click", ".btn_my_mess", function() {
  var fdata = new FormData();
  var val = $(".btn_my_mess em").text();
  if(val == "N"){
    fdata.append("val", val);
    fdata.append("mode", "message_all");
    $.ajax({
        type: "post",
        async: false,
        data: fdata,
        contentType: false,
        processData: false,
        url: '/inc/works_process.php',
        success: function(data) {
          // console.log(data);
            if (data) {
                tdata = data.split("|");
                console.log(tdata);
                if (tdata) {
                    var result = tdata[0];
                    var cnt = tdata[1];
                    if (result == "complete") {
                        $(".rew_head_my_message").html(cnt);
                    }
                }
            }
        }
    });
  }
});

//알림 리스트 삭제 하기
$(document).on("click", "#my_alert_close", function() {
    
  if (confirm("알림 내역을 삭제하시겠습니까?")) {
      var fdata = new FormData();
      var val = $(this).val();
      //$(this).parent().remove();
      fdata.append("val", val);
      fdata.append("mode", "my_alert_close");
      $.ajax({
          type: "post",
          async: false,
          data: fdata,
          contentType: false,
          processData: false,
          url: '/inc/works_process.php',
          success: function(data) {
            // console.log(data);
              if (data) {
                  tdata = data.split("|");
                  // console.log(data);
                  if (tdata) {
                      var result = tdata[0];
                      var cnt = tdata[1];
                      var html = tdata[2];
                      if (result == "complete") {
                          $("#rew_head_my_cnt").html(cnt);
                          $("#my_alert_list ul").html(html);
                      }
                  }
              }
          }
      });
     }
  });


  $(document).on("click", ".data_user", function(){

    
    $(".textarea_mess").val(null);
    var countElement = $('.message_count');
    countElement.text('0자 / 100자');

    var parentLi = $(this).closest("li");

    var user_name = $(this).attr("value");
    var user_part = parentLi.find(".user_part").val();
    var user_email = parentLi.find(".user_email").val();
    var user_img = parentLi.find(".user_img").val();
    $(".layer_mess_cancel").removeClass("live");
    $(".layer_mess_cancel").addClass("header");
    $(".mess_name_user").text(user_name);
    $(".mess_name_team").text(user_part);
    $(".mess_user_img_in").css("background-image", 'url("' + user_img + '")');
    $(".layer_mess").show();
    $(".user_id").val(user_email);
  });
  
  $(document).on("click", ".layer_mess_cancel", function() {
    $(".layer_mess").hide();
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

  // 쪽지 보내기
  $(document).on("click", ".layer_mess_submit", function(){
    var send_user = $(".mess_name_user").text();
    var send_user_team = $(".mess_name_team").text();
    var send_user_imgs = $(".mess_user_img_in").css("background-image").replace(/^url\(['"](.+)['"]\)/, '$1');
    var user_email  = $(".user_id").val();
    var message_content = $(".textarea_mess").val();
    console.log(":::::::::::::::"+user_email);
    if($(".layer_mess_cancel").hasClass("header") == true || $(".layer_mess_cancel").hasClass("header_like") == true){
      if(!message_content){
          alert("내용을 작성해주세요.");
          return false;
      }
      
      var fdata = new FormData();
      fdata.append("mode", "mess_live");
      fdata.append("send_email", user_email);
      fdata.append("service", "alarm");
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
              }else if(data == 'mem_fail'){
                  alert("해당 회원이 없습니다.");
                  return false;
              }
          }
      });
    }
});

  $('.my_alert_tit button').click(function () {

    $('.layer_my_alert').addClass('set')

    if ($('.layer_my_alert').hasClass('set') === true) {
      $('.my_alert_set').show()
      $('.my_alert_tit strong').text('알림설정');
      $('.my_alert_tit strong').css({
        'padding-left': '20px',
        'background': 'url(https://rewardy.co.kr/html/images/pre/ico_back.png) 5px 50% no-repeat',
        'background-size': '5px',
        'cursor' : 'pointer'
      })
    }
  })

  $('.my_alert_tit strong').click(function(){
    $('.layer_my_alert').removeClass('set');
    $('.my_alert_set').hide();
    $('.my_alert_tit strong').text('알림');
    $('.my_alert_tit strong').css({
        'padding-left': '0px',
        'background': 'none',
        'background-size': '0',
        'cursor' : 'none'
      })
  })


  $(document).on("click",".alert_set_head .btn_switch", function(){
      $(this).toggleClass("on");

      if($(this).hasClass("on") == true){
        $("div[id=setting]").addClass("on");
      }else{
        $("div[id=setting]").removeClass("on");
      }
        var fdata = new FormData();
        var onf = $(this).hasClass("on");
        var val = $(this).attr("value");
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

  $(document).on("click",".alert_set_body .btn_switch", function(){
      $(this).toggleClass("on");
       
      var fdata = new FormData();
      var onf = $(this).hasClass("on");
      var val = $(this).attr("value");
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

    $(document).on("click", ".header_cp", function(){

        $(".rl_jjim_only").hide();
        $(".rl_user_btn_coin").hide();
        $(".rl_user_mess").hide();

      // var name = $(this).parent().parent().parent().find(".ldr_user_name").text();
      var name = $(this).parent().parent().parent().parent().parent().find(".user_live_name").val();
      var team = $(this).parent().parent().parent().parent().parent().find(".user_live_part").val();
      var ldr_img = $(this).parent().parent().parent().parent().parent().find(".user_live_img").val();
      var uid = $(this).parent().parent().parent().parent().parent().find(".user_live_email").val();
      // var team = $(this).parent().parent().parent().find(".ldr_user_team").text();
      // var ldr_img = $(this).parent().parent().parent().find(".ldr_user_imgs").css("background-image").replace(/^url\(['"](.+)['"]\)/, '$1');
      // var uid = $(this).parent().parent().parent().find(".ldr_today_num").val();
      // console.log(uid+"|"+name+"|"+team+"|");
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


    });
    $(document).on("click", ".header_like", function(){

      var send_user = $(this).parent().parent().parent().parent().parent().find(".user_live_name").val();
      var send_user_team = $(this).parent().parent().parent().parent().parent().find(".user_live_part").val();
      var send_user_imgs = $(this).parent().parent().parent().parent().parent().find(".user_live_img").val();
      var val = $(this).parent().parent().parent().parent().parent().find(".user_live_value").val();
      var send_userid = $(this).parent().parent().parent().parent().parent().find(".user_live_email").val();

        $('.btn_type_list').removeClass("on");
        $('.btn_type_graph').addClass("on");

        $(".jjim_only").hide();
        $(".jg_user_btn_coin").hide();
        $(".jt_user_mess").hide();
        $(".jt_jjim_only").hide();
        $(".jt_user_btn_coin").hide();

        // var send_userid = $(".ldr_user").find("#ldr_user_id_" + val).val();
        // var penalty = $(this).parent().parent().parent().find("input[id^=user_penalty_]").val();
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

                        //그래프 높이설정
                        var jg_all = $(".jg_user_heart_all").text();
                        $(".jg_graph_list li").each(function() {
                            var jg_txt = $(this).find("span").text();
                            var jg_height = jg_txt / jg_all * 160;
                            $(this).find("strong").css({ "height": jg_height });
                        });
                        // $("#user_penalty").val(penalty);
                        $(".user_id").val(val);
                        $(".user_value").val(send_userid);
                        $("#jjim_graph").show();
                    }
                }
            }
        });
    });

    
});
