<?
exit;
$home_dir = str_replace( basename(__DIR__) , "" , __DIR__ );
include $home_dir . "inc_lude/conf_mysqli.php";
include DBCON_MYSQLI;
include FUNC_MYSQLI;

$sql = "select * from work_todaywork where idx=59247 order by idx desc";
$rs = selectQuery($sql);

$contents_no = $rs['contents'];
$contents = strip_tags($rs['contents']);

echo str_replace("\n", "<br>", $contents_no);

//echo "<br>";

//echo $contents;



?>
