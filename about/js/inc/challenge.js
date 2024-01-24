$(document).ready(function(){

  setTimeout(function(){
    $(".section240").addClass("on");
  },300);

  var top_h = $(window).height();
  var w_top	= $(window).scrollTop();
  var top10 = $(".section240").offset();
  var top10h = $(".section240").outerHeight();
  var top11 = $(".section241_right").offset();
  var top11h = $(".section241_right").outerHeight();
  var top12 = $(".section242_right").offset();
  var top12h = $(".section242_right").outerHeight();
  var top13 = $(".section243_right").offset();
  var top13h = $(".section243_right").outerHeight();
  var top14 = $(".section244_right").offset();
  var top14h = $(".section244_right").outerHeight();
  var top15 = $(".section245").offset();
  var top15h = $(".section245").outerHeight();

  if((w_top) < (top10h+top10.top)){
    $(".section240").addClass("on");
  }else{
    $(".section240").removeClass("on");
  }

  if(((top_h+w_top) > (top11.top)) && ((w_top) < (top11h+top11.top))){
    $(".section241").addClass("on");
  }else{
    $(".section241").removeClass("on");
  }

  if(((top_h+w_top) > (top12.top)) && ((w_top) < (top12h+top12.top))){
    $(".section242").addClass("on");
  }else{
    $(".section242").removeClass("on");
  }

  if(((top_h+w_top) > (top13.top)) && ((w_top) < (top13h+top13.top))){
    $(".section243").addClass("on");
  }else{
    $(".section243").removeClass("on");
  }

  if(((top_h+w_top) > (top14.top)) && ((w_top) < (top14h+top14.top))){
    $(".section244").addClass("on");
  }else{
    $(".section244").removeClass("on");
  }

  if(((top_h+w_top) > (top15.top)) && ((w_top) < (top15h+top15.top))){
    $(".section245").addClass("on");
  }else{
    $(".section245").removeClass("on");
  }

  $(window).scroll(function () {
    var top_h = $(window).height();
    var w_top	= $(window).scrollTop();
    var top10 = $(".section240").offset();
    var top10h = $(".section240").outerHeight();
    var top11 = $(".section241_right").offset();
    var top11h = $(".section241_right").outerHeight();
    var top12 = $(".section242_right").offset();
    var top12h = $(".section242_right").outerHeight();
    var top13 = $(".section243_right").offset();
    var top13h = $(".section243_right").outerHeight();
    var top14 = $(".section244_right").offset();
    var top14h = $(".section244_right").outerHeight();
    var top15 = $(".section245").offset();
    var top15h = $(".section245").outerHeight();

    if((w_top) < (top10h+top10.top)){
      $(".section240").addClass("on");
    }else{
      $(".section240").removeClass("on");
    }

    if(((top_h+w_top) > (top11.top)) && ((w_top) < (top11h+top11.top))){
      $(".section241").addClass("on");
    }else{
      $(".section241").removeClass("on");
    }

    if(((top_h+w_top) > (top12.top)) && ((w_top) < (top12h+top12.top))){
      $(".section242").addClass("on");
    }else{
      $(".section242").removeClass("on");
    }

    if(((top_h+w_top) > (top13.top)) && ((w_top) < (top13h+top13.top))){
      $(".section243").addClass("on");
    }else{
      $(".section243").removeClass("on");
    }

    if(((top_h+w_top) > (top14.top)) && ((w_top) < (top14h+top14.top))){
      $(".section244").addClass("on");
    }else{
      $(".section244").removeClass("on");
    }

    if(((top_h+w_top) > (top15.top)) && ((w_top) < (top15h+top15.top))){
      $(".section245").addClass("on");
    }else{
      $(".section245").removeClass("on");
    }

  });

  $(".depth_01 strong.depth_01_link").mouseenter(function(){
    $(this).next(".depth_02_area").show();
  });
  $(".depth_01").mouseleave(function(){
    $(this).find(".depth_02_area").hide();
  });

  $(".rb_nav_mb_btn button, .rb_nav_mb_deam").click(function(){
    $(".rb_nav_depth").toggleClass("on");
    $(".rb_nav_member").toggleClass("on");
    $(".rb_nav_mb_deam").toggleClass("on");
  });



});
