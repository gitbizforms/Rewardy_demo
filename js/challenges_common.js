$(document).ready(function(){
//기본 선착순 인원설정
 var setpeople = 1;

 //챌린지 선착순 인원 설정
 var pass = false;
 $("#btn_limit_plus").on({
   mouseup: function () {
     pass = false;
   },
   mousedown: function () {
     pass = true;
     if($(this).hasClass("coin_limit")==false){
        count_limit_ticket(setpeople);
     }  
    },
 });

 $("#btn_limit_minus").on({
   mouseup: function () {
     pass = false;
   },
   mousedown: function () {
     pass = true;
     if($(this).hasClass("coin_limit")==false){
        count_limit_ticket(-setpeople);
     } 
   },
 });

//챌린지 선착순 인원 설정
function count_limit_ticket(su) {
    user_chk_val = $("#chall_user_chk").val();
    if(user_chk_val){
        arr_val = user_chk_val.split(",");
        input_limit = $("#limit_count").val();
        if(arr_val.length < input_limit){
            alert("선착순 인원이 참여자 수보다 더 많습니다!");
            $("#limit_count").val(arr_val.length);
            return false;
        }
    }
    if (pass) {
      var max_number = 1000;

      if ($("#limit_count").val()) {
        input_count = $("#limit_count").val();
        input_count = input_count.replace(/,/g, "");
        var input_count = parseInt(input_count);
        //실수로 변환
      } else {
        input_count = 0;
      }

      if (su == "1") {
        //플러스
        if (input_count < max_number) {
          input_count = input_count + setpeople;
          input_count = addComma(input_count);

          $("#limit_count").val(input_count);
          $("#btn_limit_minus").removeClass("coin_limit");

          if ($("#not_count_in").hasClass("btn_chk_on") == true) {
            $("#not_count_in").removeClass("btn_chk_on");
          }
        }
      } else {
        //마이너스
        if (max_number > 0 && input_count > setpeople) {
          input_count = input_count - setpeople;
          input_count = addComma(input_count);
          $("#limit_count").val(input_count);

        //   if (input_count <= setcoin) {
        //     $("#btn_limit_minus").addClass("coin_limit");
        //   }

          if ($("#not_count_in").hasClass("btn_chk_on") == true) {
            $("#not_count_in").removeClass("btn_chk_on");
          }
        } else {
        }
      }
      setTimeout(function () {
        count_limit_ticket(su);
      }, 400);
    }
  }

  $(document).on("click",".cha_view_btn #btn_magam", function(){
     alert("선착순 마감 된 챌린지입니다!");
     return false;   
  });

   //챌린지 정렬순 셀렉트박스 - 마우스 오버
   $(".rew_cha_sort").hover(function () {
    $(".rew_cha_sort").addClass("on");
    // alert('adadg');
  });

  //챌린지 정렬순 셀렉트박스 - 마우스 벗어날때
  $(".rew_cha_sort").mouseleave(function () {
    $(".rew_cha_sort").removeClass("on");
  });


  //  $(document).on("keyup","textarea[id=input_type_mix]",function(){});

   $("textarea[id=input_type_mix]").bind("input", function(){
    // alert("변경");
    var input_type_mix = $("textarea[id=input_type_mix]").val();
    var file_type_mix = $("input[id=file_01]").val();
    // var mix_file_name = $("#mix_file_name").val();
    if (input_type_mix || file_type_mix) {
      $(".btns_cha_join").addClass("on");
    } else {
      $(".btns_cha_join").removeClass("on");
    }
   });

   //메모 글내용작성
   $("#textarea_memo").bind("input", function(event){
    var val = $(this).val();
    if (val) {
      if ($("#layer_memo_submit").hasClass("on") == false) {
        $("#layer_memo_submit").addClass("on");
        $("#textarea_memo").val(val);
      }
    } else {
      $("#layer_memo_submit").removeClass("on");
    }
  });

});


$(document).on("click",".mesage_btn .mesage_del",function(event){
    event.stopPropagation();
    edit_pos = $("#edit_pos").val();
    if(edit_pos){
        alert("삭제가능 기간이 아닙니다.");
        return false;
    }

    if(confirm("도전내역을 삭제하실 경우 보상 코인이 회수 됩니다!! 정말로 삭제 하시겠습니까?")){
     var idxno = $(this).parent().find("#chall_view_update").val();
    //  $(this).closest(".mix_area").remove();
    //  console.log(idxno); 
  
    // return false;
     var fdata = new FormData();
     fdata.append("idx",idxno);
     fdata.append("status","del");
     fdata.append("mode","chamyeo_update");
     $.ajax({
      type: "POST",
      data: fdata,
      contentType: false,
      processData: false,
      url: "/inc/process.php",
      success: function (data) {
          console.log(data);
          location.reload();
        },
      });
    }
  });
  
    $(document).on("click","#not_count_in",function(){
      if($(this).hasClass("btn_limit_off")==true){
        $(this).attr("class", "btn_limit_on");
        $("#btn_limit_minus").removeClass("coin_limit");
        $("#btn_limit_plus").removeClass("coin_limit");
      }else{
        $(this).attr("class", "btn_limit_off");
        $("#btn_limit_minus").addClass("coin_limit");
        $("#btn_limit_plus").addClass("coin_limit");
      }
    });

  $(document).on("click","#layer_cha_update",function(event){
    event.stopPropagation();
  });
  
  $(document).on("click",".mesage_btn #mesage_corr_3",function(event){
    event.stopPropagation();
    edit_pos = $("#edit_pos").val();
    if(edit_pos){
        alert("챌린지 수정 가능기간이 아닙니다.");
        return false;
    }else{
      var result_idx = $(".rew_cha_view_mix #chall_view_update").val();
      console.log(result_idx);
      // return false;
      if(result_idx){
        var fdata = new FormData();
        fdata.append("result_idx",result_idx);
        fdata.append("mode","layer_show");
        
        $.ajax({
          type: "POST",
          data: fdata,
          contentType: false,
          processData: false,
          url: "/inc/challenges_process.php",
          success: function (data) {
            var tdata = data.split("|");
            if(tdata[1]=="success"){
              console.log(tdata[2]);
              $("#layer_cha_update_list").html(tdata[0]);
              $("#layer_cha_update_list").show();
            }
          },
        }); 
      }
    }
  });
    
  $(document).on("click",".mesage_btn #mesage_corr_list",function(event){
    event.stopPropagation();
    edit_pos = $("#edit_pos").val();
    if(edit_pos){
        alert("챌린지 수정 가능기간이 아닙니다.");
        return false;
    }else{
      var result_idx = $(".layer_mix #chall_view_update").val();
      if(result_idx){
        var fdata = new FormData();
        fdata.append("result_idx",result_idx);
        fdata.append("mode","layer_show");
        
        $.ajax({
          type: "POST",
          data: fdata,
          contentType: false,
          processData: false,
          url: "/inc/challenges_process.php",
          success: function (data) {
            var tdata = data.split("|");
            if(tdata[1]=="success"){
              console.log(tdata[2]);
              $("#layer_cha_update_list").html(tdata[0]);
              $("#layer_cha_update_list").show();
            }
          },
        }); 
      }
    }
  });

//   $(document).on("click","#layer_cha_update #btns_chamyeo_cancel", function(event){
//     event.stopPropagation();
//     $("#layer_cha_update").hide();
//   });
  
  $("#cham_comment").keydown(function(){
    // $("#btns_chamyeo_update").addClass("on");
  });
  
  // 수정 레이어 파일 삭제
  var delArray = [];
  $(document).on("click","#layer_cha_update_list button[id^=mix_file_del_]",function(){
      var listkind = $(this).parent().parent().parent().parent().parent().parent().attr("id");
      var idxno = $(this).closest("#layer_cha_update").find("#chamyeo_idx").val(); // 참여번호
      var fileid = $(this).attr("id");
      var delno = fileid.replace("mix_file_del_", "");
  
      var fileidx = $(this).parent().find("input[id^=mix_file_idx_]").val();
      console.log(delno);
      console.log(fileidx);
  
      var fdata = new FormData();
      fdata.append("idx",fileidx);
      fdata.append("mode","layer_file_del");
  
      $.ajax({
        type: "POST",
        data: fdata,
        contentType: false,
        processData: false,
        url: "/inc/process.php",
        success: function (data) {
          console.log(data);
          delArray.push(fileidx);
            $("#layer_cha_update_list #chall_file_desc_"+delno).remove();
            // $("#layer_cha_update #chall_file_desc_"+delno).remove();
        },
      }); 
    });
  
    // 수정 레이어 전체 취소
    $(document).on("click",".join_type_mix #btns_chamyeo_cancel",function(event){
        event.stopPropagation();
        thispar = $(this).closest(".layer_cha_join");
        var idxno = thispar.find("#chamyeo_idx").val(); //참여번호(상단3)
        var listkind = thispar.attr("id");

        console.log(listkind);
        var fdata = new FormData();
        fdata.append("cham_idx",idxno);
        fdata.append("mode","update_cancel");
        fdata.append("del",delArray);

        $.ajax({
          type: "POST",
          data: fdata,
          contentType: false,
          processData: false,
          url: "/inc/process.php",
          success: function (data) {
            var tdata = data.split("|");
            html = tdata[0];
            console.log(data);
            if (tdata[1] == "cancelSuccess") {
            // $(".btns_cha_join").removeClass("on");
              $("#layer_cha_update_list .layer_cha_join_file_desc").append(html);
              // $("#layer_cha_update .layer_cha_join_file_desc").append(html);
                
              $("#layer_cha_update_list").hide();
              delArray = [];
            }
          },
        });
    });

    $(document).on("click",".join_type_mix #btns_chamyeo_update.on", function(event){
        event.stopPropagation();
         thispar = $(this).closest(".layer_cha_join");
         var idxno = thispar.find("#chamyeo_idx").val();
        //  $(this).closest(".mix_area").remove();
         console.log(idxno); 
        
        // return false;
         var fdata = new FormData();
         var comment = thispar.find("#cham_comment").val();
        
        // alert(delArray.length);
        // return false; 
        if(delArray.length>0){
            fdata.append("del",delArray);
        }

         var fileobj = $("#file_01");
         if(fileobj.val()){
           if(fileListArr.length>0){
             for (i = 0; i < fileListArr.length; i++) {
               // console.log(fileListArr[i]);
               fdata.append("files[]", fileListArr[i]);
             }
           }
          //  return false;
         }
      
         fdata.append("comment",comment);
         fdata.append("idx",idxno);
         fdata.append("status","update");
         fdata.append("mode","chamyeo_update");
         $.ajax({
          type: "POST",
          data: fdata,
          contentType: false,
          processData: false,
          url: "/inc/process.php",
          success: function (data) {
            console.log(data);
            alert('수정되었습니다.');
            delArray = [];
            location.reload();
          },
        }); 
      });

//콤마추가
function addComma(v) {
    if (v) {
      v = String(v);
      val = v.replace(/\B(?=(\d{3})+(?!\d))/g, ",");
      return val;
    }
  }
  
  //콤마제거
  function unComma(v) {
    if (v) {
      v = String(v);
      val = v.replace(/,/g, "");
      return val;
    }
  }

//테마리스트 추천버튼
$(document).on("click", ".tpl_list_switch .btn_switch", function () {
  $(this).toggleClass("on");
  var val = $(this).attr("value");
  var id = $(this).attr("id");

  if ($("#" + id).hasClass("on") == true) {
    var recom = 1;
  } else {
    var recom = 0;
  }

  var fdata = new FormData();
  fdata.append("mode", "thema_recom");
  fdata.append("val", val);
  fdata.append("recom", recom);

  $.ajax({
    type: "post",
    data: fdata,
    contentType: false,
    processData: false,
    url: "/inc/template_process.php",
    success: function (data) {
      console.log(data);
      if (data) {
      }
    },
  });
});

// $(document).on("click",".rew_cha_list_func_in .btn_sort_on",function(){
//   $(".btn_sort_on ul").show();
//   alert("성공");
// });

$(document).on("click", "#template_sort ul li", function(){
   var fdata = new FormData();

   cate = $("#chall_category").val();
  fdata.append("cate",cate);

   var sort = $(this).find("button").val();
   var sort_txt = $(this).find("button").text();
   $("#btn_sort_on").text(sort_txt);

  fdata.append("rank",sort);

  $("#template_sort").children().find("span").val(sort);
   var thema_idx = parseInt($("#thema_idx").val());
   if (thema_idx) {
     fdata.append("thema_idx", thema_idx);
   }

  var page = parseInt($("#pageno").val());
  fdata.append("gp",page);

   if ($("#input_search_thema").val()) {
    fdata.append("search", $("#input_search_thema").val());
  }

  //전체
  if ($("#cha_template_tab_all").is(":checked") == true) {
    fdata.append("viewchk_all", "1");
    //임시저장챌린지
  }

  if ($("#cha_chk_tab_save").is(":checked") == true) {
    fdata.append("viewchk_save", "1");
    //숨김챌린지
  }

  if ($("#cha_chk_tab_hide").is(":checked") == true) {
    fdata.append("viewchk_hide", "1");
  } 

    fdata.append("viewchk", "0");
  

  fdata.append("mode", "challenges_template_list_check");

  $.ajax({
    type: "post",
    data: fdata,
    contentType: false,
    processData: false,
    url: "/inc/template_process.php",
    success: function (data) {
      // console.log(data);
      if (data) {
        tdata = data.split("|");
        if (tdata) {
          var sql = tdata[0];
          var html = tdata[1];
          var totcnt = tdata[2];
          var listcnt = tdata[3];
          var lastcnt = tdata[4];
          // console.log(sql);
          if (totcnt == 0) {
            $("#template_more").hide();
          }
          console.log(html);
          $(".rew_cha_count strong").text(totcnt);
          $("#template_list").html(html);

          $(".rew_cha_list_ul li:not('.sli')").each(function (aa) {
            var tis = $(this);
            var tindex = $(this).index();
            //alert(tindex);
            setTimeout(function () {
              tis.addClass("sli");
            }, 100 + tindex * 200);
          });

          // console.log("lastcnt :: " + lastcnt);
          // console.log(totcnt+"/"+listcnt+"/"+lastcnt);

          //더보기 버튼
          setTimeout(function () {
            if (lastcnt >= $("#page_count").val()) {
              //console.log(11);
              $(".rew_cha_more").hide();
            } else {
              //console.log(22);
              $(".rew_cha_more").show();
            }
          }, 3000);
          return false;
        }
      }
    },
  });
});

$(document).on("click","button[class^=coin_limit]",function(){
  if($("#user_count").val()){
    alert("참여자가 있으므로 지급 코인 변경이 불가능합니다.");
  }
    return false;
});

 //챌린지참여하기, 혼합형(메시지+파일첨부) 작성하기 버튼 활성화

$(document).on("click","button[id^=limit_cnt]",function(){
  var not = $(this).attr("id");
  var check = not.replace("limit_cnt","");

  if(check == "_n"){
     $(this).attr("class","btn_user_toggle btn_on");
     $("#limit_cnt").attr("class","btn_user_toggle btn_off");
     $("#rew_cha_limit_cnt").css("display","none");
  }else{
    $(this).attr("class","btn_user_toggle btn_on");
     $("#limit_cnt_n").attr("class","btn_user_toggle btn_off");
     $("#rew_cha_limit_cnt").show();
  }
});

$(document).on("click", ".mix_jjim", function(){
  if($(this).hasClass("on")==true){
    return false;
  }
   var cateno = $("#cate_num").val(); //역량 종류 
   var res_idx = $(this).val();
   
   var view_idx = $("#view_idx").val();
   var name = $(this).parent().parent().find(".mix_user strong").text();
  //  alert(name);
   var fdata = new FormData();
   console.log(res_idx);

   fdata.append("cateno",cateno);
   fdata.append("result_idx",res_idx);
   fdata.append("challenges_idx",view_idx);
   fdata.append("mode","challenges_like");

   if(confirm(name+"님에게 좋아요를 보내겠습니까?")){
    $.ajax({
      type: "post",
      data: fdata,
      contentType: false,
      processData: false,
      url: "/inc/challenges_process.php",
      success: function (data) {
        console.log(data);
        tdata = data.split("|");
        penalty = tdata[0];
        result = tdata[1];
        if(penalty == "penalty"){
          alert("해당 유저에게 패널티가 적용되어 좋아요를 보낼 수 없습니다.");
          return false;
        }else if(penalty == "limit_like"){
          alert("보내려는 유저에게 일일 좋아요 횟수 제한을 초과했습니다.");
                return false;
        }else{
          if(result == "success"){
            alert("좋아요를 보냈습니다.");
            $("button[id=mix_jjim_"+res_idx+"]").addClass("on");
            // $("#mix_jjim_"+res_idx).addClass("on");
          }else{
            console.log(result);
          }
          return false;
        }
      },
    });
   }
});

//달력선택부분
$(document).on("click", ".btn_sort_on", function (event) {
  $(".list_function_sort").addClass("on");
});

//레이어 달력 마우스 이동
$(document).on("mouseleave", ".list_function_sort", function (event) {
  $(".list_function_sort").removeClass("on");
});

$(document).on("click",".list_function_sort ul li button",function(){
  $(".list_function_sort").removeClass("on");
});

$(".list_function_sort ul li button").click(function () {
  // alert('daf0');
});

 //챌린지 혼합형, 좌측메뉴 회원클릭
 $(document).on("click",".layer_mix .layer_result_user .layer_result_user_in ul li button",
 function () {
   console.log("혼합형 리스트");
   var list_idx = $(this).val();
   var idx = $("#view_idx").val();
   var fdata = new FormData();

   if (list_idx) {
     $("#user_email").val(list_idx);
     fdata.append("list_idx", list_idx);
   }

   fdata.append("idx", idx);
   fdata.append("mode", "auth_mix_list");

   if ($("#input_mix").val()) {
     fdata.append("input_val", $("#input_mix").val());
   }
   
   $.ajax({
     type: "post",
     data: fdata,
     contentType: false,
     processData: false,
     url: "/inc/process.php",
     success: function (data) {
       console.log(data);
       if (data) {
         $("#mix_zone_list").html(data);
       }
     },
   });
 });

//챌린지 혼합형 날짜검색
$(document).on("click", "button[id^='mix_reg']", function () {
  var val = $(this).val();
  var list_idx = $("#user_email").val();
  var idx = $("#view_idx").val();
  var fdata = new FormData();

  //console.log(val);
  // console.log(list_idx);
  // console.log(idx);

  fdata.append("mode", "auth_mix_list");
  if(list_idx){
   fdata.append("list_idx", list_idx);
  }
  fdata.append("idx", idx);
  fdata.append("user_date", val);

  if (val) {
    //    $("#user_email").val(val);
    $("#user_date").val(val);
  }

  if ($("#input_mix").val()) {
    fdata.append("input_val", $("#input_mix").val());
  }

  console.log(val+"|"+list_idx+"|"+idx);
  // return false;
  $.ajax({
    type: "post",
    data: fdata,
    contentType: false,
    processData: false,
    url: "/inc/process.php",
    success: function (data) {
      console.log(data);
      if (data) {
        mix_user_list();
        $("#mix_zone_list").html(data);
      }
    },
  });
});

//챌린지 메모 기능 추가 20231102

//메모클릭
$(document).on("click", ".chall_view_memo", function () {
  if ($(this).val()) {
    $("#chall_memo_result").val($(this).val());
  }
  $(".layer_memo").show();
  //$("#textarea_memo").focus();
  setTimeout(function () {
    $("#textarea_memo").focus();
  }, 100);
});

//메모취소
$(document).on("click", ".layer_memo_cancel", function () {
  //$(".layer_memo_cancel").click(function() {
  if ($("#textarea_memo").val()) {
    $("#textarea_memo").val("");
  }
  if ($("#textarea_memo_secret").val()) {
    $("#textarea_memo_secret").val("");
  }

  if ($("#layer_memo_submit").hasClass("on") == true) {
    $("#layer_memo_submit").removeClass("on");
  }else if($("#layer_memo_secret_submit").hasClass("on") == true){
    $("#layer_memo_secret_submit").removeClass("on");
  }
  $(".layer_memo").hide();
  $(".layer_memo_secret").hide();
});

  //메모 등록하기
  $(".layer_memo_submit").click(function(){
		$(".layer_memo").hide();
		// $(".tdw_list_memo").addClass("on");
	});

  //메모등록버튼
  $(document).on("click", ".layer_memo_submit", function () {
    var secret_flag = 0;
    if ($("#textarea_memo").val() == "") {
      alert("메모를 작성해주세요.");
      //$("#textarea_memo").focus();
      /*setTimeout(function() {
              $('#textarea_memo').focus();
            }, 1000);
            */
      
      if ($(".layer_memo").is(":visible") == false) {
        $(".layer_memo").show();
        $("#textarea_memo").focus();
      }
      return false;
    }

    if ($("#textarea_memo").val()) {
      var fdata = new FormData();
      var result_idx = $("#chall_memo_result").val();
      fdata.append("mode", "chall_comment");
      fdata.append("view_idx", $("#view_idx").val());
      fdata.append("result_idx", result_idx);
      fdata.append("comment", $("#textarea_memo").val());
      // fdata.append("secret_flag", secret_flag);
      var url = "/inc/challenges_process.php";

      $.ajax({
        type: "POST",
        data: fdata,
        contentType: false,
        processData: false,
        url: url,
        success: function (data) {
          console.log(data);
          tdata = data.split("|");
          result = tdata[1];
          html = tdata[0];
          if (result == "complete") {
            $("#textarea_memo").val("");
            $("#memo_area_in_"+result_idx).append(html);
            $(".layer_memo").hide();
            return false;
          }
        },
      });
    }
  });
  
  // 챌린지 메모 삭제
  $(document).on("click","#layer_memo_delete",function(){
    if(confirm("메모를 삭제하십니까?")){
      var fdata = new FormData();
      fdata.append("mode","chall_comment_del");
      fdata.append("view_idx", $("#view_idx").val());
      var memo_idx = $(this).val();

      fdata.append("memo_idx", memo_idx);

      $.ajax({
        type: "POST",
        data: fdata,
        contentType: false,
        processData: false,
        url: "/inc/challenges_process.php",
        success: function (data) {
          console.log(data);
          if (data == "complete") {
            alert("삭제되었습니다.");
            $("#resultCo_"+memo_idx).remove();
            return false;
          }
        },
      });
    }else{
      return false;
    }
  });

  //챌린지 메모 수정 클릭
  $(document).on("click",".tdw_list_memo_conts_txt",function(){
    memo_id = $(this).parent().parent().find("#memo_id").val();
    user_id = $("#user_id_on").val();
   
    if(memo_id == user_id){
      $(this).find(".tdw_list_memo_regi").css("display","block");
    }
  });

  //챌린지 메모 수정 취소
  $(document).on("click",".btn_regi_cancel",function(){
    memo_idx = $(this).val();
    $("#chall_memo_"+memo_idx).hide();
    console.log(memo_idx);
    return false;
  });

  //메모수정버튼
  $(document).on("click", ".btn_regi_submit", function () {
    var comment = $(this).closest(".tdw_list_memo_regi").find("textarea");
    if ($(".textarea_regi").val() == "") {
      alert("메모를 작성해주세요.");
      
      return false;
    }

    if ($(".textarea_regi").val()) {
      var fdata = new FormData();
      var memo_idx = $(this).val();
      text_edit = $(this).parent().parent().find(".textarea_regi").val();
      fdata.append("view_idx", $("#view_idx").val());
      fdata.append("mode", "chall_comment_update");
      fdata.append("memo_idx", memo_idx);
      fdata.append("comment", text_edit);
      // console.log(text_edit);
      // return false;

      var url = "/inc/challenges_process.php";

      $.ajax({
        type: "POST",
        data: fdata,
        contentType: false,
        processData: false,
        url: url,
        success: function (data) {
          console.log(data);
          tdata = data.split("|");
          result = tdata[1];
          html = tdata[0];
          if (result == "complete") {
            $("#chall_memo_"+memo_idx).hide();
            $("#resultCo_"+memo_idx).html(html);
            return false;
          }
        },
      });
    }
  });

  // 상태메세지 등록
  $(document).on('click', '.submit_btn .btn_on', function(){
    var p_memo = $(".text_area .layer_text").val();
    var char_val = $("#check_profile").val();
    console.log(p_memo);
    
    var fdata = new FormData();
    fdata.append("p_memo", p_memo);
    fdata.append("profile_no", char_val);

    fdata.append("mode", "team_memo");
        $.ajax({
            type: "post",
                data: fdata,
                contentType: false,
                processData: false,
                url: '/inc/main_process.php',
                success: function(data) {
                    // console.log(data);
                    if (data) {
                        tdata = data.split("|");
                        if(tdata){
                            // console.log(tdata);
                            var result = tdata[0];
                            var result2 = tdata[1];
                            var result3 = tdata[2];
                            if(result == 'complete'){
                                if(p_memo == ''){
                                    $('.rew_main_anno_in span').text("상태 메시지를 입력해주세요.");
                                }else{
                                    $('.rew_main_anno_in span').text(p_memo);
                                }
                                if(result2){
                                    $(".user_img").css("background-image", "url(" + result2 + ")");
                                }
                                if(result3){
                                    $(".user_img").css("background-image", "url(" + result3 + ")");
                                }
                            }
                        }
                        $('.layer_pro').hide();
                    }else{
                        $('.layer_pro').hide();
                    }
                }
        });
});

    $(document).on("click","button[id^=profile_img_0]", function(){
        var val = $(this).val();
        $("#check_profile").val(val);
    });

    //프로필 케릭터 선택
    $(document).on('click', '#tl_profile_bt', function(){
    var val;

    val = $("#check_profile").val();
    var fdata = new FormData();
    fdata.append("mode", "profile_character");
    fdata.append("profile_no", val);

    $.ajax({
        type: "POST",
        data: fdata,
        contentType: false,
        processData: false,
        url: '/inc/main_process.php',
        success: function(data) {
            console.log(data);
            if (data) {
                tdata = data.split("|");
                if (tdata) {
                    var result = tdata[0];
                    var result2 = tdata[1];
                    if (result == "complete") {
                        // $("#profile_character_img").css("background-image", "url(" + result2 + ")");
                        $("#profile_character_img1").css("background-image", "url(" + result2 + ")");
                        // main_live_list();
                    }
                }
            }
        }
    });
  });

  //프로필사진변경
  $("input[id='prof']").change(profile_img_preview);

  function profile_img_preview(event) {
    var input = this;
    var id = $(this).attr("id");

    if (input.files && input.files.length) {
      var reader = new FileReader();
      this.enabled = false
      reader.onload = (function(e) {
          $("#profile_character_img1").css("background-image", "url(" + e.target.result + ")");
      });
      reader.readAsDataURL(input.files[0]);

      var fdata = new FormData();
      fdata.append("mode", "main_profile_change");
      fdata.append("files[]", $("input[id='prof']")[0].files[0]);
      $.ajax({
          type: "POST",
          data: fdata,
          contentType: false,
          processData: false,
          url: '/inc/main_process.php',
          success: function(data) {
              console.log("ddddd ::: " + data);
          }
      });
    }
  }