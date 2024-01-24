$(function() {

  $(".tdw_ins_tab_in ul li button").click(function(){
    $(".tdw_ins_tab_in ul li button").removeClass("on");
    $(this).addClass("on");

    if($(".select_c_dd").hasClass("on") == true) {
      $(".rew_ins_c_dd").show();
      $(".rew_ins_c_ww").hide();
      $(".rew_ins_c_mm").hide();
      $("#r_work_date").width("110px");

      if ($("#r_work_month").is(":visible") == true) {
        $("#r_work_month").hide();
        $("#r_work_date").show();
      }

    } else if($(".select_c_ww").hasClass("on") == true){
      $(".rew_ins_c_ww").show();
      $(".rew_ins_c_dd").hide();
      $(".rew_ins_c_mm").hide();
      $("#r_work_date").width("220px");

      if ($("#r_work_month").is(":visible") == true) {
        $("#r_work_month").hide();
        $("#r_work_date").show();
      }

    } else if($(".select_c_mm").length){
      $(".rew_ins_c_mm").show();
      $(".rew_ins_c_dd").hide();
      $(".rew_ins_c_ww").hide();
      $("#r_work_month").width("86px");

      if ($("#r_work_month").is(":visible") == false) {
        $("#r_work_month").show();
        $("#r_work_date").hide();
      }

    } else if($(".select_l_dd").hasClass("on") == true){
      $(".rew_ins_l_dd").show();
      $(".rew_ins_l_mm").hide();
      $(".rew_ins_l_ww").hide();
      $("#r_work_date").width("110px");

      if ($("#r_work_month").is(":visible") == true) {
        $("#r_work_month").hide();
        $("#r_work_date").show();
      }

    } else if($(".select_l_ww").hasClass("on") == true){
      $(".rew_ins_l_ww").show();
      $(".rew_ins_l_dd").hide();
      $(".rew_ins_l_mm").hide();
      $("#r_work_date").width("220px");

      if ($("#r_work_month").is(":visible") == true) {
        $("#r_work_month").hide();
        $("#r_work_date").show();
      }

    } else if($(".select_l_mm").hasClass("on") == true){
      $(".rew_ins_l_mm").show();
      $(".rew_ins_l_dd").hide();
      $(".rew_ins_l_ww").hide();
      $("#r_work_month").width("86px");

      if ($("#r_work_month").is(":visible") == false) {
        $("#r_work_month").show();
        $("#r_work_date").hide();
      }
    } else if($(".select_p_dd").hasClass("on") == true){
      $(".rew_ins_p_dd").show();
      $(".rew_ins_p_mm").hide();
      $(".rew_ins_p_ww").hide();
      $("#r_work_date").width("110px");

      if ($("#r_work_month").is(":visible") == true) {
        $("#r_work_month").hide();
        $("#r_work_date").show();
      }

    } else if($(".select_p_ww").hasClass("on") == true){
      $(".rew_ins_p_ww").show();
      $(".rew_ins_p_dd").hide();
      $(".rew_ins_p_mm").hide();
      $("#r_work_date").width("220px");

      if ($("#r_work_month").is(":visible") == true) {
        $("#r_work_month").hide();
        $("#r_work_date").show();
      }

    } else if($(".select_p_mm").hasClass("on") == true){
      $(".rew_ins_p_mm").show();
      $(".rew_ins_p_dd").hide();
      $(".rew_ins_p_ww").hide();
      $("#r_work_month").width("86px");

      if ($("#r_work_month").is(":visible") == false) {
        $("#r_work_month").show();
        $("#r_work_date").hide();
      }
    }
    r_date_change();
    rank_list();
  });


  		$(".rew_conts_list_in ul").sortable({
  			axis: "y",
              opacity: 0.7,
  			zIndex: 9999,
  			//placeholder:"sort_empty",
  			cursor: "move"
  		});
  		$(".rew_conts_list_in ul").disableSelection();

  		$("#template_list").sortable({
              opacity: 0.7,
  			zIndex: 9999,
  			//placeholder:"sort_empty",
  			cursor: "move",
  			containment : 'parent'
  		});
  		$("#template_list").disableSelection();

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


  		setTimeout(function(){
  			$(".rew_box").addClass("on");
  		},400);

  		/*$(".rew_menu_onoff button").click(function(){
  			var thisonoff = $(this);
  			if(thisonoff.hasClass("on")){
  				thisonoff.removeClass("on");
  				$(".rew_box").removeClass("on");

  			}else{
  				thisonoff.addClass("on");
  				$(".rew_box").addClass("on");

  			}
  		});*/

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

  		$(".rew_cha_more button").click(function(){

  			$(".rew_cha_list_ul").append('<li class="sli2 category_06 offset0"><a href="./0006.html"><div class="cha_box"><div class="cha_box_t"><span class="cha_cate">기타</span><span class="cha_title">하루에 칭찬 3번을 했을 뿐인데 </span><span class="cha_coin"><strong>1,000</strong>코인</span></div><div class="cha_box_b"><span class="cha_member">18/20 명 도전중</span><span class="cha_dday">D - 7</span></div></div></a></li><li class="sli2 category_01"><a href="./0006.html"><div class="cha_box"><div class="cha_box_t"><span class="cha_cate">업무</span><span class="cha_title">윈도우 업데이트 점검한다면</span><span class="cha_coin"><strong>500</strong>코인</span></div><div class="cha_box_b"><span class="cha_member">12/20 명 도전중</span><span class="cha_dday">D - 20</span></div></div></a></li><li class="sli2 category_02"><a href="./0006.html"><div class="cha_box"><div class="cha_box_t"><span class="cha_cate">생활</span><span class="cha_title">책 읽고 독서메모를 남긴다면</span><span class="cha_coin"><strong>1,500</strong>코인</span></div><div class="cha_box_b"><span class="cha_member">7/20 명 도전중</span><span class="cha_dday">D - 10</span></div></div></a></li><li class="sli2 category_05"><a href="./0006.html"><div class="cha_box"><div class="cha_box_t"><span class="cha_cate">신입사원</span><span class="cha_title">보고서 작성법을 배우면</span><span class="cha_coin"><strong>1,000</strong>코인</span></div><div class="cha_box_b"><span class="cha_member">1/1 명 도전중</span><span class="cha_dday">D - 30</span></div></div></a></li><li class="sli2 category_02"><a href="./0006.html"><div class="cha_box"><div class="cha_box_t"><span class="cha_cate">생활</span><span class="cha_title">캔크러시 챌린지, 그저 밟기만 했을 뿐인데</span><span class="cha_coin"><strong>500</strong>코인</span></div><div class="cha_box_b"><span class="cha_member">12/20 명 도전중</span><span class="cha_dday">D - 60</span></div></div></a></li><li class="sli2 category_05"><a href="./0006.html"><div class="cha_box"><div class="cha_box_t"><span class="cha_cate">신입사원</span><span class="cha_title">비즈니스 명함 예절을 배우면</span><span class="cha_coin"><strong>10,000</strong>코인</span></div><div class="cha_box_b"><span class="cha_member">1/1 명 도전중</span><span class="cha_dday">D - 30</span></div></div></a></li><li class="sli2 category_06"><a href="./0006.html"><div class="cha_box"><div class="cha_box_t"><span class="cha_cate">기타</span><span class="cha_title">하루에 칭찬 3번을 했을 뿐인데 </span><span class="cha_coin"><strong>1,000</strong>코인</span></div><div class="cha_box_b"><span class="cha_member">18/20 명 도전중</span><span class="cha_dday">D - 7</span></div></div></a></li><li class="sli2 category_01"><a href="./0006.html"><div class="cha_box"><div class="cha_box_t"><span class="cha_cate">업무</span><span class="cha_title">윈도우 업데이트 점검한다면</span><span class="cha_coin"><strong>500</strong>코인</span></div><div class="cha_box_b"><span class="cha_member">12/20 명 도전중</span><span class="cha_dday">D - 20</span></div></div></a></li><li class="sli2 category_02"><a href="./0006.html"><div class="cha_box"><div class="cha_box_t"><span class="cha_cate">생활</span><span class="cha_title">책 읽고 독서메모를 남긴다면</span><span class="cha_coin"><strong>1,500</strong>코인</span></div><div class="cha_box_b"><span class="cha_member">7/20 명 도전중</span><span class="cha_dday">D - 10</span></div></div></a></li><li class="sli2 category_05"><a href="./0006.html"><div class="cha_box"><div class="cha_box_t"><span class="cha_cate">신입사원</span><span class="cha_title">보고서 작성법을 배우면</span><span class="cha_coin"><strong>1,000</strong>코인</span></div><div class="cha_box_b"><span class="cha_member">1/1 명 도전중</span><span class="cha_dday">D - 30</span></div></div></a></li><li class="sli2 category_02"><a href="./0006.html"><div class="cha_box"><div class="cha_box_t"><span class="cha_cate">생활</span><span class="cha_title">캔크러시 챌린지, 그저 밟기만 했을 뿐인데</span><span class="cha_coin"><strong>500</strong>코인</span></div><div class="cha_box_b"><span class="cha_member">12/20 명 도전중</span><span class="cha_dday">D - 60</span></div></div></a></li><li class="sli2 category_05"><a href="./0006.html"><div class="cha_box"><div class="cha_box_t"><span class="cha_cate">신입사원</span><span class="cha_title">비즈니스 명함 예절을 배우면</span><span class="cha_coin"><strong>10,000</strong>코인</span></div><div class="cha_box_b"><span class="cha_member">1/1 명 도전중</span><span class="cha_dday">D - 30</span></div></div></a></li>');


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


  		$(".cha_jjim").click(function(){
  			$(this).toggleClass("on");
  		});

  		$(".tpl_list_switch .btn_switch").click(function(){
  			$(this).toggleClass("on");
  		});

  		$(".tpl_list_area ul").sortable({
  			axis: "y",
              opacity: 0.7,
  			zIndex: 9999,
  			handle: ".tpl_list_drag",
  			//placeholder:"sort_empty",
  			cursor: "move",
  			items:"li:not(.ui-state-disabled)"
  		});


  		//그래프 높이
  		var irg_t_coin = $(".ins_rank_graph .ir_rank_4 .ir_bar_coin span").text();
  		var irg_t_heart = $(".ins_rank_graph .ir_rank_4 .ir_bar_heart span").text();
  		var irg_t_power = $(".ins_rank_graph .ir_rank_4 .ir_bar_power span").text();
  		var irg_t = irg_t_coin + irg_t_heart + irg_t_power;
  		var irg_h = 160;
  		var irg_no = /[\{\}\[\]\/?.,;:|\)*~`!^\-_+<>@\#$%&\\\=\(\'\"]/gi
  		var irg_t = irg_t.replace(irg_no, "");
  		$(".ins_rank_graph li").each(function(){
  			var irg_count_coin = $(this).find(".ir_bar_coin span").text().replace(irg_no, "");
  			var irg_count_heart = $(this).find(".ir_bar_heart span").text().replace(irg_no, "");
        var irg_count_power = $(this).find(".ir_bar_power span").text().replace(irg_no, "");
        var irg_count = irg_count_coin + irg_count_heart + irg_count_power;
  			var irg_height = irg_h*irg_count/irg_t;
  			$(this).find(".ir_bar_graph").css({"height":irg_height});
  		});


});

//순위날짜 이동
function rank_list(){

  var mode = "rank_list";

  var fdata = new FormData();
  if($(".select_c_dd").hasClass("on") == true){
    var rank_type = "c_day";
    var wdate = $("#r_work_date").val();
    fdata.append("rank_wdate", $("#work_wdate").val());

  } else if ($(".select_c_ww").hasClass("on") == true){
    var rank_type = "c_week";
    var wdate = $("#r_work_date").val();
    fdata.append("rank_wdate", $("#work_wdate").val());

  } else if ($(".select_c_mm").hasClass("on") == true){
    var rank_type = "c_month";
    var wdate = $("#r_work_month").val();
    fdata.append("rank_wdate", $("#work_wdate").val());

  } else if ($(".select_co_mm").length){
    // alert("여기");
    console.log('month');
    var rank_type = "co_month";
    var wdate = $("#r_work_month").val();
    fdata.append("rank_wdate", $("#work_month").val());

  } else if ($(".select_l_dd").hasClass("on") == true){
    var rank_type = "l_day";
    var wdate = $("#r_work_date").val();
    fdata.append("rank_wdate", $("#work_wdate").val());

  } else if ($(".select_l_ww").hasClass("on") == true){
    var rank_type = "l_week";
    var wdate = $("#r_work_date").val();
    fdata.append("rank_wdate", $("#work_wdate").val());

  } else if ($(".select_l_mm").hasClass("on") == true){
    var rank_type = "l_month";
    var wdate = $("#r_work_month").val();
    fdata.append("rank_wdate", $("#work_month").val());

  } else if ($(".select_p_dd").hasClass("on") == true){
    var rank_type = "p_day";
    var wdate = $("#r_work_date").val();
    fdata.append("rank_wdate", $("#work_month").val());

  } else if ($(".select_p_ww").hasClass("on") == true){
    var rank_type = "p_week";
    var wdate = $("#r_work_date").val();
    fdata.append("rank_wdate", $("#work_month").val());

  } else if ($(".select_p_mm").hasClass("on") == true){
    var rank_type = "p_month";
    var wdate = $("#r_work_month").val();
    fdata.append("rank_wdate", $("#work_month").val());

  }

  fdata.append("wdate", wdate);
  fdata.append("mode",mode);
  fdata.append("rank_type", rank_type);
  console.log(rank_type);
  $.ajax({
    type: "POST",
    data: fdata,
    async: false,
    contentType: false,
    processData: false,
    url: '/inc/insight_process.php',
    success: function(data){
      if(data) {
        console.log(data);
        if(rank_type == "c_day"){
          $(".rew_ins_c_dd").html(data);
          $(".rew_ins_c_ww").html("");
          $(".rew_ins_c_mm").html("");
        } else if(rank_type == "c_week"){
          $(".rew_ins_c_ww").html(data);
          $(".rew_ins_c_dd").html("");
          $(".rew_ins_c_mm").html("");
        } else if(rank_type == "c_month"){
          $(".rew_ins_c_ww").html("");
          $(".rew_ins_c_dd").html("");
          $(".rew_ins_c_mm").html(data);
        } else if(rank_type == "co_month"){
          $(".rew_ins_c_mm").html("");
          // $(".rew_ins_c_mm").show();
          $(".rew_ins_c_dd").html(data);
          $(".rew_ins_c_ww").html("");
        } else if(rank_type == "l_day"){
          $(".rew_ins_l_dd").html(data);
          $(".rew_ins_l_ww").html("");
          $(".rew_ins_l_mm").html("");
        } else if(rank_type == "l_week"){
          $(".rew_ins_l_ww").html(data);
          $(".rew_ins_l_dd").html("");
          $(".rew_ins_l_mm").html("");
        } else if(rank_type == "l_month"){
          $(".rew_ins_l_mm").html(data);
          $(".rew_ins_l_dd").html("");
          $(".rew_ins_l_ww").html("");
        } else if(rank_type == "p_day"){
          $(".rew_ins_p_dd").html(data);
          $(".rew_ins_p_mm").html("");
          $(".rew_ins_p_ww").html("");
        } else if(rank_type == "p_week"){
          $(".rew_ins_p_ww").html(data);
          $(".rew_ins_p_dd").html("");
          $(".rew_ins_p_mm").html("");
        } else if(rank_type == "p_month"){
          $(".rew_ins_p_mm").html(data);
          $(".rew_ins_p_dd").html("");
          $(".rew_ins_p_ww").html("");
        }

        $(document).find("input[id^=listdate]").removeClass('hasDatepicker').datepicker();

        graph_h();

      }
    }
  });
}

function graph_h() {
  //그래프 높이
  var irg_t_coin = $(".ins_rank_graph .ir_rank_4 .ir_bar_coin span").text();
  var irg_t_heart = $(".ins_rank_graph .ir_rank_4 .ir_bar_heart span").text();
  var irg_t_power = $(".ins_rank_graph .ir_rank_4 .ir_bar_power span").text();
  var irg_t = irg_t_coin + irg_t_heart + irg_t_power;
  var irg_h = 160;
  var irg_no = /[\{\}\[\]\/?.,;:|\)*~`!^\-_+<>@\#$%&\\\=\(\'\"]/gi
  var irg_t = irg_t.replace(irg_no, "");
  $(".ins_rank_graph li").each(function(){
    var irg_count_coin = $(this).find(".ir_bar_coin span").text().replace(irg_no, "");
    var irg_count_heart = $(this).find(".ir_bar_heart span").text().replace(irg_no, "");
    var irg_count_power = $(this).find(".ir_bar_power span").text().replace(irg_no, "");
    var irg_count = irg_count_coin + irg_count_heart + irg_count_power;
    var irg_height = irg_h*irg_count/irg_t;
    $(this).find(".ir_bar_graph").animate({"height":irg_height});

  });
}

function r_date_change() {
  var fdata = new FormData();
  var wdate = $("#work_date").val();

  if ($(".select_c_dd").hasClass("on") == true){
    var day_type = "day";
    var work_wdate = $("#r_work_wdate").val();
  }else if ($(".select_c_ww").hasClass("on") == true){
    var day_type = "week";
    var work_wdate = $("#r_work_wdate").val();
  }else if($(".select_c_mm").length){
    var day_type = "month";
    var work_wdate = $("#r_work_month").val();
  } else if ($(".select_l_dd").hasClass("on") == true){
    var day_type = "day";
    var work_wdate = $("#r_work_wdate").val();
  } else if ($(".select_l_ww").hasClass("on") == true){
    var day_type = "week";
    var work_wdate = $("#r_work_wdate").val();
  } else if ($(".select_l_mm").hasClass("on") == true){
    var day_type = "month";
    var work_wdate = $("#r_work_month").val();
  } else if ($(".select_p_dd").hasClass("on") == true){
    var day_type = "day";
    var work_wdate = $("#r_work_wdate").val();
  } else if ($(".select_p_ww").hasClass("on") == true){
    var day_type = "week";
    var work_wdate = $("#r_work_wdate").val();
  } else if ($(".select_p_mm").hasClass("on") == true){
    var day_type = "month";
    var work_wdate = $("#r_work_month").val();
  }

  fdata.append("mode", "r_date_change");
  fdata.append("wdate", wdate);
  fdata.append("work_wdate", work_wdate);
  fdata.append("day_type", day_type);

  $.ajax({
    type: "POST",
    data: fdata,
    async: false,
    contentType: false,
    processData: false,
    url: '/inc/insight_process.php',
    success: function(data) {
      if(data){
        $("#r_work_date").val(data);

        return false;
      }
    }
  });
}
