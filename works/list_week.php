<?

include str_replace( basename(__DIR__) , "", __DIR__ ) ."inc_lude/conf.php";
include DBCON;
include FUNC;

/*
print "<pre>";
print_r($_POST);
print "</pre>";
*/

//날짜
$wdate = $_POST['wdate']?$_POST['wdate']:$_GET['wdate'];

if($_SERVER['HTTP_REFERER']){
	$http_query_str = @explode("?", $_SERVER['HTTP_REFERER']);
	parse_str($http_query_str['1']);
}

if(!$wdate){
	$wdate = TODATE;
}


//년,월,일 시간:분:초
$yyyy = substr($wdate, 0, 4);
$mm = substr($wdate, 5, 2);
$dd = substr($wdate, 8, 2);
$his = date("H:i:s");


//나의업무/전체/팀별업무별 조건
$type = $_POST['type'];

//일주일 리스트
$time = strtotime("$yyyy-$mm-$dd $his"); //time();
$week = date("w", $time);
$weeks = array("일","월","화","수","목","금","토");


//오늘날짜기준
//$month = strtotime(date('Y-m-d', strtotime('-'.($week - 1).'days')));			//월요일
//$today_no = date('w', $time);
//$monthday = date('Y-m-d', strtotime('-'.($today_no - 1).'days'));				//월요일(년-월-일)
//$friday = date('Y-m-d',strtotime('+'.(5 -$today_no).'days'));					//금요일(년-월-일)

$ret = week_day("$yyyy-$mm-$dd");
if($ret){

	//월요일
	$monthday = $ret['month'];

	//금요일
	$friday = $ret['friday'];

	//월요일
	$month = strtotime($monthday);
}

if($wdate){
	$newdate = str_replace(".","-",$wdate);
}else{
	$newdate = TODATE;
}

if(!$type){
	$type = "my";
}

if($type == "all"){
	$where .= "";
	$where1 .= "";
}
else if($type == "team"){
	$where .= "and a.part_flag='".$user_part."'";
	$where1 .= "and part_flag='".$user_part."'";
	
}
else{

	$where .= "and a.email='".$user_id."'";
	$where1 .= "and email='".$user_id."'";
}

$where .= " and convert( varchar(10) , workdate, 120) between '".$monthday."' and '".$friday."'";
$where1 .= " and convert( varchar(10) , workdate, 120) between '".$monthday."' and '".$friday."'";
/*
$sql = "SELECT DISTINCT idx, state, work_flag, part_flag, convert(varchar(max) , contents) as contents, convert(varchar(max) , contents1) as contents1, email, name, contents2,";
$sql = $sql .= " STUFF(( SELECT ', ' + name FROM work_req_write as b WHERE b.work_idx = a.idx FOR XML PATH('') ),1,1,'') AS req_name,";
$sql = $sql .= " STUFF(( SELECT ',' + req_date FROM work_req_write as b WHERE b.work_idx = a.idx group by req_date FOR XML PATH('') ),1,1,'') AS req_date,";
$sql = $sql .= " STUFF(( SELECT ',' + req_stime FROM work_req_write as b WHERE b.work_idx = a.idx group by req_stime FOR XML PATH('') ),1,1,'') AS req_stime,";
$sql = $sql .= " STUFF(( SELECT ',' + req_etime FROM work_req_write as b WHERE b.work_idx = a.idx group by req_etime FOR XML PATH('') ),1,1,'') AS req_etime,";
$sql = $sql .= " workdate, regdate as reg ";
$sql = $sql .= " FROM work_todaywork as a where a.state != 9";
$sql = $sql .= " ".$where."";
$sql = $sql .= " order by idx desc";
*/

$sql ="select idx, state, work_flag, part_flag, convert(varchar(max) , contents) as contents, convert(varchar(max) , contents1) as contents1,";
$sql = $sql .=" email, name, contents2, req_date, workdate, regdate ";
$sql = $sql .= " FROM work_todaywork where state != 9".$where1."";
$sql = $sql .= " order by idx desc";
$res = selectAllQuery($sql);


for($i=0; $i<count($res['idx']); $i++){
	$idx = $res['idx'][$i];
	$state = $res['state'][$i];
	$work_flag = $res['work_flag'][$i];
	$workdate = $res['workdate'][$i];
	$contents = $res['contents'][$i];
	$contents1 = $res['contents1'][$i];
	$week_work_contents[$workdate][$idx] = $contents;
	$week_work_contents1[$workdate][$idx] = $contents1;
	$week_state[$workdate][$idx] = $state;
	$week_flag[$workdate][$idx] = $work_flag;

}

//업무구분(0:기본, 1:일정, 2:업무요청, 31:일일목표 , 32:주간목표, 33:성과목표)

?>


	<div class="tc_index_middle_week">
		<ul>

			<?for ($i=1, $day=$month; $i<6; $i++, $day += 86400){

				$date_list = date("Y-m-d",$day);
				$day_list = $weeks[date("w",$day)];
			?>
				<li>
					<div class="tc_chk_week">
						<span><?=$day_list?></span>
					</div>
					<div class="tc_desc_week">
						<ul>
							<?
							
							for($j=0; $j<count($res['idx']); $j++){

								

								if($week_flag[$date_list][$res['idx'][$j]] == '0'){
									$flag = "(할일)";
									$contents1 = "";
								}else if($week_flag[$date_list][$res['idx'][$j]] == '1'){
									$flag = "(일정)";
									$contents1 = "";
								}else if($week_flag[$date_list][$res['idx'][$j]] == '2'){
									$flag = "(업무요청)";
									$contents1 = "";
								}else if(@in_array($week_flag[$date_list][$res['idx'][$j]], array('31','32','33'))){
									$flag = "(목표)";
									$contents1 = $week_work_contents1[$date_list][$res['idx'][$j]];
								}else{
									$flag = "";
									$contents1 = "";
								}

								//내용
								$contents = $week_work_contents[$date_list][$res['idx'][$j]];

								//상태값
								$state = $week_state[$date_list][$res['idx'][$j]];

								if ($state == 1){
									$span_class = " class=\"complete\"";
								}else{
									$span_class = "";
								}
							?>
								<li><span<?=$span_class?>><?=$flag?> <?=$contents?> <?=$contents1?"<br>".$contents1:""?></span></li>
							<?}?>
						</ul>
					</div>
				</li>
			<?}?>

		</ul>
	</div>
