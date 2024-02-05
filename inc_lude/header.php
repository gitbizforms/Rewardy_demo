<?
	$home_dir = str_replace( basename(__DIR__) , "" , __DIR__ );

	//윈도우서버용 php-mssql 사용, 도메인 : https://rewardy.co.kr

	//리눅스 환경 변수 : /inc_lude/conf_mysqli.php
	//리눅스 환경 함수 : /inc_lude/func_mysqli.php

	include $home_dir . "inc_lude/conf_mysqli.php";
	include DBCON_MYSQLI;
	include FUNC_MYSQLI;

	//디렉토리 추출
	$get_dirname = str_replace(NAS_HOME_DIR,"", get_dirname());

	//쿼리스트링은 메일으로 통해서 가입하는 사용자 경우
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
							header("Location:https://".$_SERVER['HTTP_HOST']."/team/?".$_SERVER['QUERY_STRING']);
						}
					}
				}
			}
		}else{
			//챌린지 idx, 파티 idx경우 메인으로
			if(strstr( $_SERVER['QUERY_STRING'] , "idx" )){
				parse_str($_SERVER['QUERY_STRING'], $output);
				if($output['idx']){
					header("Location:https://".$_SERVER['HTTP_HOST']."/");
				}
			}
		}
	}else{
		if(!$user_id && $_SERVER['PHP_SELF']!='/index.php'){
			header("Location:http://demo.rewardy.co.kr/about/index.php");
			exit;
		
		}
	}

?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="initial-scale=1.0,user-scalable=no,maximum-scale=1,width=device-width" />
<meta http-equiv="X-UA-Compatible" content="IE=Edge; chrome=1" />
<title>Rewardy</title>

<meta name="title" content="Rewardy">
<meta name="description" content="Rewardy 입니다.">
<meta name="keywords" content="비즈폼, 스마트, SMART, Rewardy, 업무, 오늘업무, Rewardy, 챌린지, 보상, live, 업무관리">

<meta property="og:description" content="Rewardy 입니다.">
<meta property="og:title" content="Rewardy">
<meta property="og:image" content="/images/main/img_meta.jpg">

<link rel="shortcut icon" href="/favicon.ico">

<!-- 노토산스 -->
<link href="https://cdn.jsdelivr.net/npm/noto-sans-kr-font@0.0.6/noto-sans-kr.min.css" rel="stylesheet">
<link rel="stylesheet" type="text/css" href="/html/css/window-date-picker.css<?php echo VER;?>" />
<link rel="stylesheet" type="text/css" href="/html/css/common.css<?php echo VER;?>" />
<link rel="stylesheet" type="text/css" href="/html/css/mainy.css<?php echo VER;?>" />
<link rel="stylesheet" type="text/css" href="/html/css/logo.css<?php echo VER;?>" />
<link rel="stylesheet" type="text/css" href="/html/css/demo_start.css<?php echo VER;?>" />

<link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css<?php echo VER;?>" />
<!-- slick -> work_process -->
<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.css<?php echo VER;?>"/>
<!-- Google tag (gtag.js) -->
<script async src="https://www.googletagmanager.com/gtag/js?id=G-K2JZRSPQSF"></script>
<script>
window.dataLayer = window.dataLayer || [];
function gtag(){dataLayer.push(arguments);}
gtag('js', new Date());

gtag('config', 'G-K2JZRSPQSF');
</script>	
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script src="https://cdn.jsdelivr.net/clipboard.js/1.5.3/clipboard.min.js"></script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/Sortable/1.15.0/Sortable.min.js"></script>
<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/spectrum-colorpicker2/dist/spectrum.min.css">
<script src="https://cdn.jsdelivr.net/npm/spectrum-colorpicker2/dist/spectrum.min.js"></script>

<script src="https://cdn.jsdelivr.net/npm/window-date-picker@1.0.1/dist/js/window-date-picker.min.js"></script>
<!-- slick -> work_process -->
<!-- <script src="/js/slick.js"></script> -->
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.min.js"></script>
<!--  circle-progess -> team/live -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-circle-progress/1.2.2/circle-progress.min.js"></script>
<!-- counterup -> Team / index-->
<script src="https://cdnjs.cloudflare.com/ajax/libs/Counter-Up/1.0.0/jquery.counterup.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui-touch-punch/0.2.3/jquery.ui.touch-punch.min.js"></script>
<!-- counterup -> Team / index-->
<script src="https://cdnjs.cloudflare.com/ajax/libs/waypoints/4.0.1/noframework.waypoints.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/jquery.touchflow@1.6.7/jquery.touchFlow.min.js"></script>

<link href="/css/datepicker.css<?php echo VER;?>" rel="stylesheet" type="text/css">
<script src="/js/datepicker.js<?php echo VER;?>"></script>
<script src="/js/datepicker.kr.js<?php echo VER;?>"></script>
<script src="https://cdn.jsdelivr.net/bxslider/4.2.12/jquery.bxslider.min.js"></script>
<!-- 0608 추가 -->
<script type="text/javascript" src="/js/re_common.js"></script>
<script type="text/javascript" src="/js/demo_login.js"></script>

<?
if ($get_dirname == $reward_type['0']){?>
<script src="/js/works_common.js<?php echo VER;?>"></script>
<script src="/js/tutorial_common.js<?php echo VER;?>"></script>
<?//라이브
}else if ($get_dirname == $reward_type['1']){?>
<script src="/js/lives_common.js<?php echo VER;?>"></script>

<?//챌린지
}else if($get_dirname == $reward_type['2']){?>
<script src="/js/challenges_common.js<?php echo VER;?>"></script>
	
<?//메인페이지
}else if($get_dirname == $reward_type['3']){?>
<script src="/js/team_common.js<?php echo VER;?>"></script>

<?//보상
}else if($get_dirname == $reward_type['4']){?>
<script src="/js/reward_common.js<?php echo VER;?>"></script>

<?//맴버관리
}else if($get_dirname == $reward_type['5']){?>
<script src="/js/member_common.js<?php echo VER;?>"></script>

<? //파티관리
}else if($get_dirname == $reward_type['9']){?>
<script src="/js/project_common.js<?php echo VER;?>"></script>

<?
//insight
}else if($get_dirname == $reward_type['10']){?>
<script src="/js/insight_common.js<?php echo VER;?>"></script>

<?
//alarm
}else if($get_dirname == $reward_type['11']){?>
<script src="/js/member_common.js<?php echo VER;?>"></script>

<?
// itemshop
}else if($get_dirname == $reward_type['12']){?>
<script src="/js/item_common.js<?php echo VER;?>"></script>

<?} else if($get_dirname == $reward_type['13']){?>
	<script src="/js/common.js<?php echo VER;?>"></script>
	<script src="/js/backoffice_common.js<?php echo VER;?>"></script>
<?}

if($get_dirname != $reward_type['13']){?>
	<script src="/js/common.js<?php echo VER;?>"></script>
<?}?>


<script src="/js/jquery.fileDownload.js<?php echo VER;?>"></script>
<script src="/js/jquery.mousewheel.min.js"></script>

</head>
<body>

<input type="text" name="user_name" autocomplete="false" required="" style="display:none;">
<input style="display:none" aria-hidden="true">
<input type="password" style="display:none" aria-hidden="true">


		<?
//회원 전체 정보가져오기
$member_info = member_list_all();
$member_total_cnt = $member_info['total_cnt'];

//부서별 정렬순
$part_info = member_part_info();
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


		$sql = "select idx, t_flag, comcoin from work_member where state = '0' and companyno='".$companyno."' and email = '".$user_id."'";
		$t_flag_info = selectQuery($sql);
		
		//지급할 수 있는
		$common_coin = $t_flag_info['comcoin'];

	}

	$sql = "select idx,code,coin,icon,memo from work_coin_reward_info where state='0' and kind='live' order by idx asc";
	$coin_reward_info = selectAllQuery($sql);


	
?>

