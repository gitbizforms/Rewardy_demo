$(document).ready(function(){
    setTimeout(function(){
        $(".tuto").each(function(i){
            var i = i+1;
            var tuto = $(this);
            var tt_l = tuto.offset().left;
            var tt_t = tuto.offset().top;
            var tt_w = tuto.width() / 2;
            var tt_h = tuto.height() / 2;
            var tt_x = tt_l + tt_w;
            var tt_y = tt_t + tt_h;
            var win_w = $(window).width();
            var win_h = $(window).height();
            var win_h2 = $(window).height() / 2;
            var tt_r = win_w - 400;
            var tt_p = $(".tuto_pop_01_0"+i+"").height();
            var tt_ph = tt_p + tt_y;
            if(tt_x > tt_r){
                $(".tuto_pop_01_0"+i+"").css({
                    left:"auto",
                    right:70,
                    opacity:1
                });
                $(".tuto_pop_01_0"+i+"").removeClass("tuto_l tuto_r tuto_t tuto_b").addClass("tuto_r");
            }else{
                $(".tuto_pop_01_0"+i+"").css({
                    left:(tt_x-47),
                    opacity:1
                });
                $(".tuto_pop_01_0"+i+"").removeClass("tuto_l tuto_r tuto_t tuto_b").addClass("tuto_l");
            }
            if(tt_ph > (win_h - 70)){
                $(".tuto_pop_01_0"+i+"").css({
                    top:(tt_t-tt_p-24),
                });
                $(".tuto_pop_01_0"+i+"").removeClass("tuto_l tuto_r tuto_t tuto_b").addClass("tuto_b");
            }else{
                $(".tuto_pop_01_0"+i+"").css({
                    top:(tt_y+42),
                });
                $(".tuto_pop_01_0"+i+"").removeClass("tuto_l tuto_r tuto_t tuto_b").addClass("tuto_t");
            }
            $(".tuto_mark_01_0"+i+"").css({
                left:tt_x,
                top:tt_y,
                opacity:1
            });
        });
    },1300);

    $(".tuto_mark_01_02,.tuto_mark_01_03,.tuto_mark_01_04").hide();
    $(".tuto_pop_01_02,.tuto_pop_01_03,.tuto_pop_01_04").hide();
    $(".tuto_phase").hide();

    $(window).resize(function(){
        $(".tuto").each(function(i){
            var i = i+1;
            var tuto = $(this);
            var tt_l = tuto.offset().left;
            var tt_t = tuto.offset().top;
            var tt_w = tuto.width() / 2;
            var tt_h = tuto.height() / 2;
            var tt_x = tt_l + tt_w;
            var tt_y = tt_t + tt_h;
            var win_w = $(window).width();
            var win_h = $(window).height();
            var win_h2 = $(window).height() / 2;
            var tt_r = win_w - 400;
            var tt_p = $(".tuto_pop_01_0"+i+"").height();
            var tt_ph = tt_p + tt_y;
            if(tt_x > tt_r){
                $(".tuto_pop_01_0"+i+"").css({
                    left:"auto",
                    right:70,
                    opacity:1
                });
                $(".tuto_pop_01_0"+i+"").removeClass("tuto_l tuto_r tuto_t tuto_b").addClass("tuto_r");
            }else{
                $(".tuto_pop_01_0"+i+"").css({
                    left:(tt_x-47),
                    opacity:1
                });
                $(".tuto_pop_01_0"+i+"").removeClass("tuto_l tuto_r tuto_t tuto_b").addClass("tuto_l");
            }
            if(tt_ph > (win_h - 70)){
                $(".tuto_pop_01_0"+i+"").css({
                    top:(tt_t-tt_p-24),
                });
                $(".tuto_pop_01_0"+i+"").removeClass("tuto_l tuto_r tuto_t tuto_b").addClass("tuto_b");
            }else{
                $(".tuto_pop_01_0"+i+"").css({
                    top:(tt_y+42),
                });
                $(".tuto_pop_01_0"+i+"").removeClass("tuto_l tuto_r tuto_t tuto_b").addClass("tuto_t");
            }
            $(".tuto_mark_01_0"+i+"").css({
                left:tt_x,
                top:tt_y,
                opacity:1
            });
            
        });
    }); 



    $(window).scroll(function(){
        $(".tuto").each(function(i){
            var i = i+1;
            var tuto = $(this);
            var tt_l = tuto.offset().left;
            var tt_t = tuto.offset().top;
            var tt_w = tuto.width() / 2;
            var tt_h = tuto.height() / 2;
            var tt_x = tt_l + tt_w;
            var tt_y = tt_t + tt_h;
            var win_w = $(window).width();
            var win_h = $(window).height();
            var win_h2 = $(window).height() / 2;
            var tt_r = win_w - 400;
            var tt_p = $(".tuto_pop_01_0"+i+"").height();
            var tt_ph = tt_p + tt_y;
            console.log(tt_y);
            if(tt_x > tt_r){
                $(".tuto_pop_01_0"+i+"").css({
                    left:"auto",
                    right:70,
                    opacity:1
                });
                $(".tuto_pop_01_0"+i+"").removeClass("tuto_l tuto_r tuto_t tuto_b").addClass("tuto_r");
            }else{
                $(".tuto_pop_01_0"+i+"").css({
                    left:(tt_x-47),
                    opacity:1
                });
                $(".tuto_pop_01_0"+i+"").removeClass("tuto_l tuto_r tuto_t tuto_b").addClass("tuto_l");
            }
            if(tt_ph > (win_h - 70)){
                $(".tuto_pop_01_0"+i+"").css({
                    top:(tt_t-tt_p-24),
                });
                $(".tuto_pop_01_0"+i+"").removeClass("tuto_l tuto_r tuto_t tuto_b").addClass("tuto_b");
            }else{
                $(".tuto_pop_01_0"+i+"").css({
                    top:(tt_y+42),
                });
                $(".tuto_pop_01_0"+i+"").removeClass("tuto_l tuto_r tuto_t tuto_b").addClass("tuto_t");
            }
            $(".tuto_mark_01_0"+i+"").css({
                left:tt_x,
                top:tt_y,
                opacity:1
            });
            
        });
    }); 

    
$(document).on("click",".tuto_pop_01_01 .tuto_next",function(){
    $(".tuto_mark_01_01").hide();
    $(".tuto_pop_01_01").hide();

    $(".tuto_mark_01_02").show();
    $(".tuto_pop_01_02").show();
});

$(document).on("click",".tuto_pop_01_02 .tuto_next",function(){   
    $(".rew_box").removeClass("on");
    $(".rew_menu_onoff button").removeClass("on");
    $(".tuto_mark_01_02").hide();
    $(".tuto_pop_01_02").hide();

    setTimeout(function(){
        $("#tdw_list_1depth").css("display","block");
        tuto_position();
        $(".tuto_mark_01_03").show();
        $(".tuto_pop_01_03").show();
    },1100);
});

$(document).on("click",".tuto_pop_01_03 .tuto_next",function(){
    $(".tuto_mark_01_03").hide();
    $(".tuto_pop_01_03").hide();

    $(".tuto_mark_01_04").show();
    $(".tuto_pop_01_04").show();  
    });
});


$(document).on("click",".tuto_pop_01_04 .tuto_next",function(){
    var fdata = new FormData();
    fdata.append("mode","update");
    fdata.append("level","work");

    tuto_flag = $("#tutorial_flag").val();
    if(tuto_flag > 0){
        fdata.append("not_reward","1");
    }

    $.ajax({
        type: "POST",
        data: fdata,
        contentType: false,
        processData: false,
        url: "/inc/tu_process.php",
        success: function (data) {
            console.log(data);
            $(".tuto_mark_01_04").hide();
            $(".tuto_pop_01_04").hide();
            if(tuto_flag==0){
                $(".phase_02").addClass("tuto_on");
            }
            $(".phase_01").removeClass("tuto_on");
            $(".phase_02 button").attr("onclick","location.href='/todaywork/tu_works_like.php'");
            $(".phase_01").addClass("tuto_clear")
            $(".tuto_phase").show();
        },
      });
    });

$(document).on("click",".tuto_pop_01_02 .tuto_prev",function(){
    $(".tuto_mark_01_02").hide();
    $(".tuto_pop_01_02").hide();

    $(".tuto_mark_01_01").show();
    $(".tuto_pop_01_01").show();
});

$(document).on("click",".tuto_pop_01_03 .tuto_prev",function(){
    $(".rew_box").addClass("on");
    $(".rew_menu_onoff button").addClass("on");

    setTimeout(function(){
        $("#tdw_list_1depth").css("display","none");
        tuto_position();
        $(".tuto_mark_01_02").show();
        $(".tuto_pop_01_02").show();
    },1100);

    $(".tuto_mark_01_03").hide();
    $(".tuto_pop_01_03").hide();
});

$(document).on("click",".tuto_pop_01_04 .tuto_prev",function(){
    $(".tuto_mark_01_04").hide();
    $(".tuto_pop_01_04").hide();

    $(".tuto_mark_01_03").show();
    $(".tuto_pop_01_03").show();
    
});
// 좋아요 튜토리얼

// 코인 튜토리얼

link_href = window.location.href;
//다음에 이어하기
if(link_href.includes("tu_")){
    $(document).on("click",".tuto_phase_pause button", function(){
        location.href = '/team/index.php';
        return false;
    }); 
}

$(document).on("click",".phase_01",function(){
    t_flag = $("#tutorial_flag").val();
    if(t_flag >= 0){
        location.href = '/todaywork/tu_works.php';
    }else{
        alert('이전 단계를 먼저 진행해주세요.');
    }
    return false;
});

$(document).on("click",".phase_02",function(){
    t_flag = $("#tutorial_flag").val();
    if(t_flag >= 0){
        location.href = '/todaywork/tu_works_like.php';
    }
    return false;
});

$(document).on("click",".phase_03",function(){
    t_flag = $("#tutorial_flag").val();
    if(t_flag >= 1){
        location.href = '/todaywork/tu_works_coin.php';
    }else{
        alert('이전 단계를 먼저 진행해주세요.');
    }
    return false;
});

$(document).on("click",".phase_04",function(){
    t_flag = $("#tutorial_flag").val();
    if(t_flag >= 2){
        location.href = '/party/tu_project.php';
    }else{
        alert('이전 단계를 먼저 진행해주세요.');
    }
    return false;
});

$(document).on("click",".phase_05",function(){
    t_flag = $("#tutorial_flag").val();
    if(t_flag >= 3){
        location.href = '/challenge/tu_chall.php';
    }else{
        alert('이전 단계를 먼저 진행해주세요.');
    }
    return false;
});

$(document).on("click",".phase_06",function(){
    t_flag = $("#tutorial_flag").val();
    if(t_flag >= 4){
        location.href = '/team/tu_team.php';
    }else{
        alert('이전 단계를 먼저 진행해주세요.');
    }
    return false;
});