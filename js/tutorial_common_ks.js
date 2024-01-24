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
});

$(document).on("click",".tuto_pop_01_01 .tuto_next",function(){
    $(".tuto_mark_01_01").hide();
    $(".tuto_pop_01_01").hide();

    $(".tuto_mark_01_02").show();
    $(".tuto_pop_01_02").show();
});

$(document).on("click",".tuto_pop_01_02 .tuto_next",function(){
    $(".tuto_mark_01_02").hide();
    $(".tuto_pop_01_02").hide();

    $(".tuto_mark_01_03").show();
    $(".tuto_pop_01_03").show();
});

$(document).on("click",".tuto_pop_01_03 .tuto_next",function(){
    $(".tuto_mark_01_03").hide();
    $(".tuto_pop_01_03").hide();

    $(".tuto_mark_01_04").show();
    $(".tuto_pop_01_04").show();
});

$(document).on("click",".tuto_pop_01_02 .tuto_prev",function(){
    $(".tuto_mark_01_02").hide();
    $(".tuto_pop_01_02").hide();

    $(".tuto_mark_01_01").show();
    $(".tuto_pop_01_01").show();
});

$(document).on("click",".tuto_pop_01_03 .tuto_prev",function(){
    $(".tuto_mark_01_03").hide();
    $(".tuto_pop_01_03").hide();

    $(".tuto_mark_01_02").show();
    $(".tuto_pop_01_02").show();
});

$(document).on("click",".tuto_pop_01_04 .tuto_prev",function(){
    $(".tuto_mark_01_04").hide();
    $(".tuto_pop_01_04").hide();

    $(".tuto_mark_01_03").show();
    $(".tuto_pop_01_03").show();
});

$(document).ready(function(){
    setTimeout(function(){
        tuto_position();
    },1300);

    $(window).resize(function(){
        tuto_position();
    }); 

    $(window).scroll(function(){
        tuto_position();
    }); 

    $(".tuto_pop_02_01 .tuto_next").click(function(){
        $(".tuto_mark_02_01").hide();
        $(".tuto_pop_02_01").hide();

        $(".jjim_first").show();
        tuto_position();

        $(".tuto_mark_02_02").show();
        $(".tuto_pop_02_02").show();
    });
});

function tuto_position(){
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
        var tt_p = $(".tuto_pop_02_0"+i+"").height();
        var tt_ph = tt_p + tt_y;
        if(tt_x > tt_r){
            $(".tuto_pop_02_0"+i+"").css({
                left:"auto",
                right:70,
                opacity:1
            });
            $(".tuto_pop_02_0"+i+"").removeClass("tuto_l tuto_r").addClass("tuto_r");
        }else{
            $(".tuto_pop_02_0"+i+"").css({
                left:(tt_x-47),
                opacity:1
            });
            $(".tuto_pop_02_0"+i+"").removeClass("tuto_l tuto_r").addClass("tuto_l");
        }
        if(tt_ph > (win_h - 70)){
            $(".tuto_pop_02_0"+i+"").css({
                top:(tt_t-tt_p-24),
            });
            $(".tuto_pop_02_0"+i+"").removeClass("tuto_t tuto_b").addClass("tuto_t");
        }else{
            $(".tuto_pop_02_0"+i+"").css({
                top:(tt_y+42),
            });
            $(".tuto_pop_02_0"+i+"").removeClass("tuto_t tuto_b").addClass("tuto_b");
        }
        $(".tuto_mark_02_0"+i+"").css({
            left:tt_x,
            top:tt_y,
            opacity:1
        });
    });
}


$(document).on("click",".tuto_pop_02_01 .tuto_next",function(){
    $(".tuto_mark_02_01").hide();
    $(".tuto_pop_02_01").hide();

    $(".tuto_mark_02_02").show();
    $(".tuto_pop_02_02").show();
});

$(document).on("click",".tuto_pop_02_02 .tuto_prev",function(){
    $(".tuto_mark_02_02").hide();
    $(".tuto_pop_02_02").hide();

    $(".tuto_mark_02_01").show();
    $(".tuto_pop_02_01").show();
});