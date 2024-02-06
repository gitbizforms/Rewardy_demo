$(document).ready(function () {
   $(".depth_01 strong.depth_01_link").mouseenter(function () {
      $(this).next(".depth_02_area").show();
    });
    $(".depth_01").mouseleave(function () {
      $(this).find(".depth_02_area").hide();
    });

    $(".rb_nav_mb_btn button, .rb_nav_mb_deam").click(function () {
      $(".rb_nav_depth").toggleClass("on");
      $(".rb_nav_member").toggleClass("on");
      $(".rb_nav_mb_deam").toggleClass("on");
    });

    $(".faq_q").click(function () {
      $(".faq_q").not(this).next(".faq_a").slideUp(200);
      $(this).next(".faq_a").slideToggle(200);
      $(this).toggleClass("on");
    });

    $('.top_nav li').click(function(){
      $(this).addClass('on');
      $(this).siblings().removeClass('on');
    });

    $('.side_nav li').click(function(){
      $(this).addClass('on');
      $(this).siblings().removeClass('on');
    });

    $('.side_open').click(function(){
      $(this).parent('ul').toggleClass('on');
    });



  //자주묻는 질문 펼치기
  $(document).on("click",".faq_q",function(){
    $(".faq_q").not(this).next(".faq_a").slideUp(200);
    // $(this).next(".faq_a").slideToggle(200);
    // $(this).toggleClass("on");
    if($(this).hasClass("on")==true){
      $(this).closest(".faq_a").slideUp(200);
      $(this).removeClass("on");
    }else if($(this).hasClass("on")==false){
      $(this).closest(".faq_a").slideDown(200);
      $(this).addClass("on");
    }
  });

// $(document).on("click", ".faq_q", function(){
  
// });

function list_pageing(list, str, page = "1") {
    var fdata = new FormData();
  
    var search = $("#bro_search").val();
    var type = $("#bro_type").val();
    var list_cnt = $("#list_cnt").val();
    var code = $("#code").val();
    if (list_cnt) {
      fdata.append("list", list_cnt);
    }
  
    if (search) {
      fdata.append("search", search);
    }
  
    if (code) {
      fdata.append("code", code);
    }
    if (list) {
      //indexval = url.indexOf("customer");
      //console.log("url ::::: " + url);
      fdata.append("mode", type);
      url = "/inc/about_process.php";
  
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
            kind = tdata[3];
            if(kind == "notice"){
               $(".notice_list").html(tdata[0]);
            }else if(kind == "faq"){
              $(".faq_list").html(tdata[0]);
            }
            $("#bro_type").append(tdata[1]);
            $("#back_pagelist").html(tdata[2]);
          }
        },
      });
    }
  }

  $(document).on("click", ".cont_main_in ul li", function(){
    idx = $(this).find("#sam_li_idx").val();
    // alert(idx);
    location.href = "sample_view.php?idx="+idx;
  });

  // 활용사례 유형 선택
  $(document).on("click",".tab_btn ul li",function(){
      // var cate = $(this).attr("class").replace("btn_","");
      $(this).addClass('on');
      $(this).siblings().removeClass('on');
      var cate = $(this).val();
      // alert(kind);
      var fdata = new FormData();
      fdata.append("cate", cate);
      fdata.append("mode","sample_list");

      $.ajax({
        type: "POST",
        data: fdata,
        contentType: false,
        processData: false,
        url: "/inc/about_process.php",
        success: function (data) {
          // console.log(data);
          if (data) {
            var tdata = data.split("|");
            console.log(tdata[1]);
            if (tdata) {
               $(".cont_main_in ul").html(tdata[0]);
               $("#sample_page").val(tdata[2]);
               $("#bro_type").append(tdata[1]);
            }
          }
        },
      });
   });

   // 활용사례 검색
  $(document).on("click", ".cont_search button", function(){
    search = $(".input_search").val();
    // alert(search);
    // return false;
    var fdata = new FormData();
    fdata.append("search",search);
    fdata.append("mode","sample_list");

    $.ajax({
      type: "POST",
      data: fdata,
      contentType: false,
      processData: false,
      url: "/inc/about_process.php",
      success: function (data) {
        // console.log(data);
        if (data) {
          var tdata = data.split("|");
          console.log(tdata[2]);
          if (tdata) {
            $(".cont_main_in ul").html(tdata[0]);
            $("#sample_page").val(tdata[2]);
          }
        }
      },
    });
  });

  // 활용사례 더보기
  $(document).on("click", "#sample_more", function(){
    cate = $("#sample_cate").val();
    search = $(".input_search").val();

    var fdata = new FormData();
    page = $("#sample_page").val();
    fdata.append("p",page);

    if(cate){
      fdata.append("cate",cate);
    }

    if(search){
      fdata.append("search",search);
    }

    fdata.append("mode","sample_more");

    $.ajax({
      type: "POST",
      data: fdata,
      contentType: false,
      processData: false,
      url: "/inc/about_process.php",
      success: function (data) {
        // console.log(data);
        if (data) {
          var tdata = data.split("|");
          console.log(tdata[1]);
          if (tdata) {
            $(".cont_main_in ul").append(tdata[0]);
            $("#sample_page").val(tdata[2]);
          }
        }
      },
    });
  });

  //FAQs 검색
  $(document).on("click", ".faq_search_box button", function(){
    search = $(".input_search").val();
    // alert(search);
    // return false;
    var fdata = new FormData();
    fdata.append("search",search);
    fdata.append("mode","faq_list");

    $.ajax({
      type: "POST",
      data: fdata,
      contentType: false,
      processData: false,
      url: "/inc/about_process.php",
      success: function (data) {
        // console.log(data);
        if (data) {
          var tdata = data.split("|");
          console.log(tdata[1]);
          if (tdata) {
            $(".faq_list").html(tdata[0]);
            $(".faq_top").append(tdata[1]);
          }
        }
      },
    });
  });

  $(document).on("mouseenter",".depth_01 strong.depth_01_link", function(){
    $(this).next(".depth_02_area").show();
  });

  $(document).on("mouseleave", ".depth_01", function(){
    $(this).find(".depth_02_area").hide();
  });
  
  $(document).on("click",".rb_nav_member button:eq(0)", function(){
    $('.rew_layer_pay_01').show();
  });

  $(document).on("click",".rb_nav_member button:eq(1)", function(){
    if (GetCookie("user_id") == null) {
      $(".rew_layer_login").show();
    } else {
      location.href = "/team/index.php";
    }
  });

    //로그인하기 엔터키 처리
    $("input").keypress(function (e) {
      var id = $(this).attr("id");
      if (id == "z1" || id == "z2") {
        if (e.keyCode == 13) {
          $("#loginbtn").trigger("click");
        }
      }
    });
  
//로그인버튼
$(document).on("click", "#loginbtn", function(){
  console.log($("#z1").val());
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

  // if ($("input[name='chk_login']").is(":checked") == true) {
  //   fdata.append("chk_login", true);
  // }

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
          window.location.pathname == "/index.php" ||
          window.location.pathname == "/index2.php"
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


  // 사용자 매뉴얼 사이드 업무 클릭
  $(document).on("click",".side_nav ul li",function(){
    id = $(this).attr("id");
    id_arr = id.split("_");
    service = $("#service_type").val();

    idx = $(this).val();
    if($(".side_nav li").hasClass("on")==true){
      $(".side_nav li").removeClass("on");
    }
    var fdata = new FormData();
    fdata.append("mode","manual_view");
    fdata.append("idx",idx);
    fdata.append("service",service);

    $.ajax({
      type: "POST",
      data: fdata,
      contentType: false,
      processData: false,
      url: "/inc/about_process.php",
      success: function (data) {
        // console.log(data);
        if (data) {
          $("#"+service+"_"+idx).addClass("on");
          $(".manual_content").html(data);
        }
      },
    });
  });

  // 사용자 매뉴얼 종류 링크
  $(document).on("click", ".manual_in li", function(){
      link_kind = $(this).attr("id");
      location.href = link_kind+".php";
      return false;
  }); 

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

//온라인 문의하기 레이어
$(document).on("click","button[class=btn_layer_ask]",function(){
    $(".t_layer.rew_layer_ask").css("display","block");
});

$(document).on("click","button[id=inquiry_cancel]",function(){
  $(".t_layer.rew_layer_ask").css("display","none");
});

$(document).on("click","#email_inquiry", function(){
  id = $("#k1").val();
  email = $("#k3").val();
  contents = $("#k5").val();
  security = $("#k6").val();

  if(!id){
    alert('이름을 입력해주세요.');
    $("#k1").focus();
    return false
  }


  if(!email){
    alert('이메일을 입력해주세요.');
    $("#k3").focus();
    return false
  }

  if(!contents){
    alert('내용을 입력해주세요.');
    $("#k5").focus();
    return false
  }

  if(!security){
    alert('보안문자를 올바르게 입력해주세요.');
    $("#k6").focus();
    return false
  }

  var fdata = new FormData();
  fdata.append("name",id);
  fdata.append("email",email);
  fdata.append("contents",contents);
  fdata.append("security",security);
  fdata.append("mode","email_inquiry");

  $.ajax({
    type: "POST",
    data: fdata,
    contentType: false,
    processData: false,
    url: "/inc/about_process.php",
    success: function (data) {
      console.log(data);
      if (data) {
        if(data == "complete"){
          alert('문의 메일이 발송되었습니다.');
        }
        $(".t_layer.rew_layer_ask").hide();
        $("input[id^=k]").val("");
      }
    },
  });
});

});