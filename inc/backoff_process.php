<?php

$home_dir = str_replace( basename(__DIR__) , "", __DIR__ );
// if($_SERVER['HTTP_HOST'] == "officeworker.co.kr"){

include $home_dir . "inc_lude/conf_mysqli.php";
include $home_dir . "inc/SHA256/KISA_SHA256.php";
include DBCON_MYSQLI;
include FUNC_MYSQLI;

// }else{
// 	include $home_dir . "inc_lude/conf.php";
// 	include DBCON;
// 	include FUNC;
// }

//실서버
//include_once(__DIR__."\\PHPMailer\\PHPMailerAutoload.php");
include_once($home_dir."/PHPMailer/libphp-phpmailer/PHPMailerAutoload.php");

$mode = $_POST["mode"];					//mode값 전달받음
$type_flag = ($chkMobile)?1:0;				//구분(0:사이트, 1:모바일)

if($_COOKIE){
	$user_id = $_COOKIE['user_id'];
	$user_name = $_COOKIE['user_name'];
	$user_level = $_COOKIE['user_level'];
	$user_part = $_COOKIE['user_part'];
	// $companyno = $_COOKIE['companyno'];
}

//오늘날짜
$today = TODATE;

//전월날짜
$timestamp = strtotime("-1 weeks");
$last_week = date("Y-m-d", $timestamp);

if($mode == "backcoin_list"){ 
	$sdate = $_POST['sdate'];
	$edate = $_POST['edate'];

	$user = $_POST['user'];

	$where = "";
	if($sdate && $edate){
		$where = $where .= " and (wc.workdate >= '".$sdate."' and wc.workdate <= '".$edate."')";
	}

	$search = $_POST['search'];
	if($search){
		if($user == 'send_user'){
			$where = $where .= " and (wc.reward_name like '%".$search."%' or wc.reward_user like '%".$search."%')";
		}else if($user == 'reward_user'){
			$where = $where .= " and (wc.email like '%".$search."%' or wc.name like '%".$search."%')";
		}else{
			$where = $where .= " and (wc.email like '%".$search."%' or wc.name like '%".$search."%' or wm.company like '%".$search."%' or wc.reward_name like '%".$search."%' or wc.reward_user like '%".$search."%')";
		}
	}

	$code = $_POST['code'];
	if($code=="all"){
		$where = $where .= " and wc.code in (500,600,700,900,1000,1100)";
	}else{
		$where = $where .= " and wc.code in (".$code.")";
	}

	$kind = $_POST['kind'];
	$tclass = $_POST['tclass'];

	if($kind){
		$sort = " wc.".$kind;
	}else if($kind == "company"){
		$sort = " wm.company";
	}else{
		$sort = " wc.idx";
	}

	if($tclass=="btn_sort_up"){
		$updown = " asc";
	}else{
		$updown = " desc";
	}

	$url = "backcoin_list";
	$string = "&page=".$url;

	$p = $_POST['p']?$_POST['p']:$_GET['p'];
	if (!$p){
		$p = 1;
	}

	$list = $_POST['list'];
	$pagingsize = 5;					//페이징 사이즈
	if($list){
		$pagesize = $list;	
	}else{
		$pagesize = 15;
	}
	//페이지 출력갯수
	$startnum = 0;						//페이지 시작번호
	$endnum = $p * $pagesize;			//페이지 끝번호

	//시작번호
	if ($p == 1){
		$startnum = 0;
	}else{
		$startnum = ($p - 1) * $pagesize;
	}

	$sql = "select wc.idx, wc.regdate, wc.reward_user, wc.email, wc.name, wc.reward_name, wc.coin, wc.code, wc.memo, wm.company from work_coininfo as wc, work_member as wm where wc.email = wm.email and wc.state = '0' and wm.state = '0' ".$where; 
	$sql = $sql .= " order by ".$sort.$updown." limit ". $startnum.",".$pagesize;
	$query = selectAllQuery($sql);

	$sql = "select count(wc.idx) as cnt from work_coininfo as wc, work_member as wm where wc.email = wm.email and wc.state = '0' and wm.state = '0' and wc.code in (500,600,700,900,1000,1100) ".$where ;
	$history_info_cnt = selectQuery($sql);
	if($history_info_cnt['cnt']){ 
		$total_count = $history_info_cnt['cnt'];
	}


	if($_POST['reset']=="re"){
		$sql = "select wc.idx, wc.regdate, wc.reward_user, wc.email, wc.name, wc.reward_name, wc.coin, wc.code, wc.memo, wm.company from work_coininfo as wc, work_member as wm where wc.email = wm.email and wc.state = '0' and wc.code in (500,600,700,900,1000,1100) and (wc.workdate >= '".$last_week."' and wc.workdate <= '".$today."') order by wc.idx desc limit 0,15";
		$query = selectAllQuery($sql);

		$where = "";
		$sql = "select count(*) as cnt from work_coininfo where state = '0' and code in (500,600,700,900,1000,1100) and (workdate >= '".$last_week."' and workdate <= '".$today."') ";
		$history_info_cnt = selectQuery($sql);
		if($history_info_cnt['cnt']){ 
			$total_count = $history_info_cnt['cnt'];
		}

		$sdate = $last_week;
		$edate = $today;
	}
		
		for($i=0; $i<count($query['idx']); $i++){
			$mem_idx = $query['idx'][$i];
			$coinkind = $query['code'][$i];
			$code_arr = ['500','600','700','900','1000','1100'];
			$title_arr = ['챌린지 참여 보상','공용코인 지급','리워드 개인보상','월별 역량 보상','월별 좋아요 보상','파티코인 분배'];
			for($j=0;$j<count($code_arr);$j++){
				if($coinkind == $code_arr[$j]){
					$coin_kind = $title_arr[$j];
				}
			}
			$reward_name = $query['reward_name'][$i];
			$reward_user = $query['reward_user'][$i];
			if($reward_name == ""){
				$reward_name = "리워디";
				$reward_user = "rewardy";
			}
			?>
			<tr>
				<td><?=$query['regdate'][$i]?></td>
				<td><?=$reward_name."(".$reward_user.")"?></td>
				<td><?=$query['name'][$i]."(".$query['email'][$i].")"?></td>
				<td><?=number_format($query['coin'][$i])?></td>
				<td><?=$coin_kind?></td>
				<td class="coin_memo"><p><?=$query['memo'][$i]?></p></td>
				<td><?=$query['company'][$i]?></td>
			</tr>
		<?}
			if(count($query['idx'])==0){?>
				<tr><td colspan="6">조회된 목록이 없습니다.</td></tr>
			<?}
		?>
		<input type="hidden" value="<?=$p?>" id="page_num" >
		<input type="hidden" value="<?=$pagesize?>" id="list_cnt">
		<input type="hidden" value="<?=$search?>" id="backoff_search">
		<input type="hidden" value="<?=$sdate?>" id="backoff_sdate">
		<input type="hidden" value="<?=$edate?>" id="backoff_edate">
		<input type="hidden" value="backcoin_list" id="backoffice_type">
		<input type="hidden" value="<?=$tclass?>" id="tclass">
		<input type="hidden" value="<?=$kind?>" id="kind">
		<input type="hidden" value="<?=$user?>" id="user_kind">
		<input type="hidden" value="<?=$code?>" id="code">|
		<?php echo back_pageing($pagingsize, $total_count, $pagesize, $string)?>
		<?echo "|".$tclass."|backcoin|";
		if($_POST['reset']=="re"){
			echo $last_week."|".$today;
		}
	}

//백오피스 좋아요 리스트
if($mode == "backlike_list"){
	
	$sdate = $_POST['sdate'];
	$edate = $_POST['edate'];

	$where = "";
	if($sdate && $edate){
		$where = $where .= " and (wc.workdate >= '".$sdate."' and wc.workdate <= '".$edate."')";
	}

	$search = $_POST['search'];
	if($search){
		$where = $where .= " and (wc.name like '%".$search."%' or wm.company like '%".$search."%' or wc.send_email like '%".$search."%' or wc.email like '%".$search."%' or wc.send_name like '%".$search."%')";
	}

	$code = $_POST['code'];
	if($code == "all"){
		$where = $where .= "";
	}else{
		$where = $where .= " and service = '".$code."' ";
	}


	$kind = $_POST['kind'];
	$tclass = $_POST['tclass'];

	if($kind){
		$sort = " wc.".$kind;
	}else if($kind == "company"){
		$sort = " wm.company";
	}else{
		$sort = " wc.idx";
	}

	if($tclass=="btn_sort_up"){
		$updown = " asc";
	}else{
		$updown = " desc";
	}

	$url = "backlike_list";
	$string = "&page=".$url;

	$p = $_POST['p']?$_POST['p']:$_GET['p'];
	if (!$p){
		$p = 1;
	}

	$list = $_POST['list'];
	$pagingsize = 5;					//페이징 사이즈
	if($list){
		$pagesize = $list;	
	}else{
		$pagesize = 15;
	}						//페이지 출력갯수
	$startnum = 0;						//페이지 시작번호
	$endnum = $p * $pagesize;			//페이지 끝번호

	//시작번호
	if ($p == 1){
		$startnum = 0;
	}else{
		$startnum = ($p - 1) * $pagesize;
	}

	$sql = "select wc.idx, wc.regdate, wc.send_name, wc.send_email, wc.name, wc.email, wc.service, wc.comment, wm.company from work_todaywork_like as wc, work_member as wm where wc.email = wm.email and wc.state = '0' and wm.state = '0' ".$where ; 
	$sql = $sql .= " order by ".$sort.$updown." limit ". $startnum.",".$pagesize;
	$query = selectAllQuery($sql);

	$sql = "select count(wc.idx) as cnt from work_todaywork_like as wc, work_member as wm where wc.email = wm.email and wc.state = '0' and wm.state = '0' ".$where ;
	$history_info_cnt = selectQuery($sql);
	if($history_info_cnt['cnt']){ 
		$total_count = $history_info_cnt['cnt'];
	}

	if($_POST['reset']=="re"){
		if($code == "all"){
			$codekind = "";
		}else{
			$codekind = "and service = '".$code."'";
		}
		$sql = "select wc.idx, wc.regdate, wc.send_name, wc.send_email, wc.name, wc.email, wc.service, wc.comment, wm.company from work_todaywork_like as wc, work_member as wm where wc.email = wm.email and wc.state = '0' and wm.state = '0' ".$codekind." and (wc.workdate >= '".$last_week."' and wc.workdate <= '".$today."')"; 
		$sql = $sql .= " order by idx desc limit 0,15";
		$query = selectAllQuery($sql);

		$sql = "select count(wc.idx) as cnt from work_todaywork_like as wc, work_member as wm where wc.email = wm.email and wc.state = '0' and wm.state = '0' and service = '".$codekind."' and (wc.workdate >= '".$last_week."' and wc.workdate <= '".$today."') ";
		$history_info_cnt = selectQuery($sql);
		if($history_info_cnt['cnt']){ 
			$total_count = $history_info_cnt['cnt'];
		}

		$sdate = $last_week;
		$edate = $today;
	}

	for($i=0; $i<count($query['idx']); $i++){
		$mem_idx = $query['idx'][$i];
		$likekind = $query['service'][$i];
		$code_arr = ['main','live','memo','party','work','challenge'];
		$title_arr = ['메인페이지','라이브','메모','파티','오늘업무','챌린지'];
		for($j=0;$j<count($code_arr);$j++){
			if($likekind == $code_arr[$j]){
				$like_kind = $title_arr[$j];
			}		
		}
		?>
		<tr>
			<td><?=$query['regdate'][$i]?></td>
			<td><?=$query['send_name'][$i]."(".$query['send_email'][$i].")"?></td>
			<td><?=$query['name'][$i]."(".$query['email'][$i].")"?></td>
			<td><?=$like_kind?></td>
			<td class="like_memo"><p><?=$query['comment'][$i]?></p></td>
			<td><?=$query['company'][$i]?></td>
		</tr>
	<?}
		if(count($query['idx'])==0){?>
			<tr><td colspan="6">조회된 목록이 없습니다.</td></tr>
		<?}
	?>
	<input type="hidden" id="page_num" value="<?=$p?>">
	<input type="hidden" value="<?=$pagesize?>" id="list_cnt" >
	<input type="hidden" value="<?=$search?>" id="backoff_search">
	<input type="hidden" value="<?=$sdate?>" id="backoff_sdate">
	<input type="hidden" value="<?=$edate?>" id="backoff_edate">
	<input type="hidden" value="backlike_list" id="backoffice_type">
	<input type="hidden" value="<?=$tclass?>" id="tclass">
	<input type="hidden" value="<?=$kind?>" id="kind">
	<input type="hidden" value="<?=$code?>" id="code">|
	<?php echo back_pageing($pagingsize, $total_count, $pagesize, $string)?>
	<?echo "|".$tclass."|backlike|";
	if($_POST['reset']=="re"){
		echo $last_week."|".$today;
	}
}

//백오피스 유저 리스트
if($mode == "backuser_list"){
	// $sdate = $_POST['sdate'];
	// $edate = $_POST['edate'];

	$where = "";
	// if($sdate && $edate){
	// 	$where = $where .= " and (workdate >= '".$sdate."' and workdate <= '".$edate."')";
	// }

	$search = $_POST['search'];
	if($search){
		$where = $where .= " and (name like '%".$search."%' or company like '%".$search."%' or email like '%".$search."%' )";
	}

	$code = $_POST['code'];
	if($code=='0'){
		$where = $where .= " and state = '0'";
	}else if($code == '1'){
		$where = $where .= " and state = '1'";
	}

	$kind = $_POST['kind'];
	$tclass = $_POST['tclass'];

	if($kind){
		$sort = $kind;
	}else{
		$sort = " idx";
	}

	if($tclass=="btn_sort_up"){
		$updown = " asc";
	}else{
		$updown = " desc";
	}

	$url = "backuser_list";
	$string = "&page=".$url;

	$p = $_POST['p']?$_POST['p']:$_GET['p'];
	if (!$p){
		$p = 1;
	}

	$list = $_POST['list'];
	$pagingsize = 5;					//페이징 사이즈
	if($list){
		$pagesize = $list;	
	}else{
		$pagesize = 15;
	}						//페이지 출력갯수
	$startnum = 0;						//페이지 시작번호
	$endnum = $p * $pagesize;			//페이지 끝번호

	//시작번호
	if ($p == 1){
		$startnum = 0;
	}else{
		$startnum = ($p - 1) * $pagesize;
	}

	$sql = "select idx,regdate, name, email, company, comcoin, coin, login_count, login_date from work_member where idx > 0 ".$where ; 
	$sql = $sql .= " order by ".$sort.$updown." limit ". $startnum.",".$pagesize;
	$query = selectAllQuery($sql);

	$sql = "select count(idx) as cnt from work_member where idx > 0 ".$where ;
	$history_info_cnt = selectQuery($sql);
	if($history_info_cnt['cnt']){ 
		$total_count = $history_info_cnt['cnt'];
	}

	for($i=0; $i<count($query['idx']); $i++){
		$mem_idx = $query['idx'][$i];
		$no = ($p-1)*$pagesize+($i+1);
		?>
		<tr>
			<td><?=$mem_idx?></td>
			<td><?=$query['name'][$i]?></td>
			<td class="user_td_email"><?=$query['email'][$i]?></td>
			<td><?=$query['company'][$i]?></td>
			<td><?=number_format($query['comcoin'][$i])?></td>
			<td><?=number_format($query['coin'][$i])?></td>
			<td><?=$query['login_count'][$i]?></td>
			<td><?=$query['login_date'][$i]?></td>
			<td><button type="button" class="btn btn-outline-dark btn-sm" id="reset_<?=$mem_idx?>" value="<?=$mem_idx?>"><i class="fa-solid fa-rotate-right"></i></button></td>
		</tr>
	<?}
		if(count($query['idx'])==0){?>
			<tr><td colspan="6">조회된 목록이 없습니다.</td></tr>
		<?}
	?>
	<input type="hidden" id="page_num" value="<?=$p?>">
	<input type="hidden" value="<?=$pagesize?>" id="list_cnt" >
	<input type="hidden" value="<?=$sear터ch?>" id="backoff_search">
	<input type="hidden" value="backuser_list" id="backoffice_type">
	<input type="hidden" value="<?=$tclass?>" id="tclass">
	<input type="hidden" value="<?=$kind?>" id="kind">
	<input type="hidden" value="<?=$code?>" id="code">|
		<?php echo back_pageing($pagingsize, $total_count, $pagesize, $string)?>
	<? echo "|".$tclass."|backuser|";
}

//백오피스 챌린지 리스트
if($mode == "backchall_list"){

	//전월날짜
	$timestamp = strtotime("-3 months");
	$last_week = date("Y-m-d", $timestamp);

	$sdate = $_POST['sdate'];
	$edate = $_POST['edate'];

	$where = "";
	if($sdate && $edate){
		$where = $where .= " and (wc.sdate >= '".$sdate."' and wc.edate <= '".$edate."')";
	}

	$search = $_POST['search'];
	if($search){
		$where = $where .= " and (wc.email like '%".$search."%' or wm.company like '%".$search."%' or wc.title like '%".$search."%')";
	}

	$kind = $_POST['kind'];
	$tclass = $_POST['tclass'];

	if($kind){
		$sort = " wc.".$kind;
	}else if($kind == "company"){
		$sort = " wm.company";
	}else{
		$sort = " wc.idx";
	}

	if($tclass=="btn_sort_up"){
		$updown = " asc";
	}else{
		$updown = " desc";
	}

	$url = "backchall_list";
	$string = "&page=".$url;

	$p = $_POST['p']?$_POST['p']:$_GET['p'];
	if (!$p){
		$p = 1;
	}

	$list = $_POST['list'];
	$pagingsize = 5;					//페이징 사이즈
	if($list){
		$pagesize = $list;	
	}else{
		$pagesize = 15;
	}					//페이지 출력갯수
	$startnum = 0;						//페이지 시작번호
	$endnum = $p * $pagesize;			//페이지 끝번호

	//시작번호
	if ($p == 1){
		$startnum = 0;
	}else{
		$startnum = ($p - 1) * $pagesize;
	}

	
	$sql = "select  wc.idx, wc.title, wc.regdate, wc.coin, wc.total_max_coin, wc.keyword, wc.email, wc.sdate, wc.edate, wc.pageview, (select count(idx) from work_challenges_user as cu where cu.challenges_idx = wc.idx) as ucnt, (select count(idx) from work_challenges_result as cr where cr.state = '1' and cr.challenges_idx = wc.idx ) as rcnt from work_challenges as wc where wc.state = '0' and wc.day_type = '0'".$where ; 
	$sql = $sql .= " order by ".$sort.$updown." limit ". $startnum.",".$pagesize;
	$query = selectAllQuery($sql);

	$sql = "select count(wc.idx) as cnt from work_challenges as wc, work_member as wm where wc.email = wm.email and wc.state = '0' and wm.state = '0' and wc.day_type = '0' ".$where;
	$history_info_cnt = selectQuery($sql);
	if($history_info_cnt['cnt']){ 
		$total_count = $history_info_cnt['cnt'];
	}

	
	if($_POST['reset']=="re"){
		if($code == "all"){
			$codekind = "";
		}else{
			$codekind = "and service = '".$code."'";
		}
		$sql = "select  wc.idx, wc.title, wc.regdate, wc.coin, wc.total_max_coin, wc.keyword, wc.email, wc.sdate, wc.edate, wc.pageview, (select count(idx) from work_challenges_user as cu where cu.challenges_idx = wc.idx) as ucnt, (select count(idx) from work_challenges_result as cr where cr.state = '1' and cr.challenges_idx = wc.idx ) as rcnt from work_challenges as wc where wc.state = '0' and wc.day_type = '0' and (wc.sdate >= '".$last_week."' and wc.edate <= '".$today."')"; 
		$sql = $sql .= " order by idx desc limit 0,15";
		$query = selectAllQuery($sql);

		$sql = "select count(wc.idx) as cnt from work_challenges as wc, work_member as wm where wc.email = wm.email and wc.state = '0' and wm.state = '0' and wc.day_type = '0'  and (wc.workdate >= '".$last_week."' and wc.workdate <= '".$today."') ";
		$history_info_cnt = selectQuery($sql);
		if($history_info_cnt['cnt']){ 
			$total_count = $history_info_cnt['cnt'];
		}

		$sdate = $last_week;
		$edate = $today;
	}
	

	for($i=0; $i<count($query['idx']); $i++){
		$sql = "select email, company from work_member where email = '".$query['email'][$i]."' and state = '0' ";
		$company = selectQuery($sql);
		$result_coin = $query['coin'][$i] * $query['rcnt'][$i];
		?>
		<tr>
			<td><?=$query['regdate'][$i]?></td>
			<td><?=$query['title'][$i]?></td>
			<td><?=$company['company']?></td>
			<td><?=$query['email'][$i]?></td>
			<td><?=$query['ucnt'][$i]?></td>
			<td><?=$query['rcnt'][$i]?></td>
			<td><?=number_format($query['total_max_coin'][$i])?></td>
			<td><?=number_format($result_coin)?></td>
			<td><?=$query['sdate'][$i]?></td>
			<td><?=$query['edate'][$i]?></td>
		</tr>
	<?}
		if(count($query['idx'])==0){?>
			<tr><td colspan="6">조회된 목록이 없습니다.</td></tr>
		<?}
	?>
	<input type="hidden" id="page_num" value="<?=$p?>">
	<input type="hidden" value="<?=$pagesize?>" id="list_cnt" >
	<input type="hidden" value="<?=$search?>" id="backoff_search">
	<input type="hidden" value="backchall_list" id="backoffice_type">
	<input type="hidden" value="<?=$tclass?>" id="tclass">
	<input type="hidden" value="<?=$sdate?>" id="backoff_sdate">
	<input type="hidden" value="<?=$edate?>" id="backoff_edate">
	<input type="hidden" value="<?=$kind?>" id="kind">|
		<?php echo back_pageing($pagingsize, $total_count, $pagesize, $string)?>
	<? echo "|".$tclass."|backchall|";
	if($_POST['reset']=="re"){
		echo $last_week."|".$today;
	}
}

if($mode == "backchall_user"){
	//전월날짜
	$timestamp = strtotime("-3 months");
	$last_week = date("Y-m-d", $timestamp);

	$sdate = $_POST['sdate'];
	$edate = $_POST['edate'];

	$where = "";
	if($sdate && $edate){
		$where = $where .= " and (DATE_FORMAT(comment_regdate, '%Y-%m-%d') >= '".$sdate."' and DATE_FORMAT(comment_regdate, '%Y-%m-%d') <= '".$edate."')";
	}

	$search = $_POST['search'];
	if($search){
		$where = $where .= " and (cr.email like '%".$search."%' or wm.company like '%".$search."%' or wc.title like '%".$search."%' or wm.name like '%".$search."%' or cr.part like '%".$search."%')";
	}

	$kind = $_POST['kind'];
	$tclass = $_POST['tclass'];

	if($kind){
		$sort = " cr.".$kind;
	}else if($kind == "company"){
		$sort = " wm.company";
	}else if($kind == "title"){
		$sort = " wc.title";
	}else{
		$sort = " cr.idx";
	}

	if($tclass=="btn_sort_up"){
		$updown = " asc";
	}else{
		$updown = " desc";
	}

	$url = "backchall_user";
	$string = "&page=".$url;

	$p = $_POST['p']?$_POST['p']:$_GET['p'];
	if (!$p){
		$p = 1;
	}

	$list = $_POST['list'];
	$pagingsize = 5;					//페이징 사이즈
	if($list){
		$pagesize = $list;	
	}else{
		$pagesize = 15;
	}					//페이지 출력갯수
	$startnum = 0;						//페이지 시작번호
	$endnum = $p * $pagesize;			//페이지 끝번호

	//시작번호
	if ($p == 1){
		$startnum = 0;
	}else{
		$startnum = ($p - 1) * $pagesize;
	}

	$sql = "select cr.idx, cr.email, cr.comment_regdate, cr.comment, cr.part, wm.company, wm.name, wc.title, wc.coin ";
	$sql = $sql .= ", (select count(1) from work_challenges_file_info as cf where cf.challenges_idx = cr.idx) as cnt";
	$sql = $sql .= " from work_challenges_result as cr, work_member as wm, work_challenges as wc ";
	$sql = $sql .= " where cr.email = wm.email and wm.state = '0' and cr.challenges_idx = wc.idx ".$where;
	$sql = $sql .= " order by ".$sort.$updown."";
	$sql = $sql .= " limit ".$startnum.",".$pagesize;
	$query = selectAllQuery($sql);

	$sql ="select count(cr.idx) as cnt from work_challenges_result as cr, work_member as wm, work_challenges as wc";
	$sql = $sql .= " where cr.email = wm.email and wm.state = '0' and cr.challenges_idx = wc.idx ".$where;
	$history_info_cnt = selectQuery($sql);
	if($history_info_cnt['cnt']){ 
		$total_count = $history_info_cnt['cnt'];
	}

	if($_POST['reset']=="re"){
		if($code == "all"){
			$codekind = "";
		}else{
			$codekind = "and service = '".$code."'";
		}
		$sql = "select cr.idx, cr.email, cr.comment_regdate, cr.comment, cr.part, wm.company, wm.name, wc.title, wc.coin ";
		$sql = $sql .= ", (select count(1) from work_challenges_file_info as cf where cf.challenges_idx = cr.idx) as cnt";
		$sql = $sql .= " from work_challenges_result as cr, work_member as wm, work_challenges as wc ";
		$sql = $sql .= " where cr.email = wm.email and wm.state = '0' and cr.challenges_idx = wc.idx and";
		$sql = $sql .= " (DATE_FORMAT(comment_regdate, '%Y-%m-%d')>= '".$last_week."' and  DATE_FORMAT(comment_regdate, '%Y-%m-%d') <= '".$today."')";
		$sql = $sql .= " order by cr.idx desc";
		$sql = $sql .= " limit 0,15";
		$query = selectAllQuery($sql);

		$sql = "select count(idx) as cnt from work_challenges_result ";
    	$sql = $sql .= "where ( DATE_FORMAT(comment_regdate, '%Y-%m-%d') >= '".$last_week."' and  DATE_FORMAT(comment_regdate, '%Y-%m-%d') <= '".$today."') ";
		$history_info_cnt = selectQuery($sql);
		if($history_info_cnt['cnt']){ 
			$total_count = $history_info_cnt['cnt'];
		}

		$sdate = $last_week;
		$edate = $today;
	}

	for($i=0; $i<count($query['idx']); $i++){	?>
		<tr>
			<td><?=$query['comment_regdate'][$i]?></td>
			<td><?=$query['title'][$i]?></td>
			<td><?=$query['name'][$i]?></td>
			<td><?=$query['company'][$i]."/".$query['part'][$i]?></td>
			<td><?=$query['cnt'][$i]?></td>
			<td><?=$query['coin'][$i]?></td>
			<td class="backoff_cham_comment"><p><?=$query['comment'][$i]?></p></td>
		</tr>
	<?}
		if(count($query['idx'])==0){?>
			<tr><td colspan="6">조회된 목록이 없습니다.</td></tr>
		<?}
	?>
	<input type="hidden" id="page_num" value="<?=$p?>">
	<input type="hidden" value="<?=$pagesize?>" id="list_cnt" >
	<input type="hidden" value="<?=$search?>" id="backoff_search">
	<input type="hidden" value="backchall_list" id="backoffice_type">
	<input type="hidden" value="<?=$tclass?>" id="tclass">
	<input type="hidden" value="<?=$sdate?>" id="backoff_sdate">
	<input type="hidden" value="<?=$edate?>" id="backoff_edate">
	<input type="hidden" value="<?=$kind?>" id="kind">|
		<?php echo back_pageing($pagingsize, $total_count, $pagesize, $string)?>
	<? echo "|".$tclass."|backchall_user|";
	if($_POST['reset']=="re"){
		echo $last_week."|".$today;
	}

}

//백오피스 파티 리스트
if($mode == "backparty_list"){

	//전월날짜
	$timestamp = strtotime("-1 months");
	$last_week = date("Y-m-d", $timestamp);

	$sdate = $_POST['sdate'];
	$edate = $_POST['edate'];
	$where = "";

	if($sdate && $edate){
		$where = $where .= " and (wc.regdate >= '".$sdate."' and wc.regdate <= '".$edate."')";
	}

	$search = $_POST['search'];
	if($search){
		$where = $where .= " and (wc.email like '%".$search."%' or wm.company like '%".$search."%' or wc.title like '%".$search."%')";
	}

	$kind = $_POST['kind'];
	$tclass = $_POST['tclass'];

	if($kind){
		$sort = " wc.".$kind;
	}else if($kind == "company"){
		$sort = " wm.company";
	}else{
		$sort = " wc.idx";
	}

	if($tclass=="btn_sort_up"){
		$updown = " asc";
	}else{
		$updown = " desc";
	}

	$url = "backparty_list";
	$string = "&page=".$url;

	$p = $_POST['p']?$_POST['p']:$_GET['p'];
	if (!$p){
		$p = 1;
	}

	$list = $_POST['list'];

	$pagingsize = 5;					//페이징 사이즈
	if($list){
		$pagesize = $list;	
	}else{
		$pagesize = 15;
	}					
		//페이지 출력갯수
	$startnum = 0;						//페이지 시작번호
	$endnum = $p * $pagesize;			//페이지 끝번호

	//시작번호
	if ($p == 1){
		$startnum = 0;
	}else{
		$startnum = ($p - 1) * $pagesize;
	}
	
	
	$sql = "select  wc.idx, wc.title, wc.regdate, wc.state, wc.com_coin_pro, wc.email, wc.page_count, (select count(idx) from work_todaywork_project_user as cu where cu.project_idx = wc.idx) as ucnt from work_todaywork_project as wc where wc.state in('0','1') ".$where ; 
	$sql = $sql .= " order by ".$sort.$updown." limit ". $startnum.",".$pagesize;
	$query = selectAllQuery($sql);

	$sql = "select count(wc.idx) as cnt from work_todaywork_project as wc, work_member as wm where wc.email = wm.email and wc.state in ('0','1') and wm.state = '0' ".$where;
	$history_info_cnt = selectQuery($sql);
	if($history_info_cnt['cnt']){ 
		$total_count = $history_info_cnt['cnt'];
	}

	
	if($_POST['reset']=="re"){
		if($code == "all"){
			$codekind = "";
		}else{
			$codekind = "and service = '".$code."'";
		}
		$sql = "select  wc.idx, wc.title, wc.regdate, wc.state, wc.com_coin_pro, wc.email, wc.page_count, (select count(idx) from work_todaywork_project_user as cu where cu.project_idx = wc.idx) as ucnt from work_todaywork_project as wc where wc.state in ('0','1') and (wc.sdate >= '".$last_week."' and wc.edate <= '".$today."')"; 
		$sql = $sql .= " order by idx desc limit 0,15";
		$query = selectAllQuery($sql);

		$sql = "select count(wc.idx) as cnt from work_todaywork_project as wc, work_member as wm where wc.email = wm.email and wc.state in ('0','1') and wm.state = '0' and (wc.regdate >= '".$last_week."' and wc.regdate <= '".$today."') ";
		$history_info_cnt = selectQuery($sql);
		if($history_info_cnt['cnt']){ 
			$total_count = $history_info_cnt['cnt'];
		}

		$sdate = $last_week;
		$edate = $today;
	}
	

	for($i=0; $i<count($query['idx']); $i++){
		$sql = "select email, company from work_member where email = '".$query['email'][$i]."' and state = '0' ";
		$company = selectQuery($sql);
		?>
		<tr>
			<td><?=$query['regdate'][$i]?></td>
			<td><?=$query['email'][$i]?></td>
			<td><?=$query['title'][$i]?></td>
			<td><?=number_format($query['com_coin_pro'][$i])?></td>
			<td><?=$query['ucnt'][$i]?></td>
			<td><?=$company['company']?></td>
			<td><?=$query['state'][$i]?></td>
			<td><?=$query['page_count'][$i]?></td>
		</tr>
	<?}
		if(count($query['idx'])==0){?>
			<tr><td colspan="6">조회된 목록이 없습니다.</td></tr>
		<?}
	?>
	<input type="hidden" id="page_num" value="<?=$p?>">
	<input type="hidden" value="<?=$pagesize?>" id="list_cnt" >
	<input type="hidden" value="<?=$search?>" id="backoff_search">
	<input type="hidden" value="backchall_list" id="backoffice_type">
	<input type="hidden" value="<?=$tclass?>" id="tclass">
	<input type="hidden" value="<?=$sdate?>" id="backoff_sdate">
	<input type="hidden" value="<?=$edate?>" id="backoff_edate">
	<input type="hidden" value="<?=$kind?>" id="kind">|
		<?php echo back_pageing($pagingsize, $total_count, $pagesize, $string)?>
	<? echo "|".$tclass."|backparty|";
	if($_POST['reset']=="re"){
		echo $last_week."|".$today;
	}
}

//백오피스 좋아요 리스트
if($mode == "backwork_list"){
	
	$sdate = $_POST['sdate'];
	$edate = $_POST['edate'];

	$where = "";
	if($sdate && $edate){
		$where = $where .= " and (wc.workdate >= '".$sdate."' and wc.workdate <= '".$edate."')";
	}

	$search = $_POST['search'];
	if($search){
		$where = $where .= " and (wc.name like '%".$search."%' or wm.company like '%".$search."%' or wc.email like '%".$search."%' or wc.contents like '%".$search."%')";
	}

	$code = $_POST['code'];
	if($code == "work"){
		$where = $where .= " and work_flag = '2' and share_flag = '0'";
	}else if($code == "share"){
		$where = $where .= " and work_flag = '2' and share_flag in('1','2')";
	}else if($code == "request"){
		$where = $where .= " and work_flag = '3'";
	}else if($code == "report"){
		$where = $where .= " and work_flag = '1'";
	}

	$kind = $_POST['kind'];
	$tclass = $_POST['tclass'];

	if($kind){
		$sort = " wc.".$kind;
	}else if($kind == "company"){
		$sort = " wm.company";
	}else{
		$sort = " wc.idx";
	}

	if($tclass=="btn_sort_up"){
		$updown = " asc";
	}else{
		$updown = " desc";
	}

	$url = "backwork_list";
	$string = "&page=".$url;

	$p = $_POST['p']?$_POST['p']:$_GET['p'];
	if (!$p){
		$p = 1;
	}

	$list = $_POST['list'];
	$pagingsize = 5;					//페이징 사이즈
	if($list){
		$pagesize = $list;	
	}else{
		$pagesize = 15;
	}						//페이지 출력갯수
	$startnum = 0;						//페이지 시작번호
	$endnum = $p * $pagesize;			//페이지 끝번호

	//시작번호
	if ($p == 1){
		$startnum = 0;
	}else{
		$startnum = ($p - 1) * $pagesize;
	}

	$sql = "select wc.idx, wc.state, wc.regdate, wc.name, wc.email, wc.workdate, wm.company, wc.contents, wc.work_flag, wc.repeat_flag, wc.share_flag, wc.work_idx from work_todaywork as wc, work_member as wm where wc.email = wm.email and wm.state = '0' ".$where ; 
	$sql = $sql .= " order by ".$sort.$updown." limit ". $startnum.",".$pagesize;
	$query = selectAllQuery($sql);

	$sql = "select count(wc.idx) as cnt from work_todaywork as wc, work_member as wm where wc.email = wm.email and wm.state = '0'  ".$where ;
	$history_info_cnt = selectQuery($sql);
	if($history_info_cnt['cnt']){ 
		$total_count = $history_info_cnt['cnt'];
	}

	if($_POST['reset']=="re"){
		if($code == "work"){
			$codekind = "and work_flag = '2' and share_flag = '0'";
		}else if($code == "share"){
			$codekind = "and work_flag = '2' and share_flag in('1','2')";
		}else if($code == "request"){
			$codekind = "and work_flag = '3'";
		}else if($code == "report"){
			$codekind = "and work_flag = '1'";
		}
		$sql = "select wc.idx, wc.state, wc.regdate, wc.name, wc.email, wc.workdate, wm.company, wc.contents, wc.work_flag, wc.repeat_flag, wc.share_flag, wc.work_idx from work_todaywork as wc, work_member as wm where wc.email = wm.email and wm.state = '0' ".$codekind." and (wc.workdate >= '".$last_week."' and wc.workdate <= '".$today."')"; 
		$sql = $sql .= " order by idx desc limit 0,15";
		$query = selectAllQuery($sql);

		$sql = "select count(wc.idx) as cnt from work_todaywork as wc, work_member as wm where wc.email = wm.email and wm.state = '0' ".$codekind." and (wc.workdate >= '".$last_week."' and wc.workdate <= '".$today."') ";
		$history_info_cnt = selectQuery($sql);
		if($history_info_cnt['cnt']){ 
			$total_count = $history_info_cnt['cnt'];
		}

		$sdate = $last_week;
		$edate = $today;

		$listarrow = $tclass;
		
	}
	?>
	<input type="hidden" id="page_num" value="<?=$p?>">
	<input type="hidden" value="<?=$pagesize?>" id="list_cnt" >
	<input type="hidden" value="<?=$search?>" id="backoff_search">
	<input type="hidden" value="<?=$sdate?>" id="backoff_sdate">
	<input type="hidden" value="<?=$edate?>" id="backoff_edate">
	<input type="hidden" value="backwork_list" id="backoffice_type">
	<input type="hidden" value="<?=$tclass?>" id="tclass">
	<input type="hidden" value="<?=$kind?>" id="kind">
	<input type="hidden" value="<?=$code?>" id="code">
	<?
	if($code == "work"){?>
		<thead>
			<tr>
				<th class="work_regdate"><div class="back_sortkind" value="regdate">등록시간 <button class="list_arrow" value="btn_sort_down"></button></div></th>
				<th class="work_user"><div class="back_sortkind" value="name">작성자<button class="list_arrow" value="btn_sort_down"></button></div></th>
				<th class="work_state"><div class="back_sortkind" value="state">state</div></th>
				<th class="work_company"><div class="back_sortkind" value="company">기업<button class="list_arrow" value="btn_sort_down"></button></div></th>
				<th class="work_content" id="work_contents"><div class="back_sortkind" value="contents">작성 내용<button class="list_arrow" value="btn_sort_down"></button></div></th>
				<th class="work_again"><div class="back_sortkind" value="repeat_flag">반복 설정<button class="list_arrow" value="btn_sort_down"></button></div></th>
				<th class="work_date"><div class="back_sortkind" value="workdate">업무수행일<button class="list_arrow" value="btn_sort_down"></button></div></th>
				<th class="work_edit"><div>수정/삭제</div></th>
			</tr>
		</thead>
		<tbody>
		<?for($i=0; $i<count($query['idx']); $i++){
			$repeat = $query['repeat_flag'][$i];
			$code_arr = ['0','1','2','3'];
			$title_arr = ['설정 안함','매일반복','매주반복','매월반복'];
			$idx = $query['idx'][$i];
			for($j=0;$j<count($code_arr);$j++){
				if($repeat == $code_arr[$j]){
					$repeat_flag = $title_arr[$j];
				}		
			}
			?>
			<tr id="table_tr_<?=$idx?>">
				<td><?=$query['regdate'][$i]?></td>
				<td><?=$query['name'][$i]."(".$query['email'][$i].")"?></td>
				<td class="work_state_<?=$idx?>"><?=$query['state'][$i]?></td> 
				<td><?=$query['company'][$i]?></td>
				<td class="work_content">
					<p><?=$query['contents'][$i]?></p>
					<textarea class="form-control form-control-sm content_edit_<?=$idx?>" style="width:95%;display:none;" rows="1"><?=$query['contents'][$i]?></textarea>
				</td>
				<td><?=$repeat_flag?></td>
				<td><?=$query['workdate'][$i]?></td>
				<td>
					<div class="btn-group btn-group-sm d-flex back_btn" id="back_btn_<?=$idx?>" value="<?=$idx?>" role="group" style="position:relative;z-index:1;" aria-label="Basic outlined example">
						<button type="button" class="btn btn-outline-dark" id="back_edit">수정</button>
						<button type="button" class="btn btn-outline-dark" id="back_remove">삭제</button>
					</div>
					<div class="btn-group btn-group-sm d-flex back_btn" id="back_btn_e_<?=$idx?>" value="<?=$idx?>" role="group" style="position:relative;z-index:1;">
						<button style="display:none;" type="button" class="btn btn-outline-dark" id="back_enter">확인</button>
						<button style="display:none;" type="button" class="btn btn-outline-dark" id="back_cancel">취소</button>
					</div>
				</td>
			</tr>
			<?}
			if(count($query['idx'])==0){?>
				<tr><td colspan="6">조회된 목록이 없습니다.</td></tr>
			<?}?>
		</tbody>|
	<?}else if($code == "share"){?>
		<thead>
			<tr>
				<th class="work_regdate"><div class="back_sortkind" value="regdate">등록시간<button class="list_arrow" value="btn_sort_down"></button></div></th>
				<th class="work_user"><div class="back_sortkind" value="name">작성자<button class="list_arrow" value="btn_sort_down"></button></div></th>
				<th class="work_state"><div class="back_sortkind" value="state">state</div></th>
				<th class="work_company"><div class="back_sortkind" value="company">기업<button class="list_arrow" value="btn_sort_down"></button></div></th>
				<th class="work_content"><div class="back_sortkind" value="contents">작성 내용<button class="list_arrow" value="btn_sort_down"></button></div></th>
				<th class="work_share"><div class="back_sortkind" value="repeat_flag">공유여부<button class="list_arrow" value="btn_sort_down"></button></div></th>
				<th class="work_again"><div class="back_sortkind" value="repeat_flag">반복 설정<button class="list_arrow" value="btn_sort_down"></button></div></th>
				<th class="work_date"><div class="back_sortkind" value="workdate">업무수행일<button class="list_arrow" value="btn_sort_down"></button></div></th>
				<th class="work_edit"><div class="back_edit">수정/삭제</div></th>
			</tr>
		</thead>
		<tbody>
		<?for($i=0; $i<count($query['idx']); $i++){
			$repeat = $query['repeat_flag'][$i];
			$share_flag = $query['share_flag'][$i];
			if($share_flag=='1'){
				$share_sort = "공유함";
			}else{
				$share_sort = "공유받음";
			}
			$code_arr = ['0','1','2','3'];
			$title_arr = ['설정 안함','매일반복','매주반복','매월반복'];
			$idx = $query['idx'][$i];
			for($j=0;$j<count($code_arr);$j++){
				if($repeat == $code_arr[$j]){
					$repeat_flag = $title_arr[$j];
				}		
			}
			?>
			<tr id="table_tr_<?=$idx?>">
				<td><?=$query['regdate'][$i]?></td>
				<td><?=$query['name'][$i]."(".$query['email'][$i].")"?></td>
				<td class="work_state_<?=$idx?>"><?=$query['state'][$i]?></td> 
				<td><?=$query['company'][$i]?></td>
				<td class="work_content">
					<p><?=$query['contents'][$i]?></p>
					<textarea class="form-control form-control-sm content_edit_<?=$idx?>" style="width:95%;display:none;" rows="1"><?=$query['contents'][$i]?></textarea>
				</td>
				<td><?=$share_sort?></td>
				<td><?=$repeat_flag?></td>
				<td><?=$query['workdate'][$i]?></td>
				<td>
					<div class="btn-group btn-group-sm d-flex back_btn " id="back_btn_<?=$idx?>" value="<?=$idx?>" role="group" style="position:relative;z-index:1;" aria-label="Basic outlined example">
						<button type="button" class="btn btn-outline-dark" id="back_edit">수정</button>
						<button type="button" class="btn btn-outline-dark" id="back_remove">삭제</button>
					</div>
					<div class="btn-group btn-group-sm d-flex back_btn" id="back_btn_e_<?=$idx?>" value="<?=$idx?>" role="group" style="position:relative;z-index:1;">
						<button style="display:none;" type="button" class="btn btn-outline-dark" id="back_enter">확인</button>
						<button style="display:none;" type="button" class="btn btn-outline-dark" id="back_cancel">취소</button>
					</div>
				</td>
			</tr>
			<?}
			if(count($query['idx'])==0){?>
				<tr><td colspan="6">조회된 목록이 없습니다.</td></tr>
			<?}?>
		</tbody>|
	<?}else if($code == "request"){?>
		<thead>
			<tr>
				<th class="work_regdate"><div class="back_sortkind" value="regdate">등록시간<button class="list_arrow" value="btn_sort_down"></button></div></th>
				<th class="work_user"><div class="back_sortkind" value="name">작성자<button class="list_arrow" value="btn_sort_down"></button></div></th>
				<th class="work_state"><div class="back_sortkind" value="state">state</div></th>
				<th class="work_company"><div class="back_sortkind" value="company">기업<button class="list_arrow" value="btn_sort_down"></button></div></th>
				<th class="work_content" id="work_contents"><div class="back_sortkind" value="contents">작성 내용<button class="list_arrow" value="btn_sort_down"></button></div></th>
				<th class="work_request"><div class="back_sortkind">요청여부<button class="list_arrow" value="btn_sort_down"></button></div></th>
				<th class="work_again><div class="back_sortkind" value="repeat_flag">반복 설정<button class="list_arrow" value="btn_sort_down"></button></div></th>
				<th class="work_date"><div class="back_sortkind" value="workdate">업무수행일<button class="list_arrow" value="btn_sort_down"></button></div></th>
				<th class="work_edit"><div class="back_edit">수정/삭제</div></th>
			</tr>
		</thead>
		<tbody>
		<?for($i=0; $i<count($query['idx']); $i++){
			$repeat = $query['repeat_flag'][$i];
			$work_flag = $query['work_idx'][$i];
				if($work_flag){
					$reqname = "요청받음";
				}else{
					$reqname = "요청함";
				}
			$code_arr = ['0','1','2','3'];
			$title_arr = ['설정 안함','매일반복','매주반복','매월반복'];
			$idx = $query['idx'][$i];
			for($j=0;$j<count($code_arr);$j++){
				if($repeat == $code_arr[$j]){
					$repeat_flag = $title_arr[$j];
				}		
			}
			?>
			<tr id="table_tr_<?=$idx?>">
				<td><?=$query['regdate'][$i]?></td>
				<td><?=$query['name'][$i]."(".$query['email'][$i].")"?></td>
				<td class="work_state_<?=$idx?>"><?=$query['state'][$i]?></td> 
				<td><?=$query['company'][$i]?></td>
				<td class="work_content">
					<p><?=$query['contents'][$i]?></p>
					<textarea class="form-control form-control-sm content_edit_<?=$idx?>" style="width:95%;display:none;" rows="1"><?=$query['contents'][$i]?></textarea>
				</td>
				<td><?=$reqname?></td>
				<td><?=$repeat_flag?></td>
				<td><?=$query['workdate'][$i]?></td>
				<td>
					<div class="btn-group btn-group-sm d-flex back_btn " id="back_btn_<?=$idx?>" value="<?=$idx?>" role="group" style="position:relative;z-index:1;" aria-label="Basic outlined example">
						<button type="button" class="btn btn-outline-dark" id="back_edit">수정</button>
						<button type="button" class="btn btn-outline-dark" id="back_remove">삭제</button>
					</div>
					<div class="btn-group btn-group-sm d-flex back_btn" id="back_btn_e_<?=$idx?>" value="<?=$idx?>" role="group" style="position:relative;z-index:1;">
						<button style="display:none;" type="button" class="btn btn-outline-dark" id="back_enter">확인</button>
						<button style="display:none;" type="button" class="btn btn-outline-dark" id="back_cancel">취소</button>
					</div>
				</td>
			</tr>
		<?}
			if(count($query['idx'])==0){?>
				<tr><td colspan="6">조회된 목록이 없습니다.</td></tr>
			<?}
		?>
		</tbody>|
	<?}else if($code == "report"){?>
		<thead>
			<tr>
				<th class="work_regdate"><div class="back_sortkind" value="regdate">등록시간<button class="list_arrow" value="btn_sort_down"></button></div></th>
				<th class="work_user"><div class="back_sortkind" value="name">작성자<button class="list_arrow" value="btn_sort_down"></button></div></th>
				<th class="work_state"><div class="back_sortkind" value="state">state</div></th>
				<th class="work_company"><div class="back_sortkind" value="company">기업<button class="list_arrow" value="btn_sort_down"></button></div></th>
				<th class="work_content" id="work_contents"><div class="back_sortkind" value="contents">작성 내용<button class="list_arrow" value="btn_sort_down"></button></div></th>
				<th class="work_report"><div class="back_sortkind" value="repeat_flag">보고 여부<button class="list_arrow" value="btn_sort_down"></button></div></th>
				<th class="work_again"><div class="back_sortkind" value="repeat_flag">반복 설정<button class="list_arrow" value="btn_sort_down"></button></div></th>
				<th class="work_date"><div class="back_sortkind" value="workdate">업무수행일<button class="list_arrow" value="btn_sort_down"></button></div></th>
				<th class="work_edit"><div class="back_edit">수정/삭제</div></th>
			</tr>
		</thead>
		<tbody>
		<?for($i=0; $i<count($query['idx']); $i++){
			$repeat = $query['repeat_flag'][$i];
			$work_flag = $query['work_idx'][$i];
				if($work_flag){
					$repname = "보고받음";
				}else{
					$repname = "보고함";
				}
			$idx = $query['idx'][$i];
			for($j=0;$j<count($code_arr);$j++){
				if($repeat == $code_arr[$j]){
					$repeat_flag = $title_arr[$j];
				}		
			}
			?>
			<tr id="table_tr_<?=$idx?>">
				<td><?=$query['regdate'][$i]?></td>
				<td><?=$query['name'][$i]."(".$query['email'][$i].")"?></td>
				<td class="work_state_<?=$idx?>"><?=$query['state'][$i]?></td> 
				<td><?=$query['company'][$i]?></td>
				<td class="work_content">
					<p><?=$query['contents'][$i]?></p>
					<textarea class="form-control form-control-sm content_edit_<?=$idx?>" style="width:95%;display:none;" rows="1"><?=$query['contents'][$i]?></textarea>
				</td>
				<td><?=$repname?></td>
				<td><?=$repeat_flag?></td>
				<td><?=$query['workdate'][$i]?></td>
				<td>
					<div class="btn-group btn-group-sm d-flex back_btn " id="back_btn_<?=$idx?>" value="<?=$idx?>" role="group" style="position:relative;z-index:1;" aria-label="Basic outlined example">
						<button type="button" class="btn btn-outline-dark" id="back_edit">수정</button>
						<button type="button" class="btn btn-outline-dark" id="back_remove">삭제</button>
					</div>
					<div class="btn-group btn-group-sm d-flex back_btn" id="back_btn_e_<?=$idx?>" value="<?=$idx?>" role="group" style="position:relative;z-index:1;">
						<button style="display:none;" type="button" class="btn btn-outline-dark" id="back_enter">확인</button>
						<button style="display:none;" type="button" class="btn btn-outline-dark" id="back_cancel">취소</button>
					</div>
				</td>
			</tr>
		<?}
			if(count($query['idx'])==0){?>
				<tr><td colspan="6">조회된 목록이 없습니다.</td></tr>
			<?}
		?>
		</tbody>|
	<?}?>
	<?php echo back_pageing($pagingsize, $total_count, $pagesize, $string)?>
	<?echo "|".$tclass."|backwork|";
	if($_POST['reset']=="re"){
		echo $last_week."|".$today;
	}
}

//백오피스 퇴근/소감 리스트
if($mode == "backcomm_list"){
	$sdate = $_POST['sdate'];
	$edate = $_POST['edate'];

	$where = "";
	if($sdate && $edate){
		$where = $where .= " and (wr.workdate >= '".$sdate."' and wr.workdate <= '".$edate."')";
	}

	$search = $_POST['search'];
	if($search){
		$where = $where .= " and (wr.email like '%".$search."%' or wr.name like '%".$search."%' or wm.company like '%".$search."%')";
	}

	$code = $_POST['code'];
	if($code=="all"){
		$where = $where .= " and wr.work_idx in (1,2,3,4,5,6,7,8,9)";
	}else{
		$where = $where .= " and wr.work_idx in (".$code.")";
	}

	$kind = $_POST['kind'];
	$tclass = $_POST['tclass'];

	if($kind == "company"){
		$sort = " wm.".$kind;
	}else if($kind != ""){
		$sort = " wr.".$kind;
	}else{
		$sort = " wr.idx";
	}

	if($tclass=="btn_sort_up"){
		$updown = " asc";
	}else{
		$updown = " desc";
	}

	$url = "backcomm_list";
	$string = "&page=".$url;

	$p = $_POST['p']?$_POST['p']:$_GET['p'];
	if (!$p){
		$p = 1;
	}

	$list = $_POST['list'];
	$pagingsize = 5;					//페이징 사이즈
	if($list){
		$pagesize = $list;	
	}else{
		$pagesize = 15;
	}
	
	//페이지 출력갯수
	$startnum = 0;						//페이지 시작번호
	$endnum = $p * $pagesize;			//페이지 끝번호

	//시작번호
	if ($p == 1){
		$startnum = 0;
	}else{
		$startnum = ($p - 1) * $pagesize;
	}

	$sql = "select wr.idx, wr.regdate, wr.email, wr.name, wr.partno, wr.part, wr.work_idx, wr.comment, wm.company from work_todaywork_review as wr, work_company as wm where wr.companyno = wm.idx and wr.state = '0' and wr.comment != '' ".$where;
	$sql = $sql .= " order by ".$sort.$updown." limit ". $startnum.",".$pagesize;
	$query = selectAllQuery($sql);

	$sql = "select count(wr.idx) as cnt from work_todaywork_review as wr, work_company as wm where wr.companyno = wm.idx and wr.state = '0' and wr.comment != '' ".$where;
	$history_info_cnt = selectQuery($sql);
	if($history_info_cnt['cnt']){ 
		$total_count = $history_info_cnt['cnt'];
	}

	if($_POST['reset']=="re"){

		$sql = "select wr.idx, wr.regdate, wr.email, wr.name, wr.partno, wr.part, wr.work_idx, wr.comment, wm.company from work_todaywork_review as wr, work_company as wm where wr.companyno = wm.idx and wr.state = '0' and wr.comment != '' and (wr.workdate >= '".$last_week."' and wr.workdate <= '".$today."') order by wr.idx desc limit 0,15";
		$query = selectAllQuery($sql);

		$where = "";
		$sql = "select count(*) as cnt from work_todaywork_review where state = '0' and comment != '' and (workdate >= '".$last_week."' and workdate <= '".$today."') ";
		$history_info_cnt = selectQuery($sql);
		if($history_info_cnt['cnt']){ 
			$total_count = $history_info_cnt['cnt'];
		}

		$sdate = $last_week;
		$edate = $today;
	}
		
	for($i=0; $i<count($query['idx']); $i++){
		$mem_idx = $query['idx'][$i];
		$feelkind = $query['work_idx'][$i];
		$feel_arr = ['1','2','3','4','5','6','7','8','9'];
		$title_arr = ['최고야','뿌듯해','기분좋아','감사해','재밌어','수고했어','무난해','지쳤어','속상해'];
		for($j=0;$j<count($feel_arr);$j++){
			if($feelkind == $feel_arr[$j]){
				$feel_kind = $title_arr[$j];
			}
		}
		?>
		<tr>
			<td><?=$query['regdate'][$i]?></td>
			<td><?=$query['name'][$i]."(".$query['email'][$i].")"?></td>
			<td><?=$query['company'][$i]?></td>
			<td><?=$query['part'][$i]?></td>
			<td class="backoff_memo"><?=$feel_kind?></td>
			<td class="backoff_comment"><p><?=$query['comment'][$i]?></p></td>
		</tr>
	<?}
		if(count($query['idx'])==0){?>
			<tr><td colspan="6">조회된 목록이 없습니다.</td></tr>
		<?}
	?>
	<input type="hidden" value="<?=$p?>" id="page_num" >
	<input type="hidden" value="<?=$pagesize?>" id="list_cnt">
	<input type="hidden" value="<?=$search?>" id="backoff_search">
	<input type="hidden" value="<?=$sdate?>" id="backoff_sdate">
	<input type="hidden" value="<?=$edate?>" id="backoff_edate">
	<input type="hidden" value="backcomm_list" id="backoffice_type">
	<input type="hidden" value="<?=$tclass?>" id="tclass">
	<input type="hidden" value="<?=$kind?>" id="kind">
	<input type="hidden" value="<?=$code?>" id="code">|
	<?php echo back_pageing($pagingsize, $total_count, $pagesize, $string)?>
	<?echo "|".$tclass."|backcomm|";
	if($_POST['reset']=="re"){
		echo $last_week."|".$today;
	}
}

//백오피스 역량 리스트(2023.11.29)
if($mode == "backcp_list"){
	$sdate = $_POST['sdate'];
	$edate = $_POST['edate'];

	$where = "";
	if($sdate && $edate){
		$where = $where .= "where (cp.workdate >= '".$sdate."' and cp.workdate <= '".$edate."')";
	}

	$search = $_POST['search'];
	if($search){
		$where = $where .= " and (cp.email like '%".$search."%' or cp.name like '%".$search."%')";
	}

	$code = $_POST['code'];
	if($code=="all"){
		$where = $where .= "";
	}else{
		$where = $where .= " and cp.".$code."='1'";
	}

	$kind = $_POST['kind'];
	$tclass = $_POST['tclass'];

	if($kind == "company"){
		$sort = $kind;
	}else{
		$sort = " cp.idx";
	}

	if($tclass=="btn_sort_up"){
		$updown = " asc";
	}else{
		$updown = " desc";
	}

	$url = "backcp_list";
	$string = "&page=".$url;

	$p = $_POST['p']?$_POST['p']:$_GET['p'];
	if (!$p){
		$p = 1;
	}

	$list = $_POST['list'];
	$pagingsize = 5;					//페이징 사이즈
	if($list){
		$pagesize = $list;	
	}else{
		$pagesize = 15;
	}
	//페이지 출력갯수
	$startnum = 0;						//페이지 시작번호
	$endnum = $p * $pagesize;			//페이지 끝번호

	//시작번호
	if ($p == 1){
		$startnum = 0;
	}else{
		$startnum = ($p - 1) * $pagesize;
	}

	$sql = "select cp.idx, cp.state, cp.email, cp.name, (select company from work_company where idx = cp.companyno) as company,";
	$sql = $sql .= " (cp.type1 + cp.type2 + cp.type3 + cp.type4 + cp.type5 + cp.type6) as score,";
	$sql = $sql .= " cp.type1, cp.type2, cp.type3, cp.type4, cp.type5, cp.type6, cp.service, cp.act, cp.regdate,";
	$sql = $sql .= " (select act_title from work_cp_reward where state = 0 and act = cp.act and service = cp.service limit 0,1) as act_title";
	$sql = $sql .= " from work_cp_reward_list as cp ".$where;
	$sql = $sql .= " order by ".$sort.$updown." limit ". $startnum.",".$pagesize;
	$query = selectAllQuery($sql);

	$sql = "select count(idx) as cnt from work_cp_reward_list as cp ".$where;
	$history_info_cnt = selectQuery($sql);
	if($history_info_cnt['cnt']){ 
		$total_count = $history_info_cnt['cnt'];
	}

	if($_POST['reset']=="re"){
		$sql = "select cp.idx, cp.state, cp.email, cp.name, (select company from work_company where idx = cp.companyno) as company,";
		$sql = $sql .= " (cp.type1 + cp.type2 + cp.type3 + cp.type4 + cp.type5 + cp.type6) as score,";
		$sql = $sql .= " cp.type1, cp.type2, cp.type3, cp.type4, cp.type5, cp.type6, cp.service, cp.act, cp.regdate,";
		$sql = $sql .= " (select act_title from work_cp_reward where state = 0 and act = cp.act and service = cp.service limit 0,1) as act_title";
		$sql = $sql .= " from work_cp_reward_list as cp where (cp.workdate >= '".$last_week."' and cp.workdate <= '".$today."') order by cp.idx desc limit ".$startnum.",".$pagesize;
		$query = selectAllQuery($sql);

		$where = "";
		$sql = "select count(idx) as cnt from work_cp_reward_list where (workdate >= '".$last_week."' and workdate <= '".$today."')";
		$history_info_cnt = selectQuery($sql);
		if($history_info_cnt['cnt']){ 
			$total_count = $history_info_cnt['cnt'];
		}

		$sdate = $last_week;
		$edate = $today;
	}
		
	for($i=0; $i<count($query['idx']); $i++){
		?>
		<tr>
			<td><?=$query['company'][$i]?></td>
			<td><?=$query['name'][$i]?></td>
			<td><?=$query['email'][$i]?></td>
			<td style="<?=$query['type1'][$i]=="1"?"color:red;font-weight:bold":"color:black"?>"><?=$query['type1'][$i]?></td>
			<td style="<?=$query['type2'][$i]=="1"?"color:red;font-weight:bold":"color:black"?>"><?=$query['type2'][$i]?></td>
			<td style="<?=$query['type3'][$i]=="1"?"color:red;font-weight:bold":"color:black"?>"><?=$query['type3'][$i]?></td>
			<td style="<?=$query['type4'][$i]=="1"?"color:red;font-weight:bold":"color:black"?>"><?=$query['type4'][$i]?></td>
			<td style="<?=$query['type5'][$i]=="1"?"color:red;font-weight:bold":"color:black"?>"><?=$query['type5'][$i]?></td>
			<td style="<?=$query['type6'][$i]=="1"?"color:red;font-weight:bold":"color:black"?>"><?=$query['type6'][$i]?></td>
			<td><?=$query['act_title'][$i]?></td>
			<td><?=$query['regdate'][$i]?></td>
		</tr>
	<?}
		if(count($query['idx'])==0){?>
			<tr><td colspan="10">조회된 목록이 없습니다.</td></tr>
		<?}
	?>
	<input type="hidden" value="<?=$p?>" id="page_num" >
	<input type="hidden" value="<?=$pagesize?>" id="list_cnt">
	<input type="hidden" value="<?=$search?>" id="backoff_search">
	<input type="hidden" value="<?=$sdate?>" id="backoff_sdate">
	<input type="hidden" value="<?=$edate?>" id="backoff_edate">
	<input type="hidden" value="backcp_list" id="backoffice_type">
	<input type="hidden" value="<?=$tclass?>" id="tclass">
	<input type="hidden" value="<?=$kind?>" id="kind">
	<input type="hidden" value="<?=$code?>" id="code">|
	<?php echo back_pageing($pagingsize, $total_count, $pagesize, $string)?>
	<?echo "|".$tclass."|backcp|";
	if($_POST['reset']=="re"){
		echo $last_week."|".$today;
	}
}

//백오피스 출근기록 리스트(2023.12.11)
if($mode == "backin_list"){
	$sdate = $_POST['sdate'];
	$edate = $_POST['edate'];

	$where = "";
	if($sdate && $edate){
		$where = $where .= " and (ml.workdate >= '".$sdate."' and ml.workdate <= '".$edate."')";
	}

	$search = $_POST['search'];
	if($search){
		$where = $where .= " and (ml.email like '%".$search."%' or ml.name like '%".$search."%' or wm.company like '%".$search."%')";
	}

	$kind = $_POST['kind'];
	$tclass = $_POST['tclass'];

	// if($kind == "company"){
	// 	$sort = " wm.".$kind;
	// }else if($kind != ""){
	// 	$sort = " wr.".$kind;
	// }else{
	// 	$sort = " wr.idx";
	// }

	if($kind == "regdate"){
		$sort = " ml.".$kind;
	}else{
		$sort = " ml.idx";
	}

	if($tclass=="btn_sort_up"){
		$updown = " asc";
	}else{
		$updown = " desc";
	}

	$url = "backin_list";
	$string = "&page=".$url;

	$p = $_POST['p']?$_POST['p']:$_GET['p'];
	if (!$p){
		$p = 1;
	}

	$list = $_POST['list'];
	$pagingsize = 5;					//페이징 사이즈
	if($list){
		$pagesize = $list;	
	}else{
		$pagesize = 15;
	}
	
	//페이지 출력갯수
	$startnum = 0;						//페이지 시작번호
	$endnum = $p * $pagesize;			//페이지 끝번호

	//시작번호
	if ($p == 1){
		$startnum = 0;
	}else{
		$startnum = ($p - 1) * $pagesize;
	}

	$sql = "select ml.idx, ml.email, ml.ip, ml.regdate, ml.name, wm.company, wm.part from work_member_login as ml, work_member as wm where ml.email = wm.email and wm.state = '0' and ml.state = '0' ".$where; 
	$sql = $sql .= " order by ".$sort.$updown." limit ". $startnum.",".$pagesize;
	$query = selectAllQuery($sql);

	$sql = "select count(ml.idx) as cnt from work_member_login as ml, work_member as wm where ml.email = wm.email and wm.state = '0' and ml.state = '0' ".$where; 
	$history_info_cnt = selectQuery($sql);
	if($history_info_cnt['cnt']){ 
		$total_count = $history_info_cnt['cnt'];
	}

	if($_POST['reset']=="re"){
		$sql = "select ml.idx, ml.email, ml.ip, ml.regdate, ml.name, wm.company, wm.part from work_member_login as ml, work_member as wm where ml.email = wm.email and wm.state = '0' and ml.state = '0' order by ml.idx desc limit 0,15";
		$query = selectAllQuery($sql);

		$where = "";
		$sql = "select count(1) as cnt from work_member_login where state = '0' and (workdate >= '".$last_week."' and workdate <= '".$today."')";
		$history_info_cnt = selectQuery($sql);
		if($history_info_cnt['cnt']){ 
			$total_count = $history_info_cnt['cnt'];
		}

		$sdate = $last_week;
		$edate = $today;
	}
		
	for($i=0; $i<count($query['idx']); $i++){?>
		<tr>
			<td><?=$query['regdate'][$i]?></td>
			<td><?=$query['name'][$i]?></td>
			<td><?=$query['email'][$i]?></td>
			<td><?=$query['company'][$i]?></td>
			<td><?=$query['part'][$i]?></td>
			<td><?=$query['ip'][$i]?></td>
		</tr>
	<?}
		if(count($query['idx'])==0){?>
			<tr><td colspan="6">조회된 목록이 없습니다.</td></tr>
		<?}
	?>
	<input type="hidden" value="<?=$p?>" id="page_num" >
	<input type="hidden" value="<?=$pagesize?>" id="list_cnt">
	<input type="hidden" value="<?=$search?>" id="backoff_search">
	<input type="hidden" value="<?=$sdate?>" id="backoff_sdate">
	<input type="hidden" value="<?=$edate?>" id="backoff_edate">
	<input type="hidden" value="backin_list" id="backoffice_type">
	<input type="hidden" value="<?=$tclass?>" id="tclass">
	<input type="hidden" value="<?=$kind?>" id="kind">
	<input type="hidden" value="<?=$code?>" id="code">|
	<?php echo back_pageing($pagingsize, $total_count, $pagesize, $string)?>
	<?echo "|".$tclass."|backcomm|";
	if($_POST['reset']=="re"){
		echo $last_week."|".$today;
	}
}

//페널티,경고 리스트
if($mode == "backpenalty_list"){
	$sdate = $_POST['sdate'];
	$edate = $_POST['edate'];

	$where = "";
	if($sdate && $edate){
		$where = $where .= "and (DATE_FORMAT(p.updatetime, '%Y-%m-%d') >= '".$sdate."' and DATE_FORMAT(p.updatetime, '%Y-%m-%d') <= '".$edate."')";
	}

	$search = $_POST['search'];
	if($search){
		$where = $where .= " and (p.email like '%".$search."%' or p.name like '%".$search."%' or wc.company like '%".$search."%')";
	}

	$code = $_POST['code'];
	if($code=="all"){
		$where = $where .= "";
	}else{
		$where = $where .= " and p.".$code. "= '1'";
	}

	$kind = $_POST['kind'];
	$tclass = $_POST['tclass'];

	if($kind == "company"){
		$sort = $kind;
	}else{
		$sort = "p.idx";
	}

	if($tclass=="btn_sort_up"){
		$updown = " asc";
	}else{
		$updown = " desc";
	}

	$url = "backpenalty_list";
	$string = "&page=".$url;

	$p = $_POST['p']?$_POST['p']:$_GET['p'];
	if (!$p){
		$p = 1;
	}

	$list = $_POST['list'];
	$pagingsize = 5;					//페이징 사이즈
	if($list){
		$pagesize = $list;	
	}else{
		$pagesize = 15;
	}
	//페이지 출력갯수
	$startnum = 0;						//페이지 시작번호
	$endnum = $p * $pagesize;			//페이지 끝번호

	//시작번호
	if ($p == 1){
		$startnum = 0;
	}else{
		$startnum = ($p - 1) * $pagesize;
	}


	

	$sql = "select p.idx, p.email, p.companyno, p.state, p.name, p.incount, p.outcount, p.work, p.challenge, p.updatetime, wc.company from work_member_penalty as p, work_company as wc where p.companyno = wc.idx ".$where;
	$sql = $sql .= " order by ".$sort.$updown." limit ". $startnum.",".$pagesize;
	$query = selectAllQuery($sql);

	$sql = "select count(p.idx) as cnt from work_member_penalty as p, work_company as wc where p.companyno = wc.idx ".$where;
	$history_info_cnt = selectQuery($sql);
	if($history_info_cnt['cnt']){ 
		$total_count = $history_info_cnt['cnt'];
	}

	if($_POST['reset']=="re"){
		$sql = "select p.idx, p.email, p.companyno, p.state, p.name, p.incount, p.outcount, p.work, p.challenge, p.updatetime, wc.company from work_member_penalty as p, work_company as wc where p.companyno = wc.idx and (DATE_FORMAT(p.updatetime, '%Y-%m-%d') >= '".$last_week."' and DATE_FORMAT(p.updatetime, '%Y-%m-%d') <= '".$today."') ";
		$sql = $sql .= " order by p.idx desc limit ". $startnum.",".$pagesize;
		$query = selectAllQuery($sql);

		$where = "";
		$sql = "select count(p.idx) as cnt from work_member_penalty as p, work_company as wc where p.companyno = wc.idx and (DATE_FORMAT(p.updatetime, '%Y-%m-%d') >= '".$last_week."' and DATE_FORMAT(p.updatetime, '%Y-%m-%d') <= '".$today."')";
		$history_info_cnt = selectQuery($sql);
		if($history_info_cnt['cnt']){ 
			$total_count = $history_info_cnt['cnt'];
		}

		$sdate = $last_week;
		$edate = $today;
	}
		
	for($i=0; $i<count($query['idx']); $i++){
		if($query['incount'][$i]=='1'){
			$penalty_name = '출근 시간 미준수';
			$sql = "select sum(incount) as pen_cnt from work_member_penalty where incount = '1' and email = '".$query['email'][$i]."' and state = '0' ";
		}else if($query['outcount'][$i]=='1'){
			$penalty_name = '퇴근 소감 미작성';
			$sql = "select sum(outcount) as pen_cnt from work_member_penalty where outcount = '1' and email = '".$query['email'][$i]."' and state = '0' ";
		}else if($query['work'][$i]=='1'){
			$penalty_name = '오늘 업무 미작성';
			$sql = "select sum(work) as pen_cnt from work_member_penalty where work = '1' and email = '".$query['email'][$i]."' and state = '0' ";
		}else if($query['challenge'][$i]=='1'){
			$penalty_name = '챌린지 미참여';
			$sql = "select sum(challenge) as pen_cnt from work_member_penalty where challenge = '1' and email = '".$query['email'][$i]."' and state = '0' ";
		}
			$count = selectQuery($sql);
		$pen_count = $count['pen_cnt'];
		if($pen_count < 1){
			$pen_count = 0;
		}
	?>
	<tr>
		<td><?=$query['company'][$i]?></td>
		<td><?=$query['name'][$i]?></td>
		<td><?=$query['email'][$i]?></td>
		<td><?=$penalty_name?></td>
		<td><?=$pen_count?></td>
		<td class="backoff_memo"><p><?=$query['updatetime'][$i]?></p></td>
		<td><?=$query['state'][$i]=='0'?"활성화":"비활성화"?></td>
	</tr>
	<?}
		if(count($query['idx'])==0){?>
			<tr><td colspan="10">조회된 목록이 없습니다.</td></tr>
		<?}
	?>
	<input type="hidden" value="<?=$p?>" id="page_num" >
	<input type="hidden" value="<?=$pagesize?>" id="list_cnt">
	<input type="hidden" value="<?=$search?>" id="backoff_search">
	<input type="hidden" value="<?=$sdate?>" id="backoff_sdate">
	<input type="hidden" value="<?=$edate?>" id="backoff_edate">
	<input type="hidden" value="backpenalty_list" id="backoffice_type">
	<input type="hidden" value="<?=$tclass?>" id="tclass">
	<input type="hidden" value="<?=$kind?>" id="kind">
	<input type="hidden" value="<?=$code?>" id="code">|
	<?php echo back_pageing($pagingsize, $total_count, $pagesize, $string)?>
	<?echo "|".$tclass."|backpenalty|";
	if($_POST['reset']=="re"){
		echo $last_week."|".$today;
	}
}

//기업별 리스트
if($mode == "backcomp_list"){
	$where = "";

	$search = $_POST['search'];
	if($search){
		$where = $where .= " and company like '%".$search."%' ";
	}

	$kind = $_POST['kind'];
	$tclass = $_POST['tclass'];

	if($kind){
		$sort = $kind;
	}else{
		$sort = " idx";
	}

	if($tclass=="btn_sort_down"){
		$updown = " desc";
	}else{
		$updown = " asc";
	}

	$url = "backcomp_list";
	$string = "&page=".$url;

	$p = $_POST['p']?$_POST['p']:$_GET['p'];
	if (!$p){
		$p = 1;
	}

	$list = $_POST['list'];
	$pagingsize = 5;					//페이징 사이즈
	if($list){
		$pagesize = $list;	
	}else{
		$pagesize = 5;
	}									//페이지 출력갯수
	$startnum = 0;						//페이지 시작번호
	$endnum = $p * $pagesize;			//페이지 끝번호

	//시작번호
	if ($p == 1){
		$startnum = 0;
	}else{
		$startnum = ($p - 1) * $pagesize;
	}

	$sql = "select idx, state, company, comcoin, regdate, penalty, intime, outtime,";
	$sql = $sql .= " (select count(1) from work_member where companyno = work_company.idx and state = '0') as usercnt";
	$sql = $sql .= " from work_company where state in ('0','9')".$where;
	$sql = $sql .= " order by ".$sort.$updown." limit ". $startnum.",".$pagesize;
	$query = selectAllQuery($sql);

	$sql = "select count(idx) as cnt from work_company where state in('0','9') ".$where;
	$history_info_cnt = selectQuery($sql);
	if($history_info_cnt['cnt']){ 
	   $total_count = $history_info_cnt['cnt'];
	}

	for($i=0; $i<count($query['idx']); $i++){
		?>
		<tr>
			<td><?=$query['company'][$i]?></td>
			<td><?=$query['idx'][$i]?></td>
			<td><?=$query['usercnt'][$i]?></td>
			<td><?=$query['regdate'][$i]?></td>
			<td><?=number_format($query['comcoin'][$i])?></td>
		</tr>
	<?}
		if(count($query['idx'])==0){?>
			<tr><td colspan="6">조회된 목록이 없습니다.</td></tr>
		<?}
	?>
	<input type="hidden" id="page_num" value="<?=$p?>">
	<input type="hidden" value="<?=$pagesize?>" id="list_cnt" >
	<input type="hidden" value="<?=$search?>" id="backoff_search">
	<input type="hidden" value="backcomp_list" id="backoffice_type">
	<input type="hidden" value="<?=$tclass?>" id="tclass">
	<input type="hidden" value="<?=$kind?>" id="kind">|
		<?php echo back_pageing($pagingsize, $total_count, $pagesize, $string)?>
	<? echo "|".$tclass."|backcomp|";
}


// 에러 로그
if($mode == "backerror_log"){ 
	$sdate = $_POST['sdate'];
	$edate = $_POST['edate'];

	$where = "";
	if($sdate && $edate){
		$where = $where .= " and (regdate >= '".$sdate."' and regdate <= '".$edate."')";
	}

	$search = $_POST['search'];
	if($search){
		$where = $where .= " and (query like '%".$search."%' or host like '%".$search."%' or page like '%".$search."%')";
	}

	$code = $_POST['code'];
	if($code=="all"){
		$where = $where .= " ";
	}else{
		$where = $where .= " and kind = '".$code."' ";
	}

	$kind = $_POST['kind'];
	$tclass = $_POST['tclass'];

	if($kind){
		$sort = $kind;
	}else{
		$sort = " idx";
	}

	if($tclass=="btn_sort_up"){
		$updown = " asc";
	}else{
		$updown = " desc";
	}

	$url = "backerror_log";
	$string = "&page=".$url;

	$p = $_POST['p']?$_POST['p']:$_GET['p'];
	if (!$p){
		$p = 1;
	}

	$list = $_POST['list'];
	$pagingsize = 5;					//페이징 사이즈
	if($list){
		$pagesize = $list;	
	}else{
		$pagesize = 15;
	}
	//페이지 출력갯수
	$startnum = 0;						//페이지 시작번호
	$endnum = $p * $pagesize;			//페이지 끝번호

	//시작번호
	if ($p == 1){
		$startnum = 0;
	}else{
		$startnum = ($p - 1) * $pagesize;
	}

	$sql = "select idx, regdate, code, query, kind, host, page from work_page_error_list where state = '0' ".$where; 
	$sql = $sql .= " order by ".$sort.$updown." limit ". $startnum.",".$pagesize;
	$query = selectAllQuery($sql);

	$sql = "select count(idx) as cnt from work_page_error_list where state = '0' ".$where ;
	$history_info_cnt = selectQuery($sql);
	if($history_info_cnt['cnt']){ 
		$total_count = $history_info_cnt['cnt'];
	}


	if($_POST['reset']=="re"){
		$sql = "select idx, regdate, code, query, kind, host, page from work_page_error_list where regdate >='".$last_week."' and regdate <= '".$today."'  order by idx desc limit 0,15";
		$query = selectAllQuery($sql);

		$where = "";
		$sql = "select count(*) as cnt from work_page_error_list where regdate >='".$last_week."' and regdate <= '".$today."'";
		$history_info_cnt = selectQuery($sql);
		if($history_info_cnt['cnt']){ 
			$total_count = $history_info_cnt['cnt'];
		}

		$sdate = $last_week;
		$edate = $today;
	}
		
		for($i=0; $i<count($query['idx']); $i++){
			$no = ($p-1)*$pagesize+($i+1);
			?>
			<tr>
				<td><?=$no?></td>
				<!-- <td class="backoff_query"><p><?=$query['query'][$i]?></p></td> -->
				<td><?=$query['query'][$i]?></td>
				<td><?=$query['kind'][$i]?></td>
				<td><?=$query['page'][$i]?></td>
				<td><?=$query['host'][$i]?></td>
				<td><?=$query['regdate'][$i]?></td>
			</tr>
		<?}
			if(count($query['idx'])==0){?>
				<tr><td colspan="6">조회된 목록이 없습니다.</td></tr>
			<?}
		?>
		<input type="hidden" value="<?=$p?>" id="page_num">
		<input type="hidden" value="<?=$pagesize?>" id="list_cnt">
		<input type="hidden" value="<?=$search?>" id="backoff_search">
		<input type="hidden" value="<?=$sdate?>" id="backoff_sdate">
		<input type="hidden" value="<?=$edate?>" id="backoff_edate">
		<input type="hidden" value="backerror_log" id="backoffice_type">
		<input type="hidden" value="<?=$tclass?>" id="tclass">
		<input type="hidden" value="<?=$kind?>" id="kind">
		<input type="hidden" value="<?=$code?>" id="code">|
		<?php echo back_pageing($pagingsize, $total_count, $pagesize, $string)?>
		<?echo "|".$tclass."|backerror_log|";
		if($_POST['reset']=="re"){
			echo $last_week."|".$today;
		}
	}

// work_data_log
if($mode == "backlog_list"){ 
	$sdate = $_POST['sdate'];
	$edate = $_POST['edate'];

	$where = "";
	if($sdate && $edate){
		$where = $where .= " and (workdate >= '".$sdate."' and workdate <= '".$edate."')";
	}

	$search = $_POST['search'];
	if($search){
		$where = $where .= " and (email like '%".$search."%' or memo like '%".$search."%')";
	}

	$code = $_POST['code'];
	if($code=="all"){
		$where = $where .= " ";
	}else{
		$where = $where .= " and code in (".$code.") ";
	}

	$kind = $_POST['kind'];
	$tclass = $_POST['tclass'];


	$sort = " idx";

	if($tclass=="btn_sort_up"){
		$updown = " asc";
	}else{
		$updown = " desc";
	}

	$url = "backlog_list";
	$string = "&page=".$url;

	$p = $_POST['p']?$_POST['p']:$_GET['p'];
	if (!$p){
		$p = 1;
	}

	$list = $_POST['list'];
	$pagingsize = 5;					//페이징 사이즈
	if($list){
		$pagesize = $list;	
	}else{
		$pagesize = 15;
	}
	//페이지 출력갯수
	$startnum = 0;						//페이지 시작번호
	$endnum = $p * $pagesize;			//페이지 끝번호

	//시작번호
	if ($p == 1){
		$startnum = 0;
	}else{
		$startnum = ($p - 1) * $pagesize;
	}

	$sql = "select idx, state, code, work_idx, email, name, memo, ip, workdate, regdate, companyno from work_data_log";
	$sql = $sql .= " where state in (0,1) ".$where;
	$sql = $sql .= " order by ".$sort.$updown." limit ". $startnum.",".$pagesize;
	$query = selectAllQuery($sql);

	$sql = "select count(1) as cnt from work_data_log where state in (0,1) ".$where ;
	$history_info_cnt = selectQuery($sql);
	if($history_info_cnt['cnt']){ 
		$total_count = $history_info_cnt['cnt'];
	}


	if($_POST['reset']=="re"){
		$sql = "select idx, state, code, work_idx, email, name, memo, ip, workdate, regdate, companyno from work_data_log where workdate >='".$last_week."' and workdate <= '".$today."'  order by idx desc limit 0,15";
		$query = selectAllQuery($sql);

		$where = "";
		$sql = "select count(1) as cnt from work_data_log where workdate >='".$last_week."' and workdate <= '".$today."'";
		$history_info_cnt = selectQuery($sql);
		if($history_info_cnt['cnt']){ 
			$total_count = $history_info_cnt['cnt'];
		}

		$sdate = $last_week;
		$edate = $today;
	}
		
		for($i=0; $i<count($query['idx']); $i++){
			$no = ($p-1)*$pagesize+($i+1);
			?>
			<tr>
				<td><?=$i+1?></td>
				<td><?=$query['work_idx'][$i]?></td>
				<td><?=$query['email'][$i]?></td>
				<td><?=$query['code'][$i]?></td>
				<td><?=$query['memo'][$i]?></td>
				<td><?=$query['ip'][$i]?></td>
				<td><?=$query['regdate'][$i]?></td>
				<td><?=$query['companyno'][$i]?></td>
			</tr>
		<?}
			if(count($query['idx'])==0){?>
				<tr><td colspan="6">조회된 목록이 없습니다.</td></tr>
			<?}
		?>
		<input type="hidden" value="<?=$p?>" id="page_num">
		<input type="hidden" value="<?=$pagesize?>" id="list_cnt">
		<input type="hidden" value="<?=$search?>" id="backoff_search">
		<input type="hidden" value="<?=$sdate?>" id="backoff_sdate">
		<input type="hidden" value="<?=$edate?>" id="backoff_edate">
		<input type="hidden" value="backlog_list" id="backoffice_type">
		<input type="hidden" value="<?=$tclass?>" id="tclass">
		<input type="hidden" value="<?=$kind?>" id="kind">
		<input type="hidden" value="<?=$code?>" id="code">|
		<?php echo back_pageing($pagingsize, $total_count, $pagesize, $string)?>
		<?echo "|".$tclass."|backlog_list|";
		if($_POST['reset']=="re"){
			echo $last_week."|".$today;
		}
	}

// 알람 리스트
if($mode == "backalarm_list"){ 
	$sdate = $_POST['sdate'];
	$edate = $_POST['edate'];

	$where = "";
	if($sdate && $edate){
		$where = $where .= " and (wa.workdate >= '".$sdate."' and wa.workdate <= '".$edate."')";
	}

	$search = $_POST['search'];
	if($search){
		$where = $where .= " and (wa.email like '%".$search."%' or wa.title like '%".$search."%' or wa.contents like '%".$search."%' or wa.service_name like '%".$search."%')";
	}

	$code = $_POST['code'];
	if($code=="all"){
		$where = $where .= " ";
	}else{
		$where = $where .= " and wa.service = '".$code."'";
	}

	$kind = $_POST['kind'];
	$tclass = $_POST['tclass'];

	if($kind){
		$sort = "wa.".$kind;
	}else{
		$sort = " wa.idx";
	}

	$sort = " wa.idx";

	if($tclass=="btn_sort_up"){
		$updown = " asc";
	}else{
		$updown = " desc";
	}

	$url = "backalarm_list";
	$string = "&page=".$url;

	$p = $_POST['p']?$_POST['p']:$_GET['p'];
	if (!$p){
		$p = 1;
	}

	$list = $_POST['list'];
	$pagingsize = 5;					//페이징 사이즈
	if($list){
		$pagesize = $list;	
	}else{
		$pagesize = 15;
	}
	//페이지 출력갯수
	$startnum = 0;						//페이지 시작번호
	$endnum = $p * $pagesize;			//페이지 끝번호

	//시작번호
	if ($p == 1){
		$startnum = 0;
	}else{
		$startnum = ($p - 1) * $pagesize;
	}


	$sql = "select wa.idx, wa.state, wa.service_name, wa.title, wa.contents, wa.regdate, wa.workdate, wa.email, wm.name, wm.company from work_alarm as wa, work_member as wm";
	$sql = $sql .= " where wa.email = wm.email ".$where;
	$sql = $sql .= " order by ".$sort.$updown." limit ".$startnum.",".$pagesize;
	$query = selectAllQuery($sql);

	$sql = "select count(1) as cnt from work_alarm as wa, work_member as wm where wa.email = wm.email ".$where ;
	$history_info_cnt = selectQuery($sql);
	if($history_info_cnt['cnt']){ 
		$total_count = $history_info_cnt['cnt'];
	}


	if($_POST['reset']=="re"){
		$sql = "select wa.idx, wa.state, wa.service_name, wa.title, wa.contents, wa.regdate, wa.workdate, wa.email, wm.name, wm.company from work_alarm as wa, work_member as wm";
		$sql = $sql .= " where wa.email = wm.email and workdate >='".$last_week."' and workdate <= '".$today."'";
		$sql = $sql .= " order by idx desc limit 0,15";
		
		$query = selectAllQuery($sql);

		$where = "";
		$sql = "select count(1) as cnt from work_alarm where (workdate >= '".$last_week."' and workdate <= '".$today."')";
		$history_info_cnt = selectQuery($sql);
		if($history_info_cnt['cnt']){ 
			$total_count = $history_info_cnt['cnt'];
		}

		$sdate = $last_week;
		$edate = $today;
	}
		
		for($i=0; $i<count($query['idx']); $i++){
			?>
			<tr>
				<td><?=$query['regdate'][$i]?></td>
				<td><?=$query['service_name'][$i]?></td>
				<td><?=$query['email'][$i]?></td>
				<td><?=$query['title'][$i]?></td>
				<td class="al_contents"><p><?=$query['contents'][$i]?></p></td>
				<td><?=$query['company'][$i]?></td>
			</tr>
		<?}
			if(count($query['idx'])==0){?>
				<tr><td colspan="6">조회된 목록이 없습니다.</td></tr>
			<?}
		?>
		<input type="hidden" value="<?=$p?>" id="page_num">
		<input type="hidden" value="<?=$pagesize?>" id="list_cnt">
		<input type="hidden" value="<?=$search?>" id="backoff_search">
		<input type="hidden" value="<?=$sdate?>" id="backoff_sdate">
		<input type="hidden" value="<?=$edate?>" id="backoff_edate">
		<input type="hidden" value="backalarm_list" id="backoffice_type">
		<input type="hidden" value="<?=$tclass?>" id="tclass">
		<input type="hidden" value="<?=$kind?>" id="kind">
		<input type="hidden" value="<?=$code?>" id="code">|
		<?php echo back_pageing($pagingsize, $total_count, $pagesize, $string)?>
		<?echo "|".$tclass."|backalarm_list|";
		if($_POST['reset']=="re"){
			echo $last_week."|".$today;
		}
	}

// 브로슈어 공지사항 리스트
if($mode == "backnote_list"){ 
	$where = "";

	$search = $_POST['search'];
	if($search){
		$where = $where .= " and (wm.name like '%".$search."%' or bn.title like '%".$search."%')";
	}

	$code = $_POST['code'];
	if($code=="all"){
		$where = $where .= " and bn.state in (0,1) ";
	}else{
		$where = $where .= " and bn.state = '".$code."'";
	}

	$kind = $_POST['kind'];
	$tclass = $_POST['tclass'];

	if($kind){
		$sort = " bn.".$kind;
	}else{
		$sort = " bn.idx";
	}

	if($tclass=="btn_sort_up"){
		$updown = " asc";
	}else{
		$updown = " desc";
	}

	$url = "backnote_list";
	$string = "&page=".$url;

	$p = $_POST['p']?$_POST['p']:$_GET['p'];
	if (!$p){
		$p = 1;
	}

	$list = $_POST['list'];
	$pagingsize = 5;					//페이징 사이즈
	if($list){
		$pagesize = $list;	
	}else{
		$pagesize = 15;
	}
	//페이지 출력갯수
	$startnum = 0;						//페이지 시작번호
	$endnum = $p * $pagesize;			//페이지 끝번호

	//시작번호
	if ($p == 1){
		$startnum = 0;
	}else{
		$startnum = ($p - 1) * $pagesize;
	}


	$sql = "select bn.idx, bn.email, bn.title, bn.regdate, bn.state, wm.name from bro_notice as bn, work_member as wm";
	$sql = $sql .= " where bn.email = wm.email ".$where;
	$sql = $sql .= " order by ".$sort.$updown." limit ".$startnum.",".$pagesize;
	$query = selectAllQuery($sql);

	$sql = "select count(1) as cnt from bro_notice as bn, work_member as wm where bn.email = wm.email ".$where ;
	$history_info_cnt = selectQuery($sql);
	if($history_info_cnt['cnt']){ 
		$total_count = $history_info_cnt['cnt'];
	}
		
		for($i=0; $i<count($query['idx']); $i++){
			$idx = $query['idx'][$i];
			?>
			<tr class="backnote_<?=$idx?>">
				<td><?=$i+$startnum+1?></td>
				<td class="title_list" id="backnote_<?=$idx?>"><?=$query['title'][$i]?></td>
				<td>
					<div class="btn-group btn-group-sm border border-dark" id="btn_group_<?=$idx?>" role="group" aria-label="">
						<button class="btn <?=$query['state'][$i]=='1'?"btn-dark":"btn-outline-dark"?>" id="btnradio_1" value="<?=$idx?>">노출</button>
						<button class="btn <?=$query['state'][$i]=='0'?"btn-dark":"btn-outline-dark"?>" id="btnradio_0" value="<?=$idx?>">미노출</button>
					</div>
				</td>
				<td><?=$query['name'][$i]?></td>
				<td><?=$query['regdate'][$i]?></td>
				<td>
					<? if($code == "9"){?>
						<button type="button" class="btn btn-outline-dark btn-sm" id="noteres_<?=$idx?>"><i class="fa-solid fa-rotate-right"></i></button>
					<?}else{?>
						<button type="button" class="btn btn-outline-dark btn-sm" id="notedel_<?=$idx?>"><i class="fa-solid fa-trash-can"></i></button>
					<?}?>					
				</td>
			</tr>
		<?}
			if(count($query['idx'])==0){?>
				<tr><td colspan="6">조회된 목록이 없습니다.</td></tr>
			<?}
		?>
		<input type="hidden" value="<?=$p?>" id="page_num">
		<input type="hidden" value="<?=$pagesize?>" id="list_cnt">
		<input type="hidden" value="<?=$search?>" id="backoff_search">
		<input type="hidden" value="backnote_list" id="backoffice_type">
		<input type="hidden" value="notice" id="bro_view">
		<input type="hidden" value="<?=$tclass?>" id="tclass">
		<input type="hidden" value="<?=$kind?>" id="kind">
		<input type="hidden" value="<?=$code?>" id="code">|
		<?php echo back_pageing($pagingsize, $total_count, $pagesize, $string)?>
		<?echo "|".$tclass."|backnote_list|";
	}

// 브로슈어 FAQs 리스트
if($mode == "backfaq_list"){ 

	$where = "";

	$search = $_POST['search'];
	if($search){
		$where = $where .= " and (wm.name like '%".$search."%' or bf.title like '%".$search."%')";
	}

	$code = $_POST['code'];
	if($code=="all"){
		$where = $where .= " and bf.state in (0,1) ";
	}else{
		$where = $where .= " and bf.state = '".$code."'";
	}

	$kind = $_POST['kind'];
	$tclass = $_POST['tclass'];

	if($kind){
		$sort = " bf.".$kind;
	}else{
		$sort = " bf.idx";
	}

	if($tclass=="btn_sort_up"){
		$updown = " asc";
	}else{
		$updown = " desc";
	}

	$url = "backfaq_list";
	$string = "&page=".$url;

	$p = $_POST['p']?$_POST['p']:$_GET['p'];
	if (!$p){
		$p = 1;
	}

	$list = $_POST['list'];
	$pagingsize = 5;					//페이징 사이즈
	if($list){
		$pagesize = $list;	
	}else{
		$pagesize = 15;
	}
	//페이지 출력갯수
	$startnum = 0;						//페이지 시작번호
	$endnum = $p * $pagesize;			//페이지 끝번호

	//시작번호
	if ($p == 1){
		$startnum = 0;
	}else{
		$startnum = ($p - 1) * $pagesize;
	}


	$sql = "select bf.idx, bf.email, bf.title, bf.regdate, bf.state, wm.name from bro_faq as bf, work_member as wm";
	$sql = $sql .= " where bf.email = wm.email ".$where;
	$sql = $sql .= " order by ".$sort.$updown." limit ".$startnum.",".$pagesize;
	$query = selectAllQuery($sql);

	$sql = "select count(1) as cnt from bro_notice as bf, work_member as wm where bf.email = wm.email ".$where ;
	$history_info_cnt = selectQuery($sql);
	if($history_info_cnt['cnt']){ 
		$total_count = $history_info_cnt['cnt'];
	}
		
		for($i=0; $i<count($query['idx']); $i++){
			$idx = $query['idx'][$i];
			?>
			<tr class="backfaq_<?=$idx?>">
				<td><?=$i+$startnum+1?></td>
				<td class="title_list" id="backnote_<?=$idx?>"><?=$query['title'][$i]?></td>
				<td>
					<div class="btn-group btn-group-sm border border-dark" id="btn_group_<?=$idx?>" role="group" aria-label="">
						<button class="btn <?=$query['state'][$i]=='1'?"btn-dark":"btn-outline-dark"?>" id="btnradio_1" value="<?=$idx?>">노출</button>
						<button class="btn <?=$query['state'][$i]=='0'?"btn-dark":"btn-outline-dark"?>" id="btnradio_0" value="<?=$idx?>">미노출</button>
					</div>
				</td>
				<td><?=$query['name'][$i]?></td>
				<td><?=$query['regdate'][$i]?></td>
				<td>
					<? if($code == "9"){?>
						<button type="button" class="btn btn-outline-dark btn-sm" id="noteres_<?=$idx?>"><i class="fa-solid fa-rotate-right"></i></button>
					<?}else{?>
						<button type="button" class="btn btn-outline-dark btn-sm" id="notedel_<?=$idx?>"><i class="fa-solid fa-trash-can"></i></button>
					<?}?>					
				</td>
			</tr>
		<?}
			if(count($query['idx'])==0){?>
				<tr><td colspan="6">조회된 목록이 없습니다.</td></tr>
			<?}
		?>
		<input type="hidden" value="<?=$p?>" id="page_num">
		<input type="hidden" value="<?=$pagesize?>" id="list_cnt">
		<input type="hidden" value="<?=$search?>" id="backoff_search">
		<input type="hidden" value="backfaq_list" id="backoffice_type">
		<input type="hidden" id="bro_view" value="faq">
		<input type="hidden" value="<?=$tclass?>" id="tclass">
		<input type="hidden" value="<?=$kind?>" id="kind">
		<input type="hidden" value="<?=$code?>" id="code">|
		<?php echo back_pageing($pagingsize, $total_count, $pagesize, $string)?>
		<?echo "|".$tclass."|backfaq_list|";
	}
	
	// 브로슈어 활용사례 리스트
	if($mode == "backsample_list"){ 

		$where = "";
	
		$search = $_POST['search'];
		if($search){
			$where = $where .= " and (wm.name like '%".$search."%' or bs.title like '%".$search."%')";
		}
	
		$code = $_POST['code'];
		if($code=="all"){
			$where = $where .= " and bs.state in (0,1) ";
		}else{
			$where = $where .= " and bs.state = '".$code."'";
		}
	
		$kind = $_POST['kind'];
		$tclass = $_POST['tclass'];
	
		if($kind){
			$sort = " bs.".$kind;
		}else{
			$sort = " bs.idx";
		}
	
		if($tclass=="btn_sort_up"){
			$updown = " asc";
		}else{
			$updown = " desc";
		}
	
		$url = "backsample_list";
		$string = "&page=".$url;
	
		$p = $_POST['p']?$_POST['p']:$_GET['p'];
		if (!$p){
			$p = 1;
		}
	
		$list = $_POST['list'];
		$pagingsize = 5;					//페이징 사이즈
		if($list){
			$pagesize = $list;	
		}else{
			$pagesize = 15;
		}
		//페이지 출력갯수
		$startnum = 0;						//페이지 시작번호
		$endnum = $p * $pagesize;			//페이지 끝번호
	
		//시작번호
		if ($p == 1){
			$startnum = 0;
		}else{
			$startnum = ($p - 1) * $pagesize;
		}
	
	
		$sql = "select bs.idx, bs.email, bs.title, bs.regdate, bs.state, wm.name from bro_sample as bs, work_member as wm";
		$sql = $sql .= " where bs.email = wm.email ".$where;
		$sql = $sql .= " order by ".$sort.$updown." limit ".$startnum.",".$pagesize;
		$query = selectAllQuery($sql);
	
		$sql = "select count(1) as cnt from bro_sample as bs, work_member as wm where bs.email = wm.email ".$where ;
		$history_info_cnt = selectQuery($sql);
		if($history_info_cnt['cnt']){ 
			$total_count = $history_info_cnt['cnt'];
		}
			
			for($i=0; $i<count($query['idx']); $i++){
				$idx = $query['idx'][$i];
				?>
				<tr class="backfaq_<?=$idx?>">
					<td><?=$i+$startnum+1?></td>
					<td class="title_list" id="backnote_<?=$idx?>"><?=$query['title'][$i]?></td>
					<td>
						<div class="btn-group btn-group-sm border border-dark" id="btn_group_<?=$idx?>" role="group" aria-label="">
							<button class="btn <?=$query['state'][$i]=='1'?"btn-dark":"btn-outline-dark"?>" id="btnradio_1" value="<?=$idx?>">노출</button>
							<button class="btn <?=$query['state'][$i]=='0'?"btn-dark":"btn-outline-dark"?>" id="btnradio_0" value="<?=$idx?>">미노출</button>
						</div>
					</td>
					<td><?=$query['name'][$i]?></td>
					<td><?=$query['regdate'][$i]?></td>
					<td>
						<? if($code == "9"){?>
							<button type="button" class="btn btn-outline-dark btn-sm" id="noteres_<?=$idx?>"><i class="fa-solid fa-rotate-right"></i></button>
						<?}else{?>
							<button type="button" class="btn btn-outline-dark btn-sm" id="notedel_<?=$idx?>"><i class="fa-solid fa-trash-can"></i></button>
						<?}?>					
					</td>
				</tr>
			<?}
				if(count($query['idx'])==0){?>
					<tr><td colspan="6">조회된 목록이 없습니다.</td></tr>
				<?}
			?>
			<input type="hidden" value="<?=$p?>" id="page_num">
			<input type="hidden" value="<?=$pagesize?>" id="list_cnt">
			<input type="hidden" value="<?=$search?>" id="backoff_search">
			<input type="hidden" value="backsample_list" id="backoffice_type">
			<input type="hidden" id="bro_view" value="sample">
			<input type="hidden" value="<?=$tclass?>" id="tclass">
			<input type="hidden" value="<?=$kind?>" id="kind">
			<input type="hidden" value="<?=$code?>" id="code">|
			<?php echo back_pageing($pagingsize, $total_count, $pagesize, $string)?>
			<?echo "|".$tclass."|backsample_list|";
		}

	// 브로슈어 사용자매뉴얼 리스트
if($mode == "backmanual_list"){ 

	$where = "";

	$search = $_POST['search'];
	if($search){
		$where = $where .= " and (wm.name like '%".$search."%' or bm.title like '%".$search."%')";
	}

	$code = $_POST['code'];
	if($code=="all"){
		$where = $where .= " and bm.state in (0,1) ";
	}else{
		$where = $where .= " and bm.state = '".$code."'";
	}

	$kind = $_POST['kind'];
	$tclass = $_POST['tclass'];

	if($kind){
		$sort = " bm.".$kind;
	}else{
		$sort = " bm.idx";
	}

	if($tclass=="btn_sort_up"){
		$updown = " asc";
	}else{
		$updown = " desc";
	}

	$url = "backmanual_list";
	$string = "&page=".$url;

	$p = $_POST['p']?$_POST['p']:$_GET['p'];
	if (!$p){
		$p = 1;
	}

	$list = $_POST['list'];
	$pagingsize = 5;					//페이징 사이즈
	if($list){
		$pagesize = $list;	
	}else{
		$pagesize = 15;
	}
	//페이지 출력갯수
	$startnum = 0;						//페이지 시작번호
	$endnum = $p * $pagesize;			//페이지 끝번호

	//시작번호
	if ($p == 1){
		$startnum = 0;
	}else{
		$startnum = ($p - 1) * $pagesize;
	}


	$sql = "select bm.idx, bm.email, bm.title, bm.regdate, bm.state, wm.name from bro_manual as bm, work_member as wm";
	$sql = $sql .= " where bm.email = wm.email ".$where;
	$sql = $sql .= " order by ".$sort.$updown." limit ".$startnum.",".$pagesize;
	$query = selectAllQuery($sql);

	$sql = "select count(1) as cnt from bro_manual as bm, work_member as wm where bm.email = wm.email ".$where ;
	$history_info_cnt = selectQuery($sql);
	if($history_info_cnt['cnt']){ 
		$total_count = $history_info_cnt['cnt'];
	}
		
		for($i=0; $i<count($query['idx']); $i++){
			$idx = $query['idx'][$i];
			?>
			<tr class="backfaq_<?=$idx?>">
				<td><?=$i+$startnum+1?></td>
				<td class="title_list" id="backnote_<?=$idx?>"><?=$query['title'][$i]?></td>
				<td>
					<div class="btn-group btn-group-sm border border-dark" id="btn_group_<?=$idx?>" role="group" aria-label="">
						<button class="btn <?=$query['state'][$i]=='1'?"btn-dark":"btn-outline-dark"?>" id="btnradio_1" value="<?=$idx?>">노출</button>
						<button class="btn <?=$query['state'][$i]=='0'?"btn-dark":"btn-outline-dark"?>" id="btnradio_0" value="<?=$idx?>">미노출</button>
					</div>
				</td>
				<td><?=$query['name'][$i]?></td>
				<td><?=$query['regdate'][$i]?></td>
				<td>
					<? if($code == "9"){?>
						<button type="button" class="btn btn-outline-dark btn-sm" id="noteres_<?=$idx?>"><i class="fa-solid fa-rotate-right"></i></button>
					<?}else{?>
						<button type="button" class="btn btn-outline-dark btn-sm" id="notedel_<?=$idx?>"><i class="fa-solid fa-trash-can"></i></button>
					<?}?>					
				</td>
			</tr>
		<?}
			if(count($query['idx'])==0){?>
				<tr><td colspan="6">조회된 목록이 없습니다.</td></tr>
			<?}
		?>
		<input type="hidden" value="<?=$p?>" id="page_num">
		<input type="hidden" value="<?=$pagesize?>" id="list_cnt">
		<input type="hidden" value="<?=$search?>" id="backoff_search">
		<input type="hidden" value="backmanual_list" id="backoffice_type">
		<input type="hidden" id="bro_view" value="manual">
		<input type="hidden" value="<?=$tclass?>" id="tclass">
		<input type="hidden" value="<?=$kind?>" id="kind">
		<input type="hidden" value="<?=$code?>" id="code">|
		<?php echo back_pageing($pagingsize, $total_count, $pagesize, $string)?>
		<?echo "|".$tclass."|backmanual_list|";
	}

//백오피스 파티 리스트
if($mode == "work_total"){
	$sdate = $_POST['sdate'];
	$edate = $_POST['edate'];

	$where = "";
	$code = $_POST['code'];
	if($code == "work"){
		$where = $where .= " and work_flag = '2' and share_flag = '0'";
	}else if($code == "share"){
		$where = $where .= " and work_flag = '2' and share_flag = '1'";
	}else if($code == "request"){
		$where = $where .= " and work_flag = '3'";
	}else if($code == "report"){
		$where = $where .= " and work_flag = '1'";
	}else if($code == "all"){
		$where = " and share_flag in ('0','1') and work_idx is null";
	}

	//일간 막대그래프
    $sql = "select count(workdate) as cnt, workdate from work_todaywork where state = '0' and workdate >= '".$sdate."' and workdate <= '".$edate."' ".$where." group by workdate order by workdate desc ";
    $daycount = selectAllQuery($sql);

	$reset = $_POST['reset'];
	if($reset == "re"){
		//일간 막대그래프
		$sql = "select count(workdate) as cnt, workdate from work_todaywork where state = '0' and workdate >= '".$last_week."' and workdate <= '".$today."' ".$where." group by workdate order by workdate desc ";
		$daycount = selectAllQuery($sql);

		$sdate = $last_week;
		$edate = $today;
	}	

	 for($i=0;$i<count($daycount['workdate']);$i++){?>
	<input type="hidden" class="bar_cnt_<?=$i?>" value="<?=$daycount['cnt'][$i]?>"> 
	<input type="hidden" class="bar_today_<?=$i?>" value="<?=$daycount['workdate'][$i]?>">
	<?}?>
	<input type="hidden" id="code" value="<?=$code?>">
	<input type="hidden" id="backcoin_sdate" value="<?=$sdate?>">
	<input type="hidden" id="backcoin_edate" value="<?=$edate?>">
	<?
	$sql = "select count(idx) as cnt from work_todaywork where state = '0' and work_flag = '2' and share_flag = '0' and (workdate >= '".$sdate."' and workdate <= '".$edate."') ";
	$query = selectQuery($sql);
	$work = $query['cnt'];

	//오늘 보고 수치
	$sql = "select count(idx) as cnt from work_todaywork where state = '0' and work_flag = '1' and work_idx is null and (workdate >= '".$sdate."' and workdate <= '".$edate."') ";
	$query = selectQuery($sql);
	$report = $query['cnt'];

	//오늘 요청 수치
	$sql = "select count(idx) as cnt from work_todaywork where state = '0' and work_flag = '3' and work_idx is null and (workdate >= '".$sdate."' and workdate <= '".$edate."') ";
	$query = selectQuery($sql);
	$request = $query['cnt'];

	//오늘 공유 수치
	$sql = "select count(idx) as cnt from work_todaywork where state = '0' and work_flag = '2' and share_flag = '1' and (workdate >= '".$sdate."' and workdate <= '".$edate."') ";
	$query = selectQuery($sql);
	$share = $query['cnt'];

	?>
	|<input type="hidden" id="pie_value_0" value="<?=$work?>">
	<input type="hidden" id="pie_value_1" value="<?=$report?>">
	<input type="hidden" id="pie_value_2" value="<?=$request?>">
	<input type="hidden" id="pie_value_3" value="<?=$share?>">|
		<td><?=$sdate." ~ ".$edate?></td>
		<td><?=number_format($work)?></td>
		<td><?=number_format($report)?></td>
		<td><?=number_format($request)?></td>
		<td><?=number_format($share)?></td>
		<td><?=number_format($week_total)?></td>|
	<?if($_POST['reset']=="re"){
			echo $sdate."|".$edate;
	}
}
if($mode == "like_total"){
	$sdate = $_POST['sdate'];
	$edate = $_POST['edate'];

	//날짜 수치로 변환(사용중지 09.08)
	// $edate_format = strtotime($edate);
    // $date1 = new DateTime($sdate);
    // $date2 = new DateTime($edate);
    // $interval = $date2->diff($date1);
    // $date_cnt = $interval->days;

	$where = "";
	$code = $_POST['code'];
	if($code == "main"){
		$where = $where .= " and service = 'main' ";
	}else if($code == "live"){
		$where = $where .= " and service = 'live' ";
	}else if($code == "memo"){
		$where = $where .= " and service = 'memo' ";
	}else if($code == "party"){
		$where = $where .= " and service = 'party' ";
	}else if($code == "work"){
		$where = $where .= " and service = 'work' ";
	}else if($code == "all"){
		$where = "";
	}

	//일간 막대그래프
    $sql = "select count(workdate) as cnt, workdate from work_todaywork_like where state = '0' and workdate >= '".$sdate."' and workdate <= '".$edate."' ".$where." group by workdate order by workdate desc ";
    $daycount = selectAllQuery($sql);

	 for($i=0;$i<count($daycount['workdate']);$i++){?>
	<input type="hidden" class="bar_cnt_<?=$i?>" value="<?=$daycount['cnt'][$i]?>"> 
	<input type="hidden" class="bar_today_<?=$i?>" value="<?=$daycount['workdate'][$i]?>">
	<?}?>
	<input type="hidden" id="code" value="<?=$code?>">
	<input type="hidden" id="backcoin_sdate" value="<?=$sdate?>">
	<input type="hidden" id="backcoin_edate" value="<?=$edate?>">
	<?
	//날짜별 원형 그래프(메인)
	$sql = "select count(idx) as cnt from work_todaywork_like where state = '0' and service = 'main' and (workdate >= '".$sdate."' and workdate <= '".$edate."') ";
	$query = selectQuery($sql);
	$main = $query['cnt'];

	//날짜별 원형 그래프(라이브)
	$sql = "select count(idx) as cnt from work_todaywork_like where state = '0' and service = 'live' and (workdate >= '".$sdate."' and workdate <= '".$edate."') ";
	$query = selectQuery($sql);
	$live = $query['cnt'];

	//날짜별 원형 그래프(메모)
	$sql = "select count(idx) as cnt from work_todaywork_like where state = '0' and service = 'memo' and (workdate >= '".$sdate."' and workdate <= '".$edate."') ";
	$query = selectQuery($sql);
	$memo = $query['cnt'];

	//날짜별 원형 그래프(파티)
	$sql = "select count(idx) as cnt from work_todaywork_like where state = '0' and service = 'party' and (workdate >= '".$sdate."' and workdate <= '".$edate."') ";
	$query = selectQuery($sql);
	$project = $query['cnt'];

	//날짜별 원형 그래프(업무)
	$sql = "select count(idx) as cnt from work_todaywork_like where state = '0' and service = 'work' and (workdate >= '".$sdate."' and workdate <= '".$edate."') ";
	$query = selectQuery($sql);
	$work = $query['cnt'];

	?>
	|<input type="hidden" id="pie_value_0" value="<?=$main?>">
	<input type="hidden" id="pie_value_1" value="<?=$live?>">
	<input type="hidden" id="pie_value_2" value="<?=$memo?>">
	<input type="hidden" id="pie_value_3" value="<?=$project?>">
	<input type="hidden" id="pie_value_4" value="<?=$work?>">|
	<tr>
		<td><?=$sdate."-".$edate?></td>
		<td><?=number_format($work)?></td>
		<td><?=number_format($report)?></td>
		<td><?=number_format($request)?></td>
		<td><?=number_format($share)?></td>
		<td><?=number_format($week_total)?></td>
	</tr>
<? }

if($mode == "auth_plus"){
	$idx = $_POST['idx'];
	$authcode = $_POST['authcode'];
	$auth = $_POST['auth'];
	
	if($auth == "all_auth"){
		$query = "chall_auth = ".$authcode." ,coin_auth = ".$authcode." ,all_auth = ".$authcode." ,highlevel = '0' ";
	}else if($auth == "admin_auth"){
		if($authcode == '0'){
			$query = "highlevel = '5'";
		}else if($authcode == '1'){
			$query = "highlevel = '0'";
		}
	}else{
		$query = $auth." =".$authcode;
	}

	$sql = "update work_member set ".$query." where idx = '".$idx."'";
	$updatequery = updateQuery($sql);
	
	exit;
}

if($mode == "backuser_auth"){
	// $sdate = $_POST['sdate'];
	// $edate = $_POST['edate'];

	$where = "";
	// if($sdate && $edate){
	// 	$where = $where .= " and (workdate >= '".$sdate."' and workdate <= '".$edate."')";
	// }

	$search = $_POST['search'];
	if($search){
		$where = $where .= " and (name like '%".$search."%' or company like '%".$search."%' or email like '%".$search."%' )";
	}

	$kind = $_POST['kind'];
	$tclass = $_POST['tclass'];

	if($kind){
		$sort = $kind;
	}else{
		$sort = " idx";
	}

	if($tclass=="btn_sort_up"){
		$updown = " asc";
	}else{
		$updown = " desc";
	}

	$url = "backuser_auth";
	$string = "&page=".$url;

	$p = $_POST['p']?$_POST['p']:$_GET['p'];
	if (!$p){
		$p = 1;
	}

	$list = $_POST['list'];
	$pagingsize = 5;					//페이징 사이즈
	if($list){
		$pagesize = $list;	
	}else{
		$pagesize = 15;
	}						//페이지 출력갯수
	$startnum = 0;						//페이지 시작번호
	$endnum = $p * $pagesize;			//페이지 끝번호

	//시작번호
	if ($p == 1){
		$startnum = 0;
	}else{
		$startnum = ($p - 1) * $pagesize;
	}

	$sql = "select idx,regdate, name, email, company, comcoin, coin, login_count, login_date, chall_auth, admin_auth, coin_auth, all_auth from work_member where state = '0' ".$where ; 
	$sql = $sql .= " order by ".$sort.$updown." limit ". $startnum.",".$pagesize;
	$query = selectAllQuery($sql);

	$sql = "select count(idx) as cnt from work_member where state = '0' ".$where ;
	$history_info_cnt = selectQuery($sql);
	if($history_info_cnt['cnt']){ 
		$total_count = $history_info_cnt['cnt'];
	}

	for($i=0; $i<count($query['idx']); $i++){
		$mem_idx = $query['idx'][$i];
		$no = ($p-1)*$pagesize+($i+1);
		?>
		<tr>
			<td><?=$no."(".$query['idx'][$i].")"?></td>
			<td><?=$query['name'][$i]?></td>
			<td><?=$query['email'][$i]?></td>
			<td><?=$query['company'][$i]?></td>
			<td class="text-center align-middle"><input class="form-check-input chall_chk" type="checkbox" value="<?=$mem_idx?>" id="chall_<?=$mem_idx?>" <?=$query['chall_auth'][$i]=='1'? "checked":""?>></td>
			<td class="text-center align-middle"><input class="form-check-input admin_chk" type="checkbox" value="<?=$mem_idx?>" id="admin_<?=$mem_idx?>" <?=$query['admin_auth'][$i]=='1'? "checked":""?>></td>
			<td class="text-center align-middle"><input class="form-check-input coin_chk" type="checkbox" value="<?=$mem_idx?>" id="coin_<?=$mem_idx?>" <?=$query['coin_auth'][$i]=='1'? "checked":""?>></td>
			<td class="text-center align-middle"><input class="form-check-input all_chk" type="checkbox" value="<?=$mem_idx?>" id="all_<?=$mem_idx?>" <?=$query['all_auth'][$i]=='1'? "checked":""?>></td>
		</tr>
	<?}
		if(count($query['idx'])==0){?>
			<tr><td colspan="6">조회된 목록이 없습니다.</td></tr>
		<?}
	?>
	<input type="hidden" id="page_num" value="<?=$p?>">
	<input type="hidden" value="<?=$pagesize?>" id="list_cnt" >
	<input type="hidden" value="<?=$search?>" id="backoff_search">
	<input type="hidden" value="backuser_auth" id="backoffice_type">
	<input type="hidden" value="<?=$tclass?>" id="tclass">
	<input type="hidden" value="<?=$kind?>" id="kind">|
		<?php echo back_pageing($pagingsize, $total_count, $pagesize, $string)?>
	<? echo "|".$tclass."|backuser_auth|";
}

if($mode == "penalty_now"){
	$where = "";
	$search = $_POST['search'];

	$kind = $_POST['kind'];
	$tclass = $_POST['tclass'];

	if($kind){
		$sort = $kind;
	}else{
		$sort = " idx";
	}

	if($tclass=="btn_sort_up"){
		$updown = " asc";
	}else{
		$updown = " desc";
	}

	$url = "backnow_pen";
	$string = "&page=".$url;

	$p = $_POST['p']?$_POST['p']:$_GET['p'];
	if (!$p){
		$p = 1;
	}

	$list = $_POST['list'];
	$pagingsize = 5;					//페이징 사이즈
	if($list){
		$pagesize = $list;	
	}else{
		$pagesize = 15;
	}						//페이지 출력갯수
	$startnum = 0;						//페이지 시작번호
	$endnum = $p * $pagesize;			//페이지 끝번호

	//시작번호
	if ($p == 1){
		$startnum = 0;
	}else{
		$startnum = ($p - 1) * $pagesize;
	}
	?>
	<thead>
		<tr>
			<th class="penalty_comp">회사</th>
			<th class="penalty_user"><div value="name">이름 <button class="list_arrow" value="btn_sort_down"></button></div></th>
			<th class="penalty_email"><div value="email">이메일 <button class="list_arrow" value="btn_sort_down"></button></div></th>
			<th class="penalty">패널티 유형</th>
			<th class="penalty_time"><div value="live_1_regdate">페널티 적용시간<button class="list_arrow" value="btn_sort_down"></button></div></th>
		</tr>
	</thead>
	<tbody>
		<?
			$sql = "select idx, state, name, email, penalty_state, company from work_member where penalty_state = '1' and state = '0' order by idx desc limit 0,10 ";
			$query = selectAllQuery($sql);

			for($i=0; $i<count($query['idx']); $i++){
			?>
			<tr>
				<td><?=$query['company'][$i]?></td>
				<td><?=$query['name'][$i]?></td>
				<td><?=$query['email'][$i]?></td>
				<td><?=$query['penalty_state']?></td>
				<td><?=$query['live_1_regdate']?></td>
			</tr>
		<?}
		if(count($query['idx'])==0){?>
			<tr><td colspan="6">조회된 목록이 없습니다.</td></tr>
		<?}?>
		<input type="hidden" id="tclass" value="btn_sort_down">
		<input type="hidden" id="kind" value="idx">
		<input type="hidden" id="code" value="<?=$code?>">
		<input type="hidden" value="<?=$pagesize?>" id="list_cnt" >
		<input type="hidden" id="backoff_sdate" value="<?=$last_week?>">
		<input type="hidden" id="backoff_edate" value="<?=$today?>">
	</tbody>|
	<?php echo back_pageing($pagingsize, $total_count, $pagesize, $string)?>
	<?
}

if($mode == "backwork_remove"){
	$idx = $_POST['idx'];

	$sql = "select idx, work_idx, state from work_todaywork where idx = '".$idx."' ";
	$query = selectQuery($sql);

	if($query['state']=='9'){
		echo "state9|";
		exit;
	}

	$sql = "select idx, email, state, admin_auth from work_member where email = '".$user_id."' and state ='0' and idx > 0";
	$member = selectQuery($sql);

	if($member['admin_auth']=='1' && $query['idx']){
		$sql = "update work_todaywork set state = '9' where idx = '".$idx."' ";
		$query = updateQuery($sql);
		if($query){
			echo "success|".$idx;
		}
	}else{
		echo "not_auth|";
	}
}

if($mode == "backwork_edit"){
	$idx = $_POST['idx'];
	$content = $_POST['content'];
	$sql = "select idx, email, state, admin_auth from work_member where email = '".$user_id."' and state ='0' and idx > 0";
	$member = selectQuery($sql);

	if($member['admin_auth']=='1'){
		$sql = "update work_todaywork set contents = '".$content."' where idx = '".$idx."' ";
		$query = updateQuery($sql);
		if($query){
			echo "success|".$idx;
		}else{
			echo "not_update|".$idx;
		}
	}else{
		echo "not_auth|";
	}
}

if($mode == "bro_notice_write"){
	$content = $_POST['content'];
	$title = $_POST['title'];
	$todatetime = date('Y-m-d H:i:s');
	$up = $_POST['update'];

	$sql = "select idx, state, email, admin_auth from work_member where state = '0' and email = '".$user_id."'";
	$member_chk = selectQuery($sql);

	if($member_chk['admin_auth']=='1'){
		if($up){
			$sql = "update bro_notice set title = '".$title."', contents = '".$content."'  where idx = '".$up."'";
			$update = updateQuery($sql);
		}else{
			$sql = "insert into bro_notice (state, email, companyno, title, contents, editdate, regdate) values ('0', '".$user_id."', '".$companyno."', '".$title."', '".$content."', '".TODATE."', '".$todatetime."')";
			$insert = insertIdxQuery($sql);
		}
		echo $sql;
	}else{
		echo "|not_auth";
	}

	if($update){
		echo "|update";
	}else if($insert){
		echo "|success";
	}

	echo "|공지사항|bro_notice.php";
	exit;
}

if($mode == "bro_faq_write"){
	$content = $_POST['content'];
	$title = $_POST['title'];
	$todatetime = date('Y-m-d H:i:s');
	$up = $_POST['update'];

	$sql = "select idx, state, email, admin_auth from work_member where state = '0' and email = '".$user_id."'";
	$member_chk = selectQuery($sql);

	if($member_chk['admin_auth']=='1'){
		if($up){
			$sql = "update bro_faq set title = '".$title."', contents = '".$content."'  where idx = '".$up."'";
			$update = updateQuery($sql);
		}else{
			$sql = "insert into bro_faq (state, email, companyno, title, contents, editdate, regdate) values ('0', '".$user_id."', '".$companyno."', '".$title."', '".$content."', '".TODATE."', '".$todatetime."')";
			$insert = insertIdxQuery($sql);
		}
		echo $sql;
	}else{
		echo "|not_auth";
	}

	if($update){
		echo "|update";
	}else if($insert){
		echo "|success";
	}
	echo "|FAQ|bro_faq.php";
	exit;
}

if($mode == "bro_sample_write"){
	$content = $_POST['content'];
	$title = $_POST['title'];
	$title_color = $_POST['title_color'];
	$cate = $_POST['category'];
	$service = $_POST['service'];

	$todatetime = date('Y-m-d H:i:s');
	$up = $_POST['update'];

	$sql = "select idx, state, email, admin_auth from work_member where state = '0' and email = '".$user_id."'";
	$member_chk = selectQuery($sql);

	if($member_chk['admin_auth']=='1'){
		if($up){
			$sql = "update bro_sample set category = '".$cate."', service = '".$service."', title = '".$title."', contents = '".$content."', title_color = '".$title_color."'  where idx = '".$up."'";
			$update = updateQuery($sql);
		}else{
			$sql = "insert into bro_sample (state, category, service, email, companyno, title, contents, title_color, editdate, regdate) values ('0', '".$cate."', '".$service."', '".$user_id."', '".$companyno."', '".$title."', '".$content."', '".$title_color."', '".TODATE."', '".$todatetime."')";
			$insert = insertIdxQuery($sql);
		}
		echo $sql;
	}else{
		echo "|not_auth";
	}

	if($update){
		echo "|update";
	}else if($insert){
		echo "|success";
	}
	echo "|활용사례|bro_sample.php";
	exit;
}

if($mode == "bro_manual_write"){
	$content = $_POST['content'];
	$title = $_POST['title'];
	$kind = $_POST['kind'];

	$todatetime = date('Y-m-d H:i:s');
	$up = $_POST['update'];

	$sql = "select idx, state, email, admin_auth from work_member where state = '0' and email = '".$user_id."'";
	$member_chk = selectQuery($sql);

	if($member_chk['admin_auth']=='1'){
		if($up){
			$sql = "update bro_manual set service = '".$kind."', title = '".$title."', contents = '".$content."' where idx = '".$up."'";
			$update = updateQuery($sql);
		}else{
			$sql = "insert into bro_manual (state, service, email, companyno, title, contents, editdate, regdate) values ('0', '".$kind."', '".$user_id."', '".$companyno."', '".$title."', '".$content."', '".TODATE."', '".$todatetime."')";
			$insert = insertIdxQuery($sql);
		}
		echo $sql;
	}else{
		echo "|not_auth";
	}

	if($update){
		echo "|update";
	}else if($insert){
		echo "|success";
	}
	echo "|사용자매뉴얼|bro_manual.php";
	exit;
}

// 노출/미노출/삭제/복원
if($mode == "backnote_btn"){
	$idx = $_POST['idx'];
	$status = $_POST['status'];
	$kind = $_POST['kind'];

	$sql = "select idx, state, email, admin_auth from work_member where state = '0' and email = '".$user_id."'";
	$member_chk = selectQuery($sql);

	if($member_chk['admin_auth']=='1'){
		$sql = "update bro_".$kind." set state = '".$status."' where idx = '".$idx."' ";
		$update = updateQuery($sql);
		echo $sql."|";
	}else{
		echo "not_auth|";
	}
	echo "complete";
	exit;
}

if($mode == "pass_reset"){
	$idx = $_POST['idx'];
	//비밀번호 초기화 번호
	$mb_pass ='0000';

	//KISA측 모듈사용
	$mem_pass =  kisa_encrypt($mb_pass);
	if (LIP != '59.19.241.15'){
		write_log_dir('허용되지 않는 IP 입니다', "pass");
		echo "no_ip";
		exit;
	}

	//사용자 조회하기
	$sql = "select idx from work_member where state='0' and idx='".$idx."'";
	$mb_use_info = selectQuery($sql);

	if($mb_use_info['idx']){
		$sql = "update work_member set password='".$mem_pass."' where idx='".$mb_use_info['idx']."'";
		$up = updateQuery($sql);
		if($up){
			echo "success";
		}else{
			echo "no_change";
		}
	}else{
		echo "not_user";
	}
	exit;
}

if($mode == "backoff_excel"){
	$type = $_POST['type'];
	$sdate = $_POST['sdate'];
	$edate = $_POST['edate'];
	$kind = $_POST['kind'];
	$tclass = $_POST['tclass'];
	$code = $_POST['code'];
	$search = $_POST['search'];
	$list = $_POST['list'];

	if($tclass=="btn_sort_up"){
		$updown = " asc";
	}else{
		$updown = " desc";
	}

	if($type == "backuser_list"){
		header( "Content-type: application/vnd.ms-excel; charset=utf-8");
		header( "Content-Disposition: attachment; filename = excel_test.xls" );     //filename = 저장되는 파일명을 설정합니다.
		header( "Content-Description: PHP4 Generated Data" );

		$sql = "select * from work_member_penalty order by idx desc limit 0,15";
		$query = selectAllQuery($sql);
		
		$excel = "<table border='1'>
			<tr>
			<td>이름</td>
			<td>성별</td>
			<td>나이</td>
			<td>전화번호</td>
			<td>사진여부</td>
			</tr>
		";

		for($i=0; $i<count($query['idx']); $i++){
			$excel = $excel .= "<tr>
				<td>".$query['idx'][$i]."</td>
				<td>".$query['email'][$i]."</td>
				<td>".$query['state'][$i]."</td>
				<td>".$query['name'][$i]."</td>
				<td>".$query['companyno'][$i]."</td>
			</tr>";
		}
		$excel = $excel .= "</table>";
		
		echo "<meta http-equiv='Content-Type' content='text/html; charset=utf-8'>";
		echo $excel;
		exit;
	}


}
?>

