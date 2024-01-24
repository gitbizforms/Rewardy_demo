<?
	$home_dir = str_replace( basename(__DIR__) , "" , __DIR__ );
	
	//연결된 도메인으로 분리
	include $home_dir . "inc_lude/conf_mysqli.php";
	include $home_dir . "inc/SHA256/KISA_SHA256.php";
	include DBCON_MYSQLI;
	include FUNC_MYSQLI;

	$type_flag = ($chkMobile)?1:0;	
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
<link href="/about/css/style_font_notosans.css" rel="stylesheet" />
<link rel="stylesheet" type="text/css" href="/about/css/window-date-picker.css<?php echo VER;?>" />
<link rel="stylesheet" type="text/css" href="/about/css/common.css<?php echo VER;?>" />
<link rel="stylesheet" type="text/css" href="/about/css/mainy.css<?php echo VER;?>" />
<link rel="stylesheet" type="text/css" href="/about/css/all.min.css<?php echo VER;?>" />
<link rel="stylesheet" type="text/css" href="/about/css/about.css<?php echo VER;?>" />
<link rel="stylesheet" type="text/css" href="/about/css/slick.css<?php echo VER;?>" />
<link rel="stylesheet" type="text/css" href="/html/css/mainy.css<?php echo VER;?>" />
<link rel="stylesheet" type="text/css" href="/about/css/jquery.fullPage.css" />
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script src="https://cdn.jsdelivr.net/clipboard.js/1.5.3/clipboard.min.js"></script>
<script src="/about/js/clipboard.min.js"></script>

<script type="text/javascript" src="/about/js/jquery.fullPage.js"></script>
<script type="text/javascript" src="/about/js/scrolloverflow.min.js"></script>
<script src="/about/js/window-date-picker.js"></script>
<script type="text/javascript" src="/about/js/slick.js"></script>
<script type="text/javascript" src="/about/js/circle-progress.js"></script>
<script type="text/javascript" src="/about/js/jquery.counterup.min.js"></script>
<script type="text/javascript" src="/about/js/jquery.ui.touch-punch.js"></script>
<script src="/about/js/waypoints.min.js"></script>

<link rel="stylesheet" type="text/css" href="/about/css/jquery.fullPage.css" />
<script type="text/javascript" src="/about/js/jquery.fullPage.js"></script>
<script type="text/javascript" src="/about/js/scrolloverflow.min.js"></script>

</head>
<body>

	<input style="display:none" aria-hidden="true">
	<input type="password" style="display:none" aria-hidden="true">
	<?
	//js파일과 php파일 이름 같아서 파일이름만 따오기
	$file = basename($_SERVER['PHP_SELF']);
	$file_nm = basename($file,strchr($file,'.'));
	?>

<script type="text/javascript" src="/about/js/inc/<?echo $file_nm;?>.js"></script>
<script src="/js/about_common.js<?php echo VER;?>"></script>
<script>
	$(document).ready(function(){
		$(document).on('click', '.price_btn', function(){
			$('.rew_layer_pay_01').show();
		});
	});
</script>
<style type="text/css">
	html, body{background:none; height:100%;}
</style>

<?

if($type_flag == '0'){
	// 결제 팝업(PC)
	include $home_dir . "/payment/bill_pay/bill_pay_pop.php";
}else if($type_flag == '1'){
	// 결제 팝업(MO)
	include $home_dir . "/payment/bill_pay_mo/bill_pay_mo_pop.php";
}

//상단 메뉴
include $home_dir . "inc_lude/menu_about.php";
?>
