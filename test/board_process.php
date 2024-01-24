<?php

include str_replace( basename(__DIR__) , "", __DIR__ ) ."inc_lude/conf.php";
include DBCON;
include FUNC;

$regdate = date("Y-m-d H:i:s");

if($_COOKIE){
	$user_id = $_COOKIE['user_id'];
	$user_name = $_COOKIE['user_name'];
}

// if(!$_POST["mode"]){
	// $mode = "board_save";
// }else{
$mode = $_POST["mode"];
// }
if($mode == "board_save"){
	$board_con = $_POST['contents'];
	$board_tit = $_POST['board_tit'];
	// $board_con = urlencode($board_con);

	$sql = "insert into work_board(id,name,title,contents) values(";
	$sql = $sql  ."'".$user_id."','".$user_name."','".$board_tit."','".$board_con."')";

	$con_save = insertIdxQuery($sql);

	echo "save";
	exit;

} else if($mode == "board_list") {

	//페이지
	$p = $_POST['p']?$_POST['p']:$_GET['p'];
	if (!$p){
		$p = 1;
	}

	$pagingsize = 5;					//페이징 사이즈
	$pagesize = 20;						//페이지 출력갯수
	$startnum = 0;						//페이지 시작번호
	$endnum = $p * $pagesize;			//페이지 끝번호

	//시작번호
	if ($p == 1){
		$startnum = 1;
	}else{
		$startnum = ($p - 1) * $pagesize + 1;
	}

	$pageurl = "/about/customer/notice.php";
	$string = "&page=".$pageurl."&sdate=".$sdate."&edate=".$edate."&nday=".$nday."&type=".$type;

	// 전체 카운터수
	$sql = "select count(*) as cnt from work_board where state = 0 and type_flag = 0";
	$board_count = selectQuery($sql);
	if($board_count['cnt']){
		$total_count = $board_count['cnt'];
	}

	$sql = "select * from";
	$sql = $sql .= " (select ROW_NUMBER() over(order by idx desc) as r_num,  idx, state, title, convert(varchar, regdate, 102) as regdate from work_board";
	$sql = $sql .= " work_board where state = 0 and type_flag = 0)";
	$sql = $sql .= " as a where r_num between ". $startnum ." and " .$endnum ."";
	$sql = $sql .= " order by idx desc";
	$notice_li = selectAllQuery($sql);
?>

	<?if($notice_li['idx']){?>

		<?for($i=0; $i<count($notice_li['idx']); $i++){

			$r_num = $notice_li['r_num'][$i];
			$idx = $notice_li['idx'][$i];
			$state = $notice_li['state'][$i];
			$title = $notice_li['title'][$i];
			$regdate = $notice_li['regdate'][$i];

		?>

			<li>
				<div class="notice_desc">
					<div class="notice_num">
						<span><?=$r_num?></span>
					</div>
					<div class="notice_title">
						<a href="/about/customer/notice_list.php"><span><?=$title?></span></a>
					</div>
					<div class="notice_date">
						<span><?=$regdate?></span>
					</div>
				</div>
			</li>

		<?}?>

	<?}?>|

	<div class="notice_paging_in">

		<?
			//페이징사이즈, 전체카운터, 페이지출력갯수
			echo pageing($pagingsize, $total_count, $pagesize, $string);
		?>

	</div>

<?
	exit;
}
?>
