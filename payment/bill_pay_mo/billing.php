<?php

header('Content-Type:text/html; charset=utf-8');


    //step1. 요청을 위한 파라미터 설정
    $key = "rKnPljRn5m6J9Mzz";
	$iv = "W2KLNKra6Wxc1P==";
    $mid = "INIBillTst";
	$type = "billing";
	$paymethod = "Card";
	$timestamp = date("YmdHis");
	$clientIp = "192.0.0.0";

	$postdata = array();
	$postdata["mid"] = $mid;
	$postdata["type"] = $type;
	$postdata["paymethod"] = $paymethod;
    $postdata["timestamp"] = $timestamp;
	$postdata["clientIp"] = $clientIp;

	//// Data 상세
    $detail = array();
	$detail["url"] = "www.inicis.com";
	$detail["moid"] = $mid."_".$timestamp;
	$detail["goodName"] = "테스트상품";
	$detail["buyerName"] = "홍길동";
	$detail["buyerEmail"] = "test@test.com";
	$detail["buyerTel"] = "01012345678";
	$detail["price"] = "1000";
	$detail["billKey"] = "734f880246ece4ffdba841c1436399c9968d32cc";
	$detail["authentification"] = "00";
	$detail["cardQuota"] = "00";
	$detail["quotaInterest"] = "0";
	
	$postdata["data"] = $detail;

	$details = str_replace('\\/', '/', json_encode($detail, JSON_UNESCAPED_UNICODE));

	//// Hash Encryption
	$plainTxt = $key.$mid.$type.$timestamp.$details;
    $hashData = hash("sha512", $plainTxt);

	$postdata["hashData"] = $hashData;

	echo "plainTxt : ".$plainTxt."<br/><br/>";
	echo "hashData : ".$hashData."<br/><br/>"; 


	$post_data = json_encode($postdata, JSON_UNESCAPED_UNICODE);
	
	echo "**** 요청전문 **** <br/>" ; 
	echo str_replace(',', ',<br>', $post_data)."<br/><br/>" ; 
	
	
	//step2. 요청전문 POST 전송
	
    $url = "https://iniapi.inicis.com/v2/pg/billing";
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json;charset=utf-8'));
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
     
    $response = curl_exec($ch);
    curl_close($ch);
	
	
    //step3. 결과출력
	
    echo "**** 응답전문 **** <br/>" ;
	echo str_replace(',', ',<br>', $response)."<br><br>";
    
?>