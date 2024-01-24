$(document).ready(function () {

  $(".rew_mypage_08").click(function (e) {
    if (!$(e.target).is(".rew_mypage_08 *")) {
      $(".rew_box").removeClass("on");
      $(".rew_menu_onoff button").removeClass("on");
    }
  });

  $(".tdw_write_btns ul").slick({
    slidesToShow: 6,
    slidesToScroll: 4,
    arrows: true,
    infinite: false,
    speed: 500,
    autoplay: false,
    dots: false,
    responsive: [
      {
        breakpoint: 420,
        settings: {
          slidesToShow: 4,
          slidesToScroll: 4,
        }
      }
    ]
  });

  //$(".layer_user_slc_list_in").touchFlow({
  //axis : "x"
  //});

  $(".layer_user_slc_list_in ul").mousewheel(function (event, delta) {
    this.scrollLeft -= (delta * 30);
    event.preventDefault();
  });

  $(".tdw_list ul").sortable({
    axis: "y",
    opacity: 0.7,
    zIndex: 9999,
    handle: ".tdw_list_drag",
    //placeholder:"sort_empty",
    cursor: "move"
  });
  //$(".tdw_list ul").disableSelection();


  $(".rew_grid_onoff_in .btn_switch").click(function () {
    var switchon = $(this);
    if (switchon.hasClass("on")) {
      $(this).removeClass("on");
      $(this).prev("em").removeClass("on");
    } else {
      $(".btn_switch").not($(this)).removeClass("on");
      $(".btn_switch").not($(this)).prev("em").removeClass("on");
      $(this).addClass("on");
      $(this).prev("em").addClass("on");
    }
  });

  $(".rew_conts_list_in ul li button").click(function () {
    $(this).parent("li").toggleClass("on");
  });

  $(".rew_btn_icons_more").click(function () {
    $(".rew_icons").toggle();
  });

  $(".rew_mypage_tab_04 a").click(function () {
    $(".rew_mypage_tab_04 li").removeClass("on");
    $(this).parent("li").addClass("on");
  });


  setTimeout(function () {
    $(".rew_box").addClass("on");
    $(".rew_menu_onoff button").addClass("on");

    var bar_t = $(".rew_mypage_section .live_list_today_bar strong").text();
    var bar_b = $(".rew_mypage_section .live_list_today_bar span").text();
    var bar_w = bar_t / bar_b * 100;
    $(".rew_mypage_section .live_list_today_bar strong").css({
      width: bar_w + "%"
    });

  }, 400);

  setTimeout(function () {
    $(".tabs_on").addClass("now_01");
  }, 1100);

  $(".btn_next_step_02").click(function () {
    $(".rew_cha_step_01").addClass("step_z");
    $(".rew_cha_step_02").addClass("step_z");
    $(".rew_cha_step_03").removeClass("step_z");
    $(".rew_cha_step_01").animate({
      "left": -100 + "%"
    }, 700);
    $(".rew_cha_step_02").animate({
      "left": 0 + "%"
    }, 700);
    $(".rew_cha_step_03").animate({
      "left": 100 + "%"
    }, 700);

    setTimeout(function () {
      $(".rew_cha_step_01").animate({
        scrollTop: 0
      }, 0);
      $(".rew_cha_step_02").animate({
        scrollTop: 0
      }, 0);
      $(".rew_cha_step_03").animate({
        scrollTop: 0
      }, 0);
    }, 200);

    setTimeout(function () {
      $(".tabs_on").addClass("now_02");
    }, 1100);
  });
  $(".btn_prev_step_01").click(function () {
    $(".rew_cha_step_01").addClass("step_z");
    $(".rew_cha_step_02").addClass("step_z");
    $(".rew_cha_step_03").removeClass("step_z");
    $(".rew_cha_step_01").animate({
      "left": 0 + "%"
    }, 700);
    $(".rew_cha_step_02").animate({
      "left": 100 + "%"
    }, 700);
    $(".rew_cha_step_03").animate({
      "left": -100 + "%"
    }, 700);

    setTimeout(function () {
      $(".rew_cha_step_01").animate({
        scrollTop: 0
      }, 0);
      $(".rew_cha_step_02").animate({
        scrollTop: 0
      }, 0);
      $(".rew_cha_step_03").animate({
        scrollTop: 0
      }, 0);
    }, 200);

    setTimeout(function () {
      $(".tabs_on").removeClass("now_02");
    }, 1100);
  });
  $(".btn_next_step_03").click(function () {
    $(".rew_cha_step_01").removeClass("step_z");
    $(".rew_cha_step_02").addClass("step_z");
    $(".rew_cha_step_03").addClass("step_z");
    $(".rew_cha_step_01").animate({
      "left": 100 + "%"
    }, 700);
    $(".rew_cha_step_02").animate({
      "left": -100 + "%"
    }, 700);
    $(".rew_cha_step_03").animate({
      "left": 0 + "%"
    }, 700);

    setTimeout(function () {
      $(".rew_cha_step_01").animate({
        scrollTop: 0
      }, 0);
      $(".rew_cha_step_02").animate({
        scrollTop: 0
      }, 0);
      $(".rew_cha_step_03").animate({
        scrollTop: 0
      }, 0);
    }, 200);

    setTimeout(function () {
      $(".tabs_on").addClass("now_03");
    }, 1100);
  });


  $(".sl_slc .btn_sort_on").click(function () {
    $(".sl_slc").addClass("on");
  });
  $(".sl_slc").mouseleave(function () {
    $(".sl_slc").removeClass("on");
  });
  $(".sl_slc ul li button").click(function () {
    $(".sl_slc").removeClass("on");
  });
  $(".btn_tdw_search").click(function () {
    $(".search_layer").show();
  });
  $(".sl_close button").click(function () {
    $(".search_layer").hide();
  });

  $(".party_link_layer .ldl_chk button").click(function () {
    $(this).closest(".ldl_box").toggleClass("on");
  });
  $(".pll_close button").click(function () {
    $(".party_link_layer").hide();
  });
  $(".tdw_list_party_link").click(function () {
    $(".party_link_layer").show();
  });

  $(".rew_menu_onoff button").click(function () {
    var thisonoff = $(this);
    if (thisonoff.hasClass("on")) {
      thisonoff.removeClass("on");
      $(".rew_box").removeClass("on");

    } else {
      thisonoff.addClass("on");
      $(".rew_box").addClass("on");

    }
  });


  $(".rew_conts_scroll_08").scroll(function () {
    var tbt = $(".rew_todaywork").offset().top;
    if (tbt < 140) {
      $(".tdw_date").addClass("pos_fix");
    } else {
      $(".tdw_date").removeClass("pos_fix");
    }

  });
  $(".list_area").scroll(function () {
    var rct = $(".list_area_in").position().top;
    if (rct < 60) {
      $(".layer_result_right").addClass("pos_fix");
    } else {
      $(".layer_result_right").removeClass("pos_fix");
    }
  });

  $(".layer_report .report_area").scroll(function () {
    var rct = $(".layer_report .report_area_in").position().top;
    if (rct < 60) {
      $(".layer_report .layer_result_right").addClass("pos_fix");
    } else {
      $(".layer_report .layer_result_right").removeClass("pos_fix");
    }
  });

  $(".layer_challenge .report_area").scroll(function () {
    var rat = $(".layer_challenge .report_area_in").position().top;
    if (rat < 50) {
      $(".layer_challenge .layer_result_right").addClass("pos_fix");
    } else {
      $(".layer_challenge .layer_result_right").removeClass("pos_fix");
    }
  });


  $("#go_view_01").click(function () {
    var offset1 = $(".rew_conts_scroll_06").offset();
    $(".rew_conts_scroll_06").animate({
      scrollTop: 0
    }, 400 + (offset1.top / 2));
  });
  var offset2 = $(".rew_cha_view_result").position();
  $("#go_view_02").click(function () {
    var offset1 = $(".rew_conts_scroll_06").offset();
    var offset_sum = Math.abs(offset1.top - offset2.top);
    setTimeout(function () {
      $(".rew_conts_scroll_06").animate({
        scrollTop: offset2.top - 155
      }, offset_sum / 4 + 200);
    }, 100);
  });
  var offset3 = $(".rew_cha_view_masage").position();
  $("#go_view_03").click(function () {
    var offset1 = $(".rew_conts_scroll_06").offset();
    //console.log(offset2.top);
    var offset_sum = Math.abs(offset1.top - offset3.top);
    setTimeout(function () {
      $(".rew_conts_scroll_06").animate({
        scrollTop: offset3.top - 155
      }, offset_sum / 4 + 200);
    }, 100);
  });


  $(".conts_tab ul li button").click(function () {
    var drM_li = $(this).parent("li").index();

    var offt = $(".conts_main > div:eq(" + drM_li + ")").offset();
    var tabt = $(".conts_tab").offset();
    var matht = Math.abs(tabt.top - offt.top);
    $("html, body").animate({
      scrollTop: offt.top - 101
    }, matht / 8 + 200);

    return false;
  });

  $(".layer_user_info dt .btn_team_toggle").click(function () {
    $(this).parent().parent("dl").toggleClass("on");
  });
  $(".layer_user_info dt .btn_team_slc").click(function () {
    if ($(this).hasClass("on")) {
      $(this).removeClass("on");
      $(this).parent().parent("dl").find("dd button").removeClass("on");
    } else {
      $(this).addClass("on");
      $(this).parent().parent("dl").find("dd button").addClass("on");
    }
  });

  $(".layer_user_info dd button").click(function () {
    $(this).toggleClass("on");
    if ($(".layer_user_info dd button").hasClass("on")) {
      $(".layer_user_submit").addClass("on");
    } else {
      $(".layer_user_submit").removeClass("on");
    }
  });

  $(".layer_test_01").click(function () {
    $("#layer_test_02").hide();
    $("#layer_test_03").hide();
    $("#layer_test_01").show();
    $(".layer_user_info dl").addClass("on");
    $(".layer_user_info dd button").removeClass("on");
    $(".layer_user_submit").removeClass("on");
    $(".layer_user_info").animate({
      scrollTop: 0
    }, 0);
  });

  $(".layer_test_02").click(function () {
    $("#layer_test_01").hide();
    $("#layer_test_03").hide();
    $("#layer_test_02").show();
    $(".layer_user_info dl").addClass("on");
    $(".layer_user_info dd button").removeClass("on");
    $(".layer_user_submit").removeClass("on");
    $(".layer_user_info").animate({
      scrollTop: 0
    }, 0);
  });

  $(".layer_test_03").click(function () {
    $("#layer_test_01").hide();
    $("#layer_test_02").hide();
    $("#layer_test_03").show();
    $(".layer_user_info dl").addClass("on");
    $(".layer_user_info dd button").removeClass("on");
    $(".layer_user_submit").removeClass("on");
    $(".layer_user_info").animate({
      scrollTop: 0
    }, 0);
  });

  $(".layer_user_cancel").click(function () {
    $(".layer_user").hide();
  });

  $(".layer_user_submit").click(function () {
    if ($(this).hasClass("on")) {
      $(".layer_user").hide();
    }
  });

  $("#open_layer_user").click(function () {
    $(".layer_user").show();
    $("#layer_test_02").hide();
    $("#layer_test_03").hide();
    $("#layer_test_01").show();
    $(".layer_user_info dl").addClass("on");
    $(".layer_user_info dd button").removeClass("on");
    $(".layer_user_submit").removeClass("on");
    $(".layer_user_info").animate({
      scrollTop: 0
    }, 0);
  });

  $(".list_function_type .type_list").click(function () {
    $(".list_function_type button").removeClass("on");
    $(this).addClass("on");
    $(".list_box .list_conts").removeClass("type_img");
    $(".list_box .list_conts").removeClass("type_on");
    $(".list_box .list_conts").addClass("type_list");
    $(".list_box .list_conts").addClass("type_on");
  });

  $(".list_function_type .type_img").click(function () {
    $(".list_function_type button").removeClass("on");
    $(this).addClass("on");
    $(".list_box .list_conts").removeClass("type_list");
    $(".list_box .list_conts").removeClass("type_on");
    $(".list_box .list_conts").addClass("type_img");
    $(".list_box .list_conts").addClass("type_on");
  });

  $(".list_conts .list_ul li button").click(function () {
    $(this).toggleClass("on");
  });

  $(".rew_cha_view_result .title_area .title_more").click(function () {
    $(".layer_result").show();
  });

  $(".rew_cha_view_result li button").click(function () {
    $(".layer_result").show();
  });

  $(".rew_cha_view_header .view_user li button").click(function () {
    $(".layer_result").show();
  });

  $(".layer_result .layer_close button").click(function () {
    $(".layer_result").hide();
  });

  $(".layer_result_user li button").click(function () {
    $(".layer_result_user li button").removeClass("on");
    $(this).addClass("on");
  });

  $(".rew_cha_view_masage .title_area .title_more").click(function () {
    $(".layer_masage").show();
  });

  $(".masage_zone").click(function () {
    $(".layer_masage").show();
  });

  $(".masage_area_in").click(function () {
    $(this).toggleClass("on");
  });

  $(".layer_masage .layer_close button").click(function () {
    $(".layer_masage").hide();
  });

  $(".layer_report .layer_close button").click(function () {
    $(".layer_report").hide();
  });

  $(".layer_challenge .layer_close button").click(function () {
    $(".layer_challenge").hide();
    $(".layer_challenge .report_cha .rew_cha_list_ul li").removeClass("sli");
  });

  $(".join_type_file .btns_cha_cancel").click(function () {
    $(".join_type_file").hide();
  });

  $(".join_type_masage .btns_cha_cancel").click(function () {
    $(".join_type_masage").hide();
  });

  $(".join_type_mix .btns_cha_cancel").click(function () {
    $(".join_type_mix").hide();
  });

  $(".btn_join_ok").click(function () {
    $(".layer_cha_join").show();
  });


  $(".btn_eval").click(function () {
    $(".layer_report").show();
  });

  $(".live_list_cha_tit, .live_list_cha_count").click(function () {
    $(".layer_challenge .report_cha .rew_cha_list_ul li").removeClass("sli");
    $(".layer_challenge").show();
    $(".layer_challenge .report_cha .rew_cha_list_ul li").each(function () {
      var tis = $(this);
      var tindex = $(this).index();
      setTimeout(function () {
        tis.addClass("sli");
      }, 600 + tindex * 200);
    });
  });

  $(".live_list .live_list_box").each(function () {
    var tis = $(this);
    var tindex = $(this).index();
    var bar_t = tis.find(".live_list_today_bar strong").text();
    var bar_b = tis.find(".live_list_today_bar span").text();
    var bar_w = bar_t / bar_b * 100;
    setTimeout(function () {
      tis.addClass("sli");
      tis.find(".live_list_today_bar strong").css({
        width: bar_w + "%"
      });
    }, 600 + tindex * 200);
  });


  $(".live_user_state li .live_user_state_circle").mouseenter(function () {
    $(".layer_state").removeClass("on");
    $(this).next(".layer_state").addClass("on");
    $(".live_list_box").removeClass("zindex");
    $(this).closest(".live_list_box").addClass("zindex");
  });

  $(".live_user_state li .live_user_state_circle").mouseleave(function () {
    $(".layer_state").removeClass("on");
    $(".live_list_box").removeClass("zindex");
  });

  $(".layer_report").hide();

  $("#write_tab_01").click(function () {
    $(".tdw_write_tab_in button").removeClass("on");
    $(this).addClass("on");
    $(".input_write").attr("placeholder", "업무를 입력해 주세요.");
    //$(".input_write").val("");
    $(".tdw_write_btns button").removeClass("on");
    $(".tdw_write_req").hide();
    $(".tdw_write_report").hide();
    $(".tdw_write_text_report").hide();
    $(".tdw_write_user_desc").hide();
    $(".tdw_write_date").show();
    $(".tdw_write_text").show();
    $(".tdw_write_btns").addClass("on");
  });

  $("#write_tab_02").click(function () {
    $(".tdw_write_tab_in button").removeClass("on");
    $(this).addClass("on");
    $(".input_write").attr("placeholder", "업무를 입력해 주세요.");
    //$(".input_write").val("");
    $(".tdw_write_btns").removeClass("on");
    $(".tdw_write_report").hide();
    $(".tdw_write_text_report").hide();
    $(".tdw_write_date").show();
    $(".tdw_write_req").show();
    $(".tdw_write_text").show();
    $(".tdw_write_user_desc").show();
  });

  $("#write_tab_03").click(function () {
    $(".tdw_write_tab_in button").removeClass("on");
    $(this).addClass("on");
    $(".input_write").attr("placeholder", "보고할 내용을 작성해 주세요.");
    //$(".input_write").val("");
    $(".tdw_write_btns").removeClass("on");
    $(".tdw_write_date").hide();
    $(".tdw_write_text").hide();
    $(".tdw_write_req").show();
    $(".tdw_write_report").show();
    $(".tdw_write_text_report").show();
    $(".tdw_write_user_desc").show();
  });

  $("#write_tab_04").click(function () {
    $(".tdw_write_tab_in button").removeClass("on");
    $(this).addClass("on");
    $(".input_write").attr("placeholder", "업무를 입력해 주세요.");
    //$(".input_write").val("");
    $(".tdw_write_btns").removeClass("on");
    $(".tdw_write_report").hide();
    $(".tdw_write_text_report").hide();
    $(".tdw_write_date").show();
    $(".tdw_write_req").show();
    $(".tdw_write_text").show();
    $(".tdw_write_user_desc").show();
  });

  $(".tdw_date_select .select_dd").click(function () {
    $(".tdw_date_select button").removeClass("on");
    $(this).addClass("on");
    $(".tdw_list_dd").show();
    $(".tdw_list_ww").hide();
    $(".tdw_list_mm").hide();
  });

  // 추가
  $(".tdw_tab_sort").mouseleave(function () {
    $(this).addClass("on");
    $(".tdw_new_select").show();
  });

  $(".tdw_date_select .select_ww").click(function () {
    $(".tdw_date_select button").removeClass("on");
    $(this).addClass("on");
    $(".tdw_list_dd").hide();
    $(".tdw_list_ww").show();
    $(".tdw_list_mm").hide();
  });

  $(".tdw_date_select .select_mm").click(function () {
    $(".tdw_date_select button").removeClass("on");
    $(this).addClass("on");
    $(".tdw_list_dd").hide();
    $(".tdw_list_ww").hide();
    $(".tdw_list_mm").show();
  });

  $(".btn_tdw_list_chk").click(function () {
    $(this).closest(".tdw_list_box").toggleClass("on");
  });

  $(".tdw_list_desc p").click(function () {
    $(this).closest(".tdw_list_desc").children(".tdw_list_regi").show();
  });

  $(".tdw_list .tdw_list_desc .tdw_list_regi button").click(function () {
    $(this).closest(".tdw_list_regi").hide();
  });

  

  $(".btn_req").click(function () {
    $(".layer_user").show();
    $("#layer_test_02").hide();
    $("#layer_test_03").hide();
    $("#layer_test_01").show();
    $(".layer_user_info dl").addClass("on");
    $(".layer_user_info dd button").removeClass("on");
    $(".layer_user_submit").removeClass("on");
    $(".layer_user_info").animate({
      scrollTop: 0
    }, 0);
  });



  $(".tdw_list .btn_regi_cancel").click(function () {
    $(this).closest(".tdw_list_memo_regi").hide();
  });

  $(".tdw_list .tdw_list_memo_conts_txt strong").click(function () {
    $(this).next(".tdw_list_memo_regi").show();
    var memo_width = $(this).parent(".tdw_list_memo_conts_txt").width();
    $(this).next(".tdw_list_memo_regi").css({
      "width": memo_width + 199
    });
  });

  $(".tdw_list .btn_memo_del").click(function () {
    $(this).closest(".tdw_list_memo_desc").remove();
  });

  $(".tdw_list .tdw_list_memo").click(function () {
    $(".layer_memo").show();
  });

  $(".layer_memo_cancel").click(function () {
    $(".layer_memo").hide();
  });

  $(".layer_memo_submit").click(function () {
    $(".layer_memo").hide();
    $(".tdw_list_memo").addClass("on");
  });

  $(".tdw_list .tdw_list_repeat").click(function () {
    $(this).next(".tdw_list_repeat_list").show();
  });

  $(".tdw_list .tdw_list_repeat_list button").click(function () {
    var this_text = $(this).text();
    $(this).closest(".tdw_list_repeat_box").addClass("on");
    $(this).closest(".tdw_list_repeat_box").find(".tdw_list_repeat").text(this_text);
    $(".tdw_list_repeat_list").hide();
  });

  $(".tdw_list .tdw_list_repeat_box").mouseleave(function () {
    $(".tdw_list_repeat_list").hide();
  });

  $("#loginbtn").click(function () {
    $(".rew_layer_login").hide();
    $(".layer_work").show();
  });

  $(".lw_off").click(function () {
    $(this).closest(".layer_work").hide();
  });

  $(".l100_off").click(function () {
    $(this).closest(".layer_100c").hide();
  });

  $(".tdw_list .tdw_list_100c").click(function () {
    $(".layer_100c").show();
  });

  $(".layer_100c_more .l100_btn .l100_on").click(function () {
    $(this).closest(".layer_100c").hide();
    $(".type_coin").show();
  });


  //0921 보고 열기/접기
  $(".tdw_list_report_area").each(function () {
    var rath = $(this);
    var rathle = rath.next(".tdw_list_memo_area").children().length;
    if (rathle > 0) {
      rath.find(".btn_list_report_onoff").addClass("memo_on");
    } else {
      rath.find(".btn_list_report_onoff").removeClass("memo_on");
    }
  });

  $(".btn_list_report_onoff").click(function () {
    $(this).parent().prev(".tdw_list_report_area_in").toggleClass("off");
    $(this).toggleClass("off");
  });

  //0831 메모 열기/접기
  $(".btn_list_memo_onoff").click(function () {
    $(this).parent().prev(".tdw_list_memo_area_in").toggleClass("off");
    $(this).toggleClass("off");
  });

  //0901 메모 열기/접기 보여지는 기준 : 메모 영역 높이기준이라 메모 삭제 시 다시 계산 필요
  setTimeout(function () {
    $(".tdw_list_memo_area_in").each(function () {
      var maih = $(this);
      if (maih.height() > 110) {
        maih.next($(".tdw_list_memo_onoff")).show();
      }
    });
  }, 400);

  //0901 임시 팝업
  // $(".tdw_write_btn").click(function () {
  //   $(".rew_popup").show();
  //   setTimeout(function () {
  //     $(".rew_popup").hide();
  //   }, 4700);
  // });


  //230803
  $(".tdw_list_o").click(function () {
    $(this).next(".tdw_list_1depth").show();
  });

  $(".tdw_list_r").click(function () {
    $(this).next(".tdw_list_2depth").show();
  });

  $(".tdw_list_more button").click(function () {
    $(this).not(".tdw_list_r").toggleClass("on");
  });

  $(".tdw_list_1depth").mouseleave(function () {
    $(".tdw_list_1depth").hide();
    $(".tdw_list_2depth").hide();
  });


  $(".tdw_list_1depth > ul > li > button").not(".tdw_list_r").mouseenter(function () {
    $(".tdw_list_2depth").hide();
  });

  //230808
  $(".btn_my_alert").click(function () {
    $(this).toggleClass("on");
    $(".layer_my_info").hide();
    $(".layer_my_alert").toggle();
  });

  $(".layer_my_alert").mouseleave(function () {
    $(".btn_my_alert").removeClass("on");
    $(".layer_my_alert").hide();
  });

  $(".btn_my_info").click(function () {
    $(".layer_my_alert").hide();
    $(".layer_my_info").toggle();
  });

  $(".layer_my_info").mouseleave(function () {
    $(".layer_my_info").hide();
  });

  $(".rew_head_my").mouseleave(function () {
    $(".btn_my_mail").removeClass("on");
    $(".btn_my_alert").removeClass("on");
    $(".layer_my_alert").hide();
    $(".layer_my_info").hide();
  });

  var ff_class = "";
  var ff_text = "";

  $(".btn_feeling_banner").click(function () {
    $(".feeling_first").show();
  });

  $(".ff_area button").click(function () {
    ff_class = $(this).attr("class");
    if ($(this).attr("class") == "btn_ff_01") {
      ff_text = "최고의";
    }
    if ($(this).attr("class") == "btn_ff_02") {
      ff_text = "뿌듯한";
    }
    if ($(this).attr("class") == "btn_ff_03") {
      ff_text = "기분 좋은";
    }
    if ($(this).attr("class") == "btn_ff_04") {
      ff_text = "감사한";
    }
    if ($(this).attr("class") == "btn_ff_05") {
      ff_text = "재밌는";
    }
    if ($(this).attr("class") == "btn_ff_06") {
      ff_text = "수고한";
    }
    if ($(this).attr("class") == "btn_ff_07") {
      ff_text = "무난한";
    }
    if ($(this).attr("class") == "btn_ff_08") {
      ff_text = "지친";
    }
    if ($(this).attr("class") == "btn_ff_09") {
      ff_text = "속상한";
    }
    $(".ff_area button").not(this).removeClass("on");
    $(this).addClass("on");
    $(".ff_bottom button").removeClass("btn_off").addClass("btn_on");
  });

  $(".ff_close button").click(function () {
    $(".feeling_first").hide();
    $(".ff_area button").removeClass("on");
    $(".ff_bottom button").removeClass("btn_on").addClass("btn_off");
  });

  $(".ff_bottom button").click(function () {
    if ($(this).hasClass("btn_on")) {
      $(".feeling_first").hide();
      $(".ff_area button").removeClass("on");
      $(".ff_bottom button").removeClass("btn_on").addClass("btn_off");
      $(".feeling_layer").show();
      $(".fl_area .fl_desc").removeClass().addClass("fl_desc");
      $(".fl_area .fl_desc").addClass(ff_class);
      $(".fl_area .fl_desc span").text(ff_text);
    } else {

    }
  });

  $(".fl_bottom button").click(function () {
    $(".feeling_layer").hide();
    $(".tdw_feeling_banner").removeClass().addClass("tdw_feeling_banner");
    $(".tdw_feeling_banner").addClass(ff_class);
    var inputfl = $(".fl_area .input_fl").val();
    $(".tdw_feeling_banner p").text(inputfl);
  });
  $(".fl_close button").click(function () {
    $(".feeling_layer").hide();
  });

  $(".btn_penalty_banner").click(function () {
    $(".penalty_layer").show();
  });
  $(".pl_close button").click(function () {
    $(".penalty_layer").hide();
  });
  $(".pl_img_on img").click(function () {
    $(".layer_cha_image").show();
  });
  $(".layer_cha_image .layer_cha_image_in, .layer_cha_image .layer_deam").click(function () {
    $(".layer_cha_image").hide();
  });

  $(".btn_open_join").click(function () {
    $(".rew_layer_join").show();
  });
  $(".btn_open_login").click(function () {
    $(".rew_layer_login").show();
  });
  $(".btn_open_repass").click(function () {
    $(".rew_layer_repass").show();
  });
  $(".btn_open_setting").click(function () {
    $(".rew_layer_setting").show();
  });
  $(".tl_close button").click(function () {
    $(this).closest(".t_layer").hide();
  });

  $(".button_prof").click(function () {
    $(".tl_prof_slc ul").show();
  });
  $("#btn_slc_character").click(function () {
    $(".rew_layer_character").show();
  });
  $(".rew_layer_character .tl_btn").click(function () {
    $(".rew_layer_character").hide();
  });
  $(".btn_profile").click(function () {
    $(".btn_profile").removeClass("on");
    $(this).addClass("on");
  });
  $(".tl_prof_slc").mouseleave(function () {
    $(".tl_prof_slc ul").hide();
  });

});


$(document).ready(function () {

  var ff_class = "";
  var ff_text = "";

  $(".btn_feeling_banner").click(function () {
    $(".feeling_first").show();
  });
  $(".ff_area button").click(function () {
    ff_class = $(this).attr("class");
    if ($(this).attr("class") == "btn_ff_01") {
      ff_text = "최고의";
    }
    if ($(this).attr("class") == "btn_ff_02") {
      ff_text = "뿌듯한";
    }
    if ($(this).attr("class") == "btn_ff_03") {
      ff_text = "기분 좋은";
    }
    if ($(this).attr("class") == "btn_ff_04") {
      ff_text = "감사한";
    }
    if ($(this).attr("class") == "btn_ff_05") {
      ff_text = "재밌는";
    }
    if ($(this).attr("class") == "btn_ff_06") {
      ff_text = "수고한";
    }
    if ($(this).attr("class") == "btn_ff_07") {
      ff_text = "무난한";
    }
    if ($(this).attr("class") == "btn_ff_08") {
      ff_text = "지친";
    }
    if ($(this).attr("class") == "btn_ff_09") {
      ff_text = "속상한";
    }
    $(".ff_area button").not(this).removeClass("on");
    $(this).addClass("on");
    $(".ff_bottom button").removeClass("btn_off").addClass("btn_on");
  });
  $(".ff_close button").click(function () {
    $(".feeling_first").hide();
    $(".ff_area button").removeClass("on");
    $(".ff_bottom button").removeClass("btn_on").addClass("btn_off");
  });

  $(".ff_bottom button").click(function () {
    if ($(this).hasClass("btn_on")) {
      $(".feeling_first").hide();
      $(".ff_area button").removeClass("on");
      $(".ff_bottom button").removeClass("btn_on").addClass("btn_off");
      $(".feeling_layer").show();
      $(".fl_area .fl_desc").removeClass().addClass("fl_desc");
      $(".fl_area .fl_desc").addClass(ff_class);
      $(".fl_area .fl_desc span").text(ff_text);
    } else {

    }
  });

  $(".fl_bottom button").click(function () {
    $(".feeling_layer").hide();
    $(".tdw_feeling_banner").removeClass().addClass("tdw_feeling_banner");
    $(".tdw_feeling_banner").addClass(ff_class);
    var inputfl = $(".fl_area .input_fl").val();
    $(".tdw_feeling_banner p").text(inputfl);
  });
  $(".fl_close button").click(function () {
    $(".feeling_layer").hide();
  });

  $(".btn_penalty_banner").click(function () {
    $(".penalty_layer").show();
  });
  $(".pl_close button").click(function () {
    $(".penalty_layer").hide();
  });
  $(".pl_img_on img").click(function () {
    $(".layer_cha_image").show();
  });
  $(".layer_cha_image .layer_cha_image_in, .layer_cha_image .layer_deam").click(function () {
    $(".layer_cha_image").hide();
  });

  $(".btn_open_join").click(function () {
    $(".rew_layer_join").show();
  });
  $(".btn_open_login").click(function () {
    $(".rew_layer_login").show();
  });
  $(".btn_open_repass").click(function () {
    $(".rew_layer_repass").show();
  });
  $(".btn_open_setting").click(function () {
    $(".rew_layer_setting").show();
  });
  $(".tl_close button").click(function () {
    $(this).closest(".t_layer").hide();
  });

  $(".button_prof").click(function () {
    $(".tl_prof_slc ul").show();
  });
  $("#btn_slc_character").click(function () {
    $(".rew_layer_character").show();
  });
  $(".rew_layer_character .tl_btn").click(function () {
    $(".rew_layer_character").hide();
  });
  $(".btn_profile").click(function () {
    $(".btn_profile").removeClass("on");
    $(this).addClass("on");
  });
  $(".tl_prof_slc").mouseleave(function () {
    $(".tl_prof_slc ul").hide();
  });

  // media

  $(".hamburger_btn").click(function () {

    $(this).toggleClass("on");

    if($(this).hasClass("on")){
      $(".rew_bar").css({"left": "0%"});
      $(".rew_bg_black").fadeIn();
    } else{
      $(".rew_bar").css({"left": "-55%"});
      $(".rew_bg_black").fadeOut();
    }
  });
  
  $(".rew_bg_black").click(function(){
    $(".hamburger_btn").removeClass("on");
    $(".rew_bar").css({"left": "-55%"});
    $(this).fadeOut()
  })

  $(".rew_mypage_close").click(function(){
    $(".rew_box").removeClass("on");
    $(".rew_menu").css({"top": "100%"});
  })

  $(".tdw_open_btn button").click(function(){
    let windowWidth = $(window).width();

    $(".rew_box").addClass("on");
    if(windowWidth < 540) {
      $(".rew_menu").css({"top": "calc(100% - 475px)"});
    } else {
      $(".rew_menu").css({"top": "calc(100% - 490px)"});
    }


    if($('.rew_box').hasClass('on') === true){

      $(".tdw_open_btn button").css({"display": "none"});
    } else {
      $(".tdw_open_btn button").css({"display": "block"});
    }
  })
});