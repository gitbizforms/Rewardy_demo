<?

	include str_replace( basename(__DIR__) , "", __DIR__ ) ."inc_lude/conf.php";
	include DBCON;
	include FUNC;

	//comment 테이블 좋아요 대상 데이타 카운트	
	$count_sql = "select count(0) as comment_cnt from work_todaywork_comment"; 
	$count_sql = $count_sql. " where link_idx = work_idx and state != 9 and email is NULL";
	$count_res = selectQuery($count_sql);

	if($count_res){
		$comment_cnt = $count_res['comment_cnt'];
	}

	echo "총 건수 : ".$comment_cnt;
	echo "<br>";

	$sql = "select idx, work_idx, convert(char(19),regdate,20) as regdate from work_todaywork_comment"; 
	$sql = $sql. " where link_idx = work_idx and state != 9 and email is NULL order by regdate desc";
	$res = selectAllQuery($sql);

	echo "comment테이블 조회:".$sql;
	echo "<br>";

	if($res){
		if($comment_cnt > 0){
			for($i=0; $i<$comment_cnt; $i++){
				$idx = $res['idx'][$i];
				$work_idx = $res['work_idx'][$i];
				$comment_reg_date = $res['regdate'][$i];

				$comment_convert_add_reg_date = strtotime($comment_reg_date,'+1 seconds');
				$comment_convert_add_reg_date = date("Y-m-d H:i:s", $comment_convert_reg_date);

				$comment_convert_dis_reg_date = strtotime($comment_reg_date,'-1 seconds');
				$comment_convert_dis_reg_date = date("Y-m-d H:i:s", $comment_convert_reg_date);				

				$sql2 = "select send_email from work_todaywork_like where com_idx is NULL and work_idx ='".$work_idx."'";
				$sql2 = $sql2. " and convert(char(19),regdate,20) = '".$comment_reg_date."' order by regdate desc";
				$res2 = selectQuery($sql2);

				$sql3 = "select send_email from work_todaywork_like where com_idx is NULL and work_idx ='".$work_idx."'";
				$sql3 = $sql3. " and convert(char(19),regdate,20) = '".$comment_convert_add_reg_date."' order by regdate desc";
				$res3 = selectQuery($sql3);

				$sql4 = "select send_email from work_todaywork_like where com_idx is NULL and work_idx ='".$work_idx."'";
				$sql4 = $sql4. " and convert(char(19),regdate,20) = '".$comment_convert_dis_reg_date."' order by regdate desc";
				$res4 = selectQuery($sql4);

				if($res3){
					$comment_reg_date = $comment_convert_add_reg_date;
					$send_email = $res3['send_email'];
				}elseif($res4){
					$comment_reg_date = $comment_convert_dis_reg_date;
					$send_email = $res4['send_email'];
				}

				if($res2 || $res3 || $res4){

					$send_email = $res2['send_email']; 

					$update_sql = " update work_todaywork_like set com_idx = '".$idx."'";
					$update_sql = $update_sql . " where com_idx is NULL and work_idx = '".$work_idx."'"; 
					$update_sql = $update_sql . " and convert(char(19),regdate,20) = '".$comment_reg_date."'"; 
					//$update_res = updateQuery($update_sql); 

					echo $i."번째 like테이블 업데이트 쿼리:".$update_sql;  
					echo "<br>"; 

					$update_sql2 = "update work_todaywork_comment set like_email = '".$send_email."'";
					$update_sql2 = $update_sql2 . " where idx = '".$idx."'";
					//$update_res2 = updateQuery($update_sql2);

					echo $i."번째 commnet테이블 업데이트 쿼리:".$update_sql;  
					echo "<br>"; 
				}
			}
		}
	}
?>