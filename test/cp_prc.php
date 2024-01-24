<?

//phpinfo();

print "<pre>";
print_r($_SERVER);


echo "_SERVER :: ". $_SERVER['HTTP_X_FORWARDED_SSL'];
print "</pre>";


exit;

//테스트페이입니다.
$home_dir = str_replace( basename(__DIR__) , "" , __DIR__ );
include $home_dir . "inc_lude/conf.php";
include DBCON;
include FUNC;


//$cp = main_like_cp();


//print "<pre>";
//print_r($cp);
//print "</pre>";


//$time = date("Y-m-d H:i:s" , TODAYTIME);
//$log = "업데이트 시간 : ".$time ." (좋아요 지표)";
//write_log_dir($log , "update_20221107");

/*
	$sql = "select * from (select ROW_NUMBER() over(order by a.idx desc) as r_num, a.idx, a.state, a.cate, a.title, a.companyno, a.email, a.keyword, a.pageview, a.temp_flag, a.view_flag, (select count(1) from work_challenges_thema_zzim_list where a.idx=challenges_idx and state='0' and email='sadary0@nate.com') as zzim, (select top 1 sort from work_challenges_thema_list where a.idx=challenges_idx and state='0' and sort > 0 order by idx desc) as sort, (select top 1 thema_idx from work_challenges_thema_list where state='0' and thema_idx='9' and a.idx=challenges_idx ) as themaidx9, (select top 1 thema_idx from work_challenges_thema_list where state='0' and thema_idx='2' and a.idx=challenges_idx ) as themaidx2, (select top 1 thema_idx from work_challenges_thema_list where state='0' and thema_idx='16' and a.idx=challenges_idx ) as themaidx16, (select top 1 thema_idx from work_challenges_thema_list where state='0' and thema_idx='10' and a.idx=challenges_idx ) as themaidx10, (select top 1 thema_idx from work_challenges_thema_list where state='0' and thema_idx='5' and a.idx=challenges_idx ) as themaidx5, (select top 1 thema_idx from work_challenges_thema_list where state='0' and thema_idx='7' and a.idx=challenges_idx ) as themaidx7, (select top 1 thema_idx from work_challenges_thema_list where state='0' and thema_idx='1' and a.idx=challenges_idx ) as themaidx1, (select top 1 thema_idx from work_challenges_thema_list where state='0' and thema_idx='17' and a.idx=challenges_idx ) as themaidx17, (select top 1 thema_idx from work_challenges_thema_list where state='0' and thema_idx='15' and a.idx=challenges_idx ) as themaidx15 from work_challenges as a  where a.state='0' and a.template='1' and a.coaching_chk='0' and view_flag='0' and temp_flag='0' and (select top 1 thema_idx from work_challenges_thema_list where state='0' and thema_idx='15' and a.idx=challenges_idx)='15') as a";
	$sql = $sql .= " where r_num between 1 and 70 and view_flag='0' and temp_flag='0' ORDER BY case when sort is not null then sort end asc, case when sort is null then a.idx end desc";
	$row = selectAllQuery($sql);

	for($i=0; $i<count($row['idx']); $i++){

		echo urldecode($row['title'][$i]);
		echo "\t";
		echo "https://rewardy.co.kr/challenge/view.php?idx=".$row['idx'][$i];
		echo "\n";
	}
	*/

?>
