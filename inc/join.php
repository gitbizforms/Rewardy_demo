<?php

	include str_replace( basename(__DIR__) , "", __DIR__ ) ."inc_lude/conf.php";
	include DBCON;
	include FUNC;


	$return = coin_add("works_week");

	

	print " ::  ". $return;
?>