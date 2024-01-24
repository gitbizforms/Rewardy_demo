<?php

	$home_dir = str_replace( basename(__DIR__) , "", __DIR__ );
	include $home_dir . "inc_lude/conf_mysqli.php";
	include $home_dir . "inc/SHA256/KISA_SHA256.php";

	include DBCON_MYSQLI;
	include FUNC_MYSQLI;

	//$sql = "select * from work_member where state=0 and companyno=1 and email in ('sun@bizforms.co.kr','qohse@nate.com','eyson@bizforms.co.kr','yoonjh8932@naver.com','fpqldhtk3@nate.com','audtjs2282@nate.com','havimk@nate.com','ruda-ju@nate.com','chdk1001@nate.com','hj9495@hanmail.net','hyhyhy0313@nate.com','zhowlsk2@nate.com','jangsannim@nate.com','nansli7@nate.com','rlatjdgml9@nate.com','adsb12@nate.com')";
	$sql = "select * from work_member where state=9";
	$mb_info = selectAllQuery($sql);

	for($i=0; $i<count($mb_info['idx']); $i++){


		$mb_idx = $mb_info['idx'][$i];
		$email = $mb_info['email'][$i];

		//$pass = $email . "biz";
		$pass = "vheh139";
		
		$pass = kisa_encrypt($pass);
		
		$sql = "update work_member set password=null where idx='".$mb_idx."'";
		echo $sql;
		echo "\n\n";
		updateQuery($sql);
	}


?>