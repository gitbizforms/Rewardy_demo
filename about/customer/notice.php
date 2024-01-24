<?
//header페이지
$home_dir = str_replace( basename(__DIR__) , "" , __DIR__ );
include $home_dir . "../inc_lude/header_about.php";

$sql = "select count(idx) as cnt from bro_notice where state = '1'";
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

$sql = "select idx, email, title, regdate, state, editdate, (select name from work_member where email = bro_notice.email and state = '0') as name from bro_notice where state = '1' order by idx desc limit ".$startnum.",".$pagesize;	
$query = selectAllQuery($sql);
?>
<div class="rb_main fp-notransition">
	<div class="rb_main_sub_img">
		<div class="notice_wrap">
			<div class="notice_top">
				<strong>공지사항</strong>
			</div>
			<input type="hidden" value="notice_list" id="bro_type">
			<div class="notice_mid">
				<div class="notice_header">
					<div class="notice_num">
						<span>번호</span>
					</div>
					<div class="notice_title">
						<span>제목</span>
					</div>
					<div class="notice_date">
						<span>등록일</span>
					</div>
				</div>
				<ul class="notice_list">
					<? for($i=0;$i<count($query['idx']);$i++){
						$idx = $query['idx'][$i] ?>
					<li class="notice_<?=$idx?>">
						<div class="notice_desc">
							<div class="notice_num">
								<span><?=$i+1?></span>
							</div>
							<div class="notice_title">
								<a href="/about/customer/notice_list.php?idx=<?=$idx?>"><span><?=$query['title'][$i]?></span></a>
							</div>
							<div class="notice_date">
								<span><?=$query['editdate'][$i]?></span>
							</div>
						</div>
					</li>
					<?}?>
				</ul>
			</div>
			<div class="notice_bottom">
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

