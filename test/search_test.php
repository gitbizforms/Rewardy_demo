<?php
ob_start();
$home_dir = str_replace( basename(__DIR__) , "" , __DIR__ );
//연결된 도메인으로 분리

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

	//예약업무 예약기능
	$sql = "select idx, title, type_flag from work_decide where state='0' and companyno='".$companyno."' order by sort asc";
	$decide_info = selectAllQuery($sql);


	//알림기능
	$sql = "select idx, title from work_notice where state='0' and companyno='".$companyno."' order by sort asc";
	$notice_info = selectAllQuery($sql);
	$notice_info_cnt = count($notice_info[idx]);
	for($i=0; $i<$notice_info_cnt; $i++){
		$idx = $notice_info[idx][$i];
		$title = $notice_info[title][$i];
		$notice_list[$idx] = $title;
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

		//업무요청 보낸사람
		$where = " and workdate between '".$monthday."' and '".$sunday."'";
		if($_SERVER[HTTP_HOST] == T_DOMAIN){
			$sql = "select idx, work_idx, name from work_todaywork_user where state='0' and companyno='".$companyno."'".$where." order by name asc";
		}else{
			$sql = "select idx, work_idx, name from work_todaywork_user where state='0' and companyno='".$companyno."'".$where." order by name collate Korean_Wansung_CI_AS asc";
		}
		$work_user_info = selectAllQuery($sql);
		if($work_user_info[idx]){
			$work_user_info_cnt = count($work_user_info[idx]);
			for($i=0; $i<$work_user_info_cnt; $i++){
				$work_user_list[$work_user_info[work_idx][$i]][] = $work_user_info[name][$i];
			}
		}

		//업무요청 받은사용자
		if($_SERVER[HTTP_HOST] == T_DOMAIN){
			$sql = "select idx, work_idx, work_email, work_name, name from work_todaywork_user where state='0' and companyno='".$companyno."' and email='".$user_id."'".$where." order by name asc";
		}else{
			$sql = "select idx, work_idx, work_email, work_name, name from work_todaywork_user where state='0' and companyno='".$companyno."' and email='".$user_id."'".$where." order by name collate Korean_Wansung_CI_AS asc";
		}
		$work_to_user_info = selectAllQuery($sql);
		if($work_to_user_info[idx]){
			$work_to_user_list[work_name] = @array_combine($work_to_user_info[work_idx], $work_to_user_info[work_name]);
		}


		//업무공유 보낸사람
		if($_SERVER[HTTP_HOST] == T_DOMAIN){
			$sql = "select idx, work_idx, name from work_todaywork_share where state='0' and companyno='".$companyno."'".$where." order by name asc";
		}else{
			$sql = "select idx, work_idx, name from work_todaywork_share where state='0' and companyno='".$companyno."'".$where." order by name collate Korean_Wansung_CI_AS asc";
		}
		$work_share_send_info = selectAllQuery($sql);
		if($work_share_send_info[idx]){
			$work_share_send_info_cnt = count($work_share_send_info[idx]);
			for($i=0; $i<$work_share_send_info_cnt; $i++){
				$work_share_send_list[$work_share_send_info[work_idx][$i]][] = $work_share_send_info[name][$i];
			}
		}

		//업무공유 받은사람
		if($_SERVER[HTTP_HOST] == T_DOMAIN){
			$sql = "select idx, work_idx, work_email, work_name, name from work_todaywork_share where state='0' and companyno='".$companyno."' and email='".$user_id."'".$where." order by name asc";
		}else{
			$sql = "select idx, work_idx, work_email, work_name, name from work_todaywork_share where state='0' and companyno='".$companyno."' and email='".$user_id."'".$where." order by name collate Korean_Wansung_CI_AS asc";
		}
		$work_share_info = selectAllQuery($sql);
		if($work_share_info[idx]){
			$work_share_info_cnt = count($work_share_info[idx]);
			for($i=0; $i<$work_share_info_cnt; $i++){
				$work_share_list[work_name] = @array_combine($work_share_info[work_idx], $work_share_info[work_name]);
			}
		}


		//보고업무한 사용자
		if($_SERVER[HTTP_HOST] == T_DOMAIN){
			$sql = "select idx, work_idx, name from work_todaywork_report where state='0'".$where." order by name asc";
		}else{
			$sql = "select idx, work_idx, name from work_todaywork_report where state='0'".$where." order by name collate Korean_Wansung_CI_AS asc";
		}
		$report_user_info = selectAllQuery($sql);
		if($report_user_info[idx]){
			$report_user_info_cnt = count($report_user_info[idx]);
			for($i=0; $i<$report_user_info_cnt; $i++){
				$work_report_user_list[$report_user_info[work_idx][$i]][] = $report_user_info[name][$i];
			}
		}

		//보고업무 받은사용자
		if($_SERVER[HTTP_HOST] == T_DOMAIN){
			$sql = "select idx, work_idx, work_email, work_name, name from work_todaywork_report where state='0' and email='".$user_id."'".$where." order by name asc";
		}else{
			$sql = "select idx, work_idx, work_email, work_name, name from work_todaywork_report where state='0' and email='".$user_id."'".$where." order by name collate Korean_Wansung_CI_AS asc";
		}
		$report_user_to_info = selectAllQuery($sql);
		if($report_user_to_info[idx]){
			$work_report_uer_to_list[work_name] = @array_combine($report_user_to_info[work_idx], $report_user_to_info[work_name]);
		}


		//업무요청한 사용자(읽음체크)
		$sql = "select idx, work_idx, name, read_flag from work_todaywork_user where state='0' and companyno='".$companyno."'".$where." order by idx desc";
		$work_user_req_info = selectAllQuery($sql);
		if($work_user_req_info[idx]){
			$work_user_req_info_cnt = count($work_user_req_info[idx]);
			for($i=0; $i<$work_user_req_info_cnt; $i++){
				$work_user_idx = $work_user_req_info[idx][$i];
				$work_user_work_idx = $work_user_req_info[work_idx][$i];
				//$work_user_list[$work_user_work_idx][] = $work_user_req_info[name][$i];
				if($work_user_work_idx){
					$work_req_read[$work_user_work_idx][all]++;
					if($work_user_req_info[read_flag][$i]=='1'){
						$work_req_read[$work_user_work_idx][read]++;
					}
				}
			}
		}

		//보고한 사용자(읽음체크)
		$sql = "select idx, work_idx, name, read_flag from work_todaywork_report where state='0' and companyno='".$companyno."'".$where." order by idx desc";
		$work_user_report_info = selectAllQuery($sql);
		if($work_user_report_info[idx]){
			$work_user_report_info_cnt = count($work_user_report_info[idx]);
			for($i=0; $i<$work_user_report_info_cnt; $i++){
				$work_user_idx = $work_user_report_info[idx][$i];
				$work_user_work_idx = $work_user_report_info[work_idx][$i];
				//$work_user_list[$work_user_work_idx][] = $work_user_report_info[name][$i];
				if($work_user_work_idx){
					$work_report_read[$work_user_work_idx][all]++;
					if($work_user_report_info[read_flag][$i]=='1'){
						$work_report_read[$work_user_work_idx][read]++;
					}
				}
			}
		}

		//공유한 사용자(읽음체크)
		$sql = "select idx, work_idx, name, read_flag from work_todaywork_share where state='0' and companyno='".$companyno."'".$where." order by idx desc";
		$work_user_share_info = selectAllQuery($sql);
		if($work_user_share_info[idx]){
			$work_user_share_info_cnt =  count($work_user_share_info[idx]);
			for($i=0; $i<$work_user_share_info_cnt; $i++){
				$work_user_idx = $work_user_share_info[idx][$i];
				$work_user_work_idx = $work_user_share_info[work_idx][$i];
				//$work_user_list[$work_user_work_idx][] = $work_user_share_info[name][$i];
				if($work_user_work_idx){
					$work_share_read[$work_user_work_idx][all]++;
					if($work_user_share_info[read_flag][$i]=='1'){
						$work_share_read[$work_user_work_idx][read]++;
					}
				}
			}
		}


		//업무 댓글
		$where = " and b.workdate between '".$monthday."' and '".$sunday."'";
		if($_SERVER[HTTP_HOST] == T_DOMAIN){
			$sql = "select a.idx as cidx, a.link_idx, a.work_idx, a.email, a.name, a.comment, a.cmt_flag, CASE WHEN a.editdate is not null then date_format(a.editdate, '%Y-%m-%d') WHEN a.editdate is null then date_format(a.regdate, '%Y-%m-%d') end as ymd,";
			$sql = $sql .= " CASE WHEN a.editdate is not null then date_format( a.editdate , '%m/%d/%y %l:%i:%s %p') WHEN a.editdate is null then date_format( a.regdate , '%m/%d/%y %l:%i:%s %p') end as regdate, b.idx from work_todaywork_comment as a left join work_todaywork as b on(a.link_idx=b.idx) where a.state='0' and a.companyno='".$companyno."' and b.workdate='".$wdate."' order by a.regdate desc";
		}else{
			$sql = "select a.idx as cidx, a.link_idx, a.work_idx, a.email, a.name, a.comment, a.cmt_flag, CASE WHEN a.editdate is not null then CONVERT(varchar(10), a.editdate, 120) WHEN a.editdate is null then CONVERT(varchar(10), a.regdate, 120) end as ymd,";
			$sql = $sql .= " CASE WHEN a.editdate is not null then CONVERT(varchar(20), a.editdate, 22) WHEN a.editdate is null then CONVERT(varchar(20), a.regdate, 22) end as regdate, b.idx from work_todaywork_comment as a left join work_todaywork as b on(a.link_idx=b.idx) where a.state='0' and a.companyno='".$companyno."' ".$where." order by a.regdate desc";
		}

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


		//좋아요 리스트
		$like_flag_list = array();
		$sql = "select idx, email,service, work_idx, send_email, like_flag from work_todaywork_like where state='0' and companyno='".$companyno."' and send_email='".$user_id."' and workdate between '".$monthday."' and '".$sunday."'";
		$like_info = selectAllQuery($sql);
		$like_info_cnt = count($like_info[idx]);
		for($i=0; $i<$like_info_cnt; $i++){
			$like_info_idx = $like_info[idx][$i];
			$like_info_email = $like_info[email][$i];
			$like_info_work_idx = $like_info[work_idx][$i];
			$like_info_like_flag = $like_info[like_flag][$i];
			$like_info_send_email = $like_info[send_email][$i];
			$work_like_list[$like_info_work_idx] = $like_info_idx;
		}

		//좋아요 받은내역
		$work_like_receive = array();
		$sql = "select idx, email,service, work_idx, send_email, like_flag from work_todaywork_like where state='0' and email='".$user_id."' and workdate between '".$monthday."' and '".$sunday."'";
		$like_info = selectAllQuery($sql);
		$like_info_cnt = count($like_info[idx]);
		for($i=0; $i<$like_info_cnt; $i++){
			$like_info_idx = $like_info[idx][$i];
			$like_info_email = $like_info[email][$i];
			$like_info_work_idx = $like_info[work_idx][$i];
			$like_info_like_flag = $like_info[like_flag][$i];
			$like_info_send_email = $like_info[send_email][$i];
			$work_like_receive[$like_info_work_idx] = $like_info_idx;
		}

		//오늘한줄소감
		if($_SERVER[HTTP_HOST] == T_DOMAIN){
			$sql = "select idx, work_idx, comment, workdate, DATE_FORMAT( regdate , '%Y%m%d') as wdate from work_todaywork_review where state='0' and companyno='".$companyno."' and email='".$user_id."' and workdate between '".$monthday."' and '".$sunday."'";
		}else{
			$sql = "select idx, work_idx, comment, workdate, convert(char(8), regdate, 112) as wdate from work_todaywork_review where state='0' and companyno='".$companyno."' and email='".$user_id."' and workdate between '".$monthday."' and '".$sunday."'";
		}
		$review_info = selectAllQuery($sql);
		$review_info_cnt = count($review_info[idx]);
		for($i=0; $i<$review_info_cnt; $i++){
			$review_info_idx = $review_info[idx][$i];
			$review_info_workdate = $review_info[workdate][$i];
			$review_info_comment = $review_info[comment][$i];
			$review_info_work_idx = $review_info[work_idx][$i];
			$review_info_wdate = $review_info[wdate][$i];

			$review_info_arr[$review_info_workdate][idx] = $review_info_idx;
			$review_info_arr[$review_info_workdate][comment] = $review_info_comment;
			$review_info_arr[$review_info_workdate][work_idx] = $review_info_work_idx;
			$review_info_arr[$review_info_workdate][workdate] = $review_info_workdate;
		}


		//첨부파일정보
		$sql = "select idx, work_idx, num, email, file_path, file_name, file_real_name, workdate from work_filesinfo_todaywork where state='0' and companyno='".$companyno."' and workdate between '".$monthday."' and '".$sunday."'";
		$todaywork_file_info = selectAllQuery($sql);
		$todaywork_file_info = count($todaywork_file_info[idx]);
		for($i=0; $i<$todaywork_file_info; $i++){

			$tdf_idx = $todaywork_file_info[idx][$i];
			$tdf_num = $todaywork_file_info[num][$i];
			$tdf_email = $todaywork_file_info[email][$i];
			$tdf_work_idx = $todaywork_file_info[work_idx][$i];
			$tdf_file_path = $todaywork_file_info[file_path][$i];
			$tdf_file_name = $todaywork_file_info[file_name][$i];
			$tdf_file_real_name = $todaywork_file_info[file_real_name][$i];

			//검색된 단어가 있을경우
			if($search){
				$tdf_file_real_name = keywordHightlight($search, $tdf_file_real_name);
			}

			$tdf_files[$tdf_work_idx][idx][] = $tdf_idx;
			$tdf_files[$tdf_work_idx][num][] = $tdf_num;
			$tdf_files[$tdf_work_idx][email][] = $tdf_email;
			$tdf_files[$tdf_work_idx][email][] = $todaywork_file_info[email][$i];
			$tdf_files[$tdf_work_idx][file_path][] = $tdf_file_path;
			$tdf_files[$tdf_work_idx][tdf_file_name][] = $tdf_file_name;
			$tdf_files[$tdf_work_idx][file_real_name][] = $tdf_file_real_name;
		}


		//조건절
		$where = " and workdate between '".$monthday."' and '".$sunday."'";

		//보고업무
		if($_SERVER[HTTP_HOST] == T_DOMAIN){
			$sql = "select idx, state, work_flag, decide_flag, email, name, work_idx, repeat_flag, notice_flag, share_flag, title, contents, workdate, date_format( regdate , '%m/%d/%y %l:%i:%s %p') as reg, date_format(regdate, '%H:%i') as his from work_todaywork where state!='9'";
			$sql = $sql .=" and companyno='".$companyno."' and work_flag='1' and work_idx is null".$where;
			$sql = $sql .= " order by sort asc, idx desc";
		}else{
			$sql = "select idx, state, work_flag, decide_flag, email, name, work_idx, repeat_flag, notice_flag, share_flag, title, contents, workdate, CONVERT(varchar(20), regdate, 22) as reg, CONVERT(CHAR(5), regdate, 8) as his from work_todaywork where state!='9'";
			$sql = $sql .=" and companyno='".$companyno."' and work_flag='1' and work_idx is null".$where;
			$sql = $sql .= " order by sort asc, idx desc";
		}
		$works_report_info = selectAllQuery($sql);
		$works_report_info_cnt = count($works_report_info[idx]);
		for($i=0; $i<$works_report_info_cnt; $i++){
			$work_report_idx = $works_report_info[idx][$i];
			$work_report_title = $works_report_info[title][$i];
			$work_report_contents = $works_report_info[contents][$i];
			$work_report_email = $works_report_info[email][$i];
			$work_report_name = $works_report_info[name][$i];
			$work_report_workdate = $works_report_info[workdate][$i];
			$work_report_reg = $works_report_info[reg][$i];


			if($search){
				$work_report_title = keywordHightlight($search, $work_report_title);
				$work_report_contents = keywordHightlight($search, $work_report_contents);
			}


			$work_report_list[$work_report_idx][title] = $work_report_title;
			$work_report_list[$work_report_idx][contents] = $work_report_contents;
			$work_report_list[$work_report_idx][email] = $work_report_email;
			$work_report_list[$work_report_idx][name] = $work_report_name;
			$work_report_list[$work_report_idx][workdate] = $work_report_workdate;
			$work_report_list[$work_report_idx][reg] = $work_report_reg;
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

				if($_SERVER[HTTP_HOST] == T_DOMAIN){
					$sql = "select a.idx, a.state, a.work_flag, a.part_flag, a.decide_flag, a.work_idx, a.repeat_flag, a.notice_flag, a.share_flag, date_format( a.regdate , '%Y.%m.%d') as ymd, date_format( a.regdate , '%H:%i') as his, a.memo_view, a.contents_view, a.title, a.contents as contents, a.email, a.name, a.req_date, a.workdate, a.regdate";
					$sql = $sql .= " from work_todaywork as a left join work_filesinfo_todaywork as b on(a.idx=b.work_idx or a.work_idx=b.work_idx)";
					$sql = $sql .= " where a.state!='9' and b.state='0' and a.companyno='".$companyno."' and a.email='".$user_id."'";
					$sql = $sql .= "".$where."";
					$sql = $sql .= " group by a.idx, a.state, a.work_flag, a.part_flag, a.decide_flag, a.work_idx, a.repeat_flag, a.notice_flag, a.share_flag,  a.memo_view, a.contents_view, a.title,  a.contents, a.email, a.name, a.req_date, a.workdate, a.regdate";
					$sql = $sql .= " order by a.idx desc";
				}else{
					$sql = "select a.idx, a.state, a.work_flag, a.part_flag, a.decide_flag, a.work_idx, a.repeat_flag, a.notice_flag, a.share_flag, CONVERT(CHAR(10), a.regdate, 102) as ymd, CONVERT(CHAR(5), a.regdate, 8) as his, a.memo_view, a.contents_view, a.title, convert(varchar(max) , a.contents) as contents, a.email, a.name, a.req_date, a.workdate, a.regdate";
					$sql = $sql .= " from work_todaywork as a left join work_filesinfo_todaywork as b on(a.idx=b.work_idx or a.work_idx=b.work_idx)";
					$sql = $sql .= " where a.state!='9' and b.state='0' and a.companyno='".$companyno."' and a.email='".$user_id."'";
					$sql = $sql .= "".$where."";
					$sql = $sql .= " group by a.idx, a.state, a.work_flag, a.part_flag, a.decide_flag, a.work_idx, a.repeat_flag, a.notice_flag, a.share_flag,  a.memo_view, a.contents_view, a.title,  a.contents, a.email, a.name, a.req_date, a.workdate, a.regdate";
					$sql = $sql .= " order by a.idx desc";
				}

				//메모검색
			}else if($search_kind=='memo'){

				if($_SERVER[HTTP_HOST] == T_DOMAIN){
					$sql = "(";
					$sql .= " select a.idx, a.state, a.work_flag, a.part_flag, a.decide_flag, a.work_idx, a.repeat_flag, a.notice_flag, a.share_flag, date_format( a.regdate , '%Y.%m.%d') as ymd, date_format( a.regdate , '%H:%i') as his, a.memo_view, a.contents_view, a.title, a.contents as contents, a.email, a.name, a.req_date, a.workdate, a.regdate, b.email as c_email from";
					$sql .= " (select idx, state, work_flag, part_flag, decide_flag, work_idx, repeat_flag, notice_flag, share_flag, date_format( a.regdate , '%Y.%m.%d') as ymd, date_format( a.regdate , '%H:%i') as his, memo_view, contents_view, title, a.contents as contents, email, name, req_date, workdate, regdate,companyno";
					$sql .= " from work_todaywork where workdate between '".$monthday."' and '".$sunday."' and state !='9') as a";
					$sql .= " left join (select work_idx,comment,email from work_todaywork_comment where workdate between '".$monthday."' and '".$sunday."' and state !='9') as b on (a.idx=b.work_idx)";
					$sql .= " where a.companyno='1' and (a.email = '".$user_id."' and b.comment like '%".$search."%' or a.contents like '%".$search."%')";
					$sql .= " ) union (";
					$sql .= " select a.idx, a.state, a.work_flag, a.part_flag, a.decide_flag, a.work_idx, a.repeat_flag, a.notice_flag, a.share_flag, date_format( a.regdate , '%Y.%m.%d') as ymd, date_format( a.regdate , '%H:%i') as his, a.memo_view, a.contents_view, a.title, a.contents as contents, a.email, a.name, a.req_date, a.workdate, a.regdate from ";
					$sql .= " (select idx, state, work_flag, part_flag, decide_flag, work_idx, repeat_flag, notice_flag, share_flag, date_format( a.regdate , '%Y.%m.%d') as ymd, date_format( a.regdate , '%H:%i') as his, memo_view, contents_view, title, a.contents as contents, email, name, req_date, workdate, regdate,companyno";
					$sql .= " from work_todaywork where workdate between '".$monthday."' and '".$sunday."' and state != '9' and email = '".$user_id."' ) as a";
					$sql .= " join (select comment,workdate,state,email,link_idx from work_todaywork_comment  where workdate between '".$monthday."' and '".$sunday."' and state != '9') as b on (a.idx = b.link_idx)";
					$sql .= " where a.companyno='1' and b.comment like '%".$search."%' and b.email != '".$user_id."' )";

				}else{

					$sql = "(";
					$sql .= " select a.idx, a.state, a.work_flag, a.part_flag, a.decide_flag, a.work_idx, a.repeat_flag, a.notice_flag, a.share_flag, CONVERT(CHAR(10), a.regdate, 102) as ymd, CONVERT(CHAR(5), a.regdate, 8) as his, a.memo_view, a.contents_view, a.title, convert(varchar(max), a.contents) as contents, a.email, a.name, a.req_date, a.workdate, a.regdate, b.email as c_email from";
					$sql .= " (select idx, state, work_flag, part_flag, decide_flag, work_idx, repeat_flag, notice_flag, share_flag, CONVERT(CHAR(10), regdate, 102) as ymd, CONVERT(CHAR(5), regdate, 8) as his, memo_view, contents_view, title, convert(varchar(max), contents) as contents, email, name, req_date, workdate, regdate,companyno";
					$sql .= " from work_todaywork where workdate between '".$monthday."' and '".$sunday."' and state !='9') as a";
					$sql .= " left join (select work_idx,comment,email from work_todaywork_comment where workdate between '".$monthday."' and '".$sunday."' and state !='9') as b on (a.idx=b.work_idx)";
					$sql .= " where a.companyno='1' and (a.email = '".$user_id."' and b.comment like '%".$search."%' or a.contents like '%".$search."%')";
					$sql .= " ) union (";
					$sql .= " select a.idx, a.state, a.work_flag, a.part_flag, a.decide_flag, a.work_idx, a.repeat_flag, a.notice_flag, a.share_flag, CONVERT(CHAR(10), a.regdate, 102) as ymd, CONVERT(CHAR(5), a.regdate, 8) as his, a.memo_view, a.contents_view, a.title, convert(varchar(max), a.contents) as contents, a.email, a.name, a.req_date, a.workdate, a.regdate from ";
					$sql .= " (select idx, state, work_flag, part_flag, decide_flag, work_idx, repeat_flag, notice_flag, share_flag, CONVERT(CHAR(10), regdate, 102) as ymd, CONVERT(CHAR(5), regdate, 8) as his, memo_view, contents_view, title, convert(varchar(max), contents) as contents, email, name, req_date, workdate, regdate,companyno";
					$sql .= " from work_todaywork where workdate between '".$monthday."' and '".$sunday."' and state != '9' and email = '".$user_id."' ) as a";
					$sql .= " join (select comment,workdate,state,email,link_idx from work_todaywork_comment  where workdate between '".$monthday."' and '".$sunday."' and state != '9') as b on (a.idx = b.link_idx)";
					$sql .= " where a.companyno='1' and b.comment like '%".$search."%' and b.email != '".$user_id."' )";

				}

			//전체검색
			}else if($search_kind=='all'){

				//조건
				$where = "";
				$where .= " and (a.contents like '%".$search."%' or b.comment like '%".$search."%' or c.file_real_name like '%".$search."%')";
				$where .= " and (a.workdate between '".$monthday."' and '".$sunday."' or b.workdate between '".$monthday."' and '".$sunday."' or c.workdate between '".$monthday."' and '".$sunday."' ) ";

				if($_SERVER[HTTP_HOST] == T_DOMAIN){
					$sql = "(";
					$sql .= " select a.idx, a.state, a.work_flag, a.part_flag, a.decide_flag, a.work_idx, a.repeat_flag, a.notice_flag, a.share_flag, date_format( a.regdate , '%Y.%m.%d') as ymd, date_format( a.regdate , '%H:%i') as his, a.memo_view, a.contents_view, a.title, a.contents as contents, a.email, a.name, a.req_date, a.workdate, a.regdate from";
					$sql .= " (select idx, state, work_flag, part_flag, decide_flag, work_idx, repeat_flag, notice_flag, share_flag, date_format( a.regdate , '%Y.%m.%d') as ymd, date_format( a.regdate , '%H:%i') as his, memo_view, contents_view, title, a.contents as contents, email, name, req_date, workdate, regdate,companyno";
					$sql .= " from work_todaywork where workdate between '".$monthday."' and '".$sunday."' and state !='9') as a";
					$sql .= " left join (select work_idx,comment,email from work_todaywork_comment where workdate between '".$monthday."' and '".$sunday."' and state !='9') as b on (a.idx=b.work_idx)";
					$sql .= " left join (select work_idx,file_real_name from work_filesinfo_todaywork where workdate between '".$monthday."' and '".$sunday."' and state ='0') as c on(a.idx=c.work_idx or a.work_idx=c.work_idx)";
					$sql .= " where a.companyno='1' and (a.email = '".$user_id."' and b.comment like '%".$search."%' or a.title like '%".$search."%' or a.contents like '%".$search."%' or c.file_real_name like '%".$search."%')";
					$sql .= " ) union (";
					$sql .= " select a.idx, a.state, a.work_flag, a.part_flag, a.decide_flag, a.work_idx, a.repeat_flag, a.notice_flag, a.share_flag, date_format( a.regdate , '%Y.%m.%d') as ymd, date_format( a.regdate , '%H:%i') as his, a.memo_view, a.contents_view, a.title, a.contents as contents, a.email, a.name, a.req_date, a.workdate, a.regdate from ";
					$sql .= " (select idx, state, work_flag, part_flag, decide_flag, work_idx, repeat_flag, notice_flag, share_flag, date_format( a.regdate , '%Y.%m.%d') as ymd, date_format( a.regdate , '%H:%i') as his, memo_view, contents_view, title, a.contents as contents, email, name, req_date, workdate, regdate,companyno";
					$sql .= " from work_todaywork where workdate between '".$monthday."' and '".$sunday."' and state != '9' and email = '".$user_id."' ) as a";
					$sql .= " join (select comment,workdate,state,email,link_idx from work_todaywork_comment  where workdate between '".$monthday."' and '".$sunday."' and state != '9') as b on (a.idx = b.link_idx)";
					$sql .= " where a.companyno='1' and b.comment like '%".$search."%' and b.email != '".$user_id."' )";

				}else{

					$sql = "(";
					$sql .= " select a.idx, a.state, a.work_flag, a.part_flag, a.decide_flag, a.work_idx, a.repeat_flag, a.notice_flag, a.share_flag, CONVERT(CHAR(10), a.regdate, 102) as ymd, CONVERT(CHAR(5), a.regdate, 8) as his, a.memo_view, a.contents_view, a.title, convert(varchar(max), a.contents) as contents, a.email, a.name, a.req_date, a.workdate, a.regdate from";
					$sql .= " (select idx, state, work_flag, part_flag, decide_flag, work_idx, repeat_flag, notice_flag, share_flag, CONVERT(CHAR(10), regdate, 102) as ymd, CONVERT(CHAR(5), regdate, 8) as his, memo_view, contents_view, title, convert(varchar(max), contents) as contents, email, name, req_date, workdate, regdate,companyno";
					$sql .= " from work_todaywork where workdate between '".$monthday."' and '".$sunday."' and state !='9' and email = '".$user_id."') as a";
					$sql .= " left join (select work_idx,comment,email from work_todaywork_comment where workdate between '".$monthday."' and '".$sunday."' and state !='9') as b on (a.idx=b.work_idx)";
					$sql .= " left join (select work_idx,file_real_name from work_filesinfo_todaywork where workdate between '".$monthday."' and '".$sunday."' and state ='0') as c on(a.idx=c.work_idx or a.work_idx=c.work_idx)";
					$sql .= " where a.companyno='1' and (b.comment like '%".$search."%' or a.title like '%".$search."%' or a.contents like '%".$search."%' or c.file_real_name like '%".$search."%')";
					$sql .= " ) union (";
					$sql .= " select a.idx, a.state, a.work_flag, a.part_flag, a.decide_flag, a.work_idx, a.repeat_flag, a.notice_flag, a.share_flag, CONVERT(CHAR(10), a.regdate, 102) as ymd, CONVERT(CHAR(5), a.regdate, 8) as his, a.memo_view, a.contents_view, a.title, convert(varchar(max), a.contents) as contents, a.email, a.name, a.req_date, a.workdate, a.regdate from ";
					$sql .= " (select idx, state, work_flag, part_flag, decide_flag, work_idx, repeat_flag, notice_flag, share_flag, CONVERT(CHAR(10), regdate, 102) as ymd, CONVERT(CHAR(5), regdate, 8) as his, memo_view, contents_view, title, convert(varchar(max), contents) as contents, email, name, req_date, workdate, regdate,companyno";
					$sql .= " from work_todaywork where workdate between '".$monthday."' and '".$sunday."' and state != '9' and email = '".$user_id."' ) as a";
					$sql .= " join (select comment,workdate,state,email,link_idx from work_todaywork_comment  where workdate between '".$monthday."' and '".$sunday."' and state != '9') as b on (a.idx = b.link_idx)";
					$sql .= " where a.companyno='1' and b.comment like '%".$search."%' and b.email != '".$user_id."' )";

				}


			}else{

				//업무검색
				if($_SERVER[HTTP_HOST] == T_DOMAIN){
					$sql ="select idx, state, work_flag, part_flag, decide_flag, work_idx, repeat_flag, notice_flag, share_flag, date_format( regdate , '%Y.%m.%d') as ymd, date_format( regdate , '%H:%i') as his, memo_view, contents_view, title, contents, contents1,";
					$sql = $sql .=" email, name, req_date, workdate, regdate ";
					$sql = $sql .= " FROM work_todaywork where state!='9' and companyno='".$companyno."' and email='".$user_id."'".$where."";
					$sql = $sql .= " order by workdate asc";
				}else{
					$sql ="select idx, state, work_flag, part_flag, decide_flag, work_idx, repeat_flag, notice_flag, share_flag, CONVERT(CHAR(10), regdate, 102) as ymd, CONVERT(CHAR(5), regdate, 8) as his, memo_view, contents_view, title, convert(varchar(max) , contents) as contents, convert(varchar(max) , contents1) as contents1,";
					$sql = $sql .=" email, name, req_date, workdate, regdate ";
					$sql = $sql .= " FROM work_todaywork where state!='9' and companyno='".$companyno."' and email='".$user_id."'".$where."";
					$sql = $sql .= " order by workdate asc";
				}

			}

		}else{

			//주간업무
			if($_SERVER[HTTP_HOST] == T_DOMAIN){
				$sql ="select idx, state, work_flag, part_flag, decide_flag, work_idx, repeat_flag, notice_flag, share_flag, date_format( regdate , '%Y.%m.%d') as ymd as ymd, date_format( regdate , '%H:%i') as his, memo_view, contents_view, title, contents, contents1,";
				$sql = $sql .=" email, name, req_date, workdate, regdate ";
				$sql = $sql .= " FROM work_todaywork where state!='9' and companyno='".$companyno."' and email='".$user_id."'".$where."";
				$sql = $sql .= " order by sort asc, idx desc";
			}else{
				$sql ="select idx, state, work_flag, part_flag, decide_flag, work_idx, repeat_flag, notice_flag, share_flag, CONVERT(CHAR(10), regdate, 102) as ymd, CONVERT(CHAR(5), regdate, 8) as his, memo_view, contents_view, title, convert(varchar(max) , contents) as contents, convert(varchar(max) , contents1) as contents1,";
				$sql = $sql .=" email, name, req_date, workdate, regdate ";
				$sql = $sql .= " FROM work_todaywork where state!='9' and companyno='".$companyno."' and email='".$user_id."'".$where."";
				$sql = $sql .= " order by sort asc, idx desc";
			}
		}


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
				$title = $week_info[title][$i];
				$contents = $week_info[contents][$i];
				$contents_edit = strip_tags($week_info[contents][$i]);


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
				$week_works[$workdate][contents][] = $contents;
				$week_works[$workdate][contents_edit][] = $contents_edit;
				$week_works[$workdate][email][] = $work_email;
				$week_works[$workdate][decide_flag][] = $decide_flag;
				$week_works[$workdate][work_flag][] = $work_flag;
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


			$sql = "select idx,email from work_todaywork where work_idx is null and companyno='".$companyno."' and workdate>='".$monthday."'";
			$work_link_coin = selectAllQuery($sql);
			if($work_link_coin){
				$work_link_coin_cnt = count($work_link_coin[idx]);
				for($i=0; $i<$work_link_coin_cnt; $i++){
					$work_link_coin_idx = $work_link_coin[idx][$i];
					$work_link_coin_email = $work_link_coin[email][$i];
					$work_link_coin_arr[$work_link_coin_idx][] = $work_link_coin_email;
				}
				unset($work_link_coin_idx);
				unset($work_link_coin_email);
			}


			//AI댓글에 본인이 좋아요를 보낸 내역
			$sql = "select a.idx , b.idx as comment_idx, a.companyno, a.workdate from (select idx,ai_like_idx,send_email,work_idx,companyno,workdate from work_todaywork_like where send_email = '".$user_id."' and state = 0)";
			$sql = $sql." a join (select idx,ai_like_idx,work_idx,link_idx from work_todaywork_comment where state = 0) b on a.work_idx = b.link_idx";
			$sql = $sql." where a.ai_like_idx = b.ai_like_idx and a.companyno='".$companyno."' and a.workdate>='".$monthday."'";
			$click_like = selectAllQuery($sql);
			if($click_like){
				$click_like_cnt = count($click_like[idx]);
				for($i=0; $i<$click_like_cnt; $i++){
					$click_like_idx = $click_like[idx][$i];
					$click_like_comment_idx = $click_like[comment_idx][$i];
					$click_like_arr[$click_like_comment_idx][] = $click_like_idx;
				}
				unset($click_like_idx);
				unset($click_like_comment_idx);
			}

			$sql = "select a.idx,b.idx as comment_idx,a.companyno,a.workdate from (select idx,work_idx,state,send_email,companyno,workdate from work_todaywork_like where state = 0 and send_email = '".$user_id."') a join";
			$sql = $sql." (select idx from work_todaywork_comment where state=0) b on a.work_idx = b.idx";
			$sql = $sql ." where a.companyno='".$companyno."' and a.workdate>='".$monthday."'";
			$cli_like = selectAllQuery($sql);
			if($cli_like){
				$cli_like_cnt = count($cli_like[idx]);
				for($i=0; $i<$cli_like_cnt; $i++){
					$cli_like_idx = $cli_like[idx][$i];
					$cli_like_comment_idx = $cli_like[comment_idx][$i];
					$cli_like_arr[$cli_like_comment_idx][] = $cli_like_idx;
				}
				unset($cli_like_idx);
				unset($cli_like_comment_idx);
			}


			$sql = "select idx,email from work_todaywork_comment where like_email = '".$user_id."' or email = '".$user_id."' and companyno='".$companyno."' and workdate>='".$monthday."'";
			$my_like = selectAllQuery($sql);
			if($my_like){
				$my_like_cnt = count($my_like[idx]);
				for($i=0; $i<$my_like_cnt; $i++){
					$my_like_idx = $my_like[idx][$i];
					$my_like_email = $my_like[email][$i];
					$my_like_arr[$my_like_idx][] = $my_like_email;
				}
				unset($my_like_idx);
				unset($my_like_email);
			}


			$sql = "select idx,email from work_todaywork_comment where companyno='".$companyno."' and workdate>='".$monthday."'";
			$my_coin_like = selectAllQuery($sql);
			if($my_coin_like){
				$my_coin_like_cnt = count($my_coin_like[idx]);
				for($i=0; $i<$my_coin_like_cnt; $i++){
					$my_coin_like_idx = $my_coin_like[idx][$i];
					$my_coin_like_email = $my_coin_like[like_email][$i];
					$my_coin_like_arr[$my_coin_like_idx] = $my_coin_like_email;
				}
				unset($my_coin_like_idx);
				unset($my_coin_like_email);
			}

			$sql = "select idx,link_idx from work_todaywork_comment where cmt_flag=1 and link_idx != work_idx and companyno='".$companyno."' and workdate>='".$monthday."'";
			$coin_work_l = selectAllQuery($sql);
			if($coin_work_l){
				$coin_work_cnt = count($coin_work_l[idx]);
				for($i=0; $i<$coin_work_cnt; $i++){
					$coin_work_idx = $coin_work_l[idx][$i];
					$coin_work_link_idx = $coin_work_l[link_idx][$i];
					$coin_work_arr_l[$coin_work_idx] = $coin_work_link_idx;
				}
				unset($coin_work_idx);
				unset($coin_work_link_idx);
			}

			$sql = "select idx,work_idx from work_todaywork where companyno='".$companyno."' and workdate>='".$monthday."'";
			$coin_work_i = selectAllQuery($sql);
			if($coin_work_i){
				$coin_work_cnt = count($coin_work_i[idx]);
				for($i=0; $i<$coin_work_cnt; $i++){
					$coin_work_idx = $coin_work_i[idx][$i];
					$coin_work_work_idx = $coin_work_i[work_idx][$i];
					$coin_work_arr_i[$coin_work_idx] = $coin_work_idx;
				}
				unset($coin_work_idx);
				unset($coin_work_work_idx);
			}



			$sql = "select idx,work_idx from work_todaywork where share_flag = 2 and companyno='".$companyno."' and workdate>='".$monthday."'";
			$coin_work_w = selectAllQuery($sql);
			if($coin_work_w){
				$coin_work_cnt = count($coin_work_w[idx]);
				for($iw=0; $iw<$coin_work_cnt; $iw++){
					$coin_work_idx_w = $coin_work_w[idx][$iw];
					$coin_work_work_idx_w = $coin_work_w[work_idx][$iw];
					$coin_work_arr_w[$coin_work_work_idx_w] = $coin_work_idx_w;
				}
				unset($coin_work_idx_w);
				unset($coin_work_work_idx_w);
			}

			$sql = "select idx,coin_work_idx, reward_user, reward_name,memo,coin,CONVERT(varchar(20), regdate, 120) as regdate from work_coininfo";
			$sql = $sql." where state != 9 and code = 700";
			$sql = $sql." and companyno='".$companyno."' and workdate>='".$monthday."' and coin_work_idx is not null order by regdate desc";

			$coin_info_comment = selectAllQuery($sql);

			for($j=0; $j<count($coin_info_comment[idx]); $j++){

				$coin_info_r_idx = $coin_info_comment[idx][$j];
				$coin_info_r_work_idx = $coin_info_comment[coin_work_idx][$j];
				$coin_info_email = $coin_info_comment[email][$j];
				$coin_info_r_email = $coin_info_comment[reward_user][$j];
				$coin_info_r_name = $coin_info_comment[reward_name][$j];
				$coin_info_r_coin = $coin_info_comment[coin][$j];
				$coin_info_r_memo = $coin_info_comment[memo][$j];
				$coin_info_r_regdate = $coin_info_comment[regdate][$j];

				$coin_info_arr[$coin_info_r_work_idx][idx][] = $coin_info_r_idx;
				$coin_info_arr[$coin_info_r_work_idx][coin_work_idx][] = $coin_info_r_work_idx;
				$coin_info_arr[$coin_info_r_work_idx][email][] = $coin_info_email;
				$coin_info_arr[$coin_info_r_work_idx][reward_user][] = $coin_info_r_email;
				$coin_info_arr[$coin_info_r_work_idx][reward_name][] = $coin_info_r_name;
				$coin_info_arr[$coin_info_r_work_idx][coin][] = $coin_info_r_coin;
				$coin_info_arr[$coin_info_r_work_idx][memo][] = $coin_info_r_memo;
				$coin_info_arr[$coin_info_r_work_idx][regdate][] = $coin_info_r_regdate;

			}

			$first_day = @current($week_info[workdate]);
			$last_day = @end($week_info[workdate]);
			$month = strtotime($first_day);
			$d_day = count($week_info[workdate]);


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
							$repeat_flag = $week_works[$workdate][repeat_flag][$j];

							$work_wtitle = $week_works[$workdate][title][$j];
							$work_contents = $week_works[$workdate][contents][$j];
							$work_wtitle = $week_works[$workdate][title][$j];
							$work_contents_edit = $week_works[$workdate][contents_edit][$j];
							$work_email = $week_works[$workdate][email][$j];

							//반복설정
							$decide_flag = $week_works[$workdate][decide_flag][$j];

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
										$work_to_name = $work_report_uer_to_list[work_name][$work_idx];
										$work_title = "[".$work_to_name ."님에게 보고받음]";

									}else{

										//보고 1명 이상인 경우
										$work_report_user_list_cnt = count($work_report_user_list[$work_com_idx]);
										if($work_report_user_list_cnt > 1){
											$work_user_count = $work_report_user_list_cnt - 1;
											$work_req_user = $work_report_user_list[$work_com_idx][0]. "님 외 ". $work_user_count . "명에게 보고함";
											$work_title = "[". $work_req_user. "]";
										}else{
											$work_req_user = $work_report_user_list[$work_com_idx][0];
											$work_title = "[". $work_req_user. "님에게 보고함]";
										}

										$work_report_read_all = $work_report_read[$idx][all];
										$work_report_read_cnt = $work_report_read[$idx][read];
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

										$work_to_name = $work_to_user_list[work_name][$work_idx];
										$work_title = "[".$work_to_name ."님에게 요청받음]";

									}else{
										$work_user_list_cnt = count($work_user_list[$work_com_idx]);
										if($work_user_list_cnt > 1){
											$work_user_count = $work_user_list_cnt - 1;
											$work_req_user = $work_user_list[$work_com_idx][0]. "님 외 ". $work_user_count . "명에게 요청함";
											$work_title = "[". $work_req_user. "]";
										}else{
											$work_req_user = $work_user_list[$work_com_idx][0];
											$work_title = "[". $work_req_user. "님에게 요청함]";
										}

										$work_req_read_all = $work_req_read[$work_com_idx][all];
										$work_req_read_cnt = $work_req_read[$work_com_idx][read];
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
										$work_share_send_list_cnt = count($work_share_send_list[$idx]);
										if($work_share_send_list_cnt > 1){
											$work_user_count = $work_share_send_list_cnt - 1;
											$work_req_user = $work_share_send_list[$idx][0]. "님 외 ". $work_user_count . "명에게 공유함";
											$work_title = "[". $work_req_user. "]";
										}else{
											$work_req_user = $work_share_send_list[$idx][0];
											$work_title = "[". $work_req_user. "님에게 공유함]";
										}


										$work_share_read_all = $work_share_read[$idx][all];
										$work_share_read_cnt = $work_share_read[$idx][read];
										$work_read_reading = $work_share_read_cnt;

										//읽지않은사용자
										if($work_read_reading>0){
											$read_text = "&nbsp;&nbsp;<em class=\"tdw_read\">읽음 ".$work_read_reading."</em>";
										}else{
											$read_text = "&nbsp;&nbsp;<em class=\"tdw_read\">읽음 0</em>";
										}

										$edit_id = " id='tdw_wlist_edit_".$idx."'";

									}else if($share_flag=='2'){

										$work_to_name = $work_share_list[work_name][$work_idx];
										$work_title = "[".$work_to_name."님에게 공유받음]";
									}
								}

							}

							//공유함($share_flag=1), 공유취소($share_flag=2), 요청받은업무($work_flag=3) 아이콘 변경
							$li_class = "";
							$tdw_list = false;
							if($share_flag=="1"){
								$li_class = " share_to";
							}else if($share_flag=="2"){
								$li_class = " share_cancel";
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
							<li class="tdw_list_li<?=$li_class?>" id="workslist_<?=$idx?>">
								<div class="tdw_list_box<?=$week_works[$workdate][state][$j]=='1'?" on":""?>" id="tdw_wlist_box_<?=$idx?>">
									<div class="tdw_list_chk">
										<button class="btn_tdw_list_chk" <?if($work_flag!='1'){?>value="<?=$idx?>"<?}?> <?=$tdw_list?" id='tdw_wlist_chk'":""?>><span>완료체크</span></button>
									</div>
									<div class="tdw_list_desc">

										<?if($work_idx){?>

											<?if($notice_flag=="1"){?>
												<p <?=$edit_id?> id="notice_link" value="<?=$work_idx?>">
											<?}else{?>
												<p <?=$edit_id?>>
											<?}?>
												<?=$work_title?"<span>".$work_title."</span>":""?>
												<?=textarea_replace($work_contents)?>
											</p>

										<?}else{?>

											<?//보고업무
											if($work_flag == "1"){?>

												<p id="tdw_wlist_edit_<?=$idx?>">
												<?if($work_title){?>
													<span><?=$work_title?></span>
												<?}?>
												<?=textarea_replace($work_contents)?><?=$read_text?></p>
											<?}else{?>

												<p id="tdw_wlist_edit_<?=$idx?>">
												<?if($work_title){?>
													<span><?=$work_title?></span>
												<?}?>
												<?=textarea_replace($work_contents)?><?=$read_text?></p>
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

									<div class="tdw_list_function">
										<div class="tdw_list_function_in">

											<?//보고받은 업무
											if($work_flag=="1" && $work_idx){?>
												<button class="tdw_list_reported_hart<?=$work_like_list[$work_idx]>0?" on":""?>" title="좋아요" <?=$work_like_list[$work_idx]>0?"":" id=\"tdw_list_jjim\""?> value="<?=$work_idx?>"><span>좋아요</span></button>
											<?
											//알림글, 공유받음
											}else if($notice_flag=='0' && ($share_flag=='2' && $work_idx)){?>
												<button class="tdw_list_reported_hart<?=$work_like_list[$work_idx]>0?" on":""?>" title="좋아요" <?=$work_like_list[$work_idx]>0?"":" id=\"tdw_list_jjim\""?> value="<?=$work_idx?>"><span>좋아요</span></button>
											<?}else{?>

												<?//공유 보낸 업무?>
												<?if($share_flag=="1" && $work_idx){?>

													<?if($work_like_receive[$work_idx]){?>
														<button class="tdw_list_jjim_clear<?=$work_like_receive[$work_idx]>0?"":" on"?>" title="좋아요" value="<?=$work_idx?>"><span>좋아요</span></button>
													<?}?>
												<?}?>

												<?//보고업무 보낸 업무?>
												<?if($work_flag=="1" && $work_idx==null){?>
													<?if($work_like_receive[$idx]){?>
														<button class="tdw_list_jjim_clear<?=$work_like_receive[$idx]>0?" on":""?>" title="좋아요"  value="<?=$idx?>"><span>좋아요</span></button>
													<?}?>
												<?}?>

											<?}?>


											<?//공유한 업무는 취소 가능, 공유한업무(share_flag=1), 공유받은업무(share_flag=2)?>
											<?//공유하기?>
											<?//공유한 업무?>
											<?if($share_flag=='1' && $work_idx){?>
												<button class="tdw_list_share_cancel" id="tdw_list_share_cancel" value="<?=$idx?>" title="공유취소"><span>공유취소</span></button>
											<?}else{?>
												<?//나의업무작성, 공유업무작성?>
												<?if(($work_flag=='2' && $work_idx==null) || ($share_flag=='1' && $work_idx==null)){?>
													<button class="tdw_list_share" id="tdw_list_share" value="<?=$idx?>" title="공유"><span>공유</span></button>
												<?}?>
											<?}?>


											<?//파일첨부?>
											<?//파일첨부(나의업무, 공유업무작성, 보고업무작성, 요청업무작성)?>
											<?if(($work_flag=='2' && $work_idx==null) || ($share_flag=='1' && $work_idx) || ($work_flag=='1' && $work_idx==null) || ($work_flag=='3' && $work_idx==null)){?>
												<button class="tdw_list_files" id="tdw_file_add_<?=$idx?>" title="파일추가4"><span>파일추가</span></button>
												<input type="file" id="files_add_<?=$idx?>" style="display:none;">
											<?}?>


											<?//사람선택?>
											<?//공유업무작성, 보고업무작성?>
											<?if(($share_flag=='1' && $work_idx) || ($work_flag=='1' &&  $work_idx==null)){?>
												<button class="tdw_list_user" id="tdw_send_user_<?=$idx?>" value="<?=$idx?>" title="받을사람"><span>받을사람</span></button>
											<?}?>

											<?//메모작성?>
											<button class="tdw_list_memo" id="tdw_list_memo" value="<?=$idx?>" title="메모"><span>메모</span></button>


											<?//나의업무, 요청업무 작성자만 반복설정?>
											<?if(($work_flag=='2' && $work_idx==null) || ($work_flag=='3' && $work_idx==null)){?>
												<div class="tdw_list_repeat_box<?=$repeat_flag?" on":""?>" title="반복설정">
													<button class="tdw_list_repeat" id="tdw_list_repeat"><span><?=$repeat_text?></span></button>
													<div class="tdw_list_repeat_list">
														<div><button id="repeat1" value="<?=$idx?>" >매일반복</button></div>
														<div><button id="repeat2" value="<?=$idx?>">매주반복</button></div>
														<div><button id="repeat3" value="<?=$idx?>">매월반복</button></div>
														<div><button id="repeat4" value="<?=$idx?>">반복안함</button></div>
													</div>
												</div>
											<?}?>


											<?//일정변경?>
											<?//나의업무, 공유업무작성, 보고업무작성, 요청업무작성?>
											<?if(($work_flag=='2' && $work_idx==null) || ($share_flag=='1' && $work_idx==null) || ($work_flag=='1' && $work_idx==null) || ($work_flag=='3' && $work_idx==null)){?>
												<input class="tdw_list_date" type="image" title="일정 변경" id="listdate_<?=$idx?>">
											<?}?>


											<?//삭제?>
											<?//알림글삭제?>
											<?if($notice_flag){?>

												<?if($user_id == $work_email){?>
													<button class="tdw_list_del" title="삭제" id="notice_list_del" value="<?=$idx?>"><span>삭제</span></button>
												<?}else{?>
													<button class="tdw_list_del" title="삭제" value="<?=$idx?>"><span>삭제</span></button>
												<?}?>

											<?}else{?>
											<?//업무글삭제?>
												<?if($user_id == $work_email){?>
														<button class="tdw_list_del" title="삭제" id="tdw_list_del" value="<?=$idx?>"><span>삭제</span></button>
												<?}else{?>
													<button class="tdw_list_del" title="삭제" value="<?=$idx?>"><span>삭제</span></button>
												<?}?>
											<?}?>


											<?//업무이동 드래그앤드랍?>
											<div class="tdw_list_drag" title="순서 변경" value="<?=$idx?>"><span>드래그 드랍 기능</span></div>

											<?/*2022-01-12 주석처리 <button class="tdw_list_tomorrow" title="내일로 미루기" value="<?=$idx?>"><span>내일로 미루기</span></button>*/?>

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
														<button class="btn_list_file" id="btn_list_file_<?=$tdf_files[$work_com_idx][num][$k]?>" value="<?=$tdf_files[$work_com_idx][idx][$k]?>"><span><?=$tdf_files[$work_com_idx][file_real_name][$k]?></span></button>
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

									if($work_idx == null){
										$report_email = $work_report_list[$idx][email];
										$report_name = $work_report_list[$idx][name];
										$report_contents = $work_report_list[$idx][contents];
										$report_workdate = $work_report_list[$idx][workdate];
										$report_reg = $work_report_list[$idx][reg];

										if($report_reg){
											$report_reg = str_replace("  "," ", $report_reg);
											$his_tmp = @explode(" ", $report_reg);
											if ($his_tmp[2] == "PM"){
												$after = "오후 ";
											}else{
												$after = "오전 ";
											}
											$ctime = @explode(":", $his_tmp[1]);
											$work_his = $report_workdate . " " . $after . $ctime[0] .":". $ctime[1];
										}

									}else{
										$report_email = $work_report_list[$work_idx][email];
										$report_name = $work_report_list[$work_idx][name];
										$report_contents = $work_report_list[$work_idx][contents];
										$report_workdate = $work_report_list[$work_idx][workdate];
										$report_reg = $work_report_list[$work_idx][reg];

										if($report_reg){
											$report_reg = str_replace("  "," ", $report_reg);
											$his_tmp = @explode(" ", $report_reg);
											if ($his_tmp[2] == "PM"){
												$after = "오후 ";
											}else{
												$after = "오전 ";
											}
											$ctime = @explode(":", $his_tmp[1]);
											$work_his = $report_workdate . " " . $after . $ctime[0] .":". $ctime[1];
										}

									}

								?>

									<div class="tdw_list_report_area">
										<div class="tdw_list_report_area_in<?=$report_view_in?>" id="tdw_listw_report_area_in_<?=$idx?>">
											<div class="tdw_list_report_desc">
												<div class="tdw_list_report_conts">
													<?if($user_id==$report_email){?>
														<span class="tdw_list_report_conts_txt" id="tdw_list_report_conts_txt_<?=$idx?>"><?=textarea_replace($report_contents)?></span>
													<?}else{?>
														<span class="tdw_list_report_conts_txt"><?=textarea_replace($report_contents)?></span>
													<?}?>
													<em class="tdw_list_report_conts_date"><?=$work_his?></em>
													<div class="tdw_list_report_regi" id="tdw_list_report_regi_<?=$idx?>">
														<textarea name="" class="textarea_regi" id="tdw_report_edit_<?=$idx?>"><?=strip_tags($report_contents)?></textarea>
														<div class="btn_regi_box">
															<button class="btn_regi_submit" id="btn_report_submit" value="<?=$idx?>"><span>확인</span></button>
															<button class="btn_regi_cancel"><span>취소</span></button>
														</div>
													</div>
												</div>
											</div>

											<?//첨부파일 정보
											if($tdf_files[$work_com_idx][file_path]){?>
												<div class="tdw_list_file">
													<?
													$tdf_files_cnt = count($tdf_files[$work_com_idx][file_path]);
													for($k=0; $k<$tdf_files_cnt; $k++){?>
														<div class="tdw_list_file_box">
															<button class="btn_list_file" id="btn_list_file_<?=$k?>" value="<?=$tdf_files[$work_com_idx][idx][$k]?>"><span><?=$tdf_files[$work_com_idx][file_real_name][$k]?></span></button>
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
											<button class="btn_list_report_onoff<?=$report_view_bt?>" id="btn_listw_report_onoff_<?=$idx?>" value="<?=$idx?>" <?if(trim($report_view_bt)=="on"){ echo "title='보고 접기'"; }else{ echo "title='보고 펼치기'"; }?>><span>보고 접기/펼치기</span></button>
										</div>
									</div>
								<?}?>


								<div class="tdw_list_memo_area">
									<div class="tdw_list_memo_area_in<?=$memo_view_in?>" id="tdw_listw_memo_area_in_<?=$idx?>">

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

													$coin_work_list_l = array();
													$coin_work_list_l = $coin_work_arr_l[$comment_idx];

													if($coin_info_arr[$coin_work_list_l]){
														$coin_info_cnt = count($coin_work_list_l);

														for($co_kj=0; $co_kj<$coin_info_cnt; $co_kj++){
															$coin_info_r_idx = $coin_info_arr[$coin_work_list_l][idx][$co_kj];
															$coin_info_r_email = $coin_info_arr[$coin_work_list_l][reward_user][$co_kj];
															$coin_info_r_name = $coin_info_arr[$coin_work_list_l][reward_name][$co_kj];
															$coin_info_r_coin = $coin_info_arr[$coin_work_list_l][coin][$co_kj];
															$coin_info_r_memo = $coin_info_arr[$coin_work_list_l][memo][$co_kj];
															$coin_info_r_regdate = $coin_info_arr[$coin_work_list_l][regdate][$co_kj];

															$coin_date = date("Y-m-d",strtotime($coin_info_r_regdate));

															$hour = date("H", strtotime($coin_info_r_regdate));
															$min = date("i", strtotime($coin_info_r_regdate));

															if($hour > 12){
																$hour = $hour - 12;
																$coin_info_r_time = $coin_date." 오후 ".$hour.":".$min;
															}else{
																$coin_info_r_time = $coin_date." 오전 ".$hour.":".$min;
															}
															?>
															<div class="tdw_list_memo_desc" id="comment_list_<?=$comment_idx?>#" >
																<div class="tdw_list_memo_name"><?=$coin_info_r_name?></div>
																<p class="btn_req_100c" id="btn_req_100c" title="100코인"></p>
																<div  class="tdw_list_memo_conts">
																	<span class="tdw_list_memo_conts_txt"><?=$coin_info_r_coin?> <?=$coin_info_r_memo?></span>
																	<em class="tdw_list_memo_conts_date"><?=$coin_info_r_time?></em>
																</div>
															</div>
														<?
														}
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
																<span class="tdw_list_memo_conts_txt" id="tdw_list_memo_conts_txt_<?=$comment_idx?>"><?=textarea_replace($comment_list[$work_com_idx][comment][$k])?></span>
															<?}else if($cmt_flag && $work_send_like_name[$comment_idx][send]){?>
																<span class="tdw_list_memo_conts_txt"><?=textarea_replace($comment_list[$work_com_idx][comment][$k])?></span>
															<?}else{?>
																<span class="tdw_list_memo_conts_txt"><?=textarea_replace($comment_list[$work_com_idx][comment][$k])?></span>
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
																$coin_work_list_i = array();
																$coin_work_list_i = $coin_work_arr_i[$idx];

																if($coin_info_arr[$coin_work_list_i][idx]){
																	$coin_info_cnt = count($coin_info_arr[$coin_work_list_i][idx]);

																	for($co_kj=0; $co_kj<$coin_info_cnt; $co_kj++){
																		$coin_info_r_idx = $coin_info_arr[$coin_work_list_i][idx][$co_kj];
																		$coin_info_r_email = $coin_info_arr[$coin_work_list_i][reward_user][$co_kj];
																		$coin_info_r_name = $coin_info_arr[$coin_work_list_i][reward_name][$co_kj];
																		$coin_info_r_coin = $coin_info_arr[$coin_work_list_i][coin][$co_kj];
																		$coin_info_r_memo = $coin_info_arr[$coin_work_list_i][memo][$co_kj];
																		$coin_info_r_regdate = $coin_info_arr[$coin_work_list_i][regdate][$co_kj];

																		$coin_date = date("Y-m-d",strtotime($coin_info_r_regdate));

																		$hour = date("H", strtotime($coin_info_r_regdate));
																		$min = date("i", strtotime($coin_info_r_regdate));

																		if($hour > 12){
																			$hour = $hour - 12;
																			$coin_info_r_time = $coin_date." 오후 ".$hour.":".$min;
																		}else{
																			$coin_info_r_time = $coin_date." 오전 ".$hour.":".$min;
																		}

																		?>
																		<div class="tdw_list_memo_desc" id="comment_list_<?=$comment_idx?>!" >
																			<div class="tdw_list_memo_name"><?=$coin_info_r_name?></div>
																			<p class="btn_req_100c" id="btn_req_100c" title="100코인"></p>
																			<div  class="tdw_list_memo_conts">
																				<span class="tdw_list_memo_conts_txt"><?=$coin_info_r_coin?> <?=$coin_info_r_memo?></span>
																				<em class="tdw_list_memo_conts_date"><?=$coin_info_r_time?></em>
																			</div>
																		</div>
																	<?
																	}
																}

																$coin_work_list_w = array();
																$coin_work_list_w = $coin_work_arr_w[$idx];

																if($coin_info_arr[$coin_work_list_w][idx]){
																	$coin_info_cnt = count($coin_info_arr[$coin_work_list_w][idx]);

																	for($co_kj=0; $co_kj<$coin_info_cnt; $co_kj++){
																		$coin_info_r_idx = $coin_info_arr[$coin_work_list_w][idx][$co_kj];
																		$coin_info_r_email = $coin_info_arr[$coin_work_list_w][reward_user][$co_kj];
																		$coin_info_r_name = $coin_info_arr[$coin_work_list_w][reward_name][$co_kj];
																		$coin_info_r_coin = $coin_info_arr[$coin_work_list_w][coin][$co_kj];
																		$coin_info_r_memo = $coin_info_arr[$coin_work_list_w][memo][$co_kj];
																		$coin_info_r_regdate = $coin_info_arr[$coin_work_list_w][regdate][$co_kj];

																		$coin_date = date("Y-m-d",strtotime($coin_info_r_regdate));

																		$hour = date("H", strtotime($coin_info_r_regdate));
																		$min = date("i", strtotime($coin_info_r_regdate));

																		if($hour > 12){
																			$hour = $hour - 12;
																			$coin_info_r_time = $coin_date." 오후 ".$hour.":".$min;
																		}else{
																			$coin_info_r_time = $coin_date." 오전 ".$hour.":".$min;
																		}

																		?>
																		<div class="tdw_list_memo_desc" id="comment_list_<?=$comment_idx?>!" >
																			<div class="tdw_list_memo_name"><?=$coin_info_r_name?></div>
																			<p class="btn_req_100c" id="btn_req_100c" title="100코인"></p>
																			<div  class="tdw_list_memo_conts">
																				<span class="tdw_list_memo_conts_txt"><?=$coin_info_r_coin?> <?=$coin_info_r_memo?></span>
																				<em class="tdw_list_memo_conts_date"><?=$coin_info_r_time?></em>
																			</div>
																		</div>
																	<?
																	}
																}

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
																		<span class="tdw_list_memo_conts_txt" id="tdw_list_memo_conts_txt_<?=$comment_idx?>"><?=textarea_replace($comment_list[$work_idx][comment][$k])?></span>
																	<?}else if($cmt_flag && $work_send_like_name[$comment_idx][send]){?>
																		<!-- 좋아요 받았을 때 문장 -->
																		<span class="tdw_list_memo_conts_txt"><?=textarea_replace($comment_list[$work_idx][comment][$k])?></span>
																	<?}else{?>
																		<!-- AI 문장 -->
																		<span class="tdw_list_memo_conts_txt"><?=textarea_replace($comment_list[$work_idx][comment][$k])?></span>
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

													<?
														$coin_work_list_i = array();
														$coin_work_list_i = $coin_work_arr_i[$idx];


														if($coin_info_arr[$coin_work_list_i]){
															$coin_info_cnt = count($coin_info_arr[$coin_work_list_i][idx]);

															for($co_kj=0; $co_kj<$coin_info_cnt; $co_kj++){
																$coin_info_r_idx = $coin_info_arr[$coin_work_list_i][idx][$co_kj];
																$coin_info_r_email = $coin_info_arr[$coin_work_list_i][reward_user][$co_kj];
																$coin_info_r_name = $coin_info_arr[$coin_work_list_i][reward_name][$co_kj];
																$coin_info_r_coin = $coin_info_arr[$coin_work_list_i][coin][$co_kj];
																$coin_info_r_memo = $coin_info_arr[$coin_work_list_i][memo][$co_kj];
																$coin_info_r_regdate = $coin_info_arr[$coin_work_list_i][regdate][$co_kj];

																$coin_date = date("Y-m-d",strtotime($coin_info_r_regdate));

																$hour = date("H", strtotime($coin_info_r_regdate));
																$min = date("i", strtotime($coin_info_r_regdate));

																if($hour > 12){
																	$hour = $hour - 12;
																	$coin_info_r_time = $coin_date." 오후 ".$hour.":".$min;
																}else{
																	$coin_info_r_time = $coin_date." 오전 ".$hour.":".$min;
																}
														?>
														<div class="tdw_list_memo_desc" id="comment_list_<?=$comment_idx?>!@" >
															<div class="tdw_list_memo_name"><?=$coin_info_r_name?></div>
															<p class="btn_req_100c" id="btn_req_100c" title="100코인"></p>
															<div  class="tdw_list_memo_conts">
																<span class="tdw_list_memo_conts_txt"><?=$coin_info_r_coin?> <?=$coin_info_r_memo?></span>
																<em class="tdw_list_memo_conts_date"><?=$coin_info_r_time?></em>
															</div>
														</div>
													<?
													}
												}

												$coin_work_list_w = array();
												$coin_work_list_w = $coin_work_arr_w[$idx];

												if($coin_info_arr[$coin_work_list_w][idx]){
													$coin_info_cnt = count($coin_info_arr[$coin_work_list_w][idx]);

													for($co_kj=0; $co_kj<$coin_info_cnt; $co_kj++){
														$coin_info_r_idx = $coin_info_arr[$coin_work_list_w][idx][$co_kj];
														$coin_info_r_email = $coin_info_arr[$coin_work_list_w][reward_user][$co_kj];
														$coin_info_r_name = $coin_info_arr[$coin_work_list_w][reward_name][$co_kj];
														$coin_info_r_coin = $coin_info_arr[$coin_work_list_w][coin][$co_kj];
														$coin_info_r_memo = $coin_info_arr[$coin_work_list_w][memo][$co_kj];
														$coin_info_r_regdate = $coin_info_arr[$coin_work_list_w][regdate][$co_kj];

														$coin_date = date("Y-m-d",strtotime($coin_info_r_regdate));

														$hour = date("H", strtotime($coin_info_r_regdate));
														$min = date("i", strtotime($coin_info_r_regdate));

														if($hour > 12){
															$hour = $hour - 12;
															$coin_info_r_time = $coin_date." 오후 ".$hour.":".$min;
														}else{
															$coin_info_r_time = $coin_date." 오전 ".$hour.":".$min;
														}

														?>
														<div class="tdw_list_memo_desc" id="comment_list_<?=$comment_idx?>!" >
															<div class="tdw_list_memo_name"><?=$coin_info_r_name?></div>
															<p class="btn_req_100c" id="btn_req_100c" title="100코인"></p>
															<div  class="tdw_list_memo_conts">
																<span class="tdw_list_memo_conts_txt"><?=$coin_info_r_coin?> <?=$coin_info_r_memo?></span>
																<em class="tdw_list_memo_conts_date"><?=$coin_info_r_time?></em>
															</div>
														</div>
													<?
													}
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
																	<span class="tdw_list_memo_conts_txt" id="tdw_list_memo_conts_txt_<?=$comment_idx?>"><?=textarea_replace($comment_list[$idx][comment][$k])?></span>
																<?}else if($cmt_flag && $work_send_like_name[$comment_idx][send]){?>
																	<!-- 좋아요 받았을 때 문장 -->
																	<span class="tdw_list_memo_conts_txt"><?=textarea_replace($comment_list[$idx][comment][$k])?></span>
																<?}else{?>
																	<!-- AI 문장 -->
																	<span class="tdw_list_memo_conts_txt"><?=textarea_replace($comment_list[$idx][comment][$k])?></span>
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
											<button class="btn_list_memo_onoff<?=$memo_view_bt?>" id="btn_listw_memo_onoff_<?=$idx?>" value="<?=$idx?>" <?//if(trim($memo_view_bt)=="on"){ echo "title='메모 접기!'"; }else{ echo "title='메모 펼치기!'"; }?>><span>메모 접기/펼치기</span></button>
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
