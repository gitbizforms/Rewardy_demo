$(function () {


$(document).ready(function(){

    $(document).on("click", "#ldr .ldr_li:not(.ldr_me)", function() {
        //console.log("%%%%%");
    });


    var obj_ldr = $("#ldr .ldr_li:not(.ldr_me)");

    // $(document).on("click", "#ldr .ldr_li:not(.ldr_me)", function() {

    //     $(this).droppable({
    //         accept:"#ldr .ldr_me",
    //         classes: {
    //             "ui-droppable-active": "ldr_active",
    //             "ui-droppable-hover": "ldr_hover"
    //         },
    //         drop:function(event, ui) {
    //             console.log("드래그앤드랍");
    //             //$(this).append($("ui.draggable").clone());
    //             $(".lt_ul_02 .textarea_lt").val("");
    //             var ldr_img = $(this).find(".ldr_user_imgs").css("background-image").replace(/^url\(['"](.+)['"]\)/, '$1');
    //             var ldr_name = $(this).find(".ldr_user_name").text();
    //             var ldr_team = $(this).find(".ldr_user_team").text();
    //             $(".lt_ul_01 .ll_li .ll_name_team").text(ldr_team);
    //             $(".lt_ul_01 .ll_li .ll_name_user").text(ldr_name);
    //             var ldr_no = $(this).find(".ldr_user_name").attr("id").replace("ldr_user_name_","");
    //             var lt_id = $("#ldr_user_id_"+ ldr_no).val();
    //             $("#lt_id").val(lt_id);
    //             $(".ll_li .ll_img_user").css("background-image", "url(" + ldr_img + ")");
    //             $("#layer_team").show();
    //             $("#team_input").focus();
    //         }
    //     });
    // });


    $(document).on("click", "#template_list", function() {
        $(this).sortable({
    //		console.log(111);
        });
    });

    
    
    //$(".ldr_li").addClass("ldr_li ui-droppable");
    if($(".ldr_li")){
        $(".ldr_li").trigger("click");
    }


    //프로젝트 사용자 추가
    //$(".ldl_box").droppable({

    // $(document).on("click", ".ldl_box", function() {

    //     $(this).droppable({
    //         accept:"#ldr .ldr_me",
    //         classes: {
    //             "ui-droppable-active": "ldl_active",
    //             "ui-droppable-hover": "ldl_hover"
    //         },
    //         drop:function(event, ui) {
    //             var ldl_tit = $(this).find(".ldl_box_tit p").text();

    //             console.log(ldl_tit);

    //             $(".lp_in .lp_tit").text(ldl_tit);

    //             //var project_idx = $(this).find("button[id^=ldl_box_close_]").attr("value");
    //             var project_idx = $(this).attr("value");
    //             if(project_idx){
    //                 $("#plus_idx").val(project_idx);
    //             }

    //             var me_img = $(".ldr_me .ldr_user_img .ldr_user_imgs").css("background-image").replace(/^url\(['"](.+)['"]\)/, '$1');
    //             var me_user = $(".ldr_me .ldr_user_desc .ldr_user_name").text();
    //             var me_team = $(".ldr_me .ldr_user_desc .ldr_user_team").text();
    //             $(this).find(".ldl_box_user ul").append('<li class="ldl_box_me"><div class="ldl_box_img" style="background-image:url('+me_img+')"></div><div class="ldl_box_user"><strong>'+me_user+'</strong><span>'+me_team+'</span></div></li>');
    //             $("#layer_plus").show();
    //         }
    //     });
    // });




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
        $(".tabs_on").addClass("now_01");
    },1100);

    $(".btn_next_step_02").click(function(){
        $(".rew_cha_step_01").addClass("step_z");
        $(".rew_cha_step_02").addClass("step_z");
        $(".rew_cha_step_03").removeClass("step_z");
        $(".rew_cha_step_01").animate({"left":-100+"%"},700);
        $(".rew_cha_step_02").animate({"left":0+"%"},700);
        $(".rew_cha_step_03").animate({"left":100+"%"},700);
        
        setTimeout(function(){
            $(".rew_cha_step_01").animate({scrollTop :0}, 0);
            $(".rew_cha_step_02").animate({scrollTop :0}, 0);
            $(".rew_cha_step_03").animate({scrollTop :0}, 0);
        },200);

        setTimeout(function(){
            $(".tabs_on").addClass("now_02");
        },1100);
    });
    $(".btn_prev_step_01").click(function(){
        $(".rew_cha_step_01").addClass("step_z");
        $(".rew_cha_step_02").addClass("step_z");
        $(".rew_cha_step_03").removeClass("step_z");
        $(".rew_cha_step_01").animate({"left":0+"%"},700);
        $(".rew_cha_step_02").animate({"left":100+"%"},700);
        $(".rew_cha_step_03").animate({"left":-100+"%"},700);
        
        setTimeout(function(){
            $(".rew_cha_step_01").animate({scrollTop :0}, 0);
            $(".rew_cha_step_02").animate({scrollTop :0}, 0);
            $(".rew_cha_step_03").animate({scrollTop :0}, 0);
        },200);
        
        setTimeout(function(){
            $(".tabs_on").removeClass("now_02");
        },1100);
    });
    $(".btn_next_step_03").click(function(){
        $(".rew_cha_step_01").removeClass("step_z");
        $(".rew_cha_step_02").addClass("step_z");
        $(".rew_cha_step_03").addClass("step_z");
        $(".rew_cha_step_01").animate({"left":100+"%"},700);
        $(".rew_cha_step_02").animate({"left":-100+"%"},700);
        $(".rew_cha_step_03").animate({"left":0+"%"},700);
        
        setTimeout(function(){
            $(".rew_cha_step_01").animate({scrollTop :0}, 0);
            $(".rew_cha_step_02").animate({scrollTop :0}, 0);
            $(".rew_cha_step_03").animate({scrollTop :0}, 0);
        },200);
        
        setTimeout(function(){
            $(".tabs_on").addClass("now_03");
        },1100);
    });


    $(".rew_live_sort .btn_sort_on").click(function(){
        $(".rew_live_sort").addClass("on");
    });
    $(".rew_live_sort").mouseleave(function(){
        $(".rew_live_sort").removeClass("on");
    });
    $(".rew_live_sort ul li button").click(function(){
        $(".rew_live_sort").removeClass("on");
    });

    $(".list_function_sort .btn_sort_on").click(function(){
        $(".list_function_sort").addClass("on");
    });
    $(".list_function_sort").mouseleave(function(){
        $(".list_function_sort").removeClass("on");
    });
    $(".list_function_sort ul li button").click(function(){
        $(".list_function_sort").removeClass("on");
    });

    


    $(".rew_conts_scroll_07").scroll(function(){
        var lbt = $(".rew_live").offset().top;
        if(lbt < 120){
            $(".rew_live_my").addClass("pos_fix");
        }else{
            $(".rew_live_my").removeClass("pos_fix");
        }
    });
    $(".list_area").scroll(function(){
        var rct = $(".list_area_in").position().top;
        if(rct<60){
            $(".layer_result_right").addClass("pos_fix");
        }else{
            $(".layer_result_right").removeClass("pos_fix");
        }
    });

    $(".layer_report .report_area").scroll(function(){
        var rct = $(".layer_report .report_area_in").position().top;
        if(rct<60){
            $(".layer_report .layer_result_right").addClass("pos_fix");
        }else{
            $(".layer_report .layer_result_right").removeClass("pos_fix");
        }
    });

    $(".layer_challenge .report_area").scroll(function(){
        var rat = $(".layer_challenge .report_area_in").position().top;
        console.log(rat);
        if(rat<50){
            $(".layer_challenge .layer_result_right").addClass("pos_fix");
        }else{
            $(".layer_challenge .layer_result_right").removeClass("pos_fix");
        }
    });

    $(".desc_lr_01 .report_area").scroll(function(){
        var rat = $(".desc_lr_01 .report_area_in").position().top;
        console.log(rat);
        if(rat<50){
            $(".layer_today .layer_result_right").addClass("pos_fix");
        }else{
            $(".layer_today .layer_result_right").removeClass("pos_fix");
        }
    });
    $(".desc_lr_02 .report_area").scroll(function(){
        var rat = $(".desc_lr_02 .report_area_in").position().top;
        console.log(rat);
        if(rat<50){
            $(".layer_today .layer_result_right").addClass("pos_fix");
        }else{
            $(".layer_today .layer_result_right").removeClass("pos_fix");
        }
    });
    $(".desc_lr_03 .report_area").scroll(function(){
        var rat = $(".desc_lr_03 .report_area_in").position().top;
        console.log(rat);
        if(rat<50){
            $(".layer_today .layer_result_right").addClass("pos_fix");
        }else{
            $(".layer_today .layer_result_right").removeClass("pos_fix");
        }
    });


    $("#go_view_01").click(function(){
        var offset1 = $(".rew_conts_scroll_06").offset();
        $(".rew_conts_scroll_06").animate({scrollTop : 0}, 400+(offset1.top/2));
    });
    var offset2 = $(".rew_cha_view_result").position();
    $("#go_view_02").click(function(){
        var offset1 = $(".rew_conts_scroll_06").offset();
        var offset_sum = Math.abs(offset1.top - offset2.top);
        setTimeout(function(){
            $(".rew_conts_scroll_06").animate({scrollTop : offset2.top - 155}, offset_sum/4+200);
        },100);
    });
    var offset3 = $(".rew_cha_view_masage").position();
    $("#go_view_03").click(function(){
        var offset1 = $(".rew_conts_scroll_06").offset();
        //console.log(offset2.top);
        var offset_sum = Math.abs(offset1.top - offset3.top);
        setTimeout(function(){
            $(".rew_conts_scroll_06").animate({scrollTop : offset3.top - 155}, offset_sum/4+200);
        },100);
    });


    $(".conts_tab ul li button").click(function(){
        var drM_li = $(this).parent("li").index();

        var offt = $(".conts_main > div:eq("+drM_li+")").offset();
        var tabt = $(".conts_tab").offset();
        var matht = Math.abs(tabt.top - offt.top);
        $("html, body").animate({scrollTop:offt.top-101},matht/8+200);

        return false;
    });


    $("#open_layer_user").click(function(){
        $(".layer_user").show();
        $("#layer_test_02").hide();
        $("#layer_test_03").hide();
        $("#layer_test_01").show();
        $(".layer_user_info dl").addClass("on");
        $(".layer_user_info dd button").removeClass("on");
        $(".layer_user_submit").removeClass("on");
        $(".layer_user_info").animate({scrollTop :0}, 0);
    });

    $(".list_function_type .type_list").click(function(){
        $(".list_function_type button").removeClass("on");
        $(this).addClass("on");
        $(".list_box .list_conts").removeClass("type_img");
        $(".list_box .list_conts").removeClass("type_on");
        $(".list_box .list_conts").addClass("type_list");
        $(".list_box .list_conts").addClass("type_on");
    });
    $(".list_function_type .type_img").click(function(){
        $(".list_function_type button").removeClass("on");
        $(this).addClass("on");
        $(".list_box .list_conts").removeClass("type_list");
        $(".list_box .list_conts").removeClass("type_on");
        $(".list_box .list_conts").addClass("type_img");
        $(".list_box .list_conts").addClass("type_on");
    });

    $(".list_conts .list_ul li button").click(function(){
        $(this).toggleClass("on");
    });
    $(".rew_cha_view_result .title_area .title_more").click(function(){
        $(".layer_result").show();
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
        $(".layer_report").css({opacity:0});
    });

    $(".layer_challenge .layer_close button").click(function(){
        $(".layer_challenge").hide();
        $(".layer_challenge .report_cha .rew_cha_list_ul li").removeClass("sli");
    });

    $(".layer_today .layer_close button").click(function(){
        $(".layer_today").hide();
        $(".layer_today .report_cha .rew_cha_list_ul li").removeClass("sli");
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


    $(".layer_result_list.desc_lr_01").hide();
    $(".layer_result_list.desc_lr_02").hide();
    $(".layer_result_list.desc_lr_03").hide();
    $(".layer_today").hide();
    $(".layer_today").css({opacity:1});
    $(".btn_eval").click(function(){
        $(".layer_today .layer_result_right").removeClass("pos_fix");
        $(".layer_result_list.desc_lr_01").css({opacity:0});
        $(".layer_result_list.desc_lr_02").css({opacity:0});
        $(".desc_lr_03 .report_cha .rew_cha_list_ul li").removeClass("sli");
        $(".layer_today").show();
        $(".layer_result_tab li button").removeClass("on");
        $(".layer_result_tab li .btn_lr_03").addClass("on");
        $(".layer_result_list").hide();
        $(".layer_result_list.desc_lr_03").css({opacity:1});
        $(".layer_result_list.desc_lr_03").show();
        $(".desc_lr_03 .report_cha .rew_cha_list_ul li").each(function(){
            var tis = $(this);
            var tindex = $(this).index();
            setTimeout(function(){
                tis.addClass("sli");
            },600+tindex*200);
        });
    });


    $(".live_list .live_list_box").each(function(){
        var tis = $(this);
        var tindex = $(this).index();
        var bar_t = tis.find(".live_list_today_bar strong").text();
        var bar_b = tis.find(".live_list_today_bar span").text();
        var bar_w = bar_t/bar_b*100;
        setTimeout(function(){
            tis.addClass("sli");
            tis.find(".live_list_today_bar strong").css({width:bar_w+"%"});
        },500+tindex*100);
    });


    $(document).on('mouseenter', '.ldr_user_state li .ldr_user_state_circle', function(){
        $(".layer_state").removeClass("on");
        $(this).next(".layer_state").addClass("on");
        $(".ldr_list_box").removeClass("zindex");
        $(this).closest(".ldr_list_box").addClass("zindex");
    });
    $(document).on('mouseleave', '.ldr_user_state li .ldr_user_state_circle', function(){
        $(".layer_state").removeClass("on");
        $(".ldr_list_box").removeClass("zindex");
    });

    $(document).on('click','.live_tab_in li', function(){
        $(this).addClass('on');
        $(this).siblings().removeClass('on');
    });


    $(".layer_report").hide();

    $(".tdw_list .btn_regi_cancel").click(function(){
        $(this).closest(".tdw_list_memo_regi").hide();
    });
    $(".tdw_list .tdw_list_memo_conts_txt strong").click(function(){
        $(this).next(".tdw_list_memo_regi").show();
        var memo_width = $(this).parent(".tdw_list_memo_conts_txt").width();
        $(this).next(".tdw_list_memo_regi").css({"width":memo_width+199});
    });
    $(".tdw_list .btn_memo_del").click(function(){
        $(this).closest(".tdw_list_memo_desc").remove();
    });
    $(".tdw_list .tdw_list_memo").click(function(){
        $(".layer_memo").show();
    });
    $(".layer_memo_cancel").click(function(){
        $(".layer_memo").hide();
    });

});

$(document).ready(function(){
			
    $(".lt_close").click(function(){
        $("#layer_team").hide();
    });
    $(".ls_close").click(function(){
        $("#layer_sort").hide();
    });
    $(".lrs_close").click(function(){
        $("#layer_re_small").hide();
    });

    $(".ls_ul_01 .ll_li").click(function(){
        $(this).siblings().removeClass("on");
        $(this).addClass("on");
        $(".ls_bottom button").removeClass("btn_off").addClass("btn_on");
    });

    $(".gf_cate button").click(function(){
        $(this).siblings().removeClass("on");
        $(this).addClass("on");
    });

    $(".ls_bottom button").click(function(){
        if($(this).hasClass("btn_on")){
            var ls_user = $(".ls_ul_01 .ll_li.on").find(".ll_name_user").text();
            $("#layer_sort").hide();
            $("#gridDemo .ll_li").each(function(){
                var gdli = $(this);
                var gdlit = gdli.find(".ll_name_user").text();
                var met = $("#gridDemo .ll_me").find(".ll_name_user").text();
                if(gdlit == ls_user){
                    $("#gridDemo .ll_li").removeClass("ll_on");
                    gdli.addClass("ll_on");
                    $("#gridDemo .ll_me").addClass("ll_on");
                    $("#gridDemo .ll_li").find(".ll_meeting_list strong").remove();
                    gdli.find(".ll_meeting_list").append("<strong>"+met+"</strong>");
                    $("#gridDemo .ll_me").find(".ll_meeting_list").append("<strong>"+gdlit+"</strong>");
                }
            });

        }
    });

    $(".lrs_bottom button").click(function(){
        if($(this).hasClass("btn_on")){
            var lt_user = $(".lrs_ul_01 .lls_li .lls_name_user").text();
            $("#layer_re_small").hide();

            $("#gridSmall .lls_li").removeClass("lls_on");
            $("#gridSmall .lls_li").find(".lls_meeting_list em").remove();
            $("#gridSmall .lls_li").find(".lls_meeting_list strong").remove();

            var gdlit0 = $("#gridSmall .lls_li.on").find(".lls_name_user").text();
            var gdlit00 = $("#gridSmall .lls_li.on").find(".lls_name_team").text();
            var met0 = $("#gridSmall .lls_me").find(".lls_name_user").text();
            var met00 = $("#gridSmall .lls_me").find(".lls_name_team").text();

            $("#gridSmall .lls_li.on .lls_meeting").addClass("on");
            $("#gridSmall .lls_li.on .lls_meeting_list").append("<em>"+met00+"</em>");
            $("#gridSmall .lls_li.on .lls_meeting_list").append("<strong>"+met0+"</strong>");
            $("#gridSmall .lls_me .lls_meeting").addClass("on");
            $("#gridSmall .lls_me .lls_meeting_list").append("<em>"+gdlit00+"</em>");
            $("#gridSmall .lls_me .lls_meeting_list").append("<strong>"+gdlit0+"</strong>");

            $("#gridSmall .lls_li.on").addClass("lls_on").removeClass("on");
            $("#gridSmall .lls_me").addClass("lls_on")

        }
    });

    $(document).on("click", ".ldr_me .ldr_anno", function(){
        $("#check_profile").val(null);
         $(".layer_pro").show();
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

    mem_login_time_chk();
    setInterval(mem_login_time_chk, 1000);
});

function mem_login_time_chk(){

    var fdata = new FormData();
    fdata.append("mode", "lives_works_time");
    $.ajax({
        type: "post",
        data: fdata,
        contentType: false,
        processData: false,
        //async: false,
        url: '/inc/lives_process.php',
        success: function(data) {
            //console.log(data);
            if(data){
                var tdata = data.split("|");
                if(tdata){
                    var res_result = tdata[0];
                    var res_hour = tdata[1];
                    var res_min = tdata[2];
                    var res_sec = tdata[3];
                    if(res_result == "complete"){
                        $(".rew_now_timer strong").html(res_hour + "<em>:</em>" + res_min + "<em>:</em>" + res_sec);
                        $(".rew_now_timer").addClass("on");
                    }
                }
            }
        }
    });
}

});