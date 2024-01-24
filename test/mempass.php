<?

$home_dir = str_replace( basename(__DIR__) , "", __DIR__ );
include $home_dir . "inc_lude/conf_mysqli.php";
include $home_dir . "inc/SHA256/KISA_SHA256.php";

include DBCON_MYSQLI;
include FUNC_MYSQLI;

$mb_pass ='0000';

$mb_pass =  kisa_encrypt($mb_pass);


$sql = "update work_member set password='".$mb_pass."' where email='homme1980@naver.com'";
echo $sql;
updateQuery($sql);


?>