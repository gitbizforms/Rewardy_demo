	$(document).ready(function() {

	    $(".review_list ul").slick({
	        dots: false,
	        arrows: false,
	        infinite: true,
	        speed: 3000,
	        autoplay: true,
	        autoplaySpeed: 0,
	        cssEase: 'linear',
	        pauseOnHover: true,
	        pauseOnFocus: true,
	        variableWidth: true
	    });

	    $(".depth_01 strong.depth_01_link").mouseenter(function() {
	        $(this).next(".depth_02_area").show();
	    });
	    $(".depth_01").mouseleave(function() {
	        $(this).find(".depth_02_area").hide();
	    });

	    $(".rb_nav_mb_btn button, .rb_nav_mb_deam").click(function() {
	        $(".rb_nav_depth").toggleClass("on");
	        $(".rb_nav_member").toggleClass("on");
	        $(".rb_nav_mb_deam").toggleClass("on");
	    });

	    //리워디 회사 가입하기
	    $("#rewardy_join_btn_a").click(function() {

	        if ($("#rewardy_join_a_com").val() == "") {
	            alert("회사명을 입력해 주세요.");
	            $("#rewardy_join_a_com").focus();
	            return false;
	        } else if ($("#rewardy_join_a_id").val() == "") {
	            alert("가입할 이메일을 입력해 주세요.");
	            $("#rewardy_join_a_id").focus();
	            return false;
	        }


	        if (confirm("입력하신 이메일로 인증메일을 발송 하시겠습니까?")) {
	            var fdata = new FormData();
	            fdata.append("company", $("#rewardy_join_a_com").val());
	            fdata.append("mail", $("#rewardy_join_a_id").val());
	            $.ajax({
	                type: "POST",
	                data: fdata,
	                contentType: false,
	                processData: false,
	                url: '/inc/sendmail_about_rewardy.php',
	                success: function(data) {
	                    console.log(data);
	                    if (data == "ok") {
	                        alert("메일이 정상 발송되었습니다.");
	                        location.reload();
	                    } else if (data == "fail") {
	                        alert("메일 발송이 되지 않았습니다.");
	                        return false;
	                    } else if (data == "over") {
	                        alert("이미 가입된 이메일 입니다.\n새로운 이메일을 입력해 주세요.");
	                        $("#rewardy_join_a_id").focus();
	                        return false;
	                    }
	                }
	            });
	        }

	    });



	});