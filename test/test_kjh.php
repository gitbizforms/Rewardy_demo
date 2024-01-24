<?php
$home_dir = str_replace( basename(__DIR__) , "" , __DIR__ );

	include $home_dir . "inc_lude/conf_mysqli.php";
	include $home_dir . "inc/SHA256/KISA_SHA256.php";
	include DBCON_MYSQLI;
	include FUNC_MYSQLI;

$sql = "select * from work_todaywork_comment where email is null and state = 0 and (like_idx is null or like_idx = 0) and regdate >= '2023-01-01' and cmt_flag <> 2 and comment not like '%요청하신%' and workdate is not null and work_idx <> 0";
$com_list = selectAllQuery($sql);

for($i=0; $i<count($com_list['idx']); $i++){
	$idx = $com_list[idx][$i];
	$reg = $com_list[regdate][$i];
	
	//$sql_u = "update work_todaywork_like set com_idx = '".$idx."' where regdate = '".$reg."'";

	
}
	$sql_u = "select * from work_todaywork_like where regdate in ('".$reg."')";
echo "<br>";
	echo $sql_u;
	echo "</br>";
?>
