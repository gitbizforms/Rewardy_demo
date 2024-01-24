
$(document).ready(function(){
	
	$(".sort_01 .btn_sort_on").click(function(){
		$(".sort_01").addClass("on");
	  });
	$(".sort_02 .btn_sort_on").click(function(){
		$(".sort_02").addClass("on");
	});
	$(".tdw_tab_sort").mouseleave(function(){
		$(".tdw_tab_sort").removeClass("on");
	  });

	
	$(".btn_tdw_search").click(function(){
		$(".search_layer").show();
	});
	$(".sl_close button").click(function(){
		$(".search_layer").hide();
	});

	$(".pll_close button").click(function(){
		$(".party_link_layer").hide();
	});
	$(".tdw_list_party_link").click(function(){
		$(".party_link_layer").show();
	});
	
	$(".tdw_list .tdw_list_100c").click(function () {
		$(".layer_100c").show();
	  });
	

	$(".rew_cha_view_result li button").click(function(){
		$(".layer_result").show();
	});
	$(".rew_cha_view_header .view_user li button").click(function(){
		$(".layer_result").show();
	});
	$(".layer_result .layer_close button").click(function(){
		$(".layer_result").hide();
	});
	$(".layer_result_user li button").click(function(){
		$(".layer_result_user li button").removeClass("on");
		$(this).addClass("on");
	});

	$(".rew_cha_view_masage .title_area .title_more").click(function(){
		$(".layer_masage").show();
	});
	$(".masage_zone").click(function(){
		$(".layer_masage").show();
	});
	$(".masage_area_in").click(function(){
		$(this).toggleClass("on");
	});
	$(".layer_masage .layer_close button").click(function(){
		$(".layer_masage").hide();
	});

	$(".layer_report .layer_close button").click(function(){
		$(".layer_report").hide();
	});

	$(".layer_challenge .layer_close button").click(function(){
		$(".layer_challenge").hide();
		$(".layer_challenge .report_cha .rew_cha_list_ul li").removeClass("sli");
	});

	$(".join_type_file .btns_cha_cancel").click(function(){
		$(".join_type_file").hide();
	});
	$(".join_type_masage .btns_cha_cancel").click(function(){
		$(".join_type_masage").hide();
	});
	$(".join_type_mix .btns_cha_cancel").click(function(){
		$(".join_type_mix").hide();
	});
	$(".btn_join_ok").click(function(){
		$(".layer_cha_join").show();
	});

	

	$(".btn_eval").click(function(){
		$(".layer_report").show();
	});


	$(".btn_tdw_list_chk").click(function(){
		// $(this).closest(".tdw_list_box").toggleClass("on");//제거
	});
	$(".tdw_list_desc p").click(function(){
		$(this).closest(".tdw_list_desc").children(".tdw_list_regi").show();
	});
	$(".tdw_list .tdw_list_desc .tdw_list_regi button").click(function(){
		$(this).closest(".tdw_list_regi").hide();
	});

	

	$(".btn_req").click(function(){
		$(".layer_user").show();
		$("#layer_test_02").hide();
		$("#layer_test_03").hide();
		$("#layer_test_01").show();
		$(".layer_user_info dl").addClass("on");
		// $(".layer_user_info dd button").removeClass("on");
		$(".layer_user_submit").removeClass("on");
		$(".layer_user_info").animate({scrollTop :0}, 0);
	});



	$(".tdw_list .btn_regi_cancel").click(function(){
		$(this).closest(".tdw_list_memo_regi").hide();
	});
	
	$(".tdw_list .tdw_list_memo_conts_txt strong").click(function(){
		$(this).next(".tdw_list_memo_regi").show();
		var memo_width = $(this).parent(".tdw_list_memo_conts_txt").width();
		$(this).next(".tdw_list_memo_regi").css({"width":memo_width+199});
	});
	
	$(".tdw_list .tdw_list_repeat").click(function(){
		$(this).next(".tdw_list_repeat_list").show();
	});
	$(".tdw_list .tdw_list_repeat_list button").click(function(){
		var this_text = $(this).text();
		$(this).closest(".tdw_list_repeat_box").addClass("on");
		$(this).closest(".tdw_list_repeat_box").find(".tdw_list_repeat").text(this_text);
		$(".tdw_list_repeat_list").hide();
	});
	$(".tdw_list .tdw_list_repeat_box").mouseleave(function(){
		$(".tdw_list_repeat_list").hide();
	});

	$("#loginbtn").click(function(){
		$(".rew_layer_login").hide();
		$(".layer_work").show();
	});
	


	// $(".btn_list_report_onoff").click(function(){
	// 	$(this).parent().prev(".tdw_list_report_area_in").toggleClass("off");
	// 	$(this).toggleClass("off");
	// });

	// //0831 메모 열기/접기
	// $(".btn_list_memo_onoff").click(function(){
	// 	$(this).parent().prev(".tdw_list_memo_area_in").toggleClass("off");
	// 	$(this).toggleClass("off");
	// });
	
	//0901 메모 열기/접기 보여지는 기준 : 메모 영역 높이기준이라 메모 삭제 시 다시 계산 필요
	setTimeout(function(){
		$(".tdw_list_memo_area_in").each(function(){
			var maih = $(this);
			if(maih.height()>110){
				maih.next($(".tdw_list_memo_onoff")).show();
			}
		});
	},400);

	//0901 임시 팝업
	// $(".tdw_write_btn").click(function(){
	// 	$(".rew_popup").show();
	// 	setTimeout(function(){
	// 		$(".rew_popup").hide();
	// 	},4700);
	// });




	var ff_class = "";
	var ff_text = "";

	$(".btn_feeling_banner").click(function(){
		$(".feeling_first").show();
	});
	$(".ff_area button").click(function(){
		ff_class = $(this).attr("class");
		if($(this).attr("class") == "btn_ff_01"){ff_text="최고의";}
		if($(this).attr("class") == "btn_ff_02"){ff_text="뿌듯한";}
		if($(this).attr("class") == "btn_ff_03"){ff_text="기분 좋은";}
		if($(this).attr("class") == "btn_ff_04"){ff_text="감사한";}
		if($(this).attr("class") == "btn_ff_05"){ff_text="재밌는";}
		if($(this).attr("class") == "btn_ff_06"){ff_text="수고한";}
		if($(this).attr("class") == "btn_ff_07"){ff_text="무난한";}
		if($(this).attr("class") == "btn_ff_08"){ff_text="지친";}
		if($(this).attr("class") == "btn_ff_09"){ff_text="속상한";}
		$(".ff_area button").not(this).removeClass("on");
		$(this).addClass("on");
		$(".ff_bottom button").removeClass("btn_off").addClass("btn_on");
	});
	$(".ff_close button").click(function(){
		$(".feeling_first").hide();
		$(".ff_area button").removeClass("on");
		$(".ff_bottom button").removeClass("btn_on").addClass("btn_off");
	});

	$(".ff_bottom button").click(function(){
		if($(this).hasClass("btn_on")){
			$(".feeling_first").hide();
			$(".ff_area button").removeClass("on");
			$(".ff_bottom button").removeClass("btn_on").addClass("btn_off");
			$(".feeling_layer").show();
			$(".fl_area .fl_desc").removeClass().addClass("fl_desc");
			$(".fl_area .fl_desc").addClass(ff_class);
			$(".fl_area .fl_desc span").text(ff_text);
		}else{

		}
	});

	$(".fl_bottom button").click(function(){
		$(".feeling_layer").hide();
		$(".tdw_feeling_banner").removeClass().addClass("tdw_feeling_banner");
		$(".tdw_feeling_banner").addClass(ff_class);
		var inputfl = $(".fl_area .input_fl").val();
		$(".tdw_feeling_banner p").text(inputfl);
	});
	$(".fl_close button").click(function(){
		$(".feeling_layer").hide();
	});

	$(".btn_penalty_banner").click(function(){
		$(".penalty_layer").show();
	});
	$(".pl_close button").click(function(){
		$(".penalty_layer").hide();
	});
	$(".pl_img_on img").click(function(){
		$(".layer_cha_image").show();
	});
	$(".layer_cha_image .layer_cha_image_in, .layer_cha_image .layer_deam").click(function(){
		$(".layer_cha_image").hide();
	});

	$(".btn_open_join").click(function(){
		$(".rew_layer_join").show();
	});
	$(".btn_open_login").click(function(){
		$(".rew_layer_login").show();
	});
	$(".btn_open_repass").click(function(){
		$(".rew_layer_repass").show();
	});
	$(".btn_open_setting").click(function(){
		$(".rew_layer_setting").show();
	});
	$(".tl_close button").click(function(){
		$(this).closest(".t_layer").hide();
	});
	
});

$(window).load(function(){
	let originSize = $(window).width() + $(window).height();
	$(window).resize(function(){
		if($(window).width() + $(window).height() != originSize) {    
			$('.rew_menu').addClass('on')
		} else {
			$('.rew_menu').removeClass('on')
		}
	})
})