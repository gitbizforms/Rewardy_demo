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

$tuto_close = $_COOKIE['tuto_close'];
if(!$tuto_close){
	if(!$tuto_start){?>
		<?if($t_flag_info['t_flag'] != 6){?>
			<div class="tuto_link">
				<div class="tuto_link_in">
					<button class="btn_tuto_link_close">닫기</button>
					<a href="#" class="tuto_link_area"  onclick="tutorial_insert();">
						<p>튜토리얼을 시작하고, <br />코인으로 보상도 받아 가세요!</p>
					</a>
					<div class="close_check">
						<input type="checkbox" id="close_che">
						<label for="close_che">오늘 하루 안보기</label>
					</div>
				</div>
			</div>
		<?}?>
	<?}
}?>