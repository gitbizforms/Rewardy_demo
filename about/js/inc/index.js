$(document).ready(function(){

		setTimeout(function(){
			$(".section0").addClass("on");
		}, 300);

		var top_h = $(window).height();
		var w_top	= $(window).scrollTop();
		var top0 = $(".section0").offset();
		var top0h = $(".section0").outerHeight();
		var top01 = $(".section01_in").offset();
		var top02 = $(".section02_in").offset();
		var top02h = $(".section02_in").outerHeight();
		var top03 = $(".section03_in").offset();
		var top03h = $(".section03_in").outerHeight();
		var top04 = $(".section04_in").offset();
		var top04h = $(".section04_in").outerHeight();
		var top05 = $(".section05_in").offset();
		var top05h = $(".section05_in").outerHeight();
		var top06 = $(".section06_in").offset();
		var top06h = $(".section06_in").outerHeight();
		var top10 = $(".section10_in").offset();
		var top10h = $(".section10_in").outerHeight();
		var top07 = $(".section07_bottom").offset();
		var top07h = $(".section07_bottom").outerHeight();
		var top08 = $(".section08").offset();
		var top08h = $(".section08").outerHeight();

		if((w_top) < (top0h+top0.top)){
			$(".section0").addClass("on");
		}else{
			$(".section0").removeClass("on");
		}

		if(((top_h+w_top) > (top02.top)) && ((w_top) < (top02h+top02.top))){
		//if((top_h+w_top) > (top2.top+100)){
			$(".section02").addClass("on");
		}else{
			$(".section02").removeClass("on");
		}

		if(((top_h+w_top) > (top03.top)) && ((w_top) < (top03h+top03.top))){
			$(".section03").addClass("on");
		}else{
			$(".section03").removeClass("on");
		}

		if(((top_h+w_top) > (top04.top)) && ((w_top) < (top04h+top04.top))){
			$(".section04").addClass("on");
		}else{
			$(".section04").removeClass("on");
		}

		if(((top_h+w_top) > (top05.top)) && ((w_top) < (top05h+top05.top))){
			$(".section05").addClass("on");
		}else{
			$(".section05").removeClass("on");
		}

		if(((top_h+w_top) > (top06.top)) && ((w_top) < (top06h+top06.top))){
			$(".section06").addClass("on");
		}else{
			$(".section06").removeClass("on");
		}

		if(((top_h+w_top) > (top10.top)) && ((w_top) < (top10h+top10.top))){
			$(".section10").addClass("on");
		}else{
			$(".section10").removeClass("on");
		}

		if(((top_h+w_top) > (top07.top)) && ((w_top) < (top07h+top07.top))){
			$(".section07").addClass("on");
		}else{
			$(".section07").removeClass("on");
		}

		if(((top_h+w_top) > (top08.top)) && ((w_top) < (top08h+top08.top))){
			$(".section08").addClass("on");
		}else{
			$(".section08").removeClass("on");
		}

		$(window).scroll(function () {
			var top_h = $(window).height();
			var w_top	= $(window).scrollTop();
			var top0 = $(".section0").offset();
			var top0h = $(".section0").outerHeight();
			var top01 = $(".section01_in").offset();
			var top02 = $(".section02_in").offset();
			var top02h = $(".section02_in").outerHeight();
			var top03 = $(".section03_in").offset();
			var top03h = $(".section03_in").outerHeight();
			var top04 = $(".section04_in").offset();
			var top04h = $(".section04_in").outerHeight();
			var top05 = $(".section05_in").offset();
			var top05h = $(".section05_in").outerHeight();
			var top06 = $(".section06_in").offset();
			var top06h = $(".section06_in").outerHeight();
			var top10 = $(".section10_in").offset();
			var top10h = $(".section10_in").outerHeight();
			var top07 = $(".section07_bottom").offset();
			var top07h = $(".section07_bottom").outerHeight();
			var top08 = $(".section08").offset();
			var top08h = $(".section08").outerHeight();

			if((w_top) < (top0h+top0.top)){
				$(".section0").addClass("on");
			}else{
				$(".section0").removeClass("on");
			}

			if(((top_h+w_top) > (top02.top)) && ((w_top) < (top02h+top02.top))){
			//if((top_h+w_top) > (top2.top+100)){
				$(".section02").addClass("on");
			}else{
				$(".section02").removeClass("on");
			}

			if(((top_h+w_top) > (top03.top)) && ((w_top) < (top03h+top03.top))){
				$(".section03").addClass("on");
			}else{
				$(".section03").removeClass("on");
			}

			if(((top_h+w_top) > (top04.top)) && ((w_top) < (top04h+top04.top))){
				$(".section04").addClass("on");
			}else{
				$(".section04").removeClass("on");
			}

			if(((top_h+w_top) > (top05.top)) && ((w_top) < (top05h+top05.top))){
				$(".section05").addClass("on");
			}else{
				$(".section05").removeClass("on");
			}

			if(((top_h+w_top) > (top06.top)) && ((w_top) < (top06h+top06.top))){
				$(".section06").addClass("on");
			}else{
				$(".section06").removeClass("on");
			}

			if(((top_h+w_top) > (top10.top)) && ((w_top) < (top10h+top10.top))){
				$(".section10").addClass("on");
			}else{
				$(".section10").removeClass("on");
			}

			if(((top_h+w_top) > (top07.top)) && ((w_top) < (top07h+top07.top))){
				$(".section07").addClass("on");
			}else{
				$(".section07").removeClass("on");
			}

			if(((top_h+w_top) > (top08.top)) && ((w_top) < (top08h+top08.top))){
				$(".section08").addClass("on");
			}else{
				$(".section08").removeClass("on");
			}

		});

		$("#fullpage2").fullpage({
			//sectionsColor: ['#f2f2f2', '#4BBFC3', '#7BAABE', 'whitesmoke', '#ccddff']
			'scrollOverflow':true,
			'css3': true,
			'navigation': true,
			'navigationPosition': 'right',
			'navigationTooltips': ['리워디', '필요성', 'LIVE', '챌린지', '역량평가', '보상', 'UI', '성장', '리워디'],
			"afterLoad": function(anchorLink, index){
				if(index == 3){
					$(".section2_right li").each(function(){
						var tis = $(this);
						var tindex = $(this).index();
						setTimeout(function(){
							tis.addClass("sli5");
						},200+tindex*150);
					});
				}
				var s3ih = $(".section3_in").height();
				var s3h = $("#section3 .fp-tableCell").height();
				if((s3ih+80) >= s3h){
					$(".section3_bottom li").eq(5).remove();
					$(".section3_bottom li").eq(4).remove();
					$(".section3_bottom li").eq(3).remove();
				}

				if(index == 4){
					$(".section3_bottom li").each(function(){
						var tis = $(this);
						var tindex = $(this).index();
						setTimeout(function(){
							tis.addClass("sli5");
						},200+tindex*150);
					});
				}

				if(index == 5){
					$("#rb_radarChart").addClass("on");
				}

				if(index == 6){
					$(".mobile_list li").each(function(){
						var tis = $(this);
						var tindex = $(this).index();
						setTimeout(function(){
							tis.addClass("sli5");
						},200+tindex*150);
					});
				}

				if(index == 7){
					$(".ui_pc").addClass("on");
					$(".ui_tb").addClass("on");
					$(".ui_mb").addClass("on");
				}
			},
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




		$(".quick_nav_go_01").click(function() {
			var offset = $(".section0").offset();
			var win_top = $(window).scrollTop();
			var ani_time = Math.abs(win_top - offset.top-80);
			$("html, body").animate({scrollTop:0},(200+ani_time/8));
		});

		$(".quick_nav_go_02").click(function() {
			var top_h = $(window).height();
			var offset = $(".section02_in").offset();
			var offseth = $(".section02_in").outerHeight();
			var win_top = $(window).scrollTop();
			var ani_time = Math.abs(win_top - offset.top-80);
			$("html, body").animate({scrollTop:(offset.top-(top_h/2)+(offseth/2))},(200+ani_time/8));
		});

		$(".quick_nav_go_03").click(function() {
			var top_h = $(window).height();
			var offset = $(".section03_in").offset();
			var offseth = $(".section03_in").outerHeight();
			var win_top = $(window).scrollTop();
			var ani_time = Math.abs(win_top - offset.top-80);
			$("html, body").animate({scrollTop:(offset.top-(top_h/2)+(offseth/2))},(200+ani_time/8));
		});

		$(".quick_nav_go_04").click(function() {
			var top_h = $(window).height();
			var offset = $(".section04_in").offset();
			var offseth = $(".section04_in").outerHeight();
			var win_top = $(window).scrollTop();
			var ani_time = Math.abs(win_top - offset.top-80);
			$("html, body").animate({scrollTop:(offset.top-(top_h/2)+(offseth/2))},(200+ani_time/8));
		});

		$(".quick_nav_go_05").click(function() {
			var top_h = $(window).height();
			var offset = $(".section05_in").offset();
			var offseth = $(".section05_in").outerHeight();
			var win_top = $(window).scrollTop();
			var ani_time = Math.abs(win_top - offset.top-80);
			$("html, body").animate({scrollTop:(offset.top-(top_h/2)+(offseth/2))},(200+ani_time/8));
		});

		$(".quick_nav_go_06").click(function() {
			var top_h = $(window).height();
			var offset = $(".section06_in").offset();
			var offseth = $(".section06_in").outerHeight();
			var win_top = $(window).scrollTop();
			var ani_time = Math.abs(win_top - offset.top-80);
			$("html, body").animate({scrollTop:(offset.top-(top_h/2)+(offseth/2))},(200+ani_time/8));
		});

		$(".quick_nav_go_10").click(function() {
			var top_h = $(window).height();
			var offset = $(".section10_in").offset();
			var offseth = $(".section10_in").outerHeight();
			var win_top = $(window).scrollTop();
			var ani_time = Math.abs(win_top - offset.top-80);
			$("html, body").animate({scrollTop:(offset.top-(top_h/2)+(offseth/2))},(200+ani_time/8));
		});






		$(".live_user_state li .live_user_state_circle").mouseenter(function(){
			$(".layer_state").removeClass("on");
			$(this).next(".layer_state").addClass("on");
			$(".live_list_box").removeClass("zindex");
			$(this).closest(".live_list_box").addClass("zindex");
		});
		$(".live_user_state li .live_user_state_circle").mouseleave(function(){
			$(".layer_state").removeClass("on");
			$(".live_list_box").removeClass("zindex");
		});

		$(".input_main").keyup(function(){
			var input_length = $(this).val().length; //입력한 값의 글자수
			if(input_length>0){
				$(".btn_grid_02").addClass("on");
			}else{
				$(".btn_grid_02").removeClass("on");
			}
		});

		$(".btn_grid_02").click(function(){
			if($(".btn_grid_02").hasClass("on")){
				$(".rew_grid_list_none").hide();
				var textspan = $(".input_main").val();
				var text01 = $(".rew_grid_list_in ul li.rew_grid_list_01 span").text();
				var text02 = $(".rew_grid_list_in ul li.rew_grid_list_02 span").text();
				var text03 = $(".rew_grid_list_in ul li.rew_grid_list_03 span").text();
				$(".rew_grid_list_in ul li.rew_grid_list_01 span").text(textspan);
				$(".rew_grid_list_in ul li.rew_grid_list_02 span").text(text01);
				$(".rew_grid_list_in ul li.rew_grid_list_03 span").text(text02);
				//$(".rew_grid_list_in ul").prepend("<li class='ui-sortable-handle'><button></button><div><span>"+textspan+"</span></div></li>");
				//$(".rew_grid_list_in ul li:eq(3)").remove();
			}
			//var textspan = $(".input_main").value();
			//$(".rew_grid_list_in ul").prepend("<li class='ui-sortable-handle'><button></button><div><span>"+textspan+"</span></div></li>");

			if($(".rew_grid_list_in ul li.rew_grid_list_01 span").is(':empty')){

			}else{
				$(".rew_grid_list_in ul li.rew_grid_list_01").addClass("view");
			}
			if($(".rew_grid_list_in ul li.rew_grid_list_02 span").is(':empty')){

			}else{
				$(".rew_grid_list_in ul li.rew_grid_list_02").addClass("view");
			}
			if($(".rew_grid_list_in ul li.rew_grid_list_03 span").is(':empty')){

			}else{
				$(".rew_grid_list_in ul li.rew_grid_list_03").addClass("view");
			}
		});

		$(".rew_grid_list_in ul li button").click(function(){
			$(this).parent("li").toggleClass("on");
		});

		$(".rew_btn_icons_more").click(function(){
			$(".rew_icons").toggle();
		});

		$(".onoff_01 .btn_switch").click(function(){
			$(".onoff_04 .btn_switch").removeClass("on");
			$(".onoff_04 .btn_switch").prev("em").removeClass("on");
			$(this).addClass("on");
			$(this).prev("em").addClass("on");
		});

		$(".onoff_02 .btn_switch").click(function(){
			var switchon = $(this);
			if(switchon.hasClass("on")){
				$(this).removeClass("on");
				$(this).prev("em").removeClass("on");
			}else{
				$(".onoff_03 .btn_switch").removeClass("on");
				$(".onoff_03 .btn_switch").prev("em").removeClass("on");
				$(".onoff_04 .btn_switch").removeClass("on");
				$(".onoff_04 .btn_switch").prev("em").removeClass("on");
				$(".onoff_01 .btn_switch").addClass("on");
				$(".onoff_01 .btn_switch").prev("em").addClass("on");
				$(this).addClass("on");
				$(this).prev("em").addClass("on");
			}
		});

		$(".onoff_03 .btn_switch").click(function(){
			var switchon = $(this);
			if(switchon.hasClass("on")){
				$(this).removeClass("on");
				$(this).prev("em").removeClass("on");
			}else{
				$(".onoff_02 .btn_switch").removeClass("on");
				$(".onoff_02 .btn_switch").prev("em").removeClass("on");
				$(".onoff_04 .btn_switch").removeClass("on");
				$(".onoff_04 .btn_switch").prev("em").removeClass("on");
				$(".onoff_01 .btn_switch").addClass("on");
				$(".onoff_01 .btn_switch").prev("em").addClass("on");
				$(this).addClass("on");
				$(this).prev("em").addClass("on");
			}
		});

		$(".onoff_04 .btn_switch").click(function(){
			var switchon = $(this);
			if(switchon.hasClass("on")){
				$(this).removeClass("on");
				$(this).prev("em").removeClass("on");
			}else{
				$(".onoff_01 .btn_switch").removeClass("on");
				$(".onoff_01 .btn_switch").prev("em").removeClass("on");
				$(".onoff_02 .btn_switch").removeClass("on");
				$(".onoff_02 .btn_switch").prev("em").removeClass("on");
				$(".onoff_03 .btn_switch").removeClass("on");
				$(".onoff_03 .btn_switch").prev("em").removeClass("on");
				$(this).addClass("on");
				$(this).prev("em").addClass("on");
			}
		});

		$(".rew_grid_state_in .rew_grid_state_circle").mouseenter(function(){
			$(".layer_state").removeClass("on");
			$(this).next(".layer_state").addClass("on");
		});
		$(".rew_grid_state_in .rew_grid_state_circle").mouseleave(function(){
			$(".layer_state").removeClass("on");
		});

		setTimeout(function(){
			$("#bar_graph_05 strong").animate({"height":70+"%","background-color":"#f7241f"},1400,"linear");
			$("#bar_graph_04 strong").animate({"height":50+"%","background-color":"#334ff9"},1000,"linear");
			$("#bar_graph_03 strong").animate({"height":40+"%","background-color":"#334ff9"},800,"linear");
			$("#bar_graph_02 strong").animate({"height":100+"%","background-color":"#f7241f"},2000,"linear");
			$("#bar_graph_01 strong").animate({"height":60+"%","background-color":"#334ff9"},1200,"linear");
		}, 1400);

		setTimeout(function(){
			$(".rew_mains_company_coin p strong").text("1,500,000");
			$(".rew_mains_company_coin p strong").counterUp({
				delay:35,
				time:1600
			});
		}, 1000);


	});

	$(window).scroll(function(){

	});
