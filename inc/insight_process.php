<?php
$home_dir = str_replace( basename(__DIR__) , "" , __DIR__ );
//연결된 도메인으로 분리
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

$mode = $_POST["mode"];

if($_COOKIE){
	$user_id = $_COOKIE['user_id'];
	$user_name = $_COOKIE['user_name'];
	$user_level = $_COOKIE['user_level'];
	$user_part = $_COOKIE['user_part'];
	$part_name = $_COOKIE['part_name'];
}

if($mode == "rank_list"){

  $wdate = $_POST['wdate'];
  $rank_type = $_POST['rank_type'];
    //날짜변환

  if($wdate){
    $wdate = str_replace(".","-",$wdate);
  }
  $ywdate = date("Y-m-d", strtotime($wdate." -1 day"));

  if($rank_type == 'c_day'){
    $where = "and workdate = '".$wdate."'";
    $where1 = "and workdate = '".$ywdate."'";
    $where_work = "and (b.workdate = '".$wdate."' or c.start_date = '".$wdate."')";
    $where_work1 = "and b.workdate = '".$wdate."'";
  }else if($rank_type == 'c_week'){
    if(strpos($wdate, "~") !== false) {
      $wdate = trim($wdate);
      $tmp = explode("~", $wdate);
      $monthday = trim($tmp['0']);
      $sunday = trim($tmp['1']);
      $month = strtotime($monthday);
    }else{
      $date_tmp = explode("-",$wdate);
      $year = $date_tmp[0];
      $month = $date_tmp[1];
      $day = $date_tmp[2];
      $ret = week_day("$year-$month-$day");
      if($ret){
  
        //월요일
        $monthday = $ret['month'];
  
        //금요일
        $friday = $ret['friday'];
  
        //일요일
        $sunday = $ret['sunday'];
  
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
    $where = "and workdate between '".$monthday."' and '".$sunday."'";
    $where1 = "and workdate between '".$monthday."' and '".$sunday."'";
    $where_work = "and (b.workdate between '".$monthday."' and '".$sunday."' or c.start_date between '".$monthday."' and '".$sunday."')";
    $where_work1 = "and b.workdate between '".$monthday."' and '".$sunday."'";
  }else if($rank_type == 'c_month'){
    $date_tmp = explode(".",$wdate);

		if($date_tmp){
			$year = $date_tmp[0];
			$month = $date_tmp[1];
			if(!$date_tmp[2]){
				$date_tmp[2] = "01";
			}
			$day = $date_tmp[2];
		}

		$sel_day = date("t",mktime(0, 0, 0, $month, $day, $year));				// 지정된 달은 몇일까지 있을까요?
		$sel_yoil = date("N",mktime(0, 0, 0, $month, 1, $year));				// 지정된 달의 첫날은 무슨요일일까요?
		$day_line = $sel_yoil%7;												// 지정된 달 1일 앞의 공백 숫자.
		$ra = ($sel_day + $day_line)/7;
		$ra = ceil($ra);
		$ra = $ra-1;															// 지정된 달은 총 몇주로 라인을 그어야 하나?

		//월간업무
		$ym = $year."-".$month;
    
    $where = "and DATE_FORMAT(workdate, '%Y-%m') = '".$ym."'";
    $where1 = "and DATE_FORMAT(workdate, '%Y-%m') = '".$ym."'";
    $where_work = "and (DATE_FORMAT(b.workdate, '%Y-%m') = '".$ym."' or DATE_FORMAT(c.start_date, '%Y-%m') = '".$ym."')";
    $where_work1 = "and DATE_FORMAT(b.workdate, '%Y-%m') = '".$ym."'";
  }

 

  $sql = "select 
	b.email as email,
  count(case when (b.work_flag = '2' and b.share_flag = '0' and b.notice_flag = '0') then 1 end) as work,
  count(case when (b.work_flag = '1' and b.work_idx is null) then 1 end) as report_to,
  count(case when (b.work_flag = '1' and b.work_idx is not null) then 1 end) as report_from,
  count(case when (b.work_flag = '3'and b.work_idx is null) then 1 end) as request_to,
  count(case when (b.work_flag = '3'and b.work_idx is not null) then 1 end) as request_from,
  count(case when b.share_flag = '1' then 1 end) as share_to,
  count(case when b.share_flag = '2' then 1 end) as share_from,
    (select count(*) from work_todaywork_comment where email = '".$user_id."' and companyno = '".$companyno."'  and comment is not null $where) as comment,
    a.coin as coin,
    a.comcoin as comcoin,
      (select count(*) from work_todaywork_like where email = '".$user_id."' and companyno = '".$companyno."' and comment is not null $where) as great_to,
      (select count(*) from work_todaywork_like where send_email = '".$user_id."' and companyno = '".$companyno."' and comment is not null $where) as great_from,
      (SELECT count(idx) FROM work_coininfo where email = '".$user_id."' and code in ('500','600','700','900','1000','1100')  and companyno = '".$companyno."' $where) as reward_to,
      (SELECT count(idx) FROM work_coininfo where email = '".$user_id."' and code not in ('500','600','700','900','1000','1100')  and companyno = '".$companyno."' $where) as reward_from
    from
    work_todaywork b 
    left join work_member a on b.email = a.email
  	left join calendar_events c on b.idx = c.work_idx
    where 1=1
    and a.email = '".$user_id."'
    and a.state = '0'
    and b.state != '9'
    and (c.state != '9' or c.state is null)
	  $where_work
    and b.companyno = '".$companyno."'";
  var_dump($sql);
	$new_chart = selectQuery($sql);

	$sql = "select 
    b.email as email,
    floor(COUNT(CASE WHEN (b.work_flag = '2' and b.share_flag = '0' and b.notice_flag = '0') THEN 1 END) / 
    count(DISTINCT CASE WHEN (b.work_flag = '2' and b.share_flag = '0' and b.notice_flag = '0') THEN b.email END)) as work,
    floor(count(case when (b.work_flag = '1' and b.work_idx is null) then 1 end) / 
    count(distinct case when (b.work_flag = '1' and b.work_idx is null) then b.email end)) as report_to,
    floor(count(case when (b.work_flag = '1' and b.work_idx is not null) then 1 end) / 
    count(distinct case when (b.work_flag = '1' and b.work_idx is not null) then b.email end)) as report_from,
    floor(count(case when (b.work_flag = '3' and b.work_idx is null) then 1 end) / 
    count(distinct case when (b.work_flag = '3' and b.work_idx is null) then b.email end)) as request_to,
    floor(count(case when (b.work_flag = '3' and b.work_idx is not null) then 1 end) / 
    count(distinct case when (b.work_flag = '3' and b.work_idx is not null) then b.email end)) as request_from,
    floor(count(case when b.share_flag = '1' then 1 end) / 
    count(distinct case when b.share_flag = '1' then b.email end)) as share_to,
    floor(count(case when b.share_flag = '2' then 1 end) / 
    count(distinct case when b.share_flag = '2' then b.email end)) as share_from,
    (select floor(count(idx) / count(distinct(email))) from work_todaywork_comment where companyno = '".$companyno."'  and comment is not null $where) as comment,
    (select sum(coin) from work_member where state = '0' and companyno = '1') as coin,
    (select sum(comcoin) from work_member where state = '0' and companyno = '1') as comcoin,
    (select floor(count(idx) / count(distinct(email))) from work_todaywork_like where companyno = '".$companyno."' and comment is not null $where) as great_to,
    (select floor(count(idx) / count(distinct(send_email))) from work_todaywork_like where companyno = '".$companyno."' and comment is not null $where) as great_from,
    (SELECT floor(count(idx) / count(distinct(email))) FROM work_coininfo where code in ('500','600','700','900','1000','1100') and companyno = '".$companyno."' $where) as reward_to,
    (SELECT floor(count(idx) / count(distinct(email))) FROM work_coininfo where code not in ('500','600','700','900','1000','1100') and companyno = '".$companyno."' $where) as reward_from
    from
    work_todaywork b 
    left join work_member a on b.email = a.email
    where 1=1
    and a.state = '0'
    and b.state != '9'
    $where_work1
    and b.companyno = '".$companyno."'";
	  $new_chart_all = selectQuery($sql);
	$sql = "select DATE_FORMAT(
		SEC_TO_TIME(
			AVG(TIME_TO_SEC(STR_TO_DATE(worktime, '%H:%i')))
		),
		'%H:%i'
	) as work_in from work_member_login where companyno = '".$companyno."' $where";
	$avg_work_in_all = selectQuery($sql);
	$sql = "select worktime
	as work_in from work_member_login where companyno = '".$companyno."' and email = '".$user_id."' $where";
	$avg_work_in = selectQuery($sql);

	$sql = "select DATE_FORMAT(
		SEC_TO_TIME(
			AVG(TIME_TO_SEC(STR_TO_DATE(date_format(regdate, '%H:%i'), '%H:%i')))
		),
		'%H:%i'
	) as work_out FROM work_member_logoff where companyno = '".$companyno."' $where1";
  $avg_work_out_all = selectQuery($sql);
	$sql = "select date_format(regdate, '%H:%i')
	as work_out from work_member_logoff where companyno = '".$companyno."' and email = '".$user_id."' $where1";
	$avg_work_out = selectQuery($sql);


	// $hour1과 $minute1 변수에 시간과 분 값이 할당됩니다

	// ':'를 구분자로 사용하여 시간 및 분 추출
	list($hour1, $minute1) = explode(':', $avg_work_in_all['work_in']); // 9:38
	list($hour2, $minute2) = explode(':', $avg_work_out_all['work_out']); // 6:31
	list($hour3, $minute3) = explode(':', $avg_work_in['work_in']);
	list($hour4, $minute4) = explode(':', $avg_work_out['work_out']);
	
	// 시간 및 분을 정수로 변환
	$hour1 = (int)$hour1;
	$minute1 = (int)$minute1;
	$hour2 = (int)$hour2;
	$minute2 = (int)$minute2;
	$hour3 = (int)$hour3;
	$minute3 = (int)$minute3;
	$hour4 = (int)$hour4;
	$minute4 = (int)$minute4;
  
	// 두 시간 간의 차이 계산
	$hour_difference_all = $hour2 - $hour1;
	$minute_difference_all = $minute2 - $minute1;
	$hour_difference = $hour4 - $hour3;
	$minute_difference = $minute4 - $minute3;
	if ($hour_difference_all < 0 ) {
		$hour_difference_all += 24; // 음수 시간 차이 보정
	}else if($hour_difference < 0){
    $hour_difference += 24;
  }

  // 분 차이 보정
if ($minute_difference_all < 0) {
  $minute_difference_all += 60;
  $hour_difference_all -= 1; // 음수 시간 차이 보정
}else if($minute_difference < 0){
  $minute_difference += 60;
  $hour_difference -= 1; // 음수 시간 차이 보정
}


  if($rank_type == "c_day" || $rank_type == "c_week" || $rank_type == "c_month"){

?>
    <div class="ins_rank_graph">
      <div class="ins_rank_graph_in">
      <ul style = "padding-top : 20px;">
      <li class="ir_rank">
          <div class="ir_name_user"><?=$avg_work_in['work_in']?></div>
          <div class="ir_name_team">출근 시간</div>
      </li>
      <li class="ir_rank">
          <div class="ir_name_user"><?=$avg_work_out['work_out']?$avg_work_out['work_out']:"없음"?></div>
          <div class="ir_name_team">퇴근 시간</div>
      </li>
      <li class="ir_rank">
          <div class="ir_name_user"><?=$avg_work_out['work_out']?$hour_difference.".".$minute_difference."시간":"없음"?></div>
          <div class="ir_name_team">근무 시간</div>
      </li>
      <li class="ir_rank">
          <div class="ir_name_user"><?=$new_chart['work']?></div>
          <div class="ir_name_team">업무</div>
      </li>
      <li class="ir_rank">
          <div class="ir_name_user"><?=$new_chart['report_to']." | ".$new_chart['report_from']?></div>
          <div class="ir_name_team">보고</div>
      </li>
      <li class="ir_rank">
          <div class="ir_name_user"><?=$new_chart['request_to']." | ".$new_chart['request_from']?></div>
          <div class="ir_name_team">요청</div>
      </li>
      <li class="ir_rank">
          <div class="ir_name_user"><?=$new_chart['share_to']." | ".$new_chart['share_from']?></div>
          <div class="ir_name_team">공유</div>
      </li>
      <li class="ir_rank">
          <div class="ir_name_user"><?=$new_chart['comment']?></div>
          <div class="ir_name_team">댓글</div>
      </li>
      <li class="ir_rank">
          <div class="ir_name_user"><?=number_format($new_chart['coin'])?> 코인</div>
          <div class="ir_name_team">내 코인</div>
      </li>
      <li class="ir_rank">
          <div class="ir_name_user"><?=number_format($new_chart['comcoin'])?> 코인</div>
          <div class="ir_name_team">공용 코인</div>
      </li>
      <li class="ir_rank">
          <div class="ir_name_user"><?=$new_chart['great_to']." | ".$new_chart['great_from']?></div>
          <div class="ir_name_team">좋아요</div>
      </li>
      <li class="ir_rank">
          <div class="ir_name_user"><?=$new_chart['reward_to']." | ".$new_chart['reward_from']?>회</div>
          <div class="ir_name_team">보상지급</div>
      </li>
      </ul>
      <ul style = "padding-top : 100px;">
      <li class="ir_rank">
          <div class="ir_name_user"><?=$avg_work_in_all['work_in']?></div>
          <div class="ir_name_team">출근 시간</div>
      </li>
      <li class="ir_rank">
          <div class="ir_name_user"><?=$avg_work_out_all['work_out']?$avg_work_out_all['work_out']:"없음"?></div>
          <div class="ir_name_team">퇴근 시간</div>
      </li>
      <li class="ir_rank">
          <div class="ir_name_user"><?=$hour_difference_all.".".$minute_difference_all?>시간</div>
          <div class="ir_name_team">근무 시간</div>
      </li>
      <li class="ir_rank">
          <div class="ir_name_user"><?=$new_chart_all['work']?$new_chart_all['work']:"0"?></div>
          <div class="ir_name_team">업무</div>
      </li>
      <li class="ir_rank">
          <div class="ir_name_user"><?=$new_chart_all['report_to']?$new_chart_all['report_to']:"0"?> | <?=$new_chart_all['report_from']?$new_chart_all['report_from']:"0"?></div>
          <div class="ir_name_team">보고</div>
      </li>
      <li class="ir_rank">
          <div class="ir_name_user"><?=$new_chart_all['request_to']?$new_chart_all['request_to']:"0"?> | <?=$new_chart_all['request_from']?$new_chart_all['request_from']:"0"?></div>
          <div class="ir_name_team">요청</div>
      </li>
      <li class="ir_rank">
          <div class="ir_name_user"><?=$new_chart_all['share_to']?$new_chart_all['share_to']:"0"?> | <?=$new_chart_all['share_from']?$new_chart_all['share_from']:"0"?></div>
          <div class="ir_name_team">공유</div>
      </li>
      <li class="ir_rank">
          <div class="ir_name_user"><?=$new_chart_all['comment']?$new_chart_all['comment']:"0"?></div>
          <div class="ir_name_team">댓글</div>
      </li>
      <li class="ir_rank">
          <div class="ir_name_user"><?=number_format($new_chart_all['coin'])?> 코인</div>
          <div class="ir_name_team">내 코인</div>
      </li>
      <li class="ir_rank">
          <div class="ir_name_user"><?=number_format($new_chart_all['comcoin'])?> 코인</div>
          <div class="ir_name_team">공용 코인</div>
      </li>
      <li class="ir_rank">
          <div class="ir_name_user"><?=$new_chart_all['great_to']?$new_chart_all['great_to']:"0"?> | <?=$new_chart_all['great_from']?$new_chart_all['great_from']:"0"?></div>
          <div class="ir_name_team">좋아요</div>
      </li>
      <li class="ir_rank">
          <div class="ir_name_user"><?=$new_chart_all['reward_to']?$new_chart_all['reward_to']:"0"?> | <?=$new_chart_all['reward_from']?$new_chart_all['reward_from']:"0"?>회</div>
          <div class="ir_name_team">보상 지급</div>
      </li>
      </ul>
      </div>
    </div>

    <?
  }
  else if($rank_type == "l_day"){

    ?>
    <div class="ins_rank_top">
      <ul>
        <?
					$sql = "select email, count(email) as sum from work_todaywork_like";
					$sql = $sql .= " where workdate = '".$wdate."' and state = 0";
					$sql = $sql .= " and companyno = '".$companyno."' group by email order by sum desc limit 3";
          $rank = selectAllQuery($sql);

          if($rank){
            for($i=0; $i<count($rank[email]); $i++){
              $r_email = $rank[email][$i];
              $r_sum = $rank[sum][$i];
              $r_rank = $rank[rank][$i];

          $rank_img_src = profile_img_info($r_email);

          $sql = "select name,part from work_member where email = '".$r_email."' and state = 0 and companyno = '".$companyno."'";
          $r_mem = selectQuery($sql);

          if($r_mem){
            $r_part = $r_mem[part];
            $r_name = $r_mem[name];
          }
        ?>
          <li class="ir_rank_<?=$i+1?>">
            <div class="ins_rank_user">
              <div class="ir_user_img">
                <div class="ir_user_img_in" style="background-image:url('<?=$rank_img_src?>');"></div>
              </div>
              <div class="ir_name">
                <div class="ir_name_user"><?=$r_name?></div>
                <div class="ir_name_team"><?=$r_part?></div>
              </div>
              <div class="ir_rank_mark">
                <span><?=$i+1?>위</span>
              </div>
            </div>
            <div class="ir_user_heart"><!-- 좋아요 : ir_user_heart -->
              <span><?=number_format($r_sum)?></span>
            </div>
          </li>
        <?}?>
       <?}?>
      </ul>
    </div>

    <div class="ins_rank_graph">
      <div class="ins_rank_graph_in">
        <ul>
          <?
						$sql = "select email, count(email) as sum from work_todaywork_like";
						$sql = $sql .= " where workdate = '".$wdate."' and state = 0";
						$sql = $sql .= " and companyno = '".$companyno."' group by email order by sum desc limit 3,7";

            $rank = selectAllQuery($sql);

            if($rank){
              for($i=0; $i<count($rank[email]); $i++){
                $r_email = $rank[email][$i];
                $r_sum = $rank[sum][$i];
                $r_rank = $rank[rank][$i];

            $rank_img_src = profile_img_info($r_email);

            $sql = "select name,part from work_member where email = '".$r_email."' and state = 0 and companyno = '".$companyno."'";
            $r_mem = selectQuery($sql);

            if($r_mem){
              $r_part = $r_mem[part];
              $r_name = $r_mem[name];
            }
          ?>
            <li class="ir_rank_<?=$i+4?>">
              <div class="ins_rank_bar">
                <div class="ir_bar_heart"><!-- 좋아요 : ir_bar_heart -->
                  <span><?=number_format($r_sum)?></span>
                </div>
                <div class="ir_bar_rank">
                  <span><?=$i+4?></span>
                </div>
                <div class="ir_bar_graph"><span></span></div>
              </div>
              <div class="ins_rank_user">
                <div class="ir_user_img">
                  <div class="ir_user_img_in" style="background-image:url('<?=$rank_img_src?>');"></div>
                </div>
                <div class="ir_name">
                  <div class="ir_name_user"><?=$r_name?></div>
                  <div class="ir_name_team"><?=$r_part?></div>
                </div>
              </div>
            </li>
          <?}?>
         <?}?>

        </ul>
      </div>
    </div>

      <?
  } else if($rank_type == "l_week"){

    if(strpos($wdate, "~") !== false) {
      $wdate = trim($wdate);
      $tmp = explode("~", $wdate);
      $monthday = trim($tmp['0']);
      $sunday = trim($tmp['1']);
      $month = strtotime($monthday);
    }else{

      $date_tmp = explode("-",$wdate);
      $year = $date_tmp[0];
      $month = $date_tmp[1];
      $day = $date_tmp[2];
      $ret = week_day("$year-$month-$day");
      if($ret){

        //월요일
        $monthday = $ret['month'];

        //금요일
        $friday = $ret['friday'];

        //일요일
        $sunday = $ret['sunday'];

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
    echo "date!!".$wdate;
    ?>
    <div class="ins_rank_top">
      <ul>
        <?
					$sql = "select email, count(email) as sum from work_todaywork_like";
					$sql = $sql .= " where workdate between '".$monthday."' and '".$sunday."' and state = 0";
					$sql = $sql .= " and companyno = '".$companyno."' group by email order by sum desc limit 3";
          $rank = selectAllQuery($sql);

          if($rank){
            for($i=0; $i<count($rank[email]); $i++){
              $r_email = $rank[email][$i];
              $r_sum = $rank[sum][$i];
              $r_rank = $rank[rank][$i];

          $rank_img_src = profile_img_info($r_email);

          $sql = "select name,part from work_member where email = '".$r_email."' and state = 0 and companyno = '".$companyno."'";
          $r_mem = selectQuery($sql);

          if($r_mem){
            $r_part = $r_mem[part];
            $r_name = $r_mem[name];
          }
        ?>
          <li class="ir_rank_<?=$i+1?>">
            <div class="ins_rank_user">
              <div class="ir_user_img">
                <div class="ir_user_img_in" style="background-image:url('<?=$rank_img_src?>');"></div>
              </div>
              <div class="ir_name">
                <div class="ir_name_user"><?=$r_name?></div>
                <div class="ir_name_team"><?=$r_part?></div>
              </div>
              <div class="ir_rank_mark">
                <span><?=$i+1?>위</span>
              </div>
            </div>
            <div class="ir_user_heart"><!-- 좋아요 : ir_user_heart -->
              <span><?=number_format($r_sum)?></span>
            </div>
          </li>
        <?}?>
       <?}?>
      </ul>
    </div>

    <div class="ins_rank_graph">
      <div class="ins_rank_graph_in">
        <ul>
          <?
						$sql = "select email, count(email) as sum from work_todaywork_like";
						$sql = $sql .= " where workdate between '".$monthday."' and '".$sunday."' and state = 0";
						$sql = $sql .= " and companyno = '".$companyno."' group by email order by sum desc limit 3,7";

            $rank = selectAllQuery($sql);

            if($rank){
              for($i=0; $i<count($rank[email]); $i++){
                $r_email = $rank[email][$i];
                $r_sum = $rank[sum][$i];
                $r_rank = $rank[rank][$i];

            $rank_img_src = profile_img_info($r_email);

            $sql = "select name,part from work_member where email = '".$r_email."' and state = 0 and companyno = '".$companyno."'";
            $r_mem = selectQuery($sql);

            if($r_mem){
              $r_part = $r_mem[part];
              $r_name = $r_mem[name];
            }
          ?>
            <li class="ir_rank_<?=$i+4?>">
              <div class="ins_rank_bar">
                <div class="ir_bar_heart"><!-- 좋아요 : ir_bar_heart -->
                  <span><?=number_format($r_sum)?></span>
                </div>
                <div class="ir_bar_rank">
                  <span><?=$i+4?></span>
                </div>
                <div class="ir_bar_graph"><span></span></div>
              </div>
              <div class="ins_rank_user">
                <div class="ir_user_img">
                  <div class="ir_user_img_in" style="background-image:url('<?=$rank_img_src?>');"></div>
                </div>
                <div class="ir_name">
                  <div class="ir_name_user"><?=$r_name?></div>
                  <div class="ir_name_team"><?=$r_part?></div>
                </div>
              </div>
            </li>
          <?}?>
         <?}?>

        </ul>
      </div>
    </div>

      <?
  }else if($rank_type == "l_month"){

    //현재일자기준으로 한주간 체크
		$date_tmp = explode("-",$wdate);

		if($date_tmp){
			$year = $date_tmp[0];
			$month = $date_tmp[1];
			if(!$date_tmp[2]){
				$date_tmp[2] = "01";
			}
			$day = $date_tmp[2];
		}

		$sel_day = date("t",mktime(0, 0, 0, $month, $day, $year));				// 지정된 달은 몇일까지 있을까요?
		$sel_yoil = date("N",mktime(0, 0, 0, $month, 1, $year));				// 지정된 달의 첫날은 무슨요일일까요?
		$day_line = $sel_yoil%7;												// 지정된 달 1일 앞의 공백 숫자.
		$ra = ($sel_day + $day_line)/7;
		$ra = ceil($ra);
		$ra = $ra-1;															// 지정된 달은 총 몇주로 라인을 그어야 하나?

		//월간업무
		$ym = $year."-".$month;

    ?>
    <div class="ins_rank_top">
      <ul>
        <?
					$sql = "select email, count(email) as sum from work_todaywork_like";
					$sql = $sql .= " where DATE_FORMAT(workdate, '%Y-%m') = '".$ym."' and state = 0";
					$sql = $sql .= " and companyno = '".$companyno."' group by email order by sum desc limit 3";

          $rank = selectAllQuery($sql);

          if($rank){
            for($i=0; $i<count($rank[email]); $i++){
              $r_email = $rank[email][$i];
              $r_sum = $rank[sum][$i];
              $r_rank = $rank[rank][$i];

          $rank_img_src = profile_img_info($r_email);

          $sql = "select name,part from work_member where email = '".$r_email."' and state = 0 and companyno = '".$companyno."'";
          $r_mem = selectQuery($sql);

          if($r_mem){
            $r_part = $r_mem[part];
            $r_name = $r_mem[name];
          }
        ?>
          <li class="ir_rank_<?=$i+1?>">
            <div class="ins_rank_user">
              <div class="ir_user_img">
                <div class="ir_user_img_in" style="background-image:url('<?=$rank_img_src?>');"></div>
              </div>
              <div class="ir_name">
                <div class="ir_name_user"><?=$r_name?></div>
                <div class="ir_name_team"><?=$r_part?></div>
              </div>
              <div class="ir_rank_mark">
                <span><?=$i+1?>위</span>
              </div>
            </div>
            <div class="ir_user_heart"><!-- 좋아요 : ir_user_heart -->
              <span><?=number_format($r_sum)?></span>
            </div>
          </li>
        <?}?>
       <?}?>
      </ul>
    </div>

    <div class="ins_rank_graph">
      <div class="ins_rank_graph_in">
        <ul>
          <?
						$sql = "select email, count(email) as sum from work_todaywork_like";
						$sql = $sql .= " where DATE_FORMAT(workdate, '%Y-%m') = '".$ym."' and state = 0";
						$sql = $sql .= " and companyno = '".$companyno."' group by email order by sum desc limit 3,7";

            $rank = selectAllQuery($sql);

            if($rank){
              for($i=0; $i<count($rank[email]); $i++){
                $r_email = $rank[email][$i];
                $r_sum = $rank[sum][$i];
                $r_rank = $rank[rank][$i];

            $rank_img_src = profile_img_info($r_email);

            $sql = "select name,part from work_member where email = '".$r_email."' and state = 0 and companyno = '".$companyno."'";
            $r_mem = selectQuery($sql);

            if($r_mem){
              $r_part = $r_mem[part];
              $r_name = $r_mem[name];
            }
          ?>
            <li class="ir_rank_<?=$i+4?>">
              <div class="ins_rank_bar">
                <div class="ir_bar_heart"><!-- 좋아요 : ir_bar_heart -->
                  <span><?=number_format($r_sum)?></span>
                </div>
                <div class="ir_bar_rank">
                  <span><?=$i+4?></span>
                </div>
                <div class="ir_bar_graph"><span></span></div>
              </div>
              <div class="ins_rank_user">
                <div class="ir_user_img">
                  <div class="ir_user_img_in" style="background-image:url('<?=$rank_img_src?>');"></div>
                </div>
                <div class="ir_name">
                  <div class="ir_name_user"><?=$r_name?></div>
                  <div class="ir_name_team"><?=$r_part?></div>
                </div>
              </div>
            </li>
          <?}?>
         <?}?>

        </ul>
      </div>
    </div>

      <?
  }else if($rank_type == "co_month"){
     //현재일자기준으로 한주간 체크
		$date_tmp = explode("-",$wdate);

		if($date_tmp){
			$year = $date_tmp[0];
			$month = $date_tmp[1];
			if(!$date_tmp[2]){
				$date_tmp[2] = "01";
			}
			$day = $date_tmp[2];
		}

		$sel_day = date("t",mktime(0, 0, 0, $month, $day, $year));				// 지정된 달은 몇일까지 있을까요?
		$sel_yoil = date("N",mktime(0, 0, 0, $month, 1, $year));				// 지정된 달의 첫날은 무슨요일일까요?
		$day_line = $sel_yoil%7;												// 지정된 달 1일 앞의 공백 숫자.
		$ra = ($sel_day + $day_line)/7;
		$ra = ceil($ra);
		$ra = $ra-1;															// 지정된 달은 총 몇주로 라인을 그어야 하나?

		//월간업무
		$ym = $year."-".$month;

    ?>
    <div class="ins_rank_top">
      <ul>
        <?
          $sql = "select email, sum(coin) as sum from work_coininfo";
          $sql = $sql .= " where code in (500,700,900,1000,1100) and DATE_FORMAT(workdate, '%Y-%m') = '".$ym."' and state = 0";
          $sql = $sql .= " and companyno = '".$companyno."' group by email order by sum desc limit 3";
          $rank = selectAllQuery($sql);

          if($rank){
            for($i=0; $i<count($rank['email']); $i++){
              $r_email = $rank['email'][$i];
              $r_sum = $rank['sum'][$i];

          $rank_img_src = profile_img_info($r_email);

          $sql = "select name,part from work_member where email = '".$r_email."' and state = 0 and companyno = '".$companyno."'";
          $r_mem = selectQuery($sql);

          if($r_mem){
            $r_part = $r_mem['part'];
            $r_name = $r_mem['name'];
          }
        ?>
          <li class="ir_rank_<?=$i+1?>">
            <div class="ins_rank_user">
              <div class="ir_user_img">
                <div class="ir_user_img_in" style="background-image:url('<?=$rank_img_src?>');"></div>
              </div>
              <div class="ir_name">
                <div class="ir_name_user"><?=$r_name?></div>
                <div class="ir_name_team"><?=$r_part?></div>
              </div>
              <div class="ir_rank_mark">
                <span><?=$i+1?>위</span>
              </div>
            </div>
            <div class="ir_user_coin"><!-- 좋아요 : ir_user_heart -->
              <span><?=number_format($r_sum)?></span>
            </div>
          </li>
        <?}?>
       <?}?>
      </ul>
    </div>

    <div class="ins_rank_graph">
      <div class="ins_rank_graph_in">
        <ul>
          <?
						$sql = "select email, sum(coin) as sum from work_coininfo";
            $sql = $sql .= " where  code in (500,700,900,1000,1100) and DATE_FORMAT(workdate, '%Y-%m') = '".$ym."' and state = 0";
            $sql = $sql .= " and companyno = '".$companyno."' group by email order by sum desc limit 3,7";

            $rank = selectAllQuery($sql);

            if($rank){
              for($i=0; $i<count($rank['email']); $i++){
                $r_email = $rank['email'][$i];
                $r_sum = $rank['sum'][$i];

            $rank_img_src = profile_img_info($r_email);

            $sql = "select name,part from work_member where email = '".$r_email."' and state = 0 and companyno = '".$companyno."'";
            $r_mem = selectQuery($sql);

            if($r_mem){
              $r_part = $r_mem['part'];
              $r_name = $r_mem['name'];
            }
          ?>
            <li class="ir_rank_<?=$i+4?>">
              <div class="ins_rank_bar">
                <div class="ir_bar_coin"><!-- 좋아요 : ir_bar_heart -->
                  <span><?=number_format($r_sum)?></span>
                </div>
                <div class="ir_bar_rank">
                  <span><?=$i+4?></span>
                </div>
                <div class="ir_bar_graph"><span></span></div>
              </div>
              <div class="ins_rank_user">
                <div class="ir_user_img">
                  <div class="ir_user_img_in" style="background-image:url('<?=$rank_img_src?>');"></div>
                </div>
                <div class="ir_name">
                  <div class="ir_name_user"><?=$r_name?></div>
                  <div class="ir_name_team"><?=$r_part?></div>
                </div>
              </div>
            </li>
          <?}?>
         <?}?>

        </ul>
      </div>
    </div>

      <?
  }else if($rank_type == "p_day"){

    ?>
    <div class="ins_rank_top">
      <ul>
        <?
					$sql = "select email, sum(type1) + sum(type2) + sum(type3) + sum(type4) + sum(type5) + sum(type6) as sum";
					$sql = $sql .= " from work_cp_reward_list where workdate = '".$wdate."' and state = 0 and companyno = '".$companyno."'";
					$sql = $sql .= " group by email order by sum desc limit 3";
          $rank = selectAllQuery($sql);

          if($rank){
            for($i=0; $i<count($rank[email]); $i++){
              $r_email = $rank[email][$i];
              $r_sum = $rank[sum][$i];
              $r_rank = $rank[rank][$i];

          $rank_img_src = profile_img_info($r_email);

          $sql = "select name,part from work_member where email = '".$r_email."' and state = 0 and companyno = '".$companyno."'";
          $r_mem = selectQuery($sql);

          if($r_mem){
            $r_part = $r_mem[part];
            $r_name = $r_mem[name];
          }
        ?>
          <li class="ir_rank_<?=$i+1?>">
            <div class="ins_rank_user">
              <div class="ir_user_img">
                <div class="ir_user_img_in" style="background-image:url('<?=$rank_img_src?>');"></div>
              </div>
              <div class="ir_name">
                <div class="ir_name_user"><?=$r_name?></div>
                <div class="ir_name_team"><?=$r_part?></div>
              </div>
              <div class="ir_rank_mark">
                <span><?=$i+1?>위</span>
              </div>
            </div>
            <div class="ir_user_power"><!-- 좋아요 : ir_user_heart -->
              <span><?=$r_sum?></span>
            </div>
          </li>
        <?}?>
       <?}?>
      </ul>
    </div>

    <div class="ins_rank_graph">
      <div class="ins_rank_graph_in">
        <ul>
          <?
						$sql = "select email, sum(type1) + sum(type2) + sum(type3) + sum(type4) + sum(type5) + sum(type6) as sum";
						$sql = $sql .= " from work_cp_reward_list where workdate = '".$today."' and state = 0 and companyno = '".$companyno."'";
						$sql = $sql .= " group by email order by sum desc limit 3,7";

            $rank = selectAllQuery($sql);

            if($rank){
              for($i=0; $i<count($rank[email]); $i++){
                $r_email = $rank[email][$i];
                $r_sum = $rank[sum][$i];
                $r_rank = $rank[rank][$i];

            $rank_img_src = profile_img_info($r_email);

            $sql = "select name,part from work_member where email = '".$r_email."' and state = 0 and companyno = '".$companyno."'";
            $r_mem = selectQuery($sql);

            if($r_mem){
              $r_part = $r_mem[part];
              $r_name = $r_mem[name];
            }
          ?>
            <li class="ir_rank_<?=$i+4?>">
              <div class="ins_rank_bar">
                <div class="ir_bar_power"><!-- 좋아요 : ir_bar_heart -->
                  <span><?=$r_sum?></span>
                </div>
                <div class="ir_bar_rank">
                  <span><?=$i+4?></span>
                </div>
                <div class="ir_bar_graph"><span></span></div>
              </div>
              <div class="ins_rank_user">
                <div class="ir_user_img">
                  <div class="ir_user_img_in" style="background-image:url('<?=$rank_img_src?>');"></div>
                </div>
                <div class="ir_name">
                  <div class="ir_name_user"><?=$r_name?></div>
                  <div class="ir_name_team"><?=$r_part?></div>
                </div>
              </div>
            </li>
          <?}?>
         <?}?>

        </ul>
      </div>
    </div>

      <?
  } else if($rank_type == "p_week"){

    if(strpos($wdate, "~") !== false) {
      $wdate = trim($wdate);
      $tmp = explode("~", $wdate);
      $monthday = trim($tmp['0']);
      $sunday = trim($tmp['1']);
      $month = strtotime($monthday);
    }else{

      $date_tmp = explode("-",$wdate);
      $year = $date_tmp[0];
      $month = $date_tmp[1];
      $day = $date_tmp[2];
      $ret = week_day("$year-$month-$day");
      if($ret){

        //월요일
        $monthday = $ret['month'];

        //금요일
        $friday = $ret['friday'];

        //일요일
        $sunday = $ret['sunday'];

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
    ?>
    <div class="ins_rank_top">
      <ul>
        <?
					$sql = "select email, sum(type1) + sum(type2) + sum(type3) + sum(type4) + sum(type5) + sum(type6) as sum";
					$sql = $sql .= " from work_cp_reward_list where workdate between '".$monthday."' and '".$sunday."' and state = 0 and companyno = '".$companyno."'";
					$sql = $sql .= " group by email order by sum desc limit 3";
          $rank = selectAllQuery($sql);

          if($rank){
            for($i=0; $i<count($rank[email]); $i++){
              $r_email = $rank[email][$i];
              $r_sum = $rank[sum][$i];
              $r_rank = $rank[rank][$i];

          $rank_img_src = profile_img_info($r_email);

          $sql = "select name,part from work_member where email = '".$r_email."' and state = 0 and companyno = '".$companyno."'";
          $r_mem = selectQuery($sql);

          if($r_mem){
            $r_part = $r_mem[part];
            $r_name = $r_mem[name];
          }
        ?>
          <li class="ir_rank_<?=$i+1?>">
            <div class="ins_rank_user">
              <div class="ir_user_img">
                <div class="ir_user_img_in" style="background-image:url('<?=$rank_img_src?>');"></div>
              </div>
              <div class="ir_name">
                <div class="ir_name_user"><?=$r_name?></div>
                <div class="ir_name_team"><?=$r_part?></div>
              </div>
              <div class="ir_rank_mark">
                <span><?=$i+1?>위</span>
              </div>
            </div>
            <div class="ir_user_power"><!-- 좋아요 : ir_user_heart -->
              <span><?=$r_sum?></span>
            </div>
          </li>
        <?}?>
       <?}?>
      </ul>
    </div>

    <div class="ins_rank_graph">
      <div class="ins_rank_graph_in">
        <ul>
          <?
						$sql = "select email, sum(type1) + sum(type2) + sum(type3) + sum(type4) + sum(type5) + sum(type6) as sum";
						$sql = $sql .= " from work_cp_reward_list where workdate between '".$monthday."' and '".$sunday."' and state = 0 and companyno = '".$companyno."'";
						$sql = $sql .= " group by email order by sum desc limit 3,7";

            $rank = selectAllQuery($sql);

            if($rank){
              for($i=0; $i<count($rank[email]); $i++){
                $r_email = $rank[email][$i];
                $r_sum = $rank[sum][$i];
                $r_rank = $rank[rank][$i];

            $rank_img_src = profile_img_info($r_email);

            $sql = "select name,part from work_member where email = '".$r_email."' and state = 0 and companyno = '".$companyno."'";
            $r_mem = selectQuery($sql);

            if($r_mem){
              $r_part = $r_mem[part];
              $r_name = $r_mem[name];
            }
          ?>
            <li class="ir_rank_<?=$i+4?>">
              <div class="ins_rank_bar">
                <div class="ir_bar_power"><!-- 좋아요 : ir_bar_heart -->
                  <span><?=$r_sum?></span>
                </div>
                <div class="ir_bar_rank">
                  <span><?=$i+4?></span>
                </div>
                <div class="ir_bar_graph"><span></span></div>
              </div>
              <div class="ins_rank_user">
                <div class="ir_user_img">
                  <div class="ir_user_img_in" style="background-image:url('<?=$rank_img_src?>');"></div>
                </div>
                <div class="ir_name">
                  <div class="ir_name_user"><?=$r_name?></div>
                  <div class="ir_name_team"><?=$r_part?></div>
                </div>
              </div>
            </li>
          <?}?>
         <?}?>

        </ul>
      </div>
    </div>

      <?
  }else if($rank_type == "p_month"){

    //현재일자기준으로 한주간 체크
		$date_tmp = explode("-",$wdate);

		if($date_tmp){
			$year = $date_tmp[0];
			$month = $date_tmp[1];
			if(!$date_tmp[2]){
				$date_tmp[2] = "01";
			}
			$day = $date_tmp[2];
		}

		$sel_day = date("t",mktime(0, 0, 0, $month, $day, $year));				// 지정된 달은 몇일까지 있을까요?
		$sel_yoil = date("N",mktime(0, 0, 0, $month, 1, $year));				// 지정된 달의 첫날은 무슨요일일까요?
		$day_line = $sel_yoil%7;												// 지정된 달 1일 앞의 공백 숫자.
		$ra = ($sel_day + $day_line)/7;
		$ra = ceil($ra);
		$ra = $ra-1;															// 지정된 달은 총 몇주로 라인을 그어야 하나?

		//월간업무
		$ym = $year."-".$month;

    ?>
    <div class="ins_rank_top">
      <ul>
        <?
					$sql = "select email, sum(type1) + sum(type2) + sum(type3) + sum(type4) + sum(type5) + sum(type6) as sum";
					$sql = $sql .= " from work_cp_reward_list where DATE_FORMAT(workdate, '%Y-%m') = '".$ym."' and state = 0 and companyno = '".$companyno."'";
					$sql = $sql .= " group by email order by sum desc limit 3";
          $rank = selectAllQuery($sql);

          if($rank){
            for($i=0; $i<count($rank[email]); $i++){
              $r_email = $rank[email][$i];
              $r_sum = $rank[sum][$i];
              $r_rank = $rank[rank][$i];

          $rank_img_src = profile_img_info($r_email);

          $sql = "select name,part from work_member where email = '".$r_email."' and state = 0 and companyno = '".$companyno."'";
          $r_mem = selectQuery($sql);

          if($r_mem){
            $r_part = $r_mem[part];
            $r_name = $r_mem[name];
          }
        ?>
          <li class="ir_rank_<?=$i+1?>">
            <div class="ins_rank_user">
              <div class="ir_user_img">
                <div class="ir_user_img_in" style="background-image:url('<?=$rank_img_src?>');"></div>
              </div>
              <div class="ir_name">
                <div class="ir_name_user"><?=$r_name?></div>
                <div class="ir_name_team"><?=$r_part?></div>
              </div>
              <div class="ir_rank_mark">
                <span><?=$i+1?>위</span>
              </div>
            </div>
            <div class="ir_user_power"><!-- 좋아요 : ir_user_heart -->
              <span><?=$r_sum?></span>
            </div>
          </li>
        <?}?>
       <?}?>
      </ul>
    </div>

    <div class="ins_rank_graph">
      <div class="ins_rank_graph_in">
        <ul>
          <?
						$sql = "select email, sum(type1) + sum(type2) + sum(type3) + sum(type4) + sum(type5) + sum(type6) as sum";
						$sql = $sql .= " from work_cp_reward_list where DATE_FORMAT(workdate, '%Y-%m') = '".$ym."' and state = 0 and companyno = '".$companyno."'";
						$sql = $sql .= " group by email order by sum desc limit 3,7";

            $rank = selectAllQuery($sql);

            if($rank){
              for($i=0; $i<count($rank[email]); $i++){
                $r_email = $rank[email][$i];
                $r_sum = $rank[sum][$i];
                $r_rank = $rank[rank][$i];

            $rank_img_src = profile_img_info($r_email);

            $sql = "select name,part from work_member where email = '".$r_email."' and state = 0 and companyno = '".$companyno."'";
            $r_mem = selectQuery($sql);

            if($r_mem){
              $r_part = $r_mem[part];
              $r_name = $r_mem[name];
            }
          ?>
            <li class="ir_rank_<?=$i+4?>">
              <div class="ins_rank_bar">
                <div class="ir_bar_power"><!-- 좋아요 : ir_bar_heart -->
                  <span><?=$r_sum?></span>
                </div>
                <div class="ir_bar_rank">
                  <span><?=$i+4?></span>
                </div>
                <div class="ir_bar_graph"><span></span></div>
              </div>
              <div class="ins_rank_user">
                <div class="ir_user_img">
                  <div class="ir_user_img_in" style="background-image:url('<?=$rank_img_src?>');"></div>
                </div>
                <div class="ir_name">
                  <div class="ir_name_user"><?=$r_name?></div>
                  <div class="ir_name_team"><?=$r_part?></div>
                </div>
              </div>
            </li>
          <?}?>
         <?}?>

        </ul>
      </div>
    </div>

      <?
  }

}

if($mode == "r_date_change"){

  $wdate = $_POST['wdate'];
  $work_wdate = $_POST['work_wdate'];
  $day_type = $_POST['day_type'];

  //날짜변환
	if($wdate){
		if(strpos($wdate, ".") !== false) {
			$wdate = str_replace(".", "-", $wdate);
		}
	}

	if($work_wdate){
		if(strpos($work_wdate, ".") !== false) {
			$work_wdate = str_replace(".", "-", $work_wdate);
		}
	}

	//일일
	if($day_type == "day"){
		if($work_wdate){
			if(strpos($work_wdate, "~") !== false) {
				$wdate = str_replace(" ","",$work_wdate);
				$work_wdate = str_replace("-", ".", $work_wdate);
				$tmp = explode("~", $work_wdate);

				$date1 = $tmp[0];
				$date2 = $tmp[1];
				$result = $date1;

			}else{
				$result = date("Y.m.d", strtotime($work_wdate));
			}

		}else{

			$result = date("Y.m.d", time());
		}

		echo $result;

	//주간
	}else if($day_type == "week"){



		$wdate = $work_wdate;

		if(strpos($wdate, "~") !== false) {

			$wdate = str_replace(" ","",$wdate);
			$tmp = explode("~", $wdate);
			$date1 = $tmp[0];
			$date2 = $tmp[1];
			$result = $date1 ." ~ ". $date2;
			echo $result;

		}else{

			//echo ">> ".$wdate;



			$ret = week_day($wdate);
			if($ret){

				$ret['month'] = str_replace("-", ".", $ret['month']);
				$ret['sunday'] = str_replace("-", ".", $ret['sunday']);

				//월요일
				$monthday = $ret['month'];

				//일요일
				$sunday = $ret['sunday'];

				$result = $monthday . " ~ " . $sunday;

				echo $result;
			}
		}

	//월간
	}else if($day_type == "month"){


		echo date("Y.m", time());
	}

	exit;
}

?>
