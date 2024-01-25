<?php

$home_dir = str_replace( basename(__DIR__) , "" , __DIR__ );
//연결된 도메인으로 분리
include $home_dir . "inc_lude/conf_mysqli.php";
include $home_dir . "inc/SHA256/KISA_SHA256.php";
include DBCON_MYSQLI;
include FUNC_MYSQLI;

$mode = $_POST["mode"];					//mode값 전달받음
$type_flag = ($chkMobile)?1:0;				//구분(0:사이트, 1:모바일)

if($_COOKIE){
	$user_id = $_COOKIE['user_id'];
	$user_name = $_COOKIE['user_name'];
	$user_level = $_COOKIE['user_level'];
	$user_part = $_COOKIE['user_part'];
}


//프로젝트 리스트
if($mode == "project_list"){

	$chk_tab0 = $_POST['chk_tab0'];
	$chk_tab1 = $_POST['chk_tab1'];
	$chk_tab2 = $_POST['chk_tab2'];
	$page_delay = $_POST['page_delay'];
	$page_sort = $_POST['page_sort'];
	$user_my = $_POST['user_my'];
	$search = $_POST['search'];
	$gp = $_POST['gp'];

	if(!$gp) {
		$gp = 1;
	}

	if($page_sort){
		if($page_sort == '1'){
			$sort = "order by a.editdate desc";
		}else if($page_sort == '2'){
			$sort = "order by work desc";
		}else if($page_sort == '3'){
			$sort = "order by work asc";
		}else if($page_sort == '4'){
			$sort = "order by a.com_coin_pro desc";
		}
	}


	$pagesize = 12;						//페이지 출력갯수
	$startnum = 0;						//페이지 시작번호
	$endnum = $gp * $pagesize;			//페이지 끝번

	//시작번호
	if ($gp == 1){
		$startnum = 0;
	}else{
		$startnum = ($gp - 1) * $pagesize;
	}

	

	$whereis = "";

	//지연일수(1:1일이내, 3:3일간 이내, 7:7일간 업데이트 없음)
	if($page_delay){

		$whereis = " a.state='0' and a.companyno='".$companyno."'";
		switch($page_delay){
		
			//수정일자가 없으면 등록일로 부터 1일 이내(원활)
			case "1": 
				
				$where = " and datediff(".DBDATE.", a.editdate)<='".$party_delay['1']."'";
				$where =  $where .= " and a.state = '0'";
				break;

			//보통(1일 ~ 6일)
			case "3": 
				$where = " and datediff(".DBDATE.", a.editdate)>='".$party_delay['2']."'";
				$where = $where .= " and datediff(".DBDATE.", a.editdate)<='".$party_delay['3']."'";
				$where =  $where .= " and a.state = '0'";
				break;
			
			//지연(7일이상)
			case "7": 
				$where = " and (datediff(".DBDATE.", a.editdate)>='".$party_delay['4']."' or datediff(now(), a.editdate) < 0)";
				$where =  $where .= " and a.state = '0'";
				break;

			// 종료
			case "end": 
				$where = " and a.state = '1'";
				break;
		}
	}else{
		$whereis = " a.state!='9' and a.companyno='".$companyno."'";
	}

	if($search){
		$where_search = " and a.title like '%".$search."%'";
	}

	//원활:7일, 보통:8~ 3일, 지연:14일
	//$party_delay값은 /inc_lude/conf_mysqli.php 설정되었음
	//파티 좌측 메뉴 내파티 클릭시 조회
	// alert($chk_tab2);
	if($chk_tab2 == '2' || $user_my){
		$sql = "select count(1) as cnt from work_todaywork_project as a left join work_todaywork_project_user AS b ON (a.idx = b.project_idx)";
		$sql .= "where a.state!='9' and b.state = '0' and a.companyno='".$companyno."' and b.email = '".$user_id."'";
		$sql .= $where.$where_search;
	}else{
		$sql = "select count(1) as cnt from work_todaywork_project as a ";
		$sql .= "where a.state!='9' and a.companyno='".$companyno."'";
		$sql .= $where.$where_search;
	}
	$project_row = selectQuery($sql);
	if($project_row){
		$total_count = $project_row['cnt'];
	}

	//페이징 갯수
	
	if ( ($total_count % $pagesize) > 0 ){
		$page_count = floor($total_count/$pagesize)+1;
	}else{
		$page_count = floor($total_count/$pagesize);
	}

	if( $user_my  && $chk_tab0!='all' ){

		$sql = "select a.idx, a.state, a.title, a.email, a.com_coin_pro, date_format(a.regdate, '%Y-%m-%d %H:%i') as sdate, date_format(a.editdate, '%Y-%m-%d %H:%i') as udate,";
		$sql .= " date_format(a.enddate, '%Y-%m-%d %H:%i') as edate, case when a.editdate is null then datediff(now(), a.regdate) when a.editdate is not null then datediff(a.editdate, a.regdate) end as reg,";
		$sql .= " (select count(p.idx) from work_todaywork_project_info as p, work_todaywork as t where p.work_idx = t.idx and p.party_idx=a.idx and p.state='0' and t.state != '9') as work from work_todaywork_project as a left join work_todaywork_project_user as b on(a.idx=project_idx)";
		$sql .= " where a.state in ('0','1') and a.companyno='".$companyno."' and b.state = '0' and b.email='".$user_id."'";
		$sql .= $where.$where_search;
		if($sort){
		$sql .= $sort;
		}else{
		$sql .= " order by";
		$sql .= " CASE WHEN a.state='0' THEN a.idx END desc,";
		$sql .= " CASE WHEN a.state='1' THEN a.idx END ASC";
		}
		$sql .= " limit ".$startnum.", ".$pagesize;
		$project_info = selectAllQuery($sql);

	}else if($page_sort == '1' || $page_sort == '2' || $page_sort == '3' || $page_sort == '4'){
		$sql = "select a.idx, a.state, a.title, a.email, com_coin_pro, date_format(a.regdate, '%Y-%m-%d %H:%i') as sdate, date_format(a.editdate, '%Y-%m-%d %H:%i') as udate, ";
		$sql = $sql .= "date_format(a.enddate, '%Y-%m-%d %H:%i') as edate, case when a.editdate is null then datediff(now(), a.regdate) when a.editdate is not null then datediff(a.editdate , a.regdate) end as reg, ";
		$sql = $sql .= "(select count(p.idx) from work_todaywork_project_info as p, work_todaywork as t where p.work_idx = t.idx and p.party_idx=a.idx and p.state='0' and t.state != '9') as work, b.state as bstate ";
		$sql = $sql .= "from work_todaywork_project as a left join ";
		$sql = $sql .= "(select state, project_idx from work_project_like where state = 1 and email = '".$user_id."' and companyno='".$companyno."') ";
		$sql = $sql .= "as b on (a.idx = b.project_idx) where a.state!='9' and a.companyno='".$companyno."' ";
		$sql = $sql .= $where.$where_search;
		$sql = $sql .= $sort;
		$sql .= " limit ".$startnum.", ".$pagesize;

			$project_info = selectAllQuery($sql);
		
	}else{

		if($chk_tab0 || $chk_tab1 || $chk_tab2){

			if($chk_tab0=='all' && !$page_sort){
				//전체파티
				$sql = "select a.idx, a.state, a.title, a.email, com_coin_pro, date_format(a.regdate, '%Y-%m-%d %H:%i') as sdate, date_format(a.editdate, '%Y-%m-%d %H:%i') as udate, ";
				$sql = $sql .= "date_format(a.enddate, '%Y-%m-%d %H:%i') as edate, case when a.editdate is null then datediff(now(), a.regdate) when a.editdate is not null then datediff(a.editdate , a.regdate) end as reg, ";
				$sql = $sql .= "(select count(p.idx) from work_todaywork_project_info as p, work_todaywork as t where p.work_idx = t.idx and p.party_idx=a.idx and p.state='0' and t.state != '9') as work, b.state as bstate ";
				$sql = $sql .= "from work_todaywork_project as a left join ";
				$sql = $sql .= "(select state, project_idx from work_project_like where state = 1 and email = '".$user_id."' and companyno='".$companyno."') ";
				$sql = $sql .= "as b on (a.idx = b.project_idx) where a.state!='9' and a.companyno='".$companyno."' ";
				$sql = $sql .= $where.$where_search;
				$sql = $sql .= "order by a.state asc, b.state desc,CASE WHEN a.state='0' THEN a.idx END desc, CASE WHEN a.state='1' THEN enddate END ASC";
				$sql .= " limit ".$startnum.", ".$pagesize;
			}
			$project_info = selectAllQuery($sql);

		}
	}
	

	// 업무 건수
	$project_work_cnt = array_combine($project_info['idx'], $project_info['work']);

	
	//전체 프로젝트 내역
	$sql = "select a.idx, a.project_idx, a.name, a.email, a.part, b.profile_type, b.profile_img_idx, c.file_path, c.file_name
	from work_todaywork_project_user a
    left join work_member b on a.email = b.email
    left join work_member_profile_img c on a.email = c.email
    where 1=1
	and a.state!='9' 
    and a.companyno='".$companyno."'
    and b.state != '9'";
	// $sql = $sql .= " group by a.project_idx, a.email";
	$sql = $sql .= " order by a.idx asc";
	//echo $sql;
	$project_user_info = selectAllQuery($sql);
	for($i=0; $i<count($project_user_info['idx']); $i++){
		$project_user_idx = $project_user_info['project_idx'][$i];
		$project_user_email = $project_user_info['email'][$i];
		$project_user_name = $project_user_info['name'][$i];
		$project_user_part = $project_user_info['part'][$i];
		$project_user_profile_type = $project_user_info['profile_type'][$i];
		$project_user_profile_img_idx = $project_user_info['profile_img_idx'][$i];
		$project_user_file_path = $project_user_info['file_path'][$i];
		$project_user_file_name = $project_user_info['file_name'][$i];
		$project_user_list[$project_user_idx]['email'][] = $project_user_email;
		$project_user_list[$project_user_idx]['name'][] = $project_user_name;
		$project_user_list[$project_user_idx]['part'][] = $project_user_part;
		$project_user_list[$project_user_idx]['profile_type'][] = $project_user_profile_type;
		$project_user_list[$project_user_idx]['profile_img_idx'][] = $project_user_profile_img_idx;
		$project_user_list[$project_user_idx]['file_path'][] = $project_user_file_path;
		$project_user_list[$project_user_idx]['file_name'][] = $project_user_file_name;

		$profile_img =  'http://demo.rewardy.co.kr'.$project_user_file_path.$project_user_file_name;
		$project_use[$project_user_idx][] = $project_user_email;
	}

	?>

	<?if($project_info['idx']){?>
		<?for($i=0; $i<count($project_info['idx']); $i++){
			$project_idx = $project_info['idx'][$i];
			$project_state = $project_info['state'][$i];
			$project_title = $project_info['title'][$i];
			$project_sdate = $project_info['sdate'][$i];
			$project_udate = $project_info['udate'][$i];
			$project_edate = $project_info['edate'][$i];
			$com_coin_pro = $project_info['com_coin_pro'][$i];
			$project_bstate = $project_info['bstate'][$i];


			if($project_udate){
				$project_udate = substr($project_udate,0, 10);
			}else{
				$project_udate = "";
			}

			$project_sdate = substr($project_sdate,0, 10);
			$project_edate = substr($project_edate,0, 10);
		
			//완료된 파티
			if($project_state == '1'){
				$project_state_in = " cha_dend";
				$project_state_text = "종료";
				$project_date = $project_sdate . "~" . $project_edate;
			}else{

				//날짜차이체크
				$project_now = new DateTime( date("Y-m-d H:i", time()) );
				$project_start = new DateTime($project_sdate); // 20120101 같은 포맷도 잘됨
				

				if($project_udate){
					$project_update = new DateTime($project_udate);
					$project_diff = date_diff($project_update, $project_now);
					$project_diff_day = $project_diff->days; // 284



					//날짜차이가 1,3,7
					if($project_udate && $project_diff_day>=0 && $project_diff_day<=$party_delay['1']){
						$project_state_text = "원활";
						$project_li_class = "list_delay_1";
					}else if($project_udate && $project_diff_day>=$party_delay['2'] && $project_diff_day<=$party_delay['3']){
						$project_state_text = "보통";
						$project_li_class = "list_delay_3";
					}else if($project_udate && $project_diff_day>=$party_delay['4'] || !$project_update){
						$project_li_class = "list_delay_7";
						$project_state_text = "지연";
					}else{
						$project_li_class = "list_delay_7";
						$project_state_text = "지연";
					}

				}else{
					$project_n_diff = date_diff($project_start, $project_now);
					$project_n_diff_day = $project_n_diff->days;

					//날짜차이가 1,3,7
					if($project_sdate && $project_n_diff_day>=0 && $project_n_diff_day<=$party_delay['1']){
						$project_state_text = "원활";
						$project_li_class = "list_delay_1";
					}else if($project_sdate && $project_n_diff_day>=$party_delay['2'] && $project_n_diff_day<=$party_delay['3']){
						$project_state_text = "보통";
						$project_li_class = "list_delay_3";
					}else if($project_sdate && $project_n_diff_day>=$party_delay['4'] || !$project_update){
						$project_li_class = "list_delay_7";
						$project_state_text = "지연";
					}else{
						$project_li_class = "list_delay_7";
						$project_state_text = "지연";
					}

				}

				$project_date = $project_sdate ."~";


				$project_state_in = "";
			}

			$pro_like_cli = "";

			if($project_bstate == 1){
				$pro_like_cli = "on";
			}

		?>
			
			<li class="<?=$project_li_class?><?=$project_state_in?>" value="<?=$project_idx?>">
				<button class="cha_fav <?=$pro_like_cli?>" id="cha_fav_<?=$project_idx?>" onclick="pro_like(<?=$project_idx?>);"><span>즐겨찾기</span></button>
				<a href="#null" onclick="javascript:void(0);">
				<div class="cha_box">
				<div class="cha_delay">
						<span><?=$project_state_text?></span>
					</div>
				<span class="cha_tit"><?=$project_title?></span>
				<div class="cha_date">
					<span><?=$project_date?></span>
				</div>
				<div class="cha_bar">
					<div class="cha_num">
						<span>업무 <strong><?=$project_work_cnt[$project_idx]?></strong></span>
					</div>
					<div class="cha_coin_all">
						<span><?=number_format($com_coin_pro);?></span>
					</div>
				</div>
					<div class="cha_user_box">
						<?for($j=0; $j<count($project_user_list[$project_idx]['email']); $j++){
							$user_cnt = 0;

							$project_user_list_email = $project_user_list[$project_idx]['email'][$j];
							$project_user_list_profile_type = $project_user_list[$project_idx]['profile_type'][$j];
							$project_user_list_profile_img_idx = $project_user_list[$project_idx]['profile_img_idx'][$j];
							$project_user_list_file_path = $project_user_list[$project_idx]['file_path'][$j];
							$project_user_list_file_name = $project_user_list[$project_idx]['file_name'][$j];

							$profile_img =  'http://demo.rewardy.co.kr'.$project_user_list_file_path.$project_user_list_file_name;

							if($project_state==0 && $user_id==$project_user_list_email){
								$li_class = ' cha_user_me';
							}else{
								$li_class = '';
							}

							if($j>6){
								$user_more_cnt = count($project_user_list[$project_idx]['email'])-6;
								$user_more ="<div class=\"cha_user_more\">+".$user_more_cnt."</div>";
								$user_cnt = 1;
							}
						?>
							<?if($j<6){?>
								<div class="cha_user_img<?=$li_class?>" style="background-image:url('<?=$project_user_list_profile_type >= '0'?$profile_img:"/html/images/pre/img_prof_default.png"?>');"></div>
							<?}?>
						<?}?>
						<?if($user_cnt == 1){?>
							<?=$user_more?>
						<?}?>
					</div>
					<div class="cha_user_list" style = "display:none;">
						<?for($j=0; $j<count($project_user_list[$project_idx]['email']); $j++){
																$user_cnt = 0;

							$project_user_list_email = $project_user_list[$project_idx]['email'][$j];
							$project_user_list_profile_type = $project_user_list[$project_idx]['profile_type'][$j];
							$project_user_list_profile_img_idx = $project_user_list[$project_idx]['profile_img_idx'][$j];
							$project_user_list_file_path = $project_user_list[$project_idx]['file_path'][$j];
							$project_user_list_file_name = $project_user_list[$project_idx]['file_name'][$j];

							$profile_img =  'http://demo.rewardy.co.kr'.$project_user_list_file_path.$project_user_list_file_name;

							if($project_state==0 && $user_id==$project_user_list_email){
								$li_class = ' cha_user_me';
							}else{
								$li_class = '';
							}

							if($j>0){
								$user_more_cnt = count($project_user_list[$project_idx]['email'])-1;
								$user_more ="<div class=\"cha_user_more\">+".$user_more_cnt."</div>";
								$user_cnt = 1;
							}
						?>
							<?if($j<1){?>
								<div class="cha_user_img<?=$li_class?>" style="background-image:url('<?=$project_user_list_profile_type >= '0'?$profile_img:"/html/images/pre/img_prof_default.png"?>');"></div>
							<?}?>
						<?}?>
						<?if($user_cnt == 1){?>
							<?=$user_more?>
						<?}?>
						</div>
					</div>
				</a>
			</li>
		<?}?>|<?=$total_count?>|<?=$page_count?>
	<?}else{?>
		<div class="tdw_list_none"><strong><span>등록된 파티가 없습니다.</span></strong></div>
	<?}?>

<?php
	exit;
}


//파티연결하기
if($mode == "part_link_clear"){

	$work_idx = $_POST['work_idx'];
	$party_idx = $_POST['party_idx'];
	$work_idx = preg_replace("/[^0-9]/", "", $work_idx);
	$party_idx = preg_replace("/[^0-9]/", "", $party_idx);
	if($work_idx && $party_idx){

		//오늘업무 있는지 체크
		$sql = "select idx from work_todaywork where state!='9' and companyno='".$companyno."' and idx='".$work_idx."'";
		$work_info = selectQuery($sql);
		if($work_info['idx']){

			//파티가 있는지 체크
			$sql = "select idx from work_todaywork_project where state!='9' and companyno='".$companyno."' and idx='".$party_idx."'";
			$part_info = selectQuery($sql);
			if($part_info['idx']){

				//파티연결해제
				$sql = "select idx from work_todaywork_project_info where state='0' and companyno='".$companyno."' and party_idx='".$party_idx."' and work_idx='".$work_idx."'";
				$part_check_info = selectQuery($sql);
				if($part_check_info['idx']){
					$sql = "update work_todaywork_project_info set state='9', editdate=".DBDATE." where idx='".$part_check_info['idx']."'";
					$res = updateQuery($sql);
					if($res){
						//오늘업무 파티연결해제
						$sql = "update work_todaywork set party_link=null where idx='".$work_idx."'";
						$res = updateQuery($sql);
						if($res){
							//정상등록
							echo "complete";
							exit;
						}
					}
				}
			}else{
				//파티가 없을때
				echo "part_none";
				exit;
			}
		}else{
			echo "work_none";
			exit;
		}

	}else{
		echo "info_none";
		exit;
	}

	exit;
}

//파티구성원 새로고침
if($mode == "part_mem_list"){
	
	$sort = $_POST['sort'];
	$sort_type = $_POST['sort_type'];
	$party_idx = $_POST['party_idx'];
	$party_idx = preg_replace("/[^0-9]/", "", $party_idx);
	if($party_idx){

		$sql = "select idx, party_link from work_todaywork_project_info where state='0' and companyno='".$companyno."' and party_idx='".$party_idx."' order by idx asc limit 1";
		$project_allinfo = selectQuery($sql);
		$party_link = $project_allinfo['party_link'];

		$sql = "select a.idx, b.idx as bidx, b.email, b.name, a.part, case when a.email=b.email then 1 when a.email!=b.email then 0 end as pma";
		$sql = $sql .= " ,(select count(1) from work_todaywork as w left join work_todaywork_project_info as f on(w.idx=f.work_idx) where w.state='1' and f.state='0' and f.party_link='".$party_link."' and f.party_idx='".$party_idx."' and w.companyno='".$companyno."' and w.email=b.email) as complete";
		$sql = $sql .= " ,(select count(1) from work_todaywork as w left join work_todaywork_project_info as f on(w.idx=f.work_idx) where w.state!='9' and f.state='0' and f.party_link='".$party_link."' and f.party_idx='".$party_idx."' and w.companyno='".$companyno."' and w.email=b.email) as work";
		$sql = $sql .= " , (select count(1) from work_todaywork_like where state='0' and companyno='1' and service='party' and workdate='2023-02-24' and email=b.email) as heart";
		$sql = $sql .= " from work_todaywork_project as a left join work_todaywork_project_user as b on(a.idx=b.project_idx)";
		$sql = $sql .= " left join work_member as c on b.email = c.email";
		$sql = $sql .= " where a.state!='9' and b.state='0' and a.companyno='".$companyno."' and a.idx='".$party_idx."' and c.state != '9'";
		$sql = $sql .= " order by";


		//전체정렬
		if($sort_type == "all"){
			if($sort == "up"){
				$sql = $sql .= " b.idx asc";
			}else if($sort == "down"){
				$sql = $sql .= " b.idx desc";
			}
		//업무수
		}else if($sort_type == "works"){			
			if($sort == "up"){
				$sql = $sql .= " complete asc";
			}else if($sort == "down"){
				$sql = $sql .= " complete desc";
			}
		}
		//하트
		else if($sort_type == "heart"){			
			if($sort == "up"){
				$sql = $sql .= " heart asc";
			}else if($sort == "down"){
				$sql = $sql .= " heart desc";
			}
		}else{
			$sql = $sql .= " case when a.email=b.email then a.email end desc,";
			$sql = $sql .= " case when a.email!=b.email then b.idx end asc";
		}
		
		//echo $sql;
		$project_user_info = selectAllQuery($sql);

		for($i=0; $i<count($project_user_info['idx']); $i++){
			$project_user_idx = $project_user_info['bidx'][$i];
			$project_user_email = $project_user_info['email'][$i];
			$project_user_pma = $project_user_info['pma'][$i];
			$project_user_complete = $project_user_info['complete'][$i];
			$project_user_work = $project_user_info['work'][$i];
			$project_user_img = profile_img_info($project_user_email);

			$sql = "select idx, email,service, work_idx, send_email, like_flag from work_todaywork_like where state='0' and companyno='".$companyno."' and send_email='".$user_id."' and work_idx = '".$project_user_idx."'";
			$send_l = selectQuery($sql);


			//파티장일때 class설정
			if($project_user_pma=="1"){
				$part_leader = " party_leader ";
			}else{
				$part_leader = " ";
			}

			$member_row_info = member_row_info($project_user_email);
		?>
			<li>
				<div class="pu_list_conts_name<?=$part_leader?>party_new">
					<div class="pu_list_conts_name_in">
						<div class="user_img" style="background-image:url(<?=$project_user_img?>);"></div>
						<div class="user_name" id="user_name_<?=$project_user_idx?>">
							<strong><?=$member_row_info['name']?></strong>
							<span><?=$member_row_info['part']?></span>
							<input type="hidden" id="pu_list_id_<?=$project_user_idx?>" value="<?=$project_user_email?>">
							<?/*<em>N</em>*/?>
						</div>
					</div>
				</div>
				<div class="pu_list_conts_count">
					<span><?=$project_user_complete?>/<?=$project_user_work?></span>
				</div>
				<div class="pu_list_conts_heart">
					<?if($user_id != $project_user_email){?>
						<button class="btn_pu_coin" id="btn_pu_coin" value="<?=$project_user_idx?>"><span>코인</span></button>
						<?if($send_l['idx']){?>
							<button class="btn_pu_heart on" value="<?=$project_user_idx?>"><span>좋아요</span></button>
						<?}else{?>
							<button class="btn_pu_heart<?=($user_id==$project_user_email)?"_me":""?>" id="btn_pu_heart" value="<?=$project_user_idx?>"><span>좋아요</span></button>
						<?}?>
					<?}?>
				</div>
			</li>
		
		<?php
		}
	}

	exit;
}


//파티구성원 업무수 정렬
if($mode == "part_all_sort"){
	$sort = $_POST['sort'];
	$party_idx = $_POST['party_idx'];
	$party_idx = preg_replace("/[^0-9]/", "", $party_idx);

	if($party_idx){

		$sql = "select a.idx, b.idx as bidx, a.email, a.name, b.email, b.name, a.part, case when a.email=b.email then 1 when a.email!=b.email then 0 end as pma";
		$sql = $sql .= " from work_todaywork_project as a left join work_todaywork_project_user as b on(a.idx=b.project_idx)";
		$sql = $sql .= " where a.state!='9' and b.state='0' and a.companyno='".$companyno."' and a.idx='".$party_idx."'";
		$sql = $sql .= " order by";
		if($sort=="up"){
			$sql = $sql .= " b.idx asc";
		}else{
			$sql = $sql .= " b.idx desc";
		}

		$project_user_info = selectAllQuery($sql);

		for($i=0; $i<count($project_user_info['idx']); $i++){
			$project_user_idx = $project_user_info['bidx'][$i];
			$project_user_email = $project_user_info['email'][$i];
			$project_user_pma = $project_user_info['pma'][$i];
			$project_user_img = profile_img_info($project_user_email);

			$sql = "select idx, email,service, work_idx, send_email, like_flag from work_todaywork_like where state='0' and companyno='".$companyno."' and send_email='".$user_id."' and work_idx = '".$project_user_idx."'";
			$send_l = selectQuery($sql);

			//파티장일때 class설정
			
			if($project_user_pma=="1"){
				$part_leader = " party_leader ";
			}else{
				$part_leader = " ";
			}

			$member_row_info = member_row_info($project_user_email);
		?>
			<li>
				<div class="pu_list_conts_name<?=$part_leader?>party_new">
					<div class="pu_list_conts_name_in">
						<div class="user_img" style="background-image:url(<?=$project_user_img?>);"></div>
						<div class="user_name" id="user_name_<?=$project_user_idx?>">
							<strong><?=$member_row_info['name']?></strong>
							<span><?=$member_row_info['part']?></span>
							<input type="hidden" id="pu_list_id_<?=$project_user_idx?>" value="<?=$project_user_email?>">
							<?/*<em>N</em>*/?>
						</div>
					</div>
				</div>
				<div class="pu_list_conts_count">
					<span><?=$project_user_complete?>/<?=$project_user_work?></span>
				</div>
				<div class="pu_list_conts_heart">
					<?if($user_id != $project_user_email){?>
						<button class="btn_pu_coin" id="btn_pu_coin" value="<?=$project_user_idx?>"><span>코인</span></button>
						<?if($send_l['idx']){?>
							<button class="btn_pu_heart on" value="<?=$project_user_idx?>"><span>좋아요</span></button>
						<?}else{?>
							<button class="btn_pu_heart<?=($user_id==$project_user_email)?"_me":""?>" id="btn_pu_heart" value="<?=$project_user_idx?>"><span>좋아요</span></button>
						<?}?>
					<?}?>
				</div>
			</li>
		
		<?php
		}
	}
	exit;
}


//파티뷰페이지
if($mode == "party_view"){

	$party_idx = $_POST['party_idx'];
	$party_idx = preg_replace("/[^0-9]/", "", $party_idx);
	$input_search = $_POST['input_search'];
	$date_flag = $_POST['date_flag'];

	//파티링크정보
	$sql = "select idx, party_link from work_todaywork_project_info where state='0' and companyno='".$companyno."' and party_idx='".$party_idx."' order by idx asc limit 1";
	$project_allinfo = selectQuery($sql);
	$party_link = $project_allinfo['party_link'];


	//파티구성원
	$sql = "select a.idx, b.email, b.idx as bidx, b.name, a.part, case when a.email=b.email then 1 when a.email!=b.email then 0 end as pma";
	$sql = $sql .= " ,(select count(1) from work_todaywork as w left join work_todaywork_project_info as f on(w.idx=f.work_idx) where w.state='1' and f.party_link='".$party_link."' and f.party_idx='".$party_idx."' and w.companyno='".$companyno."' and w.email=b.email) as complete";
	$sql = $sql .= " ,(select count(1) from work_todaywork as w left join work_todaywork_project_info as f on(w.idx=f.work_idx) where w.state!='9' and f.party_link='".$party_link."' and f.party_idx='".$party_idx."' and w.companyno='".$companyno."' and w.email=b.email) as work";
	$sql = $sql .= " from work_todaywork_project as a left join work_todaywork_project_user as b on(a.idx=b.project_idx)";
	$sql = $sql .= " where a.state!='9' and b.state='0' and a.companyno='".$companyno."' and a.idx='".$party_idx."'";
	$sql = $sql .= " order by";
	$sql = $sql .= " case when a.email=b.email then a.email end desc,";
	$sql = $sql .= " case when a.email!=b.email then b.idx end asc";
	$project_user_info = selectAllQuery($sql);
	
	//파티구성원 전체인원수
	if($project_user_info['idx']){
		$project_user_cnt = count($project_user_info['idx']);
	}else{
		$project_user_cnt = 0;
	}

	//좋아요수
	$sql = "select count(1) as cnt from work_todaywork_like where state='0' and companyno='".$companyno."' and work_idx='".$party_idx."' and service='party'";
	$project_heart_info = selectQuery($sql);
	if($project_heart_info){
		$project_heart_cnt = $project_heart_info['cnt'];
	}else{
		$project_heart_cnt = 0;
	}


	if($input_search){
		$where_search = "";
		if($type == 'todaywork'){
			$where_search = $where_search .= " and a.work_flag='2'";
		}else if($type == 'report'){
			$where_search = $where_search .= " and a.work_flag='1'";
		}else if($type == 'share'){
			$where_search = $where_search .= " and a.share_flag in('1','2')";
		}else if($type == 'all'){
			$where_search = $where_search .= " and a.work_flag in('1','2','3')";
		}

		$where_search = $where_search .= " and (a.contents like '%".$input_search."%' or a.title like '%".$input_search."%')";

		// $sc_where = " and a.workdate between '".$sdate."' and '".$edate."'";
		// $where = " and workdate between '".$sdate."' and '".$edate."'";
	}else{
		if($date_flag == 1){
			$sc_where = "";
		}else{
			// $sc_where = " and a.workdate between '".$sdate."' and '".$edate."'";
		}
	}

	$sc_email = trim(@implode("','", $project_user_info['email']));
	
	$sc_where = $sc_where .= " and a.email in('".$sc_email."')";
	

	//전체업무수
	$sql = "select count(1) as cnt FROM work_todaywork as a left join work_todaywork_project_info as b on(a.idx=b.work_idx)";
	$sql = $sql .= " where a.state!='9' and b.state='0' and a.companyno='".$companyno."' and b.party_link= '".$party_link."' and b.party_idx='".$party_idx."'";
	$sql = $sql .= "and b.party_link is not null".$sc_where. $where_search;
	//echo $sql;
	$work_cnt_info = selectQuery($sql);
	$work_all_cnt = $work_cnt_info['cnt'];

	// echo $sql."|".$work_all_cnt."|";
	$pageurl = "party"; //project
	$string = "&page=".$pageurl."&sdate=".$sdate."&edate=".$edate."&nday=".$nday."&type=".$type;

	$countdate = $work_all_cnt;
	//echo "\n\n";
 
	//페이지
	$p = $_POST['p']?$_POST['p']:$_GET['p'];
	if (!$p){
		$p = 1;
	}

	$pagingsize = 5;					//페이징 사이즈
	$pagesize = 20;						//페이지 출력갯수
	$endnum = $p * $pagesize;			//페이지 끝번호

	//시작번호
	if ($p == 1){
		$startnum = 0;
	}else{
		$startnum = ($p - 1) * $pagesize;
	}
	$total_count = $countdate;

	$sql ="select a.idx, a.state, b.state as bstate, a.work_flag, a.part_flag, a.decide_flag, a.secret_flag, a.work_idx, a.repeat_flag, a.notice_flag, a.share_flag, date_format(a.regdate, '%Y.%m.%d') as ymd";
	$sql = $sql .= ", date_format(a.regdate, '%H:%i') as his, a.memo_view, a.contents_view, a.title, a.contents, a.email, a.name, a.req_date, a.workdate, a.regdate";
	$sql = $sql .= " FROM work_todaywork as a 
						left join work_todaywork_project_info as b on(a.idx=b.work_idx)
						";
	$sql = $sql .= " where a.state!='9' and b.state='0'";
	$sql = $sql .= " and b.party_link='".$party_link."' and b.party_idx='".$party_idx."'";
	$sql = $sql .= " and a.companyno='".$companyno."' and b.party_link is not null".$sc_where. $where_search;
	$sql = $sql .= " order by a.idx desc";
	$sql = $sql .= " limit ". $startnum.", ".$pagesize;
	if($user_id == 'adsb12@nate.com'){
		echo $sql."!@#";
	}
	$week_info = selectAllQuery($sql);

	for($i=0; $i<count($week_info['idx']); $i++){

		$idx = $week_info['idx'][$i];
		$state = $week_info['state'][$i];
		$work_email = $week_info['email'][$i];
		$work_name = $week_info['name'][$i];
		$work_flag = $week_info['work_flag'][$i];
		$work_idx = $week_info['work_idx'][$i];
		$repeat_flag = $week_info['repeat_flag'][$i];
		$share_flag = $week_info['share_flag'][$i];
		$memo_view = $week_info['memo_view'][$i];
		$contents_view = $week_info['contents_view'][$i];
		$decide_flag = $week_info['decide_flag'][$i];
		$notice_flag = $week_info['notice_flag'][$i];
		$workdate = $week_info['workdate'][$i];
		$title = $week_info['title'][$i];
		$contents = $week_info['contents'][$i];
		$contents_edit = strip_tags($week_info['contents'][$i]);


		//검색된 단어가 있을경우
		if($search){
			$contents = keywordHightlight($search, $contents);
			$title = keywordHightlight($search, $title);
		}

		$his = $week_info['his'][$i];
		$ymd = $week_info['ymd'][$i];

		$week_works[$workdate]['idx'][] = $idx;
		$week_works[$workdate]['state'][] = $state;
		$week_works[$workdate]['his'][] = $his;

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

		$week_works[$workdate]['ymd'][] = $ymd_change;
		$week_works[$workdate]['state'][] = $state;
		$week_works[$workdate]['title'][] = $title;
		$week_works[$workdate]['contents'][] = $contents;
		$week_works[$workdate]['contents_edit'][] = $contents_edit;
		$week_works[$workdate]['email'][] = $work_email;
		$week_works[$workdate]['name'][] = $work_name;
		$week_works[$workdate]['decide_flag'][] = $decide_flag;
		$week_works[$workdate]['work_flag'][] = $work_flag;
		$week_works[$workdate]['work_idx'][] = $work_idx;
		$week_works[$workdate]['repeat_flag'][] = $repeat_flag;
		$week_works[$workdate]['notice_flag'][] = $notice_flag;
		$week_works[$workdate]['share_flag'][] = $share_flag;
		$week_works[$workdate]['work_com_idx'][] = $work_com_idx;
		$week_works[$workdate]['memo_view'][] = $memo_view;
		$week_works[$workdate]['contents_view'][] = $contents_view;
	}


	//중복제거
	$week_unique = array_unique($week_info['workdate']);
	$key = 0;
	$new_arr = array();
	foreach($week_unique as $var) {
		$new_arr[$key] = $var;
		$key++;
	}

	rsort($new_arr);

	if($sdate && $edate){
		$monthday = $sdate;
		$sunday = $edate;

	}else if(!$search_date){
		$date_tmp = explode("-", TODATE);
		$year = $date_tmp[0];
		$month = $date_tmp[1];
		$day = $date_tmp[2];
		$ret = week_day($year-$month-$day);
		if($ret){
			//월요일
			$monthday = $ret['month'];

			//금요일
			$friday = $ret['friday'];

			//일요일
			$sunday = $ret['sunday'];

			//월요일, 타임으로
			$month = strtotime($monthday);
		}

	}
	

	if(count($new_arr)>0) {

		//등록된 업무가 있을경우
		$new_arr_date = end($new_arr[0]);

		//업무 댓글 (프로세스에서는 굳이 하트관련한 댓글을 필요없다고 판단)
		$sql = "select a.idx as cidx, a.link_idx, a.work_idx, a.email, a.name, a.comment, a.cmt_flag, CASE WHEN a.editdate is not null then date_format(a.editdate , '%Y-%m-%d') WHEN a.editdate is null then date_format(a.regdate , '%Y-%m-%d') end as ymd,";
		$sql = $sql .= " CASE WHEN a.editdate is not null then date_format(a.editdate , '%m/%d/%y %l:%i:%s %p') WHEN a.editdate is null then date_format(a.regdate , '%m/%d/%y %l:%i:%s %p') end as regdate";
		$sql = $sql .= " ,b.idx"; 
		$sql = $sql .= " from work_todaywork_comment as a 
		left join work_todaywork as b on(a.link_idx=b.idx)
		where a.state=0 and a.email is not null and a.companyno='".$companyno."' and b.workdate>='".$new_arr_date."' order by a.regdate desc";
		$works_comment_info = selectAllQuery($sql);

		for($i=0; $i<count($works_comment_info['idx']); $i++){
			$works_comment_info_idx = $works_comment_info['cidx'][$i];
			$works_comment_info_link_idx = $works_comment_info['link_idx'][$i];
			$works_comment_info_work_idx = $works_comment_info['work_idx'][$i];
			$works_comment_info_email = $works_comment_info['email'][$i];
			$works_comment_info_send = $works_comment_info['send'][$i];
			$works_comment_info_name = $works_comment_info['name'][$i];
			$works_comment_info_comment = $works_comment_info['comment'][$i];
			$works_comment_info_comment_strip = strip_tags($works_comment_info['comment'][$i]);
			$works_comment_info_ymd = $works_comment_info['ymd'][$i];
			$works_comment_info_regdate = $works_comment_info['regdate'][$i];
			$works_comment_info_cmt_flag = $works_comment_info['cmt_flag'][$i];
			$works_comment_info_secret_flag = $works_comment_info['secret_flag'][$i];


			if($works_comment_info_link_idx){
				$comment_list[$works_comment_info_link_idx]['cidx'][] = $works_comment_info_idx;
				$comment_list[$works_comment_info_link_idx]['work_idx'][] = $works_comment_info_work_idx;
				$comment_list[$works_comment_info_link_idx]['name'][] = $works_comment_info_name;
				$comment_list[$works_comment_info_link_idx]['email'][] = $works_comment_info_email;
				$comment_list[$works_comment_info_link_idx]['send'][] = $works_comment_info_send;
				$comment_list[$works_comment_info_link_idx]['comment'][] = $works_comment_info_comment;
				$comment_list[$works_comment_info_link_idx]['comment_strip'][] = $works_comment_info_comment_strip;
				$comment_list[$works_comment_info_link_idx]['ymd'][] = $works_comment_info_ymd;
				$comment_list[$works_comment_info_link_idx]['regdate'][] = $works_comment_info_regdate;
				$comment_list[$works_comment_info_link_idx]['cmt_flag'][] = $works_comment_info_cmt_flag;
				$comment_list[$works_comment_info_link_idx]['secret_flag'][] = $works_comment_info_secret_flag;
			}
		}


		//첨부파일정보
		$sql = "select idx, work_idx, email, num, file_path, file_name, file_real_name, workdate from work_filesinfo_todaywork where state='0' and companyno='".$companyno."' order by idx asc";
		$todaywork_file_info = selectAllQuery($sql);
		for($i=0; $i<count($todaywork_file_info['idx']); $i++){

			$tdf_idx = $todaywork_file_info['idx'][$i];
			$tdf_num = $todaywork_file_info['num'][$i];
			$tdf_email = $todaywork_file_info['email'][$i];
			$tdf_work_idx = $todaywork_file_info['work_idx'][$i];
			$tdf_file_path = $todaywork_file_info['file_path'][$i];
			$tdf_file_name = $todaywork_file_info['file_name'][$i];
			$tdf_file_real_name = $todaywork_file_info['file_real_name'][$i];
			$tdf_file_workdate = $todaywork_file_info['workdate'][$i];

			$tdf_files[$tdf_work_idx]['idx'][] = $tdf_idx;
			$tdf_files[$tdf_work_idx]['num'][] = $tdf_num;
			$tdf_files[$tdf_work_idx]['email'][] = $tdf_email;
			$tdf_files[$tdf_work_idx]['file_path'][] = $tdf_file_path;
			$tdf_files[$tdf_work_idx]['tdf_file_name'][] = $tdf_file_name;
			$tdf_files[$tdf_work_idx]['file_real_name'][] = $tdf_file_real_name;
		}
		for($i=0; $i<count($new_arr); $i++){
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

					<? for($j=0; $j < count($week_works[$workdate]['contents']); $j++){

						$week_works_idx = $week_works[$workdate]['idx'][$j];
						$work_idx = $week_works[$workdate]['work_idx'][$j]; // 공유 받거나 한것들
						$week_works_state = $week_works[$workdate]['state'][$j];
						$week_works_his = $week_works[$workdate]['his'][$j];
						$week_works_email = $week_works[$workdate]['email'][$j];
						$week_works_name = $week_works[$workdate]['name'][$j];
						$work_flag = $week_works[$workdate]['work_flag'][$j];

						$work_wtitle = $week_works[$workdate]['title'][$j];
						$work_contents = $week_works[$workdate]['contents'][$j];

						//검색어 하이라이트 처리
						if($input_search){
							$work_wtitle = keywordHightlight($input_search, $work_wtitle);
							$work_contents = keywordHightlight($input_search, $work_contents);
						}


						//알림설정
						$notice_flag = $week_works[$workdate]['notice_flag'][$j];

						//공유설정
						$share_flag = $week_works[$workdate]['share_flag'][$j];


						//요청 및 공유, 보고
						if($work_idx){
							$work_com_idx = $work_idx;
						}else{
							$work_com_idx = $week_works_idx;
						}

					?>
						<? if(($secret_flag == '1' && $week_works_email == $user_id)){?>
							<li class="tdw_list_li<?=$li_class?>" id="workslist_<?=$week_works_idx?>" value="<?=($i+1)?>">
								<div class="tdw_list_box<?=($week_works_state=='1')?" on":""?>"  id="tdw_list_box_<?=$week_works_idx?>">
									<div class="tdw_list_chk">
										<?if($work_flag=='1'){?>
											<button class="btn_tdw_list_chk" id="tdw_dlist_chk" value="<?=$week_works_idx?>"><span>완료체크</span></button>
										<?}else{?>
											<button class="btn_tdw_list_chk" id="tdw_dlist_chk" value="<?=$week_works_idx?>"><span>완료체크</span></button>
										<?}?>
									</div>
									<div class="tdw_list_desc <?=$secret_flag == '1'?"lock":""?>">
										<?/*<p><span></span><?=$week_works_contents?></p>*/?>

										<?if($work_idx){?>
											<?if($notice_flag=="1"){?>
												<p <?=$edit_id?> id="notice_link" value="<?=$work_idx?>">
											<?}else{?>
												<p <?=$edit_id?>>
											<?}?>
												<?=textarea_replace($work_contents)?>
											</p>

											<?}else{?>

											<!--보고업무-->
												<p id="tdw_wlist_edit_<?=$week_works_idx?>">
												<?=textarea_replace($work_contents)?></p>
											<?}?>
									</div>

									<div class="tdw_list_function">
										<? if($user_id == $week_works_email){?>
											<div class="tdw_list_function_in">
												<input type="hidden" id="work_idx" value="<?=$week_works_idx?>">
												<input type="hidden" id="work_date" value="<?=$workdate?>">
												<button class="tdw_list_party_date"><span><?=$week_works_his?></span></button>
												<button class="tdw_list_party_name"><span><?=$week_works_name?></span></button>
												<?php if($secret_flag == '1'){?>
													<button class="tdw_list_party_memo_secret" id="tdw_list_party_memo" value="<?=$week_works_idx?>"><span>메모</span></button>
												<?php }else{ ?>
													<button class="tdw_list_party_memo" id="tdw_list_party_memo" value="<?=$week_works_idx?>"><span>메모</span></button>
												<?php } ?>
												<button class="tdw_list_party_link<?=($user_id==$week_works_email)?" on":""?>" id="tdw_list_party_link" value="<?=$week_works_idx?>"><span>파티연결</span></button>
											</div>
										<?}else{
											$sql = "select idx,state,companyno,service,work_idx,email,send_email,workdate from work_todaywork_like where state = '0' and companyno = '".$companyno."' and send_email = '".$user_id."' and work_idx = '".$week_works_idx."'";
											$like_coma = selectQuery($sql);
											$sql = "select * from work_todaywork where state = '0' and companyno = '".$companyno."' and idx = '".$week_works_idx."'";
											$work_kind = selectQuery($sql);
											if($work_kind['work_flag']=='1' or ($work_kind['work_flag']=='2' and $work_kind['share_flag']=='1')){?>
											<div class="tdw_list_function_in">
												<input type="hidden" id="work_flag_<?=$week_works_idx?>" value="<?=$work_kind['work_flag']?>">
												<input type="hidden" id="work_idx" value="<?=$week_works_idx?>">
												<input type="hidden" id="work_date" value="<?=$workdate?>">
												<input type="hidden" id="pu_list_id_<?=$week_works_idx?>" value="<?=$week_works_email?>">
												<button class="tdw_list_party_date"><span><?=$week_works_his?></span></button>
												<button class="tdw_list_party_name"><span><?=$week_works_name?></span></button>
												<?php if($secret_flag == '1'){?>
													<button class="tdw_list_party_memo_secret" id="tdw_list_party_memo" value="<?=$week_works_idx?>"><span>메모</span></button>
												<?php }else{ ?>
													<button class="tdw_list_party_memo" id="tdw_list_party_memo" value="<?=$week_works_idx?>"><span>메모</span></button>
												<?php } ?>
												<button class="tdw_list_100c" title="100코인" id="coin_reward" value="<?=$week_works_idx?>"><span>100</span></button>
												<button class="tdw_list_party_heart<?=$like_coma>0?" on":""?>" id="tdw_list_party_heart_<?=$week_works_idx?>" value="<?=$week_works_idx?>"><span>좋아요</span></button>
											</div>
											<?}else{?>
											<div class="tdw_list_function_in">
												<input type="hidden" id="work_idx" value="<?=$week_works_idx?>">
												<input type="hidden" id="work_date" value="<?=$workdate?>">
												<button class="tdw_list_party_date"><span><?=$week_works_his?></span></button>
												<button class="tdw_list_party_name"><span><?=$week_works_name?></span></button>
												<?php if($secret_flag == '1'){?>
													<button class="tdw_list_party_memo_secret" id="tdw_list_party_memo" value="<?=$week_works_idx?>"><span>메모</span></button>
												<?php }else{ ?>
													<button class="tdw_list_party_memo" id="tdw_list_party_memo" value="<?=$week_works_idx?>"><span>메모</span></button>
												<?php } ?>
											</div>
											<?}?>
										<?}?>
									</div>
									
									<?//첨부파일 정보
									//나의업무, 요청업무
									if(in_array($work_flag, array('1','2','3'))){
										if($tdf_files[$work_com_idx]['file_path']){?>
											<div class="tdw_list_file">
												<?for($k=0; $k<count($tdf_files[$work_com_idx]['file_path']); $k++){?>
													<div class="tdw_list_file_box">
														<button class="btn_list_file" id="btn_list_file_<?=$k?>" value="<?=$tdf_files[$work_com_idx]['idx'][$k]?>"><span><?=$tdf_files[$work_com_idx]['file_real_name'][$k]?></span></button>

														<?//보고업무 작성한 사용자만 삭제
														if($user_id==$tdf_files[$work_com_idx]['email'][$k]){?>
															<button class="btn_list_file_del" id="btn_list_fdel" value="<?=$tdf_files[$work_com_idx]['idx'][$k]?>" title="삭제"><span>삭제</span></button>
														<?}?>

													</div>
												<?}?>
											</div>
										<?}
									}?>

								</div>


								<div class="tdw_list_memo_area">
									<div class="tdw_list_memo_area_in<?=$memo_view_in?>" id="tdw_list_memo_area_in_<?=$week_works_idx?>">
										<?//댓글리스트
										//요청업무
										if($work_flag == '3'){?>
											<?if($comment_list[$work_com_idx]){?>
												<?for($k=0; $k<count($comment_list[$work_com_idx]['cidx']); $k++){
													$comment_idx = $comment_list[$work_com_idx]['cidx'][$k];

													$chis = $comment_list[$work_com_idx]['regdate'][$k];
													$ymd = $comment_list[$work_com_idx]['ymd'][$k];
													$cmt_flag = $comment_list[$work_com_idx]['cmt_flag'][$k];
													if($chis){
														$chis = str_replace("  "," ", $chis);
														$chis_tmp = @explode(" ", $chis);
														if ($chis_tmp['2'] == "PM"){
															$after = "오후 ";
														}else{
															$after = "오전 ";
														}
														$ctime = @explode(":", $chis_tmp['1']);
														$chiss = $ymd . " " . $after . $ctime['0'] .":". $ctime['1'];
													}

													$sql = "select a.name as name,a.comment as comment,a.send_name as send from work_todaywork_like a join work_todaywork_comment b";
													$sql = $sql." on a.com_idx = b.idx where a.state != 9 and a.com_idx = '".$comment_idx."'";
													$work_give_list = selectQuery($sql);

													if($work_give_list){
														$work_give_like_name = $work_give_list['name'];
														$work_give_like_comment = $work_give_list['comment'];
														$work_send_like_name = $work_give_list['send'];
													}

													// 코인보상 레이어 업무 요청한 사람만 보이게(김정훈)
													$sql = "select idx from work_todaywork where idx = '".$week_works_idx."' and work_idx is null";
													$work_link_coin = selectQuery($sql);

													$sql = "select a.idx from work_todaywork_like a join work_todaywork_comment b on a.work_idx = b.link_idx";
													$sql = $sql." where b.idx = '".$comment_idx."' and a.ai_like_idx = b.ai_like_idx and send_email = '".$user_id."'";
													$click_like = selectQuery($sql);

													$sql = "select a.idx from work_todaywork_like a join work_todaywork_comment b on a.work_idx = b.idx where b.idx = '".$comment_idx."'";
													$sql = $sql." and a.state = 0 and a.send_email = '".$user_id."'";
													$cli_like = selectQuery($sql);

													$sql = "select idx from work_todaywork_comment where idx = '".$comment_idx."' and email = '".$user_id."'";
													$my_like = selectQuery($sql);

													$sql = "select idx from work_todaywork_comment where idx = '".$comment_idx."' and like_email = '".$user_id."'";
													$my_coin_like = selectQuery($sql);

												?>

												<div class="tdw_list_memo_desc" id="comment_list_<?=$comment_idx?>" >
													<?if($cmt_flag == 1){?>
														<!-- 좋아요 변경으로 인한 코드 -->
														<?if($work_give_list){?>
															<div class="tdw_list_memo_name"><?=$work_send_like_name?></div>
														<?}else{?>
															<div class="tdw_list_memo_name ai">AI</div>
														<?}?>
													<?}else{?>
														<?if($cmt_flag != 2){?>
															<div class="tdw_list_memo_name"><?=$comment_list[$work_com_idx]['name'][$k]?></div>
														<?}?>
													<?}?>

													<!-- 좋아요 변경으로 인한 코드(김정훈) -->
													<?if($cmt_flag == 1){?>
														<?//좋아요 보낸 내역이 있을때
														if($work_give_list){?>
															<button class="btn_memo_jjim on" value="<?=$comment_idx?>"><span>좋아요</span></button>
														<?}?>
													<?}?>

													<div class="tdw_list_memo_conts">
														<?if(!$cmt_flag && $user_id==$comment_list[$work_com_idx]['email'][$k]){?>
															<span class="tdw_list_memo_conts_txt" id="tdw_list_memo_conts_txt_<?=$comment_idx?>"><?=textarea_replace($comment_list[$work_com_idx]['comment'][$k])?></span>
														<?}else if($cmt_flag == 1 && $work_give_list){?>
															<span class="tdw_list_memo_conts_txt"><?=textarea_replace($comment_list[$work_com_idx]['comment'][$k])?></span>
														<?}else{?>
															<?if($cmt_flag != 2){?>
																<span class="tdw_list_memo_conts_txt"><?=textarea_replace($comment_list[$work_com_idx]['comment'][$k])?></span>
															<?}?>
														<?}?>
														<?if($cmt_flag != 2){?>
															<em class="tdw_list_memo_conts_date"><?=$chiss?>
														<?}?>
														<?//ai글 일때, 공유요청한 사람만 뜨게
														if($cmt_flag == 1 && $work_link_coin && !$my_coin_like){?>

														<?}?>

														<?//자동 ai댓글?>
														<?if($cmt_flag == 1){?>

														<?}else{?>
															<?if($cmt_flag != 2){?>
																<?if(!$my_like){?>
																	<?if($cli_like){?>
																		<button class="btn_memo_jjim on" value="<?=$comment_idx?>"><span>좋아요</span></button>
																	<?}else{?>
																		<button class="btn_memo_jjim" id="btn_memo_jjim_<?=$comment_idx?>" value="<?=$comment_idx?>"><span>좋아요</span></button>
																	<?}?>
																	<button class="btn_req_100c" id="btn_req_100c" title="100코인" value="<?=$comment_list[$work_com_idx]['cidx'][$k]?>"><span>100코인</span></button>
																	<input type="hidden" value="<?=$comment_list[$work_com_idx]['email'][$k]?>" id="comment_idx_<?=$comment_idx?>">
																<?}?>
															<?}?>
														<?}?>

														<?if(!$cmt_flag && $user_id==$comment_list[$work_com_idx]['email'][$k]){?>
															<button class="btn_memo_del" id="btn_memo_del" value="<?=$comment_idx?>"><span>삭제</span></button>
														<?}?>

														<?if($cmt_flag != 2){?>
															</em>
														<?}?>
														<div class="tdw_list_memo_regi" id="tdw_list_memo_regi_<?=$comment_idx?>">
															<textarea name="" class="textarea_regi" id="tdw_comment_edit_<?=$comment_idx?>"><?=$comment_list[$work_com_idx]['comment_strip'][$k]?></textarea>
															<div class="btn_regi_box">
																<button class="btn_regi_submit" id="btn_comment_submit" value="<?=$comment_idx?>"><span>확인</span></button>
																<button class="btn_regi_cancel" id="btn_regi_cancel" value="<?=$comment_idx?>"><span>취소</span></button>
															</div>
														</div>
													</div>
												</div>
											<?}?>
										<?}?>

										<?}else{?>
											<?//받은업무
											if ($work_idx){?>
												<?if($comment_list[$work_idx]){?>
													<?for($k=0; $k<count($comment_list[$work_idx]['cidx']); $k++){
														$comment_idx = $comment_list[$work_idx]['cidx'][$k];
														$chis = $comment_list[$work_idx]['regdate'][$k];
														$ymd = $comment_list[$work_idx]['ymd'][$k];
														$cmt_flag = $comment_list[$work_idx]['cmt_flag'][$k];

														if($chis){
															$chis = str_replace("  "," ", $chis);
															$chis_tmp = @explode(" ", $chis);
															if ($chis_tmp['2'] == "PM"){
																$after = "오후 ";
															}else{
																$after = "오전 ";
															}
															$ctime = @explode(":", $chis_tmp['1']);
															$chiss = $ymd . " " . $after . $ctime['0'] .":". $ctime['1'];
														}

														$sql = "select a.name as name,a.comment as comment,a.send_name as send from work_todaywork_like a join work_todaywork_comment b";
														$sql = $sql." on a.com_idx = b.idx where a.state != 9 and a.com_idx = '".$comment_idx."'";
														$work_give_list = selectQuery($sql);

														if($work_give_list){
															$work_give_like_name = $work_give_list['name'];
															$work_give_like_comment = $work_give_list['comment'];
															$work_send_like_name = $work_give_list['send'];
														}

														$sql = "select a.idx from work_todaywork_like a join work_todaywork_comment b on a.work_idx = b.idx where b.idx = '".$comment_idx."'";
														$sql = $sql." and a.state = 0 and a.send_email = '".$user_id."'";
														$cli_like = selectQuery($sql);
														?>

														<div class="tdw_list_memo_desc" id="comment_list_<?=$comment_idx?>" >

															<?if($cmt_flag == 1){?>
																<!-- 좋아요 변경으로 인한 코드 -->
																<?if($work_give_list){?>
																	<div class="tdw_list_memo_name"><?=$work_send_like_name?></div>
																<?}else{?>
																	<div class="tdw_list_memo_name ai">AI</div>
																<?}?>
															<?}else{?>
																<?if($cmt_flag !=2){?>
																	<div class="tdw_list_memo_name"><?=$comment_list[$work_idx]['name'][$k]?></div>
																<?}?>
															<?}?>

															<!-- 좋아요 변경으로 인한 코드(김정훈) -->
															<?if($cmt_flag == 1){?>
																<?//좋아요 보낸 내역이 있을때
																if($work_give_list){?>
																	<button class="btn_memo_jjim on" value="<?=$comment_idx?>"><span>좋아요</span></button>
																<?}?>
															<?}?>

															<div class="tdw_list_memo_conts">
																<?if(!$cmt_flag && $user_id==$comment_list[$work_idx]['email'][$k]){?>
																	<!-- 일반 메모 -->
																	<span class="tdw_list_memo_conts_txt" id="tdw_list_memo_conts_txt_<?=$comment_idx?>"><?=textarea_replace($comment_list[$work_idx]['comment'][$k])?></span>
																<?}else if($cmt_flag == 1 && $work_give_list){?>
																	<!-- 좋아요 받았을 때 문장 -->
																	<span class="tdw_list_memo_conts_txt"><?=textarea_replace($comment_list[$work_idx]['comment'][$k])?></span>
																<?}else{?>
																	<?if($cmt_flag != 2){?>
																		<!-- AI 문장 -->
																		<span class="tdw_list_memo_conts_txt"><?=textarea_replace($comment_list[$work_idx]['comment'][$k])?></span>
																	<?}?>
																<?}?>

																<?if($cmt_flag != 2){?>
																	<em class="tdw_list_memo_conts_date"><?=$chiss?>
																<?}?>

																<?//자동 ai댓글?>
																<?if($cmt_flag == 1){?>

																<?}else{?>
																	<?if($cmt_flag != 2){?>
																		<?if($user_id!=$comment_list[$work_idx]['email'][$k]){?>
																			<?if($cli_like){?>
																				<button class="btn_memo_jjim on" value="<?=$comment_idx?>"><span>좋아요</span></button>
																			<?}else{?>
																				<button class="btn_memo_jjim" id="btn_memo_jjim_<?=$comment_idx?>" value="<?=$comment_idx?>"><span>좋아요</span></button>
																			<?}?>
																			<button class="btn_req_100c" id="btn_req_100c" title="100코인" value="<?=$comment_list[$work_idx]['cidx'][$k]?>"><span>100코인</span></button>
																			<input type="hidden" value="<?=$comment_list[$work_idx]['email'][$k]?>" id="comment_idx_<?=$comment_idx?>">
																		<?}?>
																	<?}?>
																<?}?>

																<?if(!$cmt_flag && $user_id==$comment_list[$work_idx]['email'][$k]){?>
																	<button class="btn_memo_del" id="btn_memo_del" value="<?=$comment_idx?>"><span>삭제</span></button>
																<?}?>
																</em>

																<div class="tdw_list_memo_regi" id="tdw_list_memo_regi_<?=$comment_idx?>">
																	<textarea name="" class="textarea_regi" id="tdw_comment_edit_<?=$comment_idx?>"><?=$comment_list[$work_idx]['comment_strip'][$k]?></textarea>
																	<div class="btn_regi_box">
																		<button class="btn_regi_submit" id="btn_comment_submit" value="<?=$comment_idx?>"><span>확인</span></button>
																		<button class="btn_regi_cancel" id="btn_regi_cancel" value="<?=$comment_idx?>"><span>취소</span></button>
																	</div>
																</div>
															</div>
														</div>

													<?}?>

												<?}?>

											<?}else{?>
												<?
												//일반업무
												if($comment_list[$week_works_idx]){?>

													<?for($k=0; $k<count($comment_list[$week_works_idx]['cidx']); $k++){
														$comment_idx = $comment_list[$week_works_idx]['cidx'][$k];

														$chis = $comment_list[$week_works_idx]['regdate'][$k];
														$ymd = $comment_list[$week_works_idx]['ymd'][$k];
														$cmt_flag = $comment_list[$week_works_idx]['cmt_flag'][$k];

														if($chis){
															$chis = str_replace("  "," ", $chis);
															$chis_tmp = @explode(" ", $chis);
															if ($chis_tmp['2'] == "PM"){
																$after = "오후 ";
															}else{
																$after = "오전 ";
															}
															$ctime = @explode(":", $chis_tmp['1']);
															$chiss = $ymd . " " . $after . $ctime['0'] .":". $ctime['1'];
														}

														$sql = "select a.name as name,a.comment as comment,a.send_name as send from work_todaywork_like a join work_todaywork_comment b";
														$sql = $sql." on a.com_idx = b.idx where a.state != 9 and a.com_idx = '".$comment_idx."'";
														$work_give_list = selectQuery($sql);

														if($work_give_list){
															$work_give_like_name = $work_give_list['name'];
															$work_give_like_comment = $work_give_list['comment'];
															$work_send_like_name = $work_give_list['send'];
														}

														$sql = "select a.idx from work_todaywork_like a join work_todaywork_comment b on a.work_idx = b.idx where b.idx = '".$comment_idx."'";
														$sql = $sql." and a.state = 0 and a.send_email = '".$user_id."'";
														$cli_like = selectQuery($sql);

													?>

													<div class="tdw_list_memo_desc" id="comment_list_<?=$comment_idx?>" >

														<?if($cmt_flag == 1){?>
															<!-- 좋아요 변경으로 인한 코드 -->
															<?if($work_give_list){?>
																<div class="tdw_list_memo_name"><?=$work_send_like_name?></div>
															<?}else{?>
																<div class="tdw_list_memo_name ai">AI</div>
															<?}?>
														<?}else{?>
															<?if($cmt_flag != 2){?>
																<div class="tdw_list_memo_name"><?=$comment_list[$week_works_idx]['name'][$k]?></div>
															<?}?>
														<?}?>

														<!-- 좋아요 변경으로 인한 코드(김정훈) -->
														<?if($cmt_flag == 1){?>
															<?//좋아요 보낸 내역이 있을때
															if($work_give_list){?>
																<button class="btn_memo_jjim on" value="<?=$comment_idx?>"><span>좋아요</span></button>
															<?}?>
														<?}?>


														<div class="tdw_list_memo_conts">
															<?if(!$cmt_flag && $user_id==$comment_list[$week_works_idx]['email'][$k]){?>
																<!-- 일반 메모 -->
																<span class="tdw_list_memo_conts_txt" id="tdw_list_memo_conts_txt_<?=$comment_idx?>"><?=textarea_replace($comment_list[$week_works_idx]['comment'][$k])?></span>
															<?}else if($cmt_flag == 1 && $work_give_list){?>
																<!-- 좋아요 받았을 때 문장 -->
																<span class="tdw_list_memo_conts_txt"><?=textarea_replace($comment_list[$week_works_idx]['comment'][$k])?></span>
															<?}else{?>
																<?if($cmt_flag != 2){?>
																	<!-- AI 문장 -->
																	<span  class="tdw_list_memo_conts_txt"><?=textarea_replace($comment_list[$week_works_idx]['comment'][$k])?></span>
																<?}?>
															<?}?>

															<?if($cmt_flag != 2){?>
																<em class="tdw_list_memo_conts_date"><?=$chiss?>
															<?}?>

																<?//자동 ai댓글?>
																<?if($cmt_flag == 1){?>

																<?}else{?>
																	<?if($cmt_flag != 2){?>
																		<?if($user_id!=$comment_list[$week_works_idx]['email'][$k]){?>
																			<?if($cli_like){?>
																				<button class="btn_memo_jjim on" value="<?=$comment_idx?>"><span>좋아요</span></button>
																			<?}else{?>
																				<button class="btn_memo_jjim" id="btn_memo_jjim_<?=$comment_idx?>" value="<?=$comment_idx?>"><span>좋아요</span></button>
																			<?}?>
																			<button class="btn_req_100c" id="btn_req_100c" title="100코인" value="<?=$comment_list[$week_works_idx]['cidx'][$k]?>"><span>100코인</span></button>
																			<input type="hidden" value="<?=$comment_list[$week_works_idx]['email'][$k]?>" id="comment_idx_<?=$comment_idx?>">
																		<?}?>
																	<?}?>
																<?}?>

															<?if(!$cmt_flag && $user_id==$comment_list[$week_works_idx]['email'][$k]){?>
																<button class="btn_memo_del" id="btn_memo_del" value="<?=$comment_idx?>"><span>삭제</span></button>
															<?}?>

															<?if($cmt_flag != 2){?>
																</em>
															<?}?>

															<div class="tdw_list_memo_regi" id="tdw_list_memo_regi_<?=$comment_idx?>">
																<textarea name="" class="textarea_regi" id="tdw_comment_edit_<?=$comment_idx?>"><?=strip_tags($comment_list[$week_works_idx]['comment'][$k])?></textarea>
																<div class="btn_regi_box">
																	<button class="btn_regi_submit" id="btn_comment_submit" value="<?=$comment_idx?>"><span>확인</span></button>
																	<button class="btn_regi_cancel"><span>취소</span></button>
																</div>
															</div>
														</div>
													</div>
													<?}?>
												<?}?>
											<?}?>
										<?}?>
									</div>
								</div>

							</li>
						<? }else if($secret_flag != '1'){?>
							<li class="tdw_list_li<?=$li_class?>" id="workslist_<?=$week_works_idx?>" value="<?=($i+1)?>">
								<div class="tdw_list_box<?=($week_works_state=='1')?" on":""?>"  id="tdw_list_box_<?=$week_works_idx?>">
									<div class="tdw_list_chk">
										<?if($work_flag=='1'){?>
											<button class="btn_tdw_list_chk" id="tdw_dlist_chk" value="<?=$week_works_idx?>"><span>완료체크</span></button>
										<?}else{?>
											<button class="btn_tdw_list_chk" id="tdw_dlist_chk" value="<?=$week_works_idx?>"><span>완료체크</span></button>
										<?}?>
									</div>
									<div class="tdw_list_desc">
										<?/*<p><span></span><?=$week_works_contents?></p>*/?>

										<?if($work_idx){?>
											<?if($notice_flag=="1"){?>
												<p <?=$edit_id?> id="notice_link" value="<?=$work_idx?>">
											<?}else{?>
												<p <?=$edit_id?>>
											<?}?>
											<?if($secret_flag == '1' && $week_works_email != $user_id){?>
													<?if($secret_flag == '1' && $send == $user_id){?>
														<?=textarea_replace($work_contents)?>
													<?}else{?>
														<img src = "/html/images/pre/ico_tr_l.png">비밀글 입니다.</p>
													<?}?>
												<?}else{?>
													<?=textarea_replace($work_contents)?>
												<?}?>
											</p>

											<?}else{?>
											<!--보고업무-->
												<p id="tdw_wlist_edit_<?=$week_works_idx?>">
												<?if($secret_flag == '1' && $week_works_email != $user_id){?>
													<?if($secret_flag == '1' && $send == $user_id){?>
														<?=textarea_replace($work_contents)?></p>
													<?}else{?>
														<img src = "/html/images/pre/ico_tr_l.png">비밀글 입니다.</p>
													<?}?>
												<?}else{?>
													<?=textarea_replace($work_contents)?></p>
												<?}?>
											<?}?>
									</div>

									<div class="tdw_list_function">
										<? if($user_id == $week_works_email){?>
											<div class="tdw_list_function_in">
												<input type="hidden" id="work_idx" value="<?=$week_works_idx?>">
												<input type="hidden" id="work_date" value="<?=$workdate?>">
												<button class="tdw_list_party_date"><span><?=$week_works_his?></span></button>
												<button class="tdw_list_party_name"><span><?=$week_works_name?></span></button>
												<?php if($secret_flag == '1'){?>
													<button class="tdw_list_party_memo_secret" id="tdw_list_party_memo" value="<?=$week_works_idx?>"><span>메모</span></button>
												<?php }else{ ?>
													<button class="tdw_list_party_memo" id="tdw_list_party_memo" value="<?=$week_works_idx?>"><span>메모</span></button>
												<?php } ?>
												<button class="tdw_list_party_link<?=($user_id==$week_works_email)?" on":""?>" id="tdw_list_party_link" value="<?=$week_works_idx?>"><span>파티연결</span></button>
											</div>
										<?}else{
											$sql = "select idx,state,companyno,service,work_idx,email,send_email,workdate from work_todaywork_like where state = '0' and companyno = '".$companyno."' and send_email = '".$user_id."' and work_idx = '".$week_works_idx."'";
											$like_coma = selectQuery($sql);
											$sql = "select * from work_todaywork where state = '0' and companyno = '".$companyno."' and idx = '".$week_works_idx."'";
											$work_kind = selectQuery($sql);
											if($work_kind['work_flag']=='1' or ($work_kind['work_flag']=='2' and $work_kind['share_flag']=='1')){?>
											<div class="tdw_list_function_in">
												<input type="hidden" id="work_flag_<?=$week_works_idx?>" value="<?=$work_kind['work_flag']?>">
												<input type="hidden" id="work_idx" value="<?=$week_works_idx?>">
												<input type="hidden" id="work_date" value="<?=$workdate?>">
												<input type="hidden" id="pu_list_id_<?=$week_works_idx?>" value="<?=$week_works_email?>">
												<button class="tdw_list_party_date"><span><?=$week_works_his?></span></button>
												<button class="tdw_list_party_name"><span><?=$week_works_name?></span></button>
												<?php if($secret_flag == '1'){?>
													<button class="tdw_list_party_memo_secret" id="tdw_list_party_memo" value="<?=$week_works_idx?>"><span>메모</span></button>
												<?php }else{ ?>
													<button class="tdw_list_party_memo" id="tdw_list_party_memo" value="<?=$week_works_idx?>"><span>메모</span></button>
												<?php } ?>
												<button class="tdw_list_100c" title="100코인" id="coin_reward" value="<?=$week_works_idx?>"><span>100</span></button>
												<button class="tdw_list_party_heart<?=$like_coma>0?" on":""?>" id="tdw_list_party_heart_<?=$week_works_idx?>" value="<?=$week_works_idx?>"><span>좋아요</span></button>
											</div>
											<?}else{?>
											<div class="tdw_list_function_in">
												<input type="hidden" id="work_idx" value="<?=$week_works_idx?>">
												<input type="hidden" id="work_date" value="<?=$workdate?>">
												<button class="tdw_list_party_date"><span><?=$week_works_his?></span></button>
												<button class="tdw_list_party_name"><span><?=$week_works_name?></span></button>
												<?php if($secret_flag == '1'){?>
													<button class="tdw_list_party_memo_secret" id="tdw_list_party_memo" value="<?=$week_works_idx?>"><span>메모</span></button>
												<?php }else{ ?>
													<button class="tdw_list_party_memo" id="tdw_list_party_memo" value="<?=$week_works_idx?>"><span>메모</span></button>
												<?php } ?>
											</div>
											<?}?>
										<?}?>
									</div>
									
									<?//첨부파일 정보
									//나의업무, 요청업무
									if(in_array($work_flag, array('1','2','3'))){
										if($tdf_files[$work_com_idx]['file_path']){?>
											<div class="tdw_list_file">
												<?for($k=0; $k<count($tdf_files[$work_com_idx]['file_path']); $k++){?>
													<div class="tdw_list_file_box">
														<button class="btn_list_file" id="btn_list_file_<?=$k?>" value="<?=$tdf_files[$work_com_idx]['idx'][$k]?>"><span><?=$tdf_files[$work_com_idx]['file_real_name'][$k]?></span></button>

														<?//보고업무 작성한 사용자만 삭제
														if($user_id==$tdf_files[$work_com_idx]['email'][$k]){?>
															<button class="btn_list_file_del" id="btn_list_fdel" value="<?=$tdf_files[$work_com_idx]['idx'][$k]?>" title="삭제"><span>삭제</span></button>
														<?}?>

													</div>
												<?}?>
											</div>
										<?}
									}?>

								</div>


								<div class="tdw_list_memo_area">
									<div class="tdw_list_memo_area_in<?=$memo_view_in?>" id="tdw_list_memo_area_in_<?=$week_works_idx?>">
										<?//댓글리스트
										//요청업무
										if($work_flag == '3'){?>
											<?if($comment_list[$work_com_idx]){?>
												<?for($k=0; $k<count($comment_list[$work_com_idx]['cidx']); $k++){
													$comment_idx = $comment_list[$work_com_idx]['cidx'][$k];

													$chis = $comment_list[$work_com_idx]['regdate'][$k];
													$ymd = $comment_list[$work_com_idx]['ymd'][$k];
													$cmt_flag = $comment_list[$work_com_idx]['cmt_flag'][$k];
													if($chis){
														$chis = str_replace("  "," ", $chis);
														$chis_tmp = @explode(" ", $chis);
														if ($chis_tmp['2'] == "PM"){
															$after = "오후 ";
														}else{
															$after = "오전 ";
														}
														$ctime = @explode(":", $chis_tmp['1']);
														$chiss = $ymd . " " . $after . $ctime['0'] .":". $ctime['1'];
													}

													$sql = "select a.name as name,a.comment as comment,a.send_name as send from work_todaywork_like a join work_todaywork_comment b";
													$sql = $sql." on a.com_idx = b.idx where a.state != 9 and a.com_idx = '".$comment_idx."'";
													$work_give_list = selectQuery($sql);

													if($work_give_list){
														$work_give_like_name = $work_give_list['name'];
														$work_give_like_comment = $work_give_list['comment'];
														$work_send_like_name = $work_give_list['send'];
													}

													// 코인보상 레이어 업무 요청한 사람만 보이게(김정훈)
													$sql = "select idx from work_todaywork where idx = '".$week_works_idx."' and work_idx is null";
													$work_link_coin = selectQuery($sql);

													$sql = "select a.idx from work_todaywork_like a join work_todaywork_comment b on a.work_idx = b.link_idx";
													$sql = $sql." where b.idx = '".$comment_idx."' and a.ai_like_idx = b.ai_like_idx and send_email = '".$user_id."'";
													$click_like = selectQuery($sql);

													$sql = "select a.idx from work_todaywork_like a join work_todaywork_comment b on a.work_idx = b.idx where b.idx = '".$comment_idx."'";
													$sql = $sql." and a.state = 0 and a.send_email = '".$user_id."'";
													$cli_like = selectQuery($sql);

													$sql = "select idx from work_todaywork_comment where idx = '".$comment_idx."' and email = '".$user_id."'";
													$my_like = selectQuery($sql);

													$sql = "select idx from work_todaywork_comment where idx = '".$comment_idx."' and like_email = '".$user_id."'";
													$my_coin_like = selectQuery($sql);

												?>

												<div class="tdw_list_memo_desc" id="comment_list_<?=$comment_idx?>" >
													<?if($cmt_flag == 1){?>
														<!-- 좋아요 변경으로 인한 코드 -->
														<?if($work_give_list){?>
															<div class="tdw_list_memo_name"><?=$work_send_like_name?></div>
														<?}else{?>
															<div class="tdw_list_memo_name ai">AI</div>
														<?}?>
													<?}else{?>
														<?if($cmt_flag != 2){?>
															<div class="tdw_list_memo_name"><?=$comment_list[$work_com_idx]['name'][$k]?></div>
														<?}?>
													<?}?>

													<!-- 좋아요 변경으로 인한 코드(김정훈) -->
													<?if($cmt_flag == 1){?>
														<?//좋아요 보낸 내역이 있을때
														if($work_give_list){?>
															<button class="btn_memo_jjim on" value="<?=$comment_idx?>"><span>좋아요</span></button>
														<?}?>
													<?}?>

													<div class="tdw_list_memo_conts">
														<?if($secret_flag == '1' && $week_works_email != $user_id){?>
															<?if($secret_flag == '1' && $user_id == $comment_list[$work_com_idx]['send'][$k]){?>
																<span class="tdw_list_memo_conts_txt"><?=textarea_replace($comment_list[$work_com_idx]['comment'][$k])?></span>
															<?}else{?>
																<img src = "/html/images/pre/ico_tr_l.png">비밀글 입니다.</span>
															<?}?>	
														<?}else{?>
															<?php if($comment_list[$week_works_idx]['secret_flag'][$k] == '1'){?>
																<?if((!$cmt_flag && $user_id==$comment_list[$work_com_idx]['send'][$k]) || (!$cmt_flag && $user_id==$comment_list[$work_com_idx]['email'][$k])){?>
																	<!-- 일반 메모 -->
																	<span class="tdw_list_memo_conts_txt" id="tdw_list_memo_conts_txt_<?=$comment_idx?>"><?=textarea_replace($comment_list[$work_com_idx]['comment'][$k])?></span>
																<?}else{?>
																	<?if($cmt_flag != 2){?>
																		<!-- AI 문장 -->
																		<span  class="tdw_list_memo_conts_txt"><img src = "/html/images/pre/ico_tr_l.png">비밀글 입니다.</span></span>
																	<?}?>
																<?}?>
															<?}else{?>
																<?if(!$cmt_flag && $user_id==$comment_list[$work_com_idx]['email'][$k]){?>
																	<span class="tdw_list_memo_conts_txt" id="tdw_list_memo_conts_txt_<?=$comment_idx?>"><?=textarea_replace($comment_list[$work_com_idx]['comment'][$k])?></span>
																<?}else{?>
																	<?if($cmt_flag != 2){?>
																		<span class="tdw_list_memo_conts_txt"><?=textarea_replace($comment_list[$work_com_idx]['comment'][$k])?></span>
																	<?}?>
																<?}?>
															<?}?>
														<?}?>

														<?if($cmt_flag != 2){?>
															<em class="tdw_list_memo_conts_date"><?=$chiss?>
														<?}?>
														<?//ai글 일때, 공유요청한 사람만 뜨게
														if($cmt_flag == 1 && $work_link_coin && !$my_coin_like){?>

														<?}?>

														<?//자동 ai댓글?>
														<?if($cmt_flag == 1){?>

														<?}else{?>
															<?if($cmt_flag != 2){?>
																<?if(!$my_like){?>
																	<?if($cli_like){?>
																		<button class="btn_memo_jjim on" value="<?=$comment_idx?>"><span>좋아요</span></button>
																	<?}else{?>
																		<button class="btn_memo_jjim" id="btn_memo_jjim_<?=$comment_idx?>" value="<?=$comment_idx?>"><span>좋아요</span></button>
																	<?}?>
																	<button class="btn_req_100c" id="btn_req_100c" title="100코인" value="<?=$comment_list[$work_com_idx]['cidx'][$k]?>"><span>100코인</span></button>
																	<input type="hidden" value="<?=$comment_list[$work_com_idx]['email'][$k]?>" id="comment_idx_<?=$comment_idx?>">
																<?}?>
															<?}?>
														<?}?>

														<?if(!$cmt_flag && $user_id==$comment_list[$work_com_idx]['email'][$k]){?>
															<button class="btn_memo_del" id="btn_memo_del" value="<?=$comment_idx?>"><span>삭제</span></button>
														<?}?>

														<?if($cmt_flag != 2){?>
															</em>
														<?}?>
														<div class="tdw_list_memo_regi" id="tdw_list_memo_regi_<?=$comment_idx?>">
															<textarea name="" class="textarea_regi" id="tdw_comment_edit_<?=$comment_idx?>"><?=$comment_list[$work_com_idx]['comment_strip'][$k]?></textarea>
															<div class="btn_regi_box">
																<button class="btn_regi_submit" id="btn_comment_submit" value="<?=$comment_idx?>"><span>확인</span></button>
																<button class="btn_regi_cancel" id="btn_regi_cancel" value="<?=$comment_idx?>"><span>취소</span></button>
															</div>
														</div>
													</div>
												</div>
											<?}?>
										<?}?>

										<?}else{?>
											<?//받은업무
											if ($work_idx){?>
												<?if($comment_list[$work_idx]){?>
													<?for($k=0; $k<count($comment_list[$work_idx]['cidx']); $k++){
														$comment_idx = $comment_list[$work_idx]['cidx'][$k];
														$chis = $comment_list[$work_idx]['regdate'][$k];
														$ymd = $comment_list[$work_idx]['ymd'][$k];
														$cmt_flag = $comment_list[$work_idx]['cmt_flag'][$k];

														if($chis){
															$chis = str_replace("  "," ", $chis);
															$chis_tmp = @explode(" ", $chis);
															if ($chis_tmp['2'] == "PM"){
																$after = "오후 ";
															}else{
																$after = "오전 ";
															}
															$ctime = @explode(":", $chis_tmp['1']);
															$chiss = $ymd . " " . $after . $ctime['0'] .":". $ctime['1'];
														}

														$sql = "select a.name as name,a.comment as comment,a.send_name as send from work_todaywork_like a join work_todaywork_comment b";
														$sql = $sql." on a.com_idx = b.idx where a.state != 9 and a.com_idx = '".$comment_idx."'";
														$work_give_list = selectQuery($sql);

														if($work_give_list){
															$work_give_like_name = $work_give_list['name'];
															$work_give_like_comment = $work_give_list['comment'];
															$work_send_like_name = $work_give_list['send'];
														}

														$sql = "select a.idx from work_todaywork_like a join work_todaywork_comment b on a.work_idx = b.idx where b.idx = '".$comment_idx."'";
														$sql = $sql." and a.state = 0 and a.send_email = '".$user_id."'";
														$cli_like = selectQuery($sql);
														?>

														<div class="tdw_list_memo_desc" id="comment_list_<?=$comment_idx?>" >

															<?if($cmt_flag == 1){?>
																<!-- 좋아요 변경으로 인한 코드 -->
																<?if($work_give_list){?>
																	<div class="tdw_list_memo_name"><?=$work_send_like_name?></div>
																<?}else{?>
																	<div class="tdw_list_memo_name ai">AI</div>
																<?}?>
															<?}else{?>
																<?if($cmt_flag !=2){?>
																	<div class="tdw_list_memo_name"><?=$comment_list[$work_idx]['name'][$k]?></div>
																<?}?>
															<?}?>

															<!-- 좋아요 변경으로 인한 코드(김정훈) -->
															<?if($cmt_flag == 1){?>
																<?//좋아요 보낸 내역이 있을때
																if($work_give_list){?>
																	<button class="btn_memo_jjim on" value="<?=$comment_idx?>"><span>좋아요</span></button>
																<?}?>
															<?}?>

															<div class="tdw_list_memo_conts">
																<?if($secret_flag == '1' && $week_works_email != $user_id){?>
																	<?if($secret_flag == '1' && $user_id == $comment_list[$work_idx]['send'][$k]){?>
																		<span class="tdw_list_memo_conts_txt"><?=textarea_replace($comment_list[$work_idx]['comment'][$k])?></span>
																	<?}else{?>
																		<img src = "/html/images/pre/ico_tr_l.png">비밀글 입니다.</span>
																	<?}?>	
																<?}else{?>
																	<?php if($comment_list[$week_works_idx]['secret_flag'][$k] == '1'){?>
																		<?if((!$cmt_flag && $user_id==$comment_list[$work_idx]['send'][$k]) || (!$cmt_flag && $user_id==$comment_list[$work_idx]['email'][$k])){?>
																			<!-- 일반 메모 -->
																			<span class="tdw_list_memo_conts_txt" id="tdw_list_memo_conts_txt_<?=$comment_idx?>"><?=textarea_replace($comment_list[$work_idx]['comment'][$k])?></span>
																		<?}else{?>
																			<?if($cmt_flag != 2){?>
																				<!-- AI 문장 -->
																				<span  class="tdw_list_memo_conts_txt"><img src = "/html/images/pre/ico_tr_l.png">비밀글 입니다.</span></span>
																			<?}?>
																		<?}?>
																	<?}else{?>
																		<?if(!$cmt_flag && $user_id==$comment_list[$work_idx]['email'][$k]){?>
																			<!-- 일반 메모 -->
																			<span class="tdw_list_memo_conts_txt" id="tdw_list_memo_conts_txt_<?=$comment_idx?>"><?=textarea_replace($comment_list[$work_idx]['comment'][$k])?></span>
																		<?}else if($cmt_flag == 1 && $work_give_list){?>
																			<!-- 좋아요 받았을 때 문장 -->
																			<span class="tdw_list_memo_conts_txt"><?=textarea_replace($comment_list[$work_idx]['comment'][$k])?></span>
																		<?}else{?>
																			<?if($cmt_flag != 2){?>
																				<!-- AI 문장 -->
																				<span class="tdw_list_memo_conts_txt"><?=textarea_replace($comment_list[$work_idx]['comment'][$k])?></span>
																			<?}?>
																		<?}?>
																	<?}?>
																<?}?>

																<?if($cmt_flag != 2){?>
																	<em class="tdw_list_memo_conts_date"><?=$chiss?>
																<?}?>

																<?//자동 ai댓글?>
																<?if($cmt_flag == 1){?>

																<?}else{?>
																	<?if($cmt_flag != 2){?>
																		<?if($user_id!=$comment_list[$work_idx]['email'][$k]){?>
																			<?if($cli_like){?>
																				<button class="btn_memo_jjim on" value="<?=$comment_idx?>"><span>좋아요</span></button>
																			<?}else{?>
																				<button class="btn_memo_jjim" id="btn_memo_jjim_<?=$comment_idx?>" value="<?=$comment_idx?>"><span>좋아요</span></button>
																			<?}?>
																			<button class="btn_req_100c" id="btn_req_100c" title="100코인" value="<?=$comment_list[$work_com_idx]['cidx'][$k]?>"><span>100코인</span></button>
																			<input type="hidden" value="<?=$comment_list[$work_com_idx]['email'][$k]?>" id="comment_idx_<?=$comment_idx?>">
																		<?}?>
																	<?}?>
																<?}?>

																<?if(!$cmt_flag && $user_id==$comment_list[$work_idx]['email'][$k]){?>
																	<button class="btn_memo_del" id="btn_memo_del" value="<?=$comment_idx?>"><span>삭제</span></button>
																<?}?>
																</em>

																<div class="tdw_list_memo_regi" id="tdw_list_memo_regi_<?=$comment_idx?>">
																	<textarea name="" class="textarea_regi" id="tdw_comment_edit_<?=$comment_idx?>"><?=$comment_list[$work_idx]['comment_strip'][$k]?></textarea>
																	<div class="btn_regi_box">
																		<button class="btn_regi_submit" id="btn_comment_submit" value="<?=$comment_idx?>"><span>확인</span></button>
																		<button class="btn_regi_cancel" id="btn_regi_cancel" value="<?=$comment_idx?>"><span>취소</span></button>
																	</div>
																</div>
															</div>
														</div>

													<?}?>

												<?}?>

											<?}else{?>
												<?
												//일반업무
												if($comment_list[$week_works_idx]){?>

													<?for($k=0; $k<count($comment_list[$week_works_idx]['cidx']); $k++){
														$comment_idx = $comment_list[$week_works_idx]['cidx'][$k];

														$chis = $comment_list[$week_works_idx]['regdate'][$k];
														$ymd = $comment_list[$week_works_idx]['ymd'][$k];
														$cmt_flag = $comment_list[$week_works_idx]['cmt_flag'][$k];

														if($chis){
															$chis = str_replace("  "," ", $chis);
															$chis_tmp = @explode(" ", $chis);
															if ($chis_tmp['2'] == "PM"){
																$after = "오후 ";
															}else{
																$after = "오전 ";
															}
															$ctime = @explode(":", $chis_tmp['1']);
															$chiss = $ymd . " " . $after . $ctime['0'] .":". $ctime['1'];
														}

														$sql = "select a.name as name,a.comment as comment,a.send_name as send from work_todaywork_like a join work_todaywork_comment b";
														$sql = $sql." on a.com_idx = b.idx where a.state != 9 and a.com_idx = '".$comment_idx."'";
														$work_give_list = selectQuery($sql);

														if($work_give_list){
															$work_give_like_name = $work_give_list['name'];
															$work_give_like_comment = $work_give_list['comment'];
															$work_send_like_name = $work_give_list['send'];
														}

														$sql = "select a.idx from work_todaywork_like a join work_todaywork_comment b on a.work_idx = b.idx where b.idx = '".$comment_idx."'";
														$sql = $sql." and a.state = 0 and a.send_email = '".$user_id."'";
														$cli_like = selectQuery($sql);

													?>

													<div class="tdw_list_memo_desc" id="comment_list_<?=$comment_idx?>" >

														<?if($cmt_flag == 1){?>
															<!-- 좋아요 변경으로 인한 코드 -->
															<?if($work_give_list){?>
																<div class="tdw_list_memo_name"><?=$work_send_like_name?></div>
															<?}else{?>
																<div class="tdw_list_memo_name ai">AI</div>
															<?}?>
														<?}else{?>
															<?if($cmt_flag != 2){?>
																<div class="tdw_list_memo_name"><?=$comment_list[$week_works_idx]['name'][$k]?></div>
															<?}?>
														<?}?>

														<!-- 좋아요 변경으로 인한 코드(김정훈) -->
														<?if($cmt_flag == 1){?>
															<?//좋아요 보낸 내역이 있을때
															if($work_give_list){?>
																<button class="btn_memo_jjim on" value="<?=$comment_idx?>"><span>좋아요</span></button>
															<?}?>
														<?}?>


														<div class="tdw_list_memo_conts">
															<?if($secret_flag == '1' && $week_works_email != $user_id){?>
																<?if($secret_flag == '1' && $user_id == $comment_list[$week_works_idx]['send'][$k]){?>
																	<span class="tdw_list_memo_conts_txt"><?=textarea_replace($comment_list[$week_works_idx]['comment'][$k])?></span>
																<?}else{?>
																	<img src = "/html/images/pre/ico_tr_l.png">비밀글 입니다.</span>
																<?}?>	
															<?}else{?>
																<?php if($comment_list[$week_works_idx]['secret_flag'][$k] == '1'){?>
																	<?if((!$cmt_flag && $user_id==$comment_list[$week_works_idx]['send'][$k]) || (!$cmt_flag && $user_id==$comment_list[$week_works_idx]['email'][$k])){?>
																		<!-- 일반 메모 -->
																		<span class="tdw_list_memo_conts_txt" id="tdw_list_memo_conts_txt_<?=$comment_idx?>"><?=textarea_replace($comment_list[$week_works_idx]['comment'][$k])?></span>
																	<?}else{?>
																		<?if($cmt_flag != 2){?>
																			<!-- AI 문장 -->
																			<span  class="tdw_list_memo_conts_txt"><img src = "/html/images/pre/ico_tr_l.png">비밀글 입니다.</span></span>
																		<?}?>
																	<?}?>
																<?}else{?>
																	<?if(!$cmt_flag && $user_id==$comment_list[$week_works_idx]['email'][$k]){?>
																		<!-- 일반 메모 -->
																		<span class="tdw_list_memo_conts_txt" id="tdw_list_memo_conts_txt_<?=$comment_idx?>"><?=textarea_replace($comment_list[$week_works_idx]['comment'][$k])?></span>
																	<?}else{?>
																		<?if($cmt_flag != 2){?>
																			<!-- AI 문장 -->
																			<span  class="tdw_list_memo_conts_txt"><?=textarea_replace($comment_list[$week_works_idx]['comment'][$k])?></span>
																		<?}?>
																	<?}?>
																<?}?>
															<?}?>

															<?if($cmt_flag != 2){?>
																<em class="tdw_list_memo_conts_date"><?=$chiss?>
															<?}?>

																<?//자동 ai댓글?>
																<?if($cmt_flag == 1){?>

																<?}else{?>
																	<?if($cmt_flag != 2){?>
																		<?if($user_id!=$comment_list[$week_works_idx]['email'][$k]){?>
																			<?if($cli_like){?>
																				<button class="btn_memo_jjim on" value="<?=$comment_idx?>"><span>좋아요</span></button>
																			<?}else{?>
																				<button class="btn_memo_jjim" id="btn_memo_jjim_<?=$comment_idx?>" value="<?=$comment_idx?>"><span>좋아요</span></button>
																			<?}?>
																			<button class="btn_req_100c" id="btn_req_100c" title="100코인" value="<?=$comment_list[$work_com_idx]['cidx'][$k]?>"><span>100코인</span></button>
																			<input type="hidden" value="<?=$comment_list[$work_com_idx]['email'][$k]?>" id="comment_idx_<?=$comment_idx?>">
																		<?}?>
																	<?}?>
																<?}?>

															<?if(!$cmt_flag && $user_id==$comment_list[$week_works_idx]['email'][$k]){?>
																<button class="btn_memo_del" id="btn_memo_del" value="<?=$comment_idx?>"><span>삭제</span></button>
															<?}?>

															<?if($cmt_flag != 2){?>
																</em>
															<?}?>

															<div class="tdw_list_memo_regi" id="tdw_list_memo_regi_<?=$comment_idx?>">
																<textarea name="" class="textarea_regi" id="tdw_comment_edit_<?=$comment_idx?>"><?=strip_tags($comment_list[$week_works_idx]['comment'][$k])?></textarea>
																<div class="btn_regi_box">
																	<button class="btn_regi_submit" id="btn_comment_submit" value="<?=$comment_idx?>"><span>확인</span></button>
																	<button class="btn_regi_cancel"><span>취소</span></button>
																</div>
															</div>
														</div>
													</div>
													<?}?>
												<?}?>
											<?}?>
										<?}?>
									</div>
								</div>

							</li>
						<?}?>
					<?}?>
				</ul>
			</div>
		<?php
		}
		echo "|".$work_all_cnt."|"?>
		<div class="rew_ard_paging_in">
			<?
				//페이징사이즈, 전체카운터, 페이지출력갯수
				echo pageing($pagingsize, $total_count, $pagesize, $string);
			?>
		</div>
	<?}else{?>
		<div class="tdw_list_search_none">
			<strong><span>파티에 연결된 업무가 없습니다.</span></strong>
		</div>
	<?php
	}

	exit;
}


//프로젝트 메모작성
if($mode == "project_comment"){

	$comment = $_POST['comment'];
	$work_idx = $_POST['work_idx']; //todaywork_idx
	$work_idx = preg_replace("/[^0-9]/", "", $work_idx);

	// 비밀 메모 작업
	$secret_flag = $_POST['secret_flag'];

	//홑따옴표 때문에 아래와 같이 처리
	$comment = replace_text($comment);

	//회원정보 추출
	$member_info = member_row_info($user_id);

	$sql = "select idx, work_idx, contents, work_flag, share_flag, email from work_todaywork where state!='9' and companyno='".$companyno."' and idx='".$work_idx."'";
	$work_info = selectQuery($sql);
	if($work_info['idx']){

		$work_flag = $work_info['work_flag'];
		//요청된 업무가 있을경우
		if($work_info['work_idx']){
			$link_idx = $work_info['work_idx'];
		}else{
			$link_idx = $work_info['idx'];
		}

		$contents = $work_info['contents'];

		//오늘업무 작성한 사용자 확인
		$sql = "select idx, email, name from work_todaywork where state='0' and companyno='".$companyno."' and idx='".$work_info['idx']."'";
		$work_real_info = selectQuery($sql);

		//메모작성
		$sql = "insert into work_todaywork_comment(link_idx, work_idx, companyno, email, name, part, partno, comment, type_flag, secret_flag, workdate, ip) values";
		$sql = $sql .= "('".$link_idx."','".$work_info['idx']."','".$companyno."','".$user_id."','".$user_name."','".$member_info['part']."','".$member_info['partno']."','".$comment."','".$type_flag."','".$secret_flag."','".TODATE."','".LIP."')";
		//echo $sql;

		$res_idx = insertIdxQuery($sql);
		if($res_idx){
			//보고업무 메모작성
			// if($work_flag=='1'){
			// 	//역량평가지표(보고업무 메모작성)

			// }else if($work_flag=='3'){
			// 	//요청업무 메모작성
			// 	//역량평가지표(업무요청 메모작성)
			// }else{

			// 	if($work_info['share_flag'] == '2'){

			// 		//역량평가지표(업무공유 메모작성)

			// 	}else{

			// 		//역량평가지표(타인 오늘업무 메모작성)
			// 		if($work_real_info['email'] != $user_id){ // work_real_info = 공유,요청한 사람 user_id = 나
			// 		}
			// 	}
			// }
			
			if($work_info['email']!=$user_id){
				if($work_real_info['idx']){
					$tokenTitle = $user_name."님이 메모를 남겼어요";
					$tokenInfo = $comment;
					$tokenUser = $work_real_info['email'];

					pushToken($tokenTitle,$tokenInfo,$tokenUser,'memo','37',$user_id,$user_name,$work_real_info['idx'],$contents,'party');
				}
			}

			echo "complete|";

			$time = date("H:i",time());

			if ($time > '12:00'){
				$com_time = @explode(":",$time);
				if($time > '13:00'){
					$com_his_tmp_h = $com_time[0]-'12';
				}else{
					$com_his_tmp_h = $com_time[0];
				}
				
				$com_his_tmp_h = $com_his_tmp_h .":". $com_time[1];
				$after = "오후 ";
			}else{
				$com_his_tmp_h = $time;
				$after = "오전 ";
			}
			?>
				<div class="tdw_list_memo_desc" id="comment_list_<?=$res_idx?>" >
					<div class="tdw_list_memo_name"><?=$user_name?></div>
					<div class="tdw_list_memo_conts">
						<span class="tdw_list_memo_conts_txt"><?=$comment?></span>
						<em class="tdw_list_memo_conts_date"><?=TODATE." ".$after." ".$com_his_tmp_h?>
						<button class="btn_memo_del" id="btn_memo_del" value="<?=$res_idx?>"><span>삭제</span></button>
					</div>
				</div>
			<?
			
			exit;

		}else{

			echo "not";
			exit;

		}
	}
	exit;
}


//파티에서 나가기
if($mode == "party_out"){

	$party_idx = $_POST['party_idx'];
	$party_idx = preg_replace("/[^0-9]/", "", $party_idx);
	if($party_idx){

		//파티정보 검색
		$sql = "select idx, work_idx from work_todaywork_project_info where state='0' and companyno='".$companyno."' and party_idx='".$party_idx."' and mem_email='".$user_id."'";
		$info = selectQuery($sql);

		echo $sql;
		echo "\n\n";
		if($info['idx']){

			//파티정보 업데이트
			$sql = "update work_todaywork_project_info set state='9', editdate=".DBDATE." where idx='".$info['idx']."'";

			echo $sql;
			echo "\n\n";
			$res = updateQuery($sql);
			if($res){

				//오늘업무에서 파티로 연결된 업무 업데이트
				$sql = "update work_todaywork set party_link=null where idx='".$info['work_idx']."'";

				echo $sql;
				echo "\n\n";

				$work_res = updateQuery($sql);

				//파티의 구성원 제거(업데이트)
				$sql = "select idx, work_idx from work_todaywork_project_user where state='0' and companyno='".$companyno."' and project_idx='".$party_idx."' and email='".$user_id."'";
				$user_info = selectQuery($sql);
				if($user_info['idx']){
					$sql = "update work_todaywork_project_user set state='9', editdate=".DBDATE." where idx='".$user_info['work_idx']."'";
					echo $sql;
					echo "\n\n";
					$user_res = updateQuery($sql);
				}

				if($work_res && $user_res){
					echo "complete";
					exit;
				}
			}
		}
	}else{
		echo "party_not";
		exit;
	}
}


//파티구성원
if($mode=="project_user_layer"){

	$party_idx = $_POST['party_idx'];
	$party_idx = preg_replace("/[^0-9]/", "", $party_idx);
	if($party_idx){

		$sql = "select idx, project_idx, email from work_todaywork_project_user where state='0' and companyno='".$companyno."' and project_idx='".$party_idx."'";
		$user_info = selectAllQuery($sql);
		if($user_info['idx']){
			$user_info_uid = @implode("','",$user_info['email']);
			$party_uid = "'".$user_info_uid."'";
			$result_uid = member_list_userid($party_uid);
			echo $result_uid;
			exit;
		}
	}
}


//파티구성원변경
if($mode=='project_user_edit'){
	
	$chall_user_chk = $_POST['chall_user_chk'];
	$chall_user_chk = trim($chall_user_chk);
		
	$party_idx = $_POST['party_idx'];
	$party_idx = preg_replace("/[^0-9]/", "", $party_idx);
	if($party_idx){
		$member_list_user_id = member_list_useridx($chall_user_chk);
		//구성원 조회후 삭제처리
		
		//파티 생성자 조회
		$sql = "select idx, email from work_todaywork_project where state='0' and companyno='".$companyno."' and idx='".$party_idx."'";
		
		$party_info = selectQuery($sql);
		$party_info_email = $party_info['email'];

		//파티장 아이디 배열에 추가
		@array_push($member_list_user_id['0'] , $party_info_email);
		$member_list_user_arr = @implode("','", $member_list_user_id['0']);

		$sql = "select idx from work_todaywork_project_user where state='0' and project_idx='".$party_idx."' and companyno='".$companyno."' and email not in('".$member_list_user_arr."')";
		$project_user_info = selectAllQuery($sql);
		if($project_user_info['idx']){
			$user_info_idx = implode("','", $project_user_info['idx']);
			$sql =  "update work_todaywork_project_user set state='9', editdate=".DBDATE." where idx in ('".$user_info_idx."')";
			$user_up = updateQuery($sql);
		}

		//구성원 추가
		$sql = "select idx, email, name, part, partno, highlevel from work_member where state='0' and companyno='".$companyno."' and email in('".$member_list_user_arr."') order by idx asc";
		$party_user_id_list = selectAllQuery($sql);
		
		for($i=0; $i<count($party_user_id_list['idx']); $i++){
			$party_user_email = $party_user_id_list['email'][$i];
			$party_user_name = $party_user_id_list['name'][$i];
			$party_user_part = $party_user_id_list['part'][$i];
			$party_user_partno = $party_user_id_list['partno'][$i];
			
			$sql = "select idx from work_todaywork_project_user where state='0' and project_idx='".$party_idx."' and companyno='".$companyno."' and email='".$party_user_email."'";
			$project_user_new_info = selectQuery($sql);
			if(!$project_user_new_info['idx']){

				$sql = "insert into work_todaywork_project_user(project_idx, companyno, part_flag, email, name, part, ip) values(";
				$sql = $sql .= "'".$party_idx."','".$companyno."','".$party_user_partno."','".$party_user_email."','".$party_user_name."','".$party_user_part."','".LIP."')";
				$insert_idx = insertIdxQuery($sql);

			}

		}

		//업데이트 되었을때
		if($insert_idx || $user_up){
			echo "complete";
			exit;
		}

	}
}

if($mode == "project_title_update"){
	$pro_idx = $_POST['project_idx'];
	$title = addslashes($_POST['title']);

	$sql = "update work_todaywork_project set title = '".$title."' where idx = '".$pro_idx."' and companyno = '".$companyno."' and state !=9 ";
	$res = updateQuery($sql);

	$sql2 = "update work_todaywork_project_info set party_title = '".$title."' where party_idx = '".$pro_idx."' and companyno = '".$companyno."' and state !='9'  ";
	$res2 = updateQuery($sql2);

	echo "update|".$title."|"
	?>
		<p id="party_title_edit" class="party_title_edit"><span>✏️</span><strong class="party_title_text"><?=textarea_replace(stripslashes($title))?></strong></p>
		<input type="hidden" value="<?=$user_id?>">
		<input type="hidden" value="<?=$party_idx?>">
		<div class="tdw_list_regi" id="tdw_list_regi_edit_<?=$idx?>" >
			<!-- <strong>수정중</strong> -->
			<textarea class="textarea_regi" id="textarea_regi_<?=$idx?>"><?=strip_tags($title)?></textarea>
			<div class="btn_regi_box">
				<button class="btn_regi_submit" id="btn_regi_submit" value="<?=$party_idx?>"><span>확인</span></button>
				<button class="btn_regi_cancel"><span>취소</span></button>
			</div>
		</div>
	<?exit;
}


if($mode == "project_like"){
	$pro_idx = $_POST["pro_idx"];

	$sql = "select idx,state from work_project_like where project_idx = ".$pro_idx." and companyno = '".$companyno."' and email = '".$user_id."' and state != 9";
	$pro_like_che = selectQuery($sql);

	if($pro_like_che){
		$pro_like_idx = $pro_like_che['idx'];
		$pro_like_state = $pro_like_che['state'];

		if($pro_like_state == 0){
			$sql = "update work_project_like set state = 1 where idx = '".$pro_like_idx."'";
			$res = updateQuery($sql);
		}else if($pro_like_state == 1){
			$sql = "update work_project_like set state = 0 where idx = '".$pro_like_idx."'";
			$res = updateQuery($sql);
		}

		if($res){
			$sql = "select state from work_project_like where idx = '".$pro_like_idx."'";
			$on_off_rs = selectQuery($sql);

			if($on_off_rs){
				$on_off = $on_off_rs['state'];

				echo "update|".$on_off;
				exit;
			}
		}

	}else{
		$sql = "insert into work_project_like (state, project_idx, companyno, email, name, ip, regdate) values (1,'".$pro_idx."','".$companyno."','".$user_id."','".$user_name."','".LIP."',".DBDATE.")";
		$insert_idx = insertIdxQuery($sql);

		if($insert_idx){
			echo "insert|";
			exit;
		}
	}
}
?>