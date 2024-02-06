<?
	// $home_dir = str_replace( basename(__DIR__) , "" , __DIR__ );

	//연결되는 서버에 따라 파일따로 사용함, 사용언어:php-mysql, php-mssql

	// 윈도우서버용 php-mssql 사용, 도메인 : http://demo.rewardy.co.kr
	//리눅스서버용 php-mysql 사용, 도메인 : http://officeworker.co.kr

	//윈도우 환경 변수 : /inc_lude/conf.php
	//윈도우 환경 함수 : /inc_lude/func.php

	//리눅스 환경 변수 : /inc_lude/conf_mysqli.php
	//리눅스 환경 함수 : /inc_lude/func_mysqli.php

	include $home_dir . "inc_lude/conf_mysqli.php";
	include DBCON_MYSQLI;
	include FUNC_MYSQLI;

	//디렉토리 추출
	$get_dirname = str_replace(NAS_HOME_DIR,"", get_dirname());

	if(!$user_id){
	//	header("Location:http://demo.rewardy.co.kr/myinfo/index.php");z
	//	exit;
	}

	//쿼리스트링이 있을경우
	//로그인되어 있는경우
	//로그아웃 처리
	if($_SERVER['QUERY_STRING']){
		parse_str(Decrypt($_SERVER['QUERY_STRING']), $output);
		if($user_id){

			//보낸이메일, 받는이메일, 멤버idx
			if($output['send_email'] && $output['to_email'] && $output['sendno']){

				//쿠키값(아이디, 아이디저장여부:아이디, 아이디저장여부)
				$DelNotCookieArr = array("cid", "id_save");
				if($_COOKIE){
					foreach( $_COOKIE as $key => $value ){
						//쿠키삭제예외
						if(!in_array($key, $DelNotCookieArr)) {
							setcookie( $key, $value, time()-3600 , '/', C_DOMAIN);
							unset($_COOKIE[$key]);
							// header("Location:https://".$_SERVER['HTTP_HOST']."/team/?".$_SERVER['QUERY_STRING']);
						}
					}
				}
			}
		}
	}
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<meta http-equiv="X-UA-Compatible" content="IE=Edge; chrome=1" />
<title>Rewardy</title>

<meta name="title" content="Rewardy">
<meta name="description" content="Rewardy 입니다.">
<meta name="keywords" content="비즈폼, 스마트, SMART, Rewardy, 업무, 오늘업무, Rewardy, 챌린지, 보상, live, 업무관리">

<meta property="og:description" content="Rewardy 입니다.">
<meta property="og:title" content="Rewardy">
<meta property="og:image" content="/images/main/img_meta.jpg">


<link rel="shortcut icon" href="/favicon.ico">

<!--[if lt IE 9]>
<script src="https://www.bizforms.co.kr/js/html5.js"></script>
<script src="https://www.bizforms.co.kr/js/imgSizer.js"></script>
<script src="https://www.bizforms.co.kr/js/html5shiv.js"></script>
<script src="https://www.bizforms.co.kr/js/respond.min.js"></script>
<![endif]-->

<!-- 노토산스 -->
<link href="/css/style_font_notosans.css" rel="stylesheet" />
<!-- <link rel="stylesheet" type="text/css" href="/css/timepicki.css" />
<link rel="stylesheet" type="text/css" href="/css/air_datepicker.css" /> -->
<!-- <link rel="stylesheet" type="text/css" href="/css/tui-time-picker.css" />
<link rel="stylesheet" type="text/css" href="/css/tui-date-picker.css" /> -->
<link rel="stylesheet" type="text/css" href="/html/css/window-date-picker.css<?php echo VER;?>" />
<link rel="stylesheet" type="text/css" href="/html/css/common.css<?php echo VER;?>" />
<link rel="stylesheet" type="text/css" href="/html/css/mainy.css<?php echo VER;?>" />
<link rel="stylesheet" type="text/css" href="/html/css/all.min.css<?php echo VER;?>" />
<link rel="stylesheet" type="text/css" href="/html/css/slick.css<?php echo VER;?>" />
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script src="https://cdn.jsdelivr.net/clipboard.js/1.5.3/clipboard.min.js"></script>
<script src="/js/clipboard.min.js"></script>
<!-- <script src="/js/timepicki.js"></script>
<script src="/js/air_datepicker.js"></script>  -->
<!-- <script src="/js/tui-time-picker.js"></script>
<script src="/js/tui-date-picker.js"></script>  -->


<script src="/js/Sortable.min.js"></script>
<script src="/js/spectrum.js"></script>
<link rel="stylesheet" type="text/css" href="/css/spectrum.css" />


<script src="/js/window-date-picker.js"></script>
<script type="text/javascript" src="/js/slick.js"></script>
<script type="text/javascript" src="/js/circle-progress.js"></script>
<script type="text/javascript" src="/js/jquery.counterup.min.js"></script>
<script type="text/javascript" src="/js/jquery.ui.touch-punch.js"></script>
<script src="/js/waypoints.min.js"></script>
<script src="/js/jquery.touchFlow.js"></script>

<link href="/css/datepicker.css<?php echo VER;?>" rel="stylesheet" type="text/css">
<script src="/js/datepicker.js<?php echo VER;?>"></script>
<script src="/js/datepicker.kr.js<?php echo VER;?>"></script>

<!-- 0608 추가 -->
<script type="text/javascript" src="/js/re_common.js"></script>
<script src="/js/common.js<?php echo VER;?>"></script>
<script src="/js/jquery.fileDownload.js<?php echo VER;?>"></script>
<script src="/js/jquery.mousewheel.min.js"></script>
<script  src="/js/alarm_common.js<?php echo VER;?>"></script>

<?
//라이브 페이지 5분마다 새로고침
/*if ( basename(__FILE__) == "index.php" && get_dirname() == $reward_type['1']){?>
	<meta http-equiv="refresh" content="300">
<?}*/?>
</head>
<body>

<input type="text" name="user_name" autocomplete="false" required="" style="display:none;">
<input style="display:none" aria-hidden="true">
<input type="password" style="display:none" aria-hidden="true">

<script type="text/javascript">
	$(document).ready(function(){


	<?if($_COOKIE['onoff']=='1'){?>
		//$(".rew_box").removeClass("on");
	<?}?>

	<?
	//메인페이지에서만 circleProgress 동작하도록 스크립트 제한
	if ($_SERVER["PHP_SELF"] == "/index.php") { ?>

	<?}?>

		$(".input_main").keyup(function(){
			var input_length = $(this).val().length; //입력한 값의 글자수
			if(input_length>0){
				$(".btn_grid_02").addClass("on");
			}else{
				$(".btn_grid_02").removeClass("on");
			}
		});

		$(".btn_grid_02").click(function(){
			if($(".btn_grid_02").hasClass("on")){
				$(".rew_grid_list_none").hide();
				var textspan = $(".input_main").val();
				var text01 = $(".rew_grid_list_in ul li.rew_grid_list_01 span").text();
				var text02 = $(".rew_grid_list_in ul li.rew_grid_list_02 span").text();
				var text03 = $(".rew_grid_list_in ul li.rew_grid_list_03 span").text();
				$(".rew_grid_list_in ul li.rew_grid_list_01 span").text(textspan);
				$(".rew_grid_list_in ul li.rew_grid_list_02 span").text(text01);
				$(".rew_grid_list_in ul li.rew_grid_list_03 span").text(text02);
				//$(".rew_grid_list_in ul").prepend("<li class='ui-sortable-handle'><button></button><div><span>"+textspan+"</span></div></li>");
				//$(".rew_grid_list_in ul li:eq(3)").remove();
			}
			//var textspan = $(".input_main").value();
			//$(".rew_grid_list_in ul").prepend("<li class='ui-sortable-handle'><button></button><div><span>"+textspan+"</span></div></li>");

			if($(".rew_grid_list_in ul li.rew_grid_list_01 span").is(':empty')){

			}else{
				$(".rew_grid_list_in ul li.rew_grid_list_01").addClass("view");
			}
			if($(".rew_grid_list_in ul li.rew_grid_list_02 span").is(':empty')){

			}else{
				$(".rew_grid_list_in ul li.rew_grid_list_02").addClass("view");
			}
			if($(".rew_grid_list_in ul li.rew_grid_list_03 span").is(':empty')){

			}else{
				$(".rew_grid_list_in ul li.rew_grid_list_03").addClass("view");
			}
		});

		$(".rew_grid_list_in ul li button").click(function(){
			$(this).parent("li").toggleClass("on");
		});



		//if (GetCookie("user_id") == null){
		//	if ($(".rew_layer_login").is(":visible") == false){
		//		$(".rew_bar_li_01").trigger("click");
		//	}
		//}


	});



	<?if(ATTEND_STIME){?>
		var late_stime = "<?=ATTEND_STIME?>";
	<?}?>

	<?if(ATTEND_ETIME){?>
		var late_etime = "<?=ATTEND_ETIME?>";
	<?}?>

	$(window).scroll(function(){

	});
</script>

<?

	//if (@in_array(trim(get_dirname()), array("challenge","team","inc"))){
		//챌린지카테고리
		$sql = "select idx, name from work_category where state='0' order by rank asc";
		$cate_info = selectAllQuery($sql);
		for($i=0; $i<count($cate_info['idx']); $i++){
			$chall_category[$cate_info['idx'][$i]] = $cate_info['name'][$i];
		}
	//}

	//로그인상태
	if($user_id){  

		//회원등급(관리:1, 일반:5) : manager:1, normal:5
		//$grade_arr['normal']
		if($companyno){
			$where_challenge = " and companyno='".$companyno."'";
			$company_info = company_info();
			$company_comcoin = company_comcoin_total();
		}

		//권한설정으로
		$sql = "select idx, highlevel from work_member where state='0' and companyno='".$companyno."' and email='".$user_id."'";
		$mb_info = selectQuery($sql);
		if($mb_info['highlevel'] != $user_level){
			$user_level = $mb_info['highlevel'];
			if (is_numeric($mb_info['highlevel']) == true){
				setcookie('user_level', $mb_info['highlevel'] , $cookie_limit_time , '/', C_DOMAIN);
			}
		}
	}else{
	}
?>
