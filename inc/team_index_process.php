<?php

$home_dir = str_replace( basename(__DIR__) , "" , __DIR__ );

include $home_dir . "inc_lude/conf_mysqli.php";
include DBCON_MYSQLI;
include FUNC_MYSQLI;


$mode = $_POST["mode"];		
$team = $_POST["team"];
if($mode == 'date_list'){
	$ttoday = TODATE;

	$next = $_POST["next"];
	$prev = $_POST["prev"];
	$cate = $_POST["cate"];
	$tsNum = $_POST["ts_no"];
	$team = $_POST["team"];
	$teamNo = $_POST["teamNo"];
	$now = $_POST["now"];


	if($team){
		$where = "and member.partno = '".$teamNo."'";
	}else{
		$where = "and member.partno = '".$tsNum."'";
	}
	if($now){
		$str_now = strtotime($now);
		$today = date('Y-m-d', $str_now);
		$today_y = date('Y', $str_now);
		$today_m =  date('m', $str_now);
		$today_d =  date('d',$str_now);
	}else if($next){
		$str_next = strtotime($next.'+7 days');
		$today = date('Y-m-d', $str_next);
		$today_y = date('Y',$str_next);
		$today_m =  date('m',$str_next);
		$today_d =  date('d',$str_next);
	}else if($prev){
		$str_prev = strtotime($prev.'-7 days'); 
		$today = date('Y-m-d', $str_prev);
		$today_y = date('Y',$str_prev);
		$today_m =  date('m',$str_prev);
		$today_d =  date('d',$str_prev);
	}

	$week_info = week_day($today);
	$weekNumber = getCurrentWeekNumberInMonth($today_y, $today_m, $today_d);
	
	$sql = "select work.idx, work.name, work.companyno, member.partno, work.secret_flag, work.part, work.contents, work.workdate, work.state, work.share_flag, work.work_flag, work.work_idx
	from work_todaywork work
	left join work_member member on work.name = member.name
	left join calendar_events calendar on work.idx = calendar.work_idx
	where 1=1
	and member.state = '0'
	and ((work.state NOT IN ('9', '99') AND (calendar.state IS NULL OR calendar.state <> '9')) OR (calendar.state = '0')) 
	and work.notice_flag = '0'
	and work.secret_flag = '0'
	and work.companyno = '".$companyno."'
	and work.part != ''";
	$sql .= $where;
	$sql .="
	and work.workdate between '".$week_info['month']."' and '".$week_info['friday']."' 
	group by work.name, work.idx";
	$date_team = selectAllQuery($sql);
	$sql = "select idx, partname from work_team where 1=1 and companyno = '".$companyno."' and state != '9' order by partname desc";
	$team_list = selectAllQuery($sql);
?>

<body>
<input type = "hidden" class = "tweek" value = "<?=$ttoday?>">
<div class="rew_warp">
	<style type="text/css">
		.rew_warp{min-height:360px;}
	</style>
	<div class="team_summary">
		<div class="ts_in">
			<div class="ts_tab">
				<div class="ts_team"><span>Weekly Team Summary</span></div>
				<div class="ts_calendar">
					<div class="ts_calendar_week">
						<button class="ts_prev"><span>이전주</span></button>
						<button class="ts_now"value ="<?php echo $today?>"><span><?php echo $today_y?>년 <?php echo $today_m?>월 <?php echo $weekNumber;?>주</span></button>
						<button class="ts_next"><span>다음주</span></button>
					</div>
				</div>
				<div class="ts_sort">
					<div class="ts_sort_in">
						<button class="btn_sort_on" value = "<?=$tsNum?$tsNum:$teamNo?>"><span><?php echo $cate?$cate:$team?></span></button>
						<ul>
						<?php foreach($team_list['idx'] as $index => $id){?>
							<li><button value = '<?php echo $team_list['partname'][$index]?>'><span value = "<?php echo $team_list['idx'][$index]?>"><?php echo $team_list['partname'][$index]?></span></button></li>
						<?php }?>
						</ul>
					</div>
				</div>
			</div>

			<div class="ts_tbl">
				<table>
					<caption>팀별 주간업무</caption>
					<colgroup>
						<col class="ts_col_00" />
						<col class="ts_col_01" />
						<col class="ts_col_02" />
						<col class="ts_col_03" />
						<col class="ts_col_04" />
						<col class="ts_col_05" />
					</colgroup>

					<?php 
					$unique_names = array();
					foreach ($date_team['idx'] as $index => $id) {
						$name = $date_team['name'][$index];
						$contents = $date_team['contents'][$index];
						$workdate = $date_team['workdate'][$index];
						$states = $date_team['state'][$index];
						$shares = $date_team['share_flag'][$index];
						$works = $date_team['work_flag'][$index];
						$workId = $date_team['work_idx'][$index];

						if (!isset($unique_names[$name])) {
							$unique_names[$name] = array(
								'workdates' => array(),
								'contents' => array(),
								'states' => array(),
								'shares' => array(),
								'works' => array(),
								'workId' => array(),
							);
						}

						$unique_names[$name]['workdates'][] = $workdate;
						$unique_names[$name]['contents'][] = $contents;
						$unique_names[$name]['states'][] = $states;
						$unique_names[$name]['shares'][] = $shares;
						$unique_names[$name]['works'][] = $works;
						$unique_names[$name]['workId'][] = $workId;
					}

					function getNextWeekdays($date) {
						$weekdays = array();
						$currentDate = strtotime($date);
						$firstMonday = strtotime('Monday this week', $currentDate);
						for ($i = 0; $i < 5; $i++) {
							$weekday = date('Y-m-d', $firstMonday);
							$weekdays[] = $weekday;
							$firstMonday = strtotime('+1 day', $firstMonday); // Move to the next day
						}

						return $weekdays;
					}

					function getDayOfWeek($date) {
						$days = array('일', '월', '화', '수', '목', '금', '토');
						$dayOfWeek = date('w', strtotime($date)); // 0: Sunday, 1: Monday, ..., 6: Saturday
						return $days[$dayOfWeek];
					}
					
					$unique_dates = array_unique($date_team['workdate']);
					sort($unique_dates);
					$weekdays_intervals = array();
					
					$weekdays_intervals = getNextWeekdays($today);

					?>
					<thead>
						<tr>
							<th scope="col"><span>구성원</span></th>
							<?php foreach ($weekdays_intervals as $date): ?>
								<th scope="col"><span><?php echo $date. ' (' . getDayOfWeek($date) . ')'	; ?></span></th>
							<?php endforeach; ?>
						</tr>
					</thead>
					<tbody>
						<?php foreach ($unique_names as $name => $data): ?>
							<tr>
								<th scope="row"><span><?php echo $name; ?></span></th>
								<?php foreach ($weekdays_intervals as $date): ?>
									<?php
									$date_contents = array();
									for ($i = 0; $i < count($data['workdates']); $i++) {
										if ($data['workdates'][$i] === $date) {
											$contents = $data['contents'][$i];
											$states = $data['states'][$i];
											$shares = $data['shares'][$i];
											$works = $data['works'][$i];
											$workId = $data['workId'][$i];
											if($states != '0'){
                    					    	$date_contents[] = " - <span class='over' style='color:#a5a5a5;'>" . $contents."</span>";
											}else{
												if($shares == '1'){
													$date_contents[] = " - <span>[공유함]</span> " . $contents;
												}else if($shares == '2'){
													$date_contents[] = " - <span>[공유받음]</span> " . $contents;
												}else if($works == '1' && $workId == ''){
													$date_contents[] = " - <span>[보고함]</span> " . $contents;
												}else if($works == '1' && $workId != ''){
													$date_contents[] = " - <span>[보고받음]</span> " . $contents;
												}else if($works == '3' && $workId == ''){
													$date_contents[] = " - <span>[요청함]</span> " . $contents;
												}else if($works == '3' && $workId != ''){
													$date_contents[] = " - <span>[요청받음]</span> " . $contents;
												}else{
													$date_contents[] = " - " . $contents;
												}
											}
										}
									}
									?>
									<td>
										<?php
										if (!empty($date_contents)) {
											echo "<p>".implode("<br>", $date_contents)."</p>";
										} else {
											echo "-";
										}
										?>
									</td>
								<?php endforeach; ?>
							</tr>
						<?php endforeach; ?>
					</tbody>

				</table>
			</div>
			
		</div>
		
	</div>
</div>
</body>
<?php
	exit; 
}