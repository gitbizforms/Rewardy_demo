$(function () {

    $(".is_layer button").click(function(){
      $(".is_layer").hide();
    });

    $(".rew_mypage_13").click(function (e) {
      if (!$(e.target).is(".rew_mypage_13 *")) {
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


    setTimeout(function(){
      $(".rew_box").addClass("on");
    },400);
    
    $(".rew_menu_onoff button").click(function(){
      var thisonoff = $(this);
      if(thisonoff.hasClass("on")){
        thisonoff.removeClass("on");
        $(".rew_box").removeClass("on");

      }else{
        thisonoff.addClass("on");
        $(".rew_box").addClass("on");

      }
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

    $(".rew_cha_more button").click(function(){

      $(".rew_cha_list_ul").append('<li class="sli2 category_06 offset0"><a href="#"><div class="cha_box"><div class="cha_box_t"><span class="cha_cate">기타</span><span class="cha_title">하루에 칭찬 3번을 했을 뿐인데 </span><span class="cha_coin"><strong>1,000</strong>코인</span></div><div class="cha_box_b"><span class="cha_member">18/20 명 도전중</span><span class="cha_dday">D - 7</span></div></div></a></li><li class="sli2 category_01"><a href="#"><div class="cha_box"><div class="cha_box_t"><span class="cha_cate">업무</span><span class="cha_title">윈도우 업데이트 점검한다면</span><span class="cha_coin"><strong>500</strong>코인</span></div><div class="cha_box_b"><span class="cha_member">12/20 명 도전중</span><span class="cha_dday">D - 20</span></div></div></a></li><li class="sli2 category_02"><a href="#"><div class="cha_box"><div class="cha_box_t"><span class="cha_cate">생활</span><span class="cha_title">책 읽고 독서메모를 남긴다면</span><span class="cha_coin"><strong>1,500</strong>코인</span></div><div class="cha_box_b"><span class="cha_member">7/20 명 도전중</span><span class="cha_dday">D - 10</span></div></div></a></li><li class="sli2 category_05"><a href="#"><div class="cha_box"><div class="cha_box_t"><span class="cha_cate">신입사원</span><span class="cha_title">보고서 작성법을 배우면</span><span class="cha_coin"><strong>1,000</strong>코인</span></div><div class="cha_box_b"><span class="cha_member">1/1 명 도전중</span><span class="cha_dday">D - 30</span></div></div></a></li><li class="sli2 category_02"><a href="#"><div class="cha_box"><div class="cha_box_t"><span class="cha_cate">생활</span><span class="cha_title">캔크러시 챌린지, 그저 밟기만 했을 뿐인데</span><span class="cha_coin"><strong>500</strong>코인</span></div><div class="cha_box_b"><span class="cha_member">12/20 명 도전중</span><span class="cha_dday">D - 60</span></div></div></a></li><li class="sli2 category_05"><a href="#"><div class="cha_box"><div class="cha_box_t"><span class="cha_cate">신입사원</span><span class="cha_title">비즈니스 명함 예절을 배우면</span><span class="cha_coin"><strong>10,000</strong>코인</span></div><div class="cha_box_b"><span class="cha_member">1/1 명 도전중</span><span class="cha_dday">D - 30</span></div></div></a></li><li class="sli2 category_06"><a href="#"><div class="cha_box"><div class="cha_box_t"><span class="cha_cate">기타</span><span class="cha_title">하루에 칭찬 3번을 했을 뿐인데 </span><span class="cha_coin"><strong>1,000</strong>코인</span></div><div class="cha_box_b"><span class="cha_member">18/20 명 도전중</span><span class="cha_dday">D - 7</span></div></div></a></li><li class="sli2 category_01"><a href="#"><div class="cha_box"><div class="cha_box_t"><span class="cha_cate">업무</span><span class="cha_title">윈도우 업데이트 점검한다면</span><span class="cha_coin"><strong>500</strong>코인</span></div><div class="cha_box_b"><span class="cha_member">12/20 명 도전중</span><span class="cha_dday">D - 20</span></div></div></a></li><li class="sli2 category_02"><a href="#"><div class="cha_box"><div class="cha_box_t"><span class="cha_cate">생활</span><span class="cha_title">책 읽고 독서메모를 남긴다면</span><span class="cha_coin"><strong>1,500</strong>코인</span></div><div class="cha_box_b"><span class="cha_member">7/20 명 도전중</span><span class="cha_dday">D - 10</span></div></div></a></li><li class="sli2 category_05"><a href="#"><div class="cha_box"><div class="cha_box_t"><span class="cha_cate">신입사원</span><span class="cha_title">보고서 작성법을 배우면</span><span class="cha_coin"><strong>1,000</strong>코인</span></div><div class="cha_box_b"><span class="cha_member">1/1 명 도전중</span><span class="cha_dday">D - 30</span></div></div></a></li><li class="sli2 category_02"><a href="#"><div class="cha_box"><div class="cha_box_t"><span class="cha_cate">생활</span><span class="cha_title">캔크러시 챌린지, 그저 밟기만 했을 뿐인데</span><span class="cha_coin"><strong>500</strong>코인</span></div><div class="cha_box_b"><span class="cha_member">12/20 명 도전중</span><span class="cha_dday">D - 60</span></div></div></a></li><li class="sli2 category_05"><a href="#"><div class="cha_box"><div class="cha_box_t"><span class="cha_cate">신입사원</span><span class="cha_title">비즈니스 명함 예절을 배우면</span><span class="cha_coin"><strong>10,000</strong>코인</span></div><div class="cha_box_b"><span class="cha_member">1/1 명 도전중</span><span class="cha_dday">D - 30</span></div></div></a></li>');

      
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

$(document).on("click", ".item_layer", function(){
    var val = $(this).val();
    $("#item_idx").val(val);

    var fdata = new FormData();

    fdata.append("mode", "item_img_layer");
    fdata.append("img_idx",val);
 

    $.ajax({
        type: "post",
        async: false,
        data: fdata,
        contentType: false,
        processData: false,
        url: '/inc/item_process.php',
        success: function(data){
            console.log(data);
            $(".is_layer").html(data);
            $(".item_prof").hide();
            $(".is_layer").show();
        }
    });

});

$(document).on("click", ".is_layer_btn_off", function(){
    $(".is_layer").hide();
});

$(document).on("click", "#item_img_buy", function(){

    if(confirm("아이템을 구매 하시겠습니까?") == false){
        return false;
    }

    var val = $("#item_idx").val();
    var fdata = new FormData();

    fdata.append("mode","item_img_buy");
    fdata.append("img_idx",val);

    $.ajax({
        type: "post",
        async: false,
        data: fdata,
        contentType: false,
        processData: false,
        url: '/inc/main_process.php',
        success: function(data){
            console.log(data);
            if(data == "exp"){
                alert("코인이 부족합니다.");
                return false;
            }else if(data == "complete"){
                alert("구매가 완료되었습니다.");
                location.reload();
                return false;
            }else if(data == "exist"){
              alert("이미 구입한 아이템입니다.");
              $(".is_layer").hide();
            }else if(data == "date_be"){
              alert("아직 사용기간이 남은 아이템입니다.");
              $(".is_layer").hide();
            }
        }
    });
});

$(document).on("click", ".my_is_box", function(){
  var val = $(this).val();
  var item_kind = $(this).parent("li").val();
  var default_flag = 0;

  if(item_kind == 0){
    if($(this).parent("li").hasClass("on")){
      if(confirm("기본 이미지로 변경 하시겠습니까?")){
        $(this).parent("li").removeClass("on");
        default_flag = 1;  
      }else{
        return false; 
      }
    }else{
      $(".my_is_list ul li").removeClass("on");
      $(this).parent("li").addClass("on");
    }
  }

  var fdata = new FormData();

  fdata.append("mode", "item_change");
  fdata.append("tem_idx", val);
  fdata.append("def_flag", default_flag);

  $.ajax({
    type: "post",
    async: false,
    data: fdata,
    contentType: false,
    processData: false,
    url: '/inc/item_process.php',
    success: function(data){
      console.log(data);
      if(data == "complete"){
        // location.reload();
      }else if(data == "def"){
        location.reload();
      }
    }
  });
});