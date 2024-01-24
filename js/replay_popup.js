$(document).ready(function () {


  //tab_menu
  $(document).on("click", ".replay_tab_navi li", function () {
    $(this).addClass('on');
    $(this).siblings().removeClass('on');
  });
  
  $(document).on("click", ".day", function () {
    $('.replay_day').addClass('on');
    $('.replay_week').removeClass('on');
    $('.replay_month').removeClass('on');
    $('.replay_year').removeClass('on');
    $('.replay_day').siblings().removeClass('on');
  });

  $(document).on("click", ".week", function () {
    $('.replay_week').addClass('on');
    $('.replay_day').removeClass('on');
    $('.replay_month').removeClass('on');
    $('.replay_year').removeClass('on');
    $('.replay_week').siblings().removeClass('on');
  });

  $(document).on("click", ".month", function () {
    $('.replay_month').addClass('on');
    $('.replay_day').removeClass('on');
    $('.replay_week').removeClass('on');
    $('.replay_year').removeClass('on');
    $('.replay_month').siblings().removeClass('on');
  });

  $(document).on("click", ".year", function () {
    $('.replay_year').addClass('on');
    $('.replay_week').removeClass('on');
    $('.replay_month').removeClass('on');
    $('.replay_day').removeClass('on');
    $('.replay_year').siblings().removeClass('on');
  });



 
});