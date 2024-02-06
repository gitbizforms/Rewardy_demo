<?php
ob_start();
$home_dir = str_replace( basename(__DIR__) , "" , __DIR__ );

include $home_dir . "inc_lude/conf_mysqli.php";
include $home_dir . "inc/SHA256/KISA_SHA256.php";
include DBCON_MYSQLI;
include FUNC_MYSQLI;

$mode = $_POST[mode];					//mode값 전달받음
$type_flag = ($chkMobile)?1:0;				//구분(0:사이트, 1:모바일)

if($_COOKIE){
	$user_id = $_COOKIE[user_id];
	$user_name = $_COOKIE[user_name];
	$user_level = $_COOKIE[user_level];
	$user_part = $_COOKIE[user_part];
	$part_name = $_COOKIE[part_name];
}

//검색 리스트
if($mode =="works_list_search"){

	//현재날짜
	$wdate = $_POST[wdate];
	$works_type = $_POST[works_type];

	//날짜변환
	if($wdate){
		$wdate = str_replace(".","-",$wdate);
	}

	if($works_type=="week"){

		if(strpos($wdate, "~") !== false) {
			$wdate = trim($wdate);
			$tmp = explode("~", $wdate);
			$monthday = trim($tmp[0]);
			$sunday = trim($tmp[1]);
			$month = strtotime($monthday);
		}else{

			$date_tmp = explode("-",$wdate);
			$year = $date_tmp[0];
			$month = $date_tmp[1];
			$day = $date_tmp[2];
			$ret = week_day("$year-$month-$day");
			if($ret){

				//월요일
				$monthday = $ret[month];

				//금요일
				$friday = $ret[friday];

				//일요일
				$sunday = $ret[sunday];

				//월요일, 타임으로
				$month = strtotime($monthday);


				if(strpos($monthday, "-") !== false) {
					$ex_monthday = str_replace("-", ".", $monthday);
				}

				if(strpos($sunday, "-") !== false) {
					$ex_sunday = str_replace("-", ".", $sunday);
				}

				$ex_wdate = $ex_monthday ." ~ ". $ex_sunday;
			}
		}


		//날짜 차이 계산
		$s_date = new DateTime($monthday);
		$e_date = new DateTime($sunday);
		$d_diff = date_diff($s_date, $e_date);
		$d_day = $d_diff->days + 1;

		//김정훈
		//검색관련
		$search = $_POST[search];
		$search_kind = $_POST[search_kind];

		//검색어 있을때
		if($search){
			//검색 조건이 없는경우 전체로
			if(!$search_kind){
				$search_kind = "all";
			}else{
				$search_kind = trim($search_kind);
			}
		}


		//업무보고 받는사람, 보고보낸사람 정보
		$work_report_user = work_report_user($wdate);

		//업무요청 받는사람, 요청보낸사람 정보
		$work_req_user = work_req_user($wdate);

		//업무공유 받는사람, 공유보낸사람 정보
		$work_share_user = work_share_user($wdate);

		$tdf_files = work_files_linfo($wdate, $search);

		//업무 댓글
		$where = " and b.workdate between '".$monthday."' and '".$sunday."'";
		$sql = "select a.idx as cidx, a.link_idx, a.work_idx, a.email, a.name, a.comment, a.cmt_flag, CASE WHEN a.editdate is not null then date_format(a.editdate, '%Y-%m-%d') WHEN a.editdate is null then date_format(a.regdate, '%Y-%m-%d') end as ymd,";
		$sql = $sql .= " CASE WHEN a.editdate is not null then date_format( a.editdate , '%m/%d/%y %l:%i:%s %p') WHEN a.editdate is null then date_format( a.regdate , '%m/%d/%y %l:%i:%s %p') end as regdate, b.idx from work_todaywork_comment as a left join work_todaywork as b on(a.link_idx=b.idx) where a.state='0' and a.companyno='".$companyno."'".$where." order by a.regdate desc";

		$works_comment_info = selectAllQuery($sql);
		$works_comment_info_cnt = count($works_comment_info[idx]);
		for($i=0; $i<$works_comment_info_cnt; $i++){
			$works_comment_info_idx = $works_comment_info[cidx][$i];
			$works_comment_info_link_idx = $works_comment_info[link_idx][$i];
			$works_comment_info_work_idx = $works_comment_info[work_idx][$i];
			$works_comment_info_email = $works_comment_info[email][$i];
			$works_comment_info_name = $works_comment_info[name][$i];
			$works_comment_info_ymd = $works_comment_info[ymd][$i];
			$works_comment_info_regdate = $works_comment_info[regdate][$i];
			$works_comment_info_comment = $works_comment_info[comment][$i];
			$works_comment_info_comment_strip = strip_tags($works_comment_info[comment][$i]);
			$works_comment_info_cmt_flag = $works_comment_info[cmt_flag][$i];


			//검색된 단어가 있을경우
			if($search){
				$works_comment_info_comment = keywordHightlight($search, $works_comment_info_comment);
			}

			if($works_comment_info_link_idx){
				$comment_list[$works_comment_info_link_idx][cidx][] = $works_comment_info_idx;
				$comment_list[$works_comment_info_link_idx][name][] = $works_comment_info_name;
				$comment_list[$works_comment_info_link_idx][email][] = $works_comment_info_email;
				$comment_list[$works_comment_info_link_idx][ymd][] = $works_comment_info_ymd;
				$comment_list[$works_comment_info_link_idx][regdate][] = $works_comment_info_regdate;
				$comment_list[$works_comment_info_link_idx][comment][] = $works_comment_info_comment;
				$comment_list[$works_comment_info_link_idx][comment_strip][] = $works_comment_info_comment_strip;
				$comment_list[$works_comment_info_link_idx][cmt_flag][] = $works_comment_info_cmt_flag;
			}
		}


		//검색(업무종류)
		//works : 오늘업무,
		if($search_kind){
			switch($search_kind){

				//오늘업무
				case "works" :
					$where = " and work_flag='2' and contents like '%".$search."%'";
					//조건
					$where = $where .= " and workdate between '".$monthday."' and '".$sunday."'";
					break;

				//보고업무
				case "report" :
					$where = " and work_flag='1' and (title like '%".$search."%' or contents like '%".$search."%')";
					//조건
					$where = $where .= " and workdate between '".$monthday."' and '".$sunday."'";
					break;

				//요청업무
				case "req" :
					$where = " and work_flag='3' and contents like '%".$search."%'";
					//조건
					$where = $where .= " and workdate between '".$monthday."' and '".$sunday."'";
					break;

				//공유업무
				case "share" :
					$where = " and share_flag in(1,2) and contents like '%".$search."%'";
					//조건
					$where = $where .= " and workdate between '".$monthday."' and '".$sunday."'";
					break;

				//첨부파일
				case "file" :
					$where = " and b.file_real_name like '%".$search."%'";
					//조건
					$where = $where .= " and b.workdate between '".$monthday."' and '".$sunday."'";
					break;

				//메모
				case "memo" :
					$where = " and b.comment like '%".$search."%'";
					//조건
					$where = $where .= " and b.workdate between '".$monthday."' and '".$sunday."'";
					break;


				//전체
				case "all" :
					$where_search = "t1.contents like '%".$search."%' or  t1.file_real_name like '%".$search."%'";	// or t1.comment like '%".$search."%'";
					//$where = " and a.contents like '%".$search."%'";

					//조건
					$where = $where .= " and workdate between '".$monthday."' and '".$sunday."'";
					//$where = $where .= " and workdate between '".$monthday."' and '".$sunday."'";
					break;

				//전체
				default :
					$where = " and contents like '%".$search."%'";
					break;
			}
		}




		//검색어 && 파일명일경우
		if($search){

			//파일검색
			if($search_kind=='file'){

				$sql = "select a.idx, a.state, a.work_flag, a.part_flag, a.decide_flag, a.work_idx, a.repeat_flag, a.notice_flag, a.share_flag, date_format( a.regdate , '%Y.%m.%d') as ymd, date_format( a.regdate , '%H:%i') as his, a.memo_view, a.contents_view, a.title, a.contents as contents, a.email, a.name, a.req_date, a.workdate, a.regdate";
				$sql = $sql .= " from work_todaywork as a left join work_filesinfo_todaywork as b on(a.idx=b.work_idx or a.work_idx=b.work_idx)";
				$sql = $sql .= " where a.state!='9' and b.state='0' and a.companyno='".$companyno."' and a.email='".$user_id."'";
				$sql = $sql .= "".$where."";
				$sql = $sql .= " group by a.idx, a.state, a.work_flag, a.part_flag, a.decide_flag, a.work_idx, a.repeat_flag, a.notice_flag, a.share_flag,  a.memo_view, a.contents_view, a.title,  a.contents, a.email, a.name, a.req_date, a.workdate, a.regdate";
				$sql = $sql .= " order by a.idx desc";

				//메모검색
			}else if($search_kind=='memo'){

				$sql = "(";
				$sql .= " select a.idx, a.state, a.work_flag, a.part_flag, a.decide_flag, a.work_idx, a.repeat_flag, a.notice_flag, a.share_flag, date_format( a.regdate , '%Y.%m.%d') as ymd, date_format( a.regdate , '%H:%i') as his, a.memo_view, a.contents_view, a.title, a.contents as contents, a.email, a.name, a.req_date, a.workdate, a.regdate, b.email as c_email from";
				$sql .= " (select idx, state, work_flag, part_flag, decide_flag, work_idx, repeat_flag, notice_flag, share_flag, date_format(regdate , '%Y.%m.%d') as ymd, date_format(regdate , '%H:%i') as his, memo_view, contents_view, title, contents, email, name, req_date, workdate, regdate,companyno";
				$sql .= " from work_todaywork where workdate between '".$monthday."' and '".$sunday."' and state !='9' and email='".$user_id."') as a";
				$sql .= " left join (select work_idx,comment,email from work_todaywork_comment where workdate between '".$monthday."' and '".$sunday."' and state !='9') as b on (a.idx=b.work_idx)";
				$sql .= " where a.companyno='".$companyno."' and (b.comment like '%".$search."%' or a.contents like '%".$search."%')";
				$sql .= " ) union (";
				$sql .= " select a.idx, a.state, a.work_flag, a.part_flag, a.decide_flag, a.work_idx, a.repeat_flag, a.notice_flag, a.share_flag, date_format( a.regdate , '%Y.%m.%d') as ymd, date_format( a.regdate , '%H:%i') as his, a.memo_view, a.contents_view, a.title, a.contents as contents, a.email, a.name, a.req_date, a.workdate, a.regdate from ";
				$sql .= " (select idx, state, work_flag, part_flag, decide_flag, work_idx, repeat_flag, notice_flag, share_flag, date_format(regdate , '%Y.%m.%d') as ymd, date_format( regdate , '%H:%i') as his, memo_view, contents_view, title, contents, email, name, req_date, workdate, regdate,companyno";
				$sql .= " from work_todaywork where workdate between '".$monthday."' and '".$sunday."' and state != '9' and email = '".$user_id."' ) as a";
				$sql .= " join (select comment,workdate,state,email,link_idx from work_todaywork_comment  where workdate between '".$monthday."' and '".$sunday."' and state != '9') as b on (a.idx = b.link_idx)";
				$sql .= " where a.companyno='".$companyno."' and b.comment like '%".$search."%' and b.email != '".$user_id."' )";

			//전체검색
			}else if($search_kind=='all'){

				//조건
				$where = "";
				$where .= " and (a.contents like '%".$search."%' or b.comment like '%".$search."%' or c.file_real_name like '%".$search."%')";
				$where .= " and (a.workdate between '".$monthday."' and '".$sunday."' or b.workdate between '".$monthday."' and '".$sunday."' or c.workdate between '".$monthday."' and '".$sunday."' ) ";

				// $sql = "(";
				// $sql .= " select a.idx, a.state, a.work_flag, a.part_flag, a.decide_flag, a.work_idx, a.repeat_flag, a.notice_flag, a.share_flag, date_format( a.regdate , '%Y.%m.%d') as ymd, date_format( a.regdate , '%H:%i') as his, a.memo_view, a.contents_view, a.title, a.contents as contents, a.email, a.name, a.req_date, a.workdate, a.regdate from";
				// $sql .= " (select idx, state, work_flag, part_flag, decide_flag, work_idx, repeat_flag, notice_flag, share_flag, date_format(regdate , '%Y.%m.%d') as ymd, date_format(regdate , '%H:%i') as his, memo_view, contents_view, title, contents, email, name, req_date, workdate, regdate,companyno";
				// $sql .= " from work_todaywork where workdate between '".$monthday."' and '".$sunday."' and state !='9' and email='".$user_id."') as a";
				// $sql .= " left join (select work_idx,comment,email from work_todaywork_comment where workdate between '".$monthday."' and '".$sunday."' and state !='9') as b on (a.idx=b.work_idx)";
				// $sql .= " left join (select work_idx,file_real_name from work_filesinfo_todaywork where workdate between '".$monthday."' and '".$sunday."' and state ='0') as c on(a.idx=c.work_idx or a.work_idx=c.work_idx)";
				// $sql .= " where a.companyno='".$companyno."' and (b.comment like '%".$search."%' or a.title like '%".$search."%' or a.contents like '%".$search."%' or c.file_real_name like '%".$search."%')";
				// $sql .= " ) union (";
				// $sql .= " select a.idx, a.state, a.work_flag, a.part_flag, a.decide_flag, a.work_idx, a.repeat_flag, a.notice_flag, a.share_flag, date_format( a.regdate , '%Y.%m.%d') as ymd, date_format( a.regdate , '%H:%i') as his, a.memo_view, a.contents_view, a.title, a.contents as contents, a.email, a.name, a.req_date, a.workdate, a.regdate from ";
				// $sql .= " (select idx, state, work_flag, part_flag, decide_flag, work_idx, repeat_flag, notice_flag, share_flag, date_format(regdate , '%Y.%m.%d') as ymd, date_format(regdate , '%H:%i') as his, memo_view, contents_view, title, contents, email, name, req_date, workdate, regdate,companyno";
				// $sql .= " from work_todaywork where workdate between '".$monthday."' and '".$sunday."' and state != '9' and email = '".$user_id."' ) as a";
				// $sql .= " join (select comment,workdate,state,email,link_idx from work_todaywork_comment  where workdate between '".$monthday."' and '".$sunday."' and state != '9') as b on (a.idx = b.link_idx)";
				// $sql .= " where a.companyno='".$companyno."' and b.comment like '%".$search."%' and b.email != '".$user_id."' )";

				$sql = "
				SELECT 
					a.idx, a.state, a.work_flag, 
					GROUP_CONCAT(b.comment ORDER BY b.comment SEPARATOR ' ') AS comments, 
					a.part_flag, a.decide_flag, a.work_idx, a.repeat_flag, a.notice_flag, a.share_flag, 
					DATE_FORMAT(a.regdate, '%Y.%m.%d') AS ymd, DATE_FORMAT(a.regdate, '%H:%i') AS his, 
					a.memo_view, a.contents_view, a.title, a.contents, a.email, a.name, a.req_date, a.workdate, a.work_stime, a.work_etime, a.regdate
				FROM  (
					SELECT 
						idx, state, work_flag, part_flag, decide_flag, work_idx, repeat_flag, notice_flag, share_flag, 
						DATE_FORMAT(regdate, '%Y.%m.%d') AS ymd, DATE_FORMAT(regdate, '%H:%i') AS his, memo_view, 
						contents_view, title, contents, email, name, req_date, workdate, work_stime, work_etime, regdate, companyno
					FROM work_todaywork
					WHERE workdate BETWEEN '".$monthday."' AND '".$sunday."' 
						AND state != '9' AND email = '".$user_id."'
				) AS a
				LEFT JOIN work_todaywork_comment b ON (a.idx = b.work_idx) 
				WHERE a.companyno = '".$companyno."' 
					AND (match(b.comment) AGAINST('".$search."' IN BOOLEAN MODE) OR match(a.contents) AGAINST('".$search."' IN BOOLEAN MODE))
				GROUP BY a.idx
				order by a.workdate desc";
			}else{

				//업무검색
				$sql ="select idx, state, work_flag, part_flag, decide_flag, work_idx, repeat_flag, notice_flag, share_flag, date_format( regdate , '%Y.%m.%d') as ymd, date_format( regdate , '%H:%i') as his, memo_view, contents_view, title, contents, contents1,";
				$sql = $sql .=" email, name, req_date, workdate, regdate ";
				$sql = $sql .= " FROM work_todaywork where state!='9' and companyno='".$companyno."' and email='".$user_id."'".$where."";
				$sql = $sql .= " order by workdate asc";
			}

		}else{

			//주간업무
			$sql ="select idx, state, work_flag, part_flag, decide_flag, work_idx, repeat_flag, notice_flag, share_flag, date_format( regdate , '%Y.%m.%d') as ymd as ymd, date_format( regdate , '%H:%i') as his, memo_view, contents_view, title, contents, contents1,";
			$sql = $sql .=" email, name, req_date, workdate, regdate ";
			$sql = $sql .= " FROM work_todaywork where state!='9' and companyno='".$companyno."' and email='".$user_id."'".$where."";
			$sql = $sql .= " order by sort asc, idx desc";
		}
		//echo $sql;
		$week_info = selectAllQuery($sql);
    	php_timer();

		if($search){
			$first_day = @current($week_info[workdate]);
			$last_day = @end($week_info[workdate]);
			$month = strtotime($first_day);
			$d_day = count($week_info[workdate]);
		}

		//결과가 없을때
		if(!$week_info[idx]){
			//검색 키워드, 검색 분류가 있을경우
			if($search && $search_kind){
				$list_result_text = "검색어로 입력한 `".$search."`에 대한 업무가 없습니다.";
			}else{
				$list_result_text = "현재 등록된 주간업무가 없습니다.";
			}
		}

		$week_works = array();
		if($week_info[idx]){
			$week_info_cnt = count($week_info[idx]);
			for($i=0; $i<$week_info_cnt; $i++){
				$idx = $week_info[idx][$i];
				$state = $week_info[state][$i];
				$work_email = $week_info[email][$i];
				$work_name = $week_info[name][$i];
				$work_flag = $week_info[work_flag][$i];
				$work_idx = $week_info[work_idx][$i];
				$repeat_flag = $week_info[repeat_flag][$i];
				$share_flag = $week_info[share_flag][$i];
				$memo_view = $week_info[memo_view][$i];
				$contents_view = $week_info[contents_view][$i];

				$decide_flag = $week_info[decide_flag][$i];
				$notice_flag = $week_info[notice_flag][$i];
				$workdate = $week_info[workdate][$i];
				$work_stime = $week_info[work_stime][$i];
				$work_etime = $week_info[work_etime][$i];
				$title = $week_info[title][$i];
				$contents = $week_info[contents][$i];
				$contents_edit = strip_tags($week_info[contents][$i]);


				if($decide_flag == '1'){$decide_name = "연차";}else if($decide_flag == '2'){ $decide_name = "반차";}else if($decide_flag == '3'){$decide_name = "외출";}else if($decide_flag == '4'){$decide_name = "조퇴";}
				else if($decide_flag == '5'){$decide_name = "출장";}else if($decide_flag == '6'){$decide_name = "교육";}
				else if($decide_flag == '7'){$decide_name = "미팅";}else if($decide_flag == '8'){$decide_name = "회의";}



				//검색된 단어가 있을경우
				if($search){
					$contents = keywordHightlight($search, $contents);
					$title = keywordHightlight($search, $title);
				}

				$his = $week_info[his][$i];
				$ymd = $week_info[ymd][$i];

				$week_works[$workdate][idx][] = $idx;
				$week_works[$workdate][state][] = $state;
				$week_works[$workdate][his][] = $his;

				if ($ymd){
					$ymd_tmp = explode(".",$ymd);
					$ymd_change = $ymd_tmp[1].".".$ymd_tmp[2];
				}

				//요청 및 공유, 보고
				if($work_idx){
					$work_com_idx = $work_idx;
				}else{
					$work_com_idx = $idx;
				}

				$week_works[$workdate][ymd][] = $ymd_change;
				$week_works[$workdate][title][] = $title;
				$week_works[$workdate][state][] = $state;
				$week_works[$workdate][contents][] = $contents;
				$week_works[$workdate][contents_edit][] = $contents_edit;
				$week_works[$workdate][email][] = $work_email;
				$week_works[$workdate][work_flag][] = $work_flag;
				$week_works[$workdate][decide_flag][] = $decide_flag;
				$week_works[$workdate][work_stime][] = $work_stime;
				$week_works[$workdate][work_etime][] = $work_etime;
				$week_works[$workdate][work_idx][] = $work_idx;
				$week_works[$workdate][repeat_flag][] = $repeat_flag;
				$week_works[$workdate][notice_flag][] = $notice_flag;
				$week_works[$workdate][share_flag][] = $share_flag;
				$week_works[$workdate][work_com_idx][] = $work_com_idx;
				$week_works[$workdate][memo_view][] = $memo_view;
				$week_works[$workdate][contents_view][] = $contents_view;

				if($work_idx == null){
					$week_works[$workdate][work_link_coin][] = $idx;
				}
			}

			//좋아요 보낸사람 리스트
			$sql = "select a.com_idx as com_idx, a.send_name as send,a.companyno,a.workdate from (select send_name,com_idx,state,companyno,workdate from work_todaywork_like where state != 9) a";
			$sql = $sql." join work_todaywork_comment b on a.com_idx = b.idx where a.companyno='".$companyno."' and a.workdate>='".$monthday."'";
			$work_give_list = selectAllQuery($sql);
			if($work_give_list){
				$work_send_like_name = array();
				$work_give_cnt = count($work_give_list[com_idx]);
				for($i=0; $i< $work_give_cnt; $i++){
					$com_idx = $work_give_list[com_idx][$i];
					$send = $work_give_list[send][$i];
					$work_send_like_name[$com_idx][send] = $send;
				}
				unset($com_idx);
				unset($send);
			}


			//중복제거
			$week_unique = array_unique($week_info[workdate]);

			$key = 0;
			$new_arr = array();
			foreach($week_unique as $var) {
			  $new_arr[$key] = $var;
			  $key++;
			}

			$new_arr_cnt = count($new_arr);
			for($i=0; $i<$new_arr_cnt; $i++){

				$workdate = $new_arr[$i];
				$day_list = $week[date("w", strtotime($workdate))];
				$day_tmp = @explode("-", $workdate);
				if($day_tmp){
					$week_day = $day_tmp[1].".".$day_tmp[2];
				}

				?>
				<div class="tdw_list_ww_box">
					<strong class="tdw_list_title_date"><?=$week_day?> (<?=$day_list?>)</strong>
					<ul class="tdw_list_ul">
						<?
						$week_works_cnt = count($week_works[$workdate][contents]);
						for($j=0; $j < $week_works_cnt; $j++){

							$idx = $week_works[$workdate][idx][$j];
							$work_flag = $week_works[$workdate][work_flag][$j];
							$work_idx = $week_works[$workdate][work_idx][$j];
							$state = $week_works[$workdate][state][$j];
							$repeat_flag = $week_works[$workdate][repeat_flag][$j];

							$work_wtitle = $week_works[$workdate][title][$j];
							$work_contents = $week_works[$workdate][contents][$j];
							$work_contents_edit = $week_works[$workdate][contents_edit][$j];
							$work_email = $week_works[$workdate][email][$j];

							//반복설정
							$decide_flag = $week_works[$workdate][decide_flag][$j];

							$work_stime = $week_works[$workdate][work_stime][$j];
							$work_etime = $week_works[$workdate][work_etime][$j];

							//알림설정
							$notice_flag = $week_works[$workdate][notice_flag][$j];

							$share_flag = $week_works[$workdate][share_flag][$j];
							$work_com_idx = $week_works[$workdate][work_com_idx][$j];
							$memo_view = $week_works[$workdate][memo_view][$j];
							$contents_view = $week_works[$workdate][contents_view][$j];

							$comment_list_work_com_cnt = count($comment_list[$work_com_idx][cidx]);
							$comment_list_work_cnt = count($comment_list[$work_idx][cidx]);
							$comment_list_cnt = count($comment_list[$idx][cidx]);

							if($repeat_flag == 1){
								$repeat_text = "매일반복";
							}else if($repeat_flag == 2){
								$repeat_text = "매주반복";
							}else if($repeat_flag == 3){
								$repeat_text = "매월반복";
							}else if($repeat_flag == 4){
								$repeat_text = "반복안함";
							}else{
								$repeat_text = "반복설정";
							}

							$memo_view_bt_style = "";
							//메모 접기/펼치기(0:펼치기, 1:접기)
							if($memo_view == '1'){
								$memo_view_in = " off";
								$memo_view_bt = " off memo_on";
								$memo_view_bt_style = " style=\"display: block;\"";

							}else{
								$memo_view_in = "";
								$memo_view_bt = " on";
								$memo_view_bt_style = "";
							}

							$report_view_bt_style = "";
							//보고업무 내용 접기/펼치기(0:펼치기, 1:접기)
							if($contents_view == '1'){
								$report_view_in = " off";
								$report_view_bt = " off memo_on";
								$report_view_bt_style = " style=\"display: block;\"";

							}else{
								$report_view_in = "";
								$report_view_bt = " on memo_on";
								$report_view_bt_style = "";
							}

							$share_view_bt_style = "";
							//공유업무 내용 접기/펼치기(0:펼치기, 1:접기)
							if($contents_view == '1'){
								$share_view_in = " off";
								$share_view_bt = " off";
								$share_view_bt_style = " off";

							}else{
								$share_view_in = "";
								$share_view_bt = " on";
								$share_view_bt_style = "";
							}

							$work_view_bt_style = "";
							//오늘업무 내용 접기/펼치기(0:펼치기, 1:접기)
							if($contents_view == '1'){
								$work_view_in = " off";
								$work_view_bt = " off";
								$work_view_bt_style = " off";

							}else{
								$work_view_in = "";
								$work_view_bt = " on";
								$work_view_bt_style = "";
							}
							
							$req_view_bt_style = "";
							//공유업무 내용 접기/펼치기(0:펼치기, 1:접기)
							if($contents_view == '1'){
								$req_view_in = " off";
								$req_view_bt = " off";
								$req_view_bt_style = " off";

							}else{
								$req_view_in = "";
								$req_view_bt = " on";
								$req_view_bt_style = "";
							}

							//읽음표시
							//요청업무
							$read_text="";
							$work_read_reading="";


							//챌린지 알림( notice_flag:1 )
							if($notice_flag){
								$work_title = "[".$notice_list[$notice_flag] ."]";
							}else{

								//보고업무
								if($work_flag == "1"){
									///$work_title = "";

									//보고받은 업무
									if($work_idx){
										$work_to_name = $work_report_user['receive'][$work_idx];
										$work_title = "[".$work_to_name ."님에게 보고받음]";

									}else{

										//보고 1명 이상인 경우
										$work_report_user_list_cnt = $work_report_user['send_cnt'][$work_com_idx];
										if($work_report_user_list_cnt > 1){
											$work_user_count = $work_report_user_list_cnt - 1;
											$work_report_title = $work_report_user['send'][$work_com_idx][0]. "님 외 ". $work_user_count . "명에게 보고함";
											$work_title = "[". $work_report_title. "]";
										}else{
											$work_report_title = $work_report_user['send'][$work_com_idx][0];
											$work_title = "[". $work_report_title. "님에게 보고함]";
										}

										$work_report_read_all = $work_report_user['read'][$idx][all];
										$work_report_read_cnt = $work_report_user['read'][$idx][read];
										$work_read_reading = $work_report_read_cnt;

										//읽지않은사용자
										if($work_read_reading>0){
											$read_text = "&nbsp;&nbsp;<em class=\"tdw_read\">읽음 ".$work_read_reading."</em>";
										}else{
											$read_text = "&nbsp;&nbsp;<em class=\"tdw_read\">읽음 0</em>";
										}

									}

									$work_contents = $work_wtitle;
									$work_contents_edit = $work_wtitle;

								//오늘업무
								}else if($work_flag == "2"){
									if($decide_list[$decide_flag]){
										$work_title = "<span>[".$decide_list[$decide_flag]."]</span>";
									}else{
										$work_title = "";
									}

									$work_contents = $work_contents;
									$work_contents_edit = $work_contents_edit;


								//업무요청
								}else if($work_flag == "3"){

									if($work_idx && $work_email == $user_id){

										$work_to_name = $work_req_user['receive'][$work_idx];
										$work_title = "[".$work_to_name ."님에게 요청받음]";

									}else{
										$work_user_list_cnt = $work_req_user['send_cnt'][$work_com_idx];
										if($work_user_list_cnt > 1){
											$work_user_count = $work_user_list_cnt - 1;
											$work_req_title = $work_req_user['send'][$work_com_idx][0]. "님 외 ". $work_user_count . "명에게 요청함";
											$work_title = "[". $work_req_title. "]";
										}else{
											$work_req_title = $work_req_user['send'][$work_com_idx][0];
											$work_title = "[". $work_req_title. "님에게 요청함]";
										}

										$work_req_read_all = $work_req_user['read'][$work_com_idx][all];
										$work_req_read_cnt = $work_req_user['read'][$work_com_idx][read];
										$work_read_reading = $work_req_read_cnt;

										if($work_read_reading>0){
											$read_text = "&nbsp;&nbsp;<em class=\"tdw_read\">읽음 ".$work_read_reading."</em>";
										}else{
											$read_text = "&nbsp;&nbsp;<em class=\"tdw_read\">읽음 0</em>";
										}

									}

									$work_contents = $work_contents;
									$work_contents_edit = $work_contents;
								}



								if($work_idx){
									$edit_id = "";

									//공유한 업무
									if($share_flag=='1'){
										$work_share_send_list_cnt = $work_share_user['send_cnt'][$idx];
										if($work_share_send_list_cnt > 1){
											$work_user_count = $work_share_send_list_cnt - 1;
											$work_share_title = $work_share_user['send'][$idx][0]. "님 외 ". $work_user_count . "명에게 공유함";
											$work_title = "[". $work_share_title. "]";
										}else{
											$work_share_title = $work_share_user['send'][$idx][0];
											$work_title = "[". $work_share_title. "님에게 공유함]";
										}


										$work_share_read_all = $work_share_user['read'][$idx][all];
										$work_share_read_cnt = $work_share_user['read'][$idx][read];
										$work_read_reading = $work_share_read_cnt;



										//읽지않은사용자
										if($work_read_reading>0){
											$read_text = "&nbsp;&nbsp;<em class=\"tdw_read\">읽음 ".$work_read_reading."</em>";
										}else{
											$read_text = "&nbsp;&nbsp;<em class=\"tdw_read\">읽음 0</em>";
										}

										$edit_id = " id='tdw_wlist_edit_".$idx."'";

									}else if($share_flag=='2'){

										$work_share_title = $work_share_user['receive'][$work_idx];
										$work_title = "[".$work_share_title."님에게 공유받음]";
									}
								}

							}

							//공유함($share_flag=1), 공유취소($share_flag=2), 요청받은업무($work_flag=3) 아이콘 변경
							$li_class = "";
							$tdw_list = false;
							if($share_flag=="1"){
								$li_class = " share";
							}else if($share_flag=="2"){
								$li_class = " share_cancel";
							}else if($work_flag == "2" && $share_flag == "0" && $notice_flag !="1"){
								$li_class = " work";
								$tdw_list = false;
							}else{
								//notice_flag=1 챌린지알림,
								//$work_flag=3 요청업무, $work_idx=null 요청보낸업무
								//$work_flag=3 요청업무, $work_idx 요청받은업무
								if($work_flag=='3' && $work_idx){
									$li_class = " req_get";
									$tdw_list = true;
								}else if($work_flag=='3' && $work_idx==null){
									$li_class = " req";
									$tdw_list = "";
								}else if($work_flag=='0' && $work_idx!=null){
									$li_class = " getreq";
									$tdw_list = true;
								}else if($work_flag == "1"){

									//보고받음
									if($work_idx){
										$li_class = " report_get";
										$tdw_list = false;
									}else{
										//보고함
										$li_class = " report";
										$tdw_list = false;
									}
								}else{

									//알림글
									if($notice_flag=="1"){
										$li_class = " challenges";
										$tdw_list = false;
									}else{
										$li_class = "";
										$tdw_list = true;
									}
								}
							}
						?>
						<input type="hidden" id="search_value" value="search">
							<li class="tdw_list_li<?=$li_class?>" id="workslist_<?=$idx?>">
								<div class="tdw_list_box<?=$week_works[$workdate][state][$j]=='1'?" on":""?>" id="tdw_wlist_box_<?=$idx?>" name="onoff_<?=$i?>">
									<div class="tdw_list_chk">
										<button class="btn_tdw_list_chk" <?if($work_flag!='1'){?>value="<?=$idx?>"<?}?> <?=$tdw_list?" id='tdw_slist_chk'":""?>><span>완료체크</span></button>
									</div>
									<div class="tdw_list_desc">

										<?if($work_idx){?>

											<?if($notice_flag=="1"){?>
												<p <?=$edit_id?> id="notice_link" value="<?=$work_idx?>">
											<?}else{?>
												<p <?=$edit_id?>>
											<?}?>
												<?=$work_title?"<span>".$work_title."</span>":""?><?=$work_contents?><?=$read_text?>
											</p>

										<?}else{?>

											<?//보고업무
											if($work_flag == "1"){?>
												<p id="tdw_wlist_edit_<?=$idx?>">
												<?if($work_title){?><span><?=$work_title?></span><?}?><?=$work_contents?><?=$read_text?></p>
											<?}else if($decide_flag > '1' && $work_stime != null && $work_etime != null){?>
												<p id="tdw_wlist_edit_<?=$idx?>">
													<?if($decide_flag == 1){?>
														<span> <?= "[ ".$decide_name." ]" ?></span><?=$contents?>
													<?}else if($decide_flag > 1){?>
														<span> <?= "[ ".$decide_name."   ".$work_stime."~".$work_etime." ]" ?></span><?=$contents?>
													<?}?>
												</p>
											<?}else{?>
												<p id="tdw_wlist_edit_<?=$idx?>">
												<?if($work_title){?><span><?=$work_title?></span><?}?><?=$work_contents?><?=$read_text?></p>
											<?}?>
										<?}?>

										<div class="tdw_list_regi" id="tdw_wlist_regi_edit_<?=$idx?>">
											<strong>수정중</strong>
											<textarea name="" class="textarea_regi" id="textarea_wregi_<?=$idx?>"><?=strip_tags($work_contents_edit)?></textarea>
											<div class="btn_regi_box">
												<button class="btn_regi_submit" id="btn_regiw_submit" value="<?=$idx?>"><span>확인</span></button>
												<button class="btn_regi_cancel"><span>취소</span></button>
											</div>
										</div>
									</div>

									<div class="tdw_list_function new_type">
										<div class="tdw_list_function_in">
											<?
											//받은업무
											if($work_flag=="1" && $work_idx){?>
												<button class="tdw_list_h tdw_list_reported_hart<?=$work_like_list[$work_idx]>0?" on":""?>" title="좋아요" <?=$work_like_list[$work_idx]>0?"":" id=\"tdw_list_jjim\""?> value="<?=$work_idx?>"><span>좋아요</span></button>
											<?
											//공유받음
											}else if($share_flag=='2' && $work_idx){?>
												<button class="tdw_list_h tdw_list_shared_hart<?=$work_like_list[$work_idx]>0?" on":""?>" title="좋아요" <?=$work_like_list[$work_idx]>0?"":" id=\"tdw_list_jjim\""?> value="<?=$work_idx?>"><span>좋아요</span></button>
											<?}else{?>

												<?//공유 보낸 업무?>
												<?if($share_flag=="1" && $work_idx){?>
													<?if($work_like_receive[$work_idx]){?>
														<button class="tdw_list_jjim_clear<?=$work_like_receive[$work_idx]>0?" on":""?>" title="좋아요" value="<?=$work_idx?>"><span>좋아요</span></button>
													<?}?>
												<?}?>

												<?//보고업무 보낸 업무?>
												<?if($work_flag=="1" && $work_idx==null){?>
													<?if($work_like_receive[$idx]){?>
														<button class="tdw_list_jjim_clear<?=$work_like_receive[$idx]>0?" on":""?>" title="좋아요"  value="<?=$work_idx?>"><span>좋아요</span></button>
													<?}?>
												<?}?>
											<?}?>
											<div class="tdw_list_more">
												<?if($work_flag != '4'){?>
													<button class="tdw_list_o" title="메뉴열기" id=""><span>메뉴열기</span></button>
												<?}?>
												<div class="tdw_list_1depth">
													<ul>
													<?if(($notice_flag=='0' || $decide_flag=='0') && $share_flag!=='2' && $notice_flag!='1' && $work_flag!='4'){?>
														<li>
															<button class="tdw_list_p tdw_list_party_link <?=$project_link_info[$idx]?"on":""?>" id="tdw_list_party_link" value="<?=$idx?>" title="파티연결"><span>파티연결</span></button>
														</li>
													<?}?>
													<?//공유하기?>
													<?//공유한 업무?>
													<?if($share_flag=='1' && $work_idx){?>
														<li>
															<button class="tdw_list_share_cancel tdw_list_s" id="tdw_list_share_cancel" value="<?=$idx?>" title="공유취소"><span>공유취소</span></button>
														</li>
													<?}else{?>
														<?//나의업무작성, 공유업무작성?>
														<?if(($work_flag=='2' && $work_idx==null) || ($share_flag=='1' && $work_idx==null)){?>
														<li>
															<button class="tdw_list_share tdw_list_s" id="tdw_list_share" value="<?=$idx?>" title="공유하기"><span>공유하기</span></button>
														</li>
															<?}?>
													<?}?>
													
													<?//파일첨부?>
													<?//파일첨부(나의업무, 공유업무작성, 보고업무작성, 요청업무작성)?>
													<?if(($work_flag=='2' && $work_idx==null) || ($share_flag=='1' && $work_idx) || ($work_flag=='1' && $work_idx==null) || ($work_flag=='3' && $work_idx==null)){?>
														<li>
															<button class="tdw_list_files tdw_list_f" id="tdw_file_add_<?=$idx?>" title="파일추가"><span>파일추가</span></button>
															<input type="file" id="files_add_<?=$idx?>" style="display:none;">
														</li>
													<?}?>
													
													<?//사람선택?>
													<?//공유업무작성, 보고업무작성, 요청업무작성?>
													<?if(($share_flag=='1' && $work_idx) || ($work_flag=='1' &&  $work_idx==null) || ($work_flag=='3' && $work_idx==null)){?>
														<li>
															<button class="tdw_list_user tdw_list_u" id="tdw_send_user_<?=$idx?>" value="<?=$idx?>" title="사람추가"><span>사람추가</span></button>
														</li>
													<?}?>
														<?//메모작성?>
													<? if($notice_flag!='1' && $work_flag!='4'){?>
														<?php if($secret_flag == '1'){?>
															<li>
																<button class="tdw_list_memo_secret tdw_list_m" id="tdw_list_memo" value="<?=$idx?>" title="메모하기"><span>메모하기</span></button>
															</li>
														<?php }else{ ?>
															<li>
																<button class="tdw_list_memo tdw_list_m" id="tdw_list_memo" value="<?=$idx?>" title="메모하기"><span>메모하기</span></button>
															</li>
														<?php } ?>	
														<?}?>
													<?if(($work_flag=='2' && $work_idx==null) || ($work_flag=='3' && $work_idx==null)){?>
														<? if(($repeat_flag && ($work_date < '2023-09-19')) || $repeat_work_idx != null){ ?>
															<li>
																<button class="tdw_list_r <?=$repeat_flag?" on":""?>" id="tdw_list_repeat_info_new" value="<?php echo $idx?>"><span>반복설정</span></button>
															</li>
														<?php }else{?>
															<li>
																<button class="tdw_list_r <?=$repeat_flag?" on":""?>" id="tdw_list_repeat_new" value="<?php echo $idx?>"><span>반복설정</span></button>
															</li>
														<?php } ?>
													<?php } ?>
													
													<?//일정변경?>
													<?//나의업무, 공유업무작성, 보고업무작성, 요청업무작성?>
													<?if(($work_flag=='2' && $work_idx==null) || ($share_flag=='1' && $work_idx==null) || ($work_flag=='1' && $work_idx==null) || ($work_flag=='3' && $work_idx==null)){?>
														<li>
															<div class ="tdw_list_c">
																<input class="tdw_list_date tdw_list_cc" type="text" id="listdate_<?=$idx?>" value="날짜변경" readonly>
															</div>
														</li>
													<?}?>
													<?//일정변경?>
													<?//나의업무, 공유업무작성, 보고업무작성, 요청업무작성?>
													<?if(($work_stime && $work_etime && $work_flag == '2' && $share_flag == '0' && $state == '0' && $decide_flag > '1')){?>
														<li>
															<button class="tdw_list_time tdw_list_t" id="tdw_list_time" value="<?=$idx?>" title="시간변경"><span>시간변경</span></button>
														</li>
													<?}?>
													<li>
														<?if($work_flag!='4'){
															if($notice_flag){?>
																<?if($user_id == $work_email){?>
																	<button class="tdw_list_del tdw_list_d" title="삭제" id="notice_list_del" value="<?=$idx?>"><span>삭제</span></button>
																<?}else{?>
																	<button class="tdw_list_del tdw_list_d" title="삭제" value="<?=$idx?>"><span>삭제</span></button>
																<?}?>
															<?}else{?>
															<?//업무글삭제?>
																<?if($user_id == $work_email && $share_flag == 0 && $work_flag == 2){?>
																	<button class="tdw_list_del tdw_list_d" title="삭제" id="tdw_list_per_del" value="<?=$idx?>"><span>삭제</span></button>
																<?}else if($user_id == $work_email){?>
																	<button class="tdw_list_del tdw_list_d" title="삭제" id="tdw_list_del" value="<?=$idx?>"><span>삭제</span></button>
																<?}else{?>
																	<button class="tdw_list_del tdw_list_d" title="삭제" value="<?=$idx?>"><span>삭제</span></button>
																<?}?>
															<?}
															}
														?>
													</li>
													<?php if($chkMobile == '1'){?>
													<li>
														<button class="tdw_list_cancel" id="tdw_list_cancel" title="닫기"><span>닫기</span></button>
													</li>
													<?php }?>
													</ul>
												</div>
											</div>
										</div>
									</div>

									<?//첨부파일 정보
									//나의업무, 요청업무
									if(in_array($work_flag, array('2','3'))){
										if($tdf_files[$work_com_idx][file_path]){?>
											<div class="tdw_list_file">
												<?
												$tdf_files_cnt = count($tdf_files[$work_com_idx][file_path]);
												for($k=0; $k<$tdf_files_cnt; $k++){?>
													<div class="tdw_list_file_box">
														<button class="btn_list_file" id="tdw_list_file_<?=$tdf_files[$work_com_idx][num][$k]?>" value="<?=$tdf_files[$work_com_idx][idx][$k]?>"><span><?=$tdf_files[$work_com_idx][file_real_name][$k]?></span></button>
														<?//보고업무 작성한 사용자만 삭제
														if($user_id==$tdf_files[$work_com_idx][email][$k]){?>
															<button class="btn_list_file_del" id="btn_list_fdel" value="<?=$tdf_files[$work_com_idx][idx][$k]?>" title="삭제"><span>삭제</span></button>
														<?}?>
													</div>
												<?}?>
											</div>
										<?}?>
									<?}?>
								</div>


								<?//보고업무

								if($work_flag=='1'){

								?>

									<div class="tdw_list_report_area">
										<div class="tdw_list_report_area_in<?=$report_view_in?>" id="tdw_list_report_area_in_<?=$idx?>">
											<div class="tdw_list_report_desc">
												<div class="tdw_list_report_conts">
													<?if($user_id==$report_email){?>
														<span class="tdw_list_report_conts_txt" id="tdw_list_report_conts_txt_<?=$idx?>"><?=$week_works[$workdate][contents][$j]?></span>
													<?}else{?>
														<span class="tdw_list_report_conts_txt"><?=$week_works[$workdate][contents][$j]?></span>
													<?}?>
													<em class="tdw_list_report_conts_date"><?=$work_his?></em>
													<div class="tdw_list_report_regi" id="tdw_list_report_regi_<?=$idx?>">
														<textarea name="" class="textarea_regi" id="tdw_report_edit_<?=$idx?>"><?=strip_tags($week_works[$workdate][contents][$j])?></textarea>
														<div class="btn_regi_box">
															<button class="btn_regi_submit" id="btn_report_submit" value="<?=$idx?>"><span>확인</span></button>
															<button class="btn_regi_cancel"><span>취소</span></button>
														</div>
													</div>
												</div>
											</div>

											<?//첨부파일 정보
											if($tdf_files[$work_com_idx][file_path]){
											?>
												<div class="tdw_list_file">
													<?
													$tdf_files_cnt = count($tdf_files[$work_com_idx][file_path]);
													for($k=0; $k<$tdf_files_cnt; $k++){?>
														<div class="tdw_list_file_box">
															<button class="btn_list_file" id="tdw_list_file_<?=$k?>" value="<?=$tdf_files[$work_com_idx][idx][$k]?>"><span><?=$tdf_files[$work_com_idx][file_real_name][$k]?></span></button>
															<?//보고업무 작성한 사용자만 삭제
															if($user_id==$report_email){?>
																<button class="btn_list_file_del" id="btn_list_fdel" value="<?=$tdf_files[$work_com_idx][idx][$k]?>" title="삭제"><span>삭제</span></button>
															<?}?>
														</div>
													<?}?>
												</div>
											<?}?>

										</div>


										<div class="tdw_list_report_onoff"<?=$report_view_bt_style?>>
											<button class="btn_list_report_onoff<?=$report_view_bt?>" id="btn_list_report_onoff_<?=$idx?>" value="<?=$idx?>" <?if(trim($report_view_bt)=="on"){ echo "title='보고 접기'"; }else{ echo "title='보고 펼치기'"; }?>><span>보고 접기/펼치기</span></button>
										</div>
									</div>
								<?}?>

								<!-- <?if($work_flag=='2' && $share_flag=='0' && $notice_flag == '0'){?>
									<div class="tdw_list_work_onoff"<?=$work_view_bt_style?>>
										<button class="btn_list_work_onoff<?=($comment_list[$work_com_idx]?" memo_on": "");?><?=$work_view_bt?>" id="btn_list_work_onoff_<?=$idx?>" value="<?=$idx?>" <?if(trim($work_view_bt)=="on"){ echo "title='공유 접기'";}else{ echo "title='업무 펼치기'"; }?>><span>업무 접기/펼치기</span></button>
									</div>
								<?}?> -->
								
								<?if($share_flag && $work_idx){?>
									<div class="tdw_list_share_onoff"<?=$share_view_bt_style?>>
										<button class="btn_list_share_onoff<?=($comment_list[$work_com_idx]?" memo_on": "");?><?=$share_view_bt?>" id="btn_list_share_onoff_<?=$idx?>" value="<?=$idx?>" <?if(trim($share_view_bt)=="on"){ echo "title='공유 접기'"; }else{ echo "title='공유 펼치기'"; }?>><span>공유 접기/펼치기</span></button>
									</div>
								<?}?>

								<?if($work_flag=='3'){?>
									<div class="tdw_list_req_onoff"<?=$req_view_bt_style?>>
										<button class="btn_list_req_onoff<?=($comment_list[$work_com_idx]?" memo_on": "");?><?=$req_view_bt?>" id="btn_list_req_onoff_<?=$idx?>" value="<?=$idx?>" <?if(trim($req_view_bt)=="on"){ echo "title='요청 접기'";}else{ echo "title='요청 펼치기'"; }?>><span>요청 접기/펼치기</span></button>
									</div>
								<?}?>


								<div class="tdw_list_memo_area">
									<div class="tdw_list_memo_area_in<?=$memo_view_in?>" id="tdw_list_memo_area_in_<?=$idx?>">

										<?//댓글리스트

										//요청업무
										if($work_flag == '3'){?>
											<?if($comment_list[$work_com_idx]){?>
												<?
												for($k=0; $k<$comment_list_work_com_cnt; $k++){
													$comment_idx = $comment_list[$work_com_idx][cidx][$k];

													$chis = $comment_list[$work_com_idx][regdate][$k];
													$ymd = $comment_list[$work_com_idx][ymd][$k];
													$cmt_flag = $comment_list[$work_com_idx][cmt_flag][$k];

													if($chis){
														$chis = str_replace("  "," ", $chis);
														$chis_tmp = @explode(" ", $chis);
														if ($chis_tmp[2] == "PM"){
															$after = "오후 ";
														}else{
															$after = "오전 ";
														}
														$ctime = @explode(":", $chis_tmp[1]);
														$chiss = $ymd . " " . $after . $ctime[0] .":". $ctime[1];
													}
													
													if($cmt_flag != 2){
													?>
													<div class="tdw_list_memo_desc" id="comment_list_<?=$comment_idx?>" >

														<?if($cmt_flag){?>
															<!-- 좋아요 변경으로 인한 코드 -->
															<?if($work_send_like_name[$comment_idx][send]){?>
																<div class="tdw_list_memo_name"><?=$work_send_like_name[$comment_idx][send]?></div>
															<?}else{?>
																<div class="tdw_list_memo_name ai">AI</div>
															<?}?>
														<?}else{?>
															<div class="tdw_list_memo_name" id="3"><?=$comment_list[$work_com_idx][name][$k]?></div>
														<?}?>

														<!-- 좋아요 변경으로 인한 코드(김정훈) -->
														<?if($cmt_flag){?>
															<?//좋아요 보낸 내역이 있을때
															if($work_send_like_name[$comment_idx][send]){?>
																<button class="btn_memo_jjim on" value="<?=$comment_idx?>"><span>좋아요</span></button>
															<?}?>
														<?}?>

														<div class="tdw_list_memo_conts">
															<?if(!$cmt_flag && $user_id==$comment_list[$work_com_idx][email][$k]){?>
																<span class="tdw_list_memo_conts_txt" id="tdw_list_memo_conts_txt_<?=$comment_idx?>"><?=$comment_list[$work_com_idx][comment][$k]?></span>
															<?}else if($cmt_flag && $work_send_like_name[$comment_idx][send]){?>
																<span class="tdw_list_memo_conts_txt"><?=$comment_list[$work_com_idx][comment][$k]?></span>
															<?}else{?>
																<span class="tdw_list_memo_conts_txt"><?=$comment_list[$work_com_idx][comment][$k]?></span>
															<?}?>

															<em class="tdw_list_memo_conts_date"><?=$chiss?>

																<?//ai글 일때, 공유요청한 사람만 뜨게
																if($cmt_flag && $work_link_coin_arr[$idx] && !$my_like_arr[$comment_idx]){?>
																	<button class="btn_req_100c" id="btn_req_100c" title="100코인" value="<?=$comment_list[$work_com_idx][cidx][$k]?>"><span>100코인</span></button>
																<?}?>

																<?//자동 ai댓글?>
																<?if($cmt_flag){?>

																	<?if($work_link_coin_arr[$idx] && !$my_like_arr[$comment_idx]){?>
																		<?if($click_like_arr[$comment_idx]){?>
																			<button class="btn_memo_jjim on" value="<?=$comment_idx?>"><span>좋아요</span></button>
																		<?}else{?>
																			<button class="btn_memo_jjim" id="btn_memo_jjim_<?=$comment_idx?>" value="<?=$comment_idx?>"><span>좋아요</span></button>
																		<?}?>
																	<?}?>

																<?}else{?>
																	<?if(!$my_like_arr[$comment_idx]){?>
																		<?if($cli_like_arr[$comment_idx]){?>
																			<button class="btn_memo_jjim on" value="<?=$comment_idx?>"><span>좋아요</span></button>
																		<?}else{?>
																			<button class="btn_memo_jjim" id="btn_memo_jjim_<?=$comment_idx?>" value="<?=$comment_idx?>"><span>좋아요</span></button>
																		<?}?>
																	<?}?>
																<?}?>

															<?if(!$cmt_flag && $user_id==$comment_list[$work_com_idx][email][$k]){?>
																<button class="btn_memo_del" id="btn_memo_del" value="<?=$comment_idx?>"><span>삭제</span></button>
															<?}?>
															</em>

															<div class="tdw_list_memo_regi" id="tdw_wlist_memo_regi_<?=$comment_idx?>">
																<textarea name="" class="textarea_regi" id="tdw_wcomment_edit_<?=$comment_idx?>"><?=$comment_list[$work_com_idx][comment_strip][$k]?></textarea>
																<div class="btn_regi_box">
																	<button class="btn_regi_submit" id="btn_wcomment_submit" value="<?=$comment_idx?>"><span>확인</span></button>
																	<button class="btn_regi_cancel" id="btn_wregi_cancel" value="<?=$comment_idx?>"><span>취소</span></button>
																</div>
															</div>
														</div>
													</div>
													<?}?>
												<?}?>
											<?}?>

										<?}else{?>

											<?//받은업무
												if ($work_idx){?>

														<?
														if($comment_list[$work_idx]){

															for($k=0; $k<$comment_list_work_cnt; $k++){
																$comment_idx = $comment_list[$work_idx][cidx][$k];

																$chis = $comment_list[$work_idx][regdate][$k];
																$ymd = $comment_list[$work_idx][ymd][$k];
																$cmt_flag = $comment_list[$work_idx][cmt_flag][$k];
																if($chis){
																	$chis = str_replace("  "," ", $chis);
																	$chis_tmp = @explode(" ", $chis);
																	if ($chis_tmp[2] == "PM"){
																		$after = "오후 ";
																	}else{
																		$after = "오전 ";
																	}
																	$ctime = @explode(":", $chis_tmp[1]);
																	$chiss = $ymd . " " . $after . $ctime[0] .":". $ctime[1];
																}
															?>
															<div class="tdw_list_memo_desc" id="comment_list_<?=$comment_idx?>" >

																<?if($cmt_flag){?>
																	<!-- 좋아요 변경으로 인한 코드 -->
																	<?if($work_send_like_name[$comment_idx][send]){?>
																		<div class="tdw_list_memo_name"><?=$work_send_like_name[$comment_idx][send]?></div>
																	<?}else{?>
																		<div class="tdw_list_memo_name ai">AI</div>
																	<?}?>
																<?}else{?>
																	<div class="tdw_list_memo_name" id="2"><?=$comment_list[$work_idx][name][$k]?></div>
																<?}?>

																<!-- 좋아요 변경으로 인한 코드(김정훈) -->
																<?if($cmt_flag){?>
																	<?//좋아요 보낸 내역이 있을때
																	if($work_send_like_name[$comment_idx][send]){?>
																		<button class="btn_memo_jjim on" value="<?=$comment_idx?>"><span>좋아요</span></button>
																	<?}?>
																<?}?>

																<div class="tdw_list_memo_conts">
																	<?if(!$cmt_flag && $user_id==$comment_list[$work_idx][email][$k]){?>
																		<!-- 일반 메모 -->
																		<span class="tdw_list_memo_conts_txt" id="tdw_list_memo_conts_txt_<?=$comment_idx?>"><?=$comment_list[$work_idx][comment][$k]?></span>
																	<?}else if($cmt_flag && $work_send_like_name[$comment_idx][send]){?>
																		<!-- 좋아요 받았을 때 문장 -->
																		<span class="tdw_list_memo_conts_txt"><?=$comment_list[$work_idx][comment][$k]?></span>
																	<?}else{?>
																		<!-- AI 문장 -->
																		<span class="tdw_list_memo_conts_txt"><?=$comment_list[$work_idx][comment][$k]?></span>
																	<?}?>

																	<em class="tdw_list_memo_conts_date"><?=$chiss?>

																		<?//자동 ai댓글?>
																		<?if($cmt_flag){?>

																		<?}else{?>

																			<?if($user_id!=$comment_list[$work_idx][email][$k]){?>
																				<?if($cli_like_arr[$comment_idx]){?>
																					<button class="btn_memo_jjim on" value="<?=$comment_idx?>"><span>좋아요</span></button>
																				<?}else{?>
																					<button class="btn_memo_jjim" id="btn_memo_jjim_<?=$comment_idx?>" value="<?=$comment_idx?>"><span>좋아요</span></button>
																				<?}?>
																			<?}?>

																		<?}?>

																	<?if($user_id==$comment_list[$work_idx][email][$k]){?>
																		<button class="btn_memo_del" id="btn_memo_del" value="<?=$comment_idx?>"><span>삭제</span></button>
																	<?}?>
																	</em>

																	<div class="tdw_list_memo_regi" id="tdw_wlist_memo_regi_<?=$comment_idx?>">
																		<textarea name="" class="textarea_regi" id="tdw_wcomment_edit_<?=$comment_idx?>"><?=$comment_list[$work_idx][comment_strip][$k]?></textarea>
																		<div class="btn_regi_box">
																			<button class="btn_regi_submit" id="btn_wcomment_submit" value="<?=$comment_idx?>"><span>확인</span></button>
																			<button class="btn_regi_cancel" id="btn_wregi_cancel" value="<?=$comment_idx?>"><span>취소</span></button>
																		</div>
																	</div>
																</div>
															</div>
														<?}?>
													<?}?>

											<?}else{?>

												<?
												//일반업무
												if($comment_list[$idx]){?>
													<?
													for($k=0; $k<$comment_list_cnt; $k++){
														$comment_idx = $comment_list[$idx][cidx][$k];
														$chis = $comment_list[$idx][regdate][$k];
														$ymd = $comment_list[$idx][ymd][$k];
														$cmt_flag = $comment_list[$idx][cmt_flag][$k];
														if($chis){
															$chis = str_replace("  "," ", $chis);
															$chis_tmp = @explode(" ", $chis);
															if ($chis_tmp[2] == "PM"){
																$after = "오후 ";
															}else{
																$after = "오전 ";
															}
															$ctime = @explode(":", $chis_tmp[1]);
															$chiss = $ymd . " " . $after . $ctime[0] .":". $ctime[1];
														}

													?>
														<div class="tdw_list_memo_desc" id="comment_list_<?=$comment_idx?>" >

															<?if($cmt_flag){?>
																<!-- 좋아요 변경으로 인한 코드 -->
																<?if($work_send_like_name[$comment_idx][send]){?>
																	<div class="tdw_list_memo_name"><?=$work_send_like_name[$comment_idx][send]?></div>
																<?}else{?>
																	<div class="tdw_list_memo_name ai">AI</div>
																<?}?>
															<?}else{?>
																<div class="tdw_list_memo_name" id="1"><?=$comment_list[$idx][name][$k]?></div>
															<?}?>

															<!-- 좋아요 변경으로 인한 코드(김정훈) -->
															<?if($cmt_flag){?>
																<?//좋아요 보낸 내역이 있을때
																if($work_send_like_name[$comment_idx][send]){?>
																	<button class="btn_memo_jjim on" value="<?=$comment_idx?>"><span>좋아요</span></button>
																<?}?>
															<?}?>

															<div class="tdw_list_memo_conts">
																<?if(!$cmt_flag && $user_id==$comment_list[$idx][email][$k]){?>
																	<!-- 일반 메모 -->
																	<span class="tdw_list_memo_conts_txt" id="tdw_list_memo_conts_txt_<?=$comment_idx?>"><?=$comment_list[$idx][comment][$k]?></span>
																<?}else if($cmt_flag && $work_send_like_name[$comment_idx][send]){?>
																	<!-- 좋아요 받았을 때 문장 -->
																	<span class="tdw_list_memo_conts_txt"><?=$comment_list[$idx][comment][$k]?></span>
																<?}else{?>
																	<!-- AI 문장 -->
																	<span class="tdw_list_memo_conts_txt"><?=$comment_list[$idx][comment][$k]?></span>
																<?}?>

																<em class="tdw_list_memo_conts_date"><?=$chiss?>

																	<?//자동 ai댓글?>
																	<?if($cmt_flag){?>

																	<?}else{?>

																		<?if($user_id!=$comment_list[$idx][email][$k]){?>
																			<?if($cli_like_arr[$comment_idx]){?>
																				<button class="btn_memo_jjim on" value="<?=$comment_idx?>"><span>좋아요</span></button>
																			<?}else{?>
																				<button class="btn_memo_jjim" id="btn_memo_jjim_<?=$comment_idx?>" value="<?=$comment_idx?>"><span>좋아요</span></button>
																			<?}?>
																		<?}?>

																	<?}?>

																<?if($user_id==$comment_list[$idx][email][$k]){?>
																	<button class="btn_memo_del" id="btn_memo_del" value="<?=$comment_idx?>"><span>삭제</span></button>
																<?}?>
																</em>

																<div class="tdw_list_memo_regi" id="tdw_wlist_memo_regi_<?=$comment_idx?>">
																	<textarea name="" class="textarea_regi" id="tdw_wcomment_edit_<?=$comment_idx?>"><?=$comment_list[$idx][comment_strip][$k]?></textarea>
																	<div class="btn_regi_box">
																		<button class="btn_regi_submit" id="btn_wcomment_submit" value="<?=$comment_idx?>"><span>확인</span></button>
																		<button class="btn_regi_cancel" id="btn_wregi_cancel" value="<?=$comment_idx?>"><span>취소</span></button>
																	</div>
																</div>
															</div>
														</div>
													<?}?>
												<?}?>
											<?}?>
										<?}?>
									</div>

									<?if($comment_list[$work_com_idx]){?>
										<div class="tdw_list_memo_onoff" <?=$memo_view_bt_style?>>
											<button class="btn_list_memo_onoff<?=$memo_view_bt?>" id="btn_list_memo_onoff_<?=$idx?>" value="<?=$idx?>" <?//if(trim($memo_view_bt)=="on"){ echo "title='메모 접기!'"; }else{ echo "title='메모 펼치기!'"; }?>><span>메모 접기/펼치기</span></button>
										</div>
									<?}?>
								</div>
							</li>
						<?}?>
					</ul>

					<?if(!$search){?>
						<?if($workdate <= TODATE){?>
							<div class="tdw_feeling_banner<?=$review_info_arr[$workdate][work_idx]?" btn_ff_0".$review_info_arr[$workdate][work_idx]."":""?>" id="tdw_feeling_banner_<?=$workdate?>">
								<div class="tdw_fb_in">
									<strong></strong>
									<p id="feeling_banner_<?=$workdate?>"><?=$review_info_arr[$workdate][comment]?"".$review_info_arr[$workdate][comment]."":"오늘 하루는 어떤가요?"?></p>
									<button class="btn_feeling_banner" id="btn_feeling_banner_<?=$workdate?>" value="<?=$workdate?>"><span>오늘 한 줄 소감</span></button>
								</div>
							</div>
						<?}?>
					<?}?>
				</div>

			<?php
			echo "timer:::".php_timer();}

			echo "|".$ex_wdate;
    }else{?>
			<div class="tdw_list_none">
				<strong><span><?=$list_result_text?></span></strong>
			</div>

		<?php

		}

		//주간읽음처리
		work_read_check($user_id, "week", $monthday, $sunday);

	//월간업무
	}

	if($_COOKIE[read_date]){
		setcookie('read_date', '', time()-3600 , '/', C_DOMAIN);
	}
	exit;
}
ob_end_flush();
?>
