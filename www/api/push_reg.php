<?php
//header페이지
$home_dir = str_replace( basename(__DIR__) , "" , __DIR__ );
include $home_dir . "../../inc_lude/header.php";
///home/todaywork/rewardyNAS/user/

$dbHost = "localhost";      // 호스트 주소(localhost, 120.0.0.1)
$dbName = "todaywork";      // 데이타 베이스(DataBase) 이름
$dbUser = "todaywork";          // DB 아이디
$dbPass = "work2021%%^^";        // DB 패스워드

$db = new PDO("mysql:host={$dbHost};dbname={$dbName}", $dbUser, $dbPass);


// write_log( "device_uuid : " .$device_uuid );s
// write_log( "push_register_id : " .$push_register_id );
// write_log( "device_platform : " .$device_platform );

try{
    $device_uuid = $_POST['device_uuid'];
    $push_register_id = $_POST['push_register_id'];
    $device_platform = $_POST['device_platform'];

    define('_CHK_', true);
    require_once $_SERVER["DOCUMENT_ROOT"]."/../common/bootstrap.php";

    $device_uuid = $_POST['device_uuid'];
    $push_register_id = $_POST['push _register_id'];
    $device_platform = $_POST['device_platform'];

    if (!empty($device_uuid) && !empty($push_register_id)){
        $sql = "SELECT * FROM push_device_info WHERE device_uuid = ?";
        $stmt = $db->prepare($sql);
        $stmt->execute([$device_uuid]);
        $rst = $stmt->fetch();
        if (empty($rst)){
            $sql = "INSERT INTO push_device_info (device_uuid,push_register_id,device_platform,push_yn) values (?,?,?,'Y')";
            $stmt2 = $db->prepare($sql);
            $stmt2->execute([$device_uuid,$push_register_id,$device_platform]);
        }else{
            $sql = "UPDATE push_device_info SET push_register_id = ?,device_platform = ? WHERE device_uuid = ?";
            $stmt2 = $db->prepare($sql);
            $stmt2->execute([$push_register_id,$device_platform,$device_uuid]);
        }
    }
}catch(PDOException)
{
	$sql = "INSERT INTO push_device_info (device_uuid,push_register_id,device_platform,push_yn) values (?,?,?,'Y')";
            $stmt2 = $db->prepare($sql);
            $stmt2->execute([1,1,1]);
}

?>
