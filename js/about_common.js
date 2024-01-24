$(document).ready(function () {
  $(".rb_nav_mb_btn button, .rb_nav_mb_deam").click(function () {
    $(".rb_nav_depth").toggleClass("on");
    $(".rb_nav_member").toggleClass("on");
    $(".rb_nav_mb_deam").toggleClass("on");
  });
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
    location.href = "/team/index.php";
  });

  