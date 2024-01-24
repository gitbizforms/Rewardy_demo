<?
	//서버주소
	$serverName = "10.17.239.90";

	//데이터베이스
	$dbcon_Database = "todaywork_demo";

	//계정
	$dbcon_UID = "todaywork_demo";

	//패스워드
	$dbcon_pass = "work2021%%^^";

	//포트
	$dbcon_port = "3306";

	//DB연결
	$conn = mysqli_connect($serverName, $dbcon_UID, $dbcon_pass, $dbcon_Database, $dbcon_port) or die("db connect fail");

?>