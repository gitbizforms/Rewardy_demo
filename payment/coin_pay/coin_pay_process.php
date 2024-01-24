<?php
$home_dir = str_replace( basename(__DIR__) , "" , __DIR__ );
$home_pay = str_replace( basename($home_dir) , "" , $home_dir );
// include $home_pay . "/inc_lude/header.php";
// var_dump($home_pay . "/inc_lude/header.php");
include $home_pay . "inc_lude/conf_mysqli.php";
include DBCON_MYSQLI;
include FUNC_MYSQLI;
$type_flag = ($chkMobile)?1:0;	

require_once('pay_libs/INIStdPayUtil.php');
$SignatureUtil = new INIStdPayUtil();

// $mid 			= "smartbiz04";  								// 상점아이디			
// $signKey 		= "ZkhYaUVsVytkWG52ajJHdmNFWHNGUT09"; 			// 웹 결제 signkey
$mid 			= "INIpayTest";  								// 상점아이디			
$signKey 		= "SU5JTElURV9UUklQTEVERVNfS0VZU1RS"; 			// 웹 결제 signkey

$mKey 	= $SignatureUtil->makeHash($signKey, "sha256");

$timestamp 		= $SignatureUtil->getTimestamp();   			// util에 의해서 자동생성
$use_chkfake	= "Y";											// PC결제 보안강화 사용 ["Y" 고정]	
$orderNumber 	= $mid . "_" . $timestamp; 						// 가맹점 주문번호(가맹점에서 직접 설정)

$price = $_POST['price'] * 0.05;

$params = array(
    "oid" => $orderNumber,
    "price" => $price,
    "timestamp" => $timestamp
);

$sign = $SignatureUtil->makeSignature($params);

$params = array(
    "oid" => $orderNumber,
    "price" => $price,
    "signKey" => $signKey,
    "timestamp" => $timestamp
);

$sign2 = $SignatureUtil->makeSignature($params);
?>
    <head>
        <meta charset="UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport"
            content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no">
        <title>KG이니시스 결제샘플</title>
        <link rel="stylesheet" href="/html/css/common.css">
        <link rel="stylesheet" href="/html/css/pay_pop.css">
        <link rel="stylesheet" href="css/style.css">
		<link rel="stylesheet" href="css/bootstrap.min.css">
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"
            integrity="sha512-894YE6QWD5I59HgZOGReFYm4dnWc1Qt5NtvYSaNcOP+u1T9qYdvdihz0PPSiiqn/+/3e7Jo4EaG7TubfWGUrMQ=="
            crossorigin="anonymous" referrerpolicy="no-referrer"></script>
		<!--테스트JS--><script language="javascript" type="text/javascript" src="https://stgstdpay.inicis.com/stdjs/INIStdPay.js" charset="UTF-8"></script> 
		<!--운영 JS> <script language="javascript" type="text/javascript" src="https://stdpay.inicis.com/stdjs/INIStdPay.js" charset="UTF-8"></script> -->`
         <script type="text/javascript">
            function paybtn() {
                INIStdPay.pay('SendPayForm_id');
            }

            paybtn();

        </script>
    </head>
    <?php
       echo "|";
    ?>
     <form name="" id="SendPayForm_id" method="post" class="mt-5">
                <input type="hidden" name="version" value="1.0">
                <input type="hidden" name="gopaymethod" value="Card">
                <input type="hidden" name="mid" value="<?php echo $mid ?>">
                <input type="hidden" name="oid" value="<?php echo $orderNumber ?>">
                <input type="hidden" name="price" class = "price" value="<?php echo $price ?>">
                <input type="hidden" name="timestamp" value="<?php echo $timestamp ?>">
                <input type="hidden" name="use_chkfake" value="<?php echo $use_chkfake ?>">
                <input type="hidden" name="signature" value="<?php echo $sign ?>">
                <input type="hidden" name="verification" value="<?php echo $sign2 ?>">
                <input type="hidden" name="mKey" value="<?php echo $mKey ?>">
                <input type="hidden" name="currency" value="WON">
                <input type="hidden" name="goodname" value="코인 충전">
                <input type="hidden" name="buyername" class= "buyername" value="<?php echo $user_name?>">
                <input type="hidden" name="buyertel" value="01012345678">
                <input type="hidden" name="buyeremail" class= "buyeremail" value="<?php echo $user_id?>">
                <input type="hidden" name="merchantData" value="<?php echo $companyno?>">
                <!-- <input type="hidden" name="returnUrl" value="https://rewardy.co.kr/payment/coin_pay/coin_pay_return.php">
                <input type="hidden" name="closeUrl" value="https://rewardy.co.kr/payment/coin_pay/close.php"> -->
                <input type="hidden" name="returnUrl" value="http://localhost:8090/payment/coin_pay/coin_pay_return.php">
                <input type="hidden" name="closeUrl" value="http://localhost:8090/payment/coin_pay/close.php">
                <input type="hidden" name="acceptmethod" value="HPP(1):below1000:centerCd(Y)">
            </form> 