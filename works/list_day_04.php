<?php
//오늘업무 리스트
//일일 리스트

include str_replace( basename(__DIR__) , "", __DIR__ ) ."inc_lude/conf.php";
include DBCON;
include FUNC;

//날짜
$wdate = $_POST['wdate']?$_POST['wdate']:$_GET['wdate'];

if($_SERVER['HTTP_REFERER']){
	$http_query_str = @explode("?", $_SERVER['HTTP_REFERER']);
	parse_str($http_query_str['1']);
}

//나의업무/전체/팀별업무별 조건
$type = $_POST['type'];

if($wdate){
	$newdate = str_replace(".","-",$wdate);
}else{
	$newdate = TODATE;
}

//팀별업무
if(!$type){
	$type = "my";
}

//전체업무
if($type == "all"){
	$where .= "";
}
else if($type == "team"){
	$where .= "and a.part_flag='".$user_part."'";
}
else{
	if($user_id=='sadary1@nate.com'){
		//$user_id = "sunppp@naver.com";
	}
	$where .= "and (a.email='".$user_id."' or b.email like '%".$user_id."%')";
}


//날짜조건
$where .= " and convert( varchar(10) , workdate, 120) = '".$newdate."' ";

/*
$sql = "SELECT DISTINCT idx, state, work_flag, part_flag, convert(varchar(max) , contents) as contents, convert(varchar(max) , contents1) as contents1, email, name, contents2,";
$sql = $sql .= " STUFF(( SELECT ', ' + name FROM work_req_write as b WHERE b.work_idx = a.idx FOR XML PATH('') ),1,1,'') AS req_name,";
$sql = $sql .= " STUFF(( SELECT ',' + req_date FROM work_req_write as b WHERE b.work_idx = a.idx group by req_date FOR XML PATH('') ),1,1,'') AS req_date,";
$sql = $sql .= " STUFF(( SELECT ',' + req_stime FROM work_req_write as b WHERE b.work_idx = a.idx group by req_stime FOR XML PATH('') ),1,1,'') AS req_stime,";
$sql = $sql .= " STUFF(( SELECT ',' + req_etime FROM work_req_write as b WHERE b.work_idx = a.idx group by req_etime FOR XML PATH('') ),1,1,'') AS req_etime,";
$sql = $sql .= " workdate, regdate as reg";
$sql = $sql .= " FROM work_todaywork as a where a.state!=9 ";
$sql = $sql .= " ".$where."";
$sql = $sql .= " order by idx desc";
*/

$sql = "select a.idx, a.state, a.work_flag, a.part_flag, b.work_idx, a.type_flag, a.email, a.name, b.user_idx as user_idx, b.email as req_email, b.name as req_name, convert(varchar(max) , contents) as contents,";
$sql = $sql .= " convert(varchar(max) , a.contents1) as contents1, a.contents2, workdate, a.regdate from work_todaywork as a";
$sql = $sql .= " left join work_req_write as b on (a.idx = b.work_idx) ";
$sql = $sql .= " where a.state!=9";
$sql = $sql .= " ".$where."";
$sql = $sql .= " order by a.idx desc";
$res = selectAllQuery($sql);


//업무요청 회원목록
$highlevel = 5;
$sql = "select idx, name, part from work_member where state='0' and highlevel='".$highlevel."' and companyno='".$companyno."' and email != '".$user_id."' order by idx asc";
$mem_req = selectAllQuery($sql);

?>
<div class="tc_index_middle">
	<ul>

	<?for($i=0; $i<count($res['idx']); $i++){
			$idx = $res['idx'][$i];
			$state = $res['state'][$i];
			$list_id = $res['email'][$i];
			$list_name = $res['name'][$i];
			$work_flag = $res['work_flag'][$i];
			$contents = $res['contents'][$i];
			$contents1 = $res['contents1'][$i];
			$req_date = $res['contents2'][$i];
			$workdate = $res['workdate'][$i];
			if($work_flag == 2){
				$req_name = $res['req_name'][$i];
				$req_email = $res['req_email'][$i];
				$user_idx = $res['user_idx'][$i];
				$req_user = explode(",", $user_idx);

			}else{
				$req_name = "";
				$req_email = "";
				$user_idx = "";
			}



		//	echo "############" . iconv("UTF-8", "EUC-KR", $contents);
		//$contents = iconv("UTF-8", "utf8_general_ci", $contents); 

			$req_email_arr = @explode(",", $req_email);
			if (is_array($req_email_arr) == true){
				if (in_array($user_id , $req_email_arr) == true){
					$from_name = "From. " .$list_name;
				}else{
					$from_name = "To. " .$req_name;
				}
			}else{
				$from_name = "From. " .$list_name;
			}


		?>
			<li <?=$cls?>>
				<div class="tc_desc" <?if($list_editor == true){?>id="tc_dec_<?=$idx?>"<?}?>>
					<span <?if($list_editor == true){?>id="contents1_<?=$idx?>"<?}?>>(<?=$idx?>) <?=$contents?></span>
				</div>
			</li>
		<?}?>

	</ul>
</div>