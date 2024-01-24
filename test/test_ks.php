<?php
    $home_dir = str_replace( basename(__DIR__) , "" , __DIR__ );
	include $home_dir . "/inc_lude/header.php";

    $sql = "select * from push_device_info where push_yn = 'Y' and division = '0' order by idx asc";
    $rst = selectAllQuery($sql);
    $push_arr = [];
    $test_arr = ['1','k','가'];
    for($i=0;$i<count($rst['idx']);$i++){
        array_push($push_arr, $rst['device_platform'][$i]);
        echo $push_arr[$i]."<br>";
    }
   
?>
