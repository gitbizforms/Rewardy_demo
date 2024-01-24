<?
	//실서버
	$serverName = "10.17.239.118";
	$dbcon_Database = "todaywork";
	$dbcon_UID = "todaywork";
	$dbcon_pass = "work2021%%^^";

	/*
	$serverName = "10.17.239.118";
	$dbcon_Database = "biz_conference";
	$dbcon_UID = "bizcom";
	$dbcon_pass = "conference2020!#@@";
	*/

	//로컬서버
	/*
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

	//CharacterSet : 한글꺠짐방지 UTF-8 설정
	//$connectionInfo = array ("Database"=>$dbcon_Database, "UID"=>$dbcon_UID, "PWD"=>$dbcon_pass, 'CharacterSet' => 'UTF-8');
	$connectionInfo = array ("Database"=>$dbcon_Database, "UID"=>$dbcon_UID, "PWD"=>$dbcon_pass, 'CharacterSet' => 'UTF-8');
	$conn = sqlsrv_connect($serverName, $connectionInfo);


	$connectionInfo2 = array ("Database"=>$dbcon_Database, "UID"=>$dbcon_UID, "PWD"=>$dbcon_pass, 'CharacterSet' => SQLSRV_ENC_CHAR);
	$conn2 = sqlsrv_connect($serverName, $connectionInfo2);

	if($conn == false){
		echo "연결 실패!\n";
		$err = sqlsrv_errors();
		print "<pre>";
		print_r($err);
		print "</pre>";
		exit;
	}

	if(sqlsrv_begin_transaction( $conn) === false){
		echo "Could not begin transaction.\n";
		die( print_r( sqlsrv_errors(), true ));
	}
?>
