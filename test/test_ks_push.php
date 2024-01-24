<?php
$home_dir = str_replace( basename(__DIR__) , "" , __DIR__ );
include $home_dir . "/inc_lude/header.php";

$sql = "select * from push_device_info where push_yn = 'Y' and division = '0' order by idx asc";
$rst = selectAllQuery($sql);
$push_arr = []; 

for($i=0;$i<count($rst['idx']);$i++){
	array_push($push_arr, $rst['push_register_id'][$i]);
	echo $rst['push_register_id'][$i]."<br>";
}

echo $push_arr[0]."<br>";
echo $push_arr[2]."<br>";
echo $push_arr[1]."<br>";

?>