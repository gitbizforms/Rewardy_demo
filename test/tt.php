<?php

	set_time_limit (0);

	//header페이지
	$home_dir = str_replace( basename(__DIR__) , "" , __DIR__ );
	//include $home_dir . "/inc_lude/header.php";
	include $home_dir . "inc_lude/conf.php";
	include DBCON;
	include FUNC;
	


	//$sql = "select * from work_todaywork where state=0 and email='sun@bizforms.co.kr' and repeat_work_idx='33596' and workdate>='2022-07-12' order by workdate asc";
	$sql = "select * from work_todaywork where state='0' and repeat_flag='1' and workdate>='2022-07-12' and email='sun@bizforms.co.kr' and contents='[O월 O일 팀장회의] 팀장회의 안건' order by workdate asc";
	
	//$sql = "select * from work_todaywork_user where state=0 and workdate >= '2022-07-12' and work_email='sun@bizforms.co.kr' order by workdate asc";
	//echo $sql;
	//echo "\n\n";
	$list = selectAllQuery($sql);

	$work_mem_idx = "50,51,52,56,58,64";
	$sql = "select idx, email, name, part, partno, highlevel from work_member where state='0' and companyno='1' and idx in (".$work_mem_idx.")";
	$mb = selectAllQuery($sql);

/*
	for($i=0; $i<count($mb['idx']); $i++){

		//부서명
		$work_mem_part = @array_combine($mb['email'] , $mb['part']);

		//부서번호
		$work_mem_partno = @array_combine($mb['email'] , $mb['partno']);

		//회원레벨
		$work_mem_highlevel = @array_combine($mb['email'] , $mb['highlevel']);
	}
*/

/*
	for($i=0; $i<count($list['idx']); $i++){

		$workdate = $list['workdate'][$i];
		$idx = $list['idx'][$i];
		$work_email = $list['email'][$i];
		$work_name = $list['name'][$i];
		$work_contents = $list['contents'][$i];
		$work_flag = $list['work_flag'][$i];
		$work_title = $list['title'][$i];

		$sql = "select idx from work_todaywork where state='0' and repeat_flag='1' and workdate='".$workdate."' and contents='".$work_contents."' and email in('sadary0@nate.com') order by workdate asc";
		$work_info = selectQuery($sql);
		if($work_info['idx']){
			$sql = "update work_todaywork set work_idx='".$idx."' where state='0' and idx='".$work_info['idx']."' and work_idx is null";
			$up = updateQuery($sql);
			echo  $sql;
			echo "\n";
		}
	}
*/


	for($i=0; $i<count($list['idx']); $i++){

		$workdate = $list['workdate'][$i];
		$idx = $list['idx'][$i];
		$work_email = $list['email'][$i];
		$work_name = $list['name'][$i];
		$work_contents = $list['contents'][$i];
		$work_flag = $list['work_flag'][$i];
		$work_title = $list['title'][$i];

		$sql = "select idx from work_todaywork_user where state='0' and companyno='1' and workdate='".$workdate."' and work_email ='sun@bizforms.co.kr' order by workdate asc";
		$work_info = selectAllQuery($sql);
		
		for($k=0; $k <count($work_info['idx']); $k++){

			$sql = "update work_todaywork_user set work_idx='".$idx."' where state='0' and idx='".$work_info['idx'][$k]."' and workdate='".$workdate."'";
			$up = updateQuery($sql);
			echo  $sql;
			echo "\n";
		}
	}

	/*
	$companyno = '1';
	for($i=0; $i<count($list['idx']); $i++){

		$workdate = $list['workdate'][$i];
		$idx = $list['idx'][$i];
		$work_email = $list['email'][$i];
		$work_name = $list['name'][$i];
		$work_contents = $list['contents'][$i];
		$work_flag = $list['work_flag'][$i];
		$work_title = $list['title'][$i];



		for($j=0; $j<count($mb['idx']); $j++){

			$email = $mb['email'][$j];
			$name = $mb['name'][$j];
			$part = $mb['part'][$j];
			$partno = $mb['partno'][$j];
			$highlevel = $mb['highlevel'][$j];


			//$sql = "select idx from work_todaywork where state='0' and workdate='".$workdate."' and contents='".$work_contents."' and email in('fpqldhtk3@nate.com','hj9495@hanmail.net','zhowlsk2@nate.com','eyson@bizforms.co.kr','sadary0@nate.com','yoonjh8932@naver.com','chdk1001@nate.com')";

			//'fpqldhtk3@nate.com'
			//'hj9495@hanmail.net'
			//'zhowlsk2@nate.com'
			//'eyson@bizforms.co.kr'
			//'sadary0@nate.com'
			//'yoonjh8932@naver.com'
			//'chdk1001@nate.com'

			$sql = "select idx from work_todaywork where state='0' and workdate='".$workdate."' and contents='".$work_contents."' and email='".$email."' ";
			$work_info = selectQuery($sql);
			if(!$work_info['idx']){

				$sql = "insert into work_todaywork(companyno, email, name, highlevel, type_flag, work_flag, part_flag, part, repeat_work_idx, repeat_flag, work_idx, title, contents, workdate, ip)";
				$sql = $sql .=" values('".$companyno."','".$email."','".$name."','".$highlevel."','0','".$work_flag."','".$partno."','".$part."','33596','1', '".$idx."','".$work_title."', N'".$work_contents."','".$workdate."','".LIP."')";
				$insert_idx = insertIdxQuery($sql);

				echo $sql;
				echo "\n\n";

				if($insert_idx){

					$sql = "select idx from work_todaywork_user where state='0' and companyno='".$companyno."' and work_idx='".$insert_idx."' and email='".$email."'";
					$work_row = selectQuery($sql);
					if(!$work_row['idx']){
						$sql = "insert into work_todaywork_user(companyno, work_idx, work_email, work_name, email, name, workdate, ip)";
						$sql = $sql .=" values('".$companyno."','".$insert_idx."','".$work_email."','".$work_name."','".$email."','".$name."','".$workdate."','".LIP."')";
				
						echo $sql;
						echo "\n\n";

						$res = insertQuery($sql);
					}
				}
			}
		}
	}*/



	exit;

	$sql = "select * from work_member231";
	echo $sql;
	selectQuery($sql);
	exit;
	$t1 =   strtotime(date("Y-m-d H:i:s", time()));
	
	$t2 = time();

	echo name_random(10).$t1;
	echo "<br>";
	echo name_random(10).$t2;
	echo "<br>";

	
	//strtotime();

	echo "<br>";
	echo date("Y-m-d H:i:s", $t1);


	exit;
	$type4 = '0';
	$type5 = '0';
	$type6 = '1';

	//$result = work_cp_reward_month($type4, $type5, $type6);
	$result = member_todaywork_over();

	print "<pre>";
	print_r($result);
	print "</pre>";

	exit;
	$res_idx = 671;
	//$work_mem_idx = trim($chall_user_chk);

	$work_mem_idx = "46,47,48,50,51";
	$sql = "select idx, email, name, highlevel, part, partno from work_member where state='0' and idx in (".$work_mem_idx.")";
	$chall_mem_info = selectAllQuery($sql);

	for($i=0; $i<count($chall_mem_info['idx']); $i++){
		$sql = "select idx from work_challenges_user where state='0' and challenges_idx='".$res_idx."' and email='".$chall_mem_info['email'][$i]."'";
		$chall_row = selectQuery($sql);

		if(!$chall_row['idx']){
			$sql = "insert into work_challenges_user(challenges_idx,email,name,ip) values('".$res_idx."','".$chall_mem_info['email'][$i]."','".$chall_mem_info['name'][$i]."','".LIP."')";
	//		$res = insertQuery($sql);
		}

	}



	exit;

	$sql = "select idx, email, name, highlevel, part, partno from work_member where state='0' and companyno='".$companyno."'";
	$chall_mem_info = selectAllQuery($sql);
	$res_idx = 668;
	$type_flag = 0;

	$sql = "select * from work_challenges where state=0 and idx=668";
	$chall_info = selectQuery($sql);
	if($chall_info['idx']){

		$chall_title = urldecode($chall_info['title']);
		$chall_coin = $chall_info['coin'] * $chall_info['attend'];
	}


	for($i=0; $i<count($chall_mem_info['idx']); $i++){

		$sql = "select idx from work_todaywork where state='0' and work_flag='2' and decide_flag='9' and email='".$chall_mem_info['email'][$i]."' and work_idx='".$res_idx."' and workdate='".TODATE."'";
		$todaywork_info = selectQuery($sql);
		if(!$todaywork_info['idx']){
			$work_contents = $chall_title . " (최대 ".number_format($chall_coin). " 코인 획득 가능)";
			$sql = "insert into work_todaywork(highlevel, work_flag, part_flag, part, type_flag, decide_flag, work_idx, email, name, contents, ip, workdate) ";
			echo $sql = $sql .= " values('".$chall_mem_info['highlevel'][$i]."', '2', '".$chall_mem_info['partno'][$i]."' ,'".$chall_mem_info['part'][$i]."', '".$type_flag."', '9', '".$res_idx."', '".$chall_mem_info['email'][$i]."', '".$chall_mem_info['name'][$i]."', N'".$work_contents."','".LIP."','2022-01-07')";
			echo "<br>";
			$work_insert = insertQuery($sql);
		}
	}


	exit;



	$sql = "select * from work_todaywork where part is null order by idx asc";
	$info = selectAllQuery($sql);
	
	for($i=0;$i<count($info['idx']); $i++){

		$part_flag = $info['part_flag'][$i];
		if($part_flag){
			$sql = "select idx, part,partno from work_member where state=0 and email='".$info['email'][$i]."' ";
			$mem_info = selectQuery($sql);
			if ($mem_info['idx']){
				
				$partno = $mem_info['partno'];
				$part = $mem_info['part'];
			echo	$sql = "update work_todaywork set part_flag='".$partno."', part='".$part."' where idx='".$info['idx'][$i]."'";
			echo "<br>";
				//$up = updateQuery($sql);
			}
		}
	}

	exit;
	$sql = "select * from work_challenges_comment where state=1 and challenges_idx=648 and idx=393";
	$info = selectAllQuery($sql);

	
	for($i=0; $i<count($info['idx']); $i++){


		$contents = urlencode("투움바떡치세트 추천해요! 로제소스도 맛있고, 치킨도 일품입니다~! 닭껍질튀김도 추천드려요~~>_<");

		//echo $contents;


	echo	$sql = "update work_challenges_comment set contents='".$contents."' where idx='".$info['idx'][$i]."'";
		//$up = updateQuery($sql);
	}







	exit;
	$sql = "select * from work_coininfo where state='0' and convert(char(10), regdate, 120) >= '2021-12-10' and idx>=660 and idx<=672 ";
	$coininfo = selectAllQuery($sql);

	for($i=0; $i<count($coininfo['idx']); $i++){

		
		
		
		$idx = $coininfo['idx'][$i];
		$work_idx = $coininfo['work_idx'][$i];
		$auth_comment_idx = $coininfo['auth_comment_idx'][$i];
		$auth_file_idx = $coininfo['auth_file_idx'][$i];
		$email = $coininfo['email'][$i];
		$name = $coininfo['name'][$i];
		$reward_user = $coininfo['reward_user'][$i];
		$reward_name = $coininfo['reward_name'][$i];
		$coin = $coininfo['coin'][$i];
		$ip = $coininfo['ip'][$i];
		$regdate = $coininfo['regdate'][$i];

		/*if ($auth_comment_idx == null){
			$auth_comment_idx = null;
		}


		if ($auth_file_idx == null){
			$auth_file_idx = null;
		}*/


	echo 	$sql = "select * from work_coininfo where idx='".$idx."'";
	//echo 	$sql = "select idx from work_coininfo where work_idx='".$work_idx."' and code='500' and email='".$email."' and coin='".$coin."' and idx='".$idx."'";
	echo "<Br>";

		$list_info = selectQuery($sql);

	//echo " :: " . $list_info['idx'];


		if($list_info['idx']){
			$sql = "insert into work_coininfo(state, code, work_idx, reward_type, auth_comment_idx, auth_file_idx, email, name, reward_user, reward_name, coin, memo, ip)";
	echo	$sql = $sql .=" values('0', '520', '".$work_idx."', 'challenge', '".$auth_comment_idx."', '".$auth_file_idx."', '".$reward_user."', '".$reward_name."', '".$email."', '".$name."', '".$coin."', '챌린지 참여 보상으로 차감', '".$ip."')";
			insertQuery($sql);


			echo "<Br>";
			echo	$sql = "update work_member set coin=coin-'".$coin."' where idx='46'";
			updateQuery($sql);
			echo "<Br>";
		}

		echo $i;
	}










	exit;
	//$sql = "select * from work_challenges where state=0 and idx in(6,7,8,9,11,16,17,19,20,21)";
	$sql = "select * from work_challenges where state=0  order by idx desc";
	//$sql = "select * from work_challenges where state=0 and idx in(546)";
	//$sql = "select * from work_challenges where state=0 and edate >='2021-11-18' ";
	$res = selectAllQuery($sql);

	for($i=0; $i<count($res['idx']); $i++){

		$idx = $res['idx'][$i];
		//$kind = $res['kind'][$i];
		//$contents = $res['contents'][$i];
		//$title = urlencode($res['title'][$i]);
		$title = urldecode($res['title'][$i]);
		


		//$sql = "update work_challenges set title='".$title."' where idx='".$res['idx'][$i]."'";
		//$sql = "update work_challenges set template='1' where idx='".$res['idx'][$i]."'";
		//$sql = "update work_contents set contents='".$contents."' where work_idx='".$res['idx'][$i]."'";
		//updateQuery($sql);	
		
		//$sql = "insert into work_contents( kind, work_idx, contents) values('".$kind."','".$idx."','".$contents."')";
		//insertQuery($sql);

	//	echo $idx . "\t" . $title ."\n";

	}
	

?>