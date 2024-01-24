$(document).ready(function(){

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

  $(".faq_q").click(function(){
    $(".faq_q").not(this).next(".faq_a").slideUp(200);
    $(this).next(".faq_a").slideToggle(200);
    $(this).toggleClass("on");
  });



});
