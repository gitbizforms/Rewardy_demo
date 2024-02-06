$(function () {
  //자동완성 off
  $(
    "input[id=input_reason],input[id=input_coin],input[id=withdraw_coin],input[id=input_bank_num],input[id=input_bank_user]"
  ).attr("autocomplete", "off");

  //출금신청 금액 길이제한
  $("#withdraw_coin").attr("maxlength", 12);

  //출금신청 계좌번호 길이제한
  $("#input_bank_num").attr("maxlength", 16);

  //출금신청 예금주 길이제한
  $("#input_bank_user").attr("maxlength", 10);

  //날짜선택(1주일,1개월,3개월)
  $(".rew_ard_btns button").click(function () {
    var index = $(this).index();
    //console.log("click");
    // console.log(index);

    var btn_cnt = $(".rew_ard_btns button").length;
    for (var i = 0; i < btn_cnt; i++) {
      if (index == i) {
        $(".rew_ard_btns button").eq(i).addClass("on");
      } else {
        $(".rew_ard_btns button").eq(i).removeClass("on");
      }
    }

    if ($(".rew_ard_btns button").eq(0).hasClass("on")) {
      var n = 7;
    } else if ($(".rew_ard_btns button").eq(1).hasClass("on")) {
      var n = 30;
    } else if ($(".rew_ard_btns button").eq(2).hasClass("on")) {
      var n = 90;
    }

    var m = 0;
    var date = new Date();
    var start = new Date(Date.parse(date) - n * 1000 * 60 * 60 * 24);
    var today = new Date(Date.parse(date) - m * 1000 * 60 * 60 * 24);

    var yyyy = start.getFullYear();
    var mm = start.getMonth() + 1;
    var dd = start.getDate();

    if (mm < 10) {
      mm = "0" + mm;
    }
    if (dd < 10) {
      dd = "0" + dd;
    }

    var t_yyyy = today.getFullYear();
    var t_mm = today.getMonth() + 1;
    var t_dd = today.getDate();

    if (t_mm < 10) {
      t_mm = "0" + t_mm;
    }
    if (t_dd < 10) {
      t_dd = "0" + t_dd;
    }

    var reward_sdate = yyyy + "-" + mm + "-" + dd;
    var reward_edate = t_yyyy + "-" + t_mm + "-" + t_dd;
    $("#reward_sdate").val(reward_sdate);
    $("#reward_edate").val(reward_edate);

    $(this).each(function (index, item) {
      //console.log(index);
      /*if ($(this).eq(index).hasClass("on") == false) {
                $(this).eq(index).addClass("on");
            } else {
                $(this).eq(index).removeClass("on");
            }*/
    });

    if ($(".rew_ard_btns button").eq(index).hasClass("on") == false) {
      //$(".rew_ard_btns button").eq(index).addClass("on");
    }
  });

  //시작일
  $("#btn_calendar_l").click(function () {
    $("#reward_sdate").focus();
  });

  //종료일
  $("#btn_calendar_r").click(function () {
    $("#reward_edate").focus();
  });

  //조회하기
  $("#btn_inquiry").click(function () {
    var reward_sdate = $("#reward_sdate").val();
    var reward_edate = $("#reward_edate").val();

    $("#reward_inquiry").val(true);
    if ($(".rew_ard_btns button").eq(0).hasClass("on")) {
      var n = 7;
    } else if ($(".rew_ard_btns button").eq(1).hasClass("on")) {
      var n = 30;
    } else if ($(".rew_ard_btns button").eq(2).hasClass("on")) {
      var n = 90;
    }
    var type = $("#rew_ard_sort ul li button:eq(0)").hasClass("on");

    var string =
      "&page=reward&sdate=" +
      reward_sdate +
      "&edate=" +
      reward_edate +
      "&nday=" +
      n +
      "&type=" +
      type;
    var fdata = new FormData();
    fdata.append("mode", "reward_list");
    fdata.append("sdate", reward_sdate);
    fdata.append("edate", reward_edate);
    fdata.append("string", string);
    fdata.append("type", type);
    fdata.append("nday", n);

    $.ajax({
      type: "post",
      async: false,
      data: fdata,
      contentType: false,
      processData: false,
      url: "/inc/reward_process.php",
      success: function (data) {
        console.log(data);
        $("#rew_ard_in").html(data);
      },
    });
  });

  //출금신청
  $("#rew_withdraw_btn").click(function () {
    if ($("#layer_withdraw").is(":visible") == false) {
      $("#layer_withdraw").show();
    }
  });

  //출근신청 레이어 닫기
  $("#btn_withdraw_off").click(function () {
    if ($("#layer_withdraw").is(":visible") == true) {
      var obj = $("#withdraw_coin");
      if (obj.val()) {
        obj.val("");
      }
      $("#layer_withdraw").hide();
    }
  });

  //출금신청하기
  $("#btn_withdraw_on").click(function () {
    var obj_coin = $("#withdraw_coin").val();
    let coin = obj_coin.replace(/,/g, '');

    if (!obj_coin) {
      alert("출금할 금액을 입력해 주세요.");
      obj_coin.focus();
      return false;
    }
    if (confirm("입력한 내용으로 출금 신청하시겠습니까?")) {
      var fdata = new FormData();
      fdata.append("mode", "withdraw_add");
      fdata.append("coin", coin);

      $.ajax({
        type: "post",
        async: false,
        data: fdata,
        contentType: false,
        processData: false,
        url: "/inc/reward_process.php",
        success: function (data) {
          console.log(data);
          if (data == "over") {
            alert(
              "출금 신청한 코인이 보유한 코인 보다 많습니다.\n남은 공용 코인을 확인 해주세요."
            );
            return false;
          } else if (data == "not") {
            alert("보유한 공용코인이 부족하여 출금신청을 할수 없습니다.");
            return false;
		      } else if (data == "coin_short"){
			      alert("보유한 코인이 부족하여 출금신청을 할수 없습니다.");
            return false;
          } else if (data == "complete") {
            alert("출금 신청이 완료되었습니다.");
            location.reload();
            return false;
          }
        },
      });
    }
  });

  //초기화버튼
  $("#btn_coin_reset").click(function (){
    $("#withdraw_coin").css("color", "#858585");
    var obj = $("#withdraw_coin");
    if (obj.val()) {
      obj.val("");
      obj.focus();
      return false;
    }
  });

  //출금금액 숫자만입력
  $("#input_bank_num").bind("change keyup input",function (event) {
      if (!(event.keyCode >= 37 && event.keyCode <= 40)) {
        var inputVal = $(this).val();
        $(this).val(inputVal.replace(/[^0-9]/gi, ""));
      }
      if ($("#withdraw_coin").val()) {
        var with_coin = $("#withdraw_coin").val();
        var account_coin = $("#layer_withdraw_coin strong").text();
        account_coin = unComma(account_coin);

        if (with_coin && account_coin) {
          coin_as_color(with_coin, account_coin);
        }
      } else {
        coin_as_color(0, 0);
      }
    }
  );
  
  $("#withdraw_coin").bind("change keyup input",function (event) {
    if (!(event.keyCode >= 37 && event.keyCode <= 40)) {
      var inputVal = $(this).val();
      $(this).val(inputVal.replace(/[^0-9]/gi, ""));
    }
    if ($("#withdraw_coin").val()) {
      var with_coin = $("#withdraw_coin").val();
      var account_coin = $("#layer_withdraw_coin p").text();
      account_coin = unComma(account_coin);

      if (with_coin && account_coin) {
        coin_as_color(with_coin, account_coin);
      }

    } else {
      coin_as_color(0, 0);
    }
  });

  //입력폼 포커스 되었을때
  $(document).on("focus", "input[id^=withdraw_coin]", function () {
    focused(this.id);
  });

  //입력폼 포커스 벗어날때
  $(document).on("blur", "input[id^=withdraw_coin]", function () {
    account_coin = $("#withdraw_coin").val();
    account_ceil = account_coin / 10000;
    account_ceil2 = Math.floor(account_ceil);
    account_ceil_coin = account_ceil2 * 10000;

    $("#withdraw_coin").val(account_ceil_coin);

    blured(this.id);
  });

  //출금신청 > 은행선택하기 클릭
  $("#layer_withdraw_bank #btn_bank_on").click(function () {
    $("#layer_withdraw_bank").addClass("on");
  });

  //출금신청 > 마우스벗어날때
  $("#layer_withdraw_bank").mouseleave(function () {
    $("#layer_withdraw_bank").removeClass("on");
  });

  //출금신청 > 은행선택 했을때
  $("#layer_withdraw_bank ul li button").click(function () {
    var val = $(this).val();
    var name = $(this).text();

    if (val && name) {
      $("#btn_bank_on").text(name);
      $("#btn_bank_on").val(val);
      $("#layer_withdraw_bank").removeClass("on");
    }
  });

  //금액선택
  $("#layer_withdraw_btns button").click(function () {
    //chkobj = $("button[id=requsechk]");
    //var checkCount = chkobj.size();
    var val = $(this).val();
    var account_coin = $("#layer_withdraw_coin p").text();
    account_coin = unComma(account_coin);
    console.log(account_coin);

    if (val) {
      // 버튼의 value가 있다면
      input_coin = $("#withdraw_coin").val();
      if (input_coin) {
        // input coin이 있다면
        input_coin = unComma(input_coin); // 콤마 삭제
        input_coin = Number(input_coin); // 문자열을 int형태로 변경
        with_coin = input_coin + Number(val);
        coin_as_color(with_coin, account_coin); // 인출하려는 금액, 전체금액을 비교하는 함수
        $("#withdraw_coin").val(with_coin); // 출금할 금액 텍스트에 인출하려는 금액을 대입함.
        blured("withdraw_coin");
      } else {
        input_coin = Number(0);
        with_coin = input_coin + Number(val);
        coin_as_color(with_coin, account_coin);
        $("#withdraw_coin").val(with_coin);
        blured("withdraw_coin");
      }
    } else {
      // 버튼의 value가 없다면
      if (account_coin) {
        //전체금액을 넣음
        account_ceil = account_coin / 10000;
        account_ceil2 = Math.floor(account_ceil);
        account_ceil_coin = account_ceil2 * 10000;

        coin_as_color(0, account_coin);
        $("#withdraw_coin").val(account_ceil_coin);
        blured("withdraw_coin");
      } else {
        coin_as_color(0, 0);
        $("#withdraw_coin").val("");
      }
    }
  });

  //보상하기 - 레이어
  $("#btn_coin_reward").click(function () {
    $(".layer_user").show();
  });

  $(document).on("click","#input_reward_search_btn", function(){
    if ($(".layer_user_search_box input").val() == "") {
      alert("이름, 부서명을 입력하세요.");
      return false;
    } else {
      var fdata = new FormData();
      var input_val = $(".layer_user_search_box input").val();

      fdata.append("mode", "chall_chamyeo_search");
      fdata.append("input_val", input_val);
      fdata.append("user_chk_val", $("#chall_user_chk").val());

      $.ajax({
        type: "post",
        data: fdata,
        contentType: false,
        processData: false,
        url: "/inc/process.php",
        success: function (data) {
          console.log(data);

          var tdata = data.split("|");
          var search_chk;
          if (tdata) {
            var html = tdata[0];
            var totcnt = tdata[1].trim();
            if (tdata[2]) {
              search_chk = tdata[2].trim();
            }
            console.log(totcnt);
            console.log(search_chk);

            if (totcnt > 0) {
              if ($("#chall_user_chk").val()) {
                var user_chk_val = $("#chall_user_chk").val();
                arr_val = user_chk_val.split(",");
                if (arr_val.length > 0) {
                  userchk = arr_val.length;
                } else {
                  userchk = 0;
                }
              }

              $("#chall_user_cnt").val(totcnt);
              $(".layer_user_info").empty();

              if (search_chk > 0) {
                $("#usercnt").text("전체 " + totcnt + "명");
                $("#usercnt").text(
                  $("#usercnt").text() + ", " + search_chk + "명 선택"
                );
              } else {
                $("#usercnt").text("전체 " + totcnt + "명");
              }

              $(".layer_user_info").html(html);
            } else {
              //$("#chall_user_chk").val('');
              $("#chall_user_cnt").val("");
              $(".layer_user_info").empty();
              $("#usercnt").text("전체 " + totcnt + "명");
              $(".layer_user_info").html(html);
            }
          }
        },
      });
    }
  });

  //보상하기 - 레이어 닫기
  $(".layer_user_cancel").click(function () {
    if ($(".layer_user").is(":visible") == true) {
      $(".layer_user").hide();
    }
  });

  //보상하기 코인입력
  $("#input_coin").bind("input", function (event) {
    var input_coin = $(this).val();
    var common_coin = $("#common_coin").val();
    var user_chk_val = $("#chall_user_chk").val();

    if (user_chk_val) {
      arr_val = user_chk_val.split(",");
      if (arr_val.length > 0) {
        userchk = arr_val.length;
      } else {
        userchk = 0;
      }
    }

    comcoin = unComma(common_coin);
    input_coin = unComma(input_coin);

    comcoin = Number(comcoin);
    input_coin = Number(input_coin);

    if (input_coin && comcoin) {
      if (input_coin > comcoin) {
        alert(
          "현재보유 공용코인보다 지급하는 코인이 많습니다.\n코인을 확인 해주세요."
        );

        input_coin = String($(this).val());
        input_coin = input_coin.substr(0, input_coin.length - 1);
        $("#input_coin").val(addComma(input_coin));
        return false;
      } else {
        input_coin = input_coin * userchk;
        if (comcoin) {
          var coin = comcoin - input_coin;

          if (coin) {
            $(".layer_user_coin_num strong").text(addComma(coin));
          } else {
            $(".layer_user_coin_num strong").text(0);
          }
        }
      }
    } else {
      $(".layer_user_coin_num strong").text(common_coin);
    }
  });

  //보상하기 버튼
  $("#layer_user_submit").click(function () {
    var input_reason = $("#input_reason").val();
    var input_coin = $("#input_coin").val();

    if ($("#chall_user_chk").val()) {
      var user_chk_val = $("#chall_user_chk").val();
      var arr_val = user_chk_val.split(",");

      if (!$("#input_coin").val()) {
        alert("보상할 코인을 입력해주세요.");
        $("#input_coin").focus();
        return false;
      }

      if (!input_reason) {
        alert("보상 사유를 작성하세요.");
        $("#input_reason").focus();
        return false;
      }

      if (arr_val.length > 0) {
        userchk = arr_val.length;

        if (userchk > 1) {
          var confirm_text = "씩";
        } else {
          var confirm_text = "을";
        }
      } else {
        userchk = 0;
      }

      if (
        confirm(
          "선택한 " +
            userchk +
            "명에게 " +
            $("#input_coin").val() +
            "코인" +
            confirm_text +
            " 보상하시겠습니까?"
        )
      ) {
        var fdata = new FormData();
        fdata.append("mode", "coin_reward");
        fdata.append("user_chk_val", user_chk_val);
        fdata.append("input_coin", input_coin);
        fdata.append("input_reason", input_reason);

        $.ajax({
          type: "post",
          async: false,
          data: fdata,
          contentType: false,
          processData: false,
          url: "/inc/reward_process.php",
          success: function (data) {
            console.log(data);
            if (data == "duplication") {
              if (
                !confirm(
                  "1분 이내에 코인을 지급한 유저가 있습니다. 그래도 지급 하시겠습니까?"
                )
              ) {
                return false; //아니오
              } else {
                //예
                var fdata = new FormData();
                fdata.append("mode", "coin_reward_chk");
                fdata.append("user_chk_val", user_chk_val);
                fdata.append("input_coin", input_coin);
                fdata.append("input_reason", input_reason);

                $.ajax({
                  type: "post",
                  async: false,
                  data: fdata,
                  contentType: false,
                  processData: false,
                  url: "/inc/reward_process.php",
                  success: function (data) {
                    console.log(data);
                    if (data == "complete") {
                      alert("코인 보상이 지급 되었습니다.");
                      location.reload();
                      return false;
                    } else if(data == "penalty"){
                      alert("코인을 보내려는 유저에게 패널티가 적용되어 보낼 수 없습니다.");
                      return false;
                    } else {
                      alert("코인 지급에 실패 했습니다.");
                      return false;
                    }
                  },
                });
              }
            } else if (data == "penalty"){
              alert("코인을 보내려는 유저에게 패널티가 적용되어 보낼 수 없습니다.");
              return false;
            } else if (data == "user_me") {
              alert("자신에게는 코인을 보상할수 없습니다.");
              return false;
            } else if (data == "none") {
              alert("보유한 공용코인이 지급할 코인보다 작습니다.");
              return false;
            } else if (data == "comcoin_not") {
              alert("보유한 공용코인이 없습니다.");
              return false;
            } else if (data == "complete") {
              alert("코인 보상이 지급 되었습니다.");
              location.reload();
              return false;
            }
          },
        });
      }
    } else {
      userchk = "";
    }

    if (userchk == "") {
      alert("보상할 대상을 선택해주세요.");
      return false;
    }
  });

  //셀렉트박스 - 마우스 오버
  $("#rew_ard_sort").hover(function () {
    $("#rew_ard_sort").addClass("on");
  });

  //셀렉트박스 마우스 벗어날때
  $("#rew_ard_sort").mouseleave(function () {
    $("#rew_ard_sort").removeClass("on");
  });

  //셀렉트박스 항목선택했을때
  $("#rew_ard_sort ul li button").click(function () {
    var val = $(this).val();
    var reward_sdate = "";
    var reward_edate = "";
    var n = "";
    var string = "";
    var reward_sort = new Array();
    reward_sort["all"] = "전체보기";
    reward_sort["add"] = "보상";
    reward_sort["out"] = "차감";

    if (val) {
      if (reward_sort[val]) {
        $("#rew_ard_sort").removeClass("on");
        $("#btn_sort_on").text(reward_sort[val]);

        if ($("#reward_inquiry").val()) {
          var reward_sdate = $("#reward_sdate").val();
          var reward_edate = $("#reward_edate").val();
          if ($(".rew_ard_btns button").eq(0).hasClass("on")) {
            var n = 7;
          } else if ($(".rew_ard_btns button").eq(1).hasClass("on")) {
            var n = 30;
          } else if ($(".rew_ard_btns button").eq(2).hasClass("on")) {
            var n = 90;
          }
        }

        var fdata = new FormData();
        var string =
          "&page=reward&sdate=" +
          reward_sdate +
          "&edate=" +
          reward_edate +
          "&nday=" +
          n +
          "&type=" +
          val;
        fdata.append("mode", "reward_list");
        fdata.append("sdate", reward_sdate);
        fdata.append("edate", reward_edate);
        fdata.append("string", string);
        fdata.append("nday", n);
        fdata.append("type", val);

        $.ajax({
          type: "post",
          async: false,
          data: fdata,
          contentType: false,
          processData: false,
          url: "/inc/reward_process.php",
          success: function (data) {
            console.log(data);
            $("#rew_ard_in").html(data);
          },
        });
      }
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
    $("#tl_profile_bt").click(function() {

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
});



//출금 신청금액 비교하여 초과시 붉은색, 이하인경우 검정색
function coin_as_color(all_coin, account_coin) {
  all_coin = Number(all_coin);
  account_coin = Number(account_coin);

  if (all_coin > account_coin) {
    $("#withdraw_coin").css("color", "#f10006");
  } else {
    $("#withdraw_coin").css("color", "#858585");
  }
}
