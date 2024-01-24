<div class="layer_work" id="layer_work" style="display:none;">
	<div class="lw_deam"></div>
	<div class="lw_in" id="popup">
		<div class="lw_box">
			<div class="lw_box_in">
				<div class="lw_top">
					<strong>좋은아침입니다!</strong>
					<p id="layer_lw_time"><?=$goto_year?>년 <?=$goto_month?>월 <?=$goto_day?>일 <?=$get_time_text?> <?=$goto_gg?>:<?=$goto_ii?></p>
				</div>
				<div class="lw_btn">
					<button class="lw_off" id="lw_off"><span>취소</span></button>
					<button class="lw_on" id="lw_on"><span>출근하기</span></button>
				</div>
			</div>
			<div class="lw_today_off"><button id="hidePopup"><span></span>오늘 하루 안보기</button></div>
		</div>
	</div>
</div>	

<!-- <div class="tuto_start" style="display:none;">
	<div class="tuto_deam"></div>
	<div class="tuto_start_in">
		<div class="tuto_start_tit">
			<img src="/html/images/pre/img_tuto_tit.png" alt="시작해야 돈이 된다. 누군가는 쌓고 있다." />
			<p>튜토리얼을 시작하고 보상받자<em>!</em></p>
		</div>
		<div class="tuto_start_btn">
			<button id="tutorial_start"><span>튜토리얼 시작하기</span></button>
		</div>
	</div>
</div> -->


<?
$tuto_start = strstr($_SERVER['PHP_SELF'],'tu_');

// $tu_page = @in_array($_SERVER['PHP_SELF'],array('/myinfo/index.php'));
$dir_arr = ['alarm','admin','choco','myinfo'];
$tu_page = in_array($get_dirname, $dir_arr);

$requestUri = $_SERVER[ "REQUEST_URI" ];
$parts = explode('/', trim($requestUri, '/'));

$sql = "select idx, t_flag from work_member where state = '0' and companyno='".$companyno."' and email = '".$user_id."'";
$t_flag_info = selectQuery($sql);
?>
		
<!-- <div class="tuto_link">
	<div class="tuto_link_in">
		<button class="btn_tuto_link_close">닫기</button>
		<a href="#" class="tuto_link_area"  onclick="tutorial_insert();">
			<p>튜토리얼을 시작하고, <br />코인으로 보상도 받아 가세요!</p>
		</a>
	</div>
</div> -->

<script language="JavaScript">
/* FOR BIZ., COM. AND ENT. SERVICE. */
_TRK_CP = "/Rewardy"; /* 페이지 이름 지정 Contents Path */
</script>