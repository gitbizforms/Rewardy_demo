$(function () {

		$(document).ready(function(){

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

			$(".rew_type_slc .btn_type_box").click(function(){	
			// 파티 리스트 변경 작업 20230511	
			$(".rew_type_slc .btn_type_list").removeClass("on");	
			$(this).addClass("on");	
			$(".cha_user_list").hide();
			$(".cha_user_box").show();	
			$(".rew_party_wrap.rew_cha_list .rew_cha_list_ul").removeClass("party_type_list");	
			});	

			$(".rew_type_slc .btn_type_list").click(function(){	
				$(".rew_type_slc .btn_type_box").removeClass("on");	
				$(this).addClass("on");
				$(".cha_user_box").hide();
				$(".cha_user_list").show();		
				$(".rew_party_wrap.rew_cha_list .rew_cha_list_ul").addClass("party_type_list");	
			});	
		
		$(".rew_party_sort .btn_sort_on").click(function(){	
			$(".rew_party_sort").addClass("on");	
		});	
		$(".rew_party_sort").mouseleave(function(){	
			$(".rew_party_sort").removeClass("on");	
		});	
		$(".rew_party_sort ul li button").click(function(){	
			$(".rew_party_sort").removeClass("on");	
		});	
		$(".rew_cha_tab_sort .btn_sort_on").click(function(){	
			$(".rew_cha_tab_sort").addClass("on");	
		});	
		$(".rew_cha_tab_sort").mouseleave(function(){	
			$(".rew_cha_tab_sort").removeClass("on");	
		});	
		$(".rew_cha_tab_sort ul li button").click(function(){	
			$(".rew_cha_tab_sort").removeClass("on");	
		});	
		$(".rew_cha_sort .btn_sort_on").click(function(){	
			$(".rew_cha_sort").addClass("on");	
		});	
		$(".rew_cha_sort").mouseleave(function(){	
			$(".rew_cha_sort").removeClass("on");	
		});	
		$(".rew_cha_sort ul li button").click(function(){	
			$(".rew_cha_sort").removeClass("on");	
		});	
		
		$(".rew_cha_more button").click(function(){
			
			setTimeout(function(){
				var offset = $(".offset0").position();
				$(".rew_conts_scroll_04").animate({scrollTop : offset.top - 5}, 700);
			},400);

			setTimeout(function(){
				$(".offset0").removeClass("offset0");
			},1100);

			$(".rew_cha_list_ul li:not('.sli')").each(function(aa){
				var tis = $(this);
				var tindex = $(this).index();
				//alert(tindex);
				setTimeout(function(){
					tis.addClass("sli");
				},700+(aa+1)*150);
			});

			
		});
		});

	$(document).ready(function(){

		$(".rew_mypage_10").click(function (e) {
			if (!$(e.target).is(".rew_mypage_10 *")) {
				$(".rew_box").removeClass("on");
				$(".rew_menu_onoff button").removeClass("on");
			}
		});

		$(".rew_conts_list_in ul").sortable({
			axis: "y",
            opacity: 0.7,
			zIndex: 9999,
			//placeholder:"sort_empty",
			cursor: "move"
		});
		$(".rew_conts_list_in ul").disableSelection();

		$(".rew_conts_list_in ul li button").click(function(){
			$(this).parent("li").toggleClass("on");
		});

		$(".rew_btn_icons_more").click(function(){
			$(".rew_icons").toggle();
		});

		$(".rew_mypage_tab_04 a").click(function(){
			$(".rew_mypage_tab_04 li").removeClass("on");
			$(this).parent("li").addClass("on");
		});


		$(".rew_conts_scroll_04").scroll(function(){
			var rct = $(".rew_cha_list_in").offset().top;
			console.log(rct);
			if(rct<216){
				$(".rew_cha_list_func").addClass("pos_fix");
			}else{
				$(".rew_cha_list_func").removeClass("pos_fix");
			}
		});

		$(".rew_cha_list_ul li").each(function(){
			var tis = $(this);
			var tindex = $(this).index();
			setTimeout(function(){
				tis.addClass("sli");
			},700+tindex*150);
		});

		$(".rew_cha_tab_sort .btn_sort_on").click(function(){
			$(".rew_cha_tab_sort").addClass("on");
		});
		$(".rew_cha_tab_sort").mouseleave(function(){
			$(".rew_cha_tab_sort").removeClass("on");
		});
		$(".rew_cha_tab_sort ul li button").click(function(){
			$(".rew_cha_tab_sort").removeClass("on");
		});

		$(".rew_cha_sort .btn_sort_on").click(function(){
			$(".rew_cha_sort").addClass("on");
		});
		$(".rew_cha_sort").mouseleave(function(){
			$(".rew_cha_sort").removeClass("on");
		});
		$(".rew_cha_sort ul li button").click(function(){
			$(".rew_cha_sort").removeClass("on");
		});

		


	});

	function link(pro_idx) {

		//미확인 업무로 이동
		location.href = "/party/view.php?idx="+pro_idx;

	}

	
    });