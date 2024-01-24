$(function () {

 

    $(document).ready(function(){


        var ff_class = "";
        var ff_text = "";

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
            $(".onoff_01 .btn_switch").removeClass("on");
            $(".onoff_01 .btn_switch").prev("em").removeClass("on");
            $(".onoff_02 .btn_switch").removeClass("on");
            $(".onoff_02 .btn_switch").prev("em").removeClass("on");
            $(".onoff_03 .btn_switch").removeClass("on");
            $(".onoff_03 .btn_switch").prev("em").removeClass("on");
            $(".onoff_04 .btn_switch").addClass("on");
            $(".onoff_04 .btn_switch").prev("em").addClass("on");
        });
        $(".fl_close button").click(function(){
            $(".feeling_layer").hide();
        });

        $(".pf_box button").click(function(){
            $(".penalty_layer").show();
            $(".penalty_first").hide();
        });
        $(".pl_close button").click(function(){
            $(this).closest(".penalty_layer").hide();
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
        $(document).on('click','.live_tab_in li', function(){
            $(this).addClass('on');
            $(this).siblings().removeClass('on');
        });

        $(document).on('click','.send_heart button', function(){
            $(this).addClass('on');
            $(this).parents('div.heart_user_hover_btn').addClass('on')
            if ($(this).parents('div.heart_user_hover_btn').hasClass('on')) {
                $(this).parents('div.heart_user_hover').css({
                    opacity: '1'
                })
                setTimeout(() => {
                    $(this).closest('li').slideUp()
                }, 2000)
            }

        });

        $(document).on("click", ".heart_close", function(){
            $(this).parents('li').slideUp()
        });
        
            // 타이핑할 텍스트
            var textToType = $(".typing_event").text();
             function typeEffect() {
                const container = $('.typing_event');
                container.empty();
                let index = 0;

                function type() {
                    container.append(textToType[index]);
                    index++;

                    if (index < textToType.length) {
                        setTimeout(type, 100);
                    }
                }
                type();
             }

            $(document).ready(typeEffect);
            
            $(document).on("mouseenter", ".live_user_state li .live_user_state_circle", function() {
				$(".layer_state").removeClass("on");
				$(this).next(".layer_state").addClass("on");
				$(".live_list_box").removeClass("zindex");
				$(this).closest(".live_list_box").addClass("zindex");
			});

			$(document).on("mouseleave", ".live_user_state li .live_user_state_circle", function() {
				$(".layer_state").removeClass("on");
				$(".live_list_box").removeClass("zindex");
			});
    });
});