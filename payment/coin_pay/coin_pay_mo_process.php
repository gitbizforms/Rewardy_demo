<?php
$home_dir = str_replace( basename(__DIR__) , "" , __DIR__ );
$home_pay = str_replace( basename($home_dir) , "" , $home_dir );
include $home_pay . "inc_lude/conf_mysqli.php";
include DBCON_MYSQLI;
include FUNC_MYSQLI;

// var_dump($home_pay . "/inc_lude/header.php");
$type_flag = ($chkMobile)?1:0;	
$price = $_POST['price'] * 0.05;
?>
<head>
        <meta charset="UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport"
            content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no">
        <title>KG이니시스 결제샘플</title>
        <link rel="stylesheet" href="css/style.css">
		<link rel="stylesheet" href="css/bootstrap.min.css">
		
		<script> 
	        function on_pay() { 
	        	myform = document.mobileweb; 
	        	myform.action = "https://mobile.inicis.com/smart/payment/";
	        	myform.target = "_self";
	        	myform.submit(); 
	        }
        </script> 
    </head>
    <?php
       echo "|";
    ?>
        <form name="mobileweb" id="" method="post" class="mt-5" accept-charset="euc-kr">
            <div class="row g-3 justify-content-between" style="--bs-gutter-x:0rem;">
        
                <input type="hidden" name="P_INI_PAYMENT" value="CARD">
                <input type="hidden" name="P_MID" value="INIpayTest">
                <input type="hidden" name="P_OID" value="mobile_test1234">
                <input type="hidden" name="P_AMT" value="<?php echo $price;?>">
                <input type="hidden" name="P_GOODS" value="코인 충전">
                <input type="hidden" name="P_UNAME" value="<?php echo $user_name;?>">
                <input type="hidden" name="P_MOBILE" value="01012345678">
                <input type="hidden" name="P_EMAIL" value="<?php echo $user_id;?>">
                <!-- <input type="hidden" name="P_NEXT_URL" value="http://localhost/payment/pay_mo/pay_pop_return_mo.php"> -->
                <input type="hidden" name="P_NEXT_URL" value="http://localhost:8090/payment/coin_pay/coin_pay_mo_return.php">
                <input type="hidden" name="P_CHARSET" value="utf8">
                <input type="hidden" name="P_NOTI" value="<?php echo $companyno?>">
                <input type="hidden" name="P_RESERVED" value="below1000=Y&vbank_receipt=Y&centerCd=Y">
                
            </div>
        </form>