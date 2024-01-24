$(document).ready(function(){

  setTimeout(function(){
    $(".section230").addClass("on");
  },300);

  var top_h = $(window).height();
  var w_top	= $(window).scrollTop();
  var top10 = $(".section230").offset();
  var top10h = $(".section230").outerHeight();
  var top11 = $(".section231_right").offset();
  var top11h = $(".section231_right").outerHeight();
  var top12 = $(".section232_right").offset();
  var top12h = $(".section232_right").outerHeight();
  var top13 = $(".section233_right").offset();
  var top13h = $(".section233_right").outerHeight();
  var top14 = $(".section234_right").offset();
  var top14h = $(".section234_right").outerHeight();
  var top15 = $(".section235").offset();
  var top15h = $(".section235").outerHeight();

  if((w_top) < (top10h+top10.top)){
    $(".section230").addClass("on");
  }else{
    $(".section230").removeClass("on");
  }

  if(((top_h+w_top) > (top11.top)) && ((w_top) < (top11h+top11.top))){
    $(".section231").addClass("on");
  }else{
    $(".section231").removeClass("on");
  }

  if(((top_h+w_top) > (top12.top)) && ((w_top) < (top12h+top12.top))){
    $(".section232").addClass("on");
  }else{
    $(".section232").removeClass("on");
  }

  if(((top_h+w_top) > (top13.top)) && ((w_top) < (top13h+top13.top))){
    $(".section233").addClass("on");
  }else{
    $(".section233").removeClass("on");
  }

  if(((top_h+w_top) > (top14.top)) && ((w_top) < (top14h+top14.top))){
    $(".section234").addClass("on");
  }else{
    $(".section234").removeClass("on");
  }

  if(((top_h+w_top) > (top15.top)) && ((w_top) < (top15h+top15.top))){
    $(".section235").addClass("on");
  }else{
    $(".section235").removeClass("on");
  }

  $(window).scroll(function () {
    var top_h = $(window).height();
    var w_top	= $(window).scrollTop();
    var top10 = $(".section230").offset();
    var top10h = $(".section230").outerHeight();
    var top11 = $(".section231_right").offset();
    var top11h = $(".section231_right").outerHeight();
    var top12 = $(".section232_right").offset();
    var top12h = $(".section232_right").outerHeight();
    var top13 = $(".section233_right").offset();
    var top13h = $(".section233_right").outerHeight();
    var top14 = $(".section234_right").offset();
    var top14h = $(".section234_right").outerHeight();
    var top15 = $(".section235").offset();
    var top15h = $(".section235").outerHeight();

    if((w_top) < (top10h+top10.top)){
      $(".section230").addClass("on");
    }else{
      $(".section230").removeClass("on");
    }

    if(((top_h+w_top) > (top11.top)) && ((w_top) < (top11h+top11.top))){
      $(".section231").addClass("on");
    }else{
      $(".section231").removeClass("on");
    }

    if(((top_h+w_top) > (top12.top)) && ((w_top) < (top12h+top12.top))){
      $(".section232").addClass("on");
    }else{
      $(".section232").removeClass("on");
    }

    if(((top_h+w_top) > (top13.top)) && ((w_top) < (top13h+top13.top))){
      $(".section233").addClass("on");
    }else{
      $(".section233").removeClass("on");
    }

    if(((top_h+w_top) > (top14.top)) && ((w_top) < (top14h+top14.top))){
      $(".section234").addClass("on");
    }else{
      $(".section234").removeClass("on");
    }

    if(((top_h+w_top) > (top15.top)) && ((w_top) < (top15h+top15.top))){
      $(".section235").addClass("on");
    }else{
      $(".section235").removeClass("on");
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
