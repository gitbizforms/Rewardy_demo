<?
//header페이지
$home_dir = str_replace( basename(__DIR__) , "" , __DIR__ );
include $home_dir . "../inc_lude/header_about.php";

$sql = "select count(idx) as cnt from bro_faq where state = '1'";
$history_info_cnt = selectQuery($sql);
if($history_info_cnt['cnt']){ 
	$total_count = $history_info_cnt['cnt'];
}

$code = "all";
	
$url = "notice_list";
$string = "&page=".$url;

$p = $_POST['p']?$_POST['p']:$_GET['p'];
if (!$p){
	$p = 1;
}

$pagingsize = 5;					//페이징 사이즈
$pagesize = 5;						//페이지 출력갯수
$startnum = 0;						//페이지 시작번호
$endnum = $p * $pagesize;			//페이지 끝번호

//시작번호
if ($p == 1){
	$startnum = 0;
}else{
$startnum = ($p - 1) * $pagesize;
}

$sql = "select idx, email, title, contents, regdate, state, editdate, (select name from work_member where email = bro_faq.email and state = '0') as name from bro_faq where state = '1' order by idx desc limit ".$startnum.",".$pagesize;
$query = selectAllQuery($sql);
?>
<div class="rb_main fp-notransition">
	<div class="rb_main_sub_img">
		<div class="faq_wrap">
			<div class="faq_top">
				<input type="hidden" value="faq_list" id="bro_type">
				<strong>자주 묻는 질문</strong>
				<div class="faq_search">
					<div class="faq_search_box">
						<input type="text" class="input_search" placeholder="질문을 입력하세요." />
						<button><span>검색</span></button>
					</div>
				</div>
			</div>

			<div class="faq_mid">
				<ul class="faq_list">
					<? for($i=0; $i<count($query['idx']); $i++){?>
					<li>
						<div class="faq_q">
							<div class="faq_q_txt">
								<span><?=$query['title'][$i]?></span>
							</div>
						</div>
						<div class="faq_a">
							<div class="faq_a_txt">
								<?=$query['contents'][$i]?>
							</div>
						</div>
					</li>
					<?}?>
				</ul>
			</div>

			<div class="faq_bottom">
				<div id="back_pagelist">
					<?php echo back_pageing($pagingsize, $total_count, $pagesize, $string)?>
				</div>
			</div>
		</div>
	</div>
<?
//footer 페이지
include $home_dir . "/inc_lude/footer_about.php";
?>
