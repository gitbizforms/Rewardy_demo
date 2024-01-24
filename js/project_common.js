$(function () {
  //내 파티
  $("#party_tab_my").click(function () {
    console.log("내파티");
    $("#page_delay").val("");
    $("#user_my").val("1");

    //전체
    if ($(".rew_cha_chk_tab .chk_tab input:eq(0)").is(":checked") == true) {
      $(".rew_cha_chk_tab .chk_tab input:eq(0)").prop("checked", false);
    }

    //종료된 파티
    if ($(".rew_cha_chk_tab .chk_tab input:eq(1)").is(":checked") == true) {
      $(".rew_cha_chk_tab .chk_tab input:eq(1)").prop("checked", false);
    }

    project_ajax_list();
  });

  //전체선택(챌린지)
  $("#cha_chk_tab_all").click(function () {
    //전체선택
    if ($("#cha_chk_tab_all").is(":checked") == true) {
      var chk_cnt = $(".rew_cha_chk_tab .chk_tab input").length;
      for (var i = 0; i < chk_cnt; i++) {
        if (
          $(".rew_cha_chk_tab .chk_tab input:eq(" + i + ")").is(":checked") ==
          false
        ) {
          $(".rew_cha_chk_tab .chk_tab input:eq(" + i + ")").prop(
            "checked",
            true
          );
        }
      }
    } else {
      var chk_cnt = $(".rew_cha_chk_tab .chk_tab input").length;
      for (var i = 0; i < chk_cnt; i++) {
        if (
          $(".rew_cha_chk_tab .chk_tab input:eq(" + i + ")").is(":checked") ==
          true
        ) {
          $(".rew_cha_chk_tab .chk_tab input:eq(" + i + ")").prop(
            "checked",
            false
          );
        }
      }
    }

    project_ajax_list();
  });

  //체크박스 선택 > 체크박스 선택 유뮤
  $(document).on("click", "input[name=cha_chk_tab]", function () {
    var chk_cnt = $(".rew_cha_chk_tab .chk_tab input").length;
    for (var i = 0; i < chk_cnt; i++) {
      if (
        $(".rew_cha_chk_tab .chk_tab input:eq(" + i + ")").is(":checked") ==
        false
      ) {
        $(".rew_cha_chk_tab .chk_tab input:eq(0)").prop("checked", false);
      }
    }
    //$("#pageno").val(1);

    //파티 전체
    if ($(".rew_cha_chk_tab .chk_tab input:eq(1)").is(":checked") == true) {
      $("#page_delay").val("");
      var chk1 = true;
    } else {
      var chk1 = false;
    }

    if ($(".rew_cha_chk_tab .chk_tab input:eq(2)").is(":checked") == true) {
      $("#page_delay").val("");
      var chk2 = true;
    } else {
      var chk2 = false;
    }

    if (chk1 && chk2) {
      if ($(".rew_cha_chk_tab .chk_tab input:eq(0)").is(":checked") == false) {
        $(".rew_cha_chk_tab .chk_tab input:eq(0)").prop("checked", true);
      }
    }

    project_ajax_list();
  });

  //검색버튼
  $("#input_search_btn").click(function () {
    var input_search = $("#input_part_search").val();
    if (!input_search) {
      alert("파티명을 입력해주세요.");
      $("#input_part_search").focus();
      return false;
    }

    var fdata = new FormData();
    var page = parseInt($("#pageno").val());

    //내 파티
    if ($(".rew_cha_chk_tab .chk_tab input:eq(0)").is(":checked") == false) {
      chk_tab = "all";
      fdata.append("chk_tab0", chk_tab);
    }

    fdata.append("mode", "project_list");
    fdata.append("search", input_search);
    fdata.append("gp", page);

    $.ajax({
      type: "post",
      data: fdata,
      contentType: false,
      processData: false,
      url: "/inc/project_process.php",
      success: function (data) {
        console.log(data);
        if (data) {
          tdata = data.split("|");
          if (tdata) {
            var html = tdata[0];
            var totcnt = tdata[1];
            var listcnt = tdata[2];

            //페이지수
            $("#page_count").val(parseInt(listcnt));

            //카운터수
            $(".rew_cha_count strong").text(totcnt);

            var html = data;
            $(".rew_cha_list_ul").html(html);

            $(".rew_cha_list_ul li").each(function () {
              var tis = $(this);
              var tindex = $(this).index();
              setTimeout(function () {
                tis.addClass("sli");
              }, 100 + tindex * 200);
            });

            setTimeout(function () {
              //	$(".rew_cha_sort").removeClass("on");
            }, 200);

            //더보기 버튼
            setTimeout(function () {
              console.log(page + " === " + $("#page_count").val());

              if (page >= $("#page_count").val()) {
                $(".rew_cha_more").hide();
              } else {
                $(".rew_cha_more").show();
              }
            }, 3000);
          }
        }
      },
    });
  });

  //파티검색 검색, 인풋박스 입력시
  $("#input_part_search").bind("input keyup", function (e) {
    var input_val = $(this).val();
    if (input_val) {
      if (e.keyCode == 13) {
        $("#input_search_btn").trigger("click");
        return false;
      }
    } else {
      project_ajax_list();
      return false;
    }
  });

  //파티 리스트 > 뷰페이지로 이동
  $(document).on("click", "#rew_part_list ul li a", function (event) {
    var val = $(this).parent().val();
    if (val) {
      if (GetCookie("user_id") != null) {
        location.href = "/party/view.php?idx=" + val;
      } else {
        $(".t_layer").show();
      }
    }
  });

  //인덱스페이지로 이동
  $(document).on("click", "#rew_part_title a", function (event) {
    //var val = $(this).parent().html();
    location.href = "/party/index.php";
  });

  var selectedSortValue = ""; // 선택된 deleay 버튼의 값 저장 변수
  var selectedDelayValue = ""; // 선택된 sort 버튼의 값 저장 변수
  var selectedUserVlaue = ""; // 선택된 user 버튼의 값 저장 변수
  $("button[id^=btn_delay]").click(function () {
    var btn_id = $(this).attr("id");
    console.log("delay 버튼 클릭 시");
    if (btn_id) {
      var selectedDelayValue = btn_id.replace("btn_delay_", "");
      $("#page_delay").val(selectedDelayValue);

      // 사용할 때 selectedSortValue 변수 사용
      if (selectedSortValue) {
        $("#page_sort").val(selectedSortValue);
      }
      if (selectedUserVlaue) {
        $("#user_my").val(selectedUserVlaue);
      }
      project_ajax_list();
    }
  });

  $("button[id^=btn_sort]").click(function () {
    var btn_id = $(this).attr("id");
    console.log("sort select 박스 클릭 시");
    if (btn_id) {
      selectedSortValue = btn_id.replace("btn_sort_", "");
      $("#page_sort").val(selectedSortValue);

      if (selectedDelayValue) {
        $("#page_delay").val(selectedDelayValue);
      }
      if (selectedUserVlaue) {
        $("#user_my").val(selectedUserVlaue);
      }
      project_ajax_list();
    }
  });

  //내파티 원할
  $("#party_tab_1").click(function () {
    //전체
    if ($(".rew_cha_chk_tab .chk_tab input:eq(0)").is(":checked") == true) {
      $(".rew_cha_chk_tab .chk_tab input:eq(0)").prop("checked", false);
    }

    //종료된 파티
    if ($(".rew_cha_chk_tab .chk_tab input:eq(1)").is(":checked") == true) {
      $(".rew_cha_chk_tab .chk_tab input:eq(1)").prop("checked", false);
    }
    console.log("원할");
    $("#page_delay").val("1");
    $("#user_my").val("1");
    project_ajax_list();
  });

  //내파티 보통
  $("#party_tab_3").click(function () {
    //전체
    if ($(".rew_cha_chk_tab .chk_tab input:eq(0)").is(":checked") == true) {
      $(".rew_cha_chk_tab .chk_tab input:eq(0)").prop("checked", false);
    }

    //종료된 파티
    if ($(".rew_cha_chk_tab .chk_tab input:eq(1)").is(":checked") == true) {
      $(".rew_cha_chk_tab .chk_tab input:eq(1)").prop("checked", false);
    }
    console.log("보통");
    $("#page_delay").val("3");
    $("#user_my").val("1");
    project_ajax_list();
  });

  //내파티 지연
  $("#party_tab_7").click(function () {
    //전체
    if ($(".rew_cha_chk_tab .chk_tab input:eq(0)").is(":checked") == true) {
      $(".rew_cha_chk_tab .chk_tab input:eq(0)").prop("checked", false);
    }

    //종료된 파티
    if ($(".rew_cha_chk_tab .chk_tab input:eq(1)").is(":checked") == true) {
      $(".rew_cha_chk_tab .chk_tab input:eq(1)").prop("checked", false);
    }
    console.log("지연");
    $("#page_delay").val("7");
    $("#user_my").val("1");
    project_ajax_list();
  });

  //파티연결 레이어 닫기
  $(document).on("click", "#pll_close button", function () {
    $("#party_link_layer").hide();
  });

  //파티연결 - 파티 선택하기
  $(document).on("click", ".party_link_layer .ldl_chk button", function () {
    $(this).closest(".ldl_box").toggleClass("on");
  });

  //마우스오버
  $(document).on("mouseenter click", "ldl_chk,.ldl_box_in", function (e) {
    $(this).css("cursor", "pointer");
  });

  //파티연결 - 파티 박스 선택시
  $(document).on("click", "#ldl_chk,.ldl_box_in", function () {
    $(this).closest(".ldl_box").toggleClass("on");
  });

  //파티에서 나가기
  $(document).on("click", "#btn_mypage_party_out", function () {
    if (confirm("파티에서 나가기 하시겠습니까?")) {
      var fdata = new FormData();
      var party_idx = $("#party_idx").val();
      fdata.append("mode", "project_part_out");
      fdata.append("project_idx", party_idx);
      $.ajax({
        type: "post",
        async: false,
        data: fdata,
        contentType: false,
        processData: false,
        url: "/inc/lives_process.php",
        success: function (data) {
          console.log(data);
          if (data) {
            var tdata = data.split("|");
            if (tdata) {
              var result = tdata[0];
              if (result == "complete") {
                //project_ajax_view();
                alert("파티에서 나가기 하였습니다.");
                location.reload();
              }
            }
          }
        },
      });
    }
  });

  //파티에 참여하기
  $(document).on("click", "#btn_mypage_party_in", function () {
    console.log("파티에 참여하기");
    if (confirm("파티에 참여 하시겠습니까?")) {
      var fdata = new FormData();
      var party_idx = $("#party_idx").val();
      fdata.append("mode", "project_user_add");
      fdata.append("project_idx", party_idx);
      fdata.append("page_kind", "party");

      $.ajax({
        type: "post",
        data: fdata,
        contentType: false,
        processData: false,
        url: "/inc/lives_process.php",
        success: function (data) {
          console.log(data);
          var tdata = data.split("|");
          if (tdata) {
            var result = tdata[0];
            var edate = tdata[1];

            if (result == "over_step1") {
              alert("이미 참여한 파티입니다.");
              return false;
            } else if (result == "over_step2") {
              alert("이미 참여한 파티입니다.");
              return false;
            } else if (result == "complete") {
              alert("파티에 참여 하였습니다.");
              location.reload();
            }
          }
        },
      });
    }
  });

  //파티연결해제
  $(document).on("click", "#party_link_reset", function () {
    if (confirm("파티 연결을 해제하시겠습니까?")) {
      var fdata = new FormData();
      var len = $("#party_link_layer").find(".ldl_box").length;
      var work_idx = $("#work_idx").val();
      var work_date = $("input[id=work_date]").val();

      for (var i = 0; i < len; i++) {
        var ldl_box_st = $("#party_link_layer")
          .find(".ldl_box")
          .eq(i)
          .hasClass("on");
        if (ldl_box_st == true) {
          val = $("#party_link_layer")
            .find(".ldl_box")
            .eq(i)
            .find("#ldl_chk")
            .val();
          fdata.append("party_idx[]", val);
        }
      }

      fdata.append("mode", "party_link_clear");
      fdata.append("work_idx", work_idx);
      fdata.append("work_date", work_date);
      $.ajax({
        type: "post",
        async: false,
        data: fdata,
        contentType: false,
        processData: false,
        url: "/inc/works_process.php",
        success: function (data) {
          console.log(data);
          if (data == "complete") {
            alert("파티가 해제 되었습니다.");
            //$(".party_link_layer .ldl_chk button").closest(".ldl_box").removeClass("on");
            $("#party_link_layer").hide();
            project_ajax_view();
            return false;
          }
        },
      });
    }
  });

  //파티 변경 완료 버튼
  $(document).on("click", "#party_link_edit", function () {
    var fdata = new FormData();
    var len = $("#party_link_layer").find(".ldl_box").length;
    var work_idx = $("#work_idx").val();
    var work_date = $("input[id=work_date]").val();

    var ldl_box_st_cnt = 0;
    for (var i = 0; i < len; i++) {
      var ldl_box_st = $("#party_link_layer")
        .find(".ldl_box")
        .eq(i)
        .hasClass("on");
      if (ldl_box_st == true) {
        val = $("#party_link_layer")
          .find(".ldl_box")
          .eq(i)
          .find("#ldl_chk")
          .val();
        fdata.append("party_idx[]", val);
        ldl_box_st_cnt++;
      }
    }

    if (ldl_box_st_cnt == 0) {
      alert("파티를 선택해주세요.");
      return false;
    }

    if (confirm("파티 연결을 변경하시겠습니까?")) {
      fdata.append("mode", "party_link_edit");
      fdata.append("work_idx", work_idx);
      fdata.append("work_date", work_date);
      $.ajax({
        type: "post",
        async: false,
        data: fdata,
        contentType: false,
        processData: false,
        url: "/inc/works_process.php",
        success: function (data) {
          console.log(data);
          //return false;
          if (data == "complete") {
            alert("파티 변경 되었습니다.");
            //$(".party_link_layer .ldl_chk button").closest(".ldl_box").removeClass("on");
            $("#party_link_layer").hide();
            project_ajax_view();
            return false;
          }
        },
      });
    }
  });
 
  $(document).on("click", ".btn_tdw_list_chk", function () {
    var val = $(this).val();
    var wdate = $("#work_date").val();
    var fdata = new FormData();
    fdata.append("mode", "works_check");
    fdata.append("idx", val);
    fdata.append("wdate", wdate);
    $.ajax({
      type: "POST",
      data: fdata,
      contentType: false,
      processData: false,
      url: "/inc/works_process.php",
      success: function (data) {
        console.log(data);
        if (data == "complete") {
          $("#tdw_list_box_" + val).addClass("on");
          // works_list();
          return false;
        } else if (data == "recomplete") {
          $("#tdw_list_box_" + val).removeClass("on");
          // works_list();
          return false;
        } else if (data == "logout") {
          $(".t_layer").show();
          return false;
        } else if (data == "req_complete") {
          alert("완료는 요청받은 사람만 가능합니다.");
          return false;
        } else if (data == "req_cancel") {
          alert("취소는 요청받은 사람만 가능합니다.");
          return false;
        } else if (data == "unclick") {
          $("#tdw_list_box_" + val).removeClass("on");
          // works_list();
          return false;
        }
      },
    });
  });

  //파티연결 및 해제
  $(document).on("click", "button[id=tdw_list_party_link]", function () {
    var val = $(this).val();
    $("#work_idx").val(val);
    var work_idx = $("#work_idx").val();
    var fdata = new FormData();
    var work_date = $(this).parent().find("#work_date").val();
    //console.log("work_date :: " + work_date);
    $("#work_date").val(work_date);
    fdata.append("mode", "party_layer_open");
    fdata.append("work_idx", work_idx);
    fdata.append("work_date", work_date);
    $.ajax({
      type: "post",
      async: false,
      data: fdata,
      contentType: false,
      processData: false,
      url: "/inc/works_process.php",
      success: function (data) {
        console.log(data);
        if (data) {
          $("#pll_box_party_link").html(data);
          $("#party_link_layer").show();
        }
      },
    });
  });

  //메모작성 관련
  //메모클릭
  $(document).on("click", ".tdw_list_party_memo", function () {
    if ($(this).val()) {
      $("#work_idx").val($(this).val());
    }
    $(".layer_memo").show();
    //$("#textarea_memo").focus();
    setTimeout(function () {
      $("#textarea_memo").focus();
    }, 100);
  });
  // 비밀업무 메모 클릭
  $(document).on("click", ".tdw_list_party_memo_secret", function () {
    if ($(this).val()) {
      $("#work_idx").val($(this).val());
    }
    $(".layer_memo_secret").show();
    //$("#textarea_memo").focus();
    setTimeout(function () {
      $("#textarea_memo_secret").focus();
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
  $(".layer_memo_secret_submit").click(function(){
		$(".layer_memo_secret").hide();
		// $(".tdw_list_memo").addClass("on");
	});

  //메모 글내용작성
  $("#textarea_memo").bind("input", function (event) {
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

  $("#textarea_memo_secret").bind("input", function (event) {
    var val = $(this).val();
    if (val) {
      if ($("#layer_memo_secret_submit").hasClass("on") == false) {
        $("#layer_memo_secret_submit").addClass("on");
        $("#textarea_memo_secret").val(val);
      }
    } else {
      $("#layer_memo_secret_submit").removeClass("on");
    }
  });


  // 메모등록버튼
  $(document).on("click", "#layer_memo_submit", function () {
    var secret_flag = 0;
    if ($("#textarea_memo").val() == "") {
      alert("메모를 작성해주세요.");

      if ($(".layer_memo").is(":visible") == false) {
        $(".layer_memo").show();
        $("#textarea_memo").focus();
      }
      return false;
    }

    if ($("#textarea_memo").val()) {
      var work_idx = $("#work_idx").val();

      var fdata = new FormData();
      fdata.append("mode", "project_comment");
      fdata.append("work_idx", $("#work_idx").val());
      fdata.append("comment", $("#textarea_memo").val());
      fdata.append("secret_flag", secret_flag);
      fdata.append("sdate", $(".input_date_l").val());
      fdata.append("edate", $(".input_date_r").val());
      // console.log(work_idx);
      var url = "/inc/project_process.php";

      // var con = $("#workslist_"+work_idx).find(".tdw_list_memo_desc").first().before(html2);
      // console.log(con);

      $.ajax({
        type: "POST",
        data: fdata,
        contentType: false,
        processData: false,
        url: url,
        beforeSend: function () {
          $(".rewardy_loading_01").css("display", "block");
        },
        complete: function () {
          $(".rewardy_loading_01").css("display", "none");
        },
        success: function (data) {
          console.log(data);
          var tdata = data.split("|");
          var enter = tdata[0];
          var html2 = tdata[1];
          if (enter == "complete") {
            // alert("asdag");
            $("#layer_memo_submit").removeClass("on");
            $("#textarea_memo").val("");
            $(".layer_memo").hide();
            find = $("#workslist_" + work_idx).find(".tdw_list_memo_desc");
            if(find.length == 0){
              $("#workslist_" + work_idx).find("div[class^=tdw_list_memo_area_in]").html(html2);
            }else{
              $("#workslist_" + work_idx).find(".tdw_list_memo_desc").first().before(html2);
            }
            
            // if ($("#work_wdate").val()) {
            //   //    feeling_banner_reload($("#work_wdate").val());
            // }
            // project_ajax_view(); 2023.08.03 주석처리

            // return false;
          } else if (enter == "logout") {
            //	$(".t_layer").show();
            //	return false;
          }
        },
      });
    }
  });

  //비밀 메모 등록
  $(document).on("click", ".layer_memo_secret_submit", function () {
    var secret_flag = '1';
    if ($("#textarea_memo_secret").val() == "") {
      alert("메모를 작성해주세요.");
      //$("#textarea_memo").focus();
      /*setTimeout(function() {
              $('#textarea_memo').focus();
            }, 1000);
            */
      
      if ($(".layer_memo_secret").is(":visible") == false) {
        $(".layer_memo_secret").show();
        $("#textarea_memo_secret").focus();
      }
      return false;
    }

    if ($("#textarea_memo_secret").val()) {
      var fdata = new FormData();
      fdata.append("mode", "work_comment");
      fdata.append("work_idx", $("#work_idx").val());
      fdata.append("comment", $("#textarea_memo_secret").val());
      fdata.append("secret_flag", secret_flag);
      var url = "/inc/works_process.php";

      $.ajax({
        type: "POST",
        data: fdata,
        contentType: false,
        processData: false,
        url: url,
        beforeSend: function () {
          $('.rewardy_loading_01').css('display', 'block');
        },
        complete: function (){
          $('.rewardy_loading_01').css('display', 'none');
        },
        success: function (data) {
          console.log(data);
          if (data == "complete") {
            $("#textarea_memo_secret").val("");
            if ($("#work_wdate").val()) {
              feeling_banner_reload($("#work_wdate").val());
            }
            works_list();
            return false;
          } else if (data == "logout") {
            //  $(".t_layer").show();
            //  return false;
          }
        },
      });
    }
  });

  //메모삭제
  $(document).on("click", ".tdw_list #btn_memo_del", function () {
    if (confirm("작성한 업무 메모를 삭제하시겠습니까?")) {
      var val = $(this).val();
      var fdata = new FormData();
      fdata.append("mode", "work_comment_del");
      fdata.append("idx", val);
      $.ajax({
        type: "POST",
        data: fdata,
        contentType: false,
        processData: false,
        url: "/inc/works_process.php",
        success: function (data) {
          console.log(data);
          if (data) {
            tdata = data.split("|");
            result = tdata[0];
            result1 = tdata[1];
            if (result == "complete") {
              $("#comment_list_" + result1).remove();
              //memo_line_check();
            }

            //$(this).closest(".tdw_list_memo_desc").remove();
          }
        },
      });
    }
  });

  //검색버튼 - 돋보기
  $(document).on("click", "#btn_input_search", function () {
    $("#btn_party_search").trigger("click");
  });

  //뷰페이지 키워드 입력박스
  $("#party_input_search").bind("input keyup", function (e) {
    var input_val = $(this).val();
    if (input_val) {
      if (e.keyCode == 13) {
        $("#btn_party_search").trigger("click");
        return false;
      }
    } else {
      //challenges_ajax_list();
      return false;
    }
  });

  //파티 뷰페이지 키워드 검색,조회 버튼
  $(document).on("click", "#btn_party_search", function () {
    console.log("검색");
    var fdata = new FormData();
    var input_search = $("#party_input_search").val();
    if (!input_search) {
      // alert("키워드를 입력해주세요.");
      // $("#party_input_search").focus();
      // return false;
    }

    if ($(".rew_member_sub_func_btns button").eq(0).hasClass("on")) {
      var n = 7;
    } else if ($(".rew_member_sub_func_btns button").eq(1).hasClass("on")) {
      var n = 30;
    } else if ($(".rew_member_sub_func_btns button").eq(2).hasClass("on")) {
      var n = 90;
    }

    var btn_sort_on = $("#btn_sort_on").val();
    var party_idx = $("#party_idx").val();
    var project_sdate = $("#project_sdate").val();
    var project_edate = $("#project_edate").val();

    fdata.append("mode", "party_view");
    fdata.append("input_search", input_search);
    fdata.append("party_idx", party_idx);

    $.ajax({
      type: "post",
      async: false,
      data: fdata,
      contentType: false,
      processData: false,
      url: "/inc/project_process.php",
      beforeSend: function () {
        $('.rewardy_loading_01').css('display', 'block');
      },
      complete: function (){
        $('.rewardy_loading_01').css('display', 'none');
      },
      success: function (data) {
        console.log(data);
        if (data) {
          tdata = data.split("|");
          if (tdata) {
            var html = tdata[0];
            var totcnt = tdata[1];
            //var listcnt = tdata[2];
            $("#tdw_list_ww").html(html);
            $(".rew_cha_count strong").html(totcnt);
          }
        }
      },
    });
  });

  //파티만들기
  $(document).on("click", "#btn_mypage_party_make", function () {
    if ($(".layer_user_slc_list_in ul li").length > 0) {
      $(".layer_user_slc_list_in ul li").remove();
    }
    $(".layer_user").show();
  });

  /*
    $("#tdw_list_party_link").on('mouseenter', function() {
        $(this).css("cursor", "Default");
        //$(this).css("background-color", "transparent !important");
        $(this).css("background-color", "#fff");
        $(this).removeClass("on");
    });


    $("#tdw_list_party_link_on").on('mouseenter', function() {
        $(this).css("cursor", "pointer");
        $(this).css("background-color", "#f5f5f5");
        $(this).addClass("on");
    });*/

  //파티구성원 새로고침
  $(document).on("click", "#btn_pu_reset", function () {
    var fdata = new FormData();
    var party_idx = $("#party_idx").val();
    fdata.append("party_idx", party_idx);
    fdata.append("mode", "part_mem_list");

    $.ajax({
      type: "post",
      data: fdata,
      contentType: false,
      processData: false,
      url: "/inc/project_process.php",
      success: function (data) {
        console.log(data);
        if (data) {
          $("#pu_list_conts_in ul").html(data);

          if ($(".btn_sort_down").hasClass("on") == true) {
            $(".btn_sort_down").removeClass("on");
          }

          if ($(".btn_sort_up").hasClass("on") == true) {
            $(".btn_sort_up").removeClass("on");
          }
        }
      },
    });
  });

  //전체 정렬버튼
  $(document).on("click", ".pu_list_header_name em button", function () {
    var val = $(this).attr("class");
    var party_idx = $("#party_idx").val();
    var fdata = new FormData();

    if (val) {
      val = val.replace(" on", "");
      fdata.append("mode", "part_mem_list");
      fdata.append("party_idx", party_idx);
      fdata.append("sort_type", "all");
      if (val == "btn_sort_up") {
        fdata.append("sort", "up");
        if ($(".btn_sort_down").hasClass("on") == true) {
          $(".btn_sort_down").removeClass("on");
        }
      } else if (val == "btn_sort_down") {
        fdata.append("sort", "down");
        if ($(".btn_sort_up").hasClass("on") == true) {
          $(".btn_sort_up").removeClass("on");
        }
      }

      $(this).addClass("on");

      $.ajax({
        type: "post",
        data: fdata,
        contentType: false,
        processData: false,
        url: "/inc/project_process.php",
        success: function (data) {
          console.log(data);
          if (data) {
            $("#pu_list_conts_in ul").html(data);
          }
        },
      });
    }
  });

  //업무수 정렬버튼
  $(document).on("click", ".pu_list_header_count em button", function () {
    var val = $(this).attr("class");
    var party_idx = $("#party_idx").val();
    var fdata = new FormData();
    fdata.append("mode", "part_mem_list");
    fdata.append("party_idx", party_idx);
    fdata.append("sort_type", "works");

    if (val) {
      val = val.replace(" on", "");
      if (val == "btn_sort_up") {
        fdata.append("sort", "up");
        if ($(".btn_sort_down").hasClass("on") == true) {
          $(".btn_sort_down").removeClass("on");
        }
      } else if (val == "btn_sort_down") {
        fdata.append("sort", "down");
        if ($(".btn_sort_up").hasClass("on") == true) {
          $(".btn_sort_up").removeClass("on");
        }
      }
      $(this).addClass("on");

      $.ajax({
        type: "post",
        data: fdata,
        contentType: false,
        processData: false,
        url: "/inc/project_process.php",
        success: function (data) {
          console.log(data);
          if (data) {
            $("#pu_list_conts_in ul").html(data);
          }
        },
      });
    }
  });

  //좋아요 정렬버튼
  $(document).on("click", ".pu_list_header_heart em button", function () {
    var val = $(this).attr("class");
    var party_idx = $("#party_idx").val();
    var fdata = new FormData();
    fdata.append("mode", "part_mem_list");
    fdata.append("party_idx", party_idx);
    fdata.append("sort_type", "heart");

    if (val) {
      val = val.replace(" on", "");
      if (val == "btn_sort_up") {
        fdata.append("sort", "up");
        if ($(".btn_sort_down").hasClass("on") == true) {
          $(".btn_sort_down").removeClass("on");
        }
      } else if (val == "btn_sort_down") {
        fdata.append("sort", "down");
        if ($(".btn_sort_up").hasClass("on") == true) {
          $(".btn_sort_up").removeClass("on");
        }
      }
      $(this).addClass("on");

      $.ajax({
        type: "post",
        data: fdata,
        contentType: false,
        processData: false,
        url: "/inc/project_process.php",
        success: function (data) {
          console.log(data);
          if (data) {
            $("#pu_list_conts_in ul").html(data);
          }
        },
      });
    }
  });

  // -------------------------------------------- 아이템 샵  헤더 ----------------------------
  $(document).on("click", "button[id^='item_img_0']", function(){
		var val = $(this).val();
		$("#img_idx").val(val);
	
		var fdata = new FormData();
	
		fdata.append("mode", "item_img_layer");
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
				$(".is_layer").html(data);
				$(".item_prof").hide();
				$(".is_layer").show();
			}
		});
	
	});

  
  $(document).on("click", ".is_layer_btn_off", function(){
    $(".is_layer").hide();
    $(".item_prof").show();
  });

  $(document).on("click", "#item_img_buy", function(){

      if(confirm("아이템을 구매 하시겠습니까?") == false){
          return false;
      }

      var val = $("#img_idx").val();
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
              }
          }
      });
  });

  //날짜선택(1주일,1개월,3개월)
  $(".rew_member_sub_func_btns button").click(function () {
    var index = $(this).index();
    //console.log("click");
    // console.log(index);

    var btn_cnt = $(".rew_member_sub_func_btns button").length;
    for (var i = 0; i < btn_cnt; i++) {
      if (index == i) {
        $(".rew_member_sub_func_btns button").eq(i).addClass("on");
      } else {
        $(".rew_member_sub_func_btns button").eq(i).removeClass("on");
      }
    }

    if ($(".rew_member_sub_func_btns button").eq(0).hasClass("on")) {
      var n = 7;
    } else if ($(".rew_member_sub_func_btns button").eq(1).hasClass("on")) {
      var n = 30;
    } else if ($(".rew_member_sub_func_btns button").eq(2).hasClass("on")) {
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
    $("#project_sdate").val(reward_sdate);
    $("#project_edate").val(reward_edate);

    $(this).each(function (index, item) {
      //console.log(index);
      /*if ($(this).eq(index).hasClass("on") == false) {
                $(this).eq(index).addClass("on");
            } else {
                $(this).eq(index).removeClass("on");
            }*/
    });

    if (
      $(".rew_member_sub_func_btns button").eq(index).hasClass("on") == false
    ) {
      //$(".rew_ard_btns button").eq(index).addClass("on");
    }
  });

  //셀렉트박스 - 마우스 오버
  $("#rew_member_sub_func_sort").hover(function () {
    $("#rew_member_sub_func_sort").addClass("on");
  });

  //셀렉트박스 마우스 벗어날때
  $("#rew_member_sub_func_sort").mouseleave(function () {
    $("#rew_member_sub_func_sort").removeClass("on");
  });

  //시작일
  $("#btn_calendar_l").click(function () {
    $("#project_sdate").focus();
  });

  //종료일
  $("#btn_calendar_r").click(function () {
    $("#project_edate").focus();
  });

  //셀렉트박스 항목선택했을때
  $("#rew_member_sub_func_sort ul li button").click(function () {
    var val = $(this).val();
    var project_sdate = "";
    var project_edate = "";
    var n = "";
    var string = "";
    var project_sort = new Array();
    project_sort["all"] = "전체보기";
    project_sort["todaywork"] = "오늘업무";
    project_sort["report"] = "보고";
    project_sort["share"] = "공유";

    //console.log(val);
    if (val) {
      if (project_sort[val]) {
        $("#rew_member_sub_func_sort").removeClass("on");
        $("#btn_sort_on").text(project_sort[val]);
        $("#btn_sort_on").val(val);
        if ($("#reward_inquiry").val()) {
          var project_sdate = $("#project_sdate").val();
          var project_edate = $("#project_edate").val();
          if ($(".rew_ard_btns button").eq(0).hasClass("on")) {
            var n = 7;
          } else if ($(".rew_ard_btns button").eq(1).hasClass("on")) {
            var n = 30;
          } else if ($(".rew_ard_btns button").eq(2).hasClass("on")) {
            var n = 90;
          }
        }

        /*
                var fdata = new FormData();
                var string = "&page=reward&sdate=" + reward_sdate + "&edate=" + reward_edate + "&nday=" + n + "&type=" + val;
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
                    url: '/inc/reward_process.php',
                    success: function(data) {
                        console.log(data);
                        $("#rew_ard_in").html(data);
                    }
                });
				*/
      }
    }
  });

  //파티 셀렉트박스 항목선택했을때
  $("#rew_party_sort ul li button").click(function () {
    var val = $(this).val();
    var project_sdate = "";
    var project_edate = "";
    var n = "";
    var string = "";
    var project_sort = new Array();
    project_sort["created"] = "파티 생성일 순";
    project_sort["updated"] = "업데이트 순";
    project_sort["p_desc"] = "업무 많은 순";
    project_sort["p_asc"] = "업무 적은 순";
    project_sort["c_desc"] = "코인 많은 순";

    console.log(val);

    if (val) {
      if (project_sort[val]) {
        $("#rew_party_sort").removeClass("on");
        $("#btn_on").text(project_sort[val]);
        $("#btn_on").val(val);
      }
    }
  });

  //좋아요 보내기
  $(document).on("click", "#btn_pu_heart", function () {
    var val = $(this).val();
    var party_idx = $("#party_idx").val();

    $("#btn_pu_heart").val(val);

    var send_user = $(".pu_list_conts_name")
      .find("#user_name_" + val + " strong")
      .text();
    var send_userid = $(".pu_list_conts_name")
      .find("#pu_list_id_" + val)
      .val();

    if (send_userid) {
      $("#send_userid").val(send_userid);
    }

    $(".jf_box_in .jf_top strong span").text(send_user);
    $(".jjim_first").show();
  });

  //좋아요 보내기
  $(document).on("click", "button[id^=tdw_list_party_heart]", function () {
    var val = $(this).val();
    console.log(val);
    if ($(this).hasClass("on") == true) {
      return false;
    }
    var fdata = new FormData();
    fdata.append("work_idx", val);
    fdata.append("service", "party");
    fdata.append("mode", "work_todaywork_check");

    $.ajax({
      type: "post",
      async: false,
      data: fdata,
      contentType: false,
      processData: false,
      url: "/inc/works_process.php",
      success: function (data) {
        console.log(data);
        if (data) {
          var tdata = data.split("|");
          if (tdata) {
            if(tdata[2] == "penalty_on"){
              alert("좋아요를 보내려는 유저에게 페널티가 적용되어 보낼 수 없습니다.");
              return false;
            } else if (tdata[2] == "like_check") {
               //좋아요 이력이 있을때 아무런동작 하지않음
              return false;
            } else {
              var uid = tdata[0];
              var name = tdata[1];
              $("#send_userid").val(uid);
              $(".jf_box_in .jf_top strong span").text(name);
              $(".jl_box_in .jl_top strong span").text(name);
              $("#work_idx").val(val);
              $("#service").val("party");
              $(".jjim_first").show();
            }
          }
        }
      },
    });
  });

  //챌린지 검색
  $("#input_search_project").bind("input keyup", function (e) {
    var input_val = $(this).val();
    if (input_val) {
      if (e.keyCode == 13) {
        $("#btn_inquiry_btn").trigger("click");
        return false;
      }
    }
  });

  //검색조회
  $(document).on("click", "#btn_inquiry_btn", function () {
    console.log("검색");

    var project_sdate = $("#project_sdate").val();
    var project_edate = $("#project_edate").val();
    var btn_sort_on = $("#btn_sort_on").val();
    var input_search_project = $("#input_search_project").val();
    var party_idx = $("#party_idx").val();

    if ($(".rew_member_sub_func_btns button").eq(0).hasClass("on")) {
      var n = 7;
    } else if ($(".rew_member_sub_func_btns button").eq(1).hasClass("on")) {
      var n = 30;
    } else if ($(".rew_member_sub_func_btns button").eq(2).hasClass("on")) {
      var n = 90;
    }

    var fdata = new FormData();
    fdata.append("mode", "project_work_list");
    fdata.append("input_search_project", input_search_project);
    fdata.append("sdate", project_sdate);
    fdata.append("edate", project_edate);
    fdata.append("type", btn_sort_on);
    fdata.append("nday", n);
    fdata.append("party_idx", party_idx);

    $.ajax({
      type: "post",
      async: false,
      data: fdata,
      contentType: false,
      processData: false,
      url: "/inc/project_process.php",
      success: function (data) {
        console.log(data);
        $("#tdw_list_ww").html(data);
      },
    });
  });

  $("#pu_list_conts_in li").hover(function () {
    //$(this).addClass("on");
    $(this).css("cursor", "pointer");
  });

  $(".rew_cha_tab_sort").hover(function () {
    //		$(".rew_cha_tab_sort").addClass("on");
  });

  //파티구성원 선택
  $(document).on("click", "#pu_list_conts_in li", function () {
    /*
		////선택시 아이디별로 검색기능 
		var licnt = $("#pu_list_conts_in li").length;
        var index = $(this).index();

        //console.log(licnt);
        //console.log(index);

        var party_uid = new Array();
        for (var i = 0; i < licnt; i++) {
            if (index == i) {
                if ($("#pu_list_conts_in li").eq(i).hasClass("on") == true) {
                    $("#pu_list_conts_in li").eq(i).removeClass("on");
                } else {

                    $("#pu_list_conts_in li").eq(i).addClass("on");
                    //v = $(".pu_list_conts_name").find("#pu_list_id_393").val();
                    //v = $(".pu_list_conts_name").eq(i).attr("id");
                    //v = $(this).attr("id");

                    //var id = $(this).find(".user_name").attr("id");
                    //var no = id.replace("user_name_", "")
                    //val = $("#pu_list_id_" + no).val();

                }
            }
        }

        var id = $(this).find(".user_name").attr("id");
        var no = id.replace("user_name_", "")
        val = $("#pu_list_id_" + no).val();

        console.log(val);
		
        var fdata = new FormData();
        fdata.append("mode", "project_work_list");
		*/
  });

  //보상하기 닫기
  $("#lr_close").click(function () {
    $(".lr_area li button").removeClass("on");
    $("#lr_input").val("");
    $("#lr_input_text").val("");
    $("#lr_uid").val("");
    $("#layer_reward").hide();
  });

  //보상하기 선택
  $(".lr_area li button").click(function () {
    var val = $(this).val();

    $(".lr_area li button").removeClass("on");
    $(this).addClass("on");
    if (val) {
      $("#lr_val").val(val);
    }
  });

  //보상하기 클릭
  $(".lr_area ul li button").click(function () {
    var coin = $(this).find(".lr_coin em").text();
    var lr_input_text = $(this).find(".lr_txt strong").text();
    $("#lr_input").val(coin);
    $("#lr_input_text").val(lr_input_text);
  });

  //파티 코인보내기
  $(document).on("click", "#party_coin", function () {
    $("#layer_reward").show();
    $(".lr_btn").addClass("party");

    var idx = $("#party_idx").val();
    $("#lr_work_idx").val(idx);
    $(".btn_lr_01").trigger("click");
  });

  //파티 코인지급하기
  $(document).on("click", "#btn_pu_coin", function () {
    var val = $(this).val();

    $("#layer_reward").show();
    $(".btn_lr_01").trigger("click");
    if (val) {
      var r_user = $("#pu_list_id_" + val).val();
      console.log(r_user);
      $("#lr_uid").val(r_user);
      $("#lr_work_idx").val(val);
    }
  });

  //보상하기 버튼
    $(document).on("click",".lr_btn",function(){
      var lr_work_idx = $("#lr_work_idx").val();
      //console.log("lr_work_idx::"+lr_work_idx);

      var coin = $("#lr_input").val();
      var lr_uid = $("#lr_uid").val();
      var lr_val = $("#lr_val").val();
      var work_flag = $("#work_flag").val();
      var lr_input_text = $("#lr_input_text").val();
      if (!coin) {
        alert("지급할 코인을 선택 또는 입력해 주세요.");
        $("#lr_input").focus();
        return false;
      }

      if (!lr_input_text) {
        alert("메세지를 입력하세요.");
        $("#lr_input_text").focus();
        return false;
      }

      if (lr_uid) {
        var mode = "member_coin_reward";
      } else {
        mode = "party_coin_reward";
      }

        if (confirm(coin + "코인을 지급 하시겠습니까?")) {
        var fdata = new FormData();

        fdata.append("mode", mode);

        fdata.append("coin", coin);
        fdata.append("lr_val", lr_val);
        fdata.append("lr_uid", lr_uid);
        fdata.append("lr_input_text", lr_input_text);
        fdata.append("lr_work_idx", lr_work_idx);
        fdata.append("work_flag", work_flag);

        $.ajax({
          type: "post",
          async: false,
          data: fdata,
          contentType: false,
          processData: false,
          url: "/inc/party_process.php",
          success: function (data) {
            console.log(data);
            if (data) {
              if(data == "penalty") {
                alert("해당 유저에게 패널티가 적용되어 코인을 보낼 수 없습니다.");
                return false;
              } else if (data == "id_same") {
                alert("보상은 본인에게 지급할 수 없습니다. ");
                return false;
              } else if (data == "none") {
                alert(
                  "보유한 공용코인이 지급할 코인보다 작습니다.\n보상할 코인을 확인해 주세요."
                );
                $("#lr_input").focus();
                return false;
              } else if (data == "complete") {
                alert(lr_input_text + " " + coin + "코인이 보상 되었습니다.");
                $("#lr_input").val("");
                $("#layer_reward").hide();
                location.reload();
              }
            }
          },
        });
      }
  });

  //보상하기
  $(document).on("click", "#coin_reward", function () {
    var val = $(this).val();
    console.log(val);

    $(".lr_btn").removeClass("live");
    $(".lr_btn").removeClass("work");
    $(".lr_btn").addClass("party");
    
    $("#layer_reward").show();
    $(".btn_lr_01").trigger("click");
    if (val) {
      var workflag = $("#work_flag_" + val).val();
      var r_user = $("#pu_list_id_" + val).val();
      // console.log(workflag);
      $("#lr_uid").val(r_user);
      console.log(r_user);
      $("#lr_work_idx").val(val);
      $("#work_flag").val(workflag);
    }
  });

  $(document).on("click", ".btn_req_100c", function () {
    var val = $(this).val();
    console.log(val);

    $("#layer_reward").show();
    $(".btn_lr_01").trigger("click");
    if (val) {
      var r_user = $("#comment_idx_" + val).val();
      console.log(r_user);
      $("#lr_uid").val(r_user);
      $("#lr_work_idx").val(val);
    }
  });

  //파티종료
  $(document).on("click", "#btn_mypage_party_end", function () {
    var p_close = $("#party_close_flag").val();

    if (p_close == 0) {
      $(".layer_party_end").show();
    } else {
      alert("파티장만 파티를 종료할 수 있습니다.");
      return false;
    }
  });

  //파티삭제
  $("#btn_mypage_party_del").click(function () {
    if (confirm("파티를 삭제하시겠습니까?")) {
      party_idx = $("#party_idx").val();
      var fdata = new FormData();
      fdata.append("mode", "project_del");
      fdata.append("project_idx", party_idx);
      $.ajax({
        type: "POST",
        data: fdata,
        contentType: false,
        processData: false,
        url: "/inc/lives_process.php",
        success: function (data) {
          console.log(data);
          if (data == "complete") {
            alert("파티가 삭제되었습니다.");
            location.href = "/party/index.php";
            return false;
            //	$("#ldl_in #" + id).closest(".ldl_box").remove();
            //	var ldl_length = Math.abs($(".rew_mypage_section em:eq(0)").text()) - 1;
            //	$(".rew_mypage_section em:eq(0)").text(ldl_length);
          }
        },
      });
    }
  });

  //파티원관리
  $("#btn_mypage_party_admin").click(function () {
    var fdata = new FormData();
    var party_idx = $("#party_idx").val();

    fdata.append("mode", "project_user_layer");
    fdata.append("party_idx", party_idx);

    $.ajax({
      type: "POST",
      data: fdata,
      contentType: false,
      processData: false,
      url: "/inc/project_process.php",
      success: function (data) {
        //console.log(data);
        if (data) {
          $("#chall_user_chk").val(data);
        }

        layer_user_info_list();
        layer_user_slc_list();

        if ($(".layer_user_submit").hasClass("on") == false) {
          $(".layer_user_submit").addClass("on");
        }

        $("#layer_user").show();
        $(".layer_user_submit").attr("id", "layer_user_party");
        $(".layer_user_search_desc strong").text("파티 구성원");
      },
    });
  });

  //파티원관리-설정하기
  $(document).on("click", "#layer_user_party", function () {
    console.log("설정하기");

    var party_idx = $("#party_idx").val();
    var chall_user_chk = $("#chall_user_chk").val();
    if (!chall_user_chk) {
      alert("파티 구성원을 선택해주세요");
      return false;
    }

    if (confirm("파티 구성원을 변경하시겠습니까?")) {
      var fdata = new FormData();
      fdata.append("mode", "project_user_edit");
      fdata.append("party_idx", party_idx);
      fdata.append("chall_user_chk", chall_user_chk);

      $.ajax({
        type: "post",
        async: false,
        data: fdata,
        contentType: false,
        processData: false,
        url: "/inc/project_process.php",
        success: function (data) {
          console.log(data);
          if (data == "complete") {
            alert("파티 구성원이 변경되었습니다.");
            location.reload();
            return false;
          }
        },
      });
    }
  });

  //첨부파일 다운로드
  $(document).on("click", "button[id^=btn_list_file]", function () {
    var url = "/inc/file_download.php";
    var num;
    var id = $(this).attr("id");

    if (id) {
      var num = id.replace("btn_list_file_", "");
      var idx = $(this).val();
      var mode = "todaywork";
      var params = { idx: idx, num: num, mode: mode };
      //console.log(params);
      var fdata = new FormData();
      fdata.append("mode", mode);
      fdata.append("idx", idx);
      fdata.append("num", num);

      $.ajax({
        type: "POST",
        data: fdata,
        contentType: false,
        processData: false,
        url: url,
        success: function (data) {
          console.log(data);
          if (data) {
            var tdata = data.split("|");
            if (tdata) {
              var f_result = tdata[0];
              var f_name = tdata[1];
              var f_url = tdata[2];
              if (f_result == "complete") {
                fdownload(f_name, f_url);
              }
            }
          }
        },
      });
    }
  });

  //////종료된 파티//////

  //파티 코인보내기 - 파티종료
  //코인보내기, 파티삭제, 파티종료, 코인, 좋아요, 파티나가기, 파티참여하기, 파티원관리
  $(document).on(
    "click",
    "#party_coin_expire, #btn_mypage_party_end_expire, #btn_pu_coin_expire, #btn_pu_heart_expire, #btn_mypage_party_out_expire, #btn_mypage_party_in_expire, #btn_mypage_party_admin_expire",
    function () {
      alert("종료된 파티입니다.");
      return false;
    }
  );

  //종료된 파티
  $(document).on("click", "#btn_mypage_party_del_expire", function () {
    if (confirm("파티를 삭제하시겠습니까?")) {
      party_idx = $("#party_idx").val();
      var fdata = new FormData();
      fdata.append("mode", "project_del");
      fdata.append("project_idx", party_idx);
      $.ajax({
        type: "POST",
        data: fdata,
        contentType: false,
        processData: false,
        url: "/inc/lives_process.php",
        success: function (data) {
          console.log(data);

          if (data == "party_del_not") {
            alert("종료된 파티 입니다.");
            return false;
          } else if (data == "complete") {
            alert("파티가 삭제되었습니다.");
            location.href = "/party/index.php";
            return false;
          }
        },
      });
    }
  });

  $("#lpe_off").click(function () {
    $(".layer_party_end").hide();
  });

  $("#lpe_on").click(function () {
    var p_idx = $("#party_idx").val();
    var p_edate = $("#party_e_date").val();
    var p_coin = $("#party_r_coin").val();
    var r_m_name = $("#party_r_name").val();

    // alert(p_coin);

    var mode = "share_coin";
    // alert(r_m_name);
    // alert(numberCommas(p_coin));

    if (confirm("파티를 종료하시겠습니까?")) {
      $(".rewardy_loading_01").css("display", "block");
      var fdata = new FormData();
      fdata.append("mode", mode);
      fdata.append("p_idx", p_idx);
      fdata.append("p_edate", p_edate);
      fdata.append("p_coin", p_coin);

      $.ajax({
        type: "post",
        async: false,
        data: fdata,
        contentType: false,
        processData: false,
        url: "/inc/party_process.php",
        success: function (data) {
          console.log(data);

          if (data == "complete") {
            alert("해당 파티원에게 " + numberCommas(p_coin) + "코인이 보상 되었습니다.");
            $(".layer_party_end").hide();
            $(".rewardy_loading_01").css("display", "none");
            location.href = "/party/index.php";
          } else if (data == "ncoin") {
            alert("파티가 종료되었습니다.");
            $(".layer_party_end").hide();
            $(".rewardy_loading_01").css("display", "none");
            location.href = "/party/index.php";
          }
        },
      });
    }
  });

  //파티 리스트 더보기
  $("#project_more").click(function () {
    var fdata = new FormData();
    var page = parseInt($("#pageno").val());
    var lastcnt;

    if ($("#project_more").css("display") == "block") {
      $("#project_more").hide();
    }

    var li_totcnt = parseInt($(".rew_cha_list_ul li").length);
    if (li_totcnt >= 0) {
      var li_cnt = li_totcnt;
    }

    //페이지
    if (page > 0) {
      page = page + 1;
    }

    //내파티
    if ($(".rew_cha_chk_tab .chk_tab input:eq(0)").is(":checked") == false) {
      chk_tab = "all";
      fdata.append("chk_tab0", chk_tab);
    }

    if ($("#page_delay").val()) {
      fdata.append("page_delay", $("#page_delay").val());
    }
    if ($("#page_sort").val()) {
      fdata.append("page_sort", $("#page_sort").val());
    }

    if ($("#user_my").val()) {
      fdata.append("user_my", $("#user_my").val());
    }

    fdata.append("mode", "project_list");
    fdata.append("gp", page);

    $.ajax({
      type: "post",
      data: fdata,
      contentType: false,
      processData: false,
      url: "/inc/project_process.php",
      success: function (data) {
        if (data) {
          tdata = data.split("|");
          console.log("더 보기 데이터=======" + tdata)
          if (tdata) {
            var html = tdata[0];
            var totcnt = tdata[1];
            var listcnt = tdata[2];
            lastcnt = tdata[3];

            $("#pageno").val(page);
            $("#page_count").val(parseInt(listcnt));

            $(".rew_cha_list_ul").append(html);

            setTimeout(function () {
              $(".offset0").removeClass("offset0");
            }, 1100);

            $(".rew_cha_list_ul li:not('.sli')").each(function (aa) {
              var tis = $(this);
              var tindex = $(this).index();
              //alert(tindex);
              setTimeout(function () {
                tis.addClass("sli");
              }, 700 + (aa + 1) * 150);
            });

            setTimeout(function () {
              if (page >= $("#page_count").val()) {
                $(".project_more").hide();
              } else {
                $(".project_more").show();
              }
            }, 3000);

            return false;
          }
        }
      },
    });
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
                                    $(".cha_user_box .cha_user_me").css("background-image", "url(" + result2 + ")");
                                    $(".user_img").css("background-image", "url(" + result2 + ")");
                                }
                                if(result3){
                                    $(".cha_user_box .cha_user_me").css("background-image", "url(" + result2 + ")");
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

//파티 리스트
function project_ajax_list() {
  var fdata = new FormData();
  // var page = parseInt($("#pageno").val());
  var cate = 0;
  var page_count = parseInt($("#page_count").val());
  var chk_tab = "";
  var page = 1;
  //내파티
  // 전체 파티 및 종료된 파티 없앰 2023.05.11
  if ($(".rew_cha_chk_tab .chk_tab input:eq(0)").is(":checked") == true) {
    chk_tab = "2";

    if (chk_tab == "1") {
      $("#user_my").val("");
    } else {
      $("#user_my").val(1);
    }
    fdata.append("chk_tab2", chk_tab);
  } else {
    chk_tab = "all";
    $("#user_my").val(1);
    fdata.append("chk_tab0", chk_tab);
  }

  if ($("#page_delay").val()) {
    fdata.append("page_delay", $("#page_delay").val());
  }
  if ($("#page_sort").val()) {
    fdata.append("page_sort", $("#page_sort").val());
  }

  if ($("#user_my").val()) {
    fdata.append("user_my", $("#user_my").val());
  }
  fdata.append("mode", "project_list");
  fdata.append("gp", page);

  $.ajax({
    type: "post",
    data: fdata,
    contentType: false,
    processData: false,
    url: "/inc/project_process.php",
    success: function (data) {
      console.log("파티 딜레이 구간");
      if (data) {
        tdata = data.split("|");
        // console.log("tadata-----" + tdata);
        if (tdata) {
          var html = tdata[0];
          var totcnt = tdata[1];
          var listcnt = tdata[2];
          
          //페이지수
          $("#pageno").val(page);
          $("#page_count").val(parseInt(listcnt));
          $(".rew_cha_count strong").text(totcnt);
          //    $(".rew_cha_list_ul").empty();

          $(".rew_cha_list_ul").html("");

          $(".rew_cha_list_ul").append(html);

          $(".rew_cha_list_ul li").each(function () {
            var tis = $(this);
            var tindex = $(this).index();
            setTimeout(function () {
              tis.addClass("sli");
            }, 100 + tindex * 200);
          });
          //더보기 버튼
          setTimeout(function () {
            if (page >= $("#page_count").val()) {
              $(".project_more").hide();
            } else {
              $(".project_more").show();
            }
          }, 10);
        }
        return false;
      }
    },
  });
}

function project_ajax_view() {
  var fdata = new FormData();
  var party_idx = $("#party_idx").val();
  var date_flag = 1;

  fdata.append("mode", "party_view");
  fdata.append("party_idx", party_idx);
  fdata.append("date_flag", date_flag);

  $.ajax({
    type: "post",
    data: fdata,
    contentType: false,
    processData: false,
    url: "/inc/project_process.php",
    beforeSend: function () {
      $(".rewardy_loading_01").css("display", "block");
    },
    complete: function () {
      $(".rewardy_loading_01").css("display", "none");
    },
    success: function (data) {
      console.log(data);
      if (data) {
        $("#tdw_list_ww").html(data);
        tdata = data.split("|");
        if (tdata) {
          //var html = tdata[0];
          //var totcnt = tdata[1];
          //var listcnt = tdata[2];
          //$("#tdw_list_ww").html(html);
        }
      }
    },
  });
}

function layer_user_info_list() {
  var input_val = $(".layer_user_search_box input").val();
  var fdata = new FormData();
  var arr_val = new Array();
  var userchk = 0;

  fdata.append("mode", "chall_user_list");
  fdata.append("layer_user_list", "1");
  fdata.append("user_chk_val", $("#chall_user_chk").val());

  //console.log("chall_user_chk  :: " + $("#chall_user_chk").val());

  if ($("#chall_user_chk").val()) {
    user_chk_val = $("#chall_user_chk").val();
    arr_val = user_chk_val.split(",");
    if (arr_val.length > 0) {
      userchk = arr_val.length;
    } else {
      userchk = 0;
    }
  }

  fdata.append("input_val", input_val);
  $.ajax({
    type: "post",
    data: fdata,
    contentType: false,
    processData: false,
    url: "/inc/process.php",
    success: function (data) {
      console.log("리스트 시작하기2");
      console.log("data :: " + data);

      var tdata = data.split("|");
      if (tdata) {
        var html = tdata[0];
        var totcnt = tdata[1].trim();

        if (totcnt > 0) {
          $("#chall_user_cnt").val(totcnt);
          $(".layer_user_info").empty();

          if (userchk > 0) {
            $("#usercnt").text("전체 " + totcnt + "명, " + userchk + "명 선택");
          } else {
            $("#usercnt").text("전체 " + totcnt + "명");
          }
          $(".layer_user_info").html(html);
        } else {
          $("#chall_user_chk").val("");
          $("#chall_user_cnt").val(totcnt);
          $(".layer_user_info").empty();
          $("#usercnt").text("전체 " + totcnt + "명");
          $(".layer_user_info").html(html);
        }
      }
    },
  });
}

function layer_user_slc_list() {
  var fdata = new FormData();
  fdata.append("mode", "project_user_create");
  fdata.append("user_chk_val", $("#chall_user_chk").val());

  $.ajax({
    type: "post",
    data: fdata,
    contentType: false,
    processData: false,
    url: "/inc/lives_process.php",
    success: function (data) {
      console.log(data);

      if (data) {
        if ($("#chall_user_chk").val()) {
          var user_chk_val = $("#chall_user_chk").val();
          arr_val = user_chk_val.split(",");
          if (arr_val.length > 0) {
            userchk = arr_val.length;
          } else {
            userchk = 0;
          }
        }

        if (userchk > 4) {
          if ($(".lm_area .layer_user_slc_list").hasClass("over_4") == false) {
            $(".lm_area .layer_user_slc_list").addClass("over_4");
          }
        } else {
          $(".lm_area layer_user_slc_list").removeClass("over_4");
        }

        $("#layer_user_slc_list_in ul").html(data);
        //$(".layer_user").hide();
        //$("#layer_make").show();
      }
    },
  });
}

function pro_like(pro_idx) {
  var fdata = new FormData();
  fdata.append("mode", "project_like");
  fdata.append("pro_idx", pro_idx);

  $.ajax({
    type: "post",
    data: fdata,
    contentType: false,
    processData: false,
    url: "/inc/project_process.php",
    success: function (data) {
      console.log(data);
      project_ajax_list();
    },
  });
}

//파티 댓글 좋아요
function works_list() {
  var idx = $("#work_idx").val();
  $("#comment_list_" + idx)
    .find("button")
    .addClass("on");
}

//파티 링크 좋아요
function project_like(likeidx) {
  var like_idx = $("#work_idx").val();
  $("#tdw_list_party_heart_" + like_idx).addClass("on");
  var jl_comment  = $("#jl_comment").val();

  var fdata = new FormData();
  fdata.append("mode", "party_like");
  fdata.append("idx", like_idx);
  fdata.append("comment",jl_comment);
  fdata.append("like_idx",likeidx);
  $.ajax({
    type: "post",
    data: fdata,
    contentType: false,
    processData: false,
    url: "/inc/process.php",
    success: function (data) {
      console.log(data);
      var tdata = data.split("|");
      result = tdata[0];
      html = tdata[1];
      if (result) {
        find = $("#workslist_" + like_idx).find(".tdw_list_memo_desc");
        if(find.length == 0){
          $("#workslist_" + like_idx).find("div[class^=tdw_list_memo_area_in]").html(html);
        }else{
          $("#workslist_" + like_idx).find(".tdw_list_memo_desc").first().before(html);
        }
      }
    },
  });
}
