<?
//header페이지
$home_dir = str_replace( basename(__DIR__) , "" , __DIR__ );
include $home_dir . "../inc_lude/header_about.php";

$sql = "select count(idx) as cnt from bro_manual where state = '1'";
$history_info_cnt = selectQuery($sql);
if($history_info_cnt['cnt']){ 
	$total_count = $history_info_cnt['cnt'];
}

$code = "all";
	
$url = "manual_list";
$string = "&page=".$url;
		//페이지 끝번호


$sql = "select idx, email, title, regdate, state, editdate, (select name from work_member where email = bro_manual.email and state = '0') as name from bro_manual where state = '1' order by idx desc ";	
$query = selectAllQuery($sql);
?>
<div class="rb_main fp-notransition">
	<div class="rb_main_sub_img">
		<div class="notice_wrap">
			<div class="notice_top">
				<strong>사용자 매뉴얼</strong>
			</div>
			<input type="hidden" value="manual_list" id="bro_type">
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
								<a href="/about/customer/manual_view.php?idx=<?=$idx?>"><span><?=$query['title'][$i]?></span></a>
							</div>
							<div class="notice_date">
								<span><?=$query['editdate'][$i]?></span>
							</div>
						</div>
					</li>
					<?}?>
                    <? if(count($query['idx'])==0){?>
                        <li>등록된 매뉴얼이 없습니다.</li>
                    <?}?>
				</ul>
            </div>
		</div>
	</div>
<?
//footer 페이지
include $home_dir . "/inc_lude/footer_about.php";
?>

