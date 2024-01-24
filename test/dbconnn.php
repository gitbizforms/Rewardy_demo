<?php

//	require_once ('KISA_SHA256.php');



	$home_dir = str_replace( basename(__DIR__) , "" , __DIR__ );
	include $home_dir . "/inc_lude/header.php";
	include $home_dir . "/inc/SHA256/KISA_SHA256.php";


	//실서버
	//$serverName = "10.17.239.97";
	//$serverName = "14.63.161.43";
	$serverName = "localhost";
	//$serverName = "127.0.0.1";
	$dbcon_Database = "todaywork";
	$dbcon_UID = "todaywork";
	$dbcon_pass = "work2021%%^^";
	$dbcon_port = "13306";



/*	$serverName = "10.17.239.118";
	$dbcon_Database = "biz_conference";
	$dbcon_UID = "bizcom";
	$dbcon_pass = "conference2020!#@@";
	*/


	/*
	//로컬서버
	$serverName = "192.168.0.249,1433";
	$dbcon_Database = "todaywork";
	$dbcon_UID = "todaywork";
	$dbcon_pass = "work2021%%^^";
	*/

	/*비즈폼디비연결
	$serverName = "10.17.239.118";
	$dbcon_Database = "sun";
	$dbcon_UID = "biz_forms";
	$dbcon_pass = "!@biz2017";
	*/

	/*
	print "<pre>";
	print_r($_SERVER);
	print "</pre>";
	*/

	
	if($_SERVER['HTTP_HOST'] == "officeworker.co.kr"){

		//$dbconn     = @mysql_connect($serverName, $dbcon_UID, $dbcon_pass )  or die(header("Location: http://officeworker.co.kr/"));
		$dbconn     = mysqli_connect($serverName, $dbcon_UID, $dbcon_pass, $dbcon_Database, $dbcon_port) or die("db connect fail");
		//@mysql_select_db($dbcon_Database, $dbconn);
		//@mysql_query("SET NAMES UTF-8");

	}else{
		//CharacterSet : 한글꺠짐방지 UTF-8 설정
		//$connectionInfo = array ("Database"=>$dbcon_Database, "UID"=>$dbcon_UID, "PWD"=>$dbcon_pass, 'CharacterSet' => 'UTF-8');
		$connectionInfo = array ("Database"=>$dbcon_Database, "UID"=>$dbcon_UID, "PWD"=>$dbcon_pass, 'CharacterSet' => 'UTF-8');
		$conn = sqlsrv_connect($serverName, $connectionInfo);

		//$connectionInfo2 = array ("Database"=>$dbcon_Database, "UID"=>$dbcon_UID, "PWD"=>$dbcon_pass, 'CharacterSet' => SQLSRV_ENC_CHAR);
		//$conn2 = sqlsrv_connect($serverName, $connectionInfo2);

	}


	
	function KISA256_encrypt($str) {
		$planBytes = array_slice(unpack('c*',$str), 0); // 평문을 바이트 배열로 변환
		$ret = null;
		$bszChiperText = null;
		KISA_SEED_SHA256::SHA256_Encrypt($planBytes, count($planBytes), $bszChiperText);
		$r = count($bszChiperText);

		foreach($bszChiperText as $encryptedString) {
			$ret .= bin2hex(chr($encryptedString)); // 암호화된 16진수 스트링 추가 저장
		}
		return $ret;
	}

	
	//쿼리문, 쿼리문 출력 : 1 실행
	function dbQuery_old($query="", $debug="0"){
		global $dbconn, $debug;

		
		//$query = "select * from product where idx='31s118' and id='ssssiseehss' and view_flag='0' ";
		//전체URL확인
		if($_SERVER['SERVER_PROTOCOL']){
			$arr_protocol =  explode("/", $_SERVER['SERVER_PROTOCOL']);
			$protocol = strtolower($arr_protocol[0]);
			$protocol_url = ConvertStr($protocol."://".$_SERVER['HTTP_HOST']);
		}else{
			$protocol_url = "";
		}


		$query_sub = "/*".$_SERVER['REMOTE_ADDR']."|".$protocol_url . $_SERVER['PHP_SELF']."*/";
		

		//전체 쿼리 출력
		if($debug == 1){
			echo $query_sub . $query;
		}


		//쿼리 조건절 체크
		$condition = @end(explode('where',strtolower($query)));

		//문자열을 배열로 정의
		//parse_str( str_replace(' and ','&',$condition) , $arr_field);

		$result = mysqli_query($dbconn, $query);
		if(!$result){
		//	Queryfail($query, $debug);
		}

		return $result;
	}


	/*
	//단일 셀렉트 쿼리
	function selectQuery($query="", $debug="0"){
		global $dbconn;

		$result = dbQuery($query, $debug);
		$result = mysqli_fetch_array($result, MYSQLI_ASSOC);

		return $result;
	}
	*/

	/*
	//전체 셀렉트 쿼리
	function selectAllQuery($query="", $debug="0"){
		global $dbconn;
	
		$List = array();
		$result = dbQuery($query, $debug);
		while( $row = mysqli_fetch_array($result, MYSQLI_ASSOC))
		{
			foreach ($row as $key => $value) {
				$List[$key][] = $value;
			}
		}

		return $List;
	}
	*/



	/*
	//인서트 쿼리
	function insertQuery($query="", $debug="0"){
		global $dbconn;

		$result = dbQuery($query, $debug);

		//쿼리실행된 row수 리턴, 없을경우 0
		$row = mysqli_affected_rows($dbconn);


		print "result : " .$result;
		print "###############";
		print "\n\n";
		print "row : ";
		print_r($row);

		if($row){
			//커밋처리
			mysqli_commit($dbconn);
			return $row;
		}else{
			//롤백처리
			mysqli_rollback($dbconn);
			print "<pre> rollback ";
			//print_r( sqlsrv_errors() );
			print "</pre>";
			return 0;

		}
	}
	*/

	/*
	function updateQuery($query="", $debug="0"){
		global $dbconn;

		$result = dbQuery($query, $debug);
		//쿼리실행된 row수 리턴, 없을경우 0
		$rows_affected = mysqli_affected_rows($dbconn);

		//오류났을때
		if( $rows_affected === false){
			mysqli_rollback($dbconn);
			//die( print_r( sqlsrv_errors(), true));
		//쿼리 영향받은 행수가 확인 할수 없을경우 : -1
		}elseif( $rows_affected == -1) {
			mysqli_rollback($dbconn);
			//echo "No information available.<br />";
			return $rows_affected;
		}else{
			mysqli_commit($dbconn);

			//정상적으로 성공 : 1 반환
			return $rows_affected;
		}
	}*/
	


	//$sql = "select * from work_member limit 10";
	$sql = "INSERT INTO work_member set state='0', password='".KISA256_encrypt('1234')."', email='sadary0@naver.com'";
	//$sql = "update work_member set state='0' where email='sadary0@naver.com'";
	//$sql = $sql .= "update work_member set state='0' where email='sadary010@naver.com'";
	//$row = selectAllQuery($sql);
	$row = insertIdxQuery($sql);


	print "<pre>";
	print_r($row);
	print "</pre>";

	//phpinfo();
	//PHP Version 7.2.24-0ubuntu0.18.04.15

	echo "nnn";
	/*
	if($conn == false){
		echo "연결 실패!\n";
		$err = sqlsrv_errors();
		print "<pre>";
		print_r($err);
		print "</pre>";
		exit;
	}
	*/

	/*
	if(sqlsrv_begin_transaction( $conn) === false){
		echo "Could not begin transaction.\n";
		die( print_r( sqlsrv_errors(), true ));
	}
	*/
?>