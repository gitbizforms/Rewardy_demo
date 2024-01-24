<?	//전체 사용되는 인클루드
	$home_dir = str_replace( basename(__DIR__) , "" , __DIR__ );
	include $home_dir . "inc_lude/conf.php";
	include DBCON;
	include FUNC;


	/*
	$pagename = basename($_SERVER['PHP_SELF']);
	$page_arr = explode(".", $pagename);
	echo $page_arr[0];
	echo $page_arr[1];
	*/

	$filename = basename($_SERVER['PHP_SELF']); 
	$file_extension = substr($filename, 0, strrpos($filename, ".")); 
	$file_extension = str_replace( array("list_", "write_"), "", $file_extension);

	if( $filename =='list_01.php' || $filename =='list_02.php' || $filename =='list_03.php' || $filename =='write_03.php'){
		$file_extension = "_".$file_extension;
	}else{
		$file_extension = "";
	}

?>
<!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<meta http-equiv="X-UA-Compatible" content="IE=Edge; chrome=1" />
<title>오늘일</title>

<meta name="title" content="오늘일">
<meta name="description" content="오늘일">
<meta name="keywords" content="비즈폼, 스마트, SMART, 오늘일">

<meta property="og:description" content="오늘일">
<meta property="og:title" content="오늘일">
<meta property="og:image" content="/images/main/img_meta.jpg">


<!--[if lt IE 9]>
<script src="https://www.bizforms.co.kr/js/html5.js"></script>
<script src="https://www.bizforms.co.kr/js/imgSizer.js"></script>
<script src="https://www.bizforms.co.kr/js/html5shiv.js"></script>
<script src="https://www.bizforms.co.kr/js/respond.min.js"></script>
<![endif]-->

<!-- 노토산스 -->
<link href="https://www.bizforms.co.kr/magazine/content/hotclick/css/style_font_notosans.css" rel="stylesheet" />
<link rel="stylesheet" type="text/css" href="/css/common.css<?php echo VER;?>" />
<link rel="stylesheet" type="text/css" href="/css/main_challenges.css<?php echo VER;?>" />
<link rel="stylesheet" type="text/css" href="/css/all.min.css<?php echo VER;?>" />
<link rel="stylesheet" type="text/css" href="/css/datepicker.css<?php echo VER;?>">

<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
<!--<script src="https://cdn.jsdelivr.net/clipboard.js/1.5.3/clipboard.min.js"></script>-->
<!--<script src="https://www.bizforms.co.kr/magazine/content/hotclick/js/clipboard.min.js"></script> -->


<script type="text/javascript" src="/js/datepicker.js<?php echo VER;?>"></script>
<script type="text/javascript" src="/js/datepicker.kr.js<?php echo VER;?>"></script>
<script src="/js/common<? echo $file_extension?>.js?v=<?echo date("YmdHis",time());?>"></script> 

</head>
<body>
<script type="text/javascript">

	$(document).ready(function(){

		$("#login_btn").click(function(){
			$(".t_layer").show();
		});

		$(".tl_close, .tl_deam").click(function(){
			$(".t_layer").hide();
		});

		$(".label_01").click(function(){
			$(this).parent(".tc_input").addClass("now_focus");
		});

		$(".input_01").focusin(function(){
			$(this).parent(".tc_input").addClass("now_focus");
		});

		$(".input_01").focusout(function(){
			var this_val = $(this).val();
			if(this_val==0){
				$(this).parent(".tc_input").removeClass("now_focus");
			}else{
				//$(this).parent(".tc_input").addClass("now_ok");
				//$(this).parent(".tc_input").addClass("now_no");
			}
		});

	});

	$(window).scroll(function(){
		
	});
</script>