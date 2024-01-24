$(document).ready(function(){

  setTimeout(function(){
    $(".section220").addClass("on");
  },300);

  var top_h = $(window).height();
  var w_top	= $(window).scrollTop();
  var top10 = $(".section220").offset();
  var top10h = $(".section220").outerHeight();
  var top11 = $(".section221_right").offset();
  var top11h = $(".section221_right").outerHeight();
  var top12 = $(".section222_right").offset();
  var top12h = $(".section222_right").outerHeight();
  var top13 = $(".section223_right").offset();
  var top13h = $(".section223_right").outerHeight();
  var top14 = $(".section224_right").offset();
  var top14h = $(".section224_right").outerHeight();
  var top16 = $(".section226_right").offset();
  var top16h = $(".section226_right").outerHeight();
  var top17 = $(".section227_right").offset();
  var top17h = $(".section227_right").outerHeight();
  var top18 = $(".section228_right").offset();
  var top18h = $(".section228_right").outerHeight();
  var top15 = $(".section225").offset();
  var top15h = $(".section225").outerHeight();

  if((w_top) < (top10h+top10.top)){
    $(".section220").addClass("on");
  }else{
    $(".section220").removeClass("on");
  }

  if(((top_h+w_top) > (top11.top)) && ((w_top) < (top11h+top11.top))){
    $(".section221").addClass("on");
  }else{
    $(".section221").removeClass("on");
  }

  if(((top_h+w_top) > (top12.top)) && ((w_top) < (top12h+top12.top))){
    $(".section222").addClass("on");
  }else{
    $(".section222").removeClass("on");
  }

  if(((top_h+w_top) > (top13.top)) && ((w_top) < (top13h+top13.top))){
    $(".section223").addClass("on");
  }else{
    $(".section223").removeClass("on");
  }

  if(((top_h+w_top) > (top14.top)) && ((w_top) < (top14h+top14.top))){
    $(".section224").addClass("on");
  }else{
    $(".section224").removeClass("on");
  }

  if(((top_h+w_top) > (top16.top)) && ((w_top) < (top16h+top16.top))){
    $(".section226").addClass("on");
  }else{
    $(".section226").removeClass("on");
  }

  if(((top_h+w_top) > (top17.top)) && ((w_top) < (top17h+top17.top))){
    $(".section227").addClass("on");
  }else{
    $(".section227").removeClass("on");
  }

  if(((top_h+w_top) > (top18.top)) && ((w_top) < (top18h+top18.top))){
    $(".section228").addClass("on");
  }else{
    $(".section228").removeClass("on");
  }

  if(((top_h+w_top) > (top15.top)) && ((w_top) < (top15h+top15.top))){
    $(".section225").addClass("on");
  }else{
    $(".section225").removeClass("on");
  }

  $(window).scroll(function () {
    var top_h = $(window).height();
    var w_top	= $(window).scrollTop();
    var top10 = $(".section220").offset();
    var top10h = $(".section220").outerHeight();
    var top11 = $(".section221_right").offset();
    var top11h = $(".section221_right").outerHeight();
    var top12 = $(".section222_right").offset();
    var top12h = $(".section222_right").outerHeight();
    var top13 = $(".section223_right").offset();
    var top13h = $(".section223_right").outerHeight();
    var top14 = $(".section224_right").offset();
    var top14h = $(".section224_right").outerHeight();
    var top16 = $(".section226_right").offset();
    var top16h = $(".section226_right").outerHeight();
    var top17 = $(".section227_right").offset();
    var top17h = $(".section227_right").outerHeight();
    var top18 = $(".section228_right").offset();
    var top18h = $(".section228_right").outerHeight();
    var top15 = $(".section225").offset();
    var top15h = $(".section225").outerHeight();

    if((w_top) < (top10h+top10.top)){
      $(".section220").addClass("on");
    }else{
      $(".section220").removeClass("on");
    }

    if(((top_h+w_top) > (top11.top)) && ((w_top) < (top11h+top11.top))){
      $(".section221").addClass("on");
    }else{
      $(".section221").removeClass("on");
    }

    if(((top_h+w_top) > (top12.top)) && ((w_top) < (top12h+top12.top))){
      $(".section222").addClass("on");
    }else{
      $(".section222").removeClass("on");
    }

    if(((top_h+w_top) > (top13.top)) && ((w_top) < (top13h+top13.top))){
      $(".section223").addClass("on");
    }else{
      $(".section223").removeClass("on");
    }

    if(((top_h+w_top) > (top14.top)) && ((w_top) < (top14h+top14.top))){
      $(".section224").addClass("on");
    }else{
      $(".section224").removeClass("on");
    }

    if(((top_h+w_top) > (top16.top)) && ((w_top) < (top16h+top16.top))){
      $(".section226").addClass("on");
    }else{
      $(".section226").removeClass("on");
    }

    if(((top_h+w_top) > (top17.top)) && ((w_top) < (top17h+top17.top))){
      $(".section227").addClass("on");
    }else{
      $(".section227").removeClass("on");
    }

    if(((top_h+w_top) > (top18.top)) && ((w_top) < (top18h+top18.top))){
      $(".section228").addClass("on");
    }else{
      $(".section228").removeClass("on");
    }

    if(((top_h+w_top) > (top15.top)) && ((w_top) < (top15h+top15.top))){
      $(".section225").addClass("on");
    }else{
      $(".section225").removeClass("on");
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
