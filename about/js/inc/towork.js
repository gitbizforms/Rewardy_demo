$(document).ready(function(){

  setTimeout(function(){
    $(".section210").addClass("on");
  },300);

  var top_h = $(window).height();
  var w_top	= $(window).scrollTop();
  var top10 = $(".section210").offset();
  var top10h = $(".section210").outerHeight();
  var top11 = $(".section211_right").offset();
  var top11h = $(".section211_right").outerHeight();
  var top12 = $(".section212_right").offset();
  var top12h = $(".section212_right").outerHeight();
  var top13 = $(".section213_right").offset();
  var top13h = $(".section213_right").outerHeight();
  var top14 = $(".section214_right").offset();
  var top14h = $(".section214_right").outerHeight();
  var top16 = $(".section216_right").offset();
  var top16h = $(".section216_right").outerHeight();
  var top17 = $(".section217_right").offset();
  var top17h = $(".section217_right").outerHeight();
  var top15 = $(".section215").offset();
  var top15h = $(".section215").outerHeight();

  if((w_top) < (top10h+top10.top)){
    $(".section210").addClass("on");
  }else{
    $(".section210").removeClass("on");
  }

  if(((top_h+w_top) > (top11.top)) && ((w_top) < (top11h+top11.top))){
    $(".section211").addClass("on");
  }else{
    $(".section211").removeClass("on");
  }

  if(((top_h+w_top) > (top12.top)) && ((w_top) < (top12h+top12.top))){
    $(".section212").addClass("on");
  }else{
    $(".section212").removeClass("on");
  }

  if(((top_h+w_top) > (top13.top)) && ((w_top) < (top13h+top13.top))){
    $(".section213").addClass("on");
  }else{
    $(".section213").removeClass("on");
  }

  if(((top_h+w_top) > (top14.top)) && ((w_top) < (top14h+top14.top))){
    $(".section214").addClass("on");
  }else{
    $(".section214").removeClass("on");
  }

  if(((top_h+w_top) > (top16.top)) && ((w_top) < (top16h+top16.top))){
    $(".section216").addClass("on");
  }else{
    $(".section216").removeClass("on");
  }

  if(((top_h+w_top) > (top17.top)) && ((w_top) < (top17h+top17.top))){
    $(".section217").addClass("on");
  }else{
    $(".section217").removeClass("on");
  }

  if(((top_h+w_top) > (top15.top)) && ((w_top) < (top15h+top15.top))){
    $(".section215").addClass("on");
  }else{
    $(".section215").removeClass("on");
  }

  $(window).scroll(function () {
    var top_h = $(window).height();
    var w_top	= $(window).scrollTop();
    var top10 = $(".section210").offset();
    var top10h = $(".section210").outerHeight();
    var top11 = $(".section211_right").offset();
    var top11h = $(".section211_right").outerHeight();
    var top12 = $(".section212_right").offset();
    var top12h = $(".section212_right").outerHeight();
    var top13 = $(".section213_right").offset();
    var top13h = $(".section213_right").outerHeight();
    var top14 = $(".section214_right").offset();
    var top14h = $(".section214_right").outerHeight();
    var top16 = $(".section216_right").offset();
    var top16h = $(".section216_right").outerHeight();
    var top17 = $(".section217_right").offset();
    var top17h = $(".section217_right").outerHeight();
    var top15 = $(".section215").offset();
    var top15h = $(".section215").outerHeight();

    if((w_top) < (top10h+top10.top)){
      $(".section210").addClass("on");
    }else{
      $(".section210").removeClass("on");
    }

    if(((top_h+w_top) > (top11.top)) && ((w_top) < (top11h+top11.top))){
      $(".section211").addClass("on");
    }else{
      $(".section211").removeClass("on");
    }

    if(((top_h+w_top) > (top12.top)) && ((w_top) < (top12h+top12.top))){
      $(".section212").addClass("on");
    }else{
      $(".section212").removeClass("on");
    }

    if(((top_h+w_top) > (top13.top)) && ((w_top) < (top13h+top13.top))){
      $(".section213").addClass("on");
    }else{
      $(".section213").removeClass("on");
    }

    if(((top_h+w_top) > (top14.top)) && ((w_top) < (top14h+top14.top))){
      $(".section214").addClass("on");
    }else{
      $(".section214").removeClass("on");
    }

    if(((top_h+w_top) > (top16.top)) && ((w_top) < (top16h+top16.top))){
      $(".section216").addClass("on");
    }else{
      $(".section216").removeClass("on");
    }

    if(((top_h+w_top) > (top17.top)) && ((w_top) < (top17h+top17.top))){
      $(".section217").addClass("on");
    }else{
      $(".section217").removeClass("on");
    }

    if(((top_h+w_top) > (top15.top)) && ((w_top) < (top15h+top15.top))){
      $(".section215").addClass("on");
    }else{
      $(".section215").removeClass("on");
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
