<?
	//header페이지
	$home_dir = str_replace( basename(__DIR__) , "" , __DIR__ );
	include $home_dir . "/inc_lude/header.php";


	

	$user_id = $_COOKIE["user_id"]; 

	$today_y = date("Y");
	$today_m = date("m");
	$today_d = date("d");
	$today = date("Y-m-d");

	$week_info = week_day($today);
	$weekNumber = getCurrentWeekNumberInMonth($today_y, $today_m, $today_d);
	
	$sql = "select partno, part from work_member where email = '".$user_id."'";
	$person_part = selectQuery($sql);

	
	$sql = "select work.idx, work.name, work.companyno,work.secret_flag,member.partno, work.part, work.contents, work.workdate, work.state, work.share_flag, work.work_flag, work.work_idx
	from work_todaywork work
    left join work_member member on work.name = member.name
    left join calendar_events calendar on work.idx = calendar.work_idx
    where 1=1
    and member.state = '0'
	and ((work.state NOT IN ('9', '99') AND (calendar.state IS NULL OR calendar.state <> '9')) OR (calendar.state = '0')) 
	and work.notice_flag = '0'
    and work.part != ''
	and work.secret_flag = '0'
	and work.companyno = '".$companyno."'
    and work.workdate between '".$week_info['month']."' and '".$week_info['friday']."' 
    and member.partno = '".$person_part['partno']."'
	group by work.name, work.idx";
	$member_list = selectAllQuery($sql);
	$sql = "select idx, partname from work_team where 1=1 and state != '9' and companyno = '".$companyno."' order by partname desc";
	$team_list = selectAllQuery($sql);
	
?>
<body>

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
						<button class="ts_prev" id ="ts_prev"><span>이전주</span></button>
						<button class="ts_now" value ="<?php echo $today?>"><span><?php echo $today_y?>년 <?php echo $today_m?>월 <?php echo $weekNumber;?>주</span></button>
						<button class="ts_next" id ="ts_next"><span>다음주</span></button>
					</div>
				</div>
				<div class="ts_sort">
					<div class="ts_sort_in">
						<button class="btn_sort_on" value = "<?php echo $person_part['partno']?>"><span><?php echo $person_part['part']?></span></button>
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

					foreach ($member_list['idx'] as $index => $id) {
						$name = $member_list['name'][$index];
						$contents = $member_list['contents'][$index];
						$workdate = $member_list['workdate'][$index];
						$states = $member_list['state'][$index];
						$secret = $member_list['secret_flag'][$index];
						$shares = $member_list['share_flag'][$index];
						$works = $member_list['work_flag'][$index];
						$workId = $member_list['work_idx'][$index];

						if (!isset($unique_names[$name])) {
							$unique_names[$name] = array(
								'workdates' => array(),
								'contents' => array(),
								'states' => array(),
								'secret' => array(),
								'shares' => array(),
								'works' => array(),
								'workId' => array(),
							);
						}

						$unique_names[$name]['workdates'][] = $workdate;
						$unique_names[$name]['contents'][] = $contents;
						$unique_names[$name]['states'][] = $states;
						$unique_names[$name]['secret'][] = $secret;
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

					$unique_dates = array_unique($member_list['workdate']);
					sort($unique_dates);
					$weekdays_intervals = array();
					
					$current_date = date('Y-m-d');
					$weekdays_intervals = getNextWeekdays($current_date);
					
					
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
											$secret = $data['secret'][$i];
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
													$date_contents[] = " - ". $contents;
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
<script type="text/javascript" src = "/js/team_index.js"></script>
<script language="JavaScript">
/* FOR BIZ., COM. AND ENT. SERVICE. */
_TRK_CP = "/Rewardy"; /* 페이지 이름 지정 Contents Path */
</script>
</body>