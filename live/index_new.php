<?
	//header페이지
	$home_dir = str_replace( basename(__DIR__) , "" , __DIR__ );

	if($user_id=='sadary0@nate.com'){
	//	echo "###";
	//	$user_id='adsb12@nate.com';
	}

	include $home_dir . "/inc_lude/header.php";

	//회원전체
	$sql = "select idx, email, name, part, gender, profile_type, profile_img_idx, live_1, live_2, live_3, live_4, convert(char(5), live_1_regdate, 8) as live_1_time, convert(varchar, live_1_regdate , 120) as live_1_regdate, convert(varchar, DATEADD(HOUR, 10, convert(varchar, live_1_regdate , 120)), 120) AS live_10_regdate";
	$sql = $sql .= " from work_member where state=0";

	

	//회원정보
	//$member_row_info = member_row_info($user_id);
	if($user_id=='sadary0@nate.com'){
		//$user_id='eyson@bizforms.co.kr';
		//$user_id='adsb12@nate.com';
		/*
		print "<pre>";
		print_r($member_info);
		print "</pre>";
		*/
	}

	$curYear = (int)date('Y');
	$curMonth = (int)date('m');
	$month_first_day = date("Y-m-d", mktime(0, 0, 0, $curMonth , 1, $curYear));
	$month_last_day = date("Y-m-d", mktime(0, 0, 0, $curMonth+1 , 0, $curYear));

	//관리권한은 제외처리
	if($user_level == 1){
		$sql = $sql .= " and email !='".$user_id."'";
	}else{
		$sql = $sql .= " and companyno='".$companyno."'";
		$sql = $sql .= " and highlevel != '1'";
	}

	$sql = $sql .= " order by ";
	$sql = $sql .= " CASE WHEN email='".$user_id."' THEN email END DESC,";
	$sql = $sql .= " CASE WHEN live_4 = '1' THEN live_4_regdate END desc";
	$sql = $sql .= ", CASE WHEN live_2 = '1' THEN live_2_regdate END desc";
	$sql = $sql .= ", CASE WHEN live_3 = '1' THEN live_3_regdate END desc";
	$sql = $sql .= ", CASE WHEN live_2 = '0' THEN live_1_regdate END desc";
	$sql = $sql .= ", CASE WHEN live_3 = '0' THEN live_1_regdate END desc";
	$sql = $sql .= ", CASE WHEN live_1_regdate is null THEN name END collate Korean_Wansung_CI_AS asc, CASE WHEN live_1_regdate is not null THEN live_1_regdate END ASC";
	$member_list_info = selectAllQuery($sql);
	//정렬기준
	//1. 자기 자신은 제일 앞으로 정렬함
	//2. 퇴근한 회원
	//3. 집중모드 ON
	//4. 자리비움 ON
	//5. 집중모드 OFF
	//6. 자리비움 OFF
	//7. 출근을 하지 않은경우 이름순으로 정렬, 

	if($member_list_info['idx']){
		$member_all_cnt = number_format(count($member_list_info['idx']));
	}else{
		$member_all_cnt = 0;
	}
	
	//오늘 업무 작성 회원수
	//$sql = "select email from work_todaywork where state in('0','1') and decide_flag!='9' and workdate=convert(char(10), getdate(), 120) group by email order by min(regdate) asc";
	//$member_live_info = selectAllQuery($sql);

	//라이브 카운터
	$live_1_cnt = 0;
	foreach($member_list_info['live_1'] as $key => $val){
		if($val == '1'){
			$live_1_cnt++;
		}
	}
	if($live_1_cnt){
		$member_live_cnt = number_format($live_1_cnt);
	}


	//회원정보
	$sql = "select idx, email, name, gender, live_1, live_2, live_3, live_4, convert(char(5), live_1_regdate, 8) as live_1_time, highlevel, coin, profile_type, profile_img_idx from work_member where state='0' and companyno='".$companyno."' and email='".$user_id."'";
	$member_one_info = selectQuery($sql);
	if($member_one_info['idx']){
		$part_name = $member_part['part'][$member_one_info['email']];
		$member_info_email = $member_one_info['email'];
		$member_info_name = $member_one_info['name'];
		$gender = $member_one_info['gender'];
		$live_1 = $member_one_info['live_1'];
		$live_2 = $member_one_info['live_2'];
		$live_3 = $member_one_info['live_3'];
		$live_4 = $member_one_info['live_4'];

		$live_1_time = $member_one_info['live_1_time'];
		if($live_1_time){
			$tmp_live_1_time = explode(":", $live_1_time);
			if($tmp_live_1_time){
				$ex_live_1_time = (int)$tmp_live_1_time[0].":" .$tmp_live_1_time[1] ."";
			}
		}
		
		$profile_type = $member_one_info['profile_type'];
		$profile_img_idx = $member_one_info['profile_img_idx'];
		$member_info_coin = number_format($member_info_coin);

		//프로필 사진
		$profile_main_img_src = profile_img_info($member_info_email);
	}


	//오늘업무 등록,완료갯수
	//삭제된업무, 공유받은업무는 제외
	$sql = "select email, state, work_flag, decide_flag, count(1) as cnt from work_todaywork where state='0' and share_flag!='2' and workdate='".TODATE."'  group by email, state, work_flag, decide_flag";
	$works_myinfo = selectAllQuery($sql);
	if($works_myinfo['email']){
		for($i=0; $i<count($works_myinfo['email']); $i++){

			$works_myinfo_email = $works_myinfo['email'][$i];
			$work_list[TODATE][$works_myinfo_email][$works_myinfo['state'][$i]] += $works_myinfo['cnt'][$i];
			

			//업무예약일정
			if($works_myinfo['work_flag'][$i] =='2' && $works_myinfo['decide_flag'][$i] != '0'){
				$work_flag_list[TODATE][$works_myinfo_email][] = $works_myinfo['decide_flag'][$i];
			}
		}


		//오늘업무 전체갯수
		$work_all_list = number_format($work_list[TODATE][$user_id][0] + $work_list[TODATE][$user_id][1]);

		//오늘업무 완료갯수
		$work_com_list = number_format($work_list[TODATE][$user_id][1]);

	}else{
		$work_all_list = 0;
		$work_com_list = 0;
	}
	
	/*print "<pre>";
	print_r($work_list);
	print "</pre>";*/

	//업무예약
	$sql = "select idx, title from work_decide where state='0' and type_flag='0'";
	$work_decide_info = selectAllQuery($sql);
	if($work_decide_info['idx']){
		$work_decide_list = @array_combine($work_decide_info['idx'], $work_decide_info['title']);
	}

	//전체회원 오늘업무 갯수
	$sql = "select a.email, b.state, count(1) as cnt from work_member as a FULL OUTER JOIN work_todaywork as b on(a.email=b.email)";
	$sql = $sql .= " where a.state='0'";

	//관리권한은 제외처리
	if($user_level == 1){
		$sql = $sql .= " and a.email !='".$user_id."'";
	}else{
		$sql = $sql .= " and a.companyno='".$companyno."'";
	}
	$sql = $sql .= " and b.state!='9' and b.notice_flag='0' and b.decide_flag !='9' and b.share_flag!='2' and b.workdate='".TODATE."' group by a.email, b.state order by a.email asc";
	$works_all_info = selectAllQuery($sql);
	for($i=0; $i<count($works_all_info['email']); $i++){
		$works_email = $works_all_info['email'][$i];
		$works_state = $works_all_info['state'][$i];
		$works_cnt = $works_all_info['cnt'][$i];
		$work_live[$works_email][$works_state] += $works_cnt;
	}

	//오늘날짜
	$wdate = str_replace("-",".",$today);

	//오늘업무 제일 많이 씀
	$sql = "select top 1 email, name, count(1) as cnt from work_todaywork where state!='9' and share_flag!='2' and notice_flag='0' and workdate='".TODATE."'";
	$sql = $sql .= " group by email, name order by count(1) desc";
	$works_top_info = selectQuery($sql);
	if($works_top_info['email']){
		$works_top_arr[TODATE] = $works_top_info['email'];
	}

	//오늘 출근 제일 빠르게한 회원 조회
	$sql = "select top 1 email, name, count(1) as cnt, live_1_regdate from work_member where state='0' and companyno='".$companyno."' and live_1='1' and convert(char(10), live_1_regdate, 120)='".TODATE."' group by email, name, live_1_regdate";
	$sql = $sql .= " order by live_1_regdate asc";
	$mem_top_info = selectQuery($sql);
	if($mem_top_info['email']){
		$mem_top_arr[TODATE] = $mem_top_info['email'];
	}

	//오늘 마음전하기
	$sql = "select top 1 email, name, count(1) as cnt from work_todaywork_like where state='0' and service='live' and like_flag='0' and workdate='".TODATE."' group by email, name";
	$like_top_info = selectQuery($sql);
	if($like_top_info['email']){
		$like_top_arr[TODATE] = $like_top_info['email'];
	}

	//역략지표 좋아요 타이틀
	$work_reward_title = work_reward_like();

	//역량지표
	$avg_point = 1;

	//and idx>='6529'
	$sql = "select email, sum(type1) as type1, sum(type2) as type2, sum(type3) as type3, sum(type4) as type4, sum(type5) as type5, sum(type6) as type6 from work_cp_reward_list";
	$sql = $sql .= " where state='0' and workdate between '".$month_first_day."' and '".$month_last_day."' group by email";
	$work_reward_info = selectAllQuery($sql);
	for($i=0; $i<count($work_reward_info['email']); $i++){

		$reward_email = $work_reward_info['email'][$i];
		$reward_type1 = $work_reward_info['type1'][$i];
		$reward_type2 = $work_reward_info['type2'][$i];
		$reward_type3 = $work_reward_info['type3'][$i];
		$reward_type4 = $work_reward_info['type4'][$i];
		$reward_type5 = $work_reward_info['type5'][$i];
		$reward_type6 = $work_reward_info['type6'][$i];
		$reward_reg = $work_reward_info['reg'][$i];

		//역량평가점수 아이디별
		$reward_type_sum = $reward_type1 + $reward_type2 + $reward_type3 + $reward_type4 + $reward_type5 + $reward_type6;
		$reward_cp_sum[$reward_email] = $reward_type_sum;
	}

	// //리워디 역량지표 합계
	//$reward_cp_sum = @array_sum($reward_cp_type[$member_list_email]);
	//if(!$reward_cp_sum){
	//	$reward_cp_sum = "0";
	//}




	//좋아요 갯수 조회
	$sql = "select email, count(1) as cnt from work_todaywork_like where state='0' and workdate between '".$month_first_day."' and '".$month_last_day."' group by email";
	$like_info = selectAllQuery($sql);
	for($i=0; $i<count($like_info['email']); $i++){
		$link_info_email = $like_info['email'][$i];
		$link_info_cnt = $like_info['cnt'][$i];
		$like_list[$link_info_email] = $link_info_cnt;
	}





	//좋아요 리스트
	$like_flag_list = array();
	$sql = "select idx, email, send_email, like_flag from work_todaywork_like where state='0' and send_email='".$user_id."' and workdate='".TODATE."'";
	$like_flag_info = selectAllQuery($sql);
	for($i=0; $i<count($like_flag_info['idx']); $i++){
		$like_flage_info_idx = $like_flag_info['idx'][$i];
		$like_flage_info_email = $like_flag_info['email'][$i];
		$like_flage_info_like_flag = $like_flag_info['like_flag'][$i];
		$like_flage_info_send_email = $like_flag_info['send_email'][$i];
		$like_flag_list[$like_flage_info_email][$like_flage_info_like_flag][] = $like_flage_info_idx;
	}

	if($user_id=='sadary0@nate.com'){
		
		/*print "<pre>";
		print_r($like_flag_list);
		print "</pre>";*/
		
	}


	//프로젝트 리스트
	$sql = "select a.idx, a.email, a.title, b.email, a.editdate from work_todaywork_project as a left join work_todaywork_project_user as b on (a.idx=b.project_idx)";
	$sql = $sql .=" where a.state=0 and a.email='".$user_id."' or b.email='".$user_id."'";
	//$project_info = selectAllQuery($sql);


	//전체프로젝트 리스트
	$sql = "select idx, sort, email, name, part, title, convert(char(16), editdate, 120) as edate, convert(char(16), regdate, 120) as rdate, CONVERT(CHAR(10), editdate, 103), CONVERT(CHAR(8), editdate, 24),";
	$sql = $sql .= " case when editdate is null then datediff(MI, regdate , getdate() ) when editdate is not null then datediff(MI, regdate , editdate) ";
	$sql = $sql .= " end as reg";
	$sql = $sql .= " from work_todaywork_project_list where state='0' order by sort asc";
	//$project_info = selectAllQuery($sql);
	

	//전체 파티
	//좌측메뉴 프로젝트 리스트(사용자별 정렬 리스트)
	/*
	$sql = "select a.idx, a.title, a.email, b.sort, convert(char(16), a.editdate, 120) as edate, convert(char(16), a.regdate, 120) as rdate,";
	$sql = $sql .= " case when a.editdate is null then datediff(MI, a.regdate , getdate() ) when a.editdate is not null then datediff(MI, a.regdate , a.editdate)";
	$sql = $sql .= " end as reg from work_todaywork_project as a left join work_todaywork_project_sort as b on(a.idx=project_idx)";
	$sql = $sql .=" where a.state='0' and b.state='0' and b.email='".$user_id."' and b.sort is not null order by b.sort asc";
	*/

	//파티 목록중에 정렬값이 없는 갯수
	$sql = "select count(1) as cnt from work_todaywork_project_sort where state='0' and companyno='".$companyno."' and email='".$user_id."' and sort is null";
	$project_cnt_info = selectQuery($sql);
	if($project_cnt_info['cnt'] > 0){
		$sql = "select a.idx, a.title, a.email, b.sort, convert(char(16), a.editdate, 120) as edate, convert(char(16), a.regdate, 120) as rdate,";
		$sql = $sql .=" case when a.editdate is null then datediff(MI, a.regdate , getdate() ) when a.editdate is not null then datediff(MI, a.regdate , a.editdate)";
		$sql = $sql .=" end as reg from work_todaywork_project as a left join work_todaywork_project_sort as b on(a.idx=project_idx)";
		$sql = $sql .=" where a.state='0' and b.state='0' and b.email='".$user_id."'";
		//$sql = $sql .=" and b.sort is not null";
		$sql = $sql .=" order by b.sort asc";
	}else{
		$sql = "select a.idx, a.title, a.email, b.sort, convert(char(16), a.editdate, 120) as edate, convert(char(16), a.regdate, 120) as rdate,";
		$sql = $sql .=" case when a.editdate is null then datediff(MI, a.regdate , getdate() ) when a.editdate is not null then datediff(MI, a.regdate , a.editdate)";
		$sql = $sql .=" end as reg from work_todaywork_project as a left join work_todaywork_project_sort as b on(a.idx=project_idx)";
		$sql = $sql .=" where a.state='0' and b.state='0' and b.email='".$user_id."'";
		$sql = $sql .=" and b.sort is not null";
		$sql = $sql .=" order by b.sort asc";
	}
	$project_info = selectAllQuery($sql);

	if(!$project_info['idx']){
		/*$sql = "select idx, sort, email, name, part, title, convert(char(16), editdate, 120) as edate, convert(char(16), regdate, 120) as rdate, CONVERT(CHAR(10), editdate, 103), CONVERT(CHAR(8), editdate, 24),";
		$sql = $sql .= " case when editdate is null then datediff(MI, regdate , getdate() ) when editdate is not null then datediff(MI, editdate , getdate())";
		$sql = $sql .= " end as reg";
		$sql = $sql .= " from work_todaywork_project where state='0' and companyno='".$companyno."' order by idx desc";
		*/
		$sql = "select a.idx, a.title, convert(char(16), a.editdate, 120) as edate, convert(char(16), a.regdate, 120) as rdate,";
		$sql = $sql .= " case when a.editdate is null then datediff(MI, a.regdate , getdate() ) when a.editdate is not null then datediff(MI, a.regdate , a.editdate) end as reg";
		$sql = $sql .= " from work_todaywork_project as a, work_todaywork_project_sort as b where a.idx=b.project_idx and a.state=0 and b.state=0 and a.companyno='".$companyno."'";
		$sql = $sql .= " group by a.idx , a.title, convert(char(16), a.editdate, 120), convert(char(16), a.regdate, 120) ,";
		$sql = $sql .= " case when a.editdate is null then datediff(MI, a.regdate , getdate() ) when a.editdate is not null then datediff(MI, a.regdate , a.editdate) end ";
		$sql = $sql .= " order by idx desc";
		$project_info = selectAllQuery($sql);
	}

	if($user_id=='sadary0@nate.com'){
	//	echo $sql;
	}

	//나의파티
	$sql = "select a.idx, a.title, a.email, b.sort, convert(char(16), a.editdate, 120) as edate, convert(char(16), a.regdate, 120) as rdate, ";
	$sql = $sql .= " case when a.editdate is null then datediff(MI, a.regdate , getdate() ) when a.editdate is not null then datediff(MI, a.regdate , a.editdate) end as reg";
	$sql = $sql .= " from work_todaywork_project as a left join work_todaywork_project_user as b on(a.idx=project_idx)";
	$sql = $sql .= " where a.state='0' and b.state='0' and b.email='".$user_id."' order by b.sort asc";
	$project_myinfo = selectAllQuery($sql);


	//프로젝트 생성한 아이디 내역
	$sql = "select idx, email from work_todaywork_project where state='0' and companyno='".$companyno."' order by sort asc";
	$project_part_info = selectAllQuery($sql);
	for($i=0; $i<count($project_part_info['idx']); $i++){
		$project_part_info_idx = $project_part_info['idx'][$i];
		$project_part_info_email = $project_part_info['email'][$i];
		$project_part_info_auth[$project_part_info_idx] = $project_part_info_email;
	}

	//전체 프로젝트 내역
	$sql = "select idx, project_idx, email, name, part from work_todaywork_project_user where state='0' and companyno='".$companyno."' order by idx asc";
	//echo $sql;
	$project_user_info = selectAllQuery($sql);
	for($i=0; $i<count($project_user_info['idx']); $i++){
		$project_user_idx = $project_user_info['project_idx'][$i];
		$project_user_email = $project_user_info['email'][$i];
		$project_user_name = $project_user_info['name'][$i];
		$project_user_part = $project_user_info['part'][$i];
		$project_user_list[$project_user_idx]['email'][] = $project_user_email;
		$project_user_list[$project_user_idx]['name'][] = $project_user_name;
		$project_user_list[$project_user_idx]['part'][] = $project_user_part;

		$project_use[$project_user_idx][] = $project_user_email;
	}



	//도전완료 챌린지
	$sql = " select count(1) cnt from ( select a.idx from work_challenges as a left join work_challenges_result as b on(a.idx=b.challenges_idx)";
	$sql = $sql .=" where a.state='0'";
	$sql = $sql .=" AND ( (b.state='1' and a.attend=(select count(1) from work_challenges_result where state='1' and challenges_idx=a.idx and email='".$user_id."'))) and a.coaching_chk='0' and a.view_flag='0' and a.temp_flag='0' and a.template='0' and a.company ='".$companyno."' group by a.idx) as c";
	$chall_list_row = selectQuery($sql);
	/*if($chall_list_row){
		$challenges_num = $chall_list_row['cnt'];
	}else{
		$challenges_num = 0;
	}*/



	//코인보상
	$sql = "select idx,code,coin,icon,memo from work_coin_reward_info where state='0' and kind='live' order by idx asc";
	$coin_reward_info = selectAllQuery($sql);

	//코인 보상 대상 추출
	$sql = "select idx, kind, sort, cnt, day_flag, day, memo from work_cp_reward_info where state='0' order by sort asc";
	$work_cp_reward_info = selectAllQuery($sql);
	for($i=0; $i<count($work_cp_reward_info['idx']); $i++){
		$work_cp_reward_info_kind = $work_cp_reward_info['kind'][$i];
		$work_cp_reward_info_sort = $work_cp_reward_info['sort'][$i];
		$work_cp_reward_info_cnt = $work_cp_reward_info['cnt'][$i];
		$work_cp_reward_info_day = $work_cp_reward_info['day'][$i];
		$work_cp_reward_info_day_flag = $work_cp_reward_info['day_flag'][$i];
		$work_cp_reward_info_memo = $work_cp_reward_info['memo'][$i];
		
		//if($work_cp_reward_info_day != 0){
			$reward_cp_info[$work_cp_reward_info_sort][$work_cp_reward_info_kind][$work_cp_reward_info_day_flag]['memo'] = $work_cp_reward_info_memo;
			$reward_cp_info[$work_cp_reward_info_sort][$work_cp_reward_info_kind][$work_cp_reward_info_day_flag]['cnt'] = $work_cp_reward_info_cnt;

			$reward_cp_info2[$work_cp_reward_info_sort]['memo'] = $work_cp_reward_info_memo;
			$reward_cp_info2[$work_cp_reward_info_sort]['cnt'] = $work_cp_reward_info_cnt;

		//}else{
		//	$cp_reward_like[$work_cp_reward_info_sort][$work_cp_reward_info_kind][$work_cp_reward_info_day_flag][$work_cp_reward_info_cnt] = $work_cp_reward_info_cnt;
		//}
	}

	$yesterday = date("Y-m-d", strtotime(TODATE." -1 day"));
	
	//전날 좋아요1등
	$sql = "select top 1 email, count(1) as cnt from work_todaywork_like where state='0' and workdate = '".$yesterday."'";
	$sql = $sql .= " group by email order by cnt desc";
	$like_day_info = selectQuery($sql);
	if($like_day_info['email']){
		$like_day_info_list[$like_day_info['email']] = $like_day_info['cnt'];
	}

	//전날 역량1등
	$sql = "select top 1 email, sum( type1 ) as type1, sum( type2) as type2, sum( type3) as type3, sum( type4) as type4, sum( type5) as type5, sum( type6) as type6";
	$sql = $sql .= " , sum( type1 ) + sum( type2) + sum( type3) + sum( type4) + sum( type5) + sum( type6) as tot";
	$sql = $sql .= " from work_cp_reward_list where state='0' and workdate='".$yesterday."' group by email order by tot desc";
	$reward_day_info = selectQuery($sql);
	if($reward_day_info['email']){
		$reward_day_info_list[$reward_day_info['email']] = $reward_day_info['tot'];
	}


	//전날 역량100점 획득
	$sql = "select email, sum( type1 ) as type1, sum( type2) as type2, sum( type3) as type3, sum( type4) as type4, sum( type5) as type5, sum( type6) as type6";
	$sql = $sql .= " , sum( type1 ) + sum( type2) + sum( type3) + sum( type4) + sum( type5) + sum( type6) as tot";
	$sql = $sql .= " from work_cp_reward_list where state='0' and workdate between '".$month_first_day."' and '".$month_last_day."' group by email order by tot desc";
	$reward_tot_info = selectAllQuery($sql);
	for($i=0; $i<count($reward_tot_info['email']); $i++){
		$reward_tot_info_email = $reward_tot_info['email'][$i];
		$reward_tot_info_tot = $reward_tot_info['tot'][$i];
		$reward_tot_info_list[$reward_tot_info_email] = $reward_tot_info_tot;
	}

	if($user_id=='sadary0@nate.com'){
		print "<pre>";

		echo count($reward_cp_info);


		print_r($reward_cp_info);


		print_r($reward_tot_info_list);
		print "</pre>";
		
		
		//echo ($like_day_info_list['date'][$user_id]['2022-06-13']);
	}

?>

<script type="text/javascript">
	
	var work_reward_title_arr = new Array();
	<?
	//역량지표 타이틀
	foreach($work_reward_title as $key => $val){?>
		work_reward_title_arr["<?=$key?>"] = "<?=$val?>";
	<?}?>

	$(document).ready(function(){


		//$(document).on("droppable", "#ldr .ldr_li:not(.ldr_me)", function() {
		//$(this).droppable({
		//$(".ldr_li:not(.ldr_me)").droppable({
		//드래그앤드랍
		
		$(document).on("click", "#ldr .ldr_li:not(.ldr_me)", function() {
			//console.log("%%%%%");
		});


		var obj_ldr = $("#ldr .ldr_li:not(.ldr_me)");

		$(document).on("click", "#ldr .ldr_li:not(.ldr_me)", function() {

			//console.log(99999);

		//obj_ldr.on("drop" , function (){
			//$("#ldr .ldr_li:not(.ldr_me)").droppable({
			$(this).droppable({
			//$("#ldl3").droppable({
				accept:"#ldr .ldr_me",
				classes: {
					"ui-droppable-active": "ldr_active",
					"ui-droppable-hover": "ldr_hover"
				},
				drop:function(event, ui) {
					console.log("드래그앤드랍");
					//$(this).append($("ui.draggable").clone());
					$(".lt_ul_02 .textarea_lt").val("");
					var ldr_img = $(this).find(".ldr_user_imgs").css("background-image").replace(/^url\(['"](.+)['"]\)/, '$1');
					var ldr_name = $(this).find(".ldr_user_name").text();
					var ldr_team = $(this).find(".ldr_user_team").text();
					$(".lt_ul_01 .ll_li .ll_name_team").text(ldr_team);
					$(".lt_ul_01 .ll_li .ll_name_user").text(ldr_name);
					var ldr_no = $(this).find(".ldr_user_name").attr("id").replace("ldr_user_name_","");
					var lt_id = $("#ldr_user_id_"+ ldr_no).val();
					$("#lt_id").val(lt_id);
					$(".ll_li .ll_img_user").css("background-image", "url(" + ldr_img + ")");
					$("#layer_team").show();
					$("#team_input").focus();
				}
			});
		});


		$(document).on("click", "#template_list", function() {
            $(this).sortable({
		//		console.log(111);
			});
		});

		
		
		//$(".ldr_li").addClass("ldr_li ui-droppable");
		if($(".ldr_li")){
			$(".ldr_li").trigger("click");
		}



		/*$("#ldr .ldr_li:not(.ldr_me)").droppable({
			//$(this).droppable({
			//$("#ldl3").droppable({
				accept:"#ldr .ldr_me",
				classes: {
					"ui-droppable-active": "ldr_active",
					"ui-droppable-hover": "ldr_hover"
				},
				drop:function(event, ui) {
					console.log("드래그앤드랍");
					//$(this).append($("ui.draggable").clone());
					$(".lt_ul_02 .textarea_lt").val("");
					var ldr_img = $(this).find(".ldr_user_imgs").css("background-image").replace(/^url\(['"](.+)['"]\)/, '$1');
					var ldr_name = $(this).find(".ldr_user_name").text();
					var ldr_team = $(this).find(".ldr_user_team").text();
					$(".lt_ul_01 .ll_li .ll_name_team").text(ldr_team);
					$(".lt_ul_01 .ll_li .ll_name_user").text(ldr_name);
					var ldr_no = $(this).find(".ldr_user_name").attr("id").replace("ldr_user_name_","");
					var lt_id = $("#ldr_user_id_"+ ldr_no).val();
					$("#lt_id").val(lt_id);
					$(".ll_li .ll_img_user").css("background-image", "url(" + ldr_img + ")");
					$("#layer_team").show();
					$("#team_input").focus();
				}
		});*/










		//프로젝트 사용자 추가
		//$(".ldl_box").droppable({

		$(document).on("click", ".ldl_box", function() {

			$(this).droppable({
				accept:"#ldr .ldr_me",
				classes: {
					"ui-droppable-active": "ldl_active",
					"ui-droppable-hover": "ldl_hover"
				},
				drop:function(event, ui) {
					var ldl_tit = $(this).find(".ldl_box_tit").text();
					$(".lp_in .lp_tit").text(ldl_tit);

					//var project_idx = $(this).find("button[id^=ldl_box_close_]").attr("value");
					var project_idx = $(this).attr("value");
					if(project_idx){
						$("#plus_idx").val(project_idx);
					}

					var me_img = $(".ldr_me .ldr_user_img .ldr_user_imgs").css("background-image").replace(/^url\(['"](.+)['"]\)/, '$1');
					var me_user = $(".ldr_me .ldr_user_desc .ldr_user_name").text();
					var me_team = $(".ldr_me .ldr_user_desc .ldr_user_team").text();
					$(this).find(".ldl_box_user ul").append('<li class="ldl_box_me"><div class="ldl_box_img" style="background-image:url('+me_img+')"></div><div class="ldl_box_user"><strong>'+me_user+'</strong><span>'+me_team+'</span></div></li>');
					$("#layer_plus").show();
				}
			});
		});




		$(".rew_conts_list_in ul li button").click(function(){
			$(this).parent("li").toggleClass("on");
		});

		$(".rew_btn_icons_more").click(function(){
			$(".rew_icons").toggle();
		});

		$(".rew_mypage_tab_04 a").click(function(){
			$(".rew_mypage_tab_04 li").removeClass("on");
			$(this).parent("li").addClass("on");
		});


		setTimeout(function(){
			$(".tabs_on").addClass("now_01");
		},1100);

		$(".btn_next_step_02").click(function(){
			$(".rew_cha_step_01").addClass("step_z");
			$(".rew_cha_step_02").addClass("step_z");
			$(".rew_cha_step_03").removeClass("step_z");
			$(".rew_cha_step_01").animate({"left":-100+"%"},700);
			$(".rew_cha_step_02").animate({"left":0+"%"},700);
			$(".rew_cha_step_03").animate({"left":100+"%"},700);
			
			setTimeout(function(){
				$(".rew_cha_step_01").animate({scrollTop :0}, 0);
				$(".rew_cha_step_02").animate({scrollTop :0}, 0);
				$(".rew_cha_step_03").animate({scrollTop :0}, 0);
			},200);

			setTimeout(function(){
				$(".tabs_on").addClass("now_02");
			},1100);
		});
		$(".btn_prev_step_01").click(function(){
			$(".rew_cha_step_01").addClass("step_z");
			$(".rew_cha_step_02").addClass("step_z");
			$(".rew_cha_step_03").removeClass("step_z");
			$(".rew_cha_step_01").animate({"left":0+"%"},700);
			$(".rew_cha_step_02").animate({"left":100+"%"},700);
			$(".rew_cha_step_03").animate({"left":-100+"%"},700);
			
			setTimeout(function(){
				$(".rew_cha_step_01").animate({scrollTop :0}, 0);
				$(".rew_cha_step_02").animate({scrollTop :0}, 0);
				$(".rew_cha_step_03").animate({scrollTop :0}, 0);
			},200);
			
			setTimeout(function(){
				$(".tabs_on").removeClass("now_02");
			},1100);
		});
		$(".btn_next_step_03").click(function(){
			$(".rew_cha_step_01").removeClass("step_z");
			$(".rew_cha_step_02").addClass("step_z");
			$(".rew_cha_step_03").addClass("step_z");
			$(".rew_cha_step_01").animate({"left":100+"%"},700);
			$(".rew_cha_step_02").animate({"left":-100+"%"},700);
			$(".rew_cha_step_03").animate({"left":0+"%"},700);
			
			setTimeout(function(){
				$(".rew_cha_step_01").animate({scrollTop :0}, 0);
				$(".rew_cha_step_02").animate({scrollTop :0}, 0);
				$(".rew_cha_step_03").animate({scrollTop :0}, 0);
			},200);
			
			setTimeout(function(){
				$(".tabs_on").addClass("now_03");
			},1100);
		});
	

		$(".rew_live_sort .btn_sort_on").click(function(){
			$(".rew_live_sort").addClass("on");
		});
		$(".rew_live_sort").mouseleave(function(){
			$(".rew_live_sort").removeClass("on");
		});
		$(".rew_live_sort ul li button").click(function(){
			$(".rew_live_sort").removeClass("on");
		});

		$(".list_function_sort .btn_sort_on").click(function(){
			$(".list_function_sort").addClass("on");
		});
		$(".list_function_sort").mouseleave(function(){
			$(".list_function_sort").removeClass("on");
		});
		$(".list_function_sort ul li button").click(function(){
			$(".list_function_sort").removeClass("on");
		});

		


		$(".rew_conts_scroll_07").scroll(function(){
			var lbt = $(".rew_live").offset().top;
			if(lbt < 120){
				$(".rew_live_my").addClass("pos_fix");
			}else{
				$(".rew_live_my").removeClass("pos_fix");
			}
		});
		$(".list_area").scroll(function(){
			var rct = $(".list_area_in").position().top;
			if(rct<60){
				$(".layer_result_right").addClass("pos_fix");
			}else{
				$(".layer_result_right").removeClass("pos_fix");
			}
		});

		$(".layer_report .report_area").scroll(function(){
			var rct = $(".layer_report .report_area_in").position().top;
			if(rct<60){
				$(".layer_report .layer_result_right").addClass("pos_fix");
			}else{
				$(".layer_report .layer_result_right").removeClass("pos_fix");
			}
		});

		$(".layer_challenge .report_area").scroll(function(){
			var rat = $(".layer_challenge .report_area_in").position().top;
			console.log(rat);
			if(rat<50){
				$(".layer_challenge .layer_result_right").addClass("pos_fix");
			}else{
				$(".layer_challenge .layer_result_right").removeClass("pos_fix");
			}
		});

		$(".desc_lr_01 .report_area").scroll(function(){
			var rat = $(".desc_lr_01 .report_area_in").position().top;
			console.log(rat);
			if(rat<50){
				$(".layer_today .layer_result_right").addClass("pos_fix");
			}else{
				$(".layer_today .layer_result_right").removeClass("pos_fix");
			}
		});
		$(".desc_lr_02 .report_area").scroll(function(){
			var rat = $(".desc_lr_02 .report_area_in").position().top;
			console.log(rat);
			if(rat<50){
				$(".layer_today .layer_result_right").addClass("pos_fix");
			}else{
				$(".layer_today .layer_result_right").removeClass("pos_fix");
			}
		});
		$(".desc_lr_03 .report_area").scroll(function(){
			var rat = $(".desc_lr_03 .report_area_in").position().top;
			console.log(rat);
			if(rat<50){
				$(".layer_today .layer_result_right").addClass("pos_fix");
			}else{
				$(".layer_today .layer_result_right").removeClass("pos_fix");
			}
		});


		$("#go_view_01").click(function(){
			var offset1 = $(".rew_conts_scroll_06").offset();
			$(".rew_conts_scroll_06").animate({scrollTop : 0}, 400+(offset1.top/2));
		});
		var offset2 = $(".rew_cha_view_result").position();
		$("#go_view_02").click(function(){
			var offset1 = $(".rew_conts_scroll_06").offset();
			var offset_sum = Math.abs(offset1.top - offset2.top);
			setTimeout(function(){
				$(".rew_conts_scroll_06").animate({scrollTop : offset2.top - 155}, offset_sum/4+200);
			},100);
		});
		var offset3 = $(".rew_cha_view_masage").position();
		$("#go_view_03").click(function(){
			var offset1 = $(".rew_conts_scroll_06").offset();
			//console.log(offset2.top);
			var offset_sum = Math.abs(offset1.top - offset3.top);
			setTimeout(function(){
				$(".rew_conts_scroll_06").animate({scrollTop : offset3.top - 155}, offset_sum/4+200);
			},100);
		});


		$(".conts_tab ul li button").click(function(){
			var drM_li = $(this).parent("li").index();

			var offt = $(".conts_main > div:eq("+drM_li+")").offset();
			var tabt = $(".conts_tab").offset();
			var matht = Math.abs(tabt.top - offt.top);
			$("html, body").animate({scrollTop:offt.top-101},matht/8+200);

			return false;
		});


		$("#open_layer_user").click(function(){
			$(".layer_user").show();
			$("#layer_test_02").hide();
			$("#layer_test_03").hide();
			$("#layer_test_01").show();
			$(".layer_user_info dl").addClass("on");
			$(".layer_user_info dd button").removeClass("on");
			$(".layer_user_submit").removeClass("on");
			$(".layer_user_info").animate({scrollTop :0}, 0);
		});

		$(".list_function_type .type_list").click(function(){
			$(".list_function_type button").removeClass("on");
			$(this).addClass("on");
			$(".list_box .list_conts").removeClass("type_img");
			$(".list_box .list_conts").removeClass("type_on");
			$(".list_box .list_conts").addClass("type_list");
			$(".list_box .list_conts").addClass("type_on");
		});
		$(".list_function_type .type_img").click(function(){
			$(".list_function_type button").removeClass("on");
			$(this).addClass("on");
			$(".list_box .list_conts").removeClass("type_list");
			$(".list_box .list_conts").removeClass("type_on");
			$(".list_box .list_conts").addClass("type_img");
			$(".list_box .list_conts").addClass("type_on");
		});

		$(".list_conts .list_ul li button").click(function(){
			$(this).toggleClass("on");
		});
		$(".rew_cha_view_result .title_area .title_more").click(function(){
			$(".layer_result").show();
		});
		$(".rew_cha_view_result li button").click(function(){
			$(".layer_result").show();
		});
		$(".rew_cha_view_header .view_user li button").click(function(){
			$(".layer_result").show();
		});
		$(".layer_result .layer_close button").click(function(){
			$(".layer_result").hide();
		});
		$(".layer_result_user li button").click(function(){
			$(".layer_result_user li button").removeClass("on");
			$(this).addClass("on");
		});

		$(".rew_cha_view_masage .title_area .title_more").click(function(){
			$(".layer_masage").show();
		});
		$(".masage_zone").click(function(){
			$(".layer_masage").show();
		});
		$(".masage_area_in").click(function(){
			$(this).toggleClass("on");
		});
		$(".layer_masage .layer_close button").click(function(){
			$(".layer_masage").hide();
		});

		$(".layer_report .layer_close button").click(function(){
			$(".layer_report").hide();
			$(".layer_report").css({opacity:0});
		});

		$(".layer_challenge .layer_close button").click(function(){
			$(".layer_challenge").hide();
			$(".layer_challenge .report_cha .rew_cha_list_ul li").removeClass("sli");
		});

		$(".layer_today .layer_close button").click(function(){
			$(".layer_today").hide();
			$(".layer_today .report_cha .rew_cha_list_ul li").removeClass("sli");
		});

		$(".join_type_file .btns_cha_cancel").click(function(){
			$(".join_type_file").hide();
		});
		$(".join_type_masage .btns_cha_cancel").click(function(){
			$(".join_type_masage").hide();
		});
		$(".join_type_mix .btns_cha_cancel").click(function(){
			$(".join_type_mix").hide();
		});
		$(".btn_join_ok").click(function(){
			$(".layer_cha_join").show();
		});


		$(".layer_result_list.desc_lr_01").hide();
		$(".layer_result_list.desc_lr_02").hide();
		$(".layer_result_list.desc_lr_03").hide();
		$(".layer_today").hide();
		$(".layer_today").css({opacity:1});
		$(".btn_eval").click(function(){
			$(".layer_today .layer_result_right").removeClass("pos_fix");
			$(".layer_result_list.desc_lr_01").css({opacity:0});
			$(".layer_result_list.desc_lr_02").css({opacity:0});
			$(".desc_lr_03 .report_cha .rew_cha_list_ul li").removeClass("sli");
			$(".layer_today").show();
			$(".layer_result_tab li button").removeClass("on");
			$(".layer_result_tab li .btn_lr_03").addClass("on");
			$(".layer_result_list").hide();
			$(".layer_result_list.desc_lr_03").css({opacity:1});
			$(".layer_result_list.desc_lr_03").show();
			$(".desc_lr_03 .report_cha .rew_cha_list_ul li").each(function(){
				var tis = $(this);
				var tindex = $(this).index();
				setTimeout(function(){
					tis.addClass("sli");
				},600+tindex*200);
			});
		});

		<?//if($user_id=='sadary0@nate.com' || $user_id=='qohse@nate.com'){?>
			//역량지표레이어 
			$(".ldr_chall_num").click(function(){
				var name = $(this).parent().parent().parent().find(".ldr_user_name").text();
				var team = $(this).parent().parent().parent().find(".ldr_user_team").text();
				var ldr_img = $(this).parent().parent().parent().find(".ldr_user_imgs").css("background-image").replace(/^url\(['"](.+)['"]\)/, '$1');
				
				var v = $(this).parent().parent().parent().html();
				//console.log(v);
				$(".rl_name_user").text(name);
				$(".rl_name_team").text(team);
				$(".rl_user_img_in").css("background-image", "url(" + ldr_img + ")");
				$("#radar_layer").show();
				$(".btn_rl_01").trigger("click");
				rl_chart_run();
			});
		<?//}?>


		$(".ldr_chall_num").click(function(){
			/*
			$(".layer_today .layer_result_right").removeClass("pos_fix");
			$(".layer_result_list.desc_lr_01").css({opacity:0});
			$(".layer_result_list.desc_lr_03").css({opacity:0});
			$(".desc_lr_02 .report_cha .rew_cha_list_ul li").removeClass("sli");
			//$(".layer_today").show();
			$(".layer_result_tab li button").removeClass("on");
			$(".layer_result_tab li .btn_lr_03").addClass("on");
			$(".layer_result_list").hide();
			
			$(".layer_result_list.desc_lr_02").css({opacity:1});
			$(".layer_result_list.desc_lr_02").show();
			$(".btn_lr_03").trigger("click");

			//$(".desc_lr_02 .report_cha .rew_cha_list_ul li").each(function(){
			//	var tis = $(this);
			//	var tindex = $(this).index();
			//	setTimeout(function(){
			//		tis.addClass("sli");
			//	},600+tindex*200);
			//});
			*/
		});


		$(".live_list .live_list_box").each(function(){
			var tis = $(this);
			var tindex = $(this).index();
			var bar_t = tis.find(".live_list_today_bar strong").text();
			var bar_b = tis.find(".live_list_today_bar span").text();
			var bar_w = bar_t/bar_b*100;
			setTimeout(function(){
				tis.addClass("sli");
				tis.find(".live_list_today_bar strong").css({width:bar_w+"%"});
			},500+tindex*100);
		});


		$(".ldr_user_state li .ldr_user_state_circle").mouseenter(function(){
			$(".layer_state").removeClass("on");
			$(this).next(".layer_state").addClass("on");
			$(".ldr_list_box").removeClass("zindex");
			$(this).closest(".ldr_list_box").addClass("zindex");
		});
		$(".ldr_user_state li .ldr_user_state_circle").mouseleave(function(){
			$(".layer_state").removeClass("on");
			$(".ldr_list_box").removeClass("zindex");
		});

		$(".layer_report").hide();

		$(".tdw_list .btn_regi_cancel").click(function(){
			$(this).closest(".tdw_list_memo_regi").hide();
		});
		$(".tdw_list .tdw_list_memo_conts_txt strong").click(function(){
			$(this).next(".tdw_list_memo_regi").show();
			var memo_width = $(this).parent(".tdw_list_memo_conts_txt").width();
			$(this).next(".tdw_list_memo_regi").css({"width":memo_width+199});
		});
		$(".tdw_list .btn_memo_del").click(function(){
			$(this).closest(".tdw_list_memo_desc").remove();
		});
		$(".tdw_list .tdw_list_memo").click(function(){
			$(".layer_memo").show();
		});
		$(".layer_memo_cancel").click(function(){
			$(".layer_memo").hide();
		});
		$(".layer_memo_submit").click(function(){
			$(".layer_memo").hide();
			$(".tdw_list_memo").addClass("on");
		});


	});

	
	<?if($member_total_cnt > 0){?>
		var member_total_cnt = '<?=number_format($member_total_cnt)?>';
	<?}?>
</script>
<div class="rew_warp live_warp">
	<div class="rew_warp_in">
		<div class="rew_box">
			<div class="rew_box_in">
				<!-- menu -->
				<? include $home_dir . "/inc_lude/menu.php";?>
				<!-- //menu -->

				<!-- 콘텐츠 -->
				<div class="rew_conts">
					<div class="rew_conts_in">
						<!-- <div class="rew_header">
							<div class="rew_header_in">
								<div class="rew_header_notice">
									<span></span>
								</div>
							</div>
						</div> -->

						<div class="rew_live_my">
							<div class="rew_live_my_in">
								<div class="rew_live_my_left">
									<div class="rew_live_my_user_img">
										<div class="rew_live_my_user_img_bg"></div>
										<div class="rew_live_my_user_imgs" style="background-image:url('<?=$profile_main_img_src?>');"></div>
									</div>
									<div class="rew_live_my_user_name">
										<strong><?=$member_info_name?></strong>
										<span><?=$part_name?></span>
										<input type="hidden" id="chall_user_cnt" value="<?=$member_total_cnt?>">
										<input type="hidden" id="chall_user_chk">
									</div>
									<div class="rew_live_my_list_today" style="display:none;">
										<input type="hidden" id="user_email" value="all">
										<button class="btn_today" value="<?=$member_info_email?>">
											<div class="rew_live_my_list_today_tit">오늘업무</div>
											<div class="rew_live_my_list_today_count"><strong><?=$work_com_list?></strong> / <?=$work_all_list?></div>
										</button>
									</div>
									<div class="rew_live_my_list_cha" style="display:none;">
										<button class="btn_cha" id="btn_cha_layer">
											<div class="rew_live_my_list_cha_tit">챌린지</div>
											<div class="rew_live_my_list_cha_count"><strong></strong></div>
										</button>
									</div>
									<!-- <div class="rew_live_my_eval">
										<button class="btn_eval"><span>역량평가 리포트</span></button>
									</div> -->
								</div>

								<div class="rew_live_my_right">
									<div class="rew_live_my_tit">
										<span>나의 상태</span>
									</div>
									<div class="rew_grid_onoff">
										<div class="rew_grid_onoff_inner">
											<ul>
												<li class="onoff_01">
													<em <?=($live_1=='1')?"class='on'":""?>>출근(<?=$ex_live_1_time?>)</em>
													<div class="btn_switch<?=($live_1=='1')?" on":""?>" id="live_1_bt">
														<strong class="btn_switch_on"></strong>
														<span>버튼</span>
														<strong class="btn_switch_off"></strong>
													</div>
												</li>
											</ul>
										</div>
									</div>

									<div class="rew_grid_onoff">
										<div class="rew_grid_onoff_in">
											<ul>
												<li class="onoff_02">
													<em <?=($live_2=='1')?"class='on'":""?>>집중</em>
													<div class="btn_switch<?=($live_2=='1')?" on":""?>" id="live_2_bt">
														<strong class="btn_switch_on"></strong>
														<span>버튼</span>
														<strong class="btn_switch_off"></strong>
													</div>
												</li>
												<li class="onoff_03">
													<em <?=($live_3=='1')?"class='on'":""?>>자리비움</em>
													<div class="btn_switch<?=($live_3=='1')?" on":""?>" id="live_3_bt">
														<strong class="btn_switch_on"></strong>
														<span>버튼</span>
														<strong class="btn_switch_off"></strong>
													</div>
												</li>
												<?
												/*<li class="onoff_04">
													<em <?=($live_4=='1')?"class='on'":""?>>퇴근</em>
													<div class="btn_switch<?=($live_4=='1')?" on":""?>" id="live_4_bt">
														<strong class="btn_switch_on"></strong>
														<span>버튼</span>
														<strong class="btn_switch_off"></strong>
													</div>
												</li>
												*/
												?>
											</ul>
										</div>
									</div>
								</div>
							</div>
						</div>

						<div class="rew_conts_scroll_07">

							<div class="rew_live">
								<div class="rew_live_in">

									<div class="rew_live_func">
										<div class="rew_live_func_in">
											<div class="rew_live_count">
												<span>LIVE</span>
												<strong id="lives_list_cnt"><span><?=$member_live_cnt?></span>(<?=$member_all_cnt?>)</strong>
											</div>

											<div class="rew_live_sort" id="rew_live_sort_list">
												<div class="rew_live_sort_in">
													<button class="btn_sort_on" id="rew_live_sort"><span>전체보기</span></button>
													<ul>
														<li><button value="all"><span>전체보기</span></button></li>
														<li><button value="on"><span>접속자보기</span></button></li>
														<li><button value="off"><span>미접속자보기</span></button></li>
													</ul>
												</div>
											</div>

											<div class="rew_live_search">
												<div class="rew_live_search_box">
													<input type="text" class="input_search" placeholder="이름, 부서명을 검색" id="input_index_search_new"/>
													<button id="lives_index_search_bt_new"><span>검색</span></button>
												</div>
											</div>
										</div>
									</div>

									<div class="live_drop_right">
										<div class="ldr_in">
											<ul class="ldr_ul" id="ldr">

												<?
												for($i=0; $i<count($member_list_info['idx']); $i++){
													
													$like_jjim=0;
													
													$works_like_cnt = 0;
												
													//마음전하기
													$like_layer0 = false;

													//출근
													$like_layer1 = false;

													//오늘업무
													$like_layer2 = false;

													//
													$ldr_li = false;
													
													$member_list_idx = $member_list_info['idx'][$i];
													$member_list_email = $member_list_info['email'][$i];
													$gender = $member_list_info['gender'][$i];
	
													$member_list_name = $member_list_info['name'][$i];
													$member_list_part = $member_list_info['part'][$i];
	
													$live_1_time = $member_list_info['live_1_time'][$i];
													if($live_1_time){
														$tmp_live_1_time = explode(":", $live_1_time);
														if($tmp_live_1_time){
															$ex_live_1_time = (int)$tmp_live_1_time[0].":" .$tmp_live_1_time[1] ."";
														}
													}else{
														$ex_live_1_time = "";
													}

													$member_list_live_1 = $member_list_info['live_1'][$i];
													$member_list_live_2 = $member_list_info['live_2'][$i];
													$member_list_live_3 = $member_list_info['live_3'][$i];
													$member_list_live_4 = $member_list_info['live_4'][$i];
													$profile_type = $member_list_info['profile_type'][$i];
													$profile_img_idx = $member_list_info['profile_img_idx'][$i];

													//프로필 케릭터 사진
													$profile_img_src = profile_img_info($member_list_email);

													$member_work0 = $work_live[$member_list_email][0];
													$member_work1 = $work_live[$member_list_email][1];
													$member_work_all = $member_work0 + $member_work1;

													//업무일정이 있는경우
													if ($work_flag_list[$today][$member_list_email]){
														$member_decide_cnt = count($work_flag_list[$today][$member_list_email]);
														for($j=0; $j<$member_decide_cnt; $j++){
															$member_decide_flag[$j] = $work_flag_list[$today][$member_list_email][$j];
														}

													}else{
														$member_decide_flag = "";
														$member_decide_cnt = 0;
													}


													//오늘 출근 제일 빨리함
													if(!$like_flag_list[$member_list_email][1][0] && $mem_top_arr[TODATE] == $member_list_email){
														$like_jjim++;
													}

													//오늘업무 제일 많이씀
													if(!$like_flag_list[$member_list_email][2][0] && $works_top_arr[TODATE] == $member_list_email){
														$like_jjim++;
													}

													if($like_flag_list[$member_list_email][0][0]){
														$like_layer0 = true;
													}else{
														$like_layer0 = false;
													}

													if ($like_flag_list[$member_list_email][1][0]){
														$like_layer1 = true;
													}else{
														$like_layer1 = false;
													}

													if ($like_flag_list[$member_list_email][2][0]){
														$like_layer2 = true;
													}else{
														$like_layer2 = false;
													}

													if($member_list_email == 'sadary0@nate.com' ){

														
														/*echo $like_flag_list[$member_list_email][1][0] . "<<<< ";
														echo "\n";
														echo "like_jjim=".$like_jjim;
														echo "\n";
														echo "like_layer0=". $like_layer0;
														echo "\n";
														echo "like_layer1=". $like_layer1;
														echo "\n";
														echo "like_layer2=". $like_layer2;
														echo "\n";*/
														
													}

													if(!$like_layer0 && !$like_layer1 && !$like_layer2){
														$ldr_li = false;
													}else{
														$ldr_li = true;
													}


													//하트 채우기
													//출근 제일 빨리 함
													if( $like_flag_list[$member_list_email][1][0] && $mem_top_arr[TODATE] == $member_list_email){
														$like_class_on = " on";
													//업무 제일 많이 씀
													}else if($like_flag_list[$member_list_email][2][0] && $works_top_arr[TODATE] == $member_list_email){
														$like_class_on = " on";
													}else{

														//그냥 마음전하기
														if($like_flag_list[$member_list_email][0][0]){
															$like_class_on = " on";
														}else{
															$like_class_on = "";
														}
													}



												?>
													<li class="ldr_li<?=$user_id==$member_list_email?" ldr_me":""?><?=$member_list_live_1=='0'?" live_none":""?>"<?=$user_id==$member_list_email?" id='live_ldr_me'":""?>>
														<div class="ldr_li_in">
															<div class="ldr_function">
																<div class="ldr_function_in">
																	<div class="ldr_time"><?=$ex_live_1_time?></div>
																	<?if($user_id!=$member_list_email){?>
																		<button class="ldr_menu<?=$like_jjim?"":" jjim_only"?><?=$like_class_on?>"<?=$user_id!=$member_list_email && $ldr_li==false?"id='jjim_only_".$member_list_idx."'":""?> value="<?=$member_list_idx?>"><span>좋아요</span></button>
																	<?}?>
																</div>
															</div>


															<?

															//자기자신은 좋아요 기능 제한함
															if($user_id != $member_list_email){?>

																<?if($like_jjim){?>

																	<div class="ldr_popup" id="ldr_popup">
																		<div class="ldr_popup_in">
																			<ul>
																				<?if(!$like_flag_list[$member_list_email][1][0] && $mem_top_arr[TODATE] == $member_list_email){?>
																					<li><button value="<?=$member_list_idx?>"><span>출근 제일 빨리 함</span></button></li>
																				<?}?>

																				<?if(!$like_flag_list[$member_list_email][2][0] && $works_top_arr[TODATE] == $member_list_email){?>
																					<li><button value="<?=$member_list_idx?>"><span>오늘업무 제일 많이 씀</span></button></li>
																				<?}?>

																				<?//if(!$like_flag_list[$member_list_email][0][0]){?>
																					<li><button value="<?=$member_list_idx?>"><span>그냥 마음전하기</span></button></li>
																				<?//}?>
																			</ul>
																		</div>
																	</div>

																<?}else{?>

																	<?//if(!$like_flag_list[$member_list_email][0][0]){?>
																		<div class="ldr_popup" id="ldr_popup">
																			<div class="ldr_popup_in">
																				<ul>
																					<li><button value="<?=$member_list_idx?>"><span>그냥 마음전하기</span></button></li>
																				</ul>
																			</div>
																		</div>
																	<?//}?>
																<?}?>
															<?}?>

															<div class="ldr_user">
																<div class="ldr_user_in">
																	<div class="ldr_user_desc">
																		<div class="ldr_user_name" id="ldr_user_name_<?=$member_list_idx?>"><?=$member_list_name?></div>
																		<div class="ldr_user_team" id="ldr_user_team_<?=$member_list_idx?>"><?=$member_list_part?></div>
																		<input type="hidden" id="ldr_user_id_<?=$member_list_idx?>" value="<?=$member_list_email?>">
																	</div>
																	<div class="ldr_user_style">
																		<div class="ldr_user_img">
																			<div class="ldr_circle circle_01"><canvas width="64" height="64"></canvas></div>
																			<div class="ldr_user_img_bg"></div>
																			<div class="ldr_user_imgs" id="ldr_user_imgs_<?=$member_list_idx?>" style="background-image:url(<?=$profile_img_src?>);"></div>
																		</div>
																		<div class="ldr_user_state">
																			<div class="ldr_user_state_in">
																				<ul <?=$user_id==$member_list_email?" id='ldr_me_state'":""?>>
																					<?if($member_list_live_2 == "1"){?>
																						<li class="state_01">
																							<div class="ldr_user_state_circle">
																								<strong>집중</strong>
																							</div>
																							<div class="layer_state layer_state_01">
																								<div class="layer_state_in">
																									<p>업무에 집중하고 있습니다.</p>
																									<em></em>
																								</div>
																							</div>
																						</li>
																					<?}?>

																					<?if($member_list_live_3 == "1"){?>
																						<li class="state_02">
																							<div class="ldr_user_state_circle">
																								<strong>잠시</strong>
																							</div>
																							<div class="layer_state layer_state_02">
																								<div class="layer_state_in">
																									<p>잠시 자리를 비웁니다.</p>
																									<em></em>
																								</div>
																							</div>
																						</li>
																					<?}?>

																					<?if($member_list_live_4 == "1"){?>
																						<li class="state_03">
																							<div class="ldr_user_state_circle">
																								<strong>퇴근</strong>
																							</div>
																							<div class="layer_state layer_state_03">
																								<div class="layer_state_in">
																									<p>업무를 모두 종료 하였습니다.</p>
																									<em></em>
																								</div>
																							</div>
																						</li>
																					<?}?>


																					<?if ($member_decide_flag){?>


																						<?if( count($member_decide_flag) == '1'){?>

																							<li class="state_04">
																								<div class="ldr_user_state_circle">
																									<strong><?=$work_decide_list[$work_flag_list[$today][$member_list_email][0]]?></strong>
																								</div>
																								<div class="layer_state layer_state_04">
																									<div class="layer_state_in">
																										<p><span><?=$work_decide_list[$work_flag_list[$today][$member_list_email][0]]?></span> 일정이 있습니다.</p>
																										<em></em>
																									</div>
																								</div>
																							</li>

																						<?}else{?>

																							<li class="state_04">
																								<div class="ldr_user_state_circle">
																									<strong>일정</strong>
																								</div>
																								<div class="layer_state layer_state_04">
																									<div class="layer_state_in">
																										<p><span>
																										<?for($k=0; $k<$member_decide_cnt; $k++){
																											echo $work_decide_list[$work_flag_list[$today][$member_list_email][$k]];
																											if($work_flag_list[$today][$member_list_email][$k] != end($work_flag_list[$today][$member_list_email])){
																												$comma = ", ";
																											}else{
																												$comma = "";
																											}
																											echo $comma;
																										}?>
																										</span> 일정이 있습니다.</p>
																										<em></em>
																									</div>
																								</div>
																							</li>
																						<?}?>
																					<?}?>

																				</ul>
																			</div>
																		</div>
																	</div>
																</div>
															</div>

															<div class="ldr_numbers">
																<div class="ldr_today<?=$member_work_all>0?"":" today_num_none"?>" id="ldr_today">
																	<button class="ldr_today_num" value="<?=$member_list_email?>">
																		<strong><?=number_format($member_work_all)?></strong>
																		<span>업무</span>
																		<input type="hidden" id="ldr_today_num" value="<?=number_format($member_work1)?>">
																	</button>
																</div>
																<div class="ldr_chall<?=$reward_cp_sum[$member_list_email]>0?"":" challenges_num_none"?>">
																	<button class="ldr_chall_num">
																		<strong><?=$reward_cp_sum[$member_list_email]>0?$reward_cp_sum[$member_list_email]:"0"?></strong>
																		<span>역량</span>
																	</button>
																</div>
																<div class="ldr_jjim<?=$like_list[$member_list_email]>0?"":" jjim_num_none"?>">
																	<button class="ldr_jjim_num" id="ldr_jjim_num_<?=$member_list_idx?>" value="<?=$member_list_idx?>">
																		<strong><?=$like_list[$member_list_email]?$like_list[$member_list_email]:"0"?></strong>
																		<span>좋아요</span>
																	</button>
																</div>

																<?//if($user_id=='sadary0@nate.com'){
																	echo $like_list[$member_list_email] ."&&". $like_list[$member_list_email] ."==". $reward_cp_info2[$k]['cnt'];

																	/*for($k=0; $k<count($reward_cp_info2); $k++){
																		$reward_cp_info[$k]['cp']['now']['memo'];
																		if($like_list[$member_list_email] && $like_list[$member_list_email] == $reward_cp_info2[$k]['cnt']){?>
																			<div class="ldr_jjim_pop">
																				<div class="ldr_jjim_pop_in" id="ldr_jjim_pop_in">
																					
																					<strong><?=$reward_cp_info2[$k]['memo']?></strong>
																					
																				</div>
																			</div>
																		<?
																		}
																	}*/

																	echo $like_list[$member_list_email]." && ". $like_list[$member_list_email] ." == ". $reward_cp_info[17]['like']['now']['cnt'];

																	if($like_list[$member_list_email] && $like_list[$member_list_email] == $reward_cp_info[17]['like']['now']['cnt']){?>
																		<div class="ldr_jjim_pop">
																			<div class="ldr_jjim_pop_in" id="ldr_jjim_pop_in">
																				<span>좋아요</span>
																				<strong><?=$reward_cp_info[17]['like']['now']['memo']?></strong>
																				<span>개 획득!</span>
																			</div>
																		</div>
																	<?
																	}

	
																	//전날 역량 1등!
																	if($reward_day_info_list[$member_list_email]){?>
																		<div class="ldr_jjim_pop">
																			<div class="ldr_jjim_pop_in" id="ldr_jjim_pop_in">
																				<strong><?=$reward_cp_info[16]['cp']['pre']['memo']?></strong>
																			</div>
																		</div>
																	<?}

																	//전날 좋아요1등!
																	if($like_day_info_list[$member_list_email]){?>
																		<div class="ldr_jjim_pop">
																			<div class="ldr_jjim_pop_in" id="ldr_jjim_pop_in">
																				<strong><?=$reward_cp_info[15]['like']['pre']['memo']?></strong>
																			</div>
																		</div>
																	<?}

																	//역량 100점획득!
																	if($reward_tot_info_list[$member_list_email] == $reward_cp_info[13]['cp']['now']['cnt'] ){?>
																		<div class="ldr_jjim_pop">
																			<div class="ldr_jjim_pop_in" id="ldr_jjim_pop_in">
																				<strong><?=$reward_cp_info['13']['cp']['now']['memo']?></strong>
																			</div>
																		</div>
																	<?}

																	?>
															</div>
														</div>
													</li>
												<?}?>

											</ul>
										</div>
									</div>


								</div>
							</div>
						</div>

					</div>
				</div>
				<!-- //콘텐츠 -->
			</div>
		</div>
	</div>

	

	<?	//좋아요 레이어
		include $home_dir . "/layer/member_jjim.php";
	?>


	<div class="jjim_graph" id="jjim_graph" style="display:none;">
		<div class="jg_deam"></div>
		<div class="jg_in">
			<div class="jg_box">
				<div class="jg_box_in">
					<div class="jg_close">
						<button><span>닫기</span></button>
					</div>
					<div class="jg_top">
						<strong>좋아요</strong>
						<input type="hidden" id="jg_userid">
					</div>
					<div class="jg_area">
						<div class="jg_user_area">
							<div class="jg_user_left">
								<div class="jg_user_left_in">
									<div class="jg_user_img">
										<div class="jg_user_img_in" id="jg_user_img" style="background-image:url(/html/images/pre/img_prof_02.png);"></div>
									</div>
									<div class="jg_name">
										<div class="jg_name_user" id="jg_name_user">윤지혜</div>
										<div class="jg_name_team" id="jg_name_team">디자인팀</div>
									</div>
								</div>
							</div>

							<div class="jg_user_right">
								<div class="jg_user_right_in">
									<div class="jg_user_heart_all" id="jg_user_heart_all">
										<span><?=$reward_cp_sum?></span>
									</div>
									<button class="jg_user_btn_coin" id="jg_user_btn_coin"><span>코인으로 보상하기</span></button>
								</div>
							</div>
						</div>

						<div class="jg_graph_area">
							<div class="jg_graph_area_in">
								<ul class="jg_graph_list" id="jg_graph_list">
									<li class="jg_g01">
										<span>12</span>
										<strong></strong>
									</li>
									<li class="jg_g02">
										<span>5</span>
										<strong></strong>
									</li>
									<li class="jg_g03">
										<span>4</span>
										<strong></strong>
									</li>
									<li class="jg_g04">
										<span>5</span>
										<strong></strong>
									</li>
									<li class="jg_g05">
										<span>7</span>
										<strong></strong>
									</li>
									<li class="jg_g06">
										<span>5</span>
										<strong></strong>
									</li>
								</ul>
							</div>
						</div>

						<div class="jg_icon_area">
							<div class="jg_icon_area_in">
								<ul class="jg_icon_list">
									<li class="jg_i01">
										<button value="1">
											<strong></strong>
											<span>인정</span>
											<em>상세보기</em>
										</button>
									</li>
									<li class="jg_i02">
										<button value="2">
											<strong></strong>
											<span>응원</span>
											<em>상세보기</em>
										</button>
									</li>
									<li class="jg_i03">
										<button value="3">
											<strong></strong>
											<span>칭찬</span>
											<em>상세보기</em>
										</button>
									</li>
									<li class="jg_i04">
										<button value="4">
											<strong></strong>
											<span>격려</span>
											<em>상세보기</em>
										</button>
									</li>
									<li class="jg_i05">
										<button value="5">
											<strong></strong>
											<span>축하</span>
											<em>상세보기</em>
										</button>
									</li>
									<li class="jg_i06">
										<button value="6">
											<strong></strong>
											<span>감사</span>
											<em>상세보기</em>
										</button>
									</li>
								</ul>
							</div>
						</div>
					</div>

				</div>
			</div>
		</div>
	</div>

	<div class="jjim_table" id="jjim_table" style="display:none;">
		<div class="jt_deam"></div>
		<div class="jt_in">
			<div class="jt_box">
				<div class="jt_box_in">
					<div class="jt_close">
						<button><span>닫기</span></button>
					</div>
					<div class="jt_top">
						<button class="jg_i03" id="jt_top_btn"><strong>잘해서 칭찬하기</strong></button>
					</div>
					<div class="jt_area">
						<div class="jt_user_area">
							<div class="jt_user_left">
								<div class="jt_user_left_in">
									<div class="jt_user_img">
										<div class="jt_user_img_in" id="jt_user_img" style="background-image:url(/html/images/pre/img_prof_02.png);"></div>
									</div>
									<div class="jt_name">
										<div class="jt_name_user" id="jt_name_user">윤지혜</div>
										<div class="jt_name_team" id="jt_name_team">디자인팀</div>
									</div>
								</div>
							</div>

							<div class="jt_user_right">
								<div class="jt_user_right_in">
									<div class="jt_user_heart_all" id="jt_user_heart_all">
										<span>4</span>
									</div>
									<button class="jt_user_btn_coin"><span>코인으로 보상하기</span></button>
								</div>
							</div>
						</div>

						<div class="jt_table_area" id="jt_table_area">
							<div class="jt_table_area_in">
								<div class="jt_table_head">
									<div class="jt_table_01"><span>카테고리</span></div>
									<div class="jt_table_02"><span>구분</span></div>
									<div class="jt_table_03"><span>내용</span></div>
									<div class="jt_table_04"><span>보낸이</span></div>
									<div class="jt_table_05"><span>일시</span></div>
								</div>
								<div class="jt_table_body">
									<ul>
										<li>
											<div class="jt_table_01"><span>라이브</span></div>
											<div class="jt_table_02"><span>칭찬하기</span></div>
											<div class="jt_table_03"><span>오늘업무 최다 작성</span></div>
											<div class="jt_table_04"><span>김광재</span></div>
											<div class="jt_table_05"><span>2022.03.30 15:23</span></div>
										</li>
										<li>
											<div class="jt_table_01"><span>라이브</span></div>
											<div class="jt_table_02"><span>칭찬하기</span></div>
											<div class="jt_table_03"><span>오늘업무 최다 작성</span></div>
											<div class="jt_table_04"><span>이선규</span></div>
											<div class="jt_table_05"><span>2022.03.30 15:23</span></div>
										</li>
										<li>
											<div class="jt_table_01"><span>라이브</span></div>
											<div class="jt_table_02"><span>칭찬하기</span></div>
											<div class="jt_table_03"><span>오늘업무 최다 작성</span></div>
											<div class="jt_table_04"><span>김명선</span></div>
											<div class="jt_table_05"><span>2022.03.30 15:23</span></div>
										</li>
										<li>
											<div class="jt_table_01"><span>라이브</span></div>
											<div class="jt_table_02"><span>칭찬하기</span></div>
											<div class="jt_table_03"><span>가장 일찍 출근</span></div>
											<div class="jt_table_04"><span>이선규</span></div>
											<div class="jt_table_05"><span>2022.03.30 15:23</span></div>
										</li>
										<li>
											<div class="jt_table_01"><span>라이브</span></div>
											<div class="jt_table_02"><span>칭찬하기</span></div>
											<div class="jt_table_03"><span>가장 일찍 출근</span></div>
											<div class="jt_table_04"><span>이선규</span></div>
											<div class="jt_table_05"><span>2022.03.30 15:23</span></div>
										</li>
										<li>
											<div class="jt_table_01"><span>라이브</span></div>
											<div class="jt_table_02"><span>칭찬하기</span></div>
											<div class="jt_table_03"><span>가장 일찍 출근</span></div>
											<div class="jt_table_04"><span>김명선</span></div>
											<div class="jt_table_05"><span>2022.03.30 15:23</span></div>
										</li>
									</ul>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>


	<!-- 드래그 -->
	<div class="layer_sort" id="layer_sort" style="display: none;">
		<div class="ls_deam"></div>
		<div class="ls_in">
			<div class="ls_box">
				<div class="ls_box_in">
					<div class="ls_close">
						<button><span>닫기</span></button>
					</div>
					<div class="ls_top">
						<strong>협업 모드</strong>
					</div>
					<div class="ls_area">
						<div class="ls_ul_01">
							<button class="ll_li ll_li_01">
								<div class="ll_li_in">
									<div class="ll_img">
										<div class="ll_img_user" style="background-image:url(/html/images/pre/img_prof_02.png);"></div>
									</div>
									<div class="ll_name">
										<div class="ll_name_team">디자인팀</div>
										<div class="ll_name_user">윤지혜</div>
									</div>
									<div class="ll_btn">
										
									</div>
								</div>
							</button>
							<button class="ll_li ll_li_02">
								<div class="ll_li_in">
									<div class="ll_img">
										<div class="ll_img_user" style="background-image:url(/html/images/pre/img_prof_02.png);"></div>
									</div>
									<div class="ll_name">
										<div class="ll_name_team">디자인팀</div>
										<div class="ll_name_user">정현주</div>
									</div>
									<div class="ll_btn">
										
									</div>
								</div>
							</button>
						</div>
						<div class="ls_ul_02">
							<button>업무요청(예정)</button>
							<button class="on">회의</button>
						</div>
					</div>
					<div class="ls_bottom">
						<button class="btn_off">확인</button>
					</div>
				</div>
			</div>
		</div>
	</div>

	<!-- 드래그 -->
	<div class="layer_plus" id="layer_plus" style="display:none;">
		<div class="lp_deam"></div>
		<div class="lp_in">
			<div class="lp_box">
				<div class="lp_box_in">
					<div class="lp_close">
						<button><span>닫기</span></button>
					</div>
					<div class="lp_top">
						<strong class="lp_tit">
							제목
						</strong>
					</div>
					<div class="lp_bottom">
						<button class="btn_on" id="plus_idx">참여하기</button>
					</div>
				</div>
			</div>
		</div>
	</div>


	<!-- 파티만들기 -->
	<div class="layer_make" id="layer_make" style="display:none;">
		<div class="lm_deam"></div>
		<div class="lm_in">
			<div class="lm_box">
				<div class="lm_box_in">
					<div class="lm_close">
						<button><span>닫기</span></button>
					</div>
					<div class="lm_top">
						<strong class="lm_tit">
							파티 만들기
						</strong>
					</div>
					<div class="lm_area">
						<div class="layer_user_slc_list">
							<div class="layer_user_slc_list_in" id="layer_user_slc_list_in">
								<ul>
								</ul>
							</div>
						</div>
						<div class="lm_ul_02">
							<input type="text" class="textarea_lm" placeholder="함께 할 업무를 작성해주세요." onkeyup="if(window.event.keyCode==13){ltb()}" id="textarea_lm" />
						</div>
					</div>
					<div class="lm_bottom">
						<button class="btn_off" id="lm_bottom">확인</button>
					</div>
				</div>
			</div>
		</div>
	</div>



	<!-- 보상하기 -->
	<div class="layer_reward" id="layer_reward" style="display:none;">
		<div class="lr_deam"></div>
		<div class="lr_in">
			<div class="lr_box">
				<div class="lr_box_in">
					<div class="lr_close" id="lr_close">
						<button><span>닫기</span></button>
					</div>
					<div class="lr_top">
						<strong>열심히 하는 동료에게 보상은 언제나 옳습니다!</strong>
					</div>
					<div class="lr_area">
						<ul>
							<?
							for($i=0; $i<count($coin_reward_info['idx']); $i++){
								$reward_info_idx = $coin_reward_info['idx'][$i];
								$reward_info_coin = $coin_reward_info['coin'][$i];
								$reward_info_icon = $coin_reward_info['icon'][$i];
								$reward_info_memo = $coin_reward_info['memo'][$i];
								$reward_info_icon = urldecode($reward_info_icon);
							?>
							<li>
								<button class="btn_lr_0<?=($i+1)?>" value="<?=$reward_info_idx?>">
									<div class="lr_txt">
										<span><?=$reward_info_icon?></span>
										<strong><?=$reward_info_memo?></strong>
									</div>
									<div class="lr_coin">
										<em><?=number_format($reward_info_coin)?></em>
									</div>
								</button>
							</li>
							<?}?>
						</ul>
					</div>
					<div class="lr_bottom">
						<input type="hidden" id="lr_idx"/>
						<input type="hidden" id="lr_val"/>
						<input type="text" class="lr_input" placeholder="보상할 코인을 입력하세요." id="lr_input"/>
						<button class="lr_btn" id="lr_btn"><span>보상하기</span></button>
						<p>(현재보유 공용코인 : <strong><?=number_format($common_coin)?></strong>코인)</p>
					</div>
				</div>
			</div>
		</div>
	</div>



	<div class="layer_team" id="layer_team" style="display:none;">
		<div class="lt_deam"></div>
		<div class="lt_in">
			<div class="lt_box">
				<div class="lt_box_in">
					<div class="lt_close">
						<button><span>닫기</span></button>
					</div>
					<div class="lt_top">
						<div class="lt_ul_01">
							<div class="ll_li">
								<div class="ll_li_in">
									<div class="ll_img">
										<div class="ll_img_user" style="background-image:url(/html/images/pre/img_prof_02.png);"></div>
									</div>
									<div class="ll_name">
										<div class="ll_name_user">윤지혜</div>
										<div class="ll_name_team">디자인팀</div>
										<input type="hidden" id="lt_id">
									</div>
								</div>
							</div>
						</div>
					</div>
					<div class="lt_area">
						<div class="lt_ul_02">
							<input type="text" class="textarea_lt" placeholder="내용을 작성해주세요." onkeyup="if(window.event.keyCode==13){ltb()}" id="team_input"/>
						</div>
					</div>
					<div class="lt_bottom">
						<button class="btn_on">확인</button>
					</div>
				</div>
			</div>
		</div>
	</div>

	<div class="layer_re_small" id="layer_re_small" style="display:none;">
		<div class="lrs_deam"></div>
		<div class="lrs_in">
			<div class="lrs_box">
				<div class="lrs_box_in">
					<div class="lrs_close">
						<button><span>닫기</span></button>
					</div>
					<div class="lrs_top">
						<strong>협업 모드</strong>
					</div>
					<div class="lrs_area">
						<div class="lrs_ul_01">
							<button class="ll_li">
								<div class="ll_li_in">
									<div class="ll_img">
										<div class="ll_img_user" style="background-image:url(/html/images/pre/img_prof_02.png);"></div>
									</div>
									<div class="ll_name">
										<div class="ll_name_team">디자인팀</div>
										<div class="ll_name_user">윤지혜</div>
									</div>
									<div class="ll_btn">
										
									</div>
								</div>
							</button>
						</div>
						<div class="lrs_ul_02">
							<button>업무요청(예정)</button>
							<button class="on">회의</button>
						</div>
					</div>
					<div class="lrs_bottom">
						<button class="btn_on">확인</button>
					</div>
				</div>
			</div>
		</div>
	</div>


	<div class="layer_report">
		<div class="layer_deam"></div>
		<div class="layer_result_in">
			<div class="layer_result_box">
				<div class="layer_result_left">
					<div class="layer_result_search">
						<div class="layer_result_search_box">
							<input type="text" class="input_search" placeholder="이름, 부서명을 검색"/>
							<button><span>검색</span></button>
						</div>
					</div>

					<div class="layer_result_user">
						<div class="layer_result_user_in">
							<ul>
								<li>
									<button class="on">
										<img src="/html/images/pre/ico_me.png" alt="" class="user_me" />
										<div class="user_img">
											<img src="/html/images/pre/ico_user_005.png" alt="" />
										</div>
										<div class="user_name">
											<strong>윤지혜</strong>
											디자인팀
										</div>
										<span class="user_num">
											<span>21</span>
										</span>
									</button>
								</li>

								<li>
									<button>
										<div class="user_img">
											<img src="/html/images/pre/ico_user_005.png" alt="" />
										</div>
										<div class="user_name">
											<strong>김광재</strong>
											콘텐츠팀
										</div>
										<span class="user_num">
											<span>2</span>
										</span>
									</button>
								</li>

								<li>
									<button>
										<div class="user_img">
											<img src="/html/images/pre/ico_user_005.png" alt="" />
										</div>
										<div class="user_name">
											<strong>김명선</strong>
											마케팅팀
										</div>
										<span class="user_num user_num_0">
											<span>0</span>
										</span>
									</button>
								</li>

								<li>
									<button>
										<div class="user_img">
											<img src="/html/images/pre/ico_user_005.png" alt="" />
										</div>
										<div class="user_name">
											<strong>김민경</strong>
											경영지원팀
										</div>
										<span class="user_num">
											<span>17</span>
										</span>
									</button>
								</li>

								<li>
									<button>
										<div class="user_img">
											<img src="/html/images/pre/ico_user_005.png" alt="" />
										</div>
										<div class="user_name">
											<strong>김상엽</strong>
											개발팀
										</div>
										<span class="user_num">
											<span>4</span>
										</span>
									</button>
								</li>

								<li>
									<button>
										<div class="user_img">
											<img src="/html/images/pre/ico_user_005.png" alt="" />
										</div>
										<div class="user_name">
											<strong>박정헌</strong>
											마케팅팀
										</div>
										<span class="user_num">
											<span>9</span>
										</span>
									</button>
								</li>

								<li>
									<button>
										<div class="user_img">
											<img src="/html/images/pre/ico_user_005.png" alt="" />
										</div>
										<div class="user_name">
											<strong>박희정</strong>
											콘텐츠팀
										</div>
										<span class="user_num">
											<span>12</span>
										</span>
									</button>
								</li>

								<li>
									<button>
										<div class="user_img">
											<img src="/html/images/pre/ico_user_005.png" alt="" />
										</div>
										<div class="user_name">
											<strong>서민정</strong>
											고객행복팀
										</div>
										<span class="user_num">
											<span>20</span>
										</span>
									</button>
								</li>

								<li>
									<button>
										<div class="user_img">
											<img src="/html/images/pre/ico_user_005.png" alt="" />
										</div>
										<div class="user_name">
											<strong>성지훈</strong>
											마케팅팀
										</div>
										<span class="user_num">
											<span>2</span>
										</span>
									</button>
								</li>

							</ul>
						</div>
					</div>
				</div>

				<div class="layer_result_right">
					<div class="layer_close">
						<button><span>닫기</span></button>
					</div>
					<div class="layer_result_top">
						<strong>역량평가 리포트</strong>
					</div>
					<div class="layer_result_list">
						<div class="layer_result_list_in">
							<div class="list_function">
								<div class="list_function_in">
									<div class="list_function_left">
										윤지혜 <span>디자인팀</span><strong>[2021년 10월]</strong>
									</div>
									<div class="list_function_right">
										<div class="list_function_sort">
											<div class="list_function_sort_in">
												<button class="btn_sort_on"><span>2021년 10월</span></button>
												<ul>
													<li><button><span>2021년 10월</span></button></li>
													<li><button><span>2021년 9월</span></button></li>
													<li><button><span>2021-09</span></button></li>
													<li><button><span>2021.09</span></button></li>
												</ul>
											</div>
										</div>
									</div>
								</div>
							</div>
							<div class="report_area">
								<div class="report_area_in">
									
									<div class="report_left">
										<div id="radarChart2"></div>
									</div>
									<div class="report_right">
										<div class="report_now">
											<dl>
												<dt>
													<strong>2021년 10월 20일</strong>
													<span>달성율은 일 단위로 산정합니다.</span>
												</dt>
												<dd>
													<strong>노하우</strong>
													<span>100%</span>
													<em>😀</em>
												</dd>
												<dd class="report_now_low">
													<strong>자기계발</strong>
													<span>0%</span>
													<em>😔</em>
												</dd>
												<dd>
													<strong>협업능력</strong>
													<span>100%</span>
													<em>😀</em>
												</dd>
												<dd>
													<strong>성실</strong>
													<span>100%</span>
													<em>😀</em>
												</dd>
												<dd class="report_now_low">
													<strong>적극성</strong>
													<span>0%</span>
													<em>😔</em>
												</dd>
											</dl>
										</div>
									</div>

									<div class="report_cha">
										<!-- <div class="report_cha_banner">추천 챌린지로 능력치를 올려보세요!</div> -->
										<div class="report_cha_title">📢 추천 챌린지 <span>추천 챌린지로 능력치를 올려보세요!</span></div>
										<ul class="rew_cha_list_ul">
											<li class="category_01">
												<a href="#">
													<div class="cha_box">
														<div class="cha_box_t">
															<span class="cha_cate">업무</span>
															<span class="cha_title">윈도우 업데이트 점검한다면</span>
															<span class="cha_coin"><strong>500</strong>코인</span>
														</div>
														<div class="cha_box_b">
															<span class="cha_member">12/20 명 도전중</span>
															<span class="cha_dday">D - 20</span>
														</div>
													</div>
												</a>
											</li>
											<li class="category_02">
												<a href="#">
													<div class="cha_box">
														<div class="cha_box_t">
															<span class="cha_cate">생활</span>
															<span class="cha_title">책 읽고 독서메모를 남긴다면</span>
															<span class="cha_coin"><strong>1,500</strong>코인</span>
														</div>
														<div class="cha_box_b">
															<span class="cha_member">7/20 명 도전중</span>
															<span class="cha_dday">D - 10</span>
														</div>
													</div>
												</a>
											</li>
											<li class="category_05">
												<a href="#">
													<div class="cha_box">
														<div class="cha_box_t">
															<span class="cha_cate">신입사원</span>
															<span class="cha_title">보고서 작성법을 배우면</span>
															<span class="cha_coin"><strong>1,000</strong>코인</span>
														</div>
														<div class="cha_box_b">
															<span class="cha_member">1/1 명 도전중</span>
															<span class="cha_dday">D - 30</span>
														</div>
													</div>
												</a>
											</li>
											<li class="category_02">
												<a href="#">
													<div class="cha_box">
														<div class="cha_box_t">
															<span class="cha_cate">생활</span>
															<span class="cha_title">캔크러시 챌린지, 그저 밟기만 했을 뿐인데</span>
															<span class="cha_coin"><strong>500</strong>코인</span>
														</div>
														<div class="cha_box_b">
															<span class="cha_member">12/20 명 도전중</span>
															<span class="cha_dday">D - 60</span>
														</div>
													</div>
												</a>
											</li>
										</ul>
									</div>
								</div>
							</div>
						</div>
					</div>

				</div>

			</div>
		</div>
	</div>

	<div class="layer_challenge" style="display:none;">
		<div class="layer_deam"></div>
		<div class="layer_result_in">
			<div class="layer_result_box">
				<div class="layer_result_left">
					<div class="layer_result_search">
						<div class="layer_result_search_box">
							<input type="text" class="input_search" placeholder="이름, 부서명을 검색" />
							<button><span>검색</span></button>
						</div>
					</div>

					<div class="layer_result_user">
						<div class="layer_result_user_in">
							<ul>
								<li>
									<button class="on">
										<img src="/html/images/pre/ico_me.png" alt="" class="user_me" />
										<div class="user_img">
											<img src="/html/images/pre/ico_user_005.png" alt="" />
										</div>
										<div class="user_name">
											<strong>윤지혜</strong>
											디자인팀
										</div>
										<span class="user_num">
											<span>21</span>
										</span>
									</button>
								</li>

								<li>
									<button>
										<div class="user_img">
											<img src="/html/images/pre/ico_user_005.png" alt="" />
										</div>
										<div class="user_name">
											<strong>김광재</strong>
											콘텐츠팀
										</div>
										<span class="user_num">
											<span>2</span>
										</span>
									</button>
								</li>

								<li>
									<button>
										<div class="user_img">
											<img src="/html/images/pre/ico_user_005.png" alt="" />
										</div>
										<div class="user_name">
											<strong>김명선</strong>
											마케팅팀
										</div>
										<span class="user_num user_num_0">
											<span>0</span>
										</span>
									</button>
								</li>

								<li>
									<button>
										<div class="user_img">
											<img src="/html/images/pre/ico_user_005.png" alt="" />
										</div>
										<div class="user_name">
											<strong>김민경</strong>
											경영지원팀
										</div>
										<span class="user_num">
											<span>17</span>
										</span>
									</button>
								</li>

								<li>
									<button>
										<div class="user_img">
											<img src="/html/images/pre/ico_user_005.png" alt="" />
										</div>
										<div class="user_name">
											<strong>김상엽</strong>
											개발팀
										</div>
										<span class="user_num">
											<span>4</span>
										</span>
									</button>
								</li>

								<li>
									<button>
										<div class="user_img">
											<img src="/html/images/pre/ico_user_005.png" alt="" />
										</div>
										<div class="user_name">
											<strong>박정헌</strong>
											마케팅팀
										</div>
										<span class="user_num">
											<span>9</span>
										</span>
									</button>
								</li>

								<li>
									<button>
										<div class="user_img">
											<img src="/html/images/pre/ico_user_005.png" alt="" />
										</div>
										<div class="user_name">
											<strong>박희정</strong>
											콘텐츠팀
										</div>
										<span class="user_num">
											<span>12</span>
										</span>
									</button>
								</li>

								<li>
									<button>
										<div class="user_img">
											<img src="/html/images/pre/ico_user_005.png" alt="" />
										</div>
										<div class="user_name">
											<strong>서민정</strong>
											고객행복팀
										</div>
										<span class="user_num">
											<span>20</span>
										</span>
									</button>
								</li>

								<li>
									<button>
										<div class="user_img">
											<img src="/html/images/pre/ico_user_005.png" alt="" />
										</div>
										<div class="user_name">
											<strong>성지훈</strong>
											마케팅팀
										</div>
										<span class="user_num">
											<span>2</span>
										</span>
									</button>
								</li>

							</ul>
						</div>
					</div>
				</div>

				<div class="layer_result_right">
					<div class="layer_close">
						<button><span>닫기</span></button>
					</div>
					<div class="layer_result_top">
						<strong>참여 완료한 챌린지</strong>
					</div>
					<div class="layer_result_list">
						<div class="layer_result_list_in">
							<div class="list_function">
								<div class="list_function_in">
									<div class="list_function_left">
										윤지혜 <span>디자인팀</span><strong>8</strong>
									</div>
								</div>
							</div>
							<div class="report_area">
								<div class="report_area_in">
									<div class="report_cha">
										<ul class="rew_cha_list_ul">
											<li class="category_01">
												<a href="#">
													<div class="cha_box">
														<div class="cha_box_t">
															<span class="cha_cate">업무</span>
															<span class="cha_title">윈도우 업데이트 점검한다면</span>
															<span class="cha_coin"><strong>500</strong>코인</span>
														</div>
														<div class="cha_box_b">
															<span class="cha_member">12/20 명 도전중</span>
															<span class="cha_dday">D - 20</span>
														</div>
													</div>
												</a>
											</li>
											<li class="category_02">
												<a href="#">
													<div class="cha_box">
														<div class="cha_box_t">
															<span class="cha_cate">생활</span>
															<span class="cha_title">책 읽고 독서메모를 남긴다면</span>
															<span class="cha_coin"><strong>1,500</strong>코인</span>
														</div>
														<div class="cha_box_b">
															<span class="cha_member">7/20 명 도전중</span>
															<span class="cha_dday">D - 10</span>
														</div>
													</div>
												</a>
											</li>
											<li class="category_05">
												<a href="#">
													<div class="cha_box">
														<div class="cha_box_t">
															<span class="cha_cate">신입사원</span>
															<span class="cha_title">보고서 작성법을 배우면</span>
															<span class="cha_coin"><strong>1,000</strong>코인</span>
														</div>
														<div class="cha_box_b">
															<span class="cha_member">1/1 명 도전중</span>
															<span class="cha_dday">D - 30</span>
														</div>
													</div>
												</a>
											</li>
											<li class="category_02">
												<a href="#">
													<div class="cha_box">
														<div class="cha_box_t">
															<span class="cha_cate">생활</span>
															<span class="cha_title">캔크러시 챌린지, 그저 밟기만 했을 뿐인데</span>
															<span class="cha_coin"><strong>500</strong>코인</span>
														</div>
														<div class="cha_box_b">
															<span class="cha_member">12/20 명 도전중</span>
															<span class="cha_dday">D - 60</span>
														</div>
													</div>
												</a>
											</li>
											<li class="category_05">
												<a href="#">
													<div class="cha_box">
														<div class="cha_box_t">
															<span class="cha_cate">신입사원</span>
															<span class="cha_title">보고서 작성법을 배우면</span>
															<span class="cha_coin"><strong>1,000</strong>코인</span>
														</div>
														<div class="cha_box_b">
															<span class="cha_member">1/1 명 도전중</span>
															<span class="cha_dday">D - 30</span>
														</div>
													</div>
												</a>
											</li>
											<li class="category_02">
												<a href="#">
													<div class="cha_box">
														<div class="cha_box_t">
															<span class="cha_cate">생활</span>
															<span class="cha_title">캔크러시 챌린지, 그저 밟기만 했을 뿐인데</span>
															<span class="cha_coin"><strong>500</strong>코인</span>
														</div>
														<div class="cha_box_b">
															<span class="cha_member">12/20 명 도전중</span>
															<span class="cha_dday">D - 60</span>
														</div>
													</div>
												</a>
											</li>
											<li class="category_05">
												<a href="#">
													<div class="cha_box">
														<div class="cha_box_t">
															<span class="cha_cate">신입사원</span>
															<span class="cha_title">보고서 작성법을 배우면</span>
															<span class="cha_coin"><strong>1,000</strong>코인</span>
														</div>
														<div class="cha_box_b">
															<span class="cha_member">1/1 명 도전중</span>
															<span class="cha_dday">D - 30</span>
														</div>
													</div>
												</a>
											</li>
											<li class="category_02">
												<a href="#">
													<div class="cha_box">
														<div class="cha_box_t">
															<span class="cha_cate">생활</span>
															<span class="cha_title">캔크러시 챌린지, 그저 밟기만 했을 뿐인데</span>
															<span class="cha_coin"><strong>500</strong>코인</span>
														</div>
														<div class="cha_box_b">
															<span class="cha_member">12/20 명 도전중</span>
															<span class="cha_dday">D - 60</span>
														</div>
													</div>
												</a>
											</li>
										</ul>
									</div>
								</div>
							</div>
						</div>
					</div>

				</div>

			</div>
		</div>
	</div>

	<div class="layer_today">
		<div class="layer_deam"></div>
		<div class="layer_result_in">
			<div class="layer_result_box">
				<div class="layer_result_left">
					<div class="layer_result_search">
						<div class="layer_result_search_box">
							<input type="text" class="input_search" placeholder="이름, 부서명을 검색" id="input_user_search"/>
							<button id="lives_search_bt"><span>검색</span></button>
						</div>
					</div>

					<div class="layer_result_user">
						<div class="layer_result_user_in">
							<ul>
								<li>
									<button class="on">
										<img src="/html/images/pre/ico_me.png" alt="" class="user_me" />
										<div class="user_img">
											<img src="/html/images/pre/ico_user_005.png" alt="" />
										</div>
										<div class="user_name">
											<strong>윤지혜</strong>
											디자인팀
										</div>
										<span class="user_num">
											<span>21</span>
										</span>
									</button>
								</li>

								<li>
									<button>
										<div class="user_img">
											<img src="/html/images/pre/ico_user_005.png" alt="" />
										</div>
										<div class="user_name">
											<strong>김광재</strong>
											콘텐츠팀
										</div>
										<span class="user_num">
											<span>2</span>
										</span>
									</button>
								</li>

								<li>
									<button>
										<div class="user_img">
											<img src="/html/images/pre/ico_user_005.png" alt="" />
										</div>
										<div class="user_name">
											<strong>김명선</strong>
											마케팅팀
										</div>
										<span class="user_num user_num_0">
											<span>0</span>
										</span>
									</button>
								</li>

								<li>
									<button>
										<div class="user_img">
											<img src="/html/images/pre/ico_user_005.png" alt="" />
										</div>
										<div class="user_name">
											<strong>김민경</strong>
											경영지원팀
										</div>
										<span class="user_num">
											<span>17</span>
										</span>
									</button>
								</li>

								<li>
									<button>
										<div class="user_img">
											<img src="/html/images/pre/ico_user_005.png" alt="" />
										</div>
										<div class="user_name">
											<strong>김상엽</strong>
											개발팀
										</div>
										<span class="user_num">
											<span>4</span>
										</span>
									</button>
								</li>

								<li>
									<button>
										<div class="user_img">
											<img src="/html/images/pre/ico_user_005.png" alt="" />
										</div>
										<div class="user_name">
											<strong>박정헌</strong>
											마케팅팀
										</div>
										<span class="user_num">
											<span>9</span>
										</span>
									</button>
								</li>

								<li>
									<button>
										<div class="user_img">
											<img src="/html/images/pre/ico_user_005.png" alt="" />
										</div>
										<div class="user_name">
											<strong>박희정</strong>
											콘텐츠팀
										</div>
										<span class="user_num">
											<span>12</span>
										</span>
									</button>
								</li>

								<li>
									<button>
										<div class="user_img">
											<img src="/html/images/pre/ico_user_005.png" alt="" />
										</div>
										<div class="user_name">
											<strong>서민정</strong>
											고객행복팀
										</div>
										<span class="user_num">
											<span>20</span>
										</span>
									</button>
								</li>

								<li>
									<button>
										<div class="user_img">
											<img src="/html/images/pre/ico_user_005.png" alt="" />
										</div>
										<div class="user_name">
											<strong>성지훈</strong>
											마케팅팀
										</div>
										<span class="user_num">
											<span>2</span>
										</span>
									</button>
								</li>

							</ul>
						</div>
					</div>
				</div>

				<div class="layer_result_right">
					<div class="layer_close">
						<button><span>닫기</span></button>
					</div>
					<div class="layer_result_top">
						<div class="layer_result_tab">
							<ul>
								<li><button class="btn_lr_01 on" ><span>오늘업무</span></button></li>
								<li><button class="btn_lr_02"><span>챌린지</span></button></li>
								<!-- <li><button class="btn_lr_03"><span>역량평가</span></button></li> -->
								<li><button class="btn_lr_04"><span>페널티</span></button></li>
							</ul>
						</div>
					</div>
					<div class="layer_result_list desc_lr_01">
						<div class="layer_result_list_in" id="todaywork_zone_list">
							<div class="list_function">
								<div class="list_function_in">
									<div class="list_function_left">
										윤지혜 <span>디자인팀</span><strong>4</strong>
									</div>
									<div class="list_function_right">
										<div class="list_function_calendar">
										<button class="calendar_prev" id="prev_wdate"><span>이전</span></button>
										<input type="text" class="calendar_num" value="<?=$wdate?>" id="lives_date" readonly="readonly"/>
											<button class="calendar_next" id="next_wdate"><span>다음</span></button>
										</div>
									</div>
								</div>
							</div>
							<div class="report_area">
								<div class="report_area_in">
									<div class="report_cha">
										
										<div class="tdw_penalty_banner">
											<div class="tdw_pb_in">
												<img src="/html/images/pre/img_penalty.png" alt="" />
												<p><span>[긴급]</span>지각 페널티 카드가 발동했습니다.</p>
												<button class="btn_penalty_banner" style="display:none;"><span>미션 수행하기</span></button>
												<strong class="penalty_comp"><span>미션 완료</span></strong><!-- 미션 완료 -->
											</div>
										</div>

										<div class="tdw_list">
											<div class="tdw_list_in">
												<ul class="tdw_list_ul">
													<li class="tdw_list_li">
														<div class="tdw_list_box">
															<div class="tdw_list_chk">
																<button class="btn_tdw_list_chk"><span>완료체크</span></button>
															</div>
															<div class="tdw_list_desc">
																<p><span>[15:00]</span>엑셀 뷰페이지 추천서식 영역 디자인 교체</p>
															</div>
															<div class="tdw_list_func">
																<button class="tdw_list_memo" id="tdw_list_memo" value="<?=$idx?>"><span>메모</span></button>
																<!-- <button class="tdw_list_memo"><span>메모</span></button> -->
															</div>
														</div>

														<div class="tdw_list_memo_area">
															<div class="tdw_list_memo_area_in" id="memo_area_list_8360"></div>
														</div>
													</li>

													<li class="tdw_list_li">
														<div class="tdw_list_box">
															<div class="tdw_list_chk">
																<button class="btn_tdw_list_chk"><span>완료체크</span></button>
															</div>
															<div class="tdw_list_desc">
																<p>비즈폼 리워드 뷰페이지 시안 확인 및 수정사항 체크</p>
															</div>
														</div>
													</li>

													<li class="tdw_list_li">
														<div class="tdw_list_box">
															<div class="tdw_list_chk">
																<button class="btn_tdw_list_chk"><span>완료체크</span></button>
															</div>
															<div class="tdw_list_desc">
																<p><span>[2021-01-01 14:00 반차]</span>오후 반차</p>
															</div>
														</div>
													</li>

													<li class="tdw_list_li">
														<div class="tdw_list_box">
															<div class="tdw_list_chk">
																<button class="btn_tdw_list_chk"><span>완료체크</span></button>
															</div>
															<div class="tdw_list_desc">
																<p><span>[최순영 → 김상엽 업무요청]</span>월결제 관련 통계 자료 추출</p>
															</div>
														</div>
													</li>

													<li class="tdw_list_li">
														<div class="tdw_list_box">
															<div class="tdw_list_chk">
																<button class="btn_tdw_list_chk"><span>완료체크</span></button>
															</div>
															<div class="tdw_list_desc">
																<p><span>[2021-01-01 14:00 반차]</span>오후 반차</p>
															</div>
														</div>
													</li>

													<li class="tdw_list_li">
														<div class="tdw_list_box">
															<div class="tdw_list_chk">
																<button class="btn_tdw_list_chk"><span>완료체크</span></button>
															</div>
															<div class="tdw_list_desc">
																<p><span>[최순영 → 김상엽 업무요청]</span>월결제 관련 통계 자료 추출</p>
															</div>
														</div>
													</li>

													<li class="tdw_list_li">
														<div class="tdw_list_box">
															<div class="tdw_list_chk">
																<button class="btn_tdw_list_chk"><span>완료체크</span></button>
															</div>
															<div class="tdw_list_desc">
																<p><span>[2021-01-01 14:00 반차]</span>오후 반차</p>
															</div>
														</div>
													</li>

													<li class="tdw_list_li">
														<div class="tdw_list_box">
															<div class="tdw_list_chk">
																<button class="btn_tdw_list_chk"><span>완료체크</span></button>
															</div>
															<div class="tdw_list_desc">
																<p><span>[최순영 → 김상엽 업무요청]</span>월결제 관련 통계 자료 추출</p>
															</div>
														</div>
													</li>

													<li class="tdw_list_li">
														<div class="tdw_list_box">
															<div class="tdw_list_chk">
																<button class="btn_tdw_list_chk"><span>완료체크</span></button>
															</div>
															<div class="tdw_list_desc">
																<p><span>[2021-01-01 14:00 반차]</span>오후 반차</p>
															</div>
														</div>
													</li>

													<li class="tdw_list_li">
														<div class="tdw_list_box">
															<div class="tdw_list_chk">
																<button class="btn_tdw_list_chk"><span>완료체크</span></button>
															</div>
															<div class="tdw_list_desc">
																<p><span>[최순영 → 김상엽 업무요청]</span>월결제 관련 통계 자료 추출</p>
															</div>
														</div>
													</li>
												</ul>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>

						<div class="layer_result_btns">
							<div class="layer_result_btns_in">
								<div class="btns_right">
									<button class="btns_write" onclick="location.href='/todaywork/index.php'"><span>작성하러 가기</span></button>
								</div>
							</div>
						</div>
					</div>

					<div class="layer_result_list desc_lr_02">
						<div class="layer_result_list_in">
							<div class="list_function">
								<div class="list_function_in">
									<div class="list_function_left">
										윤지혜 <span>디자인팀</span><strong>8</strong>
									</div>
								</div>
							</div>
							<div class="report_area">
								<div class="report_area_in">
									<div class="report_cha">
										<ul class="rew_cha_list_ul">
											<li class="category_01">
												<a href="#">
													<div class="cha_box">
														<div class="cha_box_t">
															<span class="cha_cate">업무</span>
															<span class="cha_title">윈도우 업데이트 점검한다면</span>
															<span class="cha_coin"><strong>500</strong>코인</span>
														</div>
														<div class="cha_box_b">
															<span class="cha_member">12/20 명 도전중</span>
															<span class="cha_dday">D - 20</span>
														</div>
													</div>
												</a>
											</li>
											<li class="category_02">
												<a href="#">
													<div class="cha_box">
														<div class="cha_box_t">
															<span class="cha_cate">생활</span>
															<span class="cha_title">책 읽고 독서메모를 남긴다면</span>
															<span class="cha_coin"><strong>1,500</strong>코인</span>
														</div>
														<div class="cha_box_b">
															<span class="cha_member">7/20 명 도전중</span>
															<span class="cha_dday">D - 10</span>
														</div>
													</div>
												</a>
											</li>
											<li class="category_05">
												<a href="#">
													<div class="cha_box">
														<div class="cha_box_t">
															<span class="cha_cate">신입사원</span>
															<span class="cha_title">보고서 작성법을 배우면</span>
															<span class="cha_coin"><strong>1,000</strong>코인</span>
														</div>
														<div class="cha_box_b">
															<span class="cha_member">1/1 명 도전중</span>
															<span class="cha_dday">D - 30</span>
														</div>
													</div>
												</a>
											</li>
											<li class="category_02">
												<a href="#">
													<div class="cha_box">
														<div class="cha_box_t">
															<span class="cha_cate">생활</span>
															<span class="cha_title">캔크러시 챌린지, 그저 밟기만 했을 뿐인데</span>
															<span class="cha_coin"><strong>500</strong>코인</span>
														</div>
														<div class="cha_box_b">
															<span class="cha_member">12/20 명 도전중</span>
															<span class="cha_dday">D - 60</span>
														</div>
													</div>
												</a>
											</li>
											<li class="category_05">
												<a href="#">
													<div class="cha_box">
														<div class="cha_box_t">
															<span class="cha_cate">신입사원</span>
															<span class="cha_title">보고서 작성법을 배우면</span>
															<span class="cha_coin"><strong>1,000</strong>코인</span>
														</div>
														<div class="cha_box_b">
															<span class="cha_member">1/1 명 도전중</span>
															<span class="cha_dday">D - 30</span>
														</div>
													</div>
												</a>
											</li>
											<li class="category_02">
												<a href="#">
													<div class="cha_box">
														<div class="cha_box_t">
															<span class="cha_cate">생활</span>
															<span class="cha_title">캔크러시 챌린지, 그저 밟기만 했을 뿐인데</span>
															<span class="cha_coin"><strong>500</strong>코인</span>
														</div>
														<div class="cha_box_b">
															<span class="cha_member">12/20 명 도전중</span>
															<span class="cha_dday">D - 60</span>
														</div>
													</div>
												</a>
											</li>
											<li class="category_05">
												<a href="#">
													<div class="cha_box">
														<div class="cha_box_t">
															<span class="cha_cate">신입사원</span>
															<span class="cha_title">보고서 작성법을 배우면</span>
															<span class="cha_coin"><strong>1,000</strong>코인</span>
														</div>
														<div class="cha_box_b">
															<span class="cha_member">1/1 명 도전중</span>
															<span class="cha_dday">D - 30</span>
														</div>
													</div>
												</a>
											</li>
											<li class="category_02">
												<a href="#">
													<div class="cha_box">
														<div class="cha_box_t">
															<span class="cha_cate">생활</span>
															<span class="cha_title">캔크러시 챌린지, 그저 밟기만 했을 뿐인데</span>
															<span class="cha_coin"><strong>500</strong>코인</span>
														</div>
														<div class="cha_box_b">
															<span class="cha_member">12/20 명 도전중</span>
															<span class="cha_dday">D - 60</span>
														</div>
													</div>
												</a>
											</li>
										</ul>
									</div>
								</div>
							</div>
						</div>
					</div>

					<div class="layer_result_list desc_lr_03">
						<div class="layer_result_list_in">
							<div class="list_function">
								<div class="list_function_in">
									<div class="list_function_left">
										윤지혜 <span>디자인팀</span><strong>[2021년 10월]</strong>
									</div>
									<div class="list_function_right">
										<div class="list_function_sort">
											<div class="list_function_sort_in">
												<button class="btn_sort_on"><span>2021년 10월</span></button>
												<ul>
													<li><button><span>2021년 10월</span></button></li>
													<li><button><span>2021년 9월</span></button></li>
													<li><button><span>2021-09</span></button></li>
													<li><button><span>2021.09</span></button></li>
												</ul>
											</div>
										</div>
									</div>
								</div>
							</div>
							<div class="report_area">
								<div class="report_area_in">
									
									<div class="report_left">
										<div id="radarChart"></div>
									</div>
									<div class="report_right">
										<div class="report_now">
											<dl>
												<dt>
													<strong>2021년 10월 20일</strong>
													<span>달성율은 일 단위로 산정합니다.</span>
												</dt>
												<dd>
													<strong>노하우</strong>
													<span>100%</span>
													<em>😀</em>
												</dd>
												<dd class="report_now_low">
													<strong>자기계발</strong>
													<span>0%</span>
													<em>😔</em>
												</dd>
												<dd>
													<strong>협업능력</strong>
													<span>100%</span>
													<em>😀</em>
												</dd>
												<dd>
													<strong>성실</strong>
													<span>100%</span>
													<em>😀</em>
												</dd>
												<dd class="report_now_low">
													<strong>적극성</strong>
													<span>0%</span>
													<em>😔</em>
												</dd>
											</dl>
										</div>
									</div>

									<div class="report_cha">
										<!-- <div class="report_cha_banner">추천 챌린지로 능력치를 올려보세요!</div> -->
										<div class="report_cha_title">📢 추천 챌린지 <span>추천 챌린지로 능력치를 올려보세요!</span></div>
										<ul class="rew_cha_list_ul">
											<li class="category_01">
												<a href="#">
													<div class="cha_box">
														<div class="cha_box_t">
															<span class="cha_cate">업무</span>
															<span class="cha_title">윈도우 업데이트 점검한다면</span>
															<span class="cha_coin"><strong>500</strong>코인</span>
														</div>
														<div class="cha_box_b">
															<span class="cha_member">12/20 명 도전중</span>
															<span class="cha_dday">D - 20</span>
														</div>
													</div>
												</a>
											</li>
											<li class="category_02">
												<a href="#">
													<div class="cha_box">
														<div class="cha_box_t">
															<span class="cha_cate">생활</span>
															<span class="cha_title">책 읽고 독서메모를 남긴다면</span>
															<span class="cha_coin"><strong>1,500</strong>코인</span>
														</div>
														<div class="cha_box_b">
															<span class="cha_member">7/20 명 도전중</span>
															<span class="cha_dday">D - 10</span>
														</div>
													</div>
												</a>
											</li>
											<li class="category_05">
												<a href="#">
													<div class="cha_box">
														<div class="cha_box_t">
															<span class="cha_cate">신입사원</span>
															<span class="cha_title">보고서 작성법을 배우면</span>
															<span class="cha_coin"><strong>1,000</strong>코인</span>
														</div>
														<div class="cha_box_b">
															<span class="cha_member">1/1 명 도전중</span>
															<span class="cha_dday">D - 30</span>
														</div>
													</div>
												</a>
											</li>
											<li class="category_02">
												<a href="#">
													<div class="cha_box">
														<div class="cha_box_t">
															<span class="cha_cate">생활</span>
															<span class="cha_title">캔크러시 챌린지, 그저 밟기만 했을 뿐인데</span>
															<span class="cha_coin"><strong>500</strong>코인</span>
														</div>
														<div class="cha_box_b">
															<span class="cha_member">12/20 명 도전중</span>
															<span class="cha_dday">D - 60</span>
														</div>
													</div>
												</a>
											</li>
										</ul>
									</div>
								</div>
							</div>
						</div>
					</div>



				</div>

			</div>

			<div class="layer_memo" style="display:none;">
				<input type="hidden" id="work_idx">
				<div class="layer_deam"></div>
				<div class="layer_memo_in">
					<div class="layer_memo_box">
						<textarea name="" class="textarea_memo" placeholder="메모를 작성해주세요." id="textarea_memo"></textarea>
					</div>
					<div class="layer_memo_btn">
						<button class="layer_memo_cancel" id="layer_memo_cancel"><span>취소</span></button>
						<button class="layer_memo_submit" id="layer_memo_submit"><span>등록하기</span></button>
					</div>
				</div>
			</div>

		</div>
	</div>


	

	

	<div class="t_layer rew_layer_join" style="display:none;">
		<div class="tl_deam"></div>
		<div class="tl_in">
			<div class="tl_close">
				<button><span>닫기</span></button>
			</div>
			<div class="tl_login_logo">
				<span>리워디</span>
			</div>
			<div class="tl_tit">
				<strong>가입하기</strong>
				<span>리워디에서 인증을요청합니다. <br />
				리워디와 함께 하세요!</span>
			</div>
			<div class="tl_list">
				<ul>
					<li>
						<div class="tc_input">
							<input type="text" id="z5" name="user_id" class="input_001" placeholder="이메일" />
							<label for="z5" class="label_001">
								<strong class="label_tit">이메일을 입력하세요</strong>
							</label>
						</div>
					</li>
				</ul>
			</div>
			<div class="tl_btn">
				<button><span>인증메일 발송</span></button>
			</div>
			<div class="tl_descript">
				<p>리워디에서 인증을 요청합니다.<br />
				아래 링크를 클릭하셔서, 비밀번호를 설정해 주세요.<br />
				링크가 클릭되지 않으시면 아래 주소를 복사하여 인터넷 브라우저에 붙여<br />
				넣어주세요.<br />
				<br />
				https://rewardy.co.kr/<br />
				<br />
				기타 문의사항은 1588-8443으로 문의해 주세요.<br />
				리워디와 함께 해주셔서 감사합니다.
				</p>
			</div>
		</div>
	</div>

	<div class="t_layer rew_layer_repass" style="display:none;">
		<div class="tl_deam"></div>
		<div class="tl_in">
			<div class="tl_close">
				<button><span>닫기</span></button>
			</div>
			<div class="tl_login_logo">
				<span>리워디</span>
			</div>
			<div class="tl_tit">
				<strong>비밀번호 재설정</strong>
				<span>비밀번호를 초기화할 수 있는 링크를 보내드립니다.<br />
				리워디에 가입한 이메일 주소를 입력해 주세요.</span>
			</div>
			<div class="tl_list">
				<ul>
					<li>
						<div class="tc_input">
							<input type="text" id="z3" name="user_id" class="input_001" placeholder="이메일" />
							<label for="z3" class="label_001">
								<strong class="label_tit">이메일을 입력하세요</strong>
							</label>
						</div>
					</li>
				</ul>
			</div>
			<div class="tl_btn">
				<button><span>비밀번호 재설정 메일 보내기</span></button>
			</div>
			<div class="tl_back">
				<button><span>이전으로</span></button>
			</div>
		</div>
	</div>


	<div class="t_layer rew_layer_setting" style="display:none;">
		<div class="tl_deam"></div>
		<div class="tl_in">
			<div class="tl_close">
				<button><span>닫기</span></button>
			</div>
			<div class="tl_login_logo">
				<span>리워디</span>
			</div>
			<div class="tl_prof">
				<div class="tl_prof_box">
					<div class="tl_prof_img">
					</div>
					<div class="tl_prof_slc">
						<div class="tl_prof_slc_in">
							<button class="button_prof"><span>프로필 변경</span></button>
							<ul>
								<li>
									<input type="file" id="prof" class="input_prof" />
									<label for="prof" class="label_prof"><span>사진 변경</span></label>
								</li>
								<li><button id="btn_slc_character"><span>캐릭터 선택</span></button></li>
								<li><button class="default_on"><span>기본 이미지로 변경</span></button></li>
							</ul>
						</div>
					</div>
				</div>
			</div>

			<div class="tl_list">
				<ul>
					<li>
						<div class="tc_input">
							<input type="text" id="z7" name="user_id" class="input_002" disabled value="young@bizforms.co.kr" />
							<label for="z7" class="label_001">
								<strong class="label_tit">이메일</strong>
							</label>
						</div>
					</li>
					<li>
						<div class="tc_input tc_50">
							<input type="text" id="z8" name="user_name" class="input_002" disabled placeholder="윤지혜" />
							<label for="z8" class="label_001">
								<strong class="label_tit">이름을 입력하세요</strong>
							</label>
						</div>
						<div class="tc_input tc_50">
							<input type="text" id="z9" name="user_name" class="input_002" disabled placeholder="디자인팀" />
							<label for="z9" class="label_001">
								<strong class="label_tit">이름을 입력하세요</strong>
							</label>
						</div>
					</li>
					<li>
						<div class="tc_input">
							<input type="password" id="z10" name="user_pwd" class="input_001" placeholder="비밀번호" />
							<label for="z10" class="label_001">
								<strong class="label_tit">비밀번호를 입력하세요</strong>
							</label>
						</div>
					</li>
					<li>
						<div class="tc_input">
							<input type="password" id="z11" name="user_repwd" class="input_001" placeholder="비밀번호 재확인" />
							<label for="z11" class="label_001">
								<strong class="label_tit">비밀번호를 확인하세요</strong>
							</label>
						</div>
					</li>
				</ul>
			</div>
			
			<div class="tl_btn">
				<button><span>가입하기</span></button>
			</div>
		</div>
	</div>

	<div class="t_layer rew_layer_character" style="display:none;">
		<div class="tl_deam"></div>
		<div class="tl_in">
			<div class="tl_close">
				<button><span>닫기</span></button>
			</div>
			<div class="tl_tit">
				<strong>캐릭터 설정</strong>
				<span>리워디에서 기본으로 제공하는 <br />캐릭터입니다.</span>
			</div>
			<div class="tl_profile">
				<ul>
					<li>
						<div class="tl_profile_box">
							<div class="tl_profile_img" style="background-image:url(/html/images/pre/img_prof_01.png);">
								<button class="btn_profile" id="profile_img_01"><span>기본 프로필 이미지1 선택</span></button>
							</div>
						</div>
					</li>
					<li>
						<div class="tl_profile_box">
							<div class="tl_profile_img" style="background-image:url(/html/images/pre/img_prof_02.png);">
								<button class="btn_profile" id="profile_img_02"><span>기본 프로필 이미지2 선택</span></button>
							</div>
						</div>
					</li>
					<li>
						<div class="tl_profile_box">
							<div class="tl_profile_img" style="background-image:url(/html/images/pre/img_prof_03.png);">
								<button class="btn_profile" id="profile_img_03"><span>기본 프로필 이미지3 선택</span></button>
							</div>
						</div>
					</li>
					<li>
						<div class="tl_profile_box">
							<div class="tl_profile_img" style="background-image:url(/html/images/pre/img_prof_04.png);">
								<button class="btn_profile" id="profile_img_04"><span>기본 프로필 이미지4 선택</span></button>
							</div>
						</div>
					</li>
				</ul>
			</div>
			<div class="tl_btn">
				<button><span>적용</span></button>
			</div>
		</div>
	</div>


	<div class="radar_layer" id="radar_layer" style="display:none;">
		<div class="rl_deam"></div>
		<div class="rl_in">
			<div class="rl_box">
				<div class="rl_box_in">
					<div class="rl_close">
						<button><span>닫기</span></button>
					</div>
					<div class="rl_top">
						<strong>역량</strong>
					</div>
					<div class="rl_area">
						<div class="rl_user_area">
							<div class="rl_user_left">
								<div class="rl_user_left_in">
									<div class="rl_user_img">
										<div class="rl_user_img_in" style="background-image:url(/html/images/pre/img_prof_02.png);"></div>
									</div>
									<div class="rl_name">
										<div class="rl_name_user">윤지혜</div>
										<div class="rl_name_team">디자인팀</div>
									</div>
								</div>
							</div>

							<div class="rl_user_right">
								<div class="rl_user_right_in">
									<div class="rl_user_heart_all">
										<span>44</span>
									</div>
									<button class="rl_user_btn_coin"><span>코인으로 보상하기</span></button>
								</div>
							</div>
						</div>

						<div class="rl_conts_area">
							<div class="rl_conts_area_in">
								<div class="rl_right">
									<div class="rew_mains_info_r">
										<div class="rew_mains_chart_graph">
											<div id="rl_radarChart"></div>
											<div class="radar_grade radar_01">
												<span class="radar_tit">지식</span>
												<em class="grade_s">S</em>
												<span class="radar_pt">(6.8)</span>
											</div>
											<div class="radar_grade radar_02">
												<span class="radar_tit">성장</span>
												<em class="grade_s">S</em>
												<span class="radar_pt">(5.6)</span>
											</div>
											<div class="radar_grade radar_03">
												<span class="radar_tit">성실</span>
												<em class="grade_b">B</em>
												<span class="radar_pt">(0.5)</span>
											</div>
											<div class="radar_grade radar_04">
												<span class="radar_tit">실행</span>
												<em class="grade_b">B</em>
												<span class="radar_pt">(1.4)</span>
											</div>
											<div class="radar_grade radar_05">
												<span class="radar_tit">협업</span>
												<em class="grade_s">S</em>
												<span class="radar_pt">(3.3)</span>
											</div>
											<div class="radar_grade radar_06">
												<span class="radar_tit">성과</span>
												<em class="grade_a">A</em>
												<span class="radar_pt">(7.2)</span>
											</div>
											<div class="radar_total">
												<span>3</span>
											</div>
										</div>

									</div>
								</div>

								<div class="rl_left">
									<div class="rl_tab">
										<ul>
											<li><button class="btn_rl_01 on" value="1"><span>지식</span></button></li>
											<li><button class="btn_rl_02" value="3"><span>성장</span></button></li>
											<li><button class="btn_rl_03" value="5"><span>성실</span></button></li>
											<li><button class="btn_rl_04" value="6"><span>실행</span></button></li>
											<li><button class="btn_rl_05" value="4"><span>협업</span></button></li>
											<li><button class="btn_rl_06" value="2"><span>성과</span></button></li>
										</ul>
									</div>
									<div class="rl_desc">
										<dl>
											<dt>주요지표 : 신기술, 전략기획, 노하우</dt>
											<dd>성과를 칭찬하기 영역의 ♥ 좋아요를 받거나, <br />성과 챌린지 완료 시 지표가 상승합니다.</dd>
										</dl>
									</div>
									<div class="rl_grade">
										<span class="rl_grade_title">지식</span>
										<em class="rl_grade_score">(6.8)</em>
										<strong class="rl_grade_rank">E</strong>
										<span class="rl_grade_txt">등급</span>
									</div>
								</div>
							</div>
						</div>

					</div>

				</div>
			</div>
		</div>
	</div>

	<!-- <div class="rew_qck">
		<button class="btn_open_join"><span>회원가입</span></button>
		<button class="btn_open_login"><span>로그인</span></button>
		<button class="btn_open_repass"><span>비밀번호 재설정</span></button>
		<button class="btn_open_setting"><span>프로필 변경</span></button>
	</div> -->


	<div class="layer_user" style="display:none;">
		<div class="layer_deam"></div>
		<div class="layer_user_in">
			<div class="layer_user_box none" id="layer_test_01">
				<div class="layer_user_search">
					<div class="layer_user_search_desc">
						<strong>참여자 설정</strong>
						<span id="usercnt">전체 <?=$member_total_cnt?>명</span>
					</div>
					<div class="layer_user_search_box">
						<input type="text" class="input_search" placeholder="이름, 부서명을 검색" id="input_todaywork_search"/>
						<button id="input_todaywork_search_btn"><span>검색</span></button>
					</div>
				</div>

				<div class="layer_user_slc_list">
					<div class="layer_user_slc_list_in">
						<ul>

						</ul>
					</div>
				</div>

				<div class="layer_user_info">
					<ul>
						<?
						//회원정보내역으로 회원이름 = [name][부서번호][순번]
						for($i=0; $i<count($member_info['idx']); $i++){
							$member_idx = $member_info['idx'][$i];
							$member_uid = $member_info['email'][$i];
							$member_name = $member_info['name'][$i];
							$partno 	= $member_info['partno'][$i];

							$mem_uid[$partno][$member_idx] = $member_uid;
							$mem_name[$partno][$member_idx] = $member_name;
							$mem_idx[$partno][$member_idx] = $member_info['idx'][$i];
						}

						//회원정보 키값 초기화처리
						for($i=0; $i<count($member_info['idx']); $i++){

							$member_idx = $member_info['idx'][$i];
							$member_name = $member_info['name'][$i];
							$member_email = $member_info['email'][$i];
							$partno 	= $member_info['partno'][$i];

							$j = 0;
							foreach($mem_name[$partno] as $key=>$val)
							{
								unset($mem_name[$partno][$key]);
								$new_key = $j;  
								$mem_name[$partno][$new_key] = $val;
								$j++;
							}

							$j = 0;
							foreach($mem_idx[$partno] as $key=>$val)
							{
								unset($mem_idx[$partno][$key]);
								$new_key = $j;  
								$mem_idx[$partno][$new_key] = $val;
								$j++;
							}

							$j = 0;
							foreach($mem_uid[$partno] as $key=>$val)
							{
								unset($mem_uid[$partno][$key]);
								$new_key = $j;  
								$mem_uid[$partno][$new_key] = $val;
								$j++;
							}

						}

						for($i=0; $i<count($part_info['partno']); $i++){
							$partno = $part_info['partno'][$i];
							$part_cnt = count($mem_name[$partno]);
							?>
							<li>
								<dl class="on">
									<dt>
										<button class="btn_team_slc" id="btn_team_slc_<?=($partno)?>"><span><?=$part_info['part'][$i]?> <?=$part_cnt?></span></button>
										<button class="btn_team_toggle" id="btn_team_toggle"><span>열고닫기</span></button>
									</dt>

									<?for($j=0; $j<$part_cnt; $j++){
										//프로필 케릭터 사진
										$profile_img_src = profile_img_info($mem_uid[$partno][$j]);
									?>
										<dd id="udd_<?=$mem_idx[$partno][$j]?>">
											<button value="<?=$mem_idx[$partno][$j]?>" id="team_<?=$partno?>">
												<?=($user_id == $mem_uid[$partno][$j]?"<img src=\"/html/images/pre/ico_me.png\" alt=\"\" class=\"user_me\" />":"");?>
											<div class="user_img" style="background-image:url('<?=$profile_img_src?>');"></div>
											<div class="user_name">
												<strong><?=$mem_name[$partno][$j]?></strong>
												<span><?=$part_info['part'][$i]?></span>
											</div>
										</button>
										</dd>
									<?}?>
								</dl>
							</li>
						<?}?>
					</ul>
				</div>
			</div>

			<div class="layer_user_btn">
				<!-- <div class="layer_test">
					<span>임시 버튼(테스트용)</span>
					<button class="layer_test_01">레이어초기화</button>
					<button class="layer_test_02">검색결과O</button>
					<button class="layer_test_03">검색결과X</button>
				</div> -->
				<button class="layer_user_all_slc" id="layer_user_all_slc"><span>전체선택</span></button>
				<button class="layer_user_cancel"><span>취소</span></button>
				<button class="layer_user_submit" id="layer_todaywork_user"><span>설정하기</span></button>
			</div>
		</div>
	</div>


	<!-- Step 1) Load D3.js -->
	<script src="https://d3js.org/d3.v6.min.js"></script>
	<!-- Step 2) Load billboard.js with style -->
	<script src="/js/billboard.js"></script>
	<!-- Load with base style -->
	<link rel="stylesheet" href="/html/css/billboard.css">
	<script>
		var chart = bb.generate({
			data: {
				x: "x",
				columns: [
				["x", "노하우", "자기계발", "협업능력", "성실", "적극성", "셀프"],
				["역량평가 리포트", 75, 95, 80, 65, 90, 50]
				//["역량평가 리포트 9월", 80, 50, 50, 90, 70, 60]
				],
				color: "#aaa",
				type: "radar",
				labels: false,
				colors: {
				"역량평가 리포트": "#ffaf04"
				// "역량평가 리포트 9월": "#c5c5c5"
				}
			},
			size: {
				//width: 400,
				height: 300
			},
			radar: {
				axis: {
					max: 100
				},
				level: {
					depth: 4
				},
				direction: {
					clockwise: true
				}
			},
			tooltip: {
				show: false
			},
			point: {
				show: true
			},
			transition: {
			   duration: 500
			},
			bindto: "#radarChart"
		});

	</script>

	<script>
		//setTimeout(function(){
		function rl_chart_run(){
			var rl_chart = bb.generate({
				data: {
					x: "x",
					columns: [
					["x", "지식", "성장", "성실", "실행", "협업", "성과"],
					["역량평가 리포트", 75, 95, 80, 65, 90, 50]
					],
					color: "#aaa",
					type: "radar",
					labels: false,
					colors: {
					"역량평가 리포트": "#38c9d2"
					}
				},
				size: {
					//width: 400,
					height: 256
				},
				radar: {
					axis: {
						max: 100
					},
					level: {
						depth: 4
					},
					direction: {
						clockwise: true
					}
				},
				tooltip: {
					show: false
				},
				point: {
					show: true
				},
				transition: {
				duration: 500
				},
				bindto: "#rl_radarChart"
			});
		}
		//	},100);
	</script>

	<script type="text/javascript">
		$(document).ready(function(){
			
			$(".lt_close").click(function(){
				$("#layer_team").hide();
			});
			$(".ls_close").click(function(){
				$("#layer_sort").hide();
			});
			$(".lrs_close").click(function(){
				$("#layer_re_small").hide();
			});

			$(".ls_ul_01 .ll_li").click(function(){
				$(this).siblings().removeClass("on");
				$(this).addClass("on");
				$(".ls_bottom button").removeClass("btn_off").addClass("btn_on");
			});

			$(".gf_cate button").click(function(){
				$(this).siblings().removeClass("on");
				$(this).addClass("on");
			});

			$(".ls_bottom button").click(function(){
				if($(this).hasClass("btn_on")){
					var ls_user = $(".ls_ul_01 .ll_li.on").find(".ll_name_user").text();
					$("#layer_sort").hide();
					$("#gridDemo .ll_li").each(function(){
						var gdli = $(this);
						var gdlit = gdli.find(".ll_name_user").text();
						var met = $("#gridDemo .ll_me").find(".ll_name_user").text();
						if(gdlit == ls_user){
							$("#gridDemo .ll_li").removeClass("ll_on");
							gdli.addClass("ll_on");
							$("#gridDemo .ll_me").addClass("ll_on");
							$("#gridDemo .ll_li").find(".ll_meeting_list strong").remove();
							gdli.find(".ll_meeting_list").append("<strong>"+met+"</strong>");
							$("#gridDemo .ll_me").find(".ll_meeting_list").append("<strong>"+gdlit+"</strong>");
						}
					});

				}
			});

			

			$(".lrs_bottom button").click(function(){
				if($(this).hasClass("btn_on")){
					var lt_user = $(".lrs_ul_01 .lls_li .lls_name_user").text();
					$("#layer_re_small").hide();

					$("#gridSmall .lls_li").removeClass("lls_on");
					$("#gridSmall .lls_li").find(".lls_meeting_list em").remove();
					$("#gridSmall .lls_li").find(".lls_meeting_list strong").remove();

					var gdlit0 = $("#gridSmall .lls_li.on").find(".lls_name_user").text();
					var gdlit00 = $("#gridSmall .lls_li.on").find(".lls_name_team").text();
					var met0 = $("#gridSmall .lls_me").find(".lls_name_user").text();
					var met00 = $("#gridSmall .lls_me").find(".lls_name_team").text();

					$("#gridSmall .lls_li.on .lls_meeting").addClass("on");
					$("#gridSmall .lls_li.on .lls_meeting_list").append("<em>"+met00+"</em>");
					$("#gridSmall .lls_li.on .lls_meeting_list").append("<strong>"+met0+"</strong>");
					$("#gridSmall .lls_me .lls_meeting").addClass("on");
					$("#gridSmall .lls_me .lls_meeting_list").append("<em>"+gdlit00+"</em>");
					$("#gridSmall .lls_me .lls_meeting_list").append("<strong>"+gdlit0+"</strong>");

					$("#gridSmall .lls_li.on").addClass("lls_on").removeClass("on");
					$("#gridSmall .lls_me").addClass("lls_on")

				}
			});

			$(".btn_open_join").click(function(){
				$(".rew_layer_join").show();
			});
			$(".btn_open_login").click(function(){
				$(".rew_layer_login").show();
			});
			$(".btn_open_repass").click(function(){
				$(".rew_layer_repass").show();
			});
			$(".btn_open_setting").click(function(){
				$(".rew_layer_setting").show();
			});
			$(".tl_close button").click(function(){
				$(this).closest(".t_layer").hide();
			});

			$(".button_prof").click(function(){
				$(".tl_prof_slc ul").show();
			});
			$("#btn_slc_character").click(function(){
				$(".rew_layer_character").show();
			});
			$(".rew_layer_character .tl_btn").click(function(){
				$(".rew_layer_character").hide();
			});
			$(".btn_profile").click(function(){
				$(".btn_profile").removeClass("on");
				$(this).addClass("on");
			});
			$(".tl_prof_slc").mouseleave(function(){
				$(".tl_prof_slc ul").hide();
			});
		});
	</script>
	
</div>


	<!-- footer start-->
	<? include $home_dir . "/inc_lude/footer.php";?>
	<!-- footer end-->
</body>
</html>