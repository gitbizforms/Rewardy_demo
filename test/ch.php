<?php
exit;

	//header페이지
	$home_dir = str_replace( basename(__DIR__) , "" , __DIR__ );
	//include $home_dir . "/inc_lude/header.php";
	include $home_dir . "inc_lude/conf.php";
	include DBCON;
	include FUNC;
	
	//echo $sql = "select * , CONVERT(datetime, regdate, 121) as reg,  CONVERT(char(23), regdate, 21) as reg1  from work_challenges_comment where state='1' order by idx asc";
	echo $sql = "select * , CONVERT(datetime, regdate, 121) as reg,  CONVERT(char(23), regdate, 21) as reg1  from work_challenges_file where state='1' order by idx asc";
	//echo $sql = "select * from work_challenges_comment where state='1' order by idx asc";
	echo "<br>";
	$list = selectAllQuery($sql);
	for($i=0; $i<count($list['idx']); $i++){

		
		$sql = "select idx, attend_type from work_challenges where state='0' and idx='".$list['challenges_idx'][$i]."'";
		$chall_info = selectQuery($sql);
		if($chall_info['idx']){
			$attend_type = $chall_info['attend_type'];
		}else{
			$attend_type = NULL;
		}

		$sql = "select idx from work_challenges_result where state='1' and challenges_idx='".$list['challenges_idx'][$i]."' and email='".$list['email'][$i]."'";
		$res = selectQuery($sql);
		if(!$res['idx']){

			//$sql = "insert into work_challenges_result(challenges_idx,state,email,name,part,partno,comment,ip,comment_regdate) values(";
			//$sql = $sql .= "'".$list['challenges_idx'][$i]."','".$list['state'][$i]."','".$list['email'][$i]."','".$list['name'][$i]."','".$list['part'][$i]."','".$list['partno'][$i]."','".$list['contents'][$i]."','".$list['ip'][$i]."', '".$list['reg1'][$i]."' )";

			$sql = "insert into work_challenges_result(challenges_idx,state,email,name,part,partno,attend_type,resize,file_path,file_name,file_size,file_ori_path,file_ori_name,file_ori_size,file_real_img_name,file_real_name,file_type,file_regdate) values(";
			$sql = $sql .= "'".$list['challenges_idx'][$i]."','".$list['state'][$i]."','".$list['email'][$i]."','".$list['name'][$i]."','".$list['part'][$i]."','".$list['partno'][$i]."','".$list['attend_type'][$i]."','".$list['resize'][$i]."','".$list['file_path'][$i]."','".$list['file_name'][$i]."',";
			$sql = $sql .= "'".$list['file_size'][$i]."','".$list['file_ori_path'][$i]."','".$list['file_ori_name'][$i]."','".$list['file_ori_size'][$i]."','".$list['file_real_img_name'][$i]."','".$list['file_real_name'][$i]."','".$list['file_type'][$i]."','".$list['reg1'][$i]."')";
			echo "<pre>";
			echo $sql;
			echo "</pre>";
			insertQuery($sql);
		}else{

			$sql = "update work_challenges_result set attend_type='".$attend_type."', resize='".$list['resize'][$i]."',file_path='".$list['file_path'][$i]."',file_name='".$list['file_name'][$i]."',file_size='".$list['file_size'][$i]."',file_ori_path='".$list['file_ori_path'][$i]."',file_ori_name='".$list['file_ori_name'][$i]."',file_ori_size='".$list['file_ori_size'][$i]."',file_real_img_name='".$list['file_real_img_name'][$i]."',file_real_name='".$list['file_real_name'][$i]."',file_type='".$list['file_type'][$i]."',file_regdate='".$list['reg1'][$i]."' where challenges_idx='".$list['challenges_idx'][$i]."' and email='".$list['email'][$i]."'";
			updateQuery($sql);
			echo $sql;
			echo "<br>";
		}
	}



?>	