<?php

	exit;
	//챌린지 템플릿 리스트
	//header페이지
	$home_dir = str_replace( basename(__DIR__) , "" , __DIR__ );
	//include $home_dir . "/inc_lude/header.php";
	include $home_dir . "inc_lude/conf.php";
	include DBCON;
	include FUNC;
	

	//$sql = "select *, CONVERT(CHAR(19), regdate, 20) as regdate , (CASE WHEN day_type='1' THEN coin * attend WHEN day_type='0' THEN coin END ) as maxcoin from work_challenges where state='0' and template='1' order by idx desc";


	$sql = "select * from (select ROW_NUMBER() over(order by a.idx desc) as r_num, a.idx, a.state, a.cate, a.title, a.company, a.email, a.pageview, a.temp_flag, a.view_flag,";
	$sql = $sql .= " (CASE WHEN day_type='1' THEN coin * attend WHEN day_type='0' THEN coin END ) as maxcoin,";
	$sql = $sql .= " a.attend_type, a.attend, a.day_type, a.sdate, a.edate, convert(char(10),regdate, 120) as regdate, ";
	$sql = $sql .= " (select count(1) from work_challenges_thema_zzim_list where a.idx=challenges_idx and state='0' and email='sadary0@nate.com') as zzim,";
	$sql = $sql .= " (select top 1 thema_idx from work_challenges_thema_list where state='0' and thema_idx='9' and a.idx=challenges_idx ) as themaidx9,";
	$sql = $sql .= " (select top 1 thema_idx from work_challenges_thema_list where state='0' and thema_idx='1' and a.idx=challenges_idx ) as themaidx1,";
	$sql = $sql .= " (select top 1 thema_idx from work_challenges_thema_list where state='0' and thema_idx='2' and a.idx=challenges_idx ) as themaidx2,";
	$sql = $sql .= " (select top 1 thema_idx from work_challenges_thema_list where state='0' and thema_idx='16' and a.idx=challenges_idx ) as themaidx16,";
	$sql = $sql .= " (select top 1 thema_idx from work_challenges_thema_list where state='0' and thema_idx='5' and a.idx=challenges_idx ) as themaidx5,";
	$sql = $sql .= " (select top 1 thema_idx from work_challenges_thema_list where state='0' and thema_idx='7' and a.idx=challenges_idx ) as themaidx7,";
	$sql = $sql .= " (select top 1 thema_idx from work_challenges_thema_list where state='0' and thema_idx='17' and a.idx=challenges_idx ) as themaidx17,";
	$sql = $sql .= " (select top 1 thema_idx from work_challenges_thema_list where state='0' and thema_idx='10' and a.idx=challenges_idx ) as themaidx10,";
	$sql = $sql .= " (select top 1 thema_idx from work_challenges_thema_list where state='0' and thema_idx='15' and a.idx=challenges_idx ) as themaidx15";
	$sql = $sql .= " from work_challenges as a  where a.state='0' and a.template='1' and a.coaching_chk='0'";
	$sql = $sql .= " and (select top 1 thema_idx from work_challenges_thema_list where state='0' and thema_idx!='15' and a.idx=challenges_idx)!='15'";
	$sql = $sql .= " ) as a";
	$info = selectAllQuery($sql);


	//카테고리명
	$sql = "select idx, name from work_category where state='0' order by rank asc";
	$cate_info = selectAllQuery($sql);
	for($i=0; $i<count($cate_info['idx']); $i++){
		$chall_category[$cate_info['idx'][$i]] = $cate_info['name'][$i];
	}

	//테마명
	$sql = "select idx, title from work_challenges_thema where state='0'";
	$thema_info = selectAllQuery($sql);
	for($i=0; $i<count($thema_info['idx']); $i++){
		$chall_thema_title[$thema_info['idx'][$i]] = $thema_info['title'][$i];
	}

	echo "챌린지번호";
	echo "\t";
	echo "참여형태";
	echo "\t";
	echo "카테고리";
	echo "\t";
	echo "테마명";
	echo "\t";
	echo "코인";
	echo "\t";
	echo "기간내 참여횟수";
	echo "\t";
	echo "참여횟수";
	echo "\t";
	echo "제목";
	echo "\t";
	
	echo "시작일";
	echo "\t";
	echo "종료일";
	echo "\t";
	echo "등록일자";
	echo "\n";




	for($i=0; $i<count($info['idx']); $i++){

		$idx = $info['idx'][$i];
		$title = $info['title'][$i];
		$title = urldecode($title);
		$cate = $info['cate'][$i];
		$coin = $info['coin'][$i];
		$sdate = $info['sdate'][$i];
		$edate = $info['edate'][$i];
		$attend = $info['attend'][$i];
		$day_type = $info['day_type'][$i];
		$regdate = $info['regdate'][$i];

		if($day_type == '1'){
			$day_type_title = "매일";
			$mcoin = number_format($info['maxcoin'][$i]);
		}else{
			$day_type_title = "한번";
			$mcoin = number_format($info['coin'][$i]);
		}

		$attend_type = $info['attend_type'][$i];
		if($attend_type=='1'){
			$attend_type_text = "메시지형";
		}else if($attend_type=='2'){
			$attend_type_text = "파일형";
		}else if($attend_type=='3'){
			$attend_type_text = "혼합형";
		}

		//카테고리명
		$category = $chall_category[$cate];

		//테마명
		$thema_title = $chall_thema_title[''];
		

		echo $idx;
		echo "\t";
		echo $attend_type_text;
		echo "\t";
		echo $category;
		echo "\t";
		echo $mcoin;
		echo "\t";
		echo $day_type_title;
		echo "\t";
		echo $attend;
		echo "\t";
		echo $title;
		echo "\t";

		echo $sdate;
		echo "\t";

		echo $edate;
		echo "\t";

		echo $regdate;
		echo "\n";

	}
?>

